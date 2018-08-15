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
    admin_priv('13_offline');

    /* 模板赋值 */
    $smarty->assign('ur_here', "线下订单");
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
    $smarty->assign ( 'statics_type', "xian_xia_ji_fen" );
    assign_query_info();
    $smarty->display('order_statics.htm');
}  elseif ($_REQUEST['act'] == 'export') {
	//导出
	$adminpriv = "13_offline";
	admin_priv($adminpriv);
	
	$result = get_offline_man_list();
	// 引入phpexcel核心类文件
	require_once ROOT_PATH . '/includes/phpexcel/Classes/PHPExcel.php';
	// 实例化excel类
	$objPHPExcel = new PHPExcel();
	// 实例化excel图片处理类
	$objDrawing = new PHPExcel_Worksheet_Drawing();
	// 操作第一个工作表
	$objPHPExcel->setActiveSheetIndex(0);
	// 设置sheet名
	$objPHPExcel->getActiveSheet()->setTitle('线下积分订单');
	
	// 表格宽度
// 	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
	// 列名表头加粗
	$objPHPExcel->getActiveSheet()->getStyle('A1:J1')->getFont()->setBold(true);
	// 列名赋值
	$objPHPExcel->getActiveSheet()->setCellValue('A1', '编号');
	$objPHPExcel->getActiveSheet()->setCellValue('B1', '时间');
	$objPHPExcel->getActiveSheet()->setCellValue('C1', '省');
	$objPHPExcel->getActiveSheet()->setCellValue('D1', '市');
	$objPHPExcel->getActiveSheet()->setCellValue('E1', '县');
	$objPHPExcel->getActiveSheet()->setCellValue('F1', '商家');
	$objPHPExcel->getActiveSheet()->setCellValue('G1', '商家姓名');
	$objPHPExcel->getActiveSheet()->setCellValue('H1', '商家手机号');
	$objPHPExcel->getActiveSheet()->setCellValue('I1', '消费者姓名');
	$objPHPExcel->getActiveSheet()->setCellValue('J1', '消费者手机号');
	$objPHPExcel->getActiveSheet()->setCellValue('K1', '订单金额');
	$objPHPExcel->getActiveSheet()->setCellValue('L1', '佣金');
	$objPHPExcel->getActiveSheet()->setCellValue('M1', '货款积分');
// 	$objPHPExcel->getActiveSheet()->getStyle('J')->getNumberFormat()
// 	->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
	// 数据起始行
	$row_num = 2;
	// 向每行单元格插入数据
	foreach ($result['agency'] as $key => $value) {
		// 设置单元格高度
		$objPHPExcel->getActiveSheet()->getRowDimension($row_num)->setRowHeight(32);
		// 设置排序列、是否显示列居中显示
		// 设置单元格数值
	 
		$objPHPExcel->getActiveSheet()->setCellValue('A' . $row_num, " ".$value['id']);
		$objPHPExcel->getActiveSheet()->setCellValue('B' . $row_num, $value['add_time']);
		$objPHPExcel->getActiveSheet()->setCellValue('C' . $row_num, $value['province']);
		$objPHPExcel->getActiveSheet()->setCellValue('D' . $row_num, $value['city']);
		$objPHPExcel->getActiveSheet()->setCellValue('E' . $row_num, $value['district']);
		$objPHPExcel->getActiveSheet()->setCellValue('F' . $row_num, $value['supplier_name']);
		$objPHPExcel->getActiveSheet()->setCellValue('G' . $row_num, $value['user2_real_name']);
		$objPHPExcel->getActiveSheet()->setCellValue('H' . $row_num, $value['user2_mobile_phone']);
		$objPHPExcel->getActiveSheet()->setCellValue('I' . $row_num, $value['user1_real_name']);
		$objPHPExcel->getActiveSheet()->setCellValue('J' . $row_num, $value['user1_mobile_phone']);
		$objPHPExcel->getActiveSheet()->setCellValue('K' . $row_num, $value['all_money']);
		$objPHPExcel->getActiveSheet()->setCellValue('L' . $row_num, $value['rebate_money']  );
		$objPHPExcel->getActiveSheet()->setCellValue('M' . $row_num,    $value['result_money'] );
		$row_num++;
	} 
	$outputFileName = '线下积分订单_' . time() . '.xls';
	$xlsWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
	header("Content-Type: application/force-download");
	header("Content-Type: application/octet-stream");
	header("Content-Type: application/download");
	header('Content-Disposition:inline;filename="' . iconv("UTF-8", "gb2312", $outputFileName) . '"');
	header("Content-Transfer-Encoding: binary");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Pragma: no-cache");
	$xlsWriter->save("php://output");
	echo file_get_contents($outputFileName);
	
}

/* ------------------------------------------------------ */
//-- 排序、分页、查询
/* ------------------------------------------------------ */ 
elseif ($_REQUEST['act'] == 'query') {
    /* 检查权限 */
    admin_priv('13_offline');
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
    $filter['consumer_mobile_phone'] = empty($_REQUEST['consumer_mobile_phone']) ? 0 : addslashes($_REQUEST['consumer_mobile_phone']);
    $filter['is_offline'] = empty($_REQUEST['is_offline']) ? 2 : addslashes($_REQUEST['is_offline']);
    
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
    if (!empty($filter['consumer_mobile_phone']) )
    {//消费者的手机号
    	$where .= " and user1.mobile_phone like '%".$filter['consumer_mobile_phone']."%' ";
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
    			  ." where ( bd.`is_offline` = ".$filter['is_offline'].")  $where  ORDER BY $filter[sort_by] $filter[sort_order] ";
  
    // 统计所有报单费 //
    $sql_sum = "SELECT COUNT(*) as num, sum(bd.all_money) as all_money_all , sum(bd.rebate_money) as rebate_money_all , sum(bd.result_money) as result_money_all"
                ." FROM ". $GLOBALS['ecs']->table('supplier_rebate_log') ." as bd  "
                ." left JOIN " . $GLOBALS['ecs']->table('supplier_admin_user') . " a ON bd.`member_id`   = a.user_id "
                ." left  JOIN " . $GLOBALS['ecs']->table('supplier') . " su ON su.user_id  = bd.supplier_id "
                		." left  JOIN " . $GLOBALS['ecs']->table('users') . "  user2 ON bd.supplier_id=user2.user_id "//做单者信息
                		." left  JOIN " . $GLOBALS['ecs']->table('order_bd') . "   orderBD ON orderBD.order_sn = bd.order_sn "//做单者信息
                		." left  JOIN " . $GLOBALS['ecs']->table('users') . "  user1 ON orderBD.user_id = user1.user_id "//做单者信息
                ." where ( bd.`is_offline` = ".$filter['is_offline']." )  $where ";
    $order_sum = $GLOBALS['db']->getRow($sql_sum);
    $filter['record_count'] = $order_sum['num'];
    $filter = page_and_size($filter);
    /* 查询记录 */
    set_filter($filter, $sql);
    if ($_REQUEST["act"] == "export") {
    	$res = $GLOBALS['db']->selectLimit($sql, 10000000, 0);
    }else{
    	$res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);
    }

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