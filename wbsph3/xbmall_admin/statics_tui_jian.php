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
	$smarty->assign ( 'ur_here', "个人推荐信息" );
	$smarty->assign ( 'full_page', 1 );
	
	
	$smarty->assign ( 'filter', array ());
	$smarty->assign ( 'record_count', 0 );
	$smarty->assign ( 'page_count', 0);
	
// 	$agency_list = get_finance_statics ();
// 	$smarty->assign ( 'order_list', $agency_list ['agency'] );
// 	$smarty->assign ( 'filter', $agency_list ['filter'] );
// 	$smarty->assign ( 'record_count', $agency_list ['record_count'] );
// 	$smarty->assign ( 'page_count', $agency_list ['page_count'] );
	assign_query_info ();
	$smarty->display ( 'statics_tui_jian.htm' );
} /* ------------------------------------------------------ */
// -- 排序、分页、查询
/* ------------------------------------------------------ */
elseif ($_REQUEST ['act'] == 'query') {
	
	/* 检查权限 */
	admin_priv ( 'finance_statics' );
	$agency_list = get_finance_statics ();
	$list = $agency_list ['agency']; // 所有他推荐的人员
	$smarty->assign ( 'order_list', $list );
 
	$smarty->assign ( 'filter', $agency_list ['filter'] );
	$smarty->assign ( 'record_count', $agency_list ['record_count'] );
	$smarty->assign ( 'page_count', $agency_list ['page_count'] );
	
	make_json_result ( $smarty->fetch ( 'statics_tui_jian.htm' ), '', array (
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
	
	if (! empty ( $_REQUEST ['mobile_phone'] )) { // 手机号不为空
		$filter ['mobile_phone'] = addslashes ( $_REQUEST ['mobile_phone'] );
		$where .= " and mobile_phone like '%" . $filter ['mobile_phone'] . "%' ";
	}
	if (! empty ( $_REQUEST ['real_name'] )) { // 真实姓名不为空
		$filter ['real_name'] = addslashes ( $_REQUEST ['real_name'] );
		$where .= " and real_name like '%" . $filter ['real_name'] . "%' ";
	}
	
	/* 查询出本人的信息 */
	$sql = "select user_id,is_bigfamily, mobile_phone, real_name, parent_id from  " . $GLOBALS ['ecs']->table ( 'users' ) . "  where 1 " . $where;
	$root = $GLOBALS ['db']->getAll ( $sql );
	$totalArr = array (); // 记录所有的子推荐人信息
	 
	foreach ( $root as $key => $row ) {
		try {
			getChildTuiJian ( $row, $totalArr ,$row['mobile_phone']."->");
		}catch (Exception $e){
			var_dump($e);
		}
	}
	
	return array (
			'agency' => $totalArr,
			'filter' => $filter,
			'root' => $root, // 根
			'page_count' => 1,
			'record_count' => count ( $totalArr ) 
	);
}


function getChildTuiJian($parentInfo, &$totalArr = array(),  $path="") {
	 
	if (! $parentInfo) {
		return;
	}

	/* 查询出本人的信息 */
	$sql = "select user_id, real_name, mobile_phone, is_bigfamily, real_name, parent_id from  " . $GLOBALS ['ecs']->table ( 'users' ) . "   where parent_id= " . $parentInfo['user_id'];
	$res = $GLOBALS ['db']->getAll ( $sql ); // 分页数据
	if (!$res){
		return ;
	}
	foreach ( $res as $key => $row ) {
		if(!$row)continue;
		$row['path']=$path.$row['mobile_phone'];
		setXiaoFei($row);
		$totalArr [] = $row;
		getChildTuiJian ( $row, $totalArr ,$path.$row['mobile_phone']."(".$row['real_name'].")->"); // 继续搜索子推荐人
	}
	
	return $totalArr;
}

function  setXiaoFei(&$row=array() ){
	/* 查询记录 */
	$sql =  "select user_id, mobile_phone, real_name from  " . $GLOBALS['ecs']->table('users')." u where 1 ".$where;
  
		//查询出线上积分消费和现金消费
		$sql="SELECT   	SUM(a.goods_amount) as xian_shang_xian_jin ,SUM(a.integral)  as xian_shang_ji_fen FROM 	xbmall_order_info a    WHERE   a.user_id = ".$row['user_id'];
		$tempRow = $GLOBALS['db']->getRow($sql);
		$row['xian_shang_xian_jin'] = $tempRow['xian_shang_xian_jin'];
		$row['xian_shang_ji_fen'] = $tempRow['xian_shang_ji_fen'];
		//线下现金做单
		$sql=" SELECT   	SUM(a.order_amt) FROM 	xbmall_order_bd a   WHERE    type = 0 and  a.user_id =".$row['user_id'];
		$row['xian_xia_xian_jin'] = $GLOBALS['db']->getOne($sql);
		
		//线下积分做单
		$sql=" SELECT   	SUM(a.order_amt) FROM 	xbmall_order_bd a   WHERE    type = 1 and  a.user_id =".$row['user_id'];
		$row['xian_xia_ji_fen'] = $GLOBALS['db']->getOne($sql);
		
		/**
		 * 线下给他人现金做单
		 */
		$sql="SELECT   	SUM(a.order_amt)  FROM 	xbmall_order_bd a    WHERE type = 0 and a.supplier_id = ".$row['user_id'];
		$row['xian_xia_supplier_xian_jin'] = $GLOBALS['db']->getOne($sql);
		/**
		 * 线下给他人积分做单
		 */
		$sql="SELECT   	SUM(a.order_amt)  FROM 	xbmall_order_bd a    WHERE type =1 and a.supplier_id = ".$row['user_id'];
		$row['xian_xia_supplier_ji_fen'] = $GLOBALS['db']->getOne($sql);
		
		//提现金额
		$sql="SELECT SUM(amount) FROM `xbmall_user_account` where process_type = 1 and is_paid =1 and user_id = ".$row['user_id'];
		$row['ti_xian_jin_e'] = $GLOBALS['db']->getOne($sql);
		
}
/**
 * 线上订单统计
 * 累计订单总金额/累计平台服务费/累计累计货款积分
 *
 * @return array
 */

?>