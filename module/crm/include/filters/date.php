<?php 
require_once(dirname(__FILE__)."/../filter.php");

class DateFilter extends BaseFilter
{
	function AppendSQLCondition($request, &$join, &$where, &$having)
	{
		if(isset($this->config["Disabled"]) && $this->config["Disabled"])
		{
			return;
		}
		if($request->ValidateNotEmpty($this->config["Name"]))
		{
			$tempWhere = array();
			foreach ($this->config["Fields"] as $field)
			{
				$tempWhere[] .= "DATE(t.".$field.") ".$this->config["Operation"]." ".Connection::GetSQLString(ToSQLDate($request->GetProperty($this->config["Name"])));
			}
			$tempWhere = "(".implode($tempWhere, " OR ").")";
			$where[] = $tempWhere;
		}
	}
}

class CurrentDateFilter extends BaseFilter
{
	function AppendSQLCondition($request, &$join, &$where, &$having)
	{
		if(isset($this->config["Disabled"]) && $this->config["Disabled"])
		{
			return;
		}
		$where[] = "DATE(t.".$this->config["Field"].") ".$this->config["Operation"]." ".Connection::GetSQLString(date("Y-m-d"));
	}
}

?>