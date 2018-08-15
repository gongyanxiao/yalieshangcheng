<?php
@include("../../inc/header.php");

/*
		SoftName : EmpireBak
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

E_D("DROP TABLE IF EXISTS `phome_ecms_video_data_1`;");
E_C("CREATE TABLE `phome_ecms_video_data_1` (
  `id` int(10) unsigned NOT NULL DEFAULT '0',
  `classid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `keyid` varchar(255) NOT NULL DEFAULT '',
  `dokey` tinyint(1) NOT NULL DEFAULT '0',
  `newstempid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `closepl` tinyint(1) NOT NULL DEFAULT '0',
  `haveaddfen` tinyint(1) NOT NULL DEFAULT '0',
  `infotags` varchar(80) NOT NULL DEFAULT '',
  `writer` varchar(30) NOT NULL DEFAULT '',
  `befrom` varchar(60) NOT NULL DEFAULT '',
  `newstext` mediumtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `classid` (`classid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");
E_D("replace into `phome_ecms_video_data_1` values('105','8','','1','0','0','0','','','','<p><iframe src=\\\\\"http://share.vrs.sohu.com/my/v.swf&amp;topBar=1&amp;id=86102788&amp;autoplay=false&amp;xuid=86e60c5b1e1a4e0x&amp;from=page\\\\\" width=\\\\\"704\\\\\" height=\\\\\"469\\\\\"></iframe></p>');");
E_D("replace into `phome_ecms_video_data_1` values('106','8','','1','0','0','0','','','','<p><iframe src=\\\\\"http://player.youku.com/player.php/Type/Folder/Fid//Ob//sid/XMTc3MjgyMTYyOA==/v.swf\\\\\" width=\\\\\"704\\\\\" height=\\\\\"469\\\\\"></iframe></p>');");
E_D("replace into `phome_ecms_video_data_1` values('107','8','','1','0','0','0','','','','<p><iframe src=\"http://share.vrs.sohu.com/my/v.swf&amp;topBar=1&amp;id=86102788&amp;autoplay=false&amp;xuid=86e60c5b1e1a4e0x&amp;from=page\" width=\"704\" height=\"469\"></iframe></p>');");
E_D("replace into `phome_ecms_video_data_1` values('108','8','','1','0','0','0','','','','<p><iframe src=\"http://player.youku.com/player.php/Type/Folder/Fid//Ob//sid/XMTc3MjgyMTYyOA==/v.swf\" width=\"704\" height=\"469\"></iframe></p>');");
E_D("replace into `phome_ecms_video_data_1` values('109','8','','1','0','0','0','','','','<p><iframe src=\"http://share.vrs.sohu.com/my/v.swf&amp;topBar=1&amp;id=86102788&amp;autoplay=false&amp;xuid=86e60c5b1e1a4e0x&amp;from=page\" width=\"704\" height=\"469\"></iframe></p>');");
E_D("replace into `phome_ecms_video_data_1` values('110','8','','1','0','0','0','','','','<p><iframe src=\"http://player.youku.com/player.php/Type/Folder/Fid//Ob//sid/XMTc3MjgyMTYyOA==/v.swf\" width=\"704\" height=\"469\"></iframe></p>');");
E_D("replace into `phome_ecms_video_data_1` values('111','8','','1','0','0','0','','','','<p><iframe src=\"http://share.vrs.sohu.com/my/v.swf&amp;topBar=1&amp;id=86102788&amp;autoplay=false&amp;xuid=86e60c5b1e1a4e0x&amp;from=page\" width=\"704\" height=\"469\"></iframe></p>');");
E_D("replace into `phome_ecms_video_data_1` values('112','8','','1','0','0','0','','','','<p><iframe src=\"http://player.youku.com/player.php/Type/Folder/Fid//Ob//sid/XMTc3MjgyMTYyOA==/v.swf\" width=\"704\" height=\"469\"></iframe></p>');");

@include("../../inc/footer.php");
?>