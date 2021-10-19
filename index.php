<?php

define("IS_ADMIN", true);
require_once(dirname(__FILE__)."/include/init.php");
es_include("localpage.php");
es_include("user.php");

$user = new User();
$request = new LocalObject(array_merge($_POST, $_GET));

if ($request->GetProperty("Logout"))
{
	$user->Logout();
	$adminPage = new PopupPage();
	$content = $adminPage->Load("login.html");
	$content->LoadMessagesFromObject($user);
	$adminPage->Output($content);
}
else
{
	if ($user->LoadBySession() && $user->Validate(array(INTEGRATOR, ADMINISTRATOR, MANAGER, GUIDE)))
	{
		if ($request->GetProperty("ReturnPath"))
			header("Location: ".$request->GetProperty("ReturnPath"));
		elseif($user->GetProperty("Role") == GUIDE)
			header("Location: ".ADMIN_PATH."module.php?load=crm&entity=child");
		else
			header("Location: ".ADMIN_PATH."module.php?load=crm&entity=dashboard&EntityViewID=1");
		exit();
	}

	if ($request->GetProperty("Login"))
	{
		if ($user->LoadByRequest($request) && $user->Validate(array(INTEGRATOR, ADMINISTRATOR, MANAGER, GUIDE)))
		{
			if ($request->GetProperty("ReturnPath"))
				header("Location: ".$request->GetProperty("ReturnPath"));
			elseif($user->GetProperty("Role") == GUIDE)
				header("Location: ".ADMIN_PATH."module.php?load=crm&entity=child");
			else
				header("Location: ".ADMIN_PATH."module.php?load=crm&entity=dashboard&EntityViewID=1");
			exit();
		}
	}
	$adminPage = new PopupPage();
	$content = $adminPage->Load("login.html");
	$content->LoadErrorsFromObject($user);
	$content->LoadFromObject($request, array("ReturnPath", "RememberMe", "Login"));
	$adminPage->Output($content);
}

?>