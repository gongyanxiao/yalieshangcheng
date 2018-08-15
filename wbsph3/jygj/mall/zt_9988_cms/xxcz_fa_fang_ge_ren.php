<?php

/**
 * 积分的发放
 */
include "../myphplib/init.php";
include "../myphplib/message.php";
include "../myphplib/db.php";
include "config/check.php";
date_default_timezone_set("Asia/Shanghai");
set_time_limit(3000);//50分钟执行时间


// $lastYear=2017;
// $lastMonth=11;
// $lastDay =30;
// addDay($lastYear, $lastMonth, $lastDay);
// var_dump($lastYear.":".$lastMonth.":".$lastDay."<br>");
// $days = cal_days_in_month(CAL_GREGORIAN, $lastMonth, $lastYear);//查询月份有多少天
// var_dump($days);
// die;

//查询出配置信息

$second_step_zeng_song =22; //第二阶段分22元
$first_step_zeng_song =96;  //第一阶段分96元

$first_period =45;
$second_period =54;


$user=  $_REQUEST['user'] ;

if (!isset($_REQUEST['ffrq'])){
    alertAndRelocationHistory("请设置日期");
}

$endTime =  strtotime($_REQUEST['ffrq']);

$zuoTian = $endTime -  date('H')*3600;

$sql="select * from zt_xxtz_ffjl where rq >={$zuoTian}";
// if(getRow($sql)){
//     $endTime =  date("Y-m-d H:i:s", $endTime);
//     alertAndRelocationHistory("{$endTime}之前的已经发放过了");
// }

$sql="select max(rq) from zt_xxtz_ffjl ";//最近发放的日期
$lastDate = getOne($sql);
if(!isset($lastDate)){//如果还没有发放记录
    $lastDate =  getOne("select min(tjrq) from zt_xxtz where user='{$user}'");
}
if(!isset($lastDate)){
    alertAndRelocationHistory("没有可以发放的投资");
}

$lastDay=date('d',$lastDate);//上一次发放在哪天
$lastMonth = date('m',$lastDate);//上一次发放的月份
$lastYear = date('Y',$lastDate);//上一次发放的年份

$endYear =  date('Y',$endTime);
$endMonth =  date('m',$endTime);
$endDay =  date('d',$endTime);

$nowHour =  date('H',$endTime);//发放时的小时
$nowMinute =  date('i',$endTime);//发放时的分钟
$nowSecond =  date('s',$endTime);//发放时的
  var_dump("上次发放的日期{$lastYear}-{$lastMonth}-{$lastDay}");
  var_dump("要发放的日期{$endYear}-{$endMonth}-{$endDay} {$nowHour}:{$nowMinute}:{$nowSecond} ");


$time = strtotime("{$lastYear}-{$lastMonth}-{$lastDay} {$nowHour}:{$nowMinute}:{$nowSecond}");



$lastDay = $lastDay+1;
$timeStr ="";
$endMonth = intval($endMonth);
$endDay = intval($endDay);

$iiii=0;

 
while($time<$endTime){
    
    $time = strtotime("{$lastYear}-{$lastMonth}-{$lastDay} {$nowHour}:{$nowMinute}:{$nowSecond}");
    var_dump("{$lastYear}-{$lastMonth}-{$lastDay} {$endYear}-{$endMonth}-{$endDay}  <br>");
    
    $zuoTian5dian = $time  - (date('H',$time))*3600-(date('i',$time))*60-date('s',$time)-1; //昨天24点
    $timeStr = date("Y-m-d H:i:s", $time);
    $zuoTian5dianStr = date("Y-m-d H:i:s", $zuoTian5dian);
    var_dump("zuoTian5dianStr{$zuoTian5dianStr}           timeStr{$timeStr} <br>");
    
    
    $sql = "SELECT * from zt_xxtz  where    user='".$user."' and cj_state = 0 and  tjrq<={$zuoTian5dian}";
    var_dump($sql);
    $result=mysql_query($sql);
    $num = mysql_num_rows($result);
    for($i=0;$i<$num;$i++){
        startTrans();//每条数据一个事务+
        $data2=mysql_fetch_array($result);
        faFangUserJiFen($data2);
        chuJu($data2);//
        commitTrans();//
    }
    addDay($lastYear, $lastMonth, $lastDay);
    
}

/**
 * 添加一天
 * @param unknown $lastYear
 * @param unknown $lastMonth
 * @param unknown $lastDay
 */
function   addDay(&$lastYear,&$lastMonth,&$lastDay){
    $days = date("t",strtotime($lastYear."-".$lastMonth));//查询月份有多少天
    if($lastDay>=$days){//超过了月份的最大天数, 转到下个月
        $lastDay =1;
        $lastMonth++;
        if($lastMonth>12){
            $lastMonth =1;
            $lastYear++;
        }
    }else {
        $lastDay++;
    }
}




alertAndRelocationHistory("指定个人发放成功");


/**
 * 发放用户积分
 */
function faFangUserJiFen($data2){
    
    global  $timeStr;
    $benCiJiFen= getUserRate($data2['step']);//本次投资本次分得的积分总数
    $xxtzzfwf = $benCiJiFen *0.01;//总服务费
    $xxtzzcbjz= $benCiJiFen *0.02;//总储备聚珠
    $xxtzzsyjz= $benCiJiFen *0.02;//总收益聚珠
    $xxtzzxfjz= $benCiJiFen *0.05;//总消费聚珠
    $xxtzztxjf= $benCiJiFen *0.9; //总提现积分
    
    if(substr($timeStr, 0,4)=="1970"){
        var_dump($data2);
        die("时间不对:".$timeStr);
    }
    /**
     * 修改充值记录, 增加各种收益
     */
    $sql="update zt_xxtz set xxtzzfwf=xxtzzfwf+{$xxtzzfwf}, xxtzzcbjz=xxtzzcbjz+{$xxtzzcbjz} ,xxtzzsyjz=xxtzzsyjz+{$xxtzzsyjz} ,
 	 xxtzzxfjz=xxtzzxfjz+{$xxtzzxfjz} , xxtzztxjf=xxtzztxjf+{$xxtzztxjf}  where  id={$data2['id']}" ;
    mysql_query($sql);
    /**
     * 修改用户的账户,包括以前的消费积分
     */
    $sql ="update xbmall_users  set xxtzzfwf=xxtzzfwf+{$xxtzzfwf}, xxtzzcbjz=xxtzzcbjz+{$xxtzzcbjz} ,xxtzzsyjz=xxtzzsyjz+{$xxtzzsyjz} ,
 	pay_points=pay_points+{$xxtzzxfjz} ,   xxtzztxjf=xxtzztxjf+{$xxtzztxjf} where user_name='{$data2['user']}'";
    mysql_query($sql);
    /**
     * 创建本次的分配记录
     */
    $sql ="insert into zt_xxtzzs (user,cz_id,zsrq,jf ,xxtzzfwf,xxtzzcbjz,xxtzzsyjz,xxtzzxfjz,xxtzztxjf, comment,type)
 	values('{$data2['user']}',{$data2['id']},'{$timeStr}',{$benCiJiFen}, {$xxtzzfwf},{$xxtzzcbjz},{$xxtzzsyjz},{$xxtzzxfjz},{$xxtzztxjf},'发放{$benCiJiFen['id']}的每日赠送',3)";
    mysql_query($sql);
    
}

/**
 * 返回每阶段的赠送额度
 * @param unknown $step
 * @return unknown
 */
function  getUserRate($step){
    global    $four_step_zeng_song , 	$third_step_zeng_song , 	$second_step_zeng_song, 	$first_step_zeng_song;
    if($step==1){//如果处于第一阶段
        return  96;
    }else if($step==2){
        return  22;
    }
}


/**
 * 设置出局
 * 查询下一阶段有没有审核通过的复投
 * 如果没有并且超过了复投时间， 那么就出局
 * 设置出局状态为出局
 */
function  chuJu($chong_zhi){
    
    global   $first_period , 	$second_period ,$time;
    
    if( $chong_zhi["step"]==1){//如果在第一阶段
        $end=$chong_zhi["step_start_rq"]+($first_period)*24*3600;//复投的结束时间
        if($chong_zhi["ft_state"]!=1){//还没有进行复投
            if($end<$time){//如果已经超过了时间,设置出局
                $sql="update zt_xxtz  set cj_state=1 where id={$chong_zhi['id']}";
                mysql_query($sql);
            }
            
        }else {//已经复投过了
            $end=$chong_zhi["step_start_rq"]+($first_period)*24*3600;//第二阶段的结束时间
            if($end<$time){//如果已经超过了时间,设置进入第二阶段
                $sql="update zt_xxtz  set step=2 ,step_start_rq={$end} where id={$chong_zhi['id']}";
                mysql_query($sql);
            }
        }
    }else if( $chong_zhi["step"]==2){//如果在第二阶段
        $end=$chong_zhi["step_start_rq"]+($second_period)*24*3600;// 第二阶段的结束时间
        if($end<$time){//第二阶段的时间到了,  设置出局
            $sql="update zt_xxtz  set cj_state=1 where id={$chong_zhi['id']}";
            mysql_query($sql);
        }
    }
    
}



?>
  