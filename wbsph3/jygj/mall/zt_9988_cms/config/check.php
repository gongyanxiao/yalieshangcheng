<?php

function isLogin()
{
    $session_id = $_COOKIE["ECSCP_ID"];
 
    $session_id = substr($session_id, 0, 32);
    $sql = "select * from xbmall_sessions where sesskey='{$session_id}' ";
    $isLogin = getRow($sql);
    if ($isLogin == null)
        return false;
        
    return true; // 已登录
}

if (isLogin() == false) {
    
    alertAndRelocation("登录超时", "/xbmall_admin/privilege.php?act=login");
}

?>