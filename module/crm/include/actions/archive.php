<?php 
require_once(dirname(__FILE__)."/../action.php");

class ArchiveAction extends BaseAction
{
	function DoAction($request, $user)
	{
		switch($this->name)
		{
			case "SendToArchive": 
				$this->ActionSendToArchive($request, $user);
			break;
			case "RemoveFromArchive":
				$this->Action‌RemoveFromArchive($request, $user);
			break;
		}
	}
	
	private function ActionSendToArchive($request, $user)
	{
		$stmt = GetStatement();
		$query = "UPDATE `".$this->actionConfig["Table"]."` 
						SET `".$this->actionConfig["ArchiveField"]."`='Y' 
					WHERE `".$this->actionConfig["KeyField"]."` IN (".implode(", ", Connection::GetSQLArray($request->GetProperty("EntityIDs"))).")";
		$stmt->Execute($query);
	}
	private function Action‌RemoveFromArchive($request, $user)
	{
		$stmt = GetStatement();
		$query = "UPDATE `".$this->actionConfig["Table"]."` 
						SET `".$this->actionConfig["ArchiveField"]."`='N' 
					WHERE `".$this->actionConfig["KeyField"]."` IN (".implode(", ", Connection::GetSQLArray($request->GetProperty("EntityIDs"))).")";
		$stmt->Execute($query);
	}
}

?>