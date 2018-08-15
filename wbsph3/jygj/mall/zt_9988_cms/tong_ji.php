<?php
include "../myphplib/db.php";
include "../myphplib/message.php";
include "config/check.php";
include "../myphplib/page.php";

function getShenFen($level)
{
    if ($level == 1) {
        return "会员";
    } else if ($level == 2) {
        return "合伙人";
    } else if ($level == 3) {
        return "准市级代理";
    } else if ($level == 4) {
        return "市级代理";
    }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>合伙人管理</title>
<link rel="stylesheet" type="text/css" href="css/table.css">
	<script type=text/javascript src="../js/jquery.min.js"></script>
	<script src="../lib/sea.js"></script>
	<script>
seajs.config({
  alias: {
    "jquery": "../lib/jquery-1.10.2.js"
  }
});

$(document).ready(function(){
	var url="/xbmall_admin/area_manage.php?act=get_child_area&is_ajax=1";

	$.ajax( {
	     type : "POST",
	     url : url,
	     dataType:"json",
	     cache:false,
	     async:false,
	     data : "",
	     success : function(data) {
	    	 alert(data.message);
	 		var arr =data.content;
	 		$.each(data.content,function(name,value) {
	 			
	 			   $("#province").append("<option value='"+value+"'>"+name+"</option>");
	 			});
	 	
	     }
	    });
	 
});
</script>


	<SCRIPT language=javascript> 
function  changeFlag(user,flag){
	 $.ajax({  
		type: "POST",  
		url: "/mobile/user.php?act=ajax_setFlag&user="+user+"&flag="+flag,  
		data:"",  
		cache:false,  
		success:function(html){  
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
if ($is_ti_xi != 1) {
    $is_ti_xi = 0;
}

$so = trim(htmlspecialchars($_GET['xm']));

$page = isset($_GET['page']) ? $_GET['page'] : 1;

$where = " 1 ";

if ($so != "") {
    $where = "(user_name like '%$so%' or real_name like '%$so%')";
}

if ($is_ti_xi == 1) { // 查询体系下的
    $resultArr = array();
    $query1 = "select *  from xbmall_users where  $where ";
    $result = mysql_query($query1, $db);
    while ($row = mysql_fetch_array($result)) {
        $resultArr[] = $row;
        getTiXi($row['user_id'], $resultArr);
    }
} else { // 查询单个人
    $q = "select *  from xbmall_users   where  $where    ";
    $query = mysql_query($q);
    $counts = mysql_num_rows($query);
    $getpageinfo = page($page, $counts, 'tong_ji.php?xm=' . $so);
    if ($page > 1) {
        $page = 12 * $page - 12 + 1;
    }
    $page = $page - 1;
    $sql .= $getpageinfo['sqllimit'];
    $data = $row = array();
    $query1 = "select *  from xbmall_users where  $where    order by user_id desc limit $page,12";
    $result = mysql_query($query1);
    while ($row = mysql_fetch_array($result)) {
        $resultArr[] = $row;
    }
}

/**
 * 获取体系下所有人
 * 
 * @param unknown $rootId
 */
function getTiXi($rootId, &$resultArr)
{
    $sql = "select * from  xbmall_users where parent_id=" . $rootId;
    $result = mysql_query($sql);
    if (! isset($result)) {
        return;
    }
    while ($row = mysql_fetch_array($result)) {
        if ($row) {
            $resultArr[] = $row;
            getTiXi($row['user_id'], $resultArr);
        }
    }
}

/**
 * 对结果进行处理
 */
foreach ($resultArr as $key => $row1) {
    $sql = "select sum(czje) as touZi,sum(xxtzztxjf) as xxtzztxjf from `zt_xxtz` where user='" . $row1['user_name'] . "' and step=1  and is_ce_shi!=1";
    $touZiZongE = getRow($sql);
    $row1['touZiZongE'] = $touZiZongE['touZi'];
    if ($row1['touZiZongE'] == '') {
        $row1['touZiZongE'] = 0;
    }
    
    $sql = "select count(0) from `zt_xxtz` where user='" . $row1['user_name'] . "'";
    $row1['zongDanShu'] = getOne($sql); // 总单数
    
    $sql = "select  sum(czje) as czje from `zt_xxtz_cz` where user='" . $row1['user_name'] . "' and cw_sh_state=1 and js_sh_state=1  and is_ce_shi!=1";
    $czje = getOne($sql);
    $row1['czje'] = $czje;
    if ($row1['czje'] == '') {
        $row1['czje'] = 0;
    }
    
    $sql = "select sum(je) from `zt_xxtz_ti_xian` where user='" . $row1['user_name'] . "' and (type=0 or type=2)  and jssh!=2 and cwsh!=2 and  is_ce_shi!=1";
    $tiXianZongE = getOne($sql);
    $tiXianZongE = round($tiXianZongE, 2);
    $row1['tiXianZongE'] = $tiXianZongE;
    
    $sql = "select sum(je) from `zt_xxtz_ti_xian` where user='" . $row1['user_name'] . "' and type=1 and   jssh!=2 and cwsh!=2 and   is_ce_shi!=1";
    $tiXianZongE = getOne($sql);
    $tiXianZongE = round($tiXianZongE, 2); // 动态提现金额
    $row1['tiXianZongE_dong_tai'] = $tiXianZongE;
    
    // $sql="SELECT SUM(xxtzztxjf) as xxtzztxjf , SUM(tui_jian_shou_yi) as tui_jian_shou_yi FROM `zt_xxtzzs` WHERE user='". $row1['user_name'] ."' and (xxtzztxjf >0 or tui_jian_shou_yi >0) ";
    // $zengSongZongE = getRow($sql);
    
    // $yingLi =$touZiZongE['xxtzztxjf'] - $touZiZongE['touZi'];//总分得的钱减去总的投资=总的盈利
    // $yingLi = $yingLi + $row1['tiXianZongE_dong_tai']; //加上动态收益
    
    $yingLi = $row1['tiXianZongE'] + $row1['tiXianZongE_dong_tai'] - $czje; // 静态提现总额+动态提现总额-充值总额
    $row1['wei_hui_ben'] = $yingLi; // $row1['tiXianZongE']+$row1['tiXianZongE_dong_tai']-$touZiZongE['touZi'];// 推荐收益提现+分红收益提现-投资总额
    
    $sql = "SELECT real_name from  `xbmall_users` WHERE  user_name='" . $row1['recommend_user'] . "' ";
    $row1['recommend_user_real_name'] = getOne($sql);
    
    $nowDateStr = date("Y-m-d", time());
    
    $nowDateStr = $nowDateStr . " 00:00:00";
    
    $sql = "select sum(czje) as touZi  from `zt_xxtz` where user='" . $row1['user_name'] . "' and tjrq >" . strtotime($nowDateStr) . "    and is_ce_shi!=1";
    $jinRiTouZi = getOne($sql);
    $row1['jinRiTouZi'] = $jinRiTouZi;
    
    $resultArr[$key] = $row1;
}

?>
<div style="z-index: 100:width:100%; height: 22px;">

		<table width="100%" border="0" height="22px;" cellpadding="0"
			cellspacing="0">
			<tr>
				<td width="2%" height="22"><img src="images/position1.jpg"></td>
				<td width="98%" align="left" background="images/position2.jpg">您当前的位置>>合伙人管理</td>
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
					&nbsp;&nbsp; &nbsp;用户名|姓名 <input type="text" name="xm" id="xm"
						value="<?php echo $_GET['xm'];?>"
						style="border: 1px solid #ccc; height: 20px;" /> &nbsp;&nbsp;
					&nbsp;查询体系<input type="checkbox" name="is_ti_xi" id="is_ti_xi"
						value="1" <?php if($is_ti_xi==1) echo  "checked='checked'";?>
						style="border: 1px solid #ccc; height: 20px;" /> 省<select
						id="province"></select> 市<select id="city"></select> <input
						type="submit" name="button" id="button" value="搜索"
						style="background-color: #F63; color: #FFF; border: 0px; line-height: 14px; height: 22px; cursor: pointer;" />
				</form>  <?php echo   $counts;?>个超级会员</td>
		</tr>
		<tr>
			<td width="6%" height="29" align="center" bgcolor="#FFFFFF"
				class="bk"><span class="btdx"> 用户名</span></td>
			<td width="4%" align="center" class="bk"><span class="btdx">姓名</span></td>
			<td width="4%" align="center" class="bk"><span class="btdx">充值总额</span></td>
			<td width="4%" align="center" class="bk"><span class="btdx">A投资总额</span></td>
			<td width="4%" align="center" class="bk"><span class="btdx">总单数</span></td>
			<td width="4%" align="center" class="bk"><span class="btdx">今日投资</span></td>

			<td width="4%" align="center" class="bk"><span class="btdx">静态提现总额</span></td>
			<td width="4%" align="center" class="bk"><span class="btdx">动态提现总额</span></td>
			<td width="4%" align="center" class="bk"><span class="btdx">回本(负数为未回本)</span></td>

			<td width="5%" align="center" class="bk"><span class="btdx">推荐人</span></td>
			<td width="3%" align="center" class="bk"><span class="btdx">级别</span></td>
			<td width="4%" align="center" class="bk"><span class="btdx">店铺</span></td>
			<td width="4%" align="center" class="bk"><span class="btdx">实际一层店铺</span></td>
			<td width="4%" align="center" class="bk"><span class="btdx">实际两层店铺总数</span></td>
			<td width="4%" align="center" class="bk"><span class="btdx">实际业绩</span></td>
			<td width="3%" align="center" class="bk"><span class="btdx">消费积分</span></td>
			<td width="4%" align="center" class="bk"><span class="btdx">店铺资金</span></td>
			<td width="4%" align="center" class="bk"><span class="btdx">投资收益A</span></td>
			<td width="4%" align="center" class="bk"><span class="btdx">投资收益B</span></td>
			<td width="4%" align="center" class="bk"><span class="btdx">推荐收益</span></td>

			<td width="5%" align="center" class="bk"><span class="btdx">注册日期</span></td>
			<td width="8%" align="center" class="bk"><span class="btdx">推荐收益</span></td>
		</tr>
<?php

foreach ($resultArr as $key => $row1) {
    // $res[$key] = $val;
    
    // while ($row1 = mysql_fetch_array($result)) {
    
    ?>
  
<tr bgcolor=#cccccc>
			<td align="center" bgcolor="#FFFFFF" class="bk"><span
				style="color: blue"><?php echo $row1['user_name']?></span></td>

			<td align="center" bgcolor="#FFFFFF" class="bk"><span
				style="color: red;"><?php echo $row1['real_name']?></span></td>

			<td align="center" bgcolor="#FFFFFF" class="bk"
				style="padding-right: 10px">
				
				  <?php
    echo $row1['czje'];
    ?>
			</td>
			<td align="center" bgcolor="#FFFFFF" class="bk"
				style="padding-right: 10px">
			 <?php
    echo $row1['touZiZongE'];
    ?>
			</td>

			<td align="center" bgcolor="#FFFFFF" class="bk"
				style="padding-right: 10px">
				
				  <?php
    echo $row1['zongDanShu'];
    ?>
			</td>


			<td align="center" bgcolor="#FFFFFF" class="bk"
				style="padding-right: 10px">
			 <?php
    echo $row1['jinRiTouZi'];
    ?>
			</td>


			<td align="center" bgcolor="#FFFFFF" class="bk"
				style="padding-right: 10px">
				
				  <?php
    echo $row1['tiXianZongE'];
    ?>
			</td>
			<td align="center" bgcolor="#FFFFFF" class="bk"
				style="padding-right: 10px">
				
				  <?php
    echo $row1['tiXianZongE_dong_tai'];
    ?>
			</td>
			<td align="center" bgcolor="#FFFFFF" class="bk"><span
				style="color: red;"><?php
    echo $row1['wei_hui_ben'];
    ?></span></td>



			<td align="center" bgcolor="#FFFFFF" class="bk"
				style="padding-right: 10px"><b><?php echo $row1['recommend_user_real_name']?></b></td>



			<td align="center" bgcolor="#FFFFFF" style="color: red;">
    <?php
    echo getShenFen($row1['level']);
    
    ?> </td>
			<td align="center" bgcolor="#FFFFFF" class="bk"><?php
    if ($row1['is_dian_pu'] == '1')
        echo "已开店";
    else
        echo "未开店";
    ?>    </td>
			<td align="center" bgcolor="#FFFFFF" class="bk"><?php echo $row1['dian_pu_shu1']-$row1['vitual_dian_pu_shu1']?></td>
			<td align="center" bgcolor="#FFFFFF" class="bk"><?php echo $row1['dian_pu_shu2']-$row1['vitual_dian_pu_shu2']?></td>
			<td align="center" bgcolor="#FFFFFF" class="bk"><?php echo $row1['ye_ji']?></td>

			<td align="center" bgcolor="#FFFFFF" class="bk"><span
				style="color: red;"><?php echo $row1['pay_points']?$row1['pay_points']:0;?></span></td>
			<td align="center" bgcolor="#FFFFFF" class="bk"><span
				style="color: red;"><?php echo $row1['dian_pu_zi_jin']?$row1['dian_pu_zi_jin']:0;?></span></td>
			<td align="center" bgcolor="#FFFFFF" class="bk"><span
				style="color: red;"><?php echo $row1['xxtzztxjf']?$row1['xxtzztxjf']:0;?></span></td>
			<td align="center" bgcolor="#FFFFFF" class="bk"><span
				style="color: red;"><?php echo $row1['xxtzztxjf_b']?$row1['xxtzztxjf_b']:0;?></span></td>

			<td align="center" bgcolor="#FFFFFF" class="bk"><span
				style="color: red;"><?php echo $row1['tui_jian_shou_yi']?$row1['tui_jian_shou_yi']:0;?></span></td>
			<td align="center" bgcolor="#FFFFFF" class="bk"><?php echo $row1['zcrq']?></td>
			<td align="center" bgcolor="#FFFFFF" class="bk"><?php if( $row1['flag']==1) {?>已启用<button
					onclick="changeFlag(<?php echo $row1['user_name'];?>,0)">停用</button> <?php
    
} else {
        ?>已停用 <button
					onclick="changeFlag(<?php echo $row1['user_name'];?>,1)">启用</button><?php }?></td>

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
