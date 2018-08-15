<?php
include "../page.class.php" ;
include "../myphplib/db.php";
// include "config/check.php";
date_default_timezone_set("Asia/Shanghai");


$export=$_REQUEST['export'];

if($export==1){
    export();
}


function export(){
    
    
    require_once "../myphplib/PHPExcel.php";
    $ksrq = $_REQUEST['ksrq'];
    $jsrq = $_REQUEST['jsrq'];
    
    $condition .= " where cw_sh_state=1 ";
    
    if(isset($ksrq)){
        $ksrq = strtotime($ksrq);
        $condition .= " and a.cwshrq >= {$ksrq}";
    }
    if(isset($jsrq)){
        $jsrq = strtotime($jsrq);
        $condition .= " and a.cwshrq <= {$jsrq}";
    }
    
    
    // 实例化excel类
    $objPHPExcel = new PHPExcel();
    // 实例化excel图片处理类
    $objDrawing = new PHPExcel_Worksheet_Drawing();
    // 操作第一个工作表
    $objPHPExcel->setActiveSheetIndex(0);
    // 设置sheet名
    $objPHPExcel->getActiveSheet()->setTitle('充值记录列表');
    
    // 列名表头加粗
    $objPHPExcel->getActiveSheet()->getStyle('A1:D1')->getFont()->setBold(true);
    // 列名赋值
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '日期');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '姓名');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '金额');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '用户名');
    
    //技术审核通过, 财务未审核
    $sql = "select b.real_name,b.bank_num, a.czje, date_format(FROM_UNIXTIME(a.tjrq),'%Y-%m-%d %H:%i') as date, a.user  from  xbmall_xb_cz a join  xbmall_users b   on a.user=b.user_name     {$condition} order by  a.tjrq ";
    
    $result=mysql_query($sql);
    $row_num = 2;
    while ( $value = mysql_fetch_array ( $result ) ) {
        // 设置单元格高度
        $objPHPExcel->getActiveSheet()->getRowDimension($row_num)->setRowHeight(32);
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(0) ->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(1) ->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(2) ->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(3) ->setWidth(20);
        // 设置排序列、是否显示列居中显示
        // 设置单元格数值
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_num, $value['date']);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $row_num, $value['real_name']  );
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $row_num, $value['czje']);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $row_num, $value['user']);
        $row_num++;
        
    }
    
    $outputFileName = '充值记录_' . time() . '.xls';
    $xlsWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");
    header('Content-Disposition:inline;filename="' . iconv("UTF-8", "gb2312", $outputFileName) . '"');
    header("Content-Transfer-Encoding: binary");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Pragma: no-cache");
    $xlsWriter->save("php://output");
    echo file_get_contents($outputFileName);
    die;
    
}
include "getRoleId.php";

?>
<!DOCTYPE html>
<html>
<head lang="en">
<meta charset="UTF-8">
<title>充值审核</title>
<meta name="keyword" content="充值列表" />
<meta name="description" content="充值列表" />
<link href="/Public/ico.ico" type="image/x-icon" rel="shortcut icon" />
<link rel="stylesheet" type="text/css" href="/jygj/Public/home/css/page.css">
<link rel="stylesheet" type="text/css" href="/jygj/Public/home/css/index.css" />
<script type="text/javascript" src="/jygj/Public/home/js/jquery-1.7.2.js"></script>
<script type="text/javascript" src="/jygj/Public/js/clipboard.min.js"></script>


</head>


<body style="background: #F7F7F7">
		<!------最顶部开始------>
		<!------logo结束------>
		<script>
		 
			function f1(url){
				window.location.href=url;
			}


			function  exportExcel(){
				 var  url="/jygj/mall/zt_9988_cms/xxtz_cz_list.php?export=1";
					if($("#ksrq").val()!=""){
						url =url+"&ksrq="+$("#ksrq").val();
					}
					if($("#jsrq").val()!=""){
						url =url+"&jsrq="+$("#jsrq").val();
					}
					window.location.href= url;
			}

			
		</script>
	 
		<?php




$keyWord = $_REQUEST['keyWord'];
$ksrq = $_REQUEST['ksrq'];
$jsrq = $_REQUEST['jsrq'];
if(isset($keyWord)){
	$keyWord = trim($keyWord);
}
 $sql="select  sum(a.czje) from xbmall_xb_cz a    where     cw_sh_state=1 and js_sh_state=1 and is_ce_shi!=1 ";
  
 $totalCZ = getOne($sql);
 ?>
 
				<!----right内容开始---->
				<div class=" ">

					<div class="warp_f">

						<div class="warp_f_div">
							<b>充值审核(审核通过的充值总额:<?php echo $totalCZ;?>)</b>
							<form action="xxtz_cz_list.php">
							     <input name="keyWord" value="<?php echo $keyWord;?>">  <input type="submit" value="搜索"> 
							    开始时间:<input type="text" name="ksrq" id="ksrq" value="<?php echo  date("Y-m-d", time());?> 00:00:00"> 
							    结束时间:<input type="text" name="jsrq" id="jsrq" value="<?php echo  date("Y-m-d H:i:s", time());?>">  
							      <input type="button" value="导出" onclick="exportExcel()">
							</form>
							
						</div>

						<table border="1" cellspacing="0" cellpadding="0"
							class="warp_f_th">
							<tr>
							    <th align="center">充值金额</th>
							    <th align="center">姓名</th>
							    <th align="center">用户账户</th>
							    <th align="center">tap账号</th>
								<th align="center">提交日期</th>
								<th align="center">充值日期</th>
								<th align="center">用户备注</th>
								<th align="center">凭证</th>
								<th align="center">技术审核状态</th>
								<th align="center">技术审核日期</th>
								<th align="center">技术备注</th>
								<th align="center">财务审核状态</th>
								<th align="center">财务审核日期</th>
								<th align="center">财务备注</th>
								<th align="center">操作</th>
							</tr>
							<?php
							
							
							if(isset($keyWord)){
							    $condition = " and (b.xm like '%{$keyWord}%'  or a.user like '%{$keyWord}%' ) ";
							}
							
// 							if(isset($ksrq)){
// 							    $ksrq = strtotime($ksrq);
// 							    $condition .= " and a.cwshrq >= {$ksrq}";
// 							}
// 							if(isset($jsrq)){
// 							    $jsrq = strtotime($jsrq);
// 							    $condition .= " and a.cwshrq <= {$jsrq}";
// 							}
							
							
					   	$per = 12;
					   	$num  = getOne("select  count(*) from xbmall_xb_cz a join  xbmall_users  b  on  a.user=b.user_name where  a.is_ce_shi!=1 $condition    ");
					   	
					   	
						$page_obj=new Page($num,$per);
 						$q="select b.user_type, b.real_name as xm,a.id,a.user,a.czyhkh ,a.skyhkh,a.user,a.czje,  date_format(FROM_UNIXTIME(a.tjrq),'%Y-%m-%d %H:%i') as tjrq ,
date_format(FROM_UNIXTIME(a.czrq),'%Y-%m-%d %H:%i') as czrq , a.cw_sh_state, a.js_sh_state, 
 date_format(FROM_UNIXTIME(a.cwshrq),'%Y-%m-%d %H:%i') as cwshrq,date_format(FROM_UNIXTIME(a.jsshrq),'%Y-%m-%d %H:%i') as jsshrq, a.user_bz,a.cw_bz,a.js_bz ,a.ping_zheng 
 from xbmall_xb_cz a join  xbmall_users  b  on  a.user=b.user_name where  a.is_ce_shi!=1 $condition   order by a.cw_sh_state asc, a.id desc limit ".($page_obj->page-1)*$per.",".$per;
 						
 						$result=mysql_query($q,$db);
						$pagelist=$page_obj->fpage();
						while($res=mysql_fetch_assoc($result)) {
						    $data2=$res;
					
				?>
							<tr>
								<td align="center" style="border-bottom: 1px solid #CCC;">
									<?=$data2["czje"];?>
								</td>
								<td align="center" style="border-bottom: 1px solid #CCC;">
									<?php  
									 echo $data2["xm"]; 
									 ?>
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
									<?=$data2["user_bz"];?>
								</td>
							    <td align="center"
									style="font-family: Arial, Helvetica, sans-serif; color: #F00; border-bottom: 1px solid #CCC;">
									<a href="/data/xxtzuploads/<?=$data2['ping_zheng'];?>" target="blank">查看</a>
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
									<?=$data2["js_bz"];?>
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
								  <td align="center"
									style="font-family: Arial, Helvetica, sans-serif; color: #F00; border-bottom: 1px solid #CCC;">
									<?=$data2["cw_bz"];?>
								</td>
							   <td align="center" style="border-bottom: 1px solid #CCC;">
							   
							     <?php 
							     
							     if(  ($data2["cw_sh_state"]==0 || $data2["js_sh_state"]==0) && ($role_id==3 ||$role_id==6)){
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