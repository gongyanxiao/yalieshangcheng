<?php
@include("../../inc/header.php");

/*
		SoftName : EmpireBak
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

E_D("DROP TABLE IF EXISTS `phome_enewslink`;");
E_C("CREATE TABLE `phome_enewslink` (
  `lid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `lname` varchar(100) NOT NULL DEFAULT '',
  `lpic` varchar(255) NOT NULL DEFAULT '',
  `lurl` varchar(255) NOT NULL DEFAULT '',
  `ltime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `onclick` int(11) NOT NULL DEFAULT '0',
  `width` varchar(10) NOT NULL DEFAULT '',
  `height` varchar(10) NOT NULL DEFAULT '',
  `target` varchar(10) NOT NULL DEFAULT '',
  `myorder` tinyint(4) NOT NULL DEFAULT '0',
  `email` varchar(60) NOT NULL DEFAULT '',
  `lsay` text NOT NULL,
  `checked` tinyint(1) NOT NULL DEFAULT '0',
  `ltype` smallint(6) NOT NULL DEFAULT '0',
  `classid` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`lid`),
  KEY `classid` (`classid`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8");
E_D("replace into `phome_enewslink` values('1','中国狼网','/jsgj/d/file/p/2016-12-13/86e8d034545ae69abf2f235395bcce5c.jpg','http://www.chinalw.30edu.com.cn/','2016-12-13 15:39:49','0','152','73','_blank','0','','','1','0','0');");
E_D("replace into `phome_enewslink` values('2','鼎凡律师','/jsgj/d/file/p/2016-12-13/b294f0cb5ff5e1ee57684431b8cf9dc2.jpg','http://www.hljdfls.cn/','2016-12-13 15:41:20','0','152','73','_blank','0','','','1','0','0');");
E_D("replace into `phome_enewslink` values('3','中国太平','/jsgj/d/file/p/2016-12-13/b2bce64c17ef9d671781090c10a70638.jpg','http://www.cpic.com.cn/','2016-12-13 15:43:02','0','152','73','_blank','0','','','1','0','0');");
E_D("replace into `phome_enewslink` values('4','中国正能量','/jsgj/d/file/p/2016-12-13/c8a292ea54d580e4205789681ab88ad9.jpg','http://www.zhongguozhengnengliang.com/','2016-12-13 15:44:06','0','152','73','_blank','0','','','1','0','0');");
E_D("replace into `phome_enewslink` values('5','鼎凡律师','/jsgj/d/file/p/2016-12-13/b294f0cb5ff5e1ee57684431b8cf9dc2.jpg','http://www.hljdfls.cn/','2016-12-13 16:04:05','0','152','73','_blank','0','','','1','0','0');");
E_D("replace into `phome_enewslink` values('6','中国太平','/jsgj/d/file/p/2016-12-13/b2bce64c17ef9d671781090c10a70638.jpg','http://www.cpic.com.cn/','2016-12-13 16:04:55','0','152','73','_blank','0','','','1','0','0');");

@include("../../inc/footer.php");
?>