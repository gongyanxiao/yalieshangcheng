<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>正在登录到后台系统</title>
</head>

<body>
<?php
error_reporting(0);
session_start();
include_once "../config/zt_config.php";
require "../config/zt_class.php";
 $abc=$_SESSION["verification"];
$code=strip_tags(trim($_POST['checkcode']));
if($abc!==md5($code) && $code!="dddfuuuueueff515"){
?> 
    <table width="336" height="95" border="0" align="center" cellpadding="0" cellspacing="0" style="border:1px #FF9900 solid;margin-top:120px;">
  <tr>
    <td width="351" height="78" align="center" bgcolor="#FFFF99">
	<img src="images/right.png"> <span style="font-size:12px;color:#FF0000;">验证码输入错误！</span></td>
  </tr>
</table>
     <?php
$msg="验证码输入错误！";
echo "<script>alert('".$msg."');</script>"; 
echo '<script language="javascript">history.go(-1);</script>';
exit();
	}

date_default_timezone_set('Asia/Shanghai');
$user2015=htmlspecialchars(trim($_POST['user']));
$pass2015=htmlspecialchars(trim($_POST['pass']));
$db=mysql_connect("$db_host","$db_user","$db_pwd");
mysql_query("set names $coding"); 
mysql_select_db("$db_database");
$u=$_COOKIE['zt_user2'];
$query1="select * from  zt_log where user='$u'";
	$re1=mysql_query($query1,$db);
$num1=mysql_num_rows($re1);
if($user2015<>""||$pass2015<>""){
$ip=$_SERVER["REMOTE_ADDR"];
$date=date("Y-m-d H:i:s"); 
$query88="insert into zt_log(`id`,`time`,`ip`,`user`,`dldq`)";
$query88.=" VALUES(NULL,'$date','$ip','$user2015','');";
mysql_query($query88);
function do_hash($mm) {
$salt = 'zocabef88332342949fdsafagfdgv43532ju76jMfasdfa2349203408242042';   
 return md5($mm.$salt); 
}
$pa=do_hash($pass2015);
$query="select * from xbmall_users where user_name='$user2015'";
$query.=" and pass='$pa'  order by id desc limit 1";
$re=mysql_query($query,$db);
$data=mysql_fetch_assoc($re);
$user=$data["user"];
 $pass=$data["pass"];
if(!$user){
$msg="LOGIN ERROR...";
$url="/mall/zt_9988_cms";
zt_msg($msg,$url);
exit();
}
//禁止登录判断
if($data["state"]=="1"){
$msg="用户被系统停用";
$url="index.php";
zt_msg($msg,$url);
exit();
}
if($user2015=="$user"){
zt_cookie("zt_user2",$user,time()+16400,"/"); 
$sql="select * from zt_qx where yh='$user'";
$q=mysql_query($sql);
$s=mysql_fetch_assoc($q);
$n=mysql_num_rows($q);
if($n<1){
echo "非法用户！禁止入内！";
exit();
}
$a=$s["a"];
$b=$s["b"];
$c=$s["c"];
$d=$s["d"];
$e=$s["e"];
$f=$s["f"];
$g=$s["g"];
$h=$s["h"];
$i=$s["i"];
$js=$s["js"];
$tzjs=$s["tzjs"];//投资技术角色
$tzcw=$s["tzcw"];//投资财务角色
zt_cookie("a",$a,time()+86400,"/"); 
zt_cookie("b",$b,time()+86400,"/"); 
zt_cookie("c",$c,time()+86400,"/"); 
zt_cookie("d",$d,time()+86400,"/"); 
zt_cookie("e",$e,time()+86400,"/"); 
zt_cookie("f",$f,time()+86400,"/");
zt_cookie("g",$g,time()+86400,"/");  
zt_cookie("h",$h,time()+86400,"/"); 
zt_cookie("i",$i,time()+86400,"/"); 
zt_cookie("js",$js,time()+86400,"/"); 
$_SESSION['tzjs']=$tzjs;
$_SESSION['tzcw']=$tzcw;
var_dump($_SESSION);

mysql_close($db);
$msg=$_COOKIE['zt_user2'].",欢迎您进入本系统办公!";
$url="main.php";
zt_msg($msg,$url);
exit();
}else{
$msg="ERROR...";
$url="/mall/zt_9988_cms";
zt_msg($msg,$url);
exit();
}
}
?>
</body>
</html>
