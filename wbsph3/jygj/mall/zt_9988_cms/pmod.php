<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>修改密码</title>
</head>

<body>
<?php
if($_COOKIE['a']<>"1"){
exit();
}
include_once"config/check.php";
include "../config/zt_config.php";
$db =mysql_connect($db_host,$db_user,$db_pwd);
mysql_query("set names $coding");
mysql_select_db($db_database);
$czz=$_COOKIE['zt_user2'];
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


$user=htmlspecialchars(trim($_POST['yh']));
$user=iconv('GBK','UTF-8',$user);
$password='';
$password2='';
$sfzh='';
$szh='';
$pid='';

if(!empty($_POST['password'])) {
  $password=htmlspecialchars(trim($_POST['password']));
}
if(!empty($_POST['password2'])) {
  $password2=htmlspecialchars(trim($_POST['password2']));
}
if(!empty($_POST['sfzh'])) {
  $sfzh=htmlspecialchars(trim($_POST['sfzh']));
}

if(!empty($_POST['szh'])) {
  $szh=htmlspecialchars(trim($_POST['szh']));
}
if(!empty($_POST['pid'])) {
  $pid=htmlspecialchars(trim($_POST['pid']));
}
if(!empty($_POST['grsjhm'])) {
  $grsjhm=htmlspecialchars(trim($_POST['grsjhm']));
}


if(!empty($grsjhm)) {
  $query="update xbmall_users set  sjhm='$grsjhm' where user='$user'";
$info="修改了".$user."个人手机号码".$grsjhm;
  $query1="INSERT INTO `zt_scrz`(`id`, `ip`, `date`, `czz`, `bsyh`, `bz`, `by`) VALUES (null,'$ip','$date','$czz','$pid','$info','')";
  $re=mysql_query($query,$db);
  $re1=mysql_query($query1,$db);
  if($re&&$re1){
?>
<script type="text/javascript"> alert("修改个人手机号码完成！");  </script>

<?
  } 
}else{

$query1="SELECT count(*) as c,lx from xbmall_users where user_name='$user'";
$re1=mysql_query($query1,$db);
$rst1=mysql_fetch_array($re1);

if($rst1['c']==0||($password==''&&$password2==''&&$sfzh==''&&$szh=='')) {
?>
  <table width="336" height="81" border="0" align="center" cellpadding="0" cellspacing="0" style="border:1px #FF9900 solid;margin-top:85px;">
  <tr>
    <td width="351" height="79" align="center" bgcolor="#FFFF99">
  
  
  <img src="images/error.png"> <span class="STYLE1">查无此用户！</span></td>
  </tr>
</table>
<meta http-equiv="refresh" content="3;url=pass_mod.php?<?=$pid!=''?'?pid='.$pid:''?>"> 
<?
exit();
} 

$ts='';
if($password!=''||$password2!='') {
if(strlen($password)<3){


?>
 <table width="336" height="81" border="0" align="center" cellpadding="0" cellspacing="0" style="border:1px #FF9900 solid;margin-top:85px;">
  <tr>
    <td width="351" height="79" align="center" bgcolor="#FFFF99">
	
	
	<img src="images/error.png"> <span class="STYLE1">密码位数太短！</span></td>
  </tr>
</table>
 <script type="text/javascript">setTimeout("location.replace(document.referrer)",2000);  </script>

<?php
exit();
}
if($password2!==$password){
?>
<table width="336" height="81" border="0" align="center" cellpadding="0" cellspacing="0" style="border:1px #FF9900 solid;margin-top:85px;">
  <tr>
    <td width="351" height="79" align="center" bgcolor="#FFFF99">
	
	
	<img src="images/error.png"> <span class="STYLE1">两次输入的密码不一致！</span></td>
  </tr>
</table>
 <script type="text/javascript">setTimeout("location.replace(document.referrer)",2000); </script>

<?php
exit();
}
function do_hash($password){
$salt ='zocabef88332342949fdsafagfdgv43532ju76jMfasdfa2349203408242042';   
 return md5($password.$salt); 
}
$pa=trim(do_hash($password));
$query="update xbmall_users set  pass='$pa' where user='$user'";
  $query1="INSERT INTO `zt_scrz`(`id`, `ip`, `date`, `czz`, `bsyh`, `bz`, `by`) VALUES (null,'$ip','$date','$czz','$pid','被修改密码用户','')";
  $re=mysql_query($query,$db);
  $re1=mysql_query($query1,$db);
  if($re&&$re1){

 $ts.='<tr><td width="351" height="79" align="center" bgcolor="#FFFF99"><img src="images/dd.png"> <span class="STYLE1">修改密码成功！</span></td></tr>';

  } 
} 

if($sfzh!='') {

  if(!preg_match('/^\d{17}(\d|X)$/',$sfzh)&&!preg_match('/^\d{15}$/',$sfzh)){
?>
<table width="336" height="81" border="0" align="center" cellpadding="0" cellspacing="0" style="border:1px #FF9900 solid;margin-top:85px;">
  <tr>
    <td width="351" height="79" align="center" bgcolor="#FFFF99">
  
  
  <img src="images/error.png"> <span class="STYLE1">身份证格式不正确！若含有字母，请大写！</span></td>
  </tr>
</table>
 <script type="text/javascript">setTimeout("location.replace(document.referrer)",2000); </script>

<?php
    exit();
  }

$query="update xbmall_users set  sfzh='$sfzh' where user='$user'";
  $query1="INSERT INTO `zt_scrz`(`id`, `ip`, `date`, `czz`, `bsyh`, `bz`, `by`) VALUES (null,'$ip','$date','$czz','$pid','被修改身份证用户','')";
  $re=mysql_query($query,$db);
  $re1=mysql_query($query1,$db);
  if($re&&$re1){
 $ts.='<tr><td width="351" height="79" align="center" bgcolor="#FFFF99"><img src="images/dd.png"> <span class="STYLE1">修改身份号成功！</span></td></tr>';

  } 


} 


if($szh==1) {
  $query="update xbmall_users set  lx='0' where user='$user'";
  $query1="INSERT INTO `zt_scrz`(`id`, `ip`, `date`, `czz`, `bsyh`, `bz`, `by`) VALUES (null,'$ip','$date','$czz','$pid','被转会员的商家用户','')";
  $re=mysql_query($query,$db);
  $re1=mysql_query($query1,$db);
  if($re&&$re1){
 $ts.='<tr><td width="351" height="79" align="center" bgcolor="#FFFF99"><img src="images/dd.png"> <span class="STYLE1">商家转会员成功！</span></td></tr>';

  } 
}





?>
 <table width="336" height="81" border="0" align="center" cellpadding="0" cellspacing="0" style="border:1px #FF9900 solid;margin-top:85px;">
 <?=$ts?>
</table>
 <script type="text/javascript">setTimeout("location.replace(document.referrer)",2000); </script>



<?php

}

mysql_close($db);
?>


</body>
</html>

</div>
</body>
</html>
