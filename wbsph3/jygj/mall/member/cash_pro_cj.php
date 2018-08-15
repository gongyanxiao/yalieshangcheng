<?php

function getIP()
{
    if (isset($_SERVER)) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $realip = $_SERVER['REMOTE_ADDR'];
        }
    } else {
        if (getenv("HTTP_X_FORWARDED_FOR")) {
            $realip = getenv("HTTP_X_FORWARDED_FOR");
        } elseif (getenv("HTTP_CLIENT_IP")) {
            $realip = getenv("HTTP_CLIENT_IP");
        } else {
            $realip = getenv("REMOTE_ADDR");
        }
    }
    return $realip;
}

// 超级会员提现
error_reporting(0);
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL;
$host = $_SERVER['HTTP_HOST'];
if (substr($referer, 7, strlen($host)) != $host) {
    echo '非法操作';
    exit();
}
date_default_timezone_set("Asia/Shanghai");
include_once "../config/check.php";
include_once "../myphplib/db.php";
include_once "../myphplib/message.php";
session_start();
$session = htmlspecialchars(trim($_POST['session']));

$money = abs(htmlspecialchars(trim($_POST['money'])));
$yzm = htmlspecialchars(trim($_POST['yzm']));
$user = $_COOKIE['ECS']['username'];
if (($session !== $_SESSION['uniqid'])) {
    echo '<script>alert("请不要重复点击!")</script>';
    print("<script language='javascript'>location.reload();</script>");
    exit();
} else {
    unset($_SESSION['uniqid']);
}

// if(($_COOKIE["yzm"]<>"$yzm")||$yzm==''||empty($yzm)){
// echo '<script>alert("验证码不正确，请核实!")</script>';
// print("<script language='javascript'>location.reload();</script>");
// exit();
// }

if (! is_numeric($money) || empty($money)) {
    alertAndRelocationHistory('提现金额必须为数字格式!');
}
if (! is_int($money / 100)) {
    alertAndRelocationHistory('您提现的金额必须是100的倍数!');
}
if ($money < 500) {
    alertAndRelocationHistory('提现金额最低500元!');
}




// 绑定银行卡判断
$sql3 = "select * from zt_bind_bank where ssyh='$user' order by id desc";
$num3 = getRow($sql3);
if ($num3 < "1") {
    alertAndRelocation('您还没有绑定银行卡!', 'card.html');
}

$time = time();
$date = date("Y-m-d H:i:s", $time);

$url = get_url();
$sql3 = "select * from zt_bind_bank where ssyh='$user'  order by bind desc";
$bank = getRow($sql3);



tiXian($user, $money);
// 插入提现记录
$sql4 = "INSERT INTO  zt_xxtz_ti_xian( `date`,`je`,`user`,`url`,`khh`,`yhkh`,`zh`)VALUES( '$date','{$money}','{$user}','$url','{$bank["khh"]}','{$bank["yhkh"]}','{$bank["zhihang"]}');";
mysql_query($sql4);



print("<script language='javascript'>alert('提现提交完成！我们在48小时内处理！');</script>");
print("<script language='javascript'>window.location.href='money_cj_list.html';</script>");
exit();

/**
 * 提现
 * @param unknown $liYou 理由
 */
function  tiXian($userAcc,$money){
    global  $date;
    $sql="select * from xbmall_users where user_name='{$userAcc}'";
    $user =  getRow($sql);
    
   
    $zhangHuJinE =$user['xxtzztxjf']+$user['tui_jian_shou_yi'];//可提现的总金额
   
    if (floor($money / 100) * 100 > floor($zhangHuJinE / 100) * 100) {
        alertAndRelocationHistory("您要提现的金额超额了, 账户现在可以提现{$zhangHuJinE}");
    }
    $jianZengSongJinE =  $money>$user['xxtzztxjf']?$user['xxtzztxjf']:$money;//要减的赠送收益
    $jianTuiJianShouYi = $money>$user['xxtzztxjf']?$zhangHuJinE-$money:0;//要减的推荐收益
   
    $sql = "update xbmall_users set xxtzztxjf=xxtzztxjf-{$jianZengSongJinE} ,tui_jian_shou_yi=tui_jian_shou_yi-{$jianTuiJianShouYi} where  user='{$user['user_name']}'";
    mysql_query($sql);
    // 插入积分变动记录， 返回用户的积分
    $sql = "insert into zt_xxtzzs (user,xxtzztxjf,zsrq,comment) values('{$user['user_name']}',-{$money},'{$date}','用户提现, 减去投资收益{$jianZengSongJinE},减去推荐收益{$jianTuiJianShouYi}')";
    mysql_query($sql);
}


function get_url()
{
    $ip = getIP();
    $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'] . '<br><font color="red">操作IP:' . $ip . '</font>';
    return $url;
}

?>