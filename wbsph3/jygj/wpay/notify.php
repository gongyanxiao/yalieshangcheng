<?php
include_once "AopSdk.php";
include_once "aop/AopClient.php";
require_once 'aop/request/AlipayUserUserinfoShareRequest.php';
$app_auth_code=$_GET['app_auth_code'];
$aop = new AopClient ();
$aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
$aop->appId = '2017041306681958';
$aop->rsaPrivateKey = '+taa52TFtlqpUHc7Xquf0o65NxatInSqMUfiv6b53ADDXVFvInmlOKZ1G3l0cjZ8KRUNVmAJP6oVjeAfaOfvuVDE9yt/vzPOG9lwpDJpzD7jo1C7+HFcJWnSYUxWyPRsBZZUjSoE/Vsfx/ryocs8F9F7BIyMYvp+IAHv6YW8aAv3hRTCa29HvWkIdr019NAgMBAAECggEBAJkxUDRM/mgHR6/EwmpNvlbTVY6Rlk67rC0bz8TSiAxzwjDlPhVYHsKZMYNFQZwdjSvbaSIkvJwGeKvWtmWqZu/DI1QC567wp+GYc5IrWQO7p1SWSW3YMzYwGNBYa9Ss5pjU+tTy8sneuS9aCk5vyKwQCS8Bn9SJ5HnyIKPkW+1PLp5r4FmYz2qG6wr2kY4VpqNWGvDzgWXlvXUB/IX/+1D83mOO9RPxQQ+/p1/nEOGFHfiCuP2GSKxNPSBvmxensUiAIhY/RfHybKxMDiiTXQvIQzOvkB86fUgGApi35xe5q4FGa5YU6s9ebPRIrM3pRAY4UzG7guCauoMz2k2ZIIkCgYEA7uE+mOHSmxFoMEOBNZnswl0J6EG3zDOf8yNTF0Z7eKVMfZ/tt36BBXEFuVZi9JFvrflbBJiyAHBB+JB/nJFX7t/2BQjOU9SPN+ICR4gXIAxA24LwWGRSzDCa+4ESpu1oJ3tYCU2E5pMqNnhu+ARplotrwMQLMAl5gAjU8YfHBrsCgYEA5nYeJE3D1f0ChJ3DkeJDxYCdkhdKNeb0P+dW/H1uwL7rd9OxQ2rFItvxf6mVSWxwgWVWPLEqLA8PhVA/UVD59d14KoWaA1c8NDJtQWxrPQrOzUUpzl0GztS+ryUMLaUkKzxxnj/Fwqg6ys2/ACBMw2DEho0pg1aaV8AZUnr6RZcCgYBZe9sdHgrst9qVqdPvJlgAfyXE8UlOn1Adnm/z2h1KlnFO5egAwszGIw6H3Qp4nzp8q1ojIKgdbe/okwiat/9+pjrcq+3OjWORBYLhFOPEx5JMKIKsOfiiMNr9t94q1egcUiIqafWNAjff68F1+wEqudOFhGrEdegjxCswxzIRHQKBgQC7iQ7KGAXuki6EYCUPB4KCq0CN6ynWZxQHFGeymxeO/U17euZap/23eicw7Xyv/PAoO6BLTxe0NqU4pK6Bq1Vcf1YTtBg587jn+MjzXhh92dejLk7wwL6TfIeW2pzoX';
$aop->alipayrsaPublicKey='MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAnAadJdC8+poXTFy0oJn5UE+4V0/A0C3Rd0cgxRnW8X6vbuGQz2wGpQh/YO1npzMA3TgGyN36UacxuAe9GjmICMNboyaekAtsFlNMnAg2orJ4Tmcz4P66H6pY4cN3tw8GS0zEBM/ZhUbZlw5eIdpSyeEdBqYHH8g56esD0otxgrrI5QXOnQFXW0qw3zYXw35Rka/y6OKXrnoU2Or9BBXTjWbXaIXUb2PU3gV63/oQNQVBklEX/+Q4czfh0LnzaPsU6oEGKWtLqScDdn85e17Hry5iX6y3VRzoHsNg8y65j5oAcjBs8WBrOJoJCTs4al1turgpUIH7episcbtVdE2FxQIDAQAB';
$aop->apiVersion = '1.0';
$aop->signType = 'RSA2';
$aop->postCharset='GBK';
$aop->format='json';
$request = new AlipayUserUserinfoShareRequest ();
$result= $aop->execute($request,"accessToken");
 
$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
$resultCode = $result->$responseNode->code;
if(!empty($resultCode)&&$resultCode == 10000){
echo "成功";
} else {
echo "失败";
}

?>  
