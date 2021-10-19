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
		case "getFile":
			$url = stripcslashes($request->GetProperty("url"));
			$url = urlencode($url);
			$url = file_get_contents('http://crm.local/include/filegator/repository/%D0%9D%D0%BE%D0%B2%D1%8B%D0%B9%20%D1%82%D0%B5%D0%BA%D1%81%D1%82%D0%BE%D0%B2%D1%8B%D0%B9%20%D0%B4%D0%BE%D0%BA%D1%83%D0%BC%D0%B5%D0%BD%D1%82%20%287%29.txt');
			$result = $url;
			break;	
		default:
			$result = "Unknown action";
	}
}

echo json_encode($result);

?>