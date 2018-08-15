<?php
error_reporting(0);
 
header("Content-type:text/html; charset=utf-8"); 
include "../myphplib/db.php";
include "../myphplib/message.php";
include "config/check.php";
session_start(); 

include "getRoleId.php";


$sh=trim(htmlspecialchars($_POST["sh"]));
$url=trim(htmlspecialchars($_POST["url"]));
$page =trim(htmlspecialchars($_POST["page"]));
$id=trim(htmlspecialchars($_POST["id"]));
if ($id==''){
	alertAndRelocationHistory("操作失败， 请重新操作");
}
$session=trim(htmlspecialchars($_POST["mysession"]));
$date=date("Y-m-d H:i:s");
 


$sql="select je,user,  cwsh     from xbmall_xb_ti_xian where id={$id} ";
$tiXian=getRow($sql);
$userAcc=$tiXian['user'];
 
$txjf=$tiXian['je'];//提现
if(  $tiXian['cwsh']!=2 && $sh==2) {//没有被驳回过, 被驳回
	
	//插入积分变动记录， 返回用户的积分
	$sql ="insert into xbmall_xb_xxtzzs (user,jf,zsrq,comment) values('{$userAcc}',{$tiXian['je']},'{$date}','提现驳回， 返回积分')";
	 
	mysql_query($sql);
	
    //修改用户的账户, 把用户的积分返回去
    $sql="update xbmall_users set user_money=user_money+{$txjf} where user_name='{$userAcc}'";
    mysql_query($sql);
	
} 


$shrq = time();
if($role_id==3){//财务人员的角色
    $shenHe = " cwsh={$sh},cwshrq={$shrq} , cw_bz='{$bz}'";
}

 
//修改提现的审核状态,  
$sql1="update xbmall_xb_ti_xian set {$shenHe}  where id={$id} ";
$re1=mysql_query($sql1);

if($re1){
    if(isset($page)&& $page!=''){
        echo '<script>alert("操作提交完成!");window.self.location.href="'.$url.'&page='.$page.'";</script>';
    }else {
        echo '<script>alert("操作提交完成!");window.self.location.href="'.$url.'";</script>';
    }
  
}

 
?>