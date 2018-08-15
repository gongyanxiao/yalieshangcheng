<?php

// 补单
include "../config.php";
include "../myphplib/init.php";
include "../myphplib/db.php";
include "../myphplib/message.php";
include "config/check.php";
date_default_timezone_set("Asia/Shanghai");
set_time_limit(3000);//50分钟执行时间
session_start();
if ($_SESSION['addXXFT'] != null) {
    $_SESSION['addXXFT'] = null;
} else { // cookie是空的
           // alertAndRelocation("您已经提交过了, 如再次提交, 请刷新页面", "xxcz_list.html");
}


$shengJiArr=  array( 
    array( 'user'=>'18553939990' , 'type'=>0, 'fenShu'=>2,'tjrq'=>'2018-03-14 00:00:00') //史佩翠
    
);

$sql = "select * from  zt_setting ";
$row = mysql_query($sql);
while ($rs = mysql_fetch_assoc($row)) {
    $configs[] = $rs;
}

 
foreach ($shengJiArr as $key =>$value){
    chong_zhi();
    kaiDianZuoDan($value);
} 

mysql_close($db);
success("补单提交成功");
                              
$nowDateStr ="";
$nowDayStr ="";

/**
 * 补发投资收益
 * @param unknown $user
 * @param unknown $xxtz_id  投资单id
 */
function   bu_fa_fang($user,$xxtz_id,$startTime,$endTime){
   
    $second_step_zeng_song =22; //第二阶段分22元
    $first_step_zeng_song =96;  //第一阶段分96元
    $first_period =45;
    $second_period =54;
    $endTime =  strtotime($endTime);
    $lastDate = strtotime($startTime);
    
    $lastDay=date('d',$lastDate);//上一次发放在哪天
    $lastMonth = date('m',$lastDate);//上一次发放的月份
    $lastYear = date('Y',$lastDate);//上一次发放的年份
    
    $endYear =  date('Y',$endTime);
    $endMonth =  date('m',$endTime);
    $endDay =  date('d',$endTime);
    
    $nowHour =  date('H',$endTime);//发放时的小时
    $nowMinute =  date('i',$endTime);//发放时的分钟
    $nowSecond =  date('s',$endTime);//发放时的
    //   var_dump("要发放的日期{$endYear}-{$endMonth}-{$endDay} {$nowHour}:{$nowMinute}:{$nowSecond} ");
    
    $time = strtotime("{$lastYear}-{$lastMonth}-{$lastDay} {$nowHour}:{$nowMinute}:{$nowSecond}");
    
    //   var_dump($endTime);
    //   die  ("{$lastYear}-{$lastMonth}-{$lastDay} {$nowHour}:{$nowMinute}:{$nowSecond}");
    
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
        //       die($timeStr);
        //昨天下午5点前做单的, 没有出局的投资都查出来
        $sql = "SELECT * from zt_xxtz  where  cj_state = 0 and user='{$user}' and tjrq<={$zuoTian5dian} and id=${$xxtz_id}";
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
}




/**
 * 添加一天
 * @param unknown $lastYear
 * @param unknown $lastMonth
 * @param unknown $lastDay
 */
function   addDay(&$lastYear,&$lastMonth,&$lastDay){
    
    $days = date("t",strtotime($lastYear."-".$lastMonth));//查询月份有多少天
    
    //       $days = cal_days_in_month(CAL_GREGORIAN, $lastMonth, $lastYear);//查询月份有多少天
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


/**
 * 充值
 */
function  chong_zhi($data){
    $czje = $data['fenShu']*3600;
    $sql="insert into zt_xxtz_cz (czyhkh,skyhkh, user,czje,tjrq,user_bz,ping_zheng)values('补单充值','{$skyhkh}','{$operateUser}',{$czje},'{$data['tjrq']}','补单充值','')";
    mysql_query($sql);
}



function    kaiDianZuoDan($data){
    
    global $nowDateStr,$configs,$nowDayStr;
	
    $czje = $data['fenShu']*3600;
    $type = $data['type'];
    $tjrq = $data['tjrq'];
   
    $operateUser = $data['user']; // 给谁做单
    
    
    $tjrq = strtotime($tjrq); // 提交时间
    $chuJuRiQi = $tjrq-(45*24*3600); //当前入单日期减去45天, 投资时间大于$chuJuRiQi的投资是没有出局的
    $userInfo = getUser($operateUser);
    
    $zhangHuJinE =  $userInfo['dian_pu_zi_jin']; // 只使用店铺资金
    
    if ($czje > $zhangHuJinE) {
        failed($data['tjrq']."   {$data['user']}您的账户余额不足, 现有余额{$zhangHuJinE}");
    }
    
    $nowDateStr = date("Y-m-d H:i:s", $tjrq);
    $nowDayStr = date("Y-m-d", $tjrq);
    
    
   
    if ($type == 2) { // 开通店铺
        kaiDian($data);
    }
    $type = 0;
    
    $fenShu = $czje / 3600; // 投资的份数
    
    $min = todayInvestC($operateUser);
    
    if ($fenShu > $min) {
        json_encode(array(
            'result' => 1,
            'message' => 2
        ));
        failed("您今天最多投资{$min}笔");
    }
    
    $sql = "select yi_tou_zi_ci_shu,ke_tou_zi_ci_shu from xbmall_users where user_name='{$operateUser}'";
    $touZi = getRow($sql);
  
    $haiKeTouZiCiShu = $touZi['ke_tou_zi_ci_shu'] - $touZi['yi_tou_zi_ci_shu']; // 还可投资的次数
    
    // 查询还没有出局的投资数
    $sql = " select count(0) from  zt_xxtz where user='{$operateUser}'  and cj_state=0 and ft_state!=1 and tjrq > {$chuJuRiQi} ";
    $weiChuJuChuShiTouZiShu = getOne($sql);
    
    // 处在第一阶段的投资
    $sql = "select count(0) from zt_xxtz where user='{$operateUser}' and cj_state=0  and ft_state!=1    ";
    $step1TouZiCount = getOne($sql); //
    $step1TouZiCount = $step1TouZiCount >= 3 ? 3 : $step1TouZiCount;
    
    if ($touZi['yi_tou_zi_ci_shu'] + $fenShu > $touZi['ke_tou_zi_ci_shu'] + 3) { //
        $shengYuCiShu = $touZi['ke_tou_zi_ci_shu'] - $touZi['yi_tou_zi_ci_shu']; // 可能是负数
        $shengYuCiShu = $shengYuCiShu + $step1TouZiCount; // 剩余投资数等于剩余的投资数加上最多3笔的复投次数
        $shengYuCiShu = $shengYuCiShu * 3600; // 剩余投资金额
        failed("{$operateUser}您已投资{$touZi['yi_tou_zi_ci_shu']}次,还可投资{$shengYuCiShu}元,可以继续推荐店铺, 获取更多投资资格");
    } else if ($touZi['yi_tou_zi_ci_shu'] >= $touZi['ke_tou_zi_ci_shu']) {
        // 已经超出投资数限制, 判断还能不能复投3笔
        if ($step1TouZiCount >= 3) {
        	failed("{$operateUser}您的投资次数已达到上限{$touZi['ke_tou_zi_ci_shu']},并且已有3笔投资运行中,可以继续推荐店铺, 获取更多投资资格");
        }
    }
    
    // 如果投资分数大于未出局的初始投资数, 那么可以复投的数量是未出局的初始投资数,否则为投资的份数
    $fuTouShu = $fenShu > $weiChuJuChuShiTouZiShu ? $weiChuJuChuShiTouZiShu : $fenShu;
    
    // 处在第一阶段的投资,并且没有出局
    $sql = "select * from zt_xxtz where user='{$operateUser}' and cj_state=0  and ft_state!=1   and tjrq >{$chuJuRiQi}  order  by id asc  ";
    $xxtz = mysql_query($sql);
    
    $next_ft_time = $tjrq + 3600 * 24 * 45;
    
    for ($i = 0; $i < $fuTouShu; $i ++) {
        $data = mysql_fetch_array($xxtz);
        // 插入新的初始投资, 记录是为哪笔初始投资复投的
        $sql = "insert into zt_xxtz(fk_user,user, tjrq,step_start_rq,type,czje, parent_id,step,cj_state,next_ft_time)values('{$operateUser}','{$operateUser}','{$tjrq}','{$tjrq}',{$type},3600,{$data['id']},1,0,$next_ft_time)";
        mysql_query($sql);
        $newId = mysql_insert_id();
        log3600($operateUser, "复投{$data['id']},编号:{$newId}");
        $sql = "update  zt_xxtz set child_id={$newId} ,ft_state=1  where id={$data['id']}";
        mysql_query($sql);
        $data =getRow( "select * from   zt_xxtz where id={$newId}");
        jiangLi($data, getUser($data['user']));
    }
    
    if ($fenShu > $weiChuJuChuShiTouZiShu) { // 已经没有可复投的初始投资, 开始新的初始投资
        for ($i = 0; $i < $fenShu - $weiChuJuChuShiTouZiShu; $i ++) {
            $sql = "insert into zt_xxtz(fk_user,user ,tjrq,step_start_rq,type,czje,step,cj_state,next_ft_time)values('{$operateUser}','{$operateUser}','{$tjrq}','{$tjrq}',{$type},3600,1,0,$next_ft_time)";
            mysql_query($sql);
            $newId = mysql_insert_id();
            $data = getRow("select * from  zt_xxtz where id={$newId}");
            log3600($operateUser, "投资一份,编号:{$newId}");
            jiangLi($data, getUser($data['user']));
        }
    }
    
    
}







function getUser($userAcc)
{
    $sql = "select * from xbmall_users where user_name='{$userAcc}'";
    return getRow($sql);
}

/**
 * 发放代理和运营中心的奖励
 * 
 * @param unknown $xxtz
 * @param unknown $user
 *            投资人
 */
function jiangLi($xxtz, $user)
{
    if (empty($user['recommend_user'])) { // 没有推荐人, 那么就不需要再往上找
        return;
    }
    // 修改投资用户的已投资次数
    $sql = " update xbmall_users set yi_tou_zi_ci_shu = yi_tou_zi_ci_shu+1 where user_name='{$xxtz['user']}' ";
    mysql_query($sql);
    
    $zhongXinNum = 0; // 向上查找的过程中运营中心的数量
    $dongShiNum = 0; // 向上查找的过程中懂事的数量
    
//     if($xxtz['user']!='18153252679'){//测试毛一鸣的投资
//     	return ;
//     }
    $tuiJianRen = yiCengJiangLi($xxtz, $user, $zhongXinNum, $dongShiNum);
    $tuiJianRen = erCengJiangLi($xxtz, $tuiJianRen, $zhongXinNum, $dongShiNum);
    while ($tuiJianRen && $dongShiNum < 3) { // 直到懂事层数达到3层,或没有推荐人为止
        if (empty($tuiJianRen['recommend_user']))
            break; // 或没有推荐人了
        $tuiJianRen = duoCengJiangLi($xxtz, $tuiJianRen, $zhongXinNum, $dongShiNum);
    }
}

/**
 * 第一层需要的操作
 * 
 * @param unknown $user
 * @return void|void|number
 */
function yiCengJiangLi($xxtz, $user, &$zhongXinNum, &$dongShiNum)
{
    global $nowDateStr, $nowDayStr;
    
    
    $sql = "select user_name,recommend_user, parent_id ,level, dian_pu_shu1, dian_pu_shu2,ye_ji from xbmall_users where user_name='{$user['recommend_user']}'";
    
    $tuiJianRen = getRow($sql); // 找出链中需要奖励的人
    
    if ($tuiJianRen == null)
        return;
    
    if ($xxtz['type'] == 2) { // 店铺审核
                          // 修改推荐人的可投资次数
        $sql = "update  xbmall_users set ke_tou_zi_ci_shu=ke_tou_zi_ci_shu+50  where user_name='{$xxtz['user']}'";
        mysql_query($sql);
        $dianPuXM = getXM($xxtz['fk_user']);
        $sql = "insert into zt_xxtzzs (user,zsrq,comment) values('{$xxtz['user']}','{$nowDateStr}','您推荐的({$dianPuXM})开通店铺,您分得一份店铺投资,ID:{$xxtz['id']},并增加了50个可用投资数')";
        mysql_query($sql);
    }
    
    $sql = "select count(0) from zt_xxtz where user='{$user['user_name']}' "; // 有3份投资
    $touZiShu = getOne($sql);
    if ($touZiShu == 3) {
        // 增加一层店铺数
        $sql = "update xbmall_users set dian_pu_shu1=dian_pu_shu1+1   where user_name='{$tuiJianRen['user_name']}'";
        mysql_query($sql);
        $tuiJianRen['dian_pu_shu1'] = $tuiJianRen['dian_pu_shu1'] + 1;
        $zg_userName = getXM($user['user_name']);
        $sql = "insert into zt_xxtz_sj(user,zg_user,rq,zg_level,user_level) values('{$tuiJianRen['user_name']}','{$user['user_name']}','{$nowDayStr}',1,{$tuiJianRen['level']})";
        mysql_query($sql); // 插入今天满足3单的用户信息
        
        $sql = "insert into zt_xxtzzs (user,zsrq,comment) values('{$tuiJianRen['user_name']}','{$nowDateStr}','({$zg_userName})成为你的一层有效商家,有效商家个数{$tuiJianRen['dian_pu_shu1']}')";
        mysql_query($sql);
    }
    $sql = "update xbmall_users set ye_ji=ye_ji+3600    where user_name='{$tuiJianRen['user_name']}'";
    mysql_query($sql);
    
    $level = $tuiJianRen['level'];
    $sql = " select * from zt_xxtz_sj where zg_user='{$xxtz['user']}' and is_sj=1 and  rq='{$nowDayStr}'"; // 看看投资人, 是不是今天成为的有效商家
    $xxtz_sj = getRow($sql);
    if ($xxtz_sj) { // 投资人今天成为的有效商家,那推荐人享受的是投资人成为有效商家时, 推荐人身份所在的级别
        $level = $xxtz_sj['user_level'];
    }
    
    $shenFen = getShenFen($level);
    if ($level == 4) { // 找到了运营中心
        $guanLiFei = getYunYingGuanLiFei($zhongXinNum); // 运营中心管理费
        $guanLiFei = $guanLiFei + 40; // 运营中心得到高级代理身份的40元
        $zhongXinNum ++;
//         echo  "运营中心管理费:{$guanLiFei}\r\n" ;
    } else if ($level == 5) { // 找到了懂事
        $guanLiFei = getDongShiGuanLiFei($dongShiNum); // 懂事管理费
        $dongShiNum ++;
    } else { // 初中高级代理的管理费
        $guanLiFei = getDaiLiGuanLiFei($level);
        
    }
    
    
    // 增加积分变动记录,推荐收益
    $userXM = getXM($user['user_name']);
  
    if($guanLiFei>0){
    	if ($xxtz_sj) { // 投资人是今天成为的有效商家
    		faFangUserJiFen($tuiJianRen['user_name'], $guanLiFei, "您推荐的{$userXM}投资一份,由于其今天成为的有效商家,您作为{$shenFen}获得管理费{$guanLiFei}");
    	} else {
    		faFangUserJiFen($tuiJianRen['user_name'], $guanLiFei, "您推荐的{$userXM}投资一份,您作为{$shenFen}获得管理费{$guanLiFei}");
    	}
    }
    
    // mysql_query($sql);
    
    $tuiJianRen = shengJi($tuiJianRen);
    return $tuiJianRen;
}

/**
 * 第二层需要的操作
 * 
 * @param unknown $user
 * @return void|void|number
 */
function erCengJiangLi($xxtz, $yiCengTuiJianRen, &$zhongXinNum, &$dongShiNum)
{
    global $nowDateStr;
    $sql = "select user_name, parent_id ,recommend_user,level, dian_pu_shu1, dian_pu_shu2,ye_ji from xbmall_users where user_name='{$yiCengTuiJianRen['recommend_user']}'";
   
    $tuiJianRen = getRow($sql); // 找出链中需要奖励的人
    if ($tuiJianRen == null)
        return;
        
    if ($xxtz['type'] == 2) {
        // 增加间接推荐人的二层店铺数
        if ($xxtz['fk_user'] != $xxtz['user']) { // 是推荐人给开通店铺
            $sql = "update xbmall_users set dian_pu_shu2=dian_pu_shu2+1  where user_name='{$yiCengTuiJianRen['user']}'";
            mysql_query($sql);
            $yiCengTuiJianRen['dian_pu_shu2'] = $yiCengTuiJianRen['dian_pu_shu2'] + 1;
            $zg_userName = getXM($xxtz['fk_user']);
            $sql = "insert into zt_xxtzzs (user,zsrq,comment) values('{$yiCengTuiJianRen['user']}','{$nowDateStr}','({$zg_userName})成为你的二层有效商家,二层有效商家个数{$yiCengTuiJianRen['dian_pu_shu2']}')";
            mysql_query($sql);
        } else {//自己开通店铺
            $sql = "update xbmall_users set dian_pu_shu2=dian_pu_shu2+1  where user_name='{$tuiJianRen['user_name']}'";
            mysql_query($sql);
            $tuiJianRen['dian_pu_shu2'] = $tuiJianRen['dian_pu_shu2'] + 1;
            $zg_userName = getXM($xxtz['user']);
            $sql = "insert into zt_xxtzzs (user,zsrq,comment) values('{$tuiJianRen['user_name']}','{$nowDateStr}','({$zg_userName})成为你的二层有效商家,二层有效商家个数{$tuiJianRen['dian_pu_shu2']}')";
            mysql_query($sql);
        }
    }
    
    $sql = "update xbmall_users set ye_ji=ye_ji+3600    where user_name='{$tuiJianRen['user_name']}'";
    mysql_query($sql);
    
    $level = $tuiJianRen['level'];
    if ($level == 4) { // 找到了运营中心
        $guanLiFei = getYunYingGuanLiFei($zhongXinNum); // 运营中心管理费
        $guanLiFei = $guanLiFei + 40; // 运营中心得到高级代理身份的40元
        $zhongXinNum ++;
    } else if ($level == 5) { // 找到了懂事
        $guanLiFei = getDongShiGuanLiFei($dongShiNum); // 懂事管理费
        $dongShiNum ++;
    } else { // 初中高级代理的管理费
        $guanLiFei = getDaiLiGuanLiFei($level);
    }
    
 
    $shenFen = getShenFen($level);
    // 增加积分变动记录
    $userXM = getXM($xxtz['user']);
//     if(empty($userXM)){
//         var_dump($xxtz);
//     }

    if($guanLiFei>0){
    	faFangUserJiFen($tuiJianRen['user_name'], $guanLiFei, "第2层{$userXM}投资一份,您作为{$shenFen}获得管理费{$guanLiFei}");
    }
   
  
    $tuiJianRen = shengJi($tuiJianRen); // 奖励完了再升级
    return $tuiJianRen;
}

/**
 * 发放用户积分
 */
function faFangUserJiFen($user, $benCiJiFen, $message)
{
	
// echo "本次积分:{$benCiJiFen}\r\n";
    global $nowDateStr;
    $xxtzzfwf = $benCiJiFen * 0.01; // 总服务费
    $xxtzzcbjz = $benCiJiFen * 0.02; // 总储备聚珠
    $xxtzzsyjz = $benCiJiFen * 0.02; // 总收益聚珠
    $xxtzzxfjz = $benCiJiFen * 0.05; // 总消费聚珠
    $tui_jian_shou_yi = $benCiJiFen * 0.9; // 总推荐收益
    
    /**
     * 修改用户的账户,包括以前的消费聚珠
     */
    $sql = "update xbmall_users  set xxtzzfwf=xxtzzfwf+{$xxtzzfwf}, xxtzzcbjz=xxtzzcbjz+{$xxtzzcbjz} ,xxtzzsyjz=xxtzzsyjz+{$xxtzzsyjz} ,
 	 pay_points=pay_points+{$xxtzzxfjz} , xfjf=xfjf+{$xxtzzxfjz} ,  tui_jian_shou_yi=tui_jian_shou_yi+{$tui_jian_shou_yi}  where user_name='{$user}'";
    mysql_query($sql);
    
    
//     echo "修改用户账户:".$sql."\r\n";
    
    $sql = "INSERT INTO  zt_b_exchange_record(`id`,`date`,`jf`,`sf`,`user`,`url`)VALUES(NULL,'$nowDateStr','$xxtzzxfjz','忽略','{$user}','系统发放推荐奖聚珠');";
    // success($sql."<br>");
    mysql_query($sql);
    
    /**
     * 创建本次的分配记录
     */
    $sql = "insert into zt_xxtzzs (type,user, zsrq,jf ,xxtzzfwf,xxtzzcbjz,xxtzzsyjz,xxtzzxfjz,tui_jian_shou_yi, comment)
 	values(1,'{$user}','{$nowDateStr}',{$benCiJiFen}, {$xxtzzfwf},{$xxtzzcbjz},{$xxtzzsyjz},{$xxtzzxfjz},{$tui_jian_shou_yi},'{$message}')";
    mysql_query($sql);
}

/**
 * 3层及3层以上的奖励
 * 
 * @param unknown $xxtz
 * @param unknown $yiCengTuiJianRen
 * @param unknown $zhongXinNum
 * @param unknown $dongShiNum
 * @return void|void|unknown|number
 */
function duoCengJiangLi($xxtz, $shangCengTuiJianRen, &$zhongXinNum, &$dongShiNum)
{
    global $nowDateStr;
    $sql = "select user_name,recommend_user, parent_id ,level, dian_pu_shu1, dian_pu_shu2,ye_ji from xbmall_users where user_name='{$shangCengTuiJianRen['recommend_user']}'";
//     echo "多层{$sql}\r\n";
    $tuiJianRen = getRow($sql); // 找出链中需要奖励的人
    if ($tuiJianRen == null)
        return null;
    
    if ($tuiJianRen['level'] == 4) { // 找到了运营中心
        
        $guanLiFei = getYunYingGuanLiFei($zhongXinNum); // 运营中心管理费
        $zhongXinNum ++;
    } else if ($tuiJianRen['level'] == 5) { // 找到了懂事
        $guanLiFei = getDongShiGuanLiFei($dongShiNum); // 懂事管理费
        $dongShiNum ++;
    } else { // 代理不享受2层以外的奖励
        return $tuiJianRen;
    }
    
    
//     faFangUserJiFen($user, $guanLiFei, $message);
//     $sql = "update  xbmall_users  set tui_jian_shou_yi=tui_jian_shou_yi+{$guanLiFei}  where user_name='{$tuiJianRen['user_name']}'"; // 发放奖励
//     mysql_query($sql); // 发放第一层的奖励
    
    $shenFen = getShenFen($tuiJianRen['level']);
    $userXM = getXM($xxtz['user']);
    if($guanLiFei>0){
    	faFangUserJiFen($tuiJianRen['user_name'], $guanLiFei, "{$userXM}投资一份,您作为{$shenFen}获得管理费{$guanLiFei}");
    }
    // 增加积分变动记录
    // $sql ="insert into zt_xxtzzs (type,user,xxtzztxjf,zsrq,comment) values(1,'{$tuiJianRen['user_name']}',{$guanLiFei},'{$nowDateStr}','{$userXM}投资一份,您作为{$shenFen}获得管理费{$guanLiFei}')";
    // mysql_query($sql);
    return $tuiJianRen;
}

/**
 * 获取姓名
 * 
 * @param unknown $user
 *            用户账户
 * @return NULL|unknown 用户姓名
 */
function getXM($user)
{
    $sql = "select real_name from xbmall_users where user_name='{$user}'";
    return getOne($sql);
}

/**
 * 获取用户的身份
 * 
 * @param unknown $level
 * @return string
 */
function getShenFen($level)
{
    if ($level == 1) {
        return "初级代理";
    } else if ($level == 2) {
        return "中级代理";
    } else if ($level == 3) {
        return "高级代理";
    } else if ($level == 4) {
        return "运营中心";
    } else if ($level == 5) {
        return "懂事";
    }
    return "会员";
}

/**
 * 获取某层次用户升级需要某层多少店铺数, 懂事不参与升级
 * 
 * @param unknown $name
 */
function getLevelDianPuShengJi($userLevel, $level)
{
    if ($level == 1) { // 第一层
        if ($userLevel == 0) {
            return getWebConfig("sheng_chu_dai_dian_pu");
        } else if ($userLevel == 1) {
            return getWebConfig("sheng_zhong_dai_dian_pu");
        } else if ($userLevel == 2) {
            return getWebConfig("sheng_gao_dai_dian_pu");
        } else if ($userLevel == 3) {
            return getWebConfig("sheng_yun_ying_dian_pu");
        }
    } elseif ($level == 2) { // 需要的是第二层的数据
        if ($userLevel == 0) {
            return getWebConfig("sheng_chu_dai_dian_pu2");
        } else if ($userLevel == 1) {
            return getWebConfig("sheng_zhong_dai_dian_pu2");
        } else if ($userLevel == 2) {
            return getWebConfig("sheng_gao_dai_dian_pu2");
        } else if ($userLevel == 3) {
            return getWebConfig("sheng_yun_ying_dian_pu2");
        }
    } else { // 需要的是第二层业绩,默认不需要业绩
        if ($userLevel == 0) {
            return getWebConfig("sheng_chu_dai_ye_ji");
        } else if ($userLevel == 1) {
            return getWebConfig("sheng_zhong_dai_ye_ji");
        } else if ($userLevel == 2) {
            return getWebConfig("sheng_gao_dai_ye_ji");
        } else if ($userLevel == 3) {
            return getWebConfig("sheng_yun_ying_ye_ji");
        }
    }
    return - 1;
}

/**
 * 获取管理费
 * 
 * @param unknown $level
 *            推荐层数
 * @param unknown $yunYingNum
 *            //运营中心个数
 */
function getYunYingGuanLiFei($yunYingNum)
{
    if ($yunYingNum == 0) {
        return getWebConfig("yun_ying_ling_ceng_quan_wang");
    } else if ($yunYingNum == 1) {
        return getWebConfig("yun_ying_yi_ceng_quan_wang");
    } else if ($yunYingNum == 2) {
        return getWebConfig("yun_ying_er_ceng_quan_wang"); // 运营中心以下两层运营中心时得全网管理费
    }
    return 0;
}

/**
 * 获取懂事的管理费
 * 
 * @param unknown $level
 *            推荐层数
 * @param unknown $dongShiNum
 *            懂事的层数
 * @return number
 */
function getDongShiGuanLiFei($dongShiNum)
{
    if ($yunYingNum == 0) {
        return getWebConfig("dong_shi_ling_ceng_quan_wang");
    } else if ($yunYingNum == 1) {
        return getWebConfig("dong_shi_yi_ceng_quan_wang");
    } else if ($yunYingNum == 2) {
        return getWebConfig("dong_shi_er_ceng_quan_wang");
    }
    return 0;
}

function getDaiLiGuanLiFei($level)
{
    $guanLiFei = 0;
    if ($level == 3) {
        $guanLiFei = getWebConfig("gao_dai_guan_li_fei");
    } else if ($level == 2) {
        $guanLiFei = getWebConfig("zhong_dai_guan_li_fei");
    } else if ($level == 1) { // 初级代理得10元
        $guanLiFei = getWebConfig("chu_dai_guan_li_fei");
    }
    return $guanLiFei;
}

/**
 * 记录资金变化,
 * 
 * @param unknown $operateUser
 *            谁在花钱
 * @param unknown $message
 * @param unknown $money
 */
function logMoney($operateUser, $message, $money)
{
    global $nowDateStr;
    
    $sql = "select * from xbmall_users where user_name='{$operateUser}'";
    $user = getRow($sql);
    $zhangHuJinE =   $user['dian_pu_zi_jin']; // 可用的总金额
    
    if (floor($money / 100) * 100 > floor($zhangHuJinE / 100) * 100) {
    	failed("{$operateUser}的可用金额为:{$zhangHuJinE}");
    }
    
    $jianDianPuJinE = $money >= $user['dian_pu_zi_jin'] ? $user['dian_pu_zi_jin'] : $money; // 要减的赠送收益
    $stepMoney = $money - $jianDianPuJinE; // 还需要减多少
    if ($stepMoney > 0) { // 店铺资金不够
        $jianZengSongJinE = $stepMoney >= $user['xxtzztxjf'] ? $user['xxtzztxjf'] : $stepMoney; // 要减的赠送收益
        $stepMoney = $stepMoney - $jianZengSongJinE;
        if ($stepMoney > 0) { // 投资收益不够
            $jianTuiJianShouYi = $stepMoney >= $user['tui_jian_shou_yi'] ? $user['tui_jian_shou_yi'] : $stepMoney; // 要减的推荐收益
        } else {
            $jianTuiJianShouYi = 0;
        }
    } else {
        $jianZengSongJinE = 0;
        $jianTuiJianShouYi = 0;
    }
    
    $sql = "update xbmall_users set dian_pu_zi_jin=dian_pu_zi_jin-{$jianDianPuJinE} , xxtzztxjf=xxtzztxjf-{$jianZengSongJinE} ,tui_jian_shou_yi=tui_jian_shou_yi-{$jianTuiJianShouYi} where  user_name='{$user['user_name']}'";
//     echo  $sql."\r\n";
    mysql_query($sql);
    // 插入积分变动记录， 返回用户的积分
    $sql = "insert into zt_xxtzzs (user,dian_pu_zi_jin,xxtzztxjf,tui_jian_shou_yi,zsrq,comment) values('{$user['user_name']}',-{$jianDianPuJinE},-{$jianZengSongJinE},-{$jianTuiJianShouYi},'{$nowDateStr}','{$message},减去店铺资金{$jianDianPuJinE},减去投资收益{$jianZengSongJinE},减去推荐收益{$jianTuiJianShouYi}')";
    mysql_query($sql);
//     echo  $sql."\r\n";
}

/**
 *
 * @param unknown $user
 *            谁在花钱
 * @param unknown $message
 */
function log3600($user, $message)
{
    logMoney($user, $message, 3600);
}

function isKaiDian($userAcc)
{
    $sql = "select  is_dian_pu  from  xbmall_users where user_name='{$userAcc}' and is_dian_pu=1";
    $is_dian_pu = getOne($sql);
    return $is_dian_pu;
}

function kaiDian($data)
{
	global $nowDateStr;
    $tjrq = strtotime( $data['tjrq']); 
    $next_ft_time = $tjrq + 3600 * 24 * 45;
    $operateUser = $data['user'];
   
 
    $nowDateStr = date("Y-m-d H:i:s", $tjrq);
    
    
    $operateUserInfo = getUser($operateUser);
    
    $dianPuUser = $data['dianPuUser']; // 要开通店铺的人
    
    if (isset($dianPuUser)) { // 给别人开店
        $userInfo = getUser($operateUser);
    } else { // 自己开店
        $userInfo = getUser($operateUserInfo['recommend_user']); // 收益人是推荐人的推荐人
        $dianPuUser = $operateUser;
    }
    
    if (isKaiDian($dianPuUser)!=null) {
    	failed($dianPuUser."不要重复开店");
    }
    
    $tuiJianRen = $userInfo['user_name']; // 店铺的推荐人
    
    $dianPuXM = getXM($dianPuUser);
    if (isset($data['dianPuUser'])) { // 推荐人给开店, 花推荐人的钱
        log3600($operateUser, "为{$dianPuXM}开通店铺");
    } else { // 自己开店,花自己的钱
        log3600($dianPuUser, "为{$dianPuXM}开通店铺");
    }
    
    // 设置店铺开通了
    $sql = "update xbmall_users set is_dian_pu=1  where user_name='{$dianPuUser}' ";
    mysql_query($sql);
    
    $sql = "select * from zt_xxtz where user='{$tuiJianRen}' and cj_state=0  and ft_state!=1    order  by id asc  limit 1 ";
    
    $chuShiTouZi = getRow($sql);
    
    if ($chuShiTouZi) { // 如果有初始投资, 那么就把推荐人的初始投资改为复投
                      // 开通店铺, 付款人是被推荐人, 受益人是推荐人.
        $sql = "insert into zt_xxtz(parent_id,fk_user,user,tjrq,step_start_rq,next_ft_time,type,czje,step,cj_state)values({$chuShiTouZi['id']},'{$dianPuUser}','{$tuiJianRen}' ,{$tjrq},{$tjrq},{$next_ft_time},2,3600,1,0)";
        mysql_query($sql);
        $newId = mysql_insert_id();
        $sql = "update  zt_xxtz set child_id={$newId} ,ft_state=1  where id={$chuShiTouZi['id']}";
        mysql_query($sql);
    } else {
        // 开通店铺, fk_user是店铺主人, 受益人是推荐人.
        $sql = "insert into zt_xxtz(fk_user,user,tjrq,step_start_rq,next_ft_time,type,czje,step,cj_state)values('{$dianPuUser}','{$tuiJianRen}' ,{$tjrq},{$tjrq},{$next_ft_time},2,3600,1,0)";
        mysql_query($sql);
        $newId = mysql_insert_id();
    }
    $sql = "select * from zt_xxtz where id = {$newId}";
  
    $xxtz = getRow($sql);
    
    jiangLi($xxtz, $userInfo);
    
    if (isset($data['dianPuUser'])) {
//         success("开店成功");
    } else {
//         success("开店成功");
    }
}

/**
 * 如果用户达到了升级条件, 就升级用户,如果没有就不升级, 最后都返回处理后的用户信息
 */
function shengJi($user)
{
    if ($user == null)
        return;
    
    global $nowDateStr;
    $yiCengNum = getLevelDianPuShengJi($user['level'], 1);
    $erCengNum = getLevelDianPuShengJi($user['level'], 2);
    $yeJi = getLevelDianPuShengJi($user['level'], 3);
    if ($yiCengNum == - 1 || $erCengNum == - 1) { // 已经不能升级了
        return $user;
    }
    
    if ($user['dian_pu_shu1'] >= $yiCengNum && $user['dian_pu_shu2'] >= $erCengNum && $user['ye_ji'] >= $yeJi) { // 满足了升级条件
        $sql = "update  xbmall_users set level=level+1 where user_name='{$user['user_name']}'";
        mysql_query($sql);
        $user['level'] = $user['level'] + 1;
        $shenFen = getShenFen($user['level']);
        $sql = "insert into zt_xxtzzs (user,xxtzztxjf,zsrq,comment) values('{$user['user_name']}',0,'{$nowDateStr}','恭喜您,升级为{$shenFen}')";
        echo $sql;
        mysql_query($sql);
        global $nowDayStr;
        // 修改今天成为有效商家的用户为起升级作用的用户
        $sql = "update  zt_xxtz_sj set is_sj=1 where  user='{$user['user_name']}' and  rq='{$nowDayStr}' ";
        mysql_query($sql);
    } else {}
    return $user;
}

/**
 * 今天最多可以投多少笔
 * 
 * @param unknown $fenShu
 */
function todayInvestC($operateUser)
{
    return 100; // 后台操作不限制一天的投资次数
    /**
     * 今日 , 本周, 本月
     */
    $now = time(); // 提交时间
    $day = $now - 3600 * 24;
    $week = $now - 3600 * 24 * 7;
    $month = $now - 3600 * 24 * 30;
    
    // 今天已经投资了多少
    $sql = "select count(0) from  zt_xxtz where    user='{$operateUser}'    and tjrq   between  $day and  $now  ";
    $dayC = getOne($sql);
    
    // 7天内的投资份数
    $sql = "select count(0) from  zt_xxtz where    user='{$operateUser}'   and tjrq   between  $week and  $now  ";
    $weekC = getOne($sql);
    /**
     * 30天内的投资份数
     */
    $sql = "select count(0) from  zt_xxtz where    user='{$operateUser}'   and tjrq   between  $month and  $now  ";
    $monthC = getOne($sql);
    
    $monthC = 50 - $monthC;
    $weekC = 20 - $weekC;
    $dayC = 10 - $dayC;
    $min = $monthC;
    if ($min > $weekC) {
        $min = $weekC;
    }
    if ($min > $dayC) {
        $min = $dayC;
    }
    return $min;
}

function getWebConfig($name)
{
    global $configs;
    for ($i = 0; $i < count($configs); $i ++) {
        if ($configs[$i]['name'] == $name) {
            return $configs[$i]['value'];
        }
    }
}


 

function failed($message)
{
 echo $message."\r\n";
	//     var_dump($message."<br>");
	//     echo json_encode(array(
	//         'result' => 0,
	//         'message' => "{$message}"
	//     ));
	die("出错退出");
}



function success($message)
{
	echo $message."\r\n";
//     var_dump($message."<br>");
//     echo json_encode(array(
//         'result' => 0,
//         'message' => "{$message}"
//     ));
   
}

 
// header("Location: xxcz_list.html");

?>
  			  