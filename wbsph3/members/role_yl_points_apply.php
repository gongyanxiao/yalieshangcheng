<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
if ($_REQUEST['act'] == "add") {
    admin_priv('role_yl_points_apply');
    $smarty->assign('full_page', 1);

    $sql = "SELECT `pension_name`,`money`,`id` FROM " . $GLOBALS['ecs']->table('pension_infos') . " where `pension_status`=1";
    $pension_infos = $GLOBALS['db']->getAll($sql);
    $smarty->assign("pension_infos", $pension_infos);
    $sql = "SELECT real_name,mobile_phone,card FROM " . $GLOBALS['ecs']->table("users") . " WHERE user_id='" . $_SESSION['member_uid'] . "'";
    $userinfos = $GLOBALS['db']->getRow($sql);
    $smarty->assign("userinfos", $userinfos);
    if (isset($_SESSION['yl_apply_time'])) {
        unset($_SESSION['yl_apply_time']);
    }
    $_SESSION['yl_apply_time'] = gmtime();
    $smarty->assign("postToken", md5($_SESSION['user_id'] . $_SESSION['yl_apply_time'] . AUTH_KEY));
    $smarty->display('role_yl_points_apply.htm');
} elseif ($_REQUEST['act'] == "list") {
    $smarty->assign('full_page', 1);
    assign_query_info();
    $smarty->assign('ur_here', "养老金申请记录");
    $smarty->assign('action_link', array('text' => "养老金申请", 'href' => 'role_yl_points_apply.php?act=add'));
    $account_list = get_yl_apply_list($_SESSION["member_uid"]);
    $smarty->assign('account_list', $account_list['account']);
    $smarty->assign('filter', $account_list['filter']);
    $smarty->assign('record_count', $account_list['record_count']);
    $smarty->assign('page_count', $account_list['page_count']);
    $smarty->display('get_yl_apply_list.htm');
} elseif ($_REQUEST['act'] == "query") {
    assign_query_info();
    $account_list = get_yl_apply_list($_SESSION["member_uid"]);
    $smarty->assign('account_list', $account_list['account']);
    $smarty->assign('filter', $account_list['filter']);
    $smarty->assign('record_count', $account_list['record_count']);
    $smarty->assign('page_count', $account_list['page_count']);
    make_json_result($smarty->fetch('get_yl_apply_list.htm'), '', array('filter' => $account_list['filter'], 'page_count' => $account_list['page_count']));
} elseif ($_REQUEST['act'] == "act_account") {
    if ($_POST) {
        //20170315防止数据重复提交添加token验证
        if (!isset($_POST['postToken']) || empty($_POST['postToken']) || !isset($_SESSION['yl_apply_time']) || empty($_SESSION['yl_apply_time'])) {
            sys_msg("提交异常，重复提交");
            exit;
        } else {
            $postToken = $_POST['postToken'];
            if ($postToken != md5($_SESSION['user_id'] . $_SESSION['yl_apply_time'] . AUTH_KEY)) {
                sys_msg("异常提交，请返回后刷新页面重试");
                exit;
            }
            //20170315防止数据重复提交添加token验证开始
//            if (isset($_SESSION['offline_detail_time'])) {
            unset($_SESSION['yl_apply_time']);
//            }
            //20170315防止数据重复提交添加token验证结束
        }
        if (isset($_POST['yl_id']) && !empty($_POST['yl_id'])) {
            $yl_id = trim($_POST['yl_id']);
            $sql = "SELECT money FROM " . $GLOBALS['ecs']->table("pension_infos") . " WHERE `pension_status`=1 AND `id`='" . $yl_id . "'";
            $money = $GLOBALS['db']->getOne($sql);
            if (empty($money)) {
                sys_msg("请选择购买的养老金");
            }
            $sql = "SELECT yl_points FROM " . $GLOBALS['ecs']->table("users") . " WHERE `user_id`='" . $_SESSION['member_uid'] . "'";
            $yl_points = $GLOBALS['db']->getOne($sql);
            if ($money * 1 > $yl_points * 1) {
                sys_msg("您当前的养老积分不足，无法购买当前养老金");
            }
        } else {
            sys_msg("请选择购买的养老金");
        }
        if (isset($_POST['real_name']) && !empty($_POST['real_name'])) {
            $real_name = trim($_POST['real_name']);
        } else {
            sys_msg("请输入真实姓名");
        }
        if (isset($_POST['idcard']) && !empty($_POST['idcard'])) {
            $idcard = trim($_POST['idcard']);
        } else {
            sys_msg("请输入身份证号");
        }
        if (isset($_POST['mobile_phone']) && !empty($_POST['mobile_phone'])) {
            $mobile_phone = trim($_POST['mobile_phone']);
        } else {
            sys_msg("请输入手机号");
        }
        $GLOBALS['db']->startTrans();
        try {
            $data = array(
                "user_id" => $_SESSION['member_uid'],
                "yl_id" => $yl_id,
                "money" => $money,
                "real_name" => $real_name,
                "add_time" => gmtime(),
                "idcard" => $idcard,
                "phone" => $mobile_phone
            );
            $res = $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table("pension_order"), $data);
            if ($res) {
                $res = log_account_change($_SESSION['member_uid'], 0, 0, 0, 0, "购买养老金，扣除养老积分:" . $money, 7, 0, 0, 0, 0, 0, 0, 0, 0, 0, $money * (-1), 0, 0);
            }
            if ($res) {
                $GLOBALS['db']->commitTrans();
                $links = array(
                    array('href' => 'role_yl_points_apply.php?act=list', 'text' => "申请列表")
                );
                sys_msg("养老金申请成功，请耐心等待审核", 0, $links);
            } else {
                $GLOBALS['db']->rollbackTrans();
                sys_msg("养老金申请失败");
            }
        } catch (Exception $ex) {
            sys_msg("养老金申请失败");
        }
    }
}

function get_yl_apply_list($user_id) {
    $filter = array(
        'user_id' => $user_id,
    );
    $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'add_time' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

    $filter['start_time'] = empty($_REQUEST['start_time']) ? '' : (strpos($_REQUEST['start_time'], '-') > 0 ? local_strtotime($_REQUEST['start_time']) : $_REQUEST['start_time']);
    $filter['end_time'] = empty($_REQUEST['end_time']) ? '' : (strpos($_REQUEST['end_time'], '-') > 0 ? local_strtotime($_REQUEST['end_time']) : $_REQUEST['end_time']);

    $where = " WHERE s.user_id = '$user_id' ";

    if ($filter['start_time']) {
        $where .= " AND s.add_time >= '$filter[start_time]'";
    }
    if ($filter['end_time']) {
        $where .= " AND s.add_time <= '$filter[end_time]'";
    }
    /* 查询记录总数，计算分页数 */
    $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('pension_order') . " as s " . $where;
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);
    $filter = page_and_size($filter);

    $sql = "SELECT s.id,s.real_name,s.idcard,s.phone,s.add_time,s.`status`,a.`pension_name`,s.`money` FROM" . $GLOBALS['ecs']->table('pension_order') . " as s LEFT JOIN " . $GLOBALS['ecs']->table("pension_infos") . " as a  on s.yl_id=a.id " . $where . "  ORDER BY $filter[sort_by] $filter[sort_order]";
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);
    $arr = array();
    while ($rows = $GLOBALS['db']->fetchRow($res)) {
        $rt['real_name'] = $rows['real_name']; // preg_replace($pattern, $replacement, $rt['mobile_phone']);
        $rt['idcard'] = $rows['idcard'];
        $rt['id'] = $rows['id'];
        $rt['mobile_phone'] = $rows['phone'];
        $rt['money'] = $rows['money'];
        $rt['pension_name'] = $rows['pension_name'];
        $rt['add_time'] = local_date("Y-m-d G:i:s", $rows['add_time']);
        $rt['status'] = $rows['status']; //mb_substr($rt['supplier_name'], 0, 1, 'utf-8') . '***' . mb_substr($rt['supplier_name'], -1, 1, 'utf-8');
        $arr[] = $rt;
    }
    return array('account' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}
