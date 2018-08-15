
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>退出注销系统</title>
<style type="text/css">
<!--
.STYLE1 {color: #FF6600}
body,td,th {
	font-family: 宋体;
	font-size: 12px;
}
-->
</style>
</head>

<body>
<?php
error_reporting(0);
function cookie($var,$value='', $time=0, $path='', $domain=''){ 
$_COOKIE[$var] = $value; 
if(is_array($value)){ 
foreach($value as $k=>$v){ 
setcookie($var.'['.$k.']', $v, $time, $path, $domain, $s); 
} 
}else{ 
setcookie($var, $value, $time, $path, $domain, $s); 
} 
} 
cookie("zt_user2",$user,time()-86400,"./",""); 
cookie("a","",time()+86400,"/"); 
cookie("b","",time()+86400,"/"); 
cookie("c","",time()+86400,"/"); 
cookie("d","",time()+86400,"/"); 
cookie("e","",time()+86400,"/"); 
cookie("f","",time()+86400,"/");
cookie("g","",time()+86400,"/");  
cookie("h","",time()+86400,"/"); 
cookie("i","",time()+86400,"/"); 
cookie("js","",time()+86400,"/"); 
?>
 <table width="336" height="95" border="0" align="center" cellpadding="0" cellspacing="0" style="border:1px #FF9900 solid;margin-top:120px;">
  <tr>
    <td width="351" height="78" align="center" bgcolor="#FFFF99">
	
	
	<img src="images/right.png"><span class="STYLE1">  您已经注销退出本系统，欢迎下次继续使用!</span></td>
  </tr>
</table>
<meta http-equiv="refresh" content="3;url=index.php"> 


<?php

print("<script language='javascript'>window.location.href='index.php'</script>");

?>
</body>
</html>
