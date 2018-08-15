<?php
include "../myphplib/db.php";

header("Content-type: text/html; charset=utf-8"); 

// $total = getTotalMoney("18669671133");//王
// var_dump($total);

// $total = getTotalMoney("13160060999");//朱
// var_dump($total);


// $total = getTotalMoney("18669987933");//许
// var_dump($total);

// $total = getTotalMoney("13356999836");//毛
// var_dump($total);


// $arr = array();
// tiXiUser("13356999836", $arr);
// $userStr ="";
// foreach ($arr AS $key => $val)
// {
//     $userStr = $userStr."'".$val[0]."',";
// }
// $userStr = substr($userStr, 0, strlen($userStr)-1);
// $userStr="(".$userStr.")";
// var_dump($userStr);


//   $yunYingZhongXinArr = getYunYingZhongXin();
  $yunYingZhongXinArr[] = array( "user_name"=>"13160060999","real_name"=>"朱家君"       );
  $yunYingZhongXinArr[] =array(  "user_name"=>"18669671133","real_name"=>"王海娜"  );
//   $yunYingZhongXinArr[] =array( "user_name"=>"13356999836","real_name"=>"毛建军");
  
 
  
  foreach  ($yunYingZhongXinArr AS $key => $val)
  {
      $userStr="<table>";
      $arr = array();
      tiXiUserDan($val['user_name'], $arr,1);
      foreach ($arr AS $key2 => $val2)
      {
          
          if($val2["real_name"]=='')$val2["real_name"]="未实名";
          
          if($val2["cj"]=='出局'){
              $userStr = $userStr."<tr>".getCengTab($val2["cengShu"])."<td><b>".$val2["real_name"]  ."</b> &#9;".$val2["level"]."(".$val2["cj"].")</td></tr>";
          }else{
              if($val2['level']=='运营中心'){
                  $userStr = $userStr."<tr>".getCengTab($val2["cengShu"])."<td><b>".$val2["real_name"]  
                  ."</b>:".$val2["level"]."单数".  $val2["wei_chu_ju_dan_shu"].":直推".$val2["zhi_tui_ren_shu"]."人,可提现:".$val2["jing_tai_ke_ti_xian"]."</td></tr>";
              }else{
                  $userStr = $userStr."<tr>".getCengTab($val2["cengShu"])."<td><b>".$val2["real_name"]  
                  ."</b>:".$val2["level"]."单数".  $val2["wei_chu_ju_dan_shu"].":直推".$val2["zhi_tui_ren_shu"]."人,可提现:".$val2["jing_tai_ke_ti_xian"]."</td></tr>";
              }
          }
      }
      $userStr.="</table>";
      var_dump($val['real_name']."体系下</br>".$userStr);
      var_dump( "</br></br></br></br></br>");
  }
  
  /**
   * 返回最大的层数
   * @param unknown $arr
   * @return number|unknown
   */
  function   getMaxCengShu($arr){
      $max = 0;
      foreach ($arr AS $key2 => $val2)
      {
          if($val2['cengShu']>$max){
              $max = $val2['cengShu'];
          }
      }
      return  $max;
  }
  
  function  getCengTab($cengShu){
      $str="";
      for ($i=0;$i<$cengShu;$i++){
          $str.="<td>&nbsp;</td>";
      }
      return $str;
  }
  
  function  chengBen($user){
      //算出一共分了多少钱,  算出现在一共有多少单
      $sql="SELECT SUM(xxtzztxjf) as xxtzztxjf , SUM(tui_jian_shou_yi) as tui_jian_shou_yi FROM `zt_xxtzzs` WHERE  user='". $user."' and (xxtzztxjf >0 or tui_jian_shou_yi >0) ";
     
      $zengSongZongE = getRow($sql);
      //可用于复投的金额是推荐收益+分红收益
      $keYongFuTou= $zengSongZongE['xxtzztxjf']+$zengSongZongE['tui_jian_shou_yi'];//推荐收益+分红收益
      $fuTouDanShu= $keYongFuTou/3600;
      //一共多少单, 算上出局的
      $sql=" select count(0) from  zt_xxtz where user='".$user."'";
      $danShu = getOne($sql);//总单数
      $danShu = $danShu -$fuTouDanShu;//成本单数
      return  $danShu;
  }
  
  //运营中心体系下的做单和级别情况
  function tiXiUserDan($user, &$arr,$cengShu)
  {
      $sql = "select  * from xbmall_users where recommend_user='{$user}'";
      
      $result = mysql_query($sql);
      
      while ($value = mysql_fetch_array($result)) {
          $sql="select count(0) from zt_xxtz where user='".$value['user_name']."' and cj_state=0";
          $count = getOne($sql);//没有出局的单数
          
          $sql="select count(0) from zt_xxtz where user='".$value['user_name']."' ";
          $zongDanShu = getOne($sql);
          $value['wei_chu_ju_dan_shu'] = $count;
          $value['count'] = $zongDanShu;
          if($value['level']==0){
              $value['level']="";
          } 
          if($value['level']==1){
              $value['level']="<b>★</b>";
          } 
          if($value['level']==2){
              $value['level']="<b>★★</b>";
          } 
          if($value['level']==3){
              $value['level']="<b>★★★</b>";
          } 
          if($value['level']==4){
              $value['level']="<b>★★★★</b>";
          } 
          $sql="select count(0) from xbmall_users where recommend_user='{$value['user_name']}'";
          $zhi_tui_ren_shu = getOne($sql);
          $value['zhi_tui_ren_shu']=$zhi_tui_ren_shu;
          
          
          if($count==0){
              $value['cj']="出局";
          }else{
              $value['cj']="";
          }
          
         
          $value['cengShu']=$cengShu;//所在层数
           
          
          $value['chengBen'] =  chengBen($value['user_name']);
          $arr[]=$value;
          tiXiUserDan($value['user_name'], $arr,$cengShu+1);
      }
  }
//查询出所有的运营中心
function getYunYingZhongXin(){
    $sql = "select  user_name, real_name from xbmall_users where level=4";
    
    $result = mysql_query($sql);
    
    while ($value = mysql_fetch_array($result)) {
        $arr[]=$value;
    }
    return  $arr;
}

function  getTotalMoney($user){
    $arr = array();
    tiXiUser($user, $arr);
    $userStr ="";
    foreach ($arr AS $key => $val)
    {
        $userStr = $userStr.$val[0].",";
    }
    $userStr = substr($userStr, 0, strlen($userStr)-1);
    $userStr="(".$userStr.")";
    
    $sql="select sum(je) from zt_xxtz_ti_xian WHERE  date >'2018-03-11' and user in {$userStr}";
    $total = getOne($sql);
    return $total;
}




function tiXiUser($user, &$arr)
{
    $sql = "select  user_name from xbmall_users where recommend_user='{$user}'";
    
    $result = mysql_query($sql);
    
    while ($value = mysql_fetch_array($result)) {
        $arr[]=$value;
        tiXiUser($value['user_name'], $arr);
    }
}
?>
  