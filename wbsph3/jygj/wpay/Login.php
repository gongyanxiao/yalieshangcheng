<?
header("Content-type:text/html;charset=utf-8");
if (isset($_GET['code'])){
	$code=$_GET['code'];
}else{
    echo "NO CODE";
}
$url='https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx24f4bca011ce867e&secret=3eca2765b7277e95300f7d0ac59863d3&code='.$code.'&grant_type=authorization_code';
$info=file_get_contents($url);
$obj1 = json_decode($info);
$openids=$obj1->{"openid"};
    include_once "../wap/mall/config/check.php";
include("../wap/mall/config.php");
include("../mall/pay/wx/query.php");
?>
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <title>在线充值_<?=_TITLE_?></title>
    <meta name="keyword" content="<?=_KEYWORD_?>"/>
    <meta name="description" content="<?=_DESCRIPTION_?>"/>
    <link href="/Public/ico.ico" type="image/x-icon" rel="shortcut icon" /> 
	
    <link rel="stylesheet" type="text/css" href="/Public/wap/css/index.css">
    <script type="text/javascript" src="/jygj/Public/home/js/jquery-1.js"></script>
	  <script type="text/javascript" src="/jygj/Public/home/js/jquery-1.7.2.js"></script>
	<style>
	.ktp_y{height: auto}
	.STYLE1 {color: #FF0000}
    .STYLE2 {color: #000099}
    </style>

</head>
<body>
<div id="jygj"></div>
<div class="warp">
	<div class="warp_q">
        <a href="../wap/mall/b_center.html"></a>
    <span>微信在线充值</span></div>
	<!-- <span class="STYLE2">（由于移动端充值接口正在调试，建议大家大额充值用电脑端充值）</span> -->
	<form action="index.php" method="post" id="form">	
    <div class="ktp_u" style="float:left;">
        <ul class="ktp_y">
            <li style="text-align:left;">
            <b>账号余额:<?php
						include "../wap/mall/config/zt_config.php";
date_default_timezone_set("Asia/Shanghai");
$db=mysql_connect($db_host,$db_user,$db_pwd);
mysql_query("SET NAMES $coding"); 
mysql_select_db($db_database);
$yh=$_COOKIE['ECS']['username'];
$sql="select lx from xbmall_users where user_name='$yh'";
$qs1=mysql_query($sql);
$sf1=mysql_fetch_assoc($qs1);
$lx=$sf1["lx"];
if($lx=="0"){
exit();
}
$user=$_COOKIE['ECS']['username'];
$sql="select sum(je) as je1 from zt_cz where zt in(1,2) and  user='$user' order by id desc";
$query=mysql_query($sql);
$out=mysql_fetch_assoc($query);
echo $out['je1']?abs(round($out['je1'],3)):0;
mysql_close($db);



						?>元</b></li>
            <li style="padding: 3% 0">
                <b><input type="text" class="warp_h_ex" id="money" name="money"  style="margin: 0">
<input type="hidden" name="pay1" value="<?php session_start(); $_SESSION['pay1'] = md5(uniqid('jygj123456',true)); echo $_SESSION['pay1'];?>">	
<input type="hidden" name="openids" value="<?=$openids;?>">	
                </b>
                <span>金额</span>
            </li>
        </ul>
    </div>
	
	<div style="overflow: hidden; padding: 6% 3% 1% 3%;"></div>


    <input type="button" value="确认充值" onClick="sousuo();" class="warp_h_ton" style="display: block;margin: 5% auto;width: 94%">

    <span class="warp_q"></span>
	</form>
	<!-- <strong><span class="STYLE1">（注意：支付成功后，请耐心等几秒，系统会自动
	跳转返回此界面，视为支付成功！如下图所示：）<br>
  </span></strong> -->
	
</div>
<script>
function sousuo(){
	var num=$("#money").val();
	if(num<0){
		alert('金额格式不正确');return false;
	
	}
	if(num==0){
		alert('金额不能为空');return false;
	
	}
	$("#form").submit();
	
}
		
</script>

<div class="footer">
<?php include("../wap/mall/footer.html");?>
</div>

</body>
</html>