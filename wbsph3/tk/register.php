<?php

/**
 *   注册
 */
define('IN_ECS', true);

require (dirname(__FILE__) . '/includes/init_without_login.php');
/* 载入语言文件 */
require_once (ROOT_PATH . 'languages/' . $_CFG['lang'] . '/user.php');

$action = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : 'default';

$affiliate = unserialize($GLOBALS['_CFG']['affiliate']);
$smarty->assign('affiliate', $affiliate);
$back_act = '';


/* 如果是显示页面，对页面进行相应赋值 */
if (true) {
    $smarty->assign('shop_country', $_CFG['shop_country']);
    $smarty->assign('shop_province', get_regions(1, $_CFG['shop_country']));
    $smarty->assign('province_list', get_regions(1, $_CFG['shop_country']));

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

        //清空验证码
        unset($_SESSION[$captcha->session_word]);
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

        //清空验证码
        unset($_SESSION[$captcha->session_word]);
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

    require_once (ROOT_PATH . 'sms/sms.php');


    $mobile_code = rand_number(6);
    $mobile_code = (string) $mobile_code;
    $aa = array(
        'code' => $mobile_code,
        'product' => $GLOBALS['_CFG']['shop_name']
    );

    $moban = trim($GLOBALS['db']->getOne("select value from " . $GLOBALS['ecs']->table("shop_config") . " where code='dayu_zhuce_tpl'")); //$GLOBALS['_CFG']['dayu_zhuce_tpl']

    $result = sendSMS($mobile_phone, $aa, $moban);
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

    if ( check_mobile_phone($mobile)) {
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

    /* 验证码相关设置 */
    if ((intval($_CFG['captcha']) & CAPTCHA_REGISTER) && gd_version() > 0) {
        $smarty->assign('enabled_captcha', 1);
        $smarty->assign('rand', mt_rand());
    }

    /* 密码提示问题 */
    $smarty->assign('passwd_questions', $_LANG['passwd_questions']);
    /* 代码增加_start By www.68ecshop.com */
    $smarty->assign('sms_register', $_CFG['sms_register']);
    /* 代码增加_end By www.68ecshop.com */
    /* 代码增加_star By www.68ecshop.com */
    $smarty->assign('sms_register', $_CFG['sms_register']);
    /* 代码增加_end By www.68ecshop.com */
    /* 增加是否关闭注册 */
    $smarty->assign('shop_reg_closed', $_CFG['shop_reg_closed']);
    // 登陆注册-注册类型
    $register_type = empty($_REQUEST['register_type']) ? 'mobile' : $_REQUEST['register_type'];
    if ($register_type != 'email' && $register_type != 'mobile') {
        $register_type = 'mobile';
    }
    $dayu_zhuce = $GLOBALS['db']->getOne("SELECT value FROM " . $GLOBALS['ecs']->table("shop_config") . " WHERE code='dayu_zhuce' limit 1");
    $smarty->assign('dayu_zhuce', $dayu_zhuce);

    $smarty->assign('register_type', $register_type);
    $smarty->assign('back_act', $back_act);
    $smarty->display('user_register.dwt');
}

function action_xieyi() {
    // 获取全局变量
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $result = array("code" => 2, "message" => "");
    if (($result["code"] * 1 == 2)) {
        require_once (ROOT_PATH . 'includes/lib_validate_record.php');
        $mobile_phone = !empty($_POST['mobile_phone']) ? trim($_POST['mobile_phone']) : '';
        $mobile_code = !empty($_POST['mobile_code']) ? trim($_POST['mobile_code']) : '';
        $record = get_validate_record($mobile_phone);
        $session_mobile_phone = $_SESSION[VT_MOBILE_REGISTER];
        /* 手机验证码检查 */
        if (empty($mobile_code)) {
            $result["message"] = $_LANG['msg_mobile_phone_blank'];
        }
        // 检查发送短信验证码的手机号码和提交的手机号码是否匹配
        else if ($session_mobile_phone != $mobile_phone) {
            //show_message($_LANG['mobile_phone_changed'], $_LANG['sign_up'], 'register.php', 'error');
        }
        // 检查验证码是否正确
        else if ($record['record_code'] != $mobile_code) {
            $result["message"] = $_LANG['invalid_mobile_phone_code'];
        }
        // 检查过期时间
        else if ($record['expired_time'] < time()) {
            $result["message"] = $_LANG['invalid_mobile_phone_code'];
        }
    }
    if (empty($result["message"])) {
        $province = isset($_POST['province']) ? intval($_POST['province']) : 0;
        $city = isset($_POST['city']) ? intval($_POST['city']) : 0;
        $district = isset($_POST['district']) ? intval($_POST['district']) : 0;
        if ($province == 0 || $city == 0 || $district == 0) {
            $result["message"] = "地区不合法，请选择地区";
        } else {
            $result["code"] = 0;
            $article_id = $db->getOne("SELECT article_id FROM " . $ecs->table('article') . " WHERE cat_id = '-2' ");
            /* 获得文章的信息 */
            $sql = "SELECT a.* " .
                    "FROM " . $GLOBALS['ecs']->table('article') . " AS a " .
                    "WHERE a.is_open = 1 AND a.article_id = '$article_id' GROUP BY a.article_id";
            $row = $GLOBALS['db']->getRow($sql);
            $result["message"] = $row["content"];
        }
    }
    exit(json_encode($result));
}

/**
 * 注册前先检查一下验证码和短信验证码
 */
function action_beforeSubmit() {
    
}

/**
 * 注册会员的处理
 */
function action_register() {
 
    // 获取全局变量
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];

    /* 增加是否关闭注册 */
    if ($_CFG['shop_reg_closed']) {
        $smarty->assign('action', 'register');
        $smarty->assign('shop_reg_closed', $_CFG['shop_reg_closed']);
        $smarty->display('user_passport.dwt');
        return  ;
    }
    
  
    include_once (ROOT_PATH . 'includes/lib_passport.php');
    
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $other['msn'] = isset($_POST['extend_field1']) ? $_POST['extend_field1'] : '';
    $other['qq'] = isset($_POST['extend_field2']) ? $_POST['extend_field2'] : '';
    $other['office_phone'] = isset($_POST['extend_field3']) ? $_POST['extend_field3'] : '';
    $other['home_phone'] = isset($_POST['extend_field4']) ? $_POST['extend_field4'] : '';
    $sel_question = empty($_POST['sel_question']) ? '' : compile_str($_POST['sel_question']);
    $passwd_answer = isset($_POST['passwd_answer']) ? compile_str(trim($_POST['passwd_answer'])) : '';
    
    $other['country'] = 0;
    $other['province'] = isset($_POST['province']) ? intval($_POST['province']) : 0;
    $other['city'] = isset($_POST['city']) ? intval($_POST['city']) : 0;
    $other['district'] = isset($_POST['district']) ? intval($_POST['district']) : 0;
    if ($other['province'] == 0 || $other['city'] == 0 || $other['district'] == 0) {
    	sys_msg("地区不合法，请选择");
    }
  
    // 注册类型：email、mobile
    $register_type = isset($_POST['register_type']) ? trim($_POST['register_type']) : '';
    $back_act = isset($_POST['back_act']) ? trim($_POST['back_act']) : '';
    
    if (empty($_POST['agreement'])) {
    	sys_msg($_LANG['passport_js']['agreement'],1);
    }
    
    // 注册类型不能为空
    if (empty($register_type)) {
    	sys_msg($_LANG['passport_js']['msg_register_type_blank'],1);
    }
    
    if (strlen($password) < 6) {
    	sys_msg($_LANG['passport_js']['password_shorter'],1);
    }
    
    if (strpos($password, ' ') > 0) {
    	sys_msg($_LANG['passwd_balnk'],1);
    }
   

    $card = empty($_POST['card']) ? '' : $_POST['card'];
    
  
     if ($register_type == "mobile") {
    	
    	require_once (ROOT_PATH . 'includes/lib_validate_record.php');
    	$_COOKIE['ecshop_affiliate_uid']=$_POST['tui_jian_mobile_phone'];
    	$mobile_phone = !empty($_POST['mobile_phone']) ? trim($_POST['mobile_phone']) : '';
    	$mobile_code = !empty($_POST['mobile_code']) ? trim($_POST['mobile_code']) : '';
    	
    	$record = get_validate_record($mobile_phone);
    	
    	$session_mobile_phone = $_SESSION[VT_MOBILE_REGISTER];
    	
    	/* 手机验证码检查 */
    	$dayu_zhuce = $GLOBALS['db']->getOne("SELECT value FROM " . $GLOBALS['ecs']->table("shop_config") . " WHERE code='dayu_zhuce' limit 1");
    	if ($dayu_zhuce * 1 == 1) {
    		if (empty($mobile_code)) {
    			$links[0]['text']="到注册页面";
    			$links[0]['href'] = 'register.php';
    			sys_msg($_LANG['msg_mobile_phone_blank'], 1, $links);
    		}
    		// 检查发送短信验证码的手机号码和提交的手机号码是否匹配
    		else if ($session_mobile_phone != $mobile_phone) {
    			//show_message($_LANG['mobile_phone_changed'], $_LANG['sign_up'], 'register.php', 'error');
    		}
    		// 检查验证码是否正确
    		else if ($record['record_code'] != $mobile_code) {
    			$links[0]['text']="到注册页面";
    			$links[0]['href'] = 'register.php';
    			sys_msg($_LANG['invalid_mobile_phone_code'], 1, $links);
    		}
    		// 检查过期时间
    		else if ($record['expired_time'] < time()) {
    			$links[0]['text']="到注册页面";
    			$links[0]['href'] = 'register.php';
    			sys_msg($_LANG['invalid_mobile_phone_code'], 1, $links);
    		}
    	}
    
    	/* 手机注册时，用户名默认为u+手机号 */
    	$username = generate_username_by_mobile($mobile_phone);
    	$reg_date = time();
    	$ip = real_ip();
    	$ec_salt = rand(1, 9999);
    	$password =  md5( $password );
    	$password = compile_password(array(
    			'md5password' => $password, 'ec_salt' => $ec_salt));
    	
    	$tui_jian_mobile_phone= !empty($_POST['tui_jian_mobile_phone']) ? trim($_POST['tui_jian_mobile_phone']) : '';
    	$sql="select  user_id from  ".$GLOBALS['ecs']->table("users")." where mobile_phone=".$tui_jian_mobile_phone;
    	$parent_id = $db->getOne($sql);//根据推荐人的手机号查询推荐人的前台id
    	
    	/* 手机注册 */
    	$sql='INSERT INTO ' . $GLOBALS['ecs']->table("users") . "(parent_id,ec_salt,  `mobile_phone`, `user_name`, `password`, `reg_time`, `last_login`, `last_ip`)
    	VALUES ('$parent_id', '$ec_salt', '$mobile_phone', '$username', '$password', '$reg_date', '$reg_date', '$ip')";
    	$result = $db->query($sql);
    	
    	$insertSql = "INSERT INTO " . $GLOBALS['ecs']->table("supplier_admin_user")
    	. "(uid,user_name, password,ec_salt,add_time,mobile_phone,role,checked)" .
    	" VALUES('" . $db->insert_id(). "','" . $username. "', '" . $password. "','" .
    	$ec_salt. "','" . $reg_date. "','" . $mobile_phone. "',7,1)";//甜空用户(待审核), 审核周角色是7
    	//     		file_put_contents("c:/test.txt",  $insertSql." fffffffffffffffff7\r\n", FILE_APPEND);
    	$db->query($insertSql);
    	
    	
    	if ($result) {
    		/* 删除注册的验证记录 */
    		remove_validate_record($mobile_phone);
    	}
    } else {
    	/* 无效的注册类型 */
    	$links[0]['text']="到注册页面";
    	$links[0]['href'] = 'register.php';
    	sys_msg($_LANG['register_type_invalid'],1,$links );
    }
    
 
 
    if ($result) {
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
    	 
    	 //             $sql = 'UPDATE ' . $ecs->table('users') . " SET `validated`=1,`card`='" . $card . "'  WHERE `user_id`='" . $_SESSION['user_id'] . "'";
    	 //             $db->query($sql);
    	 setcookie("ecshop_affiliate_uid", "", time() - 3600);
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
    	 	$_LANG['register_success'] = '用户名 %s 注册成功';
    	 } 
    	 
    	 
    	 
    	
    	 
    	 
    	 
    	 $links[0]['text']="到登录页面";
    	 $links[0]['href'] = 'privilege.php?act=login';
    	 sys_msg("用户名".$mobile_phone."注册成功", 0, $links);
     
    } else {
    	$GLOBALS['err']->show($_LANG['sign_up'], 'register.php');
    }
    
    
     
}

/**
 * 检查身份证是否存在
 */
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

//	$username = 'u'.substr($mobile, 0, 3);
//
//	$charts = "ABCDEFGHJKLMNPQRSTUVWXYZ";
//	$max = strlen($charts);
//
//	for($i = 0; $i < 4; $i ++)
//	{
//		$username .= $charts[mt_rand(0, $max)];
//	}
//
//	$username .= substr($mobile, -4);
//	
//	$sql = "select count(*) from " . $GLOBALS['ecs']->table('users') . " where user_name = '$username'";
//	$count = $GLOBALS['db']->getOne($sql);
//	if($count > 0)
//	{
//		return generate_username_by_mobile();
//	}

    return $mobile;
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


function compile_password ($cfg)
{
	if(isset($cfg['password']))
	{
		$cfg['md5password'] = md5($cfg['password']);
	}
	if(empty($cfg['type']))
	{
		$cfg['type'] = PWD_MD5;
	}
	
	switch($cfg['type'])
	{
		case PWD_MD5:
			if(! empty($cfg['ec_salt']))
			{
				return md5($cfg['md5password'] . $cfg['ec_salt']);
			}
			else
			{
				return $cfg['md5password'];
			}
			
		case PWD_PRE_SALT:
			if(empty($cfg['salt']))
			{
				$cfg['salt'] = '';
			}
			
			return md5($cfg['salt'] . $cfg['md5password']);
			
		case PWD_SUF_SALT:
			if(empty($cfg['salt']))
			{
				$cfg['salt'] = '';
			}
			
			return md5($cfg['md5password'] . $cfg['salt']);
			
		default:
			return '';
	}
}


function check_mobile_phone ($mobile_phone)
{
	$db = $GLOBALS['db'];
	if(! empty($mobile_phone))
	{
		/* 检查email是否重复 */
		$sql = "SELECT mobile_phone  FROM " . $GLOBALS['ecs']->table('users'). " WHERE  mobile_phone =".$mobile_phone;
		if($db->getOne($sql, true) > 0)
		{
			return true;
		}
		return false;
	}
}
?>