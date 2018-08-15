<?php

/**
 * 铺货记录
 */
require_once (dirname(__FILE__) . '/xb_header.php');

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
    assign('ur_here', "财务汇总");
    // assign('action_link', array('href' => getPhpName().'.php?act=add', 'text' => "添加抽奖"));
    $data = data_list();
    /*
     * 城市列表
     */
    $sql = 'SELECT region_id, region_name FROM ' . db_table('region') . " WHERE parent_id = 1 ";
    $province_list = db_getAll($sql);
    assign('province_list', $province_list);
    
    assign('file_name', PHP_SELF);
    assign('data', $data);
    
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
                'text' => "返回抽奖列表",
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
    $auction = get_xb_zrzcjl($id);
    if (empty($auction)) {
        sys_msg("活动不存在");
    }
    $auction['status'] = "不存在";
    
    assign('auction', $auction);
    
    /* 赋值时间控件的语言 */
    assign('cfg_lang', $_CFG['lang']);
    
    /* 商品货品表 */
    assign('good_products_select', get_good_products_select($auction['goods_id']));
    
    /* 显示模板 */
    assign('ur_here', "编辑活动");
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
            'text' => "返回抽奖列表"
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
    db_autoExecute($ecs->table('xb_zrzcjl'), $auction, 'UPDATE', "act_id = '$auction[act_id]'");
    /* 记日志 */
    admin_log($auction['act_name'], 'edit', 'auction');
    /* 清除缓存 */
    clear_cache_files();
    
    $links = array(
        array(
            'href' => getPhpName() . '.php?act=list&' . list_link_postfix(),
            'text' => "返回抽奖列表"
        )
    );
    sys_msg("编辑成功", 0, $links);
}

/* ------------------------------------------------------ */
// -- 添加、编辑后提交
/* ------------------------------------------------------ */

/*
 * 取得拍卖活动列表
 * @return array
 */
function data_list()
{
    $filter = array();
    $where =  " where 1=1";
    quYuTiaoJian($filter, $where);
    
    $sql = "select 
   sum(ye_he_huo_ren) as  ye_he_huo_ren, sum(ye_pu_huo) as ye_pu_huo, sum(ye_dian_pu)as ye_dian_pu,
 sum(ye_yqjl) as ye_yqjl , sum(ye_tui_jian_shou_yi) as ye_tui_jian_shou_yi, 
 sum(ye_qian_dao) as ye_qian_dao , sum(ye_hui_tong_bao) as ye_hui_tong_bao , 
 sum(total_tx) as total_tx , sum(total_xfj) as total_xfj, sum(total_tui_jian_shou_yi) as total_tui_jian_shou_yi,
 sum(total_dian_pu) as total_dian_pu, sum(total_yqjl) as total_yqjl, sum(total_qian_dao) as total_qian_dao,
sum(total_pu_huo) as total_pu_huo, sum(total_he_huo_ren) as total_he_huo_ren from  xbmall_users {$where}";
    $row =db_getRow($sql);
    
    return $row;
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
        $text = "抽奖列表";
    }
    
    return array(
        'href' => $href,
        'text' => $text
    );
}
?>