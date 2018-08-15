<?php

/**
 * ECSHOP 会员黑名单
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/* act操作项的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}

/*------------------------------------------------------ */
//-- 会员余额记录列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    /* 权限判断 */
    admin_priv('user_blanklist_list');

    /* 指定会员的ID为查询条件 */
    $user_id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

    $smarty->assign('ur_here',       "会员小黑屋");
    $smarty->assign('action_link',   array('text' => "新添加会员", 'href'=>'user_blacklist.php?act=add'));

    $list = user_blacklist_list();
    $smarty->assign('list',         $list['list']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);
    $smarty->assign('full_page',    1);

    assign_query_info();
    $smarty->display('user_blacklist.htm');
}

/*------------------------------------------------------ */
//-- 添加/编辑会员余额页面
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add')
{
    admin_priv('user_blanklist'); //权限判断

    
    /* 模板赋值 */
    $smarty->assign('ur_here',          $_LANG['add_user_blacklist']);
    $smarty->assign('form_act',           'insert');
    
    $href = 'user_blacklist.php?act=list';
    
    $smarty->assign('action_link', array('href' => $href, 'text' => $_LANG['user_blacklist']));

    assign_query_info();
    $smarty->display('user_blacklist_info.htm');
}

/*------------------------------------------------------ */
//-- 添加/编辑会员余额的处理部分
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'insert')
{
    /* 权限判断 */
    admin_priv('user_blanklist');

    /* 初始化变量 */
    $user_name    = !empty($_POST['user_name'])      ? trim($_POST['user_name'])          : '';
    $limit_desc   = !empty($_POST['limit_desc'])   ? trim($_POST['limit_desc'])       : '';

    $user_id = $db->getOne("SELECT user_id FROM " .$ecs->table('users'). " WHERE user_name = '$user_name'");

    /* 此会员是否存在 */
    if ($user_id == 0)
    {
        $link[] = array('text' => $_LANG['go_back'], 'href'=>'javascript:history.back(-1)');
        sys_msg($_LANG['username_not_exist'], 0, $link);
    }

   
    if ($_REQUEST['act'] == 'insert')
    {
        /* 入库的操作 */
        if ($process_type == 1)
        {
            $amount = (-1) * $amount;
        }
        
        $sql = "INSERT INTO " .$ecs->table('user_blacklist').
        		"(user_id, limit_desc, add_time)" . 
               " VALUES ('$user_id', '$limit_desc', '".gmtime()."')";
        $db->query($sql);
        $id = $db->insert_id();
    }
    
    admin_log($user_name, 'add', 'user_blacklist');

    /* 提示信息 */
    $href = 'user_blacklist.php?act=list';
    $link[0]['text'] = $_LANG['user_blacklist'];
    $link[0]['href'] = $href;

    $link[1]['text'] = $_LANG['continue_add'];
    $link[1]['href'] = 'user_blacklist.php?act=add';

    sys_msg($_LANG['attradd_succed'], 0, $link);
}

/*------------------------------------------------------ */
//-- ajax帐户信息列表
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $list = user_blacklist_list();
    $smarty->assign('list',         $list['list']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('user_blacklist.htm'), '', array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}
/*------------------------------------------------------ */
//-- ajax删除一条信息
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    /* 检查权限 */
    check_authz_json('user_blanklist');
    $id = @intval($_REQUEST['id']);
    
    //$sql = "DELETE FROM " . $ecs->table('user_blacklist') . " WHERE blacklist_id = '$id'";
    echo $sql = "UPDATE " . $ecs->table('user_blacklist') . " SET is_remove = 0 WHERE blacklist_id = '$id'";
    if ($db->query($sql, 'SILENT'))
    {
       admin_log(addslashes($user_name), 'remove', 'user_blacklist');
       $url = 'user_blacklist.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
       ecs_header("Location: $url\n");
       exit;
    }
    else
    {
        make_json_error($db->error());
    }
}
//刷邀请奖励会员  防止刷子 ,  批量关小黑屋
elseif ($_REQUEST['act'] == 'regg_err')  
{
//     $now_time = gmtime ()-86400;
//     $sql = "SELECT u.user_id,u.user_name ,temp.reg_ip, temp.t   FROM  `jindong_users` as u LEFT JOIN
//     (SELECT  COUNT(reg_ip) as t ,reg_ip FROM `jindong_users` WHERE reg_time>='{$now_time}'   GROUP BY reg_ip ) as temp
//     on u.reg_ip=temp.reg_ip WHERE temp.t>=10 ORDER BY temp.reg_ip DESC";
// $arrayIp = ['222.84.219.133',];
    $sql = "SELECT u.user_id,u.user_name ,temp.reg_ip, temp.t  FROM  ".$GLOBALS['ecs']->table('users')." as u LEFT JOIN
    (SELECT  COUNT(reg_ip) as t ,reg_ip FROM ".$GLOBALS['ecs']->table('users')." GROUP BY reg_ip ) as temp
    on u.reg_ip=temp.reg_ip WHERE temp.t>=5 ORDER BY temp.reg_ip DESC";
// $sql = "SELECT u.user_id,u.user_name ,temp.reg_ip, temp.t  FROM  `jindong_users` as u LEFT JOIN
//     (SELECT  COUNT(reg_ip) as t ,reg_ip FROM `jindong_users` GROUP BY reg_ip ) as temp
//     on u.reg_ip=temp.reg_ip WHERE temp.t in (" .$arrayIp . ") ORDER BY temp.reg_ip DESC";

    $arr1 = $GLOBALS ['db']->getAll($sql);
//     echo '<pre>';
//     var_dump($arr1);
//     echo '</pre>';exit;
    foreach ($arr1 as $k=>$v)
    {
        $bsql = "select blacklist_id from ".$GLOBALS['ecs']->table('user_blacklist')." where user_id={$v['user_id']}";
        $bbdata = $GLOBALS ['db']->getRow($bsql);
        if (empty($bbdata))
        {
            $blog= array(
                'user_id'=>$v['user_id'],
                'limit_desc'=>"违规注册，系统自动冻结！",
                'add_time'=>time(),
                'is_remove'=>1
            );
            $step3 = $GLOBALS ['db']->autoExecute ( $GLOBALS ["ecs"]->table ( 'user_blacklist' ), $blog, 'INSERT' );
            if($step3){
                echo '会员' . $v['user_name'] . '成功关入小黑屋<br />';
            }
        }
    }
    echo '会员批量加关入小黑屋完成';exit;
}

/*
elseif ($_REQUEST['act'] == 'dark_room')
{
    //检查权限 
    check_authz_json('user_blanklist');
    $id = @intval($_REQUEST['user_id']);
    
    //$sql = "DELETE FROM " . $ecs->table('user_blacklist') . " WHERE blacklist_id = '$id'";
    $sql = "SELECT COUNT(*) FROM " . $ecs->table('user_blacklist') . " WHERE user_id = ".$id;
    $num = $db->getOne($sql);
    $smarty->assign('num', $num);
    assign_query_info();
    $smarty->display('dark_room.htm');
    
}*/
/*------------------------------------------------------ */
//-- 会员余额函数部分
/*------------------------------------------------------ */
/**
 * 查询会员余额的数量
 * @access  public
 * @param   int     $user_id        会员ID
 * @return  int
 */
function get_user_surplus($user_id)
{
    $sql = "SELECT SUM(user_money) FROM " .$GLOBALS['ecs']->table('account_log').
           " WHERE user_id = '$user_id'";

    return $GLOBALS['db']->getOne($sql);
}

/**
 * 更新会员账目明细
 *
 * @access  public
 * @param   array     $id          帐目ID
 * @param   array     $admin_note  管理员描述
 * @param   array     $amount      操作的金额
 * @param   array     $is_paid     是否已完成
 *
 * @return  int
 */
function update_user_account($id, $amount, $admin_note, $is_paid)
{
    $sql = "UPDATE " .$GLOBALS['ecs']->table('user_account'). " SET ".
           "admin_user  = '$_SESSION[admin_name]', ".
           "amount      = '$amount', ".
           "paid_time   = '".gmtime()."', ".
           "admin_note  = '$admin_note', ".
           "is_paid     = '$is_paid' WHERE id = '$id'";
    return $GLOBALS['db']->query($sql);
}

/**
 *
 *
 * @access  public
 * @param
 *
 * @return void
 */
function user_blacklist_list()
{
    $result = get_filter();
    if ($result === false)
    {
        /* 过滤列表 */
        $filter['keywords'] = empty($_REQUEST['keywords']) ? '' : trim($_REQUEST['keywords']);
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['keywords'] = json_str_iconv($filter['keywords']);
        }

        $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'ub.add_time' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $where = " WHERE 1 = 1 ";
       
        if ($filter['keywords'])
        {
            $where .= " AND u.user_name LIKE '%" . mysql_like_quote($filter['keywords']) . "%'";
        }
        
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('user_blacklist') . 
        		" AS ub LEFT JOIN " . $GLOBALS['ecs']->table('users') . " AS u ON ub.user_id = u.user_id" . $where;
                
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
      	
        /* 分页大小 */
        $filter = page_and_size($filter);
        
        $sql = "SELECT ub.blacklist_id, ub.is_remove, ub.user_id, u.user_name, ub.limit_desc, ub.add_time, ub.rows FROM " . $GLOBALS['ecs']->table('user_blacklist') .
        " AS ub LEFT JOIN " . $GLOBALS['ecs']->table('users') . " AS u ON ub.user_id = u.user_id" . $where
        . "ORDER by " . $filter['sort_by'] . " " .$filter['sort_order']. " LIMIT ".$filter['start'].", ".$filter['page_size'];
        
        $filter['keywords'] = stripslashes($filter['keywords']);
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }

    $list = $GLOBALS['db']->getAll($sql);
    foreach ($list AS $key => $value)
    {
        $list[$key]['add_time']             = local_date('Y-m-d H:i:s', $value['add_time']);
     }
    $arr = array('list' => $list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

?>