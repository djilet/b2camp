<?php
/**
 * Created by PhpStorm.
 * User: der
 * Date: 30.03.18
 * Time: 12:46
 */

function dumpOldStructure(){
    $sql_dir = PROJECT_DIR."sql-migrations/";
    $var_dir = PROJECT_DIR."var/";

    $stmt = GetStatement();
    //crm_staff dump
    $query = "SELECT `StaffID`,`LastName`, `FirstName`,`MiddleName`,`Sex`,`DOB`,`Social`,`AddressCity`,`AddressStreet`,`AddressHome`,`AddressFlat` FROM `crm_staff`";
    $fields = $stmt->FetchList($query);

    $text = "";
    if ($fields)
        $text = "INSERT INTO `crm_staff` (`StaffID`,`LastName`, `FirstName`,`MiddleName`,`Sex`,`DOB`,`Social`,`AddressCity`,`AddressStreet`,`AddressHome`,`AddressFlat`)\nVALUES";
    foreach($fields as $row){
        $text.= "(".$row['StaffID'].",'".$row['LastName']."','".$row['FirstName']."','".$row['MiddleName']."','".$row['Sex']."','".$row['DOB']."','".$row['Social']."','".$row['AddressCity']."','".$row['AddressStreet']."','".$row['AddressHome']."','".$row['AddressFlat']."'),\n";
    }
    $text = substr($text, 0, -2);
    $text.= "\nON DUPLICATE KEY UPDATE `LastName` = VALUES(LastName),
         `FirstName` = VALUES(FirstName),
         `MiddleName` = VALUES(MiddleName),
         `Sex` = VALUES(Sex),
         `DOB` = VALUES(DOB),
         `Social` = VALUES(Social),
         `AddressCity` = VALUES(AddressCity),
         `AddressStreet` = VALUES(AddressStreet),
         `AddressHome` = VALUES(AddressHome),
         `AddressFlat` = VALUES(AddressFlat);";

    $handle = fopen($var_dir."bk_staff.sql", "w");
    fwrite($handle, $text);
    fclose($handle);

    //crm_staff_email dump
    $query = "SHOW TABLES LIKE 'crm_staff_email'";
    if ($stmt->FetchField($query)){
        $query = "SELECT * FROM `crm_staff_email`";
        $fields = $stmt->FetchList($query);

        $text = file_get_contents($sql_dir."bk_staff_email.sql");
        $text.="\n";

        if ($fields)
            $text.= "INSERT INTO `crm_staff_email` (`StaffID`, `Email`)\nVALUES";
        foreach ($fields as $row) {
            $text.= "(".$row['StaffID'].",'".$row['Email']."'),";
        }
        $text = substr($text, 0, -1).";";

        $handle = fopen($var_dir."bk_staff_email.sql", "w");
        fwrite($handle, $text);
        fclose($handle);
    }
    //crm_staff_phone dump
    $query = "SHOW TABLES LIKE 'crm_staff_phone'";
    if ($stmt->FetchField($query)){
        $query = "SELECT * FROM `crm_staff_phone`";
        $fields = $stmt->FetchList($query);

        $text = file_get_contents($sql_dir."bk_staff_phone.sql");
        $text.="\n";

        if($fields)
            $text.= "INSERT INTO `crm_staff_phone` (`StaffID`, `Type`,`Prefix`,`Number`)\nVALUES";
        foreach ($fields as $row) {
            $text.= "(".$row['StaffID'].",'".$row['Type']."','".$row['Prefix']."','".$row['Number']."'),";
        }
        $text = substr($text, 0, -1).";";

        $handle = fopen($var_dir."bk_staff_phone.sql", "w");
        fwrite($handle, $text);
        fclose($handle);
    }
    //crm_child2staff dump

        $query = "SELECT * FROM `crm_child2staff`";
        $fields = $stmt->FetchList($query);

        $text="TRUNCATE `crm_child2staff`;\n";

        if ($fields)
            $text.= "INSERT INTO `crm_child2staff` (`StaffID`, `ChildID`)\nVALUES";
        foreach ($fields as $row) {
            $text.= "(".$row['StaffID'].",".$row['ChildID']."),";
        }
        $text = substr($text, 0, -1).";";

        $handle = fopen($var_dir."bk_child2staff.sql", "w");
        fwrite($handle, $text);
        fclose($handle);
}

function checkRestructureDatabase(){
    $stmt = GetStatement();
    $query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name='crm_staff'";

    $fields = $stmt->FetchList($query);
    $result = new Object();
    if (!$fields){
        $result->AddMessage(GetTranslation('sql-error'));
    }
    else {
        $fields = array_column($fields, "COLUMN_NAME");

        if (!in_array("LastName", $fields))
            $result->AddMessage("Database is already restructured. Nothing to do.");
    }
    return $result;
}

function checkEmails(){
    $result = new Object();

    $stmt = GetStatement();
    $query = "SELECT StaffID FROM `crm_staff_email` WHERE Email IN (SELECT Email FROM `crm_staff_email` WHERE Email<>'' AND Email IS NOT NULL GROUP BY Email HAVING COUNT(*)>1)";
    $ids = $stmt->FetchList($query);
    if ($ids){
        $result->AddError('email-is-not-unique');
        $result->SetProperty("StaffID", array_column($ids,"StaffID"));
        return $result;
    }

    $query = "UPDATE crm_staff s, (SELECT UserID, StaffID, se.Email FROM user JOIN crm_staff_email AS se ON user.Email=se.Email) t SET s.UserID=t.UserID WHERE s.StaffID=t.StaffID";
    if (!$stmt->Execute($query))
        $result->AddError("sql-error");

    return $result;
}

function linkChild2Users(){

    $stmt = GetStatement();
    $query = "UPDATE `crm_child2staff` c2s, `crm_staff` s SET c2s.StaffID=s.UserID WHERE c2s.StaffID=s.StaffID";
    $result = new Object();

    if ($stmt->Execute($query)){
        $result->AddMessage("Children relinked, affected ".$stmt->GetAffectedRows()." rows");
        $query = "DELETE FROM `crm_child2staff` WHERE StaffID=0";
        $stmt->Execute($query);
    }
    else {
        $result->AddError("SQL error Unlinking children");
    }
    return $result;

}

function confirmRestructure($cond, $message){
    $result = new Object();
    if ($cond)
        $result->AddMessage($message);

    return $result;
}

function doRestructure(){
    $result = new Object();
    $stmt = GetStatement();

    $handler = fopen(PROJECT_DIR."sql-migrations/restructure.sql", "r");

    while(($query = fgets($handler))!==false){
        $req = true;
        if (strlen($query)>1){
            $req = $stmt->Execute($query);
        }
        if (!$req){
            $result->AddError("sql-error");
            break;
        }
    }
    fclose($handler);
    if (!$result->HasErrors())
        $result->AddMessage("restructure-successfull");

    return $result;
}