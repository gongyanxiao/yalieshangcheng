<?php
 
include "../myphplib/init.php";
include "../myphplib/db.php";
include "../myphplib/message.php";
include "config/check.php";
date_default_timezone_set("Asia/Shanghai");
$id=$_REQUEST['id'];
$type=$_REQUEST['type'];
$czrq=$_REQUEST['czrq'];
$czrq= strtotime($czrq);
$shrq = time();

if(empty($id)){//充值记录id
	alertAndRelocation("id不能为空",  $_SERVER['HTTP_REFERER']);
}

if($type==1){//审核通过, 
	if(empty($czrq)){//当时充值日期不符合标准
		alertAndRelocation("审核通过时, 充值日期不能为空,格式为:2017-08-08 08:08:08", $_SERVER['HTTP_REFERER']);
	}
	$sql=" update zt_xxcz_ft set sh_state={$type},cw_sh_rq={$shrq}, czrq= {$czrq}   where id={$id}";
} else{//审核不通过是没有充值日期的
	$sql=" update zt_xxcz_ft set sh_state={$type},cw_sh_rq={$shrq}   where id={$id}";
}
startTrans();//开启事物
mysql_query($sql);
 


$sql="select * from zt_xxcz_ft where id={$id}";
$fuTou=getRow($sql);
$sql="select * from zt_xxcz where id={$fuTou['xxcz_id']}";
$chongZhi = getRow($sql);

tuiJianJiangLi($chongZhi);
/**
 * 查找推荐人, 给推荐人奖励, 查找用户现在的身份, 计算此次金额与以前的金额加起来是否是多于24万. 
 * 一直向上查找， 直到最顶层， 或者直到股东为止
 * 
 */

$sql="select * from xbmall_users where user_name='{$chongZhi['user']}'";
$user = getRow($sql);
loopUser($chongZhi,$user);



 commitTrans();//提交事物
// $sql="select * from  zt_xxcz where id={$id}";

 //发放直推奖励，直接增加提现积分
 function tuiJianJiangLi($chongZhi){
 	$xiaoFeiJinE = $chongZhi['czje']/0.12;
 	$ftje = $chongZhi['czje']*0.3;
 	if ($chongZhi['zhi_jie_user']){
 		$money =$xiaoFeiJinE*0.004;
 		$sql="update  xbmall_users  set xxtztxjf=xxtztxjf+".$money." where user='{$chongZhi['zhi_jie_user']}'"; //直推奖励是充值金额的千分之四
 		mysql_query($sql);
 		var_dump($sql);
 		$comment = "会员{$chongZhi['user']}复投{$ftje}直推奖励{$money}";
 		jiangLiLog($chongZhi,$chongZhi['zhi_jie_user'],$money,"{$comment}");
 	}

 	if ($chongZhi['jian_jie_user']){
 		$money =$xiaoFeiJinE*0.002;
 		$sql="update  xbmall_users  set xxtztxjf=xxtztxjf+".$money." where user='{$chongZhi['jian_jie_user']}'"; //间接奖励是充值金额的千分之二
 		mysql_query($sql);
 		var_dump($sql);
 		$comment = "会员{$chongZhi['user']}复投{$ftje}间接奖励{$money}";
 		jiangLiLog($chongZhi,$chongZhi['jian_jie_user'],$money,'{$comment}');
 	}
 }

 /**
  * 奖励日志
  * @param unknown $chongZhi 充值记录
  * @param unknown $userAcc 受益人账号
  * @param unknown $level 受益人级别
  * @param unknown $money 受益金额
  * @param unknown $comment
  */
 function jiangLiLog($chongZhi,$userAcc,$money,$comment){
 	$time = time();
 	$level = getOne("select level from xbmall_users where user_name='{$userAcc}'");
 	$sql="insert into zt_xxcz_jl(user,zuo_dan_user,jin_e,shou_yi_user,shou_yi_user_level,shou_yi_jin_e,rq,comment)
 	values('{$chongZhi['user']}','{$chongZhi['quan_fan_user']}',{$chongZhi['czje']},'{$userAcc}',$level,$money,{$time},'{$comment}')";
 	var_dump($sql);
 	mysql_query($sql);
 }
 
 
 /**
  * 层级奖励  代理， 运营中心   股东
  * @param unknown $chongZhi
  * @param unknown $userAcc
  * @param unknown $level
  * @param unknown $money
  */
 function levelJiangLiLog($chongZhi,$userAcc,$level,$money){
 	
 	$ftje = $chongZhi['czje']*0.3;
 	if ($level==2){
 		$comment = "会员{$chongZhi['user']}复投{$ftje}代理奖励{$money}";
 	}elseif ($level==3){
 		$comment = "会员{$chongZhi['user']}复投{$ftje}运营中心奖励{$money}";
 	}elseif ($level==4){
 		$comment = "会员{$chongZhi['user']}复投{$ftje}股东奖励{$money}";
 	}
 	$time =time();
 	$sql="insert into zt_xxcz_jl(user,zuo_dan_user,jin_e,shou_yi_user,shou_yi_user_level,shou_yi_jin_e,rq,comment)
 	values('{$chongZhi['user']}','{$chongZhi['quan_fan_user']}',{$chongZhi['czje']},'{$userAcc}',$level,$money,{$time},'{$comment}')";
 	var_dump($sql);
 	mysql_query($sql);
 }
 
 
 
  
 function  loopUser($chongZhi,$user){
 	$level=2;//从代理开始寻找
 	$sql="select user, parent_id ,level from xbmall_users where user_name='{$user['parent_id']}'";
 	$user= getRow($sql);//找出链中需要奖励的人
 	if (levelJiangLi($chongZhi,$user,$level))$level++;
 	
 	var_dump($sql);
 	while($user&& $user['level']<=4   ){//直到找到股东或没有上一级为止
 		if($user['level']<$level){//如果当前用户的级别小于要奖励的级别， 就查找用户的推荐人
 			if ($user['level']==4 || empty($user['parent_id']))break;//已经找到股东了 或没有推荐人了
 			
 			$sql="select user, parent_id ,level from xbmall_users where user_name='{$user['parent_id']}'";
 			var_dump($sql);
 			$user= getRow($sql);
 		}else{//当前用户的级别大于要奖励的级别
 			$level++;
 		}
 		
 		if (levelJiangLi($chongZhi,$user,$level))$level++;
 		
 		
 	}
 	return null;
 }
 
 
 /**
  * 对于代理， 运营中心, 股东的奖励
  * @param unknown $chongZhi
  * @param unknown $user
  * @param unknown $level
  * @return boolean
  */
 function levelJiangLi($chongZhi,$user,$level){
 	$xiaoFeiJinE = $chongZhi['czje']/0.12;
 	//如果等级对应， 那么就发放对应等级的奖励
 	var_dump($user['level'].":".$level);
 	if($user&& $user['level']==$level){
 		$money = $xiaoFeiJinE*0.003;//这个是按照层级来计算的
 		if($level==2){
 			$money = $xiaoFeiJinE*0.003;//代理的奖励
 		}elseif ($level==3){
 			$money = $xiaoFeiJinE*0.003;//运营中心的奖励
 		}elseif ($level==4){
 			$money = $xiaoFeiJinE*0.002;//股东的奖励
 		}
 		$sql="update  xbmall_users  set xxtztxjf=xxtztxjf+".$money." where user='{$user['user_name']}'"; //对于代理， 运营中心, 股东的奖励
 		mysql_query($sql);
 		var_dump($sql);
 		levelJiangLiLog($chongZhi, $user['user_name'], $level, $money);
 		return true; //人是对的， 奖励已经发放
 	}
 	return false;
 }
 
 

//alertAndRelocation("保存成功", "xxcz_list.php");//转到来时的页面
 

 ?>
   