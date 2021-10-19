<?php 
require_once(dirname(__FILE__)."/../component.php");

class VisitViewComponent extends BaseComponent
{
	function PrepareBeforeShow(&$item, $user)
	{
		if(isset($item["EntityID"]))
		{
			$stmt = GetStatement();
			$query = "SELECT CONCAT(u.LastName, ' ', u.FirstName) AS ManagerName, c.Date, c.Class, c.Text FROM ".$this->config["Table"]." c LEFT JOIN user u ON c.ManagerID=u.UserID WHERE c.".$this->config["KeyField"]."=".intval($item["EntityID"])." ORDER BY c.Date DESC";
			$visitList = $stmt->FetchList($query);
			$item[$this->name."List"] = $visitList;
			$item[$this->name."DefaultDate"] = date('Y-m-d',time());
		}
	}
}

?>