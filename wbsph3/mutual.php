<?php

/**
 * 爱心互助
 */
define('IN_ECS', true);
require (dirname(__FILE__) . '/includes/init.php');

/* 载入语言文件 */
require_once (ROOT_PATH . 'languages/' . $_CFG['lang'] . '/user.php');

$user_id = $_SESSION['user_id'];
$action = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : 'index';
$smarty->assign('act', $action);
$affiliate = unserialize($GLOBALS['_CFG']['affiliate']);
$smarty->assign('affiliate', $affiliate);
$back_act = '';

// 不需要登录的操作
$not_login_arr = array(
    'index'
);

/* 未登录处理 */
if(empty($_SESSION['user_id']))
{
    if(! in_array($action, $not_login_arr))
    {
        // 未登录提交数据。非正常途径提交数据！
        show_message($_LANG['require_login'], array(
            '</br>登录',
            '</br>返回首页'
        ), array(
            'user.php?act=login',
            $ecs->url()
        ), 'error', false);
    }
}

/* 限制某个会员登录 */
if($user_id > 0)
{
    $blacklist = check_user_is_blacklist($user_id);
    if(! empty($blacklist))
    {
        $user->logout();
        show_message($blacklist['limit_desc']);
        exit();
    }
}

assign_template();

// 互助列表
if('index' == $action)
{
    $filter['page_size'] = 12;
    $filter['page'] = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);
    $sql = "SELECT count(*)as num FROM " . $GLOBALS['ecs']->table('mutual') . " as m left join " . $GLOBALS['ecs']->table('users') . "  as u on u.user_id = m.user_id where m.is_delete  ";
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);
    $filter['page_count'] = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;
    
    // 查询所有爱心互助列表
    $sql = "SELECT m.mutual_id, m.images , m.title ,m.target_money, u.mobile_phone , u.user_id , u.user_name , u.real_name  FROM " . $GLOBALS['ecs']->table('mutual') . " as m left join " . $GLOBALS['ecs']->table('users') . "  as u on u.user_id = m.user_id where m.is_delete <> 1 LIMIT " . ($filter['page_size'] * ($filter['page'] - 1)) . ", " . $filter['page_size'] . " ";
    $arr = array();
    $arr = $GLOBALS['db']->getAll($sql);
    $smarty->assign('page', $filter);
    $smarty->assign('lists', $arr);
    $smarty->assign('filter', $filter['filter']);
    $smarty->assign('record_count', $filter['record_count']);
    $smarty->assign('page_count', $filter['page_count']);
    
    $page = (isset($_REQUEST['page'])) ? intval($_REQUEST['page']) : 1;
    
    $start_array = range(1, $page);
    $end_array = range($page, $filter['page_count']);
    if($page - 5 > 0)
    {
        $smarty->assign('start', $page - 3);
        $start_array = range($page, $page - 2);
    }
    if($filter['page_count'] - $page > 5)
    {
        $smarty->assign('end', $page + 3);
        $end_array = range($page, $page + 2);
    }
    $page_array = array_merge($start_array, $end_array);
    sort($page_array);
    $smarty->assign('page_array', array_unique($page_array));
    
    // echo '<pre>';
    // print_r($arr);
    // echo '</pre>';
    
    // 查询所有huzhu
    $smarty->display('mutual.dwt');
}

/*
 * 查看赞助
 */
else if('redone' == $action)
{
    $id = $_GET['id'] > 0 ? intval($_GET['id']) : 0;
    $sql = "SELECT m.mutual_id,m.images, m.title ,m.target_money, m.content ,u.mobile_phone , u.user_id , u.user_name , u.real_name  FROM " . $GLOBALS['ecs']->table('mutual') . " as m left join " . $GLOBALS['ecs']->table('users') . "  as u on u.user_id = m.user_id where m.mutual_id ='" . $id . "'";
    $arr = $GLOBALS['db']->getRow($sql);
    $smarty->assign('mutual_info', $arr);
    $smarty->display('mutual.dwt');
}
/*
 * 选择赞助
 */
else if('checkout' == $action)
{
    $id = $_GET['id'] > 0 ? intval($_GET['id']) : 0;
    $sql = "SELECT m.mutual_id, m.title ,m.target_money, m.content ,u.mobile_phone , u.user_id , u.user_name , u.real_name  FROM " . $GLOBALS['ecs']->table('mutual') . " as m left join " . $GLOBALS['ecs']->table('users') . "  as u on u.user_id = m.user_id where m.mutual_id ='" . $id . "'";
    $arr = $GLOBALS['db']->getRow($sql);
    if($arr['status'] == 1 || empty($arr))
    {
        show_message('该项目已关闭', '返回上一页', 'mutual.php');
        exit();
    }
    $user_info = $GLOBALS['db']->getRow(" SELECT real_name , email , mobile_phone  FROM " . $GLOBALS['ecs']->table('users') . " WHERE `user_id` = '" . $_SESSION['user_id'] . "' LIMIT 0, 1000 ");
    
    require (ROOT_PATH . 'includes/lib_order.php');
    // 给货到付款的手续费加<span id>，以便改变配送的时候动态显示
    $payment_list = available_payment_list(1, 100, true, $_SESSION['extension_code'] == 'virtual_good' ? 1 : 0);
    
    $pay_balance_id = 0; // 当前配置于的余额支付的递增id
    if(isset($payment_list))
    {
        foreach($payment_list as $key =>$payment)
        {
            if($payment['is_cod'] == '1')
            {
                $payment_list[$key]['format_pay_fee'] = '<span id="ECS_CODFEE">' . $payment['format_pay_fee'] . '</span>';
            }
            
            /* 如果有余额支付 */
            if($payment['pay_code'] == 'balance')
            {
                $pay_balance_id = $payment['pay_id'];
                /* 如果未登录，不显示 */
                if($_SESSION['user_id'] == 0)
                {
                    unset($payment_list[$key]);
                }
                else
                {
                    if($_SESSION['flow_order']['pay_id'] == $payment['pay_id'])
                    {
                        $smarty->assign('disable_surplus', 1);
                    }
                }
            }
            if($payment['pay_code'] == "bank")
            {
                // unset($payment_list[$key]);
            }
        }
    }
    
    assign_template();
    $smarty->assign('pay_balance_id', $pay_balance_id);
    $smarty->assign('payment_list', $payment_list);
    
    $smarty->assign('mutual_info', $arr);
    $smarty->assign('user_info', $user_info);
    $smarty->display('mutual_checkout.dwt');
}
/*
 * 添加订单
 */
else if('mutual_done' == $action)
{
    include_once (ROOT_PATH . 'includes/lib_base.php');
    include_once (ROOT_PATH . 'includes/lib_order.php');
    require_once (ROOT_PATH . 'includes/lib_clips.php');
    
    // 插入订单
    $order = array(
        'user_id' => $_SESSION['user_id'],
        'mobile' => compile_str(make_semiangle(trim($_POST['tbDonateMobile']))),
        'consignee' => compile_str(make_semiangle(trim($_POST['tbDonateName']))),
        'order_sn' => get_order_sn(),
        'add_time' => gmtime(),
        'pay_id' => intval($_POST['payment']),
        'mutual_id' => intval($_POST['mutual_id']),
        'content' => compile_str(make_semiangle(trim($_POST['tbRemark']))),
        'email' => compile_str(make_semiangle(trim($_POST['tbDonateEmail']))),
        'mutual_money' => floatval($_POST['jz_money']),
        'is_user' => intval($_POST['is_user_name'])
    );
    
    // 查询是否是自己的项目
    $rs = $GLOBALS['db']->getOne(" SELECT * FROM `xbmall_mutual` WHERE mutual_id = '" . $order['mutual_id'] . "' and  `user_id` = '" . $_SESSION['user_id'] . "' LIMIT 0, 1000 ");
    if($rs)
    {
        show_message('不能给自己献爱心', '返回上一页');
        exit();
    }
    
    // 过滤必填条件
    if($order['mutual_money'] <= 0)
    {
        show_message('付款金额有误,请重新操作', '返回上一页');
        exit();
    }
    
    // 查询互助条件
    $mutual_info = $GLOBALS['db']->getRow(" SELECT * FROM " . $GLOBALS['ecs']->table('mutual') . " WHERE `mutual_id` = '" . $order['mutual_id'] . "' LIMIT 0, 10 ");
    
    // 过滤必填条件
    if(empty($mutual_info) || $mutual_info['status'] == 1 || $mutual_info['is_delete'] == 1)
    {
        show_message('该互助项目已经结束', '返回上一页');
        exit();
    }
    
    // 是否为支付宝微信银行打款
    if($order['pay_id'] == 0 && $_POST['payment_other'] > 0)
    {
        $order['pay_id'] = $_POST['payment_other'];
    }
    
    /* 支付方式 */
    if($order['pay_id'] > 0)
    {
        $payment = payment_info($order['pay_id']);
        $order['pay_name'] = addslashes($payment['pay_name']);
    }
    else
    {
        $order['pay_id'] = 0;
        $order['pay_name'] = "积分支付";
        // show_message('支付方式必须选择一项!');
    }
    
    // 余额支付金额
    $surplus = 0;
    
    // 积分支付金额
    $integral = 0;
    
    $pay_fee = $order['mutual_money'];
    
    // 付款金额是否正确
    if(($surplus + $integral + $pay_fee) != $order['mutual_money'])
    {
        show_message('付款金额有误,请重新操作');
    }
    
    // 余额付款操作
    if($surplus > 0)
    {
    
    }
    // 积分付款操作
    if($integral > 0)
    {
    
    }
    // 网上付款操作
    if($pay_fee > 0)
    {
    
    }
    
    $order['surplus'] = $surplus;
    $order['integral'] = $integral;
    $order['pay_fee'] = $pay_fee;
    
    // 开启事物
    $GLOBALS['db']->startTrans();
    
    $insertBd = $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table("mutual_info"), $order);
    if($insertBd)
    {
        $bdId = $GLOBALS['db']->insert_id();
    }
    else
    {
        $GLOBALS['db']->rollbackTrans();
        show_message('操作有误,请重新操作');
        exit();
    }
    
    // 为每一个订单生成一个支付日志记录
    $order['log_id'] = insert_pay_log($bdId, $order['mutual_money'], 2);
    
    // 插入订单
    $GLOBALS['db']->commitTrans();
    // $GLOBALS['db']->rollbackTrans();
    ecs_header('Location:mutual.php?act=mutual_done_info&order_id=' . $bdId . " \n");
    exit();
}
else if('mutual_done_info' == $_REQUEST['act'])
{
    if(empty($_GET['order_id']))
    {
        show_message('操作有误,请重新操作');
    }
    $order_id = intval($_GET['order_id']);
    // 查询订单
    $order_info = $GLOBALS['db']->getRow("SELECT * FROM " . $GLOBALS['ecs']->table('mutual_info') . " where order_id='" . $order_id . "'");
    $order_info['title'] = $GLOBALS['db']->getOne("SELECT title FROM " . $GLOBALS['ecs']->table('mutual') . " where mutual_id = '" . $order_info['mutual_id'] . "' ");
    assign_template();
    
//     require_once (ROOT_PATH . 'includes/modules/PHPKit-CMBC/php_java.php');
//     include_once (ROOT_PATH . 'includes/modules/PHPKit-CMBC/CMBC_SDK_TOOLS.php');
//     require_once (ROOT_PATH . 'includes/lib_clips.php');
//     require_once (ROOT_PATH . 'includes/lib_order.php');
//     $cmbc_sdk = new CMBC_SDK_TOOLS(0);
//     $payment = payment_info($order_info['pay_id']);
//     if ($payment['pay_code'] == "alipay") {
//         $selectTradeType = CMBC_SDK_TOOLS::API_ZFBQRCODE;
//     } elseif ($payment['pay_code'] == "weixin") {
//         $selectTradeType = CMBC_SDK_TOOLS::API_WXQRCODE;
//     }
    
//     $order_info['log_id'] = insert_pay_log($order_info['order_id'],$order_info['mutual_money'], 2);
    
//     $payInfo = $cmbc_sdk->toCreateOrder($selectTradeType, $order_info['mutual_money'], "爱心互助，订单号：" . $order_info['order_sn'], $order_info['log_id'], $GLOBALS['ecs']->get_domain() . "/testNotify.php", "");
    
//     if ($payInfo['code'] * 1 == 1) {
//         $sql = "UPDATE " . $GLOBALS['ecs']->table('pay_log') . " SET `pay_info`='" . $payInfo['data'] . "' where `log_id`='" . $order_info['log_id'] . "'";
//         $GLOBALS['db']->query($sql);
//         //生成支付代码
//         $pay_online = $cmbc_sdk->toGetPayInfo($selectTradeType, $payInfo['data']);
//         $smarty->assign('pay_online', $pay_online);
//         $smarty->assign('log_ids', $order_info['log_id']);
//     } else {
//         show_message('系统繁忙,请稍候再试', '我的投保', 'user.php?act=mutual');
//         //show_message($payInfo['message']);
//     }

    //重新生成pay_log
    require_once (ROOT_PATH . 'includes/lib_clips.php');
    require_once (ROOT_PATH . 'includes/lib_order.php');
    $payment = payment_info($order_info['pay_id']);
    $order_info['log_id'] = insert_pay_log($order_info['order_id'],$order_info['mutual_money'], 2);
//     include_once('includes/modules/payment/zhizunbao.php');
//     include_once('includes/modules/openepay/zhifutong.php');
//     $pay_obj = new zhifutong();
    include_once('includes/modules/payment/zhizunbao.php');
    $pay_obj = new zhizunbao();
    $orderUrl = $GLOBALS['ecs']->url();
    $order_pay=array(
            'order_amount' =>   $order_info['mutual_money'] ,
            'user_id' => $order_info['user_id'],
            'log_id' => $order_info['log_id'],
            'order_id' => $order_info['order_id'],
    );
    $pay_online = $pay_obj->get_code($order_pay, $payment['pay_code'], $orderUrl . "user.php?act=mutual", "PC");
    
    $smarty->assign('pay_online', $pay_online); 
    $smarty->assign('order_info', $order_info); // 页面标题
    $smarty->assign('page_title', '支付金额'); // 页面标题
    $smarty->display('mutual_done.dwt');
}

// 获取支付按钮
function get_pay_ment($order)
{
    
    require_once (ROOT_PATH . 'includes/lib_clips.php');
    require_once (ROOT_PATH . 'includes/lib_order.php');
    require_once (ROOT_PATH . 'includes/lib_payment.php');
    
    /*
     * 在线支付按钮
     */
    // 支付方式信息
    $payment_info = array();
    $payment_info = payment_info($order['pay_id']);
    
    if($order['order_status'])
    {
        return false;
    }
    
    // 无效支付方式
    if($payment_info === false)
    {
        $order['pay_online'] = '';
    }
    else
    {
        // 取得支付信息，生成支付代码
        $payment = unserialize_config($payment_info['pay_config']);
        if($payment_info['pay_code'] == 'alipay_bank')
        {
            $payment['www_ecshop68_com_alipay_bank'] = $order['defaultbank'];
        }
        
        // 获取需要支付的log_id
        $order['log_id'] = get_paylog_id($order['order_id'], $order['pay_log_type']);
        $order['user_name'] = $_SESSION['user_name'];
        $order['pay_desc'] = $payment_info['pay_desc'];
        
        /* 调用相应的支付方式文件 */
        include_once (ROOT_PATH . 'includes/modules/payment/' . $payment_info['pay_code'] . '.php');
        
        /* 取得在线支付方式的支付按钮 */
        $pay_obj = new $payment_info['pay_code'](); // alipay_bank
        
        $order['pay_online'] = $pay_obj->get_code($order, $payment);
        return $order;
    }
    return '';
}
?>