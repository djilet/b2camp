<?php 
require_once(dirname(__FILE__)."/../filter.php");

class ManagerFilter extends BaseFilter
{
	function AppendSQLCondition($request, &$join, &$where, &$having)
	{
		if(isset($this->config["Disabled"]) && $this->config["Disabled"])
		{
			return;
		}
		if($request->GetProperty($this->config["Name"]))
		{
			if(is_array($request->GetProperty($this->config["Name"])))
			{
				$request->SetProperty($this->config["Name"], array_filter($request->GetProperty($this->config["Name"])));
				if($request->GetProperty($this->config["Name"]))
					$where[] = "t.".$this->config["FromField"]." IN(".implode(", ", Connection::GetSQLArray($request->GetProperty($this->config["Name"]))).")";
			}
			else
			{
				$where[] = "t.".$this->config["FromField"]."=".$request->GetPropertyForSQL($this->config["Name"]);
			}
		}
	}
	
	function LoadFilterData($request, &$content)
	{
		parent::LoadFilterData($request, $content);
		$stmt = GetStatement();
		
		$where[] = 'InManagerStat = 1';
		
		$query = "SELECT ".$this->config["ToField"].", ".$this->config["ViewField"]." 
					FROM ".$this->config["Table"]. 
					(count($where) > 0 ? " WHERE ".implode(" AND ", $where) : "")." 
					ORDER BY ".$this->config["ViewField"]." ASC";
		if($data = $stmt->FetchList($query))
		{
			if($request->GetProperty($this->config["Name"]))
			{
				$selectedIDs = $request->GetProperty($this->config["Name"]);
				if(!is_array($selectedIDs))
				{
					$selectedIDs = array($selectedIDs);
				}
				for($i = 0; $i < count($data); $i++)
				{
					if(in_array($data[$i][$this->config["ToField"]], $selectedIDs))
						$data[$i]["Selected"] = 1;
				}
			}
			$content->SetLoop($this->config["ArrayName"], $data);
		}
		$script = '<script type="text/javascript">$(document).ready(function(){';
		$script .= '$("#'.$this->name.'").select2({placeholder:"",allowClear: true}).on("select2-open", function() {$(this).data("select2").results.addClass("overflow-hidden").perfectScrollbar();});';
        $script .= '$("#s2id_'.$this->config["StatusName"].'").removeClass("s2container");';
		$script .= "});</script>";
		$content->SetVar($this->name."ControlHTML", $script);
	}
}

?>