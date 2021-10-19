<?php 
require_once(dirname(__FILE__)."/../filter.php");

class CountFilter extends BaseFilter
{
	function AppendSQLCondition($request, &$join, &$where, &$having)
	{
		if(isset($this->config["Disabled"]) && $this->config["Disabled"])
		{
			return;
		}
		if($request->ValidateNotEmpty($this->config["Name"]))
		{
			$alias = "l".count($join);
			$join[] = "LEFT JOIN ".$this->config["LinkTable"]." ".$alias." ON t.".$this->config["FromField"]."=".$alias.".".$this->config["ToField"];
			$having[] = "COUNT(".$alias.".".$this->config["ToField"].") ".$this->config["Operation"]." ".$request->GetIntProperty($this->config["Name"]);	
		}
	}
}

class LinkedFilter extends BaseFilter
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
		
		$where = array();
		if(isset($this->config["IncludeKeys"]) && count($this->config["IncludeKeys"]))
		{
			$where[] = $this->config["ToField"]." IN(".implode(", ", Connection::GetSQLArray($this->config["IncludeKeys"])).")";
		}
		if(isset($this->config["ExcludeKeys"]) && count($this->config["ExcludeKeys"]))
		{
			$where[] = $this->config["ToField"]." NOT IN(".implode(", ", Connection::GetSQLArray($this->config["ExcludeKeys"])).")";
		}
		
		$table_prefix = isset($this->config['TablePrefix'])?$this->config['TablePrefix'].".":"";
		$as = isset($this->config['ToFieldAlias'])?" AS ".$this->config['ToFieldAlias']: "";
		$keyfield = $as? $this->config['ToFieldAlias']:$this->config['ToField'];
		
		$query = "SELECT ".$table_prefix.$this->config["ToField"].$as.", ".$this->config["ViewField"]." 
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
					if(in_array($data[$i][$keyfield], $selectedIDs))
						$data[$i]["Selected"] = 1;
				}
			}
			$content->SetLoop($this->config["ArrayName"], $data);
		}
		$script = '<script type="text/javascript">$(document).ready(function(){';
		$script .= '$("#'.$this->name.'").select2({placeholder:"",allowClear: true}).on("select2-open", function() {$(this).data("select2").results.addClass("overflow-hidden").perfectScrollbar();});';
        $script .= '$("#s2id_'.$this->name.'").removeClass("s2container");';
		$script .= "});</script>";
		$content->SetVar($this->name."ControlHTML", $script);
	}

}

class LinkedMultipleSelectFilter extends LinkedFilter
{
	function AppendSQLCondition($request, &$join, &$where, &$having)
	{
		if(isset($this->config["Disabled"]) && $this->config["Disabled"])
		{
			return;
		}
		if($request->GetProperty($this->config["Name"]))
		{
			$alias = "l".count($join);
			if(is_array($request->GetProperty($this->config["Name"])))
			{
				$request->SetProperty($this->config["Name"], array_filter($request->GetProperty($this->config["Name"])));
			}
			else
			{
				$request->SetProperty($this->config["Name"], array($request->GetProperty($this->config["Name"])));
			}
			if($request->GetProperty($this->config["Name"]))
			{
				if(isset($this->config["Symmetric"]))
				{
					$join[] = "LEFT JOIN ".$this->config["LinkTable"]." ".$alias." ON t.".$this->config["FromField"]."=".$alias.".".$this->config["LinkFromField"];
					$alias2 = "l".(count($join)+1);
					$join[] = "LEFT JOIN ".$this->config["LinkTable"]." ".$alias2." ON t.".$this->config["FromField"]."=".$alias2.".".$this->config["LinkToField"];
					$where[] = "(".$alias.".".$this->config["LinkToField"]." IN(".implode(", ", Connection::GetSQLArray($request->GetProperty($this->config["Name"]))).") OR 
							".$alias2.".".$this->config["LinkFromField"]." IN(".implode(", ", Connection::GetSQLArray($request->GetProperty($this->config["Name"])))."))";
				}
				else
				{
					$join[] = "LEFT JOIN ".$this->config["LinkTable"]." ".$alias." ON t.".$this->config["FromField"]."=".$alias.".".$this->config["LinkFromField"];
					$where[] = $alias.".".$this->config["LinkToField"]." IN(".implode(", ", Connection::GetSQLArray($request->GetProperty($this->config["Name"]))).")";	
				}
			}
		}
	}
	
	function LoadFilterData($request, &$content)
	{
		parent::LoadFilterData($request, $content);
		/*$stmt = GetStatement();
		$query = "SELECT ".$this->config["ToField"].", ".$this->config["ViewField"]." 
					FROM ".$this->config["Table"]." 
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
		$script .= "});</script>";
		$content->SetVar($this->name."ControlHTML", $script);*/
	}
}

class CustomLinkedFilter extends LinkedFilter
{
	function AppendSQLCondition($request, &$join, &$where, &$having)
	{
		if(isset($this->config["Disabled"]) && $this->config["Disabled"])
		{
			return;
		}
		if($request->GetProperty($this->config["Name"]))
		{
			if(isset($this->config["Type"]) && $this->config["Type"] == "date")
				$request->SetProperty($this->config["Name"], ToSQLDate($request->GetProperty($this->config["Name"])));
			foreach ($this->config["Path"] as $key => $chunk)
			{
				$alias = "l".count($join);
				$prevAlias = "l".(count($join) - 1);
				if($key == 0)
					$join[] = "LEFT JOIN ".$chunk["Table"]." ".$alias." ON t.".$chunk["FromField"]."=".$alias.".".$chunk["ToField"];
				else 
					$join[] = "LEFT JOIN ".$chunk["Table"]." ".$alias." ON ".$prevAlias.".".$chunk["FromField"]."=".$alias.".".$chunk["ToField"];
				if($key == count($this->config["Path"]) - 1)
				{
					if(isset($chunk["SQLTemplate"]))
						$field = str_replace("#Field#", $alias.".".$chunk["Field"], $chunk["SQLTemplate"]);
					else 
						$field = $alias.".".$chunk["Field"];
					if(isset($chunk["OperationTemplate"]))
						$where[] = $field.str_replace("#Value#", $request->GetPropertyForSQL($this->config["Name"]), $chunk["OperationTemplate"]);
					else 
						$where[] = $field.$chunk["Operation"].$request->GetPropertyForSQL($this->config["Name"]);
				}	
			}
		}
	}
	
	function LoadFilterData($request, &$content)
	{
		if(isset($this->config["Table"]) && isset($this->config["ToField"]) && isset($this->config["ViewField"]) && isset($this->config["ArrayName"]))
		{
			LinkedFilter::LoadFilterData($request, $content);
		}
		else
		{
			BaseFilter::LoadFilterData($request, $content);
		}
	}
}

?>