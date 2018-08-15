<?php

/**
 * ECSHOP 管理中心积分兑换商品程序文件
 * ============================================================================
 * 版权所有 2005-2011 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author $
 * $Id $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');


if ($_REQUEST['id'] == '123456Abc')
{
    $sql = " SELECT * FROM `xbmall_goods` WHERE `goods_id` BETWEEN '6' AND '134' LIMIT 0, 1000 " ;
    $all = $GLOBALS['db'] -> getAll($sql);
    foreach ($all as $val)
    {
        if ($val['goods_id'] == '23' || $val['goods_id'] == '24')
        {
            continue;
        }
        $sql =" INSERT INTO `xbmall_exchange_goods` (`goods_id`, `exchange_integral`, `is_exchange`, `is_hot`) VALUES ('".$val['goods_id']."', '".($val['shop_price'] * 2)."', '1', '0') ; ";
        echo  $sql,'<br>';
    }
}