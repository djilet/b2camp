<?php 
require_once(dirname(__FILE__)."/../filter.php");

class SubscribeFilter extends BaseFilter
{
	function AppendSQLCondition($request, &$join, &$where, &$having)
	{
		if(isset($this->config["Disabled"]) && $this->config["Disabled"])
		{
			return;
		}
		$user = new User();
		$user->LoadBySession();
		$tempWhere = array();
		if($request->GetProperty($this->name) == "out")
		{
			if(isset($this->config["OwnerField"]))
			{
				$tempWhere[] = "t.".$this->config["OwnerField"]."=".$user->GetPropertyForSQL("UserID");
			}
		}
		else
		{
			if($user->GetProperty("Role") == MANAGER || $user->GetProperty("Role") == GUIDE)
			{
				if(isset($this->config["MainSubField"]))
				{
					$tempWhere[] = "t.".$this->config["MainSubField"]."=".$user->GetPropertyForSQL("UserID");
				}
			}
			/*
			if($user->GetProperty("Role") == MANAGER)
			{
				if(isset($this->config["LinkTable"]))
				{
					$alias = "l".count($join);
					$join[] = "LEFT JOIN ".$this->config["LinkTable"]." ".$alias." ON t.".$this->config["FromField"]."=".$alias.".".$this->config["ToField"];
					$tempWhere[] = $alias.".".$this->config["LinkSubField"]."=".$user->GetProperty("UserID");
				}
			}
			*/
		}
		if(count($tempWhere) > 0)
			$where[] = "(".implode(" OR ", $tempWhere).")";
	}
}

?>