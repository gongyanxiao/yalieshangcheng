<?php
//正式数据库
$db_host   = "127.0.0.1:3306";

// database name
$db_name   = "wbsph3";

// database username
$db_user   = "root";

// database password
$db_pass   = "root";

 //database host
//$db_host   = "192.168.200.120:3306";
//
//// database name
//$db_name   = "jinan";
//
//// database username
//$db_user   = "jinan";
//
//// database password
//$db_pass   = "8uhb7ygv";



//$db_host   = "localhost:3306";
//
//// database name
//$db_name   = "jiejiele";
//
//// database username
//$db_user   = "jiejiele_user";
//
//// database password
//$db_pass   = "Vbr7vaQjS";


// table prefix
$prefix    = "xbmall_";

$timezone    = "UTC";

$cookie_path    = "/";

$cookie_domain    = "";

$session = "1440";

define('EC_CHARSET','utf-8');

if(!defined('ADMIN_PATH'))
{
define('ADMIN_PATH','xbmall_admin');
}

define('AUTH_KEY', 'this is a key');

define('OLD_AUTH_KEY', '');

define('API_TIME', '2018-08-12 01:04:02');
define('DEBUG_MODE', 0); 
?>
