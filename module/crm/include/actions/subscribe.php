<?php 
require_once(dirname(__FILE__)."/../action.php");

class SubscribeAction extends BaseAction
{
	function DoAction($request, $user)
	{
		switch($this->name)
		{
			case "SaveSubscribers": 
				$this->ActionSaveSubscribers($request, $user);
			break;
		}
	}
	
	private function ActionSaveSubscribers($request, $user)
	{
		$stmt = GetStatement();
		$query = "SELECT ".$this->actionConfig["SubID"]." FROM `".$this->actionConfig["LinkTable"]."` 
					WHERE ".$this->config["ID"]."=".$request->GetProperty("EntityID");
		$currentSubscribers = $stmt->FetchList($query);
		$currentSubscribersArr = array();
		foreach ($currentSubscribers as $subscriber)
		{
			$currentSubscribersArr[] = $subscriber[$this->actionConfig["SubID"]];
			if(!in_array($subscriber[$this->actionConfig["SubID"]], $request->GetProperty("SubscriberID")))
			{
				$query = "DELETE FROM `".$this->actionConfig["LinkTable"]."` 
							WHERE ".$this->config["ID"]."=".$request->GetPropertyForSQL("EntityID")." 
								AND ".$this->actionConfig["SubID"]."=".$subscriber[$this->actionConfig["SubID"]];
				$stmt->Execute($query);
			}
		}
		
		if(is_array($request->GetProperty("SubscriberID")))
		{
			foreach ($request->GetProperty("SubscriberID") as $id)
			{
				if(!in_array($id, $currentSubscribersArr))
				{
					$query = "INSERT INTO `".$this->actionConfig["LinkTable"]."` 
								SET ".$this->config["ID"]."=".$request->GetPropertyForSQL("EntityID").",
								".$this->actionConfig["SubID"]."=".Connection::GetSQLString($id).", 
								`Read`='N'";
					$stmt->Execute($query);
				}
			}
		}
	}
}

?>