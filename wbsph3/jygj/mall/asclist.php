 <?
header("Content-type:text/html;charset=utf-8");
include("config/zt_config.php");
include("config.php");
include("page.class.php");
$link = mysqli_connect($db_host,$db_user,$db_pwd,$db_database) or die('Unale to link');
$link->set_charset('utf8');
$query1=$link->query("select * from xbmall_users where user_name='".$_COOKIE['ECS']['username']."'"); 
$r1=$query1->fetch_array();
$query2='';
if($r1['lx']==1) {
$query2=$link->query("select * from zt_shopinfo where userid=".$r1['id']); 
} else {
$query2=$link->query("select * from zt_memberinfo where userid=".$r1['id']); 	
}
$r2=$query2->fetch_array(); 
$cpids=strtr($r2['sc'],array("|"=>","));
$sql1="SELECT * FROM  `zt_goods` where id in (".$cpids.")";
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
    echo "<center>您尚未收藏任何产品！</center>";
} else {
?>
        
<?
while($rst1=$qrya1->fetch_array()){		
?> 
		<tr>

<td width="660"><a href="cpcontent.html?cpid=<?=$rst1[id];?>" title="<?=$rst1[spmc];?>" style="color:#666;"><?=$rst1[spmc];?></a></td>
						
							<td width="85"><i>￥</i><?=$rst1[spjg];?></td>
<td><?if($rst1[sfsj]==1) {?>
								<center><button onclick="sc_qx(<?=$rst1[id]?>)" style="background-color: #00459A;color: #fff">取消收藏</button></center>
		<?} else {?>
<center><span style="top: 2%;width: 90%;left: 5%;">该商品已经下架，是否</span><button onclick="sc_qx(<?=$rst1[id]?>)" style="background-color: #f00;color: #fff">取消收藏</button></center>
							<?}?></td></tr>
                       
		<?
	    }
	    ?>


<tr height="22">  <td colspan="3">               
<center>

<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" style="text-align: center;">
  <tr>
    <td height="21"><div class="epages"><?=$pagelist;?></div></td>
  </tr>
</table>

</center> </td> </tr>
<?
}
?>

<?$link->close();?>