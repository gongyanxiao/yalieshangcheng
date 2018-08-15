<?php

/**
 * ECSHOP 管理中心供货商管理
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.68ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: 68ecshop $
 * $Id: suppliers.php 15013 2009-05-13 09:31:42Z 68ecshop $
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/admin/supplier.php');
$smarty->assign('lang', $_LANG);
$stype = intval($_REQUEST["stype"]);

if ($stype > 0) {
    $_SESSION["supplier_stype"] = $stype;
}

if ($_SESSION["supplier_stype"]) {
    $stype = intval($_SESSION["supplier_stype"]);
    if (!in_array($stype, array(3, 4))) {
        $stype = 3;
    }
} else {
    if (!empty($_SESSION['supplier_stype'])) {
        $stype = $_SESSION["supplier_stype"];
    } else {
        $stype = 3;
    }
}
unset($_SESSION["supplier_stype"]);
$_SESSION["supplier_stype"] = $stype;
if ($stype == 3) {
    $adminpriv = "2_online_suppliers";
} else {
    $adminpriv = "3_offline_suppliers";
}
 
/* ------------------------------------------------------ */
//-- 供货商列表
/* ------------------------------------------------------ */
if ($_REQUEST['act'] == 'list') {
	
	if ($stype == 3) {
		$adminpriv = "2_online_suppliers_list";
	} else {
		$adminpriv = "3_offline_suppliers_list";
	}
	
	
    /* 检查权限 */
    admin_priv($adminpriv);
   
    /* 查询 */
    $result = suppliers_list();
    die("kkk");
    if ($_SESSION["supplier_stype"]) {
        if ($_SESSION["supplier_stype"] * 1 === 3) {
            $sname = "线上商家";
        } else {
            $sname = "联盟商家";
        }
    }  
    /* 模板赋值 */
    $ur_here_lang = $_REQUEST['status'] == '1' ? $_LANG['supplier_list'] : $_LANG['supplier_reg_list'];
    $smarty->assign('ur_here', $ur_here_lang); // 当前导航
    $smarty->assign('action_link', array(
        'text' => '添加' . $sname, 'href' => 'supplier.php?act=add',
    ));
    if ($_REQUEST['status'] * 1 == 1) {
        $smarty->assign('action_link2', array(
            "text" => "未通过" . $sname . "列表", 'href' => 'supplier.php?act=list&stype=' . $_SESSION["supplier_stype"]
        ));
    } else {
        $smarty->assign('action_link2', array(
            "text" => "通过" . $sname . "列表", 'href' => 'supplier.php?act=list&status=1&stype=' . $_SESSION["supplier_stype"]
        ));
    }
    $smarty->assign('full_page', 1); // 翻页参数

    $smarty->assign('status', $_REQUEST['status']);
    $smarty->assign('supplier_list', $result['result']);
    $smarty->assign('filter', $result['filter']);
    $smarty->assign('record_count', $result['record_count']);
    $smarty->assign('page_count', $result['page_count']);
    $smarty->assign('sort_suppliers_id', '<img src="images/sort_desc.gif">');
    $sql = "select rank_id,rank_name from " . $ecs->table('supplier_rank') . " order by sort_order";
    $supplier_rank = $db->getAll($sql);
    $smarty->assign('supplier_rank', $supplier_rank);
  
    /* 显示模板 */
    assign_query_info();
    $smarty->display('supplier_list.htm');
} elseif ($_REQUEST['act'] == 'add') {
    /* 检查权限 */
    admin_priv($adminpriv);
    $suppliers = array();

    /* 省市县 */
    $supplier_country = $supplier['country'] ? $supplier['country'] : $_CFG['shop_country'];
    $smarty->assign('country_list', get_regions());
    $smarty->assign('province_list', get_regions(1, $supplier_country));
    $smarty->assign('city_list', get_regions(2, $supplier['province']));
    $smarty->assign('district_list', get_regions(3, $supplier['city']));
    $smarty->assign('supplier_country', $supplier_country);
    /* 供货商等级 */
//    $sql = "select rank_name from " . $ecs->table('supplier_rank') . "  " ;
//    $rank_name = $db->getAll($sql);
//    $supplier['rank_name'] = $rank_name;
    // $sql="select rank_id,rank_name from ". $ecs->table('supplier_rank') ." order by sort_order";
    //$supplier_rank=$db->getAll($sql);
    //$smarty->assign('supplier_rank', $supplier_rank);

    /* 店铺类型 */
    $sql = "select cat_id,cat_name from " . $ecs->table('category') . "  where parent_id=0 ";
    $categories1 = $db->getAll($sql);

    $categories = array();
    foreach ($categories1 AS $row) {
        $categories[$row['cat_id']] = addslashes($row['cat_name']);
    }

    $smarty->assign('ur_here', $_LANG['edit_supplier']);
// 	 $lang_supplier_list = $status=='1' ? $_LANG['supplier_list'] :  $_LANG['supplier_reg_list'];
//      $smarty->assign('action_link', array('href' => 'supplier.php?act=list', 'text' =>$lang_supplier_list ));
    if ($_REQUEST['status'] == '1') {
        $lang_supplier_list = $_LANG['supplier_list'];
        $smarty->assign('action_link', array('href' => 'supplier.php?act=list&stype=' . $_SESSION["supplier_stype"] . '&status=1', 'text' => $lang_supplier_list));
    } else {
        $lang_supplier_list = $_LANG['supplier_reg_list'];
        $smarty->assign('action_link', array('href' => 'supplier.php?act=list&stype=' . $_SESSION["supplier_stype"] . '', 'text' => $lang_supplier_list));
    }

    $smarty->assign('form_action', 'add_post');
    $smarty->assign('supplier', $supplier);
    $smarty->assign('categories', $categories);
    /* 代码增加 By  www.68ecshop.com Start */
    // 商品等级
    $smarty->assign('rank_id', $supplier['rank_id']);
    $smarty->assign('supplier_rank_list', get_supplier_rank_list());
    /* 显示模板 */
    assign_query_info();
    $smarty->display('supplier_info_new.htm');
} elseif ($_REQUEST['act'] == 'add_post') {
    /* 检查权限 */
    admin_priv($adminpriv);
    $status_url = intval($_POST['status_url']);

    $sql = "SELECT * from " . $GLOBALS['ecs']->table("users") . " where mobile_phone='" . trim($_POST['user_id']) . "'";
    $supplier_old = $GLOBALS['db']->getRow($sql);
    if (empty($supplier_old)) {
        sys_msg('该手机号不是会员，请先注册会员！');
        exit;
    }

    //查询是否存在其他角色
    //限制supplier_admin_user只能一个帐号
    $sql = "SELECT supplier_id , user_id,uid FROM " . $GLOBALS['ecs']->table("supplier_admin_user") . " where uid = '" . $supplier_old['user_id'] . "'";
    $admin_user_id = $GLOBALS['db']->getRow($sql);
    if ($admin_user_id) {
        sys_msg('当前人员已经是营运中心人员或商城入驻商家，不能再次申请成为入驻商家', 0);
        exit();
    }

//    $sql = "SELECT count(*) FROM " . $GLOBALS['ecs']->table("region") . " where user_id=" . $supplier_old['user_id'];
//
//    $isYunYing = $GLOBALS['db']->getOne($sql);
//    if ($isYunYing * 1 > 0) {
//        sys_msg('该会员已经是运营人员，无法入驻商家！');
//        exit;
//    }
//
//    $sql = "SELECT count(*) FROM " . $GLOBALS['ecs']->table("supplier") . " where user_id=" . $supplier_old['user_id'];
//    $isYunYing = $GLOBALS['db']->getOne($sql);
//    if ($isYunYing * 1 > 0) {
//        sys_msg('该会员已经是商家，无法入驻商家！');
//        exit;
//    }
    include_once(ROOT_PATH . 'includes/cls_image.php');
    $image = new cls_image($_CFG['bgcolor']);
    $supplier = array(
        'user_id' => $supplier_old['user_id'],
        'rank_id' => intval($_POST['rank_id']),
        'country' => intval($_POST['country']),
        'province' => intval($_POST['province']),
        'city' => intval($_POST['city']),
        'district' => intval($_POST['district']),
        'address' => trim($_POST['address']),
        'contacts_name' => trim($_POST['contacts_name']),
        'contacts_phone' => trim($_POST['contacts_phone']),
        'supplier_name' => trim($_POST['supplier_name']),
        'shop_category' => trim($_POST['shop_category']),
        'rank_id' => $_POST['rank_id'],
        'supplier_remark' => trim($_POST['supplier_remark']),
        'status' => intval($_POST['status']),
        'zuodan' => $_POST['zuodan'],
        'business_sphere' => $_POST['business_sphere'],
        'wx' => $_POST['wx'],
        'applynum' => 3,
        'add_time' => gmtime(),
            //'role' => ($_POST['supp_type'] == 3 ? 3 : 4 ),
    );
    $sql = "SELECT `is_bigfamily` FROM " . $GLOBALS['ecs']->table("users") . " WHERE user_id='" . $supplier_old['user_id'] . "' limit 1";
    $is_bigfamily = $GLOBALS['db']->getOne($sql);
    $supplier['role'] = 4; //普通会员只能申请联盟商家
    if ($is_bigfamily > 0) {//大家庭人员可以入住线上商家
        $supplier['role'] = 3;
    }


    $res = $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table("supplier"), $supplier, 'INSERT');
    if ($res) {
        $supplierid = $GLOBALS['db']->insert_id();
        $config_data = array("shop_name" => $supplier['supplier_name'], "shop_title" => $supplier['supplier_name'], "shop_desc" => $supplier['supplier_name'], "shop_keywords" => $supplier['supplier_name'], "shop_country" => $supplier['country'], "shop_province" => $supplier['province'], "shop_city" => $supplier['city'], 'shop_district' => $supplier['district'], "shop_address" => $supplier['address'], "shop_closed" => 0, 'template' => 'dianpu1');
        foreach ($config_data as $configKey => $configValue) {
            $store_range = "";
            if (in_array($configKey, array('shop_country', 'shop_province', 'shop_city', 'shop_district'))) {
                $type = "manual";
            } else if (in_array($configKey, array('shop_closed'))) {
                $type = "select";
                $store_range = "0,1";
            } else if (in_array($configKey, array('shop_desc'))) {
                $type = "hidden";
            } else {
                $type = "text";
            }
            $sql = "insert into " . $GLOBALS['ecs']->table("supplier_shop_config") . " (parent_id,code,type,store_range,store_dir,value,sort_order,supplier_id)" .
                    "   VALUES (1,'" . $configKey . "','" . $type . "','" . $store_range . "','','" . $configValue . "',1,'" . $supplierid . "')";
            $GLOBALS['db']->query($sql);
        }
        $logo_path = "supplier/" . $supplierid;
        if ($_FILES['shop_logo']['size']) {
            $logo_name = "shop_logo" . $supplierid . substr($_FILES['shop_logo']['name'], -4);
            $picinfo = $image->upload_image($_FILES['shop_logo'], $logo_path, $logo_name);
            $parray = pathinfo($picinfo);
            if ($picinfo) {
                $create_pic_info = array('220x220' => array('width' => 220, 'height' => 220));
                foreach ($create_pic_info as $key => $val) {
                    $path = ROOT_PATH . $parray['dirname'] . '/';
                    $image->create_pic_name = "shop_logo" . $supplierid . "_" . $key;
                    $pinfo = $image->make_thumb(ROOT_PATH . $picinfo, $val['width'], $val['height'], $path);
                }
                $shop_logo = '/' . $pinfo;
            }
            $pic_sql = "update " . $ecs->table('supplier') . " set shop_logo='" . $shop_logo . "' where supplier_id=" . $supplierid;
            $db->query($pic_sql);
            $pnum = $db->affected_rows();
            $sql = "insert into " . $GLOBALS['ecs']->table("supplier_shop_config") . " (parent_id,code,type,store_range,store_dir,value,sort_order,supplier_id)" .
                    "   VALUES (1,'shop_logo','file','','','" . $shop_logo . "',1,'" . $supplierid . "')";
            $GLOBALS['db']->query($sql);
        }
        if ($_FILES['zhizhao']['size']) {
            $logo_name = "zhizhao" . $supplierid . substr($_FILES['zhizhao']['name'], -4);
            $picinfo = $image->upload_image($_FILES['zhizhao'], $logo_path, $logo_name);
            $parray = pathinfo($picinfo);
            if ($picinfo) {

                $create_pic_info = array('220x220' => array('width' => 220, 'height' => 220));
                foreach ($create_pic_info as $key => $val) {
                    $path = ROOT_PATH . $parray['dirname'] . '/';
                    $image->create_pic_name = "zhizhao" . $supplierid . "_" . $key;
                    $pinfo = $image->make_thumb(ROOT_PATH . $picinfo, $val['width'], $val['height'], $path);
                }
                $zhizhao = '/' . $pinfo;
            }
            $pic_sql = "update " . $ecs->table('supplier') . " set zhizhao='" . $zhizhao . "' where supplier_id=" . $supplierid;
            $db->query($pic_sql);
            $pnum = $db->affected_rows();
            $sql = "insert into " . $GLOBALS['ecs']->table("supplier_shop_config") . " (parent_id,code,type,store_range,store_dir,value,sort_order,supplier_id)" .
                    "   VALUES (1,'shop_zhizhao','file','','','" . $zhizhao . "',1,'" . $supplierid . "')";
            $GLOBALS['db']->query($sql);
        }
        if ($_FILES['shop_pics1']['size']) {
            $logo_name = "shop_pics1" . $supplierid . substr($_FILES['shop_pics1']['name'], -4);
            $picinfo = $image->upload_image($_FILES['shop_pics1'], $logo_path, $logo_name);
            $parray = pathinfo($picinfo);
            if ($picinfo) {

                $create_pic_info = array('220x220' => array('width' => 220, 'height' => 220));
                foreach ($create_pic_info as $key => $val) {
                    $path = ROOT_PATH . $parray['dirname'] . '/';
                    $image->create_pic_name = "shop_pics1" . $supplierid . "_" . $key;
                    $pinfo = $image->make_thumb(ROOT_PATH . $picinfo, $val['width'], $val['height'], $path);
                }
                $shop_pics1 = '/' . $pinfo;
            }
            $pic_sql = "update " . $ecs->table('supplier') . " set shop_pics1='" . $shop_pics1 . "' where supplier_id=" . $supplierid;
            $db->query($pic_sql);
            $pnum = $db->affected_rows();
            $sql = "insert into " . $GLOBALS['ecs']->table("supplier_shop_config") . " (parent_id,code,type,store_range,store_dir,value,sort_order,supplier_id)" .
                    "   VALUES (1,'shop_pics1','file','','','" . $shop_pics1 . "',1,'" . $supplierid . "')";
            $GLOBALS['db']->query($sql);
        }
        if ($_FILES['shop_pics2']['size']) {
            $logo_name = "shop_pics2" . $supplierid . substr($_FILES['shop_pics2']['name'], -4);
            $picinfo = $image->upload_image($_FILES['shop_pics2'], $logo_path, $logo_name);
            $parray = pathinfo($picinfo);
            if ($picinfo) {

                $create_pic_info = array('220x220' => array('width' => 220, 'height' => 220));
                foreach ($create_pic_info as $key => $val) {
                    $path = ROOT_PATH . $parray['dirname'] . '/';
                    $image->create_pic_name = "shop_pics2" . $supplierid . "_" . $key;
                    $pinfo = $image->make_thumb(ROOT_PATH . $picinfo, $val['width'], $val['height'], $path);
                }
                $shop_pics2 = '/' . $pinfo;
            }
            $pic_sql = "update " . $ecs->table('supplier') . " set shop_pics2='" . $shop_pics2 . "' where supplier_id=" . $supplierid;
            $db->query($pic_sql);
            $pnum = $db->affected_rows();
            $sql = "insert into " . $GLOBALS['ecs']->table("supplier_shop_config") . " (parent_id,code,type,store_range,store_dir,value,sort_order,supplier_id)" .
                    "   VALUES (1,'shop_pics2','file','','','" . $shop_pics2 . "',1,'" . $supplierid . "')";
            $GLOBALS['db']->query($sql);
        }
        if ($_FILES['shop_pics3']['size']) {
            $logo_name = "shop_pics3" . $supplierid . substr($_FILES['shop_pics3']['name'], -4);
            $picinfo = $image->upload_image($_FILES['shop_pics3'], $logo_path, $logo_name);
            $parray = pathinfo($picinfo);
            if ($picinfo) {

                $create_pic_info = array('220x220' => array('width' => 220, 'height' => 220));
                foreach ($create_pic_info as $key => $val) {
                    $path = ROOT_PATH . $parray['dirname'] . '/';
                    $image->create_pic_name = "shop_pics3" . $supplierid . "_" . $key;
                    $pinfo = $image->make_thumb(ROOT_PATH . $picinfo, $val['width'], $val['height'], $path);
                }
                $shop_pics3 = '/' . $pinfo;
            }
            $pic_sql = "update " . $ecs->table('supplier') . " set shop_pics3='" . $shop_pics3 . "' where supplier_id=" . $supplierid;
            $db->query($pic_sql);
            $pnum = $db->affected_rows();
            $sql = "insert into " . $GLOBALS['ecs']->table("supplier_shop_config") . " (parent_id,code,type,store_range,store_dir,value,sort_order,supplier_id)" .
                    "   VALUES (1,'shop_pics3','file','','','" . $shop_pics3 . "',1,'" . $supplierid . "')";
            $GLOBALS['db']->query($sql);
        }
        if ($_FILES['shop_pics4']['size']) {
            $logo_name = "shop_pics4" . $supplierid . substr($_FILES['shop_pics4']['name'], -4);
            $picinfo = $image->upload_image($_FILES['shop_pics4'], $logo_path, $logo_name);
            $parray = pathinfo($picinfo);
            if ($picinfo) {

                $create_pic_info = array('220x220' => array('width' => 220, 'height' => 220));
                foreach ($create_pic_info as $key => $val) {
                    $path = ROOT_PATH . $parray['dirname'] . '/';
                    $image->create_pic_name = "shop_pics4" . $supplierid . "_" . $key;
                    $pinfo = $image->make_thumb(ROOT_PATH . $picinfo, $val['width'], $val['height'], $path);
                }
                $shop_pics4 = '/' . $pinfo;
            }
            $pic_sql = "update " . $ecs->table('supplier') . " set shop_pics4='" . $shop_pics4 . "' where supplier_id=" . $supplierid;
            $db->query($pic_sql);
            $pnum = $db->affected_rows();
            $sql = "insert into " . $GLOBALS['ecs']->table("supplier_shop_config") . " (parent_id,code,type,store_range,store_dir,value,sort_order,supplier_id)" .
                    "   VALUES (1,'shop_pics4','file','','','" . $shop_pics4 . "',1,'" . $supplierid . "')";
            $GLOBALS['db']->query($sql);
        }
        if ($_FILES['shop_pics5']['size']) {
            $logo_name = "shop_pics5" . $supplierid . substr($_FILES['shop_pics5']['name'], -4);
            $picinfo = $image->upload_image($_FILES['shop_pics5'], $logo_path, $logo_name);
            $parray = pathinfo($picinfo);
            if ($picinfo) {

                $create_pic_info = array('220x220' => array('width' => 220, 'height' => 220));
                foreach ($create_pic_info as $key => $val) {
                    $path = ROOT_PATH . $parray['dirname'] . '/';
                    $image->create_pic_name = "shop_pics5" . $supplierid . "_" . $key;
                    $pinfo = $image->make_thumb(ROOT_PATH . $picinfo, $val['width'], $val['height'], $path);
                }
                $shop_pics5 = '/' . $pinfo;
            }
            $pic_sql = "update " . $ecs->table('supplier') . " set shop_pics5='" . $shop_pics5 . "' where supplier_id=" . $supplierid;
            $db->query($pic_sql);
            $pnum = $db->affected_rows();
            $sql = "insert into " . $GLOBALS['ecs']->table("supplier_shop_config") . " (parent_id,code,type,store_range,store_dir,value,sort_order,supplier_id)" .
                    "   VALUES (1,'shop_pics5','file','','','" . $shop_pics5 . "',1,'" . $supplierid . "')";
            $GLOBALS['db']->query($sql);
        }

        $supplier_old['supplier_id'] = $supplierid;
        //更新相关店铺的管理员状态
        $sql = "select * from " . $ecs->table('supplier_admin_user') . " where supplier_id=" . $supplierid;
        $info = $db->getAll($sql);
        if (count($info) > 0) {
            $sql = "UPDATE " . $ecs->table('supplier_admin_user') . " SET user_name = '" . $supplier_old['mobile_phone'] . "',password = '" . $supplier_old['password'] . "',email='" . $supplier_old['email'] . "',ec_salt='" . $supplier_old['ec_salt'] . "', checked = " . intval($_POST['status']) . ",mobile_phone='" . $supplier_old['mobile_phone'] . "' ,`district_id`='" . intval($_POST['district']) . "'  ,`province_id`='" . intval($_POST['province']) . "'  ,`citys_id`='" . intval($_POST['city']) . "' ,`role`='" . $supplier['role'] . "'  WHERE supplier_id=" . $supplier_old['supplier_id'] . " and uid=" . $supplier_old['user_id'];
            $db->query($sql);
        } else {
            $insql = "INSERT INTO " . $ecs->table('supplier_admin_user') . " (`uid`, `user_name`, `email`, `password`, `ec_salt`, `add_time`, `last_login`, `last_ip`, `action_list`, `nav_list`, `lang_type`, `agency_id`, `supplier_id`, `todolist`, `role_id`, `checked`,`mobile_phone` ,province_id,citys_id,district_id,role) " .
                    "VALUES(" . $supplier_old['user_id'] . ", '" . $supplier_old['mobile_phone'] . "', '" . $supplier_old['email'] . "', '" . $supplier_old['password'] . "', '" . $supplier_old['ec_salt'] . "', " . $supplier_old['last_login'] . ", " . $supplier_old['last_login'] . ", '" . $supplier_old['last_ip'] . "', 'all', '', '', 0, " . $supplier_old['supplier_id'] . ", NULL, NULL, " . intval($_POST['status']) . ",'" . $supplier_old['mobile_phone'] . "' ,'" . intval($_POST['province']) . "','" . intval($_POST['city']) . "','" . intval($_POST['district']) . "','" . $supplier['role'] . "')";

            $db->query($insql);
        }
        /* 代码增加_end  By  supplier.68ecshop.com */

        if ($_POST['status'] != '1') {
            $sql = "update " . $ecs->table('goods') . " set is_on_sale=0 where supplier_id='$supplierid' ";
            $db->query($sql);
        }

        /* 清除缓存 */
        clear_cache_files();

        /* 提示信息 */
        $links[] = array('href' => ($status_url > 0 ? 'supplier.php?act=list&stype=' . $_SESSION["supplier_stype"] . '&status=1' : 'supplier.php?act=list'), 'text' => ($status_url > 0 ? $_LANG['back_supplier_list'] : $_LANG['back_supplier_reg']));
        sys_msg($_LANG['edit_supplier_ok'], 0, $links);
    } else {
        sys_msg('添加失败！');
    }
} elseif ($_REQUEST['act'] == "edit_new") {
    /* 检查权限 */
    admin_priv($adminpriv);
    $suppliers = array();

    /* 取得供货商信息 */
    $id = $_REQUEST['id'];
// 	 $status = intval($_REQUEST['status']);
    $sql = "SELECT * FROM " . $ecs->table('supplier') . " WHERE supplier_id = '$id'";
    $supplier = $db->getRow($sql);
    if (count($supplier) <= 0) {
        sys_msg('该供应商不存在！');
    }

    /* 省市县 */
    $supplier_country = $supplier['country'] ? $supplier['country'] : $_CFG['shop_country'];
    $smarty->assign('country_list', get_regions());
    $smarty->assign('province_list', get_regions(1, $supplier_country));
    $smarty->assign('city_list', get_regions(2, $supplier['province']));
    $smarty->assign('district_list', get_regions(3, $supplier['city']));
    $smarty->assign('supplier_country', $supplier_country);
    /* 供货商等级 */
//    $sql = "select rank_name from " . $ecs->table('supplier_rank') . " where rank_id = " . $supplier['rank_id'];
//    $rank_name = $db->getOne($sql);
//    $supplier['rank_name'] = $rank_name;
    // $sql="select rank_id,rank_name from ". $ecs->table('supplier_rank') ." order by sort_order";
    //$supplier_rank=$db->getAll($sql);
    //$smarty->assign('supplier_rank', $supplier_rank);

    $sql = "select cat_id,cat_name from " . $ecs->table('category') . "  where parent_id=0 ";
    $categories1 = $db->getAll($sql);

    $categories = array();
    foreach ($categories1 AS $row) {
        $categories[$row['cat_id']] = addslashes($row['cat_name']);
    }

    $smarty->assign('ur_here', $_LANG['edit_supplier']);

    $smarty->assign('categories', $categories);

    $cat_id = $supplier["shop_category"];

    $smarty->assign('cat_id', $cat_id);

    $smarty->assign('ur_here', $_LANG['edit_supplier']);
// 	 $lang_supplier_list = $status=='1' ? $_LANG['supplier_list'] :  $_LANG['supplier_reg_list'];
//      $smarty->assign('action_link', array('href' => 'supplier.php?act=list', 'text' =>$lang_supplier_list ));
    if ($_REQUEST['status'] == '1') {
        $lang_supplier_list = $_LANG['supplier_list'];
        $smarty->assign('action_link', array('href' => 'supplier.php?act=list&stype=' . $_SESSION["supplier_stype"] . '&status=1', 'text' => $lang_supplier_list));
    } else {
        $lang_supplier_list = $_LANG['supplier_reg_list'];
        $smarty->assign('action_link', array('href' => 'supplier.php?act=list&stype=' . $_SESSION["supplier_stype"] . '', 'text' => $lang_supplier_list));
    }

    $smarty->assign('form_action', 'update_new');
    $smarty->assign('supplier', $supplier);
    /* 代码增加 By  www.68ecshop.com Start */
    // 商品等级
    $smarty->assign('rank_id', $supplier['rank_id']);
    $smarty->assign('supplier_rank_list', get_supplier_rank_list());
    /* 代码增加 By  www.68ecshop.com End */

    assign_query_info();

    $smarty->display('supplier_info_new.htm');
} elseif ($_REQUEST['act'] == 'update_new') {
	/*周:审核店铺*/
    /* 检查权限 */
    admin_priv($adminpriv);
    $status_url = intval($_POST['status_url']);

    $sql = "SELECT * from " . $GLOBALS['ecs']->table("users") . " where user_id='" . trim($_POST['user_id']) . "'";
    $supplier_old = $GLOBALS['db']->getRow($sql);
    if (empty($supplier_old)) {
        sys_msg('该手机号不是会员，请先注册会员！');
        exit;
    }

    $supplierid = intval($_POST['id']);

    //查询是否存在其他角色
    //限制supplier_admin_user只能一个帐号
//     $sql = "SELECT supplier_id , user_id,uid FROM " . $GLOBALS['ecs']->table("supplier_admin_user") . " where uid = '" . $supplier_old['user_id'] . "'";
//     $admin_user_id = $GLOBALS['db']->getRow($sql);
//     if ($admin_user_id && ($admin_user_id['supplier_id'] != $supplierid)) {
//         sys_msg('当前人员已经是营运中心人员或商城入驻商家，不能再次申请成为入驻商家', 0);
//         exit();
//     }

//      $sql="SELECT user_id FROM `".$GLOBALS['ecs']->table("supplier")."` WHERE user_id =".$_POST['user_id'];
//      $isExistUserId= $GLOBALS['db']->getOne($sql);
//      if($isExistUserId){
//      	sys_msg('您已经开过店了, 不能再开店！');
//         exit;
//      }
     
     
//    $sql = "SELECT count(*) FROM " . $GLOBALS['ecs']->table("region") . " where user_id=" . $supplier_old['user_id'];
//
//    $isYunYing = $GLOBALS['db']->getOne($sql);
//    if ($isYunYing * 1 > 0) {
//        sys_msg('该会员已经是运营人员，无法入驻商家！');
//        exit;
//    }


    $sql = "SELECT count(*) FROM " . $GLOBALS['ecs']->table("supplier") . " where supplier_id=" . $supplierid;
    $isYunYing = $GLOBALS['db']->getOne($sql);
    if ($isYunYing * 1 == 0) {
        sys_msg('商家信息无效！');
        exit;
    }
    include_once(ROOT_PATH . 'includes/cls_image.php');
    $image = new cls_image($_CFG['bgcolor']);
    $supplier = array(
        'user_id' => $supplier_old['user_id'],
        'rank_id' => intval($_POST['rank_id']),
        'country' => intval($_POST['country']),
        'province' => intval($_POST['province']),
        'city' => intval($_POST['city']),
        'district' => intval($_POST['district']),
        'address' => trim($_POST['address']),
        'contacts_name' => trim($_POST['contacts_name']),
        'contacts_phone' => trim($_POST['contacts_phone']),
        'supplier_name' => trim($_POST['supplier_name']),
        'shop_category' => trim($_POST['shop_category']),
        'supplier_remark' => trim($_POST['supplier_remark']),
        'status' => intval($_POST['status']),
        'supplier_rebate' => 10,
        'zuodan' => $_POST['zuodan'],
        'business_sphere' => $_POST['business_sphere'],
        'wx' => $_POST['wx'],
        'applynum' => 3,
        'add_time' => gmtime(),
        'role' => ( $_POST['supp_type'] == 3 ? 3 : 4 ),
    );
    //周:审核后修改商家表
    $res = $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table("supplier"), $supplier, 'UPDATE', 'supplier_id=' . $supplierid);
    if ($res) {//周:修改图片资源
        $config_data = array("shop_name" => $supplier['supplier_name'], "shop_title" => $supplier['supplier_name'], "shop_desc" => $supplier['supplier_name'], "shop_keywords" => $supplier['supplier_name'], "shop_country" => $supplier['country'], "shop_province" => $supplier['province'], "shop_city" => $supplier['city'], 'shop_district' => $supplier['district'], "shop_address" => $supplier['address'], "shop_closed" => 0, 'template' => 'dianpu1');
        foreach ($config_data as $configKey => $configValue) {
            $store_range = "";
            if (in_array($configKey, array('shop_country', 'shop_province', 'shop_city', 'shop_district'))) {
                $type = "manual";
            } else if (in_array($configKey, array('shop_closed'))) {
                $type = "select";
                $store_range = "0,1";
            } else if (in_array($configKey, array('shop_desc', "template"))) {
                $type = "hidden";
            } else {
                $type = "text";
            }
            $sql = "DELETE FROM " . $GLOBALS['ecs']->table("supplier_shop_config") . " where supplier_id=" . $supplierid . " and code='" . $configKey . "'";
            $GLOBALS['db']->query($sql);
            $sql = "insert into " . $GLOBALS['ecs']->table("supplier_shop_config") . " (parent_id,code,type,store_range,store_dir,value,sort_order,supplier_id)" .
                    "   VALUES (1,'" . $configKey . "','" . $type . "','" . $store_range . "','','" . $configValue . "',1,'" . $supplierid . "')";
            $GLOBALS['db']->query($sql);
        }
        $logo_path = "supplier/" . $supplierid;
        if ($_FILES['shop_logo']['size']) {
            $logo_name = "shop_logo" . $supplierid . substr($_FILES['shop_logo']['name'], -4);
            $picinfo = $image->upload_image($_FILES['shop_logo'], $logo_path, $logo_name);
            $parray = pathinfo($picinfo);
            if ($picinfo) {
                $create_pic_info = array('220x220' => array('width' => 220, 'height' => 220));
                foreach ($create_pic_info as $key => $val) {
                    $path = ROOT_PATH . $parray['dirname'] . '/';
                    $image->create_pic_name = "shop_logo" . $supplierid . "_" . $key;
                    $pinfo = $image->make_thumb(ROOT_PATH . $picinfo, $val['width'], $val['height'], $path);
                }
                $shop_logo = '/' . $pinfo;
            }
            $pic_sql = "update " . $ecs->table('supplier') . " set shop_logo='" . $shop_logo . "' where supplier_id=" . $supplierid;
            $db->query($pic_sql);
            $pnum = $db->affected_rows();
            $sql = "DELETE FROM " . $GLOBALS['ecs']->table("supplier_shop_config") . " where supplier_id=" . $supplierid . " and code='shop_logo'";
            $GLOBALS['db']->query($sql);
            $sql = "insert into " . $GLOBALS['ecs']->table("supplier_shop_config") . " (parent_id,code,type,store_range,store_dir,value,sort_order,supplier_id)" .
                    "   VALUES (1,'shop_logo','file','','','" . $shop_logo . "',1,'" . $supplierid . "')";
            $GLOBALS['db']->query($sql);
        }
        if ($_FILES['zhizhao']['size']) {
            $logo_name = "zhizhao" . $supplierid . substr($_FILES['zhizhao']['name'], -4);
            $picinfo = $image->upload_image($_FILES['zhizhao'], $logo_path, $logo_name);
            $parray = pathinfo($picinfo);
            if ($picinfo) {

                $create_pic_info = array('220x220' => array('width' => 220, 'height' => 220));
                foreach ($create_pic_info as $key => $val) {
                    $path = ROOT_PATH . $parray['dirname'] . '/';
                    $image->create_pic_name = "zhizhao" . $supplierid . "_" . $key;
                    $pinfo = $image->make_thumb(ROOT_PATH . $picinfo, $val['width'], $val['height'], $path);
                }
                $zhizhao = '/' . $pinfo;
            }
            $pic_sql = "update " . $ecs->table('supplier') . " set zhizhao='" . $zhizhao . "' where supplier_id=" . $supplierid;
            $db->query($pic_sql);
            $pnum = $db->affected_rows();
            $sql = "DELETE FROM " . $GLOBALS['ecs']->table("supplier_shop_config") . " where supplier_id=" . $supplierid . " and code='shop_zhizhao'";
            $GLOBALS['db']->query($sql);
            $sql = "insert into " . $GLOBALS['ecs']->table("supplier_shop_config") . " (parent_id,code,type,store_range,store_dir,value,sort_order,supplier_id)" .
                    "   VALUES (1,'shop_zhizhao','file','','','" . $zhizhao . "',1,'" . $supplierid . "')";
            $GLOBALS['db']->query($sql);
        }
        if ($_FILES['shop_pics1']['size']) {
            $logo_name = "shop_pics1" . $supplierid . substr($_FILES['shop_pics1']['name'], -4);
            $picinfo = $image->upload_image($_FILES['shop_pics1'], $logo_path, $logo_name);
            $parray = pathinfo($picinfo);
            if ($picinfo) {

                $create_pic_info = array('220x220' => array('width' => 220, 'height' => 220));
                foreach ($create_pic_info as $key => $val) {
                    $path = ROOT_PATH . $parray['dirname'] . '/';
                    $image->create_pic_name = "shop_pics1" . $supplierid . "_" . $key;
                    $pinfo = $image->make_thumb(ROOT_PATH . $picinfo, $val['width'], $val['height'], $path);
                }
                $shop_pics1 = '/' . $pinfo;
            }
            $pic_sql = "update " . $ecs->table('supplier') . " set shop_pics1='" . $shop_pics1 . "' where supplier_id=" . $supplierid;
            $db->query($pic_sql);
            $pnum = $db->affected_rows();
            $sql = "DELETE FROM " . $GLOBALS['ecs']->table("supplier_shop_config") . " where supplier_id=" . $supplierid . " and code='shop_pics1'";
            $GLOBALS['db']->query($sql);
            $sql = "insert into " . $GLOBALS['ecs']->table("supplier_shop_config") . " (parent_id,code,type,store_range,store_dir,value,sort_order,supplier_id)" .
                    "   VALUES (1,'shop_pics1','file','','','" . $shop_pics1 . "',1,'" . $supplierid . "')";
            $GLOBALS['db']->query($sql);
        }
        if ($_FILES['shop_pics2']['size']) {
            $logo_name = "shop_pics2" . $supplierid . substr($_FILES['shop_pics2']['name'], -4);
            $picinfo = $image->upload_image($_FILES['shop_pics2'], $logo_path, $logo_name);
            $parray = pathinfo($picinfo);
            if ($picinfo) {

                $create_pic_info = array('220x220' => array('width' => 220, 'height' => 220));
                foreach ($create_pic_info as $key => $val) {
                    $path = ROOT_PATH . $parray['dirname'] . '/';
                    $image->create_pic_name = "shop_pics2" . $supplierid . "_" . $key;
                    $pinfo = $image->make_thumb(ROOT_PATH . $picinfo, $val['width'], $val['height'], $path);
                }
                $shop_pics2 = '/' . $pinfo;
            }
            $pic_sql = "update " . $ecs->table('supplier') . " set shop_pics2='" . $shop_pics2 . "' where supplier_id=" . $supplierid;
            $db->query($pic_sql);
            $pnum = $db->affected_rows();
            $sql = "DELETE FROM " . $GLOBALS['ecs']->table("supplier_shop_config") . " where supplier_id=" . $supplierid . " and code='shop_pics2'";
            $GLOBALS['db']->query($sql);
            $sql = "insert into " . $GLOBALS['ecs']->table("supplier_shop_config") . " (parent_id,code,type,store_range,store_dir,value,sort_order,supplier_id)" .
                    "   VALUES (1,'shop_pics2','file','','','" . $shop_pics2 . "',1,'" . $supplierid . "')";
            $GLOBALS['db']->query($sql);
        }
        if ($_FILES['shop_pics3']['size']) {
            $logo_name = "shop_pics3" . $supplierid . substr($_FILES['shop_pics3']['name'], -4);
            $picinfo = $image->upload_image($_FILES['shop_pics3'], $logo_path, $logo_name);
            $parray = pathinfo($picinfo);
            if ($picinfo) {

                $create_pic_info = array('220x220' => array('width' => 220, 'height' => 220));
                foreach ($create_pic_info as $key => $val) {
                    $path = ROOT_PATH . $parray['dirname'] . '/';
                    $image->create_pic_name = "shop_pics3" . $supplierid . "_" . $key;
                    $pinfo = $image->make_thumb(ROOT_PATH . $picinfo, $val['width'], $val['height'], $path);
                }
                $shop_pics3 = '/' . $pinfo;
            }
            $pic_sql = "update " . $ecs->table('supplier') . " set shop_pics3='" . $shop_pics3 . "' where supplier_id=" . $supplierid;
            $db->query($pic_sql);
            $pnum = $db->affected_rows();
            $sql = "DELETE FROM " . $GLOBALS['ecs']->table("supplier_shop_config") . " where supplier_id=" . $supplierid . " and code='shop_pics3'";
            $GLOBALS['db']->query($sql);
            $sql = "insert into " . $GLOBALS['ecs']->table("supplier_shop_config") . " (parent_id,code,type,store_range,store_dir,value,sort_order,supplier_id)" .
                    "   VALUES (1,'shop_pics3','file','','','" . $shop_pics3 . "',1,'" . $supplierid . "')";
            $GLOBALS['db']->query($sql);
        }
        if ($_FILES['shop_pics4']['size']) {
            $logo_name = "shop_pics4" . $supplierid . substr($_FILES['shop_pics4']['name'], -4);
            $picinfo = $image->upload_image($_FILES['shop_pics4'], $logo_path, $logo_name);
            $parray = pathinfo($picinfo);
            if ($picinfo) {

                $create_pic_info = array('220x220' => array('width' => 220, 'height' => 220));
                foreach ($create_pic_info as $key => $val) {
                    $path = ROOT_PATH . $parray['dirname'] . '/';
                    $image->create_pic_name = "shop_pics4" . $supplierid . "_" . $key;
                    $pinfo = $image->make_thumb(ROOT_PATH . $picinfo, $val['width'], $val['height'], $path);
                }
                $shop_pics4 = '/' . $pinfo;
            }
            $pic_sql = "update " . $ecs->table('supplier') . " set shop_pics4='" . $shop_pics4 . "' where supplier_id=" . $supplierid;
            $db->query($pic_sql);
            $pnum = $db->affected_rows();
            $sql = "DELETE FROM " . $GLOBALS['ecs']->table("supplier_shop_config") . " where supplier_id=" . $supplierid . " and code='shop_pics4'";
            $GLOBALS['db']->query($sql);
            $sql = "insert into " . $GLOBALS['ecs']->table("supplier_shop_config") . " (parent_id,code,type,store_range,store_dir,value,sort_order,supplier_id)" .
                    "   VALUES (1,'shop_pics4','file','','','" . $shop_pics4 . "',1,'" . $supplierid . "')";
            $GLOBALS['db']->query($sql);
        }
        if ($_FILES['shop_pics5']['size']) {
            $logo_name = "shop_pics5" . $supplierid . substr($_FILES['shop_pics5']['name'], -4);
            $picinfo = $image->upload_image($_FILES['shop_pics5'], $logo_path, $logo_name);
            $parray = pathinfo($picinfo);
            if ($picinfo) {

                $create_pic_info = array('220x220' => array('width' => 220, 'height' => 220));
                foreach ($create_pic_info as $key => $val) {
                    $path = ROOT_PATH . $parray['dirname'] . '/';
                    $image->create_pic_name = "shop_pics5" . $supplierid . "_" . $key;
                    $pinfo = $image->make_thumb(ROOT_PATH . $picinfo, $val['width'], $val['height'], $path);
                }
                $shop_pics5 = '/' . $pinfo;
            }
            $pic_sql = "update " . $ecs->table('supplier') . " set shop_pics5='" . $shop_pics5 . "' where supplier_id=" . $supplierid;
            $db->query($pic_sql);
            $pnum = $db->affected_rows();
            $sql = "DELETE FROM " . $GLOBALS['ecs']->table("supplier_shop_config") . " where supplier_id=" . $supplierid . " and code='shop_pics5'";
            $GLOBALS['db']->query($sql);
            $sql = "insert into " . $GLOBALS['ecs']->table("supplier_shop_config") . " (parent_id,code,type,store_range,store_dir,value,sort_order,supplier_id)" .
                    "   VALUES (1,'shop_pics5','file','','','" . $shop_pics5 . "',1,'" . $supplierid . "')";
            $GLOBALS['db']->query($sql);
        }

        $supplier_old['supplier_id'] = $supplierid;
        //更新相关店铺的管理员状态
        $sql = "select * from " . $ecs->table('supplier_admin_user') . " where supplier_id=" . $supplierid;
        $info = $db->getAll($sql);
        if (count($info) > 0) {//周:如果商家账户存在, 就修改账户
            $sql = "UPDATE " . $ecs->table('supplier_admin_user') . " SET user_name = '" . $supplier_old['user_name'] . "',password = '" . $supplier_old['password'] . "',email='" . $supplier_old['email'] . "',ec_salt='" . $supplier_old['ec_salt'] . "', checked = " . intval($_POST['status']) . ",`mobile_phone`='" . $supplier_old['mobile_phone'] . "'  ,`district_id`='" . intval($_POST['district']) . "'  ,`province_id`='" . intval($_POST['province']) . "'  ,`citys_id`='" . intval($_POST['city']) . "'  , `role` = '" . $supplier['role'] . "'
            WHERE supplier_id=" . $supplier_old['supplier_id'] . " and uid=" . $supplier_old['user_id'];
            $db->query($sql);
        } else {//周:如果商家账户不存在, 就添加一个商家账户
            $insql = "INSERT INTO " . $ecs->table('supplier_admin_user') . " (`uid`, `user_name`, `email`, `password`, `ec_salt`, `add_time`, `last_login`, `last_ip`, `action_list`, `nav_list`, `lang_type`, `agency_id`, `supplier_id`, `todolist`, `role_id`, `checked`,`mobile_phone`,province_id,citys_id,district_id,role) " .
                    "VALUES(" . $supplier_old['user_id'] . ", '" . $supplier_old['user_name'] . "', '" . $supplier_old['email'] . "', '" . $supplier_old['password'] . "', '" . $supplier_old['ec_salt'] . "', " . $supplier_old['last_login'] . ", " . $supplier_old['last_login'] . ", '" . $supplier_old['last_ip'] . "', 'all', '', '', 0, " . $supplier_old['supplier_id'] . ", NULL, NULL, " . intval($_POST['status']) . ",'" . $supplier_old['mobile_phone'] . "','" . intval($_POST['province']) . "','" . intval($_POST['city']) . "','" . intval($_POST['district']) . "','" . $supplier['role'] . "')";
            $db->query($insql);

            // 联盟商家 赠送奖励基数
            if ($supplier['role'] == 4) {//周:角色为联盟商家
                log_account_change($supplier_old['user_id'], 0, 0, 0, 0, "系统自动赠送城市奖励基数10000", ACT_OTHER, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 10000);
            }
        }
        /* 代码增加_end  By  supplier.68ecshop.com */

        if ($_POST['status'] != '1') {
            $sql = "update " . $ecs->table('goods') . " set is_on_sale=0 where supplier_id='$supplierid' ";
            $db->query($sql);
        }

        /* 清除缓存 */
        clear_cache_files();

        /* 提示信息 */
        $links[] = array('href' => ($status_url > 0 ? 'supplier.php?act=list&stype=' . $_SESSION["supplier_stype"] . '&status=1' : 'supplier.php?act=list'), 'text' => ($status_url > 0 ? $_LANG['back_supplier_list'] : $_LANG['back_supplier_reg']));
        sys_msg($_LANG['edit_supplier_ok'], 0, $links);
    } else {
        sys_msg('添加失败！');
    }
}
/* ------------------------------------------------------ */
//-- 排序、分页、查询
/* ------------------------------------------------------ */ 
elseif ($_REQUEST['act'] == 'query') {
	if ($stype == 3) {
		$adminpriv = "2_online_suppliers_list";
	} else {
		$adminpriv = "3_offline_suppliers_list";
	}
    check_authz_json($adminpriv);

    $result = suppliers_list();

    $smarty->assign('supplier_list', $result['result']);
    $smarty->assign('filter', $result['filter']);
    $smarty->assign('record_count', $result['record_count']);
    $smarty->assign('page_count', $result['page_count']);

    /* 排序标记 */
    $sort_flag = sort_flag($result['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('supplier_list.htm'), '', array('filter' => $result['filter'], 'page_count' => $result['page_count']));
}


/* ------------------------------------------------------ */
//-- 查看、编辑供货商
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'edit') {
    /* 检查权限 */
    admin_priv($adminpriv);
    $suppliers = array();

    /* 取得供货商信息 */
    $id = $_REQUEST['id'];
// 	 $status = intval($_REQUEST['status']);
    $sql = "SELECT * FROM " . $ecs->table('supplier') . " WHERE supplier_id = '$id'";
    $supplier = $db->getRow($sql);
    if (count($supplier) <= 0) {
        sys_msg('该供应商不存在！');
    }

    /* 省市县 */
    $supplier_country = $supplier['country'] ? $supplier['country'] : $_CFG['shop_country'];
    $smarty->assign('country_list', get_regions());
    $smarty->assign('province_list', get_regions(1, $supplier_country));
    $smarty->assign('city_list', get_regions(2, $supplier['province']));
    $smarty->assign('district_list', get_regions(3, $supplier['city']));
    $smarty->assign('supplier_country', $supplier_country);
    /* 供货商等级 */
    $sql = "select rank_name from " . $ecs->table('supplier_rank') . " where rank_id = " . $supplier['rank_id'];
    $rank_name = $db->getOne($sql);
    $supplier['rank_name'] = $rank_name;
    // $sql="select rank_id,rank_name from ". $ecs->table('supplier_rank') ." order by sort_order";
    //$supplier_rank=$db->getAll($sql);
    //$smarty->assign('supplier_rank', $supplier_rank);

    /* 店铺类型 */
    $sql = "select str_name from " . $ecs->table('street_category') . " where str_id = " . $supplier['type_id'];
    $type_name = $db->getOne($sql);
    $supplier['type_name'] = $type_name;

    $smarty->assign('ur_here', $_LANG['edit_supplier']);
// 	 $lang_supplier_list = $status=='1' ? $_LANG['supplier_list'] :  $_LANG['supplier_reg_list'];
//      $smarty->assign('action_link', array('href' => 'supplier.php?act=list', 'text' =>$lang_supplier_list ));
    if ($_REQUEST['status'] == '1') {
        $lang_supplier_list = $_LANG['supplier_list'];
        $smarty->assign('action_link', array('href' => 'supplier.php?act=list&stype=' . $_SESSION["supplier_stype"] . '&status=1', 'text' => $lang_supplier_list));
    } else {
        $lang_supplier_list = $_LANG['supplier_reg_list'];
        $smarty->assign('action_link', array('href' => 'supplier.php?act=list&stype=' . $_SESSION["supplier_stype"] . '', 'text' => $lang_supplier_list));
    }

    $smarty->assign('form_action', 'update');
    $smarty->assign('supplier', $supplier);
    /* 代码增加 By  www.68ecshop.com Start */
    // 商品等级
    $smarty->assign('rank_id', $supplier['rank_id']);
    $smarty->assign('supplier_rank_list', get_supplier_rank_list());
    /* 代码增加 By  www.68ecshop.com End */

    assign_query_info();

    $smarty->display('supplier_info.htm');
}

/* ------------------------------------------------------ */
//-- 查看供货商佣金日志
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'view') {
    /* 检查权限 */
    admin_priv($adminpriv);

    /* 查询 */
    $result = rebate_log_list();

    /* 模板赋值 */
    $smarty->assign('ur_here', '佣金日志记录'); // 当前导航

    $smarty->assign('full_page', 1); // 翻页参数

    $smarty->assign('log_list', $result['result']);
    $smarty->assign('filter', $result['filter']);
    $smarty->assign('record_count', $result['record_count']);
    $smarty->assign('page_count', $result['page_count']);
    $smarty->assign('sort_suppliers_id', '<img src="images/sort_desc.gif">');

    /* 显示模板 */
    assign_query_info();
    $smarty->display('supplier_log_list.htm');
}

/* ------------------------------------------------------ */
//-- 排序、分页、查询
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'query_log') {
    check_authz_json($adminpriv);

    $result = rebate_log_list();

    $smarty->assign('log_list', $result['result']);
    $smarty->assign('filter', $result['filter']);
    $smarty->assign('record_count', $result['record_count']);
    $smarty->assign('page_count', $result['page_count']);
    ;

    make_json_result($smarty->fetch('supplier_log_list.htm'), '', array('filter' => $result['filter'], 'page_count' => $result['page_count']));
}

/* ------------------------------------------------------ */
//-- 提交添加、编辑供货商
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'update') {
    /* 检查权限 */
    admin_priv($adminpriv);

    //审核通过，必须要填写的项目
    /* 代码删除 By www.68ecshop.com Start */
//    if(intval($_POST['status']) == 1){
//        if(intval($_POST['supplier_rebate_paytime'])<=0){
//            sys_msg('结算类型必须选择！');
//        }
//    }
    /* 代码删除 By www.68ecshop.com End */

    /* 提交值 */
    $supplier_id = intval($_POST['id']);
    $status_url = intval($_POST['status_url']);
    $supplier = array(
        //'rank_id'   => intval($_POST['rank_id']),
        //'country'   => intval($_POST['country']),
        //'province'   => intval($_POST['province']),
        //'city'   => intval($_POST['city']),
        //'district'   => intval($_POST['district']),
        //'address'   => trim($_POST['address']),
        //'tel'   => trim($_POST['tel']),
        //'email'   => trim($_POST['email']),
        //'contact_back'   => trim($_POST['contact_back']),
        //'contact_shop'   => trim($_POST['contact_shop']),
        //'contact_yunying'   => trim($_POST['contact_yunying']),
        //'contact_shouhou'   => trim($_POST['contact_shouhou']),
        //'contact_caiwu'   => trim($_POST['contact_caiwu']),
        //'contact_jishu'   => trim($_POST['contact_jishu']),

        'bank_account_name' => trim($_POST['bank_account_name']),
        'bank_account_number' => trim($_POST['bank_account_number']),
        'bank_name' => trim($_POST['bank_name']),
        'bank_code' => trim($_POST['bank_code']),
        'settlement_bank_account_name' => trim($_POST['settlement_bank_account_name']),
        'settlement_bank_account_number' => trim($_POST['settlement_bank_account_number']),
        'settlement_bank_name' => trim($_POST['settlement_bank_name']),
        'settlement_bank_code' => trim($_POST['settlement_bank_code']),
        /* 代码增加 By  www.68ecshop.com Start */
        'rank_id' => $_POST['rank_id'],
        /* 代码增加 By  www.68ecshop.com End */
        'system_fee' => trim($_POST['system_fee']),
        'supplier_bond' => trim($_POST['supplier_bond']),
        'supplier_rebate' => trim($_POST['supplier_rebate']),
        'supplier_rebate_paytime' => intval($_POST['supplier_rebate_paytime']),
        'supplier_remark' => trim($_POST['supplier_remark']),
        'status' => intval($_POST['status'])
    );
    /* 代码增加_start  By  supplier.68ecshop.com */
    /* 取得供货商信息 */
    //$sql = "SELECT * FROM " . $ecs->table('supplier') . " WHERE supplier_id = '" . $supplier_id ."' ";
    $sql = "select s.supplier_id,s.add_time,s.status,u.* from " . $ecs->table('supplier') . " as s left join " . $ecs->table('users') .
            " as u on s.user_id=u.user_id where s.supplier_id=" . $supplier_id;
    $supplier_old = $db->getRow($sql);
    if (empty($supplier_old['supplier_id'])) {
        sys_msg('该供货商信息不存在！');
    }
    if (empty($supplier_old['add_time']) && $supplier['status'] == 1) {
        //审核通过时就是店铺创建成功的时间
        $supplier['add_time'] = time();
    }

    //操作店铺商品与店铺街信息
    if ($supplier['status'] != $supplier_old['status'] && $supplier['status'] == -1) {
        //审核不通过
        //店铺街信息失效
        $check_info = array(
            'is_groom' => 0,
            'is_show' => 0,
            'supplier_notice' => '',
            'status' => 0
        );
        $db->autoExecute($ecs->table('supplier_street'), $check_info, 'UPDATE', "supplier_id = '" . $supplier_id . "'");
        //商品下架
        $good_info = array(
            'is_on_sale' => 0
        );
        $db->autoExecute($ecs->table('goods'), $good_info, 'UPDATE', "supplier_id = '" . $supplier_id . "'");
        //删除店铺所在的标签
        $db->query('delete FROM ' . $ecs->table('supplier_tag_map') . ' WHERE supplier_id = ' . $supplier_id);
    }

    //更新相关店铺的管理员状态
    $sql = "select * from " . $ecs->table('supplier_admin_user') . " where supplier_id=" . $supplier_old['supplier_id'];
    $info = $db->getAll($sql);
    if (count($info) > 0) {
        $sql = "UPDATE " . $ecs->table('supplier_admin_user') . " SET user_name = '" . $supplier_old['user_name'] . "',password = '" . $supplier_old['password'] . "',email='" . $supplier_old['email'] . "',ec_salt='" . $supplier_old['ec_salt'] . "', checked = " . intval($_POST['status']) . " WHERE supplier_id=" . $supplier_old['supplier_id'] . " and uid=" . $supplier_old['user_id'];
        $db->query($sql);
    } else {
        $insql = "INSERT INTO " . $ecs->table('supplier_admin_user') . " (`uid`, `user_name`, `email`, `password`, `ec_salt`, `add_time`, `last_login`, `last_ip`, `action_list`, `nav_list`, `lang_type`, `agency_id`, `supplier_id`, `todolist`, `role_id`, `checked`) " .
                "VALUES(" . $supplier_old['user_id'] . ", '" . $supplier_old['user_name'] . "', '" . $supplier_old['email'] . "', '" . $supplier_old['password'] . "', '" . $supplier_old['ec_salt'] . "', " . $supplier_old['last_login'] . ", " . $supplier_old['last_login'] . ", '" . $supplier_old['last_ip'] . "', 'all', '', '', 0, " . $supplier_old['supplier_id'] . ", NULL, NULL, " . intval($_POST['status']) . ")";
        $db->query($insql);
    }
    /* 代码增加_end  By  supplier.68ecshop.com */

    /* 保存供货商信息 */
    $db->autoExecute($ecs->table('supplier'), $supplier, 'UPDATE', "supplier_id = '" . $supplier_id . "'");

    if ($_POST['status'] != '1') {
        $sql = "update " . $ecs->table('goods') . " set is_on_sale=0 where supplier_id='$supplier_id' ";
        $db->query($sql);
    }

    /* 清除缓存 */
    clear_cache_files();

    /* 提示信息 */
    $links[] = array('href' => ($status_url > 0 ? 'supplier.php?act=list&status=1' : 'supplier.php?act=list'), 'text' => ($status_url > 0 ? $_LANG['back_supplier_list'] : $_LANG['back_supplier_reg']));
    sys_msg($_LANG['edit_supplier_ok'], 0, $links);
}

//删除店铺信息
elseif ($_REQUEST['act'] == 'delete') {
    /* 检查权限 */
    admin_priv($adminpriv);
    $supplier_id = intval($_GET['id']);
   
    $sql = "SELECT * FROM " . $ecs->table('supplier') . " WHERE supplier_id = " . $supplier_id;
    $supplier = $db->getRow($sql);
    if (count($supplier) <= 0) {
        sys_msg('该供应商不存在！');
    }

    //周:注释掉了,  查询是否存在
//     $rs = $GLOBALS['db'] ->getRow(' select * from   ' . $ecs->table('supplier_admin_user') . ' WHERE supplier_id = ' . $supplier_id );
//     if ($rs)
//     {
//         sys_msg('该会员已存在业务记录无法删除！');
//         exit;
//     }
    
    

    if ($supplier_id > 0) {
        $ret = array();
        //入驻商相关删除信息
        $supplier_info = array(
            'delete FROM ' . $ecs->table('supplier_admin_user') . ' WHERE supplier_id = ' . $supplier_id,
            'delete FROM ' . $ecs->table('supplier_article') . ' WHERE supplier_id = ' . $supplier_id,
            'delete FROM ' . $ecs->table('supplier_category') . ' WHERE supplier_id = ' . $supplier_id,
            'delete FROM ' . $ecs->table('supplier_cat_recommend') . ' WHERE supplier_id = ' . $supplier_id,
            'delete FROM ' . $ecs->table('supplier_goods_cat') . ' WHERE supplier_id = ' . $supplier_id,
            'delete FROM ' . $ecs->table('supplier_guanzhu') . ' WHERE supplierid = ' . $supplier_id,
            'delete FROM ' . $ecs->table('supplier_money_log') . ' WHERE supplier_id = ' . $supplier_id,
            'delete FROM ' . $ecs->table('supplier_nav') . ' WHERE supplier_id = ' . $supplier_id,
            /* 代码删除 By  www.68ecshop.com Start */
//            'delete FROM '.$ecs->table('supplier_rebate_log').' WHERE rebateid in (SELECT rebate_id FROM '.$ecs->table('supplier_rebate').' WHERE supplier_id ='.$supplier_id.')',
            /* 代码删除 By  www.68ecshop.com End */
            'delete FROM ' . $ecs->table('supplier_shop_config') . ' WHERE supplier_id = ' . $supplier_id,
            'delete FROM ' . $ecs->table('supplier_street') . ' WHERE supplier_id = ' . $supplier_id,
            'delete FROM ' . $ecs->table('supplier_tag_map') . ' WHERE supplier_id = ' . $supplier_id
        );
        /* 代码增加 By  www.68ecshop.com Start */
//        if($db->getOne('SELECT COUNT(*) FROM ' . $ecs->table('supplier_rebate') . ' WHERE supplier_id = ' . $supplier_id))
//        {
//            array_push($supplier_info, 'delete FROM '.$ecs->table('supplier_rebate_log').' WHERE rebateid in (SELECT rebate_id FROM '.$ecs->table('supplier_rebate').' WHERE supplier_id ='.$supplier_id.')');
//        }
        /* 代码增加 By  www.68ecshop.com End */
        foreach ($supplier_info as $sk => $sv) {
            if ($db->query($sv)) {
                
            } else {
                $ret[] = $sv;
            }
        }
        delete_supplier_pic($supplier_id);
        //商品相关删除信息
        $goods_info = array(
            'delete FROM ' . $ecs->table('goods_activity') . ' WHERE goods_id in (SELECT goods_id FROM ' . $ecs->table('goods') . ' WHERE supplier_id =' . $supplier_id . ')',
            'delete FROM ' . $ecs->table('goods_attr') . ' WHERE goods_id in (SELECT goods_id FROM ' . $ecs->table('goods') . ' WHERE supplier_id =' . $supplier_id . ')',
            'delete FROM ' . $ecs->table('goods_cat') . ' WHERE goods_id in (SELECT goods_id FROM ' . $ecs->table('goods') . ' WHERE supplier_id =' . $supplier_id . ')',
            'delete FROM ' . $ecs->table('goods_gallery') . ' WHERE goods_id in (SELECT goods_id FROM ' . $ecs->table('goods') . ' WHERE supplier_id =' . $supplier_id . ')',
            'delete FROM ' . $ecs->table('goods_tag') . ' WHERE goods_id in (SELECT goods_id FROM ' . $ecs->table('goods') . ' WHERE supplier_id =' . $supplier_id . ')',
            'delete FROM ' . $ecs->table('products') . ' WHERE goods_id in (SELECT goods_id FROM ' . $ecs->table('goods') . ' WHERE supplier_id =' . $supplier_id . ')'
        );
        foreach ($goods_info as $gk => $gv) {
            if ($db->query($gv)) {
                
            } else {
                $ret[] = $gv;
            }
        }
        //最后删除中间表信息
        $other_info = array(
            'delete FROM ' . $ecs->table('goods') . ' WHERE supplier_id = ' . $supplier_id,
            'delete FROM ' . $ecs->table('supplier') . ' WHERE supplier_id = ' . $supplier_id
//            'delete FROM '.$ecs->table('supplier_rebate').' WHERE supplier_id = '.$supplier_id
        );
        foreach ($other_info as $ok => $ov) {
            if ($db->query($ov)) {
                
            } else {
                $ret[] = $ov;
            }
        }
    }
    if (count($ret) > 0) {
        echo "如下删除语句执行失败:";
        echo "<pre>";
        print_r($ret);
        sleep(10);
    }

    /* 提示信息 */
    $links[0] = array('href' => 'supplier.php?act=list&stype=' . $_SESSION["supplier_stype"] . '&status=' . $supplier['status'], 'text' => '返回上一页');
    sys_msg('删除成功!', 0, $links);
}

/* 代码增加 By  www.68ecshop.com Start */
/* ------------------------------------------------------ */
//-- 批量导出供货商
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'export') {
    $where = " WHERE s.applynum = 3 AND s.status = 1 ";

    // 入驻商名称
    if (isset($_REQUEST['supplier_name']) && !empty($_REQUEST['supplier_name'])) {
        $where .= " AND s.supplier_name LIKE '%" . mysql_like_quote($_REQUEST['supplier_name']) . "%'";
    }
    // 入驻商等级
    if (isset($_REQUEST['rank_id']) && !empty($_REQUEST['rank_id'])) {
        $where .= " AND s.rank_id = " . $_REQUEST['rank_id'];
    }

    /* 查询 */
    $sql = "SELECT "
            . "u.user_name, " // 会员名称
            . "s.supplier_name, " // 入驻商名称
            . "s.rank_id, " // 入驻商等级
            . "s.tel, " // 公司电话
            . "s.system_fee, " // 平台使用费
            . "s.supplier_bond, " // 商家保证金
            . "s.supplier_rebate, " // 分成利率
            . "s.supplier_remark, " // 入驻商备注
            . "s.status " // 状态
            . "FROM "
            . $GLOBALS['ecs']->table("supplier") . " AS s LEFT JOIN "
            . $GLOBALS['ecs']->table("users") . " AS u ON s.user_id = u.user_id "
            . $where;

    $res = $GLOBALS['db']->getAll($sql);

    // 引入phpexcel核心类文件
    require_once ROOT_PATH . '/includes/phpexcel/Classes/PHPExcel.php';
    // 实例化excel类
    $objPHPExcel = new PHPExcel();
    // 操作第一个工作表
    $objPHPExcel->setActiveSheetIndex(0);
    // 设置sheet名
    $objPHPExcel->getActiveSheet()->setTitle('入驻商列表');
    // 设置表格宽度
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
    // 列名表头文字加粗
    $objPHPExcel->getActiveSheet()->getStyle('A1:I1')->getFont()->setBold(true);
    // 列表头文字居中
    $objPHPExcel->getActiveSheet()->getStyle('A1:I1')->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // 列名赋值
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '会员名称');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '入驻商名称');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '入驻商等级');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '公司电话');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '平台使用费');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '商家保证金');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', '分成利率');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', '入驻商备注');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', '状态');

    // 数据起始行
    $row_num = 2;
    // 向每行单元格插入数据
    foreach ($res as $value) {
        // 入驻商等级
        switch ($value['rank_id']) {
            case 1:
                $rank_name = '初级店铺';
                break;
            case 2:
                $rank_name = '中级店铺';
                break;
            case 3:
                $rank_name = '高级店铺';
                break;
            default:
                $rank_name = '';
        }

        // 设置所有垂直居中
        $objPHPExcel->getActiveSheet()->getStyle('A' . $row_num . ':' . 'I' . $row_num)->getAlignment()
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        // 设置平台使用费和商家保证金为数字格式
        $objPHPExcel->getActiveSheet()->getStyle('E' . $row_num . ':' . 'F' . $row_num)->getNumberFormat()
                ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
        // 设置分成利率为数字格式
        $objPHPExcel->getActiveSheet()->getStyle('G' . $row_num)->getNumberFormat()
                ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

        // 设置单元格数值
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('A' . $row_num, $value['user_name'], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $row_num, $value['supplier_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $row_num, $rank_name);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('D' . $row_num, $value['tel'], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $row_num, $value['system_fee']);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $row_num, $value['supplier_bond']);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $row_num, $value['supplier_rebate']);
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $row_num, $value['supplier_remark']);
        $objPHPExcel->getActiveSheet()->setCellValue('I' . $row_num, $value['status'] ? '通过' : '');
        $row_num++;
    }
    $outputFileName = '入驻商_' . time() . '.xls';
    $xlsWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");
    header('Content-Disposition:inline;filename="' . $outputFileName . '"');
    header("Content-Transfer-Encoding: binary");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Pragma: no-cache");
    $xlsWriter->save("php://output");
    echo file_get_contents($outputFileName);
}
/* 代码增加 By  www.68ecshop.com End */ elseif ($_REQUEST["act"] == "checkUser") {
    $sql = "SELECT `user_id`,`is_bigfamily` FROM " . $GLOBALS['ecs']->table("users") . " WHERE mobile_phone='" . $_POST['user_mobile'] . "' limit 1";
    $is_bigfamily_row = $GLOBALS['db']->getRow($sql);
    if (empty($is_bigfamily_row)) {
        $role = -1;
    } else {
        $role = 4; //普通会员只能申请联盟商家
        if ($is_bigfamily_row["is_bigfamily"] > 0) {//大家庭人员可以入住线上商家
            $role = 3;
        }
    }
    echo $role;
}

/**
 *  获取供应商列表信息
 *
 * @access  public
 * @param
 *
 * @return void
 */
function suppliers_list() {
    $result = get_filter();
//    if ($result === false) {
    $aiax = isset($_GET['is_ajax']) ? $_GET['is_ajax'] : 0;

    /* 过滤信息 */
    $filter['supplier_name'] = empty($_REQUEST['supplier_name']) ? '' : trim($_REQUEST['supplier_name']);
    $filter['rank_name'] = empty($_REQUEST['rank_name']) ? '' : trim($_REQUEST['rank_name']);
    $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'supplier_id' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'ASC' : trim($_REQUEST['sort_order']);
    $filter['status'] = empty($_REQUEST['status']) ? '0' : intval($_REQUEST['status']);
    $filter['role'] = empty($_SESSION["supplier_stype"]) ? '' : intval($_SESSION["supplier_stype"]);

    $where = 'WHERE applynum = 3 ';
    $where .= $filter['status'] ? " AND s.status = '" . $filter['status'] . "' " : " AND s.status in('0','-1') ";
    if ($filter['role']) {
        $where .= " AND role = '" . $filter['role'] . "'";
    }

    if ($filter['supplier_name']) {
        $where .= " AND supplier_name LIKE '%" . mysql_like_quote($filter['supplier_name']) . "%'";
    }
    if ($filter['rank_name']) {
        $where .= " AND rank_id = '$filter[rank_name]'";
    }

    /* 分页大小 */
    $filter['page'] = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);

    if (isset($_REQUEST['page_size']) && intval($_REQUEST['page_size']) > 0) {
        $filter['page_size'] = intval($_REQUEST['page_size']);
    } elseif (isset($_COOKIE['ECSCP']['page_size']) && intval($_COOKIE['ECSCP']['page_size']) > 0) {
        $filter['page_size'] = intval($_COOKIE['ECSCP']['page_size']);
    } else {
        $filter['page_size'] = 15;
    }

    /* 记录总数 */
    $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('supplier') . " as s " . $where;
    var_dump($sql);
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);
    $filter['page_count'] = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;

    /* 查询 */
//        $sql = "SELECT supplier_id,u.user_name, rank_id, supplier_name, tel, system_fee, supplier_bond, supplier_rebate, supplier_remark,  " .
//                "s.status " .
//                "FROM " . $GLOBALS['ecs']->table("supplier") . " as s left join " . $GLOBALS['ecs']->table("users") . " as u on s.user_id = u.user_id
//                $where
//                ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order'] . "
//                LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'] . " ";

    $sql = "SELECT supplier_id,u.user_name, rank_id,u.zuodan, supplier_name, tel, system_fee, supplier_bond, supplier_rebate, supplier_remark,a.cat_name,u.mobile_phone,contacts_name,contacts_phone,role,  " .
            "s.status,c.mobile_phone as mobile_phone1,u.parent_id " .
            "FROM " . $GLOBALS['ecs']->table("supplier") . " as s left join " . $GLOBALS['ecs']->table("users") . " as u on s.user_id = u.user_id
                    LEFT  JOIN " . $GLOBALS['ecs']->table("category") . " as a on s.shop_category=a.cat_id 
                    LEFT  JOIN " . $GLOBALS['ecs']->table("users") . " as c on u.parent_id=c.user_id 
                $where
                ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order'] . "
                LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'] . " ";

    set_filter($filter, $sql);
//    } else {
//        $sql = $result['sql'];
//        $filter = $result['filter'];
//    }

    $rankname_list = array();
    $sql2 = "select * from " . $GLOBALS['ecs']->table("supplier_rank");
    $res2 = $GLOBALS['db']->query($sql2);
    while ($row2 = $GLOBALS['db']->fetchRow($res2)) {
        $rankname_list[$row2['rank_id']] = $row2['rank_name'];
    }

    $list = array();
    $res = $GLOBALS['db']->getAll($sql);
    foreach ($res as $row) {
        $row['rank_name'] = $rankname_list[$row['rank_id']];
        $row['status_name'] = $row['status'] == '1' ? '通过' : ($row['status'] == '0' ? "未审核" : "未通过");
        $open = $GLOBALS['db']->getRow("select value from " . $GLOBALS['ecs']->table("supplier_shop_config") . " where supplier_id=" . $row['supplier_id'] . " and code='shop_closed'");
        if ($open && $open['value'] == 0) {
            $row['open'] = 1;
        } else {
            $row['open'] = 0;
        }
        $list[] = $row;
    }
    $arr = array('result' => $list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

/*
 * 入驻商的佣金记录
 */

function rebate_log_list() {
    $result = get_filter();
    if ($result === false) {
        /* 过滤信息 */
        $filter['id'] = intval($_REQUEST['id']);
        $filter['addtime_start'] = !empty($_REQUEST['addtime_start']) ? local_strtotime($_REQUEST['addtime_start']) : 0;
        $filter['addtime_end'] = !empty($_REQUEST['addtime_end']) ? local_strtotime($_REQUEST['addtime_end'] . " 23:59:59") : 0;
        $filter['status'] = empty($_REQUEST['status']) ? '0' : intval($_REQUEST['status']);

        $where = ' WHERE supplier_id = ' . $filter['id'];
        $where .= $filter['addtime_start'] ? " AND addtime >= '" . $filter['addtime_start'] . "' " : " ";
        $where .= $filter['addtime_end'] ? " AND addtime_end <= '" . $filter['addtime_end'] . "' " : " ";
        $where .= $filter['status'] > 0 ? " AND status = '" . $filter['status'] . "' " : "";

        /* 分页大小 */
        $filter['page'] = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);

        if (isset($_REQUEST['page_size']) && intval($_REQUEST['page_size']) > 0) {
            $filter['page_size'] = intval($_REQUEST['page_size']);
        } elseif (isset($_COOKIE['ECSCP']['page_size']) && intval($_COOKIE['ECSCP']['page_size']) > 0) {
            $filter['page_size'] = intval($_COOKIE['ECSCP']['page_size']);
        } else {
            $filter['page_size'] = 15;
        }

        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('supplier_money_log') . $where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
        $filter['page_count'] = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;

        /* 查询 */
        $sql = "SELECT * " .
                "FROM " . $GLOBALS['ecs']->table("supplier_money_log") . $where . "
                ORDER BY addtime desc 
                LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'] . " ";

        set_filter($filter, $sql);
    } else {
        $sql = $result['sql'];
        $filter = $result['filter'];
    }

    $list = array();
    $res = $GLOBALS['db']->query($sql);
    while ($row = $GLOBALS['db']->fetchRow($res)) {
        $row['add_time'] = local_date("Y-m-d H:i:s", $row['addtime']);
        $list[] = $row;
    }

    $arr = array('result' => $list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

/*
  删除店铺下所有商品的上传的图片
 */

function delete_supplier_pic($suppid) {
    global $db, $ecs;

    $sql = "select goods_thumb,goods_img,original_img from " . $ecs->table('goods') . " where supplier_id=" . $suppid;

    $query = $db->query($sql);
    while ($row = $db->fetchRow($query)) {
        @unlink(ROOT_PATH . $row['goods_thumb']);
        @unlink(ROOT_PATH . $row['goods_img']);
        @unlink(ROOT_PATH . $row['original_img']);
    }

    $sql = "select gg.img_url,gg.thumb_url,gg.img_original from " . $ecs->table('goods_gallery') . " as gg," . $ecs->table('goods') . " as g where g.supplier_id=" . $suppid . " and g.goods_id=gg.goods_id";

    $query = $db->query($sql);
    while ($row = $db->fetchRow($query)) {
        @unlink(ROOT_PATH . $row['img_url']);
        @unlink(ROOT_PATH . $row['thumb_url']);
        @unlink(ROOT_PATH . $row['img_original']);
    }
}

/* 代码增加 By  www.68ecshop.com Start */

/**
 * 取得店铺等级列表
 * @return array 店铺等级列表 id => name
 */
function get_supplier_rank_list() {
    $sql = 'SELECT rank_id, rank_name FROM ' . $GLOBALS['ecs']->table('supplier_rank') . ' ORDER BY sort_order';
    $res = $GLOBALS['db']->getAll($sql);

    $rank_list = array();
    foreach ($res AS $row) {
        $rank_list[$row['rank_id']] = addslashes($row['rank_name']);
    }

    return $rank_list;
}

/* 代码增加 By  www.68ecshop.com End */
?>