SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


DROP TABLE IF EXISTS `Article`;
CREATE TABLE IF NOT EXISTS `Article` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `WebsiteID` tinyint(4) NOT NULL,
  `URL` text NOT NULL,
  `Title` tinytext NOT NULL,
  `SubTitle` text NOT NULL,
  `LastUpdate` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `ArticleParagraph`;
CREATE TABLE IF NOT EXISTS `ArticleParagraph` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ArticleID` int(11) NOT NULL,
  `Paragraph` text NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `ArticleID` (`ArticleID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `Log`;
CREATE TABLE IF NOT EXISTS `Log` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IP` tinytext NOT NULL,
  `URL` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `TitleList`;
CREATE TABLE IF NOT EXISTS `TitleList` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `WebsiteID` tinyint(4) NOT NULL,
  `Title` tinytext NOT NULL,
  `URL` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `TitleListUpdate`;
CREATE TABLE IF NOT EXISTS `TitleListUpdate` (
  `WebsiteID` tinyint(4) NOT NULL,
  `LastUpdate` int(11) NOT NULL,
  KEY `WebsiteName` (`WebsiteID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;