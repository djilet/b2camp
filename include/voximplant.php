<?php

es_include("localobject.php");
es_include("user.php");

class VoxImplant extends LocalObject
{
	var $email = "info@2bcampcrm.ru";
	var $apiKey = "22433e96-20d3-4d27-9e8b-49c345eefc14";
	var $application = "crm.bluebirdcrm.voximplant.com";
	
	var $callInfoDelay = 1;
	
	function VoxImplant($data = array())
	{
		parent::LocalObject($data);
	}
	
	function GetVoximplantRedirectUserList()
	{
		$user = new User();
		$user->LoadBySession();
		
		$stmt = GetStatement();
		$query = "SELECT VoxImplantLogin, FirstName, LastName  
					FROM `user` 
					WHERE VoxImplantLogin IS NOT NULL 
						AND VoxImplantLogin != '' 
						AND UserID != ".$user->GetPropertyForSQL("UserID")." 
					ORDER BY LastName ASC, FirstName ASC";
		return $stmt->FetchList($query);
	}
	
	function GetIncomingCallInfo($phone)
	{
		$result = array();
		$stmt = GetStatement();
		$query = "SELECT * FROM(
				SELECT 'child' AS Entity, p.ChildID AS EntityID, CONCAT(c.LastName, ' ', c.FirstName) AS Title, CONCAT('7', p.Prefix, p.Number) AS Phone  
					FROM `crm_child_phone` AS p
						JOIN `crm_child` AS c ON c.ChildID=p.ChildID 
				UNION
					SELECT 'staff' AS Entity, s.StaffID AS EntityID, CONCAT(s.LastName, ' ', s.FirstName) AS Title, CONCAT('7', p.Prefix, p.Number) AS Phone 
					FROM `crm_staff_phone` AS p
						JOIN `crm_staff` AS s ON s.StaffID=p.StaffID
				UNION
					SELECT 'school' AS Entity, s.SchoolID AS EntityID, s.Title, CONCAT('7', p.Prefix, p.Number) AS Phone 
					FROM `crm_school_phone` AS p
						JOIN `crm_school` AS s ON s.SchoolID=p.SchoolID
				UNION
					SELECT 'legal' AS Entity, l.LegalID AS EntityID, l.Title, CONCAT('7', p.Prefix, p.Number) AS Phone 
					FROM `crm_legal_phone` AS p
						JOIN `crm_legal` AS l ON l.LegalID=p.LegalID
				) AS t
				WHERE t.Phone=".Connection::GetSQLString($phone)." 
				GROUP BY t.Entity, t.EntityID";
		
		$callerList = $stmt->FetchList($query);
		$result["CallerListJSON"] = json_encode($callerList);
		return $result;
	}

	function GetCallInfoByKey($callKey)
	{
		$params = array();
		$params["account_email"] = $this->email;
		$params["api_key"] = $this->apiKey;
		$params["with_calls"] = true;
		$params["with_records"] = true;
		$params["call_session_history_custom_data"] = $callKey;
		
		$result = file_get_contents("https://api.voximplant.com/platform_api/GetCallHistory?".http_build_query($params));
		$result = json_decode($result, true);
		if(isset($result["result"]) && count($result["result"]) === 1 && count($result["result"][0]["records"]) > 0)
		{
			return array(
				"Duration" => $result["result"][0]["records"][0]["duration"],
				"RecordURL" => $result["result"][0]["records"][0]["record_url"]
			);
		}
		else
		{
			$this->AddError("voximplant-call-info-error");
			return array(
				"Duration" => null,
				"RecordURL" => null
			);
		}
	}
}
?>