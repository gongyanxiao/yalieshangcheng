<?php
require_once (dirname(__FILE__) . '/includes/init.php');
require_once (dirname(__FILE__) . '/weixin/wechat.class.php');

$weixinconfig = $GLOBALS['db']->getRow("SELECT * FROM " . $GLOBALS['ecs']->table('weixin_config') . " WHERE `id` = 1");

$weixin = new core_lib_wechat($weixinconfig);

if ($_GET['code']) {
    $json = $weixin->getOauthAccessToken();
    
    // if($json==false){//测试
    // $json['openid'] = 'oJl2j0gu8f9HNtEQZ2nOfagjAKAQ';
    // $json['access_token'] ='12_is1fE21bA1z8fbEyYM0JDjDKzezewX1WkiAvF';
    // }
    
    if ($json['openid']) {
        $info = getOauthUserinfo($weixin, $json);
        $user_info = $GLOBALS['db']->getRow("SELECT * FROM " . $GLOBALS['ecs']->table('weixin_user') . " WHERE fake_id='{$json['openid']}'");
        if ($user_info == null) {
            $pid = intval($_GET['pid']);
            if (! isset($pid)) {
                show_message('链接没有推荐人信息.请让你的推荐人给你发邀请链接');
            }
            $info['pid'] = $pid;
            $user_info = createUser($json, $info);
        }
        // $info['name'] = $user_info['user_name'];
        setUserInfo($user_info, $json, $info);
    }
    $url = $GLOBALS['ecs']->url() . "xb_user.php";
    if (isset($pid)) {
        $url = $url . "?pid=" . $pid; // 加上推荐人;
    }
    header("Location:$url");
    exit();
}

if (isset($_GET['wxid'])) {
    $_SESSION['wxid'] = $_GET['wxid'];
}

if (! isset($_SESSION['wxid'])) { // 不包含微信信息,就到微信进行认证
    
    $pid = intval($_GET['pid']);
    $url = $GLOBALS['ecs']->url() . "xb_user.php?pid=" . $pid; // 加上推荐人
    $url = $weixin->getOauthRedirect($url, 1, 'snsapi_userinfo');
    // $url = $weixin->getOauthRedirect($url,1,'snsapi_base');
    header("Location:$url");
    exit();
}

function testMessage($mesage)
{
    // var_dump("<br>");
    // var_dump($mesage);
}

/**
 * 从微信服务器获取用户信息
 *
 * @param unknown $weixin
 * @param unknown $json
 * @return string|mixed
 */
function getOauthUserinfo($weixin, $json)
{
    $info = $weixin->getOauthUserinfo($json['access_token'], $json['openid']);
    if ($info['nickname']) {
        $info['name'] = str_replace("'", "", $info['nickname']);
        if ($GLOBALS['user']->check_user($info['name'])) { // 重名处理
            $info['name'] = $info['name'] . '_' . 'weixin' . (rand(10000, 99999));
        }
    } else {
        $info['name'] = 'weixin_' . rand(10000, 99999);
    }
    
    return $info;
}

/**
 * 主要是设置session信息
 *
 * @param unknown $user_info
 * @param unknown $json
 * @param unknown $info
 */
function setUserInfo($user_info, $json, $info)
{
    session_start();
    $_SESSION['wxid'] = $json['openid'];
    $_SESSION['user_id'] = $user_info['ecuid'];
    $_SESSION['user_name'] = $user_info['ecuid'];
    $GLOBALS['user']->set_session($info['name']);
    $GLOBALS['user']->set_cookie($info['name']);
    update_user_info(); // 更新用户信息
    recalculate_price(); // 重新计算购物车中的商品价格
    
    $info_user_id = 'weixin' . '_' . $info['openid'];
    if ($user_info['aite_id'] == $info['openid']) {
        $sql = 'UPDATE ' . $GLOBALS['ecs']->table('users') . " SET aite_id = '$info_user_id' WHERE aite_id = '$user_info[aite_id]'";
        $GLOBALS['db']->query($sql);
    }
    testMessage($_SESSION);
}

// 根据微信登录,创建用户
function createUser($json, $info)
{
    $createtime = gmtime();
    $id = createSysUser($json, $info, $createtime);
    
    createWeiXinUser($id, $createtime, $json['openid'], $json[access_token]);
    
    $user_info = $GLOBALS['db']->getRow("SELECT * FROM " . $GLOBALS['ecs']->table('weixin_user') . " WHERE fake_id='{$json['openid']}'");
    
    return $user_info;
}

/**
 * 创建系统用户
 * 
 * @param unknown $json
 * @param unknown $info
 * @param unknown $createtime
 * @return unknown
 */
function createSysUser($json, $info, $createtime)
{
    $pid = $info['pid'];
    $info_user_id = 'weixin' . '_' . $info['openid'];
    $user_pass = $GLOBALS['user']->compile_password(array(
        'password' => $info['openid']
    ));
    
    if ($pid != null) {
        $parentUser = getUserInfo($pid);
        if ($parentUser['parent_id'] != null) {
            $er_ceng_user = getUserInfo($parentUser['parent_id']);
            $er_ceng_user_id = $er_ceng_user['user_id'];
        }
    }
    
    $dai_li_id = findDaiLi($pid);//找到代理
    
    $sql = 'INSERT INTO ' . $GLOBALS['ecs']->table('users') . '(qu_yu_user,er_ceng_user,parent_id,user_name , password, aite_id , sex , reg_time , is_validated,froms,headimg) VALUES ' . "({$dai_li_id},{$er_ceng_user_id},{$pid},'$info[name]' , '$user_pass' ,
      '$info_user_id' , '$info[sex]' , '" . $createtime . "' , '1','mobile','$info[headimgurl]')";
    testMessage($sql);
    
    $GLOBALS['db']->query($sql);
    $id = $GLOBALS['db']->insert_id();
    return $id;
}

/**
 * 创建微信用户
 * 
 * @param unknown $user_id
 * @param unknown $createtime
 * @param unknown $openid
 * @param unknown $access_token
 */
function createWeiXinUser($user_id, $createtime, $openid, $access_token)
{
    $createymd = date('Y-m-d', $createtime);
    $sql = "INSERT INTO " . $GLOBALS['ecs']->table('weixin_user') . " (`ecuid`,`fake_id`,`createtime`,`createymd`,`isfollow` , access_token)
				value ({$user_id},'" . $openid . "','{$createtime}','{$createymd}',0,'{$access_token}')";
    testMessage($sql);
    
    $GLOBALS['db']->query($sql);
}

/**
 * 根据id获取用户信息
 * 
 * @param unknown $user_id
 * @return unknown|NULL|boolean
 */
function getUserInfo($user_id)
{
    $sql = "select * from xbmall_users where user_id={$user_id}";
    $userInfo = db_getRow($sql);
    return $userInfo;
}

function findDaiLi($pid)
{
    $userInfo = getUserInfo($pid);
    if ($userInfo == null)
        return null;
    
    if ($userInfo['level'] == 1) { // 找到了代理
        return $userInfo['user_id'];
    }
    
    return findDaiLi($userInfo['user_id']); // 继续向上找代理
}
?>