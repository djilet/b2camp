<?php

require_once(dirname(__FILE__)."/../init.php");
require_once(dirname(__FILE__)."/utilities.php");
es_include("localobjectlist.php");

class ItemList extends LocalObjectList
{
	var $module;
	var $entity;
	var $config;
	
	function ItemList($module, $entity, $config = array(), $data = array())
	{
		parent::LocalObjectList($data);
		$this->module = $module;
		$this->entity = $entity;
		$this->config = $config;
	}
	
	function Load(LocalObject $request, $user)
	{
		if ($request->GetProperty('FullList'))
			$this->SetItemsOnPage(0);
		else
			$this->SetItemsOnPage($this->config['ItemsPerPage']);

    $join = array();
    $where = array();
    $having = array();

    //filters support v2
    if(isset($this->config['ListConfig']['Filters']))
    {
    for($i = 0; $i < count($this->config['ListConfig']['Filters']); $i++)
    {
    // component
    if(isset($this->config['ListConfig']['Filters'][$i]["File"]) && isset($this->config['ListConfig']['Filters'][$i]["Class"]))
    {
        $filter = Utilities::GetComponent(
            $this->config['ListConfig']['Filters'][$i]["Name"],
            $this->config['ListConfig']['Filters'][$i]["File"],
            $this->config['ListConfig']['Filters'][$i]["Class"],
            $this->config['ListConfig']['Filters'][$i]["Config"]
        );
        $filter->AppendSQLCondition($request, $join, $where, $having);
    }
    // field
    else
    {
        if(isset($this->config['ListConfig']['Filters'][$i]["Value"]) && strlen($this->config['ListConfig']['Filters'][$i]["Value"]))
        {
            $currentWhere =
                "t.".$this->config['ListConfig']['Filters'][$i]['Field']." "
                .$this->config['ListConfig']['Filters'][$i]["Operation"]." '".$this->config['ListConfig']['Filters'][$i]["Value"]."'";
            $where[] = $currentWhere;
        }
        else if($request->ValidateNotEmpty($this->config['ListConfig']['Filters'][$i]["Name"]))
        {
            $table_prefix =
                isset($this->config['ListConfig']['Filters'][$i]['TablePrefix']) ?
                    $this->config['ListConfig']['Filters'][$i]['TablePrefix'] : "t";

						$currentWhere = $table_prefix.".".$this->config['ListConfig']['Filters'][$i]['Field']." LIKE '%".Connection::GetSQLLike($request->GetProperty($this->config['ListConfig']['Filters'][$i]["Name"]))."%'";
						
						if(isset($this->config['ListConfig']['Filters'][$i]["AlternativePath"]) && $this->config['ListConfig']['Filters'][$i]["AlternativePath"])
						{
							foreach ($this->config['ListConfig']['Filters'][$i]["AlternativePath"] as $key => $chunk)
							{
								$alias = "l".count($join);
								$prevAlias = "l".(count($join) - 1);
								if($key == 0)
									$join[] = "LEFT JOIN ".$chunk["Table"]." ".$alias." ON t.".$chunk["FromField"]."=".$alias.".".$chunk["ToField"];
								else 
									$join[] = "LEFT JOIN ".$chunk["Table"]." ".$alias." ON ".$prevAlias.".".$chunk["FromField"]."=".$alias.".".$chunk["ToField"];
								if($key == count($this->config['ListConfig']['Filters'][$i]["AlternativePath"]) - 1)
								{
									$currentWhere = "(".$currentWhere;
									$currentWhere .= " OR ".$alias.".".$this->config['ListConfig']['Filters'][$i]['Field']." LIKE '%".Connection::GetSQLLike($request->GetProperty($this->config['ListConfig']['Filters'][$i]["Name"]))."%'";
									$currentWhere .= ")";
								}
							}
						}
						$where[] = $currentWhere;
					}	
				}
			}
		}
		
		$query = Utilities::GetSelectPrefixForSQL($this->config, "ListConfig");
		$query .= (count($join) > 0 ? " ".implode(" ", $join) : "");
		$query .= (count($where) > 0 ? " WHERE ".implode(" AND ", $where) : "");
		$query .= " GROUP BY t.".$this->config['ID'];
		$query .= (count($having) > 0 ? " HAVING ".implode(" AND ", $having) : "");
		
		if($this->config['ItemsOrderBy'])
		{
			$query .= " ORDER BY ".$this->config['ItemsOrderBy'];
		}

		$this->SetCurrentPage();
		$this->LoadFromSQL($query);
		$this->PrepareBeforeShow($user);
	}

	function Remove($request)
	{
		$stmt = GetStatement();
		$ids = $request->GetProperty("EntityIDs");
		if (is_array($ids) && count($ids) > 0)
		{
            if (isset($this->config['LinkTable'])){
                $query = "DELETE FROM ".$this->config['LinkTable']." WHERE ".$this->config['LinkField']." IN (SELECT ".$this->config['LinkField']." FROM ".$this->config['Table']." WHERE ".$this->config['ID']." IN (".implode(",", Connection::GetSQLArray($ids))."))";
                $stmt->Execute($query);
            }
			$query = "DELETE FROM ".$this->config['Table']." WHERE ".$this->config['ID']." IN (".implode(",", Connection::GetSQLArray($ids)).")";
			$stmt->Execute($query);

			if (count($ids) > 1)
				$key = "entity-are-removed";
			else
				$key = "entity-is-removed";

			$this->AddMessage($key, $this->module);
		}
	}
	
	private function PrepareBeforeShow($user)
	{
		$fields = $this->config["ListConfig"]['Fields'];
		for($i=0; $i<count($fields); $i++)
		{
			if($fields[$i]['Type'] == 'component')
			{
				$component = Utilities::GetComponent($fields[$i]['Name'], $fields[$i]['File'], $fields[$i]['Class'], $fields[$i]['Config']);
				if($component)
				{
					for ($j = 0; $j < count($this->_items); $j++)
					{
						$component->PrepareBeforeShow($this->_items[$j], $user);

						if (isset($this->_items[$j]['Status'])) {
							$this->_items[$j]['StatusTitle'] = GetTranslation('status-value-'.$this->_items[$j]['Status'], $this->module);
							$this->_items[$j]['Canceled'] = ($this->_items[$j]['Status'] == 'cancel' ? 1 : 0);
						}
					}
				}
			}
		}
	}

	public function Cancel(LocalObject $request, User $user)
	{
		if ('crm_bookkeeping' != $this->config['Table']) {
			return;
		}

		$stmt = GetStatement();
		$ids = $request->GetProperty("EntityIDs");
		if (is_array($ids) && count($ids) > 0)
		{
			$query = "UPDATE ".$this->config['Table']." SET 
			    `Status` = 'cancel',
			    `StatusUserID` = ".$user->GetIntProperty('UserID').",
			    `StatusUpdateDatetime` = Now() 
			    WHERE ".$this->config['ID']." IN (".implode(",", Connection::GetSQLArray($ids)).")";
			$stmt->Execute($query);

			if (count($ids) > 1)
				$key = "entity-are-canceled";
			else
				$key = "entity-is-canceled";

			$this->AddMessage($key, $this->module);
		}
	}
}

?>