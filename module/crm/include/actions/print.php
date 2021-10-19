<?php
/**
 * Created by PhpStorm.
 * User: der
 * Date: 03.07.18
 * Time: 11:44
 */

require_once(dirname(__FILE__)."/../action.php");

class MassPrintAction extends BaseAction{

    function DoAction($request, $user)
    {
        $page = new PopupPage("crm", true);
        $content = $page->Load($this->actionConfig["Template"]);
        $request->SetProperty($this->actionConfig['Key'], $request->GetProperty($this->actionConfig['KeyField']));

        $itemList = new ItemList("crm", $this->actionConfig['Entity'], $this->actionConfig);
        $itemList->Load($request, $user);

        $content->LoadFromObjectList($this->actionConfig['EntityName']."List", $itemList);

        $page->Output($content);
        exit();
    }

}