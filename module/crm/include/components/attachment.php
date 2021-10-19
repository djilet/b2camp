<?php 
require_once(dirname(__FILE__)."/../component.php");

class AttachmentViewComponent extends BaseComponent
{
	function PrepareBeforeShow(&$item, $user)
	{
		if(isset($item["EntityID"]))
		{
			$stmt = GetStatement();
			$query = "SELECT AttachmentID, FilePath FROM ".$this->config["Table"]." WHERE ".$this->config["KeyField"]."=".intval($item["EntityID"]);
			$attachmentList = $stmt->FetchList($query);
			for($i = 0; $i < count($attachmentList); $i++)
			{
				$attachmentList[$i]["FilePath"] = ProjectDirToURLPrefix($attachmentList[$i]["FilePath"]);
			}
			$item[$this->name."List"] = $attachmentList;
		}
	}
}