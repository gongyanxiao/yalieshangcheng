<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<style type="text/css">
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
body,td,th {
	font-size: 12px;
	color: #666;
}
</style>
</head>

<body>
<table width="100%" border="0" cellpadding="0" cellspacing="1">
  <tr>
    <td width="356" height="26" align="center" bgcolor="#FFCC00">项目分类</td>
    <td width="360" align="center" bgcolor="#CCCCCC">赠送日期</td>
    <td width="479" align="center" bgcolor="#FFCC66">赠送积分</td>
    <td width="371" align="center" bgcolor="#CCCCCC">用户</td>
  </tr>
</table>
 <?php
 include "../config/zt_config.php";
$db=mysql_connect($db_host,$db_user,$db_pwd);
mysql_query("SET NAMES $coding"); 
mysql_select_db($db_database);
$q="select * from zt_jf_record  order by id desc";
$r=mysql_query($q,$db);
$num=mysql_num_rows($r);
for($i=0;$i<$num;$i++){
$data2=mysql_fetch_array($r);
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="358" height="26" align="center" style="color:blue;"><?=$data2["xmfl"];?></td>
    <td width="363" align="center"><?=$data2["date"];?></td>
    <td width="477" align="center"><?=$data2["jf"];?></td>
    <td width="373" align="center" style="color: #F00;"><?=$data2["user"];?></td>
  </tr>
</table>
<?php
  }
  mysql_close($db);
  ?>  
</body>
</html>
