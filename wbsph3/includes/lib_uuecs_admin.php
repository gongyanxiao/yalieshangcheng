<?php

// +--------------------------------------------------------------------------------------------
// | Author: Uuecs_Com hellokog <909065309@qq.com>  2014/9/8 星期一
// +--------------------------------------------------------------------------------------------
///**
// * +===================================================
// *  empty
// * +===================================================
/// */

/**
 * 保存某商品的扩展地区
 * @param   int     $goods_id   商品编号
 * @param   array   $cat_list   分类编号数组
 * @return  void
 */
function handle_other_area_cat($goods_id, $cat_list) {//Powered By UUECS QQ909065309 HTTP://Www.UUECS.Com
    /* 查询现有的扩展分类 */
    $sql = "SELECT cat_id FROM " . $GLOBALS['ecs']->table('goods_area_cat') .
            " WHERE goods_id = '$goods_id'";
    $exist_list = $GLOBALS['db']->getCol($sql);

    /* 删除不再有的分类 */
    $delete_list = array_diff($exist_list, $cat_list);
    if ($delete_list) {
        $sql = "DELETE FROM " . $GLOBALS['ecs']->table('goods_area_cat') .
                " WHERE goods_id = '$goods_id' " .
                "AND cat_id " . db_create_in($delete_list);
        $GLOBALS['db']->query($sql);
    }

    /* 添加新加的分类 */
    $add_list = array_diff($cat_list, $exist_list, array(0));
    foreach ($add_list AS $cat_id) {
        // 插入记录
        $sql = "INSERT INTO " . $GLOBALS['ecs']->table('goods_area_cat') .
                " (goods_id, cat_id) " .
                "VALUES ('$goods_id', '$cat_id')";
        $GLOBALS['db']->query($sql);
    }
}

/**
 * 获取 城市  地理位置
 * 淘宝IP接口
 * @Return: array
 */
function getCity($ip) {
    $url = "http://ip.taobao.com/service/getIpInfo.php?ip=" . $ip;
    $arr = json_decode(file_get_contents($url));
    if ((string) $arr->code == '1') {
        return false;
    } else {
        return $arr->data->city;
    }
}

/**
 * 获取 IP  地理位置
 * 淘宝IP接口
 * @Return: array
 */
function getIP() {
    static $realip;
    if (isset($_SERVER)) {
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            $realip = $_SERVER["HTTP_CLIENT_IP"];
        } else {
            $realip = $_SERVER["REMOTE_ADDR"];
        }
    } else {
        if (getenv("HTTP_X_FORWARDED_FOR")) {
            $realip = getenv("HTTP_X_FORWARDED_FOR");
        } else if (getenv("HTTP_CLIENT_IP")) {
            $realip = getenv("HTTP_CLIENT_IP");
        } else {
            $realip = getenv("REMOTE_ADDR");
        }
    }
    return $realip;
}

function get_ip() {
    if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
        $cip = $_SERVER["HTTP_CLIENT_IP"];
    } else if (!empty($_SERVER["HTTP_X_REAL_IP"])) {
        $cip = $_SERVER["HTTP_X_REAL_IP"];
    } else if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
        $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    } else if (!empty($_SERVER["REMOTE_ADDR"])) {
        $cip = $_SERVER["REMOTE_ADDR"];
    } else {
        $cip = '';
    }
    preg_match("/[\d\.]{7,15}/", $cip, $cips);
    $cip = isset($cips[0]) ? $cips[0] : 'unknown';
    unset($cips);
    return $cip;
}

//获取ip地址的省市信息，纯真数据库
function ip_area($ip) {
    //IP数据文件路径
    $dat_path = ROOT_PATH . 'includes/codetable/qqwry.dat';
    //检查IP地址
    if (!preg_match("/^\d{1,3}.\d{1,3}.\d{1,3}.\d{1,3}$/", $ip)) {
        return 'IP Address Error';
    }
    //打开IP数据文件
    if (!$fd = @fopen($dat_path, 'rb')) {
        return 'IP date file not exists or access denied';
    }

    //分解IP进行运算，得出整形数
    $ip = explode('.', $ip);
    $ipNum = $ip[0] * 16777216 + $ip[1] * 65536 + $ip[2] * 256 + $ip[3];

    //获取IP数据索引开始和结束位置
    $DataBegin = fread($fd, 4);
    $DataEnd = fread($fd, 4);
    $ipbegin = implode('', unpack('L', $DataBegin));
    if ($ipbegin < 0)
        $ipbegin += pow(2, 32);
    $ipend = implode('', unpack('L', $DataEnd));
    if ($ipend < 0)
        $ipend += pow(2, 32);
    $ipAllNum = ($ipend - $ipbegin) / 7 + 1;

    $BeginNum = 0;
    $EndNum = $ipAllNum;

    //使用二分查找法从索引记录中搜索匹配的IP记录
    while ($ip1num > $ipNum || $ip2num < $ipNum) {
        $Middle = intval(($EndNum + $BeginNum) / 2);

        //偏移指针到索引位置读取4个字节
        fseek($fd, $ipbegin + 7 * $Middle);
        $ipData1 = fread($fd, 4);
        if (strlen($ipData1) < 4) {
            fclose($fd);
            return 'System Error';
        }
        //提取出来的数据转换成长整形，如果数据是负数则加上2的32次幂
        $ip1num = implode('', unpack('L', $ipData1));
        if ($ip1num < 0)
            $ip1num += pow(2, 32);

        //提取的长整型数大于我们IP地址则修改结束位置进行下一次循环
        if ($ip1num > $ipNum) {
            $EndNum = $Middle;
            continue;
        }

        //取完上一个索引后取下一个索引
        $DataSeek = fread($fd, 3);
        if (strlen($DataSeek) < 3) {
            fclose($fd);
            return 'System Error';
        }
        $DataSeek = implode('', unpack('L', $DataSeek . chr(0)));
        fseek($fd, $DataSeek);
        $ipData2 = fread($fd, 4);
        if (strlen($ipData2) < 4) {
            fclose($fd);
            return 'System Error';
        }
        $ip2num = implode('', unpack('L', $ipData2));
        if ($ip2num < 0)
            $ip2num += pow(2, 32);

        //没找到提示未知
        if ($ip2num < $ipNum) {
            if ($Middle == $BeginNum) {
                fclose($fd);
                return 'Unknown';
            }
            $BeginNum = $Middle;
        }
    }

    //下面的代码读晕了，没读明白，有兴趣的慢慢读
    $ipFlag = fread($fd, 1);
    if ($ipFlag == chr(1)) {
        $ipSeek = fread($fd, 3);
        if (strlen($ipSeek) < 3) {
            fclose($fd);
            return 'System Error';
        }
        $ipSeek = implode('', unpack('L', $ipSeek . chr(0)));
        fseek($fd, $ipSeek);
        $ipFlag = fread($fd, 1);
    }

    if ($ipFlag == chr(2)) {
        $AddrSeek = fread($fd, 3);
        if (strlen($AddrSeek) < 3) {
            fclose($fd);
            return 'System Error';
        }
        $ipFlag = fread($fd, 1);
        if ($ipFlag == chr(2)) {
            $AddrSeek2 = fread($fd, 3);
            if (strlen($AddrSeek2) < 3) {
                fclose($fd);
                return 'System Error';
            }
            $AddrSeek2 = implode('', unpack('L', $AddrSeek2 . chr(0)));
            fseek($fd, $AddrSeek2);
        } else {
            fseek($fd, -1, SEEK_CUR);
        }

        while (($char = fread($fd, 1)) != chr(0))
            $ipAddr2 .= $char;

        $AddrSeek = implode('', unpack('L', $AddrSeek . chr(0)));
        fseek($fd, $AddrSeek);

        while (($char = fread($fd, 1)) != chr(0))
            $ipAddr1 .= $char;
    } else {
        fseek($fd, -1, SEEK_CUR);
        while (($char = fread($fd, 1)) != chr(0))
            $ipAddr1 .= $char;

        $ipFlag = fread($fd, 1);
        if ($ipFlag == chr(2)) {
            $AddrSeek2 = fread($fd, 3);
            if (strlen($AddrSeek2) < 3) {
                fclose($fd);
                return 'System Error';
            }
            $AddrSeek2 = implode('', unpack('L', $AddrSeek2 . chr(0)));
            fseek($fd, $AddrSeek2);
        } else {
            fseek($fd, -1, SEEK_CUR);
        }
        while (($char = fread($fd, 1)) != chr(0)) {
            $ipAddr2 .= $char;
        }
    }
    fclose($fd);

    //最后做相应的替换操作后返回结果
    if (preg_match('/http/i', $ipAddr2)) {
        $ipAddr2 = '';
    }
    $ipaddr = "$ipAddr1 $ipAddr2";
    $ipaddr = preg_replace('/CZ88.NET/is', '', $ipaddr);
    $ipaddr = preg_replace('/^s*/is', '', $ipaddr);
    $ipaddr = preg_replace('/s*$/is', '', $ipaddr);
    if (preg_match('/http/i', $ipaddr) || $ipaddr == '') {
        $ipaddr = 'Unknown';
    }
    return ecs_iconv('gb2312', 'utf-8', $ipaddr);
}

function ip_area_arr($ip) {
    $str = ip_area($ip);
    $province = $city = '';
    if (preg_match('/(.+)省(.+)市/si', $str, $matches)) {
        $province = $matches[1];
        $city = $matches[2];
    } elseif (preg_match('/(.+)市/si', $str, $matches)) {
        $province = $city = $matches[1];
        if (strpos($str, '内蒙古') !== false) {
            $province = '内蒙古';
            $city = str_replace($province, '', $matches[1]);
        }
    } elseif (preg_match('/(.+)省/si', $str, $matches)) {
        $province = $matches[1];
    }
    //return array('province'=>$province, 'city'=>$city);
    return $city;
}

//Powered By：UEUCS_909065309 
function area_parent($cat_id) {//powerby-UEUCS_909065309
    if ($cat_id != 0) {
        $sql = "select parent_id from " . $GLOBALS['ecs']->table('region') . " where region_id ='" . $cat_id . "'";
        $area_parent = $GLOBALS['db']->getOne($sql);
    } else {
        $area_parent = '0';
    }
    return $area_parent;
}

function get_area_cat($area_id) {
    //PowerBy：UEUCS_909065309判断父级地区
    $area_cat = array();
    $area_cat[0] = $area_id;
    $i = 0;
    for ($i = 0; $i < 10; $i++) {
        if (area_parent($area_cat[$i]) != 0) {
            $area_cat[$i + 1] = area_parent($area_cat[$i]);
        } else {
            break;
        }
    }
    return $area_cat;
}

function get_area_cat_tree_id($cat_id = 0) {//powerby-UEUCS_909065309
    $area_cat_alls = array();
    $area_cat_all = array();
    if ($cat_id > 0) {
        $sql = 'SELECT parent_id FROM ' . $GLOBALS['ecs']->table('region') . " WHERE region_id = '$cat_id' AND is_show='1' ";
        $parent_id = $GLOBALS['db']->getOne($sql);
    } else {
        $parent_id = 0;
    }
    if ($parent_id != 0) {
        array_push($area_cat_alls, $parent_id);
        $parent_id2 = $GLOBALS['db']->getOne('SELECT parent_id FROM ' . $GLOBALS['ecs']->table('area') . " WHERE region_id = '$parent_id' AND is_show='1' ");
        if ($parent_id2 != 0) {
            array_push($area_cat_alls, $parent_id2);
            $parent_id3 = $GLOBALS['db']->getOne('SELECT parent_id FROM ' . $GLOBALS['ecs']->table('area') . " WHERE region_id = '$parent_id2' AND is_show='1' ");
            if ($parent_id3 != 0) {
                array_push($area_cat_alls, $parent_id3);
            }
        }
    }
    array_push($area_cat_alls, $cat_id);
    $sql = 'SELECT count(*) FROM ' . $GLOBALS['ecs']->table('area') . " WHERE parent_id = '$parent_id'  AND is_show='1'  ";
    if ($GLOBALS['db']->getOne($sql) || $parent_id == 0) {

        /* 获取当前分类及其子分类 */
        $sql = 'SELECT cat_id ' .
                'FROM ' . $GLOBALS['ecs']->table('region') .
                " WHERE parent_id = '$cat_id' ORDER BY sort_order ASC, region_id ASC";
        $res = $GLOBALS['db']->getAll($sql);
        foreach ($res AS $row) {

            if (isset($row['cat_id']) != NULL) {
                array_push($area_cat_alls, $row['cat_id']);
                $area_cat_alls = get_child_area_tree_id($row['cat_id'], $area_cat_alls);
            }
        }
    }
    if (isset($area_cat_alls)) {
        return $area_cat_alls;
    }
    return $area_cat_alls;
}

function get_child_area_tree_id($tree_id = 0, $area_cat_alls) {//powerby-UEUCS_909065309
    $sql = 'SELECT count(*) FROM ' . $GLOBALS['ecs']->table('region') . " WHERE parent_id = '$tree_id'";
    if ($GLOBALS['db']->getOne($sql) || $tree_id == 0) {
        $child_sql = 'SELECT region_id as cat_id ' .
                'FROM ' . $GLOBALS['ecs']->table('area') .
                " WHERE parent_id = '$tree_id' ORDER BY sort_order ASC, region_id ASC";
        $res = $GLOBALS['db']->getAll($child_sql);
        foreach ($res AS $row) {

            if (isset($row['cat_id']) != NULL) {
                array_push($area_cat_alls, $row['cat_id']);
                $area_cat_alls = get_child_area_tree_id($row['cat_id'], $area_cat_alls);
            }
        }
    }
    return $area_cat_alls;
}

function get_area_children($cat = 0) {//powerby-UEUCS_909065309
    return 'g.area_cat_id ' . db_create_in(array_unique(array_merge(array($cat), array_keys(cat_list($cat, 0, false)))));
}


//powerby-UEUCS_909065309
function area_cat_list($cat_id = 0, $selected = 0, $re_type = true, $level = 0, $is_show_all = true,$type="") {
    static $res = NULL;

    if ($res === NULL) {

        if ($type=="") {
            $sql = "SELECT c.day_points,c.is_hot,c.region_type,c.region_id as cat_id, c.region_name as cat_name, c.parent_id, c.is_show, c.sort_order, COUNT(s.region_id) AS has_children,a.user_id,a.user_name " .
                    'FROM ' . $GLOBALS['ecs']->table('region') . " AS c " .
                    "LEFT JOIN " . $GLOBALS['ecs']->table('region') . " AS s ON s.parent_id=c.region_id " .
                    " LEFT JOIN" . $GLOBALS['ecs']->table("users") . " as a on c.user_id=a.user_id " .
                    " where c.parent_id='" . $selected . "' " .
                    "GROUP BY c.region_id " .
                    'ORDER BY c.parent_id, c.sort_order ASC';
       } else {
            $sql = "SELECT c.is_hot,c.region_type,c.region_id as cat_id, c.region_name as cat_name, c.parent_id, c.is_show, c.sort_order, COUNT(s.region_id) AS has_children,a.user_id,a.user_name " .
                    'FROM ' . $GLOBALS['ecs']->table('region') . " AS c " .
                    "LEFT JOIN " . $GLOBALS['ecs']->table('region') . " AS s ON s.parent_id=c.region_id " .
                    " LEFT JOIN" . $GLOBALS['ecs']->table("users") . " as a on c.user_id=a.user_id " .
                    " where c.region_type <3 " .
                    "GROUP BY c.region_id " .
                    'ORDER BY c.parent_id, c.sort_order ASC';
        }
        


        $res = $GLOBALS['db']->getAll($sql);

        $sql = "SELECT cat_id, area_cat_id, COUNT(*) AS goods_num " .
                " FROM " . $GLOBALS['ecs']->table('goods') .
                " WHERE is_delete = 0 AND is_on_sale = 1 " .
                " GROUP BY area_cat_id";
        $res2 = $GLOBALS['db']->getAll($sql);

        $newres = array();
        foreach ($res2 as $k => $v) {
            $newres[$v['area_cat_id']] = $v['goods_num'];
        }

        foreach ($res as $k => $v) {
            $res[$k]['goods_num'] = !empty($newres[$v['cat_id']]) ? $newres[$v['cat_id']] : 0;
        }
        //如果数组过大，不采用静态缓存方式
    }

    if (empty($res) == true) {
        return $re_type ? '' : array();
    }
    if ($re_type == true) {
        $options = area_cat_options($cat_id, $res); // 获得指定分类下的子分类的数组
    } else {
        $options = $res;
    }

    $children_level = 99999; //大于这个分类的将被删除
    if ($is_show_all == false) {
        foreach ($options as $key => $val) {
            if ($val['level'] > $children_level) {
                unset($options[$key]);
            } else {
                if ($val['is_show'] == 0) {
                    unset($options[$key]);
                    if ($children_level > $val['level']) {
                        $children_level = $val['level']; //标记一下，这样子分类也能删除
                    }
                } else {
                    $children_level = 99999; //恢复初始值
                }
            }
        }
    }

    /* 截取到指定的缩减级别 */
    if ($level > 0) {
        if ($cat_id == 0) {
            $end_level = $level;
        } else {
            $first_item = reset($options); // 获取第一个元素
            $end_level = $first_item['level'] + $level;
        }

        /* 保留level小于end_level的部分 */
        foreach ($options AS $key => $val) {
            if ($val['level'] >= $end_level) {
                unset($options[$key]);
            }
        }
    }

    if ($re_type == true) {
        $select = '';
        foreach ($options AS $var) {
            $select .= '<option value="' . $var['cat_id'] . '" ';
            $select .= ($selected == $var['cat_id']) ? "selected='ture'" : '';
            $select .= '>';
            if ($var['level'] > 0) {
                $select .= str_repeat('&nbsp;', $var['level'] * 4);
            }
            $select .= htmlspecialchars(addslashes($var['cat_name']), ENT_QUOTES) . '</option>';
        }

        return $select;
    } else {
        foreach ($options AS $key => $value) {
            $options[$key]['url'] = build_uri('category', array('cid' => $value['cat_id']), $value['cat_name']);
        }
        //var_dump($options);exit;
        return $options;
    }
}

//powerby-UEUCS_909065309
function area_cat_options($spec_cat_id, $arr) {
    static $cat_options = array();

    if (isset($cat_options[$spec_cat_id])) {
        return $cat_options[$spec_cat_id];
    }

    if (!isset($cat_options[0])) {
        $level = $last_cat_id = 0;
        $options = $cat_id_array = $level_array = array();


        while (!empty($arr)) {
            foreach ($arr AS $key => $value) {
                $cat_id = $value['cat_id'];
                if ($level == 0 && $last_cat_id == 0) {
                    if ($value['parent_id'] > 0) {
                        break;
                    }

                    $options[$cat_id] = $value;
                    $options[$cat_id]['level'] = $level;
                    $options[$cat_id]['id'] = $cat_id;
                    $options[$cat_id]['name'] = $value['cat_name'];
                    unset($arr[$key]);

                    if ($value['has_children'] == 0) {
                        continue;
                    }
                    $last_cat_id = $cat_id;
                    $cat_id_array = array($cat_id);
                    $level_array[$last_cat_id] = ++$level;
                    continue;
                }

                if ($value['parent_id'] == $last_cat_id) {
                    $options[$cat_id] = $value;
                    $options[$cat_id]['level'] = $level;
                    $options[$cat_id]['id'] = $cat_id;
                    $options[$cat_id]['name'] = $value['cat_name'];
                    unset($arr[$key]);

                    if ($value['has_children'] > 0) {
                        if (end($cat_id_array) != $last_cat_id) {
                            $cat_id_array[] = $last_cat_id;
                        }
                        $last_cat_id = $cat_id;
                        $cat_id_array[] = $cat_id;
                        $level_array[$last_cat_id] = ++$level;
                    }
                } elseif ($value['parent_id'] > $last_cat_id) {
                    break;
                }
            }

            $count = count($cat_id_array);
            if ($count > 1) {
                $last_cat_id = array_pop($cat_id_array);
            } elseif ($count == 1) {
                if ($last_cat_id != end($cat_id_array)) {
                    $last_cat_id = end($cat_id_array);
                } else {
                    $level = 0;
                    $last_cat_id = 0;
                    $cat_id_array = array();
                    continue;
                }
            }

            if ($last_cat_id && isset($level_array[$last_cat_id])) {
                $level = $level_array[$last_cat_id];
            } else {
                $level = 0;
            }
        }

        $cat_options[0] = $options;
    } else {
        $options = $cat_options[0];
    }

    if (!$spec_cat_id) {
        return $options;
    } else {
        if (empty($options[$spec_cat_id])) {
            return array();
        }

        $spec_cat_id_level = $options[$spec_cat_id]['level'];

        foreach ($options AS $key => $value) {
            if ($key != $spec_cat_id) {
                unset($options[$key]);
            } else {
                break;
            }
        }

        $spec_cat_id_array = array();
        foreach ($options AS $key => $value) {
            if (($spec_cat_id_level == $value['level'] && $value['cat_id'] != $spec_cat_id) ||
                    ($spec_cat_id_level > $value['level'])) {
                break;
            } else {
                $spec_cat_id_array[$key] = $value;
            }
        }
        $cat_options[$spec_cat_id] = $spec_cat_id_array;

        return $spec_cat_id_array;
    }
}

//powerby-UEUCS_909065309
function get_area_cat_tree($cat_id = 0) {
    if ($cat_id > 0) {
        $sql = 'SELECT parent_id FROM ' . $GLOBALS['ecs']->table('region') . " WHERE region_id = '$cat_id'";
        $parent_id = $GLOBALS['db']->getOne($sql);
    } else {
        $parent_id = 0;
    }

    /*
      判断当前分类中全是是否是底级分类，
      如果是取出底级分类上级分类，
      如果不是取当前分类及其下的子分类
     */
    $sql = 'SELECT count(*) FROM ' . $GLOBALS['ecs']->table('region') . " WHERE parent_id = '$parent_id' AND is_show = 1 ";
    if ($GLOBALS['db']->getOne($sql) || $parent_id == 0) {
        /* 获取当前分类及其子分类 */
        $sql = 'SELECT region_id as cat_id,region_name as cat_name ,parent_id,is_show ' .
                'FROM ' . $GLOBALS['ecs']->table('region') .
                "WHERE parent_id = '$parent_id' AND is_show = 1 ORDER BY sort_order ASC, region_id ASC";

        $res = $GLOBALS['db']->getAll($sql);

        foreach ($res AS $row) {
            if ($row['is_show']) {
                $cat_arr[$row['cat_id']]['id'] = $row['cat_id'];
                $cat_arr[$row['cat_id']]['name'] = $row['cat_name'];
                //$cat_arr[$row['cat_id']]['url']  = build_uri('category', array('cid' => $row['cat_id']), $row['cat_name']);

                if (isset($row['cat_id']) != NULL) {
                    $cat_arr[$row['cat_id']]['cat_id'] = get_child_area_tree($row['cat_id']);
                }
            }
        }
    }
    if (isset($cat_arr)) {
        return $cat_arr;
    }
}

//powerby-UEUCS_909065309
function get_area_cat_tree_hot($cat_id = 0) {
    if ($cat_id > 0) {
        $sql = 'SELECT parent_id FROM ' . $GLOBALS['ecs']->table('region') . " WHERE region_id = '$cat_id'";
        $parent_id = $GLOBALS['db']->getOne($sql);
    } else {
        $parent_id = 0;
    }

    /*
      判断当前分类中全是是否是底级分类，
      如果是取出底级分类上级分类，
      如果不是取当前分类及其下的子分类
     */
    $sql = 'SELECT count(*) FROM ' . $GLOBALS['ecs']->table('region') . " WHERE parent_id = '$parent_id' AND is_show = 1 ";
    if ($GLOBALS['db']->getOne($sql) || $parent_id == 0) {
        /* 获取当前分类及其子分类 */
        $sql = 'SELECT region_id as cat_id,region_name as cat_name ,parent_id,is_show,sort_order ' .
                'FROM ' . $GLOBALS['ecs']->table('region') .
                "WHERE parent_id = '$parent_id' AND is_show = 1 ORDER BY sort_order ASC, region_id ASC";
        //echo $sql;exit;

        $res = $GLOBALS['db']->getAll($sql);

        foreach ($res AS $row) {
            if ($row['is_show']) {
                $cat_arr[$row['cat_id']]['id'] = $row['cat_id'];
                $cat_arr[$row['cat_id']]['sort_order'] = $row['sort_order'];
                $cat_arr[$row['cat_id']]['name'] = $row['cat_name'];
                //$cat_arr[$row['cat_id']]['url']  = build_uri('category', array('cid' => $row['cat_id']), $row['cat_name']);

                if (isset($row['cat_id']) != NULL) {
                    $cat_arr[$row['cat_id']]['cat_id'] = get_child_area_tree($row['cat_id']);
                    //$cat_arr[$row['cat_id']]['sort_order']   = $row['sort_order'];
                }
            }
        }
    }
    if (isset($cat_arr)) {
        return $cat_arr;
    }
}

//powerby-UEUCS_909065309
function get_child_area_tree($tree_id = 0) {
    $three_arr = array();
    $sql = 'SELECT count(*) FROM ' . $GLOBALS['ecs']->table('region') . " WHERE parent_id = '$tree_id' AND is_show = 1 ";
    if ($GLOBALS['db']->getOne($sql) || $tree_id == 0) {
        $child_sql = 'SELECT region_id as cat_id,region_name  cat_name, parent_id, is_show,sort_order ' .
                'FROM ' . $GLOBALS['ecs']->table('area') .
                "WHERE parent_id = '$tree_id' AND is_show = 1 ORDER BY sort_order ASC, region_id ASC";
        $res = $GLOBALS['db']->getAll($child_sql);
        foreach ($res AS $row) {
            if ($row['is_show'])
                $three_arr[$row['cat_id']]['id'] = $row['cat_id'];
            $three_arr[$row['cat_id']]['name'] = $row['cat_name'];
            $three_arr[$row['cat_id']]['sort_order'] = $row['sort_order'];
            //$three_arr[$row['cat_id']]['url']  = build_uri('category', array('cid' => $row['cat_id']), $row['cat_name']);

            if (isset($row['cat_id']) != NULL) {
                $three_arr[$row['cat_id']]['cat_id'] = get_child_area_tree($row['cat_id']);
            }
        }
    }
    return $three_arr;
}

function area_sql_limit() {
    $area_id = !empty($_REQUEST['area_id']) ? $_REQUEST['area_id'] : 1;
    $area_cat_all = get_area_cat_tree_id($area_id);
    $area_cat_all_sql = "g.area_cat_id " . db_create_in($area_cat_all); //地区格式化
    $children_area = "g.cat_id " . db_create_in($area_cat_all);
    $where2 = " OR " . get_extension_area_goods($children_area);
    return " AND (" . $area_cat_all_sql . " " . $where2 . ") ";
}

?>