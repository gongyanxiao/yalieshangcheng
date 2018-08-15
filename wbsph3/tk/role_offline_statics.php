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
    admin_priv('role_offline_man');
    $smarty->assign('ur_here', "做单统计");
    $smarty->assign('action_link', array('text' => "新增做单", 'href' => 'role_offline_man.php?act=add'));
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
    else 
    {
        die('无权访问');
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
    $smarty->display('offline_statics_list.htm');
}

/* ------------------------------------------------------ */
//-- 排序、分页、查询
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'query') {
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

    make_json_result($smarty->fetch('offline_statics_list.htm'), '', array('filter' => $agency_list['filter'], 'page_count' => $agency_list['page_count']));
}


/* ------------------------------------------------------ */
//-- 添加、编辑办事处
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'add') {
    /* 检查权限 */
    admin_priv('role_offline_man');

    /* 是否添加 */
    $is_add = $_REQUEST['act'] == 'add';
    $smarty->assign('form_action', $is_add ? 'insert' : 'update');

    assign_query_info();
    $smarty->display('offline_man_info.htm');
}

/* ------------------------------------------------------ */
//-- 提交添加、编辑办事处
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'insert') {
    /* 检查权限 */
    admin_priv('role_offline_man');

    /* 是否添加 */
    $is_add = $_REQUEST['act'] == 'insert';


    if (!isset($_POST["user_id"]) || !isset($_POST["good_name"]) || !isset($_POST["order_amt"])) {
        //show_message("参数不合法", "返回上一页", 'user.php?act=offline_detail', 'info');
        sys_msg("参数不合法");
    }
    $sql = "select `user_id`,`user_name` from" . $GLOBALS["ecs"]->table("users") . " where `mobile_phone`='" . $_POST["user_id"] . "'";
    $check_user = $GLOBALS['db']->getRow($sql);
    if (!empty($check_user)) {
        if ($check_user["user_id"] * 1 == $_SESSION["member_uid"] * 1) {
            sys_msg("不能给自己添加报单");
        }
        //exit(json_encode(array("code" => 1, "user_id" => $check_user["user_id"], 'user_name' => $check_user['user_name'])));
    } else {
        sys_msg("会员不存在");
    }

    $bd_order_amt = $_POST["order_amt"];
    $amount = intval($bd_order_amt) * 0.1;
    if ($amount > 0) {
        $sql = "select `user_money` from" . $GLOBALS["ecs"]->table("users") . " where `user_id`='" . $_SESSION["member_uid"] . "'";
        $checkje = $GLOBALS['db']->getOne($sql);
        $zuodan_set_max = $_CFG["zuodan_set_max"] * 1;
        if (empty($zuodan_set_max)) {
            $zuodan_set_max = 50000;
        }
        if ($amount > $checkje) {
            sys_msg("金额不合法,超过可用服务费");
            //exit(json_encode(array("code" => 0, "message" => "")));
        } else {
            if ($amount > $zuodan_set_max) {
                sys_msg("金额不合法,超过系统设置最大服务费");
            } else {
                // exit(json_encode(array("code" => 1)));
            }
        }
    } else {
        sys_msg("金额错误");
    }
    if ($_FILES['fp_url']['size'] > 0) {
        include_once (ROOT_PATH . '/includes/cls_image.php');
        $fp_image = new cls_image($_CFG['bgcolor']);
        $fp_image_original = $fp_image->upload_image($_FILES['fp_url'], 'fp_url/' . date('Ym'));
        $fp_url_path = DATA_DIR . '/fp_url/' . date('Ym') . '/';
        $fp_url_thumb = $fp_image->make_thumb($fp_image_original, '250', '168', $fp_url_path);
        $fp_url = $fp_url_thumb ? $fp_url_thumb : $fp_image_original;
    }
    if ($_FILES['good_url']['size'] > 0) {
        include_once (ROOT_PATH . '/includes/cls_image.php');
        $good_image = new cls_image($_CFG['bgcolor']);
        $good_image_original = $good_image->upload_image($_FILES['good_url'], 'good_url/' . date('Ym'));

        $good_url_path = DATA_DIR . '/good_url/' . date('Ym') . '/';
        $good_url_thumb = $good_image->make_thumb($good_image_original, '250', '168', $good_url_path);
        $good_url = $good_url_thumb ? $good_url_thumb : $good_image_original;
    }
    $real_supplier_id = 0;
//    if (!empty($_REQUEST['real_supplier_id'])) {
//        $sql = "select supplier_id from " . $GLOBALS['ecs']->table("supplier") . " where user_id=" . intval($_SESSION['user_id']) . " and supplier_id=" . $_REQUEST['real_supplier_id'] . " and zuodan=1";
//        $real_supplier_id = $GLOBALS['db']->getOne($sql);
//        if (empty($real_supplier_id)) {
//            show_message("所选择商家不合法", "返回上一页", 'user.php?act=offline_detail', 'info');
//        } else {
//            $real_supplier_id = $_REQUEST['real_supplier_id'];
//        }
//    }
    /* 代码增加_start By www.68ecshop.com */
    $GLOBALS['db']->startTrans();
    include_once (ROOT_PATH . '/includes/lib_order.php');
    $userDefault = get_user_account_info($_SESSION['member_uid']);

    $bd_data = array(
        "user_id" => $check_user["user_id"],
        "supplier_id" => $_SESSION['member_uid'],
        "order_amt" => $bd_order_amt * 1,
        "order_bdf" => $amount,
        "good_name" => $_POST['good_name'],
        "createtime" => gmtime(),
        "fp_url" => $fp_url,
        "good_url" => $good_url,
        "status" => 1,
        "order_sn" => get_order_sn(),
        'supplier_parent_id' => $userDefault['parent_id'],
        'real_supplier_id' => $_SESSION['supplier_id'],
        'large_area_id' => $_SESSION['member_large_area_id'],
        'city_id' => $_SESSION['member_city_id'],
        'member_id' => $_SESSION['member_user_id']
    );
    $insertBd = $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table("order_bd"), $bd_data);
    if ($insertBd) {
        $bdId = $GLOBALS['db']->insert_id();
        $currentUser = get_user_account_info($check_user["user_id"]);
        $historyZsq = $currentUser["history_zsq"];
        $consum_money_value = $currentUser["consum_money_value"] * 1 + $bd_order_amt * 1;
        $addZsq = intval($consum_money_value / 1000) - $historyZsq; //需要赠送的赠送权数量
        $resUser = log_account_change($check_user["user_id"], 0, 0, 0, 0, "添加报单，报单记录:" . $bdId . "报单金额" . $bd_order_amt . "", 3, $bd_order_amt, 0, 0, $addZsq, $addZsq);

        $resAffiliate = true;
        if ($check_user['user_id'] > 0) {
            if ($GLOBALS["_CFG"]["recommend_set_open"] * 1 === 1) {
//                $parentInfo = get_user_default($check_user['user_id']);
                if ($currentUser["parent_id"] > 0) {
                    $change_desc = "推荐会员线下购物获得推荐赠送，" . sprintf($GLOBALS['_LANG']['order_gift_integral'], $bd_data['order_sn'], 4);
                    $recommend_points = $bd_data["order_amt"] * 1 * $GLOBALS["_CFG"]["recommend_set_percent"] / 100;
                    //$affiliate = "insert into " . $GLOBALS['ecs']->table('affiliate_log') . " VALUES('" . $bdId . "','" . gmtime() . "','" . $check_user['user_id'] . "','',0,'" . $recommend_points . "',0,'" . $change_desc . "')";
                    $affiliate = "insert into " . $GLOBALS['ecs']->table('affiliate_log') . "(order_id,`time`,`user_id`,`user_name`,`money`,`point`,`separate_type`,`change_desc`) VALUES('" . $bdId . "','" . gmtime() . "','" . $check_user['user_id'] . "','',0,'" . $recommend_points . "',0,'" . $change_desc . "')";
                    $resAffiliate = $GLOBALS['db']->query($affiliate);
                }
            }
        }
        if ($userDefault['jljs_points'] >= $amount * 0.2) {
            $amount1 = floor($amount * 0.2 * 100) / 100;
            $amount2 = $amount - $amount1;
            $resSupp = log_account_change($_SESSION['member_uid'], $amount2 * (-1), 0, 0, 0, "添加报单，报单记录:" . $bdId . "报单金额" . $bd_order_amt . ",报单服务费:" . $amount, 3, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, $amount1 * (-1));
        } else {
            $resSupp = log_account_change($_SESSION['member_uid'], $amount * (-1), 0, 0, 0, "添加报单，报单记录:" . $bdId . "报单金额" . $bd_order_amt . ",报单服务费:" . $amount, 3, 0, 0, 0, 0, 0);
        }
//        $supplier_idSql = "select supplier_id from " . $GLOBALS['ecs']->table("supplier") . " where `user_id`='" . $_SESSION['user_id'] . "' and status=1";
//        $supplier_id = $GLOBALS['db']->getOne($supplier_idSql);
//        if (empty($supplier_id)) {//如果是商家的话，计入商家的佣金分成体系
//        }
        $rebeat = array(
            "order_sn" => $bd_data["order_sn"],
            "order_id" => $bdId,
            "supplier_id" => $_SESSION['member_uid'],
            "all_money" => $bd_data["order_amt"],
            "rebate_money" => $bd_data["order_bdf"],
            "result_money" => $bd_data["order_amt"] - $bd_data["order_bdf"],
            "pay_id" => 0,
            "pay_name" => "充值积分",
            "texts" => "线下支付",
            "add_time" => $bd_data['createtime'],
            'supplier_parent_id' => $userDefault['parent_id'],
            "is_offline" => 1,
            'real_supplier_id' => $_SESSION['supplier_id'],
            'large_area_id' => $_SESSION['member_large_area_id'],
            'city_id' => $_SESSION['member_city_id'],
            'member_id' => $_SESSION['member_user_id']
        );
        $insertRebeat = $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table("supplier_rebate_log"), $rebeat);
//    }
        if ($resUser && $resSupp && $insertRebeat && $resAffiliate) {
            admin_log("提交报单", 'add', 'role_offline_man');
            $GLOBALS['db']->commitTrans();
            $links = array(
                array('href' => 'role_offline_man.php?act=add', 'text' => "继续添加报单"),
                array('href' => 'role_offline_man.php?act=list', 'text' => "报单列表")
            );
            sys_msg("提交报单成功", 0, $links);
        } else {
            $GLOBALS['db']->rollbackTrans();
            sys_msg("提交报单失败");
        }
    } else {
        $GLOBALS['db']->rollbackTrans();
        sys_msg("提交报单失败");
    }
} elseif ($_REQUEST['act'] == 'get_user') {
    if (!empty($_POST['user_id'])) {
        $userInfo = get_user_info_bymobile($_POST['user_id']);
        if (($userInfo["code"] * 1 == 1) && ($userInfo['user_id'] * 1 == $_SESSION["member_uid"] * 1)) {
            exit(json_encode(array("code" => 0, "message" => "不能给自己添加报单")));
        } else {
            exit(json_encode($userInfo));
        }
    }
} elseif ($_REQUEST['act'] == 'checkje') {
    checkje($_POST['amount']);
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
        $where = " and bd.createtime BETWEEN $up_time AND $end_time ";
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
    }
        
    // 查询报单SQL语句
    $sql = "SELECT bd.id  ,su.supplier_name ,  a.district_id, a.uid , u.`user_name` , u.`real_name` ,bd.order_amt ,bd.order_bdf , bd.createtime , bd.good_name ,bd.fp_url,bd.good_url"
                ." FROM ". $GLOBALS['ecs']->table('order_bd') ." as bd "
                ." left JOIN " . $GLOBALS['ecs']->table('supplier_admin_user') . " a ON bd.`member_id`   = a.user_id "
                ." left  JOIN " . $GLOBALS['ecs']->table('users') . " u ON u.user_id  = bd.user_id "
                ." left  JOIN " . $GLOBALS['ecs']->table('supplier') . " su ON su.user_id  = bd.supplier_id "
                ." where 1 $where ORDER BY $filter[sort_by] $filter[sort_order] ";
    
    // 统计所有报单费 //
    $sql_sum = "SELECT COUNT(*) as num, sum(bd.order_amt) as order_amt_sum , sum(bd.order_bdf) as order_bdf_sum
                FROM ". $GLOBALS['ecs']->table('order_bd') ." as bd "
                ." left JOIN " . $GLOBALS['ecs']->table('supplier_admin_user') . " a ON bd.`member_id`   = a.user_id "
                ." left  JOIN " . $GLOBALS['ecs']->table('users') . " u ON u.user_id  = a.user_id "
                ." left  JOIN " . $GLOBALS['ecs']->table('supplier') . " su ON su.user_id  = bd.supplier_id "
                ." where 1 $where ";
    //echo  $sql;
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

        if (empty($rows['supplier_name']))
        {
            $sqlss = " SELECT region_name FROM ".$GLOBALS['ecs']->table('region') ." WHERE `user_id` = '".$rows['uid']."' AND `region_id` = '".$rows['district_id']."' LIMIT 1  ";
            $region_name = $GLOBALS['db']->getone($sqlss);
            $rows['supplier_name'] = $region_name ? $region_name .'(运营中心)' : '' ;
        }
        $arr[] = $rows;
    }
    
    /* 在分页数组中添加 统计数据 */
    $filter['order_amt_sum'] = $order_sum['order_amt_sum'] ;
    $filter['order_bdf_sum'] = $order_sum['order_bdf_sum'] ;

    return array('agency' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count'] ? $filter['record_count'] : 10 );
}
?>