<?php

/**
 * ECSHOP 会员管理程序
 * ============================================================================
 * 版权所有 2005-2011 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: users.php 17217 2011-01-19 06:29:08Z liubo $
 */
define('IN_ECS', true);

require (dirname(__FILE__) . '/includes/init.php');
/* 代码增加2014-12-23 by www.68ecshop.com _star */
include_once (ROOT_PATH . '/includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);
/* 代码增加2014-12-23 by www.68ecshop.com _end */

$action = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : 'list';

if ($_REQUEST['stype']) {
    $stype = intval($_REQUEST['stype']);
    if (! in_array($stype, array(
        "view",
        "edit"
    ))) {
        $stype = "view";
    }
    unset($_SESSION["users_stype"]);
    $_SESSION["users_stype"] = $stype;
} else {
    if (! empty($_SESSION['users_stype'])) {
        $stype = $_SESSION["users_stype"];
    } else {
        $stype = "view";
    }
}

if ($stype == "view") {
    $adminpriv = "1_scores_manage";
} else {
    $adminpriv = "1_user_manage";
}

/* 路由 */

$function_name = 'action_' . $action;

if (! function_exists($function_name)) {
    $function_name = "action_list";
}

call_user_func($function_name);

/* 路由 */

/* ------------------------------------------------------ */

// -- 用户帐号列表
/* ------------------------------------------------------ */
function action_list()
{
    $stype = intval($_REQUEST['stype']);
    if ($stype == "view") {
        $adminpriv = "1_scores_manage";
    } else {
        $adminpriv = "users_manage_list";
    }
    admin_priv($adminpriv);
    // 全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    
  
    
    assign('user_ranks', $ranks);
    assign('ur_here', $_LANG['03_users_list']);
    assign('action_link', array(
        'text' => $_LANG['04_users_add'],
        'href' => 'users.php?act=add'
    ));
    
    /*
     * 城市列表
     */
    $sql = 'SELECT region_id, region_name FROM ' . $GLOBALS['ecs']->table('region') . " WHERE parent_id = 1 ";
    $daqu_name = db_getAll($sql);
    assign('province_list', $daqu_name);
    $user_list = user_list();
    
    $count = count($user_list);
    for ($i = 0; $i < $count; $i ++) {
        
//         $user_list[$i]['pu_huo'] = getPuHuoXinXi($user_list[$i]['user_name']);
    }
    assign('user_list', $user_list['user_list']);
    assign('filter', $user_list['filter']);
    assign('record_count', $user_list['record_count']);
    assign('page_count', $user_list['page_count']);
    assign('full_page', 1);
    assign('sort_user_id', '<img src="images/sort_desc.gif">');
    
    assign_query_info();
    display('users_list.htm');
}

 
/* ------------------------------------------------------ */

// -- ajax返回用户列表
/* ------------------------------------------------------ */
function action_query()
{
    // 全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    
    $ecs = $GLOBALS['ecs'];
    
    $user_list = user_list();
    
    assign('user_list', $user_list['user_list']);
    assign('filter', $user_list['filter']);
    assign('record_count', $user_list['record_count']);
    assign('page_count', $user_list['page_count']);
    
    $sort_flag = sort_flag($user_list['filter']);
    assign($sort_flag['tag'], $sort_flag['img']);
    
    make_json_result(fetch('users_list.htm'), '', array(
        'filter' => $user_list['filter'],
        'page_count' => $user_list['page_count']
    ));
}

function action_export()
{
    
    global $adminpriv;
    admin_priv($adminpriv);
    // header("Content-type: application/vnd.ms-excel; charset=gbk");
    // header("Content-Disposition: attachment; filename=user_account.xls");
    //
    // $export = "<table border='1'><tr><td colspan='2'>会员名称</td><td colspan='2'>操作日期</td><td colspan='2'>类型</td><td colspan='2'>金额</td><td colspan='2'>支付方式</td><td colspan='2'>到款状态</td><td colspan='2'>真实姓名</td><td colspan='2'>银行</td><td colspan='2'>开户行</td><td colspan='2'>银行卡号</td></tr>";
    // $result = account_list();
    // foreach ($result['list'] as $key => $val) {
    // $unconfirm = "未确认";
    // if ($val['is_paid'] * 1 == 1) {
    // $unconfirm = "已完成";
    // }
    // $paymentName = $val['payment'];
    // if (empty($paymentName)) {
    // $paymentName = "N/A";
    // }
    // $bank="".$val['bank_num'];
    // $export .= "<tr><td colspan='2'>" . $val['user_name'] . "</td><td colspan='2'>" . $val['add_date'] . "</td><td colspan='2'>" . $val['process_type_name'] . "</td><td colspan='2'>" . $val['surplus_amount_formatted'] . "</td><td colspan='2'>" . $paymentName . "</td><td colspan='2'>" . $unconfirm . "</td><td colspan='2'>" . $val['real_name'] . "</td><td colspan='2'>" . $val['bank'] . "</td><td colspan='2'>" . $val['bank_kh'] . "</td><td colspan='2'> " . (' '.$bank." ") . "</td></tr>";
    // }
    // $export .= "</table>";
    // if (EC_CHARSET != 'gbk') {
    // echo ecs_iconv(EC_CHARSET, 'gbk', $export) . "\t";
    // } else {
    // echo $export . "\t";
    // }
    // 引入phpexcel核心类文件
    $result = user_list();
    require_once ROOT_PATH . '/includes/phpexcel/Classes/PHPExcel.php';
    // 实例化excel类
    $objPHPExcel = new PHPExcel();
    // 实例化excel图片处理类
    $objDrawing = new PHPExcel_Worksheet_Drawing();
    // 操作第一个工作表
    $objPHPExcel->setActiveSheetIndex(0);
    // 设置sheet名
    $objPHPExcel->getActiveSheet()->setTitle('会员列表');
    
    // 表格宽度
    // $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(100);
    // $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(100);
    // $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(100);
    // $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(100);
    // $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(100);
    // $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(100);
    // $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(100);
    // $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(100);
    // $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(100);
    // $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(100);
    // 列名表头加粗
    $objPHPExcel->getActiveSheet()
        ->getStyle('A1:R1')
        ->getFont()
        ->setBold(true);
    // 列名赋值
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '会员编号');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '会员名称');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '会员手机号');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '充值积分');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '分红积分');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '冻结积分');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', '累计消费');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', '累计赠送积分');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', '成功提现积分');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', '累计赠送权');
    $objPHPExcel->getActiveSheet()->setCellValue('K1', '当前赠送权');
    $objPHPExcel->getActiveSheet()->setCellValue('L1', '推荐人');
    $objPHPExcel->getActiveSheet()->setCellValue('M1', '注册日期');
    $objPHPExcel->getActiveSheet()->setCellValue('N1', '养老积分');
    $objPHPExcel->getActiveSheet()->setCellValue('O1', '互助基金');
    $objPHPExcel->getActiveSheet()->setCellValue('P1', '大家庭分红');
    $objPHPExcel->getActiveSheet()->setCellValue('Q1', '基数奖励');
    $objPHPExcel->getActiveSheet()->setCellValue('R1', '奖励基数');
    $objPHPExcel->getActiveSheet()->setCellValue('S1', '累计收益分红权');
    $objPHPExcel->getActiveSheet()->setCellValue('T1', '现有收益分红权');
    // 数据起始行
    $row_num = 2;
    // 向每行单元格插入数据
    foreach ($result['user_list'] as $key => $value) {
        // 设置单元格高度
        $objPHPExcel->getActiveSheet()
            ->getRowDimension($row_num)
            ->setRowHeight(32);
        // 设置排序列、是否显示列居中显示
        // 设置单元格数值
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_num, " " . $value['user_id']);
        $real_name = "";
        if (! empty($value['real_name'])) {
            $real_name = "(" . $value['real_name'] . ")";
        }
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $row_num, " " . $value['user_name'] . $real_name);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $row_num, " " . $value['mobile_phone']);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $row_num, $value['user_money']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $row_num, $value['pay_points']);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $row_num, $value['frozen_points']);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $row_num, $value['consum_money']);
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $row_num, $value['give_points']);
        $objPHPExcel->getActiveSheet()->setCellValue('I' . $row_num, $value['tx_points']); // history_zsq,zsq,tj_name,reg_time
        $objPHPExcel->getActiveSheet()->setCellValue('J' . $row_num, $value['history_zsq']);
        $objPHPExcel->getActiveSheet()->setCellValue('K' . $row_num, $value['zsq']);
        $objPHPExcel->getActiveSheet()->setCellValue('L' . $row_num, " " . $value['tj_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('M' . $row_num, " " . $value['reg_time'], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValue('N' . $row_num, $value['yl_points']);
        $objPHPExcel->getActiveSheet()->setCellValue('O' . $row_num, $value['hz_points']);
        $objPHPExcel->getActiveSheet()->setCellValue('P' . $row_num, $value['djtfh_points']);
        $objPHPExcel->getActiveSheet()->setCellValue('Q' . $row_num, $value['gz_points']);
        $objPHPExcel->getActiveSheet()->setCellValue('R' . $row_num, $value['jljs_points']);
        $objPHPExcel->getActiveSheet()->setCellValue('S' . $row_num, $value['sg_history_zsq']);
        $objPHPExcel->getActiveSheet()->setCellValue('T' . $row_num, $value['sg_zsq']);
        $row_num ++;
    }
    $outputFileName = '会员列表_' . time() . '.xls';
    $xlsWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");
    header('Content-Disposition:inline;filename="' . iconv("UTF-8", "gb2312", $outputFileName) . '"');
    header("Content-Transfer-Encoding: binary");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Pragma: no-cache");
    $xlsWriter->save("php://output");
    echo file_get_contents($outputFileName);
}

/* ------------------------------------------------------ */

// -- 添加会员帐号
/* ------------------------------------------------------ */
function action_add()
{
    global $adminpriv;
    // 全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    
    /* 检查权限 */
    admin_priv($adminpriv);
    
    $user = array(
        'rank_points' => $_CFG['register_points'],
        'pay_points' => $_CFG['register_points'],
        'sex' => 0,
        'credit_line' => 0
    );
    /* 取出注册扩展字段 */
    $sql = 'SELECT * FROM ' . db_table('reg_fields') . ' WHERE type < 2 AND display = 1 AND id != 6 ORDER BY dis_order, id';
    $extend_info_list = db_getAll($sql);
    assign('extend_info_list', $extend_info_list);
    
    assign('ur_here', $_LANG['04_users_add']);
    assign('action_link', array(
        'text' => $_LANG['03_users_list'],
        'href' => 'users.php?act=list'
    ));
    assign('form_action', 'insert');
    assign('user', $user);
    assign('special_ranks', get_rank_list(true));
    
    assign('lang', $_LANG);
    assign('country_list', get_regions());
    $province_list = get_regions(1, $row['country']);
    $city_list = get_regions(2, $row['province']);
    $district_list = get_regions(3, $row['city']);
    
    assign('province_list', $province_list);
    assign('city_list', $city_list);
    assign('district_list', $district_list);
    
    assign_query_info();
    display('user_info.htm');
}

/* ------------------------------------------------------ */

// -- 添加会员帐号
/* ------------------------------------------------------ */
function action_insert()
{
    global $adminpriv;
    // 全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    
    /* 检查权限 */
    admin_priv($adminpriv);
    $username = empty($_POST['username']) ? '' : trim($_POST['username']);
    $password = empty($_POST['password']) ? '' : trim($_POST['password']);
    $email = empty($_POST['email']) ? '' : trim($_POST['email']);
    $mobile_phone = empty($_POST['mobile_phone']) ? '' : trim($_POST['mobile_phone']);
    $sex = empty($_POST['sex']) ? 0 : intval($_POST['sex']);
    $sex = in_array($sex, array(
        0,
        1,
        2
    )) ? $sex : 0;
    $birthday = $_POST['birthdayYear'] . '-' . $_POST['birthdayMonth'] . '-' . $_POST['birthdayDay'];
    $rank = empty($_POST['user_rank']) ? 0 : intval($_POST['user_rank']);
    $credit_line = empty($_POST['credit_line']) ? 0 : floatval($_POST['credit_line']);
    
    $real_name = empty($_POST['real_name']) ? '' : trim($_POST['real_name']);
    $card = empty($_POST['card']) ? '' : trim($_POST['card']);
    $country = $_POST['country'];
    $province = $_POST['province'];
    $city = $_POST['city'];
    $district = $_POST['district'];
    $address = empty($_POST['address']) ? '' : trim($_POST['address']);
    $status = $_POST['status'];
    
    $users = & init_users();
    if (empty($email)) {
        $email = $mobile_phone;
    }
    
    if (! $users->add_user($username, $password, $email)) {
        /* 插入会员数据失败 */
        if ($users->error == ERR_INVALID_USERNAME) {
            $msg = $_LANG['username_invalid'];
        } elseif ($users->error == ERR_USERNAME_NOT_ALLOW) {
            $msg = $_LANG['username_not_allow'];
        } elseif ($users->error == ERR_USERNAME_EXISTS) {
            $msg = $_LANG['username_exists'];
        } elseif ($users->error == ERR_INVALID_EMAIL) {
            $msg = $_LANG['email_invalid'];
        } elseif ($users->error == ERR_EMAIL_NOT_ALLOW) {
            $msg = $_LANG['email_not_allow'];
        } elseif ($users->error == ERR_EMAIL_EXISTS) {
            $msg = $_LANG['email_exists'];
        } else {
            $msg = "未知错误";
            // die('Error:'.$users->error_msg());
        }
        sys_msg($msg, 1);
    }
    
    /* 注册送积分 */
    if (! empty($GLOBALS['_CFG']['register_points'])) {
        log_account_change($_SESSION['user_id'], 0, 0, $GLOBALS['_CFG']['register_points'], $GLOBALS['_CFG']['register_points'], $_LANG['register_points']);
    }
    
    /* 把新注册用户的扩展信息插入数据库 */
    $sql = 'SELECT id FROM ' . db_table('reg_fields') . ' WHERE type = 0 AND display = 1 ORDER BY dis_order, id'; // 读出所有扩展字段的id
    $fields_arr = db_getAll($sql);
    
    $extend_field_str = ''; // 生成扩展字段的内容字符串
    $user_id_arr = $users->get_profile_by_name($username);
    foreach ($fields_arr as $val) {
        $extend_field_index = 'extend_field' . $val['id'];
        if (! empty($_POST[$extend_field_index])) {
            $temp_field_content = strlen($_POST[$extend_field_index]) > 100 ? mb_substr($_POST[$extend_field_index], 0, 99) : $_POST[$extend_field_index];
            $extend_field_str .= " ('" . $user_id_arr['user_id'] . "', '" . $val['id'] . "', '" . $temp_field_content . "'),";
        }
    }
    $extend_field_str = substr($extend_field_str, 0, - 1);
    
    if ($extend_field_str) { // 插入注册扩展数据
        $sql = 'INSERT INTO ' . db_table('reg_extend_info') . ' (`user_id`, `reg_field_id`, `content`) VALUES' . $extend_field_str;
        db_query($sql);
    }
    
    /* 更新会员的其它信息 */
    $other = array();
    $other['credit_line'] = $credit_line;
    $other['user_rank'] = $rank;
    $other['sex'] = $sex;
    $other['birthday'] = $birthday;
    $other['reg_time'] = local_strtotime(local_date('Y-m-d H:i:s'));
    
    $other['msn'] = isset($_POST['extend_field1']) ? htmlspecialchars(trim($_POST['extend_field1'])) : '';
    $other['qq'] = isset($_POST['extend_field2']) ? htmlspecialchars(trim($_POST['extend_field2'])) : '';
    $other['office_phone'] = isset($_POST['extend_field3']) ? htmlspecialchars(trim($_POST['extend_field3'])) : '';
    $other['home_phone'] = isset($_POST['extend_field4']) ? htmlspecialchars(trim($_POST['extend_field4'])) : '';
    $other['mobile_phone'] = isset($_POST['extend_field5']) ? htmlspecialchars(trim($_POST['extend_field5'])) : '';
    
    db_autoExecute(db_table('users'), $other, 'UPDATE', "user_name = '$username'");
    
    include_once (ROOT_PATH . '/includes/cls_image.php');
    $image = new cls_image($_CFG['bgcolor']);
    
    /* 代码增加2014-12-23 by www.68ecshop.com _star */
    if (isset($_FILES['face_card']) && $_FILES['face_card']['tmp_name'] != '') {
        $face_card = $image->upload_image($_FILES['face_card']);
        if ($face_card === false) {
            sys_msg($image->error_msg(), 1, array(), false);
        }
    }
    if (isset($_FILES['back_card']) && $_FILES['back_card']['tmp_name'] != '') {
        $back_card = $image->upload_image($_FILES['back_card']);
        if ($back_card === false) {
            sys_msg($image->error_msg(), 1, array(), false);
        }
    }
    
    // 周:添加用户时,默认用户的类型为普通用户
    $sql = "update " . db_table('users') . " set  user_type=" . USER_TYPE_NORMAL . ", mobile_phone='$mobile_phone' , `real_name`='$real_name',`card`='$card',`country`='$country',`province`='$province',`city`='$city',`district`='$district',`address`='$address',`status`='$status' where user_name = '" . $username . "'";
    db_query($sql);
    
    if ($face_card != '') {
        $sql = "update " . db_table('users') . " set `face_card` = '$face_card' where user_name = '" . $username . "'";
        db_query($sql);
    }
    if ($back_card != '') {
        $sql = "update " . db_table('users') . " set `back_card` = '$back_card' where user_name = '" . $username . "'";
        db_query($sql);
    }
    /* 代码增加2014-12-23 by www.68ecshop.com _end */
    /* 记录管理员操作 */
    admin_log($_POST['username'], 'add', 'users');
    
    /* 提示信息 */
    $link[] = array(
        'text' => $_LANG['go_back'],
        'href' => 'users.php?act=list&stype=' . $stype . ''
    );
    sys_msg(sprintf($_LANG['add_success'], htmlspecialchars(stripslashes($_POST['username']))), 0, $link);
}

/* ------------------------------------------------------ */

// -- 编辑用户帐号
/* ------------------------------------------------------ */
function action_edit()
{
    global $adminpriv ;
    // 全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    
    /* 检查权限 */
    admin_priv($adminpriv);
    
    $sql = "SELECT  u.level,u.user_type, u.user_name, u.sex, u.birthday, u.pay_points, u.rank_points, u.user_rank , u.user_money, u.frozen_money, u.credit_line, u.parent_id, u2.user_name as parent_username,    u.mobile_phone " . " FROM " . db_table('users') . " u LEFT JOIN " . db_table('users') . " u2 ON u.parent_id = u2.user_id WHERE u.user_id='$_GET[id]'";
  
    $row = db_getRow($sql);

    $row['user_name'] = addslashes($row['user_name']);
    $users = & init_users();
    $user = $users->get_user_info($row['user_name']);
   
    /* 代码增加2014-12-23 by www.68ecshop.com _star */
    $sql = "SELECT  u.level,u.pai_che_state, u.pai_che_ming_ci,  u.user_type, u.consum_money, u.user_id, u.sex, u.birthday, u.pay_points, u.user_rank , u.user_money, u.frozen_money,   u.parent_id, u2.user_name as parent_username,  
     u.mobile_phone,u.real_name,u.card,u.face_card,u.back_card,u.country,u.province,u.city,u.district,u.address,u.status,u.bank,u.bank_kh,u.bank_num   " . " FROM " . db_table('users') . " u LEFT JOIN " . db_table('users') . " u2 ON u.parent_id = u2.user_id WHERE u.user_id='$_GET[id]'";
    /* 代码增加2014-12-23 by www.68ecshop.com _end */
     
    $row = db_getRow($sql);
   
    if ($row) {
        $user['pai_che_state'] = $row['pai_che_state'];
        $user['pai_che_ming_ci'] = $row['pai_che_ming_ci'];
        $user['level'] = $row['level'];
        $user['user_id'] = $row['user_id'];
        $user['sex'] = $row['sex'];
        $user['birthday'] = date($row['birthday']);
        $user['pay_points'] = $row['pay_points'];
        $user['rank_points'] = $row['rank_points'];
        $user['user_rank'] = $row['user_rank'];
        $user['user_money'] = $row['user_money'];
        $user['frozen_money'] = $row['frozen_money'];
        
        $user['consum_money'] = $row['consum_money'];
        $user['formated_user_money'] = price_format($row['user_money']);
        $user['formated_frozen_money'] = price_format($row['frozen_money']);
        $user['parent_id'] = $row['parent_id'];
        $user['parent_username'] = $row['parent_username'];
        $user['mobile_phone'] = $row['mobile_phone'];
  
        $user['real_name'] = $row['real_name'];
        $user['card'] = $row['card'];
        $user['face_card'] = $row['face_card'];
        $user['back_card'] = $row['back_card'];
        $user['country'] = $row['country'];
        $user['province'] = $row['province'];
        $user['city'] = $row['city'];
        $user['district'] = $row['district'];
        $user['address'] = $row['address'];
        $user['status'] = $row['status'];
        
        $user['bank'] = $row['bank'];
        $user['bank_kh'] = $row['bank_kh'];
        $user['bank_num'] = $row['bank_num'];
        
       
    } else {
        $link[] = array(
            'text' => $_LANG['go_back'],
            'href' => 'users.php?act=list&stype=' . $stype . ''
        );
        sys_msg($_LANG['username_invalid'], 0, $links);
    }
    
    /* 取出注册扩展字段 */
    $sql = 'SELECT * FROM ' . db_table('reg_fields') . ' WHERE type < 2 AND display = 1 AND id != 6 ORDER BY dis_order, id';
    $extend_info_list = db_getAll($sql);
    
    $sql = 'SELECT reg_field_id, content ' . 'FROM ' . db_table('reg_extend_info') . " WHERE user_id = $user[user_id]";
    $extend_info_arr = db_getAll($sql);
    
    $temp_arr = array();
    foreach ($extend_info_arr as $val) {
        $temp_arr[$val['reg_field_id']] = $val['content'];
    }
    
    foreach ($extend_info_list as $key => $val) {
        switch ($val['id']) {
            case 1:
                $extend_info_list[$key]['content'] = $user['msn'];
                break;
            case 2:
                $extend_info_list[$key]['content'] = $user['qq'];
                break;
            case 3:
                $extend_info_list[$key]['content'] = $user['office_phone'];
                break;
            case 4:
                $extend_info_list[$key]['content'] = $user['home_phone'];
                break;
            case 5:
                $extend_info_list[$key]['content'] = $user['mobile_phone'];
                break;
            default:
                $extend_info_list[$key]['content'] = empty($temp_arr[$val['id']]) ? '' : $temp_arr[$val['id']];
        }
    }
    
    assign('extend_info_list', $extend_info_list);
    
   
    
   
    /* 代码增加2014-12-23 by www.68ecshop.com _star */
    assign('lang', $_LANG);
    assign('country_list', get_regions());
    $province_list = get_regions(1, $row['country']);
    $city_list = get_regions(2, $row['province']);
    $district_list = get_regions(3, $row['city']);
    
    assign('province_list', $province_list);
    assign('city_list', $city_list);
    assign('district_list', $district_list);
    /* 代码增加2014-12-23 by www.68ecshop.com _end */
 
    assign_query_info();
    assign('ur_here', $_LANG['users_edit']);
    assign('action_link', array(
        'text' => $_LANG['03_users_list'],
        'href' => 'users.php?act=list&stype=' . $stype . '&' . list_link_postfix()
    ));
    assign('user', $user);
    assign('form_action', 'update');
    assign('special_ranks', get_rank_list(true));
    display('user_info_edit.htm');
}

/* ------------------------------------------------------ */

// -- 更新用户帐号
/* ------------------------------------------------------ */
function action_update()
{
    
    // 全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    
    $ecs = $GLOBALS['ecs'];
    global $adminpriv;
    /* 检查权限 */
    admin_priv($adminpriv);
    $username = empty($_POST['username']) ? '' : trim($_POST['username']);
    $password = empty($_POST['password']) ? '' : trim($_POST['password']);
    $email = empty($_POST['email']) ? '' : trim($_POST['email']);
    $mobile_phone = empty($_POST['mobile_phone']) ? '' : trim($_POST['mobile_phone']);
    $sex = empty($_POST['sex']) ? 0 : intval($_POST['sex']);
    $sex = in_array($sex, array(
        0,
        1,
        2
    )) ? $sex : 0;
    $birthday = $_POST['birthdayYear'] . '-' . $_POST['birthdayMonth'] . '-' . $_POST['birthdayDay'];
    $rank = empty($_POST['user_rank']) ? 0 : intval($_POST['user_rank']);
    $credit_line = empty($_POST['credit_line']) ? 0 : floatval($_POST['credit_line']);
    /* 代码增加2014-12-23 by www.68ecshop.com _star */
    $real_name = empty($_POST['real_name']) ? '' : trim($_POST['real_name']);
    $card = empty($_POST['card']) ? '' : trim($_POST['card']);
    $country = $_POST['country'];
    $province = $_POST['province'];
    $city = $_POST['city'];
    $district = $_POST['district'];
    $address = empty($_POST['address']) ? '' : trim($_POST['address']);
    $status = $_POST['status'];
    $pai_che_state = $_POST['pai_che_state'];
    
    $level = intval($_POST['level']);
    $user_id = intval($_POST['id']);
    /* 代码增加2014-12-23 by www.68ecshop.com _end */
  
    $users = & init_users();
    
    // 获取用户邮箱和手机号已经验证信息,如果手机号、邮箱变更则需验证，如果未变化则沿用原来的验证结果
    $user = getUserInfo($user_id);
    
    
    
    if($level!=$user['level']){//如果修改了用户的级别
        updateUserLevel($user_id, $level);
    }
 
    if (empty($mobile_phone)) {
        $sql = "select mobile_phone from " . $GLOBALS['ecs']->table("users") . " where user_id={$user_id}";
        $mobile_phone = $GLOBALS['db']->getOne($sql);
    }
    $profile = array(
        'username' => $username,
        'password' => $password,
        'email' => $email,
        'mobile_phone' => $mobile_phone,
        'gender' => $sex,
        'bday' => $birthday
    );
    
    $parent_id = 0;
   
     
    if ($user['mobile_phone'] != $mobile_phone) {
        $profile['mobile_validated'] = 0;
    } else {
        $profile['mobile_validated'] = $user['mobile_validated'];
    }
  
    $result = $users->edit_user($profile, 1);
    
    if (! $result) {
        if ($users->error == ERR_EMAIL_EXISTS) {
            $msg = $_LANG['email_exists'];
        } else if ($users->error == ERR_MOBILE_PHONE_EXISTS) {
            $msg = $_LANG['mobile_phone_exists'];
        } else {
            $msg = $_LANG['edit_user_failed'];
        }
        sys_msg($msg, 1);
    }
    if (! empty($password)) {
        $sql = "UPDATE " . db_table('users') . "SET `ec_salt`='0' WHERE user_id= " . $user_id . "";
        db_query($sql);
    }
    
    include_once (ROOT_PATH . '/includes/cls_image.php');
    $image = new cls_image($_CFG['bgcolor']);
    
    /* 代码增加2014-12-23 by www.68ecshop.com _star */
    if (isset($_FILES['face_card']) && $_FILES['face_card']['tmp_name'] != '') {
        $face_card = $image->upload_image($_FILES['face_card']);
        if ($face_card === false) {
            sys_msg($image->error_msg(), 1, array(), false);
        }
    }
    if (isset($_FILES['back_card']) && $_FILES['back_card']['tmp_name'] != '') {
        $back_card = $image->upload_image($_FILES['back_card']);
        if ($back_card === false) {
            sys_msg($image->error_msg(), 1, array(), false);
        }
    }
   
    
    if($pai_che_state==1){
        $ming_ci = 0;
        $ming_ci=db_getOne("select max(pai_che_ming_ci) from xbmall_users");
        $ming_ci =$ming_ci+1;
        $sql = "update " . db_table('users') . " set level=1, pai_che_state=1,pai_che_ming_ci={$ming_ci},  `real_name`='$real_name',`card`='$card',`country`='$country',`province`='$province',`city`='$city',`district`='$district',`address`='$address',`status`='$status' where user_name = '" . $username . "'";
    }else{//排车状态不是1的时候, 不修改排车名次
        $sql = "update " . db_table('users') . " set level=" . $level . ",pai_che_state={$pai_che_state}, `real_name`='$real_name',`card`='$card',`country`='$country',`province`='$province',`city`='$city',`district`='$district',`address`='$address',`status`='$status' where user_name = '" . $username . "'";
    }
     db_query($sql);
    
    
    if ($parent_id * 1 > 0) {
        $sql = "update " . db_table('users') . " set `parent_id`='$parent_id' where user_id = " . $user_id . "";
        db_query($sql);
    }
    if ($face_card != '') {
        $sql = "update " . db_table('users') . " set `face_card` = '$face_card' where user_id = " . $user_id . "";
        db_query($sql);
    }
    if ($back_card != '') {
        $sql = "update " . db_table('users') . " set `back_card` = '$back_card' where user_id = ". $user_id . "";
        db_query($sql);
    }
  
    /* 更新用户扩展字段的数据 */
    $sql = 'SELECT id FROM ' . db_table('reg_fields') . ' WHERE type = 0 AND display = 1 ORDER BY dis_order, id'; // 读出所有扩展字段的id
    $fields_arr = db_getAll($sql);
    $user_id_arr = $users->get_profile_by_name($username);
    $user_id = $user_id_arr['user_id'];
    
    foreach ($fields_arr as $val) { // 循环更新扩展用户信息
        $extend_field_index = 'extend_field' . $val['id'];
        if (isset($_POST[$extend_field_index])) {
            $temp_field_content = strlen($_POST[$extend_field_index]) > 100 ? mb_substr($_POST[$extend_field_index], 0, 99) : $_POST[$extend_field_index];
            
            $sql = 'SELECT * FROM ' . db_table('reg_extend_info') . "  WHERE reg_field_id = '$val[id]' AND user_id = '$user_id'";
            if (db_getOne($sql)) { // 如果之前没有记录，则插入
                $sql = 'UPDATE ' . db_table('reg_extend_info') . " SET content = '$temp_field_content' WHERE reg_field_id = '$val[id]' AND user_id = '$user_id'";
            } else {
                $sql = 'INSERT INTO ' . db_table('reg_extend_info') . " (`user_id`, `reg_field_id`, `content`) VALUES ('$user_id', '$val[id]', '$temp_field_content')";
            }
            db_query($sql);
        }
    }
    
    /* 更新会员的其它信息 */
    $other = array();
    $other['credit_line'] = $credit_line;
    $other['user_rank'] = $rank;
    
    $other['msn'] = isset($_POST['extend_field1']) ? htmlspecialchars(trim($_POST['extend_field1'])) : '';
    $other['qq'] = isset($_POST['extend_field2']) ? htmlspecialchars(trim($_POST['extend_field2'])) : '';
    $other['office_phone'] = isset($_POST['extend_field3']) ? htmlspecialchars(trim($_POST['extend_field3'])) : '';
    $other['home_phone'] = isset($_POST['extend_field4']) ? htmlspecialchars(trim($_POST['extend_field4'])) : '';
    
    
    // dqy add end 2015-1-6
    
    db_autoExecute(db_table('users'), $other, 'UPDATE', "user_name = '$username'");
    
    /* 记录管理员操作 */
    admin_log($username, 'edit', 'users');
    
    /* 提示信息 */
    $links[0]['text'] = $_LANG['goto_list'];
    $links[0]['href'] = 'users.php?act=list&stype=' . $stype . '&' . list_link_postfix();
    $links[1]['text'] = $_LANG['go_back'];
    $links[1]['href'] = 'javascript:history.back()';
    
    sys_msg($_LANG['update_success'], 0, $links);
}


function getUserInfo($user_id)
{
    $sql = "select * from xbmall_users where user_id={$user_id}";
    $userInfo = db_getRow($sql);
    return $userInfo;
}



function  updateUserLevel($user_id, $level){
    if($level==1){//设置下面的人的代理id为$user_id
        updateTiXiDaiLi($user_id, $user_id);
    }else{
        //设置下面的人的代理id为null
        updateTiXiDaiLi($user_id, null);
    }
}

/**
 * 修改体系下的所有人的代理id
 * @param unknown $user_id
 * @param unknown $dai_li_id
 */
function  updateTiXiDaiLi($pid, $dai_li_id){
    if($dai_li_id==null){
        $sql="update xbmall_users set qu_yu_user=null where parent_id={$pid}";
    }else{
        $sql="update xbmall_users set qu_yu_user={$dai_li_id} where parent_id={$pid}";
    }
    
    db_query($sql);
  
    $sql="select user_id from xbmall_users where parent_id={$pid} ";
    $userIds = db_getAll($sql);
    
    foreach ($userIds as $key=>$value){
        updateTiXiDaiLi($value['user_id'], $dai_li_id);
    }
  
}
/* ------------------------------------------------------ */

// -- 批量删除会员帐号
/* ------------------------------------------------------ */
function action_batch_remove()
{
    // 全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    
    /* 检查权限 */
    admin_priv('users_drop');
    
    if (isset($_POST['checkboxes'])) {
        $sql = "SELECT user_name FROM " . db_table('users') . " WHERE user_id " . db_create_in($_POST['checkboxes']);
        $col = db_getCol($sql);
        $usernames = implode(',', addslashes_deep($col));
        $count = count($col);
        /* 通过插件来删除用户 */
        $users = & init_users();
        $users->remove_user($col);
        
        admin_log($usernames, 'batch_remove', 'users');
        
        $lnk[] = array(
            'text' => $_LANG['go_back'],
            'href' => 'users.php?act=list&stype=' . $stype . ''
        );
        sys_msg(sprintf($_LANG['batch_remove_success'], $count), 0, $lnk);
    } else {
        $lnk[] = array(
            'text' => $_LANG['go_back'],
            'href' => 'users.php?act=list&stype=' . $stype . ''
        );
        sys_msg($_LANG['no_select_user'], 0, $lnk);
    }
}

/* 编辑用户名 */
function action_edit_username()
{
    // 全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    global $adminpriv;
    
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    
    /* 检查权限 */
    check_authz_json($adminpriv);
    
    $username = empty($_REQUEST['val']) ? '' : json_str_iconv(trim($_REQUEST['val']));
    $id = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);
    
    if ($id == 0) {
        make_json_error('NO USER ID');
        return;
    }
    
    if ($username == '') {
        make_json_error($GLOBALS['_LANG']['username_empty']);
        return;
    }
    
    $users = & init_users();
    
    if ($users->edit_user($id, $username)) {
        if ($_CFG['integrate_code'] != 'ecshop') {
            /* 更新商城会员表 */
            db_query('UPDATE ' . db_table('users') . " SET user_name = '$username' WHERE user_id = '$id'");
        }
        
        admin_log(addslashes($username), 'edit', 'users');
        make_json_result(stripcslashes($username));
    } else {
        $msg = ($users->error == ERR_USERNAME_EXISTS) ? $GLOBALS['_LANG']['username_exists'] : $GLOBALS['_LANG']['edit_user_failed'];
        make_json_error($msg);
    }
}

/* ------------------------------------------------------ */

// -- 编辑email
/* ------------------------------------------------------ */
function action_edit_email()
{
    // 全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    global $adminpriv;
    
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    
    /* 检查权限 */
    check_authz_json($adminpriv);
    
    $id = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);
    $email = empty($_REQUEST['val']) ? '' : json_str_iconv(trim($_REQUEST['val']));
    
    $users = & init_users();
    
    $sql = "SELECT user_name FROM " . db_table('users') . " WHERE user_id = '$id'";
    $username = db_getOne($sql);
    
    if (is_email($email)) {
        if ($users->edit_user(array(
            'username' => $username,
            'email' => $email
        ))) {
            admin_log(addslashes($username), 'edit', 'users');
            
            make_json_result(stripcslashes($email));
        } else {
            $msg = ($users->error == ERR_EMAIL_EXISTS) ? $GLOBALS['_LANG']['email_exists'] : $GLOBALS['_LANG']['edit_user_failed'];
            make_json_error($msg);
        }
    } else {
        make_json_error($GLOBALS['_LANG']['invalid_email']);
    }
}

function  action_edit_tjsy_rate(){
    global $adminpriv,$ecs;
    check_authz_json($adminpriv);
    $id = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);
    $rate= empty($_REQUEST['val']) ? '0' : intval($_REQUEST['val'] );
    $sql="update " . db_table('users') . " set shi_fang_tjsy_rate={$rate}  where user_id={$id} ";
    $result = db_query($sql);
    if ($result) {
        make_json_result(stripcslashes($rate));
    } else {
        make_json_error("修改出错");
    }
}

function  action_edit_tjsy_ts(){
    global $adminpriv,$ecs;
    check_authz_json($adminpriv);
    $id = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);
    $rate= empty($_REQUEST['val']) ? '0' : intval($_REQUEST['val'] );
    $sql="update " . db_table('users') . " set shi_fang_tjsy_ts={$rate}  where user_id={$id} ";
    $result = db_query($sql);
    if ($result) {
        make_json_result(stripcslashes($rate));
    } else {
        make_json_error("修改出错");
    }
}



function  action_edit_qdsy_rate(){
    global $adminpriv,$ecs;
    check_authz_json($adminpriv);
    $id = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);
    $rate= empty($_REQUEST['val']) ? '' : json_str_iconv(trim($_REQUEST['val']));
    $sql="update " . db_table('users') . " set shi_fang_qdsy_rate={$rate}  where user_id={$id} ";
    $result = db_query($sql);
    if ($result) {
        make_json_result(stripcslashes($rate));
    } else {
        make_json_error("修改出错");
    }
}



function  action_edit_qdsy_ts(){
    global $adminpriv,$ecs;
    check_authz_json($adminpriv);
    $id = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);
    $rate= empty($_REQUEST['val']) ? '' : json_str_iconv(trim($_REQUEST['val']));
    $sql="update " . db_table('users') . " set shi_fang_qdsy_ts={$rate}  where user_id={$id} ";
    $result = db_query($sql);
    if ($result) {
        make_json_result(stripcslashes($rate));
    } else {
        make_json_error("修改出错");
    }
}


function action_edit_mobile_phone()
{
    global $adminpriv;
    // 全局变量
    $user = $GLOBALS['user'];
    $ecs = $GLOBALS['ecs'];
    /* 检查权限 */
    check_authz_json($adminpriv);
    
    $id = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);
    $mobile_phone = empty($_REQUEST['val']) ? '' : json_str_iconv(trim($_REQUEST['val']));
    
    
    $sql = "SELECT user_name FROM " . db_table('users') . " WHERE user_id = '$id'";
    $username = db_getOne($sql);
    
    if (is_mobile_phone($mobile_phone)) {
        $result = db_query("update " . db_table('users') . " set mobile_phone='{$mobile_phone}' where user_id={$id}");
        if ($result) {
            admin_log(addslashes($username), 'edit', 'users');
            make_json_result(stripcslashes($mobile_phone));
        } else {
            make_json_error("修改手机号出错");
        }
    } else {
        make_json_error("请输入正确的手机号");
    }
}

/* ------------------------------------------------------ */

// -- 删除会员帐号
/* ------------------------------------------------------ */
function action_remove()
{
    // 全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    
    /* 检查权限 */
    admin_priv('users_drop');
    /* 如果会员已申请或正在申请入驻商家，不能删除会员 */
    $sql = " SELECT COUNT(*) FROM " . db_table('supplier') . " WHERE user_id='" . $_GET['id'] . "'";
    $issupplier = db_getOne($sql);
    if ($issupplier > 0) {
        /* 提示信息 */
        $link[] = array(
            'text' => $_LANG['go_back'],
            'href' => 'users.php?act=list'
        );
        sys_msg(sprintf('该会员已申请或正在申请入驻商，不能删除！'), 0, $link);
    } else {
        $sql = "SELECT user_name FROM " . db_table('users') . " WHERE user_id = '" . $_GET['id'] . "'";
        $username = db_getOne($sql);
        /* 通过插件来删除用户 */
        $users = & init_users();
        $users->remove_user($username); // 已经删除用户所有数据
        
        /* 记录管理员操作 */
        admin_log(addslashes($username), 'remove', 'users');
        
        /* 提示信息 */
        $link[] = array(
            'text' => $_LANG['go_back'],
            'href' => 'users.php?act=list&stype=' . $stype . ''
        );
        sys_msg(sprintf($_LANG['remove_success'], $username), 0, $link);
    }
}

/* ------------------------------------------------------ */

// -- 收货地址查看
/* ------------------------------------------------------ */
function action_address_list()
{
    // 全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $sql = "SELECT a.*, c.region_name AS country_name, p.region_name AS province, ct.region_name AS city_name, d.region_name AS district_name " . " FROM " . db_table('user_address') . " as a " . " LEFT JOIN " . db_table('region') . " AS c ON c.region_id = a.country " . " LEFT JOIN " . db_table('region') . " AS p ON p.region_id = a.province " . " LEFT JOIN " . db_table('region') . " AS ct ON ct.region_id = a.city " . " LEFT JOIN " . db_table('region') . " AS d ON d.region_id = a.district " . " WHERE a.user_id='$id'";
    $address = db_getAll($sql);
    assign('address', $address);
    assign_query_info();
    assign('ur_here', $_LANG['address_list']);
    assign('action_link', array(
        'text' => $_LANG['03_users_list'],
        'href' => 'users.php?act=list&stype=' . $stype . '&' . list_link_postfix()
    ));
    display('user_address_list.htm');
}

function action_toggle_zuodan()
{
    global $adminpriv;
    check_authz_json($adminpriv);
    
    $id = intval($_POST['id']);
    $val = intval($_POST['val']);
    
    $sql = "UPDATE " . $GLOBALS['ecs']->table("users") . " SET zuodan='" . $val . "' where user_id='" . $id . "'";
    if ($GLOBALS['db']->query($sql)) {
        make_json_result($val);
    } else {
        make_json_error($GLOBALS['db']->error());
    }
}

function str_update1($cat_id, $args)
{
    if (empty($args) || empty($cat_id)) {
        return false;
    }
    
    return $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('users'), $args, 'update', "user_id='$cat_id'");
}

/* ------------------------------------------------------ */

// -- 脱离推荐关系
/* ------------------------------------------------------ */
function action_remove_parent()
{
    // 全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    global $adminpriv;
    
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    
    /* 检查权限 */
    admin_priv($adminpriv);
    
    $sql = "UPDATE " . db_table('users') . " SET parent_id = 0 WHERE user_id = '" . $_GET['id'] . "'";
    db_query($sql);
    
    /* 记录管理员操作 */
    $sql = "SELECT user_name FROM " . db_table('users') . " WHERE user_id = '" . $_GET['id'] . "'";
    $username = db_getOne($sql);
    admin_log(addslashes($username), 'edit', 'users');
    
    /* 提示信息 */
    $link[] = array(
        'text' => $_LANG['go_back'],
        'href' => 'users.php?act=list&stype=' . $stype . ''
    );
    sys_msg(sprintf($_LANG['update_success'], $username), 0, $link);
}

/* ------------------------------------------------------ */

// -- 查看用户推荐会员列表
/* ------------------------------------------------------ */
function action_aff_list()
{
    // 全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    
    global $adminpriv;
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    
    /* 检查权限 */
    admin_priv($adminpriv);
    assign('ur_here', $_LANG['03_users_list']);
    
    $auid = $_GET['auid'];
    $user_list['user_list'] = array();
    
    $affiliate = unserialize($GLOBALS['_CFG']['affiliate']);
    assign('affiliate', $affiliate);
    
    empty($affiliate) && $affiliate = array();
    
    $num = count($affiliate['item']);
    $up_uid = "'$auid'";
    $all_count = 0;
    for ($i = 1; $i <= $num; $i ++) {
        $count = 0;
        if ($up_uid) {
            $sql = "SELECT user_id FROM " . db_table('users') . " WHERE parent_id IN($up_uid)";
            $query = db_query($sql);
            $up_uid = '';
            while ($rt = db_fetch_array($query)) {
                $up_uid .= $up_uid ? ",'$rt[user_id]'" : "'$rt[user_id]'";
                $count ++;
            }
        }
        $all_count += $count;
        
        if ($count) {
            $sql = "SELECT user_id, user_name, '$i' AS level, email, is_validated, user_money, frozen_money, rank_points, pay_points, reg_time " . " FROM " . $GLOBALS['ecs']->table('users') . " WHERE user_id IN($up_uid)" . " ORDER by level, user_id";
            $user_list['user_list'] = array_merge($user_list['user_list'], db_getAll($sql));
        }
    }
    
    $temp_count = count($user_list['user_list']);
    for ($i = 0; $i < $temp_count; $i ++) {
        $user_list['user_list'][$i]['reg_time'] = local_date($_CFG['date_format'], $user_list['user_list'][$i]['reg_time']);
    }
    
    $user_list['record_count'] = $all_count;
    
    assign('user_list', $user_list['user_list']);
    assign('record_count', $user_list['record_count']);
    assign('full_page', 1);
    assign('action_link', array(
        'text' => $_LANG['back_note'],
        'href' => "users.php?act=edit&id=$auid"
    ));
    
    assign_query_info();
    display('affiliate_list.htm');
}

/**
 * 返回用户列表数据
 *
 * @access public
 * @param
 *
 * @return void
 */
function user_list()
{
    $result = get_filter();
    // if ($result === false) {
    /* 过滤条件 */
    $filter['keywords'] = getRequestStr('keywords');
    if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1) {
        $filter['keywords'] = json_str_iconv($filter['keywords']);
    }
   
    // 周:增加用户类型查询条件
    $filter['user_type'] = getRequestInt('user_type');
    $filter['level'] = getRequestInt('level');
    // 地区设置
    $filter['province'] = getRequestInt('province');
    $filter['city'] = getRequestInt('city');
    $filter['district'] = getRequestInt('district');
    
    $filter['sort_by'] = getRequestStrTrim('sort_by', 'user_id');
    $filter['sort_order'] = getRequestStrTrim('sort_order', 'DESC');
    
    $ex_where = ' WHERE 1 ';
    if ($filter['keywords']) {
        $ex_where .= " AND real_name LIKE '%" . mysql_like_quote($filter['keywords']) . "%' or email like  '%" . mysql_like_quote($filter['keywords']) . "%' or mobile_phone like  '%" . mysql_like_quote($filter['keywords']) . "%' ";
    }
   
    if ($filter['user_type'] > 0) {
        $ex_where .= " AND user_type = '$filter[user_type]' ";
    }
    
    if ($filter['level'] > 0) {
        $ex_where .= " AND level = '$filter[level]' ";
    }
    
    // 地区设置 检索
    if ($filter['province'] > 0) {
        $ex_where .= " AND province = '$filter[province]' ";
    }
    if ($filter['city'] > 0) {
        $ex_where .= " AND city = '$filter[city]' ";
    }
    if ($filter['district'] > 0) {
        $ex_where .= " AND district = '$filter[district]' ";
    }
    
    if (isset($_REQUEST["is_status"]) && $_REQUEST["is_status"] != "") {
        $ex_where .= "    and status='" . intval($_REQUEST["is_status"]) . "'";
    }
    
    $filter['record_count'] = $GLOBALS['db']->getOne("SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('users') . $ex_where);
    
    /* 分页大小 */
    $filter = page_and_size($filter);
    
    $sql = "SELECT   *  " . " FROM " . $GLOBALS['ecs']->table('users') . $ex_where . " ORDER by " . $filter['sort_by'] . ' ' . $filter['sort_order'] . " LIMIT " . $filter['start'] . ',' . $filter['page_size'];

    $filter['keywords'] = stripslashes($filter['keywords']);
    set_filter($filter, $sql);
    
    $user_list = $GLOBALS['db']->getAll($sql);
 
    
    $count = count($user_list);
    for ($i = 0; $i < $count; $i ++) {
        $pai_che_state =  $user_list[$i]['pai_che_state'] ;
        if($pai_che_state==0){
            $user_list[$i]['pai_che_state'] = "未排车";
        }
       else if($pai_che_state==1){
            $user_list[$i]['pai_che_state'] = "排车中";
        }
       else{
            $user_list[$i]['pai_che_state'] = "已取车";
        }
        
        $user_list[$i]['reg_time'] = local_date($GLOBALS['_CFG']['date_format'], $user_list[$i]['reg_time']);
        
        $user_list[$i]['is_wx'] = $GLOBALS['db']->getOne("SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table("weixin_user") . " where ecuid='" . $user_list[$i]['user_id'] . "'") * 1;
//         $user_list[$i]['tj_name'] = $GLOBALS['db']->getOne("select mobile_phone FROM " . $GLOBALS['ecs']->table("users") . " where user_id =" . $user_list[$i]['parent_id']);
    }
    $arr = array(
        'user_list' => $user_list,
        'filter' => $filter,
        'page_count' => $filter['page_count'],
        'record_count' => $filter['record_count']
    );
    return $arr;
}

?>
