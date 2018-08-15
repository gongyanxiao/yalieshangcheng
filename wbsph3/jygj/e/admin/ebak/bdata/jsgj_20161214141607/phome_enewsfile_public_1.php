<?php
@include("../../inc/header.php");

/*
		SoftName : EmpireBak
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

E_D("DROP TABLE IF EXISTS `phome_enewsfile_public`;");
E_C("CREATE TABLE `phome_enewsfile_public` (
  `fileid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pubid` tinyint(1) NOT NULL DEFAULT '0',
  `filename` char(60) NOT NULL DEFAULT '',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0',
  `path` char(20) NOT NULL DEFAULT '',
  `adduser` char(30) NOT NULL DEFAULT '',
  `filetime` int(10) unsigned NOT NULL DEFAULT '0',
  `classid` tinyint(1) NOT NULL DEFAULT '0',
  `no` char(60) NOT NULL DEFAULT '',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `onclick` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `id` int(10) unsigned NOT NULL DEFAULT '0',
  `cjid` int(10) unsigned NOT NULL DEFAULT '0',
  `fpath` tinyint(1) NOT NULL DEFAULT '0',
  `modtype` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`fileid`),
  KEY `id` (`id`),
  KEY `type` (`type`),
  KEY `modtype` (`modtype`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8");
E_D("replace into `phome_enewsfile_public` values('1','0','86e8d034545ae69abf2f235395bcce5c.jpg','22961','2016-12-13','admin','1481614748','0','5823e66558499.jpg','1','0','0','0','0','0');");
E_D("replace into `phome_enewsfile_public` values('2','0','b294f0cb5ff5e1ee57684431b8cf9dc2.jpg','5379','2016-12-13','admin','1481614852','0','5812aa9eb6d4b.jpg','1','0','0','0','0','0');");
E_D("replace into `phome_enewsfile_public` values('3','0','b2bce64c17ef9d671781090c10a70638.jpg','6608','2016-12-13','admin','1481614969','0','5812aaaa4fd58.jpg','1','0','0','0','0','0');");
E_D("replace into `phome_enewsfile_public` values('4','0','c8a292ea54d580e4205789681ab88ad9.jpg','36093','2016-12-13','admin','1481615033','0','5823e297a494d.jpg','1','0','0','0','0','0');");

@include("../../inc/footer.php");
?>