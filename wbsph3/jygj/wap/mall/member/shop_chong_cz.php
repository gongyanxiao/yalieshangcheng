<?php
error_reporting(0);
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL;
$host = $_SERVER['HTTP_HOST'];
if(substr($referer,7,strlen($host)) != $host){
 echo '�Ƿ�����';
exit();
}
date_default_timezone_set("Asia/Shanghai");
include_once "../config/zt_config.php";
$db=mysql_connect($db_host,$db_user,$db_pwd);
mysql_query("SET NAMES $coding"); 
mysql_select_db($db_database);
$m=abs(htmlspecialchars($_POST['m']));
if(empty($tel)){
echo '�û��ֻ��Ų���Ϊ��!';
exit();
}
$yh=$_COOKIE['ECS']['username'];
$je2=number_format($m*0.12,2);
//��ѯ�̼�����Ƿ�ɹ�֧��
$yh=$_COOKIE['ECS']['username'];
$sql1="select sum(je) as je from zt_cz where zt='1' and  user='$yh' order by id desc";
$query1=mysql_query($sql1);
$out1=mysql_fetch_assoc($query1);
if($je2>$out1['je']){
$info="��ǰ�˻�����֧��ƽ̨ʹ�÷���".$je2."!";
echo "<script language='javascript'>alert(".$info.");</script>";
print("<script language='javascript'>window.location.href='b_recharge.html'</script>");
exit();
}
?>