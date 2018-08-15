<?
error_reporting(0);
header("Content-type:text/html;charset=utf-8");
include("config.php");
$link = mysqli_connect($db_host,$db_user,$db_pwd,$db_database) or die('Unale to link');
$link->set_charset('utf8');
$user=$_COOKIE['ECS']['username'];
if(empty($user)){
header("Location:/wap/mall/wap-shop.html"); 
    exit();
}

$id=htmlspecialchars(trim($_POST['id']));
$type=htmlspecialchars(trim($_POST['type']));
$url='';
$sql0="SELECT * from `xbmall_users` where `user`='".$user."'";
$query0=$link->query($sql0); 
$r0=$query0->fetch_array();
if($r0['lx']==1) {
	$url="/wap/mall/dhjl.html";
} else {
	$url="/wap/mall/dhjl.html";
}
$sql1="SELECT * from `zt_dhsp` where `user`='".$user."'";
$query1=$link->query($sql1); 

$c=array();
while ( $r1=$query1->fetch_array()) {
	$c[]=$r1['id'];
}
if(!in_array($id,$c)) {
	echo '<script>alert("您已删除该条记录，请勿重复删除！")</script>';
	echo '<script>location.href="'.$url.'";</script>';
	exit();
}
$sql2=$sql1." and id='$id' ";
$query2=$link->query($sql2);
$r2=$query2->fetch_array();

$sqlb='';
if($type==1) {
	$sqlb="DELETE FROM `zt_dhsp` WHERE `id`='".$id."'";
} elseif($type==2) {
	if($r2['zt']=='已签收') {
		echo '<script>alert("您已签收，请勿重复操作！")</script>';
		echo '<script>location.href="'.$url.'";</script>';
		exit();
	} elseif ($r2['zt']=='待发货') {
		echo '<script>alert("请在买家发货之后进行签收！")</script>';
		echo '<script>location.href="'.$url.'";</script>';
		exit();
	} else {
		$sqlb="UPDATE `zt_dhsp` SET `zt`='已签收' WHERE `id`='".$id."'";
	}
}

$link->query($sqlb);
$link->close();
if($type==1) {
	echo '<script>alert("删除兑换订单成功！")</script>';
} elseif($type==2) {
	echo '<script>alert("签收成功！")</script>';
}
echo '<script>location.href="'.$url.'";location.reload(true);</script>';

?>