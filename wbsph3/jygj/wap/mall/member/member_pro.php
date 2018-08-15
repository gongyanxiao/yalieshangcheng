<?php
error_reporting(0);
ini_set("session.cookie_httponly",1);
include_once "../config/zt_config.php";
include_once "../config/zt_class.php";
date_default_timezone_set('Asia/Shanghai');
$user2015=trim(htmlspecialchars($_POST['user']));
$pass2015=trim(htmlspecialchars($_POST['password']));
$typeip=htmlspecialchars($_POST['typeip']);
if($user2015==""){
echo '<script>alert("用户名不能为空");</script>';
exit();
}
if($pass2015==""){
echo '<script>alert("密码不能为空");</script>';
exit();
}
$db=mysql_connect("$db_host","$db_user","$db_pwd");
mysql_query("set names $coding"); 
mysql_select_db("$db_database");
if($user2015<>"" and  $pass2015<>""){
$ip=$_SERVER["REMOTE_ADDR"];
$date=date("Y-m-d H:i:s"); 
function do_hash($mm) {
$salt = 'zocabef88332342949fdsafagfdgv43532ju76jMfasdfa2349203408242042';   
 return md5($mm.$salt); 
}

$pa=do_hash($pass2015);
$query="select * from xbmall_users where user_name='$user2015'";
$query.=" and pass='$pa'   order by id desc limit 1";
$re=mysql_query($query,$db);
$data=mysql_fetch_assoc($re);
$user=$data["user"];
 $pass=$data["pass"];
if(empty($user)){
echo '<script>alert("用户名或密码错误");</script>';
exit();
}
//禁止登录判断
if($data["state"]=="1"){
echo '<script>alert("用户不存在或被禁用");</script>';
exit();
}
$queryl=mysql_query("select * from xbmall_users where user_name=".$user2015,$db); 
$rl=mysql_fetch_array($queryl);
if($rl['lx']==0&&$typeip==1) {
echo '<script>alert("请选择会员登陆");</script>';
exit();
}
if($rl['lx']==1&&$typeip<>1) {
echo '<script>alert("请选择商家登陆");</script>';
exit();
}
if($user2015=="$user"){
	
zt_cookie("zt_user1",$user,time()+120960,"/"); 
if($data['is_cj']){
    zt_cookie("is_cj",   "1"  ,0,"/");
}

	
$msg=$_COOKIE['ECS']['username'].",登录成功！";
$dl="dl".GetRandStr(9);
$query88="insert into zt_log(`id`,`time`,`ip`,`user`,`dldq`)";
$query88.=" VALUES(NULL,'$date','$ip','$user2015','$dl');";
mysql_query($query88);
zt_cookie("dl",$dl,time()+86400,"/"); 
if($data['lx']=="1"){
//echo '<script>location.href="b_order_list.html";</script>';
header("location:b_order_list.html");
}else{
//echo '<script>location.href="center.html";</script>';
header("location:center.html");
}
exit();
}else{
$msg="ERROR...";
$url="/";
echo '<script>alert("'.$msg.'");</script>';
exit();
}

}
mysql_close($db); 
?>

