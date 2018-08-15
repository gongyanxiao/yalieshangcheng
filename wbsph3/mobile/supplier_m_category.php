<?php
/**
 * ============================================================================
 * * 版权所有 2017-2022 儒孝养老服务有限公司，并保留所有权利。
 *   周：店内分类
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

$ui_arr [] = 'list';

$ui_arr [] = 'add';
$ui_arr [] = 'insert';
$ui_arr [] = 'edit';
$ui_arr [] = 'update';
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
 * 获得商品分类的所有信息
 *
 * @param   integer     $cat_id     指定的分类ID
 *
 * @return  mix
 */
function get_cat_info($cat_id)
{
	$sql = "SELECT * FROM " .$GLOBALS['ecs']->table('supplier_category'). " WHERE cat_id='$cat_id' LIMIT 1";
	return $GLOBALS['db']->getRow($sql);
}

/**
 * 获取属性列表
 *
 * @access  public
 * @param
 *
 * @return void
 */
function get_attr_list_private()
{
	$sql = "SELECT a.attr_id, a.cat_id, a.attr_name ".
			" FROM " . $GLOBALS['ecs']->table('attribute'). " AS a,  ".
			$GLOBALS['ecs']->table('goods_type') . " AS c ".
			" WHERE  a.cat_id = c.cat_id AND c.enabled = 1 ".
			" ORDER BY a.cat_id , a.sort_order";
			
			$arr = $GLOBALS['db']->getAll($sql);
			
			$list = array();
			
			foreach ($arr as $val)
			{
				$list[$val['cat_id']][] = array($val['attr_id']=>$val['attr_name']);
			}
			
			return $list;
}



function action_edit() {
	$smarty = $GLOBALS ['smarty'];
	$ecs= $GLOBALS['ecs'];
	$db= $GLOBALS['db'];
	$cat_id = intval($_REQUEST['cat_id']);
	$cat_info = get_cat_info($cat_id);  // 查询分类信息数据
	$attr_list = get_attr_list_private();
	$filter_attr_list = array();
	
	if ($cat_info['filter_attr'])
	{
		$filter_attr = explode(",", $cat_info['filter_attr']);  //把多个筛选属性放到数组中
		
		foreach ($filter_attr AS $k => $v)
		{
			$attr_cat_id = $db->getOne("SELECT cat_id FROM " . $ecs->table('attribute') . " WHERE attr_id = '" . intval($v) . "'");
			$filter_attr_list[$k]['goods_type_list'] = goods_type_list($attr_cat_id);  //取得每个属性的商品类型
			$filter_attr_list[$k]['filter_attr'] = $v;
			$attr_option = array();
			
			foreach ($attr_list[$attr_cat_id] as $val)
			{
				$attr_option[key($val)] = current ($val);
			}
			
			$filter_attr_list[$k]['option'] = $attr_option;
		}
		
		$smarty->assign('filter_attr_list', $filter_attr_list);
	}
	else
	{
		$attr_cat_id = 0;
	}
	/* 模板赋值 */
	$smarty->assign('attr_list',        $attr_list); // 取得商品属性
	$smarty->assign('attr_cat_id',      $attr_cat_id);
	$smarty->assign('ur_here',     $_LANG['category_edit']);
	$smarty->assign('action_link', array('text' => $_LANG['04_category_list'], 'href' => 'category.php?act=list'));
	
	//分类是否存在首页推荐
	$res = $db->getAll("SELECT recommend_type FROM " . $ecs->table("supplier_cat_recommend") . " WHERE cat_id=" . $cat_id . " AND supplier_id=".$_SESSION['supplier_id']);
	if (!empty($res))
	{
		$cat_recommend = array();
		foreach($res as $data)
		{
			$cat_recommend[$data['recommend_type']] = 1;
		}
		$smarty->assign('cat_recommend', $cat_recommend);
	}
	
	$smarty->assign('cat_info',    $cat_info);
	$smarty->assign('form_act',    'update');
	$smarty->assign('cat_select',  cat_list_2(0, $cat_info['parent_id'], true));
	$smarty->assign('goods_type_list',  goods_type_list(0)); // 取得商品类型
	
	/* 显示页面 */
 
	$smarty->display('supplier/category_info.dwt');
	
}

function action_insert() {

	$smarty = $GLOBALS ['smarty'];
	$ecs= $GLOBALS['ecs'];
	$db= $GLOBALS['db'];
	/* 初始化变量 */
	$cat['cat_id']       = !empty($_POST['cat_id'])       ? intval($_POST['cat_id'])     : 0;
	$cat['parent_id']    = !empty($_POST['parent_id'])    ? intval($_POST['parent_id'])  : 0;
	$cat['sort_order']   = !empty($_POST['sort_order'])   ? intval($_POST['sort_order']) : 0;
	$cat['keywords']     = !empty($_POST['keywords'])     ? trim($_POST['keywords'])     : '';
	$cat['cat_desc']     = !empty($_POST['cat_desc'])     ? $_POST['cat_desc']           : '';
	$cat['measure_unit'] = !empty($_POST['measure_unit']) ? trim($_POST['measure_unit']) : '';
	$cat['cat_name']     = !empty($_POST['cat_name'])     ? trim($_POST['cat_name'])     : '';
	$arrCatName = explode("," ,$cat['cat_name']);
	$cat['show_in_nav']  = !empty($_POST['show_in_nav'])  ? intval($_POST['show_in_nav']): 0;
	$cat['is_show_cat_pic'] = !empty($_POST['is_show_cat_pic'])  ? intval($_POST['is_show_cat_pic']): 0;
	$cat['cat_pic_url'] = !empty($_POST['cat_pic_url'])  ? htmlspecialchars($_POST['cat_pic_url']): '';
	$cat['cat_goods_limit'] = !empty($_POST['cat_goods_limit'])  ? intval($_POST['cat_goods_limit']): '';
	$cat['style']        = !empty($_POST['style'])        ? trim($_POST['style'])        : '';
	$cat['is_show']      = !empty($_POST['is_show'])      ? intval($_POST['is_show'])    : 0;
	$cat['grade']        = !empty($_POST['grade'])        ? intval($_POST['grade'])      : 0;
	$cat['filter_attr']  = !empty($_POST['filter_attr'])  ? implode(',', array_unique(array_diff($_POST['filter_attr'],array(0)))) : 0;
	
	$cat['cat_recommend']  = !empty($_POST['cat_recommend'])  ? $_POST['cat_recommend'] : array();
	$cat['supplier_id'] = getSupplierIdMF();
	
	$catpic = upload_category_pic($cat['cat_id']);//上传图片
	if(!empty($catpic)){
		$cat['cat_pic'] = $catpic;
	}
	/* 代码增加 By  www.68ecshop.com Start */
	foreach($arrCatName as $arrCatNameValue)
	{
		$cat['cat_name'] = $arrCatNameValue;
		/* 代码增加 By  www.68ecshop.com End */
		
		if (cat_exists_supplier(getSupplierIdMF(),$cat['cat_name'], $cat['parent_id']))
		{
			/* 同级别下不能有重复的分类名称 */
			$link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
			sys_msg($_LANG['catname_exist'], 0, $link);
		}
		
		if($cat['grade'] > 10 || $cat['grade'] < 0)
		{
			/* 价格区间数超过范围 */
			$link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
			sys_msg($_LANG['grade_error'], 0, $link);
		}
		
		/* 入库的操作 */
		if ($db->autoExecute($ecs->table('supplier_category'), $cat) !== false)
		{
			$cat_id = $db->insert_id();
			/*
			 *  暂时注释掉这部分代码
			 */
			
			if($cat['show_in_nav'] == 1)
			{
				$vieworder = $db->getOne("SELECT max(vieworder) FROM ". $ecs->table('supplier_nav') . " WHERE type = 'middle' AND supplier_id=".getSupplierIdMF());
				$vieworder += 2;
				//显示在自定义导航栏中
				$sql = "INSERT INTO " . $ecs->table('supplier_nav') .
				" (name,ctype,cid,ifshow,vieworder,opennew,url,type,supplier_id)".
				" VALUES('" . $cat['cat_name'] . "', 'c', '".$db->insert_id()."','1','$vieworder','0', '" . build_uri('supplier', array('go'=>'category','suppid'=>getSupplierIdMF(),'cid'=> $cat_id), $cat['cat_name']) . "','middle',".getSupplierIdMF().")";
				$db->query($sql);
			}
			insert_cat_recommend($cat['cat_recommend'], $cat_id);
			/* 代码增加 By  www.68ecshop.com Start */
		}
	}
	/* 代码增加 By  www.68ecshop.com End */
	
	
	//admin_log($_POST['cat_name'], 'add', 'category');   // 记录管理员操作
	clear_cache_files();    // 清除缓存
	
	/*添加链接*/
	$link[0]['text'] = "继续添加分类";
	$link[0]['href'] = 'supplier_m_category.php?act=add';
	
	$link[1]['text'] = "分类列表";
	$link[1]['href'] = 'supplier_m_category.php?act=list';
	
	$link[2]['text'] = "添加商品";
	$link[2]['href'] = 'supplier_m_goods.php?act=add';
	sys_msg("添加成功", 0, $link);
	
	
}
function action_remove() {
	
	$smarty = $GLOBALS ['smarty'];
	$ecs= $GLOBALS['ecs'];
	$db= $GLOBALS['db'];
 
	
	/* 初始化分类ID并取得分类名称 */
	$cat_id   = intval($_GET['id']);
	$cat_name = $db->getOne('SELECT cat_name FROM ' .$ecs->table('supplier_category'). " WHERE cat_id='$cat_id'");
	
	/* 当前分类下是否有子分类 */
	$cat_count = $db->getOne('SELECT COUNT(*) FROM ' .$ecs->table('supplier_category'). " WHERE parent_id='$cat_id' AND supplier_id=".getSupplierIdMF());
	
	/* 当前分类下是否存在商品 */
	//$goods_count = $db->getOne('SELECT COUNT(*) FROM ' .$ecs->table('goods'). " WHERE cat_id='$cat_id' AND supplier_id = ".$_SESSION['supplier_id']);
	$goods_count = $db->getOne('SELECT COUNT(*) FROM ' .$ecs->table('supplier_goods_cat'). " WHERE cat_id='$cat_id' AND supplier_id = ".getSupplierIdMF());
	
	/* 如果不存在下级子分类和商品，则删除之 */
	if ($cat_count == 0 && $goods_count == 0)
	{
		/* 删除分类 */
		$sql = 'DELETE FROM ' .$ecs->table('supplier_category'). " WHERE cat_id = '$cat_id'";
		if ($db->query($sql))
		{
			//$db->query("DELETE FROM " . $ecs->table('nav') . "WHERE ctype = 'c' AND cid = '" . $cat_id . "' AND type = 'middle'");
			clear_cache_files();
			//admin_log($cat_name, 'remove', 'category');
		}
	}
	else
	{
		make_json_error($cat_name .' '. $_LANG['cat_isleaf']);
	}
	
	$url = 'supplier_m_category.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
	ecs_header("Location: $url\n");
	exit;
	
}
function action_query() {
	$smarty = $GLOBALS ['smarty'];
	/* 代码修改 By  www.68ecshop.com Start */
	if(empty($_POST['cat_name']))
	{
		/* 获取分类列表 */
		$cat_list = cat_list_2(0, 0, false);
	}
	// 如果查询条件不为空
	else {
		$cat_list = search_cat($_POST['cat_name']);
	}
	/* 代码修改 By  www.68ecshop.com End */
	$smarty->assign('cat_info',     $cat_list);
	
	make_json_result($smarty->fetch('supplier/category_list.dwt'));
	
}

function action_update() {
	
	$smarty = $GLOBALS ['smarty'];
	$ecs= $GLOBALS['ecs'];
	$db= $GLOBALS['db'];
	/* 初始化变量 */
	$cat_id              = !empty($_POST['cat_id'])       ? intval($_POST['cat_id'])     : 0;
	$old_cat_name        = $_POST['old_cat_name'];
	$cat['parent_id']    = !empty($_POST['parent_id'])    ? intval($_POST['parent_id'])  : 0;
	$cat['sort_order']   = !empty($_POST['sort_order'])   ? intval($_POST['sort_order']) : 0;
	$cat['keywords']     = !empty($_POST['keywords'])     ? trim($_POST['keywords'])     : '';
	$cat['cat_desc']     = !empty($_POST['cat_desc'])     ? $_POST['cat_desc']           : '';
	$cat['measure_unit'] = !empty($_POST['measure_unit']) ? trim($_POST['measure_unit']) : '';
	$cat['cat_name']     = !empty($_POST['cat_name'])     ? trim($_POST['cat_name'])     : '';
	$cat['is_show']      = !empty($_POST['is_show'])      ? intval($_POST['is_show'])    : 0;
	$cat['show_in_nav']  = !empty($_POST['show_in_nav'])  ? intval($_POST['show_in_nav']): 0;
	$cat['is_show_cat_pic'] = !empty($_POST['is_show_cat_pic'])  ? intval($_POST['is_show_cat_pic']): 0;
	$cat['cat_pic_url'] = !empty($_POST['cat_pic_url'])  ? htmlspecialchars($_POST['cat_pic_url']): '';
	$cat['cat_goods_limit'] = !empty($_POST['cat_goods_limit'])  ? intval($_POST['cat_goods_limit']): '';
	$cat['style']        = !empty($_POST['style'])        ? trim($_POST['style'])        : '';
	$cat['grade']        = !empty($_POST['grade'])        ? intval($_POST['grade'])      : 0;
	$cat['filter_attr']  = !empty($_POST['filter_attr'])  ? implode(',', array_unique(array_diff($_POST['filter_attr'],array(0)))) : 0;
	$cat['cat_recommend']  = !empty($_POST['cat_recommend'])  ? $_POST['cat_recommend'] : array();
	$cat['supplier_id'] = $_SESSION['supplier_id'];
	
	$catpic = upload_category_pic($cat_id);//上传图片
	if(!empty($catpic)){
		$cat['cat_pic'] = $catpic;
	}
	
	/* 判断分类名是否重复 */
	
	if ($cat['cat_name'] != $old_cat_name)
	{
		if (cat_exists_supplier($_SESSION['supplier_id'],$cat['cat_name'],$cat['parent_id'], $cat_id))
		{
			$link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
			sys_msg($_LANG['catname_exist'], 0, $link);
		}
	}
	
	/* 判断上级目录是否合法 */
	$children = array_keys(cat_list_2($cat_id, 0, false));     // 获得当前分类的所有下级分类
	if (in_array($cat['parent_id'], $children))
	{
		/* 选定的父类是当前分类或当前分类的下级分类 */
		$link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
		sys_msg($_LANG["is_leaf_error"], 0, $link);
	}
	
	if($cat['grade'] > 10 || $cat['grade'] < 0)
	{
		/* 价格区间数超过范围 */
		$link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
		sys_msg($_LANG['grade_error'], 0, $link);
	}
	
	$dat = $db->getRow("SELECT cat_name, show_in_nav FROM ". $ecs->table('supplier_category') . " WHERE cat_id = '$cat_id'");
	
	if ($db->autoExecute($ecs->table('supplier_category'), $cat, 'UPDATE', "cat_id='$cat_id'"))
	{
		if($cat['cat_name'] != $dat['cat_name'])
		{
			//如果分类名称发生了改变
			$sql = "UPDATE " . $ecs->table('supplier_nav') . " SET name = '" . $cat['cat_name'] . "' WHERE ctype = 'c' AND cid = '" . $cat_id . "' AND type = 'middle' AND supplier_id=".getSupplierIdMF();
			$db->query($sql);
		}
		
		/*
		 * 暂时注释掉这部分代码
		 */
		
		if($cat['show_in_nav'] != $dat['show_in_nav'])
		{
			//是否显示于导航栏发生了变化
			if($cat['show_in_nav'] == 1)
			{
				//显示
				$nid = $db->getOne("SELECT id FROM ". $ecs->table('supplier_nav') . " WHERE ctype = 'c' AND cid = '" . $cat_id . "' AND type = 'middle' AND supplier_id=".getSupplierIdMF());
				if(empty($nid))
				{
					//不存在
					$vieworder = $db->getOne("SELECT max(vieworder) FROM ". $ecs->table('supplier_nav') . " WHERE type = 'middle' AND supplier_id=".getSupplierIdMF());
					$vieworder += 2;
					$uri = build_uri('supplier', array('go'=>'category','suppid'=>getSupplierIdMF(),'cid'=> $cat_id), $cat['cat_name']);
					
					$sql = "INSERT INTO " . $ecs->table('supplier_nav') . " (name,ctype,cid,ifshow,vieworder,opennew,url,type,supplier_id) VALUES('" . $cat['cat_name'] . "', 'c', '$cat_id','1','$vieworder','0', '" . $uri . "','middle',".$_SESSION['supplier_id'].")";
				}
				else
				{
					$sql = "UPDATE " . $ecs->table('supplier_nav') . " SET ifshow = 1 WHERE ctype = 'c' AND cid = '" . $cat_id . "' AND type = 'middle' AND supplier_id=".getSupplierIdMF();
				}
				$db->query($sql);
			}
			else
			{
				//去除
				$db->query("UPDATE " . $ecs->table('supplier_nav') . " SET ifshow = 0 WHERE ctype = 'c' AND cid = '" . $cat_id . "' AND type = 'middle' AND supplier_id=".getSupplierIdMF());
			}
		}
		
		//更新首页推荐
		insert_cat_recommend($cat['cat_recommend'], $cat_id);
	
		
		/* 更新分类信息成功 */
		clear_cache_files(); // 清除缓存
		//admin_log($_POST['cat_name'], 'edit', 'category'); // 记录管理员操作
		
		/* 提示信息 */
		$link[] = array('text' => "返回", 'href' => 'supplier_m_category.php?act=list');
		sys_msg("修改分类成功", 0, $link);
	}
	
	
}

/**
 * 插入首页推荐扩展分类
 *
 * @access  public
 * @param   array   $recommend_type 推荐类型
 * @param   integer $cat_id     分类ID
 *
 * @return void
 */
function insert_cat_recommend($recommend_type, $cat_id)
{
	//检查分类是否为首页推荐
	if (!empty($recommend_type))
	{
		//取得之前的分类
		$recommend_res = $GLOBALS['db']->getAll("SELECT recommend_type FROM " . $GLOBALS['ecs']->table("supplier_cat_recommend") . " WHERE cat_id=" . $cat_id . " AND supplier_id=".getSupplierIdMF());
		if (empty($recommend_res))
		{
			foreach($recommend_type as $data)
			{
				$data = intval($data);
				$GLOBALS['db']->query("INSERT INTO " . $GLOBALS['ecs']->table("supplier_cat_recommend") . "(cat_id, recommend_type, supplier_id) VALUES ('$cat_id', '$data', ".getSupplierIdMF().")");
			}
		}
		else
		{
			$old_data = array();
			foreach($recommend_res as $data)
			{
				$old_data[] = $data['recommend_type'];
			}
			$delete_array = array_diff($old_data, $recommend_type);
			if (!empty($delete_array))
			{
				$GLOBALS['db']->query("DELETE FROM " . $GLOBALS['ecs']->table("supplier_cat_recommend") . " WHERE supplier_id=".getSupplierIdMF()." AND cat_id=$cat_id AND recommend_type " . db_create_in($delete_array));
			}
			$insert_array = array_diff($recommend_type, $old_data);
			if (!empty($insert_array))
			{
				foreach($insert_array as $data)
				{
					$data = intval($data);
					$GLOBALS['db']->query("INSERT INTO " . $GLOBALS['ecs']->table("supplier_cat_recommend") . "(cat_id, recommend_type, supplier_id) VALUES ('$cat_id', '$data', ".getSupplierIdMF().")");
				}
			}
		}
	}
	else
	{
		$GLOBALS['db']->query("DELETE FROM ". $GLOBALS['ecs']->table("supplier_cat_recommend") . " WHERE cat_id=" . $cat_id. " AND supplier_id=".getSupplierIdMF());
	}
}

//获取快递模块的路径
function getShopRootPath(){
	$pos=strpos(ROOT_PATH,"mobile/");
	$shippingRootPath= substr(ROOT_PATH,0,$pos);
	return $shippingRootPath;
	
}
/**
 * 分类商品代表图片
 * @param int $catid 商品分类id
 */
function upload_category_pic($catid){

	/* 允许上传的文件类型 */
	$allow_file_types = '|GIF|JPG|PNG|BMP|';
	foreach ($_FILES AS $code => $file)
	{
		/* 判断用户是否选择了文件 */
		if ((isset($file['error']) && $file['error'] == 0) || (!isset($file['error']) && $file['tmp_name'] != 'none'))
		{
			/* 检查上传的文件类型是否合法 */
			if (!check_file_type($file['tmp_name'], $file['name'], $allow_file_types))
			{
				sys_msg(sprintf($_LANG['msg_invalid_file'], $file['name']));
			}
			else
			{
				$file_name =getShopRootPath(). "data/supplier/category/";
				if($code == 'cat_pic')
				{
					$ext = array_pop(explode('.', $file['name']));
					
					$file_name .= getSupplierIdMF().'c'.time() . '.' . $ext;
					
					if($catid>0){
						$catpic = get_cat_info($catid);
						if (file_exists($catpic['cat_pic']))
						{
							@unlink($catpic['cat_pic']);
						}
					}
				}
				
				/* 判断是否上传成功 */
				if (move_upload_file($file['tmp_name'], $file_name))
				{
					return $file_name;
				}
				else
				{
					sys_msg(sprintf("上传文件 %s 失败，请检查 %s 目录是否可写。", $file['name'], $file_name));
				}
			}
		}
	}
}
/**
 * 店内分类添加页面
 */
function action_add() {
	$smarty = $GLOBALS ['smarty'];
	
    /* 模板赋值 */
    $smarty->assign('ur_here',      "添加分类");

    $smarty->assign('action_link',  array('href' => 'supplier_m_category.php?act=list', 'text' => "添加分类"));

    $smarty->assign('goods_type_list',  goods_type_list(0)); // 取得商品类型
    $smarty->assign('attr_list',        get_attr_list_private()); // 取得商品属性

    $smarty->assign('cat_select',   cat_list_2(0, 0, true));
    $smarty->assign('form_act',     'insert');
    $smarty->assign('cat_info',     array('is_show' => 1));

    $smarty->display('supplier/category_info.dwt');

	
	
	
}



/**
 * 店内分类列表
 */
function action_list() { 
	$smarty = $GLOBALS ['smarty'];	
	
	/* 获取分类列表 */
	$cat_list = cat_list_2(0, 0, false);
 
	/* 模板赋值 */
	$smarty->assign('ur_here',      $_LANG['04_category_list']);
	$smarty->assign('action_link',  array('href' => 'supplier_m_category.php?act=add', 'text' => "添加分类"));
	$smarty->assign('full_page',    1);
	
	$smarty->assign('cat_info',     $cat_list);
 
	/* 列表页面 */
	$smarty->display('supplier/category_list.dwt');
	
	
	
}

?>