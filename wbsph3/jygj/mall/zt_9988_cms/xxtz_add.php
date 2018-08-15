<?php
include "../myphplib/init.php";
include "../myphplib/db.php";
include "../myphplib/message.php";
include "config/check.php";
date_default_timezone_set("Asia/Shanghai");

session_start();
if ($_SESSION['addXXFT'] != null) {
    $_SESSION['addXXFT'] = null;
} else { // cookie是空的
    // alertAndRelocation("您已经提交过了, 如再次提交, 请刷新页面", "xxcz_list.html");
}

$czje = $_REQUEST['czje'];
$type = $_REQUEST['type'];
$tjrq = $_POST['tjrq'];
// $tui_jian_user = $_POST['tui_jian_user'];//推荐人
$operateUser = $_POST['user']; // 给谁做单
 
$operateUser = getUserNameFromId($operateUser);//前台传来的是用户的id


$tjrq = strtotime($tjrq); // 提交时间

$userInfo = getUser($operateUser);
$chuJuRiQi = $tjrq - (45 * 24 * 3600);

$zhangHuJinE = $userInfo['xxtzztxjf'] + $userInfo['tui_jian_shou_yi'] + $userInfo['dian_pu_zi_jin']; // 可用的总金额

if ($czje > $zhangHuJinE) {
    alertAndRelocation("您的账户余额不足, 现有余额{$zhangHuJinE}", "xxtz_cz.html");
}

$nowDateStr = date("Y-m-d H:i:s", $tjrq);

if ($czje % 3600 != 0) {
    alertAndRelocationHistory("您的投资金额,必须是3600的整数倍");
}

$sql = "select * from  zt_setting ";
$row = mysql_query($sql);
while ($rs = mysql_fetch_assoc($row)) {
    $configs[] = $rs;
}

if ($type == 2) { // 开通订单
    kaiDian();
}
$type = 0;

$fenShu = $czje / 3600; // 投资的份数

$min = todayInvestC($operateUser);

if ($fenShu > $min) {
    alertAndRelocationHistory("您今天最多投资{$min}笔");
}

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
$sql = "select * from zt_xxtz where user='{$operateUser}' and cj_state=0  and ft_state!=1   and tjrq > {$chuJuRiQi} order  by id asc  ";
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

function getUserNameFromId($userId)
{
    $sql = "select user_name from xbmall_users where user_id='{$userId}'";
    return getOne($sql);
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
    
    $sql = "select user_name,recommend_user,yi_tou_zi_ci_shu, parent_id ,level, dian_pu_shu1, dian_pu_shu2,ye_ji from xbmall_users where user_name='{$user['recommend_user']}'";
    
    $tuiJianRen = getRow($sql); // 找出链中需要奖励的人
    
    if ($tuiJianRen == null)
        return;
        
        
        $sql = "select count(0) from zt_xxtz where user='{$user['user_name']}' "; // 有3份投资
        $touZiShu = getOne($sql);
        if ($touZiShu == 3) {
            // 增加一层订单数
            $sql = "update xbmall_users set dian_pu_shu1=dian_pu_shu1+1   where user_name='{$tuiJianRen['user_name']}'";
            mysql_query($sql);
            $tuiJianRen['dian_pu_shu1'] = $tuiJianRen['dian_pu_shu1'] + 1;
            $zg_userName = getXM($user['user_name']);
            $sql = "insert into zt_xxtz_sj(user,zg_user,rq,zg_level,user_level) values('{$tuiJianRen['user_name']}','{$user['user_name']}','{$nowDayStr}',1,{$tuiJianRen['level']})";
            mysql_query($sql); // 插入今天满足3单的用户信息
            
            $sql = "insert into zt_xxtzzs (user,zsrq,comment) values('{$tuiJianRen['user_name']}','{$nowDateStr}','({$zg_userName})成为你的一层有效商家,有效商家个数{$tuiJianRen['dian_pu_shu1']}')";
            mysql_query($sql);
            
            
            
            $stop_max = getWebConfig("stop_max"); // 静态多少份止局
            if($tuiJianRen['yi_tou_zi_ci_shu']>=$stop_max){//已投资次数大于等于止局数
                $sql="select num from zt_xxtz_zhi_ju where user='{$tuiJianRen['user_name']}'";
                $num = getOne($sql);
                if($num==null){
                    $sql="insert into zt_xxtz_zhi_ju (user,num) values('{$tuiJianRen['user_name']}',1)";
                    $num=1;
                }else{
                    $sql="update zt_xxtz_zhi_ju set num=num+1  where user='{$tuiJianRen['user_name']}'";
                    $num = $num+1;
                }
                mysql_query($sql);
                if($num%3==0){//每满3个
                    $sql = "update  xbmall_users set ke_tou_zi_ci_shu=ke_tou_zi_ci_shu+50  where user_name='{$tuiJianRen['user_name']}'";
                    mysql_query($sql);
                    $sql = "insert into zt_xxtzzs (user,zsrq,comment) values('{$tuiJianRen['user_name']}','{$nowDateStr}','您达到止局数{$stop_max},推荐满3个会员第".($num/3)."次,增加了50个可用投资数')";
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
        // if(empty($userXM)){
        // var_dump($xxtz);
        // }
        
        if ($guanLiFei > 0) {
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
    
    $sql = "INSERT INTO  zt_b_exchange_record(`id`,`date`,`jf`,`sf`,`user`,`url`)VALUES(NULL,'$timeStr','$xxtzzxfjz','忽略','{$user}','系统发放推荐奖聚珠');";
    // var_dump($sql."<br>");
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
    // echo "多层{$sql}\r\n";
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
        
        // faFangUserJiFen($user, $guanLiFei, $message);
        // $sql = "update xbmall_users set tui_jian_shou_yi=tui_jian_shou_yi+{$guanLiFei} where user_name='{$tuiJianRen['user_name']}'"; // 发放奖励
        // mysql_query($sql); // 发放第一层的奖励
        
        $shenFen = getShenFen($tuiJianRen['level']);
        $userXM = getXM($xxtz['user']);
        if ($guanLiFei > 0) {
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
 * @param unknown $message
 * @param unknown $money
 */
function logMoney($message, $money)
{
    global $nowDateStr,$operateUser;
    
    $sql = "select * from xbmall_users where user_name='{$operateUser}'";
    $user = getRow($sql);
    $zhangHuJinE = $user['xxtzztxjf'] + $user['tui_jian_shou_yi'] + $user['dian_pu_zi_jin']; // 可用的总金额
    
    if (floor($money / 100) * 100 > floor($zhangHuJinE / 100) * 100) {
        alertAndRelocationHistory("您的可用金额为:{$zhangHuJinE}");
    }
    
    $jianDianPuJinE = $money >= $user['dian_pu_zi_jin'] ? $user['dian_pu_zi_jin'] : $money; // 要减的赠送收益
    $stepMoney = $money - $jianDianPuJinE; // 还需要减多少
    if ($stepMoney > 0) { // 订单资金不够
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
    mysql_query($sql);
    // 插入积分变动记录， 返回用户的积分
    $sql = "insert into zt_xxtzzs (user,dian_pu_zi_jin,xxtzztxjf,tui_jian_shou_yi,zsrq,comment) values('{$user['user_name']}',-{$jianDianPuJinE},-{$jianZengSongJinE},-{$jianTuiJianShouYi},'{$nowDateStr}','{$message},减去订单资金{$jianDianPuJinE},减去投资收益{$jianZengSongJinE},减去推荐收益{$jianTuiJianShouYi}')";
    mysql_query($sql);
}

function log3600($message)
{
    logMoney($message, 3600);
}

function kaiDian()
{
    global $nowDateStr, $tjrq,$operateUser;
    $next_ft_time = $tjrq + 3600 * 24 * 45;
      
    
    $dianPuUser = $_REQUEST['dianPuUser']; // 要开通订单的人
    
    $sql = "select  is_dian_pu  from  xbmall_users where user_name='{$operateUser}'";
    $is_dian_pu = getOne($sql);
    if ($is_dian_pu == 1 && $dianPuUser == null) {
        alertAndRelocation("您已有订单, 无需再开通", "xxtz_dp.html");
    }
    
    $operateUserInfo = getUser($operateUser);
    
    if (isset($dianPuUser)) { // 给别人开通
        $userInfo = getUser($operateUser);
    } else { // 自己开通
        $userInfo = getUser($operateUserInfo['recommend_user']); // 收益人是推荐人的推荐人
        $dianPuUser = $operateUser;
    }
    
    $tuiJianRen = $userInfo['user_name']; // 订单的推荐人
    
    log3600("为{$operateUser}开通订单");
    
    // 设置订单开通了
    $sql = "update xbmall_users set is_dian_pu=1  where user_name='{$dianPuUser}' ";
    mysql_query($sql);
    
    $sql = "select * from zt_xxtz where user='{$tuiJianRen}' and cj_state=0  and ft_state!=1    order  by id asc  limit 1 ";
    $chuShiTouZi = getRow($sql);
    
    if ($chuShiTouZi) { // 如果有初始投资, 那么就把推荐人的初始投资改为复投
        // 开通订单, 付款人是被推荐人, 受益人是推荐人.
        $sql = "insert into zt_xxtz(parent_id,fk_user,user,tjrq,step_start_rq,next_ft_time,type,czje,step,cj_state)values({$chuShiTouZi['id']},'{$dianPuUser}','{$tuiJianRen}' ,{$tjrq},{$tjrq},{$next_ft_time},2,3600,1,0)";
        mysql_query($sql);
        $newId = mysql_insert_id();
        $sql = "update  zt_xxtz set child_id={$newId} ,ft_state=1  where id={$chuShiTouZi['id']}";
        mysql_query($sql);
    } else {
        // 开通订单, fk_user是订单主人, 受益人是推荐人.
        $sql = "insert into zt_xxtz(fk_user,user,tjrq,step_start_rq,next_ft_time,type,czje,step,cj_state)values('{$dianPuUser}','{$tuiJianRen}' ,{$tjrq},{$tjrq},{$next_ft_time},2,3600,1,0)";
        mysql_query($sql);
        $newId = mysql_insert_id();
    }
    
    $sql = "select * from zt_xxtz where id = {$newId}";
    $xxtz = getRow($sql);
    
    jiangLi($xxtz, $userInfo);
    
    if (isset($_REQUEST['dianPuUser'])) {
        echo "开通成功";
        die();
    } else {
        alertAndRelocation("开通成功", "/mobile/user.php?act=li_cai");
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
            mysql_query($sql);
        }
        return $user;
}

/**
 * 今天最多可以投多少笔
 *
 * @param unknown $fenShu
 */
function todayInvestC($operateUser)
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

// header("Location: xxcz_list.html");

?>
  			  