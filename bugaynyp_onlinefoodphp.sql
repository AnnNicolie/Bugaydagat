-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 11, 2026 at 09:23 AM
-- Server version: 11.4.10-MariaDB-cll-lve
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bugaynyp_onlinefoodphp`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `adm_id` int(222) NOT NULL,
  `username` varchar(222) NOT NULL,
  `password` varchar(222) NOT NULL,
  `email` varchar(222) NOT NULL,
  `code` varchar(222) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`adm_id`, `username`, `password`, `email`, `code`, `date`) VALUES
(1, 'admin', 'CAC29D7A34687EB14B37068EE4708E7B', 'admin@mail.com', '', '2022-05-27 13:21:52');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `rs_id` int(222) NOT NULL,
  `c_id` int(222) NOT NULL,
  `title` varchar(222) NOT NULL,
  `email` varchar(222) NOT NULL,
  `phone` varchar(222) NOT NULL,
  `url` varchar(222) NOT NULL,
  `o_hr` varchar(222) NOT NULL,
  `c_hr` varchar(222) NOT NULL,
  `o_days` varchar(222) NOT NULL,
  `address` text NOT NULL,
  `image` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`rs_id`, `c_id`, `title`, `email`, `phone`, `url`, `o_hr`, `c_hr`, `o_days`, `address`, `image`, `date`) VALUES
(1, 1, 'FISH', 'fisherfolks@gmail.com', '09300925151', 'www.fisherfolksguimbal.com', '6am', '9pm', 'mon-sat', ' Guimbal, Iloilo ', '680828fac5611.jpg', '2025-04-22 23:40:42'),
(2, 3, 'SEAWEEDS', 'fisherfolks@gmail.com', '09300925151', 'www.fisherfolksguimbal.com', '6am', '9pm', 'mon-sat', ' Guimbal, Iloilo ', '680828bd82528.jpg', '2025-04-22 23:39:41'),
(3, 2, 'SHELLFISH', 'fisherfolks@gmail.com', '09300925151', 'www.fisherfolksguimbal.com', '6am', '9pm', 'mon-sat', ' Guimbal, Iloilo ', '68082895679cc.jpg', '2025-04-22 23:39:01'),
(4, 4, 'PUSIT', 'fisherfolks@gmail.com', '09300925151', 'www.fisherfolksguimbal.com', '6am', '9pm', 'mon-sat', ' Guimbal, Iloilo ', '680828550284f.jpg', '2025-04-22 23:37:57'),
(5, 7, 'DRIED FISH', 'guimbal@gmail.com', '09484644390', 'guimbalfishers@gmail.com', '6am', '9pm', 'mon-sat', ' Guimbal, Iloilo ', '68e7bb67ba353.jpg', '2025-10-09 13:40:55');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_drivers`
--

CREATE TABLE `delivery_drivers` (
  `driver_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `plate_number` varchar(50) NOT NULL,
  `vehicle_type` varchar(50) NOT NULL,
  `license_number` varchar(100) NOT NULL,
  `profile_pic` varchar(500) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_available` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `delivery_drivers`
--

INSERT INTO `delivery_drivers` (`driver_id`, `full_name`, `username`, `email`, `password`, `phone`, `address`, `plate_number`, `vehicle_type`, `license_number`, `profile_pic`, `status`, `created_at`, `is_available`) VALUES
(8, 'Patrick Eigo', 'patty', 'patrick@gmail.com', '8e3a8d3e644e608d25ec40162988a137', '09092345456', 'Baras, Guimbal Iloilo', '12-345A ', 'Truck', '1234-5678', 'uploads/driver_images/driver_1761310926_68fb78ce42e72.jpg', 0, '2025-10-24 13:02:06', 1),
(9, 'Orlan Gayares', 'OrlyGayares', 'Orlangayares@gmail.com', '3fc0a7acf087f549ac2b266baf94b8b1', '09692439124', 'Zone 7 Brgy. Baras, Guimbal, Iloilo', '12-345A ', 'Car', '123456', 'uploads/driver_images/driver_1761450335_68fd995fd3e68.jpg', 1, '2025-10-26 03:45:35', 1),
(10, 'Cesar Esteva', 'Cesar', 'cesar@gmail.com', '286f6ff9be8cca78598a17b402839909', '09123456789', 'Baras, Guimbal Iloilo', '12-345A ', 'Truck', '1234-5678', 'uploads/driver_images/driver_1772857177_69aba7598322d.jpg', 1, '2026-03-07 04:19:37', 1),
(11, 'Miguel Cruz', 'admin', 'miguelc@gmail.com', '495936dbaa44b29b60e70b35575fc24d', '09692439124', 'Nalundan Guimbal, Iloilo', '123345', 'Motorcycle', '343567', 'images/default-avatar.png', 1, '2026-03-19 20:04:47', 1);

-- --------------------------------------------------------

--
-- Table structure for table `delivery_proofs`
--

CREATE TABLE `delivery_proofs` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `delivery_proofs`
--

INSERT INTO `delivery_proofs` (`id`, `order_id`, `driver_id`, `image_path`, `uploaded_at`) VALUES
(1, 43, 8, 'delivery_43_1761382975.png', '2025-10-25 09:02:55'),
(2, 45, 8, 'delivery_45_1761409818.png', '2025-10-25 16:30:18'),
(3, 46, 8, 'delivery_46_1761410417.png', '2025-10-25 16:40:17'),
(4, 47, 9, 'delivery_47_1761450750.png', '2025-10-26 03:52:30'),
(5, 48, 9, 'delivery_48_1761634489.jpg', '2025-10-28 06:54:49'),
(6, 50, 9, 'delivery_50_1761710840.jpg', '2025-10-29 04:07:20'),
(7, 51, 9, 'delivery_51_1761711271.jpg', '2025-10-29 04:14:31');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `feedback_type` varchar(50) NOT NULL,
  `rating` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('new','read','addressed') DEFAULT 'new'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `user_id`, `user_name`, `feedback_type`, `rating`, `message`, `created_at`, `status`) VALUES
(1, 23, 'Ann', 'praise', 0, 'Good Website!', '2025-10-28 11:24:28', 'read'),
(2, 23, 'Ann', 'praise', 5, 'Good Website!', '2025-10-28 11:25:59', 'addressed');

-- --------------------------------------------------------

--
-- Table structure for table `iloilo_shipping_rates`
--

CREATE TABLE `iloilo_shipping_rates` (
  `id` int(11) NOT NULL,
  `municipality` varchar(50) NOT NULL,
  `base_fee` decimal(10,2) NOT NULL,
  `distance_km` decimal(5,2) NOT NULL,
  `district` tinyint(4) NOT NULL,
  `delivery_time` varchar(20) DEFAULT '3-5 days'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `iloilo_shipping_rates`
--

INSERT INTO `iloilo_shipping_rates` (`id`, `municipality`, `base_fee`, `distance_km`, `district`, `delivery_time`) VALUES
(1, 'Guimbal', 20.00, 0.00, 1, '3-5 days'),
(2, 'Igbaras', 45.00, 18.50, 1, '3-5 days'),
(3, 'Miagao', 30.00, 12.50, 1, '3-5 days'),
(4, 'Oton', 35.00, 15.00, 1, '3-5 days'),
(5, 'San Joaquin', 50.00, 22.00, 1, '3-5 days'),
(6, 'Tigbauan', 25.00, 8.20, 1, '3-5 days'),
(7, 'Tubungan', 60.00, 25.00, 1, '3-5 days'),
(8, 'Alimodian', 55.00, 23.00, 2, '3-5 days'),
(9, 'Leganes', 40.00, 17.00, 2, '3-5 days'),
(10, 'Leon', 65.00, 27.00, 2, '3-5 days'),
(11, 'New Lucena', 45.00, 19.00, 2, '3-5 days'),
(12, 'Pavia', 30.00, 13.00, 2, '3-5 days'),
(13, 'San Miguel', 70.00, 29.00, 2, '3-5 days'),
(14, 'Santa Barbara', 50.00, 25.00, 2, '3-5 days'),
(15, 'Zarraga', 35.00, 14.50, 2, '3-5 days'),
(16, 'Badiangan', 85.00, 35.00, 3, '3-5 days'),
(17, 'Bingawan', 95.00, 40.00, 3, '3-5 days'),
(18, 'Cabatuan', 60.00, 28.00, 3, '3-5 days'),
(19, 'Calinog', 120.00, 50.00, 3, '3-5 days'),
(20, 'Janiuay', 80.00, 42.00, 3, '3-5 days'),
(21, 'Lambunao', 110.00, 45.00, 3, '3-5 days'),
(22, 'Maasin', 75.00, 32.00, 3, '3-5 days'),
(23, 'Mina', 65.00, 30.00, 3, '3-5 days'),
(24, 'Pototan', 55.00, 26.00, 3, '3-5 days'),
(25, 'Anilao', 90.00, 38.00, 4, '3-5 days'),
(26, 'Banate', 100.00, 42.00, 4, '3-5 days'),
(27, 'Barotac Nuevo', 85.00, 36.00, 4, '3-5 days'),
(28, 'Dingle', 75.00, 34.00, 4, '3-5 days'),
(29, 'Dueñas', 70.00, 31.00, 4, '3-5 days'),
(30, 'Dumangas', 60.00, 28.00, 4, '3-5 days'),
(31, 'Passi City', 120.00, 68.00, 4, '3-5 days'),
(32, 'San Enrique', 95.00, 40.00, 4, '3-5 days'),
(33, 'Ajuy', 150.00, 65.00, 5, '3-5 days'),
(34, 'Balasan', 180.00, 75.00, 5, '3-5 days'),
(35, 'Barotac Viejo', 130.00, 55.00, 5, '3-5 days'),
(36, 'Batad', 160.00, 70.00, 5, '3-5 days'),
(37, 'Carles', 200.00, 110.00, 5, '3-5 days'),
(38, 'Concepcion', 190.00, 85.00, 5, '3-5 days'),
(39, 'Estancia', 170.00, 80.00, 5, '3-5 days'),
(40, 'Lemery', 140.00, 60.00, 5, '3-5 days'),
(41, 'San Dionisio', 160.00, 70.00, 5, '3-5 days'),
(42, 'San Rafael', 150.00, 65.00, 5, '3-5 days');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `u_id` int(11) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_reference` varchar(100) DEFAULT NULL,
  `shipping_fee` decimal(10,2) NOT NULL,
  `delivery_location` varchar(100) NOT NULL,
  `barangay` varchar(100) NOT NULL,
  `grand_total` decimal(10,2) NOT NULL,
  `order_date` datetime NOT NULL,
  `status` enum('pending','processing','on the way','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `proof_image` varchar(255) DEFAULT NULL,
  `driver_id` int(11) DEFAULT NULL,
  `cancelled_by` enum('customer','admin') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `u_id`, `payment_method`, `payment_reference`, `shipping_fee`, `delivery_location`, `barangay`, `grand_total`, `order_date`, `status`, `proof_image`, `driver_id`, `cancelled_by`) VALUES
(17, 12, 'COD', '', 20.00, 'Guimbal', 'Brgy. Baras Guimbal, Iloilo', 1020.00, '2025-07-17 22:13:10', 'cancelled', NULL, NULL, NULL),
(19, 12, 'COD', '', 20.00, 'Guimbal', 'Brgy. Baras Guimbal, Iloilo', 1370.00, '2025-07-17 22:33:24', 'cancelled', NULL, NULL, NULL),
(20, 12, 'COD', '', 20.00, 'Guimbal', 'Brgy. Baras Guimbal, Iloilo', 320.00, '2025-07-17 22:37:59', 'delivered', NULL, NULL, NULL),
(21, 12, 'COD', '', 20.00, 'Guimbal', 'Brgy. Baras Guimbal, Iloilo', 830.00, '2025-07-17 22:40:01', 'cancelled', NULL, NULL, NULL),
(22, 12, 'COD', '', 20.00, 'Guimbal', 'Brgy. Baras Guimbal, Iloilo', 270.00, '2025-07-17 22:52:19', 'cancelled', NULL, NULL, NULL),
(23, 12, 'COD', '', 20.00, 'Guimbal', 'Brgy. Baras Guimbal, Iloilo', 140.00, '2025-07-17 22:55:53', 'cancelled', NULL, NULL, NULL),
(24, 13, 'COD', '', 80.00, 'Janiuay', 'Brgy. Mangil, Janiuay, Iloilo', 690.00, '2025-07-29 20:19:02', 'cancelled', NULL, NULL, NULL),
(25, 13, 'COD', '', 80.00, 'Janiuay', 'Brgy. Mangil, Janiuay, Iloilo', 330.00, '2025-07-30 08:59:20', 'on the way', NULL, NULL, NULL),
(26, 13, 'COD', '', 80.00, 'Janiuay', 'Brgy. Mangil, Janiuay, Iloilo', 580.00, '2025-08-03 00:14:50', 'on the way', NULL, NULL, NULL),
(27, 13, 'COD', '', 80.00, 'Janiuay', 'Brgy. Mangil, Janiuay, Iloilo', 460.00, '2025-08-03 00:17:10', 'cancelled', NULL, NULL, NULL),
(28, 13, 'COD', '', 120.00, 'Passi City', 'Brgy. Mangil, Passi, Iloilo', 360.00, '2025-08-03 00:30:32', 'delivered', NULL, NULL, NULL),
(29, 14, 'COD', '', 80.00, 'Janiuay', 'Brgy. Jibolo, Iloilo', 1150.00, '2025-08-13 20:11:56', 'delivered', NULL, NULL, NULL),
(30, 14, 'COD', '', 80.00, 'Janiuay', 'Brgy. Jibolo, Iloilo', 260.00, '2025-08-13 20:38:00', 'cancelled', NULL, NULL, NULL),
(31, 14, 'COD', '', 80.00, 'Janiuay', 'Brgy. Jibolo, Iloilo', 830.00, '2025-08-13 20:46:37', 'delivered', NULL, NULL, NULL),
(32, 15, 'COD', '', 20.00, 'Guimbal', 'Brgy. Baras Guimbal, Iloilo', 780.00, '2025-08-13 23:48:36', 'delivered', NULL, NULL, NULL),
(33, 15, 'gcash', '6031641925788', 20.00, 'Guimbal', 'Brgy. Baras Guimbal, Iloilo', 140.00, '2025-08-14 00:46:54', 'pending', NULL, NULL, NULL),
(34, 15, 'gcash', '0978654765432', 20.00, 'Guimbal', 'Brgy. Baras Guimbal, Iloilo', 320.00, '2025-08-14 01:06:36', 'pending', NULL, NULL, NULL),
(35, 15, 'COD', '', 20.00, 'Guimbal', 'Brgy. Baras Guimbal, Iloilo', 620.00, '2025-08-14 09:35:42', 'processing', NULL, NULL, NULL),
(36, 15, 'COD', '', 20.00, 'Guimbal', 'Brgy. Baras Guimbal, Iloilo', 1520.00, '2025-08-14 09:50:56', 'pending', NULL, NULL, NULL),
(37, 16, 'COD', '', 50.00, 'San Joaquin', 'Brgy. Baras Guimbal, Iloilo', 300.00, '2025-09-02 23:01:52', 'pending', NULL, NULL, NULL),
(38, 20, 'COD', '', 35.00, 'Oton', 'Brgy. Baras Oton, Iloilo', 85.00, '2025-10-10 01:05:37', 'cancelled', NULL, NULL, NULL),
(39, 20, 'COD', '', 35.00, 'Oton', 'Brgy. Baras Oton, Iloilo', 5985.00, '2025-10-10 01:06:12', 'on the way', NULL, NULL, NULL),
(40, 20, 'COD', '', 35.00, 'Oton', 'Brgy. Baras Oton, Iloilo', 110.00, '2025-10-14 13:42:10', 'on the way', NULL, NULL, NULL),
(41, 23, 'COD', '', 35.00, 'Oton', 'Bgry. Baras Oton ', 3835.00, '2025-10-19 20:56:00', 'delivered', NULL, NULL, NULL),
(42, 23, 'COD', '', 35.00, 'Oton', 'Bgry. Baras Oton ', 535.00, '2025-10-22 19:22:41', 'on the way', NULL, 6, NULL),
(43, 23, 'COD', '', 35.00, 'Oton', 'Bgry. Baras Oton ', 85.00, '2025-10-24 22:08:45', 'delivered', NULL, 8, NULL),
(44, 23, 'COD', '', 35.00, 'Oton', 'Bgry. Baras Oton ', 3835.00, '2025-10-25 23:00:01', 'cancelled', NULL, NULL, NULL),
(45, 26, 'COD', '', 20.00, 'Guimbal', 'Bgry. Baras Igbaras', 780.00, '2025-10-26 00:25:08', 'delivered', NULL, 8, NULL),
(46, 23, 'COD', '', 35.00, 'Oton', 'Bgry. Baras Oton ', 1175.00, '2025-10-26 00:38:16', 'delivered', NULL, 8, NULL),
(47, 23, 'COD', '', 35.00, 'Oton', 'Bgry. Baras Oton ', 1175.00, '2025-10-26 11:47:35', 'delivered', NULL, 9, NULL),
(48, 23, 'gcash', '0134081573650', 35.00, 'Oton', 'Bgry. Baras Oton ', 415.00, '2025-10-28 09:50:52', 'delivered', NULL, 9, NULL),
(49, 23, 'gcash', '0134081573650', 35.00, 'Oton', 'Bgry. Baras Oton ', 285.00, '2025-10-28 14:35:58', 'on the way', NULL, 9, NULL),
(50, 23, 'COD', '', 35.00, 'Oton', 'Bgry. Baras Oton ', 155.00, '2025-10-28 15:38:47', 'delivered', NULL, 9, NULL),
(51, 28, 'gcash', '1234567891234', 45.00, 'Igbaras', ' Brgy. Panaytayon', 125.00, '2025-10-29 12:12:49', 'delivered', NULL, 9, NULL),
(52, 23, 'gcash', '1234567890123', 35.00, 'Oton', 'Bgry. Baras Oton ', 355.00, '2025-10-29 22:31:16', 'pending', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`item_id`, `order_id`, `product_id`, `product_name`, `quantity`, `price`, `subtotal`) VALUES
(32, 17, 22, 'Sword Fish', 1.00, 320.00, 320.00),
(33, 17, 27, 'Shrimp', 1.00, 320.00, 320.00),
(34, 17, 29, 'Tahong', 2.00, 180.00, 360.00),
(35, 19, 17, 'Baby-squid', 3.00, 250.00, 750.00),
(36, 19, 24, 'Lato', 5.00, 120.00, 600.00),
(37, 20, 25, 'Oyster', 2.00, 150.00, 300.00),
(38, 21, 21, 'Crab', 3.00, 220.00, 660.00),
(39, 21, 25, 'Oyster', 1.00, 150.00, 150.00),
(40, 22, 17, 'Baby-squid', 1.00, 250.00, 250.00),
(41, 23, 24, 'Lato', 1.00, 120.00, 120.00),
(42, 24, 18, 'Bangus Boneless', 1.00, 250.00, 250.00),
(43, 24, 29, 'Tahong', 2.00, 180.00, 360.00),
(44, 25, 17, 'Baby-squid', 1.00, 250.00, 250.00),
(45, 26, 18, 'Bangus Boneless', 2.00, 250.00, 500.00),
(46, 27, 31, 'Tuna', 1.00, 380.00, 380.00),
(47, 28, 24, 'Lato', 2.00, 120.00, 240.00),
(48, 29, 18, 'Bangus Boneless', 1.00, 250.00, 250.00),
(49, 29, 27, 'Shrimp', 2.00, 320.00, 640.00),
(50, 29, 29, 'Tahong', 1.00, 180.00, 180.00),
(51, 30, 29, 'Tahong', 1.00, 180.00, 180.00),
(52, 31, 30, 'Tawilis', 5.00, 150.00, 750.00),
(53, 32, 21, 'Crab', 2.00, 220.00, 440.00),
(54, 32, 27, 'Shrimp', 1.00, 320.00, 320.00),
(55, 33, 24, 'Lato', 1.00, 120.00, 120.00),
(56, 34, 28, 'Large squid ', 1.00, 300.00, 300.00),
(57, 35, 19, 'Bangus', 3.00, 200.00, 600.00),
(58, 36, 18, 'Bangus Boneless', 2.00, 250.00, 500.00),
(59, 36, 19, 'Bangus', 5.00, 200.00, 1000.00),
(60, 37, 18, 'Bangus Boneless', 1.00, 250.00, 250.00),
(61, 38, 42, 'Daing na Bisugo', 1.00, 50.00, 50.00),
(62, 39, 42, 'Daing na Bisugo', 119.00, 50.00, 5950.00),
(63, 40, 42, 'Daing na Bisugo', 1.50, 50.00, 75.00),
(64, 41, 31, 'Tuna', 10.00, 380.00, 3800.00),
(65, 42, 42, 'Daing na Bisugo', 10.00, 50.00, 500.00),
(66, 43, 42, 'Daing na Bisugo', 1.00, 50.00, 50.00),
(67, 44, 31, 'Tuna', 10.00, 380.00, 3800.00),
(68, 45, 31, 'Tuna', 2.00, 380.00, 760.00),
(69, 46, 31, 'Tuna', 3.00, 380.00, 1140.00),
(70, 47, 31, 'Tuna', 3.00, 380.00, 1140.00),
(71, 48, 31, 'Tuna', 1.00, 380.00, 380.00),
(72, 49, 18, 'Bangus Boneless', 1.00, 250.00, 250.00),
(73, 50, 24, 'Lato', 1.00, 120.00, 120.00),
(74, 51, 42, 'Daing na Bisugo', 1.00, 80.00, 80.00),
(75, 52, 22, 'Sword Fish', 1.00, 320.00, 320.00);

-- --------------------------------------------------------

--
-- Table structure for table `payment_proofs`
--

CREATE TABLE `payment_proofs` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_proofs`
--

INSERT INTO `payment_proofs` (`id`, `order_id`, `image_path`, `uploaded_at`) VALUES
(1, 49, 'payment_proofs/gcash_proof_49_1761633358.jpg', '2025-10-28 06:35:58'),
(2, 51, 'payment_proofs/gcash_proof_51_1761711169.jpg', '2025-10-29 04:12:49'),
(3, 52, 'payment_proofs/gcash_proof_52_1761748276.jpg', '2025-10-29 14:31:16');

-- --------------------------------------------------------

--
-- Table structure for table `remark`
--

CREATE TABLE `remark` (
  `id` int(11) NOT NULL,
  `frm_id` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `remark` mediumtext NOT NULL,
  `remarkDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `remark`
--

INSERT INTO `remark` (`id`, `frm_id`, `status`, `remark`, `remarkDate`) VALUES
(20, 17, '', 'DELIVERED', '2025-04-29 01:26:21'),
(23, 19, '', 'Your order is on your way', '2025-04-29 03:32:39'),
(24, 19, '', 'DELIVERED', '2025-04-29 03:35:42'),
(25, 20, '', '.', '2025-05-13 17:14:28'),
(26, 21, '', 'Your ', '2025-05-14 00:45:12'),
(27, 21, '', 'Delivered', '2025-05-14 00:46:02'),
(28, 22, '', 'Delivered', '2025-05-14 00:46:35'),
(29, 23, '', 'Delivered', '2025-05-14 00:48:02'),
(30, 24, '', 'Delivered', '2025-05-14 00:48:48'),
(31, 25, '', 'Your order is on the way', '2025-05-14 02:07:10'),
(32, 28, '', 'Thank you for ordering', '2025-06-10 01:22:12'),
(50, 17, 'processing', 'Your order is now being prepared.\n\nPayment Method: Cash on Delivery\nCOD Amount: ?1,020.00\nPlease prepare the exact amount.', '2025-07-17 14:31:37'),
(51, 17, 'on the way', 'Your order is on the way to your location!\n\nPayment Method: Cash on Delivery\nCOD Amount: ?1,020.00\nPlease prepare the exact amount.\n\nYour delivery driver: Mark Tahimik', '2025-07-17 14:32:32'),
(52, 20, 'pending', 'Your order was submitted. Thank you for ordering!', '2025-07-17 14:37:59'),
(53, 21, 'pending', 'Your order was submitted. Thank you for ordering!', '2025-07-17 14:40:01'),
(54, 22, 'pending', 'Your order was submitted. Thank you for ordering!', '2025-07-17 14:52:19'),
(55, 23, 'pending', 'Your order was submitted. Thank you for ordering!', '2025-07-17 14:55:53'),
(56, 17, 'delivered', 'Your order has been delivered. Thank you for shopping with us!\n\nPayment Method: Cash on Delivery\nCOD Amount: ?1,020.00\nPlease prepare the exact amount.', '2025-07-25 12:41:52'),
(57, 23, 'cancelled', 'Order cancelled by customer', '2025-07-25 13:23:28'),
(58, 22, 'pending', 'Your order is being processed. We will update you soon.\n\nPayment Method: Cash on Delivery\nCOD Amount: ?270.00\nPlease prepare the exact amount.', '2025-07-28 05:53:38'),
(59, 22, 'cancelled', 'Order cancelled by customer', '2025-07-28 05:56:13'),
(60, 21, 'cancelled', 'Order cancelled by customer', '2025-07-28 05:57:02'),
(61, 20, 'processing', 'We are preparing your order\n\nPayment Method: COD\nAmount Due: ?320.00\n', '2025-07-28 06:23:52'),
(62, 20, 'on the way', 'Your order is on the way!\n\nPayment Method: COD\nAmount Due: ?320.00\nDriver: Mark Tahimik\n', '2025-07-28 06:24:26'),
(63, 20, 'delivered', 'Your order has been delivered\n\nPayment Method: COD\nAmount Due: ?320.00\n', '2025-07-28 06:25:00'),
(64, 24, 'pending', 'Your order was submitted. Thank you for ordering!', '2025-07-29 12:19:02'),
(65, 24, 'processing', 'We are preparing your order\n\nPayment Method: COD\nAmount Due: ?690.00\n', '2025-07-29 12:20:17'),
(66, 24, 'cancelled', 'Order cancelled by customer on 2025-07-30 02:57:48', '2025-07-30 00:57:48'),
(67, 25, 'pending', 'Your order was submitted. Thank you for ordering!', '2025-07-30 00:59:20'),
(68, 25, 'processing', 'We are preparing your order\n\nPayment Method: COD\nAmount Due: ?330.00\n', '2025-07-30 01:02:54'),
(69, 26, 'pending', 'Your order was submitted. Thank you for ordering!', '2025-08-02 16:14:50'),
(70, 26, 'processing', 'We are preparing your order\n\nPayment Method: COD\nAmount Due: ?580.00\n', '2025-08-02 16:15:16'),
(71, 27, 'pending', 'Your order was submitted. Thank you for ordering!', '2025-08-02 16:17:10'),
(72, 27, 'cancelled', 'Order cancelled by customer on 2025-08-02 18:17:15', '2025-08-02 16:17:15'),
(73, 28, 'pending', 'Your order was submitted. Thank you for ordering!', '2025-08-02 16:30:32'),
(74, 28, 'processing', 'We are preparing your order\n\nPayment Method: COD\nAmount Due: ?360.00\n', '2025-08-02 16:30:58'),
(75, 28, 'on the way', 'Your order is on the way!\n\nPayment Method: COD\nAmount Due: ?360.00\nDriver: Mark Tahimik\nNote: Your Order is on the way', '2025-08-02 16:32:22'),
(76, 28, 'delivered', 'Your order has been delivered\n\nPayment Method: COD\nAmount Due: ?360.00\n', '2025-08-04 18:05:22'),
(77, 26, 'on the way', 'Your order is on the way!\n\nPayment Method: COD\nAmount Due: ?580.00\n', '2025-08-04 18:08:13'),
(78, 25, 'on the way', 'Your order is on the way!\n\nPayment Method: COD\nAmount Due: ?330.00\n', '2025-08-04 19:12:46'),
(79, 29, 'pending', 'Your order was submitted. Thank you for ordering!', '2025-08-13 12:11:56'),
(80, 29, 'processing', 'We are preparing your order\n\nPayment Method: COD\nAmount Due: ?1,150.00\n', '2025-08-13 12:13:40'),
(81, 29, 'on the way', 'Your order is on the way!\n\nPayment Method: COD\nAmount Due: ?1,150.00\nDriver: Mark Tahimik\n', '2025-08-13 12:14:19'),
(82, 29, 'delivered', 'Your order has been delivered\n\nPayment Method: COD\nAmount Due: ?1,150.00\n', '2025-08-13 12:15:00'),
(83, 19, 'cancelled', 'Your order has been cancelled\n\nPayment Method: COD\nAmount Due: ?1,370.00\n', '2025-08-13 12:31:29'),
(84, 30, 'pending', 'Your order was submitted. Thank you for ordering!', '2025-08-13 12:38:00'),
(85, 30, 'cancelled', 'Order cancelled by customer', '2025-08-13 12:38:41'),
(86, 31, 'pending', 'Your order was submitted. Thank you for ordering!', '2025-08-13 12:46:37'),
(87, 31, 'processing', 'We are preparing your order\n\nPayment Method: COD\nAmount Due: ?830.00\n', '2025-08-13 12:46:51'),
(88, 31, 'on the way', 'Your order is on the way!\n\nPayment Method: COD\nAmount Due: ?830.00\n', '2025-08-13 12:47:24'),
(89, 31, 'on the way', 'Your order is on the way!\n\nPayment Method: COD\nAmount Due: ?830.00\n', '2025-08-13 13:47:12'),
(90, 31, 'on the way', 'Your order is on the way!\n\nPayment Method: COD\nAmount Due: ?830.00\n', '2025-08-13 13:47:27'),
(91, 31, 'delivered', 'Your order has been delivered\n\nPayment Method: COD\nAmount Due: ?830.00\nDriver: Mark Tahimik\n', '2025-08-13 13:47:42'),
(92, 32, 'pending', 'Your order was submitted. Thank you for ordering!', '2025-08-13 15:48:36'),
(93, 32, 'processing', 'We are preparing your order\n\nPayment Method: COD\nAmount Due: ?780.00\n', '2025-08-13 15:50:20'),
(94, 32, 'on the way', 'Your order is on the way!\n\nPayment Method: COD\nAmount Due: ?780.00\nDriver: Mark Tahimik\n', '2025-08-13 15:52:00'),
(95, 32, 'on the way', 'Your order is on the way!\n\nPayment Method: COD\nAmount Due: ?780.00\n', '2025-08-13 15:53:04'),
(96, 32, 'delivered', 'Your order has been delivered\n\nPayment Method: COD\nAmount Due: ?780.00\n', '2025-08-13 15:53:15'),
(97, 33, 'pending', 'Your order was submitted. Thank you for ordering!', '2025-08-13 16:46:54'),
(98, 34, 'pending', 'Your order was submitted. Thank you for ordering!', '2025-08-13 17:06:36'),
(99, 35, 'pending', 'Your order was submitted. Thank you for ordering!', '2025-08-14 01:35:42'),
(100, 35, 'processing', 'We are preparing your order\n\nPayment Method: COD\nAmount Due: ?620.00\n', '2025-08-14 01:36:30'),
(101, 36, 'pending', 'Your order was submitted. Thank you for ordering!', '2025-08-14 01:50:56'),
(102, 37, 'pending', 'Your order was submitted. Thank you for ordering!', '2025-09-02 15:01:52'),
(103, 38, 'pending', 'Your order was submitted. Thank you for ordering!', '2025-10-09 17:05:37'),
(104, 39, 'pending', 'Your order was submitted. Thank you for ordering!', '2025-10-09 17:06:12'),
(105, 39, 'processing', 'We are preparing your order\n\nPayment Method: COD\nAmount Due: ?5,985.00\n', '2025-10-09 17:07:30'),
(106, 39, 'on the way', 'Your order is on the way!\n\nPayment Method: COD\nAmount Due: ?5,985.00\nDriver: Mark Tahimik\n', '2025-10-09 17:08:00'),
(107, 40, 'pending', 'Your order was submitted. Thank you for ordering!', '2025-10-14 05:42:10'),
(108, 40, 'on the way', 'Your order is on the way!\n\nPayment Method: COD\nAmount Due: ?110.00\nDriver: Mark Tahimik\nNote: Your Order is on the Way', '2025-10-14 05:44:03'),
(109, 38, 'cancelled', 'Order cancelled by customer on 2025-10-14 12:21:10', '2025-10-14 10:21:10'),
(110, 41, 'pending', 'Your order was submitted. Thank you for ordering!', '2025-10-19 12:56:00'),
(111, 41, 'processing', 'We are preparing your order\n\nPayment Method: COD\nAmount Due: ?3,835.00\nNote: Bantayi sagwa balay nyo', '2025-10-19 12:57:37'),
(112, 42, 'pending', 'Your order was submitted. Thank you for ordering!', '2025-10-22 11:22:41'),
(113, 41, 'on the way', 'Your order is on the way!\n\nPayment Method: COD\nAmount Due: ?3,835.00\nDriver: Mark Tahimik\n', '2025-10-22 12:56:58'),
(114, 42, 'processing', 'We are preparing your order\n\nPayment Method: COD\nAmount Due: ?535.00\n', '2025-10-24 08:19:57'),
(115, 41, 'delivered', 'Your order has been delivered\n\nPayment Method: COD\nAmount Due: ?3,835.00\n', '2025-10-24 08:24:28'),
(116, 42, 'on the way', 'Your order is on the way!\n\nPayment Method: COD\nAmount Due: ?535.00\n\n--- Delivery Driver Information ---\nDriver Name: Patrick Eigo\nContact Number: 09092345456\nVehicle: Motorcycle (12-345A )\n---\n\n', '2025-10-24 09:06:32'),
(117, 43, 'pending', 'Your order was submitted. Thank you for ordering!', '2025-10-24 14:08:45'),
(118, 43, 'processing', 'We are preparing your order\n\nPayment Method: COD\nAmount Due: ?85.00\n', '2025-10-24 14:09:19'),
(119, 43, 'on the way', 'Your order is on the way!\n\nPayment Method: COD\nAmount Due: ?85.00\n\n--- Delivery Driver Information ---\nDriver Name: Patrick Eigo\nContact Number: 09092345456\nVehicle: Truck (12-345A )\n---\n\n', '2025-10-24 14:09:43'),
(120, 43, 'delivered', 'Your order has been delivered\n\nPayment Method: COD\nAmount Due: ?85.00\n\n--- Delivery Driver Information ---\nDriver Name: Patrick Eigo\nContact Number: 09092345456\nVehicle: Truck (12-345A )\n---\n\n', '2025-10-25 14:01:55'),
(121, 17, 'cancelled', 'Your order has been cancelled\n\nPayment Method: COD\nAmount Due: ?1,020.00\n', '2025-10-25 14:15:25'),
(122, 44, 'pending', 'Your order was submitted. Thank you for ordering!', '2025-10-25 15:00:01'),
(123, 45, 'pending', 'Your order was submitted. Thank you for ordering!', '2025-10-25 16:25:08'),
(124, 45, 'processing', 'We are preparing your order\n\nPayment Method: COD\nAmount Due: ?780.00\n', '2025-10-25 16:27:48'),
(125, 45, 'on the way', 'Your order is on the way!\n\nPayment Method: COD\nAmount Due: ?780.00\n\n--- Delivery Driver Information ---\nDriver Name: Patrick Eigo\nContact Number: 09092345456\nVehicle: Truck (12-345A )\n---\n\n', '2025-10-25 16:29:12'),
(126, 45, 'delivered', 'Your order has been delivered\n\nPayment Method: COD\nAmount Due: ?780.00\n\n--- Delivery Driver Information ---\nDriver Name: Patrick Eigo\nContact Number: 09092345456\nVehicle: Truck (12-345A )\n---\n\n', '2025-10-25 16:30:31'),
(127, 46, 'pending', 'Your order was submitted. Thank you for ordering!', '2025-10-25 16:38:16'),
(128, 46, 'processing', 'We are preparing your order\n\nPayment Method: COD\nAmount Due: ?1,175.00\n', '2025-10-25 16:38:50'),
(129, 46, 'on the way', 'Your order is on the way!\n\nPayment Method: COD\nAmount Due: ?1,175.00\n\n--- Delivery Driver Information ---\nDriver Name: Patrick Eigo\nContact Number: 09092345456\nVehicle: Truck (12-345A )\n---\n\n', '2025-10-25 16:39:38'),
(130, 46, 'delivered', 'Your order has been delivered\n\nPayment Method: COD\nAmount Due: ?1,175.00\n\n--- Delivery Driver Information ---\nDriver Name: Patrick Eigo\nContact Number: 09092345456\nVehicle: Truck (12-345A )\n---\n\n', '2025-10-25 16:41:13'),
(131, 47, 'pending', 'Your order was submitted. Thank you for ordering!', '2025-10-26 03:47:35'),
(132, 47, 'processing', 'We are preparing your order\n\nPayment Method: COD\nAmount Due: ?1,175.00\n', '2025-10-26 03:48:27'),
(133, 47, 'on the way', 'Your order is on the way!\n\nPayment Method: COD\nAmount Due: ?1,175.00\n\n--- Delivery Driver Information ---\nDriver Name: Orlan Gayares\nContact Number: 09692439124\nVehicle: Car (12-345A )\n---\n\n', '2025-10-26 03:49:11'),
(134, 47, 'delivered', 'Your order has been delivered\n\nPayment Method: COD\nAmount Due: ?1,175.00\n\n--- Delivery Driver Information ---\nDriver Name: Orlan Gayares\nContact Number: 09692439124\nVehicle: Car (12-345A )\n---\n\n', '2025-10-26 03:53:08'),
(135, 44, 'cancelled', 'Order cancelled by customer on 2025-10-26 04:53:36', '2025-10-26 03:53:36'),
(136, 48, 'pending', 'Your order was submitted. Thank you for ordering!', '2025-10-28 01:50:52'),
(137, 48, 'processing', 'We are preparing your order\n\nPayment Method: GCash\nAmount: ?415.00\nReference: 0134081573650\n', '2025-10-28 01:51:46'),
(138, 48, 'on the way', 'Your order is on the way!\n\nPayment Method: GCash\nAmount: ?415.00\nReference: 0134081573650\n\n--- Delivery Driver Information ---\nDriver Name: Orlan Gayares\nContact Number: 09692439124\nVehicle: Car (12-345A )\n---\n\n', '2025-10-28 01:52:15'),
(139, 49, 'pending', 'Your order was submitted. Thank you for ordering!', '2025-10-28 06:35:58'),
(140, 49, 'on the way', 'Your order is on the way!\n\nPayment Method: GCash\nAmount: ?285.00\nReference: 0134081573650\n\n--- Delivery Driver Information ---\nDriver Name: Orlan Gayares\nContact Number: 09692439124\nVehicle: Car (12-345A )\n---\n\n', '2025-10-28 06:55:15'),
(141, 50, 'pending', 'Your order was submitted. Thank you for ordering!', '2025-10-28 07:38:47'),
(142, 50, 'on the way', 'Your order is on the way!\n\nPayment Method: COD\nAmount Due: ?155.00\n\n--- Delivery Driver Information ---\nDriver Name: Orlan Gayares\nContact Number: 09692439124\nVehicle: Car (12-345A )\n---\n\n', '2025-10-28 07:39:06'),
(143, 51, 'pending', 'Your order was submitted. Thank you for ordering!', '2025-10-29 04:12:49'),
(144, 51, 'processing', 'We are preparing your order\n\nPayment Method: GCash\nAmount: ?125.00\nReference: 1234567891234\n', '2025-10-29 04:13:23'),
(145, 51, 'on the way', 'Your order is on the way!\n\nPayment Method: GCash\nAmount: ?125.00\nReference: 1234567891234\n\n--- Delivery Driver Information ---\nDriver Name: Orlan Gayares\nContact Number: 09692439124\nVehicle: Car (12-345A )\n---\n\n', '2025-10-29 04:13:56'),
(146, 52, 'pending', 'Your order was submitted. Thank you for ordering!', '2025-10-29 14:31:16');

-- --------------------------------------------------------

--
-- Table structure for table `res_category`
--

CREATE TABLE `res_category` (
  `c_id` int(222) NOT NULL,
  `c_name` varchar(222) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `res_category`
--

INSERT INTO `res_category` (`c_id`, `c_name`, `date`) VALUES
(1, 'FISH', '2025-04-22 23:36:05'),
(2, 'SHELLFISH', '2025-04-22 23:36:27'),
(3, 'SEAWEEDS', '2025-04-22 23:36:38'),
(4, 'PUSIT', '2025-04-22 23:36:48'),
(7, 'DRIED FISH', '2025-10-06 03:15:56');

-- --------------------------------------------------------

--
-- Table structure for table `seafoods`
--

CREATE TABLE `seafoods` (
  `d_id` int(222) NOT NULL,
  `rs_id` int(222) NOT NULL,
  `title` varchar(222) NOT NULL,
  `slogan` varchar(222) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `img` varchar(222) NOT NULL,
  `stock` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `seafoods`
--

INSERT INTO `seafoods` (`d_id`, `rs_id`, `title`, `slogan`, `price`, `img`, `stock`) VALUES
(17, 4, 'Baby-squid', 'Fresh Baby-squid for you', 250.00, '68082aa16eeb4.jpg', 20.00),
(18, 1, 'Bangus Boneless', 'Bangus Bonelss for you', 250.00, '68082ae0a19e0.jpg', 59.00),
(19, 1, 'Bangus', 'Fresh Bangus for you', 200.00, '68082b000bfd7.jpg', 20.00),
(20, 1, 'Blue Marlin', 'Blue Blue-marlin for you', 450.00, '68082b7306ad3.jpg', 0.00),
(21, 3, 'Crab', 'Fresh Crab for you', 220.00, '68082b9cd0ed9.jpg', 30.00),
(22, 1, 'Sword Fish', 'Fresh Sword Fish for you', 320.00, '68082c0602e5a.jpg', 79.00),
(23, 1, 'Galunggong', 'Fresh Galunggong for you', 220.00, '68082c5813e7d.jpg', 0.00),
(24, 2, 'Lato', 'Fresh Lato for you', 120.00, '68082c8197a0c.jpg', 49.00),
(25, 3, 'Oyster', 'Fresh Oyster for you', 150.00, '68082c9f45e8a.jpg', 60.00),
(26, 1, 'Pampano', 'Fresh Pampano for you', 390.00, '68082cc617cef.jpg', 50.00),
(27, 3, 'Shrimp', 'Fresh Shrimp for you', 320.00, '68082cf19452e.jpg', 100.00),
(28, 4, 'Large squid ', 'Fresh large-squid for you', 300.00, '68082d403d79b.jpg', 0.00),
(29, 3, 'Tahong', 'Fresh Tahong for you', 180.00, '68082d64b5cca.jpg', 50.00),
(30, 1, 'Tawilis', 'Fresh Tawilis for you', 150.00, '68082d9239a6b.jpg', 50.00),
(31, 1, 'Tuna', 'Fresh Tuna for you', 380.00, '68082db272e51.jpg', 1.00),
(42, 5, 'Daing na Bisugo', 'Quality Dried Fish', 80.00, '68e7bbf560369.jpg', 9.00),
(43, 5, 'Daing na Galunggong', 'Quality Dried Fish', 150.00, '690045a623677.jpg', 10.00),
(44, 5, 'Daing na Pusit', 'Quality Dried Fish', 180.00, '69004601ac1da.jpg', 10.00),
(45, 5, 'Daing na Sapsap', 'Quality Dried Fish', 60.00, '6900462f6762d.jpg', 10.00),
(46, 5, 'Danggit', 'Quality Dried Fish', 60.00, '690056edc4cbe.jpg', 10.00),
(47, 5, 'Dried Hipon', 'Quality Dried Fish', 50.00, '6900570fc839c.jpg', 10.00),
(48, 5, 'Tinapa Galunggong', 'Quality Dried Fish', 200.00, '6900573bc36b2.jpg', 10.00),
(49, 5, 'Tinapa Salinas', 'Quality Dried Fish', 130.00, '6900575c1f0b2.jpg', 10.00);

-- --------------------------------------------------------

--
-- Table structure for table `stock_history`
--

CREATE TABLE `stock_history` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `old_stock` decimal(10,2) DEFAULT NULL,
  `new_stock` decimal(10,2) DEFAULT NULL,
  `change_type` enum('purchase','restock','adjustment') DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `u_id` int(222) NOT NULL,
  `username` varchar(222) NOT NULL,
  `f_name` varchar(222) NOT NULL,
  `l_name` varchar(222) NOT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `email` varchar(222) NOT NULL,
  `phone` varchar(222) NOT NULL,
  `password` varchar(222) NOT NULL,
  `address` text NOT NULL,
  `status` int(222) NOT NULL DEFAULT 1,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `municipality` varchar(50) DEFAULT NULL,
  `barangay` varchar(50) DEFAULT NULL,
  `postal_code` varchar(10) DEFAULT NULL,
  `shipping_fee` decimal(10,2) DEFAULT 0.00,
  `profile_image` varchar(255) DEFAULT 'images/user-icn.webp'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`u_id`, `username`, `f_name`, `l_name`, `fullname`, `email`, `phone`, `password`, `address`, `status`, `date`, `municipality`, `barangay`, `postal_code`, `shipping_fee`, `profile_image`) VALUES
(12, 'Karen', 'Karen', 'Allones', 'Karen Allones', 'allones@gmail.com', '09091234567', '5a4629189a54ce51bd2018227e923bbc', 'Zone 7 Brgy. Baras, Guimbal, Iloilo', 1, '2025-07-17 14:09:45', 'Guimbal', 'Brgy. Baras Guimbal, Iloilo', '5022', 0.00, 'images/user-icn.webp'),
(13, 'Fenny', 'Fenny  Rose Paguntalan  Rose Paguntalan', ' Rose Paguntalan', 'Fenny  Rose Paguntalan', 'fen@gmail.com', '09876543210', 'e7d884c74bf71cbdf6fd798d28ed0822', 'Zone7 Brgy. Mangil, Passi, Iloilo', 1, '2025-08-02 16:29:26', 'Passi City', 'Brgy. Mangil, Passi, Iloilo', '5034', 0.00, 'images/user-icn.webp'),
(14, 'Kimberly', 'Kimberly', 'Allado', 'Kimberly Allado', 'Kemberly@gmail.com', '09876543210', 'b8642793343ac75d5b6109d8b6bff0fc', 'Brgy. Jibolo, Janiuay, Iloilo', 1, '2025-08-13 12:09:00', 'Janiuay', 'Brgy. Jibolo, Iloilo', '5034', 0.00, 'images/user-icn.webp'),
(15, 'Ashley', 'Ashley Marie Gayares', 'Marie Gayares', 'Ashley Marie Gayares', 'marie@gmail.com', '09876543210', '510c123df046cfc0a8bdd961edeefff6', 'Zone 7 Brgy. Baras, Guimbal, Iloilo', 1, '2025-08-13 15:37:38', 'Guimbal', 'Brgy. Baras Guimbal, Iloilo', '5022', 0.00, 'images/user-icn.webp'),
(16, 'cherry', 'Cherry', 'Mae Sardenola', 'Cherry Mae Sardenola', 'chh@gmail.com', '09876543210', '855728c307db5bf8cbc086cc14a47866', 'Zone 7 Brgy. Baras, Guimbal, Iloilo', 1, '2025-09-02 14:59:42', 'San Joaquin', 'Brgy. Baras Guimbal, Iloilo', '5022', 0.00, 'images/user-icn.webp'),
(20, 'janreb', 'Janreb', 'Piad', 'Janreb Piad', 'jan@gmail.com', '09092345456', 'fc81488f5b269ee4fa43bb85cef4395a', 'Oton, Iloilo', 1, '2025-10-09 17:01:15', 'Oton', 'Brgy. Baras Oton, Iloilo', '5000', 0.00, 'images/user-icn.webp'),
(21, 'tin', 'Tinay', '', 'Tinay', 'tin@gmail.com', '09092345456', '811137414f6975fd9b48dd36b73f2fb1', 'Oton, Iloilo', 1, '2025-10-11 16:09:12', 'Oton', 'Brgy. Baras Oton, Iloilo', '5000', 0.00, 'images/user-icn.webp'),
(22, 'Kate', 'Kate Suplico', 'Suplico', 'Kate Suplico', 'kate@gmail.com', '09092345456', '0dead1dcb2d3ffccb45b53ec126967ed', 'Zone1, Igbaras', 1, '2025-10-14 05:17:20', 'Igbaras', 'Bgry. Baras', '5000', 45.00, 'images/user-icn.webp'),
(23, 'Ann', 'Ann Antonino', 'Antonino', 'Ann Antonino', 'ann2@gmail.com', '09092345456', '62e40d56609618f2928101b5e41da3ae', 'Tigbauan, Iloilo ZONE1', 1, '2025-10-22 02:10:45', 'Oton', 'Bgry. Baras Oton ', '5000', 35.00, 'uploads/profile_images/profile_1761099045_68f83d255674d.jpg'),
(24, 'Kyra', 'Kyra Antonino Antonino Antonino Antonino', 'Antonino', 'Kyra Antonino', 'kyra@gmail.com', '09092345456', '5b7fde0bb33ea5cf99221939f44791be', 'Tigbauan, Iloilo ZONE1', 1, '2025-10-20 17:03:23', 'Oton', 'Bgry. Baras Oton ', '5000', 35.00, 'uploads/profile_images/profile_1760979803_68f66b5b858a5.jpg'),
(25, 'Bell', 'Bell', 'Ann', 'Bell Ann', 'bell@gmail.cpm', '09092345456', '95959e6fa51147a533521895a47e2338', 'Brgy. Baras, Igbaras, Iloilo', 1, '2025-10-20 12:30:05', 'Igbaras', 'Bgry. Baras Igbaras', '5000', 45.00, 'uploads/profile_images/profile_1760963405_68f62b4d1af31.jpg'),
(26, 'Patt', 'John', 'Patrick Eigo', 'John Patrick Eigo', 'patt@gmail.com', '09092345456', 'd782ced531b23a523bd885d689446bf2', 'Zone1 Baras, Guimbal Iloilo', 1, '2025-10-25 16:23:18', 'Guimbal', 'Bgry. Baras Igbaras', '5000', 30.00, 'uploads/profile_images/profile_1761409398_68fcf976dc573.jpg'),
(27, 'katee', 'Kate Suplico', 'Suplico', 'Kate Suplico', 'keta@gmail.com', '09876543210', '71ae71565c931a0964e00f388cac88fe', 'ZONE 7 Brgy. Baras Guimbal, Iloilo', 1, '2025-10-28 15:21:06', 'Guimbal', 'Brgy. Baras', '5022', 30.00, 'uploads/profile_images/profile_1761664866_6900df6240df1.jpg'),
(28, 'kylie', 'Kylie', 'Suplico', 'Kylie Suplico', 'kyle@gmail.com', '09876543210', 'e29e16c4c4df5c332f87583939463079', 'ZONE 1 Brgy. Panaytayon, Igbaras, Iloilo', 1, '2025-10-28 15:36:32', 'Igbaras', ' Brgy. Panaytayon', '5022', 45.00, 'uploads/profile_images/profile_1761665792_6900e300ce858.jpg'),
(29, 'Love', 'Lovely', '123', 'Lovely 123', 'lovely@gmail.com', '09234567892', 'cac833d2935427f349ac26abffd1639e', 'Zone 7 Brgy. Baras, Guimbal, Iloilo', 1, '2026-02-10 04:46:04', 'Guimbal', 'Brgy. Baras Guimbal, Iloilo', '5022', 30.00, 'images/user-icn.webp'),
(30, 'angel', 'Angel', 'Locsin', 'Angel Locsin', 'angel@gmail.com', '09123456789', 'ab1dbd386662b62477b62087a389256a', 'Brgy. Baras, Guimbal, Iloilo', 1, '2026-03-07 03:51:04', 'Guimbal', 'Baras', '5000', 30.00, 'images/user-icn.webp'),
(31, 'lars', 'Lars', 'Haha', 'Lars Haha', 'larss@gmail.com', '0912345678', '5df5c54d34fe4fa089b1c53ecc2619a7', 'Brgy. Baras, Guimbal, Iloilo', 1, '2026-03-12 02:32:54', 'Guimbal', 'Brgy. Baras Guimbal, Iloilo', '5034', 30.00, 'images/user-icn.webp');

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
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`adm_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`rs_id`);

--
-- Indexes for table `delivery_drivers`
--
ALTER TABLE `delivery_drivers`
  ADD PRIMARY KEY (`driver_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `delivery_proofs`
--
ALTER TABLE `delivery_proofs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `driver_id` (`driver_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `iloilo_shipping_rates`
--
ALTER TABLE `iloilo_shipping_rates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `municipality` (`municipality`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `u_id` (`u_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `payment_proofs`
--
ALTER TABLE `payment_proofs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `remark`
--
ALTER TABLE `remark`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `res_category`
--
ALTER TABLE `res_category`
  ADD PRIMARY KEY (`c_id`);

--
-- Indexes for table `seafoods`
--
ALTER TABLE `seafoods`
  ADD PRIMARY KEY (`d_id`);

--
-- Indexes for table `stock_history`
--
ALTER TABLE `stock_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`u_id`);

--
-- Indexes for table `users_orders`
--
ALTER TABLE `users_orders`
  ADD PRIMARY KEY (`o_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `adm_id` int(222) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `rs_id` int(222) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `delivery_drivers`
--
ALTER TABLE `delivery_drivers`
  MODIFY `driver_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `delivery_proofs`
--
ALTER TABLE `delivery_proofs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `iloilo_shipping_rates`
--
ALTER TABLE `iloilo_shipping_rates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `payment_proofs`
--
ALTER TABLE `payment_proofs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `remark`
--
ALTER TABLE `remark`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=147;

--
-- AUTO_INCREMENT for table `res_category`
--
ALTER TABLE `res_category`
  MODIFY `c_id` int(222) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `seafoods`
--
ALTER TABLE `seafoods`
  MODIFY `d_id` int(222) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `stock_history`
--
ALTER TABLE `stock_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `u_id` int(222) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `users_orders`
--
ALTER TABLE `users_orders`
  MODIFY `o_id` int(222) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `delivery_proofs`
--
ALTER TABLE `delivery_proofs`
  ADD CONSTRAINT `delivery_proofs_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `delivery_proofs_ibfk_2` FOREIGN KEY (`driver_id`) REFERENCES `delivery_drivers` (`driver_id`);

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`u_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`u_id`) REFERENCES `users` (`u_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `payment_proofs`
--
ALTER TABLE `payment_proofs`
  ADD CONSTRAINT `payment_proofs_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_history`
--
ALTER TABLE `stock_history`
  ADD CONSTRAINT `stock_history_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `seafoods` (`d_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
