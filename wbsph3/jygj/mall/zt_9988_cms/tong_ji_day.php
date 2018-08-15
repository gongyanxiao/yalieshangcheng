<?php
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>每日统计</title>

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

</head>

<body>  
  
  <?php
error_reporting(0);
// $sh1=strip_tags($_GET['sh1']);

include "config/check.php";
include "../myphplib/db.php";

include "../myphplib/page.php";

$so = trim(htmlspecialchars($_GET['xm']));

$page = isset($_GET['page']) ? $_GET['page'] : 1;

$sql = "select count(0)  from zt_tongji_day   ";
$counts = getOne($sql);
$getpageinfo = page($page, $counts, 'tong_ji_day.php');
if ($page > 1) {
    $page = 12 * $page - 12 + 1;
}
$page = $page - 1;
$sql .= $getpageinfo['sqllimit'];
$data = $row = array();
$query1 = "select *  from zt_tongji_day     order by user_id desc limit $page,12";
$result = mysql_query($query1);
while ($row = mysql_fetch_array($result)) {
    $resultArr[] = $row;
}

?>
<div style="z-index: 100:width:100%; height: 22px;">

		<table width="100%" border="0" height="22px;" cellpadding="0"
			cellspacing="0">
			<tr>
				<td width="2%" height="22"><img src="images/position1.jpg"></td>
				<td width="98%" align="left" background="images/position2.jpg">您当前的位置>>每日网站统计</td>
			</tr>
		</table>

	</div>
	<div id="jsgj"></div>


	<table width="100%" border=0 align="center" cellspacing=0
		bordercolorlight="#94D0EA" bordercolordark="#F5FBFE"
		style="margin-top: 15px;">
		<tr>
			<td height="35" colspan="15" align="left" bgcolor="#DBEBFA">
				<form method="get" action="tong_ji.php"
					enctype="multipart/form-data">
					&nbsp;&nbsp; &nbsp;开始日期<input type="text" name="xm" id="xm"
						value="<?php echo $_GET['xm'];?>"
						style="border: 1px solid #ccc; height: 20px;" /> &nbsp;&nbsp;
					&nbsp;结束日期<input type="text" name="xm" id="xm"
						value="<?php echo $_GET['xm'];?>"
						style="border: 1px solid #ccc; height: 20px;" /> <input
						type="submit" name="button" id="button" value="搜索"
						style="background-color: #F63; color: #FFF; border: 0px; line-height: 14px; height: 22px; cursor: pointer;" />
				</form>
			</td>
		</tr>
		<tr>
			<td width="6%" height="29" align="center" bgcolor="#FFFFFF"
				class="bk"><span class="btdx"> 日期</span></td>
			<td width="4%" align="center" class="bk"><span class="btdx">充值金额</span></td>
			<td width="4%" align="center" class="bk"><span class="btdx">提现总额</span></td>
			<td width="4%" align="center" class="bk"><span class="btdx">做单个数</span></td>
			<td width="4%" align="center" class="bk"><span class="btdx">出局个数</span></td>
			<td width="4%" align="center" class="bk"><span class="btdx">静态收益发放</span></td>
			<td width="4%" align="center" class="bk"><span class="btdx">动态收益发放</span></td>
			<td width="4%" align="center" class="bk"><span class="btdx">消费聚珠发放</span></td>
			<td width="4%" align="center" class="bk"><span class="btdx">储备聚珠发放</span></td>
			<td width="4%" align="center" class="bk"><span class="btdx">收益聚珠发放</span></td>
			<td width="4%" align="center" class="bk"><span class="btdx">手续费</span></td>
		</tr>
<?php
foreach ($resultArr as $key => $row1) {
    ?>
  
<tr bgcolor=#cccccc>
			<td align="center" bgcolor="#FFFFFF" class="bk"><span
				style="color: blue"><?php echo $row1['chong_zhi']?></span></td>

			<td align="center" bgcolor="#FFFFFF" class="bk"><span
				style="color: red;"><?php echo $row1['ti_xian']?></span></td>

			<td align="center" bgcolor="#FFFFFF" class="bk"
				style="padding-right: 10px">
			     <?php    echo $row1['zuo_dan_ge_shu'];    ?>
			</td>

			<td align="center" bgcolor="#FFFFFF" class="bk"
				style="padding-right: 10px">
				  <?php    echo $row1['chu_ju_ge_shu'];    ?>
			</td>
			<td align="center" bgcolor="#FFFFFF" class="bk"
				style="padding-right: 10px">
				  <?php    echo $row1['jing_tai_fa_fang'];    ?>
			</td>
			<td align="center" bgcolor="#FFFFFF" class="bk"><span
				style="color: red;"><?php    echo $row1['dong_tai_fa_fang'];    ?></span>
			</td>

			<td align="center" bgcolor="#FFFFFF" class="bk"
				style="padding-right: 10px"><b><?php echo $row1['xfjz_fa_fang']?></b></td>
			 
			<td align="center" bgcolor="#FFFFFF" class="bk"><span
				style="color: red;"><?php echo $row1['cbjz_fa_fang']?></span></td>
			<td align="center" bgcolor="#FFFFFF" class="bk"><span
				style="color: red;"><?php echo $row1['syjz_fa_fang']?></span></td>
			<td align="center" bgcolor="#FFFFFF" class="bk"><?php echo $row1['fwf_fa_fang']?></td>

		</tr>

	<?php
}
@mysql_close($db);
?>
</table>


	<table width="100%" height="24" border="0" cellpadding="0"
		cellspacing="0">
		<tr>
			<td width="38" align="left"></td>
			<td width="1373" align="left">
	<?php
    echo '<BR>' . $getpageinfo['pagecode'];
?></td>
		</tr>
	</table>




</body>

</html>
