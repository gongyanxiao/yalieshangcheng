<?php
	header("Content-type:text/html;charset=utf-8");
	require_once 'AppConfig.php';
	require_once 'AppUtil.php';
	function GetRandStr($length){
$str='9048321060123456789';
$len=strlen($str)-1;
$randstr='';
for($i=0;$i<$length;$i++){
$num=mt_rand(0,$len);
$randstr .= $str[$num];
}
return $randstr;
}
        function pay(){
		$params = array();
		$params["cusid"] = AppConfig::CUSID;
	    $params["appid"] = AppConfig::APPID;
	    $params["version"] = AppConfig::APIVERSION;
	    $params["trxamt"] = "1";
	    $params["reqsn"] = "wx".GetRandStr('8');;//订单号,自行生成
	    $params["paytype"] = "W01";
	    $params["randomstr"] = "1450432107647";
	    $params["body"] = "聚元国际商城商家充值";
	    $params["remark"] = "备注信息";
	    $params["acct"] = "gh_88d53469a468";
	    $params["limit_pay"] = "no_credit";
            $params["notify_url"] = "http://www.jsguoji.cn/wap/mall/pay/wx/notify.php";
	    $params["sign"] = AppUtil::SignArray($params,AppConfig::APPKEY);//签名
	    $paramsStr = AppUtil::ToUrlParams($params);
	    $url = AppConfig::APIURL . "/pay";
	    $rsp = request($url, $paramsStr);
 $rspArray = json_decode($rsp, true); 
	    if(validSign($rspArray)){
echo "验签正确,进行业务处理";
echo $rsp;
$obj = json_decode($rsp);
$appid=$obj->{"appid"};
$cusid=$obj->{"cusid"};
$sign=$obj->{"sign"};
$ddbh=$obj->{"trxid"};
$reqsn=$obj->{"reqsn"};
?>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>
<!--
function onBridgeReady(){
    WeixinJSBridge.invoke(
        'getBrandWCPayRequest', {
            "appId" ： "<?=$appid;?>",     //公众号名称，由商户传入     
            "timeStamp"："<?=strtotime(date("Y-m-d H:i:s"));?>",         //时间戳，自1970年以来的秒数     
            "nonceStr" ： "<?=$reqsn;?>", //随机串     
            "package" ： "prepay_id=<?=$ddbh;?>",     
            "signType" ： "MD5",         //微信签名方式：     
            "paySign" ： "<?=$sign;?>" //微信签名 
        },
        function(res){     
            if(res.err_msg == "get_brand_wcpay_request：ok" ) {
			
			}     // 使用以上方式判断前端返回,微信团队郑重提示：res.err_msg将在用户支付成功后返回    ok，但并不保证它绝对可靠。 
        }
    ); 
 }

if (typeof WeixinJSBridge == "undefined"){
    if( document.addEventListener ){
        document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
    }else if (document.attachEvent){
        document.attachEvent('WeixinJSBridgeReady', onBridgeReady); 
        document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
    }
 }else{
    onBridgeReady();
 } 
 //-->
 </script>
<?php


	    }
	    
	}
	
	//当天交易用撤销
	function cancel(){
		$params = array();
		$params["cusid"] = AppConfig::CUSID;
	    $params["appid"] = AppConfig::APPID;
	    $params["version"] = AppConfig::APIVERSION;
	    $params["trxamt"] = "1";
	    $params["reqsn"] = "123456788";
	    $params["oldreqsn"] = "1234569";//原来订单号
	    $params["randomstr"] = "1450432107647";//
	    $params["sign"] = AppUtil::SignArray($params,AppConfig::APPKEY);//签名
	    $paramsStr = AppUtil::ToUrlParams($params);
	    $url = AppConfig::APIURL . "/cancel";
	    $rsp = request($url, $paramsStr);
		echo "请求返回:".$rsp;
	    echo "<br/>";
	    $rspArray = json_decode($rsp, true); 
	    if(validSign($rspArray)){
	    	echo "验签正确,进行业务处理";
 
	    }
	}
	
	//当天交易请用撤销,非当天交易才用此退货接口
	function refund(){
		$params = array();
		$params["cusid"] = AppConfig::CUSID;
	    $params["appid"] = AppConfig::APPID;
	    $params["version"] = AppConfig::APIVERSION;
	    $params["trxamt"] = "1";
	    $params["reqsn"] = "1234567889";
	    $params["oldreqsn"] = "123456";//原来订单号
	    $params["randomstr"] = "1450432107647";//
	    $params["sign"] = AppUtil::SignArray($params,AppConfig::APPKEY);//签名
	    $paramsStr = AppUtil::ToUrlParams($params);
	    $url = AppConfig::APIURL . "/refund";
	    $rsp = request($url, $paramsStr);
		echo "请求返回:".$rsp;
	    echo "<br/>";
	    $rspArray = json_decode($rsp, true); 
	    if(validSign($rspArray)){
	    	echo "验签正确,进行业务处理";
	    }
	}
	
	function query(){
		$params = array();
		$params["cusid"] = AppConfig::CUSID;
	    $params["appid"] = AppConfig::APPID;
	    $params["version"] = AppConfig::APIVERSION;
	    $params["reqsn"] = "123456";
	    $params["randomstr"] = "1450432107647";//
	    $params["sign"] = AppUtil::SignArray($params,AppConfig::APPKEY);//签名
	    $paramsStr = AppUtil::ToUrlParams($params);
	    $url = AppConfig::APIURL . "/query";
	    $rsp = request($url, $paramsStr);
		echo "请求返回:".$rsp;
	    echo "<br/>";
	    $rspArray = json_decode($rsp, true); 
	    if(validSign($rspArray)){
	    	echo "验签正确,进行业务处理";
	    }
	}
	
	//发送请求操作仅供参考,不为最佳实践
	function request($url,$params){
		$ch = curl_init();
		$this_header = array("content-type: application/x-www-form-urlencoded;charset=UTF-8");
		curl_setopt($ch,CURLOPT_HTTPHEADER,$this_header);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		 
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);//如果不加验证,就设false,商户自行处理
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		 
		$output = curl_exec($ch);
		curl_close($ch);
		return  $output;
	}
	
	//验签
	function validSign($array){
		if("SUCCESS"==$array["retcode"]){
			$signRsp = strtolower($array["sign"]);
			$array["sign"] = "";
			$sign =  strtolower(AppUtil::SignArray($array, AppConfig::APPKEY));
			if($sign==$signRsp){
				return TRUE;
			}
			else {
				echo "验签失败:".$signRsp."--".$sign;
			}
		}
		else{
			echo $array["retmsg"];
		}
		
		return FALSE;
	}
	
	pay();
	//cancel();
	//refund();
	//query();
	$appid=$obj->{"appid"};
	$sign=$obj->{"sign"};
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>微信支付</title>


</head>

<body>
</body>
</html>
