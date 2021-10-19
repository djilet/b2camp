<?php
/**
 * Created by PhpStorm.
 * User: der
 * Date: 23.03.18
 * Time: 12:52
 */

require_once(dirname(__FILE__)."/../filter.php");

class OldStaffFilter extends BaseFilter{

    function AppendSQLCondition($request, &$join, &$where, &$having)
    {
        if (isset($this->config['OldIDs']) && count($this->config['OldIDs'])>0) {
            $condition = implode(",", $this->config['OldIDs']);
            $where[] = "t.StaffID IN (" . $condition . ")";
        }
    }
}