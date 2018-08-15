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
require_once (ROOT_PATH . 'languages/' . $_CFG ['lang'] . '/admin/suppliers_goods.php');
// 不需要登录的操作或自己验证是否登录（如ajax处理）的act
$not_login_arr = array (
		'login',
);


 
/* 显示页面的action列表 */

$ui_arr [] = 'list';
$uif_arr [] = 'add';
$ui_arr [] = 'insert';
$ui_arr [] = 'edit';
$ui_arr [] = 'update';
$ui_arr [] = 'toggle_on_sale';

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




/**
 * 获取店铺的根目录
 * @return string
 */
function getShopRoot(){
	$pos=strpos(ROOT_PATH,"mobile/");
	$shippingRootPath= substr(ROOT_PATH,0,$pos);
	return $shippingRootPath;
	
}
 

function action_ajax_category(){
	$cat_list = cat_list(0, $selected, false);
	
	$json = cat_list_to_json_string($cat_list, $goods['cat_id']);
	
	print $json;
}

/**
 * 将商品分类列表转换成符合zTree标准的JSON字符串格式
 */
function cat_list_to_json_string($cat_list, $selected = 0)
{
	
	include_once(getShopRoot(). 'includes/Pinyin.php');
	
	$tree = array();
	
	foreach ($cat_list as $k => $cat)
	{
		$id = $cat['cat_id'];
		$pId = $cat['parent_id'];
		$name = $cat['cat_name'];
		//$open = true;
		
		$name_pinyin = Pinyin($name, 'utf-8', 1).$name;
		
		$node = array("id"=>$id, "pId"=>$pId, "name"=>$name, "name_pinyin"=>$name_pinyin);
		
		array_push($tree, $node);
		
	}
	
	return json_encode($tree);
	
}


/**
 * 获得指定商品的关联商品
 *
 * @access  public
 * @param   integer     $goods_id
 * @return  array
 */
function get_linked_goods($goods_id)
{
	$sql = 'SELECT g.goods_id, g.goods_name, g.goods_thumb, g.goods_img, g.shop_price AS org_price, ' .
			"IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, ".
			'g.market_price, g.promote_price, g.promote_start_date, g.promote_end_date ' .
			'FROM ' . $GLOBALS['ecs']->table('link_goods') . ' lg ' .
			'LEFT JOIN ' . $GLOBALS['ecs']->table('goods') . ' AS g ON g.goods_id = lg.link_goods_id ' .
			"LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
			"ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
			"WHERE lg.goods_id = '$goods_id' AND g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 ".
			"LIMIT " . $GLOBALS['_CFG']['related_goods_number'];
	$res = $GLOBALS['db']->query($sql);
	
	$arr = array();
	while ($row = $GLOBALS['db']->fetchRow($res))
	{
		$arr[$row['goods_id']]['goods_id']     = $row['goods_id'];
		$arr[$row['goods_id']]['goods_name']   = $row['goods_name'];
		$arr[$row['goods_id']]['short_name']   = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
		sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
		$arr[$row['goods_id']]['goods_thumb']  = get_pc_url().'/'.get_image_path($row['goods_id'], $row['goods_thumb'], true);
		$arr[$row['goods_id']]['goods_img']    = get_pc_url().'/'.get_image_path($row['goods_id'], $row['goods_img']);
		$arr[$row['goods_id']]['market_price'] = price_format($row['market_price']);
		$arr[$row['goods_id']]['shop_price']   = price_format($row['shop_price']);
		$arr[$row['goods_id']]['url']          = build_uri('goods', array('gid'=>$row['goods_id']), $row['goods_name']);
		
		if ($row['promote_price'] > 0)
		{
			$arr[$row['goods_id']]['promote_price'] = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
			$arr[$row['goods_id']]['formated_promote_price'] = price_format($arr[$row['goods_id']]['promote_price']);
		}
		else
		{
			$arr[$row['goods_id']]['promote_price'] = 0;
		}
	}
	
	return $arr;
}





function action_get_attr(){
	
	
	$goods_id   = empty($_GET['goods_id']) ? 0 : intval($_GET['goods_id']);
	$goods_type = empty($_GET['goods_type']) ? 0 : intval($_GET['goods_type']);
	$content    = build_attr_html($goods_type, $goods_id,$bar_code);
	// $content    = build_attr_html($goods_type, $goods_id);
	make_json_result($content);
	
}
/**
 * 到编辑页面
 */
function action_edit() {
	require_once (ROOT_PATH. 'languages/' . $GLOBALS['_CFG'] ['lang'] . '/admin/common.php');
	$smarty = $GLOBALS ['smarty'];
	$ecs= $GLOBALS['ecs'];
	$db= $GLOBALS['db'];
	//include_once(ROOT_PATH . 'includes/fckeditor/fckeditor.php'); // 包含 html editor 类文件
	include_once(getShopRoot() . 'includes/Pinyin.php');
	
	$is_add = $_REQUEST['act'] == 'add'; // 添加还是编辑的标识
	$code = empty($_REQUEST['extension_code']) ? '' : trim($_REQUEST['extension_code']);
	$code=$code=='virual_card' ? 'virual_card': '';
	if (isset($GLOBALS['_CFG']['supplier_addbest']))
	{
		$smarty->assign('is_addbest', $GLOBALS['_CFG']['supplier_addbest']);
	}
	if (isset($GLOBALS['_CFG']['supplier_editgoods']))
	{
		$smarty->assign('is_editgoods', $GLOBALS['_CFG']['supplier_editgoods']);
	}
	if (isset($GLOBALS['_CFG']['supplier_secondadd']))
	{
		$smarty->assign('is_secondadd', $GLOBALS['_CFG']['supplier_secondadd']);
	}
	

	/* 供货商名 */
	$suppliers_list_name = suppliers_list_name();
	$suppliers_exists = 1;
	if (empty($suppliers_list_name))
	{
		$suppliers_exists = 0;
	}
	$smarty->assign('suppliers_exists', $suppliers_exists);
	$smarty->assign('suppliers_list_name', $suppliers_list_name);
	unset($suppliers_list_name, $suppliers_exists);
	
	/* 如果是安全模式，检查目录是否存在 */
	if (ini_get('safe_mode') == 1 && (!file_exists('../' . IMAGE_DIR . '/'.date('Ym')) || !is_dir('../' . IMAGE_DIR . '/'.date('Ym'))))
	{
		if (@!mkdir('../' . IMAGE_DIR . '/'.date('Ym'), 0777))
		{
			$warning = sprintf($_LANG['safe_mode_warning'], '../' . IMAGE_DIR . '/'.date('Ym'));
			$smarty->assign('warning', $warning);
		}
	}
	
	/* 如果目录存在但不可写，提示用户 */
	elseif (file_exists('../' . IMAGE_DIR . '/'.date('Ym')) && file_mode_info('../' . IMAGE_DIR . '/'.date('Ym')) < 2)
	{
		$warning = sprintf($_LANG['not_writable_warning'], '../' . IMAGE_DIR . '/'.date('Ym'));
		$smarty->assign('warning', $warning);
	}
	
	
	
	
	
		/*
		 *查询输出条形码
		 */
		$sql = "SELECT * FROM". $ecs->table('bar_code') ."WHERE goods_id ='$_REQUEST[goods_id]'";
		$bar_code =$db->getAll($sql);
	
        /* 商品信息 */
        $sql = "SELECT * FROM " . $ecs->table('goods') . " WHERE goods_id = '$_REQUEST[goods_id]'";
        $goods = $db->getRow($sql);

		
		$r_b_id = $db->getOne("select brand_name from ".$ecs->table('brand')." where brand_id=".$goods['brand_id']);
		$goods['brand_name'] = $r_b_id;
		$smarty->assign('brand_name_val',$goods['brand_name']);

        /* 虚拟卡商品复制时, 将其库存置为0*/
        if ($is_copy && $code != '')
        {
            $goods['goods_number'] = 0;
        }

        if (empty($goods) === true)
        {
            /* 默认值 */
            $goods = array(
                'goods_id'      => 0,
                'goods_desc'    => '',
                'cat_id'        => 0,
                'is_on_sale'    => '1',
                'is_alone_sale' => '1',
                'is_shipping' => '0',
                'other_cat'     => array(), // 扩展分类
                'goods_type'    => 0,       // 商品类型
                'shop_price'    => 0,
                'promote_price' => 0,
                'market_price'  => 0,
                'integral'      => 0,
                'goods_number'  => 1,
                'warn_number'   => 1,
                'promote_start_date' => local_date('Y-m-d'),
                'promote_end_date'   => local_date('Y-m-d', gmstr2tome('+1 month')),
                'goods_weight'  => 0,
                'give_integral' => 0,
				'exclusive' => -1,//手机专享价格   app  jx  
                'rank_integral' => -1
            );
        }
      
        /* 获取商品类型存在规格的类型 */
        $specifications = get_goods_type_specifications(); 
        $goods['specifications_id'] = $specifications[$goods['goods_type']];
        $_attribute = get_goods_specifications_list($goods['goods_id']); 
        $goods['_attribute'] = empty($_attribute) ? '' : 1;
       
        /* 根据商品重量的单位重新计算 */
        if ($goods['goods_weight'] > 0)
        {
            $goods['goods_weight_by_unit'] = ($goods['goods_weight'] >= 1) ? $goods['goods_weight'] : ($goods['goods_weight'] / 0.001);
        }

        if (!empty($goods['goods_brief']))
        {
            //$goods['goods_brief'] = trim_right($goods['goods_brief']);
            $goods['goods_brief'] = $goods['goods_brief'];
        }
        if (!empty($goods['keywords']))
        {
            //$goods['keywords']    = trim_right($goods['keywords']);
            $goods['keywords']    = $goods['keywords'];
        }

        /* 如果不是促销，处理促销日期 */
        if (isset($goods['is_promote']) && $goods['is_promote'] == '0')
        {
            unset($goods['promote_start_date']);
            unset($goods['promote_end_date']);
        }
        else
        {
            $goods['promote_start_date'] = local_date('Y-m-d', $goods['promote_start_date']);
            $goods['promote_end_date'] = local_date('Y-m-d', $goods['promote_end_date']);
        }

		$goods['buymax_start_date'] = local_date('Y-m-d', $goods['buymax_start_date']);
		$goods['buymax_end_date'] = local_date('Y-m-d', $goods['buymax_end_date']);
	
		

        // 扩展分类
        $other_cat_list = array();
        $sql = "SELECT cat_id FROM " . $ecs->table('goods_cat') . " WHERE goods_id = '$_REQUEST[goods_id]'";
        $goods['other_cat'] = $db->getCol($sql);
        foreach ($goods['other_cat'] AS $cat_id)
        {
            $other_cat_list[$cat_id] = cat_list(0, $cat_id);
        }
        $smarty->assign('other_cat_list', $other_cat_list);

        $link_goods_list    = get_linked_goods($goods['goods_id']); // 关联商品
        $group_goods_list   = get_group_goods($goods['goods_id']); // 配件
        $goods_article_list = get_goods_articles($goods['goods_id']);   // 关联文章

        /* 商品图片路径 */
        if (isset($GLOBALS['shop_id']) && ($GLOBALS['shop_id'] > 10) && !empty($goods['original_img']))
        {
            $goods['goods_img'] = get_image_path($_REQUEST['goods_id'], $goods['goods_img']);
            $goods['goods_thumb'] = get_image_path($_REQUEST['goods_id'], $goods['goods_thumb'], true);
			$goods['original_img'] = get_image_path($_REQUEST['goods_id'], $goods['original_img']);
        }
       
        /* 图片列表 */
        $sql = "SELECT * FROM " . $ecs->table('goods_gallery') . " WHERE goods_id = '$goods[goods_id]'";
        $img_list = $db->getAll($sql);

        /* 格式化相册图片路径 */
        if (isset($GLOBALS['shop_id']) && ($GLOBALS['shop_id'] > 0))
        {
            foreach ($img_list as $key => $gallery_img)
            {
                $gallery_img[$key]['img_url'] = get_image_path($gallery_img['goods_id'], $gallery_img['img_original'], false, 'gallery');
                $gallery_img[$key]['thumb_url'] = get_image_path($gallery_img['goods_id'], $gallery_img['img_original'], true, 'gallery');
            }
        }
        else
        {
            foreach ($img_list as $key => $gallery_img)
            {
                $gallery_img[$key]['thumb_url'] = '../' . (empty($gallery_img['thumb_url']) ? $gallery_img['img_url'] : $gallery_img['thumb_url']);
            }
        }

		$cat_list = cat_list(0, $selected, false);
		$smarty->assign('goods_cat_name', $cat_list[$goods['cat_id']]['cat_name']);
 
		

	
	
		
		$smarty->assign('is_add_zhyh', 0);
		$sql_cats_zhyh="select * from ". $ecs->table('supplier_goods_cat') ." where goods_id='$goods[goods_id]' ";
		$res_old_zhyh = $db->query($sql_cats_zhyh);
		while ($row_old_zhyh = $db->fetchRow($res_old_zhyh))
		{
			$cats_old_zhyh[]=$row_old_zhyh['cat_id'];
		}
		
		
	
	
	$cate=array();
	$sqlc = "select cat_id, parent_id, cat_name from ". $ecs->table('supplier_category') ." where supplier_id='". $_SESSION['supplier_id'] ."' ";
	$resc = $db->query($sqlc);
	while ($rowc = $db->fetchRow($resc))
	{
		$cate[$rowc['cat_id']] =array(
				'id' => $rowc['cat_id'],
				'pid' => $rowc['parent_id'],
				'name' => $rowc['cat_name']
		);
	}
	get_tree(0,$cate,0, $cats_old_zhyh);
	global $catstr;
	$smarty->assign('catstr',$catstr);
	
	/* 拆分商品名称样式 */
	$goods_name_style = explode('+', empty($goods['goods_name_style']) ? '+' : $goods['goods_name_style']);
	
	/* 创建 html editor */
	create_html_editor('goods_desc', htmlspecialchars($goods['goods_desc'])); /* 修改 by www.68ecshop.com 百度编辑器 */
	
	/* 模板赋值 */
	$action_link_supplier = array('href' => 'supplier_m_goods.php?act=list&supplier_status='.$_REQUEST['supplier_status'] , 'text' => '返回商品列表');
	$smarty->assign('code',    $code);
	$smarty->assign('ur_here', $is_add ? (empty($code) ? $_LANG['03_goods_add'] : $_LANG['51_virtual_card_add']) : ($_REQUEST['act'] == 'edit' ? $_LANG['edit_goods'] : $_LANG['copy_goods']));
	$smarty->assign('action_link', $action_link_supplier);
	$smarty->assign('goods', $goods);
	$smarty->assign('goods_name_color', $goods_name_style[0]);
	$smarty->assign('goods_name_style', $goods_name_style[1]);
	$smarty->assign('area_cat_list', area_cat_list(0, $goods['area_cat_id'],true,0,true,2));//UEUCS_364642382
	$smarty->assign('cat_list', cat_list(0, $goods['cat_id']));
	// 代码修改_start_derek20150129admin_goods  www.68ecshop.com
	$smarty->assign('goods_cat_id', $goods['cat_id']);
	
	$smarty->assign('brand_list_for_select', get_brand_list());
	$brand_list = get_brand_list(true);
	$smarty->assign('brand_list', $brand_list);
	
	// 代码修改_start_derek20150129admin_goods  www.68ecshop.com
	$smarty->assign('unit_list', get_unit_list());
	$smarty->assign('user_rank_list', get_user_rank_list());
	$smarty->assign('weight_unit', $is_add ? '1' : ($goods['goods_weight'] >= 1 ? '1' : '0.001'));
	$smarty->assign('cfg', $_CFG);
	$smarty->assign('form_act', $is_add ? 'insert' : ($_REQUEST['act'] == 'edit' ? 'update' : 'insert'));
	$smarty->assign('is_add', true);
	
	$smarty->assign('link_goods_list', $link_goods_list);
	$smarty->assign('group_goods_list', $group_goods_list);
	$smarty->assign('goods_article_list', $goods_article_list);
	$smarty->assign('img_list', $img_list);
	$smarty->assign('goods_type_list', goods_type_list($goods['goods_type']));
	$smarty->assign('gd', gd_version());
	$smarty->assign('thumb_width', $_CFG['thumb_width']);
	$smarty->assign('thumb_height', $_CFG['thumb_height']);
	$smarty->assign('goods_attr_html', build_attr_html($goods['goods_type'], $goods['goods_id'],$bar_code));
	$volume_price_list = '';
	if(isset($_REQUEST['goods_id']))
	{
		$volume_price_list = get_volume_price_list($_REQUEST['goods_id']);
	}
	if (empty($volume_price_list))
	{
		$volume_price_list = array('0'=>array('number'=>'','price'=>''));
	}
	$smarty->assign('volume_price_list', $volume_price_list);
	if (isset($_CFG['supplier_addbest']))
	{
		$smarty->assign('is_addbest', $_CFG['supplier_addbest']);
	}
	 
	$smarty->display('supplier/goods_info.dwt');
	
}

/**
 * 保存商品
 */
function action_insert() {
	
	require_once(ROOT_PATH. '/includes/lib_goods.php');
	include_once(ROOT_PATH . '/includes/cls_image.php');
	$image = new cls_image($_CFG['bgcolor']);
    require_once (getShopRootPath(). 'languages/' . $GLOBALS['_CFG'] ['lang'] . '/admin/suppliers_goods.php');
	$smarty = $GLOBALS ['smarty'];
	$ecs= $GLOBALS['ecs'];
	$db= $GLOBALS['db'];
	$code = empty($_REQUEST['extension_code']) ? '' : trim($_REQUEST['extension_code']);
	
	/* 是否处理缩略图 */
	$proc_thumb = (isset($GLOBALS['shop_id']) && $GLOBALS['shop_id'] > 0)? false : true;
 
	/* 检查货号是否重复 */
	if ($_POST['goods_sn'])
	{
		$sql = "SELECT COUNT(*) FROM " . $ecs->table('goods') .
		" WHERE goods_sn = '$_POST[goods_sn]' AND is_delete = 0 AND goods_id <> '$_POST[goods_id]'";
		if ($db->getOne($sql) > 0)
		{
			sys_msg($_LANG['goods_sn_exists'], 1, array(), true);
		}
	}
	
	/* 检查图片：如果有错误，检查尺寸是否超过最大值；否则，检查文件类型 */
	if (isset($_FILES['goods_img']['error'])) // php 4.2 版本才支持 error
	{	
		// 最大上传文件大小
		$php_maxsize = ini_get('upload_max_filesize');
		$htm_maxsize = '2M';
		
		// 商品图片
		if ($_FILES['goods_img']['error'] == 0)
		{
			if (!$image->check_img_type($_FILES['goods_img']['type']))
			{
				sys_msg($_LANG['invalid_goods_img'], 1, array(), false);
			}
		}
		elseif ($_FILES['goods_img']['error'] == 1)
		{
			sys_msg(sprintf($_LANG['goods_img_too_big'], $php_maxsize), 1, array(), false);
		}
		elseif ($_FILES['goods_img']['error'] == 2)
		{
			sys_msg(sprintf($_LANG['goods_img_too_big'], $htm_maxsize), 1, array(), false);
		}
	
		// 商品缩略图
		if (isset($_FILES['goods_thumb']))
		{
			if ($_FILES['goods_thumb']['error'] == 0)
			{
				if (!$image->check_img_type($_FILES['goods_thumb']['type']))
				{
					sys_msg($_LANG['invalid_goods_thumb'], 1, array(), false);
				}
			}
			elseif ($_FILES['goods_thumb']['error'] == 1)
			{
				sys_msg(sprintf($_LANG['goods_thumb_too_big'], $php_maxsize), 1, array(), false);
			}
			elseif ($_FILES['goods_thumb']['error'] == 2)
			{
				sys_msg(sprintf($_LANG['goods_thumb_too_big'], $htm_maxsize), 1, array(), false);
			}
		}
		
		// 相册图片
		 
		if($_FILES['img_url']['error'])
		{
			/* 代码增加_end   By www.ecshop68.com */
			foreach ($_FILES['img_url']['error'] AS $key => $value)
			{
				if ($value == 0)
				{
					if (!$image->check_img_type($_FILES['img_url']['type'][$key]))
					{
						sys_msg(sprintf($_LANG['invalid_img_url'], $key + 1), 1, array(), false);
					}
				}
				elseif ($value == 1)
				{
					sys_msg(sprintf($_LANG['img_url_too_big'], $key + 1, $php_maxsize), 1, array(), false);
				}
				elseif ($_FILES['img_url']['error'] == 2)
				{
					sys_msg(sprintf($_LANG['img_url_too_big'], $key + 1, $htm_maxsize), 1, array(), false);
				}
			}
		}
	}
	/* 4.1版本 */
	else
	{ 
		// 商品图片
		if ($_FILES['goods_img']['tmp_name'] != 'none')
		{
			if (!$image->check_img_type($_FILES['goods_img']['type']))
			{
				
				sys_msg($_LANG['invalid_goods_img'], 1, array(), false);
			}
		}
		
		// 商品缩略图
		if (isset($_FILES['goods_thumb']))
		{
			if ($_FILES['goods_thumb']['tmp_name'] != 'none')
			{
				if (!$image->check_img_type($_FILES['goods_thumb']['type']))
				{
					sys_msg($_LANG['invalid_goods_thumb'], 1, array(), false);
				}
			}
		}
		
		// 相册图片
		foreach ($_FILES['img_url']['tmp_name'] AS $key => $value)
		{
			if ($value != 'none')
			{
				if (!$image->check_img_type($_FILES['img_url']['type'][$key]))
				{
					sys_msg(sprintf($_LANG['invalid_img_url'], $key + 1), 1, array(), false);
				}
			}
		}
	}
	
	/* 插入还是更新的标识 */
	$is_insert = $_REQUEST['act'] == 'insert';
	
	/* 处理商品图片 */
	$goods_img        = '';  // 初始化商品图片
	$goods_thumb      = '';  // 初始化商品缩略图
	$original_img     = '';  // 初始化原始图片
	$old_original_img = '';  // 初始化原始图片旧图
	
	// 如果上传了商品图片，相应处理
	if (($_FILES['goods_img']['tmp_name'] != '' && $_FILES['goods_img']['tmp_name'] != 'none') or (($_POST['goods_img_url'] != $_LANG['lab_picture_url'] && $_POST['goods_img_url'] != 'http://') && $is_url_goods_img = 1))
	{
		if ($_REQUEST['goods_id'] > 0)
		{
			/* 删除原来的图片文件 */
			$sql = "SELECT goods_thumb, goods_img, original_img " .
					" FROM " . $ecs->table('goods') .
					" WHERE goods_id = '$_REQUEST[goods_id]'";
			$row = $db->getRow($sql);
			if ($row['goods_thumb'] != '' && is_file('../' . $row['goods_thumb']))
			{
				@unlink('../' . $row['goods_thumb']);
			}
			if ($row['goods_img'] != '' && is_file('../' . $row['goods_img']))
			{
				@unlink('../' . $row['goods_img']);
			}
			if ($row['original_img'] != '' && is_file('../' . $row['original_img']))
			{
				/* 先不处理，以防止程序中途出错停止 */
				//$old_original_img = $row['original_img']; //记录旧图路径
			}
			/* 清除原来商品图片 */
			if ($proc_thumb === false)
			{
				get_image_path($_REQUEST[goods_id], $row['goods_img'], false, 'goods', true);
				get_image_path($_REQUEST[goods_id], $row['goods_thumb'], true, 'goods', true);
			}
		}
		
		if (empty($is_url_goods_img))
		{
			$original_img   = $image->upload_image($_FILES['goods_img']); // 原始图片
		}
		elseif ($_POST['goods_img_url'])
		{
			
			if(preg_match('/(.jpg|.png|.gif|.jpeg)$/',$_POST['goods_img_url']) && copy(trim($_POST['goods_img_url']), ROOT_PATH . 'temp/' . basename($_POST['goods_img_url'])))
			{
				$original_img = 'temp/' . basename($_POST['goods_img_url']);
			}
			
		}
		
		if ($original_img === false)
		{
			sys_msg($image->error_msg(), 1, array(), false);
		}
		$goods_img      = $original_img;   // 商品图片
		
		/* 复制一份相册图片 */
		/* 添加判断是否自动生成相册图片 */
		if ($_CFG['auto_generate_gallery'])
		{
			$img        = $original_img;   // 相册图片
			$pos        = strpos(basename($img), '.');
			$newname    = dirname($img) . '/' . $image->random_filename() . substr(basename($img), $pos);
			if (!copy('../' . $img, '../' . $newname))
			{
				sys_msg('fail to copy file: ' . realpath('../' . $img), 1, array(), false);
			}
			$img        = $newname;
			
			$gallery_img    = $img;
			$gallery_thumb  = $img;
		}
		
		// 如果系统支持GD，缩放商品图片，且给商品图片和相册图片加水印
		if ($proc_thumb && $image->gd_version() > 0 && $image->check_img_function($_FILES['goods_img']['type']) || $is_url_goods_img)
		{
			
			if (empty($is_url_goods_img))
			{
				// 如果设置大小不为0，缩放图片
				if ($_CFG['image_width'] != 0 || $_CFG['image_height'] != 0)
				{
					$goods_img = $image->make_thumb('../'. $goods_img , $GLOBALS['_CFG']['image_width'],  $GLOBALS['_CFG']['image_height']);
					if ($goods_img === false)
					{
						sys_msg($image->error_msg(), 1, array(), false);
					}
				}
				
				/* 添加判断是否自动生成相册图片 */
				if ($_CFG['auto_generate_gallery'])
				{
					$newname    = dirname($img) . '/' . $image->random_filename() . substr(basename($img), $pos);
					if (!copy('../' . $img, '../' . $newname))
					{
						sys_msg('fail to copy file: ' . realpath('../' . $img), 1, array(), false);
					}
					$gallery_img        = $newname;
				}
				
				// 加水印
				if (intval($_CFG['watermark_place']) > 0 && !empty($GLOBALS['_CFG']['watermark']))
				{
					if ($image->add_watermark('../'.$goods_img,'',$GLOBALS['_CFG']['watermark'], $GLOBALS['_CFG']['watermark_place'], $GLOBALS['_CFG']['watermark_alpha']) === false)
					{
						sys_msg($image->error_msg(), 1, array(), false);
					}
					/* 添加判断是否自动生成相册图片 */
					if ($_CFG['auto_generate_gallery'])
					{
						if ($image->add_watermark('../'. $gallery_img,'',$GLOBALS['_CFG']['watermark'], $GLOBALS['_CFG']['watermark_place'], $GLOBALS['_CFG']['watermark_alpha']) === false)
						{
							sys_msg($image->error_msg(), 1, array(), false);
						}
					}
				}
			}
			
			// 相册缩略图
			/* 添加判断是否自动生成相册图片 */
			if ($_CFG['auto_generate_gallery'])
			{
				if ($_CFG['thumb_width'] != 0 || $_CFG['thumb_height'] != 0)
				{
					$gallery_thumb = $image->make_thumb('../' . $img, $GLOBALS['_CFG']['thumb_width'],  $GLOBALS['_CFG']['thumb_height']);
					if ($gallery_thumb === false)
					{
						sys_msg($image->error_msg(), 1, array(), false);
					}
				}
			}
		}
		/* 取消该原图复制流程 */
		// else
		// {
		//     /* 复制一份原图 */
		//     $pos        = strpos(basename($img), '.');
		//     $gallery_img = dirname($img) . '/' . $image->random_filename() . // substr(basename($img), $pos);
		//     if (!copy('../' . $img, '../' . $gallery_img))
		//     {
		//         sys_msg('fail to copy file: ' . realpath('../' . $img), 1, array(), false);
		//     }
		//     $gallery_thumb = '';
		// }
	}
	
	
	// 是否上传商品缩略图
	if (isset($_FILES['goods_thumb']) && $_FILES['goods_thumb']['tmp_name'] != '' &&
			isset($_FILES['goods_thumb']['tmp_name']) &&$_FILES['goods_thumb']['tmp_name'] != 'none')
	{
		// 上传了，直接使用，原始大小
		$goods_thumb = $image->upload_image($_FILES['goods_thumb']);
		if ($goods_thumb === false)
		{
			sys_msg($image->error_msg(), 1, array(), false);
		}
	}
	else
	{
		// 未上传，如果自动选择生成，且上传了商品图片，生成所略图
		if ($proc_thumb && isset($_POST['auto_thumb']) && !empty($original_img))
		{
			// 如果设置缩略图大小不为0，生成缩略图
			if ($_CFG['thumb_width'] != 0 || $_CFG['thumb_height'] != 0)
			{
				$goods_thumb = $image->make_thumb('../' . $original_img, $GLOBALS['_CFG']['thumb_width'],  $GLOBALS['_CFG']['thumb_height']);
				if ($goods_thumb === false)
				{
					sys_msg($image->error_msg(), 1, array(), false);
				}
			}
			else
			{
				$goods_thumb = $original_img;
			}
		}
	}
	
	
	/* 删除下载的外链原图 */
	if (!empty($is_url_goods_img))
	{
		unlink(ROOT_PATH . $original_img);
		empty($newname) || unlink(ROOT_PATH . $newname);
		$url_goods_img = $goods_img = $original_img = htmlspecialchars(trim($_POST['goods_img_url']));
	}
	
 
	/* 如果没有输入商品货号则自动生成一个商品货号 */
	if (empty($_POST['goods_sn']))
	{
		$max_id     = $db->getOne("SELECT MAX(goods_id) + 1 FROM ".$ecs->table('goods')) ;
		$goods_sn   = generate_goods_sn($max_id);
	}
	else
	{
		$goods_sn   = $_POST['goods_sn'];
	}
	
	/* 处理商品数据 */
	$cost_price = !empty($_POST['cost_price']) ? $_POST['cost_price'] : 0;
	$shop_price = !empty($_POST['shop_price']) ? $_POST['shop_price'] : 0;
	$market_price = !empty($_POST['market_price']) ? $_POST['market_price'] : 0;
	$promote_price = !empty($_POST['promote_price']) ? floatval($_POST['promote_price'] ) : 0;
	$is_promote = empty($promote_price) ? 0 : 1;
	$zhekou = ($promote_price == 0 ? 10.0 : (number_format(($promote_price/$shop_price),2))*10);
	$promote_start_date = ($is_promote && !empty($_POST['promote_start_date'])) ? local_strtotime($_POST['promote_start_date']) : 0;
	$promote_end_date = ($is_promote && !empty($_POST['promote_end_date'])) ? local_strtotime($_POST['promote_end_date']) : 0;
	$goods_weight = !empty($_POST['goods_weight']) ? $_POST['goods_weight'] * $_POST['weight_unit'] : 0;
	$is_best = isset($_POST['is_best']) ? 1 : 0;
	$is_new = isset($_POST['is_new']) ? 1 : 0;
	$is_hot = isset($_POST['is_hot']) ? 1 : 0;
	$is_on_sale = isset($_POST['is_on_sale']) ? 1 : 0;
	$is_alone_sale = isset($_POST['is_alone_sale']) ? 1 : 0;
	$is_shipping = isset($_POST['is_shipping']) ? 1 : 0;
	$goods_number = isset($_POST['goods_number']) ? $_POST['goods_number'] : 0;
	$warn_number = isset($_POST['warn_number']) ? $_POST['warn_number'] : 0;
	$goods_type = isset($_POST['goods_type']) ? $_POST['goods_type'] : 0;
	$give_integral = isset($_POST['give_integral']) ? intval($_POST['give_integral']) : '0';
	$rank_integral = isset($_POST['rank_integral']) ? intval($_POST['rank_integral']) : '-1';
	$suppliers_id = isset($_POST['suppliers_id']) ? intval($_POST['suppliers_id']) : '0';
	$supplier_id = isset($_SESSION['supplier_id']) ? intval($_SESSION['supplier_id']) : $_COOKIE['ECSCP']['supplier_id'];
	
	//手机专享价格 app   jx
	$exclusive = !empty($_POST['exclusive']) ? $_POST['exclusive'] : -1;
	//手机专享价格 app  jx
	
	$goods_name_style = $_POST['goods_name_color'] . '+' . $_POST['goods_name_style'];
	
	$catgory_id = empty($_POST['cat_id']) ? '' : intval($_POST['cat_id']);
	
	//$catgory_id = $_REQUEST['cat_id_'.$_REQUEST['cat_level_id']];
	$brand_id = empty($_POST['brand_id']) ? '' : intval($_POST['brand_id']);
	$reason  = $_POST['reason'];
	$is_pass =  $_POST['is_pass'];
	$area_catgory_id = empty($_POST['area_cat_id']) ? 1 : intval($_POST['area_cat_id']);//UEUCS_364642382
	
	$goods_thumb = (empty($goods_thumb) && !empty($_POST['goods_thumb_url']) && goods_parse_url($_POST['goods_thumb_url'])) ? htmlspecialchars(trim($_POST['goods_thumb_url'])) : $goods_thumb;
	$goods_thumb = (empty($goods_thumb) && isset($_POST['auto_thumb']))? $goods_img : $goods_thumb;
	
	$buymax = !empty($_POST['buymax']) ? floatval($_POST['buymax'] ) : 0;
	$is_buy = empty($buymax) ? 0 : 1;
	$buymax_start_date = ($is_buy && !empty($_POST['buymax_start_date'])) ? local_strtotime($_POST['buymax_start_date']) : 0;
	$buymax_end_date = ($is_buy && !empty($_POST['buymax_end_date'])) ? local_strtotime($_POST['buymax_end_date']) : 0;
	/* 入库 */
	if ($is_insert)
	{
		if ($code == '')
		{
			$sql = "INSERT INTO " . $ecs->table('goods') . " (is_pass,reason,area_cat_id,goods_name, goods_name_style, goods_sn, " .
					"promote_start_date, promote_end_date, is_buy,buymax,buymax_start_date,buymax_end_date,goods_img, goods_thumb, original_img, keywords, goods_brief, " .
					"cat_id, cost_price, brand_id, shop_price, market_price, is_promote, zhekou, promote_price, " .
					"seller_note, goods_weight, goods_number, warn_number, integral, give_integral, is_best, is_new, is_hot, " .
					"is_on_sale, is_alone_sale, is_shipping, goods_desc, add_time, last_update, goods_type, rank_integral,exclusive, supplier_id,supplier_status)" .
					"VALUES ('$is_pass','$reason','$area_catgory_id','$_POST[goods_name]', '$goods_name_style', '$goods_sn', '$catgory_id', '$cost_price', " .
					"'$brand_id', '$shop_price', '$market_price', '$is_promote', '$zhekou', '$promote_price', ".
					"'$promote_start_date', '$promote_end_date', '$is_buy','$buymax','$buymax_start_date','$buymax_end_date','$goods_img', '$goods_thumb', '$original_img', ".
					"'$_POST[keywords]', '$_POST[goods_brief]', '$_POST[seller_note]', '$goods_weight', '$goods_number',".
					" '$warn_number', '$_POST[integral]', '$give_integral', '$is_best', '$is_new', '$is_hot', '0', '$is_alone_sale', $is_shipping, ".
					" '$_POST[goods_desc]', '" . gmtime() . "', '". gmtime() ."', '$goods_type', '$rank_integral','$exclusive', '$supplier_id', '0')";
		}
		else
		{
			$sql = "INSERT INTO " . $ecs->table('goods') . " (is_pass,reason,area_cat_id,goods_name, goods_name_style, goods_sn, " .
					"cat_id, cost_price, brand_id, shop_price, market_price, is_promote, zhekou, promote_price, " .
					"promote_start_date, promote_end_date, is_buy,buymax,buymax_start_date,buymax_end_date,goods_img, goods_thumb, original_img, keywords, goods_brief, " .
					"seller_note, goods_weight, goods_number, warn_number, integral, give_integral, is_best, is_new, is_hot, is_real, " .
					"is_on_sale, is_alone_sale, is_shipping, goods_desc, add_time, last_update, goods_type, extension_code,exclusive, rank_integral, supplier_status)" .
					"VALUES ('$is_pass','$reason','$area_catgory_id','$_POST[goods_name]', '$goods_name_style', '$goods_sn', '$catgory_id', '$cost_price', " .
					"'$brand_id', '$shop_price', '$market_price', '$is_promote', '$zhekou', '$promote_price', ".
					"'$promote_start_date', '$promote_end_date', '$is_buy','$buymax','$buymax_start_date','$buymax_end_date','$goods_img', '$goods_thumb', '$original_img', ".
					"'$_POST[keywords]', '$_POST[goods_brief]', '$_POST[seller_note]', '$goods_weight', '$goods_number',".
					" '$warn_number', '$_POST[integral]', '$give_integral', '$is_best', '$is_new', '$is_hot', 0, '$is_on_sale', '$is_alone_sale', $is_shipping, ".
					" '$_POST[goods_desc]', '" . gmtime() . "', '". gmtime() ."', '$goods_type', '$code','$exclusive', '$rank_integral','0')";
		}
	
	}
	 
	
	$db->query($sql);
	
	/* 商品编号 */
	$goods_id =  $db->insert_id();
	
	 
	
	/*存入条形码*/
	if($_POST['txm_shu'] && $_POST['tiaoxingm']){//如果txm_shu 和 tiaoxingm存在 就存入  不存在就不执行
		if(isset($_POST['txm_shu']) && isset($_POST['tiaoxingm']) || (empty($_POST['txm_shu'])) && (empty($_POST['tiaoxingm'])) ){
			$type = $_POST['txm_shu'];
			$bar_code = $_POST['tiaoxingm'];
			$db->query("DELETE FROM" .$ecs->table('bar_code')."WHERE goods_id ='$goods_id'");//根据商品ID清空数据
			foreach($type as $key=>$value){
				foreach($bar_code as $k=>$v){
					$arr['bar_code'] = $v;
					$arr['taypes'] = $value;
					$arr['goods_id'] = $goods_id;
					if($key == $k){
						$sql = "INSERT INTO " . $ecs->table('bar_code') . " (goods_id, taypes, bar_code) " .
								"VALUES ('$arr[goods_id]', '$arr[taypes]','$arr[bar_code]')";//插入数据
						$name = $db->query($sql);
					}
				}
			}
		}
		
	}
 
	
	/* 处理属性 */
	if ((isset($_POST['attr_id_list']) && isset($_POST['attr_value_list'])) || (empty($_POST['attr_id_list']) && empty($_POST['attr_value_list'])))
	{
		// 取得原有的属性值
		$goods_attr_list = array();
		
		$keywords_arr = explode(" ", $_POST['keywords']);
		
		$keywords_arr = array_flip($keywords_arr);
		if (isset($keywords_arr['']))
		{
			unset($keywords_arr['']);
		}
		
		$sql = "SELECT attr_id, attr_index FROM " . $ecs->table('attribute') . " WHERE cat_id = '$goods_type'";
		
		$attr_res = $db->query($sql);
		
		$attr_list = array();
		
		while ($row = $db->fetchRow($attr_res))
		{
			$attr_list[$row['attr_id']] = $row['attr_index'];
		}
		
		$sql = "SELECT g.*, a.attr_type
                FROM " . $ecs->table('goods_attr') . " AS g
                    LEFT JOIN " . $ecs->table('attribute') . " AS a
                    ON a.attr_id = g.attr_id
                    WHERE g.goods_id = '$goods_id'";
		
		$res = $db->query($sql);
		
		while ($row = $db->fetchRow($res))
		{
			$goods_attr_list[$row['attr_id']][$row['attr_value']] = array('sign' => 'delete', 'goods_attr_id' => $row['goods_attr_id']);
		}
		// 循环现有的，根据原有的做相应处理
		if(isset($_POST['attr_id_list']))
		{
			foreach ($_POST['attr_id_list'] AS $key => $attr_id)
			{
				$attr_value = $_POST['attr_value_list'][$key];
				$attr_price = $_POST['attr_price_list'][$key];
				$attr_price = ($attr_price>=0) ? $attr_price : 0;
				if (!empty($attr_value))
				{
					if (isset($goods_attr_list[$attr_id][$attr_value]))
					{
						// 如果原来有，标记为更新
						$goods_attr_list[$attr_id][$attr_value]['sign'] = 'update';
						$goods_attr_list[$attr_id][$attr_value]['attr_price'] = $attr_price;
					}
					else
					{
						// 如果原来没有，标记为新增
						$goods_attr_list[$attr_id][$attr_value]['sign'] = 'insert';
						$goods_attr_list[$attr_id][$attr_value]['attr_price'] = $attr_price;
					}
					$val_arr = explode(' ', $attr_value);
					foreach ($val_arr AS $k => $v)
					{
						if (!isset($keywords_arr[$v]) && $attr_list[$attr_id] == "1")
						{
							$keywords_arr[$v] = $v;
						}
					}
				}
			}
		}
		$keywords = join(' ', array_flip($keywords_arr));
		
		$sql = "UPDATE " .$ecs->table('goods'). " SET keywords = '$keywords' WHERE goods_id = '$goods_id' LIMIT 1";
		
		$db->query($sql);
		
		/* 插入、更新、删除数据 */
		foreach ($goods_attr_list as $attr_id => $attr_value_list)
		{
			foreach ($attr_value_list as $attr_value => $info)
			{
				if ($info['sign'] == 'insert')
				{
					$sql = "INSERT INTO " .$ecs->table('goods_attr'). " (attr_id, goods_id, attr_value, attr_price)".
							"VALUES ('$attr_id', '$goods_id', '$attr_value', '$info[attr_price]')";
				}
				elseif ($info['sign'] == 'update')
				{
					$sql = "UPDATE " .$ecs->table('goods_attr'). " SET attr_price = '$info[attr_price]' WHERE goods_attr_id = '$info[goods_attr_id]' LIMIT 1";
				}
				else
				{
					$sql = "DELETE FROM " .$ecs->table('goods_attr'). " WHERE goods_attr_id = '$info[goods_attr_id]' LIMIT 1";
				}
				$db->query($sql);
			}
		}
	}
 
	/* 处理会员价格 */
	if (isset($_POST['user_rank']) && isset($_POST['user_price']))
	{
		handle_member_price($goods_id, $_POST['user_rank'], $_POST['user_price']);
	}
	
	/* 处理优惠价格 */
	if (isset($_POST['volume_number']) && isset($_POST['volume_price']))
	{
		$temp_num = array_count_values($_POST['volume_number']);
		foreach($temp_num as $v)
		{
			if ($v > 1)
			{
				sys_msg($_LANG['volume_number_continuous'], 1, array(), false);
				break;
			}
		}
		handle_volume_price($goods_id, $_POST['volume_number'], $_POST['volume_price']);
	}

	/* 处理扩展分类 */
	if (isset($_POST['supplier_cat_id']))
	{
		handle_other_cat2($goods_id, array_unique($_POST['supplier_cat_id']));
	}
	
 
	/* 处理关联商品 */
	handle_link_goods($goods_id);
	
	/* 处理组合商品 */
	handle_group_goods($goods_id);
	
	/* 处理关联文章 */
	handle_goods_article($goods_id);
	 
	
	/* 重新格式化图片名称 */
	$original_img = reformat_image_name('goods', $goods_id, $original_img, 'source');
	$goods_img = reformat_image_name('goods', $goods_id, $goods_img, 'goods');
	$goods_thumb = reformat_image_name('goods_thumb', $goods_id, $goods_thumb, 'thumb');
	if ($goods_img !== false)
	{
		$db->query("UPDATE " . $ecs->table('goods') . " SET goods_img = '$goods_img' WHERE goods_id='$goods_id'");
	}
	
	if ($original_img !== false)
	{
		$db->query("UPDATE " . $ecs->table('goods') . " SET original_img = '$original_img' WHERE goods_id='$goods_id'");
	}
	
	if ($goods_thumb !== false)
	{
		$db->query("UPDATE " . $ecs->table('goods') . " SET goods_thumb = '$goods_thumb' WHERE goods_id='$goods_id'");
	}
	
	/* 如果有图片，把商品图片加入图片相册 */
	if (isset($img))
	{
		/* 重新格式化图片名称 */
		if (empty($is_url_goods_img))
		{
			$img = reformat_image_name('gallery', $goods_id, $img, 'source');
			$gallery_img = reformat_image_name('gallery', $goods_id, $gallery_img, 'goods');
			$gallery_img = reformat_image_name('gallery', $goods_id, $goods_img, 'goods');
		}
		else
		{
			$img = $url_goods_img;
			$gallery_img = $url_goods_img;
		}
		
		$gallery_thumb = reformat_image_name('gallery_thumb', $goods_id, $gallery_thumb, 'thumb');
		$sql = "INSERT INTO " . $ecs->table('goods_gallery') . " (goods_id, img_url, img_desc, thumb_url, img_original) " .
				"VALUES ('$goods_id', '$gallery_img', '', '$gallery_thumb', '$img')";
		$db->query($sql);
	}
	
	/* 处理相册图片 */
	handle_gallery_image($goods_id, $_FILES['img_url'], $_POST['img_desc'], $_POST['img_file']);


	
	/* 不保留商品原图的时候删除原图 */
	if ($proc_thumb && !$_CFG['retain_original_img'] && !empty($original_img))
	{
		$db->query("UPDATE " . $ecs->table('goods') . " SET original_img='' WHERE `goods_id`='{$goods_id}'");
		$db->query("UPDATE " . $ecs->table('goods_gallery') . " SET img_original='' WHERE `goods_id`='{$goods_id}'");
		@unlink('../' . $original_img);
		@unlink('../' . $img);
	}

	/* 记录上一次选择的分类和品牌 */
	setcookie('ECSCP[last_choose]', $catgory_id . '|' . $brand_id, gmtime() + 86400);
	/* 清空缓存 */
	clear_cache_files();
	
	/* 提示页面 */
	$link = array();
	if (check_goods_specifications_exist($goods_id))
	{
		$link[0] = array('href' => 'supplier_m_goods.php?act=product_list&supplier_status='. $_REQUEST['supplier_status'] .'&goods_id=' . $goods_id, 'text' => $_LANG['product']);
	}
	if ($code == 'virtual_card')
	{
		$link[1] = array('href' => 'virtual_card.php?act=replenish&goods_id=' . $goods_id, 'text' => $_LANG['add_replenish']);
	}
	 
    $link[2] = add_link($code);
	 
	
	
	
	 $link[3] = array('href' => 'supplier_m_goods.php?act=list&supplier_status=0' , 'text' => '返回商品列表');
 
	
	
	//$key_array = array_keys($link);
	for($i=0;$i<count($link);$i++)
	{
		$key_array[]=$i;
	}
	krsort($link);
	$link = array_combine($key_array, $link);
	sys_msg($_LANG['add_goods_ok'] , 0, $link);
}




function action_remove() {
	$smarty = $GLOBALS ['smarty'];
	$ecs= $GLOBALS['ecs'];
	$db= $GLOBALS['db'];
	include_once(getShopRoot() . 'members/includes/cls_exchange.php');//调用会员中心的类
	$exc = new exchange($ecs->table('goods'), $db, 'goods_id', 'goods_name');

	$goods_id = intval($_REQUEST['id']);
	
	if ($exc->edit("is_delete = 1", $goods_id))
	{
		clear_cache_files();
		$goods_name = $exc->get_name($goods_id);
		//admin_log(addslashes($goods_name), 'trash', 'goods'); // 记录日志
		$url = 'supplier_m_goods.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
		ecs_header("Location: $url\n");
		exit;
	}
	 
}
function action_query() {
	$smarty = $GLOBALS ['smarty'];
	$ecs= $GLOBALS['ecs'];
	$db= $GLOBALS['db'];
	$is_delete = empty($_REQUEST['is_delete']) ? 0 : intval($_REQUEST['is_delete']);
	$code = empty($_REQUEST['extension_code']) ? '' : trim($_REQUEST['extension_code']);
	$goods_list = goods_list($is_delete, ($code=='') ? 1 : 0);
	
	$handler_list = array();
	$handler_list['virtual_card'][] = array('url'=>'virtual_card.php?act=card', 'title'=>$_LANG['card'], 'img'=>'icon_send_bonus.gif');
	$handler_list['virtual_card'][] = array('url'=>'virtual_card.php?act=replenish', 'title'=>$_LANG['replenish'], 'img'=>'icon_add.gif');
	$handler_list['virtual_card'][] = array('url'=>'virtual_card.php?act=batch_card_add', 'title'=>$_LANG['batch_card_add'], 'img'=>'icon_output.gif');
	
	if (isset($handler_list[$code]))
	{
		$smarty->assign('add_handler',      $handler_list[$code]);
	}
	
	$smarty->assign('code',         $code);
	$smarty->assign('goods_list',   $goods_list['goods']);
	$smarty->assign('filter',       $goods_list['filter']);
	$smarty->assign('record_count', $goods_list['record_count']);
	$smarty->assign('page_count',   $goods_list['page_count']);
	$smarty->assign('list_type',    $is_delete ? 'trash' : 'goods');
	$smarty->assign('use_storage',  empty($GLOBALS['_CFG']['use_storage']) ? 0 : 1);
	
	/* 排序标记 */
	$sort_flag  = sort_flag($goods_list['filter']);
	$smarty->assign($sort_flag['tag'], $sort_flag['img']);
	
	/* 获取商品类型存在规格的类型 */
	$specifications = get_goods_type_specifications();
	$smarty->assign('specifications', $specifications);
	
	$tpl = $is_delete ? 'supplier/goods_trash.dwt' : 'supplier/goods_list.dwt';
	
	make_json_result($smarty->fetch($tpl), '',
			array('filter' => $goods_list['filter'], 'page_count' => $goods_list['page_count']));
	
}

function action_update() {
 
	require_once(ROOT_PATH. '/includes/lib_goods.php');
	include_once(ROOT_PATH . '/includes/cls_image.php');
	$image = new cls_image($_CFG['bgcolor']);
	require_once (getShopRootPath(). 'languages/' . $GLOBALS['_CFG'] ['lang'] . '/admin/suppliers_goods.php');
	$smarty = $GLOBALS ['smarty'];
	$ecs= $GLOBALS['ecs'];
	$db= $GLOBALS['db'];
	$code = empty($_REQUEST['extension_code']) ? '' : trim($_REQUEST['extension_code']);
	
	/* 是否处理缩略图 */
	$proc_thumb = (isset($GLOBALS['shop_id']) && $GLOBALS['shop_id'] > 0)? false : true;
	
	/* 检查货号是否重复 */
	if ($_POST['goods_sn'])
	{
		$sql = "SELECT COUNT(*) FROM " . $ecs->table('goods') .
		" WHERE goods_sn = '$_POST[goods_sn]' AND is_delete = 0 AND goods_id <> '$_POST[goods_id]'";
		if ($db->getOne($sql) > 0)
		{
			sys_msg($_LANG['goods_sn_exists'], 1, array(), true);
		}
	}
	
	
	
	
	/* 检查图片：如果有错误，检查尺寸是否超过最大值；否则，检查文件类型 */
	if (isset($_FILES['goods_img']['error'])) // php 4.2 版本才支持 error
	{
		// 最大上传文件大小
		$php_maxsize = ini_get('upload_max_filesize');
		$htm_maxsize = '2M';
	
		// 商品图片
		if ($_FILES['goods_img']['error'] == 0)
		{	
			if (!$image->check_img_type($_FILES['goods_img']['type']))
			{
				sys_msg($_LANG['invalid_goods_img'], 1, array(), false);
			}
		}
		elseif ($_FILES['goods_img']['error'] == 1)
		{
			sys_msg(sprintf($_LANG['goods_img_too_big'], $php_maxsize), 1, array(), false);
		}
		elseif ($_FILES['goods_img']['error'] == 2)
		{
			sys_msg(sprintf($_LANG['goods_img_too_big'], $htm_maxsize), 1, array(), false);
		}
		
		// 商品缩略图
		if (isset($_FILES['goods_thumb']))
		{
			
			if ($_FILES['goods_thumb']['error'] == 0)
			{
				if (!$image->check_img_type($_FILES['goods_thumb']['type']))
				{
					sys_msg($_LANG['invalid_goods_thumb'], 1, array(), false);
				}
			}
			elseif ($_FILES['goods_thumb']['error'] == 1)
			{
				sys_msg(sprintf($_LANG['goods_thumb_too_big'], $php_maxsize), 1, array(), false);
			}
			elseif ($_FILES['goods_thumb']['error'] == 2)
			{
				sys_msg(sprintf($_LANG['goods_thumb_too_big'], $htm_maxsize), 1, array(), false);
			}
		}
		
		// 相册图片
		
		if($_FILES['img_url']['error'])
		{
			/* 代码增加_end   By www.ecshop68.com */
			foreach ($_FILES['img_url']['error'] AS $key => $value)
			{
				if ($value == 0)
				{
					if (!$image->check_img_type($_FILES['img_url']['type'][$key]))
					{
						sys_msg(sprintf($_LANG['invalid_img_url'], $key + 1), 1, array(), false);
					}
				}
				elseif ($value == 1)
				{
					sys_msg(sprintf($_LANG['img_url_too_big'], $key + 1, $php_maxsize), 1, array(), false);
				}
				elseif ($_FILES['img_url']['error'] == 2)
				{
					sys_msg(sprintf($_LANG['img_url_too_big'], $key + 1, $htm_maxsize), 1, array(), false);
				}
			}
		}
	}
	/* 4.1版本 */
	else
	{
		// 商品图片
		if ($_FILES['goods_img']['tmp_name'] != 'none')
		{
			if (!$image->check_img_type($_FILES['goods_img']['type']))
			{
				
				sys_msg($_LANG['invalid_goods_img'], 1, array(), false);
			}
		}
		
		// 商品缩略图
		if (isset($_FILES['goods_thumb']))
		{
			if ($_FILES['goods_thumb']['tmp_name'] != 'none')
			{
				if (!$image->check_img_type($_FILES['goods_thumb']['type']))
				{
					sys_msg($_LANG['invalid_goods_thumb'], 1, array(), false);
				}
			}
		}
		
		// 相册图片
		foreach ($_FILES['img_url']['tmp_name'] AS $key => $value)
		{
			if ($value != 'none')
			{
				if (!$image->check_img_type($_FILES['img_url']['type'][$key]))
				{
					sys_msg(sprintf($_LANG['invalid_img_url'], $key + 1), 1, array(), false);
				}
			}
		}
	}

	/* 插入还是更新的标识 */
	$is_insert = $_REQUEST['act'] == 'insert';
	
	/* 处理商品图片 */
	$goods_img        = '';  // 初始化商品图片
	$goods_thumb      = '';  // 初始化商品缩略图
	$original_img     = '';  // 初始化原始图片
	$old_original_img = '';  // 初始化原始图片旧图
	
	// 如果上传了商品图片，相应处理
	if (($_FILES['goods_img']['tmp_name'] != '' && $_FILES['goods_img']['tmp_name'] != 'none') or (($_POST['goods_img_url'] != $_LANG['lab_picture_url'] && $_POST['goods_img_url'] != 'http://') && $is_url_goods_img = 1))
	{
		
		if ($_REQUEST['goods_id'] > 0)
		{
			/* 删除原来的图片文件 */
			$sql = "SELECT goods_thumb, goods_img, original_img " .
					" FROM " . $ecs->table('goods') .
					" WHERE goods_id = '$_REQUEST[goods_id]'";
			$row = $db->getRow($sql);
			if ($row['goods_thumb'] != '' && is_file('../' . $row['goods_thumb']))
			{
				@unlink('../' . $row['goods_thumb']);
			}
			if ($row['goods_img'] != '' && is_file('../' . $row['goods_img']))
			{
				@unlink('../' . $row['goods_img']);
			}
			if ($row['original_img'] != '' && is_file('../' . $row['original_img']))
			{
				/* 先不处理，以防止程序中途出错停止 */
				//$old_original_img = $row['original_img']; //记录旧图路径
			}
			/* 清除原来商品图片 */
			if ($proc_thumb === false)
			{
				get_image_path($_REQUEST[goods_id], $row['goods_img'], false, 'goods', true);
				get_image_path($_REQUEST[goods_id], $row['goods_thumb'], true, 'goods', true);
			}
		}
		
	
		if (empty($is_url_goods_img))
		{
			$original_img   = $image->upload_image($_FILES['goods_img']); // 原始图片
		}
		elseif ($_POST['goods_img_url'])
		{
			
			if(preg_match('/(.jpg|.png|.gif|.jpeg)$/',$_POST['goods_img_url']) && copy(trim($_POST['goods_img_url']), ROOT_PATH . 'temp/' . basename($_POST['goods_img_url'])))
			{
				$original_img = 'temp/' . basename($_POST['goods_img_url']);
			}
			
		}
		 
		if ($original_img === false)
		{
			sys_msg($image->error_msg(), 1, array(), false);
		}
		$goods_img      = $original_img;   // 商品图片
		
		/* 复制一份相册图片 */
		/* 添加判断是否自动生成相册图片 */
		if ($_CFG['auto_generate_gallery'])
		{
			$img        = $original_img;   // 相册图片
			$pos        = strpos(basename($img), '.');
			$newname    = dirname($img) . '/' . $image->random_filename() . substr(basename($img), $pos);
			if (!copy('../' . $img, '../' . $newname))
			{
				sys_msg('fail to copy file: ' . realpath('../' . $img), 1, array(), false);
			}
			$img        = $newname;
			
			$gallery_img    = $img;
			$gallery_thumb  = $img;
		}
		
	 
		
		// 如果系统支持GD，缩放商品图片，且给商品图片和相册图片加水印
		if ($proc_thumb && $image->gd_version() > 0 && $image->check_img_function($_FILES['goods_img']['type']) || $is_url_goods_img)
		{
			
			if (empty($is_url_goods_img))
			{
				// 如果设置大小不为0，缩放图片
				if ($_CFG['image_width'] != 0 || $_CFG['image_height'] != 0)
				{
					$goods_img = $image->make_thumb('../'. $goods_img , $GLOBALS['_CFG']['image_width'],  $GLOBALS['_CFG']['image_height']);
					if ($goods_img === false)
					{
						sys_msg($image->error_msg(), 1, array(), false);
					}
				}
				
				/* 添加判断是否自动生成相册图片 */
				if ($_CFG['auto_generate_gallery'])
				{
					$newname    = dirname($img) . '/' . $image->random_filename() . substr(basename($img), $pos);
					if (!copy('../' . $img, '../' . $newname))
					{
						sys_msg('fail to copy file: ' . realpath('../' . $img), 1, array(), false);
					}
					$gallery_img        = $newname;
				}
				
				// 加水印
				if (intval($_CFG['watermark_place']) > 0 && !empty($GLOBALS['_CFG']['watermark']))
				{
					if ($image->add_watermark('../'.$goods_img,'',$GLOBALS['_CFG']['watermark'], $GLOBALS['_CFG']['watermark_place'], $GLOBALS['_CFG']['watermark_alpha']) === false)
					{
						sys_msg($image->error_msg(), 1, array(), false);
					}
					/* 添加判断是否自动生成相册图片 */
					if ($_CFG['auto_generate_gallery'])
					{
						if ($image->add_watermark('../'. $gallery_img,'',$GLOBALS['_CFG']['watermark'], $GLOBALS['_CFG']['watermark_place'], $GLOBALS['_CFG']['watermark_alpha']) === false)
						{
							sys_msg($image->error_msg(), 1, array(), false);
						}
					}
				}
			}
		
			// 相册缩略图
			/* 添加判断是否自动生成相册图片 */
			if ($_CFG['auto_generate_gallery'])
			{
				if ($_CFG['thumb_width'] != 0 || $_CFG['thumb_height'] != 0)
				{
					$gallery_thumb = $image->make_thumb('../' . $img, $GLOBALS['_CFG']['thumb_width'],  $GLOBALS['_CFG']['thumb_height']);
					if ($gallery_thumb === false)
					{
						sys_msg($image->error_msg(), 1, array(), false);
					}
				}
			}
		}
		 
	}
	
	
	// 是否上传商品缩略图
	if (isset($_FILES['goods_thumb']) && $_FILES['goods_thumb']['tmp_name'] != '' &&
			isset($_FILES['goods_thumb']['tmp_name']) &&$_FILES['goods_thumb']['tmp_name'] != 'none')
	{
		// 上传了，直接使用，原始大小
		$goods_thumb = $image->upload_image($_FILES['goods_thumb']);
		if ($goods_thumb === false)
		{
			sys_msg($image->error_msg(), 1, array(), false);
		}
	}
	else
	{	
		// 未上传，如果自动选择生成，且上传了商品图片，生成所略图
		if ($proc_thumb && isset($_POST['auto_thumb']) && !empty($original_img))
		{
			// 如果设置缩略图大小不为0，生成缩略图
			if ($_CFG['thumb_width'] != 0 || $_CFG['thumb_height'] != 0)
			{
				$goods_thumb = $image->make_thumb('../' . $original_img, $GLOBALS['_CFG']['thumb_width'],  $GLOBALS['_CFG']['thumb_height']);
				if ($goods_thumb === false)
				{
					sys_msg($image->error_msg(), 1, array(), false);
				}
			}
			else
			{ 
				$goods_thumb = $original_img;
			}
		}
	}
	
	
	/* 删除下载的外链原图 */
	if (!empty($is_url_goods_img))
	{
		unlink(ROOT_PATH . $original_img);
		empty($newname) || unlink(ROOT_PATH . $newname);
		$url_goods_img = $goods_img = $original_img = htmlspecialchars(trim($_POST['goods_img_url']));
	}
	
	
	/* 如果没有输入商品货号则自动生成一个商品货号 */
	if (empty($_POST['goods_sn']))
	{
		$max_id     =  $_REQUEST['goods_id'];
		$goods_sn   = generate_goods_sn($max_id);
	}
	else
	{
		$goods_sn   = $_POST['goods_sn'];
	}
	
	/* 处理商品数据 */
	$cost_price = !empty($_POST['cost_price']) ? $_POST['cost_price'] : 0;
	$shop_price = !empty($_POST['shop_price']) ? $_POST['shop_price'] : 0;
	$market_price = !empty($_POST['market_price']) ? $_POST['market_price'] : 0;
	$promote_price = !empty($_POST['promote_price']) ? floatval($_POST['promote_price'] ) : 0;
	$is_promote = empty($promote_price) ? 0 : 1;
	$zhekou = ($promote_price == 0 ? 10.0 : (number_format(($promote_price/$shop_price),2))*10);
	$promote_start_date = ($is_promote && !empty($_POST['promote_start_date'])) ? local_strtotime($_POST['promote_start_date']) : 0;
	$promote_end_date = ($is_promote && !empty($_POST['promote_end_date'])) ? local_strtotime($_POST['promote_end_date']) : 0;
	$goods_weight = !empty($_POST['goods_weight']) ? $_POST['goods_weight'] * $_POST['weight_unit'] : 0;
	$is_best = isset($_POST['is_best']) ? 1 : 0;
	$is_new = isset($_POST['is_new']) ? 1 : 0;
	$is_hot = isset($_POST['is_hot']) ? 1 : 0;
	$is_on_sale = isset($_POST['is_on_sale']) ? 1 : 0;
	$is_alone_sale = isset($_POST['is_alone_sale']) ? 1 : 0;
	$is_shipping = isset($_POST['is_shipping']) ? 1 : 0;
	$goods_number = isset($_POST['goods_number']) ? $_POST['goods_number'] : 0;
	$warn_number = isset($_POST['warn_number']) ? $_POST['warn_number'] : 0;
	$goods_type = isset($_POST['goods_type']) ? $_POST['goods_type'] : 0;
	$give_integral = isset($_POST['give_integral']) ? intval($_POST['give_integral']) : '0';
	$rank_integral = isset($_POST['rank_integral']) ? intval($_POST['rank_integral']) : '-1';
	$suppliers_id = isset($_POST['suppliers_id']) ? intval($_POST['suppliers_id']) : '0';
	$supplier_id = isset($_SESSION['supplier_id']) ? intval($_SESSION['supplier_id']) : $_COOKIE['ECSCP']['supplier_id'];

	//手机专享价格 app   jx
	$exclusive = !empty($_POST['exclusive']) ? $_POST['exclusive'] : -1;
	//手机专享价格 app  jx
	
	$goods_name_style = $_POST['goods_name_color'] . '+' . $_POST['goods_name_style'];
	
	$catgory_id = empty($_POST['cat_id']) ? '' : intval($_POST['cat_id']);
	
	//$catgory_id = $_REQUEST['cat_id_'.$_REQUEST['cat_level_id']];
	$brand_id = empty($_POST['brand_id']) ? '' : intval($_POST['brand_id']);
	$reason  = $_POST['reason'];
	$is_pass =  $_POST['is_pass'];
	$area_catgory_id = empty($_POST['area_cat_id']) ? 1 : intval($_POST['area_cat_id']);//UEUCS_364642382
	die("kkk".goods_parse_url($goods_thumb));
	$goods_thumb = (empty($goods_thumb) && !empty($_POST['goods_thumb_url']) && goods_parse_url($_POST['goods_thumb_url'])) ? htmlspecialchars(trim($_POST['goods_thumb_url'])) : $goods_thumb;
	$goods_thumb = (empty($goods_thumb) && isset($_POST['auto_thumb']))? $goods_img : $goods_thumb;
	die;
	$buymax = !empty($_POST['buymax']) ? floatval($_POST['buymax'] ) : 0;
	$is_buy = empty($buymax) ? 0 : 1;
	$buymax_start_date = ($is_buy && !empty($_POST['buymax_start_date'])) ? local_strtotime($_POST['buymax_start_date']) : 0;
	$buymax_end_date = ($is_buy && !empty($_POST['buymax_end_date'])) ? local_strtotime($_POST['buymax_end_date']) : 0;
	/* 修改*/
	
	/* 如果有上传图片，删除原来的商品图 */
	$sql = "SELECT goods_thumb, goods_img, original_img, supplier_status " .
			" FROM " . $ecs->table('goods') .
			" WHERE goods_id = '$_REQUEST[goods_id]'";
	$row = $db->getRow($sql);
	if ($proc_thumb && $goods_img && $row['goods_img'] && !goods_parse_url($row['goods_img']))
	{
		@unlink(ROOT_PATH . $row['goods_img']);
		@unlink(ROOT_PATH . $row['original_img']);
	}
	
	
	
 
	if ($proc_thumb && $goods_thumb && $row['goods_thumb'] && !goods_parse_url($row['goods_thumb']))
	{
		@unlink(ROOT_PATH . $row['goods_thumb']);
	}
	
	$sql = "UPDATE " . $ecs->table('goods') . " SET " .
			"goods_name = '$_POST[goods_name]', " .
			"goods_name_style = '$goods_name_style', " .
			"goods_sn = '$goods_sn', " .
			"cat_id = '$catgory_id', " .
			"cost_price = '$cost_price', " .
			"brand_id = '$brand_id', " .
			"shop_price = '$shop_price', " .
			"area_cat_id = '$area_catgory_id', " .//UEUCS_364642382
			"exclusive = '$exclusive', ".//手机专享价  app jx
			"market_price = '$market_price', " .
			"is_promote = '$is_promote', " .
			"zhekou = '$zhekou', " .
			"promote_price = '$promote_price', " .
			"promote_start_date = '$promote_start_date', " .
			"is_buy = '$is_buy', " .
			"buymax = '$buymax', " .
			"buymax_start_date = '$buymax_start_date', " .
			"buymax_end_date = '$buymax_end_date', " .
			"supplier_id = '$supplier_id', " .
			"promote_end_date = '$promote_end_date', ";

	/* 如果有上传图片，需要更新数据库 */
	if ($goods_img)
	{
		$sql .= "goods_img = '$goods_img', original_img = '$original_img', ";
	}
	if ($goods_thumb)
	{
		$sql .= "goods_thumb = '$goods_thumb', ";
	}
	if ($code != '')
	{
		$sql .= "is_real=0, extension_code='$code', ";
	}
	if ($row['supplier_status']=='-1')
	{
		$sql .= "supplier_status='0', ";
	}
	
	if ($row['supplier_status'] != '1')
	{
		$is_on_sale = 0;
	}

	$sql .= "keywords = '$_POST[keywords]', " .
	"goods_brief = '$_POST[goods_brief]', " .
	"seller_note = '$_POST[seller_note]', " .
	"goods_weight = '$goods_weight'," .
	"goods_number = '$goods_number', " .
	"warn_number = '$warn_number', " .
	"integral = '$_POST[integral]', " .
	"give_integral = '$give_integral', " .
	"rank_integral = '$rank_integral', " .
	"is_pass = '$is_pass', " .
	"reason = '$reason', " .
	"is_best = '$is_best', " .
	"is_new = '$is_new', " .
	"is_hot = '$is_hot', " .
	"is_on_sale = '$is_on_sale', " .
	"is_alone_sale = '$is_alone_sale', " .
	"is_shipping = '$is_shipping', " .
	"goods_desc = '$_POST[goods_desc]', " .
	"last_update = '". gmtime() ."', ".
	"goods_type = '$goods_type' " .
	"WHERE goods_id = '$_REQUEST[goods_id]' LIMIT 1";
	
	
	$db->query($sql);
	
	/* 商品编号 */
	$goods_id =   $_REQUEST['goods_id'];
	//同步购物车中相关商品价格
		//只有修改操作才会触发
    tongbu_cart_price(intval($_REQUEST['goods_id']));
  
	/*存入条形码*/
	if($_POST['txm_shu'] && $_POST['tiaoxingm']){//如果txm_shu 和 tiaoxingm存在 就存入  不存在就不执行
		if(isset($_POST['txm_shu']) && isset($_POST['tiaoxingm']) || (empty($_POST['txm_shu'])) && (empty($_POST['tiaoxingm'])) ){
			$type = $_POST['txm_shu'];
			$bar_code = $_POST['tiaoxingm'];
			$db->query("DELETE FROM" .$ecs->table('bar_code')."WHERE goods_id ='$goods_id'");//根据商品ID清空数据
			foreach($type as $key=>$value){
				foreach($bar_code as $k=>$v){
					$arr['bar_code'] = $v;
					$arr['taypes'] = $value;
					$arr['goods_id'] = $goods_id;
					if($key == $k){
						$sql = "INSERT INTO " . $ecs->table('bar_code') . " (goods_id, taypes, bar_code) " .
								"VALUES ('$arr[goods_id]', '$arr[taypes]','$arr[bar_code]')";//插入数据
						$name = $db->query($sql);
					}
				}
			}
		}
		
	}
	
	
	/* 处理属性 */
	if ((isset($_POST['attr_id_list']) && isset($_POST['attr_value_list'])) || (empty($_POST['attr_id_list']) && empty($_POST['attr_value_list'])))
	{
		// 取得原有的属性值
		$goods_attr_list = array();
		
		$keywords_arr = explode(" ", $_POST['keywords']);
		
		$keywords_arr = array_flip($keywords_arr);
		if (isset($keywords_arr['']))
		{
			unset($keywords_arr['']);
		}
		
		$sql = "SELECT attr_id, attr_index FROM " . $ecs->table('attribute') . " WHERE cat_id = '$goods_type'";
		
		$attr_res = $db->query($sql);
		
		$attr_list = array();
		
		while ($row = $db->fetchRow($attr_res))
		{
			$attr_list[$row['attr_id']] = $row['attr_index'];
		}
		
		$sql = "SELECT g.*, a.attr_type
                FROM " . $ecs->table('goods_attr') . " AS g
                    LEFT JOIN " . $ecs->table('attribute') . " AS a
                    ON a.attr_id = g.attr_id
                    WHERE g.goods_id = '$goods_id'";
		
		$res = $db->query($sql);
		
		while ($row = $db->fetchRow($res))
		{
			$goods_attr_list[$row['attr_id']][$row['attr_value']] = array('sign' => 'delete', 'goods_attr_id' => $row['goods_attr_id']);
		}
		// 循环现有的，根据原有的做相应处理
		if(isset($_POST['attr_id_list']))
		{
			foreach ($_POST['attr_id_list'] AS $key => $attr_id)
			{
				$attr_value = $_POST['attr_value_list'][$key];
				$attr_price = $_POST['attr_price_list'][$key];
				$attr_price = ($attr_price>=0) ? $attr_price : 0;
				if (!empty($attr_value))
				{
					if (isset($goods_attr_list[$attr_id][$attr_value]))
					{
						// 如果原来有，标记为更新
						$goods_attr_list[$attr_id][$attr_value]['sign'] = 'update';
						$goods_attr_list[$attr_id][$attr_value]['attr_price'] = $attr_price;
					}
					else
					{
						// 如果原来没有，标记为新增
						$goods_attr_list[$attr_id][$attr_value]['sign'] = 'insert';
						$goods_attr_list[$attr_id][$attr_value]['attr_price'] = $attr_price;
					}
					$val_arr = explode(' ', $attr_value);
					foreach ($val_arr AS $k => $v)
					{
						if (!isset($keywords_arr[$v]) && $attr_list[$attr_id] == "1")
						{
							$keywords_arr[$v] = $v;
						}
					}
				}
			}
		}
		$keywords = join(' ', array_flip($keywords_arr));
		
		$sql = "UPDATE " .$ecs->table('goods'). " SET keywords = '$keywords' WHERE goods_id = '$goods_id' LIMIT 1";
		
		$db->query($sql);
		
		/* 插入、更新、删除数据 */
		foreach ($goods_attr_list as $attr_id => $attr_value_list)
		{
			foreach ($attr_value_list as $attr_value => $info)
			{
				if ($info['sign'] == 'insert')
				{
					$sql = "INSERT INTO " .$ecs->table('goods_attr'). " (attr_id, goods_id, attr_value, attr_price)".
							"VALUES ('$attr_id', '$goods_id', '$attr_value', '$info[attr_price]')";
				}
				elseif ($info['sign'] == 'update')
				{
					$sql = "UPDATE " .$ecs->table('goods_attr'). " SET attr_price = '$info[attr_price]' WHERE goods_attr_id = '$info[goods_attr_id]' LIMIT 1";
				}
				else
				{
					$sql = "DELETE FROM " .$ecs->table('goods_attr'). " WHERE goods_attr_id = '$info[goods_attr_id]' LIMIT 1";
				}
				$db->query($sql);
			}
		}
	}
	
	/* 处理会员价格 */
	if (isset($_POST['user_rank']) && isset($_POST['user_price']))
	{
		handle_member_price($goods_id, $_POST['user_rank'], $_POST['user_price']);
	}
	
	/* 处理优惠价格 */
	if (isset($_POST['volume_number']) && isset($_POST['volume_price']))
	{
		$temp_num = array_count_values($_POST['volume_number']);
		foreach($temp_num as $v)
		{
			if ($v > 1)
			{
				sys_msg($_LANG['volume_number_continuous'], 1, array(), false);
				break;
			}
		}
		handle_volume_price($goods_id, $_POST['volume_number'], $_POST['volume_price']);
	}
	
	
	/* 处理扩展分类 */
	if (isset($_POST['supplier_cat_id']))
	{
		handle_other_cat2($goods_id, array_unique($_POST['supplier_cat_id']));
	}
	
	 
	/* 重新格式化图片名称 */
	$original_img = reformat_image_name('goods', $goods_id, $original_img, 'source');
	$goods_img = reformat_image_name('goods', $goods_id, $goods_img, 'goods');
	$goods_thumb = reformat_image_name('goods_thumb', $goods_id, $goods_thumb, 'thumb');
	if ($goods_img !== false)
	{
		$db->query("UPDATE " . $ecs->table('goods') . " SET goods_img = '$goods_img' WHERE goods_id='$goods_id'");
	}
	
	if ($original_img !== false)
	{
		$db->query("UPDATE " . $ecs->table('goods') . " SET original_img = '$original_img' WHERE goods_id='$goods_id'");
	}
	
	if ($goods_thumb !== false)
	{
		$db->query("UPDATE " . $ecs->table('goods') . " SET goods_thumb = '$goods_thumb' WHERE goods_id='$goods_id'");
	}
	
	/* 如果有图片，把商品图片加入图片相册 */
	if (isset($img))
	{
		/* 重新格式化图片名称 */
		if (empty($is_url_goods_img))
		{
			$img = reformat_image_name('gallery', $goods_id, $img, 'source');
			$gallery_img = reformat_image_name('gallery', $goods_id, $gallery_img, 'goods');
			$gallery_img = reformat_image_name('gallery', $goods_id, $goods_img, 'goods');
		}
		else
		{
			$img = $url_goods_img;
			$gallery_img = $url_goods_img;
		}
		
		$gallery_thumb = reformat_image_name('gallery_thumb', $goods_id, $gallery_thumb, 'thumb');
		$sql = "INSERT INTO " . $ecs->table('goods_gallery') . " (goods_id, img_url, img_desc, thumb_url, img_original) " .
				"VALUES ('$goods_id', '$gallery_img', '', '$gallery_thumb', '$img')";
		$db->query($sql);
	}
	
	/* 处理相册图片 */
	handle_gallery_image($goods_id, $_FILES['img_url'], $_POST['img_desc'], $_POST['img_file']);
	
	/* 编辑时处理相册图片描述 */
	if (!$is_insert && isset($_POST['old_img_desc']))
	{
		foreach ($_POST['old_img_desc'] AS $img_id => $img_desc)
		{
			$sql = "UPDATE " . $ecs->table('goods_gallery') . " SET img_desc = '$img_desc' WHERE img_id = '$img_id' LIMIT 1";
			$db->query($sql);
		}
	}
	
	/* 不保留商品原图的时候删除原图 */
	if ($proc_thumb && !$_CFG['retain_original_img'] && !empty($original_img))
	{
		$db->query("UPDATE " . $ecs->table('goods') . " SET original_img='' WHERE `goods_id`='{$goods_id}'");
		$db->query("UPDATE " . $ecs->table('goods_gallery') . " SET img_original='' WHERE `goods_id`='{$goods_id}'");
		@unlink('../' . $original_img);
		@unlink('../' . $img);
	}
	
	/* 记录上一次选择的分类和品牌 */
	setcookie('ECSCP[last_choose]', $catgory_id . '|' . $brand_id, gmtime() + 86400);
	/* 清空缓存 */
	clear_cache_files();
	
	/* 提示页面 */
	$link = array();
	if (check_goods_specifications_exist($goods_id))
	{
		$link[0] = array('href' => 'supplier_m_goods.php?act=product_list&supplier_status='. $_REQUEST['supplier_status'] .'&goods_id=' . $goods_id, 'text' => $_LANG['product']);
	}
	if ($code == 'virtual_card')
	{
		$link[1] = array('href' => 'virtual_card.php?act=replenish&goods_id=' . $goods_id, 'text' => $_LANG['add_replenish']);
	}
	if ($is_insert)
	{
		$link[2] = add_link($code);
	}
	
	
	
 
// 	 $link[3] = array('href' => 'supplier_m_goods.php?act=list&supplier_status=' . $_REQUEST['supplier_status'], 'text' => '返回商品列表');
	 $link[3] = array('href' => 'supplier_m_goods.php?act=list', 'text' => '返回商品列表');
		
	
	
	//$key_array = array_keys($link);
	for($i=0;$i<count($link);$i++)
	{
		$key_array[]=$i;
	}
	krsort($link);
	$link = array_combine($key_array, $link);
	sys_msg( $_LANG['edit_goods_ok'], 0, $link);
	
}

 

//获取快递模块的路径
function getShopRootPath(){
	$pos=strpos(ROOT_PATH,"mobile/");
	$shippingRootPath= substr(ROOT_PATH,0,$pos);
	return $shippingRootPath;
	
}
 

function action_copy() {
	
}

function action_check_goods_sn(){
	$smarty = $GLOBALS ['smarty'];
	$ecs= $GLOBALS['ecs'];
	$db= $GLOBALS['db'];
	include_once(getShopRoot() . 'members/includes/cls_exchange.php');//调用会员中心的类
	$exc = new exchange($ecs->table('goods'), $db, 'goods_id', 'goods_name');
	$goods_id = intval($_REQUEST['goods_id']);
	$goods_sn = htmlspecialchars(json_str_iconv(trim($_REQUEST['goods_sn'])));
	 
	/* 检查是否重复 */
	if (!$exc->is_only('goods_sn', $goods_sn, $goods_id))
	{
		make_json_error("您输入的货号已存在，请换一个");
	}  
	if(!empty($goods_sn))
	{ 
		$sql="SELECT goods_id FROM ". $ecs->table('products')." WHERE product_sn='$goods_sn'";
		if($db->getOne($sql))
		{
			make_json_error("您输入的货号已存在，请换一个");
		}
	}
	 make_json_result('');
	
}

function goods_parse_url($url)
{
	$parse_url = @parse_url($url);
	return (!empty($parse_url['scheme']) && !empty($parse_url['host']));
}
/**
 * 添加页面
 */
function action_add() {
	$smarty = $GLOBALS ['smarty'];
	$ecs= $GLOBALS['ecs'];
	$db= $GLOBALS['db'];
	//include_once(ROOT_PATH . 'includes/fckeditor/fckeditor.php'); // 包含 html editor 类文件
	include_once(getShopRoot() . 'includes/Pinyin.php');
	
	$is_add = $_REQUEST['act'] == 'add'; // 添加还是编辑的标识
	$code = empty($_REQUEST['extension_code']) ? '' : trim($_REQUEST['extension_code']);
	$code=$code=='virual_card' ? 'virual_card': '';
	
	if (isset($_CFG['supplier_addbest']))
	{
		$smarty->assign('is_addbest', $_CFG['supplier_addbest']);
	}
	if (isset($_CFG['supplier_editgoods']))
	{
		$smarty->assign('is_editgoods', $_CFG['supplier_editgoods']);
	}
	if (isset($_CFG['supplier_secondadd']))
	{
		$smarty->assign('is_secondadd', $_CFG['supplier_secondadd']);
	}
	
	
	/* 供货商名 */
	$suppliers_list_name = suppliers_list_name();
	$suppliers_exists = 1;
	if (empty($suppliers_list_name))
	{
		$suppliers_exists = 0;
	}
	$smarty->assign('suppliers_exists', $suppliers_exists);
	$smarty->assign('suppliers_list_name', $suppliers_list_name);
	unset($suppliers_list_name, $suppliers_exists);
	
	/* 如果是安全模式，检查目录是否存在 */
	if (ini_get('safe_mode') == 1 && (!file_exists('../' . IMAGE_DIR . '/'.date('Ym')) || !is_dir('../' . IMAGE_DIR . '/'.date('Ym'))))
	{
		if (@!mkdir('../' . IMAGE_DIR . '/'.date('Ym'), 0777))
		{
			$warning = sprintf($_LANG['safe_mode_warning'], '../' . IMAGE_DIR . '/'.date('Ym'));
			$smarty->assign('warning', $warning);
		}
	}
	
	/* 如果目录存在但不可写，提示用户 */
	elseif (file_exists('../' . IMAGE_DIR . '/'.date('Ym')) && file_mode_info('../' . IMAGE_DIR . '/'.date('Ym')) < 2)
	{
		$warning = sprintf($_LANG['not_writable_warning'], '../' . IMAGE_DIR . '/'.date('Ym'));
		$smarty->assign('warning', $warning);
	}
	
	
	 
	
	   /* 取得商品信息 */
		/* 默认值 */
		$last_choose = array(0, 0);
		if (!empty($_COOKIE['ECSCP']['last_choose']))
		{
			$last_choose = explode('|', $_COOKIE['ECSCP']['last_choose']);
		}
		$goods = array(
				'goods_id'      => 0,
				'goods_desc'    => '',
				'cat_id'        => $last_choose[0],
				'brand_id'      => $last_choose[1],
				'is_on_sale'    => '1',
				'is_alone_sale' => '1',
				'is_shipping' => '0',
				'other_cat'     => array(), // 扩展分类
				'goods_type'    => 0,       // 商品类型
				'shop_price'    => 0,
				'promote_price' => 0,
				'market_price'  => 0,
				'integral'      => 0,
				'goods_number'  => $_CFG['default_storage'],
				'warn_number'   => 1,
				'promote_start_date' => local_date('Y-m-d'),
				'promote_end_date'   => local_date('Y-m-d', local_strtotime('+1 month')),
				'goods_weight'  => 0,
				'give_integral' => 0,
				'exclusive' => -1,//手机专享价格   app  jx
				'rank_integral' => -1
		);
		
		if ($code != '')
		{
			$goods['goods_number'] = 0;
		}
		
		/* 关联商品 */
		$link_goods_list = array();
		$sql = "DELETE FROM " . $ecs->table('link_goods') .
		" WHERE (goods_id = 0 OR link_goods_id = 0)" .
		" AND admin_id = '$_SESSION[admin_id]'";
		$db->query($sql);
		
		/* 组合商品 */
		$group_goods_list = array();
		$sql = "DELETE FROM " . $ecs->table('group_goods') .
		" WHERE parent_id = 0 AND admin_id = '$_SESSION[admin_id]'";
		$db->query($sql);
		
		/* 关联文章 */
		$goods_article_list = array();
		$sql = "DELETE FROM " . $ecs->table('goods_article') .
		" WHERE goods_id = 0 AND admin_id = '$_SESSION[admin_id]'";
		$db->query($sql);
		
		/* 属性 */
		$sql = "DELETE FROM " . $ecs->table('goods_attr') . " WHERE goods_id = 0";
		$db->query($sql);
		
		/* 图片列表 */
		$img_list = array();
	
 
		$cats_old_zhyh =array();
		$smarty->assign('is_add_zhyh', 1);
	
		 
	$cate=array();
	$sqlc = "select cat_id, parent_id, cat_name from ". $ecs->table('supplier_category') ." where supplier_id='". $_SESSION['supplier_id'] ."' ";
	$resc = $db->query($sqlc);
	while ($rowc = $db->fetchRow($resc))
	{
		$cate[$rowc['cat_id']] =array(
				'id' => $rowc['cat_id'],
				'pid' => $rowc['parent_id'],
				'name' => $rowc['cat_name']
		);
	}
	get_tree(0,$cate,0, $cats_old_zhyh);
	global $catstr;
	$smarty->assign('catstr',$catstr);
	
	/* 拆分商品名称样式 */
	$goods_name_style = explode('+', empty($goods['goods_name_style']) ? '+' : $goods['goods_name_style']);
	
	/* 创建 html editor */
	create_html_editor('goods_desc', htmlspecialchars($goods['goods_desc'])); /* 修改 by www.68ecshop.com 百度编辑器 */
	
	/* 模板赋值 */
	$action_link_supplier = $is_add ? array('href' => 'supplier_m_goods.php?act=list&supplier_status=0' , 'text' => '返回商品列表'): array('href' => 'supplier_m_goods.php?act=list&supplier_status='.$_REQUEST['supplier_status'] , 'text' => '返回商品列表');
	$smarty->assign('code',    $code);
	$smarty->assign('ur_here', $is_add ? (empty($code) ? $_LANG['03_goods_add'] : $_LANG['51_virtual_card_add']) : ($_REQUEST['act'] == 'edit' ? $_LANG['edit_goods'] : $_LANG['copy_goods']));
	$smarty->assign('action_link', $action_link_supplier);
	$smarty->assign('goods', $goods);
	$smarty->assign('goods_name_color', $goods_name_style[0]);
	$smarty->assign('goods_name_style', $goods_name_style[1]);
	$smarty->assign('area_cat_list', area_cat_list(0, $goods['area_cat_id'],true,0,true,2));//UEUCS_364642382
	$smarty->assign('cat_list', cat_list(0, $goods['cat_id']));
	// 代码修改_start_derek20150129admin_goods  www.68ecshop.com
	$smarty->assign('goods_cat_id', $goods['cat_id']);
	
	$smarty->assign('brand_list_for_select', get_brand_list());
	$brand_list = get_brand_list(true);
	$smarty->assign('brand_list', $brand_list);
	 
	// 代码修改_start_derek20150129admin_goods  www.68ecshop.com
	$smarty->assign('unit_list', get_unit_list());
	$smarty->assign('user_rank_list', get_user_rank_list());
	$smarty->assign('weight_unit', $is_add ? '1' : ($goods['goods_weight'] >= 1 ? '1' : '0.001'));
	$smarty->assign('cfg', $_CFG);
	$smarty->assign('form_act', $is_add ? 'insert' : ($_REQUEST['act'] == 'edit' ? 'update' : 'insert'));
	$smarty->assign('is_add', true);
 
	$smarty->assign('link_goods_list', $link_goods_list);
	$smarty->assign('group_goods_list', $group_goods_list);
	$smarty->assign('goods_article_list', $goods_article_list);
	$smarty->assign('img_list', $img_list);
	$smarty->assign('goods_type_list', goods_type_list($goods['goods_type']));
	$smarty->assign('gd', gd_version());
	$smarty->assign('thumb_width', $_CFG['thumb_width']);
	$smarty->assign('thumb_height', $_CFG['thumb_height']);
	$smarty->assign('goods_attr_html', build_attr_html($goods['goods_type'], $goods['goods_id'],$bar_code));
	$volume_price_list = '';
	if(isset($_REQUEST['goods_id']))
	{
		$volume_price_list = get_volume_price_list($_REQUEST['goods_id']);
	}
	if (empty($volume_price_list))
	{
		$volume_price_list = array('0'=>array('number'=>'','price'=>''));
	}
	$smarty->assign('volume_price_list', $volume_price_list);
	if (isset($_CFG['supplier_addbest']))
	{
		$smarty->assign('is_addbest', $_CFG['supplier_addbest']);
	}
 
	
	$smarty->display('supplier/goods_info.dwt');
	
}


function action_show_image(){
	
	
	if (isset($GLOBALS['shop_id']) && $GLOBALS['shop_id'] > 0)
	{
		$img_url = $_GET['img_url'];
	}
	else
	{
		if (strpos($_GET['img_url'], 'http://') === 0)
		{
			$img_url = $_GET['img_url'];
		}
		else
		{
			$img_url = '../' . $_GET['img_url'];
		}
	}
	$smarty->assign('img_url', $img_url);
	$smarty->display('supplier/goods_show_image.dwt');
	
}

function getchild($pid,$arr){
	$sa = $newarr = array();
	if(is_array($arr)){
		foreach($arr as $id => $sa){
			if($sa['pid'] == $pid) $newarr[$id]=$sa;
		}
	}
	return $newarr ? $newarr :array();
}


/*
 同步购物车中的商品价格
 */
function tongbu_cart_price($goods_id){
	global $db,$ecs;
	
	$sql = "select c.rec_id,c.goods_id,c.goods_attr_id,c.user_id,c.session_id,g.market_price from ".$ecs->table('cart')." as c left join ".$ecs->table('goods')." as g on c.goods_id=g.goods_id where c.goods_id=".$goods_id." and c.rec_type='".CART_GENERAL_GOODS."' AND c.extension_code <> 'package_buy'";
	$query = $db->query($sql);
	while($row = $db->fetchRow($query)){
		
		if($row['user_id']>0){
			//已经有用户的商品
			
			$sql1 = "select u.user_rank,IFNULL(ur.discount,100) as discount from ".$ecs->table('users')." as u left join ".$ecs->table('user_rank')." as ur on u.user_rank=ur.rank_id where u.user_id=".$row['user_id'];
			
			$data = $db->getRow($sql1);
			$GLOBALS['tongbu_user_discount'] = ($data['discount']/100) > 0 ? ($data['discount']/100) : 1  ;
			$GLOBALS['tongbu_user_rank'] = $data['user_rank'];
		}else{
			$GLOBALS['tongbu_user_discount'] = 1;
			$GLOBALS['tongbu_user_rank'] = 1;
		}
		$attr_id    = empty($row['goods_attr_id']) ? array() : explode(',', $row['goods_attr_id']);
		$price = get_final_price($row['goods_id'],1,true,$attr_id);
		$db->query("update ".$ecs->table('cart')." set market_price='".$row['market_price']."',goods_price='".$price."' where rec_id=".$row['rec_id']);
	}
}

function get_tree($pid,$arr,$num, $cats_old_zhyh){
	global $catstr;
	$child = getchild($pid,$arr);
	
	if (is_array($child)){
		
		$total = count($child);
		foreach($child as $id => $sa){
			$pstr = '';
			for($i = 0; $i <= $num; $i ++){
				$pstr = $pstr . ($num ? '&nbsp;&nbsp;&nbsp;&nbsp;' : '&nbsp;&nbsp;&nbsp;');
			}
			$children =array();
			$zhyhcount=0;
			$children = getchild($id,$arr);
			$zhyhcount = count($children);
			//			if( $zhyhcount == 0 )
				//			{
			if (@in_array($id, $cats_old_zhyh))
			{
				$selected_zhyh=" checked";
			}
			else
			{
				$selected_zhyh=" ";
			}
			$zhyh = '<input type="checkbox" class="nfl" name="supplier_cat_id[]" id="supplier_cat_id" value="'. $id .'" '. $selected_zhyh .'>';
			//			}
			//			else
				//			{
				//				$zhyh='';
				//			}
			$catstr =   $catstr . $pstr . $zhyh. $sa['name'] ."  <br>";
			
			$num++;
			get_tree($sa['id'],$arr,$num, $cats_old_zhyh);
			$num--;
		}
	}else{return;}
}


/**
 * 商品列表
 */
function action_list() {
	
	
	$smarty = $GLOBALS ['smarty'];
	$ecs= $GLOBALS['ecs'];
	$db= $GLOBALS['db'];
	 
	getSupplierIdMF();//为了在session中放置supplier_id
	if(!$_SESSION['supplier_id']) {
		sys_msg("您还没有店铺");//不是商家
		die;
	}
	
	include_once(ROOT_PATH . '/includes/cls_image.php');
	include_once(getShopRoot() . 'members/includes/cls_exchange.php');//调用会员中心的类
	$image = new cls_image($_CFG['bgcolor']);	
	$exc = new exchange($ecs->table('goods'), $db, 'goods_id', 'goods_name');
	$smarty = $GLOBALS ['smarty'];

// 	admin_priv('goods_trash');
//     require_once(ROOT_PATH . '/' . ADMIN_PATH . '/includes/inc_menu.php');
    $cat_id = empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);
    $code   = empty($_REQUEST['extension_code']) ? '' : trim($_REQUEST['extension_code']);
    $suppliers_id = isset($_REQUEST['suppliers_id']) ? (empty($_REQUEST['suppliers_id']) ? '' : trim($_REQUEST['suppliers_id'])) : '';
    $is_on_sale = isset($_REQUEST['is_on_sale']) ? ((empty($_REQUEST['is_on_sale']) && $_REQUEST['is_on_sale'] === 0) ? '' : trim($_REQUEST['is_on_sale'])) : '';

    $handler_list = array();
    $handler_list['virtual_card'][] = array('url'=>'virtual_card.php?act=card', 'title'=>$_LANG['card'], 'img'=>'icon_send_bonus.gif');
    $handler_list['virtual_card'][] = array('url'=>'virtual_card.php?act=replenish', 'title'=>$_LANG['replenish'], 'img'=>'icon_add.gif');
    $handler_list['virtual_card'][] = array('url'=>'virtual_card.php?act=batch_card_add', 'title'=>$_LANG['batch_card_add'], 'img'=>'icon_output.gif');

    if ($_REQUEST['act'] == 'list' && isset($handler_list[$code]))
    {
        $smarty->assign('add_handler',      $handler_list[$code]);
    }
  
    /* 供货商名 */
    $suppliers_list_name = suppliers_list_name(); 
    $suppliers_exists = 1;
    if (empty($suppliers_list_name))
    {
        $suppliers_exists = 0;
    }
    $smarty->assign('is_on_sale', $is_on_sale);
    $smarty->assign('suppliers_id', $suppliers_id);
    $smarty->assign('suppliers_exists', $suppliers_exists);
    $smarty->assign('suppliers_list_name', $suppliers_list_name);
    unset($suppliers_list_name, $suppliers_exists);
   
    /* 模板赋值 */
    $goods_ur = array('' => $_LANG['01_goods_list'], 'virtual_card'=>$_LANG['50_virtual_card_list']);
	if ($_REQUEST['act'] == 'list' && $_REQUEST['supplier_status']=='1')
	{
		$ur_here = $_LANG['01_goods_list_pass1'];
	}
	elseif ($_REQUEST['act'] == 'list' && $_REQUEST['supplier_status']=='0')
	{
		$ur_here = $_LANG['01_goods_list_pass2'];
	}
	elseif ($_REQUEST['act'] == 'list' && $_REQUEST['supplier_status']=='-1')
	{
		$ur_here = $_LANG['01_goods_list_pass3'];
	}
	elseif ($_REQUEST['act'] == 'list' && $_REQUEST['supplier_status']=='')
	{
		$ur_here = $_LANG['01_goods_list'];
	}
	else
	{
		$ur_here = $_LANG['11_goods_trash'];
	}
	
	if (isset($_CFG['supplier_editgoods']))
	{
		$smarty->assign('is_editgoods', $_CFG['supplier_editgoods']);
	}
 
    $smarty->assign('ur_here', $ur_here);
	$smarty->assign('supplier_status', $_REQUEST['supplier_status']);
    $action_link = ($_REQUEST['act'] == 'list') ? add_link($code) : array('href' => 'supplier_m_goods.php?act=list', 'text' => $_LANG['01_goods_list']);
    $smarty->assign('action_link',  $action_link);
    $smarty->assign('code',     $code);  
  //  $smarty->assign('cat_list',     cat_list(0, $cat_id));
    //$smarty->assign('cat_list',     cat_list_2(0, $cat_id));
    //     die(ROOT_PATH . 'includes/lib_supplier_common.php');    
   
    include_once(ROOT_PATH . '/includes/lib_supplier_common.php'); 
  
   
    $smarty->assign('cat_list',     cat_list_supplier(0, $cat_id)); 
    $brand_list = get_brand_list();
    $smarty->assign('brand_list', $brand_list);

    $smarty->assign('intro_list',   get_intro_list());
    $smarty->assign('lang',         $_LANG);
    $smarty->assign('list_type',    $_REQUEST['act'] == 'list' ? 'goods' : 'trash');
    $smarty->assign('use_storage',  empty($_CFG['use_storage']) ? 0 : 1);
    
    $suppliers_list = suppliers_list_info(' is_check = 1 ');
    $suppliers_list_count = count($suppliers_list);
    $smarty->assign('suppliers_list', ($suppliers_list_count == 0 ? 0 : $suppliers_list)); // 取供货商列表
    
    $goods_list = goods_list($_REQUEST['act'] == 'list' ? 0 : 1, ($_REQUEST['act'] == 'list') ? (($code == '') ? 1 : 0) : -1);
    $smarty->assign('goods_list',   $goods_list['goods']);
    $smarty->assign('filter',       $goods_list['filter']);
    $smarty->assign('record_count', $goods_list['record_count']);
    $smarty->assign('page_count',   $goods_list['page_count']);
    $smarty->assign('full_page',    1);
    $pager = get_pager('supplier_m_goods.php', array('act' => 'list'), $goods_list['record_count'], $goods_list['filter']['page'], $goods_list['filter']['page_size']);
    $smarty->assign ( 'pager', $pager);
    
    
    /* 排序标记 */
    $sort_flag  = sort_flag($goods_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);
    /* 获取商品类型存在规格的类型 */
    $specifications = get_goods_type_specifications();  
    $smarty->assign('specifications', $specifications);
    if($_REQUEST['act'] == 'trash'){
    	$smarty->assign('is_goods_trash', '1');
    }
    if (isset($_CFG['supplier_addbest']))
    {
        $smarty->assign('is_addbest', $_CFG['supplier_addbest']);
    }
  
    /* 显示商品列表页面 */
//     $htm_file = ($_REQUEST['act'] == 'list') ?
//         'supplier/goods_list.dwt' : (($_REQUEST['act'] == 'trash') ? 'goods_trash.htm' : 'group_list.htm');
    $smarty->display('supplier/goods_list.dwt');

	
}
function  action_trash(){
	
	
	
	$smarty = $GLOBALS ['smarty'];
	$ecs= $GLOBALS['ecs'];
	$db= $GLOBALS['db'];
	
	getSupplierIdMF();//为了在session中放置supplier_id
	if(!$_SESSION['supplier_id']) {
		sys_msg("您还没有店铺");//不是商家
		die;
	}
	
	include_once(ROOT_PATH . '/includes/cls_image.php');
	include_once(getShopRoot() . 'members/includes/cls_exchange.php');//调用会员中心的类
	$image = new cls_image($_CFG['bgcolor']);
	$exc = new exchange($ecs->table('goods'), $db, 'goods_id', 'goods_name');
	$smarty = $GLOBALS ['smarty'];
	
	// 	admin_priv('goods_trash');
	//     require_once(ROOT_PATH . '/' . ADMIN_PATH . '/includes/inc_menu.php');
	$cat_id = empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);
	$code   = empty($_REQUEST['extension_code']) ? '' : trim($_REQUEST['extension_code']);
	$suppliers_id = isset($_REQUEST['suppliers_id']) ? (empty($_REQUEST['suppliers_id']) ? '' : trim($_REQUEST['suppliers_id'])) : '';
	$is_on_sale = isset($_REQUEST['is_on_sale']) ? ((empty($_REQUEST['is_on_sale']) && $_REQUEST['is_on_sale'] === 0) ? '' : trim($_REQUEST['is_on_sale'])) : '';
	
	$handler_list = array();
	$handler_list['virtual_card'][] = array('url'=>'virtual_card.php?act=card', 'title'=>$_LANG['card'], 'img'=>'icon_send_bonus.gif');
	$handler_list['virtual_card'][] = array('url'=>'virtual_card.php?act=replenish', 'title'=>$_LANG['replenish'], 'img'=>'icon_add.gif');
	$handler_list['virtual_card'][] = array('url'=>'virtual_card.php?act=batch_card_add', 'title'=>$_LANG['batch_card_add'], 'img'=>'icon_output.gif');
	
	if ($_REQUEST['act'] == 'list' && isset($handler_list[$code]))
	{
		$smarty->assign('add_handler',      $handler_list[$code]);
	}
	
	/* 供货商名 */
	$suppliers_list_name = suppliers_list_name();
	$suppliers_exists = 1;
	if (empty($suppliers_list_name))
	{
		$suppliers_exists = 0;
	}
	$smarty->assign('is_on_sale', $is_on_sale);
	$smarty->assign('suppliers_id', $suppliers_id);
	$smarty->assign('suppliers_exists', $suppliers_exists);
	$smarty->assign('suppliers_list_name', $suppliers_list_name);
	unset($suppliers_list_name, $suppliers_exists);
	
	/* 模板赋值 */
	$goods_ur = array('' => $_LANG['01_goods_list'], 'virtual_card'=>$_LANG['50_virtual_card_list']);
	if ($_REQUEST['act'] == 'list' && $_REQUEST['supplier_status']=='1')
	{
		$ur_here = $_LANG['01_goods_list_pass1'];
	}
	elseif ($_REQUEST['act'] == 'list' && $_REQUEST['supplier_status']=='0')
	{
		$ur_here = $_LANG['01_goods_list_pass2'];
	}
	elseif ($_REQUEST['act'] == 'list' && $_REQUEST['supplier_status']=='-1')
	{
		$ur_here = $_LANG['01_goods_list_pass3'];
	}
	elseif ($_REQUEST['act'] == 'list' && $_REQUEST['supplier_status']=='')
	{
		$ur_here = $_LANG['01_goods_list'];
	}
	else
	{
		$ur_here = $_LANG['11_goods_trash'];
	}
	
	if (isset($_CFG['supplier_editgoods']))
	{
		$smarty->assign('is_editgoods', $_CFG['supplier_editgoods']);
	}
	
	$smarty->assign('ur_here', $ur_here);
	$smarty->assign('supplier_status', $_REQUEST['supplier_status']);
	$action_link = ($_REQUEST['act'] == 'list') ? add_link($code) : array('href' => 'supplier_m_goods.php?act=list', 'text' => $_LANG['01_goods_list']);
	$smarty->assign('action_link',  $action_link);
	$smarty->assign('code',     $code);
	//  $smarty->assign('cat_list',     cat_list(0, $cat_id));
	//$smarty->assign('cat_list',     cat_list_2(0, $cat_id));
	//     die(ROOT_PATH . 'includes/lib_supplier_common.php');
	
	include_once(ROOT_PATH . '/includes/lib_supplier_common.php');
	
	
	$smarty->assign('cat_list',     cat_list_supplier(0, $cat_id));
	$brand_list = get_brand_list();
	$smarty->assign('brand_list', $brand_list);
	
	$smarty->assign('intro_list',   get_intro_list());
	$smarty->assign('lang',         $_LANG);
	$smarty->assign('list_type',    $_REQUEST['act'] == 'list' ? 'goods' : 'trash');
	$smarty->assign('use_storage',  empty($_CFG['use_storage']) ? 0 : 1);
	
	$suppliers_list = suppliers_list_info(' is_check = 1 ');
	$suppliers_list_count = count($suppliers_list);
	$smarty->assign('suppliers_list', ($suppliers_list_count == 0 ? 0 : $suppliers_list)); // 取供货商列表
	
	$goods_list = goods_list($_REQUEST['act'] == 'list' ? 0 : 1, ($_REQUEST['act'] == 'list') ? (($code == '') ? 1 : 0) : -1);
	$smarty->assign('goods_list',   $goods_list['goods']);
	$smarty->assign('filter',       $goods_list['filter']);
	$smarty->assign('record_count', $goods_list['record_count']);
	$smarty->assign('page_count',   $goods_list['page_count']);
	$smarty->assign('full_page',    1);
	$pager = get_pager('supplier_m_goods.php', array('act' => 'list'), $goods_list['record_count'], $goods_list['filter']['page'], $goods_list['filter']['page_size']);
	$smarty->assign ( 'pager', $pager);
	
	
	/* 排序标记 */
	$sort_flag  = sort_flag($goods_list['filter']);
	$smarty->assign($sort_flag['tag'], $sort_flag['img']);
	/* 获取商品类型存在规格的类型 */
	$specifications = get_goods_type_specifications();
	$smarty->assign('specifications', $specifications);
	if($_REQUEST['act'] == 'trash'){
		$smarty->assign('is_goods_trash', '1');
	}
	if (isset($_CFG['supplier_addbest']))
	{
		$smarty->assign('is_addbest', $_CFG['supplier_addbest']);
	}
	
	/* 显示商品列表页面 */
	//     $htm_file = ($_REQUEST['act'] == 'list') ?
	//         'supplier/goods_list.dwt' : (($_REQUEST['act'] == 'trash') ? 'goods_trash.htm' : 'group_list.htm');
	$smarty->display('supplier/goods_trash.dwt');
	
}

/*------------------------------------------------------ */
//-- 还原回收站中的商品
/*------------------------------------------------------ */
function action_restore_goods(){
	$smarty = $GLOBALS ['smarty'];
	$ecs= $GLOBALS['ecs'];
	$db= $GLOBALS['db'];
	include_once(getShopRoot() . 'members/includes/cls_exchange.php');//调用会员中心的类
	$exc = new exchange($ecs->table('goods'), $db, 'goods_id', 'goods_name');
	
	$goods_id = intval($_REQUEST['id']);
	
	$exc->edit("is_delete = 0, add_time = '" . gmtime() . "'", $goods_id);
	clear_cache_files();
	$goods_name = $exc->get_name($goods_id);
	admin_log(addslashes($goods_name), 'restore', 'goods'); // 记录日志
	$url = 'supplier_m_goods.php?act=query&' . str_replace('act=restore_goods', '', $_SERVER['QUERY_STRING']);
	ecs_header("Location: $url\n");
	exit;
}

/*------------------------------------------------------ */
//-- 彻底删除商品
/*------------------------------------------------------ */
function  action_drop_goods(){
	
	$smarty = $GLOBALS ['smarty'];
	$ecs= $GLOBALS['ecs'];
	$db= $GLOBALS['db'];
	include_once(getShopRoot() . 'members/includes/cls_exchange.php');//调用会员中心的类
	$exc = new exchange($ecs->table('goods'), $db, 'goods_id', 'goods_name');
	
	// 取得参数
	$goods_id = intval($_REQUEST['id']);
	if ($goods_id <= 0)
	{
		make_json_error('invalid params');
	}
	
	/* 取得商品信息 */
	$sql = "SELECT goods_id, goods_name, is_delete, is_real, goods_thumb, " .
			"goods_img, original_img ,supplier_id" .
			" FROM " . $ecs->table('goods') .
			" WHERE goods_id = '$goods_id'";
	$goods = $db->getRow($sql);
	$supplier_id = $goods['supplier_id'];
	$sql = "DELETE FROM " . $ecs->table('supplier_goods_cat') . " WHERE goods_id = '$goods_id' and supplier_id = '$supplier_id'";
	$db->query($sql);
	if (empty($goods))
	{
		make_json_error($_LANG['goods_not_exist']);
	}
	
	if ($goods['is_delete'] != 1)
	{
		make_json_error($_LANG['goods_not_in_recycle_bin']);
	}
	
	/* 删除商品图片和轮播图片 */
	if (!empty($goods['goods_thumb']))
	{
		@unlink('../' . $goods['goods_thumb']);
	}
	if (!empty($goods['goods_img']))
	{
		@unlink('../' . $goods['goods_img']);
	}
	if (!empty($goods['original_img']))
	{
		@unlink('../' . $goods['original_img']);
	}
	/* 删除商品 */
	$exc->drop($goods_id);
	
	/* 删除商品的货品记录 */
	$sql = "DELETE FROM " . $ecs->table('products') .
	" WHERE goods_id = '$goods_id'";
	$db->query($sql);
	
	/* 记录日志 */
	admin_log(addslashes($goods['goods_name']), 'remove', 'goods');
	
	/* 删除商品相册 */
	$sql = "SELECT img_url, thumb_url, img_original " .
			"FROM " . $ecs->table('goods_gallery') .
			" WHERE goods_id = '$goods_id'";
	$res = $db->query($sql);
	while ($row = $db->fetchRow($res))
	{
		if (!empty($row['img_url']))
		{
			@unlink('../' . $row['img_url']);
		}
		if (!empty($row['thumb_url']))
		{
			@unlink('../' . $row['thumb_url']);
		}
		if (!empty($row['img_original']))
		{
			@unlink('../' . $row['img_original']);
		}
	}
	
	$sql = "DELETE FROM " . $ecs->table('goods_gallery') . " WHERE goods_id = '$goods_id'";
	$db->query($sql);
	
	/* 删除相关表记录 */
	$sql = "DELETE FROM " . $ecs->table('collect_goods') . " WHERE goods_id = '$goods_id'";
	$db->query($sql);
	$sql = "DELETE FROM " . $ecs->table('goods_article') . " WHERE goods_id = '$goods_id'";
	$db->query($sql);
	$sql = "DELETE FROM " . $ecs->table('goods_attr') . " WHERE goods_id = '$goods_id'";
	$db->query($sql);
	$sql = "DELETE FROM " . $ecs->table('goods_cat') . " WHERE goods_id = '$goods_id'";
	$db->query($sql);
	$sql = "DELETE FROM " . $ecs->table('member_price') . " WHERE goods_id = '$goods_id'";
	$db->query($sql);
	$sql = "DELETE FROM " . $ecs->table('group_goods') . " WHERE parent_id = '$goods_id'";
	$db->query($sql);
	$sql = "DELETE FROM " . $ecs->table('group_goods') . " WHERE goods_id = '$goods_id'";
	$db->query($sql);
	$sql = "DELETE FROM " . $ecs->table('link_goods') . " WHERE goods_id = '$goods_id'";
	$db->query($sql);
	$sql = "DELETE FROM " . $ecs->table('link_goods') . " WHERE link_goods_id = '$goods_id'";
	$db->query($sql);
	$sql = "DELETE FROM " . $ecs->table('tag') . " WHERE goods_id = '$goods_id'";
	$db->query($sql);
	$sql = "DELETE FROM " . $ecs->table('comment') . " WHERE comment_type = 0 AND id_value = '$goods_id'";
	$db->query($sql);
	$sql = "DELETE FROM " . $ecs->table('collect_goods') . " WHERE goods_id = '$goods_id'";
	$db->query($sql);
	$sql = "DELETE FROM " . $ecs->table('booking_goods') . " WHERE goods_id = '$goods_id'";
	$db->query($sql);
	$sql = "DELETE FROM " . $ecs->table('goods_activity') . " WHERE goods_id = '$goods_id'";
	$db->query($sql);
	
	/* 如果不是实体商品，删除相应虚拟商品记录 */
	if ($goods['is_real'] != 1)
	{
		$sql = "DELETE FROM " . $ecs->table('virtual_card') . " WHERE goods_id = '$goods_id'";
		if (!$db->query($sql, 'SILENT') && $db->errno() != 1146)
		{
			die($db->error());
		}
	}
	
	clear_cache_files();
	$url = 'supplier_m_goods.php?act=query&' . str_replace('act=drop_goods', '', $_SERVER['QUERY_STRING']);
	
	ecs_header("Location: $url\n");
	
	exit;
	
}
function  action_toggle_on_sale()
{
	$smarty = $GLOBALS ['smarty'];
	$ecs= $GLOBALS['ecs'];
	$db= $GLOBALS['db'];
	include_once(getShopRoot() . 'members/includes/cls_exchange.php');//调用会员中心的类
	$exc = new exchange($ecs->table('goods'), $db, 'goods_id', 'goods_name');
	$goods_id       = intval($_POST['id']);
	$on_sale        = intval($_POST['val']);
	
	$sql="select supplier_id,supplier_status from ". $ecs->table('goods') ." where goods_id='$goods_id' ";
	$supplier_row =$db->getRow($sql);
	if ($supplier_row['supplier_id']>0 && $supplier_row['supplier_status'] <=0 )
	{
		make_json_error('对不起，该商品还未审核通过！不能上架！');
	}
	
	if ($exc->edit("is_on_sale = '$on_sale', last_update=" .gmtime(), $goods_id))
	{
		clear_cache_files();
		make_json_result($on_sale);
	}
}




/**
 * 保存某商品的优惠价格
 * @param   int     $goods_id    商品编号
 * @param   array   $number_list 优惠数量列表
 * @param   array   $price_list  价格列表
 * @return  void
 */
function handle_volume_price($goods_id, $number_list, $price_list)
{
	$sql = "DELETE FROM " . $GLOBALS['ecs']->table('volume_price') .
	" WHERE price_type = '1' AND goods_id = '$goods_id'";
	$GLOBALS['db']->query($sql);
	
	
	/* 循环处理每个优惠价格 */
	foreach ($price_list AS $key => $price)
	{
		/* 价格对应的数量上下限 */
		$volume_number = $number_list[$key];
		
		if (!empty($price))
		{
			$sql = "INSERT INTO " . $GLOBALS['ecs']->table('volume_price') .
			" (price_type, goods_id, volume_number, volume_price) " .
			"VALUES ('1', '$goods_id', '$volume_number', '$price')";
			$GLOBALS['db']->query($sql);
		}
	}
}


/**
 * 添加链接
 * @param   string  $extension_code 虚拟商品扩展代码，实体商品为空
 * @return  array('href' => $href, 'text' => $text)
 */
function add_link($extension_code = '')
{
	$href = 'supplier_m_goods.php?act=add';
	if (!empty($extension_code))
	{
		$href .= '&extension_code=' . $extension_code;
	}
	
	if ($extension_code == 'virtual_card')
	{
		$text = "添加虚拟商品";
	}
	else
	{
		$text = "添加新商品";
	}
	
	return array('href' => $href, 'text' => $text);
}

function handle_other_cat2($goods_id, $cat_list)
{
	/* 查询现有的扩展分类 */
	$sql = "SELECT cat_id FROM " . $GLOBALS['ecs']->table('supplier_goods_cat') .
	" WHERE goods_id = '$goods_id' and supplier_id= '$_SESSION[supplier_id]' ";
	$exist_list = $GLOBALS['db']->getCol($sql);
	
	/* 删除不再有的分类 */
	$delete_list = array_diff($exist_list, $cat_list);
	if ($delete_list)
	{
		$sql = "DELETE FROM " . $GLOBALS['ecs']->table('supplier_goods_cat') .
		" WHERE goods_id = '$goods_id' AND supplier_id= '$_SESSION[supplier_id]' " .
		"AND cat_id " . db_create_in($delete_list);
		$GLOBALS['db']->query($sql);
	}
	
	/* 添加新加的分类 */
	$add_list = array_diff($cat_list, $exist_list, array(0));
	foreach ($add_list AS $cat_id)
	{
		// 插入记录
		$sql = "INSERT INTO " . $GLOBALS['ecs']->table('supplier_goods_cat') .
		" (goods_id, cat_id, supplier_id) " .
		"VALUES ('$goods_id', '$cat_id', '$_SESSION[supplier_id]')";
		$GLOBALS['db']->query($sql);
	}
}
?>