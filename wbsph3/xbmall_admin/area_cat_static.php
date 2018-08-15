<?php

/**
 * UEUCS_364642382
 * $Id: area.php 17063 2010-03-25 06:35:46Z liuhui $
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
$exc = new exchange($ecs->table("area"), $db, 'area_id', 'area_name');

/* act操作项的初始化 */
if (empty($_REQUEST['act'])) {
    $_REQUEST['act'] = 'list';
} else {
    $_REQUEST['act'] = trim($_REQUEST['act']);
}

/* ------------------------------------------------------ */
//-- 商品分类列表
/* ------------------------------------------------------ */
if ($_REQUEST['act'] == 'list') {
    $region_id = empty($_REQUEST['pid']) ? 0 : intval($_REQUEST['pid']);
    $smarty->assign('parent_id', $region_id);

    /* 获取分类列表 */
    $cat_list = area_cat_list(0, $region_id, false);
    /* 模板赋值 */
    $smarty->assign('ur_here', '运营中心列表');
    //$smarty->assign('action_link', array('href' => 'area_cat.php?act=add', 'text' => '添加运营中心'));
    $smarty->assign('full_page', 1);

    $smarty->assign('cat_info', $cat_list);

    /* 列表页面 */
    assign_query_info();
    $smarty->display('area_cat_list.htm');
}

/* ------------------------------------------------------ */
//-- 排序、分页、查询
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'query') {
    $cat_list = area_cat_list(0, 0, false);
    $smarty->assign('cat_info', $cat_list);

    make_json_result($smarty->fetch('area_cat_list.htm'));
}

function get_area_static() {
     $result = get_filter();
    if ($result === false)
    {
        /* 过滤列表 */
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['keywords'] = json_str_iconv($filter['keywords']);
        }

        $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'add_time' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
        $filter['start_date'] = empty($_REQUEST['start_date']) ? '' : local_strtotime($_REQUEST['start_date']);
        $filter['end_date'] = empty($_REQUEST['end_date']) ? '' : (local_strtotime($_REQUEST['end_date']) + 86400);

        $where = " WHERE 1 ";
        /*　时间过滤　*/
        if (!empty($filter['start_date']) && !empty($filter['end_date']))
        {
            $where .= "AND paid_time >= " . $filter['start_date']. " AND paid_time < '" . $filter['end_date'] . "'";
        }

        // 代码修改   By  www.68ecshop.com Start
//        $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('user_account'). " AS ua, ".
//                   $GLOBALS['ecs']->table('users') . " AS u " . $where;
        $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('user_account'). " AS ua LEFT JOIN ".
            $GLOBALS['ecs']->table('users') . " AS u ON ua.user_id = u.user_id " . $where;
        // 代码修改   By  www.68ecshop.com End
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);

        /* 查询数据 */
        $sql  = 'SELECT ua.*, u.user_name,u.real_name,u.bank,u.bank_kh,u.bank_num,u.pay_points,u.user_money FROM ' .
            $GLOBALS['ecs']->table('user_account'). ' AS ua LEFT JOIN ' .
            $GLOBALS['ecs']->table('users'). ' AS u ON ua.user_id = u.user_id'.
            $where . "ORDER by " . $filter['sort_by'] . " " .$filter['sort_order']. " LIMIT ".$filter['start'].", ".$filter['page_size'];

        $filter['keywords'] = stripslashes($filter['keywords']);
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }

    $list = $GLOBALS['db']->getAll($sql);
    foreach ($list AS $key => $value)
    {
        $list[$key]['surplus_amount']       = price_format(abs($value['amount']), false);
        $list[$key]['add_date']             = local_date($GLOBALS['_CFG']['time_format'], $value['add_time']);
        $list[$key]['process_type_name']    = $GLOBALS['_LANG']['surplus_type_' . $value['process_type']];
     }
    $arr = array('list' => $list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

?>