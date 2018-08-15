<?php
session_start();
$base64_string = $_POST['base64_string'];
$savename = uniqid().'.jpeg';//localResizeIMG压缩后的图片都是jpeg格式
    $savepath = '../uploads/'.$savename; 
    $image = base64_to_img( $base64_string, $savepath );
include_once "../config/zt_class.php";
if($image){
echo '{"status":1,"content":"图片上传成功","url":"'.$image.'"}';
if($_SESSION['pic11']<>""){
$p11=$_SESSION['pic11'].','.$savepath;
}else{
$p11=addslashes($savepath);
}
$_SESSION['pic11']=$p11;
  }else{
        echo '{"status":0,"content":"上传失败"}';
    } 

    function base64_to_img( $base64_string, $output_file ) {
        $ifp = fopen( $output_file, "wb" ); 
        fwrite( $ifp, base64_decode( $base64_string) ); 
        fclose( $ifp ); 
        return( $output_file ); 
    } 

?>