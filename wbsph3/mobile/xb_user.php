<?php
/**
 *   会员中心
 */
define ( 'IN_ECS', true );
require_once (dirname(__FILE__) . '/xb_header.php');
if (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'MicroMessenger' )!=-1){
    include 'weixin_login.php';
}



call_user_func($function_name);

/**
 * 我的钱包
 */
function action_my()
{
    global $smarty, $user_id;
    $user = get_user_default($user_id);
    $smarty->assign("shop_name", "我的钱包");
    $smarty->assign("user", $user);
    $smarty->display("xb_my.dwt");
}


function action_default()
{
    global $smarty, $user_id;
    $user = get_user_default($user_id);
    $smarty->assign("shop_name", "我的钱包");
    $smarty->assign("user", $user);
    $smarty->display("xb_my.dwt");
}

/**
 * 设置tap账号
 */
function action_to_set_tap()
{
    global $smarty, $user_id;
    $user = get_user_default($user_id);
    $smarty->assign("shop_name", "我的tap账号");
    $smarty->assign("user", $user);
    $smarty->display("xb_my_tap.dwt");
}

/**
 * 设置tap账号
 */
function action_set_tap()
{
    global $smarty, $user_id;
    $user = get_user_default($user_id);
    $tap_account = getRequestStr("tap_account");
    $sql = "update xbmall_users  set tap_account='{$tap_account}', bank_num='{$tap_account}' where user_name='{$user['user_name']}'";
    db_query($sql);
    $smarty->assign("shop_name", "我的tap账号");
    $smarty->assign("user", $user);
    show_message('恭喜，设置成功==！', '返回', 'xb_user.php?act=to_set_tap');
}

function action_syjl()
{
    global $smarty, $user_id;
    $result = data_list_syjl();
    $smarty->assign("data_list", $result['list']);
    $smarty->display("xb_syjl_list.dwt");
}

/**
 * 充值记录
 */
function action_chong_zhi_ji_lu()
{
    global $smarty, $user_id;
    $user = get_user_default($user_id);
    
    $result = data_list_chong_zhi($user['user_name']);
    $smarty->assign("data_list", $result['list']);
    $smarty->display("xb_chong_zhi_list.dwt");
}

/**
 * 收益记录的查询
 *
 * @return
 */
function data_list_syjl()
{
    $result = get_filter();
    if ($result === false) {
        
        /* 过滤条件 */
        
        $where = " 1=1 ";
        
        $fromSql = db_table('xb_syjl') . " WHERE  $where";
        setPageInfo($filter, $fromSql);
        
        $sql = getDataSql($filter, $fromSql, "*", 'id');
        set_filter($filter, $sql);
        $res = db_query($sql);
        $list = array();
        
        while ($row = db_fetchRow($res)) {
            $row['rq'] = local_date('Y-m-d H:i', $row['rq']);
            $list[] = $row;
        }
        
        $arr = array(
            'list' => $list,
            'filter' => $filter,
            'page_count' => $filter['page_count'],
            'record_count' => $filter['record_count']
        );
        
        return $arr;
    }
}


function  action_chong_zhi(){
    global $smarty, $user_id;
    $user = get_user_default($user_id);
    $czje=getRequestInt("czje");
    $tjrq = time();//提交时间
     
    if($czje==-1 || $czje==0  ){
        show_message("请填写充值金额");
    }
    
    if($user['tap_account']==null || $user['tap_account']=='' ){
        show_message("请先绑定tap账号",'绑定tap', "/mobile/xb_user.php?act=to_set_tap");
    }
    
    
    $filePath= saveFile("ping_zheng");
   
    $sql="insert into xbmall_xb_cz (czyhkh, user,czje,tjrq,ping_zheng)values('{$user['tap_account']}','{$user['user_name']}',{$czje},{$tjrq},'{$filePath}')";
    db_query($sql);
    show_message("充值提交成功,等待审核",'返回', "/mobile/xb_user.php?act=my");
}

/**
 * 今天最多可以投多少笔
 * @param unknown $fenShu
 */
function  saveFile($file_name){
    
    $path = "../data/xxtzuploads/";
    $extArr = array("jpg", "png", "gif");
    
    if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST"){
        $name = $_FILES[$file_name]['name'];
        $size = $_FILES[$file_name]['size'];
        if(empty($name)){
            show_message("请选择要上传的图片",'返回', "xb_user.php?act=to_chong_zhi");
        }
        
        $extend = pathinfo($name);
        $ext = strtolower($extend["extension"]);
        
        if(!in_array($ext,$extArr)){
            show_message("图片格式错误",'返回', "xb_user.php?act=to_chong_zhi");
        }
        if($size>(100*102400)){
            show_message("图片大小不能超过10M",'返回', "xb_user.php?act=to_chong_zhi");
        }
        $image_name = time().rand(100,999).".".$ext;
        $tmp = $_FILES[$file_name]['tmp_name'];
        $filePath=$path.$image_name;
        if(move_uploaded_file($tmp, $filePath)){
            return $image_name;
        }else{
            die( '上传出错了！');
        }
    }
    
    return  $filePath ;
}


/**
 * 充值记录
 *
 * @return unknown[]|string[][]
 */
function data_list_chong_zhi($user)
{
    $filter = array();
    
    $where = " where user='{$user}' ";
    $sql = "select id, czyhkh ,skyhkh,user,czje,  date_format(FROM_UNIXTIME(tjrq),'%Y-%m-%d %H:%i') as tjrq ,
date_format(FROM_UNIXTIME(czrq),'%Y-%m-%d %H:%i') as czrq , cw_sh_state, js_sh_state,
 date_format(FROM_UNIXTIME(cwshrq),'%Y-%m-%d %H:%i') as cwshrq,date_format(FROM_UNIXTIME(jsshrq),'%Y-%m-%d %H:%i') as jsshrq,cw_bz
,js_bz ,user_bz,ping_zheng,fs_state from  zt_xxtz_cz {$where}  ";
    
    /* 过滤条件 */
    
    $where = " user='$user'  ";
    
    $fromSql = db_table('xb_cz') . "  WHERE  user='$user'";
    setPageInfo($filter, $fromSql);
    
    $sql = getDataSql($filter, $fromSql, " id, czyhkh ,skyhkh,user,czje,  date_format(FROM_UNIXTIME(tjrq),'%Y-%m-%d %H:%i') as tjrq ,
date_format(FROM_UNIXTIME(czrq),'%Y-%m-%d %H:%i') as czrq , cw_sh_state, js_sh_state,
 date_format(FROM_UNIXTIME(cwshrq),'%Y-%m-%d %H:%i') as cwshrq,date_format(FROM_UNIXTIME(jsshrq),'%Y-%m-%d %H:%i') as jsshrq,cw_bz
,js_bz ,user_bz,ping_zheng,fs_state ", 'id');
    set_filter($filter, $sql);
    $res = db_query($sql);
    $list = array();
    
    while ($row = db_fetchRow($res)) {
        if ($row["cw_sh_state"] == 0)
            $row["cw_sh_state"] = "未审核";
        else if ($row["cw_sh_state"] == 1)
            $row["cw_sh_state"] = "审核通过";
        else
            $row["cw_sh_state"] = "审核不通过";
        $list[] = $row;
    }
    
    $arr = array(
        'list' => $list,
        'filter' => $filter,
        'page_count' => $filter['page_count'],
        'record_count' => $filter['record_count']
    );
    
    return $arr;
}

/**
 * 我的邀请
 */
function action_my_invest()
{
    global $smarty, $user_id;
    $smarty->assign("shop_name", "我的邀请");
    $user = get_user_default($user_id);
    $smarty->assign("user", $user);
    
    require_once "wxjs/jssdk.php";
    $ret = db_getRow("SELECT  *  FROM " .db_table('weixin_config'). " ");
    $jssdk = new JSSDK($appid=$ret['appid'], $ret['appsecret']);
    $signPackage = $jssdk->GetSignPackage();
  
    $smarty->assign('signPackage',  $signPackage);	
    
    
    $smarty->display("xb_invite.dwt");
}

/**
 * 我的余额
 */
function action_my_balance()
{
    global $smarty, $user_id;
    $user = get_user_default($user_id);
    $smarty->assign("user", $user);
    $smarty->assign("shop_name", "邀请奖励余额");
    $smarty->display("xb_my_balance.dwt");
}

function action_to_invite()
{
    global $smarty, $user_id;
    $user = get_user_default($user_id);
    $smarty->assign("user", $user);
    $smarty->assign("shop_name", "邀请好友注册");
    
    
    $smarty->display("xb_invite_link.dwt");
}

/**
 * 到充值页面
 */
function action_to_chong_zhi()
{
    global $smarty, $user_id;
    $user = get_user_default($user_id);
    $smarty->assign("user", $user);
    $smarty->assign("shop_name", "充值");
    $smarty->display("xb_my_chong_zhi.dwt");
}
/**
 * 安全中心
 */
function action_my_safe()
{
    global $smarty, $user_id;
    $smarty->assign("shop_name", "安全中心");
    $smarty->display("xb_safe_center.dwt");
}
// function action_my(){
// display("my.dwt");
// }

?>
