<?php
define("IS_ADMIN", true);
require_once(dirname(__FILE__)."/include/init.php");
es_include("localpage.php");
es_include("urlfilter.php");
es_include("user_action_list.php");
es_include("userlist.php");

$auth = new User();
$auth->ValidateAccess(array(INTEGRATOR, ADMINISTRATOR));

$request = new LocalObject(array_merge($_GET, $_POST));

$navigation = array(array('Title' => GetTranslation("title-user-action-list"), 'Link' => "user_action.php"));
$header = array(
    "Title" => GetTranslation("title-user-action-list"),
    'Navigation' => $navigation
    );

$adminPage = new AdminPage();
$content = $adminPage->Load("user_action_list.html", $header);

$filterParams = array('FilterUserID', 'FilterDateFrom', 'FilterDateTo');

//load filter data from session and to session
$session = GetSession();
foreach ($filterParams as $key)
{
	if($session->IsPropertySet("UserAction".$key) && !$request->IsPropertySet($key))
		$request->SetProperty($key, $session->GetProperty("UserAction".$key));
	else
		$session->SetProperty("UserAction".$key, $request->GetProperty($key));
}
$session->SaveToDB();

$userActionList = new UserActionList();

$urlFilter = new URLFilter();
$urlFilter->LoadFromObject($request, array_merge(array($userActionList->GetPageParam()), $filterParams));

$userActionList->LoadUserActionList($request);
$content->LoadFromObjectList("UserActionList", $userActionList);
$content->SetVar("Paging", $userActionList->GetPagingAsHTML("user_action.php?".$urlFilter->GetForURL()));
$content->SetVar("ListInfo", GetTranslation('list-info1', array('Page' => $userActionList->GetItemsRange(), 'Total' => $userActionList->GetCountTotalItems())));

$content->SetVar("ParamsForURL", $urlFilter->GetForURL());
$content->SetVar("ParamsForForm", $urlFilter->GetForForm());
$content->SetVar("ParamsForFilter", $urlFilter->GetForForm(array_merge(array($userActionList->GetPageParam()), $filterParams)));
$content->LoadFromObject($urlFilter);

$userList = new UserList();
$userList->LoadUserList($request, true);
for($i = 0; $i < $userList->GetCountItems(); $i++)
{
	if($userList->_items[$i]["UserID"] == $request->GetProperty("FilterUserID"))
		$userList->_items[$i]["Selected"] = 1;
}
$content->LoadFromObjectList("FilterUserList", $userList);

$adminPage->Output($content);

?>