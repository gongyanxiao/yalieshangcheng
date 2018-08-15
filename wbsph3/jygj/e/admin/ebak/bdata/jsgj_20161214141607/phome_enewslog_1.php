<?php
@include("../../inc/header.php");

/*
		SoftName : EmpireBak
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

E_D("DROP TABLE IF EXISTS `phome_enewslog`;");
E_C("CREATE TABLE `phome_enewslog` (
  `loginid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL DEFAULT '',
  `logintime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `loginip` varchar(20) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `password` varchar(30) NOT NULL DEFAULT '',
  `loginauth` tinyint(1) NOT NULL DEFAULT '0',
  `ipport` varchar(6) NOT NULL DEFAULT '',
  PRIMARY KEY (`loginid`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8");
E_D("replace into `phome_enewslog` values('1','admin','2016-12-12 17:06:17','::1','1','','0','23039');");
E_D("replace into `phome_enewslog` values('2','admin','2016-12-13 08:07:19','::1','1','','0','1670');");
E_D("replace into `phome_enewslog` values('3','admin','2016-12-13 09:46:03','::1','1','','0','46516');");
E_D("replace into `phome_enewslog` values('4','admin','2016-12-13 10:49:26','::1','1','','0','23750');");
E_D("replace into `phome_enewslog` values('5','admin','2016-12-13 11:00:15','::1','1','','0','57920');");
E_D("replace into `phome_enewslog` values('6','admin','2016-12-13 11:03:27','::1','1','','0','24201');");
E_D("replace into `phome_enewslog` values('7','admin','2016-12-14 08:06:45','::1','1','','0','14450');");
E_D("replace into `phome_enewslog` values('8','admin','2016-12-14 14:00:56','::1','1','','0','46341');");

@include("../../inc/footer.php");
?>