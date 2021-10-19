<?php 
require_once(dirname(__FILE__)."/../component.php");


class ReadListViewComponent extends BaseComponent
{
	function PrepareBeforeShow(&$item, $user)
	{
		$stmt = GetStatement();
		$query = "SELECT ";
		
		if(isset($this->config["ViewSQL"]))
			$query .= $this->config["ViewSQL"];
		else 
			$query .= "`".$this->config["ViewField"]."`";

		$query .= " FROM ".$this->config["Table"]." 
					WHERE ".$this->config["ToField"]."=".intval($item["EntityID"])." 
					AND ".$this->config["UserField"]."=".$user->GetProperty("UserID");
		
		$item[$this->name.$this->config["ViewField"]] = $stmt->FetchField($query);
	}
}

class ReadViewComponent extends BaseComponent
{
	function PrepareBeforeShow(&$item, $user)
	{
		if(isset($_REQUEST["EntityViewID"]))
		{
			$stmt = GetStatement();
			if(isset($this->config["Table"]))
			{
				$query = "UPDATE ".$this->config["Table"]." 
							SET `".$this->config["MainReadField"]."`='Y' 
							WHERE ".$this->config["EntityField"]."=".$item["EntityID"]." AND ".$this->config["MainUserField"]."=".$user->GetPropertyForSQL("UserID");
				$stmt->Execute($query);
			}
			if(isset($this->config["LinkTable"]))
			{
				$query = "UPDATE ".$this->config["LinkTable"]." 
							SET `".$this->config["LinkReadField"]."`='Y' 
							WHERE ".$this->config["EntityField"]."=".$item["EntityID"]." AND ".$this->config["LinkUserField"]."=".$user->GetPropertyForSQL("UserID");
				$stmt->Execute($query);
			}
		}
	}
	
	function GetUnreadEntityCount()
	{
		$user = new User();
		$user->LoadBySession();
		
		$ids = array();
		
		$stmt = GetStatement();
		if(!is_null($this->config["Table"]))
		{
			$query = "SELECT ".$this->config["EntityIDField"]." AS EntityID FROM ".$this->config["Table"]." 
						WHERE ".$this->config["UserField"]."=".$user->GetPropertyForSQL("UserID")." AND `".$this->config["ReadField"]."`='N'";
			$entityList = $stmt->FetchList($query);
			foreach ($entityList as $entity)
			{
				if(!in_array($entity["EntityID"], $ids))
					$ids[] = $entity["EntityID"];
			}
		}
		if(!in_array($user->GetProperty("Role"), array(MANAGER, GUIDE)))
		{
			if(!is_null($this->config["LinkTable"]))
			{
				$query = "SELECT t.".$this->config["EntityIDField"]." AS EntityID FROM ".$this->config["LinkTable"]." AS t ";
				//dont count subscriptions for deleted items
				if(!is_null($this->config["Table"]))
				{
					$query .= "JOIN ".$this->config["Table"]." AS j ON j.".$this->config["EntityIDField"]."=t.".$this->config["EntityIDField"];
				}
				$query .= " WHERE t.".$this->config["LinkUserField"]."=".$user->GetPropertyForSQL("UserID")." AND t.".$this->config["LinkReadField"]."='N'";
				$linkList = $stmt->FetchList($query);
				foreach ($linkList as $link)
				{
					if(!in_array($link["EntityID"], $ids))
						$ids[] = $link["EntityID"];
				}
			}
		}
		return count($ids);
	}
}

?>