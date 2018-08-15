<?
$a='0';
$b='600';
$c='0.00001';
$outa= $a/600;
$outb= $b/600;
$outc= $c/600;
$out1= ($b+$c)/600;
$out2= ($a+$b)/600;
$out3= ($a+$b+$c)/600;

$outa= floor($outa);
$outb= floor($outb);
$outc= floor($outc);
$out1= floor($out1);
$out2= floor($out2);
$out3= floor($out3);

if($out3==0) {
	//$a,$b,$c不变,$outa为积分赠送权的个数
	echo $a;
	echo "<br/>";
	echo $b;
	echo "<br/>";
	echo $c;
	echo "<br/>";
	echo $outa;
}//第一种情况,加在a头上
 elseif($outa>0&&$outa==$out3) {
	$a=$a-$outa*600;
	//$b,$c不变,$outa为积分赠送权的个数
	echo $a;
	echo "<br/>";
	echo $b;
	echo "<br/>";
	echo $c;
	echo "<br/>";
	echo $outa;
}//第二种情况,加在b头上
 elseif($out2>0&&$out2==$out3) {
 	$b=$a+$b-$out2*600;
 	$a=0;
 	//$c不变,$out2为积分赠送权的个数
 	echo $a;
	echo "<br/>";
	echo $b;
	echo "<br/>";
	echo $c;
	echo "<br/>";
	echo $out2;
}//第三种情况,加在c头上
 elseif(($out3-$out2==1)||$outc>0) {
 	$c=$a+$b+$c-$out3*600;
 	$a=0;
 	$b=0;
 	//$out3为积分赠送权的个数
 	echo $a;
	echo "<br/>";
	echo $b;
	echo "<br/>";
	echo $c;
	echo "<br/>";
	echo $out3;
}