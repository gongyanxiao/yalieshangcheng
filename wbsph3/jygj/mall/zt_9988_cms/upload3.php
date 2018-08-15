<?php

$path = "uploads/";
include "../config/zt_class.php";
$extArr = array("jpg", "png", "gif");
if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST"){
	$name = $_FILES['photoimg3']['name'];
	$size = $_FILES['photoimg3']['size'];
	
	if(empty($name)){
		echo '请选择要上传的图片';
		exit;
	}
	$ext = extend($name);
	if(!in_array($ext,$extArr)){
		echo '图片格式错误！';
		exit;
	}
	if($size>(100000*1024)){
		echo '图片大小不能超过10000KB';
		exit;
	}
	$image_name = time().rand(100,999).".".$ext;
	$tmp = $_FILES['photoimg3']['tmp_name'];
	if(move_uploaded_file($tmp, $path.$image_name)){
		echo '<img src="'.$path.$image_name.'"  class="preview">';
 $photo='<img src="'.$path.$image_name.'" width=310 height=80>';
$p=addslashes($image_name);
include "../config/zt_config.php";
$db=mysql_connect($db_host,$db_user,$db_pwd);
mysql_query("SET NAMES $coding"); 
mysql_select_db($db_database);
$url3=strip_tags($_POST['url3']);
$sql="update zt_jdt set url3='$url3',pic3='$p' where id='1'";
mysql_query($sql,$db);
	}else{
		echo '上传出错了！';
	}
	exit;
}
exit;
function extend($file_name){
	$extend = pathinfo($file_name);
	$extend = strtolower($extend["extension"]);
	return $extend;
}
?>