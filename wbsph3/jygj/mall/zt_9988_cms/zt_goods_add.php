<?php
session_start();
if($_COOKIE['c']<>"1"){
exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>商品管理</title>


<style type="text/css">
<!--
	.middle-img {
		position: absolute;
		left: 259px;
		top: 0px;
		width: 40px;
	}

	.up-img {
		position: absolute;
		top: -40px;
		left: 261px;
		width: 40px;
	}

	.down-img {
		position: absolute;
		top: 40px;
		left: 257px;
		width: 40px;
	}

	.left-img {
		position: absolute;
		left: 218px;
		width: 40px;
	}

	.right-img {
		position: absolute;
		left: 300px;
		width: 40px;
	}
	@media screen and (max-width:450px) {
		.middle-img {
			position: absolute;
			left: 259px;
			top: 0px;
			width: 40px;
			display: none;
		}

		.up-img {
			position: absolute;
			top: -40px;
			left: 261px;
			width: 40px;
			display: none;
		}

		.down-img {
			position: absolute;
			top: 40px;
			left: 257px;
			width: 40px;
			display: none;
		}

		.left-img {
			position: absolute;
			left: 218px;
			width: 40px;
			display: none;
		}

		.right-img {
			position: absolute;
			left: 300px;
			width: 40px;
			display: none;
		}

		.rotate_jia{
			display: none;
		}.rotate_div{
			display: none;
		}.rotate_jian{
			display: none;
		}
	}
body,td,th {
	font-size: 12px;
	color: #666666;
	font-family: Arial, Helvetica, sans-serif;
}
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
.btdx {
	color: #0085B6;
	font-weight: bold;
}

.nav8 a:link{
color:#0085B6;
text-decoration:none;
font-size:12px;
font-family: 宋体;
 
}
.nav8 a:visited{
color:#0085B6 ;
text-decoration:none;
font-size:12px;
 
font-family: 宋体;
}
.nav8 a:hover{
color:#f00000;
text-decoration:none;
font-size:12px;
 
font-family: 宋体;
}

.page {

	padding:3px;

	font-weight:normal;

	font-size:12px;

}

.page a {

	border:1px solid #0085B6;

	padding:3px;

	margin:2px;
	font-size:12px;

text-decoration:none;

	color:#0085B6;

}

.page span {

	padding:3px;

	margin:2px;

	background:#ffffff;

	color:#0085B6;

	border:1px solid  #0085B6;

}

.ss{
color:#FF6602;
font-size:14px;
font-weight:bold;
}
.ssborder{
color:#FF6602;
font-size:14px;
border:1px #FF9900 solid;
}
a {
	font-family: 宋体;
	color: #333333;
	font-size: 12px;
}
a:link {
	text-decoration: none;
}

a:hover {
	text-decoration: none;
	color: #FF0000;
}
a:active {
	text-decoration: none;
}
.bk{border-bottom:1px #999 dotted;}
.in{width:380px;border:1px solid #999;height:22px;}
.fbsj{width:60px;border:1px solid #999;height:22px;}
.hand{background-color:#F63;color:#FFF;border:0px;line-height:14px;height:22px; cursor:hand;}
.STYLE1 {color: #3366CC}
.imglist1{margin:3px;}
  .imglist1 img{width:120px;height:80px;}
   .imglist2{margin:3px;float:left;width:80%;margin-left:72px;}
  .imglist2 img{width:60px;height:60px;}
 
-->
</style>
<link type="text/css" rel="stylesheet" href="css/showBo.css" />
<script language="javascript" type="text/javascript" src="dateeditor/WdatePicker.js"></script>
<script type="text/javascript" src="js/showBo.js"></script>
<script type='text/javascript' src='js/jquery-2.0.3.min.js'></script>
<script type='text/javascript' src='js/LocalResizeIMG.js'></script>
<script type='text/javascript' src='js/patch/mobileBUGFix.mini.js'></script>
<script type="text/javascript">
$(document).ready(function(e) {
var uploadphoto1;
var uploadphoto2;
   $('#uploadphoto1').localResizeIMG({
      width: 600,
      quality: 2,
      success: function (result) {  
		  var submitData={
		base64_string:result.clearBase64, 
			}; 
		$.ajax({
		   type: "POST",
		   url: "uploadpicjk1.php",
		   data: submitData,
		   dataType:"json",
		   success: function(data){
			 if (0 == data.status) {
	Showbo.Msg.alert(data.content);
				return false;
			 }else{
				Showbo.Msg.alert(data.content);
				var attstr= '<img src="'+data.url+'">'; 
				$(".imglist1").html(attstr);
				return false;
			 }
		   }, 
			complete :function(XMLHttpRequest, textStatus){
			},
			error:function(XMLHttpRequest, textStatus, errorThrown){ //上传失败 
			   alert(XMLHttpRequest.status);
			   alert(XMLHttpRequest.readyState);
			   alert(textStatus);
			}
		}); 
      }
  });

 $('#uploadphoto2').localResizeIMG({
      width: 600,
      quality: 2,
      success: function (result) {  
		  var submitData={
				base64_string:result.clearBase64, 
			}; 
		$.ajax({
		   type: "POST",
		   url: "uploadpicjk2.php",
		   data: submitData,
		   dataType:"json",
		   success: function(data){
			 if (0 == data.status) {
						Showbo.Msg.alert(data.content);
				return false;
			 }else{
					Showbo.Msg.alert(data.content);
				var attstr= '<div style="float:left;border:1px solid #ccc;margin-left:5px;"><img src="'+data.url+'"></div>'; 
				$(".imglist2").append(attstr);
				return false;
			 }
		   }, 
			complete :function(XMLHttpRequest, textStatus){
			},
			error:function(XMLHttpRequest, textStatus, errorThrown){ //上传失败 
			   alert(XMLHttpRequest.status);
			   alert(XMLHttpRequest.readyState);
			   alert(textStatus);
			}
		}); 
      }
  });
}); 
</script>
</head>
<body> 
<div style="z-index:100:width:100%;height:22px;">
<table width="100%" border="0" height="22px;" cellpadding="0" cellspacing="0">
  <tr>
    <td width="2%" height="22"><img src="images/position1.jpg"></td>
    <td width="98%" align="left" background="images/position2.jpg">您当前的位置 &gt;&gt;<a href="zt_goods_list.php">商品管理</a>&gt;&gt;增加商品</td>
  </tr>
</table>

</div>
<form method="post" name="myform" id="myform" action="?sid=1">
 <?php  
include "config/check.php";
$yh=$_COOKIE['zt_user2'];
date_default_timezone_set("Asia/Shanghai");
include "../config/zt_config.php";
include "../config/zt_class.php";
$db=mysql_connect($db_host,$db_user,$db_pwd);
mysql_query("SET NAMES $coding"); 
mysql_select_db($db_database);
if(strip_tags($_GET["sid"])=="1"){
//商品名称
$spmc=htmlspecialchars($_POST['spmc']);
$jjms=htmlspecialchars($_POST['jjms']);
$sftj=htmlspecialchars($_POST['sftj']);
$scjg=htmlspecialchars($_POST['scjg']);
$spjg=htmlspecialchars($_POST['spjg']);
//省份
$sql="select * from zt_qx where yh='$yh'";
$qs1=mysql_query($sql);
$sf1=mysql_fetch_assoc($qs1);
$sf=$sf1["area"];
//是否上架
$sfsj=htmlspecialchars($_POST['sfsj']);
//发布时间
$fbsj=htmlspecialchars($_POST['fbsj']);
$kc=htmlspecialchars($_POST['kc']);
$cnxh=htmlspecialchars($_POST['cnxh']);
$pp=htmlspecialchars($_POST['pp']);
$dpmc=htmlspecialchars($_POST['dpmc']);
$spslt=$_SESSION['pic1'];
$dt=$_SESSION['pic11'];
$spjs=$_POST['content'];
$ggjbz=$_POST['content1'];
$ssfw=$_POST['content2'];
$fl=htmlspecialchars($_POST['fl']);
$spbh="jsgj".GetRandStr(9);
$query="INSERT INTO `zt_goods`(`id`,`spmc`,`sf`,`spbh`,`pp`,`jjms`,`sftj`,`sfsj`,`scjg`,`spjg`,`jfgm`,`kc`,`fbsj`,`dt`,`spslt`,`spjs`,`ggjbz`,`cnxh`,`fl`,`ssfw`,`username`,`dpmc`)";
$query.=" VALUES ('0','$spmc','$sf','$spbh','$pp','$jjms','$sftj','$sfsj','$scjg','$spjg','','$kc','$fbsj','$dt','$spslt','$spjs','$ggjbz','$cnxh','$fl','$ssfw','$yh','$dpmc');";

$re=mysql_query($query);
if($re){
unset($_SESSION['pic1']);
unset($_SESSION['pic11']);
?>
<script>
<!--
alert("上传商品信息完成");
//-->
</script>
<?php
print("<script language='javascript'>window.location.href='zt_goods_add.php';</script>");
?>
<?php
}else{
?>
<script>alert("上传商品信息有误，请重新上传！");</script>
<?
	print("<script language='javascript'>window.location.href='zt_goods_add.php';</script>");
	exit();
	}
}
?>  

<table width="50%" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-left:100px;margin-top:20px;">
  <tr>
    <td height="43" colspan="3" align="left">商品名称：
     
      <input type="text" name="spmc" id="spmc" class="in"/>
&nbsp;      <input type="submit" name="button" id="button" value="增加信息" class="hand"/></td>
  </tr>
  <tr>
    <td height="41" colspan="3" align="left">店铺名称：
      <input type="text" name="dpmc" id="dpmc" class="in"/></td>
  </tr>
  <tr>
    <td height="44" colspan="3">简洁描述： 
      <input type="text" name="jjms" id="jjms" class="in"/>
      <span class="STYLE1">
      <input name="sftj" type="checkbox" id="sftj" value="1" checked="checked" />
      <label for="tj"></label>
      是否推荐
      <input name="sfsj" type="checkbox" id="sfsj" value="1" checked="checked" />
      上架&nbsp;</span></td>
  </tr>
  <tr>
    <td height="45" colspan="3">发布时间：
 <input type="text" name="fbsj" id="fbsj" class="Wdate" onClick="WdatePicker()" value="<?=date("Y-m-d");?>"/>
      &nbsp;市场价格：
<input type="text" name="scjg" id="scjg" class="fbsj"/>
      &nbsp;商品价格：
<input type="text" name="spjg" id="spjg" class="fbsj"/>
<span class="STYLE1">
<input name="cnxh" type="checkbox" id="cnxh" value="1" />
猜你喜欢
&nbsp;</span></td>
  </tr>
  <tr>
    <td width="9%" height="39">商品缩例图
 
  
    </td>
    <td width="19%" align="center">

<div style="width:80px;margin:1px auto;  overflow:hidden;float:left;margin-left:10px;height:20px;">
		<a href="javascript:void(0);" onclick="uploadphoto1.click();" class="uploadbtn">
  <input type="file" id="uploadphoto1" name="uploadfile1" value="上传凭证"   style="display:none;"/> 

 <DIV style="background-color:#F60;width:80px;height:auto;">  <div style="height:20px;line-height:20px;"><span style="color:#fff; font-family:'宋体'; font-size:12px;"><strong>上传</strong></span>
            </div> 
    </DIV></a>

 </div></td>
    <td width="72%" align="left"><strong>商品分类：</strong>
      <?php
$sql12="SELECT * FROM zt_goods_sort where item1=1";
$re1=mysql_query($sql12,$db);
$num=mysql_num_rows($re1);
$sql2="SELECT * FROM zt_goods_sort where item1=1";
echo '<select name="fl" id="fl" style="border:1px solid #cccccc;color:#000;">';
$ref=mysql_query($sql2,$db);
for($i=0;$i<$num;$i++){
$data=mysql_fetch_array($ref);
echo '<option value="'.$data['id'].'">'.$data['columname'].'</option>';
$sql3="SELECT * FROM zt_goods_sort where item1=".$data['id'];
$retwo=mysql_query($sql3,$db);
echo '<ul style="margin:0px;padding:0px;width:260px;">';
$re2n=mysql_query($sql3,$db);
$num2=mysql_num_rows($re2n);
for($i1=0;$i1<$num2;$i1++){
$data2=mysql_fetch_array($retwo);
echo '<option value="'.$data2['id'].'">'."&nbsp;&nbsp;┠-".$data2['columname'].'</option>';
$sql4="SELECT * FROM zt_goods_sort where item2=".$data2['id'];
$rethree=mysql_query($sql4,$db);
$rethree=mysql_query($sql4,$db);
$num4=mysql_num_rows($rethree);
for($i2=0;$i2<$num4;$i2++){
$data4=mysql_fetch_array($rethree);
echo '<option value="'.$data4['id'].'">'."┠--".$data4['columname'].'</option>';
}
}
}
echo "</script>";
mysql_close($db);

?></td>
  </tr>
  <tr>
    <td height="15" colspan="3" style="padding-left:75px;">     <div class="imglist1"></div>  </td>
  </tr>
  <tr>
    <td height="41">商品相册
  
</td>
    <td height="41" align="center">
    <div style="width:80px;margin:1px auto;  overflow:hidden;float:left;margin-left:10px;height:20px;">
		<a href="javascript:void(0);" onclick="uploadphoto2.click();" class="uploadbtn">
 <input type="file" id="uploadphoto2" name="uploadfile" value="上传借款凭证"   style="display:none;"/> 
  
  <DIV style="background-color:#F60;width:80px;height:20px;">  <div style="height:20px;line-height:20px;"><span style="color:#fff; font-family:'宋体'; font-size:12px;"><strong>上传</strong></span>
            </div> 
    </DIV></a>
 </div>
    </td>
    <td height="41" align="left">库存：
      <input type="text" name="kc" id="kc" class="fbsj"/>
      <span class="STYLE1">&nbsp;</span>品牌：
      <input type="text" name="pp" id="pp" class="fbsj"/></td>
  </tr>
  <tr>
    <td height="15" colspan="3">  <div class="imglist2"></div> 
</td>
  </tr>
  <tr>
    <td height="35" colspan="3">
      <strong>商品介绍：</strong>      <script type="text/javascript" src="./ztphpeditor/ueditor.config.js"></script>
          <!-- 编辑器源码文件 -->
      <script type="text/javascript" src="./ztphpeditor/ueditor.all.js"></script>
          <!-- 语言包文件(建议手动加载语言包，避免在ie下，因为加载语言失败导致编辑器加载失败) -->
        <script type="text/javascript" src="./ztphpeditor/lang/zh-cn/zh-cn.js"></script>
        <script id="container" name="content" type="text/plain" style="width:733px;height:100px;z-index:-999px;">
          </script><br>
        <script type="text/javascript">
    var editor = UE.getEditor('container')
          </script>
    <strong>规格及包装:</strong>
   
        <script id="container1" name="content1" type="text/plain" style="width:733px;height:100px;z-index:-999px;">
          </script>
        <script type="text/javascript">
    var editor = UE.getEditor('container1')
          </script>
   </td>
  </tr>
  <tr>
    <td height="15" colspan="3"><strong>售后服务:</strong>
        
        <script id="container2" name="content2" type="text/plain" style="width:733px;height:100px;z-index:-99px;">
          </script>
        <script type="text/javascript">
    var editor = UE.getEditor('container2')
          </script>
    </td>
  </tr>
  <tr>
    <td height="15" colspan="3" align="center"><input type="submit" name="button2" id="button2" value="增加商品信息" class="hand"/></td>
  </tr>

</table>
</form>
</body>
</html>


