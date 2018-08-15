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
.STYLE3 {
	font-size: 36px;
	font-weight: bold;
	color: #FF0000;
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

$date=date("Y-m-d H:i:s");
$db=mysql_connect($db_host,$db_user,$db_pwd);
mysql_query("SET NAMES $coding"); 
mysql_select_db($db_database);
$sql1="select * from zt_ed  order by id desc";
	$q1=mysql_query($sql1);
	$ss=mysql_fetch_assoc($q1);
	$jf1=$ss['jf1'];
	$jf2=$ss['jf2'];
	$jf3=$ss['jf3'];
	//提醒是否确定赠送额定是否正确
 $week=date("w");
//可提现部分
if($week>='1' and  $week<='5'){
$sql2="select * from zt_zsq where zsq>='1'  order by id desc";
$r1=mysql_query($sql2,$db);
$num1=mysql_num_rows($r1);
for($i=0;$i<$num1;$i++){
$data=mysql_fetch_array($r1);
$user=$data['user'];
$sql3="select * from zt_zs where user='$user' order by id desc";
$query3=mysql_query($sql3);
$num3=mysql_num_rows($query3);
//判断是否存在记录
$out=mysql_fetch_assoc($query3);
$zw1=$jf1*$data['zsq'];
$zw1=round($zw1,2);
$zw0=empty($out['zw'])?0:$out['zw'];
$zw=$zw1+$zw0;
$zw4=empty($out['jqzsjf'])?0:$out['jqzsjf'];
$zw2=$zw1+$zw4;
$zw3=floor($zw2/600);
if($zw3>0) {
	$zw2=$zw2-$zw3*600;
}
//查询赠送权

$sql0="select * from zt_zsq where user='$user' and user<>''  order by id desc";
$q0=mysql_query($sql0);
$ss0=mysql_fetch_assoc($q0);
$zsq0=$ss0['zsq']-$zw3;	
$sql01="update zt_zsq set zsq='$zsq0'  where user='$user'";
$zs0=mysql_query($sql01);
//更新至用户积分中
$sql1="select jf,zsq,yzs from xbmall_users where user_name='$user'  order by id desc";
$q1=mysql_query($sql1);
$ss=mysql_fetch_assoc($q1);
$jf=(empty($ss['jf'])||$ss['jf']=='')?0:$ss['jf'];
$zsq=(empty($ss['zsq'])||$ss['zsq']=='')?0:$ss['zsq'];
$yzs=(empty($ss['yzs'])||$ss['yzs']=='')?0:$ss['yzs'];
$jf5=$zw1+$jf;
$zsq=$zsq-$zw3;
$yzs=$yzs+$zw3;
$sql5="update xbmall_users set yzs='$yzs',zsq='$zsq',jf='$jf5' where user='$user'";
mysql_query($sql5);
//赠送记录到数据库
$bz="当日赠送".$zw1;
$sqlzw="INSERT INTO  `zt_jf_record`(`id`,`xmfl`,`date`,`jf`,`bz`,`user`,`bh`) VALUES(null,'兑现积分','$date','$zw1','$bz','$user','0');";
mysql_query($sqlzw);

//赠送记录
if($num3>=1){
	$sql4="update zt_zs set zw='$zw',jqzsjf='$zw2',date='$date' where user='$user'";
	mysql_query($sql4);
}else{
	$sql4="INSERT INTO  `zt_zs`(`id`,`zw`,`zl`,`zr`,`date`,`user`,`jqzsjf`) VALUES(null,'$zw1','0','0','$date','$user','$zw1');";
	mysql_query($sql4);
}	
if($zs0){
	echo $user.'今天赠送完毕'.'<br>';
}
//赠送全结束
}
//周五赠送记录
}
//线上兑换部分
if($week=='6'){
$sql2="select * from zt_zsq where zsq>='1' and user<>''  order by id desc";
$r1=mysql_query($sql2,$db);
$num1=mysql_num_rows($r1);
for($i=0;$i<$num1;$i++){
$data=mysql_fetch_array($r1);
$user=$data['user'];
$sql3="select * from zt_zs where user='$user' order by id desc";
$query3=mysql_query($sql3);
$num3=mysql_num_rows($query3);
//判断是否存在记录
$out=mysql_fetch_assoc($query3);
$zw1=$jf2*$data['zsq'];
$zw1=round($zw1,2);
$zw0=empty($out['zl'])?0:$out['zl'];
$zw=$zw1+$zw0;
$zw4=empty($out['jqzsjf'])?0:$out['jqzsjf'];
$zw2=$zw1+$zw4;
$zw3=floor($zw2/600);
if($zw3>0) {
	$zw2=$zw2-$zw3*600;
}
//查询赠送权

$sql0="select * from zt_zsq where user='$user'  order by id desc";
$q0=mysql_query($sql0);
$ss0=mysql_fetch_assoc($q0);
$zsq0=$ss0['zsq']-$zw3;	
$sql01="update zt_zsq set zsq='$zsq0'  where user='$user'";
$zs0=mysql_query($sql01);
//更新至用户积分中
$sql1="select xfjf,zsq,yzs from xbmall_users where user_name='$user'  order by id desc";
$q1=mysql_query($sql1);
$ss=mysql_fetch_assoc($q1);
$xfjf=(empty($ss['xfjf'])||$ss['xfjf']=='')?0:$ss['xfjf'];
$jf5=$zw1+$xfjf;
$zsq=(empty($ss['zsq'])||$ss['zsq']=='')?0:$ss['zsq'];
$yzs=(empty($ss['yzs'])||$ss['yzs']=='')?0:$ss['yzs'];
$zsq=$zsq-$zw3;
$yzs=$yzs+$zw3;
$sql5="update xbmall_users set yzs='$yzs',xfjf='$jf5',zsq='$zsq' where user='$user'";
mysql_query($sql5);
//赠送记录到数据库
$bz="当日赠送".$zw1;
$sqlzw="INSERT INTO  `zt_jf_record`(`id`,`xmfl`,`date`,`jf`,`bz`,`user`,`bh`) VALUES(null,'消费兑换积分','$date','$zw1','$bz','$user','0');";
mysql_query($sqlzw);

//赠送记录
if($num3>=1){
	$sql4="update zt_zs set zl='$zw',jqzsjf='$zw2',date='$date' where user='$user'";
	mysql_query($sql4);
}else{
	$sql4="INSERT INTO  `zt_zs`(`id`,`zw`,`zl`,`zr`,`date`,`user`,`jqzsjf`) VALUES(null,'0','$zw1','0','$date','$user','$zw1');";
	mysql_query($sql4);
}	
if($zs0){
	echo $user.'今天赠送完毕'.'<br>';
}
//赠送全结束
}
//周六赠送记录


}

//消费保险积分
if($week=='0'){
$sql2="select * from zt_zsq where zsq>='1' and user<>''  order by id desc";
$r1=mysql_query($sql2,$db);
$num1=mysql_num_rows($r1);
while($data=mysql_fetch_array($r1)){
$user=$data['user'];
$sql3="select * from zt_zs where user='$user' order by id desc";
$query3=mysql_query($sql3);
$num3=mysql_num_rows($query3);
//判断是否存在记录
$out=mysql_fetch_assoc($query3);
$zw1=$jf3*$data['zsq'];
$zw1=round($zw1,2);
$zw0=empty($out['zr'])?0:$out['zr'];
$zw=$zw1+$zw0;
$zwa=floor($zw/1000);
$zwb=$zwa*1000;
$zw11='';
if($zwa>0) {
	for($i=0;$i<$zwa;$i++) {
		$x=$i+1;
		$bzn='这是'.$user.'于'.$date.'扣除'.$zwb.'的养老教育积分生成的第'.$x.'个保单';
		$sqln="INSERT INTO `zt_bd`(`id`, `user`, `zt`, `xmfl`, `date`, `jf`, `bz`, `shbz`) VALUES (null,'$user','0','保单兑换','$date','1000','$bzn','')";
		mysql_query($sqln);	
		$sqlm="select max(id) maxid from `zt_bd`";
		$querym=mysql_query($sqlm);	
		$rowm=mysql_fetch_array($querym);
		$res=$rowm['maxid'];
		$bzm='生成保单扣除1000';
		$sqlzwm="INSERT INTO  `zt_jf_record`(`id`,`xmfl`,`date`,`jf`,`bz`,`user`,`bh`) VALUES(null,'保单兑换','$date','-1000','$bzm','$user','$res');";
		mysql_query($sqlzwm);

	}
	$zw11=$zw-$zwb;
}
$zw4=empty($out['jqzsjf'])?0:$out['jqzsjf'];
$zw2=$zw1+$zw4;
$zw3=floor($zw2/600);
if($zw3>0) {
	$zw2=$zw2-$zw3*600;
}
//查询赠送权

$sql0="select * from zt_zsq where user='$user'  order by id desc";
$q0=mysql_query($sql0);
$ss0=mysql_fetch_assoc($q0);
$zsq0=$ss0['zsq']-$zw3;	
$sql01="update zt_zsq set zsq='$zsq0'  where user='$user'";

$zs0=mysql_query($sql01);
//更新至用户积分中
$sql1="select yljf,zsq,yzs from xbmall_users where user_name='$user'  order by id desc";
$q1=mysql_query($sql1);
$ss=mysql_fetch_assoc($q1);
$zsq=(empty($ss['zsq'])||$ss['zsq']=='')?0:$ss['zsq'];
$yzs=(empty($ss['yzs'])||$ss['yzs']=='')?0:$ss['yzs'];
$zsq=$zsq-$zw3;
$yzs=$yzs+$zw3;
$sql5="update xbmall_users set yzs='$yzs',yljf='$zw11',zsq='$zsq' where user='$user'";

mysql_query($sql5);
//赠送记录到数据库
$bz="当日赠送".$zw1;
$sqlzw="INSERT INTO  `zt_jf_record`(`id`,`xmfl`,`date`,`jf`,`bz`,`user`,`bh`) VALUES(null,'养老教育积分','$date','$zw1','$bz','$user','0');";

mysql_query($sqlzw);

//赠送记录
if($num3>=1){
	$sql4="update zt_zs set zr='$zw',jqzsjf='$zw2',date='$date' where user='$user'";
	
	mysql_query($sql4);
}else{
	$sql4="INSERT INTO  `zt_zs`(`id`,`zw`,`zl`,`zr`,`date`,`user`,`jqzsjf`) VALUES(null,'0','0','$zw','$date','$user','$zw1');";

	mysql_query($sql4);
}	
if($zs0){
	echo $user.'今天赠送完毕'.'<br>';
}
//赠送全结束
}
	//周日赠送记录

	
}

	?>
<table width="548" height="227" border="0" align="center" style="border:1px #FF3300  dashed;margin-top:58px;">
      <tr>
        <td align="center" valign="middle" bgcolor="#FFFF99"><span class="STYLE3">今日赠送操作完成!</span>
		<br><?php 
$weekarray=array("日","一","二","三","四","五","六");
echo date("Y-m-d H:i:s")." 星期".$weekarray[date("w")];
?></td>
      </tr>
    </table>
</body>
</html>
