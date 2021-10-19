<?php 
require_once(dirname(__FILE__)."/../action.php");

class MailingAction extends BaseAction
{
	function DoAction($request, $user)
	{
		switch($this->name)
		{
			case "Send": 
				$this->ActionSend($request, $user);
				break;
			case "SetEmail": 
				$this->ActionSetEmail($request, $user);
				break;
			case "Resend": 
				$this->ActionResend($request, $user);
				break;
		}
	}
	
	private function ActionSend($request, $user)
	{
		set_time_limit(0);
		//collect reciever list
		$object = new LocalObject();
		$dispatchList = array();
		foreach ($this->actionConfig["EntityList"] as $entity => $entityConfig)
		{
			if($request->GetProperty("load_".$entity))
			{
				$itemList = new ItemList("crm", $entity, $entityConfig);
				$itemList->Load($request, $user);
				foreach ($itemList->GetItems() as $item)
				{
					$item["EntityType"] = $entity;
					$item["Direct"] = "N";
					$dispatchList[] = $item;
					if($entity == "child")
					{
						$parentList = new ItemList("crm", "parent", $this->actionConfig["EntityList"]["parent"]);
						$request->SetProperty("ChildID", $item["EntityID"]);
						$parentList->Load($request, $user);
						foreach ($parentList->GetItems() as $parent)
						{
							$parent["EntityType"] = "parent";
							$parent["Direct"] = "N";
							$dispatchList[] = $parent;
						}
					}
				}
			}
		}
		$fixedDispatchList = array();
		foreach ($dispatchList as $dispatch)
		{
			if(isset($dispatch["EmailList"]))
			{
				if(count($dispatch["EmailList"]) > 0)
				{
					foreach ($dispatch["EmailList"] as $email)
					{
						$dispatch["Email"] = $email["Email"];
						$fixedDispatchList[] = $dispatch;
					}
				}
				else
				{
					$dispatch["Email"] = "";
					$fixedDispatchList[] = $dispatch;
				}
			}
			elseif(isset($dispatch["Email"]))
			{
				$fixedDispatchList[] = $dispatch;
			}
		}
		$dispatchList = $fixedDispatchList;
		
		if($request->GetProperty("TargetEntityID") && $request->GetProperty("TargetEntityType") && $request->GetProperty("TargetEmail"))
		{
			$dispatchList[] = array("Email" => $request->GetProperty("TargetEmail"),
									"EntityType" => $request->GetProperty("TargetEntityType"),
									"EntityID" => $request->GetProperty("TargetEntityID"),
									"Direct" => "Y");
		}
		if(count($dispatchList) == 0)
			$this->AddError("mailing-reciever-list-empty", "crm");
		//exit();
			
		//check sender and required fields
		$sender = new LocalObject();
		$sender->LoadFromSQL("SELECT SenderID, Email FROM `crm_mailing_sender` WHERE SenderID=".$request->GetPropertyForSQL("SenderID"));
		if(!$sender->GetProperty("SenderID"))
			$this->AddError("mailing-sender-required", "crm");
		if(!$request->ValidateNotEmpty("Subject"))
			$this->AddError("mailing-subject-required", "crm");
		if(!$request->ValidateNotEmpty("Content"))
			$this->AddError("mailing-content-required", "crm");
		
		//save attachments and finance attachments
		$attachmentList = array();
		$templateAttachmentList = array(); 
		$filesys = new FileSys();
		$uploadedFiles = $filesys->Upload("AttachmentFileList", PROJECT_DIR."var/data/mailing/attachment/", true, null);
		if($filesys->HasErrors())
		{
			$this->AppendErrorsFromObject($filesys);
		}
		else if($uploadedFiles)
		{
			foreach ($uploadedFiles as $file)
			{
				if($file["error"])
				{
					$this->AddError($file["ErrorInfo"]);
				}
				else
				{
					$attachmentList[] = PROJECT_DIR."var/data/mailing/attachment/".$file["FileName"];
					$templateAttachmentList[] = array("Value" => PROJECT_DIR."var/data/mailing/attachment/".$file["FileName"]);	
				}
			}
		}
		if($request->GetProperty("AttachmentList"))
		{
			foreach ($request->GetProperty("AttachmentList") as $attachment)
			{
				$attachmentList[] = $attachment;
				$templateAttachmentList[] = array("Value" => $attachment);
			}
		}
		$this->contentData["AttachmentList"] = $templateAttachmentList;
			
		if($this->HasErrors())
			return false;
		
		$stmt = GetStatement();
		$stmt->Execute("SET NAMES utf8mb4"); //support emoji-symbols inserting
		$query = "INSERT INTO `crm_mailing` SET UserID=".$user->GetPropertyForSQL("UserID").", 
												SenderID=".$request->GetProperty("SenderID").", 
												Created=".Connection::GetSQLString(date("Y-m-d H:i:s")).", 
												Subject=".$request->GetPropertyForSQL("Subject").", 
												Content=".Connection::GetSQLString(PrepareContentBeforeSave($request->GetProperty("Content"))).", 
												FilterData=".Connection::GetSQLString(serialize($request->GetProperties()));
		$result = $stmt->Execute($query);
		$mailingID = $stmt->GetLastInsertID();
		$stmt->Execute("SET NAMES ".Connection::GetSQLString(GetLanguage()->GetMySQLEncoding()));
		if($result)
		{
			foreach ($attachmentList as $attachment)
			{
				$query = "INSERT INTO `crm_mailing_attachment` SET MailingID=".Connection::GetSQLString($mailingID).", 
																	FilePath=".Connection::GetSQLString(PrepareFilePathBeforeSave($attachment));
				$stmt->Execute($query);
			}
			foreach ($dispatchList as $dispatch)
			{
				$sent = "N";
				$errorInfo = "";
				if(is_null($dispatch["Email"]))
					$dispatch["Email"] = "";
				$object->SetProperty("Email", $dispatch["Email"]);
				if($object->ValidateEmail("Email"))
				{
					$page = new PopupPage("crm", true);
					$emailContent = $page->Load("mailing_email.html");
					$content = PrepareContentBeforeSend($request->GetProperty("Content"));
					$emailContent->SetVar("Content", $content);
					$unsubscribeURL = false;
					if($dispatch["Direct"] == "N")
					{
						if($dispatch["EntityType"] == "parent")
						{
							$urlEntity = "child";
							$urlEntityID = $dispatch["ChildID"];
						}
						else
						{ 
							$urlEntity = $dispatch["EntityType"];
							$urlEntityID = $dispatch["EntityID"];
						}
						$unsubscribeURL = GetUrlPrefix()."module/crm/public.php?Action=Unsubscribe&Entity=".$urlEntity."&EntityID=".$urlEntityID."&Sign=".md5(CRM_UNSUBSCRIBE_SALT.$urlEntity.$urlEntityID);
						$emailContent->SetVar("UnsubscribeURL", $unsubscribeURL);	
					}
					$result = SendMailFromAdmin($dispatch["Email"], $request->GetProperty("Subject"), $page->Grab($emailContent), $attachmentList, $sender->GetProperty("Email"), null, $unsubscribeURL);
					if($result)
					{
						$sent = "Y";
					}
					else
					{
						$errorInfo = $result;
					}
				}
				else
				{
					$errorInfo = GetTranslation("error-sending-incorrect-email");
				}
				$query = "INSERT INTO `crm_mailing_dispatch` SET MailingID=".Connection::GetSQLString($mailingID).", 
																EntityType=".Connection::GetSQLString($dispatch["EntityType"]).", 
																RecieverEntityID=".Connection::GetSQLString($dispatch["EntityID"]).", 
																Email=".Connection::GetSQLString($dispatch["Email"]).",
																Direct=".Connection::GetSQLString($dispatch["Direct"]).",
																Sent=".Connection::GetSQLString($sent).", 
																ErrorInfo=".Connection::GetSQLString($errorInfo);
				$stmt->Execute($query);
			}
		}
		else
		{
			$this->AddError("sql-error");
			return;
		}
		header("Location:".ADMIN_PATH."module.php?load=crm&entity=mailing&EntityViewID=".$mailingID."&FilterShow=history&ClearFormData=1&ClearEntityID=".$request->GetIntProperty("EntityID"));
		exit();
	}
	
	private function ActionResend($request, $user)
	{
		set_time_limit(0);
		if($request->GetProperty("DispatchIDs"))
		{
			$stmt = GetStatement();
			$query = "SELECT m.Subject, m.Content, s.Email FROM crm_mailing AS m 
							LEFT JOIN crm_mailing_sender AS s ON m.SenderID=s.SenderID 
						WHERE m.MailingID=".$request->GetPropertyForSQL("MailingID");
			$mailingInfo = $stmt->FetchRow($query);
			
			$query = "SELECT * FROM `crm_mailing_attachment` WHERE MailingID=".$request->GetPropertyForSQL("MailingID");
			$attachmentList = $stmt->FetchList($query);
			for($i = 0; $i < count($attachmentList); $i++)
			{
				$attachmentList[$i] = PrepareFilePathBeforeShow($attachmentList[$i]["FilePath"]);
			}
			
			$query = "SELECT DispatchID, Email, Direct, EntityType, RecieverEntityID AS EntityID FROM crm_mailing_dispatch 
						WHERE DispatchID IN(".implode(", ", Connection::GetSQLArray($request->GetProperty("DispatchIDs"))).") AND Sent='N'";
			$dispatchList = $stmt->FetchList($query);
			$successCount = 0;
			foreach ($dispatchList as $dispatch)
			{
				$page = new PopupPage("crm", true);
				$emailContent = $page->Load("mailing_email.html");
				$content = PrepareContentBeforeSend($mailingInfo["Content"]);
				$emailContent->SetVar("Content", $content);
				if($dispatch["EntityType"] == "parent")
				{
					$urlEntity = "child";
					$urlEntityID = $dispatch["ChildID"];
				}
				else
				{ 
					$urlEntity = $dispatch["EntityType"];
					$urlEntityID = $dispatch["EntityID"];
				}
				$unsubscribeURL = GetUrlPrefix()."module/crm/public.php?Action=Unsubscribe&Entity=".$urlEntity."&EntityID=".$urlEntityID."&Sign=".md5(CRM_UNSUBSCRIBE_SALT.$urlEntity.$urlEntityID);
				$emailContent->SetVar("UnsubscribeURL", $unsubscribeURL);	
				$result = SendMailFromAdmin($dispatch["Email"], $mailingInfo["Subject"], $page->Grab($emailContent), $attachmentList, $mailingInfo["Email"], null, $unsubscribeURL);
				if($result === true)
				{
					$successCount++;
					$query = "UPDATE crm_mailing_dispatch SET Sent='Y', ErrorInfo='' WHERE DispatchID=".Connection::GetSQLString($dispatch["DispatchID"]);
					$stmt->Execute($query);
				}
			}
			$this->AddMessage("mailing-resend-result", "crm", array("SuccessCount" => $successCount));
		}
		else
		{
			$this->AddError("mailing-resend-dispatch-required", "crm");
		}
	}
	
	private function ActionSetEmail($request, $user)
	{
		if($request->ValidateEmail("Email"))
		{
			$stmt = GetStatement();
			$table = "crm_".$request->GetProperty("EmailEntity")."_email";
			$id = explode("_", $request->GetProperty("EmailEntity"));
			$id = end($id);
			$id = ucfirst($id)."ID";
			
			$currentEmail = $stmt->FetchField("SELECT Email FROM crm_mailing_dispatch WHERE DispatchID=".$request->GetPropertyForSQL("DispatchID"));
			if($currentEmail)
			{
				$emailID = $stmt->FetchField("SELECT EmailID FROM ".$table." WHERE ".$id."=".$request->GetPropertyForSQL("EmailEntityID")." AND Email=".Connection::GetSQLString($currentEmail));
				if($emailID > 0)
				{
					$query = "UPDATE ".$table."
								SET Email=".$request->GetPropertyForSQL("Email")."
							WHERE EmailID=".Connection::GetSQLString($emailID);
					$stmt->Execute($query);
				}
				else
				{
					$query = "INSERT INTO ".$table."
							SET Email=".$request->GetPropertyForSQL("Email").",
								".$id."=".$request->GetPropertyForSQL("EmailEntityID");
					$stmt->Execute($query);
				}
			}
			else
			{
				$query = "INSERT INTO ".$table."
							SET Email=".$request->GetPropertyForSQL("Email").",
								".$id."=".$request->GetPropertyForSQL("EmailEntityID");
				$stmt->Execute($query);
			}
			$query = "UPDATE crm_mailing_dispatch SET Email=".$request->GetPropertyForSQL("Email")." 
						WHERE DispatchID=".$request->GetPropertyForSQL("DispatchID");
			$stmt->Execute($query);
			$this->AddMessage("mailing-email-updated", "crm");
		}
		else
		{
			$this->AddError("mailing-email-incorrect", "crm");
		}
	}
}

?>