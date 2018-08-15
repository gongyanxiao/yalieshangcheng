<?php
include "config.php";
include "myphplib/init.php";
include "myphplib/db.php";
include "myphplib/message.php";
include "config/check.php";
include_once "xxtz_kao_he.php";
date_default_timezone_set("Asia/Shanghai");
$operateUser = $_COOKIE['ECS']['username'];
// alertAndRelocationHistory("系统正在录入数据,用户自己的操作稍后进行");

session_start();
if ($_SESSION['addXXFT'] != null) {
    $_SESSION['addXXFT'] = null;
} else { // cookie是空的
             // alertAndRelocation("您已经提交过了, 如再次提交, 请刷新页面", "xxcz_list.html");
}

$earlierUserArr = array();
$earlierUserArr[] = array(
    "user_name" => "13160060999",
    "riQi" => "2018-04-20",
    'level' => 4
);
$earlierUserArr[] = array(
    "user_name" => "18669671133",
    "riQi" => "2018-04-20",
    'level' => 4
);
$earlierUserArr[] = array(
    "user_name" => "13356999836",
    "riQi" => "2018-04-20",
    'level' => 4
);
$earlierUserArr[] = array(
    "user_name" => "18669987933",
    "riQi" => "2018-04-20",
    'level' => 4
);
$earlierUserArr[] = array(
    "user_name" => "15264492456",
    "riQi" => "2018-04-20",
    'level' => 4
);
$earlierUserArr[] = array(
    "user_name" => "13176990165",
    "riQi" => "2018-04-20",
    'level' => 4
);
$earlierUserArr[] = array(
    "user_name" => "18754913456",
    "riQi" => "2018-04-20",
    'level' => 4
);
$earlierUserArr[] = array(
    "user_name" => "13805397721",
    "riQi" => "2018-04-20",
    'level' => 4
);
$earlierUserArr[] = array(
    "user_name" => "13953967383",
    "riQi" => "2018-04-20",
    'level' => 4
);
$earlierUserArr[] = array(
    "user_name" => "13305395444",
    "riQi" => "2018-04-20",
    'level' => 4
);
$earlierUserArr[] = array(
    "user_name" => "13173078555",
    "riQi" => "2018-04-20",
    'level' => 4
);
$earlierUserArr[] = array(
    "user_name" => "15949880912",
    "riQi" => "2018-04-20",
    'level' => 4
);
$earlierUserArr[] = array(
    "user_name" => "15966570777",
    "riQi" => "2018-04-20",
    'level' => 4

);
$earlierUserArr[] = array(
    "user_name" => "15098508029",
    "riQi" => "2018-04-20",
    'level' => 4
);
$earlierUserArr[] = array(
    "user_name" => "13854931768",
    "riQi" => "2018-04-20",
    'level' => 1

);
$earlierUserArr[] = array(
    "user_name" => "13256577066",
    "riQi" => "2018-04-20",
    'level' => 1
);

$earlierUserArr[] = array(
    "user_name" => "13905391175",
    "riQi" => "2018-05-03",
    'level' => 4
);
$earlierUserArr[] = array(
    "user_name" => "13371285559",
    "riQi" => "2018-05-06",
    'level' => 1
);
$earlierUserArr[] = array(
    "user_name" => "13805390053",
    "riQi" => "2018-05-08",
    'level' => 1
);
$earlierUserArr[] = array(
    "user_name" => "13953901888",
    "riQi" => "2018-05-15",
    'level' => 1
);
$earlierUserArr[] = array(
    "user_name" => "15853578878",
    "riQi" => "2018-05-11",
    'level' => 1
);
$earlierUserArr[] = array(
    "user_name" => "13581096869",
    "riQi" => "2018-05-24",
    'level' => 1
);
$earlierUserArr[] = array(
    "user_name" => "18315725220",
    "riQi" => "2018-05-25",
    'level' => 1
);
$earlierUserArr[] = array(
    "user_name" => "15253913779",
    "riQi" => "2018-05-25",
    'level' => 1

);
$earlierUserArr[] = array(
    "user_name" => "15069990887",
    "riQi" => "2018-05-27",
    'level' => 1

);
$earlierUserArr[] = array(
    "user_name" => "18369526610",
    "riQi" => "2018-05-28",
    "level" => 1
);
$earlierUserArr[] = array(
    "user_name" => "18754977533",
    "riQi" => "2018-05-28",
    "level" => 1
);
$earlierUserArr[] = array(
    "user_name" => "17353976321",
    "riQi" => "2018-06-03",
    "level" => 1
);
$earlierUserArr[] = array(
    "user_name" => "18905495757",
    "riQi" => "2018-06-10",
    "level" => 1
);

$earlierUserArr[] = array(
    "user_name" => "15653900166",
    "riQi" => "2018-06-11",
    "level" => 1
);
$earlierUserArr[] = array(
    "user_name" => "15853914555",
    "riQi" => "2018-06-01",
    "level" => 1
);
$earlierUserArr[] = array(
    "user_name" => "13188731999",
    "riQi" => "2018-06-01",
    "level" => 1
);
$earlierUserArr[] = array(
    "user_name" => "15588179567",
    "riQi" => "2018-06-05",
    "level" => 1
);
$earlierUserArr[] = array(
    "user_name" => "18669517166",
    "riQi" => "2018-06-07",
    "level" => 1
);
$earlierUserArr[] = array(
    "user_name" => "18669655836",
    "riQi" => "2018-06-10",
    "level" => 1
);
$earlierUserArr[] = array(
    "user_name" => "13153957313",
    "riQi" => "2019-06-10",
    "level" => 5
);


$sql = "select * from  zt_setting ";
$row = mysql_query($sql);
while ($rs = mysql_fetch_assoc($row)) {
    $configs[] = $rs;
}



// 要风控的体系
$tiXiUser = "('15966570777')"; // 毛建军
$tiXiUserArr = array();
$query1 = "select *  from xbmall_users where  user_name in {$tiXiUser} ";
$result = mysql_query($query1, $db);
while ($row = mysql_fetch_array($result)) {
    $resultArr[] = $row;
    getTiXi($row['user_id'], $tiXiUserArr);
}

$czje = $_REQUEST['czje'];
$type = $_REQUEST['type'];
$tjrq = time(); // 提交时间
$nowDateStr = date("Y-m-d H:i:s", $tjrq);


// $sql = "select * from xbmall_users ";
// $res = mysql_query($sql);
// $i=0;
// while ($row = mysql_fetch_array($res)) {
//     if ($row) {
// //         var_dump($row);
// //         $i++;
// //         if($i>10)break;
//         tiao_zheng_ji_bie($row);
//     }
// }

// die;



$userInfo = getUser($operateUser);
$chuJuRiQi = $tjrq - (45 * 24 * 3600);

$zhangHuJinE = $userInfo['xxtzztxjf'] + $userInfo['xxtzztxjf_b'] + $userInfo['tui_jian_shou_yi'] + $userInfo['dian_pu_zi_jin']; // 可用的总金额

if ($czje > $zhangHuJinE) {
    alertAndRelocation("您的账户余额不足, 现有余额{$zhangHuJinE}", "xxtz_cz.html");
}



if ($czje % 3600 != 0) {
    alertAndRelocationHistory("您的投资金额,必须是3600的整数倍");
}


$type = 0;
$fenShu = $czje / 3600; // 投资的份数
                        
// 查询有没有投资过
$userInfo = getUser($operateUser);
$is_new = 1;

if ($userInfo['is_dian_pu'] == 0) { // 还没有开通订单
    checkAdress($operateUser);
    $fenShu = $fenShu - 1; // 第一笔投资,投资次数减一
    diYiBiTouZi($operateUser);
    $is_new = 0; // 用户第一次投资
    $sql = "update xbmall_users set is_dian_pu=1 where user_name='{$operateUser}'";
    mysql_query($sql);
}

$min = todayInvestC($fenShu, $operateUser);

$sql = "select yi_tou_zi_ci_shu,ke_tou_zi_ci_shu from xbmall_users where user_name='{$operateUser}'";
$touZi = getRow($sql);
$haiKeTouZiCiShu = $touZi['ke_tou_zi_ci_shu'] - $touZi['yi_tou_zi_ci_shu']; // 还可投资的次数
                                                                            
// 查询还没有出局的投资数
$sql = " select count(0) from  zt_xxtz where user='{$operateUser}'  and cj_state=0 and ft_state!=1  and tjrq > {$chuJuRiQi}  ";
$weiChuJuChuShiTouZiShu = getOne($sql);

// 处在第一阶段的投资
$sql = "select count(0) from zt_xxtz where user='{$operateUser}' and cj_state=0  and ft_state!=1    ";
$step1TouZiCount = getOne($sql); //
$step1TouZiCount = $step1TouZiCount >= 3 ? 3 : $step1TouZiCount;

if ($touZi['yi_tou_zi_ci_shu'] + $fenShu > $touZi['ke_tou_zi_ci_shu'] + 3) { //
    $shengYuCiShu = $touZi['ke_tou_zi_ci_shu'] - $touZi['yi_tou_zi_ci_shu']; // 可能是负数
    $shengYuCiShu = $shengYuCiShu + $step1TouZiCount; // 剩余投资数等于剩余的投资数加上最多3笔的复投次数
    $shengYuCiShu = $shengYuCiShu * 3600; // 剩余投资金额
    alertAndRelocationHistory("您还可投资{$shengYuCiShu}元,可以继续推荐, 获取更多投资资格");
} else if ($touZi['yi_tou_zi_ci_shu'] >= $touZi['ke_tou_zi_ci_shu']) {
    // 已经超出投资数限制, 判断还能不能复投3笔
    if ($step1TouZiCount >= 3) {
        alertAndRelocationHistory("您的投资次数已达到上限{$touZi['ke_tou_zi_ci_shu']},并且已有3笔投资运行中,可以继续推荐, 获取更多投资资格");
    }
}

// 如果投资分数大于未出局的初始投资数, 那么可以复投的数量是未出局的初始投资数,否则为投资的份数
$fuTouShu = $fenShu > $weiChuJuChuShiTouZiShu ? $weiChuJuChuShiTouZiShu : $fenShu;

// 处在第一阶段的投资,并且没有出局
$sql = "select * from zt_xxtz where user='{$operateUser}' and cj_state=0  and ft_state!=1  and step=1   and tjrq > {$chuJuRiQi} order  by id asc  ";
$xxtz = mysql_query($sql);

$next_ft_time = $tjrq + 3600 * 24 * 45;

for ($i = 0; $i < $fuTouShu; $i ++) {
    $data = mysql_fetch_array($xxtz);
    // 插入新的初始投资, 记录是为哪笔初始投资复投的
    $sql = "insert into zt_xxtz(fk_user,user, tjrq,step_start_rq,type,czje, parent_id,step,cj_state,next_ft_time)values('{$operateUser}','{$operateUser}','{$tjrq}','{$tjrq}',{$type},3600,{$data['id']},1,0,$next_ft_time)";
    mysql_query($sql);
    $newId = mysql_insert_id();
    log3600("复投{$data['id']},编号:{$newId}");
    
    $sql = "update  zt_xxtz set child_id={$newId} ,ft_state=1  where id={$data['id']}";
    mysql_query($sql);
    jiangLi($data, getUser($data['user']));
}

if ($fenShu > $weiChuJuChuShiTouZiShu) { // 已经没有可复投的初始投资, 开始新的初始投资
    for ($i = 0; $i < $fenShu - $weiChuJuChuShiTouZiShu; $i ++) {
        $sql = "insert into zt_xxtz(fk_user,user ,tjrq,step_start_rq,type,czje,step,cj_state,next_ft_time)values('{$operateUser}','{$operateUser}','{$tjrq}','{$tjrq}',{$type},3600,1,0,$next_ft_time)";
        mysql_query($sql);
        $newId = mysql_insert_id();
        $data = getRow("select * from  zt_xxtz where id={$newId}");
        $tjrq = $tjrq + 1;
        $nowDateStr = date("Y-m-d H:i:s", $tjrq);
        log3600("投资一份,编号:{$newId}");
        jiangLi($data, getUser($data['user']));
    }
}

mysql_close($db);

alertAndRelocation("提交成功", "xxtz_list.html");

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
    
    $zhongXinNum = 0; // 向上查找的过程中运营中心的数量
    $dongShiNum = 0; // 向上查找的过程中懂事的数量
    
    tiao_zheng_ji_bie($user);//先调整做单者本人的级别
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
    global $nowDateStr, $nowDayStr, $is_new;
    
    $sql = "select yi_ceng_kao_he_ri_qi , flag, user_name,recommend_user,yi_tou_zi_ci_shu, parent_id ,level, dian_pu_shu1, dian_pu_shu2,ye_ji,FROM_UNIXTIME(man_dan_ri_qi)  as man_dan_ri_qi from xbmall_users where user_name='{$user['recommend_user']}'";
    
    $tuiJianRen = getRow($sql); // 找出链中需要奖励的人
    
    if ($tuiJianRen == null)
        return;
    
    if ($is_new == 0) { // 是用户第一次入单, 每单额外给推荐人36推荐奖励
                        // 给推荐人赠送36的推荐收益
        faFangUserJiFen($tuiJianRen['user_name'], 36, "您推荐的" . $user['real_name'] . "提交第一笔订单,您获得推荐收益36");
    }
    
    $sql = "select count(0) from zt_xxtz where user='{$user['user_name']}' and step=1 and cj_state=0 "; // 有3份A阶段未出局投资
    $touZiShu = getOne($sql);
    if ($touZiShu == 3) {
        $zg_userName = getXM($user['user_name']);
        $sql = "insert into zt_xxtz_sj(user,zg_user,rq,zg_level,user_level) values('{$tuiJianRen['user_name']}','{$user['user_name']}','{$nowDayStr}',1,{$tuiJianRen['level']})";
        mysql_query($sql); // 插入今天满足3单的用户信息
                           
        // 如果做单者的推荐人有满单日期, 并且做单者是在推荐人满100单后注册的, 给推荐人增加10800提现额度
        if ($tuiJianRen['man_dan_ri_qi'] != null && $tuiJianRen['man_dan_ri_qi'] != '' && $user['zcrq'] > $tuiJianRen['man_dan_ri_qi']) {
            
            $sql = "update xbmall_users set jing_tai_ke_ti_xian=jing_tai_ke_ti_xian+10800 where user_name='{$tuiJianRen['user_name']}'";
            mysql_query($sql);
            $sql = "insert into zt_xxtzzs (user,zsrq,comment) values('{$tuiJianRen['user_name']}','{$nowDateStr}','({$zg_userName})成为你的一层有效会员,您的提现额度增加10800')";
            mysql_query($sql);
        }
        
        $stop_max = getWebConfig("stop_max"); // 静态多少份止局
        if ($tuiJianRen['yi_tou_zi_ci_shu'] >= $stop_max) { // 已投资次数大于等于止局数
            $sql = "select num from zt_xxtz_zhi_ju where user='{$tuiJianRen['user_name']}'";
            $num = getOne($sql);
            if ($num == null) {
                $sql = "insert into zt_xxtz_zhi_ju (user,num) values('{$tuiJianRen['user_name']}',1)";
                $num = 1;
            } else {
                $sql = "update zt_xxtz_zhi_ju set num=num+1  where user='{$tuiJianRen['user_name']}'";
                $num = $num + 1;
            }
            mysql_query($sql);
            if ($num % 3 == 0) { // 每满3个
                $sql = "update  xbmall_users set ke_tou_zi_ci_shu=ke_tou_zi_ci_shu+50  where user_name='{$tuiJianRen['user_name']}'";
                mysql_query($sql);
                $sql = "insert into zt_xxtzzs (user,zsrq,comment) values('{$tuiJianRen['user_name']}','{$nowDateStr}','您达到止局数{$stop_max},推荐满3个会员第" . ($num / 3) . "次,增加了50个可用投资数')";
                mysql_query($sql);
            }
        }
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
        // echo "运营中心管理费:{$guanLiFei}\r\n" ;
    } else if ($level == 5) { // 找到了懂事
        $guanLiFei = getDongShiGuanLiFei($dongShiNum); // 懂事管理费
        $dongShiNum ++;
    } else { // 初中高级代理的管理费
        $guanLiFei = getDaiLiGuanLiFei($level);
    }
    
    // 增加积分变动记录,推荐收益
    $userXM = getXM($user['user_name']);
    
    if ($guanLiFei > 0) {
        if ($xxtz_sj) { // 投资人是今天成为的有效商家
            faFangUserJiFen($tuiJianRen['user_name'], $guanLiFei, "您推荐的{$userXM}投资一份,由于其今天成为的有效商家,您作为{$shenFen}获得管理费{$guanLiFei}");
        } else {
            faFangUserJiFen($tuiJianRen['user_name'], $guanLiFei, "您推荐的{$userXM}投资一份,您作为{$shenFen}获得管理费{$guanLiFei}");
        }
    }
    
    $tuiJianRen = shengJi($tuiJianRen);//一层升级
    
    return $tuiJianRen;
}

/**
 * 是否考核第二层
 *
 * @param unknown $user_name
 */
function is_kao_he_er_ceng($userInfo)
{
    global $tjrq, $earlierUserArr;
    var_dump("tjrq:".$tjrq.":".$userInfo['yi_ceng_kao_he_ri_qi']);
    var_dump("<br>");
 
    $nextTime = 0;
    for ($i = 0; $i < count($earlierUserArr); $i ++) {
        $data2 = $earlierUserArr[$i];
        if ($data2['user_name'] == $userInfo['user_name']) { // 如果是数组中的人名, 指定了时间的人
            if ($data2['level'] >= 4) { // 运营中心级别及以上,延后一个月进行二次考核
                $nextTime = strtotime("+1 months", $userInfo['yi_ceng_kao_he_ri_qi']);
            } else {
                $nextTime = strtotime("+2 months", $userInfo['yi_ceng_kao_he_ri_qi']);
            }
           
            if ($tjrq >= $nextTime)
                return true; // 到了2层考核时间
        }
    }
    if ($nextTime == 0) { // 非指定的人,第一层考核后延后一个月进行二层考核
        if ($userInfo['yi_ceng_kao_he_ri_qi'] != null) {
            $nextTime = strtotime("+1 months", $userInfo['yi_ceng_kao_he_ri_qi']);
            if ($tjrq >= $nextTime)
                return true;
        }
    }
    
    return false;
}

/**
 * 是否考核一层
 * @param unknown $userInfo
 * @return boolean
 */
function is_kao_he_yi_ceng($userInfo)
{
    global $tjrq, $earlierUserArr;
    $nextTime = 0;
    
    if ($userInfo['yi_ceng_kao_he_ri_qi'] == null) {//如果没有设置一层考核时间,考核一层
        return true;
    }elseif($tjrq> $userInfo['yi_ceng_kao_he_ri_qi']){//如果设置了一层考核时间,并且现在的时间超过了一层考核时间
        return true;
    }
    
    return false;
}

/**
 * 根据用户下面合格的人员数及自身的有效单(A阶段), 调整会员的级别
 *
 * @param unknown $user
 */
function tiao_zheng_ji_bie($userInfo)
{
    global $nowDateStr, $earlierUserArr, $tjrq;
 
    $user = $userInfo['user_name'];
    
    // 自己A阶段有效单数
    $sql = "select count(0) from zt_xxtz  where user='{$user}' and step=1 and cj_state=0 ";
    $you_xiao_dan = getOne($sql);
    $yi_ceng_dian_pu_shu = getYiCengDianPuShu($user);
    
    if (is_kao_he_er_ceng($userInfo)) {
       
        er_ceng_kao_he($userInfo, $yi_ceng_dian_pu_shu, $you_xiao_dan);
    } elseif (is_kao_he_yi_ceng($userInfo)) {
        
//         echo  "<br>{$userInfo['user_name']}需要考核一层";
        yi_ceng_kao_he($userInfo, $yi_ceng_dian_pu_shu, $you_xiao_dan);
    } else { // 不考核一层, 也不考核二层,只管升级
//          echo  "<br>{$userInfo['user_name']}不考核一层也不考核二层";
        yi_ceng_sheng_ji($userInfo, $yi_ceng_dian_pu_shu, $you_xiao_dan);
    }
    
    return $userInfo;
}

/**
 * 对二层进行考核
 * 
 * @param unknown $userInfo
 * @param unknown $yi_ceng_dian_pu_shu
 * @param unknown $you_xiao_dan
 */
function er_ceng_kao_he($userInfo, $yi_ceng_dian_pu_shu, $you_xiao_dan)
{
    global $nowDateStr, $earlierUserArr, $tjrq;
    $user  = $userInfo['user_name'];
    $level = getLevelByDianPuShu($userInfo, $yi_ceng_dian_pu_shu, $you_xiao_dan); // 当前应该在的级别
   
    $er_ceng_dian_pu_shu = getErCengDianPuShu($user);
    $level2 = getLevelByErCengDianPuShu($er_ceng_dian_pu_shu); // 二层店铺数考核
    
//     $yeJi = getYiCengYeJi($user);
//     $yeJi2 = getErCengYeJi($user);
//     $zongYeJi = $yeJi + $yeJi2;
//     $level3 = getLevelByYeJi($zongYeJi);
   
    if ($level > $level2) {
        $level = $level2;
    }
//     if ($level > $level3) {
//         $level = $level3;
//     }
  
    
    
    
    // 有级别变化
    if ($userInfo['level'] != $level) {
        
//         var_dump(  "<br>".$user."考核二层:一层店铺".$yi_ceng_dian_pu_shu.":二层店铺".$er_ceng_dian_pu_shu.
//             ":总业绩".$zongYeJi.":有效单".$you_xiao_dan."调整级别为:".$level);
        
        $sql = "update  xbmall_users  set level={$level} where user_name='{$user}'";
        mysql_query($sql);
        $shenFen = getShenFen($level);
        if ($userInfo['level'] > $level) { // 降级了
            $sql = "insert into zt_xxtzzs (user,zsrq,comment) values('{$user}','{$nowDateStr}','您的A阶段有效单{$you_xiao_dan},直推有效会员{$yi_ceng_dian_pu_shu}个, 二层店铺数{$er_ceng_dian_pu_shu}, 两层业绩{$zongYeJi}, 当前级别调整为{$shenFen},需要加油了')";
        } else { // 升级了
            $sql = "insert into zt_xxtzzs (user,zsrq,comment) values('{$user}','{$nowDateStr}','您的A阶段有效单{$you_xiao_dan},直推有效会员{$yi_ceng_dian_pu_shu}个, 二层店铺数{$er_ceng_dian_pu_shu}, 两层业绩{$zongYeJi},当前级别升级为{$shenFen},恭喜恭喜!')";
        }
        $userInfo['level'] = $level;
        mysql_query($sql);
    }else{
//         var_dump(  "<br>".$user."考核二层:一层店铺".$yi_ceng_dian_pu_shu.":二层店铺".$er_ceng_dian_pu_shu.
//             ":总业绩".$zongYeJi.":有效单".$you_xiao_dan."级别仍然为:".$level);
    }
}


/**
 * 没到一层考核时间, 只进行升级操作
 * @param unknown $userInfo
 * @param unknown $yi_ceng_dian_pu_shu
 * @param unknown $you_xiao_dan
 */
function   yi_ceng_sheng_ji($userInfo, $yi_ceng_dian_pu_shu, $you_xiao_dan){
    
    global $nowDateStr  ;
    $user = $userInfo['user_name'];
    $level = getLevelByDianPuShu($userInfo, $yi_ceng_dian_pu_shu, $you_xiao_dan); // 当前应该在的级别
    if ($userInfo['level'] < $level) { // 升级
        $sql = "update  xbmall_users  set level={$level} where user_name='{$user}'";
        mysql_query($sql);
        $shenFen = getShenFen($level);
        // 升级了
        $sql = "insert into zt_xxtzzs (user,zsrq,comment) values('{$user}','{$nowDateStr}','您的A阶段有效单{$you_xiao_dan},直推有效会员{$yi_ceng_dian_pu_shu}个,当前级别升级为{$shenFen},恭喜恭喜!')";
        $userInfo['level'] = $level;
        mysql_query($sql);
    }
    
}
/**
 * 对一层进行考核
 * 
 * @param unknown $userInfo
 * @param unknown $yi_ceng_dian_pu_shu
 * @param unknown $you_xiao_dan
 */
function yi_ceng_kao_he($userInfo, $yi_ceng_dian_pu_shu, $you_xiao_dan)
{
    global $nowDateStr, $earlierUserArr, $tjrq;
    $user = $userInfo['user_name'];
    
    $level = getLevelByDianPuShu($userInfo, $yi_ceng_dian_pu_shu, $you_xiao_dan); // 当前应该在的级别
    
    if ($userInfo['level'] != $level) { // 有级别变化
        
        echo  $user."考核1层,店铺数{$yi_ceng_dian_pu_shu},有效单{$you_xiao_dan},级别调整为".$level ;
        if($userInfo['yi_ceng_kao_he_ri_qi']==null){//没有一层考核时间, 记录一层的考核时间
            $sql = "update  xbmall_users  set level={$level},yi_ceng_kao_he_ri_qi={$tjrq} where user_name='{$user}'";
        }else{
            $sql = "update  xbmall_users  set level={$level} where user_name='{$user}'";
        }
        
       
        mysql_query($sql);
        $shenFen = getShenFen($level);
        if ($userInfo['level'] > $level) { // 降级了
            $sql = "insert into zt_xxtzzs (user,zsrq,comment) values('{$user}','{$nowDateStr}','您的A阶段有效单{$you_xiao_dan},直推有效会员{$yi_ceng_dian_pu_shu}个,当前级别调整为{$shenFen},需要加油了')";
        } else { // 升级了
            $sql = "insert into zt_xxtzzs (user,zsrq,comment) values('{$user}','{$nowDateStr}','您的A阶段有效单{$you_xiao_dan},直推有效会员{$yi_ceng_dian_pu_shu}个,当前级别升级为{$shenFen},恭喜恭喜!')";
        }
        $userInfo['level'] = $level;
        mysql_query($sql);
    }
}

/**
 * 获取二层店铺数
 *
 * @param unknown $userName
 * @return unknown
 */
function getErCengDianPuShu($userName)
{
    $sql = "SELECT d.user_name, d.real_name, d.recommend_user from xbmall_users d JOIN
    (
SELECT b.user as buser,  count(0) from xbmall_users a JOIN   zt_xxtz  b
on a.user_name=b.user  WHERE a.recommend_user=
'{$userName}' and b.cj_state=0 and b.step=1   GROUP BY  b.user HAVING count(0)>=3 ) c
  on d.recommend_user=c.buser  JOIN  zt_xxtz e on d.user_name=e.user WHERE e.cj_state=0 and e.step=1 GROUP BY e.`user` HAVING count(0)>=3";
    $res = mysql_query($sql);
    $er_ceng_dian_pu_shu = mysql_num_rows($res);
    return $er_ceng_dian_pu_shu;
}

/**
 * 获取一层店铺数
 *
 * @param unknown $userName
 * @return unknown
 */
function getYiCengDianPuShu($userName)
{
    // 自己一层推荐的店铺数(A阶段未出局单数大于等于3份)
    $sql = "  SELECT  b.user,  count(0) as num from xbmall_users a JOIN   zt_xxtz  b   on a.user_name=b.user  WHERE a.recommend_user='{$userName}' and b.cj_state=0 and b.step=1   GROUP BY  b.user HAVING count(0)>=3 ";
    $res = mysql_query($sql);
    $yi_ceng_dian_pu_shu = mysql_num_rows($res);
    return $yi_ceng_dian_pu_shu;
}

/**
 * 获取一层业绩
 * 只要投单, 不论出局与否都算业绩
 *
 * @param unknown $userName
 * @return unknown
 */
function getYiCengYeJi($userName)
{
    $sql = "  SELECT    count(0)*3600 as yeJi from xbmall_users a JOIN   zt_xxtz  b   on a.user_name=b.user  WHERE a.recommend_user='{$userName}' ";
    $yeJi = getOne($sql);
    return $yeJi;
}

function getErCengYeJi($userName)
{
    $sql = " SELECT count(0)*3600 as yeJi from xbmall_users d JOIN
    (
SELECT b.user as buser,  count(0) from xbmall_users a JOIN   zt_xxtz  b
on a.user_name=b.user  WHERE a.recommend_user=
'{$userName}'  ) c
  on d.recommend_user=c.buser  JOIN  zt_xxtz e on d.user_name=e.user ";
    $yeJi = getOne($sql);
    return $yeJi;
}

/**
 * 获取店铺数应该在的级别
 *
 * @param unknown $dianPuShu
 */
function getLevelByDianPuShu($userInfo, $dianPuShu, $you_xiao_dan)
{
    $chu = getWebConfig("sheng_chu_dai_dian_pu");
    $zhong = getWebConfig("sheng_zhong_dai_dian_pu");
    $gao = getWebConfig("sheng_gao_dai_dian_pu");
    $yunYing = getWebConfig("sheng_yun_ying_dian_pu");
    
    if ($dianPuShu >= $yunYing) {
        $level = getLevelByYouXiaoDan($userInfo, $you_xiao_dan);
        if ($level <= 4)
            return $level;
        return 4;
    }
    
    if ($dianPuShu >= $gao) {
        $level = getLevelByYouXiaoDan($userInfo, $you_xiao_dan);
        if ($level <= 3)
            return $level;
        return 3;
    }
    
    if ($dianPuShu >= $zhong) {
        $level = getLevelByYouXiaoDan($userInfo, $you_xiao_dan);
        if ($level <= 2)
            return $level;
        return 2;
    }
    
    if ($dianPuShu >= $chu) {
        $level = getLevelByYouXiaoDan($userInfo, $you_xiao_dan);
        if ($level <= 1)
            return $level;
        return 1; //
    }
    
    return 0;
}

/**
 * 二层店铺数
 *
 * @param unknown $userInfo
 * @param unknown $dianPuShu
 * @param unknown $you_xiao_dan
 * @return number
 */
function getLevelByErCengDianPuShu($dianPuShu)
{
    $chu = getWebConfig("sheng_chu_dai_dian_pu2");
    $zhong = getWebConfig("sheng_zhong_dai_dian_pu2");
    $gao = getWebConfig("sheng_gao_dai_dian_pu2");
    $yunYing = getWebConfig("sheng_yun_ying_dian_pu2");
   
    if ($dianPuShu >= $yunYing) {
        return 4;
    }
    if ($dianPuShu >= $gao) {
        return 3;
    }
    if ($dianPuShu >= $zhong) {
        return 2;
    }
    if ($dianPuShu >= $chu) {
        return 1; //
    }
    
    return 0;
}

/**
 * 根据业绩返回层级
 *
 * @param unknown $yeJi
 * @return number
 */
function getLevelByYeJi($yeJi)
{
    $chu = getWebConfig("sheng_chu_dai_ye_ji");
    $zhong = getWebConfig("sheng_zhong_dai_ye_ji");
    $gao = getWebConfig("sheng_gao_dai_ye_ji");
    $yunYing = getWebConfig("sheng_yun_ying_ye_ji");
    if ($yeJi >= $yunYing) {
        return 4;
    }
    if ($yeJi >= $gao) {
        return 3;
    }
    if ($yeJi >= $zhong) {
        return 2;
    }
    if ($yeJi >= $chu) {
        return 1; //
    }
    
    return 0;
}

function getLevelByYouXiaoDan($userInfo, $you_xiao_dan)
{
    if ($userInfo['flag'] == 1) // 不检查flag等于1的人的自身danshu
        return 100;
    
    if ($you_xiao_dan >= 10)
        return 4; // 运营中心
    
    if ($you_xiao_dan >= 7)
        return 3; // 高级
    
    if ($you_xiao_dan >= 5)
        return 2; // 中级
    
    if ($you_xiao_dan >= 3)
        return 1; // 初级代理
    
    return 0;
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
    $sql = "select yi_ceng_kao_he_ri_qi , flag, user_name,recommend_user,yi_tou_zi_ci_shu, parent_id ,level, dian_pu_shu1, dian_pu_shu2,ye_ji,FROM_UNIXTIME(man_dan_ri_qi)  as man_dan_ri_qi  from xbmall_users where user_name='{$yiCengTuiJianRen['recommend_user']}'";
    $tuiJianRen = getRow($sql); // 找出链中需要奖励的人
    
    if ($tuiJianRen == null)
        return;
    
    if ($xxtz['type'] == 2) {
        // 增加间接推荐人的二层订单数
        if ($xxtz['fk_user'] != $xxtz['user']) { // 是推荐人给开通订单
            $sql = "update xbmall_users set dian_pu_shu2=dian_pu_shu2+1  where user_name='{$yiCengTuiJianRen['user']}'";
            mysql_query($sql);
            $yiCengTuiJianRen['dian_pu_shu2'] = $yiCengTuiJianRen['dian_pu_shu2'] + 1;
            $zg_userName = getXM($xxtz['fk_user']);
            $sql = "insert into zt_xxtzzs (user,zsrq,comment) values('{$yiCengTuiJianRen['user']}','{$nowDateStr}','({$zg_userName})成为你的二层有效商家,二层有效商家个数{$yiCengTuiJianRen['dian_pu_shu2']}')";
            mysql_query($sql);
        } else { // 自己开通订单
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
    
    if ($guanLiFei > 0) {
        faFangUserJiFen($tuiJianRen['user_name'], $guanLiFei, "第2层{$userXM}投资一份,您作为{$shenFen}获得管理费{$guanLiFei}");
    }
    
    $tuiJianRen = shengJi($tuiJianRen); // 奖励完了再升级, 间接推荐人
    return $tuiJianRen;
}

/**
 * 发放用户积分
 */
function faFangUserJiFen($user, $benCiJiFen, $message, $type = 1)
{
    global $nowDateStr;
    $xxtzzfwf = $benCiJiFen * 0.06; // 总服务费
    $xxtzzcbjz = $benCiJiFen * 0.02; // 总储备聚珠
    $xxtzzsyjz = $benCiJiFen * 0.02; // 总收益聚珠
    $xxtzzxfjz = $benCiJiFen * 0; // 总消费聚珠
    $tui_jian_shou_yi = $benCiJiFen * 0.9; // 总推荐收益
    
    /**
     * 修改用户的账户,包括以前的消费聚珠
     */
    $sql = "update xbmall_users  set xxtzzfwf=xxtzzfwf+{$xxtzzfwf}, xxtzzcbjz=xxtzzcbjz+{$xxtzzcbjz} ,xxtzzsyjz=xxtzzsyjz+{$xxtzzsyjz} ,
 	 pay_points=pay_points+{$xxtzzxfjz} , xfjf=xfjf+{$xxtzzxfjz} ,  tui_jian_shou_yi=tui_jian_shou_yi+{$tui_jian_shou_yi}  where user_name='{$user}'";
    mysql_query($sql);
    
    /**
     * 创建本次的分配记录
     */
    $sql = "insert into zt_xxtzzs (type,user, zsrq,jf ,xxtzzfwf,xxtzzcbjz,xxtzzsyjz,xxtzzxfjz,tui_jian_shou_yi, comment)
 	values({$type},'{$user}','{$nowDateStr}',{$benCiJiFen}, {$xxtzzfwf},{$xxtzzcbjz},{$xxtzzsyjz},{$xxtzzxfjz},{$tui_jian_shou_yi},'{$message}')";
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
    $sql = "select flag, user_name,recommend_user, parent_id ,level, dian_pu_shu1, dian_pu_shu2,ye_ji from xbmall_users where user_name='{$shangCengTuiJianRen['recommend_user']}'";
    
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
    
    $shenFen = getShenFen($tuiJianRen['level']);
    $userXM = getXM($xxtz['user']);
    
    faFangUserJiFen($tuiJianRen['user_name'], $guanLiFei, "{$userXM}投资一份,您作为{$shenFen}获得管理费{$guanLiFei}");
    
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
 * 获取某层次用户升级需要某层多少订单数, 懂事不参与升级
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
    } else { // 需要的是两层业绩,默认不需要业绩
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
 * @param unknown $message
 * @param unknown $money
 */
function logMoney($message, $money)
{
    global $nowDateStr;
    $operateUser = $_COOKIE['ECS']['username'];
    
    $sql = "select * from xbmall_users where user_name='{$operateUser}'";
    $user = getRow($sql);
    $zhangHuJinE = $user['xxtzztxjf'] + $user['xxtzztxjf_b'] + $user['tui_jian_shou_yi'] + $user['dian_pu_zi_jin']; // 可用的总金额
    
    if (floor($money / 100) * 100 > floor($zhangHuJinE / 100) * 100) {
        alertAndRelocationHistory("您的可用金额为:{$zhangHuJinE}");
    }
    
    $jianDianPuJinE = $money >= $user['dian_pu_zi_jin'] ? $user['dian_pu_zi_jin'] : $money; // 要减的赠送收益
    $stepMoney = $money - $jianDianPuJinE; // 还需要减多少
    if ($stepMoney > 0) { // 订单资金不够
        $jianZengSongJinE = $stepMoney >= $user['xxtzztxjf'] ? $user['xxtzztxjf'] : $stepMoney; // 要减的A阶段赠送收益
        $stepMoney = $stepMoney - $jianZengSongJinE;
        if ($stepMoney > 0) { // A阶段投资收益不够
            
            $jianZengSongJinEB = $stepMoney >= $user['xxtzztxjf_b'] ? $user['xxtzztxjf_b'] : $stepMoney; // 要减的A阶段赠送收益
            $stepMoney = $stepMoney - $jianZengSongJinEB;
            if ($stepMoney > 0) { // B阶段的收益不够
                $jianTuiJianShouYi = $stepMoney >= $user['tui_jian_shou_yi'] ? $user['tui_jian_shou_yi'] : $stepMoney; // 要减的推荐收益
            } else {
                $jianTuiJianShouYi = 0;
            }
        } else {
            $jianTuiJianShouYi = 0;
            $jianZengSongJinEB = 0;
        }
    } else {
        $jianZengSongJinE = 0;
        $jianTuiJianShouYi = 0;
        $jianZengSongJinEB = 0;
    }
    
    $sql = "update xbmall_users set dian_pu_zi_jin=dian_pu_zi_jin-{$jianDianPuJinE} , xxtzztxjf=xxtzztxjf-{$jianZengSongJinE}, xxtzztxjf_b=xxtzztxjf_b-{$jianZengSongJinEB} ,tui_jian_shou_yi=tui_jian_shou_yi-{$jianTuiJianShouYi} where  user_name='{$user['user_name']}'";
    mysql_query($sql);
    // 插入积分变动记录， 返回用户的积分
    $sql = "insert into zt_xxtzzs (user,dian_pu_zi_jin,xxtzztxjf,xxtzztxjf_b,tui_jian_shou_yi,zsrq,comment) values('{$user['user_name']}',-{$jianDianPuJinE},-{$jianZengSongJinE},-{$jianZengSongJinEB},-{$jianTuiJianShouYi},'{$nowDateStr}','{$message},减去订单资金{$jianDianPuJinE},减去A阶段投资收益{$jianZengSongJinE},减去B阶段投资收益{$jianZengSongJinEB},减去推荐收益{$jianTuiJianShouYi}')";
    mysql_query($sql);
}

function log3600($message)
{
    logMoney($message, 3600);
}

function getUserAddress($userName)
{
    $arr = array();
    if ($userName != null) {
        /* 取默认地址 */
        $sql = "SELECT ua.*  FROM xbmall_user_address   AS ua, xbmall_users  AS u
        WHERE u.user_name='$userName' AND ua.address_id = u.address_id";
        $arr = getRow($sql);
    }
    return $arr;
}

// 检查用户有没有收货地址
function checkAdress($userName)
{
    $userAdress = getUserAddress($userName);
    
    if (empty($userAdress)) { // 转到收货地址添加页面
        @header("Location: /mobile/flow.php?step=consignee&type=xxtz_add\n", true);
        exit();
    }
}

function get_regions($type = 0, $parent = 0)
{
    $sql = "SELECT region_id, region_name FROM xbmall_region  WHERE region_type = {$type} AND parent_id = {$parent}";
    
    return mysql_query($sql);
}

/**
 * 第一笔投资, 奖励用户3600消费积分
 */
function diYiBiTouZi($dianPuUser)
{
    global $tjrq;
    
    log3600("开通订单");
    
    $nowDateStr = date("Y-m-d H:i:s", time());
    // 给会员赠送3600的消费积分
    $sql = "insert into zt_xxtzzs (user,xxtzzxfjz,zsrq,comment,type) values('{$dianPuUser}',3600,'{$nowDateStr}','开通订单,奖励消费积分3600',4)";
    mysql_query($sql);
    $nowDateStr = date("Y-m-d H:i:s", time() + 1);
    $sql = "insert into zt_xxtzzs (user,xxtzzxfjz,zsrq,comment,type) values('{$dianPuUser}',-3600,'{$nowDateStr}','开通订单,大礼包准备发货,减去消费积分3600',4)";
    mysql_query($sql);
}

/**
 * 是否是体系用户
 *
 * @param unknown $userName
 */
function isTiXiUser($userName)
{
    global $tiXiUserArr;
    if ($tiXiUserArr == null)
        return false;
    
    foreach ($tiXiUserArr as $key => $val) {
        if ($val['user_name'] == $userName)
            return true; // 在体系内
    }
    return false; // 不在体系内
}

/**
 * 获取体系下所有人
 *
 * @param unknown $rootId
 */
function getTiXi($rootId, &$resultArr)
{
    $sql = "select * from  xbmall_users where parent_id=" . $rootId;
    $result = mysql_query($sql);
    if (! isset($result)) {
        return;
    }
    while ($row = mysql_fetch_array($result)) {
        if ($row) {
            $resultArr[] = $row;
            getTiXi($row['user_id'], $resultArr);
        }
    }
}

/**
 * 如果用户达到了升级条件, 就升级用户,如果没有就不升级, 最后都返回处理后的用户信息
 */
function shengJi($userInfo)
{
    if ($userInfo == null)
        return;
    
    // 升级推荐人
    
    // 升级用户本身
    return tiao_zheng_ji_bie($userInfo);
}

/**
 * 今天最多可以投多少笔
 *
 * @param unknown $fenShu
 */
function todayInvestC($fenShu, $operateUser)
{
    
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
    $message = "您30天内";
    if ($min > $weekC) {
        $min = $weekC;
        $message = "您7天内";
    }
    if ($min > $dayC) {
        $min = $dayC;
        $message = "您今天";
    }
    
    if ($fenShu > $min) {
        alertAndRelocationHistory($message . "最多投资{$min}笔");
    }
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

// header("Location: xxcz_list.html");

?>
  			  