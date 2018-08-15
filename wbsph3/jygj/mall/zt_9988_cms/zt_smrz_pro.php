<?php
error_reporting(0);
header("Content-type:text/html; charset=utf-8"); 
include "../config/zt_config.php";
include "config/check.php";
$db=mysql_connect($db_host,$db_user,$db_pwd);
mysql_query("SET NAMES $coding"); 
mysql_select_db($db_database);
$sh=trim(htmlspecialchars($_POST["sh"]));
$bz=trim(htmlspecialchars($_POST["bz"]));
$url=trim(htmlspecialchars($_POST["url"]));
$id=trim(htmlspecialchars($_POST["id"]));
$sql1="update xbmall_users set ck='$sh',pid='$bz' where id='$id';";
$re1=mysql_query($sql1,$db);
if($re1){
echo '<script>alert("操作提交完成!");window.location.href="'.$url.'";</script>';
}
?>