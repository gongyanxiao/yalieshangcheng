	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<html>
	<head>

		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<meta http-equiv="Content-Language" content="zh-CN"/>
		<meta http-equiv="Expires" content="0" />        
		<meta http-equiv="Cache-Control" content="no-cache" />        
		<meta http-equiv="Pragma" content="no-cache" />
		<title>通联网上支付平台-商户接口范例-支付结果</title>
		<link href="css.css" rel="stylesheet" type="text/css" />
	</head>
	<body>
	<center> <font size=16><strong>支付结果</strong></font></center>

<?php
	//如果需要用证书加密，使用phpseclib包
	set_include_path(get_include_path() . PATH_SEPARATOR . 'phpseclib');
	require("File/X509.php"); 
	require("Crypt/RSA.php");

	//如果不用证书加密，使用php_rsa.php函数
	require_once("./php_rsa.php"); 
	
	//测试商户的key! 请修改。
	$md5key = "1234567890";
	
	$merchantId=$_POST["merchantId"];
	$version=$_POST['version'];
	$language=$_POST['language'];
	$signType=$_POST['signType'];
	$payType=$_POST['payType'];
	$issuerId=$_POST['issuerId'];
	$paymentOrderId=$_POST['paymentOrderId'];
	$orderNo=$_POST['orderNo'];
	$orderDatetime=$_POST['orderDatetime'];
	$orderAmount=$_POST['orderAmount'];
	$payDatetime=$_POST['payDatetime'];
	$payAmount=$_POST['payAmount'];
	$ext1=$_POST['ext1'];
	$ext2=$_POST['ext2'];
	$payResult=$_POST['payResult'];
	$errorCode=$_POST['errorCode'];
	$returnDatetime=$_POST['returnDatetime'];
	$signMsg=$_POST["signMsg"];
	
	
	$bufSignSrc="";
	if($merchantId != "")
	$bufSignSrc=$bufSignSrc."merchantId=".$merchantId."&";		
	if($version != "")
	$bufSignSrc=$bufSignSrc."version=".$version."&";		
	if($language != "")
	$bufSignSrc=$bufSignSrc."language=".$language."&";		
	if($signType != "")
	$bufSignSrc=$bufSignSrc."signType=".$signType."&";		
	if($payType != "")
	$bufSignSrc=$bufSignSrc."payType=".$payType."&";
	if($issuerId != "")
	$bufSignSrc=$bufSignSrc."issuerId=".$issuerId."&";
	if($paymentOrderId != "")
	$bufSignSrc=$bufSignSrc."paymentOrderId=".$paymentOrderId."&";
	if($orderNo != "")
	$bufSignSrc=$bufSignSrc."orderNo=".$orderNo."&";
	if($orderDatetime != "")
	$bufSignSrc=$bufSignSrc."orderDatetime=".$orderDatetime."&";
	if($orderAmount != "")
	$bufSignSrc=$bufSignSrc."orderAmount=".$orderAmount."&";
	if($payDatetime != "")
	$bufSignSrc=$bufSignSrc."payDatetime=".$payDatetime."&";
	if($payAmount != "")
	$bufSignSrc=$bufSignSrc."payAmount=".$payAmount."&";
	if($ext1 != "")
	$bufSignSrc=$bufSignSrc."ext1=".$ext1."&";
	if($ext2 != "")
	$bufSignSrc=$bufSignSrc."ext2=".$ext2."&";
	if($payResult != "")
	$bufSignSrc=$bufSignSrc."payResult=".$payResult."&";
	if($errorCode != "")
	$bufSignSrc=$bufSignSrc."errorCode=".$errorCode."&";
	if($returnDatetime != "")
	$bufSignSrc=$bufSignSrc."returnDatetime=".$returnDatetime;
	
	//验签
	
	/*
	//解析publickey.txt文本获取公钥信息
	$publickeyfile = './publickey.txt';
	$publickeycontent = file_get_contents($publickeyfile);

	$publickeyarray = explode(PHP_EOL, $publickeycontent);
	$publickey_arr = explode('=',$publickeyarray[0]);
	$modulus_arr = explode('=',$publickeyarray[1]);
	$publickey = trim($publickey_arr[1]);
	$modulus = trim($modulus_arr[1]);
		
	$keylength = 1024;
	$verifyResult = rsa_verify($bufSignSrc,$signMsg, $publickey, $modulus, $keylength,"sha1");
	*/
	
	
	//解析证书方式
	$certfile = file_get_contents('TLCert-test.cer');
	$x509 = new File_X509();
	$cert = $x509->loadX509($certfile);
	$pubkey = $x509->getPublicKey();
	$rsa = new Crypt_RSA();
	$rsa->loadKey($pubkey); // public key
	$rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);
	$verifyResult = $rsa->verify($bufSignSrc, base64_decode(trim($signMsg)));
	
	
	$value = null;
	if($verifyResult){
		$value = "报文验签成功！";
	}
	else{
		$value = "报文验签失败！";
	}
	
	//验签成功，还需要判断订单状态，为"1"表示支付成功。
	$payvalue = null;
	$pay_result = false;
	if($verifyResult and $payResult == 1){
		$pay_result = true;
		$payvalue = "报文验签成功，且订单支付成功";
	}else{
	  $payvalue = "报文验签成功，但订单支付失败";
	}
		
?>
	<div style="padding-left:40px;">			
			<div>验证结果：<?=$value?></div>
			<div>支付结果：<?=$payvalue?></div>
			<hr/>
			<div>商户号：<?=$merchantId ?> </div>
			<div>商户订单号：<?=$orderNo ?> </div>
			<div>商户订单金额：<?=$orderAmount ?></div>
			<div>商户订单时间：<?=$orderDatetime ?> </div>
			<div>网关支付金额：<?=$payAmount ?></div>
			<div>网关支付时间：<?=$payDatetime ?></div>

	</div>	
 </body>
</html>
	
	
