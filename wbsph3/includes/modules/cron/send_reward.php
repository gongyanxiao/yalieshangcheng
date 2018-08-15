<?php

/**
 * 定时赠送奖励
 */
if (! defined ( 'IN_ECS' )) {
	die ( 'Hacking attempt' );
}

ignore_user_abort ( true );
set_time_limit ( 0 );
$send_reward = ROOT_PATH . 'languages/' . $GLOBALS ['_CFG'] ['lang'] . '/cron/send_reward.php';
if (file_exists ( $send_reward )) {
	global $_LANG;
	include_once ($send_reward);
}

/* 模块的基本信息 */
if (isset ( $set_modules ) && $set_modules == TRUE) {
	$i = isset ( $modules ) ? count ( $modules ) : 0;
	
	/* 代码 */
	$modules [$i] ['code'] = basename ( __FILE__, '.php' );
	
	/* 描述对应的语言项 */
	$modules [$i] ['desc'] = 'send_reward_desc';
	
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
// $date= local_strtotime(local_date("Y-m-d"));
// 'give_open' => '1',
// 'give_bl' => '5',
// 'give_time' => '17',
$time = gmtime ();
$sql = "select nextime from " . $GLOBALS ['ecs']->table ( "crons" ) . " where cron_code='send_reward'";
$nextime = $GLOBALS ['db']->getOne ( $sql );

write_send_log ( "当前任务执行时间：" . local_date ( "Y-m-d H:i:s", $time ) . ",任务计划开始执行时间：" . local_date ( "Y-m-d H:i:s", $nextime ) );
write_send_log ( "执行这个,任务执行时间：" . local_date ( "Y-m-d H:i:s", $nextime ) );

if ($nextime > $time) {
	write_send_log ( "未到任务开始时间,任务执行时间：" . local_date ( "Y-m-d H:i:s", $nextime ) );
} else {
	write_send_log ( "任务开始执行" );
	
	$givOpenSql = "select value from " . $GLOBALS ['ecs']->table ( "shop_config" ) . " where code ='give_open'"; // ,'give_bl'
	$give_open = $GLOBALS ['db']->getOne ( $givOpenSql );
	$giveBlSql = "select value from " . $GLOBALS ['ecs']->table ( "shop_config" ) . " where code ='give_bl'"; // ,''
	$give_bl = $GLOBALS ['db']->getOne ( $giveBlSql );
	$giveSetPointsSql = "select value from " . $GLOBALS ['ecs']->table ( "shop_config" ) . " where code ='give_set_points'"; // ,''
	$give_set_points = $GLOBALS ['db']->getOne ( $giveSetPointsSql );
	
	$giveSetPointsSql = "select value from " . $GLOBALS ['ecs']->table ( "shop_config" ) . " where code ='zuo_dan_give_set_points'"; // 做单这每个分红权赠送的积分数
	$zuo_dan_give_set_points = $GLOBALS ['db']->getOne ( $giveSetPointsSql );
	
	$dataLog = ROOT_PATH . "/data/log/" . date ( "Y-m-d" ) . ".log";
	if ( empty ( $give_open ) || empty ( $give_bl ) || empty ( $give_set_points )) {
		write_send_log ( "设置无效，不执行任务\r\n" );
	} else {
		if ($give_open * 1 == 1 && $give_set_points * 1 > 0) {
			// 每个会员依次赠送，因为需要判断是否已经赠送完成
			$GLOBALS ['db']->startTrans ();
			
			$usersSql = "SELECT   ben_jin_rate,shou_yi_rate,    mobile_phone,zsq_step,give_points, user_id,history_zsq,zsq,sg_zsq,sg_history_zsq,sg_day_points, day_points,province,city,district from " . $GLOBALS ['ecs']->table ( 'users' ) . " where zsq>0 or sg_zsq>0 FOR UPDATE";
			
			$usersData = $GLOBALS ['db']->getAll ( $usersSql );
			$isok = true;
			$message = "";
			$dateStart = local_strtotime ( local_date ( "Y-m-d" ) );
			$sql = "select count(*) from " . $GLOBALS ['ecs']->table ( "send_log" ) . " where send_date='" . $dateStart . "'";
			$isSend = $GLOBALS ['db']->getOne ( $sql );
// 			$isSend = 0;
			if ($isSend * 1 == 0) { // 今天没有送过
				try {
					
					write_send_log ( "当前赠送积分数值:$give_set_points\r\n", 3 );
					$people_num = 0;
					$totalPayPoints = 0; // 积分赠送总额
					
					foreach ( $usersData as $userKey => $userValue ) {
						
						// if($userValue['mobile_phone']!='15131258393')continue;
						
						// if ($userValue['give_points_step']!=0 && $userValue['give_points_step']>=$userValue['zsq_step']*240)
						// {//已经赠送积分分数 大于可以赠送的积分分数,就不再赠送, 现在消费分红权转为冻结, 可以赠送的赠送权清零, 把当前的赠送权加到手工分红权, 初始化已经赠送的积分
						// $sql="update xbmall_users set sg_zsq=sg_zsq+zsq, sg_history_zsq=sg_history_zsq+zsq, frozen_zsq=zsq,zsq=0, zsq_step=0, give_points_step=0 where user_id=".$userValue['user_id'];
						// $GLOBALS['db']->query($sql);
						// $userValue["sg_zsq"] = $userValue["sg_zsq"]+$userValue["zsq"];
						// $userValue["zsq"]=0;
						// $userValue["zsq_step"]=0;
						// $userValue["give_points_step"]=0;
						// // file_put_contents("c:/test.txt", $sql."\r\n", FILE_APPEND);
						// }
						
						if (isset ( $userValue ['ben_jin_rate'] ) && $userValue ['ben_jin_rate'] != - 1) { // 设置了单个用户的本金发放速度
							$canSend = $userValue ['ben_jin_rate'] * $userValue ["zsq"]; // 本次消费分红权可以赠送的积分数
						} else {
							$canSend = ($zuo_dan_give_set_points * $userValue ["zsq"]); // 本次消费分红权可以赠送的积分数
						}
						
						if($canSend<0){
						    $canSend=0;
						}
						// var_dump($userValue);
						// var_dump("赠送完了{$userValue['ben_jin_rate']}");
						
						$sended = $userValue ["give_points"]; // 已经赠送的积分数量
						$canSendAll = $userValue ["history_zsq"] * 120; // 消费分红权总共可以赠送的积分数量
						
						if (isset ( $userValue ['shou_yi_rate'] ) && $userValue ['shou_yi_rate'] != - 1) { // 设置了单个用户的收益发放速度
							$sg_canSend = ($userValue ['shou_yi_rate'] * $userValue ["sg_zsq"]); // 本次收益分红权可以赠送的积分数
						} else {
							$sg_canSend = ($give_set_points * $userValue ["sg_zsq"]); // 本次收益分红权可以赠送的积分数
						}
						if($sg_canSend<0){
						    $sg_canSend = 0;
						}
						
						$sg_sended = $userValue ["sg_day_points"]; // 手工已经赠送的积分数量
						$sg_canSendAll = $userValue ["sg_history_zsq"] * 180; // 手工分红权总共可以赠送的积分数量
						
						if ($sended >= $canSendAll && $sg_sended >= $sg_canSendAll) {
							// 直接不赠送，赠送的数量已经超过了可赠送的数量记录，该会员存在问题
							var_dump ( "赠送完了" );
						} else {
							// 计算剩余赠送积分
							$leftSend = $canSendAll - $sended;
							if ($leftSend < $canSend) { // 如果剩余的积分不够本次赠送额,那么只赠送剩余积分
								$canSend = $leftSend;
							}
							
							$ben_ci_zsq = ($sended + $canSend) / 120; // 已经使用的分红权和本次要使用的分红权
							                                            // var_dump($sended.":".$canSend);
							
							$ben_ci_zsq = jieQu ( $ben_ci_zsq );
							$subZsq = ( float ) $userValue ["zsq"] - (( float ) $userValue ["history_zsq"] - $ben_ci_zsq);
							// var_dump("zsq:".$userValue["zsq"]."; history_zsq:".$userValue["history_zsq"]."; ben_ci_zsq:".$ben_ci_zsq."; subZsq".$subZsq);
							
							$subZsq = jieQu ( ( float ) $subZsq );
							
							//
							$ben_ci_sg_zsq = ($sg_sended + $sg_canSend) / 180;
							$sg_sheng_yu = $userValue ["sg_history_zsq"] - $ben_ci_sg_zsq;
							$sg_sub_Zsq = $userValue ["sg_zsq"] - $sg_sheng_yu; // 需要减掉的手工赠送权
							$sg_sub_Zsq = jieQu ( $sg_sub_Zsq );
							
							// var_dump(" sg_sheng_yu".$sg_sheng_yu." 要减掉的手工分红权:".$sg_sub_Zsq." ");
							
							if ($subZsq < 0 && $sg_sub_Zsq < 0) { // 已经赠送完成
							} else {
								$pay_points = $canSend + $sg_canSend; // 总的分红积分
								$yl_points = $canSend * 0;
								$hz_points = $canSend * 0; // 互助积分不再赠送
								$tdate = local_date ( "Y-m-d", $cDate );
								
								$people_num ++;
								$totalPayPoints = $totalPayPoints + $pay_points + $yl_points + $hz_points;
								if ($subZsq < 0) {
									$subZsq = 0;
								}
								if ($sg_sub_Zsq < 0) {
									$sg_sub_Zsq = 0;
								}
								// 记录赠送,但分红/养老/互助积分不到账. 等待领取
								$tres = log_account_change ( $userValue ['user_id'], 0, 0, 0, $pay_points, "赠送" . $tdate . "的积分，分红积分数量{$pay_points},其中本金{$canSend},收益{$sg_canSend}", ACT_REWARD_EVERY_DAY, 0, $canSend, 0, 0, $subZsq * (- 1), 0, $canSend, 0, 0, 0, 0, 0, 0, 0, 0, $sg_sub_Zsq * (- 1), 0, $sg_canSend );
								
								write_send_log ( "会员" . $userValue ["user_id"] . ",赠送" . $tdate . "的积分，消费积分数量" . $canSend . "消费分红积分数量" . $pay_points . "\r\n" );
// 								dongJieJieDong($userValue ["user_id"]);
								
								if ($tres === false) {
									$message = "会员" . $userValue ["user_id"] . ",赠送" . $cDate . "的积分，分红积分数量" . $pay_points . ",养老积分数量" . $yl_points . ',互助基金数量:' . $hz_points;
									$isok = false;
									break;
								}
								pushUserMsg1 ( $ecuid, array (
										"amount" => $canSend 
								), 4 );
							}
						}
					}
					if ($isok) { // 记录今天已经赠送了
						$sql = "UPDATE " . $GLOBALS ['ecs']->table ( "supplier_rebate_log" ) . " SET is_send=1 where add_time>='" . $cDate . "' AND add_time<'" . $dateEnd . "'";
						$GLOBALS ['db']->query ( $sql );
						// 记录分配的人数和时间
						$sql = "insert into  " . $GLOBALS ['ecs']->table ( "bonus_points_log" ) . " (people_num,pay_points,send_time) values(" . $people_num . "," . $totalPayPoints . "," . gmtime () . ")";
						$GLOBALS ['db']->query ( $sql );
					}
				} catch ( Exception $ex ) {
					$message = $ex->getMessage ();
					$isok = false;
				}
				if ($isok) { // 记录今天已经赠送了
					$sql = "insert into " . $GLOBALS ['ecs']->table ( 'send_log' ) . " (send_date,send_points,send_time) values('" . $dateStart . "','" . $give_set_points . "','" . gmtime () . "')";
					$isok = $GLOBALS ['db']->query ( $sql );
				}
				if ($isok) { // 修改下次执行的时间
					$sql = "update " . $GLOBALS ['ecs']->table ( "crons" ) . " set nextime='" . ($nextime + 86400) . "' where nextime<'" . $time . "' and cron_code='send_reward'";
					$GLOBALS ['db']->query ( $sql );
					
					write_send_log ( "日期" . local_date ( "Y-m-d" ) . "任务执行完毕\r\n" );
					$GLOBALS ['db']->commitTrans ();
					SendWxMessage ();
				} else {
					write_send_log ( "日期" . local_date ( "Y-m-d" ) . "任务执行错误,错误信息:" . $message . "\r\n" );
					$GLOBALS ['db']->rollbackTrans ();
				}
			} else {
				error_log ( "日期" . local_date ( "Y-m-d" ) . "已经赠送\r\n", 3, $dataLog );
			}
		} else {
		}
	}
}

/**
 * 冻结解冻用户的收益分红赠送
 */
function dongJieJieDong($user_id) {
	/**
	 * 如果用户最近一个月的投资少于一个数字就冻结用户的收益分红权积分发放, 如果达到了一个数字, 就解冻积分发放
	 */
	$time = gmtime ();
	$startTime = $time - 3600 * 24 * 30; // 30天内
	$sql = "SELECT SUM( amount ) FROM `xbmall_user_account`  WHERE user_id = {$user_id} and process_type = 0 and is_paid =1 and  add_time  BETWEEN  {$startTime} and {$time};";
	$total = $GLOBALS ['db']->getOne ( $sql ); //30天内的投资数
	
	$sql="select sg_zsq,shou_yi_rate from xbmall_users where user_id = {$user_id} ";//查询收益分红权的个数
	$user= $GLOBALS ['db']->getRow( $sql ); //30天内的投资数
	
	
	 
	if($total<$user['sg_zsq']*10){//如果投资金额不足收益分红权个数乘以10, 那么就冻结账户
		
		$sql="update xbmall_users  set shou_yi_rate=0 where user_id = {$user_id}";
		$GLOBALS ['db']->query ( $sql );
		dongJieJieDongLog($user_id,"由于最近一个月的投资额低于收益分红权所能分得的收益的十八分之一,所以收益分红权暂停分红,请消费后激活");
	}else {//解冻用户的收益积分赠送
		if($user['shou_yi_rate']==0){//等于0, 说明被冻结了, 那么解冻
			$sql="update xbmall_users  set shou_yi_rate=-1 where user_id = {$user_id}";
			$GLOBALS ['db']->query ( $sql );
			dongJieJieDongLog($user_id,"由于最近一个月的投资额达到收益分红权所能分得的收益的十八分之一,收益分红权激活分红.");
		}
		
	}
	
	
}

function dongJieJieDongLog($user_id, $change_desc){
	/* 插入帐户变动记录 */
	$account_log = array(
			'user_id' => $user_id,
			'change_time' => gmtime(),
			'change_desc' => $change_desc,
			'change_type' => ACT_REWARD_EVERY_DAY
	);
	$queryAccountLog = $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('account_log'), $account_log, 'INSERT');
	
}

/**
 * 截取小数点后一位, 不四舍五入小数点后第二位
 * 
 * @param unknown $total        	
 * @return number
 */
function jieQu($total) {
	$total = (floatval ( $total ));
	$total = $total * 10;
	$totalInt = intval ( strval ( $total ) );
	$total = (( float ) $totalInt / 10.0);
	return $total;
}
function SendWxMessage() {
	require (ROOT_PATH . 'mobile/weixin/wechat.class.php');
	$t = time ();
	$type = 4;
	$rses = $GLOBALS ['db']->getAll ( "SELECT * FROM " . $GLOBALS ['ecs']->table ( 'weixin_corn' ) . " WHERE `issend` = 0 and `sendtype`={$type} order by sendtime desc" );
	if ($rses) {
		foreach ( $rses as $rs ) {
			$sql = "SELECT * FROM " . $GLOBALS ['ecs']->table ( 'weixin_config' );
			$list = $GLOBALS ['db']->getAll ( $sql );
			$weixinconfig = array ();
			for($i = 0; $i < count ( $list ); $i ++) {
				$weixinconfig ['token'] = $list [$i] ['token'];
				$weixinconfig ['appid'] = $list [$i] ['appid'];
				$weixinconfig ['appsecret'] = $list [$i] ['appsecret'];
				// $weixinconfig = $db->getRow ( "SELECT * FROM " . $GLOBALS['ecs']->table('weixin_config') . " WHERE `id` = 1" );
				$weixin = new core_lib_wechat ( $weixinconfig );
				$content = unserialize ( $rs ['content'] );
				$msg = array ();
				$msg = $content;
				$user = array (
						array (
								'fake_id' => $content ['touser'] 
						) 
				);
				if ($user) {
					foreach ( $user as $u ) {
						$msg ['touser'] = $u ['fake_id'];
						// if ($msg['touser'] == "olFy0uF1UpIz5KRUG9RpL21S-ykk") {
						$ret = $weixin->sendCustomMessage ( $msg );
						if ($ret !== false) {
							$GLOBALS ['db']->query ( "UPDATE " . $GLOBALS ['ecs']->table ( 'weixin_corn' ) . " SET issend=2 where id ={$rs['id']}" );
						}
						// var_dump($ret);
						// }
						// if (!$_GET['ajax'])
						// var_dump($ret);
					}
				}
				// if ($_GET['ajax'] == 1) {
				// $result = array('error' => 0, 'content' => '');
				// echo json_encode($result);
				// }
			}
		}
	} else {
		echo "no data";
	}
}

// 统计昨天商家的佣金总数
// $sql="SELECT SUM(rebate_money) FROM ".$GLOBALS['ecs']->table("supplier_rebate_log") ." where ";
// $cron['send_reward_com_day'] = !empty($cron['send_reward_com_day']) ? $cron['send_reward_com_day'] : 1;
// file_put_contents("a.txt", $cron['send_reward_com_day']);
// $deltime = gmtime() - $cron['send_reward_com_day'] * 3600 * 24;
//
// $cron['order_del_www_ecshop68_com_action'] = !empty($cron['order_del_www_ecshop68_com_action']) ? $cron['order_del_www_ecshop68_com_action'] : 'invalid';
// //echo $cron['order_del_www_ecshop68_com_action'];
//
// $sql_www_ecshop68_com = "select order_id FROM " . $ecs->table('order_info') .
// " WHERE pay_status ='0' and add_time < '$deltime'";
// $res_www_ecshop68_com = $db->query($sql_www_ecshop68_com);
// while ($row_www_ecshop68_com = $db->fetchRow($res_www_ecshop68_com)) {
// if ($cron['order_del_www_ecshop68_com_action'] == 'cancel' || $cron['order_del_www_ecshop68_com_action'] == 'invalid') {
// /* 设置订单为取消 */
// if ($cron['order_del_www_ecshop68_com_action'] == 'cancel') {
// $order_cancel_www_ecshop68_com = array('order_status' => OS_CANCELED, 'to_buyer' => '超过一定时间未付款，订单自动取消');
// $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('order_info'), $order_cancel_www_ecshop68_com, 'UPDATE', "order_id = '$row_www_ecshop68_com[order_id]' ");
// }
// /* 设置订单未无效 */ elseif ($cron['order_del_www_ecshop68_com_action'] == 'invalid') {
// $order_invalid_www_ecshop68_com = array('order_status' => OS_INVALID, 'to_buyer' => ' ');
// $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('order_info'), $order_invalid_www_ecshop68_com, 'UPDATE', "order_id = '$row_www_ecshop68_com[order_id]' ");
// }
// } elseif ($cron['order_del_www_ecshop68_com_action'] == 'remove') {
// /* 删除订单 */
// $db->query("DELETE FROM " . $ecs->table('order_info') . " WHERE order_id = '$row_www_ecshop68_com[order_id]' ");
// $db->query("DELETE FROM " . $ecs->table('order_goods') . " WHERE order_id = '$row_www_ecshop68_com[order_id]' ");
// $db->query("DELETE FROM " . $ecs->table('order_action') . " WHERE order_id = '$row_www_ecshop68_com[order_id]' ");
// $action_array = array('delivery', 'back');
// del_delivery_www_ecshop68_com($row_www_ecshop68_com['order_id'], $action_array);
// }
// }
// function del_delivery_www_ecshop68_com($order_id, $action_array) {
// $return_res = 0;
//
// if (empty($order_id) || empty($action_array)) {
// return $return_res;
// }
//
// $query_delivery = 1;
// $query_back = 1;
// if (in_array('delivery', $action_array)) {
// $sql = 'DELETE O, G
// FROM ' . $GLOBALS['ecs']->table('delivery_order') . ' AS O, ' . $GLOBALS['ecs']->table('delivery_goods') . ' AS G
// WHERE O.order_id = \'' . $order_id . '\'
// AND O.delivery_id = G.delivery_id';
// $query_delivery = $GLOBALS['db']->query($sql, 'SILENT');
// }
// if (in_array('back', $action_array)) {
// $sql = 'DELETE O, G
// FROM ' . $GLOBALS['ecs']->table('back_order') . ' AS O, ' . $GLOBALS['ecs']->table('back_goods') . ' AS G
// WHERE O.order_id = \'' . $order_id . '\'
// AND O.back_id = G.back_id';
// $query_back = $GLOBALS['db']->query($sql, 'SILENT');
// }
//
// if ($query_delivery && $query_back) {
// $return_res = 1;
// }
//
// return $return_res;
// }
function write_send_log($logMessage) {
	$dataLog = ROOT_PATH . "/data/log/" . local_date ( "Y-m-d" ) . ".log";
	error_log ( "当前时间：" . local_date ( "Y-m-d H:i:s", time () ) . ",内容:" . $logMessage . "\r\n", 3, $dataLog );
}
function pushUserMsg1($ecuid, $msg = array(), $type = 1) {
	$weixinconfig = $GLOBALS ['db']->getRow ( "SELECT * FROM " . $GLOBALS ['ecs']->table ( 'weixin_config' ) . " WHERE `id` 

= 1" );
	
	if ($type == 1 && $weixinconfig ['buynotice'] == 1) {
		$text = htmlspecialchars_decode ( $weixinconfig ['buymsg'] );
		foreach ( $msg as $k => $v ) {
			$text = str_replace ( $k, $v, $text );
			$text = $text;
		}
	} elseif ($type == 2 && $weixinconfig ['sendnotice'] == 1) {
		$text = htmlspecialchars_decode ( $weixinconfig ['sendmsg'] );
		foreach ( $msg as $k => $v ) {
			$text = str_replace ( $k, $v, $text );
			$text = $text;
		}
	} elseif ($type == 3 && $weixinconfig ['txnotice'] == 1) {
		$text = htmlspecialchars_decode ( $weixinconfig ['txmsg'] );
		foreach ( $msg as $k => $v ) {
			$text = str_replace ( $k, $v, $text );
			$text = $text;
		}
	} elseif ($type == 4 && $weixinconfig ['daynotice'] == 1) {
		$text = htmlspecialchars_decode ( $weixinconfig ['daymsg'] );
		foreach ( $msg as $k => $v ) {
			$text = str_replace ( $k, $v, $text );
			$text = $text;
		}
	} else {
		return false;
	}
	$user = $GLOBALS ['db']->getRow ( "select * from " . $GLOBALS ['ecs']->table ( 'weixin_user' ) . " where ecuid='{$ecuid}'" );
	if ($user && $user ['fake_id']) {
		$content = array (
				'touser' => $user ['fake_id'],
				'msgtype' => 'text',
				'text' => array (
						'content' => $text 
				) 
		);
		$content = serialize ( $content );
		$sendtime = $sendtime ? $sendtime : time ();
		$createtime = time ();
		$sql = "insert into " . $GLOBALS ['ecs']->table ( 'weixin_corn' ) . "

(`ecuid`,`content`,`createtime`,`sendtime`,`issend`,`sendtype`) 
			value ({$ecuid},'{$content}','{$createtime}','{$sendtime}','0',

{$type})";
		$GLOBALS ['db']->query ( $sql );
		return true;
	} else {
		return false;
	}
}
function curl_get($url) {
	$ch = curl_init ();
	curl_setopt ( $ch, CURLOPT_URL, $url );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt ( $ch, CURLOPT_HEADER, 0 );
	$output = curl_exec ( $ch );
	curl_close ( $ch );
}

?>