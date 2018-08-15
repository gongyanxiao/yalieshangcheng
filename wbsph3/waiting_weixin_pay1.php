<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
if ($_REQUEST['step'] == 'waiting_weixin_pay'){

   	$order_id=intval($_GET['log_id']);
   file_put_contents('a.txt', $order_id.date('Y-m-d H:i:s'));
   	$sql="SELECT is_paid FROM ". $GLOBALS['ecs']->table('pay_log')." WHERE log_id=". $order_id;
	$pay_status=$GLOBALS['db']->getOne($sql);

	if($pay_status*1 == 1){
	   	echo "1";
	} else{
	  	echo "2";
	}
}