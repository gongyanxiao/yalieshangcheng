<?php
error_reporting(0);
date_default_timezone_set("Asia/Shanghai");
include_once "../config/zt_config.php";
$db=mysql_connect($db_host,$db_user,$db_pwd);
mysql_query("SET NAMES $coding"); 
mysql_select_db($db_database);
$tel=htmlspecialchars($_POST['tel']);
$hyxm=htmlspecialchars($_POST['hyxm']);
$spmc=htmlspecialchars($_POST['spmc']);
$m=abs(htmlspecialchars(floatval($_POST['m'])));
if(empty($tel)){
echo '用户手机号不能为空!';
exit();
}

$sql="select xm from xbmall_users where user_name='$tel';";
$qs1=mysql_query($sql);
$sf1=mysql_fetch_assoc($qs1);
if(empty($sf1["xm"])){
	echo '系统不存在此用户';
	}else{
		$xm=$sf1["xm"].',';
	}
		if(empty($spmc)){
echo '商品名称不能为空!';
exit();
}
if(empty($m)){
echo '交易金额不能为空!';
exit();
}else{
	$je=$m*0.12.',';
	}
$jt=$_COOKIE['pic100'];
echo $xm.$je;
?>