<?php
//header('Access-Control-Allow-Origin: http://www.baidu.com'); //设置http://www.baidu.com允许跨域访问
//header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With'); //设置允许的跨域header
require('../../../class/connect.php'); //引入数据库配置文件和公共函数文件
require('../../../class/db_sql.php'); //引入数据库操作文件
require('../../../data/dbcache/class.php'); //栏目缓存
$CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents("config.json")), true);
$link=db_connect(); //连接MYSQL
$empire=new mysqlquery(); //声明数据库操作

$qiantai=(int)$_GET['qiantai'];//是否前台
if($qiantai){
$userid= (int)getcvar('mluserid');
$username=RepPostVar(getcvar('mlusername'));
$user_fj='[Member]'.$username;
}else{
$userid=getcvar('loginuserid',1);
$username=getcvar('loginusername',1);
$rnd=getcvar('loginrnd',1);
$adminr=$empire->fetch1("select userid,groupid,classid,userprikey from {$dbtbpre}enewsuser where userid='$userid' and username='".$username."' and rnd='".$rnd."' and checked=0 limit 1");
$user_fj=$username;
}
$classid= (int)$_GET['classid'];
$filepass= (int)$_GET['filepass'];


$action=RepPostVar($_GET['action']);
//验证权限并用帝国的配置项
if($action!='config')
{
//获取配置
$pr=$empire->fetch1("select * from {$dbtbpre}enewspublic");
$check=check();
if($check){
echo $check;exit();
}else{//通过验证用帝国配置项及目录等
if($qiantai){
//前台大小 后缀等限制
$qaddtransize=$public_r['qaddtransize']*1024;
$CONFIG['imageMaxSize'] = $qaddtransize;
$CONFIG['scrawlMaxSize'] = $qaddtransize;
$CONFIG['catcherMaxSize'] = $qaddtransize;
$qaddtranimgtype = substr($pr['qaddtranimgtype'],1,-1);
$qaddtranimgtype = explode('|',$qaddtranimgtype);
$CONFIG['imageAllowFiles'] = $qaddtranimgtype;
$CONFIG['imageManagerAllowFiles'] = $qaddtranimgtype;
$CONFIG['catcherAllowFiles'] = $qaddtranimgtype;

$qaddtranfilesize = $pr['qaddtranfilesize']*1024;
$CONFIG['fileMaxSize'] = $qaddtranfilesize;
$CONFIG['videoMaxSize'] = $qaddtranfilesize;
$qaddtranfiletype = substr($pr['qaddtranfiletype'],1,-1);
$qaddtranfiletype = explode('|',$qaddtranfiletype);
$CONFIG['fileAllowFiles'] = $qaddtranfiletype;
$CONFIG['fileManagerAllowFiles'] = $qaddtranfiletype;
}else{
//后台大小 后缀等限制
$filesize = $public_r['filesize']*1024;
$CONFIG['imageMaxSize']=$filesize;
$CONFIG['scrawlMaxSize']=$filesize;
$CONFIG['catcherMaxSize']=$filesize;
$CONFIG['videoMaxSize']=$filesize;
$CONFIG['fileMaxSize']=$filesize;
$filetype = substr($pr['filetype'],1,-1);
$filetype = explode('|',$filetype);
$CONFIG['fileAllowFiles']=$filetype;
$CONFIG['fileManagerAllowFiles'] = $filetype;
}
$classpath = ReturnFileSavePath($classid); //栏目附件目录
$time=time();
$filedir='';
if($public_r['filepath']){
$filedir=date($public_r['filepath'], $time).'/';
$filedir2=date($public_r['filepath'], $time);
}
$uepath=$pr['newsurl'].$classpath['filepath'].$filedir.'{time}{rand:6}';//百度编辑器上传路径
if($pr['openfileserver']==1){//开启远程附件
$ftp_r=$empire->fetch1("select * from {$dbtbpre}enewspostserver where pid=1");//返回FTP
$truepath=$ftp_r['purl'].$filedir; //日期栏目目录filepath
$bdpath=ECMS_PATH.$classpath['filepath'].$filedir;
}else{
$truepath=$bdpath.$filedir; 
$bdpath=ECMS_PATH.$classpath['filepath'].$filedir;
}
//$CONFIG['imageUrlPrefix']=$public_r['fileurl'];
//$CONFIG['scrawlUrlPrefix']=$public_r['fileurl'];
//$CONFIG['snapscreenUrlPrefix']=$public_r['fileurl'];
//$CONFIG['catcherUrlPrefix']=$public_r['fileurl'];
//$CONFIG['videoUrlPrefix']=$public_r['fileurl'];
//$CONFIG['fileUrlPrefix']=$public_r['fileurl'];
$CONFIG['imagePathFormat']=$uepath;
$CONFIG['scrawlPathFormat']=$uepath;
$CONFIG['snapscreenPathFormat']=$uepath;
$CONFIG['videoPathFormat']=$uepath;
$CONFIG['filePathFormat']=$uepath;
$CONFIG['catcherPathFormat']=$uepath;

}
}

//验证函数
function check(){
global $adminr,$dbtbpre,$action,$empire,$pr,$qiantai;
if($qiantai==1){//前台的验证

if($pr['addnews_ok']==1)
{
$error='{"state": "网站已关闭投稿功能！"}';
}
else if(($action=='uploadimage'||$action=='uploadscrawl'||$action=='catchimage')&&(!$pr['qaddtran']))
{
$error='{"state": "网站已关闭上传图片功能！"}';
}
else if(($action=='uploadvideo'||$action=='uploadfile')&&!$pr['qaddtranfile'])
{
$error='{"state": "网站已关闭上传附件功能！"}';
}
}else{//后台的验证
if($adminr){
//通过验证
}else{
$error='{"state": "请重新登录"}';
}
}
return $error;
}


switch ($action) {
case 'config':
$result =  json_encode($CONFIG);
break;

/* 上传图片 */
case 'uploadimage':
/* 上传涂鸦 */
case 'uploadscrawl':
/* 上传视频 */
case 'uploadvideo':
/* 上传文件 */
case 'uploadfile':
$result = include("action_upload.php");
if($pr['openfileserver']==1){//开启远程附件补充前缀
$result=json_decode($result,TRUE);
$result['url']=  str_replace('/d/file/', '', $ftp_r['purl']).$result['url'];//FTP前缀
//$result['original']=$result['title'];//标题是路径还是名称
$result=json_encode($result);
}
break;

/* 列出图片 */
case 'listimage':
//$result = include("action_list.php");
$result=action_list();
break;
/* 列出文件 */
case 'listfile':
// $result = include("action_list.php");
$result=action_list();
break;

/* 抓取远程文件 */
case 'catchimage':
$result = include("action_crawler.php");
if($pr['openfileserver']==1){//开启远程附件补充前缀
$result=json_decode($result,TRUE);
$result['url']=  str_replace('/d/file/', '', $ftp_r['purl']).$result['url'];//FTP前缀
//$result['original']=$result['title'];//标题是路径还是名称
$result=json_encode($result);
}
break;

default:
$result = json_encode(array(
'state'=> '请求地址出错'
));
break;
}

/* 输出结果 */
if (isset($_GET["callback"])) {
if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
} else {
echo json_encode(array(
'state'=> 'callback参数不合法'
));
}
} else {
echo $result;
}


$actionarr=array('uploadimage','uploadscrawl','uploadvideo','uploadfile','catchimage');
if(in_array($action, $actionarr))
{
$file_r=json_decode($result,true);
if($action=='uploadimage'||$action=='catchimage'||$action=='uploadscrawl'){
$type=1;
}elseif($action=='uploadvideo'){
$type=3;
}else{
$type=0;
}
if($action=="catchimage") //远程保存
{
for($i=0;$i<count($file_r['list']);$i++)
{
if($file_r['list'][$i]['state']=="SUCCESS")
{
$title = RepPostStr(trim($file_r['list'][$i]['title']));
$filesize = RepPostStr(trim($file_r['list'][$i]['size']));
$original = RepPostStr(trim($file_r['list'][$i]['original']));
eInsertFileTable($title,$filesize,$filedir2,$user_fj,$classid,$original,$type,$filepass,$filepass,$public_r[fpath],0,0,0);
}
}
}
else if($file_r['state']=="SUCCESS")
{
$title = RepPostStr(trim($file_r[title]));
//判断是否FLASH
$typearr=explode('.', $title);
if($typearr[1]=='swf'){
$type=2;
}
$filesize = RepPostStr(trim($file_r[size]));
$original = RepPostStr(trim($file_r[original]));
//帝国上传附件是md5的文件名
if(!$type){
$original=$title;
}
eInsertFileTable($title,$filesize,$filedir2,$user_fj,$classid,$original,$type,$filepass,$filepass,$public_r[fpath],0,0,0);
}
$efileftp_fr=array();//远程附件的图片数组
$efileftp_fr[0]=$bdpath.'/'.$title;//远程附件所用的本地图片路径
}

// 列出已经上传的图像和文件
function action_list(){
global $empire,$dbtbpre,$filepass,$classid,$user_fj,$action,$qiantai,$pr,$ftp_r;
$list=array();
$result= json_encode(array("state" => "no match file","list" => $list,"start" => 0,"total" => 0));
$where="classid='$classid' and id='$filepass'";
if($qiantai==1){
$where.=" and adduser='$user_fj'";
}
if($action=='listimage') //图片 
{
$where.=' and type=1';
}else if($action=='listfile') //附件、多媒体、FLASH
{
$where.=' and type<>1';
} else
{
return $result;
}
$size=(int)$_GET['size'];
$start=(int)$_GET['start'];
$limit=$start.",".$size;
$total=$empire->gettotal("select count(*) as total from {$dbtbpre}enewsfile_1 where ".$where);
$sql=$empire->query("select * from {$dbtbpre}enewsfile_1 where ".$where." order by fileid DESC limit ".$limit);
$i=0;
while($r=$empire->fetch($sql))
{
$classpath = ReturnFileSavePath($r[classid],$r[fpath]); //栏目附件目录
if($pr['openfileserver']==1){//开启远程附件补充前缀
$classpath['filepath']=str_replace('d/file/', '', $ftp_r['purl']).$classpath['filepath'];//FTP前缀
}else{
$classpath['filepath']=$pr['newsurl'].$classpath['filepath'];
}
$list[$i]['url'] = $classpath['filepath'].($r['path']?$r['path'].'/':$r[path]).$r['filename'];
$list[$i]['mtime'] =$r['filetime'];
$i++;
}
/* 返回数据 */
if (!count($list)) { return $result; }
return $result = json_encode(array(
"state" => "SUCCESS",
"list" => $list,
"start" => $start,
"total" => $total
));
}

db_close();
$empire=null;
exit();
?>