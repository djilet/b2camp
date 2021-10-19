<?php 
require_once(dirname(__FILE__)."/../filter.php");

class MailingFilter extends BaseFilter
{
	function AppendSQLCondition($request, &$join, &$where, &$having)
	{
		if(isset($this->config["Disabled"]) && $this->config["Disabled"])
		{
			return;
		}
		if($request->ValidateNotEmpty($this->config["Name"]))
		{
			$email = $request->GetProperty($this->config["Name"]);

			$join[] = "LEFT JOIN crm_mailing_dispatch cmd ON t.MailingID = cmd.MailingID";
			$where[] = "cmd.Email LIKE '%".$email."%'";
		}
	}
}

?>