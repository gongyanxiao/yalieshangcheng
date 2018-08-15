<?php

/**
 * pc 端产品中心充值操作
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
assign_template();
setUserCenterValue();
$smarty->assign('act', $_REQUEST ['act']);
 
/* ------------------------------------------------------ */
// 列表
/* ------------------------------------------------------ */
if($_REQUEST ['act'] == 'list' || $_REQUEST ['act'] == ''){
	
	$recharge_list= get_product_account_log($_SESSION["user_id"]); 
	
	$smarty->assign('recharge_list', $recharge_list['account']); 
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
	$smarty->display('product_center.dwt');
}
elseif ($_REQUEST ['act'] == 'add') {
	//到充值页面
		$smarty = $GLOBALS['smarty']; 
		require_once(ROOT_PATH . 'includes/lib_order.php');
		$payment_list = available_payment_list(1, 0 ,true);
		$smarty->assign('payment_list', $payment_list);
		$smarty->display('product_center.dwt');
 
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
	

	// 获取支付方式名称
	$surplus['payment'] = $payment_info['pay_name'];
	
	if ($surplus['rec_id'] > 0) {
		// 更新会员账目明细, 更新金额, 支付方式,注释
		$surplus['rec_id'] = update_user_account($surplus);
	} else {
		// 插入会员账目明细状态为未支付
		$surplus['rec_id'] = insert_user_account($surplus, $amount,PAY_PRODUCT_SURPLUS);//账户变化的id
		 
		$referee_phone_num = $_POST['$referee_phone_num'];
	
		if($amount<10000){
			$points= 12000;//给的积分
		}else{
			$points= 24000;
		}
		if($referee_phone_num){
			$sql="select user_id from ".$ecs->table("users")." where mobile_phone='".$referee_phone_num."'";
		    $referee_user_id = $db->getOne($sql);
		    if($referee_user_id){//添加的号码是平台有的
		    	$sql=" insert into ".$GLOBALS['ecs']->table('product_points_recharge')."(acount_log_id,user_id,product_type,points,buy_points,referee_user_id,referee_phone_num,send_time)"
		    			."values(".$surplus['rec_id'].",".$surplus['user_id'].",".$amount.",".$points.",".$points.",".$referee_user_id.",".$referee_phone_num.",".gmtime().")";
		    			 
		    }else{
		    	$sql=" insert into ".$GLOBALS['ecs']->table('product_points_recharge')."(acount_log_id,user_id,product_type,points,buy_points,send_time)"
		    			."values(".$surplus['rec_id'].",".$surplus['user_id'].",".$amount.",".$points.",".$points."," .gmtime().")";
		    			
		    }
		}else {	
			$sql=" insert into ".$GLOBALS['ecs']->table('product_points_recharge')."(acount_log_id,user_id,product_type,points,buy_points,send_time)"
					."values(".$surplus['rec_id'].",".$surplus['user_id'].",".$amount.",".$points.",".$points."," .gmtime().")";
		}
		
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
// 	include_once(ROOT_PATH.'includes/modules/payment/zhizunbao.php');
// 	include_once(ROOT_PATH.'includes/modules/openepay/zhifutong.php');
// 	$pay_obj = new zhifutong();
	include_once('includes/modules/payment/zhizunbao.php');
	$pay_obj = new zhizunbao();
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
 
	$smarty->display('product_center.dwt');
	
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
	$smarty->display('product_center.dwt');
	
	
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
	$smarty->assign('account_log', $account_log['account']);
	$smarty->assign('pager', $pager);
	$smarty->assign('act', 'chan_pin_ji_fen_ti_xian_log');
	$smarty->display('product_center.dwt');
}elseif ($_REQUEST['act'] == "chan_pin_ji_fen_ti_xian"){
	
	$user = $GLOBALS['user'];
	$_CFG = $GLOBALS['_CFG'];
	$_LANG = $GLOBALS['_LANG'];
	$smarty = $GLOBALS['smarty'];
	$db = $GLOBALS['db'];
	$ecs = $GLOBALS['ecs'];
	$user_id = $_SESSION['user_id'];
	$smarty->assign('action', 'chan_pin_ji_fen_ti_xian');
	$sql = "select `real_name`,`card`,`bank`,`bank_kh`,`bank_num` from" . $ecs->table("users") . " where `user_id`='" . $user_id . "'";
	$profileRow = $db->getRow($sql);
	
	if (empty($profileRow["real_name"]) || empty($profileRow["card"]) || empty($profileRow["bank"]) || empty($profileRow['bank_kh']) || empty($profileRow['bank_num'])) {
		show_message("请将银行卡信息补充完整，才能继续进行积分兑换操作", "用户信息", 'user.php?act=profile');
	} else {
		$smarty->display('product_center.dwt');
	}
}elseif ($_REQUEST['act'] == "chan_pin_ji_fen_ti_xian_save"){
	
	$user = $GLOBALS['user'];
	$_CFG = $GLOBALS['_CFG'];
	$_LANG = $GLOBALS['_LANG'];
	$smarty = $GLOBALS['smarty'];
	$db = $GLOBALS['db'];
	$ecs = $GLOBALS['ecs'];
	$user_id = $_SESSION['user_id'];
	
	include_once (ROOT_PATH . 'includes/lib_clips.php');
	include_once (ROOT_PATH . 'includes/lib_order.php');
	$amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
	if ($amount <= 0) {
		show_message($_LANG['amount_gt_zero']);
	}
	
	
	 
	/* 变量初始化 */
	$surplus = array(
			'user_id' => $user_id, 'rec_id' => !empty($_POST['rec_id']) ? intval($_POST['rec_id']) : 0,
			'process_type' => 1, //提现
			'payment_id' => isset($_POST['payment_id']) ? intval($_POST['payment_id']) : 0,
			'user_note' => isset($_POST['user_note']) ? trim($_POST['user_note']) : '',
			'amount' => $amount,
			'fee' => $amount*0.01,
			'real_amount' => $amount*0.99
	);
	
	
	$payment_info = payment_info($surplus['payment_id']);//获取支付方式
	if ($payment_info['pay_code'] == 'alipay_bank') {
		$surplus['defaultbank'] = isset($_POST['com_bank']) ? trim($_POST['com_bank']) : '';
	}
	
	
	/* 判断是否有足够的余额的进行退款的操作 */
	$userDefault = get_user_default($user_id);
	$sur_amount = $userDefault["cp_points"] * 1;//产品积分
 
	if ( date("w") * 1 != 4) {
		$content = "提现日期为周四.";
		show_message($content, $_LANG['back_page_up'], '', 'info');
	}
	
	$hoursNow =  date("H")+8;
	if($hoursNow<7|| $hoursNow>16){
		show_message("提现时间为早8点到下午5点之间");
	}
	
	
	$min = 100;
 
	
	$sql = "SELECT add_time FROM " . $GLOBALS['ecs']->table("user_account") . " where user_id='" . $_SESSION["user_id"] . "' and process_type=1 order by add_time DESC limit 1 ";
	$add_time = $GLOBALS['db']->getOne($sql);
	$time = gmtime();
	if (!empty($add_time) && ($time < $add_time + 86400)) {
		show_message("您今天已经提过现了,请下次提现时间再进行操作");
	}
	
	
	if ($sur_amount < $min) {
		$content = "您现有的产品积分不足" . $min . "，此操作将不可进行！";
		show_message($content, $_LANG['back_page_up'], '', 'info');
	}
	
	
// 	$test = $amount / $min;
// 	if (ceil($test) !== $test) {
// 		$content = "您的提现必须是" . $min . "的整数倍，此操作将不可进行！";
// 		show_message($content, $_LANG['back_page_up'], '', 'info');
// 	}
	
	
	if ($amount > $sur_amount) {
		$content = "您要申请提现的积分超过了您现有的产品积分".$sur_amount."，此操作将不可进行！";
		show_message($content, $_LANG['back_page_up'], '', 'info');
	}
	
	
	
	
	
	// 插入会员账目明细
	$amount = '-' . $amount;//已经是负数了
	$surplus['payment'] = '';
	$GLOBALS['db']->startTrans();
	 
	$surplus['rec_id'] = insert_user_account($surplus, $amount, PAY_PRODUCT_SURPLUS);//产品积分提现
	
	$sql = "update  ".$ecs->table("users")." set cp_points=cp_points+".$amount."  where user_id=".$user_id;
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
	
	$filter['start_time'] = empty($_REQUEST['start_time']) ? '' : (strpos($_REQUEST['start_time'], '-') > 0 ? local_strtotime($_REQUEST['start_time']) : $_REQUEST['start_time']);
	$filter['end_time'] = empty($_REQUEST['end_time']) ? '' : (strpos($_REQUEST['end_time'], '-') > 0 ? local_strtotime($_REQUEST['end_time']) : $_REQUEST['end_time']);
	
	$filter['process_type'] = empty($_REQUEST['process_type']) ? '0' : '1';//要么查充值, 要么查提现
	
	$where = " WHERE a.user_id = '$user_id'  ";
	
	if ($filter['start_time']) {
		$where .= " AND a.add_time >= '$filter[start_time]'";
	}
	if ($filter['end_time']) {
		$where .= " AND a.add_time <= '$filter[end_time]'";
	}
	$where .= " and a.type ='".$type."' ";
	
 
	$where .= " and a.process_type ='".$filter['process_type']."' ";
	 

	
	
	/* 查询记录总数，计算分页数 */
	if($filter['process_type']){//提现记录
		$sql = "SELECT COUNT(0) FROM " . $GLOBALS['ecs']->table('user_account') . " a ".$where;
		
	}else{//充值记录
		$sql = "SELECT COUNT(0) FROM " . $GLOBALS['ecs']->table('user_account') . " a join " . $GLOBALS['ecs']->table('product_points_recharge'). " b on b.acount_log_id=a.id ".$where;
		
	}
	
	$filter['record_count'] = $GLOBALS['db']->getOne($sql);
	$filter = page_and_size($filter);
	
	if($filter['process_type']){//提现记录
		$sql = "SELECT a.* FROM " . $GLOBALS['ecs']->table('user_account') . " a ".$where." ORDER BY a.add_time DESC";
		
	}else{//充值记录
		$sql = "SELECT a.*, b.points,b.referee_phone_num ,b.buy_points FROM " . $GLOBALS['ecs']->table('user_account') . " a join " . $GLOBALS['ecs']->table('product_points_recharge'). " b on b.acount_log_id=a.id ".$where." ORDER BY a.add_time DESC";
		
	}
	
 
 
	$res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);
	
	if ($res) {
		while ($rows = $GLOBALS['db']->fetchRow($res)) {	
			$rows['add_time'] = local_date('Y-m-d H:i', $rows['add_time']);
			$rows['admin_note'] = nl2br(htmlspecialchars($rows['admin_note']));
			$rows['short_admin_note'] = ($rows['admin_note'] > '') ? sub_str($rows['admin_note'], 30) : 'N/A';
			$rows['user_note'] = nl2br(htmlspecialchars($rows['user_note']));
			$rows['short_user_note'] = ($rows['user_note'] > '') ? sub_str($rows['user_note'], 30) : 'N/A';
			$rows['pay_status'] = ($rows['is_paid'] == 0) ? $GLOBALS['_LANG']['un_confirm'] : $GLOBALS['_LANG']['is_confirm'];
			$rows['amount'] = price_format(abs($rows['amount']), false);
			$rows['fee'] = price_format(abs($rows['fee']), false);
			$rows['real_amount'] = price_format(abs($rows['real_amount']), false);
			 
			 
			/* 会员的操作类型： 冲值，提现 */
			if ($rows['process_type'] == 0) {
				$rows['type'] = $GLOBALS['_LANG']['surplus_type_0'];
			} else {
				$rows['type'] = $GLOBALS['_LANG']['surplus_type_1'];
			}
			
			/* 支付方式的ID */
			$sql = 'SELECT pay_id FROM ' . $GLOBALS['ecs']->table('payment') .
			" WHERE pay_name = '$rows[payment]' AND enabled = 1";
			$pid = $GLOBALS['db']->getOne($sql);
			
			/* 如果是预付款而且还没有付款, 允许付款 */
			if (($rows['is_paid'] == 0) && ($rows['process_type'] == 0)) {
				$rows['handle'] = '<a href="user.php?act=pay&id=' . $rows['id'] . '&pid=' . $pid . '">' . $GLOBALS['_LANG']['pay'] . '</a>';
				$rows['pay_id'] = $pid;
			}
			
			$account_log[] = $rows;
		}
		
		return array('account' => $account_log, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
	} else {
		return false;
	}
}

/**
 * 分页的信息加入条件的数组
 *
 * @access  public
 * @return  array
 */
function page_and_size($filter)
{
	if (isset($_REQUEST['page_size']) && intval($_REQUEST['page_size']) > 0)
	{
		$filter['page_size'] = intval($_REQUEST['page_size']);
	}
	elseif (isset($_COOKIE['ECSCP']['page_size']) && intval($_COOKIE['ECSCP']['page_size']) > 0)
	{
		$filter['page_size'] = intval($_COOKIE['ECSCP']['page_size']);
	}
	else
	{
		$filter['page_size'] = 10;
	}
	 
	/* 每页显示 */
	$filter['page'] = (empty($_REQUEST['page']) || intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);
	
	/* page 总数 */
	$filter['page_count'] = (!empty($filter['record_count']) && $filter['record_count'] > 0) ? ceil($filter['record_count'] / $filter['page_size']) : 1;
	
	/* 边界处理 */
	if ($filter['page'] > $filter['page_count'])
	{
		$filter['page'] = $filter['page_count'];
	}
	 
	$filter['start'] = ($filter['page'] - 1) * $filter['page_size'];
 
	return $filter;
}


function  setUserCenterValue(){
	
	$smarty = $GLOBALS['smarty'];
	$db = $GLOBALS['db'];
	$ecs = $GLOBALS['ecs'];
	$user_id = $_SESSION['user_id'];
	$smarty->assign('info', get_user_default($user_id)); // 获取用户中心默认页面所需的数据
	$sql="SELECT user_id  FROM ".$ecs->table('supplier_admin_user')." where uid=".$user_id;
	$supplierUserId =  $db->getOne($sql);
	$smarty->assign('supplierUserId', $supplierUserId);// 商家/运营中心用户的id
	
}
 
?>