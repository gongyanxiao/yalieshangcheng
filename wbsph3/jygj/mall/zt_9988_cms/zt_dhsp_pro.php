<?php
error_reporting(0);
if($_COOKIE['e']<>"1"){
exit();
}
header("Content-type:text/html; charset=utf-8"); 
include "../config/zt_config.php";
include "config/check.php";
$db=mysql_connect($db_host,$db_user,$db_pwd);
mysql_query("SET NAMES $coding"); 
mysql_select_db($db_database);
//$sh=trim(htmlspecialchars($_POST["sh"]));
$id=trim(htmlspecialchars($_POST["id"]));
$bz=trim(htmlspecialchars($_POST["bz"]));
$sql1="update zt_dhsp set zt='待确认收货',kd='$bz' where id='$id';";
$re1=mysql_query($sql1,$db);
if($re1){
echo '<script>alert("操作提交完成!");window.location.href="zt_dh_order_list.php";</script>';
}
?>