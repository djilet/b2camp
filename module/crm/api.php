<?php
require_once(dirname(__FILE__)."/../../include/init.php");
require_once(dirname(__FILE__)."/init.php");
require_once(dirname(__FILE__)."/include/item.php");
require_once(dirname(__FILE__)."/include/item_list.php");
es_include("voximplant.php");
es_include("user.php");
es_include("userlist.php");

$module = "crm";
$request = new LocalObject(array_merge($_GET, $_POST));

if(strlen(GetFromConfig("ApiKey")) > 0 && $request->GetProperty("ApiKey") != GetFromConfig("ApiKey"))
	Send403();

$result = array();

//do all actions by user #1 (Anna Baykalova with integrator role)
$user = new User();
$user->LoadByID(1);

switch($request->GetProperty("Action"))
{
	case "AddTask":
		$config = $GLOBALS["entityConfig"]["task"];
		$item = new Item($module, "task", $config);
		$item->LoadFromObject($request);
		$item->SetProperty("CreatedManagerID", $user->GetProperty("UserID"));
		$item->SetProperty("ExecutorManagerID", $user->GetProperty("UserID"));
		if($item->Save($user) && !$item->HasErrors())
		{
			$result["Result"] = 1;
			$userList = new UserList();
			$userList->LoadUserList($request, true);
			$subscriberIDs = array();
			for($i = 0; $i < $userList->GetCountItems(); $i++)
			{
				$subscriberIDs[] = $userList->_items[$i]["UserID"];
			}
			$request->SetProperty("SubscriberID", $subscriberIDs);
			$request->SetProperty("EntityID", $item->GetProperty("EntityID"));
			
			$actionConfig = $config['ActionConfig']['SaveSubscribers'];
			$action = Utilities::GetComponent('SaveSubscribers', $actionConfig["File"], $actionConfig["Class"], $config);
			if($action)
			{
				$action->DoAction($request, $user);
			}
		}
		else
		{
			$result["Result"] = 0; 
			$result["ErrorList"] = $item->GetErrors();
		}
		break;
	case "GetIncomingCallInfo":
		$voximplant = new VoxImplant();
		$result = $voximplant->GetIncomingCallInfo($request->GetProperty("Phone"));
		break;
	/*
	case "ClearStatuses":
		$entity = $request->GetProperty("Entity");

		if (!empty($entity)) {
			$stmt = GetStatement();
			$sql = 'SELECT '.ucfirst($entity).'ID FROM crm_'.strtolower($entity);
			$rows = $stmt->FetchList($sql);

			$insertSql = '';
			foreach ($rows as $row) {
				$insertSql = 'INSERT INTO crm_'.strtolower($entity).'_status VALUES(NULL, '.$row[ucfirst($entity) . 'ID'].', 7, now());';
				$stmt->Execute($insertSql);
				$result['Result'] = 1;
				$result['Message'] = 'Статусы с '.$entity.' обнулены!';
			}
		} else {
			$result['Result'] = 0;
			$result['Message'] = 'Ошибка!';
		}
	*/
}
header("Content-type: application/json; charset=utf-8");
echo json_encode($result);
?>