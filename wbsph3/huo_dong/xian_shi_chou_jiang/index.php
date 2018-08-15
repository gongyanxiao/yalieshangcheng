 
	<?php  
    		 
		    include "../../jygj/mall/myphplib/db.php";
		    include "../../jygj/mall/myphplib/message.php";
		    include "../../jygj/mall/myphplib/page.php";
		    //不检查登录状态
		    
		    
		    $nowDateStr = date("Y-m-d H:i:s", time());
		    $act_id = intval($_POST['act_id']);
		    $user_names = $_POST['user_names'];
		    $userNameArr = explode(",",$user_names);
		    
		    //修改活动状态
		    $sql="update  xbmall_goods_activity  set  is_finished = 1 where act_id=".$act_id;
		  
		    mysql_query($sql);
		    
		    $goods_activity = getRow("select * from  xbmall_goods_activity'  where  act_id=".$act_id);
		    
		    $is_xian_shi_chou_jiang = true;
		    
		    //修改中奖者
		    foreach ($userNameArr as $key => $value) {
		        if($value=='')continue;
		        
		        $sql="update xbmall_hd_chou_jiang_bao_ming  set  state = 1 where act_id={$act_id} and user_name='".$value."'";
		        mysql_query($sql);
		         //  减去中奖者的积分
		    
		            $sql="update xbmall_users   set  pay_points = pay_points-100  where  user_name='".$value."'";
		            mysql_query($sql);
		            
		            $sql = "insert into zt_xxtzzs (user,zsrq,xxtzzxfjz,comment) values('{$value}','{$nowDateStr}',-100,'现实抽奖中奖{$goods_activity['goods_name']},消耗消费积分100')";
		            mysql_query($sql);
		         
		        
		    }
		    die(0);
		    
		    
  ?>
	 
 