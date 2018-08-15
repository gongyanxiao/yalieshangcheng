<?php
include "../page.class.php" ;

?>
<!DOCTYPE html>
<html>
<head lang="en">
<meta charset="UTF-8">
<title><?=_TITLE_?></title>
<meta name="keyword" content="投资列表" />
<meta name="description" content="投资列表" />
<link href="/Public/ico.ico" type="image/x-icon" rel="shortcut icon" />
<link rel="stylesheet" type="text/css" href="/jygj/Public/home/css/page.css">
<link rel="stylesheet" type="text/css" href="/jygj/Public/home/css/index.css" />
<script type="text/javascript" src="/jygj/Public/home/js/jquery-1.7.2.js"></script>
<script type="text/javascript" src="/jygj/Public/js/jquery.flexslider-min.js"></script>
<script type="text/javascript" src="/jygj/Public/js/clipboard.min.js"></script>


</head>


<body style="background: #F7F7F7">
		<!------最顶部开始------>
		<!------logo结束------>
		<script>
			$(function() {
				 
			});
		</script>
	 
		<?php
include "../myphplib/db.php";
include "config/check.php";
date_default_timezone_set("Asia/Shanghai");
$user=$_COOKIE['ECS']['username'];
 
 ?>
 
				<!----right内容开始---->
				<div class=" ">

					<div class="warp_f">

						<div class="warp_f_div">
							<b>店铺审核</b>
						</div>

						<table border="1" cellspacing="0" cellpadding="0"
							class="warp_f_th">
							<tr>
							    <th align="center">充值金额</th>
							    <th align="center">用户账户</th>
							    <th align="center">充值卡号</th>
								<th align="center">提交日期</th>
								<th align="center">充值日期</th>
								<th align="center">技术审核状态</th>
								<th align="center">技术审核日期</th>
								<th align="center">财务审核状态</th>
								<th align="center">财务审核日期</th>
								<th align="center">操作</th>
							</tr>
							<?php
						$num  = getOne("select count(0) from zt_xxtz where type=2");
				     	$per = 12;
						$page_obj=new Page($num,$per);
 						$q="select id,user,czyhkh ,skyhkh,user,czje,  date_format(FROM_UNIXTIME(tjrq),'%Y-%m-%d %H:%i') as tjrq ,
date_format(FROM_UNIXTIME(czrq),'%Y-%m-%d %H:%i') as czrq , cw_sh_state, js_sh_state,cj_state,date_format(FROM_UNIXTIME(step_start_rq),'%Y-%m-%d %H:%i') as step_start_rq,
 date_format(FROM_UNIXTIME(cwshrq),'%Y-%m-%d %H:%i') as cwshrq,date_format(FROM_UNIXTIME(jsshrq),'%Y-%m-%d %H:%i') as jsshrq,step, ktxje,ytxje,yfje  from zt_xxtz  where type=2 order by cw_sh_state asc, id desc limit ".($page_obj->page-1)*$per.",".$per;
 						$result=mysql_query($q,$db);
						$pagelist=$page_obj->fpage();
					for($i=0;$i<$num;$i++){
					$data2=mysql_fetch_array($result);
				?>
							<tr>
								<td align="center" style="border-bottom: 1px solid #CCC;">
									<?=$data2["czje"];?>
								</td>
								<td align="center" style="border-bottom: 1px solid #CCC;">
									<?=$data2["user"];?>
								</td>
								<td align="center" style="border-bottom: 1px solid #CCC;">
									<?=$data2["czyhkh"];?>
								</td>
								<td align="center"
									style="font-family: Arial, Helvetica, sans-serif; color: #F00; border-bottom: 1px solid #CCC;">
									<?=$data2["tjrq"];?>
								</td>
								<td align="center"
									style="font-family: Arial, Helvetica, sans-serif; color: #F00; border-bottom: 1px solid #CCC;">
									<?=$data2["czrq"];?>
								</td>
								<td align="center"
									style="font-family: Arial, Helvetica, sans-serif; color: #F00; border-bottom: 1px solid #CCC;">
									<?php  if($data2["js_sh_state"]==0) 
  												 echo "未审核";
										  else if($data2["js_sh_state"]==1) 
                                           echo   "审核通过";
										  else echo "审核不通过";
                                    ?>
								</td>
							     <td align="center"
									style="font-family: Arial, Helvetica, sans-serif; color: #F00; border-bottom: 1px solid #CCC;">
									<?=$data2["jsshrq"];?>
								</td>
								<td align="center"
									style="font-family: Arial, Helvetica, sans-serif; color: #F00; border-bottom: 1px solid #CCC;">
									<?php  if($data2["cw_sh_state"]==0) 
  												 echo "未审核";
										  else if($data2["cw_sh_state"]==1) 
                                           echo   "审核通过";
										  else echo "审核不通过";
                                    ?>
								</td>
							     <td align="center"
									style="font-family: Arial, Helvetica, sans-serif; color: #F00; border-bottom: 1px solid #CCC;">
									<?=$data2["cwshrq"];?>
								</td>
							   <td align="center" style="border-bottom: 1px solid #CCC;">
							   
							     <?php 
							     if($data2["js_sh_state"]==0 || $data2["cw_sh_state"]==0){
							     ?>
									<a href="xxtz_shen_he.html?id=<?=$data2["id"];?>"  >审核</a>
									<?php 
							         }
							     ?>
							   </td>
							</tr>
							<?php  } ?>


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
					</div>

				</div>
				<!----right内容结束---->
	 
 
</body>
</html>