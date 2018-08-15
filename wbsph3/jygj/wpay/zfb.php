<?php
$app_id="2017050207082383";
$user=$_GET['user'];
$redirect_uri=UrlEncode("http://zfb.jsguoji.cn/pay/notify.php?user=$user");
$url="https://openauth.alipay.com/oauth2/appToAppAuth.htm?app_id=$app_id&scope=auth_base&redirect_uri=$redirect_uri";
header("Location:".$url);
?>