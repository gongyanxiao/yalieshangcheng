<?php
include "../myphplib/db.php";
include "../myphplib/message.php";
include "config/check.php";
require_once "../myphplib/PHPExcel.php";

function  export(){
   
    require_once "../myphplib/PHPExcel.php";
  
    // 实例化excel类
    $objPHPExcel = new PHPExcel();
    // 实例化excel图片处理类
    $objDrawing = new PHPExcel_Worksheet_Drawing();
    // 操作第一个工作表
    $objPHPExcel->setActiveSheetIndex(0);
    // 设置sheet名
    $objPHPExcel->getActiveSheet()->setTitle('聚珠变动记录列表');
    
    // 列名表头加粗
    $objPHPExcel->getActiveSheet()->getStyle('A1:R1')->getFont()->setBold(true);
    // 列名赋值
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '用户');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '发放日期');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '总聚珠数');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '服务费');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '储备聚珠');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '收益聚珠');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', '消费聚珠');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', '店铺资金');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', '投资收益');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', '推荐收益');
    $objPHPExcel->getActiveSheet()->setCellValue('K1', '描述');
    $objPHPExcel->getActiveSheet()->setCellValue('L1', '姓名');
    $row_num = 2;
    
    $user=$_REQUEST['user'];
    $type=$_REQUEST['type'];
    $where =" 1 ";
    if(isset($user) &&  $user!=""){
        $where =$where." and a.user like '%$user%'";
    }
    if(isset($type)&&  $type!="-1"){
        $where =$where." and a.type ={$type}";
    } 
    
    $q="select  a.id,a.cz_id,a.user, a.zsrq , a.jf,  a.comment,a.xxtzzfwf,a.xxtzzcbjz,a.xxtzzsyjz,a.xxtzzxfjz,a.xxtzztxjf, a.dian_pu_zi_jin,
a.tui_jian_shou_yi  from zt_xxtzzs a    where  ".$where." order by a.id desc";
  
    $result=mysql_query($q);
    
    while ( $value = mysql_fetch_array ( $result ) ) {
        $value['xm'] = getOne("select xm from xbmall_users where user_name='{$value['user']}'");
        // 设置单元格高度
        $objPHPExcel->getActiveSheet()->getRowDimension($row_num)->setRowHeight(32);
        // 设置排序列、是否显示列居中显示
        // 设置单元格数值
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_num,  $value['user']);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $row_num,  $value['zsrq']  );
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $row_num,  $value['jf']);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $row_num, $value['xxtzzfwf']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $row_num, $value['xxtzzcbjz']);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $row_num, $value['xxtzzsyjz']);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $row_num, $value['xxtzzxfjz']);
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $row_num, $value['dian_pu_zi_jin']);
        $objPHPExcel->getActiveSheet()->setCellValue('I' . $row_num, $value['xxtzztxjf']); //history_zsq,zsq,tj_name,reg_time
        $objPHPExcel->getActiveSheet()->setCellValue('J' . $row_num, $value['tui_jian_shou_yi']);
        $objPHPExcel->getActiveSheet()->setCellValue('K' . $row_num, $value['comment']);
        $objPHPExcel->getActiveSheet()->setCellValue('L' . $row_num, $value['xm']);
        $row_num++;
        
    }
 
    $outputFileName = '赠送记录_' . time() . '.xls';
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
<title><?=_TITLE_?></title>
<meta name="keyword" content="<?=_KEYWORD_?>" />
<meta name="description" content="<?=_DESCRIPTION_?>" />
<link href="/Public/ico.ico" type="image/x-icon" rel="shortcut icon" />
<link rel="stylesheet" type="text/css" href="/jygj/Public/home/css/page.css">
<link rel="stylesheet" type="text/css" href="/jygj/Public/home/css/index.css" />
<script type="text/javascript" src="/jygj/Public/home/js/jquery-1.7.2.js"></script>
<script type="text/javascript" src="/jygj/Public/js/jquery.flexslider-min.js"></script>
<script type="text/javascript" src="/jygj/Public/js/clipboard.min.js"></script>


</head>


<body style="background: #F7F7F7">
	<div class="warp">
		<!------最顶部开始------>
		<!------logo结束------>
		<script>
			function f1(url) {
				window.location.href = url;
			}

			function submitExport(type){
				document.getElementById("export").value=type;
				document.getElementById("searchForm").submit();
			   
			}
		</script>

		<!------导航结束------>

		<div style="z-index: 100:width:100%; height: 22px;">

			<table width="100%" border="0" height="22px;" cellpadding="0"
				cellspacing="0">
				<tr>
					<td width="2%" height="22"><img src="images/position1.jpg"></td>
					<td width="98%" align="left" background="images/position2.jpg">您当前的位置>>配置管理</td>
				</tr>
			</table>

		</div>
		<div id="ryd">
		   <form action="xxtzzs_list.php" id="searchForm" method="get">
		    <input type="text" name="user" value="<?php echo $_REQUEST['user'];?>">
		    <select name="type" id="type">
		     <option value="-1">请选择</option>
		     <option value="0">每日赠送</option>
		     <option value="1">推荐奖励</option>
		    </select>
		    <input type="hidden" name="export" id="export">
		    <input type="button" value="搜索" onclick="submitExport('0')">
		    <input type="button" value="导出" onclick="submitExport('1')" >
		   </form>
		   
		</div>
		<table border="1" cellspacing="0" cellpadding="0" class="warp_f_th">
			<tr>
				<th align="center">用户</th>
				<th align="center">姓名</th>
				<th align="center">发放日期</th>
				<th align="center">总聚珠数</th>
				<th align="center">服务费</th>
				<th align="center">储备聚珠</th>
				<th align="center">收益聚珠</th>
				<th align="center">消费聚珠</th>
				<th align="center">店铺资金</th>
				<th align="center">投资收益</th>
				<th align="center">推荐收益</th>
				<th align="center">描述</th>
			</tr>
			<?php
				include "../page.class.php";
					date_default_timezone_set("Asia/Shanghai");
// 					$user=$_COOKIE['zt_user2'];
                        $user=$_REQUEST['user'];
                      
                        $type=$_REQUEST['type'];
                        $where =" 1 ";
                        if(isset($user) &&  $user!=""){
                        	$user = trim($user);
						    $where =$where." and a.user like '%$user%'";
						}
						if(isset($type)&&  $type!="-1"){
						    $where =$where." and a.type ={$type}";
						} 
						$num  = getCount("zt_xxtzzs a ", $where);
				     	$per = 12;
						$page_obj=new Page($num,$per);
 						$q="select a.id,a.cz_id,a.user, a.zsrq , a.jf,  a.comment,a.xxtzzfwf,a.xxtzzcbjz,a.xxtzzsyjz,a.xxtzzxfjz,a.xxtzztxjf, a.dian_pu_zi_jin,
a.tui_jian_shou_yi  from zt_xxtzzs a    where  ".$where." order by a.zsrq desc limit ".($page_obj->page-1)*$per.",".$per;
 					    $result=mysql_query($q);
						$pagelist=$page_obj->fpage();
					while ( $data2 = mysql_fetch_array ( $result ) ) {
					    $data2['real_name'] = getOne("select real_name from xbmall_users where user_name='{$data2['user']}'");
				?>
			<tr>
				<td align="center"
					style="font-family: Arial, Helvetica, sans-serif; color: #F00; border-bottom: 1px solid #CCC;">
					<?=$data2["user"];?>
				</td>
				<td align="center"
					style="font-family: Arial, Helvetica, sans-serif; color: #F00; border-bottom: 1px solid #CCC;">
					<?=$data2["real_name"];?>
				</td>
				<td align="center"
					style="font-family: Arial, Helvetica, sans-serif; color: #F00; border-bottom: 1px solid #CCC;">
					<?=$data2["zsrq"];?>
				</td>
				<td align="center" style="border-bottom: 1px solid #CCC;"><?=$data2["jf"];?>
				</td>

				<td align="center" style="border-bottom: 1px solid #CCC;"><?=$data2["xxtzzfwf"];?>
				</td>
				<td align="center" style="border-bottom: 1px solid #CCC;"><?=$data2["xxtzzcbjz"];?>
				</td>
				<td align="center" style="border-bottom: 1px solid #CCC;"><?=$data2["xxtzzsyjz"];?>
				</td>
				<td align="center" style="border-bottom: 1px solid #CCC;"><?=$data2["xxtzzxfjz"];?>
				</td>
				<td align="center" style="border-bottom: 1px solid #CCC;"><?=$data2["dian_pu_zi_jin"];?>
				</td>
				<td align="center" style="border-bottom: 1px solid #CCC;"><?=$data2["xxtzztxjf"];?>
				</td>
				<td align="center" style="border-bottom: 1px solid #CCC;"><?=$data2["tui_jian_shou_yi"];?>
				</td>
				<td align="center" style="border-bottom: 1px solid #CCC;"><?=$data2["comment"];?>
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
</body>
</html>