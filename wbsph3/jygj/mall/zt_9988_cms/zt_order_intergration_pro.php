<?php
error_reporting(0);
date_default_timezone_set("Asia/Shanghai");
header("Content-type:text/html; charset=utf-8"); 
include "../config/zt_config.php";
include "config/check.php";
$db=mysql_connect($db_host,$db_user,$db_pwd);
mysql_query("SET NAMES $coding"); 
mysql_select_db($db_database);
$sh=trim(htmlspecialchars($_POST["sh"]));
$bz1=trim(htmlspecialchars($_POST["bz"]));
$url=trim(htmlspecialchars($_POST["url"]));
$id=trim(htmlspecialchars($_POST["id"]));
$sql1="update zt_orderlist set ck='$sh',bz='$bz1' where id='$id';";
$re1=mysql_query($sql1,$db);
if($re1){
$sql="select * from zt_orderlist where id='$id'";
$qs1=mysql_query($sql);
$sf1=mysql_fetch_assoc($qs1);
$ddbh=$sf1["ddbh"];
$date=date("Y-m-d H:i:s");
$yh=$_COOKIE["zt_user2"];
$sql6="INSERT INTO  zt_jf_record(`id`,`ddbh`,`date`,`jf`,`bz`,`user`)VALUES (NULL,'$ddbh','$date','$sh','$bz1','$yh');";
mysql_query($sql6);
echo '<script>alert("审核提交完成!");window.location.href="'.$url.'";</script>';
}
?>