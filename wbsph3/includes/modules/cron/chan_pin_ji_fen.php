<?php

/**
 * 定时发放产品积分
 */
if (! defined ( 'IN_ECS' )) {
	die ( 'Hacking attempt' );
}

set_time_limit ( 0 );

/* 模块的基本信息 */
if (isset ( $set_modules ) && $set_modules == TRUE) {
	$i = isset ( $modules ) ? count ( $modules ) : 0;
	
	/* 代码 */
	$modules [$i] ['code'] = basename ( __FILE__, '.php' );
	
	/* 描述对应的语言项 */
	$modules [$i] ['desc'] = 'chan_pin_ji_fen_desc';
	
	/* 作者 */
	$modules [$i] ['author'] = 'author';
	
	/* 网址 */
	$modules [$i] ['website'] = 'http://www.baidu.com';
	
	/* 版本号 */
	$modules [$i] ['version'] = '1.0.0';
	
	/* 配置信息 */
	$modules [$i] ['config'] = array ();
	
	return;
}

$time = gmtime ();
write_chan_pin_ji_fen_log ( "当前时间：" . local_date ( "Y-m-d H:i:s", $time ) );
ignore_user_abort ( true );
$hours = date ( "H",$time) + 8;
 
if ( $hours !=8 ) { // 7点执行都可以执行一次
	write_chan_pin_ji_fen_log ( "现在是".$hours."点,还没到执行时间" );
	exit ( "现在是".$hours."点,还没到执行时间" );
}

write_chan_pin_ji_fen_log ( "当前积分分配任务执行时间：" . local_date ( "Y-m-d H:i:s", $time ) );

$sql = "SELECT  a.id, a.user_id, a.monthDiff,a.product_type,a.points from (" . " select id, user_id,product_type,points, TIMESTAMPDIFF(MONTH,  date_format(FROM_UNIXTIME(send_time),'%Y-%m-%d %H:%i'),DATE_FORMAT(NOW(),'%Y-%m-%d %H:%i:%s')) as  monthDiff" . " from " . $GLOBALS ['ecs']->table ( 'product_points_recharge' ) . ")a where  a.points>0 and a.monthDiff>0 "; // 找出发放时间已经超过一个月的

$res = $GLOBALS ['db']->selectLimit ( $sql, 1000000, 0 );

if ($res) {//有需要分配积分的
	
	while ( $rows = $GLOBALS ['db']->fetchRow ( $res ) ) {
		
		if ($rows ['product_type'] < 10000) { // 每次发放1000积分
			$jiFenShu = 1000;
		} else {
			$jiFenShu = 2000;
		}
		
		// 修改账户信息
		$sql = "update " . $GLOBALS ['ecs']->table ( 'users' ) . " set cp_points = IFNULL(cp_points,0)+" . $jiFenShu . " where user_id=" . $rows ['user_id'];
// 		var_dump ( $sql );
		$GLOBALS ['db']->query ( $sql );
		// 修改已经发放的积分信息,和发放时间
		$sql = "update " . $GLOBALS ['ecs']->table ( 'product_points_recharge' ) . " set points = points-" . $jiFenShu .",send_time=".$time." where id=" . $rows ['id'];
		$GLOBALS ['db']->query ( $sql );
// 		var_dump ( $sql );
		
		// 插入发放记录
		$sql = "insert into " . $GLOBALS ['ecs']->table ( 'product_points_log' ) . " (user_id,points,log_time,note)values(" . $rows ['user_id'] . "," . $jiFenShu . "," . $time . ",'发放产品报单积分" . $jiFenShu . "')";
		$GLOBALS ['db']->query ( $sql );
// 		var_dump ( $sql );
		
		
		
	}
	exit ( "执行完毕" );
}else{
	exit ( "执行完毕, 没有可发放的产品积分" );
}
function write_chan_pin_ji_fen_log($logMessage) {
	$dataLog = ROOT_PATH . "/data/log/" . local_date ( "Y-m-d" ) . "chan_pin_ji_fen.log";
	error_log ( "当前时间：" . local_date ( "Y-m-d H:i:s", time () ) . ",内容:" . $logMessage . "\r\n", 3, $dataLog );
}

?>