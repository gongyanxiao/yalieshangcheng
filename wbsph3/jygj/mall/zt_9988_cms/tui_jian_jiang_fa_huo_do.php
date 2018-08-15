<?php

include "../myphplib/db.php";
include "../myphplib/message.php";
include "config/check.php";
include "../myphplib/page.php";

    $user = $_GET ['user'];
//     include ('../../../includes/cls_json.php');
    $tjrq =time();
    $sql = "update xbmall_users set  fa_huo_ri_qi=" . $tjrq."    where user_name='".$user."'";
    
    $res = mysql_query ( $sql );
    
//     $nowDateStr = date("Y-m-d H:i:s", $tjrq);
//     //  给会员赠送3600的消费积分
//     $sql = "insert into zt_xxtzzs (user,xxtzzxfjz,zsrq,comment,type) values('{$user}',-3600,'{$nowDateStr}','大礼包发货,消费云贝-3600',4)";
//     mysql_query($sql);
    
    
    die ( "发货成功" );
 
 ?>