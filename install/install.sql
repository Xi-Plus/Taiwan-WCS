SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `area` (
`no` int(11) NOT NULL,
`area` varchar(5) NOT NULL,
`name` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `area` (`no`, `area`, `name`) VALUES
(4, 'E', '東部'),
(2, 'M', '中部'),
(1, 'N', '北部'),
(5, 'O', '外島'),
(3, 'S', '南部');

CREATE TABLE `city` (
`no` int(11) NOT NULL,
`area` varchar(5) NOT NULL,
`city` varchar(20) NOT NULL,
`name` varchar(10) NOT NULL,
`text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `city` (`no`, `area`, `city`, `name`, `text`) VALUES
(9, 'M', 'Changhua County', '彰化縣', ''),
(12, 'S', 'Chiayi City', '嘉義市', ''),
(13, 'S', 'Chiayi County', '嘉義縣', ''),
(5, 'N', 'Hsinchu City', '新竹市', ''),
(6, 'N', 'Hsinchu County', '新竹縣', ''),
(18, 'E', 'Hualien County', '花蓮縣', ''),
(15, 'S', 'Kaohsiung City', '高雄市', ''),
(1, 'N', 'Keelung City', '基隆市', ''),
(22, 'O', 'Kinmen County', '金門縣', ''),
(21, 'O', 'Lienchiang County', '連江縣', ''),
(7, 'M', 'Miaoli County', '苗栗縣', ''),
(11, 'M', 'Nantou county', '南投縣', ''),
(3, 'N', 'New Taipei City', '新北市', ''),
(20, 'O', 'Penghu County', '澎湖縣', ''),
(16, 'S', 'Pingtung County', '屏東縣', ''),
(8, 'M', 'Taichung City', '臺中市', ''),
(14, 'S', 'Tainan City', '臺南市', ''),
(2, 'N', 'Taipei City', '臺北市', ''),
(19, 'E', 'Taitung County', '臺東縣', ''),
(4, 'N', 'Taoyuan City', '桃園市', ''),
(17, 'E', 'Yilan County', '宜蘭縣', ''),
(10, 'M', 'Yunlin County', '雲林縣', '');

CREATE TABLE `follow` (
`uid` varchar(20) NOT NULL,
`city` varchar(20) NOT NULL,
`hash` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `log` (
`uid` varchar(20) NOT NULL,
`text` text NOT NULL,
`time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `area`
ADD UNIQUE KEY `area` (`area`);

ALTER TABLE `city`
ADD UNIQUE KEY `city` (`city`),
ADD UNIQUE KEY `no` (`no`);

ALTER TABLE `follow`
ADD UNIQUE KEY `hash` (`hash`);


ALTER TABLE `city`
MODIFY `no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
