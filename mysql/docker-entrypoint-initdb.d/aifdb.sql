-- MySQL dump 10.13  Distrib 5.7.30, for Linux (x86_64)
--
-- Host: localhost    Database: aifdb
-- ------------------------------------------------------
-- Server version	5.7.30-0ubuntu0.16.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

CREATE DATABASE aifdb;
USE aifdb;

--
-- Table structure for table `descriptorFulfillment`
--

DROP TABLE IF EXISTS `descriptorFulfillment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `descriptorFulfillment` (
  `nodeID` int(10) unsigned NOT NULL,
  `descriptorID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`nodeID`,`descriptorID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `descriptors`
--

DROP TABLE IF EXISTS `descriptors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `descriptors` (
  `descriptorID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `text` varchar(250) NOT NULL,
  PRIMARY KEY (`descriptorID`)
) ENGINE=MyISAM AUTO_INCREMENT=229 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `edges`
--

DROP TABLE IF EXISTS `edges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `edges` (
  `edgeID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fromID` int(10) unsigned NOT NULL,
  `toID` int(10) unsigned NOT NULL,
  `formEdgeID` int(10) DEFAULT NULL,
  PRIMARY KEY (`edgeID`),
  KEY `toID` (`toID`),
  KEY `fromID` (`fromID`)
) ENGINE=MyISAM AUTO_INCREMENT=658954 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `externalLinks`
--

DROP TABLE IF EXISTS `externalLinks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `externalLinks` (
  `table` varchar(32) NOT NULL,
  `rowID` int(10) NOT NULL,
  `field` varchar(32) NOT NULL,
  `URI` int(10) NOT NULL,
  PRIMARY KEY (`table`,`rowID`,`field`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formEdgeTypes`
--

DROP TABLE IF EXISTS `formEdgeTypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formEdgeTypes` (
  `formEdgeTypeID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `direction` enum('IN','OUT') NOT NULL,
  PRIMARY KEY (`formEdgeTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formEdges`
--

DROP TABLE IF EXISTS `formEdges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formEdges` (
  `formEdgeID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `schemeID` int(10) unsigned NOT NULL,
  `descriptorID` int(10) unsigned DEFAULT NULL,
  `schemeTarget` int(10) unsigned DEFAULT NULL,
  `formEdgeTypeID` int(10) NOT NULL,
  `name` varchar(128) NOT NULL,
  `description` varchar(128) DEFAULT NULL,
  `CQ` varchar(128) DEFAULT NULL,
  `Explicit` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`formEdgeID`)
) ENGINE=MyISAM AUTO_INCREMENT=433 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `locutions`
--

DROP TABLE IF EXISTS `locutions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `locutions` (
  `nodeID` int(10) unsigned NOT NULL,
  `personID` int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `start` varchar(64) DEFAULT NULL,
  `end` varchar(64) DEFAULT NULL,
  `source` varchar(256) DEFAULT NULL,
  KEY `nodeID` (`nodeID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `locutions_seq`
--

DROP TABLE IF EXISTS `locutions_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `locutions_seq` (
  `sequence` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`sequence`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nodeSetMappings`
--

DROP TABLE IF EXISTS `nodeSetMappings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nodeSetMappings` (
  `nodeID` int(10) unsigned NOT NULL,
  `nodeSetID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`nodeID`,`nodeSetID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nodeSets`
--

DROP TABLE IF EXISTS `nodeSets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nodeSets` (
  `nodeSetID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`nodeSetID`)
) ENGINE=MyISAM AUTO_INCREMENT=17854 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nodes`
--

DROP TABLE IF EXISTS `nodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nodes` (
  `nodeID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `text` longtext NOT NULL,
  `type` varchar(2) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`nodeID`)
) ENGINE=MyISAM AUTO_INCREMENT=506009 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `people`
--

DROP TABLE IF EXISTS `people`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `people` (
  `personID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `firstName` varchar(64) NOT NULL,
  `surname` varchar(32) NOT NULL,
  `description` text,
  PRIMARY KEY (`personID`)
) ENGINE=MyISAM AUTO_INCREMENT=3859 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `schemeFulfillment`
--

DROP TABLE IF EXISTS `schemeFulfillment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `schemeFulfillment` (
  `nodeID` int(10) unsigned NOT NULL,
  `schemeID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`nodeID`,`schemeID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `schemeTypes`
--

DROP TABLE IF EXISTS `schemeTypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `schemeTypes` (
  `schemeTypeID` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`schemeTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `schemes`
--

DROP TABLE IF EXISTS `schemes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `schemes` (
  `schemeID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `schemeTypeID` int(10) NOT NULL,
  PRIMARY KEY (`schemeID`)
) ENGINE=MyISAM AUTO_INCREMENT=408 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `userID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`userID`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-05-20 17:04:12
