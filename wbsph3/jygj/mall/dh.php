<?
header("content-type:text/html;charset:utf-8");
include("config/zt_config.php");
include("config/zt_class.php");
include("config.php");
date_default_timezone_set("Asia/Shanghai");
$date=date("Y-m-d H:i:s");
$link = mysqli_connect($db_host,$db_user,$db_pwd,$db_database) or die('Unale to link');
$link->set_charset('utf8');
session_start(); 
$session=htmlspecialchars(trim($_POST['session']));
$user=htmlspecialchars(trim($_POST['mid']));
$ujf=htmlspecialchars(trim($_POST['ujf']));
$price=htmlspecialchars(trim($_POST['price']));
$kc=htmlspecialchars(trim($_POST['kc']));
$num=htmlspecialchars(trim($_POST['num']));
$jfid=htmlspecialchars(trim($_POST['jfid']));
$shxx=htmlspecialchars(trim($_POST['shxx']));
//验证
if($session<>$_SESSION['uniqid']) {
   	
print("<script language='javascript'>location.replace(document.referrer); </script>");
    exit();
} else {
	 unset($_SESSION['uniqid']);
}
	
if(empty($kc)||(empty($shxx)||$shxx=='')||(empty($num)||$num<1)||(empty($price)||$price<0)||$price*$num>$ujf) {
	echo "<script language='javascript'>alert('此商品库存已为零，请选择其它商品进行兑换！');</script>";
print("<script language='javascript'>location.replace(document.referrer); </script>");
	exit();
}
if($kc<=0) {
	echo "<script language='javascript'>alert('此商品库存已为零，请选择其它商品进行兑换！');</script>";
	
print("<script language='javascript'>location.replace(document.referrer); </script>");
	exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title></title>
<meta name="description" content="">
<meta name="keywords" content="">
<link href="" rel="stylesheet">
</head>
<body>
<?
$sj=time();
$zj=$price*$num;
$zj1=-1*$price*$num;
$bh='jydd'.$sj.GetRandStr(5);
//更新user表
$sql0="select * from xbmall_users where user_name='$user'";
$qry0=$link->query($sql0);
$r0=$qry0->fetch_array();
$xfjf=$r0['xfjf']-$zj;
$sql01="UPDATE `xbmall_users` set xfjf='$xfjf' where user_name='$user'";
$qry01=$link->query($sql01);
//更新zt_dhsp表
$bz1="兑换商品扣除".$zj."消费积分";
$sql3="INSERT INTO `zt_dhsp`(`id`, `xmfl`, `date`, `jf`, `jfid`, `sl`, `shxx`, `kd`, `zt`, `bz`, `user`, `bh`) VALUES (null,'兑换商品','$date','$zj','$jfid','$num','$shxx','','待发货','$bz1','$user','$bh')";
//更新zt_jf_record表
$sql10="select id from zt_dhsp where bh='$bh'";
$qry10=$link->query($sql10);
$r10=$qry10->fetch_array();
$jfbh=$r10['id'];
$bz="兑换商品扣除".$zj;
$sql2="INSERT INTO  `zt_jf_record`(`id`,`xmfl`,`date`,`jf`,`bz`,`user`,`bh`) VALUES(null,'兑换商品','$date','$zj1','$bz','$user','$jfbh');";
$qry2=$link->query($sql2);

$qry3=$link->query($sql3);
//更新jifen表
$sql4="select * from zt_jifen where id='$jfid'";
$qry4=$link->query($sql4);
$r4=$qry4->fetch_array();
$xl=$r4['spxl']+$num;
$kc=$r4['kc']-$num;
$sql41="UPDATE `zt_jifen` set spxl='$xl',kc='$kc' where id='$jfid'";
$qry41=$link->query($sql41);




$link->close();
echo "<script language='javascript'>alert('兑换成功，可在兑换记录中查看！');</script>";

print("<script language='javascript'>location.replace(document.referrer); </script>");
?>
</body>
</html>