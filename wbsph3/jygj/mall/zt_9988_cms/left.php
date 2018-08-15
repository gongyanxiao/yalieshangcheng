<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>menu</title>

<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}

a {
	font-family: 宋体;
	font-size: 14px;
	color: #666666;
}
a:link {
	text-decoration: none;
	color: #333333;
}
a:visited {
	text-decoration: none;
	color: #333333;
}
a:hover {
	text-decoration: none;
	color: #0099FF;
}
a:active {
	text-decoration: none;
}
.STYLE1 {font-size: 12px}
.STYLE2 {color: #0066CC}
td {
	font-size: 14px;
}
font {
	font-weight: bold;
}
#navlink A:link {
COLOR:#0066cc; font-size:12px;TEXT-DECORATION: none;list-style-type:none;
font-weight:bold;
}
#navlink A:hover{
COLOR:#ff0000; font-size:12px;TEXT-DECORATION:none;list-style-type:none;
font-weight:bold;
}
#navlink1 A:visited{
 COLOR:#606060; font-size:12px; TEXT-DECORATION: none;list-style-type:none;

}
#navlink1 A:link {
COLOR:#0066cc; font-size:12px;TEXT-DECORATION: none;list-style-type:none;

}
#navlink1 A:hover{
COLOR:#ff0000; font-size:12px;TEXT-DECORATION:none;list-style-type:none;

}
#navlink1 A:visited{
 COLOR:#606060; font-size:12px; TEXT-DECORATION: none;list-style-type:none;

}
.dot{border-bottom:1px solid #CCC;line-height:20px;}
-->
</style>
</head>
<body style="overflow: scroll;">
<table width="196" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="182"><img src="images/gndh001.jpg" width="196" height="54" border="0"></td>
  </tr>
  <tr>
    <td><table cellspacing="0" cellpadding="0" width="196" border="0">
  <tr>
    <td width="1599" height="31"><img src="images/zhuabangdongtai.jpg" width="196" border="0" 

/></td>
  </tr>
  <tr>
    <td height="69" align="center" valign="top"><table width="196" height="35" border="0" 

cellpadding="0" cellspacing="0">
      <tr>
    <td height="35" align="left" valign="middle"><font color="#0094CF">频道菜单</font></td>
  </tr>
</table>
<?php
if(isset($_COOKIE['a']) && $_COOKIE['a']=="1"){
?>
<table width="196" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="9%" height="20" align="right" background="img/admin_img/side_bg.gif" id="navlink" class="dot"><span class="STYLE1"><img src="images/list_ico.jpg" width="16" height="20" /></span></td>
    <td width="91%" align="left" id="navlink" class="dot"><a href="zt_user_list.php"  target="mainFrame">&nbsp;普通会员</a></td>
  </tr>
</table>
<?php
}
?>

<?php
if(isset($_COOKIE['b']) && $_COOKIE['b']=="1"){
?>
<table width="196" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="9%" height="33" align="left" class="dot" id="navlink">    <span class="STYLE1"><img src="images/list_ico.jpg" width="16" height="20" /></span></td>
    <td width="91%" align="left" class="dot" id="navlink"><a href="zt_shop_user_list.php" 

target="mainFrame">&nbsp;商家用户</a></td>
  </tr>
</table>
<?php
}
?>
<?php
if(isset($_COOKIE['i']) && $_COOKIE['i']=="1"){
?>
<table width="196" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="9%" height="29" align="right" background="img/admin_img/side_bg.gif" id="navlink" class="dot"><a href="zt_chongzhi_list.php"  target="mainFrame"><img src="images/grpic.png" border="0"></a></td>
    <td width="91%" align="left" id="navlink" class="dot"><a href="zt_chongzhi_list.php"  target="mainFrame"><font color="#00CC66">&nbsp;赠送积分</font></a></td>
  </tr>
</table>

<?php
}
?>

<?php
if(isset($_COOKIE['d']) && $_COOKIE['d']=="1"){
?>
<table width="196" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="9%" height="33" align="left" class="dot" id="navlink"> <span class="STYLE1"><img src="images/list_ico.jpg" width="16" height="20" /></span></td>
    <td width="91%" align="left" class="dot" id="navlink1"><a href="zt_shop_order_list.php" target="mainFrame">&nbsp;订单管理</a></td>
  </tr>
</table>
<?php
}
?>
<?php
if(isset($_COOKIE['e']) && $_COOKIE['e']=="1"){
?>
<table width="196" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="9%" height="33" align="left" class="dot" id="navlink"> <span class="STYLE1"><img src="images/list_ico.jpg" width="16" height="20" /></span></td>
    <td width="91%" align="left" class="dot" id="navlink1"><a href="zt_bd_order_list.php" target="mainFrame">&nbsp;保单管理</a></td>
  </tr>
</table>
<?php
}
?>
<?php
if(isset($_COOKIE['e']) && $_COOKIE['e']=="1"){
?>
<table width="196" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="9%" height="33" align="left" class="dot" id="navlink"> <span class="STYLE1"><img src="images/list_ico.jpg" width="16" height="20" /></span></td>
    <td width="91%" align="left" class="dot" id="navlink1"><a href="zt_dh_order_list.php" target="mainFrame">&nbsp;兑换订单管理</a></td>
  </tr>
</table>
<?php
}
?>
<?php
if(isset($_COOKIE['e']) && $_COOKIE['e']=="1"){
?>
<table width="196" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="9%" height="33" align="left" class="dot" id="navlink"> <span class="STYLE1"><img src="images/list_ico.jpg" width="16" height="20" /></span></td>
    <td width="91%" align="left" class="dot" id="navlink1"><a href="zt_jf_exchange.php" target="mainFrame">&nbsp;积分转换</a></td>
  </tr>
</table>
<?php
}
?>
<?php
if(isset($_COOKIE['f']) && $_COOKIE['f']=="1"){
?>
<table width="196" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="9%" height="33" align="left" class="dot" id="navlink"> <span class="STYLE1"><img src="images/list_ico.jpg" width="16" height="20" /></span></td>
    <td width="91%" align="left" class="dot" id="navlink1"><a href="zt_shop_chongzi_list.php" target="mainFrame">&nbsp;充值记录</a></td>
  </tr>
</table>
<table width="196" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="9%" height="33" align="left" class="dot" id="navlink"> <span class="STYLE1"><img src="images/list_ico.jpg" width="16" height="20" /></span></td>
    <td width="91%" align="left" class="dot" id="navlink1"><a href="zt_cash_exchange.php" target="mainFrame">&nbsp;提现记录</a></td>
  </tr>
</table>
<?php
}
?>

<?php
if(isset($_COOKIE['c']) && $_COOKIE['c']=="1"){
?>
<table width="196" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="9%" height="35" align="left" class="dot" id="navlink">   <span class="STYLE1"><img src="images/list_ico.jpg" width="16" height="20" /></span></td>
    <td width="91%" align="left" class="dot" id="navlink"><a href="zt_goods_list.php" target="mainFrame">&nbsp;商品信息</a></td>
  </tr>
</table>




<?php
}
?>

<?php
if(isset($_COOKIE['g']) && $_COOKIE['g']=="1"){
?>
<table width="196" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="9%" height="35" align="left" class="dot" id="navlink">   <span class="STYLE1"><img src="images/list_ico.jpg" width="16" height="20" /></span></td>
    <td width="91%" align="left" class="dot" id="navlink"><a href="zt_sort_add.php" target="mainFrame">&nbsp;商品分类</a></td>
  </tr>
</table>



<?php
if(isset($_COOKIE['c']) && $_COOKIE['c']=="1"){
?>
<table width="196" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="9%" height="35" align="left" class="dot" id="navlink">   <span class="STYLE1"><img src="images/list_ico.jpg" width="16" height="20" /></span></td>
    <td width="91%" align="left" class="dot" id="navlink"><a href="zt_jifen_list.php" target="mainFrame">&nbsp;积分商品信息</a></td>
  </tr>
</table>




<?php
}
?>

<?php
}
?>
<?php
if(isset($_COOKIE['g']) && $_COOKIE['g']=="1"){
?>
<table width="196" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="9%" height="35" align="left" class="dot" id="navlink">   <span class="STYLE1"><img src="images/list_ico.jpg" width="16" height="20" /></span></td>
    <td width="91%" align="left" class="dot" id="navlink"><a href="zt_sort1_add.php" target="mainFrame">&nbsp;积分分类</a></td>
  </tr>
</table>

<?php
}
?>
<?php
if(isset($_COOKIE['h']) && $_COOKIE['h']=="1"){
?>
<table width="196" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="9%" height="36" align="left" class="dot" id="navlink">   <span class="STYLE1"><img src="images/list_ico.jpg" width="16" height="20" /></span></td>
    <td width="91%" align="left" class="dot" id="navlink"><a href="zt_qx_list.php" target="mainFrame">&nbsp;权限管理</a></td>
  </tr>
</table>
<?php
}
?>

<table width="196" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="9%" height="20" align="right" background="img/admin_img/side_bg.gif" id="navlink" class="dot"><span class="STYLE1"><img src="images/list_ico.jpg" width="16" height="20" /></span></td>
    <td width="91%" align="left" id="navlink" class="dot"><a href="zt_user_cj_list.php"  target="mainFrame">&nbsp;超级会员</a></td>
  </tr>
</table>


<table width="196" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="9%" height="36" align="left" class="dot" id="navlink">   <span class="STYLE1"><img src="images/list_ico.jpg" width="16" height="20" /></span></td>
    <td width="91%" align="left" class="dot" id="navlink"><a href="xxtz_cz_list.php" target="mainFrame">&nbsp;充值审核</a></td>
  </tr>
</table>
<table width="196" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="9%" height="36" align="left" class="dot" id="navlink">   <span class="STYLE1"><img src="images/list_ico.jpg" width="16" height="20" /></span></td>
    <td width="91%" align="left" class="dot" id="navlink"><a href="xxtz_list.php" target="mainFrame">&nbsp;投资记录</a></td>
  </tr>
</table>
 


<table width="196" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="9%" height="33" align="left" class="dot" id="navlink"> <span class="STYLE1"><img src="images/list_ico.jpg" width="16" height="20" /></span></td>
    <td width="91%" align="left" class="dot" id="navlink1"><a href="zt_cash_exchange_cj.php" target="mainFrame">&nbsp;投资提现记录</a></td>
  </tr>
</table>
<table width="196" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="9%" height="36" align="left" class="dot" id="navlink">   <span class="STYLE1"><img src="images/list_ico.jpg" width="16" height="20" /></span></td>
    <td width="91%" align="left" class="dot" id="navlink"><a href="xxcz_fa_fang_manager.php" target="mainFrame">&nbsp;聚珠发放管理</a></td>
  </tr>
</table>
<table width="196" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="9%" height="36" align="left" class="dot" id="navlink">   <span class="STYLE1"><img src="images/list_ico.jpg" width="16" height="20" /></span></td>
    <td width="91%" align="left" class="dot" id="navlink"><a href="zt_setting_list.php" target="mainFrame">&nbsp;系统配置</a></td>
  </tr>
</table>
<table width="196" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="9%" height="36" align="left" class="dot" id="navlink">   <span class="STYLE1"><img src="images/list_ico.jpg" width="16" height="20" /></span></td>
    <td width="91%" align="left" class="dot" id="navlink"><a href="xxtzzs_list.php" target="mainFrame">&nbsp;聚珠变动记录</a></td>
  </tr>
</table>

<table width="196" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="9%" height="36" align="left" class="dot" id="navlink">   <span class="STYLE1"><img src="images/list_ico.jpg" width="16" height="20" /></span></td>
    <td width="91%" align="left" class="dot" id="navlink"><a href="xxtz_tree.php" target="mainFrame">&nbsp;测试</a></td>
  </tr>
</table>

<table width="196" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="9%" height="36" align="left" class="dot" id="navlink">   <span class="STYLE1"><img src="images/list_ico.jpg" width="16" height="20" /></span></td>
    <td width="91%" align="left" class="dot" id="navlink"><a href="tong_ji.php" target="mainFrame">&nbsp; 统计</a></td>
  </tr>
</table>

<table width="196" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="9%" height="36" align="left" class="dot" id="navlink">   <span class="STYLE1"><img src="images/list_ico.jpg" width="16" height="20" /></span></td>
    <td width="91%" align="left" class="dot" id="navlink"><a href="tong_ji.php" target="mainFrame">&nbsp; 推荐分红统计</a></td>
  </tr>
</table>


</td>
  </tr>
  <tr background="img/me_bg.jpg">  </tr>
  <tr background="img/me_bg.jpg">
    <td  align="center"></td>
  </tr>
    <td></td>
  </tr>
  <tr>
    <td height="2"></td>
  </tr>
</table>	  
</td>
  </tr>
</table>

<table width="196" height="450" border="0" align="left" cellpadding="0" cellspacing="0" style="height:auto;">
  <tr>
    <td width="182" height="356" valign="bottom"><img src="images/left_end.jpg" width="196" 

height="287"></td>
  </tr>
</table>


</body>
</html>
