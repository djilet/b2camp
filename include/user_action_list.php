<?php
es_include("localobject.php");
es_include("user_action.php");

class UserActionList extends LocalObjectList
{
	function UserActionList($data = array())
	{
		parent::LocalObjectList($data);
		$this->SetSortOrderFields(array(
			"created_asc" => "a.Created ASC",
			"created_desc" => "a.Created DESC",
		));
		$this->SetOrderBy("created_desc");
		$this->SetItemsOnPage(100);
	}
	
	function LoadUserActionList($request)
	{
		$where = array();
		if($request->GetProperty("FilterUserID"))
			$where[] = "a.UserID=".$request->GetPropertyForSQL("FilterUserID");
		if($request->GetProperty("FilterDateFrom"))
			$where[] = "DATE(a.Created) >= ".$request->GetPropertyForSQL("FilterDateFrom");
		if($request->GetProperty("FilterDateTo"))
			$where[] = "DATE(a.Created) <= ".$request->GetPropertyForSQL("FilterDateTo");
		
		$query = "SELECT a.*, DATE(a.Created) AS CreatedDate, CONCAT(u.LastName, ' ', u.FirstName) AS UserName 
					FROM `user_action` AS a 
						LEFT JOIN `user` AS u ON u.UserID=a.UserID  
					".(count($where) > 0 ? " WHERE ".implode(" AND ", $where) : "");
		$this->SetCurrentPage();
		$this->LoadFromSQL($query);
		$this->PrepareBeforeShow();
	}
	
	function PrepareBeforeShow()
	{
		for($i = 0; $i < $this->GetCountItems(); $i++)
		{
			if($i == 0 || $this->_items[$i]["CreatedDate"] != $this->_items[$i-1]["CreatedDate"])
				$this->_items[$i]["ShowDateRow"] = 1;
			
			$filteredRequest = new LocalObject(json_decode($this->_items[$i]["FilteredRequestParams"], true));
			$this->_items[$i]["Description"] = UserAction::GenerateDescription($filteredRequest->GetProperties());
			
			//build url for overseer to follow to check the action
			$requestURL = str_replace(PROJECT_PATH, "", GetUrlPrefix()).$this->_items[$i]["RequestUri"];
			$parts = parse_url($requestURL);
			$tempRequest = new LocalObject();
			$tempRequest->LoadFromObject($filteredRequest, array("load", "entity", "EntityID", "EntityViewID"));
			$this->_items[$i]["RequestURL"] = $parts["scheme"]."://".$parts["host"].$parts["path"]."?".http_build_query($tempRequest->GetProperties());
		}
	}
}

?>