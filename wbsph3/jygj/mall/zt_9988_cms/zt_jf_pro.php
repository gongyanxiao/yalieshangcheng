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
.STYLE1 {
	color: #FF0000;
	font-weight: bold;
}
</style>
<script type="text/javascript" language="javascript"> 
<!-- 
function confirmAct() 
{ 
    if(confirm('确定要执行此操作吗?')) 
    { 
        return true; 
    } 
    return false; 
} 
//-->
</script> 
</head>

<body>


<table width="100%" border="0" height="22px;" cellpadding="0" cellspacing="0">
  <tr>
    <td width="2%" height="22"><img src="images/position1.jpg"></td>
    <td width="98%" align="left" background="images/position2.jpg">您当前的位置>><span class="STYLE1">以下是赠送的明细，请仔细检查后慎重操作！</span></td>
  </tr>
</table>


<?php


session_start(); 
$_SESSION['uniqid1'] = md5(uniqid('jygj',true));


if($_COOKIE['i']<>"1"){
exit();
}
//积分赠送批量操作
include "../config/zt_config.php";
date_default_timezone_set("Asia/Shanghai");
$db=mysql_connect($db_host,$db_user,$db_pwd);
mysql_query("SET NAMES $coding"); 
mysql_select_db($db_database);
$sql1="select * from zt_ed  order by id desc";
	$q1=mysql_query($sql1);
	$ss=mysql_fetch_assoc($q1);
	$jf1=$ss['jf1'];
	$jf2=$ss['jf2'];
	$jf3=$ss['jf3'];
//提醒是否确定赠送额定是否正确
 $week=date("w");



?>

  


<table width="548" height="227" border="0" align="center" style="border:1px #FF3300  dashed;margin-top:58px;">
  <tr>
    <td align="center" valign="middle" bgcolor="#FFFF99"><table width="449" height="171" border="0">
      <tr>
        <td width="129" align="center"><span class="STYLE1">赠送日期：</span></td>
        <td width="310" style="font-size:14px;"><?php 
$weekarray=array("日","一","二","三","四","五","六");
echo date("Y-m-d H:i:s")." 星期".$weekarray[date("w")];
?></td>
      </tr>
      <tr>
        <td align="center"><span class="STYLE1">项目分类：</span></td>
        <td><?php
		 $week=date("w");
//可提现部分
if($week>='1' and  $week<='5'){
echo "周一~周五可兑现积分赠送!";
}
if($week=='6'){
echo "周六消费兑换积分赠送!";

}
if($week=='0'){
echo "养老教育保险积分赠送!";
}
		?>
		</td>
      </tr>
      <tr>
        <td align="center"><span class="STYLE1">所赠额度：</span></td>
        <td style="font-size:18px; font-family:Arial, Helvetica, sans-serif;color:red;">
		<?php
		if($week>='1' and  $week<='5'){
echo $jf1;
}
if($week=='6'){
echo $jf2;

}
if($week=='0'){
echo $jf3;
}
?>
</td>
      </tr>
      <tr>
        <td align="center">&nbsp;</td>
        <td align="left"><a href="zt_jf_pro3.php?session1=<?=$_SESSION['uniqid1']?>" onclick="return confirmAct();"><img src="images/kszs.png" border="0"/></a></td>
      </tr>
    </table></td>
  </tr>
</table>
</body>
</html>
