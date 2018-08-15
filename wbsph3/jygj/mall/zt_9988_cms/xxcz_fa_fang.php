<?php

/**
 * 发放用户的铺货收益
 */
include "../myphplib/init.php";
include "../myphplib/message.php";
include "../myphplib/db.php";
include "config/check.php";
date_default_timezone_set("Asia/Shanghai");
set_time_limit(300000); // 50分钟执行时间
$user = $_COOKIE['zt_user2'];
 


$sql = "select * from  zt_setting ";
$row = mysql_query($sql);
while ($rs = mysql_fetch_assoc($row)) {
    $configs[] = $rs;
}

// 查询出配置信息

 
$tjrq = time();
$nowDateStr = date("Y-m-d", $tjrq);

 

$endTime = strtotime($_REQUEST['ffrq']);

$zuoTian = $endTime - date('H') * 3600;

$sql = "select FROM_UNIXTIME(max(rq),'%Y-%m-%d') from xbmall_xb_ffjl ";

$fa_fang_ri_qi = getOne($sql);

if ($nowDateStr<=$fa_fang_ri_qi) {//现在的天数日期, 小于上次发放的天数日期
    alertAndRelocationHistory("{$fa_fang_ri_qi}已经发放过了");
}
 
  
 
    // die($timeStr);
    // 昨天晚上12点前做单的, 没有出局的投资都查出来
    $sql = "SELECT * from xbmall_xb_xxtz  where  syxsts > 0 ";
    $result = mysql_query($sql);
    $num = mysql_num_rows($result);
    startTrans(); //  
    for ($i = 0; $i < $num; $i++) {
        $data2 = mysql_fetch_array($result);
        faFangUserJiFen($data2);
    }
    commitTrans(); //
    startTrans(); //  
    $sql = "insert into zt_xxtz_ffjl  (rq) values({$time})";
    mysql_query($sql);
    commitTrans(); //
 

 

die("发放成功");

// alertAndRelocationHistory("发放成功");

/**
 * 发放用户积分
 */
function faFangUserJiFen($data2)
{
    global $timeStr;
  
    $cheng_ben = $data2['dan_shu']*76;
    $xiao_fei_jin = $data2['dan_shu']*4;//消费金
    
    /**
     * 修改用户的账户,消费金和铺货可用余额
     */
    
    
    $sql = "update xbmall_users  set  
 	    pay_points=pay_points+{$xiao_fei_jin}, ye_he_huo_ren=ye_he_huo_ren+{$cheng_ben}  where user_name='{$data2['user']}'";
    mysql_query($sql);
    
    /**
     * 修改剩余发放天数
     */
    $sql = "update zt_xxtz set  syxsts=syxsts-1   where  id={$data2['id']}";
    mysql_query($sql);
  
    
    $sql = "insert into xbmall_xb_xxtzzs (user,cz_id,zsrq,jf , comment,type)
 	values('{$data2['user']}',{$data2['id']},'{$timeStr}',{$benCiJiFen},'铺货成本收回',3)";
    mysql_query($sql);

/**
 * 创建本次的分配记录
 */
}

 

 
 
/**
 * 根据用户下面合格的人员数及自身的有效单(A阶段), 调整会员的级别
 *
 * @param unknown $user
 */
function tiao_zheng_ji_bie($user_name)
{
    global $nowDateStr, $earlierUserArr, $tjrq;
    
    $sql = "select * from xbmall_users where user_name='{$user_name}'";
    $userInfo = getRow($sql);
    // 自己A阶段有效单数
    $sql = "select count(0) from zt_xxtz  where user='{$user_name}' and step=1 and cj_state=0 ";
    $you_xiao_dan = getOne($sql);
    $yi_ceng_dian_pu_shu = getYiCengDianPuShu($user_name);
    
    if (is_kao_he_yi_ceng($userInfo)) {
        echo "即将考核一层<br>";
        yi_ceng_kao_he($userInfo, $yi_ceng_dian_pu_shu, $you_xiao_dan);
    } else { // 不考核一层, 也不考核二层,只管升级
        echo "<br>{$userInfo['user_name']}不考核一层也不考核二层{$yi_ceng_dian_pu_shu}:{$you_xiao_dan}";
    }
    
 
    
    return $userInfo;
}

  
function show($obj){
    var_dump($obj);
    var_dump("<br>");
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

?>
  