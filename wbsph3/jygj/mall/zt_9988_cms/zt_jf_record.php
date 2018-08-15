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
<table width="480" border="0" cellpadding="0" cellspacing="1">
  <tr>
    <td width="131" height="26" align="center" bgcolor="#FFCC00">订单编号</td>
    <td width="96" align="center" bgcolor="#CCCCCC">操作日期</td>
    <td width="85" align="center" bgcolor="#FFCC66">状态</td>
    <td width="102" align="center" bgcolor="#CCCCCC">备注</td>
    <td width="80" align="center" bgcolor="#FFCC00">操作员</td>
  </tr>
</table>
 <?php
 include "../config/zt_config.php";
$db=mysql_connect($db_host,$db_user,$db_pwd);
mysql_query("SET NAMES $coding"); 
mysql_select_db($db_database);
$sid=htmlspecialchars($_GET["sid"]);
$q="select * from zt_jf_record where ddbh='$sid'  order by id desc";
$r=mysql_query($q,$db);
$num=mysql_num_rows($r);
for($i=0;$i<$num;$i++){
$data2=mysql_fetch_array($r);
?>
<table width="480" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="132" height="26" align="center" style="color:blue;"><?=$data2["ddbh"];?></td>
    <td width="96" align="center"><?=$data2["date"];?></td>
    <td width="85" align="center"><?
if($data2["jf"]=="2"){
echo "已审核";
}else{
echo "未通过";
};

?></td>
    <td width="105" align="center"><?=$data2["bz"];?></td>
    <td width="82" align="center" style="color: #F00;"><?=$data2["user"];?></td>
  </tr>
</table>
<?php
  }
  mysql_close($db);
  ?>  
</body>
</html>
