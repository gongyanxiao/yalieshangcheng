<?php

/**
 * ECSHOP 管理中心办事处管理
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: agency.php 17217 2011-01-19 06:29:08Z liubo $
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/* ------------------------------------------------------ */
//-- 办事处列表
/* ------------------------------------------------------ */
if ($_REQUEST['act'] == 'list') {
    /* 检查权限 */
    admin_priv('role_points_man');
    $smarty->assign('ur_here', "我的积分");
    //$smarty->assign('action_link', array('text' => "新增线下订单", 'href' => 'role_offline_order.php?act=add'));
    $smarty->assign('full_page', 1);

    $sql = "SELECT  fxjl_points,`user_money` as cz_points,pay_points,consum_money,give_points,tx_points,history_zsq,zsq,hk_points,frozen_hk_points,yl_points,jljs_points,gz_points,hz_points FROM " . $GLOBALS['ecs']->table("users") . " WHERE user_id='" . $_SESSION["member_uid"] . "'";
    $userInfo = $GLOBALS['db']->getRow($sql);
    $smarty->assign('userinfo', $userInfo);

    $sql = "SELECT user_id FROM " . $GLOBALS['ecs']->table("users") . " WHERE parent_id='" . $_SESSION['member_uid'] . "' and `history_zsq`>0 ";
    $tjUsers = $GLOBALS['db']->getAll($sql);
    $realValidUserCount = 0;
    foreach ($tjUsers as $cuserid) {
        if (!check_user_is_blacklist($real_supplier_id)) {
            $realValidUserCount++;
        }
    }
    $smarty->assign('realValidUserCount', $realValidUserCount);
    assign_query_info();
    $smarty->display('role_points_man.htm');
}  elseif ($_REQUEST['act'] == 'account_log_list') {
    admin_priv('role_points_man');
    $type = $_REQUEST["type"];
    $types = array("user_money","fxjl_points","pay_points", "consum_money", "history_zsq", "zsq", "hk_points", "frozen_hk_points", "yl_points", "jljs_points", "gz_points","hz_points");
    if (!in_array($type, $types) && !empty($type)) {
        echo "参数错误";
    } else {
        $sql = "SELECT fxjl_points,`user_money` as cz_points,pay_points,consum_money,give_points,tx_points,history_zsq,zsq,hk_points,frozen_hk_points,yl_points,jljs_points,gz_points,hz_points FROM " . $GLOBALS['ecs']->table("users") . " WHERE user_id='" . $_SESSION["member_uid"] . "'";
        $userInfo = $GLOBALS['db']->getRow($sql);
        $smarty->assign('user', $userInfo);
        $smarty->assign('type', $type);
        $change_type = "";
        if (isset($_REQUEST['change_type'])) {
            $change_type = $_REQUEST["change_type"];
        }
        $smarty->assign('change_type', $change_type);
        $account_list = get_accountlist($_SESSION["member_uid"], $type, $change_type);
        $smarty->assign('account_list', $account_list['account']);
        $smarty->assign('filter', $account_list['filter']);
        $smarty->assign('record_count', $account_list['record_count']);
        $smarty->assign('page_count', $account_list['page_count']);
        $smarty->assign('full_page', 1);
        assign_query_info();
        $smarty->display('account_list.htm');
    }
} elseif ($_REQUEST['act'] == 'query') {
    admin_priv('role_points_man');
    /* 检查参数 */
    $type = $_REQUEST["type"];
    $types = array("user_money", "pay_points", "consum_money", "history_zsq", "zsq", "hk_points", "frozen_hk_points", "yl_points", "jljs_points", "gz_points","hz_points");
    if (!in_array($type, $types)) {
        echo "参数错误";
    }
    $change_type = "";
    if (isset($_REQUEST['change_type'])) {
        $change_type = $_REQUEST["change_type"];
    }
    $smarty->assign('change_type', $change_type);
    $smarty->assign('type', $type);
    $account_list = get_accountlist($_SESSION["member_uid"], $type, $change_type);
    $smarty->assign('account_list', $account_list['account']);
    $smarty->assign('filter', $account_list['filter']);
    $smarty->assign('record_count', $account_list['record_count']);
    $smarty->assign('page_count', $account_list['page_count']);

    make_json_result($smarty->fetch('account_list.htm'), '', array('filter' => $account_list['filter'], 'page_count' => $account_list['page_count']));
}

/**
 * 取得帐户明细
 * @param   int     $user_id    用户id
 * @param   string  $account_type   帐户类型：空表示所有帐户，user_money表示可用资金，
 *                  frozen_money表示冻结资金，rank_points表示等级积分，pay_points表示消费积分
 * @return  array
 */
function get_accountlist($user_id, $account_type = '', $change_type = "") {
    /* 初始化分页参数 */
    $filter = array(
        'user_id' => $user_id,
        'type' => $account_type,
        'change_type' => $change_type
    );
    $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'id' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
    /* 检查参数 */
    $where = " WHERE user_id = '$user_id' ";
    if (in_array($account_type, array('user_money', 'pay_points', 'consum_money', 'history_zsq', 'zsq', 'hk_points', 'frozen_hk_points', 'yl_points', 'jljs_points', "gz_points","hz_points"))) {
        $where .= " AND $account_type <> 0 ";
    }
    if ($change_type != "") {
        $where .= " AND change_type = '" . $change_type . "' ";
    }

    /* 查询记录总数，计算分页数 */
    $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('account_log') . $where;
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);
    $filter = page_and_size($filter);

    /* 查询记录 */
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('account_log') . $where .
            " ORDER BY log_id DESC";
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);

    $arr = array();
    while ($row = $GLOBALS['db']->fetchRow($res)) {
        $row['change_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['change_time']);
        $arr[] = $row;
    }
    return array('account' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

?>