<?php
if(!defined('InEmpireCMS'))
{
	exit();
}
?><!DOCTYPE html>
<html>
<head lang="en">
<meta charset="UTF-8">
    <title><?=$grpagetitle?></title>
    <meta name="keyword" content="<?=$grpagetitle?>"/>
    <meta name="description" content="<?=$ecms_gr[keyboard]?>"/>
    <link href="Public/ico.ico" type="image/x-icon" rel="shortcut icon" /> 
    <link rel="stylesheet" type="text/css" href="/jygj/Public/home/css/style.css">
    <script type="text/javascript" src="/jygj/Public/home/js/jquery-1.8.2.js"></script>
    <script type="text/javascript" src="/jygj/Public/home/js/jquery.flexslider-min.js"></script>
    <script type="text/javascript" src="/jygj/Public/home/js/MSClass1.65.js"></script>
 <script type="text/javascript" src="/js/index.js"></script>
<style type="text/css">
	.spwk {
    width: 236px;

    float: left;
    margin-right: 0px;
    margin-bottom:5px;


margin-top:20px;
margin-left:7px;

}
.spwk tr{
display:block;

}
</style>
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

<table width="100%" border="0" cellpadding="0" cellspacing="0" class="title_info" style="text-align: center;">
<tr>
<td><h1><font style=" line-height: 40px;"><?=$ecms_gr[title]?></font></h1></td>
</tr>
<tr>
<td class="info_text">时间：<?=date('Y-m-d H:i:s',$ecms_gr[newstime])?>&nbsp;&nbsp;来源：<?=$docheckrep[1]?ReplaceBefrom($ecms_gr[befrom]):$ecms_gr[befrom]?>&nbsp;&nbsp;作者：<?=$docheckrep[2]?ReplaceWriter($ecms_gr[writer]):$ecms_gr[writer]?></td>
</tr>
</table>
<?php

if($navinfor[video]<>""){
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="text-align: center;">
<tr>
<td id="text2">
<script type="text/javascript" src="/player/js/swfobject.js"></script>
<div class="video" id="CuPlayer" style="margin-top:15px;"><b>极酷网页视频播放器加载中，请稍后...</b></div>
<script type="text/javascript">
var so = new SWFObject("/player/player.swf","ply","600","410","9","#000000");
so.addParam("allowfullscreen","true");
so.addParam("allowscriptaccess","always");
so.addParam("wmode","opaque");
so.addParam("quality","high");
so.addParam("salign","lt");
so.addVariable("JcScpFile","/player/CuSunV4set.xml");
so.addVariable("JcScpVideoPath","<?=$ecms_gr[video]?>");
so.addVariable("JcScpImg","player/Images/flashChangfa2.jpg");
so.addVariable("JcScpSharetitle","<?=$ecms_gr[title]?>");
so.write("CuPlayer");
</script>
<script language=javascript src="/player/js/jquery-1.4.2.min.js" type=text/javascript></script>
<script language=javascript src="/player/js/action.js" type=text/javascript></script>
</td>
</tr>
</table>
<?}?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">


<tr>
<td id="text"><?=strstr($ecms_gr[newstext],'[!--empirenews.page--]')?'[!--newstext--]':$ecms_gr[newstext]?>
<p align="center" class="pageLink">[!--page.url--]</p></td>
</tr>
</table>


		
   
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
