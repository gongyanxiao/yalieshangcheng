<?php
@include("../../inc/header.php");

/*
		SoftName : EmpireBak
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

E_D("DROP TABLE IF EXISTS `phome_ecms_news_index`;");
E_C("CREATE TABLE `phome_ecms_news_index` (
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
) ENGINE=MyISAM AUTO_INCREMENT=118 DEFAULT CHARSET=utf8");
E_D("replace into `phome_ecms_news_index` values('105','1','1','1481534831','1481695423','1481695423','1');");
E_D("replace into `phome_ecms_news_index` values('104','1','1','1481534831','1481695423','1481695423','1');");
E_D("replace into `phome_ecms_news_index` values('117','1','1','1481534831','1481695439','1481695439','1');");
E_D("replace into `phome_ecms_news_index` values('116','1','1','1481534831','1481695439','1481695439','1');");
E_D("replace into `phome_ecms_news_index` values('115','1','1','1481534831','1481695439','1481695439','1');");
E_D("replace into `phome_ecms_news_index` values('114','1','1','1481534831','1481695439','1481695439','1');");
E_D("replace into `phome_ecms_news_index` values('113','1','1','1481534831','1481695439','1481695439','1');");
E_D("replace into `phome_ecms_news_index` values('112','1','1','1481534831','1481695439','1481695439','1');");
E_D("replace into `phome_ecms_news_index` values('111','1','1','1481534831','1481695439','1481695439','1');");
E_D("replace into `phome_ecms_news_index` values('110','1','1','1481534831','1481695439','1481695439','1');");
E_D("replace into `phome_ecms_news_index` values('109','1','1','1481534831','1481695432','1481695432','1');");
E_D("replace into `phome_ecms_news_index` values('108','1','1','1481534831','1481695432','1481695432','1');");
E_D("replace into `phome_ecms_news_index` values('107','1','1','1481534831','1481695432','1481695432','1');");
E_D("replace into `phome_ecms_news_index` values('106','1','1','1481534831','1481695432','1481695432','1');");
E_D("replace into `phome_ecms_news_index` values('31','1','1','1481534831','1481535193','1481686793','1');");
E_D("replace into `phome_ecms_news_index` values('32','1','1','1481534831','1481535193','1481684367','1');");
E_D("replace into `phome_ecms_news_index` values('75','5','1','1481534831','1481619521','1481619521','1');");
E_D("replace into `phome_ecms_news_index` values('76','5','1','1481534831','1481619521','1481619521','1');");
E_D("replace into `phome_ecms_news_index` values('77','5','1','1481534831','1481619521','1481619521','1');");
E_D("replace into `phome_ecms_news_index` values('78','5','1','1481534831','1481619521','1481619521','1');");
E_D("replace into `phome_ecms_news_index` values('37','7','1','1481534831','1481595517','1481595517','1');");
E_D("replace into `phome_ecms_news_index` values('38','7','1','1481534831','1481595517','1481595517','1');");
E_D("replace into `phome_ecms_news_index` values('73','10','1','1481601856','1481601878','1481601878','1');");
E_D("replace into `phome_ecms_news_index` values('79','5','1','1481534831','1481619521','1481619521','1');");
E_D("replace into `phome_ecms_news_index` values('80','5','1','1481534831','1481619521','1481619521','1');");
E_D("replace into `phome_ecms_news_index` values('81','5','1','1481534831','1481619521','1481619521','1');");
E_D("replace into `phome_ecms_news_index` values('82','5','1','1481534831','1481619521','1481619521','1');");
E_D("replace into `phome_ecms_news_index` values('83','5','1','1481534831','1481619521','1481619521','1');");
E_D("replace into `phome_ecms_news_index` values('84','5','1','1481534831','1481619521','1481619521','1');");
E_D("replace into `phome_ecms_news_index` values('85','5','1','1481534831','1481619521','1481619521','1');");
E_D("replace into `phome_ecms_news_index` values('86','5','1','1481534831','1481619521','1481619521','1');");
E_D("replace into `phome_ecms_news_index` values('87','5','1','1481534831','1481619521','1481619521','1');");
E_D("replace into `phome_ecms_news_index` values('88','5','1','1481534831','1481619521','1481619521','1');");
E_D("replace into `phome_ecms_news_index` values('89','5','1','1481534831','1481619521','1481619521','1');");
E_D("replace into `phome_ecms_news_index` values('90','5','1','1481534831','1481619521','1481619521','1');");
E_D("replace into `phome_ecms_news_index` values('91','5','1','1481534831','1481619521','1481619521','1');");
E_D("replace into `phome_ecms_news_index` values('92','5','1','1481534831','1481619521','1481619521','1');");
E_D("replace into `phome_ecms_news_index` values('93','5','1','1481534831','1481619521','1481619521','1');");
E_D("replace into `phome_ecms_news_index` values('94','5','1','1481534831','1481619521','1481619521','1');");
E_D("replace into `phome_ecms_news_index` values('95','5','1','1481534831','1481619521','1481619521','1');");
E_D("replace into `phome_ecms_news_index` values('96','5','1','1481534831','1481619521','1481619521','1');");
E_D("replace into `phome_ecms_news_index` values('97','5','1','1481534831','1481619521','1481619521','1');");
E_D("replace into `phome_ecms_news_index` values('98','5','1','1481534831','1481619521','1481619521','1');");
E_D("replace into `phome_ecms_news_index` values('99','5','1','1481534831','1481619521','1481619521','1');");
E_D("replace into `phome_ecms_news_index` values('100','5','1','1481534831','1481619521','1481619521','1');");
E_D("replace into `phome_ecms_news_index` values('101','5','1','1481534831','1481619521','1481619521','1');");
E_D("replace into `phome_ecms_news_index` values('102','5','1','1481534831','1481619521','1481619521','1');");

@include("../../inc/footer.php");
?>