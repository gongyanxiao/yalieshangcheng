<?php
  include "config.php" ;
include "myphplib/init.php";
include "myphplib/db.php";
include "myphplib/message.php";
include "config/check.php";
date_default_timezone_set("Asia/Shanghai");
$operateUser=$_COOKIE['ECS']['username'];

session_start();
if($_SESSION['addXXFT']!=null){
	$_SESSION['addXXFT']=null;
}else{//cookie是空的
// 	alertAndRelocation("您已经提交过了, 如再次提交, 请刷新页面", "xxcz_list.html");
}

#$sql="select * from xbmall_users where user_name='{$operateUser}'";
$czyhkh=$_POST['czyhkh'];
$skyhkh=$_POST['skyhkh'];
$czje=$_POST['czje'];
$tjrq = time();//提交时间
$user_bz=$_POST['user_bz'];
 $filePath= saveFile("ping_zheng");
 
$sql="insert into zt_xxtz_cz (czyhkh,skyhkh, user,czje,tjrq,user_bz,ping_zheng)values('{$czyhkh}','{$skyhkh}','{$operateUser}',{$czje},{$tjrq},'{$user_bz}','{$filePath}')";
mysql_query($sql);
alertAndRelocation("充值提交成功,等待审核", "xxtz_cz_list.html");
 
/**
 * 今天最多可以投多少笔
 * @param unknown $fenShu
 */
function  saveFile($file_name){ 
    include "config/zt_class.php";
    $path = "../xxtzuploads/";
    $extArr = array("jpg", "png", "gif");
  
    if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST"){
        $name = $_FILES[$file_name]['name'];
        $size = $_FILES[$file_name]['size'];
        if(empty($name)){
            echo '请选择要上传的图片';
            exit;
        }
        
        $extend = pathinfo($name);
        $ext = strtolower($extend["extension"]);
        
        if(!in_array($ext,$extArr)){
            echo '图片格式错误！';
            exit;
        }
        if($size>(100*102400)){
            echo '图片大小不能超过10M';
            exit;
        }
        $image_name = time().rand(100,999).".".$ext;
        $tmp = $_FILES[$file_name]['tmp_name'];
        $filePath=$path.$image_name;
        var_dump($tmp.":".$filePath);
        if(move_uploaded_file($tmp, $filePath)){
            return $image_name;
        }else{
            die( '上传出错了！');
        }
    }
    
    return  $filePath ;
}
 
// header("Location: xxcz_list.html");  
 
 ?>
  			  