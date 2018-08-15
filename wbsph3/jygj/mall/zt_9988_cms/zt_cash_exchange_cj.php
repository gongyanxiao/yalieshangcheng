<?php

include "../myphplib/page.php";

include "../myphplib/message.php";
include "../myphplib/db.php";
include  "config/check.php";
date_default_timezone_set("Asia/Shanghai");


 
$export=$_REQUEST['export'];

if($export==1){
    export();
} 

 
function export(){
    
    
    require_once "../myphplib/PHPExcel.php";
    
    // 实例化excel类
    $objPHPExcel = new PHPExcel();
    // 实例化excel图片处理类
    $objDrawing = new PHPExcel_Worksheet_Drawing();
    // 操作第一个工作表
    $objPHPExcel->setActiveSheetIndex(0);
    // 设置sheet名
    $objPHPExcel->getActiveSheet()->setTitle('提现记录列表');
    
    // 列名表头加粗
    $objPHPExcel->getActiveSheet()->getStyle('A1:R1')->getFont()->setBold(true);
    // 列名赋值
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '日期');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '姓名');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '金额');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '类型');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '银行卡号');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '开户行');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', '用户名');
    
    //技术审核通过, 财务未审核 
    $sql = "select b.real_name,b.bank_num, a.* from  xbmall_xb_ti_xian a join  xbmall_users b  
 on a.user=b.user_name  where a.jssh=0 and a.cwsh=0  and  date  >='2018-06-14' 
order  by   b.real_name, a.date asc  ";
    
    $result=mysql_query($sql);
    $row_num = 2;
    while ( $value = mysql_fetch_array ( $result ) ) {
        // 设置单元格高度
        $objPHPExcel->getActiveSheet()->getRowDimension($row_num)->setRowHeight(32);
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(0) ->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(4) ->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(5) ->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(6) ->setWidth(25);
        // 设置排序列、是否显示列居中显示
        // 设置单元格数值
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_num, $value['date']);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $row_num, $value['real_name']  );
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $row_num, $value['je']);
        
        if($value['type']==1){
            $typeName = "推荐收益";
        }else{
            $typeName = "订单收益";
        }
        if($value['type']==0){
            $typeName = "A阶段订单收益";
        }else  if($value['type']==1){
            $typeName = "推荐收益";
        }else  if($value['type']==2){
            $typeName = "B阶段订单收益";
        }
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $row_num, $typeName);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $row_num, "卡号".$value['bank_num']);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $row_num, $value['yhkh'].$value['khh']);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $row_num, $value['user']);
        $row_num++;
                
    }
    
    $outputFileName = '提现记录_' . time() . '.xls';
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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>提现记录</title>
<?php include "style.php";?>

<script type=text/javascript src="../js/jquery.min.js"></script>
<script src="../lib/sea.js"></script>
<script>
seajs.config({
  alias: {
    "jquery": "../lib/jquery-1.10.2.js"
  }
});

function  exportExcel(){

	window.location.href="/jygj/mall/zt_9988_cms/zt_cash_exchange_cj.php?export=1";
	
}
</script>



<script language=javascript> 

<!-- 
seajs.use(['jquery', '../src/dialog'],function ($, dialog){
$('button[data-event=ts]').on('click',function(){
	
var id= $(this).attr("name");
var d = dialog({
title: '审核操作',
content:'&nbsp;&nbsp;&nbsp;&nbsp;<select name="sh" id="sh"><option value="1">通过</option><option value="2">不通过</option></select><br>备注:<textarea name="bz" id="bz" cols="45" rows="5"></textarea><br>',
ok: function(){
//提交到后台查询
var  sh= $('#sh').val();
var  bz= $('#bz').val();
var mysession="<?=$_SESSION['uniqid']?>";
var url="<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];?>";
var dataString='id='+id+'&bz='+bz+'&sh='+sh+'&url='+url+'&mysession='+mysession; 
$.ajax({  
type: "POST",  
url: "zt_cash_pro_cj.php",  
data:dataString,  
cache:false,  
success:function(html){  
//$("#fhl").html("<pre>" + html + "</pre>");
$("#jygj").html(html);
 }  
 }); 
this.close(value);
this.remove();
	 }
	});
d.addEventListener('close', function () {

	});
d.show();
  });
  
});
// --> 
</script> 

</head>

<body>  
  <div id="jygj"></div>
<div style="z-index:100:width:100%;height:32px;">

<table width="100%" border="0" height="32px;" cellpadding="0" cellspacing="0">
  <tr>
    <td width="2%" height="22" background="images/position1.jpg"></td>
    <td width="98%" align="left" background="images/position2.jpg">您当前的位置 >>提现记录&nbsp;总计：<a href="javascript:;">
      <font color="#FF0000" size="3px;" face="Arial, Helvetica, sans-serif"><?php
     
$so=trim(htmlspecialchars($_REQUEST['xm']));
$where =" where 1";
if($so<>""){
	if($so!=""){
		$where .=" and (a.user like '%$so%' or  b.real_name like '%$so%') ";
	}
} 


 $sq2="select sum(a.je) as ze from xbmall_xb_ti_xian a join xbmall_users b on a.user=b.user_name $where ";
$sf2=getRow($sq2);
echo number_format($sf2['ze'],2);
									?></font>
    </a> 元 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="width:500px;"><a href="zt_cash_nsh.php">财务未处理（<?php
$sq2="select sum(je) as ze from xbmall_xb_ti_xian  where zt='0'  ";
$sf2=getRow($sq2);
echo number_format($sf2['ze'],2);
									?>)</a> </span>&nbsp;&nbsp;<span style="width:500px;">&nbsp;<a href="zt_cash_pass.php">已驳回（<?php
									$sq2="select sum(je) as ze from xbmall_xb_ti_xian  where zt='3'   ";
 
$sf2=getRow($sq2);
echo number_format($sf2['ze'],2);
									?>） </a><a href="zt_cash_sh.php">&nbsp;已通过（<?php
									$sq3="select sum(a.je) as ze from xbmall_xb_ti_xian a  join xbmall_users b on a.user=b.user_name $where and cwsh=1 ";
$sf2=getRow($sq3);
echo number_format($sf2['ze'],2);
									?>） </a></span></td>
  </tr>
</table>

</div>
<div id="jsgj"></div>


  <table width="100%" border=0 align="center" cellspacing=0 bordercolorlight="#94D0EA" 

bordercolordark="#F5FBFE" style="margin-top:15px;">
<tr>
    <td height="36" colspan="13" align="left" bgcolor="#DBEBFA">
      <form method="get" enctype="multipart/form-data" style="width:500px;" >
  &nbsp;&nbsp; &nbsp;用户名
        <input type="text" name="xm" id="xm" value="<?php echo $so;?>" style="border:1px solid #ccc;height:20px;"/>
  &nbsp;&nbsp;&nbsp;
		<input type="submit" name="button" id="button" value="搜索记录"  style="background-color:#F63;color:#FFF;border:0px;line-height:14px;height:22px;cursor:pointer;"/>
		<input type="button"   value="导出" onclick="exportExcel()"  style="background-color:#F63;color:#FFF;border:0px;line-height:14px;height:22px;cursor:pointer;"/>
		
		
		
      </form></td>
    </tr>

<tr>
  <td width="5%" height="29" align="center" bgcolor="#FFFFFF" class="bk"><span class="btdx">

ID</span></td>
  <td width="11%" align="center" class="bk"><span class="btdx">提现日期</span></td>
    <td width="7%" align="center" class="bk"><span class="btdx">姓名</span></td>
    <td width="5%" align="center" class="bk"><span class="btdx">金额</span></td>
    <td width="5%" align="center" class="bk"><span class="btdx">类型</span></td>
    <td width="7%" align="center" class="bk"><span class="btdx">银行卡号</span></td>
    <td width="12%" align="center" class="bk"><span class="btdx">开户行</span></td>
    <td width="9%" align="center" class="bk"><span class="btdx">用户名</span></td>
  
  <td width="5%" align="center" class="bk"><span class="btdx">财务审核</span></td>
  <td width="7%" align="center" class="bk"><span class="btdx">说明</span></td>
  <td width="7%" align="center" class="bk"><span class="btdx">审核时间</span></td>


  
  <td width="9%" align="center" class="bk"><span class="btdx">监测来路URL</span></td>
  <td width="9%" align="center" class="bk"><span class="btdx">操作面板</span></td>
</tr>
<?php


$sh=strip_tags($_POST['sh']);
 
error_reporting(0);
 
//省份
$sql="select * from zt_qx where yh='$yh'";
$sf1=getRow($sql);
$sf=$sf1["area"];
 

?>
<?php

$phpfile = 'zt_cash_exchange_cj.php?xm='.$so;
$page= isset($_GET['page'])?$_GET['page']:1;
if($_GET['page']==""){
    $page = 1;
}


$q="select a.*  from xbmall_xb_ti_xian a join xbmall_users b on a.user=b.user_name {$where}    ";

$query=mysql_query($q);
$counts = mysql_num_rows($query);
$getpageinfo=page($page,$counts,$phpfile);
$sql.=$getpageinfo['sqllimit'];
$data=$row=array();
$result=mysql_query($sql,$db);
while($row=mysql_fetch_array($result)){
 $data[]=$row;
}
if($page>1){
	$page=12*$page-12+1;
}
if($so<>"" ){
	$col=1000;   
}else{
	$col=12;   
}
$cols=1;  
$page=$page-1;


 
 $query1="select a.*  from xbmall_xb_ti_xian a join xbmall_users b on a.user=b.user_name   $where   order by a.date desc limit $page,12";
 $result= mysql_query($query1);   
  while($row1=mysql_fetch_array($result)) {   
      $col=$col+ 1;   
      if($col%$cols ==1)
  ?>
  
<tr bgcolor="#cccccc">
 <td height="79" align="center" bgcolor="#FFFFFF" class="bk"><?php echo $row1['id']?></td>
    <td align="center" bgcolor="#FFFFFF" class="bk" style="color: #39F;"><?php echo $row1['date']?></td>
     <td align="center" bgcolor="#FFFFFF" class="bk"><span style="color:red;font-size:14px;"><?php
	$yh=$row1['user'];
	$sql1="select * from xbmall_users where user_name='$yh' ";
	$bank=getRow($sql1);
	 echo $bank['real_name'].'<br>';echo $bank['card'].'<br>';?></span></td>
	 <td align="center" bgcolor="#FFFFFF" class="bk" style="color: #39F;"><span style="color: #333;font-size:14px;"><?php echo $row1['je'];?></span></td>
	 <td align="center" bgcolor="#FFFFFF" class="bk" style="color: #39F;"><?php if( $row1['type']==0)echo 'A阶段订单收益';
	 if( $row1['type']==1)echo '推荐收益';if( $row1['type']==2)echo 'B阶段订单收益';
?></td>
	  <td align="center" bgcolor="#FFFFFF" class="bk"><?=$bank['bank_num'];?></td>
    <td align="center" bgcolor="#FFFFFF" class="bk"><?=$bank['bank'];?><?
     echo $bank['bank_kh'];
    ?>
    </td>
    
  
    
    <td align="center" bgcolor="#FFFFFF" class="bk" style="color: #39F;"><span class="bk" style="color: #39F;"><span style="color: #333;font-size:14px;"><?php echo $row1['user']?></span></span></td>
    
   
    <td align="center" bgcolor="#FFFFFF" class="bk" style="color: #39F;"><span style="color:red;font-size:14px;">
      <?php if($row1['cwsh']=="0"){echo "处理中";}elseif($row1['cwsh']=="2"){echo "不通过";}else{echo '<font color="green">已通过</font>';}?>
    </span></td>
    <td align="center" bgcolor="#FFFFFF" class="bk" style="color: #39F;"><span style="color:red;font-size:14px;"><?php echo $row1['comment']?></span></td>
    <td align="center" bgcolor="#FFFFFF" class="bk"><?php echo $row1['shrq']?$row1['shrq']:"无";?></td>
 
   
    <td align="center" bgcolor="#FFFFFF" class="bk"><?php echo $row1['url']?$row1['url']:"无";?></td>
    <td align="center" bgcolor="#FFFFFF" class="bk">
	  <?php
	  if( $row1['cwsh']==0 && $role_id==3 ){
	  ?>
      <button type="submit" data-event="ts" style="background-color: #F60;color:#FFF;width:62px;height:25px;cursor:pointer;" name="<?=$row1['id'];?>">审核</button><br>
  <?php
  }
  ?>
  
  </td>


</tr>

	<?php

	 echo   "</td>\n";   
      if   ($col%$cols==0)  
       echo   "</tr>\n";   
  }   

  if($col%$cols!=0){   
      for($i=1;$i<=$cols-$col%$cols; $i++) {   
          echo   "<td>&nbsp;</td>\n";   
      }   
  }   

  if($i>1) 
    echo   "</tr>\n";   

@mysql_close($db);

?>



<table width="100%" height="24" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="38" align="left"> </td>
    <td width="1373" align="left">
	<?php
echo '<BR>'.$getpageinfo['pagecode'];
?></td>
  </tr>
</table>
</table>



</body>

</html>
