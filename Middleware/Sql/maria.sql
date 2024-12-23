/* maria db create / reset -- cloud & service */

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

DROP TABLE IF EXISTS `wi_agent_company`, `wi_agent_user`, `wi_company_user`, `wi_company_power_focus`, `wi_pf_data`, `wi_virtual_station`, `wi_power_focus`, `wi_agent`, `wi_company`, `wi_user` CASCADE;

-- 장비 데이터
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 장비 포트 설정
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 장비 설정
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 에이전트
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 회사
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 회원
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 회사 회원
CREATE TABLE `wi_company_user` (
  `wc_idx` int(11) unsigned NOT NULL,
  `wu_idx` int(11) unsigned NOT NULL,
  UNIQUE KEY `wu_idx` (`wu_idx`),
  KEY `wi_company_wi_user` (`wc_idx`),
  CONSTRAINT `wi_company_wi_user` FOREIGN KEY (`wc_idx`) REFERENCES `wi_company` (`wc_idx`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `wi_user_wi_company` FOREIGN KEY (`wu_idx`) REFERENCES `wi_user` (`wu_idx`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 회사 장비
CREATE TABLE `wi_company_power_focus` (
  `wc_idx` int(11) unsigned NOT NULL,
  `wp_idx` int(11) unsigned NOT NULL,
  UNIQUE KEY `wp_idx` (`wp_idx`),
  KEY `wi_company_wi_power_focus` (`wc_idx`),
  CONSTRAINT `wi_company_wi_power_focus` FOREIGN KEY (`wc_idx`) REFERENCES `wi_company` (`wc_idx`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `wi_power_focus_wi_company` FOREIGN KEY (`wp_idx`) REFERENCES `wi_power_focus` (`wp_idx`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 회사 에이전트
CREATE TABLE `wi_agent_company` (
  `wa_idx` int(11) unsigned NOT NULL,
  `wc_idx` int(11) unsigned NOT NULL,
  UNIQUE KEY `wc_idx` (`wc_idx`),
  KEY `wi_agent_wi_company` (`wa_idx`),
  CONSTRAINT `wi_agent_wi_company` FOREIGN KEY (`wa_idx`) REFERENCES `wi_agent` (`wa_idx`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `wi_company_wi_agent` FOREIGN KEY (`wc_idx`) REFERENCES `wi_company` (`wc_idx`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 에이전트 회원
CREATE TABLE `wi_agent_user` (
  `wa_idx` int(11) unsigned NOT NULL,
  `wu_idx` int(11) unsigned NOT NULL,
  UNIQUE KEY `wu_idx` (`wu_idx`),
  KEY `wi_agent_wi_user` (`wa_idx`),
  CONSTRAINT `wi_agent_wi_user` FOREIGN KEY (`wa_idx`) REFERENCES `wi_agent` (`wa_idx`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `wi_user_wi_agent` FOREIGN KEY (`wu_idx`) REFERENCES `wi_user` (`wu_idx`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `wi_user`
(
    `wu_email`,
    `wu_passwd`,
    `wu_name`,
    `wu_name_last`,
    `wu_level`,
    `wu_tel`,
    `wu_access`,
    `wu_create_date`
)
VALUES
(
    'mail@ulalalab.com',
    '$2y$10$tgVpKlQVVKrVRz0fn6Qhs.IlESWvarjMt2lWfJoI.ESL9VVZDRj6W',
    '시스템관리자',
    '울랄라랩',
    255, -- 127
    '+82-2-873-0010',
    'Y',
    NOW()
);

-- insert into wi_company
--     (`wc_name`, `wc_ceo`, `wc_business`, `wc_tel`, `wc_zip`, `wc_address`, `wc_address_detail`, `wc_access`, `wc_create_date`)
-- select
--     lpad(conv(floor(rand()*pow(36,8)), 10, 36), 8, 0),
--     lpad(conv(floor(rand()*pow(36,8)), 10, 36), 8, 0),
--     `wc_business`,
--     `wc_tel`,
--     `wc_zip`,
--     `wc_address`,
--     `wc_address_detail`,
--     `wc_access`,
--     NOW()
-- from
--     wi_company

-- insert into wi_user
--     (`wu_email`, `wu_passwd`, `wu_name`, `wu_name_last`, `wu_level`, `wu_tel`, `wu_access`, `wu_create_date`)
-- select
--     concat(lpad(conv(floor(rand()*pow(36,8)), 10, 36), 8, 0), '@mail.com'),
--     lpad(conv(floor(rand()*pow(36,8)), 10, 36), 8, 0),
--     `wu_name`,
--     `wu_name_last`,
--     1,
--     `wu_tel`,
--     `wu_access`,
--     NOW()
-- from
--     wi_user

-- insert into wi_power_focus
--     (`wp_name`, `wp_serial`, `wp_sw_version`, `wp_hw_version`, `wp_server`, `wp_create_date`)
-- select
--     lpad(conv(floor(rand()*pow(36,8)), 10, 36), 8, 0),
--     lpad(conv(floor(rand()*pow(36,8)), 10, 36), 8, 0),
--     lpad(conv(floor(rand()*pow(36,8)), 10, 36), 8, 0),
--     lpad(conv(floor(rand()*pow(36,8)), 10, 36), 8, 0),
--     lpad(conv(floor(rand()*pow(36,8)), 10, 36), 8, 0),
--     NOW()
-- from
--     wi_power_focus

-- insert into wi_pf_data (
-- wp_idx,
-- wv_idx,
-- wu_idx,
-- wd_torque,
-- wd_angle,
-- wd_set,
-- wd_status,
-- wd_create_date
-- ) values (
-- 0,
-- 0,
-- 0,
-- CONCAT(FLOOR((RAND() * (100-(-100)+1))+(-100)), '.', FLOOR((RAND() * (100-(-100)+1))+(-100))),
-- CONCAT(FLOOR((RAND() * (100-(-100)+1))+(-100)), '.', FLOOR((RAND() * (100-(-100)+1))+(-100))),
-- CONCAT(FLOOR((RAND() * (100-(-100)+1))+(-100)), '.', FLOOR((RAND() * (100-(-100)+1))+(-100))),
-- '1',
-- now()
-- )

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

/* widget design -- types */

/* alarm, push */
  -- `1` varchar(25) DEFAULT NULL,
  -- `2` int(11) DEFAULT NULL,
  -- `3` char(1) DEFAULT NULL,
  -- `4` smallint(5) DEFAULT 0,
  -- `5` decimal(23,10) DEFAULT 0.0,
  -- `6` text NULL ,
  -- `7` varchar(25) NOT NULL DEFAULT '1',
  -- `8` int(11) NOT NULL DEFAULT 1,
  -- `9` char(1) NOT NULL DEFAULT 'A',
  -- `10` smallint(5) NOT NULL DEFAULT 0,
  -- `11` decimal(23,10) NOT NULL DEFAULT 0.0,
  -- `12` text NOT NULL DEFAULT 'A',
  -- `13` enum(1,2,3) NOT NULL DEFAULT 1,
