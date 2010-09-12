SET FOREIGN_KEY_CHECKS=0;

drop database facekeeper;

CREATE DATABASE facekeeper CHARACTER SET utf8 COLLATE utf8_general_ci;

use facekeeper;

set names 'utf8';

-- ----------------------------
-- Table structure for `administrators`
-- ----------------------------
DROP TABLE IF EXISTS `administrators`;
CREATE TABLE `administrators` (
  `id` int(14) NOT NULL AUTO_INCREMENT COMMENT '管理員編號',
  `username` varchar(32) NOT NULL COMMENT '管理員帳號',
  `password` varchar(40) NOT NULL COMMENT '管理員密碼',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of administrators
-- ----------------------------
INSERT INTO `administrators` VALUES ('1', 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997');

-- ----------------------------
-- Table structure for `administrator_logs`
-- ----------------------------
DROP TABLE IF EXISTS `administrator_logs`;
CREATE TABLE `administrator_logs` (
  `id` int(14) NOT NULL AUTO_INCREMENT,
  `user` varchar(14) NOT NULL,
  `message` text,
  `time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of administrator_logs
-- ----------------------------

-- ----------------------------
-- Table structure for `configs`
-- ----------------------------
DROP TABLE IF EXISTS `configs`;
CREATE TABLE `configs` (
  `name` varchar(64) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of configs
-- ----------------------------
INSERT INTO `configs` VALUES ('source.google.enable', '1');
INSERT INTO `configs` VALUES ('source.yahoo.enable', '1');
INSERT INTO `configs` VALUES ('source.bing.enable', '1');
INSERT INTO `configs` VALUES ('source.youtube.enable', '1');
INSERT INTO `configs` VALUES ('report.enable', '1');
INSERT INTO `configs` VALUES ('report.interval', '7');
INSERT INTO `configs` VALUES ('report.receiver', 'test@t1est.com');
INSERT INTO `configs` VALUES ('report.format', 'TXT');
INSERT INTO `configs` VALUES ('fetch.interval', '12');
INSERT INTO `configs` VALUES ('fetch.depth', '1');
INSERT INTO `configs` VALUES ('backend.showtooltip', '1');
INSERT INTO `configs` VALUES ('fetch.delete_enable', '0');
INSERT INTO `configs` VALUES ('fetch.keyword_combination', '1');
INSERT INTO `configs` VALUES ('source.facebook.enable', '1');
INSERT INTO `configs` VALUES ('source.ptt.enable', '1');
INSERT INTO `configs` VALUES ('pager.itemperpage', '10');
INSERT INTO `configs` VALUES ('fb.username', '');
INSERT INTO `configs` VALUES ('fb.password', '');
INSERT INTO `configs` VALUES ('fb.enable', '1');

-- ----------------------------
-- Table structure for `cron_running_logs`
-- ----------------------------
DROP TABLE IF EXISTS `cron_running_logs`;
CREATE TABLE `cron_running_logs` (
  `id` int(14) NOT NULL AUTO_INCREMENT COMMENT '流水編號',
  `start_time` datetime NOT NULL,
  `type` varchar(10) DEFAULT 'WEBGRAB',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cron_running_logs
-- ----------------------------

-- ----------------------------
-- Table structure for `fb_directories`
-- ----------------------------
DROP TABLE IF EXISTS `fb_directories`;
CREATE TABLE `fb_directories` (
  `id` int(14) NOT NULL AUTO_INCREMENT,
  `url` text NOT NULL,
  `title` text NOT NULL,
  `type` int(1) NOT NULL DEFAULT '0',
  `tracking` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fb_directories
-- ----------------------------

-- ----------------------------
-- Table structure for `keywords`
-- ----------------------------
DROP TABLE IF EXISTS `keywords`;
CREATE TABLE `keywords` (
  `id` int(14) NOT NULL AUTO_INCREMENT,
  `keyword` text NOT NULL,
  `type` int(1) NOT NULL DEFAULT '1',
  `creator` int(14) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=144 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of keywords
-- ----------------------------
INSERT INTO `keywords` VALUES ('112', '武裝兵', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('117', '裝無辜', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('116', '裝不懂', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('110', '下士', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('108', '上士', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('106', '中尉', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('75', '凉兵', '4', '1', '0000-00-00 00:00:00');
INSERT INTO `keywords` VALUES ('74', '爽兵', '4', '1', '0000-00-00 00:00:00');
INSERT INTO `keywords` VALUES ('104', '少校', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('102', '上校', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('100', '中將', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('97', '外島', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('95', '南引', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('94', '東引', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('93', '馬祖', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('91', '學弟', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('86', '女兵', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('84', '涼兵', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('82', '大頭兵', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('66', '爽兵', '3', '1', '0000-00-00 00:00:00');
INSERT INTO `keywords` VALUES ('67', '兵', '3', '1', '0000-00-00 00:00:00');
INSERT INTO `keywords` VALUES ('68', '兵', '4', '1', '0000-00-00 00:00:00');
INSERT INTO `keywords` VALUES ('69', '凉兵', '3', '1', '0000-00-00 00:00:00');
INSERT INTO `keywords` VALUES ('70', '當兵好凉', '4', '1', '0000-00-00 00:00:00');
INSERT INTO `keywords` VALUES ('71', '當兵好操', '4', '1', '0000-00-00 00:00:00');
INSERT INTO `keywords` VALUES ('72', '當兵好爽', '4', '1', '0000-00-00 00:00:00');
INSERT INTO `keywords` VALUES ('73', '兵', '2', '1', '0000-00-00 00:00:00');
INSERT INTO `keywords` VALUES ('119', '宿舍', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('118', '替代役', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('113', '裝天', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('111', '天兵', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('115', '裝白痴', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('109', '中士', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('107', '少尉', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('105', '上尉', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('103', '中校', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('101', '少將', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('99', '上將', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('98', '長官', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('96', '官田', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('92', '金門', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('90', '學長', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('89', '懷孕', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('88', '輔導長', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('87', '上空', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('85', '憲兵', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('83', '爽兵', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('81', '當兵好涼', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('80', '﻿當兵好爽', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('120', '兵,爽,涼', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('121', '兵,操', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('122', '宿舍,酒', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('123', '兵,酒', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('124', '女兵,上空,乳溝', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('125', '女兵,接吻', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('126', '宿舍,女兵', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('127', '宿舍,兵', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('128', '女兵,長官', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('129', '兵,裝天', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('131', '兵,裝白癡', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('132', '兵,裝不懂', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('133', '兵,裝無辜', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('134', '天兵,學弟', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('135', '天,學弟', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('136', '兵,學弟', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('137', '學長學弟制', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('138', '兵,偷懶', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('139', '兵,納涼', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('140', '兵,爽', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('141', '兵,涼', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('142', '外島,爽', '1', '1', '2010-08-13 18:38:21');
INSERT INTO `keywords` VALUES ('143', '外島,涼', '1', '1', '2010-08-13 18:38:21');

-- ----------------------------
-- Table structure for `logs`
-- ----------------------------
DROP TABLE IF EXISTS `logs`;
CREATE TABLE `logs` (
  `id` int(14) NOT NULL AUTO_INCREMENT,
  `daemon` varchar(14) NOT NULL,
  `message` text,
  `time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of logs
-- ----------------------------

-- ----------------------------
-- Table structure for `reports`
-- ----------------------------
DROP TABLE IF EXISTS `reports`;
CREATE TABLE `reports` (
  `id` int(14) NOT NULL AUTO_INCREMENT,
  `filename` text NOT NULL,
  `filesize` varchar(10) NOT NULL,
  `time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of reports
-- ----------------------------

-- ----------------------------
-- Table structure for `result_pool`
-- ----------------------------
DROP TABLE IF EXISTS `result_pool`;
CREATE TABLE `result_pool` (
  `id` int(14) NOT NULL AUTO_INCREMENT COMMENT '流水編號',
  `pid` int(14) NOT NULL DEFAULT '1',
  `hash` varchar(40) NOT NULL,
  `source` int(1) NOT NULL DEFAULT '0',
  `url` text NOT NULL COMMENT '網址',
  `title` text,
  `time` datetime NOT NULL COMMENT '記錄時間',
  `keywords` text,
  `keyword_length` int(14) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of result_pool
-- ----------------------------

-- ----------------------------
-- Table structure for `youtube_pool`
-- ----------------------------
DROP TABLE IF EXISTS `youtube_pool`;
CREATE TABLE `youtube_pool` (
  `id` int(14) NOT NULL AUTO_INCREMENT COMMENT '流水編號',
  `pid` int(14) NOT NULL DEFAULT '1',
  `hash` varchar(40) NOT NULL,
  `url` text NOT NULL COMMENT '網址',
  `title` text,
  `time` datetime NOT NULL COMMENT '記錄時間',
  `date` text,
  `views` varchar(14) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of youtube_pool
-- ----------------------------

-- ----------------------------
-- View structure for `fb_pool`
-- ----------------------------
DROP VIEW IF EXISTS `fb_pool`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `fb_pool` AS select `fb_directories`.`url` AS `url`,`fb_directories`.`id` AS `id`,`fb_directories`.`title` AS `title` from `fb_directories` where (`fb_directories`.`tracking` = 1);


-- ----------------------------
-- View structure for `result_counting`
-- ----------------------------
DROP VIEW IF EXISTS `result_counting`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `result_counting` AS select `result_pool`.`hash` AS `hash`,`result_pool`.`url` AS `url`,`result_pool`.`pid` AS `pid`,`result_pool`.`title` AS `title`,count(`result_pool`.`id`) AS `count` from `result_pool` where (`result_pool`.`source` = 0) group by `result_pool`.`hash`;

-- ----------------------------
-- View structure for `result_keyword`
-- ----------------------------
DROP VIEW IF EXISTS `result_keyword`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `result_keyword` AS select `result_pool`.`pid` AS `pid`,`result_pool`.`url` AS `url`,`result_pool`.`hash` AS `hash`,`result_pool`.`title` AS `title`,`result_pool`.`keywords` AS `keywords`,`result_pool`.`keyword_length` AS `keyword_length` from `result_pool` where (`result_pool`.`source` = 0) group by `result_pool`.`hash` order by `result_pool`.`keyword_length`;
