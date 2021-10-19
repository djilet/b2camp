<?php 
require_once(dirname(__FILE__)."/../action.php");

class PropertyAction extends BaseAction
{
	function DoAction($request, $user)
	{
		switch($this->name)
		{
			case "SaveProperty": 
				$this->ActionSaveProperty($request, $user);
			break;
		}
	}
	
	private function ActionSaveProperty($request, $user)
	{
		$stmt = GetStatement();
		$query = "UPDATE ".$this->config['Table']." 
					SET ".$this->actionConfig["Field"]."=".$request->GetPropertyForSQL($this->actionConfig["Name"])." 
					WHERE ".$this->config['ID']."=".$request->GetIntProperty("EntityID");
		$stmt->Execute($query);
	}
}

?>