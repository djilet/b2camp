<?php

define("IS_ADMIN", true);
require_once(dirname(__FILE__)."/include/init.php");
es_include("user.php");
es_include("userlist.php");
es_include("localpage.php");

$user = new User();
$user->ValidateAccess(array(INTEGRATOR, ADMINISTRATOR, MANAGER, GUIDE));

$request = new LocalObject(array_merge($_GET, $_POST));

$adminFile = dirname(__FILE__)."/module/".$request->GetProperty('load')."/admin.php";

if ($request->GetProperty('load') && is_file($adminFile))
{
	$moduleURL = "module.php?load=".$request->GetProperty('load');
	require_once($adminFile);
}
else
{
	echo "Module is not specified";
}

?>