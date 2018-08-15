<?php

/**
 * ECSHOP 订单管理
 * ============================================================================
 * 版权所有 2005-2010 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: yehuaixiao $
 * $Id: order.php 17219 2011-01-27 10:49:19Z yehuaixiao $
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
    admin_priv('offline_view');

    /* 模板赋值 */
    $smarty->assign('ur_here', "报单统计");
    $smarty->assign('is_suppliers', array(array("code" => 1, "name" => "商家"), array("code" => 2, "name" => "运营中心")));
    //$smarty->assign('action_link', array('href' => 'offline.php?act=query', 'text' => "线下订单"));

    $smarty->assign('full_page', 1);
    
    //查询所有大区
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('large_area')." ";
    $all_daqu = $GLOBALS['db']->getAll($sql);
    $smarty->assign('all_daqu', $all_daqu);
    
    
    /*
     *  大区   报单查询     获取会员管辖的城市
     * */
    $sql = 'SELECT region_id, region_name FROM ' . $GLOBALS['ecs']->table('region') . " WHERE parent_id = 1 "  ;
    $daqu_name =  $GLOBALS['db']->GetAll($sql);
    $smarty->assign('province_list', $daqu_name);

    $agency_list = get_offline_man_list();
    $smarty->assign('order_list', $agency_list['agency']);
    $smarty->assign('filter', $agency_list['filter']);
    $smarty->assign('record_count', $agency_list['record_count']);
    $smarty->assign('page_count', $agency_list['page_count']);

    /* 排序标记 */
    $sort_flag = sort_flag($agency_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('offline.htm');
} 

/* ------------------------------------------------------ */
//-- 排序、分页、查询
/* ------------------------------------------------------ */ 
elseif ($_REQUEST['act'] == 'query') {
    /* 检查权限 */
    admin_priv('role_offline_man');
    $agency_list = get_offline_man_list();
    $smarty->assign('order_list', $agency_list['agency']);
    $smarty->assign('filter', $agency_list['filter']);
    $smarty->assign('record_count', $agency_list['record_count']);
    $smarty->assign('page_count', $agency_list['page_count']);

    /* 排序标记 */
    $sort_flag = sort_flag($agency_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('offline.htm'), '', array('filter' => $agency_list['filter'], 'page_count' => $agency_list['page_count']));
}
elseif ($_REQUEST['act'] == 'export') {
    admin_priv('offline_view');
    header("Content-type: application/vnd.ms-excel; charset=gbk");
    header("Content-Disposition: attachment; filename=offline_orders.xls");

    $export = "<table border='1'><tr><td colspan='2'>运营中心名称</td><td colspan='2'>商家名称</td><td colspan='2'>报单人</td><td colspan='2'>会员名称</td><td colspan='2'>会员手机号</td><td colspan='2'>报单时间</td><td colspan='2'>订单金额</td><td colspan='2'>报单服务费</td><td colspan='2'>商品名称</td></tr>";
    $result = order_list(1);
    foreach ($result['orders'] as $key => $val) {
        $region_name = $val["region_name"];
        if (!empty($val['supplier_name'])&&!empty($val["region_name"])) {
            $region_name = "所属运营中心:" . $val["region_name"];
        }
        $real_name="";
        if(!empty($val['real_name']))
        {
            $real_name="(".$val['real_name'].")";
        }
        $export .= "<tr><td colspan='2'>" . $region_name . "</td><td colspan='2'>" . $val['supplier_name'] . "</td><td colspan='2'>" . $val['mobile_phone1'] . "".$real_name."</td><td colspan='2'>" . $val['user_name'] . "</td><td colspan='2'>" . $val['mobile_phone'] . "</td><td colspan='2'>" . $val['createtime'] . "</td><td colspan='2'>" . $val['order_amt'] . "</td><td colspan='2'>" . $val['order_bdf'] . "</td><td colspan='2'>" . $val['good_name'] . "</td></tr>";
    }
    $export .= "</table>";
    if (EC_CHARSET != 'gbk') {
        echo ecs_iconv(EC_CHARSET, 'gbk', $export) . "\t";
    } else {
        echo $export . "\t";
    }
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
    $filter['daqu'] = empty($_REQUEST['daqu']) ? 0 : addslashes($_REQUEST['daqu']);
    $filter['fanwei'] = empty($_REQUEST['fanwei']) ? 0 : addslashes($_REQUEST['fanwei']);
    
    /* 初始化时间查询条件 */
    $up_time  = strtotime($_REQUEST['start_time']) ;  //开始时间
    $end_time = strtotime($_REQUEST['end_time']);  //结束时间
    if ($up_time > 0 || $end_time>0 )
    {
        $up_time  = $up_time  > 0 ? $up_time  - 28800  : 0 ;
        $end_time = $end_time > 0 ? $end_time - 28800  : 0 ;
        $where = " and bd.createtime BETWEEN $up_time AND $end_time ";
    }

    switch ($filter['fanwei'] * 1) {

        case 0:
            
            break;
        case 1:

            /*
             *  大区   报单查询     获取会员管辖的城市
             * */
            if ($filter['daqu']>0)
            {
                $sql = 'SELECT region_id FROM ' . $GLOBALS['ecs']->table('region') . " WHERE parent_id = 1 and large_area_id='".$filter['daqu']."'"  ;
                $daqu_name =  $GLOBALS['db']->GetAll($sql);
                $daqu_names[] = 9999999;
                foreach ($daqu_name as $vals)
                {
                    $daqu_names[] = $vals['region_id'];
                }
                $child_province =   implode(',', $daqu_names);
                
                $where .= " and a.`province_id` in (".$child_province.")";
            }
            
            break;
        case 2:

            /*
             * 城市经理
             */

            // 省市区 检索
            if ( $filter['district'] > 0 )
            {
                //县级检索
                $where .= " and a.`district_id` = '".$filter['district']."' ";
            
            }
            elseif ($filter['city'] > 0)
            {
                //市区检索
                $where .= " and a.`citys_id` = '".$filter['city']."' ";
            }
            elseif ($filter['province'] > 0)
            {
                //县级检索
                $where .= " and a.`province_id` = '".$filter['province']."' ";
            }
            break;
            
        case 3:
            
            //联盟商家 搜索
            if (!empty($filter['supplier_name']) )
            {
                $where .= " and su.supplier_name like '".$filter['supplier_name']."' ";
            }
            break;
    }

    // 查询报单SQL语句
    $sql = "SELECT bd.id , u.`user_name` , u.`real_name` ,bd.order_amt ,bd.order_bdf , bd.createtime , bd.good_name ,bd.fp_url,bd.good_url"
        ." FROM ". $GLOBALS['ecs']->table('order_bd') ." as bd "
        ." left JOIN " . $GLOBALS['ecs']->table('supplier_admin_user') . " a ON bd.`member_id`   = a.user_id "
        ." left JOIN " . $GLOBALS['ecs']->table('users') . " u ON u.user_id  = bd.user_id "
        ." left JOIN " . $GLOBALS['ecs']->table('supplier') . " su ON su.supplier_id  = bd.real_supplier_id "
        ."where 1 $where ORDER BY $filter[sort_by] $filter[sort_order] ";

    // 统计所有报单费 //
    $sql_sum = "SELECT COUNT(*) as num, sum(bd.order_amt) as order_amt_sum , sum(bd.order_bdf) as order_bdf_sum"
        ." FROM ". $GLOBALS['ecs']->table('order_bd') ." as bd "
        ." left JOIN " . $GLOBALS['ecs']->table('supplier_admin_user') . " a ON bd.`member_id`   = a.user_id "
        ." left  JOIN " . $GLOBALS['ecs']->table('users') . " u ON u.user_id  = a.user_id "
        ." left  JOIN " . $GLOBALS['ecs']->table('supplier') . " su ON su.supplier_id  = bd.real_supplier_id "
        ." where 1 $where ";
    
//     // 查询报单SQL语句
//     $sql = "SELECT bd.order_sn as id , su.supplier_name, bd.all_money , bd.rebate_money ,  bd.rebate_money , bd.`result_money`   , bd.add_time "
//         ." FROM ". $GLOBALS['ecs']->table('supplier_rebate_log') ." as bd "
//         ." left JOIN " . $GLOBALS['ecs']->table('supplier_admin_user') . " a ON bd.`supplier_id`   = a.uid "
//         ." left  JOIN " . $GLOBALS['ecs']->table('supplier') . " su ON su.user_id  = bd.supplier_id "
//         ." where ( bd.`is_offline` = 0 or bd.`is_offline`  = 2 )  $where ORDER BY $filter[sort_by] $filter[sort_order] ";
//         // 统计所有报单费 //
//     $sql_sum = "SELECT COUNT(*) as num, sum(bd.all_money) as all_money_all , sum(bd.rebate_money) as rebate_money_all , sum(bd.result_money) as result_money_all"
//         ." FROM ". $GLOBALS['ecs']->table('supplier_rebate_log') ." as bd "
//         ." left JOIN " . $GLOBALS['ecs']->table('supplier_admin_user') . " a ON bd.`supplier_id`   = a.uid "
//         ." left  JOIN " . $GLOBALS['ecs']->table('supplier') . " su ON su.user_id  = bd.supplier_id "
//         ." where ( bd.`is_offline` = 0 or bd.`is_offline`  = 2 )  $where ";
        
//     echo  $sql;
    //exit() ;
    $order_sum = $GLOBALS['db']->getRow($sql_sum);
    $filter['record_count'] = $order_sum['num'];

    $filter = page_and_size($filter);
    /* 查询记录 */
    set_filter($filter, $sql);

    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);

    $arr = array();
    while ($rows = $GLOBALS['db']->fetchRow($res)) {
        $rows["order_time"] = local_date("Y-m-d H:i:s", $rows['createtime']);
        $arr[] = $rows;
    }

    /* 在分页数组中添加 统计数据 */
    $filter['order_amt_sum'] = $order_sum['order_amt_sum'] ;
    $filter['order_bdf_sum'] = $order_sum['order_bdf_sum'] ;

    return array('agency' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count'] ? $filter['record_count'] : 10 );
}

?>