<?php 
require_once(dirname(__FILE__)."/../action.php");

class AssignAction extends BaseAction
{
	function DoAction($request, $user)
	{
		switch($this->name)
		{
			case "Reassign": 
				$this->ActionReassign($request, $user);
			break;
		}
	}
	
	private function ActionReassign($request, $user)
	{
		if($user->GetProperty("Role") != INTEGRATOR)
			return false;
			
		$stmt = GetStatement();
		$query = "UPDATE `".$this->actionConfig["Table"]."` 
						SET `".$this->actionConfig["ManagerField"]."`=".$request->GetPropertyForSQL("ManagerID")." 
					WHERE `".$this->actionConfig["KeyField"]."` IN (".implode(", ", Connection::GetSQLArray($request->GetProperty("EntityIDs"))).")";
		$stmt->Execute($query);
	}
}

?>