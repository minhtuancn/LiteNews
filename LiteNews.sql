/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Article` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `WebsiteID` tinyint(4) NOT NULL,
  `URL` text NOT NULL,
  `Title` tinytext NOT NULL,
  `SubTitle` text,
  `Timestamp` int(11) NOT NULL,
  `LastUpdate` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=0;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ArticleParagraph` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ArticleID` int(11) NOT NULL,
  `Paragraph` text NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `ArticleID` (`ArticleID`)
) ENGINE=InnoDB AUTO_INCREMENT=0;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Feedback` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Type` tinyint(4) NOT NULL,
  `Content` text NOT NULL,
  `IP` tinytext NOT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Log` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IP` tinytext NOT NULL,
  `URL` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=0;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TitleList` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `WebsiteID` tinyint(4) NOT NULL,
  `Title` tinytext NOT NULL,
  `URL` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=0;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TitleListUpdate` (
  `WebsiteID` tinyint(4) NOT NULL,
  `LastUpdate` int(11) NOT NULL,
  KEY `WebsiteName` (`WebsiteID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
