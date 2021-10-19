<?php 
require_once(dirname(__FILE__)."/../component.php");

class CurrentDateViewComponent extends BaseComponent
{
	function GetSelectPrefixForSQL()
	{
		return null;
	}
	
	function PrepareBeforeShow(&$item, $user)
	{
		$result = true;
		foreach ($this->config["Conditions"] as $condition)
		{
			$result = $result && eval('return '.strtotime($item[$condition["Field"]]).' '.$condition["Operation"].' '.time().';');
		}
		$item[$this->name] = $result;
	}
}

?>