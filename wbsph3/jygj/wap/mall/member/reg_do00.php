<?php 
//普通会员注册处理脚本
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL;
$host = $_SERVER['HTTP_HOST'];
if(substr($referer,7,strlen($host)) != $host){
 echo '非法操作';
exit();
}
date_default_timezone_set("Asia/Shanghai");
include "../config/zt_config.php";
include "../config/zt_class.php";
$username=htmlspecialchars(trim($_POST['username']));
$password1=htmlspecialchars(trim($_POST['password1']));
$password2=htmlspecialchars(trim($_POST['password2']));
$sjhm=htmlspecialchars($_POST['sjhm']);
$xm=htmlspecialchars(trim($_POST['xm']));
//身份证号
$sfzh=htmlspecialchars(trim($_POST['sfzh']));
$xxdz=htmlspecialchars($_POST['xxdz']);
$p=htmlspecialchars($_POST['p']);
$c=htmlspecialchars($_POST['c']);
$a=htmlspecialchars($_POST['a']);
$xy=htmlspecialchars($_POST['xy']);
if(!preg_match("/^(13[0-9]|15[012356789]|17[01235678]|18[0-9]|14[579])[0-9]{8}$/",$username)){
	echo '<script>alert("请输入正确的手机号作为会员帐号！")</script>';
	exit();
	}
if(empty($password1)){
	echo '<script>alert("密码不能为空！")</script>';
	exit();
}
if(strlen($password1)>6){
	echo '<script>alert("密码长度限制六位！")</script>';
	exit();
}
if($password1<>$password2){
	echo '<script>alert("两次输入的密码不一致！")</script>';
	exit();
}
if(strlen($xm)<4 or strlen($xm)>24   or $xm=="请输入姓名"){
	echo '<script>alert("姓名不能为空!")</script>';
	exit();
}
if(!preg_match('/^\d{17}(\d|X)$/',$sfzh)&&!preg_match('/^\d{15}$/',$sfzh)){
	echo '<script>alert("身份证格式不正确！若含有字母，请大写！")</script>';
	exit();
}
if(!preg_match("/^(13[0-9]|15[012356789]|17[01235678]|18[0-9]|14[579])[0-9]{8}$/",$sjhm)){
	echo '<script>alert("请输入正确的手机号作为会员帐号！")</script>';
	exit();
	}

if($username<>$sjhm){
	echo '<script>alert("输入的手机号码必须和用户名保持一致！")</script>';
	exit();
}
if(strlen($xxdz)<"6" or $xxdz=="详细地址 ~地区注册后不能自行修改"){
	echo '<script>alert("详细地址不完整或为空!")</script>';
	exit();
}
if($xy<>"1"){
echo '<script>alert("请同意协议后方可进行注册!")</script>';
	exit();
}

$mysql_servername=$db_host; //服务器名称
$mysql_username=$db_user;
$mysql_password=$db_pwd;
$mysql_dbname=$db_database;
$conn=mysql_connect($mysql_servername ,$mysql_username ,$mysql_password);
  mysql_query("set names utf8"); //指定字符集为UTF8
  mysql_select_db($mysql_dbname,$conn);
//验证用户
if(!empty($username)){
$sql = "select  * from `xbmall_users` WHERE `user` = '$username' order by id desc";
$result= mysql_query($sql);
$num=mysql_num_rows($result);
if($num>=1){
echo '<script>alert("用户名已被人使用,请更换!")</script>';
exit();
}
$sql3= "select  * from xbmall_users where sfzh='$sfzh' and lx=0 order by id desc";
$result3= mysql_query($sql3);
$num3=mysql_num_rows($result3);
if($num3>=1){
echo '<script>alert("此身份证号已被人使用,请更换!")</script>';
exit();
}
}

/*
if(!empty($sjhm)){
$sql1 = "select  * from `xbmall_users` where `sjhm` = '$username' order by id desc";
$result1= mysql_query($sql1);
$num1=mysql_num_rows($result1);
if($num1>=1){
echo '<script>alert("手机号已被人使用,请更换!")</script>';
exit();
}
}*/

//验证密码
if (!empty($password1)){
if (strlen($password1)<6 ||strlen($password1)>16){
echo '<script>alert("密码为6-16个字符组成!")</script>';
exit();
}
}

function getIP(){ 
if (isset($_SERVER)) { 
if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) { 
$realip = $_SERVER['HTTP_X_FORWARDED_FOR']; 
} elseif (isset($_SERVER['HTTP_CLIENT_IP'])) { 
$realip = $_SERVER['HTTP_CLIENT_IP']; 
} else { 
$realip = $_SERVER['REMOTE_ADDR']; 
} 
} else { 
if (getenv("HTTP_X_FORWARDED_FOR")) { 
$realip = getenv( "HTTP_X_FORWARDED_FOR"); 
} elseif (getenv("HTTP_CLIENT_IP")) { 
$realip = getenv("HTTP_CLIENT_IP"); 
} else { 
$realip = getenv("REMOTE_ADDR"); 
} 
} 
return $realip; 
}
$ip=getIP();
$date=date("Y-m-d H:i:s");
function do_hash($password1) {
$salt = 'zocabef88332342949fdsafagfdgv43532ju76jMfasdfa2349203408242042';   
return md5($password1.$salt); 
}
$mm1=do_hash($password1);
$query99="insert into zt_user(`id`,`user`,`pass`,`ip`,`role`,`ssfl`,`state`,`tx`,`xm`,`zsq`,`yzs`,`zcrq`,`sfzh`,";
$query99.="`sjhm`,`ck`,`a`,`b`,`c`,`xxdz`,`dpmc`,`lxdh`,`zj`,`lx`,`jf`,`xfjf`,`yljf`,`pid`)";
$query99.=" VALUES(NULL,'$username','$mm1','$ip','8','','0','','$xm','','0','$date',";
$query99.="'$sfzh','$sjhm','0','$p','$c','$a','$xxdz','','','','0','0','0','0','');";
$queryc=mysql_query($query99);
//记录登录日志
$dldq="dl".GetRandStr(9);
$query88="insert into zt_log(`id`,`time`,`ip`,`user`,`dldq`)";
$query88.=" VALUES(NULL,'$date','$ip','$username','$dldq');";
mysql_query($query88);
zt_cookie("dl",$dldq,time()+86400,"/"); 
if($queryc){
zt_cookie("zt_user1",$username,time()+86400,"/"); 
echo '<script>location.href="center.html";</script>';
}else{
echo '<script>alert("'.$query99.'")</script>';
echo '<script>alert("注册出错!")</script>';
}

?>  
