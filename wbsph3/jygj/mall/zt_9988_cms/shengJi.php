<?php
include"config/check.php";
  error_reporting(0);
  include "../myphplib/db.php";
  include "../myphplib/message.php";
  $sql="select * from  zt_setting ";
  $row = mysql_query($sql);
  while($rs = mysql_fetch_assoc($row)){
      $configs[] = $rs;
  }
  
  function  getWebConfig($name){
      global   $configs;
      for ($i=0;$i<count($configs);$i++){
          if($configs[$i]['name']==$name)   {
              return $configs[$i]['value'];
          }
      }
  }
  
  /**
   *获取某层次用户升级需要某层多少店铺数,  懂事不参与升级
   * @param unknown $name
   */
  function  getLevelDianPuShengJi($userLevel, $type){
      if($type==1){//第一层
          if($userLevel==0){
              return getWebConfig("sheng_chu_dai_dian_pu");
          }else if($userLevel==1){
              return  getWebConfig("sheng_zhong_dai_dian_pu");
          }else if($userLevel==2){
              return getWebConfig("sheng_gao_dai_dian_pu");
          }else if($userLevel==3){
              return getWebConfig("sheng_yun_ying_dian_pu");
          }
      }elseif($type==2){//需要的是第二层的数据
          if($userLevel==0){
              return getWebConfig("sheng_chu_dai_dian_pu2");
          }else if($userLevel==1){
              return  getWebConfig("sheng_zhong_dai_dian_pu2");
          }else if($userLevel==2){
              return getWebConfig("sheng_gao_dai_dian_pu2");
          }else if($userLevel==3){
              return getWebConfig("sheng_yun_ying_dian_pu2");
          }
      }
      else{//需要的是第二层业绩,默认不需要业绩
          if($userLevel==0){
              return getWebConfig("sheng_chu_dai_ye_ji");
          }else if($userLevel==1){
              return  getWebConfig("sheng_zhong_dai_ye_ji");
          }else if($userLevel==2){
              return getWebConfig("sheng_gao_dai_ye_ji");
          }else if($userLevel==3){
              return getWebConfig("sheng_yun_ying_ye_ji");
          }
      }
      return -1;
  }
  
  /**
   * 获取用户的身份
   * @param unknown $level
   * @return string
   */
  function  getShenFen($level){
      if($level==1){
          return "初级代理";
      }else if($level==2){
          return "中级代理";
      }else if($level==3){
          return "高级代理";
      }else if($level==4){
          return "运营中心";
      }else if($level==5){
          return "懂事";
      }
  }
  

 
  $user=$_REQUEST['user'];
  $user = getOne("select user_name from xbmall_users where user_id={$user}");
  
   
  $level = $_REQUEST['level'];//要升级到的级别
  $type = $_REQUEST['type'];//升级还是降级
 
  if(!isset($user)){
      alertAndCloseAndRefreshParent("请制定要升级的用户");
  }
  
  if($type==1){
//          恢复用户真实的店铺数据
      $sql="update xbmall_users  set   dian_pu_shu1=dian_pu_shu1-vitual_dian_pu_shu1,dian_pu_shu2=dian_pu_shu2-vitual_dian_pu_shu2,ye_ji=ye_ji-vitual_ye_ji  where user_name='{$user}'";
      mysql_query($sql);
  }
  
  $sql="select  * from xbmall_users  where user_name='{$user}'";
  $userInfo = getRow($sql);
 
  $yiCengNum = getLevelDianPuShengJi($level-1,1);
  $erCengNum =getLevelDianPuShengJi($level-1,2);
  $yeJi =getLevelDianPuShengJi($level-1,3);
  
  $shenFen = getShenFen($level);
  
  $addDianPu1 = $userInfo['dian_pu_shu1']>$yiCengNum?0:$yiCengNum-$userInfo['dian_pu_shu1'];
  $addDianPu2 = $userInfo['dian_pu_shu2']>$erCengNum?0:$erCengNum-$userInfo['dian_pu_shu2'];
  $addYeJi = $userInfo['ye_ji']>$yeJi?0:$yeJi-$userInfo['ye_ji'];
  $tjrq = time();//提交时间
  $nowDateStr =  date("Y-m-d H:i:s", $tjrq);
  $sql ="insert into zt_xxtzzs (user,zsrq,comment) values('{$user}','{$nowDateStr}','手动调整等级为{$shenFen},增加虚拟一层店铺数{$addDianPu1},增加虚拟二层店铺数{$addDianPu2},增加虚拟业绩{$addYeJi}')";
  mysql_query($sql);
  
  $sql="update xbmall_users  set  vitual_dian_pu_shu1={$addDianPu1},vitual_dian_pu_shu2={$addDianPu2},
   vitual_ye_ji = {$addYeJi},  level={$level},dian_pu_shu1=dian_pu_shu1+{$addDianPu1},dian_pu_shu2=dian_pu_shu2+{$addDianPu2},ye_ji=ye_ji+{$addYeJi} where user_name='{$user}'";
  mysql_query($sql);
  @mysql_close($db);
  
  alertAndCloseAndRefreshParent("升级成功");
  ?>
  


</body>

</html>
