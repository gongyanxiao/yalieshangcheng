<?php

/**
 * 代码生成
   	
 */

define ( 'IN_ECS', true );

require (dirname ( __FILE__ ) . '/includes/init.php');
require_once (ROOT_PATH . '/' . ADMIN_PATH . '/includes/lib_goods.php');
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
	$goods_id = $_REQUEST ['id'];
	
	$smarty->assign ( 'image_width', $_CFG ['image_width'] );
	$smarty->assign ( 'image_height', $_CFG ['image_height'] );
	$smarty->assign ( 'id', $goods_id );
	$smarty->assign ( 'type', 0 );
	$smarty->assign ( 'cfg', $_CFG );
 
	$smarty->assign ( 'shop_country', $_CFG ['shop_country'] );
	$sql = 'select region_id, region_name from ' . $ecs->table ( 'region' ) . ' where parent_id=' . $_CFG ['shop_country'];
	$country_list = $GLOBALS ['db']->getAll ( $sql );
	$smarty->assign ( 'country_list', $country_list );
	$city_id = $country_list [0] ['region_id'];
	$smarty->assign ( 'city_id', $city_id );
	$district_id = $db->getOne ( 'select region_id from ' . $ecs->table ( 'region' ) . ' where parent_id=' . $city_id );
	$smarty->assign ( 'district_id', $district_id );
 
	
	$pups = $db->getOne ( 'select * from ' . $ecs->table ( 'shipping' ) . ' where shipping_code="pups"' );
	$smarty->assign ( 'pups', $pups );
	
	$suppid = intval ( $_REQUEST ['suppid'] );
	$ppts = $GLOBALS ['db']->getOne ( 'SELECT COUNT(*) FROM ' . $GLOBALS ['ecs']->table ( 'pickup_point' ) . ' WHERE supplier_id = ' . $suppid );
	$smarty->assign ( 'ppts', $ppts );
	/* 获得商品的信息 */
	$goods = get_goods_info ( $goods_id );
	
	if ($goods === false) {
		/* 如果没有找到任何记录则跳回到首页 */
		ecs_header ( "Location: ./\n" );
		exit ();
	}
	
	if ($goods ['brand_id'] > 0) {
		$goods ['goods_brand_url'] = build_uri ( 'brand', array (
				'bid' => $goods ['brand_id']
		), $goods ['goods_brand'] );
	}
	
	/* 代码增加_start By www.68ecshop.com */
	$goods ['supplier_name'] = "网站自营";
	
	$shop_price = $goods ['shop_price'];
	
	$smarty->assign ( 'url', $_SERVER ["REQUEST_URI"] );
	$smarty->assign ( 'goods', $goods );
	$smarty->assign ( 'ur_here', $position ['ur_here'] ); // 当前位置
// 		var_dump($goods);
	$smarty->display ( 'goods.html' );
	
}


 
function action_list(){
	
	global $smarty;
	global $db ,$ecs,$_CFG,$_LANG;
	
	admin_priv ( 'tk_chan_pin_list' );
	require_once (ROOT_PATH . '/' . ADMIN_PATH . '/includes/inc_menu.php');
	$cat_id = empty ( $_REQUEST ['cat_id'] ) ? 0 : intval ( $_REQUEST ['cat_id'] );
	$code = empty ( $_REQUEST ['extension_code'] ) ? '' : trim ( $_REQUEST ['extension_code'] );
	$suppliers_id = isset ( $_REQUEST ['suppliers_id'] ) ? (empty ( $_REQUEST ['suppliers_id'] ) ? '' : trim ( $_REQUEST ['suppliers_id'] )) : '';
	$is_on_sale = isset ( $_REQUEST ['is_on_sale'] ) ? ((empty ( $_REQUEST ['is_on_sale'] ) && $_REQUEST ['is_on_sale'] === 0) ? '' : trim ( $_REQUEST ['is_on_sale'] )) : '';
	
	$handler_list = array ();
	$handler_list ['virtual_card'] [] = array (
			'url' => 'virtual_card.php?act=card',
			'title' => $_LANG ['card'],
			'img' => 'icon_send_bonus.gif'
	);
	$handler_list ['virtual_card'] [] = array (
			'url' => 'virtual_card.php?act=replenish',
			'title' => $_LANG ['replenish'],
			'img' => 'icon_add.gif'
	);
	$handler_list ['virtual_card'] [] = array (
			'url' => 'virtual_card.php?act=batch_card_add',
			'title' => $_LANG ['batch_card_add'],
			'img' => 'icon_output.gif'
	);
	
	if ($_REQUEST ['act'] == 'list' && isset ( $handler_list [$code] )) {
		$smarty->assign ( 'add_handler', $handler_list [$code] );
	}
	
	/* 供货商名 */
	$suppliers_list_name = suppliers_list_name ();
	$suppliers_exists = 1;
	if (empty ( $suppliers_list_name )) {
		$suppliers_exists = 0;
	}
	$smarty->assign ( 'is_on_sale', $is_on_sale );
	$smarty->assign ( 'suppliers_id', $suppliers_id );
	$smarty->assign ( 'suppliers_exists', $suppliers_exists );
	$smarty->assign ( 'suppliers_list_name', $suppliers_list_name );
	unset ( $suppliers_list_name, $suppliers_exists );
	
	/* 模板赋值 */
	$goods_ur = array (
			'' => $_LANG ['01_goods_list'],
			'virtual_card' => $_LANG ['50_virtual_card_list']
	);
	
	if (isset ( $_CFG ['supplier_editgoods'] )) {
		$smarty->assign ( 'is_editgoods', $_CFG ['supplier_editgoods'] );
	}
	
	$smarty->assign ( 'ur_here', "产品列表" );
	$action_link = ($_REQUEST ['act'] == 'list') ? add_link ( $code ) : array (
			'href' => 'goods.php?act=list',
			'text' => $_LANG ['01_goods_list']
	);
	$smarty->assign ( 'action_link', $action_link );
	$smarty->assign ( 'code', $code );
	$smarty->assign ( 'cat_list', cat_list_supplier ( 0, $cat_id ) );
	$smarty->assign ( 'brand_list', get_brand_list () );
	
	$smarty->assign ( 'intro_list', get_intro_list () );
	$smarty->assign ( 'lang', $_LANG );
	$smarty->assign ( 'list_type', $_REQUEST ['act'] == 'list' ? 'goods' : 'trash' );
	$smarty->assign ( 'use_storage', empty ( $_CFG ['use_storage'] ) ? 0 : 1 );
	
	$suppliers_list = suppliers_list_info ( ' is_check = 1 ' );
	$suppliers_list_count = count ( $suppliers_list );
	$smarty->assign ( 'suppliers_list', ($suppliers_list_count == 0 ? 0 : $suppliers_list) ); // 取供货商列表
	
	$goods_list = goods_list ( $_REQUEST ['act'] == 'list' ? 0 : 1, ($_REQUEST ['act'] == 'list') ? (($code == '') ? 1 : 0) : - 1 );
	$smarty->assign ( 'goods_list', $goods_list ['goods'] );
	$smarty->assign ( 'filter', $goods_list ['filter'] );
	$smarty->assign ( 'record_count', $goods_list ['record_count'] );
	$smarty->assign ( 'page_count', $goods_list ['page_count'] );
	$smarty->assign ( 'full_page', 1 );
	
	/* 排序标记 */
	$sort_flag = sort_flag ( $goods_list ['filter'] );
	$smarty->assign ( $sort_flag ['tag'], $sort_flag ['img'] );
	
	/* 获取商品类型存在规格的类型 */
	$specifications = get_goods_type_specifications ();
	$smarty->assign ( 'specifications', $specifications );
	if ($_REQUEST ['act'] == 'trash') {
		$smarty->assign ( 'is_goods_trash', '1' );
	}
	if (isset ( $_CFG ['supplier_addbest'] )) {
		$smarty->assign ( 'is_addbest', $_CFG ['supplier_addbest'] );
	}
	/* 显示商品列表页面 */
	assign_query_info ();
	$smarty->display ( 'goods_list.htm');
	
}
/* ------------------------------------------------------ */
// -- 商品列表，商品回收站
/* ------------------------------------------------------ */

 
/* ------------------------------------------------------ */
// -- 显示图片
/* ------------------------------------------------------ */

function action_show_image(){
	
	global $smarty;
	global $db ,$ecs,$_CFG;
	
	
	if (isset ( $GLOBALS ['shop_id'] ) && $GLOBALS ['shop_id'] > 0) {
		$img_url = $_GET ['img_url'];
	} else {
		if (strpos ( $_GET ['img_url'], 'http://' ) === 0) {
			$img_url = $_GET ['img_url'];
		} else {
			$img_url = '../' . $_GET ['img_url'];
		}
	}
	$smarty->assign ( 'img_url', $img_url );
	$smarty->display ( 'goods_show_image.htm' );
	
	
}
 
/* ------------------------------------------------------ */
// -- 排序、分页、查询
/* ------------------------------------------------------ */
function action_query(){
	
	global $smarty;
	global $db ,$ecs,$_CFG;
	
	$is_delete = empty ( $_REQUEST ['is_delete'] ) ? 0 : intval ( $_REQUEST ['is_delete'] );
	$code = empty ( $_REQUEST ['extension_code'] ) ? '' : trim ( $_REQUEST ['extension_code'] );
	$goods_list = goods_list ( $is_delete, ($code == '') ? 1 : 0 );
	
	$handler_list = array ();
	$handler_list ['virtual_card'] [] = array (
			'url' => 'virtual_card.php?act=card',
			'title' => $_LANG ['card'],
			'img' => 'icon_send_bonus.gif'
	);
	$handler_list ['virtual_card'] [] = array (
			'url' => 'virtual_card.php?act=replenish',
			'title' => $_LANG ['replenish'],
			'img' => 'icon_add.gif'
	);
	$handler_list ['virtual_card'] [] = array (
			'url' => 'virtual_card.php?act=batch_card_add',
			'title' => $_LANG ['batch_card_add'],
			'img' => 'icon_output.gif'
	);
	
	if (isset ( $handler_list [$code] )) {
		$smarty->assign ( 'add_handler', $handler_list [$code] );
	}
	$smarty->assign ( 'code', $code );
	$smarty->assign ( 'goods_list', $goods_list ['goods'] );
	$smarty->assign ( 'filter', $goods_list ['filter'] );
	$smarty->assign ( 'record_count', $goods_list ['record_count'] );
	$smarty->assign ( 'page_count', $goods_list ['page_count'] );
	$smarty->assign ( 'list_type', $is_delete ? 'trash' : 'goods' );
	$smarty->assign ( 'use_storage', empty ( $_CFG ['use_storage'] ) ? 0 : 1 );
	
	/* 排序标记 */
	$sort_flag = sort_flag ( $goods_list ['filter'] );
	$smarty->assign ( $sort_flag ['tag'], $sort_flag ['img'] );
	
	/* 获取商品类型存在规格的类型 */
	$specifications = get_goods_type_specifications ();
	$smarty->assign ( 'specifications', $specifications );
	
	$tpl = $is_delete ? 'goods_trash.htm' : 'goods_list.htm';
	
	make_json_result ( $smarty->fetch ( $tpl ), '', array (
			'filter' => $goods_list ['filter'],
			'page_count' => $goods_list ['page_count']
	) );
	
}
 
 
/* ------------------------------------------------------ */
// -- 切换商品类型
/* ------------------------------------------------------ */
function action_get_attr(){
	
	global $smarty;
	global $db ,$ecs,$_CFG;
	
	check_authz_json ( 'goods_manage' );
	
	$goods_id = empty ( $_GET ['goods_id'] ) ? 0 : intval ( $_GET ['goods_id'] );
	$goods_type = empty ( $_GET ['goods_type'] ) ? 0 : intval ( $_GET ['goods_type'] );
	$content = build_attr_html ( $goods_type, $goods_id, $bar_code );
	
	// $content = build_attr_html($goods_type, $goods_id);
	
	make_json_result ( $content );
	
}
 
/* ------------------------------------------------------ */
// -- 搜索商品，仅返回名称及ID
/* ------------------------------------------------------ */
function action_get_goods_list(){
	
	global $smarty;
	global $db ,$ecs,$_CFG;
	
	include_once (ROOT_PATH . 'includes/cls_json.php');
	$json = new JSON ();
	
	$filters = $json->decode ( $_GET ['JSON'] );
	
	$arr = get_goods_list ( $filters );
	$opt = array ();
	
	foreach ( $arr as $key => $val ) {
		$opt [] = array (
				'value' => $val ['goods_id'],
				'text' => $val ['goods_name'],
				'data' => $val ['shop_price']
		);
	}
	
	make_json_result ( $opt );
	
}
 

/**
 * 列表链接
 * 
 * @param bool $is_add
 *        	是否添加（插入）
 * @param string $extension_code
 *        	虚拟商品扩展代码，实体商品为空
 * @return array('href' => $href, 'text' => $text)
 */
function list_link($is_add = true, $extension_code = '', $supplier_status = '') {
	if ($supplier_status != '') {
		$href = 'goods.php?act=list&supplier_status=' . $supplier_status;
	} else {
		$href = 'goods.php?act=list';
	}
	
	if (! empty ( $extension_code )) {
		$href .= '&extension_code=' . $extension_code;
	}
	if (! $is_add) {
		$href .= '&' . list_link_postfix ();
	}
	
	if ($extension_code == 'virtual_card') {
		$text = $GLOBALS ['_LANG'] ['50_virtual_card_list'];
	} else {
		$text = $GLOBALS ['_LANG'] ['01_goods_list'];
	}
	
	return array (
			'href' => $href,
			'text' => $text 
	);
}

/**
 * 添加链接
 * 
 * @param string $extension_code
 *        	虚拟商品扩展代码，实体商品为空
 * @return array('href' => $href, 'text' => $text)
 */
function add_link($extension_code = '') {
	$href = 'goods.php?act=add';
	if (! empty ( $extension_code )) {
		$href .= '&extension_code=' . $extension_code;
	}
	
	if ($extension_code == 'virtual_card') {
		$text = $GLOBALS ['_LANG'] ['51_virtual_card_add'];
	} else {
		$text = $GLOBALS ['_LANG'] ['03_goods_add'];
	}
	
	return array (
			'href' => $href,
			'text' => $text 
	);
}

/**
 * 检查图片网址是否合法
 *
 * @param string $url
 *        	网址
 *        	
 * @return boolean
 */
function goods_parse_url($url) {
	$parse_url = @parse_url ( $url );
	return (! empty ( $parse_url ['scheme'] ) && ! empty ( $parse_url ['host'] ));
}

/**
 * 保存某商品的优惠价格
 * 
 * @param int $goods_id
 *        	商品编号
 * @param array $number_list
 *        	优惠数量列表
 * @param array $price_list
 *        	价格列表
 * @return void
 */
function handle_volume_price($goods_id, $number_list, $price_list) {
	$sql = "DELETE FROM " . $GLOBALS ['ecs']->table ( 'volume_price' ) . " WHERE price_type = '1' AND goods_id = '$goods_id'";
	$GLOBALS ['db']->query ( $sql );
	
	/* 循环处理每个优惠价格 */
	foreach ( $price_list as $key => $price ) {
		/* 价格对应的数量上下限 */
		$volume_number = $number_list [$key];
		
		if (! empty ( $price )) {
			$sql = "INSERT INTO " . $GLOBALS ['ecs']->table ( 'volume_price' ) . " (price_type, goods_id, volume_number, volume_price) " . "VALUES ('1', '$goods_id', '$volume_number', '$price')";
			$GLOBALS ['db']->query ( $sql );
		}
	}
}

/**
 * 获得商品的详细信息
 *
 * @access public
 * @param integer $goods_id        	
 * @return void
 */
function get_goods_info($goods_id) {
	$time = gmtime ();
	$sql = 'SELECT g.*, c.measure_unit, b.brand_id, b.brand_name AS goods_brand, m.type_money AS bonus_money, ' . 'IFNULL(AVG(r.comment_rank), 0) AS comment_rank, ' . "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS rank_price " . 'FROM ' . $GLOBALS ['ecs']->table ( 'goods' ) . ' AS g ' . 'LEFT JOIN ' . $GLOBALS ['ecs']->table ( 'category' ) . ' AS c ON g.cat_id = c.cat_id ' . 'LEFT JOIN ' . $GLOBALS ['ecs']->table ( 'brand' ) . ' AS b ON g.brand_id = b.brand_id ' . 'LEFT JOIN ' . $GLOBALS ['ecs']->table ( 'comment' ) . ' AS r ' . 'ON r.id_value = g.goods_id AND comment_type = 0 AND r.parent_id = 0 AND r.status = 1 ' . 'LEFT JOIN ' . $GLOBALS ['ecs']->table ( 'bonus_type' ) . ' AS m ' . "ON g.bonus_type_id = m.type_id AND m.send_start_date <= '$time' AND m.send_end_date >= '$time'" . " LEFT JOIN " . $GLOBALS ['ecs']->table ( 'member_price' ) . " AS mp " . "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' " . "WHERE g.goods_id = '$goods_id' AND g.is_delete = 0 " . "GROUP BY g.goods_id";
	
	$row = $GLOBALS ['db']->getRow ( $sql );
	
	if ($row !== false) {
		/* 用户评论级别取整 */
		$row ['comment_rank'] = ceil ( $row ['comment_rank'] ) == 0 ? 5 : ceil ( $row ['comment_rank'] );
		
		/* 获得商品的销售价格 */
		$row ['market_price'] = price_format ( $row ['market_price'] );
		$row ['shop_price_formated'] = price_format ( $row ['shop_price'] );
		
		/* 修正促销价格 */
		if ($row ['promote_price'] > 0) {
			$promote_price = bargain_price ( $row ['promote_price'], $row ['promote_start_date'], $row ['promote_end_date'] );
		} else {
			$promote_price = 0;
		}
		
		/* 处理商品水印图片 */
		$watermark_img = '';
		
		if ($promote_price != 0) {
			$watermark_img = "watermark_promote";
		} elseif ($row ['is_new'] != 0) {
			$watermark_img = "watermark_new";
		} elseif ($row ['is_best'] != 0) {
			$watermark_img = "watermark_best";
		} elseif ($row ['is_hot'] != 0) {
			$watermark_img = 'watermark_hot';
		}
		
		if ($watermark_img != '') {
			$row ['watermark_img'] = $watermark_img;
		}
		
		$row ['promote_price_org'] = $promote_price;
		$row ['promote_price'] = price_format ( $promote_price );
		
		/* 修正重量显示 */
		$row ['goods_weight'] = (intval ( $row ['goods_weight'] ) > 0) ? $row ['goods_weight'] . $GLOBALS ['_LANG'] ['kilogram'] : ($row ['goods_weight'] * 1000) . $GLOBALS ['_LANG'] ['gram'];
		
		/* 修正上架时间显示 */
		$row ['add_time'] = local_date ( $GLOBALS ['_CFG'] ['date_format'], $row ['add_time'] );
		
		/* 促销时间倒计时 */
		$time = gmtime ();
		if ($time >= $row ['promote_start_date'] && $time <= $row ['promote_end_date']) {
			$row ['gmt_end_time'] = $row ['promote_end_date'];
		} else {
			$row ['gmt_end_time'] = 0;
		}
		
		/* 是否显示商品库存数量 */
		$row ['goods_number'] = ($GLOBALS ['_CFG'] ['use_storage'] == 1) ? $row ['goods_number'] : '';
		
		/* 修正积分：转换为可使用多少积分（原来是可以使用多少钱的积分） */
		$row ['integral'] = $GLOBALS ['_CFG'] ['integral_scale'] ? round ( $row ['integral'] * 100 / $GLOBALS ['_CFG'] ['integral_scale'] ) : 0;
		
		/* 修正优惠券 */
		$row ['bonus_money'] = ($row ['bonus_money'] == 0) ? 0 : price_format ( $row ['bonus_money'], false );
		
		/* 修正商品图片 */
		$row ['goods_img'] = get_image_path ( $goods_id, $row ['goods_img'] );
		$row ['goods_thumb'] = get_image_path ( $goods_id, $row ['goods_thumb'], true );
		
		return $row;
	} else {
		return false;
	}
}
function getchild($pid, $arr) {
	$sa = $newarr = array ();
	if (is_array ( $arr )) {
		foreach ( $arr as $id => $sa ) {
			if ($sa ['pid'] == $pid)
				$newarr [$id] = $sa;
		}
	}
	return $newarr ? $newarr : array ();
}
function get_tree($pid, $arr, $num, $cats_old_zhyh) {
	global $catstr;
	$child = getchild ( $pid, $arr );
	
	if (is_array ( $child )) {
		
		$total = count ( $child );
		foreach ( $child as $id => $sa ) {
			$pstr = '';
			for($i = 0; $i <= $num; $i ++) {
				$pstr = $pstr . ($num ? '&nbsp;&nbsp;&nbsp;&nbsp;' : '&nbsp;&nbsp;&nbsp;');
			}
			$children = array ();
			$zhyhcount = 0;
			$children = getchild ( $id, $arr );
			$zhyhcount = count ( $children );
			// if( $zhyhcount == 0 )
			// {
			if (@in_array ( $id, $cats_old_zhyh )) {
				$selected_zhyh = " checked";
			} else {
				$selected_zhyh = " ";
			}
			$zhyh = '<input type="checkbox" class="nfl" name="supplier_cat_id[]" id="supplier_cat_id" value="' . $id . '" ' . $selected_zhyh . '>';
			// }
			// else
			// {
			// $zhyh='';
			// }
			$catstr = $catstr . $pstr . $zhyh . $sa ['name'] . "  <br>";
			
			$num ++;
			get_tree ( $sa ['id'], $arr, $num, $cats_old_zhyh );
			$num --;
		}
	} else {
		return;
	}
}

/**
 * 将商品分类列表转换成符合zTree标准的JSON字符串格式
 */
function cat_list_to_json_string($cat_list, $selected = 0) {
	include_once (ROOT_PATH . 'includes/Pinyin.php');
	
	$tree = array ();
	
	foreach ( $cat_list as $k => $cat ) {
		$id = $cat ['cat_id'];
		$pId = $cat ['parent_id'];
		$name = $cat ['cat_name'];
		// $open = true;
		
		$name_pinyin = Pinyin ( $name, 'utf-8', 1 ) . $name;
		
		$node = array (
				"id" => $id,
				"pId" => $pId,
				"name" => $name,
				"name_pinyin" => $name_pinyin 
		);
		
		array_push ( $tree, $node );
	}
	
	return json_encode ( $tree );
}

?>