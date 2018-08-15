<!DOCTYPE html >
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width">
<title><?php echo $this->_var['page_title']; ?></title>
<meta name="Keywords" content="<?php echo $this->_var['keywords']; ?>" />
<meta name="Description" content="<?php echo $this->_var['description']; ?>" />
<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
<link rel="stylesheet" type="text/css" href="themesmobile/default/css/public.css?v=<?php echo time(); ?>"/>
<link rel="stylesheet" type="text/css" href="themesmobile/default/css/index.css?v=<?php echo time(); ?>"/>
<script type="text/javascript" src="themesmobile/default/js/jquery.js"></script>
<script type="text/javascript" src="themesmobile/default/js/TouchSlide.1.1.js"></script>
<script type="text/javascript" src="themesmobile/default/js/jquery.more.js"></script>
<script type="text/javascript" src="themesmobile/default/js/jquery.cookie.js"></script>
<script type="text/javascript" src="themesmobile/default/js/mobile.js"></script>
</head>
<body>
<?php 
$k = array (
  'name' => 'share',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?><?php 
$k = array (
  'name' => 'add_url_uid',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?>


<div class="body_bj">


<header id="header"> <?php echo $this->fetch('library/page_header.lbi'); ?> </header>
 
<?php echo $this->fetch('library/index_ad.lbi'); ?> 
 

 
<a href='http://shop.xiangbai315.com/mobile/article.php?id=155' style=' margin:0 auto; width:90% !important; font-size:16px;display:block'>
	<marquee id="blink" direction="left" behavior="scroll" scrollamount="2" scrolldelay="0" loop="-1" width="100%"    hspace="10" vspace="10" onmouseover=this.stop() onmouseout=this.start()>
 公告: <?php echo $this->_var['message']; ?>
	</marquee>
</a><script language="javascript"> 
function changeColor(){ 
var color="#f00|#0f0|#00f|#880|#808|#088|yellow|green|blue|gray"; 
color=color.split("|"); 
document.getElementById("blink").style.color=color[parseInt(Math.random() * color.length)]; 
} 
setInterval("changeColor()",200); 
</script>

<div class="index_search">
  <div class="index_search_mid"> <a href="searchindex.php"> <em>请输入您所搜索的商品</em> <span><img src="themesmobile/default/images/icosousuo.png"></span> </a> </div>
</div>
 



<script>
var oMarquee = document.getElementById("mq"); //滚动对象
var iLineHeight = 30; //单行高度，像素
var iLineCount = 7; //实际行数
var iScrollAmount = 1; //每次滚动高度，像素
function run() {
oMarquee.scrollTop += iScrollAmount;
if ( oMarquee.scrollTop == iLineCount * iLineHeight )
oMarquee.scrollTop = 0;
if ( oMarquee.scrollTop % iLineHeight == 0 ) {
window.setTimeout( "run()", 2000 );
} else {
window.setTimeout( "run()", 50 );
}
}
oMarquee.innerHTML += oMarquee.innerHTML;
window.setTimeout( "run()", 2000 );
</script>

<div class="floor_img">
	<h2>
	
	<?php echo $this->fetch('library/ad_position.lbi'); ?>
	 
	</h2>
 
 <dl>
	    <dt> 
	 </dt>
	    <dd> 
	    <span class="Edge"> 
	 </span> 
	<span> 
	
	 </span> </dd>
 </dl>
 
	<ul>
		<li class="brom">
		 
		 
		</li>
	</ul>
</div>

 

 





<?php echo $this->fetch('library/recommend_promotion.lbi'); ?>
 

 





 <div class="floor_body2" >
    <h2>————&nbsp;产品&nbsp;————</h2>
      <div id="J_ItemList">
      <ul class="product single_item info">
      </ul>
      <a href="javascript:;" class="get_more"> </a> 
      </div>
 </div>




 
<?php echo $this->fetch('library/footer_nav.lbi'); ?> 

<script type="text/javascript" src="themesmobile/default/js/jquery.js"></script>
<?php echo $this->smarty_insert_scripts(array('files'=>'jquery.json.js,transport.js')); ?>
<script type="text/javascript" src="themesmobile/default/js/touchslider.dev.js"></script>
<script type="text/javascript" src="themesmobile/default/js/jquery.more.js"></script>
<?php echo $this->smarty_insert_scripts(array('files'=>'common.js')); ?>
<script type="text/javascript">
var url = 'exchange.php?act=ajax_list';
$(function(){
	$('#J_ItemList').more({'address': url});
});

</script> 
<script>
function goTop(){
	$('html,body').animate({'scrollTop':0},600);
}
</script>
<a href="javascript:goTop();" class="gotop"><img src="themesmobile/default/images/topup.png"></a> 
<script type="Text/Javascript" language="JavaScript">


	function selectPage(sel)
	{
	   sel.form.submit();
	}


</script>
<script type="text/javascript">
<?php $_from = $this->_var['lang']['compare_js']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
<?php if ($this->_var['key'] != 'button_compare'): ?>
var <?php echo $this->_var['key']; ?> = "<?php echo $this->_var['item']; ?>";
<?php else: ?>
var button_compare = "";
<?php endif; ?>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
var compare_no_goods = "<?php echo $this->_var['lang']['compare_no_goods']; ?>";
var btn_buy = "<?php echo $this->_var['lang']['btn_buy']; ?>";
var is_cancel = "<?php echo $this->_var['lang']['is_cancel']; ?>";
var select_spe = "<?php echo $this->_var['lang']['select_spe']; ?>";
</script>

 
</div> 
</body>
</html>