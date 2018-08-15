<?php

/**
 *  地区列表管理文件
 * ============================================================================
 * $Id: xb_area_manage_rate.php   $
 */
define('IN_ECS', true);

require (dirname(__FILE__) . '/includes/init.php');
$exc = new exchange($ecs->table('region'), $db, 'region_id', 'region_name');

$action = ! empty($_REQUEST['act']) ? trim($_REQUEST['act']) : '';
$function_name = 'action_' . $action;
if (! function_exists($function_name)) {
    $function_name = "action_list";
}
call_user_func($function_name);

/* ------------------------------------------------------ */
// -- 列出某地区下的所有地区列表
/* ------------------------------------------------------ */
function action_list()
{
    global $exc;
    admin_priv('area_manage_list');
    
    /* 取得参数：上级地区id */
    $region_id = empty($_REQUEST['pid']) ? 0 : intval($_REQUEST['pid']);
    assign('parent_id', $region_id);
    
    /* 取得列表显示的地区的类型 */
  
    
    /* 获取地区列表 */
    $region_arr = area_list($region_id);
    assign('region_arr', $region_arr);
    
    /* 当前的地区名称 */
    if ($region_id > 0) {
        $area_name = $exc->get_name($region_id);
        $area = '[ ' . $area_name . ' ] ';
        if ($region_arr) {
            $area .= $region_arr[0]['type'];
        }
    } else {
        $area = $_LANG['country'];
    }
    assign('area_here', $area);
    
    /* 返回上一级的链接 */
    if ($region_id > 0) {
        $parent_id = $exc->get_name($region_id, 'parent_id');
        $action_link = array(
            'text' => $_LANG['back_page'],
            'href' => 'xb_area_manage_rate.php?act=list&&pid=' . $parent_id
        );
    } else {
        $action_link = '';
    }
    assign('action_link', $action_link);
    /* 赋值模板显示 */
    assign('ur_here', $_LANG['05_xb_area_list']);
    assign('full_page', 1);
    
    assign_query_info();
    display('xb_area_rate_list.htm');
}


function area_rate_list($region_id)
{
    $area_arr = array();
    
    $sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('region').
    " WHERE parent_id = '$region_id' ORDER BY region_id";
    $res = $GLOBALS['db']->query($sql);
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $row['type']  = ($row['region_type'] == 0) ? $GLOBALS['_LANG']['country']  : '';
        $row['type'] .= ($row['region_type'] == 1) ? $GLOBALS['_LANG']['province'] : '';
        $row['type'] .= ($row['region_type'] == 2) ? $GLOBALS['_LANG']['city']     : '';
        $row['type'] .= ($row['region_type'] == 3) ? $GLOBALS['_LANG']['cantonal'] : '';
        
        $area_arr[] = $row;
    }
    
    return $area_arr;
}




function action_edit_area_rate(){
    global $exc;
    check_authz_json('area_manage');
    $id = intval($_POST['id']);
    $region_rate = json_str_iconv(trim($_POST['val']));
    
    $sql="select * from xbmall_region where region_id={$id}";
    $row = db_getRow($sql);
    
    
    if ($exc->edit("region_rate = {$region_rate}", $id)) {
        $sql="update xbmall_region set region_rate={$region_rate} where parent_id={$id}";
        db_query($sql);//设置所有的子区域的释放比例一样
        admin_log($region_rate, 'edit', 'area');
        make_json_result($region_rate);
    } else {
        make_json_error($db->error());
    }
    
}


function action_edit_area_qd_rate(){
    global $exc;
    check_authz_json('area_manage');
    $id = intval($_POST['id']);
    $region_qd_rate = json_str_iconv(trim($_POST['val']));
    
    $sql="select * from xbmall_region where region_id={$id}";
    $row = db_getRow($sql);
    
    
    if ($exc->edit("region_qd_rate = {$region_qd_rate}", $id)) {
        $sql="update xbmall_region set region_qd_rate={$region_qd_rate} where parent_id={$id}";
        db_query($sql);//设置所有的子区域的释放比例一样
        admin_log($region_qd_rate, 'edit', 'area');
        make_json_result($region_qd_rate);
    } else {
        make_json_error($db->error());
    }
    
}




function action_get_child_area(){
    $region_id = empty($_REQUEST['pid']) ? 1 : intval($_REQUEST['pid']);
    /* 获取地区列表 */
    $region_arr = area_list($region_id);
    make_json_result($region_arr);
}
/* ------------------------------------------------------ */
 

/* ------------------------------------------------------ */
// -- 编辑区域名称
/* ------------------------------------------------------ */
function action_edit_area_name()
{
    global $exc;
    
    check_authz_json('area_manage');
    
    $id = intval($_POST['id']);
    $region_name = json_str_iconv(trim($_POST['val']));
    
    if (empty($region_name)) {
        make_json_error($_LANG['region_name_empty']);
    }
    
    $msg = '';
    
    /* 查看区域是否重复 */
    $parent_id = $exc->get_name($id, 'parent_id');
    if (! $exc->is_only('region_name', $region_name, $id, "parent_id = '$parent_id'")) {
        make_json_error($_LANG['region_name_exist']);
    }
    
    if ($exc->edit("region_name = '$region_name'", $id)) {
        admin_log($region_name, 'edit', 'area');
        make_json_result(stripslashes($region_name));
    } else {
        make_json_error($db->error());
    }
}
 
 
?>