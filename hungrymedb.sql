-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 21, 2024 at 01:35 PM
-- Server version: 8.2.0
-- PHP Version: 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hungrymedb`
--

-- --------------------------------------------------------

--
-- Table structure for table `addon`
--

DROP TABLE IF EXISTS `addon`;
CREATE TABLE IF NOT EXISTS `addon` (
  `AddonID` int NOT NULL,
  `AddonName` varchar(255) DEFAULT NULL,
  `Price` decimal(10,2) DEFAULT NULL,
  `AdminID` int DEFAULT NULL,
  PRIMARY KEY (`AddonID`),
  KEY `AdminID` (`AdminID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `addon`
--

INSERT INTO `addon` (`AddonID`, `AddonName`, `Price`, `AdminID`) VALUES
(1, 'Extras', 230.00, 2);

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `AdminID` int NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `password` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `role` varchar(50) NOT NULL,
  PRIMARY KEY (`AdminID`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`AdminID`, `name`, `password`, `role`) VALUES
(1, 'Admin1', '789', 'Admin'),
(2, 'Admin2', '123', 'Admin'),
(3, 'Admin3', '1234', 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
CREATE TABLE IF NOT EXISTS `cart` (
  `CartID` int NOT NULL AUTO_INCREMENT,
  `CusID` int DEFAULT NULL,
  PRIMARY KEY (`CartID`),
  KEY `CusID` (`CusID`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`CartID`, `CusID`) VALUES
(1, 1),
(27, 4),
(28, 3),
(29, 4),
(30, 6),
(31, 7),
(32, 8),
(33, 9);

-- --------------------------------------------------------

--
-- Table structure for table `cartmenuitem`
--

DROP TABLE IF EXISTS `cartmenuitem`;
CREATE TABLE IF NOT EXISTS `cartmenuitem` (
  `CartID` int NOT NULL,
  `MenuItemID` int NOT NULL,
  `Quantity` int DEFAULT '1',
  PRIMARY KEY (`CartID`,`MenuItemID`),
  KEY `MenuItemID` (`MenuItemID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cartmenuitem`
--

INSERT INTO `cartmenuitem` (`CartID`, `MenuItemID`, `Quantity`) VALUES
(21, 8, 1),
(21, 9, 1),
(31, 6, 2),
(1, 15, 1),
(1, 16, 2),
(28, 15, 1),
(28, 9, 1),
(33, 29, 1),
(33, 31, 1);

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

DROP TABLE IF EXISTS `customer`;
CREATE TABLE IF NOT EXISTS `customer` (
  `CusID` int NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `password` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `role` enum('user') NOT NULL,
  PRIMARY KEY (`CusID`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`CusID`, `name`, `password`, `role`) VALUES
(1, 'dil', '789', 'user'),
(2, 'Janaka', '789', 'user'),
(3, 'Vikasitha', '123', 'user'),
(7, 'sarath', '789', 'user'),
(6, 'nipuni', '123', 'user'),
(9, 'Navindu', '123', 'user');

-- --------------------------------------------------------

--
-- Table structure for table `deliveryboy`
--

DROP TABLE IF EXISTS `deliveryboy`;
CREATE TABLE IF NOT EXISTS `deliveryboy` (
  `DeliveryBoyID` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `role` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`DeliveryBoyID`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `deliveryboy`
--

INSERT INTO `deliveryboy` (`DeliveryBoyID`, `name`, `password`, `role`) VALUES
(1, 'Sasindu', 'DEL001', 'delivery_boy'),
(2, 'Tharusha', 'DEL002', 'delivery_boy');

-- --------------------------------------------------------

--
-- Table structure for table `menuitem`
--

DROP TABLE IF EXISTS `menuitem`;
CREATE TABLE IF NOT EXISTS `menuitem` (
  `MenuItemID` int NOT NULL AUTO_INCREMENT,
  `Description` varchar(500) NOT NULL,
  `Price` double NOT NULL,
  `MenuName` varchar(255) NOT NULL,
  `ImagePath` varchar(200) NOT NULL,
  `District` varchar(255) NOT NULL,
  `Location` varchar(200) NOT NULL,
  `ShopID` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`MenuItemID`),
  KEY `fk_shop` (`ShopID`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `menuitem`
--

INSERT INTO `menuitem` (`MenuItemID`, `Description`, `Price`, `MenuName`, `ImagePath`, `District`, `Location`, `ShopID`) VALUES
(1, 'mix', 1000, 'Rice', 'uploads/mixmixrice.jpeg', '', '', NULL),
(5, 'Chicken Full', 500, 'Kottu', 'uploads/java.jpeg', '', '', NULL),
(7, 'egg', 550, 'Rice', 'uploads/rice.jpeg', '', '', NULL),
(6, 'Sea food', 890, 'Rice', 'uploads/rice.jpeg', 'Galle', '', 'SHP017'),
(29, 'Hela chicken rice', 700, 'Rice', 'uploads/instant-pot-chicken-and-rice.jpg', 'Galle', 'Labuduwa', 'SHP020'),
(9, 'Pop Cone rice', 1400, 'Rice', 'uploads/noodle.jpg', 'Galle', 'Dewata', 'SHP017'),
(14, 'Thandoori ', 1500, 'Pizza', 'uploads/pitza2.jpeg', 'Galle', 'Dewata', 'SHP017'),
(15, 'Cheese kottu', 990, 'Kottu', 'uploads/Seafood-Cheese-Kottu.jpg', 'Matara', 'Matara', 'SHP016'),
(16, 'Chicken kottu', 800, 'Kottu', 'uploads/Chicken-kottu.jpg', 'Matara', 'Matara', 'SHP016'),
(17, 'Masala Kottu', 1200, 'Kottu', 'uploads/kottu-masala.jpg', 'Galle', 'Dewata', 'SHP017'),
(18, 'Egg noodle', 520, 'Noodles', 'uploads/Noodles-with-chilli-.jpg', 'Galle', 'Dewata', 'SHP017'),
(19, 'Dum Rice', 1140, 'Rice', 'uploads/dum-recipe-1a.jpg', 'Matara', 'Matara', 'SHP016'),
(20, 'Vegetable Noodle ', 450, 'Noodles', 'uploads/hq720.jpg', 'Matara', 'Matara', 'SHP016'),
(21, 'mix pizza', 2400, 'Pizza', 'uploads/пицца1.jpg', 'Matara', 'Matara', 'SHP016'),
(22, 'Dolpin Kottu', 2100, 'Kottu', 'uploads/3534789.jpg', 'Colombo', 'kotte', 'SHP018'),
(23, 'Pot Rice', 1800, 'Rice', 'uploads/maxresdefault.jpg', 'Colombo', 'kotte', 'SHP018'),
(25, 'LargePizza', 2420, 'Pizza', 'uploads/Mexican-Beef-Delight.jpg', 'Colombo', 'kotte', 'SHP018'),
(26, 'Sweet noodle', 620, 'Noodles', 'uploads/vegetable-noodles.jpg', 'Colombo', 'kotte', 'SHP018'),
(27, 'Chilly rice', 780, 'Rice', 'uploads/Chilli Garlic.jpg', 'Kandy', 'Kandy', 'SHP019'),
(28, 'Chilly Kottu', 880, 'Kottu', 'uploads/chili-chicken-kottu.jpg', 'Kandy', 'Kandy', 'SHP019'),
(30, 'Hela chicken kottu', 720, 'Kottu', 'uploads/Chicken-Kottu-Ro.jpg', 'Galle', 'Labuduwa', 'SHP020'),
(31, 'Hela biriyani', 750, 'Rice', 'uploads/Chicken-Biryani-Recipe.jpg', 'Galle', 'Labuduwa', 'SHP020'),
(32, 'Hela pizza', 1100, 'Pizza', 'uploads/BBQ-Chicken-Pizza-4-500x500.jpg', 'Galle', 'Labuduwa', 'SHP020'),
(33, 'Hela noodle', 420, 'Noodles', 'uploads/chicken-noodles-500x375.jpg', 'Galle', 'Labuduwa', 'SHP020');

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

DROP TABLE IF EXISTS `order`;
CREATE TABLE IF NOT EXISTS `order` (
  `OrderID` int NOT NULL AUTO_INCREMENT,
  `OrderDate` date NOT NULL,
  `OrderStatus` varchar(100) NOT NULL,
  `TotAmount` double(10,0) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Address` varchar(250) NOT NULL,
  `PhoneNo` int NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Landmarks` varchar(200) NOT NULL,
  `PaymentMethod` varchar(100) NOT NULL,
  `CartID` int DEFAULT NULL,
  `DeliveryBoyID` int DEFAULT NULL,
  PRIMARY KEY (`OrderID`),
  KEY `fk_cart` (`CartID`),
  KEY `fk_deliveryboy` (`DeliveryBoyID`)
) ENGINE=MyISAM AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`OrderID`, `OrderDate`, `OrderStatus`, `TotAmount`, `Name`, `Address`, `PhoneNo`, `Email`, `Landmarks`, `PaymentMethod`, `CartID`, `DeliveryBoyID`) VALUES
(41, '2024-08-09', 'delivered', 3400, 'anudi', 'jafna', 772546320, 'dihanianudiranathunga@gmail.com', 'town', 'cod', NULL, NULL),
(45, '2024-08-09', 'delivered', 4180, 'anudi', 'jaffna', 712603060, 'scaj12345@gmail.com', '', 'cod', NULL, NULL),
(46, '2024-08-09', 'On the Way', 4180, 'anudi', 'weligama', 712603060, 'dihanianudiranathunga@gmail.com', '', 'cod', NULL, NULL),
(47, '2024-08-09', 'On the Way', 4180, 'anudi', 'colombo', 769773393, 'hello@gmail.com', '', 'cod', NULL, NULL),
(48, '2024-08-12', 'delivered', 2700, 'NIPUNI', 'megalle', 701424978, 'nipu@gmail.com', 'RailwayGate', 'cod', NULL, 1),
(49, '2024-09-11', 'Pending', 1998, 'nf', 'mehshss', 701424978, 'amashagurusinghe2003@gmail.com', 'Police', 'cod', 1, NULL),
(50, '2024-09-18', 'delivered', 1400, 'Nipunj', 'hj', 7485963, 'amashagurusinghe2003@gmail.com', 'Nera the Rail gate', 'cod', 1, NULL),
(51, '2024-09-18', 'delivered', 5090, 'zzz', 'zzzz', 111111111, 'zzz@gmail.com', 'zzz', 'cod', 1, 1),
(52, '2024-09-20', 'delivered', 12580, 'Janaka', 'hhsyys', 701424978, 'shashimalmadhuwantha12@gmail.com', 'police', 'cod', 1, 1),
(53, '2024-09-20', 'delivered', 890, 'hgdrfcbn', 'vgvggvgv', 711424978, 'mn@gmail.com', 'police', 'cod', 31, 1),
(54, '2024-09-20', 'On the Way', 890, 'vvv', 'vvv', 255555555, 'vv@gmail.com', 'vv', 'cod', 28, 1),
(55, '2024-09-20', 'On the Way', 1000, 'cc', 'cc', 232323232, 'cc@gmail.com', 'cc', 'cod', 28, 1),
(56, '2024-09-20', 'Pending', 1000, 'bbb', 'bbb', 1234563210, 'bb@gmail.com', 'bb', 'cod', 28, NULL),
(57, '2024-09-20', 'Pending', 1600, 'ooo', 'ooo', 123123123, 'oo@gmail.com', 'oo', 'cod', 28, NULL),
(58, '2024-09-21', 'On the Way', 1450, 'navi', 'meepawala', 774585230, 'navi@gmail.com', 'meepawala post office', 'cod', 33, 2);

-- --------------------------------------------------------

--
-- Table structure for table `orderitem`
--

DROP TABLE IF EXISTS `orderitem`;
CREATE TABLE IF NOT EXISTS `orderitem` (
  `OrderItemID` int NOT NULL AUTO_INCREMENT,
  `OrderID` int DEFAULT NULL,
  `MenuItemID` int DEFAULT NULL,
  `AddonID` int DEFAULT NULL,
  PRIMARY KEY (`OrderItemID`),
  KEY `OrderID` (`OrderID`),
  KEY `MenuItemID` (`MenuItemID`),
  KEY `AddonID` (`AddonID`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orderitem`
--

INSERT INTO `orderitem` (`OrderItemID`, `OrderID`, `MenuItemID`, `AddonID`) VALUES
(2, 49, 11, NULL),
(4, 51, 6, 1),
(7, 52, 6, 1),
(8, 52, 15, 1),
(9, 52, 15, 1),
(10, 53, 6, 1),
(11, 54, 6, 1),
(12, 55, 15, 1),
(13, 55, 15, 1),
(14, 56, 15, 1),
(15, 56, 16, 1),
(16, 57, 9, 1),
(17, 57, 15, 1),
(18, 58, 29, 1),
(19, 58, 31, 1);

-- --------------------------------------------------------

--
-- Table structure for table `ordershop`
--

DROP TABLE IF EXISTS `ordershop`;
CREATE TABLE IF NOT EXISTS `ordershop` (
  `OrderID` int NOT NULL,
  `ShopID` varchar(100) NOT NULL,
  PRIMARY KEY (`OrderID`,`ShopID`),
  KEY `ShopID` (`ShopID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ordershop`
--

INSERT INTO `ordershop` (`OrderID`, `ShopID`) VALUES
(48, 'SHP017'),
(49, 'SHP017'),
(50, 'SHP017'),
(51, 'SHP017,SHP017'),
(52, 'SHP017,SHP017,SHP016,SHP016'),
(53, 'SHP017'),
(54, 'SHP017'),
(55, 'SHP016,SHP016'),
(56, 'SHP016,SHP016'),
(57, 'SHP017,SHP016'),
(58, 'SHP020,SHP020');

-- --------------------------------------------------------

--
-- Table structure for table `shop`
--

DROP TABLE IF EXISTS `shop`;
CREATE TABLE IF NOT EXISTS `shop` (
  `ShopID` varchar(100) NOT NULL,
  `ShopEmail` varchar(200) NOT NULL,
  `ShopName` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `role` varchar(100) NOT NULL,
  PRIMARY KEY (`ShopID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `shop`
--

INSERT INTO `shop` (`ShopID`, `ShopEmail`, `ShopName`, `password`, `role`) VALUES
('SHP021', 'madeena@gmail.com', 'Madeena', 'SHP021', 'shop_owner'),
('SHP020', 'helabojun@gmail.com', 'HelaBojun', 'SHP020', 'shop_owner'),
('SHP019', 'rasamusu@gmail.com', 'RasaMusu', 'SHP019', 'shop_owner'),
('SHP018', 'dominoz@gmail.com', 'Dominoz', 'SHP018', 'shop_owner'),
('SHP015', 'kixi@gmail.com', 'Kixi', 'SHP015', 'shop_owner'),
('SHP016', 'ameena@gmail.com', 'Ameena', 'SHP016', 'shop_owner'),
('SHP017', 'pizzahut@gmail.com', 'Pizza hut', 'SHP017', 'shop_owner');

-- --------------------------------------------------------

--
-- Table structure for table `shopphone`
--

DROP TABLE IF EXISTS `shopphone`;
CREATE TABLE IF NOT EXISTS `shopphone` (
  `ShopID` varchar(200) NOT NULL,
  `ShopPhone` int NOT NULL,
  PRIMARY KEY (`ShopID`,`ShopPhone`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `shopphone`
--

INSERT INTO `shopphone` (`ShopID`, `ShopPhone`) VALUES
('SHP001', 769773393),
('SHP002', 915628313),
('SHP003', 778952103),
('SHP004', 778542369),
('SHP005', 2147483647),
('SHP006', 724446030),
('SHP007', 0),
('SHP008', 0),
('SHP009', 0),
('SHP010', 0),
('SHP011', 914586921),
('SHP012', 777582667),
('SHP013', 777582667),
('SHP014', 915222222),
('SHP015', 915222222),
('SHP016', 769777393),
('SHP017', 914542360),
('SHP018', 115896570),
('SHP019', 118579630),
('SHP020', 789568996),
('SHP021', 769854712);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
