<?
   $jd_dbname="xbshp";
    $conn=mysql_connect("127.0.0.1","root","root");
    $host="127.0.0.1";
    $jd_user="root";
    $jd_pass="root";
     $db = mysql_connect($host,$jd_user,$jd_pass);
    @mysql_query("SET NAMES utf8"); 
    mysql_select_db($jd_dbname);
  $connarr=array('pagetitle' => '聚元国际|安徽诺斯贝尔电子商务有限公司',
  				'pagekey'=>'聚元国际|安徽诺斯贝尔电子商务有限公司',
  				'pagedes'=>'聚元国际|安徽诺斯贝尔电子商务有限公司'

   );
?>