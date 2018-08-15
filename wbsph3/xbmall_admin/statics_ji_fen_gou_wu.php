<?php

/**
 * 积分购物统计
 * ============================================================================
 * 版权所有 2005-2010 山东儒孝消费养老服务有限公司，并保留所有权利。
 * 网站地址: http://www.xiangbai315.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 */
define ( 'IN_ECS', true );

require (dirname ( __FILE__ ) . '/includes/init.php');
require_once (ROOT_PATH . 'includes/lib_order.php');
require_once (ROOT_PATH . 'includes/lib_goods.php');

/* ------------------------------------------------------ */
// -- 订单查询
/* ------------------------------------------------------ */

if ($_REQUEST ['act'] == 'list') {
	
	/* 检查权限 */
	admin_priv ( 'finance_statics' ); // 只要有统计权限就可以(没有使用新的权限)
	
	/* 模板赋值 */
	$smarty->assign ( 'ur_here', "积分购物统计" );
	$smarty->assign ( 'full_page', 1 );

	$agency_list = get_finance_statics ();
	$smarty->assign ( 'order_list', $agency_list['agency']);
	$smarty->assign ( 'totalPoints', $agency_list['totalPoints']);
	$smarty->assign('filter', $agency_list['filter']);
	$smarty->assign('record_count', $agency_list['record_count']);
	$smarty->assign('page_count', $agency_list['page_count']);
	assign_query_info ();
	$smarty->display ( 'statics_ji_fen_gou_wu.htm' );
} /* ------------------------------------------------------ */
// -- 排序、分页、查询
/* ------------------------------------------------------ */
elseif ($_REQUEST ['act'] == 'query') {
	
	/* 检查权限 */
	admin_priv ( 'finance_statics' );
	$agency_list = get_finance_statics ();
	$smarty->assign('order_list', $agency_list['agency']);
	$smarty->assign ( 'totalPoints', $agency_list['totalPoints']);
	$smarty->assign('filter', $agency_list['filter']);
	$smarty->assign('record_count', $agency_list['record_count']);
	$smarty->assign('page_count', $agency_list['page_count']);
	make_json_result ( $smarty->fetch ( 'statics_ji_fen_gou_wu.htm' ), '', array (
			'filter' => $agency_list ['filter'],
			'page_count' => $agency_list ['page_count'] 
	) );
}

/* ------------------------------------------------------ */

// -- 
/* ------------------------------------------------------ */
function get_finance_statics() {
	
	/* 初始化分页参数 */
	$filter = array ();
	$filter ['sort_by'] = empty ( $_REQUEST ['sort_by'] ) ? 'id' : trim ( $_REQUEST ['sort_by'] );
	$filter ['sort_order'] = empty ( $_REQUEST ['sort_order'] ) ? 'DESC' : trim ( $_REQUEST ['sort_order'] );
	$filter ['mobile_phone'] = empty ( $_REQUEST ['mobile_phone'] ) ? 0 : trim ( $_REQUEST ['mobile_phone'] );
	$filter ['real_name'] = empty ( $_REQUEST ['real_name'] ) ? 0 : trim ( $_REQUEST ['real_name'] );
	 
	// /* 初始化时间查询条件 */
	// $up_time = strtotime ( $_REQUEST ['start_time'] ); // 开始时间
	// $end_time = strtotime ( $_REQUEST ['end_time'] ); // 结束时间
	// $where = " WHERE 1=1 and s.is_paid=1 ";
	// if ($up_time > 0 || $end_time > 0) {
	// $up_time = $up_time > 0 ? $up_time - 28800 : 0;
	// $end_time = $end_time > 0 ? $end_time - 28800 : 99999999999;
	// $where .= " and s.add_time > $up_time AND s.add_time< $end_time ";
	// }
	// $filter ['start_time'] =$up_time;
	// $filter ['end_time'] =$end_time;
	
	if (! empty ( $_REQUEST ['mobile_phone'] )) { // 手机号不为空
		$filter ['mobile_phone'] = addslashes ( $_REQUEST ['mobile_phone'] );
		$where .= " and u.mobile_phone='" . $filter ['mobile_phone'] . "' ";
	}
	if (! empty ( $_REQUEST ['real_name'] )) { // 手机号不为空
		$filter ['real_name'] = addslashes ( $_REQUEST ['real_name'] );
		$where .= " and u.real_name like '%" . $filter ['real_name'] . "%' ";
	}
	
	/* 查询记录总数，计算分页数 */
	// $filter ['record_count'] = $GLOBALS ['db']->getOne ( $sql );
	// $filter = page_and_size ( $filter );
	/* 查询记录 */
	$sql = "SELECT  a.integral AS integral ,
			'线上积分订单' as type,
             date_format(FROM_UNIXTIME(a.add_time),'%Y-%m-%d %H:%i') as createtime,
			u.real_name,
			u.mobile_phone,
			u.user_id as user_id FROM " . $GLOBALS ['ecs']->table ( 'order_info' ) . " as a JOIN " . $GLOBALS ['ecs']->table ( 'users' ) . " u  ON a.user_id = u.user_id 
            where a.integral>0 ".$where ."  union  SELECT  a.order_amt AS integral,
			'线下积分订单' as type,
  			 date_format(FROM_UNIXTIME(a.createtime),'%Y-%m-%d %H:%i') as createtime,
			u.real_name,
			u.mobile_phone,
			u.user_id as user_id FROM " . $GLOBALS ['ecs']->table ( 'order_bd' ) . " as a JOIN " . $GLOBALS ['ecs']->table ( 'users' ) . " u  ON a.user_id = u.user_id
            where  type =1  " . $where ." order by  createtime desc ";
	 
	$countSQL = "SELECT count(0) as num, SUM(d.integral) as totalPoints   from ( ".$sql." ) d";
	$order_sum = $GLOBALS['db']->getRow($countSQL);
	$filter['record_count'] = $order_sum['num'];
	$totalPoints =  $order_sum['totalPoints'];//总的积分
	$filter = page_and_size($filter);
	 
	$res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);//分页数据
	while ($rows = $GLOBALS['db']->fetchRow($res)) {
// 		$rows["add_time"] = local_date("Y-m-d H:i:s", $rows['createtime']);
		$arr[] = $rows;
	}
	 
	return array (
			'agency' => $arr,
			'filter' => $filter,
			'totalPoints' => $totalPoints,
			'page_count' => $filter ['page_count'],
			'record_count' => $filter ['record_count'] 
	);
}

/**
 * 线上订单统计
 * 累计订单总金额/累计平台服务费/累计累计货款积分
 *
 * @return array
 */

?>