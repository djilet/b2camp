<?php

define("IS_ADMIN", true);
require_once(dirname(__FILE__)."/include/init.php");
es_include("localpage.php");
es_include("user.php");

$result = array();

$user = new User();
if (!$user->LoadBySession() || !$user->Validate(array(INTEGRATOR, ADMINISTRATOR, MANAGER, GUIDE)))
{
	$result["SessionExpired"] = GetTranslation("your-session-expired");
}
else 
{
	$request = new LocalObject(array_merge($_GET, $_POST));
	switch ($request->GetProperty("Action"))
	{
		case "RemoveUserImage":
			$user = new User();
			$user->RemoveUserImage($request->GetProperty("ItemID"), $request->GetProperty('SavedImage'));
			$result = "Done";
			break;
		default:
			$result = "Unknown action";
	}
}

echo $result;

?>