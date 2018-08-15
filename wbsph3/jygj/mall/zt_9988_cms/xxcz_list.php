<?php
include "../page.class.php" ;

?>
<!DOCTYPE html>
<html>
<head lang="en">
<meta charset="UTF-8">
<title><?=_TITLE_?></title>
<meta name="keyword" content="线下充值列表" />
<meta name="description" content="线下充值列表" />
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
							<b>线下充值记录</b>
						</div>

						<table border="1" cellspacing="0" cellpadding="0"
							class="warp_f_th">
							<tr>
								<th align="center">提交日期</th>
								<th align="center">充值日期</th>
								<th align="center">审核日期</th>
								<th align="center">充值金额</th>
								<th align="center">消费者分红权</th>
								<th align="center">全返分红权</th>
								<th align="center">审核状态</th>
								<th align="center">当前阶段</th>
								<th align="center">消费者已分积分</th>
								<th align="center">全返已分积分</th>
								<th align="center">操作</th>
							</tr>
							<?php
						$num  = getCount("zt_xxcz");
				     	$per = 12;
						$page_obj=new Page($num,$per);
 						$q="select id,date_format(FROM_UNIXTIME(tjrq),'%Y-%m-%d %H:%i') as tjrq ,date_format(FROM_UNIXTIME(czrq),'%Y-%m-%d %H:%i') as czrq ,date_format(FROM_UNIXTIME(shrq),'%Y-%m-%d %H:%i') as shrq ,czyhkh,  skyhkh,czje,quan_fan_fhq, fhq,sh_state,step,zjf,quan_fan_zjf  from zt_xxcz    order by sh_state asc, id desc limit ".($page_obj->page-1)*$per.",".$per;
 						$result=mysql_query($q,$db);
						$pagelist=$page_obj->fpage();
					for($i=0;$i<$num;$i++){
					$data2=mysql_fetch_array($result);
				?>
							<tr>
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
									<?=$data2["shrq"];?>
								</td>
								<td align="center" style="border-bottom: 1px solid #CCC;">
									<?=$data2["czje"];?>
								</td>
								<td align="center" style="border-bottom: 1px solid #CCC;">
									<?=$data2["fhq"];?>
								</td>
								<td align="center" style="border-bottom: 1px solid #CCC;">
									<?=$data2["quan_fan_fhq"];?>
								</td>
								<td align="center" style="border-bottom: 1px solid #CCC;">
									<?php  if($data2["sh_state"]==0) 
  												 echo "未审核";
										  else if($data2["sh_state"]==1) 
                                           echo   "审核通过";
										  else echo "审核不通过";
                                    ?>
								</td>
								<td align="center" style="border-bottom: 1px solid #CCC;">
									<?=$data2["step"];?>
								</td>
								<td align="center" style="border-bottom: 1px solid #CCC;">
									<?=$data2["zjf"];?>
								</td>
								<td align="center" style="border-bottom: 1px solid #CCC;">
									<?=$data2["quan_fan_zjf"];?>
								</td>
							   <td align="center" style="border-bottom: 1px solid #CCC;">
							   
							     <?php 
							     if($data2["sh_state"]==0){
							     ?>
									<a href="xxcz_shen_he.html?id=<?=$data2["id"];?>"  >审核</a>
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