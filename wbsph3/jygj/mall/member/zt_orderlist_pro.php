<?php
session_start();
error_reporting(0);
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL;
$host = $_SERVER['HTTP_HOST'];
if(substr($referer,7,strlen($host)) != $host){
 echo '非法操作';
exit();
}
date_default_timezone_set("Asia/Shanghai");
include_once "../config/zt_config.php";
include_once "../config/check.php";
$db=mysql_connect($db_host,$db_user,$db_pwd);
mysql_query("SET NAMES $coding"); 
mysql_select_db($db_database);
$tel=htmlspecialchars(trim($_POST['tel']));
$hyxm=htmlspecialchars(trim($_POST['hyxm']));
$spmc=htmlspecialchars(trim($_POST['spmc']));
$m=htmlspecialchars($_POST['m']);
$s2=htmlspecialchars($_POST['sl']);
$session1=htmlspecialchars(trim($_POST['session1']));
if($session1!=$_SESSION['uniqid1']) {
  ?>
  <?  exit();
} else {
	 unset($_SESSION['uniqid1']);
}

if(empty($tel)){
?>
<script>alert("用户会员号不能为空!");</script>
<?php
echo '<script>location.href="b_orderadd.html";</script>';
exit();
}
$yh=trim($_COOKIE['ECS']['username']);
//判断商家不能给自己会员号做单
$sql1="select sfzh from xbmall_users where user_name='$yh';";
$qs11=mysql_query($sql1);
$sf11=mysql_fetch_assoc($qs11);
$sfzh2=$sf11['sfzh'];
$sql="select xm,lx,sfzh from xbmall_users where user_name='$tel';";
$qs1=mysql_query($sql);
$sf1=mysql_fetch_assoc($qs1);
$sfzh1=$sf1['sfzh'];
if($sfzh1===$sfzh2){
?>
<script>alert("不可以给自己会员号做单!");</script>
<?php
echo '<script>location.href="b_orderadd.html";</script>';
exit();
}

if($sf1["lx"]=="1"){
?>
<script>alert("买家会员号不可以用商家性质帐号!");</script>
<?php
echo '<script>location.href="b_orderadd.html";</script>';
exit();
}
if(empty($sf1["xm"])){
	echo '系统不存在此用户';
	}else{
		$xm=$sf1["xm"].',';
	}
		if(empty($spmc)){
echo '商品名称不能为空!';
echo '<script>location.href="b_orderadd.html";</script>';
exit();
}
if(empty($m)){
echo '交易金额不能为空!';
echo '<script>location.href="b_orderadd.html";</script>';
exit();
}else{
	$je=$m*0.12.',';
	}
$jt=$_COOKIE['pic100'];
echo $xm.$je;
$je2=$m*0.12;
$sql1="select sum(je) as je from zt_cz where zt in(1,2) and  user='$yh' order by id desc";
$query1=mysql_query($sql1);
$out1=mysql_fetch_assoc($query1);
if($je2>round($out1['je'],1)){
?>
<script>alert("当前账户余额不足支付平台使用!");</script>
<?php
echo '<script>location.href="b_recharge.html";</script>';
exit();
}else{
//录入订单
$sql88="select ddrq from zt_orderlist where user='$yh' order by id desc";
$q88=mysql_query($sql88);
$s88=mysql_fetch_assoc($q88);
$time=strtotime(date("Y-m-d H:i:s"))-strtotime($s88["ddrq"]);
$cle=strtotime(date("Y-m-d H:i:s"))-strtotime($s88["ddrq"]);
$tjsj=floor(($cle%(3600*24)));
if($tjsj<=8){
?>
<script>alert("请不要频繁操作!");</script>
<?php
echo '<script>location.href="b_orderadd.html";</script>';
exit();
}

include "../config/zt_class.php";
$s="select a from xbmall_users where user_name='$yh' order by id desc";
$q=mysql_query($s);
$s1=mysql_fetch_assoc($q);
$sf=$s1["a"];
$yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
$orderSn = $yCode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
$ddbh="JYGJ".$orderSn;
$xm=$sf1["xm"];
$ddrq=date("Y-m-d H:i:s");
$kc="-".$m*0.12;
sleep(2);
//扣除费用
$sql99="INSERT INTO  `zt_cz`(`id`,`ddbh`,`czmc`,`je`,`czrq`,`user`,`zt`,`sf`)VALUES(null,'$ddbh','商家平台费用扣除','$kc','$ddrq','$yh','2','$sf');";
mysql_query($sql99);
$sql3="INSERT INTO  zt_orderlist(`id`,`ddbh`,`mjhyh`,`hyxm`,`spmc`,`jyje`,`scpz`,`ddrq`,`ssyh`,`zfzt`,`ck`,`shyh`,`sf`,`bz`)";
$sql3.=" VALUES (NULL,'$ddbh','$tel','$xm','$spmc','$m','$jt','$ddrq','$yh','1','0','','$sf','$s2');";
$q3=mysql_query($sql3);
if($q3){
//转换个人积分增加权记录
$q2="select * from  zt_zsq   where  user='$tel'  order by id desc";
$r2=mysql_query($q2,$db);
$data2=mysql_fetch_assoc($r2);	
$syje=$data2["syje"];
$dhje1=$m+$syje;
$num1=mysql_num_rows($r2);
if(floor($dhje1/1000)>=1){
$zsq=floor($dhje1/1000);
$ye=fmod($dhje1,1000);
if($num1>=1){
$zsq1=$data2["zsq"];
$zsq2=$zsq+$zsq1;
$sql3="update zt_zsq set syje='$ye',zsq='$zsq2' where user='$tel';";
$sql31="update xbmall_users set zsq='$zsq2' where user='$tel';";
mysql_query($sql3);
mysql_query($sql31);
}
if($num1<1){
$zsq=floor($m/1000);
$sql33="INSERT INTO  `zt_zsq`(`id`,`zsq`,`syje`,`user`,`date`,`sf`)VALUES(null,'$zsq','$ye','$tel','$ddrq','$sf');";
$sql32="update xbmall_users set zsq='$zsq' where user='$tel';";
mysql_query($sql32);
mysql_query($sql33);
}
}
if(floor($dhje1/1000)<1){
//若不满积分兑换赠送权
if($num1>=1){
$ye5=$data2["syje"]+$m;
$sql5="update zt_zsq set syje='$ye5' where user='$tel';";
mysql_query($sql5);
}
if($num1<1){
$sql6="INSERT INTO  `zt_zsq`(`id`,`zsq`,`syje`,`user`,`date`,`sf`)VALUES(null,'','$m','$tel','$ddrq','$sf');";
//更新数据库
mysql_query($sql6);
}
}

//计算商家会员赠送权
$q4="select * from  zt_zsq   where  user='$yh'  order by id desc";
$r4=mysql_query($q4,$db);
$data4=mysql_fetch_assoc($r4);	
$num4=mysql_num_rows($r4);
$syje4=$data4["syje"];
$zsq41=$data4["zsq"];
//赠送的金额
$m1=$m*0.12;
$sje=$m1+$syje4;
//判断是否满足兑换
if(floor($sje/600)>=1){
$zsq4=floor($sje/600);
$zsq5=$zsq41+$zsq4;
$ye4=fmod($sje,600);
if($num4>=1){
$sql4="update zt_zsq set syje='$ye4',zsq='$zsq5' where user='$yh';";
$sql41="update xbmall_users set zsq='$zsq5' where user='$yh';";
mysql_query($sql41);
mysql_query($sql4);
}else{
$zsq4=floor(($m/1000)*0.2);
$ye8=fmod($m*0.12,600);
$sql4="INSERT INTO  `zt_zsq`(`id`,`zsq`,`syje`,`user`,`date`,`sf`)VALUES(null,'$zsq4','$ye8','$yh','$ddrq','$sf');";
$sql42="update xbmall_users set zsq='$zsq4' where user='$yh';";
mysql_query($sql42);
mysql_query($sql4);
}
}else{
if($num4>=1){
$ye6=$data4["syje"]+$m1;
$sql6="update zt_zsq set syje='$ye6' where user='$yh';";
mysql_query($sql6);
}else{
$sql61="INSERT INTO  `zt_zsq`(`id`,`zsq`,`syje`,`user`,`date`,`sf`)VALUES(null,'','$m1','$yh','$ddrq','$sf');";
mysql_query($sql61);
}
}
?>
<script>alert("录入订单完成!");</script>
<?php
echo '<script>location.href="b_orderadd.html";</script>';
exit();

}
}
mysql_close($db);
?>