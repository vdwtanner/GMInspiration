
-- phpMyAdmin SQL Dump
-- version 2.11.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 02, 2015 at 05:10 PM
-- Server version: 5.1.57
-- PHP Version: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `a9044814_dungeon`
--

-- --------------------------------------------------------

--
-- Table structure for table `contributions`
--
-- Creation: Feb 01, 2015 at 07:21 PM
-- Last update: Feb 02, 2015 at 04:09 PM
--

CREATE TABLE `contributions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `name` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `type` varchar(75) COLLATE latin1_general_ci NOT NULL,
  `wtype` varchar(75) COLLATE latin1_general_ci DEFAULT NULL COMMENT 'Weapon Type',
  `game` varchar(60) COLLATE latin1_general_ci NOT NULL COMMENT 'game and version info',
  `desc` text COLLATE latin1_general_ci NOT NULL COMMENT 'description',
  `img` varchar(255) COLLATE latin1_general_ci DEFAULT NULL COMMENT 'path to file or URL',
  `json` text COLLATE latin1_general_ci NOT NULL COMMENT 'All extra fields',
  `uses` int(11) DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'creation date',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `contributions`
--

INSERT INTO `contributions` VALUES(1, 'tehcoconut', 'The Sword of 1000 Truths', 'Weapon', '', 'Dungeons and Dragons: 5th edition', 'This sword appears to be a normal flashdrive, but when uploaded to a certain copyrighted game....', NULL, '{"how":"Be a faggot","effect":"Faggotry","attack":"Blah","label_1":"Attack\\/Damage","text_1":"+20 to hit, range 5ft, Damage 6d12+23 slashing"}', 0, '2015-02-01 20:48:17');
INSERT INTO `contributions` VALUES(2, 'vdwtanner', 'test', 'Weapon', '', 'Dungeons and Dragons: 5th edition', 'sdfg', NULL, '{"how":"efw","effect":"wef","attack":"2d6","label_1":"","text_1":""}', 0, '2015-02-02 15:33:39');
INSERT INTO `contributions` VALUES(3, 'vdwtanner', 'Bloodborne', 'Weapon', 'disease', 'Dungeons and Dragons: 5th edition', 'Kill all the things with all the diseases', NULL, '{"how":"Walk up and spit on someone","effect":"makes eneies dead","attack":"12d20","label_1":"Freaking awesome label","text_1":"This does all the damages","label_2":"This label is cooler than you","text_2":"This text is even better"}', 0, '2015-02-02 16:09:00');
INSERT INTO `contributions` VALUES(4, 'vdwtanner', 'Bloodborne', 'Weapon', 'disease', 'Dungeons and Dragons: 5th edition', 'Kill all the things with all the diseases', NULL, '{"how":"Walk up and spit on someone","effect":"makes eneies dead","attack":"12d20","Freaking awesome label":"This does all the damages","This label is cooler than you":"This text is even better"}', 0, '2015-02-02 16:09:56');

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--
-- Creation: Jan 23, 2015 at 04:04 AM
-- Last update: Jan 23, 2015 at 04:04 AM
--

CREATE TABLE `friends` (
  `username` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `friends` text COLLATE latin1_general_ci NOT NULL COMMENT 'CSV',
  PRIMARY KEY (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `friends`
--


-- --------------------------------------------------------

--
-- Table structure for table `friend_request`
--
-- Creation: Jan 23, 2015 at 10:19 PM
-- Last update: Jan 23, 2015 at 10:19 PM
--

CREATE TABLE `friend_request` (
  `sender` varchar(20) COLLATE latin1_general_ci NOT NULL COMMENT 'sender username',
  `username` varchar(20) COLLATE latin1_general_ci DEFAULT NULL COMMENT 'recipient username',
  `email` varchar(60) COLLATE latin1_general_ci DEFAULT NULL COMMENT 'recipient email',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'time sent',
  PRIMARY KEY (`sender`),
  UNIQUE KEY `username` (`username`,`email`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci COMMENT='timestamp used to determine if request should be deleted. 7 ';

--
-- Dumping data for table `friend_request`
--


-- --------------------------------------------------------

--
-- Table structure for table `users`
--
-- Creation: Jan 30, 2015 at 01:05 AM
-- Last update: Feb 01, 2015 at 08:44 PM
--

CREATE TABLE `users` (
  `username` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `pass` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `email` varchar(60) COLLATE latin1_general_ci NOT NULL,
  `description` text COLLATE latin1_general_ci COMMENT 'user can set their description',
  `picture` text COLLATE latin1_general_ci COMMENT 'dataURL for profile picture',
  `contributions` int(11) NOT NULL DEFAULT '0' COMMENT 'contributions made by user',
  `joined` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When this user joined',
  `hash` varchar(32) COLLATE latin1_general_ci NOT NULL COMMENT 'hashcode for verification',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` VALUES('vdwtanner', '824796736ed18484d2bbeae55d6a3861', 'vdwtanner@gmail.com', NULL, NULL, 0, '2015-01-30 01:06:30', 'b5b41fac0361d157d9673ecb926af5ae', 1);
INSERT INTO `users` VALUES('Bardabass', '9716784a9409f47f5d6eca4dc4c1c6f7', 'brientcrowder@gmail.com', NULL, NULL, 0, '2015-02-01 14:46:47', '9b8619251a19057cff70779273e95aa6', 1);
INSERT INTO `users` VALUES('tehcoconut', '5f4dcc3b5aa765d61d8327deb882cf99', 'zdcorley@gmail.com', NULL, NULL, 0, '2015-02-01 20:44:33', 'd56b9fc4b0f1be8871f5e1c40c0067e7', 1);
