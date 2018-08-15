<?php

/**
 * 线上订单统计
 * ============================================================================
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_order.php');
require_once(ROOT_PATH . 'includes/lib_goods.php');

/* ------------------------------------------------------ */
//-- 订单查询
/* ------------------------------------------------------ */

if ($_REQUEST['act'] == 'list') {
    /* 检查权限 */
    admin_priv('12_order_statics_up');

    /* 模板赋值 */
    $smarty->assign('ur_here', "商家线上订单统计");
    //$smarty->assign('action_link', array('href' => 'offline.php?act=query', 'text' => "线下订单"));

    $smarty->assign('full_page', 1);

    $agency_list = get_offline_man_list();
    $smarty->assign('order_list', $agency_list['agency']);
    $smarty->assign('filter', $agency_list['filter']);
    $smarty->assign('record_count', $agency_list['record_count']);
    $smarty->assign('page_count', $agency_list['page_count']);

    /* 排序标记 */
    $sort_flag = sort_flag($agency_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    /*
     * 城市列表
     */
    $sql = 'SELECT region_id, region_name FROM ' . $GLOBALS ['ecs']->table ( 'region' ) . " WHERE parent_id = 1 ";
    $daqu_name = $GLOBALS ['db']->GetAll ( $sql );
    $smarty->assign ( 'province_list', $daqu_name );
    
    assign_query_info();
    $smarty->display('order_statics.htm');
} 

/* ------------------------------------------------------ */
//-- 排序、分页、查询
/* ------------------------------------------------------ */ 
elseif ($_REQUEST['act'] == 'query') {
    /* 检查权限 */
    admin_priv('12_order_statics_up');
    $agency_list = get_offline_man_list();
    $smarty->assign('order_list', $agency_list['agency']);
    $smarty->assign('filter', $agency_list['filter']);
    $smarty->assign('record_count', $agency_list['record_count']);
    $smarty->assign('page_count', $agency_list['page_count']);

    /* 排序标记 */
    $sort_flag = sort_flag($agency_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('order_statics.htm'), '', array('filter' => $agency_list['filter'], 'page_count' => $agency_list['page_count']));
}
/**
 * 取得办事处列表
 * @return  array
 */
function get_offline_man_list() {
    
//    $result = get_filter();
//    if ($result === false) {
    /* 初始化分页参数 */
    $filter = array();
    $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'id' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
    $filter['query_type'] = empty($_REQUEST['query_type']) ? 0 : intval($_REQUEST['query_type']);
    $filter['province'] = empty($_REQUEST['province']) ? 0 : intval($_REQUEST['province']);
    $filter['city'] = empty($_REQUEST['city']) ? 0 : intval($_REQUEST['city']);
    $filter['district'] = empty($_REQUEST['district']) ? 0 : intval($_REQUEST['district']);
    $filter['supplier_name'] = empty($_REQUEST['supplier_name']) ? 0 : addslashes($_REQUEST['supplier_name']);
    $filter['supplier_mobile_phone'] = empty($_REQUEST['supplier_mobile_phone']) ? 0 : addslashes($_REQUEST['supplier_mobile_phone']);
    $filter['supplier_real_name'] = empty($_REQUEST['supplier_real_name']) ? 0 : addslashes($_REQUEST['supplier_real_name']);
    
    /* 初始化时间查询条件 */
    $up_time  = strtotime($_REQUEST['start_time']) ;  //开始时间
    $end_time = strtotime($_REQUEST['end_time']);  //结束时间
    if ($up_time > 0 || $end_time>0 )
    {
        $up_time  = $up_time  > 0 ? $up_time  - 28800  : 0 ;
        $end_time = $end_time > 0 ? $end_time - 28800  : 9491207314 ;
        $where = " and bd.add_time BETWEEN $up_time AND $end_time ";
    }
    
    //联盟商家 搜索
    if (!empty($filter['supplier_name']) )
    {
        $where .= " and su.supplier_name like '%".$filter['supplier_name']."%' ";
    }
    
    if (!empty($filter['supplier_real_name']) )
    {
    	$where .= " and user2.real_name like '%".$filter['supplier_real_name']."%' ";
    }
    
    
    if (!empty($filter['supplier_mobile_phone']) )
    {
    	$where .= " and user2.mobile_phone like '%".$filter['supplier_mobile_phone']."%' ";
    }
    
    
    if ($filter ["province"] != 0) {
    	$where .= " and a.province_id=" . $filter ["province"];
    }
    if ($filter ["city"] != 0) {
    	$where .= " and a.city_id=" . $filter ["city"];
    }
    if ($filter ["district"] != 0) {
    	$where .= " and a.district_id=" . $filter ["district"];
    }
    // 查询报单SQL语句
    $sql = "SELECT user1.real_name as user1_real_name,user1.mobile_phone as user1_mobile_phone,user2.real_name as user2_real_name,user2.mobile_phone as user2_mobile_phone, bd.order_sn as id , a.district_id, a.uid , su.supplier_name, bd.all_money , bd.rebate_money ,  bd.rebate_money , bd.`result_money`   , bd.add_time, re1.region_name as province,re2.region_name as city,re3.region_name as district "
    		." FROM ". $GLOBALS['ecs']->table('supplier_rebate_log') ." as bd "
    				." left JOIN " . $GLOBALS['ecs']->table('supplier_admin_user') . " a ON bd.`member_id`   = a.user_id "
    			 ." left  JOIN " . $GLOBALS['ecs']->table('supplier') . " su ON su.supplier_id  = a.supplier_id "
    			 ." left  JOIN " . $GLOBALS['ecs']->table('region') . " re1 ON a.province_id=re1.region_id "
    		     ." left  JOIN " . $GLOBALS['ecs']->table('region') . " re2 ON a.citys_id=re2.region_id "
    			 ." left  JOIN " . $GLOBALS['ecs']->table('region') . " re3 ON a.district_id=re3.region_id "
    			 ." left  JOIN " . $GLOBALS['ecs']->table('users') . "  user2 ON bd.supplier_id=user2.user_id "//做单者信息
    			 ." left  JOIN " . $GLOBALS['ecs']->table('order_bd') . "   orderBD ON orderBD.order_sn = bd.order_sn "//做单者信息
    			 ." left  JOIN " . $GLOBALS['ecs']->table('users') . "  user1 ON orderBD.user_id = user1.user_id "//做单者信息
                 ." where ( bd.`is_offline` = 0 )  $where ORDER BY $filter[sort_by] $filter[sort_order] ";
    
    
    // 统计所有报单费 //
    $sql_sum = "SELECT COUNT(*) as num, sum(bd.all_money) as all_money_all , sum(bd.rebate_money) as rebate_money_all , sum(bd.result_money) as result_money_all"
                ." FROM ". $GLOBALS['ecs']->table('supplier_rebate_log') ." as bd "
//                 ." left JOIN " . $GLOBALS['ecs']->table('supplier_admin_user') . " a ON bd.`supplier_id`   = a.uid "
                ." left  JOIN " . $GLOBALS['ecs']->table('supplier') . " su ON su.supplier_id  = bd.real_supplier_id "
                ." left  JOIN " . $GLOBALS['ecs']->table('users') . "  user2 ON bd.supplier_id=user2.user_id "//做单者信息
                ." where ( bd.`is_offline` = 0 )  $where ";

    $order_sum = $GLOBALS['db']->getRow($sql_sum);
    $filter['record_count'] = $order_sum['num'];

    $filter = page_and_size($filter);
    /* 查询记录 */
    set_filter($filter, $sql);

    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);

    $arr = array();
    while ($rows = $GLOBALS['db']->fetchRow($res)) {
        $rows["add_time"] = local_date("Y-m-d H:i:s", $rows['add_time']);
        $arr[] = $rows;
    }
    
    /* 在分页数组中添加 统计数据 */
    $filter['all_money_all']    = $order_sum['all_money_all'] ;
    $filter['rebate_money_all'] = $order_sum['rebate_money_all'] ;
    $filter['result_money_all'] = $order_sum['result_money_all'] ;

    return array('agency' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count'] );
}
?>