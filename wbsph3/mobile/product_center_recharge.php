<?php

/**
 * 产品中心充值操作
 * ============================================================================
 */
define ( 'IN_ECS', true );

require (dirname ( __FILE__ ) . '/includes/init.php');
$_LANG = $GLOBALS['_LANG'];
 
if (empty ( $_SESSION ['user_id'] )) {//没有登录
	show_message ( $_LANG ['require_login'], array (
			'</br>登录',
			'</br>返回首页'
	), array (
			'user.php?act=login',
			$ecs->url ()
	), 'error', false );
}	
include_once(dirname ( __FILE__ ) . '/includes/lib_clips.php');
 
$userInfo = get_user_default($_SESSION ['user_id']);//获取用户信息


$smarty = $GLOBALS['smarty'];
$db = $GLOBALS['db'];
$ecs = $GLOBALS['ecs'];

/* ------------------------------------------------------ */
// 列表
/* ------------------------------------------------------ */
if($_REQUEST ['act'] == 'list' || $_REQUEST ['act'] == ''){
	
	$recharge_list= get_product_account_log($_SESSION["user_id"]); 

	$smarty->assign('recharge_list', $recharge_list['list']); 
	$smarty->assign('filter', $recharge_list['filter']);
	$smarty->assign('record_count', $recharge_list['record_count']);
	$smarty->assign('page_count', $recharge_list['page_count']);
 
	$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
	// 分页函数
	$pager = get_pager('product_center_recharge.php', array(
			'act' => 'list'
	), $recharge_list['record_count'], $page);
	// 	$smarty->display('product/recharge_list.dwt');
	$smarty->assign('pager', $pager);
	
	$smarty->display('product/recharge_list.dwt');
	
}
elseif ($_REQUEST ['act'] == 'add') {
	//到充值页面
		$smarty = $GLOBALS['smarty']; 
		require_once(ROOT_PATH . 'includes/lib_order.php');
		$payment_list = available_payment_list(1, 0 ,true);
		$smarty->assign('payment_list', $payment_list);
		$smarty->display('product/chong_zhi.dwt');
 
}
elseif ($_REQUEST ['act'] == 'insert'){
	$smarty = $GLOBALS['smarty'];
	include_once (ROOT_PATH . 'includes/lib_clips.php');
	include_once (ROOT_PATH . 'includes/lib_order.php');
	$amount= isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
 
	if ($amount<= 1) {
		sys_msg("充值金额不能小于1");
	}
	
	/* 变量初始化 */
	$surplus = array(
			'user_id' => $_SESSION["user_id"],
			'payment_id' => isset($_POST['payment_id']) ? intval($_POST['payment_id']) : 0,
			'user_note' => isset($_POST['user_note']) ? trim($_POST['user_note']) : '',
			'amount' => $amount,
			'type'=>PAY_PRODUCT_SURPLUS,
			'process_type'=>0//充值
	);
	
	$payment_info = payment_info($surplus['payment_id']);
	if ($payment_info['pay_code'] == 'alipay_bank') {
		$surplus['defaultbank'] = isset($_POST['com_bank']) ? trim($_POST['com_bank']) : '';
	}
	if ($surplus['payment_id'] <= 0) {
		sys_msg("支付方式不合法，请选择支付方式");
	}
	$referee_phone_num = $_POST['referee_phone_num'];
	$sql="select user_id , is_bigfamily from ".$ecs->table("users")." where mobile_phone='".$referee_phone_num."'";
	$tuiJianRen = $db->getRow($sql);
	if($tuiJianRen==null || $tuiJianRen['is_bigfamily']<=0){//商家和代理可以填写自己是推荐人
		sys_msg("必须填写合适的推荐人");
	}
	
	// 获取支付方式名称
	$surplus['payment'] = $payment_info['pay_name'];
	
	if ($surplus['rec_id'] > 0) {
		// 更新会员账目明细, 更新金额, 支付方式,注释
		$surplus['rec_id'] = update_user_account($surplus);
	} else {
		// 插入会员账目明细状态为未支付
		$surplus['rec_id'] = insert_user_account($surplus, $amount,PAY_PRODUCT_SURPLUS);//账户变化的id
		
		if($amount==500){
			$points  = 540;
		}
		if($amount==5000){
			$points  = 5400;
		}
		if($amount==10000){
			$points  = 10800;
		}
		if($amount==20000){
			$points  = 21600;
		}
		if($amount==50000){
			$points  = 54000;
		}
		 
        $sql=" insert into ".$GLOBALS['ecs']->table('product_points_recharge')."(acount_log_id,user_id,product_type,points,referee_user_id,referee_phone_num,send_time)"
        		."values(".$surplus['rec_id'].",".$surplus['user_id'].",".$amount.",".$points.",".$tuiJianRen['user_id'].",".$referee_phone_num.",".gmtime().")";
				
		$db->query($sql);
		
	}

	// 取得支付信息，生成支付代码
	$payment = unserialize_config($payment_info['pay_config']);
	 
	// 生成伪订单号, 不足的时候补0
	$order = array();
	$order['user_id'] = $_SESSION["user_id"];
	$order['order_sn'] = $surplus['rec_id'];
	$order['user_name'] = $_SESSION['user_name'];
	$order['surplus_amount'] = $amount;
	$order['defaultbank'] = $surplus['defaultbank'];
	 
	// 计算此次预付款需要支付的总金额
	$order['order_amount'] = $amount    ;
 
	// 记录支付log
	$order['log_id'] = insert_pay_log($surplus['rec_id'], $order['order_amount'], $type = PAY_PRODUCT_SURPLUS, 0);
	
	 
	//重新生成pay_log
	if($surplus['payment_id']==16 || $surplus['payment_id']==17 || $surplus['payment_id']==18 ){
		include_once(ROOT_PATH.'includes/modules/openepay/zhifutong.php');
		$pay_obj = new zhifutong();
	}else{
		include_once('includes/modules/payment/zhizunbao.php');
		$pay_obj = new zhizunbao();
	}
	
	$orderUrl = $GLOBALS['ecs']->url();
	$order_pay=array(
			'order_amount' =>   $order['order_amount'] ,
			'user_id' => $order['user_id'],
			'log_id' => $order['log_id'],
			'order_id' => $order['order_sn'],
	);
	
	$pay_online = $pay_obj->get_code($order_pay, $payment_info['pay_code'], $orderUrl . "/product_center_recharge.php",'MP');
	 
	$pay_online = "<div style='margin-top:10px'>".$pay_online.'</div>';
	$smarty->assign('pay_button', $pay_online);

	$smarty->assign('site_domain', $GLOBALS['ecs']->get_domain());
	
	/* 模板赋值 */
	$smarty->assign('payment', $payment_info);
	$smarty->assign('pay_fee', price_format($payment_info['pay_fee'], false));
	$smarty->assign('amount', price_format($amount, false));
	$smarty->assign('order', $order);	 
	$smarty->display("product/chong_zhi_zhi_fu.dwt");
	
}elseif ($_REQUEST['act'] == "fa_fang_ji_lu") {
	
	
	$sql="select count(0) from " . $GLOBALS['ecs']->table('product_points_log') . " where user_id=".$_SESSION["user_id"];

	$filter['record_count'] = $GLOBALS['db']->getOne($sql);
	$filter = page_and_size($filter);
	
	$sql="select * from " . $GLOBALS['ecs']->table('product_points_log') . " where user_id=".$_SESSION["user_id"];
	
	
	$res = $GLOBALS['db']->selectLimit($sql, 15, $filter['start']);
	
	
	if ($res) {
		while ($rows = $GLOBALS['db']->fetchRow($res)) {
			$rows['log_time'] = local_date('Y-m-d H:i', $rows['log_time']);
			$recharge_list[] = $rows;
		}
	}	
		
 
	
	$smarty->assign('account_log', $recharge_list);
	$smarty->assign('filter', $filter);
	$smarty->assign('record_count', $filter['record_count']);
	$smarty->assign('page_count', $filter['page_count']);
	$smarty->display('product/chan_pin_ji_fen_fa_fang_ji_lu.dwt');
	
	
	
}elseif ($_REQUEST['act'] == "cancel") {
	
	$user = $GLOBALS['user'];
	$_CFG = $GLOBALS['_CFG'];
	$_LANG = $GLOBALS['_LANG'];
	$smarty = $GLOBALS['smarty'];
	$db = $GLOBALS['db'];
	$ecs = $GLOBALS['ecs'];
	$user_id = $_SESSION['user_id'];
	
	include_once (ROOT_PATH . 'includes/lib_clips.php');
	
	$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
	if ($id == 0 || $user_id == 0) {
		ecs_header("Location: product_center_recharge.php?act=chan_pin_ji_fen_ti_xian_log\n");
		exit();
	}
	
	
	$GLOBALS['db']->startTrans();
	$account_order = $GLOBALS['db']->getRow('SELECT *  FROM ' . $GLOBALS['ecs']->table('user_account') .
			" WHERE is_paid = 0 AND id = '$id' AND user_id = '$user_id' for update ");
	$result = del_user_account($id, $user_id);
	if ($result) {
		$amount = $account_order['amount'];
		$sql = "update  ".$ecs->table("users")." set cp_points=cp_points-".$amount."  where user_id=".$user_id;//负负得正
		$GLOBALS['db']->query($sql);
		$GLOBALS['db']->commitTrans();
		ecs_header("Location: product_center_recharge.php?act=chan_pin_ji_fen_ti_xian_log\n");
		exit();
		 
	}
	$GLOBALS['db']->rollbackTrans();
}
elseif ($_REQUEST['act'] == "chan_pin_ji_fen_ti_xian_log"){
	
	$user = $GLOBALS['user'];
	$_CFG = $GLOBALS['_CFG'];
	$_LANG = $GLOBALS['_LANG'];
	$smarty = $GLOBALS['smarty'];
	$db = $GLOBALS['db'];
	$ecs = $GLOBALS['ecs'];
	$user_id = $_SESSION['user_id'];
	 
	$ti_xian_lei_xing = PAY_PRODUCT_SURPLUS;
	$process_type = 1;
	
	include_once (ROOT_PATH . 'includes/lib_clips.php');
	
	$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
	
	/* 获取记录条数 */
	$sql = "SELECT COUNT(*) FROM " . $ecs->table('user_account') . " WHERE user_id = '$user_id'" . " AND type =".$ti_xian_lei_xing." and process_type=$process_type";
	

	$record_count = $db->getOne($sql);
	
	// 分页函数
	$pager = get_pager('user.php', array(
			'act' => $action
	), $record_count, $page);
	/* /查看账户明细页面 获取会员用户的余额 jx 2015-1-1 */
	 
	$_REQUEST['process_type']=1;//提现查询
	$account_log = get_product_account_log($_SESSION["user_id"]); // 获取提现充值记录
	
	 
	// 模板赋值
	//$smarty->assign('surplus_amount',$surplus_amount);
	$smarty->assign('account_log', $account_log['list']);
	$smarty->assign('pager', $pager);
	$smarty->assign('action', "chan_pin_ji_fen_ti_xian_log");
	$smarty->display('user_transaction.dwt');
	
}elseif ($_REQUEST['act'] == "chan_pin_ji_fen_ti_xian"){
	
	$user = $GLOBALS['user'];
	$_CFG = $GLOBALS['_CFG'];
	$_LANG = $GLOBALS['_LANG'];
	$smarty = $GLOBALS['smarty'];
	$db = $GLOBALS['db'];
	$ecs = $GLOBALS['ecs'];
	$user_id = $_SESSION['user_id'];
	$id=$_REQUEST['id'];
	$sql="select * from xbmall_product_points_recharge where id=".$id;
	$row = $db->getRow($sql);
	$smarty->assign('bean', $row);
	
	$sql="select  is_bigfamily from  xbmall_users where user_id=".$user_id;
	$is_bigfamily = $db->getOne($sql);
	
	$rate = 0;
	if($is_bigfamily==0){
		$rate = 12;
	} 
	$smarty->assign('rate', $rate);//手续费
	$smarty->assign('action', 'chan_pin_ji_fen_ti_xian');
	$sql = "select `real_name`,`card`,`bank`,`bank_kh`,`bank_num` from" . $ecs->table("users") . " where `user_id`='" . $user_id . "'";
	$profileRow = $db->getRow($sql);
	
	if (empty($profileRow["real_name"]) || empty($profileRow["card"]) || empty($profileRow["bank"]) || empty($profileRow['bank_kh']) || empty($profileRow['bank_num'])) {
		show_message("请将银行卡信息补充完整，才能继续进行积分兑换操作", "用户信息", 'user.php?act=profile');
	} else {
		$smarty->display('user_transaction.dwt');
	}
}elseif ($_REQUEST['act'] == "chan_pin_ji_fen_ti_xian_save"){
	
	$user = $GLOBALS['user'];
	$_CFG = $GLOBALS['_CFG'];
	$_LANG = $GLOBALS['_LANG'];
	$smarty = $GLOBALS['smarty'];
	$db = $GLOBALS['db'];
	$ecs = $GLOBALS['ecs'];
	$user_id = $_SESSION['user_id'];
	

	$id=$_POST['id'];
	$sql="select * from xbmall_product_points_recharge where id=".$id;
	$row = $db->getRow($sql);
	

	if ($row==null) {
		show_message("提现失败, 请重新申请");
	}
	$amount = $row['points'];
	
	include_once (ROOT_PATH . 'includes/lib_clips.php');
	include_once (ROOT_PATH . 'includes/lib_order.php');
	
	$sql="select  is_bigfamily from  xbmall_users where user_id=".$user_id;
	$is_bigfamily = $db->getOne($sql);
	
	$rate = 0;
	if($is_bigfamily==0){
		$rate = 0.12;
	} 
	
	
	
	/* 变量初始化 */
	$surplus = array(
			'user_id' => $user_id, 'rec_id' => !empty($_POST['rec_id']) ? intval($_POST['rec_id']) : 0,
			'process_type' => 1, //提现
			'payment_id' => isset($_POST['payment_id']) ? intval($_POST['payment_id']) : 0,
			'user_note' => isset($_POST['user_note']) ? trim($_POST['user_note']) : '',
			'amount' => $amount,
			'fee' => $amount*$rate,
			'real_amount' => $amount*(1-$rate)
	);
	
	$payment_info = payment_info($surplus['payment_id']);//获取支付方式
	if ($payment_info['pay_code'] == 'alipay_bank') {
		$surplus['defaultbank'] = isset($_POST['com_bank']) ? trim($_POST['com_bank']) : '';
	}
	

	
	
	/* 判断是否有足够的余额的进行退款的操作 */
	$userDefault = get_user_default($user_id);
	$sur_amount = $userDefault["cp_points"] * 1;//产品积分
 
	if (date("w") * 1 != 4) {
		$content = "提现日期为周四.";
		show_message($content, $_LANG['back_page_up'], '', 'info');
	}
	
	$hoursNow =  date("H")+8;
	if($hoursNow<7|| $hoursNow>16){
		show_message("提现时间为早8点到下午5点之间");
	}
	
	
	
	 
	
// 	$sql = "SELECT add_time FROM " . $GLOBALS['ecs']->table("user_account") . " where user_id='" . $_SESSION["user_id"] . "' and process_type=1 and type=".PAY_PRODUCT_SURPLUS." order by add_time DESC limit 1 ";
// 	$add_time = $GLOBALS['db']->getOne($sql);
// 	$time = gmtime();
// 	if (!empty($add_time) && ($time < $add_time + 86400)) {
// 		sys_msg("您今天已经提过现了,请下次提现时间再进行操作");
// 	}
	
	
	 

	// 插入会员账目明细
	$amount = '-' . $amount;//已经是负数了
	$surplus['payment'] = '';
	$GLOBALS['db']->startTrans();
	 
	$surplus['rec_id'] = insert_user_account($surplus, $amount, PAY_PRODUCT_SURPLUS);//产品积分提现
	
	
	//记录提现记录id
	$sql = "update  ".$ecs->table("product_points_recharge")." set ti_xian_acount_log_id=".$surplus['rec_id']."  where id=".$id;
	$GLOBALS['db']->query($sql);
	/* 如果成功提交 */
	if ($surplus['rec_id'] > 0) {
		 
		$GLOBALS['db']->commitTrans();
		show_message("产品积分提现申请成功", "返回提现记录", 'product_center_recharge.php?act=chan_pin_ji_fen_ti_xian_log', 'info');
	} else {
		$GLOBALS['db']->rollbackTrans();
		$content = $_LANG['process_false'];
		show_message($content, $_LANG['back_page_up'], '', 'info');
	}
	exit();
	
}




/**
 * 查询会员资金变动记录(产品积分)
 *
 * @access  public
 * @param   int     $user_id    会员ID
 * @param   int     $num        每页显示数量
 * @param   int     $start      开始显示的条数
 * @return  array
 */
function get_product_account_log($user_id, $type=PAY_PRODUCT_SURPLUS) {
	$account_log = array();
	/* 初始化分页参数 */
	$filter = array(
			'user_id' => $user_id,
	);
	$filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'add_time' : trim($_REQUEST['sort_by']);
	$filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
 
	$where = " WHERE user_id = '$user_id'  ";
	
 
    $sql = "SELECT COUNT(0) FROM " . $GLOBALS['ecs']->table('product_points_recharge') . "   ".$where;
	 
	
	$filter['record_count'] = $GLOBALS['db']->getOne($sql);
	$filter = page_and_size($filter);
	
	 
	 $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('product_points_recharge') . " ".$where." ORDER BY  send_time DESC";
	 
	
	$res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);
	
	if ($res) {
		$now = gmtime();
		while ($rows = $GLOBALS['db']->fetchRow($res)) {
			
			if( strtotime("+1 months", $rows['send_time'])<$now){//冻结日期小于现在的时间, 那么是可以提现的
				$rows['is_can_ti_xian']=1;
			}else {
				$rows['is_can_ti_xian']=0;
			}
			
			
			$rows['end_time'] = local_date('Y-m-d H:i', strtotime("+1 months", $rows['send_time']) );
			$rows['send_time'] = local_date('Y-m-d H:i', $rows['send_time']);
			
			if($rows['ti_xian_acount_log_id']){//有提现日志
			
				// 提现记录的提现状态
				$sql="select is_paid, add_time from ". $GLOBALS['ecs']->table('user_account')." where user_id=".$user_id." and id=".$rows['ti_xian_acount_log_id'];
				$isTiXian = $GLOBALS['db']->getRow($sql);
				if($isTiXian['is_paid']==0 && $rows['end_time']<$now){//冻结日期小于现在的时间, 那么是可以提现的
					$rows['is_can_ti_xian']=1;
				}else {//已经提过现了
					$rows['is_can_ti_xian']=2;
					if($rows['ti_xian_time']!=null){
						$rows['ti_xian_time'] = local_date('Y-m-d H:i', $isTiXian['add_time']);
					}
				}
			} 
			
			 
			if($rows['is_paid']==0){
				$rows['is_paid'] ="未付款";
			}else{
				$rows['is_paid'] ="已付款";
			}
			$account_log[] = $rows;
			
		}
		
		
		return array('list' => $account_log, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
	} else {
		return false;
	}
}


 
?>