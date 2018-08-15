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
    $smarty->assign('action_link', array('text' => "新增理财产品", 'href' => 'tk_li_cai_chan_pin.php?act=add'));
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

 elseif ($_REQUEST['act'] == 'query') {
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


  elseif ($_REQUEST['act'] == 'add') {
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

 
    assign_query_info();
    $smarty->display('tk_chan_pin_add.htm');
}

 elseif ($_REQUEST['act'] == 'insert') {
  	$product_name = $_REQUEST['product_name'];
  	$period= $_REQUEST['period'];
  	$rate= $_REQUEST['rate'];
  	$description= $_REQUEST['description'];
     $sql="insert into ".$ecs->table("tk_li_cai")." (product_name, period, rate,description)
 values ('$product_name','$period','$rate','$description')";
    $db->query($sql);
    
    $links[0]['text'] = "转到列表页";
    $links[0]['href'] = "tk_li_cai_chan_pin.php?act=list";
    sys_msg("添加成功", 0, $links);
}  

elseif ($_REQUEST['act'] == 'toEdit') {
	$tk_id = $_REQUEST['tk_id'];
	
	$sql="select * from  ".$ecs->table("tk_li_cai")." where tk_id='$tk_id' ";
	$bean = $db->getRow($sql);
	$smarty->assign("bean",$bean);
	$smarty->display('tk_chan_pin_edit.htm');
} elseif ($_REQUEST['act'] == 'delete') {
	$tk_id = $_REQUEST['tk_id'];
	$sql="delete from  ".$ecs->table("tk_li_cai")." where tk_id='$tk_id' ";
	$bean = $db->query($sql);
	$links[0]['text'] = "转到列表页";
	$links[0]['href'] = "tk_li_cai_chan_pin.php?act=list";
	sys_msg("删除成功", 0, $links);
} 
elseif ($_REQUEST['act'] == 'update') {
	$tk_id = $_REQUEST['tk_id'];
	$product_name = $_REQUEST['product_name'];
	$period= $_REQUEST['period'];
	$rate= $_REQUEST['rate'];
	$description= $_REQUEST['description'];
	$sql="update ".$ecs->table("tk_li_cai")." set product_name='$product_name'
    ,period='$period',rate='$rate',description='$description' where tk_id='$tk_id' ";
	$db->query($sql);
	
	$links[0]['text'] = "转到列表页";
	$links[0]['href'] = "tk_li_cai_chan_pin.php?act=list";
	sys_msg("修改成功", 0, $links);
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