 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>配置管理</title>


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

<script src="../lib/sea.js"></script>
<script>
seajs.config({
  alias: {
    "jquery": "../lib/jquery-1.10.2.js"
  }
});

function  toAddConfig(){
	self.location.href="zt_setting_add.html";
}

//分页方法用
function  f1(url){
	window.location.href="http://"+window.location.host+url;
}
</script>

<script type=text/javascript src="../js/jquery.min.js"></script>

</head>

<body>

	<div style="z-index: 100:width:100%; height: 22px;">

		<table width="100%" border="0" height="22px;" cellpadding="0"
			cellspacing="0">
			<tr>
				<td width="2%" height="22"><img src="images/position1.jpg"></td>
				<td width="98%" align="left" background="images/position2.jpg">您当前的位置>>配置管理</td>
			</tr>
		</table>

	</div>
	<div id="ryd"></div>


	<table width="100%" border=0 align="center" cellspacing=0
		bordercolorlight="#94D0EA" bordercolordark="#F5FBFE"
		style="margin-top: 15px;">
		<tr>
			<td height="35" colspan="3" align="left" bgcolor="#DBEBFA">


				<form method="post" enctype="multipart/form-data">
					<input type="button" onclick="toAddConfig()" value="增加配置"
						style="background-color: #F63; color: #FFF; border: 0px; line-height: 14px; height: 22px; cursor: hand;"
						onclick="add();"> &nbsp;&nbsp; &nbsp;配置名 <input type="text"
						name="comment" id=""
						comment""
						style="border: 1px solid #ccc; height: 20px;" /> &nbsp;&nbsp;
					&nbsp; <input type="submit" name="button" id="button" value="搜索" />
				</form>
			</td>
		</tr>
		<tr>
			<td height="19"   align="center" bgcolor="#FFFFFF"
				class="bk"><span class="btdx">配置名</span></td>
			<td width="15%" align="center" class="bk"><span class="btdx">配置值</span></td>
			<td width="15%" align="center" class="bk"><span class="btdx">状态</span></td>
			<td width="15%" align="center" class="bk"><span class="btdx">注释</span></td>
			<td width="15%" align="center" class="bk"><span class="btdx">类型</span></td>
			<td width="15%" align="center" class="bk"><span class="btdx">操作</span></td>
		</tr>
<?php
error_reporting ( 0 );

include "../myphplib/db.php";
include "../myphplib/message.php";
include "config/check.php";
include "../page.class.php";
?>
<?php
$so = trim ( htmlspecialchars ( $_POST ['name'] ) );

if ($so != "") {
	$where = " name like '%$so%' ";
} else {
	$where = "1=1";
}

$num = getCount ( "zt_setting", $where );
$per = 12;
$page_obj = new Page ( $num, $per );
$pagelist = $page_obj->fpage ();

$sql = "select * from  zt_setting where " . $where . " order by id desc  limit " . ($page_obj->page - 1) * $per . "," . $per;

$data = $row = array ();
$result = mysql_query ( $sql, $db );

while ( $row1 = mysql_fetch_array ( $result ) ) {
	?>
  
<tr >
			<td width="2%" height="79" align="center" bgcolor="#FFFFFF"
				class="bk"> <?php echo $row1['name']?> </td>
				
			<td align="center" bgcolor="#FFFFFF" style="color: red;" class="bk">  <?php echo $row1['value']?>  </td>
			
			<td align="center" bgcolor="#FFFFFF" class="bk"><?php
				if ($row1 ['state'] == '0') {
					echo "正常";
				}
				if ($row1 ['state'] == '1') {
					echo "<font color=red>废弃</font>";
				}
				?>    
		   </td>
			<td align="center" bgcolor="#FFFFFF" style="color: red;" class="bk">  <?php echo $row1['comment']?>  </td>
			<td align="center" bgcolor="#FFFFFF" class="bk"><?php
				if ($row1 ['type'] == '0') {
					echo "单值";
				}
				if ($row1 ['type'] == '1') {
					echo "<font color=red>组合值</font>";
				}
				?>    
		   </td>
			<td align="center" bgcolor="#FFFFFF" class="bk"><a
				href="zt_setting_add.html?id=<?=$row1['id']?>">修改</a></td>
		</tr>

	<?php
}

@mysql_close ( $db );

?>
 
</table>

	<center>
		<table width="100%" border="0" align="center" cellpadding="0"
			cellspacing="0" style="text-align: center;">
			<tr>
				<td height="21" colspan="6"><div class="epages"
						style="margin-top: 0">
				<?php  echo $pagelist;?>
				</div></td>
			</tr>
		</table>
	</center>

</body>
</html>
