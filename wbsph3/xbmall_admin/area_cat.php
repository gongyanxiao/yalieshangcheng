<?php

/**
 * UEUCS_364642382
 * $Id: area.php 17063 2010-03-25 06:35:46Z liuhui $
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
$exc = new exchange($ecs->table("area"), $db, 'area_id', 'area_name');

/* act操作项的初始化 */
if (empty($_REQUEST['act'])) {
    $_REQUEST['act'] = 'list';
} else {
    $_REQUEST['act'] = trim($_REQUEST['act']);
}

/* ------------------------------------------------------ */
//-- 列表
/* ------------------------------------------------------ */
if ($_REQUEST['act'] == 'list') {
	admin_priv('4_city_manage_list');
    $region_id = empty($_REQUEST['pid']) ? 0 : intval($_REQUEST['pid']);
    $smarty->assign('parent_id', $region_id);

    /* 获取分类列表 */
    $cat_list = area_cat_list(0, $region_id, false); 
    /* 模板赋值 */
    $smarty->assign('ur_here', '运营中心列表');
    //$smarty->assign('action_link', array('href' => 'area_cat.php?act=add', 'text' => '添加运营中心'));
    $smarty->assign('full_page', 1);

    $smarty->assign('cat_info', $cat_list);

    /* 列表页面 */
    assign_query_info();
    $smarty->display('area_cat_list.htm');
}

/* ------------------------------------------------------ */
//-- 排序、分页、查询
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'query') {
	check_authz_json('4_city_manage_list');
    $cat_list = area_cat_list(0, 0, false);
    $smarty->assign('cat_info', $cat_list);

    make_json_result($smarty->fetch('area_cat_list.htm'));
}
/* ------------------------------------------------------ */
//-- 添加商品分类
/* ------------------------------------------------------ */
if ($_REQUEST['act'] == 'add') {
    /* 权限检查 */
    admin_priv('4_city_manage');



    /* 模板赋值 */
    $smarty->assign('ur_here', '添加运营中心');
    $smarty->assign('action_link', array('href' => 'area_cat.php?act=list', 'text' => '运营中心列表'));


    $smarty->assign('cat_select', area_cat_list(0, 0, true));
    $smarty->assign('form_act', 'insert');
    $smarty->assign('cat_info', array('is_show' => 1));



    /* 显示页面 */
    assign_query_info();
    $smarty->display('area_cat_info.htm');
}

/* ------------------------------------------------------ */
//-- 商品分类添加时的处理
/* ------------------------------------------------------ */
if ($_REQUEST['act'] == 'insert') {
    /* 权限检查 */
    admin_priv('4_city_manage');

    /* 初始化变量 */
    $cat['cat_id'] = !empty($_POST['cat_id']) ? intval($_POST['cat_id']) : 0;
    $cat['parent_id'] = !empty($_POST['parent_id']) ? intval($_POST['parent_id']) : 1;
    $cat['sort_order'] = !empty($_POST['sort_order']) ? intval($_POST['sort_order']) : 0;
    $cat['cat_desc'] = !empty($_POST['cat_desc']) ? $_POST['cat_desc'] : '';

    $cat['city_title'] = !empty($_POST['city_title']) ? $_POST['city_title'] : '';
    $cat['city_keywords'] = !empty($_POST['city_keywords']) ? $_POST['city_keywords'] : '';
    $cat['city_desc'] = !empty($_POST['city_desc']) ? $_POST['city_desc'] : '';

    $cat['shortname'] = !empty($_POST['shortname']) ? $_POST['shortname'] : '';
    $cat['cat_name'] = !empty($_POST['cat_name']) ? trim($_POST['cat_name']) : '';
    $cat['is_show'] = !empty($_POST['is_show']) ? intval($_POST['is_show']) : 0;
    $cat['password'] = !empty($_POST['cat_admin_password']) ? md5($_POST['cat_admin_password']) : md5('admin123');

    if (cat_exists($cat['cat_name'], $cat['parent_id'])) {
        /* 同级别下不能有重复的分类名称 */
        $link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
        sys_msg($_LANG['catname_exist'], 0, $link);
    }
    /*     * ****POWERBY UEUCS_364642382***** */
//    $email = $cat['cat_name'] . 'admin@163.com';
//    $add_time = gmtime();
//    $action_list = "goods_manage,suppliers_manage,delivery_view,back_view";
//    $user_name = $cat['cat_name'] . '管理员';
//    $password = $cat['password'];
//
//    $sql1 = "SELECT nav_list FROM " . $ecs->table('admin_user') . " WHERE action_list = 'all'";
//    $row1 = $db->getRow($sql1);
//
//    $sql = "INSERT INTO " . $ecs->table('suppliers') . " (suppliers_name, is_check,is_alone) " .
//            "VALUES ('$user_name','1','1')";
//    $db->query($sql);
//    $suppliers_id = $db->getOne("SELECT suppliers_id FROM " . $ecs->table('suppliers') . " WHERE suppliers_name = '$user_name'");
//
//    $sql2 = "INSERT INTO " . $ecs->table('admin_user') . " (user_name,is_alone, email, password, add_time, nav_list, action_list,suppliers_id, role_id,is_check) " .
//            "VALUES ('$user_name', '1', '$email', '$password', '$add_time', '$row1[nav_list]', '$action_list',$suppliers_id, '0','1')";
//
//    $db->query($sql2);

    /* 入库的操作 */
    if ($db->autoExecute($ecs->table('region'), $cat) !== false) {
        $cat_id = $db->insert_id();

        admin_log($_POST['cat_name'], 'add', 'area_cat');   // 记录管理员操作
        clear_cache_files();    // 清除缓存

        /* 添加链接 */
        $link[0]['text'] = '继续添加运营中心';
        $link[0]['href'] = 'area_cat.php?act=add';

        $link[1]['text'] = '返回运营中心列表';
        $link[1]['href'] = 'area_cat.php?act=list';

        sys_msg('成功添加运营中心', 0, $link);
    }
}

/* ------------------------------------------------------ */
//-- 编辑商品分类信息
/* ------------------------------------------------------ */
if ($_REQUEST['act'] == 'edit') {
    admin_priv('4_city_manage');   // 权限检查
    $cat_id = intval($_REQUEST['cat_id']);
    $cat_info = get_cat_info($cat_id);  // 查询分类信息数据

    /* 模板赋值 */

    $smarty->assign('attr_cat_id', $attr_cat_id);
    $smarty->assign('ur_here', '编辑运营中心');
    $smarty->assign('action_link', array('text' => '运营中心列表', 'href' => 'area_cat.php?act=list'));


    $smarty->assign('cat_info', $cat_info);
    $smarty->assign('form_act', 'update');
    $smarty->assign('cat_select', area_cat_list(0, $cat_info['parent_id'], true, 0, true, 2));

    /* 显示页面 */
    assign_query_info();
    $smarty->display('area_cat_info.htm');
} elseif ($_REQUEST['act'] == 'add_category') {
    $parent_id = empty($_REQUEST['parent_id']) ? 0 : intval($_REQUEST['parent_id']);
    $category = empty($_REQUEST['cat']) ? '' : json_str_iconv(trim($_REQUEST['cat']));

    if (cat_exists($category, $parent_id)) {
        make_json_error($_LANG['catname_exist']);
    } else {
        $sql = "INSERT INTO " . $ecs->table('category') . "(cat_name, parent_id, is_show)" .
                "VALUES ( '$category', '$parent_id', 1)";

        $db->query($sql);
        $category_id = $db->insert_id();

        $arr = array("parent_id" => $parent_id, "id" => $category_id, "cat" => $category);

        clear_cache_files();    // 清除缓存

        make_json_result($arr);
    }
}

/* ------------------------------------------------------ */
//-- 编辑商品分类信息
/* ------------------------------------------------------ */
if ($_REQUEST['act'] == 'update') {
    /* 权限检查 */
    admin_priv('4_city_manage');

    /* 初始化变量 */
    $cat_id = !empty($_POST['cat_id']) ? intval($_POST['cat_id']) : 0;
    $old_cat_name = $_POST['old_cat_name'];
    $cat['parent_id'] = !empty($_POST['parent_id']) ? intval($_POST['parent_id']) : 0;
    $cat['sort_order'] = !empty($_POST['sort_order']) ? intval($_POST['sort_order']) : 0;
    $cat['cat_desc'] = !empty($_POST['cat_desc']) ? $_POST['cat_desc'] : '';

    $cat['city_title'] = !empty($_POST['city_title']) ? $_POST['city_title'] : '';
    $cat['city_keywords'] = !empty($_POST['city_keywords']) ? $_POST['city_keywords'] : '';
    $cat['city_desc'] = !empty($_POST['city_desc']) ? $_POST['city_desc'] : '';

    $cat['shortname'] = !empty($_POST['shortname']) ? $_POST['shortname'] : '';
    $cat['region_name'] = !empty($_POST['cat_name']) ? trim($_POST['cat_name']) : '';
    $cat['is_show'] = !empty($_POST['is_show']) ? intval($_POST['is_show']) : 0;
    $cat['is_hot'] = !empty($_POST['is_hot']) ? intval($_POST['is_hot']) : 0;
    $cat['password'] = !empty($_POST['cat_admin_password']) ? md5($_POST['cat_admin_password']) : md5('admin123');
    /* 判断分类名是否重复 */

    if ($cat['region_name'] != $old_cat_name) {
        if (cat_exists($cat['region_name'], $cat['parent_id'], $cat_id)) {
            $link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
            sys_msg($_LANG['catname_exist'], 0, $link);
        }
    }

    /* 判断上级目录是否合法 */
//    $children = array_keys(area_cat_list($cat_id, 0, false));     // 获得当前分类的所有下级分类
//    var_dump($children);exit;
//    if (in_array($cat['parent_id'], $children)) {
//        /* 选定的父类是当前分类或当前分类的下级分类 */
//        $link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
//        sys_msg($_LANG["is_leaf_error"], 0, $link);
//    }
//    $user_name = $cat['cat_name'] . '管理员';
//
//    $user_id_h = $db->getOne('SELECT user_id FROM ' . $ecs->table('admin_user') . " WHERE user_name ='$user_name'");
//    if ($user_id_h) {
//        $GLOBALS['db']->query("update " . $GLOBALS['ecs']->table("admin_user") . " set password='$cat[password]' where user_name='$user_name'");
//    } else {
//        /*         * ****POWERBY UEUCS_364642382***** */
//        $email = $cat['cat_name'] . 'admin@163.com';
//        $add_time = gmtime();
//        $action_list = "goods_manage,suppliers_manage,delivery_view,back_view";
//
//        $password = $cat['password'];
//
//        $sql1 = "SELECT nav_list FROM " . $ecs->table('admin_user') . " WHERE action_list = 'all'";
//        $row1 = $db->getRow($sql1);
//
//        $sql = "INSERT INTO " . $ecs->table('suppliers') . " (suppliers_name, is_check,is_alone) " .
//                "VALUES ('$user_name','1','1')";
//        $db->query($sql);
//        $suppliers_id = $db->getOne("SELECT suppliers_id FROM " . $ecs->table('suppliers') . " WHERE suppliers_name = '$user_name'");
//
//        $sql2 = "INSERT INTO " . $ecs->table('admin_user') . " (user_name,is_alone, email, password, add_time, nav_list, action_list,suppliers_id, role_id,is_check) " .
//                "VALUES ('$user_name', '1','$email', '$password', '$add_time', '$row1[nav_list]', '$action_list',$suppliers_id, '0','1')";
//
//        $db->query($sql2);
//    }
    //print_r($cat);exit;
    if ($db->autoExecute($ecs->table('region'), $cat, 'UPDATE', "region_id='$cat_id'")) {

        /* 更新分类信息成功 */
        clear_cache_files(); // 清除缓存
        admin_log($_POST['cat_name'], 'edit', 'area'); // 记录管理员操作

        /* 提示信息 */
        $link[] = array('text' => '返回列表', 'href' => 'area_cat.php?act=list');
        sys_msg('更新成功', 0, $link);
    }
}

/* ------------------------------------------------------ */
//-- 批量转移商品分类页面
/* ------------------------------------------------------ */
if ($_REQUEST['act'] == 'move') {
    /* 权限检查 */
    admin_priv('cat_drop');

    $cat_id = !empty($_REQUEST['cat_id']) ? intval($_REQUEST['cat_id']) : 0;

    /* 模板赋值 */
    $smarty->assign('ur_here', '转移运营中心下商品');
    $smarty->assign('action_link', array('href' => 'area_cat.php?act=list', 'text' => '运营中心分类'));

    $smarty->assign('cat_select', area_cat_list(0, $cat_id, true));
    $smarty->assign('form_act', 'move_cat');

    /* 显示页面 */
    assign_query_info();
    $smarty->display('area_cat_move.htm');
}

/* ------------------------------------------------------ */
//-- 处理批量转移商品分类的处理程序
/* ------------------------------------------------------ */
if ($_REQUEST['act'] == 'move_cat') {
    /* 权限检查 */
    admin_priv('cat_drop');

    $cat_id = !empty($_POST['cat_id']) ? intval($_POST['cat_id']) : 0;
    $target_cat_id = !empty($_POST['target_cat_id']) ? intval($_POST['target_cat_id']) : 0;

    /* 商品分类不允许为空 */
    if ($cat_id == 0 || $target_cat_id == 0) {
        $link[] = array('text' => $_LANG['go_back'], 'href' => 'area_cat.php?act=move');
        sys_msg($_LANG['cat_move_empty'], 0, $link);
    }

    /* 更新商品分类 */
    $sql = "UPDATE " . $ecs->table('goods') . " SET area_cat_id = '$target_cat_id' " .
            "WHERE area_cat_id = '$cat_id'";
    if ($db->query($sql)) {
        /* 清除缓存 */
        clear_cache_files();

        /* 提示信息 */
        $link[] = array('text' => $_LANG['go_back'], 'href' => 'area_cat.php?act=list');
        sys_msg('转移成功', 0, $link);
    }
}

/* ------------------------------------------------------ */
//-- 编辑排序序号
/* ------------------------------------------------------ */

if ($_REQUEST['act'] == 'edit_sort_order') {
    check_authz_json('cat_manage');

    $id = intval($_POST['id']);
    $val = intval($_POST['val']);

    if (cat_update($id, array('sort_order' => $val))) {
        clear_cache_files(); // 清除缓存
        make_json_result($val);
    } else {
        make_json_error($db->error());
    }
}

/* ------------------------------------------------------ */
//-- 编辑数量单位
/* ------------------------------------------------------ */

if ($_REQUEST['act'] == 'edit_measure_unit') {
    check_authz_json('cat_manage');

    $id = intval($_POST['id']);
    $val = json_str_iconv($_POST['val']);

    if (cat_update($id, array('measure_unit' => $val))) {
        clear_cache_files(); // 清除缓存
        make_json_result($val);
    } else {
        make_json_error($db->error());
    }
}

/* ------------------------------------------------------ */
//-- 编辑排序序号
/* ------------------------------------------------------ */

if ($_REQUEST['act'] == 'edit_grade') {
    check_authz_json('cat_manage');

    $id = intval($_POST['id']);
    $val = intval($_POST['val']);

    if ($val > 10 || $val < 0) {
        /* 价格区间数超过范围 */
        make_json_error($_LANG['grade_error']);
    }

    if (cat_update($id, array('grade' => $val))) {
        clear_cache_files(); // 清除缓存
        make_json_result($val);
    } else {
        make_json_error($db->error());
    }
}

/* ------------------------------------------------------ */
//-- 切换是否显示在导航栏
/* ------------------------------------------------------ */

if ($_REQUEST['act'] == 'toggle_show_in_nav') {
    check_authz_json('cat_manage');

    $id = intval($_POST['id']);
    $val = intval($_POST['val']);

    if (cat_update($id, array('show_in_nav' => $val)) != false) {
        if ($val == 1) {
            //显示
            $vieworder = $db->getOne("SELECT max(vieworder) FROM " . $ecs->table('nav') . " WHERE type = 'middle'");
            $vieworder += 2;
            $catname = $db->getOne("SELECT cat_name FROM " . $ecs->table('category') . " WHERE cat_id = '$id'");
            //显示在自定义导航栏中
            $_CFG['rewrite'] = 0;
            $uri = build_uri('category', array('cid' => $id), $catname);

            $nid = $db->getOne("SELECT id FROM " . $ecs->table('nav') . " WHERE ctype = 'c' AND cid = '" . $id . "' AND type = 'middle'");
            if (empty($nid)) {
                //不存在
                $sql = "INSERT INTO " . $ecs->table('nav') . " (name,ctype,cid,ifshow,vieworder,opennew,url,type) VALUES('" . $catname . "', 'c', '$id','1','$vieworder','0', '" . $uri . "','middle')";
            } else {
                $sql = "UPDATE " . $ecs->table('nav') . " SET ifshow = 1 WHERE ctype = 'c' AND cid = '" . $id . "' AND type = 'middle'";
            }
            $db->query($sql);
        } else {
            //去除
            $db->query("UPDATE " . $ecs->table('nav') . "SET ifshow = 0 WHERE ctype = 'c' AND cid = '" . $id . "' AND type = 'middle'");
        }
        clear_cache_files();
        make_json_result($val);
    } else {
        make_json_error($db->error());
    }
}

/* ------------------------------------------------------ */
//-- 切换是否显示
/* ------------------------------------------------------ */

if ($_REQUEST['act'] == 'toggle_is_show') {
    check_authz_json('cat_manage');

    $id = intval($_POST['id']);
    $val = intval($_POST['val']);

    if (cat_update($id, array('is_show' => $val)) != false) {
        clear_cache_files();
        make_json_result($val);
    } else {
        make_json_error($db->error());
    }
}

/* ------------------------------------------------------ */
//-- 删除商品分类
/* ------------------------------------------------------ */
if ($_REQUEST['act'] == 'remove') {
    check_authz_json('cat_manage');

    /* 初始化分类ID并取得分类名称 */
    $cat_id = intval($_GET['id']);
    $cat_name = $db->getOne('SELECT cat_name FROM ' . $ecs->table('region') . " WHERE region_id='$cat_id'");

    /* 当前分类下是否有子分类 */
    $cat_count = $db->getOne('SELECT COUNT(*) FROM ' . $ecs->table('region') . " WHERE parent_id='$cat_id'");

    /* 当前分类下是否存在商品 */
    $goods_count = $db->getOne('SELECT COUNT(*) FROM ' . $ecs->table('goods') . " WHERE area_cat_id='$cat_id'");

    /* 如果不存在下级子分类和商品，则删除之 */
    if ($cat_count == 0 && $goods_count == 0) {
        /* 删除分类 */
        $sql = 'DELETE FROM ' . $ecs->table('region') . " WHERE region_id = '$cat_id'";
        if ($db->query($sql)) {

            $admin_name = $cat_name . '管理员';
            //$GLOBALS['db']->query("delete from " . $GLOBALS['ecs']->table("admin_user") . " where user_name='" . $admin_name . "'");
            //$GLOBALS['db']->query("delete from " . $GLOBALS['ecs']->table("suppliers") . " where suppliers_name='" . $admin_name . "'");
            clear_cache_files();
            admin_log($cat_name, 'remove', 'area');
        }
    } else {
        make_json_error($cat_name . ' ' . '存在商品不能删除，请先转移商品');
    }

    $url = 'area_cat.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}


if ($_REQUEST['act'] == "set") {
    /* 权限检查 */
    admin_priv('cat_set');
    $smarty->assign('ur_here', '运营中心设置');
    $smarty->assign('action_link', array('href' => 'area_cat.php?act=list', 'text' => '运营中心列表'));


    $smarty->assign('form_act', 'set_save');

    $catSql = "select user_id,zuodan from " . $GLOBALS['ecs']->table("region") . " where region_id='" . $_GET["cat_id"] . "'";
    $cat_user_ids = $GLOBALS['db']->getRow($catSql);
    $cat_user_id = $cat_user_ids['user_id'];
    if (!empty($cat_user_id)) {
        $userSql = "select mobile_phone from " . $GLOBALS['ecs']->table("users") . " where user_id='" . $cat_user_id . "'";
        $mobile = $GLOBALS['db']->getOne($userSql);
    }
    $smarty->assign('cat_info', array('is_show' => 1, 'mobile' => $mobile, "cat_id" => $_GET["cat_id"], 'zuodan' => $cat_user_ids['zuodan']));
    assign_query_info();
    $smarty->display('area_cat_set.htm');
}
if ($_REQUEST['act'] == "set_save") {
    /* 权限检查 */
    admin_priv('cat_set');
    if (isset($_POST['user_id']) && !empty($_POST['user_id']) && isset($_POST['cat_id']) && !empty($_POST['cat_id']) && isset($_POST['zuodan'])) {
        $userSql = "SELECT user_id,user_name,mobile_phone,password,ec_salt,email from " . $GLOBALS['ecs']->table("users") . " where mobile_phone='" . $_POST['user_id'] . "'";
        $uid = $GLOBALS['db']->getRow($userSql);
        if (!empty($uid)) {
            
            //限制supplier_admin_user只能一个帐号
//             $sql = "SELECT user_id,uid FROM " . $GLOBALS['ecs']->table("supplier_admin_user") . " where uid = '".$uid['user_id']."'";
//             $admin_user_id = $GLOBALS['db']->getRow($sql);
//             if ($admin_user_id)
//             {
//                 sys_msg('当前人员已经是营运中心人员或商城入驻商家，请切换其他人员', 0, $link);
//                 exit();
//             }
            

//            $catSql = "select count(*) from " . $GLOBALS['ecs']->table("region") . " where user_id='" . $cat_user_id . "' and region_id <> '" . $_POST['cat_id'] . "'";
//            $catRows = $GLOBALS['db']->getOne($catSql);
//            if ($catRows * 1 === 0) {
//                $catSql = "update " . $GLOBALS['ecs']->table("region") . " SET user_id='" . $cat_user_id . "',zuodan='".$_POST['zuodan']."' where region_id='" . $_POST['cat_id'] . "'";
//                $GLOBALS['db']->query($catSql);
//                $link[] = array('text' => '返回列表', 'href' => 'area_cat.php?act=list');
//                sys_msg('设置成功', 0, $link);
//            } else {
//                $link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
//                sys_msg('当前人员已经是其他运营中心的营运中心人员，请切换其他人员', 0, $link);
//            }

            $sql = "SELECT user_id ,region_type,region_id FROM " . $GLOBALS['ecs']->table("region") . " WHERE region_id='" . $_POST['cat_id'] . "'";
            $regionInfo = $GLOBALS['db']->getRow($sql);
            if ($regionInfo['user_id'])
            {
                sys_msg('当前地区已存在运营中心,请清空后再添加', 0, $link);
                exit;
            }
            
            if (!empty($regionInfo)) {
                
                /* 查询该地区的上级,优化保单查询,存储省级市 */
                $i= 4;
                $all_rs = array();
                $rs = $_POST['cat_id'];
                while ($i>0)
                {
                    // 一级一级查出上级
                    $sql = "SELECT parent_id FROM " . $GLOBALS['ecs']->table("region") . " WHERE region_id='" . $rs . "'";
                    $rs = $GLOBALS['db']->getOne($sql);
                    $all_rs[] = $rs;
                    $i--;
                    if ($rs == 1)
                    {
                        break;
                    }
                }
                $all_diqu = array_reverse($all_rs);
                $all_diqu[] = $_POST['cat_id'] ;

                
                $catSql = "update " . $GLOBALS['ecs']->table("region") . " SET user_id='" . 
                $uid['user_id'] . "' , `province_id`  = '".$all_diqu[1]."', `city_id`   = '".$all_diqu[2]."', `district_id`    = '".$all_diqu[3]."' where region_id='" . $regionInfo['region_id'] . "'";
                $GLOBALS['db']->query($catSql);
				
				//周:判断当前运营中心是否存在，存在则update，否则插入
                $sql = "SELECT user_id,uid FROM " . $GLOBALS['ecs']->table("supplier_admin_user") . " where password <> '0000' and  role='2' and city_level='" . $regionInfo['region_type'] . "' and city_id='" . $regionInfo['region_id'] . "'";
                $admin_user_id = $GLOBALS['db']->getRow($sql);

                if (empty($admin_user_id)) {//插入
                    $sql = "INSERT INTO " . $GLOBALS['ecs']->table("supplier_admin_user") 
                        . "(uid,user_name,email,password,ec_salt,add_time,mobile_phone,role,city_id,city_level,checked,province_id,citys_id,district_id)" .
                            " VALUES('" . $uid['user_id'] . "','" . $uid['user_name'] . "','" . $uid['email'] . "','" . $uid['password'] . "','" . 
                        $uid['ec_salt'] . "','" . time() . "','" . $uid['mobile_phone'] . "',2,'" . $regionInfo['region_id'] . "','" . 
                    $regionInfo['region_type'] . "',1,'".$all_diqu[1]."','".$all_diqu[2]."','".$all_diqu[3]."')";
                    
                    log_account_change($uid['user_id'], 0, 0, 0, 0, "系统自动赠送城市奖励基数1000000", ACT_OTHER, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1000000);
                    $db->query($sql);
                    
                    //设置用户的角色为代理
                    $sql="update ".$GLOBALS['ecs']->table("users")."  set  is_bigfamily = 2 WHERE mobile_phone='".$uid['mobile_phone']."' " ;
                    $db->query($sql);
                } else {//修改
                    if ($admin_user_id['uid'] * 1 == $agency['user_id']) {
                        
                    } else {
                        $sql = "UPDATE " . $GLOBALS['ecs']->table("supplier_admin_user") . " SET `user_name`='" . $uid['user_name'] . "',  "
                                . " uid='" . $uid['user_id'] . "',"
                                . " email='" . $uid['email'] . "',"
                                . " password='" . $uid['password'] . "',"
                                . " ec_salt='" . $uid['ec_salt'] . "',"
                                . " province_id='" . $all_diqu[1] . "',"
                                . " citys_id='" . $all_diqu[2] . "',"
                                . " district_id='" . $all_diqu[3] . "',"
                                . " mobile_phone='" . $uid['mobile_phone'] . "' "
                                . " WHERE user_id=" . $admin_user_id['user_id'];
                        $db->query($sql);
                        
                        //周:运营中心自动设置用户的角色为代理
                        $sql=" update ".$GLOBALS['ecs']->table("users")."  set  is_bigfamily = 2 WHERE mobile_phone='".$uid['mobile_phone']."' " ;
                        $db->query($sql);
                    }
                }
                $link[] = array('text' => '返回列表', 'href' => 'area_cat.php?act=list');
                sys_msg('设置成功', 0, $link);
            } else {
                $link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
                sys_msg('城市不存在，无法设置', 0, $link);
            }
        } else {

            $link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
            sys_msg('人员员不存在，无法设置', 0, $link);
        }
    }
    else 
    {
        if (intval($_POST['cat_id']))
        {
            
            /* 查询该地区的上级,优化保单查询,存储省级市 */
            $i= 4;
            $all_rs = array();
            $rs = $_POST['cat_id'];
            while ($i>0)
            {
                // 一级一级查出上级
                $sql = "SELECT parent_id FROM " . $GLOBALS['ecs']->table("region") . " WHERE region_id='" . $rs . "'";
                $rs = $GLOBALS['db']->getOne($sql);
                $all_rs[] = $rs;
                $i--;
                if ($rs == 1)
                {
                    break;
                }
            }
            $all_diqu = array_reverse($all_rs);
            $all_diqu[] = $_POST['cat_id'] ;
            
            $sql = "SELECT user_id ,region_type,region_id FROM " . $GLOBALS['ecs']->table("region") . " WHERE region_id='" . intval($_POST['cat_id']) . "'";
            $regionInfo = $GLOBALS['db']->getRow($sql);
            if ($regionInfo['user_id'])
            {
                $catSql = "update " . $GLOBALS['ecs']->table("region") . " SET user_id=0 where region_id='" . $regionInfo['region_id'] . "'";
                $GLOBALS['db']->query($catSql);
                
                //查询会员 admin_user 信息
                $sql = "SELECT * FROM " . $GLOBALS['ecs']->table("supplier_admin_user") . " WHERE 
                    uid='" . $regionInfo['user_id'] . "' and  province_id='" . $all_diqu[1] . "' and citys_id='" . $all_diqu[2] . "' and district_id='" . $all_diqu[3] . "'  ";
                $admin_info = $GLOBALS['db']->getAll($sql);
                foreach ($admin_info as $val)
                {
                    $sql =  " UPDATE " . $GLOBALS['ecs']->table("supplier_admin_user") . "  SET user_name = '".$val['user_name']."_del' , action_list ='' , role = 0 , uid = '0000' , `password`='00' WHERE (`user_id`='".$val['user_id']."') LIMIT 1 " ;
                    $GLOBALS['db']->query($sql);
                }
            }
            sys_msg('已清空', 0, $link);
        }
    }
}

/* ------------------------------------------------------ */
//-- 编辑每日赠送积分数
/* ------------------------------------------------------ */

if ($_REQUEST['act'] == 'edit_day_points') {
    check_authz_json('cat_manage');

    $id = intval($_POST['id']);
    $val = ($_POST['val']);
    if (cat_update($id, array('day_points' => $val))) {
        clear_cache_files(); // 清除缓存
        make_json_result($val);
    } else {
        make_json_error($db->error());
    }
}

/* ------------------------------------------------------ */
//-- PRIVATE FUNCTIONS
/* ------------------------------------------------------ */
//
///**
// * 检查分类是否已经存在
// *
// * @param   string      $cat_name       分类名称
// * @param   integer     $parent_cat     上级分类
// * @param   integer     $exclude        排除的分类ID
// *
// * @return  boolean
// */
//function cat_exists($cat_name, $parent_cat, $exclude = 0)
//{
//    $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('category').
//           " WHERE parent_id = '$parent_cat' AND cat_name = '$cat_name' AND cat_id<>'$exclude'";
//    return ($GLOBALS['db']->getOne($sql) > 0) ? true : false;
//}

/**
 * 获得商品分类的所有信息
 *
 * @param   integer     $cat_id     指定的分类ID
 *
 * @return  mix
 */
function get_cat_info($cat_id) {
    $sql = "SELECT *,region_id as cat_id,region_name as cat_name FROM " . $GLOBALS['ecs']->table('region') . " WHERE region_id='$cat_id' LIMIT 1";
    return $GLOBALS['db']->getRow($sql);
}

/**
 * 添加商品分类
 *
 * @param   integer $cat_id
 * @param   array   $args
 *
 * @return  mix
 */
function cat_update($cat_id, $args) {
    if (empty($args) || empty($cat_id)) {
        return false;
    }
    return $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('region'), $args, 'UPDATE', "region_id='$cat_id'");
}

?>