<?php 
require_once(dirname(__FILE__)."/../component.php");

class ListCountViewComponent extends BaseComponent
{
	function PrepareBeforeShow(&$item, $user)
	{
		$count = 0;
		foreach ($this->config["Fields"] as $field)
		{
			if(isset($item[$field]) && is_array($item[$field]))
			{
				$count += count($item[$field]);
			}
		}
		$item[$this->name] = $count;
	}
}

?>