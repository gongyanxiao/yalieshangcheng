<?php
@include("../../inc/header.php");

/*
		SoftName : EmpireBak
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

E_D("DROP TABLE IF EXISTS `phome_enewsfile_1`;");
E_C("CREATE TABLE `phome_enewsfile_1` (
  `fileid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pubid` bigint(16) unsigned NOT NULL DEFAULT '0',
  `filename` char(60) NOT NULL DEFAULT '',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0',
  `path` char(20) NOT NULL DEFAULT '',
  `adduser` char(30) NOT NULL DEFAULT '',
  `filetime` int(10) unsigned NOT NULL DEFAULT '0',
  `classid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `no` char(60) NOT NULL DEFAULT '',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `onclick` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `id` int(10) unsigned NOT NULL DEFAULT '0',
  `cjid` int(10) unsigned NOT NULL DEFAULT '0',
  `fpath` tinyint(1) NOT NULL DEFAULT '0',
  `modtype` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`fileid`),
  KEY `id` (`id`),
  KEY `type` (`type`),
  KEY `classid` (`classid`),
  KEY `pubid` (`pubid`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8");
E_D("replace into `phome_enewsfile_1` values('11','0','0c55135dc99388392b8ccd42eff03580.jpg','12484','2016-12-14','admin','1481695683','1','logo.png','1','0','117','117','0','0');");
E_D("replace into `phome_enewsfile_1` values('9','1000010000000032','ad858ee256141842c7cf3fd14138a4f9.jpg','753834','2016-12-14','admin','1481684362','1','zz.jpg','1','0','32','0','0','0');");
E_D("replace into `phome_enewsfile_1` values('8','1000100000000106','c5679139d90645786666a0dee2860684.jpg','49645','2016-12-13','admin','1481620794','8','580ed0b60b2b6.jpg','1','0','106','0','0','0');");
E_D("replace into `phome_enewsfile_1` values('7','1000100000000105','d2e44bcd58ef326fd6d5aa1a8b6e0880.jpg','39039','2016-12-13','admin','1481620622','8','5836751c5357f.jpg','1','0','105','0','0','0');");
E_D("replace into `phome_enewsfile_1` values('10','1000010000000031','5b6b7f1da55b2e2d8754ab63caa4fb9a.gif','109765','2016-12-14','admin','1481686568','1','1479378036222990.gif','1','0','31','0','0','0');");

@include("../../inc/footer.php");
?>