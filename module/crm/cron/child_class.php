<?php
require_once(dirname(__FILE__)."/../../../include/init.php");

$logPath = PROJECT_DIR."var/log/child_class.log";
$f = fopen($logPath, "a");
fwrite($f, date("Y-m-d H:i:s")."\r\n");
fwrite($f, "Updating child Class field\r\n");

$stmt = GetStatement();

$query = "UPDATE `crm_child` SET Class = NULL WHERE Class >= 11";
$stmt->Execute($query);
fwrite($f, "Set to NULL: ".$stmt->GetAffectedRows()."\r\n");

$query = "UPDATE `crm_child` SET Class = Class + 1 WHERE Class IS NOT NULL";
$stmt->Execute($query);
fwrite($f, "Set to n+1: ".$stmt->GetAffectedRows()."\r\n\r\n");

fclose($f);