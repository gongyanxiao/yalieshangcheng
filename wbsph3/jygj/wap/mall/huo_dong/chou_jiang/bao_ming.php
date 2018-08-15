 
  
 <?php  
 
		    include "../../myphplib/db.php";
		    include "../../myphplib/message.php";
		    include "../../myphplib/page.php";
		    $act_id = $_POST['act_id'];
		    
		    $nowDateStr = date("Y-m-d H:i:s", time());
		    
		    $sql="select * from  xbmall_goods_activity  where act_id={$act_id} and  is_finished=0  ";
		    $goods_activity = getRow($sql);
		    if($goods_activity==null || $goods_activity==false){
		       
		        die("报名已经截止了");//报名已经截止了
		    }
		    
		    
		    $ext_info = unserialize($goods_activity['ext_info']);
		    $goods_activity = array_merge($goods_activity, $ext_info);
		    
		    $operateUser = $_COOKIE['ECS']['username'];
		    $sql = "select * from xbmall_users where user_name='{$operateUser}'";
		    $userInfo =  getRow($sql);
		    
		    if($userInfo['pay_points']<$goods_activity['price']){
		        die("您的积分不足".$goods_activity['price']);
		    }
		    
		    $sql=" insert  into  xbmall_hd_chou_jiang_bao_ming(act_id, user_name, real_name, state, jf)
              values({$act_id},'{$operateUser}','{$userInfo['real_name']}',0,{$goods_activity['price']})   ";
  		    $res = mysql_query($sql);
  		    
  		    //扣除用户的消费积分
  		    
  		     
  		    if($goods_activity['act_type']==6){
  		        $sql = "insert into zt_xxtzzs (user,zsrq,xxtzzxfjz,comment) values('{$operateUser}',
              '{$nowDateStr}',-{$goods_activity['price']},'参与幸运抽奖{$goods_activity['goods_name']},使用消费积分{$goods_activity['price']}') ";
  		        mysql_query($sql);
  		        
  		    }
  		    die("报名成功");
	 
		?>
	 

 