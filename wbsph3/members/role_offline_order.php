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
    admin_priv('role_offline_order_m');
    $smarty->assign('ur_here', "线下订单列表");
    $smarty->assign('action_link', array('text' => "新增线下订单", 'href' => 'role_offline_order.php?act=add'));
    $smarty->assign('full_page', 1);

    $agency_list = get_offline_order_list();
    $smarty->assign('order_list', $agency_list['agency']);
    $smarty->assign('filter', $agency_list['filter']);
    $smarty->assign('record_count', $agency_list['record_count']);
    $smarty->assign('page_count', $agency_list['page_count']);
    $smarty->assign('sum_order_amt', $agency_list['sum_order_amt']);
    /* 排序标记 */
    $sort_flag = sort_flag($agency_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('offline_order_list.htm');
}

/* ------------------------------------------------------ */
//-- 排序、分页、查询
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'query') {
    /* 检查权限 */
    admin_priv('role_offline_order_m');
    $agency_list = get_offline_order_list();
    $smarty->assign('order_list', $agency_list['agency']);
    $smarty->assign('filter', $agency_list['filter']);
    $smarty->assign('record_count', $agency_list['record_count']);
    $smarty->assign('page_count', $agency_list['page_count']);
    $smarty->assign('sum_order_amt', $agency_list['sum_order_amt']);

    /* 排序标记 */
    $sort_flag = sort_flag($agency_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('offline_order_list.htm'), '', array('filter' => $agency_list['filter'], 'page_count' => $agency_list['page_count']));
}


/* ------------------------------------------------------ */
//-- 添加、编辑办事处
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'add') {
    /* 检查权限 */
    admin_priv('role_offline_order_m');

    /* 是否添加 */
    $is_add = $_REQUEST['act'] == 'add';
    $smarty->assign('form_action', $is_add ? 'insert' : 'update');

    //20170315防止数据重复提交添加token验证开始
    if (isset($_SESSION['offline_order_time'])) {
        unset($_SESSION['offline_order_time']);
    }
    $_SESSION['offline_order_time'] = gmtime();
    $smarty->assign("postToken", md5($_SESSION['user_id'] . $_SESSION['offline_order_time'] . AUTH_KEY));
    //20170315防止数据重复提交添加token验证结束

    $bdFei = $GLOBALS['db']->getOne("select value from " . $GLOBALS['ecs']->table("shop_config") . " where code='xianxiabd_fei'"); //
    if (empty($bdFei)) {
        $bdFei = 0.12;
    }// * $bdFei
    $smarty->assign("bdFei", $bdFei);

    $dayu_xiaofei = $GLOBALS['db']->getOne("select value from " . $GLOBALS['ecs']->table("shop_config") . " where code='dayu_xiaofei' limit 1"); //
    $smarty->assign("dayu_xiaofei", $dayu_xiaofei);


    assign_query_info();
    $smarty->display('offline_order_info.htm');
}

/* ------------------------------------------------------ */
//-- 提交添加、编辑办事处
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'insert') {
    /* 检查权限 */
    admin_priv('role_offline_order_m');

    /* 是否添加 */
    $is_add = $_REQUEST['act'] == 'insert';

    //20170315防止数据重复提交添加token验证
    if (!isset($_POST['postToken']) || empty($_POST['postToken']) || !isset($_SESSION['offline_order_time']) || empty($_SESSION['offline_order_time'])) {
        sys_msg("提交异常，重复提交");
        exit;
    } else {
        $postToken = $_POST['postToken'];
        if ($postToken != md5($_SESSION['user_id'] . $_SESSION['offline_order_time'] . AUTH_KEY)) {
            sys_msg("异常提交，请返回后刷新页面重试");
            exit;
        }
        //20170315防止数据重复提交添加token验证开始
//            if (isset($_SESSION['offline_detail_time'])) {
        //unset($_SESSION['offline_order_time']);
//            }
        //20170315防止数据重复提交添加token验证结束
    }
    //20170315防止数据重复提交添加token验证结束

    if (!isset($_POST["user_id"]) || !isset($_POST["good_name"]) || !isset($_POST["order_amt"])) {
        //show_message("参数不合法", "返回上一页", 'user.php?act=offline_detail', 'info');
        sys_msg("参数不合法");
    }
    /* 20170415验证码验证开始 */
    require_once (ROOT_PATH . 'includes/lib_passport.php');
    $dayu_xiaofei = $GLOBALS['db']->getOne("SELECT value FROM " . $GLOBALS['ecs']->table("shop_config") . " WHERE code='dayu_xiaofei' limit 1");
    $dayu_xiaofei = $dayu_xiaofei * 1;
    if ($dayu_xiaofei * 1 > 0) {
        $result = validate_mobile_code($_POST["user_id"], $_POST["mobile_code"]);

        if ($result == 1) {
            sys_msg("手机号不能为空");
        } else if ($result == 2) {
            sys_msg("手机号格式不能为空");
        } else if ($result == 3) {
            sys_msg("手机验证码不能为空");
        } else if ($result == 4) {
            sys_msg("手机验证码不正确");
        } else if ($result == 5) {
            sys_msg("手机验证码不正确");
        }
    }
    /* 20170415验证码验证结束 */

    $order_amt = $_POST["order_amt"];
    if ($order_amt * 1 > 0) {
        $sql = "select `user_id`,`user_name`,`pay_points` from" . $GLOBALS["ecs"]->table("users") . " where `mobile_phone`='" . $_POST["user_id"] . "'";
        $check_user = $GLOBALS['db']->getRow($sql);
        if (!empty($check_user)) {
            if ($check_user["user_id"] * 1 == $_SESSION["member_uid"] * 1) {
                sys_msg("不能给自己添加线下订单");
            }
            if ($check_user["pay_points"] * 1 < $order_amt * 1) {
                sys_msg("会员分红积分余额不足，无法下单");
            }
            //exit(json_encode(array("code" => 1, "user_id" => $check_user["user_id"], 'user_name' => $check_user['user_name'])));
        } else {
            sys_msg("会员不存在");
        }
    } else {
        sys_msg("金额不合法");
    }
//    if ($_FILES['fp_url']['size'] > 0) {
//        include_once (ROOT_PATH . '/includes/cls_image.php');
//        $fp_image = new cls_image($_CFG['bgcolor']);
//        $fp_image_original = $fp_image->upload_image($_FILES['fp_url'], 'fp_url/' . date('Ym'));
//        $fp_url_path = DATA_DIR . '/fp_url/' . date('Ym') . '/';
//        $fp_url_thumb = $fp_image->make_thumb($fp_image_original, '250', '168', $fp_url_path);
//        $fp_url = $fp_url_thumb ? $fp_url_thumb : $fp_image_original;
//    }
//    if ($_FILES['good_url']['size'] > 0) {
//        include_once (ROOT_PATH . '/includes/cls_image.php');
//        $good_image = new cls_image($_CFG['bgcolor']);
//        $good_image_original = $good_image->upload_image($_FILES['good_url'], 'good_url/' . date('Ym'));
//
//        $good_url_path = DATA_DIR . '/good_url/' . date('Ym') . '/';
//        $good_url_thumb = $good_image->make_thumb($good_image_original, '250', '168', $good_url_path);
//        $good_url = $good_url_thumb ? $good_url_thumb : $good_image_original;
//    }
    $real_supplier_id = 0;
//    if (!empty($_REQUEST['real_supplier_id'])) {
//        $sql = "select supplier_id from " . $GLOBALS['ecs']->table("supplier") . " where user_id=" . intval($_SESSION['user_id']) . " and supplier_id=" . $_REQUEST['real_supplier_id'] . " and zuodan=1";
//        $real_supplier_id = $GLOBALS['db']->getOne($sql);
//        if (empty($real_supplier_id)) {
//            show_message("所选择商家不合法", "返回上一页", 'user.php?act=offline_detail', 'info');
//        } else {
//            $real_supplier_id = $_REQUEST['real_supplier_id'];
//        }
//    }
    /* 代码增加_start By www.68ecshop.com */
    $GLOBALS['db']->startTrans();
    include_once (ROOT_PATH . '/includes/lib_order.php');
    $userDefault = get_user_account_info($_SESSION['member_uid']);

    //读取配置
    $xianxiabd_fei = 0;//$_CFG["xianxiabd_fei"] > 0 ? $_CFG["xianxiabd_fei"] : 0.12; // 报单费

    $hksr = floor($order_amt * (1 - $xianxiabd_fei) * 100) / 100; //暂时90%
    $bd_data = array(
        "user_id" => $check_user["user_id"],
        "supplier_id" => $_SESSION['member_uid'],
        "order_amt" => $order_amt * 1,
        "order_bdf" => ($order_amt * 1 - $hksr),
        "good_name" => $_POST['good_name'],
        "createtime" => gmtime(),
        "status" => 1,
        "order_sn" => get_order_sn(),
        "type" => 1,
        'member_id' => $_SESSION['member_user_id']
    );
    $insertBd = $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table("order_bd"), $bd_data);
    if ($insertBd) {
        $bdId = $GLOBALS['db']->insert_id();
        $currentUser = get_user_account_info($check_user["user_id"]);
        $historyZsq = 0; //线下消费不在计入累计消费和赠送权
        $consum_money_value = 0;
        $addZsq = 0; //需要赠送的赠送权数量
        $resUser = log_account_change($check_user["user_id"], 0, 0, 0, $order_amt * (-1), "会员线下消费，订单记录:" . $bdId . "订单金额" . $order_amt . "", 7, 0, 0, 0, 0, 0);
        $resAffiliate = true;

        $resSupp = log_account_change($_SESSION['member_uid'], 0, 0, 0, 0, "会员线下消费，订单记录:" . $bdId . "订单金额" . $order_amt . ",订单收入:" . $hksr, 7, 0, 0, 0, 0, 0, 0, 0, $hksr, 0, 0, 0);
        $rebeat = array(
            "order_sn" => $bd_data["order_sn"],
            "order_id" => $bdId,
            "supplier_id" => $_SESSION['member_uid'],
            "all_money" => $bd_data["order_amt"],
            "rebate_money" => ($order_amt - $hksr),
            "result_money" => $hksr,
            "pay_id" => -1,
            "pay_name" => "充值积分",
            "texts" => "线下支付",
            "add_time" => $bd_data['createtime'],
            'supplier_parent_id' => $userDefault['parent_id'],
            "is_offline" => 2,
            'real_supplier_id' => $_SESSION['supplier_id'],
            'large_area_id' => $_SESSION['member_large_area_id'],
            'city_id' => $_SESSION['member_city_id'],
            'member_id' => $_SESSION['member_user_id']
        );
        $insertRebeat = $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table("supplier_rebate_log"), $rebeat);
//    }
        if ($resUser && $resSupp && $insertRebeat && $resAffiliate) {



            admin_log("提交线下订单", 'add', 'role_offline_man');
            $GLOBALS['db']->commitTrans();
            pushUserMsg($_SESSION['member_uid'], array('order_no' => $bdId, 'order_sn' => $bd_data['order_sn'], 'order_amt' => $bd_data["order_bdf"]), 6);
            //$_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : "/admin/";
            //$autoUrl = str_replace($_SERVER['REQUEST_URI'], '', $GLOBALS['ecs']->url());
            @file_get_contents("http://" . $_SERVER['HTTP_HOST'] . '/mobile/weixin/auto_do.php?type=6');
            pushUserMsg($check_user["user_id"], array('order_no' => $bdId, 'order_sn' => $bd_data['order_sn'], 'order_amt' => $bd_data["order_amt"]), 7);
            //$_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : "/admin/";
            //$autoUrl = str_replace($_SERVER['REQUEST_URI'], '', $GLOBALS['ecs']->url());
            @file_get_contents("http://" . $_SERVER['HTTP_HOST'] . '/mobile/weixin/auto_do.php?type=7');
            $links = array(
                array('href' => 'role_offline_order.php?act=list', 'text' => "线下订单列表"),
                array('href' => 'role_offline_order.php?act=add', 'text' => "继续添加线下订单"),
            );
            sys_msg("提交线下订单成功", 0, $links);
        } else {
            $GLOBALS['db']->rollbackTrans();
            sys_msg("提交线下订单失败");
        }
    } else {
        $GLOBALS['db']->rollbackTrans();
        sys_msg("提交线下订单失败");
    }
} elseif ($_REQUEST['act'] == 'get_user') {
    if (!empty($_POST['user_id'])) {
        $userInfo = get_user_info_bymobile($_POST['user_id']);
        if (($userInfo["code"] * 1 == 1) && ($userInfo['user_id'] * 1 == $_SESSION["member_uid"] * 1)) {
            exit(json_encode(array("code" => 0, "message" => "不能给自己添加线下订单")));
        } else {
            exit(json_encode($userInfo));
        }
    }
} elseif ($_REQUEST['act'] == 'checkje') {
    checkPoints($_POST['amount']);
} elseif ($_REQUEST['act'] == "send_mobile_code") {
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    require_once (ROOT_PATH . 'includes/lib_validate_record.php');
    $mobile_phone = trim($_POST["mobile"]);



    if (empty($mobile_phone)) {
        exit("手机号不能为空");
        return;
    } else if (!is_mobile_phone($mobile_phone)) {
        exit("手机号格式不正确");
        return;
    } else if (check_validate_record_exist($mobile_phone)) {
        // 获取数据库中的验证记录
        $record = get_validate_record($mobile_phone);

        /**
         * 检查是过了限制发送短信的时间
         */
        $last_send_time = $record['last_send_time'];
        $expired_time = $record['expired_time'];
        $create_time = $record['create_time'];
        $count = $record['count'];

        // 每天每个手机号最多发送的验证码数量
        $max_sms_count = 10;
        // 发送最多验证码数量的限制时间，默认为24小时
        $max_sms_count_time = 60 * 60 * 24;

        if ((time() - $last_send_time) < 60) {
            echo ("每60秒内只能发送一次短信验证码，请稍候重试");
            return;
        } else if (time() - $create_time < $max_sms_count_time && $record['count'] > $max_sms_count) {
            echo ("您发送验证码太过于频繁，请稍后重试！");
            return;
        } else {
            $count ++;
        }
    }

    require_once (ROOT_PATH . 'includes/lib_passport.php');

    // 设置为空
    //$_SESSION[VT_MOBILE_VALIDATE] = array();

    require_once (ROOT_PATH . 'sms/sms.php');

//	// 生成6位短信验证码
//	$mobile_code = rand_number(6);
//	// 短信内容
//	$content = sprintf($_LANG['mobile_code_template'], $GLOBALS['_CFG']['shop_name'], $mobile_code, $GLOBALS['_CFG']['shop_name']);
//	
//	/* 发送激活验证邮件 */
//	$result = sendSMS($mobile_phone, $content);
//	
// 	$result = true;
    // palenggege修改开始

    $mobile_code = createRandomNum(6,'NUMBER');
    $mobile_code = (string) $mobile_code;
    $aa = array(
        'code' => $mobile_code,
        'product' => $GLOBALS['db']->getOne("select value from " . $GLOBALS['ecs']->table("shop_config") . " where code='shop_name'")
    );

    $moban = trim($GLOBALS['db']->getOne("select value from " . $GLOBALS['ecs']->table("shop_config") . " where code='dayu_xiaofei_tpl'")); //$GLOBALS['_CFG']['dayu_xiugai_tpl']

    $result = sendSMS($mobile_phone, $aa, $moban);


    // palenggege修改结束	
    if ($result) {
        if (!isset($count)) {
            $ext_info = array(
                "count" => 1
            );
        } else {
            $ext_info = array(
                "count" => $count
            );
        }
        // 保存验证的手机号
        $_SESSION[VT_MOBILE_VALIDATE] = $mobile_phone;
        // 保存验证信息
        save_validate_record($mobile_phone, $mobile_code, VT_MOBILE_VALIDATE, time(), time() + 30 * 60, $ext_info);
        echo 'ok';
    } else {
        echo '短信验证码发送失败';
    }
}

function checkPoints($amount) {
    if (isset($amount) && !empty($amount)) {
//        $bdFei = $GLOBALS['db']->getOne("select value from " . $GLOBALS['ecs']->table("shop_config") . " where code='xianxiabd_fei'"); //
//        if (empty($bdFei)) {
//            $bdFei = 0.12;
//        }// * $bdFei
        $amount = ($_POST["amount"] * 1);
        if ($amount > 0) {
            $sql = "select `user_id`,`user_name`,`pay_points` from" . $GLOBALS["ecs"]->table("users") . " where `mobile_phone`='" . $_POST["user_id"] . "'";
            $checkjeRow = $GLOBALS['db']->getRow($sql);
            $checkje = $checkjeRow["pay_points"];
            if ($checkje * 1 < $amount * 1) {
                exit(json_encode(array("code" => 0, "message" => "超过会员可用分红积分")));
            } else {
                exit(json_encode(array("code" => 1, "message" => "金额合法")));
            }
        } else {
            exit(json_encode(array("code" => 0, "message" => "金额错误")));
        }
    } else {
        exit(json_encode(array("code" => 0, "message" => "金额错误")));
    }
}

/**
 * 取得办事处列表
 * @return  array
 */
function get_offline_order_list() {
    $result = get_filter();
    if ($result === false) {
        /* 初始化分页参数 */
        $filter = array();
        $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
        $filter['start_time'] = empty($_REQUEST['start_time']) ? '' : (strpos($_REQUEST['start_time'], '-') > 0 ? local_strtotime($_REQUEST['start_time']) : $_REQUEST['start_time']);
        $filter['end_time'] = empty($_REQUEST['end_time']) ? '' : (strpos($_REQUEST['end_time'], '-') > 0 ? local_strtotime($_REQUEST['end_time']) : $_REQUEST['end_time']);

        $where = "WHERE type=1 and  member_id='" . $_SESSION['member_user_id'] . "' ";
        if ($filter['start_time']) {
            $where .= " AND createtime >= '$filter[start_time]'";
        }
        if ($filter['end_time']) {
            $where .= " AND createtime <= '$filter[end_time]'";
        }
        /* 查询记录总数，计算分页数 */
        $sql = "SELECT COUNT(*) as sum_count,sum(order_amt) as sum_order_amt,sum(order_bdf) as sum_order_bd FROM " . $GLOBALS['ecs']->table('order_bd') . "  " . $where;
        $resultRow = $GLOBALS['db']->getRow($sql);
        $filter['record_count'] = $resultRow['sum_count'];
        $filter['sum_order_amt'] = $resultRow['sum_order_amt'];
        $filter['sum_order_bd'] = $resultRow['sum_order_bd'];
        $filter = page_and_size($filter);

        /* 查询记录 */
        $sql = "SELECT s.id,s.createtime,s.good_name,s.order_amt,s.order_bdf,a.user_name,a.real_name FROM " . $GLOBALS['ecs']->table('order_bd') . " s LEFT JOIN " . $GLOBALS['ecs']->table('users') . " a ON s.user_id = a.user_id  " . $where . " ORDER BY $filter[sort_by] $filter[sort_order]";
        set_filter($filter, $sql);
    } else {
        $sql = $result['sql'];
        $filter = $result['filter'];
    }
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);

    $arr = array();
    while ($rows = $GLOBALS['db']->fetchRow($res)) {
        $rows["order_time"] = local_date("Y-m-d H:i:s", $rows['createtime']);
        $arr[] = $rows;
    }
    return array('agency' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count'], 'sum_order_amt' => $filter['sum_order_amt']);
}

/**
 * 生成随机数
 * @param number $len
 * @param string $format
 * @return string|unknown
 */
function createRandomNum($len = 8, $format = 'ALL') {
    $is_abc = $is_numer = 0;
    $password = $tmp = '';
    switch ($format) {
        case 'ALL':
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            break;
        case 'CHAR':
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            break;
        case 'NUMBER':
            $chars = '0123456789';
            break;
        default :
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            break;
    }
    mt_srand((double) microtime() * 1000000 * getmypid());
    while (strlen($password) < $len) {
        $tmp = substr($chars, (mt_rand() % strlen($chars)), 1);
        if (($is_numer <> 1 && is_numeric($tmp) && $tmp > 0 ) || $format == 'CHAR') {
            $is_numer = 1;
        }
        if (($is_abc <> 1 && preg_match('/[a-zA-Z]/', $tmp)) || $format == 'NUMBER') {
            $is_abc = 1;
        }
        $password.= $tmp;
    }
    if ($is_numer <> 1 || $is_abc <> 1 || empty($password)) {
        $password = createRandomNum($len, $format);
    }
    return $password;
}

?>