<?php
/**
 * Created by PhpStorm.
 * User: der
 * Date: 23.03.18
 * Time: 11:33
 */

require_once(dirname(__FILE__)."/include/init.php");
es_include("user.php");
es_include("localpage.php");//Глобальные инклюды
es_include("restructure.php");//Основное функциональное содержимое этого раздела

$user = new User();
if (!$user->LoadBySession() || !$user->Validate(array(INTEGRATOR, ADMINISTRATOR)))
{//Никто кроме.
    $result = GetTranslation("not-enough-rights");
    echo $result;
    exit();
}

$module = "crm";
$entity = "staff";
$module_dir = PROJECT_DIR . "module/crm/";
require_once($module_dir . "init.php");
require_once($module_dir . "/include/item.php");
require_once($module_dir . "/include/item_list.php");
$config = $GLOBALS['entityConfig'][$entity];//Задаём объекты, с которыми работаем.
$php_self = $_SERVER['PHP_SELF'];

$title = "Перенос пользователей";
$navigation = array(array('Title'=> $title, 'Link'=>$php_self));

$request = new LocalObject(array_merge($_GET, $_POST));
$properties = array(
    'load' => "crm",
    'entity' => "staff"
);

if ($entityId = $request->GetProperty("EntityID")){ //Можем отредактировать пользователя
    $adminPage = new AdminPage($module);
    $title = "Старый пользователь";
    $navigation[] = array('Title'=>$title, 'Link'=>$php_self."?EntityID=".$entityId);
    $javaScript = array(
        array("JavaScriptFile" => ADMIN_PATH."module/crm/template/js/functions.js")
    );
    $styles = array();

    $header = array(
        'Title' => $title,
        'Navigation' => $navigation,
        "JavaScripts" => $javaScript,
        "StyleSheets" => $styles
    );

    if($request->GetProperty("Do") == "Save"){
        $activeObject = new Item($module, $entity, $config, "OldEditConfig");
        $activeObject->AppendFromObject($request);
        if ($activeObject->Save($user))
        {
            if(!$activeObject->HasErrors())
            {
                header("Location: ".$php_self);
                exit;
            }
        }
    }
    else if($request->GetProperty("Do") == "Remove")
    {
        $activeObject = new ItemList($module, $entity, $config, "OldEditConfig");
        $activeObject->Remove($request);
    }

    $content = $adminPage->Load($config['OldEditTemplate'], $header);
    $content->SetVar("EntityID", $entityId);
    $content->SetVar("PHP_SELF", $php_self);
    $item = new Item($module, $entity, $config, "OldEditConfig");
    $item->LoadByID($entityId, $user);
    if($activeObject != null)
    {
        $content->LoadErrorsFromObject($activeObject);
        $content->LoadMessagesFromObject($activeObject);
        $item->AppendFromObject($activeObject);
    }
    $content->LoadFromObject($item);

    $adminPage->Output($content);
    exit();
}

/* Основная часть */
$adminPage = new AdminPage();
$title = "Перенос пользователей";
$header = array(
    'Title' => $title,
    'Navigation' => $navigation
);
$content = $adminPage->Load("restructure.html", $header);
$content->SetVar("PHP_SELF", $php_self);
if (!$request->GetProperty("Confirm")) { //Таблица перевода
    $migrate = CheckRestructureDatabase();
    if ($migrate->HasMessages()) {
        $content->LoadMessagesFromObject($migrate);
        $adminPage->Output($content);
        exit();
    }
    dumpOldStructure();

    $imitate = new LocalObject($properties);

    $config['Access'] = array(INTEGRATOR, ADMINISTRATOR);
    $config['ItemsPerPage'] = 0;
    Utilities::ValidateAccess($config);

    $itemList = new ItemList($module, $entity, $config);
    $itemList->Load($imitate, $user);

    $staff2migrate = array();
    $affectedRows = array();
    foreach ($itemList->GetItems() as $item) {
        if (!$item['FIO']) {
            $staff2migrate[] = $item['EntityID'];
        }
    }

    $migrate = confirmRestructure(!$staff2migrate, "No staff to migrate");
    if ($migrate->HasMessages()) {
        $content->LoadMessagesFromObject($migrate);
        $content->SetVar("Confirm", true);
        $adminPage->Output($content);
        exit();
    }

    $config['ListConfig'] = $config['OldConfig'];
    unset($config['Join']);
    $config['ListConfig']['Filters'][0]['Config']['OldIDs'] = $staff2migrate;
    $config['ItemsOrderBy'] = '';
    $config["Second"] = true;
    $itemList = new ItemList($module, $entity, $config);
    $itemList->Load($imitate, $user);

    $duplicate = checkEmails();
    if ($duplicate->HasErrors()){
        $duplicateIds = $duplicate->GetProperty("StaffID");
        $defectedRows = array();
        foreach ($itemList->GetItems() as $item)
            if (in_array($item['EntityID'], $duplicateIds))
                $defectedRows[] = $item;

        $content->SetVar("defected", "Ошибка проверки пользователей");
        $content->SetLoop("defectedRows", $defectedRows);
        $content->LoadErrorsFromObject($duplicate);
        $adminPage->Output($content);
        exit();
    }

    $result = array();
    foreach ($itemList->GetItems() as $item) {
        $properties = array_merge($properties, $item);
        if($item['EmailList'])
            $properties['Email'] = $item['EmailList'][0]['Email'];
        else{
            $properties['Email'] = translit($item['FirstName']).".".translit($item['LastName'])."@2bcamp.ru";
        }
        $properties['Phone'] = "+7-(" . $item['PhoneList'][0]['Prefix'] . ")-" . preg_replace("/(\d{3})(\d{2})(\d{2})/", "$1-$2-$3", $item['PhoneList'][0]['Number']);
        $properties['DOB'] = date("d.m.Y", strtotime($item['DOB']));
        $properties['Do'] = "Save";
        $properties['Password'] = "standart";
        $properties['CheckPassword'] = "standart";
        $properties['StaffUser'] = intval($item['UserID']);

        $imitate = new LocalObject($properties);
        $activeObject = new Item($module, $entity, $config);
        $activeObject->AppendFromObject($imitate);
        $activeObject->Save($user);

        if ($activeObject->HasErrors()) {
            $content->LoadErrorsFromObject($activeObject);
            $result[] = $item;
        } else {
            $affectedRows[] = $item;
        }
    }

    $content->SetVar("defected", "Не удалось создать пользователей");
    $content->SetLoop("defectedRows", $result);
    $content->SetVar("affected", "Созданы пользователи");
    $content->SetLoop("affectedRows", $affectedRows);

    $migrate = confirmRestructure(!$result, "Перенос выполнен без ошибок");
    $content->LoadMessagesFromObject($migrate);
    $content->SetVar("Confirm", true);
    $adminPage->Output($content);
    exit();
}

$migration = linkChild2Users();
$content->LoadMessagesFromObject($migration);

if (!$migration->HasErrors())
    $migration = doRestructure();
$content->LoadErrorsFromObject($migration);
$content->LoadMessagesFromObject($migration);

$adminPage->Output($content);
