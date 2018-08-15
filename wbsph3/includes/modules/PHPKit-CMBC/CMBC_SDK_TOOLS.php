<?php

if (!defined('IN_ECS')) {
    die('Hacking attempt');
}
require_once ('CMBC_SDK_COMMON.php');

/*
 * CMBC统一SDK工具类
 */

class CMBC_SDK_TOOLS {

    //环境配置
    const MODEL_TEST = 0;
    const MODEL_PUBLISH = 1;
    //支付方式
    const API_WXQRCODE = "API_WXQRCODE";
    const API_ZFBQRCODE = "API_ZFBQRCODE";
    const H5_WXJSAPI = "H5_WXJSAPI";

    //环境配置
    private $mode;
    //接入平台号码
    private $plateformId;
    //商户号码
    private $merchantNo;
    //商户名称
    private $merchantName;
    //平台公钥
    private $cmbcCer;
    //商户私钥
    private $merchantCer;
    //商户私钥密码
    private $merchantCerPwd;
    //接口地址
    private $cmbcUrl;
    //签名验证签名算法
    private $signAlg;
    //加解密算法
    private $enAlg;
    //公共类
    private $sdk_common;

    function __construct($mode) {
        if ($mode == CMBC_SDK_TOOLS::MODEL_TEST) {
            $cmbc_config = require_once ('Test/config.php');
        } else {
            $cmbc_config = require_once ( 'Publish/config.php');
        }
        $this->mode = $mode;
        $this->plateformId = $cmbc_config['CMBC_SDK_PLATFORMID'];
        $this->merchantNo = $cmbc_config['CMBC_SDK_MERCHANTNO'];
        $this->cmbcCer = $cmbc_config['CMBC_SDK_CER'];
        $this->merchantCer = $cmbc_config['CMBC_MERCHANT_CER'];
        $this->merchantCerPwd = $cmbc_config['CMBC_SDK_MERCHANTCERPWD'];
        $this->cmbcUrl = $cmbc_config['CMBC_SDK_URL'];
        $this->signAlg = $cmbc_config['CMBC_SDK_SIGN_ALG'];
        $this->enAlg = $cmbc_config['CMBC_SDK_EN_ALG'];
        $this->merchantName = $cmbc_config['CMBC_SDK_MERCHANT_NAME'];
        if (empty($this->plateformId) || empty($this->merchantNo) || empty($this->cmbcCer) || empty($this->merchantCer) || empty($this->cmbcUrl) || empty($this->signAlg) || empty($this->enAlg)) {
            throw new Exception("支付模块配置异常。");
        }
        $this->sdk_common = new CMBC_SDK_COMMON();
    }

    /**
     * 写入文件日志
     * @param type $logMessage 日志信息
     * @param type $logType 日志类型
     */
    function write_log($logMessage, $logType = "pay") {
        if (strstr(ROOT_PATH, "mobile") !== false) {
            $dataLog = dirname(ROOT_PATH) . "/data/" . $logType . "/" . date("Y-m-d") . ".log";
        } else {
            $dataLog = ROOT_PATH . "/data/" . $logType . "/" . date("Y-m-d") . ".log";
        }
        error_log("当前时间：" . local_date("Y-m-d H:i:s", time()) . ",内容:" . $logMessage . "\r\n", 3, $dataLog);
    }

    /**
     * 获取指定json字符串中指定的属性值
     * @param type $jsonStr json字符串
     * @param type $element 属性键值
     * @return string 属性值
     */
    function getDataFromJsonString($jsonStr, $element) {
        if (!empty($jsonStr)) {
            $jsonArray = json_decode($jsonStr, true);
            if (isset($jsonArray[$element]) && !empty($jsonArray[$element])) {
                return $jsonArray[$element];
            }
        }
        return "";
    }

    /**
     * CURL方式POST json数据
     * @param type $url
     * @param type $jsonData
     * @return type
     */
    function curl_post_json($url, $jsonData) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData))
        );
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * 统一下单
     * @param type $selectTradeType 支付方式
     * @param type $amount 订单金额
     * @param type $orderInfo 订单描述
     * @param type $merchantSeq 订单序号
     * @param type $notifyUrl 异步通知地址
     * @param type $remark 订单备注
     * @param type $subOpenId 公众号会员openid
     * @param type $subAppId 公众号APPid
     * @return string|int 数组code=0，message为错误信息code=1,支付宝和微信为二维码地址，微信网页为配置信息字符串
     */
    public function toCreateOrder($selectTradeType, $amount, $orderInfo, $merchantSeq, $notifyUrl, $remark, $redirectUrl = "") {
        $result = array("code" => 0, "message" => "");
        //1、参数验证
        if (!in_array($selectTradeType, array(CMBC_SDK_TOOLS::API_WXQRCODE, CMBC_SDK_TOOLS::API_ZFBQRCODE, CMBC_SDK_TOOLS::H5_WXJSAPI))) {
            $result["message"] = "支付方式暂时不支持，请选择微信支付或者支付宝支付";
            return $result;
        }
        if ($amount * 1 <= 0) {
            $result["message"] = "支付金额不合法，必须是大于0的金额";
            return $result;
        }
        if ($selectTradeType == CMBC_SDK_TOOLS::H5_WXJSAPI && (empty($redirectUrl))) {
            $result["message"] = "微信配置错误或者获取微信用户信息失败";
            return $result;
        }
        //2、拼接订单json字符串
        try {
            $t1 = microtime(true);
            $t2 = microtime(true);
            $millsecond = ($t2 - $t1) * 1000;
            //测试时使用1分$amount*100
            if ($this->mode == CMBC_SDK_TOOLS::MODEL_TEST) {
                $amount = 1;
            } else {
                $amount = $amount * 100;
            }
            $millsecond = explode(".", $millsecond);
            $millsecond = $millsecond[0];
            $orderDataArray = array("platformId" => $this->plateformId, "merchantNo" => $this->merchantNo, "selectTradeType" => $selectTradeType, "amount" => $amount, "orderInfo" => $orderInfo, "merchantSeq" => $merchantSeq, "transDate" => local_date("Ymd"), "transTime" => local_date("YmdHis") . $millsecond, "notifyUrl" => $notifyUrl, "remark" => $remark);
            if ($selectTradeType == CMBC_SDK_TOOLS::H5_WXJSAPI) {
                $orderDataArray["defaultTradeType"] = CMBC_SDK_TOOLS::H5_WXJSAPI;
                $orderDataArray["merchantName"] = $this->merchantName;
                $orderDataArray["redirectUrl"] = $redirectUrl;
            }
            $orderJsonString = json_encode($orderDataArray);
            $this->write_log("订单创建开始：1、生成订单JSON：" . $orderJsonString);
            //3、签名
            $signAlg = $this->signAlg;
            $base64SourceData = base64_encode($orderJsonString);
            $base64P12Data = $this->merchantCer;
            $p12Password = $this->merchantCerPwd;
            $p1SignMessageJson = $this->sdk_common->P1SignMessage($signAlg, $base64SourceData, $base64P12Data, $p12Password);
            $this->write_log("订单创建开始：2、签名：" . $p1SignMessageJson);
            $p1SignData = $this->getDataFromJsonString($p1SignMessageJson, "Base64SignatureData");
            if (empty($p1SignData)) {
                $result["message"] = "生成订单失败：签名生成错误。";
                return $result;
            }
            //4、生成业务报文，加密业务报文
            $orderBwDataArray = array("sign" => $p1SignData, "body" => $orderJsonString);
            $orderBwDataJson = json_encode($orderBwDataArray);

            $signAlg = $this->enAlg;
            $base64SourceData = $orderBwDataJson;
            $base64CertData = $this->cmbcCer;
            $jmBwDataJson = $this->sdk_common->envelopeMessage($signAlg, $base64SourceData, $base64CertData);
            $this->write_log("订单创建开始：3、生成业务报文：" . $jmBwDataJson);
            $jmBwData = $this->getDataFromJsonString($jmBwDataJson, "Base64EnvelopeMessage");
            if (empty($jmBwData)) {
                $result["message"] = "生成订单失败：加密报文错误。";
                return $result;
            }
            if ($selectTradeType == CMBC_SDK_TOOLS::H5_WXJSAPI) {
                $result["data"] = $this->cmbcUrl . 'cmbcPayweb.do?context=' . $jmBwData;
            } else {
                //5、准备订单报文数据，提交订单
                $orderBwDataArray = array("businessContext" => $jmBwData, "merchantNo" => "", "merchantSeq" => "", "reserve1" => "", "reserve2" => "", "reserve3" => "", "reserve4" => "", "reserve5" => "", "reserveJson" => "",
                    "securityType" => "", "sessionId" => "", "source" => "", "transCode" => "", "transDate" => "", "transTime" => "", "version" => "");
                $orderBwJsonData = json_encode($orderBwDataArray);
                $orderJsonString = $this->curl_post_json($this->cmbcUrl . 'appserver/lcbpPay.do', $orderBwJsonData); //businessContext
                $this->write_log("订单创建开始：4、提交订单：" . $orderJsonString);
                $orderBusinessContext = $this->getDataFromJsonString($orderJsonString, "businessContext");
                if (empty($orderBusinessContext)) {
                    $result["message"] = "生成订单失败:生成订单接口返回错误。";
                    return $result;
                }
                //6、解密订单报文数据
                $base64EnvelopeData = $orderBusinessContext;
                $signAlg = $this->enAlg;
                $base64P12Data = $this->merchantCer;
                $p12Password = $this->merchantCerPwd;
                $orderJmJsonString = $this->sdk_common->openEvelopedMessage($signAlg, $base64EnvelopeData, $base64P12Data, $p12Password);
                $this->write_log("订单创建开始：5、解密订单报文数据：" . $orderJmJsonString);
                $orderJmData = $this->getDataFromJsonString($orderJmJsonString, "Base64SourceString");
                if (empty($orderJmData)) {
                    $result["message"] = "生成订单失败:订单解密返回错误。";
                    return $result;
                }
                //7、验证签名
                $bs64DecodeData = json_decode(base64_decode($orderJmData), true);
                $signAlg = $this->signAlg;
                $base64SourceData = base64_encode($bs64DecodeData['body']);
                $base64X509CertData = $this->cmbcCer;
                $base64P1SignatureData = $bs64DecodeData['sign'];
                $vertifyMessageJson = $this->sdk_common->P1VerifyMessage($signAlg, $base64SourceData, $base64X509CertData, $base64P1SignatureData);
                $this->write_log("订单创建开始：6、验证签名：" . $vertifyMessageJson);
                $vertifyMessage = $this->getDataFromJsonString($vertifyMessageJson, "Result");
                if ($vertifyMessage != "True") {
                    $result["message"] = "生成订单失败:订单验证失败错误。";
                    return $result;
                }
                //8、根据不同的支付方式返回支付信息
                $this->write_log("订单创建开始：7、支付信息：" . $bs64DecodeData['body']);
                $bodyInfoArray = json_decode($bs64DecodeData['body'], true);
                if ($selectTradeType == CMBC_SDK_TOOLS::API_WXQRCODE || $selectTradeType == CMBC_SDK_TOOLS::API_ZFBQRCODE) {
                    $result["data"] = base64_decode($bodyInfoArray['payInfo']);
                } else {
                    $result["data"] = $bodyInfoArray['payInfo'];
                }
            }
            $result["code"] = 1;
        } catch (Exception $ex) {
            $this->write_log("订单创建：发生异常：" . $ex->getMessage() . ",异常码：" . $ex->getCode() . ",TraceString:" . $ex->getTraceAsString());
            $result["message"] = "订单创建异常";
        }
        return $result;
    }

    /**
     * 订单异步通知处理
     * @param type $jsonStr 异步通知报文数据
     * @return string|int 数组code=0，message为错误信息code=1,返回成功
     */
    public function toNotifyOrder($jsonStr) {
        $result = array("code" => 0, "message" => "");
        $this->write_log("订单异步通知开始：" . $jsonStr, "notify");
        //1、参数校验
        if (empty($jsonStr)) {
            $result["message"] = "参数错误。";
            return $result;
        }
        try {
            $contextData = $this->getDataFromJsonString($jsonStr, "context");
            //2、解密
            $base64EnvelopeData = $contextData;
            $signAlg = $this->enAlg;
            $base64P12Data = $this->merchantCer;
            $p12Password = $this->merchantCerPwd;
            $orderJmJsonString = $this->sdk_common->openEvelopedMessage($signAlg, $base64EnvelopeData, $base64P12Data, $p12Password);
            $this->write_log("订单异步通知开始：1、解密" . $orderJmJsonString, "notify");
            $orderJmData = $this->getDataFromJsonString($orderJmJsonString, "Base64SourceString");
            if (empty($orderJmData)) {
                $result["message"] = "订单返回处理失败:订单解密返回错误。";
                return $result;
            }
            //3、验证签名
            $bs64DecodeData = json_decode(base64_decode($orderJmData), true);
            $signAlg = $this->signAlg;
            $base64SourceData = base64_encode($bs64DecodeData['body']);
            $base64X509CertData = $this->cmbcCer;
            $base64P1SignatureData = $bs64DecodeData['sign'];
            $vertifyMessageJson = $this->sdk_common->P1VerifyMessage($signAlg, $base64SourceData, $base64X509CertData, $base64P1SignatureData);
            $this->write_log("订单异步通知开始：2、验证签名" . $vertifyMessageJson, "notify");
            $vertifyMessage = $this->getDataFromJsonString($vertifyMessageJson, "Result");
            if ($vertifyMessage != "True") {
                $result["message"] = "订单返回处理失败:订单验证失败错误。";
                return $result;
            }
            //4、返回结果处理
            //商户号	merchantNo	商户号
            //商户订单号	orderNo	原交易商户订单号
            //平台号	platformId	平台号
            //交易金额	amount	单位到分
            //凭证号	voucherNo	收单凭证号
            //银行流水号	bankTradeNo	收单系列流水号
            //交易结果	tradeStatus	收单系统流水号 S 订单交易成功
            //保留域	reserve	
            //参考号	refNo	
            //批次号	batchNo	
            //卡类型	cardType	>0借记卡 1贷记卡
            //卡号	cardNo	前六后四中间*
            //发卡行行号	cbCode	
            //发卡行行名	cardName	
            //交易手续费	fee	
            //银联终端号	cupTermId	
            //设备序列号	cupTsamNo	
            //其他信息	centerInfo	
            //微信：openid=ooEyIjvNwCKprHj_hwnayDWjy5Ks|is_subscribe=Y|bank_type=ICBC_DEBIT|time_end=20160912164155
            //支付宝： buyer_logon_id=182****7122|buyer_id=2088802699232296|app_id=2015051100069170|seller_id=2088911212416201|seller_email=zfbtest25@service.aliyun.com|payeeSeq=201609122100 1004290226429948|tradeNo=2016091221001004290226429948|fund_bill_list=[{\"amount\":\"0.01\",\"fundChannel\":\"ALIPAYACCOUNT\"}]|
            //微信/支付宝订单号	centerSeqId	
            //收单流水号	serialNo	
            //收单到微信/支付宝下单编号	bankOrderNo	
            $bodyInfoArray = json_decode($bs64DecodeData['body'], true);
            $this->write_log("订单异步通知开始：3、返回数据" . $bs64DecodeData['body'], "notify");
            if ($bodyInfoArray['tradeStatus'] == "S") {
                //TODO:执行订单状态以及其他业务逻辑处理
                $result['code'] = 1;
                $order_type = get_order_type($bodyInfoArray['orderNo']);
                if ($order_type * 1 == 2 || $order_type * 1 == 3) {
                    mutual_order_paid($bodyInfoArray['orderNo']);
                } elseif ($order_type * 1 == 4) {
                    dajiating_order_paid($bodyInfoArray['orderNo'] , $bodyInfoArray['centerInfo']);
                } else {
                    order_paid($bodyInfoArray['orderNo'], 2);
                }
                $result['message'] = "执行成功";
            }
        } catch (Exception $ex) {
            $this->write_log("订单异步通知异常：发生异常：" . $ex->getMessage() . ",异常码：" . $ex->getCode() . ",TraceString:" . $ex->getTraceAsString());
            $result["message"] = "订单异步通知异常";
        }
        return $result;
    }

    public function toGetPayInfo($selectTradeType, $payInfo, $wx_config = array()) {
        if ($selectTradeType == CMBC_SDK_TOOLS::API_WXQRCODE || $selectTradeType == CMBC_SDK_TOOLS::API_ZFBQRCODE) {
            if ($selectTradeType == CMBC_SDK_TOOLS::API_WXQRCODE) {
                $button = '<div id="paymentDiv"><div style="text-align:center" id="qrcode"></div><div id="wxPhone"></div></div>';
                $payment_path = $GLOBALS['ecs']->get_domain() . '/includes/modules/payment/wxnative/';
                $javascript = '<style>#paymentDiv{width:760px}#wxPhone{float:left;width:379px;height:421px;padding-left:50px;background:url(' . $payment_path . 'phone-bg.png) 50px 0 no-repeat}#qrcode{display:block;float:left;margin-top:30px}#qrcode img{height:260px;width:260px;padding:5px;border:1px solid #ddd}#qrcode p{padding:15px 0;background:#157058;color:#fff;margin:10px 0}</style> ';
                $javascript.='<script src="' . $payment_path . 'qrcode.js"></script>';
                //参数1表示图像大小，取值范围1-10；参数2表示质量，取值范围'L','M','Q','H'
                $javascript.='<script>
           if("' . $payInfo . '"!==""){
                var url = "' . $payInfo . '";
                var qr = qrcode(10, "M");
                qr.addData(url);
                qr.make();
                var wording=document.createElement("p");
                wording.innerHTML = "微信扫描，立即支付";
                var code=document.createElement("DIV");
                code.innerHTML = qr.createImgTag();
                var element=document.getElementById("qrcode");
                element.appendChild(wording);
                element.appendChild(code);
                                 }</script>';
                return $button . $javascript;
            } else {
//                require(ROOT_PATH . '/includes/phpqrcode.php');
//                $errorCorrectionLevel = 'L'; //容错级别  
//                $matrixPointSize = 6; //生成图片大小  
//                //生成二维码图片  
//                $time = gmtime();
//                QRcode::png($payInfo, 'qrcode_' . $time . '.png', $errorCorrectionLevel, $matrixPointSize, 2);
                $img = "http://qr.liantu.com/api.php?text=" . $payInfo;
                $javascript = "<script>function showZfb(mark){
                            if(mark===0)
                            {
                            document.getElementById('qrguide_active').style='display:block';
                            document.getElementById('qrguide_background').style='display:none';
                            }
                            else
                            {
                            document.getElementById('qrguide_background').style='display:block';
                            document.getElementById('qrguide_active').style='display:none';
                            }
                       }</script>";
                $button = '<div id="paymentDiv"><div style="text-align:center" id="qrcode"><p>支付宝扫描，立即支付</p><img src="' . $img . '" /></div><div id="zfbPhone"><img src="' . $GLOBALS['ecs']->get_domain() . '/images/pay/zfb1.png" id="qrguide_background" onclick="showZfb(0);" /><img src="' . $GLOBALS['ecs']->get_domain() . '/images/pay/zfb2.png" id="qrguide_active"  onclick="showZfb(1);"/></div></div>';
                $javascript.= '<style>#paymentDiv{width:760px}#zfbPhone{margin-top:80px;float:left;width:379px;height:421px;padding-left:50px;}#qrcode{display:block;float:left;margin-top:30px}#qrcode img{height:260px;width:260px;padding:5px;border:1px solid #ddd}#qrcode p{padding:15px 0;background:#157058;color:#fff;margin:10px 0}#qrguide_background{display:none;}#qrguide_active{display:block;}</style> ';
                if (strstr(ROOT_PATH, "mobile") !== false) {
                    return '<div style="text-align:center;width:90%" id="qrcode"><img style="width:100%" src="' . $img . '" /><p  style="background:#157058;color:#fff">支付宝扫描，立即支付</p></div>';
                } else {
                    return $button . $javascript;
                }
            }
        } else {
            return "请求参数不支持";
            //1、解析参数
//            $wxParams = explode("|", $payInfo);
//            //拿到微信需要数据
//            //appId, timeStamp, nonceStr, package, signType
//            //prepayId=wx201704050901166985e1327e0277539055|timeStamp=1491354076302|nonceStr=bkib0gu7ius61m7ezisxfn7hhlbqcei2|paySign=3958851BC2D323D8890FF3898040C1EB|appId=wx2b0ad640ef47938b|backFunc=backFunc
//            $timestampArr = explode("=", $wxParams[1]);
//            $timestamp = $timestampArr[1];
//            $nonceStrArr = explode("=", $wxParams[2]);
//            $nonceStr = $nonceStrArr[1];
//            $prepayIdArr = explode("=", $wxParams[0]);
//            $prepayId = $prepayIdArr[1];
//            $paySignArr = explode("=", $wxParams[3]);
//            $paySign = $paySignArr[1];
//            if (count($wxParams) == 6) {
//                $backFuncArr = explode("=", $wxParams[5]);
//                $backFun = $backFuncArr[1];
//            }
//            $javascript = "<script src='http://res.wx.qq.com/open/js/jweixin-1.2.0.js'></script>";
//            $javascript .= "
//            wx.config({
//    debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
//    appId: '$wx_config[appId]', // 必填，公众号的唯一标识
//    timestamp:$wx_config[timestamp] , // 必填，生成签名的时间戳
//    nonceStr: '$wx_config[nonceStr]', // 必填，生成签名的随机串
//    signature: '$wx_config[signature]',// 必填，签名，见附录1
//    jsApiList: ['chooseWXPay'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
//});";
//            if (empty($backFun)) {
//                $javascript.= "<script>wx.chooseWXPay({
//    timestamp: $timestamp, // 支付签名时间戳，注意微信jssdk中的所有使用timestamp字段均为小写。但最新版的支付后台生成签名使用的timeStamp字段名需大写其中的S字符
//    nonceStr: '$nonceStr', // 支付签名随机串，不长于 32 位
//    package: 'prepay_id=$prepayId', // 统一支付接口返回的prepay_id参数值，提交格式如：prepay_id=***）
//    signType: 'SHA1', // 签名方式，默认为'SHA1'，使用新版支付需传入'MD5'
//    paySign: '$paySign', // 支付签名
//    success: function (res) {
//        // 支付成功后的回调函数
//    }
//});</script>";
//            } else {
//                $javascript.= "<script>wx.chooseWXPay({
//    timestamp: $timestamp, // 支付签名时间戳，注意微信jssdk中的所有使用timestamp字段均为小写。但最新版的支付后台生成签名使用的timeStamp字段名需大写其中的S字符
//    nonceStr: '$nonceStr', // 支付签名随机串，不长于 32 位
//    package: 'prepay_id=$prepayId', // 统一支付接口返回的prepay_id参数值，提交格式如：prepay_id=***）
//    signType: 'SHA1', // 签名方式，默认为'SHA1'，使用新版支付需传入'MD5'
//    paySign: '$paySign', // 支付签名
//    success: function (res) {
//        // 支付成功后的回调函数
//                $backFun(res);
//    }
//});</script>";
//            }
//            return $javascript;
        }
    }

    /**
     * 查询订单
     * @param type $merchantSeq 订单支付编号
     * @param type $tradeType 查询类型1订单2退货
     * @param type $orgvoucherNo 退货单号
     * @return string
     */
    public function toSelectOrder($merchantSeq, $tradeType = 1, $orgvoucherNo = "") {
        $result = array("code" => 0);
        $queryArray = array("platformId" => $this->plateformId, "merchantNo" => $this->merchantNo, "merchantSeq" => $merchantSeq, "tradeType" => $tradeType, "orgvoucherNo" => $orgvoucherNo, "reserve" => "");
        //1、生成JSON
        $queryJsonData = json_encode($queryArray);
        $this->write_log("查询订单开始：1、生成JSON：" . $queryJsonData);
        //2、签名
        $signAlg = $this->signAlg;
        $base64SourceData = base64_encode($queryJsonData);
        $base64P12Data = $this->merchantCer;
        $p12Password = $this->merchantCerPwd;
        $p1SignMessageJson = $this->sdk_common->P1SignMessage($signAlg, $base64SourceData, $base64P12Data, $p12Password);

        $this->write_log("查询订单开始：2、签名：" . $p1SignMessageJson);
        $p1SignData = $this->getDataFromJsonString($p1SignMessageJson, "Base64SignatureData");
        if (empty($p1SignData)) {
            $result["message"] = "查询订单开始：签名生成错误。";
            return $result;
        }
        //3、生成业务报文，加密业务报文
        $orderBwDataArray = array("sign" => $p1SignData, "body" => $queryJsonData);
        $orderBwDataJson = json_encode($orderBwDataArray);
        $signAlg = $this->enAlg;
        $base64SourceData = $orderBwDataJson;
        $base64CertData = $this->cmbcCer;
        $jmBwDataJson = $this->sdk_common->envelopeMessage($signAlg, $base64SourceData, $base64CertData);
        $this->write_log("查询订单开始：3、生成业务报文：" . $jmBwDataJson);
        $jmBwData = $this->getDataFromJsonString($jmBwDataJson, "Base64EnvelopeMessage");
        if (empty($jmBwData)) {
            $result["message"] = "查询订单开始：加密报文错误。";
            return $result;
        }
        //4、准备订单报文数据，查询订单
        $orderBwDataArray = array("businessContext" => $jmBwData, "merchantNo" => "", "merchantSeq" => "", "reserve1" => "", "reserve2" => "", "reserve3" => "", "reserve4" => "", "reserve5" => "", "reserveJson" => "",
            "securityType" => "", "sessionId" => "", "source" => "", "transCode" => "", "transDate" => "", "transTime" => "", "version" => "");
        $orderBwJsonData = json_encode($orderBwDataArray);
        $orderJsonString = $this->curl_post_json($this->cmbcUrl . 'appserver/paymentResultSelect.do', $orderBwJsonData); //businessContext
        $this->write_log("查询订单开始：4、查询订单：" . $orderJsonString);
        $orderBusinessContext = $this->getDataFromJsonString($orderJsonString, "businessContext");
        if (empty($orderBusinessContext)) {
            $result["message"] = "查询订单开始:生成订单接口返回错误。";
            return $result;
        }
        //5、解密订单报文数据
        $base64EnvelopeData = $orderBusinessContext;
        $signAlg = $this->enAlg;
        $base64P12Data = $this->merchantCer;
        $p12Password = $this->merchantCerPwd;
        $orderJmJsonString = $this->sdk_common->openEvelopedMessage($signAlg, $base64EnvelopeData, $base64P12Data, $p12Password);
        $this->write_log("查询订单开始：5、解密订单报文数据：" . $orderJmJsonString);
        $orderJmData = $this->getDataFromJsonString($orderJmJsonString, "Base64SourceString");
        if (empty($orderJmData)) {
            $result["message"] = "查询订单失败:订单解密返回错误。";
            return $result;
        }
        //6、验证签名
        $bs64DecodeData = json_decode(base64_decode($orderJmData), true);
        $signAlg = $this->signAlg;
        $base64SourceData = base64_encode($bs64DecodeData['body']);
        $base64X509CertData = $this->cmbcCer;
        $base64P1SignatureData = $bs64DecodeData['sign'];
        $vertifyMessageJson = $this->sdk_common->P1VerifyMessage($signAlg, $base64SourceData, $base64X509CertData, $base64P1SignatureData);
        $this->write_log("查询订单开始：6、验证签名：" . $vertifyMessageJson);
        $vertifyMessage = $this->getDataFromJsonString($vertifyMessageJson, "Result");
        if ($vertifyMessage != "True") {
            $result["message"] = "查询订单失败:订单验证失败错误。";
            return $result;
        }
        //7、根据不同的支付方式返回支付信息
        $this->write_log("查询订单开始：7、支付信息：" . $bs64DecodeData['body']);
        $orderInfo = json_decode($bs64DecodeData["body"], true);
        if ($orderInfo['tradeStatus'] == "S") {
            return $orderInfo;
        }
        return $orderInfo;
    }
    /**
     * 取消订单
     * @param type $merchantSeq 订单号
     * @param type $orderAmount 订单金额
     * @param type $orderNote 订单说明
     * @param type $reserve 订单备注
     * @return string
     */
    public function toCancelOrder($merchantSeq, $orderAmount, $orderNote = "", $reserve = "") {
        $result = array("code" => 0);
        $queryArray = array("platformId" => $this->plateformId, "merchantNo" => $this->merchantNo, "merchantSeq" => $merchantSeq,"orderAmount"=>$orderAmount,
            "orderNote" => $orderNote, "reserve" =>$reserve);
        //1、生成JSON
        $queryJsonData = json_encode($queryArray);
        $this->write_log("取消订单开始：1、生成JSON：" . $queryJsonData);
        //2、签名
        $signAlg = $this->signAlg;
        $base64SourceData = base64_encode($queryJsonData);
        $base64P12Data = $this->merchantCer;
        $p12Password = $this->merchantCerPwd;
        $p1SignMessageJson = $this->sdk_common->P1SignMessage($signAlg, $base64SourceData, $base64P12Data, $p12Password);

        $this->write_log("取消订单开始：2、签名：" . $p1SignMessageJson);
        $p1SignData = $this->getDataFromJsonString($p1SignMessageJson, "Base64SignatureData");
        if (empty($p1SignData)) {
            $result["message"] = "取消订单开始：签名生成错误。";
            return $result;
        }
        //3、生成业务报文，加密业务报文
        $orderBwDataArray = array("sign" => $p1SignData, "body" => $queryJsonData);
        $orderBwDataJson = json_encode($orderBwDataArray);
        $signAlg = $this->enAlg;
        $base64SourceData = $orderBwDataJson;
        $base64CertData = $this->cmbcCer;
        $jmBwDataJson = $this->sdk_common->envelopeMessage($signAlg, $base64SourceData, $base64CertData);
        $this->write_log("取消订单开始：3、生成业务报文：" . $jmBwDataJson);
        $jmBwData = $this->getDataFromJsonString($jmBwDataJson, "Base64EnvelopeMessage");
        if (empty($jmBwData)) {
            $result["message"] = "取消订单开始：加密报文错误。";
            return $result;
        }
        //4、准备订单报文数据，查询订单
        $orderBwDataArray = array("businessContext" => $jmBwData, "merchantNo" => "", "merchantSeq" => "", "reserve1" => "", "reserve2" => "", "reserve3" => "", "reserve4" => "", "reserve5" => "", "reserveJson" => "",
            "securityType" => "", "sessionId" => "", "source" => "", "transCode" => "", "transDate" => "", "transTime" => "", "version" => "");
        $orderBwJsonData = json_encode($orderBwDataArray);
        $orderJsonString = $this->curl_post_json($this->cmbcUrl . 'appserver/cancelTrans.do', $orderBwJsonData); //businessContext
        $this->write_log("取消订单开始：4、取消订单：" . $orderJsonString);
        $orderBusinessContext = $this->getDataFromJsonString($orderJsonString, "businessContext");
        if (empty($orderBusinessContext)) {
            $result["message"] = "取消订单开始:生成订单接口返回错误。";
            return $result;
        }
        //5、解密订单报文数据
        $base64EnvelopeData = $orderBusinessContext;
        $signAlg = $this->enAlg;
        $base64P12Data = $this->merchantCer;
        $p12Password = $this->merchantCerPwd;
        $orderJmJsonString = $this->sdk_common->openEvelopedMessage($signAlg, $base64EnvelopeData, $base64P12Data, $p12Password);
        $this->write_log("取消订单开始：5、解密订单报文数据：" . $orderJmJsonString);
        $orderJmData = $this->getDataFromJsonString($orderJmJsonString, "Base64SourceString");
        if (empty($orderJmData)) {
            $result["message"] = "取消订单失败:订单解密返回错误。";
            return $result;
        }
        //6、验证签名
        $bs64DecodeData = json_decode(base64_decode($orderJmData), true);
        $signAlg = $this->signAlg;
        $base64SourceData = base64_encode($bs64DecodeData['body']);
        $base64X509CertData = $this->cmbcCer;
        $base64P1SignatureData = $bs64DecodeData['sign'];
        $vertifyMessageJson = $this->sdk_common->P1VerifyMessage($signAlg, $base64SourceData, $base64X509CertData, $base64P1SignatureData);
        $this->write_log("取消订单开始：6、验证签名：" . $vertifyMessageJson);
        $vertifyMessage = $this->getDataFromJsonString($vertifyMessageJson, "Result");
        if ($vertifyMessage != "True") {
            $result["message"] = "取消订单失败:订单验证失败错误。";
            return $result;
        }
        //7、根据不同的支付方式返回支付信息
        $this->write_log("取消订单开始：7、支付信息：" . $bs64DecodeData['body']);
        $orderInfo = json_decode($bs64DecodeData["body"], true);
        if ($orderInfo['tradeStatus'] == "S") {
            return $orderInfo;
        }
        return $orderInfo;
    }

}
