<?php

/**
 * 给app封装支付宝订单
 */
define('IN_ECS', true);
require (dirname(__FILE__) . '/includes/init.php');
require (dirname(__FILE__) . '/includes/lib_clips.php');

 

 

/* 载入语言文件 */

$action = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : 'default';
$function_name = 'action_' . $action;

if (! function_exists ( $function_name )) {
	$function_name = "action_default";
}

call_user_func ( $function_name );

 
/**
 * 获取支付id
 */
function   get_pay_id($user_id,$supp_id,$amount){
  
 	$amount = isset ( $_GET['amount'] ) ? floatval ( $_GET ['amount'] ) : 0;
 	$user_id= $_GET['user_id'];
 	$supp_id= $_GET['supp_id'];
 	
 	if (!isset($supp_id)){
 		echo  -1;
 		exit();
 	}
 	
 	if (!isset($user_id)){//用户没有登录或登录失效了
 		echo  -2;
 		exit();
 	}
 	
 	/* 变量初始化 */
 	$surplus = array (
 			'user_id' => $user_id,
 			'rec_id' =>  0,
 			'admin_user'=>$supp_id,//存储商户的id
 			'process_type' => 0,
 			'payment_id' => 20,//香柏商城扫码支付
 			'user_note' => '购买商户'.$supp_id.'的商品花费'.$amount.'元',
 			'amount' => $amount
 	);
 	// 插入会员账目明细状态为未支付
 	$surplus ['rec_id'] = insert_user_account ( $surplus, $amount );
  
 	// 记录支付log
 	$order ['log_id'] = insert_pay_log ( $surplus ['rec_id'], $amount, $type = 100, 0 );//支付类型是香柏客户端扫码支付
 	
 	return   $order['log_id']; //返回支付的ide
	 	
 }
 
 
 
 
 
 function  action_get_order_info(){
 	$money= isset ( $_GET['amount'] ) ? floatval ( $_GET ['amount'] ) : 0;
 	$user_id= $_GET['user_id'];
 	$supp_id= $_GET['supp_id'];
 	$out_trade_no = get_pay_id($user_id, $supp_id, $amount);
 	
 
  
  
 	 
 	require (dirname(__FILE__) . '/alipay-sdk-PHP-20170829142653/aop/AopClient.php');
 	require (dirname(__FILE__) . '/alipay-sdk-PHP-20170829142653/aop/request/AlipayOpenPublicTemplateMessageIndustryModifyRequest.php');
 	
 	$c = new AopClient;
 	$c->gatewayUrl = "https://openapi.alipay.com/gateway.do";
 	$c->appId = "2017082808420024";
 	$c->rsaPrivateKey = "MIIEvwIBADANBgkqhkiG9w0BAQEFAASCBKkwggSlAgEAAoIBAQDH7lvxsg2/0dQR1+lwDTrjASu0SbVXHEOPBlPvvpYopcQOlewxBZDyauevaABRCX8eWqpC9Lo9f6fRJc8ebKJmpWUI+oEURY2b6V58+6FwE6qOUFy5/4CmJbHokoK9r+sAM4pmdNp8S1dve4d0LTY89+w3M9B7/XwchSZrg9JogC3I9cM752yA2+gcEHB6d/y53CgB6zQx5V/IFYsJ8/fwkgnfliO7O25tBtvX/GpOta8r25diOJNAb4mevGhActNUbwie+sdbnPzG49IRlchVumRyrzMFSoMFhjRdxuujOWYciNwt8eApbBR46vSAANULoac4Vz+SdACFCp2c64atAgMBAAECggEBALY3RdEoqGNVB+UjVXxscmkGXiC4tO+psOEfsUxl9VEik2d/uH6NRifKy1IQhYlWEGdH1rmjdkIqoHZ78SMXe4P15fmi5hXltkSNEzLx25bcNavzDi/u7/99h4IiVcowFQxf7RmoEqEJway3dyKpOi+i/cTqEm+O/Zi4ueVvhUr7O1tXlY0KF8b8Y0Wp5MU1EH+SvJrT7wK9XY6ywx+iYqPHG9lfOmERiyO69zGFftx+LPRVvsBO9SSx1jLjZz1XkVSGs4ZGN1NsJz2aZMuwEw7P9Ka+SrSOebVtU41vdTIsNyT/saL/pSNLbKclfdZfI8lAg+KgzG7woLlH8Xjjh3kCgYEA4251WqqdIAdP1iI//CdzcMlYRiSmLg0lU8lQxSJXSSz+IUMKHhOEYHPTISipAybmTw6W4GaWrJlEzoroRHRfnhIKnCLQnPU2gcwAv4E0MIUSLqPkJVsX6rjfnuyjSSh1AC4cGi/JisMvDKrS5HY33WKzkSz0r3ZjnPzZ1nUzBX8CgYEA4QuRQ/0Q+8QCtn8IqsbiiwuY22LGVymOsnYa+RGZbshWFME4tnmkMgwRgZgIcKLWlAYLS6UxzvqX1IY7JdwcD1hsBKzPt3GWwSjGI/+3zlq0Cq31b2x98wz0/tApvDSXbJ7s5YDUrgUxhMW+jPLZ2niKUkY8nn6RXPdftuhngdMCgYEAohUsiYsiI5tSaHdMRnYPLYgw4vxnelZgDdBhQbzxm/L2wdm3MiwSoXKqOu1xVg+4/wqUuCQkqakpglE4quCM7GhLK28cYV/YkrRCrDh2a0XK6XZft8etydgrdmWLES5GA/TYjkkUR1JHC64KUkt2EM6wznvNfebPoygIT3CDBh0CgYAZ3EVuJaeM9uJE7GYnkcE4rzV6iGg4Xesq8M16r54ND/JsYiPNPD81DRP562mU3/F+gw9LOwl1OnC2GFK1sAx9avDGvMEF2IS1X6UdP+Z0TRIBZCofCr2Sb3u7yFnzaau47K5WVS6bbLdbBYo3EjUcmNt0RuPyZqL3pumV0DEMxwKBgQCPRRYTg0D/Uf55MvWAMVHCqcRgoyVK8MEGy1bGPkIs65zUvok7g+FM9NiNldGGWgiKQqxfOG4sSiAHiqTN477mpNi+e416jFkdwdQTB5cR2UWDiUsspuDQ/4GVv9+OMui9Cb7eERo/rLqxa+m+jcFsMs+ZWYmNi9k7GzzewIj0DA=="; 
//  	$c->format = "json";
 	$c->charset= "utf-8";
 	$c->signType= "RSA2";
 	$c->alipayrsaPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAx+5b8bINv9HUEdfpcA064wErtEm1VxxDjwZT776WKKXEDpXsMQWQ8mrnr2gAUQl/HlqqQvS6PX+n0SXPHmyiZqVlCPqBFEWNm+lefPuhcBOqjlBcuf+ApiWx6JKCva/rADOKZnTafEtXb3uHdC02PPfsNzPQe/18HIUma4PSaIAtyPXDO+dsgNvoHBBwenf8udwoAes0MeVfyBWLCfP38JIJ35YjuztubQbb1/xqTrWvK9uXYjiTQG+JnrxoQHLTVG8InvrHW5z8xuPSEZXIVbpkcq8zBUqDBYY0XcbrozlmHIjcLfHgKWwUeOr0gADVC6GnOFc/knQAhQqdnOuGrQIDAQAB';
 	//实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.open.public.template.message.industry.modify
 	$request = new AlipayOpenPublicTemplateMessageIndustryModifyRequest();
 	
 	 
 	//SDK已经封装掉了公共参数，这里只需要传入业务参数
 	//此次只是参数展示，未进行字符串转义，实际情况下请转义
 	
 	
  $tempPri="aIIEpQIBAAKCAQEAx+5b8bINv9HUEdfpcA064wErtEm1VxxDjwZT776WKKXEDpXsMQWQ8mrnr2gAUQl/HlqqQvS6PX+n0SXPHmyiZqVlCPqBFEWNm+lefPuhcBOqjlBcuf+ApiWx6JKCva/rADOKZnTafEtXb3uHdC02PPfsNzPQe/18HIUma4PSaIAtyPXDO+dsgNvoHBBwenf8udwoAes0MeVfyBWLCfP38JIJ35YjuztubQbb1/xqTrWvK9uXYjiTQG+JnrxoQHLTVG8InvrHW5z8xuPSEZXIVbpkcq8zBUqDBYY0XcbrozlmHIjcLfHgKWwUeOr0gADVC6GnOFc/knQAhQqdnOuGrQIDAQABAoIBAQC2N0XRKKhjVQflI1V8bHJpBl4guLTvqbDhH7FMZfVRIpNnf7h+jUYnystSEIWJVhBnR9a5o3ZCKqB2e/EjF3uD9eX5ouYV5bZEjRMy8duW3DWr8w4v7u//fYeCIlXKMBUMX+0ZqBKhCcGst3ciqTovov3E6hJvjv2YuLnlb4VK+ztbV5WNChfG/GNFqeTFNRB/krya0+8CvV2OssMfomKjxxvZXzphEYsjuvcxhX7cfiz0Vb7ATvUksdYy42c9V5FUhrOGRjdTbCc9mmTLsBMOz/Smvkq0jnm1bVONb3UyLDck/7Gi/6UjS2ynJX3WXyPJQIPioMxu8KC5R/F444d5AoGBAONudVqqnSAHT9YiP/wnc3DJWEYkpi4NJVPJUMUiV0ks/iFDCh4ThGBz0yEoqQMm5k8OluBmlqyZRM6K6ER0X54SCpwi0Jz1NoHMAL+BNDCFEi6j5CVbF+q4357so0kodQAuHBovyYrDLwyq0uR2N91is5Es9K92Y5z82dZ1MwV/AoGBAOELkUP9EPvEArZ/CKrG4osLmNtixlcpjrJ2GvkRmW7IVhTBOLZ5pDIMEYGYCHCi1pQGC0ulMc76l9SGOyXcHA9YbASsz7dxlsEoxiP/t85atAqt9W9sffMM9P7QKbw0l2ye7OWA1K4FMYTFvozy2dp4ilJGPJ5+kVz3X7boZ4HTAoGBAKIVLImLIiObUmh3TEZ2Dy2IMOL8Z3pWYA3QYUG88Zvy9sHZtzIsEqFyqjrtcVYPuP8KlLgkJKmpKYJROKrgjOxoSytvHGFf2JK0Qqw4dmtFyul2X7fHrcnYK3ZlixEuRgP02I5JFEdSRwuuClJLdhDOsM57zX3mz6MoCE9wgwYdAoGAGdxFbiWnjPbiROxmJ5HBOK81eohoOF3rKvDNeq+eDQ/ybGIjzTw/NQ0T+etplN/xfoMPSzsJdTpwthhStbAMfWrwxrzBBdiEtV+lHT/mdE0SAWQqHwq9km97u8hZ82mruOyuVlUum2y3WwWKNxI1HJjbdEbj8mai96bpldAxDMcCgYEAj0UWE4NA/1H+eTL1gDFRwqnEYKMlSvDBBstWxj5CLOuc1L6JO4PhTPTYjZXRhloIikKsXzhuLEogB4qkzeO+5qTYvnuNeoxZHcHUEweXEdlFg4lLLKbg0P+Blb/fjjLovQm+3hEaP6y6sWvpvo3BbDLPmVmJjYvZOxs83sCI9Aw=";
 	
 	//构造业务请求参数的集合(订单信息)
 	$content = array();
 	$content['timeout_express'] = '30m';//该笔订单允许的最晚付款时间
 	$content['product_code'] = 'QUICK_MSECURITY_PAY';//销售产品码，商家和支付宝签约的产品码，为固定值QUICK_MSECURITY_PAY
 	$content['total_amount'] = "".$money."";//订单总金额(必须定义成浮点型)
 	$content['subject'] = '1';//商品的标题/交易标题/订单标题/订单关键字等
 	$content['body'] = 'ceshi';
 	$content['out_trade_no'] ="121851";// "".$out_trade_no."";//商户网站唯一订单号
//  	$content['seller_id'] = '';//收款人账号
//  	$content['store_id'] = 'BJ_001';//商户门店编号
 	
 	$biz_content=  json_encode($content);//$content是biz_content的值,将之转化成字符串
 	
 	
 	//组装系统参数
 	$time = gmtime();
 
 	$sysParams["biz_content"]=$biz_content;
 	$sysParams["method"] = "alipay.trade.app.pay";//$request->getApiMethodName();
 	$sysParams["charset"] = "utf-8";
 	$sysParams["version"] = "1.0";
 	$sysParams["notify_url"] = "http://shop.xiangbai315.com/er_wei_ma.php";
 	$sysParams["app_id"] = $c->appId;
 	$sysParams["timestamp"] = "2016-07-29 16:55:53";//local_date("Y-m-d H:i:s", $time);
 	
 	$sysParams["sign_type"] = "RSA2";
 	
    
 	$filePath =  dirname(__FILE__) . "/alipay-sdk-PHP-20170829142653/aop/rsa_private_key.pem";
 	$filePath2 =  dirname(__FILE__) . "/alipay-sdk-PHP-20170829142653/aop/rsa_private_key_pkcs8.pem"; 
 	$paramStr = $c->getSignContent($sysParams);
 	
 	file_put_contents (  "test.txt", "订单号:".$out_trade_no."\r\n\r\n", FILE_APPEND );
 	file_put_contents (  "test.txt", $paramStr."\r\n\r\n", FILE_APPEND );
 	$sign= $c->alonersaSign($paramStr,$filePath,'RSA2',true);
 
 	$sign=  urlencode($sign);
 
 	$requestUrl= $c->getSignContentUrlencode($sysParams);
 	$requestUrl=$requestUrl."&sign="  . $sign;
 	file_put_contents (  "test.txt", $requestUrl."\r\n\r\n", FILE_APPEND );
 	header("Content-type: text/html; charset=utf-8"); 
 	die($requestUrl)  ;
 	
 }
 
 
?>