<?php

define("IS_ADMIN", true);
require_once(dirname(__FILE__)."/../../include/init.php");
require_once(dirname(__FILE__)."/init.php");
require_once(dirname(__FILE__)."/include/item.php");
require_once(dirname(__FILE__)."/include/item_list.php");
require_once(dirname(__FILE__)."/include/components/event.php");
es_include("user.php");
es_include("localpage.php");

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
	$type = isset($_POST['FilterID']) ? $_POST['FilterID'] : '';
	$numbers = isset($_POST['Number']) ? $_POST['Number'] : array();
	$colors = isset($_POST['Color']) ? $_POST['Color'] : array();
	$id = isset($_POST['ID']) ? $_POST['ID'] : array();
	$count_id = count($id);
	$stmt = GetStatement();
	$old_ids = $stmt->FetchList("SELECT DirectoryID id FROM crm_directory WHERE  DirectoryType=".$type);
	$new_id_save = array();
	
	foreach ($old_ids as $key => $old_id)
	{
		if(!in_array($old_id['id'], $id))
			$new_id_save[] = $old_id['id'];
	}
	
	foreach ($id as $key => $id_item){
		$color = isset($colors[$key]) && !empty($colors[$key]) ? $colors[$key] : '';
		$number = isset($numbers[$key]) && !empty($numbers[$key]) ? $numbers[$key] : '';
		$stmt->Execute("UPDATE crm_directory SET Name='".$number."', Color='".$color."' WHERE DirectoryID = ".$id_item);
	}
	
	$numbers = array_slice($numbers, $count_id);
	$colors = array_slice($colors, $count_id);
		
	if(!empty($new_id_save))
		$stmt->Execute("DELETE FROM crm_directory WHERE  DirectoryType=".$type." AND DirectoryID IN (".implode(",", $new_id_save).")");
	
	foreach ($numbers as $key => $num)
	{
		$color = isset($colors[$key]) && !empty($colors[$key]) ? $colors[$key] : '';
		$stmt->Execute("INSERT INTO crm_directory (DirectoryType, Color, Name) VALUES (".$type.", '".$color."', '".$num."')");
	}
	
// 	$result['params'] = $_POST;
// 	echo json_encode($result);
}