<?php

require_once(dirname(__FILE__)."/../init.php");

class Utilities
{
	public static function GetComponent($name, $file, $class, $config)
	{
		$fileName = dirname(__FILE__)."/".$file;
		if (!file_exists($fileName))
		{
			ErrorHandler::TriggerError("File \"".$fileName."\" doesn't exist", E_USER_WARNING);
			return false;
		}
		require_once($fileName);
		if (!class_exists($class))
		{
			ErrorHandler::TriggerError("Class \"".$class."\" is not found", E_USER_WARNING);
			return false;
		}
		
		eval("\$result = new ".$class."(\$name, \$config);");
		return $result;
	}
	
	public static function GetSelectPrefixForSQL($config, $configType)
	{
		$query = "SELECT t.".$config['ID']." AS EntityID";
		$fields = $config[$configType]['Fields'];
		for($i=0; $i<count($fields); $i++)
		{
			if($fields[$i]['Type'] == 'field' || $fields[$i]['Type'] == 'date' || $fields[$i]['Type'] == 'datetime' || $fields[$i]['Type']=="none")
			{
			    $table_prefix = isset($fields[$i]['TablePrefix']) ? $fields[$i]['TablePrefix'] : "t";
				$query .= ", ".$table_prefix.".".$fields[$i]['Name'];
			}
			else if($fields[$i]['Type'] == 'sql')
			{
				$query .= ", ".$fields[$i]['SQL']." AS ".$fields[$i]['Name'];
			}
			else if($fields[$i]['Type'] == 'component')
			{
				$component = Utilities::GetComponent($fields[$i]['Name'], $fields[$i]['File'], $fields[$i]['Class'], $fields[$i]['Config']);
				if($component)
				{
					$componentPrefix = (isset($fields[$i]['Config']['TablePrefix']))?$component->GetSelectPrefixForSQL($fields[$i]['Config']['TablePrefix']):$component->GetSelectPrefixForSQL();
					if($componentPrefix)
					{
						$query .= ", ".$componentPrefix;
					}
				}
			}
		}
		$query .= " FROM ".$config['Table']." t";

		if (isset($config['Join']))
		    $query.= " LEFT JOIN ".$config['Join'];

		return $query;
	}
	
	public static function GetUpdateFieldsForSQL($config, $item, $configType)
	{
		$results = array();
		$fields = $config[$configType]['Fields'];
		for($i=0; $i<count($fields); $i++)
		{
			if($fields[$i]['Type'] == 'field')
			{
				if(isset($item[$fields[$i]['Name']]))
					$results[] = $fields[$i]['Name']."=".Connection::GetSQLString($item[$fields[$i]['Name']]);
				else 
					$results[] = $fields[$i]['Name']."=NULL";
			}
			else if($fields[$i]['Type'] == 'date')
			{
				if(isset($item[$fields[$i]['Name']]))
					$results[] = $fields[$i]['Name']."=".Connection::GetSQLString(ToSQLDate($item[$fields[$i]['Name']]));
				else
					$results[] = $fields[$i]['Name']."=NULL";
			}
			else if($fields[$i]['Type'] == 'datetime')
			{
				if(isset($item[$fields[$i]['Name']]))
					$results[] = $fields[$i]['Name']."=".Connection::GetSQLString($item[$fields[$i]['Name']]);
				else
					$results[] = $fields[$i]['Name']."=NULL";
			}
			else if($fields[$i]['Type'] == 'generated')
			{
				if(isset($fields[$i]['Value']))
				{
					switch ($fields[$i]['Value'])
					{
						case 'current_datetime':
							$results[] = $fields[$i]['Name']."=".Connection::GetSQLString(date('Y-m-d H:i:s'));
							break;
						case 'current_date':
							$results[] = $fields[$i]['Name']."=".Connection::GetSQLString(date('Y-m-d'));
							break;
					}
				}
			}
			else if($fields[$i]['Type'] == 'component')
			{
				$component = Utilities::GetComponent($fields[$i]['Name'], $fields[$i]['File'], $fields[$i]['Class'], $fields[$i]['Config']);
				if($component)
				{
					$componentPrefix = $component->GetUpdatePrefixForSQL($item);
					if($componentPrefix)
					{
						$results[] = $componentPrefix;
					}
				}
			}
		}
		return implode(", ", $results);
	}
	
	public static function LoadFilterData($request, $config, &$content, $configType)
	{
		if(isset($config[$configType]['Filters']))
		{
			$filters = $config[$configType]['Filters'];
			for($i=0; $i<count($filters); $i++)
			{
				if(isset($filters[$i]['File']) && isset($filters[$i]['Class']))
				{
					$filter = Utilities::GetComponent($filters[$i]['Name'], $filters[$i]['File'], $filters[$i]['Class'], $filters[$i]['Config']);
					$filter->LoadFilterData($request, $content);
				}
				else
				{
					if($request->GetProperty($filters[$i]["Name"]))
						$content->SetVar($filters[$i]["Name"], $request->GetProperty($filters[$i]["Name"]));
				}
			}
		}
	}
	
	public static function GetFilterFieldNames($config, $configType)
	{
		$result = array('Page');
		if(isset($config[$configType]['Filters']))
		{
			foreach ($config[$configType]['Filters'] as $filter)
			{
				if(empty($filter['Class']))
				{
					$result[] = $filter['Name'];
				}
				else
				{
					$filterObject = Utilities::GetComponent($filter['Name'], $filter['File'], $filter['Class'], $filter['Config']);
					$result = array_merge($result, $filterObject->GetFilterFieldNames());
				}
			}
		}
		return $result;
	}	
	
	public static function GetAdminMenu()
	{
		global $entityConfig;
		
		$user = new User();
		$user->LoadBySession();
		$adminMenu = array();
		
		foreach ($entityConfig as $entity => $config)
		{
			if(!(isset($config["Hidden"]) && $config["Hidden"]) && (!isset($config["Access"]) || in_array($user->GetProperty("Role"), $config["Access"])))
			{
				$adminMenu[] = array(
					"Title" => GetTranslation("admin-menu-crm-".$entity),
					"Link" => "module.php?load=crm&entity=".$entity,
					"AdminMenuIcon" => isset($config["AdminMenuIcon"]) ? $config["AdminMenuIcon"] : "",
					"AdminMenuInfo" => isset($config["AdminMenuInfo"]) ? $config["AdminMenuInfo"] : "",
					"AdminMenuInfoClass" => isset($config["AdminMenuInfoClass"]) ? $config["AdminMenuInfoClass"] : "",
					"Submenu" => isset($config["AdminSubmenu"]) ? $config["AdminSubmenu"] : array()
				);
			}
		}

		return $adminMenu;
	}
	
	public static function ValidateAccess($config, $key = 'Access')
	{
		if(isset($config[$key]))
		{
			$user = new User();
			$user->ValidateAccess($config[$key]);
		}
	}
	
	public static function GenerateValidationError($type, $field, $path = array())
	{
		$string = GetTranslation("validation-error-".$type, "crm");
		$string .= " \"" . GetTranslation("validation-field-".$field, "crm") . "\"";
		
		foreach ($path as $chunk)
		{
			$string .= " " . GetTranslation("validation-entity-".$chunk["Entity"], "crm");
			if(isset($chunk["Index"]))
			{
				$string .= " #".$chunk["Index"];
			}
		}
		$descriptionKey = "validation-description-".$field;
		if(GetTranslation($descriptionKey) != $descriptionKey)
		{
			$string .= " " . GetTranslation($descriptionKey);
		}
		return $string;
	}
	
	public static function GetAutocompleteData($table, $field, $query)
	{
		static $availableData = array(
			"crm_parent" => array("LastName")
		);
		if(isset($availableData[$table]) && in_array($field, $availableData[$table]))
		{
			$stmt = GetStatement();
			$query = "SELECT DISTINCT(`".$field."`) FROM `".$table."` WHERE `".$field."` LIKE '%".Connection::GetSQLLike($query)."%' ORDER BY `".$field."` ASC";
			return $stmt->FetchList($query);
		}
		else
		{
			return array();
		}
	}
}

?>