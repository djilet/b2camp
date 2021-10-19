<?php

es_include("localobjectlist.php");

class UserList extends LocalObjectList
{
	var $module;
	var $params;

	function UserList($data = array())
	{
		parent::LocalObjectList($data);

		$this->SetItemsOnPage(abs(intval(GetFromConfig("UsersPerPage"))));

		$this->SetSortOrderFields(array(
			"UserIDAsc" => "UserID ASC",
			"UserIDDesc" => "UserID DESC",
			"CreatedAsc" => "Created ASC",
			"CreatedDesc" => "Created DESC",
			"NameAsc" => "LastName ASC, FirstName ASC",
			"NameDesc" => "LastName DESC, FirstName DESC",
			"LastLoginAsc" => "LastLogin ASC",
			"LastLoginDesc" => "LastLogin DESC"));
		$this->SetDefaultOrderByKey(GetFromConfig("UsersOrderBy"));
		$this->params = LoadImageConfig('UserImage', "user", GetFromConfig("UserImage"));
	}

	function GetQueryPrefix()
	{
		$query = "SELECT UserID, Email, FirstName, MiddleName, LastName, CONCAT(LastName, ' ', FirstName) AS Name, 
					CONCAT(FirstName, MiddleName, LastName, Email) AS SearchString, UserImage, UserImageConfig,
					DOB, Sex, Phone, Social, City, Street, House, Flat, Role, Created, LastLogin, LastIP 
					FROM `user`";

		return $query;
	}

	function LoadManagerList()
	{
		$this->SetOrderBy(isset($_REQUEST[$this->GetOrderByParam()]) ? $_REQUEST[$this->GetOrderByParam()] : GetFromConfig("UsersOrderBy"));

		$where = array();
		$having = array();

		$where[] = 'InManagerStat = 1';
		
		$query = $this->GetQueryPrefix().(count($where) > 0 ? "WHERE ".implode(" AND ", $where) : "").(count($having) > 0 ? "HAVING ".implode(" AND ", $having) : "");

		$this->SetCurrentPage();
		$this->LoadFromSQL($query);

		for ($i = 0; $i < count($this->_items); $i++)
		{
			$this->_items[$i]["RoleTitle"] = GetTranslation("role-".$this->_items[$i]["Role"]);
		}
		$this->PrepareBeforeShow();
	}

	function LoadUserList($request, $fullList = false)
	{
		$this->SetOrderBy(isset($_REQUEST[$this->GetOrderByParam()]) ? $_REQUEST[$this->GetOrderByParam()] : GetFromConfig("UsersOrderBy"));

		$where = array();
		$having = array();
		
		$roleList = $request->GetProperty("RoleList");
		if (is_array($roleList) && count($roleList) > 0)
		{
			$where[] = "Role IN (".implode(",", Connection::GetSQLArray($roleList)).")";
		}
		if ($request->GetProperty("SearchString"))
		{
			$words = explode(" ", $request->GetProperty("SearchString"));
			foreach ($words as $word)
			{
				$having[] = "INSTR(SearchString, ".Connection::GetSQLString($word).")";
			}
		}

		$query = $this->GetQueryPrefix().(count($where) > 0 ? "WHERE ".implode(" AND ", $where) : "").(count($having) > 0 ? "HAVING ".implode(" AND ", $having) : "");

		if($fullList)
		{
			$this->SetItemsOnPage(0);
		}
		$this->SetCurrentPage();
		$this->LoadFromSQL($query);

		for ($i = 0; $i < count($this->_items); $i++)
		{
			$this->_items[$i]["RoleTitle"] = GetTranslation("role-".$this->_items[$i]["Role"]);
		}
		$this->PrepareBeforeShow();
	}
	
	function PrepareBeforeShow()
	{
		for($i = 0; $i < $this->GetCountItems(); $i++)
		{
			if (isset($this->_items[$i]["UserImage"]) && $this->_items[$i]["UserImage"])
			{
				
				$imageConfig = LoadImageConfigValues("UserImage", $this->_items[$i]["UserImageConfig"]);
				
				$this->_items[$i] = array_merge($this->_items[$i], $imageConfig);
				
				for ($j = 0; $j < count($this->params); $j++)
				{
					$v = $this->params[$j];
	
					if($v["Resize"] == 13)
						$this->_items[$i][$v["Name"]."Path"] = InsertCropParams($v["Path"], 
																		$this->_items[$i][$v["Name"]."X1"], 
																		$this->_items[$i][$v["Name"]."Y1"], 
																		$this->_items[$i][$v["Name"]."X2"], 
																		$this->_items[$i][$v["Name"]."Y2"]).$this->GetProperty("UserImage");
					else
						$this->_items[$i][$v["Name"]."Path"] = $v["Path"].$this->_items[$i]["UserImage"];
				}
				
			}
		}
		
	}

	function Remove($request)
	{
		$ids = $request->GetProperty("UserIDs");
		if (is_array($ids) && count($ids) > 0)
		{
			$where = array();

			$where[] = "UserID IN (".implode(",", Connection::GetSQLArray($ids)).")";

			$roleList = $request->GetProperty("RoleList");
			if (is_array($roleList) && count($roleList) > 0)
			{
				$where[] = "Role IN (".implode(",", Connection::GetSQLArray($roleList)).")";
			}
			if ($request->GetIntProperty("CurrentUserID") > 0)
			{
				$where[] = "UserID<>".$request->GetIntProperty("CurrentUserID");
			}

			$stmt = GetStatement();

			$removed = array();
			$removedIDs = array();

			$query = $this->GetQueryPrefix()." WHERE ".implode(" AND ", $where);
			if ($result = $stmt->FetchList($query))
			{
				for ($i = 0; $i < count($result); $i++)
				{
					$removed[] = $result[$i]['Name'];
					$removedIDs[] = $result[$i]['UserID'];
					if($result[$i]["UserImage"])
					{
						@unlink(USER_IMAGE_DIR.$result[$i]["UserImage"]);
					}
				}
			}
			
			$count = count($removed);

			if ($count > 0)
			{
				// Delete user sessions
				$query = "DELETE FROM `session` WHERE UserID IN (".implode(",", Connection::GetSQLArray($removedIDs)).")";
				$stmt->Execute($query);

				// Delete user
				$query = "DELETE FROM `user` WHERE UserID IN (".implode(",", Connection::GetSQLArray($removedIDs)).")";
				$stmt->Execute($query);

				if ($count > 1)
					$key = "users-are-removed";
				else
					$key = "user-is-removed";

				$this->AddMessage($key, array("UserList" => "\"".implode("\", \"", $removed)."\"", "UserCount" => $count));
			}
		}
	}
}

?>