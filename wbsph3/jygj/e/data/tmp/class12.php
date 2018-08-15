<?php
if(!defined('InEmpireCMS'))
{
	exit();
}
?><!DOCTYPE html>
<html>
<head lang="en">
<meta charset="UTF-8">
    <title>网站地图</title>
    <meta name="keyword" content=""/>
    <meta name="description" content=""/>
    <link href="Public/ico.ico" type="image/x-icon" rel="shortcut icon" /> 
    <link rel="stylesheet" type="text/css" href="/jygj/Public/home/css/style.css">
    <script type="text/javascript" src="/jygj/Public/home/js/jquery-1.8.2.js"></script>
    <script type="text/javascript" src="/jygj/Public/home/js/jquery.flexslider-min.js"></script>
    <script type="text/javascript" src="/jygj/Public/home/js/MSClass1.65.js"></script>
 <script type="text/javascript" src="/js/index.js"></script>

</head>
<body>

<?
 $nclassid =$GLOBALS[navclassid];
?>



<div>
       <script type="text/javascript">

function browserRedirect() { 

var sUserAgent= navigator.userAgent.toLowerCase(); 

var bIsIpad= sUserAgent.match(/ipad/i) == "ipad"; 

var bIsIphoneOs= sUserAgent.match(/iphone os/i) == "iphone os"; 

var bIsMidp= sUserAgent.match(/midp/i) == "midp"; 

var bIsUc7= sUserAgent.match(/rv:1.2.3.4/i) == "rv:1.2.3.4"; 

var bIsUc= sUserAgent.match(/ucweb/i) == "ucweb"; 

var bIsAndroid= sUserAgent.match(/android/i) == "android"; 

var bIsCE= sUserAgent.match(/windows ce/i) == "windows ce"; 

var bIsWM= sUserAgent.match(/windows mobile/i) == "windows mobile"; 

if (bIsIpad || bIsIphoneOs || bIsMidp || bIsUc7 || bIsUc || bIsAndroid || bIsCE || bIsWM) { 

window.location.href= '/wap/wap.html'; 

} 

} 

browserRedirect(); 

</script>
<style type="text/css">
		.header .menu  ul .li1 a:hover{
		font-size: 110%;
	}
</style>
	

<div class="header">
        <div class="head">
			<div class="warp_p_right_ul_li_1">
				<span class="warp_p_right_ul_li_1_sp"><i></i>聚元国际</span>
				<div class="warp_p_right_ul_li_1_div">
					<img src="/Uploads/20161119/582fbf8526bdf.png">
				</div>
			</div>
		    
			<a href="/wangzhanditu/">
                网站地图
            </a>
			<div style="float: right;position:relative;" class="yuyan">
				<a href="#" class="china">
					<img src="/jygj/Public/home/images/coin3.png">
					<span>中文</span>
				</a>
				<a href="#" class="english">
					<img src="/jygj/Public/home/images/coin12.png">
					<span>英文</span>
				</a>
			</div>
        </div>

        <div class="menu">
            <h1>
                <a href="/">
                    <img src="/images/logo.png">
                </a>
            </h1>
            <ul>
                
				 <? 
	   				$nclassid =$GLOBALS[navclassid];	
	   				
		   			$arr=array(2,1,3,4,9,5,6,8,7);
			   		$arrlength=count($arr);
			   		for($x=0;$x<$arrlength;$x++)
					{ $id=$arr[$x];
					if($id==$nclassid) {
				?><li class='li1 choice'>
				
				<?
					} else {
				?>
				<li class='li1'>
				<?}
					if($id==9) {
				?>
				<a href="/mall/"><?=$class_r[$id]['classname']?></a>
					
						
				<?
					}
					else if($id==2) {
				?>
				<a href="/<?=$class_r[$id]['classpath']?>/2016-12-30/220.html"><?=$class_r[$id]['classname']?></a>
					
						
				<?
					} else if($id==6) {
				?>
					<a href="/<?=$class_r[$id]['classpath']?>/2016-12-30/223.html"><?=$class_r[$id]['classname']?></a>
					
						
				<?
					}
					else if($id==3) {
				?>
				<a href="/<?=$class_r[$id]['classpath']?>/2016-12-30/221.html"><?=$class_r[$id]['classname']?></a>
					
						
				<?
					} else if($id==4) {
				?>
					<a href="/<?=$class_r[$id]['classpath']?>/2016-12-30/222.html"><?=$class_r[$id]['classname']?></a>
					
						
				<?
					} else {
				?>
					<a href="/<?=$class_r[$id]['classpath']?>"><?=$class_r[$id]['classname']?></a>
					
						
				<?
				}
				?>
				  </li>
				<?
					
					}
				?>          
            </ul>
        </div>
    </div>
 <div class="banner">
        <div>
            <img src="/images/<?=$nclassid?>.png">
        </div>
    </div>
    <div class="sub_content">
        <div class="sub_l">
       
<?
	$sqly=$empire->query("select * from `phome_enewsad` where `classid` = 12 order by `adid` desc limit 0,3");
	while ( $ry = $empire ->fetch( $sqly )){
?>
	 <a href="<?=$ry[url]?>"  target="_blank"><img src="<?=$ry[picurl]?>"></a>
<?
	}
?>
        </div>


        <div class="sub_r">
			<div class="wzdt">
				<img src="/jygj/Public/home/images/wzdt.jpg">
				<a href="/" class="map1"></a>
				<a href="/zizhirongyu/" class="map2"></a>
				<a href="/zuzhijiagou/" class="map3"></a>
				<a href="/yunzuomoshi/" class="map4"></a>
				<a href="/bangonghuanjing/" class="map5"></a>
				<a href="/guanyuwomen/" class="map6"></a>
				<a href="/shipinzhongxin/" class="map7"></a>
				<a href="/lianxiwomen/2016-12-30/219.html" class="map8"></a>
				<a href="#" class="map9"></a>
			</div>
        </div>
    </div>
       



            <div class="footer">
	 
        <div class="dbmp_p" style="width: 520px;">
			<div class="footer_l">
				版权所有：安徽诺斯贝尔电子商务有限公司  &nbsp;&nbsp;&nbsp;皖ICP备17000434号  &nbsp;&nbsp;&nbsp;技术支持：<a href="http://www.zt315.cn/" target="_blank">滁州中天科技</a>
			</div>
			<div class="footer_r">
				<a href="/yinsishengming/2016-12-13/73.html">隐私声明</a> |
				<a href="/lianxiwomen/2016-12-30/219.html">联系我们</a> |
				<a href="/guanyuwomen/2016-12-30/223.html">关于我们</a>
			</div>
		</div>
		
		<style>
		.dbmp_o li img{width: 90px;height: 38px}
		</style>
		<ul class="dbmp_o">
		    <li>
			<a id='___szfw_logo___' href='https://credit.szfw.org/CX20170124033111820139.html' target='_blank'><img src='http://icon.szfw.org/cert.png' border='0' /></a>
<script type='text/javascript'>(function(){document.getElementById('___szfw_logo___').oncontextmenu = function(){return false;}})();</script>
		    </li>
<li>
					<a href='https://credit.szfw.org/CX20170124033111250287.html' target='_blank'><img src='http://icon.szfw.org/cert.png' border='0' /></a>
<script type='text/javascript'>(function(){document.getElementById('___szfw_logo___').oncontextmenu = function(){return false;}})();</script>
				</li>
			<li>
				<a id='___szfw_logo___' href='https://credit.szfw.org/CX20170124033111550671.html' target='_blank'><img src='http://icon.szfw.org/longtou.png' border='0' /></a>
<script type='text/javascript'>(function(){document.getElementById('___szfw_logo___').oncontextmenu = function(){return false;}})();</script>
			</li>
			<li>
				<a id='___szfw_logo___' href='https://credit.szfw.org/CX20170124033111070492.html' target='_blank'><img src='http://icon.szfw.org/sf.png' border='0' /></a>
<script type='text/javascript'>(function(){document.getElementById('___szfw_logo___').oncontextmenu = function(){return false;}})();</script>
			</li>
		</ul>
		
		
		
    </div>
</div>
</body>
</html>
		<script>
			$(function(){
					$(window).scroll(function() {
						var d1=$(document).scrollTop();
						if(d1>257){
							var d2=d1-257;
							$(".sub_l").css("top",d2);
						}else{
							$(".sub_l").css("top",0);
						}

					})
			})
		</script>
