<?php 
require_once(dirname(__FILE__)."/../component.php");

class ManagerEditComponent extends LinkedViewComponent
{	
	function GetUpdatePrefixForSQL($item)
	{
		return $this->config["FromField"]."=".Connection::GetSQLString($item[$this->name]);
	}

	function PrepareBeforeShow(&$item, $user)
	{
		$itemValue = 0;
		
		if(isset($item[$this->config["FromField"]]))
		{
			$itemValue = $item[$this->config["FromField"]];
		}
		
		$stmt = GetStatement();
		
		$query = "SELECT ".$this->config["ToField"]." AS ID,";
		if(isset($this->config["ViewSQL"]))
		{
			$query .= $this->config["ViewSQL"];
		}
		else
		{ 
			$query .= $this->config["ViewField"];
		}
		$query .= " AS Title, IF(".$this->config["ToField"]."=".$itemValue.", 1, 0) AS Selected FROM ".$this->config["Table"];
		
		$where[] = 'InManagerStat = 1';

		$query .= " WHERE " . implode(" AND ", $where);

		$item[$this->name."ValueList"] = $stmt->FetchList($query);

		$script = '<script type="text/javascript">$(document).ready(function(){';
		$script .= '$("#'.$this->name.'").select2({placeholder:"",allowClear: true}).on("select2-open", function() {$(this).data("select2").results.addClass("overflow-hidden").perfectScrollbar();});';
		$script .= "});</script>";
		$item[$this->name."ControlHTML"] = $script;
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