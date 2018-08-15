
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<html>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Language" content="zh-CN" />
<meta http-equiv="Expires" content="0" />
<meta http-equiv="Cache-Control" content="no-cache" />
<meta http-equiv="Pragma" content="no-cache" />
<title>开联通互联网支付平台-商户接口范例-支付结果</title>
<link href="css.css" rel="stylesheet" type="text/css" />
</head>
<body>
	<center>
		<font size=16><strong>支付结果</strong></font>
	</center>
<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');

require(ROOT_PATH . 'includes/lib_payment.php');
require(ROOT_PATH . 'includes/lib_order.php');
require_once (dirname ( __FILE__ ) . "/includes/modules/openepay/php_rsa.php");

file_put_contents ( dirname ( __FILE__ ) . "/test.txt",ROOT_PATH.'includes/modules/openepay/zhifutong.php\r\n', FILE_APPEND );

include_once(ROOT_PATH.'includes/modules/openepay/zhifutong.php'); 
$zhifutong = new zhifutong();
file_put_contents ( dirname ( __FILE__ ) . "/test.txt", " dddddddddddddd::::\r\n", FILE_APPEND );

$var = var_export ( $zhifutong, TRUE );
file_put_contents ( dirname ( __FILE__ ) . "/test.txt", "post:" . $var . ":::::\r\n", FILE_APPEND );

$result = $zhifutong->respond();
if ($result === false) {
	echo "no";
} else {
	echo $result;
}
 
?>
	<div style="padding-left: 40px;">
		<div>验证结果：<?=$value?></div>
		<div>支付结果：<?=$payvalue?></div>
		<hr />
		<div>商户号：<?=$merchantId ?> </div>
		<div>商户订单号：<?=$orderNo ?> </div>
		<div>商户订单金额：<?=$orderAmount ?></div>
		<div>商户订单时间：<?=$merTranTime ?> </div>
		<div>网关支付时间：<?=$payDatetime ?></div>

	</div>
</body>
</html>