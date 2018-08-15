<?php
error_reporting(0);
date_default_timezone_set("Asia/Shanghai");
include_once "../config/zt_config.php";
$db=mysql_connect($db_host,$db_user,$db_pwd);
mysql_query("SET NAMES $coding"); 
mysql_select_db($db_database);
$tel=htmlspecialchars($_POST['tel']);
if(empty($tel)){
echo '用户手机号不能为空!';
exit();
}
//查询商家余额是否可供支付
$yh=$_COOKIE['ECS']['username'];
if(empty($yh)){
exit();
}
$sql1="select * from  xbmall_users where user_name='$tel'  order by id desc";
$query1=mysql_query($sql1);
$out1=mysql_fetch_assoc($query1);
echo $out1['xm'];
mysql_close($db);
?>