	<?PHP
	define ( 'IN_ECS', true );
	require (dirname ( __FILE__ ) . '/includes/init.php');
	require (dirname ( __FILE__ ) . '/includes/phpqrcode.php');
	?>
 
	<?PHP
	
	// 页面编码要与参数inputCharset一致，否则服务器收到参数值中的汉字为乱码而导致验证签名失败。
	$serverUrl = $_POST ["serverUrl"];
	$inputCharset = $_POST ["inputCharset"];
	$pickupUrl = $_POST ["pickupUrl"];
	$receiveUrl = $_POST ["receiveUrl"];
	$version = $_POST ["version"];
	$language = $_POST ["language"];
	$signType = $_POST ["signType"];
	$merchantId = $_POST ["merchantId"];
	$payerName = $_POST ["payerName"];
	$payerEmail = $_POST ["payerEmail"];
	$payerTelephone = $_POST ["payerTelephone"];
	
	$orderNo = $_POST ["orderNo"];
	$orderAmount = $_POST ["orderAmount"];
	$orderDatetime = $_POST ["orderDatetime"];
	$orderCurrency = $_POST ["orderCurrency"];
	$orderExpireDatetime = $_POST ["orderExpireDatetime"];
	$productName = $_POST ["productName"];
	$productId = $_POST ["productId"];
	$productPrice = $_POST ["productPrice"];
	$productNum = $_POST ["productNum"];
	$productDesc = $_POST ["productDesc"];
	$ext1 = $_POST ["ext1"];
	$ext2 = $_POST ["ext2"];
	$extTL = $_POST ["extTL"];
	$payType = $_POST ["payType"]; // payType 不能为空，必须放在表单中提交。
	$issuerId = $_POST ["issuerId"]; // issueId 直联时不为空，必须放在表单中提交。
	
	$key = $_POST ["key"];
	
	// 报文参数有消息校验
	// if(preg_match("/\d/",$pickupUrl)){
	// echo "<script>alert('pickupUrl有误！！');history.back();</script>";
	// }
	
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
	$data = array (
			'inputCharset' => $inputCharset,
			'pickupUrl' => $pickupUrl,
			'receiveUrl' => $receiveUrl,
			'version' => $version,
			'language' => $language,
			'signType' => $signType,
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
	$obj = json_decode ( $html );
	
	$errorCorrectionLevel = 'L'; // 容错级别
	$matrixPointSize = 106; // 生成图片大小
	                      // 生成二维码图片
	QRcode::png ( $obj [0]->qrCode, '/qrcode/qrcode.png', $errorCorrectionLevel, $matrixPointSize, 2 );
	
	$QR = dirname ( __FILE__ ) .'/qrcode/qrcode.png'; // 已经生成的原始二维码图
	 
	// 输出图片
	// imagepng($QR, 'helloweba.png');
	// echo '<img src="helloweba.png">';
	header ( 'Content-type: image/png' );
	imagepng ( $QR );
	imagedestroy ( $QR );
	exit ();
	
	// header("Location: ".$obj[0]->qrCode);
	
	?>
	A <a href="<?= $obj[0]->qrCode;?>">aaaaaaaaaa</a>
 