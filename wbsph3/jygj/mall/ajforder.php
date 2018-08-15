<?	
header("content-type:text/html;charset:utf-8");
include("config/zt_config.php");
include("page.class.php");
$link = mysqli_connect($db_host,$db_user,$db_pwd,$db_database) or die('Unale to link');
$link->set_charset('utf8');
$user=htmlspecialchars(trim($_GET['user']));
$flg = htmlspecialchars(trim($_GET['flg']));
$ss='';
$sss='';
if($flg=='a') {
  $ss=" `zt`='待发货' and ";
  $sss='您尚未兑换任何商品！';
} else if($flg=='b') {
  $ss=" `zt`='待确认收货' and ";
  $sss='暂无待确认收获商品！';
} else if($flg=='c') {
  $ss=" `zt`='已签收' and ";
  $sss='暂无已签收商品！';
}

$sql1="SELECT * from `zt_dhsp` where ".$ss." `user`='".$user."' ORDER BY id desc";

$qry1=$link->query($sql1);
$total=$qry1->num_rows;

$per=24;
$page_obj=new Page($total,$per);
$sqla1=$sql1." limit ".($page_obj->page-1)*$per.",".$per;

$qrya1=$link->query($sqla1);
$pagelist=$page_obj->fpage(array(0,2,4,5,6,9));
$p=isset($page_obj->page)?$page_obj->page:1;
$num=($p-1)*$per;
?>
<?                    
if($total==0) {
    echo "<center>".$sss."</center>";
} else {

while($rst1=$qrya1->fetch_array()){		
	$sql2="SELECT * FROM  `zt_jifen` where id=".$rst1['jfid'];
	$qry2=$link->query($sql2);
	$rst2=$qry2->fetch_array();
?> 
		
 <tr>
    <td width="380" height="30" align="center" style="border-bottom: 1px solid #aaa"><a href="/mall/jfcontent.html?jfid=<?=$rst1['jfid']?>" style="color:#000;"><?=$rst2['spmc']?></a></td>
    <td width="90" align="center"  style="border-bottom: 1px solid #aaa"><?=$rst1['jf']?></td>
    <td width="90" align="center"  style="border-bottom: 1px solid #aaa"><?=$rst1['sl']?></td>
    <td width="110" align="center"  style="border-bottom: 1px solid #aaa"><?=$rst1['zt']?></td>
    <td width="130" align="center"  style="border-bottom: 1px solid #aaa"><button onclick="cz('<?=$rst1['id']?>','2')" style="background-color: #00459A;color: #fff">签收</button></td>
	<td align="center"  style="border-bottom: 1px solid #aaa"><?=empty($rst1['kd'])?'暂无物流信息':$rst1['kd']?></td>
</tr>
<?
}
?>



<center>

<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" style="text-align: center;">
  <tr>
    <td height="21" colspan="6"><div class="epages" style="margin-top: 0"><?=$pagelist;?></div></td>
  </tr>
</table>

</center> 
<?
}
?>
<?$link->close();?>
        
