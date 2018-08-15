	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>

		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<meta http-equiv="Content-Language" content="zh-CN"/>
		<meta http-equiv="Expires" CONTENT="0">        
		<meta http-equiv="Cache-Control" CONTENT="no-cache">        
		<meta http-equiv="Pragma" CONTENT="no-cache">
		<title>开联通互联网支付平台-商户接口范例-支付请求信息签名</title>
		<link href="css.css" rel="stylesheet" type="text/css">
	</head>
	<body>	
	<center> <font size=16><strong>订单支付请求</strong></font></center>
	<?PHP
 function  toZhiFu(){
 	
 }
	//页面编码要与参数inputCharset一致，否则服务器收到参数值中的汉字为乱码而导致验证签名失败。	
 	$serverUrl="https://wanshangxing.com/index.php?app=smartepay";//$_POST["serverUrl"];
	$inputCharset="1";//$_POST["inputCharset"];
	$pickupUrl=$_POST["pickupUrl"];
	$receiveUrl="http://shop.xiangbai315.com/testNotify2.php";//$_POST["receiveUrl"];
	$version="v1.0";//$_POST["version"];
	$language="1";//$_POST["language"];
	$signType="1";//$_POST["signType"];
	$merchantId="360849";//$_POST["merchantId"];
	$payerName=$_POST["payerName"];
	$payerEmail=$_POST["payerEmail"];	
	$payerTelephone=$_POST["payerTelephone"];
	$orderNo=$_POST["orderNo"];
	$orderAmount=$_POST["amount"];
	$orderDatetime=$_POST["orderDatetime"];
	$orderCurrency=$_POST["orderCurrency"];
	$orderExpireDatetime=$_POST["orderExpireDatetime"];
	$productName=$_POST["productName"];
	$productId=$_POST["productId"];
	$productPrice=$_POST["productPrice"];
	$productNum=$_POST["productNum"];
	$productDesc=$_POST["productDesc"];
	$ext1=$_POST["ext1"];
	$ext2=$_POST["ext2"];
	$extTL=$_POST["extTL"];
	$payType=$_POST["payType"]; //payType   不能为空，必须放在表单中提交。
	$issuerId=$_POST["issuerId"]; //issueId 直联时不为空，必须放在表单中提交。
	
	
	$key=$_POST["key"]; 
	
	//报文参数有消息校验
	//if(preg_match("/\d/",$pickupUrl)){
	//echo "<script>alert('pickupUrl有误！！');history.back();</script>";
	//}

	// 生成签名字符串。
	$bufSignSrc=""; 
	if($inputCharset != "")
	$bufSignSrc=$bufSignSrc."inputCharset=".$inputCharset."&";		
	if($pickupUrl != "")
	$bufSignSrc=$bufSignSrc."pickupUrl=".$pickupUrl."&";		
	if($receiveUrl != "")
	$bufSignSrc=$bufSignSrc."receiveUrl=".$receiveUrl."&";		
	if($version != "")
	$bufSignSrc=$bufSignSrc."version=".$version."&";		
	if($language != "")
	$bufSignSrc=$bufSignSrc."language=".$language."&";		
	if($signType != "")
	$bufSignSrc=$bufSignSrc."signType=".$signType."&";		
	if($merchantId != "")
	$bufSignSrc=$bufSignSrc."merchantId=".$merchantId."&";		
	if($payerName != "")
	$bufSignSrc=$bufSignSrc."payerName=".$payerName."&";		
	if($payerEmail != "")
	$bufSignSrc=$bufSignSrc."payerEmail=".$payerEmail."&";		
	if($payerTelephone != "")
	$bufSignSrc=$bufSignSrc."payerTelephone=".$payerTelephone."&";			
		
	if($orderNo != "")
	$bufSignSrc=$bufSignSrc."orderNo=".$orderNo."&";
	if($orderAmount != "")
	$bufSignSrc=$bufSignSrc."orderAmount=".$orderAmount."&";
	if($orderCurrency != "")
	$bufSignSrc=$bufSignSrc."orderCurrency=".$orderCurrency."&";
	if($orderDatetime != "")
	$bufSignSrc=$bufSignSrc."orderDatetime=".$orderDatetime."&";
	if($orderExpireDatetime != "")
	$bufSignSrc=$bufSignSrc."orderExpireDatetime=".$orderExpireDatetime."&";
	if($productName != "")
	$bufSignSrc=$bufSignSrc."productName=".$productName."&";
	if($productPrice != "")
	$bufSignSrc=$bufSignSrc."productPrice=".$productPrice."&";
	if($productNum != "")
	$bufSignSrc=$bufSignSrc."productNum=".$productNum."&";
	if($productId != "")
	$bufSignSrc=$bufSignSrc."productId=".$productId."&";
	if($productDesc != "")
	$bufSignSrc=$bufSignSrc."productDesc=".$productDesc."&";
	if($ext1 != "")
	$bufSignSrc=$bufSignSrc."ext1=".$ext1."&";
	if($ext2 != "")
	$bufSignSrc=$bufSignSrc."ext2=".$ext2."&";
	if($extTL != "")
	$bufSignSrc=$bufSignSrc."extTL".$extTL."&";
	if($payType != "")
	$bufSignSrc=$bufSignSrc."payType=".$payType."&";		
	if($issuerId != "")
	$bufSignSrc=$bufSignSrc."issuerId=".$issuerId."&";
	
	$bufSignSrc=$bufSignSrc."key=".$key; //key为MD5密钥，密钥是在开联通支付网关商户服务网站上设置。
// 	var_dump($bufSignSrc);
	//签名，设为signMsg字段值。
	$signMsg = strtoupper(md5($bufSignSrc));	
	
	?>
	<!--
		1、订单可以通过post方式或get方式提交，建议使用post方式；
		   提交支付请求可以使用http或https方式，建议使用https方式。
		2、开联通支付网关地址、商户号及key值，在接入测试时由开联通提供；
		   开联通支付网关地址、商户号，在接入生产时由开联通提供，key值在开联通支付网关会员服务网站上设置。
	-->
	<!--================= post 方式提交支付请求 start =====================-->
	<!--================= 测试地址为 http://ceshi.allinpay.com/gateway/index.do =====================-->
	<!--================= 生产地址请在测试环境下通过后从业务人员获取 =====================-->
	<form name="form2" action="<?=$serverUrl ?>" method="post">
		<input type="hidden" name="inputCharset" id="inputCharset" value="<?=$inputCharset ?>" />
		<input type="hidden" name="pickupUrl" id="pickupUrl" value="<?=$pickupUrl?>"/>
		<input type="hidden" name="receiveUrl" id="receiveUrl" value="<?=$receiveUrl?>" />
		<input type="hidden" name="version" id="version" value="<?=$version?>"/>
		<input type="hidden" name="language" id="language" value="<?=$language?>" />
		<input type="hidden" name="signType" id="signType" value="<?=$signType?>"/>
		<input type="hidden" name="merchantId" id="merchantId" value="<?=$merchantId?>" />
		<input type="hidden" name="payerName" id="payerName" value="<?=$payerName?>"/>
		<input type="hidden" name="payerEmail" id="payerEmail" value="<?=$payerEmail?>" />
		<input type="hidden" name="payerTelephone" id="payerTelephone" value="<?=$payerTelephone ?>" />
		
		<input type="hidden" name="orderNo" id="orderNo" value="<?=$orderNo?>" />
		<input type="hidden" name="orderAmount" id="orderAmount" value="<?=$orderAmount ?>"/>
		<input type="hidden" name="orderCurrency" id="orderCurrency" value="<?=$orderCurrency?>" />
		<input type="hidden" name="orderDatetime" id="orderDatetime" value="<?=$orderDatetime?>" />
		<input type="hidden" name="orderExpireDatetime" id="orderExpireDatetime" value="<?=$orderExpireDatetime ?>"/>
		<input type="hidden" name="productName" id="productName" value="<?=$productName?>" />
		<input type="hidden" name="productPrice" id="productPrice" value="<?=$productPrice?>" />
		<input type="hidden" name="productNum" id="productNum" value="<?=$productNum?>"/>
		<input type="hidden" name="productId" id="productId" value="<?=$productId?>" />
		<input type="hidden" name="productDesc" id="productDesc" value="<?=$productDesc?>" />
		<input type="hidden" name="ext1" id="ext1" value="<?=$ext1?>" />
		<input type="hidden" name="ext2" id="ext2" value="<?=$ext2?>" />
		<input type="hidden" name="extTL" id="extTL" value="<?=$extTL?>" />
		<input type="hidden" name="payType" value="<?=$payType?>" />
		<input type="hidden" name="issuerId" value="<?=$issuerId?>" />
		
		<input type="hidden" name="signMsg" id="signMsg" value="<?=$signMsg?>" />
		<div align="center"><input type="submit" value="确认付款，到开联通支付去啦" align=center/></div>
	<!--================= post 方式提交支付请求 end =====================-->
	</form>
	 
	</body>
	</html>