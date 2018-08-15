<?php

/**
 * 商家中心的部分操作
 * ============================================================================
 */
define ( 'IN_ECS', true );

require (dirname ( __FILE__ ) . '/includes/init.php');

if (empty ( $_SESSION ['user_id'] )) {//没有登录,登录并且有店铺
	show_message ( $_LANG ['require_login'], array (
			'</br>登录',
			'</br>返回首页'
	), array (
			'user.php?act=login',
			$ecs->url ()
	), 'error', false );
}


/* ------------------------------------------------------ */
// -货款兑换
/* ------------------------------------------------------ */
if($_REQUEST ['act'] == 'list'){
	
	$smarty->assign('full_page', 1);
	$smarty->assign('ur_here', "货款积分兑换记录");
	$smarty->assign('action_link', array('text' => "货款积分兑换", 'href' => 'supplier_m_center.php?act=to_huo_kuan_dui_huan'));
	$type=isset($_REQUEST['type'])?$_REQUEST['type']:0;//积分货款还是扫码货款
	$account_list = get_exchange_account_log($_SESSION["user_id"],$type);
	$smarty->assign('account_list', $account_list['account']);
	$smarty->assign('filter', $account_list['filter']);
	$smarty->assign('record_count', $account_list['record_count']);
	$smarty->assign('page_count', $account_list['page_count']);
	$smarty->display('supplier/huo_kuan_dui_huan_list.dwt');
	
}elseif ($_REQUEST ['act'] == 'fen_xiang_dui_huan_list') {
	
	$smarty->assign('full_page', 1);
	$smarty->assign('ur_here', "分享奖励积分兑换记录");
	$smarty->assign('action_link', array('text' => "分享奖励积分兑换", 'href' => 'supplier_m_center.php?act=to_huo_kuan_dui_huan'));
	
	$account_list = get_exchange_account_log($_SESSION["user_id"],0);
	$smarty->assign('account_list', $account_list['account']);
	$smarty->assign('filter', $account_list['filter']);
	$smarty->assign('record_count', $account_list['record_count']);
	$smarty->assign('page_count', $account_list['page_count']);
	$smarty->display('supplier/fen_xiang_dui_huan_list.dwt');
	
	
}
elseif ($_REQUEST ['act'] == 'to_huo_kuan_dui_huan') {
	$smarty = $GLOBALS ['smarty'];
	
	$sql = "select `real_name`,`card`,`bank`,`bank_kh`,`bank_num` from" . $GLOBALS['ecs']->table("users") . " where `user_id`='" . $_SESSION["user_id"] . "'";
	$profileRow = $db->getRow($sql);
	if (empty($profileRow["real_name"]) || empty($profileRow["card"]) || empty($profileRow["bank"]) || empty($profileRow['bank_kh']) || empty($profileRow['bank_num'])) {
		$links = array(
				array('href' => 'user.php?act=identity', 'text' => "个人信息")
		);
		sys_msg("请将个人信息补充完整，才能继续进行积分兑换操作", 0, $links);
	} else {//查询最近的一次货款提现的时间
		$sql = "SELECT add_time FROM " . $GLOBALS['ecs']->table("user_account") . " where user_id='" . $_SESSION["user_id"] . "' and process_type=1 and type=0 order by add_time DESC limit 1 ";
		$add_time = $GLOBALS['db']->getOne($sql);
		$time = gmtime();
		if (!empty($add_time) && ($time < $add_time + 86400)) {
			sys_msg("您今天已经提过现了,请下次提现时间再进行操作");
		}
		assign_query_info();
		$smarty->display('supplier/huo_kuan_dui_huan.dwt');
	}
	
}elseif ($_REQUEST ['act'] == 'to_sao_ma_huo_kuan_dui_huan') {
	$smarty = $GLOBALS ['smarty'];
	
	$sql = "select `real_name`,`card`,`bank`,`bank_kh`,`bank_num` from" . $GLOBALS['ecs']->table("users") . " where `user_id`='" . $_SESSION["user_id"] . "'";
	$profileRow = $db->getRow($sql);
	if (empty($profileRow["real_name"]) || empty($profileRow["card"]) || empty($profileRow["bank"]) || empty($profileRow['bank_kh']) || empty($profileRow['bank_num'])) {
		$links = array(
				array('href' => 'user.php?act=identity', 'text' => "个人信息")
		);
		sys_msg("请将个人信息补充完整，才能继续进行积分兑换操作", 0, $links);
	} else {//查询最近的一次货款提现的时间
		$sql = "SELECT add_time FROM " . $GLOBALS['ecs']->table("user_account") . " where user_id='" . $_SESSION["user_id"] . "' and process_type=1 and type=0 order by add_time DESC limit 1 ";
		$add_time = $GLOBALS['db']->getOne($sql);
		$time = gmtime();
// 		if (!empty($add_time) && ($time < $add_time + 86400)) {
// 			sys_msg("您今天已经提过现了,请下次提现时间再进行操作");
// 		}
		assign_query_info();
		$smarty->display('supplier/huo_kuan_dui_huan_sao_ma.dwt');
	}
	
}
elseif ($_REQUEST ['act'] == 'to_fen_xiang_dui_huan') {//分享积分兑换
	$smarty = $GLOBALS ['smarty'];
	
	$sql = "select `real_name`,`card`,`bank`,`bank_kh`,`bank_num` from" . $GLOBALS['ecs']->table("users") . " where `user_id`='" . $_SESSION["user_id"] . "'";
	$profileRow = $db->getRow($sql);
	if (empty($profileRow["real_name"]) || empty($profileRow["card"]) || empty($profileRow["bank"]) || empty($profileRow['bank_kh']) || empty($profileRow['bank_num'])) {
		$links = array(
				array('href' => 'user.php?act=identity', 'text' => "个人信息")
		);
		sys_msg("请将个人信息补充完整，才能继续进行积分兑换操作", 0, $links);
	} else {//查询最近的一次分享积分提现的时间
		$sql = "SELECT add_time FROM " . $GLOBALS['ecs']->table("user_account") . " where user_id='" . $_SESSION["user_id"] . "' and process_type=1 and type=".ACT_DRAWING_FEN_XIANG_JI_FEN." order by add_time DESC limit 1 ";
		$add_time = $GLOBALS['db']->getOne($sql);
		$time = gmtime();
		if (!empty($add_time) && ($time < $add_time + 86400)) {
			sys_msg("您今天分享积分已经提过现了,请下次提现时间再进行操作");
		}
		assign_query_info();
		$smarty->display('supplier/fen_xiang_dui_huan.dwt');
	}
	
}
elseif ($_REQUEST ['act'] == 'huo_kuan_dui_huan'){
	
	//周:提交积分兑换申请
	include_once (ROOT_PATH . 'includes/lib_clips.php');
	include_once (ROOT_PATH . 'includes/lib_order.php');
	$amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
	if ($amount <= 0) {
		sys_msg("积分兑换积分必须大于零");
	}
	/* 变量初始化 */
	$surplus = array(
			'user_id' => $_SESSION["user_id"], 'rec_id' => !empty($_POST['rec_id']) ? intval($_POST['rec_id']) : 0, 'process_type' => isset($_POST['surplus_type']) ? intval($_POST['surplus_type']) : 0, 'payment_id' => isset($_POST['payment_id']) ? intval($_POST['payment_id']) : 0, 'user_note' => isset($_POST['user_note']) ? trim($_POST['user_note']) : '', 'amount' => $amount
	);
	/* 判断是否有足够的余额的进行退款的操作 */
	$userDefault = get_user_default($_SESSION["user_id"]);
	$sur_amount = $userDefault["hk_points"] * 1;
	
	
	$weekDay = date("w");
	if(!($weekDay==4)){
		sys_msg("提现日期为周四");
	}
	
	$hoursNow =  date("H")+8;
	if($hoursNow<8|| $hoursNow>16){
		sys_msg("提现时间为每周四早8点到下午5点");
	}
	
	
	$sql = "SELECT add_time FROM " . $GLOBALS['ecs']->table("user_account") . " where user_id='" . $_SESSION["user_id"] . "' and process_type=1 and type=0  order by add_time DESC limit 1 ";
	
	$add_time = $GLOBALS['db']->getOne($sql);
	$time = gmtime();
	if (!empty($add_time) && ($time < $add_time + 86400)) {
		sys_msg("您今天已经货款提过现了,请下次提现时间再进行操作");
	}
	$min = 100;
	if ($sur_amount < $min) {
		$content = "您现有的货款积分不足" . $min . "，此操作将不可进行！";
		sys_msg($content);
	}
	
// 	if ($amount <$min) {
// 		$content = "最少提现金额是100元！";
// 		sys_msg($content);
// 	}

	if ($amount > $sur_amount) {
		$content = "您要申请提现的积分超过了您现有的货款积分，此操作将不可进行！";
		sys_msg($content);
	}
	// 插入会员账目明细
	$amount = '-' . $amount;
	$surplus['payment'] = '';
	$GLOBALS['db']->startTrans();
	$surplus['rec_id'] = insert_user_account($surplus, $amount);
	
	/* 如果成功提交 */
	if ($surplus['rec_id'] > 0) {
		//$user_id, $user_money = 0, $frozen_money = 0, $rank_points = 0, $pay_points = 0, $change_desc = '', $change_type = ACT_OTHER, $consum_money = 0, $give_points = 0, $tx_points = 0, $history_zsq = 0, $zsq = 0, $frozen_points = 0, $day_points = 0, $hk_points = 0, $frozen_hk_points = 0, $yl_points = 0, $jljs_points = 0
		$res1 = log_account_change($_SESSION["user_id"], 0, 0, 0, 0, "商家MP端发起积分兑换操作", 1, 0, 0, 0, 0, 0, 0, 0, $amount, $amount * (-1), 0, 0);
		if ($res1) {
			$content = "操作成功,等待审核。";
			$GLOBALS['db']->commitTrans();
			$links = array(
					array('href' => 'user.php?act=supplier_center', 'text' => "商家中心")
			);
			sys_msg($content, 0, $links);
		} else {
			$GLOBALS['db']->rollbackTrans();
			$content = "积分兑换申请失败";
			show_message($content);
		}
	} else {
		$GLOBALS['db']->rollbackTrans();
		$content = "积分兑换申请失败";
		show_message($content);
	}
	
}elseif ($_REQUEST ['act'] == 'sao_ma_huo_kuan_dui_huan'){
	
	//周:提交扫码货款积分兑换申请
	include_once (ROOT_PATH . 'includes/lib_clips.php');
	include_once (ROOT_PATH . 'includes/lib_order.php');
	$amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
	if ($amount <= 0) {
		sys_msg("积分兑换积分必须大于零");
	}
	/* 变量初始化 */
	$surplus = array(
			'user_id' => $_SESSION["user_id"], 
			'rec_id' => !empty($_POST['rec_id']) ? intval($_POST['rec_id']) : 0,
			'process_type' => isset($_POST['surplus_type']) ? intval($_POST['surplus_type']) : 0,
			'payment_id' => isset($_POST['payment_id']) ? intval($_POST['payment_id']) : 0, 
			'user_note' => isset($_POST['user_note']) ? trim($_POST['user_note']) : '', 
			'amount' => $amount
	);
	/* 判断是否有足够的余额的进行退款的操作 */
	$userDefault = get_user_default($_SESSION["user_id"]);
	$sur_amount = $userDefault["hk_xf_points"] * 1;
	
	
 
	
	$hoursNow =  date("H")+8;
	if($hoursNow<8|| $hoursNow>11){
		sys_msg("提现时间为每天早8点到中午12点之间");
	}
	
// 	$sql = "SELECT add_time FROM " . $GLOBALS['ecs']->table("user_account") . " where user_id='" . $_SESSION["user_id"] . "' and process_type=1 and type=0  order by add_time DESC limit 1 ";
// 	$add_time = $GLOBALS['db']->getOne($sql);
// 	$time = gmtime();
// 	if (!empty($add_time) && ($time < $add_time + 86400)) {
// 		sys_msg("您今天已经货款提过现了,请下次提现时间再进行操作");
// 	}
	
	
	$min = 0;
	if ($sur_amount < $min) {
		$content = "您现有的扫码货款积分不足" . $min . "，此操作将不可进行！";
		sys_msg($content);
	}
	
	if ($amount <$min) {
		$content = "最少提现金额是0元！";
		sys_msg($content);
	}
	if ($amount > $sur_amount) {
		$content = "您要申请提现的积分超过了您现有的货款积分，此操作将不可进行！";
		sys_msg($content);
	}
	// 插入会员账目明细
	$amount = '-' . $amount;
	$surplus['payment'] = '';
	$GLOBALS['db']->startTrans();
	$surplus['rec_id'] = insert_user_account($surplus, $amount,10);
	
	/* 如果成功提交 */
	if ($surplus['rec_id'] > 0) {
		//$user_id, $user_money = 0, $frozen_money = 0, $rank_points = 0, $pay_points = 0, $change_desc = '', $change_type = ACT_OTHER, $consum_money = 0, $give_points = 0, $tx_points = 0, $history_zsq = 0, $zsq = 0, $frozen_points = 0, $day_points = 0, $hk_points = 0, $frozen_hk_points = 0, $yl_points = 0, $jljs_points = 0
		$res1 =saveSaoMaTiXian($amount, $_SESSION["user_id"]);
// 		$res1 = log_account_change($_SESSION["user_id"], 0, 0, 0, 0, "商家MP端发起扫码货款积分兑换操作", 10, 0, 0, 0, 0, 0, 0, 0, $amount, $amount * (-1), 0, 0);
		if ($res1) {
			$content = "操作成功,等待审核。";
			$GLOBALS['db']->commitTrans();
			$links = array(
					array('href' => 'user.php', 'text' => "个人中心")
			);
			sys_msg($content, 0, $links);
		} else {
			$GLOBALS['db']->rollbackTrans();
			$content = "积分兑换申请失败";
			show_message($content);
		}
	} else {
		$GLOBALS['db']->rollbackTrans();
		$content = "积分兑换申请失败";
		show_message($content);
	}
	
}elseif ($_REQUEST ['act'] == 'fen_xiang_dui_huan'){
	
	//周:提交积分兑换申请
	include_once (ROOT_PATH . 'includes/lib_clips.php');
	include_once (ROOT_PATH . 'includes/lib_order.php');
	$amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
	if ($amount <= 0) {
		sys_msg("积分兑换积分必须大于零");
	}
	/* 变量初始化 */
	$surplus = array(
			'user_id' => $_SESSION["user_id"],
			'rec_id' => !empty($_POST['rec_id']) ? intval($_POST['rec_id']) : 0,
			'process_type' =>1,//提现
			'payment_id' => isset($_POST['payment_id']) ? intval($_POST['payment_id']) : 0, 
			'user_note' => isset($_POST['user_note']) ? trim($_POST['user_note']) : '',
			'amount' => $amount,
			'fee' => $amount*12/100,
			'real_amount' => $amount*88/100
	);
	/* 判断是否有足够的余额的进行退款的操作 */
	$userDefault = get_user_default($_SESSION["user_id"]);
	$fxjl_points = $userDefault["fxjl_points"] * 1;
	
	
	$weekDay = date("w");
	if(!($weekDay==4)){
		sys_msg("提现日期为周四");
	}
	
	$hoursNow =  date("H")+8;
	if($hoursNow<8|| $hoursNow>16){
		sys_msg("提现时间为每周四早8点到下午5点之间");
	}
	
	
	$sql = "SELECT add_time FROM " . $GLOBALS['ecs']->table("user_account") . " where user_id='" . $_SESSION["user_id"] . "' and process_type=1 and type=".ACT_DRAWING_FEN_XIANG_JI_FEN." order by add_time DESC limit 1 ";
	
	$add_time = $GLOBALS['db']->getOne($sql);
	$time = gmtime();
	if (!empty($add_time) && ($time < $add_time + 86400)) {
		sys_msg("您今天分享奖励积分提过现了,请下次提现时间再进行操作");
	}
	
	
	$min = 100;
	
	
	if ($fxjl_points < $min) {
		$content = "您现有的分享奖励积分不足" . $min . "，此操作将不可进行！";
		sys_msg($content);
	}
	
 
	
	
 
	
	if ($amount > $fxjl_points) {
		$content = "您要申请提现的积分超过了您现有的分享奖励积分，此操作将不可进行！";
		sys_msg($content);
	}
	// 插入会员账目明细
	$amount = '-' . $amount;
	$surplus['payment'] = '';
	$GLOBALS['db']->startTrans();
	$surplus['rec_id'] = insert_user_account($surplus, $amount,ACT_DRAWING_FEN_XIANG_JI_FEN);//分享奖励积分提现申请
	
	/* 如果成功提交 */
	if ($surplus['rec_id'] > 0) {
		//$user_id, $user_money = 0, $frozen_money = 0, $rank_points = 0, $pay_points = 0, $change_desc = '', $change_type = ACT_OTHER, $consum_money = 0, $give_points = 0, $tx_points = 0, $history_zsq = 0, $zsq = 0, $frozen_points = 0, $day_points = 0, $hk_points = 0, $frozen_hk_points = 0, $yl_points = 0, $jljs_points = 0
		$res1 = fen_xiang_jiang_li_ji_fen_log($_SESSION["user_id"], "商家MP端发起积分兑换操作",  $amount );
		if ($res1) {
			$content = "操作成功,等待审核。";
			$GLOBALS['db']->commitTrans();
			$links = array(
					array('href' => 'user.php?act=supplier_center', 'text' => "商家中心")
			);
			sys_msg($content, 0, $links);
		} else {
			$GLOBALS['db']->rollbackTrans();
			$content = "积分兑换申请失败";
			show_message($content);
		}
	} else {
		$GLOBALS['db']->rollbackTrans();
		$content = "积分兑换申请失败";
		show_message($content);
	}
	
}elseif ($_REQUEST['act'] == "cancel") {
	// 变量初始化
	$surplus_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
	include_once (ROOT_PATH . 'includes/lib_clips.php');
	include_once (ROOT_PATH . 'includes/lib_order.php');
	if ($surplus_id == 0) {
		ecs_header("Location: supplier_m_center.php?act=list\n");
		exit();
	}
	$GLOBALS['db']->startTrans();
	$account_order = $GLOBALS['db']->getRow('SELECT *  FROM ' . $GLOBALS['ecs']->table('user_account') .
			" WHERE is_paid = 0 AND id = '$surplus_id' AND user_id = '" . $_SESSION['user_id'] . "'");
	$result = del_user_account($surplus_id, $_SESSION['user_id']);
	if ($result) {
		$amount = $account_order['amount'];
		//$user_id, $user_money = 0, $frozen_money = 0, $rank_points = 0, $pay_points = 0, $change_desc = '', $change_type = ACT_OTHER, $consum_money = 0, $give_points = 0, $tx_points = 0, $history_zsq = 0, $zsq = 0, $frozen_points = 0
		if ($account_order['process_type'] * 1 == 1) {
			$ress = log_account_change($_SESSION['user_id'], 0, 0, 0, 0, "商家MP端手动取消积分兑换，货款积分增加", 1, 0, 0, 0, 0, 0, 0, 0, abs($amount), $amount, 0, 0);
		} else {
			$ress = true;
		}
		if ($ress) {
			$GLOBALS['db']->commitTrans();
			$links = array(
					array('href' => 'supplier_m_center.php?act=list', 'text' => "积分兑换"),
					array('href' => 'user.php?act=supplier_center', 'text' => "商家中心")
			);
			sys_msg('操作成功！', 0, $links);
		}
	}
	$GLOBALS['db']->rollbackTrans();
}elseif ($_REQUEST['act'] == "saveRate") {//保存服务费比例
	$rate =  $_REQUEST['rate'];
	if(  $rate==null){
		echo  '缺少参数';
		die;
	}
	$user_id = $_SESSION["user_id"];
	 
	$sql="update  ".$GLOBALS['ecs']->table('supplier')." set system_fee=".$rate." where user_id=".$user_id;
	$GLOBALS['db']->query($sql);
	echo  '设置成功';
}

/*
 * 充值记录
 * $drawType:提现类型
 */

function get_exchange_account_log($user_id,$drawType) {
	/* 初始化分页参数 */
	$filter = array(
			'user_id' => $user_id,
	);
	$filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'add_time' : trim($_REQUEST['sort_by']);
	$filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
	
	$filter['start_time'] = empty($_REQUEST['start_time']) ? '' : (strpos($_REQUEST['start_time'], '-') > 0 ? local_strtotime($_REQUEST['start_time']) : $_REQUEST['start_time']);
	$filter['end_time'] = empty($_REQUEST['end_time']) ? '' : (strpos($_REQUEST['end_time'], '-') > 0 ? local_strtotime($_REQUEST['end_time']) : $_REQUEST['end_time']);
	
	$where = " WHERE user_id = '$user_id' AND process_type ='1'  and type=".$drawType." ";//type=0:查询货款
	
	if ($filter['start_time']) {
		$where .= " AND add_time >= '$filter[start_time]'";
	}
	if ($filter['end_time']) {
		$where .= " AND add_time <= '$filter[end_time]'";
	}
	/* 查询记录总数，计算分页数 */
	$sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('user_account') . $where;
	$filter['record_count'] = $GLOBALS['db']->getOne($sql);
	$filter = page_and_size($filter);
	
	/* 查询记录 */
	$sql = "SELECT * FROM " . $GLOBALS['ecs']->table('user_account') . $where .
	" ORDER BY $filter[sort_by] $filter[sort_order]";
	$res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);
	
	
	$arr = array();
	while ($rows = $GLOBALS['db']->fetchRow($res)) {
		$rows['add_time'] = local_date("Y-m-d H:i:s", $rows['add_time']);
		$rows['admin_note'] = nl2br(htmlspecialchars($rows['admin_note']));
		$rows['short_admin_note'] = ($rows['admin_note'] > '') ? sub_str($rows['admin_note'], 30) : 'N/A';
		$rows['user_note'] = nl2br(htmlspecialchars($rows['user_note']));
		$rows['short_user_note'] = ($rows['user_note'] > '') ? sub_str($rows['user_note'], 30) : 'N/A';
		$rows['pay_status'] = ($rows['is_paid'] == 0) ? $GLOBALS['_LANG']['un_confirm'] : $GLOBALS['_LANG']['is_confirm'];
		$rows['amount'] = price_format(abs($rows['amount']), false);
		
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
		if (($rows['is_paid'] == 0) && ($rows['process_type'] == 1)) {
			$rows['handle'] = '<a href="supplier_m_center.php?act=cancel&id=' . $rows['id'] . '" onclick="if(confirm(\'确定删除当前积分兑换记录?\')){return true;}else{return false;}">删除</a>';
			$rows['pay_id'] = $pid;
		}
		$arr[] = $rows;
	}
	return array('account' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

/**
 * 保存扫码提现记录, 修改用户账户金额
 * @param unknown $hk_xf_points
 * @param unknown $user_id
 */
function   saveSaoMaTiXian($hk_xf_points,$user_id){
	 
	    $sql="update ".$GLOBALS['ecs']->table('users')." set hk_xf_points=hk_xf_points+".$hk_xf_points.  " where user_id=".$user_id;
		
		$GLOBALS['db']->query($sql);
		
		/* 插入帐户变动记录 */
		$account_log = array(
				'user_id' => $user_id,
				'change_time' => gmtime(),
				'change_desc' => "扫码支付提现",
				'change_type' => 10,
				'hk_xf_points' => $hk_xf_points 
		);
		$queryAccountLog = $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('account_log'), $account_log, 'INSERT');
		return  $queryAccountLog;
	 
	
}
?>