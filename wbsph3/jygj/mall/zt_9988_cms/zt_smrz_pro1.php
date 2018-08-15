<?php
error_reporting(0);
header("Content-type:text/html; charset=utf-8"); 
include "../config/zt_config.php";
include "config/check.php";
$db=mysql_connect($db_host,$db_user,$db_pwd);
mysql_query("SET NAMES $coding"); 
mysql_select_db($db_database);
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
$yh=$_COOKIE['zt_user2'];
$bz=trim(htmlspecialchars($_POST["bz"]));

if($bz==''){
	echo '<script>alert("请输入删除原因!");window.location.href="'.$url.'";</script>';
	exit();
}


$url=trim(htmlspecialchars($_POST["url"]));
$id=trim(htmlspecialchars($_POST["id"]));
$sql0="SELECT * from xbmall_users where id ='$id';";
$re10=mysql_query($sql0,$db);
$res0=mysql_fetch_array($re10);
$user=$res0['user'];

if($user=='admin'){
	echo '<script>alert("不可以删除admin账户!");window.location.href="'.$url.'";</script>';
	exit();
}

$sql1="delete from xbmall_users where id='$id';";
$re11=mysql_query($sql1,$db);
$sql12="delete from zt_shopinfo where userid='$id';";
$re112=mysql_query($sql12,$db);
$sql13="delete from zt_memberinfo where userid='$id';";
$re113=mysql_query($sql13,$db);
$sql2="INSERT INTO `zt_scrz`(`id`, `ip`, `date`, `czz`, `bsyh`, `bz`, `by`) VALUES (null,'$ip','$date','$yh','$user','$bz','')";
$re12=mysql_query($sql2,$db);
if($re11&&($re112||$re113)){
echo '<script>alert("删除成功!");window.location.href="'.$url.'";</script>';
}
mysql_close($db);
?>