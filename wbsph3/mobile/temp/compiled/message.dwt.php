<!DOCTYPE html >
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width">
  <title><?php echo $this->_var['page_title']; ?></title>
  <meta name="Keywords" content="<?php echo $this->_var['keywords']; ?>" />
  <meta name="Description" content="<?php echo $this->_var['description']; ?>" />
  <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
  <link rel="stylesheet" href="themesmobile/default/css/loginxin.css">
  <link rel="stylesheet" href="themesmobile/default/css/public.css" >
  </head>

<body>
    <header class="header_03">
      <div class="nl-login-title">
        <div class="h-left">
          <a class="sb-back" href="javascript:history.back(-1)" title="返回"></a>
        </div>
        <span style="text-align:center">系统提示</span>
      </div>
    </header>
    
 <script type="text/javascript" src="themesmobile/default/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="/js/jquery.cookie.js"></script>
 <script type="text/javascript" src="js/layer/layer.js" ></script>
<script>
          
          
          //定时器对象,totalMSecond(总共要执行的毫秒数);mSecondsecond(间隔的毫秒数);method(要执行的方法);args(方法的参数);
          function TimerMSecond(totalMSecond,mSecond, method,args) {
		     this.totalMSecond=totalMSecond;
		     this.mSecond = mSecond;
		     this.method= method;
		     this.args = args;
		     this._stop= false;//是否结束
		     this.stop=function(){
		       this._stop=true;
		     };
		     this.start=function(timer){//timer:创建的TimerMSecond对象
		     	if(this._stop)return ;
		     	 this.totalMSecond =this.totalMSecond-this.mSecond;
		     	 if(this.totalMSecond>0){//如果总共的时间大于0;把剩余的毫秒数传给方法
		     	   this.method(this.totalMSecond,this.args);//执行方法
		     	   setTimeout(function(){timer.start(timer);}, this.mSecond);//继续下个定时
		     	 }
		     };
		 }
		 
		 
          var isCanClose = false;
         
          //关闭弹出窗口
          function closePopWin(){
	           if(isCanClose){
	             layer.closeAll();
	           }
          }
     
        
        var timer;
       	function daoJiShi(text,second){//倒计时
	 	     if(timer)timer.stop();//停止上个计时器
	 	     timer = new TimerMSecond(second*1000,1000,setBtnText,text);
	 	     timer.start(timer);//开始新的计时器
	 	     
       	}   
		function setBtnText(mSecond,tipText) {
		    $(".layui-layer-btn0").text(tipText+"（" + (mSecond/1000) + "秒）");
		}
		
		  
	   
		$(document).ready(function(){
		    if("<?php echo $this->_var['dengLuChengGong']; ?>"==""){
		      return ;
		    } 
			if(!$.cookie('lingQuHongBao')){//还没弹出过红包
			  //  popHongBao();
			  //  $.cookie('lingQuHongBao',"已领取");
			}else{//cookie中记录了弹出过
				$.ajax({
		            url: "/mobile/user.php?act=is_hong_bao",
		            type: "POST",
		            data: {},
		            dataType:"text",
		            success: function (result) {
		            	if(result!=0){//有没有领取的红包
		            		popHongBao();
		            	}
		            }
		          });
			
			}
		});
		
		
		 
          function popHongBao(){
          		layer.open({
	                            type: 2
	                            , title: " "
	                            , closeBtn: false
	                            , area: ['100%','100%']
	                            , id: 'LAY_layuipro' //设定一个id，防止重复弹出
	                            , btn: ['领取红包（3秒）']
	                            , btnAlign: 'r'
	                            , content: ["http://"+document.domain+"/themes/default/huodong/mobile/index.html?","no"]
	                            , yes: function (layero) {
	                                closePopWin();
	                            }
	                        });
	                       
	                 
          }
          
          
			
</script>


    <div class="tishimain"><?php echo $this->_var['message']['content']; ?></div>
    <?php if ($this->_var['message']['url_info']): ?>
    <div class="tishi">
      <?php $_from = $this->_var['message']['url_info']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('info', 'url');if (count($_from)):
    foreach ($_from AS $this->_var['info'] => $this->_var['url']):
?>
      <a href="<?php echo $this->_var['url']; ?>"><span><?php echo $this->_var['info']; ?></span></a>
      <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </div>
    <?php endif; ?>


</body>

</html>