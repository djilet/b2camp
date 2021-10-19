<?php 
require_once(dirname(__FILE__)."/../action.php");

class CommentAction extends BaseAction
{
	function DoAction($request, $user)
	{
		switch($this->name)
		{
			case "AddComment": 
				$this->ActionAddComment($request, $user);
				break;
            case "AddCharacteristic":
                $this->ActionAddCharacteristic($request, $user);
                break;
            case "UpdateCharacteristic":
                $this->Edit($request, $user);
                break;
            case "RemoveCharacteristic":
                $this->ActionRemove($request, $user);
                break;
			break;
		}
	}
	
	private function ActionAddComment($request, $user)
	{
		$stmt = GetStatement();
		$query = "INSERT INTO ".$this->actionConfig["Table"]."(".$this->actionConfig["KeyField"].",ManagerID,Time,Text) 
			VALUES(".$request->GetIntProperty("EntityID").",".$user->GetProperty("UserID").",'".date('Y-m-d H:i:s',time())."',".$request->GetPropertyForSQL("Text").")";
		$stmt->Execute($query);
		
		if(isset($this->actionConfig["FileComponent"]))
		{
			$commentID = $stmt->GetLastInsertID();
			$fileEditComponent = Utilities::GetComponent($this->actionConfig["FileComponent"]["Name"], $this->actionConfig["FileComponent"]["File"], $this->actionConfig["FileComponent"]["Class"], $this->actionConfig["FileComponent"]["Config"]);
			$item = array("EntityID" => $commentID);
			$fileEditComponent->PrepareAfterSave($item, $user);	
		}
	}

    private function ActionAddCharacteristic($request, $user)
    {
        $stmt = GetStatement();
        $query = "INSERT INTO ".$this->actionConfig["Table"]."(".$this->actionConfig["KeyField"].",UserID,Date,Text, SeasonID) 
			VALUES(".$request->GetIntProperty("EntityViewID").",".$user->GetProperty("UserID").",'".date('Y-m-d H:i:s',time())."',".$request->GetPropertyForSQL("Text").",".$request->GetPropertyForSQL("SeasonID").")";
        $stmt->Execute($query);

        if(isset($this->actionConfig["FileComponent"]))
        {
            $commentID = $stmt->GetLastInsertID();
            $fileEditComponent = Utilities::GetComponent($this->actionConfig["FileComponent"]["Name"], $this->actionConfig["FileComponent"]["File"], $this->actionConfig["FileComponent"]["Class"], $this->actionConfig["FileComponent"]["Config"]);
            $item = array("EntityID" => $commentID);
            $fileEditComponent->PrepareAfterSave($item, $user);
        }
    }

    private function Edit($request, $user){
        if (!isset($this->actionConfig['PropertyName']))
            return;

        $propertyName = $this->actionConfig['PropertyName'];
        if (!$request->GetProperty($propertyName))
            return;

        $stmt = GetStatement();
        $query = "UPDATE ".$this->actionConfig["Table"]." SET Text=".$request->GetPropertyForSQL("Text").", SeasonID=".$request->GetProperty("SeasonID")." WHERE CommentID=".$request->GetProperty($propertyName);
        if ($stmt->Execute($query)){
            $this->AddMessage($this->actionConfig['Type']."-updated", "crm");
        }
        else{
            $this->AddError("sql-error");
        }

        $fileSys = new FileSys();
        $dir = CRM_DATA_DIR.$this->actionConfig["Path"]."/";
        if($request->IsPropertySet("FileRemove") && count($request->GetProperty("FileRemove"))>0)
        {
            $query = "SELECT FileID,FileName FROM crm_file WHERE EntityType='".$this->actionConfig["EntityType"]."' AND EntityID=".$request->GetProperty($propertyName)." AND FileID IN (".implode(",", $request->GetProperty("FileRemove")).")";
            $removeList = $stmt->FetchList($query);
            for($i=0; $i<count($removeList); $i++)
            {
                @unlink($dir.$removeList[$i]["FileName"]);
            }
            $query = "DELETE FROM crm_file WHERE EntityType='".$this->actionConfig["EntityType"]."' AND EntityID=".$request->GetProperty($propertyName)." AND FileID IN (".implode(",", $request->GetProperty("FileRemove")).")";
            $stmt->Execute($query);
        }

        $newFiles = $fileSys->Upload("FileUpload", $dir, false, null);

        if($newFiles)
        {
            for($i=0; $i<count($newFiles); $i++)
            {
                $fileType = $newFiles[$i]["FileExtension"];
                $fileName = $newFiles[$i]["FileName"];
                $fileTitle = $newFiles[$i]["name"];
                $query = "INSERT INTO crm_file(EntityType,EntityID,FileType,FileName,FileTitle,ManagerID,Created) VALUES('".$this->actionConfig["EntityType"]."',".$request->GetProperty($propertyName).",".Connection::GetSQLString($fileType).",".Connection::GetSQLString($fileName).",".Connection::GetSQLString($fileTitle).",".$user->GetIntProperty("UserID").",'".date('Y-m-d H:i:s',time())."')";
                $stmt->Execute($query);
            }
        }

        $this->LoadErrorsFromObject($fileSys);
    }

    private function ActionRemove(LocalObject $request, $user){
        $stmt = GetStatement();
        $query = "DELETE FROM ".$this->actionConfig['Table']." WHERE ".$this->actionConfig['KeyField']."=".$request->GetPropertyForSQL($this->actionConfig['KeyValue']);

        if ($stmt->Execute($query))
            $this->AddMessage("entity-is-removed", 'crm');
        else
            $this->AddError("sql-error");
    }
}

?>