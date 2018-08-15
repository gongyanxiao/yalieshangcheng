<?php
include "../page.class.php" ;
include "../myphplib/db.php";
include "../myphplib/message.php";
include "config/check.php";
function  export(){
    
    require_once "../myphplib/PHPExcel.php";
    
    // 实例化excel类
    $objPHPExcel = new PHPExcel();
    // 实例化excel图片处理类
    $objDrawing = new PHPExcel_Worksheet_Drawing();
    // 操作第一个工作表
    $objPHPExcel->setActiveSheetIndex(0);
    // 设置sheet名
    $objPHPExcel->getActiveSheet()->setTitle('投资列表');
    
    // 列名表头加粗
    $objPHPExcel->getActiveSheet()->getStyle('A1:R1')->getFont()->setBold(true);
    // 列名赋值
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '用户');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '编号');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '前编号');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '金额');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '提交日期');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '当前阶段');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', '本阶段开始日期');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', '可提现金额');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', '已提现金额');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', '截止时间');
    
   
    
    $user=$_REQUEST['user'];
    $where =" 1 ";
    if($user){
    	$user = trim($user);
        $where =$where." and user like '%$user%'";
    }
 
    $sql = "select ft_state, id,parent_id, user,czje,  date_format(FROM_UNIXTIME(tjrq),'%Y-%m-%d %H:%i') as tjrq ,
date_format(FROM_UNIXTIME(czrq),'%Y-%m-%d %H:%i') as czrq ,   cj_state,date_format(FROM_UNIXTIME(step_start_rq),'%Y-%m-%d %H:%i') as step_start_rq,
 step, ktxje,ytxje,yfje,date_format(FROM_UNIXTIME(next_ft_time),'%Y-%m-%d %H:%i') as next_ft_time,type,xxtzzfwf
,xxtzzcbjz,xxtzzsyjz,xxtzzxfjz,xxtzztxjf  from  zt_xxtz where  {$where}  order by id desc";
    
    $result=mysql_query($sql);
    $row_num = 2;
    while ( $value = mysql_fetch_array ( $result ) ) {
        // 设置单元格高度
        $objPHPExcel->getActiveSheet()->getRowDimension($row_num)->setRowHeight(32);
        // 设置排序列、是否显示列居中显示
        // 设置单元格数值
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_num, $value['user']);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $row_num, $value['id']  );
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $row_num, $value['parent_id']);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $row_num, $value['czje']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $row_num, $value['tjrq']);
        
        
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $row_num, $value['step']);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $row_num, $value['step_start_rq']);
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $row_num, $value['xxtzztxjf']);
        $objPHPExcel->getActiveSheet()->setCellValue('I' . $row_num, $value['ytxje']); //history_zsq,zsq,tj_name,reg_time
        
        
           if($value["cj_state"]==1) $value["ft_state"]= "已出局";
           else if($value["ft_state"]==1)
                 $value["ft_state"]= "已复投";
             else if($value["step"]==1)
                 $value["ft_state"]= $value["next_ft_time"];
                  
        $objPHPExcel->getActiveSheet()->setCellValue('J' . $row_num, $value['ft_state']);
        $row_num++;
        
    }
  
							  
							   
							 
    $outputFileName = '投资记录_' . time() . '.xls';
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

$export=$_REQUEST['export'];

if($export){
    export();
} 
?>
<!DOCTYPE html>
<html>
<head lang="en">
<meta charset="UTF-8">
<title>投资列表</title>
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

			function  f1(url){
				window.location.href=url;
			}
			function  beforeSubmit(type){
				document.getElementById("export").value=type;
				document.getElementById("searchForm").submit();
			}
		 
			
		</script>
	 
		<?php

date_default_timezone_set("Asia/Shanghai");
 
$totalCZ  = getOne("select sum(czje) from zt_xxtz where is_ce_shi!=1 ");

 ?>
 
				<!----right内容开始---->
				<div class=" ">

					<div class="warp_f">
						<div class="warp_f_div">
							<b>投资记录(投资总额:<?php echo $totalCZ;?>)</b>
							<form action="xxtz_list.php" id="searchForm">
							 <input type="text" name ="user" value="<?php echo $_REQUEST['user'];?>">
							 <input type="hidden" name="export" id="export" value="0">
							 <input type="button" value="搜索" onclick="beforeSubmit(0)">
							 <input type="button" value="导出" onclick="beforeSubmit(1)">
							</form>
						</div>

						<table border="1" cellspacing="0" cellpadding="0"
							class="warp_f_th">
							<tr>
								<th align="center">用户</th>
								<th align="center">姓名</th>
							    <th align="center">编号</th>
								<th align="center">前编号</th>
								<th align="center">金额</th>
								<th align="center">提交日期</th>
								<th align="center">当前阶段</th>
								<th align="center">本阶段开始日期</th>
								<th align="center">可提现金额</th>
								<th align="center">已提现金额</th>
								<th align="center">复投截止时间</th>
							</tr>
							<?php
							$user =   $_REQUEST['user'];
							//查询出配置信息
// 							$configSQL=" SELECT   a.`value` as first_period, b.`value` as second_period, c.`value` as three_period, d.`value` as four_period FROM `zt_setting`a
//    JOIN      `zt_setting` b
// WHERE  a.`name`='first_period' and b.`name`='second_period' ";
// 							$result = getRow($configSQL);
// 							$first_period =$result['first_period'];
// 							$second_period =$result['second_period'];
 							$where=" where a.is_ce_shi!=1 ";
							if(isset($user)){
								$user = trim($user);
							    $where .=" and a.user like'%{$user}%' ";
							}else{
							    $where .="  ";
							}
							
							//自己是消费者， 或者自己是线下消费的全返受益人
							$sql = "select b.real_name,a.ft_state, a.id,a.parent_id, a.user,a.czje,  date_format(FROM_UNIXTIME(a.tjrq),'%Y-%m-%d %H:%i') as tjrq ,
date_format(FROM_UNIXTIME(a.czrq),'%Y-%m-%d %H:%i') as czrq ,   cj_state,date_format(FROM_UNIXTIME(a.step_start_rq),'%Y-%m-%d %H:%i') as step_start_rq,
 a.step, a.ktxje,a.ytxje,a.yfje,date_format(FROM_UNIXTIME(a.next_ft_time),'%Y-%m-%d %H:%i') as next_ft_time,a.type,a.xxtzzfwf
,a.xxtzzcbjz,a.xxtzzsyjz,a.xxtzzxfjz,a.xxtzztxjf  from  zt_xxtz a  left join  xbmall_users  b  on a.user=b.user_name {$where}  ";
							$num  = getOne("select count(0) from zt_xxtz a  {$where} ");
							$per = 12;
							$page_obj=new Page($num,$per);
							$q="{$sql} order by a.id desc limit ".($page_obj->page-1)*$per.",".$per;
							$result=mysql_query($q);
							
							$pagelist=$page_obj->fpage();
							while($data=mysql_fetch_assoc($result)) {
				?>
							<tr>
								<td align="center"
									style="font-family: Arial, Helvetica, sans-serif; color: #F00; border-bottom: 1px solid #CCC;">
									<?=$data["user"];?>
								</td>
								<td align="center"
									style="font-family: Arial, Helvetica, sans-serif; color: #F00; border-bottom: 1px solid #CCC;">
									<?=$data["real_name"];?>
								</td>
								<td align="center"
									style="font-family: Arial, Helvetica, sans-serif; color: #F00; border-bottom: 1px solid #CCC;">
									<?=$data["id"];?>
								</td>
								<td align="center"
									style="font-family: Arial, Helvetica, sans-serif; color: #F00; border-bottom: 1px solid #CCC;">
									<?=$data["parent_id"];?>
								</td>
								<td align="center" style="border-bottom: 1px solid #CCC;">
									<?=$data["czje"];?>
								</td>
								<td align="center"
									style="font-family: Arial, Helvetica, sans-serif; color: #F00; border-bottom: 1px solid #CCC;">
									<?=$data["tjrq"];?>
								</td>
							    <td align="center" style="border-bottom: 1px solid #CCC;">
									第<?=$data["step"];?>阶段
								</td>
								<td align="center" style="border-bottom: 1px solid #CCC;">
									<?=$data["step_start_rq"];?>
								</td>

							 
								<td align="center" style="border-bottom: 1px solid #CCC;">
									<?php  
									   echo $data["xxtzztxjf"];
                                     ?>
								</td>
								<td align="center" style="border-bottom: 1px solid #CCC;">
									<?php  
									   echo $data["ytxje"];
                                     ?>
								</td>
								<td align="center" style="border-bottom: 1px solid #CCC;">
									<?php
									  if($data["cj_state"]==1) echo "已出局"; 
                                      else{
                                          if($data["ft_state"]==1)
                                              echo "已复投";
                                           else if($data["step"]==1)
                                              echo $data["next_ft_time"];
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