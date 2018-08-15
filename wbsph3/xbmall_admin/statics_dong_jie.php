<?php

/**
 * 消费分红权 冻结统计
 * ============================================================================
 * 版权所有 2005-2010 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: yehuaixiao $
 * $Id: order.php 17219 2011-01-27 10:49:19Z yehuaixiao $
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/* ------------------------------------------------------ */
//-- 订单查询
/* ------------------------------------------------------ */

if ($_REQUEST['act'] == 'list') {
    /* 检查权限 */
    admin_priv('finance_statics');

    /* 模板赋值 */
    $smarty->assign('ur_here', "消费冻结统计");
 
    $smarty->assign('full_page', 1);


    $agency_list = get_finance_statics();
    $smarty->assign('data_list', $agency_list['agency']);
    $smarty->assign('filter', $agency_list['filter']);
    $smarty->assign('record_count', $agency_list['record_count']);
    $smarty->assign('page_count', $agency_list['page_count']);

    /* 排序标记 */
    $sort_flag = sort_flag($agency_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);
    $smarty->assign ( 'pager', $agency_list['pager']);
    assign_query_info();
    $smarty->display('statics_dong_jie.htm');
}

/* ------------------------------------------------------ */
//-- 排序、分页、查询
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'query') {
    /* 检查权限 */
    admin_priv('finance_statics');
    $agency_list = get_finance_statics();
    $smarty->assign('data_list', $agency_list['agency']);
    $smarty->assign('filter', $agency_list['filter']);
    $smarty->assign('record_count', $agency_list['record_count']);
    $smarty->assign('page_count', $agency_list['page_count']);
    $smarty->assign ( 'pager', $agency_list['pager']);
    /* 排序标记 */
    $sort_flag = sort_flag($agency_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('statics_dong_jie.htm'), '', array('filter' => $agency_list['filter'], 'page_count' => $agency_list['page_count']));
}

/* ------------------------------------------------------ */

//-- 
/* ------------------------------------------------------ */
function get_finance_statics() {
    /* 初始化分页参数 */
    $filter = array();
    $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'id' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
   
    $sql = "SELECT  count(0)   FROM `xbmall_users`  WHERE  zsq_step!=0   ";
    $filter ['record_count'] = $GLOBALS ['db']->getOne ( $sql );
 
    $filter = page_and_size($filter);
    
    
    /* 查询记录 */
    $sql = "   SELECT a.* from (SELECT mobile_phone,
	real_name,
	zsq_step,
	zsq_step * 120 AS zong_ji_fen,
	give_points_step / 2 AS yi_fa,
	(
		zsq_step * 120 - give_points_step / 2
	) AS sheng_yu,
	zsq_step * 120 AS zong_gong, '冻结' as  type 
FROM
	`xbmall_users`
WHERE
	frozen_zsq != 0
ORDER BY
	zsq_step DESC,
	zsq_step - zsq ASC )a 
UNION all  
SELECT b.* from  ( 
SELECT
	mobile_phone,
	real_name,
	zsq_step,
	zsq_step * 120 AS zong_ji_fen,
	give_points_step / 2 AS yi_fa,
	(
		zsq_step * 120 - give_points_step / 2
	) AS sheng_yu,
	zsq_step * 120 AS zong_gong,
 '未冻结' as  type 
FROM
	`xbmall_users`
WHERE
	zsq_step != 0
ORDER BY
	zsq_step DESC,
	zsq_step - zsq ASC)b  ";
    set_filter($filter, $sql);
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);
    $arr = array();
    while ($rows = $GLOBALS['db']->fetchRow($res)) {
        $arr[] = $rows;
    }
    
    $pager = get_pager('statics_dong_jie.php', array('act' => 'list'), $filter['record_count'], $filter['page'], $filter['page_size']);
    
    return array('agency' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count'],'pager'=>$pager);
}
   

/**
 *  生成给pager.lbi赋值的数组
 *
 * @access  public
 * @param   string      $url        分页的链接地址(必须是带有参数的地址，若不是可以伪造一个无用参数)
 * @param   array       $param      链接参数 key为参数名，value为参数值
 * @param   int         $record     记录总数量
 * @param   int         $page       当前页数
 * @param   int         $size       每页大小
 *
 * @return  array       $pager
 */
function get_pager($url, $param, $record_count, $page = 1, $size = 10) {
	$size = intval($size);
	if ($size < 1) {
		$size = 10;
	}
	
	$page = intval($page);
	if ($page < 1) {
		$page = 1;
	}
	
	$record_count = intval($record_count);
	
	$page_count = $record_count > 0 ? intval(ceil($record_count / $size)) : 1;
	if ($page > $page_count) {
		$page = $page_count;
	}
	/* 分页样式 */
	$pager['styleid'] = isset($GLOBALS['_CFG']['page_style']) ? intval($GLOBALS['_CFG']['page_style']) : 0;
	
	$page_prev = ($page > 1) ? $page - 1 : 1;
	$page_next = ($page < $page_count) ? $page + 1 : $page_count;
	/* 将参数合成url字串 */
	$param_url = '?';
	foreach ($param AS $key => $value) {
		$param_url .= $key . '=' . $value . '&';
	}
	
	$pager['url'] = $url;
	$pager['start'] = ($page - 1) * $size;
	$pager['page'] = $page;
	$pager['size'] = $size;
	$pager['record_count'] = $record_count;
	$pager['page_count'] = $page_count;
	
	if ($pager['styleid'] == 0) {
		$pager['page_first'] = $url . $param_url . 'page=1';
		$pager['page_prev'] = $url . $param_url . 'page=' . $page_prev;
		$pager['page_next'] = $url . $param_url . 'page=' . $page_next;
		$pager['page_last'] = $url . $param_url . 'page=' . $page_count;
		$pager['array'] = array();
		for ($i = 1; $i <= $page_count; $i++) {
			$pager['array'][$i] = $i;
		}
	} else {
		$_pagenum = 10;     // 显示的页码
		$_offset = 2;       // 当前页偏移值
		$_from = $_to = 0;  // 开始页, 结束页
		if ($_pagenum > $page_count) {
			$_from = 1;
			$_to = $page_count;
		} else {
			$_from = $page - $_offset;
			$_to = $_from + $_pagenum - 1;
			if ($_from < 1) {
				$_to = $page + 1 - $_from;
				$_from = 1;
				if ($_to - $_from < $_pagenum) {
					$_to = $_pagenum;
				}
			} elseif ($_to > $page_count) {
				$_from = $page_count - $_pagenum + 1;
				$_to = $page_count;
			}
		}
		$url_format = $url . $param_url . 'page=';
		$pager['page_first'] = ($page - $_offset > 1 && $_pagenum < $page_count) ? $url_format . 1 : '';
		$pager['page_prev'] = ($page > 1) ? $url_format . $page_prev : '';
		$pager['page_next'] = ($page < $page_count) ? $url_format . $page_next : '';
		$pager['page_last'] = ($_to < $page_count) ? $url_format . $page_count : '';
		$pager['page_kbd'] = ($_pagenum < $page_count) ? true : false;
		$pager['page_number'] = array();
		for ($i = $_from; $i <= $_to;  ++$i) {
			$pager['page_number'][$i] = $url_format . $i;
		}
	}
	$pager['search'] = $param;
	
	return $pager;
}
?>