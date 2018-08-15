<?php    
    include 'phpqrcode.php';    
    $value = 'http://www.zt315.cn'; //二维码内容   
    $errorCorrectionLevel = 'L'; //容错级别   
    $matrixPointSize = 6; //生成图片大小

    // 生成二维码图片   
    QRcode::png($value, 'qrcode1.png', $errorCorrectionLevel, $matrixPointSize, 2);
    // 输出二维码图片
    echo '<img src="qrcode1.png">'; 
?>