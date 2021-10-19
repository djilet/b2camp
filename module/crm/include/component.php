<?php 

class BaseComponent
{
	var $name;
	var $config;
	
	function BaseComponent($name, $config)
	{
		$this->name = $name;
		$this->config = $config;
	}
	
	function GetSelectPrefixForSQL(){return false;}
	function GetUpdatePrefixForSQL($item){return false;}
	function PrepareBeforeShow(&$item, $user){return;}
	function PrepareBeforeSave(&$item, $user){return false;}
	function PrepareAfterSave(&$item, $user){return false;}
	function Validate(&$item){return true;}
}

?>