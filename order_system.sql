-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- 主機： localhost
-- 產生時間： 2025 年 06 月 11 日 12:23
-- 伺服器版本： 8.0.17
-- PHP 版本： 7.3.10
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
 /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
 /*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `order_system`
--
CREATE DATABASE IF NOT EXISTS `order_system` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `order_system`;

-- --------------------------------------------------------

--
-- 資料表結構 `customer`
--

CREATE TABLE `customer` (
  `customer_id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `phone` VARCHAR(10) NOT NULL,
  PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `restaurant`
--

CREATE TABLE `restaurant` (
  `restaurant_id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `phone` VARCHAR(10) NOT NULL,
  `location` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`restaurant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `menu_item`
--

CREATE TABLE `menu_item` (
  `item_id` INT NOT NULL AUTO_INCREMENT,
  `restaurant_id` INT NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `price` INT NOT NULL,
  PRIMARY KEY (`item_id`),
  FOREIGN KEY (`restaurant_id`) REFERENCES `restaurant`(`restaurant_id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `order`
--

CREATE TABLE `order` (
  `order_id` INT NOT NULL AUTO_INCREMENT,
  `customer_id` INT NOT NULL,
  `restaurant_id` INT NOT NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`order_id`),
  FOREIGN KEY (`customer_id`) REFERENCES `customer`(`customer_id`),
  FOREIGN KEY (`restaurant_id`) REFERENCES `restaurant`(`restaurant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `order_item`
--

CREATE TABLE `order_item` (
  `order_id` INT NOT NULL,
  `item_id` INT NOT NULL,
  `quantity` INT NOT NULL,
  `price` INT NOT NULL,
  PRIMARY KEY (`order_id`, `item_id`),
  FOREIGN KEY (`order_id`) REFERENCES `order`(`order_id`) ON DELETE CASCADE,
  FOREIGN KEY (`item_id`) REFERENCES `menu_item`(`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `order_status_history`
--

CREATE TABLE `order_status_history` (
  `order_id` INT NOT NULL,
  `status` ENUM('created', 'accepted', 'canceled', 'completed') NOT NULL,
  `timestamp` DATETIME NOT NULL,
  PRIMARY KEY (`order_id`, `status`),
  FOREIGN KEY (`order_id`) REFERENCES `order`(`order_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
-- 匯入資料表內容 `customer`
--

INSERT INTO `customer` (`customer_id`, `name`, `phone`) VALUES
(1, '林小明', '0912345678'),
(2, '張大華', '0922333444'),
(3, '李佳蓉', '0955666777'),
(4, '王美麗', '0987654321'),
(5, '陳志明', '0933221100');

-- --------------------------------------------------------
-- 匯入資料表內容 `restaurant`
--

INSERT INTO `restaurant` (`restaurant_id`, `name`, `phone`, `location`) VALUES
(1, '美味便當', '0223456789', '台北市中正區忠孝東路一段'),
(2, '義大利麵屋', '0233344556', '新北市板橋區文化路二段'),
(3, '韓式炸雞', '0277788899', '桃園市中壢區中正路'),
(4, '日式拉麵館', '0266677788', '台中市西屯區逢甲路'),
(5, '泰式料理屋', '0244455566', '高雄市苓雅區五福一路');

-- --------------------------------------------------------
-- 匯入資料表內容 `menu_item`
--

INSERT INTO `menu_item` (`item_id`, `restaurant_id`, `name`, `price`) VALUES
(1, 1, '雞腿便當', 120),
(2, 1, '排骨便當', 110),
(3, 2, '青醬義大利麵', 160),
(4, 3, '原味炸雞', 150),
(5, 4, '豚骨拉麵', 180);

-- --------------------------------------------------------
-- 匯入資料表內容 `order`
--

INSERT INTO `order` (`order_id`, `customer_id`, `restaurant_id`, `created_at`) VALUES
(1, 1, 1, '2025-06-11 10:30:00'),
(2, 2, 2, '2025-06-11 11:00:00'),
(3, 3, 3, '2025-06-11 11:15:00'),
(4, 4, 4, '2025-06-11 12:00:00'),
(5, 5, 5, '2025-06-11 12:30:00');

-- --------------------------------------------------------
-- 匯入資料表內容 `order_item`
--

INSERT INTO `order_item` (`order_id`, `item_id`, `quantity`, `price`) VALUES
(1, 1, 1, 120),
(2, 3, 2, 160),
(3, 4, 1, 150),
(4, 5, 1, 180),
(5, 2, 1, 110);

-- --------------------------------------------------------
-- 匯入資料表內容 `order_status_history`
--

INSERT INTO `order_status_history` (`order_id`, `status`, `timestamp`) VALUES
(1, 'created', '2025-06-11 10:30:00'),
(1, 'accepted', '2025-06-11 10:32:00'),
(2, 'created', '2025-06-11 11:00:00'),
(2, 'canceled', '2025-06-11 11:05:00'),
(3, 'created', '2025-06-11 11:15:00');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
 /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
 /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
