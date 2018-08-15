<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
if ($_REQUEST['act'] == "add") {
    admin_priv('3_role_points_gz_ex');
    $smarty->assign('full_page', 1);
    $sql = "select `real_name`,`card`,`bank`,`bank_kh`,`bank_num` from" . $GLOBALS['ecs']->table("users") . " where `user_id`='" . $_SESSION["member_uid"] . "'";
    $profileRow = $db->getRow($sql);
    if (empty($profileRow["real_name"]) || empty($profileRow["card"]) || empty($profileRow["bank"]) || empty($profileRow['bank_kh']) || empty($profileRow['bank_num'])) {
        $links = array(
            array('href' => 'role_points_gz_ex.php?act=info', 'text' => "个人信息")
        );
        sys_msg("请将个人信息补充完整，才能继续进行基数奖励兑换操作", 0, $links);
    } else {
//         $sql = "SELECT add_time FROM " . $GLOBALS['ecs']->table("user_account") . " where user_id='" . $_SESSION["member_uid"] . "' and process_type=1 and type=2 order by add_time DESC limit 1 ";
//         $add_time = $GLOBALS['db']->getOne($sql);
//         $time = gmtime();
//         if (!empty($add_time) && ($time < $add_time + 86400 * 1)) {
//             sys_msg("请于上次提现后1日后再次提现");
//         }
        $weekDay = date("w");
        if($weekDay!=4 ){
        	sys_msg("提现日期为周四");
        }
        
        $hoursNow =  date("H")+8;
        if($hoursNow<7|| $hoursNow>16){
        	sys_msg("提现时间为早8点到下午5点之间");
        }
        
        
        assign_query_info();
        $smarty->display('role_points_gz_ex.htm');
    }
} elseif ($_REQUEST['act'] == "info") {
    admin_priv('3_role_points_gz_ex');
    $smarty->assign('full_page', 1);
    $sql = "select `real_name`,`card`,`bank`,`bank_kh`,`bank_num`,`face_card`,`back_card` from" . $GLOBALS['ecs']->table("users") . " where `user_id`='" . $_SESSION["member_uid"] . "'";
    $profileRow = $db->getRow($sql);
    $smarty->assign('action', 1);
    $smarty->assign('profile', $profileRow);
    assign_query_info();
    $smarty->display('role_user_info.htm');
} elseif ($_REQUEST['act'] == "act_identity") {
    admin_priv('3_role_points_gz_ex');
    include_once (ROOT_PATH . '/includes/cls_image.php');
    $image = new cls_image($_CFG['bgcolor']);
    $real_name = $_POST['real_name'];
    $card = $_POST['card'];
    $bank = $_POST['bank'];
    $bank_kh = $_POST['bank_kh'];
    $bank_num = $_POST['bank_num'];
   /*  if (isset($_FILES['face_card']) && $_FILES['face_card']['tmp_name'] != '') {
        if ($_FILES['face_card']['width'] > 800) {
            sys_msg('图片宽度不能超过800像素！');
        }
        if ($_FILES['face_card']['height'] > 800) {
            sys_msg('图片高度不能超过800像素！');
        }
        $face_card = $image->upload_image($_FILES['face_card']);
        if ($face_card === false) {
            sys_msg($image->error_msg());
        }
    }
    if (isset($_FILES['back_card']) && $_FILES['back_card']['tmp_name'] != '') {
        if ($_FILES['back_card']['width'] > 800) {
            sys_msg('图片宽度不能超过800像素！');
        }
        if ($_FILES['back_card']['height'] > 800) {
            sys_msg('图片高度不能超过800像素！');
        }
        $back_card = $image->upload_image($_FILES['back_card']);
        if ($back_card === false) {
            sys_msg($image->error_msg());
        }
    } */

    $sql = 'update ' . $GLOBALS['ecs']->table('users') . " set bank='$bank',bank_kh='$bank_kh',bank_num='$bank_num' ";
   /*  if ($face_card != '') {
        $sql .= " ,face_card = '$face_card'";
    }
    if ($back_card != '') {
        $sql .= " ,back_card = '$back_card'";
    } */
    $sql .= " where user_id = '" . $_SESSION['member_uid'] . "'";
    $num = $GLOBALS['db']->query($sql);
    if ($num > 0) {
        $links = array(
            array('href' => 'role_points_gz_ex.php?act=list', 'text' => "基数奖励兑换")
        );
        sys_msg('个人信息提交成功！', 0, $links);
    } else {
        sys_msg('个人信息提交失败！');
    }
} elseif ($_REQUEST['act'] == "list") {
    $smarty->assign('full_page', 1);
    assign_query_info();
    $smarty->assign('ur_here', "基数奖励兑换记录");
    $smarty->assign('action_link', array('text' => "基数奖励兑换", 'href' => 'role_points_gz_ex.php?act=add'));
    $account_list = get_exchange_account_log($_SESSION["member_uid"]);
    $smarty->assign('account_list', $account_list['account']);
    $smarty->assign('filter', $account_list['filter']);
    $smarty->assign('record_count', $account_list['record_count']);
    $smarty->assign('page_count', $account_list['page_count']);
    $smarty->display('role_points_gz_exs.htm');
} elseif ($_REQUEST['act'] == "query") {
    assign_query_info();
    $account_list = get_exchange_account_log($_SESSION["member_uid"]);
    $smarty->assign('account_list', $account_list['account']);
    $smarty->assign('filter', $account_list['filter']);
    $smarty->assign('record_count', $account_list['record_count']);
    $smarty->assign('page_count', $account_list['page_count']);
    make_json_result($smarty->fetch('role_points_gz_exs.htm'), '', array('filter' => $account_list['filter'], 'page_count' => $account_list['page_count']));
} elseif ($_REQUEST['act'] == "act_account") {
    include_once (ROOT_PATH . 'includes/lib_clips.php');
    include_once (ROOT_PATH . 'includes/lib_order.php');
    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
    if ($amount <= 0) {
        sys_msg("基数奖励兑换积分必须大于零");
    }
    /* 变量初始化 */
    $surplus = array(
        'user_id' => $_SESSION["member_uid"],
    		'rec_id' => !empty($_POST['rec_id']) ? intval($_POST['rec_id']) : 0,
    		'process_type' => isset($_POST['surplus_type']) ? intval($_POST['surplus_type']) : 0,
    		'payment_id' => isset($_POST['payment_id']) ? intval($_POST['payment_id']) : 0,
    		'user_note' => isset($_POST['user_note']) ? trim($_POST['user_note']) : '', 
    		'amount' => $amount ,
    		'fee' => $amount*12/100 ,
    		'real_amount' => $amount*88/100
    );
    /* 判断是否有足够的余额的进行退款的操作 */
    $userDefault = get_user_default($_SESSION["member_uid"]);
    $sur_amount = $userDefault["gz_points"] * 1;

   
    
    $weekDay = date("w");
    if(!( $weekDay==4)){
    	sys_msg("提现日期为周四");
    }
    
    $hoursNow =  date("H")+8;
    if($hoursNow<7|| $hoursNow>16){
    	sys_msg("提现时间为早8点到下午5点之间");
    }
    
    
//     $sql = "SELECT add_time FROM " . $GLOBALS['ecs']->table("user_account") . " where user_id='" . $_SESSION["member_uid"] . "' and process_type=1 order by add_time DESC limit 1 ";
//     $add_time = $GLOBALS['db']->getOne($sql);
//     $time = gmtime();
//     if (!empty($add_time) && ($time < $add_time + 86400 * 1)) {
//         sys_msg("请于上次提现后1日后再次提现");
//     }
    
    $min = 100;
    if (isset($GLOBALS['_CFG']['tx_set_je']) && !empty($GLOBALS['_CFG']['tx_set_je'])) {
        $min = $GLOBALS['_CFG']['tx_set_je'] * 1;
        if ($tx_set_je < 100) {
            $tx_set_je = 100;
        }
    }
    if ($sur_amount < $min) {
        $content = "您现有的基数奖励不足" . $min . "，此操作将不可进行！";
        sys_msg($content);
    }
//     $test = $amount / $min;
//     if (ceil($test) !== $test) {
//         $content = "您的提现必须是" . $min . "的整数倍，此操作将不可进行！";
//         sys_msg($content);
//     }

    if ($amount > $sur_amount) {
        $content = "您要申请提现的积分超过了您现有的基数奖励，此操作将不可进行！";
        sys_msg($content);
    }
    // 插入会员账目明细
    $amount = '-' . $amount;
    $surplus['payment'] = '';
    $GLOBALS['db']->startTrans();
    $surplus['rec_id'] = insert_user_account($surplus, $amount, 1);

    /* 如果成功提交 */
    if ($surplus['rec_id'] > 0) {
        //$user_id, $user_money = 0, $frozen_money = 0, $rank_points = 0, $pay_points = 0, $change_desc = '', $change_type = ACT_OTHER, 
        //$consum_money = 0, $give_points = 0, $tx_points = 0, $history_zsq = 0, $zsq = 0, $frozen_points = 0, $day_points = 0, 
        //$hk_points = 0, $frozen_hk_points = 0, $yl_points = 0, $jljs_points = 0, $hz_points = 0, $djtfh_points = 0, $gz_points = 0
    	$res1 = log_account_change($_SESSION["member_uid"], 0, 0, 0, 0, "基数奖励兑换操作", ACT_DRAWING_GZ, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, abs($amount) * (-1));
        if ($res1) {
            $content = "您的基数奖励兑换申请已经提交，请等待审核。";
            $GLOBALS['db']->commitTrans();
            $links = array(
                array('href' => 'role_points_gz_ex.php?act=list', 'text' => "基数奖励兑换")
            );
            sys_msg($content, 0, $links);
        } else {
            $GLOBALS['db']->rollbackTrans();
            $content = "基数奖励兑换申请失败";
            show_message($content);
        }
    } else {
        $GLOBALS['db']->rollbackTrans();
        $content = "基数奖励兑换申请失败";
        show_message($content);
    }
} elseif ($_REQUEST['act'] == "cancel") {
    // 变量初始化
    $surplus_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    include_once (ROOT_PATH . 'includes/lib_clips.php');
    include_once (ROOT_PATH . 'includes/lib_order.php');
    if ($surplus_id == 0) {
        ecs_header("Location: role_points_gz_ex.php?act=list\n");
        exit();
    }
    $GLOBALS['db']->startTrans();
    $account_order = $GLOBALS['db']->getRow('SELECT *  FROM ' . $GLOBALS['ecs']->table('user_account') .
            " WHERE is_paid = 0 AND id = '$surplus_id' AND user_id = '" . $_SESSION['member_uid'] . "'");
    $result = del_user_account($surplus_id, $_SESSION['member_uid']);
    if ($result) {
        $amount = $account_order['amount'];
        //$user_id, $user_money = 0, $frozen_money = 0, $rank_points = 0, $pay_points = 0, $change_desc = '', $change_type = ACT_OTHER, $consum_money = 0, $give_points = 0, $tx_points = 0, $history_zsq = 0, $zsq = 0, $frozen_points = 0
        if ($account_order['process_type'] * 1 == 1) {
            $ress = log_account_change($_SESSION['member_uid'], 0, 0, 0, 0, "端手动取消基数奖励兑换，基数奖励增加", 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, abs($amount));
        } else {
            $ress = true;
        }
        if ($ress) {
            $GLOBALS['db']->commitTrans();
            $links = array(
                array('href' => 'role_points_gz_ex.php?act=list', 'text' => "基数奖励兑换")
            );
            sys_msg('操作成功！', 0, $links);
        }
    }
    $GLOBALS['db']->rollbackTrans();
}
/*
 * 充值记录 
 */

function get_exchange_account_log($user_id) {
    /* 初始化分页参数 */
    /* 初始化分页参数 */
    $filter = array(
        'user_id' => $user_id,
    );
    $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'add_time' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

    $filter['start_time'] = empty($_REQUEST['start_time']) ? '' : (strpos($_REQUEST['start_time'], '-') > 0 ? local_strtotime($_REQUEST['start_time']) : $_REQUEST['start_time']);
    $filter['end_time'] = empty($_REQUEST['end_time']) ? '' : (strpos($_REQUEST['end_time'], '-') > 0 ? local_strtotime($_REQUEST['end_time']) : $_REQUEST['end_time']);

    $where = " WHERE user_id = '$user_id' AND process_type ='1' and type=1 ";

    if ($filter['start_time']) {
        $where .= " AND add_time >= '$filter[start_time]'";
    }
    if ($filter['end_time']) {
        $where .= " AND add_time <= '$filter[end_time]'";
    }
    /* 查询记录总数，计算分页数 */
    $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('user_account') . $where;
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);
    $filter = page_and_size($filter);

    /* 查询记录 */
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('user_account') . $where .
            " ORDER BY $filter[sort_by] $filter[sort_order]";
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);

    $arr = array();
    while ($rows = $GLOBALS['db']->fetchRow($res)) {
        $rows['add_time'] = local_date("Y-m-d H:i:s", $rows['add_time']);
        $rows['admin_note'] = nl2br(htmlspecialchars($rows['admin_note']));
        $rows['short_admin_note'] = ($rows['admin_note'] > '') ? sub_str($rows['admin_note'], 30) : 'N/A';
        $rows['user_note'] = nl2br(htmlspecialchars($rows['user_note']));
        $rows['short_user_note'] = ($rows['user_note'] > '') ? sub_str($rows['user_note'], 30) : 'N/A';
        $rows['pay_status'] = ($rows['is_paid'] == 0) ? $GLOBALS['_LANG']['un_confirm'] : $GLOBALS['_LANG']['is_confirm'];
        $rows['amount'] = price_format(abs($rows['amount']), false);

        /* 会员的操作类型： 冲值，提现 */
        if ($rows['process_type'] == 0) {
            $rows['type'] = $GLOBALS['_LANG']['surplus_type_0'];
        } else {
            $rows['type'] = $GLOBALS['_LANG']['surplus_type_1'];
        }

        /* 支付方式的ID */
        $sql = 'SELECT pay_id FROM ' . $GLOBALS['ecs']->table('payment') .
                " WHERE pay_name = '$rows[payment]' AND enabled = 1";
        $pid = $GLOBALS['db']->getOne($sql);

        /* 如果是预付款而且还没有付款, 允许付款 */
        if (($rows['is_paid'] == 0) && ($rows['process_type'] == 1)) {
            $rows['handle'] = '<a href="role_points_gz_ex.php?act=cancel&id=' . $rows['id'] . '" onclick="if(confirm(\'确定删除当前基数奖励兑换记录?\')){return true;}else{return false;}">删除</a>';
            $rows['pay_id'] = $pid;
        }
        $arr[] = $rows;
    }
    return array('account' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}
