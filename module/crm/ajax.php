<?php

define("IS_ADMIN", true);
require_once(dirname(__FILE__)."/../../include/init.php");
require_once(dirname(__FILE__)."/init.php");
require_once(dirname(__FILE__)."/include/item.php");
require_once(dirname(__FILE__)."/include/item_list.php");
require_once(dirname(__FILE__)."/include/components/event.php");
es_include("user.php");
es_include("localpage.php");
es_include("voximplant.php");

$module = "crm";

$result = array();

$user = new User();
if (!$user->LoadBySession() || !$user->Validate(array(INTEGRATOR, ADMINISTRATOR, MANAGER, GUIDE)))
{
	$result["SessionExpired"] = GetTranslation("your-session-expired");
	exit();
}
else
{
	$request = new LocalObject(array_merge($_GET, $_POST));
	switch ($request->GetProperty("Action"))
	{
		case "GetAutocompleteData":
			$result = Utilities::GetAutocompleteData(
			    $request->GetProperty("Table"),
                $request->GetProperty("Field"),
                $request->GetProperty("Query")
            );
			break;
		case "GetEventList":
			$eventDate = $request->GetProperty("FilterEventDate");
			$managerID = $request->GetProperty("FilterManagerID");
			$eventListHtml = Event::drawEventList($eventDate, $managerID);
			$result["HTML"] = $eventListHtml;
			break;
		case "GetEntityListPopupHTML":
			$entity = $request->GetProperty("Entity");
			if(isset($GLOBALS["entityConfig"][$entity]["ListPopupTemplate"]) && $GLOBALS["entityConfig"][$entity]["ListPopupTemplate"])
			{
				$itemList = new ItemList($module, $entity, $GLOBALS["entityConfig"][$entity]);
				$itemList->Load($request, $user);				
				$popupPage = new PopupPage($module, true);
				$content = $popupPage->Load($GLOBALS["entityConfig"][$entity]["ListPopupTemplate"]);
				$content->LoadFromObjectList("ItemList", $itemList);
				
				$result["HTML"] = $popupPage->Grab($content);
			}
			else
			{
				$result["HTML"] = "";
			}
			break;
		case "GetEntityEditPopupForm":
			$entity = $request->GetProperty("Entity");
			if(isset($GLOBALS["entityConfig"][$entity]["EditPopupTemplate"]) && $GLOBALS["entityConfig"][$entity]["EditPopupTemplate"])
			{
				$item = new Item($module, $entity, $GLOBALS["entityConfig"][$entity], "EditPopupConfig");
				$item->LoadByID($request->GetProperty("EntityID"), $user);
				
				$popupPage = new PopupPage($module, true);
				$content = $popupPage->Load($GLOBALS["entityConfig"][$entity]["EditPopupTemplate"]);
				$content->LoadFromObject($item);
				$content->LoadFromObject($request, array("Entity", "EntityID"));
				$result["HTML"] = $popupPage->Grab($content);
			}
			else
			{
				$result["HTML"] = "";
			}
			break;
		case "SaveEntityFromPopup":
			$errors = array();
			$entity = $request->GetProperty("Entity");
			$item = new Item($module, $entity, $GLOBALS["entityConfig"][$entity], "EditPopupConfig");
			$item->LoadFromObject($request);

			$eventDateFrom = $request->GetProperty('EventDateFrom');
			$eventDateTo = $request->GetProperty('EventDateTo');

			if (!empty($eventDateFrom) && !empty($eventDateTo)) {
				
				$dateFromStr = strtotime($request->GetProperty('EventDateFrom'));
				$dateToStr   = strtotime($request->GetProperty('EventDateTo'));
				$managerId   = $request->GetProperty('ManagerID');

				//compare dates
				if ($dateFromStr >= $dateToStr) {
					$errors[] = 'Конечная дата должна быть больше начальной!';
				}

				if (date('d', $dateFromStr) !== date('d', $dateToStr)) {
					$errors[] = 'Дата начала и окончания должна относиться к одному дню!';
				}

				//check for intersected dates
				if (Event::isIntersectedEvent($dateFromStr, $dateToStr, $managerId)) {
					$errors[] = 'На данное время уже есть событие!';
				}
			}

			if (empty($errors)) {
				if($item->Save($user))
					$result["Message"] = $item->GetMessagesAsString("<br />");
				else
					$result["Error"] = $item->GetErrorsAsString("<br />");
			} else {
				$result["Error"] = implode("<br />", $errors);
			}

			break;
		case "RemoveEntity":
			$entity = $request->GetProperty("Entity");
			$config = $GLOBALS["entityConfig"][$entity];
			Utilities::ValidateAccess($config, "RemoveAccess");
			$itemList = new ItemList($module, $entity, $config);
			$itemList->Remove($request);
			break;
		case "GetCalendarEventListJSON":
			$entity = $request->GetProperty("Entity");
			
			$itemList = new ItemList($module, $entity, $GLOBALS["entityConfig"][$entity]);
			$itemList->Load($request, $user);
			
			$eventList = array();
			foreach ($itemList->GetItems() as $item)
			{
				$eventList[] = array(
					"id" => $item["EntityID"],
					"title" => $item["Title"],
					"start" => $item["EventDateFrom"],
					"end" => $item["EventDateTo"],
					"allDay" => false,
					"url" => "#",
					"className" => ($item["EventType"] == "private" ? "private-event" : "public-event")
				);
			}
			$result = $eventList;
			break;
		case "GetDuplicateListHTML":
			$entity = $request->GetProperty("Entity");
			if(isset($GLOBALS["entityConfig"][$entity]["ListDuplicateTemplate"]) && $GLOBALS["entityConfig"][$entity]["ListDuplicateTemplate"])
			{
				$itemList = new ItemList($module, $entity, $GLOBALS["entityConfig"][$entity]);
				$itemList->Load($request, $user);
				
				$popupPage = new PopupPage($module, true);
				$content = $popupPage->Load($GLOBALS["entityConfig"][$entity]["ListDuplicateTemplate"]);
				$content->LoadFromObjectList("ItemList", $itemList);
				
				$result["HTML"] = $popupPage->Grab($content);
			}
			else
			{
				$result["HTML"] = "";
			}
			break;
		case "GetFinanceAttachmentPopup":
			$entity = $request->GetProperty("Entity");

			if ($entity == 'parent') {
				$stmt = GetStatement();
				$sql = 'SELECT ChildID FROM crm_parent WHERE ParentID = ' . $request->GetProperty("EntityID");
				$childID = $stmt->FetchField($sql);

				if ($childID) {
					$request->SetProperty("Entity", "child");
				}
			}

			$item = new Item($module, $request->GetProperty("Entity"), $GLOBALS["entityConfig"][$request->GetProperty("Entity")], "ViewConfig");
			$item->LoadByID($request->GetProperty("EntityID"), $user);
			
			$popupPage = new PopupPage($module, true);
			$content = $popupPage->Load("mailing_finance_popup.html");
			$content->LoadFromObject($item);
			$content->SetVar("Entity", $request->GetProperty("Entity"));
			
			$result["HTML"] = $popupPage->Grab($content);
			break;
		case "GetUnreadTaskCount":
			$readComponent = Utilities::GetComponent(
				"UnreadTaskCount", 
				"components/read.php", 
				"ReadViewComponent", 
				array(
					"EntityIDField" => "TaskID", 
					"Table" => "crm_task", 
					"UserField" => "ExecutorManagerID", 
					"ReadField" => "Read",
					"LinkTable" => "crm_task2user",
					"LinkUserField" => "UserID", 
					"LinkReadField" => "Read"
				)
			);
			$unreadTaskCount = $readComponent->GetUnreadEntityCount();
			$result["UnreadTaskCount"] = ($unreadTaskCount > 0 ? $unreadTaskCount : "");
			break;
		case "GetPrivateEventListHTML":
			$config = array(
				'Table' => 'crm_event',
				'ID' => 'EventID',
				'ItemsPerPage' => 0,
				'ItemsOrderBy' => 't.EventDateFrom',
			);
			$config["ListConfig"] = array(
				
			);
			$itemList = new ItemList($module, "event", $config);
			$itemList->Load($request, $user);
			
			$popupPage = new PopupPage($module, true);
			$content = $popupPage->Load("blocks/block_private_event_list.html");
			$content->LoadFromObjectList("PrivateEventList", $itemList);
			
			$result["HTML"] = $popupPage->Grab($content);
			break;
		case "SaveCall":
			$voximplant = new VoxImplant();
			sleep($voximplant->callInfoDelay);
			$callInfo = $voximplant->GetCallInfoByKey($request->GetProperty("CallKey"));
			$request->AppendFromArray($callInfo);
			$request->SetProperty("UserID", $user->GetIntProperty("UserID"));
		
			$entity = "call";
			$item = new Item($module, $entity, $GLOBALS["entityConfig"][$entity], "EditConfig");
			$item->LoadFromObject($request);
			$item->SetProperty("Duration", $item->GetProperty("Duration") === null ? null : gmdate("H:i:s", $item->GetProperty("Duration")));
			$item->Save($user);
			$result["ErrorList"] = array_merge($voximplant->GetErrorsAsArray(), $item->GetErrorsAsArray());
			$result["MessageList"] = array_merge($voximplant->GetMessagesAsArray(), $item->GetMessagesAsArray());
			break;
        case "GetSeasonInfo":
            $stmt = GetStatement();
            $seasonId = $request->GetProperty("SeasonID");
            if ($seasonId) {
                $query = "SELECT s.TransferThereConditions, s.TransferBackConditions FROM crm_season s WHERE SeasonID=".$seasonId;
                $result = $stmt->FetchRow($query);
            }
            break;
        case "Select2Search":
            $searchedString = $request->GetProperty('q');
            $stmt = GetStatement();
            $query = "SELECT *, ChildID as id, CONCAT(LastName, ' ' , FirstName, ' ', MiddleName) as text FROM `crm_child`"
                ." WHERE CONCAT(LastName, ' ' , FirstName, ' ', MiddleName) LIKE '%".$searchedString."%'";
            $result = $stmt->FetchList($query);
            break;
        case "StaffContractSelectChange":
            $stmt = GetStatement();
            $query = "SELECT `ContractType` FROM `crm_staff_contract` WHERE `ContractID` = ".$request->GetProperty("ContractID");
            $result = $stmt->FetchField($query);
            break;
	}
}

echo json_encode($result);