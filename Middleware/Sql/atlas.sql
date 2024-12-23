-- MySQL dump 10.13  Distrib 5.7.23, for Linux (x86_64)
--
-- Host: localhost    Database: atlas
-- ------------------------------------------------------
-- Server version	5.7.23-log

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

--
-- Table structure for table `wi_agent`
--

DROP TABLE IF EXISTS `wi_agent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wi_agent` (
  `wa_idx` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `wa_name` varchar(255) NOT NULL DEFAULT 'company' COMMENT '대리점명',
  `wa_ceo` varchar(255) NOT NULL DEFAULT 'ceo' COMMENT '대표',
  `wa_business` varchar(255) DEFAULT NULL COMMENT '업종',
  `wa_logo` varchar(255) NOT NULL DEFAULT '' COMMENT '로고 경로',
  `wa_tel` varchar(45) NOT NULL DEFAULT '- -' COMMENT '연락처',
  `wa_zip` varchar(45) NOT NULL DEFAULT '- -' COMMENT '우편번호',
  `wa_address` text COMMENT '주소',
  `wa_address_detail` text COMMENT '상세 주소',
  `wa_access` char(1) NOT NULL DEFAULT 'N' COMMENT '승인 여부',
  `wa_delete_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '삭제 일자',
  `wa_update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '업데이트 일자',
  `wa_create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '등록 일자',
  PRIMARY KEY (`wa_idx`),
  UNIQUE KEY `wa_idx` (`wa_idx`),
  KEY `wa_delete_date` (`wa_delete_date`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wi_agent`
--

LOCK TABLES `wi_agent` WRITE;
/*!40000 ALTER TABLE `wi_agent` DISABLE KEYS */;
INSERT INTO `wi_agent` VALUES (3,'TestAgent','TA','사출','','01087654321','14099','경기도 안양시','관양두산벤처다임 506호','Y','0000-00-00 00:00:00','2018-11-07 04:35:15','2018-11-07 13:35:15'),(5,'우리집이다','도현수수수수수','소프트','','12341234','23456','경기도 안양시 동안구','관양두산벤처다임','Y','0000-00-00 00:00:00','2018-11-23 02:52:31','2018-11-23 11:47:54'),(6,'4','4','4','','4','4','4','4','Y','0000-00-00 00:00:00','2018-12-10 09:06:07','2018-12-10 18:06:07'),(7,'새로운대리점','ㅋㅋㅋ대리점 점주','자동차부품','','231231','ㄹㅈㄷ','`123','123','Y','0000-00-00 00:00:00','2018-12-12 21:05:23','2018-12-12 21:05:23');
/*!40000 ALTER TABLE `wi_agent` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wi_agent_company`
--

DROP TABLE IF EXISTS `wi_agent_company`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wi_agent_company` (
  `wa_idx` int(11) unsigned NOT NULL,
  `wc_idx` int(11) unsigned NOT NULL,
  UNIQUE KEY `wc_idx` (`wc_idx`),
  KEY `wi_agent_wi_company` (`wa_idx`),
  CONSTRAINT `wi_agent_wi_company` FOREIGN KEY (`wa_idx`) REFERENCES `wi_agent` (`wa_idx`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `wi_company_wi_agent` FOREIGN KEY (`wc_idx`) REFERENCES `wi_company` (`wc_idx`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wi_agent_company`
--

LOCK TABLES `wi_agent_company` WRITE;
/*!40000 ALTER TABLE `wi_agent_company` DISABLE KEYS */;
INSERT INTO `wi_agent_company` VALUES (3,5),(7,7),(7,12),(7,13);
/*!40000 ALTER TABLE `wi_agent_company` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wi_agent_user`
--

DROP TABLE IF EXISTS `wi_agent_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wi_agent_user` (
  `wa_idx` int(11) unsigned NOT NULL,
  `wu_idx` int(11) unsigned NOT NULL,
  UNIQUE KEY `wu_idx` (`wu_idx`),
  KEY `wi_agent_wi_user` (`wa_idx`),
  CONSTRAINT `wi_agent_wi_user` FOREIGN KEY (`wa_idx`) REFERENCES `wi_agent` (`wa_idx`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `wi_user_wi_agent` FOREIGN KEY (`wu_idx`) REFERENCES `wi_user` (`wu_idx`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wi_agent_user`
--

LOCK TABLES `wi_agent_user` WRITE;
/*!40000 ALTER TABLE `wi_agent_user` DISABLE KEYS */;
INSERT INTO `wi_agent_user` VALUES (3,5),(3,16),(5,8),(5,9),(5,10),(7,7),(7,11);
/*!40000 ALTER TABLE `wi_agent_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wi_company`
--

DROP TABLE IF EXISTS `wi_company`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wi_company` (
  `wc_idx` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `wc_name` varchar(255) NOT NULL DEFAULT 'company' COMMENT '회사명',
  `wc_ceo` varchar(255) NOT NULL DEFAULT 'ceo' COMMENT '대표',
  `wc_business` varchar(255) DEFAULT NULL COMMENT '업종',
  `wc_logo` varchar(255) NOT NULL DEFAULT '' COMMENT '로고 경로',
  `wc_tel` varchar(45) NOT NULL DEFAULT '- -' COMMENT '연락처',
  `wc_zip` varchar(45) NOT NULL DEFAULT '- -' COMMENT '우편번호',
  `wc_address` text COMMENT '주소',
  `wc_address_detail` text COMMENT '상세 주소',
  `wc_access` char(1) NOT NULL DEFAULT 'N' COMMENT '승인 여부',
  `wc_delete_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '삭제 일자',
  `wc_update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '업데이트 일자',
  `wc_create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '등록 일자',
  PRIMARY KEY (`wc_idx`),
  UNIQUE KEY `wc_idx` (`wc_idx`),
  KEY `wc_delete_date` (`wc_delete_date`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wi_company`
--

LOCK TABLES `wi_company` WRITE;
/*!40000 ALTER TABLE `wi_company` DISABLE KEYS */;
INSERT INTO `wi_company` VALUES (5,'TestCompany','TC','사출','','01012344321','14056','경기도 안양시','관양두산벤처다임 506호','Y','0000-00-00 00:00:00','2018-11-07 04:40:09','2018-11-07 13:40:09'),(6,'TestCompany1','TC1','사출','','01043214321','14056','경기도 안양시','관양두산벤처다임 506호','Y','0000-00-00 00:00:00','2018-11-14 08:44:28','2018-11-14 17:44:28'),(7,'TestCompany2','TC2','사출','','01043214321','14056','경기도 안양시','관양두산벤처다임 506호','Y','0000-00-00 00:00:00','2018-11-14 08:44:28','2018-11-14 17:44:28'),(9,'우리집','도현수','소프트웨어','','+810112341234','40251','경기도 안양시 동안구 관','관양두산벤처다임','Y','0000-00-00 00:00:00','2018-11-23 02:45:41','2018-11-23 11:25:37'),(10,'우리집이당당','도현현현수수','소소프트트','','01012341234','123456','경기도 안양시 동안구','관야동','N','0000-00-00 00:00:00','2018-11-23 03:19:32','2018-11-23 12:19:25'),(11,'4','4','4','','4','4','4','4','Y','0000-00-00 00:00:00','2018-12-10 09:07:06','2018-12-10 18:07:06'),(12,'새로운 회사','새로운 회사','새로운 회사','','새로운 회사','ㄴㅇㅁ','ㅇㅁㄴ','ㅇㅁㄴ','Y','0000-00-00 00:00:00','2018-12-12 22:47:03','2018-12-12 22:47:03'),(13,'대리점이 만든 회사','ㄴㅇㅁㅇㄴㅇㅁㄴ','ㅇㅁㄴㅇ','','ㅁㄴㅇㅁㄴㅇ','ㅇㄴㅁ','ㅇㄴㅁ','ㅇㅁㄴ','Y','0000-00-00 00:00:00','2018-12-16 17:46:29','2018-12-16 17:46:29'),(14,'Nodevice','fawe','fawe','','fawe','few','few','fwe','Y','0000-00-00 00:00:00','2018-12-17 17:50:59','2018-12-17 17:50:59');
/*!40000 ALTER TABLE `wi_company` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wi_company_power_focus`
--

DROP TABLE IF EXISTS `wi_company_power_focus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wi_company_power_focus` (
  `wc_idx` int(11) unsigned NOT NULL,
  `wp_idx` int(11) unsigned NOT NULL,
  UNIQUE KEY `wp_idx` (`wp_idx`),
  KEY `wi_company_wi_power_focus` (`wc_idx`),
  CONSTRAINT `wi_company_wi_power_focus` FOREIGN KEY (`wc_idx`) REFERENCES `wi_company` (`wc_idx`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `wi_power_focus_wi_company` FOREIGN KEY (`wp_idx`) REFERENCES `wi_power_focus` (`wp_idx`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wi_company_power_focus`
--

LOCK TABLES `wi_company_power_focus` WRITE;
/*!40000 ALTER TABLE `wi_company_power_focus` DISABLE KEYS */;
INSERT INTO `wi_company_power_focus` VALUES (5,4),(11,7),(12,5);
/*!40000 ALTER TABLE `wi_company_power_focus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wi_company_user`
--

DROP TABLE IF EXISTS `wi_company_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wi_company_user` (
  `wc_idx` int(11) unsigned NOT NULL,
  `wu_idx` int(11) unsigned NOT NULL,
  UNIQUE KEY `wu_idx` (`wu_idx`),
  KEY `wi_company_wi_user` (`wc_idx`),
  CONSTRAINT `wi_company_wi_user` FOREIGN KEY (`wc_idx`) REFERENCES `wi_company` (`wc_idx`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `wi_user_wi_company` FOREIGN KEY (`wu_idx`) REFERENCES `wi_user` (`wu_idx`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wi_company_user`
--

LOCK TABLES `wi_company_user` WRITE;
/*!40000 ALTER TABLE `wi_company_user` DISABLE KEYS */;
INSERT INTO `wi_company_user` VALUES (5,5),(11,8),(12,10),(12,12),(14,15);
/*!40000 ALTER TABLE `wi_company_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wi_pf_data`
--

DROP TABLE IF EXISTS `wi_pf_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wi_pf_data` (
  `wd_idx` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `wp_idx` int(11) unsigned DEFAULT '0' COMMENT '장비 idx',
  `wv_idx` int(11) unsigned DEFAULT '0' COMMENT '장비 포트 idx',
  `wu_idx` int(11) unsigned DEFAULT '0' COMMENT '유저 idx',
  `wd_torque` varchar(255) NOT NULL COMMENT '토크',
  `wd_torque_max` varchar(255) NOT NULL DEFAULT '0' COMMENT '토크 MAX',
  `wd_angle` varchar(255) NOT NULL COMMENT '앵글',
  `wd_angle_max` varchar(255) NOT NULL DEFAULT '0' COMMENT '앵글 MAX',
  `wd_set` varchar(255) NOT NULL COMMENT '세트',
  `wd_status` varchar(255) NOT NULL COMMENT '상태',
  `wd_update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '업데이트 일자',
  `wd_create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '등록 일자',
  PRIMARY KEY (`wd_idx`),
  UNIQUE KEY `wd_idx` (`wd_idx`),
  KEY `wp_idx` (`wp_idx`,`wv_idx`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wi_pf_data`
--

LOCK TABLES `wi_pf_data` WRITE;
/*!40000 ALTER TABLE `wi_pf_data` DISABLE KEYS */;
INSERT INTO `wi_pf_data` VALUES (81,4,8,5,'0.9','0','100','0','dd','1','2018-12-18 00:59:25','2018-12-18 00:44:55'),(82,4,8,5,'0.3','0','100','0','dd','1','2018-12-18 00:59:24','2018-12-18 00:46:34'),(83,4,9,5,'0.9','0','100','0','dd','1','2018-12-18 01:23:45','2018-12-18 00:46:55'),(84,4,3,5,'0.3','0','100','0','dd','1','2018-12-18 00:47:45','2018-12-18 00:47:36'),(85,4,3,5,'0.9','0','100','0','dd','1','2018-12-18 00:50:00','2018-12-18 00:49:50'),(86,4,3,5,'0.8','0','100','0','dd','1','2018-12-18 00:59:39','2018-12-18 00:59:11'),(87,4,3,5,'0.3','0','100','0','dd','1','2018-12-18 01:00:18','2018-12-18 01:00:10'),(88,4,3,5,'0.9','0','100','0','dd','1','2018-12-18 01:02:10','2018-12-18 01:02:04'),(89,4,3,5,'0.1','0','100','0','dd','1','2018-12-18 01:03:03','2018-12-18 01:02:54'),(90,4,3,5,'0.9','0','100','0','dd','1','2018-12-18 01:03:27','2018-12-18 01:03:22'),(91,4,3,5,'0.2','0','100','0','dd','1','2018-12-18 01:08:27','2018-12-18 01:08:27'),(92,4,3,5,'0.3','0','100','0','dd','1','2018-12-18 21:23:05','2018-12-19 01:10:29'),(93,4,3,5,'0.9','0','100','0','dd','1','2018-12-18 21:23:01','2018-12-19 01:10:47'),(94,4,3,5,'0.5','2','200','0','ef','1','2018-12-20 00:01:57','2018-12-20 23:13:07'),(95,4,8,5,'0.5','0','200','0','ee','1','2018-12-18 21:22:55','2018-12-19 01:14:26'),(96,4,8,5,'0.2','0','200','0','qq','1','2018-12-18 21:22:52','2018-12-19 01:22:03'),(97,4,8,5,'0.7','0','200','0','yy','1','2018-12-18 21:22:49','2018-12-19 01:24:14'),(98,4,8,5,'0.1','0','200','0','tt','1','2018-12-18 21:22:47','2018-12-19 01:28:04'),(99,4,8,5,'0.5','0','200','0','aa','1','2018-12-18 21:22:45','2018-12-19 01:30:27'),(100,4,8,5,'0.7','0','500','0','last','1','2018-12-20 00:01:52','2018-12-20 23:31:12'),(101,4,8,5,'0.5','0','200','200','ff','1','2018-12-20 00:02:40','2018-12-20 23:32:13');
/*!40000 ALTER TABLE `wi_pf_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wi_power_focus`
--

DROP TABLE IF EXISTS `wi_power_focus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wi_power_focus` (
  `wp_idx` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `wp_name` varchar(255) NOT NULL DEFAULT '' COMMENT '장비명',
  `wp_serial` varchar(255) NOT NULL DEFAULT '' COMMENT '시리얼',
  `wp_sw_version` varchar(45) NOT NULL DEFAULT '' COMMENT '소프트웨어 버전',
  `wp_hw_version` varchar(45) NOT NULL DEFAULT '' COMMENT '하드웨어 버전',
  `wp_server` varchar(100) NOT NULL DEFAULT '' COMMENT '접속할 아이피',
  `wp_delete_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '삭제 일자',
  `wp_update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '업데이트 일자',
  `wp_create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '등록 일자',
  PRIMARY KEY (`wp_idx`),
  UNIQUE KEY `wp_serial` (`wp_serial`,`wp_delete_date`),
  KEY `wp_delete_date` (`wp_delete_date`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wi_power_focus`
--

LOCK TABLES `wi_power_focus` WRITE;
/*!40000 ALTER TABLE `wi_power_focus` DISABLE KEYS */;
INSERT INTO `wi_power_focus` VALUES (4,'PF600','D7480773','1.0','1.0','Atlas','0000-00-00 00:00:00','2018-11-08 02:27:55','2018-11-07 13:41:24'),(5,'PF601','D7480774','1.0','1.0','Atlas','0000-00-00 00:00:00','2018-11-08 02:27:55','2018-11-07 13:41:24'),(6,'PF-100','CE1002RT21','v.31.12','v.12.05','PHP','0000-00-00 00:00:00','2018-11-23 02:21:09','2018-11-23 11:20:55'),(7,'4','4','4','4','4','0000-00-00 00:00:00','2018-12-10 09:07:16','2018-12-10 18:07:16');
/*!40000 ALTER TABLE `wi_power_focus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wi_user`
--

DROP TABLE IF EXISTS `wi_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wi_user` (
  `wu_idx` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `wu_email` varchar(100) NOT NULL COMMENT '이메일',
  `wu_passwd` varchar(255) NOT NULL COMMENT '패스워드',
  `wu_name` varchar(255) NOT NULL COMMENT '사용자 이름',
  `wu_name_last` varchar(255) NOT NULL COMMENT '사용자 성',
  `wu_level` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '권한 높을 수록 높은 계정',
  `wu_tel` varchar(45) NOT NULL DEFAULT '- -' COMMENT '연락처',
  `wu_comment` text COMMENT '메모',
  `wu_access` char(1) NOT NULL DEFAULT 'N' COMMENT '접속 권한 : N - 미승인 / Y - 승인 / R - 접속차단',
  `wu_delete_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '삭제 일자',
  `wu_update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '업데이트 일자',
  `wu_create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '등록 일자',
  PRIMARY KEY (`wu_idx`),
  UNIQUE KEY `wu_email` (`wu_email`),
  KEY `wu_delete_date` (`wu_delete_date`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wi_user`
--

LOCK TABLES `wi_user` WRITE;
/*!40000 ALTER TABLE `wi_user` DISABLE KEYS */;
INSERT INTO `wi_user` VALUES (1,'mail@ulalalab.com','$2y$10$EeSqiAEHaeZNFSibt24SFeNirP6eVJwf3JVuuMMu9R4IG0ZCeQqHy','시스템관리자','울랄라랩',127,'+82-2-873-0010',NULL,'Y','0000-00-00 00:00:00','2018-12-12 13:08:25','2018-10-23 07:45:34'),(5,'test@gmail.com','$2y$10$EeSqiAEHaeZNFSibt24SFeNirP6eVJwf3JVuuMMu9R4IG0ZCeQqHy','Test','Lee',1,'010123456711','','Y','0000-00-00 00:00:00','2018-12-10 18:59:01','2018-11-07 13:33:07'),(7,'hsdo@ulalalab.com','$2y$10$EeSqiAEHaeZNFSibt24SFeNirP6eVJwf3JVuuMMu9R4IG0ZCeQqHy','현수','도',1,'010123452313213','aHNkb0B1bGFsYWxhYi5jb20aINV7kdMc2ij9XwFrhSOns4eUEB8t6YPZTxHuQpD','Y','0000-00-00 00:00:00','2018-12-12 13:08:26','2018-11-23 11:14:31'),(8,'4124214@gmail.com','$2y$10$EeSqiAEHaeZNFSibt24SFeNirP6eVJwf3JVuuMMu9R4IG0ZCeQqHy','4','4',1,'4','','Y','0000-00-00 00:00:00','2018-12-12 13:09:37','2018-12-10 18:06:47'),(9,'789789@gmail.com','$2y$10$EeSqiAEHaeZNFSibt24SFeNirP6eVJwf3JVuuMMu9R4IG0ZCeQqHy','789','789',1,'','Nzg5Nzg5QGdtYWlsLmNvbQ4Mfv5ZFWjY7xamoSA2dwUeHJBKPsn0kD8TIO1Xrc','N','0000-00-00 00:00:00','2018-12-12 13:08:27','2018-12-11 23:41:25'),(10,'234234@dgmong.com','$2y$10$EeSqiAEHaeZNFSibt24SFeNirP6eVJwf3JVuuMMu9R4IG0ZCeQqHy','234234','234234',1,'','MjM0MjM0QGRnbW9uZy5jb205xFQaMPicp9ydV41GTbszWEX7IJDUwjqvgZn3mrC','N','0000-00-00 00:00:00','2018-12-12 13:08:28','2018-12-11 23:43:20'),(11,'smile@dgmong.com','$2y$10$A.f1pb8qxnE7WG09SuBx3uPwzZSWDNDY6B3kFUvhtHKJtHZj2RUFS','디지','몽',1,'312312321','','Y','0000-00-00 00:00:00','0000-00-00 00:00:00','2018-12-12 19:55:11'),(12,'company@dgmong.com','$2y$10$W4iIOC8EnyH8jesUVSRasezkTIAgWDAG0WLjPKlGSuPG0qN3YlgR2','321','312',1,'312','','Y','0000-00-00 00:00:00','2018-12-12 23:08:18','2018-12-12 22:51:16'),(13,'guest@dgmong.com','$2y$10$si6DogAMCjhUpj1ZuSE.MOrwIoRoONwKgoTHd6FHdCUXPTdZ3U3FS','guest','gest',1,'123213333','','Y','0000-00-00 00:00:00','2018-12-18 17:50:13','2018-12-16 16:51:45'),(14,'foragent@dgmong.com','$2y$10$c47xBbR4Hs9ItL8qkS.hw.pBQ64Jc8nQnFwLMia6B/bO7UAkzHaRi','231','312',1,'312312','','Y','0000-00-00 00:00:00','0000-00-00 00:00:00','2018-12-16 18:20:59'),(15,'companyNoDeivce@dgmong.com','$2y$10$EAQOO3jUO/YjR.yXGHziauTB1MGXCFFGxIzs8.OA/cbbhNbsAj1ey','fawe','fwe',1,'fawe','','Y','0000-00-00 00:00:00','0000-00-00 00:00:00','2018-12-17 17:50:35'),(16,'agentNew@dgmong.com','$2y$10$dkyODDCzl76Zg6nPhsmyi.uJFzCnYn9DKZfEwrmPlQuEjDcXpWxqC','4234324','23423',1,'4234','','Y','0000-00-00 00:00:00','0000-00-00 00:00:00','2018-12-18 21:04:40');
/*!40000 ALTER TABLE `wi_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wi_virtual_station`
--

DROP TABLE IF EXISTS `wi_virtual_station`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wi_virtual_station` (
  `wv_idx` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `wp_idx` int(11) unsigned DEFAULT '0' COMMENT '장비 idx',
  `wu_idx` int(11) unsigned DEFAULT '0' COMMENT '유저 idx',
  `wv_port` int(11) unsigned DEFAULT '0' COMMENT '포트',
  `wv_state` char(1) NOT NULL DEFAULT 'N' COMMENT '사용 유무',
  `wv_update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '업데이트 일자',
  `wv_create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '등록 일자',
  PRIMARY KEY (`wv_idx`),
  UNIQUE KEY `wp_idx` (`wp_idx`,`wv_port`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wi_virtual_station`
--

LOCK TABLES `wi_virtual_station` WRITE;
/*!40000 ALTER TABLE `wi_virtual_station` DISABLE KEYS */;
INSERT INTO `wi_virtual_station` VALUES (3,4,5,4545,'Y','2018-12-19 10:03:23','2018-11-14 20:35:12'),(8,4,5,4546,'Y','2018-12-20 00:00:10','2018-12-10 18:27:16'),(9,4,5,4547,'Y','2018-12-20 00:00:14','2018-12-12 09:31:34'),(10,6,6,1234,'Y','2018-12-17 21:26:01','2018-12-12 21:12:25');
/*!40000 ALTER TABLE `wi_virtual_station` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-12-20 11:20:39
