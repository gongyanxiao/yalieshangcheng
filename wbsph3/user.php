<?php

/**
 * ECSHOP 会员中心
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: user.php 17217 2011-01-19 06:29:08Z liubo $
 */
define('IN_ECS', true);

require (dirname(__FILE__) . '/includes/init.php');

/* 载入语言文件 */
require_once (ROOT_PATH . 'languages/' . $_CFG['lang'] . '/user.php');

$user_id = $_SESSION['user_id'];
$action = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : 'default';

$affiliate = unserialize($GLOBALS['_CFG']['affiliate']);
$smarty->assign('affiliate', $affiliate);
$back_act = '';

// 不需要登录的操作或自己验证是否登录（如ajax处理）的act
$not_login_arr = array(
    'login', 'act_login', 'act_edit_password', 'get_password', 'send_pwd_email', 'password', 'signin', 'add_tag', 'collect', 're_collect', 'return_to_cart', 'book_goods', 'add_book_goods', 'logout', 'user_bonus', 'email_list', 'validate_email', 'send_hash_mail', 'order_query', 'is_registered', 'check_email', 'check_mobile_phone', 'clear_history', 'qpassword_name', 'get_passwd_question', 'check_answer', 'check_register', 'oath', 'oath_login', 'other_login', 'ch_email', 'ck_email', 'check_username', 'forget_password', 'getverifycode', 'step_1',
    /* 余额额支付密码_更改_START_www.68ecshop.com */
    'act_forget_pass', 're_pass', 'open_surplus_password', 'close_surplus_password'
);
/* 余额额支付密码_更改_END_www.68ecshop.com */

/* 显示页面的action列表 */
$ui_arr = array(
    'login', 'profile', 'order_list', 'order_detail', 'address_list', 'collection_list', 'follow_shop', 'message_list', 'tag_list', 'get_password', 'reset_password', 'booking_list', 'add_booking', 'account_raply', 'account_deposit', 'account_log', 'account_detail', 'act_account', 'pay', 'default', 'bonus', 'group_buy', 'group_buy_detail', 'affiliate', 'comment_list', 'validate_email', 'track_packages', 'transform_points', 'qpassword_name', 'get_passwd_question', 'check_answer', 'check_register', 'back_order', 'back_list', 'back_order_detail', 'back_order_act', 'back_replay', 'my_comment', 'my_comment_send', 'shaidan_send', 'shaidan_sale', 'account_security', 'act_identity', 'check_phone', 'update_password', 're_binding', 'update_phone', 'update_email', 'act_update_email',
    're_binding_email', 'ch_email', 'ck_email', 'step_1', 'forget_password', 'back_order_detail', 'del_back_order', 'back_order_detail_edit', 'add_huan_goods',
    /* 余额额支付密码_更改_START_www.68ecshop.com */
    'act_forget_pass', 're_pass', 'auction_list', 'forget_surplus_password', 'act_forget_surplus_password', 'update_surplus_password', 'act_update_surplus_password', 'verify_reset_surplus_email', 'get_verify_code',
    // 爱心互助
		'mutual', 'bigfamily', 'djtfh_points_desc', 'djtfh_raply', 'mutual_detail', 'check_djt', 'check_djt_tuijian','fen_hong_ji_fen_account_log'
); // 代码修改
// By
// www.68ecshop.com
/* 余额额支付密码_更改_END_www.68ecshop.com */

/* 代码增加_start By www.68ecshop.com */
$ui_arr[] = "supplier_reg";
/* 代码增加_end By www.68ecshop.com */
/* 代码增加_start By www.68ecshop.com */
$ui_arr[] = 'tg_login_act';
$ui_arr[] = 'tg_login';
$ui_arr[] = 'tg_order';
/* 代码增加_end By www.68ecshop.com */
/* 代码增加_start By www.68ecshop.com */
$ui_arr[] = 'vc_login_act';
$ui_arr[] = 'vc_login';
$ui_arr[] = 'update_password_success';
$ui_arr[] = 'to_update_password';
$not_login_arr[] = 'login_check_yzm';
/* 代码增加_end By www.68ecshop.com */
$not_login_arr[] = 'check_mobile';
$not_login_arr[] = 'send_email_code';
$not_login_arr[] = 'send_mobile_code';

$ui_arr[] = 'get_tc_express';
$ui_arr[] = "offline";
$ui_arr[] = "offline_detail";
$ui_arr[] = "checkuser";
$ui_arr[] = "checkje";
$ui_arr[] = "save_offline";
$ui_arr[] = "offline_order";
$ui_arr[] = 'order_static';
$ui_arr[] = 'supplier_static';
$ui_arr[] = 'messages';
$ui_arr[] = 'readmsg';
$ui_arr[] = 'delmsg';
$ui_arr[] = 'yl_apply';
$ui_arr[] = 'act_yl_apply';
$ui_arr[] = 'yl_list';
$ui_arr[] = 'insure';
$ui_arr[] = 'paylog_now';
$ui_arr[] = 'loan';
$ui_arr[] = 'getAreaAds';
$ui_arr[] = 'get_hong_bao';
$ui_arr[] = 'is_hong_bao';
$ui_arr[] = 'product_center';

/* 未登录处理 */
if (empty($_SESSION['user_id']) && $action != 're_validate_email' && $action != 'valid_email') {
    if (!in_array($action, $not_login_arr)) {
        if (in_array($action, $ui_arr)) {
            /*
             * 如果需要登录,并是显示页面的操作，记录当前操作，用于登录后跳转到相应操作
             * if ($action == 'login')
             * {
             * if (isset($_REQUEST['back_act']))
             * {
             * $back_act = trim($_REQUEST['back_act']);
             * }
             * }
             * else
             * {}
             */
            $query_string = $_SERVER['QUERY_STRING'];
            if (!empty($query_string)) {
                if (strpos($query_string, 'findPwd.php') != false) {
                    $query_string = 'index.php';
                }
                $back_act = 'user.php?' . strip_tags($query_string);
            }
            $action = 'login';
        } else {
            // 未登录提交数据。非正常途径提交数据！
            // die($_LANG['require_login']);
            show_message($_LANG['require_login'], array(
                '</br>登录', '</br>返回首页'
                    ), array(
                'user.php?act=login', $ecs->url()
                    ), 'error', false);
        }
    }
}

/* 限制某个会员登录 */
if ($user_id > 0) {
    $blacklist = check_user_is_blacklist($user_id);
    if (!empty($blacklist)) {
        $user->logout();
        show_message($blacklist['limit_desc']);
        exit;
    }
}

/* 如果是显示页面，对页面进行相应赋值 */
if (in_array($action, $ui_arr)) {

	assign_template();	
    $position = assign_ur_here(0, $_LANG['user_center']);
    $smarty->assign('page_title', $position['title']); // 页面标题
    $smarty->assign('ur_here', $position['ur_here']);
    $sql = "SELECT value FROM " . $ecs->table('shop_config') . " WHERE id = 419";
    $row = $db->getRow($sql);
    $car_off = $row['value'];
    $smarty->assign('car_off', $car_off);
    /* 是否显示积分兑换 */
    if (!empty($_CFG['points_rule']) && unserialize($_CFG['points_rule'])) {
        $smarty->assign('show_transform_points', 1);
    }
    $smarty->assign('helps', get_shop_help()); // 网店帮助
    $smarty->assign('data_dir', DATA_DIR); // 数据目录
    $smarty->assign('action', $action);
    $smarty->assign("cfg", $_CFG);
    $smarty->assign('lang', $_LANG);

//    $isCanZd = isCanToZd(false, true);
    $isCanZd = 0;
    $smarty->assign('isCanZd', $isCanZd);

    $isYunying = 0;
//    if ($isCanZd > 0) {
//        $isYunying = is_yunying();
//
//        $isYunying = $isYunying ? 1 : 0;
//    }

    $smarty->assign('isYunying', $isYunying);
}

/* 代码增加_Start By www.68ecshop.com  用户中心获取会员信息 */

include_once (ROOT_PATH . 'includes/lib_clips.php');
if ($rank = get_rank_info()) {
    $smarty->assign('rank_name', $rank['rank_name']);
    if (!empty($rank['next_rank_name'])) {
        $smarty->assign('next_rank_name', sprintf($_LANG['next_level'], $rank['next_rank'], $rank['next_rank_name']));
    }
    $rn = $rank['rank_name'];
    $recomm = $db->getOne("SELECT is_recomm FROM " . $GLOBALS['ecs']->table('user_rank') . " WHERE rank_name= '$rn'");
    $smarty->assign('recomm', $recomm); // 获取当前用户是否是分成用户判断是否显示我的推荐
}

/* 路由 */

$function_name = 'action_' . $action;

if (!function_exists($function_name)) {
    $function_name = "action_default";
}
setUserCenterValue();
call_user_func($function_name);

/* 路由 */

/* 代码增加_start By www.68ecshop.com */

function action_supplier_reg() {
    // 获取全局变量
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    $sql = "select * from " . $ecs->table('supplier') . " where user_id='" . $_SESSION['user_id'] . "' ";
    $supplier = $db->getRow($sql);

    $smarty->assign('supplier', $supplier);

    $supplier_country = $supplier['country'] ? $supplier['country'] : $_CFG['shop_country'];
    $smarty->assign('country_list', get_regions());
    $smarty->assign('province_list', get_regions(1, $supplier_country));
    $smarty->assign('city_list', get_regions(2, $supplier['province']));
    $smarty->assign('district_list', get_regions(3, $supplier['city']));
    $smarty->assign('supplier_country', $supplier_country);

    $sql = "select rank_id,rank_name from " . $ecs->table('supplier_rank') . " order by sort_order";
    $supplier_rank = $db->getAll($sql);
    $smarty->assign('supplier_rank', $supplier_rank);

    $company_type = explode("\n", str_replace("\r\n", "\n", $_CFG['company_type']));
    $smarty->assign('company_type', $company_type);

    $smarty->assign('user_id', $_SESSION['user_id']);
    $smarty->assign('mydomain', $ecs->url());
    $smarty->display('user_transaction.dwt');
}

function action_act_supplier_reg() {

    // 获取全局变量
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    $supplier_name = isset($_POST['supplier_name']) ? trim($_POST['supplier_name']) : '';
    $rank_id = isset($_POST['rank_id']) ? intval($_POST['rank_id']) : 0;
    $company_name = isset($_POST['company_name']) ? trim($_POST['company_name']) : '';
    $country = isset($_POST['country']) ? intval($_POST['country']) : 1;
    $province = isset($_POST['province']) ? intval($_POST['province']) : 1;
    $city = isset($_POST['city']) ? intval($_POST['city']) : 1;
    $district = isset($_POST['district']) ? intval($_POST['district']) : 1;
    $country = isset($_POST['country']) ? intval($_POST['country']) : 1;
    $address = isset($_POST['address']) ? trim($_POST['address']) : '';
    $tel = isset($_POST['tel']) ? trim($_POST['tel']) : '';
    $guimo = isset($_POST['guimo']) ? trim($_POST['guimo']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $company_type = isset($_POST['company_type']) ? trim($_POST['company_type']) : '';
    $bank = isset($_POST['bank']) ? trim($_POST['bank']) : '';
    $contact = isset($_POST['contact']) ? trim($_POST['contact']) : '';
    $contact_back = isset($_POST['contact_back']) ? trim($_POST['contact_back']) : '';
    $contact_shop = isset($_POST['contact_shop']) ? trim($_POST['contact_shop']) : '';
    $contact_yunying = isset($_POST['contact_yunying']) ? trim($_POST['contact_yunying']) : '';
    $contact_shouhou = isset($_POST['contact_shouhou']) ? trim($_POST['contact_shouhou']) : '';
    $contact_caiwu = isset($_POST['contact_caiwu']) ? trim($_POST['contact_caiwu']) : '';
    $contact_jishu = isset($_POST['contact_jishu']) ? trim($_POST['contact_jishu']) : '';
    $add_time = gmtime();

    /* 图片上传处理 */
    $upload_size_limit = $_CFG['upload_size_limit'] == '-1' ? ini_get('upload_max_filesize') : $_CFG['upload_size_limit'];

    $last_char = strtolower($upload_size_limit{strlen($upload_size_limit) - 1});
    switch ($last_char) {
        case 'm':
            $upload_size_limit *= 1024 * 1024;
            break;
        case 'k':
            $upload_size_limit *= 1024;
            break;
    }
    if (isset($_FILES['zhizhao']) && $_FILES['zhizhao']['tmp_name'] != '' && isset($_FILES['zhizhao']['tmp_name']) && $_FILES['zhizhao']['tmp_name'] != 'none') {
        if ($_FILES['zhizhao']['size'] / 1024 > $upload_size_limit) {
            $err->add(sprintf($_LANG['upload_file_limit'], $upload_size_limit));
            $err->show($_LANG['back_up_page']);
        }
        $zhizhao_img = upload_file($_FILES['zhizhao'], 'supplier');
        if ($zhizhao_img === false) {
            $err->add('业执照图片上传失败！');
            $err->show($_LANG['back_up_page']);
        } else {
            $sql_img = "zhizhao='$zhizhao_img',";
        }
    }
    if (isset($_FILES['id_card']) && $_FILES['id_card']['tmp_name'] != '' && isset($_FILES['id_card']['tmp_name']) && $_FILES['id_card']['tmp_name'] != 'none') {
        if ($_FILES['id_card']['size'] / 1024 > $upload_size_limit) {
            $err->add(sprintf($_LANG['upload_file_limit'], $upload_size_limit));
            $err->show($_LANG['back_up_page']);
        }
        $id_card_img = upload_file($_FILES['id_card'], 'supplier');
        if ($id_card_img === false) {
            $err->add('身份证图片上传失败！');
            $err->show($_LANG['back_up_page']);
        } else {
            $sql_img .= "id_card='$id_card_img', ";
        }
    }

    $sql = "select supplier_id from " . $ecs->table('supplier') . " where user_id='$user_id' ";
    $supplier_id = $db->getOne($sql);

    if ($supplier_id) {
        $mes = '供货商申请修改成功，已经重新进入审核流程，请留意审核结果！';
        $sql = "update " . $ecs->table('supplier') . " set supplier_name='$supplier_name', rank_id='$rank_id', company_name='$company_name', " . "country='$country', province='$province', city='$city', district='$district', address='$address', tel='$tel', guimo='$guimo', email='$email', " . "company_type='$company_type', bank='$bank', " . $sql_img . " contact='$contact', contact_back='$contact_back', contact_shop='$contact_shop', contact_yunying='$contact_yunying', contact_shouhou='$contact_shouhou', contact_caiwu='$contact_caiwu', contact_jishu='$contact_jishu'," . "status='0' " . " where supplier_id='$supplier_id' ";
    } else {
        $mes = '供货商申请提交成功，已经进入审核流程，请留意审核结果！';
        $sql = "insert into " . $ecs->table('supplier') . "(user_id, supplier_name, rank_id, company_name, country, province, city, district, address, tel, guimo, email," . "company_type, bank, zhizhao, id_card, contact, contact_back, contact_shop, contact_yunying, contact_shouhou, contact_caiwu, contact_jishu, add_time) " . " values('$user_id', '$supplier_name', '$rank_id', '$company_name', '$country', '$province', '$city', '$district', '$address', '$tel', '$guimo', '$email', " . "'$company_type', '$bank',  '$zhizhao_img', '$id_card_img',  '$contact', '$contact_back', '$contact_shop', '$contact_yunying', '$contact_shouhou', '$contact_caiwu', '$contact_jishu', '$add_time')";
    }
    $db->query($sql);
    show_message($mes, '返回上一页', 'user.php?act=supplier_reg', 'info');
}

function action_act_supplier_del() {

    // 获取全局变量
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    $userid = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    $supid = isset($_POST['supid']) ? intval($_POST['supid']) : 0;
    if (empty($userid) || empty($supid)) {
        show_message('请刷新页面，重新操作！', '返回上一页', 'user.php?act=supplier_reg', 'wrong');
    }
    if ($userid != $user_id) {
        show_message('你没权限删除此申请！', '返回首页', '', 'wrong');
    }
    $sql = "select supplier_id from " . $ecs->table('supplier') . " where user_id='$user_id'";
    $supplier_id = $db->getOne($sql);
    if ($supid != $supplier_id) {
        show_message('你没权限删除此申请！', '返回首页', '', 'wrong');
    }
    $sql = "delete from " . $ecs->table('supplier') . "  where supplier_id=" . $supplier_id;
    $db->query($sql);
    show_message('操作成功！', '返回上一页', 'user.php', 'info');
}

// 用户中心欢迎页
function action_default() {
    // 获取全局变量
    /* 代码增加 By  www.68ecshop.com Start */
    $_CFG = $GLOBALS['_CFG'];
    /* 代码增加 By  www.68ecshop.com End */
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    /* 代码注释 by www.68ecshop.com _star 路由前已调用会员信息，去掉重复调用 */
    $rank = get_rank_info();
    /* 	include_once (ROOT_PATH . 'includes/lib_clips.php');
      if($rank = get_rank_info())
      {
      $smarty->assign('rank_name', $rank['rank_name']);
      if(! empty($rank['next_rank_name']))
      {
      $smarty->assign('next_rank_name', sprintf($_LANG['next_level'], $rank['next_rank'], $rank['next_rank_name']));
      }
      } */
    /* 代码注释 by www.68ecshop.com _end */

    /* 代码增加2014-12-23 by www.68ecshop.com _star */
    $min_time = gmtime() - 86400 * $_CFG['comment_youxiaoqi'];
    $num_comment = $db->getOne("SELECT COUNT(*) AS num FROM " . $ecs->table('order_goods') . " AS og
							LEFT JOIN " . $ecs->table('order_info') . " AS o ON og.order_id=o.order_id
        WHERE o.user_id = '$user_id' AND og.is_back = 0 AND og.comment_state = 0 AND o.shipping_time_end > $min_time");
    $smarty->assign('num_comment', $num_comment);
    $smarty->assign('is_identity', $_CFG['identity']);
    /* 代码增加2014-12-23 by www.68ecshop.com _end */
    /* 代码增加--cb--推荐分成-- by www.68ecshop.com _star */
    $rn = $rank['rank_name'];
    $recomm = $db->getOne("SELECT is_recomm FROM " . $GLOBALS['ecs']->table('user_rank') . " WHERE rank_name= '$rn'");

    $smarty->assign('recomm', $recomm); // 获取当前用户是否是分成用户判断是否显示我的推荐
    /* 代码增加--cb--推荐分成-- by www.68ecshop.com _end */

   
    $smarty->assign('gouwuche', get_user_gouwuche($user_id)); // 获取当前用户购物车里面的数据
    $smarty->assign('jifen', get_user_jifen()); // 获取当前积分商城里面的数据
    $smarty->assign('collection', get_user_collection($user_id)); // 获取用户收藏的商品
    $smarty->assign('guanzhu', get_user_guanzhu($user_id)); // 获取用户关注的店铺
    $smarty->assign('mai', get_user_mai($user_id)); // 获取用户购买过的商品
    $smarty->assign('reminding', get_user_reminding($user_id)); // 获取当前用户的交易记录
    $smarty->assign('shu', get_user_shu($user_id)); // 获取当前用户的交易记录
    // print_r(get_user_reminding($user_id));
    /* 代码修改 By  www.68ecshop.com Start */
//    $smarty->assign('user_notice', $_CFG['user_notice']);
    $user_notice = $db->getOne(
            'SELECT value FROM ' . $ecs->table('shop_config') . " WHERE code = 'user_notice'"
    );
    $smarty->assign('user_notice', $user_notice);
   
    $smarty->assign('prompt', get_user_prompt($user_id)); // 获取用户参与活动信息
    $smarty->display('user_clips.dwt');
}

function action_getverifycode() {

    // 获取全局变量
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    require (dirname(__FILE__) . '/send.php');
    $phone = trim($_GET['mobile']);
    $u_name = trim($_GET['u_name']);

    /* 获取验证码请求是否获取过 */
    $sql = "SELECT COUNT(id) FROM " . $ecs->table('verifycode') . " WHERE status=1 AND getip='" . real_ip() . "' AND dateline>'" . gmtime() . "'-" . "60";

    if ($db->getOne($sql) > 0) {
        echo 'false';
    }

    $sql = "select count(*) from " . $GLOBALS['ecs']->table('users') . " where user_name = '$u_name' and mobile_phone = '$phone'";
    $count = $GLOBALS['db']->getOne($sql);
    if ($count == 0) {
        echo 'false';
    }

    $shuzi = "0123456789";
    $verifycode = mc_random(6, $shuzi);

    $content = '您的验证码为' . $verifycode . '【香柏】';
    /* 发送注册手机短信验证 */
    $ret = sendSMS($phone, $content);

    $db->query("delete from " . $ecs->table('verifycode') . " where mobile='$phone'");

    // 插入获取验证码数据记录
    $sql = "INSERT INTO " . $ecs->table('verifycode') . "(mobile, getip, verifycode, dateline) VALUES ('" . $phone . "', '" . real_ip() . "', '$verifycode', '" . gmtime() . "')";
    $db->query($sql);

    echo 'ok';
    /* 代码增加2014-12-23 by www.68ecshop.com _end */
}

// 第三方登录接口
function action_oath() {

    // 获取全局变量
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    $type = empty($_REQUEST['type']) ? '' : $_REQUEST['type'];

    if ($type == "taobao") {
        header("location:includes/website/tb_index.php");
        exit();
    }

    include_once (ROOT_PATH . 'includes/website/jntoo.php');

    $c = &website($type);

    if ($c) {
        if (empty($_REQUEST['callblock'])) {
            if (empty($_REQUEST['callblock']) && isset($GLOBALS['_SERVER']['HTTP_REFERER'])) {
                $back_act = strpos($GLOBALS['_SERVER']['HTTP_REFERER'], 'user.php') ? 'index.php' : $GLOBALS['_SERVER']['HTTP_REFERER'];
            } else {
                $back_act = 'index.php';
            }
        } else {
            $back_act = trim($_REQUEST['callblock']);
        }

        if ($back_act[4] != ':')
            $back_act = $ecs->url() . $back_act;
        $open = empty($_REQUEST['open']) ? 0 : intval($_REQUEST['open']);

        $url = $ecs->url() . 'user.php?act=oath_login&type=' . $type . '&callblock=' . urlencode($back_act) . '&open=' . $open;

        $url = $c->login($url);

        if (!$url) {
            show_message($c->get_error(), '首页', $ecs->url(), 'error');
        }
        header('Location: ' . $url);
    } else {
        show_message('服务器尚未注册该插件！', '首页', $ecs->url(), 'error');
    }
}

// 处理第三方登录接口
function action_oath_login() {

    // 获取全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    $type = empty($_REQUEST['type']) ? '' : $_REQUEST['type'];

    include_once (ROOT_PATH . 'includes/website/jntoo.php');
    $c = &website($type);
    if ($c) {
        $access = $c->getAccessToken();
        if (!$access) {
            show_message($c->get_error(), '首页', $ecs->url(), 'error');
        }
        $c->setAccessToken($access);
        $info = $c->getMessage();
        if (!$info) {
            show_message($c->get_error(), '首页', $ecs->url(), 'error', false);
        }
        if (!$info['user_id'])
            show_message($c->get_error(), '首页', $ecs->url(), 'error', false);

        $info_user_id = $type . '_' . $info['user_id']; // 加个标识！！！防止 其他的标识 一样 //
        // 以后的ID 标识 将以这种形式 辨认
        $info['name'] = str_replace("'", "", $info['name']); // 过滤掉 逗号 不然出错 很难处理
        if (!$info['user_id'])
            show_message($c->get_error(), '首页', $ecs->url(), 'error', false);

        $sql = 'SELECT user_name,password,aite_id FROM ' . $ecs->table('users') . ' WHERE aite_id = \'' . $info_user_id . '\' OR aite_id=\'' . $info['user_id'] . '\'';

        $count = $db->getRow($sql);
        if (!$count) { // 没有当前数据
            if ($user->check_user($info['name'])) { // 重名处理
                $info['name'] = $info['name'] . '_' . $type . (rand(10000, 99999));
            }
            $user_pass = $user->compile_password(array(
                'password' => $info['user_id']
            ));
            $sql = 'INSERT INTO ' . $ecs->table('users') . '(user_name , password, aite_id , sex , reg_time , user_rank , is_validated) VALUES ' . "('$info[name]' , '$user_pass' , '$info_user_id' , '$info[sex]' , '" . gmtime() . "' , '$info[rank_id]' , '1')";
            $db->query($sql);
        } else {
            $sql = '';
            if ($count['aite_id'] == $info['user_id']) {
                $sql = 'UPDATE ' . $ecs->table('users') . " SET aite_id = '$info_user_id' WHERE aite_id = '$count[aite_id]'";
                $db->query($sql);
            }
            if ($info['name'] != $count['user_name']) { // 这段可删除
                if ($user->check_user($info['name'])) { // 重名处理
                    $info['name'] = $info['name'] . '_' . $type . (rand() * 1000);
                }
                $sql = 'UPDATE ' . $ecs->table('users') . " SET user_name = '$info[name]' WHERE aite_id = '$info_user_id'";
                $db->query($sql);
            }
        }
        $user->set_session($info['name']);
        $user->set_cookie($info['name']);
        update_user_info();
        recalculate_price();

        if (!empty($_REQUEST['open'])) {
            die('<script>window.opener.window.location.reload(); window.close();</script>');
        } else {
            ecs_header('Location: ' . $_REQUEST['callblock']);
        }
    }
}

// 处理其它登录接口
function action_other_login() {

    // 获取全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    $type = empty($_REQUEST['type']) ? '' : $_REQUEST['type'];
    // session_start();
    $info = $_SESSION['user_info'];

    if (empty($info)) {
        show_message("非法访问或请求超时！", '首页', $ecs->url(), 'error', false);
    }
    if (!$info['user_id'])
        show_message("非法访问或访问出错，请联系管理员！", '首页', $ecs->url(), 'error', false);

    $info_user_id = $type . '_' . $info['user_id']; // 加个标识！！！防止 其他的标识 一样 //
    // 以后的ID
    // 标识 将以这种形式 辨认
    $info['name'] = str_replace("'", "", $info['name']); // 过滤掉 逗号 不然出错 很难处理

    $sql = 'SELECT user_name,password,aite_id FROM ' . $ecs->table('users') . ' WHERE aite_id = \'' . $info_user_id . '\' OR aite_id=\'' . $info['user_id'] . '\'';

    $count = $db->getRow($sql);
    $login_name = $info['name'];
    if (!$count) { // 没有当前数据
        if ($user->check_user($info['name'])) { // 重名处理
            $info['name'] = $info['name'] . '_' . $type . (rand() * 1000);
        }
        $login_name = $info['name'];
        $user_pass = $user->compile_password(array(
            'password' => $info['user_id']
        ));
        $sql = 'INSERT INTO ' . $ecs->table('users') . '(user_name , password, aite_id , sex , reg_time , user_rank , is_validated) VALUES ' . "('$info[name]' , '$user_pass' , '$info_user_id' , '$info[sex]' , '" . gmtime() . "' , '$info[rank_id]' , '1')";
        $db->query($sql);
    } else {
        $login_name = $count['user_name'];
        $sql = '';
        if ($count['aite_id'] == $info['user_id']) {
            $sql = 'UPDATE ' . $ecs->table('users') . " SET aite_id = '$info_user_id' WHERE aite_id = '$count[aite_id]'";
            $db->query($sql);
        }
    }

    $user->set_session($login_name);
    $user->set_cookie($login_name);
    update_user_info();
    recalculate_price();

    $redirect_url = "http://" . $_SERVER["HTTP_HOST"] . str_replace("user.php", "index.php", $_SERVER["REQUEST_URI"]);
    header('Location: ' . $redirect_url);
}

/* 验证用户注册邮件 */

function action_validate_email() {

    // 获取全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    $hash = empty($_GET['hash']) ? '' : trim($_GET['hash']);
    if ($hash) {
        include_once (ROOT_PATH . 'includes/lib_passport.php');
        $id = register_hash('decode', $hash);
        if ($id > 0) {
            $sql = "UPDATE " . $ecs->table('users') . " SET is_validated = 1 WHERE user_id='$id'";
            $db->query($sql);
            $sql = 'SELECT user_name, email FROM ' . $ecs->table('users') . " WHERE user_id = '$id'";
            $row = $db->getRow($sql);
            show_message(sprintf($_LANG['validate_ok'], $row['user_name'], $row['email']), $_LANG['profile_lnk'], 'user.php');
        }
    }
    show_message($_LANG['validate_fail']);
}

/* 代码增加2014-12-23 by www.68ecshop.com _star */

function action_check_username() {
    // 获取全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    $username = trim($_GET['username']);
    $username = json_str_iconv($username);
    $sql = 'SELECT COUNT(*) FROM ' . $GLOBALS['ecs']->table('users') . " WHERE user_name = '$username' AND user_id <> '$_SESSION[user_id]'";
    $is_exist = $GLOBALS['db']->getOne($sql);
    echo $is_exist;
//	$sql = "select user_name from " . $GLOBALS['ecs']->table('users') . " where user_id = '" . $_SESSION['user_id'] . "'";
//	$u_name = $GLOBALS['db']->getOne($sql);
//	if($username == $u_name)
//	{
//		echo "true";
//	}
//	else
//	{
//		$sql = "select count(*) from " . $GLOBALS['ecs']->table('users') . " where user_name = '$username'";
//		$count = $GLOBALS['db']->getOne($sql);
//		if($count > 0)
//		{
//			echo "false";
//		}
//		else
//		{
//			echo "true";
//		}
//	}
}

/* 代码增加2014-12-23 by www.68ecshop.com _end */
/* 验证用户注册用户名是否可以注册 */

function action_is_registered() {

    // 获取全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/lib_passport.php');

    $username = trim($_GET['username']);
    $username = json_str_iconv($username);

    if ($user->check_user($username) || admin_registered($username)) {
        echo 'false';
    } else {
        echo 'true';
    }
}

/**
 * 验证用户邮箱地址是否被注册
 * @return ok-可以注册 false-不可以注册
 */
function action_check_email() {
    // 获取全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    $email = trim($_REQUEST['email']);
    if ($user->check_email($email)) {
        echo 'false';
    } else {
        echo 'ok';
    }
}

/* 验证用户输入的邮箱验证码是否正确 */

function action_check_email_code() {

    // 获取全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    $email = trim($_REQUEST['email']);
    $email_code = trim($_REQUEST['email_code']);

    if (time() - $_SESSION['email_code_time'] > 30 * 60) {
        unset($_SESSION['email_code']);
        exit(json_encode(array(
            'msg' => '验证码超过30分钟，请重新发送。'
        )));
    } else {
        if ($email != $_SESSION['email'] or $email_code != $_SESSION['email_code']) {
            exit(json_encode(array(
                'msg' => '邮箱验证码输入错误。'
            )));
        } else {
            exit(json_encode(array(
                'code' => '2'
            )));
        }
    }
}

/* 验证手机号码是否被注册 */

function action_check_mobile_phone() {

    // 获取全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    $mobile_phone = trim($_REQUEST['mobile_phone']);
    if ($user->check_mobile_phone($mobile_phone)) {
        echo 'false';
    } else {
        echo 'ok';
    }
}

/* 用户登录界面 */

function action_login() {

    // 获取全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    $back_act = $GLOBALS['back_act'];

    if (empty($back_act)) {
        if (empty($back_act) && isset($GLOBALS['_SERVER']['HTTP_REFERER'])) {
            $http_referer = $GLOBALS['_SERVER']['HTTP_REFERER'];

            // 如果来自找回密码页面则跳转到首页
            if (strpos($http_referer, 'findPwd.php') != false) {
                $http_referer = './index.php';
            }

            $back_act = strpos($GLOBALS['_SERVER']['HTTP_REFERER'], 'user.php') ? './index.php' : $http_referer;
        } else {
            $back_act = 'user.php';
        }
    }

    $captcha = intval($_CFG['captcha']);
    if (($captcha & CAPTCHA_LOGIN) && (!($captcha & CAPTCHA_LOGIN_FAIL) || (($captcha & CAPTCHA_LOGIN_FAIL) && $_SESSION['login_fail'] > 2)) && gd_version() > 0) {
        $GLOBALS['smarty']->assign('enabled_captcha', 1);
        $GLOBALS['smarty']->assign('rand', mt_rand());
    }

    $smarty->assign('back_act', $back_act);
    $smarty->display('user_passport.dwt');
}

// 代码增加--68ecshop--侧边栏登录 判断登录是否开启验证码
function action_login_check_yzm() {

    // 获取全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON();
    $result = array(
        'error' => 0, 'message' => '', 'islogin' => ''
    );
    $captcha = intval($_CFG['captcha']);
    if (empty($back_act)) {
        if (empty($back_act) && isset($GLOBALS['_SERVER']['HTTP_REFERER'])) {
            $back_act = strpos($GLOBALS['_SERVER']['HTTP_REFERER'], 'user.php') ? './index.php' : $GLOBALS['_SERVER']['HTTP_REFERER'];
        } else {
            $back_act = 'user.php';
        }
    }
    if (!$_SESSION['user_id']) {
        $result['islogin'] = 1;
        if (($captcha & CAPTCHA_LOGIN) && (!($captcha & CAPTCHA_LOGIN_FAIL) || (($captcha & CAPTCHA_LOGIN_FAIL) && $_SESSION['login_fail'] > 2)) && gd_version() > 0) {
            $result['error'] = 1;
            $result['message'] = $back_act;
            die($json->encode($result));
        } else {
            $result['error'] = 0;
            $result['message'] = $back_act;
            die($json->encode($result));
        }
    } else {
        $result['islogin'] = 0;
        die($json->encode($result));
    }
}

// 代码增加--68ecshop--侧边栏登录 判断登录是否开启验证码

/* 处理会员的登录 */
function action_act_login() {
//AXAJ 登陆修改
    include_once ('includes/cls_json.php');
    $json = new JSON();


    $result = array(
        'error' => 0, 'message' => '', 'url' => ''
    );
    //AXAJ 登陆修改
    // 获取全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $pay_password = isset($_POST['pay_password']) ? trim($_POST['pay_password']) : '';
    $back_act = isset($_POST['back_act']) ? trim($_POST['back_act']) : '';

    $captcha = intval($_CFG['captcha']);
    if (($captcha & CAPTCHA_LOGIN) && (!($captcha & CAPTCHA_LOGIN_FAIL) || (($captcha & CAPTCHA_LOGIN_FAIL) && $_SESSION['login_fail'] > 2)) && gd_version() > 0) {
        if (empty($_POST['captcha'])) {
            $result['error'] = 1;
            $result['message'] = '验证码为空！';
            die($json->encode($result));
            //show_message($_LANG['invalid_captcha'], $_LANG['relogin_lnk'], 'user.php', 'error');
        }

        /* 检查验证码 */
        include_once ('includes/cls_captcha.php');

        $validator = new captcha();
        $validator->session_word = 'captcha_login';
        if (!$validator->check_word($_POST['captcha'])) {
            $result['error'] = 1;
            $result['message'] = $_LANG['invalid_captcha'];

            die($json->encode($result));
            //show_message($_LANG['invalid_captcha'], $_LANG['relogin_lnk'], 'user.php', 'error');
        }
    }
    /* 代码增加2014-12-23 by www.68ecshop.com _star */
    if (is_email($username)) {
        $sql = "select user_name from " . $ecs->table('users') . " where email='" . $username . "'";
        $username_e = $db->getOne($sql);
        if ($username_e)
            $username = $username_e;
    }
    if (is_telephone($username)) {
        $sql = "select user_name from " . $ecs->table('users') . " where mobile_phone='" . $username . "'";
        $username_res = $db->query($sql);
        $kkk = 0;
        while ($username_row = $db->fetchRow($username_res)) {
            $username_e = $username_row['user_name'];
            $kkk = $kkk + 1;
        }
        if ($kkk > 1) {
            $result['error'] = 1;
            $result['message'] = '本网站有多个会员ID绑定了和您相同的手机号，请使用其他登录方式，如：邮箱或用户名。';
            die($json->encode($result));
            //show_message('本网站有多个会员ID绑定了和您相同的手机号，请使用其他登录方式，如：邮箱或用户名。', $_LANG['relogin_lnk'], 'user.php', 'error');
        }
        if ($username_e) {
            $username = $username_e;
        }
    }
    /* 代码增加2014-12-23 by www.68ecshop.com _end */
    if ($user->login($username, $password, isset($_POST['remember']))) {

        //判断二级密码
        if (!$user->check_erjipassword($pay_password)) {
            $user->logout();
            $_SESSION['login_fail'] ++;
            $result['error'] = 1;
            $result['message'] = "二级密码不正确";
            die($json->encode($result));
        }
        /* 限制某个会员登录 */
        if ($_SESSION['user_id'] > 0) {
            $blacklist = check_user_is_blacklist($_SESSION['user_id']);
            if (!empty($blacklist)) {
                $user->logout();
//                show_message($blacklist['limit_desc']);
//                exit;
                $result['error'] = 1;
                $result['message'] = $blacklist['limit_desc'];
                die($json->encode($result));
            }
        }

        update_user_info();
        recalculate_price();

        if (strpos($back_act, 'findPwd.php') != false || strpos($back_act, 'register.php') != false) {
            $back_act = 'index.php';
        }

        $ucdata = isset($user->ucdata) ? $user->ucdata : '';
        $result['error'] = 0;
        $result['message'] = '';
        $result['url'] = $back_act;
        die($json->encode($result));
        //show_message($_LANG['login_success'] . $ucdata, array(
//			$_LANG['back_up_page'], $_LANG['profile_lnk']
//		), array(
//			$back_act, 'user.php'
//		), 'info');
    } else {
        $_SESSION['login_fail'];
        $result['error'] = 1;
        $result['message'] = $_LANG['login_failure'];
        die($json->encode($result));
        //show_message($_LANG['login_failure'], $_LANG['relogin_lnk'], 'user.php', 'error');
    }
    die($json->encode($result));
}

/* 代码增加2014-12-23 by www.68ecshop.com _star */

function action_ch_email() {

    // 获取全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once ('includes/cls_json.php');
    $json = new JSON();

    $email = trim($_GET['email']);

    $result = array(
        'error' => 0, 'message' => ''
    );
    $sql = "select count(*) from " . $GLOBALS['ecs']->table('users') . " where email = '$email'";
    $num = $GLOBALS['db']->getOne($sql);
    if ($num > 0) {
        $result['error'] = 1;
        $result['message'] = '该邮箱已被使用，请更换其他邮箱！';
    } else {
        $tpl = get_mail_template('ch_email');
        $run = "0123456789abcdefghijklmnopqrstuvwxyz";
        $hash = mc_random(16, $run);
        $v_email = $GLOBALS['ecs']->url() . 'user.php?act=ck_email&hash=' . $hash;

        $smarty->assign('shop_name', $_CFG['shop_name']);
        $smarty->assign('send_date', date($_CFG['time_format']));
        $smarty->assign('user_name', '客户');
        $smarty->assign('email', $v_email);
        $content = $smarty->fetch('str:' . $tpl['template_content']);
        $res = send_mail($_CFG['shop_name'], $email, $tpl['template_subject'], $content, $tpl['is_html']);
        if ($res == true) {
            $add_time = time();
            $sql = "insert into " . $GLOBALS['ecs']->table('email') . "(`email`,`hash`,`add_time`,`user_id`) values('$email','$hash','$add_time',0)";
            $GLOBALS['db']->query($sql);
            $result['error'] = 0;
            $result['message'] = '邮件已发送至邮箱内，请注意查收！';
        } else {
            $result['error'] = 2;
            $result['message'] = '邮件发送失败！';
        }
    }
    die($json->encode($result));
}

function action_ck_email() {

    // 获取全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    $hash = empty($_REQUEST['hash']) ? '' : trim($_REQUEST['hash']);
    $sql = "select * from " . $GLOBALS['ecs']->table('email') . " where hash = '$hash'";
    $row = $GLOBALS['db']->getRow($sql);
    $now_time = time();
    if ($now_time - $row['add_time'] > 24 * 60 * 60) {
        $sql = "delete from " . $GLOBALS['ecs']->table('email') . " where hash = '$hash'";
        $GLOBALS['db']->query($sql);
        show_message('验证邮件已发送超过24小时，请重新验证！');
    } else {
        $sql = "select count(*) from " . $GLOBALS['ecs']->table('email') . " where hash = '$hash'";
        $count = $GLOBALS['db']->getOne($sql);
        if ($count > 0) {
            $_SESSION['tag'] = 1;
            $sql = "delete from " . $GLOBALS['ecs']->table('email') . " where hash = '$hash'";
            $GLOBALS['db']->query($sql);
            show_message('验证成功，请继续注册！');
        }
    }
}

/* 代码增加2014-12-23 by www.68ecshop.com _end */
/* 处理 ajax 的登录请求 */

function action_signin() {

    // 获取全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once ('includes/cls_json.php');
    $json = new JSON();

    $username = !empty($_POST['username']) ? json_str_iconv(trim($_POST['username'])) : '';
    $password = !empty($_POST['password']) ? trim($_POST['password']) : '';
    $captcha = !empty($_POST['captcha']) ? json_str_iconv(trim($_POST['captcha'])) : '';
    $result = array(
        'error' => 0, 'content' => ''
    );

    $captcha = intval($_CFG['captcha']);
    if (($captcha & CAPTCHA_LOGIN) && (!($captcha & CAPTCHA_LOGIN_FAIL) || (($captcha & CAPTCHA_LOGIN_FAIL) && $_SESSION['login_fail'] > 2)) && gd_version() > 0) {
        if (empty($captcha)) {
            $result['error'] = 1;
            $result['content'] = $_LANG['invalid_captcha'];
            die($json->encode($result));
        }

        /* 检查验证码 */
        include_once ('includes/cls_captcha.php');

        $validator = new captcha();
        $validator->session_word = 'captcha_login';
        if (!$validator->check_word($_POST['captcha'])) {

            $result['error'] = 1;
            $result['content'] = $_LANG['invalid_captcha'];
            die($json->encode($result));
        }
    }

    if ($user->login($username, $password)) {
        update_user_info(); // 更新用户信息
        recalculate_price(); // 重新计算购物车中的商品价格
        $smarty->assign('user_info', get_user_info());
        $ucdata = empty($user->ucdata) ? "" : $user->ucdata;
        $result['ucdata'] = $ucdata;
        $result['content'] = $smarty->fetch('library/member_info.lbi');
    } else {
        $_SESSION['login_fail'] ++;
        if ($_SESSION['login_fail'] > 2) {
            $smarty->assign('enabled_captcha', 1);
            $result['html'] = $smarty->fetch('library/member_info.lbi');
        }
        $result['error'] = 1;
        $result['content'] = $_LANG['login_failure'];
    }
    die($json->encode($result));
}


/**
 * 领取红包
 */
function action_get_hong_bao(){
	$sql="select * from ".$GLOBALS ["ecs"]->table ( "bonus_points" )." where user_id=".$_SESSION['user_id'];
	$rows = $GLOBALS['db']->getRow($sql);
	if($rows){
		
		$tdate = local_date("Y-m-d",  $rows['send_time']);
		$desc="领取" . $tdate . "的积分，分红积分:" . $rows['pay_points'];
		//积分增加到账户, 并记录账户变动日志
		$tres = log_account_change($rows['user_id'], 0, 0, 0, $rows['pay_points'], $desc, ACT_LQHB, 0, 0, 0, 0, 0, 0, 0, 0, 0, $rows['yl_points'], 0, $rows['hz_points']);
		$sql=" delete from ".$GLOBALS ["ecs"]->table ( "bonus_points" )." where user_id=".$_SESSION['user_id'];//删除掉已经领取的积分
		$GLOBALS['db']->query($sql);
// 		$json = new JSON();
		echo ($desc);
		die();
	}else{//没有可以领取的红包, 查看今天是否已经领过了
	 
		$sql="select a.* from  ".$GLOBALS ["ecs"]->table ( "account_log" )."  a join ".$GLOBALS ["ecs"]->table ( "users" )." b on a.user_id=b.user_id where  a.user_id=".$_SESSION['user_id']." and  a.pay_points <> 0 and b.zsq!=0  ";
		
		$rows = $GLOBALS['db']->getRow($sql);
		if($rows){//
			echo ("您今天已经领过红包了");
			die;
		}else{
			echo("您目前不满足赠送条件,请先消费再来领取红包吧");
			die;
		}
	}
	die;
}


/**
 * 是否有红包
 */
function action_is_hong_bao(){
	$sql="select count(0) from ".$GLOBALS ["ecs"]->table ( "bonus_points" )." where user_id=".$_SESSION['user_id'];
	$result = $GLOBALS['db']->getOne($sql);
	echo $result;
}


/**
 * 获取区域广告
 */
function action_getAreaAds(){
	include_once ('includes/cls_json.php');
	$region_id= intval($_POST["region_id"]);
	if($region_id>1){//用户所在地区不是全国
		$sql="select * from  ".$GLOBALS ["ecs"]->table ( "ad" )." where  enabled=1 and province_id is not null and media_type=0 and (province_id=".$region_id." or city_id=".$region_id." or district_id=".$region_id.")";
	}else{//用户所在地区是全国
		$sql="select * from  ".$GLOBALS ["ecs"]->table ( "ad" )." where enabled=1  and  province_id is not null and media_type=0 ";
	}

	$ads_list = $GLOBALS['db']->getAll($sql);//广告列表
	$length = count($ads_list);//地区内广告的数量
	$index = rand(0,$length-1);
	$json = new JSON();
// 	file_put_contents("c:/test.txt", $json->encode($ads_list[$index])."This is another something\r\n", FILE_APPEND);
	 
	if($ads_list){//如果有广告
		$result=$ads_list[$index];
		$result['ad_code']='/data/afficheimg/'.$result['ad_code'];
	}else{
		$result=array('ad_code' => "no");
	}
	
	die($json->encode($result));
}


/* 退出会员中心 */

function action_logout() {

    // 获取全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    if ((!isset($back_act) || empty($back_act)) && isset($GLOBALS['_SERVER']['HTTP_REFERER'])) {
        $back_act = strpos($GLOBALS['_SERVER']['HTTP_REFERER'], 'user.php') ? './index.php' : $GLOBALS['_SERVER']['HTTP_REFERER'];
    }

    $user->logout();
    $ucdata = empty($user->ucdata) ? "" : $user->ucdata;
    show_message($_LANG['logout'] . $ucdata, array(
        $_LANG['back_up_page'], $_LANG['back_home_lnk']
            ), array(
        $back_act, 'index.php'
            ), 'info');
}

/* 个人资料页面 */

function action_profile() {

    // 获取全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    include_once (ROOT_PATH . 'includes/lib_transaction.php');
    /* 代码增加2014-12-23 by www.68ecshop.com _star */
    include_once (ROOT_PATH . 'languages/' . $_CFG['lang'] . '/shopping_flow.php');
    $smarty->assign('lang', $_LANG);

    $smarty->assign('country_list', get_regions());
    /* 代码增加2014-12-23 by www.68ecshop.com _end */
    $user_info = get_profile($user_id);
    /* 代码增加2014-12-23 by www.68ecshop.com _star */
    $province_list = get_regions(1, $user_info['country']);
    $city_list = get_regions(2, $user_info['province']);
    $district_list = get_regions(3, $user_info['city']);

    $smarty->assign('province_list', $province_list);
    $smarty->assign('city_list', $city_list);
    $smarty->assign('district_list', $district_list);
    /* 代码增加2014-12-23 by www.68ecshop.com _end */
    $user_info = get_profile($user_id);

    /* 取出注册扩展字段 */
    $sql = 'SELECT * FROM ' . $ecs->table('reg_fields') . ' WHERE type < 2 AND display = 1 ORDER BY dis_order, id';
    $extend_info_list = $db->getAll($sql);

    $sql = 'SELECT reg_field_id, content ' . 'FROM ' . $ecs->table('reg_extend_info') . " WHERE user_id = $user_id";
    $extend_info_arr = $db->getAll($sql);

    $temp_arr = array();
    foreach ($extend_info_arr as $val) {
        $temp_arr[$val['reg_field_id']] = $val['content'];
    }

    foreach ($extend_info_list as $key => $val) {
        switch ($val['id']) {
            case 1:
                $extend_info_list[$key]['content'] = $user_info['msn'];
                break;
            case 2:
                $extend_info_list[$key]['content'] = $user_info['qq'];
                break;
            case 3:
                $extend_info_list[$key]['content'] = $user_info['office_phone'];
                break;
            case 4:
                $extend_info_list[$key]['content'] = $user_info['home_phone'];
                break;
            case 5:
                $extend_info_list[$key]['content'] = $user_info['mobile_phone'];
                break;
            default:
                $extend_info_list[$key]['content'] = empty($temp_arr[$val['id']]) ? '' : $temp_arr[$val['id']];
        }
    }

    $smarty->assign('extend_info_list', $extend_info_list);

    /* 密码提示问题 */
    $smarty->assign('passwd_questions', $_LANG['passwd_questions']);

    $smarty->assign('profile', $user_info);
    $smarty->display('user_transaction.dwt');
}

/* 修改个人资料的处理 */

function action_act_edit_profile() {

    // 获取全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/lib_transaction.php');

    $birthday = trim($_POST['birthdayYear']) . '-' . trim($_POST['birthdayMonth']) . '-' . trim($_POST['birthdayDay']);
    $email = trim($_POST['email']);
    $other['msn'] = $msn = isset($_POST['extend_field1']) ? trim($_POST['extend_field1']) : '';
    $other['qq'] = $qq = isset($_POST['extend_field2']) ? trim($_POST['extend_field2']) : '';
    $other['bank'] = isset($_POST['bank']) ? trim($_POST['bank']) : '';
    $other['bank_kh'] = isset($_POST['bank_kh']) ? trim($_POST['bank_kh']) : '';
    $other['bank_num'] = isset($_POST['bank_num']) ? trim($_POST['bank_num']) : '';
    $other['office_phone'] = $office_phone = isset($_POST['extend_field3']) ? trim($_POST['extend_field3']) : '';
    $other['home_phone'] = $home_phone = isset($_POST['extend_field4']) ? trim($_POST['extend_field4']) : '';
    // $other['mobile_phone'] = $mobile_phone = isset($_POST['extend_field5']) ?
    // trim($_POST['extend_field5']) : '';
    $sel_question = empty($_POST['sel_question']) ? '' : compile_str($_POST['sel_question']);
    $passwd_answer = isset($_POST['passwd_answer']) ? compile_str(trim($_POST['passwd_answer'])) : '';
    /* 代码增加2014-12-23 by www.68ecshop.com _star */
    $username = trim($_POST['username']);
    /* 代码增加2014-12-23 by www.68ecshop.com _end */

    /* 更新用户扩展字段的数据 */
    $sql = 'SELECT id FROM ' . $ecs->table('reg_fields') . ' WHERE type = 0 AND display = 1 ORDER BY dis_order, id'; // 读出所有扩展字段的id
    $fields_arr = $db->getAll($sql);

    foreach ($fields_arr as $val) { // 循环更新扩展用户信息
        $extend_field_index = 'extend_field' . $val['id'];
        if (isset($_POST[$extend_field_index])) {
            $temp_field_content = strlen($_POST[$extend_field_index]) > 100 ? mb_substr(htmlspecialchars($_POST[$extend_field_index]), 0, 99) : htmlspecialchars($_POST[$extend_field_index]);
            $sql = 'SELECT * FROM ' . $ecs->table('reg_extend_info') . "  WHERE reg_field_id = '$val[id]' AND user_id = '$user_id'";
            if ($db->getOne($sql)) { // 如果之前没有记录，则插入
                $sql = 'UPDATE ' . $ecs->table('reg_extend_info') . " SET content = '$temp_field_content' WHERE reg_field_id = '$val[id]' AND user_id = '$user_id'";
            } else {
                $sql = 'INSERT INTO ' . $ecs->table('reg_extend_info') . " (`user_id`, `reg_field_id`, `content`) VALUES ('$user_id', '$val[id]', '$temp_field_content')";
            }
            $db->query($sql);
        }
    }

    /* 写入密码提示问题和答案 */
    if (!empty($passwd_answer) && !empty($sel_question)) {
        $sql = 'UPDATE ' . $ecs->table('users') . " SET `passwd_question`='$sel_question', `passwd_answer`='$passwd_answer'  WHERE `user_id`='" . $_SESSION['user_id'] . "'";
        $db->query($sql);
    }
    /* 代码增加2014-12-23 by www.68ecshop.com _star */
    $sql = "select user_name from " . $GLOBALS['ecs']->table('users') . " where user_id = '" . $_SESSION['user_id'] . "'";
    $u_name = $GLOBALS['db']->getOne($sql);
    if ($username != $u_name) {
        $sql = "select count(*) from " . $GLOBALS['ecs']->table('users') . " where user_name = '$username'";
        $count = $GLOBALS['db']->getOne($sql);
        if ($count > 0) {
            show_message('用户名已经存在！');
        }
        if (!empty($username) && preg_match("/[\x7f-\xff]/", $username)) {

            show_message("用户名存在中文");
        }
        /* 代码增加 By  www.68ecshop.com Start */ else if (empty($username)) {
            show_message("用户名为空");
        } else if (!preg_match("/^[a-zA-Z0-9_]{1,}$/", $username)) {
            show_message("用户名只能由字母数字下划线组成");
        }
        /* 代码增加 By  www.68ecshop.com End */
    }
    /* 代码增加2014-12-23 by www.68ecshop.com _end */
    if (!empty($office_phone) && !preg_match('/^[\d|\_|\-|\s]+$/', $office_phone)) {
        show_message($_LANG['passport_js']['office_phone_invalid']);
    }
    if (!empty($home_phone) && !preg_match('/^[\d|\_|\-|\s]+$/', $home_phone)) {
        show_message($_LANG['passport_js']['home_phone_invalid']);
    }
    // if(! is_email($email))
    // {
    // show_message($_LANG['msg_email_format']);
    // }
    if (!empty($msn) && !is_email($msn)) {
        show_message($_LANG['passport_js']['msn_invalid']);
    }
    if (!empty($qq) && !preg_match('/^\d+$/', $qq)) {
        show_message($_LANG['passport_js']['qq_invalid']);
    }
    // if(! empty($mobile_phone) && ! preg_match('/^[\d-\s]+$/', $mobile_phone))
    // {
    // show_message($_LANG['passport_js']['mobile_phone_invalid']);
    // }

    $other['user_name'] = $username;
    // $profile = array(
    // 'user_id' => $user_id,'email' => isset($_POST['email']) ?
    // trim($_POST['email']) : '','sex' => isset($_POST['sex']) ?
    // intval($_POST['sex']) : 0,'birthday' => $birthday,'other' =>
    // isset($other) ? $other : array()
    // );
    $profile = array(
        'user_id' => $user_id, 'user_name' => $username, 'sex' => isset($_POST['sex']) ? intval($_POST['sex']) : 0, 'birthday' => $birthday, 'other' => isset($other) ? $other : array()
    );

    if (edit_profile($profile)) {
        show_message($_LANG['edit_profile_success'], $_LANG['profile_lnk'], 'user.php?act=profile', 'info');
    } else {
        if ($user->error == ERR_EMAIL_EXISTS) {
            $msg = sprintf($_LANG['email_exist'], $profile['email']);
        } else {
            $msg = $_LANG['edit_profile_failed'];
        }
        show_message($msg, '', '', 'info');
    }
}

/* 修改头像 */

function action_act_edit_img() {

    // 获取全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    if ($_FILES['headimg']['size'] == 0) {
        show_message("您没有选择要修改的头像图片！", $_LANG['profile_lnk'], 'user.php?act=profile', 'info');
    }

    /* 代码增加_start By www.68ecshop.com */
    include_once (ROOT_PATH . '/includes/cls_image.php');
    $image = new cls_image($_CFG['bgcolor']);
    $headimg_original = $image->upload_image($_FILES['headimg'], 'headimg/' . date('Ym'));

    $thumb_path = DATA_DIR . '/headimg/' . date('Ym') . '/';
    $headimg_thumb = $image->make_thumb($headimg_original, '120', '120', $thumb_path);
    $headimg_thumb = $headimg_thumb ? $headimg_thumb : $headimg_original;
    $sql = 'UPDATE ' . $ecs->table('users') . " SET `headimg`='$headimg_thumb'  WHERE `user_id`='" . $_SESSION['user_id'] . "'";
    $db->query($sql);
    $_SESSION['headimg'] = $headimg_thumb;
    /* 代码增加_end By www.68ecshop.com */
    show_message($_LANG['edit_profile_success'], $_LANG['profile_lnk'], 'user.php?act=profile', 'info');
}

/* 代码增加2014-12-23 by www.68ecshop.com _star */

function action_account_security() {

    // 获取全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/lib_transaction.php');

    $user_info = get_profile($user_id);

    $smarty->assign('info', $user_info);
    $smarty->display('user_transaction.dwt');
}

function action_act_identity() {

    // 获取全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . '/includes/cls_image.php');
    $image = new cls_image($_CFG['bgcolor']);
    $real_name = $_POST['real_name'];
    $card = $_POST['card'];
//	$country = $_POST['country'];
//	$province = $_POST['province'];
//	$city = $_POST['city'];
//	$district = $_POST['district'];
//	$address = $_POST['address'];
    if (isset($_FILES['face_card']) && $_FILES['face_card']['tmp_name'] != '') {
        if ($_FILES['face_card']['width'] > 800) {
            show_message('图片宽度不能超过800像素！');
        }
        if ($_FILES['face_card']['height'] > 800) {
            show_message('图片高度不能超过800像素！');
        }
        $face_card = $image->upload_image($_FILES['face_card']);
        if ($face_card === false) {
            show_message($image->error_msg());
        }
    }
    if (isset($_FILES['back_card']) && $_FILES['back_card']['tmp_name'] != '') {
        if ($_FILES['back_card']['width'] > 800) {
            show_message('图片宽度不能超过800像素！');
        }
        if ($_FILES['back_card']['height'] > 800) {
            show_message('图片高度不能超过800像素！');
        }
        $back_card = $image->upload_image($_FILES['back_card']);
        if ($back_card === false) {
            show_message($image->error_msg());
        }
    }

//     $sql = "select face_card,back_card from " . $GLOBALS['ecs']->table('users') . " where user_id = '" . $_SESSION['user_id'] . "'";
//     $rows = $GLOBALS['db']->getRow($sql);
//     if ($rows['face_card'] == '') {
//        if ($face_card == '') {
//            show_message('请上传身份证正面照！');
//        }
//     }
//     if ($rows['back_card'] == '') {
//        if ($back_card == '') {
//            show_message('请上传身份证背面照！');
//        }
//     }
    //判断是审核
    $sql = "select status from " . $GLOBALS['ecs']->table('users') . " where user_id = '" . $_SESSION['user_id'] . "'";
    $status = $GLOBALS['db']->getRow($sql);
    if ($status['status'] == 1 ) {
        show_message('信息已审核,不允许修改！', '返回上一页', 'user.php?act=profile');
    }

    if ($real_name == '') {
        show_message('请填写真实姓名！');
    }

    $sql = 'update ' . $GLOBALS['ecs']->table('users') . " set real_name = '$real_name' ";
    if ($card != '') {
        $sql .= " ,card = '$card'";
    }
    if ($face_card != '') {
        $sql .= " ,face_card = '$face_card'";
    }
    if ($back_card != '') {
        $sql .= " ,back_card = '$back_card'";
    }

    //加入待审核
    $sql .= " ,status = 2 ";

    $sql .= " where user_id = '" . $_SESSION['user_id'] . "'";
    $num = $GLOBALS['db']->query($sql);
    if ($num > 0) {
        show_message('信息提交成功！', '返回上一页', 'user.php?act=profile');
    } else {
        show_message('信息提交失败！', '返回上一页', 'user.php?act=profile');
    }
}

function action_update_email() {

    // 获取全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    $sql = "select email from " . $GLOBALS['ecs']->table('users') . " where user_id = '" . $_SESSION['user_id'] . "'";
    $email = $GLOBALS['db']->getOne($sql);
    $smarty->assign('email', $email);
    $smarty->display('user_transaction.dwt');
}

function action_act_update_email() {

    // 获取全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/lib_passport.php');
    if (empty($_POST['v_captcha'])) {
        show_message('验证码不能为空！', '返回', 'user.php?act=update_email', 'error');
    }

    /* 检查验证码 */
    include_once ('includes/cls_captcha.php');

    $validator = new captcha();
    $validator->session_word = 'captcha_login';
    if (!$validator->check_word($_POST['v_captcha'])) {
        show_message($_LANG['invalid_captcha'], '返回', 'user.php?act=update_email', 'error');
    } else {
        $sql = "select email,user_name from " . $GLOBALS['ecs']->table('users') . " where user_id = '" . $_SESSION['user_id'] . "'";
        $rows = $GLOBALS['db']->getRow($sql);
        $tpl = get_mail_template('verify_mail');
        $run = "0123456789abcdefghijklmnopqrstuvwxyz";
        $hash = mc_random(16, $run);
        $email = $GLOBALS['ecs']->url() . 'user.php?act=valid_email&hash=' . $hash;

        $smarty->assign('shop_name', $_CFG['shop_name']);
        $smarty->assign('send_date', date($_CFG['time_format']));
        $smarty->assign('user_name', $rows['user_name']);
        $smarty->assign('email', $email);
        $smarty->assign('v_email', $rows['email']);
        $content = $smarty->fetch('str:' . $tpl['template_content']);
        $result = send_mail($_CFG['shop_name'], $rows['email'], $tpl['template_subject'], $content, $tpl['is_html']);
        if ($result == true) {
            $add_time = time();
            $sql = "insert into " . $GLOBALS['ecs']->table('email') . "(`email`,`hash`,`add_time`,`user_id`) values('" . $rows['email'] . "','$hash','$add_time','" . $_SESSION['user_id'] . "')";
            $GLOBALS['db']->query($sql);
            $smarty->display('user_transaction.dwt');
        } else {
            show_message('邮件发送失败！');
        }
    }
}

function action_update_phone() {

    // 获取全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    $sql = "select mobile_phone from " . $GLOBALS['ecs']->table('users') . " where user_id = '" . $_SESSION['user_id'] . "'";
    $mobile_phone = $GLOBALS['db']->getOne($sql);
    $smarty->assign('phone', $mobile_phone);
    $smarty->display('user_transaction.dwt');
}

function action_act_update_phone() {

    // 获取全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    $phone = isset($_POST['v_phone']) ? trim($_POST['v_phone']) : '';
    $verifycode = isset($_POST['v_code']) ? trim($_POST['v_code']) : '';
    if ($phone == '') {
        show_message('手机号不能为空！');
    } else {
        if (is_telephone($phone)) {
            if ($verifycode == '') {
                show_message('手机验证码不能为空！');
            } else {
                /* 验证手机号验证码和IP */
                $sql = "SELECT COUNT(id) FROM " . $ecs->table('verifycode') . " WHERE mobile='$phone' AND verifycode='$verifycode' AND getip='" . real_ip() . "' AND status=1 AND dateline>'" . gmtime() . "'-86400"; // 验证码一天内有效

                if ($db->getOne($sql) == 0) {
                    show_message('手机号和验证码不匹配，请重新输入！');
                } else {
                    ecs_header("Location: user.php?act=re_binding\n");
                    exit();
                }
            }
        } else {
            show_message('请输入正确的手机号！');
        }
    }
}

/* 密码找回-->输入用户名界面 */

function action_qpassword_name() {

    // 获取全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    // 显示输入要找回密码的账号表单
    $smarty->display('user_passport.dwt');
}

/* 密码找回-->根据注册用户名取得密码提示问题界面 */

function action_get_passwd_question() {

    // 获取全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    if (empty($_POST['user_name'])) {
        show_message($_LANG['no_passwd_question'], $_LANG['back_home_lnk'], './', 'info');
    } else {
        $user_name = trim($_POST['user_name']);
    }

    // 取出会员密码问题和答案
    $sql = 'SELECT user_id, user_name, passwd_question, passwd_answer FROM ' . $ecs->table('users') . " WHERE user_name = '" . $user_name . "'";
    $user_question_arr = $db->getRow($sql);

    // 如果没有设置密码问题，给出错误提示
    if (empty($user_question_arr['passwd_answer'])) {
        show_message($_LANG['no_passwd_question'], $_LANG['back_home_lnk'], './', 'info');
    }

    $_SESSION['temp_user'] = $user_question_arr['user_id']; // 设置临时用户，不具有有效身份
    $_SESSION['temp_user_name'] = $user_question_arr['user_name']; // 设置临时用户，不具有有效身份
    $_SESSION['passwd_answer'] = $user_question_arr['passwd_answer']; // 存储密码问题答案，减少一次数据库访问

    $captcha = intval($_CFG['captcha']);
    if (($captcha & CAPTCHA_LOGIN) && (!($captcha & CAPTCHA_LOGIN_FAIL) || (($captcha & CAPTCHA_LOGIN_FAIL) && $_SESSION['login_fail'] > 2)) && gd_version() > 0) {
        $GLOBALS['smarty']->assign('enabled_captcha', 1);
        $GLOBALS['smarty']->assign('rand', mt_rand());
    }

    $smarty->assign('passwd_question', $_LANG['passwd_questions'][$user_question_arr['passwd_question']]);
    $smarty->display('user_passport.dwt');
}

/* 密码找回-->根据提交的密码答案进行相应处理 */

function action_check_answer() {

    // 获取全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    $captcha = intval($_CFG['captcha']);
    if (($captcha & CAPTCHA_LOGIN) && (!($captcha & CAPTCHA_LOGIN_FAIL) || (($captcha & CAPTCHA_LOGIN_FAIL) && $_SESSION['login_fail'] > 2)) && gd_version() > 0) {
        if (empty($_POST['captcha'])) {
            show_message($_LANG['invalid_captcha'], $_LANG['back_retry_answer'], 'user.php?act=qpassword_name', 'error');
        }

        /* 检查验证码 */
        include_once ('includes/cls_captcha.php');

        $validator = new captcha();
        $validator->session_word = 'captcha_login';
        if (!$validator->check_word($_POST['captcha'])) {
            show_message($_LANG['invalid_captcha'], $_LANG['back_retry_answer'], 'user.php?act=qpassword_name', 'error');
        }
    }

    if (empty($_POST['passwd_answer']) || $_POST['passwd_answer'] != $_SESSION['passwd_answer']) {
        show_message($_LANG['wrong_passwd_answer'], $_LANG['back_retry_answer'], 'user.php?act=qpassword_name', 'info');
    } else {
        $_SESSION['user_id'] = $_SESSION['temp_user'];
        $_SESSION['user_name'] = $_SESSION['temp_user_name'];
        unset($_SESSION['temp_user']);
        unset($_SESSION['temp_user_name']);
        $smarty->assign('uid', $_SESSION['user_id']);
        $smarty->assign('action', 'reset_password');
        $smarty->display('user_passport.dwt');
    }
}

/* 发送密码修改确认邮件 */

function action_send_pwd_email() {

    // 获取全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/lib_passport.php');

    /* 初始化会员用户名和邮件地址 */
    $user_name = !empty($_POST['user_name']) ? trim($_POST['user_name']) : '';
    $email = !empty($_POST['email']) ? trim($_POST['email']) : '';

    // 用户名和邮件地址是否匹配
    $user_info = $user->get_user_info($user_name);

    if ($user_info && $user_info['email'] == $email) {
        // 生成code
        // $code = md5($user_info[0] . $user_info[1]);

        $code = md5($user_info['user_id'] . $_CFG['hash_code'] . $user_info['reg_time']);
        // 发送邮件的函数
        if (send_pwd_email($user_info['user_id'], $user_name, $email, $code)) {
            show_message($_LANG['send_success'] . $email, $_LANG['back_home_lnk'], './', 'info');
        } else {
            // 发送邮件出错
            show_message($_LANG['fail_send_password'], $_LANG['back_page_up'], './', 'info');
        }
    } else {
        // 用户名与邮件地址不匹配
        show_message($_LANG['username_no_email'], $_LANG['back_page_up'], '', 'info');
    }
}

/* 修改会员密码 */

function action_act_edit_password() {

    // 获取全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/lib_passport.php');

    $old_password = isset($_POST['old_password']) ? trim($_POST['old_password']) : null;
    $new_password = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';
    /* 代码添加_68ECSHOP_20150729_STAR */
    $confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';
    /* 代码添加_68ECSHOP_20150729_END */
    $user_id = isset($_POST['uid']) ? intval($_POST['uid']) : $user_id;
    $code = isset($_POST['code']) ? trim($_POST['code']) : '';

    if (strlen($new_password) < 6) {
        show_message($_LANG['passport_js']['password_shorter']);
    }
    /* 代码添加_68ECSHOP_20150729_STAR */
    if ($new_password != $confirm_password) {
        show_message($_LANG['passport_js']['confirm_password_invalid']);
    }
    /* 代码添加_68ECSHOP_20150729_END */
    $user_info = $user->get_profile_by_id($user_id); // 论坛记录

    if (($user_info && (!empty($code) && md5($user_info['user_id'] . $_CFG['hash_code'] . $user_info['reg_time']) == $code)) || ($_SESSION['user_id'] > 0 && $_SESSION['user_id'] == $user_id && $user->check_user($_SESSION['user_name'], $old_password))) {

        if ($user->edit_user(array(
                    'username' => (empty($code) ? $_SESSION['user_name'] : $user_info['user_name']), 'old_password' => $old_password, 'password' => $new_password
                        ), empty($code) ? 0 : 1)) {
            $sql = "UPDATE " . $ecs->table('users') . "SET `ec_salt`='0' WHERE user_id= '" . $user_id . "'";
            $db->query($sql);
            // 是否开启修改密码发短信
            if ($_CFG['sms_change_password'] == 1) {
                $content = sprintf($_CFG['sms_change_password_tpl'], date("Y-m-d H:i:s", gmtime()), $_CFG['sms_sign']);
                $sql = "SELECT mobile_phone FROM " . $GLOBALS['ecs']->table('users') . " WHERE user_id = '$user_id'";
                $mobile_phone = $GLOBALS['db']->getOne($sql);
                if ($mobile_phone) {
                    include_once ('send.php');
                    sendSMS($mobile_phone, $content);
                }
            }
            $user->logout();
            show_message($_LANG['edit_password_success'], $_LANG['relogin_lnk'], 'user.php?act=login', 'info');
        } else {
            show_message($_LANG['edit_password_failure'], $_LANG['back_page_up'], '', 'info');
        }
    } else {
        show_message($_LANG['edit_password_failure'], $_LANG['back_page_up'], '', 'info');
    }
}

/* 添加一个红包 */

function action_act_add_bonus() {

    // 获取全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/lib_transaction.php');

    $bouns_sn = isset($_POST['bonus_sn']) ? intval($_POST['bonus_sn']) : '';

    if (add_bonus($user_id, $bouns_sn)) {
        show_message($_LANG['add_bonus_sucess'], $_LANG['back_up_page'], 'user.php?act=bonus', 'info');
    } else {

        $GLOBALS['err']->show($_LANG['back_up_page'], 'user.php?act=bonus');
    }
}

/* 查看订单列表 */

function action_order_list() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    $action = $GLOBALS['action'];

    include_once (ROOT_PATH . 'includes/lib_transaction.php');
    include_once (ROOT_PATH . 'includes/lib_transaction_1.php');
    include_once (ROOT_PATH . 'includes/lib_payment.php');
    include_once (ROOT_PATH . 'includes/lib_order.php');
    include_once (ROOT_PATH . 'includes/lib_clips.php');

    $ex_where = " and user_id=$user_id";

    /* 已完成的订单 */
    $order_count['finished'] = $db->GetOne('SELECT COUNT(*) FROM ' . $ecs->table('order_info') . " WHERE 1 $ex_where " . order_query_sql('finished'));
    $status['finished'] = CS_FINISHED;

    /* 待发货的订单： */
    $order_count['await_ship'] = $db->GetOne('SELECT COUNT(*)' . ' FROM ' . $ecs->table('order_info') . " WHERE 1 $ex_where " . order_query_sql('await_ship'));
    $status['await_ship'] = CS_AWAIT_SHIP;

    /* 待付款的订单： */
    $order_count['await_pay'] = $db->GetOne('SELECT COUNT(*)' . ' FROM ' . $ecs->table('order_info') . " WHERE 1 $ex_where " . order_query_sql('await_pay'));
    $status['await_pay'] = CS_AWAIT_PAY;

    /* “未确认”的订单 */
    $order_count['unconfirmed'] = $db->GetOne('SELECT COUNT(*) FROM ' . $ecs->table('order_info') . " WHERE 1 $ex_where " . order_query_sql('unconfirmed'));
    $status['unconfirmed'] = OS_UNCONFIRMED;

    // $today_start = mktime(0,0,0,date('m'),date('d'),date('Y'));
    $order_count['stats'] = $db->getRow('SELECT COUNT(*) AS oCount, IFNULL(SUM(order_amount), 0) AS oAmount' . ' FROM ' . $ecs->table('order_info'));
    $smarty->assign('order_count', $order_count);
    $smarty->assign('status', $status);

    $composite_status = isset($_REQUEST['composite_status']) ? intval($_REQUEST['composite_status']) : - 1;
    $where = '';
    switch ($composite_status) {
        case CS_AWAIT_PAY:
            $where .= order_query_sql('await_pay');
            break;

        case CS_AWAIT_SHIP:
            $where .= order_query_sql('await_ship');
            break;

        case CS_FINISHED:
            $where .= order_query_sql('finished');
            break;
        default:
            if ($composite_status != - 1) {
                $where .= " AND o.order_status = '$composite_status' ";
            }
    }
    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

    $record_count = $db->getOne("SELECT COUNT(*) FROM " . $ecs->table('order_info') . " WHERE user_id = '$user_id'");

    /* 代码添加_68ECSHOP_20150909_STAR */
    // 未确认
    if ($composite_status == OS_UNCONFIRMED) {
        $record_count = $order_count['unconfirmed'];
    }
    // 待付款
    else if ($composite_status == CS_AWAIT_PAY) {
        $record_count = $order_count['await_pay'];
    }
    // 待发货
    else if ($composite_status == CS_AWAIT_SHIP) {
        $record_count = $order_count['await_ship'];
    }
    // 已完成
    else if ($composite_status == CS_FINISHED) {
        $record_count = $order_count['finished'];
    }
    /* 代码添加_68ECSHOP_20150909_END */

    $pager = get_pager('user.php', array(
        'act' => $action, 'composite_status' => $composite_status
            ), $record_count, $page, 5);

    $orders = get_user_orders_1($user_id, $pager['size'], $pager['start'], $where);

    foreach ($orders as $k_kuaidi => $v_kuaidi) {
        // 同城快递
        if ($v_kuaidi['shipping_name_2'] == "同城快递") {
            $kos_order_id = $db->getOne("select order_id from " . $ecs->table('kuaidi_order') . " where order_sn='" . $v_kuaidi['invoice_no'] . "'");
            $sql = "select * from " . $ecs->table('kuaidi_order_status') . " where order_id='" . $kos_order_id . "'  order by status_id desc";
            $res_status = $db->query($sql);
            $have_shipping_info = 0;
            $shipping_info = "";
            while ($row_status = $db->fetchRow($res_status)) {
                if ($row_status['status_display'] == 1) {
                    switch ($row_status['status_id']) {
                        case 1:
                            $shipping_info .= "您提交了订单，请等待确认。 (" . local_date('Y-m-d H:i:s', $row_status['status_time']) . ")";
                            break;
                        case 2:
                            $shipping_info .= "您的快件已经确认，等待快递员揽收。 (" . local_date('Y-m-d H:i:s', $row_status['status_time']) . ")";
                            break;
                        case 3:
                            $postman_id = $db->getOne("select postman_id from " . $ecs->table('kuaidi_order') . " where order_sn='" . $orders[$k_kuaidi]['invoice_no'] . "'");
                            $postman_info = $db->getRow("select postman_name, mobile from " . $ecs->table('postman') . " where postman_id=" . $postman_id);
                            $shipping_info .= "您的快件正在派送，快递员：" . $postman_info['postman_name'] . "，电话：" . $postman_info['mobile'] . " (" . local_date('Y-m-d H:i:s', $row_status['status_time']) . ")";
                            break;
                        case 4:
                            $shipping_info .= "您的快件已经签收。 (" . local_date('Y-m-d H:i:s', $row_status['status_time']) . ")";
                            break;
                        case 5:
                            $shipping_info .= "您的快件已被拒收。 (" . local_date('Y-m-d H:i:s', $row_status['status_time']) . ")";
                            break;
                        case 6:
                            $shipping_info .= "您拒收的快件已被退回。 (" . local_date('Y-m-d H:i:s', $row_status['status_time']) . ")";
                            break;
                        case 7:
                            $shipping_info .= "您的快件已经取消。 (" . local_date('Y-m-d H:i:s', $row_status['status_time']) . ")";
                            break;
                    }

                    $shipping_info .= "<br>";

                    if ($row_status['status_id'] >= 1) {
                        $have_shipping_info ++;
                    }
                }
            }
            if ($have_shipping_info) {
                $orders[$k_kuaidi]['result_content'] = $shipping_info;
            } else {
                $orders[$k_kuaidi]['result_content'] = '抱歉，暂时还没有该运单的物流信息哦！';
            }
        }
    }

    $merge = get_user_merge($user_id);

    $smarty->assign('merge', $merge);
    $smarty->assign('pager', $pager);
    $smarty->assign('orders', $orders);
    $smarty->display('user_transaction.dwt');
}

/* 查看订单详情 */

function action_order_detail() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/lib_transaction.php');
    include_once (ROOT_PATH . 'includes/lib_payment.php');
    include_once (ROOT_PATH . 'includes/lib_order.php');
    include_once (ROOT_PATH . 'includes/lib_clips.php');

    $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

    /* 订单详情 */

    $order = get_order_detail($order_id, $user_id);
    
    if($order['pay_code']!='unpaywap'){
    	$order['pay_online'] = base64_encode($order['pay_online']);
    	$order['pay_online']= str_replace('+','-',$order['pay_online']);//替换掉加号,防止url传输时被转成空格
    }
     
       
    $sql_invoices = "SELECT invoice_no,shipping_name FROM " . $GLOBALS['ecs']->table('delivery_order') . " WHERE order_id = " . $order['order_id'];
    $order['invoices'] = $GLOBALS['db']->getAll($sql_invoices);

    /* 退换货插件 www.68ecshop.com增加 */
    $shipping_time = $db->getOne("SELECT shipping_time FROM " . $ecs->table('order_info') . " WHERE order_id = '$order_id'");
    $now_time = gmtime();
    $not_back = 0;
    if ($GLOBALS['_CFG']['tuihuan_days_fahuo'] > 0) { // 退换货期限（发货后第几天起）:
        if (($now_time - $shipping_time) / 86400 < $GLOBALS['_CFG']['tuihuan_days_fahuo']) {
            $not_back = 1;
        }
    }
    if ($GLOBALS['_CFG']['tuihuan_days_qianshou'] > 0) { // 退换货期限（发货后第几天止）:
        if (($now_time - $shipping_time) / 86400 > $GLOBALS['_CFG']['tuihuan_days_qianshou']) {
            $not_back = 1;
        }
    }
    $smarty->assign('not_back', $not_back);
    /* 退换货插件 www.68ecshop.com增加 */
    if ($order === false) {
        $err->show($_LANG['back_home_lnk'], './');

        exit();
    }

    if ($db->getOne("select shipping_code from " . $ecs->table('shipping') . " where shipping_id=" . $order['shipping_id']) == "tc_express") {

        $order['tc_express'] = 1;

        $ko_order_sn = $db->getOne("select invoice_no from " . $ecs->table('delivery_order') . " where order_id=" . $order_id);
        if ($ko_order_sn) {
            $kos_order_id = $db->getOne("select order_id from " . $ecs->table('kuaidi_order') . " where order_sn='" . $ko_order_sn . "'");
        }

        $sql = "select * from " . $ecs->table('kuaidi_order_status') . " where order_id='$kos_order_id'  order by status_id";

        $res_status = $db->query($sql);
        $have_shipping_info = 0;
        $shipping_info = "";
        while ($row_status = $db->fetchRow($res_status)) {
            if ($row_status['status_display'] == 1) {
                switch ($row_status['status_id']) {
                    case 1:
                        $shipping_info .= "您提交了订单，请等待确认。 (" . local_date('Y-m-d H:i:s', $row_status['status_time']) . ")";
                        break;
                    case 2:
                        $shipping_info .= "您的快件已经确认，等待快递员揽收。 (" . local_date('Y-m-d H:i:s', $row_status['status_time']) . ")";
                        break;
                    case 3:
                        $postman_id = $db->getOne("select postman_id from " . $ecs->table('kuaidi_order') . " where order_sn='" . $order['invoice_no'] . "'");
                        $postman_info = $db->getRow("select postman_name, mobile from " . $ecs->table('postman') . " where postman_id=" . $postman_id);
                        $shipping_info .= "您的快件正在派送，快递员：" . $postman_info['postman_name'] . "，电话：" . $postman_info['mobile'] . " (" . local_date('Y-m-d H:i:s', $row_status['status_time']) . ")";
                        break;
                    case 4:
                        $shipping_info .= "您的快件已经签收。 (" . local_date('Y-m-d H:i:s', $row_status['status_time']) . ")";
                        break;
                    case 5:
                        $shipping_info .= "您的快件已被拒收。 (" . local_date('Y-m-d H:i:s', $row_status['status_time']) . ")";
                        break;
                    case 6:
                        $shipping_info .= "您拒收的快件已被退回。 (" . local_date('Y-m-d H:i:s', $row_status['status_time']) . ")";
                        break;
                    case 7:
                        $shipping_info .= "您的快件已经取消。 (" . local_date('Y-m-d H:i:s', $row_status['status_time']) . ")";
                        break;
                }

                $shipping_info .= "<br>";

                if ($row_status['status_id'] >= 1) {
                    $have_shipping_info ++;
                }
            }
        }
        if ($have_shipping_info) {
            $result_content = $shipping_info;
        } else {
            $result_content = '抱歉，暂时还没有该运单的物流信息哦！';
        }
    }
    $smarty->assign('result_content', $result_content);

    /* 是否显示添加到购物车 */
    if ($order['extension_code'] != GROUP_BUY_CODE && $order['extension_code'] != 'exchange_goods' && $order['extension_code'] != PRE_SALE_CODE && $order['extension_code'] != VIRTUAL_SALE_CODE) {
        $smarty->assign('allow_to_cart', 1);
    }

    /* 订单商品 */
    $goods_list = order_goods($order_id);
    foreach ($goods_list as $key => $value) {
        $goods_list[$key]['market_price'] = price_format($value['market_price'], false);
        $goods_list[$key]['goods_price'] = price_format($value['goods_price'], false);
        $goods_list[$key]['subtotal'] = price_format($value['subtotal'], false);

        $sql_back = "SELECT bg.*, bo.back_type FROM " . $ecs->table('back_goods') . " AS bg " . " LEFT JOIN " . $ecs->table('back_order') . " AS bo " . " ON bg.back_id = bo.back_id " . " WHERE bo.order_id = " . $order_id . " AND bg.goods_id = " . $value['goods_id'] . " AND bg.product_id = " . $value['product_id'] . " AND bg.status_back < 6";
        $back_info = $db->getRow($sql_back);

        if (count($back_info['back_id']) > 0) {
            switch ($back_info['status_back']) {
                case '3':
                    $sb = "已完成";
                    break;
                case '5':
                    $sb = "已申请";
                    break;
                // case '6' : $sb = ""; break;
                // case '7' : $sb = ""; break;
                default:
                    $sb = "正在";
                    break;
            }

            switch ($back_info['back_type']) {
                case '1':
                    $bt = "退货";
                    break;
                case '3':
                    $bt = "申请维修";
                    break;
                case '4':
                    $bt = "退款";
                    break;
                default:
                    break;
            }

            $shouhou = $sb . " " . $bt;
        } else {
            $shouhou = "正常";
        }

        $goods_list[$key]['shouhou'] = $shouhou;
    }

    /* 设置能否修改使用余额数 */
    if ($order['order_amount'] > 0) {
        if ($order['order_status'] == OS_UNCONFIRMED || $order['order_status'] == OS_CONFIRMED) {
            $user = user_info($order['user_id']);
            if ($user['user_money'] + $user['credit_line'] > 0) {
                //$smarty->assign('allow_edit_surplus', 1);
                //$smarty->assign('max_surplus', sprintf($_LANG['max_surplus'], $user['user_money']));
            }
        }
    }
    /* 代码删除 By  www.68ecshop.com Start */
//	/* 未发货，未付款时允许更换支付方式 */
//	if($order['order_amount'] > 0 && $order['pay_status'] == PS_UNPAYED && $order['shipping_status'] == SS_UNSHIPPED)
//	{
//		$payment_list = available_payment_list(false, 0, true);
//
//		/* 过滤掉当前支付方式和余额支付方式 */
//		if(is_array($payment_list))
//		{
//			foreach($payment_list as $key => $payment)
//			{
//				if($payment['pay_id'] == $order['pay_id'] || $payment['pay_code'] == 'balance')
//				{
//					unset($payment_list[$key]);
//				}
//			}
//		}
//		$smarty->assign('payment_list', $payment_list);
//	}
    /* 订单 支付 配送 状态语言项 */
//	$order['order_status'] = $_LANG['os'][$order['order_status']];
//	$order['pay_status'] = $_LANG['ps'][$order['pay_status']];
//	$order['shipping_status_id'] = $order['shipping_status']; // 代码增加 By
//	                                                          // www.68ecshop.com
//	$order['shipping_status'] = $_LANG['ss'][$order['shipping_status']];
    /* 代码删除 By  www.68ecshop.com End */
    /* 增值税发票_添加_START_www.68ecshop.com */
    /* 增值税发票收票地址 */
    if ($order['inv_type'] == 'vat_invoice') {
        $order['inv_complete_address'] = get_inv_complete_address($order);
    }
    /* 发票金额 */
    $order['formatted_inv_money'] = price_format($order['inv_money']);
    /* 增值税发票_添加_END_www.68ecshop.com */
    $smarty->assign('order', $order);
    /* 代码增加_start By www.68ecshop.com */
    $smarty->assign('mobile_phone', $GLOBALS['db']->getOne("select mobile_phone from " . $GLOBALS['ecs']->table('users') . " where user_id='$_SESSION[user_id]'"));
    foreach ($goods_list as $goods_key => $goods_val) {
        $sql_goods = "select count(*) from " . $ecs->table('back_order') . " where order_id='$order[order_id]' and goods_id='$goods_val[goods_id]'";
        $back_order_count = $db->getOne($sql_goods);
        $goods_list[$goods_key]['back_can'] = $back_order_count ? '0' : '1';
        /* 代码增加_start By www.68ecshop.com _20150804 */
        if ($goods_val['extension_code'] == 'virtual_good') {
            $virtual_goods_card = $db->getAll("select gc.card_sn,gc.end_date,buy_date from " . $ecs->table('virtual_goods_card') . " as gc join (select order_id,order_sn from " . $ecs->table('order_info') . " where order_id='$goods_val[order_id]') as oi on gc.order_sn = oi.order_sn");
            foreach ($virtual_goods_card as $k => $v) {
                $virtual_goods_card[$k]['end_date'] = empty($v['end_date']) ? '' : local_date('Y-m-d', $v['end_date']);
                $virtual_goods_card[$k]['buy_date'] = empty($v['buy_date']) ? '' : local_date('Y-m-d', $v['buy_date']);
                $virtual_goods_card[$k]['end_date_time'] = empty($v['end_date']) ? '' : $v['end_date'];
                $virtual_goods_card[$k]['buy_date_time'] = empty($v['buy_date']) ? '' : $v['buy_date'];
            }
            $goods_list[$goods_key]['virtual_goods_card'] = $virtual_goods_card;
        }
        /* 代码增加_end By www.68ecshop.com _20150804 */

        $goods_list[$key]['shouhou'] = $shouhou;
    }

    /* 设置能否修改使用余额数 */
    if ($order['order_amount'] > 0) {
        if ($order['order_status'] == OS_UNCONFIRMED || $order['order_status'] == OS_CONFIRMED) {
            $user = user_info($order['user_id']);
            if ($user['user_money'] + $user['credit_line'] > 0) {
                //$smarty->assign('allow_edit_surplus', 1);
                //$smarty->assign('max_surplus', sprintf($_LANG['max_surplus'], $user['user_money']));
            }
        }
    }

    /* 未发货，未付款时允许更换支付方式 */
    if ($order['order_amount'] > 0 && $order['pay_status'] == PS_UNPAYED && $order['shipping_status'] == SS_UNSHIPPED) {
        $payment_list = available_payment_list(false, 0, true);

        /* 过滤掉当前支付方式和余额支付方式 */
        if (is_array($payment_list)) {
            foreach ($payment_list as $key => $payment) {
                if ($payment['pay_id'] == $order['pay_id'] || $payment['pay_code'] == 'balance') {
                    unset($payment_list[$key]);
                }
            }
        }
        /* 代码修改 By  www.68ecshop.com Start */
//		$smarty->assign('payment_list', $payment_list);
        // 订单状态为无效、已取消、退货时不设置支付方法
        if ($order['order_status'] != OS_CANCELED && $order['order_status'] != OS_INVALID && $order['order_status'] != OS_RETURNED) {
            $smarty->assign('payment_list', $payment_list);
        }
        /* 代码修改 By  www.68ecshop.com End */
    }

    /* 订单 支付 配送 状态语言项 */
    $order['order_status'] = $_LANG['os'][$order['order_status']];
    $order['pay_status'] = $_LANG['ps'][$order['pay_status']];
    $order['shipping_status_id'] = $order['shipping_status']; // 代码增加 By
    // www.68ecshop.com
    $order['shipping_status'] = $_LANG['ss'][$order['shipping_status']];
    /* 增值税发票_添加_START_www.68ecshop.com */
    /* 增值税发票收票地址 */
    if ($order['inv_type'] == 'vat_invoice') {
        $order['inv_complete_address'] = get_inv_complete_address($order);
    }
    /* 发票金额 */
    $order['formatted_inv_money'] = price_format($order['inv_money']);
    /* 增值税发票_添加_END_www.68ecshop.com */
    $smarty->assign('order', $order);
    /* 代码增加_start By www.68ecshop.com */
    foreach ($goods_list as $goods_key => $goods_val) {
        $sql_goods = "select count(*) from " . $ecs->table('back_order') . " where order_id='$order[order_id]' and goods_id='$goods_val[goods_id]'";
        $back_order_count = $db->getOne($sql_goods);
        $goods_list[$goods_key]['back_can'] = $back_order_count ? '0' : '1';
    }
    /* 代码增加_end By www.68ecshop.com */
    $smarty->assign('goods_list', $goods_list);
    $smarty->display('user_transaction.dwt');
}

/*
 * 代码增加_start By www.68ecshop.com
 * 退换货订单详情
 */

function action_back_order_detail() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    $back_id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    $sql = 'SELECT shipping_id, shipping_code, shipping_name ' . 'FROM ' . $GLOBALS['ecs']->table('shipping') . 'WHERE enabled = 1 and supplier_id = 0   ORDER BY shipping_order';
    $shipping_list = $db->getAll($sql);

    $smarty->assign('shipping_list', $shipping_list);

    $sql = "SELECT * " . " FROM " . $GLOBALS['ecs']->table('back_order') . " WHERE back_id= '$back_id' ";
    $back_shipping = $db->getRow($sql);

    $sql_og = "SELECT * FROM " . $GLOBALS['ecs']->table('back_goods') . " WHERE back_id = " . $back_id;
    $back_shipping['goods_list'] = $GLOBALS['db']->getAll($sql_og);

    $back_shipping['add_time'] = local_date("Y-m-d H:i", $back_shipping['add_time']);
    $back_shipping['refund_money_1'] = price_format($back_shipping['refund_money_1'], false);
    $back_shipping['refund_money_2'] = price_format($back_shipping['refund_money_2'], false);
    $back_shipping['refund_type_name'] = $back_shipping['refund_type'] == '0' ? '' : ($back_shipping['refund_type'] == '1' ? '退回用户余额' : '线下退款');
    $back_shipping['country_name'] = $db->getOne("SELECT region_name FROM " . $ecs->table('region') . " WHERE region_id = '$back_shipping[country]'");
    $back_shipping['province_name'] = $db->getOne("SELECT region_name FROM " . $ecs->table('region') . " WHERE region_id = '$back_shipping[province]'");
    $back_shipping['city_name'] = $db->getOne("SELECT region_name FROM " . $ecs->table('region') . " WHERE region_id = '$back_shipping[city]'");
    $back_shipping['district_name'] = $db->getOne("SELECT region_name FROM " . $ecs->table('region') . " WHERE region_id = '$back_shipping[district]'");

    $back_shipping['status_back_1'] = $back_shipping['status_back'];
    $back_shipping['status_back'] = $_LANG['bos'][$back_shipping['status_back']] . ($back_shipping['status_back'] == '3' && $back_shipping['back_type'] && $back_shipping['back_type'] != '4' ? ' (换回商品已寄出，请注意查收) ' : '');
    $back_shipping['status_refund'] = $_LANG['bps'][$back_shipping['status_refund']];

    $smarty->assign('back_shipping', $back_shipping);

    // 退货商品 + 换货商品 详细信息
    $list_backgoods = array();
    $sql = "select * from " . $ecs->table('back_goods') . " where back_id = '$back_id' order by back_type ";
    $res_backgoods = $db->query($sql);
    while ($row_backgoods = $db->fetchRow($res_backgoods)) {
        $back_type_temp = $row_backgoods['back_type'] == '2' ? '1' : $row_backgoods['back_type'];
        $list_backgoods[$back_type_temp]['goods_list'][] = array(
            'goods_name' => $row_backgoods['goods_name'], 'goods_attr' => $row_backgoods['goods_attr'], 'back_goods_number' => $row_backgoods['back_goods_number'], 'back_goods_money' => price_format($row_backgoods['back_goods_number'] * $row_backgoods['back_goods_price'], false), 'status_back' => $_LANG['bos'][$row_backgoods['status_back']] . ($row_backgoods['status_back'] == '3' && $row_backgoods['back_type'] && $row_backgoods['back_type'] != '4' ? ' (换回商品已寄出，请注意查收) ' : ''), 'status_refund' => $_LANG['bps'][$row_backgoods['status_refund']], 'back_type' => $row_backgoods['back_type']
        );
    }
    $smarty->assign('list_backgoods', $list_backgoods);

    /* 回复留言 www.68ecshop.com增加 */
    $res = $db->getAll("SELECT * FROM " . $ecs->table('back_replay') . " WHERE back_id = '$back_id' ORDER BY add_time ASC");
    foreach ($res as $value) {
        $value['add_time'] = local_date("Y-m-d H:i", $value['add_time']);
        $back_replay[] = $value;
    }

    $smarty->assign('back_replay', $back_replay);

    $smarty->assign('back_id', $back_id);
    $smarty->display('user_transaction.dwt');
}

/*
 * 留言回复
 */

function action_back_replay() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    $back_id = intval($_REQUEST['back_id']);
    $message = $_POST['message'];
    $add_time = gmtime();

    $db->query("INSERT INTO " . $ecs->table('back_replay') . " (back_id, message, add_time, type) VALUES ('$back_id', '$message', '$add_time', 1)");

    show_message('恭喜，回复成功！', '返回', 'user.php?act=back_order_detail&id=' . $back_id);
}

/*
 * 取消退换货订单
 */

function action_del_back_order() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    $back_id = intval($_REQUEST['id']);
    $sql = "select status_back from " . $ecs->table('back_order') . " where back_id='$back_id' ";
    $status_back = $db->getOne($sql);
    if ($status_back != 0 && $status_back != 5) {
        show_message('对不起，该退货单无法取消', '返回退货订单列表页');
    } else {
        $sql = "update " . $ecs->table('back_goods') . " set status_back = 8 where back_id='$back_id' ";
        $db->query($sql);
        $sql = "update " . $ecs->table('back_order') . " set status_back = 8 where back_id='$back_id' ";
        $db->query($sql);
        show_message('恭喜，您已经成功取消该退货单', '返回退货订单列表页', 'user.php?act=back_list', 'info');
    }
}

/*
 * 更新退换货订单的快递方式和运单号
 */

function action_back_order_detail_edit() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    if (empty($_POST['shipping_id'])) {
        show_message('快递公司不能为空');
    }
    if (empty($_POST['invoice_no'])) {
        show_message('快递运单号不能为空');
    }
    $back_id = !empty($_POST['back_id']) ? intval($_POST['back_id']) : 0;
    $invoice_no = trim($_POST['invoice_no']);
    $shipping_id = intval($_POST['shipping_id']);
    if ($shipping_id) {
        $sql = "SELECT shipping_name FROM " . $GLOBALS['ecs']->table('shipping') . " where shipping_id='$shipping_id' ";
        $shipping_name = $db->getOne($sql);
    }
    $sql = "update " . $ecs->table('back_order') . " set shipping_id='$shipping_id', shipping_name='$shipping_name', invoice_no='$invoice_no' where back_id='$back_id' ";
    $db->query($sql);
    show_message('恭喜，您已经成功更新快递方式和运单号', '返回退货订单详情页');
}

function action_back_list() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    $action = $GLOBALS['action'];

    include_once (ROOT_PATH . 'includes/lib_transaction.php');

    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

    $record_count = $db->getOne("SELECT COUNT(*) FROM " . $ecs->table('back_order') . " WHERE user_id = '$user_id'");

    $pager = get_pager('user.php', array(
        'act' => $action
            ), $record_count, $page);

    $orders = get_user_backorders($user_id, $pager['size'], $pager['start']);

    $smarty->assign('pager', $pager);
    $smarty->assign('orders', $orders);
    $smarty->display('user_transaction.dwt');
}

/* 新“退换货”订单表单 */

function action_back_order() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    $order_id = !empty($_REQUEST['order_id']) ? intval($_REQUEST['order_id']) : 0;
    if (!$_REQUEST['order_all']) {
        $goods_id = !empty($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
        $product_id = !empty($_REQUEST['product_id']) ? intval($_REQUEST['product_id']) : 0;
        $sql = "select og.goods_id, og.goods_name, og.goods_sn, og.goods_number, og.goods_price, og.product_id, og.goods_attr, o.order_id, o.order_sn, o.user_id, o.shipping_time_end " . " from " . $GLOBALS['ecs']->table('order_info') . " AS o left join " . $GLOBALS['ecs']->table('order_goods') . " AS og " . " on o.order_id=og.order_id where og.goods_id='$goods_id' and og.order_id='$order_id' and og.product_id='$product_id'";
        $row_goods = $GLOBALS['db']->getRow($sql);

        if (!$row_goods || $row_goods['user_id'] != $_SESSION['user_id']) {
            show_message('对不起！您没权限针对该商品发起退款/退货及维修', '返回订单列表页', 'user.php?act=order_list', 'info');
        } else {
            $row_goods['total_price'] = $row_goods['goods_price'] * $row_goods['goods_number'];
            $row_goods['goods_price_format'] = price_format($row_goods['goods_price'], false);
            $row_goods['total_price_format'] = price_format($row_goods['total_price'], false);
            $smarty->assign('back_goods', $row_goods);

            $properties = get_goods_properties($goods_id); // 获得商品的规格和属性
            $smarty->assign('specification', $properties['spe']); // 商品规格
        }
    } else {
        $sql_oi = "SELECT * FROM " . $GLOBALS['ecs']->table('order_info') . " WHERE order_id = " . $order_id;
        $order_info = $GLOBALS['db']->getRow($sql_oi);
        $sql_og = "SELECT * FROM " . $GLOBALS['ecs']->table('order_goods') . " WHERE order_id = " . $order_id;
        $goods_list = $GLOBALS['db']->getAll($sql_og);
        foreach ($goods_list as $key => $goods_info) {
            $goods_info['total_price'] = $goods_info['goods_price'] * $goods_info['goods_number'];
            $goods_list[$key]['goods_price_format'] = price_format($goods_info['goods_price'], false);
            $goods_list[$key]['total_price_format'] = price_format($goods_info['total_price'], false);
        }
        $order_info['goods_list'] = $goods_list;

        if (!$order_info || $order_info['user_id'] != $_SESSION['user_id']) {
            show_message('对不起！您没权限针对该订单发起退款', '返回订单列表页', 'user.php?act=order_list', 'info');
        } else {
            $smarty->assign('order_info', $order_info);

            $properties = get_goods_properties($goods_id); // 获得商品的规格和属性
            $smarty->assign('specification', $properties['spe']); // 商品规格
        }
    }

    // 收货地址 www.68ecshop.com增加
    include_once (ROOT_PATH . 'includes/lib_transaction.php');
    $order = $db->getRow("SELECT * FROM " . $ecs->table('order_info') . " WHERE order_id='$order_id'");
    $smarty->assign('order', $order);
    $smarty->assign('shop_province', get_regions(1, $order['country']));
    $smarty->assign('shop_city', get_regions(2, $order['province']));
    $smarty->assign('shop_district', get_regions(3, $order['city']));
    $smarty->assign('name_of_region', array(
        $_CFG['name_of_region_1'], $_CFG['name_of_region_2'], $_CFG['name_of_region_3'], $_CFG['name_of_region_4']
    ));
    $smarty->assign('country_list', get_regions());

    $smarty->display('user_transaction.dwt');
}

/* 保存退换货订单 */

function action_back_order_act() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    $add_time = gmtime();
    $order_id = !empty($_POST['order_id']) ? trim($_POST['order_id']) : "0";
    if (!$_POST['order_all']) {
        $order_sn = !empty($_POST['order_sn']) ? trim($_POST['order_sn']) : "";
        $goods_id = !empty($_POST['goods_id']) ? trim($_POST['goods_id']) : "";
        $goods_name = !empty($_POST['goods_name']) ? trim($_POST['goods_name']) : "";
        $goods_sn = !empty($_POST['goods_sn']) ? trim($_POST['goods_sn']) : "";
    }
    $back_reason = !empty($_POST['back_reason']) ? trim($_POST['back_reason']) : "";
    $country = intval($_POST['country']);
    $province = intval($_POST['province']);
    $city = intval($_POST['city']);
    $district = intval($_POST['district']);
    $consignee = !empty($_POST['back_consignee']) ? trim($_POST['back_consignee']) : "";
    $address = !empty($_POST['back_address']) ? trim($_POST['back_address']) : "";
    $zipcode = !empty($_POST['back_zipcode']) ? trim($_POST['back_zipcode']) : "";
    $mobile = !empty($_POST['back_mobile']) ? trim($_POST['back_mobile']) : "";
    $postscript = !empty($_POST['back_postscript']) ? trim($_POST['back_postscript']) : "";
    $imgs = ($_POST['imgs']) ? implode(',', $_POST['imgs']) : '';
    $back_pay = intval($_POST['back_pay']);
    $back_type = intval($_POST['back_type']);
    $back_type_list = $_POST['back_type'];

    if (!$order_id) {
        show_message('对不起，您进行了错误操作！');
        exit();
    }

    $sql = "select * from " . $ecs->table('order_info') . " where order_id='$order_id' ";
    $order_info = $db->getRow($sql);

    if (empty($order_info)) {
        show_message('对不起，此订单不存在！');
        exit();
    }

    if ($_POST['order_all']) {
        $order_sn = $order_info['order_sn'];
        $goods_id = 0;

        $sql_og = "SELECT * FROM " . $GLOBALS['ecs']->table('order_goods') . " WHERE order_id = " . $order_id;
        $order_info['goods_list'] = $GLOBALS['db']->getAll($sql_og);
    }

    $sql = "insert into " . $ecs->table('back_order') . "(order_sn, order_id, goods_id,  user_id, shipping_fee, consignee, address, " . "zipcode, mobile, add_time, postscript , back_reason, goods_name, imgs, back_pay, country, province, city, district, back_type, status_back, supplier_id) " . " values('$order_sn', '$order_id', '$goods_id',  '$user_id', '$order_info[shipping_fee]', '$consignee', '$address', " . "'$zipcode', '$mobile', '$add_time', '$postscript', '$back_reason', '$goods_name', '$imgs', '$back_pay', '$country', '$province', '$city', '$district', '$back_type', '5', '$order_info[supplier_id]')";

    $db->query($sql);

    // 插入退换货商品 80_back_goods
    $back_id = $db->insert_id();
    $have_tuikuan = 0; // 是否有退货
    // foreach($back_type_list as $back_type)
    // {
    if ($back_type == 1) {
        $have_tuikuan = 1;
        $tui_goods_number = $_REQUEST['tui_goods_number'] ? intval($_REQUEST['tui_goods_number']) : 1;
        $sql = "insert into " . $ecs->table('back_goods') . "(back_id, goods_id, goods_name, goods_sn, product_id, goods_attr, back_type, " . "back_goods_number, back_goods_price, status_back ) " . " values('$back_id', '$goods_id', '$goods_name', '$goods_sn', '$_REQUEST[product_id_tui]', '$_REQUEST[goods_attr_tui]', '0', " . " '$tui_goods_number', '$_REQUEST[tui_goods_price]', '5') ";
        $db->query($sql);
    }
    if ($back_type == 4) {
        $have_tuikuan = 1;
        $have_tuikuan2 = 1;
        $price_refund_all = 0;

        foreach ($order_info['goods_list'] as $goods_info) {
            $price_refund_all += ($goods_info['goods_price'] * $goods_info['goods_number']);

            $sql = "INSERT INTO " . $GLOBALS['ecs']->table('back_goods') . "(back_id, goods_id, goods_name, goods_sn, product_id, goods_attr, back_type, " . "back_goods_number, back_goods_price, status_back) " . " values('$back_id', '" . $goods_info['goods_id'] . "', '" . $goods_info['goods_name'] . "', '" . $goods_info['goods_sn'] . "', '" . $goods_info['product_id'] . "', '" . $goods_info['goods_attr'] . "', '4', '" . $goods_info['goods_number'] . "', '" . $goods_info['goods_price'] . "', '5') ";
            $db->query($sql);
        }
    }
    if ($back_type == 2) {
        $huan_count = count($_POST['product_id_huan']);
        if ($huan_count) {
            $sql = "insert into " . $ecs->table('back_goods') . "(back_id, goods_id, goods_name, goods_sn, product_id, goods_attr, back_type, status_refund, back_goods_number, status_back) " . " values('$back_id', '$goods_id', '$goods_name', '$goods_sn', '$_REQUEST[product_id_tui]', '$_REQUEST[goods_attr_tui]', '1', '9', '$huan_count', '5') ";
            $db->query($sql);
            $parent_id_huan = $db->insert_id();
            foreach ($_POST['product_id_huan'] as $pid_key => $pid_huan) {
                $sql = "insert into " . $ecs->table('back_goods') . "(back_id, goods_id, goods_name, goods_sn, product_id, goods_attr,  back_type, parent_id, status_refund, back_goods_number, status_back) " . "values('$back_id', '$goods_id', '$goods_name', '$goods_sn',  '$pid_huan', '" . $_POST['goods_attr_huan'][$pid_key] . "', '2', '$parent_id_huan', '9', '1', '5')";
                $db->query($sql);
            }
        }
    }
    if ($back_type == 3) {
        $have_weixiu = 1;
        $tui_goods_number = $_REQUEST['tui_goods_number'] ? intval($_REQUEST['tui_goods_number']) : 1;
        $sql = "insert into " . $ecs->table('back_goods') . "(back_id, goods_id, goods_name, goods_sn, product_id, goods_attr, back_type, " . "back_goods_number, back_goods_price, status_back) " . " values('$back_id', '$goods_id', '$goods_name', '$goods_sn', '$_REQUEST[product_id_tui]', '$_REQUEST[goods_attr_tui]', '3', " . " '$tui_goods_number', '$_REQUEST[tui_goods_price]', '5') ";
        $db->query($sql);
    }
    // }

    /* 更新back_order */
    if ($have_tuikuan) {
        if ($_POST['order_all']) {
            $price_refund = $GLOBALS['db']->getOne("SELECT money_paid FROM " . $GLOBALS['ecs']->table('order_info') . " WHERE order_id = " . $order_id);
        } else {
            $price_refund = $_REQUEST['tui_goods_price'] * $tui_goods_number;
        }
        $price_refund1 = $GLOBALS['db']->getOne("SELECT integral FROM " . $GLOBALS['ecs']->table('order_info') . " WHERE order_id = " . $order_id);
        $price_refund = $price_refund + $price_refund1;
        $sql = "update " . $ecs->table('back_order') . " set refund_money_1= '$price_refund' where back_id='$back_id' ";
        $db->query($sql);
    } else {
        $sql = "update " . $ecs->table('back_order') . " set status_refund= '9' where back_id='$back_id' ";
        $db->query($sql);
    }

    if ($have_tuikuan2) {
        $smarty->assign('back_act_w', 'tuikuan');
    } else if ($have_weixiu) {
        $smarty->assign('back_act_w', 'weixiu');
    } else {
        $smarty->assign('back_act_w', 'tuihuo');
    }

    $smarty->assign('back_consignee', $consignee);
    $smarty->assign('back_address', $address);
    $smarty->assign('back_zipcode', $zipcode);

    $smarty->display('user_transaction.dwt');
}

// AJAX调用
function action_add_huan_goods() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once ('includes/cls_json.php');
    include_once ('includes/lib_order.php');
    $json = new JSON();

    $result = array(
        'error' => 0, 'content' => ''
    );

    $_POST['goods'] = strip_tags(urldecode($_POST['goods']));
    $_POST['goods'] = json_str_iconv($_POST['goods']);
    $goods = $json->decode($_POST['goods']);
    $spec = $goods->spec;
    $goods_id = $goods->goods_id;
    $goods_name = $db->getOne("select goods_name from " . $ecs->table('goods') . " where goods_id='$goods_id' ");

    /* 如果商品有规格则取规格商品信息 配件除外 */
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('products') . " WHERE goods_id = '$goods_id' LIMIT 0, 1";
    $prod = $GLOBALS['db']->getRow($sql);
    if (is_spec($spec) && !empty($prod)) {
        $product_info = get_products_info($goods_id, $spec);
    }
    $goods_attr = get_goods_attr_info($spec);

    $result['error'] = 1;
    $result['goods_name'] = $goods_name . "  ";
    $result['product_id'] = $product_info['product_id'];
    $result['product_id'] = $result['product_id'] == 'null' ? '0' : intval($result['product_id']);
    $result['content'] = addslashes($goods_attr);
    die($json->encode($result));
}

/* 代码增加_end By www.68ecshop.com */

/* 取消订单 */

function action_cancel_order() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/lib_transaction.php');
    include_once (ROOT_PATH . 'includes/lib_order.php');

    $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

    if (cancel_order($order_id, $user_id)) {
        ecs_header("Location: user.php?act=order_list\n");
        exit();
    } else {
        $err->show($_LANG['order_list_lnk'], 'user.php?act=order_list');
    }
}

/* 收货地址列表界面 */

function action_address_list() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/lib_transaction.php');
    include_once (ROOT_PATH . 'languages/' . $_CFG['lang'] . '/shopping_flow.php');
    $smarty->assign('lang', $_LANG);

    /* 取得国家列表、商店所在国家、商店所在国家的省列表 */
    $smarty->assign('country_list', get_regions());
    $smarty->assign('shop_province_list', get_regions(1, $_CFG['shop_country']));

    /* 获得用户所有的收货人信息 */
    $consignee_list = get_consignee_list($_SESSION['user_id']);

    if (count($consignee_list) < 5 && $_SESSION['user_id'] > 0) {
        /* 如果用户收货人信息的总数小于5 则增加一个新的收货人信息 */
        $consignee_list[] = array(
            // 如果Session中有有效的email则放入
            'country' => $_CFG['shop_country'], 'email' => isset($_SESSION['email']) && is_email($_SESSION['email']) ? $_SESSION['email'] : ''
        );
    }

    $smarty->assign('consignee_list', $consignee_list);

    // 取得国家列表，如果有收货人列表，取得省市区列表
    foreach ($consignee_list as $region_id => $consignee) {
        $consignee['country'] = isset($consignee['country']) ? intval($consignee['country']) : 0;
        $consignee['province'] = isset($consignee['province']) ? intval($consignee['province']) : 0;
        $consignee['city'] = isset($consignee['city']) ? intval($consignee['city']) : 0;

        $province_list[$region_id] = get_regions(1, $consignee['country']);
        $city_list[$region_id] = get_regions(2, $consignee['province']);
        $district_list[$region_id] = get_regions(3, $consignee['city']);
    }

    /* 获取默认收货ID */
    $address_id = $db->getOne("SELECT address_id FROM " . $ecs->table('users') . " WHERE user_id='$user_id'");

    // 赋值于模板
    $smarty->assign('real_goods_count', 1);
    $smarty->assign('shop_country', $_CFG['shop_country']);
    $smarty->assign('shop_province', get_regions(1, $_CFG['shop_country']));
    $smarty->assign('province_list', $province_list);
    $smarty->assign('address', $address_id);
    $smarty->assign('city_list', $city_list);
    $smarty->assign('district_list', $district_list);
    $smarty->assign('currency_format', $_CFG['currency_format']);
    $smarty->assign('integral_scale', $_CFG['integral_scale']);
    $smarty->assign('name_of_region', array(
        $_CFG['name_of_region_1'], $_CFG['name_of_region_2'], $_CFG['name_of_region_3'], $_CFG['name_of_region_4']
    ));

    $smarty->display('user_transaction.dwt');
}

/* 添加/编辑收货地址的处理 */

function action_act_edit_address() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/lib_transaction.php');
    include_once (ROOT_PATH . 'languages/' . $_CFG['lang'] . '/shopping_flow.php');
    $smarty->assign('lang', $_LANG);

    $address = array(
        'user_id' => $user_id, 'address_id' => intval($_POST['address_id']), 'country' => isset($_POST['country']) ? intval($_POST['country']) : 0, 'province' => isset($_POST['province']) ? intval($_POST['province']) : 0, 'city' => isset($_POST['city']) ? intval($_POST['city']) : 0, 'district' => isset($_POST['district']) ? intval($_POST['district']) : 0, 'address' => isset($_POST['address']) ? compile_str(trim($_POST['address'])) : '', 'consignee' => isset($_POST['consignee']) ? compile_str(trim($_POST['consignee'])) : '', 'email' => isset($_POST['email']) ? compile_str(trim($_POST['email'])) : '', 'tel' => isset($_POST['tel']) ? compile_str(make_semiangle(trim($_POST['tel']))) : '', 'mobile' => isset($_POST['mobile']) ? compile_str(make_semiangle(trim($_POST['mobile']))) : '',
        'best_time' => isset($_POST['best_time']) ? compile_str(trim($_POST['best_time'])) : '', 'sign_building' => isset($_POST['sign_building']) ? compile_str(trim($_POST['sign_building'])) : '', 'zipcode' => isset($_POST['zipcode']) ? compile_str(make_semiangle(trim($_POST['zipcode']))) : ''
    );

    if (update_address($address)) {
        show_message($_LANG['edit_address_success'], $_LANG['address_list_lnk'], 'user.php?act=address_list');
    }
}

/* 删除收货地址 */

function action_drop_consignee() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once ('includes/lib_transaction.php');

    $consignee_id = intval($_GET['id']);

    if (drop_consignee($consignee_id)) {
        ecs_header("Location: user.php?act=address_list\n");
        exit();
    } else {
        show_message($_LANG['del_address_false']);
    }
}

/* 显示收藏商品列表 */

function action_collection_list() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    $action = $GLOBALS['action'];

    include_once (ROOT_PATH . 'includes/lib_clips.php');

    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

    $record_count = $db->getOne("SELECT COUNT(*) FROM " . $ecs->table('collect_goods') . " WHERE user_id='$user_id' ORDER BY add_time DESC");

    $pager = get_pager('user.php', array(
        'act' => $action
            ), $record_count, $page);
    $smarty->assign('pager', $pager);
    $smarty->assign('goods_list', get_collection_goods($user_id, $pager['size'], $pager['start']));
    $smarty->assign('url', $ecs->url());
    $lang_list = array(
        'UTF8' => $_LANG['charset']['utf8'], 'GB2312' => $_LANG['charset']['zh_cn'], 'BIG5' => $_LANG['charset']['zh_tw']
    );
    $smarty->assign('lang_list', $lang_list);
    $smarty->assign('user_id', $user_id);
    $smarty->display('user_clips.dwt');
}

/* 删除收藏的商品 */

function action_delete_collection() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/lib_clips.php');

    $collection_id = isset($_GET['collection_id']) ? intval($_GET['collection_id']) : 0;

    if ($collection_id > 0) {

        $goods_id = $db->getOne(" select goods_id from " . $ecs->table('collect_goods') . "  WHERE rec_id='$collection_id' AND user_id ='$user_id'");

        //删除商品详情缓存
        $cache_id = $goods_id . '-' . $_SESSION['user_rank'] . '-' . $_CFG['lang'];
        $cache_id = sprintf('%X', crc32($cache_id));
        $smarty->clear_cache('goods.dwt', $cache_id);

        $db->query('DELETE FROM ' . $ecs->table('collect_goods') . " WHERE rec_id='$collection_id' AND user_id ='$user_id'");
    }

    ecs_header("Location: user.php?act=collection_list\n");
    exit();
}

/* 添加关注商品 */

function action_add_to_attention() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    $rec_id = (int) $_GET['rec_id'];
    if ($rec_id) {
        $db->query('UPDATE ' . $ecs->table('collect_goods') . "SET is_attention = 1 WHERE rec_id='$rec_id' AND user_id ='$user_id'");
    }
    ecs_header("Location: user.php?act=collection_list\n");
    exit();
}

/* 取消关注商品 */

function action_del_attention() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    $rec_id = (int) $_GET['rec_id'];
    if ($rec_id) {
        $db->query('UPDATE ' . $ecs->table('collect_goods') . "SET is_attention = 0 WHERE rec_id='$rec_id' AND user_id ='$user_id'");
    }
    ecs_header("Location: user.php?act=collection_list\n");
    exit();
}

/* 显示关注的店铺列表 */

function action_follow_shop() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    $action = $GLOBALS['action'];

    include_once (ROOT_PATH . 'includes/lib_clips.php');

    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

    $record_count = $db->getOne("SELECT COUNT(*) FROM " . $ecs->table('supplier_guanzhu') . " WHERE userid='$user_id'");

    $pager = get_pager('user.php', array(
        'act' => $action
            ), $record_count, $page);
    $smarty->assign('pager', $pager);
    $smarty->assign('shop_list', get_follow_shops($user_id, $pager['size'], $pager['start']));
    $smarty->assign('url', $ecs->url());
    $lang_list = array(
        'UTF8' => $_LANG['charset']['utf8'], 'GB2312' => $_LANG['charset']['zh_cn'], 'BIG5' => $_LANG['charset']['zh_tw']
    );
    $smarty->assign('lang_list', $lang_list);
    $smarty->assign('user_id', $user_id);
    $smarty->display('user_clips.dwt');
}

/* 取消关注商品 */

function action_del_follow() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    $rec_id = (int) $_GET['rec_id'];
    if ($rec_id) {
        $db->query('DELETE FROM ' . $ecs->table('supplier_guanzhu') . " WHERE id='$rec_id' AND userid ='$user_id'");
    }
    ecs_header("Location: user.php?act=follow_shop\n");
    exit();
}

/* 显示留言列表 */

function action_message_list() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    $action = $GLOBALS['action'];

    include_once (ROOT_PATH . 'includes/lib_clips.php');

    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

    $order_id = empty($_GET['order_id']) ? 0 : intval($_GET['order_id']);
    $order_info = array();

    /* 获取用户留言的数量 */
    if ($order_id) {
        $sql = "SELECT COUNT(*) FROM " . $ecs->table('feedback') . " WHERE parent_id = 0 AND order_id = '$order_id' AND user_id = '$user_id'";
        $order_info = $db->getRow("SELECT * FROM " . $ecs->table('order_info') . " WHERE order_id = '$order_id' AND user_id = '$user_id'");
        $order_info['url'] = 'user.php?act=order_detail&order_id=' . $order_id;
    } else {
        $sql = "SELECT COUNT(*) FROM " . $ecs->table('feedback') . " WHERE parent_id = 0 AND user_id = '$user_id' AND user_name = '" . $_SESSION['user_name'] . "' AND order_id=0";
    }

    $record_count = $db->getOne($sql);
    $act = array(
        'act' => $action
    );

    if ($order_id != '') {
        $act['order_id'] = $order_id;
    }

    $pager = get_pager('user.php', $act, $record_count, $page, 5);

    $smarty->assign('message_list', get_message_list($user_id, $_SESSION['user_name'], $pager['size'], $pager['start'], $order_id));
    $smarty->assign('pager', $pager);
    $smarty->assign('order_info', $order_info);
    $smarty->display('user_clips.dwt');
}

/* 显示评论列表 */

function action_comment_list() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    $action = $GLOBALS['action'];

    include_once (ROOT_PATH . 'includes/lib_clips.php');

    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

    /* 获取用户留言的数量 */
    $sql = "SELECT COUNT(*) FROM " . $ecs->table('comment') . " WHERE parent_id = 0 AND user_id = '$user_id'";
    $record_count = $db->getOne($sql);
    $pager = get_pager('user.php', array(
        'act' => $action
            ), $record_count, $page, 5);

    $smarty->assign('comment_list', get_comment_list($user_id, $pager['size'], $pager['start']));
    $smarty->assign('pager', $pager);
    $smarty->display('user_clips.dwt');
}

/* 添加我的留言 */

function action_act_add_message() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/lib_clips.php');

    $message = array(
        'user_id' => $user_id, 'user_name' => $_SESSION['user_name'], 'user_email' => $_SESSION['email'], 'msg_type' => isset($_POST['msg_type']) ? intval($_POST['msg_type']) : 0, 'msg_title' => isset($_POST['msg_title']) ? trim($_POST['msg_title']) : '', 'msg_content' => isset($_POST['msg_content']) ? trim($_POST['msg_content']) : '', 'order_id' => empty($_POST['order_id']) ? 0 : intval($_POST['order_id']), 'upload' => (isset($_FILES['message_img']['error']) && $_FILES['message_img']['error'] == 0) || (!isset($_FILES['message_img']['error']) && isset($_FILES['message_img']['tmp_name']) && $_FILES['message_img']['tmp_name'] != 'none') ? $_FILES['message_img'] : array()
    );

    if (add_message($message)) {
        show_message($_LANG['add_message_success'], $_LANG['message_list_lnk'], 'user.php?act=message_list&order_id=' . $message['order_id'], 'info');
    } else {
        $err->show($_LANG['message_list_lnk'], 'user.php?act=message_list');
    }
}

/* 标签云列表 */

function action_tag_list() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/lib_clips.php');

    $good_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    $smarty->assign('tags', get_user_tags($user_id));
    $smarty->assign('tags_from', 'user');
    $smarty->display('user_clips.dwt');
}

/* 删除标签云的处理 */

function action_act_del_tag() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/lib_clips.php');

    $tag_words = isset($_GET['tag_words']) ? trim($_GET['tag_words']) : '';
    delete_tag($tag_words, $user_id);

    ecs_header("Location: user.php?act=tag_list\n");
    exit();
}

/* 显示缺货登记列表 */

function action_booking_list() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    $action = $GLOBALS['action'];

    include_once (ROOT_PATH . 'includes/lib_clips.php');

    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

    /* 获取缺货登记的数量 */
    $sql = "SELECT COUNT(*) " . "FROM " . $ecs->table('booking_goods') . " AS bg, " . $ecs->table('goods') . " AS g " . "WHERE bg.goods_id = g.goods_id AND user_id = '$user_id'";
    $record_count = $db->getOne($sql);
    $pager = get_pager('user.php', array(
        'act' => $action
            ), $record_count, $page);
    // jx 缺货信息添加商家名称和商品图片
    $booking = get_booking_list($user_id, $pager['size'], $pager['start']);
    foreach ($booking as $key => $value) {
        if ($value['supplier_id'] == 0) {
            $sql = "SELECT value FROM" . $ecs->table('shop_config') . "WHERE code='shop_name'";
            $booking[$key]['supplier_id'] = $value['supplier_id'];
            $booking[$key]['supplier_name'] = $db->getOne($sql);
        } else {
            $sql = "SELECT supplier_name FROM " . $ecs->table('supplier') . "WHERE supplier_id='" . $value['supplier_id'] . "'";
            $booking[$key]['supplier_id'] = $value['supplier_id'];
            $booking[$key]['supplier_name'] = $db->getOne($sql);
        }
    }
    $smarty->assign('booking_list', $booking);
    $smarty->assign('pager', $pager);
    $smarty->display('user_clips.dwt');
}

/* 添加缺货登记页面 */

function action_add_booking() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/lib_clips.php');

    $goods_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($goods_id == 0) {
        show_message($_LANG['no_goods_id'], $_LANG['back_page_up'], '', 'error');
    }

    /* 根据规格属性获取货品规格信息 */
    $goods_attr = '';
    if ($_GET['spec'] != '') {
        $goods_attr_id = $_GET['spec'];

        $attr_list = array();
        $sql = "SELECT a.attr_name, g.attr_value " . "FROM " . $ecs->table('goods_attr') . " AS g, " . $ecs->table('attribute') . " AS a " . "WHERE g.attr_id = a.attr_id " . "AND g.goods_attr_id " . db_create_in($goods_attr_id);
        $res = $db->query($sql);
        while ($row = $db->fetchRow($res)) {
            $attr_list[] = $row['attr_name'] . ': ' . $row['attr_value'];
        }
        $goods_attr = join(chr(13) . chr(10), $attr_list);
    }
    $smarty->assign('goods_attr', $goods_attr);

    $smarty->assign('info', get_goodsinfo($goods_id));
    $smarty->display('user_clips.dwt');
}

/* 添加缺货登记的处理 */

function action_act_add_booking() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/lib_clips.php');

    $booking = array(
        'goods_id' => isset($_POST['id']) ? intval($_POST['id']) : 0, 'goods_amount' => isset($_POST['number']) ? intval($_POST['number']) : 0, 'desc' => isset($_POST['desc']) ? trim($_POST['desc']) : '', 'linkman' => isset($_POST['linkman']) ? trim($_POST['linkman']) : '', 'email' => isset($_POST['email']) ? trim($_POST['email']) : '', 'tel' => isset($_POST['tel']) ? trim($_POST['tel']) : '', 'booking_id' => isset($_POST['rec_id']) ? intval($_POST['rec_id']) : 0
    );

    // 查看此商品是否已经登记过
    $rec_id = get_booking_rec($user_id, $booking['goods_id']);
    if ($rec_id > 0) {
        show_message($_LANG['booking_rec_exist'], $_LANG['back_page_up'], '', 'error');
    }

    if (add_booking($booking)) {
        show_message($_LANG['booking_success'], $_LANG['back_booking_list'], 'user.php?act=booking_list', 'info');
    } else {
        $err->show($_LANG['booking_list_lnk'], 'user.php?act=booking_list');
    }
}

/* 删除缺货登记 */

function action_act_del_booking() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/lib_clips.php');

    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id == 0 || $user_id == 0) {
        ecs_header("Location: user.php?act=booking_list\n");
        exit();
    }

    $result = delete_booking($id, $user_id);
    if ($result) {
        ecs_header("Location: user.php?act=booking_list\n");
        exit();
    }
}

/* 确认收货 */

function action_affirm_received() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    require_once(ROOT_PATH . '/includes/lib_order.php');
    include_once (ROOT_PATH . 'includes/lib_transaction.php');

    $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

    //添加是否已经退款成功，退款成功，不可以再次确认收货
    $sql = "select status_back from " . $GLOBALS['ecs']->table("back_order") . " where order_id='" . $order_id . "'";
    $status_back = $db->getOne($sql);
    //0:审核通过,1:收到寄回商品,2:换回商品已寄出,3:完成退货/返修,4:退款(无需退货),5:审核中,6:申请被拒绝,7:管理员取消,8:用户自己取消
    if (!empty($status_back) && ($status_back * 1 == 1 || $status_back * 1 == 3)) {
        if ($status_back * 1 === 1) {
            //$GLOBALS['err']->show("当前商品不能退货，因为已经收到您寄回的商品，请等待处理", 'user.php?act=order_list');
            show_message("当前商品不能退货，因为已经收到您寄回的商品，请等待处理", $_LANG['order_list_lnk'], 'user.php?act=order_list');
        }
        if ($status_back * 1 === 3) {
            //$GLOBALS['err']->show("当前商品不能退货，因为已经退货完成", 'user.php?act=order_list');
            show_message("当前商品不能退货，因为已经退货完成", $_LANG['order_list_lnk'], 'user.php?act=order_list');
        }
        exit();
    }
    if (affirm_received($order_id, $user_id)) {
        ecs_header("Location: user.php?act=order_list\n");
        exit();
    } else {
        $GLOBALS['err']->show($_LANG['order_list_lnk'], 'user.php?act=order_list');
    }
}

/* 会员退款申请界面 */

function action_account_raply() {
    die("action forbidden!");
    exit;
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    if (date("w") * 1 != 5) {
        //$content = "提现日每周五，此操作将不可进行！";
        //show_message($content, $_LANG['back_page_up'], '', 'info');
    }

    $sql = "select `real_name`,`card`,`bank`,`bank_kh`,`bank_num` from" . $ecs->table("users") . " where `user_id`='" . $user_id . "'";
    $profileRow = $db->getRow($sql);
    if (empty($profileRow["real_name"]) || empty($profileRow["card"]) || empty($profileRow["bank"]) || empty($profileRow['bank_kh']) || empty($profileRow['bank_num'])) {
        show_message("请将银行卡信息补充完整，才能继续进行积分兑换操作", "用户信息", 'user.php?act=profile');
    } else {
        $smarty->display('user_transaction.dwt');
    }
}

/* 会员预付款界面 */

function action_account_deposit() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/lib_clips.php');

    $surplus_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $account = get_surplus_info($surplus_id);

    $smarty->assign('payment', get_online_payment_list(false));

    $smarty->assign('order', $account);
    $smarty->display('user_transaction.dwt');
}

/* 会员账目明细界面 */

function action_account_detail() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    $action = $GLOBALS['action'];

    include_once (ROOT_PATH . 'includes/lib_clips.php');

    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

    $account_type = $_GET["account_type"];
    if (empty($account_type) || !in_array($account_type, array("pay_points", "consum_money", "give_points", "tx_points", "history_zsq", "zsq", "user_money", "frozen_points", "yl_points", "hz_points"))) {
        $account_type = "pay_points";
    }

    /* 获取记录条数 */
    $sql = "SELECT COUNT(*) FROM " . $ecs->table('account_log') . " WHERE  change_type!=2 and user_id = '$user_id'" . " AND $account_type <> 0 ";
    $record_count = $db->getOne($sql);

    // 分页函数
    $pager = get_pager('user.php', array(
        'act' => $action,
        'account_type' => $account_type
            ), $record_count, $page);

    
    $surplus_info = get_user_default($user_id);
    if (empty($surplus_info)) {
        $surplus_yue = 0;
    } else {
        if ($account_type == "pay_points") {//查分红积分
            $surplus_yue = $surplus_info["integral"];
        } else {
            if ($account_type == "user_money") {
                $surplus_yue = $surplus_info["surplus"];
            } else {
                $surplus_yue = $surplus_info[$account_type];
            }
        }
    }
    
    
    // 获取余额记录
    $account_log = array();
    $sql = "SELECT * FROM " . $ecs->table('account_log') . " WHERE change_type!=2 and  user_id = '$user_id'" . " AND $account_type <> 0 " . " ORDER BY log_id DESC";
   
    $res = $GLOBALS['db']->selectLimit($sql, $pager['size'], $pager['start']);
    while ($row = $db->fetchRow($res)) {
        $row['change_time'] = local_date($_CFG['time_format'], $row['change_time']);
        $row['type'] = $row[$account_type] > 0 ? $_LANG['account_inc'] : $_LANG['account_dec'];
        $row['user_money'] = (abs($row['user_money']));
        //$row['frozen_money'] = price_format(abs($row['frozen_money']), false);
        $row['rank_points'] = abs($row['rank_points']);
        $row['pay_points'] = abs($row['pay_points']);
        $row['short_change_desc'] = sub_str($row['change_desc'], 60);
        $row['amount'] = $row[$account_type];
        $account_log[] = $row;
    }

    // 模板赋值
    $smarty->assign('surplus_amount', price_format($surplus_amount, false));
    $smarty->assign('account_log', $account_log);
    $smarty->assign('surplus_yue', $surplus_yue);
    $smarty->assign('pager', $pager);
    $smarty->display('user_transaction.dwt');
}



/**
 * 产品报单中心
 */
function action_product_center() {
	
	$smarty = $GLOBALS['smarty'];
	$db = $GLOBALS['db'];
	$ecs = $GLOBALS['ecs'];
	$smarty->assign('action', 'product_center');
	
	//充值总额
	$sql =" SELECT  SUM(amount)   FROM " .$ecs->table('user_account'). " WHERE  `user_id` = ".$_SESSION['user_id']."  and process_type=0 and type=".PAY_PRODUCT_SURPLUS;
	 
	$smarty->assign('recharge_points',$db->getOne($sql));
	
	$sql =" SELECT  SUM(points)   FROM " .$ecs->table('product_points_recharge'). " WHERE  `user_id` = ".$_SESSION['user_id'];
	$smarty->assign('points',$db->getOne($sql));//剩余总额
	
	
	$sql =" SELECT  SUM(buy_points)   FROM " .$ecs->table('product_points_recharge'). " WHERE  `user_id` = ".$_SESSION['user_id'];
	$smarty->assign('buy_points',$db->getOne($sql));//购买的积分总额
	
	
	$sql =" SELECT  cp_points   FROM " .$ecs->table('users'). " WHERE  `user_id` = ".$_SESSION['user_id'];
	$smarty->assign('ke_ti_xian_jin_e',$db->getOne($sql));//购买的积分总额
	
	
	$sql =" SELECT  SUM(points)  FROM " .$ecs->table('product_points_log'). " WHERE  `user_id` = ".$_SESSION['user_id']."  ";
	$draw_points =  $db->getOne($sql);
	$smarty->assign('draw_points',$draw_points);//发放总额
	
	//查询店铺信息
	$sql =" SELECT * FROM ". $ecs->table('supplier') ." WHERE `supplier_id` = '".$supplier_info['supplier_id']."' LIMIT 1 ";
	$supp_infos = $db->getRow($sql);
	$smarty->assign('supp_infos',$supp_infos);
	$smarty->display('user_transaction.dwt');
 
	
}
/* 会员充值和提现申请记录 */

function action_account_log() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    $action = $GLOBALS['action'];
    $ctype = $_GET['type'];
    if (!isset($ctype) || !in_array($ctype, array("djtfh_points"))) {
        $ctype = "djtfh_points";
        $ctypename = "兑换";
        $ctypename1 = "大家庭分红";
        $process_type = 1;
    }
    if ($ctype == "user_money") {
        $process_type = 0;
        $ctypename = "充值";
        $ctypename1 = "金额";
    } else {
        $ctype = "djtfh_points";
        $ctypename = "兑换";
        $ctypename1 = "大家庭分红";
        $process_type = 1;
    }
    $smarty->assign('ctypename', $ctypename);
    $smarty->assign('ctypename1', $ctypename1);
    include_once (ROOT_PATH . 'includes/lib_clips.php');

    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

    /* 获取记录条数 */
    $sql = "SELECT COUNT(*) FROM " . $ecs->table('user_account') . " WHERE user_id = '$user_id'" . " AND type =2 and process_type=$process_type";
    $record_count = $db->getOne($sql);

    // 分页函数
    $pager = get_pager('user.php', array(
        'act' => $action
            ), $record_count, $page);
    /* /查看账户明细页面 获取会员用户的余额 jx 2015-1-1 */
    $surplus_info = get_user_default($user_id);
    if (empty($surplus_info)) {
        $surplus_yue = 0;
    } else {
        $surplus_yue = $surplus_info[$ctype];
    }
    // 获取花费余额
//	$surplus_amount = get_user_payed($user_id);
//	if(empty($surplus_amount))
//	{
//		$surplus_amount = 0;
//	}
    // 获取余额记录
    $account_log = get_account_log($user_id, $pager['size'], $pager['start'], $process_type, 2);

    // 模板赋值
    //$smarty->assign('surplus_amount',$surplus_amount);
    $smarty->assign('account_log', $account_log);
    $smarty->assign('surplus_yue', $surplus_yue);
    $smarty->assign('pager', $pager);
    $smarty->display('user_transaction.dwt');
}




/* 对会员余额申请的处理 */

function action_act_account() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/lib_clips.php');
    include_once (ROOT_PATH . 'includes/lib_order.php');
    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
    if ($amount <= 0) {
        show_message($_LANG['amount_gt_zero']);
    }


    /* 变量初始化 */
    $surplus = array(
        'user_id' => $user_id,
    		'rec_id' => !empty($_POST['rec_id']) ? intval($_POST['rec_id']) : 0, 
    		'process_type' => isset($_POST['surplus_type']) ? intval($_POST['surplus_type']) : 0, 
    		'payment_id' => isset($_POST['payment_id']) ? intval($_POST['payment_id']) : 0,
    		'user_note' => isset($_POST['user_note']) ? trim($_POST['user_note']) : '',
    		'amount' => $amount,
    		'fee' => $amount*12/100,
    		'real_amount' => $amount*0.88
    );

    $payment_info = payment_info($surplus['payment_id']);
    if ($payment_info['pay_code'] == 'alipay_bank') {
        $surplus['defaultbank'] = isset($_POST['com_bank']) ? trim($_POST['com_bank']) : '';
    }
    if ($_REQUEST['type'] != 2) {
        die("action forbidden!");
        exit;
    }
    /* 退款申请的处理 */
    if ($surplus['process_type'] == 1) {
        if ($_REQUEST['type'] != 2) {
            die("action forbidden!");
            exit;
        }

        /* 判断是否有足够的余额的进行退款的操作 */
        $userDefault = get_user_default($user_id);
        $sur_amount = $userDefault["djtfh_points"] * 1;
        //0 1 2 3 4 5 6 
        if (date("w") * 1 != 4) {
            $content = "提现日期为周四.";
            show_message($content, $_LANG['back_page_up'], '', 'info');
        }
        $hoursNow =  date("H")+8;
        if($hoursNow<7|| $hoursNow>16){
        	show_message("提现时间为每周四早8点到下午5点之间");
        }
        

        $txJeSet = 100;
//         if (isset($GLOBALS['_CFG']['tx_set_je']) && !empty($GLOBALS['_CFG']['tx_set_je'])) {
//             $txJeSet = $GLOBALS['_CFG']['tx_set_je'] * 1;
//             if ($tx_set_je < 100) {
//                 $tx_set_je = 100;
//             }
//         }

        
        if ($sur_amount < $txJeSet) {
            $content = "您现有的分红积分不足" . $txJeSet . "，此操作将不可进行！";
            show_message($content, $_LANG['back_page_up'], '', 'info');
        }
//         $test = $amount / $txJeSet;
//         if (ceil($test) !== $test) {
//             $content = "您的提现必须是" . $txJeSet . "的整数倍，此操作将不可进行！";
//             show_message($content, $_LANG['back_page_up'], '', 'info');
//         }

        
        if ($amount > $sur_amount) {
            $content = "您要申请提现的积分超过了您现有的分红积分，此操作将不可进行！";
            show_message($content, $_LANG['back_page_up'], '', 'info');
        }

        // 插入会员账目明细
        $amount = '-' . $amount;
        $surplus['payment'] = '';
        $GLOBALS['db']->startTrans();
        $surplus['rec_id'] = insert_user_account($surplus, $amount, 2);

        /* 如果成功提交 */
        if ($surplus['rec_id'] > 0) {
        	$res1 = log_account_change($user_id, 0, 0, 0, 0, "会员PC端发起大家庭分红积分兑换操作", ACT_DRAWING_FEN_HONG, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, $amount, 0);
            if ($res1) {
                $content = $_LANG['surplus_appl_submit'];
                $GLOBALS['db']->commitTrans();
                show_message($content, $_LANG['back_account_log'], 'user.php?act=account_log', 'info');
            } else {
                $GLOBALS['db']->rollbackTrans();
                $content = $_LANG['process_false'];
                show_message($content, $_LANG['back_page_up'], '', 'info');
            }
        } else {
            $GLOBALS['db']->rollbackTrans();
            $content = $_LANG['process_false'];
            show_message($content, $_LANG['back_page_up'], '', 'info');
        }
        exit();
    }
    /* 如果是会员预付款，跳转到下一步，进行线上支付的操作 */ else {
        exit;
        if ($surplus['payment_id'] <= 0) {
            show_message($_LANG['select_payment_pls']);
        }

        include_once (ROOT_PATH . 'includes/lib_payment.php');

        // 获取支付方式名称
        $payment_info = array();
        $payment_info = payment_info($surplus['payment_id']);
        $surplus['payment'] = $payment_info['pay_name'];

        if ($surplus['rec_id'] > 0) {
            // 更新会员账目明细
            $surplus['rec_id'] = update_user_account($surplus);
        } else {
            // 插入会员账目明细
            $surplus['rec_id'] = insert_user_account($surplus, $amount);
        }

        // 取得支付信息，生成支付代码
        $payment = unserialize_config($payment_info['pay_config']);

        // 生成伪订单号, 不足的时候补0
        $order = array();
        $order['order_sn'] = $surplus['rec_id'];
        $order['user_name'] = $_SESSION['user_name'];
        $order['surplus_amount'] = $amount;
        $order['defaultbank'] = $surplus['defaultbank'];
        // 计算支付手续费用
        $payment_info['pay_fee'] = pay_fee($surplus['payment_id'], $order['surplus_amount'], 0);

        // 计算此次预付款需要支付的总金额
        $order['order_amount'] = $amount + $payment_info['pay_fee'];

        // 记录支付log
        $order['log_id'] = insert_pay_log($surplus['rec_id'], $order['order_amount'], $type = PAY_SURPLUS, 0);

        /* 调用相应的支付方式文件 */
        include_once (ROOT_PATH . 'includes/modules/payment/' . $payment_info['pay_code'] . '.php');

        /* 取得在线支付方式的支付按钮 */
        $pay_obj = new $payment_info['pay_code']();
        $payment_info['pay_button'] = $pay_obj->get_code($order, $payment);

        /* 模板赋值 */
        $smarty->assign('payment', $payment_info);
        $smarty->assign('pay_fee', price_format($payment_info['pay_fee'], false));
        $smarty->assign('amount', price_format($amount, false));
        $smarty->assign('order', $order);
        $smarty->display('user_transaction.dwt');
    }
}



/**
 * 周:到分红积分提现页面
 */
function action_fen_hong_ji_fen_ti_xian() {
	
	$user = $GLOBALS['user'];
	$_CFG = $GLOBALS['_CFG'];
	$_LANG = $GLOBALS['_LANG'];
	$smarty = $GLOBALS['smarty'];
	$db = $GLOBALS['db'];
	$ecs = $GLOBALS['ecs'];
	$user_id = $_SESSION['user_id'];
	$smarty->assign('action', 'fen_hong_ji_fen_ti_xian');
	$sql = "select `real_name`,`card`,`bank`,`bank_kh`,`bank_num` from" . $ecs->table("users") . " where `user_id`='" . $user_id . "'";
	$profileRow = $db->getRow($sql);
	
	if (empty($profileRow["real_name"]) || empty($profileRow["card"]) || empty($profileRow["bank"]) || empty($profileRow['bank_kh']) || empty($profileRow['bank_num'])) {
		show_message("请将银行卡信息补充完整，才能继续进行积分兑换操作", "用户信息", 'user.php?act=profile');
	} else {
		$smarty->display('user_transaction.dwt');
	}
}

/* 会员分红积分提现申请记录 */
function action_fen_hong_ji_fen_account_log() {
	
	$user = $GLOBALS['user'];
	$_CFG = $GLOBALS['_CFG'];
	$_LANG = $GLOBALS['_LANG'];
	$smarty = $GLOBALS['smarty'];
	$db = $GLOBALS['db'];
	$ecs = $GLOBALS['ecs'];
	$user_id = $_SESSION['user_id'];
	$action = $GLOBALS['action'];
	$ti_xian_lei_xing = 3;
	$process_type = 1;
	
	include_once (ROOT_PATH . 'includes/lib_clips.php');
	
	$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
	
	/* 获取记录条数 */
	$sql = "SELECT COUNT(*) FROM " . $ecs->table('user_account') . " WHERE user_id = '$user_id'" . " AND type =".$ti_xian_lei_xing." and process_type=$process_type";
	$record_count = $db->getOne($sql);
	
	// 分页函数
	$pager = get_pager('user.php', array(
			'act' => $action
	), $record_count, $page);
	/* /查看账户明细页面 获取会员用户的余额 jx 2015-1-1 */
	$surplus_info = get_user_default($user_id);
	if (empty($surplus_info)) {
		$surplus_yue = 0;
	} else {
		$surplus_yue = $surplus_info[$ti_xian_lei_xing];
	}
	
	// 获取账户分红积分提现充值记录
	$account_log = get_account_log($user_id, $pager['size'], $pager['start'], $process_type, $ti_xian_lei_xing);
	
	// 模板赋值
	//$smarty->assign('surplus_amount',$surplus_amount);
	$smarty->assign('account_log', $account_log);
	$smarty->assign('surplus_yue', $surplus_yue);
	$smarty->assign('pager', $pager);
	
	$smarty->display('user_transaction.dwt');
}

/* 对会员分红积分提现的处理 */

function action_fen_hong_ji_fen_ti_xian_save() {
	$user = $GLOBALS['user'];
	$_CFG = $GLOBALS['_CFG'];
	$_LANG = $GLOBALS['_LANG'];
	$smarty = $GLOBALS['smarty'];
	$db = $GLOBALS['db'];
	$ecs = $GLOBALS['ecs'];
	$user_id = $_SESSION['user_id'];
	
	include_once (ROOT_PATH . 'includes/lib_clips.php');
	include_once (ROOT_PATH . 'includes/lib_order.php');
	$amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
	if ($amount <= 0) {
		show_message($_LANG['amount_gt_zero']);
	}
	
	
	
	
	/*当前用户 */
	$userDefault = get_user_default ( $user_id );
	/* 变量初始化 */
	if($userDefault['is_bigfamily']!=0){//大家庭会员没有手续费
		$surplus = array (
				'user_id' => $user_id,
				'rec_id' => ! empty ( $_POST ['rec_id'] ) ? intval ( $_POST ['rec_id'] ) : 0,
				'process_type' => 1, // 提现
				'payment_id' => isset ( $_POST ['payment_id'] ) ? intval ( $_POST ['payment_id'] ) : 0,
				'user_note' => isset ( $_POST ['user_note'] ) ? trim ( $_POST ['user_note'] ) : '',
				'amount' => $amount,
				'fee' => 0,
				'real_amount' => $amount
		);
	}else{
		$surplus = array (
				'user_id' => $user_id,
				'rec_id' => ! empty ( $_POST ['rec_id'] ) ? intval ( $_POST ['rec_id'] ) : 0,
				'process_type' => 1, // 提现
				'payment_id' => isset ( $_POST ['payment_id'] ) ? intval ( $_POST ['payment_id'] ) : 0,
				'user_note' => isset ( $_POST ['user_note'] ) ? trim ( $_POST ['user_note'] ) : '',
				'amount' => $amount,
				'fee' => $amount * 12 / 100,
				'real_amount' => $amount * 88 / 100
		);
	}
	
	
 
	
	
	
	$payment_info = payment_info($surplus['payment_id']);
	if ($payment_info['pay_code'] == 'alipay_bank') {
		$surplus['defaultbank'] = isset($_POST['com_bank']) ? trim($_POST['com_bank']) : '';
	}
	
	
	/* 判断是否有足够的余额的进行退款的操作 */
	$sur_amount = $userDefault["pay_points"] * 1;
	//0 1 2 3 4 5 6
	if (date("w") * 1 != 4) {
		$content = "提现日期为每周四.";
		show_message($content, $_LANG['back_page_up'], '', 'info');
	}
	$hoursNow =  date("H")+8;
	if($hoursNow<7|| $hoursNow>16){
		show_message("提现时间为每周四早8点到下午5点");
	}
	
	$min = 100;
	//         if (isset($GLOBALS['_CFG']['tx_set_je']) && !empty($GLOBALS['_CFG']['tx_set_je'])) {
	//             $txJeSet = $GLOBALS['_CFG']['tx_set_je'] * 1;
	//             if ($tx_set_je < 100) {
	//                 $tx_set_je = 100;
	//             }
	//         }
	
	

	
	if ($amount > $sur_amount) {
		$content = "您要申请提现的积分超过了您现有的分红积分，此操作将不可进行！";
		show_message($content, $_LANG['back_page_up'], '', 'info');
	}
// 	if($userDefault['user_type']==2 || $userDefault['user_type']==4){
// 		$content = "分红积分不能兑换！";
// 		show_message($content);
// 	}

	 
	
	if ($sur_amount < $min) {
		$content = "您现有的分红积分不足" . $min . "，此操作将不可进行！";
		show_message($content, $_LANG['back_page_up'], '', 'info');
	}
	
	if ($amount%$min!=0) {
		$content = "提现金额必须是100的整数倍！";
		show_message ( $content, $_LANG ['back_page_up'], '', 'info' );
	}
	
// 	$test = $amount / $txJeSet;
// 	if (ceil($test) !== $test) {
// 		$content = "您的提现必须是" . $txJeSet . "的整数倍，此操作将不可进行！";
// 		show_message($content, $_LANG['back_page_up'], '', 'info');
// 	}
	
	
	$sql = "SELECT add_time FROM " . $GLOBALS['ecs']->table("user_account") . " where user_id='" . $_SESSION["user_id"] . "' and type=3 and process_type=1 order by add_time DESC limit 1 ";
	$add_time = $GLOBALS['db']->getOne($sql);
	$time = gmtime();
	if (!empty($add_time) && ($time < $add_time + 86400)) {
		show_message("您今天已经提过现了,请下次提现时间再进行操作");
	}
	
	
	
	// 插入会员账目明细
	$amount = '-' . $amount;
	$surplus['payment'] = '';
	$GLOBALS['db']->startTrans();
	$surplus['rec_id'] = insert_user_account($surplus, $amount, 3);//分红积分提现
	
	/* 如果成功提交 */
	if ($surplus['rec_id'] > 0) {
		$res1 = log_account_change($user_id, 0, 0, 0, $amount, "会员PC端发起分红积分兑换操作", ACT_DRAWING_FEN_HONG_JI_FEN, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
		if ($res1) {
			$content = $_LANG['surplus_appl_submit'];
			$GLOBALS['db']->commitTrans();
			show_message($content, "返回提现记录", 'user.php?act=fen_hong_ji_fen_account_log', 'info');
		} else {
			$GLOBALS['db']->rollbackTrans();
			$content = $_LANG['process_false'];
			show_message($content, $_LANG['back_page_up'], '', 'info');
		}
	} else {
		$GLOBALS['db']->rollbackTrans();
		$content = $_LANG['process_false'];
		show_message($content, $_LANG['back_page_up'], '', 'info');
	}
	exit();
	
}

/* 取消分红积分提现 */

function action_cancel_fen_hong_ji_fen_ti_xian() {
	$user = $GLOBALS['user'];
	$_CFG = $GLOBALS['_CFG'];
	$_LANG = $GLOBALS['_LANG'];
	$smarty = $GLOBALS['smarty'];
	$db = $GLOBALS['db'];
	$ecs = $GLOBALS['ecs'];
	$user_id = $_SESSION['user_id'];
	
	include_once (ROOT_PATH . 'includes/lib_clips.php');
	
	$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
	if ($id == 0 || $user_id == 0) {
		ecs_header("Location: user.php?act=fen_hong_ji_fen_account_log\n");
		exit();
	}
	
	$GLOBALS['db']->startTrans();
	$account_order = $GLOBALS['db']->getRow('SELECT *  FROM ' . $GLOBALS['ecs']->table('user_account') .
			" WHERE is_paid = 0 AND id = '$id' AND user_id = '$user_id' for update ");
	$result = del_user_account($id, $user_id);
	if ($result) {
		$amount = $account_order['amount'];
	    $ress = log_account_change($user_id, 0, 0, 0, abs($amount), "会员手动取消分红积分兑换，分红积分增加", 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
		 
		if ($ress) {
			$GLOBALS['db']->commitTrans();
			ecs_header("Location: user.php?act=fen_hong_ji_fen_account_log\n");
			exit();
		}
	}
	$GLOBALS['db']->rollbackTrans();
}



/* 周:取消大家庭分红提现 */

function action_cancel() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/lib_clips.php');

    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id == 0 || $user_id == 0) {
        ecs_header("Location: user.php?act=account_log\n");
        exit();
    }

    $GLOBALS['db']->startTrans();
    $account_order = $GLOBALS['db']->getRow('SELECT *  FROM ' . $GLOBALS['ecs']->table('user_account') .
            " WHERE is_paid = 0 AND id = '$id' AND user_id = '$user_id' for update ");
    $result = del_user_account($id, $user_id);
    if ($result) {
        $amount = $account_order['amount'];
        //$user_id, $user_money = 0, $frozen_money = 0, $rank_points = 0, $pay_points = 0, $change_desc = '', $change_type = ACT_OTHER, $consum_money = 0, $give_points = 0, $tx_points = 0, $history_zsq = 0, $zsq = 0, $frozen_points = 0
        if ($account_order['process_type'] * 1 == 0) {
            $ress = true;
        } else {
            $ress = log_account_change($user_id, 0, 0, 0, 0, "会员手动取消大家庭分红兑换，大家庭分红增加", 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, abs($amount), 0);
        }
        if ($ress) {
            $GLOBALS['db']->commitTrans();
            ecs_header("Location: user.php?act=account_log&type=djtfh_points\n");
            exit();
        }
    }
    $GLOBALS['db']->rollbackTrans();
}

/* 会员通过帐目明细列表进行再付款的操作 */

function action_pay() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/lib_clips.php');
    include_once (ROOT_PATH . 'includes/lib_payment.php');
    include_once (ROOT_PATH . 'includes/lib_order.php');

    // 变量初始化
    $surplus_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $payment_id = isset($_GET['pid']) ? intval($_GET['pid']) : 0;

    if ($surplus_id == 0) {
        ecs_header("Location: user.php?act=account_log\n");
        exit();
    }

    // 如果原来的支付方式已禁用或者已删除, 重新选择支付方式
    if ($payment_id == 0) {
        ecs_header("Location: user.php?act=account_deposit&id=" . $surplus_id . "\n");
        exit();
    }

    // 获取单条会员帐目信息
    $order = array();
    $order = get_surplus_info($surplus_id);

    // 支付方式的信息
    $payment_info = array();
    $payment_info = payment_info($payment_id);

    /* 如果当前支付方式没有被禁用，进行支付的操作 */
    if (!empty($payment_info)) {
        // 取得支付信息，生成支付代码
        $payment = unserialize_config($payment_info['pay_config']);

        // 生成伪订单号
        $order['order_sn'] = $surplus_id;

        // 获取需要支付的log_id
        $order['log_id'] = insert_pay_log($surplus_id, $order['amount'], PAY_SURPLUS); // get_paylog_id($surplus_id, $pay_type = PAY_SURPLUS);

        $order['user_name'] = $_SESSION['user_name'];
        $order['surplus_amount'] = $order['amount'];

        // 计算支付手续费用
        $payment_info['pay_fee'] = pay_fee($payment_id, $order['surplus_amount'], 0);

        // 计算此次预付款需要支付的总金额
        $order['order_amount'] = $order['surplus_amount'] + $payment_info['pay_fee'];

        // 如果支付费用改变了，也要相应的更改pay_log表的order_amount
        $order_amount = $db->getOne("SELECT order_amount FROM " . $ecs->table('pay_log') . " WHERE log_id = '$order[log_id]'");
        if ($order_amount != $order['order_amount']) {
            $db->query("UPDATE " . $ecs->table('pay_log') . " SET order_amount = '$order[order_amount]' WHERE log_id = '$order[log_id]'");
        }

//        /* 调用相应的支付方式文件 */
//        include_once (ROOT_PATH . 'includes/modules/payment/' . $payment_info['pay_code'] . '.php');
//
//        /* 取得在线支付方式的支付按钮 */
//        $pay_obj = new $payment_info['pay_code']();
//        $payment_info['pay_button'] = $pay_obj->get_code($order, $payment);

//         include_once(ROOT_PATH . 'includes/modules/payment/zhizunbao.php');
//         include_once(ROOT_PATH.'includes/modules/openepay/zhifutong.php');
//         $pay_obj = new zhifutong();
        include_once('includes/modules/payment/zhizunbao.php');
        $pay_obj = new zhizunbao();
        $orderUrl = $GLOBALS['ecs']->url();
        $payment_info['pay_button'] = $pay_obj->get_code($order, $payment_info['pay_code'], $orderUrl . "user.php?act=account_log&type=user_money", "PC");


        /* 模板赋值 */
        $smarty->assign('payment', $payment_info);
        $smarty->assign('order', $order);
        $smarty->assign('pay_fee', price_format($payment_info['pay_fee'], false));
        $smarty->assign('amount', price_format($order['surplus_amount'], false));
        $smarty->assign('action', 'act_account');
        $smarty->display('user_transaction.dwt');
    }
    /* 重新选择支付方式 */ else {
        include_once (ROOT_PATH . 'includes/lib_clips.php');

        $smarty->assign('payment', get_online_payment_list());
        $smarty->assign('order', $order);
        $smarty->assign('action', 'account_deposit');
        $smarty->display('user_transaction.dwt');
    }
}

/* 添加标签(ajax) */

function action_add_tag() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once ('includes/cls_json.php');
    include_once ('includes/lib_clips.php');

    $result = array(
        'error' => 0, 'message' => '', 'content' => ''
    );
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $tag = isset($_POST['tag']) ? json_str_iconv(trim($_POST['tag'])) : '';

    if ($user_id == 0) {
        /* 用户没有登录 */
        $result['error'] = 1;
        $result['message'] = $_LANG['tag_anonymous'];
    } else {
        add_tag($id, $tag); // 添加tag
        clear_cache_files('goods'); // 删除缓存

        /* 重新获得该商品的所有缓存 */
        $arr = get_tags($id);

        foreach ($arr as $row) {
            $result['content'][] = array(
                'word' => htmlspecialchars($row['tag_words']), 'count' => $row['tag_count']
            );
        }
    }

    $json = new JSON();

    echo $json->encode($result);
    exit();
}

/* 添加收藏商品(ajax) */

function action_collect() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON();
    $result = array(
        'error' => 0, 'message' => ''
    );
    $goods_id = $_GET['id'];
    $result['goods_id'] = $goods_id;

    if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == 0) {
        $result['error'] = 1;
        $result['message'] = $_LANG['login_please'];

        die($json->encode($result));
    } else {
        /* 检查是否已经存在于用户的收藏夹 */
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('collect_goods') . " WHERE user_id='$_SESSION[user_id]' AND goods_id = '$goods_id'";
        if ($GLOBALS['db']->GetOne($sql) > 0) {
            $result['error'] = 1;
            $result['message'] = $GLOBALS['_LANG']['collect_existed'];
            die($json->encode($result));
        } else {
            $time = gmtime();
            $sql = "INSERT INTO " . $GLOBALS['ecs']->table('collect_goods') . " (user_id, goods_id, add_time)" . "VALUES ('$_SESSION[user_id]', '$goods_id', '$time')";

            //删除商品详情缓存
            $cache_id = $goods_id . '-' . $_SESSION['user_rank'] . '-' . $_CFG['lang'];
            $cache_id = sprintf('%X', crc32($cache_id));
            $smarty->clear_cache('goods.dwt', $cache_id);

            if ($GLOBALS['db']->query($sql) === false) {
                $result['error'] = 1;
                $result['message'] = $GLOBALS['db']->errorMsg();
                die($json->encode($result));
            } else {
                $result['error'] = 0;
                $result['message'] = $GLOBALS['_LANG']['collect_success'];

                //收藏个数统计
                $sql_nums = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('collect_goods') . " WHERE goods_id = '$goods_id'";
                $collect_num = $db->getOne($sql_nums);
                $collect_num = $collect_num > 0 ? $collect_num : 0;
                $result['collect_num'] = "已收藏 ($collect_num)";

                die($json->encode($result));
            }
        }
    }
}

// 代码添加 线上红包_start_cb_20150528
function action_user_bonus() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON();
    $result = array(
        'error' => 0, 'message' => ''
    );
    $type_id = $_GET['id'];
    $result['type_id'] = $type_id;
    $result['no_have'] = $_GET['no_have'];

    if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == 0) {
        $result['error'] = 1;
        $result['message'] = $_LANG['login_please'];
        die($json->encode($result));
    } else {
        $sql = "SELECT COUNT(bonus_type_id) FROM " . $GLOBALS['ecs']->table('user_bonus') . " WHERE user_id='$_SESSION[user_id]' AND bonus_type_id = '$type_id'";
        $u_bonus = $GLOBALS['db']->GetOne($sql);

        $sql1 = "SELECT user_bonus_max FROM " . $GLOBALS['ecs']->table('bonus_type') . " WHERE type_id = '$type_id'";
        $bonus_max = $db->getOne($sql1);
        if ($u_bonus >= $bonus_max) {
            $result['error'] = 1;
            $result['message'] = $GLOBALS['_LANG']['u_bonus_existed'];
            die($json->encode($result));
        } else {
            $sql = "INSERT INTO " . $GLOBALS['ecs']->table('user_bonus') . " (user_id,bonus_type_id)" . "VALUES ('$_SESSION[user_id]', '$type_id')";

            if ($GLOBALS['db']->query($sql) === false) {
                $result['error'] = 1;
                $result['message'] = $GLOBALS['db']->errorMsg();
                die($json->encode($result));
            } else {
                $u_bonus = $u_bonus + 1;
                $bonus_max = $bonus_max - $u_bonus;
                $result['error'] = 0;
                $result['message'] = "领取成功，您领取了" . $u_bonus . "个红包，还可以领取" . $bonus_max . "个红包。";
                die($json->encode($result));
            }
        }
    }
}

function action_book_goods() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON();
    $result = array(
        'error' => 0, 'message' => '', 'tel' => '', 'email' => ''
    );
    $goods_id = $_GET['id'];
    $result['goods_id'] = $goods_id;
    $result['no_have'] = $_GET['no_have'];

    if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == 0) {
        $result['error'] = 99;
        $result['message'] = $_LANG['login_please'];
        die($json->encode($result));
    } else {
        $sql = "SELECT user_id,goods_id FROM " . $GLOBALS['ecs']->table('booking_goods') . " WHERE user_id='$_SESSION[user_id]' AND is_dispose=0 AND goods_id = '$goods_id'";
        $b_goods = $GLOBALS['db']->GetOne($sql);

        if ($b_goods) {
            $result['error'] = 0;
            $result['message'] = "您已经登记过了";
            die($json->encode($result));
        } else {
            $sql = "SELECT email,mobile_phone FROM " . $GLOBALS['ecs']->table('users') . " WHERE user_id='$_SESSION[user_id]'";
            $user_msg = $db->getRow($sql);

            $result['error'] = 1;
            $result['tel'] = $user_msg['mobile_phone'];
            $result['email'] = $user_msg['email'];
            die($json->encode($result));
        }
    }
}

function action_add_book_goods() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON();
    $result = array(
        'error' => 0, 'message' => ''
    );
    $goods_id = $_GET['id'];
    $number = $_GET['num'];
    $tel = $_GET['tel'];
    $email = $_GET['em'];

    if (!preg_match("/^1(3|5|8)[0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|189[0-9]{8}$/", $tel)) {
        $result['error'] = 3;
        $result['message'] = "手机格式不正确";
        die($json->encode($result));
    } elseif (!preg_match("/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i", $email)) {
        $result['error'] = 4;
        $result['message'] = "邮箱格式不正确";
        die($json->encode($result));
    } else {
        $time = gmtime();
        $sql = "INSERT INTO " . $ecs->table('booking_goods') . " (user_id,email,tel,goods_id,goods_number,booking_time,link_man) VALUES ('$_SESSION[user_id]','$email','$tel','$goods_id','$number','$time','$_SESSION[user_name]')";
        if ($db->query($sql)) {
            $result['error'] = 2;
            $result['message'] = "登记成功";
            die($json->encode($result));
        } else {
            $result['error'] = 0;
            $result['message'] = "登记失败";
            die($json->encode($result));
        }
    }
}

/* 刷新是否收藏商品(ajax) */

function action_re_collect() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON();
    $goods_id = $_GET['id'];

    if ($goods_id > 0) {
        $result = array(
            'goods_id' => 0, 'is_collect' => ''
        );
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('collect_goods') . " WHERE user_id='$_SESSION[user_id]' AND goods_id = '$goods_id'";
        $result['goods_id'] = $goods_id;
        $result['is_collect'] = ($GLOBALS['db']->getOne($sql) > 0 ? 1 : 0);
    } else {
        $result = array(
            'goods_id' => 0, 'is_collect' => array()
        );
        $sql = "SELECT goods_id FROM " . $GLOBALS['ecs']->table('collect_goods') . " WHERE user_id='$_SESSION[user_id]'";
        $result['is_collect'] = $GLOBALS['db']->getCol($sql);
    }
    die($json->encode($result));
}

/* 删除留言 */

function action_del_msg() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $order_id = empty($_GET['order_id']) ? 0 : intval($_GET['order_id']);

    if ($id > 0) {
        $sql = 'SELECT user_id, message_img FROM ' . $ecs->table('feedback') . " WHERE msg_id = '$id' LIMIT 1";
        $row = $db->getRow($sql);
        if ($row && $row['user_id'] == $user_id) {
            /* 验证通过，删除留言，回复，及相应文件 */
            if ($row['message_img']) {
                @unlink(ROOT_PATH . DATA_DIR . '/feedbackimg/' . $row['message_img']);
            }
            $sql = "DELETE FROM " . $ecs->table('feedback') . " WHERE msg_id = '$id' OR parent_id = '$id'";
            $db->query($sql);
        }
    }
    ecs_header("Location: user.php?act=message_list&order_id=$order_id\n");
    exit();
}

/* 删除评论 */

function action_del_cmt() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id > 0) {
        $sql = "DELETE FROM " . $ecs->table('comment') . " WHERE comment_id = '$id' AND user_id = '$user_id'";
        $db->query($sql);
    }
    ecs_header("Location: user.php?act=comment_list\n");
    exit();
}

/* 合并订单 */

function action_merge_order() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/lib_transaction.php');
    include_once (ROOT_PATH . 'includes/lib_order.php');
    $from_order = isset($_POST['from_order']) ? trim($_POST['from_order']) : '';
    $to_order = isset($_POST['to_order']) ? trim($_POST['to_order']) : '';

    /* 代码增加_start By www.68ecshop.com */
    $sql = "select supplier_id from " . $ecs->table('order_info') . " where order_sn='$from_order' ";
    $supplier_id_from = $db->getOne($sql);
    $sql = "select supplier_id from " . $ecs->table('order_info') . " where order_sn='$to_order' ";
    $supplier_id_to = $db->getOne($sql);
    if ($supplier_id_from != $supplier_id_to) {
        show_message('由于供货商不同,订单合并失败', $_LANG['order_list_lnk'], 'user.php?act=order_list', 'info');
    }
    /* 代码增加_end By www.68ecshop.com */

    if (merge_user_order($from_order, $to_order, $user_id)) {
        show_message($_LANG['merge_order_success'], $_LANG['order_list_lnk'], 'user.php?act=order_list', 'info');
    } else {
        $err->show($_LANG['order_list_lnk']);
    }
}

/* 将指定订单中商品添加到购物车 */

function action_return_to_cart() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/cls_json.php');
    include_once (ROOT_PATH . 'includes/lib_transaction.php');
    $json = new JSON();

    $result = array(
        'error' => 0, 'message' => '', 'content' => ''
    );
    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    if ($order_id == 0) {
        $result['error'] = 1;
        $result['message'] = $_LANG['order_id_empty'];
        die($json->encode($result));
    }

    if ($user_id == 0) {
        /* 用户没有登录 */
        $result['error'] = 1;
        $result['message'] = $_LANG['login_please'];
        die($json->encode($result));
    }

    /* 检查订单是否属于该用户 */
    $order_user = $db->getOne("SELECT user_id FROM " . $ecs->table('order_info') . " WHERE order_id = '$order_id'");
    if (empty($order_user)) {
        $result['error'] = 1;
        $result['message'] = $_LANG['order_exist'];
        die($json->encode($result));
    } else {
        if ($order_user != $user_id) {
            $result['error'] = 1;
            $result['message'] = $_LANG['no_priv'];
            die($json->encode($result));
        }
    }

    $message = return_to_cart($order_id);

    if ($message === true) {
        $result['error'] = 0;
        $result['message'] = $_LANG['return_to_cart_success'];
        $result['content'] = insert_cart_info();
        die($json->encode($result));
    } else {
        $result['error'] = 1;
        $result['message'] = $_LANG['order_exist'];
        die($json->encode($result));
    }
}

/* 编辑使用余额支付的处理 */

function action_act_edit_surplus() {
//    $user = $GLOBALS['user'];
//    $_CFG = $GLOBALS['_CFG'];
//    $_LANG = $GLOBALS['_LANG'];
//    $smarty = $GLOBALS['smarty'];
//    $db = $GLOBALS['db'];
//    $ecs = $GLOBALS['ecs'];
//    $user_id = $_SESSION['user_id'];
//
//    /* 检查是否登录 */
//    if ($_SESSION['user_id'] <= 0) {
//        ecs_header("Location: ./\n");
//        exit();
//    }
//
//    /* 检查订单号 */
//    $order_id = intval($_POST['order_id']);
//    if ($order_id <= 0) {
//        ecs_header("Location: ./\n");
//        exit();
//    }
//
//    /* 检查余额 */
//    $surplus = floatval($_POST['surplus']);
//    if ($surplus <= 0) {
//        $err->add($_LANG['error_surplus_invalid']);
//        $err->show($_LANG['order_detail'], 'user.php?act=order_detail&order_id=' . $order_id);
//    }
//
//    include_once (ROOT_PATH . 'includes/lib_order.php');
//
//    /* 取得订单 */
//    $order = order_info($order_id);
//    if (empty($order)) {
//        ecs_header("Location: ./\n");
//        exit();
//    }
//
//    /* 检查订单用户跟当前用户是否一致 */
//    if ($_SESSION['user_id'] != $order['user_id']) {
//        ecs_header("Location: ./\n");
//        exit();
//    }
//
//    /* 检查订单是否未付款，检查应付款金额是否大于0 */
//    if ($order['pay_status'] != PS_UNPAYED || $order['order_amount'] <= 0) {
//        $err->add($_LANG['error_order_is_paid']);
//        $err->show($_LANG['order_detail'], 'user.php?act=order_detail&order_id=' . $order_id);
//    }
//
//    /* 计算应付款金额（减去支付费用） */
//    $order['order_amount'] -= $order['pay_fee'];
//
//    /* 余额是否超过了应付款金额，改为应付款金额 */
//    if ($surplus > $order['order_amount']) {
//        $surplus = $order['order_amount'];
//    }
//
//    /* 取得用户信息 */
//    $user = user_info($_SESSION['user_id']);
//
//    /* 用户帐户余额是否足够 */
//    if ($surplus > $user['user_money'] + $user['credit_line']) {
//        $err->add($_LANG['error_surplus_not_enough']);
//        $err->show($_LANG['order_detail'], 'user.php?act=order_detail&order_id=' . $order_id);
//    }
//
//    /* 修改订单，重新计算支付费用 */
//    $order['surplus'] += $surplus;
//    $order['order_amount'] -= $surplus;
//    if ($order['order_amount'] > 0) {
//        $cod_fee = 0;
//        if ($order['shipping_id'] > 0) {
//            $regions = array(
//                $order['country'], $order['province'], $order['city'], $order['district']
//            );
//            $shipping = shipping_area_info($order['shipping_id'], $regions);
//            if ($shipping['support_cod'] == '1') {
//                $cod_fee = $shipping['pay_fee'];
//            }
//        }
//
//        $pay_fee = 0;
//        if ($order['pay_id'] > 0) {
//            $pay_fee = pay_fee($order['pay_id'], $order['order_amount'], $cod_fee);
//        }
//
//        $order['pay_fee'] = $pay_fee;
//        $order['order_amount'] += $pay_fee;
//    }
//
//    /* 如果全部支付，设为已确认、已付款 */
//    if ($order['order_amount'] == 0) {
//        if ($order['order_status'] == OS_UNCONFIRMED) {
//            $order['order_status'] = OS_CONFIRMED;
//            $order['confirm_time'] = gmtime();
//        }
//        $order['pay_status'] = PS_PAYED;
//        $order['pay_time'] = gmtime();
//    }
//    $order = addslashes_deep($order);
//    update_order($order_id, $order);
//
//    /* 判断虚拟商品 如果是虚拟商品则发放虚拟卡 */
//    if ($order['extension_code'] == 'virtual_good') {
//        put_virtual_cart($order_id);
//        //修改订单配送状态
//        update_order_shipping($order_id);
//    }
//    /* 更新用户余额 */
//    $change_desc = sprintf($_LANG['pay_order_by_surplus'], $order['order_sn']);
//    log_account_change($user['user_id'], (- 1) * $surplus, 0, 0, 0, $change_desc);
//
//    /* 跳转 */
//    ecs_header('Location: user.php?act=order_detail&order_id=' . $order_id . "\n");
//    exit();
}

/* 编辑使用余额支付的处理 */

function action_act_edit_payment() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    /* 检查是否登录 */
    if ($_SESSION['user_id'] <= 0) {
        ecs_header("Location: ./\n");
        exit();
    }
    $sql = "SELECT pay_id FROM " . $ecs->table('payment') . " WHERE pay_code = '" . $_POST['pay_code'] . "'";
    $row = $db->getRow($sql);
    /* 检查支付方式 */
    $pay_id = $row['pay_id'];
    if ($pay_id <= 0) {
        ecs_header("Location: ./\n");
        exit();
    }

    include_once (ROOT_PATH . 'includes/lib_order.php');
    $payment_info = payment_info($pay_id);
    if (empty($payment_info)) {
        ecs_header("Location: ./\n");
        exit();
    }

    /* 检查订单号 */
    $order_id = intval($_POST['order_id']);
    if ($order_id <= 0) {
        ecs_header("Location: ./\n");
        exit();
    }

    /* 取得订单 */
    $order = order_info($order_id);
    if (empty($order)) {
        ecs_header("Location: ./\n");
        exit();
    }

    /* 检查订单用户跟当前用户是否一致 */
    if ($_SESSION['user_id'] != $order['user_id']) {
        ecs_header("Location: ./\n");
        exit();
    }

    /* 检查订单是否未付款和未发货 以及订单金额是否为0 和支付id是否为改变 */
    if ($order['pay_status'] != PS_UNPAYED || $order['shipping_status'] != SS_UNSHIPPED || $order['goods_amount'] <= 0 || $order['pay_id'] == $pay_id) {
        ecs_header("Location: user.php?act=order_detail&order_id=$order_id\n");
        exit();
    }

    $order_amount = $order['order_amount'] - $order['pay_fee'];
    $pay_fee = pay_fee($pay_id, $order_amount);
    $order_amount += $pay_fee;

    if ($_POST['pay_code'] == 'alipay_bank') {
        $defaultbank = $_POST['com_bank'];
        $sql = "UPDATE " . $ecs->table('order_info') . " SET pay_id='$pay_id', pay_name='$payment_info[pay_name]', pay_fee='$pay_fee', order_amount='$order_amount', defaultbank='$defaultbank'" . " WHERE order_id = '$order_id'";
    } else {
        $sql = "UPDATE " . $ecs->table('order_info') . " SET pay_id='$pay_id', pay_name='$payment_info[pay_name]', pay_fee='$pay_fee', order_amount='$order_amount'" . " WHERE order_id = '$order_id'";
    }

    $db->query($sql);

    /* 跳转 */
    ecs_header("Location: user.php?act=order_detail&order_id=$order_id\n");
    exit();
}

/* 保存订单详情收货地址 */

function action_save_order_address() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/lib_transaction.php');

    $address = array(
        'consignee' => isset($_POST['consignee']) ? compile_str(trim($_POST['consignee'])) : '', 'email' => isset($_POST['email']) ? compile_str(trim($_POST['email'])) : '', 'address' => isset($_POST['address']) ? compile_str(trim($_POST['address'])) : '', 'zipcode' => isset($_POST['zipcode']) ? compile_str(make_semiangle(trim($_POST['zipcode']))) : '', 'tel' => isset($_POST['tel']) ? compile_str(trim($_POST['tel'])) : '', 'mobile' => isset($_POST['mobile']) ? compile_str(trim($_POST['mobile'])) : '', 'sign_building' => isset($_POST['sign_building']) ? compile_str(trim($_POST['sign_building'])) : '', 'best_time' => isset($_POST['best_time']) ? compile_str(trim($_POST['best_time'])) : '', 'order_id' => isset($_POST['order_id']) ? intval($_POST['order_id']) : 0
    );
    if (save_order_address($address, $user_id)) {
        ecs_header('Location: user.php?act=order_detail&order_id=' . $address['order_id'] . "\n");
        exit();
    } else {
        $err = $GLOBALS['err'];
        if (empty($address['order_id'])) {
            $err->show($_LANG['order_list_lnk'], 'user.php?act=order_list');
        } else {
            $err->show($_LANG['order_list_lnk'], 'user.php?act=order_detail&order_id=' . $address['order_id']);
        }
    }
}

/* 我的红包列表 */

function action_bonus() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    $action = $GLOBALS['action'];

    include_once (ROOT_PATH . 'includes/lib_transaction.php');

    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
    $suppid = isset($_REQUEST['suppid']) ? intval($_REQUEST['suppid']) : - 1;

    $sql = "SELECT ub.supplier_id, ifnull( ssc.value, '网站自营' ) as suppname
			FROM " . $ecs->table('user_bonus') . " AS ub
			LEFT JOIN " . $ecs->table('supplier_shop_config') . " AS ssc ON ub.supplier_id = ssc.supplier_id
			AND ssc.code = 'shop_name'
			WHERE ub.user_id =" . $user_id . " 
			GROUP BY ub.supplier_id";

    $suppinfo = $db->getAll($sql);

    $where_suppid = '';
    if ($suppid > - 1) {
        $where_suppid = " AND supplier_id=" . $suppid;
    }

    $smarty->assign('suppinfo', $suppinfo);

    $record_count = $db->getOne("SELECT COUNT(*) FROM " . $ecs->table('user_bonus') . " WHERE user_id = '$user_id'" . $where_suppid);

    $pager = get_pager('user.php', array(
        'act' => $action, 'suppid' => $suppid
            ), $record_count, $page, 16);
    $bonus = get_user_bouns_list($user_id, $pager['size'], $pager['start'], $suppid);

    $smarty->assign('pager', $pager);
    $smarty->assign('bonus', $bonus);
    $smarty->display('user_transaction.dwt');
}

/* 我的团购列表 */

function action_group_buy() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/lib_transaction.php');

    // 待议
    $smarty->display('user_transaction.dwt');
}

/* 团购订单详情 */

function action_group_buy_detail() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/lib_transaction.php');

    // 待议
    $smarty->display('user_transaction.dwt');
}

// 用户推荐页面
function action_affiliate() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    $affiliate = unserialize($GLOBALS['_CFG']['affiliate']);

//	$goodsid = intval(isset($_REQUEST['goodsid']) ? $_REQUEST['goodsid'] : 0);
//	if(empty($goodsid))
//	{
//		// 我的推荐页面
//		
//		$page = ! empty($_REQUEST['page']) && intval($_REQUEST['page']) > 0 ? intval($_REQUEST['page']) : 1;
//		$size = ! empty($_CFG['page_size']) && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 10;
//		
//		empty($affiliate) && $affiliate = array();
//		
//		if(empty($affiliate['config']['separate_by']))
//		{
//			// 推荐注册分成
//			$affdb = array();
//			$num = count($affiliate['item']);
//			$up_uid = "'$user_id'";
//			$all_uid = "'$user_id'";
//			for($i = 1; $i <= $num; $i ++)
//			{
//				$count = 0;
//				if($up_uid)
//				{
//					$sql = "SELECT user_id FROM " . $ecs->table('users') . " WHERE parent_id IN($up_uid)";
//					$query = $db->query($sql);
//					$up_uid = '';
//					while($rt = $db->fetch_array($query))
//					{
//						$up_uid .= $up_uid ? ",'$rt[user_id]'" : "'$rt[user_id]'";
//						if($i < $num)
//						{
//							$all_uid .= ", '$rt[user_id]'";
//						}
//						$count ++;
//					}
//				}
//				$affdb[$i]['num'] = $count;
//				$affdb[$i]['point'] = $affiliate['item'][$i - 1]['level_point'];
//				$affdb[$i]['money'] = $affiliate['item'][$i - 1]['level_money'];
//			}
//			$smarty->assign('affdb', $affdb);
//			
//			$sqlcount = "SELECT count(*) FROM " . $ecs->table('order_info') . " o" . " LEFT JOIN" . $ecs->table('users') . " u ON o.user_id = u.user_id" . " LEFT JOIN " . $ecs->table('affiliate_log') . " a ON o.order_id = a.order_id" . " WHERE o.user_id > 0 AND (u.parent_id IN ($all_uid) AND o.is_separate = 0 OR a.user_id = '$user_id' AND o.is_separate > 0)";
//			
//			$sql = "SELECT o.*, a.log_id, a.user_id as suid,  a.user_name as auser, a.money, a.point, a.separate_type FROM " . $ecs->table('order_info') . " o" . " LEFT JOIN" . $ecs->table('users') . " u ON o.user_id = u.user_id" . " LEFT JOIN " . $ecs->table('affiliate_log') . " a ON o.order_id = a.order_id" . " WHERE o.user_id > 0 AND (u.parent_id IN ($all_uid) AND o.is_separate = 0 OR a.user_id = '$user_id' AND o.is_separate > 0)" . " ORDER BY order_id DESC";
//			
//			/*
//			 * SQL解释：
//			 *
//			 * 订单、用户、分成记录关联
//			 * 一个订单可能有多个分成记录
//			 *
//			 * 1、订单有效 o.user_id > 0
//			 * 2、满足以下之一：
//			 * a.直接下线的未分成订单 u.parent_id IN ($all_uid) AND o.is_separate = 0
//			 * 其中$all_uid为该ID及其下线(不包含最后一层下线)
//			 * b.全部已分成订单 a.user_id = '$user_id' AND o.is_separate > 0
//			 *
//			 */
//			
//			$affiliate_intro = nl2br(sprintf($_LANG['affiliate_intro'][$affiliate['config']['separate_by']], $affiliate['config']['expire'], $_LANG['expire_unit'][$affiliate['config']['expire_unit']], $affiliate['config']['level_register_all'], $affiliate['config']['level_register_up'], $affiliate['config']['level_money_all'], $affiliate['config']['level_point_all']));
//		}
//		else
//		{
//			// 推荐订单分成
//			$sqlcount = "SELECT count(*) FROM " . $ecs->table('order_info') . " o" . " LEFT JOIN" . $ecs->table('users') . " u ON o.user_id = u.user_id" . " LEFT JOIN " . $ecs->table('affiliate_log') . " a ON o.order_id = a.order_id" . " WHERE o.user_id > 0 AND (o.parent_id = '$user_id' AND o.is_separate = 0 OR a.user_id = '$user_id' AND o.is_separate > 0)";
//			
//			$sql = "SELECT o.*, a.log_id,a.user_id as suid, a.user_name as auser, a.money, a.point, a.separate_type,u.parent_id as up FROM " . $ecs->table('order_info') . " o" . " LEFT JOIN" . $ecs->table('users') . " u ON o.user_id = u.user_id" . " LEFT JOIN " . $ecs->table('affiliate_log') . " a ON o.order_id = a.order_id" . " WHERE o.user_id > 0 AND (o.parent_id = '$user_id' AND o.is_separate = 0 OR a.user_id = '$user_id' AND o.is_separate > 0)" . " ORDER BY order_id DESC";
//			
//			/*
//			 * SQL解释：
//			 *
//			 * 订单、用户、分成记录关联
//			 * 一个订单可能有多个分成记录
//			 *
//			 * 1、订单有效 o.user_id > 0
//			 * 2、满足以下之一：
//			 * a.订单下线的未分成订单 o.parent_id = '$user_id' AND o.is_separate = 0
//			 * b.全部已分成订单 a.user_id = '$user_id' AND o.is_separate > 0
//			 *
//			 */
//			$affiliate_intro = nl2br(sprintf($_LANG['affiliate_intro'][$affiliate['config']['separate_by']], $affiliate['config']['expire'], $_LANG['expire_unit'][$affiliate['config']['expire_unit']], $affiliate['config']['level_money_all'], $affiliate['config']['level_point_all']));
//		}
//		
//		$count = $db->getOne($sqlcount);
//		
//		$max_page = ($count > 0) ? ceil($count / $size) : 1;
//		if($page > $max_page)
//		{
//			$page = $max_page;
//		}
//		
//		$res = $db->SelectLimit($sql, $size, ($page - 1) * $size);
//		$logdb = array();
//		while($rt = $GLOBALS['db']->fetchRow($res))
//		{
//			if(! empty($rt['suid']))
//			{
//				// 在affiliate_log有记录
//				if($rt['separate_type'] == - 1 || $rt['separate_type'] == - 2)
//				{
//					// 已被撤销
//					$rt['is_separate'] = 3;
//				}
//			}
//			$rt['order_sn'] = substr($rt['order_sn'], 0, strlen($rt['order_sn']) - 5) . "***" . substr($rt['order_sn'], - 2, 2);
//			$logdb[] = $rt;
//		}
//		
//		$url_format = "user.php?act=affiliate&page=";
//		
//		$pager = array(
//			'page' => $page, 'size' => $size, 'sort' => '', 'order' => '', 'record_count' => $count, 'page_count' => $max_page, 'page_first' => $url_format . '1', 'page_prev' => $page > 1 ? $url_format . ($page - 1) : "javascript:;", 'page_next' => $page < $max_page ? $url_format . ($page + 1) : "javascript:;", 'page_last' => $url_format . $max_page, 'array' => array()
//		);
//		for($i = 1; $i <= $max_page; $i ++)
//		{
//			$pager['array'][$i] = $i;
//		}
//		
//		$smarty->assign('url_format', $url_format);
//		$smarty->assign('pager', $pager);
//		
//		$smarty->assign('affiliate_intro', $affiliate_intro);
//
//
//		$smarty->assign('affiliate_type', $affiliate['config']['separate_by']);
//		
//		$smarty->assign('logdb', $logdb);
//	}
//	else
//	{
//		// 单个商品推荐
//		$smarty->assign('userid', $user_id);
//		$smarty->assign('goodsid', $goodsid);
//		
//		$types = array(
//			1, 2, 3, 4, 5
//		);
//		$smarty->assign('types', $types);
//		
//		$goods = get_goods_info($goodsid);
//		$shopurl = $ecs->url();
//		$goods['goods_img'] = (strpos($goods['goods_img'], 'http://') === false && strpos($goods['goods_img'], 'https://') === false) ? $shopurl . $goods['goods_img'] : $goods['goods_img'];
//		$goods['goods_thumb'] = (strpos($goods['goods_thumb'], 'http://') === false && strpos($goods['goods_thumb'], 'https://') === false) ? $shopurl . $goods['goods_thumb'] : $goods['goods_thumb'];
//		$goods['shop_price'] = price_format($goods['shop_price']);
//		
//		$smarty->assign('goods', $goods);
//	}
    $page = !empty($_REQUEST['page']) && intval($_REQUEST['page']) > 0 ? intval($_REQUEST['page']) : 1;
    $size = !empty($_CFG['page_size']) && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 10;

    $countsql = "select count(*) from " . $GLOBALS['ecs']->table("users") . "    where parent_id=" . $user_id;
    $count = $GLOBALS['db']->getOne($countsql);

    $sql = "select real_name as user_name,reg_time,email,mobile_phone,user_id from " . $GLOBALS['ecs']->table("users") . " where parent_id=" . $user_id;
    $max_page = ($count > 0) ? ceil($count / $size) : 1;
    if ($page > $max_page) {
        $page = $max_page;
    }
    $url_format = "user.php?act=affiliate&affiliate_type=detail&page=";

    $pager = array(
        'page' => $page, 'size' => $size, 'sort' => '', 'order' => '', 'record_count' => $count, 'page_count' => $max_page, 'page_first' => $url_format . '1', 'page_prev' => $page > 1 ? $url_format . ($page - 1) : "javascript:;", 'page_next' => $page < $max_page ? $url_format . ($page + 1) : "javascript:;", 'page_last' => $url_format . $max_page, 'array' => array()
    );
    for ($i = 1; $i <= $max_page; $i ++) {
        $pager['array'][$i] = $i;
    }
    $smarty->assign('url_format', $url_format);
    $smarty->assign('pager', $pager);
    $res = $db->SelectLimit($sql, $size, ($page - 1) * $size);
    $affiliate_details = array();
    while ($rt = $GLOBALS['db']->fetchRow($res)) {
        $pattern = '/(\d{3})(\d{4})(\d{4})/i';
        $replacement = '$1****$3';
        $rt['mobile_phone'] = $rt['mobile_phone']; //preg_replace($pattern, $replacement, $rt['mobile_phone']);
        $rt['reg_time'] = local_date("Y-m-d G:i:s", $rt['reg_time']);
        $rt['email'] = mb_substr($rt['email'], 0, 1, 'utf-8') . '***' . mb_substr($rt['email'], -1, 1, 'utf-8');
        $rt['user_name'] = $rt['user_name']; //mb_substr($rt['user_name'], 0, 1, 'utf-8') . '***' . mb_substr($rt['user_name'], -1, 1, 'utf-8');
        if (empty($rt["user_name"])) {
            $wx_name = $GLOBALS['db']->getOne("SELECT nickname from " . $GLOBALS["ecs"]->table("weixin_user") . " WHERE ecuid='" . $rt['user_id'] . "' LIMIT 1");
            if (!empty($wx_name)) {
                $rt['user_name'] = "微信昵称：" . $wx_name;
            }
        }
        $affiliate_details[] = $rt;
    }
    $smarty->assign('affiliate_details', $affiliate_details);




    $countsql1 = "select count(*) from " . $GLOBALS['ecs']->table("account_log") . "    where user_id=" . $user_id . " and change_type=6";
    $count1 = $GLOBALS['db']->getOne($countsql1);

    $sql1 = "select change_time,change_desc,pay_points from " . $GLOBALS['ecs']->table("account_log") . " where user_id=" . $user_id . " and change_type=6" . " order by change_time desc";
    $max_page1 = ($count1 > 0) ? ceil($count1 / $size) : 1;
    if ($page > $max_page1) {
        $page = $max_page1;
    }
    $url_format1 = "user.php?act=affiliate&affiliate_type=award&page=";

    $pager1 = array(
        'page' => $page, 'size' => $size, 'sort' => '', 'order' => '', 'record_count' => $count1, 'page_count' => $max_page1, 'page_first' => $url_format1 . '1', 'page_prev' => $page > 1 ? $url_format . ($page - 1) : "javascript:;", 'page_next' => $page < $max_page ? $url_format1 . ($page + 1) : "javascript:;", 'page_last' => $url_format1 . $max_page, 'array' => array()
    );
    for ($i = 1; $i <= $max_page1; $i ++) {
        $pager1['array'][$i] = $i;
    }
    $smarty->assign('url_format1', $url_format1);
    $smarty->assign('pager1', $pager1);
    $res1 = $db->SelectLimit($sql1, $size, ($page - 1) * $size);
    $affiliate_details1 = array();
    while ($rt1 = $GLOBALS['db']->fetchRow($res1)) {
        $rt1['change_time'] = local_date("Y-m-d G:i:s", $rt1['change_time']);
        $rt1['change_desc'] = $rt1['change_desc'];
        $rt1['pay_points'] = $rt1['pay_points'];
        $affiliate_details1[] = $rt1;
    }
    $smarty->assign('affiliate_details1', $affiliate_details1);

    $affiliate['on'] = 1;
    $recomm = 1;
    //file_put_contents('./33$affiliate.txt',var_export($affiliate,true));
    //file_put_contents('./33$recomm.txt',var_export($recomm,true));
    $affiliate_type = $_GET["affiliate"];
    $smarty->assign('affiliate_type', $affiliate_type);
    $smarty->assign('shopname', $_CFG['shop_name']);
    $smarty->assign('userid', $user_id);
    $smarty->assign('shopurl', $ecs->url());
    $smarty->assign('logosrc', 'themes/' . $_CFG['template'] . '/images/logo.gif');
    $smarty->assign('recomm', $recomm);
    $smarty->assign('affiliate', $affiliate);
    $smarty->display('user_clips.dwt');
}

// 首页邮件订阅ajax操做和验证操作
function action_email_list() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    $job = $_GET['job'];

    if ($job == 'add' || $job == 'del') {
        if (isset($_SESSION['last_email_query'])) {
            if (time() - $_SESSION['last_email_query'] <= 30) {
                die($_LANG['order_query_toofast']);
            }
        }
        $_SESSION['last_email_query'] = time();
    }

    $email = trim($_GET['email']);
    $email = htmlspecialchars($email);

    if (!is_email($email)) {
        $info = sprintf($_LANG['email_invalid'], $email);
        die($info);
    }
    $ck = $db->getRow("SELECT * FROM " . $ecs->table('email_list') . " WHERE email = '$email'");
    if ($job == 'add') {
        if (empty($ck)) {
            $hash = substr(md5(time()), 1, 10);
            $sql = "INSERT INTO " . $ecs->table('email_list') . " (email, stat, hash) VALUES ('$email', 0, '$hash')";
            $db->query($sql);
            $info = $_LANG['email_check'];
            $url = $ecs->url() . "user.php?act=email_list&job=add_check&hash=$hash&email=$email";
            send_mail('', $email, $_LANG['check_mail'], sprintf($_LANG['check_mail_content'], $email, $_CFG['shop_name'], $url, $url, $_CFG['shop_name'], local_date('Y-m-d')), 1);
        } elseif ($ck['stat'] == 1) {
            $info = sprintf($_LANG['email_alreadyin_list'], $email);
        } else {
            $hash = substr(md5(time()), 1, 10);
            $sql = "UPDATE " . $ecs->table('email_list') . "SET hash = '$hash' WHERE email = '$email'";
            $db->query($sql);
            $info = $_LANG['email_re_check'];
            $url = $ecs->url() . "user.php?act=email_list&job=add_check&hash=$hash&email=$email";
            send_mail('', $email, $_LANG['check_mail'], sprintf($_LANG['check_mail_content'], $email, $_CFG['shop_name'], $url, $url, $_CFG['shop_name'], local_date('Y-m-d')), 1);
        }
        die($info);
    } elseif ($job == 'del') {
        if (empty($ck)) {
            $info = sprintf($_LANG['email_notin_list'], $email);
        } elseif ($ck['stat'] == 1) {
            $hash = substr(md5(time()), 1, 10);
            $sql = "UPDATE " . $ecs->table('email_list') . "SET hash = '$hash' WHERE email = '$email'";
            $db->query($sql);
            $info = $_LANG['email_check'];
            $url = $ecs->url() . "user.php?act=email_list&job=del_check&hash=$hash&email=$email";
            send_mail('', $email, $_LANG['check_mail'], sprintf($_LANG['check_mail_content'], $email, $_CFG['shop_name'], $url, $url, $_CFG['shop_name'], local_date('Y-m-d')), 1);
        } else {
            $info = $_LANG['email_not_alive'];
        }
        die($info);
    } elseif ($job == 'add_check') {
        if (empty($ck)) {
            $info = sprintf($_LANG['email_notin_list'], $email);
        } elseif ($ck['stat'] == 1) {
            $info = $_LANG['email_checked'];
        } else {
            if ($_GET['hash'] == $ck['hash']) {
                $sql = "UPDATE " . $ecs->table('email_list') . "SET stat = 1 WHERE email = '$email'";
                $db->query($sql);
                $info = $_LANG['email_checked'];
            } else {
                $info = $_LANG['hash_wrong'];
            }
        }
        show_message($info, $_LANG['back_home_lnk'], 'index.php');
    } elseif ($job == 'del_check') {
        if (empty($ck)) {
            $info = sprintf($_LANG['email_invalid'], $email);
        } elseif ($ck['stat'] == 1) {
            if ($_GET['hash'] == $ck['hash']) {
                $sql = "DELETE FROM " . $ecs->table('email_list') . "WHERE email = '$email'";
                $db->query($sql);
                $info = $_LANG['email_canceled'];
            } else {
                $info = $_LANG['hash_wrong'];
            }
        } else {
            $info = $_LANG['email_not_alive'];
        }
        show_message($info, $_LANG['back_home_lnk'], 'index.php');
    }
}

/* ajax 发送验证邮件 */

function action_send_hash_mail() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/cls_json.php');
    include_once (ROOT_PATH . 'includes/lib_passport.php');
    $json = new JSON();

    $result = array(
        'error' => 0, 'message' => '', 'content' => ''
    );

    if ($user_id == 0) {
        /* 用户没有登录 */
        $result['error'] = 1;
        $result['message'] = $_LANG['login_please'];
        die($json->encode($result));
    }

    if (send_regiter_hash($user_id)) {
        $result['message'] = $_LANG['validate_mail_ok'];
        die($json->encode($result));
    } else {
        $result['error'] = 1;
        $result['message'] = $GLOBALS['err']->last_message();
    }

    die($json->encode($result));
}

function action_track_packages() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    $action = $GLOBALS['action'];

    include_once (ROOT_PATH . 'includes/lib_transaction.php');
    include_once (ROOT_PATH . 'includes/lib_order.php');

    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

    $orders = array();

    $sql = "SELECT order_id,order_sn,invoice_no,shipping_id FROM " . $ecs->table('order_info') . " WHERE user_id = '$user_id' AND shipping_status = '" . SS_SHIPPED . "'";
    $res = $db->query($sql);
    $record_count = 0;
    while ($item = $db->fetch_array($res)) {
        $shipping = get_shipping_object($item['shipping_id']);

        if (method_exists($shipping, 'query')) {
            $query_link = $shipping->query($item['invoice_no']);
        } else {
            $query_link = $item['invoice_no'];
        }

        if ($query_link != $item['invoice_no']) {
            $item['query_link'] = $query_link;
            $orders[] = $item;
            $record_count += 1;
        }
    }
    $pager = get_pager('user.php', array(
        'act' => $action
            ), $record_count, $page);
    $smarty->assign('pager', $pager);
    $smarty->assign('orders', $orders);
    $smarty->display('user_transaction.dwt');
}

function action_order_query() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    $_GET['order_sn'] = trim(substr($_GET['order_sn'], 1));
    $order_sn = empty($_GET['order_sn']) ? '' : addslashes($_GET['order_sn']);
    include_once (ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON();

    $result = array(
        'error' => 0, 'message' => '', 'content' => ''
    );

    if (isset($_SESSION['last_order_query'])) {
        if (time() - $_SESSION['last_order_query'] <= 10) {
            $result['error'] = 1;
            $result['message'] = $_LANG['order_query_toofast'];
            die($json->encode($result));
        }
    }
    $_SESSION['last_order_query'] = time();

    if (empty($order_sn)) {
        $result['error'] = 1;
        $result['message'] = $_LANG['invalid_order_sn'];
        die($json->encode($result));
    }

    $sql = "SELECT order_id, order_status, shipping_status, pay_status, " . " shipping_time, shipping_id, invoice_no, user_id " . " FROM " . $ecs->table('order_info') . " WHERE order_sn = '$order_sn' LIMIT 1";

    $row = $db->getRow($sql);
    if (empty($row)) {
        $result['error'] = 1;
        $result['message'] = $_LANG['invalid_order_sn'];
        die($json->encode($result));
    }

    $order_query = array();
    $order_query['order_sn'] = $order_sn;
    $order_query['order_id'] = $row['order_id'];
    $order_query['order_status'] = $_LANG['os'][$row['order_status']] . ',' . $_LANG['ps'][$row['pay_status']] . ',' . $_LANG['ss'][$row['shipping_status']];

    if ($row['invoice_no'] && $row['shipping_id'] > 0) {
        $sql = "SELECT shipping_code FROM " . $ecs->table('shipping') . " WHERE shipping_id = '$row[shipping_id]'";
        $shipping_code = $db->getOne($sql);
        $plugin = ROOT_PATH . 'includes/modules/shipping/' . $shipping_code . '.php';
        if (file_exists($plugin)) {
            include_once ($plugin);
            $shipping = new $shipping_code();
            $order_query['invoice_no'] = $shipping->query((string) $row['invoice_no']);
        } else {
            $order_query['invoice_no'] = (string) $row['invoice_no'];
        }
    }

    $order_query['user_id'] = $row['user_id'];
    /* 如果是匿名用户显示发货时间 */
    if ($row['user_id'] == 0 && $row['shipping_time'] > 0) {
        $order_query['shipping_date'] = local_date($GLOBALS['_CFG']['date_format'], $row['shipping_time']);
    }
    $smarty->assign('order_query', $order_query);
    $result['content'] = $smarty->fetch('library/order_query.lbi');
    die($json->encode($result));
}

function action_transform_points() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    $action = $GLOBALS['action'];

    $rule = array();
    if (!empty($_CFG['points_rule'])) {
        $rule = unserialize($_CFG['points_rule']);
    }
    $cfg = array();
    if (!empty($_CFG['integrate_config'])) {
        $cfg = unserialize($_CFG['integrate_config']);
        $_LANG['exchange_points'][0] = empty($cfg['uc_lang']['credits'][0][0]) ? $_LANG['exchange_points'][0] : $cfg['uc_lang']['credits'][0][0];
        $_LANG['exchange_points'][1] = empty($cfg['uc_lang']['credits'][1][0]) ? $_LANG['exchange_points'][1] : $cfg['uc_lang']['credits'][1][0];
    }
    $sql = "SELECT user_id, user_name, pay_points, rank_points FROM " . $ecs->table('users') . " WHERE user_id='$user_id'";
    $row = $db->getRow($sql);
    if ($_CFG['integrate_code'] == 'ucenter') {
        $exchange_type = 'ucenter';
        $to_credits_options = array();
        $out_exchange_allow = array();
        foreach ($rule as $credit) {
            $out_exchange_allow[$credit['appiddesc'] . '|' . $credit['creditdesc'] . '|' . $credit['creditsrc']] = $credit['ratio'];
            if (!array_key_exists($credit['appiddesc'] . '|' . $credit['creditdesc'], $to_credits_options)) {
                $to_credits_options[$credit['appiddesc'] . '|' . $credit['creditdesc']] = $credit['title'];
            }
        }
        $smarty->assign('selected_org', $rule[0]['creditsrc']);
        $smarty->assign('selected_dst', $rule[0]['appiddesc'] . '|' . $rule[0]['creditdesc']);
        $smarty->assign('descreditunit', $rule[0]['unit']);
        $smarty->assign('orgcredittitle', $_LANG['exchange_points'][$rule[0]['creditsrc']]);
        $smarty->assign('descredittitle', $rule[0]['title']);
        $smarty->assign('descreditamount', round((1 / $rule[0]['ratio']), 2));
        $smarty->assign('to_credits_options', $to_credits_options);
        $smarty->assign('out_exchange_allow', $out_exchange_allow);
    } else {
        $exchange_type = 'other';

        $bbs_points_name = $user->get_points_name();
        $total_bbs_points = $user->get_points($row['user_name']);

        /* 论坛积分 */
        $bbs_points = array();
        foreach ($bbs_points_name as $key => $val) {
            $bbs_points[$key] = array(
                'title' => $_LANG['bbs'] . $val['title'], 'value' => $total_bbs_points[$key]
            );
        }

        /* 兑换规则 */
        $rule_list = array();
        foreach ($rule as $key => $val) {
            $rule_key = substr($key, 0, 1);
            $bbs_key = substr($key, 1);
            $rule_list[$key]['rate'] = $val;
            switch ($rule_key) {
                case TO_P:
                    $rule_list[$key]['from'] = $_LANG['bbs'] . $bbs_points_name[$bbs_key]['title'];
                    $rule_list[$key]['to'] = $_LANG['pay_points'];
                    break;
                case TO_R:
                    $rule_list[$key]['from'] = $_LANG['bbs'] . $bbs_points_name[$bbs_key]['title'];
                    $rule_list[$key]['to'] = $_LANG['rank_points'];
                    break;
                case FROM_P:
                    $rule_list[$key]['from'] = $_LANG['pay_points'];
                    $_LANG['bbs'] . $bbs_points_name[$bbs_key]['title'];
                    $rule_list[$key]['to'] = $_LANG['bbs'] . $bbs_points_name[$bbs_key]['title'];
                    break;
                case FROM_R:
                    $rule_list[$key]['from'] = $_LANG['rank_points'];
                    $rule_list[$key]['to'] = $_LANG['bbs'] . $bbs_points_name[$bbs_key]['title'];
                    break;
            }
        }
        $smarty->assign('bbs_points', $bbs_points);
        $smarty->assign('rule_list', $rule_list);
    }
    $smarty->assign('shop_points', $row);
    $smarty->assign('exchange_type', $exchange_type);
    $smarty->assign('action', $action);
    $smarty->assign('lang', $_LANG);
    $smarty->display('user_transaction.dwt');
}

function action_act_transform_points() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    $rule_index = empty($_POST['rule_index']) ? '' : trim($_POST['rule_index']);
    $num = empty($_POST['num']) ? 0 : intval($_POST['num']);

    if ($num <= 0 || $num != floor($num)) {
        show_message($_LANG['invalid_points'], $_LANG['transform_points'], 'user.php?act=transform_points');
    }

    $num = floor($num); // 格式化为整数

    $bbs_key = substr($rule_index, 1);
    $rule_key = substr($rule_index, 0, 1);

    $max_num = 0;

    /* 取出用户数据 */
    $sql = "SELECT user_name, user_id, pay_points, rank_points FROM " . $ecs->table('users') . " WHERE user_id='$user_id'";
    $row = $db->getRow($sql);
    $bbs_points = $user->get_points($row['user_name']);
    $points_name = $user->get_points_name();

    $rule = array();
    if ($_CFG['points_rule']) {
        $rule = unserialize($_CFG['points_rule']);
    }
    list($from, $to) = explode(':', $rule[$rule_index]);

    $max_points = 0;
    switch ($rule_key) {
        case TO_P:
            $max_points = $bbs_points[$bbs_key];
            break;
        case TO_R:
            $max_points = $bbs_points[$bbs_key];
            break;
        case FROM_P:
            $max_points = $row['pay_points'];
            break;
        case FROM_R:
            $max_points = $row['rank_points'];
    }

    /* 检查积分是否超过最大值 */
    if ($max_points <= 0 || $num > $max_points) {
        show_message($_LANG['overflow_points'], $_LANG['transform_points'], 'user.php?act=transform_points');
    }

    switch ($rule_key) {
        case TO_P:
            $result_points = floor($num * $to / $from);
            $user->set_points($row['user_name'], array(
                $bbs_key => 0 - $num
            )); // 调整论坛积分
            log_account_change($row['user_id'], 0, 0, 0, $result_points, $_LANG['transform_points'], ACT_OTHER);
            show_message(sprintf($_LANG['to_pay_points'], $num, $points_name[$bbs_key]['title'], $result_points), $_LANG['transform_points'], 'user.php?act=transform_points');

        case TO_R:
            $result_points = floor($num * $to / $from);
            $user->set_points($row['user_name'], array(
                $bbs_key => 0 - $num
            )); // 调整论坛积分
            log_account_change($row['user_id'], 0, 0, $result_points, 0, $_LANG['transform_points'], ACT_OTHER);
            show_message(sprintf($_LANG['to_rank_points'], $num, $points_name[$bbs_key]['title'], $result_points), $_LANG['transform_points'], 'user.php?act=transform_points');

        case FROM_P:
            $result_points = floor($num * $to / $from);
            log_account_change($row['user_id'], 0, 0, 0, 0 - $num, $_LANG['transform_points'], ACT_OTHER); // 调整商城积分
            $user->set_points($row['user_name'], array(
                $bbs_key => $result_points
            )); // 调整论坛积分
            show_message(sprintf($_LANG['from_pay_points'], $num, $result_points, $points_name[$bbs_key]['title']), $_LANG['transform_points'], 'user.php?act=transform_points');

        case FROM_R:
            $result_points = floor($num * $to / $from);
            log_account_change($row['user_id'], 0, 0, 0 - $num, 0, $_LANG['transform_points'], ACT_OTHER); // 调整商城积分
            $user->set_points($row['user_name'], array(
                $bbs_key => $result_points
            )); // 调整论坛积分
            show_message(sprintf($_LANG['from_rank_points'], $num, $result_points, $points_name[$bbs_key]['title']), $_LANG['transform_points'], 'user.php?act=transform_points');
    }
}

function action_act_transform_ucenter_points() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    $rule = array();
    if ($_CFG['points_rule']) {
        $rule = unserialize($_CFG['points_rule']);
    }
    $shop_points = array(
        0 => 'rank_points', 1 => 'pay_points'
    );
    $sql = "SELECT user_id, user_name, pay_points, rank_points FROM " . $ecs->table('users') . " WHERE user_id='$user_id'";
    $row = $db->getRow($sql);
    $exchange_amount = intval($_POST['amount']);
    $fromcredits = intval($_POST['fromcredits']);
    $tocredits = trim($_POST['tocredits']);
    $cfg = unserialize($_CFG['integrate_config']);
    if (!empty($cfg)) {
        $_LANG['exchange_points'][0] = empty($cfg['uc_lang']['credits'][0][0]) ? $_LANG['exchange_points'][0] : $cfg['uc_lang']['credits'][0][0];
        $_LANG['exchange_points'][1] = empty($cfg['uc_lang']['credits'][1][0]) ? $_LANG['exchange_points'][1] : $cfg['uc_lang']['credits'][1][0];
    }
    list($appiddesc, $creditdesc) = explode('|', $tocredits);
    $ratio = 0;

    if ($exchange_amount <= 0) {
        show_message($_LANG['invalid_points'], $_LANG['transform_points'], 'user.php?act=transform_points');
    }
    if ($exchange_amount > $row[$shop_points[$fromcredits]]) {
        show_message($_LANG['overflow_points'], $_LANG['transform_points'], 'user.php?act=transform_points');
    }
    foreach ($rule as $credit) {
        if ($credit['appiddesc'] == $appiddesc && $credit['creditdesc'] == $creditdesc && $credit['creditsrc'] == $fromcredits) {
            $ratio = $credit['ratio'];
            break;
        }
    }
    if ($ratio == 0) {
        show_message($_LANG['exchange_deny'], $_LANG['transform_points'], 'user.php?act=transform_points');
    }
    $netamount = floor($exchange_amount / $ratio);
    include_once (ROOT_PATH . './includes/lib_uc.php');
    $result = exchange_points($row['user_id'], $fromcredits, $creditdesc, $appiddesc, $netamount);
    if ($result === true) {
        $sql = "UPDATE " . $ecs->table('users') . " SET {$shop_points[$fromcredits]}={$shop_points[$fromcredits]}-'$exchange_amount' WHERE user_id='{$row['user_id']}'";
        $db->query($sql);
        $sql = "INSERT INTO " . $ecs->table('account_log') . "(user_id, {$shop_points[$fromcredits]}, change_time, change_desc, change_type)" . " VALUES ('{$row['user_id']}', '-$exchange_amount', '" . gmtime() . "', '" . $cfg['uc_lang']['exchange'] . "', '98')";
        $db->query($sql);
        show_message(sprintf($_LANG['exchange_success'], $exchange_amount, $_LANG['exchange_points'][$fromcredits], $netamount, $credit['title']), $_LANG['transform_points'], 'user.php?act=transform_points');
    } else {
        show_message($_LANG['exchange_error_1'], $_LANG['transform_points'], 'user.php?act=transform_points');
    }
}

/* 清除商品浏览历史 */

function action_clear_history() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    setcookie('ECS[history]', '', 1);
}

/* 代码增加_start By www.68ecshop.com */

function action_vc_login() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/lib_clips.php');
    $smarty->assign('info', get_user_default($user_id));

    $smarty->display('user_transaction.dwt');
}

function action_vc_login_act() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/lib_clips.php');
    $nowtime = gmtime();
    $vc_sn = isset($_POST['vcard']) ? trim($_POST['vcard']) : '';
    $vc_pwd = isset($_POST['pwd']) ? trim($_POST['pwd']) : '';
    if (empty($vc_sn) || empty($vc_pwd)) {
        show_message('卡号或密码都不能为空', '返回重新登录', 'user.php?act=vc_login');
    }
    $sql = "select vc.*, vt.type_money, vt.use_start_date, vt.use_end_date from " . $ecs->table('valuecard') . " AS vc " . " left join " . $ecs->table('valuecard_type') . " AS vt " . "on vc.vc_type_id = vt.type_id where vc.vc_sn= '$vc_sn' ";
    $vcrow = $db->getRow($sql);
    if (!$vcrow) {
        show_message('该储值卡号不存在', '请查证后重新登录', 'user.php?act=vc_login');
    }
    if ($vc_pwd != $vcrow['vc_pwd']) {
        show_message('密码错误', '请查证后重新登录', 'user.php?act=vc_login');
    }
    if ($nowtime < $vcrow['use_start_date']) {
        show_message('对不起，该储值卡还未到开始使用日期', '请过几天再登录试试', 'user.php?act=vc_login');
    }
    if ($nowtime > $vcrow['use_end_date']) {
        show_message('对不起，该储值卡已过期', '请换个卡号重新登录', 'user.php?act=vc_login');
    }
    if ($vcrow['user_id']) {
        show_message('对不起，该储值卡已使用', '请换个卡号重新登录', 'user.php?act=vc_login');
    }

    //$sql = 'INSERT INTO ' . $GLOBALS['ecs']->table('user_account') . ' (user_id, admin_user, amount, add_time, paid_time, admin_note, user_note, process_type, payment, is_paid)' . " VALUES ('$user_id', '', '$vcrow[type_money]', '" . gmtime() . "', '" . gmtime() . "', '', '储值卡充值', '0', '储值卡号：$vc_sn', 1)";
    //$GLOBALS['db']->query($sql);
    //log_account_change($user_id, $vcrow['type_money'], 0, 0, 0, '储值卡充值，卡号：' . $vc_sn, ACT_OTHER);

    $sql = "update " . $ecs->table('valuecard') . " set user_id='$user_id', used_time='$nowtime' where vc_id='$vcrow[vc_id]' ";
    $db->query($sql);
    // 是否开启余额变动给客户发短信-储值卡充值
    if ($_CFG['sms_user_money_change'] == 1) {
        $sql = "SELECT user_money,mobile_phone FROM " . $GLOBALS['ecs']->table('users') . " WHERE user_id = '$user_id'";
        $users = $GLOBALS['db']->getRow($sql);
        $content = sprintf($_CFG['sms_recharge_balance_add_tpl'], $vcrow['type_money'], $users['user_money'], $_CFG['sms_sign']);
        if ($users['mobile_phone']) {
            include_once ('send.php');
            sendSMS($users['mobile_phone'], $content);
        }
    }
    show_message('恭喜，已成功充值！', '返回上一页', 'user.php?act=vc_login');

    $smarty->display('user_transaction.dwt');
}

/* 代码增加_end By www.68ecshop.com */
/* 代码增加_start By www.68ecshop.com */

function action_tg_login() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/lib_clips.php');

    $smarty->display('user_transaction.dwt');
}

function action_tg_login_act() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/lib_clips.php');
    $nowtime = gmtime();
    $tg_sn = isset($_POST['tcard']) ? trim($_POST['tcard']) : '';
    $tg_pwd = isset($_POST['pwd']) ? trim($_POST['pwd']) : '';
    if (empty($tg_sn) || empty($tg_pwd)) {
        show_message('卡号或密码都不能为空', '返回重新输入', 'user.php?act=tg_login');
    }
    $sql = "select tg.*, tt.type_money, tt.type_money_count, tt.use_start_date, tt.use_end_date from " . $ecs->table('takegoods') . " AS tg " . " left join " . $ecs->table('takegoods_type') . " AS tt " . "on tg.type_id = tt.type_id where tg.tg_sn= '$tg_sn' ";
    $tgrow = $db->getRow($sql);
    if (!$tgrow) {
        show_message('该提货券不存在', '请查证后重新输入', 'user.php?act=tg_login');
    }
    if ($tg_pwd != $tgrow['tg_pwd']) {
        show_message('密码错误', '请查证后重新输入', 'user.php?act=tg_login');
    }
    if ($nowtime < $tgrow['use_start_date']) {
        show_message('对不起，该提货券 开始使用日期为 ' . local_date('Y-m-d H:i:s', $tgrow['use_start_date']), '请过几天再试试', 'user.php?act=tg_login');
    }
    if ($nowtime > $tgrow['use_end_date']) {
        show_message('对不起，该提货券已过期', '请换个券号重新输入', 'user.php?act=tg_login');
    }

    if ($tgrow['used_time'] and ( count(explode('@', $tgrow['used_time'])) >= $tgrow['type_money_count'])) {
        show_message('对不起，该提货券使用次数已用尽', '请换个券号重新输入', 'user.php?act=tg_login');
    }

    $_SESSION['takegoods_sn_68ecshop'] = $tg_sn;
    $_SESSION['takegoods_id_68ecshop'] = $tgrow['tg_id'];

    ecs_header("Location:takegoods.php");
}

function action_tg_order() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    $action = $GLOBALS['action'];

    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

    $record_count = $db->getOne("SELECT COUNT(*) FROM " . $ecs->table('takegoods_order') . " WHERE user_id = '$user_id'");

    $pager = get_pager('user.php', array(
        'act' => $action
            ), $record_count, $page, 10);

    $orders = get_takegoods_orders($user_id, $pager['size'], $pager['start']);

    $smarty->assign('pager', $pager);
    $smarty->assign('orders', $orders);

    $smarty->display('user_transaction.dwt');
}

function action_tg_order_confirm() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    $sql = "update " . $ecs->table('takegoods_order') . " set order_status='2' where rec_id= '$id' ";
    $db->query($sql);
    show_message('恭喜，成功确认收货！', '返回提货列表页', 'user.php?act=tg_order');
}

/* 商品评价/晒单 增加 by www.68ecshop.com */

function action_my_comment() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    $action = $GLOBALS['action'];

    $min_time = gmtime() - 86400 * $_CFG['comment_youxiaoqi'];
    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
    $count = $db->getOne("SELECT COUNT(*) FROM " . $ecs->table('order_goods') . " AS og 
						  LEFT JOIN " . $ecs->table('order_info') . " AS o ON og.order_id=o.order_id
						  WHERE o.user_id = '$user_id' AND o.shipping_time_end > 0 AND og.is_back = 0");
    $size = 20;
    $page_count = ($count > 0) ? intval(ceil($count / $size)) : 1;
    // 代码添加$o_id，if判断
    $o_id = $_REQUEST['order_id'];

    if ($o_id) {

        $sql = "SELECT og.*, o.add_time, o.shipping_time_end, o.order_id, g.goods_thumb, s.shaidan_id, s.pay_points AS shaidan_points, s.status AS shaidan_status,
			c.status AS comment_status,g.supplier_id,ifnull(ssc.value,'网站自营') AS shopname
			FROM " . $ecs->table('order_goods') . " AS og
			LEFT JOIN " . $ecs->table('order_info') . " AS o ON og.order_id=o.order_id
			LEFT JOIN " . $ecs->table('goods') . " AS g ON og.goods_id=g.goods_id
			LEFT JOIN " . $ecs->table('shaidan') . " AS s ON og.rec_id=s.rec_id
			LEFT JOIN " . $ecs->table('comment') . " AS c ON og.rec_id=c.rec_id
			LEFT JOIN " . $ecs->table('supplier_shop_config') . " AS ssc ON ssc.supplier_id=g.supplier_id AND ssc.code='shop_name'
			WHERE o.user_id = '$user_id' AND og.order_id = '$o_id' AND o.shipping_time_end > 0 AND og.is_back = 0 ORDER BY o.add_time DESC";
    } else {
        $sql = "SELECT og.*, o.add_time, o.shipping_time_end, o.order_id, g.goods_thumb, s.shaidan_id, s.pay_points AS 	shaidan_points, s.status AS shaidan_status, 
			c.status AS comment_status,g.supplier_id,ifnull(ssc.value,'网站自营') AS shopname 
			FROM " . $ecs->table('order_goods') . " AS og 
			LEFT JOIN " . $ecs->table('order_info') . " AS o ON og.order_id=o.order_id
			LEFT JOIN " . $ecs->table('goods') . " AS g ON og.goods_id=g.goods_id
			LEFT JOIN " . $ecs->table('shaidan') . " AS s ON og.rec_id=s.rec_id
			LEFT JOIN " . $ecs->table('comment') . " AS c ON og.rec_id=c.rec_id
			LEFT JOIN " . $ecs->table('supplier_shop_config') . " AS ssc ON ssc.supplier_id=g.supplier_id AND ssc.code='shop_name'
			WHERE o.user_id = '$user_id' AND o.shipping_time_end > 0 AND og.is_back = 0 ORDER BY o.add_time DESC";
    }
    $res = $db->selectLimit($sql, $size, ($page - 1) * $size);
    $points_list = array();
    while ($row = $db->fetchRow($res)) {
        $row['thumb'] = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $row['url'] = build_uri('goods', array(
            'gid' => $row['goods_id']
                ), $row['goods_name']);
        $row['add_time_str'] = local_date("Y-m-d", $row['add_time']);
        $row['goods_tags'] = $db->getAll("SELECT * FROM " . $ecs->table('goods_tag') . " WHERE goods_id = '$row[goods_id]'");
        $item_list[] = $row;
    }
    // 代码增加 for 循环
    for ($i = 1; $i < count($item_list); $i ++) {
        $item_list[$i]['o_id'] = $item_list[$i]['order_id'];
        unset($item_list[$i]['order_id']);
    }

    $smarty->assign('item_list', $item_list);

    // 统计信息
    $num['x'] = $db->getOne("SELECT COUNT(*) AS num FROM " . $ecs->table('order_goods') . " AS og 
							LEFT JOIN " . $ecs->table('order_info') . " AS o ON og.order_id=o.order_id
							WHERE o.user_id = '$user_id' AND og.is_back = 0 AND og.comment_state = 0 AND o.shipping_time_end > $min_time");
    $num['y'] = $db->getOne("SELECT COUNT(*) AS num FROM " . $ecs->table('order_goods') . " AS og 
							LEFT JOIN " . $ecs->table('order_info') . " AS o ON og.order_id=o.order_id
							WHERE o.user_id = '$user_id' AND og.is_back = 0 AND og.shaidan_state = 0 AND o.shipping_time_end > $min_time");
    $smarty->assign('num', $num);

    $pager = get_pager('user.php', array(
        'act' => $action
            ), $count, $page, $size);
    $smarty->assign('min_time', $min_time);
    $smarty->assign('pager', $pager);
    $smarty->display('user_my_comment.dwt');
}

function action_my_comment_send() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/lib_clips.php');

    $user_info = $db->getRow("SELECT * FROM " . $ecs->table('users') . " WHERE user_id = '$user_id'");
    $comment_type = 0;
    $id_value = $_POST['goods_id'];
    $email = $user_info['email'];
    $user_name = $user_info['user_name'];
    $user_id = $user_id;
    $content = $_POST['content'];
    //过滤js和html
    $search = array("'<script[^>]*?>.*?</script>'si", "'<[\/\!]*?[^<>]*?>'si");
    $content = preg_replace($search, '', $content);

    $comment_rank = $_POST['comment_rank'];

    // 代码增加
    $server = $_POST['server'];
    $send = $_POST['send'];
    $shipping = $_POST['shipping'];
    $o_id = $_REQUEST['o_id'];

    if (!$o_id) {
        $o_id = $_REQUEST['o1_id'];
    }

    // 代码增加

    $add_time = gmtime();
    $ip_address = real_ip();
    $status = ($_CFG['comment_check'] == 1) ? 0 : 1;
    $rec_id = $_POST['rec_id'];
    $hide_username = intval($_POST['hide_username']);
    $buy_time = $db->getOne("SELECT o.add_time FROM " . $ecs->table('order_info') . " AS o
							 LEFT JOIN " . $ecs->table('order_goods') . " AS og ON o.order_id=og.order_id
							 WHERE og.rec_id = '$rec_id'");

    /* 自定义标签 */
    $tags = ($_POST['comment_tag']) ? explode(",", $_POST['comment_tag']) : array();
    if (is_array($_POST['tags_zi'])) {
        foreach ($_POST["tags_zi"] as $tag) {
            $status = $_CFG['user_tag_check'];
            $db->query("INSERT INTO " . $ecs->table('goods_tag') . " (goods_id, tag_name, is_user, state) VALUES ('$id_value', '$tag', 1, '$status')");
            $tags[] = $db->insert_id();
        }
    }
    foreach ($tags as $tagid) {
        if ($tagid > 0) {
            $tagids[] = $tagid;
        }
    }
    $comment_tag = (is_array($tagids)) ? implode(",", $tagids) : '';

    // 代码增加o_id
    $sql = "INSERT INTO " . $ecs->table('comment') . "(comment_type, id_value, email, user_name, content, comment_rank, add_time, ip_address, user_id, status, rec_id, comment_tag, buy_time, hide_username, order_id)" . "VALUES ('$comment_type', '$id_value', '$email', '$user_name', '$content', '$comment_rank', '$add_time', '$ip_address', '$user_id', '$status', '$rec_id', '$comment_tag', '$buy_time', '$hide_username', '$o_id')";

    $db->query($sql);
    $db->query("UPDATE " . $ecs->table('order_goods') . " SET comment_state = 1 WHERE rec_id = '$rec_id'");

    // 代码增加
    if ($o_id) {
        $o_sn = $db->getOne("SELECT order_sn FROM " . $ecs->table('order_info') . " 				
							 WHERE order_id = '$o_id'");
        $sql = "INSERT INTO " . $ecs->table('shop_grade') . "(user_id, user_name, add_time,  server, send, shipping, order_id, order_sn)" . "VALUES ('$user_id', '$user_name', '$add_time', '$server', '$send', '$shipping', '$o_id', '$o_sn')";
        $db->query($sql);
    }
    // 代码增加

    clear_cache_files();

    if ($status == 0) {
        $msg = '您的信息提交成功，需要管理员审核后才能显示！';
    } else {
        $msg = '您的信息提交成功！';
    }
    echo "<script>alert('$msg');self.location='user.php?act=my_comment';</script>";
    exit();
}

/* 余额额支付密码_添加_START_www.68ecshop.com */

function action_check_surplus_open() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    $sql = 'SELECT `is_surplus_open`' . 'FROM  ' . $ecs->table("users") . ' WHERE `user_id` = \'' . $_SESSION['user_id'] . '\'' . 'LIMIT 1';
    $is_surplus_open = $GLOBALS['db']->getOne($sql);
    echo $is_surplus_open;
    exit();
}

function action_verify_surplus_password() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    $sql = 'SELECT COUNT( * )' . 'FROM  ' . $ecs->table("users") . ' WHERE `user_id` = \'' . $_SESSION['user_id'] . '\'' . 'AND `surplus_password` = \'' . md5($_GET['surplus_password']) . '\'';
    $count = $GLOBALS['db']->getOne($sql);
    echo $count;
    exit();
}

function action_get_verify_code() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once ('includes/cls_json.php');
    require (dirname(__FILE__) . '/send.php');
    $json = new JSON();
    $result = array();

    $phone = trim($_REQUEST['phone']);

    $sql = 'SELECT COUNT(*) FROM ' . $GLOBALS['ecs']->table('users') . ' WHERE `user_id` = \'' . $user_id . '\' AND `mobile_phone` = \'' . $phone . '\'';
    $count = $GLOBALS['db']->getOne($sql);

    if ($count == 0) {
        $result['result'] = 'fail';
        $result['message'] = '手机号跟用户不匹配';
        echo $json->encode($result);
    } else {
        $seed = "0123456789";
        $verifycode = mc_random(6, $seed);

        $content = '您的验证码为' . $verifycode;

        $ret = sendSMS($phone, $content);

        $sql = 'INSERT INTO ' . $ecs->table('verifycode') . '(`mobile`, `getip`, `verifycode`, `dateline`) VALUES (\'' . $phone . '\',\'' . real_ip() . '\',\'' . $verifycode . '\',\'' . gmtime() . '\')';
        $db->query($sql);
        if ($ret == '发送成功!' && $db->affected_rows() == 1) {
            $result['result'] = 'success';
            $result['message'] = '短信发送成功';
            echo $json->encode($result);
        } else {
            $result['result'] = 'fail';
            $result['message'] = '短信发送失败！';
            echo $json->encode($result);
        }
    }
}

function action_shaidan_send() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    $rec_id = intval($_GET['id']);
    $goods = $db->getRow("SELECT * FROM " . $ecs->table('order_goods') . " WHERE rec_id = '$rec_id'");

    $min_time = gmtime() - 86400 * $_CFG['comment_youxiaoqi'];
    $pan_1 = $db->getOne("select shipping_time_end from " . $ecs->table('order_info') . " where order_id = " . $goods['order_id']);
    $pan_1 = ($pan_1 > $min_time) ? 1 : 0;
    $smarty->assign('pan_1', $pan_1);

    $pan_2 = $db->getOne("select rec_id from " . $ecs->table('shaidan') . " where rec_id = '$rec_id'");
    $pan_2 = !empty($pan_2) ? 1 : 0;
    $smarty->assign('pan_2', $pan_2);

    $s_user = $db->getOne("select user_id from " . $ecs->table('order_info') . " where order_id = " . $goods['order_id']);
    $pan_3 = ($s_user == $_SESSION['user_id'] ? 0 : 1);
    $smarty->assign('pan_3', $pan_3);

    $smarty->assign('goods', $goods);

    $smarty->display('user_my_comment.dwt');
}

function action_shaidan_save() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (dirname(__FILE__) . '/includes/cls_image.php');
    $image = new cls_image($_CFG['bgcolor']);

    $rec_id = intval($_POST['rec_id']);
    $goods_id = intval($_POST['goods_id']);
    $title = trim($_POST['title']);
    $message = $_POST['message'];
    $add_time = gmtime();
    $status = $_CFG['shaidan_check'];
    $hide_username = intval($_POST['hide_username']);

    $sql = "INSERT INTO " . $ecs->table('shaidan') . "(rec_id, goods_id, user_id, title, message, add_time, status, hide_username)" . "VALUES ('$rec_id', '$goods_id', '$user_id', '$title', '$message', '$add_time', '$status', '$hide_username')";
    $db->query($sql);
    $shaidan_id = $db->insert_id();
    $db->query("UPDATE " . $ecs->table('order_goods') . " SET shaidan_state = 1 WHERE rec_id = '$rec_id'");

    // 处理图片
    $img_srcs = $_POST['img_srcs'];
    $img_names = $_POST['img_names'];
    if (is_array($img_srcs)) {
        foreach ($img_srcs as $i => $src) {
            $thumb = $image->make_thumb($src, 100, 100);
            $sql = "INSERT INTO " . $ecs->table('shaidan_img') . "(shaidan_id, `desc`, image, thumb)" . "VALUES ('$shaidan_id', '" . $img_names[$i] . "', '$src', '$thumb')";
            $db->query($sql);
        }
    }

    // 需要审核
    if ($status == 0) {
        $msg = '您的信息提交成功，需要管理员审核后才能显示！';
    }

    // 不需要审核
    else {
        $info = $db->GetRow("SELECT * FROM " . $ecs->table('shaidan') . " WHERE shaidan_id='$shaidan_id'");
        // 该商品第几位晒单者
        $res = $db->getAll("SELECT shaidan_id FROM " . $ecs->table("shaidan") . " WHERE goods_id = '$info[goods_id]' ORDER BY add_time ASC");
        foreach ($res as $key => $value) {
            if ($shaidan_id == $value['shaidan_id']) {
                $weizhi = $key + 1;
            }
        }
        // 图片数量
        $imgnum = count($img_srcs);

        // 是否赠送积分
        if ($info['is_points'] == 0 && $weizhi <= $_CFG['shaidan_pre_num'] && $imgnum >= $_CFG['shaidan_img_num']) {
            $pay_points = $_CFG['shaidan_pay_points'];
            $db->query("UPDATE " . $ecs->table('shaidan') . " SET pay_points = '$pay_points', is_points = 1 WHERE shaidan_id = '$shaidan_id'");
            $db->query("INSERT INTO " . $ecs->table('account_log') . "(user_id, rank_points, pay_points, change_time, change_desc, change_type) " . "VALUES ('$info[user_id]', 0, '" . $pay_points . "', " . gmtime() . ", '晒单获得积分', '99')");
            $log = $db->getRow("SELECT SUM(rank_points) AS rank_points, SUM(pay_points) AS pay_points FROM " . $ecs->table("account_log") . " WHERE user_id = '$info[user_id]'");
            $db->query("UPDATE " . $ecs->table('users') . " SET rank_points = '" . $log['rank_points'] . "', pay_points = '" . $log['pay_points'] . "' WHERE user_id = '$info[user_id]'");
        }

        $msg = '您的信息提交成功！';
    }
    echo "<script>alert('$msg');self.location='user.php?act=my_comment';</script>";
    exit();
}

function action_auction_list() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    include_once (ROOT_PATH . 'includes/lib_clips.php');
    $smarty->assign('prompt', get_user_prompt($user_id));
    $smarty->display('user_clips.dwt');
}

function action_get_tc_express() {
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];

    require_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    // 变量初始化
    $result_content = "";

    $kos_order_id = $db->getOne("select order_id from " . $ecs->table('kuaidi_order') . " where order_sn='" . $_REQUEST['expressno'] . "'");

    $sql = "select * from " . $ecs->table('kuaidi_order_status') . " where order_id='$kos_order_id'  order by status_id";
    $res_status = $db->query($sql);
    $have_shipping_info = 0;
    $shipping_info = "";
    while ($row_status = $db->fetchRow($res_status)) {
        if ($row_status['status_display'] == 1) {
            switch ($row_status['status_id']) {
                case 1 :
                    $shipping_info .= "您提交了订单，请等待确认。 (" . local_date('Y-m-d H:i:s', $row_status['status_time']) . ")";
                    break;
                case 2 :
                    $shipping_info .= "您的快件已经确认，等待快递员揽收。 (" . local_date('Y-m-d H:i:s', $row_status['status_time']) . ")";
                    break;
                case 3 :
                    $postman_id = $db->getOne("select postman_id from " . $ecs->table('kuaidi_order') . " where order_sn='" . $_REQUEST['expressno'] . "'");
                    $postman_info = $db->getRow("select postman_name, mobile from " . $ecs->table('postman') . " where postman_id=" . $postman_id);
                    $shipping_info .= "您的快件正在派送，快递员：" . $postman_info['postman_name'] . "，电话：" . $postman_info['mobile'] . " (" . local_date('Y-m-d H:i:s', $row_status['status_time']) . ")";
                    break;
                case 4 :
                    $shipping_info .= "您的快件已经签收。 (" . local_date('Y-m-d H:i:s', $row_status['status_time']) . ")";
                    break;
                case 5 :
                    $shipping_info .= "您的快件已被拒收。 (" . local_date('Y-m-d H:i:s', $row_status['status_time']) . ")";
                    break;
                case 6 :
                    $shipping_info .= "您拒收的快件已被退回。 (" . local_date('Y-m-d H:i:s', $row_status['status_time']) . ")";
                    break;
                case 7 :
                    $shipping_info .= "您的快件已经取消。 (" . local_date('Y-m-d H:i:s', $row_status['status_time']) . ")";
                    break;
            }

            $shipping_info .= "<br>";

            if ($row_status['status_id'] >= 1) {
                $have_shipping_info++;
            }
        }
    }
    if ($have_shipping_info) {
        $result_content = $shipping_info;
    } else {
        $result_content = '抱歉，暂时还没有该运单的物流信息哦！';
    }

    echo $json->encode($result_content);
}

function action_offline() {
    die("action forbidden!");
    exit;
    isCanToZd();
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    include_once (ROOT_PATH . 'includes/lib_clips.php');
    $smarty->assign('action', "offline");
    $page = !empty($_REQUEST['page']) && intval($_REQUEST['page']) > 0 ? intval($_REQUEST['page']) : 1;
    $size = !empty($_CFG['page_size']) && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 10;

    $cuser_id = $_SESSION['user_id'];
    $countsql = "SELECT count(*) FROM" . $GLOBALS['ecs']->table('order_bd') . " as s LEFT JOIN " . $GLOBALS['ecs']->table("users") . " a ON s.user_id = a.user_id where s.supplier_id=" . $cuser_id;
    $count = $GLOBALS['db']->getOne($countsql);

    $sql = "SELECT s.good_name,s.good_url,s.fp_url,s.order_amt,s.order_bdf,s.good_url, s.createtime,s.id,a.user_name,a.email,a.mobile_phone,b.supplier_name FROM" . $GLOBALS['ecs']->table('order_bd') . " as s LEFT JOIN " . $GLOBALS['ecs']->table("users") . " a ON s.user_id = a.user_id LEFT JOIN " . $GLOBALS['ecs']->table("supplier") . " b ON s.real_supplier_id = b.supplier_id where s.supplier_id=" . $cuser_id . " order by createtime desc ";
    $max_page = ($count > 0) ? ceil($count / $size) : 1;
    if ($page > $max_page) {
        $page = $max_page;
    }
//    $url_format = "user.php?act=offline&page=";
//
//    $pager = array(
//        'page' => $page, 'size' => $size, 'sort' => '', 'order' => '', 'record_count' => $count, 'page_count' => $max_page, 'page_first' => $url_format . '1', 'page_prev' => $page > 1 ? $url_format . ($page - 1) : "javascript:;", 'page_next' => $page < $max_page ? $url_format . ($page + 1) : "javascript:;", 'page_last' => $url_format . $max_page, 'array' => array()
//    );
    $pager = get_pager('user.php', array(
        'act' => "offline"), $count, $page);
    $smarty->assign('url_format', $url_format);
    $smarty->assign('pager', $pager);
    $res = $GLOBALS['db']->SelectLimit($sql, $size, ($page - 1) * $size);
    $affiliate_details = array();
    while ($rt = $GLOBALS['db']->fetchRow($res)) {
        $pattern = '/(\d{3})(\d{4})(\d{4})/i';
        $replacement = '$1****$3';
        $rt['mobile_phone'] = $rt['mobile_phone']; //preg_replace($pattern, $replacement, $rt['mobile_phone']);
        $rt['email'] = mb_substr($rt['email'], 0, 1, 'utf-8') . '***' . mb_substr($rt['email'], -1, 1, 'utf-8');
        $rt['user_name'] = $rt['user_name']; //mb_substr($rt['user_name'], 0, 1, 'utf-8') . '***' . mb_substr($rt['user_name'], -1, 1, 'utf-8');
        $rt['good_name'] = $rt['good_name'];
        $rt['good_url'] = $rt['good_url'];
        $rt['fp_url'] = $rt['fp_url'];
        $rt['order_amt'] = $rt['order_amt'];
        $rt['order_bdf'] = $rt['order_bdf'];
        $rt['createtime'] = local_date("Y-m-d G:i:s", $rt['createtime']);
        $affiliate_details[] = $rt;
    }
    $smarty->assign('offline_orders', $affiliate_details);
    $smarty->display('user_offline.dwt');
}

function action_offline_order() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    include_once (ROOT_PATH . 'includes/lib_clips.php');
    $smarty->assign('action', "offline_order");
    $page = !empty($_REQUEST['page']) && intval($_REQUEST['page']) > 0 ? intval($_REQUEST['page']) : 1;
    $size = !empty($_CFG['page_size']) && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 10;

    $cuser_id = $_SESSION['user_id'];
    $countsql = "SELECT count(*) FROM" . $GLOBALS['ecs']->table('order_bd') . " as s LEFT JOIN " . $GLOBALS['ecs']->table("users") . " a ON s.user_id = a.user_id where s.user_id=" . $cuser_id;
    $count = $GLOBALS['db']->getOne($countsql);

    $sql = "SELECT s.good_name,s.good_url,s.fp_url,s.order_amt,s.order_bdf,s.good_url, s.createtime,s.id,a.user_name,a.email,a.mobile_phone,b.supplier_name FROM" . $GLOBALS['ecs']->table('order_bd') . " as s LEFT JOIN " . $GLOBALS['ecs']->table("users") . " a ON s.user_id = a.user_id LEFT JOIN " . $GLOBALS['ecs']->table("supplier") . " b ON s.real_supplier_id = b.supplier_id where s.user_id=" . $cuser_id . " order by s.createtime desc ";

    $max_page = ($count > 0) ? ceil($count / $size) : 1;
    if ($page > $max_page) {
        $page = $max_page;
    }
//    $url_format = "user.php?act=offline_order&page=";
//
//    $pager = array(
//        'page' => $page, 'size' => $size, 'sort' => '', 'order' => '', 'record_count' => $count, 'page_count' => $max_page, 'page_first' => $url_format . '1', 'page_prev' => $page > 1 ? $url_format . ($page - 1) : "javascript:;", 'page_next' => $page < $max_page ? $url_format . ($page + 1) : "javascript:;", 'page_last' => $url_format . $max_page, 'array' => array()
//    );
    $pager = get_pager('user.php', array(
        'act' => "offline_order"), $count, $page);
    $smarty->assign('url_format', $url_format);
    $smarty->assign('pager', $pager);
    $res = $GLOBALS['db']->SelectLimit($sql, $size, ($page - 1) * $size);
    $affiliate_details = array();
    while ($rt = $GLOBALS['db']->fetchRow($res)) {
        $pattern = '/(\d{3})(\d{4})(\d{4})/i';
        $replacement = '$1****$3';
        $rt['mobile_phone'] = $rt['mobile_phone']; // preg_replace($pattern, $replacement, $rt['mobile_phone']);
        $rt['email'] = mb_substr($rt['email'], 0, 1, 'utf-8') . '***' . mb_substr($rt['email'], -1, 1, 'utf-8');
        $rt['user_name'] = mb_substr($rt['user_name'], 0, 1, 'utf-8') . '***' . mb_substr($rt['user_name'], -1, 1, 'utf-8');
        $rt['good_name'] = $rt['good_name'];
        $rt['good_url'] = $rt['good_url'];
        $rt['fp_url'] = $rt['fp_url'];
        $rt['order_amt'] = $rt['order_amt'];
        $rt['order_bdf'] = $rt['order_bdf'];
        $rt['createtime'] = local_date("Y-m-d G:i:s", $rt['createtime']);
        $rt['supplier_name'] = $rt['supplier_name']; //mb_substr($rt['supplier_name'], 0, 1, 'utf-8') . '***' . mb_substr($rt['supplier_name'], -1, 1, 'utf-8');
        $affiliate_details[] = $rt;
    }
    $smarty->assign('offline_orders', $affiliate_details);
    $smarty->display('user_offline.dwt');
}

function action_offline_detail() {
    die("action forbidden!");
    exit;
    isCanToZd();
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    $smarty->assign('fp_url', "/themes/" . $_CFG['template'] . "/images/fp.png");
    $smarty->assign('good_url', "/themes/" . $_CFG['template'] . "/images/qd.png");
    $sql = "select * from " . $GLOBALS['ecs']->table("supplier") . " where user_id=" . intval($_SESSION['user_id']) . "  and zuodan=1 ";
    $suppliers = $GLOBALS['db']->getAll($sql);
    $smarty->assign('suppliers', $suppliers);
    $smarty->display('user_offline.dwt');
}

function action_order_static() {
    die("action forbidden!");
    exit;
    isCanToZd();
    if (is_yunying()) {
        $user = $GLOBALS['user'];
        $_CFG = $GLOBALS['_CFG'];
        $_LANG = $GLOBALS['_LANG'];
        $smarty = $GLOBALS['smarty'];
        $db = $GLOBALS['db'];
        $ecs = $GLOBALS['ecs'];
        $user_id = $_SESSION['user_id'];
        $smarty->assign('action', 'order_static');
        //统计推荐的商家信息；
        $sql = "SELECT count(a.user_id) FROM " . $GLOBALS['ecs']->table("users") . " s LEFT JOIN " . $GLOBALS['ecs']->table("supplier") . " a ON s.user_id = a.user_id WHERE	a.`status` = 1 AND s.parent_id = " . $user_id;
        $sjcount = $GLOBALS['db']->getOne($sql);
        //统计推荐的会员信息；
        $sql = "SELECT count(s.user_id) FROM  " . $GLOBALS['ecs']->table("users") . " s  WHERE  s.parent_id = " . $user_id;
        $hycount = $GLOBALS['db']->getOne($sql);


        if (isset($_GET['startDate']) && !empty($_GET['startDate'])) {
            $smarty->assign("startDate", $_GET['startDate']);
            $start = local_strtotime($_GET['startDate']);
        }
        if (isset($_GET['endDate']) && !empty($_GET['endDate'])) {
            $smarty->assign("endDate", $_GET['endDate']);
            $end = local_strtotime($_GET['endDate']);
        }
        //查询所有线上、线下订单信息
        $is_offline = "";
        if (isset($_GET['is_offline'])) {
            $is_offline = $_GET['is_offline'];
            $smarty->assign("is_offline", $is_offline);
        }

        if (isset($_GET['supplier_name']) && !empty($_GET['supplier_name'])) {
            $supplier_name = $_GET['supplier_name'];
            $smarty->assign("supplier_name", $supplier_name);
        }

        //统计推荐商家的订单金额，服务费用
        $sql = "SELECT s.is_offline,sum(s.all_money) as all_money,sum(s.rebate_money) as rebate_money FROM " . $GLOBALS['ecs']->table('supplier_rebate_log') . " s LEFT JOIN " . $GLOBALS['ecs']->table("users") . " a on s.supplier_id=a.user_id LEFT JOIN " . $GLOBALS['ecs']->table('supplier') . " b ON s.supplier_id = b.user_id  WHERE b.status=1 and a.parent_id=" . $user_id . " group by s.is_offline";
        if ($is_offline !== "") {
            $sql = "SELECT s.is_offline,sum(s.all_money) as all_money,sum(s.rebate_money) as rebate_money FROM " . $GLOBALS['ecs']->table('supplier_rebate_log') . " s LEFT JOIN " . $GLOBALS['ecs']->table("users") . " a on s.supplier_id=a.user_id LEFT JOIN " . $GLOBALS['ecs']->table('supplier') . " b ON s.supplier_id = b.user_id  WHERE b.status=1 and a.parent_id=" . $user_id . " ";
            $sql = $sql . " and is_offline='" . $is_offline . "'";
        }
        if (!empty($supplier_name)) {
            $sql = $sql . " and b.supplier_name like '%" . mysql_like_quote($supplier_name) . "%'";
        }
        if (!empty($start)) {
            $sql = $sql . " and s.add_time>='" . $start . "'";
        }
        if (!empty($end)) {
            $sql = $sql . " and s.add_time<'" . $end . "'";
        }

        $sumJe = $GLOBALS['db']->getAll($sql);
        $xsSumje = array("all_money" => 0, "rebate_money" => 0);
        $xxSumje = array("all_money" => 0, "rebate_money" => 0);
        foreach ($sumJe as $value) {
            if ($value['is_offline'] * 1 === 1) {
                $xxSumje['all_money'] = $value['all_money'];
                $xxSumje['rebate_money'] = $value['rebate_money'];
            } else {
                $xsSumje['all_money'] = $value['all_money'];
                $xsSumje['rebate_money'] = $value['rebate_money'];
            }
        }
        $smarty->assign('sjcount', $sjcount);
        $smarty->assign('hycount', $hycount);
        $smarty->assign('xsSumje', $xsSumje);
        $smarty->assign('xxSumje', $xxSumje);
        $smarty->assign('Sumje', array('all_money' => $xsSumje['all_money'] + $xxSumje['all_money'], 'rebate_money' => $xsSumje['rebate_money'] + $xxSumje['rebate_money']));

        $sql = "SELECT count(*) FROM " . $GLOBALS['ecs']->table('supplier_rebate_log') . " s LEFT JOIN " . $GLOBALS['ecs']->table('users') . " a on s.supplier_id=a.user_id  LEFT JOIN " . $GLOBALS['ecs']->table('supplier') . " b ON s.supplier_id = b.user_id WHERE b.status=1 and a.parent_id= " . $user_id;
        if ($is_offline !== "") {
            $sql = $sql . " and is_offline='" . $is_offline . "'";
        }
        if (!empty($supplier_name)) {
            $sql = $sql . " and b.supplier_name like '%" . mysql_like_quote($supplier_name) . "%'";
        }
        if (!empty($start)) {
            $sql = $sql . " and s.add_time>='" . $start . "'";
        }
        if (!empty($end)) {
            $sql = $sql . " and s.add_time<'" . $end . "'";
        }
        $count = $GLOBALS['db']->getOne($sql);

        $page = !empty($_REQUEST['page']) && intval($_REQUEST['page']) > 0 ? intval($_REQUEST['page']) : 1;
        $size = 10; // !empty($_CFG['page_size']) && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) :
        $sql = "SELECT	s.supplier_id,	b.supplier_name,s.order_id,s.order_sn,	s.add_time,s.all_money,	s.pay_name,s.rebate_money,s.is_offline FROM
	" . $GLOBALS['ecs']->table('supplier_rebate_log') . " s LEFT JOIN " . $GLOBALS['ecs']->table('users') . " a ON s.supplier_id = a.user_id LEFT JOIN " . $GLOBALS['ecs']->table('supplier') . " b ON s.real_supplier_id = b.supplier_id and b.`status`=1 WHERE   a.parent_id = " . $user_id;
        if ($is_offline !== "") {
            $sql = $sql . " and s.is_offline='" . $is_offline . "'";
        }
        if (!empty($supplier_name)) {
            $sql = $sql . " and b.supplier_name like '%" . mysql_like_quote($supplier_name) . "%'";
        }
        if (!empty($start)) {
            $sql = $sql . " and s.add_time>='" . $start . "'";
        }
        if (!empty($end)) {
            $sql = $sql . " and s.add_time<'" . $end . "'";
        }
        $sql = $sql . " ORDER BY add_time DESC";
        $max_page = ($count > 0) ? ceil($count / $size) : 1;
        if ($page > $max_page) {
            $page = $max_page;
        }
//        $url_format = "user.php?act=order_static&page=";
//        $pager = array(
//            'page' => $page, 'size' => $size, 'sort' => '', 'order' => '', 'record_count' => $count, 'page_count' => $max_page, 'page_first' => $url_format . '1', 'page_prev' => $page > 1 ? $url_format . ($page - 1) : "javascript:;", 'page_next' => $page < $max_page ? $url_format . ($page + 1) : "javascript:;", 'page_last' => $url_format . $max_page, 'array' => array()
//        );
        $pager = get_pager('user.php', array(
            'act' => "order_static",
            'is_offline' => $is_offline,
            'startDate' => $_GET['startDate'],
            'endDate' => $_GET['endDate'],
            'supplier_name' => $_GET['supplier_name']
                ), $count, $page);

        $smarty->assign('pager', $pager);
        $res = $GLOBALS['db']->SelectLimit($sql, $size, ($page - 1) * $size);
        $affiliate_details = array();
        while ($rt = $GLOBALS['db']->fetchRow($res)) {
            $rt['pay_name'] = $rt['pay_name'];
            $rt['rebate_money'] = $rt['rebate_money'];
            $rt['is_offline'] = $rt['is_offline'] == 1 ? '线下订单' : '线上订单';
            $rt['all_money'] = $rt['all_money'];
            $rt['order_sn'] = $rt['order_sn'];
            $rt['order_id'] = $rt['order_id'];
            $rt['supplier_id'] = $rt['supplier_id'];
            $rt['add_time'] = local_date("Y-m-d G:i:s", $rt['add_time']);
            $rt['supplier_name'] = $rt['supplier_name']; //mb_substr($rt['supplier_name'], 0, 1, 'utf-8') . '***' . mb_substr($rt['supplier_name'], -1, 1, 'utf-8');
            $affiliate_details[] = $rt;
        }
        $smarty->assign('offline_orders', $affiliate_details);
        $smarty->display('user_offline.dwt');
    } else {
        header("Location:/");
    }
}

function action_supplier_static() {
    die("action forbidden!");
    exit;
    isCanToZd();
    if (is_yunying()) {
        $user = $GLOBALS['user'];
        $_CFG = $GLOBALS['_CFG'];
        $_LANG = $GLOBALS['_LANG'];
        $smarty = $GLOBALS['smarty'];
        $db = $GLOBALS['db'];
        $ecs = $GLOBALS['ecs'];
        $user_id = $_SESSION['user_id'];
        $smarty->assign('action', 'supplier_static');

        if (isset($_GET['startDate']) && !empty($_GET['startDate'])) {
            $smarty->assign("startDate", $_GET['startDate']);
            $start = local_strtotime($_GET['startDate']);
        }
        if (isset($_GET['endDate']) && !empty($_GET['endDate'])) {
            $smarty->assign("endDate", $_GET['endDate']);
            $end = local_strtotime($_GET['endDate']);
        }
        if (isset($_GET['supplier_name']) && !empty($_GET['supplier_name'])) {
            $supplier_name = $_GET['supplier_name'];
            $smarty->assign("supplier_name", $supplier_name);
        }

        //统计推荐商家的订单金额，服务费用
        $sql = "SELECT count(distinct s.supplier_id) FROM " . $GLOBALS['ecs']->table('supplier') . " s LEFT JOIN " . $GLOBALS['ecs']->table("users") . " a on s.user_id=a.user_id LEFT JOIN " . $GLOBALS['ecs']->table('supplier_rebate_log') . " b ON s.user_id = b.supplier_id   WHERE s.status=1 and  a.parent_id=" . $user_id . " ";
        if (!empty($supplier_name)) {
            $sql = $sql . " and s.supplier_name like '%" . mysql_like_quote($supplier_name) . "%'";
        }
        if (!empty($start)) {
            $sql = $sql . " and b.add_time>='" . $start . "'";
        }
        if (!empty($end)) {
            $sql = $sql . " and b.add_time<'" . $end . "'";
        }
        $count = $GLOBALS['db']->getOne($sql);
        $smarty->assign("count", $count);

        $page = !empty($_REQUEST['page']) && intval($_REQUEST['page']) > 0 ? intval($_REQUEST['page']) : 1;
        $size = 10; // !empty($_CFG['page_size']) && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) :
        $sql = "SELECT	s.supplier_id,	s.supplier_name,s.add_time,a.mobile_phone,SUM(b.all_money) as all_money,sum(b.rebate_money) as rebate_money FROM
	" . $GLOBALS['ecs']->table('supplier') . " s LEFT JOIN " . $GLOBALS['ecs']->table('users') . " a ON s.user_id = a.user_id LEFT JOIN " . $GLOBALS['ecs']->table('supplier_rebate_log') . " b ON s.user_id = b.supplier_id WHERE s.status=1 and  a.parent_id = " . $user_id;
        if (!empty($supplier_name)) {
            $sql = $sql . " and s.supplier_name like '%" . mysql_like_quote($supplier_name) . "%'";
        }
        if (!empty($start)) {
            $sql = $sql . " and b.add_time>='" . $start . "'";
        }
        if (!empty($end)) {
            $sql = $sql . " and b.add_time<'" . $end . "'";
        }
        $sql = $sql . " GROUP BY s.supplier_id,	s.supplier_name,s.add_time,a.mobile_phone ORDER BY add_time DESC";
        $max_page = ($count > 0) ? ceil($count / $size) : 1;
        if ($page > $max_page) {
            $page = $max_page;
        }
//        $url_format = "user.php?act=order_static&page=";
//        $pager = array(
//            'page' => $page, 'size' => $size, 'sort' => '', 'order' => '', 'record_count' => $count, 'page_count' => $max_page, 'page_first' => $url_format . '1', 'page_prev' => $page > 1 ? $url_format . ($page - 1) : "javascript:;", 'page_next' => $page < $max_page ? $url_format . ($page + 1) : "javascript:;", 'page_last' => $url_format . $max_page, 'array' => array()
//        );
        $pager = get_pager('user.php', array(
            'act' => "order_static",
            'startDate' => $_GET['startDate'],
            'endDate' => $_GET['endDate'],
            'supplier_name' => $_GET['supplier_name']
                ), $count, $page);
        $smarty->assign('pager', $pager);
        $res = $GLOBALS['db']->SelectLimit($sql, $size, ($page - 1) * $size);
        $affiliate_details = array();
        while ($rt = $GLOBALS['db']->fetchRow($res)) {
            $pattern = '/(\d{3})(\d{4})(\d{4})/i';
            $replacement = '$1****$3';
            $rt['mobile_phone'] = $rt['mobile_phone']; //preg_replace($pattern, $replacement, $rt['mobile_phone']);
            $rt['add_time'] = local_date("Y-m-d G:i:s", $rt['add_time']);
            $rt['all_money'] = $rt['all_money'];
            $rt['rebate_money'] = $rt['rebate_money'];
            $rt['supplier_name'] = $rt['supplier_name']; //mb_substr($rt['supplier_name'], 0, 1, 'utf-8') . '***' . mb_substr($rt['supplier_name'], -1, 1, 'utf-8');
            $affiliate_details[] = $rt;
        }
        $smarty->assign('offline_orders', $affiliate_details);
        $smarty->display('user_offline.dwt');
    } else {
        header("Location:/");
    }
}

function action_save_offline() {
    die("action forbidden!");
    exit;
    isCanToZd();
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    if (!isset($_POST["user_id"]) || !isset($_POST["good_name"]) || !isset($_POST["order_amt"])) {
        show_message("参数不合法", "返回上一页", 'user.php?act=offline_detail', 'info');
    }
    $sql = "select `user_id`,`user_name` from" . $GLOBALS["ecs"]->table("users") . " where `mobile_phone`='" . $_POST["user_id"] . "'";
    $check_user = $GLOBALS['db']->getRow($sql);
    if (!empty($check_user)) {
        if ($check_user["user_id"] * 1 == $_SESSION["user_id"] * 1) {
            show_message("不能给自己添加报单", "返回上一页", 'user.php?act=offline_detail', 'info');
            exit;
        }
        //exit(json_encode(array("code" => 1, "user_id" => $check_user["user_id"], 'user_name' => $check_user['user_name'])));
    } else {
        show_message("会员不存在", "返回上一页", 'user.php?act=offline_detail', 'info');
    }

    $bd_order_amt = $_POST["order_amt"];
    $amount = intval($bd_order_amt) * 0.1;
    if ($amount > 0) {
        $sql = "select `user_money` from" . $GLOBALS["ecs"]->table("users") . " where `user_id`='" . $_SESSION["user_id"] . "'";
        $checkje = $GLOBALS['db']->getOne($sql);
        $zuodan_set_max = $_CFG["zuodan_set_max"] * 1;
        if (empty($zuodan_set_max)) {
            $zuodan_set_max = 50000;
        }
        if ($amount > $checkje) {
            show_message("金额不合法,超过可用服务费", "返回上一页", 'user.php?act=offline_detail', 'info');
            //exit(json_encode(array("code" => 0, "message" => "")));
        } else {
            if ($amount > $zuodan_set_max) {
                show_message("金额不合法,超过系统设置最大服务费", "返回上一页", 'user.php?act=offline_detail', 'info');
            } else {
                // exit(json_encode(array("code" => 1)));
            }
        }
    } else {
        show_message("金额错误", "返回上一页", 'user.php?act=offline_detail', 'info');
    }
    if ($_FILES['fp_url']['size'] > 0) {
        include_once (ROOT_PATH . '/includes/cls_image.php');
        $fp_image = new cls_image($_CFG['bgcolor']);
        $fp_image_original = $fp_image->upload_image($_FILES['fp_url'], 'fp_url/' . date('Ym'));

        $fp_url_path = DATA_DIR . '/fp_url/' . date('Ym') . '/';
        $fp_url_thumb = $fp_image->make_thumb($fp_image_original, '250', '168', $fp_url_path);
        $fp_url = $fp_url_thumb ? $fp_url_thumb : $fp_image_original;
    }
    if ($_FILES['good_url']['size'] > 0) {
        include_once (ROOT_PATH . '/includes/cls_image.php');
        $good_image = new cls_image($_CFG['bgcolor']);
        $good_image_original = $good_image->upload_image($_FILES['good_url'], 'good_url/' . date('Ym'));

        $good_url_path = DATA_DIR . '/good_url/' . date('Ym') . '/';
        $good_url_thumb = $good_image->make_thumb($good_image_original, '250', '168', $good_url_path);
        $good_url = $good_url_thumb ? $good_url_thumb : $good_image_original;
    }

    $real_supplier_id = 0;
    if (!empty($_REQUEST['real_supplier_id'])) {
        $sql = "select supplier_id from " . $GLOBALS['ecs']->table("supplier") . " where user_id=" . intval($_SESSION['user_id']) . " and supplier_id=" . $_REQUEST['real_supplier_id'] . " and zuodan=1";
        $real_supplier_id = $GLOBALS['db']->getOne($sql);
        if (empty($real_supplier_id)) {
            show_message("所选择商家不合法", "返回上一页", 'user.php?act=offline_detail', 'info');
        } else {
            $real_supplier_id = $_REQUEST['real_supplier_id'];
        }
    }

    /* 代码增加_start By www.68ecshop.com */
    $GLOBALS['db']->startTrans();
    include_once (ROOT_PATH . '/includes/lib_order.php');
    $userDefault = get_user_default($_SESSION['user_id']);
    $bd_data = array(
        "user_id" => $check_user["user_id"],
        "supplier_id" => $_SESSION['user_id'],
        "order_amt" => $bd_order_amt * 1,
        "order_bdf" => $amount,
        "good_name" => $_POST['good_name'],
        "createtime" => gmtime(),
        "fp_url" => $fp_url,
        "good_url" => $good_url,
        "status" => 1,
        "order_sn" => get_order_sn(),
        'supplier_parent_id' => $userDefault['parent_id'],
        'real_supplier_id' => $real_supplier_id
    );
    $insertBd = $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table("order_bd"), $bd_data);
    if ($insertBd) {
        $bdId = $GLOBALS['db']->insert_id();
        $currentUser = get_user_default($check_user["user_id"]);
        $historyZsq = $currentUser["history_zsq"];
        $consum_money_value = $currentUser["consum_money_value"] * 1 + $bd_order_amt * 1;
        $addZsq = intval($consum_money_value / 1000) - $historyZsq; //需要赠送的赠送权数量
        $resUser = log_account_change($check_user["user_id"], 0, 0, 0, 0, "添加报单，报单记录:" . $bdId . "报单金额" . $bd_order_amt . "", 3, $bd_order_amt, 0, 0, $addZsq, $addZsq);

        $resAffiliate = true;
        if ($check_user['user_id'] > 0) {
            if ($GLOBALS["_CFG"]["recommend_set_open"] * 1 === 1) {
                $parentInfo = get_user_default($check_user['user_id']);
                if ($parentInfo["parent_id"] > 0) {
                    $change_desc = "推荐会员线下购物获得推荐赠送，" . sprintf($GLOBALS['_LANG']['order_gift_integral'], $bd_data['order_sn'], 4);
                    $recommend_points = $bd_data["order_amt"] * 1 * $GLOBALS["_CFG"]["recommend_set_percent"] / 100;
                    //$affiliate = "insert into " . $GLOBALS['ecs']->table('affiliate_log') . " VALUES('" . $bdId . "','" . gmtime() . "','" . $check_user['user_id'] . "','',0,'" . $recommend_points . "',0,'" . $change_desc . "')";
                    $affiliate = "insert into " . $GLOBALS['ecs']->table('affiliate_log') . "(order_id,`time`,`user_id`,`user_name`,`money`,`point`,`separate_type`,`change_desc`) VALUES('" . $bdId . "','" . gmtime() . "','" . $check_user['user_id'] . "','',0,'" . $recommend_points . "',0,'" . $change_desc . "')";
                    $resAffiliate = $GLOBALS['db']->query($affiliate);
                }
            }
        }

        $resSupp = log_account_change($_SESSION['user_id'], $amount * (-1), 0, 0, 0, "添加报单，报单记录:" . $bdId . "报单金额" . $bd_order_amt . ",报单服务费:" . $amount, 3, 0, 0, 0, 0, 0);

//        $supplier_idSql = "select supplier_id from " . $GLOBALS['ecs']->table("supplier") . " where `user_id`='" . $_SESSION['user_id'] . "' and status=1";
//        $supplier_id = $GLOBALS['db']->getOne($supplier_idSql);
//        if (empty($supplier_id)) {//如果是商家的话，计入商家的佣金分成体系
//        }
        $rebeat = array(
            "order_sn" => $bd_data["order_sn"],
            "order_id" => $bdId,
            "supplier_id" => $_SESSION['user_id'],
            "all_money" => $bd_data["order_amt"],
            "rebate_money" => $bd_data["order_bdf"],
            "result_money" => $bd_data["order_amt"] - $bd_data["order_bdf"],
            "pay_id" => 0,
            "pay_name" => "充值积分",
            "texts" => "线下支付",
            "add_time" => $bd_data['createtime'],
            'supplier_parent_id' => $userDefault['parent_id'],
            "is_offline" => 1,
            'real_supplier_id' => $real_supplier_id
        );
        $insertRebeat = $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table("supplier_rebate_log"), $rebeat);
//    }
        if ($resUser && $resSupp && $insertRebeat && $resAffiliate) {
            $GLOBALS['db']->commitTrans();
            show_message("提交报单成功", "查看报单记录", 'user.php?act=offline', 'info');
        } else {
            $GLOBALS['db']->rollbackTrans();
            show_message("提交报单失败", "返回上一页", 'user.php?act=offline_detail', 'info');
        }
    } else {
        $GLOBALS['db']->rollbackTrans();
        show_message("提交报单失败", "返回上一页", 'user.php?act=offline_detail', 'info');
    }
    /* 代码增加_end By www.68ecshop.com */
}

function action_checkuser() {
    isCanToZd(true);
    if (isset($_POST["mobile"]) && !empty($_POST["mobile"])) {
        $sql = "select `user_id`,`user_name`,`mobile_phone` from" . $GLOBALS["ecs"]->table("users") . " where `mobile_phone`='" . $_POST["mobile"] . "'";
        $check_user = $GLOBALS['db']->getRow($sql);
        if (!empty($check_user)) {
            if ($check_user["user_id"] * 1 == $_SESSION["user_id"] * 1) {
                exit(json_encode(array("code" => 0, "message" => "不能给自己添加报单")));
            } else {
                exit(json_encode(array("code" => 1, "user_id" => $check_user["user_id"], 'user_name' => $check_user['user_name'], 'mobile_phone' => $check_user['mobile_phone'])));
            }
        } else {
            exit(json_encode(array("code" => 0, "message" => "会员不存在")));
        }
    } else {
        exit(json_encode(array("code" => 0, "message" => "手机号码错误")));
    }
}

function action_checkje() {
    isCanToZd(true);
    if (isset($_POST["amount"]) && !empty($_POST["amount"])) {
        $amount = intval($_POST["amount"]) * 0.1;
        if ($amount > 0) {
            $sql = "select `user_money` from" . $GLOBALS["ecs"]->table("users") . " where `user_id`='" . $_SESSION["user_id"] . "'";
            $checkje = $GLOBALS['db']->getOne($sql);
            $zuodan_set_max = $_CFG["zuodan_set_max"] * 1;
            if (empty($zuodan_set_max)) {
                $zuodan_set_max = 50000;
            }
            if ($amount > $checkje) {
                exit(json_encode(array("code" => 0, "message" => "金额不合法,超过可用服务费")));
            } else {
                if ($amount > $zuodan_set_max) {
                    exit(json_encode(array("code" => 0, "message" => "金额不合法,超过系统设置最大服务费")));
                } else {
                    exit(json_encode(array("code" => 1)));
                }
            }
        } else {
            exit(json_encode(array("code" => 0, "message" => "金额错误")));
        }
    } else {
        exit(json_encode(array("code" => 0, "message" => "金额错误")));
    }

//if($_CFG[""])
}

function action_messages() {
    $smarty = $GLOBALS['smarty'];
    $smarty->assign('action', 'messages');
    $sql = "select count(*) from " . $GLOBALS['ecs']->table('user_message') . " where user_id='" . $_SESSION['user_id'] . "'";
    $count = $GLOBALS['db']->getOne($sql);
    $page = !empty($_REQUEST['page']) && intval($_REQUEST['page']) > 0 ? intval($_REQUEST['page']) : 1;
    $size = 10; // !empty($_CFG['page_size']) && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) :
    $max_page = ($count > 0) ? ceil($count / $size) : 1;
    if ($page > $max_page) {
        $page = $max_page;
    }
    $sql = "select id,`is_read` ,title,content,add_time from " . $GLOBALS['ecs']->table('user_message') . " where user_id='" . $_SESSION['user_id'] . "'";
    $sql = $sql . " ORDER BY add_time desc";
    $pager = get_pager('user.php', array(
        'act' => "mesasge"
            ), $count, $page);
    $smarty->assign('pager', $pager);
    $res = $GLOBALS['db']->SelectLimit($sql, $size, ($page - 1) * $size);
    $affiliate_details = array();
    while ($rt = $GLOBALS['db']->fetchRow($res)) {
        $rt['title'] = $rt['title'];
        $rt['content'] = $rt['content'];
        $rt['add_time'] = local_date("Y-m-d G:i:s", $rt['add_time']);
        $affiliate_details[] = $rt;
    }
    $smarty->assign('user_messages', $affiliate_details);
    $smarty->display('user_message.dwt');
}

function action_readmsg() {
    require_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;
    if (isset($_POST['ids'])) {
        if (empty($_POST['ids'])) {
            $sql = "update " . $GLOBALS['ecs']->table("user_message") . " set is_read=1 where user_id=" . $_SESSION['user_id'];
        } else {
            $sql = "update " . $GLOBALS['ecs']->table("user_message") . " set is_read=1 where user_id=" . $_SESSION['user_id'] . " AND id=" . $_POST['ids'];
        }
        if ($GLOBALS['db']->query($sql)) {
            die($json->encode(array('code' => 1, 'message' => '操作成功')));
        } else {
            die($json->encode(array('code' => 0, 'message' => '操作失败')));
        }
    } else {
        die($json->encode(array('code' => 0, 'message' => '数据错误')));
    }
}

function action_delmsg() {
    require_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;
    if (isset($_POST['ids'])) {
        if (empty($_POST['ids'])) {
            $sql = "delete from " . $GLOBALS['ecs']->table("user_message") . "  where user_id=" . $_SESSION['user_id'];
        } else {
            $sql = "delete from " . $GLOBALS['ecs']->table("user_message") . "  where user_id=" . $_SESSION['user_id'] . " AND id=" . $_POST['ids'];
        }
        if ($GLOBALS['db']->query($sql)) {
            die($json->encode(array('code' => 1, 'message' => '操作成功')));
        } else {
            die($json->encode(array('code' => 0, 'message' => '操作失败')));
        }
    } else {
        die($json->encode(array('code' => 0, 'message' => '数据错误')));
    }
}

/* 查看爱心互助列表 */

function action_mutual() {

    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    $action = 'mutual_it';

    if (empty($_REQUEST['types'])) {
        $page = !empty($_REQUEST['page']) && intval($_REQUEST['page']) > 0 ? intval($_REQUEST['page']) : 1;
        $size = !empty($_CFG['page_size']) && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 20;

        $countsql = "SELECT count(*) num FROM " . $GLOBALS['ecs']->table('mutual_info') . " as mi left join " . $GLOBALS['ecs']->table('mutual') . " m on m.mutual_id = mi.mutual_id  where mi.user_id = '" . $_SESSION['user_id'] . "'  order by mi.order_id desc ";
        $count = $GLOBALS['db']->getOne($countsql);
        $sql = "SELECT mi.* , m.title FROM " . $GLOBALS['ecs']->table('mutual_info') . " as mi left join " . $GLOBALS['ecs']->table('mutual') . " m on m.mutual_id = mi.mutual_id  where mi.user_id = '" . $_SESSION['user_id'] . "'  order by mi.order_id desc ";
        $max_page = ($count > 0) ? ceil($count / $size) : 1;
        if ($page > $max_page) {
            $page = $max_page;
        }
        $pager = get_pager('user.php', array('act' => "mutual", 'types' => 0), $count, $page, $size);
        $res = $GLOBALS['db']->SelectLimit($sql, $size, ($page - 1) * $size);
        $mutual_list = array();
        while ($rt = $GLOBALS['db']->fetchRow($res)) {
            $mutual_list[] = $rt;
        }
        $smarty->assign('mutual_list', $mutual_list);
        $smarty->assign('action', $action);
        $smarty->assign('pager', $pager);
        $smarty->display('user_mutual.dwt');
    } else {
        $action = 'mutual_me';
        $page = !empty($_REQUEST['page']) && intval($_REQUEST['page']) > 0 ? intval($_REQUEST['page']) : 1;
        $size = !empty($_CFG['page_size']) && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 20;

        $countsql = "SELECT count(*) num FROM " . $GLOBALS['ecs']->table('mutual_info') . " as mi left join " . $GLOBALS['ecs']->table('mutual') . " m on m.mutual_id = mi.mutual_id where m.user_id = '" . $_SESSION['user_id'] . "' and mi.order_status = 1 order by mi.order_id desc ";
        $count = $GLOBALS['db']->getOne($countsql);
        $sql = "SELECT mi.* , m.title FROM " . $GLOBALS['ecs']->table('mutual_info') . " as mi left join " . $GLOBALS['ecs']->table('mutual') . " m on m.mutual_id = mi.mutual_id where m.user_id = '" . $_SESSION['user_id'] . "' and mi.order_status = 1   order by mi.order_id desc ";
        $max_page = ($count > 0) ? ceil($count / $size) : 1;
        if ($page > $max_page) {
            $page = $max_page;
        }
        $pager = get_pager('user.php', array('act' => "mutual", 'types' => 1), $count, $page, $size);
        $res = $GLOBALS['db']->SelectLimit($sql, $size, ($page - 1) * $size);
        $mutual_list = array();
        while ($rt = $GLOBALS['db']->fetchRow($res)) {

            $rt['user_name'] = $rt['is_user'] ? '匿名' : ( $rt['consignee'] . ' / ' . $rt['mobile']);
            $mutual_list[] = $rt;
        }
        $smarty->assign('mutual_list', $mutual_list);
        $smarty->assign('action', $action);
        $smarty->assign('pager', $pager);
        $smarty->display('user_mutual.dwt');
    }
}

/* 查看爱心互助列表 */

function action_mutual_detail() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    $action = 'mutual_it';
    include_once('includes/lib_order.php');
    $order_id = intval($_REQUEST['order_id']);

    //查询互助信息
    $sql = "SELECT mi.* , m.title FROM " . $GLOBALS['ecs']->table('mutual_info') . " as mi left join " . $GLOBALS['ecs']->table('mutual') . " m on m.mutual_id = mi.mutual_id  where mi.order_id = '" . $order_id . "'  and mi.user_id = '" . $_SESSION['user_id'] . "'  order by mi.order_id desc ";
    $order_info = $GLOBALS['db']->getRow($sql);
    if (!$order_info) {
        show_message("信息错误", "返回上一页", 'user.php?act=mutual');
    }

    //是否付款  未付款生成二维码
    if ($order_info['order_status'] == 0) {
        $pay_id = intval($_REQUEST['pay_id']);
        if ($pay_id) {
            $order_info['pay_id'] = $pay_id;
        }
        
        $payment = payment_info($order_info['pay_id']);
        //重新生成pay_log
        $sql = 'SELECT * FROM' . $GLOBALS['ecs']->table('pay_log') . " WHERE order_id = '" . $order_info['order_id'] . "' AND order_type = 2 AND is_paid = 0";
        $pay_log = $GLOBALS['db']->getRow($sql);
        unset($pay_log['log_id'], $pay_log['pay_info']);
        $db->autoExecute($GLOBALS['ecs']->table('pay_log'), $pay_log, 'INSERT');
        $order_info['log_id'] = $db->insert_id();
//         include_once('includes/modules/payment/zhizunbao.php');
//         include_once('includes/modules/openepay/zhifutong.php');
//         $pay_obj = new zhifutong();
        include_once('includes/modules/payment/zhizunbao.php');
        $pay_obj = new zhizunbao();
        $orderUrl = $GLOBALS['ecs']->url();
        $order_pay=array(
            'order_amount' =>   $order_info['mutual_money'] ,
            'user_id' => $order_info['user_id'],
            'log_id' => $order_info['log_id'],
            'order_id' => $order_info['order_id'],
        );
        $pay_online = $pay_obj->get_code($order_pay, $payment['pay_code'], $orderUrl . "user.php?act=mutual", "PC");
        if ($pay_online) {
            $smarty->assign('pay_online', $pay_online);
        } else {
            $smarty->assign('pay_online', '系统错误，请稍后操作！');
        }

        $smarty->assign('log_ids', $order_info['log_id']);
     
    }

    /* 未发货，未付款时允许更换支付方式 */
    if ($order_info['mutual_money'] > 0 && $order_info['pay_status'] == 0) {
        $payment_list = available_payment_list(false, 0, true);

        /* 过滤掉当前支付方式和余额支付方式 */
        if (is_array($payment_list)) {
            foreach ($payment_list as $key => $payment) {
                if ($payment['pay_id'] == $order_info['pay_id'] || $order_info['pay_code'] == 'balance') {
                    unset($payment_list[$key]);
                }
            }
        }
        $smarty->assign('payment_list', $payment_list);
    }

    //处理数据
    $order_info['order_desc'] = $order_info['order_status'] ? '已付款' : '未付款';
    $smarty->assign('order_info', $order_info);
    $smarty->display('user_mutual.dwt');
}

/* 查看爱心互助列表 */

function action_insure() {

    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    $action = 'insure';
    $insure_sql = " SELECT * FROM " . $GLOBALS['ecs']->table('insure_info') . " where user_id = '" . $_SESSION['user_id'] . "' ";
    $insure_info = $GLOBALS['db']->getRow($insure_sql);
    
    if ($insure_info['order_status'] == 0) {
        require_once(ROOT_PATH . 'includes/lib_clips.php');
        require_once(ROOT_PATH . 'includes/lib_order.php');
//         $payment = payment_info($insure_info['pay_id']);

//         require_once (ROOT_PATH . 'includes/modules/PHPKit-CMBC/php_java.php');
//         include_once (ROOT_PATH . 'includes/modules/PHPKit-CMBC/CMBC_SDK_TOOLS.php');
//         $cmbc_sdk = new CMBC_SDK_TOOLS(0);

//         if ($payment['pay_code'] == "alipay") {
//             $selectTradeType = CMBC_SDK_TOOLS::API_ZFBQRCODE;
//         } elseif ($payment['pay_code'] == "weixin") {
//             $selectTradeType = CMBC_SDK_TOOLS::API_WXQRCODE;
//         } else {
//             show_message("信息错误", "返回上一页", 'user.php?act=mutual');
//         }

//         if (!empty($selectTradeType)) {

//             $insure_info['log_id'] = insert_pay_log($insure_info['order_id'], $insure_info['insure_money'], 3);

//             $payInfo = $cmbc_sdk->toCreateOrder($selectTradeType, $insure_info['insure_money'], "投保，订单号：" . $insure_info['order_sn'], $insure_info['log_id'], $GLOBALS['ecs']->get_domain() . "/testNotify.php", "");
//             if ($payInfo['code'] * 1 == 1) {
//                 $sql = "UPDATE " . $GLOBALS['ecs']->table('pay_log') . " SET `pay_info`='" . $payInfo['data'] . "' where `log_id`='" . $insure_info['log_id'] . "'";
//                 $GLOBALS['db']->query($sql);
//                 //生成支付代码
//                 $pay_online = $cmbc_sdk->toGetPayInfo($selectTradeType, $payInfo['data']);
//                 $smarty->assign('pay_online', $pay_online);
//                 $smarty->assign('log_ids', $insure_info['log_id']);
//             }
//         }
        
        $payment = payment_info($insure_info['pay_id']);
        //重新生成pay_log
        $insure_info['log_id'] = insert_pay_log($insure_info['order_id'], $insure_info['insure_money'], 3);
//         include_once('includes/modules/payment/zhizunbao.php');
//         include_once('includes/modules/openepay/zhifutong.php');
//         $pay_obj = new zhifutong();
        include_once('includes/modules/payment/zhizunbao.php');
        $pay_obj = new zhizunbao();
        $orderUrl = $GLOBALS['ecs']->url();
        $order_pay=array(
                'order_amount' =>   $insure_info['insure_money'] ,
                'user_id' => $insure_info['user_id'],
                'log_id' => $insure_info['log_id'],
                'order_id' => $insure_info['order_id'],
        );
        $pay_online = $pay_obj->get_code($order_pay, $payment['pay_code'], $orderUrl . "user.php?act=insure", "PC");
        if ($pay_online) {
            $smarty->assign('pay_online', $pay_online);
        } else {
            $smarty->assign('pay_online', '系统错误，请稍后操作！');
        }
        
    }
    
    
    if ($insure_info['order_status'] == 0) {
        $insure_info['order_s'] = '<div style="margin-top:10px">'.$pay_online .'</div>' ;
    } else {
        $insure_info['order_s'] = '已支付';
    }
    
    $smarty->assign('insure_info', $insure_info);
    $smarty->assign('action', $action);

    $smarty->display('user_mutual.dwt');
}

/*
 * 查询订单是否支付
 */

function action_paylog_now() {
    $id = intval($_REQUEST['id']);
    if ($id) {
        $sql = " SELECT * FROM `xbmall_pay_log` WHERE `log_id` = '" . $id . "' LIMIT 0, 1000 ";
        $rs = $GLOBALS['db']->getRow($sql);
        echo $rs['is_paid'];
        exit();
    }
    echo 0;
    exit();
}


function  setUserCenterValue(){

	$smarty = $GLOBALS['smarty'];
	$db = $GLOBALS['db'];
	$ecs = $GLOBALS['ecs'];
	$user_id = $_SESSION['user_id'];
	$smarty->assign('info', get_user_default($user_id)); // 获取用户中心默认页面所需的数据
	$sql="SELECT user_id  FROM ".$ecs->table('supplier_admin_user')." where uid=".$user_id;
	$supplierUserId =  $db->getOne($sql);	
	$smarty->assign('supplierUserId', $supplierUserId);// 商家/运营中心用户的id
	
}
/*
 * 香柏大家庭
 */

function action_bigfamily() {
	 
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    require_once(ROOT_PATH . 'includes/lib_clips.php');
    require_once(ROOT_PATH . 'includes/lib_order.php');
    
    $sql = "SELECT real_name,card   FROM " . $ecs->table("users") . " WHERE user_id='" . $user_id. "'";
    $user_info = $db->getRow($sql);
    if(empty($user_info['real_name']) || empty($user_info['card'])){
    	show_message("请先完成实名认证", "实名认证", 'user.php?act=profile');
    	return  ;
    }
    
    if (empty($_POST)) {
        //查询是否加入大家庭 is_bigfamily
        $sql = "SELECT is_bigfamily , parent_id FROM " . $ecs->table("users") . " WHERE user_id='" . $_SESSION['user_id'] . "'";
        $user_info = $db->getRow($sql);
        $is_bigfamily = $user_info['is_bigfamily'];
        $isShengJi=$_REQUEST['isShengJi'];
        if ($is_bigfamily ==0||$isShengJi==1) {
            //获取设置的变量
            $family_config = array(
                'bigfamily_u_m' => $_CFG['bigfamily_u_m'],
                'bigfamily_d_m' => $_CFG['bigfamily_d_m'],
            );
            $action = 'bigfamily_r';
            $smarty->assign("family_config", $family_config);
            $smarty->assign("action", $action);
            $smarty->assign("user_name", $_SESSION['user_name']);

            //查询支付方式
            // 给货到付款的手续费加<span id>，以便改变配送的时候动态显示
            $payment_list = available_payment_list(1, 0);
            $pay_balance_id = 0; //当前配置于的余额支付的递增id
            if (isset($payment_list)) {
                foreach ($payment_list as $key => $payment) {
                    if ($payment['is_cod'] == '1') {
                        $payment_list[$key]['format_pay_fee'] = '<span id="ECS_CODFEE">' . $payment['format_pay_fee'] . '</span>';
                    }
                    /* 如果有易宝神州行支付 如果订单金额大于300 则不显示 */
                    if ($payment['pay_code'] == 'yeepayszx' && $total['amount'] > 300) {
                        unset($payment_list[$key]);
                    }
                    /* 如果有余额支付 */
                    if ($payment['pay_code'] == 'balance') {
                        $pay_balance_id = $payment['pay_id'];
                        /* 如果未登录，不显示 */
                        if ($_SESSION['user_id'] == 0) {
                            unset($payment_list[$key]);
                        } else {
                            if ($_SESSION['flow_order']['pay_id'] == $payment['pay_id']) {
                                $smarty->assign('disable_surplus', 1);
                            }
                        }
                    }
                }
            }

            //获取推荐人
            if ($user_info['parent_id'] > 0) {
                $sql = "SELECT is_bigfamily , user_name , mobile_phone FROM " . $ecs->table("users") . " WHERE user_id='" . $user_info['parent_id'] . "'";
                $parent_info = $db->getRow($sql);
                $parent_desc = $parent_info['mobile_phone'];
//                 $parent_desc = $parent_info['user_name'] . "/" . $parent_info['mobile_phone'] . '/';
//                 $parent_desc.= $parent_info['is_bigfamily'] > 0 ? '已加入大家庭' : '未加入大家庭';
                $smarty->assign('parent_desc', $parent_desc);
            }

            $sql = "SELECT is_bigfamily , parent_id FROM " . $ecs->table("users") . " WHERE user_id='" . $_SESSION['user_id'] . "'";
            $user_info = $db->getRow($sql);
            $smarty->assign('is_bigfamily', $user_info['is_bigfamily']);
            $smarty->assign('pay_balance_id', $pay_balance_id);
            $smarty->assign('payment_list', $payment_list);

            $smarty->display('user_mutual.dwt');
        } else {
            $action = 'bigfamily';
            $sql = "SELECT * FROM " . $ecs->table("users") . " WHERE user_id='" . $_SESSION['user_id'] . "'";
            $user_info = $db->getRow($sql);
            $user_info['fimaly_tx'] = $_CFG['fimaly_tx'];
            $smarty->assign('is_bigfamily', $user_info['is_bigfamily']);
            $smarty->assign("user_info", $user_info);
            $smarty->assign("action", $action);
            $smarty->display('user_mutual.dwt');
        }
    } else {

        //支付处理
     

        $is_bigfamily = intval($_POST['money']) == 1 ? 1 : 2;
        $pay_id = intval($_POST['pay_id']);
        $payment = payment_info($pay_id);
       
        // 修改 大家庭选项
        if ($is_bigfamily > 0) {
            if ($pay_id < 1) {
                show_message("请选择支付方式");
                exit;
            }

            //查询推荐人是否存在
            $sql = "SELECT is_bigfamily , djt_parent_id FROM " . $ecs->table("users") . " WHERE user_id='" . $_SESSION['user_id'] . "'";
            $user_info = $db->getRow($sql);
            $tuijian_name = addslashes($_REQUEST['tuijian_name']);
            $tuijian_name_id = addslashes($_REQUEST['tuijian_name_id']);
            if ( $tuijian_name) {//允许在加入大家庭时更改推荐人
                $sql = "SELECT user_name,user_id FROM " . $ecs->table("users") . " WHERE mobile_phone ='" . $tuijian_name . "'";
                $parent_info = $db->getRow($sql);
                if ($parent_info && ($parent_info['user_id'] != $_SESSION['user_id'])) {//设置大家庭推荐人
                    $sql = " UPDATE " . $GLOBALS['ecs']->table('users') . " SET `djt_parent_id`='" . $parent_info['user_id'] . "' WHERE (`user_id`='" . $_SESSION['user_id'] . "') LIMIT 1 ";
                    $GLOBALS['db']->query($sql);
                } else {
                    show_message("推荐人有误");
                    exit;
                }
            }

            $order['order_sn'] = $_SESSION['user_id'];
            // 插入 pay_log 记录
            if ($is_bigfamily == 1) {
                $order['order_amount'] = $_CFG['bigfamily_u_m'];
            } else {
                $order['order_amount'] = $_CFG['bigfamily_d_m'];
            }

 
            //重新生成pay_log
            $order['log_id'] = insert_pay_log( $_SESSION['user_id'] , $order['order_amount'], 4);
//             include_once('includes/modules/payment/zhizunbao.php');
//             include_once('includes/modules/openepay/zhifutong.php');
//             $pay_obj = new zhifutong();
            include_once('includes/modules/payment/zhizunbao.php');
            $pay_obj = new zhizunbao();
            $orderUrl = $GLOBALS['ecs']->url();
            $order_pay=array(
                    'order_amount' =>   $order['order_amount'] ,
                    'user_id' => $_SESSION['user_id'],
                    'log_id' => $order['log_id'],
                    'order_id' => $_SESSION['user_id'],
            );
            //print_r($order_pay);
            $pay_online = $pay_obj->get_code($order_pay, $payment['pay_code'], $orderUrl . "user.php?act=bigfamily", "PC");
            if ($pay_online) {
                $smarty->assign('pay_online', $pay_online);
            } else {
                $smarty->assign('pay_online', '系统错误，请稍后操作！');
            }
            

 
            $action = 'bigfamily_d';
            $smarty->assign("action", $action);
            $smarty->display('user_mutual.dwt');
            exit;
        } else {
            show_message("请选择加入类型");
            exit;
        }
    }
}

/*
 * 是否加入大家庭
 */

function action_check_djt() {
    $sql = "SELECT is_bigfamily FROM " . $GLOBALS['ecs']->table("users") . " WHERE user_id='" . $_SESSION['user_id'] . "'";
    $is_bigfamily = $GLOBALS['db']->getOne($sql);
    if ($is_bigfamily > 0) {
        echo 1;
    } else {
        echo 0;//没有加入大家庭
    }
    exit;
}

/*
 * 是否加入大家庭
 */

function action_check_djt_tuijian() {
    $mobile_phone = $_REQUEST['mobile_phone'] + 0;
    $sql = "SELECT is_bigfamily , user_id , user_name , mobile_phone FROM " . $GLOBALS['ecs']->table("users") . " WHERE mobile_phone='" . $mobile_phone . "'";
    $parent_info = $GLOBALS['db']->getRow($sql);
    if ($parent_info) {
        $parent_desc = $parent_info['user_name'] . "/" . $parent_info['mobile_phone'] . '/';
        $parent_desc .= $parent_info['is_bigfamily'] > 0 ? '已加入大家庭' : '未加入大家庭';
        echo json_encode(array('parent_desc' => $parent_desc, 'parent_id' => $parent_info['user_id']));
    } else {
        echo json_encode(array('parent_desc' => '推荐人不存在', 'parent_id' => 0));
    }
    exit();
}

/*
 * 香柏大家庭    分红明细
 */

function action_djtfh_points_desc() {

    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    $action = 'djtfh_points_desc';

    //查询是否加入大家庭 is_bigfamily
    $sql = "SELECT is_bigfamily FROM " . $ecs->table("users") . " WHERE user_id='" . $_SESSION['user_id'] . "'";
    $is_bigfamily = $db->getOne($sql);
    if ($is_bigfamily == 0) {
        header("Location:user.php/");
    }

    //查询分红明细
    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
    $account_type = 'djtfh_points';

    /* 获取记录条数 */
    $sql = "SELECT COUNT(*) FROM " . $ecs->table('account_log') . " WHERE user_id = '$user_id'" . " AND $account_type <> 0 ";
    $record_count = $db->getOne($sql);

    // 分页函数
    $pager = get_pager('user.php', array(
        'act' => $action,
        'account_type' => $account_type
            ), $record_count, $page);

    //周: 获取账户变动记录
    $account_log = array();
    $sql = "SELECT log_id , change_time  , djtfh_points , change_desc FROM " . $ecs->table('account_log') . " WHERE user_id = '$user_id'" . " AND $account_type <> 0 " . " ORDER BY log_id DESC";
    $res = $GLOBALS['db']->selectLimit($sql, $pager['size'], $pager['start']);
    while ($row = $db->fetchRow($res)) {
        $row['change_time'] = local_date($_CFG['time_format'], $row['change_time']);
        $row['type'] = $row[$account_type] > 0 ? $_LANG['account_inc'] : $_LANG['account_dec'];
        $row['djtfh_points'] = abs($row['djtfh_points']);
        $row['short_change_desc'] = sub_str($row['change_desc'], 60);
        $account_log[] = $row;
    }

    $sql = "SELECT * FROM " . $ecs->table("users") . " WHERE user_id='" . $_SESSION['user_id'] . "'";
    $user_info = $db->getRow($sql);

    // 模板赋值
    $smarty->assign('user_info', $user_info);
    $smarty->assign('pager', $pager);
    $smarty->assign('account_log', $account_log);
    $smarty->assign("action", $action);
    $smarty->display('user_mutual.dwt');
}

//香柏大家庭 分红兑换
function action_djtfh_raply() {

    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    $sql = "select `real_name`,`card`,`bank`,`bank_kh`,`bank_num` from" . $ecs->table("users") . " where `user_id`='" . $user_id . "'";
    $profileRow = $db->getRow($sql);

    if (empty($profileRow["real_name"]) || empty($profileRow["card"]) || empty($profileRow["bank"]) || empty($profileRow['bank_kh']) || empty($profileRow['bank_num'])) {
        show_message("请将银行卡信息补充完整，才能继续进行积分兑换操作", "用户信息", 'user.php?act=profile');
    } else {
        $smarty->display('user_mutual.dwt');
    }
}

//消费分红 分红兑换
function action_xffh_raply() {
	
	$user = $GLOBALS['user'];
	$_CFG = $GLOBALS['_CFG'];
	$_LANG = $GLOBALS['_LANG'];
	$smarty = $GLOBALS['smarty'];
	$db = $GLOBALS['db'];
	$ecs = $GLOBALS['ecs'];
	$user_id = $_SESSION['user_id'];
	
	$sql = "select `real_name`,`card`,`bank`,`bank_kh`,`bank_num` from" . $ecs->table("users") . " where `user_id`='" . $user_id . "'";
	$profileRow = $db->getRow($sql);
	
	if (empty($profileRow["real_name"]) || empty($profileRow["card"]) || empty($profileRow["bank"]) || empty($profileRow['bank_kh']) || empty($profileRow['bank_num'])) {
		show_message("请将银行卡信息补充完整，才能继续进行积分兑换操作", "用户信息", 'user.php?act=profile');
	} else {
		$smarty->display('user_mutual.dwt');
	}
}




/**
 * 养老申请
 */
function action_yl_apply() {
	
	if(date("d")!=15){ 
		show_message("每月的15号为保险申请日", "返回保险页面", 'insure.php');
	}
			
	
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    $action = 'yl_apply';
    $smarty->assign('action', $action);
    $sql = "SELECT `pension_name`,`money`,`id` , pension_desc FROM " . $ecs->table('pension_infos') . " where `pension_status`=1";
    $pension_infos = $db->getAll($sql);
    $smarty->assign("pension_infos", $pension_infos);
    $sql = "SELECT real_name,mobile_phone,card FROM " . $ecs->table("users") . " WHERE user_id='" . $_SESSION['user_id'] . "'";
    $userinfos = $db->getRow($sql);
    $smarty->assign("userinfos", $userinfos);
    if (isset($_SESSION['yl_apply_time'])) {
        unset($_SESSION['yl_apply_time']);
    }
    $_SESSION['yl_apply_time'] = gmtime();
    $smarty->assign("postToken", md5($_SESSION['user_id'] . $_SESSION['yl_apply_time'] . AUTH_KEY));
    $smarty->display('user_yl_apply.dwt');
}

/**
 * 养老金申请列表
 */
function action_yl_list() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    $action = 'yl_list';
    $smarty->assign('action', $action);


    $page = !empty($_REQUEST['page']) && intval($_REQUEST['page']) > 0 ? intval($_REQUEST['page']) : 1;
    $size = !empty($_CFG['page_size']) && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 10;

    $cuser_id = $_SESSION['user_id'];
    $countsql = "SELECT count(*) FROM" . $GLOBALS['ecs']->table('pension_order') . " as s  where s.user_id=" . $cuser_id;
    $count = $GLOBALS['db']->getOne($countsql);

    $sql = "SELECT s.real_name,s.idcard,s.phone,s.add_time,s.`status`,a.`pension_name`,s.`money` FROM" . $GLOBALS['ecs']->table('pension_order') . " as s LEFT JOIN " . $GLOBALS['ecs']->table("pension_infos") . " as a  on s.yl_id=a.id where s.user_id=" . $cuser_id . " order by s.add_time desc ";

    $max_page = ($count > 0) ? ceil($count / $size) : 1;
    if ($page > $max_page) {
        $page = $max_page;
    }
    $url_format = "user.php?act=yl_list&page=";
    $pager = get_pager('user.php', array(
        'act' => "yl_list"), $count, $page);
    $smarty->assign('url_format', $url_format);
    $smarty->assign('pager', $pager);
    $res = $GLOBALS['db']->SelectLimit($sql, $size, ($page - 1) * $size);
    $affiliate_details = array();
    while ($rt = $GLOBALS['db']->fetchRow($res)) {
        $rt['real_name'] = $rt['real_name']; // preg_replace($pattern, $replacement, $rt['mobile_phone']);
        $rt['idcard'] = $rt['idcard'];
        $rt['mobile_phone'] = $rt['mobile_phone'];
        $rt['money'] = $rt['money'];
        $rt['pension_name'] = $rt['pension_name'];
        $rt['add_time'] = local_date("Y-m-d G:i:s", $rt['add_time']);
        $rt['status'] = $rt['status']; //mb_substr($rt['supplier_name'], 0, 1, 'utf-8') . '***' . mb_substr($rt['supplier_name'], -1, 1, 'utf-8');
        $affiliate_details[] = $rt;
    }
    $smarty->assign('yl_order_list', $affiliate_details);

    $smarty->display('user_yl_apply.dwt');
}

/**
 * 养老申请提交
 */
function action_act_yl_apply() {
    if ($_POST) {
        //20170315防止数据重复提交添加token验证
        if (!isset($_POST['postToken']) || empty($_POST['postToken']) || !isset($_SESSION['yl_apply_time']) || empty($_SESSION['yl_apply_time'])) {
        	show_message("提交异常，重复提交,请刷新页面重试");
            exit;
        } else {
            $postToken = $_POST['postToken'];
            if ($postToken != md5($_SESSION['user_id'] . $_SESSION['yl_apply_time'] . AUTH_KEY)) {
            	show_message("异常提交，请返回后刷新页面重试");
                exit;
            }
            //20170315防止数据重复提交添加token验证开始
//            if (isset($_SESSION['offline_detail_time'])) {
            unset($_SESSION['yl_apply_time']);
//            }
            //20170315防止数据重复提交添加token验证结束
        }
        if (isset($_POST['yl_id']) && !empty($_POST['yl_id'])) {
        	$sql=" select  add_time from  " . $GLOBALS ['ecs']->table ( "pension_order" ) . " WHERE `user_id`=" . $_SESSION ['user_id'] ;
        	$month = $GLOBALS ['db']->getOne($sql);
        	if($month!=null ){
        		$month = date("m", $month);
        		$nowMonth = date("m");
        		if( $month==$nowMonth){
        			show_message ( "您本月已经购买过保险了" );
        		}
        	}
            $yl_id = trim($_POST['yl_id']);
            $sql = "SELECT money FROM " . $GLOBALS['ecs']->table("pension_infos") . " WHERE `pension_status`=1 AND `id`='" . $yl_id . "'";
            $money = $GLOBALS['db']->getOne($sql);
            if (empty($money)) {
                show_message("请选择购买的养老金");
            }
            $sql = "SELECT yl_points FROM " . $GLOBALS['ecs']->table("users") . " WHERE `user_id`='" . $_SESSION['user_id'] . "'";
            $yl_points = $GLOBALS['db']->getOne($sql);
            if($money<30000){
            	if($yl_points>30000){
            		show_message( "由于您的养老积分大约30000, 您只能购买30000积分额度的养老保险" );
            	}
            }
            
            if ($money * 1 > $yl_points * 1) {
                show_message("您当前的养老积分不足，无法购买当前养老金");
            }
        } else {
            show_message("请选择购买的养老金");
        }
        if (isset($_POST['real_name']) && !empty($_POST['real_name'])) {
            $real_name = trim($_POST['real_name']);
        } else {
            show_message("请输入真实姓名");
        }
        if (isset($_POST['idcard']) && !empty($_POST['idcard'])) {
            $idcard = trim($_POST['idcard']);
        } else {
            show_message("请输入身份证号");
        }
        if (isset($_POST['mobile_phone']) && !empty($_POST['mobile_phone'])) {
            $mobile_phone = trim($_POST['mobile_phone']);
        } else {
            show_message("请输入手机号");
        }
        $GLOBALS['db']->startTrans();
        try {
            $data = array(
                "user_id" => $_SESSION['user_id'],
                "yl_id" => $yl_id,
                "money" => $money,
                "real_name" => $real_name,
                "add_time" => gmtime(),
                "idcard" => $idcard,
                "phone" => $mobile_phone
            );
            $res = $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table("pension_order"), $data);
            if ($res) {
                $res = log_account_change($_SESSION['user_id'], 0, 0, 0, 0, "购买养老金，扣除养老积分:" . $money, 7, 0, 0, 0, 0, 0, 0, 0, 0, 0, $money * (-1), 0, 0);
            }
            if ($res) {
                $GLOBALS['db']->commitTrans();
                show_message("养老金申请成功，请耐心等待审核", "查看购买记录", 'user.php?act=yl_list', 'info');
            } else {
                $GLOBALS['db']->rollbackTrans();
                show_message("养老金申请失败");
            }
        } catch (Exception $ex) {
            show_message("养老金申请失败");
        }
    }
}

function action_loan() {
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];
    $action = 'loan';
    $smarty->assign('action', $action);
    assign_template();	
    $smarty->display('user_loan.dwt');
}

function isCanToZd($isJson = false, $isreturn = false) {
    $isok = false;
    $sql = "select zuodan from " . $GLOBALS['ecs']->table("users") . " where user_id=" . intval($_SESSION['user_id']);
    $iszuodan = $GLOBALS['db']->getOne($sql);
    if (!empty($iszuodan)) {
        $isok = true;
    } else {
        if ($isreturn == false) {
            if ($isJson) {
                exit(json_encode(array("code" => 0, "message" => "操作失败")));
            } else {
                header("Location:/");
            }
        }
    }
//    if (is_yunying()) {
//        if ($GLOBALS['_CFG']["zuodan_set_open_1"] * 1 == 0) {
//            if ($isreturn == false) {
//                if ($isJson) {
//                    exit(json_encode(array("code" => 0, "message" => "操作失败")));
//                } else {
//                    header("Location:/");
//                }
//            } else {
//                $isok = false;
//            }
//        } else {
//            $isok = true;
//        }
//    } else {
//        if (is_supplier()) {
//            if ($GLOBALS['_CFG']["zuodan_set_open_2"] * 1 == 0) {
//                if ($isreturn == false) {
//                    if ($isJson) {
//                        exit(json_encode(array("code" => 0, "message" => "操作失败")));
//                    } else {
//                        header("Location:/");
//                    }
//                }
//            } else {
//                $isok = true;
//            }
//        } else {
//            if ($isreturn == false) {
//                if ($isJson) {
//                    exit(json_encode(array("code" => 0, "message" => "操作失败")));
//                } else {
//                    header("Location:/");
//                }
//            }
//        }
//    }
    return $isok;
}

/*
 * 是否是运营中心
 */

function is_yunying() {
    $cuser_id = $_SESSION['user_id'];
    $sql = "select user_id,zuodan from " . $GLOBALS['ecs']->table("region") . " where `user_id`='" . $cuser_id . "' ";
    $supplier_id = $GLOBALS['db']->getRow($sql);
    if (!empty($supplier_id) && ($supplier_id['zuodan'] * 1 > 0)) {
        return true;
    }
    return false;
}

/*
 * 是否是商家 
 */

function is_supplier() {
    $cuser_id = $_SESSION['user_id'];
    $sql = "select user_id,zuodan from " . $GLOBALS['ecs']->table("supplier") . " where `user_id`='" . $cuser_id . "' and `status`=1";
    $supplier_id = $GLOBALS['db']->getRow($sql);
    if (!empty($supplier_id) && ($supplier_id['zuodan'] * 1 > 0)) {
        return true;
    }
    return false;
}

/* 账户安全_end_www.68ecshop.com */
/* 余额额支付密码_添加_END_www.68ecshop.com */

function get_takegoods_orders($user_id, $num = 10, $start = 0) {
    $order_status = array(
        '0' => '提货成功，等待发货', '1' => '确认收货', '2' => '完成'
    );
    /* 取得订单列表 */
    $arr = array();

    $sql = "SELECT * " . " FROM " . $GLOBALS['ecs']->table('takegoods_order') . " WHERE user_id = '$user_id' ORDER BY rec_id DESC";
    $res = $GLOBALS['db']->SelectLimit($sql, $num, $start);

    while ($row = $GLOBALS['db']->fetchRow($res)) {
        $row['country_name'] = $GLOBALS['db']->getOne("select region_name from " . $GLOBALS['ecs']->table('region') . " where region_id='$row[country]' ");
        $row['province_name'] = $GLOBALS['db']->getOne("select region_name from " . $GLOBALS['ecs']->table('region') . " where region_id='$row[province]' ");
        $row['city_name'] = $GLOBALS['db']->getOne("select region_name from " . $GLOBALS['ecs']->table('region') . " where region_id='$row[city]' ");
        $row['district_name'] = $GLOBALS['db']->getOne("select region_name from " . $GLOBALS['ecs']->table('region') . " where region_id='$row[district]' ");
        $row['goods_url'] = build_uri('goods', array(
            'gid' => $row['goods_id']
                ), $row['goods_name']);
        $arr[] = array(
            'rec_id' => $row['rec_id'], 'tg_sn' => $row['tg_sn'], 'goods_name' => $row['goods_name'], 'address' => $row['country_name'] . $row['province_name'] . $row['city_name'] . $row['district_name'] . $row['address'], 'add_time' => local_date($GLOBALS['_CFG']['time_format'], $row['add_time']), 'order_status' => $row['order_status'], 'order_status_name' => $order_status[$row['order_status']], 'goods_url' => $row['goods_url'], 'handler' => $row['handler']
        );
    }

    return $arr;
}

/* 代码增加_end By www.68ecshop.com */
/* 代码增加_start By www.68ecshop.com */

function get_user_backorders($user_id, $num = 10, $start = 0) {
    /* 取得订单列表 */
    $arr = array();

    $sql = "SELECT bo.*, g.goods_name " . " FROM " . $GLOBALS['ecs']->table('back_order') . " AS bo left join " . $GLOBALS['ecs']->table('goods') . " AS g " . " on bo.goods_id=g.goods_id  " . " WHERE user_id = '$user_id' ORDER BY add_time DESC";
    $res = $GLOBALS['db']->SelectLimit($sql, $num, $start);

    while ($row = $GLOBALS['db']->fetchRow($res)) {

        $row['order_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['add_time']);
        $row['refund_money_1'] = price_format($row['refund_money_1'], false);

        $row['goods_url'] = build_uri('goods', array(
            'gid' => $row['goods_id']
                ), $row['goods_name']);
        $row['status_back_1'] = $row['status_back'];
        $row['status_back'] = $GLOBALS['_LANG']['bos'][(($row['back_type'] == 4 && $row['status_back'] != 8) ? $row['back_type'] : $row['status_back'])] . ' - ' . $GLOBALS['_LANG']['bps'][$row['status_refund']];

        $sql_goods = "SELECT * FROM " . $GLOBALS['ecs']->table('back_goods') . " WHERE back_id = " . $row['back_id'];
        $row['goods_list'] = $GLOBALS['db']->getAll($sql_goods);

        $arr[] = $row;
    }

//$arr['status_back'] = $GLOBALS['_LANG']['bos'][(($arr['back_type'] == 4 && $arr['status_back'] != 8) ? $row['back_type'] : $arr['status_back'])] . ' - ' . $GLOBALS['_LANG']['bps'][$arr['status_refund']];

    return $arr;
}

/* 代码增加_end By www.68ecshop.com */
/* 代码增加2014-12-23 by www.68ecshop.com _star */

function is_telephone($phone) {
    $chars = "/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/";
    if (preg_match($chars, $phone)) {
        return true;
    }
}

function mc_random($length, $char_str = 'abcdefghijklmnopqrstuvwxyz0123456789') {
    $hash = '';
    $chars = $char_str;
    $max = strlen($chars);
    for ($i = 0; $i < $length; $i ++) {
        $hash .= substr($chars, (rand(0, 1000) % $max), 1);
    }
    return $hash;
}

/* 代码增加2014-12-23 by www.68ecshop.com _end */

function get_user_payed($user_id) {
    $sql = "SELECT SUM(user_money) FROM " . $GLOBALS['ecs']->table('account_log') .
            " WHERE user_id = '$user_id' AND user_money < 0";

    return abs($GLOBALS['db']->getOne($sql));
}

function get_user_yue($user_id) {
    $sql = "SELECT user_money FROM " . $GLOBALS['ecs']->table('users') . "WHERE user_id = '$user_id'";
    $res = $GLOBALS['db']->getOne($sql);
    return $res;
}

function get_inv_complete_address($order) {
    if ($order['inv_type'] == 'normal_invoice') {
        $address = trim(get_inv_complete_region($order['order_id'], $order['inv_type']));
        if (empty($address)) {
            return $order['address'];
        } else {
            return '[' . $address . '] ' . $order['address'];
        }
    } elseif ($order['inv_type'] == 'vat_invoice') {
        $address = trim(get_inv_complete_region($order['order_id'], $order['inv_type']));
        if (empty($address)) {
            return $order['inv_consignee_address'];
        } else {
            return '[' . $address . '] ' . $order['inv_consignee_address'];
        }
    } else {
        return '';
    }
}

function get_inv_complete_region($order_id, $inv_type) {
    if (!empty($order_id)) {
        if ($inv_type == 'normal_invoice') {
            $sql = "SELECT concat(IFNULL(c.region_name, ''), '  ', IFNULL(p.region_name, ''), " . "'  ', IFNULL(t.region_name, ''), '  ', IFNULL(d.region_name, '')) AS region " . "FROM " . $GLOBALS['ecs']->table('order_info') . " AS o " . "LEFT JOIN " . $GLOBALS['ecs']->table('region') . " AS c ON o.country = c.region_id " . "LEFT JOIN " . $GLOBALS['ecs']->table('region') . " AS p ON o.province = p.region_id " . "LEFT JOIN " . $GLOBALS['ecs']->table('region') . " AS t ON o.city = t.region_id " . "LEFT JOIN " . $GLOBALS['ecs']->table('region') . " AS d ON o.district = d.region_id " . "WHERE o.order_id = '$order_id'";
            return $GLOBALS['db']->getOne($sql);
        } elseif ($inv_type == 'vat_invoice') {
            $sql = "SELECT concat(IFNULL(p.region_name, ''), " . "'  ', IFNULL(t.region_name, ''), '  ', IFNULL(d.region_name, '')) AS region " . "FROM " . $GLOBALS['ecs']->table('order_info') . " AS o " . "LEFT JOIN " . $GLOBALS['ecs']->table('region') . " AS p ON o.inv_consignee_province = p.region_id " . "LEFT JOIN " . $GLOBALS['ecs']->table('region') . " AS t ON o.inv_consignee_city = t.region_id " . "LEFT JOIN " . $GLOBALS['ecs']->table('region') . " AS d ON o.inv_consignee_district = d.region_id " . "WHERE o.order_id = '$order_id'";
            return $GLOBALS['db']->getOne($sql);
        } else {
            return ' ';
        }
    } else {
        return ' ';
    }
}

?>
