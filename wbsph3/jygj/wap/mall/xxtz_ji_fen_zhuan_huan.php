<?php

include "config.php" ;

include "myphplib/db.php";
include "myphplib/message.php";
include "config/check.php";
date_default_timezone_set("Asia/Shanghai");

$loginInfo = getLoginInfo();

 
 $type=$_POST['type'];
 $jinE=$_POST['jinE'];
 
 $sql=" select * from  xbmall_users where user_id=".$loginInfo['userid'];
 $userInfo =  getRow($sql);
 
 $nowDateStr = date("Y-m-d H:i:s", time());
 
 if($jinE<=0){
    die("转化金额需要大于0");
 } 
 
 
 if($type==0){//A阶段收益转化
     if($jinE>$userInfo['xxtzztxjf']){
         die("转化金额大于当前A阶段收益".$userInfo['xxtzztxjf']);
     } 
     $sql = "insert into zt_xxtzzs (user, xxtzztxjf, xxtzzxfjz, zsrq,comment) values('{$userInfo['user_name']}',-{$jinE},{$jinE},'{$nowDateStr}','减去A阶段投资收益{$jinE},增加消费积分{$jinE}')";
     mysql_query($sql);
     
     $sql="update xbmall_users set pay_points=pay_points+".$jinE.", xxtzztxjf=xxtzztxjf-".$jinE." where  user_id=".$loginInfo['userid'];
     mysql_query($sql);
 }
 
 if($type==1){//推荐收益转化
     if($jinE>$userInfo['tui_jian_shou_yi']){
         die("转化金额大于当前推荐收益".$userInfo['tui_jian_shou_yi']);
     }
     $sql = "insert into zt_xxtzzs (user, tui_jian_shou_yi, xxtzzxfjz, zsrq,comment) values('{$userInfo['user_name']}',-{$jinE},{$jinE},'{$nowDateStr}','减去推荐收益{$jinE},增加消费积分{$jinE}')";
     mysql_query($sql);
     
     $sql="update xbmall_users set pay_points=pay_points+".$jinE." ,tui_jian_shou_yi=tui_jian_shou_yi-".$jinE." where  user_id=".$loginInfo['userid'];
     mysql_query($sql);
 }
 
 
 if($type==2){//B阶段收益转化
     if($jinE>$userInfo['xxtzztxjf_b']){
         die("转化金额大于当前B阶段收益".$userInfo['xxtzztxjf_b']);
     }
     $sql = "insert into zt_xxtzzs (user, xxtzztxjf_b, xxtzzxfjz, zsrq,comment) values('{$userInfo['user_name']}',-{$jinE},{$jinE},'{$nowDateStr}','减去B阶段投资收益{$jinE},增加消费积分{$jinE}')";
     mysql_query($sql);
     
     $sql="update xbmall_users set pay_points=pay_points+".$jinE." , xxtzztxjf_b=xxtzztxjf_b-".$jinE."  where  user_id=".$loginInfo['userid'];
     mysql_query($sql);
 }
 
 die("转化成功");
 
 
 

 ?>
 
       
  
 
 