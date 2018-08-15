<?php

if (!defined('IN_ECS')) {
    die('Hacking attempt');
}


$modules['tk_ji_ben_xin_xi']['tk_ji_ben_xin_xi_base']= 'index.php?act=main'; //基本信息
$modules['tk_ji_ben_xin_xi']['tk_ji_ben_xin_xi_zi_liao']= 'tk_user.php?act=zi_liao'; //资料

$modules['tk_chan_pin']['tk_chan_pin_list']= 'goods.php?act=list'; // 产品列表
$modules['tk_chan_pin']['tk_chan_pin_buy_list']= 'role_offline_man.php?act=list'; // 产品购买列表
$modules['tk_chan_pin']['tk_chan_pin_xiao_shou_list']= 'tk_xiao_shou.php?act=list&type=0'; // 产品销售记录
$modules['tk_chan_pin']['tk_chan_pin_buy_list']= 'tk_xiao_shou.php?act=list&type=1'; // 产品购买记录

$modules['tk_chan_pin']['tk_chan_pin_xiao_shou_ti_xian_list']= 'tk_xiao_shou.php?act=ti_xian_list'; // 产品销售提现记录

$modules['tk_li_cai']['tk_li_cai_list']= 'tk_li_cai_chan_pin.php?act=list'; // 理财产品列表
$modules['tk_li_cai']['tk_li_cai_buy_list']= 'role_offline_man.php?act=list'; // 理财产品购买列表


 
 
 
?>
