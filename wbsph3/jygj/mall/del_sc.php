<?
header("Content-type:text/html;charset=utf-8");
include("config/zt_config.php");
include("config.php");
$link = mysqli_connect($db_host,$db_user,$db_pwd,$db_database) or die('Unale to link');
$link->set_charset('utf8');
if(empty($_COOKIE['ECS']['username'])){
header("Location:/mall/"); 
    exit;
}

$id=htmlspecialchars(trim($_POST['id']));
$query1=$link->query("select * from xbmall_users where user_name='".$_COOKIE['ECS']['username']."'"); 
$r1=$query1->fetch_array();
$sc='';
$sqlb='';
if($r1['lx']==1) {
	$query2=$link->query("select * from zt_shopinfo WHERE `userid`=".$r1['id']);
	$r2=$query2->fetch_array();
	$cpids=explode('|',$r2['sc']);
	if(!in_array($id,$cpids)) {
		echo '<script>alert("您已删除该商品，请勿重复删除！")</script>';
		exit();
	} else {
		//$ak=array_keys($cpids,$id);
		$key = array_search($id, $cpids);
		array_splice($cpids,$key,1);
		//$ncpids=array_remove($cpids,2);
		$sc=implode('|',$cpids);
		$sqlb="UPDATE `zt_shopinfo` SET `sc`='".$sc."' WHERE `userid`=".$r1['id'];
	}
	
} else {
	$query2=$link->query("select * from zt_memberinfo WHERE `userid`=".$r1['id']);
	$r2=$query2->fetch_array();
	$cpids=explode('|',$r2['sc']);
	if(!in_array($id,$cpids)) {
		echo '<script>alert("您已删除该商品，请勿重复删除！")</script>';
		exit();
	} else {
		$key = array_search($id, $cpids);
		array_splice($cpids,$key,1);
		$sc=implode('|',$cpids);
		$sqlb="UPDATE `zt_memberinfo` SET `sc`='".$sc."' WHERE `userid`=".$r1['id'];
	}
}

$link->query($sqlb);
$link->close();
echo '<script>alert("取消收藏成功！")</script>';
echo '<script>location.href="/mall/collection.html";</script>';

?>