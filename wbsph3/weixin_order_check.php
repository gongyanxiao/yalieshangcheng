<?php

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
$oid = $_GET['oid'];
if (isset($_GET['oid']) && !empty($_GET['oid'])) {
    $ids = implode(",", $_GET['oid']);
    if (count($ids) == 2) {
        $log_id = $ids[0];
        $sql = "SELECT is_paid FROM " . $GLOBALS['ecs']->table("pay_log") . " where log_id='" . $log_id . "'";
        $is_paid = $GLOBALS['db']->getOne($sql);
        if (!empty($is_paid) && ($is_paid * 1 == 1)) {
            header('Location:http://' . $_SERVER ['HTTP_HOST'].'user.php?act=account_detail&account_type=user_money');
        }
    }
}
?>
