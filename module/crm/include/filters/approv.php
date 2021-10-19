<?php
require_once(dirname(__FILE__)."/../filter.php");
class ApprovFilter extends BaseFilter{

	function LoadFilterData($request, &$content)
	{
        $content->SetVar('FilteronSending', $request->GetProperty('FilteronSending'));
        $content->SetVar('FilteronSMS', $request->GetProperty('FilteronSMS'));
        $content->SetVar('FilteronPhoto', $request->GetProperty('FilteronPhoto'));
	}

    function AppendSQLCondition($request, &$join, &$where, &$having){
        if($request->GetProperty($this->config["Name1"])){
            $alias = "l".count($join);
            $join[] = "LEFT JOIN ".$this->config["Table"]." ".$alias." ON t.".$this->config["FromField"]."=".$alias.".".$this->config["ToField"];
            if ($request->GetProperty($this->config["Name1"]) == 'Y'){
                $where[] = $alias.".".$this->config['Where1']."=".$request->GetPropertyForSQL($this->config["Name1"]);
            } elseif($request->GetProperty($this->config["Name1"]) == 'N') {
                $where[] = "(".$alias.".".$this->config['Where1']."=".$request->GetPropertyForSQL($this->config["Name1"])
                    ." OR ".$alias.".".$this->config['Where1']." IS NULL)";
            }
        }
        if($request->GetProperty($this->config["Name2"])){
            $alias = "l".count($join);
            $join[] = "LEFT JOIN ".$this->config["Table"]." ".$alias." ON t.".$this->config["FromField"]."=".$alias.".".$this->config["ToField"];
            if ($request->GetProperty($this->config["Name2"]) == 'Y'){
                $where[] = $alias.".".$this->config['Where2']."=".$request->GetPropertyForSQL($this->config["Name2"]);
            } elseif($request->GetProperty($this->config["Name2"]) == 'N') {
                $where[] = "(".$alias.".".$this->config['Where2']."=".$request->GetPropertyForSQL($this->config["Name2"])
                    ." OR ".$alias.".".$this->config['Where2']." IS NULL)";
            }
        }
        if($request->GetProperty($this->config["Name3"])){
            $alias = "l".count($join);
            $join[] = "LEFT JOIN ".$this->config["Table"]." ".$alias." ON t.".$this->config["FromField"]."=".$alias.".".$this->config["ToField"];
            if ($request->GetProperty($this->config["Name3"]) == 'Y'){
                $where[] = $alias.".".$this->config['Where3']."=".$request->GetPropertyForSQL($this->config["Name3"]);
            } elseif($request->GetProperty($this->config["Name3"]) == 'N') {
                $where[] = "(".$alias.".".$this->config['Where3']."=".$request->GetPropertyForSQL($this->config["Name3"])
                    ." OR ".$alias.".".$this->config['Where3']." IS NULL)";
            }
        }
    }
}
?>