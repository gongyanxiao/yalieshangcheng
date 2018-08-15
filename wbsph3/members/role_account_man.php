<?php

/**
 * ECSHOP 管理中心办事处管理
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: agency.php 17217 2011-01-19 06:29:08Z liubo $
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/* ------------------------------------------------------ */
//-- 办事处列表
/* ------------------------------------------------------ */
if ($_REQUEST['act'] == 'list') {
    
    /* 检查权限 */
    admin_priv('role_account_man');
    $smarty->assign('full_page', 1);

    
    /* 取得大区 下的所有地区  */
    if  ($_SESSION['member_role'] * 1 == 1) 
    {

        /*
         *  大区   报单查询     获取会员管辖的城市
         * */
        $sql = 'SELECT region_id, region_name FROM ' . $GLOBALS['ecs']->table('region') . " WHERE parent_id = 1 and large_area_id='".$_SESSION['member_large_area_id']."'"  ;
        $daqu_name =  $GLOBALS['db']->GetAll($sql);
        $smarty->assign('province_list', $daqu_name);
    }
    else if ($_SESSION['member_role'] * 1 == 2)
    {
        /*
         * 城市经理
         */
        
        //查询 是哪一级城市经理
        $sql =" SELECT citys_id , province_id , `district_id`  FROM ".$GLOBALS['ecs']->table('supplier_admin_user')." WHERE `user_id` = '".$_SESSION['member_user_id']."' ";
        $child_diqu = $GLOBALS['db']->getRow($sql);
        if ($child_diqu['citys_id'] > 0)
        {
            $sql = 'SELECT region_id, region_name FROM ' . $GLOBALS['ecs']->table('region') . " WHERE parent_id = ".$child_diqu['citys_id']." ";
            $daqu_name =  $GLOBALS['db']->GetAll($sql);
            $smarty->assign('district_list', $daqu_name );
        }
        elseif ($child_diqu['province_id'] > 0)
        {
            $sql = 'SELECT region_id, region_name FROM ' . $GLOBALS['ecs']->table('region') . " WHERE parent_id = ".$child_diqu['province_id']." ";
            $daqu_name =  $GLOBALS['db']->GetAll($sql);
            $smarty->assign('city_list', $daqu_name);
        }
    }

    $agency_list = get_offline_man_list();
    $smarty->assign('order_list', $agency_list['agency']);
    $smarty->assign('filter', $agency_list['filter']);
    $smarty->assign('record_count', $agency_list['record_count']);
    $smarty->assign('page_count', $agency_list['page_count']);

    /* 排序标记 */
    $sort_flag = sort_flag($agency_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('role_account_list.htm');
}

/* ------------------------------------------------------ */
//-- 排序、分页、查询
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'query') {
    /* 检查权限 */
    admin_priv('role_account_man');
    $agency_list = get_offline_man_list();
    $smarty->assign('order_list', $agency_list['agency']);
    $smarty->assign('filter', $agency_list['filter']);
    $smarty->assign('record_count', $agency_list['record_count']);
    $smarty->assign('page_count', $agency_list['page_count']);

    /* 排序标记 */
    $sort_flag = sort_flag($agency_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('role_account_list.htm'), '', array('filter' => $agency_list['filter'], 'page_count' => $agency_list['page_count']));
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
    
    /* 初始化时间查询条件 */
    $up_time  = strtotime($_REQUEST['up_time']) ;  //开始时间
    $end_time = strtotime($_REQUEST['end_time']);  //结束时间
    if ($up_time > 0 || $end_time>0 )
    {
        $up_time  = $up_time  > 0 ? $up_time  - 28800  : 0 ;
        $end_time = $end_time > 0 ? $end_time - 28800  : 0 ;
        $where = " and bd.add_time BETWEEN $up_time AND $end_time ";
    }
    
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
    
    //联盟商家 搜索
    if (!empty($filter['supplier_name']) )
    {
        $where .= " and su.supplier_name like '%".$filter['supplier_name']."%' ";
    }
        
    switch ($_SESSION['member_role'] * 1) {
        
        case 1:
            
            /*
             *  大区   报单查询     获取会员管辖的城市
             * */
            $sql = 'SELECT region_id FROM ' . $GLOBALS['ecs']->table('region') . " WHERE parent_id = 1 and large_area_id='".$_SESSION['member_large_area_id']."'"  ;
            $daqu_name =  $GLOBALS['db']->GetAll($sql);
            $daqu_names[] = 9999999;
            foreach ($daqu_name as $vals)
            {
                $daqu_names[] = $vals['region_id'];
            }
            $child_province =   implode(',', $daqu_names);

            $where .= " and a.`province_id` in (".$child_province.")";

            break;
        case 2:
            
            /*
             * 城市经理
             */
            
            $where .= " and ( a.`district_id` = '".$_SESSION['member_city_id']."' or  a.`citys_id` = '".$_SESSION['member_city_id']."' or  a.`province_id` = '".$_SESSION['member_city_id']."' )  ";
            
            break;
        default:
            
            /* 商家和联盟商家 */
            $where .= " and  a.supplier_id ='".$_SESSION['supplier_id']."' " ;
            break;
    }
        
    // 查询报单SQL语句
    $sql = "SELECT bd.order_sn as id , bd.all_money , bd.rebate_money ,  bd.rebate_money , bd.`result_money`   , bd.add_time "
                ." FROM ". $GLOBALS['ecs']->table('supplier_rebate_log') ." as bd "
                ." left JOIN " . $GLOBALS['ecs']->table('supplier_admin_user') . " a ON bd.`real_supplier_id`   = a.supplier_id "
                ." left  JOIN " . $GLOBALS['ecs']->table('supplier') . " su ON su.supplier_id  = bd.supplier_id "
                ." where ( bd.`is_offline` = 0 or bd.`is_offline`  = 2 )  $where ORDER BY $filter[sort_by] $filter[sort_order] ";

    // 统计所有报单费 //
    $sql_sum = "SELECT COUNT(*) as num, sum(bd.all_money) as all_money_all , sum(bd.rebate_money) as rebate_money_all , sum(bd.result_money) as result_money_all"
                ." FROM ". $GLOBALS['ecs']->table('supplier_rebate_log') ." as bd "
                ." left JOIN " . $GLOBALS['ecs']->table('supplier_admin_user') . " a ON bd.`real_supplier_id`   = a.supplier_id "
                ." left  JOIN " . $GLOBALS['ecs']->table('supplier') . " su ON su.supplier_id  = bd.supplier_id "
                ." where ( bd.`is_offline` = 0 or bd.`is_offline`  = 2 )  $where ";

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