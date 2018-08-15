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


//不能提现的人员名单
$bu_ti_xianArr = array('13356999836',
    '18754913456',
    '15949880912',
    '13455548189',
    '18705358297',
    '15162751528',
    '13813781110',
    '18653517856',
    '15653545558',
    '18865661666',
    '15688532068',
    '13355452719',
    '18612539198',
    '15953536212',
    '13645358178',
    '18615351656',
    '15966454866',
    '18765067170',
    '15954520926',
    '13356999001',
    '13356999002',
    '13356999003',
    '13356999004',
    '13356999005',
    '13356999007',
    '15192251975',
    '18053587982',
    '15098508029',
    '13918745349',
    '15653586338',
    '15192250031',
    '15133678779',
    '18701779905',
    '18121270307',
    '13361370955',
    '18253511343',
    '13012592708',
    '13697644313',
    '18753573364',
    '13573515707',
    '13156917311',
    '18660008198',
    '13285359732',
    '13356999008' 
    
    
);
    


// 超级会员提现
error_reporting(0);
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL;
$host = $_SERVER['HTTP_HOST'];
if (substr($referer, 7, strlen($host)) != $host) {
    echo '非法操作';
    exit();
}
date_default_timezone_set("Asia/Shanghai");

include_once "../myphplib/db.php";
include_once "../myphplib/message.php";
include_once "../config/check.php";
session_start();
$session = htmlspecialchars(trim($_POST['session']));
$type = abs(htmlspecialchars(trim($_POST['type'])));
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

for ($i=0;$i<count($bu_ti_xianArr);$i++){
    if($user==$bu_ti_xianArr[$i]){
        alertAndRelocationHistory('您暂时不能提现,请联系你的推荐人');
    }
}


// if(($_COOKIE["yzm"]<>"$yzm")||$yzm==''||empty($yzm)){
// echo '<script>alert("验证码不正确，请核实!")</script>';
// print("<script language='javascript'>location.reload();</script>");
// exit();
// }

$time = time();//今天
$date = date("Y-m-d H:i:s", $time);
$subDate =substr($date, 0,10);


//要风控的体系
$tiXiUser = "('15966570777','18669655836')";//吴军,赵明凤
$tiXiUserArr = array();
$query1 = "select *  from xbmall_users where  user_name in {$tiXiUser} ";

$result = mysql_query($query1, $db);
while ($row = mysql_fetch_array($result)) {
    $tiXiUserArr[] = $row;
    getTiXi($row['user_id'],$tiXiUserArr);
}

//单独控制某个人
$query1 = "select *  from xbmall_users where  user_name in ('13173078555',
'18754913456' ) ";//邵明凤, 徐振兴,刘金光
$result = mysql_query($query1, $db);
while ($row = mysql_fetch_array($result)) {
    $tiXiUserArr[] = $row;
}





if($type==1){//动态收益提现
    $sql="select * from  zt_xxtz_ti_xian where  user='{$user}' and type=1 and date like '%{$subDate}%'";
    $is_ti_xian = getRow($sql);
    if($is_ti_xian!=null){
        alertAndRelocationHistory('今天已经提过推荐收益了,请明天再进行推荐收益提现');
    }
    
}else if($type==0){
    
    $sql="select * from  zt_xxtz_ti_xian where user='{$user}' and type=0 and date like '%{$subDate}%'";
    $is_ti_xian = getRow($sql);
    if($is_ti_xian!=null){
        alertAndRelocationHistory('今天已经提过A阶段订单收益了,请明天再进行订单收益提现');
    }
    
    
    $jing_tai_ke_ti_xian =  getJingTaiTiXianJinE($user);
    if($jing_tai_ke_ti_xian!=-1   ){
        if($money>$jing_tai_ke_ti_xian){
            alertAndRelocationHistory('友情提示:您剩余兑现额度为'.$jing_tai_ke_ti_xian.'， 您的铺货订单产生的收益需要推荐有效合伙人才可以兑现，推荐一个有效合伙人兑现收益额度为10800，以此类推！');
        }
    }
    
   
    if($jing_tai_ke_ti_xian!==-1  ){//修改用户的静态可提醒金额
        if($jing_tai_ke_ti_xian>=$money){
            $sql="update xbmall_users set jing_tai_ke_ti_xian=".($jing_tai_ke_ti_xian-$money)." where user_name='{$user}'";
            mysql_query($sql);
        }else{
            alertAndRelocationHistory('友情提示:您剩余兑现额度为'.$jing_tai_ke_ti_xian.'， 您的铺货订单产生的收益需要推荐有效合伙人才可以兑现，推荐一个有效合伙人兑现收益额度为10800，以此类推！');
        }
    }
    
    
   
   
}else if($type==2){
    
    $sql="select * from  zt_xxtz_ti_xian where user='{$user}' and type=2 and date like '%{$subDate}%'";
    $is_ti_xian = getRow($sql);
    if($is_ti_xian!=null){
        alertAndRelocationHistory('今天已经提过B阶段订单收益了,请明天再进行订单收益提现');
    }
    
    
    $jing_tai_ke_ti_xian =  getJingTaiTiXianJinE($user);
//     if($jing_tai_ke_ti_xian!=-1   ){
//         if($money>$jing_tai_ke_ti_xian){
//             alertAndRelocationHistory('友情提示:您剩余兑现额度为'.$jing_tai_ke_ti_xian.'， 您的铺货订单产生的收益需要推荐有效合伙人才可以兑现，推荐一个有效合伙人兑现收益额度为10800，以此类推！');
//         }
//     }
    
    if($jing_tai_ke_ti_xian!==-1  ){//修改用户的静态可提醒金额
        if($jing_tai_ke_ti_xian>=$money){
            $sql="update xbmall_users set jing_tai_ke_ti_xian=".($jing_tai_ke_ti_xian-$money)." where user_name='{$user}'";
            mysql_query($sql);
        }else{
            alertAndRelocationHistory('友情提示:您剩余兑现额度为'.$jing_tai_ke_ti_xian.'， 您的铺货订单产生的收益需要推荐有效合伙人才可以兑现，推荐一个有效合伙人兑现收益额度为10800，以此类推！');
        }
    }else{
        
    }
    
    
   
    
}



if (! is_numeric($money) || empty($money)) {
    alertAndRelocationHistory('提现金额必须为数字格式!');
}


if ($type==0 && $money < 500) {//A阶段
    alertAndRelocationHistory('A阶段提现金额最低500元!');
}
if ($type==2 && $money < 500) {//b阶段
    alertAndRelocationHistory('B阶段提现金额最低500元!');
}


if (! is_int($money / 100)) {
    alertAndRelocationHistory('您提现的金额必须是100的倍数!');
}

 



// 绑定银行卡判断
$sql3 = "select * from xbmall_users where user_name='$user' ";
$userInfo = getRow($sql3);
if ($userInfo['bank'] == "") {
    alertAndRelocation('您还没有绑定银行卡!', '/mobile/user.php?act=profile');
}


$url = get_url();
 


tiXian($user, $money,$type);

// 插入提现记录
$sql4 = "INSERT INTO  zt_xxtz_ti_xian( `date`,`je`,`user`,`url`,`khh`,`yhkh`,`zh`,type)VALUES( '$date','{$money}','{$user}','$url','{$userInfo["bank_kh"]}','{$userInfo['bank']}','',{$type});";
mysql_query($sql4);


print("<script language='javascript'>alert('提现提交完成！我们在48小时内处理！');</script>");
print("<script language='javascript'>window.location.href='money_cj_list.html';</script>");
exit();





/**
 * 获取体系下所有人
 * @param unknown $rootId
 */
function   getTiXi($rootId,&$resultArr){
    $sql="select * from  xbmall_users where parent_id=".$rootId;
    $result = mysql_query($sql);
    if(!isset($result)){
        return ;
    }
    while ($row = mysql_fetch_array($result)) {
        if($row){
            $resultArr[] = $row;
            getTiXi($row['user_id'],$resultArr);
        }
    }
}



/**
 * 是否是体系用户
 * @param unknown $userName
 */
function  isTiXiUser($userName){
    global  $tiXiUserArr;
    
     //alertAndRelocationHistory("{$user}");
    if($tiXiUserArr==null) return  false;
    
    foreach ($tiXiUserArr AS $key => $val)
    {
        
        if($val['user_name']==$userName)
            return true; //在体系内
    }
    
    //如果单数超过了100单, 也是需要风控的
    $sql="select count(0) from zt_xxtz where user='{$userName}'";
    $num = getOne($sql);
    if($num >=100){
        return true; 
    }
    return false; //不在体系内
}


/**
 * 获取用户静态可以提现的金额
 * @param unknown $user
 */
function  getJingTaiTiXianJinE($user){
   
    if(!isTiXiUser($user)){//不是需要风控的体系用户
        return -1;
    }
    global  $time;
    
    //  返回用户的可提现金额, 如果是-1, 说明用户可以随便提现
    $sql="select jing_tai_ke_ti_xian from xbmall_users where user_name='{$user}'";
   
    $jing_tai_ke_ti_xian =  getOne($sql);
    if($jing_tai_ke_ti_xian==-1){//没有设置提现额度,查看提现总额度, 充值总额度
        $sql="select sum(czje) from zt_xxtz_cz where user='".$user."' and js_sh_state=1 and cw_sh_state=1 ";
        $chong_zhi = getOne($sql);
        $sql="select sum(je) from zt_xxtz_ti_xian where user='".$user."' and jssh!=2 and cwsh!=2 ";
        $ti_xian = getOne($sql);
        if($chong_zhi<=$ti_xian){//充值小于等于提现
            $sql="update  xbmall_users set jing_tai_ke_ti_xian=0,man_dan_ri_qi=".$time." where user_name='{$user}'";
            mysql_query($sql);
            return 0;
        }
        return  $chong_zhi-$ti_xian;
    }
    
    return  $jing_tai_ke_ti_xian;
}
/**
 * 提现
 * @param unknown $liYou 理由
 */
function  tiXian($userAcc,$money,$type){
    global  $date;
    $sql="select * from xbmall_users where user_name='{$userAcc}'";
    $user =  getRow($sql);
    if($type==0){//静态收益
    	$zhangHuJinE =$user['xxtzztxjf'];
    }else if($type==1){
    	$zhangHuJinE =$user['tui_jian_shou_yi'];
    }
    else if($type==2){
        $zhangHuJinE =$user['xxtzztxjf_b'];
    }
    
    
    if ($money >$zhangHuJinE) {
    	if($type==0){//静态收益
    		alertAndRelocationHistory("您要提现的金额超额了, 账户A阶段静态收益可以提现{$zhangHuJinE}");
    	}else if($type==1){
    	    alertAndRelocationHistory("您要提现的金额超额了, 账户推荐收益可以提现{$zhangHuJinE}");
    	}else if($type==2){
    	    alertAndRelocationHistory("您要提现的金额超额了, 账户B阶段静态收益可以提现{$zhangHuJinE}");
    	}
        
    }
 
    if($type==0){//静态收益
        
    	$sql = "update xbmall_users set xxtzztxjf=xxtzztxjf-{$money}  where  user_name='{$user['user_name']}'";
    	mysql_query($sql);
    	// 插入积分变动记录， 返回用户的积分
    	$sql = "insert into zt_xxtzzs (user,xxtzztxjf,zsrq,comment) values('{$user['user_name']}',-{$money},'{$date}','用户提现, 减去A阶段投资收益{$money}')";
    	mysql_query($sql);
    }else if($type==2){//静态收益
        $sql = "update xbmall_users set xxtzztxjf_b=xxtzztxjf_b-{$money}  where  user_name='{$user['user_name']}'";
        mysql_query($sql);
        // 插入积分变动记录， 返回用户的积分
        $sql = "insert into zt_xxtzzs (user,xxtzztxjf_b,zsrq,comment) values('{$user['user_name']}',-{$money},'{$date}','用户提现, 减去B阶段投资收益{$money}')";
        mysql_query($sql);
    }else if($type==1){
    	$sql = "update xbmall_users set tui_jian_shou_yi=tui_jian_shou_yi-{$money} where  user_name='{$user['user_name']}'";
    	mysql_query($sql);
    	// 插入积分变动记录， 返回用户的积分
    	$sql = "insert into zt_xxtzzs (user,xxtzztxjf,zsrq,comment) values('{$user['user_name']}',-{$money},'{$date}','用户提现,减去推荐收益{$money}')";
    	mysql_query($sql);
    }
     
  
}


function get_url()
{
    $ip = getIP();
    $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'] . '<br><font color="red">操作IP:' . $ip . '</font>';
    return $url;
}

?>