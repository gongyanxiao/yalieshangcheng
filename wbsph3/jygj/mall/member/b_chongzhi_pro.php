<?php
error_reporting(0);
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL;
$host = $_SERVER['HTTP_HOST'];
if(substr($referer,7,strlen($host)) != $host){
 echo '非法操作';
exit();
}
date_default_timezone_set("Asia/Shanghai");
include_once "../config/check.php";
include_once "../config/zt_config.php";
$db=mysql_connect($db_host,$db_user,$db_pwd);
mysql_query("SET NAMES $coding"); 
mysql_select_db($db_database);
$je=floor(htmlspecialchars($_POST['je']));
if(!is_numeric($je) || empty($je) || $je<0){
print("<script language='javascript'>alert('积分为数字格式!');</script>");
exit();
}
$user=$_COOKIE['ECS']['username'];
$sql="select jf,a,xfjf from xbmall_users where user_name='$user';";
$qs1=mysql_query($sql);
$sf1=mysql_fetch_assoc($qs1);
$jf=$sf1["jf"];
$xfjf=$sf1["xfjf"];
$sf=$sf1["a"];
if($jf<="0"){
print("<script language='javascript'>alert('积分余额不足!不可以转换！');</script>");
exit();
}else{
	if($jf>=$je){
	$jf1=round(($jf-$je),2);
	$xfjf1=round(($je+$xfjf),2);
$sql1="update xbmall_users set jf='$jf1',xfjf='$xfjf1' where user='$user';";
$q1=mysql_query($sql1);
if($q1){
//记录转换日志
$date=date("Y-m-d H:i:s");
$jf2="+".$je;
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
$url='http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'<br><font color="red">操作IP:'.$ip.'</font>';
$sql4="INSERT INTO  zt_b_exchange_record(`id`,`date`,`jf`,`sf`,`user`,`url`)VALUES(NULL,'$date','$jf2','$sf','$user','$url');";
mysql_query($sql4);
$bz3="转换扣除".round($je,2);
$m1="-".round($je,2);
$sqlzw3="INSERT INTO  `zt_jf_record`(`id`,`xmfl`,`date`,`jf`,`bz`,`user`) VALUES(null,'转换扣除','$date','$m1','$bz3','$user');";
mysql_query($sqlzw3);
$bz4="转换增加".round($je,2);
$m4=round($je,2);
$sqlzw4="INSERT INTO  `zt_jf_record`(`id`,`xmfl`,`date`,`jf`,`bz`,`user`) VALUES(null,'转换增加','$date','$m4','$bz4','$user');";
mysql_query($sqlzw4);
print("<script language='javascript'>alert('恭喜您！转换完成！');</script>");
print("<script language='javascript'>window.location.href='b_chongzhi.html';</script>");
exit();
	}
	}else{
print("<script language='javascript'>alert('积分余额不足！');</script>");
		
}
	
	}
?>