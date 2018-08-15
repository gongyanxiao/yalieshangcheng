<?php
if($_COOKIE['e']<>"1"){
exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>保单管理</title>

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


<script language=javascript> 
<!-- 
seajs.use(['jquery', '../src/dialog'],function ($, dialog){
$('button[data-event=ts]').on('click',function(){
var id= $(this).attr("name");
var d = dialog({
title: '审核操作',

<?php
$yh=$_COOKIE['zt_user2'];
?>
content:'&nbsp;&nbsp;&nbsp;&nbsp;<select name="sh" id="sh"><option value="2">已处理</option><option value="0">未处理</option></select><br>备注:<textarea name="bz" id="bz" cols="45" rows="5"></textarea><br>',
 ok: function(){
//提交到后台查询
var  sh= $('#sh').val();
var  bz= $('#bz').val();
var url="<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];?>";
var dataString='id='+id+'&bz='+bz+'&sh='+sh; 
$.ajax({  
type: "POST",  
url: "zt_bd_pro.php",  
data:dataString,  
cache:false,  
success:function(html){  
//$("#fhl").html("<pre>" + html + "</pre>");
$("#jygj").html(html);
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
</script> 

</head>

<body>  
  <div id="jygj"></div>
<div style="z-index:100:width:100%;height:32px;">

<table width="100%" border="0" height="32px;" cellpadding="0" cellspacing="0">
  <tr>
    <td width="2%" height="22" background="images/position1.jpg"></td>
    <td width="98%" align="left" background="images/position2.jpg">您当前的位置 >>保单管理  
&nbsp;&nbsp;&nbsp;&nbsp;<span style="width:500px;"></span></td>
  </tr>
</table>

</div>
<div id="jsgj"></div>


  <table width="100%" border=0 align="center" cellspacing=0 bordercolorlight="#94D0EA" 

bordercolordark="#F5FBFE" style="margin-top:15px;">
<tr>
    <td height="36" colspan="12" align="left" bgcolor="#DBEBFA">
      <form method="post" enctype="multipart/form-data" style="width:500px;">
  &nbsp;&nbsp; &nbsp;用户名
        <input type="text" name="xm" id="xm" style="border:1px solid #ccc;height:20px;"/>
  &nbsp;&nbsp;&nbsp;
<input type="submit" name="button" id="button" value="搜索记录"  style="background-color:#F63;color:#FFF;border:0px;line-height:14px;height:22px;cursor:pointer;"/>
      </form></td>
    </tr>

<tr>
  <td width="5%" height="29" align="center" bgcolor="#FFFFFF" class="bk"><span class="btdx">ID</span></td>
  <td width="7%" align="center" class="bk"><span class="btdx">项目分类</span></td>
  <td width="11%" align="center" class="bk"><span class="btdx">生成保单日期</span></td>
  <td width="9%" align="center" class="bk"><span class="btdx">用户名</span></td>
  <td width="7%" align="center" class="bk"><span class="btdx">扣除养老积分</span></td>
  <td width="10%" align="center" class="bk"><span class="btdx">状态</span></td>
  <td width="41%" align="center" class="bk"><span class="btdx">说明</span></td>
  <td width="10%" align="center" class="bk"><span class="btdx">操作面板</span></td>
</tr>
<?php
$sh=strip_tags($_POST['sh']);
$sh1=strip_tags($_POST['sh1']);
error_reporting(0);
include"config/check.php";
include "../config/zt_config.php";
$db = mysql_connect("$db_host","$db_user","$db_pwd");
mysql_query("set names $coding"); 
mysql_select_db("$db_database");
//省份
$sql="select * from zt_qx where yh='$yh'";
$qs1=mysql_query($sql);
$sf1=mysql_fetch_assoc($qs1);
$sf=$sf1["area"];
if($yh=="admin" or $yh="15988152863"){
$sf="";
}else{
$sf=" where  a like '%".$sf."%' ";
}
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

$pagecode.="<a href=\"{$phpfile}&page=1\">首页</a>";//第一页 

$pagecode.="<a href=\"{$phpfile}&page=".($page-1)."\">上一页</a>";//上一页 

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

$pagecode.="<a href=\"{$phpfile}&page={$i}\">$i</a>"; 

} 

} 

if($page!=$pages){ 

$pagecode.="<a href=\"{$phpfile}&page=".($page+1)."\">下一页</a>";//下一页 

$pagecode.="<a href=\"{$phpfile}&page={$pages}\">尾页</a>";//最后一页 

} 

$pagecode.='</div>'; 

return array('pagecode'=>$pagecode,'sqllimit'=>' limit '.$offset.','.$pagesize); 

} 

?>
<?php
$so=trim(htmlspecialchars($_POST['xm']));

if(strip_tags($_GET['page'])>='1'){
$so=trim(htmlspecialchars($_GET['xm']));
}
if($so<>""){
$phpfile = 'zt_bd_order_list.php?xm='.$so;
}else{
$phpfile = 'zt_bd_order_list.php?xm=';
}
$page= isset($_GET['page'])?$_GET['page']:1;
$q='';
if($so<>"" or $sh1<>""){

if($so==""){
$q="select * from zt_bd where user in (select user from xbmall_users $sf) order by id desc";
}else{
$q="select *  from zt_bd  where  user in (select user from xbmall_users $sf) and (user like '%$so%')    order by id desc";
}

}else{
$q="select *  from zt_bd where  user in (select user from xbmall_users $sf) and (user like '%$so%')   order by id desc";
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

$query1='';
if($so<>"" or $sh1<>""){
if($so==""){
$query1="select *  from zt_bd  where user in (select user from xbmall_users  $sf) order by id desc limit $page,1000";
}else{
$query1="select *  from zt_bd where (user like '%$so%' ) and  user in (select user from xbmall_users  $sf) order by id desc limit $page,12";
}
}else{
$query1="select *  from zt_bd  where  user like '%$so%'  and  user in (select user from xbmall_users $sf) order by id desc limit $page,12";
}

 $result= mysql_query($query1);   

  while($row1=mysql_fetch_array($result)) {   

      $col=$col+ 1;   

      if($col%$cols ==1)
  ?>
  
<tr bgcolor="#cccccc">
 <td height="79" align="center" bgcolor="#FFFFFF" class="bk"><?php echo $row1['id']?></td>
    <td align="center" bgcolor="#FFFFFF" class="bk" style="color: #39F;"><?php echo $row1['xmfl']?></td>
    <td align="center" bgcolor="#FFFFFF" class="bk" style="color: #39F;"><span class="bk" style="color: #39F;"><span style="color: #333;font-size:14px;"><?php echo $row1['date']?></span></span></td>
    <td align="center" bgcolor="#FFFFFF" class="bk" style="color: #39F;"><span style="color: #333;font-size:14px;"><?php echo $row1['user'];?></span></td>
    <td align="center" bgcolor="#FFFFFF" class="bk" style="color: #39F;"><span style="color:red;font-size:14px;"><?php echo $row1['jf']?></span></td>
    <td align="center" bgcolor="#FFFFFF" class="bk" style="color: #39F;"><span  style="color:red;font-size:14px;"><?php if($row1['zt']=="0"){echo "未处理";}else{echo '<font color="green">已处理</font>';}?></span></td>
    
    <td align="center" bgcolor="#FFFFFF" class="bk"><?php echo $row1['bz']?$row1['bz']:"无";?></td>
    <td align="center" bgcolor="#FFFFFF" class="bk"><?php
	  if($row1['zt']=="2"){
	 	  echo '<font color="green">已处理</font>';
	  }
	    if($row1['zt']=="0" or $row1['zt']==""){
	  echo '<font color="red">未处理</font>';
	  }
	    if($row1['zt']=="1"){
	  echo '<font color="orange">未处理</font>';
	  }
	  
	  ?> 	
  <button type="submit" data-event="ts" style="background-color: #F60;color:#FFF;width:62px;height:25px;cursor:pointer;" name="<?=$row1['id'];?>">审核</button><br></td>
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
