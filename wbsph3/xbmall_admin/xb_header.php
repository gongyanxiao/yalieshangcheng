<?php
/**
 *  头部信息
 */

 
define('IN_ECS', true);
require (dirname(__FILE__) . '/includes/init.php');

$action = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : 'default';
$function_name = 'action_' . $action;
if (! function_exists($function_name)) {
    $function_name = "action_default";
}

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
    $sql = "SELECT COUNT(*) FROM " .$fromSql;
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);
    /* 分页大小 */
    $filter = page_and_size($filter);
}


function getDataSql(&$filter, $fromSql, $dataFiled, $sort_by_name,$sort_order='DESC'){
    $filter['sort_by'] = empty($_REQUEST['sort_by']) ? $sort_by_name : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? $sort_order : trim($_REQUEST['sort_order']);
    /* 查询 */
    $sql = "SELECT {$dataFiled} " . "FROM " .$fromSql. " ORDER BY $filter[sort_by] $filter[sort_order] " . " LIMIT " . $filter['start'] . ", $filter[page_size]";
    return  $sql;
}

?>
