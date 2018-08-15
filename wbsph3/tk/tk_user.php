<?php
define ( 'IN_ECS', true );
include('../includes/cls_json.php');
$json = new JSON();
 

$abc = array();
$abc[0]['partnerName']="partnerName";//"合作伙伴用户名,平安提供"
$abc[0]['departmentCode']="departmentCode";//"机构编码,平安提供"
$abc[0]['applicantInfo']="applicantInfo";//投保人信息-必填
$abc[0]['name']="name";//"name【必填】":"名称",
$abc[0]['birthday']="partnerName";//"birthday【非身份证时必填】":"出生年月 Date",
$abc[0]['sexCode']="partnerName";//"sexCode【非身份证时必填】":"性别F,M ",
$abc[0]['certificateNo']="partnerName";//"certificateNo【必填】":"证件号码",
$abc[0]['certificateType']="partnerName";//"certificateType【必填】":"证件类型,01:身份证、02：护照、03：军人证、04：港澳通行证，05：驾驶证、06：港澳回乡证或台胞证，07：临时身份证、99：其他",
$abc[0]['address']="partnerName";//"address【选填】":"地址",
$abc[0]['postcode']="partnerName";//"postcode【选填】":"邮政编码",
$abc[0]['email']="partnerName";//"email【选填】":"E-mail",
$abc[0]['homeTelephone']="partnerName";//"homeTelephone【选填-和手机号码选一】":"家庭电话",
$abc[0]['mobileTelephone']="partnerName";//"mobileTelephone【选填，和家庭电话选一】":"手机号码",
$abc[0]['personnelType']="partnerName";//"personnelType【选填 只支持个人默认是1】":"个团标志[1个人，0团体] "

$abc['productInfoList']['baseInfo']['transactionNo']="partnerName";//"transactionNo【必填,长度不要超过50】":"交易号(合作伙伴订单号，用于幂等性控制) String",
$abc['productInfoList']['baseInfo']['totalActualPremium']="partnerName";//"totalActualPremium【必填】":"实交保费 Double",
$abc['productInfoList']['baseInfo']['productCode']="partnerName";//"productCode【必填：平安业务提供】":"产品编码,通过某一险种定义出来的产品的编码 String",
$abc['productInfoList']['baseInfo']['insuranceBeginDate']="partnerName";//"insuranceBeginDate【必填】":"保险起期 Date yyyy-MM-dd 24h:mm:ss",
$abc['productInfoList']['baseInfo']['insuranceEndDate']="partnerName";//"insuranceEndDate【必填】":"保险止期 Date yyyy-MM-dd 24h:mm:ss",
$abc['productInfoList']['baseInfo']['coinsuranceMark']="partnerName";//"coinsuranceMark【选填】":"共保标志 0：非共保 1：共保 String",
$abc['productInfoList']['baseInfo']['businessType']="partnerName";//"businessType【选填，默认个人】":"业务类型/个团属性：1-个人，2-团体 String"
$abc['productInfoList']['extendInfo']['isSendInvoice']="partnerName";//"isSendInvoice【必填】":"是否发送电子发票   0不发送 1发送短信 2发送邮件 3短信邮件都发送 String",
$abc['productInfoList']['extendInfo']['invokeMobilePhone']="partnerName";//"invokeMobilePhone【选填】":"发送电子发票短信的手机号码 String",
$abc['productInfoList']['extendInfo']['invokeEmail']="partnerName";//	"invokeEmail【选填】":"发送电子发票邮件的邮箱 String",
$abc['productInfoList']['extendInfo']['taxPayerNO']="partnerName";//"taxPayerNO【选填】":"发票纳税人编号String"
$abc['productInfoList']['productInfoList']['insurantInfoList']['name']="partnerName";//"name【必填】":"名称 ",
$abc['productInfoList']['productInfoList']['insurantInfoList']['birthday']="partnerName";//"birthday【必填】":"出生年月 Date yyyy-MM-dd",
$abc['productInfoList']['productInfoList']['insurantInfoList']['age']="partnerName";//	"age":"年龄 Short",
$abc['productInfoList']['productInfoList']['insurantInfoList']['sexCode']="partnerName";//"sexCode【必填】":"性别 String",
$abc['productInfoList']['productInfoList']['insurantInfoList']['certificateNo']="partnerName";//"certificateNo【必填】":"证件号码 String",
$abc['productInfoList']['productInfoList']['insurantInfoList']['certificateType']="partnerName";//"certificateType【必填】":" 证件类型,01:身份证、02：护照、03：军人证、04：港澳通行证，05：驾驶证、06：港澳回乡证或台胞证，07：临时身份证、99：其他",
$abc['productInfoList']['productInfoList']['insurantInfoList']['address']="partnerName";//"address【选填】":"地址 String",
$abc['productInfoList']['productInfoList']['insurantInfoList']['postcode']="partnerName";//"postcode【选填】":"邮政编码 String",
$abc['productInfoList']['productInfoList']['insurantInfoList']['email']="partnerName";//	"email【选填】":"E-mail String",
$abc['productInfoList']['productInfoList']['insurantInfoList']['homeTelephone']="partnerName";//	"homeTelephone【选填,家庭电话和手机号码选一】":"家庭电话 String",
$abc['productInfoList']['productInfoList']['insurantInfoList']['mobileTelephone']="partnerName";//	"mobileTelephone【选填,家庭电话和手机号码选一】":"手机号码 String",
$abc['productInfoList']['productInfoList']['insurantInfoList']['personnelType']="partnerName";//	"personnelType【选填】":"个团标志[1个人，0团体] String"
$abc['productInfoList']['productInfoList']['riskGroupInfoList']['riskPropertyInfoList']['riskPropertyMap']['标的信息字段1']="partnerName";//必填
$abc['productInfoList']['productInfoList']['riskGroupInfoList']['riskPropertyInfoList']['riskPropertyMap']['标的信息字段2']="partnerName";//必填
$abc['productInfoList']['productInfoList']['riskGroupInfoList']['riskPropertyInfoList']['riskPropertyMap']['标的信息字段3']="partnerName";//必填
$abc['productInfoList']['productInfoList']['planInfoList']['dutyInfoList']['dutyNoclaimInfoList']['noclaimRate']="partnerName";//	"noclaimRate【必填】":"免赔率 Double",
$abc['productInfoList']['productInfoList']['planInfoList']['dutyInfoList']['dutyNoclaimInfoList']['noclaimAmount']="partnerName";//"noclaimAmount【必填】":"免赔额 Double",
$abc['productInfoList']['productInfoList']['planInfoList']['dutyInfoList']['dutyNoclaimInfoList']['dutyCode']="partnerName";//"dutyCode【必填】":"责任编码"
$abc['productInfoList']['productInfoList']['planInfoList']['dutyInfoList']['dutyAnnualRateInfoList']['insureYear']="partnerName";//"insureYear【必填】":"保险年度：1、2、3… Double",
$abc['productInfoList']['productInfoList']['planInfoList']['dutyInfoList']['dutyAnnualRateInfoList']['rate']="partnerName";//"rate【必填】":"费率 Double"
$abc['productInfoList']['productInfoList']['planInfoList']['dutyInfoList']['dutyAnnualRateInfoList']['rate']="partnerName";//
$abc['productInfoList']['productInfoList']['planInfoList']['dutyInfoList']['dutyCode']="partnerName";//"dutyCode【必填】":"责任编码",
$abc['productInfoList']['productInfoList']['planInfoList']['dutyInfoList']['dutyName']="partnerName";//"dutyName【必填】":"责任名称",
$abc['productInfoList']['productInfoList']['planInfoList']['dutyInfoList']['insuredAmount']="partnerName";//"insuredAmount【必填】":"总保额 Double",
$abc['productInfoList']['productInfoList']['planInfoList']['dutyInfoList']['totalActualPremium']="partnerName";//"totalActualPremium【必填】":"实交保费合计 指经过有关人员复核确认在本次销售行为中，客户应该交付的保费合计 Double"
$abc['productInfoList']['productInfoList']['planInfoList']['planCode']="partnerName";//"planCode【必填】":"险种代码",
$abc['productInfoList']['productInfoList']['planInfoList']['planName']="partnerName";//"planName【必填】":"险种名称",
$abc['productInfoList']['productInfoList']['applyNum']='1';//"applyNum【必填，默认是1】":"投保份数 Double",
$abc['productInfoList']['productInfoList']['productPackageType']='1';//"productPackageType【必填：非标产品可选填】":"产品套餐编码",
$abc['productInfoList']['productInfoList']['combinedProductCode']='1';//"combinedProductCode【选填，产品组合[MP02000057,MP02000072]必填，由平安提供】】":"组合产产品中被关联的产品编码"
		
 

$data = http_build_query($abc);
$data = json_encode($data);
var_dump($data);
$opts = array (
		'http' => array (
				'method' => 'POST',
				'header'=> 'Content-type: application/x-www-form-urlencoded; charset=UTF-8\r\n' .
				'Content-Length: ' . strlen($data) . '\r\n',
				'content' => ''
		)
);
$context = stream_context_create($opts);
$html = file_get_contents('http://test-api.pingan.com.cn/open/appsvr/property/standardApplyPolicyRiskPropertyNoPay', false, $context);
die($html);
/**
 * 销售, 购买记录
 * ============================================================================
 */


require (dirname ( __FILE__ ) . '/includes/init.php');
include_once (ROOT_PATH . '/includes/cls_image.php');
$image = new cls_image ( $_CFG ['bgcolor'] );
$exc = new exchange ( $ecs->table ( 'goods' ), $db, 'goods_id', 'goods_name' );


$action = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : '';
 $function_name = 'action_' . $action;
 

if (!function_exists($function_name)) {
	$function_name = "action_default";
}


call_user_func($function_name);
 
function action_zi_liao(){
	
	global $smarty;
	global $db ,$ecs,$_CFG;
	$goods_id = $_REQUEST ['goods_id'];
	if($_SESSION['member_role']==7){
		ecs_header ( "Location: index.php?act=main\n" );
	}
	
	// 		var_dump($goods);
 
	$smarty->display("tk_sheng_ji.html");
}

/**
 * 支付成功后, 对用户进行升级
 */
function action_sheng_ji(){
	
	global $smarty;
	global $db ,$ecs,$_CFG;
	$goods_id = $_REQUEST ['goods_id'];
	var_dump($_SESSION);
	
	$sql = "select user_name , mobile_phone, parent_id from ".$ecs->table("users")." where user_id =".$_SESSION['member_uid'];
	$user = $db->getRow($sql);
	$sql = "select user_name , mobile_phone  from ".$ecs->table("users")." where user_id =".$user['parent_id'];
	$parent = $db->getRow($sql);
	//    $_SESSION['member_user_id'];
	//给12套产品, 推荐人是根据前台账号查询的
	$sql="insert into ".$ecs->table("tk_xiao_shou")." (user_id,buy_time, buy_num, buy_times,parent_id,state,user_mobile
,user_name, parent_name, parent_mobile,goods_id, goods_name, shou_yi1) values(".$_SESSION['member_user_id'].",
".gmtime().",12,1,".$user['parent_id'].",'已付款',".$user['mobile_phone'].",".$user['user_name'].",".$parent['user_name'].",".$parent['mobile_phone']."
,1,'产品',3000) ";
	$db->query($sql);
	
	$sql="update  " .$ecs->table("supplier_admin_user")." set state =1  where user_id=".$_SESSION['member_user_id'];
	$db->query($sql);
	// 		var_dump($goods);
	$smarty->display("tk_sheng_ji.html");
	
}


  


/**
 * 获取用户后台id
 * @return unknown
 */
function  getUserCenterId(){
	return  $_SESSION['member_user_id'];
}

/**
 * 获取用户前台id
 * @return unknown
 */
function  getUserId(){
	return  $_SESSION['member_uid'];
}
     　

?>