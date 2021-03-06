<!DOCTYPE html>
<head>
	<title>网上支付平台-商户接口范例-支付请求</title>
	<link href="css.css" rel="stylesheet" type="text/css">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body bgcolor='#FFFFFF' text='#000000' link='#0000FF' vlink='#0000FF' alink='#FF0000' onLoad="setOrderNo();sub();">
<script>
<!--
function sub(){
document.form.submit();
}
setTimeout(sub,10000);
//-->
</script>
<?php
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL;
$host = $_SERVER['HTTP_HOST'];
if(substr($referer,7,strlen($host)) != $host){
 echo '非法操作';
exit();
}

include "../config/check.php";
?>
	<form action="shop_chong_post1.html" method="post" name="form" id="form">
		<table border="1" cellpadding="1" cellspacing="1" align="center" style="display:none;">
				<thead>
					<tr>
						<th colspan="2" align="right">
							提交地址: 
						</th><th align="left"><input type="text" name="serverUrl" value="https://service.allinpay.com/gateway/index.do"/><font color="red">*</font>
						</th>
					</tr>
					
					<tr>
						<th colspan="2" align="right">
							用于计算signMsg的key值: 
						</th>
						<th align="left">
							<input type="text" name="key" id="key" value="A1234567890"/><font color="red">*</font>
						</th>
					</tr>
					
					<tr>
						<th colspan="3">&nbsp;</th>
					</tr>
					
					<tr>
						<th>参数名称</th>
						<th>参数含义</th>
						<th>参数值</th>
					</tr>
				</thead>
				<tr>
					<td>inputCharset</td>
					<td>字符集</td>
					<td><input type="text" name="inputCharset" value="1"/></td>
				</tr>	
				<tr>
					<td>pickupUrl</td>
					<td>取货地址</td>
					<td><input type="text" name="pickupUrl" value="http://www.jsguoji.cn/mall/pay/shop_chong_pickup.php"/><font color="red">*</font></td>
				</tr>
				<tr>
					<td>receiveUrl</td>
					<td>商户系统通知地址</td>
					<td><input type="text" name="receiveUrl" value="http://www.jsguoji.cn/mall/pay/shop_chong_receive.php"/><font color="red">*</font></td>
				</tr>
				<tr>
					<td>version</td>
					<td>接口版本号</td>
					<td><input type="text" name="version" value="v1.0"/><font color="red">*</font></td>
				</tr>
				<tr>
					<td>language</td>
					<td>网关页面语言</td>
					<td><input type="text" name="language" value=""/></td>
				</tr>
				<tr>
					<td>signType</td>
					<td>签名类型</td>
					<td><input type="text" name="signType" value="1"/><font color="red">*</font></td>
				</tr>
				<tr>
					<td>merchantId</td>
					<td>商户号</td>
					<td><input type="text" name="merchantId" value="109045511701004"/><font color="red">*测试商户号</font></td>
				</tr>
				<tr>
					<td>payerName</td>
					<td>付款人姓名</td>
					<td><input type="text" name="payerName" value="<?php echo $_COOKIE['ECS']['username'];?>"/></td>
				</tr>
				<tr>
					<td>payerEmail</td>
					<td>付款人联系email</td>
					<td><input type="text" name="payerEmail" value=""/></td>
				</tr>
				<tr>
					<td>payerTelephone</td>
					<td>付款人电话</td>
					<td><input type="text" name="payerTelephone" value=""/></td>
				</tr>
				<tr>
					<td>payerIDCard</td>
					<td>付款人证件号</td>
					<td><input type="text" name="payerIDCard" value=""/></td>
				</tr>
				<tr>
					<td>pid</td>
					<td>合作伙伴商户号</td>
					<td><input type="text" name="pid" value=""/></td>
				</tr>
				<tr>
					<td>orderNo</td>
					<td>商户订单号</td>
					<td><input type="text" name="orderNo"  id="orderNo" value=""/><font color="red">*</font><input type="button" value="生成订单号" onload="setOrderNo()"/></td>
				</tr>
				<tr>
					<td>orderAmount</td>
					<td>订单金额(单位分)</td>
					<td><input type="text" name="orderAmount" value="<?php echo htmlspecialchars($_POST['money'])*100;?>"/><font color="red">*</font></td>
				</tr>
				<tr>
					<td>orderCurrency</td>
					<td>订单金额币种类型</td>
					<td><input type="text" name="orderCurrency" value=""/></td>
				</tr>
				<tr>
					<td>orderDatetime</td>
					<td>订单提交时间</td>
					<td><input type="text" name="orderDatetime" id="orderDatetime" value=""/><font color="red">*</font></td>
				</tr>
				<tr>
					<td>orderExpireDatetime</td>
					<td>订单过期时间</td>
					<td><input type="text" name="orderExpireDatetime" value=""/></td>
				</tr>	
				<tr>
					<td>productName</td>
					<td>商品名称</td>
					<td><input type="text" name="productName" value="商家会员充值"/></td>
				</tr>
				<tr>
					<td>productPrice</td>
					<td>商品单价</td>
					<td><input type="text" name="productPrice" value=""/></td>
				</tr>
				<tr>
					<td>productNum</td>
					<td>商品数量</td>
					<td><input type="text" name="productNum" value=""/></td>
				</tr>
				<tr>
					<td>productId</td>
					<td>商品代码</td>
					<td><input type="text" name="productId" value=""/></td>
				</tr>
				<tr>
					<td>productDesc</td>
					<td>商品描述</td>
					<td><input type="text" name="productDesc" value=""/></td>
				</tr>
				<tr>
					<td>ext1</td>
					<td>扩展字段1</td>
					<td><input type="text" name="ext1" value=""/></td>
				</tr>
				<tr>
					<td>ext2</td>
					<td>扩展字段2</td>
					<td><input type="text" name="ext2" value=""/></td>
				</tr>
				<tr>
					<td>extTL</td>
					<td>业务扩展字段</td>
					<td><input type="text" name="extTL" value=""/></td>
				</tr>				
				<tr>
					<td>payType</td>
					<td>支付方式</td>
					<td><input type="text" name="payType" value="0"/><font color="red">*</font></td>
				</tr>
				<tr>
					<td>issuerId</td>
					<td>发卡方代码</td>
					<td><input type="text" name="issuerId" value="" /></td>
				</tr>	
				<tr>
					<td>pan</td>
					<td>付款人支付卡号</td>
					<td><input type="text" name="pan" value=""/></td>
				</tr>
				<tr>
					<td>tradeNature</td>
					<td>贸易类型</td>
					<td><input type="text" name="tradeNature" value="GOODS"/></td>
				</tr>
				<tr>
					<td>customsExt</td>
					<td>海关扩展字段</td>
					<td><input type="text" name="customsExt" value=""/></td>
				</tr>
				
			</table>
			<div align="center"><input type="submit" value="提交表单，计算signMsg"/></div>
	</form>

	<script language="javascript">
		//setOrderNo();	
		function setOrderNo() {
			var curr = new Date();
			var m = curr.getMonth() + 1;
			if (m < 10) {m = '0' + m;}
			var d = curr.getDate();
			if (d < 10) {d = '0' + d;}
			var h = curr.getHours();
			if (h < 10) {h = '0' + h;}
			var mi = curr.getMinutes();
			if (mi < 10) {mi = '0' + mi;}
			var s = curr.getSeconds();
			if (s < 10) {s = '0' + s;}
			var strDatetime = '' + curr.getFullYear() + m + d + h + mi + s;		
			document.getElementById("orderDatetime").value = strDatetime;
			document.getElementById("orderNo").value = 'NO' + strDatetime;
		}
	</script>

</BODY>
</HTML>
