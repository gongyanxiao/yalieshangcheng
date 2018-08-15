<?php

/**
 * ECSHOP 处理收回确认的页面
 * ============================================================================
 * 版权所有 2005-2011 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: receive.php 17217 2011-01-19 06:29:08Z liubo $
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/* 取得参数 */
$order_id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;  // 订单号
$consignee = !empty($_REQUEST['con']) ? rawurldecode(trim($_REQUEST['con'])) : ''; // 收货人

/* 查询订单信息 */
$sql = 'SELECT * FROM ' . $ecs->table('order_info') . " WHERE order_id = '$order_id'";
$order = $db->getRow($sql);

if (empty($order)) {
    $msg = $_LANG['order_not_exists'];
}
/* 检查订单 */ elseif ($order['shipping_status'] == SS_RECEIVED) {
    $msg = $_LANG['order_already_received'];
} elseif ($order['shipping_status'] != SS_SHIPPED) {
    $msg = $_LANG['order_invalid'];
} elseif ($order['consignee'] != $consignee) {
    $msg = $_LANG['order_invalid'];
} else {
    /* 修改订单发货状态为“确认收货” */
//    $sql = "UPDATE " . $ecs->table('order_info') . " SET shipping_status = '" . SS_RECEIVED . "' WHERE order_id = '$order_id'";
//    $db->query($sql);
//    
//    
//    if(affirm_received($order_id))
//    {
//        
//    }
//    
//    
//    /* 记录日志 */
//    order_action($order['order_sn'], $order['order_status'], SS_RECEIVED, $order['pay_status'], '', $_LANG['buyer']);
    //$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;


    require_once(ROOT_PATH . '/includes/lib_order.php');
    include_once (ROOT_PATH . 'includes/lib_transaction.php');

    //添加是否已经退款成功，退款成功，不可以再次确认收货
    $sql = "select status_back from " . $GLOBALS['ecs']->table("back_order") . " where order_id='" . $order_id . "'";
    $status_back = $db->getOne($sql);
    //0:审核通过,1:收到寄回商品,2:换回商品已寄出,3:完成退货/返修,4:退款(无需退货),5:审核中,6:申请被拒绝,7:管理员取消,8:用户自己取消
    if (!empty($status_back) && ($status_back * 1 == 1 || $status_back * 1 == 3)) {
        if ($status_back * 1 === 1) {
            //$GLOBALS['err']->show("当前商品不能退货，因为已经收到您寄回的商品，请等待处理", 'user.php?act=order_list');
            show_message("当前商品不能退货，因为已经收到您寄回的商品，请等待处理", $_LANG['order_list_lnk'], 'user.php?act=order_list');
        }
        if ($status_back * 1 === 3) {
            //$GLOBALS['err']->show("当前商品不能退货，因为已经退货完成", 'user.php?act=order_list');
            show_message("当前商品不能退货，因为已经退货完成", $_LANG['order_list_lnk'], 'user.php?act=order_list');
        }
        exit();
    }
    if (affirm_received($order_id, $order['user_id'])) {
//        ecs_header("Location: user.php?act=order_list\n");
//        exit();
    } else {
        $GLOBALS['err']->show($_LANG['order_list_lnk'], 'user.php?act=order_list');
    }

    $msg = $_LANG['act_ok'];
}

/* 显示模板 */
assign_template();
$position = assign_ur_here();
$smarty->assign('page_title', $position['title']);    // 页面标题
$smarty->assign('ur_here', $position['ur_here']);  // 当前位置

$smarty->assign('categories', get_categories_tree()); // 分类树
$smarty->assign('helps', get_shop_help());       // 网店帮助

assign_dynamic('receive');

$smarty->assign('msg', $msg);
$smarty->display('receive.dwt');
?>