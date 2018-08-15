<?php


$dirPath = str_replace('\\', '/', dirname(__FILE__));
$path = substr($dirPath, 0,strrpos($dirPath,"/"));//根路径
include $path."/config/zt_config.php";
$db = mysql_connect("$db_host","$db_user","$db_pwd");

mysql_query("set names $coding");
mysql_select_db("$db_database");

/**
 * 获取数据的条数
 * @param unknown $tableName 表名
 * @param string $where 条件
 * @return unknown
 */
function  getCount($tableName, $where="1=1"){
	$countSQL = "select count(0) as total from {$tableName}  where {$where}";
	return  getOne($countSQL);
}

function  getOne($sql){
// 	$countSQL = "select count(0) as total from {$tableName}  where {$where}";
	$result=mysql_query($sql);
	if(isset($result)){
		$total= mysql_fetch_row($result);
		$total = $total[0];
		return $total;
	}
	// 	$text = var_export($total,true);
	return null;
}


/**
 * 获取一行数据
 * @param unknown $sql
 * @return unknown|NULL
 */
function  getRow($sql){
	$row = mysql_query($sql);
	if($row){
		$row= mysql_fetch_assoc($row);
		return $row;
	}
	return null;
}





function startTrans() {
	mysql_query("SET AUTOCOMMIT=0"); 
	mysql_query("START TRANSACTION");
}

function commitTrans() {
	mysql_query("COMMIT");
}

function rollbackTrans() {
	mysql_query("ROLLBACK");
}


?>