<?php

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');

$tdate = 1492099201;
$edate = 1492160400;
//if ($edate > $tdate) {
//    echo '1';
//} else {
//    echo '2';
//}
echo gmtime()."<br/>";
echo time();
//echo local_date("Y-m-d H:i:s");
//echo local_date("Y-m-d H:i:s",$tdate);
//echo local_date("Y-m-d H:i:s",$edate);
exit;

require_once (ROOT_PATH . 'includes/modules/PHPKit-CMBC/php_java.php');
include('includes/modules/PHPKit-CMBC/CMBC_SDK_TOOLS.php');
$cmbc_sdk = new CMBC_SDK_TOOLS(0);

//$cmbc_sdk->toCancelOrder("2210","1");

$data = $cmbc_sdk->toCreateOrder("API_ZFBQRCODE", 2, "统一下单DEMO-API_ZFBQRCODE2", 16, "http://wxpay.cmbc.com.cn/cmbcpaydemo/NoticeServlet?name=notice", "");
;
var_dump($data);
////
//$data = $cmbc_sdk->toCreateOrder("API_WXQRCODE", 2, "统一下单DEMO-API_WXQRCODE1", 18, "http://wxpay.cmbc.com.cn/cmbcpaydemo/NoticeServlet?name=notice", "");
//var_dump($data);
//exit;
//$data = $cmbc_sdk->toCreateOrder("H5_WXJSAPI", 5, "统一下单DEMO-API_WXJSAPI5", 25, "http://wxpay.cmbc.com.cn/cmbcpaydemo/NoticeServlet?name=notice","", "http://wxpay.cmbc.com.cn/cmbcpaydemo/NoticeServlet?name=notice");
//var_dump($data);
//exit;
//https://qr.alipay.com/bax003876vhhdgmlszhh2068
//weixin://wxpay/bizpayurl?pr=AyArxWR
//prepayId=wx201704050901166985e1327e0277539055|timeStamp=1491354076302|nonceStr=bkib0gu7ius61m7ezisxfn7hhlbqcei2|paySign=3958851BC2D323D8890FF3898040C1EB|appId=wx2b0ad640ef47938b
//
//
//
//<div id="paymentDiv"><div style="text-align:center" id="qrcode"><p>微信扫描，立即支付</p><div><img src="data:image/gif;base64,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" width="130" height="130"></div></div><div id="wxPhone"></div></div>
//$payInfo = $cmbc_sdk->toGetPayInfo("API_WXQRCODE", "weixin://wxpay/bizpayurl?pr=AyArxWR");
//echo($payInfo);

//$payInfo1 = $cmbc_sdk->toGetPayInfo("API_WXQRCODE", "weixin://wxpay/bizpayurl?pr=XWB7zXC");
//echo($payInfo1)."<br/>";
//
//$zfbpayInfo = $cmbc_sdk->toGetPayInfo("API_ZFBQRCODE", "https://qr.alipay.com/bax003876vhhdgmlszhh2068");
//echo $zfbpayInfo."<br/>";
//
//$payInfo = $cmbc_sdk->toGetPayInfo("H5_WXJSAPI", "prepayId=wx201704050901166985e1327e0277539055|timeStamp=1491354076302|nonceStr=bkib0gu7ius61m7ezisxfn7hhlbqcei2|paySign=3958851BC2D323D8890FF3898040C1EB|appId=wx2b0ad640ef47938b|backFun=backFun");
//echo $payInfo."<br/>";
//exit;

//include('includes/modules/PHPKit-CMBC/php_java.php');
////include('includes/modules/PHPKit-CMBC/Test/sign.php');
////$data = array("platformId" => "cust0001", "merchantNo" => "M01002016070000000789",
////    "selectTradeType" => "API_WXQRCODE", "amount" => "2", "orderInfo" => "统一下单DEMO-API_WXQRCOD199", "merchantSeq" => "A00002016120000000294T133802225",
////    "transDate" => "20170401", "transTime" => "20170401133802225", "notifyUrl" => "http://wxpay.cmbc.com.cn/cmbcpaydemo/NoticeServlet?name=notice",
////    "remark" => "");
//$data = array("platformId" => "A00002016120000000294", "merchantNo" => "M01002016090000001273",
//    "selectTradeType" => "API_ZFBQRCODE", "amount" => "2", "orderInfo" => "统一下单DEMO-API_ZFBQRCODE", "merchantSeq" => "A00002016120000000294T133802231",
//    "transDate" => "20170401", "transTime" => "20170401133802231", "notifyUrl" => "http://xiangbai.king51.com/testNotify.php",
//    "remark" => "");
//$data_string = json_encode($data);
////echo $data_string;
//echo "<br/>";
//try {
//    //1、签名
//    $signAlg = "SM3withSM2";
//    $base64SourceData = base64_encode($data_string);
//    //$base64P12Data = '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';//$CUST_PRI_SIGN; // file_get_contents("http://xiangbai.test.com/includes/modules/PHPKit-CMBC/Test/cust0001.sm2");//$CUST_PRI_SIGN; //使用商户私钥加密签名
//    $base64P12Data = "MIIDfgIBATBHBgoqgRzPVQYBBAIBBgcqgRzPVQFoBDClyXV+hNlZh3J71DfGgmQuXE/Meyqo68/F
//Ik7Zb+vaJhsa7RCwhkA5Xn/IRezDQ7AwggMuBgoqgRzPVQYBBAIBBIIDHjCCAxowggK9oAMCAQIC
//BTABAnBYMAwGCCqBHM9VAYN1BQAwKzELMAkGA1UEBhMCQ04xHDAaBgNVBAoME0NGQ0EgU00yIFRF
//U1QgT0NBMjEwHhcNMTUwMzA2MDMwMzMyWhcNMTYwMzA2MDMwMzMyWjB0MQswCQYDVQQGEwJDTjEN
//MAsGA1UECgwEQ01CQzESMBAGA1UECwwJQ01CQ19EQ01TMRUwEwYDVQQLDAxJbmRpdmlkdWFsLTEx
//KzApBgNVBAMMIjAzMDVAMDk1MDA1ODAwNjcxNjEzMUB1c2VyNTQ5NTM3QDEwWTATBgcqhkjOPQIB
//BggqgRzPVQGCLQNCAATHK/KpRVf8e4pUFs2ov+hAjgoasBbxSi4yhEP3u0h9xlDaTw4fZyKExwbm
//ADS+NXythbCpkIP/clhcibf3x2m2o4IBgTCCAX0wHwYDVR0jBBgwFoAU4n62ELuU6xXmrtEVCv/o
//16BXOZ0wSAYDVR0gBEEwPzA9BghggRyG7yoCAjAxMC8GCCsGAQUFBwIBFiNodHRwOi8vd3d3LmNm
//Y2EuY29tLmNuL3VzL3VzLTEzLmh0bTCBzgYDVR0fBIHGMIHDMIGSoIGPoIGMhoGJbGRhcDovLzIx
//MC43NC40Mi4xMDozODkvY249Y3JsMjUsT1U9U00yLE9VPUNSTCxPPUNGQ0EgU00yIFRFU1QgT0NB
//MjEsQz1DTj9jZXJ0aWZpY2F0ZVJldm9jYXRpb25MaXN0P2Jhc2U/b2JqZWN0Y2xhc3M9Y1JMRGlz
//dHJpYnV0aW9uUG9pbnQwLKAqoCiGJmh0dHA6Ly8yMTAuNzQuNDIuMy9PQ0EyMS9TTTIvY3JsMjUu
//Y3JsMAsGA1UdDwQEAwID6DAdBgNVHQ4EFgQUWZTc4hyNnBa1uRKdcptd3JDjKBkwEwYDVR0lBAww
//CgYIKwYBBQUHAwIwDAYIKoEcz1UBg3UFAANJADBGAiEAsKWbuI3Kd4sujhzRXwbesYKjT74vIFYM
//rvLLDVYe2YQCIQC/RjQoNpJXclt+xbUSpqYuK1rC7LEKxEN1YanXJ3SxMg==";
//    $p12Password = "123123";
//    $ret = lajp_call("cfca.sadk.api.SignatureKit::P1SignMessage", $signAlg, $base64SourceData, $base64P12Data, $p12Password);
//    $signData = json_decode($ret, true);
//    if (isset($signData['Base64SignatureData']) && !empty($signData['Base64SignatureData'])) {
//        //2、数据加密
//        $sign = $signData['Base64SignatureData'];
//        $jmData = array("sign" => $sign, "body" => $data_string);
//        $jsonJmData = json_encode($jmData);
//        echo '签名：' . $sign . "<br/>";
////        echo '加密前信息：' . $jsonJmData . "<br/>";
//
//        $signAlg = "SM4/CBC/PKCS7Padding";
//        $base64SourceData = $jsonJmData;
//        //$base64CertData = 'MIIDdzCCAl+gAwIBAgIFEAUxV5AwDQYJKoZIhvcNAQEFBQAwIzELMAkGA1UEBhMCQ04xFDASBgNVBAoMC0NBMjAyOC0yMDMxMB4XDTE1MDMyOTAzMzE0MloXDTE2MDMyODAzMzE0MlowNTELMAkGA1UEBhMCQ04xFDASBgNVBAoMC0NBMjAyOC0yMDMxMRAwDgYDVQQDDAd0ZXN0MzI5MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDFm+6NCVS/g+D7y6XrAizq+G2rQl84nkiy/SKrnDZzhnuphiUuijW/6mbVuKaDUkFxbaba3z97rl66Q2fTYTEbC2ImUClWtnEiCM4IW/rXDVkp6sKD0utBP4mXvECqVi5XnyVrTLNWpqlGloUyyj0MtKcblgIYGvRUbHNWj+JXLQIDAQABo4IBIjCCAR4wHwYDVR0jBBgwFoAUQh91vsC1py4NpqRVNNbJMtnVCkYwgc4GA1UdHwSBxjCBwzCBjqCBi6CBiIaBhWxkYXA6Ly8xOTIuMTY4LjkzLjExNDozODkvY249Y3JsMzUyOSxvdT1SU0EsT1U9Q1JMLE89Q0EyMDI4LTIwMzEsQz1DTj9jZXJ0aWZpY2F0ZVJldm9jYXRpb25MaXN0P2Jhc2U/b2JqZWN0Y2xhc3M9Y1JMRGlzdHJpYnV0aW9uUG9pbnQwMKAuoCyGKmh0dHA6Ly8xOTIuMTY4LjEyMC4xMjcvY3JsL1JTQS9jcmwzNTI5LmNybDALBgNVHQ8EBAMCA/gwHQYDVR0OBBYEFFwnP005w7UYf172RmulL14U05htMA0GCSqGSIb3DQEBBQUAA4IBAQB38XeCXUwoWvUcI68tkxNIWTmw9qATzgQobnnwxaXFPunHT9aiEIdvHPZkL1jzsoBmFEK3sWOKRtqAd7qq3GHqxIP2zfg01O5+DPDWTY+L2Tfsg5bekfKNjd5I9D+ZrxUDkAyxPVugyluZYA0UHDM1bCKFW/5TyxFQqGUM7X3GsTqN7fBNf15cH8myl/3Inr/pc0zXryvf19fEv1+1KYYkU5xOVobqJj1LBe+d7ax8gmvU4lkRWFuy3wnKzetXbiNB8QQ6dApiOyWHRNDVLPL2n4Aom++bFCh7ceZVAb0hqWiGIVBp8y7AYrq7D23V72PSl7tjl72FBLRwUt+8SoTq';//$CUST_COMMON_SIGN; //file_get_contents("http://xiangbai.test.com/includes/modules/PHPKit-CMBC/Test/cmbcTest.cer"); //$CUST_COMMON_SIGN; //使用商户公钥加密
////        print_r(openssl_x509_parse($base64CertData));
////        exit;
//        $base64CertData = "-----BEGIN CERTIFICATE-----
//MIIDGjCCAr2gAwIBAgIFMAE1UAQwDAYIKoEcz1UBg3UFADArMQswCQYDVQQGEwJD
//TjEcMBoGA1UECgwTQ0ZDQSBTTTIgVEVTVCBPQ0EyMTAeFw0xNTEyMDQwODU4MDla
//Fw0xNjEyMDQwODU4MDlaMHQxCzAJBgNVBAYTAkNOMQ0wCwYDVQQKDARDTUJDMRIw
//EAYDVQQLDAlDTUJDX0RDTVMxFTATBgNVBAsMDEluZGl2aWR1YWwtMTErMCkGA1UE
//AwwiMDMwNUAwMzE4NzQ0ODYyNzc5ODM3QHVzZXIwMDMyNDRAMTBZMBMGByqGSM49
//AgEGCCqBHM9VAYItA0IABMaqALdw4tIJEcYg2Bv6TMNj8Odv9cmK5+QVnrjjwxnL
//MOoXuzn17R0E7YBAe7j87Krhxqstx6qLcuvGh2jJGUOjggGBMIIBfTAfBgNVHSME
//GDAWgBTifrYQu5TrFeau0RUK/+jXoFc5nTBIBgNVHSAEQTA/MD0GCGCBHIbvKgIC
//MDEwLwYIKwYBBQUHAgEWI2h0dHA6Ly93d3cuY2ZjYS5jb20uY24vdXMvdXMtMTMu
//aHRtMIHOBgNVHR8EgcYwgcMwgZKggY+ggYyGgYlsZGFwOi8vMjEwLjc0LjQyLjEw
//OjM4OS9jbj1jcmw5NSxvdT1TTTIsT1U9Q1JMLE89Q0ZDQSBTTTIgVEVTVCBPQ0Ey
//MSxDPUNOP2NlcnRpZmljYXRlUmV2b2NhdGlvbkxpc3Q/YmFzZT9vYmplY3RjbGFz
//cz1jUkxEaXN0cmlidXRpb25Qb2ludDAsoCqgKIYmaHR0cDovLzIxMC43NC40Mi4z
//L09DQTIxL1NNMi9jcmw5NS5jcmwwCwYDVR0PBAQDAgPoMB0GA1UdDgQWBBS1x/e/
//puEJbLQnTtwm2y/+fP1b2jATBgNVHSUEDDAKBggrBgEFBQcDAjAMBggqgRzPVQGD
//dQUAA0kAMEYCIQCba06exmipgEcbA1uhsM19ldoVY6QDlIqx1+8fjy7xAgIhALbo
//idd/NmhTdWKgbs8FRyaYuEbHuu6BNWYeC46GWzTD
//-----END CERTIFICATE-----";
//        $ret = lajp_call("cfca.sadk.api.EnvelopeKit::envelopeMessage", $base64SourceData, $signAlg, $base64CertData);
////        echo "'加密后信息：'{$ret}'<br/>";
//        //Base64EnvelopeMessage
//        $jmAfterData = json_decode($ret, true);
//        if (isset($jmAfterData['Base64EnvelopeMessage']) && !empty($jmAfterData['Base64EnvelopeMessage'])) {
//            //3、生成报文
//            $businessContext = $jmAfterData['Base64EnvelopeMessage'];
////            echo "报文数据：" . $businessContext . "<br/>";
//            $bwData = array("businessContext" => $businessContext, "merchantNo" => "", "merchantSeq" => "", "reserve1" => "", "reserve2" => "", "reserve3" => "", "reserve4" => "", "reserve5" => "", "reserveJson" => "",
//                "securityType" => "", "sessionId" => "", "source" => "", "transCode" => "", "transDate" => "", "transTime" => "", "version" => "");
//            $bwJsonData = json_encode($bwData);
//
//            //4、生成订单所需要的信息
//            $orderString = curl_post_json('http://wxpay.cmbc.com.cn/mobilePlatform/appserver/lcbpPay.do', $bwJsonData);
//            $contextJsonString = json_decode($orderString, true);
//            if (isset($contextJsonString['businessContext']) && !empty($contextJsonString['businessContext'])) {
//                $businessContext = $contextJsonString['businessContext'];
////                echo "订单返回业务报文:" . $businessContext . "<br/>";
//                //5、解密
//                $base64EnvelopeData = $businessContext;
//                $signAlg = "SM4/CBC/PKCS7Padding";
//
//                $base64P12Data = "MIIDfgIBATBHBgoqgRzPVQYBBAIBBgcqgRzPVQFoBDClyXV+hNlZh3J71DfGgmQuXE/Meyqo68/F
//Ik7Zb+vaJhsa7RCwhkA5Xn/IRezDQ7AwggMuBgoqgRzPVQYBBAIBBIIDHjCCAxowggK9oAMCAQIC
//BTABAnBYMAwGCCqBHM9VAYN1BQAwKzELMAkGA1UEBhMCQ04xHDAaBgNVBAoME0NGQ0EgU00yIFRF
//U1QgT0NBMjEwHhcNMTUwMzA2MDMwMzMyWhcNMTYwMzA2MDMwMzMyWjB0MQswCQYDVQQGEwJDTjEN
//MAsGA1UECgwEQ01CQzESMBAGA1UECwwJQ01CQ19EQ01TMRUwEwYDVQQLDAxJbmRpdmlkdWFsLTEx
//KzApBgNVBAMMIjAzMDVAMDk1MDA1ODAwNjcxNjEzMUB1c2VyNTQ5NTM3QDEwWTATBgcqhkjOPQIB
//BggqgRzPVQGCLQNCAATHK/KpRVf8e4pUFs2ov+hAjgoasBbxSi4yhEP3u0h9xlDaTw4fZyKExwbm
//ADS+NXythbCpkIP/clhcibf3x2m2o4IBgTCCAX0wHwYDVR0jBBgwFoAU4n62ELuU6xXmrtEVCv/o
//16BXOZ0wSAYDVR0gBEEwPzA9BghggRyG7yoCAjAxMC8GCCsGAQUFBwIBFiNodHRwOi8vd3d3LmNm
//Y2EuY29tLmNuL3VzL3VzLTEzLmh0bTCBzgYDVR0fBIHGMIHDMIGSoIGPoIGMhoGJbGRhcDovLzIx
//MC43NC40Mi4xMDozODkvY249Y3JsMjUsT1U9U00yLE9VPUNSTCxPPUNGQ0EgU00yIFRFU1QgT0NB
//MjEsQz1DTj9jZXJ0aWZpY2F0ZVJldm9jYXRpb25MaXN0P2Jhc2U/b2JqZWN0Y2xhc3M9Y1JMRGlz
//dHJpYnV0aW9uUG9pbnQwLKAqoCiGJmh0dHA6Ly8yMTAuNzQuNDIuMy9PQ0EyMS9TTTIvY3JsMjUu
//Y3JsMAsGA1UdDwQEAwID6DAdBgNVHQ4EFgQUWZTc4hyNnBa1uRKdcptd3JDjKBkwEwYDVR0lBAww
//CgYIKwYBBQUHAwIwDAYIKoEcz1UBg3UFAANJADBGAiEAsKWbuI3Kd4sujhzRXwbesYKjT74vIFYM
//rvLLDVYe2YQCIQC/RjQoNpJXclt+xbUSpqYuK1rC7LEKxEN1YanXJ3SxMg==";
//                $p12Password = "123123";
//
//                $ret = lajp_call("cfca.sadk.api.EnvelopeKit::openEvelopedMessage", $base64EnvelopeData, $signAlg, $base64P12Data, $p12Password);
//
//                $jmBwData = json_decode($ret, true);
//                if (isset($jmBwData['Base64SourceString']) && !empty($jmBwData['Base64SourceString'])) {
////                    echo base64_decode($jmBwData['Base64SourceString']);exit;
//                    $bs64DecodeData = json_decode(base64_decode($jmBwData['Base64SourceString']), true);
////                    var_dump($bs64DecodeData);
////                    exit;
////                    print_r($bs64DecodeData);EXIT;
//                    //验证签名
//                    $signAlg = "SM3withSM2";
//                    $base64SourceData = base64_encode($bs64DecodeData['body']); //$jmBwData['Base64SourceString'];
//                    $base64X509CertData = "-----BEGIN CERTIFICATE-----
//MIIDGjCCAr2gAwIBAgIFMAE1UAQwDAYIKoEcz1UBg3UFADArMQswCQYDVQQGEwJD
//TjEcMBoGA1UECgwTQ0ZDQSBTTTIgVEVTVCBPQ0EyMTAeFw0xNTEyMDQwODU4MDla
//Fw0xNjEyMDQwODU4MDlaMHQxCzAJBgNVBAYTAkNOMQ0wCwYDVQQKDARDTUJDMRIw
//EAYDVQQLDAlDTUJDX0RDTVMxFTATBgNVBAsMDEluZGl2aWR1YWwtMTErMCkGA1UE
//AwwiMDMwNUAwMzE4NzQ0ODYyNzc5ODM3QHVzZXIwMDMyNDRAMTBZMBMGByqGSM49
//AgEGCCqBHM9VAYItA0IABMaqALdw4tIJEcYg2Bv6TMNj8Odv9cmK5+QVnrjjwxnL
//MOoXuzn17R0E7YBAe7j87Krhxqstx6qLcuvGh2jJGUOjggGBMIIBfTAfBgNVHSME
//GDAWgBTifrYQu5TrFeau0RUK/+jXoFc5nTBIBgNVHSAEQTA/MD0GCGCBHIbvKgIC
//MDEwLwYIKwYBBQUHAgEWI2h0dHA6Ly93d3cuY2ZjYS5jb20uY24vdXMvdXMtMTMu
//aHRtMIHOBgNVHR8EgcYwgcMwgZKggY+ggYyGgYlsZGFwOi8vMjEwLjc0LjQyLjEw
//OjM4OS9jbj1jcmw5NSxvdT1TTTIsT1U9Q1JMLE89Q0ZDQSBTTTIgVEVTVCBPQ0Ey
//MSxDPUNOP2NlcnRpZmljYXRlUmV2b2NhdGlvbkxpc3Q/YmFzZT9vYmplY3RjbGFz
//cz1jUkxEaXN0cmlidXRpb25Qb2ludDAsoCqgKIYmaHR0cDovLzIxMC43NC40Mi4z
//L09DQTIxL1NNMi9jcmw5NS5jcmwwCwYDVR0PBAQDAgPoMB0GA1UdDgQWBBS1x/e/
//puEJbLQnTtwm2y/+fP1b2jATBgNVHSUEDDAKBggrBgEFBQcDAjAMBggqgRzPVQGD
//dQUAA0kAMEYCIQCba06exmipgEcbA1uhsM19ldoVY6QDlIqx1+8fjy7xAgIhALbo
//idd/NmhTdWKgbs8FRyaYuEbHuu6BNWYeC46GWzTD
//-----END CERTIFICATE-----";
//                    $base64P1SignatureData = $bs64DecodeData['sign'];
//                    ;
//                    $verifyString = lajp_call("cfca.sadk.api.SignatureKit::P1VerifyMessage", $signAlg, $base64SourceData, $base64X509CertData, $base64P1SignatureData);
//                    $verifyJson = json_decode($verifyString, true);
//                    if (isset($verifyJson['Result']) && $verifyJson['Result'] == "True") {
//                        //二维码解析
//                        $picUrl = json_decode($bs64DecodeData['body'], true);
//                        var_dump($picUrl);
//                        exit;
//                        exit;
//                        $datas1 = explode('=', $picUrl['remark']);
//                        $img = $datas1[1] . "=" . $datas1[2];
//                        define('IN_ECS', true);
//                        $payment_path = '/includes/modules/payment/wxnative/';
//                        $button = '<div id="paymentDiv"><div style="text-align:center" id="qrcode"></div><div id="wxPhone"></div></div>';
//                        echo $button;
//                        $javascript = '<style>#paymentDiv{width:760px}#wxPhone{float:left;width:379px;height:421px;padding-left:50px;background:url(' . $payment_path . 'phone-bg.png) 50px 0 no-repeat}#qrcode{display:block;float:left;margin-top:30px}#qrcode img{height:260px;width:260px;padding:5px;border:1px solid #ddd}#qrcode p{padding:15px 0;background:#157058;color:#fff;margin:10px 0}</style> ';
//                        $javascript.='<script src="' . $payment_path . 'qrcode.js"></script>';
//                        //参数1表示图像大小，取值范围1-10；参数2表示质量，取值范围'L','M','Q','H'
//                        $javascript.='<script>
//            if("' . $img . '"!==""){
//                var url = "' . $img . '";
//                var qr = qrcode(10, "M");qr.addData(url);qr.make();
//                var wording=document.createElement("p");
//                wording.innerHTML = "微信扫描，立即支付";
//                var code=document.createElement("DIV");
//                code.innerHTML = qr.createImgTag();
//                var element=document.getElementById("qrcode");
//                element.appendChild(wording);
//                element.appendChild(code);
//            }
//            </script>';
//                        echo $javascript;
//
//                        exit;
//                    }
//                }
//                //6、验证签名
//            } else {
//                //TODO:log error
//            }
//
//            //echo "生成订单数据:" . $orderString;
//        }
//    } else {
//        echo "签名失败：" . $ret;
//        exit;
//    }
//} catch (Exception $e) {
//    echo "Err:{$e}<br>";
//}
//
///**
// * CURL方式POST json数据
// * @param type $url
// * @param type $jsonData
// * @return type
// */
//function curl_post_json($url, $jsonData) {
//    $ch = curl_init($url);
//    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
//    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
//    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//        'Content-Type: application/json',
//        'Content-Length: ' . strlen($jsonData))
//    );
//    $result = curl_exec($ch);
//    curl_close($ch);
//    return $result;
//}
//
//$ch = curl_init('http://wxpay.cmbc.com.cn/mobilePlatform/appserver/lcbpPay.do');
//curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
//curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//    'Content-Type: application/json',
//    'Content-Length: ' . strlen($data_string))
//);
//
//$result = curl_exec($ch);
//var_dump($result);
//exit;
