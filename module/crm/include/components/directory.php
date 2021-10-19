<?php 
require_once(dirname(__FILE__)."/../component.php");

class DirectoryViewComponent extends BaseComponent
{	
	function GetUpdatePrefixForSQL($item)
	{
		return $this->config["FromField"]."=".Connection::GetSQLString($item[$this->name]);
	}
	
	function PrepareBeforeShow(&$item, $user)
	{	
		parent::PrepareBeforeShow($item, $user);

		$stmt = GetStatement();

		$directoryCondition = '';

		if ($this->config['DirectoryType'] != null) {
			$directoryCondition = ' WHERE DirectoryType = ' . $this->config['DirectoryType'];
		}

		$query = "SELECT DirectoryID as ".$this->config['ViewId'].", Name as ".$this->config['ViewName']." FROM crm_directory ".$directoryCondition;
		$item[$this->name."List"] = $stmt->FetchList($query);

		if(isset($item["EntityID"]))
		{
			
			$stmt = GetStatement();
		
			$directoryCondition = ' WHERE book.BookkeepingID = '.intval($item["EntityID"]);
		
			if ($this->config['DirectoryType'] != null) {
				$directoryCondition .= ' AND dir.DirectoryType = ' . $this->config['DirectoryType'];
				
				if ($this->config['DirectoryType'] == 1) 
					$join = 'LEFT JOIN crm_directory dir on dir.DirectoryID = book.Check';
				if ($this->config['DirectoryType'] == 2 || $this->config['DirectoryType'] == 3) 
					$join = 'LEFT JOIN crm_directory dir on dir.DirectoryID = book.ArticleID';
			}
		
			$query = 'SELECT book.Amount Amount, dir.Name Name, dir.Color Color FROM crm_bookkeeping book '.$join.' '.$directoryCondition;
			$result = $stmt->FetchRow($query);
			$item[$this->name."Name"] = $result['Name'];

			if ($this->config['DirectoryType'] == 1) 
			{
				$item["BillColor"] = $result['Color'];
				$item["WhiteRow"] = $item["BillColor"] ? 'white' : '';
			}

			$item['AmountOutcome'] = '';
			
			if ($this->config['DirectoryType'] == 2)
				$item['AmountIncome'] = $result['Amount'];	
			elseif ($this->config['DirectoryType'] == 3)
				$item['AmountOutcome'] = $result['Amount'];
			
		}
	}
}

class DirectorySingleViewComponent extends BaseComponent
{
	function PrepareBeforeShow(&$item, $user)
	{
		parent::PrepareBeforeShow($item, $user);

		if(isset($item["EntityID"]))
		{
			$stmt = GetStatement();

			$directoryCondition = ' WHERE DirectoryID = '.intval($item["EntityID"]);

			if ($this->config['DirectoryType'] != null) {
				$directoryCondition = ' AND DirectoryType = ' . $this->config['DirectoryType'];
			}

			$query = "SELECT Name FROM crm_directory ".$directoryCondition;

			$item[$this->name."Name"] = $stmt->FetchList($query);
			
		}
	}
}


class DirectoryEditComponent extends BaseComponent
{
	function GetUpdatePrefixForSQL($item)
	{
		return $this->config["FromField"]."=".Connection::GetSQLString($item[$this->name]);
	}

	function PrepareBeforeShow(&$item, $user)
	{
		if(isset($item[$this->config["FromField"]]))
		{
			$itemValue = $item[$this->config["FromField"]];
		}

		parent::PrepareBeforeShow($item, $user);

		$stmt = GetStatement();

		$directoryCondition = '';

		if(isset($item["EntityID"]))
		{	
			$query = "SELECT * FROM crm_bookkeeping Where BookkeepingID=".$item["EntityID"];
			$item_tmp = $stmt->FetchRow($query);

			if ($this->config['DirectoryType'] != 1) {	
				$type = $item_tmp['ArticleType'] == 2 ? 3 : 2;
				$directoryCondition = ' WHERE DirectoryType = ' . $type;
			}
			else
				$directoryCondition = ' WHERE DirectoryType = 1';
				
			$viewName = $this->config['ViewId'] == '`Check`' ? 'Check' : $this->config['ViewId'];
				
			$query = "SELECT DirectoryID as ".$this->config['ViewId'].", Name as ".$this->config['ViewName']." , IF(DirectoryID=".$item_tmp[$viewName].", 1, 0) AS Selected
						FROM crm_directory ".$directoryCondition;
				
			$item[$this->name."List"] = $stmt->FetchList($query);

			$stmt = GetStatement();

			$directoryCondition = ' WHERE book.BookkeepingID = '.intval($item["EntityID"]);

			if ($this->config['DirectoryType'] != null) {
				$directoryCondition .= ' AND dir.DirectoryType = ' . $this->config['DirectoryType'];

				if ($this->config['DirectoryType'] == 1)
					$join = 'LEFT JOIN crm_directory dir on dir.DirectoryID = book.Check';
				if ($this->config['DirectoryType'] == 2 || $this->config['DirectoryType'] == 3)
					$join = 'LEFT JOIN crm_directory dir on dir.DirectoryID = book.ArticleID';
			}

			$query = 'SELECT book.Amount Amount, dir.Name Name, dir.Color Color FROM crm_bookkeeping book '.$join.' '.$directoryCondition;
			$result = $stmt->FetchRow($query);
			$item[$this->name."Name"] = $result['Name'];

			if ($this->config['DirectoryType'] == 1)
			{
				$item["BillColor"] = $result['Color'];
				$item["WhiteRow"] = $item["BillColor"] ? 'white' : '';
			}

			$item['AmountOutcome'] = '';
				
			if ($this->config['DirectoryType'] == 2)
				$item['AmountIncome'] = $result['Amount'];
			elseif ($this->config['DirectoryType'] == 3)
			$item['AmountOutcome'] = $result['Amount'];
				
		}
		else
		{
			if ($this->config['DirectoryType'] != null) {
				$directoryCondition = ' WHERE DirectoryType = ' . $this->config['DirectoryType'];
			}
			
			$query = "SELECT DirectoryID as ".$this->config['ViewId'].", Name as ".$this->config['ViewName']." FROM crm_directory ".$directoryCondition;
			$item[$this->name."List"] = $stmt->FetchList($query);
		}
			
	}
	
	function Validate(&$item)
	{
		if(isset($this->config["Required"]) && $this->config["Required"])
		{
			if(!$item->ValidateInt($this->name) || !$item->GetIntProperty($this->name) > 0)
				$item->AddValidateError('select', $this->name);
		}
	}
}

?>