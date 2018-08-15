<?php
header("Content-type: text/html; charset=utf-8");
/**
 * 不需要清除数据的用户
 *
 * @var string $notClearUsers
 */


$GLOBALS['notClearUserArr'] = array(
    '15910142205',
    '15949880912',
    '13455548189',
    '18705358297',
    '15162751528',
    '18653517856',
    '15653545558',
    '18865661666',
    '15688532068',
    '13355452719',
    '18612539198',
    '15966570777',
    '15244508917',
    '13589860685',
    '15064500888',
    '15853578878',
    '15965355803',
    '13645358178',
    '18615351656',
    '15954520926',
    '13356999001',
    '13356999002',
    '13356999003',
    '13356999004',
    '13356999005',
    '13356999007',
    '15098508029',
    '13918745349',
    '15653586338',
    '13361370955',
    '18253511343',
    '13012592708',
    '13697644313',
    '18753573364',
    '13573515707',
    '13156917311',
    '18660008198',
    '13285359732',
    '13356999836'
);


$notClearUsers ="";
foreach ($GLOBALS['notClearUserArr'] AS $key => $val)
{
    $notClearUsers = $notClearUsers."'".$val."',";
}
$notClearUsers = substr($notClearUsers, 0, strlen($notClearUsers)-1);
$notClearUsers="(".$notClearUsers.")";
$GLOBALS['notClearUsers'] = $notClearUsers;//组合成('','')的字符串





 


/**
 * 是否是不需要清空数据的人
 * 
 * @param unknown $user_name
 * @return boolean
 */
function isNotClearUser($user_name)
{
    $user_name=trim($user_name);
    $notClearUserArr = $GLOBALS['notClearUserArr'];
    foreach ($notClearUserArr as $key => $val) {
        if ($user_name == $val)
            return true;
    }
    
    return false;
}
?>