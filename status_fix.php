<?php
require_once(dirname(__FILE__)."/include/init.php");

$stmt = GetStatement();

$entityList = array(
	array("Key" => "ChildID", "Entity" => "child"),
	array("Key" => "LegalID", "Entity" => "legal"),
	array("Key" => "SchoolID", "Entity" => "school")
);

foreach ($entityList as $entity)
{
	$query = "SELECT * 
				FROM `crm_".$entity["Entity"]."_status` AS s 
					JOIN `crm_".$entity["Entity"]."_status2season` AS ss ON ss.HistoryStatusID=s.HistoryStatusID
				WHERE s.StatusID=6 
				ORDER BY Created ASC";
	$statusList6 = $stmt->FetchList($query);
	
	
	
	$query = "SELECT t.*, ss.SeasonID AS SeasonID 
				FROM `crm_".$entity["Entity"]."_status` AS t 
					JOIN 
					(
						SELECT MAX(HistoryStatusID) AS HistoryStatusID, ".$entity["Key"]." 
						FROM `crm_".$entity["Entity"]."_status`  
						GROUP BY ".$entity["Key"]."
					) AS s_last ON t.".$entity["Key"]." = s_last.".$entity["Key"]."
					LEFT JOIN `crm_".$entity["Entity"]."_status2season` AS ss ON ss.HistoryStatusID=t.HistoryStatusID
					WHERE t.HistoryStatusID=s_last.HistoryStatusID";
	$statusList = $stmt->FetchList($query);
	
	$query = "DELETE FROM `crm_".$entity["Entity"]."_status`";
	$stmt->Execute($query);
	$query = "DELETE FROM `crm_".$entity["Entity"]."_status2season`";
	$stmt->Execute($query);
	
	
	foreach ($statusList6 as $status)
	{
		$query = "SELECT COUNT(*) FROM `crm_".$entity["Entity"]."_status` 
			WHERE ".$entity["Key"]."=".Connection::GetSQLString($status[$entity["Key"]])." 
				AND SeasonID = ".Connection::GetSQLString($status["SeasonID"]);
		if($stmt->FetchField($query) > 0)
			continue;
		
		$query = "INSERT INTO `crm_".$entity["Entity"]."_status` 
					SET Created=".Connection::GetSQLString($status["Created"]).", 
						".$entity["Key"]."=".Connection::GetSQLString($status[$entity["Key"]]).", 
						StatusID=".Connection::GetSQLString($status["StatusID"]).",
						SeasonID=".Connection::GetSQLString($status["SeasonID"]);
		$stmt->Execute($query);
	}
	
	foreach ($statusList as $status)
	{
		$query = "SELECT COUNT(*) FROM `crm_".$entity["Entity"]."_status` 
					WHERE ".$entity["Key"]."=".Connection::GetSQLString($status[$entity["Key"]])." 
						AND (Created > ".Connection::GetSQLString($status["Created"])." OR (SeasonID=".intval($status["SeasonID"])." AND SeasonID > 0))";
		if($stmt->FetchField($query) > 0)
			continue;
		
		$query = "INSERT INTO `crm_".$entity["Entity"]."_status` 
					SET Created=".Connection::GetSQLString($status["Created"]).", 
						".$entity["Key"]."=".Connection::GetSQLString($status[$entity["Key"]]).", 
						StatusID=".Connection::GetSQLString($status["StatusID"]).",
						SeasonID=".intval($status["SeasonID"]);
		$stmt->Execute($query);
	}
}