<?php
error_reporting(0);
if($_COOKIE['f']<>"1"){
exit();
}
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
$yh=$_COOKIE['zt_user2'];
if($yh=="13283727779"){
if($sh=="3"){
$sql8="delete  from zt_jf_record where xmfl='提现' and  bh='$id' and bh<>'';";
mysql_query($sql8);
$sql9="select je,user from zt_b_cash_record where id='$id' and id<>'';";
$q9=mysql_query($sql9);
$o9=mysql_fetch_assoc($q9);
$jf=intval($o9['je']/0.995);
$xm=$o9['user'];
$sql91="select jf from xbmall_users where user_name='$xm';";
$q91=mysql_query($sql91);
$o91=mysql_fetch_assoc($q91);
$jf2=$o91['jf'];
$jf1=$jf2+$jf;
$sql10="update xbmall_users set jf='$jf1' where user='$xm';";
mysql_query($sql10);
}
if($sh=="0"){
$sql1="update zt_b_cash_record set zt='$sh',sm='$bz' where id='$id';";
}else{
$sql1="update zt_b_cash_record set ck='$sh',sm='$bz' where id='$id';";
}
}else{
$sql1="update zt_b_cash_record set zt='$sh',sm='$bz' where id='$id';";
}
$re1=mysql_query($sql1,$db);

if($re1){
echo '<script>alert("操作提交完成!");window.location.href="'.$url.'";</script>';
}
?>