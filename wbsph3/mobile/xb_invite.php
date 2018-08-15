<?php

/**
 *  我的邀请
 */

require_once(dirname ( __FILE__ ).'/xb_header.php');
 

call_user_func ( $function_name );



function  action_default(){
    global  $smarty,$user_id  ;
    $user=get_user_default($user_id);
    
    $user_list = data_list();
   
    $smarty->assign("user",$user);
    $smarty->assign("data_list",$user_list['list']);
    $smarty->assign("shop_name","邀请记录");
    $smarty->display("xb_invite_list.dwt");
}


function data_list()
{
    global  $smarty ,$user_id ;
    
    $sub_user_id = getRequestInt("user_id");
    if($sub_user_id==null || $sub_user_id==-1){
        $sub_user_id =  $user_id;
    }
    $filter =  array();
    $where = " parent_id={$sub_user_id} ";
    $fromSql = db_table('users') . " WHERE  $where";
    setPageInfo($filter, $fromSql);
   
    $sql =  getDataSql($filter, $fromSql, "*", 'user_id');
    set_filter($filter, $sql);
    
    $res = db_query($sql);
    $list = array();
    while ($row = db_fetchRow($res)) {
        $row['level']=getLevelName($row['level']);
        $row['dan_shu']= $row['total_pu_huo']/3600;
        $row['reg_time'] = local_date('Y-m-d H:i', $row['reg_time']);
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

function  getLevelName($level){
    if($level==0){
        return "普通会员";
    }
    if($level==1){
        return "合伙人";
    }
    if($level==2){
        return "准区域代理";
    }
    if($level==3){
        return "区域代理";
    }
    
}
 
?>
