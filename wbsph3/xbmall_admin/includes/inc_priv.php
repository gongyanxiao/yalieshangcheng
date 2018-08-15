<?php

/**
 * ECSHOP 权限对照表
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: sunxiaodong $
 * $Id: inc_priv.php 15503 2008-12-24 09:22:45Z sunxiaodong $
 */
if (!defined('IN_ECS')) {
    die('Hacking attempt');
}

// 商品管理权限
$purview['01_goods_list'] = array(
		'goods_manage', 'remove_back','goods_manage_list'
);
$purview['goods_manage_list'] = 'goods_manage_list';

/* 代码增加_start By supplier.68ecshop.com */
$purview['02_supplier_goods_list'] = array(
		'goods_manage', 'goods_manage_list','remove_back'
);
$purview['03_goods_add'] = 'goods_manage';
$purview['04_category_list'] = array(
		'cat_manage','cat_manage_list','cat_drop'
); // 分类添加、分类转移和删除
/* 代码增加_end By supplier.68ecshop.com */
$purview['05_comment_manage'] = array('comment_priv','comment_priv_list');
$purview['05_order_comment'] =  array('order_comment_priv','order_comment_priv_list'); // 代码增加 --订单评论
$purview['06_goods_brand_list'] = array('brand_manage','brand_manage_list');
$purview['08_goods_type'] = array('attr_manage','attr_manage_list'); // 商品属性
$purview['11_goods_trash'] = array(
		'goods_manage','goods_manage_list','remove_back'
);
$purview['12_batch_pic'] = 'picture_batch';
$purview['13_batch_add'] = 'goods_batch';
$purview['14_goods_export'] = 'goods_export';
$purview['15_batch_edit'] = 'goods_batch';
$purview['16_goods_script'] = 'gen_goods_script';
$purview['17_tag_manage'] = 'tag_manage';
/* 代码删除 By  www.68ecshop.com Start */
//$purview['50_virtual_card_list'] = 'virualcard';
//$purview['51_virtual_card_add'] = 'virualcard';
//$purview['52_virtual_card_change'] = 'virualcard';
/* 代码删除 By  www.68ecshop.com End */
$purview['goods_auto'] = 'goods_auto';
$purview['scan_store'] = 'scan_store';
$purview['05_shaidan_manage'] = 'shaidan_manage';
$purview['05_question_manage'] = 'question_manage';
$purview['05_goods_tags'] = 'tag_manage';

// 促销管理权限
/* 代码删除 By  www.68ecshop.com Start */
//$purview['02_snatch_list'] = 'snatch_manage';
/* 代码删除 By  www.68ecshop.com End */
$purview['04_bonustype_list'] = 'bonus_manage';
/* 代码删除 By  www.68ecshop.com Start */
//$purview['06_pack_list'] = 'pack';
//$purview['07_card_list'] = 'card_manage';
//$purview['08_group_buy'] = 'group_by';
/* 代码删除 By  www.68ecshop.com End */
$purview['09_topic'] = array('topic_manage','topic_manage_list');

$purview['10_auction'] = 'auction';
$purview['12_favourable'] = 'favourable';
$purview['13_wholesale'] = 'whole_sale';
$purview['14_package_list'] = 'package_manage';
// $purview['02_snatch_list'] = 'gift_manage'; //赠品管理
$purview['04_back_order'] = 'back_view';
$purview['15_exchange_goods'] = array('exchange_goods','goods_manage_list'); // 赠品管理
$purview['11_kuaidi_order'] = 'order_view';
$purview['12_kuaidi_order2'] = 'order_view';
$purview['25_pre_sale_list'] = 'pre_sale'; // 预售管理
// 文章管理权限
$purview['02_articlecat_list'] = array('article_cat','article_cat_list');
$purview['03_article_list'] = array('article_manage','article_manage_list');
$purview['article_auto'] = 'article_auto';
$purview['vote_list'] = 'vote_priv';

// 会员管理权限
$purview['03_users_list'] = 'users_manage';

$purview['04_users_export'] = 'users_manage'; // 代码增加 By www.68ecshop.com

$purview['04_users_add'] = 'users_manage';
$purview['05_user_rank_list'] = array('user_rank','user_rank_list');

$purview['09_user_account'] = array('surplus_manage','surplus_manage_list');
$purview['06_list_integrate'] = 'integrate_users';
$purview['08_unreply_msg'] = array('feedback_priv','feedback_priv_list');
$purview['10_user_account_manage'] = array('account_manage','account_manage_list');
$purview['09_postman_list'] = array('users_manage','users_manage_list');

// 权限管理
$purview['admin_logs'] = array(
		'logs_manage', 'logs_drop','logs_manage_list'
);
$purview['admin_list'] = array(
		'admin_manage', 'admin_drop', 'allot_priv','admin_manage_list'
);
$purview['agency_list'] = 'agency_manage';
$purview['suppliers_list'] = 'suppliers_manage'; // 供货商
$purview['admin_role'] = array('role_manage','role_manage_list');
$purview['customer'] = 'customer'; // 在线聊天客服管理
$purview['third_customer'] = array('third_customer','third_customer_list'); // 三方客服
// 商店设置权限
$purview['01_shop_config'] = array('shop_config','shop_config_list');

$purview['shop_authorized'] = 'shop_authorized';
$purview['shp_webcollect'] = 'webcollect_manage';
$purview['02_payment_list'] = array('payment','payment_list');
$purview['03_shipping_list'] = array(
		'ship_manage', 'shiparea_manage','ship_manage_list', 'shiparea_manage_list'
);
$purview['04_mail_settings'] = array('shop_config','shop_config_mail_view');

$purview['05_area_list'] = array('area_manage','area_manage_list');
$purview['07_cron_schcron'] = array('cron','cron_list');
$purview['08_friendlink_list'] = 'friendlink';
$purview['sitemap'] = 'sitemap';
$purview['check_file_priv'] = 'file_priv';
$purview['captcha_manage'] = array('shop_config','captcha_manage_list');

$purview['file_check'] = 'file_check';
$purview['navigator'] = array('navigator','navigator_list');
$purview['flashplay'] =  array('flash_manage','flash_manage_list');
$purview['ucenter_setup'] = 'integrate_users';
$purview['021_reg_fields'] = 'reg_fields';
$purview['website'] = 'website_login';
$purview['user_blanklist'] = array('user_blanklist','user_blanklist_list');

// 广告管理

$purview['ad_position'] = array('ad_position','ad_position_list');
$purview['ad_list'] = array('ad_list','ad_list_list');


// 订单管理权限
/* 代码增加_start By supplier.68ecshop.com */
$purview['01_order_list'] = array('order_view','order_view_list');
$purview['02_supplier_order'] = array('02_supplier_order_list');
/* 代码增加_end By supplier.68ecshop.com */
$purview['03_order_query'] = 'order_view';
$purview['04_merge_order'] = 'order_os_edit';
$purview['05_edit_order_print'] = 'order_os_edit';
$purview['06_undispose_booking'] = 'booking';
$purview['08_add_order'] = 'order_edit';
$purview['09_delivery_order'] = array('delivery_view','delivery_view_list');
$purview['10_back_order'] = array('back_view','back_view_list');
$purview['11_supplier_back_order'] =  array('back_view','back_view_list');
$purview['12_order_excel'] = 'order_view';
$purview['12_invoice_list'] = array('invoice_manage','invoice_manage_list');
$purview['13_offline'] = 'offline_view';
$purview['12_order_statics'] = array('12_order_statics','12_order_statics_list');
$purview['13_order_statics'] = array('13_order_statics','13_order_statics_list');
$purview['12_order_statics_up'] = '12_order_statics_up';
$purview['13_offline'] = '13_offline';




// 报表统计权限
$purview['flow_stats'] = 'client_flow_stats';
/* 代码添加_START By www.68ecshop.com */
$purview['keyword'] = 'client_flow_stats';
/* 代码添加_SEND By www.68ecshop.com */
$purview['report_guest'] = 'client_flow_stats';
$purview['report_users'] = 'client_flow_stats';
$purview['visit_buy_per'] = 'client_flow_stats';
$purview['searchengine_stats'] = 'client_flow_stats';
$purview['report_order'] = 'sale_order_stats';
$purview['report_sell'] = 'sale_order_stats';
$purview['sale_list'] = 'sale_order_stats';
$purview['sell_stats'] = 'sale_order_stats';
/* 代码增加 By  www.68ecshop.com Start */
$purview['industry_stats'] = 'industry_stats'; // 行业分析
$purview['users_stats'] = 'users_stats'; // 会员统计
$purview['shops_stats'] = 'shops_stats'; // 店铺统计
$purview['orders_stats'] = 'orders_stats'; // 订单统计
$purview['goods_stats'] = 'goods_stats'; // 商品分析
$purview['sells_stats'] = 'sells_stats'; // 销售报告
$purview['after_sells_stats'] = 'after_sells_stats'; // 售后统计
$purview['client_keyword_stats'] = 'client_keyword_stats'; // 客户搜索记录
$purview['client_flow_stats'] = 'client_flow_stats'; // 客户统计
/* 代码增加 By  www.68ecshop.com End */

// 模板管理
$purview['02_template_select'] = 'template_select';
$purview['03_template_setup'] = array('template_setup','template_setup_list');

$purview['04_template_library'] = 'library_manage';
$purview['05_edit_languages'] = 'lang_edit';
$purview['06_template_backup'] = 'backup_setting';
$purview['mail_template_manage'] = 'mail_template';

// 数据库管理
$purview['02_db_manage'] = array(
    'db_backup', 'db_renew'
);
$purview['03_db_optimize'] = 'db_optimize';
$purview['04_sql_query'] = 'sql_query';
//$purview['convert'] = 'convert';
// 短信管理
$purview['02_sms_my_info'] = 'my_info';
$purview['03_sms_send'] = 'sms_send';
$purview['04_sms_charge'] = 'sms_charge';
$purview['05_sms_send_history'] = 'send_history';
$purview['06_sms_charge_history'] = 'charge_history';

// 推荐管理
$purview['affiliate'] = 'affiliate';
$purview['affiliate_ck'] = 'affiliate_ck';

// 邮件群发管理
$purview['attention_list'] = 'attention_list';
$purview['email_list'] = 'email_list';
$purview['magazine_list'] = 'magazine_list';
$purview['view_sendlist'] = 'view_sendlist';
$purview['sendmail'] = 'send_mail';

/* 代码增加_start By supplier.68ecshop.com */
$purview['05_supplier_rank'] = 'supplier_rank';
$purview['01_supplier_reg'] = 'supplier_manage';
$purview['02_supplier_list'] = 'supplier_manage';
$purview['03_rebate_nopay'] = 'supplier_rebate';
//$purview['03_rebate_pay'] = 'supplier_rebate';
$purview['04_shop_category'] = 'supplier_manage';
$purview['05_shop_street'] = 'supplier_manage';
$purview['06_supplier_tag'] = 'supplier_tag';
/* 代码增加_end By supplier.68ecshop.com */
// 微信权限
// $purview['weixin_config']= 'weixin_config';
// $purview['weixin_addconfig']= 'weixin_addconfig';
// $purview['weixin_menu']= 'weixin_menu';
// $purview['weixin_notice']= 'weixin_notice';
// $purview['weixin_keywords']= 'weixin_keywords';
// $purview['weixin_fans']= 'weixin_fans';
// $purview['weixin_news']= 'weixin_news';
// $purview['weixin_addqcode']= 'weixin_addqcode';
// $purview['weixin_qcode']= 'weixin_qcode';
// $purview['weixin_reg']= 'weixin_reg';
// 活动管理
// $purview['weixin_act']= 'weixin_act';
// $purview['weixin_award']= 'weixin_award';
// $purview['weixin_oauth']= 'weixin_oauth';
// $purview['weixin_qiandao']= 'weixin_qiandao';
// $purview['weixin_addkey']= 'weixin_addkey';
$purview['website'] = 'website';
/* 代码增加_start By www.68ecshop.com */
$purview['16_takegoods_list'] = 'takegoods_list';
$purview['16_takegoods_order'] = 'takegoods_order';
$purview['website'] = 'website';

$purview['area_cat'] = 'area_cat';
/* 代码增加_end By www.68ecshop.com */

// 自提点管理权限
$purview['pickup_point_list'] = 'pickup_point_manage';
$purview['pickup_point_add'] = 'pickup_point_manage';
$purview['pickup_point_batch_add'] = 'pickup_point_batch';
// 即时通信
$purview['chat'] = 'chat';
$purview['chat_settings'] = 'chat_settings';
$purview['customer'] = 'customer';

//虚拟团购管理
$purview['virtual'] = 'virtual';
$purview['virtual_goods_add'] = 'virtual_goods_add';
$purview['virtual_goods_sup'] = 'virtual_goods_sup';
$purview['virtual_goods_list'] = 'virtual_goods_list';
$purview['virtual_card_list'] = 'virtual_card_list';
$purview['virtual_validate'] = 'virtual_validate';
$purview['virtual_category'] = 'virtual_category';
$purview['virtual_district'] = 'virtual_district';

$purview['22_large_area'] = 'large_area';
$purview['23_users_role'] = array('users_role','users_role_list');

$purview['30_accounts_manage'] = 'accounts_manage';
$purview['1_user_manage'] = array('1_user_manage','users_manage_list');
$purview['2_online_suppliers'] = array('2_online_suppliers','2_online_suppliers_list');
$purview['3_offline_suppliers'] = array('3_offline_suppliers','3_offline_suppliers_list');
$purview['4_city_manage'] = array('4_city_manage','4_city_manage_list');
$purview['5_large_area_manage'] = array('5_large_area_manage','5_large_area_manage_list');

$purview['31_scores_manage'] = 'scores_manage';
$purview['1_scores_manage'] =  array('1_scores_manage','surplus_manage_list');
$purview['2_scores_recharge'] = array('2_scores_recharge','2_scores_recharge_list');
$purview['3_scores_exchange'] = array('3_scores_exchange','3_scores_exchange_list');
$purview['4_scores_exchange_gz'] = '4_scores_exchange_gz';
$purview['5_scores_exchange_djtfh'] = '5_scores_exchange_djtfh';


$purview['order_statics'] = 'order_statics';
$purview['32_finance'] = '32_finance';
$purview['finance_statics'] = array('finance_statics');


$purview['34_pension']='34_pension';
$purview['1_pension_manage']=array('1_pension_manage','1_pension_manage_list');
$purview['2_pension_apply']=array('2_pension_apply','2_pension_apply_list');

//爱心互助
// $purview['33_Mutual'] = '33_Mutual'; // 投保列表
// $purview['1_insure'] = array('1_insure','1_insure_list'); //
// $purview['2_mutual'] = array('2_mutual','2_mutual_list'); //
// $purview['3_mutual'] = 


$purview['tk_admin_user'] = array('tk_ji_ben_xin_xi'); //

// $purview['tk_admin_shen_qing'] ='tk_admin_shen_qing';
// $purview['tk_admin_user_info'] ='tk_admin_user_info';
// $purview['tk_admin_li_cai_list'] ='tk_admin_li_cai_list';

$purview['ben_ren_statics'] ='ben_ren_statics';

$purview['income_statics'] ='income_statics';
$purview['ji_fen_gou_wu_statics'] ='ji_fen_gou_wu_statics';
$purview['statics_dong_jie'] ='statics_dong_jie';
$purview['tui_jian_statics'] ='tui_jian_statics';



$purview['zt_user_cj_list']='zt_user_cj_list';
$purview['xxtz_cz_list']='xxtz_cz_list';
$purview['xxtz_list']='xxtz_list';
$purview['zt_cash_exchange_cj']='zt_cash_exchange_cj';
$purview['xxcz_fa_fang_manager']='xxcz_fa_fang_manager'; 
$purview['zt_setting_list']='zt_setting_list';
$purview['xxtzzs_list']='xxtzzs_list';
$purview['xxtz_tree']='xxtz_tree';
$purview['tong_ji']='tong_ji';
$purview['tong_ji_fen_hong']='tong_ji_fen_hong';
// $purview['jygj'] =array('zt_user_cj_list','xxtz_cz_list','xxtz_list','zt_cash_exchange_cj'
// 		,'xxcz_fa_fang_manager','zt_setting_list','xxtzzs_list','xxtz_tree','tong_ji','tong_ji_fen_hong');

$purview['tui_jian_jiang_fa_huo'] =array('tui_jian_jiang_fa_huo');
$purview['cai_gou_ding_dan'] =array('cai_gou_ding_dan');


 

?>