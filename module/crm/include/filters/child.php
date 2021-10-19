<?php
/**
 * Created by PhpStorm.
 * User: der
 * Date: 22.03.18
 * Time: 17:16
 */
require_once(dirname(__FILE__)."/../filter.php");

class Child2StaffFilter extends BaseFilter{
    function AppendSQLCondition($request, &$join, &$where, &$having)
    {
        $userId = $GLOBALS['user']->GetProperty("UserID");
        $role = $GLOBALS['user']->GetProperty("Role");
        $alias = "l" . count($join);

        if (in_array($role, array(GUIDE))) {
            $join[] = "LEFT JOIN " . $this->config['Table'] . " " . $alias . " ON t." . $this->config['FromField'] . "=" . $alias . "." . $this->config['ToField'];
            $where[] = $alias . ".StaffID=" . $userId;
        }
    }
}