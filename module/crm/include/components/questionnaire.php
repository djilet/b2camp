<?php
/**
 * Created by PhpStorm.
 * User: der
 * Date: 07.05.18
 * Time: 17:00
 */
require_once(dirname(__FILE__)."/../component.php");

class QuestionnaireComponent extends BaseComponent {

    function PrepareBeforeShow(&$item, $user)
    {
        $entityId = isset($item['EntityID'])?$item['EntityID']:0;
        if ($entityId) {
            $stmt = GetStatement();
            $query = "SELECT * FROM crm_questionnaire WHERE " . $this->config['KeyField'] . "='" . $item['EntityID'] . "'";
            $questionnaire = $stmt->FetchRow($query);
        }

        if (!$entityId || !$questionnaire)
            $questionnaire = array(
                'qMedChronical'=>'',
                'qMedTrauma'=>'',
                'qMedAllergy'=>'',
                'qMedSea'=>'',
                'qMedDrugs'=>'',
                'qMedSun'=>'',
                'qPhysSport'=>'',
                'qPhysSweam'=>'',
                'qPhysEyes'=>'',
                'qPersonalPhobia'=>'',
                'qPersonalCharacter'=>'',
                'qPersonalIndependence'=>'',
                'qPersonalHobbies'=>'',
                'qAnthropoHeight'=>'',
                'qAnthropoWeight'=>'',
                'qAnthropoSize'=>'',
                'Created' => '',
                'Modified' => ''
            );

        $html = "";
        $hid = 0;
        $h4 = "";
        if ($this->config['View']=='editor'){

            foreach ($questionnaire as $key => $value){
                if ($key[0]!="q") {
                    continue;
                }
                $question = array();
                preg_match("/^([a-z])([A-Z][a-z]+)([A-Z][a-z]+)$/", $key, $question);

                if($question[2] != $h4){
                    $hid++;
                    $h4 = $question[2];
                    $html.= "<div class=\"row\"><h4><div class='chevron'>".$hid."</div>".GetTranslation("q".$h4."-editor", 'crm')."</h4></div>";
                }
                $html.= "<div class=\"row\"><label style='line-height: normal' for='".$key."'>".GetTranslation($key."-editor", 'crm')."</label></div>";
                $html.= "<div class=\"row\"><textarea class=\"form-control\" rows='2' cols='80' name='".$key."' id='".$key."'>".$value."</textarea></div>";
            }

        }
        else{
            foreach ($questionnaire as $key => $value){
                if ($value != null and $value != "") {
                    if ($key[0]!="q") {
                        continue;
                    }
                    $question = array();
                    preg_match("/^([a-z])([A-Z][a-z]+)([A-Z][a-z]+)$/", $key, $question);

                    if($question[2] != $h4){
                        $hid++;
                        $h4 = $question[2];
                        $html.= "<div class=\"row\"><h4><div class='chevron'>".$hid."</div>".GetTranslation("q".$h4, 'crm')."</h4></div>";
                    }

                    $html.= "<div class=\"row\"><label style='line-height: normal' for='".$key."'>".GetTranslation($key, 'crm')."</label></div>";
                    $class = $value?"row bg-white":"row";
                    $html.= "<div class=\"".$class."\"><i>".$value."</i></div>";
                }
            }
        }
        $item[$this->name.'ControlHTML'] = $html;
        $item[$this->name.'Created'] = $questionnaire['Created'];
        $item[$this->name.'Modified'] = $questionnaire['Modified'];
    }

    function PrepareAfterSave(&$item, $user)
    {
        $stmt = GetStatement();
        $query = "INSERT INTO `crm_questionnaire` (";
        $columns = array();
        $values = array();
        $set = array();
        foreach ($item as $key => $value){
            if(preg_match("/^q([A-Z][a-z]+)([A-Z][a-z]+)$/", $key)){
                $set[] = $key."=".Connection::GetSQLString($value);
                $columns[] = $key;
                $values[] = Connection::GetSQLString($value);
            }
        }
        if (!$set)
            return false;

        $columns[] = $this->config['KeyField'];
        $values[] = $item['EntityID'];
        $set[] = "Modified=".Connection::GetSQLString(date("Y-m-d H:i:s"));
        $query.= implode(",", $columns). ") VALUES(".implode(",", $values).")";
        $query.= " ON DUPLICATE KEY UPDATE ".implode(",", $set);

        if (!$stmt->Execute($query))
            return GetTranslation("sql-error");

        return false;
    }

}