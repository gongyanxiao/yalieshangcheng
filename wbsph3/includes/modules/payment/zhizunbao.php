<?php

/**
 * ECSHOP 至尊宝插件
 * ============================================================================
 * 版权所有 2005-2011 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: kuaiqian.php 17217 2011-01-19 06:29:08Z liubo $
 */
if (!defined('IN_ECS')) {
    die('Hacking attempt');
}

$payment_lang = ROOT_PATH . 'languages/' . $GLOBALS['_CFG']['lang'] . '/payment/zhizunbao.php';

if (file_exists($payment_lang)) {
    global $_LANG;

    include_once($payment_lang);
}

/**
 * 模块信息
 */
if (isset($set_modules) && $set_modules == true) {
    $i = isset($modules) ? count($modules) : 0;

    /* 代码 */
    $modules[$i]['code'] = basename(__FILE__, '.php');

    /* 描述对应的语言项 */
    $modules[$i]['desc'] = 'zhizunbao_desc';

    /* 是否支持货到付款 */
    $modules[$i]['is_cod'] = '0';

    /* 是否支持在线支付 */
    $modules[$i]['is_online'] = '1';

    /* 作者 */
    $modules[$i]['author'] = 'ECSHOP TEAM';

    /* 网址 */
    $modules[$i]['website'] = 'http://www.99bill.com';

    /* 版本号 */
    $modules[$i]['version'] = '1.2';

    /* 配置信息 */
    $modules[$i]['config'] = array(
        array('name' => 'zhizunbao_account', 'type' => 'text', 'value' => ''),
        array('name' => 'zhizunbao_key', 'type' => 'text', 'value' => ''),
    );

    return;
}

class zhizunbao {

    /**
     * 构造函数
     *
     * @access  public
     * @param
     *
     * @return void
     */
    function __construct() {
        $this->zhizunbao();
    }

    function zhizunbao() {
        
    }

 
    /**
     * 生成支付代码
     * @param   array   $order  订单信息
     * @param   array   $payment    支付方式信息
     */
    function get_code($order, $payment, $notifyUrl, $paySrc = "MP", $liqType = "T0") {
        /* 生成加密签名串 请务必按照如下顺序和规则组成加密串！ */
//     	$merId = "M1705170104709";
//     	$merId = "M1705170104709";
    	$merId = "M1710130106581";
        $userId = $order["user_id"];
        $transSeqId = $order['log_id'];
        $transAmt = sprintf("%.2f", $order['order_amount']);
        $merPriv = md5($order['order_id'] . $order['log_id']);
        $gateId = $payment;
        $retUrl = $notifyUrl;
        $bgRetUrl = $GLOBALS['ecs']->url() . 'testNotify.php';
//         $key = "I4rpDZrwiI";
        $key = "LMVPhAQzBc";
        $signmsg = (md5($merId . $userId . $transSeqId . $transAmt . $merPriv . $gateId . $liqType . $retUrl . $bgRetUrl . $key));    //签名字符串 不可空

        $data = array("merId" => $merId, "user_id" => $userId, "transSeqId" => $transSeqId, "transAmt" => $transAmt, "merPriv" => $merPriv, "gateId" => $gateId, "retUrl" => $retUrl, "bgRetUrl" => $bgRetUrl, "key" => $key,
            "signmsg" => $signmsg, "paySrc" => $paySrc, "liqType" => $liqType);
        if (strstr(ROOT_PATH, "mobile") !== false) {
            $dataLog = dirname(ROOT_PATH) . "/data/pay/" . date("Y-m-d") . ".log";
        } else {
            $dataLog = ROOT_PATH . "/data/pay/" . date("Y-m-d") . ".log";
        }
        error_log("生成订单信息：" . json_encode($data), 3, $dataLog);
      
        $def_url ="<script language=\"javascript\" type=\"text/javascript\">function myBeforeSubmit(){".
         "if(window.parent.myBeforeSubmit) {window.parent.myBeforeSubmit();}".
         " document.getElementById(\"payForm\").submit();    }</script>";
        $def_url .= '<div style="text-align:center"><form name=\"form\" id=\"payForm\"   action="http://bao.appfu.net/mobile/ss/gatePayPage.do" method="post" OnSubmit="true">';
        $def_url .= "<input type=hidden name=\"merId\" value='" . $merId . "'>";
        $def_url .= "<input type=hidden name=\"userId\" value=" . $userId . ">";
        $def_url .= "<input type=hidden name=\"transSeqId\" value=" . $transSeqId . ">";
        $def_url .= "<input type=hidden name=\"transAmt\" value=" . $transAmt . ">";
        $def_url .= "<input type=hidden name=\"merPriv\" value=" . $merPriv . ">";
        $def_url .= "<input type=hidden name=\"gateId\" value=" . $gateId . ">";
        $def_url .= "<input type=hidden name=\"retUrl\" value='" . $retUrl . "'>";
        $def_url .= "<input type=hidden name=\"bgRetUrl\" value='" . $bgRetUrl . "'>";
        $def_url .= "<input type=hidden name=\"chkValue\" value=" . $signmsg . ">";
        $def_url .= "<input type=hidden name=\"paySrc\" value=" . $paySrc . ">";
        $def_url .= "<input type=hidden name=\"liqType\" value=" . $liqType . ">";
        $def_url .= "<input type='submit' value='去支付' onclick=\"myBeforeSubmit()\" name='submit' style='line-height: 12px;font-size:12px;display: inline-block;text-align: center;text-decoration: none;color: #fff;background-color: #E31939;overflow: visible;border: 0 none;outline: 0;padding: 7px;cursor: pointer'  />";
        $def_url .= "</form></div></br>";
        return $def_url;
    }

    /**
     * 响应操作
     */
    function respond() {
        $merId = $_REQUEST["merId"];    //商户号
        $transSeqId = $_REQUEST["transSeqId"];          //交易流水号
        $transAmt = $_REQUEST["transAmt"];     //交易金额
        $transFee = $_REQUEST["transFee"];        //交易手续费
        $merPriv = $_REQUEST["merPriv"];     //商户私有域
        $gateId = $_REQUEST["gateId"];      //网关
        $transStat = $_REQUEST["transStat"];      //交易状态
        $failCase = $_REQUEST["failCase"];      //失败原因
        $chkValue = $_REQUEST["chkValue"];        //签名
//         $md5Key = "I4rpDZrwiI";
        $md5Key = "LMVPhAQzBc";
        $MsgData = $merId . $transSeqId . $transAmt . $transFee . $merPriv . $gateId . $transStat . $failCase . $md5Key;
        if (strstr(ROOT_PATH, "mobile") !== false) {
            $dataLog = dirname(ROOT_PATH) . "/data/pay/" . date("Y-m-d") . ".log";
        } else {
            $dataLog = ROOT_PATH . "/data/pay/" . date("Y-m-d") . ".log";
        }
        error_log("异步回调信息：" . json_encode($_REQUEST), 3, $dataLog);
        $newChkValue = strtoupper(md5($MsgData));
      
        //首先对获得的商户号进行比对
        if ($merId != "M1710130106581") {
            //商户号错误
            return false;
        }
 
        if ($newChkValue == $chkValue) {
            if ($transStat == "S") {
                //交易成功
                //根据订单号 进行相应业务操作
                //在些插入代码
//                order_paid($transSeqId);
                $order_type = get_order_type($transSeqId);
                
                if ($order_type * 1 == 2 || $order_type * 1 == 3) {
                    mutual_order_paid($transSeqId);
                } elseif ($order_type * 1 == 4) {
                    dajiating_order_paid($transSeqId, "");
                }elseif ($order_type * 1 == 5) {
                	//产品积分充值
                	chan_pin_order_paid($transSeqId);
                }else {
                    order_paid($transSeqId, 2);
                }
                error_log("异步回调信息,支付流水号transSeqId：" . $transSeqId . "支付成功。\r\n", 3, $dataLog);
                return "RECV_".$transSeqId;
            } else {
                //交易失败
                //根据订单号 进行相应业务操作
                //在些插入代码
                return false;
            }
        } else {
            return false;
            //验签失败
        }
    }

    /**
     * 将变量值不为空的参数组成字符串
     * @param   string   $strs  参数字符串
     * @param   string   $key   参数键名
     * @param   string   $val   参数键对应值
     */
    function append_param($strs, $key, $val) {
        if ($strs != "") {
            if ($key != '' && $val != '') {
                $strs .= '&' . $key . '=' . $val;
            }
        } else {
            if ($val != '') {
                $strs = $key . '=' . $val;
            }
        }
        return $strs;
    }

}

?>