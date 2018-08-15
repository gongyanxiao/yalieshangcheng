<?php

/**
 * ECSHOP 广告位置管理程序
 * ============================================================================
 * 版权所有 2005-2011 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: ad_position.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/admin/ads.php');
include_once (ROOT_PATH . '/includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);
/* act操作项的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}

$smarty->assign('lang', $_LANG);
$exc = new exchange($ecs->table("ad_position"), $db, 'position_id', 'position_name');

/*------------------------------------------------------ */
//-- 广告位置列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    admin_priv('2_mutual_list');
    $smarty->assign('ur_here',     $_LANG['2_mutual']);
    $smarty->assign('action_link', array('text' => '添加互助项目', 'href' => 'mutual.php?act=add'));
    $smarty->assign('full_page',   1);

    $position_list = ad_position_list();

    $smarty->assign('position_list',   $position_list['position']);
    $smarty->assign('filter',          $position_list['filter']);
    $smarty->assign('record_count',    $position_list['record_count']);
    $smarty->assign('page_count',      $position_list['page_count']);

    assign_query_info();
    $smarty->display('mutual_list.htm');
}

/*------------------------------------------------------ */
//-- 添加广告位页面
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add')
{
    if ($_POST)
    {
        admin_priv('2_mutual');
        // 处理数据
        $content = !empty($_POST['FCKeditor1']) ? $_POST['FCKeditor1'] : '';
        $title = !empty($_POST['title']) ? $_POST['title'] : '';
        $target_money = empty($_POST['target_money']) ? 0 : $_POST['target_money'] ;
        $user_id = $_POST['user_id'] > 0 ? $_POST['user_id']+0 : 0 ;
    
        //判断是否为空
        if ( empty( $target_money ) )
        {
            show_message('目标金额不能为空','返回编辑', 'mutual.php', 'edit', false);
        }
        //判断用户名是否为空
        if ( empty( $user_id ) )
        {
            show_message('关联会员不能为空','返回编辑', 'mutual.php', 'edit', false);
        }
   
        if (isset($_FILES['face_card']) && $_FILES['face_card']['tmp_name'] != '') {
            $face_card = $image->upload_image($_FILES['face_card']);
            if ($face_card === false) {
                sys_msg($image->error_msg(), 1, array(), false);
            }
        }
        
        //插入数据
        $insertData['content'] = $content;
        $insertData['title'] = $title;
        $insertData['target_money'] = $target_money;
        $insertData['user_id'] = $user_id;
        $insertData['images'] = $face_card ;
        $insertData['status'] = $_POST['order_status'] ? 1 : 0 ;
        $insertData['add_time'] = gmtime() ;
    
        $GLOBALS['db']->startTrans();
    
        $insertBd = $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table("mutual"), $insertData);
        if ($insertBd) {
            $bdId = $GLOBALS['db']->insert_id();
        }
        else
        {
            $GLOBALS['db']->rollbackTrans();
            show_message('操作有误,请重新操作');
            exit();
        }
        $GLOBALS['db']->commitTrans();
        ecs_header("Location: mutual.php\n");
        exit();
    }
    
    admin_priv('2_mutual');
    
    /* 创建 html editor */
    create_html_editor('FCKeditor1');
    
    /*初始化*/
    $article = array();
    $article['is_open'] = 1;
    
    $smarty->assign('article',     $article);
    
    /* 模板赋值 */
    $smarty->assign('ur_here',     $_LANG['position_add']);
    $smarty->assign('form_act',    'add');

    $smarty->assign('action_link', array('href' => 'ad_position.php?act=list', 'text' => $_LANG['ad_position']));
    $smarty->assign('posit_arr',   array('position_style' => '<table cellpadding="0" cellspacing="0">' ."\n". '{foreach from=$ads item=ad}' ."\n". '<tr><td>{$ad}</td></tr>' ."\n". '{/foreach}' ."\n". '</table>'));
    $smarty->assign('ur_here',     '编辑爱心互助');
    assign_query_info();
    $smarty->display('mutual_info.htm');
}
/*------------------------------------------------------ */
//-- 爱心互助编辑页面
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit')
{
    $id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    
    if ($_POST && $id>0)
    {
        admin_priv('2_mutual');
        // 处理数据
        $content = !empty($_POST['FCKeditor1']) ? $_POST['FCKeditor1'] : '';
        $title = !empty($_POST['title']) ? $_POST['title'] : '';
        $target_money = empty($_POST['target_money']) ? 0 : $_POST['target_money'] ;
        $user_id = $_POST['user_id'] > 0 ? $_POST['user_id']+0 : 0 ;
    
        //判断是否为空
        if ( empty( $target_money ) )
        {
            show_message('目标金额不能为空','返回编辑', 'mutual.php', 'edit', false);
        }
        //判断用户名是否为空
        if ( empty( $user_id ) )
        {
            show_message('关联会员不能为空','返回编辑', 'mutual.php', 'edit', false);
        }
    
        if (isset($_FILES['face_card']) && $_FILES['face_card']['tmp_name'] != '') {
            $face_card = $image->upload_image($_FILES['face_card']);
            if ($face_card === false) {
                sys_msg($image->error_msg(), 1, array(), false);
            }
        }
        
        //插入数据
        $insertData['content'] = $content;
        $insertData['title'] = $title;
        $insertData['target_money'] = $target_money;
        $insertData['user_id'] = $user_id;
        $insertData['status'] = $_POST['order_status'] ? 1 : 0 ;
        if ($face_card)
        {
            $insertData['images'] = $face_card ;
        }
        $GLOBALS['db']->startTrans();
    
        $insertBd = $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table("mutual"), $insertData ,'update' , " mutual_id ='".$id."' " );
        if (!$insertBd) {
            $GLOBALS['db']->rollbackTrans();
            show_message('操作有误,请重新操作');
            exit();
        }
        $GLOBALS['db']->commitTrans();
        ecs_header("Location: mutual.php\n");
        exit();
    }
    
    admin_priv('2_mutual');

    /* 修改信息 */
    $sql = 'SELECT * FROM ' .$GLOBALS['ecs']->table('mutual'). " WHERE mutual_id='$id'";
    $mutual_info = $GLOBALS['db']->getRow($sql);

    //获取会员昵称
    $sql = " SELECT user_name,card, mobile_phone ,real_name FROM ". $GLOBALS['ecs']->table('users')." where user_id ='".$mutual_info['user_id']."' ";

    $user_info = $GLOBALS['db']->getRow($sql);
    
    $opt = $user_info['real_name'].' / '.$user_info['card'].' / '. $user_info['mobile_phone'].' / '. $user_info['user_name'];
    
    $mutual_info['user_desc'] = $opt;

     /* 创建 html editor */
    create_html_editor('FCKeditor1',htmlspecialchars($mutual_info['content'])); 
    
    $smarty->assign('ur_here',     '互助信息修改');
    $smarty->assign('action_link', array('href' => 'mutual.php?act=list', 'text' => $_LANG['1_mutual']));
    $smarty->assign('mutual',   $mutual_info);
    
//     print_r($mutual_info);
    
    $smarty->assign('form_act',    'edit');

    $smarty->display('mutual_info.htm');
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    admin_priv('2_mutual_list');
    $position_list = ad_position_list();

    $smarty->assign('position_list',   $position_list['position']);
    $smarty->assign('filter',          $position_list['filter']);
    $smarty->assign('record_count',    $position_list['record_count']);
    $smarty->assign('page_count',      $position_list['page_count']);

    make_json_result($smarty->fetch('mutual_list.htm'), '',
        array('filter' => $position_list['filter'], 'page_count' => $position_list['page_count']));
}

/*------------------------------------------------------ */
//-- 互助信息回收站
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    admin_priv('2_mutual');
    check_authz_json('ad_manage');

    $id = intval($_GET['id']);

    $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('mutual'). " WHERE mutual_id = '$id'";
    if ($db->getOne($sql) > 0)
    {
        $sql ="UPDATE " .$GLOBALS['ecs']->table('mutual'). " SET `is_delete`='1' WHERE (`mutual_id`='".$id."')";
        $db->query($sql);
    }
    
    $url = 'mutual.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/*------------------------------------------------------ */
//-- 搜索会员
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'get_goods_list')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    $filters = $json->decode($_GET['JSON']);

    $where = ' 1 ';

    //检索条件
    if(!empty($filters->keyword))
    {
        if (in_array($filters->user_type , array('user_name','real_name','mobile_phone','card')))
        {
            $where .= " and  ".$filters->user_type." =  '".$filters->keyword."'  "; 
        }
    }
    
    //整合SQL
    $sql =" SELECT user_name , user_id  , real_name , card ,mobile_phone FROM ".$GLOBALS['ecs']->table('users')." where  $where  ORDER BY `user_id` DESC LIMIT 0, 50 "; 

    $arr = $GLOBALS['db']->getAll($sql);

    foreach ($arr AS $key => $val)
    {
        $opt = $val['real_name'].' / '.$val['card'].' / '. $val['mobile_phone'].' / '. $val['user_name']; 
        $retuenData .="<option id='wyb_".$val['user_id']."' value='".$val['user_id']."'>".$opt."</option>"; 
    }
    die($retuenData);
}

/* 获取广告位置列表 */
function ad_position_list()
{	 
    $where ='';

    //是否有检索条件
    if ($_REQUEST['keyword'])
    {
        if (in_array($_REQUEST['select'], array('target_money','title')))
        {
            $where .=" and i.".$_REQUEST['select']." = '".trim($_REQUEST['keyword'])."' ";
        }
        if (in_array($_REQUEST['select'], array( 'user_name','real_name')))
        {
            $where .=" and u.".$_REQUEST['select']." = '".trim($_REQUEST['keyword'])."' ";
        }
    }
    //是否付款检索
    if ($_REQUEST['status']==='0' || $_REQUEST['status']==='1' )
    {
        $where .=" and i.status = '".$_REQUEST['status']."' ";
    }
    
    //时间检索
    if ($_REQUEST['end_time'] > 0 ||  $_REQUEST['start_time']>0)
    {
         //处理开始时间
         if (empty($_REQUEST['start_time']))
         {
             $up_time = 0;
         }
         else 
         {
             $up_time = strtotime($_REQUEST['start_time']);
         }
         
         //处理结束时间
         if (empty($_REQUEST['end_time']))
         {
             $end_time = 9999033008;
         }
         else 
         {
             $end_time = strtotime($_REQUEST['end_time']);
         }
         $where .=" and  `add_time` BETWEEN '".$up_time."' AND '".$end_time."' ";
    }
    
    $sql = " SELECT count(*) as num FROM ".$GLOBALS['ecs']->table('mutual')." as i "
        ." LEFT JOIN ".$GLOBALS['ecs']->table('users')." as u on u.user_id = i.user_id "
            ." where i.is_delete = 0 $where ";
    
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);
    
    $filter = page_and_size($filter);
    
    $sql = " SELECT i.* , u.user_name,  u.real_name , u.mobile_phone FROM ".$GLOBALS['ecs']->table('mutual')." as i "
        ." LEFT JOIN ".$GLOBALS['ecs']->table('users')." as u on u.user_id = i.user_id "
        ." where i.is_delete = 0 $where order by  mutual_id desc ";
// echo $sql;
    // 查询数据 
    $arr = array();
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);
    while ($rows = $GLOBALS['db']->fetchRow($res))
    {
        $rows['times'] = $rows['add_time'] > 0 ? date('Y-m-d H:i:s',$rows['add_time']) : '' ;
        $rows['status'] = $rows['status'] > 0 ? '已完成' : '<span style="color:red">未完成</span>' ;
        $arr[] = $rows;
    }
    
//     echo '<pre>';
//     print_r($arr);
//     echo '</pre>';
    
    return array('position' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}
?>