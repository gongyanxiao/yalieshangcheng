<?php
if($_COOKIE['h']<>"1"){
exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>权限管理</title>


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

<script src="../lib/sea.js"></script>
<script>
seajs.config({
  alias: {
    "jquery": "../lib/jquery-1.10.2.js"
  }
});
</script>

<script type=text/javascript src="../js/jquery.min.js"></script>
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
var dataString='id='+id+'&bz='+bz+'&sh='+sh; 
$.ajax({  
type: "POST",  
url: "zt_smrz_pro.php",  
data:dataString,  
cache:false,  
success:function(html){  
//$("#fhl").html("<pre>" + html + "</pre>");
$("#ryd").html(html);
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
</SCRIPT> 

</head>

<body>  
   
<div style="z-index:100:width:100%;height:22px;">

<table width="100%" border="0" height="22px;" cellpadding="0" cellspacing="0">
  <tr>
    <td width="2%" height="22"><img src="images/position1.jpg"></td>
    <td width="98%" align="left" background="images/position2.jpg">您当前的位置>>权限管理</td>
  </tr>
</table>

</div>
<div id="ryd"></div>


  <table width="100%" border=0 align="center" cellspacing=0 bordercolorlight="#94D0EA" bordercolordark="#F5FBFE" style="margin-top:15px;">
<tr>
    <td height="35" colspan="7" align="left" bgcolor="#DBEBFA">
	 <form method="post" enctype="multipart/form-data">
&nbsp;&nbsp; &nbsp;用户名
      <input type="text" name="xm" id="xm" style="border:1px solid #ccc;height:20px;"/>
&nbsp;&nbsp;  &nbsp;
  <input type="submit" name="button" id="button" value="搜索" />
  </form>
  </td>
	</tr>
<tr>
  <td height="29" colspan="2" align="center" bgcolor="#FFFFFF" class="bk"><span class="btdx">用户名</span></td>
  <td width="15%" align="center" class="bk"><span class="btdx">IP</span></td>
  <td width="21%" align="center" class="bk"><span class="btdx">状态</span></td>
  <td width="17%" align="center"  class="bk"><span class="btdx">姓名</span></td>
  <td width="13%" align="center"  class="bk">&nbsp;</td>
  <td width="13%" align="center" class="bk"><span class="btdx">操作面板</span></td>
</tr>
<?php
error_reporting(0);
include"config/check.php";
include "../config/zt_config.php";
$db = mysql_connect("$db_host","$db_user","$db_pwd");
mysql_query("set names $coding"); 
mysql_select_db("$db_database");
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
$phpfile = 'zt_qx_list.php';
$page= isset($_GET['page'])?$_GET['page']:1;
if($so<>""){
$q="select *  from xbmall_users  where (user like '%$so%')  order by id desc";
}else{
$q="select *  from xbmall_users  order by id desc";
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
$col=12;   
$cols=1;  
$page=$page-1;

if($so<>""){
$query1="select *  from xbmall_users where (user like '%$so%')  order by id desc limit $page,12";



}else{
$query1="select *  from xbmall_users   order by id desc limit $page,12";
}
 $result= mysql_query($query1);   

  while($row1=mysql_fetch_array($result)) {   

      $col=$col+ 1;   

      if($col%$cols ==1)
  ?>
  
<tr bgcolor=#cccccc>
 <td width="2%" height="79" align="center" bgcolor="#FFFFFF" class="bk"><input name="ch" type="checkbox" id="ch" value="<?php echo $row1['id']?>"/></td>
    <td width="19%" align="left" bgcolor="#FFFFFF" class="bk"><?php echo $row1['user']?> 
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
	
	
	</a></td>
    <td align="center" bgcolor="#FFFFFF" style="color:red;" class="bk"><?php echo $row1['ip']?> <a href="pass_mod.php?pid=<?php echo $row1['user']?>">[修改密码]</a></td>
    <td align="center" bgcolor="#FFFFFF" class="bk"><?php 
	if($row1['state']=='0'){
	echo "正常";
	}
		if($row1['state']=='1'){
	echo "<font color=red>已拉黑</font>";
	}
	?>    </td>
    <td align="center" bgcolor="#FFFFFF" class="bk"><span  style="color:red;"><?php echo $row1['xm']?></span></td>
    <td align="center" bgcolor="#FFFFFF" class="bk">&nbsp;</td>
    <td align="center" bgcolor="#FFFFFF" class="bk"><a href="zt_mod.php?id=<?=$row1['user']?>">修改权限</a></td>
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
