<?php
error_reporting(0);
//中天截取字符串
function ztzf($str, $start, $len) { 
$tmpstr = ""; 
$strlen = $start + $len; 
for($i = 0; $i < $strlen; $i++) { 
if(ord(substr($str, $i, 1)) > 0xa0) { 
$tmpstr .= substr($str, $i, 2); 
$i++; 
} else 
$tmpstr .= substr($str, $i, 1); 
} 
return $tmpstr; 
} 
function ztjq($str, $from, $len){ 
return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$from.'}'. '((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$len.'}).*#s','$1',$str); 
} 
//产生9位随机编码
function GetRandStr($length){
$str='0000000000123456789';
$len=strlen($str)-1;
$randstr='';
for($i=0;$i<$length;$i++){
$num=mt_rand(0,$len);
$randstr .= $str[$num];
}
return $randstr;
}

//中天弹出窗口函数
function zt_msg($msg,$url){
echo '<br> <table width="250" height="95" border="0" align="center" cellpadding="0" cellspacing="0" style="border:1px #FF9900 solid;margin-top:10px;margin:auto;">';
echo ' <tr> <td width="250" height="78" align="center" bgcolor="#FFFF99"><img src="images/rightno.jpg" width="18" heigh="18">';
echo '<span style="font-size:12px;color:#FF0000;">'.$msg.'</span></td></tr></table>';
echo '<meta http-equiv="refresh" content="2;url='.$url.'">'; 
}
//提取编辑器图片地址
function zt_pic($content){ 
 preg_match_all('/<img[^>]*src\s?=\s?[\'|"]([^\'|"]*)[\'|"]/is',$content,$picarr); 
 if($picarr[1][0]<>""){
echo $picarr[1][0]; 
}else{
echo "images/nopic.jpg";
}
  }
//cookies验证函数
function zt_cookie($var,$value='',$time=0,$path='',$domain=''){ 
$_COOKIE[$var] = $value; 
if(is_array($value)){ 
foreach($value as $k=>$v){ 
setcookie($var.'['.$k.']', $v, $time, $path, $domain, $s); 
} 
}else{ 
setcookie($var, $value, $time, $path, $domain, $s); 
} 
} 

function zt_page($page,$total,$phpfile,$pagesize=10,$pagelen=5){ 
$pagecode = '';//定义变量，存放分页生成的HTML 
$page = intval($page);//避免非数字页码 
$total = intval($total);//保证总记录数值类型正确 
if(!$total) return array();//总记录数为零返回空数组 
$pages = ceil($total/$pagesize);//计算总分页 
//处理页码合法性 
if($page<1) $page = 1; 
if($page>$pages) $page = $pages; 
//计算查询偏移量 
$offset = $pagesize*($page-1); 
//页码范围计算 

$init = 1;//起始页码数 

$max = $pages;//结束页码数 

$pagelen = ($pagelen%2)?$pagelen:$pagelen+1;//页码个数 

$pageoffset = ($pagelen-1)/2;//页码个数左右偏移量 



//生成html 

$pagecode='<div class="page">'; 

$pagecode.="<span>$page/$pages</span>";//第几页,共几页 

//如果是第一页，则不显示第一页和上一页的连接 

if($page!=1){ 

$pagecode.="<a href=\"{$phpfile}&page=1\">首页</a>";//第一页 

$pagecode.="<a href=\"{$phpfile}&page=".($page-1)."\">上一页</a>";//上一页 

} 

//分页数大于页码个数时可以偏移 

if($pages>$pagelen){ 

//如果当前页小于等于左偏移 

if($page<=$pageoffset){ 

$init=1; 

$max = $pagelen; 

}else{//如果当前页大于左偏移 

//如果当前页码右偏移超出最大分页数 

if($page+$pageoffset>=$pages+1){ 

$init = $pages-$pagelen+1; 

}else{ 

//左右偏移都存在时的计算 

$init = $page-$pageoffset; 

$max = $page+$pageoffset; 

} 

} 

} 

//生成html 

for($i=$init;$i<=$max;$i++){ 

if($i==$page){ 

$pagecode.='<span>'.$i.'</span>'; 

} else { 

$pagecode.="<a href=\"{$phpfile}&page={$i}\">$i</a>"; 

} 

} 

if($page!=$pages){ 

$pagecode.="<a href=\"{$phpfile}&page=".($page+1)."\">下一页</a>";//下一页 

$pagecode.="<a href=\"{$phpfile}&page={$pages}\">尾页</a>";//最后一页 

} 

$pagecode.='</div>'; 

return array('pagecode'=>$pagecode,'sqllimit'=>' limit '.$offset.','.$pagesize); 

} 


function zts_page($page,$total,$phpfile,$pagesize=10,$pagelen=5){ 
$pagecode = '';//定义变量，存放分页生成的HTML 
$page = intval($page);//避免非数字页码 
$total = intval($total);//保证总记录数值类型正确 
if(!$total) return array();//总记录数为零返回空数组 
$pages = ceil($total/$pagesize);//计算总分页 
//处理页码合法性 
if($page<1) $page = 1; 
if($page>$pages) $page = $pages; 
//计算查询偏移量 
$offset = $pagesize*($page-1); 
//页码范围计算 

$init = 1;//起始页码数 

$max = $pages;//结束页码数 

$pagelen = ($pagelen%2)?$pagelen:$pagelen+1;//页码个数 

$pageoffset = ($pagelen-1)/2;//页码个数左右偏移量 



//生成html 

$pagecode='<div class="page">'; 

$pagecode.="<span>$page/$pages</span>";//第几页,共几页 

//如果是第一页，则不显示第一页和上一页的连接 

if($page!=1){ 

$pagecode.="<a href=\"{$phpfile}?page=1\">首页</a>";//第一页 

$pagecode.="<a href=\"{$phpfile}?page=".($page-1)."\">上一页</a>";//上一页 

} 

//分页数大于页码个数时可以偏移 

if($pages>$pagelen){ 

//如果当前页小于等于左偏移 

if($page<=$pageoffset){ 

$init=1; 

$max = $pagelen; 

}else{//如果当前页大于左偏移 

//如果当前页码右偏移超出最大分页数 

if($page+$pageoffset>=$pages+1){ 

$init = $pages-$pagelen+1; 

}else{ 

//左右偏移都存在时的计算 

$init = $page-$pageoffset; 

$max = $page+$pageoffset; 

} 

} 

} 

//生成html 

for($i=$init;$i<=$max;$i++){ 

if($i==$page){ 

$pagecode.='<span>'.$i.'</span>'; 

} else { 

$pagecode.="<a href=\"{$phpfile}&page={$i}\">$i</a>"; 

} 

} 

if($page!=$pages){ 

$pagecode.="<a href=\"{$phpfile}&page=".($page+1)."\">下一页</a>";//下一页 

$pagecode.="<a href=\"{$phpfile}&page={$pages}\">尾页</a>";//最后一页 

} 

$pagecode.='</div>'; 

return array('pagecode'=>$pagecode,'sqllimit'=>' limit '.$offset.','.$pagesize); 

} 


?>