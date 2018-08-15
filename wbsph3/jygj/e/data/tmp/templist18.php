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
    <meta name="keyword" content="[!--pagekey--]"/>
    <meta name="description" content="[!--pagedes--]"/>
    <link href="[!--news.url--]Public/ico.ico" type="image/x-icon" rel="shortcut icon" /> 
    <link rel="stylesheet" type="text/css" href="[!--news.url--]Public/home/css/index.css">
	<link rel="stylesheet" type="text/css" href="[!--news.url--]Public/home/css/page.css">
    <script type="text/javascript" src="[!--news.url--]Public/home/js/jquery-1.7.2.js"></script>
 <script type="text/javascript" src="[!--news.url--]js/index.js"></script>
</head>
<body style="background: #F7F7F7">


<?
	$bclassid=$GLOBALS[navclassid];
?>

<div class="warp">
  <?
$a=array();
$b=array();
$c=array();
$sql0=$empire->query("select * from `phome_enewsclass` where bclassid = 24"); 

while($r0 = $empire ->fetch( $sql0 )){
	//array_push($a,$r0['classid']);
	$a[]=$r0['classid'];
} 
$al=count($a);
for($x=0;$x<$al;$x++)
{ $id=$a[$x];
$sql3=$empire->query("select * from `phome_enewsclass` where classid = $id");
$r3 = $empire ->fetch( $sql3 );
if($r3[sonclass]!=''){	
	$sql4=$empire->query("select * from `phome_enewsclass` where bclassid = $id");
	while($r4 = $empire ->fetch( $sql4 )){
		$b[]=$r4['classid'];
	} 
}
}
$bl=count($b);
for($x=0;$x<$bl;$x++)
{ $id=$b[$x];
$sql5=$empire->query("select * from `phome_enewsclass` where classid = $id");
$r5 = $empire ->fetch( $sql5 );
if($r5[sonclass]!=''){	
	$sql6=$empire->query("select * from `phome_enewsclass` where bclassid = $id");
	while($r6 = $empire ->fetch( $sql6 )){
		$c[]=$r6['classid'];
	} 
}
}

?>
<?
	 $nclassid =$GLOBALS[navclassid];
?>
<div class="warp_p">
        <div class="warp_p_home">
            <div class="warp_p_right">
                <ul class="warp_p_right_ul">
                    <li class="warp_p_right_ul_li">
                        <div class="warp_p_right_ul_li_1">
                            <span class="warp_p_right_ul_li_1_sp"><i></i>聚升国际</span>
                            <div class="warp_p_right_ul_li_1_div">
                                <img src="[!--news.url--]Uploads/20161119/582fbf8526bdf.png">
                            </div>
                        </div>
                    </li>
                    <li class="warp_p_right_ul_li">
                        <div class="warp_p_right_ul_li_2">
							 
							<a href="agent/login.html" class="warp_p_right_ul_li_2_a">账户中心</a>                           
                           
                        </div>
                    </li>
                    <li class="warp_p_right_ul_li">
                        <div class="warp_p_right_ul_li_3">
                            <a href="enterprisea/36.html">联系我们</a>
                        </div>
                    </li>
                    <li class="warp_p_right_ul_li">
                        <div class="warp_p_right_ul_li_4">
                            <span class="warp_p_right_ul_li_4_sp">语言选择：</span>
                            <div class="warp_p_right_ul_li_5">
                                <div class="warp_p_right_ul_li_6">
                                    <a href="#">
                                        <img src="[!--news.url--]Public/home/img/im3.jpg">
                                        <span>中文</span>
                                    </a>
                                </div>
                                <ul class="warp_p_right_ul_li_7">
                                    <li>
                                        <a href="#">
                                            <img src="[!--news.url--]Public/home/img/im3_1.jpg">
                                            <span>英文</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <img src="[!--news.url--]Public/home/img/im3_2.jpg">
                                            <span>俄文</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                        </div>
                    </li>
                </ul>
            </div>

            <span class="warp_p_home_sp">您好，欢迎来到聚升国际			<a href="[!--news.url--]e/member/login/index.php">[登陆]</a><a href="[!--news.url--]e/member/register/ChangeRegister.php">[注册]</a>
						</span>
        </div>
    </div>
<div class="warp_o">
	    <script>
        $(function(){
            $(".fix_v_p").click(function(){
                $(".fix_v_o").show();
            })
            $(".ri-close").click(function(){
                $(".fix_v_o").hide();
            })
        });
    </script>
	<div class="warp_o_home">

		<a href="shop.html" title="聚升国际|黑龙江克络诺斯电子商务有限公司" class="warp_o_logo"><img src="[!--news.url--]images/logo.png" width="224" height="88" alt=""></a>

		<!------城市开始------>
		<div class="fix_v">
			<a href="#" class="fix_v_p">全国<i></i></a>
						

			<div class="fix_v_o">
				<!--<a href="#" class="fix_v_p">全国<i></i></a>-->

				<div class="ri-con">
				<!--	<span class="ri-t">
					  当前全国共有<i>108</i>家分店
					</span>-->
					<a href="javascript:void(0)" class="ri-close">关闭</a>
				</div>


				<div class="bt-infos">
					<dl>
						<dt>省份&nbsp;&gt;</dt>
						<dd>
							<ul>
							<li>
									<a href="business/businesslist.html~region=北京�~.html" target="_blank">北京市</a>
								</li><li>
									<a href="business/businesslist.html~region=天津�~.html" target="_blank">天津市 </a>
								</li><li>
									<a href="business/businesslist.html~region=上海�~.html" target="_blank">上海市 </a>
								</li><li>
									<a href="business/businesslist.html~region=重庆�~.html" target="_blank">重庆市</a>
								</li><li>
									<a href="business/businesslist.html~region=河北�~.html" target="_blank">河北省</a>
								</li><li>
									<a href="business/businesslist.html~region=河南�~.html" target="_blank">河南省</a>
								</li><li>
									<a href="business/businesslist.html~region=黑龙江省.html" target="_blank">黑龙江省</a>
								</li><li>
									<a href="business/businesslist.html~region=浙江�~.html" target="_blank">浙江省</a>
								</li><li>
									<a href="business/businesslist.html~region=福建�~.html" target="_blank">福建省</a>
								</li><li>
									<a href="business/businesslist.html~region=山东�~.html" target="_blank">山东省</a>
								</li><li>
									<a href="business/businesslist.html~region=湖南�~.html" target="_blank">湖南省</a>
								</li><li>
									<a href="business/businesslist.html~region=四川�~.html" target="_blank">四川省</a>
								</li><li>
									<a href="business/businesslist.html~region=陕西�~.html" target="_blank">陕西省</a>
								</li><li>
									<a href="business/businesslist.html~region=青海�~.html" target="_blank">青海省</a>
								</li><li>
									<a href="business/businesslist.html~region=甘肃�~.html" target="_blank">甘肃省</a>
								</li><li>
									<a href="business/businesslist.html~region=山西�~.html" target="_blank">山西省</a>
								</li><li>
									<a href="business/businesslist.html~region=辽宁�~.html" target="_blank">辽宁省</a>
								</li><li>
									<a href="business/businesslist.html~region=吉林�~.html" target="_blank">吉林省</a>
								</li><li>
									<a href="business/businesslist.html~region=江苏�~.html" target="_blank">江苏省</a>
								</li><li>
									<a href="business/businesslist.html~region=安徽�~.html" target="_blank">安徽省</a>
								</li><li>
									<a href="business/businesslist.html~region=江西�~.html" target="_blank">江西省</a>
								</li><li>
									<a href="business/businesslist.html~region=湖北�~.html" target="_blank">湖北省</a>
								</li><li>
									<a href="business/businesslist.html~region=广东�~.html" target="_blank">广东省</a>
								</li><li>
									<a href="business/businesslist.html~region=海南�~.html" target="_blank">海南省</a>
								</li><li>
									<a href="business/businesslist.html~region=贵州�~.html" target="_blank">贵州省</a>
								</li><li>
									<a href="business/businesslist.html~region=云南�~.html" target="_blank">云南省</a>
								</li><li>
									<a href="business/businesslist.html~region=内蒙古自治区.html" target="_blank">内蒙古自治区</a>
								</li><li>
									<a href="business/businesslist.html~region=宁夏回族自治�~.html" target="_blank">宁夏回族自治区</a>
								</li><li>
									<a href="business/businesslist.html~region=新疆维吾尔族自治�~.html" target="_blank">新疆维吾尔族自治区</a>
								</li><li>
									<a href="business/businesslist.html~region=西藏自治�~.html" target="_blank">西藏自治区</a>
								</li>	
								
							</ul>
						</dd>
					</dl>
					
				</div>
			</div>
		</div>
		<!------城市结束------>

		<div class="warp_i">
			<div class="warp_i_div">
			<form action="" method="post" id="form1" >
				<div class="open_xl">
					<a href="#">商品</a>
				</div>
				<input type="text" name="title" class="warp_i_div_ex">
				<input type="button" value="搜索" onclick="sou()" class="warp_i_div_ton">
				<div class="xiala">
					<a href="#">商品</a>
					<a href="#">店铺</a>
				</div>
				<script>
				function sou(){
					q= $(".open_xl a").html();
					
					if(q=='商品'){
					
					$('#form1').attr("action","goods/goodlist.html").submit();
					}else if(q=='店铺'){
					
					$('#form1').attr("action","business/businesslist.html").submit();	
					}
					
				}
				</script>
			</div>
			</form>
			<ul class="warp_i_ul">
				<li><span>热门搜索：</span></li>
				<li><a href="#">手机数码</a></li>
				<li><a href="#">品牌内衣</a></li>
				<li><a href="#">护肤品</a></li>
				<li><a href="#">流行饰品</a></li>
			</ul>
		</div>

	</div>
</div>
<!------logo结束------>
<script>
	$(function(){
		$(".open_xl").click(function(){
			$(".xiala").show();
		})
		$(".xiala a").click(function(){
			var xq=$(this).html();
			$(".open_xl a").html(xq);
			$(this).parent().hide();
		})
	})
</script>

<!------导航开始------>
<div class="warp_u">
	<div class="warp_u_home">

		<div class="warp_u_rigt">
			<span class="warp_u_rigt_sp">客服电话： 400-9922-831</span>
<?
	if($nclassid!=9){
?>
<div class="warp_u_rigt_div" style="display: none;">
<?
 } else {
 ?>
 <div class="warp_u_rigt_div">
 <?
 }
?>

				<ul class="warp_u_rigt_gp">
					<li>
						<a href="member/center.html">
							<img src="[!--news.url--]Public/home/img/im19.png" width="40" height="40">
							<span>会员中心</span>
						</a>
					</li>
					<li>
						<a href="business/orderlist.html">
							<img src="[!--news.url--]Public/home/img/im21.png" width="40" height="40">
							<span>商家中心</span>
						</a>
					</li>
					<li>
						<a href="agent/index.html">
							<img src="[!--news.url--]Public/home/img/im20.png" width="40" height="40">
							<span>代理中心</span>
						</a>
					</li>
				</ul>

				<div class="warp_u_rigt_go">
					<ul class="warp_u_rigt_gi">
						<li class="thisclass" style="margin-left: -1px"><a href="#">网站公告</a></li>
						<li style="margin-right: -1px"><a href="#">商家入驻</a></li>
					</ul>
					<ul class="warp_u_rigt_gu">
						<li class="warp_u_rigt_gu_li">
							<ul class="warp_u_rigt_gy">
								<?php
$bqno=0;
$ecms_bq_sql=sys_ReturnEcmsLoopBq("select * from phome_ecms_news where  classid in(44)  order by id  desc  limit 0,3",3,24,0);
if($ecms_bq_sql){
while($bqr=$empire->fetch($ecms_bq_sql)){
$bqsr=sys_ReturnEcmsLoopStext($bqr);
$bqno++;
?>
    					<li>
    						<a href="<?=$bqsr[titleurl]?>"><?=sub($bqr[title],0,40)?></a>
    					</li>
    					<?php
}
}
?>						</ul>
						</li>
						<li class="warp_u_rigt_gu_li" style="display: none">
							<ul class="warp_u_rigt_gy">
								<?php
$bqno=0;
$ecms_bq_sql=sys_ReturnEcmsLoopBq("select * from phome_ecms_news where  classid in(45)  order by id  desc  limit 0,3",3,24,0);
if($ecms_bq_sql){
while($bqr=$empire->fetch($ecms_bq_sql)){
$bqsr=sys_ReturnEcmsLoopStext($bqr);
$bqno++;
?>
    					<li>
    						<a href="<?=$bqsr[titleurl]?>"><?=sub($bqr[title],0,40)?></a>
    					</li>
    					<?php
}
}
?>	</ul>
						</li>
					</ul>
				</div>
				
				<ul class="warp_u_rigt_gt">
					<li>
                        <? @sys_GetAd(9);?>
                    </li>                
                    <li>
                        <? @sys_GetAd(10);?>
                    </li>  
				</ul>
				
			</div>
		</div>

		<div class="warp_u_div">
			<h1>全部产品分类</h1>

<?
 if($nclassid!=9){
?>
<ul class="warp_u_div_ul" >
<?
 } else {
 ?>
 <ul class="warp_u_div_ul" style="display: block;">
 <?
 }
?>
<?
$a=array();
$sql0=$empire->query("select * from `phome_enewsclass` where bclassid = 24"); 
while($r0 = $empire ->fetch( $sql0 )){
	//array_push($a,$r0['classid']);
	$a[]=$r0['classid'];
} 
$al=count($a);
for($x=0;$x<$al;$x++){
$id=$a[$x];
$sqla=$empire->query("select * from `phome_enewsclass` where classid = $id");
$ra = $empire ->fetch( $sqla );
$xra=$ra['sonclass'];
$xra= explode('|',$xra);
$xral=count($xra);
?>
<li class="warp_u_div_li">
	<div class="warp_u_div_mp">
		<b class="warp_u_div_mo_<?=$x+1?>"><?=$class_r[$id]['classname']?></b>
		<div class="warp_u_div_mi">
		<?
		if($xral<4){
		?>
			<a href="[!--news.url--]e/action/ListInfo/?classid=<?=$class_r[$xra[1]]['classid']?>"  title="<?=$class_r[$xra[0]]['classname']?>" target="_blank"><?=$class_r[$xra[1]]['classname']?></a>
		<?			
		} else {
		?>
			<a href="[!--news.url--]e/action/ListInfo/?classid=<?=$xra[$xral-2]?>"  title="<?=$class_r[$xra[$xral-1]]['classname']?>" target="_blank"><?=$class_r[$xra[$xral-2]]['classname']?></a>	
			<a href="[!--news.url--]e/action/ListInfo/?classid=<?=$xra[$xral-3]?>"  title="<?=$class_r[$xra[$xral-2]]['classname']?>" target="_blank"><?=$class_r[$xra[$xral-3]]['classname']?></a>	
		<?
		}
		?>
		</div>
	</div>
	<ul class="warp_u_div_sj_<?=$x+1?>">
		<li class="warp_u_div_sj_li">
			<?
			if($ra[sonclass]!='') {
				$sqlb=$empire->query("select * from `phome_enewsclass` where bclassid = $id order by classid asc");
				while($rb = $empire ->fetch( $sqlb )){
			?>
			<div class="col_f1">
				<dl class="fixed">
					<dt><a href="[!--news.url--]e/action/ListInfo/?classid=<?=$rb[classid]?>" title="<?=$rb['classname']?>" target="_blank"><?=$rb[classname]?></a></dt>
					<?
					if($rb[sonclass]!='') {
					?>
					<dd>
					<?	
						$rbclassid=$rb['classid'];
						$sqlc=$empire->query("select * from `phome_enewsclass` where bclassid = $rbclassid order by classid asc");
						while($rc = $empire ->fetch( $sqlc )){
					?>
						<a href="[!--news.url--]e/action/ListInfo/?classid=<?=$rc[classid]?>" title="<?=$rc['classname']?>" target="_blank"><?=$rc[classname]?></a>
					<?
						}
					?>
					</dd>
					<?
					}
					?>
				</dl>
			</div>
			<?
				}
			}
			?>
		</li>
	</ul>
</li>
<?
}
?>		
			</ul>
		</div>

		
<ul class="warp_u_ul">
	<li class="warp_u_li">
		<a href="[!--news.url--]<?=$class_r[9]['classpath']?>" class="warp_u_li_bt">首页</a>
	</li>
	<li class="warp_u_li">
		<a href="[!--news.url--]e/action/ListInfo/?classid=23"  title="联盟商家区" target="_blank" class="warp_u_li_bt">联盟商家区</a>
	</li>
	<li class="warp_u_li">
		<a href="[!--news.url--]e/action/ListInfo/?classid=24"  title="产品列表区" target="_blank" class="warp_u_li_bt">产品列表区</a>
	</li>
	<li class="warp_u_li">
		<a href="[!--news.url--]e/action/ListInfo/?classid=25"  title="特色产品展示区(现金购买)" target="_blank" class="warp_u_li_bt">特色产品展示区</a>
		<ul class="warp_u_ml">
    		<li><a href="/e/action/ListInfo/?classid=26">国内特色产品</a></li>
			<li><a href="/e/action/ListInfo/?classid=27">国际特色产品</a></li>
		</ul>
	</li>
	<li class="warp_u_li">
		<a href="[!--news.url--]e/action/ListInfo/?classid=28"  title="积分兑换区" target="_blank" class="warp_u_li_bt">积分兑换区</a>
	</li>
	<li class="warp_u_li">
		<a href="[!--news.url--]e/action/ListInfo/?classid=29"  title="会员互动区" target="_blank" class="warp_u_li_bt">会员互动区</a>
		<ul class="warp_u_ml">
			<li><a href="/e/action/ListInfo/?classid=30">抽奖游戏</a></li>
			<li><a href="/e/action/ListInfo/?classid=31">特色旅游</a></li>
		</ul>
	</li>

	<li class="warp_u_li">
		<a href="[!--news.url--]" class="warp_u_li_bt">集团网站</a>
	</li>
	<!--<li><a href="entity/list_a.html~act=1.html"  target="_blank">国内特色产品</a></li>
	<li><a href="entity/list_a.html~act=2.html"  target="_blank">国际特色产品</a></li>
	<li><a href="business/businesslist.html">联盟商家</a></li>
	<li><a href="entity/index.html">商城</a></li>
	<li><a href="jifen/index.html">积分商城</a></li>
	<li><a href="enterprisei/index.html">集团网站</a></li>-->
</ul>

	</div>
</div>

<!------导航结束------>


    <!----内容开始---->
    <div class="warp_moer">
        <? @sys_GetAd(1);?>

        <ul class="warp_k">
        <?
            if($bclassid==24||$bclassid==23) {
        ?>
            	<li><span><a href="/">首页</a></span></li><li><span>&gt;</span></li><li><span><a href="[!--news.url--]e/action/ListInfo/?classid=<?=$bclassid?>"  title="<?=$class_r[$bclassid]['classname']?>"><?=$class_r[$bclassid]['classname']?></a></span></li>
       	<?	} else {
       	?>
            [!--newsnav--] 
        <?
			}

		?>
            </ul>

        <div class="warp_j">
            <div class="warp_j_right">
                <div class="warp_j_right_div">
                <?
                	 if($bclassid==23) {
                ?>
                    <ul class="fix_k_fl">
                        <li>
							<select name="location_p" id="location_p" class="fix_k">
								
							<option value="">不限</option><option value="北京市">北京市</option><option value="天津市">天津市</option><option value="河北省">河北省</option><option value="山西省">山西省</option><option value="内蒙古自治区">内蒙古自治区</option><option value="辽宁省">辽宁省</option><option value="吉林省">吉林省</option><option value="黑龙江省">黑龙江省</option><option value="上海市">上海市</option><option value="江苏省">江苏省</option><option value="浙江省">浙江省</option><option value="安徽省">安徽省</option><option value="福建省">福建省</option><option value="江西省">江西省</option><option value="山东省">山东省</option><option value="河南省">河南省</option><option value="湖北省">湖北省</option><option value="湖南省">湖南省</option><option value="广东省">广东省</option><option value="广西壮族自治区">广西壮族自治区</option><option value="海南省">海南省</option><option value="重庆市">重庆市</option><option value="四川省">四川省</option><option value="贵州省">贵州省</option><option value="云南省">云南省</option><option value="西藏自治区">西藏自治区</option><option value="陕西省">陕西省</option><option value="甘肃省">甘肃省</option><option value="青海省">青海省</option><option value="宁夏回族自治区">宁夏回族自治区</option><option value="新疆维吾尔自治区">新疆维吾尔自治区</option><option value="香港特别行政区">香港特别行政区</option><option value="澳门特别行政区">澳门特别行政区</option><option value="台湾省">台湾省</option><option value="其它">其它</option></select>
                            
                        </li>
                        <li>
							<select name="location_c" id="location_c" class="fix_k">
							
							<option value="">不限</option></select>

                        </li>
						<script language="javascript" type="text/javascript"> 
							$(document).ready(function(){ 
							$('#location_c').change(function(){ 
							//alert($(this).children('option:selected').val()); 
							var p1=$(this).children('option:selected').val();//这就是selected的值 
							
							window.location.href="/business/businesslist.html?name="+p1;//页面跳转并传参 
							}) 
							}) 
						</script>
						<script src="/jygj/Public/home/js/region_select.js"></script>
						<script type="text/javascript">
								new PCAS('location_p', 'location_c', 'location_a', '不限', '不限', '');
						</script>
                    </ul>                 
                 <?
                 	} else {
                 ?>
                    <ul class="warp_j_right_ul">
                        <li class=" current"><span><a href="[!--news.url--]e/action/ListInfo/?classid=<?=$bclassid?>">默认</a></span></li>
                        <li ><a href="[!--news.url--]e/action/ListInfo/?classid=<?=$bclassid?>&myo=1">销量<i></i></a></li>
                        <li ><a href="[!--news.url--]e/action/ListInfo/?classid=<?=$bclassid?>&myo=2">价格<i></i></a></li>
                    </ul>

				<?
                 }
                 ?>
                </div>
                
                <div class="warp_j_right_cp">
                    <ul class="warp_j_right_cp_ul">
	[!--empirenews.listtemp--]					
<!--list.var1-->
<!--list.var2-->
<!--list.var3-->
<!--list.var4-->               
[!--empirenews.listtemp--]
</ul>
                    <!----下一页开始---->
                              <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" style="text-align: center;">
  <tr>
    <td height="21"><div class="epages">[!--show.listpage--]</div></td>
  </tr>
</table>
                    <!----下一页结束---->
                </div>

            </div>
            <div class="warp_j_left">
                <h1 class="warp_j_left_h1">热销商品</h1>

                <ul class="warp_j_left_ul">
					<? @sys_GetEcmsInfo(24,6,24,0,1,13,0);?>
					 </ul>
            </div>
        </div>

       <div style="width: 100%;height: 20px;clear: both;"></div>
        <? @sys_GetAd(2);?>
      <div style="width: 100%;height: 20px;clear: both;"></div>

		
    </div>
    <!----内容结束---->
  <div class="warp_e">
	<div class="warp_e_more">

		<div class="warp_e_lx">
			<p>联系我们</p>
			<b>400-9922-831</b>
			<span>地址：安徽省滁州市花园西路82号科技创业中心 3号楼1306、1309和1311室</span>
			<span>邮箱：2355638615@qq.com</span>
		</div>

		<div class="warp_e_div">
			<? 
	   				$nclassid =$GLOBALS[navclassid];	
	   				
		   			$arr=array(36,37,38,39,40);
			   		$arrlength=count($arr);
			   		for($x=0;$x<$arrlength;$x++)
					{ $id=$arr[$x];
				?>
				<ul>
				<p style="background: url([!--news.url--]Public/home/img/im<?=$id+34?>.jpg) no-repeat left center;background-position: 40px"><?=$class_r[$id]['classname']?></p>
	
             	<?       $sql=$empire->query("select * from phome_ecms_news where  classid in($id)  order by id asc limit 0,6"); 
			    	while ( $r = $empire ->fetch( $sql )){ 	
			    ?>
			    		<li><a href="<?=$r[titleurl]?>"><?=sub($r[title],0,16)?></a></li>
			    <?
			    	}
                ?>
             
				</ul>
				<?
					
					}
				?>    		

				</div>

	</div>
</div>

<div class="warp_e_db">
	<div style="width: 1200px;background: #444444;overflow: hidden;margin: auto;padding: 15px 0">
		<span>版权所有：<a href="[!--news.url--]">安徽诺斯贝尔电子商务有限公司</a></span><span>技术支持：<a href="http://www.zt315.cn/" target="_blank">中天科技</a></span>
		
		<style>
		.zj_p{width: 438px;overflow: hidden;margin: auto;padding: 20px 0}
		.zj_p ul{width: 500px;overflow: hidden}
		.zj_p ul li{width: 100px;overflow: hidden;float: left;margin-right: 12px}
		.zj_p ul li img{display: block;width: 100px; height: 40px}
		</style>
		<div class="zj_p">
			<ul>
				<li>
				   <a id='___szfw_logo___' href='https://credit.szfw.org/CX20161107027133370161.html' target='_blank'><img src='http://icon.szfw.org/cert.png' border='0'/></a>
				   <script type='text/javascript'>(function(){document.getElementById('___szfw_logo___').oncontextmenu = function(){return false;}})();</script>
				</li>
				<li>
					<a href="http://t.knet.cn/index_new.jsp" target='_blank'><img src="[!--news.url--]Public/home/img/kxwzsfyz.png"></a>
				</li>
				<li>
					<script src="[!--news.url--]Public/home/js/21357254.js"></script>
				</li>
				<li>
					<a href="http://www.itrust.org.cn/Home/Index/itrust_certifi?wm=1A18YEKB78" target='_blank'><img src="[!--news.url--]Public/home/img/wxrz.jpg"></a>
				</li>
			</ul>
		</div>
	</div>
	
</div>
</div>
</body>
</html>
