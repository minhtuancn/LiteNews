-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 25, 2013 at 03:18 PM
-- Server version: 5.5.32
-- PHP Version: 5.3.10-1ubuntu3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `litenews_dev`
--

-- --------------------------------------------------------

--
-- Table structure for table `Article`
--

DROP TABLE IF EXISTS `Article`;
CREATE TABLE IF NOT EXISTS `Article` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `WebsiteID` tinyint(4) NOT NULL,
  `URL` text NOT NULL,
  `ListTitle` tinytext NOT NULL,
  `ArticleTitle` tinytext NOT NULL,
  `SubTitle` text,
  `Timestamp` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ArticleParagraph`
--

DROP TABLE IF EXISTS `ArticleParagraph`;
CREATE TABLE IF NOT EXISTS `ArticleParagraph` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ArticleID` int(11) NOT NULL,
  `Paragraph` text NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `ArticleID` (`ArticleID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Config`
--

DROP TABLE IF EXISTS `Config`;
CREATE TABLE IF NOT EXISTS `Config` (
  `ID` tinyint(4) NOT NULL AUTO_INCREMENT,
  `ParentID` tinyint(4) NOT NULL,
  `Name` tinytext NOT NULL,
  `Value` tinytext NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Feedback`
--

DROP TABLE IF EXISTS `Feedback`;
CREATE TABLE IF NOT EXISTS `Feedback` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Type` tinyint(4) NOT NULL,
  `Content` text NOT NULL,
  `Viewed` tinyint(1) NOT NULL DEFAULT '0',
  `IP` tinytext NOT NULL,
  `Timestamp` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Log`
--

DROP TABLE IF EXISTS `Log`;
CREATE TABLE IF NOT EXISTS `Log` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IP` tinytext NOT NULL,
  `Timestamp` int(11) NOT NULL,
  `URL` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `UpdateTime`
--

DROP TABLE IF EXISTS `UpdateTime`;
CREATE TABLE IF NOT EXISTS `UpdateTime` (
  `WebsiteID` tinyint(4) NOT NULL,
  `Timestamp` int(11) NOT NULL,
  PRIMARY KEY (`WebsiteID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
