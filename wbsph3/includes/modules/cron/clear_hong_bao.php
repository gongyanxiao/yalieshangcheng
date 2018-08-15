<?php
var_dump("ni");
/**
 * 定时删除红包奖励
 */
if (! defined ( 'IN_ECS' )) {
	die ( 'Hacking attempt' );
}


set_time_limit ( 0 );
$clear_hong_bao = ROOT_PATH . 'languages/' . $GLOBALS ['_CFG'] ['lang'] . '/cron/clear_hong_bao.php';
if (file_exists ( $clear_hong_bao )) {
	global $_LANG;
	include_once ($clear_hong_bao);
}


/* 模块的基本信息 */
if (isset ( $set_modules ) && $set_modules == TRUE) {
	$i = isset ( $modules ) ? count ( $modules ) : 0;
	
	/* 代码 */
	$modules [$i] ['code'] = basename ( __FILE__, '.php' );
	
	/* 描述对应的语言项 */
	$modules [$i] ['desc'] = 'clear_hong_bao_desc';
	
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
write_hong_bao_log( "当前时间：" . local_date ( "Y-m-d H:i:s", $time ));
ignore_user_abort ( true );
$hours = date("H")+8;
if (!($hours>=15 && $hours<17)){
	write_hong_bao_log( "现在是'$hours'点,还没到执行时间");
	exit("现在是'$hours'点,还没到执行时间") ;
	
}
write_hong_bao_log( "当前删除红包任务执行时间：" . local_date ( "Y-m-d H:i:s", $time ));

 $sql = "delete from  " . $GLOBALS['ecs']->table("bonus_points") ;//删除所有红包
 $GLOBALS['db']->query($sql);
 
function write_hong_bao_log($logMessage) {
	$dataLog = ROOT_PATH . "/data/log/" . local_date("Y-m-d") . "hong_bao.log";
	error_log("当前时间：" . local_date("Y-m-d H:i:s", time()) . ",内容:" . $logMessage . "\r\n", 3, $dataLog);
}

?>