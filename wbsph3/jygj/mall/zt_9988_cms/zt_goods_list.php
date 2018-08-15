<?php
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
<link type="text/css" rel="stylesheet" href="css/showBo.css" />
<script type="text/javascript" src="js/showBo.js"></script>
<script type=text/javascript src="../js/jquery.min.js"></script>
<script language=javascript> 
<!-- 
//增加商品
function add(){
location.href="zt_goods_add.php";
	}
// --> 
</script> 

</head>

<body>  
   
<div style="z-index:100:width:100%;height:22px;">

<table width="100%" border="0" height="22px;" cellpadding="0" cellspacing="0">
  <tr>
    <td width="2%" height="22" background="images/position1.jpg">&nbsp;</td>
    <td width="98%" align="left" background="images/position2.jpg">您当前的位置>>商品管理&gt;&gt;&nbsp;</td>
  </tr>
</table>

</div>
<div style="margin-top:20px;">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="11%" height="32" align="center" bgcolor="#CAEEFF" ><input type="submit" name="button2" id="button2" value="增加商品" style="background-color:#F63;color:#FFF;border:0px;line-height:14px;height:22px; cursor:hand;" onclick="add();"/>
      &nbsp;</td>
    <td width="82%" align="left" bgcolor="#CAEEFF" ><form method="post" enctype="multipart/form-data" 

style="margin:0px;">
      &nbsp;&nbsp;
      
      &nbsp;&nbsp;&nbsp; 检索名称&nbsp;
      <input type="text" name="xm" id="xm" style="border:1px solid #ccc;height:20px;"/>
      &nbsp;
      <input type="submit" name="button" id="button" value="数据检索" style="background-color:#F63;color:#FFF;border:0px;line-height:14px;height:22px;cursor:hand;"/>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    </form></td>
    <td width="7%" bgcolor="#CAEEFF" >&nbsp;</td>
  </tr>
</table></div>
<div id="ryd"></div>


<table width="100%" border=0 align="center" cellspacing=0 bordercolorlight="#94D0EA" 

bordercolordark="#F5FBFE">
<tr>
  <td height="29" align="center" bgcolor="#FFFFFF" class="bk">&nbsp;</td>
  <td height="29" align="center" bgcolor="#FFFFFF" class="bk"><span class="btdx">商品名称</span></td>
  <td height="29" align="center" bgcolor="#FFFFFF" class="bk"><span class="btdx">店铺名称</span></td>
  <td width="8%" align="center" class="bk"><span class="btdx">商品编号</span></td>
  <td width="6%" align="center" class="bk"><span class="btdx">商品价格</span></td>
  <td width="6%" align="center"  class="bk"><span class="btdx">市场价</span></td>
  <td width="8%" align="center"  class="bk"><span class="btdx">推荐</span></td>
  <td width="8%" align="center" class="bk"><span class="btdx">上架</span></td>
  <td width="9%" align="center" class="bk"><span class="btdx">缩略图</span></td>
  <td width="7%" align="center" class="bk"><span class="btdx">发布时间</span></td>
  <td width="5%" align="center" class="bk"><span class="btdx">发布者</span></td>
  <td width="10%" align="center" class="bk"><span class="btdx">操作面板</span></td>
</tr>
<?php
error_reporting(0);
include"config/check.php";
$user=$_COOKIE["zt_user2"];
include "../config/zt_config.php";
$db=mysql_connect("$db_host","$db_user","$db_pwd");
mysql_query("set names $coding"); 
mysql_select_db("$db_database");
$sql5="select area from zt_qx where yh='$user'";
$qs1=mysql_query($sql5);
$sf1=mysql_fetch_assoc($qs1);
$sf=$sf1['area'];
if($user=="admin"||$sf=='全国站长'){
$key="";
}else{
$key=" and sf like '%".$sf."%'";
}
//删除操作记录
$delid=trim($_GET['delid']);
if($delid<>""){
$sql1="delete  from zt_goods where id='$delid';";
$delquery=mysql_query($sql1);
if($delquery){
?>
<script>
<!--
alert("删除商品信息完成");
location.replace(document.referrer); 
//-->
</script>
<?php
}
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
$phpfile = 'zt_goods_list.php';
$page= isset($_GET['page'])?$_GET['page']:1;
if($so<>""){
$q="select *  from zt_goods  where (spmc like '%$so%') ".$key." order by id desc";
}else{
$q="select *  from zt_goods where  id<>'' ".$key." order by id desc";
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
$query1="select *  from zt_goods where (spmc like '%$so%') ".$key." order by id desc limit $page,12";

}else{
$query1="select *  from zt_goods  where id<>'' ".$key." order by id desc limit $page,12";
}
 $result= mysql_query($query1);   

  while($row1=mysql_fetch_array($result)) {   

      $col=$col+ 1;   

      if($col%$cols ==1)
  ?>
  
<tr bgcolor=#cccccc>
 <td width="1%" height="79" align="center" bgcolor="#FFFFFF" class="bk"><input name="ch" 

type="checkbox" id="ch" value="<?php echo $row1['id']?>"/></td>
    <td width="21%" align="left" bgcolor="#FFFFFF" class="bk"><span class="btdx">[<?php 
	$fl=$row1['fl'];
	$sql1="select * from zt_goods_sort where id='$fl'";
$qs1=mysql_query($sql1);
$sf1=mysql_fetch_assoc($qs1);
echo $sf1['columname'];
	?>]</span><?php echo $row1['spmc']?></td>
    <td width="11%" align="center" bgcolor="#FFFFFF" class="bk"><span class="bk" style="color:red;"><?php echo $row1['dpmc']?></span></td>
    <td align="center" bgcolor="#FFFFFF" style="color:red;" class="bk"><?php echo $row1

['spbh']?></td>
    <td align="center" bgcolor="#FFFFFF" class="bk"><span  style="color:red;">￥<?php 

echo $row1['spjg']?></span></td>
    <td align="center" bgcolor="#FFFFFF" class="bk"><span>￥<?php echo 

$row1['scjg']?></span></td>
    <td align="center" bgcolor="#FFFFFF" class="bk"><span><?php if($row1['sftj']=='1'){
    echo "√";
    }else{
        echo "×";
    }?></span></td>
    <td align="center" bgcolor="#FFFFFF" class="bk"><span><?php if($row1['sfsj']=='1'){
    echo "√";
    }else{
        echo "×";
    }?></span></td>
    <td align="center" bgcolor="#FFFFFF" class="bk"><?php 
if($row1['spslt']<>""){
	$v=$row1['spslt'];
   echo '<a href="'.$v.'" target=_blank>'.'<img src="'.$v.'" width=40 height=30></a>';
   }else{
   echo '<font color="#FF0000">未上传</font>';
   }
?>
      
</td>
    <td align="center" bgcolor="#FFFFFF" class="bk"><?php echo $row1['fbsj']?></td>
    <td align="center" bgcolor="#FFFFFF" class="bk"><?php echo $row1['username']?></td>

    <td align="center" bgcolor="#FFFFFF" class="bk"><img src="images/mod.jpg"><a href="zt_goods_mod.php?id=<?=$row1

['id']?>">编辑</a>  <img src="images/del.jpg"><a href="javascript:;" onclick='p_del("<?=$row1['id'];?>");'>删除</a></td>
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

<SCRIPT LANGUAGE=javascript> 
function p_del(id) { 
var msg = "确定删除信息？\n\n请确认！"; 
if (confirm(msg)==true){ 
location.href="?delid="+id

}else{ 
location.href="/mall/zt_9988_cms/zt_goods_list.php"
} 
} 
</SCRIPT>

</body>
</html>


