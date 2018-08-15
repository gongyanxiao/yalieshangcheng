<?php 
include "../myphplib/db.php";
 
/**
 * 删除所有投资
 * @var string $sql
 */
$sql="delete from  zt_xxtz ";
mysql_query($sql);


/**
 * 初始化用户的各项数据
 */
$sql="update  xbmall_users set dian_pu_shu1 = 0, dian_pu_shu2=0,is_dian_pu=0,tui_jian_shou_yi=0,
level=0,yi_tou_zi_ci_shu=0,ke_tou_zi_ci_shu=100,ye_ji=0,xxtzztxjf=0,xxtzzxfjz=0,xxtzzsyjz=0,xxtzzcbjz=0,
xxtzzfwf=0,xfjf=0  where  user like  '%jy%'";
mysql_query($sql);


/**
 * 恢复用户的充值金额
 */
$sql="SELECT user, SUM(czje) as dian_pu_zi_jin FROM  zt_xxtz_cz  where js_sh_state=1 and cw_sh_state=1    GROUP BY  `user`";
$result = mysql_query($sql);
while ( $data  = mysql_fetch_array ( $result ) ) {
    $sql="update  xbmall_users set dian_pu_zi_jin ={$data['dian_pu_zi_jin']}  where user='{$data['user']}'";
    mysql_query($sql);
}


/**
 * 删除赠送记录
 */
$sql="delete from  zt_xxtzzs";
mysql_query($sql);



/**
 * 删除提现记录
 */
$sql="delete from  zt_xxtz_ti_xian";
mysql_query($sql);

 
 
 die("okkk");
?>
  