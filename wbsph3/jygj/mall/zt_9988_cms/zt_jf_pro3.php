<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<style type="text/css">
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}

body, td, th {
	font-size: 12px;
	color: #666;
}

.STYLE3 {
	font-size: 36px;
	font-weight: bold;
	color: #FF0000;
}
</style>
</head>

<body>
<?php
session_start();
if ($_COOKIE['i'] != "1") {
    exit();
}

$session1 = htmlspecialchars(trim($_GET['session1']));
if ($session1 != $_SESSION['uniqid1']) {
    ?>
	<script type="text/javascript">alert("不要重复点击！");location.replace(document.referrer);</script>
<?
    exit();
} else {
    $_SESSION['uniqid1'] = md5(uniqid('jygj', true));
}
// 积分赠送批量操作
include "../config/zt_config.php";
date_default_timezone_set("Asia/Shanghai");

$users =array('17605935819',
    '15299950288',
    '13784989963',
    '13955091299',
    '13283727779',
    '15215501928',
    '18365293251',
    '15105507059',
    '13955014844',
    '15052292012',
    '14790025580',
    '15856650455',
    '15055011706',
    '15056980003',
    '13083308260',
    '13309605740',
    '15855000346',
    '17755069795',
    '18255018888',
    '13516408021',
    '18365287440',
    '18955008168',
    '18165570630',
    '15755091312',
    '15955073578',
    '15105506955',
    '13721001851',
    '18256900571',
    '13451698727',
    'h15020377517',
    '13855041138',
    '15755099351',
    '18075271806',
    '13855010022',
    '18098392260',
    '13965991998',
    '18055008379',
    '13855083421',
    '15357119883',
    '13965631581',
    '13866912766',
    '18365285849',
    '13865502942',
    '18755014341',
    '13855040744',
    '18755014100',
    '15869594163',
    '15357112069',
    '18726622875',
    '13955036200',
    '13955027945',
    '15705506669',
    '13637011288',
    '13485753913',
    '18019825718',
    '15381835060',
    '18055083006',
    '13866538656',
    '15955005826',
    '18905504598',
    '18651679888',
    '18355081525',
    '13905632688',
    '18900509665',
    '15655070163',
    '15855017788',
    '15955503617',
    '15655169666',
    '18655007122',
    '13955036176',
    '18755039903',
    '13505500039',
    '15357122888',
    '13955039767',
    '13505555133',
    '18855055777',
    '13305502666',
    '18226806138',
    '13805510303',
    '15357125888',
    '13866525888',
    '18055000222',
    '15256691666',
    '13865500346',
    '18855586955',
    '13965959666',
    '17755085505',
    '18755019701',
    '18949328333',
    '13470708661',
    '18712032109',
    '15212003619',
    '15056015890',
    '13866531982',
    '13093326172',
    '13696753789',
    '13696753192',
    '18955015791',
    '18365087853',
    '13855013345',
    '18155099007',
    '13865509551',
    '15385013918',
    '13956302768',
    '13865829246',
    '15955050888',
    '15055000234',
    '13359003076',
    '18954130900',
    '15506558107',
    '18366458622',
    '13561460422',
    '13963423116',
    '18263426963',
    '13562040787',
    '15066434707',
    '15506558086',
    '13561234611',
    '13256815511',
    '13044068444',
    '13455812088',
    '18266400078',
    '15149368168',
    '13863897130',
    '13947273102',
    '15661365369',
    '18648639528',
    '18004788839',
    '13190793518',
    '13262159955',
    '13603738521',
    '13346666615',
    '17793526999',
    '13893582332',
    '郭钰山',
    '18693326003',
    '18993340566',
    '13893550891',
    '13689460552',
    '18394540827',
    '13359336915',
    '18693333393',
    '13830386700',
    '18419410226',
    '18794601633',
    '13830380763',
    '13993363995',
    '18693320080',
    '15050749300',
    '18919338687',
    '18034631727',
    '15339338822',
    '15336038968',
    '15809332291',
    '13909332866',
    '15193565411',
    '13993388878',
    '18693330956',
    '18693337779',
    '13993372181',
    '13919531127',
    '15825842948',
    '13195879930',
    '18152250338',
    '13993361818',
    '15095519729',
    '13884559911',
    '18093333444',
    '13689335828',
    '13199566123',
    '13845349494',
    '15046395689',
    '13946351828',
    '13555004452',
    '13329303737',
    '15326928282',
    '13337002789',
    '15024768693',
    '15049278388',
    '13474982558',
    '15661316645',
    '13191478891',
    '18686102959',
    '18947720012',
    '13394721727',
    '13214959880',
    '15024749990',
    '15849253865',
    '13947271294',
    '13704733683',
    '18686255775',
    '15049336976',
    '13848288780',
    '15148204806',
    '18647214780',
    '15754724067',
    '15848213304',
    '13337188848',
    '13384861966',
    '15024773292',
    '13314842269',
    '13847224574',
    '13848262039',
    '15048882322',
    '13039557628',
    '13304789316',
    '13664728364',
    '13848028235',
    '15804725173',
    '13804721286',
    '13634724658',
    '13947229555',
    '18547201020',
    '15324613519',
    '13462205608',
    '15544291956',
    '15090461861',
    '13937917859',
    '13075530678',
    '18039459648',
    '15515391553',
    '15896679599',
    '13262156156',
    '15993669787',
    '13592087481',
    '15837059309',
    '15900342017',
    '18638819446',
    '13903795611',
    '15237336106',
    '13639638690',
    '15903882228',
    '18268500661',
    '15236628674',
    '13838851168',
    '18530211058',
    '18637311072',
    '15083138668',
    '13803855060',
    '18439087456',
    '13592023634',
    '15137321056',
    '13937929358',
    '13569886871',
    '18837327999',
    '18625915128',
    '13084217190',
    '13323731151',
    '18238610099',
    '18037179698',
    '15603806988',
    '18947288733',
    '15847652971',
    '18947715577',
    '18647833255',
    '13514893170',
    '13488576633',
    '13947844926',
    '13500621161',
    '13947856187',
    '15047233621',
    '15148233950',
    '13948929503',
    '13789628250',
    '13080217771',
    '15049250274',
    '13347081294',
    '18947249799',
    '14747223955',
    '15326916189',
    '15647248901',
    '13947108278',
    '15947228019',
    '13847189882',
    '13948322043',
    '15049338883',
    '13171271399',
    '18147170510',
    '13488523306',
    '13171266551',
    '15335503699',
    '13847362058',
    '13009548697',
    '18648262878',
    '18697418788',
    '15690988859',
    '13644822477',
    '13664772216',
    '13191444110',
    '15047215740',
    '13088451689',
    '13947213957',
    '18047218038',
    '13848225202',
    '13804777890',
    '15148210007',
    '18648254017',
    '15847297897',
    '15848649071',
    '13948731035',
    '13664077290',
    '15947627386',
    '13947281923',
    '15044965164',
    '17747812357',
    '13947265880',
    '13947287095',
    '13848625189',
    '15847257693',
    '15647249846',
    '13848021584',
    '15904786266',
    '18247274479',
    '13847358087',
    '13015065967',
    '18247266664',
    '15947288957',
    '15332722134',
    '13847389872',
    '15049225232',
    '15924445672',
    '13754082077',
    '14747227707',
    '17077763459',
    '15049595069',
    '15847816000',
    '15848863632',
    '15049348189',
    '13190858860',
    '15924441144',
    '13948422720',
    '18061932583',
    '17705165568',
    '18652993682',
    '18795555555',
    '15272442853',
    '18611170732',
    '15855016802',
    '13951871535',
    '13020437310',
    '15174957055');


/**
 * 是否包含这个用户
 * @param unknown $user
 * @return boolean
 */
function  isUser($user){
    global   $users;
    $length = count($users);
    for ($i=0;$i<$length;$i++){
     if($users[$i]==$user)   
         return true;
    }
    return false;
}

$date = date("Y-m-d H:i:s");
$cssk = strtotime($date);
$db = mysql_connect($db_host, $db_user, $db_pwd);
mysql_query("SET NAMES $coding");
mysql_select_db($db_database);
$sql1 = "select * from zt_ed  order by id desc";
$q1 = mysql_query($sql1);
$ss = mysql_fetch_assoc($q1);
$jf1 = $ss['jf1'];
$jf2 = $ss['jf2'];
$jf3 = $ss['jf3'];
// 提醒是否确定赠送额定是否正确
$week = date("w");
$sqlmx = "SELECT rq FROM `zt_mred` ORDER BY id desc";
$qmx = mysql_query($sqlmx);
$rmx = mysql_fetch_assoc($qmx);
$sczced = strtotime($rmx['rq']);
if ($cssk - $sczced < 86000) {
    ?>
<script type="text/javascript">alert("距上次赠送时间还过86000s");location.replace(document.referrer);</script>
<?
    exit();
}
// 可提现部分
if ($week >= '1' and $week <= '5') {
    // 记录今日额度
    $sqlx = "INSERT INTO  `zt_mred`(`id`,`xmfl`,`ed`,`rq`,`bz`) VALUES(null,'兑现积分额度','$jf1','$date','');";
    mysql_query($sqlx);
    $sql2 = "select * from zt_zsq where zsq>='1'   order by id desc";
    $r1 = mysql_query($sql2, $db);
    $num1 = mysql_num_rows($r1);
    for ($i = 0; $i <= $num1; $i ++) {
        $data = mysql_fetch_array($r1);
        $user = $data['user'];
        if(isUser($user)==false){//没有在分配之列
            var_dump("用户{$user}不在分配之列");
            continue;
        }
        $sql3 = "select * from zt_zs where user='$user' order by id desc";
        $query3 = mysql_query($sql3);
        $num3 = mysql_num_rows($query3);
        // 判断是否存在记录
        $out = mysql_fetch_assoc($query3);
        $zw1 = $jf1 * $data['zsq'];
        $zw1 = round($zw1, 2);
        $zw0 = $num3 == '1' ? $out['zw'] : 0;
        $zw = $zw1 + $zw0;
        $zw4 = $num3 == '1' ? $out['jqzsjf'] : 0;
        $zw2 = $zw1 + $zw4;
        $zw3 = floor($zw2 / 600);
        if ($zw3 > 0) {
            $zw2 = $zw2 - $zw3 * 600;
        }
        // 查询赠送权
        
        $sql0 = "select * from zt_zsq where user='$user' and user<>''  order by id desc";
        $q0 = mysql_query($sql0);
        $ss0 = mysql_fetch_assoc($q0);
        $zsq0 = $ss0['zsq'] - $zw3;
        $sql01 = "update zt_zsq set zsq='$zsq0'  where user='$user'";
        $zs0 = mysql_query($sql01);
        // 更新至用户积分中
        $sql1 = "select jf,zsq,yzs from xbmall_users where user_name='$user'  order by id desc";
        $q1 = mysql_query($sql1);
        $ss = mysql_fetch_assoc($q1);
        $jf = (empty($ss['jf']) || $ss['jf'] == '') ? 0 : $ss['jf'];
        $zsq = (empty($ss['zsq']) || $ss['zsq'] == '') ? 0 : $ss['zsq'];
        $yzs = (empty($ss['yzs']) || $ss['yzs'] == '') ? 0 : $ss['yzs'];
        $jf5 = $zw1 + $jf;
        $zsq = $zsq - $zw3;
        $yzs = $yzs + $zw3;
        $sql5 = "update xbmall_users set yzs='$yzs',zsq='$zsq',jf='$jf5' where user='$user'";
        mysql_query($sql5);
        // 赠送记录到数据库
        $bz = "当日赠送" . $zw1;
        $sqlzw = "INSERT INTO  `zt_jf_record`(`id`,`xmfl`,`date`,`jf`,`bz`,`user`,`bh`) VALUES(null,'兑现积分','$date','$zw1','$bz','$user','0');";
        mysql_query($sqlzw);
        
        // 赠送记录
        if ($num3 >= 1) {
            $sql4 = "update zt_zs set zw='$zw',jqzsjf='$zw2',date='$date' where user='$user'";
            mysql_query($sql4);
        } else {
            $sql4 = "INSERT INTO  `zt_zs`(`id`,`zw`,`zl`,`zr`,`date`,`user`,`jqzsjf`) VALUES(null,'$zw1','0','0','$date','$user','$zw2');";
            mysql_query($sql4);
        }
        if ($zs0) {
            echo $user . '今天赠送完毕' . '<br>';
        }
        // 赠送全结束
    }
    // 周五赠送记录
}
// 线上兑换部分
if ($week == '6') {
    // 记录今日额度
    $sqlx = "INSERT INTO  `zt_mred`(`id`,`xmfl`,`ed`,`rq`,`bz`) VALUES(null,'消费积分额度','$jf2','$date','');";
    mysql_query($sqlx);
    
    $sql2 = "select * from zt_zsq where zsq>='1' and user<>''  order by id desc";
    $r1 = mysql_query($sql2, $db);
    $num1 = mysql_num_rows($r1);
    for ($i = 0; $i < $num1; $i ++) {
        $data = mysql_fetch_array($r1);
        $user = $data['user'];
        if(isUser($user)==false){//没有在分配之列
            var_dump("用户{$user}不在分配之列");
            continue;
        }
        $sql3 = "select * from zt_zs where user='$user' order by id desc";
        $query3 = mysql_query($sql3);
        $num3 = mysql_num_rows($query3);
        // 判断是否存在记录
        $out = mysql_fetch_assoc($query3);
        $zw1 = $jf2 * $data['zsq'];
        $zw1 = round($zw1, 2);
        $zw0 = empty($out['zl']) ? 0 : $out['zl'];
        $zw = $zw1 + $zw0;
        $zw4 = empty($out['jqzsjf']) ? 0 : $out['jqzsjf'];
        $zw2 = $zw1 + $zw4;
        $zw3 = floor($zw2 / 600);
        if ($zw3 > 0) {
            $zw2 = $zw2 - $zw3 * 600;
        }
        // 查询赠送权
        
        $sql0 = "select * from zt_zsq where user='$user'  order by id desc";
        $q0 = mysql_query($sql0);
        $ss0 = mysql_fetch_assoc($q0);
        $zsq0 = $ss0['zsq'] - $zw3;
        $sql01 = "update zt_zsq set zsq='$zsq0'  where user='$user'";
        $zs0 = mysql_query($sql01);
        // 更新至用户积分中
        $sql1 = "select xfjf,zsq,yzs from xbmall_users where user_name='$user'  order by id desc";
        $q1 = mysql_query($sql1);
        $ss = mysql_fetch_assoc($q1);
        $xfjf = (empty($ss['xfjf']) || $ss['xfjf'] == '') ? 0 : $ss['xfjf'];
        $jf5 = $zw1 + $xfjf;
        $zsq = (empty($ss['zsq']) || $ss['zsq'] == '') ? 0 : $ss['zsq'];
        $yzs = (empty($ss['yzs']) || $ss['yzs'] == '') ? 0 : $ss['yzs'];
        $zsq = $zsq - $zw3;
        $yzs = $yzs + $zw3;
        $sql5 = "update xbmall_users set yzs='$yzs',xfjf='$jf5',zsq='$zsq' where user='$user'";
        mysql_query($sql5);
        // 赠送记录到数据库
        $bz = "当日赠送" . $zw1;
        $sqlzw = "INSERT INTO  `zt_jf_record`(`id`,`xmfl`,`date`,`jf`,`bz`,`user`,`bh`) VALUES(null,'消费兑换积分','$date','$zw1','$bz','$user','0');";
        mysql_query($sqlzw);
        
        // 赠送记录
        if ($num3 >= 1) {
            $sql4 = "update zt_zs set zl='$zw',jqzsjf='$zw2',date='$date' where user='$user'";
            mysql_query($sql4);
        } else {
            $sql4 = "INSERT INTO  `zt_zs`(`id`,`zw`,`zl`,`zr`,`date`,`user`,`jqzsjf`) VALUES(null,'0','$zw1','0','$date','$user','$zw2');";
            mysql_query($sql4);
        }
        if ($zs0) {
            echo $user . '今天赠送完毕' . '<br>';
        }
        // 赠送全结束
    }
    // 周六赠送记录
}

// 消费保险积分
if ($week == '0') {
    // 记录今日额度
    $sqlx = "INSERT INTO  `zt_mred`(`id`,`xmfl`,`ed`,`rq`,`bz`) VALUES(null,'养老积分额度','$jf3','$date','');";
    mysql_query($sqlx);
    
    $sql2 = "select * from zt_zsq where zsq>='1' and user<>''  order by id desc";
    $r1 = mysql_query($sql2, $db);
    $num1 = mysql_num_rows($r1);
    while ($data = mysql_fetch_array($r1)) {
        $user = $data['user'];
        if(isUser($user)==false){//没有在分配之列
            var_dump("用户{$user}不在分配之列");
            continue;
        }
        
        $sql3 = "select * from zt_zs where user='$user' order by id desc";
        $query3 = mysql_query($sql3);
        
        $num3 = mysql_num_rows($query3);
        // 判断是否存在记录
        $out = mysql_fetch_assoc($query3);
        $sqlv = "select yljf from xbmall_users where user_name='$user' order by id desc";
        $queryv = mysql_query($sqlv);
        $outv = mysql_fetch_assoc($queryv);
        $zw1 = $jf3 * $data['zsq'];
        $zw1 = round($zw1, 2);
        // 赠送记录到数据库
        $bz = "当日赠送" . $zw1;
        $sqlzw = "INSERT INTO  `zt_jf_record`(`id`,`xmfl`,`date`,`jf`,`bz`,`user`,`bh`) VALUES(null,'养老教育积分','$date','$zw1','$bz','$user','0');";
        
        mysql_query($sqlzw);
        
        $zw0 = empty($out['zr']) ? 0 : $out['zr'];
        $zw = $zw1 + $zw0;
        $yljf = (empty($outv['yljf']) || $outv['yljf'] == '') ? 0 : $outv['yljf'];
        $zwv = $zw1 + $yljf;
        $zwa = floor($zwv / 1000);
        $zwb = $zwa * 1000;
        if ($zwa > 0) {
            for ($i = 0; $i < $zwa; $i ++) {
                $x = $i + 1;
                $bzn = '这是' . $user . '于' . $date . '扣除' . $zwb . '的养老教育积分生成的第' . $x . '个保单';
                $sqln = "INSERT INTO `zt_bd`(`id`, `user`, `zt`, `xmfl`, `date`, `jf`, `bz`, `shbz`) VALUES (null,'$user','0','保单兑换','$date','1000','$bzn','')";
                mysql_query($sqln);
                $sqlm = "select max(id) maxid from `zt_bd`";
                $querym = mysql_query($sqlm);
                $rowm = mysql_fetch_array($querym);
                $res = empty($rowm['maxid']) ? 0 : $rowm['maxid'];
                $bzm = '生成保单扣除1000';
                $sqlzwm = "INSERT INTO  `zt_jf_record`(`id`,`xmfl`,`date`,`jf`,`bz`,`user`,`bh`) VALUES(null,'保单兑换','$date','-1000','$bzm','$user','$res');";
                mysql_query($sqlzwm);
            }
            $zwv = $zwv - $zwb;
        }
        $zw4 = empty($out['jqzsjf']) ? 0 : $out['jqzsjf'];
        $zw2 = $zw1 + $zw4;
        $zw3 = floor($zw2 / 600);
        if ($zw3 > 0) {
            $zw2 = $zw2 - $zw3 * 600;
        }
        // 查询赠送权
        
        $sql0 = "select * from zt_zsq where user='$user'  order by id desc";
        $q0 = mysql_query($sql0);
        $ss0 = mysql_fetch_assoc($q0);
        $zsq0 = $ss0['zsq'] - $zw3;
        $sql01 = "update zt_zsq set zsq='$zsq0'  where user='$user'";
        
        $zs0 = mysql_query($sql01);
        // 更新至用户积分中
        $sql1 = "select yljf,zsq,yzs from xbmall_users where user_name='$user'  order by id desc";
        $q1 = mysql_query($sql1);
        $ss = mysql_fetch_assoc($q1);
        $zsq = (empty($ss['zsq']) || $ss['zsq'] == '') ? 0 : $ss['zsq'];
        $yzs = (empty($ss['yzs']) || $ss['yzs'] == '') ? 0 : $ss['yzs'];
        $zsq = $zsq - $zw3;
        $yzs = $yzs + $zw3;
        $sql5 = "update xbmall_users set yzs='$yzs',yljf='$zwv',zsq='$zsq' where user='$user'";
        
        mysql_query($sql5);
        
        // 赠送记录
        if ($num3 >= 1) {
            $sql4 = "update zt_zs set zr='$zw',jqzsjf='$zw2',date='$date' where user='$user'";
            
            mysql_query($sql4);
        } else {
            $sql4 = "INSERT INTO  `zt_zs`(`id`,`zw`,`zl`,`zr`,`date`,`user`,`jqzsjf`) VALUES(null,'0','0','$zw','$date','$user','$zw2');";
            
            mysql_query($sql4);
        }
        if ($zs0) {
            echo $user . '今天赠送完毕' . '<br>';
        }
        // 赠送全结束
    }
    // 周日赠送记录
}

?>
<table width="548" height="227" border="0" align="center"
		style="border: 1px #FF3300 dashed; margin-top: 58px;">
		<tr>
			<td align="center" valign="middle" bgcolor="#FFFF99"><span
				class="STYLE3">今日赠送操作完成!</span> <br><?php
    $weekarray = array(
        "日",
        "一",
        "二",
        "三",
        "四",
        "五",
        "六"
    );
    echo date("Y-m-d H:i:s") . " 星期" . $weekarray[date("w")];
    ?></td>
		</tr>
	</table>
</body>
</html>
