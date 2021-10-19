<?php 
require_once(dirname(__FILE__)."/../component.php");

class ContentViewComponent extends BaseComponent
{
	function GetSelectPrefixForSQL()
	{
		return "t.".$this->config["Field"];
	}
	
	function PrepareBeforeShow(&$item, $user)
	{
		$item[$this->name] = PrepareContentBeforeShow($item[$this->config["Field"]]);
	}
}

?>