<?php 
require_once(dirname(__FILE__)."/../component.php");

class ChildlistComponent extends BaseComponent
{
	function PrepareBeforeShow(&$item, $user)
	{
        $stmt = GetStatement();
		$query = 'SELECT * from crm_child';
        $item[$this->name."List"] = $stmt->FetchList($query);
	}
}

?>