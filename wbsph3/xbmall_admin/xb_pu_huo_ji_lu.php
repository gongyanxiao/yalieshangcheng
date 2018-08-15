<?php

/**
 * 铺货记录
 */
require_once(dirname ( __FILE__ ).'/xb_header.php');
$exc = new exchange($ecs->table('xb_xxtz'), $db, 'act_id', 'act_name');

 

call_user_func($function_name);

// 获取php文件名,不带后缀
function getPhpName()
{
    $pathinfo = pathinfo(__FILE__);
    $phpFileName = $pathinfo['filename'];
    return $phpFileName;
}

function action_list()
{
    /* 检查权限 */
    admin_priv('auction');
    
    /* 模板赋值 */
    assign('full_page', 1);
    assign('ur_here', "铺货记录");
    // assign('action_link', array('href' => getPhpName().'.php?act=add', 'text' => "添加铺货"));
    $list = data_list();
    
   
    
    /*
     * 城市列表
     */
    $sql = 'SELECT region_id, region_name FROM ' . db_table('region') . " WHERE parent_id = 1 ";
    $daqu_name = db_getAll($sql);
    assign('province_list', $daqu_name);
    
    assign('file_name', PHP_SELF);
    assign('data_list', $list['list']);
    assign('filter', $list['filter']);
    assign('record_count', $list['record_count']);
    assign('page_count', $list['page_count']);
    
    $sort_flag = sort_flag($list['filter']);
    assign($sort_flag['tag'], $sort_flag['img']);
    
    /* 显示商品列表页面 */
    assign_query_info();
    display(getPhpName() . '_list.htm');
}

/**
 * 分页、排序、查询
 */
function action_query()
{
    global $smarty;
    $list = data_list();
    
    assign('data_list', $list['item']);
    assign('filter', $list['filter']);
    assign('record_count', $list['record_count']);
    assign('page_count', $list['page_count']);
    
    $sort_flag = sort_flag($list['filter']);
    assign($sort_flag['tag'], $sort_flag['img']);
    
    make_json_result($smarty->fetch(getPhpName() . '_list.htm'), '', array(
        'filter' => $list['filter'],
        'page_count' => $list['page_count']
    ));
}

/**
 * 删除
 */
function action_remove()
{
    global $exc;
    
    check_authz_json('auction');
    
    $id = intval($_GET['id']);
    $auction = get_xb_zrzcjl($id);
    if (empty($auction)) {
        make_json_error("不存在");
    }
    if ($auction['bid_user_count'] > 0) {
        make_json_error("不能删除");
    }
    $name = $auction['act_name'];
    $exc->drop($id);
    
    /* 记日志 */
    admin_log($name, 'remove', 'auction');
    
    /* 清除缓存 */
    clear_cache_files();
    
    $url = getPhpName() . '.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
    
    ecs_header("Location: $url\n");
    exit();
}

/* ------------------------------------------------------ */
// -- 批量操作
/* ------------------------------------------------------ */
function action_batch()
{
    global $smarty, $exc, $ecs;
    
    /* 取得要操作的记录编号 */
    if (empty($_POST['checkboxes'])) {
        sys_msg($_LANG['no_record_selected']);
    } else {
        /* 检查权限 */
        admin_priv('auction');
        $ids = $_POST['checkboxes'];
        
        if (isset($_POST['drop'])) {
            /* 查询哪些拍卖活动已经有人出价 */
            $sql = "SELECT DISTINCT act_id FROM " . $ecs->table('auction_log') . " WHERE act_id " . db_create_in($ids);
            $ids = array_diff($ids, $db->getCol($sql));
            if (! empty($ids)) {
                /* 删除记录 */
                $sql = "DELETE FROM " . $ecs->table('xb_zrzcjl') . " WHERE act_id " . db_create_in($ids) . " AND act_type = '" . GAT_CHOU_JIANG . "'";
                $db->query($sql);
                
                /* 记日志 */
                admin_log('', 'batch_remove', 'auction');
                
                /* 清除缓存 */
                clear_cache_files();
            }
            $links[] = array(
                'text' => "返回铺货列表",
                'href' => getPhpName() . '.php?act=list&' . list_link_postfix()
            );
            sys_msg($_LANG['batch_drop_ok'], 0, $links);
        }
    }
}

function action_add()
{
    global $smarty, $exc, $ecs;
    
    /* 检查权限 */
    admin_priv('auction');
    /* 是否添加 */
    $is_add = $_REQUEST['act'] == 'add';
    assign('form_action', 'insert');
    $auction = array(
        'act_id' => 0,
        'act_name' => '',
        'act_desc' => '',
        'goods_id' => 0,
        'product_id' => 0,
        'goods_name' => $_LANG['pls_search_goods'],
        'start_time' => date('Y-m-d', time() + 86400),
        'end_time' => date('Y-m-d', time() + 4 * 86400),
        'num' => 1,
        'price' => 100,
        'status' => 0
    );
    
    assign('auction', $auction);
    
    /* 赋值时间控件的语言 */
    assign('cfg_lang', $_CFG['lang']);
    /* 商品货品表 */
    assign('good_products_select', get_good_products_select($auction['goods_id']));
    /* 显示模板 */
    assign('ur_here', "添加活动");
    assign('action_link', list_link($is_add));
    assign_query_info();
    display(getPhpName() . '.htm');
}



/**
 * 审核
 */
function action_to_shen_he()
{
    global $exc, $ecs, $_CFG;
    /* 检查权限 */
    admin_priv('auction');
    
    if (empty($_GET['id'])) {
        sys_msg('invalid param');
    }
    $id = getRequestInt('id');
   
    $object = db_getRow("select * from ".db_table("xb_xxtz")." where id={$id}");
    
    if (empty($object)) {
        sys_msg("记录不存在");
    }
    assign('data', $object);
   
    /* 赋值时间控件的语言 */
    assign('cfg_lang', $_CFG['lang']);
    /* 显示模板 */
    assign('ur_here', "审核");
    assign('action_link', list_link(false));
    assign_query_info(); 
    display(getPhpName() . '_sh.htm');
}


/**
 * 升级准区域代理
 * @param unknown $user_name
 */
function  sheng_zhun_qu_yu_dai_li($user_name){
    $sql="update xbmall_users  set level = 2,user_type=1 where user_name='{$user_name}'";
    mysql_query($sql);
    
    
    $nowDateStr = date("Y-m-d H:i:s", time());
    //资金变动记录
    $sql="insert into xbmall_xb_xxtzzs (user,zsrq, comment,jf, type) values('{$user_name}','{$nowDateStr}'
    , '恭喜你升级为准区域招商',{$jin_e},0) ";
    mysql_query($sql);
    
}
/**
 * 铺货的推荐奖励
 */
function  pu_huo_jiang_li($user, $data){
    
    
    $sql="select count(0) from xbmall_xb_xxtz where user='{$data['user']}' ";
    $is_zuo_dan = db_getOne($sql);
    if($is_zuo_dan==0){//没有做单
        if($data['dan_shu']>=10){//单数达到10,升级为准区域代理
            sheng_zhun_qu_yu_dai_li($data['user']);
        }
    }
    
 
    //一层推荐人奖励
    tui_jian_jiang_li_yi_ceng($user, $data['dan_shu'],"一层推荐人奖励");
    //二层推荐人奖励
    tui_jian_jiang_li_er_ceng($user, $data['dan_shu'],"二层推荐人奖励");
    
    //区域代理奖励,发放到推荐奖励可用余额
    tui_jian_jiang_li_qu_yu($user, $data['dan_shu'],"区域招商奖励");
    
    
    //如果达到10单或直推的人达到3人,升级为准区域招商
    $sql="update xbmall_users  set level = 2 where user_name='{$data['user']}'";
    mysql_query($sql);
    
    //如果有区域代理
    
    //
    
    
}




/**
 * 二层推荐奖励, 如果推荐人的级别是准区域代理或区域代理享受
 * @param unknown $user_name
 * @param unknown $dan_shu
 */
function   tui_jian_jiang_li_er_ceng($user, $dan_shu){
    if($user['er_ceng_user']==null){//二层推荐人为空
        return ;
    }
    $sql="select level from xbmall_users where user_name='{$user['er_ceng_user']}'";
    $level = getOne($sql);
    if($level<2){//级别低于准区域代理
        return ;
    }
    $jin_e = 36*$dan_shu;
    tui_jian_jiang_li($user['er_ceng_user'], $jin_e);
}


/**
 * 一层推荐奖励, 不管是什么级别都是36
 * @param unknown $user_name
 * @param unknown $dan_shu
 */
function   tui_jian_jiang_li_yi_ceng($user, $dan_shu){
    if($user['recommend_user']==null){//一层推荐人为空
        return ;
    }
    
    $jin_e = 36*$dan_shu;
    tui_jian_jiang_li($user['recommend_user'], $jin_e);
}


/**
 * 推荐奖励
 * @param unknown $user_name
 * @param unknown $dan_shu
 */
function   tui_jian_jiang_li_qu_yu($user, $dan_shu){
    if($user['qu_yu_user']==null){//没有区域代理
        return ;
    }
    
    $jin_e = 20*$dan_shu;
    tui_jian_jiang_li($user['qu_yu_user'], $jin_e);
}

/**
 *
 * @param unknown $user_name
 * @param unknown $jin_e
 * @param unknown $message_head 消息头
 */
function tui_jian_jiang_li($user_name, $jin_e,$message){
    
    $nowDateStr = date("Y-m-d H:i:s", time());
    //修改账户
    $sql="update xbmall_users  set ye_tui_jian_shou_yi = ye_tui_jian_shou_yi+".$jin_e.",
     total_tui_jian_shou_yi = total_tui_jian_shou_yi+".$jin_e."  where user_name='{$user_name}'";
    mysql_query($sql);
    
    //资金变动记录
    $sql="insert into xbmall_xb_xxtzzs (user,zsrq, comment,jf, type) values('{$user_name}','{$nowDateStr}'
    , '{$message}',{$jin_e},0) ";
    mysql_query($sql);
    
    
    
}

/**
 * 铺货 的审核
 */
function action_shen_he(){
    admin_priv('auction');
    $id=getRequestInt("id");
    $state=getRequestInt("state");
    $shrq = time();
    $sql="update  xbmall_xb_xxtz  set state ={$state} ,shrq={$shrq}  where id={$id}";
    mysql_query($sql);
    
    if($state==1){//审核通过
        
        $data = db_getRow(" select * from xbmall_xb_xxtz where id={$id}");
        $sql = "select * from xbmall_users where user_name='{$data['user']}'";
        $user =  db_getRow($sql);
        $pu_huo = $data['dan_shu']*3600;//铺货金额
        $sql="update  xbmall_users set total_pu_huo=total_pu_huo+".$pu_huo." , ye_pu_huo=ye_pu_huo+".$pu_huo.
        " where user_name = '{$data['user']}'";//修改铺货总额和余额
        mysql_query($sql);
        pu_huo_jiang_li($user, $data);
    }
    
    $links = array(
        array(
            'href' => getPhpName() . '.php?act=list&' . list_link_postfix(),
            'text' => "返回铺货记录"
        )
    );
    sys_msg("编辑成功", 0, $links);
}



function action_edit()
{
    global $exc, $ecs, $_CFG;
    
    /* 检查权限 */
    admin_priv('auction');
    /* 是否添加 */
    assign('form_action', 'update');
    
    if (empty($_GET['id'])) {
        sys_msg('invalid param');
    }
    $id = intval($_GET['id']);
    $object = getRow("select * from ".db_table("xb_xxtz")." where id={$id}");
    if (empty($object)) {
        sys_msg("记录不存在");
    }
    $auction['status'] = "不存在";
    
    assign('auction', $object);
    
    /* 赋值时间控件的语言 */
    assign('cfg_lang', $_CFG['lang']);
    /* 显示模板 */
    assign('ur_here', "编辑");
    assign('action_link', list_link(false));
    assign_query_info();
    display(getPhpName() . '.htm');
}

/* ------------------------------------------------------ */
// -- 添加、编辑
/* ------------------------------------------------------ */
function action_insert()
{
    global $exc, $ecs;
    
    /* 检查权限 */
    admin_priv('auction');
    
    /* 检查是否选择了商品 */
    $goods_id = intval($_POST['goods_id']);
    if ($goods_id <= 0) {
        sys_msg("请选择商品");
    }
    
    $sql = "SELECT goods_name FROM " . $ecs->table('goods') . " WHERE goods_id = '$goods_id'";
    $row = db_getRow($sql);
    if (empty($row)) {
        sys_msg("商品不存在");
    }
    $goods_name = $row['goods_name'];
    
    /* 提交值 */
    $auction = array(
        'act_id' => intval($_POST['id']),
        'act_name' => empty($_POST['act_name']) ? $goods_name : sub_str($_POST['act_name'], 255, false),
        'act_desc' => $_POST['act_desc'],
        'act_type' => intval($_POST['act_type']),
        'goods_id' => $goods_id,
        'product_id' => empty($_POST['product_id']) ? 0 : $_POST['product_id'],
        'goods_name' => $goods_name,
        'start_time' => local_strtotime($_POST['start_time']),
        'end_time' => local_strtotime($_POST['end_time']),
        'ext_info' => serialize(array(
            'price' => round(floatval($_POST['price']), 2),
            'num' => round(floatval($_POST['num']), 2),
            'status' => empty($_POST['status']) ? 0 : $_POST['status']
        
        ))
    );
    
    $auction['is_finished'] = 0;
    db_autoExecute($ecs->table('xb_zrzcjl'), $auction, 'INSERT');
    $auction['act_id'] = db_insert_id();
    admin_log($auction['act_name'], 'add', 'auction');
    
    /* 清除缓存 */
    clear_cache_files();
    
    /* 提示信息 */
    
    $links = array(
        array(
            'href' => getPhpName() . '.php?act=add',
            'text' => "继续添加"
        ),
        array(
            'href' => getPhpName() . '.php?act=list',
            'text' => "返回铺货列表"
        )
    );
    sys_msg("添加成功", 0, $links);
}

function action_update()
{
    global $ecs;
    
    /* 检查权限 */
    admin_priv('auction');
    
    /* 检查是否选择了商品 */
    $goods_id = intval($_POST['goods_id']);
    if ($goods_id <= 0) {
        sys_msg("请选择商品");
    }
    
    $sql = "SELECT goods_name FROM " . $ecs->table('goods') . " WHERE goods_id = '$goods_id'";
    $row = db_getRow($sql);
    if (empty($row)) {
        sys_msg("商品不存在");
    }
    $goods_name = $row['goods_name'];
    
    /* 提交值 */
    $auction = array(
        'act_id' => intval($_POST['id']),
        'act_name' => empty($_POST['act_name']) ? $goods_name : sub_str($_POST['act_name'], 255, false),
        'act_desc' => $_POST['act_desc'],
        'act_type' => intval($_POST['act_type']),
        'goods_id' => $goods_id,
        'product_id' => empty($_POST['product_id']) ? 0 : $_POST['product_id'],
        'goods_name' => $goods_name,
        'start_time' => local_strtotime($_POST['start_time']),
        'end_time' => local_strtotime($_POST['end_time']),
        'ext_info' => serialize(array(
            'price' => round(floatval($_POST['price']), 2),
            'num' => round(floatval($_POST['num']), 2),
            'status' => empty($_POST['status']) ? 0 : $_POST['status']
        
        ))
    );
    db_autoExecute($ecs->table('xb_xxtz'), $auction, 'UPDATE', "act_id = '$auction[act_id]'");
    /* 记日志 */
    admin_log($auction['act_name'], 'edit', 'auction');
    /* 清除缓存 */
    clear_cache_files();
    
    $links = array(
        array(
            'href' => getPhpName() . '.php?act=list&' . list_link_postfix(),
            'text' => "返回铺货记录"
        )
    );
    sys_msg("编辑成功", 0, $links);
}

 
 


 


 
/*
 * 取得拍卖活动列表
 * @return array
 */
function data_list()
{
    
    /* 过滤条件 */
    $filter['real_name'] = empty($_REQUEST['real_name']) ? '' : trim($_REQUEST['real_name']);
    if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1) {
        $filter['real_name'] = json_str_iconv($filter['real_name']);
    }
    
    $where = " 1=1 ";
    
    quYuTiaoJian($filter, $where);//区域查询条件
    if (! empty($filter['real_name'])) {
        {
            $where .= " real_name like '" .mysql_like_quote($filter['real_name'])."' " ;
        }
    }
    $fromSql = db_table('xb_xxtz') . " WHERE  $where";
    
    setPageInfo($filter, $fromSql);
    
    $sql =  getDataSql($filter, $fromSql, "*", 'id');
    
 
    $filter['keyword'] = stripslashes($filter['keyword']);
    set_filter($filter, $sql);
    
    $res = db_query($sql);
    $list = array();
    while ($row = db_fetchRow($res)) {
        $row['tjrq'] = local_date('Y-m-d H:i', $row['tjrq']);
        if($row['shrq']!=null){
            $row['shrq'] = local_date('Y-m-d H:i', $row['shrq']);
        }
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
 * 列表链接
 *
 * @param bool $is_add
 *            是否添加（插入）
 * @param string $text
 *            文字
 * @return array('href' => $href, 'text' => $text)
 */
function list_link($is_add = true, $text = '')
{
    $href = getPhpName() . '.php?act=list';
    if (! $is_add) {
        $href .= '&' . list_link_postfix();
    }
    if ($text == '') {
        $text = "铺货列表";
    }
    
    return array(
        'href' => $href,
        'text' => $text
    );
}
?>