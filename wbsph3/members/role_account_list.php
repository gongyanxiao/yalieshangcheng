<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
if ($_REQUEST['act'] == 'list') {
    admin_priv('role_points_man');
    $account_list = get_user_list($_SESSION["member_uid"]);
    $smarty->assign('order_list', $account_list['account']);
    $smarty->assign('filter', $account_list['filter']);
    $smarty->assign('record_count', $account_list['record_count']);
    $smarty->assign('page_count', $account_list['page_count']);
    $smarty->assign('full_page', 1);
    assign_query_info();
    $smarty->display('role_reward_list.htm');
} elseif ($_REQUEST['act'] == 'query') {
    admin_priv('role_points_man');
    $account_list = get_user_list($_SESSION["member_uid"]);
    $smarty->assign('order_list', $account_list['account']);
    $smarty->assign('filter', $account_list['filter']);
    $smarty->assign('record_count', $account_list['record_count']);
    $smarty->assign('page_count', $account_list['page_count']);

    make_json_result($smarty->fetch('role_reward_list.htm'), '', array('filter' => $account_list['filter'], 'page_count' => $account_list['page_count']));
}

/**
 * 取得帐户明细
 * @param   int     $user_id    用户id
 * @param   string  $account_type   帐户类型：空表示所有帐户，user_money表示可用资金，
 *                  frozen_money表示冻结资金，rank_points表示等级积分，pay_points表示消费积分
 * @return  array
 */
function get_user_list($user_id) {
    /* 初始化分页参数 */
    $filter = array(
        'user_id' => $user_id,
    );
    $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'user_id' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
    /* 检查参数 */
    $where = " WHERE parent_id = '$user_id' and `history_zsq`>0 ";

    /* 查询记录总数，计算分页数 */
    $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('users') . $where;
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);
    $filter = page_and_size($filter);

    /* 查询记录 */
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('users') . $where . " ORDER BY $filter[sort_by] $filter[sort_order]";
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);

    $arr = array();
    while ($row = $GLOBALS['db']->fetchRow($res)) {
        $row['reg_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['reg_time']);
        if (!check_user_is_blacklist($user_id)) {
            $row['status'] = "正常";
        } else {
            $row['status'] = "异常";
        }
        $arr[] = $row;
    }
    return array('account' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}
