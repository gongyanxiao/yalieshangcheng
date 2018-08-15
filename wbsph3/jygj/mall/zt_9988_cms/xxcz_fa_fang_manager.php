<?php
include "../page.class.php" ;

?>
<!DOCTYPE html>
<html>
<head lang="en">
<meta charset="UTF-8">
<title><?=_TITLE_?></title>
<meta name="keyword" content="发放聚珠" />
<meta name="description" content="发放聚珠" />
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
		$fa_fang = false;
		function  faFangJiFen(){
			if($fa_fang){
		      alert("已经发放过了");
		      return ;
			}
			
			$fa_fang = true;
			if(document.getElementById("user").value.trim()!=""){
				window.location.href = "/jygj/mall/zt_9988_cms/xxcz_fa_fang_ge_ren.php?user="+document.getElementById("user").value+"&ffrq="+document.getElementById("ffrq").value;
			}else{
				window.location.href = "/jygj/mall/zt_9988_cms/xxcz_fa_fang.php?ffrq="+document.getElementById("ffrq").value;
			}
	      
		}

		function f1(url){
		window.location.href=url;
		}
		</script>
	 
		<?php
include "../myphplib/db.php";
// include "config/check.php";
date_default_timezone_set("Asia/Shanghai");
$user=$_COOKIE['ECS']['username'];
 
 ?>
 
				<!----right内容开始---->
				<div class=" ">

					<div class="warp_f">

						<div class="warp_f_div">
							<b>发放聚珠</b>
						</div>

						<table border="1" cellspacing="0" cellpadding="0" style="width:50%"
							class="warp_f_th">
							<tr>
							<td colspan="3">
							<form method="post" enctype="multipart/form-data">
							指定用户:<input type="text" name="user" id="user">
							发放时间:<input type="text" name="ffrq" id="ffrq" value="<?php echo  date("Y-m-d H:i:s", time());?>">(发放指定时间之前的聚珠)
					        <input type="button" onclick="faFangJiFen()" value="发放 聚珠"
						  style="background-color: #F63; color: #FFF; border: 0px; line-height: 14px; height: 22px; cursor: hand;"  > &nbsp;&nbsp; &nbsp;
				</form>
							</td>
							</tr>
							<tr>
								<th align="center">发放日期</th>
								<th align="center">发放聚珠总数</th>
							</tr>
						 
							<tr>
								<td align="center"
									style="font-family: Arial, Helvetica, sans-serif; color: #F00; border-bottom: 1px solid #CCC;">
								</td>
								<td align="center"
									style="font-family: Arial, Helvetica, sans-serif; color: #F00; border-bottom: 1px solid #CCC;">
								</td>
						</table>
						 
						 <table width="50%" border="0" align="center" cellpadding="0"
								cellspacing="0" style="text-align: center;">
								<tr>
									<td height="21" colspan="6"><div class="epages"
											style="margin-top: 0">
											<?php  echo $pagelist;?>
										</div></td>
								</tr>
						</table>
						 
					</div>

				</div>
				<!----right内容结束---->
	 
 
</body>
</html>