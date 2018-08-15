<?php
@include("../../inc/header.php");

/*
		SoftName : EmpireBak
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

E_D("DROP TABLE IF EXISTS `phome_ecms_video_index`;");
E_C("CREATE TABLE `phome_ecms_video_index` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `classid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `checked` tinyint(1) NOT NULL DEFAULT '0',
  `newstime` int(10) unsigned NOT NULL DEFAULT '0',
  `truetime` int(10) unsigned NOT NULL DEFAULT '0',
  `lastdotime` int(10) unsigned NOT NULL DEFAULT '0',
  `havehtml` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `classid` (`classid`),
  KEY `checked` (`checked`),
  KEY `newstime` (`newstime`),
  KEY `truetime` (`truetime`,`id`),
  KEY `havehtml` (`classid`,`truetime`,`havehtml`,`checked`,`id`)
) ENGINE=MyISAM AUTO_INCREMENT=113 DEFAULT CHARSET=utf8");
E_D("replace into `phome_ecms_video_index` values('106','8','1','1481620747','1481620835','1481620835','1');");
E_D("replace into `phome_ecms_video_index` values('105','8','1','1481620537','1481620654','1481620737','1');");
E_D("replace into `phome_ecms_video_index` values('112','8','1','1481620747','1481620909','1481620909','1');");
E_D("replace into `phome_ecms_video_index` values('111','8','1','1481620537','1481620909','1481620909','1');");
E_D("replace into `phome_ecms_video_index` values('110','8','1','1481620747','1481620909','1481620909','1');");
E_D("replace into `phome_ecms_video_index` values('109','8','1','1481620537','1481620909','1481620909','1');");
E_D("replace into `phome_ecms_video_index` values('108','8','1','1481620747','1481620900','1481620900','1');");
E_D("replace into `phome_ecms_video_index` values('107','8','1','1481620537','1481620900','1481620900','1');");

@include("../../inc/footer.php");
?>