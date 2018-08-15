<?php
if(!defined('InEmpireCMS'))
{
	exit();
}
?><!DOCTYPE html>
<html>
<head lang="en">
<meta charset="UTF-8">
    <title>聚元国际|安徽诺斯贝尔电子商务有限公司</title>
    <meta name="keyword" content="安徽诺斯贝尔电子商务有限公司"/>
    <meta name="description" content="安徽诺斯贝尔电子商务有限公司"/>
    <link href="Public/ico.ico" type="image/x-icon" rel="shortcut icon" /> 
    <link rel="stylesheet" type="text/css" href="Public/home/css/style.css">
    <script type="text/javascript" src="Public/home/js/jquery-1.8.2.js"></script>
    <script type="text/javascript" src="Public/home/js/jquery.flexslider-min.js"></script>
    <script type="text/javascript" src="Public/home/js/MSClass1.65.js"></script>
    <link href="css/lanrenzhijia.css" rel="stylesheet">
 <script type="text/javascript" src="/js/index.js"></script>
<script type="text/javascript" src="/js/responsiveslides.min.js"></script>
<style type="text/css">
	
	.header .menu  ul .li1 a:hover{
		font-size: 110%;
	}
  .content_r a {
  	width: 100%
  }
</style>
</head>

<body>





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
    <div class="flexslider">
        <ul class="slides">      

		<?
	$sqly=$empire->query("select * from `phome_enewsad` where `classid` = 11 order by `adid` desc limit 0,5");
	while ( $ry = $empire ->fetch( $sqly )){
?>
	<li style="background:url(<?=$ry[picurl]?>) 50% 0 no-repeat;"></li>
<?
	}
?>
</ul>
    </div>
<div style="width:100%;background: #E9E9E9 url(/images/sy.png);">
    <div class="content">
        <div class="content1">
            <div class="content_l">
                <div class="content_head">
                    <img src="Public/home/images/coin2.png">
                    <span style="font-family: '微软雅黑';">关于我们</span>
                </div>
                <div class="content_con">
                    <img src="Public/home/images/im1.jpg">
<div style="margin-left: 5px;margin-top: 5px">
     <p style="line-height: 24px;float: right;width: 250px;">
     	<font style="font-size: 14px;font-family: '微软雅黑';font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;安徽诺斯贝尔电子商务有限公司</font>
     	<font style="font-size: 13px;font-family: '微软雅黑';">(以下简称公司)是互联网+模式创新迅速发展中组建的提高型平台化电子商务企业。“聚元国际网络服务平台” 是我们倾力打造的互联网公众品牌。</font>
     </p>  
                    
    <a href="/guanyuwomen/2016-12-30/223.html" style="color:#133984; font-family: '微软雅黑';">
    	关于我们 >
    </a>
</div>
                 
                </div>
            </div>
            <div class="content_r">
                <div class="content_head">
                    <img src="Public/home/images/coin2.png">
                    <span style="font-family: '微软雅黑';">聚元国际</span>
                </div>
                <div class="content_r_con">   
                 <div class="slide_container">
                          <ul class="rslides" id="slider">
                            	<?
	$sqly=$empire->query("select * from `phome_enewsad` where `classid` = 13 order by `adid` desc limit 0,5");
	while ( $ry = $empire ->fetch( $sqly )){
?>
	<li>
      <a href="<?=$ry[url]?>"><img src="<?=$ry[picurl]?>" alt=""></a>
    </li>
<?
	}
?>
                          </ul>

                        </div>
  <script>
                    $(function () {
                        $("#slider").responsiveSlides({
                        auto: true,
                        pager: true,
                        nav: false,
                        speed: 1000,
                        // 对应外层div的class : slide_container
                        namespace: "slide"
                        });
                    });
                    </script>

                    
                   
                </div>
            </div>
        </div>
        <div class="content2">
            <div class="content_head">
                <img src="Public/home/images/coin2.png">
                <span style="font-family: '微软雅黑';">友情链接</span>
            </div>
            <div id="marqueediv6" style="width:960px;height:75px;overflow:hidden;">
                <div>
					
                   <?php
$bqno=0;
$ecms_bq_sql=sys_ReturnEcmsLoopBq('select * from phome_enewslink order by lid',3,24,0);
if($ecms_bq_sql){
while($bqr=$empire->fetch($ecms_bq_sql)){
$bqsr=sys_ReturnEcmsLoopStext($bqr);
$bqno++;
?>
<a href="<?=$bqr[lurl]?>" title="<?=$bqr[lname]?>" target="_blank"><img src="<?=$bqr[lpic]?>"/></a>
<?php
}
}
?>
                </div>
            </div>
        </div>
    </div>
	       <div class="footer">
	 
        <div class="dbmp_p" style="width: 520px;">
			<div class="footer_l">
				版权所有：安徽诺斯贝尔电子商务有限公司  &nbsp;&nbsp;&nbsp;ICP经营许可证编号：皖B2-20170076  &nbsp;&nbsp;&nbsp;技术支持：<a href="http://www.zt315.cn/" target="_blank">滁州中天科技</a>
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
</div>
</body>
</html>
<script type="text/javascript">
    $(document).ready(function(){
        $('.flexslider').flexslider({
            directionNav: true,
            pauseOnAction: false
        });
    }); 
</script>
<!----------------------------点击变色开始-------------------------->
<script>
    $(function(){
        var v1=$("#marqueediv6 a").length;
        var v2=v1*86;
        $("#marqueediv6 div").width(v2);
    });
    new Marquee("marqueediv6",2,1,960,75,20,0,0)
</script>
<!----------------------------点击变色结束-------------------------->