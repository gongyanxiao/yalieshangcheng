<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define('IN_ECS', true);
date_default_timezone_set ('Asia/Shanghai');
$logfile = "update_log.log";
include 'config.php';
include 'init.php';
include 'emoji.php';

set_time_limit(0);
$db1 = new cls_mysql($db_host1, $db_user1, $db_pass1, $db_name1,'UTF8');
//查找会员信息
$sql = "SELECT m.id,m.uid,m.mobile,m.pwd,m.agentid,m.openid,m.avatar,m.birthday,m.diymemberdata,m.credit2,m.createtime,m.province,m.city,m.area,m.avatar FROM ims_sz_yi_member m WHERE m.uniacid = 4 ";
$members = $db1->getAll($sql);

//1、以模板的会员表为主表，查询会员数据，资金数据根据uid判断为空，使用微擎的，其他同步信息：手机号、密码（md5随机生成）、推荐人、注册时间、真实姓名（扩展字段）、身份证号、银行、开户行、开户行行号、银行卡号、用户资金（需要计算）
//2、会员微信数据表，需要注意会员的uid
//3、会员充值、提现记录
//4、商家报单记录，需要同步设置会员的报单权限
//5、会员收货地址
//6、订单记录
$db = new cls_mysql($db_host, $db_user, $db_pass, $db_name,'UTF8');
$isok = true;
$count = 0;
try {
    $db->startTrans();
    foreach ($members as $memberKey => $memberValue) {
        $userid = $memberValue['id']; //用户id,肯定不等于0
        $uid = $memberValue['uid']; //用户id,肯定不等于0
        $openid = $memberValue['openid']; //openid


        $headImage = $memberValue['avatar'];
        if ($headImage == "http://weixin.jiajialept.com/addons/sz_yi/template/mobile/default/static/images/photo-mr.jpg") {
            $headImage = "";
        }
        $userData = array("mobile_phone" => $memberValue['mobile'], 'user_name' => $memberValue["mobile"], 'parent_id' => $memberValue['agentid'], "reg_time" => $memberValue['createtime'], 'headimg' => $headImage);
        $wxData = array("fake_id" => $openid, "createtime" => $memberValue['createtime'], "isfollow" => 1);
        //密码
        $ec_salt = rand(1, 9999);
        $userData["ec_salt"] = $ec_salt;
        //$userData["salt"] = $ec_salt;
        $userData["password"] = md5($memberValue['pwd'] . $ec_salt);
        //真实姓名、身份证号、银行、开户行、开户行行号、银行卡号
        if (!empty($memberValue['diymemberdata'])) {
//             'diyxingming' => string '刘斌科' (length=9)
//  'diyshoujihao' => string '18660578713' (length=11)
//  'diyshenfenzheng' => string '379009197609092218' (length=18)
//  'diyyinxing' => string '中国建设银行' (length=18)
//  'diykaihuxing' => string '山东省烟台市莱山区支行' (length=33)
//  'diykaihuxingxinghao' => string '123456' (length=6)
//  'diyyinxingkahao' => string '6227002191687109304' (length=19)
            $straa = mb_unserialize($memberValue['diymemberdata']);
            $userData['real_name'] = $straa["diyxingming"];
            $userData['card'] = $straa["diyshenfenzheng"];
            $userData['bank'] = $straa["diyyinxing"];
            $userData['bank_kh'] = $straa["diykaihuxing"];
            $userData['bank_num'] = $straa["diyyinxingkahao"];
        }
        if ($uid * 1 == 0) {
            $userData['pay_points'] = $memberValue["credit2"] * 1;
        }
        //计算会员资金数据
        //剩余积分
        if ($uid * 1 > 0) {//推荐后台添加，需要去微擎后台添加
            $sql = "select mobile,nickname,avatar,nationality,resideprovince,residecity,credit2 from ims_mc_members where uid='" . $uid . "'";
            $memberData = $db1->getRow($sql);
            if (empty($userData['mobile_phone'])) {
                $userData['mobile_phone'] = $memberData["mobile"];
                $userData['user_name'] = $memberData["mobile"];
            }
            $userData['pay_points'] = $memberData["credit2"] * 1;
//            $nickname = preg_replace('/xE0[x80-x9F][x80-xBF]|xED[xA0-xBF][x80-xBF]/S', '?', $memberData["nickname"]);
//            $nickname = preg_replace('/\xEE[\x80-\xBF][\x80-\xBF]|\xEF[\x81-\x83][\x80-\xBF]/', '', $nickname);
//            $nickname = preg_replace('/\xEE[\x80-\xBF][\x80-\xBF]|\xEF[\x81-\x83][\x80-\xBF]/', '', $nickname);
            $nickname = emoji_docomo_to_unified($memberData["nickname"]);   # DoCoMo devices
            $nickname = emoji_kddi_to_unified($nickname);     # KDDI & Au devices
            $nickname = emoji_softbank_to_unified($nickname); # Softbank & pre-iOS6 Apple devices
            $nickname = emoji_google_to_unified($nickname);   # Google Android devices
            if (strpos($nickname, "尐珥朶") !== false) {
                $wxData["nickname"] = ""; //filter
            } else {
                $wxData["nickname"] = ($nickname); //filter
            }
            $wxData["headimgurl"] = $memberData["avatar"];
            $wxData["country"] = $memberData["nationality"];
            $wxData["province"] = $memberData["resideprovince"];
            $wxData["city"] = $memberData["residecity"];
        }//if ($uid * 1 == 0) {
        if (empty($userData['mobile_phone'])) {
            error_log("没有手机号数据：" . json_encode($memberValue) . "\r\n", 3, $logfile);
            continue;
        }
        //累计消费
        $sql = "SELECT ifnull(sum(goodsprice), 0) FROM ims_sz_yi_order WHERE `status` = 3 AND `openid` ='" . $openid . "' limit 1";
        $consum_money = $db1->getOne($sql);
        $userData["consum_money"] = $consum_money * 1;
        //成功提现积分
        $sql = "select ifnull(sum(money),0) from ims_sz_yi_member_log where `type`=1 and `status` = 1  and `openid` ='" . $openid . "' limit 1";
        $tx_points = $db1->getOne($sql);
        $userData["tx_points"] = $tx_points * 1;
        //累计赠送积分
        $sql = "select  ifnull(sum(return_money),0) from ims_sz_yi_return  where mid='" . $userid . "'  limit 1";
        $give_points = $db1->getOne($sql);
        $userData["give_points"] = $give_points * 1;
        $userData["day_points"] = $give_points * 1;
        //当前赠送权
        $sql = "select count(id) from ims_sz_yi_return where mid='" . $userid . "' and status = 0 limit 1";
        $zsq = $db1->getOne($sql);
        $userData["zsq"] = $zsq * 1;
        //历史赠送权
        $sql = "select count(id) from ims_sz_yi_return where mid='" . $userid . "'  limit 1";
        $history_zsq = $db1->getOne($sql);
        $userData["history_zsq"] = $history_zsq * 1;

        $sql = "select count(*) from ecs_users where mobile_phone='" . $userData['mobile_phone'] . "' or user_name='" . $userData['mobile_phone'] . "'";
        $memberCount = $db->getOne($sql);
        if ($memberCount > 0) {
            error_log("手机号数据重复数据：" . json_encode($memberValue) . "\r\n", 3, $logfile);
            continue;
        }
        $res = $db->autoExecute("ecs_users", $userData, "INSERT");
        if ($res) {
            $user_real_id = $db->insert_id();
            if ($userData['pay_points'] * 1 > 0 || $userData["consum_money"] * 1 > 0 || $userData["give_points"] * 1 > 0 || $userData["tx_points"] * 1 > 0 || $userData["history_zsq"] * 1 > 0 || $userData["zsq"] * 1 > 0) {
                $sql = "INSERT INTO ecs_account_log (user_id,user_money,frozen_money,rank_points,pay_points,change_time,change_desc,change_type,consum_money,give_points,tx_points,history_zsq,zsq,frozen_points,day_points) " .
                        "   VALUES('$user_real_id',0,0,0,'" . $userData['pay_points'] . "','" . (time()) . "','系统切换，数据同步',2,'" . $userData["consum_money"] . "','" . $userData["give_points"] . "','" . $userData["tx_points"] . "','" . $userData["history_zsq"] . "','" . $userData["zsq"] . "','" . $userData["frozen_points"] . "','" . $userData["give_points"] . "')";

                $resLog = $db->query($sql);
            } else {
                $resLog = true;
            }
            $wxData['ecuid'] = $user_real_id;
            //微信数据
//            if ($wxData["fake_id"] == "oz-WZxGns0n99K5hT6a4mmy25Su0") {
//                $wxRes = true;
//            } else {
//                $wxRes = $db->autoExecute("ecs_weixin_user", $wxData, "INSERT");
//            }
            $wxRes = true;
            //会员充值提现记录
            $sql = "SELECT type as process_type,createtime as add_time,`status` as is_paid,money as amount FROM `ims_sz_yi_member_log` where openid='" . $openid . "'";
            $accounts = $db1->getAll($sql);
            $czRes = true;
            foreach ($accounts as $accountKey => $accountValue) {
                $temp = $accountValue;
                $temp["user_id"] = $user_real_id;
                $tempCz = $db->autoExecute("ecs_user_account", $temp, "INSERT");
                if (!$tempCz) {
                    $czRes = false;
                    break;
                }
            }
            //收货地址
            $sql = "SELECT realname as consignee,mobile,LEFT(province,char_length(province) - 1) as province,LEFT(city,char_length(city) - 1) as city,area as district,address FROM `ims_sz_yi_member_address` where openid='" . $openid . "'";
            $addresses = $db1->getAll($sql);
            $addRes = true;
            foreach ($addresses as $addKey => $addValue) {
                $temp1 = $addValue;
                $temp1['country'] = 1;
                $temp1["user_id"] = $user_real_id;
                $temp1['province'] = $db->getOne("select region_id from ecs_region where region_name='" . $addValue['province'] . "' limit 1 ");
                $temp1['city'] = $db->getOne("select region_id from ecs_region where region_name='" . $addValue['city'] . "' limit 1 ");
                $temp1['district'] = $db->getOne("select region_id from ecs_region where region_name='" . $addValue['district'] . "' limit 1 ");
                $tempAdd = $db->autoExecute("ecs_user_address", $temp1, "INSERT");
                if (!$tempAdd) {
                    $addRes = false;
                    break;
                }
            }
            if (!$wxRes || !$addRes || !$czRes || !$resLog) {
                if (!$wxRes) {
                    error_log("导入会员微信数据失败" . "\r\n", 3, $logfile);
                }
                if (!$addRes) {
                    error_log("导入会员地址数据失败" . "\r\n", 3, $logfile);
                }
                if (!$czRes) {
                    error_log("导入会员充值提现数据失败" . "\r\n", 3, $logfile);
                }
                if (!$resLog) {
                    error_log("导入会员日志数据失败" . "\r\n", 3, $logfile);
                }
                $isok = false;
                break;
            }
        } else {
            error_log("导入会员基础数据失败" . "\r\n", 3, $logfile);
            $isok = false;
            break;
        }
        //sleep(2);
        $count++;
        //    //会员微信基本信息
        //会员充值、提现记录
        //保单记录
    }
    $sql = "select user_id,parent_id FROM ecs_users where parent_id>0";
    $parents = $db->getAll($sql);
    foreach ($parents as $parentKey => $parentValue) {
        $sql = "select mobile from ims_sz_yi_member where id='" . $parentValue['parent_id'] . "'";
        $parentMobile = $db1->getOne($sql);
        if (!empty($parentMobile)) {
            $sql = "select user_id from ecs_users where mobile_phone='" . $parentMobile . "'";
            $parentId = $db->getOne($sql);
            if (!empty($parentId)) {
                $sql = "update ecs_users set parent_id='" . $parentId . "' where user_id='" . $parentValue['user_id'] . "'";
                $db->query($sql);
            }
        } else {
            $sql = "update ecs_users set parent_id='0' where user_id='" . $parentValue['user_id'] . "'";
            $db->query($sql);
        }
    }
} catch (Exception $ex) {
    $isok = false;
    error_log("导入会员数据出错" . $ex->getMessage() . "\r\n", 3, $logfile);
}

if ($isok) {
    error_log("成功导入会员数据" . $count . "\r\n", 3, $logfile);
    $db->commitTrans();
} else {
    error_log("导入会员数据失败" . "\r\n", 3, $logfile);
    $db->rollbackTrans();
}

//处理报单
//$sql = "SELECT s.addTime,a.mobile as smobile,b.mobile,s.goods,s.money FROM `ims_sz_yi_yq_goods` s LEFT JOIN ims_sz_yi_member a ON s.uid = a.id LEFT JOIN ims_sz_yi_member b ON s.returnId = b.id where a.mobile is not NULL";
//$bdInfos = $db1->getAll($sql);
//foreach ($bdInfos as $bdKey => $bdValue) {
//    $sql = "SELECT user_id,parent_id from ecs_users where mobile='" . $bdValue["smobile"] . "' limit 1";
//    $supplierInfo = $db->getRow($sql);
//    $temp2 = array();
//    $temp2["createtime"] = local_strtotime($temp2["addTime"]);
//    $sql = "SELECT user_id from ecs_users where mobile='" . $bdValue["mobile"] . "' limit 1";
//    $temp2["user_id"] = $db->getOne($sql);
//    $temp2["supplier_id"] = $supplierInfo["user_id"];
//    $temp2["supplier_parent_id"] = $supplierInfo["parent_id"];
//    $temp2["orderno"] = date("Ymd", strtotime($temp2["addTime"])) . rand(0, 99999);
//    $temp2["status"] = 1;
//    $temp2["order_amt"] = $bdValue['money'];
//    $temp2["order_bdf"] = $bdValue['money'] * 1 * 0.1;
//    $db->autoExecute("ecs_order_bd", $temp2);
//}
//echo count($members);
//exit;
//$db1 = new cls_mysql($db_host, $db_user, $db_pass, $db_name);


function mb_unserialize($serial_str) {
    $serial_str = preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $serial_str);
    $serial_str = str_replace("\r", "", $serial_str);
    return unserialize($serial_str);
}

// ascii 
function asc_unserialize($serial_str) {
    $serial_str = preg_replace('!s:(\d+):"(.*?)";!se', '"s:".strlen("$2").":\"$2\";"', $serial_str);
    $serial_str = str_replace("\r", "", $serial_str);
    return unserialize($serial_str);
}

/**
 * $str  微信昵称 
 * */
function filter($str) {
    if ($str) {
        $name = $str;
        $name = preg_replace('/\xEE[\x80-\xBF][\x80-\xBF]|\xEF[\x81-\x83][\x80-\xBF]/', '', $name);
        $name = preg_replace('/xE0[x80-x9F][x80-xBF]‘.‘|xED[xA0-xBF][x80-xBF]/S', '?', $name);
        $return = json_decode(preg_replace("#(\\\ud[0-9a-f]{3})#ie", "", json_encode($name)));
        if (!$return) {
            return $return;
        }
    } else {
        $return = '';
    }
    return $return;
}
