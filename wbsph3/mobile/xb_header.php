<?php
/**
 *  头部信息
 */

define ( 'IN_ECS', true );
session_start ();
require (dirname ( __FILE__ ) . '/includes/init.php');
include_once (ROOT_PATH . 'includes/lib_clips.php');
/* 载入语言文件 */
require_once (ROOT_PATH . 'languages/' . $_CFG ['lang'] . '/user.php');
/* end */
$user_id = isset ( $_SESSION ['user_id'] ) ? $_SESSION ['user_id'] : 0;
$action = getRequestStrTrim('act','default');


$affiliate = unserialize ( $GLOBALS ['_CFG'] ['affiliate'] );
$smarty->assign ( 'affiliate', $affiliate );

$back_act = '';
if (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'MicroMessenger' )) {
    $smarty->assign ( 'iswei', 1 ); // 判断是否为微信
}

/* 未登录处理 */
// if (empty ( $_SESSION ['user_id'] )) {
//     ecs_header ( "Location: user.php?act=login\n" );
    
// }

assign_template ();
$position = assign_ur_here ( 0, $_LANG ['user_center'] );
$smarty->assign ( 'page_title', $position ['title'] ); // 页面标题
$smarty->assign ( 'ur_here', $position ['ur_here'] );
$sql = "SELECT value FROM " . $ecs->table ( 'ecsmart_shop_config' ) . " WHERE id = 419";
$row = $db->getRow ( $sql );
$car_off = $row ['value'];
$smarty->assign ( 'car_off', $car_off );
/* 是否显示积分兑换 */
if (! empty ( $_CFG ['points_rule'] ) && unserialize ( $_CFG ['points_rule'] )) {
    $smarty->assign ( 'show_transform_points', 1 );
}
$smarty->assign ( 'helps', get_shop_help () ); // 网店帮助
$smarty->assign ( 'data_dir', DATA_DIR ); // 数据目录
$smarty->assign ( 'action', $action );
$smarty->assign ( 'lang', $_LANG );
$smarty->assign ( 'is_distrib', $is_distrib );

$function_name = 'action_' . $action;

if (! function_exists ( $function_name )) {
    $function_name = "action_default";
}
date_default_timezone_set("Asia/Shanghai");
/**
 * 设置区域查询条件
 * @param unknown $filter
 * @param unknown $where
 */
function  quYuTiaoJian(&$filter,&$where){
    // 地区设置
    $filter['province'] = getRequestInt('province');
    $filter['city'] = getRequestInt('city');
    $filter['district'] = getRequestInt('district');
    // 地区设置 检索
    if ($filter['province'] > 0) {
        $where .= " AND province = '$filter[province]' ";
    }
    if ($filter['city'] > 0) {
        $where .= " AND city = '$filter[city]' ";
    }
    if ($filter['district'] > 0) {
        $where .= " AND district = '$filter[district]' ";
    }
}
/**
 * 设置int型的查询条件
 * @param unknown $where
 * @param unknown $paramName
 */
function  setIntCondition(&$filter, &$where, $paramName){
    if (! empty($_REQUEST['type'])) {
        $filter[$paramName] = $_REQUEST[$paramName];
        $where .= " {$paramName}=" . $filter[$paramName];
    }
}


/**
 * 设置分页信息
 * @param unknown $filter
 * @param unknown $fromSql
 */
function  setPageInfo(&$filter, $fromSql){
    global  $smarty  ;
    $sql = "SELECT COUNT(*) FROM " .$fromSql;
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);
    /* 分页大小 */
    $filter = page_and_size($filter);
    include "../jygj/wap/mall/page.class.php" ;
    $page_obj=new Page($filter['record_count'],12);
    $pagelist=$page_obj->fpage();//分页信息
    $smarty->assign('pagelist', $pagelist);
    
}


function getDataSql(&$filter, $fromSql, $dataFiled, $sort_by_name,$sort_order='DESC'){
    $filter['sort_by'] = empty($_REQUEST['sort_by']) ? $sort_by_name : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? $sort_order : trim($_REQUEST['sort_order']);
    /* 查询 */
    $sql = "SELECT {$dataFiled} " . "FROM " .$fromSql. " ORDER BY $filter[sort_by] $filter[sort_order] " . " LIMIT " . $filter['start'] . ", $filter[page_size]";
    return  $sql;
}

?>
