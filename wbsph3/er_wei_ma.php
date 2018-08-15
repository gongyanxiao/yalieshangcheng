<?php

/**
 * ECSHOP 注册
 */
define('IN_ECS', true);
require (dirname(__FILE__) . '/includes/init.php');
require (dirname(__FILE__) . '/includes/lib_clips.php');


/* 载入语言文件 */

$action = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : 'default';
$function_name = 'action_' . $action;

if (! function_exists ( $function_name )) {
	$function_name = "action_default";
}

call_user_func ( $function_name );


//支付宝的支付回调
function  action_default(){
	
	file_put_contents ( dirname(__FILE__). "/test.txt", "收到支付宝的回复\r\n", FILE_APPEND );
// 	$r = var_export($_REQUEST,true);
// 	file_put_contents ( dirname(__FILE__). "/test.txt", $r."\r\n", FILE_APPEND );
	$r = var_export($_POST,true);
	file_put_contents ( dirname(__FILE__). "/test.txt", $r."\r\n", FILE_APPEND );
	$trade_status = $_REQUEST['trade_status'];
	if("TRADE_SUCCESS"!=$trade_status){//交易失败
		$text = var_export($_REQUEST,true);
		file_put_contents (  "test.txt", $text."支付宝扫码交易失败\r\n", FILE_APPEND );
		return ;
	}
	
	$out_trade_no = intval($_REQUEST['out_trade_no']);//交易号码
	
	if ($out_trade_no<0)return ;
	
	
	$sign = $_REQUEST['sign'];//验签
	handleXiaoFei($out_trade_no);
	
	
}


function  xiao_fei_huo_kuan($user_id,$hk_xf_points,$zuodan_set_fei){
	/* 插入帐户变动记录 */
	$account_log = array(
			'user_id' => $user_id,
			'change_time' => gmtime(),
			'change_desc' => "消费者线下扫码支付,获得现金消费货款".$hk_xf_points."  当前服务费比例:".$zuodan_set_fei."%",
			'change_type' => 3,
			'hk_xf_points' => $hk_xf_points
		 
	);
	$queryAccountLog = $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('account_log'), $account_log, 'INSERT');
 
	if ($queryAccountLog) {
		/* 更新用户信息 */
		$sql = "UPDATE " . $GLOBALS['ecs']->table('users') .
		" SET hk_xf_points = hk_xf_points + ('$hk_xf_points')" .
		" WHERE user_id = '$user_id'  ";
	  $GLOBALS['db']->query($sql);
	}
}

function handleXiaoFei($log_id){
 
	/* 取得要修改的支付记录信息 */
	$sql = "SELECT * FROM " . $GLOBALS['ecs']->table('pay_log') . " WHERE log_id = '$log_id' and  is_paid=0";
	
	$pay_log = $GLOBALS['db']->getRow($sql);
	if($pay_log==null){
		return ;//已结付款了
	}
	
	
// 	    if(!$pay_log || $pay_log['is_paid']!=0)
// 		return ;//没有这条记录或已经付款了
		
		
		try {
			$GLOBALS['db']->startTrans();
			
			/* 修改此次支付操作的状态为已付款 */
			$sql = 'UPDATE ' . $GLOBALS['ecs']->table('pay_log') . " SET is_paid = '1' WHERE log_id = '$log_id'";
			$GLOBALS['db']->query($sql);
			$orderId = $pay_log['order_id'];
			
			
			$sql="update  ".$GLOBALS['ecs']->table('user_account')." set is_paid=1 where id=".$orderId;
			$GLOBALS['db']->query($sql);//修改账户状态为已支付
			
			$sql="select * from    ".$GLOBALS['ecs']->table('user_account')."   where id=".$orderId;
			//给商家货款积分, 给用户分红权
			$userAccount= $GLOBALS['db']->getRow($sql);//获取支付信息
		 
			$sql= "select `user_id`,parent_id,`user_name` from" . $GLOBALS ["ecs"]->table ( "users" ) . " where `user_id`='" . $userAccount["user_id"] . "'";
			$check_user = $GLOBALS ['db']->getRow ( $sql );//被做单的人
			if (! empty ( $check_user )) {
				// 		if ($check_user ["user_id"] * 1 == $_SESSION ["user_id"] * 1) {
				// 			sys_msg ( "不能给自己添加报单" );
				// 		}
				// exit(json_encode(array("code" => 1, "user_id" => $check_user["user_id"], 'user_name' => $check_user['user_name'])));
			} else {
				sys_msg ( "会员不存在" );
			}
			$_CFG= $GLOBALS ['_CFG'];
			$sql="select system_fee from  ".$GLOBALS ["ecs"]->table ( "supplier" )." where user_id=".$userAccount["admin_user"];
			$zuodan_set_fei =  $GLOBALS ['db']->getOne ($sql); // 报单费比例
			$zuodan_set_fei = $zuodan_set_fei/100;
			$zuodan_supp_fei = $_CFG ["zuodan_supp_fei"] > 0 ? $_CFG ["zuodan_supp_fei"] : 0.15; // 城市经理获得奖励
			$zuodan_supp_tjnum = $_CFG ["zuodan_supp_tjnum"] > 0 ? $_CFG ["zuodan_supp_tjnum"] : 50; // 商家推荐奖励需推荐人数大多少人
			$zuodan_supp_tjnum_jl = $_CFG ["zuodan_supp_tjnum_jl"] > 0 ? $_CFG ["zuodan_supp_tjnum_jl"] : 0.02; // 商家做单奖励(原先名字是推荐奖励)
			
			$bd_order_amt = $userAccount["real_amount"];
			$amount = intval ( $bd_order_amt ) * $zuodan_set_fei;//$amount做单服务费
		 
			
			
			include_once (ROOT_PATH . '/includes/lib_order.php');
			include_once (ROOT_PATH . '/members/includes/lib_main.php');
			$userDefault = get_user_account_info ( $userAccount['admin_user'] );//做单者(商家)
			
			$sql= "select user_id from ".$GLOBALS ["ecs"]->table ( "supplier_admin_user" )." where uid=".$userAccount["admin_user"]." order by role desc";
			//首先获取的是商家角色,然后是运营中心
			$member_id=$GLOBALS ['db']->getOne ($sql);
			$bd_data = array (
					"user_id" => $check_user ["user_id"],
					"supplier_id" => $userAccount["admin_user"],
					"order_amt" => $bd_order_amt * 1,
					"order_bdf" => 0,
					"good_name" => $_POST ['good_name'],
					"createtime" => gmtime (),
					"fp_url" => $fp_url,
					"good_url" => $good_url,
					"status" => 1,
					"order_sn" => get_order_sn (),
					'supplier_parent_id' => $userDefault ['parent_id'],
					'real_supplier_id' => $member_id,
					'large_area_id' => 0,
					'city_id' => 0,
					'member_id' => $member_id
			);
			$insertBd = $GLOBALS ['db']->autoExecute ( $GLOBALS ['ecs']->table ( "order_bd" ), $bd_data );
			if ($insertBd) {
				$bdId = $GLOBALS ['db']->insert_id ();
				$currentUser = get_user_account_info ( $check_user ["user_id"] );//被做单的人
				 
				$howManyZsq = ($bd_order_amt/1000)*($zuodan_set_fei/0.12);//按照商家的服务费比例, 算出消费者本次消费可以得到多少个分红权(小数点后两位不计)
				// 		$yuShu= sprintf('%.1f', (float)$yuShu);
			
				$howManyZsq= jieQu($howManyZsq);
				
				
				$addZsq =  $howManyZsq ; //需要赠送的赠送权数量
				$temp = intval($addZsq*10);
				if($addZsq*10>($temp+0.99)){
					$addZsq =  $addZsq+0.01;
				}
				$addZsq= jieQu($addZsq);
				// 		$addZsq = intval ( $consum_money_value / 1000 ) - $historyZsq; // 需要赠送的赠送权数量
				$consum_money_value=$currentUser['consum_money_value'];
				$resUser = log_account_change ( $check_user ["user_id"], 0, 0, 0, 0, "添加报单，已有消费金额:" . $consum_money_value. ",报单金额" . $bd_order_amt . "", 3, $bd_order_amt, 0, 0, $addZsq, $addZsq );
				if($addZsq>0){//用户有赠送权的增加
					$gouWuQuan =$addZsq;
					if($currentUser['hlph_points']<$addZsq*100){//赠送购物券
						$gouWuQuan = $currentUser['hlph_points']/100;//赠送的购物券数等于积分数除以100
					}
					$sql="update  " . $GLOBALS ['ecs']->table ( 'users' ) . " set gou_wu_quan=gou_wu_quan+".$gouWuQuan.", hlph_points=hlph_points-".($gouWuQuan*100)." where user_id=".$check_user ["user_id"];
					$GLOBALS ['db']->query ( $sql);//增加购物券
					gou_wu_quan_log($check_user ["user_id"], "消费满1000,赠送互联普惠购物券".$gouWuQuan."张", $gouWuQuan);
					
				}
				
				$resAffiliate = true;
				if ($check_user ['user_id'] > 0) {
					if ($GLOBALS ["_CFG"] ["recommend_set_open"] * 1 === 1) {
						// $parentInfo = get_user_default($check_user['user_id']);
						if ($currentUser ["parent_id"] > 0) {
							$change_desc = "推荐会员线下购物获得推荐赠送，" . sprintf ( $GLOBALS ['_LANG'] ['order_gift_integral'], $bd_data ['order_sn'], 4 );
							$recommend_points = $bd_data ["order_amt"] * 1 * $GLOBALS ["_CFG"] ["recommend_set_percent"] / 100;
							// $affiliate = "insert into " . $GLOBALS['ecs']->table('affiliate_log') . " VALUES('" . $bdId . "','" . gmtime() . "','" . $check_user['user_id'] . "','',0,'" . $recommend_points . "',0,'" . $change_desc . "')";
							$affiliate = "insert into " . $GLOBALS ['ecs']->table ( 'affiliate_log' ) . "(order_id,`time`,`user_id`,`user_name`,`money`,`point`,`separate_type`,`change_desc`) VALUES('" . $bdId . "','" . gmtime () . "','" . $check_user ['user_id'] . "','',0,'" . $recommend_points . "',0,'" . $change_desc . "')";
							$resAffiliate = $GLOBALS ['db']->query ( $affiliate );
						}
					}
				}
				
				
				if(!$member_id){
					sys_msg ( "您还没开通店铺, 不能做单" );
				}
				
				xiao_fei_huo_kuan($userAccount["admin_user"],$bd_order_amt-$amount,$zuodan_set_fei);//消费货款积分(扣除手续费)
// 				$resSupp = log_account_change ( $userAccount["admin_user"], $amount * (- 1), 0, 0, 0, "添加报单，报单记录:" . $bdId . "报单金额" . $bd_order_amt . ",报单服务费:" . $amount, 3, 0, 0, 0, 0, 0 );
				
				
				
				$resCs = true;
				
				
				
				/**
				 * 做单费的15%归代理商得. 
				 */
				$parent_id = $userDefault['parent_id'];
				$amount1 =  $amount* $zuodan_supp_fei;//代理的工资
				
				
				$beiZuoDanZhe=$check_user['user_name'];
				
				
				if($userDefault['is_bigfamily'] ==2){//如果做单者是代理
					$tuiJianJiangLi= $amount*2/100;
					if($parent_id){//如果推荐人存在,发放推荐奖励,不扣奖励基数
						$tuiJianRen = get_user_account_info ( $parent_id);//做单者(商家)
						if($tuiJianRen['is_bigfamily'] ==2){//推荐代理的人得平台服务费的2%(推荐人必须是代理)
							log_account_change($parent_id, 0, 0, 0, 0, "推荐的代理".$userDefault['username']."做单，基数奖励增加:" . $tuiJianJiangLi, ACT_REWARD_ZD, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,  0,0,0,$tuiJianJiangLi);
						}
					}
					//代理自己得到平台服务费的15%的
					$kouchu_jljs = log_account_change($userDefault['user_id'], 0, 0, 0, 0, "代理自己做单，基数奖励增加:" . $amount1, ACT_REWARD_ZD, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,0,0, ($amount1) );
					if($kouchu_jljs){
						$resCs =log_account_change($userDefault['user_id'], 0, 0, 0, 0, "代理自己做单，奖励基数减少:" . $amount1, ACT_REWARD_ZD, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, -($amount1));
					}
				}else{//做单者是高级会员,推荐做单者的代理得15%的做单服务费
					
					while (1) {
						//查询会员信息
						$sql = "SELECT * FROM " . $GLOBALS['ecs']->table("users") . " WHERE user_id='" . $parent_id. "'";
						$user_daili = $GLOBALS['db']->getRow($sql);
						
						if( $user_daili['is_bigfamily'] ==2 ){
							$tuiJianRenDaiLi[] = $user_daili;
							$sql = "SELECT * FROM " . $GLOBALS['ecs']->table("users") . " WHERE user_id='" . $user_daili['parent_id']. "' and is_bigfamily = 2 ";//查询代理的推荐人是否是代理
							$user_daili = $GLOBALS['db']->getRow($sql);
							if( $user_daili['is_bigfamily'] ==2 ){//代理的推荐人是代理
								$tuiJianRenDaiLi[] = $user_daili;
							}
							break;
							
						}
						
						
						if ($user_daili) {
							$parent_id = $user_daili['parent_id'];
						} else {//直到找不到推荐人退出
							break;
						}
						
					}
					
					if($tuiJianRenDaiLi){//找到了会员推荐人路径中的代理
						$kouchu_jljs = log_account_change($parent_id, 0, 0, 0, 0, "代理推荐的会员".$userDefault['username']."做单，基数奖励增加:" . $amount1, ACT_REWARD_ZD, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,0,0, ($amount1) );
						if($kouchu_jljs){
							$resCs =log_account_change($parent_id, 0, 0, 0, 0, "代理推荐的会员".$userDefault['username']."做单，奖励基数减少:" . $amount1, ACT_REWARD_ZD, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, -($amount1));
						}
						
						if(count($tuiJianRenDaiLi)==2){//合伙人的推荐人是代理, 代理的推荐人
							$amount1 = floor($amount*2) / 100;//服务费的2%作为推荐奖励
							$kouchu_jljs = log_account_change($tuiJianRenDaiLi[1]['user_id'], 0, 0, 0, 0, "推荐的代理推荐的会员".$userDefault['username']."做单，基数奖励增加:" . $amount1, ACT_REWARD_ZD, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,0,0, ($amount1) );
							if($kouchu_jljs){
								$resCs =log_account_change($tuiJianRenDaiLi[1]['user_id'], 0, 0, 0, 0, "推荐的代理推荐的会员".$userDefault['username']."做单，奖励基数减少:" . $amount1, ACT_REWARD_ZD, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, -($amount1));
							}
						}
					}
				}
				
				
				
				$zuo_dan_shang_jia = get_user_account_info ( $_SESSION ['user_id'] );//做单者(商家)
				if($zuo_dan_shang_jia['is_bigfamily']!=2){//商家不是代理, 给分享奖励积分
					$amount1 = floor ( $amount* 0.02* 100 ) / 100;//做单服务费的2%给商家
					$resCs = fen_xiang_jiang_li_ji_fen_log( $_SESSION ['user_id'],  "商家做单奖励，增加分享奖励积分：" . $amount1,  $amount1);
				}
				
				
				$xiao_fei_tui_jian_ren = get_user_account_info ( $check_user['user_id'] );//消费者的推荐人不是代理, 给分享奖励积分
				if($xiao_fei_tui_jian_ren['is_bigfamily']!=2){
					$amount1 = floor ( $amount* 0.01 * 100 ) / 100;//做单服务费的1%给推荐人
					$resCs = fen_xiang_jiang_li_ji_fen_log( $check_user['parent_id'],  "推荐的会员在商家消费做单奖励，增加分享奖励积分：" . $amount1,  $amount1);
				}
				
				
				
				$rebeat = array (
						"order_sn" => $bd_data ["order_sn"],
						"order_id" => $bdId,
						"supplier_id" => $userAccount["admin_user"],
						"all_money" => $bd_data ["order_amt"],
						"rebate_money" => $bd_data ["order_bdf"],
						"result_money" => $bd_data ["order_amt"] - $bd_data ["order_bdf"],
						"pay_id" => 0,
						"pay_name" => "充值积分",
						"texts" => "线下支付",
						"add_time" => $bd_data ['createtime'],
						'supplier_parent_id' => $userDefault ['parent_id'],
						"is_offline" => 1,
						'real_supplier_id' => $member_id,
						'large_area_id' => 0,
						'city_id' => 0,
						'member_id' => $member_id,
						'user_id'=>$check_user['user_id']
				);
				$insertRebeat = $GLOBALS ['db']->autoExecute ( $GLOBALS ['ecs']->table ( "supplier_rebate_log" ), $rebeat );
				 
			} 
			
// 			$ress = log_account_change($userAccount["admin_user"], 0, 0, 0, 0, "消费者线下扫码支付, 获得货款积分", 1, 0, 0, 0, 0, 0, 0, 0, $bd_order_amt, 0, 0, 0);
			
// 			log_account_change($userAccount["admin_user"], $bd_order_amt, 0, 0, 0, "消费者线下扫码支付, 获得货款积分", 0);
			
			$GLOBALS['db']->commitTrans();//提交事物
			
		}catch (Exception $ex){
			$GLOBALS['db']->rollbackTrans();//回退事物
		}
		
		
		
}

function jieQu($total){
	$total = (floatval($total));
	$total = $total*10;
	$totalInt = intval(strval( $total));
	$total =((float)$totalInt/10.0);
	return $total;
}
/**
 * 获取支付id
 */
 function  action_get_pay_id(){
  
 	$amount = isset ( $_GET['amount'] ) ? floatval ( $_GET ['amount'] ) : 0;
 	$user_id= $_GET['user_id'];
 	$supp_id= $_GET['supp_id'];
 	if($supp_id==null){
 		echo -1;
 		return ;
 	}
 	
 	if (!isset($supp_id)){
 		echo  -1;
 		exit();
 	}
 	
 	if (!isset($user_id)){//用户没有登录或登录失效了
 		echo  -2;
 		exit();
 	}
 	
 	/* 变量初始化 */
 	$surplus = array (
 			'user_id' => $user_id,
 			'rec_id' =>  0,
 			'admin_user'=>$supp_id,//存储商户的id
 			'process_type' => 0,
 			'payment_id' => 20,//香柏商城扫码支付
 			'user_note' => '购买商户'.$supp_id.'的商品花费'.$amount.'元',
 			'amount' => $amount
 	);
 	// 插入会员账目明细状态为未支付
 	$surplus ['rec_id'] = insert_user_account ( $surplus, $amount );
  
 	// 记录支付log
 	$order ['log_id'] = insert_pay_log ( $surplus ['rec_id'], $amount, $type = 100, 0 );//支付类型是香柏客户端扫码支付
 	
 	echo $order['log_id']; //返回支付的ide
	 	
 }
 
 
 function insert_user_account_er_wei_ma($surplus, $amount) {
 	$sql = 'INSERT INTO ' . $GLOBALS['ecs']->table('user_account') .
 	' (user_id, admin_user, amount, add_time, paid_time, admin_note, user_note, process_type, payment, is_paid)' .
 	" VALUES ('$surplus[user_id]', '', '$amount', '" . gmtime() . "', 0, '', '$surplus[user_note]', '$surplus[process_type]', '$surplus[payment]', 0)";
 	$GLOBALS['db']->query($sql);
 	
 	return $GLOBALS['db']->insert_id();
 }
 
  
 
?>