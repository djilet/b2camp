<?php 
require_once(dirname(__FILE__)."/../component.php");

class StatusViewComponent extends BaseComponent
{
	function PrepareBeforeShow(&$item, $user)
	{
		$stmt = GetStatement();
		$query = "SELECT st.StatusID, DATE(st.Updated) AS StatusDate, s.DateTo AS SeasonDateTo, s.Archive 
					FROM ".$this->config["StatusTable"]." AS st 
						LEFT JOIN `crm_season` AS s ON s.SeasonID=st.SeasonID 
					WHERE st.".$this->config["KeyField"]."=".Connection::GetSQLString($item["EntityID"])." 
					ORDER BY st.Created DESC, st.HistoryStatusID DESC
					LIMIT 1";
		$row = $stmt->FetchRow($query);
		if(is_array($row) && $row["Archive"] != "Y")
		{
			$query = "SELECT Title FROM crm_status WHERE StatusID=".intval($row["StatusID"]);
			$item[$this->name."Title"] = $stmt->FetchField($query);
			$item[$this->name."Date"] = $row["StatusDate"];
		}
		else
		{
			$item[$this->name."Title"] = GetTranslation("status-didnt-call", "crm");
			if(is_array($row))
			{
				$item[$this->name."Date"] = $row["SeasonDateTo"];
			}
		}
	}
}

class StatusEditComponent extends BaseComponent
{
	function PrepareBeforeShow(&$item, $user)
	{
		$stmt = GetStatement();
        if (!isset($item[$this->name."List"])) {
            $query = "SELECT h.HistoryStatusID, h." . $this->config["KeyField"] . ", h.SeasonID, h.Quantity, h.StatusID, h.Created, s.Title AS SeasonTitle,t.ExecutionDateFrom AS StatusDate,
                        h.TransferThere, h.TransferThereNote, h.TransferBack, h.TransferBackNote 
					FROM " . $this->config["StatusTable"] . " AS h
						LEFT JOIN `crm_season` AS s ON s.SeasonID=h.SeasonID
						LEFT JOIN `crm_status2task`AS st ON h.HistoryStatusID=st.HistoryStatusID AND st.entity='" . $this->config['Entity'] . "'
						LEFT JOIN `crm_task` AS t ON st.TaskID=t.TaskID 
					WHERE h." . $this->config["KeyField"] . "=" . Connection::GetSQLString($item["EntityID"]) . "
					GROUP BY h.HistoryStatusID
					ORDER BY h.Created DESC, h.HistoryStatusID DESC";
            $item[$this->name."List"] = $stmt->FetchList($query);
            foreach ($item[$this->name."List"] as $k=>$v){
				$query = "SELECT c.*, CONCAT(d.LastName, ' ', d.FirstName, ' ', d.MiddleName) as Name FROM crm_child_status_history as c"
					." LEFT JOIN user as d ON c.UserID=d.UserID WHERE HistoryStatusID=".$v['HistoryStatusID']." ORDER BY ChangesID DESC";
				$item[$this->name."List"][$k]['ChangesHistory']=$stmt->FetchList($query);
			}
        }
		$query = "SELECT * FROM `crm_status` ORDER BY StatusID ASC";
		$item[$this->name."PossibleStatusList"] = $stmt->FetchList($query);
		
		$query = "SELECT SeasonID, Title, TypeID FROM `crm_season` WHERE Archive='N' ORDER BY TypeID ASC, DateFrom ASC";
		$item[$this->name."PossibleSeasonList"] = $stmt->FetchList($query);
		
		$script = '<script type="text/javascript">$(document).ready(function(){';
		$script .= 'InitStatusControl($("#'.$this->name.'"));';
		$script .= "});</script>";
		$item[$this->name."ControlHTML"] = $script;
		$item[$this->name."CustomQuantity"] = $this->config["CustomQuantity"];
	}
}

?>