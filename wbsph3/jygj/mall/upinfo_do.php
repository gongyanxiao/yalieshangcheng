<?
header("Content-type:text/html;charset=utf-8");
include("config/zt_config.php");
include("config/zt_class.php");
include("config.php");
$link = mysqli_connect($db_host,$db_user,$db_pwd,$db_database) or die('Unale to link');
$link->set_charset('utf8');
if(empty($_COOKIE['ECS']['username'])){
header("Location:/mall/"); 
    exit();
}

$lxdh=htmlspecialchars(trim($_POST['lxdh']));
$tx=htmlspecialchars(trim($_COOKIE['tx']));
$yx=htmlspecialchars(trim($_POST['yx']));
$xm=htmlspecialchars(trim($_POST['xm']));
$xxdz=htmlspecialchars(trim($_POST['xxdz']));
$dpmc=htmlspecialchars(trim($_POST['dpmc']));
$a=htmlspecialchars(trim($_POST['a']));
$b=htmlspecialchars(trim($_POST['b']));
$c=htmlspecialchars(trim($_POST['c']));
$jj=$_POST['jj'];
$dt=$_POST['dt'];

$cpid=htmlspecialchars(trim($_POST['cpid']));

$sc='';
$stmt=='';
$query1=$link->query("select * from xbmall_users where user_name='".$_COOKIE['ECS']['username']."'"); 
$r1=$query1->fetch_array(); 
if($a=='省份'||$b=='地级市'||$c=='市、县级市') {
	$stmt=$link->prepare("UPDATE `xbmall_users` SET `dpmc`=?,`lxdh`=?,`xxdz`=?,`xm`=? WHERE `id`=?");  
	$stmt->bind_param('ssssi',$dpmc,$lxdh,$xxdz,$xm,$r1['id']); 
} else {
	$stmt=$link->prepare("UPDATE `xbmall_users` SET `dpmc`=?,`lxdh`=?,`xxdz`=?,`xm`=?,`a`=?,`b`=?,`c`=? WHERE `id`=?");  
	$stmt->bind_param('sssssssi',$dpmc,$lxdh,$xxdz,$xm,$a,$b,$c,$r1['id']); 
}
if(empty($cpid)) {
	$stmt->execute(); 
	$stmt->close();
}

$type=htmlspecialchars(trim($_GET['type']));
$sqlb='';
if($type==1) {
	/*$stmt1=$link->prepare("UPDATE `zt_shopinfo` SET `tx`=?,`yx`=?,`jj`=?,`dt`=? WHERE `userid`=?");
	$stmt1->bind_param('ssbbi',$_POST['tx'],$_POST['yx'],$_POST['jj'],$_POST['dt'],$r1['id']);  
	$stmt1->execute();
	echo $stmt1->affected_rows;
	$stmt1->close();*/
	$query2=$link->query("select * from zt_shopinfo where userid=".$r1['id']); 
	$r2=$query2->fetch_array(); 
	if($r2['sc']=='') {
		$sc=$cpid;
	} else {
		$cpids=explode('|',$r2['sc']);
		if(in_array($cpid,$cpids)) {
				echo '<script>alert("您已收藏过该商品，请勿重复收藏！")</script>';
echo '<script>location.href="/mall/cpcontent.html?cpid='.$cpid.'";</script>';
exit();
		} 
		$sc=$r2['sc'].'|'.$cpid;
	}
	if(empty($cpid)) {
		$sqlb="UPDATE `zt_shopinfo` SET `tx`='".$tx."',`yx`='".$yx."',`jj`='".$jj."',`dt`='".$dt."' WHERE `userid`=".$r1['id'];
		if($tx=='') {
			$sqlb="UPDATE `zt_shopinfo` SET `yx`='".$yx."',`jj`='".$jj."',`dt`='".$dt."' WHERE `userid`=".$r1['id'];
		}
	} else {
		$sqlb="UPDATE `zt_shopinfo` SET `sc`='".$sc."' WHERE `userid`=".$r1['id'];
	}
} else {
	if(empty($cpid)) {
		$sqlb="INSERT INTO `zt_shopinfo` (`id` ,`userid` ,`tx` ,`yx` ,`jj` ,`dt` ,`dz`,`sc`) VALUES (null,'".$r1['id']."','".$tx."','".$yx."','".$jj."','".$dt."','','')";
	} else {
		$sqlb="INSERT INTO `zt_shopinfo` (`id` ,`userid` ,`tx` ,`yx` ,`jj` ,`dt` ,`dz`,`sc`) VALUES (null,'".$r1['id']."','','','','','','".$cpid."')";
	}
}
$link->query($sqlb);
$link->close();

if(empty($cpid)) {
echo '<script>alert("修改成功！")</script>';
zt_cookie("tx","","0","/");
echo '<script>location.href="upinfor.html";</script>';
}else {
	echo '<script>alert("收藏成功！")</script>';
echo '<script>location.href="/mall/cpcontent.html?cpid='.$cpid.'";</script>';
}
?>