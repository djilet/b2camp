<?php
es_include("localobject.php");
/**
 * Current version of UserAction saves action to db when script is ending 
 * to allow load main request from one place and append any properties later from another places
 */
class UserAction extends LocalObject
{
	public $trackRequest;
	private $trackDateTime;
	
	function SetEntityTitle($object)
	{
		$entityTitleObject = new LocalObject();
		$entityTitleObject->LoadFromObject($object, array("LastName", "FirstName", "MiddleName", "Name", "FIO", "Title"));
		$this->trackRequest->SetProperty("EntityTitle", implode(" ", array_filter($entityTitleObject->GetProperties())));
	}
	
	function __construct($data = array())
	{
		parent::LocalObject($data);
		
		//set created datetime in constructor because destructor has wrong timezone
		$this->trackDateTime = date("Y-m-d H:i:s");
	}
	
	function __destruct()
	{
		$user = new User();
		if($user->LoadBySession() && $this->trackRequest)
		{
			
			$filteredRequest = new LocalObject();
			$filteredRequest->LoadFromObject($this->trackRequest, array("load"));
			
			if($this->trackRequest->GetProperty("load") == "crm")
				$filteredRequest->LoadFromObject($this->trackRequest, array("entity", "EntityViewID", "EntityID", "EntityPrintID", "EntityPrintList", "Do", "Action", "EntityTitle"));
			
			$this->SetProperty("Created", $this->trackDateTime);
			$this->SetProperty("UserID", $user->GetProperty("UserID"));
			$this->SetProperty("IP", !empty($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : "");
			$this->SetProperty("RequestMethod", $_SERVER["REQUEST_METHOD"]);
			$this->SetProperty("RequestUri", $_SERVER["REQUEST_URI"]);
			$this->SetProperty("FilteredRequestParams", json_encode($filteredRequest->GetProperties()));
			
			$stmt = GetStatement();
			$query = "INSERT INTO `user_action` 
						SET Created=".$this->GetPropertyForSQL("Created").", 
							UserID=".$this->GetPropertyForSQL("UserID").", 
							IP=".$this->GetPropertyForSQL("IP").", 
							RequestMethod=".$this->GetPropertyForSQL("RequestMethod").",
							RequestUri=".$this->GetPropertyForSQL("RequestUri").",
							FilteredRequestParams=".$this->GetPropertyForSQL("FilteredRequestParams");
			$stmt->Execute($query);
			$this->SetProperty("UserActionID", $stmt->GetLastInsertID());
			$emailNotificationRequired = false;
			if($filteredRequest->GetProperty("load") == "crm")
			{
				if($filteredRequest->GetProperty("entity") == "report" && $filteredRequest->GetProperty("Action") == "Generate")
					$emailNotificationRequired = true;
				elseif($filteredRequest->GetProperty("Action") == "Export")
					$emailNotificationRequired = true;
				elseif($filteredRequest->GetProperty("entity") == "mailing" && $filteredRequest->GetProperty("Action") == "Send")
					$emailNotificationRequired = true;
				elseif($filteredRequest->GetProperty("entity") == "bookkeeping")
					$emailNotificationRequired = true;
			}
			if($emailNotificationRequired)
			{
				$userAction = new UserAction();
				$userAction->LoadByID($this->GetProperty("UserActionID"));
				SendMailFromAdmin("2bcamp@mail.ru", GetTranslation("user-action-notification"), $userAction->GetProperty("UserName").": ".$userAction->GetProperty("Description"));
			}
		}
	}
	
	function LoadByID($id)
	{
		$this->LoadFromSQL("SELECT a.*, CONCAT(u.LastName, ' ', u.FirstName) AS UserName 
								FROM `user_action` AS a 
									LEFT JOIN `user` AS u ON u.UserID=a.UserID  
							WHERE a.UserActionID=".Connection::GetSQLString($id));
		$this->SetProperty("Description", self::GenerateDescription(json_decode($this->GetProperty("FilteredRequestParams"), true)));
	}
	
	static function GenerateDescription($filteredRequestParams)
	{
		$request = new LocalObject($filteredRequestParams);
		$description = array();
		if($request->GetProperty("load"))
			$description[] = GetTranslation("user-action-module-".$request->GetProperty("load"));
		if($request->GetProperty("load") == "crm")
		{
			if($request->GetProperty("entity"))
				$description[] = GetTranslation("user-action-crm-entity-".$request->GetProperty("entity"));
			if($request->GetProperty("EntityViewID"))
				$description[] = GetTranslation("user-action-crm-entity-view");
			if($request->GetProperty("EntityPrintID"))
				$description[] = GetTranslation("user-action-crm-entity-print");
			if($request->GetProperty("EntityPrintList"))
				$description[] = GetTranslation("user-action-crm-entity-print-list");
			if($request->GetProperty("EntityID") && $request->GetProperty("Action"))
				$description[] = GetTranslation("user-action-crm-entity-view");
			elseif($request->GetProperty("EntityID"))
				$description[] = GetTranslation("user-action-crm-entity-edit");
			elseif($request->IsPropertySet("EntityID"))
				$description[] = GetTranslation("user-action-crm-entity-add");
			if($request->GetProperty("Do") == "Remove")
				$description[] = GetTranslation("user-action-crm-entity-remove");
			elseif($request->GetProperty("Do") == "Cancel")
				$description[] = GetTranslation("user-action-crm-entity-cancel");
			if($request->GetProperty("EntityTitle"))
				$description[] = $request->GetProperty("EntityTitle");
			if($request->GetProperty("Action"))
				$description[] = GetTranslation("user-action-crm-action-".$request->GetProperty("Action"));
		}
		return implode(" - ", $description);
	}
}

?>