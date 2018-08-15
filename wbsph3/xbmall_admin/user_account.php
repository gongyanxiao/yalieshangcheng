<?php

/**
 * ECSHOP 会员帐目管理(包括预付款，余额)
 * ============================================================================
 * 版权所有 2005-2011 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: user_account.php 17217 2011-01-19 06:29:08Z liubo $
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');


// if ($_REQUEST['stype']) {
//     $stype = intval($_REQUEST['stype']);
//     if (!in_array($stype, array(1, 2))) {
//         $stype = "0";
//     }
//     unset($_SESSION["user_account_stype"]);
//     $_SESSION["user_account_stype"] = $stype;
// } else {
//     if (!empty($_SESSION['user_account_stype'])) {
//         $stype = $_SESSION["user_account_stype"];
//     } else {
//         $stype = "";
//     }
//     $_SESSION["user_account_stype"]="";
// }
// if ($_REQUEST['atype']) {
//     $atype = intval($_REQUEST['atype']);
//     if (!in_array($stype, array(1))) {
//         $atype = 1;
//     }
//     unset($_SESSION["user_account_atype"]);
//     $_SESSION["user_account_atype"] = $atype;
// } else {
//     unset($_SESSION["user_account_atype"]);
//     $atype = 0;
//     $_SESSION["user_account_atype"] = 0;
// }
// if ($atype == 1) {
//     $adminpriv = "4_scores_exchange_gz";
// } elseif ($atype == 2)
// {
//     $adminpriv = "5_scores_exchange_djtfh";
// }else {
//     if ($stype == 2) {
//         $adminpriv = "2_scores_recharge";
//     } elseif ($stype == 1) {
//         $adminpriv = "3_scores_exchange";
//     } else {
//       //  exit("ACCESS DENIED 404");
//     }
// }

$adminpriv = "3_scores_exchange";
/* act操作项的初始化 */
if (empty($_REQUEST['act'])) {
    $_REQUEST['act'] = 'list';
} else {
    $_REQUEST['act'] = trim($_REQUEST['act']);
}

// die($adminpriv);
/* ------------------------------------------------------ */
//-- 周:这里应该把财务的权限分配放到权限表,后期做
/* ------------------------------------------------------ */
if ($_REQUEST['act'] == 'list') {
	 
		/* 权限判断 */
	 admin_priv('2_scores_recharge_list');
 


    /* 指定会员的ID为查询条件 */
    $user_id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

    /* 获得支付方式列表 */
    $payment = array();
    $sql = "SELECT pay_id, pay_name FROM " . $ecs->table('payment') .
            " WHERE enabled = 1 AND pay_code != 'cod' ORDER BY pay_id";
    $res = $db->query($sql);
    while ($row = $db->fetchRow($res)) {
        $payment[$row['pay_name']] = $row['pay_name'];
    }

    /* 模板赋值 */
    if (isset($_REQUEST['process_type'])) {
        $smarty->assign('process_type_' . intval($_REQUEST['process_type']), 'selected="selected"');
    }
    if (isset($_REQUEST['is_paid'])) {
        $smarty->assign('is_paid_' . intval($_REQUEST['is_paid']), 'selected="selected"');
    }
    $smarty->assign('ur_here', $_LANG[$adminpriv]);
    $smarty->assign('id', $user_id);
    $smarty->assign('payment_list', $payment);
    // $smarty->assign('action_link',   array('text' => $_LANG['surplus_add'], 'href'=>'user_account.php?act=add'));

    $list = account_list_all();
    $smarty->assign('list', $list['list']);
    $smarty->assign('filter', $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count', $list['page_count']);
    $smarty->assign('record_amount', $list['record_amount']);
    $smarty->assign('record_amount1', $list['record_amount1']);
    $smarty->assign('record_count1', $list['record_count1']);

    $smarty->assign('sf_amount', $list['sf_amount']);
    $smarty->assign('tx_amount', $list['tx_amount']);
    $smarty->assign('isOneUser', $list['isOneUser']);
    $smarty->assign('full_page', 1);

    assign_query_info();
    $smarty->display('user_account_list.htm');
} elseif ($_REQUEST['act'] == 'export') {//导出
	$adminpriv = "3_scores_exchange";
    admin_priv($adminpriv);
//    header("Content-type: application/vnd.ms-excel; charset=gbk");
//    header("Content-Disposition: attachment; filename=user_account.xls");
//
//    $export = "<table border='1'><tr><td colspan='2'>会员名称</td><td colspan='2'>操作日期</td><td colspan='2'>类型</td><td colspan='2'>金额</td><td colspan='2'>支付方式</td><td colspan='2'>到款状态</td><td colspan='2'>真实姓名</td><td colspan='2'>银行</td><td colspan='2'>开户行</td><td colspan='2'>银行卡号</td></tr>";
//    $result = account_list();
//    foreach ($result['list'] as $key => $val) {
//        $unconfirm = "未确认";
//        if ($val['is_paid'] * 1 == 1) {
//            $unconfirm = "已完成";
//        }
//        $paymentName = $val['payment'];
//        if (empty($paymentName)) {
//            $paymentName = "N/A";
//        }
//        $bank="".$val['bank_num'];
//        $export .= "<tr><td colspan='2'>" . $val['user_name'] . "</td><td colspan='2'>" . $val['add_date'] . "</td><td colspan='2'>" . $val['process_type_name'] . "</td><td colspan='2'>" . $val['surplus_amount_formatted'] . "</td><td colspan='2'>" . $paymentName . "</td><td colspan='2'>" . $unconfirm . "</td><td colspan='2'>" . $val['real_name'] . "</td><td colspan='2'>" . $val['bank'] . "</td><td colspan='2'>" . $val['bank_kh'] . "</td><td colspan='2'> " . (' '.$bank." ") . "</td></tr>";
//    }
//    $export .= "</table>";
//    if (EC_CHARSET != 'gbk') {
//        echo ecs_iconv(EC_CHARSET, 'gbk', $export) . "\t";
//    } else {
//        echo $export . "\t";
//    }
    // 引入phpexcel核心类文件
    $result = account_list_all();
  
    require_once ROOT_PATH . '/includes/phpexcel/Classes/PHPExcel.php';
    // 实例化excel类
    $objPHPExcel = new PHPExcel();
    // 实例化excel图片处理类
    $objDrawing = new PHPExcel_Worksheet_Drawing();
    // 操作第一个工作表
    $objPHPExcel->setActiveSheetIndex(0);
    // 设置sheet名
    $objPHPExcel->getActiveSheet()->setTitle('充值提现');

    // 表格宽度
//    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(100);
   $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
//    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(100);
//    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(100);
//    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(100);
//    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(100);
//    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(100);
//    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(100);
   $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(30);
   $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
   $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30);
    // 列名表头加粗
    $objPHPExcel->getActiveSheet()->getStyle('A1:J1')->getFont()->setBold(true);
    // 列名赋值
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '会员名称');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '操作日期');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '类型');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '金额');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '手续费');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '实际金额');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', '支付方式');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', '到款状态');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', '真实姓名');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', '银行');
    $objPHPExcel->getActiveSheet()->setCellValue('K1', '开户行');
    $objPHPExcel->getActiveSheet()->setCellValue('L1', '银行卡号');
    $objPHPExcel->getActiveSheet()->setCellValue('M1', '会员类型');
    $objPHPExcel->getActiveSheet()->setCellValue('N1', '会员手机号');
    $objPHPExcel->getActiveSheet()->getStyle('J')->getNumberFormat()
            ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
    // 数据起始行
    $row_num = 2;
    // 向每行单元格插入数据
    foreach ($result['list'] as $key => $value) {
        // 设置单元格高度
        $objPHPExcel->getActiveSheet()->getRowDimension($row_num)->setRowHeight(32);
        // 设置排序列、是否显示列居中显示
        // 设置单元格数值
        $unconfirm = "未确认";
        if ($value['is_paid'] * 1 == 1) {
            $unconfirm = "已完成";
        }
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_num, " " . $value['user_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $row_num, $value['add_date']);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $row_num, $value['process_type_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $row_num, $value['surplus_amount_formatted']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $row_num, $value['fee']);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $row_num, $value['real_amount']);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $row_num, $value['payment']);
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $row_num, $unconfirm);
        $objPHPExcel->getActiveSheet()->setCellValue('I' . $row_num, $value['real_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('J' . $row_num, $value['bank']);
        $objPHPExcel->getActiveSheet()->setCellValue('K' . $row_num, $value['bank_kh']);
        $objPHPExcel->getActiveSheet()->setCellValue('L' . $row_num, " " . $value['bank_num']."", PHPExcel_Cell_DataType::TYPE_STRING);
      
        $bigFamily="普通会员";
        if($value['is_bigfamily']==1){
        	$bigFamily="合伙人";
        }else if($value['is_bigfamily']==2){
        	$bigFamily="代理";
        }
        $objPHPExcel->getActiveSheet()->setCellValue('M' . $row_num, " " . $bigFamily, PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValue('N' . $row_num, $value['mobile_phone']);
        $row_num++;
    }
    $outputFileName = '充值提现_' . time() . '.xls';
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
//-- 添加/编辑会员余额页面
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'add' || $_REQUEST['act'] == 'edit') {
$adminpriv = "3_scores_exchange";
    admin_priv($adminpriv); //权限判断

    $ur_here = ($_REQUEST['act'] == 'add') ? $_LANG['surplus_add'] : $_LANG['surplus_edit'];
    $form_act = ($_REQUEST['act'] == 'add') ? 'insert' : 'update';
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    /* 获得支付方式列表, 不包括“货到付款” */
    $user_account = array();
    $payment = array();
    $sql = "SELECT pay_id, pay_name FROM " . $ecs->table('payment') .
            " WHERE enabled = 1 AND pay_code != 'cod' ORDER BY pay_id";
    $res = $db->query($sql);

    while ($row = $db->fetchRow($res)) {
        $payment[$row['pay_name']] = $row['pay_name'];
    }

    if ($_REQUEST['act'] == 'edit') {
        /* 取得余额信息 */
        $user_account = $db->getRow("SELECT * FROM " . $ecs->table('user_account') . " WHERE id = '$id'");

        // 如果是负数，去掉前面的符号
        $user_account['amount'] = str_replace('-', '', $user_account['amount']);

        /* 取得会员名称 */
        $sql = "SELECT user_name FROM " . $ecs->table('users') . " WHERE user_id = '$user_account[user_id]'";
        $user_name = $db->getOne($sql);
    } else {
        $surplus_type = '';
        $user_name = '';
    }

    /* 模板赋值 */
    $smarty->assign('ur_here', $ur_here);
    $smarty->assign('form_act', $form_act);
    $smarty->assign('payment_list', $payment);
    $smarty->assign('action', $_REQUEST['act']);
    $smarty->assign('user_surplus', $user_account);
    $smarty->assign('user_name', $user_name);
    if ($_REQUEST['act'] == 'add') {
        $href = 'user_account.php?act=list';
    } else {
        $href = 'user_account.php?act=list&' . list_link_postfix();
    }
    $smarty->assign('action_link', array('href' => $href, 'text' => $_LANG['09_user_account']));

    assign_query_info();
    $smarty->display('user_account_info.htm');
}

/* ------------------------------------------------------ */
//-- 添加/编辑会员余额的处理部分
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'insert' || $_REQUEST['act'] == 'update') {
    /* 权限判断 */
$adminpriv = "3_scores_exchange";
    admin_priv($adminpriv);

    /* 初始化变量 */
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $is_paid = !empty($_POST['is_paid']) ? intval($_POST['is_paid']) : 0;
    $amount = !empty($_POST['amount']) ? floatval($_POST['amount']) : 0;
    $process_type = !empty($_POST['process_type']) ? intval($_POST['process_type']) : 0;
    $user_name = !empty($_POST['user_id']) ? trim($_POST['user_id']) : '';
    $admin_note = !empty($_POST['admin_note']) ? trim($_POST['admin_note']) : '';
    $user_note = !empty($_POST['user_note']) ? trim($_POST['user_note']) : '';
    $payment = !empty($_POST['payment']) ? trim($_POST['payment']) : '';

    $user_id = $db->getOne("SELECT user_id FROM " . $ecs->table('users') . " WHERE user_name = '$user_name'");

    /* 此会员是否存在 */
    if ($user_id == 0) {
        $link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
        sys_msg($_LANG['username_not_exist'], 0, $link);
    }

    /* 退款，检查余额是否足够 */
    if ($process_type == 1) {
        $user_account = get_user_surplus($user_id);

        /* 如果扣除的余额多于此会员拥有的余额，提示 */
        if ($amount > $user_account) {
            $link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
            sys_msg($_LANG['surplus_amount_error'], 0, $link);
        }
    }

    if ($_REQUEST['act'] == 'insert') {
        /* 入库的操作 */
        if ($process_type == 1) {
            $amount = (-1) * $amount;
        }
        $sql = "INSERT INTO " . $ecs->table('user_account') .
                " VALUES ('', '$user_id', '$_SESSION[admin_name]', '$amount', '" . gmtime() . "', '" . gmtime() . "', '$admin_note', '$user_note', '$process_type', '$payment', '$is_paid')";
        $db->query($sql);
        $id = $db->insert_id();
    } else {
        /* 更新数据表 */
        $sql = "UPDATE " . $ecs->table('user_account') . " SET " .
                "admin_note   = '$admin_note', " .
                "user_note    = '$user_note', " .
                "payment      = '$payment' " .
                "WHERE id      = '$id'";
        $db->query($sql);
    }

    // 更新会员余额数量
    if ($is_paid == 1) {
        $change_desc = $amount > 0 ? $_LANG['surplus_type_0'] : $_LANG['surplus_type_1'];
        $change_type = $amount > 0 ? ACT_SAVING : ACT_DRAWING;
        log_account_change($user_id, 0, 0, 0, $amount, $change_desc, $change_type);
    }

    //如果是预付款并且未确认，向pay_log插入一条记录
    if ($process_type == 0 && $is_paid == 0) {
        include_once(ROOT_PATH . 'includes/lib_order.php');

        /* 取支付方式信息 */
        $payment_info = array();
        $payment_info = $db->getRow('SELECT * FROM ' . $ecs->table('payment') .
                " WHERE pay_name = '$payment' AND enabled = '1'");
        //计算支付手续费用
        $pay_fee = pay_fee($payment_info['pay_id'], $amount, 0);
        $total_fee = $pay_fee + $amount;

        /* 插入 pay_log */
        $sql = 'INSERT INTO ' . $ecs->table('pay_log') . " (order_id, order_amount, order_type, is_paid)" .
                " VALUES ('$id', '$total_fee', '" . PAY_SURPLUS . "', 0)";
        $db->query($sql);
    }

    /* 记录管理员操作 */
    if ($_REQUEST['act'] == 'update') {
        admin_log($user_name, 'edit', 'user_surplus');
    } else {
        admin_log($user_name, 'add', 'user_surplus');
    }

    /* 提示信息 */
    if ($_REQUEST['act'] == 'insert') {
    	$href = 'user_account.php?act=list&process_type=' . $_REQUEST["process_type"];
    } else {
        $href = 'user_account.php?act=list&' . list_link_postfix() . '&process_type=' . $_REQUEST["process_type"];
    }
    $link[0]['text'] = $_LANG['back_list'];
    $link[0]['href'] = $href;

    $link[1]['text'] = $_LANG['continue_add'];
    $link[1]['href'] = 'user_account.php?act=add';

    sys_msg($_LANG['attradd_succed'], 0, $link);
}

/* ------------------------------------------------------ */
//-- 审核会员余额页面
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'check') {
/* 检查权限 */
     $adminpriv = "3_scores_exchange";
    admin_priv($adminpriv);

    /* 初始化 */
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    /* 如果参数不合法，返回 */
    if ($id == 0) {
        ecs_header("Location: user_account.php?act=list\n");
        exit;
    }

    /* 查询当前的预付款信息 */
    $account = array();
    $account = $db->getRow("SELECT * FROM " . $ecs->table('user_account') . " WHERE id = '$id'");
    $account['add_time'] = local_date($_CFG['time_format'], $account['add_time']);

    //余额类型:预付款，退款申请，购买商品，取消订单
    if ($account['process_type'] == 0) {
        $process_type = $_LANG['surplus_type_0'];
    } elseif ($account['process_type'] == 1) {
        $process_type = $_LANG['surplus_type_1'];
    } elseif ($account['process_type'] == 2) {
        $process_type = $_LANG['surplus_type_2'];
    } else {
        $process_type = $_LANG['surplus_type_3'];
    }

    $sql = "SELECT user_name FROM " . $ecs->table('users') . " WHERE user_id = '$account[user_id]'";
    $user_name = $db->getOne($sql);
    
    /* 模板赋值 */
    $smarty->assign('ur_here', $_LANG['check']);
    $account['user_note'] = htmlspecialchars($account['user_note']);
    $smarty->assign('surplus', $account);
    $smarty->assign('process_type', $process_type);
    $smarty->assign('user_name', $user_name);
    $smarty->assign('id', $id);
    $smarty->assign('action_link', array('text' => $_LANG['09_user_account'],
    		'href' => 'user_account.php?act=list&process_type=' . $_GET["process_type"] . '&' . list_link_postfix()));

    /* 页面显示 */
    assign_query_info();
    $smarty->display('user_account_check.htm');
}

/* ------------------------------------------------------ */
//-- 更新会员余额的状态
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'action') {
  
/* 检查权限 */
    admin_priv("3_scores_exchange");//有积分提现权限就可以操作

    /* 初始化 */
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $is_paid = isset($_POST['is_paid']) ? intval($_POST['is_paid']) : 0;
    $admin_note = isset($_POST['admin_note']) ? trim($_POST['admin_note']) : '';

    /* 如果参数不合法，返回 */
    if ($id == 0 || empty($admin_note)) {
        ecs_header("Location: user_account.php?act=list\n");
        exit;
    }

    /* 查询当前的预付款信息 */
    $account = array();
    $account = $db->getRow("SELECT * FROM " . $ecs->table('user_account') . " WHERE id = '$id'");
    $amount = $account['amount'];
   
    //如果状态为未确认
    if ($account['is_paid'] == 0) {
        //如果是退款申请, 并且已完成,更新此条记录,扣除相应的余额
        if ($is_paid == '1' && $account['process_type'] == '1') {//提现完成
            $user_account = get_user_points($account['user_id']);
            $fmt_amount = str_replace('-', '', $amount);

            //如果扣除的余额多于此会员拥有的余额，提示
            if ($fmt_amount > $user_account) {
                //$link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
                //sys_msg($_LANG['surplus_amount_error'], 0, $link);
            }

            update_user_account($id, $amount, $admin_note, $is_paid);

            if ($account['type'] * 1 == 0) {
                //更新会员余额数量
                log_account_change($account['user_id'], 0, 0, 0, 0, $_LANG['surplus_type_1'], ACT_DRAWING, 0, 0, $fmt_amount, 0, 0, 0, 0, 0, $amount, 0, 0);
            }
            
          
           
            //是否开启余额变动给客户发短信 -提现
            if ($_CFG['sms_user_money_change'] == 1) {
                $sql = "SELECT user_money,mobile_phone FROM " . $GLOBALS['ecs']->table('users') . " WHERE user_id = '" . $account['user_id'] . "'";
                $users = $GLOBALS['db']->getRow($sql);
                $content = sprintf($_CFG['sms_deposit_balance_reduce_tpl'], date("Y-m-d H:i:s", gmtime()), $amount, $users['user_money'], $_CFG['sms_sign']);
                if ($users['mobile_phone']) {
                    include_once('../send.php');
                    sendSMS($users['mobile_phone'], $content);
                }
            }

            $txData = array("add_time" => gmtime(), "user_id" => $account['user_id'], "content" => "您的积分提现已经处理，请及时查看，提现积分数:" . $amount . ",该消息只用于消息通知，不作为最后到账的依据。");
            $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('user_message'), $txData);

            pushUserMsg($account['user_id'], array('order_time' => local_date("Y-m-d G:i:s", $account['add_time']), 'amount' => abs($amount)), 3);
            @file_get_contents("http://" . $_SERVER['HTTP_HOST'] . '/mobile/weixin/auto_do.php?type=3');
        } elseif ($is_paid == '1' && $account['process_type'] == '0') {//处理充值
            //如果是预付款，并且已完成, 更新此条记录，增加相应的余额
            update_user_account($id, $amount, $admin_note, $is_paid);

            //更新会员余额数量
            log_account_change($account['user_id'], $amount, 0, 0, 0, $_LANG['surplus_type_0'], ACT_SAVING);

            //查询supplier_id
            $sql_su = "SELECT user_id,supplier_id FROM " . $GLOBALS['ecs']->table('supplier_admin_user') . " WHERE `uid` = '" . $account['user_id'] . "' LIMIT 1";
            $sql_su_data = $GLOBALS['db']->GetRow($sql_su);
            //supplier_rebat 添加log
            $data_i = array(
                'order_id' => $account['id'],
                'supplier_id' => $account['user_id'],
                'all_money' => $amount,
                'rebate_money' => 0,
                'result_money' => 0,
                'add_time' => $account['add_time'],
                'is_send' => 0,
                'is_offline' => 3,
                'pay_name' => $account['payment'],
                'texts' => '余额积分充值',
                'real_supplier_id' => $sql_su_data['supplier_id'],
                'member_id' => $sql_su_data['user_id'],
            );
            $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('supplier_rebate_log'), $data_i, 'INSERT');

            pushUserMsg($account['user_id'], array('order_no' => $id, 'order_amt' => $amount), 8);
            //$_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : "/admin/";
            //$autoUrl = str_replace($_SERVER['REQUEST_URI'], '', $GLOBALS['ecs']->url());
            @file_get_contents("http://" . $_SERVER['HTTP_HOST'] . '/mobile/weixin/auto_do.php?type=8');
        } elseif ($is_paid == '0') {
            /* 否则更新信息 */
            $sql = "UPDATE " . $ecs->table('user_account') . " SET " .
                    "admin_user    = '$_SESSION[admin_name]', " .
                    "admin_note    = '$admin_note', " .
                    "is_paid       = 0 WHERE id = '$id'";
            $db->query($sql);
            
            if ($account['type'] * 1 == 5) {//产品积分取消
            	$sql="delete from  ". $ecs->table('user_account')." where id=".$id;
            	$db->query($sql);
            	//取消提现id
            	$sql="update    ". $ecs->table('product_points_recharge')." set ti_xian_acount_log_id=null  where ti_xian_acount_log_id=".$id;
            	$db->query($sql);
            	
            }
            
            
        }

        /* 记录管理员日志 */
        admin_log('(' . addslashes($_LANG['check']) . ')' . $admin_note, 'edit', 'user_surplus');

        /* 提示信息 */
        $link[0]['text'] = $_LANG['back_list'];
        $link[0]['href'] = 'user_account.php?type=-1&act=list&process_type=' . $account['process_type'];
//         $link[0]['href'] = 'user_account.php?act=list&process_type=' . $account['process_type']. '&' . list_link_postfix();
        sys_msg($_LANG['attradd_succed'], 0, $link);
    }
}

/* ------------------------------------------------------ */
//-- ajax帐户信息列表
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'query') {
$adminpriv = "3_scores_exchange_list";
    $list = account_list_all();
    $smarty->assign('list', $list['list']);
    $smarty->assign('filter', $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count', $list['page_count']);
    $smarty->assign('record_amount', $list['record_amount']);
    $smarty->assign('record_count1', $list['record_count1']);
    $smarty->assign('record_amount1', $list['record_amount1']);
    $smarty->assign('sf_amount', $list['sf_amount']);
    $smarty->assign('tx_amount', $list['tx_amount']);
    $smarty->assign('isOneUser', $list['isOneUser']);

    $sort_flag = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('user_account_list.htm'), '', array('filter' => $list['filter'], 'page_count' => $list['page_count'], 'record_amount' => $list['record_amount'], 'record_count1' => $list['record_count1'], 'record_amount1' => $list['record_amount']));
}
/* ------------------------------------------------------ */
//-- ajax删除一条信息
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'remove') {
$adminpriv = "3_scores_exchange";
    /* 检查权限 */
    check_authz_json($adminpriv);
    $id = @intval($_REQUEST['id']);
    $sql = "SELECT u.mobile_phone FROM " . $ecs->table('users') . " AS u, " .
            $ecs->table('user_account') . " AS ua " .
            " WHERE u.user_id = ua.user_id AND ua.id = '$id' ";
    $user_name = $db->getOne($sql);
    $GLOBALS['db']->startTrans();
    $account_order = $GLOBALS['db']->getRow('SELECT *  FROM ' . $GLOBALS['ecs']->table('user_account') .
            " WHERE id = '$id' ");
    $sql = "DELETE FROM " . $ecs->table('user_account') . " WHERE id = '$id'";
    if ( $db->query($sql, 'SILENT')) {
        $amount = $account_order['amount'];
        if ($account_order['process_type'] * 1 == 0) {
            $ress = true;
        } else {//取消提现
        	 
         
            //$user_id, $user_money = 0, $frozen_money = 0, $rank_points = 0, $pay_points = 0, $change_desc = '', $change_type = ACT_OTHER, $consum_money = 0, $give_points = 0,
            // $tx_points = 0, $history_zsq = 0, $zsq = 0, $frozen_points = 0, $day_points = 0, $hk_points = 0, $frozen_hk_points = 0, $yl_points = 0, $jljs_points = 0
            if ($account_order['type'] * 1 == 0) {
            	$ress = log_account_change($account_order['user_id'], 0, 0, 0, 0, "后台取消积分提现，货款积分增加", ACT_DRAWING_HK, 0, 0, 0, 0, 0, 0, 0, abs($amount), $amount, 0, 0);
            }elseif ($account_order['type'] * 1 == 5) {//产品积分提现
            	$sql = "update  ".$ecs->table("users")." set cp_points=cp_points-".$amount."  where user_id=".$account_order['user_id'];//负负得正
            	//只记录日期
            	log_account_change($account_order['user_id'], 0, 0, 0, 0, "后台取消产品积分提现，产品积分增加".abs($amount), ACT_DRAWING_CHAN_PIN_JI_FEN, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            	$GLOBALS['db']->query($sql);
            	$ress = true;
            }
            elseif ($account_order['type'] * 1  == 2 )
            {
            	$ress = log_account_change($account_order['user_id'],0,0,0,0,"后台取消提现，大家庭分红增加", ACT_DRAWING_FEN_HONG,0,0,0,0,0,0,0,0,0,0,0,0,abs($amount),0);
            }
            elseif ($account_order['type'] * 1  == 3 )
            {
            	$ress = log_account_change($account_order['user_id'],0,0,0,abs($amount),"后台取消消费分红积分提现，消费分红积分增加", ACT_DRAWING_FEN_HONG_JI_FEN,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
            }
            elseif ($account_order['type'] * 1  == ACT_DRAWING_FEN_XIANG_JI_FEN)
            {
            	 $ress =fen_xiang_jiang_li_ji_fen_log($account_order["user_id"], "后台取消分享奖励积分兑换",  abs($amount));//退给用户分享奖励积分
//             	$ress =log_account_change($account_order['user_id'],0,0,0,abs($amount),"后台取消消费分红积分提现，消费分红积分增加", ACT_DRAWING_FEN_XIANG_JI_FEN,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
            }
            else
            {
            	$ress = log_account_change($account_order['user_id'], 0, 0, 0, 0, "后台取消提现，基数奖励增加", ACT_DRAWING_GZ, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, abs($amount));
            }
            
          
            if ($account_order['type'] * 1  == 10 ){//扫码货款积分驳回
            	$sql="update ".$GLOBALS['ecs']->table('users')." set hk_xf_points=hk_xf_points+".abs($amount)." where user_id=".$account_order['user_id'];
            	$ress = $GLOBALS['db']->query($sql);
            }
        }
        if ($ress) {
            admin_log(addslashes($user_name), 'remove', 'user_surplus');
            $GLOBALS['db']->commitTrans();
            $url = 'user_account.php?act=query&stype=1&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
            ecs_header("Location: $url\n");
            exit();
        }
    }
    $GLOBALS['db']->rollbackTrans();
    make_json_error($db->error());
}

/* ------------------------------------------------------ */
//-- 会员余额函数部分
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == "batch") {
$adminpriv = "3_scores_exchange";
    $type = $_REQUEST['type'];
    if (in_array($type, array("todo", "cancel"))) {
        $checkboxes = $_REQUEST["checkboxes"];
        if (!isset($_REQUEST['checkboxes']) || empty($_REQUEST['checkboxes'])) {
            echo "参数错误";
            exit;
        } else {
            $checkboxes = $_REQUEST['checkboxes'];
            if (count($checkboxes) == 0) {
                echo "参数错误";
                exit;
            } else {
                $isok = true;
                $GLOBALS['db']->startTrans();
                $isSend = false;
                try {
                    foreach ($checkboxes as $idKey => $idValue) {
                        if ($type == "todo") {
                            $is_paid = "1";
                        } elseif ($type == "cancel") {
                            $is_paid = "0";
                        }
                        /* 查询当前的预付款信息 */
                        $account = array();
                        $account = $db->getRow("SELECT * FROM " . $ecs->table('user_account') . " WHERE id = '$idValue'");
                        if (!$account) {
                            continue;
                        }
                        $id = $idValue;
                        $amount = $account['amount'];
                        //如果状态为未确认
                        if ($account['is_paid'] == 0) {
                            //如果是退款申请, 并且已完成,更新此条记录,扣除相应的余额
                            if ($is_paid == '1' && $account['process_type'] == '1') {
                                $user_account = get_user_points($account['user_id']);
                                $fmt_amount = str_replace('-', '', $amount);

                                //如果扣除的余额多于此会员拥有的余额，提示
                                if ($fmt_amount > $user_account) {
                                    //$link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
                                    //sys_msg($_LANG['surplus_amount_error'], 0, $link);
                                }

                                update_user_account($id, $amount, $admin_note, $is_paid);

                                //更新会员余额数量
                                //$user_id, $user_money = 0, $frozen_money = 0, $rank_points = 0, $pay_points = 0, $change_desc = '', $change_type = ACT_OTHER, $consum_money = 0, $give_points = 0, $tx_points = 0, $history_zsq = 0, $zsq = 0, $frozen_points = 0, $day_points = 0, $hk_points = 0, $frozen_hk_points = 0, $yl_points = 0, $jljs_points = 0
                                if ($account['type'] * 1 == 0) {
                                    log_account_change($account['user_id'], 0, 0, 0, 0, $_LANG['surplus_type_1'], ACT_DRAWING, 0, 0, $fmt_amount, 0, 0, 0, 0, 0, $amount, 0, 0);
                                }
                                //是否开启余额变动给客户发短信 -提现
//                                if ($_CFG['sms_user_money_change'] == 1) {
//                                    $sql = "SELECT user_money,mobile_phone FROM " . $GLOBALS['ecs']->table('users') . " WHERE user_id = '" . $account['user_id'] . "'";
//                                    $users = $GLOBALS['db']->getRow($sql);
//                                    $content = sprintf($_CFG['sms_deposit_balance_reduce_tpl'], date("Y-m-d H:i:s", gmtime()), $amount, $users['user_money'], $_CFG['sms_sign']);
//                                    if ($users['mobile_phone']) {
//                                        include_once('../send.php');
//                                        sendSMS($users['mobile_phone'], $content);
//                                    }
//                                }
                                $txData = array("add_time" => gmtime(), "user_id" => $account['user_id'], "content" => "您的积分提现已经处理，请及时查看，提现积分数:" . $amount . ",该消息只用于消息通知，不作为最后到账的依据。");
                                $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('user_message'), $txData);
                                $isSend = true;
                                pushUserMsg($account['user_id'], array('order_time' => local_date("Y-m-d G:i:s", $account['add_time']), 'amount' => abs($amount)), 3);
                            } elseif ($is_paid == '1' && $account['process_type'] == '0') {
                                //如果是预付款，并且已完成, 更新此条记录，增加相应的余额
                                update_user_account($id, $amount, $admin_note, $is_paid);
                                //更新会员余额数量
                                log_account_change($account['user_id'], $amount, 0, 0, 0, $_LANG['surplus_type_0'], ACT_SAVING);

                                //查询supplier_id
                                $sql_su = "SELECT user_id,supplier_id FROM " . $GLOBALS['ecs']->table('supplier_admin_user') . " WHERE `uid` = '" . $account['user_id'] . "' LIMIT 1";
                                $sql_su_data = $GLOBALS['db']->GetRow($sql_su);
                                //supplier_rebat 添加log
                                $data_i = array(
                                    'order_id' => $account['id'],
                                    'supplier_id' => $account['user_id'],
                                    'all_money' => $amount,
                                    'rebate_money' => 0,
                                    'result_money' => 0,
                                    'add_time' => $account['add_time'],
                                    'is_send' => 0,
                                    'is_offline' => 3,
                                    'pay_name' => $account['payment'],
                                    'texts' => '余额积分充值',
                                    'real_supplier_id' => $sql_su_data['supplier_id'],
                                    'member_id' => $sql_su_data['user_id'],
                                );
                                $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('supplier_rebate_log'), $data_i, 'INSERT');

                                pushUserMsg($account['user_id'], array('order_no' => $id, 'order_amt' => $amount), 8);
                                //$_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : "/admin/";
                                //$autoUrl = str_replace($_SERVER['REQUEST_URI'], '', $GLOBALS['ecs']->url());
                                @file_get_contents("http://" . $_SERVER['HTTP_HOST'] . '/mobile/weixin/auto_do.php?type=8');
                            } elseif ($is_paid == '0') {
                                $sql = "SELECT u.mobile_phone FROM " . $ecs->table('users') . " AS u, " .
                                        $ecs->table('user_account') . " AS ua " .
                                        " WHERE u.user_id = ua.user_id AND ua.id = '$idValue' ";
                                $user_name = $db->getOne($sql);
                                $GLOBALS['db']->startTrans();
                                $account_order = $GLOBALS['db']->getRow('SELECT *  FROM ' . $GLOBALS['ecs']->table('user_account') .
                                        " WHERE id = '$id' ");
                                $sql = "DELETE FROM " . $ecs->table('user_account') . " WHERE id = '$id'";
                                if ($db->query($sql, 'SILENT')) {
                                    $amount = $account_order['amount'];
                                    //$user_id, $user_money = 0, $frozen_money = 0, $rank_points = 0, $pay_points = 0, $change_desc = '', $change_type = ACT_OTHER, $consum_money = 0, $give_points = 0, $tx_points = 0, $history_zsq = 0, $zsq = 0, $frozen_points = 0
                                    if ($account_order['process_type'] * 1 == 0) {
                                        $ress = true;
                                    } else {
                                        if ($account_order['type'] * 1 == 0) {
                                            $ress = log_account_change($account_order['user_id'], 0, 0, 0, 0, "后台取消货款积分提现，货款积分增加", 1, 0, 0, 0, 0, 0, 0, 0, abs($amount), $amount, 0, 0);
                                        } else {
                                            if ($account_order['type'] * 1  == 2 )
                                            {
                                                $ress = log_account_change($account_order['user_id'],0,0,0,0,"后台取消大家庭分红提现，大家庭分红增加", 1 ,0,0,0,0,0,0,0,0,0,0,0,0,abs($amount),0);
                                            }
                                            else 
                                            {
                                                $ress = log_account_change($account_order['user_id'], 0, 0, 0, 0, "后台取消基数奖励提现，基数奖励增加", 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, abs($amount));
                                            }
                                        }
                                    }
                                    if ($ress) {
                                        admin_log(addslashes($user_name) . "删除操作记录" . $id, 'remove', 'user_surplus');
                                    } else {
                                        $ress = false;
                                        break;
                                    }
                                }
                            }
                            /* 记录管理员日志 */
                            admin_log('(' . addslashes($_LANG['check']) . ')' . $admin_note . "操作记录：" . $id, 'edit', 'user_surplus');
                        }
                    }
                } catch (Exception $ex) {
                    $isok = false;
                    $GLOBALS['db']->rollbackTrans();
                }
                if ($isok) {
                    $GLOBALS['db']->commitTrans();
                    /* 提示信息 */
                    $link[0]['text'] = $_LANG['back_list'];
                    $link[0]['href'] = 'user_account.php?act=list&process_type=' . $_REQUEST["process_type"] . '&' . list_link_postfix();
                    if ($isSend == true) {
                        @file_get_contents("http://" . $_SERVER['HTTP_HOST'] . '/mobile/weixin/auto_do.php?type=3');
                    }
                    sys_msg($_LANG['attradd_succed'], 0, $link);
                } else {
                    $GLOBALS['db']->rollbackTrans();
                }
            }
        }
    } else {
        echo "请求被禁止";
    }
    //echo "aaa";exit;
}

/**
 * 查询会员余额的数量
 * @access  public
 * @param   int     $user_id        会员ID
 * @return  int
 */
function get_user_surplus($user_id) {
    $sql = "SELECT SUM(user_money) FROM " . $GLOBALS['ecs']->table('account_log') .
            " WHERE user_id = '$user_id'";

    return $GLOBALS['db']->getOne($sql);
}

/* ------------------------------------------------------ */
//-- 会员余额函数部分
/* ------------------------------------------------------ */

/**
 * 查询会员余额的数量
 * @access  public
 * @param   int     $user_id        会员ID
 * @return  int
 */
function get_user_points($user_id) {
    $sql = "SELECT SUM(pay_points) FROM " . $GLOBALS['ecs']->table('account_log') .
            " WHERE user_id = '$user_id'";

    return $GLOBALS['db']->getOne($sql);
}

/**
 * 更新会员账目明细
 *
 * @access  public
 * @param   array     $id          帐目ID
 * @param   array     $admin_note  管理员描述
 * @param   array     $amount      操作的金额
 * @param   array     $is_paid     是否已完成
 *
 * @return  int
 */
function update_user_account($id, $amount, $admin_note, $is_paid) {
    $sql = "UPDATE " . $GLOBALS['ecs']->table('user_account') . " SET " .
            "admin_user  = '$_SESSION[admin_name]', " .
            "amount      = '$amount', " .
            "paid_time   = '" . gmtime() . "', " .
            "admin_note  = '$admin_note', " .
            "is_paid     = '$is_paid' WHERE id = '$id'";
    return $GLOBALS['db']->query($sql);
}



function account_list_all() {
	 
 
	$result = get_filter();
	//if ($result === false) {
	/* 过滤列表 */
	$filter['user_id'] = !empty($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
	$filter['keywords'] = empty($_REQUEST['keywords']) ? '' : trim($_REQUEST['keywords']);
	if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1) {
		$filter['keywords'] = json_str_iconv($filter['keywords']);
	}
	
	$filter['process_type'] = isset($_REQUEST['process_type']) ? intval($_REQUEST['process_type']) : -1;
	 
	$filter['payment'] = empty($_REQUEST['payment']) ? '' : trim($_REQUEST['payment']);
	$filter['is_paid'] = isset($_REQUEST['is_paid']) ? intval($_REQUEST['is_paid']) : -1;
	$filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'add_time' : trim($_REQUEST['sort_by']);
	$filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
	$filter['start_date'] = empty($_REQUEST['start_time']) ? '' : local_strtotime($_REQUEST['start_time']);
	$filter['end_date'] = empty($_REQUEST['end_time']) ? '' : (local_strtotime($_REQUEST['end_time']) + 86400);
	
	$where = " WHERE 1 ";
	$filter['type'] = $_REQUEST['ti_xian_type'];
	if($filter['type']===null){//如果不存在
		if($_REQUEST['type']!==null){
			$filter['type']=$_REQUEST['type'];
		}else{
			$filter['type']= -1;
		}
	}
 
	if ($filter['type']!=-1 ) {//充值提现类型(大家庭/货款/工资)
		$where.=" AND ua.type='" . intval($filter['type']) . "'";
	}  

	if ($filter['user_id'] > 0) {
		$where .= " AND ua.user_id = '$filter[user_id]' ";
	}
	
	if ($filter['payment']) {
		$where .= " AND ua.payment = '$filter[payment]' ";
	}
	
	if($_SESSION['admin_name']=='cztx'){//充值提现角色, 只能看9月份以后的数据
		$where .= " AND ua.add_time>= ".local_strtotime("2017-09-30 23:59:59");
	}
	
	
	 
	$isOneUser = false;
	$sf_amount = 0;
	$tx_amount = 0;
	if ($filter['keywords']) {
		$where .= " AND (u.user_name LIKE '%" . mysql_like_quote($filter['keywords']) . "%' OR  u.real_name LIKE '%" . mysql_like_quote($filter['keywords']) . "%'  OR  u.mobile_phone LIKE '%" . mysql_like_quote($filter['keywords']) . "%')";
		
		
		$sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('user_account') . " AS ua, " .
				$GLOBALS['ecs']->table('users') . " AS u " . $where;
				$sql1 = "select consum_money,tx_points from " . $GLOBALS['ecs']->table("users") . " where user_name like '%" . mysql_like_quote($filter['keywords']) . "%' OR  real_name LIKE '%" . mysql_like_quote($filter['keywords']) . "%'";
				$count11 = $GLOBALS['db']->getAll($sql1);
				if (count($count11) * 1 == 1) {
					$isOneUser = true;
					$sf_amount = $count11[0]['consum_money'];
					$tx_amount = $count11[0]['tx_points'];
				}
	}
	
	/* 　时间过滤　 */
	if (!empty($filter['start_date'])) {
		$where .= "AND ua.add_time >= " . $filter['start_date'] . " ";
	}
	if (!empty($filter['end_date'])) {
		$where .= " AND ua.add_time < '" . $filter['end_date'] . "'";
	}
	if ($filter['is_paid'] != -1) {
		$where .= " AND ua.is_paid = '$filter[is_paid]' ";
	}
	
	
	
	
	
	$sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('user_account') . " AS ua LEFT JOIN " .
			$GLOBALS['ecs']->table('users') . " AS u ON ua.user_id = u.user_id " . $where . " and ua.process_type=1"; //提现
		
			$filter['record_count1'] = $GLOBALS['db']->getOne($sql);
			
			$sql = "SELECT ifnull(sum(abs(amount)),0) FROM " . $GLOBALS['ecs']->table('user_account') . " AS ua  LEFT JOIN " .
			$GLOBALS['ecs']->table('users') . " AS u ON ua.user_id = u.user_id ".  $where." and ua.process_type=1 " ;
			$filter['record_amount'] = $GLOBALS['db']->getOne($sql);
			
		
			
			$sql = "SELECT ifnull(sum(abs(amount)),0) FROM " . $GLOBALS['ecs']->table('user_account') . " AS ua LEFT JOIN " .
			$GLOBALS['ecs']->table('users') . " AS u ON ua.user_id = u.user_id ". $where . "  and ua.process_type=0 ";
		 
			$filter['record_amount1'] = $GLOBALS['db']->getOne($sql);//充值总金额
					
			
			
			if ($filter['process_type'] != -1) {
				
				$where .= " AND ua.process_type = '$filter[process_type]' ";
			} else {
				$where .= " AND ua.process_type " . db_create_in(array(SURPLUS_SAVE, SURPLUS_RETURN));
			}
			
				 
				$sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('user_account') . " AS ua LEFT JOIN " .
				$GLOBALS['ecs']->table('users') . " AS u ON ua.user_id = u.user_id " . $where;
				 
				$filter['record_count'] = $GLOBALS['db']->getOne($sql);
				
				/* 分页大小 */
				$filter = page_and_size($filter);
				if ($_REQUEST["act"] == "export") {
					/* 查询数据 */
					$sql = 'SELECT ua.*, u.is_bigfamily,u.mobile_phone, u.user_name,u.real_name,u.bank,u.bank_kh,u.bank_num,u.pay_points,u.user_money,u.consum_money,u.tx_points,ua.is_paid FROM ' .
							$GLOBALS['ecs']->table('user_account') . ' AS ua LEFT JOIN ' .
							$GLOBALS['ecs']->table('users') . ' AS u ON ua.user_id = u.user_id' .
							$where . "ORDER by " . $filter['sort_by'] . " " . $filter['sort_order'];
				} else {
					/* 查询数据 */
					$sql = 'SELECT ua.*, u.is_bigfamily,u.mobile_phone, u.user_name,u.real_name,u.bank,u.bank_kh,u.bank_num,u.pay_points,u.user_money,u.consum_money,u.tx_points,ua.is_paid FROM ' .
							$GLOBALS['ecs']->table('user_account') . ' AS ua LEFT JOIN ' .
							$GLOBALS['ecs']->table('users') . ' AS u ON ua.user_id = u.user_id' .
							$where . "ORDER by " . $filter['sort_by'] . " " . $filter['sort_order'] . " LIMIT " . $filter['start'] . ", " . $filter['page_size'];
				}
				
			$filter['keywords'] = stripslashes($filter['keywords']);
			set_filter($filter, $sql);
			 
		 
			file_put_contents ( "c:/test.txt", "post:".$sql. "\r\n", FILE_APPEND );    
			$list = $GLOBALS['db']->getAll($sql);
			if ($isOneUser == true) {
				
			}
			
			foreach ($list AS $key => $value) {
				$list[$key]['process_type'] = $value['process_type'];
				$list[$key]['surplus_amount'] = price_format(abs($value['amount']), false);
				$list[$key]['surplus_amount_formatted'] = (abs($value['amount']));
				$list[$key]['fee'] = price_format(abs($value['fee']), false);
				$list[$key]['real_amount'] = price_format(abs($value['real_amount']), false);
				$list[$key]['add_date'] = local_date($GLOBALS['_CFG']['time_format'], $value['add_time']);
				if($value['process_type']==1){//提现
					if ($value['type'] == 0)
					{
						$list[$key]['process_type_name'] ="货款积分提现";
					}else if ($value['type'] == 1)
					{
						$list[$key]['process_type_name'] = "基数奖励提现";
					}else if ($value['type'] == 2)
					{
						$list[$key]['process_type_name'] = "大家庭分红积分提现";
					}else if ($value['type'] == 3)
					{
						$list[$key]['process_type_name'] = "消费分红积分提现";
					}else if ($value['type'] == 5)
					{
						$list[$key]['process_type_name'] = "产品积分提现";
					}else if ($value['type'] == 9)
					{
						$list[$key]['process_type_name'] = "分享奖励积分提现";
					}else if ($value['type'] == 10)
					{
						$list[$key]['process_type_name'] = "扫码货款积分提现";
					}
					 
				}else{//充值
					if ($value['type'] == 3)
					{
						$list[$key]['process_type_name'] = "大家庭充值";
					}elseif ($value['type'] ==5){
						$list[$key]['process_type_name'] = "产品积分充值";
					}elseif ($value['type'] ==0){
						$list[$key]['process_type_name'] = "做单充值";
					} 
					 
				}
				
			}
			$arr = array('list' => $list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count'], 'record_count1' => $filter['record_count1'], 'record_amount' => $filter['record_amount'], 'record_amount1' => $filter['record_amount1'], 'sf_amount' => $sf_amount, 'tx_amount' => $tx_amount, 'isOneUser' => ($isOneUser ? 1 : 0));
			
			return $arr;
									
}


function  chan_pin_ti_xian(){
	
	
	global  $db,$ecs;
	/* 初始化 */
	$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
	$is_paid = isset($_POST['is_paid']) ? intval($_POST['is_paid']) : 0;
	$admin_note = isset($_POST['admin_note']) ? trim($_POST['admin_note']) : '';
	
	/* 如果参数不合法，返回 */
	if ($id == 0 || empty($admin_note)) {
		ecs_header("Location: user_account.php?act=list\n");
		exit;
	}
	
	/* 查询当前的预付款信息 */
	$account = array();
	$sql="SELECT * FROM " . $ecs->table('user_account') . " WHERE id = '$id'";
	$account = $db->getRow($sql);
 
	$amount = $account['amount'];
	var_dump($account);
	die;
	//如果状态为未确认
	if ($account['is_paid'] == 0) {
		//如果是退款申请, 并且已完成,更新此条记录,扣除相应的余额
		if ($is_paid == '1' && $account['process_type'] == '1') {//完成提现操作
			$user_account = get_user_points($account['user_id']);
			$fmt_amount = str_replace('-', '', $amount);
			update_user_account($id, $amount, $admin_note, $is_paid);//标记为已提现
			
			if ($account['type'] * 1 ==5) {//产品积分提现
				//更新会员余额数量
				log_account_change($account['user_id'], 0, 0, 0, 0, $_LANG['surplus_type_1'], ACT_DRAWING, 0, 0, $fmt_amount, 0, 0, 0, 0, 0, $amount, 0, 0);
			}
			$txData = array("add_time" => gmtime(), "user_id" => $account['user_id'], "content" => "您的产品积分提现已经处理，请及时查看，提现积分数:" . $amount . ",该消息只用于消息通知，不作为最后到账的依据。");
			$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('user_message'), $txData);
			
			pushUserMsg($account['user_id'], array('order_time' => local_date("Y-m-d G:i:s", $account['add_time']), 'amount' => abs($amount)), 3);
		
		}else{//取消提现, 
			
		}
		
		/* 记录管理员日志 */
		admin_log('(' . addslashes($_LANG['check']) . ')' . $admin_note, 'edit', '处理产品积分提现');
		
		/* 提示信息 */
		$link[0]['text'] = $_LANG['back_list'];
		$link[0]['href'] = 'user_account.php?type=-1&act=list&process_type=' . $account['process_type'];
		//         $link[0]['href'] = 'user_account.php?act=list&process_type=' . $account['process_type']. '&' . list_link_postfix();
		sys_msg($_LANG['attradd_succed'], 0, $link);
	}
	
}
/**
 *
 *
 * @access  public
 * @param
 *
 * @return void
 */
function account_list() { 
    $result = get_filter();
    //if ($result === false) {
    /* 过滤列表 */
    $filter['user_id'] = !empty($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
    $filter['keywords'] = empty($_REQUEST['keywords']) ? '' : trim($_REQUEST['keywords']);
    if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1) {
        $filter['keywords'] = json_str_iconv($filter['keywords']);
    }

    $filter['process_type'] = isset($_REQUEST['process_type']) ? intval($_REQUEST['process_type']) : -1;
    if (!empty($_REQUEST['stype'])) {
        if ($_REQUEST["stype"] * 1 == 2) {
            $filter['process_type'] = 0;
        } else {
            $filter['process_type'] = 1;
        }
    }
    $filter['payment'] = empty($_REQUEST['payment']) ? '' : trim($_REQUEST['payment']);
    $filter['is_paid'] = isset($_REQUEST['is_paid']) ? intval($_REQUEST['is_paid']) : -1;
    $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'add_time' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
    $filter['start_date'] = empty($_REQUEST['start_time']) ? '' : local_strtotime($_REQUEST['start_time']);
    $filter['end_date'] = empty($_REQUEST['end_time']) ? '' : (local_strtotime($_REQUEST['end_time']) + 86400);

    $where = " WHERE 1 ";
    $filter['stype'] = $_REQUEST['stype'];
    if (!empty($_SESSION['user_account_atype'])) {
        $where.=" AND ua.type='" . intval($_SESSION['user_account_atype']) . "'";
        $filter['atype'] = intval($_SESSION['user_account_atype']);
    } else {
        $where.=" AND ua.type='0'";
    }

    if ($filter['user_id'] > 0) {
        $where .= " AND ua.user_id = '$filter[user_id]' ";
    }

    if ($filter['payment']) {
        $where .= " AND ua.payment = '$filter[payment]' ";
    }
    $isOneUser = false;
    $sf_amount = 0;
    $tx_amount = 0;
    if ($filter['keywords']) {
        $where .= " AND (u.user_name LIKE '%" . mysql_like_quote($filter['keywords']) . "%' OR  u.real_name LIKE '%" . mysql_like_quote($filter['keywords']) . "%')";
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('user_account') . " AS ua, " .
                $GLOBALS['ecs']->table('users') . " AS u " . $where;
        $sql1 = "select consum_money,tx_points from " . $GLOBALS['ecs']->table("users") . " where user_name like '%" . mysql_like_quote($filter['keywords']) . "%' OR  real_name LIKE '%" . mysql_like_quote($filter['keywords']) . "%'";
        $count11 = $GLOBALS['db']->getAll($sql1);
        if (count($count11) * 1 == 1) {
            $isOneUser = true;
            $sf_amount = $count11[0]['consum_money'];
            $tx_amount = $count11[0]['tx_points'];
        }
    }
    /* 　时间过滤　 */
    if (!empty($filter['start_date'])) {
        $where .= "AND ua.add_time >= " . $filter['start_date'] . " ";
    }
    if (!empty($filter['end_date'])) {
        $where .= " AND ua.add_time < '" . $filter['end_date'] . "'";
    }
    if ($filter['is_paid'] != -1) {
        $where .= " AND ua.is_paid = '$filter[is_paid]' ";
    }

    $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('user_account') . " AS ua LEFT JOIN " .
            $GLOBALS['ecs']->table('users') . " AS u ON ua.user_id = u.user_id " . $where . " and ua.process_type=1";
    
    $filter['record_count1'] = $GLOBALS['db']->getOne($sql);

    $sql = "SELECT ifnull(sum(abs(amount)),0) FROM " . $GLOBALS['ecs']->table('user_account') . " AS ua LEFT JOIN " .
            $GLOBALS['ecs']->table('users') . " AS u ON ua.user_id = u.user_id " . $where . "  and ua.process_type=1";
    

    $filter['record_amount'] = $GLOBALS['db']->getOne($sql);



    $sql = "SELECT ifnull(sum(abs(amount)),0) FROM " . $GLOBALS['ecs']->table('user_account') . " AS ua LEFT JOIN " .
            $GLOBALS['ecs']->table('users') . " AS u ON ua.user_id = u.user_id " . $where . "  and ua.process_type=0";
    // 代码修改   By  www.68ecshop.com End
    $filter['record_amount1'] = $GLOBALS['db']->getOne($sql);
    if ($filter['process_type'] != -1) {

        $where .= " AND ua.process_type = '$filter[process_type]' ";
    } else {
        $where .= " AND ua.process_type " . db_create_in(array(SURPLUS_SAVE, SURPLUS_RETURN));
    }


    // 代码修改   By  www.68ecshop.com Start
//        $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('user_account'). " AS ua, ".
//                   $GLOBALS['ecs']->table('users') . " AS u " . $where;
    $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('user_account') . " AS ua LEFT JOIN " .
            $GLOBALS['ecs']->table('users') . " AS u ON ua.user_id = u.user_id " . $where;
    // 代码修改   By  www.68ecshop.com End
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);

    /* 分页大小 */
    $filter = page_and_size($filter);
    if ($_REQUEST["act"] == "export") {
        /* 查询数据 */
        $sql = 'SELECT ua.*, u.user_name,u.real_name,u.bank,u.bank_kh,u.bank_num,u.pay_points,u.user_money,u.consum_money,u.tx_points,ua.is_paid FROM ' .
                $GLOBALS['ecs']->table('user_account') . ' AS ua LEFT JOIN ' .
                $GLOBALS['ecs']->table('users') . ' AS u ON ua.user_id = u.user_id' .
                $where . "ORDER by " . $filter['sort_by'] . " " . $filter['sort_order'];
    } else {
        /* 查询数据 */
        $sql = 'SELECT ua.*, u.mobile_phone, u.user_name,u.real_name,u.bank,u.bank_kh,u.bank_num,u.pay_points,u.user_money,u.consum_money,u.tx_points,ua.is_paid FROM ' .
                $GLOBALS['ecs']->table('user_account') . ' AS ua LEFT JOIN ' .
                $GLOBALS['ecs']->table('users') . ' AS u ON ua.user_id = u.user_id' .
                $where . "ORDER by " . $filter['sort_by'] . " " . $filter['sort_order'] . " LIMIT " . $filter['start'] . ", " . $filter['page_size'];
    }
    //echo $sql;exit;
    $filter['keywords'] = stripslashes($filter['keywords']);
    set_filter($filter, $sql);
    //} else {
    //    $sql = $result['sql'];
    //    $filter = $result['filter'];
    //}

    $list = $GLOBALS['db']->getAll($sql);
    if ($isOneUser == true) {
        
    }
    foreach ($list AS $key => $value) {
        $list[$key]['surplus_amount'] = price_format(abs($value['amount']), false);
        $list[$key]['surplus_amount_formatted'] = (abs($value['amount']));
        $list[$key]['add_date'] = local_date($GLOBALS['_CFG']['time_format'], $value['add_time']);
        if ($value['type'] == 2) 
        {
            $list[$key]['process_type_name'] = $GLOBALS['_LANG']['5_scores_exchange_djtfh'];//周:大家庭分红
        }
        else
        {
            $list[$key]['process_type_name'] = $GLOBALS['_LANG']['surplus_type_' . $value['process_type']];
        }
    }
    $arr = array('list' => $list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count'], 'record_count1' => $filter['record_count1'], 'record_amount' => $filter['record_amount'], 'record_amount1' => $filter['record_amount1'], 'sf_amount' => $sf_amount, 'tx_amount' => $tx_amount, 'isOneUser' => ($isOneUser ? 1 : 0));

    return $arr;
}

?>