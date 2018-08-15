<?php

/**
 * ECSHOP 角色管理信息以及权限管理程序
 * ============================================================================
 * 版权所有 2005-2011 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: wangleisvn $
 * $Id: privilege.php 16529 2009-08-12 05:38:57Z wangleisvn $
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
/* 权限检查 */

/* act操作项的初始化 */
if (empty($_REQUEST['act'])) {
    $_REQUEST['act'] = 'login';
} else {
    $_REQUEST['act'] = trim($_REQUEST['act']);
}

/* 初始化 $exc 对象 */
$exc = new exchange($ecs->table("users_role"), $db, 'id', 'name');

/* ------------------------------------------------------ */
//-- 退出登录
/* ------------------------------------------------------ */
if ($_REQUEST['act'] == 'logout') {
    /* 清除cookie */
    setcookie('ECSCP[admin_id]', '', 1);
    setcookie('ECSCP[admin_pass]', '', 1);

    $sess->destroy_session();

    $_REQUEST['act'] = 'login';
}

/* ------------------------------------------------------ */
//-- 登陆界面
/* ------------------------------------------------------ */
if ($_REQUEST['act'] == 'login') {
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");

    if ((intval($_CFG['captcha']) & CAPTCHA_ADMIN) && gd_version() > 0) {
        $smarty->assign('gd_version', gd_version());
        $smarty->assign('random', mt_rand());
    }

    $smarty->display('login.htm');
}


/* ------------------------------------------------------ */
//-- 角色列表页面
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'list') {
admin_priv('users_role_list');
    /* 模板赋值 */
    $smarty->assign('ur_here', "用户权限列表");
    $smarty->assign('full_page', 1);
    $smarty->assign('admin_list', get_role_list());

    /* 显示页面 */
    assign_query_info();
    $smarty->display('users_role_list.htm');
}

/* ------------------------------------------------------ */
//-- 查询
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'query') {
    $smarty->assign('admin_list', get_role_list());

    make_json_result($smarty->fetch('users_role_list.htm'));
}

/* ------------------------------------------------------ */
//-- 编辑角色信息
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'edit') {
     admin_priv('users_role');

    include_once(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/admin/priv_action.php');
    $_REQUEST['id'] = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    /* 模板赋值 */
    /* 获取角色信息 */
    $sql = "SELECT id, name, action FROM " . $ecs->table('users_role') .
            " WHERE id = '" . $_REQUEST['id'] . "'";
    $user_info = $db->getRow($sql);
    /* 获取权限的分组数据 */
    $sql_query = "SELECT action_id, parent_id, action_code,relevance FROM " . $ecs->table('supplier_admin_action') .
            " WHERE parent_id = 0";
    $res = $db->query($sql_query);
    while ($rows = $db->FetchRow($res)) {
        $priv_arr[$rows['action_id']] = $rows;
    }

    /* 按权限组查询底级的权限名称 */
    $sql = "SELECT action_id, parent_id, action_code,relevance FROM " . $ecs->table('supplier_admin_action') .
            " WHERE parent_id " . db_create_in(array_keys($priv_arr));
    $result = $db->query($sql);
    while ($priv = $db->FetchRow($result)) {
        $priv_arr[$priv["parent_id"]]["priv"][$priv["action_code"]] = $priv;
    }

    // 将同一组的权限使用 "," 连接起来，供JS全选
    foreach ($priv_arr AS $action_id => $action_group) {
        $priv_arr[$action_id]['priv_list'] = join(',', @array_keys($action_group['priv']));

        foreach ($action_group['priv'] AS $key => $val) {
            $priv_arr[$action_id]['priv'][$key]['cando'] = (strpos($user_info['action'], $val['action_code']) !== false || $user_info['action'] == 'all') ? 1 : 0;
        }
    }
    
    $smarty->assign('user', $user_info);
    $smarty->assign('form_act', 'update');
    $smarty->assign('action', 'edit');
    $smarty->assign('ur_here', $_LANG['admin_edit_role']);
    $smarty->assign('action_link', array('href' => 'role.php?act=list', 'text' => $_LANG['admin_list_role']));
    $smarty->assign('lang', $_LANG);
    $smarty->assign('priv_arr', $priv_arr);
    $smarty->assign('user_id', $_GET['id']);

    assign_query_info();
    $smarty->display('users_role_info.htm');
}

/* ------------------------------------------------------ */
//-- 更新角色信息
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'update') {
admin_priv('users_role');
/* 更新管理员的权限 */
    $act_list = @join(",", $_POST['action_code']);
    $sql = "UPDATE " . $ecs->table('users_role') . " SET action = '$act_list' " .
            "WHERE id = '$_POST[id]'";
    $db->query($sql);
    $user_sql = "UPDATE " . $ecs->table('supplier_admin_user') . " SET action_list = '$act_list' " .
            "WHERE role = '$_POST[id]'";
    $db->query($user_sql);
    /* 提示信息 */
    $link[] = array('text' => $_LANG['back_admin_list'], 'href' => 'users_role.php?act=list');
    sys_msg($_LANG['edit'] . "&nbsp;" . $_POST['name'] . "&nbsp;" . $_LANG['action_succeed'], 0, $link);
}

/* 获取角色列表 */

function get_role_list() {
    $list = array();
    $sql = 'SELECT id, name, action ' .
            'FROM ' . $GLOBALS['ecs']->table('users_role') . ' ORDER BY id DESC';
    $list = $GLOBALS['db']->getAll($sql);
    return $list;
}

?>
