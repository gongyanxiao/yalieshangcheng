<?php
if($_COOKIE['i']<>"1"){
exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>赠送积分</title>

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
 $('button[data-event=ck').on('click',function(){
var d=dialog({
title: '赠送明细',
content: '<iframe src="zt_jf_view.php" width="620" height="90" frameborder="0" scrolling="auto" ></iframe>',
 ok: function(){
//提交到后台查询
var url="<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];?>";
var dataString='url='+url; 
$.ajax({  
type: "POST",  
url: "zt_jf_view.php",  
data:dataString,  
cache:false,  
success:function(html){  
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
  
  function confirmAct(){ 
    if(confirm('确定要执行赠送操作吗?')) 
    { 
        return true; 
    } 
    return false; 
} 
// --> 
</script> 

</head>

<body>  
  
<div style="z-index:100:width:100%;height:22px;">

<table width="100%" border="0" height="22px;" cellpadding="0" cellspacing="0">
  <tr>
    <td width="2%" height="22"><img src="images/position1.jpg"></td>
    <td width="98%" align="left" background="images/position2.jpg">您当前的位置>>赠送积分操作界面</td>
  </tr>
</table>

</div>
<div id="jsgj"></div>


  <table width="100%" border=0 align="center" cellspacing=0 bordercolorlight="#94D0EA" 

bordercolordark="#F5FBFE" style="margin-top:15px;">
<tr>
    <td height="35" colspan="13" align="left" bgcolor="#DBEBFA"><table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td width="36%" height="53" align="left" valign="middle"><form method="post" enctype="multipart/form-data">
用户名
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
<input type="submit" name="button" id="button" value="搜索"  style="background-color:#F63;color:#FFF;border:0px;line-height:14px;height:22px;cursor:pointer;"/>
	 </form></td>
        <td width="14%" align="center" valign="middle"> <a href="zt_jf_pro.php" onclick="return confirmAct();"><img src="images/zs.png" border="0"/></a></td>
        <td width="12%" valign="middle"> <button type="submit" data-event="ck" style="background-color:#F60;color:#FFF;width:100px;height:25px;cursor:pointer;font-weight:bold;" id="<?=$row1["user"];?>" name="<?=$row1["user"];?>">赠送明细</button></td>
        <td width="38%" align="center" valign="middle">
        <?php
		include"config/check.php";
include "../config/zt_config.php";
$db = mysql_connect("$db_host","$db_user","$db_pwd");
mysql_query("set names $coding"); 
mysql_select_db("$db_database");
	
		?>
        <form method="post" enctype="multipart/form-data" name="jf" action="?pro=1">
        <?php
		$jf9=htmlspecialchars($_GET['pro']);
		$jf1=htmlspecialchars($_POST['jf1']);
				$jf2=htmlspecialchars($_POST['jf2']);
				$jf3=htmlspecialchars($_POST['jf3']);
		if($jf9=="1"){
$sql4="INSERT INTO  `zt_ed`(`id`,`jf1`,`jf2`,`jf3`) VALUES(null,'$jf1','$jf2','$jf3');";
mysql_query($sql4);
			}
	$sql1="select * from zt_ed  order by id desc";
	$q1=mysql_query($sql1);
	$ss=mysql_fetch_assoc($q1);
		?>
可兑换
      <input name="jf1" type="text" id="jf1" style="border:1px solid #ccc;height:20px;width:60px;" value="<?=$ss['jf1'];?>"/>
       &nbsp;商品兑换
       <input name="jf2" type="text" id="jf2" style="border:1px solid #ccc;height:20px;width:60px;" value="<?=$ss['jf2'];?>"/>
       消费养老
       <input name="jf3" type="text" id="jf3" style="border:1px solid #ccc;height:20px;width:60px;" value="<?=$ss['jf3'];?>"/>
       <input type="submit" name="button" id="button" value="额度设置"  style="background-color:#F63;color:#FFF;border:0px;line-height:14px;height:22px;cursor:pointer;"/>
	 </form></td>
      </tr>
    </table>	  </td>
	</tr>
<tr>
  <td width="9%" height="29" align="center" bgcolor="#FFFFFF" class="bk"><span class="btdx">

用户名</span></td>
  <td width="5%" align="center" class="bk"><span class="btdx">IP</span></td>
  <td width="5%" align="center" class="bk"><span class="btdx">状态</span></td>
  <td width="6%" align="center"  class="bk"><span class="btdx">姓名</span></td>
  <td width="6%" align="center"  class="bk"><span class="btdx">会员类型</span></td>
  <td width="6%" align="center" class="bk"><span class="btdx">身份证号</span></td>
  <td width="7%" align="center" bgcolor="#FF6600" class="bk"><span style="color:#FFF;"><strong>赠送权</strong></span></td>
  <td width="6%" align="center" class="bk"><span style="color:orange;">可兑换</span></td>
  <td width="6%" align="center" class="bk"><span style="color:#39F;">商品兑换</span></td>
  <td width="6%" align="center" class="bk"><span  style="color:#960;">消费养老</span></td>
  <td width="13%" align="center" class="bk"><span class="btdx">注册日期</span></td>
  <td width="9%" align="center" class="bk"><span class="btdx">联系电话</span></td>
  <td width="16%" align="center" class="bk"><span class="btdx">地区</span></td>
  </tr>
<?php
$sh=strip_tags($_POST['sh']);
$sh1=strip_tags($_POST['sh1']);
error_reporting(0);

//省份
$yh=$_COOKIE['zt_user2'];
$sql="select * from zt_qx where yh='$yh'";
$qs1=mysql_query($sql);
$sf1=mysql_fetch_assoc($qs1);
$sf=$sf1["area"];
function page($page,$total,$phpfile,$pagesize=12,$pagelen=20){ 
$pagecode = '';//定义变量，存放分页生成的HTML 
$page = intval($page);//避免非数字页码 
$total = intval($total);//保证总记录数值类型正确 
if(!$total) return array();//总记录数为零返回空数组 
$pages = ceil($total/$pagesize);//计算总分页 
//处理页码合法性 
if($page<1) $page = 1; 
if($page>$pages) $page = $pages; 
//计算查询偏移量 
$offset = $pagesize*($page-1); 
//页码范围计算 

$init = 1;//起始页码数 

$max = $pages;//结束页码数 

$pagelen = ($pagelen%2)?$pagelen:$pagelen+1;//页码个数 

$pageoffset = ($pagelen-1)/2;//页码个数左右偏移量 



//生成html 

$pagecode='<div class="page">'; 

$pagecode.="<span>$page/$pages</span>";//第几页,共几页 

//如果是第一页，则不显示第一页和上一页的连接 

if($page!=1){ 

$pagecode.="<a href=\"{$phpfile}?page=1\">首页</a>";//第一页 

$pagecode.="<a href=\"{$phpfile}?page=".($page-1)."\">上一页</a>";//上一页 

} 

//分页数大于页码个数时可以偏移 

if($pages>$pagelen){ 

//如果当前页小于等于左偏移 

if($page<=$pageoffset){ 

$init=1; 

$max = $pagelen; 

}else{//如果当前页大于左偏移 

//如果当前页码右偏移超出最大分页数 

if($page+$pageoffset>=$pages+1){ 

$init = $pages-$pagelen+1; 

}else{ 

//左右偏移都存在时的计算 

$init = $page-$pageoffset; 

$max = $page+$pageoffset; 

} 

} 

} 

//生成html 

for($i=$init;$i<=$max;$i++){ 

if($i==$page){ 

$pagecode.='<span>'.$i.'</span>'; 

} else { 

$pagecode.="<a href=\"{$phpfile}?page={$i}\">$i</a>"; 

} 

} 

if($page!=$pages){ 

$pagecode.="<a href=\"{$phpfile}?page=".($page+1)."\">下一页</a>";//下一页 

$pagecode.="<a href=\"{$phpfile}?page={$pages}\">尾页</a>";//最后一页 

} 

$pagecode.='</div>'; 

return array('pagecode'=>$pagecode,'sqllimit'=>' limit '.$offset.','.$pagesize); 

} 

?>
<?php
$so=trim(htmlspecialchars($_POST['xm']));
$phpfile = 'zt_chongzhi_list.php';
$page= isset($_GET['page'])?$_GET['page']:1;

if($so<>"" or $sh1<>""){

if($so==""){
$q="select *  from xbmall_users  where  (ck='$sh1' or ck='3'  or ck='1' )    order by id desc";
}else{
$q="select *  from xbmall_users  where (user like '%$so%')    order by id desc";
}

}else{
$q="select *  from xbmall_users   order by id desc";
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
$query1="select *  from xbmall_users where    (ck='$sh1' or ck='3' or ck='1')    order by id desc limit $page,1000";
}else{
$query1="select *  from xbmall_users where  (user like '%$so%')   order by id desc limit $page,12";
}
}else{
$query1="select *  from xbmall_users    order by id desc limit $page,12";
}


 $result= mysql_query($query1);   

  while($row1=mysql_fetch_array($result)) {   

      $col=$col+ 1;   

      if($col%$cols ==1)
  ?>
  
<tr bgcolor=#cccccc>
 <td height="79" align="center" bgcolor="#FFFFFF" class="bk"><span style="color::#39F;"><?php echo $row1['user']?></span> 
   <?php
	if($row1['state']=="1"){
$p="0";
	}else{
	$p="1";

	}
	?>
   <a href="lock.php?pid=<?php echo $row1['user']?>&p=<?php echo $p;?>" title="<?php echo 

$row1['xm']?>">   </a><br></td>
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
    <td align="center" bgcolor="#FFFFFF" class="bk"><span  style=" color:#39F;"><?php echo 

$row1['xm']?></span></td>
    <td align="center" bgcolor="#FFFFFF" class="bk"><span style="color:#666;"><?php  
	if($row1['lx']=="1"){
	echo "商家";
	}else{
echo "个人会员";
		}
	?></span></td>
    <td align="center" bgcolor="#FFFFFF" class="bk"><?php echo $row1

['sfzh']?></td>
    <td align="center" bgcolor="#FFFFFF" class="bk"><?php
    $user1=$row1['user'];
	$sql1="select zsq from zt_zsq where user='$user1' order by id desc";
	$q1=mysql_query($sql1);
	$ss=mysql_fetch_assoc($q1);
	echo $ss['zsq']?$ss['zsq']:0;
	?></td>
    <td align="center" bgcolor="#FFFFFF" class="bk"><span style="color:red;font-size:14px; font-family:Arial, Helvetica, sans-serif;"><?php echo $row1['jf']?$row1['jf']:0;?></span></td>
    <td align="center" bgcolor="#FFFFFF" class="bk"><span style="color:red;font-size:14px;font-family:Arial, Helvetica, sans-serif;"><?php echo $row1['xfjf']?$row1['xfjf']:0;?></span></td>
    <td align="center" bgcolor="#FFFFFF" class="bk"><span style="color:red;font-size:14px;font-family:Arial, Helvetica, sans-serif;"><?php echo $row1['yljf']?$row1['yljf']:0;?></span></td>
    <td align="center" bgcolor="#FFFFFF" class="bk"><?php echo $row1['zcrq']?></td>
    <td align="center" bgcolor="#FFFFFF" class="bk"><?php echo $row1['lxdh']?$row1['lxdh']:$row1['sjhm'];?></td>
    <td colspan="2" align="center" bgcolor="#FFFFFF" class="bk"><?php echo $row1['a']?> <?php echo $row1['b']?> <?php echo $row1['c']?></td>
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
