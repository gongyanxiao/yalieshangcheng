<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<style type="text/css">
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
body,td,th {
	font-size: 12px;
	color: #666;
}
.STYLE1 {
	color: #fff;
	font-weight: bold;
}
</style>
</head>

<body>
<?php
if($_COOKIE['i']<>"1"){
exit();
}
//积分赠送批量操作
include "../config/zt_config.php";
date_default_timezone_set("Asia/Shanghai");
$db=mysql_connect($db_host,$db_user,$db_pwd);
mysql_query("SET NAMES $coding"); 
mysql_select_db($db_database);
$week=date("w");
//可提现部分
if($week>='1' and  $week<='5'){
//查询兑换日期，判断是否重复操作可提现部分兑换
$date=date("Y-m-d");
$sql="select * from zt_sjzw  where date like '%$date%' order by id desc";
$r2=mysql_query($sql,$db);
$num2=mysql_num_rows($r2);
if($num2>=1){
echo '<font color="red"><b>周一周五积分已赠送，请不要重复操作!</b></font>';
exit();
}
$q1="select sum(jyje) as je,mjhyh from zt_orderlist group by jyje order by id desc";
$r1=mysql_query($q1,$db);
$num1=mysql_num_rows($r1);
	for($i=0;$i<$num1;$i++){
	$data=mysql_fetch_array($r1);
	$user=$data['mjhyh'];
    $je=$data['je']*0.12*0.2;
if(floor($je/1000)>=1){
//查询上次兑换积分
$dhje=$je*0.633;
$q2="select  sum(dhje) as dh,date  from zt_sjzw  where  ssyh='$user'  order by id desc";
$r2=mysql_query($q2,$db);
$data2=mysql_fetch_assoc($r2);	
//去除上次兑换金额
$scdhje=$data2['dh'];
$syje=$dhje-$scdhje;
if(floor($syje/1000)>=1){
$xhje=floor($syje/1000)*1000;
$jf=floor($syje/1000)*600;
$sql4="select jf from xbmall_users where user_name='$user' order by id desc";
$query4=mysql_query($sql4);
$jf4=mysql_fetch_assoc($query4);
$jf5=$jf4['jf']+$jf;
$q3="update xbmall_users set jf='$jf5' where user='$user'";
$r3=mysql_query($q3,$db);
if($r3){
$date=date('Y-m-d');
$sql6="INSERT INTO `zt_sjzw`(`id`,`date`,`dhje`,`zw`,`ssyh`,`lx`) VALUES (NULL,'$date','$xhje','$jf','$user','可提现积分');";
$query6=mysql_query($sql6);
if($query6){
echo $user."兑换了".$xhje.'<br>';
}
}
}

}
}
}
//线上兑换部分
if($week=='6'){

//查询兑换日期，判断是否重复操作可提现部分兑换
$date=date("Y-m-d");
$sql="select * from zt_sjzl where date like '%$date%' order by id desc";
$r2=mysql_query($sql,$db);
$num2=mysql_num_rows($r2);
if($num2>=1){
echo '<font color="red"><b>周六积分已赠送，请不要重复操作!</b></font>';
exit();
}
$q1="select sum(jyje) as je,mjhyh from zt_orderlist group by jyje order by id desc";
$r1=mysql_query($q1,$db);
$num1=mysql_num_rows($r1);
	for($i=0;$i<$num1;$i++){
	$data=mysql_fetch_array($r1);
	$user=$data['mjhyh'];
    $je=$data['je']*0.12*0.2;
if(floor($je/1000)>=1){
//查询上次兑换积分
$dhje=$je*0.167;
$q2="select  sum(dhje) as dh,date  from zt_sjzl where  ssyh='$user'  order by id desc";
$r2=mysql_query($q2,$db);
$data2=mysql_fetch_assoc($r2);	
//去除上次兑换金额
$scdhje=$data2['dh'];
$syje=$dhje-$scdhje;
if(floor($syje/1000)>=1){
$xhje=floor($syje/1000)*1000;
$jf=floor($syje/1000)*600;
//更新积分
$sql4="select xfjf from xbmall_users where user_name='$user' order by id desc";
$query4=mysql_query($sql4);
$jf4=mysql_fetch_assoc($query4);
$jf5=$jf4['xfjf']+$jf;
$q3="update xbmall_users set xfjf='$jf5' where user='$user'";
$r3=mysql_query($q3,$db);
if($r3){
//更新积分
$date=date('Y-m-d');
$sql6="INSERT INTO `zt_sjzl`(`id`,`date`,`dhje`,`zw`,`ssyh`,`lx`) VALUES (NULL,'$date','$xhje','$jf','$user','保险和养老金积分');";
$query6=mysql_query($sql6);
if($query6){
echo $user."兑换了".$xhje.'<br>';
}
}
}

}
}

}

//消费保险积分
if($week=='0'){
//查询兑换日期，判断是否重复操作可提现部分兑换
$date=date("Y-m-d");
$sql="select * from zt_sjzr where date like '%$date%' order by id desc";
$r2=mysql_query($sql,$db);
$num2=mysql_num_rows($r2);
if($num2>=1){
echo '<font color="red"><b>周日积分已赠送，请不要重复操作!</b></font>';
exit();
}
$q1="select sum(jyje) as je,mjhyh from zt_orderlist group by jyje order by id desc";
$r1=mysql_query($q1,$db);
$num1=mysql_num_rows($r1);
	for($i=0;$i<$num1;$i++){
	$data=mysql_fetch_array($r1);
	$user=$data['mjhyh'];
    $je=$data['je']*0.12*0.2;
if(floor($je/1000)>=1){
//查询上次兑换积分
$dhje=$je*0.167;
$q2="select  sum(dhje) as dh,date  from  zt_sjzr   where  ssyh='$user'  order by id desc";
$r2=mysql_query($q2,$db);
$data2=mysql_fetch_assoc($r2);	
//去除上次兑换金额
$scdhje=$data2['dh'];
$syje=$dhje-$scdhje;
if(floor($syje/1000)>=1){
$xhje=floor($syje/1000)*1000;
$jf=floor($syje/1000)*600;
//更新积分
$sql4="select sjjf from xbmall_users where user_name='$user' order by id desc";
$query4=mysql_query($sql4);
$jf4=mysql_fetch_assoc($query4);
$jf5=$jf4['sjjf']+$jf;
$q3="update xbmall_users set xfjf='$jf5' where user='$user'";
$r3=mysql_query($q3,$db);
if($r3){
//更新积分
$date=date('Y-m-d');
$sql6="INSERT INTO `zt_sjzr`(`id`,`date`,`dhje`,`zw`,`ssyh`,`lx`) VALUES (NULL,'$date','$xhje','$jf','$user','保险和养老金积分');";
$query6=mysql_query($sql6);
if($query6){
echo $user."兑换了".$xhje.'<br>';
}
}
}

}
}

}
?>
</body>
</html>
