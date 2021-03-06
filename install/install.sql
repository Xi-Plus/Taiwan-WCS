SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `taiwan_wcs_city` (
  `no` tinyint(4) NOT NULL DEFAULT '0',
  `city` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `taiwan_wcs_city` (`no`, `city`) VALUES
(11, '南投縣'),
(12, '嘉義市'),
(13, '嘉義縣'),
(1, '基隆市'),
(17, '宜蘭縣'),
(16, '屏東縣'),
(9, '彰化縣'),
(3, '新北市'),
(5, '新竹市'),
(6, '新竹縣'),
(4, '桃園市'),
(20, '澎湖縣'),
(8, '臺中市'),
(2, '臺北市'),
(14, '臺南市'),
(19, '臺東縣'),
(18, '花蓮縣'),
(7, '苗栗縣'),
(21, '連江縣'),
(22, '金門縣'),
(10, '雲林縣'),
(15, '高雄市');

CREATE TABLE `taiwan_wcs_cityshortname` (
  `shortname` varchar(10) NOT NULL,
  `fullname` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `taiwan_wcs_cityshortname` (`shortname`, `fullname`) VALUES
('南投', '南投縣'),
('台中', '臺中市'),
('台中市', '臺中市'),
('台北', '臺北市'),
('台北市', '臺北市'),
('台南', '臺南市'),
('台南市', '臺南市'),
('台東', '臺東縣'),
('台東縣', '臺東縣'),
('嘉市', '嘉義市'),
('嘉縣', '嘉義縣'),
('基隆', '基隆市'),
('宜蘭', '宜蘭縣'),
('屏東', '屏東縣'),
('彰化', '彰化縣'),
('新北', '新北市'),
('桃園', '桃園市'),
('澎湖', '澎湖縣'),
('竹市', '新竹市'),
('竹縣', '新竹縣'),
('臺中', '臺中市'),
('臺北', '臺北市'),
('臺南', '臺南市'),
('臺東', '臺東縣'),
('花蓮', '花蓮縣'),
('苗栗', '苗栗縣'),
('連江', '連江縣'),
('金門', '金門縣'),
('雲林', '雲林縣'),
('馬祖', '連江縣'),
('高雄', '高雄市');

CREATE TABLE `taiwan_wcs_follow` (
  `tmid` varchar(50) NOT NULL,
  `city` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `taiwan_wcs_input` (
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `input` text NOT NULL,
  `hash` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `taiwan_wcs_log` (
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `message` text NOT NULL,
  `hash` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `taiwan_wcs_msg` (
  `city` varchar(10) COLLATE utf8_bin NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `msg` varchar(255) COLLATE utf8_bin NOT NULL,
  `fbpost` tinyint(1) DEFAULT '0',
  `fbmessage` tinyint(1) NOT NULL DEFAULT '0',
  `tgmessage` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `taiwan_wcs_msgqueue` (
  `tmid` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `hash` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `taiwan_wcs_spam` (
  `tmid` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `taiwan_wcs_user` (
  `uid` varchar(255) NOT NULL,
  `tmid` varchar(255) NOT NULL,
  `sid` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL,
  `lastread` timestamp NOT NULL DEFAULT '2038-01-19 03:14:07'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `taiwan_wcs_city`
  ADD UNIQUE KEY `name` (`city`);

ALTER TABLE `taiwan_wcs_cityshortname`
  ADD UNIQUE KEY `shortname` (`shortname`);

ALTER TABLE `taiwan_wcs_input`
  ADD UNIQUE KEY `hash` (`hash`);

ALTER TABLE `taiwan_wcs_log`
  ADD UNIQUE KEY `hash` (`hash`);

ALTER TABLE `taiwan_wcs_msgqueue`
  ADD UNIQUE KEY `hash` (`hash`);

ALTER TABLE `taiwan_wcs_user`
  ADD UNIQUE KEY `tmid` (`tmid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
