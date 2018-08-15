<?php

if (!defined('IN_ECS')) {
    die('Hacking attempt');
}

$modules['role_offline']['role_offline_man'] = 'role_offline_man.php?act=list';         // 商品列表
$modules['role_offline']['role_offline_statics'] = 'role_offline_statics.php?act=list';         // 商品列表
$modules['role_offline_order']['role_offline_order_m'] = 'role_offline_order.php?act=list';         // 商品列表
$modules['role_points']['role_points_man'] = 'role_points_man.php?act=list';         // 我的积分
$modules['role_points']['role_points_ex_man'] = 'role_points_ex.php?act=list';         // 货款积分兑换
$modules['role_yl_points']['role_yl_points_man'] = 'role_yl_points_man.php?act=list';         // 养老金
$modules['role_points']['3_role_points_gz_ex'] = 'role_points_gz_ex.php?act=list';         // 基数奖励兑换
$modules['role_yl_points']['role_yl_points_apply'] = 'role_yl_points_apply.php?act=list';         // 养老金申请

/* 我的商品 */
$modules['role_goods']['01_goods_list_pass1'] = 'goods.php?act=list&supplier_status=1';         // 商品列表
$modules['role_goods']['01_goods_list_pass2'] = 'goods.php?act=list&supplier_status=0';         // 商品列表
$modules['role_goods']['01_goods_list_pass3'] = 'goods.php?act=list&supplier_status=-1';         // 商品列表
$modules['role_goods']['05_comment_manage'] = 'comment_manage.php?act=list'; //评论
$modules['role_goods']['11_goods_trash'] = 'goods.php?act=trash';        // 商品回收站
$modules['role_goods']['04_category_list'] = 'category.php?act=list';
$modules['role_goods']['05_order_comment'] = 'order_comment.php?act=list';

/* 线上订单 */
$modules['role_orders']['01_order_list'] = 'order.php?act=list';
$modules['role_orders']['09_delivery_order'] = 'order.php?act=delivery_list';
$modules['role_orders']['10_back_order'] = 'back.php?act=back_list';  //代码修改 By www.68ecshop.com
$modules['role_orders']['12_invoice_list'] = 'order.php?act=invoice_list';

$modules['role_account']['role_account_man'] = 'role_account_man.php?act=list';         // 商品列表

$modules['z_dianpu_set']['08_shipping_list'] = 'shipping.php?act=list';
$modules['z_dianpu_set']['admin_list'] = 'privilege.php?act=list';
?>
