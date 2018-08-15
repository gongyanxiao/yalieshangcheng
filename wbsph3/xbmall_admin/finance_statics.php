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
    admin_priv('finance_statics');

    /* 模板赋值 */
    $smarty->assign('ur_here', "网站应收款统计");
    //$smarty->assign('is_suppliers', array(array("code" => 1, "name" => "商家"), array("code" => 2, "name" => "运营中心")));
    //$smarty->assign('action_link', array('href' => 'offline.php?act=query', 'text' => "线下订单"));

    $smarty->assign('full_page', 1);

    //查询所有大区
//    $sql = "SELECT * FROM  " . $GLOBALS['ecs']->table('large_area') . " ";
//    $all_daqu = $GLOBALS['db']->getAll($sql);
//    $smarty->assign('all_daqu', $all_daqu);


    /*
     *  大区   报单查询     获取会员管辖的城市
     * */
//    $sql = 'SELECT region_id, region_name FROM ' . $GLOBALS['ecs']->table('region') . " WHERE parent_id = 1 ";
//    $daqu_name = $GLOBALS['db']->GetAll($sql);
//    $smarty->assign('province_list', $daqu_name);

    $agency_list = get_finance_statics();
    $smarty->assign('order_list', $agency_list['agency']);
    $smarty->assign('filter', $agency_list['filter']);
    $smarty->assign('record_count', $agency_list['record_count']);
    $smarty->assign('page_count', $agency_list['page_count']);

    /* 排序标记 */
    $sort_flag = sort_flag($agency_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('finance_statics.htm');
}

/* ------------------------------------------------------ */
//-- 排序、分页、查询
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'query') {
    /* 检查权限 */
    admin_priv('finance_statics');
    $agency_list = get_finance_statics();
    $smarty->assign('order_list', $agency_list['agency']);
    $smarty->assign('filter', $agency_list['filter']);
    $smarty->assign('record_count', $agency_list['record_count']);
    $smarty->assign('page_count', $agency_list['page_count']);
    /* 排序标记 */
    $sort_flag = sort_flag($agency_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('finance_statics.htm'), '', array('filter' => $agency_list['filter'], 'page_count' => $agency_list['page_count']));
}

/* ------------------------------------------------------ */

//-- 获取网站应收款统计
/* ------------------------------------------------------ */
function get_finance_statics() {
    /* 初始化分页参数 */
    $filter = array();
    $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'id' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
//    $filter['province'] = empty($_REQUEST['province']) ? 0 : intval($_REQUEST['province']);
//    $filter['city'] = empty($_REQUEST['city']) ? 0 : intval($_REQUEST['city']);
//    $filter['district'] = empty($_REQUEST['district']) ? 0 : intval($_REQUEST['district']);
//    $filter['supplier_name'] = empty($_REQUEST['supplier_name']) ? 0 : addslashes($_REQUEST['supplier_name']);
//    $filter['daqu'] = empty($_REQUEST['daqu']) ? 0 : addslashes($_REQUEST['daqu']);
//    $filter['fanwei'] = empty($_REQUEST['fanwei']) ? 0 : addslashes($_REQUEST['fanwei']);

     
   
    /* 初始化时间查询条件 */
    $up_time = strtotime($_REQUEST['start_time']);  //开始时间
    $end_time = strtotime($_REQUEST['end_time']);  //结束时间
    $where = "    WHERE 1=1 and is_paid=1 ";
    if ($up_time > 0 || $end_time > 0) {
        $up_time = $up_time > 0 ? $up_time - 28800 : 0;
        $end_time = $end_time > 0 ? $end_time - 28800 :99999999999;
        $where .= " and s.add_time > $up_time AND s.add_time< $end_time ";
    }
    
    if(!empty($_REQUEST['phone_num'])){//手机号不为空
    	$phone_num = addslashes($_REQUEST['phone_num']);
    	$user_id = $GLOBALS['db']->getOne("select user_id from " . $GLOBALS['ecs']->table("users") . " WHERE mobile_phone='".$phone_num."'");
    }
    if($user_id){
    	$where .= " and s.user_id=".$user_id;
    }
    
    //统计时间内的充值总金额
    $sql = "SELECT SUM(abs(`amount`)) FROM " . $GLOBALS['ecs']->table("user_account") . " as s" . $where . " AND `process_type`=0";
    $filter["cz_money"] = $GLOBALS['db']->getOne($sql);
    if (empty($filter["cz_money"])) {
        $filter["cz_money"] = 0;
    }
    //统计时间内的提现总金额
    $sql = "SELECT SUM(abs(`amount`)) FROM " . $GLOBALS['ecs']->table("user_account") . " as s" . $where . " AND `process_type`=1";
    $filter["tx_money"] = $GLOBALS['db']->getOne($sql);
    if (empty($filter["cz_money"])) {
        $filter["tx_money"] = 0;
    }
    if (isset($_REQUEST['process_type']) && $_REQUEST["process_type"] != '') {
        $where .= " and s.process_type = " . intval($_REQUEST['process_type']);
    }
    if (isset($_REQUEST['type']) && $_REQUEST["type"] != '') {
        $where .= " and s.type = " . intval($_REQUEST['type']);
    }
    /* 查询记录总数，计算分页数 */
    $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('user_account') . " as s " . $where;
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);
    $filter = page_and_size($filter);
    /* 查询记录 */
    $sql = "SELECT s.id,s.add_time,s.paid_time,s.type,s.user_id,a.user_name,a.mobile_phone,a.real_name,s.amount,s.`process_type` FROM " . $GLOBALS['ecs']->table('user_account') . " as s LEFT JOIN " . $GLOBALS['ecs']->table("users") . "  as  a on s.user_id=a.user_id  " . $where . " ORDER BY $filter[sort_by] $filter[sort_order]";
    set_filter($filter, $sql);
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);
    $arr = array();
    while ($rows = $GLOBALS['db']->fetchRow($res)) {
        $rows["add_time"] = local_date("Y-m-d H:i:s", $rows["add_time"]);
        $rows["paid_time"] = local_date("Y-m-d H:i:s", $rows["paid_time"]);
        $arr[] = $rows;
    }

    return array('agency' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

/**
 * 取得办事处列表
 * @return  array
 */
function get_offline_man_list() {

    /* 初始化分页参数 */
    $filter = array();
    $filter['province'] = empty($_REQUEST['province']) ? 0 : intval($_REQUEST['province']);
    $filter['city'] = empty($_REQUEST['city']) ? 0 : intval($_REQUEST['city']);
    $filter['district'] = empty($_REQUEST['district']) ? 0 : intval($_REQUEST['district']);
    $filter['supplier_name'] = empty($_REQUEST['supplier_name']) ? 0 : addslashes($_REQUEST['supplier_name']);
    $filter['daqu'] = empty($_REQUEST['daqu']) ? 0 : addslashes($_REQUEST['daqu']);
    $filter['fanwei'] = empty($_REQUEST['fanwei']) ? 0 : addslashes($_REQUEST['fanwei']);

    /* 初始化时间查询条件 */
    $up_time = strtotime($_REQUEST['start_time']);  //开始时间
    $end_time = strtotime($_REQUEST['end_time']);  //结束时间
    if ($up_time > 0 || $end_time > 0) {
        $up_time = $up_time > 0 ? $up_time - 28800 : 0;
        $end_time = $end_time > 0 ? $end_time - 28800 : 99999999999;
        $where = " and bd.add_time BETWEEN $up_time AND $end_time ";
    }

    switch ($filter['fanwei'] * 1) {

        case 0:

            //查询有几个大区
            $sql = " SELECT * FROM  " . $GLOBALS['ecs']->table('large_area') . " ";
            $all_daqu = $GLOBALS['db']->GetAll($sql);

            $all_data_list = array();
            // 统计每个大区的数据

            $chongzhi_all_money = $consum_money_all = $all_money_all = $yongjin_all = $fuwufei_all = $result_money_all = 0;
            foreach ($all_daqu as $val) {
                //查询每个大区下的省份名称
                $sql = " SELECT `region_id` FROM " . $GLOBALS['ecs']->table('region') . " WHERE `large_area_id` = '" . $val['id'] . "' LIMIT 0, 1000 ";
                $all_daqu = $GLOBALS['db']->GetCol($sql);
                $sql = " SELECT "
                        . " sum(if(bd.is_offline<>3 and bd.is_offline<>4,bd.all_money,0 ) ) as all_money_all , "
                        . " sum(if(bd.is_offline!=1,bd.rebate_money,0 ) ) as yongjin_all ,"
                        . " sum(if(bd.is_offline =1,bd.rebate_money,0 ) ) as fuwufei_all ,"
                        . " sum(bd.result_money) as result_money_all ,"
                        . " sum(if(bd.is_offline=3,bd.all_money,0 ) ) as chongzhi_all_money ,"
                        . " sum(if(bd.is_offline=4,bd.all_money,0 ) ) as tixian_all_money "
                        . " FROM " . $GLOBALS['ecs']->table('supplier_rebate_log') . " as bd "
                        . " left JOIN " . $GLOBALS['ecs']->table('supplier_admin_user') . " a ON bd.`supplier_id`   = a.uid "
                        . " where a.`province_id` in (" . implode(',', $all_daqu) . ") $where "
                ;
                //echo $sql;
                $rs_once = $GLOBALS['db']->getRow($sql);
                $rs_once['list_name'] = $val['name'];
                $chongzhi_all_money+=$rs_once['chongzhi_all_money'] = $rs_once['chongzhi_all_money'] ? $rs_once['chongzhi_all_money'] : 0;
                $all_money_all += $rs_once['all_money_all'] = $rs_once['all_money_all'] ? $rs_once['all_money_all'] : 0;
                $yongjin_all += $rs_once['yongjin_all'] = $rs_once['yongjin_all'] ? $rs_once['yongjin_all'] : 0;
                $fuwufei_all += $rs_once['fuwufei_all'] = $rs_once['fuwufei_all'] ? $rs_once['fuwufei_all'] : 0;
                $result_money_all += $rs_once['result_money_all'] = $rs_once['result_money_all'] ? $rs_once['result_money_all'] : 0;

                $all_data_list[] = $rs_once;
            }
            break;
        case 1:
            break;
        case 2:

            /*
             * 城市经理
             */

            // 省市区 检索
            if ($filter['province'] > 0) {
                //县级检索
                $reregion_short = "citys_id";
                $where1 = " `parent_id` = '" . $filter['province'] . "' ";
            }
            if ($filter['city'] > 0) {
                //市区检索
                $reregion_short = "district_id";
                $where1 = " `parent_id` = '" . $filter['city'] . "' ";
            }
            if ($filter['district'] > 0) {
                //县级检索 -> 查询下面所有会员  分组查询
                $reregion_short = "district_id";
                $where1 = " `region_id` = '" . $filter['district'] . "' ";
            }
            if (empty($reregion_short)) {
                $reregion_short = "province_id";
                $where1 = " `parent_id` = 1 ";
                $filter['province'] = 0;
            }

            // 要是县级  需要显示 城市经理的订单
            if ($filter['district'] > 0) {
                // 统计每个大区的数据
                $consum_money_all = $all_money_all = $yongjin_all = $fuwufei_all = $result_money_all = 0;
                //查询子集地区
                $all_where = " where a.`$reregion_short` = " . $filter['district'] . " $where ";

                $sql = " SELECT "
                        . " sum(if(bd.is_offline<>3 and bd.is_offline<>4,bd.all_money,0 ) ) as all_money_all , "
                        . " sum(if(bd.is_offline!=1,bd.rebate_money,0) ) as yongjin_all ,"
                        . " sum(if(bd.is_offline=1,bd.rebate_money,0 ) ) as fuwufei_all ,"
                        . " sum(bd.result_money) as result_money_all , "
                        . " sum(if(bd.is_offline=3,bd.all_money,0 ) ) as chongzhi_all_money ,"
                        . " sum(if(bd.is_offline=4,bd.all_money,0 ) ) as tixian_all_money ,"
                        . " su.supplier_name ,"
                        . " bd.supplier_id "
                        . " FROM " . $GLOBALS['ecs']->table('supplier_rebate_log') . " as bd "
                        . " left JOIN " . $GLOBALS['ecs']->table('supplier_admin_user') . " a ON bd.`supplier_id`   = a.uid "
                        . " left  JOIN " . $GLOBALS['ecs']->table('supplier') . " su ON su.user_id  = bd.supplier_id "
                        . " $all_where group by su.supplier_id "
                ;

                //统计总金额
                $sql_sum = " SELECT "
                        . " sum(if(bd.is_offline<>3 and bd.is_offline<>4,bd.all_money,0 ) ) as all_money_all , "
                        . " sum(if(bd.is_offline!=1,bd.rebate_money,0) ) as yongjin_all ,"
                        . " sum(if(bd.is_offline=1,bd.rebate_money,0 ) ) as fuwufei_all ,"
                        . " sum(bd.result_money) as result_money_all , "
                        . " sum(if(bd.is_offline=3,bd.all_money,0 ) ) as chongzhi_all_money ,"
                        . " sum(if(bd.is_offline=4,bd.all_money,0 ) ) as tixian_all_money ,"
                        . " su.supplier_name ,"
                        . " bd.supplier_id "
                        . " FROM " . $GLOBALS['ecs']->table('supplier_rebate_log') . " as bd "
                        . " left JOIN " . $GLOBALS['ecs']->table('supplier_admin_user') . " a ON bd.`supplier_id`   = a.uid "
                        . " left  JOIN " . $GLOBALS['ecs']->table('supplier') . " su ON su.user_id  = bd.supplier_id "
                        . " $all_where group by su.supplier_id "
                ;
                $all_sum = $GLOBALS['db']->getRow($sql_sum);

                $sql_count = " SELECT "
                        . " count(DISTINCT bd.supplier_id) AS num "
                        . " FROM " . $GLOBALS['ecs']->table('supplier_rebate_log') . " as bd "
                        . " left JOIN " . $GLOBALS['ecs']->table('supplier_admin_user') . " a ON bd.`supplier_id`   = a.uid "
                        . " left  JOIN " . $GLOBALS['ecs']->table('supplier') . " su ON su.user_id  = bd.supplier_id "
                        . " $all_where "
                ;

                $filter['record_count'] = $GLOBALS['db']->getRow($sql_count);
                $filter['record_count'] = $filter['record_count']['num'];

                $filter = page_and_size($filter);
                /* 查询记录 */
                set_filter($filter, $sql);

                $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);
                $all_data_list = array();
                while ($rs_once = $GLOBALS['db']->fetchRow($res)) {
                    $rs_once['list_name'] = $rs_once['supplier_name'];
                    $all_data_list[] = $rs_once;
                }

                $all_money_all = $all_sum['all_money_all'] ? $all_sum['all_money_all'] : 0;
                $yongjin_all = $all_sum['yongjin_all'] ? $all_sum['yongjin_all'] : 0;
                $fuwufei_all = $all_sum['fuwufei_all'] ? $all_sum['fuwufei_all'] : 0;
                $result_money_all = $all_sum['result_money_all'] ? $all_sum['result_money_all'] : 0;
                $chongzhi_all_money = $all_sum['chongzhi_all_money'] ? $all_sum['chongzhi_all_money'] : 0;
            } else {
                $sql = " SELECT * FROM " . $GLOBALS['ecs']->table('region') . " WHERE $where1 ORDER BY `region_id` DESC LIMIT 0, 1000 ";
                $all_daqu = $GLOBALS['db']->GetAll($sql);

                $all_data_list = array();
                // 统计每个大区的数据

                $consum_money_all = $all_money_all = $yongjin_all = $fuwufei_all = $result_money_all = 0;
                foreach ($all_daqu as $val) {
                    //查询子集地区
                    $all_where = " where a.`$reregion_short` = " . $val['region_id'] . " $where ";

                    $sql = " SELECT "
                            . " sum(if(bd.is_offline<>3 and bd.is_offline<>4,bd.all_money,0 ) ) as all_money_all , "
                            . " sum(if(bd.is_offline!=1,bd.rebate_money,0) ) as yongjin_all ,"
                            . " sum(if(bd.is_offline=1,bd.rebate_money,0 ) ) as fuwufei_all ,"
                            . " sum(bd.result_money) as result_money_all , "
                            . " sum(if(bd.is_offline=3,bd.all_money,0 ) ) as chongzhi_all_money ,"
                            . " sum(if(bd.is_offline=4,bd.all_money,0 ) ) as tixian_all_money "
                            . " FROM " . $GLOBALS['ecs']->table('supplier_rebate_log') . " as bd "
                            . " left JOIN " . $GLOBALS['ecs']->table('supplier_admin_user') . " a ON bd.`supplier_id`   = a.uid "
                            . " $all_where "
                    ;
                    $rs_once = $GLOBALS['db']->getRow($sql);
                    $rs_once['list_name'] = $val['region_name'];
                    $all_money_all += $rs_once['all_money_all'] = $rs_once['all_money_all'] ? $rs_once['all_money_all'] : 0;
                    $yongjin_all += $rs_once['yongjin_all'] = $rs_once['yongjin_all'] ? $rs_once['yongjin_all'] : 0;
                    $fuwufei_all += $rs_once['fuwufei_all'] = $rs_once['fuwufei_all'] ? $rs_once['fuwufei_all'] : 0;
                    $result_money_all += $rs_once['result_money_all'] = $rs_once['result_money_all'] ? $rs_once['result_money_all'] : 0;
                    $chongzhi_all_money += $rs_once['chongzhi_all_money'] = $rs_once['chongzhi_all_money'] ? $rs_once['chongzhi_all_money'] : 0;
                    $all_data_list[] = $rs_once;
                }
            }
            break;

        case 3:

            //查询商家数据
            if ($filter['supplier_name']) {
                $where = " and su.supplier_name  = '" . trim($filter['supplier_name']) . "' $where ";
                $sql = " SELECT "
                        . " (if(bd.is_offline<>3 and bd.is_offline<>4,bd.all_money,0 ) ) as all_money_all , "
                        . " (if(bd.is_offline!=1,bd.rebate_money,0) ) as yongjin_all ,"
                        . " (if(bd.is_offline=1,bd.rebate_money,0 ) ) as fuwufei_all ,"
                        . " (bd.result_money) as result_money_all , "
                        . " (if(bd.is_offline=3,bd.all_money,0 ) ) as chongzhi_all_money ,"
                        . " (if(bd.is_offline=4,bd.all_money,0 ) ) as tixian_all_money ,"
                        . " su.supplier_name ,"
                        . " bd.supplier_id "
                        . " FROM " . $GLOBALS['ecs']->table('supplier_rebate_log') . " as bd "
                        . " left JOIN " . $GLOBALS['ecs']->table('supplier_admin_user') . " a ON bd.`supplier_id`   = a.uid "
                        . " left  JOIN " . $GLOBALS['ecs']->table('supplier') . " su ON su.user_id  = bd.supplier_id "
                        . " left  JOIN " . $GLOBALS['ecs']->table('users') . " us ON us.user_id  = su.user_id "
                        . " where 1 $where "
                ;

                //统计金额
                $sql_sum = " SELECT "
                        . " sum(if(bd.is_offline<>3 and bd.is_offline<>4,bd.all_money,0 ) ) as all_money_all , "
                        . " sum(if(bd.is_offline!=1,bd.rebate_money,0) ) as yongjin_all ,"
                        . " sum(if(bd.is_offline=1,bd.rebate_money,0 ) ) as fuwufei_all ,"
                        . " sum(bd.result_money) as result_money_all , "
                        . " sum(if(bd.is_offline=3,bd.all_money,0 ) ) as chongzhi_all_money ,"
                        . " sum(if(bd.is_offline=4,bd.all_money,0 ) ) as tixian_all_money "
                        . " FROM " . $GLOBALS['ecs']->table('supplier_rebate_log') . " as bd "
                        . " left JOIN " . $GLOBALS['ecs']->table('supplier_admin_user') . " a ON bd.`supplier_id`   = a.uid "
                        . " left  JOIN " . $GLOBALS['ecs']->table('supplier') . " su ON su.user_id  = bd.supplier_id "
                        . " left  JOIN " . $GLOBALS['ecs']->table('users') . " us ON us.user_id  = su.user_id "
                        . " where 1 $where "
                ;
                $all_sum = $GLOBALS['db']->getRow($sql_sum);

                $sql_count = " SELECT "
                        . " count(*) as num "
                        . " FROM " . $GLOBALS['ecs']->table('supplier_rebate_log') . " as bd "
                        . " left JOIN " . $GLOBALS['ecs']->table('supplier_admin_user') . " a ON bd.`supplier_id`   = a.uid "
                        . " left  JOIN " . $GLOBALS['ecs']->table('supplier') . " su ON su.user_id  = bd.supplier_id "
                        . " left  JOIN " . $GLOBALS['ecs']->table('users') . " us ON us.user_id  = su.user_id "
                        . " where 1 $where "
                ;

                $filter['record_count'] = $GLOBALS['db']->getRow($sql_count);
                $filter['record_count'] = $filter['record_count']['num'];

                $filter = page_and_size($filter);
                /* 查询记录 */
                set_filter($filter, $sql);

                $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);
                $all_data_list = array();
                while ($rs_once = $GLOBALS['db']->fetchRow($res)) {
                    $rs_once['list_name'] = $rs_once['supplier_name'];
                    $all_data_list[] = $rs_once;
                }

                $consum_money_all = $all_sum['consum_money_all'] ? $all_sum['consum_money_all'] : 0;
                $all_money_all = $all_sum['all_money_all'] ? $all_sum['all_money_all'] : 0;
                $yongjin_all = $all_sum['yongjin_all'] ? $all_sum['yongjin_all'] : 0;
                $fuwufei_all = $all_sum['fuwufei_all'] ? $all_sum['fuwufei_all'] : 0;
                $result_money_all = $all_sum['result_money_all'] ? $all_sum['result_money_all'] : 0;
                $chongzhi_all_money = $all_sum['chongzhi_all_money'] ? $all_sum['chongzhi_all_money'] : 0;
            } else {
                // 统计每个大区的数据
                $consum_money_all = $all_money_all = $yongjin_all = $fuwufei_all = $result_money_all = 0;
                //查询子集地区
                $all_where = " where 1 $where ";

                $sql = " SELECT "
                        . " sum(if(bd.is_offline<>3 and bd.is_offline<>4,bd.all_money,0 ) ) as all_money_all , "
                        . " sum(if(bd.is_offline!=1,bd.rebate_money,0) ) as yongjin_all ,"
                        . " sum(if(bd.is_offline=1,bd.rebate_money,0 ) ) as fuwufei_all ,"
                        . " sum(bd.result_money) as result_money_all , "
                        . " sum(if(bd.is_offline=3,bd.all_money,0 ) ) as chongzhi_all_money ,"
                        . " sum(if(bd.is_offline=4,bd.all_money,0 ) ) as tixian_all_money ,"
                        . " su.supplier_name ,"
                        . " bd.supplier_id "
                        . " FROM " . $GLOBALS['ecs']->table('supplier_rebate_log') . " as bd "
                        . " left JOIN " . $GLOBALS['ecs']->table('supplier_admin_user') . " a ON bd.`supplier_id`   = a.uid "
                        . " left  JOIN " . $GLOBALS['ecs']->table('supplier') . " su ON su.user_id  = bd.supplier_id "
                        . " $all_where group by su.supplier_id "
                ;

                //统计总金额
                $sql_sum = " SELECT "
                        . " sum(if(bd.is_offline<>3 and bd.is_offline<>4,bd.all_money,0 ) ) as all_money_all , "
                        . " sum(if(bd.is_offline!=1,bd.rebate_money,0) ) as yongjin_all ,"
                        . " sum(if(bd.is_offline=1,bd.rebate_money,0 ) ) as fuwufei_all ,"
                        . " sum(bd.result_money) as result_money_all , "
                        . " sum(if(bd.is_offline=3,bd.all_money,0 ) ) as chongzhi_all_money ,"
                        . " sum(if(bd.is_offline=4,bd.all_money,0 ) ) as tixian_all_money "
                        . " FROM " . $GLOBALS['ecs']->table('supplier_rebate_log') . " as bd "
                        . " left JOIN " . $GLOBALS['ecs']->table('supplier_admin_user') . " a ON bd.`supplier_id`   = a.uid "
                        . " left  JOIN " . $GLOBALS['ecs']->table('supplier') . " su ON su.user_id  = bd.supplier_id "
                        . " $all_where "
                ;
                $all_money = $GLOBALS['db']->getRow($sql_sum);

                //查询条数
                $sql_count = " SELECT "
                        . " count(DISTINCT bd.supplier_id) AS num "
                        . " FROM " . $GLOBALS['ecs']->table('supplier_rebate_log') . " as bd "
                        . " left JOIN " . $GLOBALS['ecs']->table('supplier_admin_user') . " a ON bd.`supplier_id`   = a.uid "
                        . " left  JOIN " . $GLOBALS['ecs']->table('supplier') . " su ON su.user_id  = bd.supplier_id "
                        . " $all_where "
                ;

                $filter['record_count'] = $GLOBALS['db']->getRow($sql_count);
                $filter['record_count'] = $filter['record_count']['num'];

                $filter = page_and_size($filter);
                /* 查询记录 */
                set_filter($filter, $sql);

                $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);
                $all_data_list = array();
                while ($rs_once = $GLOBALS['db']->fetchRow($res)) {
                    $rs_once['list_name'] = $rs_once['supplier_name'];
                    $all_data_list[] = $rs_once;
                }

                $all_money_all = $all_money['all_money_all'] ? $all_money['all_money_all'] : 0;
                $yongjin_all = $all_money['yongjin_all'] ? $all_money['yongjin_all'] : 0;
                $fuwufei_all = $all_money['fuwufei_all'] ? $all_money['fuwufei_all'] : 0;
                $result_money_all = $all_money['result_money_all'] ? $all_money['result_money_all'] : 0;
                $chongzhi_all_money = $all_money['chongzhi_all_money'] ? $all_money['chongzhi_all_money'] : 0;
            }
            break;
    }

    /* 在分页数组中添加 统计数据 */
    $filter['chongzhi_all_money'] = $chongzhi_all_money;
    $filter['all_money_all'] = $all_money_all;
    $filter['yongjin_all'] = $yongjin_all;
    $filter['fuwufei_all'] = $fuwufei_all;
    $filter['result_money_all'] = $result_money_all;

    return array('agency' => $all_data_list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count'] ? $filter['record_count'] : 10);
}

?>