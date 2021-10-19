<?php 
require_once(dirname(__FILE__)."/../action.php");

class StatusAction extends BaseAction
{
	function DoAction($request, $user)
	{
		switch($this->name)
		{
			case "SaveStatus": 
				$this->ActionSaveStatus($request, $user);
			break;
		}
	}

	private function ActionSaveStatus($request, $user)
	{
		$stmt = GetStatement();
		$entity = $request->GetProperty("entity");
		$newHistoryStatusIDs = array();

		//add or update statuses from request
		if($request->GetProperty("StatusList"))
		{
			$request->SetProperty("StatusList", array_reverse($request->GetProperty("StatusList")));
			foreach($request->GetProperty("StatusList") as $status)
			{
				$transferThere = isset($status['TransferThere']) ? Connection::GetSQLString($status['TransferThere']) : "NULL";
				$transferThereNote = isset($status['TransferThereNote']) ? Connection::GetSQLString($status['TransferThereNote']) : "NULL";
				$transferBack = isset($status['TransferBack']) ? Connection::GetSQLString($status['TransferBack']) : "NULL";
				$transferBackNote = isset($status['TransferBackNote']) ? Connection::GetSQLString($status['TransferBackNote']) : "NULL";
				$currentDBStatusID = $stmt->FetchField(
					"SELECT StatusID FROM `".$this->actionConfig['StatusTable']
					."` WHERE HistoryStatusID=".intval($status["HistoryStatusID"])
				);
				$currentDBStatusName = $stmt->FetchField("SELECT Title FROM crm_status WHERE StatusID=".$currentDBStatusID);
				$currentStatusName = $stmt->FetchField("SELECT Title FROM crm_status WHERE StatusID=".$status["StatusID"]);
				//если статус уже существует
				if($status["HistoryStatusID"] > 0) {
					$query = "SELECT t.ExecutionDateTo
						FROM crm_child_status AS h
						LEFT JOIN crm_status2task AS st ON h.HistoryStatusID=st.HistoryStatusID
						LEFT JOIN crm_task AS t ON st.TaskID=t.TaskID
						WHERE h.HistoryStatusID=".$status["HistoryStatusID"];
					$currentDBStatusDate = $stmt->FetchField($query);
					$newHistoryStatusIDs[] = $status["HistoryStatusID"];
					// отказ/выкупили поменяли на отказ/выкупили
					if ($currentDBStatusDate==null && $status["StatusDate"]==null){
						if ($currentDBStatusName!=$currentStatusName){
							$text = '"'.'Статус '.$currentDBStatusName.' изменён на '.$currentStatusName.'"';
							$query = "INSERT INTO crm_child_status_history SET".
								" HistoryStatusID=".$status["HistoryStatusID"].
								", EventTime=".Connection::GetSQLString(date("Y-m-d H:i:s")).
								", UserID=".$user->GetProperty('UserID').
								", EventText=".$text;
							$stmt->Execute($query);
						}
					}
					// изменилась дата но не изменился статус
					elseif ($currentDBStatusDate!=ToSQLDate($status["StatusDate"]) && $currentDBStatusName==$currentStatusName){
						$text = '"'.'Дата статуса '.$currentDBStatusName.' изменена с '
							.$currentDBStatusDate.' на '.ToSQLDate($status["StatusDate"]).'"';
						$query = "INSERT INTO crm_child_status_history SET".
						" HistoryStatusID=".$status["HistoryStatusID"].
						", EventTime=".Connection::GetSQLString(date("Y-m-d H:i:s")).
						", UserID=".$user->GetProperty('UserID').
						", EventText=".$text;
						$stmt->Execute($query);
					}
					// изменился статус но не изменилась дата
					elseif ($currentDBStatusName!=$currentStatusName && $currentDBStatusDate==ToSQLDate($status["StatusDate"])){
						$text = '"'.'Статус '.$currentDBStatusName.' изменён на '.$currentStatusName.'"';
						$query = "INSERT INTO crm_child_status_history SET".
							" HistoryStatusID=".$status["HistoryStatusID"].
							", EventTime=".Connection::GetSQLString(date("Y-m-d H:i:s")).
							", UserID=".$user->GetProperty('UserID').
							", EventText=".$text;
						$stmt->Execute($query);
					}
					// изменилась дата и статус
					elseif ($currentDBStatusName!=$currentStatusName && $currentDBStatusDate!=ToSQLDate($status["StatusDate"])){
						$text = '"'.'Статус '.$currentDBStatusName.' изменён на '.$currentStatusName.'"';
						$query = "INSERT INTO crm_child_status_history SET".
							" HistoryStatusID=".$status["HistoryStatusID"].
							", EventTime=".Connection::GetSQLString(date("Y-m-d H:i:s")).
							", UserID=".$user->GetProperty('UserID').
							", EventText=".$text;
						$stmt->Execute($query);
					}

					$query = "UPDATE `".$this->actionConfig['StatusTable']."` 
						SET Updated=".(
							$currentDBStatusID == $status["StatusID"] ? 'Updated' : Connection::GetSQLString(date("Y-m-d H:i:s"))
						).",
							Quantity=".intval($status["Quantity"]).",
							StatusID=".Connection::GetSQLString($status["StatusID"]).",
							TransferThere=".$transferThere.",   
							TransferThereNote=".$transferThereNote.",   
							TransferBack=".$transferBack.",
							UserID=".$user->GetProperty('UserID').",
							TransferBackNote=".$transferBackNote."  
						WHERE HistoryStatusID=".Connection::GetSQLString($status["HistoryStatusID"]);
					$stmt->Execute($query);
					//если "Выкупили"
					if(isset($this->actionConfig["Entity2SeasonTable"]) && $status["StatusID"] == 6)
					{
						$this->AddEntity2SeasonRecord($this->config['ID'], $request->GetIntProperty("EntityID"), $status["SeasonID"], $this->actionConfig["Entity2SeasonTable"]);
					}
					//если сменили "Выкупили" на что то другое
					if(isset($this->actionConfig["Entity2SeasonTable"]) && $currentDBStatusID == 6 && $status["StatusID"] != 6)
					{
						$this->RemoveEntity2SeasonRecord($this->config['ID'], $request->GetIntProperty("EntityID"), $status["SeasonID"], $this->actionConfig["Entity2SeasonTable"]);
					}
					// если сменили предоплату на "выкупили"/"отказ" закрываем таску
					if($currentDBStatusID == 7 && ($status["StatusID"] == 6 || $status["StatusID"] == 4)){
						$taskId = $stmt->FetchField("SELECT `TaskID` FROM `crm_status2task` WHERE HistoryStatusID=".$status['HistoryStatusID']." AND `entity`='".$entity."'");
						$query = "UPDATE crm_task SET Status="."'closed'"." WHERE TaskID=".$taskId;
						$stmt->Execute($query);
					}
				}
				//если новый статус
				else {
					$query = "INSERT INTO `".$this->actionConfig['StatusTable']."` 
								SET ".$this->config['ID']."=".$request->GetIntProperty("EntityID").",
									Created=".Connection::GetSQLString(date("Y-m-d H:i:s")).", 
									Updated=".Connection::GetSQLString(date("Y-m-d H:i:s")).", 
									SeasonID=".Connection::GetSQLString($status["SeasonID"]).",
									Quantity=".intval($status["Quantity"]).",
									StatusID=".Connection::GetSQLString($status["StatusID"]).",
									TransferThere=".$transferThere.",   
									TransferThereNote=".$transferThereNote.",   
									TransferBack=".$transferBack.",
									UserID=".$user->GetProperty('UserID').",
									TransferBackNote=".$transferBackNote;
					$stmt->Execute($query);
					$newHistoryStatusIDs[] = $stmt->GetLastInsertID();
					$status['HistoryStatusID'] = $stmt->GetLastInsertID();
					$currentStatusName = $stmt->FetchField('SELECT Title FROM crm_status WHERE StatusID='.$status["StatusID"]);
					$text = '"'.'Создан статус '.$currentStatusName.'"';
					$query = "INSERT INTO crm_child_status_history SET".
						" HistoryStatusID=".$status["HistoryStatusID"].
						", EventTime=".Connection::GetSQLString(date("Y-m-d H:i:s")).
						", UserID=".$user->GetProperty('UserID').
						", EventText=".$text;
					$stmt->Execute($query);
					//add entity2season record if bought or pre-order
					if(isset($this->actionConfig["Entity2SeasonTable"]) && ($status["StatusID"] == 6 || $status["StatusID"] == 7))
					{
						$this->AddEntity2SeasonRecord(
							$this->config['ID'],
							$request->GetIntProperty("EntityID"),
							$status["SeasonID"],
							$this->actionConfig["Entity2SeasonTable"]
						);
					}
				}
				//если не "отказ" и "выкупили" добавляем/обновляем таску
				if ($status['StatusID']!=4 && $status['StatusID']!= 6){
					$this->UpdateStatusTask($status, $request, $user);
				}
			}
		}

		$this->RemoveStatusTasks($newHistoryStatusIDs, $request->GetIntProperty("EntityID"), $entity);

		if(isset($this->actionConfig["Entity2SeasonTable"])) {
		// удаяем из crm_child2season если статуса куплен/предоплата уже нет по этому ребенку
			$query = "SELECT SeasonID FROM ".$this->actionConfig['StatusTable']." WHERE ".$this->config['ID']."=".$request->GetIntProperty("EntityID");
			if(count($newHistoryStatusIDs) > 0) $query .= " AND HistoryStatusID NOT IN (".implode(", ", $newHistoryStatusIDs).")";
			$query .= " AND StatusID=6 OR StatusID=7";
			$statusList = $stmt->FetchList($query);
			foreach ($statusList as $status)
			{
				$this->RemoveEntity2SeasonRecord(
					$this->config['ID'],
					$request->GetIntProperty("EntityID"),
					$status["SeasonID"],
					$this->actionConfig["Entity2SeasonTable"]
				);
			}
		}

		//remove statuses from db which are not presented in request
		$query = "DELETE FROM `".$this->actionConfig['StatusTable']."` WHERE ".$this->config['ID']."=".$request->GetIntProperty("EntityID");
		if(count($newHistoryStatusIDs) > 0) $query .= " AND HistoryStatusID NOT IN (".implode(", ", $newHistoryStatusIDs).")";
		$stmt->Execute($query);
	}

	private function AddEntity2SeasonRecord($entityIDKey, $entityID, $seasonID, $table)
	{
		$stmt = GetStatement();
		$query = "SELECT COUNT(*) FROM `".$table."` WHERE SeasonID=".intval($seasonID)." AND ".$entityIDKey."=".intval($entityID);
		if($stmt->FetchField($query) == 0)
		{
			$query = "INSERT INTO `".$table."` 
						SET ".$entityIDKey."=".intval($entityID).", 
							SeasonID=".intval($seasonID);
			$stmt->Execute($query);
		}
	}

	private function RemoveEntity2SeasonRecord($entityIDKey, $entityID, $seasonID, $table)
	{
		$stmt = GetStatement();
		$query = "DELETE FROM `".$table."` 
					WHERE ".$entityIDKey."=".intval($entityID)." 
						AND SeasonID=".intval($seasonID);
		$stmt->Execute($query);
	}

	private function RemoveStatusTasks($newHistoryStatusIDs, $EntityID, $entity)
	{
		$stmt = GetStatement();
		$and = $newHistoryStatusIDs ? " AND(HistoryStatusID NOT IN (".implode(", ", $newHistoryStatusIDs)."))" :"";
		$query = "SELECT HistoryStatusID FROM ".$this->actionConfig['StatusTable']." 
			WHERE ".$this->config['ID']."=".$EntityID.$and;
		$deletingStatus = $stmt->FetchList($query);
		if ($deletingStatus) {
			$deletingStatus = "(".implode(",",array_column($deletingStatus, "HistoryStatusID")).")";
			$query = "UPDATE `crm_task` 
			  SET `Status`='closed', `Read`='Y' 
			  WHERE `TaskID` IN (SELECT `TaskID` FROM `crm_status2task` WHERE HistoryStatusID IN ".$deletingStatus." AND entity='".$entity."')";
			$stmt->Execute($query);

			$query = "DELETE FROM `crm_status2task` WHERE `HistoryStatusID` IN ".$deletingStatus." AND entity='".$entity."'";
			$stmt->Execute($query);
		}
	}

	private function UpdateStatusTask($status, LocalObject $request, User $user){
		$entity = $request->GetProperty("entity");
		$ChildName = GetStatement()->FetchField(
			"SELECT CONCAT(LastName, ' ' , FirstName, ' ', MiddleName) FROM crm_child WHERE ChildID=".$request->GetProperty("EntityID")
		);
		switch ($status["StatusID"]){
			case '1':
				$title = "Повторный звонок ребенку ".$ChildName." по статусу Звонили (думают)";
				break;
			case '2':
				$title = "Повторный звонок ребенку ".$ChildName." по статусу Не дозвонились";
				break;
			case '3':
				$title = "Позвонить ребенку ".$ChildName." по забронированному проекту";
				break;
			case '5':
				$title = "Повторный звонок ребенку ".$ChildName." по статусу Не звонить";
				break;
			case '7':
				$title = "Позвонить ребенку ".$ChildName." по предоплате за проект";
				break;
			default:
				$title = $request->GetProperty("Title");
		}
		$creator = $user->GetProperty("UserID");
		$date = ToSQLDate($status["StatusDate"]);
		$link = $_SERVER['REQUEST_URI']."&EntityViewID=".$request->GetProperty("EntityID");
		$description = "<a href=\'".$link."\'>".$title."</a>";

		if ($user->GetProperty("Role") == "parent")
			$manager = $request->GetProperty("ManagerID");
		else
		$manager = $user->GetProperty("UserID");

		$stmt = GetStatement();
		$taskId = $stmt->FetchField(
			"SELECT `TaskID` FROM `crm_status2task` WHERE HistoryStatusID=".$status['HistoryStatusID']." AND `entity`='".$entity."'"
		);
		//если таски с таким айди нет
		if (!$taskId){
			$notif = "N";
			if ($user->GetProperty("Role") != "parent" && $date > date("Y-m-d") )
				$notif = "Y";
			$query = "INSERT INTO `crm_task` (`Title`, `Priority`,`Created`,`CreatedManagerID`, `ExecutorManagerID`, `ExecutionDateFrom`, `ExecutionDateTo`, `Read`,`Description`)
				VALUES('".$title."','normal',".Connection::GetSQLString(date('Y-m-d H:i:s')).",'".$creator."','".$manager."','".$date."','".$date."','".$notif."','".$description."')";
			if ($stmt->Execute($query)) {$taskId = $stmt->GetLastInsertID();}
			else {$this->AddError('sql-error'); return;}
			$query = "INSERT INTO `crm_status2task` (`HistoryStatusID`, `TaskID`, `entity`) VALUES(".$status['HistoryStatusID'].",".$taskId.",'".$entity."')";
			$stmt->Execute($query);
		}
		//если таска уже существует обновляем её содержание и вставляем новую если нужно
		else {
			$notif = "IF(`ExecutorManagerID`=".$manager." AND `ExecutionDateFrom`='".$date."',`Read`,'".($date>date("Y-m-d")?"Y":"N")."')";
			$currentDBTaskTitle = $stmt->FetchField('SELECT Title FROM crm_task WHERE TaskID='.$taskId);
			$currentDBTaskDate = $stmt->FetchField('SELECT ExecutionDateTo FROM crm_task WHERE TaskID='.$taskId);
			$query = "UPDATE `crm_task` SET `Read`=".$notif.", 
				`ExecutorManagerID`=".$manager.",`ExecutionDateFrom`='".$date."',`ExecutionDateTo`='".$date."',`Title`='".$title."'
				WHERE TaskID=".$taskId;
			$stmt->Execute($query);
			if ($title != $currentDBTaskTitle || $date != $currentDBTaskDate){
				// вставляем новую строку в таблицу если данные не совпадают
				$query = "INSERT INTO `crm_task` (`Title`, `Priority`,`Created`,`CreatedManagerID`, `ExecutorManagerID`, `ExecutionDateFrom`, `ExecutionDateTo`, `Read`,`Description`) VALUES('".$title."','normal',".Connection::GetSQLString(date('Y-m-d H:i:s')).",'".$creator."','".$manager."','".$date."','".$date."',".$notif.",'".$description."')";
				$stmt->Execute($query);
				$taskId = $stmt->GetLastInsertID();
				$query = "INSERT INTO `crm_status2task` (`HistoryStatusID`, `TaskID`, `entity`) VALUES(".$status['HistoryStatusID'].",".$taskId.",'".$entity."')";
				$stmt->Execute($query);
			}
		}
	}
}

?>