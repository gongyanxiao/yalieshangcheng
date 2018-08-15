<?php
function user_SetCook($name,$id,$time=0,$num=10){//cookie名称 ID 时间戳 数量
if(!$id){
printerror2('不存在的产品记录',$_SERVER['HTTP_REFERER']);
}
$prodq=$id.'|';
$projilu=getcvar($name);
if($projilu){
$prozuhe=$prodq.$projilu;//把新浏览的放在第一
$pro_arr=explode('|',$prozuhe);
$new_arr=array_merge(array_unique($pro_arr));//去除重复,重新索引下标
foreach($new_arr as $key=>$val){
if(($key<$num) && $val){
$pro.=$val.'|';
}
}
}else{
$pro=$prodq;
}
esetcookie($name,$pro,$time);
//esetcookie($name,'',0);
//print_r($_COOKIE);
}


function user_GetCook($name,$tbname,$num=10){ //名称 数据表 数量
global $empire,$dbtbpre;
if(preg_match("/^[0-9\|]+$/",getcvar($name))){
$jilu_all=substr(getcvar($name),0,-1); //1|2|3
$jilu_idin=str_replace('|',',',$jilu_all);//1,2,3
}else{
$jilu_idin=0;//避免sql错误没有返回0
}
$sql=$empire->query("select id,title,titlepic,titleurl from {$dbtbpre}ecms_{$tbname} where id in ({$jilu_idin}) order by find_in_set(id,'{$jilu_idin}') limit {$num}"); //按id in 里面的排序
while($jilu_r=$empire->fetch($sql))
{
$titleurl=sys_ReturnBqTitleLink($jilu_r);
echo '<li><a href="'.$titleurl.'">'.$jilu_r[title].'</a></li>';//此处是修改样式需要图片自己加
}
}
function user_PhotoMorepic($havepic){
 global $navinfor,$public_r;
 $morepic=$navinfor['morepic'];
 $rexp="\r\n";
 $fexp="::::::";
 $rstr="";
 $sdh="";
 $w_morepic="";
 $rr=explode($rexp,$morepic);
 $count=count($rr);
 
 for($i=0;$i<$count;$i++)
 {
  if($i==($count-1))
  {$fh="";}else{$fh=",";}
  $fr=explode($fexp,$rr[$i]);
  
   $smallpic=$fr[0]?$fr[0]:$public_r[newsurl]."e/data/images/notimg.gif"; //小图
   $bigpic=$fr[1]?$fr[1]:$public_r[newsurl]."e/data/images/notimg.gif"; //大图
   if(empty($bigpic))
   {
    $bigpic=$smallpic;
   }
   $picname=htmlspecialchars($fr[2]); //名称
   $w_morepic.="<li>
   <img src='$smallpic' alt='$picname' width='52' height='52'/>
     </li>";
 }
  
 echo $w_morepic;
}
 
?>