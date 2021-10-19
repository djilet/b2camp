<?php 
require_once(dirname(__FILE__)."/../component.php");

class PhoneViewComponent extends BaseComponent
{
	function PrepareBeforeShow(&$item, $user)
	{
		if(isset($item["EntityID"]))
		{
			$stmt = GetStatement();
			$query = "SELECT Type,Prefix,Number FROM ".$this->config["Table"]." WHERE ".$this->config["KeyField"]."=".intval($item["EntityID"]);
			$item[$this->name."List"] = $stmt->FetchList($query);
		}
	}
}

class PhoneMorphViewComponent extends BaseComponent
{
	function PrepareBeforeShow(&$item, $user)
	{
		if(isset($item["EntityID"]))
		{
			$table = "crm_".$item[$this->config['EntityTypeField']]."_phone";
			$id = explode("_", $item[$this->config['EntityTypeField']]);
			$id = end($id);
			$id = ucfirst($id)."ID";
			$stmt = GetStatement();
			$query = "SELECT Type,Prefix,Number FROM ".$table."  
						WHERE ".$id."=".intval($item[$this->config["EntityIDField"]]);
			$item[$this->name."List"] = $stmt->FetchList($query);
		}
	}
}

class PhoneEditComponent extends PhoneViewComponent
{
	function PrepareBeforeShow(&$item, $user)
	{
		parent::PrepareBeforeShow($item, $user);
		
		$script = '<script type="text/javascript">$(document).ready(function(){';
		$script .= 'InitPhoneControl($("#'.$this->name.'"));';
		if(isset($item[$this->name."List"]))
		{
			for($i=0; $i<count($item[$this->name."List"]); $i++)
			{
				$rowInfo = $item[$this->name."List"][$i];
				//$script .= 'AddPhoneRow($("#'.$this->name.'"),"'.$rowInfo["Type"].'","'.$rowInfo["Prefix"].'","'.$rowInfo["Number"].'");';
				$script .= 'AddPhoneRow($("#'.$this->name.'"),"'.$rowInfo["Type"].'","'.$rowInfo["Prefix"].$rowInfo["Number"].'");';
			}
		}
		$script .= "});</script>";
		$item[$this->name."ControlHTML"] = $script;
	}
	
	function PrepareAfterSave(&$item, $user)
	{
		$stmt = GetStatement();
		$stmt->Execute("DELETE FROM ".$this->config["Table"]." WHERE ".$this->config["KeyField"]."=".intval($item["EntityID"]));
		for($i=0; $i<100; $i++)
		{
			if(isset($item[$this->name.$i."Type"]))
			{
				preg_match("/^\+7\-\(([0-9]{3})\)\-([0-9]{3})-([0-9]{2})-([0-9]{2})$/", $item[$this->name.$i."Number"], $matches);
				$prefix = $matches[1];
				$number = $matches[2].$matches[3].$matches[4];
				$stmt->Execute("INSERT INTO ".$this->config["Table"]."(".$this->config["KeyField"].",Type,Prefix,Number) VALUES(".intval($item["EntityID"]).",'".$item[$this->name.$i."Type"]."','".$prefix."','".$number."')");
			}
		}
	}
	
	function Validate(&$item)
	{
		$index = 1;
		$hasValid = false;
		for($i=0; $i<100; $i++)
		{
			if($item->IsPropertySet($this->name.$i."Type"))
			{
				if(preg_match("/^\+7\-\([0-9]{3}\)\-[0-9]{3}-[0-9]{2}-[0-9]{2}$/", $item->GetProperty($this->name.$i."Number"), $matches))
				{
					$hasValid = true;
				}
				else
				{
					$item->AddValidateError("incorrect", "PhoneNumber", array(array("Entity" => "phone", "Index" => $index)));
				}
				$index++;
			}
		}
		if(!$hasValid && isset($this->config["Required"]) && $this->config["Required"])
			$item->AddValidateError("empty", "Phone");
	}
}

class PhoneUserComponent extends BaseComponent
{

    function PrepareBeforeShow(&$item, $user)
    {
        if(isset($item["EntityID"]))
        {
            $stmt = GetStatement();
            $query = "SELECT t.Phone FROM ".$this->config["Table"]." t LEFT JOIN `crm_staff` USING (".$this->config["KeyField"].") WHERE StaffID=".intval($item["EntityID"]);
            $phone_string = $stmt->FetchField($query);

            $phone = array(
                'String' => $phone_string,
                'Type' => "mobile",
                'Prefix' => (string)substr($phone_string, 4, 3),
                'Number' => str_replace("-", "", substr($phone_string, 9))
            );

            $item[$this->name."List"] = array(
                $phone
            );
        }

    }

    function Validate(&$item)
    {
        $phone = $item->GetProperty($this->name);

        if($phone && !preg_match("/^\+7\-\([0-9]{3}\)\-[0-9]{3}-[0-9]{2}-[0-9]{2}$/", $phone, $matches))
        {
            $item->AddValidateError("incorrect", "PhoneNumber");
        }

        if(!$phone && isset($this->config["Required"]) && $this->config["Required"])
            $item->AddValidateError("empty", "Phone");
    }

}

?>