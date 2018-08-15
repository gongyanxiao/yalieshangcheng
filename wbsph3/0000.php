<?php

/**
 * 模拟
 * ============================================================================
 * 版权所有 2005-2010 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: douqinghua $
 * $Id: flow.php 17218 2011-01-24 04:10:41Z douqinghua $
 */
define ( 'IN_ECS', true );

require (dirname ( __FILE__ ) . '/includes/init.php');
require (ROOT_PATH . 'includes/lib_order.php');

if ($_REQUEST ['act'] == 'chong_zhi') {
	$db = $GLOBALS ['db'];
	$amount = $_REQUEST ['amount'];
	$mobile_phone = $_REQUEST ['mobile_phone'];
	$sql = " select user_id from xbmall_users where mobile_phone='" . $mobile_phone . "'";
	$user_id = $db->getOne ( $sql );
	
	$sql = "INSERT INTO  xbmall_user_account (user_id,  amount,  add_time,  user_note, process_type, payment,  is_paid,  type,  fee, real_amount)
VALUES (" . $user_id . ", " . $amount . ", " . gmtime () . ", '线下已付款',0,'线下支付',0,0,0," . $amount . ")";
	
	$db->query ( $sql );
	$user_account_id = $db->insert_id ();
	
	$sql = "INSERT INTO  xbmall_pay_log(order_id, order_amount, order_type, is_paid, pay_info)  
VALUES (" . $user_account_id . ", " . $amount . ", 1 ,0, 0 )";
	$db->query ( $sql );
	die ( "支付id:" . $db->insert_id () );
} else if ($_REQUEST ['act'] == 'join') {//加入大家庭
	$db = $GLOBALS ['db'];
	$type = $_REQUEST ['type'];
	$mobile_phone = $_REQUEST ['mobile_phone'];
	$tj_mobile_phone= $_REQUEST ['tj_mobile_phone'];
	
	$sql = " select user_id from xbmall_users where mobile_phone='" . $mobile_phone . "'";
	$user_id = $db->getOne ( $sql );
	$sql = " select user_id from xbmall_users where mobile_phone='" . $tj_mobile_phone. "'";
	$tj_user_id = $db->getOne ( $sql );
	
	
	
	$sql = "update xbmall_users set djt_parent_id= ".$tj_user_id." where user_id=".$user_id;
	$db->query ( $sql );//设置大家庭的推荐人
	$family_config = array(
			'bigfamily_u_m' => $GLOBALS['_CFG']['bigfamily_u_m'],
			'bigfamily_d_m' => $GLOBALS['_CFG']['bigfamily_d_m'],
	);
	$amount = $type==1?$family_config['bigfamily_u_m']:$family_config['bigfamily_d_m'];
	$sql = "INSERT INTO  xbmall_pay_log(order_id, order_amount, order_type, is_paid, pay_info)
VALUES (" . $user_id. ", " . $amount . ", 4 ,0, 0 )";
	$db->query ( $sql );
	die ( "支付id:" . $db->insert_id () );
}

?>
