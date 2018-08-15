<?php

/**
 * ECSHOP 管理中心办事处管理
 * ============================================================================
 * 版权所有 2005-2011 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: agency.php 17217 2011-01-19 06:29:08Z liubo $
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

$exc = new exchange($ecs->table('large_area'), $db, 'id', 'name');

/* ------------------------------------------------------ */
//-- 办事处列表
/* ------------------------------------------------------ */
if ($_REQUEST['act'] == 'list') {
	admin_priv('5_large_area_manage_list');
    $smarty->assign('ur_here', "大区列表");
    $smarty->assign('action_link', array('text' => "新增大区", 'href' => 'large_area.php?act=add'));
    $smarty->assign('full_page', 1);

    $agency_list = get_large_area_list();
    $smarty->assign('agency_list', $agency_list['agency']);
    $smarty->assign('filter', $agency_list['filter']);
    $smarty->assign('record_count', $agency_list['record_count']);
    $smarty->assign('page_count', $agency_list['page_count']);

    /* 排序标记 */
    $sort_flag = sort_flag($agency_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('large_area_list.htm');
}

/* ------------------------------------------------------ */
//-- 排序、分页、查询
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'query') {
	check_authz_json('5_large_area_manage_list');
    $agency_list = get_large_area_list();
    $smarty->assign('agency_list', $agency_list['agency']);
    $smarty->assign('filter', $agency_list['filter']);
    $smarty->assign('record_count', $agency_list['record_count']);
    $smarty->assign('page_count', $agency_list['page_count']);

    /* 排序标记 */
    $sort_flag = sort_flag($agency_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('large_area_list.htm'), '', array('filter' => $agency_list['filter'], 'page_count' => $agency_list['page_count']));
}

/* ------------------------------------------------------ */
//-- 删除办事处
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'remove') {
    check_authz_json('5_large_area_manage');

    $id = intval($_GET['id']);
    $name = $exc->get_name($id);
    $exc->drop($id);


    $sql = "DELETE FROM  " . $GLOBALS['ecs']->table("supplier_admin_user") . " where role='1' and large_area_id = " . $id;
    $db->query($sql);

    /* 更新管理员、配送地区、发货单、退货单和订单关联的办事处 */
//    $table_array = array('admin_user', 'region', 'order_info', 'delivery_order', 'back_order');
//    foreach ($table_array as $value) {
//        $sql = "UPDATE " . $ecs->table($value) . " SET agency_id = 0 WHERE agency_id = '$id'";
//        $db->query($sql);
//    }

    /* 记日志 */
    admin_log($name, 'remove', 'agency');

    /* 清除缓存 */
    clear_cache_files();

    $url = 'large_area.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/* ------------------------------------------------------ */
//-- 批量操作
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'batch') {
    /* 取得要操作的记录编号 */
    if (empty($_POST['checkboxes'])) {
        sys_msg($_LANG['no_record_selected']);
    } else {
        /* 检查权限 */
        admin_priv('5_large_area_manage');

        $ids = $_POST['checkboxes'];

        if (isset($_POST['remove'])) {


            $sql = "DELETE FROM  " . $GLOBALS['ecs']->table("supplier_admin_user") . " where role='1' and large_area_id " . db_create_in($ids);
            $db->query($sql);
            /* 删除记录 */
            $sql = "DELETE FROM " . $ecs->table('large_area') .
                    " WHERE id " . db_create_in($ids);
            $db->query($sql);

//            /* 更新管理员、配送地区、发货单、退货单和订单关联的办事处 */
//            $table_array = array('admin_user', 'region', 'order_info', 'delivery_order', 'back_order');
//            foreach ($table_array as $value) {
//                $sql = "UPDATE " . $ecs->table($value) . " SET agency_id = 0 WHERE agency_id " . db_create_in($ids) . " ";
//                $db->query($sql);
//            }

            /* 记日志 */
            admin_log('', 'batch_remove', 'large_area');

            /* 清除缓存 */
            clear_cache_files();

            sys_msg("操作成功");
        }
    }
}

/* ------------------------------------------------------ */
//-- 添加、编辑办事处
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'add' || $_REQUEST['act'] == 'edit') {
    /* 检查权限 */
admin_priv('5_large_area_manage');

    /* 是否添加 */
    $is_add = $_REQUEST['act'] == 'add';
    $smarty->assign('form_action', $is_add ? 'insert' : 'update');





    /* 初始化、取得办事处信息 */
    if ($is_add) {


        $agency = array(
            'id' => 0,
            'name' => '',
            'user_name' => ''
        );
    } else {
        if (empty($_GET['id'])) {
            sys_msg('invalid param');
        }

        $id = $_GET['id'];
        $sql = "SELECT * FROM " . $GLOBALS['ecs']->table("large_area") . " WHERE id = '$id'";
        $agency = $db->getRow($sql);
        if (empty($agency)) {
            sys_msg('agency does not exist');
        }

        $sql = "SELECT user_name from " . $GLOBALS['ecs']->table("users") . " where user_id='" . $agency['user_id'] . "'";
        $agency['user_name'] = $db->getOne($sql);
    }

    $sql = "SELECT region_id,region_name,large_area_id from " . $GLOBALS['ecs']->table("region") . " where region_type=1";
    $provices = $GLOBALS['db']->getAll($sql);

    $smarty->assign('provices', $provices);
    $smarty->assign('agency', $agency);
    /* 显示模板 */
    if ($is_add) {
        $smarty->assign('ur_here', $_LANG['add_agency']);
    } else {
        $smarty->assign('ur_here', $_LANG['edit_agency']);
    }
    if ($is_add) {
        $href = 'large_area.php?act=list';
    } else {
        $href = 'large_area.php?act=list&' . list_link_postfix();
    }
    $smarty->assign('action_link', array('href' => $href, 'text' => "大区管理"));
    assign_query_info();
    $smarty->display('large_area_info.htm');
}

/* ------------------------------------------------------ */
//-- 提交添加、编辑办事处
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'insert' || $_REQUEST['act'] == 'update') {
    /* 检查权限 */
    admin_priv('5_large_area_manage');

    /* 是否添加 */
    $is_add = $_REQUEST['act'] == 'insert';

    /* 提交值 */
    $agency = array(
        'id' => intval($_POST['id']),
        'name' => sub_str($_POST['name'], 255, false),
        'user_name' => $_POST['user_name']
    );

    $sql = "SELECT user_id,user_name,mobile_phone,password,ec_salt,email FROM " . $GLOBALS['ecs']->table("users") . " where mobile_phone='" . $agency['user_name'] . "'";
    $uid = $GLOBALS['db']->getRow($sql);
    if (empty($uid)) {
        sys_msg("会员不存在");
    }
    if (check_user_is_blacklist($uid['user_id'])) {
        sys_msg("会员已经被锁定");
    }

    //判断会员有其他身份
    //限制supplier_admin_user只能一个帐号
    $sql = "SELECT user_id,uid FROM " . $GLOBALS['ecs']->table("supplier_admin_user") . " where uid = '".$uid['user_id']."'";
    $admin_user_id = $GLOBALS['db']->getRow($sql);
    if ($admin_user_id)
    {
        sys_msg('当前人员已经是营运中心人员或商城入驻商家，请切换其他人员', 0);
        exit();
    }
    
    /* 判断名称是否重复 */
    if (!$exc->is_only('name', $agency['name'], $agency['id'])) {
        sys_msg("大区名称已经存在");
    }

    $agency["user_id"] = $uid["user_id"];
    /* 保存办事处信息 */
    if ($is_add) {
        $db->autoExecute($ecs->table('large_area'), $agency, 'INSERT');
        $agency['id'] = $db->insert_id();
    } else {
        $db->autoExecute($ecs->table('large_area'), $agency, 'UPDATE', "id = '" . $agency["id"] . "'");
    }

    $region_id = $_POST['province'];
    $sql = "update " . $GLOBALS['ecs']->table("region") . " set large_area_id=" . $agency['id'] . " where region_id in (" . implode(",", $region_id) . ")";
    $GLOBALS['db']->query($sql);

    //判断当前大区是否存在，存在则update，否则插入
    $sql = "SELECT user_id,uid FROM " . $GLOBALS['ecs']->table("supplier_admin_user") . " where role='1' and large_area_id='" . $agency['id'] . "'";
    $admin_user_id = $GLOBALS['db']->getRow($sql);
    if (empty($admin_user_id)) {//插入
        $sql = "INSERT INTO " . $GLOBALS['ecs']->table("supplier_admin_user") . "(uid,user_name,email,password,ec_salt,add_time,mobile_phone,role,large_area_id,checked)" .
                " VALUES('" . $uid['user_id'] . "','" . $uid['user_name'] . "','" . $uid['email'] . "','" . $uid['password'] . "','" . $uid['ec_salt'] . "','" . time() . "','" . $uid['mobile_phone'] . "',1,'" . $agency['id'] . "',1)";
        log_account_change($uid['user_id'], 0, 0, 0, 0, "系统自动赠送大区奖励基数5000000", ACT_OTHER, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 5000000);
        $db->query($sql);
    } else {//修改
        if ($admin_user_id['uid'] * 1 == $agency['user_id']) {
            
        } else {
            $sql = "UPDATE " . $GLOBALS['ecs']->table("supplier_admin_user") . " SET `user_name`='" . $uid['user_name'] . "',  "
                    . " uid='" . $uid['user_name'] . "',"
                    . " email='" . $uid['email'] . "',"
                    . " password='" . $uid['password'] . "',"
                    . " ec_salt='" . $uid['ec_salt'] . "',"
                    . " mobile_phone='" . $uid['mobile_phone'] . "' "
                    . " WHERE user_id=" . $admin_user_id['user_id'];
            $db->query($sql);
        }
    }

    /* 记日志 */
    if ($is_add) {
        admin_log($agency['agency_name'], 'add', 'large_area');
    } else {
        admin_log($agency['agency_name'], 'edit', 'large_area');
    }

    /* 清除缓存 */
    clear_cache_files();

    /* 提示信息 */
    if ($is_add) {
        $links = array(
            array('href' => 'large_area.php?act=add', 'text' => "添加大区"),
            array('href' => 'large_area.php?act=list', 'text' => "大区列表")
        );
        sys_msg("添加大区成功", 0, $links);
    } else {
        $links = array(
            array('href' => 'large_area.php?act=list&' . list_link_postfix(), 'text' => "大区列表")
        );
        sys_msg("编辑大区成功", 0, $links);
    }
}

/**
 * 取得办事处列表
 * @return  array
 */
function get_large_area_list() {
    $result = get_filter();
    if ($result === false) {
        /* 初始化分页参数 */
        $filter = array();
        $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        /* 查询记录总数，计算分页数 */
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('large_area');
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
        $filter = page_and_size($filter);

        /* 查询记录 */
        $sql = "SELECT s.id,s.name,a.user_name FROM " . $GLOBALS['ecs']->table('large_area') . " s left join " . $GLOBALS['ecs']->table("users") . " a on s.user_id=a.user_id ORDER BY $filter[sort_by] $filter[sort_order]";

        set_filter($filter, $sql);
    } else {
        $sql = $result['sql'];
        $filter = $result['filter'];
    }
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);

    $arr = array();
    while ($rows = $GLOBALS['db']->fetchRow($res)) {
        $arr[] = $rows;
    }

    return array('agency' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

?>