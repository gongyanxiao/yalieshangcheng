<?php
header("Content-type:text/html;charset=utf-8");
$lm=strip_tags($_POST['lm']);
$item1=1;
include "../config/zt_config.php";
include "config/check.php";
$db =mysql_connect($db_host,$db_user,$db_pwd);
mysql_query("SET NAMES utf8"); 
mysql_select_db($db_database);
$date=date('Y-m-d'); 
if($lm==""){

print("<script language='javascript'>alert(\"一级栏目不能空!\");</script>");

print("<script language='javascript'>window.location.href='zt_sort_add.php'</script>");

exit();

}

$db =mysql_connect($db_host,$db_user,$db_pwd) or mysqli_connect_errno();
 
mysql_select_db($db_database);
mysql_query("set names utf8");
$sql="INSERT INTO zt_goods_sort(`id`,`columname`,`item1`,`item2`,`item3`) VALUES (NULL ,'$lm','$item1','0','0');";
$query2=mysql_query($sql);
if($query2){
print("<script language='javascript'>alert(\"增加一级栏目信息成功!\");</script>");

print("<script language='javascript'>window.location.href='zt_sort_add.php'</script>");

exit();

}

else{

echo "添加信息失败!";

}

?>