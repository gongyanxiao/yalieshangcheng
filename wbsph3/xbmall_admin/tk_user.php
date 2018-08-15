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
 
function   action_shen_he(){
	global $db ,$ecs,$_CFG,$smarty;
	$user_id = $_REQUEST['user_id'];//申请者的用户中心id
	$type=$_REQUEST['type'];
	if($type==0){
		$sql="update  ".$ecs->table("supplier_admin_user")." set role=6 where user_id=".$user_id;
		$db->query($sql);
	} else{
		
		$sql="select uid from  ".$ecs->table("supplier_admin_user")."  where user_id=".$user_id;
		$uid = $db->getOne($sql);
		$sql="delete from   ".$ecs->table("users")."  where user_id=".$uid; //删除前端用户, 让用户可以重新注册
		$db->query($sql);
		
		$sql="delete from   ".$ecs->table("supplier_admin_user")."  where user_id=".$user_id; //删除记录
		$db->query($sql);
		$sql="delete from   ".$ecs->table("supplier_admin_user")."  where user_id=".$user_id; //删除记录
		$db->query($sql);
	}
	ecs_header("Location: tk_user.php?act=list&type=7\n");//转到未审核列表
	
}
function   action_list(){
	global $db ,$ecs,$_CFG,$smarty;
	$user_role= $_REQUEST['type'];
	$filter = getList();
	
	$smarty->assign ( 'data_list', $filter['list']);
	$smarty->assign ( 'record_count', $filter['record_count']);
	$smarty->assign ( 'page_count', $filter['page_count'] );
	$smarty->assign ( 'full_page', 1 );
	$smarty->assign('filter', $filter);
	
	/* 排序标记 */
	$sort_flag = sort_flag ( $filter);
	$smarty->assign ( $sort_flag ['tag'], $sort_flag ['img'] );
	/* 显示商品列表页面 */
	$smarty->display("tk_user_list.htm");
}

 
function    getList(){
	global $db ,$ecs,$_CFG,$smarty;
	
	$user_role= $_REQUEST['type'];
	 
 
	 
	$sql ="select count(*) from  ".$ecs->table('supplier_admin_user')." where 1=1 and role=".$user_role;
	
	$filter['record_count'] = $GLOBALS['db']->getOne($sql);
	
	/* 分页大小 */
	$filter = page_and_size($filter);
	
	$sql="SELECT
	b.real_name,
	c.mobile_phone as tui_jian_mobile_phone,
	c.real_name as tui_jian_real_name,
	a.*
FROM
	xbmall_supplier_admin_user a
JOIN xbmall_users b ON a.uid = b.user_id
JOIN xbmall_users c ON b.parent_id = c.user_id  where a.role= ". $user_role; 
 
	$res= $db->selectLimit($sql, $filter['page_size'], $filter['start']);
	 
	$arr = array();
	while ($row = $db->fetchRow($res)) {
		$row['add_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['add_time']);
		$row['state'] = $row['state']==0?"会员":"代理";
		$arr[] = $row;
	}
 
	$filter['list'] = $arr;
	$smarty->assign('type', $_REQUEST['type']);
	return  $filter;
}



function action_zi_liao(){
	
	global $smarty;
	global $db ,$ecs,$_CFG;
	$goods_id = $_REQUEST ['goods_id'];
	if($_SESSION['member_role']==7){
		ecs_header ( "Location: index.php?act=main\n" );
	}
	// 		var_dump($goods);
 
	
}

function action_sheng_ji(){
	
	global $smarty;
	global $db ,$ecs,$_CFG;
	$goods_id = $_REQUEST ['goods_id'];
	var_dump($_SESSION); 
  
	// 		var_dump($goods);
	$smarty->display("tk_sheng_ji.html");
	
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
     　

?>