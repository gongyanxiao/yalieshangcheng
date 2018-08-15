<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
if ($_REQUEST['act'] == "recharge") {
    admin_priv('role_points_man');
    $smarty->assign('full_page', 1);
    include_once (ROOT_PATH . 'includes/lib_clips.php');
    $smarty->assign('payment', get_online_payment_list(false));
    assign_query_info();
    $smarty->display('role_points_recharge.htm');
} elseif ($_REQUEST['act'] == "recharge_list") {
    $smarty->assign('full_page', 1);
    assign_query_info();
    $account_list = get_recharge_account_log($_SESSION["member_uid"]);
    $smarty->assign('account_list', $account_list['account']);
    $smarty->assign('filter', $account_list['filter']);
    $smarty->assign('record_count', $account_list['record_count']);
    $smarty->assign('page_count', $account_list['page_count']);
    $smarty->display('role_points_recharges.htm');
} elseif ($_REQUEST['act'] == "query") {
    assign_query_info();
    $account_list = get_recharge_account_log($_SESSION["member_uid"]);
    $smarty->assign('account_list', $account_list['account']);
    $smarty->assign('filter', $account_list['filter']);
    $smarty->assign('record_count', $account_list['record_count']);
    $smarty->assign('page_count', $account_list['page_count']);
    make_json_result($smarty->fetch('role_points_recharges.htm'), '', array('filter' => $account_list['filter'], 'page_count' => $account_list['page_count']));
} elseif ($_REQUEST['act'] == "act_account") {//周:商家充值
    include_once (ROOT_PATH . 'includes/lib_clips.php');
    include_once (ROOT_PATH . 'includes/lib_order.php');
    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
    if ($amount <= 0) {
        sys_msg("充值金额不能小于零");
    }
    
    if( $_SESSION ["member_uid"]==null || $_SESSION ["member_uid"]==0 || !$_SESSION ["member_uid"]){
    	sys_msg ( "登录失效, 请重新登录" );
    }
    
    
    /* 变量初始化 */
    $surplus = array(
        'user_id' => $_SESSION["member_uid"], 
    		'rec_id' => !empty($_POST['rec_id']) ? intval($_POST['rec_id']) : 0,
    		'process_type' => isset($_POST['surplus_type']) ? intval($_POST['surplus_type']) : 0,
    		'payment_id' => isset($_POST['payment_id']) ? intval($_POST['payment_id']) : 0, 
    		'user_note' => isset($_POST['user_note']) ? trim($_POST['user_note']) : '',
    		'amount' => $amount
    );

    $payment_info = payment_info($surplus['payment_id']);
    if ($payment_info['pay_code'] == 'alipay_bank') {
        $surplus['defaultbank'] = isset($_POST['com_bank']) ? trim($_POST['com_bank']) : '';
    }
    if ($surplus['payment_id'] <= 0) {
        sys_msg("支付方式不合法，请选择支付方式");
    }

    include_once (ROOT_PATH . 'includes/lib_payment.php');

    // 获取支付方式名称
    $payment_info = array();
    $payment_info = payment_info($surplus['payment_id']);
    $surplus['payment'] = $payment_info['pay_name'];

    if ($surplus['rec_id'] > 0) {
        // 更新会员账目明细
        $surplus['rec_id'] = update_user_account($surplus);
    } else {
        // 插入会员账目明细
        $surplus['rec_id'] = insert_user_account($surplus, $amount);
    }

    // 取得支付信息，生成支付代码
    $payment = unserialize_config($payment_info['pay_config']);

    // 生成伪订单号, 不足的时候补0
    $order = array();
    $order['user_id'] = $_SESSION["member_uid"];
    $order['order_sn'] = $surplus['rec_id'];
    $order['user_name'] = $_SESSION['user_name'];
    $order['surplus_amount'] = $amount;
    $order['defaultbank'] = $surplus['defaultbank'] ;
    // 计算支付手续费用
    $payment_info['pay_fee'] = pay_fee($surplus['payment_id'], $order['surplus_amount'], 0);

    // 计算此次预付款需要支付的总金额
    $order['order_amount'] = $amount + $payment_info['pay_fee'];

    // 记录支付log
    $order['log_id'] = insert_pay_log($surplus['rec_id'], $order['order_amount'], $type = PAY_SURPLUS, 0);


    //重新生成pay_log
    include_once(ROOT_PATH.'includes/modules/payment/zhizunbao.php');
    $pay_obj = new zhizunbao();
//     include_once(ROOT_PATH.'includes/modules/openepay/zhifutong.php');
//     $pay_obj = new zhifutong();
    $orderUrl = $GLOBALS['ecs']->url();
    $order_pay=array(
            'order_amount' =>   $order['order_amount'] ,
            'user_id' => $order['user_id'],
            'log_id' => $order['log_id'],
            'order_id' => $order['order_sn'],
    		'user_note' => isset($_POST['user_note']) ? trim($_POST['user_note']) : ''
    		
    );
    

    $pay_online = $pay_obj->get_code($order_pay, $payment_info['pay_code'], $orderUrl . "members/role_recharge.php?act=recharge_list",'PC');
    $pay_online = "<div style='margin-top:10px'>".$pay_online.'</div>';
    $smarty->assign('pay_button', $pay_online);
    
    $sql = "SELECT pay_id FROM " . $GLOBALS['ecs']->table('payment') . " WHERE pay_code='weixin'";
    $weixin_pay = $GLOBALS['db']->getOne($sql);
    $smarty->assign('weixin_pay', strval($weixin_pay));

    $sql = "SELECT pay_id FROM " . $GLOBALS['ecs']->table('payment') . " WHERE pay_code='alipay'";
    $alipay_pay = $GLOBALS['db']->getOne($sql);
    $smarty->assign('alipay_pay', strval($alipay_pay));

    $smarty->assign('site_domain', $GLOBALS['ecs']->get_domain());

    /* 模板赋值 */
    $smarty->assign('payment', $payment_info);
    $smarty->assign('pay_fee', price_format($payment_info['pay_fee'], false));
    $smarty->assign('amount', price_format($amount, false));
    $smarty->assign('order', $order);
    $smarty->display("role_points_torecharge.htm");
} elseif ($_REQUEST['act'] == "pay") {
    include_once (ROOT_PATH . 'includes/lib_clips.php');
    include_once (ROOT_PATH . 'includes/lib_payment.php');
    include_once (ROOT_PATH . 'includes/lib_order.php');

    // 变量初始化
    $surplus_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $payment_id = isset($_GET['pid']) ? intval($_GET['pid']) : 0;

    if ($surplus_id == 0) {
        ecs_header("Location: role_recharge.php?act=recharge_list\n");
        exit();
    }

    // 如果原来的支付方式已禁用或者已删除, 重新选择支付方式
    if ($payment_id == 0) {
        ecs_header("Location: role_recharge.php?act=recharge_list&id=" . $surplus_id . "\n");
        exit();
    }

    // 获取单条会员帐目信息
    $order = array();
    $order = get_surplus_info($surplus_id);

    // 支付方式的信息
    $payment_info = array();
    $payment_info = payment_info($payment_id);
    /* 如果当前支付方式没有被禁用，进行支付的操作 */
    if (!empty($payment_info)) {
        // 取得支付信息，生成支付代码
        $payment = unserialize_config($payment_info['pay_config']);

        // 生成伪订单号
        $order['order_sn'] = $surplus_id;

        // 获取需要支付的log_id
        //$order['log_id'] = get_paylog_id($surplus_id, $pay_type = PAY_SURPLUS);
        $order['log_id'] = insert_pay_log($surplus_id, $order['order_amount'], $type = PAY_SURPLUS, 0);
        $order['user_name'] = $_SESSION['user_name'];
        $order['surplus_amount'] = $order['amount'];

        // 计算支付手续费用
        $payment_info['pay_fee'] = pay_fee($payment_id, $order['surplus_amount'], 0);

        // 计算此次预付款需要支付的总金额
        $order['order_amount'] = $order['surplus_amount'] + $payment_info['pay_fee'];

        // 如果支付费用改变了，也要相应的更改pay_log表的order_amount
        $order_amount = $db->getOne("SELECT order_amount FROM " . $ecs->table('pay_log') . " WHERE log_id = '$order[log_id]'");
        if ($order_amount != $order['order_amount']) {
            $db->query("UPDATE " . $ecs->table('pay_log') . " SET order_amount = '$order[order_amount]' WHERE log_id = '$order[log_id]'");
        }

        
        
        //重新生成pay_log
//         include_once(ROOT_PATH.'includes/modules/payment/zhizunbao.php');
//         include_once(ROOT_PATH.'includes/modules/openepay/zhifutong.php');
//         $pay_obj = new zhifutong();
        include_once('includes/modules/payment/zhizunbao.php');
        $pay_obj = new zhizunbao();
        $orderUrl = $GLOBALS['ecs']->url();
        $order_pay=array(
                'order_amount' =>   $order['order_amount'] ,
                'user_id' => $order['user_id'],
                'log_id' => $order['log_id'],
                'order_id' => $order['order_sn'],
        );
       // print_r($order_pay);
        $pay_online = $pay_obj->get_code($order_pay, $payment_info['pay_code'], $orderUrl . "members/role_recharge.php?act=recharge_list",'PC');
        $pay_online = "<div style='margin-top:10px'>".$pay_online.'</div>';
        $smarty->assign('pay_button', $pay_online);
        
        
        /* 模板赋值 */
        $smarty->assign('payment', $payment_info);
        $smarty->assign('order', $order);
        $smarty->assign('pay_fee', price_format($payment_info['pay_fee'], false));
        $smarty->assign('amount', price_format($order['surplus_amount'], false));
        $smarty->assign('action', 'act_account');
        $smarty->display('role_points_torecharge.htm');
    }
    /* 重新选择支付方式 */ else {
        include_once (ROOT_PATH . 'includes/lib_clips.php');
        $smarty->assign('payment', get_online_payment_list());
        $smarty->assign('order', $order);
        $smarty->assign('action', 'account_deposit');
        $smarty->display('role_points_torecharge.htm');
    }
} elseif ($_REQUEST['act'] == "cancel") {
    // 变量初始化
    $surplus_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $payment_id = isset($_GET['pid']) ? intval($_GET['pid']) : 0;

    if ($surplus_id == 0) {
        ecs_header("Location: role_recharge.php?act=recharge_list\n");
        exit();
    }
    $sql = "delete from " . $GLOBALS['ecs']->table("user_account") . " where id='" . $surplus_id . "'";
    $GLOBALS['db']->query($sql);
    $links = array(
        array('href' => 'role_recharge.php?act=recharge_list', 'text' => "充值列表")
    );
    sys_msg("操作成功", 0, $links);
}
/*
 * 充值记录 
 */

function get_recharge_account_log($user_id) {
    /* 初始化分页参数 */
    /* 初始化分页参数 */
    $filter = array(
        'user_id' => $user_id,
    );
    $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'add_time' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

    $filter['start_time'] = empty($_REQUEST['start_time']) ? '' : (strpos($_REQUEST['start_time'], '-') > 0 ? local_strtotime($_REQUEST['start_time']) : $_REQUEST['start_time']);
    $filter['end_time'] = empty($_REQUEST['end_time']) ? '' : (strpos($_REQUEST['end_time'], '-') > 0 ? local_strtotime($_REQUEST['end_time']) : $_REQUEST['end_time']);

    $where = " WHERE user_id = '$user_id' AND process_type ='0' ";

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
        if (($rows['is_paid'] == 0) && ($rows['process_type'] == 0)) {
            $rows['handle'] = '<a href="role_recharge.php?act=pay&id=' . $rows['id'] . '&pid=' . $pid . '">去支付</a>|<a href="role_recharge.php?act=cancel&id=' . $rows['id'] . '&pid=' . $pid . '" onclick="if(confirm(\'确定删除当前充值记录?\')){return true;}else{return false;}">删除</a>';
            $rows['pay_id'] = $pid;
        }
        $arr[] = $rows;
    }
    return array('account' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}
