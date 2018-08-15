<?php

/**
 * ECSHOP 管理中心办事处管理
 * ============================================================================
 * 版权所有 2005-2011 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: agency.php 17217 2011-01-19 06:29:08Z liubo $
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/* ------------------------------------------------------ */
//-- 办事处列表
/* ------------------------------------------------------ */
if ($_REQUEST['act'] == 'list') {
    admin_priv('2_pension_apply_list');
    $smarty->assign('ur_here', $_LANG['pension_apply_list']);
    $smarty->assign('full_page', 1);

    $agency_list = get_pension_order_list();
    $smarty->assign('pension_apply_list', $agency_list['pension_apply_list']);
    $smarty->assign('filter', $agency_list['filter']);
    $smarty->assign('record_count', $agency_list['record_count']);
    $smarty->assign('page_count', $agency_list['page_count']);

    /* 排序标记 */
    $sort_flag = sort_flag($agency_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('pension_apply_list.htm');
}

/* ------------------------------------------------------ */
//-- 排序、分页、查询
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'query') {
    check_authz_json('2_pension_apply_list');
    $agency_list = get_pension_order_list();
    $smarty->assign('pension_apply_list', $agency_list['pension_apply_list']);
    $smarty->assign('filter', $agency_list['filter']);
    $smarty->assign('record_count', $agency_list['record_count']);
    $smarty->assign('page_count', $agency_list['page_count']);

    /* 排序标记 */
    $sort_flag = sort_flag($agency_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('pension_apply_list.htm'), '', array('filter' => $agency_list['filter'], 'page_count' => $agency_list['page_count']));
}

/* ------------------------------------------------------ */
//-- 删除办事处
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == 'apply') {
admin_priv('2_pension_apply');
    $id = intval($_GET['id']);
    $type = intval($_GET['type']);
    $GLOBALS['db']->startTrans();
    $sql = "SELECT user_id,money FROM " . $GLOBALS['ecs']->table('pension_order') . " WHERE id='$id' and status=0";
    $record = $GLOBALS['db']->getRow($sql);
    if (!empty($record)) {
        $sql = "UPDATE " . $ecs->table("pension_order") . " SET `status` ='$type',app_time='" . gmtime() . "' WHERE id = '$id'";
        $res = false;
        if ($GLOBALS['db']->query($sql)) {
            if ($type * 1 == 1) {
                $res = true;
                admin_log($name, 'apply-success', 'pension_infos');
            } else {
                admin_log($name, 'apply-failure', 'pension_infos');
                $res = log_account_change($record['user_id'], 0, 0, 0, 0, '养老审核不通过，返还养老积分' . $record['money'], 8, 0, 0, 0, 0, 0, 0, 0, 0, 0, $record['money'] * 1, 0, 0);
            }
        }
        if ($res == true) {
            $GLOBALS['db']->commitTrans();
            $url = 'pension_apply.php?act=list&' . str_replace('act=apply', '', $_SERVER['QUERY_STRING']);
            ecs_header("Location: $url\n");
        } else {
            $GLOBALS['db']->rollbackTrans();
            $url = 'pension_apply.php?act=list&' . str_replace('act=apply', '', $_SERVER['QUERY_STRING']);
            ecs_header("Location: $url\n");
        }
    } else {
        $url = 'pension_apply.php?act=list&' . str_replace('act=apply', '', $_SERVER['QUERY_STRING']);
        ecs_header("Location: $url\n");
    }
}


/* ------------------------------------------------------ */
//-- 会员余额函数部分
/* ------------------------------------------------------ */ elseif ($_REQUEST['act'] == "batch") {
	admin_priv('2_pension_apply');
    $type = $_REQUEST['type'];
    if (in_array($type, array("todo", "cancel"))) {
        $checkboxes = $_REQUEST["checkboxes"];
        if (!isset($_REQUEST['checkboxes']) || empty($_REQUEST['checkboxes'])) {
            echo "参数错误";
            exit;
        } else {
            $checkboxes = $_REQUEST['checkboxes'];
            if (count($checkboxes) == 0) {
                echo "参数错误";
                exit;
            } else {
                $isok = true;
                $GLOBALS['db']->startTrans();
                $isSend = false;
                try {
                    foreach ($checkboxes as $idKey => $idValue) {
                        if ($type == "todo") {
                            $type = 1;
                        } elseif ($type == "cancel") {
                            $type = 2;
                        }
                        $sql = "SELECT user_id,money FROM " . $GLOBALS['ecs']->table('pension_order') . " WHERE id='$id' and status=0";
                        $record = $GLOBALS['db']->getRow($sql);
                        if (!empty($record)) {
                            $sql = "UPDATE " . $ecs->table("pension_order") . " SET `status` ='$type',app_time='" . gmtime() . "' WHERE id = '$idValue'";
                            $res = false;
                            if ($GLOBALS['db']->query($sql)) {
                                if ($type * 1 == 1) {
                                    $res = true;
                                    admin_log($name, 'apply-success', 'pension_infos');
                                } else {
                                    admin_log($name, 'apply-failure', 'pension_infos');
                                    $res = log_account_change($record['user_id'], 0, 0, 0, 0, '养老审核不通过，返还养老积分' . $record['money'], 8, 0, 0, 0, 0, 0, 0, 0, 0, 0, $record['money'] * 1, 0, 0);
                                    if ($res == false) {
                                        $isok = false;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                } catch (Exception $ex) {
                    $isok = false;
                    $GLOBALS['db']->rollbackTrans();
                }
                if ($isok) {
                    $GLOBALS['db']->commitTrans();
                    /* 提示信息 */
                    $link[0]['text'] = $_LANG['pension_apply_list'];
                    $link[0]['href'] = 'pension_apply.php?act=list&stype=' . $stype . '&' . list_link_postfix();
                    sys_msg($_LANG['pension_apply_attradd_succed'], 0, $link);
                } else {
                    $GLOBALS['db']->rollbackTrans();
                }
            }
        }
    } else {
        echo "请求被禁止";
    }
}

/**
 * 取得办事处列表
 * @return  array
 */
function get_pension_order_list() {
    $result = get_filter();
    if ($result === false) {
        /* 初始化分页参数 */
        $filter = array();
        $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $filter['start_date'] = empty($_REQUEST['start_time']) ? '' : local_strtotime($_REQUEST['start_time']);
        $filter['end_date'] = empty($_REQUEST['end_time']) ? '' : (local_strtotime($_REQUEST['end_time']) + 86400);
        $where = ' WHERE 1=1 ';

        if (isset($_REQUEST['status']) && trim($_REQUEST['status']) != '') {
            $where.=" AND s.`status`='" . $_REQUEST['status'] . "'";
        }

        if (isset($_REQUEST['keywords']) && !empty($_REQUEST['keywords'])) {
            $keyword = mysql_like_quote($_REQUEST['keywords']);
            $where.="  AND (s.real_name like '%" . $keyword . "%' or s.idcard like '%" . $keyword . "%' or s.phone like '%" . $keyword . "%' or a.mobile_phone like '%" . $keyword . "%')";
        }

        if (!empty($filter['start_date'])) {
            $where .= "AND s.add_time >= " . $filter['start_date'] . " ";
        }
        if (!empty($filter['end_date'])) {
            $where .= " AND s.add_time < '" . $filter['end_date'] . "'";
        }

        /* 查询记录总数，计算分页数 */
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('pension_order') . " s LEFT JOIN" . $GLOBALS['ecs']->table("users") . " as a on s.user_id=a.user_id" . $where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
        $filter = page_and_size($filter);

        /* 查询记录 */
        $sql = "SELECT s.*,a.mobile_phone FROM " . $GLOBALS['ecs']->table('pension_order') . " s LEFT JOIN" . $GLOBALS['ecs']->table("users") . " as a on s.user_id=a.user_id " . $where . " ORDER BY $filter[sort_by] $filter[sort_order]";

        set_filter($filter, $sql);
    } else {
        $sql = $result['sql'];
        $filter = $result['filter'];
    }
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);

    $arr = array();
    while ($rows = $GLOBALS['db']->fetchRow($res)) {
        $rows['add_time'] = local_date('Y-m-d H:i:s', $rows['add_time']);
        $arr[] = $rows;
    }
    return array('pension_apply_list' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

?>