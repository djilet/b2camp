<?php 
require_once(dirname(__FILE__)."/../filter.php");

class StatusFilter extends BaseFilter
{
	function AppendSQLCondition($request, &$join, &$where, &$having)
	{
		if(isset($this->config["Disabled"]) && $this->config["Disabled"])
		{
			return;
		}
		if($request->GetProperty($this->config["StatusName"]))
		{
			$statusAlias = "l".(count($join));
			$join[] = "LEFT JOIN `".$this->config["StatusTable"]."` AS ".$statusAlias." ON ".$statusAlias.".".$this->config["Key"]."=t.".$this->config["Key"];
			$where[] = $statusAlias.".StatusID=".$request->GetPropertyForSQL($this->config["StatusName"]);
		}
		$seasonName = $request->GetProperty($this->config["SeasonName"]);
		if($seasonName)
		{
			if($request->GetProperty($this->config["StatusName"]))
			{
			    $alias = $statusAlias;
			}
			else
			{
				$seasonAlias = "l".(count($join));
				$join[] = "LEFT JOIN `".$this->config["EntitySeasonTable"]."` AS ".$seasonAlias." ON ".$seasonAlias.".".$this->config["Key"]."=t.".$this->config["Key"];
				$alias = $seasonAlias;
			}

			if (gettype($seasonName)=="array")
                $where[] = $alias.".SeasonID IN (".implode(",",Connection::GetSQLArray($seasonName)).")";
            else
                $where[] = $alias.".SeasonID=".Connection::GetSQLString($seasonName);
		}
	}
	
	function LoadFilterData($request, &$content)
	{
		//parent::LoadFilterData($request, $content);
		$stmt = GetStatement();
		
		$query = "SELECT StatusID, Title FROM `crm_status` ORDER BY Title ASC";
		if($statusList = $stmt->FetchList($query))
		{
			if($request->GetProperty($this->config["StatusName"]))
			{
				$selectedIDs = $request->GetProperty($this->config["StatusName"]);
				if(!is_array($selectedIDs))
				{
					$selectedIDs = array($selectedIDs);
				}
				for($i = 0; $i < count($statusList); $i++)
				{
					if(in_array($statusList[$i]["StatusID"], $selectedIDs))
						$statusList[$i]["Selected"] = 1;
				}
			}
			$content->SetLoop($this->config["StatusArrayName"], $statusList);
		}
		$script = '<script type="text/javascript">$(document).ready(function(){';
		$script .= '$("#'.$this->config["StatusName"].'").select2({placeholder:"",allowClear: true}).on("select2-open", function() {$(this).data("select2").results.addClass("overflow-hidden").perfectScrollbar();});';
        $script .= '$("#s2id_'.$this->config["StatusName"].'").removeClass("s2container");';
		$script .= "});</script>";
		$content->SetVar($this->config["StatusName"]."ControlHTML", $script);
		
		$query = "SELECT SeasonID, Title, Archive, TypeID FROM `crm_season` ORDER BY FIELD(Archive, 'N', 'Y') ASC, TypeID ASC, Title ASC";
		if($seasonList = $stmt->FetchList($query))
		{
			if($request->GetProperty($this->config["SeasonName"]))
			{
				$selectedIDs = $request->GetProperty($this->config["SeasonName"]);
				if(!is_array($selectedIDs))
				{
					$selectedIDs = array($selectedIDs);
				}
				for($i = 0; $i < count($seasonList); $i++)
				{
					if(in_array($seasonList[$i]["SeasonID"], $selectedIDs))
						$seasonList[$i]["Selected"] = 1;
				}
			}
			$content->SetLoop($this->config["SeasonArrayName"], $seasonList);
		}
		$script = '<script type="text/javascript">$(document).ready(function(){';
		$script .= '$("#'.$this->config["SeasonName"].'").select2({placeholder:"",allowClear: true}).on("select2-open", function() {$(this).data("select2").results.addClass("overflow-hidden").perfectScrollbar();});';
        $script .= '$("#s2id_'.$this->config["SeasonName"].'").removeClass("s2container");';
		$script .= "});</script>";
		$content->SetVar($this->config["SeasonName"]."ControlHTML", $script);
	}

	function GetFilterFieldNames()
	{
		return array($this->config["StatusName"], $this->config["SeasonName"]);
	}
}