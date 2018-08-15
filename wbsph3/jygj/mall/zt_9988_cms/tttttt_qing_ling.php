<?php
include "../myphplib/init.php";
include "../myphplib/db.php";


$notClearUsers =  $GLOBALS['notClearUsers'];

/**
 * 删除所有投资
 * 
 * @var string $sql
 */
$sql = "delete from  zt_xxtz where user not in {$notClearUsers}";
mysql_query($sql);

/**
 * 初始化用户的各项数据
 */
$sql = "update  xbmall_users set dian_pu_shu1 = 0, dian_pu_shu2=0,is_dian_pu=0,tui_jian_shou_yi=0,
level=0,yi_tou_zi_ci_shu=0,ke_tou_zi_ci_shu=150,ye_ji=0,xxtzztxjf=0,xxtzzxfjz=0,xxtzzsyjz=0,xxtzzcbjz=0,
xxtzzfwf=0,xfjf=0 ,vitual_dian_pu_shu1=0,dian_pu_zi_jin=0,vitual_dian_pu_shu2=0,vitual_ye_ji=0 ,pay_points=0 where user_name not in {$notClearUsers}";
mysql_query($sql);

/**
 * 删除充值记录
 */
$sql = "delete from  zt_xxtz_cz  where user not in {$notClearUsers}";
mysql_query($sql);

/**
 * 删除赠送记录
 */
$sql = "delete from  zt_xxtzzs where user not in {$notClearUsers}";
mysql_query($sql);

/**
 * 删除提现记录
 */
$sql = "delete from  zt_xxtz_ti_xian where user not in {$notClearUsers}";
mysql_query($sql);

$sql = "delete from  zt_xxtz_sj  where user not in {$notClearUsers}";
mysql_query($sql);

/**
 * 删除发放记录
 */
$sql = "delete from  zt_xxtz_ffjl";
mysql_query($sql);

die("数据初始化完成");
?>
  