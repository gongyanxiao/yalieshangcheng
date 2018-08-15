<?php
 
//店铺审核
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


$step=0;

if(empty($id)){//充值记录id
	alertAndRelocation("id不能为空",  $_SERVER['HTTP_REFERER']);
}


if($_SESSION['tzcw']=="1"){//登录者是投资管理的财务
	$shzt =" cw_sh_state={$type} ,cwshrq={$shrq}";
}elseif($_SESSION['tzjs']=="1"){//登录者是投资管理的技术
	$shzt =" js_sh_state={$type} ,jsshrq={$shrq}";
} 
if($type==1){//审核通过, 
	if(empty($czrq)){//当时充值日期不符合标准
		alertAndRelocation("审核通过时, 充值日期不能为空,格式为:2017-08-08 08:08:08", $_SERVER['HTTP_REFERER']);
	}
	$step=1;
	//进入第一阶段，状态为局中
	$sql=" update zt_xxtz set  {$shzt}, step_start_rq={$shrq},cj_state=0, czrq= {$czrq}, step={$step} where id={$id}";
} else{//审核不通过是没有充值日期的
	$sql=" update zt_xxtz set  {$shzt},  step={$step} where id={$id}";
}
startTrans();//开启事物
mysql_query($sql);
 


$sql="select * from zt_xxtz where id={$id}";
$touZi = getRow($sql);
tuiJianJiangLi($touZi);

/**
 * 查找推荐人, 给推荐人奖励, 查找用户现在的身份, 计算此次金额与以前的金额加起来是否是多于24万. 
 * 一直向上查找， 直到最顶层， 或者直到股东为止
 * 
 */

$sql="select * from xbmall_users where user_name='{$touZi['user']}'";
$user = getRow($sql);
loopUser($touZi,$user);
 commitTrans();//提交事物
// $sql="select * from  zt_xxtz where id={$id}";


 shengJiShangJia($touZi);
 alertAndRelocation("保存成功", "xxcz_list.php");//转到来时的页面
 
 
 /**
  * 满足条件的升级成为商家()
  */
 function  shengJiShangJia($touZi){
 	
 	$sql="select level from xbmall_users where user_name='{$touZi['user']}'";
 	$level = getOne($sql);
 	if($level>0){//消费者已经不是普通的会员,不进行升级商家的判断
 		return  ;
 	}
 	
 	/**
 	 * 如果此次投资的用户本身投资加上下两级的投资综合超过24万, 那么此次投资的用户升级为商家, 如果用户的上级是普通用户, 用户的上级也升级为商家
 	 * #自身消费,   自己做单,    直接推荐的 会员做单审核通过的总金额
 	 */
 	$sql="SELECT SUM(czje) FROM `zt_xxtz` WHERE  (user='{$touZi['user']}' or   quan_fan_user='{$touZi['user']}' or  zhi_jie_user='{$touZi['user']}') and  sh_state=1 " ;
 	$czje = getOne($sql);
 	if($czje>=240000){//消费者的及消费者推荐的人消费额或做单金额之和大约等于24万, 把消费者升级为商家
 		$sql="update xbmall_users set level=level+1 where user='{$touZi['user']}'";
 		mysql_query($sql);
 		var_dump("消费者{$touZi['user']}升级成为商家");
 			//有人升级商家, 触发至直接推荐人升级代理事件
 		shengJiDaiLi($touZi['user']);//触发升级代理
 		 
 	
 	}
 }
 
 
 /**
  * 
  * @param unknown $userAcc 已经升级为商家的账号
  * $isChuFa  是否要触发   0:不触发  1:触发
  */
 function  shengJiDaiLi($userAcc){
 	//查询出商家
 	$sql="select level, parent_id from xbmall_users where user_name='{$userAcc}'";
 	$user = getRow($sql);
 	
 	$sql="select level, user,parent_id from xbmall_users where user_name='{$user['parent_id']}'";
 	$tuiJianRen= getRow($sql);
 	if($row['level']>1){//直接推荐人已经超过了商家, 
 		return   ;
 	}else{
 		if($tuiJianRen['level']==0){//直接推荐人是会员, 那么升级直推人为商家, 
 			$sql="update xbmall_users set level=level+1 where user='{$tuiJianRen['user']}'";
 			mysql_query($sql);
 			var_dump("消费者{$userAcc}的推荐人{$tuiJianRen['user']}跟着消费者一起成为商家");
 			shengJiDaiLi($tuiJianRen['user']);
 		}
 	}
 	
 	//查询商家的推荐人下面有几个商家
 	$sql="select count(0) as num from  xbmall_users where parent_id='{$tuiJianRen['user']}' and level=1";
    $num = getOne($sql);
    var_dump($sql);
    if($num>=10){//给商家的推荐人升级为代理
    	$sql = "update xbmall_users set level=level+1 where user='{$tuiJianRen['user']}'";
    	mysql_query($sql);
    	shengJiYunYingZhongXin($tuiJianRen['user']);//有人升级代理,  触发升级运营中心事件
    }else{
    	var_dump("商家{$tuiJianRen['user']}下面有{$num}个商家, 还不能升级为代理");
    }
    
 	//直接推荐人是商家
 }
 
 /**
  * 
  * @param unknown $userAcc升级为代理的账号
  */
 function  shengJiYunYingZhongXin($userAcc){
 	$sql="select level, parent_id from xbmall_users where user_name='{$userAcc}'";
 	$row = getRow($sql);
 	if($row['level']>2){//直接推荐人已经超过了代理,
 		return   ;
 	}
 	//查询代理的推荐人下面有几个代理
 	$sql="select count(0) as num from  xbmall_users where parent_id='{$row['parent_id']}' and level=2";
 	$num = getOne($sql);
 	if($num>=5){//给代理的推荐人升级为运营中心
 		$sql = "update xbmall_users set level=level+1 where user='{$row['parent_id']}'";
 		mysql_query($sql);
 		shengJiGuDong($row['parent_id']);//有人升级运营中心,  触发升级股东事件
 	}else{
 		var_dump("代理{$userAcc}下面有{$num}个代理, 还不能升级为运营中心");
 	}
 	
 }
 
 
 
 /**
  *
  * @param unknown $userAcc升级为运营中心的账号
  */
 function  shengJiGuDong($userAcc){
 	$sql="select level, parent_id from xbmall_users where user_name='{$userAcc}'";
 	$row = getRow($sql);
 	if($row['level']>3){//直接推荐人已经超过了运营中心,
 		return   ;
 	}
 	//查询代理的推荐人下面有几个代理
 	$sql="select count(0) as num from  xbmall_users where parent_id='{$row['parent_id']}' and level=3";
 	$num = getOne($sql);
 	if($num>=3){//给运营中心的推荐人升级为股东
 		$sql = "update xbmall_users set level=level+1 where user='{$row['parent_id']}'";
 		mysql_query($sql);
 	}else{
 		var_dump("运营中心{$userAcc}下面有{$num}个运营中心, 还不能升级为股东");
 	}
 	
 }
 
 
/**
 * 发放直推奖励，推荐人得到3600的一份投资
 */
 function tuiJianJiangLi($touZi){
 	$xiaoFeiJinE = $touZi['czje']/0.12;
 	$sql="insert into zt_xxtz (czyhkh,skyhkh,user,czje,tjrq,czrq,cw_sh_state,js_sh_state,cj_state,step_start_rq,cwshrq,jsshrq,step,ktxje,ytxje,yfje,ft_id,ft_count,next_ft_time,type)
  values () ";
 	
 	if ($touZi['zhi_jie_user']){
 		$money =$xiaoFeiJinE*0.004;
 		$sql="update  xbmall_users  set xxtztxjf=xxtztxjf+".$money." where user='{$touZi['zhi_jie_user']}'"; //直推奖励是充值金额的千分之四
 		mysql_query($sql);
 		var_dump($sql);
 		$comment = "会员{$touZi['user']}消费{$xiaoFeiJinE}直推奖励{$money}";
 		jiangLiLog($touZi,$touZi['zhi_jie_user'],$money,"{$comment}");
 	}
 	
 	if ($touZi['jian_jie_user']){
 		$money =$xiaoFeiJinE*0.002;
 		$sql="update  xbmall_users  set xxtztxjf=xxtztxjf+".$money." where user='{$touZi['jian_jie_user']}'"; //间接奖励是充值金额的千分之二
 		mysql_query($sql);
 		var_dump($sql);
 		$comment = "会员{$touZi['user']}消费{$xiaoFeiJinE}间接奖励{$money}";
 		jiangLiLog($touZi,$touZi['jian_jie_user'],$money,'{$comment}');
 	}
 }
 
 /**
  * 奖励日志
  * @param unknown $touZi 充值记录
  * @param unknown $userAcc 受益人账号
  * @param unknown $level 受益人级别
  * @param unknown $money 受益金额
  * @param unknown $comment
  */
 function jiangLiLog($touZi,$userAcc,$money,$comment){
 	$time = time();
 	$level = getOne("select level from xbmall_users where user_name='{$userAcc}'");
 	$sql="insert into zt_xxtz_jl(user,zuo_dan_user,jin_e,shou_yi_user,shou_yi_user_level,shou_yi_jin_e,rq,comment)
 	values('{$touZi['user']}','{$touZi['quan_fan_user']}',{$touZi['czje']},'{$userAcc}',$level,$money,{$time},'{$comment}')";
 	var_dump($sql);
 	mysql_query($sql);
 }
 
 
 /**
  * 层级奖励  代理， 运营中心   股东
  * @param unknown $touZi
  * @param unknown $userAcc
  * @param unknown $level
  * @param unknown $money
  */
 function levelJiangLiLog($touZi,$userAcc,$level,$money){
 	
 	if ($level==2){
 		$comment = "会员{$touZi['user']}消费{$touZi['czje']}代理奖励{$money}";
 	}elseif ($level==3){
 		$comment = "会员{$touZi['user']}消费{$touZi['czje']}运营中心奖励{$money}";
 	}elseif ($level==4){
 		$comment = "会员{$touZi['user']}消费{$touZi['czje']}股东奖励{$money}";
 	}
 	$time =time();
 	$sql="insert into zt_xxtz_jl(user,zuo_dan_user,jin_e,shou_yi_user,shou_yi_user_level,shou_yi_jin_e,rq,comment)
 	values('{$touZi['user']}','{$touZi['quan_fan_user']}',{$touZi['czje']},'{$userAcc}',$level,$money,{$time},'{$comment}')";
 	var_dump($sql);
 	mysql_query($sql);
 }
 
 
 
 
 function  loopUser($touZi,$user){
 	if(empty($user['parent_id']) ){//没有推荐人, 那么就不需要再往上找
 		return ;
 	}
 	$level=2;//从代理开始寻找
 	$sql="select user, parent_id ,level from xbmall_users where user_name='{$user['parent_id']}'";
 	$user= getRow($sql);//找出链中需要奖励的人
 	if (levelJiangLi($touZi,$user,$level))$level++;
 	
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
 		
 		if (levelJiangLi($touZi,$user,$level))$level++;
 		
 	}
 	return null;
 }
 
 
 
 /**
  * 对于代理， 运营中心, 股东的奖励
  * @param unknown $touZi
  * @param unknown $user
  * @param unknown $level
  * @return boolean
  */
 function levelJiangLi($touZi,$user,$level){
 	$xiaoFeiJinE = $touZi['czje']/0.12;
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
 		levelJiangLiLog($touZi, $user['user_name'], $level, $money);
 		return true; //人是对的， 奖励已经发放
 	}
 	return false;
 }
 


 

 ?>
   