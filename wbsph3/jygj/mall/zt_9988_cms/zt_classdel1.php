<?php
header("Content-type:text/html;charset=utf-8");
$text=strip_tags($_GET['id']);
include "../config/zt_config.php";
include "config/check.php";
$db =mysql_connect($db_host,$db_user,$db_pwd);
mysql_query("SET NAMES utf8"); 
mysql_select_db($db_database);
$query="delete  from zt_jifen_sort where id='$text'";
$re=mysql_query($query,$db);
if($re){

print("<script language='javascript'>alert(\"删除分类成功!\");</script>");

print("<script language='javascript'>window.location.href='zt_sort1_add.php'</script>");

exit();

}

mysql_close($db);

?>