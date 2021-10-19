<?php
/**
 * Created by PhpStorm.
 * User: der
 * Date: 16.04.18
 * Time: 11:08
 */

require_once(dirname(__FILE__)."/../component.php");

class ChildMainDocumentComponent extends BaseComponent {
    function GetSelectPrefixForSQL(){
        return "t.".$this->name;
    }

    function GetUpdatePrefixForSQL($item)
    {
        return $this->name."=".Connection::GetSQLString(str_replace([' ','№','-'], "", $item[$this->name]));
    }
}

class DocumentViewComponent extends BaseComponent {

    function PrepareBeforeShow(&$item, $user)
    {
        if (isset($item['EntityID']))
        {
            $stmt = GetStatement();
            $query = "SELECT DocumentID, Main, Type,Number FROM ".$this->config['Table']." WHERE ".$this->config['KeyField']."=".intval($item['EntityID']);
            $item[$this->name."List"] = $stmt->FetchList($query);

            $count = count($item[$this->name."List"]);
            for ($c = 0; $c<$count; $c++){
                $row = $item[$this->name."List"][$c];
                switch ($row['Type']){
                    case "passport":
                        $item[$this->name."List"][$c]['Number'] = preg_replace("/^[0-9]{4}/", "$0 ", $row['Number']);
                        break;
                    case "international":
                        $item[$this->name."List"][$c]['Number'] = preg_replace("/^[0-9]{2}/", "$0 ", $row['Number']);
                        break;
                    case "birth":
                        $item[$this->name."List"][$c]['Number'] = preg_replace("/^(\D+)(\D{2})(\d{6})$/u", "$1-$2 № $3", $row['Number']);
                        break;
                }
            }
        }
    }

}

class DocumentEditComponent extends DocumentViewComponent {

    function PrepareBeforeShow(&$item, $user)
    {
        parent::PrepareBeforeShow($item, $user);

        $script = "<script type='text/javascript'>$(document).ready(function(){";
        $script.= "InitDocumentControl($('#".$this->name."'));";
        if (isset($item[$this->name."List"])){
            for($i=0; $i<count($item[$this->name."List"]); $i++) {
                $rowInfo = $item[$this->name . "List"][$i];
                $script.= "AddDocumentRow($('#".$this->name."'),".$rowInfo['Main'].",'".$rowInfo['Type']."','".$rowInfo['Number']."', ".$rowInfo['DocumentID'].");";
            }
        }
        $script.= "});</script>";
        $item[$this->name."ControlHTML"] = $script;
    }

    function PrepareAfterSave(&$item, $user)
    {
        $stmt = GetStatement();
        $ids = array();
        for ($i=0; $i<25; $i++){
            if (isset($item[$this->name.$i."Type"])){
                $main = intval($item[$this->name.'Main'] == $i);
                $number = str_replace([' ','№','-'], "", $item[$this->name.$i."Number"]);

                if ($item[$this->name.$i."Id"]){
                    $query = "UPDATE ".$this->config['Table']." SET ".$this->config['KeyField']."=".intval($item['EntityID']).",Main=".$main.",
                        Type='".$item[$this->name.$i."Type"]."',`Number`='".$number."' WHERE DocumentID=".$item[$this->name.$i."Id"];
                    $res = $stmt->Execute($query);
                    $ids[] = $item[$this->name.$i."Id"];
                }
                else {
                    $query = "INSERT INTO " . $this->config['Table'] . "(" . $this->config['KeyField'] . ",Main,Type,Number) 
                    VALUES(" . intval($item['EntityID']) . "," . $main . ",'" . $item[$this->name . $i . "Type"] . "','" . $number . "')";
                    $res = $stmt->Execute($query);
                    if ($res) $ids[] = $stmt->_lastInsertID;
                }

            }
        }
        $where = $ids ? " AND DocumentID NOT IN (".implode(",", $ids).")" : "";
        $query = "DELETE FROM ".$this->config['Table']." WHERE ".$this->config['KeyField']."=".intval($item['EntityID']).$where;

        $stmt->Execute($query);
    }

}