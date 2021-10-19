<?php

if (!defined('IS_ADMIN'))
{
	echo "Access denied";
	exit();
}

require_once(dirname(__FILE__)."/init.php");
require_once(dirname(__FILE__)."/include/item.php");
require_once(dirname(__FILE__)."/include/item_list.php");

es_include("js_calendar/calendar.php");
es_include("urlfilter.php");
es_include("filesys.php");
es_include("user_action.php");

$userAction = new UserAction();
$userAction->trackRequest = clone $request;

$module = $request->GetProperty("load");
$adminPage = new AdminPage($module);
$urlFilter = new URLFilter();
$filterURLFilter = new URLFilter();

if ($request->IsPropertySet("entity"))
{
	$entity = $request->GetProperty('entity');
	$config = $GLOBALS['entityConfig'][$entity];
	if($config)
	{
		Utilities::ValidateAccess($config);
		
		$urlFilter->LoadFromObject($request, array('entity'));
		$filterURLFilter->LoadFromObject($request, Utilities::GetFilterFieldNames($config, 'ListConfig'));
		$filterURLFilter->SetProperty('FilteronSending', $request->GetProperty('FilteronSending'));
		$filterURLFilter->SetProperty('FilteronSMS', $request->GetProperty('FilteronSMS'));
		$filterURLFilter->SetProperty('FilteronPhoto', $request->GetProperty('FilteronPhoto'));
		$navigation = array(array("Title" => "", "Link" => $moduleURL."&entity=".$request->GetProperty("entity")));
		$javaScripts = array(
			array("JavaScriptFile" => ADMIN_PATH."module/crm/template/js/functions.js"),
			array("JavaScriptFile" => ADMIN_PATH."module/crm/template/js/dashboard.js"),
			array("JavaScriptFile" => ADMIN_PATH."template/plugins/typeahead/typeahead.bundle.js"),
			array("JavaScriptFile" => ADMIN_PATH."template/plugins/typeahead/handlebars.min.js"),
			array("JavaScriptFile" => ADMIN_PATH."template/plugins/responsive-tables/js/rwd-table.js"),
			array("JavaScriptFile" => ADMIN_PATH."template/plugins/calendar/moment.min.js"),
			array("JavaScriptFile" => ADMIN_PATH."template/plugins/calendar/fullcalendar.js"),
			array("JavaScriptFile" => ADMIN_PATH."template/plugins/datatables/js/jquery.dataTables.min.js"),
			array("JavaScriptFile" => ADMIN_PATH."template/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min.js"),
			array("JavaScriptFile" => ADMIN_PATH."template/plugins/calendar/lang/ru.js"),
			array("JavaScriptFile" => CKEDITOR_PATH."ckeditor.js"),
			array("JavaScriptFile" => CKEDITOR_PATH."ajexFileManager/ajex.js"),
		);
		$styleSheets = array(
			array("StyleSheetFile" => ADMIN_PATH."template/plugins/typeahead/css/typeahead.css"),
			array("StyleSheetFile" => ADMIN_PATH."template/plugins/responsive-tables/css/rwd-table.min.css"),
			array("StyleSheetFile" => ADMIN_PATH."template/plugins/calendar/fullcalendar.css"),
			array("StyleSheetFile" => ADMIN_PATH."template/plugins/datatables/css/jquery.dataTables.min.css"),
		);
		$header = array(
			"Title" => "",
			"Navigation" => $navigation,
			"JavaScripts" => $javaScripts,
			"StyleSheets" => $styleSheets,
			"ClearFormData" => $request->GetProperty("ClearFormData"),
			"ClearEntityID" => $request->GetIntProperty("ClearEntityID"),
			"ClearEntity" => $request->GetProperty("ClearEntity") ? $request->GetProperty("ClearEntity") : $request->GetProperty("entity"),
		);
		
		if ($request->IsPropertySet("EntityViewID"))
		{
			$view = "view";
			$entityID = $request->GetProperty("EntityViewID");
			Utilities::ValidateAccess($config["ViewConfig"]);
		}
		elseif ($request->IsPropertySet("EntityPrintID"))
		{
			$view = "print";
			$entityID = $request->GetProperty("EntityPrintID");
			Utilities::ValidateAccess($config["PrintConfig"]);
		}
		elseif ($request->IsPropertySet("EntityPrintList"))
		{
			$view = "printlist";
			$entityID = null;
			Utilities::ValidateAccess($config["PrintListConfig"]);
		}
		elseif($request->IsPropertySet("EntityID"))
		{
			$view = "edit";
			$entityID = $request->GetProperty("EntityID");
			Utilities::ValidateAccess($config["EditConfig"]);
		}
		else
		{
			//default view
			$view = "list";
			$entityID = null;
			Utilities::ValidateAccess($config["ListConfig"]);
		}
		
		//process actions
		$activeObject = null;
		if($request->GetProperty("Do") == "Save")
		{
			if($request->GetProperty("EntityID") > 0)
				Utilities::ValidateAccess($config["EditConfig"], "EditAccess");
			$activeObject = new Item($module, $entity, $config);
			$activeObject->AppendFromObject($request);
			$userAction->SetEntityTitle($activeObject);
			if ($activeObject->Save($user))
			{
				if(!$activeObject->HasErrors())
				{
					header("Location: ".$moduleURL."&".$urlFilter->GetForURL()."&EntityViewID=".$activeObject->GetProperty("EntityID")."&".$filterURLFilter->GetForURL()."&ClearFormData=1&ClearEntityID=".$request->GetIntProperty("EntityID"));
					exit;	
				}
				else if(!$request->GetProperty("EntityID"))
				{
					//new main object saved but has child entities errors
					$activeObject->SetProperty("LoadFromNewEntity", 1);
				}
			}
		}
		else if($request->GetProperty("Do") == "Remove")
		{
			Utilities::ValidateAccess($config, "RemoveAccess");
			$activeObject = new ItemList($module, $entity, $config);
			
			if ($config['Table'] == 'crm_bookkeeping') {
				$activeObject->Cancel($request, $user);
			} else {
				$activeObject->Remove($request);
			}
		}
		else if($request->GetProperty("Do") == "Cancel")
		{
			Utilities::ValidateAccess($config, "RemoveAccess");
			$activeObject = new ItemList($module, $entity, $config);
			$activeObject->Cancel($request, $user);
		}
		else if($request->GetProperty("Do") == "Action")
		{
			if(
			    $request->IsPropertySet("Action") &&
                isset($config['ActionConfig']) &&
                isset($config['ActionConfig'][$request->GetProperty("Action")])
            )
			{
				$actionConfig = $config['ActionConfig'][$request->GetProperty("Action")];
				$action = Utilities::GetComponent($request->GetProperty("Action"), $actionConfig["File"], $actionConfig["Class"], $config);
				if($action)
				{
					Utilities::ValidateAccess($actionConfig);
					$action->DoAction($request, $user);
				}
				if($request->IsPropertySet("Show"))
				{
					$view = $request->GetProperty("Show");
				}
			}
		}
		//load content
		switch ($view)
		{
			case "view":
				$content = $adminPage->Load($config['ViewTemplate'], $header);
				$content->SetVar("EntityID", $entityID);
				$content->SetVar("Action", $request->GetProperty("Action"));
				$item = new Item($module, $entity, $config, "ViewConfig");
                if (isset($action))
                    $item->LoadFromObject($action);
				$item->LoadByID($entityID, $user);
				$content->LoadFromObject($item);
                $content->LoadMessagesFromObject($item);
                $content->LoadErrorsFromObject($item);
                Utilities::LoadFilterData($request, $config, $content, 'ViewConfig');
				break;
			case "print":
				$popupPage = new PopupPage($module, true);
				$content = $popupPage->Load($config['PrintTemplate']);
				$item = new Item($module, $entity, $config, "PrintConfig");
				$item->LoadByID($entityID, $user);
				$content->LoadFromObject($item);
				$popupPage->Output($content);
				exit();
				break;
			case "edit":
				$content = $adminPage->Load($config['EditTemplate'], $header);
				$content->SetVar("EntityID", $entityID);
				$item = new Item($module, $entity, $config);
				$item->LoadByID($entityID, $user);
				if($activeObject != null)
				{
					$item->AppendFromObject($activeObject);
				}
				else if(!$request->Getproperty("EntityID"))
				{
					$item->AppendFromObject($request);
				}
				$content->LoadFromObject($item);
				break;
			case "printlist":				
				$popupPage = new PopupPage($module, true);
				$content = $popupPage->Load($config['PrintListTemplate'], $header);
				$config['ItemsPerPage'] = isset($config['PrintListItemsPerPage']) ? $config['PrintListItemsPerPage'] : 1000;
				$itemList = new ItemList($module, $entity, $config);
				$itemList->Load($request, $user);
				$content->LoadFromObjectList("ItemList", $itemList);
				Utilities::LoadFilterData($request, $config, $content, 'PrintListConfig');
				$popupPage->Output($content);
				exit();
				break;
			default:
				$content = $adminPage->Load($config['ListTemplate'], $header);
				$itemList = new ItemList($module, $entity, $config);
				$itemList->Load($request, $user);
				$content->LoadFromObjectList("ItemList", $itemList);
				Utilities::LoadFilterData($request, $config, $content, 'ListConfig');
				$content->SetVar(
				    "Paging",
                    $itemList->GetPagingAsHTML($moduleURL.'&'.$urlFilter->GetForURL().'&'.$filterURLFilter->GetForURL(array('Page')))
                );
				$content->SetVar(
				    "ListInfo",
                    GetTranslation(
                        'list-info1',
                        array('Page' => $itemList->GetItemsRange(), 'Total' => $itemList->GetCountTotalItems())
                    )
                );
                $content->SetVar("CurrentPage", $itemList->GetCurrentPage());
		}
		//add errors/messages processed by actions
		if($activeObject != null)
		{
			$content->LoadErrorsFromObject($activeObject);
			$content->LoadMessagesFromObject($activeObject);
			if ($view != "list")
			    $content->LoadFromObject($activeObject);
		}
		if(isset($action))
		{
			$content->LoadErrorsFromObject($action);
			$content->LoadMessagesFromObject($action);
			$content->LoadFromArray($action->GetContentData());
		}
	//end entity config
	}
	else
	{
		die('Missing config for entity: '.$request->GetProperty('entity'));
	}
}
else
{
	die('Entity not defined');
}

$content->SetVar("UserID", $user->GetProperty("UserID"));
$content->SetVar("ParamsForForm", $urlFilter->GetForForm());
$content->SetVar("CommonURLPrefix", $moduleURL);
$content->SetVar("EntityURLPrefix", $moduleURL."&".$urlFilter->GetForURL());
$content->SetVar("InnerFilterParamsForURL", $filterURLFilter->GetForURL());
$content->SetVar("InnerFilterParamsForForm", $filterURLFilter->GetForForm());
$content->SetVar("AUTOSAVE_INTERVAL", AUTOSAVE_INTERVAL);
if(isset($config["ShowExportButton"]) && $config["ShowExportButton"] && $user->GetProperty("Role") != MANAGER)
	$content->SetVar("ShowExportButton", 1);
if(isset($config["ShowPrintButton"]) && $config["ShowPrintButton"] && $user->GetProperty("Role") != MANAGER)
	$content->SetVar("ShowPrintButton", 1);
if(isset($config["ShowReassignButton"]) && $config["ShowReassignButton"] && $user->GetProperty("Role") == INTEGRATOR)
	$content->SetVar("ShowReassignButton", 1);
if(isset($config["ShowClearPanelButton"]) && $config["ShowClearPanelButton"])
	$content->SetVar("ShowClearPanelButton", 1);
if(isset($config["ShowSendToArchiveButton"]) && $config["ShowSendToArchiveButton"])
	$content->SetVar("ShowSendToArchiveButton", 1);
if(isset($config["ShowRemoveFromArchiveButton"]) && $config["ShowRemoveFromArchiveButton"])
	$content->SetVar("ShowRemoveFromArchiveButton", 1);
$content->SetVar("CURRENT_DATE", date("Y-m-d"));
$content->SetVar("Entity", $request->GetProperty("entity"));

$content->setVar('SortSeasonDate', 1);
$content->setVar('SortSales', 0);

if (isset($_POST['SortSeasonDate'])) {
	$content->setVar('SortSeasonDate', 1);
	$content->setVar('SortSales', 0);
}

if (isset($_POST['SortSales'])) {
	$content->setVar('SortSales', 1);
	$content->setVar('SortSeasonDate', 0);
}

if (isset($_POST['ManagerStDateFrom']))
	$content->setVar('ManagerStDateFrom', $_POST['ManagerStDateFrom']);

if (isset($_POST['ManagerStDateTo']))
	$content->setVar('ManagerStDateTo', $_POST['ManagerStDateTo']);

if(isset($item))
	$userAction->SetEntityTitle($item);

$adminPage->Output($content);

?>