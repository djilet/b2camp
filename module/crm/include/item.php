<?php

require_once(dirname(__FILE__)."/../init.php");
require_once(dirname(__FILE__)."/utilities.php");
es_include("localobject.php");
es_include("filesys.php");

class Item extends LocalObject
{
	var $module;
	var $entity;
	var $config;
	var $type;
	var $nestingPath = array();

	function Item($module, $entity, $config = array(), $type = "EditConfig", $data = array())
	{
		parent::LocalObject($data);
		$this->module = $module;
		$this->entity = $entity;
		$this->config = $config;
		$this->type = $type;
	}
	
	function LoadByID($id, $user)
	{
		$query = Utilities::GetSelectPrefixForSQL($this->config, $this->type);
		$query .= " WHERE t.".$this->config['ID']."=".intval($id);

		$this->LoadFromSQL($query, null, true);

		$this->PrepareBeforeShow($user);
		
		if ($this->GetIntProperty("EntityID"))
			return true;
		else
			return false;
	}

	function Validate()
	{
		$fields = $this->config[$this->type]['Fields'];
		for($i=0; $i<count($fields); $i++)
		{
			if($fields[$i]['Type'] == 'component')
			{
				$component = Utilities::GetComponent($fields[$i]['Name'], $fields[$i]['File'], $fields[$i]['Class'], $fields[$i]['Config']);
				$component->Validate($this);
			}
			else
			{
				if(isset($fields[$i]['Validate']))
				{
					switch($fields[$i]['Validate'])
					{
						case 'empty':
							if(!$this->ValidateNotEmpty($fields[$i]['Name']))
								$this->AddValidateError('empty', $fields[$i]['Name']);
							break;
						case 'int':
							if(!$this->ValidateNotEmpty($fields[$i]['Name']))
							{
								if(isset($fields[$i]['Required']) && $fields[$i]['Required'])
									$this->AddValidateError('empty', $fields[$i]['Name']);
								else
									$this->RemoveProperty($fields[$i]['Name']);
							}
							else if(!$this->ValidateInt($fields[$i]['Name']) || (isset($fields[$i]['Min']) && $this->GetIntProperty($fields[$i]['Name']) < $fields[$i]['Min']) || (isset($fields[$i]['Max']) && $this->GetIntProperty($fields[$i]['Name']) > $fields[$i]['Max']))
							{
								$this->AddValidateError('incorrect', $fields[$i]['Name']);
							}
							break;
						case 'date':
							if(!$this->ValidateNotEmpty($fields[$i]['Name']))
							{
								if(isset($fields[$i]['Required']) && $fields[$i]['Required'])
									$this->AddValidateError('empty', $fields[$i]['Name']);
								else 
									$this->RemoveProperty($fields[$i]['Name']);
							}
							else
							{
								if(!$this->ValidateDate($fields[$i]['Name'], 'dd.mm.yyyy'))
									$this->AddValidateError('incorrect', $fields[$i]['Name']);
							}
							break;
						case 'datetime':
							if(!$this->ValidateNotEmpty($fields[$i]['Name']))
							{
								if(isset($fields[$i]['Required']) && $fields[$i]['Required'])
									$this->AddValidateError('empty', $fields[$i]['Name']);
								else 
									$this->RemoveProperty($fields[$i]['Name']);
							}
							else
							{
								if(!$this->ValidateDateTime($fields[$i]['Name']))
									$this->AddValidateError('incorrect', $fields[$i]['Name']);
							}
							break;
						case 'email':
							if(!$this->ValidateNotEmpty($fields[$i]['Name']))
							{
								if(isset($fields[$i]['Required']) && $fields[$i]['Required'])
									$this->AddValidateError('empty', $fields[$i]['Name']);
							}
							else
							{
								if(!$this->ValidateEmail($fields[$i]['Name']))
									$this->AddValidateError('incorrect', $fields[$i]['Name']);
							}
							break;
						case 'option':
							if(!$this->ValidateNotEmpty($fields[$i]['Name']))
							{
								if(isset($fields[$i]['Required']) && $fields[$i]['Required'])
									$this->AddValidateError('empty', $fields[$i]['Name']);
								else 
									$this->RemoveProperty($fields[$i]['Name']);
							}
							else
							{
								if(!in_array($this->GetProperty($fields[$i]['Name']), $fields[$i]['Options']))
									$this->AddValidateError('incorrect', $fields[$i]['Name']);
							}
							break;
					}
				}
				else
				{
					switch($fields[$i]['Type'])
					{
						case 'date':
							if(!$this->ValidateNotEmpty($fields[$i]['Name']) || !$this->ValidateDate($fields[$i]['Name'], 'dd.mm.yyyy'))
								$this->RemoveProperty($fields[$i]['Name']);
							break;
						case 'datetime':
							if(!$this->ValidateNotEmpty($fields[$i]['Name']) || !$this->ValidateDate($fields[$i]['Name'], 'yyyy-mm-dd hh:ii'))
								$this->RemoveProperty($fields[$i]['Name']);
							break;
					}
				}
			}
		}
	}

	function Save($user, $index = null)
	{
		$this->SetProperty("NestingPath", array_merge($this->nestingPath, array(array("Entity" => $this->entity, "Index" => $index))));
		$stmt = GetStatement();

		$this->Validate();
        if ($this->HasErrors())
        {
            return false;
        }
		$this->PrepareBeforeSave($user);
		if ($this->HasErrors())
		{
			return false;
		}
		else
		{
			if ($this->GetIntProperty("EntityID") > 0)
			{
				$query = "UPDATE ".$this->config['Table']." SET ";
				$query .= Utilities::GetUpdateFieldsForSQL($this->config, $this->GetProperties(), $this->type);
				$query .= " WHERE ".$this->config['ID']."=".$this->GetIntProperty("EntityID");
			}
			else
			{
				$query = "INSERT INTO ".$this->config['Table']." SET ";
				$query .= Utilities::GetUpdateFieldsForSQL($this->config, $this->GetProperties(), $this->type);
			}

			if ($stmt->Execute($query))
			{
				$this->AddMessage('entity-saved', $this->module);
				if ($this->GetIntProperty("EntityID") == 0)
				{
					$this->SetProperty("EntityID", $stmt->GetLastInsertID());
					$this->SetProperty("NewItem", 1);
				}
				$this->PrepareAfterSave($user);
				if($this->HasErrors())
                    return false;

                $this->AddMessage('entity-saved', $this->module);
				return true;
			}
			else
			{
				$this->AddError("sql-error");
				return false;
			}
		}
	}
	
	private function PrepareBeforeShow($user)
	{
		$fields = $this->config[$this->type]['Fields'];
		for($i=0; $i<count($fields); $i++)
		{
			if($fields[$i]['Type'] == 'component')
			{
				$component = Utilities::GetComponent($fields[$i]['Name'], $fields[$i]['File'], $fields[$i]['Class'], $fields[$i]['Config']);
				if($component) {
                    if ($errorList = $component->PrepareBeforeShow($this->_properties, $user)){
                        switch (gettype($errorList)) {
                            case "array":
                                $this->_errors = array_merge($this->_errors, $errorList);
                                break;
                            case "string":
                                $this->_errors[] = $errorList;
                                break;
                            default:
                                $this->AddError("before-show-error");
                        }
				}
			}
		}
			elseif ($fields[$i]['Type'] == 'value')
            {
                $this->SetProperty($fields[$i]['Name'], $fields[$i]['Value']);
            }
		}
	}
	
	private function PrepareBeforeSave($user)
	{
		$fields = $this->config[$this->type]['Fields'];
		for($i=0; $i<count($fields); $i++)
		{
			if($fields[$i]['Type'] == 'component')
			{
				$component = Utilities::GetComponent($fields[$i]['Name'], $fields[$i]['File'], $fields[$i]['Class'], $fields[$i]['Config']);
				if($component)
				{
					if ($errorList = $component->PrepareBeforeSave($this->_properties, $user)) {
					    switch(gettype($errorList)){
                            case "array":
                                $this->_errors = array_merge($this->_errors, $errorList);
                                break;
                            case "string":
                                $this->_errors[] = $errorList;
                                break;
                            default:
                                $this->AddError("before-save-error");
                        }

                        return true;
                    }
				}
			}
		}

		return 0;
	}
	
	private function PrepareAfterSave($user)
	{
		$fields = $this->config[$this->type]['Fields'];
		for($i=0; $i<count($fields); $i++)
		{
			if($fields[$i]['Type'] == 'component')
			{
				$component = Utilities::GetComponent($fields[$i]['Name'], $fields[$i]['File'], $fields[$i]['Class'], $fields[$i]['Config']);
				if($component)
				{
					$errorList = $component->PrepareAfterSave($this->_properties, $user);
					if(is_array($errorList) && count($errorList) > 0)
					{
						$this->_errors = array_merge($this->_errors, $errorList);
					}
				}
			}
		}
	}
	
	function AddValidateError($type, $field, $additionalPath = array())
	{	
		$string = GetTranslation("validation-error-".$type, "crm");
		$string .= " \"" . GetTranslation("validation-field-".$field, "crm") . "\"";
		
		$path = array_reverse(array_merge($this->GetProperty("NestingPath"), $additionalPath));
		foreach ($path as $chunk)
		{
			$string .= " " . GetTranslation("validation-entity-".$chunk["Entity"], "crm");
			if(isset($chunk["Index"]))
			{
				$string .= " #".$chunk["Index"];
			}
		}
		$descriptionKey = "validation-description-".$field;
		if(GetTranslation($descriptionKey, "crm") != $descriptionKey)
		{
			$string .= " (" . GetTranslation($descriptionKey, "crm") . ")";
		}
		$this->_errors[] = $string;
	}
}

?>