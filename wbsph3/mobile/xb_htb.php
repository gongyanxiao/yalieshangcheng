<?php
/**
 *   会员中心
 */

require_once(dirname ( __FILE__ ).'/xb_header.php');

 

call_user_func ( $function_name );





function  action_htb(){
    global  $smarty,$user_id  ;
    $user=get_user_default($user_id);
    $smarty->assign("user",$user);
    $smarty->assign("shop_name","汇通宝");
    $smarty->display("xb_huitongbao.dwt");
}


function  action_to_transfer(){
    global  $smarty,$user_id  ;
    $user=get_user_default($user_id);
    $source = getRequestInt("source");
    $smarty->assign("user",$user);
    $jin_e = 0;//get_source_jin_e($user, $source);// 
    
    $smarty->assign("shop_name","转出");
    
    $smarty->assign("jin_e",$jin_e);
    $smarty->assign("source",$source);
    $smarty->display("xb_transfer.dwt");
    
}

/**
 *  
 */
function  action_htbdp(){
    global  $smarty,$user_id  ;
    $user=get_user_default($user_id);
    $smarty->assign("user",$user);
    $smarty->assign("shop_name","汇通宝大盘");
    $smarty->display("xb_htbdp.dwt");
}

/**
 * 我的邀请
 */
function  action_getData(){
    global  $smarty,$user_id  ;
    $smarty-> display("xb_invite.dwt");
}

/**
 * 安全中心
 */
function  action_my_safe(){
    global  $smarty,$user_id  ;
    $smarty->display("xb_safe_center.dwt");
}
// function  action_my(){
//     display("my.dwt");
// }


?>
