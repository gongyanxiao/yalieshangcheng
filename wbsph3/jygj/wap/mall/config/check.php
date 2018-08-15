<?php

function isLogin()
{
    $session_id = $_COOKIE["ECS_ID"];
    $session_id = substr($session_id, 0, 32);
    $sql = "select * from xbmall_sessions where sesskey='{$session_id}' ";
    
    $isLogin = getRow($sql);
    if ($isLogin == null)
        return false;
   if ($isLogin['userid'] == 0)
        return false;
    return true; // 已登录
}

//获取登录信息
function getLoginInfo()
{
    $session_id = $_COOKIE["ECS_ID"];
    $session_id = substr($session_id, 0, 32);
    $sql = "select * from xbmall_sessions where sesskey='{$session_id}' ";
    $loginInfo = getRow($sql);
    return $loginInfo; // 
}


if (isLogin() == false) {

    alertAndRelocation("登录超时", "/mobile/user.php?act=login");
}
?>
