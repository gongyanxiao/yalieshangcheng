<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>修改密码设置</title>
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	overflow:hidden;margin:0px;
	scrollbar-face-color:#DAECFA;//轴面颜色 scrollbar-arrow-color:#FFFFFF;//箭头颜色 scrollbar-track-color:#DAECFA;//轴轨道颜色 scrollbar-highlight-color:#FFFFFF;//左立体边颜色 scrollbar-3dlight-color:#D1D7DC;//轴面左边角颜色 scrollbar-shadow-color:#DAECFA; //右立体边颜色 scrollbar-darkshadow-color:#DAECFA;//轴面右边角颜色
}
//body,td,th {
	font-family: 宋体;
	font-size: 12px;
}
body,td,th {
	font-size: 12px;
	font-family: 宋体;
	color: #333333;
}
.btdx {
	color: #0085B6;
	font-weight: bold;
}
.jbsrk{
border:1px #DFDFDF solid;
margin-left:5px;
height:22px;
background-image:url(images/srkbg.jpg);
font-size:14px;
color:#535353;
text-indent:10px;
height:20px;
}
.err{color:#ff0000;}
.ok{color:#009900;}
.jbsrk1 {border:1px #DFDFDF solid;
margin-left:5px;
height:22px;
background-image:url(images/srkbg.jpg);
font-size:14px;
color:#535353;
text-indent:10px;
height:20px;
}
.jbsrk11 {border:1px #DFDFDF solid;
margin-left:5px;
height:22px;
background-image:url(images/srkbg.jpg);
font-size:14px;
color:#535353;
text-indent:10px;
height:20px;
}
.jbsrk2 {border:1px #DFDFDF solid;
margin-left:5px;
height:22px;
background-image:url(images/srkbg.jpg);
font-size:14px;
color:#535353;
text-indent:10px;
height:20px;
}
.jbsrk3 {border:1px #DFDFDF solid;
margin-left:5px;
height:22px;
background-image:url(images/srkbg.jpg);
font-size:14px;
color:#535353;
text-indent:10px;
height:20px;
}
.jbsrk21 {border:1px #DFDFDF solid;
margin-left:5px;
height:22px;
background-image:url(images/srkbg.jpg);
font-size:14px;
color:#535353;
text-indent:10px;
height:20px;
}
.jbsrk31 {border:1px #DFDFDF solid;
margin-left:5px;
height:22px;
background-image:url(images/srkbg.jpg);
font-size:14px;
color:#535353;
text-indent:10px;
height:20px;
}
.jbsrk4 {border:1px #DFDFDF solid;
margin-left:5px;
height:22px;
background-image:url(images/srkbg.jpg);
font-size:14px;
color:#535353;
text-indent:10px;
height:20px;
}

-->
</style>
<script type="text/javascript" src="js/jquery.min.js"></script>

</head>

<body>
<div id="jygj"></div>
<div style="z-index:100:width:100%;">

<?php
if($_COOKIE['a']<>"1"){
exit();
}
include "config/check.php";
include "../config/zt_config.php";
$db =mysql_connect($db_host,$db_user,$db_pwd);
mysql_query("set names $coding");
mysql_select_db($db_database);
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="2%" height="29"><img src="images/position1.jpg"></td>
    <td width="98%" align="left" background="images/position2.jpg">您当前的位置:  系统设置 &gt;&gt; 修改密码设置</td>
  </tr>
</table>

</div>

<table width="99%" height="451" border="1" align="center" cellspacing="0" bordercolorlight="#94D0EA" bordercolordark="#F5FBFE" style="margin-top:50px;">

<form  action="pmod.php" method="post" name="myform" id='myform'>
<tr bgcolor=#cccccc>
    
	
  <input type="hidden" id="pid" value="<?=htmlspecialchars(trim($_GET['pid']))?>" name='pid'>
    <td height="32" colspan="4" align="center" bgcolor="#DAECFA" class="btdx">修改密码操作面板</td>
  </tr>

<tr>
    <td width="14%" height="26" align="center" bgcolor="#DAECFA" class="btdx">用户名</td>
    <td height="26" colspan="3" align="left" bgcolor="#FFFFFF"><input name="yh" type="text" id="yh"  class="jbsrk31" value="<?php 
	if(htmlspecialchars($_GET['pid'])!==""){
	echo htmlspecialchars($_GET['pid']);
	}else{
	echo $_COOKIE['zt_user2'];
	};
	 ?>"/>
   
      (输入要修改密码的相关用户名)</td>
</tr>
<tr bgcolor=#cccccc>
  <td height="26" align="center" bgcolor="#DAECFA" class="btdx">密码</td>
  <td height="26" colspan="3" align="left" bgcolor="#FFFFFF"><input name="password" type="password" id="password"  class="jbsrk3"/>     </td>
</tr>
<tr bgcolor=#cccccc>
  <td height="36" align="center" bgcolor="#DAECFA" class="btdx">确认密码</td>
  <td height="36" colspan="3" align="left" bgcolor="#FFFFFF"><input name="password2" type="password" id="password2"  class="jbsrk"/>      </td>
</tr>
<tr bgcolor=#cccccc>
  <td height="32" align="center" bgcolor="#DAECFA" class="btdx">个人手机号码</td>
  <td height="32" colspan="3" align="left" bgcolor="#FFFFFF"><input name="grsjhm" type="text" id="grsjhm"  class="jbsrk4"/></td>
</tr>
<?
if($_COOKIE['zt_user2']=='admin') {
?>
<tr bgcolor=#cccccc>
    
    <td height="32" colspan="4" align="center" bgcolor="#DAECFA" class="btdx">修改身份证号操作面板</td>
  </tr>
<tr bgcolor=#cccccc>
  <td height="30" align="center" bgcolor="#DAECFA" class="btdx">新身份证号</td>
  <td height="30" colspan="3" align="left" bgcolor="#FFFFFF"><input name="sfzh" type="text" id="sfzh"  class="jbsrk"/>      </td>
</tr>
<?
$user=htmlspecialchars($_GET['pid']);
if($user!==""){
  $query0="SELECT * from zt_bind_bank where ssyh='$user'";
$re0=mysql_query($query0,$db);

?>

<tr bgcolor=#cccccc>
    
  
    <td height="32" colspan="4" align="center" bgcolor="#DAECFA" class="btdx">修改银行卡信息操作面板<font style="color: red;font-weight: bold;">（谨慎操作，后果自负）</font></td>
  </tr>
<tr bgcolor=#cccccc>
  <td colspan="4" align="left" bgcolor="#FFFFFF">
  <table width="99%" border="1" align="center" cellspacing="0" bordercolorlight="#94D0EA" bordercolordark="#F5FBFE">
    <tr bgcolor=#cccccc>
      <td height="30" align="center" bgcolor="#DAECFA" class="btdx">姓名</td>
      <td align="center" bgcolor="#DAECFA" class="btdx">身份证号</td>
      <td align="center" bgcolor="#DAECFA" class="btdx">开户行</td>
      <td align="center" bgcolor="#DAECFA" class="btdx">开户支行</td>
      <td align="center" bgcolor="#DAECFA" class="btdx">开户地址</td>
      <td align="center" bgcolor="#DAECFA" class="btdx">银行卡号</td>
      <td align="center" bgcolor="#DAECFA" class="btdx">操作</td>
    </tr>
<?
while($rst0=mysql_fetch_array($re0)) {
?>
<input type="hidden" id="ssyh" value="<?=$rst0['ssyh']?>">
    <tr>
      <td height="30" align="center" bgcolor="#FFFFFF"><?=$rst0['xm']?></td>
      <td align="center" bgcolor="#FFFFFF"><?=$rst0['sfzh']?></td>
      <td align="center" bgcolor="#FFFFFF"><?=$rst0['khh']?></td>
      <td align="center" bgcolor="#FFFFFF"><?=$rst0['zhihang']?></td>
      <td align="center" bgcolor="#FFFFFF"><?=$rst0['khdz']?></td>
      <td align="center" bgcolor="#FFFFFF"><?=$rst0['yhkh']?></td>
      <td align="center" bgcolor="#FFFFFF"><a href="javascript:;" onclick="bind('1','<?=$rst0['id']?>','<?=htmlspecialchars($_GET['pid'])?>')" style='color: #000'>修改</a> / <a href="javascript:;"  onclick="bind('2','<?=$rst0['id']?>','<?=htmlspecialchars($_GET['pid'])?>')"  style='color: #000'>删除</a></td>
    </tr>
<?}?>
  </table>      </td>
</tr>
<?

$query1="SELECT lx from xbmall_users where user_name='$user'";
$re1=mysql_query($query1,$db);
$rst1=mysql_fetch_array($re1);
if($rst1['lx']==1) {
?>
<tr bgcolor=#cccccc>
    
  
    <td height="32" colspan="4" align="center" bgcolor="#DAECFA" class="btdx">商家转会员操作面板<font style="color: red;font-weight: bold;">（谨慎操作，后果自负）</font></td>
  </tr>
<tr bgcolor=#cccccc>
  <td height="30" align="center" bgcolor="#DAECFA" class="btdx">商家转会员</td>
  <td height="30" colspan="3" align="left" bgcolor="#FFFFFF"><input name="szh" type="checkbox" id="szh"  class="jbsrk" value="1" style="float: left;" /><span style="float: left;margin-left: 10px;margin-top: 5px;font-weight: bold;color: red">确认商家转会员操作</span>      </td>
</tr>
<?
    }
  }
}
?>
<tr bgcolor=#cccccc>
  <td height="12" colspan="4" align="center" bgcolor="#FFFFFF"><label>
    <input type="submit" name="Submit" value="确认修改" style="background-color:#F63;color:#FFF;border:0px;line-height:14px;height:22px;"/>
    &nbsp;
    <input type="reset" name="Submit2" value="数据重置" style="background-color:#F63;color:#FFF;border:0px;line-height:14px;height:22px;"/>
  </label></td>
  </tr>
<tr bgcolor=#cccccc>
  <td height="49" colspan="4" align="center" bgcolor="#FFFFFF">&nbsp;</td>
</tr>
  </form>
<table width="124" height="24" border="0" cellpadding="0" cellspacing="0">
</table></table>
</div>
<script type="text/javascript">
function bind(cz,id,pid){  
 location.href="pmoda.php?cz="+cz+"&id="+id+"&pid="+pid; 
}
</script>
</body>
</html>
