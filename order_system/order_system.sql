-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- 主機： localhost
-- 產生時間： 2025 年 06 月 11 日 14:51
-- 伺服器版本： 8.0.17
-- PHP 版本： 7.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `order_system`
--

-- --------------------------------------------------------

--
-- 資料表結構 `cart_items`
--

CREATE TABLE `cart_items` (
  `customer_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `phone` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 傾印資料表的資料 `customer`
--

INSERT INTO `customer` (`customer_id`, `name`, `phone`) VALUES
(1, '林小明', '0912345678'),
(2, '張大華', '0922333444'),
(3, '李佳蓉', '0955666777'),
(4, '王美麗', '0987654321'),
(5, '陳志明', '0933221100');

-- --------------------------------------------------------

--
-- 資料表結構 `menu_item`
--

CREATE TABLE `menu_item` (
  `item_id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 傾印資料表的資料 `menu_item`
--

INSERT INTO `menu_item` (`item_id`, `restaurant_id`, `name`, `price`) VALUES
(1, 1, '雞腿便當', 120),
(2, 1, '排骨便當', 110),
(3, 2, '青醬義大利麵', 160),
(4, 3, '原味炸雞', 150),
(5, 4, '豚骨拉麵', 180);

-- --------------------------------------------------------

--
-- 資料表結構 `order`
--

CREATE TABLE `order` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 傾印資料表的資料 `order`
--

INSERT INTO `order` (`order_id`, `customer_id`, `restaurant_id`, `created_at`) VALUES
(1, 1, 1, '2025-06-11 10:30:00'),
(2, 2, 2, '2025-06-11 11:00:00'),
(3, 3, 3, '2025-06-11 11:15:00'),
(4, 4, 4, '2025-06-11 12:00:00'),
(5, 5, 5, '2025-06-11 12:30:00'),
(6, 1, 1, '2025-06-11 22:32:57'),
(7, 1, 3, '2025-06-11 22:32:57');

-- --------------------------------------------------------

--
-- 資料表結構 `order_item`
--

CREATE TABLE `order_item` (
  `order_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 傾印資料表的資料 `order_item`
--

INSERT INTO `order_item` (`order_id`, `item_id`, `quantity`, `price`) VALUES
(1, 1, 1, 120),
(2, 3, 2, 160),
(3, 4, 1, 150),
(4, 5, 1, 180),
(5, 2, 1, 110),
(6, 1, 1, 120);

-- --------------------------------------------------------

--
-- 資料表結構 `order_status_history`
--

CREATE TABLE `order_status_history` (
  `order_id` int(11) NOT NULL,
  `status` enum('created','accepted','canceled','completed') NOT NULL,
  `timestamp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 傾印資料表的資料 `order_status_history`
--

INSERT INTO `order_status_history` (`order_id`, `status`, `timestamp`) VALUES
(1, 'created', '2025-06-11 10:30:00'),
(1, 'accepted', '2025-06-11 10:32:00'),
(2, 'created', '2025-06-11 11:00:00'),
(2, 'canceled', '2025-06-11 11:05:00'),
(3, 'created', '2025-06-11 11:15:00');

-- --------------------------------------------------------

--
-- 資料表結構 `restaurant`
--

CREATE TABLE `restaurant` (
  `restaurant_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `location` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 傾印資料表的資料 `restaurant`
--

INSERT INTO `restaurant` (`restaurant_id`, `name`, `phone`, `location`) VALUES
(1, '美味便當', '0223456789', '台北市中正區忠孝東路一段'),
(2, '義大利麵屋', '0233344556', '新北市板橋區文化路二段'),
(3, '韓式炸雞', '0277788899', '桃園市中壢區中正路'),
(4, '日式拉麵館', '0266677788', '台中市西屯區逢甲路'),
(5, '泰式料理屋', '0244455566', '高雄市苓雅區五福一路');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`customer_id`,`item_id`),
  ADD KEY `item_id` (`item_id`);

--
-- 資料表索引 `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`);

--
-- 資料表索引 `menu_item`
--
ALTER TABLE `menu_item`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `restaurant_id` (`restaurant_id`);

--
-- 資料表索引 `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `restaurant_id` (`restaurant_id`);

--
-- 資料表索引 `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`order_id`,`item_id`),
  ADD KEY `item_id` (`item_id`);

--
-- 資料表索引 `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD PRIMARY KEY (`order_id`,`status`);

--
-- 資料表索引 `restaurant`
--
ALTER TABLE `restaurant`
  ADD PRIMARY KEY (`restaurant_id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `menu_item`
--
ALTER TABLE `menu_item`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `order`
--
ALTER TABLE `order`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `restaurant`
--
ALTER TABLE `restaurant`
  MODIFY `restaurant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- 已傾印資料表的限制式
--

--
-- 資料表的限制式 `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`),
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `menu_item` (`item_id`);

--
-- 資料表的限制式 `menu_item`
--
ALTER TABLE `menu_item`
  ADD CONSTRAINT `menu_item_ibfk_1` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurant` (`restaurant_id`) ON DELETE CASCADE;

--
-- 資料表的限制式 `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `order_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`),
  ADD CONSTRAINT `order_ibfk_2` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurant` (`restaurant_id`);

--
-- 資料表的限制式 `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `order_item_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_item_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `menu_item` (`item_id`);

--
-- 資料表的限制式 `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD CONSTRAINT `order_status_history_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
