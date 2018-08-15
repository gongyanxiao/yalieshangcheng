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
if (! defined ( 'IN_ECS' )) {
	die ( 'Hacking attempt' );
}
file_put_contents ( dirname ( __FILE__ ) . "/test.txt", "进入zhifutong\r\n", FILE_APPEND );
class zhifutong {
	public $test = "test";
	/**
	 * 构造函数
	 *
	 * @access public
	 * @param        	
	 *
	 * @return void
	 */
	function __construct() {
		$this->zhifutong ();
	}
	function zhifutong() {
	}
	
	/**
	 * 生成支付代码
	 *
	 * @param array $order
	 *        	订单信息
	 * @param array $payment
	 *        	支付方式信息
	 */
	public function get_code($order, $payType, $pickupUrl, $paySrc = "MP", $liqType = "T0") {
		$smarty = $GLOBALS ['smarty'];
		// 页面编码要与参数inputCharset一致，否则服务器收到参数值中的汉字为乱码而导致验证签名失败。
		// $serverUrl = $_POST ["serverUrl"];
		
		$inputCharset = "1";
		
		$smarty->assign ( 'inputCharset', $inputCharset );
		$receiveUrl = "http://shop.xiangbai315.com/testNotify2.php";
		$smarty->assign ( 'receiveUrl', $receiveUrl ); // 取货地址(交易结果后台通知地址)
		$smarty->assign ( 'pickupUrl', $pickupUrl ); // 取货地址
		$version = "v1.0";
		$smarty->assign ( 'version', $version );
		$language = "1"; // utf-8
		$smarty->assign ( 'language', $language );
		$signType = "1";
		$smarty->assign ( 'signType', $signType ); // 签名类型
		$merchantId = "360849";
		$smarty->assign ( 'merchantId', $merchantId ); // 商户号
		
		$payerName = "付款人姓名";
		$smarty->assign ( 'payerName', $payerName ); // 付款人姓名
		$payerEmail = "付款人联系email";
		$smarty->assign ( 'payerEmail', $payerEmail );
		
		$payerTelephone = "15910142205";
		$smarty->assign ( 'payerTelephone', $payerTelephone );
		$orderNo = $order ['log_id'];
		$smarty->assign ( 'orderNo', $orderNo );
		
		$orderAmount = $order ['order_amount'];
		$orderAmount = ($orderAmount*100);
		$smarty->assign ( 'orderAmount', $orderAmount );
		
		$orderDatetime = '20170621170638'; // 商户的订单提交时间
		$smarty->assign ( 'orderDatetime', $orderDatetime ); // 订单提交时间
		
		$orderCurrency = '156'; // 人民币
		$smarty->assign ( 'orderCurrency', $orderCurrency ); // 订单金额币种类型
		
		$orderExpireDatetime = ''; // 订单过期时间
		$smarty->assign ( 'orderExpireDatetime', $orderExpireDatetime ); // 订单金额币种类型
		
		$productName = '商品';
		$smarty->assign ( 'productName', $productName ); // 商品名称
		
		$productId = '';
		$smarty->assign ( 'productId', $productId ); // 商品标识
		$productPrice = $_POST ["productPrice"];
		$smarty->assign ( 'productPrice', $productPrice ); // 商品单价
		$productNum = "1";
		$smarty->assign ( 'productNum', $productNum ); // 商品数量
		if ($order ['user_note']) {
			$productDesc = $order ['user_note'];
		} else {
			$productDesc = $_POST ["productDesc"];
			if(!$productDesc){
				$productDesc ="描述省略";
			}
		}
		
		$smarty->assign ( 'productDesc', $productDesc ); // 商品描述
		$ext1 = '';
		$smarty->assign ( 'ext1', $ext1 ); // 扩展字段1
		$ext2 = '';
		$smarty->assign ( 'ext2', $ext2 ); // 扩展字段2
		$extTL = '';
		$smarty->assign ( 'extTL', $extTL ); // 业务扩展字段
		/**
		 * 0-代表全部，即显示该商户开通的所有支付方式
		 * 1-个人网银支付借记卡
		 * 4-企业网银
		 * 11-信用卡支付
		 * 20-微信扫码支付
		 * 22-支付宝扫码支付
		 * 31-银联认证支付
		 */
		
		// $payType = $_POST ["payType"];
		$smarty->assign ( 'payType', $payType ); // 支付方式
		if ($payType == 22) { // 支付宝支付
			$issuerId = "alipay"; // issueId 直联时不为空，必须放在表单中提交。
		} else {
			$issuerId = $_POST ["issuerId"]; // issueId 直联时不为空，必须放在表单中提交。
		}
		
		$smarty->assign ( 'issuerId', $issuerId ); // issueId 直联时不为空，必须放在表单中提交。
		$key = 'SDSD3433FG764323GH87878743328890AEF';
		
		// 生成签名字符串。
		$bufSignSrc = "";
		if ($inputCharset != "")
			$bufSignSrc = $bufSignSrc . "inputCharset=" . $inputCharset . "&";
		if ($pickupUrl != "")
			$bufSignSrc = $bufSignSrc . "pickupUrl=" . $pickupUrl . "&";
		if ($receiveUrl != "")
			$bufSignSrc = $bufSignSrc . "receiveUrl=" . $receiveUrl . "&";
		if ($version != "")
			$bufSignSrc = $bufSignSrc . "version=" . $version . "&";
		if ($language != "")
			$bufSignSrc = $bufSignSrc . "language=" . $language . "&";
		if ($signType != "")
			$bufSignSrc = $bufSignSrc . "signType=" . $signType . "&";
		if ($merchantId != "")
			$bufSignSrc = $bufSignSrc . "merchantId=" . $merchantId . "&";
		if ($payerName != "")
			$bufSignSrc = $bufSignSrc . "payerName=" . $payerName . "&";
		if ($payerEmail != "")
			$bufSignSrc = $bufSignSrc . "payerEmail=" . $payerEmail . "&";
		if ($payerTelephone != "")
			$bufSignSrc = $bufSignSrc . "payerTelephone=" . $payerTelephone . "&";
		
		if ($orderNo != "")
			$bufSignSrc = $bufSignSrc . "orderNo=" . $orderNo . "&";
		if ($orderAmount != "")
			$bufSignSrc = $bufSignSrc . "orderAmount=" . $orderAmount . "&";
		if ($orderCurrency != "")
			$bufSignSrc = $bufSignSrc . "orderCurrency=" . $orderCurrency . "&";
		if ($orderDatetime != "")
			$bufSignSrc = $bufSignSrc . "orderDatetime=" . $orderDatetime . "&";
		if ($orderExpireDatetime != "")
			$bufSignSrc = $bufSignSrc . "orderExpireDatetime=" . $orderExpireDatetime . "&";
		if ($productName != "")
			$bufSignSrc = $bufSignSrc . "productName=" . $productName . "&";
		if ($productPrice != "")
			$bufSignSrc = $bufSignSrc . "productPrice=" . $productPrice . "&";
		if ($productNum != "")
			$bufSignSrc = $bufSignSrc . "productNum=" . $productNum . "&";
		if ($productId != "")
			$bufSignSrc = $bufSignSrc . "productId=" . $productId . "&";
		if ($productDesc != "")
			$bufSignSrc = $bufSignSrc . "productDesc=" . $productDesc . "&";
		if ($ext1 != "")
			$bufSignSrc = $bufSignSrc . "ext1=" . $ext1 . "&";
		if ($ext2 != "")
			$bufSignSrc = $bufSignSrc . "ext2=" . $ext2 . "&";
		if ($extTL != "")
			$bufSignSrc = $bufSignSrc . "extTL" . $extTL . "&";
		if ($payType != "")
			$bufSignSrc = $bufSignSrc . "payType=" . $payType . "&";
		if ($issuerId != "")
			$bufSignSrc = $bufSignSrc . "issuerId=" . $issuerId . "&";
		
		$bufSignSrc = $bufSignSrc . "key=" . $key; // key为MD5密钥，密钥是在开联通支付网关商户服务网站上设置。
		                                           // 签名，设为signMsg字段值。
		$signMsg = strtoupper ( md5 ( $bufSignSrc ) );
		
		$smarty->assign ( 'signMsg', $signMsg ); // 用于计算signMsg的key值
		
		if ($payType!=1){//非网银支付
			$qrPath = $this->saveQRcode($inputCharset,$pickupUrl,$receiveUrl,$version,$language,$merchantId,$payerName,$payerEmail,$payerTelephone,$orderNo,$orderAmount
					,$orderCurrency,$orderDatetime,$orderExpireDatetime,$productName,$productPrice,$productNum,$productId,$productDesc,$ext1,$ext2,$extTL,$payType,$issuerId,$signMsg);
			
			$smarty->assign ( 'qrPath', $qrPath);
			$html = $smarty->fetch ( 'payment/qrCode.htm' );
// 			file_put_contents ( dirname ( __FILE__ ) . "/test.txt", $html, FILE_APPEND );
	 
			return $html;
		}else{
			$html = $smarty->fetch ( 'payment/payform.htm' );
			file_put_contents ( dirname ( __FILE__ ) . "/test.txt", $html, FILE_APPEND );
			return $html;
		}
		
	}
	
	//获取商铺的根目录
	public function getShopRootPath(){
		$dir =dirname ( __FILE__ );
		$dirs =str_replace( "includes\modules\openepay","",$dir); 
		return   $dirs;
	}
	//保存二维码
	public  function  saveQRcode($inputCharset,$pickupUrl,$receiveUrl,$version,$language,$merchantId,$payerName,$payerEmail,$payerTelephone,$orderNo,$orderAmount
			,$orderCurrency,$orderDatetime,$orderExpireDatetime,$productName,$productPrice,$productNum,$productId,$productDesc,$ext1,$ext2,$extTL,$payType,$issuerId,$signMsg){
			 
          $dir =dirname ( __FILE__ );
          $dirs =str_replace( "modules\openepay","",$dir); 
          require ($dirs.'/phpqrcode.php');
         
		$data = array (
				'inputCharset' => $inputCharset,
				'pickupUrl' => $pickupUrl,
				'receiveUrl' => $receiveUrl,
				'version' => $version,
				'language' => $language,
				'signType' => "1",
				'merchantId' => $merchantId,
				'payerName' => $payerName,
				'payerEmail' => $payerEmail,
				'payerTelephone' => $payerTelephone,
				'orderNo' => $orderNo,
				'orderAmount' => $orderAmount,
				'orderCurrency' => $orderCurrency,
				'orderDatetime' => $orderDatetime,
				'orderExpireDatetime' => $orderExpireDatetime,
				'productName' => $productName,
				'productPrice' => $productPrice,
				'productNum' => $productNum,
				'productId' => $productId,
				'productDesc' => $productDesc,
				'ext1' => $ext1,
				'ext2' => $ext2,
				'extTL' => $extTL,
				'payType' => $payType,
				'issuerId' => $issuerId,
				'signMsg' => $signMsg
				
		);
	 
		$data = http_build_query ( $data );
		
		$opts = array (
				'http' => array (
						'method' => 'POST',
						'header' => 'Content-type: application/x-www-form-urlencoded; charset=UTF-8\r\n' . 'Content-Length: ' . strlen ( $data ) . '\r\n',
						'content' => $data
				)
		);
		$context = stream_context_create ( $opts );
		$html = file_get_contents ( 'https://wanshangxing.com/index.php?app=smartepay', false, $context );
		$pos = strrpos($html,'}');
		$html =  substr($html,0,$pos+1);
		$obj = json_decode ( $html );//截取到json数据
	 
		$errorCorrectionLevel = 'L'; // 容错级别
		$matrixPointSize = 26; // 生成图片大小
		$refferPath = '/qrcode/'.$this->createGuid().'.png';//相对路径
		$qrPath = $this->getShopRootPath().$refferPath;//二维码的存放路径
		
		// 生成二维码图片
		QRcode::png ( $obj ->qrCode, $qrPath, $errorCorrectionLevel, $matrixPointSize, 2 );
		 
		return  $refferPath;
	}
	
	function createGuid()
	{
		if (function_exists('com_create_guid')){
			return com_create_guid();
		}else{
			
			mt_srand((double)microtime()*10000);
			$charid = strtoupper(md5(uniqid(rand(), true)));
			$hyphen = chr(45);// "-"
// 			$uuid =  chr(123)// "{"
			$uuid =substr($charid, 0, 8).$hyphen
			.substr($charid, 8, 4).$hyphen
			.substr($charid,12, 4).$hyphen
			.substr($charid,16, 4).$hyphen
			.substr($charid,20,12);
// 			.chr(125);// "}"
			return $uuid;
		}
	}
	
	/**
	 * 响应操作
	 */
	public function respond() {
		file_put_contents ( ROOT_PATH . "/test.txt", "respond\r\n", FILE_APPEND );
		
		// require_once("D:\Coms1\wwwroot\0wan\openepay\php_rsa.php"); //请修改参数为php_rsa.php文件的实际位置
		$merchantId = $_POST ["merchantId"];
		$version = $_POST ['version'];
		$language = $_POST ['language'];
		$signType = $_POST ['signType'];
		$payType = $_POST ['payType'];
		$issuerId = $_POST ['issuerId'];
		$mchtOrderId = $_POST ['mchtOrderId'];
		$orderNo = $_POST ['orderNo'];
		$merTranTime = $_POST ['merTranTime'];
		$orderAmount = $_POST ['orderAmount'];
		$payDatetime = $_POST ['payDatetime'];
		$ext1 = $_POST ['ext1'];
		$ext2 = $_POST ['ext2'];
		$payResult = $_POST ['payResult'];
		$signMsg = $_POST ["signMsg"];
		print_r ( $_POST );
		
		$bufSignSrc = "";
		if ($merchantId != "")
			$bufSignSrc = $bufSignSrc . "merchantId=" . $merchantId . "&";
		if ($version != "")
			$bufSignSrc = $bufSignSrc . "version=" . $version . "&";
		if ($language != "")
			$bufSignSrc = $bufSignSrc . "language=" . $language . "&";
		if ($signType != "")
			$bufSignSrc = $bufSignSrc . "signType=" . $signType . "&";
		if ($payType != "")
			$bufSignSrc = $bufSignSrc . "payType=" . $payType . "&";
		if ($issuerId != "")
			$bufSignSrc = $bufSignSrc . "issuerId=" . $issuerId . "&";
		if ($mchtOrderId != "")
			$bufSignSrc = $bufSignSrc . "mchtOrderId=" . $mchtOrderId . "&";
		
		if ($orderNo != "")
			$bufSignSrc = $bufSignSrc . "orderNo=" . $orderNo . "&";
		if ($merTranTime != "")
			$bufSignSrc = $bufSignSrc . "merTranTime=" . $merTranTime . "&";
		if ($orderAmount != "")
			$bufSignSrc = $bufSignSrc . "orderAmount=" . $orderAmount . "&";
		if ($payDatetime != "")
			$bufSignSrc = $bufSignSrc . "payDatetime=" . $payDatetime . "&";
		if ($ext1 != "")
			$bufSignSrc = $bufSignSrc . "ext1=" . $ext1 . "&";
		if ($ext2 != "")
			$bufSignSrc = $bufSignSrc . "ext2=" . $ext2 . "&";
		if ($payResult != "")
			$bufSignSrc = $bufSignSrc . "payResult=" . $payResult;
		$bufSignSrc = $bufSignSrc . "&key=SDSD3433FG764323GH87878743328890AEF";
		
		$var = var_export ( $_POST, TRUE );
		file_put_contents ( ROOT_PATH. "/test.txt", "post:" . $var . ":::::\r\n", FILE_APPEND );
		
		$newSignMsg = strtoupper ( md5 ( $bufSignSrc ) );
		
		if ($newSignMsg == $signMsg) {
			$verify_result = true;
		}
		
		$value = null;
		if ($verify_result) {
			// $value = "报文验签成功！";
			file_put_contents ( ROOT_PATH. "/test.txt", "报文验签成功\r\n", FILE_APPEND );
		} else {
			// $value = "报文验签失败！";
			file_put_contents ( ROOT_PATH. "/test.txt", "报文验签失败\r\n", FILE_APPEND );
		}
		
		// 验签成功，还需要判断订单状态，为"1"表示支付成功。
		$payvalue = null;
		if ($verify_result and $payResult == 1) {
			
			$order_type = get_order_type ( $orderNo );
			file_put_contents ( ROOT_PATH. "/test.txt", "订单类型:".$order_type."  订单号:".$orderNo."\r\n", FILE_APPEND );
			if ($order_type * 1 == 2 || $order_type * 1 == 3) {
				mutual_order_paid ( $orderNo);
			} elseif ($order_type * 1 == 4) {
				dajiating_order_paid ( $orderNo, "" );
			} elseif ($order_type * 1 == 5) {
				// 产品积分充值
				chan_pin_order_paid ( $orderNo);
			} else {
				order_paid ( $orderNo, 2 );
			}
			return 'success';
			// $payvalue = "报文验签成功，且订单支付成功";
		} else {
			return false;
			// $payvalue = "报文验签成功，但订单支付失败";
		}
	}
}

?>