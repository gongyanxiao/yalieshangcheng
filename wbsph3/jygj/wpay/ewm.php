<?php    
    include 'phpqrcode.php';    
    $value = 'http://www.zt315.cn'; //��ά������   
    $errorCorrectionLevel = 'L'; //�ݴ���   
    $matrixPointSize = 6; //����ͼƬ��С

    // ���ɶ�ά��ͼƬ   
    QRcode::png($value, 'qrcode1.png', $errorCorrectionLevel, $matrixPointSize, 2);
    // �����ά��ͼƬ
    echo '<img src="qrcode1.png">'; 
?>