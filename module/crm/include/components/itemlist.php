<?php 
require_once(dirname(__FILE__)."/../component.php");
require_once(dirname(__FILE__)."/../item_list.php");

class ItemListViewComponent extends BaseComponent
{
	function PrepareBeforeShow(&$item, $user)
	{
		$itemList = new ItemList(null, null, $this->config);
		$params = new LocalObject();
		$params->SetProperty("FullList", 1);	
// 		$params->SetProperty("Archive", 'N');
		$params->SetProperty("EntityID", isset($item["EntityID"]) ? $item["EntityID"] : 0);
		if($params->GetProperty("EntityID"))
		{
			$itemList->Load($params, $user);
			$item[$this->name."List"] = $itemList->GetItems();
			if(!$item[$this->name."List"] && isset($_REQUEST['OnEmptyError'])){
			    return GetTranslation($_REQUEST['OnEmptyError']);
            }
		}
	}
}

class ItemListEditComponent extends ItemListViewComponent
{
	function PrepareBeforeShow(&$item, $user)
	{
        if (!isset($item[$this->name."List"]))
		parent::PrepareBeforeShow($item, $user);
		$script = '<script type="text/javascript">$(document).ready(function(){';
		$script .= 'InitItemListControl($("#'.$this->name.'"));';
		if(isset($item[$this->name."List"]))
		{
			for($i=0; $i<count($item[$this->name."List"]); $i++)
			{
				$json = json_encode($item[$this->name."List"][$i]);
				$script .= 'AddItemListRow($("#'.$this->name.'"),'.$json.');';
			}
		}
		$script .= "});</script>";
		$item[$this->name."ControlHTML"] = $script;
	}
	
	function PrepareAfterSave(&$item, $user)
	{
	
		$errorList = array();
		$stmt = GetStatement();
		$query = "SELECT ".$this->config["ID"]." AS EntityID FROM ".$this->config["Table"]." WHERE ".$this->config["KeyField"]."=".intval($item["EntityID"]);
		$currentItems = $stmt->FetchList($query);
		$currentItemsArray = array();
		
		for($i=0; $i<count($currentItems); $i++)
			$currentItemsArray[] = $currentItems[$i]["EntityID"];
		
		$editItems = array();
		$processedItems = array();
		$index = 1;
		
		for($i=0; $i<100; $i++)
		{
			if(isset($item[$this->name.$i."EntityID"]))
			{
				$saveObject = new Item(null, $this->config["Entity"], $this->config, "ListConfig");
				$saveObject->nestingPath = $item["NestingPath"];
				
				if($item[$this->name.$i."EntityID"])
				{
					$saveObject->SetProperty("EntityID", $item[$this->name.$i."EntityID"]);
					$editItems[] = $item[$this->name.$i."EntityID"];
				}
				
				foreach($item as $key=>$value)
				{
					$start = $this->name.$i;
					if(strpos($key, $start) === 0)
					{
						$saveObject->SetProperty(substr($key, strlen($start)), $value);
					}
				}
				
				$saveObject->SetProperty($this->config["KeyField"], intval($item["EntityID"]));
				
				if(!$saveObject->Save($user, $index))
				{
					$errorList = $saveObject->GetErrors();
					break;
				}
				else{
				    $processedItems[] = $saveObject->GetProperty("EntityID");
                }

				$index++;
			}
		}

		if ($errorList){
            $item[$this->name."List"] = array();
            $match_array = array();
            foreach ($item as $key => $value){
                if (preg_match("/".$this->name."(\d+)(\w+)/", $key, $match_array)){
                    if (!isset($item[$this->name."List"][$match_array[1]])) $item[$this->name."List"][$match_array[1]] = array();
                    $item[$this->name."List"][$match_array[1]][$match_array[2]] = $value;
                }
            }
            $item[$this->name."List"] = array_values($item[$this->name."List"]);
            return $errorList;
        }

		$removeArray = array();
		
		for($i=0; $i<count($currentItemsArray); $i++)
		{
			if(!in_array($currentItemsArray[$i], $editItems))
			{
				$removeArray[] = $currentItemsArray[$i];
			}
		}
		
		if(count($removeArray) > 0)
		{
			//remove
			$removeRequest = new LocalObject();
			$removeRequest->SetProperty("EntityIDs", $removeArray);
			$itemList = new ItemList(null, null, $this->config);
			$itemList->Remove($removeRequest);
		}
		
		return $errorList;
	}
	
	function Validate(&$item)
	{
		$properties = $item->GetProperties();
		$index = 1;
		for($i=0; $i<100; $i++)
		{
			if(isset($properties[$this->name.$i."EntityID"]))
			{
				$saveObject = new Item(null, $this->config["Entity"], $this->config, "ListConfig");
				$saveObject->nestingPath = $properties["NestingPath"];
				if($properties[$this->name.$i."EntityID"])
				{
					$saveObject->SetProperty("EntityID", $properties[$this->name.$i."EntityID"]);
				}
				foreach($properties as $key=>$value)
				{
					$start = $this->name.$i;
					if(strpos($key, $start) === 0)
					{
						$saveObject->SetProperty(substr($key, strlen($start)), $value);
					}
				}
				$saveObject->SetProperty($this->config["KeyField"], intval($properties["EntityID"]));
				$saveObject->SetProperty("NestingPath", array_merge($saveObject->nestingPath, array(array("Entity" => $saveObject->entity, "Index" => $index))));
				$saveObject->Validate();
				$item->AppendErrorsFromObject($saveObject);
				$index++;
			}
		}
		if($item->HasErrors()){
            $itemThis = array();
            $match_array = array();
            foreach ($properties as $key => $value){
                if (preg_match("/".$this->name."(\d+)(\w+)/", $key, $match_array)){
                    if (!isset($itemThis[$match_array[1]])) $itemThis[$match_array[1]] = array();
                    $itemThis[$match_array[1]][$match_array[2]] = $value;
                }
            }
            $item->SetProperty($this->name."List", array_values($itemThis));
        }
	}
}

?>