<?php

/**
 * 销售, 购买记录
 * ============================================================================
 */
define ( 'IN_ECS', true );

require (dirname ( __FILE__ ) . '/includes/init.php');
include_once (ROOT_PATH . '/includes/cls_image.php');
$image = new cls_image ( $_CFG ['bgcolor'] );
$exc = new exchange ( $ecs->table ( 'goods' ), $db, 'goods_id', 'goods_name' );


$action = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : '';
 $function_name = 'action_' . $action;
 

if (!function_exists($function_name)) {
	$function_name = "action_default";
}


call_user_func($function_name);
 


function action_buy(){
	
	global $smarty;
	global $db ,$ecs,$_CFG;
	$goods_id = $_REQUEST ['goods_id'];
	$buy_num = $_REQUEST ['buy_num'];
	$sql="select goods_name, shop_price  from ".$ecs->table('goods')." where goods_id=".$goods_id;
	
	$goods = $db->getRow($sql);
	
	//查询出购买人及购买 人的推荐人信息
	$user = $db->getRow("select a.user_name , a.mobile_phone as user_mobile ,  b.user_id as parent_id, b.user_name as parent_name , b.mobile_phone as parent_mobile   
  FROM `xbmall_users` a   JOIN xbmall_users  b 
     on  a.parent_id =b.user_id   where a.user_id=".getUserId());
	if ($goods=== false) {
		/* 如果没有找到任何记录则跳回到首页 */
		ecs_header ( "Location: ./\n" );
		exit ();
	}

	$now = gmtime();
	 
	$sql = "insert into xbmall_tk_xiao_shou (user_id,user_name, user_mobile, goods_id, goods_name, buy_num, buy_time, parent_id, parent_name, parent_mobile, price, state )
    values (".$_SESSION['member_user_id'].",'".$user['user_name']."',".$user['user_mobile'].",".$goods_id.",'".$goods['goods_name']."',".$buy_num.",
    ".$now.",".$user['parent_id'].",".$user['parent_name'].",".$user['parent_mobile'].",".$goods['shop_price'].",'未付款')";
 
	$db->query($sql);
	// 		var_dump($goods);
	ecs_header("Location: tk_xiao_shou.php?act=list&type=1/\n" );
	
}



function action_query(){
	
	global $smarty;
	global $db ,$ecs,$_CFG,$_LANG;
	admin_priv ( 'tk_chan_pin_buy_list' );
	
	if($_REQUEST['type']==0){
		$smarty->assign ( 'ur_here', "购买记录" );
	}
	else{
		$smarty->assign ( 'ur_here', "销售记录" );
	}
	
	$smarty->assign ( 'lang', $_LANG );
	
	$filter= getList();
	$smarty->assign ( 'goods_list', $filter['list']);
	$smarty->assign ( 'record_count', $filter['record_count']);
	$smarty->assign ( 'page_count', $filter['page_count'] );
	
	/* 排序标记 */
	$sort_flag = sort_flag ( $filter);
	$smarty->assign ( $sort_flag ['tag'], $sort_flag ['img'] );
	/* 显示商品列表页面 */
	assign_query_info ();
	$smarty->assign('filter', $filter);
	make_json_result($smarty->fetch('tk_xiao_shou_list.htm'), '', array('filter' => $filter, 'page_count' => $filter['page_count']));
	
}


/**
 * 获取提现列表
 * @return array
 */
function    getTiXianList(){
	global $db ,$ecs,$_CFG,$smarty;
	
	
	 
// 	 $where = " and user_id=".$_SESSION['member_user_id'];
 
	$sql ="select count(*) from  ".$ecs->table('tk_ti_xian')." where 1=1 " . $where;
	

	$filter['record_count'] = $GLOBALS['db']->getOne($sql);
	
	/* 分页大小 */
	$filter = page_and_size($filter);
	
	$sql="select * from  ".$ecs->table('tk_ti_xian')." where 1=1  ". $where." order by tk_id  desc ";//. " where user_id=".$_SESSION['member_user_id'];
	$res= $db->selectLimit($sql, $filter['page_size'], $filter['start']);
	$arr = array();
	while ($row = $db->fetchRow($res)) {
		$row['ti_xian_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['ti_xian_time']);
		$arr[] = $row;
	}
	$filter['list'] = $arr;
	$smarty->assign('type', $_REQUEST['type']);
	return  $filter;
}


/**
 * 获取用户后台id
 * @return unknown
 */
function  getUserCenterId(){
	return  $_SESSION['member_user_id'];
}

/**
 * 获取用户前台id
 * @return unknown
 */
function  getUserId(){
	return  $_SESSION['member_uid'];
}
function    getList(){
	global $db ,$ecs,$_CFG,$smarty;
	
	
	if($_REQUEST['type']==1){
		$where = " and user_id=".getUserCenterId();//用户中心的id
	}
	else{
		$where = " and parent_id=".getUserCenterId();
	}
	$sql ="select count(*) from  ".$ecs->table('tk_xiao_shou')." where 1=1 " . $where;
	
	$filter['record_count'] = $GLOBALS['db']->getOne($sql);
	
	/* 分页大小 */
	$filter = page_and_size($filter);
	
	$sql="select * from  ".$ecs->table('tk_xiao_shou')." where 1=1  ". $where;//. " where user_id=".$_SESSION['member_user_id'];
	$res= $db->selectLimit($sql, $filter['page_size'], $filter['start']);
	$arr = array();
	while ($row = $db->fetchRow($res)) {
		$row['reg_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['buy_time']);
		$arr[] = $row;
	}
	$filter['list'] = $arr;
	$smarty->assign('type', $_REQUEST['type']);
	return  $filter;
}

/**
 * 销售收益的提现
 */
function action_ti_xian_list(){
	global $db ,$ecs,$_CFG,$smarty;
	
	$filter= getTiXianList();
	$smarty->assign ( 'data_list', $filter['list']);
	$smarty->assign ( 'record_count', $filter['record_count']);
	$smarty->assign ( 'page_count', $filter['page_count'] );
	$smarty->assign ( 'full_page', 1 );
	$smarty->assign('filter', $filter);
	/* 排序标记 */
	$sort_flag = sort_flag ( $filter);
	$smarty->assign ( $sort_flag ['tag'], $sort_flag ['img'] );
	/* 显示商品列表页面 */
	assign_query_info ();
	$smarty->assign('action_link', array('text' => "新增提现申请", 'href' => 'tk_xiao_shou.php?act=to_ti_xian'));
	$smarty->display ( 'tk_xiao_shou_ti_xian_list.htm');
	
}

/**
 * 添加提现申请
 */
function  action_to_ti_xian(){
	global  $smarty,  $db ,$ecs,$_CFG,$_LANG;
	 
	$sql="select * from ".$ecs->table("tk_users_info")." where user_id=".getUserCenterId();
	$row = $db->getRow($sql);
	$smarty->assign ( 'userInfo', $row);
	$smarty->display ( 'tk_ti_xian_shen_qing.html');
}


function action_cancel_ti_xian(){
	global  $smarty,  $db ,$ecs,$_CFG,$_LANG;
	$tk_id = $_REQUEST['id'];
	
	$sql="update  ".$ecs->table("tk_ti_xian")." set state='已取消' where user_id=".getUserCenterId()." and tk_id=".$tk_id;
	$db->query($sql);
	$links[0]['text']="取消申请成功, 转到记录到页面";
	$links[0]['href'] = 'tk_xiao_shou.php?act=ti_xian_list';
	sys_msg("取消申请成功", 0, $links);
}
/**
 *  提现申请
 */
function  action_ti_xian(){
	global  $smarty,  $db ,$ecs,$_CFG,$_LANG;
	
	$ti_xian_jin_e = intval($_REQUEST['ti_xian_jin_e']);
	$sql="select user_name, mobile_phone from ".$ecs->table("users")." where user_id=".getUserId();
	
	$user = $db->getRow($sql);
	
	$sql="insert into  ".$ecs->table("tk_ti_xian")." (user_id, user_name, user_mobile, ti_xian_time, ti_xian_jin_e, state) values ("
			.getUserCenterId().",'".$user['user_name']."',".$user['mobile_phone'].",".gmtime().",".$ti_xian_jin_e.", '审核中')" ;
	 
	$db->query($sql);

    $links[0]['text']="提交申请成功, 转到记录到页面";
    $links[0]['href'] = 'tk_xiao_shou.php?act=ti_xian_list';
    sys_msg("提交申请成功", 0, $links);
 


}


function action_list(){
	
	global $smarty;
	global $db ,$ecs,$_CFG,$_LANG;
	admin_priv ( 'tk_chan_pin_buy_list' );
	 
	if($_REQUEST['type']==1){
		$smarty->assign ( 'ur_here', "购买记录" );
	}
	else{
		$smarty->assign ( 'ur_here', "销售记录" );
	}
	$smarty->assign ( 'lang', $_LANG );
	 
	
	$filter= getList();
	
 
	$smarty->assign ( 'goods_list', $filter['list']);
	$smarty->assign ( 'record_count', $filter['record_count']);
	$smarty->assign ( 'page_count', $filter['page_count'] );
	$smarty->assign ( 'full_page', 1 );
	$smarty->assign('filter', $filter);
	/* 排序标记 */
	$sort_flag = sort_flag ( $filter);
	$smarty->assign ( $sort_flag ['tag'], $sort_flag ['img'] );
	/* 显示商品列表页面 */
	assign_query_info ();
	$smarty->display ( 'tk_xiao_shou_list.htm');
	
}

 

?>