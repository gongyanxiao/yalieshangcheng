<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>管理频道分类</title>
<style type="text/css">
<!--
body,td,th {
	font-size: 12px;
}
a:link {
	color: #cccccc;
	text-decoration: none;
}
a:visited {
	color: #cccccc;
	text-decoration: none;
}
a:hover {
	color: #0099FF;
	text-decoration: none;
}
a:active {
	text-decoration: none;
}
-->
</style>
</head>

<body>
<div style="z-index:100:width:100%;height:22px;">

<table width="100%" border="0" height="22px;" cellpadding="0" cellspacing="0">
  <tr>
    <td width="2%" height="22"><img src="images/position1.jpg"></td>
    <td width="98%" align="left" background="images/position2.jpg">您当前的位置>>积分管理&gt;&gt;&nbsp;<a href="zt_goods_sort.php">管理积分分类</a></span></td>
  </tr>
</table>

</div><br>

<table width="68%" height="72" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:20px;">
  <form name="one" method="post" action="zt_sort1_add_pro.php">
  <tr>
    <td height="38" align="center" bgcolor="#FFFFCA" style="border:1px #FF6600 dashed;">
    &nbsp;
    <input name="lm" type="text" id="lm" size="45" style="border:1px #CCCCCC solid;">
    <input type="submit" name="Submit" value="添加积分栏目" style="background-color:#F63;color:#FFF;border:0px;line-height:14px;height:22px;"></td>
  </tr>
  </form>
  <tr>
    <td height="34"><br><?php
include "../config/zt_config.php";
$db =mysql_connect($db_host,$db_user,$db_pwd);
mysql_query("SET NAMES utf8"); 
mysql_select_db($db_database);
$sql12="SELECT * FROM zt_jifen_sort where item1=1";
$re1=mysql_query($sql12,$db);
$num=mysql_num_rows($re1);
$sql2="SELECT * FROM zt_jifen_sort where item1=1";
echo '<ul style="margin:0px;padding:0px;width:650px;">';
$ref=mysql_query($sql2,$db);
for($i=0;$i<$num;$i++){
$data=mysql_fetch_array($ref);
echo '<li style="line-height:20px;">'.$data['id']."&nbsp;&nbsp;<b><font color=red>".$data['columname'].'</font></b>&nbsp;&nbsp;&nbsp;&nbsp;';
echo '<a href="zt_classdel1.php?id='.$data['id'].'">[删除]</a>';



}
echo '</ul>';
mysql_close($db);

?></td>
  </tr>
</table>



</body>
</html>
