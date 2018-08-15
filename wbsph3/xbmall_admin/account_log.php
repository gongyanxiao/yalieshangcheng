<?php

/**
 * ECSHOP 管理中心帐户变动记录
 * ============================================================================
 * 版权所有 2005-2011 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: account_log.php 17217 2011-01-19 06:29:08Z liubo $
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/lib_order.php');

/* ------------------------------------------------------ */
//-- 办事处列表
/* ------------------------------------------------------ */
if ($_REQUEST['act'] == 'list') {
    /* 检查参数 */
    $user_id = empty($_REQUEST['user_id']) ? 0 : intval($_REQUEST['user_id']);
    if ($user_id <= 0) {
        sys_msg('invalid param');
    }
    $user = user_info($user_id);
    if (empty($user)) {
        sys_msg($_LANG['user_not_exist']);
    }
    $smarty->assign('user', $user);

    if (empty($_REQUEST['account_type']) || !in_array($_REQUEST['account_type'], array('user_money', 'frozen_money', 'rank_points', 'pay_points', 'consum_money', 'give_points', 'tx_points', 'history_zsq', 'zsq', 'yl_points', 'hk_points', 'frozen_hk_points', 'jljs_points', 'day_points', 'hz_points', 'djtfh_points', 'gz_points'))) {
        $account_type = '';
    } else {
        $account_type = $_REQUEST['account_type'];
    }
    $smarty->assign('account_type', $account_type);

    $change_type = "";
    if (isset($_REQUEST['change_type'])) {
        $change_type = $_REQUEST["change_type"];
    }
    $smarty->assign('change_type', $change_type);
    $smarty->assign('ur_here', $_LANG['account_list']);
    $smarty->assign('action_link', array('text' => $_LANG['add_account'], 'href' => 'account_log.php?act=add&user_id=' . $user_id));
    $smarty->assign('full_page', 1);

    $account_list = get_accountlist($user_id, $account_type, $change_type);
    $smarty->assign('account_list', $account_list['account']);
    $smarty->assign('filter', $account_list['filter']);
    $smarty->assign('record_count', $account_list['record_count']);
    $smarty->assign('page_count', $account_list['page_count']);

    assign_query_info();
    $smarty->display('account_list.htm');
}

/* ------------------------------------------------------ */
//-- 排序、分页、查询
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'query') {
    /* 检查参数 */
    $user_id = empty($_REQUEST['user_id']) ? 0 : intval($_REQUEST['user_id']);
    if ($user_id <= 0) {
        sys_msg('invalid param');
    }
    $user = user_info($user_id);
    if (empty($user)) {
        sys_msg($_LANG['user_not_exist']);
    }
    $smarty->assign('user', $user);

    if (empty($_REQUEST['account_type']) || !in_array($_REQUEST['account_type'], array('user_money', 'frozen_money', 'rank_points', 'pay_points', 'consum_money', 'give_points', 'tx_points', 'history_zsq', 'zsq', 'yl_points', 'hk_points', 'frozen_hk_points', 'jljs_points', 'day_points', 'hz_points', 'djtfh_points', 'gz_points'))) {
        $account_type = '';
    } else {
        $account_type = $_REQUEST['account_type'];
    }
    $smarty->assign('account_type', $account_type);

    $account_list = get_accountlist($user_id, $account_type);
    $smarty->assign('account_list', $account_list['account']);
    $smarty->assign('filter', $account_list['filter']);
    $smarty->assign('record_count', $account_list['record_count']);
    $smarty->assign('page_count', $account_list['page_count']);

    make_json_result($smarty->fetch('account_list.htm'), '', array('filter' => $account_list['filter'], 'page_count' => $account_list['page_count']));
}

/* ------------------------------------------------------ */
//-- 调节帐户
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'add') {
    /* 检查权限 */
    admin_priv('account_manage');

    /* 检查参数 */
    $user_id = empty($_REQUEST['user_id']) ? 0 : intval($_REQUEST['user_id']);
    if ($user_id <= 0) {
        sys_msg('invalid param');
    }
    $user = user_info($user_id);
    if (empty($user)) {
        sys_msg($_LANG['user_not_exist']);
    }
    $smarty->assign('user', $user);

    /* 显示模板 */
    $smarty->assign('ur_here', $_LANG['add_account']);
    $smarty->assign('action_link', array('href' => 'account_log.php?act=list&user_id=' . $user_id, 'text' => $_LANG['account_list']));

    //20170315防止数据重复提交添加token验证开始
    if (isset($_SESSION['account_log_time'])) {
        unset($_SESSION['account_log_time']);
    }
    $_SESSION['account_log_time'] = gmtime();
    $smarty->assign("postToken", md5($_SESSION['user_id'] . $_SESSION['account_log_time'] . AUTH_KEY));
    //20170315防止数据重复提交添加token验证结束

    assign_query_info();

    $smarty->display('account_info.htm');
}

/* ------------------------------------------------------ */
//-- 提交添加、编辑办事处
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'insert' || $_REQUEST['act'] == 'update') {
    /* 检查权限 */
    admin_priv('account_manage');

    //20170315防止数据重复提交添加token验证
    if (!isset($_POST['postToken']) || empty($_POST['postToken']) || !isset($_SESSION['account_log_time']) || empty($_SESSION['account_log_time'])) {
        sys_msg("提交异常，重复提交");
        exit;
    } else {
        $postToken = $_POST['postToken'];
        if ($postToken != md5($_SESSION['user_id'] . $_SESSION['account_log_time'] . AUTH_KEY)) {
            sys_msg("异常提交，请返回后刷新页面重试");
            exit;
        }
        //20170315防止数据重复提交添加token验证开始
//            if (isset($_SESSION['offline_detail_time'])) {
        unset($_SESSION['account_log_time']);
//            }
        //20170315防止数据重复提交添加token验证结束
    }
    //20170315防止数据重复提交添加token验证结束

    /* 检查参数 */
    $user_id = empty($_REQUEST['user_id']) ? 0 : intval($_REQUEST['user_id']);
    if ($user_id <= 0) {
        sys_msg('invalid param');
    }
    $user = user_info($user_id);
    if (empty($user)) {
        sys_msg($_LANG['user_not_exist']);
    }

    /* 提交值 */
    $change_desc = sub_str($_POST['change_desc'], 255, false);
    $user_money = floatval($_POST['add_sub_user_money']) * abs(floatval($_POST['user_money']));
    $frozen_money = floatval($_POST['add_sub_frozen_money']) * abs(floatval($_POST['frozen_money']));
    $rank_points = floatval($_POST['add_sub_rank_points']) * abs(floatval($_POST['rank_points']));
    $pay_points = floatval($_POST['add_sub_pay_points']) * abs(floatval($_POST['pay_points']));

    $consum_money = floatval($_POST['add_consum_money']) * abs(floatval($_POST['consum_money']));
    $give_points = floatval($_POST['add_give_points']) * abs(floatval($_POST['give_points']));
    $tx_points = floatval($_POST['add_tx_points']) * abs(floatval($_POST['tx_points']));
    $history_zsq = floatval($_POST['add_history_zsq']) * abs(floatval($_POST['history_zsq']));
    $zsq = floatval($_POST['add_zsq']) * abs(floatval($_POST['zsq']));
    $frozen_points = floatval($_POST['add_frozen_points']) * abs(floatval($_POST['frozen_points']));

    $yl_points = floatval($_POST['add_yl_points']) * abs(floatval($_POST['yl_points']));
    $hk_points = floatval($_POST['add_hk_points']) * abs(floatval($_POST['hk_points']));
    $frozen_hk_points = floatval($_POST['add_frozen_hk_points']) * abs(floatval($_POST['frozen_hk_points']));
    $jljs_points = floatval($_POST['add_jljs_points']) * abs(floatval($_POST['jljs_points']));

    $hz_points = floatval($_POST['add_hz_points']) * abs(floatval($_POST['hz_points']));

    $day_points = floatval($_POST['add_day_points']) * abs(floatval($_POST['day_points']));

    $djtfh_points = floatval($_POST['add_djtfh_points']) * abs(floatval($_POST['djtfh_points']));
    $gz_points = floatval($_POST['add_gz_points']) * abs(floatval($_POST['gz_points']));
    $fxjl_points = floatval($_POST['add_fxjl_points']) * abs(floatval($_POST['fxjl_points']));
    
    $benJiFenHongQuan = updateBenJiFenHongQuan($user_id);
    
    
    
    if ($benJiFenHongQuan==0 && $fxjl_points==0 && $djtfh_points == 0 && $gz_points == 0 && $day_points == 0 && $hz_points == 0 && $yl_points == 0 && $hk_points == 0 && $frozen_hk_points == 0 && $jljs_points == 0 && $user_money == 0 && $frozen_money == 0 && $rank_points == 0 && $pay_points == 0 && $consum_money == 0 && $give_points == 0 && $tx_points == 0 && $history_zsq == 0 && $zsq == 0 && $frozen_points == 0) {
        sys_msg($_LANG['no_account_change']);
    }

    /* 保存 */
    //$user_id, $user_money = 0, $frozen_money = 0, $rank_points = 0, $pay_points = 0, $change_desc = '', $change_type = ACT_OTHER, $consum_money = 0, $give_points = 0, $tx_points = 0, $history_zsq = 0, $zsq = 0, $frozen_points = 0, $day_points = 0, $hk_points = 0, $frozen_hk_points = 0, $yl_points = 0, $jljs_points = 0, $hz_points = 0
    log_account_change($user_id, $user_money, $frozen_money, $rank_points, $pay_points, $change_desc, ACT_ADJUSTING, $consum_money, $give_points, $tx_points, $history_zsq, $zsq, $frozen_points, $day_points, $hk_points, $frozen_hk_points, $yl_points, $jljs_points, $hz_points, $djtfh_points, $gz_points,$fxjl_points);
    //是否开启余额变动给客户发短信-管理员调节
    if ($_CFG['sms_user_money_change'] == 1) {
//		if(abs($user_money) > 0)
//		{
//			if($_POST['add_sub_user_money'] > 0)
//			{
//				$user_money = '+'.$user_money;
//			}
//			else
//			{
//				$user_money = '-'.$user_money;
//			}
//			$sql = "SELECT user_money,mobile_phone FROM " . $GLOBALS['ecs']->table('users') . " WHERE user_id = '$user_id'";
//			$users = $GLOBALS['db']->getRow($sql);
//			$content = sprintf($_CFG['sms_admin_operation_tpl'],date("Y-m-d H:i:s",gmtime()),$user_money,$users['user_money'],$_CFG['sms_sign']);
//			if($users['mobile_phone'])
//			{
//				include_once('../send.php');
//				sendSMS($users['mobile_phone'],$content);
//			}
//		}
    }

    /* 提示信息 */
    $links = array(
        array('href' => 'account_log.php?act=list&user_id=' . $user_id, 'text' => $_LANG['account_list'])
    );
    sys_msg($_LANG['log_account_change_ok'], 0, $links);
}


/**
 * 修改本金分红权
 * @param unknown $user_id
 * @return unknown
 */
function  updateBenJiFenHongQuan($user_id){
	
	$ben_jin_rate= floatval($_POST['add_ben_jin_rate']) * abs(floatval($_POST['ben_jin_rate']));
	$shou_yi_rate= floatval($_POST['add_shou_yi_rate']) * abs(floatval($_POST['shou_yi_rate']));
	$sql = "UPDATE " . $GLOBALS['ecs']->table('users') .
	" SET  ben_jin_rate = ben_jin_rate + ('$ben_jin_rate')," .
	" shou_yi_rate = shou_yi_rate + ('$shou_yi_rate')" .
	" WHERE user_id = '$user_id' LIMIT 1";
	return $GLOBALS['db']->query($sql);
}
/**
 * 取得帐户明细
 * @param   int     $user_id    用户id
 * @param   string  $account_type   帐户类型：空表示所有帐户，user_money表示可用资金，
 *                  frozen_money表示冻结资金，rank_points表示等级积分，pay_points表示消费积分
 * @return  array
 */
function get_accountlist($user_id, $account_type = '', $change_type = "") {
    /* 检查参数 */
    $where = " WHERE user_id = '$user_id' ";
    if (in_array($account_type, array('user_money', 'frozen_money', 'rank_points', 'pay_points', 'consum_money', 'give_points', 'tx_points', 'history_zsq', 'zsq', 'yl_points', 'hk_points', 'frozen_hk_points', 'jljs_points', 'day_points', 'hz_points','djtfh_points','gz_points'))) {
        $where .= " AND $account_type <> 0 ";
    }

    if ($change_type != "") {
        $where .= " AND change_type = '" . $change_type . "' ";
    }
    /* 初始化分页参数 */
    $filter = array(
        'user_id' => $user_id,
        'account_type' => $account_type
    );

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