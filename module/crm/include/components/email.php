<?php 
require_once(dirname(__FILE__)."/../component.php");

class EmailViewComponent extends BaseComponent
{
	function PrepareBeforeShow(&$item, $user)
	{
		if(isset($item["EntityID"]))
		{
			$stmt = GetStatement();
			$query = "SELECT Email FROM ".$this->config["Table"]." WHERE ".$this->config["KeyField"]."=".intval($item["EntityID"]);
			$item[$this->name."List"] = $stmt->FetchList($query);
		}
	}
}

class EmailMorphViewComponent extends BaseComponent
{
	function PrepareBeforeShow(&$item, $user)
	{
		if(isset($item["EntityID"]))
		{
			$table = "crm_".$item[$this->config['EntityTypeField']]."_email";
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

class EmailRecieverComponent extends BaseComponent
{
	function PrepareBeforeShow(&$item, $user)
	{
		if(isset($item["EntityID"]))
		{
			$value = '';

			if ($item["CountReciever"] == 1) {
				$stmt = GetStatement();

				$query = "SELECT EntityType, RecieverEntityID, Email FROM ".$this->config["Table"].
					" WHERE ".$this->config["KeyField"]."=".intval($item["EntityID"]) . " LIMIT 1";

				$result = $stmt->FetchRow($query);

				$email = $result["Email"];
				$item['RecieverEntity'] = $result["EntityType"];
				$item['RecieverEntityID'] = $result["RecieverEntityID"];

			}
		}
	}
}

class EmailEditComponent extends EmailViewComponent
{
	function PrepareBeforeShow(&$item, $user)
	{
		parent::PrepareBeforeShow($item, $user);
		
		$script = '<script type="text/javascript">$(document).ready(function(){';
		$script .= 'InitEmailControl($("#'.$this->name.'"));';
		if(isset($item[$this->name."List"]))
		{
			for($i=0; $i<count($item[$this->name."List"]); $i++)
			{
				$rowInfo = $item[$this->name."List"][$i];
				$script .= 'AddEmailRow($("#'.$this->name.'"),"'.$rowInfo["Email"].'");';
			}
		}
		$script .= "});</script>";
		$item[$this->name."ControlHTML"] = $script;
	}

    function Validate( &$item)
    {
        $index = 0;

        for($i=0; $i<100; $i++)
        {
            if ($item->IsPropertySet($this->name.$i."Number")) {
                $index++;
                if (!filter_var($item->GetProperty($this->name . $i . "Number"), FILTER_VALIDATE_EMAIL)) {
                    $item->AddValidateError("incorrect", "EmailNumber", array(array("Entity" => "email", "Index" => $index)));
                }
            }
        }
        if(!$index && isset($this->config["Required"]) && $this->config["Required"])
            $item->AddValidateError("empty", "Email");
	}
	
	function PrepareAfterSave(&$item, $user)
	{	
		$stmt = GetStatement();
		$stmt->Execute("DELETE FROM ".$this->config["Table"]." WHERE ".$this->config["KeyField"]."=".intval($item["EntityID"]));
		
		for($i=0; $i<100; $i++)
		{
			if(isset($item[$this->name.$i."Number"]))
			{
				$number = $item[$this->name.$i."Number"];
				$stmt->Execute("INSERT INTO ".$this->config["Table"]."(".$this->config["KeyField"].",Email) VALUES(".intval($item["EntityID"]).",'".$number."')");
			}
		}
	}
	

}

class EmailUserComponent extends BaseComponent{

    function PrepareBeforeShow(&$item, $user)
	{
        if(isset($item["EntityID"]))
		{
            $stmt = GetStatement();
            $query = "SELECT t.Email FROM ".$this->config["Table"]." t LEFT JOIN `crm_staff` USING(".$this->config["KeyField"].") WHERE StaffID=".intval($item["EntityID"]);
            $item[$this->name] = $stmt->FetchField($query);
		}
    }

    function Validate( &$item)
	{
        $email = $item->GetProperty($this->name);

        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $item->AddValidateError("incorrect", "Email");
		}

        if(!$email && isset($this->config["Required"]) && $this->config["Required"])
        {
            $item->AddValidateError("empty", "Email");
		}

        if (!$item->GetErrors())
        {
            $stmt = GetStatement();
            $query = "SELECT `Email` FROM `user` WHERE Email='".$email."' AND UserID<>".$item->GetIntProperty('StaffUser');

            if ($result = $stmt->FetchField($query))
            {
                $item->AddError("email-is-not-unique");
		    }
	    }
    }

    function PrepareAfterSave(&$item, $user)
    {
        return false;
    }
}
?>