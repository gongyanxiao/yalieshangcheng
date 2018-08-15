<?php
  include "config.php" ;
include "myphplib/init.php";
include "myphplib/db.php";
include "myphplib/message.php";
include "config/check.php";
date_default_timezone_set("Asia/Shanghai");
 
$benJin = 3600; //初始投资的金额

$fanLi = 0; //系统分的可提现资金

$rate1 = 96*0.9 ;//系统第一阶段反的钱数
$rate2 = 22*0.9;//系统第二阶段反的钱数
$days  = 365 ;//要推演的天数
$data = array();//记录投资数据

$fenShu = $benJin/3600;

for ($i=0;$i<$fenShu;$i++){
	$data[$i]= array("fanLi"=>0);
}
for ($i=0;$i<$days;$i++){
	
	
}




 
 
// header("Location: xxcz_list.html");  
 
 ?>
  			  