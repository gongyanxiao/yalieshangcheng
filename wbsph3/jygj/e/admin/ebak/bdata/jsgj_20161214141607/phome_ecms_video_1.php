<?php
@include("../../inc/header.php");

/*
		SoftName : EmpireBak
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

E_D("DROP TABLE IF EXISTS `phome_ecms_video`;");
E_C("CREATE TABLE `phome_ecms_video` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `classid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ttid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `onclick` int(10) unsigned NOT NULL DEFAULT '0',
  `plnum` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `totaldown` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `newspath` char(20) NOT NULL DEFAULT '',
  `filename` char(36) NOT NULL DEFAULT '',
  `userid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `username` char(20) NOT NULL DEFAULT '',
  `firsttitle` tinyint(1) NOT NULL DEFAULT '0',
  `isgood` tinyint(1) NOT NULL DEFAULT '0',
  `ispic` tinyint(1) NOT NULL DEFAULT '0',
  `istop` tinyint(1) NOT NULL DEFAULT '0',
  `isqf` tinyint(1) NOT NULL DEFAULT '0',
  `ismember` tinyint(1) NOT NULL DEFAULT '0',
  `isurl` tinyint(1) NOT NULL DEFAULT '0',
  `truetime` int(10) unsigned NOT NULL DEFAULT '0',
  `lastdotime` int(10) unsigned NOT NULL DEFAULT '0',
  `havehtml` tinyint(1) NOT NULL DEFAULT '0',
  `groupid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `userfen` smallint(5) unsigned NOT NULL DEFAULT '0',
  `titlefont` char(14) NOT NULL DEFAULT '',
  `titleurl` char(200) NOT NULL DEFAULT '',
  `stb` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `fstb` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `restb` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `keyboard` char(80) NOT NULL DEFAULT '',
  `title` char(100) NOT NULL DEFAULT '',
  `newstime` int(10) unsigned NOT NULL DEFAULT '0',
  `titlepic` char(120) NOT NULL DEFAULT '',
  `ftitle` char(120) NOT NULL DEFAULT '',
  `smalltext` char(255) NOT NULL DEFAULT '',
  `diggtop` int(11) NOT NULL DEFAULT '0',
  `video` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `classid` (`classid`),
  KEY `newstime` (`newstime`),
  KEY `ttid` (`ttid`),
  KEY `firsttitle` (`firsttitle`),
  KEY `isgood` (`isgood`),
  KEY `ispic` (`ispic`),
  KEY `useridis` (`userid`,`ismember`)
) ENGINE=MyISAM AUTO_INCREMENT=113 DEFAULT CHARSET=utf8");
E_D("replace into `phome_ecms_video` values('108','8','0','0','0','0','2016-12-13','108','1','admin','0','0','1','0','0','0','0','1481620900','1481620900','1','0','0','','/jsgj/shipinzhongxin/2016-12-13/108.html','1','1','1','','高新技术企业','1481620747','/jsgj/d/file/shipinzhongxin/2016-12-13/c5679139d90645786666a0dee2860684.jpg','','','0','');");
E_D("replace into `phome_ecms_video` values('105','8','0','0','0','0','2016-12-13','105','1','admin','0','0','1','0','0','0','0','1481620654','1481620737','1','0','0','','/jsgj/shipinzhongxin/2016-12-13/105.html','1','1','1','','聚升国际网络平台简介','1481620537','/jsgj/d/file/shipinzhongxin/2016-12-13/d2e44bcd58ef326fd6d5aa1a8b6e0880.jpg','','','0','');");
E_D("replace into `phome_ecms_video` values('106','8','0','0','0','0','2016-12-13','106','1','admin','0','0','1','0','0','0','0','1481620835','1481620835','1','0','0','','/jsgj/shipinzhongxin/2016-12-13/106.html','1','1','1','','高新技术企业','1481620747','/jsgj/d/file/shipinzhongxin/2016-12-13/c5679139d90645786666a0dee2860684.jpg','','','0','');");
E_D("replace into `phome_ecms_video` values('107','8','0','0','0','0','2016-12-13','107','1','admin','0','0','1','0','0','0','0','1481620900','1481620900','1','0','0','','/jsgj/shipinzhongxin/2016-12-13/107.html','1','1','1','','聚升国际网络平台简介','1481620537','/jsgj/d/file/shipinzhongxin/2016-12-13/d2e44bcd58ef326fd6d5aa1a8b6e0880.jpg','','','0','');");
E_D("replace into `phome_ecms_video` values('112','8','0','0','0','0','2016-12-13','112','1','admin','0','0','1','0','0','0','0','1481620909','1481620909','1','0','0','','/jsgj/shipinzhongxin/2016-12-13/112.html','1','1','1','','高新技术企业','1481620747','/jsgj/d/file/shipinzhongxin/2016-12-13/c5679139d90645786666a0dee2860684.jpg','','','0','');");
E_D("replace into `phome_ecms_video` values('111','8','0','0','0','0','2016-12-13','111','1','admin','0','0','1','0','0','0','0','1481620909','1481620909','1','0','0','','/jsgj/shipinzhongxin/2016-12-13/111.html','1','1','1','','聚升国际网络平台简介','1481620537','/jsgj/d/file/shipinzhongxin/2016-12-13/d2e44bcd58ef326fd6d5aa1a8b6e0880.jpg','','','0','');");
E_D("replace into `phome_ecms_video` values('110','8','0','0','0','0','2016-12-13','110','1','admin','0','0','1','0','0','0','0','1481620909','1481620909','1','0','0','','/jsgj/shipinzhongxin/2016-12-13/110.html','1','1','1','','高新技术企业','1481620747','/jsgj/d/file/shipinzhongxin/2016-12-13/c5679139d90645786666a0dee2860684.jpg','','','0','');");
E_D("replace into `phome_ecms_video` values('109','8','0','0','0','0','2016-12-13','109','1','admin','0','0','1','0','0','0','0','1481620909','1481620909','1','0','0','','/jsgj/shipinzhongxin/2016-12-13/109.html','1','1','1','','聚升国际网络平台简介','1481620537','/jsgj/d/file/shipinzhongxin/2016-12-13/d2e44bcd58ef326fd6d5aa1a8b6e0880.jpg','','','0','');");

@include("../../inc/footer.php");
?>