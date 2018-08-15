<?php

/**
 * ECSHOP 专题前台
 * ============================================================================
 * 版权所有 2005-2011 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * @author:     webboy <laupeng@163.com>
 * @version:    v2.1
 * ---------------------------------------------
 */
define('IN_ECS', true);

require (dirname(__FILE__) . '/includes/init.php');
require (dirname(__FILE__) . '/includes/lib_order.php');
require (dirname(__FILE__) . '/includes/lib_clips.php');

/* 载入语言文件 */
require_once (ROOT_PATH . 'languages/' . $_CFG['lang'] . '/user.php');

/* 新版微信改动 */
if(isset($_GET['wxid']) && ! isset($_GET['is_update']))
{
    if(inject_check($_GET['wxid']))
    {
        show_message("参数错误", '返回主页', 'index.php', 'info');
    }
    
    $sql = "SELECT ecuid FROM " . $GLOBALS['ecs']->table('weixin_user') . " WHERE fake_id  = '" . $_GET['wxid'] . "'";
    $ecuid = $GLOBALS['db']->getOne($sql);
    if($ecuid > 0)
    {
        // 已绑定标识k
        $smarty->assign('tag', '1');
        $smarty->assign('shop_name', $_CFG['shop_name']);
        $smarty->display('weixin_open.dwt');
        exit();
    }
}
if(isset($_GET['wxid']))
{
    $_SESSION['wxid'] = $_GET['wxid'];
}
else
{
    if(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger'))
    {
        require (dirname(__FILE__) . '/weixin/wechat.class.php');
        $weixinconfig = $GLOBALS['db']->getRow("SELECT * FROM " . $GLOBALS['ecs']->table('weixin_config') . " WHERE `id` = 1");
        $weixin = new core_lib_wechat($weixinconfig);
        if($_GET['code'])
        {
            $json = $weixin->getOauthAccessToken();
            if($json['openid'])
            {
                $_SESSION['wxid'] = $json['openid'];
            }
        }
        if(! isset($_SESSION['wxid']))
        {
            $url = $GLOBALS['ecs']->url() . "/user.php";
            $url = $weixin->getOauthRedirect($url, 1, 'snsapi_base');
            header("Location:$url");
            exit();
        }
    }
}
/* end */
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

$action = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : 'index';
$smarty->assign('action', $action);

$affiliate = unserialize($GLOBALS['_CFG']['affiliate']);
$smarty->assign('affiliate', $affiliate);
$back_act = '';

// 不需要登录的操作或自己验证是否登录（如ajax处理）的act
$not_login_arr = array(
    'index',
    'insure'
);

/* 显示页面的action列表 */
$ui_arr = array();

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

if(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger'))
{
    $smarty->assign('iswei', 1); // 判断是否为微信
}
/* 未登录处理 */
if(empty($_SESSION['user_id']))
{
    if(! in_array($action, $not_login_arr))
    {
        header("Location:user.php?act=login");
    }
}

assign_template();

// 爱心互助
if($action == 'index')
{
    $filter['page_size'] = 10;
    $filter['page'] = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);
    $sql = "SELECT count(*)as num FROM " . $GLOBALS['ecs']->table('mutual') . " as m left join " . $GLOBALS['ecs']->table('users') . "  as u on u.user_id = m.user_id where m.is_delete  ";
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);
    $filter['page_count'] = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;
    
    // 查询所有爱心互助列表
    $sql = "SELECT m.mutual_id, m.images, m.title ,m.target_money, u.mobile_phone , u.user_id , u.user_name , u.real_name  FROM " . $GLOBALS['ecs']->table('mutual') . " as m left join " . $GLOBALS['ecs']->table('users') . "  as u on u.user_id = m.user_id where m.is_delete <> 1 LIMIT " . ($filter['page_size'] * ($filter['page'] - 1)) . ", " . $filter['page_size'] . " ";
    $arr = array();
    $arr = $GLOBALS['db']->getAll($sql);
    $smarty->assign('page', $filter);
    $smarty->assign('mutual_list', $arr);
    $smarty->assign('filter', $filter['filter']);
    $smarty->assign('record_count', $filter['record_count']);
    $smarty->assign('page_count', $filter['page_count']);
    
    $pager = get_pager('mutual.php', array(
        'act' => 'index'
    ), $filter['record_count'], $filter['page'], $filter['page_size']);
    $smarty->assign('pager', $pager);
    
    $smarty->display('mutual.dwt');
    exit();
}
// 保险列表
if($action == 'insure')
{
    
    // 定义保险类型
    
    $yanglaiimg_10 = $GLOBALS['db'] ->getOne("  SELECT store_range FROM ".$GLOBALS['ecs']->table('shop_config')." where code= 'yanglaiimg_10' ");
    $yanglaiimg_20 = $GLOBALS['db'] ->getOne("  SELECT store_range FROM ".$GLOBALS['ecs']->table('shop_config')." where code= 'yanglaiimg_20' ");
    $yanglaiimg_30 = $GLOBALS['db'] ->getOne("  SELECT store_range FROM ".$GLOBALS['ecs']->table('shop_config')." where code= 'yanglaiimg_30' ");
    
    $baoxian = array(
            '1' => array(
                    'id' => 1,
                    'insure_name' => '黄铜养老保险',
                    'insure_title' => '100000元保额',
                    'insure_money' => '10',
                    'insure_baoe' => '1000000',
                    'img_url' => $yanglaiimg_10 ,
            ),
            '2' => array(
                    'id' => 2,
                    'insure_name' => '白银养老保险',
                    'insure_title' => '200000元保额',
                    'insure_money' => '20',
                    'insure_baoe' => '2000000',
                    'img_url' => $yanglaiimg_20
            ),
            '3' => array(
                    'id' => 3,
                    'insure_name' => '黄金养老保险',
                    'insure_title' => '300000元保额',
                    'insure_money' => '30',
                    'insure_baoe' => '3000000',
                    'img_url' => $yanglaiimg_30
            )
    );

    $smarty->assign('insure_list', $baoxian);
    $smarty->display('mutual.dwt');
    exit();
}

$baoxian = array(
    '1' => array(
        'id' => 1,
        'insure_name' => '100000元保额',
        'insure_title' => '100000元保额',
        'insure_money' => '10.00',
        'insure_baoe' => '1000000',
        'img_url' => '/images/201703/thumb_img/6_thumb_G_1488524918787.jpg'
    ),
    '2' => array(
        'id' => 2,
        'insure_name' => '200000元保额',
        'insure_title' => '200000元保额',
        'insure_money' => '20.00',
        'insure_baoe' => '2000000',
        'img_url' => '/images/201703/thumb_img/6_thumb_G_1488524918787.jpg'
    ),
    '3' => array(
        'id' => 3,
        'insure_name' => '300000元保额',
        'insure_title' => '300000元保额',
        'insure_money' => '30.00',
        'insure_baoe' => '3000000',
        'img_url' => '/images/201703/thumb_img/6_thumb_G_1488524918787.jpg'
    )
);

// 保险确认
if($action == 'inaure_redone')
{
    if($_SESSION['user_id'] == 0)
    {
        /* 用户没有登录且没有选定匿名购物，转向到登录页面 */
        ecs_header("Location: user.php\n");
        exit();
    }
    
    $id = intval($_REQUEST['id']);
    
    // 获取选择的保险
    if(empty($baoxian[$id]))
    {
        show_message('选择保险信息有误');
    }
    
    // 给货到付款的手续费加<span id>，以便改变配送的时候动态显示
    $payment_list = available_payment_list(1, 0, true, $_SESSION['extension_code'] == 'virtual_good' ? 1 : 0);
    $pay_balance_id = 0; // 当前配置于的余额支付的递增id
    if(isset($payment_list))
    {
        foreach($payment_list as $key =>$payment)
        {
            if($payment['is_cod'] == '1')
            {
                $payment_list[$key]['format_pay_fee'] = '<span id="ECS_CODFEE">' . $payment['format_pay_fee'] . '</span>';
            }
            /* 如果有易宝神州行支付 如果订单金额大于300 则不显示 */
            if($payment['pay_code'] == 'yeepayszx' && $total['amount'] > 300)
            {
                unset($payment_list[$key]);
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
    $user_info = $GLOBALS['db']->getRow(" SELECT real_name , mobile_phone  FROM " . $GLOBALS['ecs']->table('users') . " WHERE `user_id` = '" . $_SESSION['user_id'] . "' LIMIT 0, 1000 ");
    $smarty->assign('user_info', $user_info);
    $smarty->assign('pay_balance_id', $pay_balance_id);
    $smarty->assign('payment_list', $payment_list);
    $smarty->assign('insure', $baoxian[$id]);
    $smarty->display('mutual.dwt');
    exit();
}
// 保存保险
if($action == 'insure_done')
{
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    if($_SESSION['user_id'] == 0)
    {
        /* 用户没有登录且没有选定匿名购物，转向到登录页面 */
        ecs_header("Location: user.php\n");
        exit();
    }
    
    $id = intval($_REQUEST['id']);
    
    // 获取选择的保险
    if(empty($baoxian[$id]))
    {
        show_message('选择保险信息有误');
    }
    
    // 判断是否实名认证
    $user_info = $db->getRow("SELECT status, user_id, card , real_name , mobile_phone  FROM " . $ecs->table('users') . " WHERE user_id = '" . intval($user_id) . "' ");
    if($user_info['status'] != 1)
    {
        show_message('请先完成实名认证');
        exit();
    }
    
    // 检查是否投保
    $rs = $db->getOne(" SELECT * FROM " . $ecs->table('insure_info') . " WHERE is_delete <> 1 and user_id = '" . intval($user_id) . "' for update ");
    if($rs)
    {
        show_message('您已经投保不能再次投保');
        exit();
    }
    
    // 插入订单
    $order = array(
        'user_id' => $user_info['user_id'],
        'mobile' => $user_info['mobile_phone'],
        'consignee' => $user_info['real_name'],
        'card_id' => $user_info['card'],
        'order_sn' => get_order_sn(),
        'add_time' => gmtime(),
        'pay_id' => $_POST['payment'],
        'insure_money' => $baoxian[$id]['insure_money'],
        'insure_baoe' => $baoxian[$id]['insure_baoe'],
        'insure_id' => $baoxian[$id]['id']
    );
    
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
    
    $pay_fee = $baoxian[$_POST['baoxian_id']]['insure_money'];
    
    // 付款金额是否正确
    if(($surplus + $integral + $pay_fee) != $baoxian[$_POST['baoxian_id']]['insure_money'])
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
    
    $insertBd = $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table("insure_info"), $order);
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
    $order['log_id'] = insert_pay_log($bdId, $order['insure_money'], 3);
    
    // 插入订单
    $GLOBALS['db']->commitTrans();
    
    ecs_header('Location:mutual.php?act=insure_done_info&order_id=' . $bdId . " \n");
    exit();

}

if('insure_done_info' == $_REQUEST['act'])
{
    if(empty($_GET['order_id']))
    {
        show_message('操作有误,请重新操作');
    }
    $order_id = intval($_GET['order_id']);
    // 查询订单
    $order_info = $GLOBALS['db']->getRow("SELECT * FROM " . $GLOBALS['ecs']->table('insure_info') . " where order_id='" . $order_id . "'");
    if($order_info['user_id'] != $_SESSION['user_id'])
    {
        show_message('操作有误,请重新操作');
    }
    
    $payment = payment_info($order_info['pay_id']);
    
    
    require_once (ROOT_PATH . 'includes/lib_clips.php');
    $payment = payment_info($order_info['pay_id']);
    $order_info['log_id'] = insert_pay_log( $order_info['order_id'] , $order_info['insure_money'], 3);
    if($order_info['pay_id']==16 || $order_info['pay_id']==17 || $order_info['pay_id']==18 ){
    	include_once(ROOT_PATH.'includes/modules/openepay/zhifutong.php');
    	$pay_obj = new zhifutong();
    }else{
    	include_once('includes/modules/payment/zhizunbao.php');
    	$pay_obj = new zhizunbao();
    }
    
    $orderUrl = $GLOBALS['ecs']->url();
    $order_pay=array(
            'order_amount' =>   $order_info['insure_money'] ,
            'user_id' => $order_info['user_id'],
            'log_id' => $order_info['log_id'],
            'order_id' => $order_info['order_id'],
    );
    //print_r($order_pay);
    $pay_online = $pay_obj->get_code($order_pay, $payment['pay_code'], $orderUrl . "user.php?act=insure");
    $pay_online = "<div style='margin-top:10px'>".$pay_online.'</div>';
    $smarty->assign('pay_online', $pay_online);
    
//     require_once (dirname(ROOT_PATH) . '/includes/modules/PHPKit-CMBC/php_java.php');
//     include_once (dirname(ROOT_PATH) . '/includes/modules/PHPKit-CMBC/CMBC_SDK_TOOLS.php');
//     $cmbc_sdk = new CMBC_SDK_TOOLS(0);
    
//     if ($payment['pay_code'] == "alipay") {
//         $selectTradeType = CMBC_SDK_TOOLS::API_ZFBQRCODE;
//     } elseif ($payment['pay_code'] == "weixin") {
//         $selectTradeType = CMBC_SDK_TOOLS::H5_WXJSAPI;
//         $redirectUrl = 'http://xiangbai.king51.com/mobile/user.php?act=bigfamily';
//     }
//     require_once (ROOT_PATH . 'includes/lib_clips.php');
//     $order_info['log_id'] = insert_pay_log($order_info['order_id'],$order_info['insure_money'], 3);
//     $payInfo = $cmbc_sdk->toCreateOrder($selectTradeType, $order_info['insure_money'], "投保" . $order_info['order_sn'], $order_info['log_id'], $GLOBALS['ecs']->get_domain() . "/testNotify.php", "",$redirectUrl);

//     if ($payInfo['code'] * 1 == 1) {
//         $sql = "UPDATE " . $GLOBALS['ecs']->table('pay_log') . " SET `pay_info`='" . $payInfo['data'] . "' where `log_id`='" . $order_info['log_id'] . "'";
//         //            echo $sql;exit;
//         $GLOBALS['db']->query($sql);
//         //生成支付代码
//         if ($redirectUrl)
//         {
//             $wx_button =  $payInfo['data'] ;
//         }
//         else 
//         {
//             $pay_online = $cmbc_sdk->toGetPayInfo($selectTradeType, $payInfo['data']);
//         }
//         $smarty->assign('pay_online', $pay_online);
//         $smarty->assign('wx_button', $wx_button);
//     } else {
//         show_message($payInfo['message']);
//     }    

    $smarty->assign('order_info', $order_info); // 页面标题
    $smarty->assign('page_title', '支付订单'); // 页面标题
    $smarty->display('mutual.dwt');
    exit();
}

if('mutual_redone' == $_REQUEST['act'])
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
    // 给货到付款的手续费加<span id>，以便改变配送的时候动态显示
    $payment_list = available_payment_list(1, 0, true, $_SESSION['extension_code'] == 'virtual_good' ? 1 : 0);
    $pay_balance_id = 0; // 当前配置于的余额支付的递增id
    if(isset($payment_list))
    {
        foreach($payment_list as $key =>$payment)
        {
            if($payment['is_cod'] == '1')
            {
                $payment_list[$key]['format_pay_fee'] = '<span id="ECS_CODFEE">' . $payment['format_pay_fee'] . '</span>';
            }
            /* 如果有易宝神州行支付 如果订单金额大于300 则不显示 */
            if($payment['pay_code'] == 'yeepayszx' && $total['amount'] > 300)
            {
                unset($payment_list[$key]);
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
    
    $smarty->assign('user_info', $user_info);
    $smarty->assign('pay_balance_id', $pay_balance_id);
    $smarty->assign('payment_list', $payment_list);
    // print_r($arr);
    $smarty->assign('mutual_info', $arr);
    $smarty->display('mutual.dwt');
}

// 保存互助信息
if($action == 'mutual_done')
{
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    if($_SESSION['user_id'] == 0)
    {
        /* 用户没有登录且没有选定匿名购物，转向到登录页面 */
        ecs_header("Location: user.php\n");
        exit();
    }
    
    $id = intval($_REQUEST['id']);
    
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

if('mutual_done_info' == $_REQUEST['act'])
{
    if(empty($_GET['order_id']))
    {
        show_message('操作有误,请重新操作');
    }
    $order_id = intval($_GET['order_id']);
    // 查询订单
    // 查询订单
    $order_info = $GLOBALS['db']->getRow("SELECT * FROM " . $GLOBALS['ecs']->table('mutual_info') . " where order_id='" . $order_id . "'");
    $order_info['title'] = $GLOBALS['db']->getOne("SELECT title FROM " . $GLOBALS['ecs']->table('mutual') . " where mutual_id = '" . $order_info['mutual_id'] . "' ");
    if($order_info['user_id'] != $_SESSION['user_id'])
    {
        show_message('操作有误,请重新操作');
    }
    $smarty->assign('order_info', $order_info); // 页面标题
    $smarty->assign('page_title', '支付订单'); // 页面标题
                                           
    
    $payment = payment_info($order_info['pay_id']);
    
    
    //重新生成pay_log
    require_once (ROOT_PATH . 'includes/lib_clips.php');
    require_once (ROOT_PATH . 'includes/lib_order.php');
    $payment = payment_info($order_info['pay_id']);
    $order_info['log_id'] = insert_pay_log($order_info['order_id'],$order_info['mutual_money'], 2);
 
    if($order_info['pay_id']==16 || $order_info['pay_id']==17 || $order_info['pay_id']==18 ){
    	include_once(ROOT_PATH.'includes/modules/openepay/zhifutong.php');
    	$pay_obj = new zhifutong();
    }else{
    	include_once('includes/modules/payment/zhizunbao.php');
    	$pay_obj = new zhizunbao();
    }
 
    $orderUrl = $GLOBALS['ecs']->url();
    $order_pay=array(
            'order_amount' =>   $order_info['mutual_money'] ,
            'user_id' => $order_info['user_id'],
            'log_id' => $order_info['log_id'],
            'order_id' => $order_info['order_id'],
    );
    $pay_online = $pay_obj->get_code($order_pay, $payment['pay_code'], $orderUrl . "user.php?act=mutual");
    $pay_online = "<div style='margin-top:10px'>".$pay_online.'</div>';
    $smarty->assign('pay_online', $pay_online);
//     require_once (dirname(ROOT_PATH) . '/includes/modules/PHPKit-CMBC/php_java.php');
//     include_once (dirname(ROOT_PATH) . '/includes/modules/PHPKit-CMBC/CMBC_SDK_TOOLS.php');
//     $cmbc_sdk = new CMBC_SDK_TOOLS(0);
    
//     if ($payment['pay_code'] == "alipay") {
//         $selectTradeType = CMBC_SDK_TOOLS::API_ZFBQRCODE;
//     } elseif ($payment['pay_code'] == "weixin") {
//         $selectTradeType = CMBC_SDK_TOOLS::H5_WXJSAPI;
//         $redirectUrl = 'http://xiangbai.king51.com/mobile/user.php?act=bigfamily';
//     }
//     require_once (ROOT_PATH . 'includes/lib_clips.php');
//     $order_info['log_id'] = insert_pay_log($order_info['order_id'],$order_info['mutual_money'], 2);
//     $payInfo = $cmbc_sdk->toCreateOrder($selectTradeType, $order_info['mutual_money'], "爱心互助" . $order_info['order_sn'], $order_info['log_id'], $GLOBALS['ecs']->get_domain() . "/testNotify.php", "",$redirectUrl);

//     if ($payInfo['code'] * 1 == 1) {
//         $sql = "UPDATE " . $GLOBALS['ecs']->table('pay_log') . " SET `pay_info`='" . $payInfo['data'] . "' where `log_id`='" . $order_info['log_id'] . "'";
//         //            echo $sql;exit;
//         $GLOBALS['db']->query($sql);
//         //生成支付代码
//         if ($redirectUrl)
//         {
//             $wx_button =  $payInfo['data'] ;
//         }
//         else
//         {
//             $pay_online = $cmbc_sdk->toGetPayInfo($selectTradeType, $payInfo['data']);
//         }
//         $smarty->assign('pay_online', $pay_online);
//         $smarty->assign('wx_button', $wx_button);
//     } else {
//         show_message($payInfo['message']);
//     }
    
    
    $smarty->display('mutual.dwt');
    exit();
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
        
        /* 修改支付按钮 针对微信支付 支付宝 */
        if($payment_info['pay_name'] == '支付宝')
        {
            $order['pay_online'] = array(
                'click' => 'onclick="window.location.href=\'./pay/alipayapi.php?out_trade_no=' . $order['log_id'] . '&total_fee=' . $order['order_amount'] . '\'"',
                'pay_name' => $payment_info['pay_name']
            );
        }
        if($payment_info['pay_name'] == '微信支付')
        {
            $order['pay_online'] = array(
                'click' => 'onclick="window.location.href=\'./weixinpay.php?out_trade_no=' . $order['log_id'] . '\'"',
                'pay_name' => $payment_info['pay_name']
            );
        }
        return $order;
    }
    return '';
}

?>