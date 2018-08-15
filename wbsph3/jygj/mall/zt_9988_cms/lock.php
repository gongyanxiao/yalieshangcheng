<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>锁定用户完成</title>
</head>

<body>
<?php
include "../config/check.php";
$pid=htmlspecialchars(trim($_GET['pid']));
$p=htmlspecialchars(trim($_GET['p']));
if(strlen($pid)<3){


?>
 <table width="336" height="81" border="0" align="center" cellpadding="0" cellspacing="0" style="border:1px #FF9900 solid;margin-top:85px;">
  <tr>
    <td width="351" height="79" align="center" bgcolor="#FFFF99">
	
	
	<img src="images/error.png"> <span class="STYLE1">用户名不能为空！</span></td>
  </tr>
</table>
<meta http-equiv="refresh" content="3;url=pass_mod.php"> 

<?php
exit();
}
include "../config/zt_config.php";
$db =mysql_connect($db_host,$db_user,$db_pwd);
mysql_query("set names $coding");
mysql_select_db($db_database);
$query="update xbmall_users set  state='$p' where user='$pid'";
$re=mysql_query($query,$db);
 if($re){

?>
 <table width="336" height="81" border="0" align="center" cellpadding="0" cellspacing="0" style="border:1px #FF9900 solid;margin-top:85px;">
  <tr>
    <td width="351" height="79" align="center" bgcolor="#FFFF99">
	
	
	<img src="images/error.png"> <span class="STYLE1">操作用户完成！</span></td>
  </tr>
</table>
<meta http-equiv="refresh" content="3;url=zt_user_list.php"> 



<?php


  }

?>


</body>
</html>

</div>
</body>
</html>
