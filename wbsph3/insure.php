<?php

/**
 * ECSHOP 自营商品
 * ============================================================================
 * 版权所有 2005-2011 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: search.php 17217 2011-01-19 06:29:08Z liubo $
 */

define('IN_ECS', true);
require (dirname(__FILE__) . '/includes/init.php');

/* 载入语言文件 */
require_once (ROOT_PATH . 'languages/' . $_CFG['lang'] . '/user.php');

$user_id = $_SESSION['user_id'];
$action = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : 'index';

$affiliate = unserialize($GLOBALS['_CFG']['affiliate']);
$smarty->assign('affiliate', $affiliate);
$back_act = '';

// 不需要登录的操作
$not_login_arr = array(
    'index'
);

// /* 未登录处理 */
// if(empty($_SESSION['user_id']))
// {
//     if(! in_array($action, $not_login_arr))
//     {
//         // 未登录提交数据。非正常途径提交数据！
//         show_message($_LANG['require_login'], array(
//             '</br>登录',
//             '</br>返回首页'
//         ), array(
//             'user.php?act=login',
//             $ecs->url()
//         ), 'error', false);
//     }
// }

 
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

// 定义保险类型

$sql = "SELECT `pension_name` as insure_title,`money` as insure_money ,`id` ,picture, pension_desc FROM " . $ecs->table('pension_infos') . " where `pension_status`=1";
// $pension_infos = $db->getAll($sql);
$baoxian = $db->getAll($sql);
// $baoxian = array(
//     '1' => array(
//         'id' => 1,
//         'insure_name' => '黄铜养老保险',
//         'insure_title' => '100000元保额',
//         'insure_money' => '10',
//         'insure_baoe' => '1000000',
//         'img_url' => $yanglaiimg_10 ,
//     ),
//     '2' => array(
//         'id' => 2,
//         'insure_name' => '白银养老保险',
//         'insure_title' => '200000元保额',
//         'insure_money' => '20',
//         'insure_baoe' => '2000000',
//         'img_url' => $yanglaiimg_20
//     ),
//     '3' => array(
//         'id' => 3,
//         'insure_name' => '黄金养老保险',
//         'insure_title' => '300000元保额',
//         'insure_money' => '30',
//         'insure_baoe' => '3000000',
//         'img_url' => $yanglaiimg_30
//     )
// );


require (ROOT_PATH . 'includes/lib_order.php');

// 查看保险的详情页
if($_REQUEST['act'] == 'insureView')
{

	if(empty($ur_here))
	{
		$ur_here = '投保详情';
	}
	assign_template();
	
	$position = assign_ur_here(0, '投保详情');
	$smarty->assign('page_title', "投保详情"); // 页面标题
	$smarty->assign('ur_here', $position['ur_here']); // 当前位置
	$smarty->assign('helps', get_shop_help()); // 网店帮助
	// 判断是否实名认证
	$pension_info= $db->getRow("SELECT *  FROM " . $ecs->table('pension_infos') . " WHERE id = ".$_REQUEST['id']);
	
	$smarty->assign('pension_info', $pension_info);
	$smarty->assign('act', "index");
	
	$smarty->display('pension_info.dwt');
}
// 开始投保
elseif($_REQUEST['act'] == 'insurebuy')
{
    $smarty->assign('page_title', '确认订单'); // 页面标题
                                           // 判断是否实名认证
    $user_info = $db->getRow("SELECT status, card , real_name , mobile_phone  FROM " . $ecs->table('users') . " WHERE user_id = '" . intval($user_id) . "' ");
    if($user_info['status'] != 1)
    {
        show_message('请先完成实名认证');
        exit();
    }
    
    // 判断选择的保额
    if(! in_array($_GET['id'], array(
        1,
        2,
        3
    )))
    {
        show_message('请选择保额');
    }
    
    $baodan = $baoxian[$_GET['id']];
    
    $smarty->assign('user_info', $user_info);
    $smarty->assign('baodan', $baodan);
    
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
    
    $smarty->display('insure_checkout.dwt');
}
elseif('insure_done' == $_REQUEST['act'])
{
    require_once (ROOT_PATH . 'includes/lib_clips.php');
    
    $smarty->assign('page_title', '确认订单'); // 页面标题
                                           // 判断保险是否存在
                                           // 判断选择的保额
    if(! in_array($_POST['baoxian_id'], array(
        1,
        2,
        3
    )))
    {
        show_message('请选择保额');
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
        'insure_money' => $baoxian[$_POST['baoxian_id']]['insure_money'],
        'insure_baoe' => $baoxian[$_POST['baoxian_id']]['insure_baoe'],
        'insure_id' => $baoxian[$_POST['baoxian_id']]['id']
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
    // $GLOBALS['db']->rollbackTrans();
    ecs_header('Location:insure.php?act=insure_done_info&order_id=' . $bdId . " \n");
    exit();
}
else if('insure_done_info' == $_REQUEST['act'])
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

    assign_template();
    
//     require_once (ROOT_PATH . 'includes/modules/PHPKit-CMBC/php_java.php');
//     include_once (ROOT_PATH . 'includes/modules/PHPKit-CMBC/CMBC_SDK_TOOLS.php');
    
//     $cmbc_sdk = new CMBC_SDK_TOOLS(0);
//     $payment = payment_info($order_info['pay_id']);
//     if ($payment['pay_code'] == "alipay") {
//         $selectTradeType = CMBC_SDK_TOOLS::API_ZFBQRCODE;
//     } elseif ($payment['pay_code'] == "weixin") {
//         $selectTradeType = CMBC_SDK_TOOLS::API_WXQRCODE;
//     }
    
//     require_once (ROOT_PATH . 'includes/lib_clips.php');
    
//     $order_info['log_id'] = insert_pay_log($order_info['order_id'],$order_info['insure_money'], 3);

//     $payInfo = $cmbc_sdk->toCreateOrder($selectTradeType, $order_info['insure_money'], "投保，订单号：" . $order_info['order_sn'], $order_info['log_id'], $GLOBALS['ecs']->get_domain() . "/testNotify.php", "");

//     if ($payInfo['code'] * 1 == 1) {
//         $sql = "UPDATE " . $GLOBALS['ecs']->table('pay_log') . " SET `pay_info`='" . $payInfo['data'] . "' where `log_id`='" . $order_info['log_id'] . "'";
//         $GLOBALS['db']->query($sql);
//         //生成支付代码
//         $pay_online = $cmbc_sdk->toGetPayInfo($selectTradeType, $payInfo['data']);
//         $smarty->assign('pay_online', $pay_online);
//         $smarty->assign('log_ids', $order_info['log_id']);
//     } else {
//         show_message('系统繁忙,请稍候再试', '我的投保', 'user.php?act=insure');
//         //show_message($payInfo['message']);
//     }

    
    //重新生成pay_log
    require_once (ROOT_PATH . 'includes/lib_clips.php');
    $payment = payment_info($order_info['pay_id']);
    $order_info['log_id'] = insert_pay_log( $order_info['order_id'] , $order_info['insure_money'], 3);
//     include_once('includes/modules/payment/zhizunbao.php');
//     include_once('includes/modules/openepay/zhifutong.php');
//     $pay_obj = new zhifutong();
    include_once('includes/modules/payment/zhizunbao.php');
    $pay_obj = new zhizunbao();
    $orderUrl = $GLOBALS['ecs']->url();
    $order_pay=array(
            'order_amount' =>   $order_info['insure_money'] ,
            'user_id' => $order_info['user_id'],
            'log_id' => $order_info['log_id'],
            'order_id' => $order_info['order_id'],
    );
    //print_r($order_pay);
    $pay_online = $pay_obj->get_code($order_pay, $payment['pay_code'], $orderUrl . "user.php?act=insure", "PC");
    $smarty->assign('pay_online', $pay_online);

    
    $smarty->assign('order_info', $order_info); // 页面标题
    $smarty->assign('page_title', '支付订单'); // 页面标题
    $smarty->display('insure_done.dwt');
}
else
{
    if(empty($ur_here))
    {
        $ur_here = '投保列表';
    }
    assign_template();
    
    $position = assign_ur_here(0, '投保列表');
    $smarty->assign('page_title', $position['title']); // 页面标题
    $smarty->assign('ur_here', $position['ur_here']); // 当前位置
    
    $smarty->assign('baoxian', $baoxian);
    
    $smarty->assign('helps', get_shop_help()); // 网店帮助
    $smarty->assign('promotion_info', get_promotion_info());
    /* www.68ecshop.com */
    $sql = "select g.cat_id, count(*) AS cat_count from " . $ecs->table('goods') . " AS g " . "WHERE g.is_delete = 0 AND g.is_on_sale = 1 AND g.is_alone_sale = 1 $attr_in " . "AND (( 1 " . $categories . $keywords . $brand . $min_price . $max_price . $intro . $outstock . " ) " . $tag_where . " ) " . " group by g.cat_id ";
    // echo $sql;
    $res_kcat = $db->query($sql);
    $kcat_list = array();
    while ($row_kcat = $db->fetchRow($res_kcat))
    {
        $kcat_list[$row_kcat['cat_id']] = $row_kcat['cat_count'];
    }
    $smarty->display('insure.dwt');
}

?>