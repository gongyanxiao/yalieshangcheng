<?php

/**
 * ECSHOP 支付接口函数库
 * ============================================================================
 * * 版权所有 2008-2015 秦皇岛商之翼网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.68ecshop.com;
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: derek $
 * $Id: lib_payment.php 17218 2011-01-24 04:10:41Z derek $
 */
if (!defined('IN_ECS')) {
    die('Hacking attempt');
}

/**
 * 取得返回信息地址
 * @param   string  $code   支付方式代码
 */
function return_url($code) {
    return $GLOBALS['ecs']->url() . 'respond.php?code=' . $code;
}

/**
 *  取得某支付方式信息
 *  @param  string  $code   支付方式代码
 */
function get_payment($code) {
    $sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('payment') .
            " WHERE pay_code = '$code' AND enabled = '1'";
    $payment = $GLOBALS['db']->getRow($sql);

    if ($payment) {
        $config_list = unserialize($payment['pay_config']);

        foreach ($config_list AS $config) {
            $payment[$config['name']] = $config['value'];
        }
    }

    return $payment;
}

/**
 *  通过订单sn取得订单ID
 *  @param  string  $order_sn   订单sn
 *  @param  blob    $voucher    是否为会员充值
 */
function get_order_id_by_sn($order_sn, $voucher = 'false') {
    if ($voucher == 'true') {
        if (is_numeric($order_sn)) {
            return $GLOBALS['db']->getOne("SELECT log_id FROM " . $GLOBALS['ecs']->table('pay_log') . " WHERE order_id=" . $order_sn . ' AND order_type=1');
        } else {
            return "";
        }
    } else {
        if (is_numeric($order_sn)) {
            $sql = 'SELECT order_id FROM ' . $GLOBALS['ecs']->table('order_info') . " WHERE order_sn = '$order_sn'";
            $order_id = $GLOBALS['db']->getOne($sql);
        }
        if (!empty($order_id)) {
            $pay_log_id = $GLOBALS['db']->getOne("SELECT log_id FROM " . $GLOBALS['ecs']->table('pay_log') . " WHERE order_id='" . $order_id . "'");
            return $pay_log_id;
        } else {
            return "";
        }
    }
}

/**
 *  通过订单ID取得订单商品名称
 *  @param  string  $order_id   订单ID
 */
function get_goods_name_by_id($order_id) {
    $sql = 'SELECT goods_name FROM ' . $GLOBALS['ecs']->table('order_goods') . " WHERE order_id = '$order_id'";
    $goods_name = $GLOBALS['db']->getCol($sql);
    return implode(',', $goods_name);
}

/**
 * 检查支付的金额是否与订单相符
 *
 * @access  public
 * @param   string   $log_id      支付编号
 * @param   float    $money       支付接口返回的金额
 * @return  true
 */
function check_money($log_id, $money) {
    if (is_numeric($log_id)) {
        $sql = 'SELECT order_amount FROM ' . $GLOBALS['ecs']->table('pay_log') .
                " WHERE log_id = '$log_id'";
        $amount = $GLOBALS['db']->getOne($sql);
    } else {
        return false;
    }
    if ($money == $amount) {
        return true;
    } else {
        return false;
    }
}

/**
 * 修改订单的支付状态
 *
 * @access  public
 * @param   string  $log_id     支付编号
 * @param   integer $pay_status 状态
 * @param   string  $note       备注
 * @return  void
 */
function order_paid($log_id, $pay_status = PS_PAYED, $note = '') {
    /* 取得支付编号 */
    $log_id = intval($log_id);
    if ($log_id > 0) {
        /* 取得要修改的支付记录信息 */
        $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('pay_log') .
                " WHERE log_id = '$log_id'";
        $pay_log = $GLOBALS['db']->getRow($sql);
        if ($pay_log && $pay_log['is_paid'] == 0) {
            /* 修改此次支付操作的状态为已付款 */
            $sql = 'UPDATE ' . $GLOBALS['ecs']->table('pay_log') .
                    " SET is_paid = '1' WHERE log_id = '$log_id'";
            $GLOBALS['db']->query($sql);

            /* 根据记录类型做相应处理 */
            if ($pay_log['order_type'] == PAY_ORDER) {
                /* 取得订单信息 */
                $sql = 'SELECT order_id, user_id,supplier_id, order_sn, consignee, address, tel, shipping_id, extension_code, extension_id, goods_amount ' .
                        'FROM ' . $GLOBALS['ecs']->table('order_info') .
                        " WHERE order_id = '$pay_log[order_id]' OR parent_order_id = '$pay_log[order_id]' ";
                $orderinfo = $GLOBALS['db']->getAll($sql);
                foreach ($orderinfo as $key => $order) {
                    $order_id = $order['order_id'];
                    $order_sn = $order['order_sn'];
                    $suppid = $order['supplier_id'];
                    $supplier[$suppid] = $order_sn;

                    /* 修改订单状态为已付款 */
                    $sql = 'UPDATE ' . $GLOBALS['ecs']->table('order_info') .
                            " SET order_status = '" . OS_CONFIRMED . "', " .
                            " confirm_time = '" . gmtime() . "', " .
                            " pay_status = '$pay_status', " .
                            " pay_time = '" . gmtime() . "', " .
                            " money_paid = order_amount," .
                            " order_amount = 0 " .
                            "WHERE order_id = '$order_id'";
                    $GLOBALS['db']->query($sql);

                    /* 记录订单操作记录 */
                    order_action($order_sn, OS_CONFIRMED, SS_UNSHIPPED, $pay_status, $note, $GLOBALS['_LANG']['buyer']);

                    /* 如果需要，发短信 */  //jx
//	               include_once('send.php');
//	
//					send_sms($supplier,'客户已付款，订单号为：ordersn请注意查看。【shopname】',2);

                    if ($GLOBALS['_CFG']['dayu_order_pay'] == 1 && $order['mobile'] != "") {

                        $aa = array(
                            'code' => $order['order_sn']
                        );



                        $moban = trim($GLOBALS['_CFG']['dayu_order_pay_tpl']);



                        require_once (ROOT_PATH . 'sms/sms.php');
                        sendSMS($order['mobile'], $aa, $moban);
                    }


                    //付款给商家发短信
                    if ($GLOBALS['_CFG']['dayu_order_payed'] == 1 && $GLOBALS['_CFG']['dayu_shop_mobile'] != "") {

                        $aa = array(
                            'code' => $order['order_sn']
                        );



                        $moban = trim($GLOBALS['_CFG']['dayu_order_payed_tpl']);



                        require_once (ROOT_PATH . 'sms/sms.php');
                        sendSMS($GLOBALS['_CFG']['dayu_shop_mobile'], $aa, $moban);
                    }

                    /* 对虚拟商品的支持 */
                    $virtual_goods = get_virtual_goods($order_id);
                    if (!empty($virtual_goods)) {
                        $msg = '';
                        if (!virtual_goods_ship($virtual_goods, $msg, $order_sn, true)) {
                            $GLOBALS['_LANG']['pay_success'] .= '<div style="color:red;">' . $msg . '</div>' . $GLOBALS['_LANG']['virtual_goods_ship_fail'];
                        }

                        /* 如果订单没有配送方式，自动完成发货操作 */
                        if ($order['shipping_id'] == -1) {
                            /* 将订单标识为已发货状态，并记录发货记录 */
                            $sql = 'UPDATE ' . $GLOBALS['ecs']->table('order_info') .
                                    " SET shipping_status = '" . SS_SHIPPED . "', shipping_time = '" . gmtime() . "'" .
                                    " WHERE order_id = '$order_id'";
                            $GLOBALS['db']->query($sql);

                            /* 记录订单操作记录 */
                            order_action($order_sn, OS_CONFIRMED, SS_SHIPPED, $pay_status, $note, $GLOBALS['_LANG']['buyer']);
                            $integral = integral_to_give($order);
                            log_account_change($order['user_id'], 0, 0, intval($integral['rank_points']), intval($integral['custom_points']), sprintf($GLOBALS['_LANG']['order_gift_integral'], $order['order_sn']));
                        }
                    }
                }
            } elseif ($pay_log['order_type'] == PAY_SURPLUS) {
                $sql = 'SELECT `id` FROM ' . $GLOBALS['ecs']->table('user_account') . " WHERE `id` = '$pay_log[order_id]' AND `is_paid` = 1  LIMIT 1";
                $res_id = $GLOBALS['db']->getOne($sql);
                if (empty($res_id)) {
                    /* 更新会员预付款的到款状态 */
                    $sql = 'UPDATE ' . $GLOBALS['ecs']->table('user_account') .
                            " SET paid_time = '" . gmtime() . "', is_paid = 1" .
                            " WHERE id = '$pay_log[order_id]' LIMIT 1";
                    $GLOBALS['db']->query($sql);

                    /* 取得添加预付款的用户以及金额 */
                    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('user_account') .
                            " WHERE id = '$pay_log[order_id]'";
                    $arr = $GLOBALS['db']->getRow($sql);

                    /* 修改会员帐户金额 */
                    $_LANG = array();
                    include_once(ROOT_PATH . 'languages/' . $GLOBALS['_CFG']['lang'] . '/user.php');
                    log_account_change($arr['user_id'], $arr['amount'], 0, 0, 0, $_LANG['surplus_type_0'], ACT_SAVING);
                    
                    //查询supplier_id
                    $sql_su = "SELECT user_id,supplier_id FROM " . $GLOBALS['ecs']->table('supplier_admin_user')." WHERE `uid` = '".$arr['user_id']."' LIMIT 1";
                    $sql_su_data = $GLOBALS['db']->GetRow($sql_su);
                    //supplier_rebat 添加log
                    $data_i = array(
                        'order_id'      => $arr['id'],
                        'supplier_id'   => $arr['user_id'],
                        'all_money'     => $arr['amount'],
                        'rebate_money'  => 0,
                        'result_money'  => 0,
                        'add_time'   => $arr['add_time'],
                        'is_send'    => 0,
                        'is_offline' => 3,
                        'pay_name'   => $arr['payment'],
                        'texts'      => '余额积分充值',
                        'real_supplier_id' => $sql_su_data['supplier_id'],
                        'member_id'        => $sql_su_data['user_id'],
                    );
                    $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('supplier_rebate_log'), $data_i, 'INSERT');
                    
                    pushUserMsg($arr['user_id'], array('order_no' => $pay_log[order_id], 'order_amt' => $arr['amount']), 8);
                    //$_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : "/admin/";
                    //$autoUrl = str_replace($_SERVER['REQUEST_URI'], '', $GLOBALS['ecs']->url());
                    @file_get_contents("http://" . $_SERVER['HTTP_HOST'] . '/mobile/weixin/auto_do.php?type=8');
                }
            }
        } else {
            /* 取得已发货的虚拟商品信息 */
            $post_virtual_goods = get_virtual_goods($pay_log['order_id'], true);

            /* 有已发货的虚拟商品 */
            if (!empty($post_virtual_goods)) {
                $msg = '';
                /* 检查两次刷新时间有无超过12小时 */
                $sql = 'SELECT pay_time, order_sn FROM ' . $GLOBALS['ecs']->table('order_info') . " WHERE order_id = '$pay_log[order_id]'";
                $row = $GLOBALS['db']->getRow($sql);
                $intval_time = gmtime() - $row['pay_time'];
                if ($intval_time >= 0 && $intval_time < 3600 * 12) {
                    $virtual_card = array();
                    foreach ($post_virtual_goods as $code => $goods_list) {
                        /* 只处理虚拟卡 */
                        if ($code == 'virtual_card') {
                            foreach ($goods_list as $goods) {
                                if ($info = virtual_card_result($row['order_sn'], $goods)) {
                                    $virtual_card[] = array('goods_id' => $goods['goods_id'], 'goods_name' => $goods['goods_name'], 'info' => $info);
                                }
                            }

                            $GLOBALS['smarty']->assign('virtual_card', $virtual_card);
                        }
                    }
                } else {
                    $msg = '<div>' . $GLOBALS['_LANG']['please_view_order_detail'] . '</div>';
                }

                $GLOBALS['_LANG']['pay_success'] .= $msg;
            }

            /* 取得未发货虚拟商品 */
            $virtual_goods = get_virtual_goods($pay_log['order_id'], false);
            if (!empty($virtual_goods)) {
                $GLOBALS['_LANG']['pay_success'] .= '<br />' . $GLOBALS['_LANG']['virtual_goods_ship_fail'];
            }
        }
    }
}

/**
 * 查看订单类型
 *
 * @access public
 * @param string $log_id
 *            支付编号
 * @param float $money
 *            支付接口返回的金额
 * @return true
 */
function get_order_type($log_id)
{
    if(is_numeric($log_id))
    {
        $sql = 'SELECT `order_type` FROM ' . $GLOBALS['ecs']->table('pay_log') . " WHERE log_id = '$log_id'";
        return $GLOBALS['db']->getOne($sql);
    }
    else
    {
        return false;
    }
}


/**
 * 修改互助订单的支付状态
 *
 * @access public
 * @param string $log_id
 *            支付编号
 * @param integer $pay_status
 *            状态
 * @param string $note
 *            备注
 * @return void
 */
function mutual_order_paid($log_id) {
    /* 取得支付编号 */
    $log_id = intval($log_id);
    if ($log_id > 0) {
        /* 取得要修改的支付记录信息 */
        $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('pay_log') . " WHERE log_id = '$log_id'";
        $pay_log = $GLOBALS['db']->getRow($sql);
        if ($pay_log && $pay_log['is_paid'] == 0) {
            /* 修改此次支付操作的状态为已付款 */
            $sql = 'UPDATE ' . $GLOBALS['ecs']->table('pay_log') . " SET is_paid = '1' WHERE log_id = '$log_id'";
            $GLOBALS['db']->query($sql);

            /* 爱心互助处理 */
            if ($pay_log['order_type'] == 2) {
                /* 修改订单状态为已付款 */
                $sql = "UPDATE " . $GLOBALS['ecs']->table('mutual_info') . " SET order_status = '1',  pay_time = '" . gmtime() . "' WHERE order_id = '" . $pay_log['order_id'] . "'";
                $GLOBALS['db']->query($sql);

                // 查询爱心会员帐号
                $mutual_info = $GLOBALS['db']->getRow(" SELECT mutual_id ,mutual_money , order_sn FROM " . $GLOBALS['ecs']->table('mutual_info') . "  where order_id ='" . $pay_log['order_id'] . "'");
                $user_id = $GLOBALS['db']->getOne(" SELECT user_id FROM " . $GLOBALS['ecs']->table('mutual') . "  where mutual_id ='" . $mutual_info['mutual_id'] . "'");

                if ($user_id > 0) {
                    // 给会员爱心账号加钱
                    log_account_change($user_id, 0, 0, 0, 0, '订单:' . $mutual_info['order_sn'] . '获得互助金额' . $mutual_info['mutual_money'], 27, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, $mutual_info['mutual_money']);
                }
            }

            /* 投保处理 */
            if ($pay_log['order_type'] == 3) {
                /* 修改订单状态为已付款 */
                $sql = "UPDATE " . $GLOBALS['ecs']->table('insure_info') . " SET order_status = '1',  pay_time = '" . gmtime() . "' WHERE order_id = '" . $pay_log['order_id'] . "'";
                $GLOBALS['db']->query($sql);
            }
        }
    }
}

/**
 * 大家庭修改状态
 *
 * @access public
 * @param string $log_id
 *            支付编号
 * @param integer $pay_status
 *            状态
 * @param string $note
 *            备注
 * @return void
 */
function dajiating_order_paid($log_id , $pay_names='') {

    /* 取得支付编号 */
    $log_id = intval($log_id);
    if ($log_id > 0) {
        /* 取得要修改的支付记录信息 */
        $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('pay_log') . " WHERE log_id = '$log_id'";
        $pay_log = $GLOBALS['db']->getRow($sql);
      
        if ($pay_log && $pay_log['is_paid'] == 0) {
            /* 修改此次支付操作的状态为已付款 */
            $sql = 'UPDATE ' . $GLOBALS['ecs']->table('pay_log') . " SET is_paid = '1' WHERE log_id = '$log_id'";
            $GLOBALS['db']->query($sql);

            $family_config = array(
                    'bigfamily_u_m' => $GLOBALS['_CFG']['bigfamily_u_m'],
                    'bigfamily_d_m' => $GLOBALS['_CFG']['bigfamily_d_m'],
            );

            //判断选择的类型
            switch ($pay_log['order_amount']) {
                case $family_config['bigfamily_u_m']:
                    $is_bigfamily = 1;
                    break;
                case $family_config['bigfamily_d_m']:
                    $is_bigfamily = 2;
                    break;
            }
          
           
            if ($is_bigfamily) {
            	
                $payment = substr($pay_names , 0 , 6);
                $payment = ($payment=='openid') ? '微信' : '支付宝';

                injoinfimaly($pay_log['order_id'], $is_bigfamily,$payment);
            }
             
        }
    }
}


/**
 * 产品中心积分充值
 * @param unknown $log_id
 * @param string $pay_names
 */

function chan_pin_order_paid($log_id , $pay_names='') {
	/* 取得支付编号 */
	$log_id = intval($log_id);
	if ($log_id<0)return ;
	
	
	
	/* 取得要修改的支付记录信息 */
	$sql = "SELECT * FROM " . $GLOBALS['ecs']->table('pay_log') . " WHERE log_id = '$log_id'";
	
	$pay_log = $GLOBALS['db']->getRow($sql);
	if(!$pay_log || $pay_log['is_paid']!=0)
		return ;//没有这条记录或已经付款了
		
		
		try {
			$GLOBALS['db']->startTrans();
			
			/* 修改此次支付操作的状态为已付款 */
			$sql = 'UPDATE ' . $GLOBALS['ecs']->table('pay_log') . " SET is_paid = '1' WHERE log_id = '$log_id'";
			$GLOBALS['db']->query($sql);
			$orderId = $pay_log['order_id'];
			
			
			
			$sql="update  ".$GLOBALS['ecs']->table('user_account')." set is_paid=1 where id=".$orderId;
			$GLOBALS['db']->query($sql);//修改账户状态为已支付
			
			
			
			$sql="update  ".$GLOBALS['ecs']->table('product_points_recharge')." set is_paid=1 where acount_log_id=".$orderId;
			$GLOBALS['db']->query($sql);//修改产品积分充值状态为已支付
			
			
			$sql ="SELECT * FROM `xbmall_product_points_recharge` where acount_log_id=".$orderId; //查询有没有推荐人, 有的话,
			
			$rechargeLog = $GLOBALS['db']->getRow($sql);//充值记录
			
			
			$sql = "select is_bigfamily from xbmall_users  where user_id = ".$rechargeLog['referee_user_id'];
			
			$isBigFamaly = $GLOBALS['db']->getOne($sql);
			
			
			if($isBigFamaly==2){//代理给1%分享奖励积分
				
				fen_xiang_jiang_li_ji_fen_log($rechargeLog['referee_user_id'], "会员购买产品积分, 您获得分享奖励积分", $pay_log['order_amount']*0.01);
				var_dump($pay_log['order_amount']*0.01);
			}
			else if($isBigFamaly==1){//商家给2%分享奖励积分
				fen_xiang_jiang_li_ji_fen_log($rechargeLog['referee_user_id'], "会员购买产品积分, 商家获得分享奖励积分", $pay_log['order_amount']*0.02);
				//查找代理, 代理得1%
				$tui_jian_ren_id = $rechargeLog['referee_user_id'];
				
				$sql="select a.parent_id , b.is_bigfamily from  xbmall_users a  join xbmall_users b  on a.parent_id = b.user_id  where a.user_id=".$tui_jian_ren_id;
				$daiLi = $GLOBALS['db']->getRow($sql);
				while ($daiLi!=null && $daiLi['is_bigfamily']!=2){
					$tui_jian_ren_id = $daiLi['parent_id'];
					$sql="select a.parent_id , b.is_bigfamily from  xbmall_users a  join xbmall_users b  on a.parent_id = b.user_id  where a.user_id=".$tui_jian_ren_id;
					$daiLi= $GLOBALS['db']->getRow($sql);
				}
				if( $daiLi!=null && $daiLi['is_bigfamily']==2){
					fen_xiang_jiang_li_ji_fen_log($daiLi['parent_id'], "会员购买产品积分, 商家的代理获得分享奖励积分", $pay_log['order_amount']*0.01);
				}
			}
			
			$GLOBALS['db']->commitTrans();//提交事物
			
		}catch (Exception $ex){
			$GLOBALS['db']->rollbackTrans();//回退事物
		}
		
		
}


/*
 * 大家庭推荐奖励发放
 */

function injoinfimaly($user_id, $type = 1 , $pay_name ='') {
	
    $type = intval($type) == 1 ? 1 : 2;
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table("users") . " WHERE user_id='" . $user_id . "'";
    $user_info = $GLOBALS['db']->getRow($sql);

    $bigfamily_u_m = $GLOBALS['_CFG']['bigfamily_u_m']; //大家庭会员金额
    $bigfamily_d_m = $GLOBALS['_CFG']['bigfamily_d_m']; //大家庭代理金额

    $fimaly_center = $GLOBALS['_CFG']['fimaly_center']; //运营中心奖励比例
    $fimaly_tui_u1 = $GLOBALS['_CFG']['fimaly_tui_u1']; //推荐大家庭会员1级推荐人奖励
    $fimaly_tui_u2 = $GLOBALS['_CFG']['fimaly_tui_u2']; //推荐大家庭会员2级推荐人奖励
    $fimaly_tui_u3 = $GLOBALS['_CFG']['fimaly_tui_u3']; //推荐大家庭会员3级代理推荐人奖励
    $fimaly_tui_d1 = $GLOBALS['_CFG']['fimaly_tui_d1']; //推荐大家庭代理1级推荐人奖励

    $bigfamily_m = $type == 1 ? $bigfamily_u_m : $bigfamily_d_m;
    $bigfamily_msg = $type == 1 ? '会员' : '代理';
   
    //获取上级推荐人
    $all_users = array();
    $yunyingzhongxin_users = 0;
    $djt_parent_id= $user_info['djt_parent_id'];
    
    if($type==1){//加入大家庭会员, 赠送两个赠送权
    	$addZsq = 2;
    	log_account_change($user_id, 0, 0, 0, 0, "会员成为香柏大家庭会员,系统赠送消费金额2000后的赠送权奖励", ACT_JRDJT, 2000, 0, 0, $addZsq, $addZsq);
    }
    
    if($type==2){//加入大家庭代理, 赠送50万奖励基数
    	log_account_change($user_id, 0, 0, 0, 0, "系统自动赠送大家庭代理奖励基数500000", ACT_JRDJT, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 500000);
    	
    	if($user_info['district']){//用户存在地区关系
    		$regionInfoSql = "SELECT user_id ,region_type,region_id FROM " . $GLOBALS['ecs']->table("region") . " WHERE region_id='" . $user_info['district']. "'";
    		$regionInfo = $GLOBALS['db']->getRow($regionInfoSql);
    		//插入
    		$insertSql= "INSERT INTO " . $GLOBALS['ecs']->table("supplier_admin_user")
    		. "(uid,user_name,email,password,ec_salt,add_time,mobile_phone,role,city_id,city_level,checked,province_id,citys_id,district_id)" .
    		" VALUES('" . $user_info['user_id'] . "','" . $user_info['user_name'] . "','" . $user_info['email'] . "','" . $user_info['password'] . "','" .
    		$user_info['ec_salt'] . "','" . time() . "','" . $user_info['mobile_phone'] . "',5,'" . $regionInfo['region_id'] . "','" .
    		$regionInfo['region_type'] . "',1,'".$user_info['province']."','".$user_info['city']."','".$user_info['district']."')";
    		//     		file_put_contents("c:/test.txt", $insertSql." DDDDD\r\n ", FILE_APPEND);
    	}else{//用户没有地区信息, 那么不存放地区信息
    		//插入
    		$insertSql = "INSERT INTO " . $GLOBALS['ecs']->table("supplier_admin_user")
    		. "(uid,user_name,email,password,ec_salt,add_time,mobile_phone,role,checked)" .
    		" VALUES('" . $user_info['user_id'] . "','" . $user_info['user_name'] . "','" . $user_info['email'] . "','" . $user_info['password'] . "','" .
    		$user_info['ec_salt'] . "','" . time() . "','" . $user_info['mobile_phone'] . "',5 ,1)";
    		//     		file_put_contents("c:/test.txt",  $insertSql." fffffffffffffffff7\r\n", FILE_APPEND);
    	
    	}
    	
    	$GLOBALS['db']->query($insertSql);
    }
    

  $i=1;
    while (1) {
    	$parent = array();
    	$user_parent = array();
    	//查询会员信息
    	$sql = "SELECT * FROM " . $GLOBALS['ecs']->table("users") . " WHERE user_id='" . $djt_parent_id. "'";
    	$user_parent = $GLOBALS['db']->getRow($sql);
    	if ($user_parent) {
    		$djt_parent_id= $user_parent['djt_parent_id'];
    	} else {//直到找不到推荐人退出
    		break;
    	}
    	$all_users[] = $user_parent;
    	
    	if( $user_parent['is_bigfamily'] ==2 ){//推荐路径里有代理,不再继续向上查找推荐人
    		break;
    	}
    	
    }
    
    //发放直接推荐人奖励, 存在推荐人 并且 推荐人必须已经加入了香柏大家庭
    if (count($all_users)>0 &&$all_users[0]['user_id'] && $all_users[0]['is_bigfamily']) {
    	//计算奖励
    	$jiangli = ($type == 1) ? $fimaly_tui_u1 : $fimaly_tui_dai_li;//根据推荐的是会员还是代理来计算
    	if($type == 1 && $all_users[0]['is_bigfamily']==2){
    		$jiangli =100;//代理推荐会员是100
    	}
    	
    	if ($jiangli && $type==1) {//只有推荐会员有线上奖励, 推荐代理的奖励转为线下了
    		$msg = "会员" . $user_info['user_name'] . "成为香柏大家庭" . $bigfamily_msg . "，您获得分享奖励积分" . $jiangli;
//     		$rs = log_account_change($all_users[0]['user_id'], 0, 0, 0, 0, $msg, ACT_REWARD_DJTTJJL, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, $jiangli, 0);
    		$rs= fen_xiang_jiang_li_ji_fen_log( $all_users[0]['user_id'],  $msg,  $jiangli);
    		
    		if (!$rs) {//资金记录如果不是1,说明出问题, 不再往下执行
    			return false;
    		}
    		
    		if($all_users[0]['is_bigfamily']==2){//如果推荐人是代理,扣除代理的奖励基数
    			reduceJiangLiJiShu($user_info, $all_users[0], $jiangli, $bigfamily_msg,ACT_REWARD_DJTTJJL);
    		}
    	}
    } 
  
    
    
    
    //     file_put_contents("c:/test.txt",  " fffffffffffffffff4\r\n", FILE_APPEND);
    //发放二级推荐人奖励   只有推荐会员有奖励
    if (count($all_users)>1 &&$all_users[1]['user_id']  && ($type == 1) && ($all_users[1]['is_bigfamily'])) {
     
    	//计算奖励
    	if($all_users[1]['is_bigfamily']==2){//二级推荐人是代理
    		$jiangli = 100;//代理的间接推荐是100
    	}else{
    		$jiangli = $fimaly_tui_u2;
    	}
    	
    	if ($jiangli) {
    		$msg = "会员" . $user_info['user_name'] . "成为香柏大家庭" . $bigfamily_msg . "，您获得分享奖励积分" . $jiangli ;
//     		$rs = log_account_change($all_users[1]['user_id'], 0, 0, 0, 0, $msg, ACT_REWARD_DJTTJJL, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, $jiangli, 0);
    		$rs= fen_xiang_jiang_li_ji_fen_log( $all_users[1]['user_id'],  $msg,  $jiangli);
    		if (!$rs) {
    			return false;
    		}
    		
    		if($all_users[1]['is_bigfamily']==2){
    			reduceJiangLiJiShu($user_info, $all_users[1], $jiangli, $bigfamily_msg,ACT_REWARD_DJTTJJL);
    		}
    		
    	}
    } 
    
    
    
    //周:发放3级推荐奖励,得第3级推荐奖励的一定是代理
    $index = 2;
    while(1){
    	if(count($all_users)<=$index){
    		break;
    	}
    	if ($all_users[$index]['user_id'] && ($type == 1) && ($all_users[$index]['is_bigfamily'] == 2)) {
    		//计算奖励
    		$jiangli = $fimaly_tui_u3;
    		if ($jiangli) {
    			
    			$msg = "会员" . $user_info['user_name'] . "成为香柏大家庭" . $bigfamily_msg . "，您获得分享奖励积分" . $jiangli ;
//     			$rs = log_account_change($all_users[$index]['user_id'], 0, 0, 0, 0, $msg, ACT_REWARD_DJTTJJL, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, $jiangli, 0);
    			$rs= fen_xiang_jiang_li_ji_fen_log( $all_users[$index]['user_id'],  $msg,  $jiangli);
    			if (!$rs) {//数据库处理出错
    				return false;
    			}
    			reduceJiangLiJiShu($user_info, $all_users[$index], $jiangli, $bigfamily_msg,ACT_REWARD_DJTTJJL);
    		}
    		break;//找到代理角色后, 退出
    	}
    	$index++;
    }
    
    
     
    
    //修改会员自己的状态
    $sql = "UPDATE " . $GLOBALS['ecs']->table('users') . " SET is_bigfamily = '" . $type . "' WHERE user_id = '" . $user_info['user_id'] . "'";
    $GLOBALS['db']->query($sql);
    
 
    //周:修改用户充值提现记录
    $user_acc = array(
            'user_id' =>  $user_info['user_id'],
            'admin_user' =>  '',
            'amount' =>  $bigfamily_m,
            'add_time' =>  gmtime(),
            'paid_time' =>  gmtime(),
            'admin_note' =>  '会员('.$user_info['user_name'].')加入大家庭,付款:'.$bigfamily_m,
            'user_note' =>   '会员('.$user_info['user_name'].')加入大家庭,付款:'.$bigfamily_m,
            'process_type' =>   0,
            'payment' =>   $pay_name ,
            'is_paid' =>   1,
            'type' =>   3,

    );
    $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('user_account'), $user_acc, 'INSERT');

    return true;
}

/**
 * 减少奖励基数
 * $userInfo用户信息
 * $tuiJianRen推荐人
 * $jiangli奖励基数
 * $bigfamily_msg会员或代理
 */
function reduceJiangLiJiShu($userInfo,$tuiJianRen,$jiangli,$bigfamily_msg,$changeType){
	//查询推荐人(代理)是否是运营中心
	$sql = "SELECT user_id FROM " . $GLOBALS['ecs']->table("region") . " WHERE region_type = 3 and user_id ='" .  $tuiJianRen['user_id']. "'";
	
	// 	file_put_contents("c:/test.txt", $sql." 推荐人是". $tuiJianRen[is_bigfamily] ."\r\n", FILE_APPEND);
	if ($GLOBALS['db']->getOne($sql) > 0 )
	{
		//扣除绑定运营中心的代理的奖励基数
		$msg = "会员" . $userInfo['user_name'] . "成为香柏大家庭" . $bigfamily_msg . "，减少" . $jiangli . "奖励基数";
		$kouchu_jljs = log_account_change($tuiJianRen['user_id'], 0, 0, 0, 0, $msg, $changeType, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, -($jiangli));
	}
	
}


?>