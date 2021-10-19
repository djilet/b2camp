<?php 
require_once(dirname(__FILE__)."/../component.php");

class ImageViewComponent extends BaseComponent
{
	var $imageParams;
	
	function ImageViewComponent($name, $config)
	{
		parent::BaseComponent($name, $config);
		$this->imageParams = LoadImageConfig($name, "data", $config["Image"]);
	}
	
	function GetSelectPrefixForSQL()
	{
		return "t.".$this->name;
	}
	
	function PrepareBeforeShow(&$item, $user)
	{
		for ($j = 0; $j < count($this->imageParams); $j++)
		{
			$v = $this->imageParams[$j];
			$item[$v["Name"]."Path"] = $v["Path"].$this->config["Path"]."/".$item[$this->name];
		}
	}
}

class ImageEditComponent extends ImageViewComponent
{
	var $_acceptMimeTypes = array(
		'image/png',
		'image/x-png',
		'image/gif',
		'image/jpeg',
		'image/pjpeg'
	);
	
	function GetUpdatePrefixForSQL($item)
	{
		return $this->name."=".Connection::GetSQLString($item[$this->name]);
	}
	
	function PrepareBeforeShow(&$item, $user)
	{
		$imageName="";
		if(isset($item[$this->name])) 
			$imageName = $item[$this->name];
		$entityID = 0;
		if(isset($item["EntityID"]))
			$entityID = $item["EntityID"];
		$imagePath = null;
		$script = '<script type="text/javascript">$(document).ready(function(){params = new Array();';
		for ($j = 0; $j < count($this->imageParams); $j++)
		{
			if($imagePath == null) 
			{
				$v = $this->imageParams[$j];
				$imagePath = $item[$v["Name"]."Path"] = $v["Path"].$this->config["Path"]."/".$imageName;
			}
			$script .= 'params.push({"Name" : "'.$this->imageParams[$j]['SourceName'].'","Width" : "'.$this->imageParams[$j]['Width'].'","Height" : "'.$this->imageParams[$j]['Height'].'","Resize" : "'.$this->imageParams[$j]['Resize'].'","X1" : "0",	"X2" : "0","Y1" : "0","Y2" : "0"});';
		}
		$script .= "CreateImageInput('".$this->name."', '".$imagePath."', '".$imagePath."', '".$imageName."', '".$entityID."', 'RemoveUserImage', 'ajax.php', 0, params);";
		$script .= "});</script>";
		$item[$this->name."ControlHTML"] = $script;
	}
	
	function PrepareBeforeSave(&$item, $user)
	{
		$fileSys = new FileSys();
		$savedImage = $item["Saved".$this->name];
		$dir = CRM_DATA_DIR.$this->config["Path"]."/";
		
		$newItemImage = $fileSys->Upload($this->name, $dir, false, $this->_acceptMimeTypes);
		if ($newItemImage)
		{
			$item[$this->name] = $newItemImage["FileName"];
			if ($savedImage && $savedImage != $newItemImage["FileName"])
				@unlink($dir.$savedImage);
		}
		else
		{
			if ($savedImage)
				$item[$this->name] = $savedImage;
			else
				$item[$this->name] = null;
		}
		return $fileSys->GetErrors();
	}
}

?>