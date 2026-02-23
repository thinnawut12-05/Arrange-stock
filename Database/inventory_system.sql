-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 12, 2026 at 07:06 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `inventory_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `cus_name`
--

CREATE TABLE `cus_name` (
  `Cus_id` varchar(5) NOT NULL,
  `Cus_name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cus_name`
--

INSERT INTO `cus_name` (`Cus_id`, `Cus_name`) VALUES
('C001', 'บริษัท เอบีซี จำกัด'),
('C002', 'นายสมชาย ใจดี'),
('C003', 'บริษัท เจย์โจ จำกัด'),
('C004', 'บริษัท เคย์ จำกัด');

-- --------------------------------------------------------

--
-- Table structure for table `d_order`
--

CREATE TABLE `d_order` (
  `Order_no` int(11) NOT NULL,
  `Goods_id` varchar(10) NOT NULL,
  `Ord_date` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  `Fin_date` datetime DEFAULT NULL,
  `Amount` decimal(10,2) DEFAULT NULL,
  `COST_UNIT` decimal(8,2) DEFAULT NULL,
  `TOT_PRC` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `goods_name`
--

CREATE TABLE `goods_name` (
  `Goods_id` varchar(10) NOT NULL,
  `Goods_name` varchar(30) NOT NULL,
  `cost_unit` decimal(8,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `goods_name`
--

INSERT INTO `goods_name` (`Goods_id`, `Goods_name`, `cost_unit`) VALUES
('G001', 'สินค้า A', 100.00),
('G002', 'สินค้า B', 250.00),
('G003', 'สินค้าD', 300.00),
('G004', 'สินค้าC', 270.00);

-- --------------------------------------------------------

--
-- Table structure for table `h_order`
--

CREATE TABLE `h_order` (
  `Order_no` int(11) NOT NULL,
  `Cus_id` varchar(5) NOT NULL,
  `Order_Date` datetime NOT NULL DEFAULT '2000-01-01 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `m_order`
--

CREATE TABLE `m_order` (
  `Cus_id` varchar(5) NOT NULL,
  `Goods_id` varchar(10) NOT NULL,
  `Doc_date` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  `Ord_date` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  `Fin_date` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  `Sys_date` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  `Amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cost_tot` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `m_order`
--

INSERT INTO `m_order` (`Cus_id`, `Goods_id`, `Doc_date`, `Ord_date`, `Fin_date`, `Sys_date`, `Amount`, `cost_tot`) VALUES
('C001', 'G001', '2026-02-12 00:00:00', '2026-02-12 00:00:00', '2026-02-14 00:00:00', '2026-02-12 08:53:56', 1000.00, 100000.00),
('C002', 'G002', '2026-02-12 00:00:00', '2026-02-12 00:00:00', '2026-02-14 00:00:00', '2026-02-12 10:41:10', 100.00, 25000.00),
('C004', 'G004', '2026-02-12 00:00:00', '2026-02-12 00:00:00', '2026-02-14 00:00:00', '2026-02-12 13:01:18', 100.00, 27000.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cus_name`
--
ALTER TABLE `cus_name`
  ADD PRIMARY KEY (`Cus_id`);

--
-- Indexes for table `d_order`
--
ALTER TABLE `d_order`
  ADD PRIMARY KEY (`Order_no`,`Goods_id`),
  ADD KEY `Goods_id` (`Goods_id`);

--
-- Indexes for table `goods_name`
--
ALTER TABLE `goods_name`
  ADD PRIMARY KEY (`Goods_id`);

--
-- Indexes for table `h_order`
--
ALTER TABLE `h_order`
  ADD PRIMARY KEY (`Order_no`),
  ADD KEY `Cus_id` (`Cus_id`);

--
-- Indexes for table `m_order`
--
ALTER TABLE `m_order`
  ADD PRIMARY KEY (`Cus_id`,`Goods_id`,`Doc_date`,`Ord_date`,`Fin_date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `h_order`
--
ALTER TABLE `h_order`
  MODIFY `Order_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `d_order`
--
ALTER TABLE `d_order`
  ADD CONSTRAINT `d_order_ibfk_1` FOREIGN KEY (`Order_no`) REFERENCES `h_order` (`Order_no`) ON DELETE CASCADE,
  ADD CONSTRAINT `d_order_ibfk_2` FOREIGN KEY (`Goods_id`) REFERENCES `goods_name` (`Goods_id`);

--
-- Constraints for table `h_order`
--
ALTER TABLE `h_order`
  ADD CONSTRAINT `h_order_ibfk_1` FOREIGN KEY (`Cus_id`) REFERENCES `cus_name` (`Cus_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
