<?php 
require_once(dirname(__FILE__)."/../filter.php");

class PhoneViewFilter extends BaseFilter
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
			$join[] = "LEFT JOIN ".$this->config["Table"]." ".$alias." ON t.".$this->config["FromField"]."=".$alias.".".$this->config["ToField"];
			
			$phone = str_replace(array("+7","-","(",")"), "", $request->GetProperty($this->config["Name"]));
			$code = str_replace("_", "", substr($phone, 0, 3));
			$number = str_replace("_", "", substr($phone, 3));
			$currentWhere = "(".$alias.".".$this->config["CodeField"]." LIKE '%".Connection::GetSQLLike($code)."%' AND ".
						$alias.".".$this->config["NumberField"]." LIKE '%".Connection::GetSQLLike($number)."%')";
			
			if(isset($this->config["AlternativePath"]) && $this->config["AlternativePath"])
			{
				foreach ($this->config["AlternativePath"] as $key => $chunk)
				{
					$alias = "l".count($join);
					$prevAlias = "l".(count($join) - 1);
					if($key == 0)
						$join[] = "LEFT JOIN ".$chunk["Table"]." ".$alias." ON t.".$chunk["FromField"]."=".$alias.".".$chunk["ToField"];
					else 
						$join[] = "LEFT JOIN ".$chunk["Table"]." ".$alias." ON ".$prevAlias.".".$chunk["FromField"]."=".$alias.".".$chunk["ToField"];
					if($key == count($this->config["AlternativePath"]) - 1)
					{
						$currentWhere = "(".$currentWhere;
						$currentWhere .= " OR (".$alias.".".$this->config["CodeField"]." LIKE '%".Connection::GetSQLLike($code)."%' AND ".
										$alias.".".$this->config["NumberField"]." LIKE '%".Connection::GetSQLLike($number)."%')";
						$currentWhere .= ")";
					}	
				}
			}
			$where[] = $currentWhere;
		}
	}
	
	function LoadFilterData($request, &$content)
	{
		if($request->ValidateNotEmpty($this->config["Name"]))
		{
			$content->SetVar($this->config["Name"], str_replace(array("+7","-","(",")"), "", $request->GetProperty($this->config["Name"])));
		}
	}
}

class PhoneUserFilter extends BaseFilter
{
    function AppendSQLCondition($request, &$join, &$where, &$having)
    {
        if(isset($this->config["Disabled"]) && $this->config["Disabled"])
        {
            return;
        }
        if($request->ValidateNotEmpty($this->config["Name"]))
        {
            $alias = $this->config['Table'];

            $phone_string = $request->GetProperty($this->config["Name"]);

            $search_string = "'".str_replace("_", "%", $phone_string)."'";

            $where[] = $alias.".".$this->config["Field"]." LIKE ".$search_string;
        }
    }

    function LoadFilterData($request, &$content)
    {
        if($request->ValidateNotEmpty($this->config["Name"]))
        {
            $content->SetVar($this->config["Name"], str_replace(array("+7","-","(",")"), "", $request->GetProperty($this->config["Name"])));
        }
    }
}

?>