<?php
 

// 超级会员提现
error_reporting(0);
 
date_default_timezone_set("Asia/Shanghai");
include_once "../myphplib/db.php";
include_once "../myphplib/message.php";
session_start();

 
 
$shengJiArr=  array(
    
    array( 'user'=>'jy18155099968' ,'money'=>'10000000', 'tjrq'=>'2017-11-20 09:09:09')//朱家军
    
  );
 


foreach ($shengJiArr as $key =>$value){
    $time = time();
    $date = date("Y-m-d H:i:s", $time);
    
    $url = "admin";
    $sql3 = "select * from zt_bind_bank where ssyh='$user'  order by bind desc";
    $bank = getRow($sql3);
    if($bank){
        // 插入提现记录
        $sql4 = "INSERT INTO  zt_xxtz_ti_xian( `date`,`je`,`user`,`url`,`khh`,`yhkh`,`zh`)VALUES( '{$value['tjrq']}','{$value['money']}','{$value['user']}','$url','{$bank["khh"]}','{$bank["yhkh"]}','{$bank["zhihang"]}');";
    }else{
        // 插入提现记录
        $sql4 = "INSERT INTO  zt_xxtz_ti_xian( `date`,`je`,`user`,`url`)VALUES( '{$value['tjrq']}','{$value['money']}','{$value['user']}','$url');";
    }
    mysql_query($sql4);
    
    tiXian($user, $value['money']);
  
    
} 





print("<script language='javascript'>alert('提现提交完成！我们在48小时内处理！');</script>");
 
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
        success("{$userAcc}您要提现的金额超额了, 账户现在可以提现{$zhangHuJinE}");
    }
    $jianZengSongJinE =  $money>$user['xxtzztxjf']?$user['xxtzztxjf']:$money;//要减的赠送收益
    $jianTuiJianShouYi = $money>$user['xxtzztxjf']?$zhangHuJinE-$money:0;//要减的推荐收益
   
    $sql = "update xbmall_users set xxtzztxjf=xxtzztxjf-{$jianZengSongJinE} ,tui_jian_shou_yi=tui_jian_shou_yi-{$jianTuiJianShouYi} where  user='{$user['user_name']}'";
    mysql_query($sql);
    // 插入积分变动记录， 返回用户的积分
    $sql = "insert into zt_xxtzzs (user,xxtzztxjf,zsrq,comment) values('{$user['user_name']}',-{$money},'{$date}','用户提现, 减去投资收益{$jianZengSongJinE},减去推荐收益{$jianTuiJianShouYi}')";
    mysql_query($sql);
}

function success($message)
{
    echo json_encode(array(
        'result' => 0,
        'message' => "{$message}"
    ));
    die();
}


 

?>