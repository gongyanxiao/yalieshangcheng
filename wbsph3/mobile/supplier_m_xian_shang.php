<?php
/**
 * ============================================================================
 * * 版权所有 2017-2022 儒孝养老服务有限公司，并保留所有权利。
 *   线上店铺相关功能
 *   网站地址: http://www.xiangbai315.com;
 * ----------------------------------------------------------------------------
 */
define ( 'IN_ECS', true );
require (dirname ( __FILE__ ) . '/includes/init.php');
/* 载入语言文件 */
require_once (ROOT_PATH . 'languages/' . $_CFG ['lang'] . '/user.php');
// 不需要登录的操作或自己验证是否登录（如ajax处理）的act
$not_login_arr = array (
		'login',
);

/* 显示页面的action列表 */

$ui_arr [] = 'get_user';
$ui_arr [] = 'install';
$ui_arr [] = 'uninstall';
$ui_arr [] = 'is_default_show';

/* end */
$user_id = isset ( $_SESSION ['user_id'] ) ? $_SESSION ['user_id'] : 0;
$action = isset ( $_REQUEST ['act'] ) ? trim ( $_REQUEST ['act'] ) : 'default';

/* 未登录处理 */
if (empty ( $_SESSION ['user_id'] )) {
	if (! in_array ( $action, $not_login_arr )) {
		if (in_array ( $action, $ui_arr )) {
			if (! empty ( $_SERVER ['QUERY_STRING'] )) {
				$back_act = 'user.php?' . strip_tags ( $_SERVER ['QUERY_STRING'] );
			}
			$action = 'login';
		} else {
			// 未登录提交数据。非正常途径提交数据！
			show_message ( $_LANG ['require_login'], array (
					'</br>登录',
					'</br>返回首页' 
			), array (
					'user.php?act=login',
					$ecs->url () 
			), 'error', false );
		}
	}
}

/* 路由 */

$function_name = 'action_' . $action;

if (! function_exists ( $function_name )) {
	$function_name = "action_default";
}

call_user_func ( $function_name );



//加载快递模块(包含语言包)
function includeShippingModules(){
	$modules =read_modules('../includes/modules/shipping');
	return $modules;
}

//获取快递模块的路径
function getShippingModuleRootPath(){
	$pos=strpos(ROOT_PATH,"mobile/");
	$shippingRootPath= substr(ROOT_PATH,0,$pos).'languages/' .$GLOBALS['_CFG']['lang']. '/shipping/';
	return $shippingRootPath;
	
}

/**
 * 获取店铺的根目录
 * @return string
 */
function getShopRoot(){
	$pos=strpos(ROOT_PATH,"mobile/");
	$shippingRootPath= substr(ROOT_PATH,0,$pos);
	return $shippingRootPath;
	
}

 
/**
 * 配送方式列表
 */
function action_to_pei_song_list() {
	$smarty = $GLOBALS ['smarty'];
	$ecs= $GLOBALS['ecs'];
	$db= $GLOBALS['db'];
	$modules = includeShippingModules();
	$shippingRootPath= getShippingModuleRootPath();
	for ($i = 0; $i < count($modules); $i++)
	{
		$lang_file =$shippingRootPath.$modules[$i]['code']. '.php';
		
		if (file_exists($lang_file))
		{
			include_once($lang_file);
		}
		/* 检查该插件是否已经安装 */
		$sql = "SELECT a.shipping_id, a.shipping_name, a.shipping_desc, a.insure, a.support_cod,a.shipping_order,a.is_default_show FROM " 
				.$ecs->table('shipping'). " a join ".$ecs->table('supplier'). " b on a.supplier_id=b.supplier_id  WHERE a.shipping_code='" 
						.$modules[$i]['code']. "' and b.user_id =".$_SESSION['user_id']." ORDER BY a.shipping_order";
						
		$row = $db->GetRow($sql);
		
		if ($row)
		{
			/* 插件已经安装了，获得名称以及描述 */
			$modules[$i]['id']      = $row['shipping_id'];
			$modules[$i]['name']    = $row['shipping_name'];
			$modules[$i]['desc']    = $row['shipping_desc'];
			$modules[$i]['insure_fee']  = $row['insure'];
			$modules[$i]['cod']     = $row['support_cod'];
			$modules[$i]['shipping_order'] = $row['shipping_order'];
			$modules[$i]['install'] = 1;
			
			if (isset($modules[$i]['insure']) && ($modules[$i]['insure'] === false))
			{
				$modules[$i]['is_insure']  = 0;
			}
			else
			{
				$modules[$i]['is_insure']  = 1;
			}
		}
		else
		{
			$modules[$i]['name']    = $_LANG[$modules[$i]['code']];
			$modules[$i]['desc']    = $_LANG[$modules[$i]['desc']];
			$modules[$i]['insure_fee']  = empty($modules[$i]['insure'])? 0 : $modules[$i]['insure'];
			$modules[$i]['cod']     = $modules[$i]['cod'];
			$modules[$i]['install'] = 0;
		}
		$modules[$i]['is_default_show'] = $row['is_default_show'];
	
	}
	
	$ret = array();
	foreach($modules as $key => $val){
		if(in_array($val['code'],$display1)){
			//门店自提
			$ret[0]['shiplist'][$key] = $val;
			$ret[0]['name'] = '门店自提';
			$ret[0]['desc'] = "门店自提区别于普通快递和同城快递，安装后买家选择门店自提后可选择距离自己最近的自提点，快递送至自提点后，买家可自行上门提货（自提点设置见自提点管理）";
		}elseif(in_array($val['code'],$display2)){
			//同城快递
			$ret[1]['shiplist'][$key] = $val;
			$ret[1]['name'] = '同城快递';
			$ret[1]['desc'] = "同城快递区别于普通快递和门店自提，是网站自己的快递，主要用于进行同城配送，安装后买家选择同城快递，后台即可生成快递配送物流单（物流单见订单管理-快递单列表）";
		}elseif(!in_array($val['code'],$display3_not)){
			//普通快递
			$ret[2]['shiplist'][$key] = $val;
			$ret[2]['name'] = '普通快递';
			$ret[2]['desc'] = "普通快递区别于同城快递和门店自提，您可以设置默认的普通快递，如:申通快递，买家选择普通快递时,订单自动使用申通快递";
		}
	}
	
	sort($ret);

	$smarty->assign('ur_here', $_LANG['03_shipping_list']);
	$smarty->assign('modules', $ret);
	$smarty->display('supplier/pei_song_fang_shi_list.dwt');
	
}
 


function action_is_default_show(){
	require(getShopRoot(). 'includes/lib_shipping.php');
	global $ecs, $_LANG;
	$smarty = $GLOBALS ['smarty'];
	$ecs= $GLOBALS['ecs'];
	$db= $GLOBALS['db'];
	$supplier_id=getSupplierIdMF();
// 	if(in_array($_REQUEST['code'],$not_set_default)){
// 		$lnk[] = array('text' => $_LANG['go_back'], 'href'=>'supplier_m_xian_shang.php?act=to_pei_song_list');
// 		sys_msg('这种配送方式没必要设置默认', 0, $lnk);
// 	}
 
	$sql = "select shipping_id,shipping_name from ".$ecs->table("shipping")." where is_default_show=0 and supplier_id=".$supplier_id." and enabled=1 and shipping_code='".$_REQUEST['code']."'";
	
	$info = $db->getRow($sql);
	
	if($info){
		
		$shipping_name = $info['shipping_name'];
		$shipping_id = $info['shipping_id'];
		
		set_default_show($shipping_id,$supplier_id);
		
		//记录管理员操作
		//admin_log(addslashes($shipping_name), 'is_default_show', 'shipping');
		 
		$lnk[] = array('text' => $_LANG['go_back'], 'href'=>'supplier_m_xian_shang.php?act=to_pei_song_list');
		$lnk[] = array('text' => "返回商家中心", 'href'=>'user.php?act=supplier_center');
		sys_msg( "配送方式 ".$shipping_name."已经成功设置。 ", 0, $lnk);
	}else{
		$lnk[] = array('text' => $_LANG['go_back'], 'href'=>'supplier_m_xian_shang.php?act=to_pei_song_list');
		$lnk[] = array('text' => "返回商家中心", 'href'=>'user.php?act=supplier_center');
		sys_msg('已经是默认显示配送地址', 0, $lnk);
	}
	
}


function action_uninstall(){
	
	global $ecs, $_LANG;
	$smarty = $GLOBALS ['smarty'];
	$ecs= $GLOBALS['ecs'];
	$db= $GLOBALS['db'];
	
	$supplier_id=getSupplierIdMF();
	/* 获得该配送方式的ID */
	$row = $db->GetRow("SELECT shipping_id,shipping_code, shipping_name, print_bg FROM " .$ecs->table('shipping'). " WHERE shipping_code='$_GET[code]' and supplier_id=".$supplier_id);
	if($row['is_default_show'] && !in_array($row['shipping_code'],$not_set_default)){
		$lnk[] = array('text' => $_LANG['go_back'], 'href'=>'shipping.php?act=list');
		$lnk[] = array('text' => "返回商家中心", 'href'=>'user.php?act=supplier_center');
		sys_msg('默认配送地址不可以卸载,请先设置别的配送地址为默认后再卸载', 0, $lnk);
	}
	$shipping_id = $row['shipping_id'];
	$shipping_name = $row['shipping_name'];
	
	/* 删除 shipping_fee 以及 shipping 表中的数据 */
	if ($row)
	{
		$all = $db->getCol("SELECT shipping_area_id FROM " .$ecs->table('shipping_area'). " WHERE shipping_id='$shipping_id'");
		$in  = db_create_in(join(',', $all));
		
		$db->query("DELETE FROM " .$ecs->table('area_region'). " WHERE shipping_area_id $in");
		$db->query("DELETE FROM " .$ecs->table('shipping_area'). " WHERE shipping_id='$shipping_id'");
		$db->query("DELETE FROM " .$ecs->table('shipping'). " WHERE shipping_id='$shipping_id'");
		
		//删除上传的非默认快递单
		if (($row['print_bg'] != '') && (!is_print_bg_default($row['print_bg'])))
		{
			
			@unlink(ROOT_PATH . $row['print_bg']);//这里跟pc端的路径不一样
		}
		
		//记录管理员操作
		//admin_log(addslashes($shipping_name), 'uninstall', 'shipping');
		
		$lnk[] = array('text' => "返回", 'href'=>'supplier_m_xian_shang.php?act=to_pei_song_list');
		$lnk[] = array('text' => "返回商家中心", 'href'=>'user.php?act=supplier_center');
		sys_msg(sprintf('配送方式 %s 已经成功卸载。', $shipping_name), 0, $lnk);
	}
	
}

/**
 * 判断是否为默认安装快递单背景图片
 *
 * @param   string      $print_bg      快递单背景图片路径名
 * @access  private
 *
 * @return  Bool
 */
function is_print_bg_default($print_bg)
{
	$_bg = basename($print_bg);
	
	$_bg_array = explode('.', $_bg);
	
	if (count($_bg_array) != 2)
	{
		return false;
	}
	
	if (strpos('|' . $_bg_array[0], 'dly_') != 1)
	{
		return false;
	}
	
	$_bg_array[0] = ltrim($_bg_array[0], 'dly_');
	$list = explode('|', SHIP_LIST);
	
	if (in_array($_bg_array[0], $list))
	{
		return true;
	}
	
	return false;
}


function action_install(){
	$smarty = $GLOBALS ['smarty'];
	$ecs= $GLOBALS['ecs'];
	$db= $GLOBALS['db'];
	$set_modules = true;
	$shippingRootPath= getShippingModuleRootPath();
	include_once($shippingRootPath. $_GET['code'] . '.php');
	$modules = includeShippingModules();
	$shippingRootPath= getShippingModuleRootPath();
	$supplierId=getSupplierIdMF();
	if(!$supplierId){//没有店铺
		$links[] = array('text' => "注册店铺", 'href' => 'supplier_reg.php');
		sys_msg("您还没有开通店铺",0,$links);
	}
	/* 检查该配送方式商家是否已经安装 */
	$sql = "SELECT shipping_id FROM " .$ecs->table('shipping'). " WHERE shipping_code = '$_GET[code]' and supplier_id=".$supplierId;
 
	$id = $db->GetOne($sql);
	
	if ($id > 0)
	{
		/* 该配送方式已经安装过, 将该配送方式的状态设置为 enable */
		$db->query("UPDATE " .$ecs->table('shipping'). " SET enabled = 1 WHERE shipping_code = '$_GET[code]' and supplier_id=".$supplierId." LIMIT 1");
	}
	else
	{
		 
		  foreach($modules as $key => $val){
		  	if($val['code']!=$_GET[code])continue;
		  	$insure = empty($val['insure']) ? 0 : $val['insure'];
		  	$support_pickup =  isset($val['support_pickup']) && $val['support_pickup'] ? 1 : 0;
		  	$is_default_show = (in_array(addslashes($val['code']),$not_set_default)) ? 1 : 0;//如果在设置数据中，添加时就为默认配送方式
		  	$sql = "INSERT INTO " . $ecs->table('shipping') . " (" .
				  	"shipping_code, shipping_name, shipping_desc, insure, support_cod, enabled, print_bg, config_lable, print_model , support_pickup, is_default_show" .
				  	",supplier_id) VALUES (" .
				  	"'" . addslashes($val['code']). "', '" . addslashes($_LANG[$val['code']]) . "', '" .
				  	addslashes($_LANG[$val['desc']]) . "', '$insure', '" . intval($val['cod']) . "', 1, '" . addslashes($val['print_bg']) . "', '" . addslashes($val['config_lable']) . "', '" . $modules[0]['print_model'] . "', $support_pickup, $is_default_show,".$_SESSION['supplier_id'].")";
				  	$db->query($sql);
				  	$id = $db->insert_Id();
			 /* 该配送方式没有安装过, 将该配送方式的信息添加到数据库 */
		  }
	
		
	}
	
	/* 记录管理员操作 */
	//admin_log(addslashes($_LANG[$modules[0]['code']]), 'install', 'shipping');

	/* 提示信息 */
	$lnk[] = array('text' => "设置区域", 'href' => 'supplier_m_pei_song_qu_yu.php?act=list&shipping=' . $id);
	$lnk[] = array('text' => 返回, 'href' => 'supplier_m_xian_shang.php?act=to_pei_song_list');
	$lnk[] = array('text' => "返回商家中心", 'href'=>'user.php?act=supplier_center');
	sys_msg(sprintf("安装成功",  $modules[0]['name'] ), 0, $lnk);
	
}
/**
 * 根据用户id获取用户,是否可以报单
 */
function action_get_user() {
	if (! empty ( $_POST ['user_id'] )) {
		
		$userInfo = get_user_info_bymobile ( $_POST ['user_id'] );
		exit ( json_encode ( $userInfo ) );
	}
}

/**
 * 做单列表
 *
 * @return array
 */
function get_offline_man_list() {
	$filter ['keywords'] = isset ( $_REQUEST ['keywords'] ) ? trim ( addslashes ( htmlspecialchars ( $_REQUEST ['keywords'] ) ) ) : '';
	$filter ['sort_by'] = empty ( $_REQUEST ['sort_by'] ) ? 'sort_order' : trim ( $_REQUEST ['sort_by'] );
	$filter ['sort_order'] = empty ( $_REQUEST ['sort_order'] ) ? 'ASC' : trim ( $_REQUEST ['sort_order'] );
	
	$result = get_filter (); // 原有的查询条件
	if ($result === false) {
		 
		/* 初始化分页参数 */
		$filter = array ();
		$filter ['sort_by'] = empty ( $_REQUEST ['sort_by'] ) ? 'id' : trim ( $_REQUEST ['sort_by'] );
		$filter ['sort_order'] = empty ( $_REQUEST ['sort_order'] ) ? 'DESC' : trim ( $_REQUEST ['sort_order'] );
		
		/* 初始化时间查询条件 */
		$up_time = strtotime ( $_REQUEST ['up_time'] ); // 开始时间
		$end_time = strtotime ( $_REQUEST ['end_time'] ); // 结束时间
		$where = " AND  type=0 AND 1=1";
		if ($up_time > 0 || $end_time > 0) {
			$up_time = $up_time > 0 ? $up_time - 28800 : 0;
			$end_time = $end_time > 0 ? $end_time - 28800 : 0;
			$where = " and createtime BETWEEN $up_time AND $end_time ";
		}
		
		/* 统计 订单金额 报单费 */
		$sql_sum = "SELECT sum(order_amt) as order_amt_sum , sum(order_bdf) as order_bdf_sum FROM " . $GLOBALS ['ecs']->table ( 'order_bd' ) . "  WHERE member_id='" . $_SESSION ['user_id'] . "' {$where} ";
		
		$order_sum = $GLOBALS ['db']->getRow ( $sql_sum );
		
		/* 查询记录总数，计算分页数 */
		$sql = "SELECT COUNT(*)  FROM " 
				. $GLOBALS ['ecs']->table ( 'order_bd' ) . " s LEFT JOIN " .
		$GLOBALS ['ecs']->table ( 'users' ) . " a ON s.user_id = a.user_id WHERE s.supplier_id = '"
				. $_SESSION ['user_id'] . "' ". $where;
		$filter ['record_count'] = $GLOBALS ['db']->getOne ( $sql );
		$filter = page_and_size ( $filter );
 
		/* 查询记录 */
		$sql = "SELECT	s.id,	s.createtime,	s.fp_url,	s.good_url,	s.good_name,	s.order_amt,	s.order_bdf,	s.order_amt,	a.user_name,	a.real_name FROM " 
				. $GLOBALS ['ecs']->table ( 'order_bd' ) . " s LEFT JOIN " .
		$GLOBALS ['ecs']->table ( 'users' ) . " a ON s.user_id = a.user_id WHERE s.supplier_id = '"
				. $_SESSION ['user_id'] . "'  {$where}  ORDER BY $filter[sort_by] $filter[sort_order]";
	 
		set_filter ( $filter, $sql );
	} else {
		$sql = $result ['sql'];
		$filter = $result ['filter'];
	}
	$res = $GLOBALS ['db']->selectLimit ( $sql, $filter ['page_size'], $filter ['start'] );
	
	$arr = array ();
	while ( $rows = $GLOBALS ['db']->fetchRow ( $res ) ) {
		$rows ["order_time"] = local_date ( "Y-m-d H:i:s", $rows ['createtime'] );
		$arr [] = $rows;
	}
	
	/* 在分页数组中添加 统计数据 */
	$filter ['order_amt_sum'] = $order_sum ['order_amt_sum'];
	$filter ['order_bdf_sum'] = $order_sum ['order_bdf_sum'];

	$pager = get_pager('supplier_m_zuo_dan.php', array('act' => 'list'), $filter['record_count'], $filter['page'], $filter['page_size']);
 
	return array (
			'agency' => $arr,
			'filter' => $filter,
			'page_count' => $filter ['page_count'],
			'record_count' => $filter ['record_count'],
			'order_amt_sum' => $order_sum ['order_amt_sum'],
			'order_bdf_sum' => $order_sum ['order_bdf_sum'] ,
			'pager'=>$pager
	);
	
}

?>