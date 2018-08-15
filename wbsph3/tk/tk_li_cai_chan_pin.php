<?php

/**
理财产品 
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/* ------------------------------------------------------ */
//--做单列表
/* ------------------------------------------------------ */
if ($_REQUEST['act'] == 'list') {

	
    $smarty->assign('ur_here', "理财产品列表");
    $smarty->assign('action_link', array('text' => "新增做单", 'href' => 'role_offline_man.php?act=add'));
    $smarty->assign('full_page', 1);
    $agency_list = get_offline_man_list();
    $smarty->assign('order_list', $agency_list['agency']);
    $smarty->assign('filter', $agency_list['filter']);
    $smarty->assign('record_count', $agency_list['record_count']);
    $smarty->assign('page_count', $agency_list['page_count']);
  
    /* 排序标记 */
    $sort_flag = sort_flag($agency_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);
    assign_query_info();
    $smarty->display('tk_chan_pin_list.htm');
}

/* ------------------------------------------------------ */
//-- 排序、分页、查询
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'query') {
    $agency_list = get_offline_man_list();
    $smarty->assign('order_list', $agency_list['agency']);

    $smarty->assign('filter', $agency_list['filter']);
    $smarty->assign('record_count', $agency_list['record_count']);
    $smarty->assign('page_count', $agency_list['page_count']);

    /* 排序标记 */
    $sort_flag = sort_flag($agency_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('tk_chan_pin_list.htm'), '', array('filter' => $agency_list['filter'], 'page_count' => $agency_list['page_count']));
}


/* ------------------------------------------------------ */
//-- 添加、编辑办事处
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'add') {
    /* 检查权限 */
    admin_priv('role_offline_man');

    /* 是否添加 */
    $is_add = $_REQUEST['act'] == 'add';
    $smarty->assign('form_action', $is_add ? 'insert' : 'update');

    //20170315防止数据重复提交添加token验证开始
    if (isset($_SESSION['offline_detail_time'])) {
        unset($_SESSION['offline_detail_time']);
    }
    $_SESSION['offline_detail_time'] = gmtime();
    $smarty->assign("postToken", md5($_SESSION['member_uid'] . $_SESSION['offline_detail_time'] . AUTH_KEY));
    //20170315防止数据重复提交添加token验证结束

    $zuodan_set_fei     = $_CFG["zuodan_fei"] > 0 ? $_CFG["zuodan_fei"] : 0.12; // 报单费
    $smarty->assign('zuodan_set_fei', $zuodan_set_fei);
    assign_query_info();
    $smarty->display('offline_man_info.htm');
}

/* ------------------------------------------------------ */
//-- 提交做单
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'insert') {
	

} elseif ($_REQUEST['act'] == 'get_user') {
    if (!empty($_POST['user_id'])) {
        $userInfo = get_user_info_bymobile($_POST['user_id']);
//         if (($userInfo["code"] * 1 == 1) && ($userInfo['user_id'] * 1 == $_SESSION["member_uid"] * 1)) {
//             exit(json_encode(array("code" => 0, "message" => "不能给自己添加报单")));
//         } else {
//             exit(json_encode($userInfo));
//         }
        exit(json_encode($userInfo));
    }
} elseif ($_REQUEST['act'] == 'checkje') {
    checkje($_POST['amount']);
}


function jieQu($total){
	$total = (floatval($total));
	$total = $total*10;
	$totalInt = intval(strval( $total));
	$total =((float)$totalInt/10.0);
	return $total;
}
/**
 * 取得办事处列表
 * @return  array
 */
function get_offline_man_list() {
    $result = get_filter(); 
    if ($result === false) {
    	/* 初始化分页参数 */
    	$filter = array();
    	$filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'tk_id' : trim($_REQUEST['sort_by']);
    	$filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
    	
    	/* 查询记录总数，计算分页数 */
    	    $sql= "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('tk_li_cai') ;
    	  
    	 $filter['record_count'] = $GLOBALS['db']->getOne($sql);
    	 $filter = page_and_size($filter);
    	 /* 查询记录 */
    	 $sql = "SELECT * from	" . $GLOBALS['ecs']->table('tk_li_cai') . "  ORDER BY ". $filter[sort_by] ." ". $filter[sort_order];
    	 set_filter($filter, $sql);
    } else {
        $sql = $result['sql'];
        $filter = $result['filter'];
    }
    
    $t = var_export($filter,true);
 
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);
    file_put_contents("c:test.txt", $sql.$t);
    $arr = array();
    while ($rows = $GLOBALS['db']->fetchRow($res)) {
        $rows["order_time"] = local_date("Y-m-d H:i:s", $rows['createtime']);
        $arr[] = $rows;
    }

    /* 在分页数组中添加 统计数据 */
    $filter['order_amt_sum'] = $order_sum['order_amt_sum'];
    $filter['order_bdf_sum'] = $order_sum['order_bdf_sum'];

    return array('agency' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count'], 'order_amt_sum' => $order_sum['order_amt_sum'], 'order_bdf_sum' => $order_sum['order_bdf_sum']);
}

?>