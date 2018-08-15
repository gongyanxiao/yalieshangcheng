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
  .imglist1 img{width:80px;height:60px;}
   .imglist2{margin:3px;float:left;width:80%;margin-left:68px;}
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
					document.getelementbyid("slt").display="none";
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
    <td width="98%" align="left" background="images/position2.jpg">您当前的位置 &gt;&gt;<a href="zt_jifen_list.php">商品管理</a>&gt;&gt;编辑商品</td>
  </tr>
</table>

</div>
<form method="post" name="myform" id="myform" action="?sid=1&id=<?php $id=htmlspecialchars($_GET['id']); echo $id;?>">
 <?php  
include "config/check.php";
$yh=$_COOKIE['zt_user2'];
date_default_timezone_set("Asia/Shanghai");
include "../config/zt_config.php";
include "../config/zt_class.php";
$db=mysql_connect($db_host,$db_user,$db_pwd);
mysql_query("SET NAMES $coding"); 
mysql_select_db($db_database);
//显示商品数据

$sql99="select * from zt_jifen where id='$id'";
$q=mysql_query($sql99);
$num=mysql_num_rows($q);
$s=mysql_fetch_assoc($q);
$id=strip_tags($_GET['id']);
if(strip_tags($_GET["sid"])=="1"){
//商品名称
$spmc=htmlspecialchars($_POST['spmc']);
$jjms=htmlspecialchars($_POST['jjms']);
$sftj=htmlspecialchars($_POST['sftj']);
if($sftj<>1) {
	$sftj=0;
}
$scjg=htmlspecialchars($_POST['scjg']);
$spjg=htmlspecialchars($_POST['spjg']);
//省份
$sql="select * from zt_qx where yh='$yh'";
$qs1=mysql_query($sql);
$sf1=mysql_fetch_assoc($qs1);
$sf=$sf1["area"];
//是否上架
$sfsj=htmlspecialchars($_POST['sfsj']);
if($sfsj<>1) {
	$sfsj=0;
}
//发布时间
$fbsj=htmlspecialchars($_POST['fbsj']);
$kc=htmlspecialchars($_POST['kc']);
$cnxh=htmlspecialchars($_POST['cnxh']);
if($cnxh<>1) {
	$cnxh=0;
}
$sfdh=htmlspecialchars($_POST['sfdh']);
if($sfdh<>1) {
	$sfdh=0;
}
$gngj=htmlspecialchars($_POST['gngj']);
$spslt=$_SESSION['pic1'];
$dt=$_SESSION['pic11'];
if($spslt<>""){
	}else{
$spslt=$s['spslt'];
		}
if($dt<>""){
	}else{
$dt=$s['dt'];
		}
$spjs=$_POST['content'];
$ggjbz=$_POST['content1'];
$ssfw=$_POST['content2'];
$fl=htmlspecialchars($_POST['fl']);
$spbh="jygjjf".GetRandStr(9);
$query="UPDATE `zt_jifen` SET `fl`='$fl',`spmc`='$spmc',`sf`='$sf',`username`='$yh',`dhjf`='$spjg',`ckj`='$scjg',`spbh`='$spbh',`spgg`='$ggjbz',`js`='$jjms',`kc`='$kc',`sl`='0',`spjs`='$spjs',`spfw`='$ssfw',`dt`='$dt',`spslt`='$spslt',`gngj`='$gngj',`fbrq`='$fbsj',`spxl`='0',`sfsj`='$sfsj',`sfxptj`='$sftj',`sfdh`='$sfdh',`sfjptj`='$cnxh' WHERE id='$id';";
$re=mysql_query($query);
if($re){
unset($_SESSION['pic1']);
unset($_SESSION['pic11']);
?>
<script>
<!--
alert("编辑商品信息完成");
//-->
</script>
<?php
$url='zt_jifen_mod.php?id='.$id;
print("<script language='javascript'>window.location.href='$url';</script>");
?>
<?php
}else{/*
echo $query;
exit();*/
?>

<script>alert("上传商品信息有误，请重新上传！");</script>
<?
	print("<script language='javascript'>window.location.href='zt_jifen_add.php';</script>");
	exit();
	}
}
?>  


<table width="50%" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-left:100px;margin-top:20px;">
  <tr>
    <td height="43" colspan="3" align="left">商品名称：
     
      <input type="text" name="spmc" id="spmc" class="in" value="<?=$s['spmc']?>" />
&nbsp;      <input type="submit" name="button" id="button" value="编辑信息" class="hand"/></td>
  </tr>
  <tr>
    <td height="41" colspan="3" align="left">国内 / 国际：
      <input type="radio" name="gngj" id="dpmc" <?=$s['gngj']=="0"?' value="0" '.'checked="checked"':' value="0" ';?> />国内
      <span>&nbsp;&nbsp;&nbsp;</span>
      <input type="radio" name="gngj" id="dpmc" <?=$s['gngj']=="1"?' value="1" '.'checked="checked"':' value="1" ';?>/>国际

      </td>
  </tr>
  <tr>
    <td height="44" colspan="3">简述： 
      <input type="text" name="jjms" id="jjms" class="in" value="<?=$s['js']?>" />
      <span class="STYLE1">
      <input name="sftj" type="checkbox" id="sftj" <?=$s['sfxptj']=="1"?' value="1" '.'checked="checked"':' value="0" ';?>/>
      <label for="tj"></label>
      是否积分首页推荐 &nbsp;&nbsp;<input name="sfdh" type="checkbox" id="sfdh" <?=$s['sfdh']=="1"?' value="1" '.'checked="checked"':' value="0" ';?> />
      是否积分首页兑换&nbsp;&nbsp;
      <input name="sfsj" type="checkbox" id="sfsj" <?=$s['sfsj']=="1"?' value="1" '.'checked="checked"':' value="0" ';?> />
      上架&nbsp;&nbsp;</span></td>
  </tr>
  <tr>
    <td height="45" colspan="3">发布时间：
 <input type="text" name="fbsj" id="fbsj" class="Wdate" onClick="WdatePicker()" value="<?=date($s['fbrq']);?>"/>
      &nbsp;参考价格：
<input type="text" name="scjg" id="scjg" class="fbsj" value="<?=$s['ckj']?>" />
      &nbsp;兑换积分：
<input type="text" name="spjg" id="spjg" class="fbsj" value="<?=$s['dhjf']?>" />
<span class="STYLE1">
<input name="cnxh" type="checkbox" id="cnxh"  <?=$s['sfjptj']=="1"?' value="1" '.'checked="checked"':' value="0" ';?>/>
是否积分详情页-精品推荐
&nbsp;</span></td>
  </tr>
  <tr>
    <td width="10%" height="42">商品缩例图
 
  
    </td>
    <td width="10%" align="center"><div style="width:80px;margin:1px auto;  overflow:hidden;float:left;height:20px;">
		<a href="javascript:void(0);" onclick="uploadphoto1.click()" 

class="uploadbtn">
  <input type="file" id="uploadphoto1" name="uploadfile" value="上传凭证"   

style="display:none;" capture="camera" accept="image/*"/> 
   <DIV style="background-color:#F60;width:80px;height:auto;">  <div style="height:20px;line-

height:20px;"><span style="color:#fff; font-family:'宋体'; font-size:12px;"><strong>上传

</strong></span>
            </div> 
    </DIV></a>

 </div></td>
    <td width="72%" align="left"><strong>商品分类：</strong>
      <?php
$sql12="SELECT * FROM zt_jifen_sort where item1=1";
$re1=mysql_query($sql12,$db);
$num=mysql_num_rows($re1);
$sql2="SELECT * FROM zt_jifen_sort where item1=1";
echo '<select name="fl" id="fl" style="border:1px solid #cccccc;color:#000;">';
if($s['fl']<>""){
	$fl=$s['fl'];
	$sql1="select * from zt_jifen_sort where id='$fl'";
$qs1=mysql_query($sql1);
$sf1=mysql_fetch_assoc($qs1);
$lm=$sf1['columname'];
echo '<option value="'.$s['fl'].'">'.$lm.'</option>';
}
$ref=mysql_query($sql2,$db);
for($i=0;$i<$num;$i++){
$data=mysql_fetch_array($ref);
echo '<option value="'.$data['id'].'">'.$data['columname'].'</option>';

}
echo "</select>";
mysql_close($db);

?></td>
  </tr>
  <tr>
    <td height="46" colspan="3" style="padding-left:75px;">     <div class="imglist1"></div>  
  <span id="slt">  <?php 
if($s['spslt']<>""){
	$v=$s['spslt'];
   echo '<a href="'.$v.'" target=_blank>'.'<img src="'.$v.'" width=80 height=60></a>';
   }else{
   echo '<font color="#FF0000">未上传</font>';
   }
?></span>

</td>
  </tr>
  <tr>
    <td height="46">商品相册
  
</td>
    <td height="46" align="center">
    <div style="width:80px;margin:1px auto;  overflow:hidden;float:left;height:20px;">
		<a href="javascript:void(0);" onclick="uploadphoto2.click()" 

class="uploadbtn">
  <input type="file" id="uploadphoto2" name="uploadfile" value="上传借款凭证"   

style="display:none;" capture="camera" accept="image/*"/> 
  
  <DIV style="background-color:#F60;width:80px;height:20px;">  <div style="height:20px;line-

height:20px;"><span style="color:#fff; font-family:'宋体'; font-size:12px;"><strong>上传

</strong></span>
            </div> 
    </DIV></a>
 </div>
    </td>
    <td height="46" align="left">库存：
      <input type="text" name="kc" id="kc" class="fbsj" value="<?=$s['kc'];?>"/>
      </td>
  </tr>
  <tr>
    <td height="41">  
    
</td>
    <td height="41" colspan="2"><?php
	$out=explode(",",$s['dt']);
foreach($out as $v){ 
   if($v<>""){
   echo '<a href="'.$v.'" target=_blank>'.'<img src="'.$v.'" width=60 height=60></a>';
   }else{
   echo '<font color="#FF0000">未上传</font>';
   }

} 
	?></td>
    </tr>
  <tr>
    <td height="34" colspan="3"><div class="imglist2"></div> </td>
  </tr>
  <tr>
    <td height="30" colspan="3">
      <strong>商品介绍：</strong>  <script type="text/javascript" src="./ztphpeditor/ueditor.config.js"></script>
          <!-- 编辑器源码文件 -->
      <script type="text/javascript" src="./ztphpeditor/ueditor.all.js"></script>
          <!-- 语言包文件(建议手动加载语言包，避免在ie下，因为加载语言失败导致编辑器加载失败) -->
        <script type="text/javascript" src="./ztphpeditor/lang/zh-cn/zh-cn.js"></script>
        <script id="container" name="content" type="text/plain" style="width:733px;height:100px;z-index:-999px;">
<?=$s['spjs'];?>
          </script><br>
        <script type="text/javascript">
    var editor = UE.getEditor('container')
          </script>
    <strong>规格及包装:</strong>
   
        <script id="container1" name="content1" type="text/plain" 

style="width:733px;height:100px;z-index:-999px;">
<?=$s['spgg'];?>
          </script>
        <script type="text/javascript">
    var editor = UE.getEditor('container1')
          </script>
   </td>
  </tr>
  <tr>
    <td height="15" colspan="3"><strong>售后服务:</strong>
        
        <script id="container2" name="content2" type="text/plain" 

style="width:733px;height:100px;z-index:-99px;">
<?=$s['spfw'];?>
          </script>
        <script type="text/javascript">
    var editor = UE.getEditor('container2')
          </script>
    </td>
  </tr>
  <tr>
    <td height="15" colspan="3" align="center"><input type="submit" name="button2" id="button2" value="编辑商品信息" class="hand"/></td>
  </tr>

</table>
</form>
</body>
</html>
