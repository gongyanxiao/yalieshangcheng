<?
header("content-type:text/html;charset:utf-8");
include("config/zt_config.php");
include("config.php");
include("page.class.php");
$link = mysqli_connect($db_host,$db_user,$db_pwd,$db_database) or die('Unale to link');
$link->set_charset('utf8');
session_start(); 
$user=htmlspecialchars(trim($_POST['zt_user1']));
$session=htmlspecialchars(trim($_POST['session']));
$shr=htmlspecialchars(trim($_POST['shr']));
$a=htmlspecialchars(trim($_POST['a']));
$b=htmlspecialchars(trim($_POST['b']));
$c=htmlspecialchars(trim($_POST['c']));
$xxdz=htmlspecialchars(trim($_POST['xxdz']));
$lxdh=htmlspecialchars(trim($_POST['lxdh']));
$sfmr=htmlspecialchars(trim($_POST['sfmr']));
if($sfmr<>1) {
    $sfmr=0;
}

$id=htmlspecialchars(trim($_GET['id']));
$type=htmlspecialchars(trim($_GET['type']));
$sql0="select * from zt_shdz where user='$user'";
$qry0=$link->query($sql0);
$row=$qry0->num_rows;
if(!empty($shr)) {

$sql1="select * from zt_shdz where shr='$shr'";
$qry1=$link->query($sql1);
$r1=$qry1->fetch_array();
if((($r1[shr]==$shr)&&($r1[a]==$a)&&($r1[b]==$b)&&($r1[c]==$c)&&($r1[xxdz]==$xxdz)&&($r1[lxdh]==$lxdh)&&($r1[sfmr]==$sfmr))||($session!=$_SESSION['uniqid'])) {
    print("<script language='javascript'>window.location.href='/mall/b_address.html';</script>");
    exit();
} else {
	 unset($_SESSION['uniqid']);
}
}
$sql3='';
if(empty($_POST)&&empty($_GET)) {
    $sql3='';
} elseif(empty($id)) {
    if($sfmr==1&&$row>0) {
        $sql1="UPDATE `zt_shdz` SET `sfmr`='0' where  user='$user'";
        $link->query($sql1);
    }
    $sql3="INSERT INTO `zt_shdz`(`id`, `user`, `shr`, `a`, `b`, `c`, `xxdz`, `lxdh`, `sfmr`, `yl`) VALUES (null,'$user','$shr','$a','$b','$c','$xxdz','$lxdh','$sfmr','')";
    unset($_POST);
    unset($_GET);
} else {
    if($type==1) {
         if($sfmr==1&&$row>1) {
        $sql1="UPDATE `zt_shdz` SET `sfmr`='0' where user='$user'";
        $link->query($sql1);
    }
        $sql3="UPDATE `zt_shdz` SET `user`='$user',`shr`='$shr',`a`='$a',`b`='$b',`c`='$c',`xxdz`='$xxdz',`lxdh`='$lxdh',`sfmr`='$sfmr',`yl`='' WHERE id='$id'";
        unset($_POST);
        unset($_GET);
    } else {
        $sql3="DELETE FROM `zt_shdz` WHERE id='$id'";
        unset($_POST);
        unset($_GET);
    }
}
$link->query($sql3);
$link->close();
print("<script language='javascript'>window.location.href='/mall/b_address.html';</script>");
?>