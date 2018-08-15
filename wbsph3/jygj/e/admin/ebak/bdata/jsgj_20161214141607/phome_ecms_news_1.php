<?php
@include("../../inc/header.php");

/*
		SoftName : EmpireBak
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

E_D("DROP TABLE IF EXISTS `phome_ecms_news`;");
E_C("CREATE TABLE `phome_ecms_news` (
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
  PRIMARY KEY (`id`),
  KEY `classid` (`classid`),
  KEY `newstime` (`newstime`),
  KEY `ttid` (`ttid`),
  KEY `firsttitle` (`firsttitle`),
  KEY `isgood` (`isgood`),
  KEY `ispic` (`ispic`),
  KEY `useridis` (`userid`,`ismember`)
) ENGINE=MyISAM AUTO_INCREMENT=118 DEFAULT CHARSET=utf8");
E_D("replace into `phome_ecms_news` values('105','1','0','0','0','0','2016-12-14','105','1','admin','0','0','1','0','0','0','0','1481695423','1481695423','1','0','0','','/jsgj/zizhirongyu/2016-12-14/105.html','1','1','1','','ICP网站征信证书','1481534831','/jsgj/d/file/zizhirongyu/2016-12-14/ad858ee256141842c7cf3fd14138a4f9.jpg','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('104','1','0','0','0','0','2016-12-14','104','1','admin','0','0','1','0','0','0','0','1481695423','1481695423','1','0','0','','/jsgj/zizhirongyu/2016-12-14/104.html','1','1','1','','ICP网站征信证书IICP网站征信证书ICP网站征','1481534831','/jsgj/d/file/zizhirongyu/2016-12-14/5b6b7f1da55b2e2d8754ab63caa4fb9a.gif','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('117','1','0','0','0','0','2016-12-14','117','1','admin','0','0','1','0','0','0','0','1481695439','1481695439','1','0','0','','/jsgj/zizhirongyu/2016-12-14/117.html','1','1','1','','ICP网站征信证书','1481534831','/jsgj/d/file/zizhirongyu/2016-12-14/ad858ee256141842c7cf3fd14138a4f9.jpg','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('116','1','0','0','0','0','2016-12-14','116','1','admin','0','0','1','0','0','0','0','1481695439','1481695439','1','0','0','','/jsgj/zizhirongyu/2016-12-14/116.html','1','1','1','','ICP网站征信证书IICP网站征信证书ICP网站征','1481534831','/jsgj/d/file/zizhirongyu/2016-12-14/5b6b7f1da55b2e2d8754ab63caa4fb9a.gif','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('115','1','0','0','0','0','2016-12-14','115','1','admin','0','0','1','0','0','0','0','1481695439','1481695439','1','0','0','','/jsgj/zizhirongyu/2016-12-14/115.html','1','1','1','','ICP网站征信证书','1481534831','/jsgj/d/file/zizhirongyu/2016-12-14/ad858ee256141842c7cf3fd14138a4f9.jpg','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('114','1','0','0','0','0','2016-12-14','114','1','admin','0','0','1','0','0','0','0','1481695439','1481695439','1','0','0','','/jsgj/zizhirongyu/2016-12-14/114.html','1','1','1','','ICP网站征信证书IICP网站征信证书ICP网站征','1481534831','/jsgj/d/file/zizhirongyu/2016-12-14/5b6b7f1da55b2e2d8754ab63caa4fb9a.gif','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('113','1','0','0','0','0','2016-12-14','113','1','admin','0','0','1','0','0','0','0','1481695439','1481695439','1','0','0','','/jsgj/zizhirongyu/2016-12-14/113.html','1','1','1','','ICP网站征信证书','1481534831','/jsgj/d/file/zizhirongyu/2016-12-14/ad858ee256141842c7cf3fd14138a4f9.jpg','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('112','1','0','0','0','0','2016-12-14','112','1','admin','0','0','1','0','0','0','0','1481695439','1481695439','1','0','0','','/jsgj/zizhirongyu/2016-12-14/112.html','1','1','1','','ICP网站征信证书IICP网站征信证书ICP网站征','1481534831','/jsgj/d/file/zizhirongyu/2016-12-14/5b6b7f1da55b2e2d8754ab63caa4fb9a.gif','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('111','1','0','0','0','0','2016-12-14','111','1','admin','0','0','1','0','0','0','0','1481695439','1481695439','1','0','0','','/jsgj/zizhirongyu/2016-12-14/111.html','1','1','1','','ICP网站征信证书','1481534831','/jsgj/d/file/zizhirongyu/2016-12-14/ad858ee256141842c7cf3fd14138a4f9.jpg','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('110','1','0','0','0','0','2016-12-14','110','1','admin','0','0','1','0','0','0','0','1481695439','1481695439','1','0','0','','/jsgj/zizhirongyu/2016-12-14/110.html','1','1','1','','ICP网站征信证书IICP网站征信证书ICP网站征','1481534831','/jsgj/d/file/zizhirongyu/2016-12-14/5b6b7f1da55b2e2d8754ab63caa4fb9a.gif','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('109','1','0','0','0','0','2016-12-14','109','1','admin','0','0','1','0','0','0','0','1481695432','1481695432','1','0','0','','/jsgj/zizhirongyu/2016-12-14/109.html','1','1','1','','ICP网站征信证书','1481534831','/jsgj/d/file/zizhirongyu/2016-12-14/ad858ee256141842c7cf3fd14138a4f9.jpg','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('108','1','0','0','0','0','2016-12-14','108','1','admin','0','0','1','0','0','0','0','1481695432','1481695432','1','0','0','','/jsgj/zizhirongyu/2016-12-14/108.html','1','1','1','','ICP网站征信证书IICP网站征信证书ICP网站征','1481534831','/jsgj/d/file/zizhirongyu/2016-12-14/5b6b7f1da55b2e2d8754ab63caa4fb9a.gif','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('107','1','0','0','0','0','2016-12-14','107','1','admin','0','0','1','0','0','0','0','1481695432','1481695432','1','0','0','','/jsgj/zizhirongyu/2016-12-14/107.html','1','1','1','','ICP网站征信证书','1481534831','/jsgj/d/file/zizhirongyu/2016-12-14/ad858ee256141842c7cf3fd14138a4f9.jpg','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('106','1','0','0','0','0','2016-12-14','106','1','admin','0','0','1','0','0','0','0','1481695432','1481695432','1','0','0','','/jsgj/zizhirongyu/2016-12-14/106.html','1','1','1','','ICP网站征信证书IICP网站征信证书ICP网站征','1481534831','/jsgj/d/file/zizhirongyu/2016-12-14/5b6b7f1da55b2e2d8754ab63caa4fb9a.gif','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('31','1','0','0','0','0','2016-12-12','31','1','admin','0','0','1','0','0','0','0','1481535193','1481686793','1','0','0','','/jsgj/zizhirongyu/2016-12-12/31.html','1','1','1','','ICP网站征信证书IICP网站征信证书ICP网站征','1481534831','/jsgj/d/file/zizhirongyu/2016-12-14/5b6b7f1da55b2e2d8754ab63caa4fb9a.gif','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('32','1','0','0','0','0','2016-12-12','32','1','admin','0','0','1','0','0','0','0','1481535193','1481684367','1','0','0','','/jsgj/zizhirongyu/2016-12-12/32.html','1','1','1','','ICP网站征信证书','1481534831','/jsgj/d/file/zizhirongyu/2016-12-14/ad858ee256141842c7cf3fd14138a4f9.jpg','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('75','5','0','0','0','0','2016-12-13','75','1','admin','0','0','1','0','0','0','0','1481619521','1481619521','1','0','0','','/jsgj/bangonghuanjing/2016-12-13/75.html','1','1','1','','ICP网站征信证书','1481534831','/jsgj/d/file/zizhirongyu/2016-12-12/d11f4e25618e23693335ee7a3e7538e2.jpg','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('76','5','0','0','0','0','2016-12-13','76','1','admin','0','0','1','0','0','0','0','1481619521','1481619521','1','0','0','','/jsgj/bangonghuanjing/2016-12-13/76.html','1','1','1','','ICP网站征信证书','1481534831','/jsgj/d/file/zizhirongyu/2016-12-12/d11f4e25618e23693335ee7a3e7538e2.jpg','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('77','5','0','0','0','0','2016-12-13','77','1','admin','0','0','1','0','0','0','0','1481619521','1481619521','1','0','0','','/jsgj/bangonghuanjing/2016-12-13/77.html','1','1','1','','ICP网站征信证书','1481534831','/jsgj/d/file/zizhirongyu/2016-12-12/d11f4e25618e23693335ee7a3e7538e2.jpg','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('78','5','0','0','0','0','2016-12-13','78','1','admin','0','0','1','0','0','0','0','1481619521','1481619521','1','0','0','','/jsgj/bangonghuanjing/2016-12-13/78.html','1','1','1','','ICP网站征信证书','1481534831','/jsgj/d/file/zizhirongyu/2016-12-12/d11f4e25618e23693335ee7a3e7538e2.jpg','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('37','7','0','0','0','0','2016-12-13','37','1','admin','0','0','1','0','0','0','0','1481595517','1481595517','1','0','0','','/jsgj/cishangongyi/2016-12-13/37.html','1','1','1','','ICP网站征信证书','1481534831','/jsgj/d/file/zizhirongyu/2016-12-12/d11f4e25618e23693335ee7a3e7538e2.jpg','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('38','7','0','0','0','0','2016-12-13','38','1','admin','0','0','1','0','0','0','0','1481595517','1481595517','1','0','0','','/jsgj/cishangongyi/2016-12-13/38.html','1','1','1','','ICP网站征信证书','1481534831','/jsgj/d/file/zizhirongyu/2016-12-12/d11f4e25618e23693335ee7a3e7538e2.jpg','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('73','10','0','0','0','0','2016-12-13','73','1','admin','0','0','0','0','0','0','0','1481601878','1481601878','1','0','0','','/jsgj/yinsishengming/2016-12-13/73.html','1','1','1','','隐私声明条款','1481601856','','','一、我们为什么需要收集、使用您的信息    我们收集您的信息是为了在遵守国家法律法规的规定的前提下，向您提供更优质的服务，提高用户体验，保证网络安全及改进安利网站内容，为了','0');");
E_D("replace into `phome_ecms_news` values('79','5','0','0','0','0','2016-12-13','79','1','admin','0','0','1','0','0','0','0','1481619521','1481619521','1','0','0','','/jsgj/bangonghuanjing/2016-12-13/79.html','1','1','1','','ICP网站征信证书','1481534831','/jsgj/d/file/zizhirongyu/2016-12-12/d11f4e25618e23693335ee7a3e7538e2.jpg','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('80','5','0','0','0','0','2016-12-13','80','1','admin','0','0','1','0','0','0','0','1481619521','1481619521','1','0','0','','/jsgj/bangonghuanjing/2016-12-13/80.html','1','1','1','','ICP网站征信证书','1481534831','/jsgj/d/file/zizhirongyu/2016-12-12/d11f4e25618e23693335ee7a3e7538e2.jpg','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('81','5','0','0','0','0','2016-12-13','81','1','admin','0','0','1','0','0','0','0','1481619521','1481619521','1','0','0','','/jsgj/bangonghuanjing/2016-12-13/81.html','1','1','1','','ICP网站征信证书','1481534831','/jsgj/d/file/zizhirongyu/2016-12-12/d11f4e25618e23693335ee7a3e7538e2.jpg','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('82','5','0','0','0','0','2016-12-13','82','1','admin','0','0','1','0','0','0','0','1481619521','1481619521','1','0','0','','/jsgj/bangonghuanjing/2016-12-13/82.html','1','1','1','','ICP网站征信证书','1481534831','/jsgj/d/file/zizhirongyu/2016-12-12/d11f4e25618e23693335ee7a3e7538e2.jpg','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('83','5','0','0','0','0','2016-12-13','83','1','admin','0','0','1','0','0','0','0','1481619521','1481619521','1','0','0','','/jsgj/bangonghuanjing/2016-12-13/83.html','1','1','1','','ICP网站征信证书','1481534831','/jsgj/d/file/zizhirongyu/2016-12-12/d11f4e25618e23693335ee7a3e7538e2.jpg','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('84','5','0','0','0','0','2016-12-13','84','1','admin','0','0','1','0','0','0','0','1481619521','1481619521','1','0','0','','/jsgj/bangonghuanjing/2016-12-13/84.html','1','1','1','','ICP网站征信证书','1481534831','/jsgj/d/file/zizhirongyu/2016-12-12/d11f4e25618e23693335ee7a3e7538e2.jpg','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('85','5','0','0','0','0','2016-12-13','85','1','admin','0','0','1','0','0','0','0','1481619521','1481619521','1','0','0','','/jsgj/bangonghuanjing/2016-12-13/85.html','1','1','1','','ICP网站征信证书','1481534831','/jsgj/d/file/zizhirongyu/2016-12-12/d11f4e25618e23693335ee7a3e7538e2.jpg','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('86','5','0','0','0','0','2016-12-13','86','1','admin','0','0','1','0','0','0','0','1481619521','1481619521','1','0','0','','/jsgj/bangonghuanjing/2016-12-13/86.html','1','1','1','','ICP网站征信证书','1481534831','/jsgj/d/file/zizhirongyu/2016-12-12/d11f4e25618e23693335ee7a3e7538e2.jpg','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('87','5','0','0','0','0','2016-12-13','87','1','admin','0','0','1','0','0','0','0','1481619521','1481619521','1','0','0','','/jsgj/bangonghuanjing/2016-12-13/87.html','1','1','1','','ICP网站征信证书','1481534831','/jsgj/d/file/zizhirongyu/2016-12-12/d11f4e25618e23693335ee7a3e7538e2.jpg','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('88','5','0','0','0','0','2016-12-13','88','1','admin','0','0','1','0','0','0','0','1481619521','1481619521','1','0','0','','/jsgj/bangonghuanjing/2016-12-13/88.html','1','1','1','','ICP网站征信证书','1481534831','/jsgj/d/file/zizhirongyu/2016-12-12/d11f4e25618e23693335ee7a3e7538e2.jpg','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('89','5','0','0','0','0','2016-12-13','89','1','admin','0','0','1','0','0','0','0','1481619521','1481619521','1','0','0','','/jsgj/bangonghuanjing/2016-12-13/89.html','1','1','1','','ICP网站征信证书','1481534831','/jsgj/d/file/zizhirongyu/2016-12-12/d11f4e25618e23693335ee7a3e7538e2.jpg','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('90','5','0','0','0','0','2016-12-13','90','1','admin','0','0','1','0','0','0','0','1481619521','1481619521','1','0','0','','/jsgj/bangonghuanjing/2016-12-13/90.html','1','1','1','','ICP网站征信证书','1481534831','/jsgj/d/file/zizhirongyu/2016-12-12/d11f4e25618e23693335ee7a3e7538e2.jpg','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('91','5','0','0','0','0','2016-12-13','91','1','admin','0','0','1','0','0','0','0','1481619521','1481619521','1','0','0','','/jsgj/bangonghuanjing/2016-12-13/91.html','1','1','1','','ICP网站征信证书','1481534831','/jsgj/d/file/zizhirongyu/2016-12-12/d11f4e25618e23693335ee7a3e7538e2.jpg','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('92','5','0','0','0','0','2016-12-13','92','1','admin','0','0','1','0','0','0','0','1481619521','1481619521','1','0','0','','/jsgj/bangonghuanjing/2016-12-13/92.html','1','1','1','','ICP网站征信证书','1481534831','/jsgj/d/file/zizhirongyu/2016-12-12/d11f4e25618e23693335ee7a3e7538e2.jpg','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('93','5','0','0','0','0','2016-12-13','93','1','admin','0','0','1','0','0','0','0','1481619521','1481619521','1','0','0','','/jsgj/bangonghuanjing/2016-12-13/93.html','1','1','1','','ICP网站征信证书','1481534831','/jsgj/d/file/zizhirongyu/2016-12-12/d11f4e25618e23693335ee7a3e7538e2.jpg','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('94','5','0','0','0','0','2016-12-13','94','1','admin','0','0','1','0','0','0','0','1481619521','1481619521','1','0','0','','/jsgj/bangonghuanjing/2016-12-13/94.html','1','1','1','','ICP网站征信证书','1481534831','/jsgj/d/file/zizhirongyu/2016-12-12/d11f4e25618e23693335ee7a3e7538e2.jpg','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('95','5','0','0','0','0','2016-12-13','95','1','admin','0','0','1','0','0','0','0','1481619521','1481619521','1','0','0','','/jsgj/bangonghuanjing/2016-12-13/95.html','1','1','1','','ICP网站征信证书','1481534831','/jsgj/d/file/zizhirongyu/2016-12-12/d11f4e25618e23693335ee7a3e7538e2.jpg','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('96','5','0','0','0','0','2016-12-13','96','1','admin','0','0','1','0','0','0','0','1481619521','1481619521','1','0','0','','/jsgj/bangonghuanjing/2016-12-13/96.html','1','1','1','','ICP网站征信证书','1481534831','/jsgj/d/file/zizhirongyu/2016-12-12/d11f4e25618e23693335ee7a3e7538e2.jpg','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('97','5','0','0','0','0','2016-12-13','97','1','admin','0','0','1','0','0','0','0','1481619521','1481619521','1','0','0','','/jsgj/bangonghuanjing/2016-12-13/97.html','1','1','1','','ICP网站征信证书','1481534831','/jsgj/d/file/zizhirongyu/2016-12-12/d11f4e25618e23693335ee7a3e7538e2.jpg','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('98','5','0','0','0','0','2016-12-13','98','1','admin','0','0','1','0','0','0','0','1481619521','1481619521','1','0','0','','/jsgj/bangonghuanjing/2016-12-13/98.html','1','1','1','','ICP网站征信证书','1481534831','/jsgj/d/file/zizhirongyu/2016-12-12/d11f4e25618e23693335ee7a3e7538e2.jpg','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('99','5','0','0','0','0','2016-12-13','99','1','admin','0','0','1','0','0','0','0','1481619521','1481619521','1','0','0','','/jsgj/bangonghuanjing/2016-12-13/99.html','1','1','1','','ICP网站征信证书','1481534831','/jsgj/d/file/zizhirongyu/2016-12-12/d11f4e25618e23693335ee7a3e7538e2.jpg','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('100','5','0','0','0','0','2016-12-13','100','1','admin','0','0','1','0','0','0','0','1481619521','1481619521','1','0','0','','/jsgj/bangonghuanjing/2016-12-13/100.html','1','1','1','','ICP网站征信证书','1481534831','/jsgj/d/file/zizhirongyu/2016-12-12/d11f4e25618e23693335ee7a3e7538e2.jpg','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('101','5','0','0','0','0','2016-12-13','101','1','admin','0','0','1','0','0','0','0','1481619521','1481619521','1','0','0','','/jsgj/bangonghuanjing/2016-12-13/101.html','1','1','1','','ICP网站征信证书','1481534831','/jsgj/d/file/zizhirongyu/2016-12-12/d11f4e25618e23693335ee7a3e7538e2.jpg','','ICP网站征信证书','0');");
E_D("replace into `phome_ecms_news` values('102','5','0','0','0','0','2016-12-13','102','1','admin','0','0','1','0','0','0','0','1481619521','1481619521','1','0','0','','/jsgj/bangonghuanjing/2016-12-13/102.html','1','1','1','','ICP网站征信证书','1481534831','/jsgj/d/file/zizhirongyu/2016-12-12/d11f4e25618e23693335ee7a3e7538e2.jpg','','ICP网站征信证书','0');");

@include("../../inc/footer.php");
?>