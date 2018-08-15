<?php
if($_COOKIE['a']<>"1"){
exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>普通会员</title>

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
-->
</style>
<script type=text/javascript src="../js/jquery.min.js"></script>
<script src="../lib/sea.js"></script>
<script>
seajs.config({
  alias: {
    "jquery": "../lib/jquery-1.10.2.js"
  }
});
</script>


<SCRIPT language=javascript> 
<!-- 
seajs.use(['jquery', '../src/dialog'],function ($, dialog){
$('button[data-event=ts]').on('click',function(){
var id= $(this).attr("name");
var d = dialog({
title: '审核操作',
content: '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select name="sh" id="sh"><option value="2">通过</option><option value="1">未通过</option></select><br>备注:<textarea name="bz" id="bz" cols="45" rows="5"></textarea><br>',
 ok: function(){
//提交到后台查询
var  sh= $('#sh').val();
var  bz= $('#bz').val();
var url="<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];?>";
var dataString='id='+id+'&bz='+bz+'&sh='+sh+'&url='+url; 
$.ajax({  
type: "POST",  
url: "zt_smrz_pro.php",  
data:dataString,  
cache:false,  
success:function(html){  
//$("#fhl").html("<pre>" + html + "</pre>");
$("#jsgj").html(html);
 }  
 }); 
this.close(value);
this.remove();
	 }
	});
d.addEventListener('close', function () {

	});
d.show();
  });
  
});
// --> 



seajs.use(['jquery', '../src/dialog'],function ($, dialog){
$('button[data-event=ts1]').on('click',function(){
var id= $(this).attr("name");
var d = dialog({
title: '删除操作,谨慎操作,后果自负!',
content: '删除缘由:<input name="bz" id="bz" width="300px" height="30px"><br>',
 ok: function(){
//提交到后台查询
var  bz= $('#bz').val();
var url="<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];?>";
var dataString='id='+id+'&bz='+bz+'&url='+url; 
$.ajax({  
type: "POST",  
url: "zt_smrz_pro1.php",  
data:dataString,  
cache:false,  
success:function(html){  
//$("#fhl").html("<pre>" + html + "</pre>");
$("#jsgj").html(html);
 }  
 }); 
this.close(value);
this.remove();
	 }
	});
d.addEventListener('close', function () {

	});
d.show();
  });
  
});
</SCRIPT> 

</head>

<body>  
  
<div style="z-index:100:width:100%;height:22px;">

<table width="100%" border="0" height="22px;" cellpadding="0" cellspacing="0">
  <tr>
    <td width="2%" height="22"><img src="images/position1.jpg"></td>
    <td width="98%" align="left" background="images/position2.jpg">您当前的位置>>普通会员</td>
  </tr>
</table>

</div>
 <div id="jsgj"></div>


  <table width="100%" border=0 align="center" cellspacing=0 bordercolorlight="#94D0EA" 

bordercolordark="#F5FBFE" style="margin-top:15px;">
<tr>
    <td height="35" colspan="15" align="left" bgcolor="#DBEBFA">
	 <form method="post" enctype="multipart/form-data">
&nbsp;&nbsp; &nbsp;用户名|姓名
      <input type="text" name="xm" id="xm" style="border:1px solid #ccc;height:20px;"/>
&nbsp;&nbsp;
<label>
<input name="sh1" type="checkbox" id="sh1" value="0" />
</label>  
未审核&nbsp;
<label>
<input name="sh1" type="checkbox" id="sh1" value="2" />
</label>  
已审核
<label></label>
<input type="submit" name="button" id="button" value="搜索"  style="background-color:#F63;color:#FFF;border:0px;line-height:14px;height:22px;cursor:pointer;"/>
	 </form>  </td>
	</tr>
<tr>
  <td width="11%" height="29" align="center" bgcolor="#FFFFFF" class="bk"><span class="btdx">

用户名</span></td>
  <td width="8%" align="center" class="bk"><span class="btdx">IP</span></td>
  <td width="8%" align="center" class="bk"><span class="btdx">状态</span></td>
  <td width="7%" align="center"  class="bk"><span class="btdx">姓名</span></td>
  <td width="5%" align="center"  class="bk"><span class="btdx">会员类型</span></td>
  <td width="5%" align="center" class="bk"><span class="btdx">身份证号</span></td>
  <td width="5%" align="center" class="bk"><span class="btdx">赠送权</span></td>
  <td width="4%" align="center" class="bk"><span class="btdx">积分</span></td>
  <td width="6%" align="center" class="bk"><span class="btdx">消费积分</span></td>
  <td width="9%" align="center" class="bk"><span class="btdx">养老积分</span></td>
  <td width="7%" align="center" class="bk"><span class="btdx">详细地址</span></td>
  <td width="5%" align="center" class="bk"><span class="btdx">注册日期</span></td>
  <td width="11%" align="center" class="bk"><span class="btdx">地区</span></td>
  <td width="4%" align="center" class="bk"><span class="btdx">手机号码</span></td>
  <td width="5%" align="center" class="bk"><span class="btdx">操作面板</span></td>
</tr>
<?php
$sh = isset($_POST['sh'])?strip_tags($_POST['sh']):null;
// $sh=strip_tags($_POST['sh']);
// $sh1=strip_tags($_POST['sh1']);
$sh1 =isset($_POST['sh1'])?strip_tags($_POST['sh1']):null;
error_reporting(0);
include"config/check.php";
include "../myphplib/db.php";
 
//省份
$yh=$_COOKIE['zt_user2'];
$sql="select * from zt_qx where yh='$yh'";
$qs1=mysql_query($sql);
$sf1=mysql_fetch_assoc($qs1);
$sf=$sf1["area"];
if($yh=="admin"||$sf=='全国站长'){
$key="";
}else{
$key=" and a like '%".$sf."%'";
}

include "../myphplib/page.php";


?>
<?php
$so=trim(htmlspecialchars($_POST['xm']));
$phpfile = 'zt_user_list.php';
$page= isset($_GET['page'])?$_GET['page']:1;
if($so<>"" or $sh1<>""){
if($so==""){
$q="select *  from xbmall_users  where is_cj is null  and  (ck='$sh1' or ck='3'  or ck='1' ) and lx='0' ".$key."  order by id desc";
}else{
$q="select *  from xbmall_users  where is_cj is null  and (user like '%$so%' or xm like '%$so%')  and lx='0' ".$key."   order by id desc";
}

}else{
$q="select *  from xbmall_users  where is_cj is null  and  lx='0' ".$key." order by id desc";
}
$query=mysql_query($q);
$counts = mysql_num_rows($query);
$getpageinfo=page($page,$counts,$phpfile);
$sql.=$getpageinfo['sqllimit'];
$data=$row=array();
$result=mysql_query($sql,$db);
while($row=mysql_fetch_array($result)){

$data[]=$row;
}
if($page>1){
$page=12*$page-12+1;
}
if($so<>"" or $sh<>""){
$col=1000;   
}else{
$col=12;   
}
$cols=1;  
$page=$page-1;


if($so<>"" or $sh1<>""){
if($so==""){
$query1="select *  from xbmall_users where  is_cj is null and   (ck='$sh1' or ck='3' or ck='1') and lx='0' ".$key."  order by id desc limit $page,1000";
}else{
$query1="select *  from xbmall_users where  is_cj is null  and (user like '%$so%' or xm like '%$so%') and lx='0' ".$key." order by id desc limit $page,12";
}
}else{
$query1="select *  from xbmall_users  where  is_cj is null  and lx='0'  ".$key." order by id desc limit $page,12";
}

 
 $result= mysql_query($query1);   

  while($row1=mysql_fetch_array($result)) {   

      $col=$col+ 1;   

      if($col%$cols ==1)
  ?>
  
<tr bgcolor=#cccccc>
 <td height="79" align="center" bgcolor="#FFFFFF" class="bk"><span style="color:blue"><?php echo $row1['user']?></span> 
   <?php
	if($row1['state']=="1"){
$p="0";
	}else{
	$p="1";

	}
	?>
   <a href="lock.php?pid=<?php echo $row1['user']?>&p=<?php echo $p;?>">
     <?php
	if($row1['state']=="1" and $row1['user']!=="admin"){
	echo "[解锁]";
	}else{
	echo "[拉黑]";
	}
	?>
   </a><br><a href="pass_mod.php?pid=<?php echo $row1['user']?>">[修改密码]</a></td>
    <td align="center" bgcolor="#FFFFFF" style="color:red;" class="bk"><?php echo $row1

['ip']?> </td>
    <td align="center" bgcolor="#FFFFFF" class="bk"><?php 
	if($row1['state']=='0'){
	echo "正常";
	}
		if($row1['state']=='1'){
	echo "<font color=red>已拉黑</font>";
	}
	?>    </td>
    <td align="center" bgcolor="#FFFFFF" class="bk"><span  style="color:red;"><?php echo 

$row1['xm']?><br></span><a href="../admin_center.html?user=<?php echo $row1['user']?>"><img src="images/jf.png" width="98" height="25" border="0"></a></td>
    <td align="center" bgcolor="#FFFFFF" class="bk"><span style="color:#666;"><?php  
	if($row1['lx']=="1"){
	echo "商家";
	}else{
echo "个人会员";
		}
	?></span></td>
    <td align="center" bgcolor="#FFFFFF" class="bk"><?php echo $row1

['sfzh']?></td>
    <td align="center" bgcolor="#FFFFFF" class="bk"><?php echo $row1['zsq']?$row1['zsq']:0;?></td>
    <td align="center" bgcolor="#FFFFFF" class="bk"><span style="color:red;"><?php echo $row1['jf']?$row1['jf']:0;?></span></td>
    <td align="center" bgcolor="#FFFFFF" class="bk"><span style="color:red;"><?php echo $row1['xfjf']?$row1['xfjf']:0;?></span></td>
    <td align="center" bgcolor="#FFFFFF" class="bk"><span style="color:red;"><?php echo $row1['yljf']?$row1['yljf']:0;?></span></td>
    <td align="center" bgcolor="#FFFFFF" class="bk"><?php echo $row1['xxdz']?></td>
    <td align="center" bgcolor="#FFFFFF" class="bk"><?php echo $row1

['zcrq']?></td>
    <td align="center" bgcolor="#FFFFFF" class="bk"><?php echo $row1['a']?> <?php echo $row1['b']?> <?php echo $row1['c']?></td>
    <td align="center" bgcolor="#FFFFFF" class="bk"><?php echo $row1['sjhm']?></td>

    <td align="center" bgcolor="#FFFFFF" class="bk">
      <?php
	  if($row1['ck']=="2"){
	 	  echo '<font color="green">已审核</font>';
	  }
	    if($row1['ck']=="0" or $row1['ck']==""){
	  echo '<font color="red">未审核</font>';
	  }
	    if($row1['ck']=="1"){
	  echo '<font color="orange">未通过</font>';
	  }
	  
	  ?> 	
  <button type="submit" data-event="ts" style="background-color: #F60;color:#FFF;width:62px;height:25px;cursor:pointer;" name="<?=$row1['id'];?>">审核</button>
 <?
if($yh=="admin"){
?><button type="submit" data-event="ts1" style="background-color:#F00;color:#FFF;width:62px;height:25px;cursor:pointer;" name="<?=$row1['id'];?>">删除</button>
<?
}?>
 
  <br>备注：<?=$row1['pid'];?></td>
    </tr>

	<?php

echo   "</td>\n";   


      if   ($col%$cols==0)  

       echo   "</tr>\n";   

  }   

  if($col%$cols!=0){   

      for($i=1;$i<=$cols-$col%$cols; $i++) {   

          echo   "<td>&nbsp;</td>\n";   

      }   

  }   

  if($i>1) 
    echo   "</tr>\n";   
	 echo   "</table>\n";   

@mysql_close($db);

?>



<table width="100%" height="24" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="38" align="left"> </td>
    <td width="1373" align="left">
	<?php
echo '<BR>'.$getpageinfo['pagecode'];
?></td>
  </tr>
</table>
</table>



</body>

</html>
