<?php 
require_once(dirname(__FILE__)."/../component.php");

class FileViewComponent extends BaseComponent
{
	function PrepareBeforeShow(&$item, $user)
	{
		if(isset($item["EntityID"]))
		{
			$stmt = GetStatement();
			$query = "SELECT FileID,FileName,FileTitle FROM crm_file WHERE EntityType='".$this->config["EntityType"]."' AND EntityID=".intval($item["EntityID"])." ORDER BY FileTitle";
			$fileList = $stmt->FetchList($query);
			for($i=0; $i<count($fileList); $i++)
			{
				$fileList[$i]["FilePath"] = CRM_DATA_PATH.$this->config["Path"]."/".$fileList[$i]["FileName"];
			}
			$item[$this->name."List"] = $fileList;
		}
	}
}

class FileEditComponent extends FileViewComponent
{
	function PrepareBeforeShow(&$item, $user)
	{
		parent::PrepareBeforeShow($item, $user);
	
		$script = '<script type="text/javascript">$(document).ready(function(){';
		$script .= 'InitFileControl($("#'.$this->name.'"));';
		$script .= "});</script>";
		$item[$this->name."ControlHTML"] = $script;
	}
	
	function PrepareAfterSave(&$item, $user)
	{
		$stmt = GetStatement();
		$fileSys = new FileSys();
		$dir = CRM_DATA_DIR.$this->config["Path"]."/";
		
		if(isset($item[$this->name."Remove"]) && strlen($item[$this->name."Remove"])>0)
		{
			$removeFiles = explode(",", $item[$this->name."Remove"]);
			$query = "SELECT FileID,FileName FROM crm_file WHERE EntityType='".$this->config["EntityType"]."' AND EntityID=".intval($item["EntityID"])." AND FileID IN (".implode(",", Connection::GetSQLArray($removeFiles)).")";
			$removeList = $stmt->FetchList($query);
			for($i=0; $i<count($removeList); $i++)
			{
				@unlink($dir.$removeList[$i]["FileName"]);
			}
			$query = "DELETE FROM crm_file WHERE EntityType='".$this->config["EntityType"]."' AND EntityID=".intval($item["EntityID"])." AND FileID IN (".implode(",", Connection::GetSQLArray($removeFiles)).")";
			$stmt->Execute($query);
		}
		
		$newFiles = $fileSys->Upload($this->name."Upload", $dir, false, null);
		if($newFiles)
		{
			for($i=0; $i<count($newFiles); $i++)
			{
				$fileType = $newFiles[$i]["FileExtension"];
				$fileName = $newFiles[$i]["FileName"];
				$fileTitle = $newFiles[$i]["name"];
				$query = "INSERT INTO crm_file(EntityType,EntityID,FileType,FileName,FileTitle,ManagerID,Created) VALUES('".$this->config["EntityType"]."',".intval($item["EntityID"]).",".Connection::GetSQLString($fileType).",".Connection::GetSQLString($fileName).",".Connection::GetSQLString($fileTitle).",".$user->GetIntProperty("UserID").",'".date('Y-m-d H:i:s',time())."')";
				$stmt->Execute($query);
			}
		}
		
		return $fileSys->GetErrors();
	}
}

?>