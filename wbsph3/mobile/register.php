<?php

/**
 *   注册
 * ============================================================================
 * $Id: register.php 17217 2015-08-07 06:29:08Z niqingyang $
 */
define('IN_ECS', true);

require (dirname(__FILE__) . '/includes/init.php');

/* 载入语言文件 */
require_once (ROOT_PATH . 'languages/' . $_CFG['lang'] . '/user.php');

$action = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : 'default';

$affiliate = unserialize($GLOBALS['_CFG']['affiliate']);
$smarty->assign('affiliate', $affiliate);
$back_act = '';

/* 如果是显示页面，对页面进行相应赋值 */
if (true) {
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
    $province_list = get_regions_wap(1);
//    $city_list = get_regions_wap($supplier['province']);
//    $district_list = get_regions_wap($supplier['city']);
//    $xiangcun_list = get_regions_wap($supplier['district']);

    $smarty->assign('shop_country', $_CFG['shop_country']);
    $smarty->assign('shop_province', get_regions(1, $_CFG['shop_country']));
    $smarty->assign('province_list', $province_list);
    $smarty->assign('re_num', $_REQUEST['re_num']);
    $smarty->assign('helps', get_shop_help()); // 网店帮助
    $smarty->assign('data_dir', DATA_DIR); // 数据目录
    $smarty->assign('action', $action);
    $smarty->assign('lang', $_LANG);
}

/* 路由 */

$function_name = 'action_' . $action;

if (!function_exists($function_name)) {
    $function_name = "action_default";
}
 
call_user_func($function_name);

/* 路由 */

/* 发送注册邮箱验证码到邮箱 */

function action_send_email_code() {
    // 获取全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    /* 载入语言文件 */
    require_once (ROOT_PATH . 'languages/' . $_CFG['lang'] . '/user.php');

    require_once (ROOT_PATH . 'includes/lib_validate_record.php');

    $email = trim($_REQUEST['email']);

    /* 验证码检查 */
    if ((intval($_CFG['captcha']) & CAPTCHA_REGISTER) && gd_version() > 0) {
        if (empty($_POST['captcha'])) {
            exit($_LANG['invalid_captcha']);
            return;
        }

        /* 检查验证码 */
        include_once ('includes/cls_captcha.php');

        $captcha = new captcha();

        if (!$captcha->check_word(trim($_POST['captcha']))) {
            exit($_LANG['invalid_captcha']);
            return;
        }
    }

    if (empty($email)) {
        exit("邮箱不能为空");
        return;
    } else if (!is_email($email)) {
        exit("邮箱格式不正确");
        return;
    } else if (check_validate_record_exist($email)) {

        $record = get_validate_record($email);

        /**
         * 检查是过了限制发送邮件的时间
         */
        if (time() - $record['last_send_time'] < 60) {
            echo ("每60秒内只能发送一次注册邮箱验证码，请稍候重试");
            return;
        }
    }

    require_once (ROOT_PATH . 'includes/lib_passport.php');

    /* 设置验证邮件模板所需要的内容信息 */
    $template = get_mail_template('reg_email_code');
    // 生成邮箱验证码
    $email_code = rand_number(6);

    $GLOBALS['smarty']->assign('email_code', $email_code);
    $GLOBALS['smarty']->assign('shop_name', $GLOBALS['_CFG']['shop_name']);
    $GLOBALS['smarty']->assign('send_date', date($GLOBALS['_CFG']['date_format']));

    $content = $GLOBALS['smarty']->fetch('str:' . $template['template_content']);

    /* 发送激活验证邮件 */
    $result = send_mail($email, $email, $template['template_subject'], $content, $template['is_html']);
    if ($result) {
        // 保存验证码到Session中
        $_SESSION[VT_EMAIL_REGISTER] = $email;
        // 保存验证记录
        save_validate_record($email, $email_code, VT_EMAIL_REGISTER, time(), time() + 30 * 60);

        echo 'ok';
    } else {
        echo '注册邮箱验证码发送失败';
    }
}

/* 发送注册邮箱验证码到邮箱 */

function action_send_mobile_code() {

    // 获取全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $user_id = $_SESSION['user_id'];

    /* 载入语言文件 */
    require_once (ROOT_PATH . 'languages/' . $_CFG['lang'] . '/user.php');

    require_once (ROOT_PATH . 'includes/lib_validate_record.php');

    $mobile_phone = trim($_REQUEST['mobile_phone']);

    /* 验证码检查 */
    if ((intval($_CFG['captcha']) & CAPTCHA_REGISTER) && gd_version() > 0) {
        if (empty($_POST['captcha'])) {
            exit($_LANG['invalid_captcha']);
            return;
        }

        /* 检查验证码 */
        include_once ('includes/cls_captcha.php');

        $captcha = new captcha();

        if (!$captcha->check_word(trim($_POST['captcha']))) {
            exit($_LANG['invalid_captcha']);
            return;
        }
    }

    if (empty($mobile_phone)) {
        exit("手机号不能为空");
        return;
    } else if (!is_mobile_phone($mobile_phone)) {
        exit("手机号格式不正确");
        return;
    } else if (check_validate_record_exist($mobile_phone)) {
        // 获取数据库中的验证记录
        $record = get_validate_record($mobile_phone);

        /**
         * 检查是过了限制发送短信的时间
         */
        $last_send_time = $record['last_send_time'];
        $expired_time = $record['expired_time'];
        $create_time = $record['create_time'];
        $count = $record['count'];

        // 每天每个手机号最多发送的验证码数量
        $max_sms_count = 10;
        // 发送最多验证码数量的限制时间，默认为24小时
        $max_sms_count_time = 60 * 60 * 24;

        if ((time() - $last_send_time) < 60) {
            echo ("每60秒内只能发送一次短信验证码，请稍候重试");
            return;
        } else if (time() - $create_time < $max_sms_count_time && $record['count'] > $max_sms_count) {
            echo ("您发送验证码太过于频繁，请稍后重试！");
            return;
        } else {
            $count ++;
        }
    }

    require_once (ROOT_PATH . 'includes/lib_passport.php');

    // 设置为空
    $_SESSION['mobile_register'] = array();
   
    require_once (ROOT_PATH . 'sms/aliyun/sendSms.php');

    // 生成6位短信验证码
//	$mobile_code = rand_number(6);
//	// 短信内容
//	$content = sprintf($_LANG['mobile_code_template'], $GLOBALS['_CFG']['shop_name'], $mobile_code, $GLOBALS['_CFG']['shop_name']);
//	
//	/* 发送激活验证邮件 */
//	// $result = true;
//	$result = sendSMS($mobile_phone, $content);
    // palenggege修改开始
    $mobile_code = rand_number(6);
    $mobile_code = (string) $mobile_code;
    $aa = array(
        'code' => $mobile_code 
    );

    $moban = trim($GLOBALS['db']->getOne("select value from " . $GLOBALS['ecs']->table("shop_config") . " where code='dayu_zhuce_tpl'")); //$GLOBALS['_CFG']['dayu_zhuce_tpl']

    
    $result = sendSms_aliyun($mobile_phone, $aa, $moban);


    // palenggege修改结束	
    if ($result) {

        if (!isset($count)) {
            $ext_info = array(
                "count" => 1
            );
        } else {
            $ext_info = array(
                "count" => $count
            );
        }

        // 保存手机号码到SESSION中
        $_SESSION[VT_MOBILE_REGISTER] = $mobile_phone;
        // 保存验证信息
        save_validate_record($mobile_phone, $mobile_code, VT_MOBILE_REGISTER, time(), time() + 30 * 60, $ext_info);
        echo 'ok';
    } else {
        echo '短信验证码发送失败';
    }
}

/**
 * 验证邮箱是否可以注册，true-已存在，不能注册 false-不存在可以注册
 */
function action_check_email_exist() {
    $_LANG = $GLOBALS['_LANG'];
    $_CFG = $GLOBALS['_CFG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];

    $email = empty($_POST['email']) ? '' : $_POST['email'];

    $user = $GLOBALS['user'];

    if ($user->check_email($email)) {
        echo 'true';
    } else {
        echo 'false';
    }
}

function action_check_mobile_exist() {
    $_LANG = $GLOBALS['_LANG'];
    $_CFG = $GLOBALS['_CFG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];

    $mobile = empty($_POST['mobile']) ? '' : $_POST['mobile'];

    $user = $GLOBALS['user'];

    if ($user->check_mobile_phone($mobile)) {
        echo 'true';
    } else {
        echo 'false';
    }
}

/**
 * 显示会员注册界面
 */
function action_default() {

    // 获取全局变量
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $back_act = trim($_REQUEST['back_act']);
    if ((!isset($back_act) || empty($back_act)) && isset($GLOBALS['_SERVER']['HTTP_REFERER'])) {
        $back_act = strpos($GLOBALS['_SERVER']['HTTP_REFERER'], 'user.php') ? './index.php' : $GLOBALS['_SERVER']['HTTP_REFERER'];
    }

    /* 取出注册扩展字段 */
    $sql = 'SELECT * FROM ' . $ecs->table('reg_fields') . ' WHERE type < 2 AND display = 1 ORDER BY dis_order, id';
    $extend_info_list = $db->getAll($sql);
    $smarty->assign('extend_info_list', $extend_info_list);
    $smarty->assign('re_num', getRequestStr("re_num"));
    
    /* 验证码相关设置 */
    if ((intval($_CFG['captcha']) & CAPTCHA_REGISTER) && gd_version() > 0) {
        $smarty->assign('enabled_captcha', 1);
        $smarty->assign('rand', mt_rand());
    }

    /* 密码提示问题 */
    $smarty->assign('passwd_questions', $_LANG['passwd_questions']);
    $smarty->assign('sms_register', $_CFG['sms_register']);
    $smarty->assign('sms_register', $_CFG['sms_register']);
    /* 增加是否关闭注册 */
    $smarty->assign('shop_reg_closed', $_CFG['shop_reg_closed']);

    $dayu_zhuce = $GLOBALS['db']->getOne("SELECT value FROM " . $GLOBALS['ecs']->table("shop_config") . " WHERE code='dayu_zhuce' limit 1");
    $smarty->assign('dayu_zhuce', $dayu_zhuce);

    // 登陆注册-注册类型
    $register_type = empty($_REQUEST['register_type']) ? 'mobile' : $_REQUEST['register_type'];
    if ($register_type != 'email' && $register_type != 'mobile') {
        $register_type = 'mobile';
    }
    $smarty->assign('register_type', $register_type);
    $smarty->assign('back_act', $back_act);
    $smarty->display('user_register.dwt');
}

/**
 * 注册会员的处理
 */
function action_register() {
	
	$path = substr(ROOT_PATH, 0,-7);
	 
	require ($path. 'jygj/mall/myphplib/db.php');
    // 获取全局变量
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];

   
    if(empty($pid)){
    	show_message("推荐人不能为空");
    }
    $pid = intval($_POST['pid']);
    
    $sql="select  * from  xbmall_users where user_id={$pid} ";
    
    $row = getRow($sql);
    if($row==null){
    	show_message("您填写的推荐人有误");
    }
    
    
    /* 增加是否关闭注册 */
    if ($_CFG['shop_reg_closed']) {
        $smarty->assign('action', 'register');
        $smarty->assign('shop_reg_closed', $_CFG['shop_reg_closed']);
        $smarty->display('user_passport.dwt');
        die;
    } 
    
    
    
    include_once (ROOT_PATH . 'includes/lib_passport.php');
    
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    
    $other['country'] = 1;
    //        $other['province'] = isset($_COOKIE['area_register_province']) ? $_COOKIE['area_register_province'] : "";
    //        $other['city'] = isset($_COOKIE['area_register_city']) ? $_COOKIE['area_register_city'] : "";
    //        $other['district'] = isset($_COOKIE['area_register_district']) ? $_COOKIE['area_register_district'] : "";
    /* 20170414地区信息修改开始 */
//     $other['province'] = isset($_POST['province']) ? intval($_POST['province']) : 0;
//     $other['city'] = isset($_POST['city']) ? intval($_POST['city']) : 0;
//     $other['district'] = isset($_POST['district']) ? intval($_POST['district']) : 0;
//     if ($other['province'] == 0 || $other['city'] == 0 || $other['district'] == 0) {
//         show_message("地区不合法，请选择");
//     }
    /* 20170414地区信息修改结束 */
    //$other['mobile_phone'] = isset($_POST['extend_field5']) ? $_POST['extend_field5'] : '';
    $other['pay_password'] = isset($_POST['pay_password']) ? $_POST['pay_password'] : "";
    // 注册类型：email、mobile
    $register_type = isset($_POST['register_type']) ? trim($_POST['register_type']) : '';
    
    $back_act = isset($_POST['back_act']) ? trim($_POST['back_act']) : '';
    
    
    // 注册类型不能为空
    if (empty($register_type)) {
        show_message($_LANG['passport_js']['msg_register_type_blank']);
    }
    
    // 用户名将自动生成
    if (strlen($username) < 3) {
        // show_message($_LANG['passport_js']['username_shorter']);
    }
    
    if (strlen($password) < 6) {
        show_message($_LANG['passport_js']['password_shorter']);
    }
    
    if (strpos($password, ' ') > 0) {
        show_message($_LANG['passwd_balnk']);
    }
    
    
    /* 验证码检查 */
    if ((intval($_CFG['captcha']) & CAPTCHA_REGISTER) && gd_version() > 0) {
        if (empty($_POST['captcha'])) {
            show_message($_LANG['invalid_captcha'], $_LANG['sign_up'], 'register.php', 'error');
        }
        
        /* 检查验证码 */
        include_once ('includes/cls_captcha.php');
        
        $captcha = new captcha();
        
        if (!$captcha->check_word(trim($_POST['captcha']))) {
            show_message($_LANG['invalid_captcha'], $_LANG['sign_up'], 'register.php', 'error');
        }
    }
    
    if ($register_type == "mobile") {
        
        require_once (ROOT_PATH . 'includes/lib_validate_record.php');
        
        $mobile_phone = !empty($_POST['mobile_phone']) ? trim($_POST['mobile_phone']) : '';
        $mobile_code = !empty($_POST['mobile_code']) ? trim($_POST['mobile_code']) : '';
        
        $record = get_validate_record($mobile_phone);
        
        $session_mobile_phone = $_SESSION[VT_MOBILE_REGISTER];
        
        /* 手机验证码检查 */
        $dayu_zhuce = $GLOBALS['db']->getOne("SELECT value FROM " . $GLOBALS['ecs']->table("shop_config") . " WHERE code='dayu_zhuce' limit 1");
        if ($dayu_zhuce * 1 == 1) {
            if (empty($mobile_code)) {
                show_message($_LANG['msg_mobile_phone_blank'], $_LANG['sign_up'], 'register.php', 'error');
            }
            // 检查发送短信验证码的手机号码和提交的手机号码是否匹配
            else if ($session_mobile_phone != $mobile_phone) {
                show_message($_LANG['mobile_phone_changed'], $_LANG['sign_up'], 'register.php', 'error');
            }
            // 检查验证码是否正确
            else if ($record['record_code'] != $mobile_code) {
                show_message($_LANG['invalid_mobile_phone_code'], $_LANG['sign_up'], 'register.php', 'error');
            }
            // 检查过期时间
            else if ($record['expired_time'] < time()) {
                show_message($_LANG['invalid_mobile_phone_code'], $_LANG['sign_up'], 'register.php', 'error');
            }
        }
        /* 手机注册时，用户名默认为u+手机号 */
        //             $username = generate_username_by_mobile($mobile_phone);
        $username = $mobile_phone;
        /* 手机注册 */
        $result = register_by_mobile($username, $password, $mobile_phone, $other);
        
        if ($result) {
            /* 删除注册的验证记录 */
            remove_validate_record($mobile_phone);
        }
    } else {
        /* 无效的注册类型 */
        show_message($_LANG['register_type_invalid'], $_LANG['sign_up'], 'register.php', 'error');
    }
    
    /* 随进生成用户名 */
    // $username = generate_username();
    
    if ($result) {
        
        if (isset($_COOKIE['area_register'])) {
            unset($_COOKIE['area_register']);
        }
        if (isset($_COOKIE['area_register_province'])) {
            unset($_COOKIE['area_register_province']);
        }
        if (isset($_COOKIE['area_register_city'])) {
            unset($_COOKIE['area_register_city']);
        }
        if (isset($_COOKIE['area_register_district'])) {
            unset($_COOKIE['area_register_district']);
        }
        
        /* 把新注册用户的扩展信息插入数据库 */
        $sql = 'SELECT id FROM ' . $ecs->table('reg_fields') . ' WHERE type = 0 AND display = 1 ORDER BY dis_order, id'; // 读出所有自定义扩展字段的id
        $fields_arr = $db->getAll($sql);
        
        $extend_field_str = ''; // 生成扩展字段的内容字符串
        foreach ($fields_arr as $val) {
            $extend_field_index = 'extend_field' . $val['id'];
            if (!empty($_POST[$extend_field_index])) {
                $temp_field_content = strlen($_POST[$extend_field_index]) > 100 ? mb_substr($_POST[$extend_field_index], 0, 99) : $_POST[$extend_field_index];
                $extend_field_str .= " ('" . $_SESSION['user_id'] . "', '" . $val['id'] . "', '" . compile_str($temp_field_content) . "'),";
            }
        }
        $extend_field_str = substr($extend_field_str, 0, - 1);
        
        if ($extend_field_str) { // 插入注册扩展数据
            $sql = 'INSERT INTO ' . $ecs->table('reg_extend_info') . ' (`user_id`, `reg_field_id`, `content`) VALUES' . $extend_field_str;
            $db->query($sql);
        }
        
       
        $recommend_parent_id  = $db->getOne("select user_id from  ".$ecs->table('users')." where user_name='".$recommend_user."'");
        date_default_timezone_set("Asia/Shanghai");
//         $date=date("Y-m-d H:i:s");
        $date=time();
        $sql = 'UPDATE ' . $ecs->table('users') . " SET  reg_time='".$date."', recommend_user='" . $recommend_user . "' , parent_id=".$recommend_parent_id."  WHERE `user_id`='" . $_SESSION['user_id'] . "'";
        
        $db->query($sql);
        
       
        /* 身份证号 */
        //             $sql = 'UPDATE ' . $ecs->table('users') . " SET `validated`=1,`card`='" . $card . "'  WHERE `user_id`='" . $_SESSION['user_id'] . "'";
        //             $db->query($sql);
        /* 代码增加_start By www.68ecshop.com */
        $now = gmtime();
        if ($_CFG['bonus_reg_rand']) {
            $sql_bonus_ext = " order by rand() limit 0,1";
        }
        $sql_b = "SELECT type_id FROM " . $ecs->table("bonus_type") . " WHERE send_type='" . SEND_BY_REGISTER . "'  AND send_start_date<=" . $now . " AND send_end_date>=" . $now . $sql_bonus_ext;
        $res_bonus = $db->query($sql_b);
        $kkk_bonus = 0;
        while ($row_bonus = $db->fetchRow($res_bonus)) {
            $sql = "INSERT INTO " . $ecs->table('user_bonus') . "(bonus_type_id, bonus_sn, user_id, used_time, order_id, emailed)" . " VALUES('" . $row_bonus['type_id'] . "', 0, '" . $_SESSION['user_id'] . "', 0, 0, 0)";
            $db->query($sql);
            $kkk_bonus = $kkk_bonus + 1;
        }
        if ($kkk_bonus) {
            $_LANG['register_success'] = '用户名 %s 注册成功,并获得官方赠送的红包礼品';
        }
        /* 代码增加_end By www.68ecshop.com */
        
        /* 判断是否需要自动发送注册邮件 */
        if ($GLOBALS['_CFG']['member_email_validate'] && $GLOBALS['_CFG']['send_verify_email']) {
            send_regiter_hash($_SESSION['user_id']);
        }
        
        //修改新注册的用户成为普通分销商
        $GLOBALS['db']->query("UPDATE " . $GLOBALS['ecs']->table('users') . " SET is_fenxiao = 2 WHERE user_id = '" . $_SESSION['user_id'] . "'");
        
        //绑定微信
        if (isset($_SESSION['wxid'])) {
            $sql = "UPDATE " . $GLOBALS['ecs']->table('weixin_user') .
            " SET ecuid = 0 WHERE ecuid = '" . $_SESSION['user_id'] . "'";
            $GLOBALS['db']->query($sql);
            $sql = "UPDATE " . $GLOBALS['ecs']->table('weixin_user') .
            " SET ecuid = '" . $_SESSION['user_id'] . "'" .
            " WHERE fake_id = '" . $_SESSION['wxid'] . "'";
            $num = $GLOBALS['db']->query($sql);
            if ($num > 0) {
                $sql = "SELECT parent_id FROM " .
                    $GLOBALS['ecs']->table('bind_record') .
                    " WHERE wxid = '" . $_SESSION['wxid'] . "'";
                    $parent_id = $GLOBALS['db']->getOne($sql);
                    if ($parent_id) {
                        //扫描分销商二维码，绑定上级分销商
                        //                         $GLOBALS['db']->query("UPDATE " .
                        //                                 $GLOBALS['ecs']->table('users') .
                        //                                 " SET parent_id = '$parent_id'" .
                        //                                 " WHERE user_id = '" . $_SESSION['user_id'] . "'");
                            $GLOBALS['db']->query("DELETE FROM " .
                                $GLOBALS['ecs']->table('bind_record') .
                                " WHERE wxid = '" . $_SESSION['wxid'] . "'");
                    }
                    $smarty->assign('tag', '2');
                    $smarty->assign('shop_name', $GLOBALS['_CFG']['shop_name']);
                    $smarty->display('weixin_open.dwt');
                    exit;
            }
        }
        
        $ucdata = empty($user->ucdata) ? "" : $user->ucdata;
        
        
        //             show_message(sprintf($_LANG['register_success'], $username . $ucdata), array(
        //                 $_LANG['back_up_page'], $_LANG['profile_lnk']
        //                     ), array(
            //                 $back_act, 'user.php'
            //                     ), 'info');
        show_message(sprintf($_LANG['register_success'], $username . $ucdata), array(
            "请登录"
        ), array(
            'user.php?act=login'
        ), 'info');
        
    } else {
        $GLOBALS['err']->show($_LANG['sign_up'], 'register.php');
    }
    
    /* 代码增加2014-12-23 by www.68ecshop.com _star */
}

function action_check_card() {
    $_LANG = $GLOBALS['_LANG'];
    $_CFG = $GLOBALS['_CFG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];

    $card = empty($_POST['card']) ? '' : $_POST['card'];

    if (!empty($card)) {
        $sql = "SELECT count(*) FROM " . $GLOBALS['ecs']->table("users") . " where card='" . $card . "'";
        $rows = $GLOBALS['db']->getOne($sql);
        if ($rows > 0) {
            echo "false";
        } else {
            echo "true";
        }
    } else {
        echo "false";
    }
}

/**
 * 随机生成指定长度的数字
 *
 * @param number $length        	
 * @return number
 */
function rand_number($length = 6) {
    if ($length < 1) {
        $length = 6;
    }

    $min = 1;
    for ($i = 0; $i < $length - 1; $i ++) {
        $min = $min * 10;
    }
    $max = $min * 10 - 1;

    return rand($min, $max);
}

/**
 * 根据手机号生成用户名
 *
 * @param number $length
 * @return number
 */
function generate_username_by_mobile($mobile) {

    $username = 'u' . substr($mobile, 0, 3);

    $charts = "ABCDEFGHJKLMNPQRSTUVWXYZ";
    $max = strlen($charts);

    for ($i = 0; $i < 4; $i ++) {
        $username .= $charts[mt_rand(0, $max)];
    }

    $username .= substr($mobile, -4);

    $sql = "select count(*) from " . $GLOBALS['ecs']->table('users') . " where user_name = '$username'";
    $count = $GLOBALS['db']->getOne($sql);
    if ($count > 0) {
        return generate_username_by_mobile();
    }

    return $username;
}

/**
 * 根据邮箱地址生成用户名
 *
 * @param number $length
 * @return number
 */
function generate_username() {

    $username = 'u' . rand_number(3);

    $charts = "ABCDEFGHJKLMNPQRSTUVWXYZ";
    $max = strlen($charts);

    for ($i = 0; $i < 4; $i ++) {
        $username .= $charts[mt_rand(0, $max)];
    }

    $username .= rand_number(4);

    $sql = "select count(*) from " . $GLOBALS['ecs']->table('users') . " where user_name = '$username'";
    $count = $GLOBALS['db']->getOne($sql);
    if ($count > 0) {
        return generate_username();
    }

    return $username;
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