<?php

if (!defined('IN_ECS')) {
    die('Hacking attempt');
}

//商品管理权限
$purview['role_offline_man'] = 'role_offline_man'; //审核通过商品列表
$purview['role_offline_statics'] = 'role_offline_statics'; //审核通过商品列表
$purview['role_offline_order_m'] = 'role_offline_order_m'; //审核通过商品列表
$purview['role_points_man'] = 'role_points_man'; //审核通过商品列表
$purview['role_points_ex_man'] = 'role_points_ex_man'; //审核通过商品列表
$purview['role_yl_points_man'] = 'role_yl_points_man'; //审核通过商品列表
$purview['role_goods_man'] = 'goods_list'; // 商品列表
$purview['role_orders_man'] = 'role_orders_man'; //审核通过商品列表
$purview['role_account_man'] = 'role_account_man'; //审核通过商品列表
$purview['3_role_points_gz_ex'] = '3_role_points_gz_ex'; //审核通过商品列表
$purview['role_yl_points_apply'] = 'role_yl_points_apply'; //养老金申请


//商品管理权限
$purview['01_goods_list_pass1']		= 'goods_list';//审核通过商品列表
$purview['01_goods_list_pass2']		= 'goods_list';//未审核商品
$purview['01_goods_list_pass3']		= 'goods_list';//审核未通过商品列表
$purview['03_goods_add']			= 'goods_add';//添加新商品
$purview['04_category_list']		= 'category_list';//商品分类
$purview['05_comment_manage']		= 'comment_priv';//用户评论
$purview['05_order_comment']        = 'order_comment_priv';//代码增加 --订单评论
$purview['05_shaidan_manage']		= 'comment_manage';//
$purview['11_goods_trash']			=  'goods_trash';//商品回收站

//订单管理
$purview['01_order_list']			= 'order_list';//订单列表
$purview['03_order_query']			= 'order_query';//订单查询
$purview['04_merge_order']			= 'merge_order';//合并订单invoice_manage
$purview['05_edit_order_print']		= 'edit_order_print';//订单打印
$purview['06_undispose_booking']    = 'booking';
$purview['09_delivery_order']		= 'delivery_order';//发货单列表
$purview['10_back_order']			= 'back_order';//退货单列表
$purview['12_invoice_list']          = 'invoice_manage';

//店铺设置
$purview['08_shipping_list']		= 'ship_manage';//配送方式管理
$purview['admin_list']				= 'admin_list';//管理员列表
?>