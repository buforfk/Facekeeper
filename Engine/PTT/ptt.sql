n SQL Dump
-- version 2.11.3deb1ubuntu1.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 30, 2010 at 07:44 PM
-- Server version: 5.0.51
-- PHP Version: 5.2.4-2ubuntu5.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `ptt`
--

-- --------------------------------------------------------

--
-- Table structure for table `boards`
--

CREATE TABLE IF NOT EXISTS `boards` (
  `board` varchar(20) NOT NULL,
  `id` int(14) NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `boards`
--

INSERT INTO `boards` (`board`, `id`) VALUES
('Gossiping', 1),
('GFonGuard', 2),
('AirForce', 3),
('Army-Sir', 4),
('Cga', 5),
('DIRDS', 6),
('GirlE_MiliW', 7),
('Militarylife', 8),
('MP', 9),
('Navy', 10),
('SMSlife', 11);

-- --------------------------------------------------------

--
-- Table structure for table `keywords`
--

CREATE TABLE IF NOT EXISTS `keywords` (
  `id` int(14) NOT NULL auto_increment,
  `keyword` text NOT NULL,
  `type` int(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=66 ;

--
-- Dumping data for table `keywords`
--

INSERT INTO `keywords` (`id`, `keyword`, `type`) VALUES
(1, '﻿當兵好爽', 1),
(2, '當兵好涼', 1),
(3, '大頭兵', 1),
(4, '爽兵', 1),
(5, '涼兵', 1),
(6, '憲兵', 1),
(7, '女兵', 1),
(8, '上空', 1),
(9, '輔導長', 1),
(10, '懷孕', 1),
(11, '學長', 1),
(12, '學弟', 1),
(13, '金門', 1),
(14, '馬祖', 1),
(15, '東引', 1),
(16, '南引', 1),
(17, '官田', 1),
(18, '外島', 1),
(19, '長官', 1),
(20, '上將', 1),
(21, '中將', 1),
(22, '少將', 1),
(23, '上校', 1),
(24, '中校', 1),
(25, '少校', 1),
(26, '上尉', 1),
(27, '中尉', 1),
(28, '少尉', 1),
(29, '上士', 1),
(30, '中士', 1),
(31, '下士', 1),
(32, '天兵', 1),
(33, '武裝兵', 1),
(34, '裝天', 1),
(35, '裝笨', 1),
(36, '裝白痴', 1),
(37, '裝不懂', 1),
(38, '裝無辜', 1),
(39, '替代役', 1),
(40, '宿舍', 1),
(41, '兵,爽,涼', 1),
(42, '兵,操', 1),
(43, '宿舍,酒', 1),
(44, '兵,酒', 1),
(45, '女兵,上空,乳溝', 1),
(46, '女兵,接吻', 1),
(47, '宿舍,女兵', 1),
(48, '宿舍,兵', 1),
(49, '女兵,長官', 1),
(50, '兵,裝天', 1),
(51, '兵,裝笨', 1),
(52, '兵,裝白癡', 1),
(53, '兵,裝不懂', 1),
(54, '兵,裝無辜', 1),
(55, '天兵,學弟', 1),
(56, '天,學弟', 1),
(57, '兵,學弟', 1),
(58, '學長學弟制', 1),
(59, '兵,偷懶', 1),
(60, '兵,納涼', 1),
(61, '兵,爽', 1),
(62, '兵,涼', 1),
(63, '外島,爽', 1),
(64, '外島,涼', 1),
(65, '蘇打綠', 2);

-- --------------------------------------------------------

--
-- Table structure for table `result_pool`
--

CREATE TABLE IF NOT EXISTS `result_pool` (
  `id` int(14) NOT NULL auto_increment COMMENT '流水編號',
  `pid` int(14) NOT NULL default '1',
  `hash` varchar(40) NOT NULL,
  `url` text NOT NULL COMMENT '網址',
  `title` text,
  `time` datetime NOT NULL COMMENT '記錄時間',
  `keywords` text,
  `keyword_length` int(14) NOT NULL default '0',
  `board` varchar(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `result_pool`
--


