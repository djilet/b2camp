<?php 
es_include("localobject.php");

class BaseAction extends LocalObject
{
	var $name;
	var $config;
	var $entityConfig;
	var $actionConfig;
	var $contentData = array();
	
	function BaseAction($name, $config)
	{
		$this->name = $name;
		$this->config = $config;
		$this->actionConfig = $config["ActionConfig"][$this->name]["Config"];
		$this->_properties = array();
	}
	
	function DoAction($request, $user){return;}
	
	function GetContentData()
	{
		return $this->contentData;
	}
}

?>