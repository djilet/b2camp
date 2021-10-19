<?php 
require_once(dirname(__FILE__)."/../component.php");

class DataComponent extends BaseComponent
{
	function PrepareBeforeShow(&$item, $user)
	{
		$request = new LocalObject(array_merge($_GET, $_POST));
		if(isset($this->config["Table"]))
		{
			//select
			$selectedValues = array();
			if($request->GetProperty($this->config["RequestField"]))
			{
				if(is_array($request->GetProperty($this->config["RequestField"])))
					$selectedValues = array_filter($request->GetProperty($this->config["RequestField"]));
				else 
					$selectedValues = array($request->GetProperty($this->config["RequestField"]));
			}
			
			$stmt = GetStatement();

			$query = "SELECT ".$this->config["KeyField"]." AS ID,";
			if(isset($this->config["ViewSQL"]))
				$query .= $this->config["ViewSQL"];
			else
				$query .= $this->config["ViewField"];
			$query .= " AS Title ";
			
			if(count($selectedValues) > 0)
			{
				$query .= ", IF(".$this->config["KeyField"]." IN(".implode(", ", Connection::GetSQLArray($selectedValues))."), 1, 0) AS Selected ";	
			}
			$query .= " FROM ".$this->config["Table"];

			if (isset($this->config["IsManager"]) && $this->config["IsManager"]) {
				$query .= " WHERE ".$this->config["Table"].".InManagerStat = 1";
			}

			if(isset($this->config["OrderBy"]))
			{
				$query .= " ORDER BY ".$this->config["OrderBy"];
			}
			
			$item[$this->name] = $stmt->FetchList($query);
			
			$script = '<script type="text/javascript">$(document).ready(function(){';
			$script .= '$("#'.$this->config["RequestField"].'").select2({allowClear: true});';
			$script .= "});</script>";
			$item[$this->name."ControlHTML"] = $script;
		}
		else
		{
			//text field
			$item[$this->name] = $request->GetProperty($this->config["RequestField"]);
		}
		
	}
}

?>