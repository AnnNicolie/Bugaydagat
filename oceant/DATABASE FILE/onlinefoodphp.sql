-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 29, 2025 at 03:07 AM
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
-- Database: `onlinefoodphp`
--

-- --------------------------------------------------------

--
-- Table structure for table `users_orders`
--

CREATE TABLE `users_orders` (
  `o_id` int(222) NOT NULL,
  `u_id` int(222) NOT NULL,
  `title` varchar(222) NOT NULL,
  `quantity` int(222) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` varchar(222) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `payment_method` varchar(50) NOT NULL DEFAULT 'COD',
  `payment_reference` varchar(100) DEFAULT NULL,
  `shipping_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `delivery_location` varchar(100) DEFAULT NULL,
  `barangay` varchar(100) DEFAULT NULL,
  `grand_total` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users_orders`
--

INSERT INTO `users_orders` (`o_id`, `u_id`, `title`, `quantity`, `price`, `status`, `date`, `payment_method`, `payment_reference`, `shipping_fee`, `delivery_location`, `barangay`, `grand_total`) VALUES
(30, 10, 'Bangus Boneless', 1, 250.00, NULL, '2025-06-16 08:39:39', 'COD', '', 20.00, 'Guimbal', 'Brgy. Baras Guimbal, Iloilo', 270.00),
(40, 10, 'Lato', 2, 120.00, 'pending', '2025-06-16 10:15:32', 'COD', '', 100.00, 'Banate', 'Brgy. Baras Guimbal, Iloilo', 1290.00),
(41, 10, 'Baby-squid', 3, 250.00, 'pending', '2025-06-16 10:15:32', 'COD', '', 100.00, 'Banate', 'Brgy. Baras Guimbal, Iloilo', 1290.00),
(42, 10, 'Bangus', 1, 200.00, 'pending', '2025-06-16 10:15:32', 'COD', '', 100.00, 'Banate', 'Brgy. Baras Guimbal, Iloilo', 1290.00),
(43, 10, 'Bangus Boneless', 2, 250.00, 'pending', '2025-06-16 10:16:38', 'COD', '', 100.00, 'Banate', 'Brgy. Baras Guimbal, Iloilo', 1500.00),
(44, 10, 'Blue Marlin', 2, 450.00, 'pending', '2025-06-16 10:16:38', 'COD', '', 100.00, 'Banate', 'Brgy. Baras Guimbal, Iloilo', 1500.00),
(45, 10, 'Lato', 1, 120.00, 'pending', '2025-06-16 10:25:43', 'COD', '', 100.00, 'Banate', 'Brgy. Baras Guimbal, Iloilo', 220.00),
(46, 10, 'Oyster', 2, 150.00, 'pending', '2025-06-16 15:24:48', 'COD', '', 100.00, 'Banate', 'Brgy. Baras Guimbal, Iloilo', 620.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users_orders`
--
ALTER TABLE `users_orders`
  ADD PRIMARY KEY (`o_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users_orders`
--
ALTER TABLE `users_orders`
  MODIFY `o_id` int(222) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
