<?php 
require_once(dirname(__FILE__)."/../component.php");

class CountViewComponent extends BaseComponent
{
	function PrepareBeforeShow(&$item, $user)
	{
		if(isset($item["EntityID"]))
		{
			$stmt = GetStatement();
			$query = "SELECT count(".$this->config["TargetField"].") FROM ".$this->config["Table"]." WHERE ".$this->config["KeyField"]."=".intval($item["EntityID"]);
			if(isset($this->config["Condition"]))
				$query .= " AND ".$this->config["Condition"]["Field"]."=".Connection::GetSQLString($this->config["Condition"]["Value"]);
			$item[$this->name] = $stmt->FetchField($query);
		}
	}
}

class ManagerViewComponent extends BaseComponent
{
	function GetUpdatePrefixForSQL($item)
	{
		return $this->config["FromField"]."=".Connection::GetSQLString($item[$this->name]);
	}
	
	function PrepareBeforeShow(&$item, $user)
	{
		$stmt = GetStatement();
		$where[] = 'InManagerStat = 1';
		
		$query = "SELECT ".$this->config["ToField"].", ".$this->config["ViewField"]." 
					FROM ".$this->config["Table"]." ". 
					(count($where) > 0 ? " WHERE ".implode(" AND ", $where) : "");
		
		if($data = $stmt->FetchList($query))
			$item[$this->name.'ValueList'] = $data; 
	}
}

class ManagerEditSecondComponent extends ManagerViewComponent
{
	function GetUpdatePrefixForSQL($item)
	{
		return $this->config["FromField"]."=".Connection::GetSQLString($item[$this->name]);
	}
	
	function PrepareBeforeShow(&$item, $user)
	{
		$stmt = GetStatement();
		$where[] = 'InManagerStat = 1';
		
		if (isset($item['EntityID']))
		{
			$query = "SELECT * FROM crm_bookkeeping Where BookkeepingID=".$item["EntityID"];
			$item_tmp = $stmt->FetchRow($query);
			
			$query = "SELECT ".$this->config["ToField"].", ".$this->config["ViewField"]."
				, IF(UserID=".$item_tmp[$this->config["FromField"]].", 1, 0) AS Selected
					FROM ".$this->config["Table"]." ".
						(count($where) > 0 ? " WHERE ".implode(" AND ", $where) : "");
		}
		else
		{
			$query = "SELECT ".$this->config["ToField"].", ".$this->config["ViewField"]."
					FROM ".$this->config["Table"]." ".
								(count($where) > 0 ? " WHERE ".implode(" AND ", $where) : "");
		}
			
		if($data = $stmt->FetchList($query))
			$item[$this->name.'ValueList'] = $data;
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

class LinkedViewComponent extends BaseComponent
{
	function GetSelectPrefixForSQL()
	{
		return "t.".$this->config["FromField"];
	}
	
	function PrepareBeforeShow(&$item, $user)
	{
		if(isset($item[$this->config["FromField"]]) && $item[$this->config["FromField"]] > 0)
		{
			$stmt = GetStatement();
			$query = "SELECT ";
			if(isset($this->config["ViewSQL"]))
				$query .= $this->config["ViewSQL"];
			else 
				$query .= $this->config["ViewField"];
			$query .= " FROM ".$this->config["Table"]." WHERE ".$this->config["ToField"]."=".intval($item[$this->config["FromField"]]);
			
			$item[$this->name.$this->config["ViewField"]] = $stmt->FetchField($query);
		}
	}
}

class ExternalLinkedMorphViewComponent extends BaseComponent
{
	function PrepareBeforeShow(&$item, $user)
	{
		if(isset($GLOBALS['entityConfig'][$item[$this->config["EntityTypeField"]]]) && (!isset($this->config["DisabledEntities"]) || !in_array($item[$this->config["EntityTypeField"]], $this->config["DisabledEntities"])))
		{
			$stmt = GetStatement();
			$query = "SELECT ";
			if(isset($this->config["ViewSQL"]))
				$query .= $this->config["ViewSQL"];
			else 
				$query .= $this->config["ViewField"];
			$query .= " FROM ".$this->config["TargetTable"]." AS t 
					LEFT JOIN ".$GLOBALS['entityConfig'][$item[$this->config["EntityTypeField"]]]['Table']." AS l ON t.".$this->config["FromField"]."=l.".$this->config["ToField"]." 
					WHERE l.".$GLOBALS['entityConfig'][$item[$this->config["EntityTypeField"]]]['ID']."=".intval($item[$this->config["EntityIDField"]]);
			$item[$this->name.$this->config["ViewField"]] = $stmt->FetchField($query);
		}
	}
}

class LinkedEditComponent extends LinkedViewComponent
{
	function GetUpdatePrefixForSQL($item)
	{
	    if (isset($item[$this->name]))
		return $this->config["FromField"]."=".Connection::GetSQLString($item[$this->name]);
	    else
	        return false;
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
		
		$where = array();
		if(isset($this->config["IncludeKeys"]) && count($this->config["IncludeKeys"]))
		{
			$where[] = $this->config["ToField"]." IN(".implode(", ", Connection::GetSQLArray($this->config["IncludeKeys"])).")";
		}
		if(isset($this->config["ExcludeKeys"]) && count($this->config["ExcludeKeys"]))
		{
			$where[] = $this->config["ToField"]." NOT IN(".implode(", ", Connection::GetSQLArray($this->config["ExcludeKeys"])).")";
		}
		if(isset($this->config['Condition']))
        {
            $where[] = $this->config['Condition'];
        }
		if(count($where) > 0)
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}
		$item[$this->name."ValueList"] = $stmt->FetchList($query);
		
		$script = '<script type="text/javascript">$(document).ready(function(){';
		$script .= '$("#'.$this->name.'").select2({placeholder:"",allowClear: true}).on("select2-open", function() {$(this).data("select2").results.addClass("overflow-hidden").perfectScrollbar();});';
        $script .= '$("#s2id_'.$this->name.'").removeClass("s2container");';
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

class UserLinkedViewComponent extends BaseComponent
{
	function GetSelectPrefixForSQL()
	{
		$user = new User();
		$user->LoadBySession();
		if(is_array($this->config["Field"]))
		{
			$fieldArr = array();
			foreach ($this->config["Field"] as $field)
				$fieldArr[] = "t.".$field."=".$user->GetPropertyForSQL("UserID");
			return "IF(".implode(" OR ", $fieldArr).", 1, 0) AS ".$this->name;
		}
		else
		{
			return "IF(t.".$this->config["Field"]."=".$user->GetPropertyForSQL("UserID").", 1, 0) AS ".$this->name;	
		}
	}
}

class LinkedMultipleSelectViewComponent extends BaseComponent
{
	function PrepareBeforeShow(&$item, $user)
	{
		$stmt = GetStatement();
		$query = "SELECT t.".$this->config["KeyField"].",".$this->config["ViewSQL"]." FROM ".$this->config["Table"]." t LEFT JOIN ".$this->config["LinkTable"]." l ON t.".$this->config["KeyField"]."=l.".$this->config["LinkToField"]." WHERE l.".$this->config["LinkFromField"]."=".$item["EntityID"];
		
		if(isset($this->config["Symmetric"]) && $this->config["Symmetric"])
		{
			$query = "(".$query.") UNION (SELECT t.".$this->config["KeyField"].",".$this->config["ViewSQL"]." FROM ".$this->config["Table"]." t LEFT JOIN ".$this->config["LinkTable"]." l ON t.".$this->config["KeyField"]."=l.".$this->config["LinkFromField"]." WHERE l.".$this->config["LinkToField"]."=".$item["EntityID"].")";
		}
		$item[$this->name."List"] = $stmt->FetchList($query);
	}
}

class LinkedMultipleSelectEditComponent extends BaseComponent
{
	function PrepareBeforeShow(&$item, $user)
	{
	    $join = isset($this->config['Join'])? " JOIN ".$this->config['Join'] : "";

		$stmt = GetStatement();
		$query = "SELECT DISTINCT t.".$this->config["KeyField"].",".$this->config["ViewSQL"]." FROM ".$this->config["Table"]." t".$join;
		$where = array();
		
		if (isset($this->config["WhereSql"]) && $this->config["WhereSql"])
		{
            if (!isset($this->config["NotSeason"]))
                $query .= ' left join  '.$this->config["LinkTable"].' season on season.'.$this->config["LinkToField"].' = t.'.$this->config["KeyField"];
			$where[] = $this->config["WhereSql"];
            if (isset($item["EntityID"]) && $item["EntityID"] && !isset($this->config["NotSeason"])){
                $where[] = " season.".$this->config["LinkFromField"]."=".$item["EntityID"];
            }
		}

        if ($where){
            if ($this->config["Concatenation"])
                $query.= " WHERE ".implode(" and ", $where);
            else
		        $query.= " WHERE ".implode(" || ", $where);
		}
        if (isset($this->config["OrderSQL"]))
            $query .= " ORDER BY ".$this->config["OrderSQL"];
		$possibleValues = $stmt->FetchList($query);
		
		if(isset($item["EntityID"]))
		{
			$query = "SELECT ".$this->config["LinkToField"]." FROM ".$this->config["LinkTable"]." WHERE ".$this->config["LinkFromField"]."=".$item["EntityID"];
			$selectedValues = $stmt->FetchList($query);
			$selectedValuesArr = array();
			for($i=0; $i<count($selectedValues); $i++)
			{
				$selectedValuesArr[] = $selectedValues[$i][$this->config["LinkToField"]];
			}
			if(isset($this->config["Symmetric"]) && $this->config["Symmetric"])
			{
				$query = "SELECT ".$this->config["LinkFromField"]." FROM ".$this->config["LinkTable"]." WHERE ".$this->config["LinkToField"]."=".$item["EntityID"];
				$selectedValues = $stmt->FetchList($query);
				for($i=0; $i<count($selectedValues); $i++)
				{
					$selectedValuesArr[] = $selectedValues[$i][$this->config["LinkFromField"]];
				}
			}
			for($i=0; $i<count($possibleValues); $i++)
			{
				if(in_array($possibleValues[$i][$this->config["KeyField"]],$selectedValuesArr))
				{
					$possibleValues[$i]["Selected"] = 1;
				}
			}
		}
		$item[$this->name."ValueList"] = $possibleValues;

		$minInput="";
		if (isset($this->config["MinInput"]))
            $minInput=", minimumInputLength: ".$this->config["MinInput"];

		$script = '<script type="text/javascript">$(document).ready(function(){';
		$script .= '$("#'.$this->name.'").select2({placeholder:"",allowClear: true'.$minInput.'}).on("select2-open", function() {$(this).data("select2").results.addClass("overflow-hidden").perfectScrollbar();});';
        $script .= '$("#s2id_'.$this->name.'").removeClass("s2container");';
		$script .= "});</script>";
		$item[$this->name."ControlHTML"] = $script;
	}

	function PrepareAfterSave(&$item, $user)
	{
        if(isset($item[$this->name]))
        {
		$stmt = GetStatement();
		$stmt->Execute("DELETE FROM ".$this->config["LinkTable"]." WHERE ".$this->config["LinkFromField"]."=".intval($item["EntityID"]));
		if(isset($this->config["Symmetric"]) && $this->config["Symmetric"])
		{
			$stmt->Execute("DELETE FROM ".$this->config["LinkTable"]." WHERE ".$this->config["LinkToField"]."=".intval($item["EntityID"]));	
		}

			for($i=0; $i<count($item[$this->name]); $i++)
			{
				$stmt->Execute("INSERT INTO ".$this->config["LinkTable"]."(".$this->config["LinkFromField"].",".$this->config["LinkToField"].") VALUES(".intval($item["EntityID"]).",".intval($item[$this->name][$i]).")");
			}
		}
	}
	
	function Validate(&$item)
	{
		if(isset($this->config["Required"]) && $this->config["Required"])
		{
			if(is_array($item->GetProperty($this->name)))
			{
				foreach ($item->Getproperty($this->name) as $value)
				{
					if(intval($value) != $value || !$value > 0)
						$item->AddValidateError('select', $this->name);
				}
			}
			else
			{
				if(!$item->ValidateInt($this->name) || !$item->GetIntProperty($this->name) > 0)
					$item->AddValidateError('select', $this->name);	
			}
		}
	}
}

class EditItemSelectComponent extends BaseComponent {
    function PrepareBeforeShow(&$item, $user)
    {
        $join = isset($this->config['Join'])? " JOIN ".$this->config['Join'] : "";

        $stmt = GetStatement();
        $query = "SELECT DISTINCT t.".$this->config["KeyField"].",".$this->config["ViewSQL"]." FROM ".$this->config["Table"]." t".$join;
        $where = array();

        if (isset($this->config["WhereSql"]) && $this->config["WhereSql"])
        {
            $query .= ' left join  '.$this->config["LinkTable"].' season on season.'.$this->config["LinkToField"].' = t.'.$this->config["KeyField"];
            $where[] = $this->config["WhereSql"];
            if (isset($item["EntityID"]) && $item["EntityID"]){
                $where[] = " season.".$this->config["LinkFromField"]."=".$item["EntityID"];
            }
        }

        if ($where){
            $query.= " WHERE ".implode(" || ", $where);
        }
        if (isset($this->config["OrderSQL"]))
            $query .= " ORDER BY ".$this->config["OrderSQL"];
        $possibleValues = $stmt->FetchList($query);

        if(isset($item["EntityID"]))
        {
            $query = "SELECT ".$this->config["LinkToField"]." FROM ".$this->config["LinkTable"]." WHERE ".$this->config["LinkFromField"]."=".$item["EntityID"];
            $selectedValues = $stmt->FetchList($query);
            $selectedValuesArr = array();
            for($i=0; $i<count($selectedValues); $i++)
            {
                $selectedValuesArr[] = $selectedValues[$i][$this->config["LinkToField"]];
            }
            if(isset($this->config["Symmetric"]) && $this->config["Symmetric"])
            {
                $query = "SELECT ".$this->config["LinkFromField"]." FROM ".$this->config["LinkTable"]." WHERE ".$this->config["LinkToField"]."=".$item["EntityID"];
                $selectedValues = $stmt->FetchList($query);
                for($i=0; $i<count($selectedValues); $i++)
                {
                    $selectedValuesArr[] = $selectedValues[$i][$this->config["LinkFromField"]];
                }
            }
            for($i=0; $i<count($possibleValues); $i++)
            {
                if(in_array($possibleValues[$i][$this->config["KeyField"]],$selectedValuesArr))
                {
                    $possibleValues[$i]["Selected"] = 1;
                }
            }
        }
        $item[$this->name."ValueList"] = $possibleValues;

        $script = '<script type="text/javascript">$(document).ready(function(){';
        $script .= 'Init'.$this->name.'Control($("#'.$this->name.'"));';
        for($i=0; $i<count($selectedValuesArr); $i++)
        {
            $script .= 'Add'.$this->name.'Row($("#'.$this->name.'"),"'.$selectedValuesArr[$i].'");';
        }
        $script .= "});</script>";
        $item[$this->name."ControlHTML"] = $script;
    }

    function PrepareAfterSave(&$item, $user)
    {
        $stmt = GetStatement();
        $stmt->Execute("DELETE FROM ".$this->config["LinkTable"]." WHERE ".$this->config["LinkFromField"]."=".intval($item["EntityID"]));
        for($i=0; $i<100; $i++)
        {
            if(isset($item[$this->name.$i."Type"]))
            {
                $stmt->Execute("INSERT INTO ".$this->config["LinkTable"]."(".$this->config["LinkFromField"].",".$this->config["LinkToField"].") VALUES(".intval($item["EntityID"]).",".intval($item[$this->name.$i."Type"]).")");
            }
        }
    }

    function Validate(&$item)
    {
        if(isset($this->config["Required"]) && $this->config["Required"])
        {
            if(is_array($item->GetProperty($this->name)))
            {
                foreach ($item->Getproperty($this->name) as $value)
                {
                    if(intval($value) != $value || !$value > 0)
                        $item->AddValidateError('select', $this->name);
                }
            }
            else
            {
                if(!$item->ValidateInt($this->name) || !$item->GetIntProperty($this->name) > 0)
                    $item->AddValidateError('select', $this->name);
            }
        }
    }
}

?>