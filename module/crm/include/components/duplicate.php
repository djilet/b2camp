<?php 
require_once(dirname(__FILE__)."/../component.php");

class DuplicateEditComponent extends BaseComponent
{
	function PrepareBeforeShow(&$item, $user)
	{
		if(!isset($item["EntityID"]) || !$item["EntityID"])
		{
			$html = "<script type=\"text/javascript\">";
			$html .= 	"$(document).ready(function(){";
			$html .= 		"var duplicateParams = new Array();";
			foreach ($this->config["DuplicateParams"] as $param)
			{
				$html .= "duplicateParams.push({'Field': '".$param["Field"]."', 'Filter':'".$param["Filter"]."'});";
			}
			$html .= "InitDuplicateControl(duplicateParams, '".$this->config["Entity"]."');";
			$html .= 	"});";
			$html .= "</script>";
			$item[$this->name."ControlHTML"] = $html;
		}
	}
}

?>