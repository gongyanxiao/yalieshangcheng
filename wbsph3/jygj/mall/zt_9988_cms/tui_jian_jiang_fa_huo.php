<?php

include "../myphplib/db.php";
include "../myphplib/message.php";
include "config/check.php";
include "../myphplib/page.php";


 

function getShenFen($level)
{
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
<title>推荐奖发货</title>
<link rel="stylesheet" type="text/css" href="css/table.css">
 
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
function  faHuo(user,flag){
	 $.ajax({  
		type: "POST",  
		url: "tui_jian_jiang_fa_huo_do.php?user="+user,  
		data:"",  
		cache:false,  
		success:function(html){  
			$("body").append(html);
			alert(html);
		    self.location.reload();
		 }  
	 }); 
}
 



</SCRIPT>

</head>

<body>  
  
  <?php
  error_reporting(0);
// $sh1=strip_tags($_GET['sh1']);
$is_ti_xi = isset($_GET['is_ti_xi']) ? strip_tags($_GET['is_ti_xi']) : null;
if ($is_ti_xi !=1) {
    $is_ti_xi = 0;
}



$so = trim(htmlspecialchars($_GET['xm']));
 
$page = isset($_GET['page']) ? $_GET['page'] : 1;

$where = " 1 ";

if ($so != "") {
    $where = "(user_name like '%$so%' or real_name like '%$so%')";
  
} 

//查询单个人
$q = "select *  from xbmall_users   where  $where    ";
$query = mysql_query($q);
$counts = mysql_num_rows($query);
$getpageinfo = page($page, $counts, 'tui_jian_jiang_fa_huo.php?xm='.$so);
if ($page > 1) {
    $page = 12 * $page - 12 + 1;
}
$page = $page - 1;
$sql .= $getpageinfo['sqllimit'];
$data = $row = array();
$sql="select a.user_name, a.real_name,b.num,a.zcrq, b.tjrq, FROM_UNIXTIME(a.fa_huo_ri_qi) as  fa_huo_ri_qi, a.pay_points 
 from xbmall_users a  JOIN   (SELECT   a.user, MIN(a.tjrq) as tjrq, num    from (
SELECT  user, from_unixtime(tjrq, '%Y-%m-%d') as tjrq  , COUNT(0) as num  from  zt_xxtz GROUP BY  user, from_unixtime(tjrq, '%Y-%m-%d') 
ORDER BY tjrq asc ) a GROUP BY  user )  b  on a.user_name= b.`user`  where  $where  ORDER BY a.fa_huo_ri_qi asc, a.user_name     limit $page,12 
";
 
$result = mysql_query($sql);
while ($row = mysql_fetch_array($result)) {
    $resultArr[] = $row;
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
				<form method="get" action="tui_jian_jiang_fa_huo.php"
					enctype="multipart/form-data">
					&nbsp;&nbsp; &nbsp;用户名|姓名 <input type="text" name="xm" id="xm"
						value="<?php echo $_GET['xm'];?>"
						style="border: 1px solid #ccc; height: 20px;" />
						
						 <input
						type="submit" name="button" id="button" value="搜索"
						style="background-color: #F63; color: #FFF; border: 0px; line-height: 14px; height: 22px; cursor: pointer;" />
				</form>  <?php echo   $counts;?>个超级会员</td>
		</tr>
		<tr>
			<td width="6%" height="29" align="center" bgcolor="#FFFFFF"
				class="bk"><span class="btdx"> 用户名</span></td>
			<td width="4%" align="center" class="bk"><span class="btdx">姓名</span></td>
		    <td width="4%" align="center" class="bk"><span class="btdx">消费积分</span></td>
		    <td width="4%" align="center" class="bk"><span class="btdx">注册日期</span></td>
		    <td width="4%" align="center" class="bk"><span class="btdx">铺货日期</span></td>
			<td width="4%" align="center" class="bk"><span class="btdx">当天铺货数量</span></td>
			<td width="5%" align="center" class="bk"><span class="btdx">发货日期</span></td>
			<td width="8%" align="center" class="bk"><span class="btdx">操作
      </td>
		</tr>
<?php


foreach ($resultArr AS $key => $row1)
{
    
//     $res[$key] = $val;

// while ($row1 = mysql_fetch_array($result)) {
   
    ?>
  
<tr bgcolor=#cccccc>
			<td align="center" bgcolor="#FFFFFF" class="bk"><span
				style="color: blue"><?php echo $row1['user_name']?></span></td>

			<td align="center" bgcolor="#FFFFFF" class="bk"><span
				style="color: red;"><?php echo $row1['real_name']?></span></td>
		   <td align="center" bgcolor="#FFFFFF" class="bk"><span
				style="color: red;"><?php echo $row1['pay_points']?></span></td>
			   <td align="center" bgcolor="#FFFFFF" class="bk"><span
				style="color: red;"><?php echo $row1['zcrq']?></span></td>
				   <td align="center" bgcolor="#FFFFFF" class="bk"><span
				style="color: red;"><?php echo $row1['tjrq']?></span></td>
				
				
				   <td align="center" bgcolor="#FFFFFF" class="bk"><span
				style="color: red;"><?php echo $row1['num']?></span></td>
			
				
				
				
		   <td align="center" bgcolor="#FFFFFF" class="bk"><span
				style="color: red;"><?php echo $row1['fa_huo_ri_qi']?></span></td>
				
			<td align="center" bgcolor="#FFFFFF" class="bk"><?php if( $row1['fa_huo_ri_qi']==null) {?><button onclick="faHuo(<?php echo $row1['user_name'];?>,0)">发货</button> 
			<?php }?> &nbsp;
			 </td>

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
