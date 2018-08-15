<?php

/**
 * ECSHOP 商品相关函数库
 * ============================================================================
 * * 版权所有 2008-2015 秦皇岛商之翼网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.68ecshop.com;
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: derek $
 * $Id: lib_goods.php 17217 2011-01-19 06:29:08Z derek $
 */
if (!defined('IN_ECS')) {
    die('Hacking attempt');
}

/**
 * 保存某商品的相册图片
 * @param   int     $goods_id
 * @param   array   $image_files
 * @param   array   $image_descs
 * @return  void
 */
function handle_gallery_image($goods_id, $image_files, $image_descs, $image_urls)
{
	/* 是否处理缩略图 */
	$proc_thumb = (isset($GLOBALS['shop_id']) && $GLOBALS['shop_id'] > 0)? false : true;
	foreach ($image_descs AS $key => $img_desc)
	{
		/* 是否成功上传 */
		$flag = false;
		if (isset($image_files['error']))
		{
			if ($image_files['error'][$key] == 0)
			{
				$flag = true;
			}
		}
		else
		{
			if ($image_files['tmp_name'][$key] != 'none')
			{
				$flag = true;
			}
		}
		
		if ($flag)
		{
			// 生成缩略图
			if ($proc_thumb)
			{
				$thumb_url = $GLOBALS['image']->make_thumb($image_files['tmp_name'][$key], $GLOBALS['_CFG']['thumb_width'],  $GLOBALS['_CFG']['thumb_height']);
				$thumb_url = is_string($thumb_url) ? $thumb_url : '';
			}
			
			$upload = array(
					'name' => $image_files['name'][$key],
					'type' => $image_files['type'][$key],
					'tmp_name' => $image_files['tmp_name'][$key],
					'size' => $image_files['size'][$key],
			);
			if (isset($image_files['error']))
			{
				$upload['error'] = $image_files['error'][$key];
			}
			$img_original = $GLOBALS['image']->upload_image($upload);
			if ($img_original === false)
			{
				sys_msg($GLOBALS['image']->error_msg(), 1, array(), false);
			}
			$img_url = $img_original;
			
			if (!$proc_thumb)
			{
				$thumb_url = $img_original;
			}
			// 如果服务器支持GD 则添加水印
			if ($proc_thumb && gd_version() > 0)
			{
				$pos        = strpos(basename($img_original), '.');
				$newname    = dirname($img_original) . '/' . $GLOBALS['image']->random_filename() . substr(basename($img_original), $pos);
				copy('../' . $img_original, '../' . $newname);
				$img_url    = $newname;
				
				$GLOBALS['image']->add_watermark('../'.$img_url,'',$GLOBALS['_CFG']['watermark'], $GLOBALS['_CFG']['watermark_place'], $GLOBALS['_CFG']['watermark_alpha']);
			}
			
			/* 重新格式化图片名称 */
			$img_original = reformat_image_name('gallery', $goods_id, $img_original, 'source');
			$img_url = reformat_image_name('gallery', $goods_id, $img_url, 'goods');
			$thumb_url = reformat_image_name('gallery_thumb', $goods_id, $thumb_url, 'thumb');
			$sql = "INSERT INTO " . $GLOBALS['ecs']->table('goods_gallery') . " (goods_id, img_url, img_desc, thumb_url, img_original) " .
					"VALUES ('$goods_id', '$img_url', '$img_desc', '$thumb_url', '$img_original')";
			$GLOBALS['db']->query($sql);
			/* 不保留商品原图的时候删除原图 */
			if ($proc_thumb && !$GLOBALS['_CFG']['retain_original_img'] && !empty($img_original))
			{
				$GLOBALS['db']->query("UPDATE " . $GLOBALS['ecs']->table('goods_gallery') . " SET img_original='' WHERE `goods_id`='{$goods_id}'");
				@unlink('../' . $img_original);
			}
		}
		elseif (!empty($image_urls[$key]) && ($image_urls[$key] != $GLOBALS['_LANG']['img_file']) && ($image_urls[$key] != 'http://') && copy(trim($image_urls[$key]), ROOT_PATH . 'temp/' . basename($image_urls[$key])))
		{
			$image_url = trim($image_urls[$key]);
			
			//定义原图路径
			$down_img = ROOT_PATH . 'temp/' . basename($image_url);
			
			// 生成缩略图
			if ($proc_thumb)
			{
				$thumb_url = $GLOBALS['image']->make_thumb($down_img, $GLOBALS['_CFG']['thumb_width'],  $GLOBALS['_CFG']['thumb_height']);
				$thumb_url = is_string($thumb_url) ? $thumb_url : '';
				$thumb_url = reformat_image_name('gallery_thumb', $goods_id, $thumb_url, 'thumb');
			}
			
			if (!$proc_thumb)
			{
				$thumb_url = htmlspecialchars($image_url);
			}
			
			/* 重新格式化图片名称 */
			$img_url = $img_original = htmlspecialchars($image_url);
			$sql = "INSERT INTO " . $GLOBALS['ecs']->table('goods_gallery') . " (goods_id, img_url, img_desc, thumb_url, img_original) " .
					"VALUES ('$goods_id', '$img_url', '$img_desc', '$thumb_url', '$img_original')";
			$GLOBALS['db']->query($sql);
			
			@unlink($down_img);
		}
	}
}
/**
 * 格式化商品图片名称（按目录存储）
 *
 */
function reformat_image_name($type, $goods_id, $source_img, $position='')
{
	$rand_name = gmtime() . sprintf("%03d", mt_rand(1,999));
	$img_ext = substr($source_img, strrpos($source_img, '.'));
	$dir = 'images';
	if (defined('IMAGE_DIR'))
	{
		$dir = IMAGE_DIR;
	}
	$sub_dir = date('Ym', gmtime());
	if (!make_dir(ROOT_PATH.$dir.'/'.$sub_dir))
	{
		return false;
	}
	if (!make_dir(ROOT_PATH.$dir.'/'.$sub_dir.'/source_img'))
	{
		return false;
	}
	if (!make_dir(ROOT_PATH.$dir.'/'.$sub_dir.'/goods_img'))
	{
		return false;
	}
	if (!make_dir(ROOT_PATH.$dir.'/'.$sub_dir.'/thumb_img'))
	{
		return false;
	}
	switch($type)
	{
		case 'goods':
			$img_name = $goods_id . '_G_' . $rand_name;
			break;
		case 'goods_thumb':
			$img_name = $goods_id . '_thumb_G_' . $rand_name;
			break;
		case 'gallery':
			$img_name = $goods_id . '_P_' . $rand_name;
			break;
		case 'gallery_thumb':
			$img_name = $goods_id . '_thumb_P_' . $rand_name;
			break;
	}
	if ($position == 'source')
	{
		if (move_image_file(ROOT_PATH.$source_img, ROOT_PATH.$dir.'/'.$sub_dir.'/source_img/'.$img_name.$img_ext))
		{
			return $dir.'/'.$sub_dir.'/source_img/'.$img_name.$img_ext;
		}
	}
	elseif ($position == 'thumb')
	{
		if (move_image_file(ROOT_PATH.$source_img, ROOT_PATH.$dir.'/'.$sub_dir.'/thumb_img/'.$img_name.$img_ext))
		{
			return $dir.'/'.$sub_dir.'/thumb_img/'.$img_name.$img_ext;
		}
	}
	else
	{
		if (move_image_file(ROOT_PATH.$source_img, ROOT_PATH.$dir.'/'.$sub_dir.'/goods_img/'.$img_name.$img_ext))
		{
			return $dir.'/'.$sub_dir.'/goods_img/'.$img_name.$img_ext;
		}
	}
	return false;
}


/**
 * 获得商品的关联文章
 *
 * @access  public
 * @param   integer $goods_id
 * @return  array
 */
function get_goods_articles($goods_id)
{
	$sql = "SELECT g.article_id, a.title " .
			"FROM " .$GLOBALS['ecs']->table('goods_article') . " AS g, " .
			$GLOBALS['ecs']->table('article') . " AS a " .
			"WHERE g.goods_id = '$goods_id' " .
			"AND g.article_id = a.article_id ";
			if ($goods_id == 0)
			{
				$sql .= " AND g.admin_id = '$_SESSION[admin_id]'";
			}
			$row = $GLOBALS['db']->getAll($sql);
			
			return $row;
}

/**
 * 获得指定商品的配件
 *
 * @access  public
 * @param   integer $goods_id
 * @return  array
 */
function get_group_goods($goods_id)
{
	$sql = "SELECT gg.goods_id, CONCAT(g.goods_name, ' -- [', gg.goods_price, ']') AS goods_name " .
			"FROM " . $GLOBALS['ecs']->table('group_goods') . " AS gg, " .
			$GLOBALS['ecs']->table('goods') . " AS g " .
			"WHERE gg.parent_id = '$goods_id' " .
			"AND gg.goods_id = g.goods_id ";
			if ($goods_id == 0)
			{
				$sql .= " AND gg.admin_id = '$_SESSION[admin_id]'";
			}
			$row = $GLOBALS['db']->getAll($sql);
			
			return $row;
}
/**
 * 获得商品已添加的规格列表
 *
 * @access      public
 * @params      integer         $goods_id
 * @return      array
 */
function get_goods_specifications_list($goods_id)
{
	if (empty($goods_id))
	{
		return array();  //$goods_id不能为空
	}
	
	$sql = "SELECT g.goods_attr_id, g.attr_value, g.attr_id, a.attr_name
            FROM " . $GLOBALS['ecs']->table('goods_attr') . " AS g
                LEFT JOIN " . $GLOBALS['ecs']->table('attribute') . " AS a
                ON a.attr_id = g.attr_id
                WHERE goods_id = '$goods_id'
                AND a.attr_type = 1
                ORDER BY g.attr_id ASC";
	$results = $GLOBALS['db']->getAll($sql);
	
	return $results;
}

/**
 * 检查单个商品是否存在规格
 *
 * @param   int        $goods_id          商品id
 * @return  bool                          true，存在；false，不存在
 */
function check_goods_specifications_exist($goods_id)
{
	$goods_id = intval($goods_id);
	
	$sql = "SELECT COUNT(a.attr_id)
            FROM " .$GLOBALS['ecs']->table('attribute'). " AS a, " .$GLOBALS['ecs']->table('goods'). " AS g
            WHERE a.cat_id = g.goods_type
            AND g.goods_id = '$goods_id'";
	
	$count = $GLOBALS['db']->getOne($sql);
	
	if ($count > 0)
	{
		return true;    //存在
	}
	else
	{
		return false;    //不存在
	}
}
function move_image_file($source, $dest)
{
	if (@copy($source, $dest))
	{
		@unlink($source);
		return true;
	}
	return false;
}
/**
 * 保存某商品的关联文章
 * @param   int     $goods_id
 * @return  void
 */
function handle_goods_article($goods_id)
{
	$sql = "UPDATE " . $GLOBALS['ecs']->table('goods_article') . " SET " .
			" goods_id = '$goods_id' " .
			" WHERE goods_id = '0'" .
			" AND admin_id = '$_SESSION[admin_id]'";
	$GLOBALS['db']->query($sql);
}

/**
 * 保存某商品的配件
 * @param   int     $goods_id
 * @return  void
 */
function handle_group_goods($goods_id)
{
	$sql = "UPDATE " . $GLOBALS['ecs']->table('group_goods') . " SET " .
			" parent_id = '$goods_id' " .
			" WHERE parent_id = '0'" .
			" AND admin_id = '$_SESSION[admin_id]'";
	$GLOBALS['db']->query($sql);
}


/**
 * 保存某商品的关联商品
 * @param   int     $goods_id
 * @return  void
 */
function handle_link_goods($goods_id)
{
	$sql = "UPDATE " . $GLOBALS['ecs']->table('link_goods') . " SET " .
			" goods_id = '$goods_id' " .
			" WHERE goods_id = '0'" .
			" AND admin_id = '$_SESSION[admin_id]'";
	$GLOBALS['db']->query($sql);
	
	$sql = "UPDATE " . $GLOBALS['ecs']->table('link_goods') . " SET " .
			" link_goods_id = '$goods_id' " .
			" WHERE link_goods_id = '0'" .
			" AND admin_id = '$_SESSION[admin_id]'";
	$GLOBALS['db']->query($sql);
}


/**
 * 为某商品生成唯一的货号
 * @param   int     $goods_id   商品编号
 * @return  string  唯一的货号
 */
function generate_goods_sn($goods_id)
{
	$goods_sn = $GLOBALS['_CFG']['sn_prefix'] . str_repeat('0', 6 - strlen($goods_id)) . $goods_id;
	
	$sql = "SELECT goods_sn FROM " . $GLOBALS['ecs']->table('goods') .
	" WHERE goods_sn LIKE '" . mysql_like_quote($goods_sn) . "%' AND goods_id <> '$goods_id' " .
	" ORDER BY LENGTH(goods_sn) DESC";
	$sn_list = $GLOBALS['db']->getCol($sql);
	if (in_array($goods_sn, $sn_list))
	{
		$max = pow(10, strlen($sn_list[0]) - strlen($goods_sn) + 1) - 1;
		$new_sn = $goods_sn . mt_rand(0, $max);
		while (in_array($new_sn, $sn_list))
		{
			$new_sn = $goods_sn . mt_rand(0, $max);
		}
		$goods_sn = $new_sn;
	}
	
	return $goods_sn;
}


/**
 * 取得通用属性和某分类的属性，以及某商品的属性值
 * @param   int     $cat_id     分类编号
 * @param   int     $goods_id   商品编号
 * @return  array   规格与属性列表
 */
function get_attr_list($cat_id, $goods_id = 0)
{
	if (empty($cat_id))
	{
		return array();
	}
	
	// 查询属性值及商品的属性值
	$sql = "SELECT a.attr_id, a.attr_name, a.attr_input_type, a.attr_type,a.attr_txm, a.attr_values, v.attr_value, v.attr_price ".
			"FROM " .$GLOBALS['ecs']->table('attribute'). " AS a ".
			"LEFT JOIN " .$GLOBALS['ecs']->table('goods_attr'). " AS v ".
			"ON v.attr_id = a.attr_id AND v.goods_id = '$goods_id' ".
			"WHERE a.cat_id = " . intval($cat_id) ." OR a.cat_id = 0 ".
			"ORDER BY a.sort_order, a.attr_type, a.attr_id, v.attr_price, v.goods_attr_id";
	
	$row = $GLOBALS['db']->GetAll($sql);
	
	return $row;
}

/**
 * 根据属性数组创建属性的表单
 *
 * @access  public
 * @param   int     $cat_id     分类编号
 * @param   int     $goods_id   商品编号
 * @return  string
 */
function build_attr_html($cat_id, $goods_id = 0 , $bar_code = 0)
{
	$attr = get_attr_list($cat_id, $goods_id);
	$html = '<table width="100%" id="attrTable">';
	$spec = 0;
	
	foreach ($attr AS $key => $val)
	{
		$html .= "<tr><td class='label'>";
		if ($val['attr_type'] == 1 || $val['attr_type'] == 2)
		{
			$html .= ($spec != $val['attr_id']) ?
			"<a href='javascript:;' onclick='addSpec(this)'>[+]</a>" :
			"<a href='javascript:;' onclick='removeSpec(this)'>[-]</a>";
			$spec = $val['attr_id'];
		}
		
		$html .= "$val[attr_name]</td><td><input type='hidden' name='attr_id_list[]' value='$val[attr_id]' txm='$val[attr_txm]' class='ctxm_$val[attr_txm]' />";
		
		if ($val['attr_input_type'] == 0)
		{
			$html .= '<input name="attr_value_list[]" type="text" value="' .htmlspecialchars($val['attr_value']). '" size="40" /> ';
		}
		elseif ($val['attr_input_type'] == 2)
		{
			$html .= '<textarea name="attr_value_list[]" rows="3" cols="40">' .htmlspecialchars($val['attr_value']). '</textarea>';
		}
		else
		{
			/*
			 *
			 *
			 *
			 *条形码点击事件开始
			 */
			if($val[attr_txm] > 0){
				$html .= '<select class=attr_num_'.$val[attr_id].' name="attr_value_list[]" onchange="getType('.$val[attr_txm].','.$cat_id.','. $this.value.','.$goods_id.')">';
			}else{
				$html .= '<select class=attr_num_'.$val[attr_id].' name="attr_value_list[]" >';
			}
			/*条形码点击事件结束*/
			$html .= '<option value="">' .$GLOBALS['_LANG']['select_please']. '</option>';
			
			$attr_values = explode("\n", $val['attr_values']);
			
			foreach ($attr_values AS $opt)
			{
				$opt    = trim(htmlspecialchars($opt));
				
				$html   .= ($val['attr_value'] != $opt) ?
				'<option value="' . $opt . '">' . $opt . '</option>' :
				'<option value="' . $opt . '" selected="selected">' . $opt . '</option>';
			}
			$html .= '</select> ';
		}
		
		$html .= ($val['attr_type'] == 1 || $val['attr_type'] == 2) ?
		$GLOBALS['_LANG']['spec_price'].' <input type="text" name="attr_price_list[]" value="' . $val['attr_price'] . '" size="5" maxlength="10" />' :
		' <input type="hidden" name="attr_price_list[]" value="0" />';
		
		$html .= '</td></tr>';
	}
	/*
	 *
	 *702460594
	 *
	 *条形码的显示开始
	 *
	 */
	$html .= '</table>';
	
	if($bar_code){
		$html .= '<div id="input_txm"><table  width="100%"  >';
		foreach($bar_code as $value){
			$html .='<tr><td class="label">条形码</td><td><input type="hidden" name="txm_shu[]" value='.$value['taypes'].'>'.$value['taypes'].'<td/><td><input type="text" name="tiaoxingm[]" value='.$value['bar_code'].'></td></tr>';
			
		}
		$html .='</table ></div>';
	}else{
		$html .= '<div id="input_txm"></div>';
	}
	
	return $html;
}


/**
 * 取得重量单位列表
 * @return  array   重量单位列表
 */
function get_unit_list()
{
	return array(
			'1'     => $GLOBALS['_LANG']['unit_kg'],
			'0.001' => $GLOBALS['_LANG']['unit_g'],
	);
}
/**
 * 取得会员等级列表
 * @return  array   会员等级列表
 */
function get_user_rank_list()
{
	$sql = "SELECT * FROM " . $GLOBALS['ecs']->table('user_rank') .
	" ORDER BY min_points";
	
	return $GLOBALS['db']->getAll($sql);
}

/**
 * 获取商品类型中包含规格的类型列表
 *
 * @access  public
 * @return  array
 */
function get_goods_type_specifications()
{
	// 查询
	$sql = "SELECT DISTINCT cat_id
            FROM " .$GLOBALS['ecs']->table('attribute'). "
            WHERE attr_type = 1";
	$row = $GLOBALS['db']->GetAll($sql);
	
	$return_arr = array();
	if (!empty($row))
	{
		foreach ($row as $value)
		{
			$return_arr[$value['cat_id']] = $value['cat_id'];
		}
	}
	return $return_arr;
}

/**
 * 获得商品列表
 *
 * @access  public
 * @params  integer $isdelete
 * @params  integer $real_goods
 * @params  integer $conditions
 * @return  array
 */
function goods_list($is_delete, $real_goods=1, $conditions = '')
{
	/* 过滤条件 */
	$param_str = '-' . $is_delete . '-' . $real_goods;
	$result = get_filter($param_str);
	// file_put_contents('./3resultresultresult3.txt',var_export($result,true));
	if ($result === false)
	{
		$day = getdate();
		$today = local_mktime(23, 59, 59, $day['mon'], $day['mday'], $day['year']);
		/* 代码增加_虚拟团购_START  www.68ecshop.com */
		// $filter['is_virtual']  = empty($_REQUEST['is_virtual'])? 0 : intval($_REQUEST['is_virtual']);
		$filter['city']  = empty($_REQUEST['city'])? 0 : intval($_REQUEST['city']);
		$filter['county']  = empty($_REQUEST['county'])? 0 : intval($_REQUEST['county']);
		$filter['district_id']  = empty($_REQUEST['district_id'])? 0 : intval($_REQUEST['district_id']);
		/* 代码增加_虚拟团购_END  www.68ecshop.com */
		$filter['cat_id']           = empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);
		$filter['supplier_status']           = $_REQUEST['supplier_status']!='' ?  $_REQUEST['supplier_status'] : '';
		$filter['intro_type']       = empty($_REQUEST['intro_type']) ? '' : trim($_REQUEST['intro_type']);
		$filter['is_promote']       = empty($_REQUEST['is_promote']) ? 0 : intval($_REQUEST['is_promote']);
		$filter['stock_warning']    = empty($_REQUEST['stock_warning']) ? 0 : intval($_REQUEST['stock_warning']);
		$filter['brand_id']         = empty($_REQUEST['brand_id']) ? 0 : intval($_REQUEST['brand_id']);
		$filter['keyword']          = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
		//$filter['suppliers_id'] = isset($_REQUEST['suppliers_id']) ? (empty($_REQUEST['suppliers_id']) ? '' : trim($_REQUEST['suppliers_id'])) : '';
		$filter['suppliers_id'] = !empty($_REQUEST['suppliers_id']) ? (empty($_REQUEST['suppliers_id']) ? $_SESSION['suppliers_id'] : trim($_REQUEST['suppliers_id'])) : '';
		$filter['is_on_sale'] = isset($_REQUEST['is_on_sale']) ? ((empty($_REQUEST['is_on_sale']) && $_REQUEST['is_on_sale'] === 0) ? '' : trim($_REQUEST['is_on_sale'])) : '';
		if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
		{
			$filter['keyword'] = json_str_iconv($filter['keyword']);
		}
		$filter['sort_by']          = empty($_REQUEST['sort_by']) ? 'goods_id' : trim($_REQUEST['sort_by']);
		$filter['sort_order']       = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
		$filter['extension_code']   = empty($_REQUEST['extension_code']) ? '' : trim($_REQUEST['extension_code']);
		$filter['is_delete']        = $is_delete;
		$filter['real_goods']       = $real_goods;
		
		//$where = $filter['cat_id'] > 0 ? " AND " . get_children($filter['cat_id']) : '';
		$where = $filter['cat_id'] > 0 ? " AND " . get_children($filter['cat_id'],'sgc') : '';
		
		/* 推荐类型 */
		switch ($filter['intro_type'])
		{
			case 'is_best':
				$where .= " AND is_best=1";
				break;
			case 'is_hot':
				$where .= ' AND is_hot=1';
				break;
			case 'is_new':
				$where .= ' AND is_new=1';
				break;
			case 'is_promote':
				$where .= " AND is_promote = 1 AND promote_price > 0 AND promote_start_date <= '$today' AND promote_end_date >= '$today'";
				break;
			case 'all_type';
			$where .= " AND (is_best=1 OR is_hot=1 OR is_new=1 OR (is_promote = 1 AND promote_price > 0 AND promote_start_date <= '" . $today . "' AND promote_end_date >= '" . $today . "'))";
		}
		
		/* 库存警告 */
		if ($filter['stock_warning'])
		{
			$where .= ' AND goods_number <= warn_number ';
		}
		
		/* 品牌 */
		if ($filter['brand_id'])
		{
			$where .= " AND brand_id='$filter[brand_id]'";
		}
		
		/* 审核状态 */
		if ($filter['supplier_status']!='')
		{
			$where .= " AND supplier_status='$filter[supplier_status]'";
		}
		
		/* 扩展 */
		if ($filter['extension_code'])
		{
			$where .= " AND extension_code='$filter[extension_code]'";
		}
		/* 虚拟团购代码添加 by www.68ecshop.com start */
		if($filter['city']){
			$where .= " AND dig.city='$filter[city]'";
		}
		if($filter['county']){
			$where .= " AND dig.county='$filter[county]'";
		}
		if($filter['district_id']){
			$where .= " AND dig.district_id=$filter[district_id]";
		}
		/* 虚拟团购代码添加 by www.68ecshop.com end */
		
		/* 关键字 */
		if (!empty($filter['keyword']))
		{
			$where .= " AND (goods_sn LIKE '%" . mysql_like_quote($filter['keyword']) . "%' OR goods_name LIKE '%" . mysql_like_quote($filter['keyword']) . "%')";
		}
		
		if ($real_goods > -1)
		{
			$where .= " AND is_real='$real_goods'";
		}
		
		/* 上架 */
		if ($filter['is_on_sale'] !== '')
		{
			$where .= " AND (is_on_sale = '" . $filter['is_on_sale'] . "')";
		}
		
		/* 供货商 */
		if (!empty($filter['suppliers_id']))
		{
			$where .= " AND (supplier_id = '" . $filter['suppliers_id'] . "')";
		}
		
		$where .= $conditions;
		
		/* 记录总数 */
		
		/* 代码增加_虚拟团购_START  www.68ecshop.com */
		if(!$real_goods && $filter['extension_code'] == 'virtual_good'){
			$sql = "SELECT COUNT(distinct(g.goods_id)) FROM " .$GLOBALS['ecs']->table('goods'). " AS g,"
					.$GLOBALS['ecs']->table('supplier_goods_cat')." as sgc, ".$GLOBALS['ecs']->table('virtual_district')." as dis, ".
					$GLOBALS['ecs']->table('virtual_goods_district')." as dig".
					" WHERE dis.supplier_id = ".$_SESSION['supplier_id']." AND sgc.goods_id=g.goods_id  and dis.goods_id = g.goods_id and dis.district_id = dig.district_id AND is_delete='$is_delete' AND sgc.supplier_id='". $_SESSION['supplier_id'] ."' $where";
					file_put_contents('./31113.txt',$sql);
		}else{
			$sql = "SELECT COUNT(distinct(g.goods_id)) FROM " .$GLOBALS['ecs']->table('goods'). " AS g,".$GLOBALS['ecs']->table('supplier_goods_cat')." as sgc WHERE sgc.goods_id=g.goods_id AND is_delete='$is_delete' AND sgc.supplier_id='". $_SESSION['supplier_id'] ."' $where";
			// 				file_put_contents('./322223.txt',$sql);
		}
		/* 代码增加_虚拟团购_END  www.68ecshop.com */
		$filter['record_count'] = $GLOBALS['db']->getOne($sql);
		
		/* 分页大小 */
		$filter = page_and_size($filter);
		
		/* 代码增加_虚拟团购_START  www.68ecshop.com */
		if(!$real_goods && $filter['extension_code'] == 'virtual_good'){
			$sql = "SELECT distinct(g.goods_id), goods_name, goods_type, exclusive, goods_sn, shop_price, is_on_sale, is_best, is_new, is_hot, sort_order,supplier_status, goods_number, integral, " .
					" (promote_price > 0 AND promote_start_date <= '$today' AND promote_end_date >= '$today') AS is_promote ".
					" FROM " . $GLOBALS['ecs']->table('goods') . " AS g,".$GLOBALS['ecs']->table('supplier_goods_cat')." as sgc,".$GLOBALS['ecs']->table('virtual_district')." as dis, ".
					$GLOBALS['ecs']->table('virtual_goods_district')." as dig".
					" WHERE dis.supplier_id = ".$_SESSION['supplier_id']." AND sgc.goods_id=g.goods_id and dis.goods_id = g.goods_id and dis.district_id = dig.district_id  AND is_delete='$is_delete' AND sgc.supplier_id='". $_SESSION['supplier_id'] ."' $where" .
					" ORDER BY $filter[sort_by] $filter[sort_order] ".
					" LIMIT " . $filter['start'] . ",$filter[page_size]";
		}else{
			$sql = "SELECT distinct(g.goods_id), goods_name, goods_type, exclusive, goods_sn, shop_price, is_on_sale, is_best, is_new, is_hot, sort_order,supplier_status, goods_number, integral, " .
					" (promote_price > 0 AND promote_start_date <= '$today' AND promote_end_date >= '$today') AS is_promote ".
					" FROM " . $GLOBALS['ecs']->table('goods') . " AS g,".$GLOBALS['ecs']->table('supplier_goods_cat')." as sgc WHERE sgc.goods_id=g.goods_id AND is_delete='$is_delete' AND sgc.supplier_id='". $_SESSION['supplier_id'] ."' $where" .
					" ORDER BY $filter[sort_by] $filter[sort_order] ".
					" LIMIT " . $filter['start'] . ",$filter[page_size]";
			
		}
		/* 代码增加_虚拟团购_END  www.68ecshop.com */
		
		$filter['keyword'] = stripslashes($filter['keyword']);
		set_filter($filter, $sql, $param_str);
	}
	else
	{
		$sql    = $result['sql'];
		$filter = $result['filter'];
	}
	$row = $GLOBALS['db']->getAll($sql);
	/* 代码增加_虚拟团购_START  www.68ecshop.com */
	/* 虚拟商品列表 添加商圈 */
	foreach($row as $k=>$v){
		$sql = "select d.district_id,v.district_name from ".$GLOBALS['ecs']->table('virtual_district')." as d
            left join ".$GLOBALS['ecs']->table('goods')." as g on  d.goods_id = g.goods_id
            left join ".$GLOBALS['ecs']->table('virtual_goods_district')." as v on v.district_id = d.district_id
            where d.goods_id = '$v[goods_id]' and d.supplier_id=".$_SESSION['supplier_id'];
		$res = $GLOBALS['db']->getAll($sql);
		$str = '';
		foreach($res as $key => $value){
			$str = $str.$value['district_name']. ',';
		}
		$row[$k]['district']  = substr($str,0,strlen($str)-1);
	}
	/* 代码增加_虚拟团购_END www.68ecshop.com */
	return array('goods' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

/**
 * 取得推荐类型列表
 * @return  array   推荐类型列表
 */
function get_intro_list()
{
	return array(
			'is_best'    => $GLOBALS['_LANG']['is_best'],
			'is_new'     => $GLOBALS['_LANG']['is_new'],
			'is_hot'     => $GLOBALS['_LANG']['is_hot'],
			'is_promote' => $GLOBALS['_LANG']['is_promote'],
			'all_type' => $GLOBALS['_LANG']['all_type'],
	);
}

/**
 * 商品推荐usort用自定义排序行数
 */
function goods_sort($goods_a, $goods_b) {
    if ($goods_a['sort_order'] == $goods_b['sort_order']) {
        return 0;
    }
    return ($goods_a['sort_order'] < $goods_b['sort_order']) ? -1 : 1;
}

/**
 * 获得指定分类同级的所有分类以及该分类下的子分类
 *
 * @access  public
 * @param   integer     $cat_id     分类编号
 * @return  array
 */
function get_categories_tree($cat_id = 0) {
    if ($cat_id > 0) {
        $sql = 'SELECT parent_id FROM ' . $GLOBALS['ecs']->table('category') . " WHERE cat_id = '$cat_id'";
        $parent_id = $GLOBALS['db']->getOne($sql);
    } else {
        $parent_id = 0;
    }

    /*
      判断当前分类中全是是否是底级分类，
      如果是取出底级分类上级分类，
      如果不是取当前分类及其下的子分类
     */
    $sql = 'SELECT count(*) FROM ' . $GLOBALS['ecs']->table('category') . " WHERE parent_id = '$parent_id' AND is_show = 1 ";
    if ($GLOBALS['db']->getOne($sql) || $parent_id == 0) {
        /* 获取当前分类及其子分类 */
        $sql = 'SELECT cat_id,cat_name ,parent_id,is_show,type_img,short_name ' .
                'FROM ' . $GLOBALS['ecs']->table('category') .
                "WHERE parent_id = '$parent_id' AND is_show = 1 AND is_virtual=0 ORDER BY sort_order ASC, cat_id ASC";

        $res = $GLOBALS['db']->getAll($sql);

        foreach ($res AS $row) {
            if ($row['is_show']) {
                $cat_arr[$row['cat_id']]['id'] = $row['cat_id'];
                $cat_arr[$row['cat_id']]['name'] = $row['cat_name'];
                $cat_arr[$row['cat_id']]['url'] = build_uri('category', array('cid' => $row['cat_id']), $row['cat_name']);
                $cat_arr[$row['cat_id']]['img'] = $row['type_img'];
                $cat_arr[$row['cat_id']]['short_name'] = $row['short_name'];
                if (isset($row['cat_id']) != NULL) {
                    $cat_arr[$row['cat_id']]['cat_id'] = get_child_tree($row['cat_id']);
                }
            }
        }
    }
    if (isset($cat_arr)) {
        return $cat_arr;
    }
}

function get_child_tree($tree_id = 0) {
    $three_arr = array();
    $sql = 'SELECT count(*) FROM ' . $GLOBALS['ecs']->table('category') . " WHERE parent_id = '$tree_id' AND is_show = 1 ";
    if ($GLOBALS['db']->getOne($sql) || $tree_id == 0) {
        $child_sql = 'SELECT cat_id, cat_name, parent_id, is_show,type_img ' .
                'FROM ' . $GLOBALS['ecs']->table('category') .
                "WHERE parent_id = '$tree_id' AND is_show = 1 ORDER BY sort_order ASC, cat_id ASC";
        $res = $GLOBALS['db']->getAll($child_sql);
        foreach ($res AS $row) {
            if ($row['is_show'])
                $three_arr[$row['cat_id']]['id'] = $row['cat_id'];
            $three_arr[$row['cat_id']]['name'] = $row['cat_name'];
            $three_arr[$row['cat_id']]['img'] = $row['type_img'];
            $three_arr[$row['cat_id']]['url'] = build_uri('category', array('cid' => $row['cat_id']), $row['cat_name']);

            if (isset($row['cat_id']) != NULL) {
                $three_arr[$row['cat_id']]['cat_id'] = get_child_tree($row['cat_id']);
            }
        }
    }
    return $three_arr;
}

/**
 * 调用当前分类的销售排行榜
 *
 * @access  public
 * @param   string  $cats   查询的分类
 * @return  array
 */
function get_top10($cats = '') {
    $cats = get_children($cats);
    $aa = get_extension_goods($cats);
    if (!empty($aa)) {
        $aa = " OR $aa";
    }
    $where = !empty($cats) ? "AND ($cats  " . $aa . ") " : '';

    /* 排行统计的时间 */
    switch ($GLOBALS['_CFG']['top10_time']) {
        case 1: // 一年
            $top10_time = "AND o.order_sn >= '" . date('Ymd', gmtime() - 365 * 86400) . "'";
            break;
        case 2: // 半年
            $top10_time = "AND o.order_sn >= '" . date('Ymd', gmtime() - 180 * 86400) . "'";
            break;
        case 3: // 三个月
            $top10_time = "AND o.order_sn >= '" . date('Ymd', gmtime() - 90 * 86400) . "'";
            break;
        case 4: // 一个月
            $top10_time = "AND o.order_sn >= '" . date('Ymd', gmtime() - 30 * 86400) . "'";
            break;
        default:
            $top10_time = '';
    }

    $sql = 'SELECT g.goods_id, g.goods_name, g.shop_price, g.goods_thumb, SUM(og.goods_number) as goods_number ' .
            'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g, ' .
            $GLOBALS['ecs']->table('order_info') . ' AS o, ' .
            $GLOBALS['ecs']->table('order_goods') . ' AS og ' .
            "WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 $where " . area_sql_limit_cookie() . " $top10_time ";
    //判断是否启用库存，库存数量是否大于0
    if ($GLOBALS['_CFG']['use_storage'] == 1) {
        $sql .= " AND g.goods_number > 0 ";
    }
    $sql .= ' AND og.order_id = o.order_id AND og.goods_id = g.goods_id ' .
            "AND (o.order_status = '" . OS_CONFIRMED . "' OR o.order_status = '" . OS_SPLITED . "') " .
            "AND (o.pay_status = '" . PS_PAYED . "' OR o.pay_status = '" . PS_PAYING . "') " .
            "AND (o.shipping_status = '" . SS_SHIPPED . "' OR o.shipping_status = '" . SS_RECEIVED . "') " .
            'GROUP BY g.goods_id ORDER BY goods_number DESC, g.goods_id DESC LIMIT ' . $GLOBALS['_CFG']['top_number'];

    $arr = $GLOBALS['db']->getAll($sql);

    for ($i = 0, $count = count($arr); $i < $count; $i++) {
        $arr[$i]['short_name'] = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
                sub_str($arr[$i]['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $arr[$i]['goods_name'];
        $arr[$i]['url'] = build_uri('goods', array('gid' => $arr[$i]['goods_id']), $arr[$i]['goods_name']);
        $arr[$i]['thumb'] = get_pc_url() . '/' . get_image_path($arr[$i]['goods_id'], $arr[$i]['goods_thumb'], true);
        $arr[$i]['price'] = price_format($arr[$i]['shop_price']);
    }

    return $arr;
}

/**
 * 获得推荐商品
 *
 * @access  public
 * @param   string      $type       推荐类型，可以是 best, new, hot
 * @return  array
 */
function get_recommend_goods($type = '', $cats = '') {
    if (!in_array($type, array('best', 'new', 'hot'))) {
        return array();
    }

    //取不同推荐对应的商品
    static $type_goods = array();
    if (empty($type_goods[$type])) {
        //初始化数据
        $type_goods['best'] = array();
        $type_goods['new'] = array();
        $type_goods['hot'] = array();
        $data = read_static_cache('recommend_goods');
        if ($data === false) {
            $sql = 'SELECT g.goods_id, g.is_best, g.is_new, g.is_hot, g.is_promote, b.brand_name,g.sort_order ' .
                    ' FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
                    ' LEFT JOIN ' . $GLOBALS['ecs']->table('brand') . ' AS b ON b.brand_id = g.brand_id ' .
                    ' WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 AND (g.is_best = 1 OR g.is_new =1 OR g.is_hot = 1)' . area_sql_limit_cookie() .
                    ' ORDER BY g.sort_order, g.last_update DESC';
            
            $goods_res = $GLOBALS['db']->getAll($sql);
            //定义推荐,最新，热门，促销商品
            $goods_data['best'] = array();
            $goods_data['new'] = array();
            $goods_data['hot'] = array();
            $goods_data['brand'] = array();
            if (!empty($goods_res)) {
                foreach ($goods_res as $data) {
                    if ($data['is_best'] == 1) {
                        $goods_data['best'][] = array('goods_id' => $data['goods_id'], 'sort_order' => $data['sort_order']);
                    }
                    if ($data['is_new'] == 1) {
                        $goods_data['new'][] = array('goods_id' => $data['goods_id'], 'sort_order' => $data['sort_order']);
                    }
                    if ($data['is_hot'] == 1) {
                        $goods_data['hot'][] = array('goods_id' => $data['goods_id'], 'sort_order' => $data['sort_order']);
                    }
                    if ($data['brand_name'] != '') {
                        $goods_data['brand'][$data['goods_id']] = $data['brand_name'];
                    }
                }
            }
            write_static_cache('recommend_goods', $goods_data);
        } else {
            $goods_data = $data;
        }
        
        

        $time = gmtime();
        $order_type = $GLOBALS['_CFG']['recommend_order'];

       
        //按推荐数量及排序取每一项推荐显示的商品 order_type可以根据后台设定进行各种条件显示
        static $type_array = array();
        $type2lib = array('best' => 'recommend_best', 'new' => 'recommend_new', 'hot' => 'recommend_hot');
        if (empty($type_array)) {
            foreach ($type2lib as $key => $data) {
                if (!empty($goods_data[$key])) {
                    $num = get_library_number($data);
                    
                    $data_count = count($goods_data[$key]);
                    $num = $data_count > $num ? $num : $data_count;
                   
                    if ($order_type == 0) {
                        //usort($goods_data[$key], 'goods_sort');
                        $rand_key = array_slice($goods_data[$key], 0, $num);
                        foreach ($rand_key as $key_data) {
                            $type_array[$key][] = $key_data['goods_id'];
                        }
                    } else {
                        $rand_key = array_rand($goods_data[$key], $num);
                        if ($num == 1) {
                            $type_array[$key][] = $goods_data[$key][$rand_key]['goods_id'];
                        } else {
                            foreach ($rand_key as $key_data) {
                                $type_array[$key][] = $goods_data[$key][$key_data]['goods_id'];
                            }
                        }
                    }
                } else {
                    $type_array[$key] = array();
                }
            }
        }

        //取出所有符合条件的商品数据，并将结果存入对应的推荐类型数组中
        $sql = 'SELECT g.goods_id, g.goods_name,g.click_count, g.goods_name_style, g.market_price, g.shop_price AS org_price, g.promote_price, ' .
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, g.exclusive, " .
                "promote_start_date, promote_end_date, g.goods_brief, g.goods_thumb, g.goods_img, RAND() AS rnd " .
                'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
                "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp " .
                "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ";
        $type_merge = array_merge($type_array['new'], $type_array['best'], $type_array['hot']);
        $type_merge = array_unique($type_merge);
        $sql .= ' WHERE g.goods_id ' . db_create_in($type_merge) . area_sql_limit_cookie();
        $sql .= ' ORDER BY g.sort_order, g.last_update DESC';

        $result = $GLOBALS['db']->getAll($sql);
        foreach ($result AS $idx => $row) {
            if ($row['promote_price'] > 0) {
                $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
                $goods[$idx]['promote_price'] = $promote_price > 0 ? price_format($promote_price) : '';
            } else {
                $goods[$idx]['promote_price'] = '';
            }
            $goods[$idx]['id'] = $row['goods_id'];
            $goods[$idx]['name'] = $row['goods_name'];
            $goods[$idx]['brief'] = $row['goods_brief'];
            $goods[$idx]['brand_name'] = isset($goods_data['brand'][$row['goods_id']]) ? $goods_data['brand'][$row['goods_id']] : '';
            $goods[$idx]['goods_style_name'] = add_style($row['goods_name'], $row['goods_name_style']);

            $goods[$idx]['short_name'] = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
                    sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
            $goods[$idx]['short_style_name'] = add_style($goods[$idx]['short_name'], $row['goods_name_style']);
            $goods[$idx]['market_price'] = price_format($row['market_price']);
            $goods[$idx]['shop_price'] = price_format($row['shop_price']);
            $goods[$idx]['final_price'] = price_format(get_final_price($row['goods_id'], 1, false));
            $goods[$idx]['is_exclusive'] = is_exclusive($row['exclusive'], get_final_price($row['goods_id']));
            if (empty($row['goods_thumb'])) {
                $row['goods_thumb'] = $GLOBALS['db']->getOne("SELECT thumb_url FROM " . $GLOBALS['ecs']->table('goods_gallery') . " WHERE goods_id=" . $row['goods_id']);
            }
            $goods[$idx]['thumb'] = get_pc_url() . '/' . get_image_path($row['goods_id'], $row['goods_thumb'], true);
            $goods[$idx]['goods_img'] = get_pc_url() . '/' . get_image_path($row['goods_id'], $row['goods_img']);
            $goods[$idx]['url'] = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);
            $goods[$idx]['sell_count'] = selled_count($row['goods_id']);
            $goods[$idx]['pinglun'] = get_evaluation_sum($row['goods_id']);
            $goods[$idx]['count'] = selled_count($row['goods_id']);
            $goods[$idx]['click_count'] = $row['click_count'];
            if (in_array($row['goods_id'], $type_array['best'])) {
                $type_goods['best'][] = $goods[$idx];
            }
            if (in_array($row['goods_id'], $type_array['new'])) {
                $type_goods['new'][] = $goods[$idx];
            }
            if (in_array($row['goods_id'], $type_array['hot'])) {
                $type_goods['hot'][] = $goods[$idx];
            }
        }
    }

    return $type_goods[$type];
}

/**
 * 获得促销商品
 *
 * @access  public
 * @return  array
 */
function get_promote_goods($cats = '') {
    $time = gmtime();
    $order_type = $GLOBALS['_CFG']['recommend_order'];

    /* 取得促销lbi的数量限制 */
    $num = get_library_number("recommend_promotion");
    $sql = 'SELECT g.goods_id, g.goods_name, g.goods_name_style, g.market_price, g.shop_price AS org_price, g.promote_price, ' .
            "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price,  g.exclusive, " .
            "promote_start_date, promote_end_date, g.goods_brief, g.goods_thumb, goods_img, b.brand_name, " .
            "g.is_best, g.is_new, g.is_hot, g.is_promote, RAND() AS rnd " .
            'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('brand') . ' AS b ON b.brand_id = g.brand_id ' .
            "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp " .
            "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' " .
            'WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 ' .
            " AND g.is_promote = 1 AND promote_start_date <= '$time' AND promote_end_date >= '$time' " . area_sql_limit_cookie();
    $sql .= $order_type == 0 ? ' ORDER BY g.sort_order, g.last_update DESC' : ' ORDER BY rnd';
    $sql .= " LIMIT $num ";
    $result = $GLOBALS['db']->getAll($sql);

    $goods = array();
    foreach ($result AS $idx => $row) {
        if ($row['promote_price'] > 0) {
            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
            $goods[$idx]['promote_price'] = $promote_price > 0 ? price_format($promote_price) : '';
        } else {
            $goods[$idx]['promote_price'] = '';
        }
        $final_price = get_final_price($row['goods_id'], 1, false);
        $goods[$idx]['final_price'] = price_format($final_price);
        $goods[$idx]['is_exclusive'] = is_exclusive($row['exclusive'], $final_price);
        $goods[$idx]['id'] = $row['goods_id'];
        $goods[$idx]['name'] = $row['goods_name'];
        $goods[$idx]['brief'] = $row['goods_brief'];
        $goods[$idx]['brand_name'] = $row['brand_name'];
        $goods[$idx]['goods_style_name'] = add_style($row['goods_name'], $row['goods_name_style']);
        $goods[$idx]['short_name'] = $GLOBALS['_CFG']['goods_name_length'] > 0 ? sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        $goods[$idx]['short_style_name'] = add_style($goods[$idx]['short_name'], $row['goods_name_style']);
        $goods[$idx]['market_price'] = price_format($row['market_price']);
        $goods[$idx]['shop_price'] = price_format($row['shop_price']);
        $goods[$idx]['thumb'] = get_pc_url() . '/' . get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $goods[$idx]['goods_img'] = get_pc_url() . '/' . get_image_path($row['goods_id'], $row['goods_img']);
        $goods[$idx]['url'] = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);
        $goods[$idx]['sell_count'] = selled_count($row['goods_id']);
        $goods[$idx]['pinglun'] = get_evaluation_sum($row['goods_id']);

        $time = gmtime();
        if ($time >= $row['promote_start_date'] && $time <= $row['promote_end_date']) {
            $goods[$idx]['gmt_end_time'] = local_date('M d, Y H:i:s', $row['promote_end_date']);
        } else {
            $goods[$idx]['gmt_end_time'] = 0;
        }
    }

    return $goods;
}

/**
 * 获得指定分类下的推荐商品
 *
 * @access  public
 * @param   string      $type       推荐类型，可以是 best, new, hot, promote
 * @param   string      $cats       分类的ID
 * @param   integer     $brand      品牌的ID
 * @param   integer     $min        商品价格下限
 * @param   integer     $max        商品价格上限
 * @param   string      $ext        商品扩展查询
 * @return  array
 */
function get_category_recommend_goods($type = '', $cats = '', $brand = 0, $min = 0, $max = 0, $ext = '') {
    $brand_where = ($brand > 0) ? " AND g.brand_id = '$brand'" : '';

    $price_where = ($min > 0) ? " AND g.shop_price >= $min " : '';
    $price_where .= ($max > 0) ? " AND g.shop_price <= $max " : '';

    $sql = 'SELECT g.goods_id, g.goods_name, g.goods_name_style, g.market_price, g.shop_price AS org_price, g.promote_price, ' .
            "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, " .
            'promote_start_date, promote_end_date, g.goods_brief, g.goods_thumb, goods_img, b.brand_name ' .
            'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('brand') . ' AS b ON b.brand_id = g.brand_id ' .
            "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp " .
            "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' " .
            'WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 ' . $brand_where . $price_where . $ext;
    $num = 0;
    $type2lib = array('best' => 'recommend_best', 'new' => 'recommend_new', 'hot' => 'recommend_hot', 'promote' => 'recommend_promotion');
    $num = get_library_number($type2lib[$type]);

    switch ($type) {
        case 'best':
            $sql .= ' AND is_best = 1';
            break;
        case 'new':
            $sql .= ' AND is_new = 1';
            break;
        case 'hot':
            $sql .= ' AND is_hot = 1';
            break;
        case 'promote':
            $time = gmtime();
            $sql .= " AND is_promote = 1 AND promote_start_date <= '$time' AND promote_end_date >= '$time'";
            break;
    }

    if (!empty($cats)) {
        $aa = get_extension_goods($cats);
        if (!empty($aa)) {
            $aa = " OR $aa";
        }
        $sql .= " AND (" . $cats . " " . $aa . ")";
    }

    $order_type = $GLOBALS['_CFG']['recommend_order'];
    $sql .= ($order_type == 0) ? ' ORDER BY g.sort_order, g.last_update DESC' : ' ORDER BY RAND()';
    $res = $GLOBALS['db']->selectLimit($sql, $num);

    $idx = 0;
    $goods = array();
    while ($row = $GLOBALS['db']->fetchRow($res)) {
        if ($row['promote_price'] > 0) {
            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
            $goods[$idx]['promote_price'] = $promote_price > 0 ? price_format($promote_price) : '';
        } else {
            $goods[$idx]['promote_price'] = '';
        }

        $goods[$idx]['id'] = $row['goods_id'];
        $goods[$idx]['name'] = $row['goods_name'];
        $goods[$idx]['brief'] = $row['goods_brief'];
        $goods[$idx]['brand_name'] = $row['brand_name'];
        $goods[$idx]['short_name'] = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
                sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        $goods[$idx]['market_price'] = price_format($row['market_price']);
        $goods[$idx]['shop_price'] = price_format($row['shop_price']);
        $goods[$idx]['thumb'] = get_pc_url() . '/' . get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $goods[$idx]['goods_img'] = get_pc_url() . '/' . get_image_path($row['goods_id'], $row['goods_img']);
        $goods[$idx]['url'] = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);

        $goods[$idx]['short_style_name'] = add_style($goods[$idx]['short_name'], $row['goods_name_style']);
        $idx++;
    }

    return $goods;
}

/**
 * 获得商品的详细信息
 *
 * @access  public
 * @param   integer     $goods_id
 * @return  void
 */
function get_goods_info($goods_id) {
    $time = gmtime();
    $sql = 'SELECT g.*, c.measure_unit, b.brand_id, b.brand_name AS goods_brand, m.type_money AS bonus_money, ' .
            'IFNULL(AVG(r.comment_rank), 0) AS comment_rank, ' .
            "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS rank_price " .
            'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('category') . ' AS c ON g.cat_id = c.cat_id ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('brand') . ' AS b ON g.brand_id = b.brand_id ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('comment') . ' AS r ' .
            'ON r.id_value = g.goods_id AND comment_type = 0 AND r.parent_id = 0 AND r.status = 1 ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('bonus_type') . ' AS m ' .
            "ON g.bonus_type_id = m.type_id AND m.send_start_date <= '$time' AND m.send_end_date >= '$time'" .
            " LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp " .
            "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' " .
            "WHERE g.goods_id = '$goods_id' AND g.is_delete = 0 " .
            "GROUP BY g.goods_id";
    $row = $GLOBALS['db']->getRow($sql);
    if ($row !== false) {
        /* 用户评论级别取整 */
        $row['comment_rank'] = ceil($row['comment_rank']) == 0 ? 5 : ceil($row['comment_rank']);

        /* 获得商品的销售价格 */
        $row['market_price'] = price_format($row['market_price']);
        $row['shop_price_formated'] = price_format($row['shop_price']);

        /* 修正促销价格 */
        if ($row['promote_price'] > 0) {
            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
        } else {
            $promote_price = 0;
        }

        /* 处理商品水印图片 */
        $watermark_img = '';

        if ($promote_price != 0) {
            $watermark_img = "watermark_promote";
        } elseif ($row['is_new'] != 0) {
            $watermark_img = "watermark_new";
        } elseif ($row['is_best'] != 0) {
            $watermark_img = "watermark_best";
        } elseif ($row['is_hot'] != 0) {
            $watermark_img = 'watermark_hot';
        }

        if ($watermark_img != '') {
            $row['watermark_img'] = $watermark_img;
        }

        $row['promote_price_org'] = $promote_price;
        $row['promote_price'] = price_format($promote_price);

        /* 修正重量显示 */
        $row['goods_weight'] = (intval($row['goods_weight']) > 0) ?
                $row['goods_weight'] . $GLOBALS['_LANG']['kilogram'] :
                ($row['goods_weight'] * 1000) . $GLOBALS['_LANG']['gram'];

        /* 修正上架时间显示 */
        $row['add_time'] = local_date($GLOBALS['_CFG']['date_format'], $row['add_time']);

        /* 促销时间倒计时 */
        $time = gmtime();
        if ($time >= $row['promote_start_date'] && $time <= $row['promote_end_date']) {
            $row['gmt_end_time'] = $row['promote_end_date'];
        } else {
            $row['gmt_end_time'] = 0;
        }

        /* 是否显示商品库存数量 */
        $row['goods_number'] = ($GLOBALS['_CFG']['use_storage'] == 1) ? $row['goods_number'] : '';

        /* 修正积分：转换为可使用多少积分（原来是可以使用多少钱的积分） */
        $row['integral'] = $GLOBALS['_CFG']['integral_scale'] ? round($row['integral'] * 100 / $GLOBALS['_CFG']['integral_scale']) : 0;

        /* 修正优惠券 */
        $row['bonus_money'] = ($row['bonus_money'] == 0) ? 0 : price_format($row['bonus_money'], false);

        /* 修正商品图片 */
        //yyy修改start
        $row['goods_img'] = get_pc_url() . '/' . get_image_path($goods_id, $row['goods_img']);
        $row['goods_thumb'] = get_pc_url() . '/' . get_image_path($goods_id, $row['goods_thumb'], true);
        //yyy修改end


        return $row;
    } else {
        return false;
    }
}

/**
 * 获得商品的属性和规格
 *
 * @access  public
 * @param   integer $goods_id
 * @return  array
 */
function get_goods_properties($goods_id) {
    /* 对属性进行重新排序和分组 */
    $sql = "SELECT attr_group " .
            "FROM " . $GLOBALS['ecs']->table('goods_type') . " AS gt, " . $GLOBALS['ecs']->table('goods') . " AS g " .
            "WHERE g.goods_id='$goods_id' AND gt.cat_id=g.goods_type";
    $grp = $GLOBALS['db']->getOne($sql);

    if (!empty($grp)) {
        $groups = explode("\n", strtr($grp, "\r", ''));
    }

    /* 获得商品的规格 */
    $sql = "SELECT a.attr_id, a.attr_name, a.attr_group, a.is_linked, a.attr_type, " .
            "g.goods_attr_id, g.attr_value, g.attr_price " .
            'FROM ' . $GLOBALS['ecs']->table('goods_attr') . ' AS g ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('attribute') . ' AS a ON a.attr_id = g.attr_id ' .
            "WHERE g.goods_id = '$goods_id' " .
            'ORDER BY a.sort_order, g.attr_price, g.goods_attr_id';
    $res = $GLOBALS['db']->getAll($sql);

    $arr['pro'] = array();     // 属性
    $arr['spe'] = array();     // 规格
    $arr['lnk'] = array();     // 关联的属性

    foreach ($res AS $row) {
        $row['attr_value'] = str_replace("\n", '<br />', $row['attr_value']);

        if ($row['attr_type'] == 0) {
            $group = (isset($groups[$row['attr_group']])) ? $groups[$row['attr_group']] : $GLOBALS['_LANG']['goods_attr'];

            $arr['pro'][$group][$row['attr_id']]['name'] = $row['attr_name'];
            $arr['pro'][$group][$row['attr_id']]['value'] = $row['attr_value'];
        } else {
            $arr['spe'][$row['attr_id']]['attr_type'] = $row['attr_type'];
            $arr['spe'][$row['attr_id']]['name'] = $row['attr_name'];
            $arr['spe'][$row['attr_id']]['values'][] = array(
                'label' => $row['attr_value'],
                'price' => $row['attr_price'],
                'format_price' => price_format(abs($row['attr_price']), false),
                'id' => $row['goods_attr_id']);
        }

        if ($row['is_linked'] == 1) {
            /* 如果该属性需要关联，先保存下来 */
            $arr['lnk'][$row['attr_id']]['name'] = $row['attr_name'];
            $arr['lnk'][$row['attr_id']]['value'] = $row['attr_value'];
        }
    }

    return $arr;
}

/**
 * 获得属性相同的商品
 *
 * @access  public
 * @param   array   $attr   // 包含了属性名称,ID的数组
 * @return  array
 */
function get_same_attribute_goods($attr) {
    $lnk = array();

    if (!empty($attr)) {
        foreach ($attr['lnk'] AS $key => $val) {
            $lnk[$key]['title'] = sprintf($GLOBALS['_LANG']['same_attrbiute_goods'], $val['name'], $val['value']);

            /* 查找符合条件的商品 */
            $sql = 'SELECT g.goods_id, g.goods_name, g.goods_thumb, g.goods_img, g.shop_price AS org_price, ' .
                    "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, " .
                    'g.market_price, g.promote_price, g.promote_start_date, g.promote_end_date ' .
                    'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
                    'LEFT JOIN ' . $GLOBALS['ecs']->table('goods_attr') . ' as a ON g.goods_id = a.goods_id ' .
                    "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp " .
                    "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' " .
                    "WHERE a.attr_id = '$key' AND g.is_on_sale=1 AND a.attr_value = '$val[value]' AND g.goods_id <> '$_REQUEST[id]' " .
                    'LIMIT ' . $GLOBALS['_CFG']['attr_related_number'];
            $res = $GLOBALS['db']->getAll($sql);

            foreach ($res AS $row) {
                $lnk[$key]['goods'][$row['goods_id']]['goods_id'] = $row['goods_id'];
                $lnk[$key]['goods'][$row['goods_id']]['goods_name'] = $row['goods_name'];
                $lnk[$key]['goods'][$row['goods_id']]['short_name'] = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
                        sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
                $lnk[$key]['goods'][$row['goods_id']]['goods_thumb'] = (empty($row['goods_thumb'])) ? $GLOBALS['_CFG']['no_picture'] : $row['goods_thumb'];
                $lnk[$key]['goods'][$row['goods_id']]['market_price'] = price_format($row['market_price']);
                $lnk[$key]['goods'][$row['goods_id']]['shop_price'] = price_format($row['shop_price']);
                $lnk[$key]['goods'][$row['goods_id']]['promote_price'] = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
                $lnk[$key]['goods'][$row['goods_id']]['url'] = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);
            }
        }
    }

    return $lnk;
}

/**
 * 获得指定商品的相册
 *
 * @access  public
 * @param   integer     $goods_id
 * @return  array
 */
function get_goods_gallery($goods_id) {
    $goods_gallery_number = !empty($GLOBALS['_CFG']['goods_gallery_number']) ? $GLOBALS['_CFG']['goods_gallery_number'] : 5;
    $sql = 'SELECT img_id, img_url, thumb_url, img_desc' .
            ' FROM ' . $GLOBALS['ecs']->table('goods_gallery') .
            " WHERE goods_id = '$goods_id' LIMIT " . $goods_gallery_number;
    $row = $GLOBALS['db']->getAll($sql);
    /* 格式化相册图片路径 */
    foreach ($row as $key => $gallery_img) {
        //yyy修改start
        $row[$key]['img_url'] = get_pc_url() . '/' . get_image_path($goods_id, $gallery_img['img_url'], false, 'gallery');
        $row[$key]['thumb_url'] = get_pc_url() . '/' . get_image_path($goods_id, $gallery_img['thumb_url'], true, 'gallery');
        //yyy修改end
    }
    return $row;
}

/**
 * 获得指定分类下的商品
 *
 * @access  public
 * @param   integer     $cat_id     分类ID
 * @param   integer     $num        数量
 * @param   string      $from       来自web/wap的调用
 * @param   string      $order_rule 指定商品排序规则
 * @return  array
 */
function assign_cat_goods($cat_id, $num = 0, $from = 'web', $order_rule = '') {
    $children = get_children($cat_id);
    $aa = get_extension_goods($children);
    if (!empty($aa)) {
        $aa = " OR $aa";
    }
    $sql = 'SELECT g.goods_id, g.goods_name,g.click_count,g.market_price, g.shop_price AS org_price, ' .
            "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, " .
            'g.promote_price, promote_start_date, promote_end_date, g.goods_brief, g.goods_thumb, g.goods_img ' .
            "FROM " . $GLOBALS['ecs']->table('goods') . ' AS g ' .
            "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp " .
            "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' " .
            'WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND ' .
            'g.is_delete = 0 AND (' . $children . ' ' . $aa . ') ';

    $order_rule = empty($order_rule) ? 'ORDER BY g.sort_order, g.goods_id DESC' : $order_rule;
    $sql .= $order_rule;
    if ($num > 0) {
        $sql .= ' LIMIT ' . $num;
    }
    $res = $GLOBALS['db']->getAll($sql);

    $goods = array();
    foreach ($res AS $idx => $row) {
        if ($row['promote_price'] > 0) {
            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
            $goods[$idx]['promote_price'] = $promote_price > 0 ? price_format($promote_price) : '';
        } else {
            $goods[$idx]['promote_price'] = '';
        }

        $goods[$idx]['id'] = $row['goods_id'];
        $goods[$idx]['name'] = $row['goods_name'];
        $goods[$idx]['brief'] = $row['goods_brief'];
        $goods[$idx]['market_price'] = price_format($row['market_price']);
        $goods[$idx]['short_name'] = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
                sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        $goods[$idx]['shop_price'] = price_format($row['shop_price']);
        $goods[$idx]['thumb'] = get_pc_url() . '/' . get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $goods[$idx]['goods_img'] = get_pc_url() . '/' . get_image_path($row['goods_id'], $row['goods_img']);
        $goods[$idx]['url'] = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);
        $goods[$idx]['count'] = selled_count($row['goods_id']);
        $goods[$idx]['click_count'] = $row['click_count'];
    }

    if ($from == 'web') {
        $GLOBALS['smarty']->assign('cat_goods_' . $cat_id, $goods);
    } elseif ($from == 'wap') {
        $cat['goods'] = $goods;
    }

    /* 分类信息 */
    $sql = 'SELECT cat_name FROM ' . $GLOBALS['ecs']->table('category') . " WHERE cat_id = '$cat_id'";
    $cat['name'] = $GLOBALS['db']->getOne($sql);
    $cat['url'] = build_uri('category', array('cid' => $cat_id), $cat['name']);
    $cat['id'] = $cat_id;

    return $cat;
}

/**
 * 获得指定的品牌下的商品
 *
 * @access  public
 * @param   integer     $brand_id       品牌的ID
 * @param   integer     $num            数量
 * @param   integer     $cat_id         分类编号
 * @param   string      $order_rule     指定商品排序规则
 * @return  void
 */
function assign_brand_goods($brand_id, $num = 0, $cat_id = 0, $order_rule = '') {
    $sql = 'SELECT g.goods_id, g.goods_name, g.market_price, g.shop_price AS org_price, ' .
            "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, " .
            'g.promote_price, g.promote_start_date, g.promote_end_date, g.goods_brief, g.goods_thumb, g.goods_img ' .
            'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
            "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp " .
            "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' " .
            "WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 AND g.brand_id = '$brand_id'";

    if ($cat_id > 0) {
        $sql .= get_children($cat_id);
    }

    $order_rule = empty($order_rule) ? ' ORDER BY g.sort_order, g.goods_id DESC' : $order_rule;
    $sql .= $order_rule;
    if ($num > 0) {
        $res = $GLOBALS['db']->selectLimit($sql, $num);
    } else {
        $res = $GLOBALS['db']->query($sql);
    }

    $idx = 0;
    $goods = array();
    while ($row = $GLOBALS['db']->fetchRow($res)) {
        if ($row['promote_price'] > 0) {
            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
        } else {
            $promote_price = 0;
        }

        $goods[$idx]['id'] = $row['goods_id'];
        $goods[$idx]['name'] = $row['goods_name'];
        $goods[$idx]['short_name'] = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
                sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        $goods[$idx]['market_price'] = price_format($row['market_price']);
        $goods[$idx]['shop_price'] = price_format($row['shop_price']);
        $goods[$idx]['promote_price'] = $promote_price > 0 ? price_format($promote_price) : '';
        $goods[$idx]['brief'] = $row['goods_brief'];
        $goods[$idx]['thumb'] = get_pc_url() . '/' . get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $goods[$idx]['goods_img'] = get_pc_url() . '/' . get_image_path($row['goods_id'], $row['goods_img']);
        $goods[$idx]['url'] = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);

        $idx++;
    }

    /* 分类信息 */
    $sql = 'SELECT brand_name FROM ' . $GLOBALS['ecs']->table('brand') . " WHERE brand_id = '$brand_id'";

    $brand['id'] = $brand_id;
    $brand['name'] = $GLOBALS['db']->getOne($sql);
    $brand['url'] = build_uri('brand', array('bid' => $brand_id), $brand['name']);

    $brand_goods = array('brand' => $brand, 'goods' => $goods);

    return $brand_goods;
}

/**
 * 获得所有扩展分类属于指定分类的所有商品ID
 *
 * @access  public
 * @param   string $cat_id     分类查询字符串
 * @return  string
 */
function get_extension_goods($cats) {
    $extension_goods_array = '';
    $sql = 'SELECT goods_id FROM ' . $GLOBALS['ecs']->table('goods_cat') . " AS g WHERE $cats";
    $extension_goods_array = $GLOBALS['db']->getCol($sql);
    //if (!empty($extension_goods_array)) {
        return db_create_in($extension_goods_array, 'g.goods_id');
//    } else {
//        return "";
//    }
}

/**
 * 判断某个商品是否正在特价促销期
 *
 * @access  public
 * @param   float   $price      促销价格
 * @param   string  $start      促销开始日期
 * @param   string  $end        促销结束日期
 * @return  float   如果还在促销期则返回促销价，否则返回0
 */
function bargain_price($price, $start, $end) {
    if ($price == 0) {
        return 0;
    } else {
        $time = gmtime();
        if ($time >= $start && $time <= $end) {
            return $price;
        } else {
            return 0;
        }
    }
}

/**
 * 获得指定的规格的价格
 *
 * @access  public
 * @param   mix     $spec   规格ID的数组或者逗号分隔的字符串
 * @return  void
 */
function spec_price($spec ,$goodid=0) {
    if (!empty($spec)) {
        
        // 查看系统是否设置属性固定价格
        if ($goodid > 0 )
        {
            $attrids = $spec;
            if(!is_array($attrids)){
                $attrids = explode(',',$attrids);
            }
        
            $goods_attr_array = sort_goods_attr_id_array($attrids);
            if(isset($goods_attr_array['sort']))
            {
                $goods_attr = implode('|', $goods_attr_array['sort']);
        
                $sql = "SELECT goods_id,goods_attr_price  FROM " . $GLOBALS['ecs']->table('products') . "  WHERE goods_id = '".$goodid."' AND goods_attr = '".$goods_attr."' LIMIT 0, 1";
                $row = $GLOBALS['db']->getRow($sql);
                if ($row['goods_attr_price'] >= 0 )
                {
                    return $row['goods_attr_price'];
                }
            }
        }
        
        if (is_array($spec)) {
            foreach ($spec as $key => $val) {
                $spec[$key] = addslashes($val);
            }
        } else {
            $spec = addslashes($spec);
        }

        $where = db_create_in($spec, 'goods_attr_id');

        $sql = 'SELECT SUM(attr_price) AS attr_price FROM ' . $GLOBALS['ecs']->table('goods_attr') . " WHERE $where";
        $price = floatval($GLOBALS['db']->getOne($sql));
    } else {
        $price = 0;
    }

    return $price;
}

/**
 * 取得团购活动信息
 * @param   int     $group_buy_id   团购活动id
 * @param   int     $current_num    本次购买数量（计算当前价时要加上的数量）
 * @return  array
 *                  status          状态：
 */
function group_buy_info($group_buy_id, $current_num = 0) {
    /* 取得团购活动信息 */
    $group_buy_id = intval($group_buy_id);
    $sql = "SELECT *, act_id AS group_buy_id, act_desc AS group_buy_desc, start_time AS start_date, end_time AS end_date " .
            "FROM " . $GLOBALS['ecs']->table('goods_activity') .
            "WHERE act_id = '$group_buy_id' " .
            "AND act_type = '" . GAT_GROUP_BUY . "'";
    $group_buy = $GLOBALS['db']->getRow($sql);

    /* 如果为空，返回空数组 */
    if (empty($group_buy)) {
        return array();
    }

    $ext_info = unserialize($group_buy['ext_info']);
    $group_buy = array_merge($group_buy, $ext_info);

    /* 格式化时间 */
    $group_buy['formated_start_date'] = local_date('Y-m-d H:i', $group_buy['start_time']);
    $group_buy['formated_end_date'] = local_date('Y-m-d H:i', $group_buy['end_time']);

    /* 格式化保证金 */
    $group_buy['formated_deposit'] = price_format($group_buy['deposit'], false);

    /* 处理价格阶梯 */
    $price_ladder = $group_buy['price_ladder'];
    if (!is_array($price_ladder) || empty($price_ladder)) {
        $price_ladder = array(array('amount' => 0, 'price' => 0));
    } else {
        foreach ($price_ladder as $key => $amount_price) {
            $price_ladder[$key]['formated_price'] = price_format($amount_price['price'], false);
        }
    }
    $group_buy['price_ladder'] = $price_ladder;

    /* 统计信息 */
    $stat = group_buy_stat($group_buy_id, $group_buy['deposit']);
    $group_buy = array_merge($group_buy, $stat);

    /* 计算当前价 */
    $cur_price = $price_ladder[0]['price']; // 初始化
    $cur_amount = $stat['valid_goods'] + $current_num; // 当前数量
    foreach ($price_ladder as $amount_price) {
        if ($cur_amount >= $amount_price['amount']) {
            $cur_price = $amount_price['price'];
        } else {
            break;
        }
    }
    $group_buy['cur_price'] = $cur_price;
    $group_buy['formated_cur_price'] = price_format($cur_price, false);

    /* 最终价 */
    $group_buy['trans_price'] = $group_buy['cur_price'];
    $group_buy['formated_trans_price'] = $group_buy['formated_cur_price'];
    $group_buy['trans_amount'] = $group_buy['valid_goods'];

    /* 状态 */
    $group_buy['status'] = group_buy_status($group_buy);
    if (isset($GLOBALS['_LANG']['gbs'][$group_buy['status']])) {
        $group_buy['status_desc'] = $GLOBALS['_LANG']['gbs'][$group_buy['status']];
    }

    $group_buy['start_time'] = $group_buy['formated_start_date'];
    $group_buy['end_time'] = $group_buy['formated_end_date'];

    return $group_buy;
}

/*
 * 取得某团购活动统计信息
 * @param   int     $group_buy_id   团购活动id
 * @param   float   $deposit        保证金
 * @return  array   统计信息
 *                  total_order     总订单数
 *                  total_goods     总商品数
 *                  valid_order     有效订单数
 *                  valid_goods     有效商品数
 */

function group_buy_stat($group_buy_id, $deposit) {
    $group_buy_id = intval($group_buy_id);

    /* 取得团购活动商品ID */
    $sql = "SELECT goods_id " .
            "FROM " . $GLOBALS['ecs']->table('goods_activity') .
            "WHERE act_id = '$group_buy_id' " .
            "AND act_type = '" . GAT_GROUP_BUY . "'";
    $group_buy_goods_id = $GLOBALS['db']->getOne($sql);

    /* 取得总订单数和总商品数 */
    $sql = "SELECT COUNT(*) AS total_order, SUM(g.goods_number) AS total_goods " .
            "FROM " . $GLOBALS['ecs']->table('order_info') . " AS o, " .
            $GLOBALS['ecs']->table('order_goods') . " AS g " .
            " WHERE o.order_id = g.order_id " .
            "AND o.extension_code = 'group_buy' " .
            "AND o.extension_id = '$group_buy_id' " .
            "AND g.goods_id = '$group_buy_goods_id' " .
            "AND (order_status = '" . OS_CONFIRMED . "' OR order_status = '" . OS_UNCONFIRMED . "')";
    $stat = $GLOBALS['db']->getRow($sql);
    if ($stat['total_order'] == 0) {
        $stat['total_goods'] = 0;
    }

    /* 取得有效订单数和有效商品数 */
    $deposit = floatval($deposit);
    if ($deposit > 0 && $stat['total_order'] > 0) {
        $sql .= " AND (o.money_paid + o.surplus) >= '$deposit'";
        $row = $GLOBALS['db']->getRow($sql);
        $stat['valid_order'] = $row['total_order'];
        if ($stat['valid_order'] == 0) {
            $stat['valid_goods'] = 0;
        } else {
            $stat['valid_goods'] = $row['total_goods'];
        }
    } else {
        $stat['valid_order'] = $stat['total_order'];
        $stat['valid_goods'] = $stat['total_goods'];
    }

    return $stat;
}

/**
 * 获得团购的状态
 *
 * @access  public
 * @param   array
 * @return  integer
 */
function group_buy_status($group_buy) {
    $now = gmtime();
    if ($group_buy['is_finished'] == 0) {
        /* 未处理 */
        if ($now < $group_buy['start_time']) {
            $status = GBS_PRE_START;
        } elseif ($now > $group_buy['end_time']) {
            $status = GBS_FINISHED;
        } else {
            if ($group_buy['restrict_amount'] == 0 || $group_buy['valid_goods'] < $group_buy['restrict_amount']) {
                $status = GBS_UNDER_WAY;
            } else {
                $status = GBS_FINISHED;
            }
        }
    } elseif ($group_buy['is_finished'] == GBS_SUCCEED) {
        /* 已处理，团购成功 */
        $status = GBS_SUCCEED;
    } elseif ($group_buy['is_finished'] == GBS_FAIL) {
        /* 已处理，团购失败 */
        $status = GBS_FAIL;
    }

    return $status;
}

/**
 * 取得拍卖活动信息
 * @param   int     $act_id     活动id
 * @return  array
 */
function auction_info($act_id, $config = false) {
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('goods_activity') . " WHERE act_id = '$act_id'";
    $auction = $GLOBALS['db']->getRow($sql);
    if ($auction['act_type'] != GAT_AUCTION) {
        return array();
    }
    $auction['status_no'] = auction_status($auction);
    if ($config == true) {

        $auction['start_time'] = local_date('Y-m-d H:i', $auction['start_time']);
        $auction['end_time'] = local_date('Y-m-d H:i', $auction['end_time']);
    } else {
        $auction['start_time'] = local_date($GLOBALS['_CFG']['time_format'], $auction['start_time']);
        $auction['end_time'] = local_date($GLOBALS['_CFG']['time_format'], $auction['end_time']);
    }
    $ext_info = unserialize($auction['ext_info']);
    $auction = array_merge($auction, $ext_info);
    $auction['formated_start_price'] = price_format($auction['start_price']);
    $auction['formated_end_price'] = price_format($auction['end_price']);
    $auction['formated_amplitude'] = price_format($auction['amplitude']);
    $auction['formated_deposit'] = price_format($auction['deposit']);

    /* 查询出价用户数和最后出价 */
    $sql = "SELECT COUNT(DISTINCT bid_user) FROM " . $GLOBALS['ecs']->table('auction_log') .
            " WHERE act_id = '$act_id'";
    $auction['bid_user_count'] = $GLOBALS['db']->getOne($sql);
    if ($auction['bid_user_count'] > 0) {
        $sql = "SELECT a.*, u.user_name " .
                "FROM " . $GLOBALS['ecs']->table('auction_log') . " AS a, " .
                $GLOBALS['ecs']->table('users') . " AS u " .
                "WHERE a.bid_user = u.user_id " .
                "AND act_id = '$act_id' " .
                "ORDER BY a.log_id DESC";
        $row = $GLOBALS['db']->getRow($sql);
        $row['formated_bid_price'] = price_format($row['bid_price'], false);
        $row['bid_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['bid_time']);
        $auction['last_bid'] = $row;
    }

    /* 查询已确认订单数 */
    if ($auction['status_no'] > 1) {
        $sql = "SELECT COUNT(*)" .
                " FROM " . $GLOBALS['ecs']->table('order_info') .
                " WHERE extension_code = 'auction'" .
                " AND extension_id = '$act_id'" .
                " AND order_status " . db_create_in(array(OS_CONFIRMED, OS_UNCONFIRMED));
        $auction['order_count'] = $GLOBALS['db']->getOne($sql);
    } else {
        $auction['order_count'] = 0;
    }

    /* 当前价 */
    $auction['current_price'] = isset($auction['last_bid']) ? $auction['last_bid']['bid_price'] : $auction['start_price'];
    $auction['formated_current_price'] = price_format($auction['current_price'], false);

    return $auction;
}

/**
 * 取得拍卖活动出价记录
 * @param   int     $act_id     活动id
 * @return  array
 */
function auction_log($act_id) {
    $log = array();
    $sql = "SELECT a.*, u.user_name " .
            "FROM " . $GLOBALS['ecs']->table('auction_log') . " AS a," .
            $GLOBALS['ecs']->table('users') . " AS u " .
            "WHERE a.bid_user = u.user_id " .
            "AND act_id = '$act_id' " .
            "ORDER BY a.log_id DESC";
    $res = $GLOBALS['db']->query($sql);
    while ($row = $GLOBALS['db']->fetchRow($res)) {
        $row['bid_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['bid_time']);
        $row['formated_bid_price'] = price_format($row['bid_price'], false);
        $log[] = $row;
    }

    return $log;
}

/**
 * 计算拍卖活动状态（注意参数一定是原始信息）
 * @param   array   $auction    拍卖活动原始信息
 * @return  int
 */
function auction_status($auction) {
    $now = gmtime();
    if ($auction['is_finished'] == 0) {
        if ($now < $auction['start_time']) {
            return PRE_START; // 未开始
        } elseif ($now > $auction['end_time']) {
            return FINISHED; // 已结束，未处理
        } else {
            return UNDER_WAY; // 进行中
        }
    } elseif ($auction['is_finished'] == 1) {
        return FINISHED; // 已结束，未处理
    } else {
        return SETTLED; // 已结束，已处理
    }
}

/**
 * 取得商品信息
 * @param   int     $goods_id   商品id
 * @return  array
 */
function goods_info($goods_id) {
    $sql = "SELECT g.*, b.brand_name " .
            "FROM " . $GLOBALS['ecs']->table('goods') . " AS g " .
            "LEFT JOIN " . $GLOBALS['ecs']->table('brand') . " AS b ON g.brand_id = b.brand_id " .
            "WHERE g.goods_id = '$goods_id'";
    $row = $GLOBALS['db']->getRow($sql);
    if (!empty($row)) {
        /* 修正重量显示 */
        $row['goods_weight'] = (intval($row['goods_weight']) > 0) ?
                $row['goods_weight'] . $GLOBALS['_LANG']['kilogram'] :
                ($row['goods_weight'] * 1000) . $GLOBALS['_LANG']['gram'];

        /* 修正图片 */
        $row['goods_img'] = get_pc_url() . '/' . get_image_path($goods_id, $row['goods_img']);
    }

    return $row;
}

/**
 * 取得优惠活动信息
 * @param   int     $act_id     活动id
 * @return  array
 */
function favourable_info($act_id) {
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('favourable_activity') .
            " WHERE act_id = '$act_id'";
    $row = $GLOBALS['db']->getRow($sql);
    if (!empty($row)) {
        $row['start_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['start_time']);
        $row['end_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['end_time']);
        $row['formated_min_amount'] = price_format($row['min_amount']);
        $row['formated_max_amount'] = price_format($row['max_amount']);
        $row['gift'] = unserialize($row['gift']);
        if ($row['act_type'] == FAT_GOODS) {
            $row['act_type_ext'] = round($row['act_type_ext']);
        }
    }

    return $row;
}

/**
 * 批发信息
 * @param   int     $act_id     活动id
 * @return  array
 */
function wholesale_info($act_id) {
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('wholesale') .
            " WHERE act_id = '$act_id'";
    $row = $GLOBALS['db']->getRow($sql);
    if (!empty($row)) {
        $row['price_list'] = unserialize($row['prices']);
    }

    return $row;
}

/**
 * 添加商品名样式
 * @param   string     $goods_name     商品名称
 * @param   string     $style          样式参数
 * @return  string
 */
function add_style($goods_name, $style) {
    $goods_style_name = $goods_name;

    $arr = explode('+', $style);

    $font_color = !empty($arr[0]) ? $arr[0] : '';
    $font_style = !empty($arr[1]) ? $arr[1] : '';

    if ($font_color != '') {
        $goods_style_name = '<font color=' . $font_color . '>' . $goods_style_name . '</font>';
    }
    if ($font_style != '') {
        $goods_style_name = '<' . $font_style . '>' . $goods_style_name . '</' . $font_style . '>';
    }
    return $goods_style_name;
}

/**
 * 取得商品属性
 * @param   int     $goods_id   商品id
 * @return  array
 */
function get_goods_attr($goods_id) {
    $attr_list = array();
    $sql = "SELECT a.attr_id, a.attr_name " .
            "FROM " . $GLOBALS['ecs']->table('goods') . " AS g, " . $GLOBALS['ecs']->table('attribute') . " AS a " .
            "WHERE g.goods_id = '$goods_id' " .
            "AND g.goods_type = a.cat_id " .
            "AND a.attr_type = 1";
    $attr_id_list = $GLOBALS['db']->getCol($sql);
    $res = $GLOBALS['db']->query($sql);
    while ($attr = $GLOBALS['db']->fetchRow($res)) {
        if (defined('ECS_ADMIN')) {
            $attr['goods_attr_list'] = array(0 => $GLOBALS['_LANG']['select_please']);
        } else {
            $attr['goods_attr_list'] = array();
        }
        $attr_list[$attr['attr_id']] = $attr;
    }

    $sql = "SELECT attr_id, goods_attr_id, attr_value " .
            "FROM " . $GLOBALS['ecs']->table('goods_attr') .
            " WHERE goods_id = '$goods_id' " .
            "AND attr_id " . db_create_in($attr_id_list);
    $res = $GLOBALS['db']->query($sql);
    while ($goods_attr = $GLOBALS['db']->fetchRow($res)) {
        $attr_list[$goods_attr['attr_id']]['goods_attr_list'][$goods_attr['goods_attr_id']] = $goods_attr['attr_value'];
    }

    return $attr_list;
}

/**
 * 获得购物车中商品的配件
 *
 * @access  public
 * @param   array     $goods_list
 * @return  array
 */
function get_goods_fittings($goods_list = array()) {
    $temp_index = 0;
    $arr = array();

    $sql = 'SELECT gg.parent_id, ggg.goods_name AS parent_name, gg.goods_id, gg.goods_price, g.goods_name, g.goods_thumb, g.goods_img, g.shop_price AS org_price, ' .
            "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price " .
            'FROM ' . $GLOBALS['ecs']->table('group_goods') . ' AS gg ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('goods') . 'AS g ON g.goods_id = gg.goods_id ' .
            "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp " .
            "ON mp.goods_id = gg.goods_id AND mp.user_rank = '$_SESSION[user_rank]' " .
            "LEFT JOIN " . $GLOBALS['ecs']->table('goods') . " AS ggg ON ggg.goods_id = gg.parent_id " .
            "WHERE gg.parent_id " . db_create_in($goods_list) . " AND g.is_delete = 0 AND g.is_on_sale = 1 " .
            "ORDER BY gg.parent_id, gg.goods_id";

    $res = $GLOBALS['db']->query($sql);

    while ($row = $GLOBALS['db']->fetchRow($res)) {
        $arr[$temp_index]['parent_id'] = $row['parent_id']; //配件的基本件ID
        $arr[$temp_index]['parent_name'] = $row['parent_name']; //配件的基本件的名称
        $arr[$temp_index]['parent_short_name'] = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
                sub_str($row['parent_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['parent_name']; //配件的基本件显示的名称
        $arr[$temp_index]['goods_id'] = $row['goods_id']; //配件的商品ID
        $arr[$temp_index]['goods_name'] = $row['goods_name']; //配件的名称
        $arr[$temp_index]['short_name'] = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
                sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name']; //配件显示的名称
        $arr[$temp_index]['fittings_price'] = price_format($row['goods_price']); //配件价格
        $arr[$temp_index]['shop_price'] = price_format($row['shop_price']); //配件原价格
        $arr[$temp_index]['goods_thumb'] = get_pc_url() . '/' . get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $arr[$temp_index]['goods_img'] = get_pc_url() . '/' . get_image_path($row['goods_id'], $row['goods_img']);
        $arr[$temp_index]['url'] = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);
        $temp_index ++;
    }

    return $arr;
}

/**
 * 取指定规格的货品信息
 *
 * @access      public
 * @param       string      $goods_id
 * @param       array       $spec_goods_attr_id
 * @return      array
 */
function get_products_info($goods_id, $spec_goods_attr_id) {
    $return_array = array();

    if (empty($spec_goods_attr_id) || !is_array($spec_goods_attr_id) || empty($goods_id)) {
        return $return_array;
    }

    $goods_attr_array = sort_goods_attr_id_array($spec_goods_attr_id);

    if (isset($goods_attr_array['sort'])) {
        $goods_attr = implode('|', $goods_attr_array['sort']);

        $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('products') . " WHERE goods_id = '$goods_id' AND goods_attr = '$goods_attr' LIMIT 0, 1";
        $return_array = $GLOBALS['db']->getRow($sql);
    }
    return $return_array;
}

function get_wap_parent_id_tree($parent_id) {
    $three_c_arr = array();
    $sql = 'SELECT count(*) FROM ' . $GLOBALS['ecs']->table('category') . " WHERE parent_id = '$parent_id' AND is_show = 1 ";
    if ($GLOBALS['db']->getOne($sql)) {
        $child_sql = 'SELECT cat_id, cat_name, parent_id, is_show ' .
                'FROM ' . $GLOBALS['ecs']->table('category') .
                "WHERE parent_id = '$parent_id' AND is_show = 1 ORDER BY sort_order ASC, cat_id ASC ";
        $res = $GLOBALS['db']->getAll($child_sql);
        foreach ($res AS $row) {
            if ($row['is_show'])
                $three_c_arr[$row['cat_id']]['id'] = $row['cat_id'];
            $three_c_arr[$row['cat_id']]['name'] = $row['cat_name'];
            $three_c_arr[$row['cat_id']]['url'] = build_uri('category', array('cid' => $row['cat_id']), $row['cat_name']);
        }
    }
    return $three_c_arr;
}

function selled_wap_count($goods_id) {
    $sql = "SELECT ghost_count FROM " . $GLOBALS['ecs']->table('goods') . " where goods_id=" . $goods_id;
    $ghost_count = $GLOBALS['db']->getOne($sql);

    $sql = "select sum(goods_number) as count from " . $GLOBALS['ecs']->table('order_goods') . "where goods_id ='" . $goods_id . "'";
    $res = $GLOBALS['db']->getOne($sql);
    if ($res > 0) {
        $res = $res;
    } else {
        $res = 0;
    }
    $res += $ghost_count;
    return $res;
}

function get_evaluation_sum($goods_id) {
    $sql = "SELECT count(*) FROM " . $GLOBALS['ecs']->table('comment') . " WHERE status=1 and  comment_type =0 and id_value =" . $goods_id; //status=1表示通过了的评论才算  comment_type =0表示针对商品的评价
    return $GLOBALS['db']->getOne($sql);
}

//获得订单数量
function selled_count($goods_id) {
    return selled_wap_count($goods_id);
//     $sql= "select sum(goods_number) as count from ".$GLOBALS['ecs']->table('order_goods')."where goods_id ='".$goods_id."'";
//     $res = $GLOBALS['db']->getOne($sql);
//     if($res>0)
//     {
//     return $res;
//     }
//     else
//     {
//       return('0');
//     }
}

//获得分类信息
function get_wap_cat_info($cat_id) {
    /* 分类信息 */
    $cat = array();
    $sql = 'SELECT cat_name FROM ' . $GLOBALS['ecs']->table('category') . " WHERE cat_id = '$cat_id'";
    $cat['name'] = $GLOBALS['db']->getOne($sql);
    $cat['url'] = build_uri('category', array('cid' => $cat_id), $cat['name']);
    $cat['id'] = $cat_id;

    return $cat;
}

/**
 * 首页获取指定分类产品
 *
 * @access      public
 * @param       string      $cat_id53_best_goods
 * @param       array       $cat_id53_best_goods
 * @return      array
 */
function get_cat_id_goods_list($cat_id = '', $num = '') {
    $sql = 'Select g.goods_id, g.cat_id,c.parent_id, g.goods_name, g.goods_name_style, g.market_price, g.shop_price AS org_price, g.promote_price, ' .
            "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, " .
            "promote_start_date, promote_end_date, g.goods_brief, g.goods_thumb, goods_img, " .
            "g.is_best, g.is_new, g.is_hot, g.is_promote " .
            'FROM ' . $GLOBALS ['ecs']->table('goods') . ' AS g ' .
            'LEFT JOIN ' . $GLOBALS ['ecs']->table('category') . ' AS c ON c.cat_id = g.cat_id ' .
            "LEFT JOIN " . $GLOBALS ['ecs']->table('member_price') . " AS mp " .
            "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' " .
            "Where g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 " .
            $sql .= " AND (c.parent_id =" . $cat_id . " OR g.cat_id = " . $cat_id . " OR g.cat_id " . db_create_in(array_unique(array_merge(array(
                $cat_id
                                    ), array_keys(cat_list($cat_id, 0, false))))) . ")";
    $sql .= " LIMIT $num";
    $res = $GLOBALS ['db']->getAll($sql);
    $goods = array();
    foreach ($res as $idx => $row) {
        $goods [$idx] ['id'] = $row ['article_id'];
        $goods [$idx] ['id'] = $row ['goods_id'];
        $goods [$idx] ['name'] = $row ['goods_name'];
        $goods [$idx] ['brief'] = $row ['goods_brief'];
        $goods [$idx] ['brand_name'] = $row ['brand_name'];
        $goods [$idx] ['goods_style_name'] = add_style($row ['goods_name'], $row ['goods_name_style']);
        $goods [$idx] ['short_name'] = $GLOBALS ['_CFG'] ['goods_name_length'] > 0 ? sub_str($row ['goods_name'], $GLOBALS ['_CFG'] ['goods_name_length']) : $row ['goods_name'];
        $goods [$idx] ['short_style_name'] = add_style($goods [$idx] ['short_name'], $row ['goods_name_style']);
        $goods [$idx] ['market_price'] = price_format($row ['market_price']);
        $goods [$idx] ['shop_price'] = price_format($row ['shop_price']);
        $goods [$idx] ['thumb'] = empty($row ['goods_thumb']) ? $GLOBALS ['_CFG'] ['no_picture'] : $row ['goods_thumb'];
        $goods [$idx] ['goods_img'] = empty($row ['goods_img']) ? $GLOBALS ['_CFG'] ['no_picture'] : $row ['goods_img'];
        $goods [$idx] ['url'] = build_uri('goods', array(
            'gid' => $row ['goods_id']
                ), $row ['goods_name']);
    }
    return $goods;
}

/* 代码增加_start  By   www.ecshop68.com */

function is_exist_prod($first_arr, $one, $prod_exist_arr) {
    if (empty($prod_exist_arr)) {
        return 0;
    }
    $first_arr[] = $one;

    $all_valid = 0;
    foreach ($prod_exist_arr AS $item_exist) {
        $first_exist = 1;
        foreach ($first_arr AS $first) {
            if (!strstr($item_exist, '|' . $first . '|')) {
                $first_exist = 0;
                break;
            }
        }
        if ($first_exist) {
            $all_valid = 1;
            break;
        }
    }
    return $all_valid;
}

/**
 * 判断当前商品是否为预售商品
 * @param unknown $goods_id
 * @return int pre_sale_id
 */
function is_pre_sale_goods($goods_id) {
    $sql = "SELECT act_id " .
            "FROM " . $GLOBALS['ecs']->table('goods_activity') . " AS b " .
            "WHERE goods_id = '$goods_id' " .
            "AND is_finished < " . PSS_FINISHED . " " .
            "AND act_type = '" . GAT_PRE_SALE . "'";
    $pre_sale_id = $GLOBALS['db']->getOne($sql);

    if (empty($pre_sale_id)) {
        return null;
    }

    return $pre_sale_id;
}

/* 代码增加_end  By   www.ecshop68.com */
?>