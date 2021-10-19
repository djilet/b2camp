<?php 
require_once(dirname(__FILE__)."/../component.php");

class CommentViewComponent extends BaseComponent
{
	var $imageParams;
	
	function CommentViewComponent($name, $config)
	{
		parent::BaseComponent($name, $config);
		$this->imageParams = LoadImageConfig("ManagerImage", "user", $config["Image"]);
	}
	
	function PrepareBeforeShow(&$item, $user)
	{
		if(isset($item["EntityID"]))
		{
			$stmt = GetStatement();
			$query = "SELECT CommentID, CONCAT(u.LastName, ' ', u.FirstName) AS ManagerName, u.UserImage AS ManagerImage, c.Time, c.Text FROM ".$this->config["Table"]." c LEFT JOIN user u ON c.ManagerID=u.UserID WHERE c.".$this->config["KeyField"]."=".intval($item["EntityID"])." ORDER BY c.Time DESC";
			$commentList = $stmt->FetchList($query);
			for($i=0; $i<count($commentList); $i++)
			{
				for ($j = 0; $j < count($this->imageParams); $j++)
				{
					$v = $this->imageParams[$j];
					$commentList[$i][$v["Name"]."Path"] = $v["Path"].$commentList[$i]["ManagerImage"];
				}
				if(isset($this->config["FileComponent"]))
				{
					$fileViewComponent = Utilities::GetComponent($this->config["FileComponent"]["Name"], $this->config["FileComponent"]["File"], $this->config["FileComponent"]["Class"], $this->config["FileComponent"]["Config"]);
					$commentItem = array("EntityID" => $commentList[$i]["CommentID"]);
					$fileViewComponent->PrepareBeforeShow($commentItem, $user);
					$commentList[$i][$fileViewComponent->name."List"] = $commentItem[$fileViewComponent->name."List"];
				}
			}
			$item[$this->name."List"] = $commentList;
		}
	}
}

class CharacteristicViewComponent extends BaseComponent
{
    var $imageParams;

    function CharacteristicViewComponent($name, $config)
    {
        parent::BaseComponent($name, $config);
        $this->imageParams = LoadImageConfig("ManagerImage", "user", $config["Image"]);
    }

    function PrepareBeforeShow(&$item, $user)
    {
        if (in_array($user->GetProperty("Role"), $this->config['FilterAccess']))
            $item[$this->name.'Access'] = true;
        if (in_array($user->GetProperty("Role"), $this->config['FilterAppend']))
            $item[$this->name.'Append'] = true;
        if (in_array($user->GetProperty("Role"), $this->config['FilterEdit']))
            $item[$this->name.'Edit'] = true;

        if(isset($item["EntityID"]))
        {
            if(in_array($user->GetProperty("Role"), array_diff($this->config['FilterAccess'], $this->config['FilterAppend'])))
                $where = " AND c.Moderate='Y'";
            else
                $where = "";
            $propertyName= isset($this->config['PropertyName']) ? $this->config['PropertyName'] : "EntityID";
            $stmt = GetStatement();
            $query = "SELECT CommentID, CONCAT(u.LastName, ' ', u.FirstName) AS UserName, c.UserID, u.UserImage AS UserImage, u.Role, c.Date, c.Text, s.Title AS SeasonTitle, s.SeasonID, s.DateTo 
                          FROM ".$this->config["Table"]." c 
                          LEFT JOIN user u ON c.UserID=u.UserID
                          LEFT JOIN crm_season s ON c.SeasonID=s.SeasonID
                          LEFT JOIN crm_child ch ON c.ChildID=ch.ChildID 
                          WHERE c.".$this->config["KeyField"]."=".intval($item[$propertyName]).$where."
                          GROUP BY CommentID 
                          ORDER BY s.SeasonID DESC, s.DateTo DESC, c.Date DESC";

            $commentList = $stmt->FetchList($query);

            $class = 0;
            $seasonTitle = "";
            for($i=0; $i<count($commentList); $i++)
            {
                $commentList[$i]['Role'] = GetTranslation("role-".$commentList[$i]['Role']);
                if($commentList[$i]['SeasonTitle'] != $seasonTitle){
                    $seasonTitle = $commentList[$i]['SeasonTitle'];
                    $class = ($class+1)%2;
                }
                else{
                    $commentList[$i]['SeasonTitle'] = "";
                }
                $commentList[$i]['Class'] = $class;
                $commentList[$i]['EditAccess'] = ($commentList[$i]['Moderate']=='N' && $commentList[$i]['UserID']==$user->GetProperty("UserID")) || in_array($user->GetProperty("Role"), [INTEGRATOR, ADMINISTRATOR]);

                if(isset($this->config["FileComponent"]))
                {
                    $fileViewComponent = Utilities::GetComponent($this->config["FileComponent"]["Name"], $this->config["FileComponent"]["File"], $this->config["FileComponent"]["Class"], $this->config["FileComponent"]["Config"]);
                    $commentItem = array("EntityID" => $commentList[$i]["CommentID"]);
                    $fileViewComponent->PrepareBeforeShow($commentItem, $user);
                    $commentList[$i][$fileViewComponent->name."List"] = $commentItem[$fileViewComponent->name."List"];
                }
            }
            $item[$this->name."List"] = $commentList;
        }
        $script = '<script type="text/javascript">$(document).ready(function(){';
        $script .= '$("#'.$this->name.'SeasonID").select2({placeholder:"",allowClear: true}).on("select2-open", function() {$(this).data("select2").results.addClass("overflow-hidden").perfectScrollbar();});';
        $script .= "});</script>";
        $item[$this->name."SeasonIDControlHTML"] = $script;
    }
}

?>