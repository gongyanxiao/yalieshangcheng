<?php
$lm2=strip_tags(trim($_POST['three']));
$i1=strip_tags(trim($_GET['id']));
$item2=3;
include "../config/zt_config.php";
include "config/check.php";
$date=date('Y-m-d'); 
if($lm2==""){
print("<script language='javascript'>alert(\"三级栏目不能空!\");</script>");
print("<script language='javascript'>window.location.href='zt_sort_add.php'</script>");
exit();
}
$db =mysql_connect($db_host,$db_user,$db_pwd) or mysqli_connect_errno();
mysql_query("SET NAMES utf8"); 
mysql_select_db($db_database);
$sql="INSERT INTO zt_goods_sort(`id`,`columname`,`item1`,`item2`,`item3`) VALUES (NULL,'$lm2','0','$i1','$item2');";
$query2=mysql_query($sql);
if($query2){
print("<script language='javascript'>alert(\"增加三级栏目信息成功!\");</script>");
print("<script language='javascript'>window.location.href='zt_sort_add.php'</script>");
exit();
}else{
echo "添加信息失败!";
}
?>