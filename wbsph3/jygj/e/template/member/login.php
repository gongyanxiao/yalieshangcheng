<!DOCTYPE html>
<html><head lang="en">
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="UTF-8">
    <title>聚升国际|黑龙江克络诺斯电子商务有限公司</title>
    <meta name="keyword" content="聚升国际|黑龙江克络诺斯电子商务有限公司">
    <meta name="description" content="聚升国际聚升国际|黑龙江克络诺斯电子商务有限公司">
    <link href="http://jsgj.data.almighty251206.hljdata.net/Public/ico.ico" type="image/x-icon" rel="shortcut icon"> 	
    <link rel="stylesheet" type="text/css" href="http://js.0550cz.com/Public/home/css/index.css">
    <script type="text/javascript" src="http://js.0550cz.com/js/jquery-1.js"></script>
	    <script language="javascript" type="text/javascript">
        $(document).ready(function(){
            $('.warp_z_ul li').click(function(){
                $('.warp_z_ble_zc li').hide();
                $('.warp_z_ble_zc li').eq($(this).index()).show()
            });
        });
    </script>
</head>
<body onkeydown="on_return();">
<div class="warp">
    <div class="warp_v_home">
        <div class="warp_v">
            <a href="/wangluopingtai/"><img src="http://js.0550cz.com/images/logo.png" width="224" height="73"></a>
            <span>用户登陆</span>
        </div>
    </div>

    <div class="warp_z">
        <div class="warp_z_mor">
            <div class="warp_z_div">
                <ul class="warp_z_ul">
                    <li value="1" class="thisclass"><span>会员登陆</span></li>
                    <li value="2"><span>商家登陆</span></li>
                    <li value="3"><span>代理登陆</span></li>
                </ul>
				 <form name="form1" method="post" action="../doaction.php">
					<table class="warp_z_ble">
    <input type=hidden name=ecmsfrom value="<?=ehtmlspecialchars($_GET['from'])?>">
    <input type=hidden name=enews value=login>
	<input name="tobind" type="hidden" id="tobind" value="<?=$tobind?>">
						<input id="flg" class="warp_z_ble_ex" name="flg" type="hidden">
						<tbody><tr>
							<td><b>请输入用户名：</b></td>
						</tr>
						<tr>
							<td><input class="warp_z_ble_ex" name="username" type="text" id="username"></td>
						</tr>
						<tr>
							<td><b>请输入密码：</b></td>
						</tr>
						<tr>
							<td><input class="warp_z_ble_pas" name="password" type="password" id="password"></td>
						</tr>
						<tr>
							<td>
								<div class="warp_z_ble_mp">
									<a href="../GetPassword/">找回密码</a>
									<label>
										<input checked="checked" type="checkbox" name=lifetime value="1209600">
										<span>两周内自动登陆</span>
									</label>
								</div>
							</td>
						</tr>
						<tr>
							<td><input value="登 陆" type="submit" name="Submit" class="warp_z_ble_ton"></td>
						</tr>
						<tr>
							<td>
								<ul class="warp_z_ble_zc">
								   <li>
										<a href="#" onclick="parent.location.href='../register/<?=$tobind?'?tobind=1':''?>';return false;">会员注册新账号</a>
								   </li>
								   <li style="display: none">
									    <a href="http://jsgj.data.almighty251206.hljdata.net/business/register.html">商家注册新账号</a>
								   </li>
								   
								</ul>
								<!---<h4 class="warp_z_ble_zh">使用合作账号登陆</h4>-->
							</td>
						</tr>
						<!---<tr>
							<td>
								<ul class="warp_z_ble_zcfs">
								   <li>
										<a href="#"><img src="/jygj/Public/home/img/dlfs_qq.png"></a>
								   </li>
								   <li>
										<a href="#"><img src="/jygj/Public/home/img/dlfs_wb.png"></a>
								   </li>
								</ul>
							</td>
						</tr>-->
					</tbody></table>
				</form>	
            </div>
            
            <img src="http://js.0550cz.com/images/im132.jpg" width="649" height="399">

        </div>
		<script>
		function deng(){
			var a=$(".thisclass").val()
			var n=$("#n").val();
			var p=$("#p").val();
			if(n==''){
				alert("用户名不能为空");
				return false;
			}
			if(p==''){
				alert("密码不能为空");
				return false;
			}
			$("#flg").attr('value',a);
			$("#form").submit();
		}
			
		//回车时，默认是登陆
		 function on_return(){
		  if(window.event.keyCode == 13){
		   if (document.all('sub')!=null){
			  document.all('sub').click();
			  }
		  }
		 }
		</script>
    </div>

   <div class="warp_e">
	<div class="warp_e_more">

		<div class="warp_e_lx">
			<p>联系我们</p>
			<b>4000-580-997</b>
			<span>地址：黑龙江省哈尔滨市江北区科技创新城</span>
			<span>邮箱：2355638615@qq.com</span>
		</div>

		<div class="warp_e_div">
							<ul>
				<p style="background: url(/Public/home/img/im70.jpg) no-repeat left center;background-position: 40px">帮助中心</p>
	
             				    		<li><a href="/wangluopingtai/bangzhuzhongxin/2016-12-17/196.html">常见问题</a></li>
			    			    		<li><a href="/wangluopingtai/bangzhuzhongxin/2016-12-17/197.html">找回密码</a></li>
			    			    		<li><a href="/wangluopingtai/bangzhuzhongxin/2016-12-17/198.html">联系客服</a></li>
			    			    		<li><a href="/wangluopingtai/bangzhuzhongxin/2016-12-17/199.html">如何注册</a></li>
			    			    		<li><a href="/wangluopingtai/bangzhuzhongxin/2016-12-17/200.html">如何充值</a></li>
			     
								</ul>
								<ul>
				<p style="background: url(/Public/home/img/im71.jpg) no-repeat left center;background-position: 40px">支付方式</p>
					<li><a href="/wangluopingtai/zhifufangshi">支付方式</a></li>
             
								</ul>
								<ul>
				<p style="background: url(/Public/home/img/im72.jpg) no-repeat left center;background-position: 40px">新手分类</p>
	
             				    		<li><a href="/wangluopingtai/xinshoufenlei/2016-12-17/201.html">购买流程</a></li>
			    			    		<li><a href="/wangluopingtai/xinshoufenlei/2016-12-17/202.html">商家说明</a></li>
			     
								</ul>
								<ul>
				<p style="background: url(/Public/home/img/im73.jpg) no-repeat left center;background-position: 40px">配送方式</p>
	
             				    		<li><a href="/wangluopingtai/peisongfangshi/2016-12-17/203.html">交易条款</a></li>
			    			    		<li><a href="/wangluopingtai/peisongfangshi/2016-12-17/204.html">配送时间和范围</a></li>
			    			    		<li><a href="/wangluopingtai/peisongfangshi/2016-12-17/205.html">配送说明</a></li>
			    			    		<li><a href="/wangluopingtai/peisongfangshi/2016-12-17/206.html">运费说明</a></li>
			    			    		<li><a href="/wangluopingtai/peisongfangshi/2016-12-17/207.html">退货退款说明</a></li>
			     
								</ul>
								<ul>
				<p style="background: url(/Public/home/img/im74.jpg) no-repeat left center;background-position: 40px">关于我们</p>
	
             				    		<li><a href="/wangluopingtai/guanyuwomen/2016-12-17/208.html">加入我们</a></li>
			    			    		<li><a href="/wangluopingtai/guanyuwomen/2016-12-17/209.html">关于我们</a></li>
			     
								</ul>
				    		

				</div>

	</div>
</div>

<div class="warp_e_db">
	<div style="width: 1200px;background: #444444;overflow: hidden;margin: auto;padding: 15px 0">
		<span>版权所有：<a href="jsgj.data.almighty251206.hljdata.html">克络诺斯企业集团</a></span><span>技术支持：<a href="http://www.zt315.cn/" target="_blank">中天科技</a></span>
		
		<style>
		.zj_p{width: 438px;overflow: hidden;margin: auto;padding: 20px 0}
		.zj_p ul{width: 500px;overflow: hidden}
		.zj_p ul li{width: 100px;overflow: hidden;float: left;margin-right: 12px}
		.zj_p ul li img{display: block;width: 100px; height: 40px}
		</style>
		<div class="zj_p">
			<ul>
				<li>
				   <a id="___szfw_logo___" href="https://credit.szfw.org/CX20161107027133370161.html" target="_blank"><img src="http://icon.szfw.org/cert.png" border="0"></a>
				   <script type="text/javascript">(function(){document.getElementById('___szfw_logo___').oncontextmenu = function(){return false;}})();</script>
				</li>
				<li>
					<a href="t.knet.cn/index_new.jsp" target="_blank"><img src="/jygj/Public/home/img/kxwzsfyz.png"></a>
				</li>
				<li>
					<a href="http://www.bcpcn.com/product/rz/bq/BCP2135725442.html" target="_blank"><img src="http://www.bcpcn.com/bcptags/img/cyqy.png" border="0"></a>
				</li>
				<li>
					<a href="http://www.itrust.org.cn/Home/Index/itrust_certifi?wm=1A18YEKB78" target="_blank"><img src="/jygj/Public/home/img/wxrz.jpg"></a>
				</li>
			</ul>
		</div>
	</div>
	
</div>
	
</div>
<!----------------------------点击变色开始-------------------------->
<script>
    $(function(){
        var cotrs = $(".warp_z_ul li");
        cotrs.click(function(){
            $(this).addClass("thisclass").siblings().removeClass("thisclass");
        });
    });
</script>
<!----------------------------点击变色结束-------------------------->

</div></body></html>