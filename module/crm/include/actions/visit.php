<?php 
require_once(dirname(__FILE__)."/../action.php");

class VisitAction extends BaseAction
{
	function DoAction($request, $user)
	{
		switch($this->name)
		{
			case "AddVisit": 
				$this->ActionAddVisit($request, $user);
			break;
		}
	}
	
	private function ActionAddVisit($request, $user)
	{
		$stmt = GetStatement();
		$query = "INSERT INTO ".$this->actionConfig["Table"]."(".$this->actionConfig["KeyField"].",ManagerID,Date,Class,Text) 
			VALUES(".$request->GetIntProperty("EntityID").",".$user->GetProperty("UserID").",".$request->GetPropertyForSQL("Date").",".$request->GetPropertyForSQL("Class").",".$request->GetPropertyForSQL("Text").")";
		$stmt->Execute($query);
	}
}

?>