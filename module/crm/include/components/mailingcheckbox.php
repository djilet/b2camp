<?php
require_once(dirname(__FILE__)."/../component.php");

class MailingCheckboxComponent extends BaseComponent{

	function PrepareBeforeShow(&$item, $user)
	{
		$stmt = GetStatement();
		$query = "SELECT * FROM ".$this->config['Table']." WHERE `ChildID`=".$item['EntityID'];
		$res = $stmt->FetchList($query)[0];
		$item["onSending"] = $res["onSending"];
		$item["onSMS"] = $res["onSMS"];
		$item["onPhoto"] = $res["onPhoto"];
	}

	function PrepareAfterSave(&$item, $user)
	{
		$stmt = GetStatement();
		if ($item['onSending']) {$item['onSending'] = 'Y';} else { $item['onSending'] = 'N'; };
		if ($item['onSMS']) {$item['onSMS'] = 'Y';} else { $item['onSMS'] = 'N';};
		if ($item['onPhoto']) {$item['onPhoto'] = 'Y';} else { $item['onPhoto'] = 'N'; };

		$isDB = $stmt->FetchRow("SELECT * FROM ".$this->config['Table']." WHERE ChildID=".$item['EntityID']);
		if ($item['EntityID'] && $isDB){
			$query = "UPDATE ".$this->config['Table']
			." SET onSending='".$item['onSending']."', onSMS='".$item['onSMS']."', onPhoto='".$item['onPhoto']
			."' WHERE ChildID=".$item['EntityID'];
		} else {
            $query = "INSERT INTO ".$this->config['Table']
                ." VALUES (".$item['EntityID'].", '".$item['onSending']."', '".$item['onSMS']."', '".$item['onPhoto']."')";
		}
		$stmt->Execute($query);
	}
}
?>