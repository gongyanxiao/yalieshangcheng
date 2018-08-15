<?php

/**
 *   合伙人
 */

require_once(dirname ( __FILE__ ).'/xb_header.php');
 

call_user_func ( $function_name );



function  action_default(){
    global  $smarty,$user_id  ;
    $user=get_user_default($user_id);
    if($user['level']==0){
        $ji_bie = "当前级别:普通会员";
    }else if($user['level']==1){
        $ji_bie = "当前级别:合伙人";
    } else if($user['level']==2){
        $ji_bie = "当前级别:准区域招商";
    }else if($user['level']==3){
        $ji_bie = "当前级别:区域招商";
    }
    
    $smarty->assign("shop_name",$ji_bie);
    
    $smarty->assign("user",$user);
    $smarty->display("xb_partner.dwt");
}


/**
 * 释放签到收益
 */
function  action_shi_fang_qian_dao_shou_yi(){
    
    global  $smarty,$user_id  ;
    $user=get_user_default($user_id);
    
    $shou_yi_rate = get_qian_dao_shou_yi_rate($user);
    if($shou_yi_rate==null){
        show_message("释放比例未设置,请联系管理员");
    }
    
    //释放推荐收益到合伙人余额
    $shi_fang =$user['ye_qian_dao']* ($shou_yi_rate/100);
    $sql="update xbmall_users set ye_qian_dao=ye_qian_dao-".$shi_fang."
    , total_he_huo_ren=total_he_huo_ren+".$shi_fang.", ye_he_huo_ren=ye_he_huo_ren+".$shi_fang."
  where user_name='{$user['user_name']}'";
    db_query($sql);
    
    zrzcjl($user, 4, 11, $shi_fang);//签到余额转到合伙人余额
    show_message("释放签到收益成功");
    
}


/**
 * 释放推荐收益
 */
function  action_shi_fang_tui_jian_shou_yi(){
    global  $smarty,$user_id  ;
    $user=get_user_default($user_id);
    
    $shou_yi_rate = get_tui_jian_shou_yi_rate($user);
    if($shou_yi_rate==null){
       show_message("释放比例未设置,请联系管理员");
    }
   
    //释放推荐收益到合伙人余额
    $shi_fang =$user['ye_tui_jian_shou_yi']* ($shou_yi_rate/100);
    $sql="update xbmall_users set ye_tui_jian_shou_yi=ye_tui_jian_shou_yi-".$shi_fang." 
    , total_he_huo_ren=total_he_huo_ren+".$shi_fang.", ye_he_huo_ren=ye_he_huo_ren+".$shi_fang."
  where user_name='{$user['user_name']}'";
    db_query($sql);
    
    zrzcjl($user, 0, 11, $shi_fang);//推荐余额转到合伙人余额
    show_message("释放成功");
}
 
function  action_to_transfer(){
    global  $smarty,$user_id  ;
    $user=get_user_default($user_id);
    $source = getRequestInt("source");
    $smarty->assign("user",$user);
     $jin_e = get_source_jin_e($user, $source);//合伙人余额
    
     $smarty->assign("shop_name","转出");
    
    $smarty->assign("jin_e",$jin_e);
    $smarty->assign("source",$source);
    $smarty->display("xb_transfer.dwt");
    
}

function  get_source_jin_e($user,$source){
    $jin_e = 0;
    if($source==11){
        $jin_e =  $user['ye_he_huo_ren'];//合伙人余额
    }
    if($source==7){
        $jin_e =  $user['ye_dian_pu'];//店铺余额
    }
    
    if($source==2){
        $jin_e =  $user['ye_yqjl'];//邀请奖励余额
    }
    
    return $jin_e;
}

/**
 * 合伙人余额转到我的余额
 */
function  action_transfer(){
    global  $smarty,$user_id  ;
    $user=get_user_default($user_id);
    $smarty->assign("user",$user);
    $source = getRequestInt("source");
    $money = getRequestInt("money");
    
    if($money<500){//最小转出金额500
        show_message("最小转出金额500");
        die;
    }
    
    
    $jin_e = get_source_jin_e($user, $source);//账户有的钱
    
    if($money>$jin_e){//要转的钱大于账户的钱
        show_message("最多转{$jin_e}");
        die;
    }
 
   transfer_money_to_yu_e($user, $source,$money);
   zrzcjl($user, 11, 1, $money);//合伙人余额转到我的余额
    
   show_message("转出成功");
}


/**
 * 交易明细
 */
function  action_transaction_detail(){
    global  $smarty,$user_id  ;
    $user=get_user_default($user_id);
    $source = getRequestInt("source");
   
    $all = data_list_transaction(); 
    $list = $all['list'];
    $data_list = array();
    foreach ($list as $key => $val){
        if($val['source']==$source){
            $val['direct'] = "转出"; 
            $val['dui_fang'] = get_trans_name($val['target']);
        }else{
            $val['direct'] = "转入"; 
            $val['dui_fang'] = get_trans_name($val['source']);
        }
      $data_list[]=$val;
    }
    $smarty->assign('shop_name',"交易明细" );
    $smarty->assign('data_list',$data_list );
    $smarty->display("xb_transaction_detail_list.dwt");
}

function  get_trans_name($type){
    if($type==0)return "推荐奖励余额";
    if($type==1)return "我的余额";
    if($type==3)return "铺货销售";
    if($type==9)return "店铺扫码";
    if($type==11)return "合伙人余额";
    
    return "提现";
    
}

/**
 * 将钱转到余额
 * @param unknown $user
 * @param unknown $source
 */
function  transfer_money_to_yu_e($user,$source,$jin_e){
    if($jin_e==0) return 0;
    
    $sql="update xbmall_users set ";
    if($source==11){
        $sql.=" ye_he_huo_ren=ye_he_huo_ren- ".$jin_e;
    }
    if($source==7){
        $sql.=" ye_dian_pu=ye_dian_pu- ".$jin_e;
    }
    
    if($source==2){
        $sql.=" ye_yqjl=ye_yqjl - ".$jin_e;
    }
    
    $sql.=", user_money=user_money+".$jin_e." where user_name='{$user['user_name']}'";
    db_query($sql);
    return   $jin_e;
}

/**
 * 转入转出记录
 * @param unknown $user
 * @param unknown $source
 * @param unknown $target
 * @param unknown $jin_e
 */
function  zrzcjl($user,$source,$target,$jin_e){
    date_default_timezone_set("Asia/Shanghai");
    $date = gmtime();
    $sql="insert into xbmall_xb_zrzcjl (source,target,je,date,user_id,mobile,real_name,
district,district_name,city,city_name,province,province_name,order_sn,comment) values
({$source},{$target},{$jin_e},{$date} ,{$user['user_id']} ,'{$user['user_name']}',
'{$user['real_name']}',{$user['district']},'{$user['district_name']}'
,{$user['city']}  ,'{$user['city_name']}' ,{$user['province']},'{$user['province_name']}','','')";
    db_query($sql);
}



/**
 * 到提现页面
 */
function  action_to_ti_xian(){
    global  $smarty,$user_id  ;
    $user=get_user_default($user_id);
    $smarty->assign("user",$user);
    $smarty->assign('shop_name',"提现" );
    $smarty->display("xb_ti_xian.dwt");
}



/**
 * 提现记录
 */
function  action_ti_xian_list(){
    global  $smarty,$user_id  ;
    $user=get_user_default($user_id);
    $list = data_list_ti_xian($user);
    $smarty->assign("data_list",$list['list']);
    $smarty->assign('shop_name',"提现记录" );
    $smarty->display("xb_ti_xian_list.dwt");
}

function data_list_ti_xian($user)
{
    
    /* 过滤条件 */
    $where = " user='{$user['user_name']}' ";
    $filter =  array();
     
    $fromSql = db_table('xb_ti_xian') . " WHERE  $where";
    
    setPageInfo($filter, $fromSql);
    
    $sql =  getDataSql($filter, $fromSql, "*", 'date');
    set_filter($filter, $sql);
    $res = db_query($sql);
    $list = array();
    
    while ($row = db_fetchRow($res)) {
        $row['date'] = local_date('Y-m-d H:i', $row['date']);
        if($row['cwshrq']!=null){
            $row['cwshrq'] = local_date('Y-m-d H:i', $row['cwshrq']);
        }
        if($row['cwsh']==0){
            $row['cwsh'] = "等待处理";
        } if($row['cwsh']==1){
            $row['cwsh'] = "通过";
        } if($row['cwsh']==2){
            $row['cwsh'] = "不通过";
        }
        
        $list[] = $row;
    }
    
    
    $arr = array(
        'list' => $list,
        'filter' => $filter,
        'page_count' => $filter['page_count'],
        'record_count' => $filter['record_count']
    );
    
    return $arr;
    
}



function  action_ti_xian(){
    global  $smarty,$user_id  ;
    date_default_timezone_set("Asia/Shanghai");
    $user=get_user_default($user_id);
    $smarty->assign("user",$user);
    $money=getRequestInt("money");
    if($money<500){
        show_message("提现金额不能少于500");
    }
    
    if($money>$user['user_money']){
        show_message("账户内的余额".$user['user_money'].",提现金额超过账户余额");
    }
    
    if($user['tap_account']==null || $user['tap_account']=='' ){
        show_message("请先绑定tap账号",'绑定tap', "/mobile/xb_user.php?act=to_set_tap");
    }
    
    
    
    //修改用户账户
    $sql="update xbmall_users set user_money=user_money-{$money} where user_name='{$user['user_name']}'";
    db_query($sql);
    
    $date = gmtime();
    //增加提现记录
    $sql=" insert into xbmall_xb_ti_xian(date,je,user,bank,bank_num,bank_kh)values
   ({$date},{$money},'{$user['user_name']}','{$user['bank']}','{$user['bank_num']}','{$user['bank_kh']}')";
    db_query($sql);
    
    show_message('提现成功！', '返回', 'xb_user.php?act=my');
//     zrzcjl($user, 1, 20, $money);//转入转出记录
}


/**
 * 获取推荐收益释放比例
 * @param unknown $user
 * @return unknown|NULL|string|boolean
 */
function  get_tui_jian_shou_yi_rate($user){
    if($user['shi_fang_tjsy_ts']!=0){//使用个人的推荐收益释放比例
        $shou_yi_rate = $user['shi_fang_tjsy_rate'];
    }else{//使用区域的释放比例
        $sql ="select a.region_rate as province_rate  from xbmall_region a
    where a.region_id={$user['district']}";
        $shou_yi_rate =  db_getOne($sql);
        if($shou_yi_rate==null){//使用市的释放比例
            $sql ="select a.region_rate as province_rate  from xbmall_region a
    where a.region_id={$user['city']}";
            $shou_yi_rate =  db_getOne($sql);
            if($shou_yi_rate==null){//使用省的释放比例
                $sql ="select a.region_rate as province_rate  from xbmall_region a
    where a.region_id={$user['province']}";
                $shou_yi_rate =  db_getOne($sql);
            }
            
        }
        
    }
    return  $shou_yi_rate;
}


/**
 * 签到
 */
function  action_qian_dao(){
    //查看今天签到了吗? 查看有没有提过现
    global  $smarty,$user_id  ;
    $user=get_user_default($user_id);
    $sql="select count(0) from  xbmall_xb_ti_xian where user='{$user['user_name']}'";
    $count = db_getOne($sql);
    if($count=0){//没有提过现
        $jin_e = ($user['total_pu_huo']/3600)*6; //总铺货单数*6
    }else{//提过
        $sql="select count(0) from  xbmall_xb_xxtz where syxsts>0 and user='{$user['user_name']}'";
        $count = db_getOne($sql);
        $jin_e = $count*6; //总铺货单数*6
    }
    if($jin_e>0){
        
    }
    
}



/**
 * 获取签到收益释放比例
 * @param unknown $user
 * @return unknown|NULL|string|boolean
 */
function  get_qian_dao_shou_yi_rate($user){
    if($user['shi_fang_qdsy_ts']!=0){//使用个人的签到收益释放比例
        $shou_yi_rate = $user['shi_fang_qdsy_rate'];
    }else{//使用区域的释放比例
        $sql ="select a.region_qd_rate as province_rate  from xbmall_region a
    where a.region_id={$user['district']}";
        $shou_yi_rate =  db_getOne($sql);
        if($shou_yi_rate==null){//使用市的释放比例
            $sql ="select a.region_qd_rate as province_rate  from xbmall_region a
    where a.region_id={$user['city']}";
            $shou_yi_rate =  db_getOne($sql);
            if($shou_yi_rate==null){//使用省的释放比例
                $sql ="select a.region_qd_rate as province_rate  from xbmall_region a
    where a.region_id={$user['province']}";
                $shou_yi_rate =  db_getOne($sql);
            }
            
        }
        
    }
    return  $shou_yi_rate;
}

/**
 * 签到收益
 */
function  action_sign_income(){
    global  $smarty,$user_id  ;
    $user=get_user_default($user_id);
    $smarty->assign('shop_name',"签到收益" );
    $smarty->assign("user",$user);
    $smarty->display("xb_sign_income.dwt");
}

/**
 * 到铺货页面
 */
function  action_to_pu_huo(){
    global  $smarty,$user_id  ;
    
    $user=get_user_default($user_id);
    $smarty->assign("user",$user);
    if($user['is_set_address']==0){//在铺货前没有设置地址 
        $province_list   = get_regions ( 1, 1 );
        $city_list   = get_regions ( 2, $user ['province'] );
        $district_list   = get_regions ( 3, $user ['city'] );
//         $district_list  = get_regions ( 4, $user ['district'] );
        $smarty->assign("province_list",$province_list);
        $smarty->assign("city_list",$city_list);
        $smarty->assign("district_list",$district_list);
        $smarty->display("xb_set_adrress.dwt");
        die;
    }
    $smarty->assign('shop_name', "铺货");
    //判断用户有没有进行地址的二次判断
    $smarty->display("xb_partner_ph.dwt");
}


function  action_set_address(){
    global  $smarty,$user_id  ;
    $province = getRequestInt("province");
    $city = getRequestInt("city");
    $district = getRequestInt("district");
   
    //查询省市县名字
    $sql="select a.region_name as province_name,b.region_name as city_name,c.region_name as district_name 
  from xbmall_region a , xbmall_region b , 
 xbmall_region  c where a. region_id={$province} and  b.region_id={$city}  and c.region_id={$district}";
    $address = db_getRow($sql);
   
    
    $sql="update xbmall_users set province={$province}, province_name='{$address['province_name']}',
  city_name='{$address['city_name']}', district_name='{$address['district_name']}',
city={$city}, district={$district},is_set_address=1 where user_id={$user_id}";
    db_query($sql);
    header("Location:xb_partner.php?act=to_pu_huo");
}

/**
 * 铺货列表页
 */
function  action_pu_huo_list(){
    global  $smarty,$user_id  ;
    $user=get_user_default($user_id);
    $smarty->assign("user",$user);
    $data_list = data_list_pu_huo();
    $smarty->assign('data_list', $data_list['list']);
    $smarty->assign('shop_name', "铺货订单");
    $smarty->display("xb_partner_pu_huo_list.dwt");
}


// -- ajax返回用户列表
/* ------------------------------------------------------ */
function action_query()
{
    // 全局变量
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    
    $ecs = $GLOBALS['ecs'];
    
    $data_list = data_list_pu_huo();
    
    assign('data_list', $data_list['list']);
    assign('filter', $data_list['filter']);
    assign('record_count', $data_list['record_count']);
    assign('page_count', $data_list['page_count']);
    
    $sort_flag = sort_flag($data_list['filter']);
    assign($sort_flag['tag'], $sort_flag['img']);
    
    make_json_result(fetch('xb_partner_pu_huo_list.dwt'), '', array(
        'filter' => $data_list['filter'],
        'page_count' => $data_list['page_count']
    ));
}


/**
 * 交易明细查询
 * @return unknown[]|string[]|unknown[][]
 */
function data_list_transaction()
{
    
    
    /* 过滤条件 */
    $source =  getRequestInt("source");
    $where = " 1=1 ";
    $filter =  array();
    if ($source!=-1) {
        {
            $where .= " and (source={$source} or target={$source})" ;
        }
    }
    $fromSql = db_table('xb_zrzcjl') . " WHERE  $where";
    
    setPageInfo($filter, $fromSql);
    
    $sql =  getDataSql($filter, $fromSql, "*", 'id');
    set_filter($filter, $sql);
    $res = db_query($sql);
    $list = array();
    
    while ($row = db_fetchRow($res)) {
        $row['date'] = local_date('Y-m-d H:i', $row['date']);
        $list[] = $row;
    }
    
    
    $arr = array(
        'list' => $list,
        'filter' => $filter,
        'page_count' => $filter['page_count'],
        'record_count' => $filter['record_count']
    );
    
    return $arr;
    
}

function data_list_pu_huo()
{
    $result = get_filter();
    if ($result === false) {
       
        /* 过滤条件 */
        $filter['real_name'] = empty($_REQUEST['real_name']) ? '' : trim($_REQUEST['real_name']);
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1) {
            $filter['real_name'] = json_str_iconv($filter['real_name']);
        }
        
        $where = " 1=1 ";
        setIntCondition($filter,$where, 'type');
        quYuTiaoJian($filter, $where);//区域查询条件
        if (! empty($filter['real_name'])) {
            {
                $where .= " real_name like '" .mysql_like_quote($filter['real_name'])."' " ;
            }
        }
        $fromSql = db_table('xb_xxtz') . " WHERE  $where";
        setPageInfo($filter, $fromSql);
      
        $sql =  getDataSql($filter, $fromSql, "*", 'id');
        $filter['keyword'] = stripslashes($filter['keyword']);
        set_filter($filter, $sql);
        $res = db_query($sql);
        $list = array();
       
        while ($row = db_fetchRow($res)) {
            $row['tjrq'] = local_date('Y-m-d H:i', $row['tjrq']);
            $list[] = $row;
        }
    
        
        $arr = array(
            'list' => $list,
            'filter' => $filter,
            'page_count' => $filter['page_count'],
            'record_count' => $filter['record_count']
        );
        
        return $arr;
    }
}


/**
 * 处理铺货
 */
function  action_pu_huo(){
    global  $smarty,$user_id  ;
    $user=get_user_default($user_id);
    date_default_timezone_set("Asia/Shanghai");
    $dan_shu=getRequestInt("dan_shu");
    $tjrq = gmtime();//提交时间
    
   
    $filePath= saveFile("pu_huo_ping_zheng");
   
//     show_message ( "参数错误", '返回主页', 'user.php?act=li_cai', 'info' );
    
    $sql="insert into ".db_table("xb_xxtz")." (user,real_name,dan_shu,tjrq,syxsts,province,province_name,
city, city_name, district, district_name, ping_zheng)values( 
'{$user['user_name']}','{$user['real_name']}',{$dan_shu},{$tjrq},45,
{$user['province']},'{$user['province_name']}',{$user['city']},'{$user['city_name']}',
{$user['district']},'{$user['district_name']}',  '{$filePath}')";
    mysql_query($sql);
    
//     $newId = db_insert_id();
//     $data = getRow(" select * from xbmall_xb_xxtz where id={$newId}");
//     pu_huo_jiang_li($user, $data);
    header("Location:xb_partner.php?act=pu_huo_list");
}



/**
 * 铺货审核
 */
function  action_pu_huo_sh(){
    global  $smarty,$user_id  ;
    
    //     show_message ( "参数错误", '返回主页', 'user.php?act=li_cai', 'info' );
    $id = getRequestInt("id");
    $sql="  select * from  xbmall_xb_xxtz where id={$id} ";
    $object = getRow($sql);
    
    mysql_query($sql);
    $smarty->display("xb_partner_ph_sh.dwt");


}

/**
 * 推荐奖励页面
 */
function  action_rewards(){
    global  $smarty,$user_id  ;
    $user=get_user_default($user_id);
    $smarty->assign("user",$user);
    //     show_message ( "参数错误", '返回主页', 'user.php?act=li_cai', 'info' );
//     $id = getRequestInt("id");
//     $sql="  select * from  xbmall_xb_xxtz where id={$id} ";
//     $object = getRow($sql);
    $smarty->assign("shop_name","推荐收益");
    mysql_query($sql);
    $smarty->display("xb_partner_rewards.dwt");
}


/**
 * 推荐奖励明细
 */
function  action_rewards_list(){
    global  $smarty,$user_id  ;
    $user=get_user_default($user_id);
    $smarty->assign("user",$user);
    //查询推荐奖励明细
    
    $filter = array();
    $fromSql = db_table('xb_xxtzzs') . " where user='{$user['user_name']}' and type=0 ";
    setPageInfo($filter, $fromSql);
    $sql =  getDataSql($filter, $fromSql, "*", 'id');
    $res = db_query($sql);
    $list = array();
    while ($row = db_fetchRow($res)) {
        $list[] = $row;
    }
    
    $smarty->assign("shop_name","奖励明细");
    $smarty->assign("data_list",$list);
    
    $smarty->display("xb_partner_rewards_list.dwt");
}



/**
 * 今天最多可以投多少笔
 * @param unknown $fenShu
 */
function  saveFile($file_name){
    
    $path = "../data/uploads_pu_huo/";
    $extArr = array("jpg", "png", "gif");
    date_default_timezone_set("Asia/Shanghai");
    if(isset($_POST) && $_SERVER['REQUEST_METHOD'] == "POST"){
        $name = $_FILES[$file_name]['name'];
        $size = $_FILES[$file_name]['size'];
      
        if(empty($name)){
            show_message("请选择要上传的图片" );
        }
        $extend = pathinfo($name);
        $ext = strtolower($extend["extension"]);
        
        if(!in_array($ext,$extArr)){
            show_message("图片格式错误" );
        }
        if($size>(100*102400)){
            show_message("图片大小不能超过10M" );
        }
      
        $image_name = gmtime().rand(100,999).".".$ext;
        $tmp = $_FILES[$file_name]['tmp_name'];
        $filePath=$path.$image_name;
        if(move_uploaded_file($tmp, $filePath)){
            return $image_name;
        }else{
            die( '上传出错了！');
        }
    }
 
    return  $filePath ;
}
?>
