<?php
error_reporting(0);
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL;
$host = $_SERVER['HTTP_HOST'];
if(substr($referer,7,strlen($host)) != $host){
 echo '非法操作';
exit();
}
include_once "../config/zt_config.php";
include_once "../config/zt_class.php";
date_default_timezone_set('Asia/Shanghai');
$user2015=trim(htmlspecialchars($_POST['user']));
$pass2015=trim(htmlspecialchars($_POST['password']));
 
if ($pass2015!="kkdiidiffjKKKGGG9992333"){
   exit();
}

$db=mysql_connect("$db_host","$db_user","$db_pwd");
mysql_query("set names $coding"); 
mysql_select_db("$db_database");
 
 
 
$query="select * from xbmall_users where user_name='$user2015'  order by id desc limit 1";
$re=mysql_query($query,$db);
$data=mysql_fetch_assoc($re);
$user=$data["user"];
     


setcookie("zt_user1",$user,0,"/");
setcookie("is_cj",$data['is_cj'],0,"/");
$msg=$_COOKIE['ECS']['username'].",登录成功！";
$dl="dl".GetRandStr(9);
 
zt_cookie("dl",$dl,time()+86400,"/");
if($data['lx']=="1"){
    header("location:/mall/b_order_list.html");
}else{
    if($data['is_cj']){
        header("location:/mall/xxtz_list.html");
    }else {
        header("location:/mall/center.html");
    }
}
exit();



mysql_close($db); 
?>

