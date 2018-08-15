<?php

/**
 * ECSHOP 专题前台
 * ============================================================================
 * 版权所有 2005-2011 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * @author:     webboy <laupeng@163.com>
 * @version:    v2.1
 * ---------------------------------------------
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if ((DEBUG_MODE & 2) != 2) {
    $smarty->caching = true;
}

if (empty($_SESSION['user_id'])) {
    $back_act = "apply.php";
    if (!empty($_SERVER['QUERY_STRING'])) {
        $back_act = 'apply.php?' . strip_tags($_SERVER['QUERY_STRING']);
    }
    show_message('请先登陆！', array('返回上一页', '点击去登陆'), array($back_act, 'user.php'), 'info');
}

//if ($_SESSION['user_id'] * 1 > 0) {
//    $cuser_id = $_SESSION['user_id'];
//    $sql = "select count(*) from " . $GLOBALS['ecs']->table("region") . " where `user_id`='" . $cuser_id . "' ";
//    $supplier_id = $GLOBALS['db']->getOne($sql);
//    if ($supplier_id > 0) {
//        show_message('你已经是运营中心人员，无需申请商家！', '返回首页', '/', 'wrong');
//    }
//}


$userid = $_SESSION['user_id'];

$shownum = (isset($_REQUEST['shownum'])) ? intval($_REQUEST['shownum']) : 0;

$upload_size_limit = $_CFG['upload_size_limit'] == '-1' ? ini_get('upload_max_filesize') : $_CFG['upload_size_limit'];



if (isset($_POST['dos']) && !empty($_POST['dos'])) {
  
	$sql = "SELECT count(*) FROM " . $GLOBALS['ecs']->table("supplier") . " where user_id=" . $userid;
   $isExistSupplier = $GLOBALS['db']->getOne($sql);
   if ($isExistSupplier* 1 > 0) {
       show_message('该会员已经是商家，无法继续入驻商家！');
       exit;
   }
	
   
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

//    $save['country'] = isset($_POST['country']) ? intval($_POST['country']) : 1;
//    $save['province'] = $sql_province_id ? $sql_province_id : 1;
//    $save['city'] =  $sql_city_id ?  $sql_city_id  : 1;
//    $save['district'] = $sql_district_id ?  $sql_district_id  : 1;

    /* 20170414修改定位信息开始 */
    $save['country'] = isset($_POST['country']) ? intval($_POST['country']) : 1;
    $save['province'] = isset($_POST['province']) ? intval($_POST['province']) : 0;
    $save['city'] = isset($_POST['city']) ? intval($_POST['city']) : 0;
    $save['district'] = isset($_POST['district']) ? intval($_POST['district']) : 0;
    
    if ($save['province'] == 0 || $save['city'] == 0) {
        show_message('请选择所在地信息！', '返回', 'apply.php', 'wrong');
    }
    /* 20170414修改定位信息 */



    $save['address'] = isset($_POST['address']) ? trim(addslashes(htmlspecialchars($_POST['address']))) : '';
    $save['contacts_name'] = isset($_POST['contacts_name']) ? trim(addslashes(htmlspecialchars($_POST['contacts_name']))) : '';
    $save['contacts_phone'] = isset($_POST['contacts_phone']) ? trim(addslashes(htmlspecialchars($_POST['contacts_phone']))) : '';
    $save['shop_category'] = isset($_POST['shop_category']) ? trim(addslashes(htmlspecialchars($_POST['shop_category']))) : '';
    $save['business_sphere'] = isset($_POST['business_sphere']) ? trim(addslashes(htmlspecialchars($_POST['business_sphere']))) : '';
    $save['wx'] = isset($_POST['wx']) ? trim(addslashes(htmlspecialchars($_POST['wx']))) : '';

    //判断已经有申请
    $sql = "select count(*) as num from " . $GLOBALS['ecs']->table("supplier") . " where user_id=" . intval($_SESSION['user_id']);
    $num = $GLOBALS['db']->getRow($sql);
    if ($num['num'] > 0) {
        show_message('您已有申请信息,不能重复申请！', '返回', 'apply.php', 'wrong');
    }

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
    
    $save1 = array_filter($save);
    if (count($save1) != count($save)) {
        show_message('请认真填写必填申请资料！', '返回', 'apply.php', 'wrong');
    }
    $supplier_id = 0;
    $save['zuodan'] = 3;
   
    
    
    
    if (!empty($_POST['sid'])) {//周:有店铺id
        $supplier_id = $_POST['sid'];
        $sql = "select user_id,status from " . $GLOBALS['ecs']->table("supplier") . " where supplier_id=" . $supplier_id . " and user_id=" . intval($_SESSION['user_id']);
        $status = $GLOBALS['db']->getRow($sql);
        if (empty($status)) {
            show_message('申请信息不存在！', '返回', 'apply.php', 'wrong');
        }
        if ($status['status'] * 1 === 1) {
            show_message('当前商家已经审核通过，不可更改！', '返回', 'apply.php', 'wrong');
        }
        $isok = $db->autoExecute($ecs->table('supplier'), $save, "UPDATE", "supplier_id=" . $supplier_id);
    } else {
    	//添加一个店铺
        $isok = $db->autoExecute($ecs->table('supplier'), $save);
    }
    if ($isok) {
        $supplierid = $GLOBALS['db']->insert_id();
        if ($is_bigfamily > 0) {  //周:如果申请人是高级会员或代理, 那么店铺不需要审核,直接添加店铺管理角色
        	 //查询是否已经有线上店铺账号, 如果没有,就添加, 有则不动
        	$checkSupplierAccountSQL=" select count(*) from  ".$GLOBALS['ecs']->table("supplier_admin_user")." where supplier_id=".$supplierid." and role=3";
        	$isHasCount = $GLOBALS['db']->getOne($checkSupplierAccountSQL);
        	if($isHasCount==0){
        		$sql = "SELECT * from " . $GLOBALS['ecs']->table("users") . " where user_id='" . $save['user_id'] . "'";
        		$user_info = $GLOBALS['db']->getRow($sql);
        		//设置其角色为线上商家
        		$insql = "INSERT INTO " . $ecs->table('supplier_admin_user') . " (`uid`, `user_name`, `email`, `password`, `ec_salt`, `add_time`, `last_login`, `last_ip`, `action_list`, `nav_list`, `lang_type`, `agency_id`, `supplier_id`, `todolist`, `role_id`, `checked`,`mobile_phone`,province_id,citys_id,district_id,role) " .
          		"VALUES(" . $user_info['user_id'] . ", '" . $user_info['user_name'] . "', '" . $user_info['email'] . "', '" . $user_info['password'] . "', '" . $user_info['ec_salt'] . "',".gmtime(). ",'" . $user_info['last_login'] . "', '" . $user_info['last_ip'] . "', 'all', '', '', 0, " . $supplierid. ", NULL, NULL, 1 ,'" . $user_info['mobile_phone'] . "'," . $user_info['province'] . "," . $user_info['city'] . "," . $user_info['district'] . ",3)";
        		$db->query($insql);
        	}
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
        header("location:apply.php");
        exit;
    } else {
        show_message('操作失败！', '返回', 'apply.php', 'wrong');
    }
}

if (!$smarty->is_cached($templates, $cache_id)) {

    /* 模板赋值 */
    assign_template();
    $position = assign_ur_here();
    $smarty->assign('page_title', $position['title']);       // 页面标题
    $smarty->assign('ur_here', $position['ur_here'] . '> ' . $topic['title']);     // 当前位置
}

$act = $_GET["act"];
if (empty($act)) {
    $act = "list";
}

$sql = "SELECT `is_bigfamily` FROM " . $GLOBALS['ecs']->table("users") . " WHERE user_id='" . $_SESSION["user_id"] . "' limit 1";
$is_bigfamily = $GLOBALS['db']->getOne($sql);

if($is_bigfamily==0){
	show_message('申请店铺需要高级会员及以上资格.', '返回上一页', $_SERVER['HTTP_REFERER'], 'info');
}


if ($act == "add") {
    $id = 0;
    if (!empty($_GET['id'])) {
        $id = $_GET["id"];
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
    $supplier_country = $supplier['country'] ? $supplier['country'] : $_CFG['shop_country'];
    if (empty($supplier_country)) {
        $supplier_country = 1;
    }
    $GLOBALS['smarty']->assign('country_list', get_regions());
    $GLOBALS['smarty']->assign('province_list', get_regions(1, $supplier_country));
    $GLOBALS['smarty']->assign('city_list', get_regions(2, $supplier['province']));
    $GLOBALS['smarty']->assign('district_list', get_regions(3, $supplier['city']));
    $GLOBALS['smarty']->assign('supplier_country', $supplier_country);

    $sql = "SELECT `is_bigfamily` FROM " . $GLOBALS['ecs']->table("users") . " WHERE user_id='" . $_SESSION["user_id"] . "' limit 1";
    $is_bigfamily = $GLOBALS['db']->getOne($sql);
    $GLOBALS["smarty"]->assign("is_bigfamily", $is_bigfamily);
} else {
	
	
    $smarty->assign('act', $act);
    $sql = "select * from " . $GLOBALS['ecs']->table("supplier") . " where user_id=" . intval($_SESSION['user_id']);
    $suppliers = $GLOBALS['db']->getAll($sql);
    foreach ($suppliers as $sKey => $sValue) {
        //所在地
        if (!empty($suppliers[$sKey]['shop_address'])) {
            $suppliers[$sKey]['address'] = ',' . $suppliers[$sKey]['shop_address'];
        }
        if (!empty($suppliers[$sKey]['shop_city'])) {
            $suppliers[$sKey]['address'] = ',' . get_region_info($suppliers[$sKey]['shop_city']) . $suppliers[$sKey]['address'];
        }
        if (!empty($suppliers[$sKey]['shop_province'])) {
            $suppliers[$sKey]['address'] = get_region_info($suppliers[$sKey]['shop_province']) . $suppliers[$sKey]['address'];
        }
        $suppliers[$sKey]['address'] = trim($suppliers[$sKey]['address'], ',');

        $suppliers[$sKey]['add_time'] = local_date("Y-m-d G:i:s", $suppliers[$sKey]['add_time']);
        $suppliers[$sKey]['cat_name'] = $GLOBALS['db']->getOne("SELECT cat_name from " . $GLOBALS['ecs']->table("category") . " where cat_id='" . $suppliers[$sKey]['shop_category'] . "'");
    }
    $GLOBALS['smarty']->assign('suppliers', $suppliers);
}
$smarty->display('apply.dwt');
?>