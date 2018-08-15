<?php
if(!defined('InEmpireCMS'))
{
	exit();
}
?><!DOCTYPE html>
<html>
<head lang="en">
<meta charset="UTF-8">
    <title>[!--pagetitle--]</title>
    <meta name="keyword" content="[!--pagedes--]"/>
    <meta name="description" content="[!--pagekey--]"/>
    <link href="Public/ico.ico" type="image/x-icon" rel="shortcut icon" /> 
    <link rel="stylesheet" type="text/css" href="[!--news.url--]Public/home/css/style.css">
    <link rel="stylesheet" type="text/css" href="[!--news.url--]Public/home/css/page.css">
    <script type="text/javascript" src="[!--news.url--]Public/home/js/jquery-1.8.2.js"></script>
 <script type="text/javascript" src="[!--news.url--]js/index.js"></script>
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
.over ul li{display: none}
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

window.location.href= '[!--news.url--]wap/wap.html'; 

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
					<img src="[!--news.url--]Uploads/20161119/582fbf8526bdf.png">
				</div>
			</div>
		    
			<a href="[!--news.url--]wangzhanditu/">
                网站地图
            </a>
			<div style="float: right;position:relative;" class="yuyan">
				<a href="#" class="china">
					<img src="[!--news.url--]Public/home/images/coin3.png">
					<span>中文</span>
				</a>
				<a href="#" class="english">
					<img src="[!--news.url--]Public/home/images/coin12.png">
					<span>英文</span>
				</a>
			</div>
        </div>

        <div class="menu">
            <h1>
                <a href="[!--news.url--]">
                    <img src="[!--news.url--]images/logo.png">
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
				<a href="[!--news.url--]mall/"><?=$class_r[$id]['classname']?></a>
					
						
				<?
					}
					else if($id==2) {
				?>
				<a href="[!--news.url--]<?=$class_r[$id]['classpath']?>/2016-12-30/220.html"><?=$class_r[$id]['classname']?></a>
					
						
				<?
					} else if($id==6) {
				?>
					<a href="[!--news.url--]<?=$class_r[$id]['classpath']?>/2016-12-30/223.html"><?=$class_r[$id]['classname']?></a>
					
						
				<?
					}
					else if($id==3) {
				?>
				<a href="[!--news.url--]<?=$class_r[$id]['classpath']?>/2016-12-30/221.html"><?=$class_r[$id]['classname']?></a>
					
						
				<?
					} else if($id==4) {
				?>
					<a href="[!--news.url--]<?=$class_r[$id]['classpath']?>/2016-12-30/222.html"><?=$class_r[$id]['classname']?></a>
					
						
				<?
					} else {
				?>
					<a href="[!--news.url--]<?=$class_r[$id]['classpath']?>"><?=$class_r[$id]['classname']?></a>
					
						
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
            <img src="[!--news.url--]images/<?=$nclassid?>.png">
        </div>
    </div>
    <div class="sub_content">
       <div class="sub_l" style="margin-top: 10px;">
           
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
			<!--<img src="[!--news.url--]Uploads/20161025/580eceb8e86c0.jpg" style="width: 729px;height: 318px" />-->
<h1 label="标题居中" style="font-size: 32px;
		font-weight: bold;
		border-bottom-color: rgb(204, 204, 204);
		border-bottom-width: 2px;
		border-bottom-style: solid;
		padding: 0px 4px 0px 0px;
		text-align: center;"><span style="font-size: 16px;
		line-height: 1.5em;
		text-indent: 2.2em;
		color: rgb(0, 112, 192);
		font-family:宋体"><strong><span style="color: rgb(0, 112, 192);
		line-height: 1.5em;
		text-indent: 2.2em;
		font-size: 16px;">
		[!--class.name--]</span></strong></span></h1>
            <ul class="zjzs">
			[!--empirenews.listtemp--]
            <!--list.var1-->
<!--list.var2-->
<!--list.var3-->
<!--list.var4-->
<!--list.var5-->
<!--list.var6-->
<!--list.var7-->
<!--list.var8-->
<!--list.var9-->

[!--empirenews.listtemp--]



            </ul>
            <div class="mb" style="display: none;">
               
            </div>
			 <table width="729"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="21" style="text-align: center;"><div class="epages">[!--show.listpage--]</div></td>
  </tr>
</table>
            <div class="over" style="left: 393px; top: -72px;">
                <ul>
				
                </ul>
            </div>
        </div>
    </div>
            <div class="footer">
	 
        <div class="dbmp_p" style="width: 520px;">
			<div class="footer_l">
				版权所有：安徽诺斯贝尔电子商务有限公司  &nbsp;&nbsp;&nbsp;ICP经营许可证编号：皖B2-20170076  &nbsp;&nbsp;&nbsp;技术支持：<a href="http://www.zt315.cn/" target="_blank">滁州中天科技</a>
			</div>
			<div class="footer_r">
				<a href="[!--news.url--]yinsishengming/2016-12-13/73.html">隐私声明</a> |
				<a href="[!--news.url--]lianxiwomen/2016-12-30/219.html">联系我们</a> |
				<a href="[!--news.url--]guanyuwomen/2016-12-30/223.html">关于我们</a>
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
		<script>
		$(function() {
			var con=$(".sub_r .zjzs").html();
			$(".over ul").append(con);
		})
			$(function(){
        $(".zjzs li").click(function(){
			var v1=$(this).index();
			var v0=$(this).html();
			var v8=$(".over li:eq("+v1+")").html();
			
			if(v8==1){
				return;
			}else{
				$(".mb").show();
			}

            $(".over li:eq("+v1+")").show();
            var v2=$(".mb").width();
            var v3=$(".mb").height();
            var v4=$(".over li:eq("+v1+")").width();
            var v5=$(".over li:eq("+v1+")").height();
            var v6=(v2-v4)/2;
            var v7=(v3-v5)/2;
            $(".over").css("left",v6)
            $(".over").css("top",v7)
        });
        $(".mb").click(function(){
            $(".mb").hide();
            $(".over ul li").hide();
        })
    })
</script>
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
		</script></body></html>
