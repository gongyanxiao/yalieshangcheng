<?php
 
function getShenFen($level) {
	if ($level == 1) {
		return "初级代理";
	} else if ($level == 2) {
		return "中级代理";
	} else if ($level == 3) {
		return "高级代理";
	} else if ($level == 4) {
		return "运营中心";
	} else if ($level == 5) {
		return "懂事";
	} else {
		return "会员";
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>统计</title>

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
	.rotate_jia {
		display: none;
	}
	.rotate_div {
		display: none;
	}
	.rotate_jian {
		display: none;
	}
}

body, td, th {
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

.nav8 a:link {
	color: #0085B6;
	text-decoration: none;
	font-size: 12px;
	font-family: 宋体;
}

.nav8 a:visited {
	color: #0085B6;
	text-decoration: none;
	font-size: 12px;
	font-family: 宋体;
}

.nav8 a:hover {
	color: #f00000;
	text-decoration: none;
	font-size: 12px;
	font-family: 宋体;
}

.page {
	padding: 3px;
	font-weight: normal;
	font-size: 12px;
}

.page a {
	border: 1px solid #0085B6;
	padding: 3px;
	margin: 2px;
	font-size: 12px;
	text-decoration: none;
	color: #0085B6;
}

.page span {
	padding: 3px;
	margin: 2px;
	background: #ffffff;
	color: #0085B6;
	border: 1px solid #0085B6;
}

.ss {
	color: #FF6602;
	font-size: 14px;
	font-weight: bold;
}

.ssborder {
	color: #FF6602;
	font-size: 14px;
	border: 1px #FF9900 solid;
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

.bk {
	border-bottom: 1px #999 dotted;
}
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
  
  <?php
		
		error_reporting ( 0 );
		
		include "../myphplib/db.php";
		include "../myphplib/message.php";
		include "config/check.php";
		include "../myphplib/page.php";
		
		$so = trim ( htmlspecialchars ( $_GET['xm'] ) );
		$type = trim ( htmlspecialchars ( $_GET['type'] ) );
		if(!isset($type) || $type==''){
			$type =1;
		}
		
		$typeStr="a.xxtzztxjf+a.xxtzztxjf_b";//静态收益
		if($type==1){//动态收益
			$typeStr="a.tui_jian_shou_yi";
		}
		
		 
		$phpfile = 'tong_ji_fen_hong.php?type='.$type;
		$page = isset ( $_GET ['page'] ) ? $_GET ['page'] : 1;
		if ($so != "") {
			$phpfile .= '&xm='.$so."&";
			$q = "SELECT a.user, SUM(".$typeStr."),a.zsrq, b.real_name  FROM `zt_xxtzzs` a JOIN  xbmall_users b  on  a.user=b.user_name WHERE  a.type =".$type." and  a.user LIKE '%".$so."%' GROUP BY  a.`user` ,a.zsrq    ";
		} else {
			$q = "SELECT a.user, SUM(".$typeStr."),a.zsrq, b.real_name  FROM `zt_xxtzzs` a JOIN  xbmall_users b  on  a.user=b.user_name WHERE  a.type =".$type."  and  a.user LIKE '%".$so."%' GROUP BY  a.`user` ,a.zsrq   ";
		}
		
		$query = mysql_query ( $q );
		$counts = mysql_num_rows ( $query );
		$getpageinfo = page ( $page, $counts, $phpfile );
		$sql .= $getpageinfo ['sqllimit'];
		$data = $row = array ();
		$result = mysql_query ( $sql, $db );
		while ( $row = mysql_fetch_array ( $result ) ) {
			$data [] = $row;
		}
		if ($page > 1) {
			$page = 12 * $page - 12 + 1;
		}
		
		$page = $page - 1;
		if ($so != "") {
			$query1 = "SELECT a.user, SUM(".$typeStr.") as shou_yi,a.zsrq, b.real_name  FROM `zt_xxtzzs` a JOIN  xbmall_users b  on  a.user=b.user_name WHERE  a.type =".$type."  and  a.user LIKE '%".$so."%' GROUP BY  a.`user` ,a.zsrq  ORDER BY  a.zsrq desc    limit $page,12";
		} else {
			$query1 = "SELECT a.user, SUM(".$typeStr.") as shou_yi,a.zsrq, b.real_name  FROM `zt_xxtzzs` a JOIN  xbmall_users b  on  a.user=b.user_name WHERE  a.type =".$type."    GROUP BY  a.`user` ,a.zsrq  ORDER BY  a.zsrq desc     limit $page,12";
		}
		
		?>
<div style="z-index: 100:width:100%; height: 22px;">

		<table width="100%" border="0" height="22px;" cellpadding="0"
			cellspacing="0">
			<tr>
				<td width="2%" height="22"><img src="images/position1.jpg"></td>
				<td width="98%" align="left" background="images/position2.jpg">您当前的位置>>统计</td>
			</tr>
		</table>

	</div>
	<div id="jsgj"></div>


	<table width="100%" border=0 align="center" cellspacing=0
		bordercolorlight="#94D0EA" bordercolordark="#F5FBFE"
		style="margin-top: 15px;">
		<tr>
			<td height="35" colspan="15" align="left" bgcolor="#DBEBFA">
				<form method="get" action="tong_ji_fen_hong.php"
					enctype="multipart/form-data">
					&nbsp;&nbsp; &nbsp;用户账号<input type="text" name="xm" id="xm"
						value="<?php echo $_GET['xm'];?>"
						style="border: 1px solid #ccc; height: 20px;" /> <input
						type="submit" name="button" id="button" value="搜索"
						style="background-color: #F63; color: #FFF; border: 0px; line-height: 14px; height: 22px; cursor: pointer;" />
					<input type="radio" name="type" value="1" <?php if($type==1) echo  "checked='checked'";?>/>	动态收益 
					<input type="radio" name="type" value="3" <?php if($type==3) echo  "checked='checked'";?>/>	静态收益  
						
				</form>  <?php echo   $counts;?>个超级会员</td>
		</tr>
		<tr>
			<td width="4%" align="center" class="bk"><span class="btdx">姓名</span></td>
			<td width="4%" align="center" class="bk"><span class="btdx">账号</span></td>
			<td width="4%" align="center" class="bk"><span class="btdx">日期</span></td>
			<td width="4%" align="center" class="bk"><span class="btdx">收益</span></td>
		</tr>
<?php
$result = mysql_query ( $query1 );
while ( $row1 = mysql_fetch_array ( $result ) ) {
	?>
  
<tr bgcolor=#cccccc>
			<td align="center" bgcolor="#FFFFFF" class="bk"><span
				style="color: blue"><?php echo $row1['real_name']?></span></td>

			<td align="center" bgcolor="#FFFFFF" class="bk"><span
				style="color: blue"><?php echo $row1['user']?></span></td>
<td align="center" bgcolor="#FFFFFF" class="bk"><span
				style="color: red;"><?php echo $row1['zsrq']?></span></td>
			<td align="center" bgcolor="#FFFFFF" class="bk"><span
				style="color: red;"><?php echo $row1['shou_yi']?></span></td>
		</tr>

	<?php
}


?>
</table>
 

	<table width="100%" height="24" border="0" cellpadding="0"
		cellspacing="0">
		<tr>
			<td width="38" align="left"></td>
			<td width="1373" align="left">
	<?php
	 echo '<BR>' . $getpageinfo ['pagecode'];
	?></td>
		</tr>
	</table>




</body>

</html>
