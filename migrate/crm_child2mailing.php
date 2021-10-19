<?php
require_once(dirname(__FILE__)."/../include/init.php");

function child2mailingMigrate(){
	$start = microtime(true);
	$stmt = GetStatement();
	$res = $stmt->FetchList("SELECT ChildID FROM crm_child");
	while (ob_end_clean()){};ob_implicit_flush(1);
	foreach ($res as $v){
		$childID = $v['ChildID'];
		$res = $stmt->FetchRow('SELECT * FROM crm_child2mailing WHERE ChildID='.intval($childID));
		if ($res){
			$query = "UPDATE crm_child2mailing SET onSending='Y' WHERE ChildID=".$childID;
			echo $query.'<br>';
			$stmt->Execute($query);
		} else {
			$query = "INSERT INTO crm_child2mailing (ChildID, onSending, onSMS, onPhoto) VALUES (".intval($childID).", 'Y', 'N', 'N')";
			echo $query.'<br>';
			$stmt->Execute($query);
		}
	}
	$time = floor(microtime(true) - $start);
	echo '<h1>Скрипт заполнения таблицы crm_child2mailing успешно завершился за '.$time.' с</h1>';
}
//child2mailingMigrate();