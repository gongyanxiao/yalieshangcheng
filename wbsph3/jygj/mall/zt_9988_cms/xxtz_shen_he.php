<?php
 
include "../myphplib/init.php";
include "../myphplib/db.php";
include "../myphplib/message.php";
include "config/check.php";
include "getRoleId.php";

session_start();
date_default_timezone_set("Asia/Shanghai");
$id=$_REQUEST['id'];
$type=$_REQUEST['type'];
$czrq=$_REQUEST['czrq'];
$js_bz=$_REQUEST['js_bz'];
$cw_bz=$_REQUEST['cw_bz'];
if (!isset($js_bz)){
    $js_bz =' ';
} 
if (!isset($cw_bz)){
    $cw_bz =' ';
} 
 
$czrq= strtotime($czrq);
$shrq = time();


if(empty($id)){//充值记录id
	alertAndRelocation("id不能为空",  $_SERVER['HTTP_REFERER']);
}



// if($type==1){//审核通过
// 	$next_ft_time = $shrq+3600*24*45;
// 	$next_ft_time = " ,next_ft_time={$next_ft_time}";
// }else {
// 	$next_ft_time = "";
// }

if($role_id==3 || $role_id==6 ){ //财务人员的角色
    $shenHe =  " cw_sh_state={$type},cwshrq={$shrq} , jsshrq={$shrq},js_sh_state={$type}  ";
    if(isset($cw_bz)){
        $shenHe= $shenHe.",cw_bz='{$cw_bz}' ";
    }
    if(isset($js_bz)){
        $shenHe= $shenHe.",js_bz='{$js_bz}' ";
    }
}
 

startTrans();//开启事物

$sql="select * from   xbmall_xb_cz  where id={$id} and js_sh_state=1 and cw_sh_state=1";
if(getRow($sql)!=null){
    alertAndRelocation("此笔充值已经审核过,不要重复点审核", "xxtz_cz_list.php");//转到来时的页面
}


if($type!=1){//审核不通过是没有充值日期的
	$sql=" update xbmall_xb_cz set {$shenHe}  where id={$id}";
	mysql_query($sql);
    alertAndRelocation("保存成功", "xxtz_cz_list.php");//转到来时的页面
	 
}

//审核通过,
if(empty($czrq) && $role_id==3){//当时充值日期不符合标准,审核人是技术时不判断时间
	alertAndRelocation("审核通过时, 充值日期不能为空,格式为:2017-08-08 08:08:08", $_SERVER['HTTP_REFERER']);
}

//修改审核状态, 本阶段的起始时间, 修改投资的局中状态,  充值日期,

if($role_id==3 || $role_id==6 ){
    $sql=" update xbmall_xb_cz set {$shenHe}, czrq= {$czrq}    where id={$id}";
    mysql_query($sql);
}  


 

if($role_id==3 ||$role_id==6 ){//财务审核通过后, 进行账户资金变化
    $sql=" select  * from xbmall_xb_cz  where id={$id}";
    $bean = getRow($sql);
    
    $dateStr =   date("Y-m-d H:i:s", $shrq);
    
    $sql="select * from  xbmall_users where user_name='{$bean['user']}'";
    $user= getRow($sql);
    $pay_points= $user['pay_points']+$bean['czje'];
    //增加变动记录
    $sql ="insert into zt_xxtzzs (user,zsrq,comment) values('{$bean['user']}','{$dateStr}','您的充值审核通过, 消费积分增加{$bean['czje']},已为{$pay_points}')";
    mysql_query($sql);
    
    $sql="update  xbmall_users set pay_points=pay_points+{$bean['czje']} where user_name='{$bean['user']}'";
    mysql_query($sql);
}


 commitTrans();//提交事物

 alertAndRelocation("保存成功", "xxtz_cz_list.php");//转到 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 

 ?>
   