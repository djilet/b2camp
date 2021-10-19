<?php
/**
 * Created by PhpStorm.
 * User: der
 * Date: 20.04.18
 * Time: 10:55
 */
require_once(dirname(__FILE__)."/../../../include/init.php");
require_once(dirname(__FILE__)."/../init.php");

$stmt = GetStatement();
$query = "UPDATE `crm_task` SET `Read`='N' WHERE `ExecutionDateFrom`=CURDATE()";
$stmt->Execute($query);