<?php
/**
 * ============================================================================
 * * 版权所有 2017-2022 儒孝养老服务有限公司，并保留所有权利。
 * 处理商家做单相关事务
 *   网站地址: http://www.xiangbai315.com;
 * ----------------------------------------------------------------------------
 */
define ( 'IN_ECS', true );
require (dirname ( __FILE__ ) . '/includes/init.php');
/* 载入语言文件 */
require_once (ROOT_PATH . 'languages/' . $_CFG ['lang'] . '/user.php');
// 不需要登录的操作或自己验证是否登录（如ajax处理）的act
$not_login_arr = array (
		'login',
		'act_login',
		'act_edit_password',
		'get_password',
		'send_pwd_email',
		'password',
		'signin' 
);

$not_login_arr [] = 'send_mobile_code';
$not_login_arr [] = 'send_email_code';
$not_login_arr [] = 'check_mobile';

/* 显示页面的action列表 */
$ui_arr = array (
		'login' 
);

$ui_arr [] = 'get_user';
$ui_arr [] = 'checkMoney';
$ui_arr [] = 'add';
$ui_arr [] = 'save';
$ui_arr [] = 'list';

/* end */
$user_id = isset ( $_SESSION ['user_id'] ) ? $_SESSION ['user_id'] : 0;
$action = isset ( $_REQUEST ['act'] ) ? trim ( $_REQUEST ['act'] ) : 'default';

/* 未登录处理 */
if (empty ( $_SESSION ['user_id'] )) {
	if (! in_array ( $action, $not_login_arr )) {
		if (in_array ( $action, $ui_arr )) {
			if (! empty ( $_SERVER ['QUERY_STRING'] )) {
				$back_act = 'user.php?' . strip_tags ( $_SERVER ['QUERY_STRING'] );
			}
			$action = 'login';
		} else {
			// 未登录提交数据。非正常途径提交数据！
			show_message ( $_LANG ['require_login'], array (
					'</br>登录',
					'</br>返回首页' 
			), array (
					'user.php?act=login',
					$ecs->url () 
			), 'error', false );
		}
	}
}

/* 路由 */

$function_name = 'action_' . $action;

if (! function_exists ( $function_name )) {
	$function_name = "action_default";
}

call_user_func ( $function_name );

/**
 * 做单列表
 */
function action_list() {
	$smarty = $GLOBALS ['smarty'];
	
	$smarty->assign ( 'action_link', array (
			'text' => "新增做单",
			'href' => 'supplier_m_zuo_dan.php?act=add' 
	) );
	
	
	$agency_list = get_offline_man_list ();
	$smarty->assign ( 'order_list', $agency_list ['agency'] );
	$smarty->assign ( 'filter', $agency_list ['filter'] );
	$smarty->assign ( 'record_count', $agency_list ['record_count'] );
	$smarty->assign ( 'page_count', $agency_list ['page_count'] );
	$smarty->assign ( 'pager', $agency_list['pager']);
	$smarty->display ( 'supplier/supplier_zuo_dan_list.dwt' );
}
/**
 * 转到做单页面
 */
function action_add() {
	$smarty = $GLOBALS ['smarty'];
	/* 是否添加 */
	$is_add = $_REQUEST ['act'] == 'add';
	$smarty->assign ( 'form_action', $is_add ? 'insert' : 'update' );
	
	// 20170315防止数据重复提交添加token验证开始
	if (isset ( $_SESSION ['offline_detail_time'] )) {
		unset ( $_SESSION ['offline_detail_time'] );
	}
	$_SESSION ['offline_detail_time'] = gmtime ();
	$smarty->assign ( "postToken", md5 ( $_SESSION ['user_id'] . $_SESSION ['offline_detail_time'] . AUTH_KEY ) );
	// 20170315防止数据重复提交添加token验证结束
	$sql="select system_fee from ".$GLOBALS ["ecs"]->table ( "supplier" )." where user_id=".$_SESSION ["user_id"];
	$zuodan_set_fei =$GLOBALS ['db']->getOne ( $sql );
	$smarty->assign ( 'zuodan_set_fei', $zuodan_set_fei/100 );
	
	$smarty->assign ( 'action', 'supplier_zuo_dan' );
	$smarty->display ( 'user_mutual.dwt' );
}

// 保存做单
function action_save() {
	 
	// 20170315防止数据重复提交添加token验证
// 	if (! isset ( $_POST ['postToken'] ) || empty ( $_POST ['postToken'] ) || ! isset ( $_SESSION ['offline_detail_time'] ) || empty ( $_SESSION ['offline_detail_time'] )) {
// 		sys_msg ( "提交异常，重复提交" );
// 		exit ();
// 	} else {
// 		$postToken = $_POST ['postToken'];
// 		if ($postToken != md5 ( $_SESSION ['user_id'] . $_SESSION ['offline_detail_time'] . AUTH_KEY )) {
// 			sys_msg ( "异常提交，请返回后刷新页面重试" );
// 			exit ();
// 		}
// 		unset ( $_SESSION ['offline_detail_time'] );
// 	}
	
	
	// 20170315防止数据重复提交添加token验证结束
	
	if (! isset ( $_POST ["user_id"] ) || ! isset ( $_POST ["good_name"] ) || ! isset ( $_POST ["order_amt"] )) {
		// show_message("参数不合法", "返回上一页", 'user.php?act=offline_detail', 'info');
		sys_msg ( "参数不合法" );
	}
	$sql= "select `user_id`,parent_id,`user_name` from" . $GLOBALS ["ecs"]->table ( "users" ) . " where `mobile_phone`='" . $_POST ["user_id"] . "'";
	
	$check_user = $GLOBALS ['db']->getRow ( $sql );//被做单的人
	if (! empty ( $check_user )) {
// 		if ($check_user ["user_id"] * 1 == $_SESSION ["user_id"] * 1) {
// 			sys_msg ( "不能给自己添加报单" );
// 		}
		// exit(json_encode(array("code" => 1, "user_id" => $check_user["user_id"], 'user_name' => $check_user['user_name'])));
	} else {
		sys_msg ( "会员不存在" );
	}
	
	
	$sql="select system_fee from ".$GLOBALS ["ecs"]->table ( "supplier" )." where user_id=".$_SESSION ["user_id"];
	$fuWuFei=$GLOBALS ['db']->getOne ( $sql );
	// 读取配置
// 	$zuodan_set_fei = $_CFG ["zuodan_fei"] > 0 ? $_CFG ["zuodan_fei"] : 0.12; // 报单费
	$zuodan_set_fei = $fuWuFei/100; // 报单费比例
	
	$zuodan_supp_fei = $_CFG ["zuodan_supp_fei"] > 0 ? $_CFG ["zuodan_supp_fei"] : 0.15; // 城市经理获得奖励
	$zuodan_supp_tjnum = $_CFG ["zuodan_supp_tjnum"] > 0 ? $_CFG ["zuodan_supp_tjnum"] : 50; // 商家推荐奖励需推荐人数大多少人
	$zuodan_supp_tjnum_jl = $_CFG ["zuodan_supp_tjnum_jl"] > 0 ? $_CFG ["zuodan_supp_tjnum_jl"] : 0.02; // 商家做单奖励(原先名字是推荐奖励)
	
	$bd_order_amt = $_POST ["order_amt"];
	$amount = intval ( $bd_order_amt ) * $zuodan_set_fei;//$amount做单服务费
	if ($amount > 0) {
		$sql = "select `user_money`,`jljs_points` from" . $GLOBALS ["ecs"]->table ( "users" ) . " where `user_id`='" . $_SESSION ["user_id"] . "'";
		$checkjeRow = $GLOBALS ['db']->getRow ( $sql );
		$checkje = $checkjeRow ["user_money"];
		$checkjeJljs = $checkjeRow ["jljs_points"];
		$zuodan_set_max = $_CFG ["zuodan_set_max"] * 1;
		if (empty ( $zuodan_set_max )) {
			$zuodan_set_max = 50000;
		}
		if ($amount > $checkje) {
			sys_msg ( "金额不合法,超过可用服务费".$checkje);
			// exit(json_encode(array("code" => 0, "message" => "")));
		} else {
			if ($amount > $zuodan_set_max) {
				sys_msg ( "金额不合法,超过系统设置最大服务费" );
			} else {
				// exit(json_encode(array("code" => 1)));
			}
		}
	} else {
		sys_msg ( "金额错误" );
	}
	
	$fp_url = $_REQUEST ['fp_url'];
	if (empty ( $fp_url )) {
		sys_msg ( "消费清单号错误" );
	}
	
	if ($_FILES ['good_url'] ['size'] > 0) {
		include_once (ROOT_PATH . '/includes/cls_image.php');
		$good_image = new cls_image ( $_CFG ['bgcolor'] );
		$good_image_original = $good_image->upload_image ( $_FILES ['good_url'], 'good_url/' . date ( 'Ym' ) );
		
		$good_url_path = DATA_DIR . '/good_url/' . date ( 'Ym' ) . '/';
		$good_url_thumb = $good_image->make_thumb ( $good_image_original, '250', '168', $good_url_path );
		$good_url = $good_url_thumb ? $good_url_thumb : $good_image_original;
	}
	
	$real_supplier_id = 0;
	
	$GLOBALS ['db']->startTrans ();
	include_once (ROOT_PATH . '/includes/lib_order.php');
	$userDefault = get_user_account_info ( $_SESSION ['user_id'] );//做单者(商家)
	
	
	
	$sql= "select user_id from ".$GLOBALS ["ecs"]->table ( "supplier_admin_user" )." where uid=".$_SESSION ['user_id']." order by role desc";
	//首先获取的是商家角色,然后是运营中心
	$member_id=$GLOBALS ['db']->getOne ($sql);
	
	$bd_data = array (
			"user_id" => $check_user ["user_id"],
			"supplier_id" => $_SESSION ['user_id'],
			"order_amt" => $bd_order_amt * 1,
			"order_bdf" => $amount,
			"good_name" => $_POST ['good_name'],
			"createtime" => gmtime (),
			"fp_url" => $fp_url,
			"good_url" => $good_url,
			"status" => 1,
			"order_sn" => get_order_sn (),
			'supplier_parent_id' => $userDefault ['parent_id'],
			'real_supplier_id' => $member_id,
			'large_area_id' => $_SESSION ['member_large_area_id'],
			'city_id' => $_SESSION ['member_city_id'],
			'member_id' => $member_id
	);
 
	
	
	$insertBd = $GLOBALS ['db']->autoExecute ( $GLOBALS ['ecs']->table ( "order_bd" ), $bd_data );

	if ($insertBd) {
		$bdId = $GLOBALS ['db']->insert_id ();
		
		$currentUser = get_user_account_info ( $check_user ["user_id"] );//被做单的人
		$historyZsq = $currentUser ["history_zsq"];
		$howManyZsq = ($bd_order_amt/1000)*($zuodan_set_fei/0.12);//按照商家的服务费比例, 算出消费者本次消费可以得到多少个分红权(小数点后两位不计)
		
	 
// 		$yuShu= sprintf('%.1f', (float)$yuShu);
		$howManyZsq= jieQu($howManyZsq);
		 
		$addZsq =  $howManyZsq;
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
		
		$resSupp = log_account_change ( $_SESSION ['user_id'], $amount * (- 1), 0, 0, 0, "添加报单，报单记录:" . $bdId . "报单金额" . $bd_order_amt . ",报单服务费:" . $amount, 3, 0, 0, 0, 0, 0 );
		
		$resGz = true;
// 		if ($_SESSION ['member_role'] * 1 <= 2) { // 如果当前会员是城市经理，则将扣减的奖励基数增加到基数奖励
// 			$resGz = log_account_change ( $member_id, 0, 0, 0, 0, "添加报单，基数奖励增加" . $amount, 3, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, $amount );
// 		}
		
		
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
				"supplier_id" => $_SESSION ['user_id'],
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
				'large_area_id' => $_SESSION ['member_large_area_id'],
				'city_id' => $_SESSION ['member_city_id'],
				'member_id' => $member_id,
				'user_id'=>$check_user['user_id']
		);
		$insertRebeat = $GLOBALS ['db']->autoExecute ( $GLOBALS ['ecs']->table ( "supplier_rebate_log" ), $rebeat );
		// }
	
		
		if ($resUser && $resSupp && $insertRebeat && $resAffiliate && $resGz && $resCs) {
		
// 			admin_log ( "提交报单", 'add', 'role_offline_man' );
			
			$GLOBALS ['db']->commitTrans ();
			$links = array (
					array (
							'href' => 'supplier_m_zuo_dan.php?act=add',
							'text' => "继续添加报单" 
					),
					array (
							'href' => 'supplier_m_zuo_dan.php?act=list',
							'text' => "报单列表" 
					) 
			);
			sys_msg ( "提交报单成功", 0, $links );
		} else {
			$GLOBALS ['db']->rollbackTrans();
			sys_msg ( "提交报单失败" );
		}
	} else {
		$GLOBALS ['db']->rollbackTrans ();
		sys_msg ( "提交报单失败" );
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
 * 检查商家的服务费金额
 *
 * @param unknown $amount        	
 */
function action_checkMoney() {
	$amount = $_POST ['amount'];
	if (isset ( $amount ) && ! empty ( $amount )) {
// 		$bdFei = $GLOBALS ['db']->getOne ( "select value from " . $GLOBALS ['ecs']->table ( "shop_config" ) . " where code='zuodan_set_fei'" ); //
		
		$sql="select system_fee from ".$GLOBALS ["ecs"]->table ( "supplier" )." where user_id=".$_SESSION ["user_id"];
		$bdFei=$GLOBALS ['db']->getOne ( $sql )/100;
		if (empty ( $bdFei )) {
			$bdFei = 0.12;
		} // * $bdFei
		$amount = intval ( $_POST ["amount"] ) * $bdFei * 1;
		if ($amount > 0) {
			$sql = "select `user_money`,`jljs_points` from" . $GLOBALS ["ecs"]->table ( "users" ) . " where `user_id`='" . $_SESSION ["user_id"] . "'";
			$checkjeRow = $GLOBALS ['db']->getRow ( $sql );
			$checkje = $checkjeRow ["user_money"];
			$checkjeJljs = $checkjeRow ["jljs_points"];
			$zuodan_set_max = $GLOBALS ['db']->getOne ( "select value from " . $GLOBALS ['ecs']->table ( "shop_config" ) . " where code='zuodan_set_max'" );
			$zuodan_set_max = $zuodan_set_max * 1; // 做单单次最大服务费
			
			
			if (empty ( $zuodan_set_max )) {
				$zuodan_set_max = 50000;
			}
			if ($amount > $checkje) {
				exit ( json_encode ( array (
						"code" => 0,
						"message" => "金额不合法,超过可用服务费" . $checkje 
				) ) );
			} else {
				if ($amount > $zuodan_set_max) {
					exit ( json_encode ( array (
							"code" => 0,
							"message" => "金额不合法,超过系统设置最大服务费" 
					) ) );
				} else {
					exit ( json_encode ( array (
							"code" => 1 
					) ) );
				}
			}
		} else {
			exit ( json_encode ( array (
					"code" => 0,
					"message" => "金额错误" 
			) ) );
		}
	} else {
		exit ( json_encode ( array (
				"code" => 0,
				"message" => "金额错误" 
		) ) );
	}
}

/**
 * 根据用户id获取用户,是否可以报单
 */
function action_get_user() {
	if (! empty ( $_POST ['user_id'] )) {
		
		$userInfo = get_user_info_bymobile ( trim($_POST ['user_id']) );
		
// 		if (($userInfo ["code"] * 1 == 1) && ($userInfo ['user_id'] * 1 == $_SESSION ["user_id"] * 1)) {
// 			exit ( json_encode ( array (
// 					"code" => 0,
// 					"message" => "不能给自己添加报单" 
// 			) ) );
// 		} else {
// 			exit ( json_encode ( $userInfo ) );
// 		}
		exit ( json_encode ( $userInfo ) );
	}
}

/**
 * 做单列表
 *
 * @return array
 */
function get_offline_man_list() {
	$filter ['keywords'] = isset ( $_REQUEST ['keywords'] ) ? trim ( addslashes ( htmlspecialchars ( $_REQUEST ['keywords'] ) ) ) : '';
	$filter ['sort_by'] = empty ( $_REQUEST ['sort_by'] ) ? 'sort_order' : trim ( $_REQUEST ['sort_by'] );
	$filter ['sort_order'] = empty ( $_REQUEST ['sort_order'] ) ? 'ASC' : trim ( $_REQUEST ['sort_order'] );
	
	$result = get_filter (); // 原有的查询条件
	if ($result === false) {
		 
		/* 初始化分页参数 */
		$filter = array ();
		$filter ['sort_by'] = empty ( $_REQUEST ['sort_by'] ) ? 'id' : trim ( $_REQUEST ['sort_by'] );
		$filter ['sort_order'] = empty ( $_REQUEST ['sort_order'] ) ? 'DESC' : trim ( $_REQUEST ['sort_order'] );
		
		/* 初始化时间查询条件 */
		$up_time = strtotime ( $_REQUEST ['up_time'] ); // 开始时间
		$end_time = strtotime ( $_REQUEST ['end_time'] ); // 结束时间
		$where = " AND  type=0 AND 1=1";
		if ($up_time > 0 || $end_time > 0) {
			$up_time = $up_time > 0 ? $up_time - 28800 : 0;
			$end_time = $end_time > 0 ? $end_time - 28800 : 0;
			$where = " and createtime BETWEEN $up_time AND $end_time ";
		}
		
		/* 统计 订单金额 报单费 */
		$sql_sum = "SELECT sum(order_amt) as order_amt_sum , sum(order_bdf) as order_bdf_sum FROM " . $GLOBALS ['ecs']->table ( 'order_bd' ) . "  WHERE member_id='" . $_SESSION ['user_id'] . "' {$where} ";
		
		$order_sum = $GLOBALS ['db']->getRow ( $sql_sum );
		
		/* 查询记录总数，计算分页数 */
		$sql = "SELECT COUNT(*)  FROM " 
				. $GLOBALS ['ecs']->table ( 'order_bd' ) . " s LEFT JOIN " .
		$GLOBALS ['ecs']->table ( 'users' ) . " a ON s.user_id = a.user_id WHERE s.supplier_id = '"
				. $_SESSION ['user_id'] . "' ". $where;
		$filter ['record_count'] = $GLOBALS ['db']->getOne ( $sql );
		$filter = page_and_size ( $filter );
 
		/* 查询记录 */
		$sql = "SELECT	s.id,	s.createtime,	s.fp_url,	s.good_url,	s.good_name,	s.order_amt,	s.order_bdf,	s.order_amt,	a.user_name,	a.real_name FROM " 
				. $GLOBALS ['ecs']->table ( 'order_bd' ) . " s LEFT JOIN " .
		$GLOBALS ['ecs']->table ( 'users' ) . " a ON s.user_id = a.user_id WHERE s.supplier_id = '"
				. $_SESSION ['user_id'] . "'  {$where}  ORDER BY $filter[sort_by] $filter[sort_order]";
	 
		set_filter ( $filter, $sql );
	} else {
		$sql = $result ['sql'];
		$filter = $result ['filter'];
	}
	$res = $GLOBALS ['db']->selectLimit ( $sql, $filter ['page_size'], $filter ['start'] );
	
	$arr = array ();
	while ( $rows = $GLOBALS ['db']->fetchRow ( $res ) ) {
		$rows ["order_time"] = local_date ( "Y-m-d H:i:s", $rows ['createtime'] );
		$arr [] = $rows;
	}
	
	/* 在分页数组中添加 统计数据 */
	$filter ['order_amt_sum'] = $order_sum ['order_amt_sum'];
	$filter ['order_bdf_sum'] = $order_sum ['order_bdf_sum'];

	$pager = get_pager('supplier_m_zuo_dan.php', array('act' => 'list'), $filter['record_count'], $filter['page'], $filter['page_size']);
 
	return array (
			'agency' => $arr,
			'filter' => $filter,
			'page_count' => $filter ['page_count'],
			'record_count' => $filter ['record_count'],
			'order_amt_sum' => $order_sum ['order_amt_sum'],
			'order_bdf_sum' => $order_sum ['order_bdf_sum'] ,
			'pager'=>$pager
	);
	
}

?>