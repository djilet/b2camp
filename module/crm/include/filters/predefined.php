<?php 
require_once(dirname(__FILE__)."/../filter.php");

class PredefinedFilter extends BaseFilter
{
	function AppendSQLCondition($request, &$join, &$where, &$having)
	{
		if(isset($this->config["Disabled"]) && $this->config["Disabled"])
		{
			return;
		}
		$where[] = "t.".$this->config["Field"]."=".Connection::GetSQLString($this->config["Value"]);
	}
	
	function LoadFilterData($request, &$content)
	{
		return;
	}
}

class PredefinedFilterSending extends BaseFilter
{
	function AppendSQLCondition($request, &$join, &$where, &$having)
	{
		if(isset($this->config["Disabled"]) && $this->config["Disabled"])
		{
			return;
		}
		$where[] = "d.".$this->config["Field"]."=".Connection::GetSQLString($this->config["Value"]);
		$join[] = "LEFT JOIN ".$this->config["MailingTable"]." d ON t.".$this->config['Using']."=d.".$this->config['Using'];
	}

	function LoadFilterData($request, &$content)
	{
		return;
	}
}
?>