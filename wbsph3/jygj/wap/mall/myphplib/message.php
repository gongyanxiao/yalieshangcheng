<?php


$dirPath = str_replace('\\', '/', dirname(__FILE__));
$path = substr($dirPath, 0,strrpos($dirPath,"/"));//根路径
// include $path."/config/zt_config.php";

// $db = mysql_connect("$db_host","$db_user","$db_pwd");
// mysql_query("set names $coding");
// mysql_select_db("$db_database");


function  alert($message ){
	 
	echo "<script> alert('".$message."');";
	echo "</script>";
	die;
}

function  alertAndRelocation($message,$href){
	echo "<script> alert('".$message."');";//用户点确定后, 转到另一个页面
	echo "self.window.location.href='{$href}';";
	echo "</script>";
	die;//后面的程序不会再执行
}

function  alertAndRelocationHistory($message){
	echo "<script> alert('".$message."');";//用户点确定后, 转到另一个页面
	echo "self.window.location.href='".$_SERVER['HTTP_REFERER']."';";
	echo "</script>";
	die;//后面的程序不会再执行
}
?>