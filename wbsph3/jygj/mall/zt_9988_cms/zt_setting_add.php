<?php
 
 include "../myphplib/db.php";
 include "config/check.php";
date_default_timezone_set("Asia/Shanghai");
$user=$_COOKIE['ECS']['username'];
$name=$_REQUEST['name'];
$value=$_REQUEST['value'];
$comment=$_REQUEST['comment'];
$type=$_REQUEST['type'];
$state=isset($_REQUEST['state'])? $_REQUEST['state']:0;
$id=$_REQUEST['id'];
if($id==null){
	$sql = "insert into zt_setting (name,value,comment,type,state)values('{$name}','{$value}','{$comment}',{$type},0)";
}else{
	$sql = "update zt_setting set name='{$name}',value='{$value}',comment='{$comment}',type={$type},state={$state} where id={$id}";
}
mysql_query($sql);
header("Location: zt_setting_list.php");  
 
 
 ?>
  			  