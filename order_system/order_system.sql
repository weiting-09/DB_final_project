-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- 主機： localhost
-- 產生時間： 2025 年 06 月 11 日 20:29
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

--
-- 傾印資料表的資料 `cart_items`
--

INSERT INTO `cart_items` (`customer_id`, `item_id`, `quantity`) VALUES
(5, 1, 1),
(5, 3, 1);

-- --------------------------------------------------------

--
-- 資料表結構 `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 傾印資料表的資料 `customer`
--

INSERT INTO `customer` (`customer_id`, `name`, `phone`, `password`) VALUES
(1, '林小明', '0912345678', '0000'),
(2, '張大華', '0922333444', '0000'),
(3, '李佳蓉', '0955666777', '0000'),
(4, '王美麗', '0987654321', '0000'),
(5, '陳志明', '0933221100', '0000'),
(6, '呂小天', '0999999999', '0000');

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
(1, 1, '雞腿便當', 125),
(2, 1, '排骨便當', 100),
(3, 2, '青醬義大利麵', 160),
(4, 3, '原味炸雞', 150),
(5, 4, '豚骨拉麵', 180),
(6, 2, '白醬義大利麵', 150);

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
(15, 1, 1, '2025-06-10 03:55:29'),
(16, 1, 2, '2025-06-10 03:55:29'),
(17, 1, 4, '2025-06-10 03:55:29'),
(18, 1, 1, '2025-06-10 04:00:16'),
(19, 1, 2, '2025-06-10 04:00:16'),
(20, 1, 4, '2025-06-10 04:00:16'),
(21, 1, 2, '2025-06-10 04:07:52'),
(22, 1, 1, '2025-06-10 04:08:20'),
(23, 1, 1, '2025-06-10 04:14:09');

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
(15, 1, 1, 125),
(16, 3, 1, 160),
(17, 5, 1, 180),
(18, 1, 1, 125),
(19, 3, 1, 160),
(20, 5, 1, 180),
(21, 3, 1, 160),
(22, 2, 3, 100),
(23, 1, 3, 125);

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
(15, 'created', '2025-06-10 03:55:29'),
(15, 'accepted', '2025-06-10 03:56:20'),
(15, 'completed', '2025-06-10 03:56:23'),
(16, 'created', '2025-06-10 03:55:29'),
(17, 'created', '2025-06-10 03:55:29'),
(18, 'created', '2025-06-10 04:00:16'),
(19, 'created', '2025-06-10 04:00:16'),
(20, 'created', '2025-06-10 04:00:16'),
(21, 'created', '2025-06-10 04:07:52'),
(22, 'created', '2025-06-10 04:08:20'),
(22, 'accepted', '2025-06-10 04:08:36'),
(22, 'completed', '2025-06-10 04:08:42'),
(23, 'created', '2025-06-10 04:14:09'),
(23, 'accepted', '2025-06-10 04:19:43');

-- --------------------------------------------------------

--
-- 資料表結構 `restaurant`
--

CREATE TABLE `restaurant` (
  `restaurant_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `password` varchar(255) NOT NULL,
  `location` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 傾印資料表的資料 `restaurant`
--

INSERT INTO `restaurant` (`restaurant_id`, `name`, `phone`, `password`, `location`) VALUES
(1, '美味便當', '0223456789', '0000', '台北市中正區忠孝東路二段'),
(2, '義大利麵屋', '0233344556', '0000', '新北市板橋區文化路五段'),
(3, '韓式炸雞', '0277788899', '0000', '桃園市中壢區中正路'),
(4, '日式拉麵館', '0266677788', '0000', '台中市西屯區逢甲路'),
(5, '泰式料理屋', '0244455566', '0000', '高雄市苓雅區五福一路');

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
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `menu_item`
--
ALTER TABLE `menu_item`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `order`
--
ALTER TABLE `order`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `restaurant`
--
ALTER TABLE `restaurant`
  MODIFY `restaurant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
