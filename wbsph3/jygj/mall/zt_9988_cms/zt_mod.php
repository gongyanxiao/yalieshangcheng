
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>用户授权</title>
<style type="text/css">
body,td,th {
	font-size: 12px;
	color: #333;
}
a {
	font-size: 12px;
}
a:link {
	color: #333;
	text-decoration: none;
}
a:visited {
	text-decoration: none;
	color: #333;
}
a:hover {
	text-decoration: none;
}
a:active {
	text-decoration: none;
}
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}

</style>
</head>
<body>
<form method="post" name="myform" id="myform" action="?sid=1">
<script src="../lib/sea.js"></script>
<script>
seajs.config({
  alias: {
    "jquery": "../lib/jquery-1.10.2.js"
  }
});

</script>
 <?php  
 if($_COOKIE['h']<>"1"){
exit();
}
include "config/check.php";
date_default_timezone_set("Asia/Shanghai");
include "../config/zt_config.php";
include "../class/zt_class.php";
$db=mysql_connect($db_host,$db_user,$db_pwd);
mysql_query("SET NAMES $coding"); 
mysql_select_db($db_database);
$id=strip_tags($_GET["id"]);
//判断是否存在用户
$sql="select * from zt_qx where yh='$id'";
$q=mysql_query($sql);
$num=mysql_num_rows($q);
$s=mysql_fetch_assoc($q);
if(strip_tags($_GET["sid"])=="1"){
//判断是否存在用户
$yh1=strip_tags($_POST['yh']);
$sql="select * from zt_qx where yh='$yh1'";
$q=mysql_query($sql);
$num=mysql_num_rows($q);
$s=mysql_fetch_assoc($q);
$js=strip_tags($_POST['js']);
$a=strip_tags($_POST['a']);
$b=strip_tags($_POST['b']);
$c=strip_tags($_POST['c']);
$d=strip_tags($_POST['d']);
$e=strip_tags($_POST['e']);
$f=strip_tags($_POST['f']);
$g=strip_tags($_POST['g']);
$h=strip_tags($_POST['h']);
$i=strip_tags($_POST['i']);
$tzcw=strip_tags($_POST['tzcw']);
$tzjs=strip_tags($_POST['tzjs']);
$area=strip_tags($_POST['area']);
if($num<1){
	$sql1="INSERT INTO `zt_qx`(`id`,`a`,`b`,`c`,`d`,`e`,`f`,`g`,`h`,`i`,`yh`,`js`,`tzcw`,`tzjs`,`area`) VALUES (NULL,'$a','$b','$c','$d','$e','$f','$g','$h','$i','$yh1','$js','$tzcw','$tzjs','$area');";
$re1=mysql_query($sql1);
}
if($num>="1"){
	$sql1="update zt_qx set a='$a',b='$b',c='$c',d='$d',e='$e',f='$f',g='$g',h='$h',i='$i',area='$area',js='$js',tzjs='$tzjs',tzcw='$tzcw' where yh='$yh1' and yh<>'admin' and yh<>'';";
$re1=mysql_query($sql1);
}     
if($re1){
?>
<script>
seajs.use(['jquery', '../src/dialog'], function ($, dialog) {
var d = dialog({
    title: '提示',
    content: '授权信息完成!',
    cancel: false,
    ok: function () {
		}
});
d.show();
});	
</script>
<?php

echo '<script>'.'location.href="zt_mod.php?id='.$yh1.'";</script>';
}
}

?>  
<div style="z-index:100:width:100%;height:22px;">

<table width="100%" border="0" height="22px;" cellpadding="0" cellspacing="0">
  <tr>
    <td width="2%" height="22"><img src="images/position1.jpg"></td>
    <td width="98%" align="left" background="images/position2.jpg">您当前的位置 &gt;&gt;用户授权&gt;&gt;&nbsp;&nbsp;<a href="../reg.php" target="_blank">注册用户</a></td>
  </tr>
</table>

</div>

 <p>&nbsp;</p>
 <table width="756" height="192" border="0" align="center" cellpadding="0" cellspacing="0">
   <tr>
     <td width="646" height="79"><p>管理用户
<input name="yh" type="text" id="yh"  value="<?php echo htmlspecialchars($_GET["id"])?>" readonly/>
     </p>
      <p>角色名称
        <input name="js" type="text" id="js" value="<?=$s["js"];?>"/>
</p>
      <p>所在区域
        <select name="area" id="area">

         <option value="<?=$s["area"];?>"><?=$s["area"];?></option>
              <option value="全国站长">全国站长</option>
          <option value="北京市">北京市</option>
          <option value="天津市">天津市</option>
          <option value="上海市 ">上海市 </option>
          <option value="重庆市">重庆市</option>
          <option value="河北省 ">河北省 </option>
         <option value="河南省">河南省</option>
         <option value="黑龙江省">黑龙江省</option>
         <option value="浙江省">浙江省</option>
         <option value="福建省">福建省</option>
         <option value="山东省">山东省</option>
         <option value="四川省">湖南省</option>
         <option value="陕西省">陕西省</option>
         <option value="青海省">青海省</option>
         <option value="甘肃省">甘肃省</option>
         <option value="山西省">山西省</option>
          <option value="山西省">山西省</option>
         <option value="辽宁省">辽宁省</option>
         <option value="吉林省">吉林省</option>
         <option value="江苏省">江苏省</option>
         <option value="安徽省">安徽省</option>
         <option value="江西省">江西省</option>
         <option value="湖北省">湖北省</option>
          <option value="广东省">广东省</option>
          <option value="海南省">海南省</option>
         <option value="贵州省">贵州省</option>
     <option value="内蒙古自治区">内蒙古自治区</option>
     <option value="宁夏回族自治区">宁夏回族自治区</option>
     <option value="新疆维吾尔族自治区">新疆维吾尔族自治区 </option>
      </select></p></td>
   </tr>
   <tr>
     <td height="76"><label>
     
   <input name="a" type="checkbox" id="a" value="1"   <?php if($s["a"]=="1"){echo 'checked="checked"';};?>/>
     普通会员　
   <input name="b" type="checkbox" id="b" value="1"  <?php if($s["b"]=="1"){echo 'checked="checked"';};?>/>
商家用户　
<input name="c" type="checkbox" id="c" value="1"  <?php if($s["c"]=="1"){echo 'checked="checked"';};?>/>
商品管理
<input name="d" type="checkbox" id="d" value="1"  <?php if($s["d"]=="1"){echo 'checked="checked"';};?>/>
订单管理
<input name="e" type="checkbox" id="e" value="1"  <?php if($s["e"]=="1"){echo 'checked="checked"';};?>/>
(保单管理)商家积分转换
<input name="f" type="checkbox" id="f" value="1"  <?php if($s["f"]=="1"){echo 'checked="checked"';};?>/>
充值记录
<input name="g" type="checkbox" id="g" value="1"  <?php if($s["g"]=="1"){echo 'checked="checked"';};?>/>
商品分类
<input name="i" type="checkbox" id="i" value="1"  <?php if($s["i"]=="1"){echo 'checked="checked"';};?>/>
赠送积分
<input name="h" type="checkbox" id="h" value="1"  <?php if($s["h"]=="1"){echo 'checked="checked"';};?>/>
权限管理　
　
　<input name="tzjs" type="checkbox" id="tzjs" value="1"  <?php if($s["tzjs"]=="1"){echo 'checked="checked"';};?>/>
投资技术角色<input name="tzcw" type="checkbox" id="tzcw" value="1"  <?php if($s["tzcw"]=="1"){echo 'checked="checked"';};?>/>
投资财务角色
</label></td>
   </tr>
   <tr>
     <td align="left"><input type="submit" name="button" id="button" value="立刻设置" style="background-color:#F63;color:#FFF;border:0px;line-height:14px;height:22px;"/></td>
   </tr>
 </table>
</form>
</body>
</html>

       