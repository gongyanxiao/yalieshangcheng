<?php
include "../myphplib/init.php";
include "../myphplib/db.php";
include "../myphplib/message.php";
include "config/check.php";
date_default_timezone_set("Asia/Shanghai");
// alertAndRelocationHistory("系统正在录入数据,用户自己的操作稍后进行");



$sql = "select * from  zt_setting ";
$row = mysql_query($sql);

while ($rs = mysql_fetch_assoc($row)) {
    $configs[] = $rs;
}
$GLOBALS["nconfigs"] =$configs;

 
function getWebConfig($name)
{
    $configs = $GLOBALS["nconfigs"];
    
    for ($i = 0; $i < count($configs); $i ++) {
        if ($configs[$i]['name'] == $name) {
            return $configs[$i]['value'];
        }
    }
}



$earlierUserArr=array();
$earlierUserArr[]=array("real_name"=>"朱家君","riQi"=>"2018-04-20");
$earlierUserArr[]=array("real_name"=>"王海娜","riQi"=>"2018-04-20");
$earlierUserArr[]=array("real_name"=>"毛建军","riQi"=>"2018-04-20");
$earlierUserArr[]=array("real_name"=>"许婷","riQi"=>"2018-04-20");
$earlierUserArr[]=array("real_name"=>"刘涛","riQi"=>"2018-04-20");
$earlierUserArr[]=array("real_name"=>"尤洪霞","riQi"=>"2018-04-20");
$earlierUserArr[]=array("real_name"=>"徐振兴","riQi"=>"2018-04-20");
$earlierUserArr[]=array("real_name"=>"周志友","riQi"=>"2018-04-20");
$earlierUserArr[]=array("real_name"=>"胡凡珍","riQi"=>"2018-04-20");
$earlierUserArr[]=array("real_name"=>"褚富霞","riQi"=>"2018-04-20");
$earlierUserArr[]=array("real_name"=>"邵明凤","riQi"=>"2018-04-20");
$earlierUserArr[]=array("real_name"=>"毛一鸣","riQi"=>"2018-04-20");
$earlierUserArr[]=array("real_name"=>"吴军","riQi"=>"2018-04-20");
$earlierUserArr[]=array("real_name"=>"李利","riQi"=>"2018-04-20");
$earlierUserArr[]=array("real_name"=>"周杨","riQi"=>"2018-04-20");
$earlierUserArr[]=array("real_name"=>"邵长存","riQi"=>"2018-04-20");


$earlierUserArr[]=array("real_name"=>"高恒新","riQi"=>"2018-05-03");
$earlierUserArr[]=array("real_name"=>"王洛林","riQi"=>"2018-05-06");
$earlierUserArr[]=array("real_name"=>"张荣香","riQi"=>"2018-05-08");
$earlierUserArr[]=array("real_name"=>"孙希勇","riQi"=>"2018-05-15");
$earlierUserArr[]=array("real_name"=>"刘锡云","riQi"=>"2018-05-11");
$earlierUserArr[]=array("real_name"=>"冉令果","riQi"=>"2018-05-24");
$earlierUserArr[]=array("real_name"=>"尚春荣","riQi"=>"2018-05-25");
$earlierUserArr[]=array("real_name"=>"颜伟晨","riQi"=>"2018-05-25");
$earlierUserArr[]=array("real_name"=>"丁方舟","riQi"=>"2018-05-27");
$earlierUserArr[]=array("real_name"=>"张珊珊","riQi"=>"2018-05-28");
$earlierUserArr[]=array("real_name"=>"席俊达","riQi"=>"2018-05-28");
$earlierUserArr[]=array("real_name"=>"范宗宝","riQi"=>"2018-06-03");
$earlierUserArr[]=array("real_name"=>"王侯雪","riQi"=>"2018-06-10");
$earlierUserArr[]=array("real_name"=>"王秀丽","riQi"=>"2018-06-11");
$earlierUserArr[]=array("real_name"=>"段效辉","riQi"=>"2018-06-01");
$earlierUserArr[]=array("real_name"=>"管士乾","riQi"=>"2018-06-01");
$earlierUserArr[]=array("real_name"=>"刘金光","riQi"=>"2018-06-05");
$earlierUserArr[]=array("real_name"=>"赵炳谦","riQi"=>"2018-06-07");
$earlierUserArr[]=array("real_name"=>"赵明凤","riQi"=>"2018-06-10");





$tjrq = time(); // 提交时间
$nowDateStr = date("Y-m-d", $tjrq);
 
//所有可以调整级别的人
// $sql="select * from xbmall_users where flag=0 ";
// $result=mysql_query($sql);
// $num = mysql_num_rows($result);
// for($i=0;$i<$num;$i++){
//     $data2=mysql_fetch_array($result);
//     tiao_zheng_ji_bie($data2['user_name']);//调整级别
    
// }

for($i=0;$i<count($earlierUserArr);$i++){
        $data2=$earlierUserArr[$i];
        if($data2['riQi']=='2018-04-20'){
            tiao_zheng_ji_bie($data2['real_name']);//调整级别
        }
 }

 

 
/**
 * 根据用户下面合格的人员数及自身的有效单(A阶段), 调整会员的级别
 * 
 * @param unknown $user
 */
function tiao_zheng_ji_bie($real_name)
{
    global $nowDateStr;
    $sql = "select * from xbmall_users where real_name='{$real_name}'";
    $userInfo = getRow($sql);
    $user = $userInfo['user_name'];
    
    
    //自己推荐的店铺数
    $sql = "SELECT  count(0) from  ( SELECT   count(0) from xbmall_users a JOIN   zt_xxtz  b   on a.user_name=b.user  WHERE a.recommend_user='{$user}' and b.cj_state=0   GROUP BY  b.user HAVING count(0)>=3 ) x";
    $dianPuShu = getOne($sql);
   
    // 自己A阶段有效单数
    $sql = "select count(0) from zt_xxtz  where user='{$user}' and step=1 ";
    $you_xiao_dan = getOne($sql);
    $level = getLevelByDianPuShu($userInfo,$dianPuShu, $you_xiao_dan); // 当前应该在的级别
   
    
    if ($userInfo['level'] != $level) {
        var_dump($real_name.":".$dianPuShu.":".$you_xiao_dan.":".$level."<br>")  ;
        
        $sql = "update  xbmall_users  set level={$level} where user_name='{$user}'";
        mysql_query($sql);
        $shenFen = getShenFen($level);
        if ($userInfo['level'] > $level) { // 降级了
            $sql = "insert into zt_xxtzzs (user,zsrq,comment) values('{$user}','{$nowDateStr}','您的A阶段有效单{$you_xiao_dan},直推有效会员{$dianPuShu}个,当前级别调整为{$shenFen},需要加油了')";
        } else { // 升级了
            $sql = "insert into zt_xxtzzs (user,zsrq,comment) values('{$user}','{$nowDateStr}','您的A阶段有效单{$you_xiao_dan},直推有效会员{$dianPuShu}个,当前级别升级为{$shenFen},恭喜恭喜!')";
        }
        mysql_query($sql);
    }else {
        var_dump("合格".$real_name.":".$dianPuShu.":".$you_xiao_dan.":".$level."<br>")  ;
    }
    
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
 * 获取店铺数应该在的级别
 * 
 * @param unknown $dianPuShu
 */
function getLevelByDianPuShu($userInfo,$dianPuShu, $you_xiao_dan)
{
    $chu = getWebConfig("sheng_chu_dai_dian_pu");
    $zhong = getWebConfig("sheng_zhong_dai_dian_pu");
    $gao = getWebConfig("sheng_gao_dai_dian_pu");
    $yunYing = getWebConfig("sheng_yun_ying_dian_pu");
    
    if ($dianPuShu >= $yunYing) {
        $level = getLevelByYouXiaoDan($userInfo,$you_xiao_dan);
        if ($level <= 4)
            return $level;
        return 4;
    }
    
    if ($dianPuShu >= $gao) {
        $level = getLevelByYouXiaoDan($userInfo,$you_xiao_dan);
        if ($level <= 3)
            return $level;
        return 3;
    }
    
    if ($dianPuShu >= $zhong) {
        $level = getLevelByYouXiaoDan($userInfo,$you_xiao_dan);
        if ($level <= 2)
            return $level;
        return 2;
    }
    
    if ($dianPuShu >= $chu) {
        $level = getLevelByYouXiaoDan($userInfo,$you_xiao_dan);
        if ($level <= 1)
            return $level;
        return 1; //
    }
    
    return 0;
}

function getLevelByYouXiaoDan($userInfo,$you_xiao_dan)
{
    if($userInfo['flag']==1)//不检查flag等于1的人的自身danshu
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

 


 

// header("Location: xxcz_list.html");

?>
  			  