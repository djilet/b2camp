<?php
set_time_limit(600);
require_once(dirname(__FILE__)."/../../../include/init.php");
es_include("voximplant.php");

$vox = new VoxImplant();
$stmt = GetStatement();
$query = "SELECT CallKey FROM `crm_call` WHERE `Status`='success' AND RecordURL IS NULL ORDER BY CallID DESC";
$callList = $stmt->FetchList($query);

foreach ($callList as $call)
{
	$callInfo = $vox->GetCallInfoByKey($call["CallKey"]);
	$query = "UPDATE `crm_call` SET Duration=".Connection::GetSQLString($callInfo["Duration"] === null ? null : gmdate("H:i:s", $callInfo["Duration"])).", 
					RecordURL=".Connection::GetSQLString($callInfo["RecordURL"])." 
				WHERE CallKey=".Connection::GetSQLString($call["CallKey"]);
	$stmt->Execute($query);
}

?>