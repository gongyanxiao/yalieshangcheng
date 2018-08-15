<?php
header("Content-type:text/html;charset=utf-8");
if(empty($_COOKIE["zt_user1"])){

}
require_once 'AppConfig.php';
require_once 'AppUtil.php';
	function query(){
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
$s="select ddbh from zt_cz where  user='$user' and zt='8' order by id desc";
$q=mysql_query($s);
$s1=mysql_fetch_assoc($q);
$ddbh=$s1["ddbh"];
if(!empty($ddbh)){
		$params = array();
		$params["cusid"] = AppConfig::CUSID;
	    $params["appid"] = AppConfig::APPID;
	    $params["version"] = AppConfig::APIVERSION;
	    $params["reqsn"] =$ddbh;
	    $params["randomstr"] = "1450432107647";//
	    $params["sign"] = AppUtil::SignArray($params,AppConfig::APPKEY);//ǩ��
	    $paramsStr = AppUtil::ToUrlParams($params);
	    $url = AppConfig::APIURL . "/query";
	    $rsp = request($url, $paramsStr);
	    $rspArray = json_decode($rsp, true); 
		if(validSign($rspArray)){
	    $o=json_decode($rsp);
		$id=$o->{"trxstatus"};
		if($id=="0000"){
		$sql="update zt_cz set zt='1' where ddbh='$ddbh' and zt='8';";
mysql_query($sql);
mysql_close($db);
}
	    }
		}
	}
	query();
	
	//����������������ο�,��Ϊ���ʵ��
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
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);//���������֤,����false,�̻����д���
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		 
		$output = curl_exec($ch);
		curl_close($ch);
		return  $output;
	}
	//��ǩ
	function validSign($array){
		if("SUCCESS"==$array["retcode"]){
			$signRsp = strtolower($array["sign"]);
			$array["sign"] = "";
			$sign =  strtolower(AppUtil::SignArray($array, AppConfig::APPKEY));
			if($sign==$signRsp){
				return TRUE;
			}
			else {
				echo "��ǩʧ��:".$signRsp."--".$sign;
			}
		}
		
		return FALSE;
	}
	?>