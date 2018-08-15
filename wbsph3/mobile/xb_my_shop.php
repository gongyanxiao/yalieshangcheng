<?php
/**
 *   会员中心
 */

require_once(dirname ( __FILE__ ).'/xb_header.php');

 

call_user_func ( $function_name );

/**
 * 我的钱包
 */
function  action_my_shop(){
    global  $smarty,$user_id  ;
    $user=get_user_default($user_id);
    $smarty->assign("user",$user);
    $smarty->assign("shop_name","我的店铺");
    $smarty->display("xb_my_shop.dwt");
}

/**
 * 转出
 */
function  action_transe_out(){
    global  $smarty,$user_id  ;
    $user=get_user_default($user_id);
    $smarty->assign("user",$user);
    $smarty->assign("shop_name","转出");
    $smarty->display("xb_transfer.dwt");
}

 


?>
