<?php
//普通会员注册处理脚本
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL;
$host = $_SERVER['HTTP_HOST'];
if(substr($referer,7,strlen($host)) != $host){
    echo '非法操作';
    exit();
}
date_default_timezone_set("Asia/Shanghai");
include "../myphplib/db.php";
include "../myphplib/message.php";
include "../config/zt_class.php";
$password1=htmlspecialchars(trim($_POST['password1']));
$password2=htmlspecialchars(trim($_POST['password2']));
$sjhm=htmlspecialchars($_POST['sjhm']);
$xm=htmlspecialchars(trim($_POST['xm']));
//身份证号
$sfzh=htmlspecialchars(trim($_POST['sfzh']));
$xxdz=htmlspecialchars($_POST['xxdz']);
$p=htmlspecialchars(trim($_POST['p']));
$c=htmlspecialchars(trim($_POST['c']));
$a=htmlspecialchars(trim($_POST['a']));
$xy=htmlspecialchars($_POST['xy']);
$tuiJianRen = htmlspecialchars($_POST['tuiJianRen']);
$tuiJianRenAcc = strpos($tuiJianRen,"jy")!==false?$tuiJianRen:"jy".$tuiJianRen;
$username= htmlspecialchars($_POST['user_name']);


//用户名必须以聚元开头+11位数字
if($username=="" or strlen($username)!=13 or  strpos($username,"jy") ===false){
    echo '<script>alert("用户名必须以jy+11位数字")</script>';
    exit();
} else{
    if(!is_numeric(substr($username,2,11))){
        echo '<script>alert("用户名必须以jy+11位数字")</script>';
        exit();
    }
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
// if(!preg_match("/^(13[0-9]|15[012356789]|17[01235678]|18[0-9]|14[579])[0-9]{8}$/",$sjhm)){
// 	echo '<script>alert("手机号码格式不正确!")</script>';
// 	exit();
// }


if(($a=='不限'||$a==''||empty($a))||($p=='不限'||$p==''||empty($p))||($c=='不限'||$c==''||empty($c))){
    echo '<script>alert("注册地区不完整!")</script>';
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


if(empty($tuiJianRen)){
    echo '<script>alert("请重新通过推荐人注册!")</script>';
    exit();
}


$sql="select  user from  xbmall_users where user_name='{$tuiJianRenAcc}'";

$tuiJianRenAcc = getOne($sql);

if(!isset($tuiJianRenAcc)){
    echo "<script>alert('您填写的推荐人{$tuiJianRen}不存在!')</script>";
    exit();
}





//验证用户

$sql = "select  * from `xbmall_users` WHERE `user` = '$username'   order by id desc";
$result= mysql_query($sql);
$num=mysql_num_rows($result);
if($num>=1){
    echo '<script>alert("用户名已被人使用,请更换!")</script>';
    exit();
}
$sql3= "select  * from xbmall_users where sfzh='$sfzh'   ";
$result3= mysql_query($sql3);
$num3=mysql_num_rows($result3);
if($num3>=1){
    echo '<script>alert("此身份证号已被人使用,请更换!")</script>';
    exit();
}


/*if(!empty($sjhm)){
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
$query99.="`sjhm`,`ck`,`a`,`b`,`c`,`xxdz`,`dpmc`,`lxdh`,`zj`,`lx`,`jf`,`xfjf`,`yljf`,`pid`,`parent_id`,`is_cj`)";
$query99.=" VALUES(NULL,'$username','$mm1','$ip','8','','0','','$xm','','0','$date',";
$query99.="'$sfzh','$sjhm','0','$p','$c','$a','$xxdz','','','','0','0','0','0','','$tuiJianRenAcc',1)";
$queryc=mysql_query($query99);
//记录登录日志
$dldq="dl".GetRandStr(9);
$query88="insert into zt_log(`id`,`time`,`ip`,`user`,`dldq`)";
$query88.=" VALUES(NULL,'$date','$ip','$username','$dldq');";
mysql_query($query88);
zt_cookie("dl",$dldq,time()+86400,"/");
if($queryc){
    zt_cookie("zt_user1",$username,time()+86400,"/");
    zt_cookie("is_cj","1",time()+864000,"/");
    alertAndRelocation("注册成功,超级会员名:{$username}", "xxtz_list.html");
}else{
    echo '<script>alert("'.$query99.'")</script>';
    echo '<script>alert("注册出错!")</script>';
}

?>
