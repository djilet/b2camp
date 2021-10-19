<?php 
require_once(dirname(__FILE__)."/../component.php");

class AgeViewComponent extends BaseComponent
{
	function GetSelectPrefixForSQL($prefix = "t")
	{
		return $prefix.".".$this->config["DOBField"];
	}
	
	function PrepareBeforeShow(&$item, $user)
	{
		if($item[$this->config["DOBField"]])
		{
			$age = floor((time() - strtotime($item[$this->config["DOBField"]])) / 31556926);
			$item[$this->name] = $age.$this->GetAgeSuffix($age);
		}
	}
	
	private function GetAgeSuffix($age)
	{
		$year = abs($age);
    	$t1 = $age % 10;
    	$t2 = $age % 100;
    	return ($t1 == 1 && $t2 != 11 ? " год" : ($t1 >= 2 && $t1 <= 4 && ($t2 < 10 || $t2 >= 20) ? " года" : " лет"));
	}
}

?>