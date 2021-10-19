<?php
/**
 * Created by PhpStorm.
 * User: der
 * Date: 19.03.18
 * Time: 14:53
 */

class StaffUserComponent extends BaseComponent {

    var $email,
        $userId,
        $passwd,
        $chkPasswd;

    function GetUpdatePrefixForSQL($item)
    {
        return "`UserID`=".$item["StaffUser"];
    }

    function PrepareBeforeShow(&$item, $user)
    {
        if(isset($item["EntityID"]))
        {
            $stmt = GetStatement();
            $query = "SELECT UserID FROM ".$this->config["Table"]." WHERE ".$this->config["KeyField"]."=".intval($item["EntityID"]);
            $item[$this->name."ID"] = $stmt->FetchField($query);
        }
    }

    function Validate(&$item)
    {
        $this->userId = $item->GetProperty("StaffUser");
        $this->passwd = $item->GetProperty("Password");
        $this->chkPasswd = $item->GetProperty("CheckPassword");

        if (!$this->userId && !$this->passwd){
            $item->AddValidateError("empty", "Password");
        }

        if (md5($this->passwd) != md5($this->chkPasswd))
            $item->AddValidateError("incorrect", "CheckPassword");

        return 0;
    }

    function PrepareBeforeSave(&$item, $user)
    {
        $this->email = $item['Email'];
        $phone = $item['Phone'];
        $dateOfBirth = ToSQLDate($item['DOB']);

        switch ($item['Group']){
            case "1":
                $role = "guide";
                break;
            case "2":
                $role = "manager";
                break;
            case "5":
                $role = HELPER;
                break;
            case "6":
                $role = INSTRUCTOR;
                break;
            default:
                $role = "servicestaff";
        }

        $this->passwd = $item["Password"]?md5($item["Password"]):"";

        $social = (string)$item['Social'];
        $city = (string)$item['City'];
        $street = (string)$item['Street'];
        $house = (string)$item['House'];
        $flat = (string)$item['Flat'];

        $this->userId = $item["StaffUser"];

        $stmt = GetStatement();

        if ($this->userId){
            $password_string = $this->passwd? "`Passwd`='".$this->passwd."'," : "";

            $query = "UPDATE `user` SET ".$password_string."
                `Email`='".$this->email."',
                `FirstName`='".$item['FirstName']."',
                `MiddleName`='".$item['MiddleName']."',
                `LastName`='".$item['LastName']."',
                `Sex`='".$item['Sex']."',
                `Phone`='".$phone."',
                `Social`='".$social."',
                `Role` = '".$role."',
                `City`='".$city."',
                `Street`='".$street."',
                `House`='".$house."',
                `flat`='".$flat."',
                `DOB`='".$dateOfBirth."'
            WHERE `UserID`=$this->userId";
            $result = $stmt->Execute($query);
        }
        else{
            $query = "INSERT INTO `user` (`Email`, `Passwd`, `FirstName`, `MiddleName`, `LastName`, 
                      `Sex`, `Phone`, `Role`, `Social`, `City`, `Street`, `House`, `Flat`,
                      `VoxImplantLogin`, `VoxImplantPassword`, `DOB`) ".
                "VALUES ('".$this->email."','". $this->passwd."','". $item['FirstName']."','". $item['MiddleName']."','". $item['LastName']."',
                        '".$item['Sex']."','".$phone."','".$role."','".$social."','".$city."','".$street."','".$house."','".$flat."',
                        '','','".$dateOfBirth."')";
            $result = $stmt->Execute($query);
            if ($result)
                $item['StaffUser'] = $stmt->_lastInsertID;
        }

        if (!$result) {
            return GetTranslation("sql-error");
        }

        return 0;
    }

}
