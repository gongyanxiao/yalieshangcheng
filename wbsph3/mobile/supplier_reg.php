<?php

/**
 * ECSHOP 商家入驻
 * ============================================================================
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

assign_template();

$action = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : 'default';
$smarty->assign('action', $action);

if ($_SESSION["user_id"] * 1 <= 0) {
    show_message("请登录后在进行操作", '去登陆', 'user.php', 'info');
}


$userid = $_SESSION['user_id'];

$shownum = (isset($_REQUEST['shownum'])) ? intval($_REQUEST['shownum']) : 0;

$upload_size_limit = $_CFG['upload_size_limit'] == '-1' ? ini_get('upload_max_filesize') : $_CFG['upload_size_limit'];

 

if ($action == 'default') {
    $id = 0;
    if (!empty($_GET['id'])) {
        $id = $_GET["id"];
    } else {
        $sql = "SELECT * FROM " . $GLOBALS['ecs']->table("supplier") . " WHERE user_id='" . intval($_SESSION["user_id"]) . "' LIMIT 1";
        $supplier = $GLOBALS['db']->getRow($sql);
        $smarty->assign('supplier', $supplier);
    }
    /* 店铺类型 */
    $sql = "select cat_id,cat_name from " . $ecs->table('category') . "  where parent_id=0 ";
    $categories = $db->getAll($sql);
    $smarty->assign('categories', $categories);
    $smarty->assign('piclimit', $upload_size_limit);
    if (!empty($id)) {
        $sql = "select * from " . $GLOBALS['ecs']->table("supplier") . " where user_id=" . intval($_SESSION['user_id']) . " and supplier_id=" . $id;
        $supplier = $GLOBALS['db']->getRow($sql);
        $smarty->assign('supplier', $supplier);
    }
    $smarty->assign('userid', intval($_SESSION['user_id']));
    $smarty->assign('act', $act);
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    include_once (ROOT_PATH . 'includes/lib_transaction.php');
    include_once (ROOT_PATH . 'languages/' . $_CFG['lang'] . '/shopping_flow.php');
    $smarty->assign('lang', $_LANG);

    /* 取得国家列表、商店所在国家、商店所在国家的省列表 */
    $smarty->assign('country_list', get_regions());
    $smarty->assign('shop_province_list', get_regions(1, $_CFG['shop_country']));


    // 取得国家列表，如果有收货人列表，取得省市区列表
    $supplier['country'] = isset($supplier['country']) ? intval($supplier['country']) : 1;

    $supplier['province'] = isset($supplier['province']) ? intval($supplier['province']) : -1;
    $supplier['city'] = isset($supplier['city']) ? intval($supplier['city']) : -1;
    $supplier['district'] = isset($supplier['district']) ? intval($supplier['district']) : -1;

//    if (!empty($supplier["supplier_id"])) {
    $province_list = get_regions_wap($supplier['country']);
    $city_list = get_regions_wap($supplier['province']);
    $district_list = get_regions_wap($supplier['city']);
    $xiangcun_list = get_regions_wap($supplier['district']);
//    }
    // 赋值于模板
    $smarty->assign("page_title", "商家入驻申请");
    $smarty->assign('shop_country', $_CFG['shop_country']);
    $smarty->assign('shop_province', get_regions(1, $_CFG['shop_country']));
    $smarty->assign('province_list', $province_list);
    $smarty->assign('city_list', $city_list);
    $smarty->assign('district_list', $district_list);
    $smarty->assign('xiangcun_list', $xiangcun_list);
    $smarty->assign('currency_format', $_CFG['currency_format']);
    $smarty->assign('integral_scale', $_CFG['integral_scale']);
    $smarty->assign('name_of_region', array(
        $_CFG['name_of_region_1'], $_CFG['name_of_region_2'], $_CFG['name_of_region_3'], $_CFG['name_of_region_4']
    ));
    
    $sql = "SELECT `is_bigfamily` FROM " . $GLOBALS['ecs']->table("users") . " WHERE user_id='" . $_SESSION["user_id"] . "' limit 1";
    $is_bigfamily = $GLOBALS['db']->getOne($sql);
    $GLOBALS["smarty"]->assign("is_bigfamily", $is_bigfamily);
    
 
    $smarty->display('supplier_reg.dwt');
}


if ($action == "act_dos") {//商家提交注册
    $save = array();
    $save['supplier_name'] = isset($_POST['supplier_name']) ? trim(addslashes(htmlspecialchars($_POST['supplier_name']))) : '';

    //获取数据库 地区ID
    $province_post = isset($_POST['province']) ? trim(addslashes(htmlspecialchars($_POST['province']))) : '';
    $city_post = isset($_POST['city']) ? trim(addslashes(htmlspecialchars($_POST['city']))) : '';
    $district_post = isset($_POST['district']) ? trim(addslashes(htmlspecialchars($_POST['district']))) : '';

    //省
    $sql_province = " SELECT `region_id`  FROM " . $GLOBALS['ecs']->table('region') . " WHERE `region_name` LIKE '%" . $province_post . "%' ";
    $sql_province_id = $db->getOne($sql_province);
    //市
    $sql_city = " SELECT `region_id`  FROM " . $GLOBALS['ecs']->table('region') . " WHERE `region_name` LIKE '%" . $city_post . "%' ";
    $sql_city_id = $db->getOne($sql_city);
    //县
    $sql_district = " SELECT `region_id`  FROM " . $GLOBALS['ecs']->table('region') . " WHERE `region_name` LIKE '%" . $district_post . "%' ";
    $sql_district_id = $db->getOne($sql_district);
//    $sql_province_id = $province_post;
//    $sql_city_id = $city_post;
//    $sql_district_id = $district_post;
    $save['country'] = isset($_POST['country']) ? intval($_POST['country']) : 1;
    if (empty($_POST['sid'])) {
//        $save['province'] = $sql_province_id ? $sql_province_id : 1;
//        $save['city'] = $sql_city_id ? $sql_city_id : 1;
//        $save['district'] = $sql_district_id ? $sql_district_id : 1;
        /* 20170414修改定位信息开始 */
        $save['country'] = isset($_POST['country']) ? intval($_POST['country']) : 1;
        $save['province'] = isset($_POST['province']) ? intval($_POST['province']) : 0;
        $save['city'] = isset($_POST['city']) ? intval($_POST['city']) : 0;
        $save['district'] = isset($_POST['district']) ? intval($_POST['district']) : 0;
        if ($save['province'] == 0 || $save['city'] == 0) {
            $err->add('商家所属地区不合法！');
            $err->show($_LANG['back_up_page']);
            exit;
        }
        /* 20170414修改定位信息 */
    }
    $save['address'] = isset($_POST['address']) ? trim(addslashes(htmlspecialchars($_POST['address']))) : '';
    $save['contacts_name'] = isset($_POST['contacts_name']) ? trim(addslashes(htmlspecialchars($_POST['contacts_name']))) : '';
    $save['contacts_phone'] = isset($_POST['contacts_phone']) ? trim(addslashes(htmlspecialchars($_POST['contacts_phone']))) : '';
    $save['shop_category'] = isset($_POST['shop_category']) ? trim(addslashes(htmlspecialchars($_POST['shop_category']))) : '';
    $save['business_sphere'] = isset($_POST['business_sphere']) ? trim(addslashes(htmlspecialchars($_POST['business_sphere']))) : '';
    $save['wx'] = isset($_POST['wx']) ? trim(addslashes(htmlspecialchars($_POST['wx']))) : '';


    if (empty($save['supplier_name'])) {
        $err->add('商家名称不能为空！');
        $err->show($_LANG['back_up_page']);
        exit;
    }
    if (empty($_POST['sid']) && (empty($save['country']) || empty($save['province']) || empty($save['city']))) {
        $err->add('商家所属地区不合法！');
        $err->show($_LANG['back_up_page']);
        exit;
    }
    if (empty($save['address'])) {
        $err->add('详细地址不能为空！');
        $err->show($_LANG['back_up_page']);
        exit;
    }
    if (empty($save['contacts_name'])) {
        $err->add('联系人不能为空！');
        $err->show($_LANG['back_up_page']);
        exit;
    }
    if (empty($save['contacts_phone'])) {
        $err->add('联系人手机号不能为空！');
        $err->show($_LANG['back_up_page']);
        exit;
    }
    if (empty($save['shop_category'])) {
        $err->add('所属分类不能为空！');
        $err->show($_LANG['back_up_page']);
        exit;
    }
    if (empty($save['business_sphere'])) {
        $err->add('经营范围不能为空！');
        $err->show($_LANG['back_up_page']);
        exit;
    }
    $upload_size_limit =900;//限制图片大小为900k.
    if (isset($_FILES['shop_logo']) && $_FILES['shop_logo']['tmp_name'] != '' && isset($_FILES['shop_logo']['tmp_name']) && $_FILES['shop_logo']['tmp_name'] != 'none') {
        if ($_FILES['shop_logo']['size'] / 1024 > $upload_size_limit) { 
            $err->add(sprintf($_LANG['upload_file_limit'], $upload_size_limit));
            $err->show($_LANG['back_up_page']);
        }
        $shop_logo_img = upload_file($_FILES['shop_logo'], 'supplier');
        if ($shop_logo_img === false) {
            $err->add('商家图片上传失败！');
            $err->show($_LANG['back_up_page']);
        } else {
            $save['shop_logo'] = "/data/supplier/" . $shop_logo_img;
        }
    }

    if (isset($_FILES['zhizhao']) && $_FILES['zhizhao']['tmp_name'] != '' && isset($_FILES['zhizhao']['tmp_name']) && $_FILES['zhizhao']['tmp_name'] != 'none') {
        if ($_FILES['zhizhao']['size'] / 1024 > $upload_size_limit) {
            $err->add(sprintf($_LANG['upload_file_limit'], $upload_size_limit));
            $err->show($_LANG['back_up_page']);
        }
        $zhizhao_img = upload_file($_FILES['zhizhao'], 'supplier');
        if ($zhizhao_img === false) {
            $err->add('营业执照号电子版图片上传失败！');
            $err->show($_LANG['back_up_page']);
        } else {
            $save['zhizhao'] = "/data/supplier/" . $zhizhao_img;
        }
    }

    if (isset($_FILES['shop_pics1']) && $_FILES['shop_pics1']['tmp_name'] != '' && isset($_FILES['shop_pics1']['tmp_name']) && $_FILES['shop_pics1']['tmp_name'] != 'none') {
        if ($_FILES['shop_pics1']['size'] / 1024 > $upload_size_limit) {
            $err->add(sprintf($_LANG['upload_file_limit'], $upload_size_limit));
            $err->show($_LANG['back_up_page']);
        }
        $shop_pics1_img = upload_file($_FILES['shop_pics1'], 'supplier');
        if ($shop_pics1_img === false) {
            $err->add('商家店铺图片上传失败！');
            $err->show($_LANG['back_up_page']);
        } else {
            $save['shop_pics1'] = "/data/supplier/" . $shop_pics1_img;
        }
    }
    if (isset($_FILES['shop_pics2']) && $_FILES['shop_pics2']['tmp_name'] != '' && isset($_FILES['shop_pics2']['tmp_name']) && $_FILES['shop_pics2']['tmp_name'] != 'none') {
        if ($_FILES['shop_pics2']['size'] / 1024 > $upload_size_limit) {
            $err->add(sprintf($_LANG['upload_file_limit'], $upload_size_limit));
            $err->show($_LANG['back_up_page']);
        }
        $shop_pics2_img = upload_file($_FILES['shop_pics2'], 'supplier');
        if ($shop_pics2_img === false) {
            $err->add('商家店铺图片上传失败！');
            $err->show($_LANG['back_up_page']);
        } else {
            $save['shop_pics2'] = "/data/supplier/" . $shop_pics2_img;
        }
    }
    if (isset($_FILES['shop_pics3']) && $_FILES['shop_pics3']['tmp_name'] != '' && isset($_FILES['shop_pics3']['tmp_name']) && $_FILES['shop_pics3']['tmp_name'] != 'none') {
        if ($_FILES['shop_pics3']['size'] / 1024 > $upload_size_limit) {
            $err->add(sprintf($_LANG['upload_file_limit'], $upload_size_limit));
            $err->show($_LANG['back_up_page']);
        }
        $shop_pics3_img = upload_file($_FILES['shop_pics3'], 'supplier');
        if ($shop_pics3_img === false) {
            $err->add('商家店铺图片上传失败！');
            $err->show($_LANG['back_up_page']);
        } else {
            $save['shop_pics3'] = "/data/supplier/" . $shop_pics3_img;
        }
    }
    if (isset($_FILES['shop_pics4']) && $_FILES['shop_pics4']['tmp_name'] != '' && isset($_FILES['shop_pics4']['tmp_name']) && $_FILES['shop_pics4']['tmp_name'] != 'none') {
        if ($_FILES['shop_pics4']['size'] / 1024 > $upload_size_limit) {
            $err->add(sprintf($_LANG['upload_file_limit'], $upload_size_limit));
            $err->show($_LANG['back_up_page']);
        }
        $shop_pics4_img = upload_file($_FILES['shop_pics4'], 'supplier');
        if ($shop_pics4_img === false) {
            $err->add('商家店铺图片上传失败！');
            $err->show($_LANG['back_up_page']);
        } else {
            $save['shop_pics4'] = "/data/supplier/" . $shop_pics4_img;
        }
    }
    if (isset($_FILES['shop_pics5']) && $_FILES['shop_pics5']['tmp_name'] != '' && isset($_FILES['shop_pics5']['tmp_name']) && $_FILES['shop_pics5']['tmp_name'] != 'none') {
        if ($_FILES['shop_pics5']['size'] / 1024 > $upload_size_limit) {
            $err->add(sprintf($_LANG['upload_file_limit'], $upload_size_limit));
            $err->show($_LANG['back_up_page']);
        }
        $shop_pics5_img = upload_file($_FILES['shop_pics5'], 'supplier');
        if ($shop_pics5_img === false) {
            $err->add('商家店铺图片上传失败！');
            $err->show($_LANG['back_up_page']);
        } else {
            $save['shop_pics5'] = "/data/supplier/" . $shop_pics5_img;
        }
    }

    $save['user_id'] = $userid;
    $save['supplier_rebate'] = 10;
    $save['applynum'] = 3;
    $save['add_time'] = gmtime();
    //$save['role'] = ($_POST['supp_type'] == 3 ? 3 : 4 );
    
    $sql = "SELECT `is_bigfamily` FROM " . $GLOBALS['ecs']->table("users") . " WHERE user_id='" . $_SESSION["user_id"] . "' limit 1";
    $is_bigfamily = $GLOBALS['db']->getOne($sql);
   
    $save['role'] = 4; //普通会员只能申请联盟商家
  
    if ($is_bigfamily > 0) {//大家庭人员可以入住线上商家
    	$save['role'] = 3;
    	//如果申请人是高级会员或代理, 那么店铺不需要审核, 直接通过
    	$save['status'] = 1;//周:店铺的状态为申请通过
    }else{
    	$save['status'] = 0;//周:店铺的状态为申请
    }
    

    
//     $save1 = array_filter($save);
//     if (count($save1) != count($save)) {
//         show_message('请认真填写必填申请资料！');
//     }
    
    
    $supplier_id = 0;
    $save['zuodan'] = 0;
 
    if (!empty($_POST['sid'])) {
        $supplier_id = $_POST['sid'];
        $sql = "select user_id,status from " . $GLOBALS['ecs']->table("supplier") . " where supplier_id=" . $supplier_id . " and user_id=" . intval($_SESSION['user_id']);
        $status = $GLOBALS['db']->getRow($sql);
        if (empty($status)) {
            show_message('申请信息不存在！');
        }
        if ($status['status'] * 1 === 1) {
            show_message('当前商家已经审核通过，不可更改！');
        }
    
        $isok = $db->autoExecute($ecs->table('supplier'), $save, "UPDATE", "supplier_id=" . $supplier_id);
    } else {
        $isok = $db->autoExecute($ecs->table('supplier'), $save);
    }
    
   
    if ($isok) {
    
    	$supplierid = $db->insert_id();
    	
    	//周:如果申请人是高级会员或代理, 那么店铺不需要审核,直接添加店铺管理角色
    	//查询是否已经有线上店铺账号, 如果没有,就添加, 有则不动
    	$checkSupplierAccountSQL=" select count(*) from  ".$GLOBALS['ecs']->table("supplier_admin_user")." where supplier_id=".$supplierid." and role=3";
    	$isHasCount = $GLOBALS['db']->getOne($checkSupplierAccountSQL);
    	if($isHasCount==0){
    	
    		$sql = "SELECT * from " . $GLOBALS['ecs']->table("users") . " where user_id='" . $save['user_id'] . "'";
    		
    		$user_info = $GLOBALS['db']->getRow($sql);
    		//设置其角色为线上商家
    		$insql = "INSERT INTO " . $ecs->table('supplier_admin_user') . " (`uid`, `user_name`, `email`, `password`, `ec_salt`, `add_time`, `last_login`, `last_ip`, `action_list`, `nav_list`, `lang_type`, `agency_id`, `supplier_id`, `todolist`, `role_id`, `checked`,`mobile_phone`,province_id,citys_id,district_id,role) " .
      		"VALUES(" . $user_info['user_id'] . ", '" . $user_info['user_name'] . "', '" . $user_info['email'] . "', '" . $user_info['password'] . "', '" .
    		$user_info['ec_salt'] . "',".gmtime(). ",'" . $user_info['last_login'] . "', '" . $user_info['last_ip'] . "', 'all', '', '', 0, " . $supplierid. ", NULL, NULL, 1 ,'" .
    		$user_info['mobile_phone'] . "'," . $save['province'] . "," . $save['city'] . "," . $save['district'] . ",3)";
    		$db->query($insql);
    	}
    	 
        
        
        $config_data = array("shop_name" => $save['supplier_name'], "shop_title" => $save['supplier_name'], "shop_desc" => $save['supplier_name'], "shop_keywords" => $save['supplier_name'], "shop_country" => $save['country'], "shop_province" => $save['province'], "shop_city" => $save['city'], 'shop_district' => $save['district'], "shop_address" => $save['address'], "shop_closed" => 0, 'template' => 'dianpu1');
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
            $sql = "DELETE FROM " . $GLOBALS['ecs']->table("supplier_shop_config") . " where supplier_id=" . $supplierid . " and code='" . $configKey . "'";
            $GLOBALS['db']->query($sql);

            $sql = "insert into " . $GLOBALS['ecs']->table("supplier_shop_config") . " (parent_id,code,type,store_range,store_dir,value,sort_order,supplier_id)" .
                    "   VALUES (1,'" . $configKey . "','" . $type . "','" . $store_range . "','','" . $configValue . "',1,'" . $supplierid . "')";
            $GLOBALS['db']->query($sql);
        }
        if (!empty($shop_logo_img)) {
            $sql = "DELETE FROM " . $GLOBALS['ecs']->table("supplier_shop_config") . " where supplier_id=" . $supplierid . " and code='shop_logo'";
            $GLOBALS['db']->query($sql);
            $sql = "insert into " . $GLOBALS['ecs']->table("supplier_shop_config") . " (parent_id,code,type,store_range,store_dir,value,sort_order,supplier_id)" .
                    "   VALUES (1,'shop_logo','file','','','" . $shop_logo_img . "',1,'" . $supplierid . "')";
            $GLOBALS['db']->query($sql);
        }
        if (!empty($zhizhao_img)) {
            $sql = "DELETE FROM " . $GLOBALS['ecs']->table("supplier_shop_config") . " where supplier_id=" . $supplierid . " and code='shop_zhizhao'";
            $GLOBALS['db']->query($sql);
            $sql = "insert into " . $GLOBALS['ecs']->table("supplier_shop_config") . " (parent_id,code,type,store_range,store_dir,value,sort_order,supplier_id)" .
                    "   VALUES (1,'shop_zhizhao','file','','','" . $zhizhao_img . "',1,'" . $supplierid . "')";
            $GLOBALS['db']->query($sql);
        }
        if (!empty($shop_pics1_img)) {
            $sql = "DELETE FROM " . $GLOBALS['ecs']->table("supplier_shop_config") . " where supplier_id=" . $supplierid . " and code='shop_pics1'";
            $GLOBALS['db']->query($sql);
            $sql = "insert into " . $GLOBALS['ecs']->table("supplier_shop_config") . " (parent_id,code,type,store_range,store_dir,value,sort_order,supplier_id)" .
                    "   VALUES (1,'shop_pics1','file','','','" . $shop_pics1_img . "',1,'" . $supplierid . "')";
            $GLOBALS['db']->query($sql);
        }
        if (!empty($shop_pics2_img)) {
            $sql = "DELETE FROM " . $GLOBALS['ecs']->table("supplier_shop_config") . " where supplier_id=" . $supplierid . " and code='shop_pics2'";
            $GLOBALS['db']->query($sql);
            $sql = "insert into " . $GLOBALS['ecs']->table("supplier_shop_config") . " (parent_id,code,type,store_range,store_dir,value,sort_order,supplier_id)" .
                    "   VALUES (1,'shop_pics2','file','','','" . $shop_pics2_img . "',1,'" . $supplierid . "')";
            $GLOBALS['db']->query($sql);
        }
        if (!empty($shop_pics3_img)) {
            $sql = "DELETE FROM " . $GLOBALS['ecs']->table("supplier_shop_config") . " where supplier_id=" . $supplierid . " and code='shop_pics3'";
            $GLOBALS['db']->query($sql);
            $sql = "insert into " . $GLOBALS['ecs']->table("supplier_shop_config") . " (parent_id,code,type,store_range,store_dir,value,sort_order,supplier_id)" .
                    "   VALUES (1,'shop_pics3','file','','','" . $shop_pics3_img . "',1,'" . $supplierid . "')";
            $GLOBALS['db']->query($sql);
        }
        if (!empty($shop_pics4_img)) {
            $sql = "DELETE FROM " . $GLOBALS['ecs']->table("supplier_shop_config") . " where supplier_id=" . $supplierid . " and code='shop_pics4'";
            $GLOBALS['db']->query($sql);
            $sql = "insert into " . $GLOBALS['ecs']->table("supplier_shop_config") . " (parent_id,code,type,store_range,store_dir,value,sort_order,supplier_id)" .
                    "   VALUES (1,'shop_pics4','file','','','" . $shop_pics4_img . "',1,'" . $supplierid . "')";
            $GLOBALS['db']->query($sql);
        }
        if (!empty($shop_pics5_img)) {
            $sql = "DELETE FROM " . $GLOBALS['ecs']->table("supplier_shop_config") . " where supplier_id=" . $supplierid . " and code='shop_pics5'";
            $GLOBALS['db']->query($sql);
            $sql = "insert into " . $GLOBALS['ecs']->table("supplier_shop_config") . " (parent_id,code,type,store_range,store_dir,value,sort_order,supplier_id)" .
                    "   VALUES (1,'shop_pics5','file','','','" . $shop_pics5_img . "',1,'" . $supplierid . "')";
            $GLOBALS['db']->query($sql);
        }
        /* 清除缓存 */
        clear_cache_files();
        show_message('操作成功！', "个人中心", "user.php", "info");
        exit;
    } else {
        show_message('操作失败！');
    }
}

function get_region_info_wap($region_id) {
    $sql = 'SELECT region_name FROM ' . $GLOBALS['ecs']->table('region') .
            " WHERE region_id = '$region_id' ";
    return $GLOBALS['db']->getOne($sql);
}

function get_regions_wap($region_id) {
    $sql = 'SELECT region_id,region_name FROM ' . $GLOBALS['ecs']->table('region') .
            " WHERE parent_id = '$region_id' ";
    return $GLOBALS['db']->getAll($sql);
}

?>