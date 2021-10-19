<?php 
require_once(dirname(__FILE__)."/../filter.php");

class EmailFilter extends BaseFilter
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
			
			$email = $request->GetProperty($this->config["Name"]);
			$currentWhere = "(".$alias.".Email LIKE '%".Connection::GetSQLLike($email)."%')";
			
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
						$currentWhere .= " OR (".$alias.".Email LIKE '%".Connection::GetSQLLike($email)."%')";
						$currentWhere .= ")";
					}	
				}
			}
			$where[] = $currentWhere;
		}
	}
}

?>