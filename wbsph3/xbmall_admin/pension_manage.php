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
define ( 'IN_ECS', true );

require (dirname ( __FILE__ ) . '/includes/init.php');
include_once (ROOT_PATH . '/includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);

$pension_infos = new exchange ( $ecs->table ( 'pension_infos' ), $db, 'id', 'name' );

/* ------------------------------------------------------ */
// -- 办事处列表
/* ------------------------------------------------------ */
if ($_REQUEST ['act'] == 'list') {
	admin_priv ( '1_pension_manage_list' );
	$smarty->assign ( 'ur_here', $_LANG ['pension_manage_list'] );
	$smarty->assign ( 'action_link', array (
			'text' => $_LANG ['add_pension_manage'],
			'href' => 'pension_manage.php?act=add'
	) );
	$smarty->assign ( 'full_page', 1 );
	
	$agency_list = get_pension_manage_list ();
	$smarty->assign ( 'pension_infos_list', $agency_list ['pension_infos'] );
	$smarty->assign ( 'filter', $agency_list ['filter'] );
	$smarty->assign ( 'record_count', $agency_list ['record_count'] );
	$smarty->assign ( 'page_count', $agency_list ['page_count'] );
	
	/* 排序标记 */
	$sort_flag = sort_flag ( $agency_list ['filter'] );
	$smarty->assign ( $sort_flag ['tag'], $sort_flag ['img'] );
	
	assign_query_info ();
	$smarty->display ( 'pension_infos_list.htm' );
}
/* ------------------------------------------------------ */
// -- 排序、分页、查询
/* ------------------------------------------------------ */
elseif ($_REQUEST ['act'] == 'query') {
	check_authz_json ( '1_pension_manage_list' );
	$agency_list = get_pension_manage_list ();
	$smarty->assign ( 'pension_infos_list', $agency_list ['pension_infos'] );
	$smarty->assign ( 'filter', $agency_list ['filter'] );
	$smarty->assign ( 'record_count', $agency_list ['record_count'] );
	$smarty->assign ( 'page_count', $agency_list ['page_count'] );
	
	/* 排序标记 */
	$sort_flag = sort_flag ( $agency_list ['filter'] );
	$smarty->assign ( $sort_flag ['tag'], $sort_flag ['img'] );
	
	make_json_result ( $smarty->fetch ( 'pension_infos_list.htm' ), '', array (
			'filter' => $agency_list ['filter'],
			'page_count' => $agency_list ['page_count']
	) );
}
/* ------------------------------------------------------ */
// -- 删除办事处
/* ------------------------------------------------------ */
elseif ($_REQUEST ['act'] == 'toggle_remove') {
	check_authz_json ( '1_pension_manage' );
	$id = intval ( $_POST ['id'] );
	$pension_status = intval ( $_POST ['val'] );
	$sql = "UPDATE " . $ecs->table ( "pension_infos" ) . " SET `pension_status` ='$pension_status' WHERE id = '$id'";
	if ($GLOBALS ['db']->query ( $sql )) {
		/* 记日志 */
		admin_log ( $name, 'toggle_remove', 'pension_infos' );
		clear_cache_files (); // 清除缓存
		make_json_result ( $val );
	} else {
		make_json_error ( $db->error () );
	}
}
/* ------------------------------------------------------ */
// -- 添加、编辑办事处
/* ------------------------------------------------------ */
elseif ($_REQUEST ['act'] == 'add' || $_REQUEST ['act'] == 'edit') {
	/* 检查权限 */
	admin_priv ( '1_pension_manage' );
	
	/* 是否添加 */
	$is_add = $_REQUEST ['act'] == 'add';
	$smarty->assign ( 'form_action', $is_add ? 'insert' : 'update' );
	
	/* 初始化、取得办事处信息 */
	if ($is_add) {
		$agency = array (
				'id' => 0,
				'pension_name' => '',
				'pension_desc' => '',
				'picture' => '',
				'money' => ''
		);
		/* 创建 html editor */
		create_html_editor ( 'FCKeditor1' );
	} else {
		if (empty ( $_GET ['id'] )) {
			sys_msg ( 'invalid param' );
		}
		
		$id = $_GET ['id'];
		$sql = "SELECT * FROM " . $ecs->table ( 'pension_infos' ) . " WHERE id = '$id'";
		$agency = $db->getRow ( $sql );
		if (empty ( $agency )) {
			sys_msg ( 'pension_infos does not exist' );
		}
		/* 创建 html editor,赋值 */
		create_html_editor('FCKeditor1',htmlspecialchars($agency['pension_desc']));
	}
	
	/* 显示模板 */
	if ($is_add) {
		$smarty->assign ( 'ur_here', $_LANG ['add_pension_manage'] );
	} else {
		
		$smarty->assign ( 'ur_here', $_LANG ['edit_pension_manage'] );
	}
	if ($is_add) {
		$href = 'pension_manage.php?act=list';
	} else {
		$href = 'pension_manage.php?act=list&' . list_link_postfix ();
	}
	$smarty->assign ( 'action_link', array (
			'href' => $href,
			'text' => $_LANG ['pension_manage_list']
	) );
	$smarty->assign ( 'agency', $agency );
	assign_query_info ();
	$smarty->display ( 'pension_info.htm' );
}
/* ------------------------------------------------------ */
// -- 提交添加、编辑
/* ------------------------------------------------------ */
elseif ($_REQUEST ['act'] == 'insert' || $_REQUEST ['act'] == 'update') {
	/* 检查权限 */
	admin_priv ( '1_pension_manage' );
	
	/* 是否添加 */
	$is_add = $_REQUEST ['act'] == 'insert';
	
	/* 提交值 */
	$agency = array (
			'id' => intval ( $_POST ['id'] ),
			'pension_name' => sub_str ( $_POST ['pension_name'], 255, false ),
			'pension_desc' => $_POST ['pension_desc'],
			'money' => doubleval ( $_POST ['money'] )
	);
	/* 判断名称是否重复 */
	if (! $pension_infos->is_only ( 'pension_name', $agency ['pension_name'], $agency ['id'] )) {
		sys_msg ( $_LANG ['pension_name_exist'] );
	}
	
	$content = !empty($_POST['FCKeditor1']) ? $_POST['FCKeditor1'] : '';
	$agency ['pension_desc'] =$content;//保存富文本框内容
	
	if (isset($_FILES['picture']) && $_FILES['picture']['tmp_name'] != '') {
		$picture= $image->upload_image($_FILES['picture']);
		
		if ($picture=== false) {
			sys_msg($image->error_msg(), 1, array(), false);
		}
	}
	if($picture!=''){
		$agency['picture'] = $picture;//保存图片路径
	}
	
	/* 保存信息 */
	if ($is_add) {
		$agency ['add_time'] = gmtime ();
		$GLOBALS ['db']->autoExecute ( $ecs->table ( 'pension_infos' ), $agency, 'INSERT' );
		$agency ['id'] = $db->insert_id ();
	} else {
		$db->autoExecute ( $ecs->table ( 'pension_infos' ), $agency, 'UPDATE', "id = '$agency[id]'" );
	}
	
	/* 记日志 */
	if ($is_add) {
		admin_log ( $agency ['name'], 'add', '1_pension_manage' );
	} else {
		admin_log ( $agency ['name'], 'edit', '1_pension_manage' );
	}
	
	/* 清除缓存 */
	clear_cache_files ();
	
	/* 提示信息 */
	if ($is_add) {
		$links = array (
				array (
						'href' => 'pension_manage.php?act=add',
						'text' => $_LANG ['continue_add_pension']
				),
				array (
						'href' => 'pension_manage.php?act=list',
						'text' => $_LANG ['back_pension_list']
				)
		);
		sys_msg ( $_LANG ['add_pension_ok'], 0, $links );
	} else {
		$links = array (
				array (
						'href' => 'pension_manage.php?act=list&' . list_link_postfix (),
						'text' => $_LANG ['back_pension_list']
				)
		);
		sys_msg ( $_LANG ['edit_pension_ok'], 0, $links );
	}
}

/**
 * 取得办事处列表
 *
 * @return array
 */
function get_pension_manage_list() {
	$result = get_filter ();
	if ($result === false) {
		/* 初始化分页参数 */
		$filter = array ();
		$filter ['sort_by'] = empty ( $_REQUEST ['sort_by'] ) ? 'id' : trim ( $_REQUEST ['sort_by'] );
		$filter ['sort_order'] = empty ( $_REQUEST ['sort_order'] ) ? 'DESC' : trim ( $_REQUEST ['sort_order'] );
		
		/* 查询记录总数，计算分页数 */
		$sql = "SELECT COUNT(*) FROM " . $GLOBALS ['ecs']->table ( 'pension_infos' );
		$filter ['record_count'] = $GLOBALS ['db']->getOne ( $sql );
		$filter = page_and_size ( $filter );
		
		/* 查询记录 */
		$sql = "SELECT * FROM " . $GLOBALS ['ecs']->table ( 'pension_infos' ) . " ORDER BY $filter[sort_by] $filter[sort_order]";
		
		set_filter ( $filter, $sql );
	} else {
		$sql = $result ['sql'];
		$filter = $result ['filter'];
	}
	$res = $GLOBALS ['db']->selectLimit ( $sql, $filter ['page_size'], $filter ['start'] );
	
	$arr = array ();
	while ( $rows = $GLOBALS ['db']->fetchRow ( $res ) ) {
		$rows ['add_time'] = local_date ( 'Y-m-d H:i:s', $rows ['add_time'] );
		$arr [] = $rows;
	}
	
	return array (
			'pension_infos' => $arr,
			'filter' => $filter,
			'page_count' => $filter ['page_count'],
			'record_count' => $filter ['record_count']
	);
}

?>