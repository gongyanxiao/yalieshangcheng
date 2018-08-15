<?php
header("Content-type:text/html;charset=utf-8");
if($_COOKIE['a']<>"1"){
exit();
}
include_once"config/check.php";

include "../config/zt_config.php";
$db =mysql_connect($db_host,$db_user,$db_pwd);
mysql_query("set names $coding");
mysql_select_db($db_database);

$czz=$_COOKIE['zt_user2'];

function getIP(){ 
if (isset($_SERVER)) { 
if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) { 
$realip = $_SERVER['HTTP_X_FORWARDED_FOR']; 
} elseif (isset($_SERVER['HTTP_CLIENT_IP'])) { 
$realip = $_SERVER['HTTP_CLIENT_IP']; 
} else { 
$realip = $_SERVER['REMOTE_ADDR']; 
} 
} else { 
if (getenv("HTTP_X_FORWARDED_FOR")) { 
$realip = getenv( "HTTP_X_FORWARDED_FOR"); 
} elseif (getenv("HTTP_CLIENT_IP")) { 
$realip = getenv("HTTP_CLIENT_IP"); 
} else { 
$realip = getenv("REMOTE_ADDR"); 
} 
} 
return $realip; 
}
$ip=getIP();
$date=date("Y-m-d H:i:s");


$ts='';
$id=htmlspecialchars($_GET['id']);
$pid=htmlspecialchars($_GET['pid']);
$cz=htmlspecialchars($_GET['cz']);
$xm=htmlspecialchars($_POST['xm']);
$sfzh=htmlspecialchars($_POST['sfzh1']);
$khh=htmlspecialchars($_POST['khh']);
$zhihang=htmlspecialchars($_POST['zhihang']);
$khdz=htmlspecialchars($_POST['khdz']);
$yhkh=htmlspecialchars($_POST['yhkh']);

$query0="SELECT * from zt_bind_bank where id='$id'";
$re0=mysql_query($query0,$db);
$rst0=mysql_fetch_array($re0);
$yyhkh=$rst0['yhkh'];
if($cz=='1') {
?>
<form  action="pmoda.php?cz=3&id=<?=$id?>&pid=<?=$pid?>" method="post" name="myform" id='myform'>
 <table width="99%" border="1" align="center" cellspacing="0" bordercolorlight="#94D0EA" bordercolordark="#F5FBFE">
 <tr bgcolor=#cccccc>
      <td height="30" align="center" bgcolor="#DAECFA" class="btdx" colspan="6">修改银行卡信息<font style="color: red;font-weight: bold;">（谨慎操作，后果自负）</font></td>
    </tr>
    <tr bgcolor=#cccccc>
      <td height="30" align="center" bgcolor="#DAECFA" class="btdx">姓名</td>
      <td align="center" bgcolor="#DAECFA" class="btdx">身份证号</td>
      <td align="center" bgcolor="#DAECFA" class="btdx">开户行</td>
      <td align="center" bgcolor="#DAECFA" class="btdx">开户支行</td>
      <td align="center" bgcolor="#DAECFA" class="btdx">开户地址</td>
      <td align="center" bgcolor="#DAECFA" class="btdx">银行卡号</td>
    </tr>

<input type="hidden" name="ssyh" value="<?=$rst0['ssyh']?>">
    <tr>
       <td height="30" align="center" bgcolor="#FFFFFF"><input type="text" name="xm" value="<?=$rst0['xm']?>"></td>
      <td align="center" bgcolor="#FFFFFF"><input type="text" name="sfzh1" value="<?=$rst0['sfzh']?>"></td>
      <td align="center" bgcolor="#FFFFFF"><input type="text" name="khh" value="<?=$rst0['khh']?>"></td>
      <td align="center" bgcolor="#FFFFFF"><input type="text" name="zhihang" value="<?=$rst0['zhihang']?>"></td>
      <td align="center" bgcolor="#FFFFFF"><input type="text" name="khdz" value="<?=$rst0['khdz']?>"></td>
      <td align="center" bgcolor="#FFFFFF"><input type="text" name="yhkh" value="<?=$rst0['yhkh']?>"></td>
    </tr>

<tr><td colspan="6" align="center" height="30"><input type="submit" value="确认修改"></td></tr>
  </table>
  </form>
<?
} elseif($cz=='2') {
  $query="DELETE FROM `zt_bind_bank` WHERE id='$id'";
  $query1="INSERT INTO `zt_scrz`(`id`, `ip`, `date`, `czz`, `bsyh`, `bz`, `by`) VALUES (null,'$ip','$date','$czz','$pid','被删银行卡用户，银行卡号为：','$yyhkh')";
  $re=mysql_query($query,$db);
  $re1=mysql_query($query1,$db);
  if($re&&$re1){
    echo '<script language="javascript" type="text/javascript">alert(修改银行卡成功！);</script>';
    echo '<script language="javascript" type="text/javascript">location.replace(document.referrer);</script>';
  }
} else if($cz=='3') {
  $sql2="select * from xbmall_users where user_name='$pid' order by id desc";
$qs2=mysql_query($sql2);
$out=mysql_fetch_assoc($qs2);
 if($xm<>$out['xm']){
echo '<script>alert("您输入的姓名与注册时信息不一致,请核实!")</script>';
  
  ?>
  <script type="text/javascript">location.replace(document.referrer); </script>
  <?
exit();
}
$k=$out['sfzh'];
if($sfzh<>$k){
echo '<script>alert("您输入的身份证号与注册时信息不一致,请核实!")</script>';

  ?>
  <script type="text/javascript">location.replace(document.referrer); </script>
  <?
exit();
  }
$sql="select * from zt_bind_bank where yhkh='$yhkh' order by id desc";
$qs1=mysql_query($sql);
if(mysql_num_rows($qs1)>=2){
echo '<script>alert("您已经绑定过银行卡!")</script>';

?>
<script type="text/javascript">location.replace(document.referrer); </script>
<?
exit();
}else{
  $query="UPDATE `zt_bind_bank` SET `xm`='$xm',`sfzh`='$sfzh',`khdz`='$khdz',`zhihang`='$zhihang',`yhkh`='$yhkh',`khh`='$khh' WHERE id='$id'";
  $query1="INSERT INTO `zt_scrz`(`id`, `ip`, `date`, `czz`, `bsyh`, `bz`, `by`) VALUES (null,'$ip','$date','$czz','$pid','被修改银行卡用户，修改前银行卡号为：','$yyhkh')";
  $re=mysql_query($query,$db);
  $re1=mysql_query($query1,$db);
  if($re&&$re1){
?>
<script type="text/javascript">alert(修改银行卡信息成功！);</script>
<script type="text/javascript">location.href='pass_mod.php?<?=$pid!=''?'pid='.$pid:''?>';</script>
<?
  }

}
}






mysql_close($db);
?>


