<?php
header("Content-type:text/html;charset=utf-8");
require_once 'AppConfig.php';
require_once 'AppUtil.php';



function pay(){
session_start();
$session1=htmlspecialchars(trim($_POST['pay1']));
if($session1!=$_SESSION['pay1']) {
echo "<center>请不要重复提交支付申请</center>";
    exit();
} else {
	 unset($_SESSION['pay1']);
}
$openids=$_POST['openids'];
$yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
$orderSn = $yCode[intval(date('Y')) - 2011].strtoupper(dechex(date('m'))).date('d').substr(time(), -5).substr(microtime(), 2, 5).sprintf('%02d', rand(0, 99));
$o="wxgzhzf".$orderSn;
$money=htmlspecialchars($_POST['money']);
$je=floatval($money)*100;
		$params = array();
		$params["cusid"] = AppConfig::CUSID;
	    $params["appid"] = AppConfig::APPID;
	    $params["version"] = AppConfig::APIVERSION;
	    $params["trxamt"] =$je;
	    $params["reqsn"] =$o;//订单号,自行生成
	    $params["paytype"] = "W02";
	    $params["randomstr"] = "1450432107647";
	    $params["body"]="聚元国际商城商家充值";
	    $params["remark"]="备注信息";
	    $params["acct"] = $openids;
	    /*var_dump($params["acct"]);
	    var_dump($params);*/
	    $params["limit_pay"]="no_credit";
        $params["notify_url"] = "http://www.jsguoji.cn/wpay/notify.php";
	    $params["sign"] = AppUtil::SignArray($params,AppConfig::APPKEY);//签名
	    $paramsStr = AppUtil::ToUrlParams($params);
	    $url = AppConfig::APIURL . "/pay";
	    $rsp = request($url, $paramsStr);
		$rspArray = json_decode($rsp, true); 
	    if(validSign($rspArray)){
	$obj = json_decode($rsp);
$out=$obj->{"payinfo"};
/*echo '<br><br><br><br><br>'.$out;*/
$obj2 = json_decode($out);

?>
<script type="text/javascript">
        //调用微信JS api 支付
        function jsApiCall()
        {
            WeixinJSBridge.invoke(
                'getBrandWCPayRequest',
                <?php echo $out;?>,
                function(res){
                    WeixinJSBridge.log(res.err_msg);
                    //alert(res.err_code+res.err_desc+res.err_msg);
	  				if(res.err_msg == "get_brand_wcpay_request:ok"){
	                   //alert(res.err_code+res.err_desc+res.err_msg);
                       window.location.href="/wap/mall/b_center.html";
                   	}else{
                       //返回跳转到订单详情页面
                       alert(支付失败);
                     window.location.href="/wap/mall/b_center.html";
                   }

                }
            );

        }

        function callpay()
        {
            if (typeof WeixinJSBridge == "undefined"){
                if( document.addEventListener ){
                    document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                }else if (document.attachEvent){
                    document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
                    document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                }
            }else{
                jsApiCall();
            }
        }
        callpay();
    </script>
<!--  <button style="width:210px; height:30px; background-color:#FE6714; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;" type="button" onclick="callpay()">微信支付</button> -->
<?
$orderNo=$params["reqsn"];
if(empty($_COOKIE["zt_user1"])){
echo '<script>location.href="http://www.jsguoji.cn/wap/mall/login.html";</script>';
}
$db_host="localhost"; 
$db_user="xbshp";
$db_pwd="F8a9h7x209988"; 
$db_database="xbshp";
$coding="utf8";
date_default_timezone_set("Asia/Shanghai");
$db=mysql_connect($db_host,$db_user,$db_pwd);
mysql_query("SET NAMES $coding"); 
mysql_select_db($db_database);
$user=$_COOKIE['ECS']['username'];
$s="select a from xbmall_users where user_name='$user' order by id desc";
$q=mysql_query($s);
$s1=mysql_fetch_assoc($q);
$sf=$s1["a"];
$sql3="select * from zt_cz where ddbh='$orderNo' and user='$user' order by id desc";
$q3=mysql_query($sql3);
$num=mysql_fetch_assoc($q3);
$date=date("Y-m-d H:i:s");
if($num["ddbh"]==$orderNo){
header("location:http://www.jsguoji.cn/wap/mall/b_recharge.html");
exit();
}else{
if($user<>""){
$je=round(($je/100),2);
include "config/config.php";
$db=mysql_connect($db_host,$db_user,$db_pwd);
mysql_query("SET NAMES $coding"); 
mysql_select_db($db_database);
$user=$_COOKIE['ECS']['username'];
$sql8="INSERT INTO `zt_cz`(`id`,`ddbh`,`czmc`,`je`,`czrq`,`user`,`zt`,`sf`)VALUES(null,'$orderNo','商家会员充值','$je','$date','$user','8','$sf');";
mysql_query($sql8);
}
}


?>

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
	
	//验签444
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


?>