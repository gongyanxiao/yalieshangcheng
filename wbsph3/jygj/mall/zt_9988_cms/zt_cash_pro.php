<?php
error_reporting(0);
if($_COOKIE['f']<>"1"){
exit();
}
header("Content-type:text/html; charset=utf-8"); 
include "../config/zt_config.php";
include "config/check.php";
session_start(); 

$db=mysql_connect($db_host,$db_user,$db_pwd);
mysql_query("SET NAMES $coding"); 
mysql_select_db($db_database);
$sh=trim(htmlspecialchars($_POST["sh"]));
$bz=trim(htmlspecialchars($_POST["bz"]));
$url=trim(htmlspecialchars($_POST["url"]));
$id=trim(htmlspecialchars($_POST["id"]));
$session=trim(htmlspecialchars($_POST["mysession"]));
$date=date("Y-m-d H:i:s");
$bid=intval($id)+1;
$sql1='';
$yh=$_COOKIE['zt_user2'];

if(($session!==$_SESSION['uniqid'])) {
	echo '<script>alert("请不要重复点击!")</script>';
print("<script language='javascript'>location.reload();</script>");
	exit();
} else {
$_SESSION['uniqid'] = md5(uniqid('jygj',true));
}



if($yh=="13283727779"){
	$sql1="update zt_b_cash_record set zt='0',ck='$sh',sm='$bz' where id='$id';";
}else{
$sql9="select ck from zt_b_cash_record where id='$id' and id<>'';";
$q9=mysql_query($sql9);
$o9=mysql_fetch_assoc($q9);
	if($o9['ck']=='4'){

	if($sh=="3") {
		$sql9="select je,user from zt_b_cash_record where id='$id' and id<>'';";
	$q9=mysql_query($sql9);
	$o9=mysql_fetch_assoc($q9);
	$jf=intval($o9['je']/0.995);
	$xm=$o9['user'];
	$sql8="INSERT INTO `zt_jf_record`(`id`, `xmfl`, `date`, `jf`, `bz`, `user`, `bh`)  VALUES (null,'被驳回提现','$date','$jf','已驳回($bz)','$xm','');";
	mysql_query($sql8);
	$sql91="select jf from xbmall_users where user_name='$xm';";
	$q91=mysql_query($sql91);
	$o91=mysql_fetch_assoc($q91);
	$jf2=$o91['jf'];
	$jf1=$jf2+$jf;
	$sql10="update xbmall_users set jf='$jf1' where user='$xm';";
	mysql_query($sql10);
		$sql1="update zt_b_cash_record set zt='$sh',sm='$bz',ck='$sh',ckdate='$date' where id='$id';";
	} else {
		$sql1="update zt_b_cash_record set zt='$sh',sm='$bz',ckdate='$date' where id='$id';";
	}
	
}else {
	echo '<script>alert("尚未通过管理!");window.location.href="'.$url.'";</script>';
	exit();
}
}
$re1=mysql_query($sql1,$db);

if($re1){
echo '<script>alert("操作提交完成!");window.location.href="'.$url.'";</script>';
}
?>