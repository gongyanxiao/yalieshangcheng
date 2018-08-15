<?php

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
//require_once (ROOT_PATH . 'includes/modules/PHPKit-CMBC/php_java.php');
//include(ROOT_PATH . '/includes/modules/PHPKit-CMBC/CMBC_SDK_TOOLS.php');
//include(ROOT_PATH . '/includes/lib_payment.php');
//
//$postContextJson = file_get_contents('php://input');
//if (!empty($postContextJson)) {
//    $cmbc_sdk = new CMBC_SDK_TOOLS(0);
//    $result = $cmbc_sdk->toNotifyOrder($postContextJson);
//    if ($result['code'] * 1 == 1) {
//        echo ("SUCCESS");
//        exit;
//    }
//}//if (!empty($postContextJson)) {
require(ROOT_PATH . '/includes/modules/payment/zhizunbao.php');
require(ROOT_PATH . 'includes/lib_payment.php');
require(ROOT_PATH . 'includes/lib_order.php');
//C159A368D0FD14BA38B2D1925F5C6C6E
//C159A368D0FD14BA38B2D1925F5C6C6E
$zhizunbao = new zhizunbao();
$result = $zhizunbao->respond();
if ($result === false) {
    echo "no";
} else {
    echo $result;
}



