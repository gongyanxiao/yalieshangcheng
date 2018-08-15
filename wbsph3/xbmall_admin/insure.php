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
    admin_priv('1_insure_list');
    $smarty->assign('ur_here',     $_LANG['1_insure']);
  //  $smarty->assign('action_link', array('text' => $_LANG['position_add'], 'href' => 'ad_position.php?act=add'));
    $smarty->assign('full_page',   1);

    $position_list = ad_position_list();

    $smarty->assign('position_list',   $position_list['position']);
    $smarty->assign('filter',          $position_list['filter']);
    $smarty->assign('record_count',    $position_list['record_count']);
    $smarty->assign('page_count',      $position_list['page_count']);

    assign_query_info();
    $smarty->display('insure_list.htm');
}

/*------------------------------------------------------ */
//-- 添加广告位页面
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add')
{
    admin_priv('1_insure');

    /* 模板赋值 */
    $smarty->assign('ur_here',     $_LANG['position_add']);
    $smarty->assign('form_act',    'insert');

    $smarty->assign('action_link', array('href' => 'ad_position.php?act=list', 'text' => $_LANG['ad_position']));
    $smarty->assign('posit_arr',   array('position_style' => '<table cellpadding="0" cellspacing="0">' ."\n". '{foreach from=$ads item=ad}' ."\n". '<tr><td>{$ad}</td></tr>' ."\n". '{/foreach}' ."\n". '</table>'));

    assign_query_info();
    $smarty->display('ad_position_info.htm');
}
elseif ($_REQUEST['act'] == 'insert')
{
    admin_priv('1_insure');

    /* 对POST上来的值进行处理并去除空格 */
    $position_name = !empty($_POST['position_name']) ? trim($_POST['position_name']) : '';
    $position_desc = !empty($_POST['position_desc']) ? nl2br(htmlspecialchars($_POST['position_desc'])) : '';
    $ad_width      = !empty($_POST['ad_width'])      ? intval($_POST['ad_width'])  : 0;
    $ad_height     = !empty($_POST['ad_height'])     ? intval($_POST['ad_height']) : 0;

    /* 查看广告位是否有重复 */
    if ($exc->num("position_name", $position_name) == 0)
    {
        /* 将广告位置的信息插入数据表 */
        $sql = 'INSERT INTO '.$ecs->table('ad_position').' (position_name, ad_width, ad_height, position_desc, position_style) '.
               "VALUES ('$position_name', '$ad_width', '$ad_height', '$position_desc', '$_POST[position_style]')";

        $db->query($sql);
        /* 记录管理员操作 */
        admin_log($position_name, 'add', 'ads_position');

        /* 提示信息 */
        $link[0]['text'] = $_LANG['ads_add'];
        $link[0]['href'] = 'ads.php?act=add';

        $link[1]['text'] = $_LANG['continue_add_position'];
        $link[1]['href'] = 'ad_position.php?act=add';

        $link[2]['text'] = $_LANG['back_position_list'];
        $link[2]['href'] = 'ad_position.php?act=list';

        sys_msg($_LANG['add'] . "&nbsp;" . stripslashes($position_name) . "&nbsp;" . $_LANG['attradd_succed'], 0, $link);
    }
    else
    {
        $link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
        sys_msg($_LANG['posit_name_exist'], 0, $link);
    }
}

/*------------------------------------------------------ */
//-- 广告位编辑页面
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit')
{
    $id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    if ($_POST)
    {
        print_r($_POST);
        
        $sql = 'SELECT * FROM ' .$ecs->table('insure_info'). " WHERE order_id='$id'";
        $insure_info = $db->getRow($sql);
        
        $updata = array();
        
        //是否支付金额
        if ($_POST['order_status'] == 1 )
        {
            if (empty($insure_info['pay_time']))
            {
                $updata['pay_time'] = gmtime();
            }
            $updata['order_status'] = 1;
        }
        else 
        {
            $updata['order_status'] = 0;
        }
        $updata['consignee'] = trim($_POST['consignee']);
        $updata['mobile']    = trim($_POST['mobile']);
        $updata['card_id']    = $_POST['card_id'];
        
        $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table("insure_info"), $updata , 'update' , " order_id='$id' ");
        ecs_header("Location: insure.php \n");
        exit;
    }
    
    admin_priv('1_insure');

    /* 获取广告位数据 */
    $sql = 'SELECT * FROM ' .$ecs->table('insure_info'). " WHERE order_id='$id'";
    $insure_info = $db->getRow($sql);

    //获取会员昵称
    $sql = " SELECT user_name FROM ". $GLOBALS['ecs']->table('users')." where user_id ='".$insure_info['user_id']."' ";
    $insure_info['user_name'] = $GLOBALS['db']->getOne($sql);
    $insure_info['times'] = $insure_info['pay_time'] > 0 ? date('Y-m-d H:i:s',$insure_info['pay_time']) : '' ;

    $smarty->assign('ur_here',     '投保信息修改');
    $smarty->assign('action_link', array('href' => 'insure.php?act=list', 'text' => $_LANG['1_insure']));
    $smarty->assign('insure_info',   $insure_info);
    $smarty->assign('form_act',    'update');

    $smarty->display('insure_info.htm');
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $position_list = ad_position_list();

    $smarty->assign('position_list',   $position_list['position']);
    $smarty->assign('filter',          $position_list['filter']);
    $smarty->assign('record_count',    $position_list['record_count']);
    $smarty->assign('page_count',      $position_list['page_count']);

    make_json_result($smarty->fetch('insure_list.htm'), '',
        array('filter' => $position_list['filter'], 'page_count' => $position_list['page_count']));
}

/*------------------------------------------------------ */
//-- 投保信息回收站
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('ad_manage');

    $id = intval($_GET['id']);

    $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('insure_info'). " WHERE order_id = '$id'";
    if ($db->getOne($sql) > 0)
    {
        $sql ="UPDATE " .$GLOBALS['ecs']->table('insure_info'). " SET `is_delete`='1' WHERE (`order_id`='".$id."')";
        $db->query($sql);
    }
    
    $url = 'insure.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/* 获取广告位置列表 */
function ad_position_list()
{	 
    $where ='';
    //是否有检索条件
    if ($_REQUEST['keyword'])
    {
        if (in_array($_REQUEST['select'], array('order_sn','consignee','mobile','insure_baoe','insure_money')))
        {
            $where .=" and i.".$_REQUEST['select']." = '".trim($_REQUEST['keyword'])."' ";
        }
        if (in_array($_REQUEST['select'], array( 'user_name')))
        {
            $where .=" and u.".$_REQUEST['select']." = '".trim($_REQUEST['keyword'])."' ";
        }
    }
    //是否付款检索
    if ($_REQUEST['order_status']==='0' || $_REQUEST['order_status']==='1' )
    {
        $where .=" and i.order_status = '".$_REQUEST['order_status']."' ";
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
         $where .=" and  `pay_time` BETWEEN '".$up_time."' AND '".$end_time."' ";
    }
    
    $sql = " SELECT count(*) as num FROM ".$GLOBALS['ecs']->table('insure_info')." as i "
        ." LEFT JOIN ".$GLOBALS['ecs']->table('users')." as u on u.user_id = i.user_id "
            ." where i.is_delete = 0 $where ";
    
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);
    
    $filter = page_and_size($filter);
    
    $sql = " SELECT * FROM ".$GLOBALS['ecs']->table('insure_info')." as i "
        ." LEFT JOIN ".$GLOBALS['ecs']->table('users')." as u on u.user_id = i.user_id "
        ." where i.is_delete = 0 $where order by order_id desc ";

    // 查询数据 
    $arr = array();
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);
    while ($rows = $GLOBALS['db']->fetchRow($res))
    {
        $rows['times'] = $rows['pay_time'] > 0 ? date('Y-m-d H:i:s',$rows['pay_time']) : '' ;
        $rows['order_status'] = $rows['order_status'] > 0 ? '已付款' : '<span style="color:red">未付款</span>' ;
        $arr[] = $rows;
    }
    
    return array('position' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}
?>