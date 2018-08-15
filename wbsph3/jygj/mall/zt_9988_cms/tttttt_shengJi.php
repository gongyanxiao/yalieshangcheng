<?php
include"config/check.php";
header("Content-type: text/html; charset=utf-8"); 
  error_reporting(0);
  include "../myphplib/db.php";
  include "../myphplib/message.php";
  date_default_timezone_set("Asia/Shanghai");
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
  

 
  $shengJiArr=  array(
       array(   'user' => '13153957313', 'level'=>5)//聚元国际
      ,array( 'user'=>'15255054767' , 'level'=>4) //毛峰
      , array( 'user'=>'13160060999' , 'level'=>4)//朱家军
  	  ,  array( 'user'=>'18669671133' , 'level'=>4)//王海娜http://www.jygj.com/mall/zt_9988_cms/shengJi.html?user=
      ,  array( 'user'=>'13160050888' , 'level'=>4)//刘书振http://www.jygj.com/mall/zt_9988_cms/shengJi.html?user=
      ,  array( 'user'=>'18651672888' , 'level'=>4)//王涛http://www.jygj.com/mall/zt_9988_cms/shengJi.html?user=
      ,  array( 'user'=>'13356999836' , 'level'=>4)//毛建军http://www.jygj.com/mall/zt_9988_cms/shengJi.html?user=
      ,  array( 'user'=>'15205248588' , 'level'=>4)//陶书华http://www.jygj.com/mall/zt_9988_cms/shengJi.html?user=
  	  ,  array( 'user'=>'15836170111' , 'level'=>4)//王俊广http://www.jygj.com/mall/zt_9988_cms/shengJi.html?user=
  	  ,  array( 'user'=>'13173078555' , 'level'=>4)//邵明凤http://www.jygj.com/mall/zt_9988_cms/shengJi.html?user=
  	  ,  array( 'user'=>'13305395444' , 'level'=>4)//李鹏飞http://www.jygj.com/mall/zt_9988_cms/shengJi.html?user=
  	  ,  array( 'user'=>'13953967383' , 'level'=>4)//胡凡珍
  	  ,  array( 'user'=>'18754913456' , 'level'=>4)//徐振兴
  	 ,  array( 'user'=>'15949880912' , 'level'=>4)//毛一鸣http://www.jygj.com/mall/zt_9988_cms/shengJi.html?user=
  	 ,  array( 'user'=>'15098508029' , 'level'=>4)//李利http://www.jygj.com/mall/zt_9988_cms/shengJi.html?user=/
  	 ,  array( 'user'=>'13361370955' , 'level'=>4)//王乃刚http://www.jygj.com/mall/zt_9988_cms/shengJi.html?user=/
  	 ,  array( 'user'=>'13285359732' , 'level'=>4)//王静http://www.jygj.com/mall/zt_9988_cms/shengJi.html?user=/
  	 ,  array( 'user'=>'15966570777' , 'level'=>4)//吴军http://www.jygj.com/mall/zt_9988_cms/shengJi.html?user=/
     ,  array( 'user'=>'15264492456' , 'level'=>4)//刘涛http://www.jygj.com/mall/zt_9988_cms/shengJi.html?user=/
     ,  array( 'user'=>'18669987933' , 'level'=>4)//许婷http://www.jygj.com/mall/zt_9988_cms/shengJi.html?user=/
     , array( 'user'=>'13805397721' , 'level'=>4)//周志友	http://www.jygj.com/mall/zt_9988_cms/shengJi.html?user=/
     ,array( 'user'=>'13905391175' , 'level'=>4)//高恒新	http://www.jygj.com/mall/zt_9988_cms/shengJi.html?user=/
      
      
      
//       ,  array( 'user'=>'17356335622' , 'level'=>4)//徐朝旭http://www.jygj.com/mall/zt_9988_cms/shengJi.html?user=
//       ,  array( 'user'=>'13951392072' , 'level'=>4)//胡海http://www.jygj.com/mall/zt_9988_cms/shengJi.html?user=
//       ,  array( 'user'=>'15555575579' , 'level'=>4)//洪爱雄http://www.jygj.com/mall/zt_9988_cms/shengJi.html?user=
    
      
  );
  
 
  foreach ($shengJiArr AS $key => $val)
  {
      $user=$val['user'];//用户账号
      $level = $val['level'];//要升级到的级别
      $sql="update xbmall_users  set   dian_pu_shu1=dian_pu_shu1-vitual_dian_pu_shu1,dian_pu_shu2=dian_pu_shu2-vitual_dian_pu_shu2,ye_ji=ye_ji-vitual_ye_ji  where user_name='{$user}'";
      mysql_query($sql);
      
      
      $sql="select  * from xbmall_users  where user_name='{$user}'";
      $userInfo = getRow($sql);
      
      $yiCengNum = getLevelDianPuShengJi($level-1,1);
      $erCengNum =getLevelDianPuShengJi($level-1,2);
      $yeJi =getLevelDianPuShengJi($level-1,3);
      
      $shenFen = getShenFen($level);
      $addDianPu1 = $userInfo['dian_pu_shu1']>=$yiCengNum?0:$yiCengNum-$userInfo['dian_pu_shu1'];
      $addDianPu2 = $userInfo['dian_pu_shu2']>=$erCengNum?0:$erCengNum-$userInfo['dian_pu_shu2'];
      $addYeJi = $userInfo['ye_ji']>=$yeJi?0:$yeJi-$userInfo['ye_ji'];
      $tjrq = time();//提交时间
      $nowDateStr =  date("Y-m-d H:i:s", $tjrq);
      $sql ="insert into zt_xxtzzs (user,zsrq,comment) values('{$user}','{$nowDateStr}','手动调整等级为{$shenFen},增加虚拟一层店铺数{$addDianPu1},增加虚拟二层店铺数{$addDianPu2},增加虚拟业绩{$addYeJi}')";
      mysql_query($sql);
      
      $sql="update xbmall_users  set  vitual_dian_pu_shu1={$addDianPu1},vitual_dian_pu_shu2={$addDianPu2},
   vitual_ye_ji = {$addYeJi},  level={$level},dian_pu_shu1=dian_pu_shu1+{$addDianPu1},dian_pu_shu2=dian_pu_shu2+{$addDianPu2},ye_ji=ye_ji+{$addYeJi} where user_name='{$user}'";
      mysql_query($sql);
  }
 
  
 
 
//          恢复用户真实的店铺数据
  
  @mysql_close($db);
  
  alertAndCloseAndRefreshParent("升级成功");
  ?>
  


</body>

</html>
