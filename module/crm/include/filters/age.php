<?php 
require_once(dirname(__FILE__)."/../filter.php");

class AgeFilter extends BaseFilter
{
	function AppendSQLCondition($request, &$join, &$where, &$having)
	{
	    $table = (isset($this->config['TablePrefix'])) ? $this->config['TablePrefix'] : "t";

		if(isset($this->config["Disabled"]) && $this->config["Disabled"])
		{
			return;
		}
		if($request->ValidateNotEmpty($this->config["Name"]))
		{
			$where[] = "TIMESTAMPDIFF(YEAR, ".$table.".".$this->config["DOBField"].", NOW())=".$request->GetIntProperty($this->config["Name"]);
		}
	}
}

class DOBFilter extends BaseFilter
{
	function AppendSQLCondition($request, &$join, &$where, &$having)
	{
	    $table_prefix = isset($this->config['TablePrefix'])? $this->config['TablePrefix']:"t";

		if(isset($this->config["Disabled"]) && $this->config["Disabled"])
		{
			return;
		}
		$where[] = "DATE_FORMAT(".$table_prefix.".".$this->config["DOBField"].", '%m-%d') = ".Connection::GetSQLString(date("m-d"));
	}
}

?>