<?
header("Content-type:text/html;charset=utf-8");
include("config/zt_config.php");
include("config.php");
include("config/zt_class.php");
$link = mysqli_connect($db_host,$db_user,$db_pwd,$db_database) or die('Unale to link');
$link->set_charset('utf8');
if(empty($_COOKIE['ECS']['username'])){
header("Location:/mall/"); 
    exit();
}
$xm=htmlspecialchars(trim($_POST['xm']));
$tx=htmlspecialchars(trim($_COOKIE['tx']));
$yx=htmlspecialchars(trim($_POST['yx']));
$sex=htmlspecialchars(trim($_POST['sex']));
$xxdz=htmlspecialchars(trim($_POST['xxdz']));
$cpid=htmlspecialchars(trim($_POST['cpid']));
$a=htmlspecialchars(trim($_POST['a']));
$b=htmlspecialchars(trim($_POST['b']));
$c=htmlspecialchars(trim($_POST['c']));

$query1=$link->query("select * from xbmall_users where user_name='".$_COOKIE['ECS']['username']."'"); 
$r1=$query1->fetch_array(); 
$sqla='';
if(empty($cpid)) {
	if($a=='省份'||$b=='地级市'||$c=='市、县级市') {
		$sqla="UPDATE `xbmall_users` SET `xm`='$xm',`xxdz`='$xxdz' WHERE `id`=".$r1['id'];
	} else {
		$sqla="UPDATE `xbmall_users` SET `xm`='$xm',`xxdz`='$xxdz',`a`='$a',`b`='$b',`c`='$c' WHERE `id`=".$r1['id'];
	}
	$link->query($sqla);
} 

$type=htmlspecialchars(trim($_GET['type']));
$sqlb='';
if($type==1) {
	/*$stmt1=$link->prepare("UPDATE `zt_shopinfo` SET `tx`=?,`yx`=?,`jj`=?,`dt`=? WHERE `userid`=?");
	$stmt1->bind_param('ssbbi',$_POST['tx'],$_POST['yx'],$_POST['jj'],$_POST['dt'],$r1['id']);  
	$stmt1->execute();
	echo $stmt1->affected_rows;
	$stmt1->close();*/
	$query2=$link->query("select * from zt_memberinfo where userid=".$r1['id']); 
	$r2=$query2->fetch_array(); 
	if($r2['sc']=='') {
		$sc=$cpid;
	} else {
		$cpids=explode('|',$r2['sc']);
		if(in_array($cpid,$cpids)) {
				echo '<script>alert("您已收藏过该商品，请勿重复收藏！")</script>';
echo '<script>location.href="/wap/mall/cpcontent.html?cpid='.$cpid.'";</script>';
exit();
		} 
		$sc=$r2['sc'].'|'.$cpid;
	}
	if(empty($cpid)) {
		$sqlb="UPDATE `zt_memberinfo` SET `tx`='".$tx."',`yx`='".$yx."',`xb`='".$sex."' WHERE `userid`=".$r1['id'];
		if($tx=='') {
			$sqlb="UPDATE `zt_memberinfo` SET `yx`='".$yx."',`xb`='".$sex."' WHERE `userid`=".$r1['id'];
		}
	} else {
		$sqlb="UPDATE `zt_memberinfo` SET `sc`='".$sc."' WHERE `userid`=".$r1['id'];
	}
	
} else {
	
	if(empty($cpid)) {
		$sqlb="INSERT INTO `zt_memberinfo` (`id`, `userid`, `xb`, `tx`, `yx`, `shdz`, `sc`) VALUES (NULL,'".$r1['id']."','".$sex."','".$tx."','".$yx."','','')"; 
	} else {
		$sqlb="INSERT INTO `zt_memberinfo` (`id`, `userid`, `xb`, `tx`, `yx`, `shdz`, `sc`) VALUES (null,'".$r1['id']."','','','','','".$cpid."')";
	}
}
$link->query($sqlb);
$link->close();
if(empty($cpid)) {
echo '<script>alert("修改成功！")</script>';
zt_cookie("tx","","0","/");
echo '<script>location.href="center.html";</script>';
}else {
	echo '<script>alert("收藏成功！")</script>';
echo '<script>location.href="/wap/mall/cpcontent.html?cpid='.$cpid.'";</script>';
}
?>