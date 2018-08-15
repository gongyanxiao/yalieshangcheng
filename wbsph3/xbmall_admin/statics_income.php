<?php

/**
 * 收入统计
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
	admin_priv ( 'finance_statics' );
	
	/* 模板赋值 */
	$smarty->assign ( 'ur_here', "收入统计" );
	// $smarty->assign('is_suppliers', array(array("code" => 1, "name" => "商家"), array("code" => 2, "name" => "运营中心")));
	// $smarty->assign('action_link', array('href' => 'offline.php?act=query', 'text' => "线下订单"));
	
	$smarty->assign ( 'full_page', 1 );
	$agency_list = get_finance_statics ();
	
	setStatics ( $smarty, $agency_list );
	
	/*
	 * 城市列表
	 */
	$sql = 'SELECT region_id, region_name FROM ' . $GLOBALS ['ecs']->table ( 'region' ) . " WHERE parent_id = 1 ";
	$daqu_name = $GLOBALS ['db']->GetAll ( $sql );
	$smarty->assign ( 'province_list', $daqu_name );
	
	assign_query_info ();
	$smarty->display ( 'statics_income.htm' );
} /* ------------------------------------------------------ */
// -- 排序、分页、查询
/* ------------------------------------------------------ */
elseif ($_REQUEST ['act'] == 'query') {
	
	/* 检查权限 */
	admin_priv ( 'finance_statics' );
	$agency_list = get_finance_statics ();
	setStatics ( $smarty, $agency_list );
	
	make_json_result ( $smarty->fetch ( 'statics_income.htm' ), '', array (
			'filter' => $agency_list ['filter'],
			'page_count' => $agency_list ['page_count'] 
	) );
}

/**
 * 设置统计页面需要的参数
 * 
 * @param unknown $smarty        	
 * @param unknown $agency_list        	
 */
function setStatics($smarty, $agency_list) {
	
	$zuo_dan_list = zuo_dan_tong_ji ( $agency_list['filter']);
	
	$smarty->assign ( 'zuo_dan_zong_e', $zuo_dan_list ['filter'] ['all_money_all'] );//做单总额
	$smarty->assign ( 'zuo_dan_fu_wu_fei', $zuo_dan_list ['filter'] ['rebate_money_all'] ); // 做单平台服务费
	$smarty->assign ( 'zuo_dan_huo_kuan', $zuo_dan_list ['filter'] ['result_money_all'] ); // 商家所得货款

	$xian_shang_list = xian_shang_ding_dan ($agency_list['filter']);
	$smarty->assign ( 'xian_shang_shang_jia_zong_e', $xian_shang_list ['filter'] ['all_money_all'] );
	$smarty->assign ( 'xian_shang_shang_jia_fu_wu_fei', $xian_shang_list ['filter'] ['rebate_money_all'] ); // 做单平台服务费
	$smarty->assign ( 'xian_shang_shang_jia_huo_kuan', $xian_shang_list ['filter'] ['result_money_all'] ); // 商家所得货款
	
	$smarty->assign ( 'order_list', $agency_list ['agency'] );
	$smarty->assign ( 'filter', $agency_list ['filter'] );
	$smarty->assign ( 'record_count', $agency_list ['record_count'] );
	$smarty->assign ( 'page_count', $agency_list ['page_count'] );
	/* 排序标记 */
	$sort_flag = sort_flag ( $agency_list ['filter'] );
	$smarty->assign ( $sort_flag ['tag'], $sort_flag ['img'] );
	
	$filter = $agency_list['filter'];
	if ($filter ["province"] != 0) {
		$where .= " and province=" . $filter ["province"];
	}
	if ($filter ["city"] != 0) {
		$where .= " and city=" . $filter ["city"];
	}
	if ($filter ["district"] != 0) {
		$where .= " and district=" . $filter ["district"];
	}
// 	file_put_contents("c:/test.txt",  $where."\r\n", FILE_APPEND);
	
	/**
	 * 总历史分红权, 现在的总分红权
	 */
	$sql = "SELECT  SUM(history_zsq) as zong_zsq, SUM(zsq)as zsq  , sum(give_points) as  give_points ,   SUM(consum_money) as consum_money  FROM " . $GLOBALS ['ecs']->table ( 'users' )." where 1 ".$where;
	$zsq = $GLOBALS ['db']->GetAll ( $sql );
	$smarty->assign ( 'zsq', $zsq [0] );
	
	$zuo_ri_zuo_dan = zuo_ri_zuo_dan();
	$smarty->assign ( 'zuo_ri_zuo_dan', $zuo_ri_zuo_dan);
	
	set_zuo_ri_zeng_song($smarty,$agency_list);
	set_zeng_song($smarty, $agency_list);
// 	$sql="SELECT  sum( money_paid)   FROM `xbmall_order_info` WHERE  order_status  = 5";
	
}

// 设置自营信息
function set_zi_ying(){
	
}


/**
 * 设置赠送额
 * @param unknown $smarty
 * @param unknown $agency_list
 * @return unknown
 */
function set_zeng_song($smarty,$agency_list){
	$pre = strtotime ( $_REQUEST ['start_time'] ); // 开始时间
	$day = strtotime ( $_REQUEST ['end_time'] ); // 结束时间
	
	
	if($pre==null ||$day==null) return ;

	
	$sql = "SELECT  SUM(a.zsq)as zsq  , sum(ABS(a.zsq)) as absZSQ ,sum(a.give_points) as  give_points ,   SUM(a.consum_money) as consum_money  FROM "
			. $GLOBALS ['ecs']->table ( 'account_log' )." a join "
					. $GLOBALS ['ecs']->table ( 'users' )." b on a.user_id=b.user_id  where a.change_time BETWEEN $pre and $day ";
					$filter = $agency_list['filter'];
					if ($filter ["province"] != 0) {
						$where .= " and b.province=" . $filter ["province"];
					}
					if ($filter ["city"] != 0) {
						$where .= " and b.city=" . $filter ["city"];
					}
					if ($filter ["district"] != 0) {
						$where .= " and b.district=" . $filter ["district"];
					}
					$sql =$sql." ".$where;
						file_put_contents("c:/test.txt",  $sql."\r\n", FILE_APPEND);
					$zeng_song = $GLOBALS ['db']->getRow ( $sql);
					$zuo_ri_zeng_song_quan_jian_shao = ($zeng_song['absZSQ']- $zeng_song['zsq'])/2;
					$smarty->assign ( 'zeng_song_quan_jian_shao', $zuo_ri_zeng_song_quan_jian_shao);// 减少的分红权
					$zuo_ri_zeng_song_quan_zeng_jia = $zeng_song['zsq']+$zuo_ri_zeng_song_quan_jian_shao;
					$smarty->assign ( 'zeng_song_quan_zeng_jia', $zuo_ri_zeng_song_quan_zeng_jia);// 增加的分红权
					$smarty->assign ( 'zeng_song_quan_he_ji', $zuo_ri_zeng_song_quan_zeng_jia-$zuo_ri_zeng_song_quan_jian_shao);// 增加的合计变动
					
					
			 
					// 领取积分的人数,及领取积分数
					$sql=" SELECT count(0)as renShu,sum(a.pay_points) as pay_points,sum(a.yl_points)as yl_points,sum(a.hz_points) as hz_points from ". $GLOBALS ['ecs']->table ( 'account_log' )." a where a.change_type=".ACT_LQHB." and a.change_time BETWEEN $pre and $day ";
					file_put_contents("c:/test.txt",  $sql."\r\n", FILE_APPEND);
					$lingQu = $GLOBALS ['db']->getRow ( $sql);
					
					$smarty->assign ( 'hong_bao_ling_qu_ren_shu', $lingQu['renShu']);// 领取人数
					$lingQuJiFen = $lingQu['pay_points']+$lingQu['yl_points']+$lingQu['hz_points'];
					$smarty->assign ( 'hong_bao_ling_qu',$lingQuJiFen);// 领取的积分总数
					
					
					
					$sql=" select sum(people_num) as people_num , sum(pay_points) as pay_points from ". $GLOBALS ['ecs']->table ( 'bonus_points_log' )." where send_time <= ".$day." and send_time >=".$pre;
					$fa_fang= $GLOBALS ['db']->getRow( $sql);
					$smarty->assign ( 'hong_bao_fa_fang_ren_shu', $fa_fang['people_num']);//红包发放人数
					$smarty->assign ( 'hong_bao_fa_fang_ji_fen', $fa_fang['pay_points']);//红包发放积分数
					$smarty->assign ( 'hong_bao_fa_fang_wei_ling_ji_fen', $fa_fang['pay_points']-$lingQuJiFen);//红包未领取积分数
					
					
					
					$smarty->assign ( 'zeng_song_quan', $zeng_song['zsq']);//赠送了多少分红权
					$smarty->assign ( 'zeng_song_ji_fen', $zeng_song['give_points']);//赠送了多少积分
					$h = var_export($smarty,TRUE);
					
					
}

/**
 * 设置昨日赠送
 */ 
function set_zuo_ri_zeng_song($smarty,$agency_list){
	
	$pre = local_mktime(0,0,0,date("m"),date("d")-1,date("y"));//昨日0点
	$day = local_mktime(0,0,0,date("m"),date("d"),date("Y"));//今日0点
	$sql = "SELECT  SUM(a.zsq)as zsq  , sum(ABS(a.zsq)) as absZSQ ,sum(a.give_points) as  give_points ,   SUM(a.consum_money) as consum_money  FROM " 
			. $GLOBALS ['ecs']->table ( 'account_log' )." a join " 
			. $GLOBALS ['ecs']->table ( 'users' )." b on a.user_id=b.user_id  where a.change_time BETWEEN $pre and $day ";
	$filter = $agency_list['filter'];
	if ($filter ["province"] != 0) {
		$where .= " and b.province=" . $filter ["province"];
	}
	if ($filter ["city"] != 0) {
		$where .= " and b.city=" . $filter ["city"];
	}
	if ($filter ["district"] != 0) {
		$where .= " and b.district=" . $filter ["district"];
	}
	$sql =$sql." ".$where;
// 	file_put_contents("c:/test.txt",  $sql."\r\n", FILE_APPEND);
	 
	$zeng_song = $GLOBALS ['db']->getRow ( $sql);
	$zuo_ri_zeng_song_quan_jian_shao = ($zeng_song['absZSQ']- $zeng_song['zsq'])/2;
	$smarty->assign ( 'zuo_ri_zeng_song_quan_jian_shao', $zuo_ri_zeng_song_quan_jian_shao);//昨日减少的分红权
	$zuo_ri_zeng_song_quan_zeng_jia = $zeng_song['zsq']+$zuo_ri_zeng_song_quan_jian_shao;
	$smarty->assign ( 'zuo_ri_zeng_song_quan_zeng_jia', $zuo_ri_zeng_song_quan_zeng_jia);//昨日增加的分红权
	
	
	$pre = local_mktime(17,0,0,date("m"),date("d")-1,date("y"));//昨日17点
	$day = local_mktime(15,0,0,date("m"),date("d"),date("Y"));//今日15点
	//昨日领取积分的人数,及领取积分数
	$sql=" SELECT count(0)as renShu,sum(a.pay_points) as pay_points,sum(a.yl_points)as yl_points,sum(a.hz_points) as hz_points from ". $GLOBALS ['ecs']->table ( 'account_log' )." a where a.change_type=".ACT_LQHB." and a.change_time BETWEEN $pre and $day ";
	
	$lingQu = $GLOBALS ['db']->getRow ( $sql);
	
	$smarty->assign ( 'zuo_ri_hong_bao_ling_qu_ren_shu', $lingQu['renShu']);//昨日领取人数
	$smarty->assign ( 'zuo_ri_hong_bao_ling_qu', $lingQu['pay_points']+$lingQu['yl_points']+$lingQu['hz_points']);//昨日领取的积分总数
	
	$sql=" select people_num from ". $GLOBALS ['ecs']->table ( 'bonus_points_log' )." where send_time < ".$day." order by id desc limit 1";
	$zuo_ri_hong_bao_fa_fang_ren_shu= $GLOBALS ['db']->getOne ( $sql);
	$smarty->assign ( 'zuo_ri_hong_bao_fa_fang_ren_shu', $zuo_ri_hong_bao_fa_fang_ren_shu);//昨日红包发放人数
	

	 
	$smarty->assign ( 'zuo_ri_zeng_song_quan', $zeng_song['zsq']);
	$smarty->assign ( 'zuo_ri_xiao_fei', $zeng_song['consum_money']);
	$smarty->assign ( 'zuo_ri_zeng_song_ji_fen', $zeng_song['give_points']);
	return $zeng_song;
}

/* ------------------------------------------------------ */

// -- 获取网站应收款统计
/* ------------------------------------------------------ */
function get_finance_statics() {
	 
	/* 初始化分页参数 */
	$filter = array ();
	$filter ['sort_by'] = empty ( $_REQUEST ['sort_by'] ) ? 'id' : trim ( $_REQUEST ['sort_by'] );
	$filter ['sort_order'] = empty ( $_REQUEST ['sort_order'] ) ? 'DESC' : trim ( $_REQUEST ['sort_order'] );
	$filter ['province'] = empty ( $_REQUEST ['province'] ) ? 0 : intval ( $_REQUEST ['province'] );
	
	$filter ['city'] = empty ( $_REQUEST ['city'] ) ? 0 : intval ( $_REQUEST ['city'] );
	$filter ['district'] = empty ( $_REQUEST ['district'] ) ? 0 : intval ( $_REQUEST ['district'] );
	// $filter['supplier_name'] = empty($_REQUEST['supplier_name']) ? 0 : addslashes($_REQUEST['supplier_name']);
	// $filter['daqu'] = empty($_REQUEST['daqu']) ? 0 : addslashes($_REQUEST['daqu']);
	// $filter['fanwei'] = empty($_REQUEST['fanwei']) ? 0 : addslashes($_REQUEST['fanwei']);
// 	file_put_contents("c:/test.txt",  "\r\n进入query:", FILE_APPEND);
	/* 初始化时间查询条件 */
	$up_time = strtotime ( $_REQUEST ['start_time'] ); // 开始时间
	$end_time = strtotime ( $_REQUEST ['end_time'] ); // 结束时间
	$where = "    WHERE 1=1 and s.is_paid=1 ";
	if ($up_time > 0 || $end_time > 0) {
		$up_time = $up_time > 0 ? $up_time - 28800 : 0;
		$end_time = $end_time > 0 ? $end_time - 28800 : 99999999999;
		$where .= " and s.add_time > $up_time AND s.add_time< $end_time ";
	}
	$filter ['start_time'] =$up_time;
	$filter ['end_time'] =$end_time;
	
	if ($filter ["province"] != 0) {
		$where .= " and u.province=" . $filter ["province"];
	}
	if ($filter ["city"] != 0) {
		$where .= " and u.city=" . $filter ["city"];
	}
	if ($filter ["district"] != 0) {
		$where .= " and u.district=" . $filter ["district"];
	}
	
	
	if(!empty($_REQUEST['mobile_phone'])){//手机号不为空
		$filter ['mobile_phone'] =addslashes($_REQUEST['mobile_phone']);
		$where .= " and u.mobile_phone='".$filter ['mobile_phone']."' ";
	}
	
 
	
	$joinSQL=" join " . $GLOBALS ['ecs']->table ( "users" ) . " as u on s.user_id=u.user_id ";
	$where=$joinSQL.$where;
	
	
	
	
	// 统计时间内的充值总金额(包含后付费用户)
	$sql = "SELECT SUM(abs(`amount`)) FROM " . $GLOBALS ['ecs']->table ( "user_account" ) . " as s " . $where . " AND s.`process_type`=0";
	$filter ["cz_money"] = $GLOBALS ['db']->getOne ( $sql );
	if (empty ( $filter ["cz_money"] )) {
		$filter ["cz_money"] = 0;
	}
	
	//充值到账金额(不包含后付费用户和虚拟用户)
	$sql = "SELECT SUM(abs(`amount`)) FROM " . $GLOBALS ['ecs']->table ( "user_account" ) . " as s " . $where . " and s.type=0 or (s.type!=0 and u.user_type!=1 and  u.user_type!=3)  AND s.`process_type`=0";
// 	var_dump($sql);
	$filter ["cz_dao_zhang_money"] = $GLOBALS ['db']->getOne ( $sql );
	if (empty ( $filter ["cz_dao_zhang_money"] )) {
		$filter ["cz_dao_zhang_money"] = 0;
	}
	
	
	
 
	// 统计时间内的提现总金额
	$sql = "SELECT SUM(abs(s.`amount`)) FROM " . $GLOBALS ['ecs']->table ( "user_account" ) . " as s " .  $where . " AND s.`process_type`=1";
	
	$filter ["tx_money"] = $GLOBALS ['db']->getOne ( $sql );
	
	if (empty ( $filter ["cz_money"] )) {
		$filter ["tx_money"] = 0;
	}
	if (isset ( $_REQUEST ['process_type'] ) && $_REQUEST ["process_type"] != '') {
		$where .= " and s.process_type = " . intval ( $_REQUEST ['process_type'] );
	}
	if (isset ( $_REQUEST ['type'] ) && $_REQUEST ["type"] != '') {
		$where .= " and s.type = " . intval ( $_REQUEST ['type'] );
	}
	
	/* 查询记录总数，计算分页数 */
	$sql = "SELECT COUNT(*) FROM " . $GLOBALS ['ecs']->table ( 'user_account' ) . " as s" . $where;
	$filter ['record_count'] = $GLOBALS ['db']->getOne ( $sql );
	$filter = page_and_size ( $filter );
	/* 查询记录 */
	$sql = "SELECT u.user_type, s.id,s.add_time,s.paid_time,s.type,s.user_id,u.user_name,u.mobile_phone,u.real_name,s.amount,s.`process_type` FROM " . 
	$GLOBALS ['ecs']->table ( 'user_account' ) . " as s " . $where . " ORDER BY $filter[sort_by] $filter[sort_order]";
	 
	 
	set_filter ( $filter, $sql );
	$res = $GLOBALS ['db']->selectLimit ( $sql, $filter ['page_size'], $filter ['start'] );
	$arr = array ();
	while ( $rows = $GLOBALS ['db']->fetchRow ( $res ) ) {
		$rows ["add_time"] = local_date ( "Y-m-d H:i:s", $rows ["add_time"] );
		$rows ["paid_time"] = local_date ( "Y-m-d H:i:s", $rows ["paid_time"] );
		$arr [] = $rows;
	}
	
	return array (
			'agency' => $arr,
			'filter' => $filter,
			'page_count' => $filter ['page_count'],
			'record_count' => $filter ['record_count'] 
	);
}


// 昨日做单总额
function  zuo_ri_zuo_dan(){
	
	$pre = local_mktime(0,0,0,date("m"),date("d")-1,date("y"));
	$day = local_mktime(0,0,0,date("m"),date("d"),date("Y"));
	$sql="select sum(all_money) as all_money from  " . $GLOBALS ['ecs']->table ( 'supplier_rebate_log' ) . " where `is_offline` = 1 and add_time BETWEEN $pre and $day ";
	
	$zonge = $GLOBALS ['db']->getone ( $sql);
	$zonge=$zonge!=0?$zonge:0;
	return  $zonge;
}



/**
 * 线下做单统计
 * 不得货款积分的
 *
 * @return array
 */
function zuo_dan_tong_ji($filter) {
	// 联盟商家 搜索
	if (! empty ( $filter ['supplier_name'] )) {
		$where .= " and su.supplier_name like '%" . $filter ['supplier_name'] . "%' ";
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
	
	$up_time = $filter['start_time']; // 开始时间
	$end_time = $filter['end_time']; // 结束时间
	if ($up_time > 0 || $end_time > 0) {
		$up_time = $up_time > 0 ? $up_time - 28800 : 0;
		$end_time = $end_time > 0 ? $end_time - 28800 : 99999999999;
		$where .= " and bd.add_time BETWEEN $up_time AND $end_time ";
	}
	
	
	// 查询报单SQL语句
	$sql = "SELECT bd.order_sn as id , a.district_id, a.uid , su.supplier_name, bd.all_money , bd.rebate_money ,  bd.rebate_money , 0 as `result_money`   , bd.add_time " .
	" FROM " . $GLOBALS ['ecs']->table ( 'supplier_rebate_log' ) . " as bd " . " left JOIN " . $GLOBALS ['ecs']->table ( 'supplier_admin_user' ) 
	. " a ON bd.`supplier_id`   = a.uid " . " left  JOIN " . $GLOBALS ['ecs']->table ( 'supplier' ) . " su ON su.supplier_id  = a.supplier_id " 
			. " where ( bd.`is_offline` = 1 )  $where ORDER BY $filter[sort_by] $filter[sort_order] ";

	
	// 统计所有报单费 //
	$sql_sum = "SELECT COUNT(*) as num, sum(bd.all_money) as all_money_all , sum(bd.rebate_money) as rebate_money_all , sum(0) as result_money_all" . " FROM " . $GLOBALS ['ecs']->table ( 'supplier_rebate_log' ) . " as bd  where ( bd.`is_offline` = 1 )  $where ";
 
	$order_sum = $GLOBALS ['db']->getRow ( $sql_sum );
	$filter ['record_count'] = $order_sum ['num'];
	$filter = page_and_size ( $filter );
	/* 查询记录 */
	set_filter ( $filter, $sql );
	
	$res = $GLOBALS ['db']->selectLimit ( $sql, $filter ['page_size'], $filter ['start'] );
	
	$arr = array ();
	while ( $rows = $GLOBALS ['db']->fetchRow ( $res ) ) {
		$rows ["add_time"] = local_date ( "Y-m-d H:i:s", $rows ['add_time'] );
		
		if (empty ( $rows ['supplier_name'] )) {
			$sqlss = " SELECT region_name FROM " . $GLOBALS ['ecs']->table ( 'region' ) . " WHERE `user_id` = '" . $rows ['uid'] . "' AND `region_id` = '" . $rows ['district_id'] . "' LIMIT 1  ";
			$region_name = $GLOBALS ['db']->getone ( $sqlss );
			$rows ['supplier_name'] = $region_name ? $region_name . '(运营中心)' : '';
		}
		
		$arr [] = $rows;
	}
	
	/* 在分页数组中添加 统计数据 */
	$filter ['all_money_all'] = $order_sum ['all_money_all'];
	$filter ['rebate_money_all'] = $order_sum ['rebate_money_all'];
	$filter ['result_money_all'] = $order_sum ['result_money_all'];
	
	return array (
			'agency' => $arr,
			'filter' => $filter,
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
function xian_shang_ding_dan($filter) {
	
	if ($filter ["province"] != 0) {
		$where .= " and a.province_id=" . $filter ["province"];
	}
	if ($filter ["city"] != 0) {
		$where .= " and a.city_id=" . $filter ["city"];
	}
	if ($filter ["district"] != 0) {
		$where .= " and a.district_id=" . $filter ["district"];
	}
	
	
	/* 初始化时间查询条件 */
	$up_time = strtotime ( $_REQUEST ['start_time'] ); // 开始时间
	$end_time = strtotime ( $_REQUEST ['end_time'] ); // 结束时间
	if ($up_time > 0 || $end_time > 0) {
		$up_time = $up_time > 0 ? $up_time - 28800 : 0;
		$end_time = $end_time > 0 ? $end_time - 28800 : 9491207314;
		$where = " and bd.add_time BETWEEN $up_time AND $end_time ";
	}
	
	// 联盟商家 搜索
	if (! empty ( $filter ['supplier_name'] )) {
		$where .= " and su.supplier_name like '%" . $filter ['supplier_name'] . "%' ";
	}
	
	// 查询线上商家订单SQL语句
	$sql = "SELECT bd.order_sn as id , su.supplier_name, bd.all_money , bd.rebate_money ,  bd.rebate_money , bd.`result_money`   , bd.add_time " . " FROM " .
	$GLOBALS ['ecs']->table ( 'supplier_rebate_log' ) . " as bd " . " left JOIN " . $GLOBALS ['ecs']->table ( 'supplier_admin_user' ) . " a ON bd.`supplier_id`   = a.uid " 
			. " left  JOIN " . $GLOBALS ['ecs']->table ( 'supplier' ) . " su ON su.supplier_id  = bd.real_supplier_id " . 
	" where ( bd.`is_offline` = 0 )  $where ORDER BY $filter[sort_by] $filter[sort_order] ";
	
			
	// 统计所有报单费 //
	$sql_sum = "SELECT COUNT(*) as num, sum(bd.all_money) as all_money_all , sum(bd.rebate_money) as rebate_money_all , sum(bd.result_money) as result_money_all" . " FROM " 
			. $GLOBALS ['ecs']->table ( 'supplier_rebate_log' ) . " as bd " . " left JOIN " . 
	$GLOBALS ['ecs']->table ( 'supplier_admin_user' ) . " a ON bd.`supplier_id`   = a.uid " . " left  JOIN " . 
	$GLOBALS ['ecs']->table ( 'supplier' ) . " su ON su.supplier_id  = bd.real_supplier_id " . " where ( bd.`is_offline` = 0 )  $where ";
	
	
	
	$order_sum = $GLOBALS ['db']->getRow ( $sql_sum );
	$filter ['record_count'] = $order_sum ['num'];
	
	$filter = page_and_size ( $filter );
	/* 查询记录 */
	set_filter ( $filter, $sql );
	
	$res = $GLOBALS ['db']->selectLimit ( $sql, $filter ['page_size'], $filter ['start'] );
	
	$arr = array ();
	while ( $rows = $GLOBALS ['db']->fetchRow ( $res ) ) {
		$rows ["add_time"] = local_date ( "Y-m-d H:i:s", $rows ['add_time'] );
		$arr [] = $rows;
	}
	
	/* 在分页数组中添加 统计数据 */
	$filter ['all_money_all'] = $order_sum ['all_money_all'];
	$filter ['rebate_money_all'] = $order_sum ['rebate_money_all'];
	$filter ['result_money_all'] = $order_sum ['result_money_all'];
	
	return array (
			'agency' => $arr,
			'filter' => $filter,
			'page_count' => $filter ['page_count'],
			'record_count' => $filter ['record_count'] 
	);
}

?>