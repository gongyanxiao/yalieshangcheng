<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>修改密码</title>
</head>

<body>
<?php
if($_COOKIE['a']<>"1"){
exit();
}
include_once"config/check.php";

$password=htmlspecialchars($_POST['password']);
$password2=htmlspecialchars($_POST['password2']);


if(strlen($password)<3){


?>
 <table width="336" height="81" border="0" align="center" cellpadding="0" cellspacing="0" style="border:1px #FF9900 solid;margin-top:85px;">
  <tr>
    <td width="351" height="79" align="center" bgcolor="#FFFF99">
	
	
	<img src="images/error.png"> <span class="STYLE1">密码位数太短！</span></td>
  </tr>
</table>
<meta http-equiv="refresh" content="3;url=pass_mod.php"> 

<?php
exit();
}
if($password2!==$password){
?>
<table width="336" height="81" border="0" align="center" cellpadding="0" cellspacing="0" style="border:1px #FF9900 solid;margin-top:85px;">
  <tr>
    <td width="351" height="79" align="center" bgcolor="#FFFF99">
	
	
	<img src="images/error.png"> <span class="STYLE1">两次输入的密码不一致！</span></td>
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
$user=htmlspecialchars(trim($_POST['yh']));
$user=iconv('GBK','UTF-8',$user);
function do_hash($password){
$salt ='zocabef88332342949fdsafagfdgv43532ju76jMfasdfa2349203408242042';   
 return md5($password.$salt); 
}
$pa=trim(do_hash($password));
$query="update xbmall_users set  pass='$pa' where user='$user'";
$re=mysql_query($query,$db);
 if($re){

?>
 <table width="336" height="81" border="0" align="center" cellpadding="0" cellspacing="0" style="border:1px #FF9900 solid;margin-top:85px;">
  <tr>
    <td width="351" height="79" align="center" bgcolor="#FFFF99">
	
	
	<img src="images/error.png"> <span class="STYLE1">修改密码成功！</span></td>
  </tr>
</table>
<meta http-equiv="refresh" content="3;url=pass_mod.php"> 



<?php


  }

?>


</body>
</html>

</div>
</body>
</html>
