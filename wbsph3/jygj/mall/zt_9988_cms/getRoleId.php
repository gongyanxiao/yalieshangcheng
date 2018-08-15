<?php
$session_id = $_COOKIE['ECSCP_ID'];
$session_id = substr($session_id, 0,32);
$sql="select  user_name from xbmall_sessions where sesskey='{$session_id}'";
$admin_name =  getOne($sql);
$sql="select role_id from xbmall_admin_user where user_name='{$admin_name}' ";
$role_id = getOne($sql);
 
?>
 