<?php

define('IN_ECS', true);

$cache_id = sprintf('%X', crc32($_SESSION['user_rank'] . '-' . $_CFG['lang'] . '-' . $_GET['suppId']));
if (!$smarty->is_cached('mall.dwt', $cache_id)) {
    assign_template();
    assign_template_supplier();
    $position = assign_ur_here();
    $smarty->assign('page_title', $position['title']);    // 页面标题
    $smarty->assign("type", "content");
    $smarty->assign('suppinfo', $suppinfo);
}

$smarty->display('mall.dwt', $cache_id);
?>