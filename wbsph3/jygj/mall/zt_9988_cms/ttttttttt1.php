<?php 
include "../myphplib/db.php";
 
function  db_execute($sql){
    var_dump($sql."<br>");
    return mysql_query($sql);
}
/**
 * 查询出所有提现未审核的人
 * @var string $sql
 */
$sql="SELECT * FROM `zt_b_cash_record` WHERE zt=0  ORDER BY id desc  ";
$result = mysql_query($sql);

$dateNow =   date("Y-m-d H:i:s",  time());  
while ( $data  = mysql_fetch_array ( $result ) ) {
    $sql="select * from  xbmall_users   where user='{$data['user']}'";//查询出用户信息
    $userInfo = mysql_query($sql);
    $data['je'] =  $data['je']+$userInfo['jf'];
    $zsq= intval(($data['je']-($data['je']%120)) /120);//保证计算出来的是整数
    //修改用户的赠送权
    $sql="update xbmall_users set zsq =zsq+{$zsq},jf=0 where user='{$data['user']}'";
    db_execute($sql);
    
    //删除这条提现记录
    $sql="delete from   zt_b_cash_record where id={$data['id']} ";
    db_execute($sql);
    
    
    //插入修改赠送权记录
    $sql="select * from   zt_zsq where user='{$data['user']}' ";
    $zsqData = getRow($sql);
    if($zsqData){
        $sql="update  zt_zsq  set zsq=zsq+{$zsq} where user='{$data['user']}' ";
        db_execute($sql);
    }else{
        $sql="insert into   zt_zsq(zsq,user,date,sf)  values({$zsq},'{$data['user']}','{$dateNow}','{$userInfo['a']}')  ";
        db_execute($sql);
    }
}


/**
 * 把用户的积分转为分红权
 */
$sql="select  jf, user from  xbmall_users where jf>0 ";
$result = mysql_query($sql);
while ( $data  = mysql_fetch_array ( $result ) ) {
    $zsq= intval(($data['jf']-($data['jf']%120)) /120);
   //插入修改赠送权记录
   $sql="select * from   zt_zsq where user='{$data['user']}' ";
   $zsqData = getRow($sql);
   if($zsqData){
       $sql="update  zt_zsq  set zsq=zsq+{$zsq} where user='{$data['user']}' ";
       db_execute($sql);
   }else{
       $sql="insert into   zt_zsq(zsq,user,date,sf)  values({$zsq},'{$data['user']}','{$dateNow}','{$userInfo['a']}')  ";
       db_execute($sql);
   }
}
 
 
 die("okkk");
?>
  