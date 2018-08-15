 
	<?php
include "../../jygj/mall/myphplib/db.php";
include "../../myphplib/message.php";
// 不检查登录状态

$act_id = intval($_POST['act_id']);
// 修改活动状态
$sql = "select count(0) from    xbmall_hd_chou_jiang_bao_ming    where act_id=" . $act_id;
$num = getOne($sql);

die($num);

?>
	 
 