-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 25, 2025 at 01:26 PM
-- Server version: 10.3.39-MariaDB-0ubuntu0.20.04.2
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `alois_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `branch_masters`
--

CREATE TABLE `branch_masters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_status` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branch_masters`
--

INSERT INTO `branch_masters` (`id`, `name`, `company_id`, `created_at`, `updated_at`, `branch_status`) VALUES
(1, 'Mumbai', 2, '2025-08-19 02:22:51', '2025-08-19 02:22:51', 1),
(2, 'Kolkata', 2, '2025-08-19 02:22:51', '2025-08-19 02:22:51', 1),
(3, 'Pune', 2, '2025-08-19 02:22:51', '2025-08-19 02:22:51', 1),
(4, 'Bengaluru', 2, '2025-08-19 02:22:51', '2025-08-19 02:22:51', 1),
(5, 'Noida', 2, '2025-08-19 02:22:51', '2025-08-19 02:22:51', 1),
(6, 'Gurgaon', 2, '2025-08-19 02:22:51', '2025-08-19 02:22:51', 1),
(7, 'Trivandrum', 2, '2025-08-19 02:22:51', '2025-08-19 02:22:51', 1),
(8, 'Hyderabad', 2, '2025-08-19 02:22:51', '2025-08-19 02:22:51', 1),
(9, 'Chennai', 2, '2025-08-19 02:22:51', '2025-08-19 02:22:51', 1),
(10, 'Delhi', 2, '2025-08-19 02:22:51', '2025-08-19 02:22:51', 1),
(11, 'Vadodara', 2, '2025-08-19 02:22:51', '2025-08-19 02:22:51', 1),
(12, 'Ahmedabad', 2, '2025-08-19 02:22:51', '2025-08-19 02:22:51', 1),
(13, 'Vijaywada', 2, '2025-08-19 02:22:51', '2025-08-19 02:22:51', 1),
(14, 'Chennai', 2, '2025-08-19 02:22:51', '2025-08-19 02:22:51', 1),
(15, 'Gurugram', 2, '2025-08-19 02:22:51', '2025-08-19 02:22:51', 1),
(16, 'Remote', 2, '2025-08-19 02:22:51', '2025-08-19 02:22:51', 1),
(17, 'Warsaw', 2, '2025-08-19 02:22:51', '2025-08-19 02:22:51', 1),
(18, 'Surat', 2, '2025-08-19 02:22:51', '2025-08-19 02:22:51', 1),
(19, 'New Delhi', 2, '2025-08-19 02:22:51', '2025-08-19 02:22:51', 1),
(20, 'Nagpur', 2, '2025-08-19 02:22:51', '2025-08-19 02:22:51', 1),
(21, 'Pune', 2, '2025-08-19 02:22:51', '2025-08-19 02:22:51', 1),
(22, 'Noida', 2, '2025-08-19 02:22:51', '2025-08-19 02:22:51', 0),
(23, 'Hyderabad', 2, '2025-08-19 02:22:51', '2025-08-19 02:22:51', 0),
(24, 'Bengaluru', 2, '2025-08-19 02:22:51', '2025-08-19 02:22:51', 0),
(25, 'Surat', 2, '2025-08-19 02:22:51', '2025-08-19 02:22:51', 0),
(26, 'Kolkata', 2, '2025-08-19 02:22:51', '2025-08-19 02:22:51', 0),
(27, 'Chennai', 2, '2025-08-19 02:22:51', '2025-08-19 02:22:51', 0),
(28, 'Andhra Pradesh', 2, '2025-08-19 02:22:51', '2025-08-19 02:22:51', 0),
(29, 'Pune', 2, '2025-08-19 02:22:51', '2025-08-19 02:22:51', 0),
(30, 'Luckhnow', 2, '2025-08-19 02:22:51', '2025-08-19 02:22:51', 0),
(31, 'Mumbai', 2, '2025-08-19 02:22:51', '2025-08-19 02:22:51', 0),
(32, 'Coimbatore', 2, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(33, 'Indore', 2, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(34, 'Tamilnadu', 2, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(35, 'Haryana', 2, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(36, 'Mumbai-Jogeshwari', 2, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(37, 'Rajasthan', 2, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(38, 'Delhi', 2, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(39, 'Gurugram', 2, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(40, 'Gurgoan', 2, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(41, 'Warangal', 2, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(42, 'Faridabad', 2, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(43, 'Kochin', 2, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(44, 'Remote', 2, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(45, 'Hyderabad', 2, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(46, 'Visakhapatnam (Remote)', 2, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(47, 'Vadodara', 2, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(48, 'Trichy', 2, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(49, 'Vadodara', 3, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(50, 'Ahmedabad', 3, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(51, 'Vadodara', 3, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(52, 'Singapore', 1, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(53, 'Sydney', 1, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(54, 'Melbourne', 1, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(55, 'Boronia', 1, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(56, 'Perth', 1, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(57, 'Brisbane', 1, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(58, 'Manila', 1, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(59, 'Selangor', 1, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(60, 'Bangkok', 1, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(61, 'Jakarta', 1, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(62, 'Auckland', 1, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(63, 'Pathum Thani', 1, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(64, 'Kuala Lumpur', 1, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(65, 'Riyadh', 1, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(66, 'Kilsyth', 1, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(67, 'Sydney', 1, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(68, 'Melbourne', 1, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(69, 'Kuala Lumpur', 1, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(70, 'Selangor', 1, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(71, 'Brisbane', 1, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(72, 'Perth', 1, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(73, 'Hamilton', 1, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(74, 'Bulgaria', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(75, 'London', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(76, 'Malven', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(77, 'Nottingham', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(78, 'Malvern', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(79, 'Doncaster', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(80, 'Slovakia', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(81, 'Romania', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(82, 'Warsaw', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(83, 'Manchester', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(84, 'Wroclaw', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(85, 'Krakow', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(86, 'Lisbon', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(87, 'Taipei', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(88, 'Poland', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(89, 'UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(90, 'Belgium', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(91, 'Poznan', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(92, 'Germany', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(93, 'United Kingdom', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(94, 'Czech Republic', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(95, 'Hungary', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(96, 'Europe', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(97, 'Madrid', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(98, 'Stavanger', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(99, 'Czech Republic,Brno', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(100, 'Prague', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(101, 'Brno', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(102, 'Sofia', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(103, 'Buchdorf', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(104, 'Telford, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(105, 'Antwerp', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(106, 'Ankara', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(107, 'Remote', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 1),
(108, 'Warsaw', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(109, 'Wokingham', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(110, 'London', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(111, 'Telford', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(112, 'Budapest', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(113, 'Gdynia', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(114, 'Stockholm', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(115, 'Farnborough', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(116, 'Manchester', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(117, 'Cliché', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(118, 'Poland', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(119, 'Berkshire', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(120, 'Egham', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(121, 'Luxembourg', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(122, 'Exeter-UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(123, 'Liverpool', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(124, 'Islington', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(125, 'Ipswich, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(126, 'Netherlands', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(127, 'Swindon', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(128, 'Cambridgeshire, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(129, 'Milton Keynes, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(130, 'Remote', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(131, 'Bulgaria', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(132, 'New Shire Hall, Huntingdon, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(133, 'Redditch', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(134, 'Woolwick', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(135, 'Alconbury Office,UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(136, 'Enfield Civic Centre, Uk', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(137, 'Northampton, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(138, 'Krakow, Poland', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(139, 'Leyton, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(140, 'Bracknell, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(141, 'London, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(142, ' Kettring Office, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(143, ' Islington Office, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(144, ' Walsall, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(145, 'York, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(146, 'Paris, France', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(147, 'Ilford, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(148, 'Thamesmead, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(149, 'Woolwich, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(150, 'Rugby, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(151, 'Walsall, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(152, ' Redditch, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(153, 'Redditch, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(154, 'Hamilton Building, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(155, 'Laurence House - Fifth Floor, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(156, 'Dublin, Ireland', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(157, 'Edinburgh, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(158, 'Swindon, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(159, '5 Pancras Square , UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(160, 'Hybrid', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(161, 'Lambeth, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(162, ' Northampton, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(163, 'Southwark, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(164, 'Oldbury, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(165, 'Ireland', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(166, 'Rotherham, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(167, 'Lincoln, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(168, 'Cypruss', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(169, 'Waltham Forest, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(170, 'Exeter, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(171, 'Telford, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(172, 'Exeter, Devon EX1 3PB', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(173, 'Camden, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(174, 'Nottingham, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(175, 'Barnet', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(176, 'Sutton, Greater London, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(177, 'Chelmsford, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(178, 'Hackney, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(179, 'Wokingham, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(180, 'Gdansk', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(181, 'Berkshire, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(182, 'Amsterdam, Netherlands', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(183, 'Liverpool, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(184, 'Met Office', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(185, 'Essex Council', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(186, 'Glasgow, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(187, 'Birmingham, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(188, 'Finland', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(189, 'Kettering, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(190, 'London', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(191, 'Barnet, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(192, 'Croydon, London', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(193, 'Pembrokeshire, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(194, 'Nottinghamshire, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(195, 'Amsterdam', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(196, 'Charnwood, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(197, 'Slough, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0),
(198, 'Kingston upon Thames, UK', 4, '2025-08-19 02:22:52', '2025-08-19 02:22:52', 0);

-- --------------------------------------------------------

--
-- Table structure for table `business_unit_masters`
--

CREATE TABLE `business_unit_masters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `unit` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `unit_status` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `business_unit_masters`
--

INSERT INTO `business_unit_masters` (`id`, `unit`, `created_at`, `updated_at`, `company_id`, `unit_status`) VALUES
(1, 'Karthik Balachandra', '2025-08-18 03:43:32', '2025-08-18 03:43:32', 2, 1),
(2, 'Vinutha P', '2025-08-18 03:43:58', '2025-08-18 03:43:58', 2, 1),
(4, 'Vinutha P', '2025-08-18 03:44:45', '2025-08-18 03:44:45', 2, 0),
(5, 'Karthik Balachandra', '2025-08-18 03:44:59', '2025-08-18 03:44:59', 2, 0),
(6, 'Ashutosh Yadav', '2025-08-18 03:56:21', '2025-08-18 03:56:21', 1, 1),
(7, 'Ashutosh Yadav', '2025-08-18 03:56:43', '2025-08-18 03:56:43', 1, 0),
(8, 'Aashay Umratkar', '2025-08-18 03:57:27', '2025-08-18 03:57:27', 3, 1),
(10, 'Karthik Balachandra', '2025-08-18 04:02:06', '2025-08-18 04:02:06', 3, 1),
(11, 'Aashay Umratkar', '2025-08-18 04:03:56', '2025-08-18 04:03:56', 3, 0),
(12, 'Karthik Balachandra', '2025-08-18 04:04:06', '2025-08-18 04:04:06', 3, 0),
(14, 'Vikas Sharma', '2025-08-18 04:21:47', '2025-08-18 04:21:47', 4, 1),
(15, 'Vikas Sharma', '2025-08-18 04:21:56', '2025-08-18 04:21:56', 4, 0);

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_code` varchar(255) DEFAULT NULL,
  `client_name` varchar(255) NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_status` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `loaded_cost` int(11) DEFAULT NULL,
  `qualify_days` int(11) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `client_code`, `client_name`, `company_id`, `client_status`, `loaded_cost`, `qualify_days`, `phone`, `email`, `status`, `created_at`, `updated_at`) VALUES
(1, 'UKTP01', 'Wisestep', 4, 0, 0, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(2, 'UKTP02', 'WorkForce LogIQ', 4, 0, 0, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(3, 'UKTP03', 'HCL', 4, 0, 0, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(4, 'UKTP04', 'Mavenir', 4, 0, 0, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(5, 'UKTP05', 'Magnit Global', 4, 0, 0, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(6, 'UKTP06', 'Staffingdesk', 4, 0, 0, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(7, 'UKTP07', 'Matrix', 4, 0, 0, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(8, 'UKTP08', 'DOS6', 4, 0, 0, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(9, 'UKTP09', 'OPUS', 4, 0, 0, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(10, 'UKTP10', 'SelectHr', 4, 0, 0, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(11, 'UKTP11', 'HCL Poland', 4, 0, 0, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(12, 'UKTP12', 'Pacer Staffing', 4, 0, 0, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(13, 'UKTP13', 'Sandwell Council', 4, 0, 0, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(14, 'UKTP14', 'JISC', 4, 0, 0, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(15, 'UKTP15', 'Marsh McLennan', 4, 0, 0, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(16, 'UKTP16', 'Anaplan', 4, 0, 0, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(17, 'UKTP17', 'Essex County Council', 4, 0, 0, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(18, 'UKTP18', 'Atlassian', 4, 0, 0, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(19, 'UKTP19', 'Prism', 4, 0, 0, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(20, 'UKTP20', 'Bloom Procurement Services', 4, 0, 0, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(21, 'UKTP21', 'Matrix - PRISM', 4, 0, 0, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(22, 'UKPERM01', 'EXL', 4, 1, 5, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(23, 'UKPERM02', 'Wishmoor HC', 4, 1, 5, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(24, 'UKPERM03', 'Alacris HC', 4, 1, 5, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(25, 'UKPERM04', 'Gold Hill HC', 4, 1, 5, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(26, 'UKPERM05', 'Laso HC', 4, 1, 5, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(27, 'UKPERM06', 'Blast Marketing & Analytics', 4, 1, 5, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(28, 'UKPERM07', 'BDO', 4, 1, 5, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(29, 'UKPERM08', 'HCL', 4, 1, 5, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(30, 'UKPERM09', 'Hexaware', 4, 1, 5, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(31, 'UKPERM10', 'Espire Infolabs', 4, 1, 5, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(32, 'UKPERM11', 'Sub Vendor', 4, 1, 5, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(33, 'UKPERM12', 'HCL-P&G', 4, 1, 5, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(34, 'UKPERM13', 'HCL-J&J', 4, 1, 5, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(35, 'UKPERM14', 'HCL-GSK', 4, 1, 5, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(36, 'UKPERM15', 'Persistant Syatem', 4, 1, 5, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(37, 'UKPERM16', 'ING Hubs', 4, 1, 5, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(38, 'UKPERM17', 'Mphasis', 4, 1, 5, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(39, 'UKPERM18', 'Mavenir', 4, 1, 5, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(40, 'UKPERM19', 'Fiserv', 4, 1, 5, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(41, 'UKPERM20', 'ING Tech', 4, 1, 5, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(42, 'UKPERM21', 'EXL Europe', 4, 1, 5, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(43, 'UKPERM22', 'CommScope', 4, 1, 5, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(44, 'UKPERM23', 'Aggreko', 4, 1, 5, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(45, 'UKPERM24', 'Aristocrat Technologies', 4, 1, 5, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(46, 'UKPERM25', 'Keysight', 4, 1, 5, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(47, 'UKPERM26', 'JISC', 4, 1, 5, 30, NULL, NULL, 'active', '2025-08-20 05:10:22', '2025-08-20 05:10:22'),
(48, 'INDTP01', 'HCL Technologies LTD', 2, 0, 12, 30, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(49, 'INDTP02', 'HCM - Deloitte', 2, 0, 30, 30, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(50, 'INDTP03', 'Capgemini-QuessCorp', 2, 0, 10, 30, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(51, 'INDTP04', 'Deloitte Touche Tohmatsu India LLP', 2, 0, 15, 30, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(52, 'INDTP05', 'Wipro', 2, 0, 12, 30, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(53, 'INDTP06', 'Capgemini', 2, 0, 12, 30, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(54, 'INDTP07', 'IBM', 2, 0, 12, 30, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(55, 'INDTP08', 'Tata Elxsi', 2, 0, 12, 30, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(56, 'INDTP09', 'Qualitykiosk Technologies', 2, 0, 12, 30, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(57, 'INDTP10', 'NTT Data Services', 2, 0, 15, 30, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(58, 'INDTP11', 'Financial Software and Systems', 2, 0, 12, 30, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(59, 'INDTP12', 'Oracle India', 2, 0, 15, 30, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(60, 'INDTP13', 'Renuity', 2, 0, 10, 30, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(61, 'INDTP14', 'Mavenir', 2, 0, 8, 30, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(62, 'INDTP15', 'PricewaterhouseCoopers', 2, 0, 15, 30, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(63, 'INDTP16', 'Magnit-Vmware', 2, 0, 8, 30, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(64, 'INDTP17', 'Genpact', 2, 0, 10, 30, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(65, 'INDTP18', 'Rallis India Limited', 2, 0, 12, 30, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(66, 'INDTP19', 'Prodapt', 2, 0, 8, 30, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(67, 'INDTP20', 'Fulcrum Digital Pvt Ltd', 2, 0, 15, 30, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(68, 'INDTP21', 'MoonPay', 2, 0, 30, 30, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(69, 'INDTP22', 'WNS GLOBAL SERVICES PRIVATE LIMITED', 2, 0, 15, 30, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(70, 'INDTP23', 'Blend 360', 2, 0, 15, 30, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(71, 'INDTP24', 'Marsh McLennan', 2, 0, 8, 30, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(72, 'INDTP25', 'Fujitsu', 2, 0, 12, 30, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(73, 'INDTP26', 'Uber India Research and Development Private Limited', 2, 0, 8, 30, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(74, 'INDTP27', 'Infostretch Corporation (India) Pvt. Ltd', 2, 0, 15, 30, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(75, 'INDTP28', 'WORKFORCE LOGIQ INDIA PRIVATE LIMITED (IBM)', 2, 0, 12, 30, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(76, 'INDTP29', 'Paytm', 2, 0, 12, 30, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(77, 'INDTP30', 'Uber India Research and Development Private Limited (eTeam Infoservices Pvt. Ltd.)', 2, 0, 8, 30, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(78, 'INDTP31', 'Magnit – Intuit', 2, 0, 12, 30, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(79, 'INDPERM01', 'Sailfin', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(80, 'INDPERM02', 'SREI', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(81, 'INDPERM03', 'Bristlecone India Ltd', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(82, 'INDPERM04', 'Itarium Tech', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(83, 'INDPERM05', 'SunGroup India', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(84, 'INDPERM06', 'Xceedance', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(85, 'INDPERM07', 'Mistral', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(86, 'INDPERM08', 'Affine Analytics', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(87, 'INDPERM09', 'IBS', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(88, 'INDPERM10', 'NEC', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(89, 'INDPERM11', 'Accion Labs', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(90, 'INDPERM12', 'Teksystems Global Services', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(91, 'INDPERM13', 'Adecco (IBM)', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(92, 'INDPERM14', 'Qualfon', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(93, 'INDPERM15', 'JLL India', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(94, 'INDPERM16', 'AltenCalsoft Labs Pvt Ltd', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(95, 'INDPERM17', 'Redstone', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(96, 'INDPERM18', 'IT Trends Technologies', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(97, 'INDPERM19', 'Lafarge Holcim', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(98, 'INDPERM20', 'Fujitsu Consulting India Pvt Ltd', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(99, 'INDPERM21', 'HCL-Experis IT', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(100, 'INDPERM22', 'L&T', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(101, 'INDPERM23', 'KPMG India Services LLP', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(102, 'INDPERM24', 'HCL Technologies LTD', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(103, 'INDPERM25', 'Nelito', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(104, 'INDPERM26', 'LocalQueen', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(105, 'INDPERM27', 'IBM(Adecco)', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(106, 'INDPERM28', 'Wipro(Adecco)', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(107, 'INDPERM29', 'CIVICA', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(108, 'INDPERM30', 'L&T Technology Services', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(109, 'INDPERM31', 'Astral Steritech Pvt Ltd', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(110, 'INDPERM32', 'LTI', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(111, 'INDPERM33', 'Mcdermott', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(112, 'INDPERM34', 'LTTS', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(113, 'INDPERM35', 'Generac', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(114, 'INDPERM36', 'Ornnova', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(115, 'INDPERM37', 'Impetus', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(116, 'INDPERM38', 'Binary Semantics', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(117, 'INDPERM39', 'Infostretch Corporation (India) Pvt. Ltd.', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(118, 'INDPERM40', 'Deloitte Touche Tohmatsu India LLP', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(119, 'INDPERM41', 'Wipro', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(120, 'INDPERM42', 'Vanderlande', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(121, 'INDPERM43', 'Ninety One', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(122, 'INDPERM44', 'HCL-SourceOne', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(123, 'INDPERM45', 'Capgemini', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(124, 'INDPERM46', 'Prodapt', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(125, 'INDPERM47', 'Qualitykiosk Technologies', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(126, 'INDPERM48', 'Maersk', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(127, 'INDPERM49', 'IRIS Business Services Limited', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(128, 'INDPERM50', 'Jindal X', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(129, 'INDPERM51', 'Financial Software and Systems', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(130, 'INDPERM52', 'Solifi India', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(131, 'INDPERM53', 'Magnit(Adobe)', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(132, 'INDPERM54', 'Alois Poland-ING Hubs', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(133, 'INDPERM55', 'GoSwirl Technologies Pvt. Ltd.', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(134, 'INDPERM56', 'NTT Data Services', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(135, 'INDPERM57', 'Bloombay Enterprises Private Limited', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(136, 'INDPERM58', 'Sheejal Marketing and Trading Company', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(137, 'INDPERM59', 'QualityKiosk', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(138, 'INDPERM60', 'Apollo Homecare', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(139, 'INDPERM61', 'Amla Commerce', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(140, 'INDPERM62', 'ACN Healthcare', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(141, 'INDPERM63', 'Flydocs India Private Limited', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(142, 'INDPERM64', 'EXL Services India', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(143, 'INDPERM65', 'Fulcrum Digital', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(144, 'INDPERM66', 'Sutherland Global Services', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(145, 'INDPERM67', 'HCL', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(146, 'INDPERM68', 'CAMS', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(147, 'INDPERM69', 'TATA Elxsi', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30'),
(148, 'INDPERM70', 'PropLegit', 2, 1, 8, 60, NULL, NULL, 'active', '2025-08-21 00:55:30', '2025-08-21 00:55:30');

-- --------------------------------------------------------

--
-- Table structure for table `company_masters`
--

CREATE TABLE `company_masters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `region` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `to_emails` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`to_emails`)),
  `cc_emails` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`cc_emails`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `company_masters`
--

INSERT INTO `company_masters` (`id`, `name`, `region`, `created_at`, `updated_at`, `to_emails`, `cc_emails`) VALUES
(1, 'ALOIS AUSTRALIA PTY LTD', 'APAC', '2025-06-20 01:03:12', '2025-08-02 04:34:23', '[\"parth.boratkar@aloissolutions.com\"]', '[\"parth.boratkar@aloissolutions.com\"]'),
(2, 'ALOIS Technologies Pvt. Ltd.', 'India', '2025-06-20 01:03:12', '2025-08-07 01:05:11', '[\"parth.boratkar@aloissolutions.com\",\"krushal@qubetatechnolab.com\"]', '[\"parth.boratkar@aloissolutions.com\",\"manish.kalal@qubetatechnolab.com\"]'),
(3, 'AEGIS', 'Aegis', '2025-06-20 01:03:12', '2025-08-02 04:34:35', '[\"parth.boratkar@aloissolutions.com\"]', '[\"parth.boratkar@aloissolutions.com\"]'),
(4, 'ALOIS Technologies Limited', 'EU-UK', '2025-06-20 01:03:12', '2025-08-02 04:34:41', '[\"parth.boratkar@aloissolutions.com\"]', '[\"parth.boratkar@aloissolutions.com\"]');

-- --------------------------------------------------------

--
-- Table structure for table `consultants`
--

CREATE TABLE `consultants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `phone1` varchar(20) NOT NULL,
  `phone2` varchar(20) DEFAULT NULL,
  `email1` varchar(255) NOT NULL,
  `email2` varchar(255) DEFAULT NULL,
  `aadhaar` varchar(255) DEFAULT NULL,
  `pan` varchar(255) DEFAULT NULL,
  `po_copy` varchar(255) DEFAULT NULL,
  `extra_doc` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `consultants`
--

INSERT INTO `consultants` (`id`, `code`, `name`, `address`, `state`, `city`, `country`, `phone1`, `phone2`, `email1`, `email2`, `aadhaar`, `pan`, `po_copy`, `extra_doc`, `status`, `created_at`, `updated_at`) VALUES
(1, 'CE0286', 'Suhail Khan', 'TRP Mall, Bopal', 'Gujarat', 'Ahmedabad', 'India', '8141525263', NULL, 'suhailkhan@gmail.com', NULL, 'consultants/CE0286/images/wfeWaa5GU9FS8qrMdkBNh9Ggg5JlI6B8Otl57B3h.jpg', NULL, NULL, NULL, 'active', '2025-06-20 02:20:47', '2025-06-20 02:20:48');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `emp_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `role` enum('maker','checker','po_maker','po_checker','finance_maker','finance_checker','backout_maker','backout_checker') DEFAULT NULL,
  `checker_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_self_checker` tinyint(1) NOT NULL DEFAULT 0,
  `designation` enum('AM','DM','TL','Recruiter') NOT NULL,
  `status` enum('active','inactive') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `user_id`, `emp_id`, `name`, `company_id`, `email`, `phone`, `role`, `checker_id`, `is_self_checker`, `designation`, `status`, `created_at`, `updated_at`) VALUES
(3, 6, '2372', 'Twinkle Chauhan', 3, 'twinkle.chauhan@aloissolutions.com', '9664668299', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:42:46', '2025-08-18 04:42:46'),
(4, 7, '2411', 'Chaitanya Nandedkar', 3, 'chaitanya.nandedkar@aloissolutions.com', '7859922648', NULL, NULL, 0, 'TL', 'active', '2025-08-18 04:42:46', '2025-08-18 04:42:46'),
(5, 8, '2510', 'Tashmit Sawhney', 3, 'tashmit.sawhney@aloissolutions.com', '7600640120', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:42:47', '2025-08-18 04:42:47'),
(6, 9, '2511', 'Khushbu Porwal', 3, 'khushbu.porwal@aloissolutions.com', '8349485527', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:42:47', '2025-08-18 04:42:47'),
(7, 10, '105', 'Japan Bhatt', 2, 'japan.bhatt@aloissolutions.com', '9726587958', NULL, NULL, 0, 'DM', 'active', '2025-08-18 04:42:47', '2025-08-18 04:42:47'),
(8, 11, '325', 'Ruchir Shah', 2, 'ruchir.shah@aloissolutions.com', '9408080872', 'maker', NULL, 0, 'DM', 'active', '2025-08-18 04:42:47', '2025-08-18 05:20:21'),
(9, 12, '329', 'Dipak Parmar', 2, 'dipak.parmar@aloissolutions.com', '9726018980', NULL, NULL, 0, 'DM', 'active', '2025-08-18 04:42:48', '2025-08-18 04:42:48'),
(10, 13, '2304', 'Simit Mehta', 2, 'simit.mehta@aloissolutions.com', '8141811558', NULL, NULL, 0, 'DM', 'active', '2025-08-18 04:42:48', '2025-08-18 04:42:48'),
(11, 14, '2592', 'Bansal Patel', 2, 'bansal.patel@aloissolutions.com', '9737492542', NULL, NULL, 0, 'DM', 'active', '2025-08-18 04:42:48', '2025-08-18 04:42:48'),
(12, 15, '286', 'Divyesh Hargun', 2, 'divyesh.hargun@aloissolutions.com', '8306068622', NULL, NULL, 0, 'TL', 'active', '2025-08-18 04:42:49', '2025-08-18 04:42:49'),
(13, 16, '301', 'Uzeir Mal', 2, 'uzeir.mal@aloissolutions.com', '7600233092', NULL, NULL, 0, 'TL', 'active', '2025-08-18 04:42:49', '2025-08-18 04:42:49'),
(14, 17, '318', 'Rachana Sane', 2, 'rachana.sane@aloissolutions.com', '9998531374', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:42:49', '2025-08-18 04:42:49'),
(15, 18, '351', 'Peri Vegada', 2, 'peri.vegada@aloissolutions.com', '8200381562', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:42:50', '2025-08-18 04:42:50'),
(16, 19, '362', 'Robert Anthony', 2, 'robert.anthony@aloissolutions.com', '9409565926', NULL, NULL, 0, 'TL', 'active', '2025-08-18 04:42:50', '2025-08-18 04:42:50'),
(17, 20, '374', 'Khushboo Vanjani', 2, 'khushi.vanjani@aloissolutions.com', '8160658182', NULL, NULL, 0, 'TL', 'active', '2025-08-18 04:42:50', '2025-08-18 04:42:50'),
(18, 21, '457', 'Rahul Patel', 2, 'rahul.patel@aloissolutions.com', '8141255848', NULL, NULL, 0, 'TL', 'active', '2025-08-18 04:42:50', '2025-08-18 04:42:50'),
(19, 22, '475', 'Ullas Mathur', 2, 'ullas.mathur@aloissolutions.com', '7073250429', NULL, NULL, 0, 'TL', 'active', '2025-08-18 04:42:51', '2025-08-18 04:42:51'),
(20, 23, '485', 'Akhilesh Nair', 2, 'akhilesh.nair@aloissolutions.com', '8866229309', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:42:51', '2025-08-18 04:42:51'),
(21, 24, '488', 'Lopa Pancholi', 2, 'lopa.pancholi@aloissolutions.com', '8511130387', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:42:51', '2025-08-18 04:42:51'),
(22, 25, '497', 'Megha Vaghasia', 2, 'megha.vaghasia@aloissolutions.com', '9714731312', NULL, NULL, 0, 'TL', 'active', '2025-08-18 04:42:52', '2025-08-18 04:42:52'),
(23, 26, '512', 'Harsh Shah', 2, 'harsh.shah@aloissolutions.com', '7874689792', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:42:52', '2025-08-18 04:42:52'),
(24, 27, '515', 'Vahbiz Patel', 2, 'vahbiz.patel@aloissolutions.com', '9737723230', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:42:52', '2025-08-18 04:42:52'),
(25, 28, '516', 'Ruknaaz Patel', 2, 'ruknaaz.patel@aloissolutions.com', '9081772691', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:42:53', '2025-08-18 04:42:53'),
(26, 29, '519', 'Akshay Shah', 2, 'akshay.shah@aloissolutions.com', '8735807994', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:42:53', '2025-08-18 04:42:53'),
(27, 30, '682', 'Megha Khubchandani', 2, 'megha.khubchandani@aloissolutions.com', '8980125587', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:42:53', '2025-08-18 04:42:53'),
(28, 31, '890', 'Yukta Hairav', 2, 'yukta.hairav@aloissolutions.com', '7228969954', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:42:53', '2025-08-18 04:42:53'),
(29, 32, '900', 'Pooja Sah', 2, 'pooja.sah@aloissolutions.com', '9265618101', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:42:54', '2025-08-18 04:42:54'),
(30, 33, '2016', 'Sangeeta Wadhwani', 2, 'sangeeta.wadhwani@aloissolutions.com', '9033209259', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:42:54', '2025-08-18 04:42:54'),
(31, 34, '2296', 'Chiranjeev Iyer', 2, 'chiranjeev.iyer@aloissolutions.com', '9998692745', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:42:54', '2025-08-18 04:42:54'),
(32, 35, '2319', 'Sarang Sangamnerkar', 2, 'sarang.sangamnekar@aloissolutions.com', '9714430448', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:42:55', '2025-08-18 04:42:55'),
(33, 36, '2333', 'Jitendra Vyas', 2, 'jitendra.vyas@aloissolutions.com', '9898546991', NULL, NULL, 0, 'TL', 'active', '2025-08-18 04:42:55', '2025-08-18 04:42:55'),
(34, 37, '2356', 'Nishant Nair', 2, 'nishant.nair@aloissolutions.com', '7567567755', NULL, NULL, 0, 'TL', 'active', '2025-08-18 04:42:55', '2025-08-18 04:42:55'),
(35, 38, '2389', 'Shubhi Sharma', 2, 'shubhi.sharma@aloissolutions.com', '8305121617', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:42:56', '2025-08-18 04:42:56'),
(36, 39, '2391', 'Jairaj Makwana', 2, 'jairaj.makwana@aloissolutions.com', '9724116385', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:42:56', '2025-08-18 04:42:56'),
(37, 40, '2398', 'Bhavika Kataria', 2, 'bhavika.kataria@aloissolutions.com', '9328613105', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:42:56', '2025-08-18 04:42:56'),
(38, 41, '2444', 'Binal Lalwani', 2, 'binal.lalwani@aloissolutions.com', '9016621072', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:42:56', '2025-08-18 04:42:56'),
(39, 42, '2445', 'Jami Patel', 2, 'jami.patel@aloissolutions.com', '8320717431', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:42:57', '2025-08-18 04:42:57'),
(40, 43, '2477', 'Archita Parmar', 2, 'archita.parmar@aloissolutions.com', '7016108455', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:42:57', '2025-08-18 04:42:57'),
(41, 44, '2478', 'Saniya Chavda', 2, 'saniya.chavda@aloissolutions.com', '7990530650', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:42:57', '2025-08-18 04:42:57'),
(42, 45, '2496', 'Arun Nishad', 2, 'arun.nishad@aloissolutions.com', '7622873667', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:42:58', '2025-08-18 04:42:58'),
(43, 46, '2513', 'Ketan Parmar', 2, 'ketan.parmar@aloissolutions.com', '9737755903', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:42:58', '2025-08-18 04:42:58'),
(44, 47, '2539', 'Pooja Chavan', 2, 'pooja.chavan@aloissolutions.com', '9313129045', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:42:58', '2025-08-18 04:42:58'),
(45, 48, '2551', 'Salomee Dcruz', 2, 'salomee.dcruz@aloissolutions.com', '8460720776', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:42:59', '2025-08-18 04:42:59'),
(46, 49, '2570', 'Heli Gohil', 2, 'heli.gohil@aloissolutions.com', '9924151759', NULL, NULL, 0, 'TL', 'active', '2025-08-18 04:42:59', '2025-08-18 04:42:59'),
(47, 50, '2574', 'Heli Kamnani', 2, 'heli.kamnani@aloissolutions.com', '9265111857', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:42:59', '2025-08-18 04:42:59'),
(48, 51, '2578', 'Dev Shah', 2, 'dev.shah@aloissolutions.com', '7228080099', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:42:59', '2025-08-18 04:42:59'),
(49, 52, '2583', 'Nikul Patel', 2, 'nikul.p@aloissolutions.com', '9016060907', NULL, NULL, 0, 'TL', 'active', '2025-08-18 04:43:00', '2025-08-18 04:43:00'),
(50, 54, '2585', 'Gaurav Kulkarni', 2, 'gaurav.kulkarni@aloissolutions.com', '7878317606', NULL, NULL, 0, 'TL', 'active', '2025-08-18 04:43:00', '2025-08-18 04:43:00'),
(51, 55, '322', 'Mujahid Patni', 1, 'mujahid.patni@aloissolutions.com.au', '7600156799', NULL, NULL, 0, 'TL', 'active', '2025-08-18 04:43:00', '2025-08-18 04:43:00'),
(52, 56, '363', 'Abutalib Shaikh ', 1, 'shaikh.abutalib@aloissolutions.com.au', '7984787830', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:43:01', '2025-08-18 04:43:01'),
(53, 57, '779', 'Anjali Wadhwani', 1, 'anjali.wadhwani@aloissolutions.com.au', '7223805638', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:43:01', '2025-08-18 04:43:01'),
(54, 58, '953', 'Gaurav Kataria', 1, 'gaurav.kataria@aloissolutions.com.au', '9016773774', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:43:01', '2025-08-18 04:43:01'),
(55, 59, '2308', 'Vedant Mane', 1, 'vedant.mane@aloissolutions.com.au', '9106313298', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:43:01', '2025-08-18 04:43:01'),
(56, 60, '2309', 'Neel Patel', 1, 'neel.patel@aloissolutions.com.au', '7874919145', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:43:02', '2025-08-18 04:43:02'),
(57, 61, '2316', 'Deeksha Shetty', 1, 'deeksha.shetty@aloissolutions.com.au', '6353478166', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:43:02', '2025-08-18 04:43:02'),
(58, 62, '2366', 'Gaurav Solanki', 1, 'gaurav.solanki@aloissolutions.com.au', '8128211650', NULL, NULL, 0, 'AM', 'active', '2025-08-18 04:43:02', '2025-08-18 04:43:02'),
(59, 63, '2409', 'Suraj Gupta', 1, 'suraj.gupta@aloissolutions.com.au', '8866778601', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:43:03', '2025-08-18 04:43:03'),
(60, 64, '2475', 'Azraa Chaviwala', 1, 'azraa.chaviwala@aloissolutions.com.au', '8128881202', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:43:03', '2025-08-18 04:43:03'),
(61, 65, '2476', 'Mariya Dhal', 1, 'mariya.dhal@aloissolutions.com.au', '9687556370', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:43:03', '2025-08-18 04:43:03'),
(62, 66, '2537', 'Kritika Gohil', 1, 'kritika.gohil@aloissolutions.com.au', '9687204858', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:43:04', '2025-08-18 04:43:04'),
(63, 67, '2577', 'Dhara Chaudhari', 1, 'dhara.chaudhari@aloissolutions.com.au', '9510749232', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:43:04', '2025-08-18 04:43:04'),
(64, 68, '403', 'Vikas Sharma', 4, 'vikas.sharma@aloissolutions.com', '9723342225', 'checker', NULL, 0, 'DM', 'active', '2025-08-18 04:43:04', '2025-08-18 04:43:04'),
(65, 69, '453', 'Namrata Patel', 4, 'namrata.patel@aloissolutions.com', '8980342380', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:43:04', '2025-08-18 04:43:04'),
(66, 70, '2257', 'Meera Parmar', 4, 'meera.parmar@aloissolutions.com', '7043380098', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:43:05', '2025-08-18 04:43:05'),
(67, 71, '2399', 'Tanay Sudarshan', 4, 'tanay.sudarshan@aloissolutions.com', '7294018347', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:43:05', '2025-08-18 04:43:05'),
(68, 72, '2403', 'Himani Bhatt', 4, 'himani.bhatt@aloissolutions.com', '7600265927', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:43:05', '2025-08-18 04:43:05'),
(69, 73, '2407', 'Ankur Patel', 4, 'ankur.patel@aloissolutions.com', '6352319624', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:43:06', '2025-08-18 04:43:06'),
(70, 74, '2447', 'Dhvani Shah', 4, 'dhvani.shah@aloissolutions.com', '7977319809', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:43:06', '2025-08-18 04:43:06'),
(71, 75, '2464', 'Vaishnavi Tripathi', 4, 'vaishnavi.tripathi@aloissolutions.com', '8758247756', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:43:06', '2025-08-18 04:43:06'),
(72, 76, '2484', 'Sumit Randhawa', 4, 'sumit.randhawa@aloissolutions.com', '8376889292', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:43:07', '2025-08-18 04:43:07'),
(73, 77, '2491', 'Vidha Kathait', 4, 'vidha.kathait@aloissolutions.com', '7877114231', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:43:07', '2025-08-18 04:43:07'),
(74, 78, '2492', 'Isha Vaya', 4, 'isha.vaya@aloissolutions.com', '7567737454', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:43:07', '2025-08-18 04:43:07'),
(75, 79, '2493', 'Ali Sulaimani', 4, 'ali.imran@aloissolutions.com', '7387387644', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:43:07', '2025-08-18 04:43:07'),
(76, 80, '2494', 'Jiya Jethva', 4, 'jiya.jethva@aloissolutions.com', '8160872805', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:43:08', '2025-08-18 04:43:08'),
(77, 81, '2497', 'Shubham Pandey', 4, 'shubham.pandey@aloissolutions.com', '8932820534', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:43:08', '2025-08-18 04:43:08'),
(78, 82, '2540', 'Ishan Sharma', 4, 'ishan.sharma@aloissolutions.com', '9893737316', NULL, NULL, 0, 'Recruiter', 'active', '2025-08-18 04:43:08', '2025-08-18 04:43:08'),
(79, 83, '10', 'Ashutosh Yadav', 1, 'ashutosh@aloissolutions.com.au', '7227937399', 'checker', NULL, 0, 'DM', 'active', '2025-08-18 04:59:53', '2025-08-18 04:59:53'),
(80, 84, '2123', 'Vinutha P', 2, 'vinutha.p@aloissolutions.com', '9535058295', 'checker', NULL, 0, 'AM', 'active', '2025-08-18 04:59:54', '2025-08-18 04:59:54'),
(81, 85, '399', 'Aashay Umratkar', 3, 'aashay@aloissolutions.com', '9979143736', 'checker', NULL, 0, 'DM', 'active', '2025-08-18 04:59:54', '2025-08-18 04:59:54'),
(82, 86, '253', 'Ashish Krishnan', 2, 'ashish.krishnan@aloissolutions.com', '9638385374', 'maker', NULL, 0, 'AM', 'active', '2025-08-18 05:02:09', '2025-08-18 05:02:09'),
(83, 87, '320', 'Bablu Pandey', 2, 'bablu.pandey@aloissolutions.com.au', '6354310074', 'maker', NULL, 0, 'TL', 'active', '2025-08-18 05:03:04', '2025-08-18 05:03:04'),
(84, 88, '323', 'Anamika Singh', 2, 'anamika.singh@aloissolutions.com', '9662448124', 'maker', NULL, 0, 'AM', 'active', '2025-08-18 05:03:51', '2025-08-18 05:03:51'),
(85, 89, '444', 'Akash Shrivastav', 2, 'akash.shrivastav@aloissolutions.com', '9879046337', 'maker', NULL, 0, 'AM', 'active', '2025-08-18 05:05:32', '2025-08-18 05:05:32'),
(86, 90, '530', 'Pooja Singh', 2, 'pooja.singh@aloissolutions.com', '8155988029', 'maker', NULL, 0, 'AM', 'active', '2025-08-18 05:06:21', '2025-08-18 05:06:21'),
(87, 91, '560', 'Shehzad Vandriwala', 2, 'shehzad.vandriwala@aloissolutions.com', '9429824892', 'maker', NULL, 0, 'TL', 'active', '2025-08-18 05:07:05', '2025-08-18 05:07:05'),
(88, 92, '592', 'Priyanka Soni', 1, 'priyanka.soni@aloissolutions.com.au', '8200724818', 'maker', NULL, 0, 'TL', 'active', '2025-08-18 05:08:37', '2025-08-18 05:08:37'),
(89, 93, '895', 'Piyush Singh', 4, 'piyush.singh@aloissolutions.com', '9033215805', 'maker', NULL, 0, 'AM', 'active', '2025-08-18 05:09:53', '2025-08-18 05:09:53'),
(90, 94, '925', 'Pratiksha Parmar', 4, 'pratiksha.parmar@aloissolutions.com', '9033569842', 'maker', NULL, 0, 'AM', 'active', '2025-08-18 05:10:52', '2025-08-18 05:10:52'),
(91, 95, '2047', 'Rishwinder Ghuman', 3, 'rashwinder.singh@aloissolutions.com', '8980713648', 'maker', NULL, 0, 'TL', 'active', '2025-08-18 05:11:37', '2025-08-18 05:11:37'),
(92, 96, '2361', 'Gurinder Nandra', 4, 'gurinder.nandra@aloissolutions.com', '9925604096', 'maker', NULL, 0, 'DM', 'active', '2025-08-18 05:12:19', '2025-08-18 05:12:19');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_seekers`
--

CREATE TABLE `job_seekers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `location_id` bigint(20) UNSIGNED NOT NULL,
  `hire_type` varchar(255) DEFAULT NULL,
  `business_unit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `am_id` bigint(20) UNSIGNED DEFAULT NULL,
  `dm_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tl_id` bigint(20) UNSIGNED DEFAULT NULL,
  `recruiter_id` bigint(20) UNSIGNED DEFAULT NULL,
  `consultant_code` varchar(255) DEFAULT NULL,
  `consultant_name` varchar(255) DEFAULT NULL,
  `skill` varchar(255) DEFAULT NULL,
  `sap_id` varchar(255) DEFAULT NULL,
  `status_id` bigint(20) UNSIGNED NOT NULL,
  `form_status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `process_status` int(11) NOT NULL DEFAULT 0,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `poc` varchar(255) DEFAULT NULL,
  `client_reporting_manager` varchar(255) DEFAULT NULL,
  `quarter` varchar(255) DEFAULT NULL,
  `selection_date` date DEFAULT NULL,
  `select_month` varchar(255) DEFAULT NULL,
  `offer_date` date DEFAULT NULL,
  `join_date` date DEFAULT NULL,
  `join_month` varchar(7) DEFAULT NULL,
  `join_year` int(11) DEFAULT NULL,
  `qly_date` date DEFAULT NULL,
  `backout_term_date` date DEFAULT NULL,
  `backout_term_month` varchar(7) DEFAULT NULL,
  `backout_term_year` int(11) DEFAULT NULL,
  `type_of_attrition` enum('Voluntary','Involuntary') DEFAULT NULL,
  `reason_of_rejection` text DEFAULT NULL,
  `bo_type` enum('Client BO','Candidate BO') DEFAULT NULL,
  `bd_absconding_term` varchar(255) DEFAULT NULL,
  `reason_of_attrition` text DEFAULT NULL,
  `currency` varchar(255) DEFAULT NULL,
  `po_end_date` date DEFAULT NULL,
  `po_end_month` varchar(7) DEFAULT NULL,
  `po_end_year` int(11) DEFAULT NULL,
  `hire_status` varchar(255) DEFAULT NULL,
  `pay_rate` decimal(10,2) DEFAULT NULL,
  `pay_rate_usd` decimal(10,2) DEFAULT NULL,
  `loaded_cost` decimal(10,2) NOT NULL,
  `pay_rate_1` decimal(10,2) DEFAULT NULL,
  `bill_rate` decimal(10,2) DEFAULT NULL,
  `bill_rate_usd` decimal(10,2) DEFAULT NULL,
  `basic_pay_rate` decimal(10,2) DEFAULT NULL,
  `benefits` decimal(10,2) DEFAULT NULL,
  `gp_month` decimal(10,2) DEFAULT NULL,
  `gp_hour` decimal(10,2) DEFAULT NULL,
  `gp_hour_usd` decimal(10,2) DEFAULT NULL,
  `otc` decimal(10,2) DEFAULT NULL,
  `otc_split` decimal(10,2) DEFAULT NULL,
  `msp_fees` decimal(10,2) DEFAULT NULL,
  `final_gp` decimal(10,2) DEFAULT NULL,
  `percentage_gp` decimal(10,2) DEFAULT NULL,
  `ctc_offered` decimal(10,2) DEFAULT NULL,
  `billing_value` decimal(10,2) DEFAULT NULL,
  `loaded_gp` decimal(10,2) DEFAULT NULL,
  `final_billing_value` decimal(10,2) DEFAULT NULL,
  `actual_billing_value` decimal(10,2) DEFAULT NULL,
  `invoice_no` varchar(255) DEFAULT NULL,
  `end_client` varchar(255) DEFAULT NULL,
  `lob` varchar(255) DEFAULT NULL,
  `remark1` text DEFAULT NULL,
  `remark2` text DEFAULT NULL,
  `sources` varchar(255) DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  `domain` varchar(255) DEFAULT NULL,
  `checker_id` bigint(20) UNSIGNED DEFAULT NULL,
  `maker_id` bigint(20) UNSIGNED DEFAULT NULL,
  `po_maker_id` bigint(20) UNSIGNED DEFAULT NULL,
  `po_checker_id` bigint(20) UNSIGNED DEFAULT NULL,
  `finance_maker_id` bigint(20) UNSIGNED DEFAULT NULL,
  `finance_checker_id` bigint(20) UNSIGNED DEFAULT NULL,
  `backout_maker_id` bigint(20) UNSIGNED DEFAULT NULL,
  `backout_checker_id` bigint(20) UNSIGNED DEFAULT NULL,
  `job_seeker_type` enum('Temporary','Permanent') NOT NULL DEFAULT 'Temporary',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `job_seekers`
--

INSERT INTO `job_seekers` (`id`, `company_id`, `location_id`, `hire_type`, `business_unit_id`, `am_id`, `dm_id`, `tl_id`, `recruiter_id`, `consultant_code`, `consultant_name`, `skill`, `sap_id`, `status_id`, `form_status`, `process_status`, `client_id`, `poc`, `client_reporting_manager`, `quarter`, `selection_date`, `select_month`, `offer_date`, `join_date`, `join_month`, `join_year`, `qly_date`, `backout_term_date`, `backout_term_month`, `backout_term_year`, `type_of_attrition`, `reason_of_rejection`, `bo_type`, `bd_absconding_term`, `reason_of_attrition`, `currency`, `po_end_date`, `po_end_month`, `po_end_year`, `hire_status`, `pay_rate`, `pay_rate_usd`, `loaded_cost`, `pay_rate_1`, `bill_rate`, `bill_rate_usd`, `basic_pay_rate`, `benefits`, `gp_month`, `gp_hour`, `gp_hour_usd`, `otc`, `otc_split`, `msp_fees`, `final_gp`, `percentage_gp`, `ctc_offered`, `billing_value`, `loaded_gp`, `final_billing_value`, `actual_billing_value`, `invoice_no`, `end_client`, `lob`, `remark1`, `remark2`, `sources`, `source`, `domain`, `checker_id`, `maker_id`, `po_maker_id`, `po_checker_id`, `finance_maker_id`, `finance_checker_id`, `backout_maker_id`, `backout_checker_id`, `job_seeker_type`, `created_at`, `updated_at`) VALUES
(1, 2, 22, 'TP', 5, 82, 8, NULL, 23, NULL, 'Devender verma', 'Fresher', NULL, 7, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-29', NULL, '2025-08-11', '2025-08-18', '2025-10', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:29:40', '2025-08-21 02:29:40'),
(2, 2, 22, 'TP', NULL, 82, 9, 34, 40, NULL, 'Praneet Tripathi', 'Fresher', NULL, 8, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-29', NULL, NULL, '2025-08-04', '2025-09', 2025, NULL, '2025-08-18', NULL, 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Candidate BO', NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:29:40', '2025-08-21 02:29:40'),
(3, 2, 22, 'TP', NULL, 82, 9, 34, 40, NULL, 'Rudra Pratap Singh', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-29', NULL, NULL, '2025-08-04', '2025-09', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:29:40', '2025-08-21 02:29:40'),
(4, 2, 22, 'TP', NULL, 82, 9, 34, 43, NULL, 'Saurabh Yadav', 'Fresher', NULL, 7, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-29', NULL, '2025-08-11', '2025-08-19', '2025-10', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:29:40', '2025-08-21 02:29:40'),
(5, 2, 22, 'TP', NULL, 82, 9, 34, 37, NULL, 'Shubham Sharma', 'Fresher', NULL, 7, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-29', NULL, '2025-08-11', '2025-08-18', '2025-10', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:29:40', '2025-08-21 02:29:40'),
(6, 2, 22, 'TP', NULL, 85, 9, 19, 29, NULL, 'Neelesh Chaturvedi', 'Fresher', NULL, 8, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-29', NULL, NULL, '2025-08-04', '2025-09', 2025, NULL, '2025-08-04', NULL, 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Candidate BO', NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:29:40', '2025-08-21 02:29:40'),
(7, 2, 22, 'TP', NULL, 85, 10, 18, 26, NULL, 'Dhruv Jha', 'Fresher', NULL, 7, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-29', NULL, '2025-08-11', '2025-08-19', '2025-10', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:29:40', '2025-08-21 02:29:40'),
(8, 2, 22, 'TP', NULL, 85, NULL, NULL, NULL, NULL, 'Aakanksha Saini', 'Fresher', NULL, 7, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-29', NULL, '2025-08-11', '2025-08-18', '2025-10', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:29:40', '2025-08-21 02:29:40'),
(9, 2, 22, 'TP', NULL, 85, NULL, NULL, NULL, NULL, 'Aayushh Yadav', 'Fresher', NULL, 7, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-29', NULL, '2025-08-11', '2025-08-18', '2025-10', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:29:40', '2025-08-21 02:29:40'),
(10, 2, 22, 'TP', NULL, 85, NULL, NULL, NULL, NULL, 'Sourabh Sharma', 'Fresher', NULL, 7, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-29', NULL, '2025-08-11', '2025-08-19', '2025-10', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:29:40', '2025-08-21 02:29:40'),
(11, 2, 22, 'TP', NULL, 85, 8, NULL, 36, NULL, 'Girisha Jain', 'Fresher', NULL, 8, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-29', NULL, NULL, '2025-08-04', '2025-09', 2025, NULL, '2025-08-04', NULL, 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Candidate BO', NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:29:40', '2025-08-21 02:29:40'),
(12, 2, 24, 'TP', NULL, 85, 8, NULL, 23, NULL, 'Yogesh D', 'Interview Coordinator', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-31', NULL, NULL, '2025-08-06', '2025-09', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25000.00, NULL, 3000.00, 28000.00, 29998.98, NULL, NULL, NULL, 4998.98, NULL, NULL, NULL, NULL, 0.00, 1998.98, 7.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:29:40', '2025-08-21 02:29:40'),
(13, 2, 24, 'TP', NULL, 84, 9, 19, 20, NULL, 'Nabanita Paul', 'Interview Coordinator', NULL, 7, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-31', NULL, '2025-08-05', '2025-08-08', '2025-09', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25000.00, NULL, 3000.00, 28000.00, 29998.98, NULL, NULL, NULL, 4998.98, NULL, NULL, NULL, NULL, 0.00, 1998.98, 7.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:29:40', '2025-08-21 02:29:40'),
(14, 2, 39, 'TP', NULL, 84, 9, 34, NULL, NULL, 'Srishti Gupta', 'Java Developer (Apache Flink)', NULL, 1, 'Approved', 2, 57, 'Stephen Anthony', NULL, 'Q32025', '2025-07-30', NULL, NULL, '2025-08-06', '2025-09', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 183333.00, NULL, 27500.00, 210833.33, 254475.00, NULL, NULL, NULL, 71141.67, NULL, NULL, NULL, NULL, 0.00, 43641.67, 17.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:29:40', '2025-08-21 02:29:40'),
(15, 2, 27, 'TP', NULL, 84, 9, 19, 28, NULL, 'Sameer Shaikh', 'Senior Functional Consultant - CRM', NULL, 7, 'Approved', 2, 57, 'Suresh S', NULL, 'Q32025', '2025-08-03', NULL, '2025-08-05', '2025-08-13', '2025-09', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 166666.00, NULL, 24999.90, 191665.90, 214346.25, NULL, NULL, NULL, 47680.25, NULL, NULL, NULL, NULL, 0.00, 22680.35, 11.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:29:40', '2025-08-21 02:29:40'),
(16, 2, 31, 'TP', NULL, 84, 8, NULL, NULL, NULL, 'Karishma Rai', 'PMO', NULL, 7, 'Approved', 2, 53, 'Dhamodharam', NULL, 'Q32025', '2025-08-01', NULL, '2025-08-04', '2025-08-07', '2025-09', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 125000.00, NULL, 15000.00, 140000.00, 152499.60, NULL, NULL, NULL, 27499.60, NULL, NULL, NULL, NULL, 0.00, 12499.60, 8.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:29:40', '2025-08-21 02:29:40'),
(17, 2, 39, 'TP', NULL, 84, 9, 34, 27, NULL, 'Sahil Lamba', 'Java Developer (Apache Flink)', NULL, 7, 'Approved', 2, 57, 'Stephen Anthony', NULL, 'Q32025', '2025-07-30', NULL, '2025-08-04', '2025-08-06', '2025-09', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 183333.00, NULL, 27500.00, 210833.33, 254475.00, NULL, NULL, NULL, 71141.67, NULL, NULL, NULL, NULL, 0.00, 43641.67, 17.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:29:40', '2025-08-21 02:29:40'),
(18, 2, 39, 'TP', NULL, 84, 9, 19, 28, NULL, 'Shivansh Rawat', 'Implementation Specialist', NULL, 7, 'Approved', 2, 57, 'Pooja Prabhuraj', NULL, 'Q32025', '2025-08-06', NULL, '2025-08-07', '2025-08-13', '2025-09', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 134167.00, NULL, 20125.00, 154291.66, 187920.00, NULL, NULL, NULL, 53753.34, NULL, NULL, NULL, NULL, 0.00, 33628.34, 18.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:29:40', '2025-08-21 02:29:40'),
(19, 2, 22, 'TP', NULL, 86, 8, NULL, 15, NULL, 'Luv Dixit', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-07', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:29:40', '2025-08-21 02:29:40'),
(20, 2, 22, 'TP', NULL, 86, 8, NULL, 15, NULL, 'Jitendra Kumar', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-07', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:29:40', '2025-08-21 02:29:40'),
(21, 2, 22, 'TP', NULL, 86, 8, NULL, 36, NULL, 'Tanu Bhaskar', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-07', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:29:40', '2025-08-21 02:29:40'),
(22, 2, 22, 'TP', NULL, 86, 8, NULL, 23, NULL, 'Shailja Choudhary', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-07', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:29:40', '2025-08-21 02:29:40'),
(23, 2, 22, 'TP', NULL, 86, 8, NULL, 15, NULL, 'Tushar Chauhan', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-07', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:29:40', '2025-08-21 02:29:40'),
(24, 2, 48, 'TP', NULL, 86, 8, NULL, 23, NULL, 'A Faseela', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-11', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:29:40', '2025-08-21 02:29:40'),
(25, 2, 48, 'TP', NULL, 86, 8, NULL, 23, NULL, 'Heraa Joy', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-11', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:29:40', '2025-08-21 02:29:40'),
(26, 2, 48, 'TP', NULL, 82, 8, NULL, 36, NULL, 'Inisha K', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-11', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:29:40', '2025-08-21 02:29:40'),
(27, 2, 48, 'TP', NULL, 82, 8, NULL, 36, NULL, 'Susmitha', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-11', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:29:40', '2025-08-21 02:29:40'),
(28, 2, 48, 'TP', NULL, 82, 8, NULL, 36, NULL, 'Yuvanika S', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-11', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:29:40', '2025-08-21 02:29:40'),
(29, 2, 48, 'TP', NULL, 82, 10, 22, 39, NULL, 'Bhuvana A', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-11', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:29:40', '2025-08-21 02:29:40'),
(30, 2, 48, 'TP', NULL, 82, 10, 22, 42, NULL, 'Deepika B', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-11', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:29:40', '2025-08-21 02:29:40'),
(31, 2, 48, 'TP', NULL, 82, 10, 18, 26, NULL, 'Saravana Moorthy', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-11', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:29:40', '2025-08-21 02:29:40'),
(32, 2, 48, 'TP', NULL, 82, 9, 34, 35, NULL, 'Charudesna K', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-11', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:29:40', '2025-08-21 02:29:40'),
(33, 2, 48, 'TP', NULL, 82, 9, 34, 40, NULL, 'Vahithulla A', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-11', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:29:41', '2025-08-21 02:29:41'),
(34, 2, 48, 'TP', NULL, 85, 10, 18, NULL, NULL, 'Swetha G', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-11', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:29:41', '2025-08-21 02:29:41'),
(35, 2, 48, 'TP', NULL, 85, 8, NULL, 36, NULL, 'Yosuva inbakumar', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-11', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:29:41', '2025-08-21 02:29:41'),
(36, 2, 24, 'TP', NULL, 85, 8, NULL, NULL, NULL, 'Sayapaneni Manoj', 'TIBCO Developer', NULL, 2, 'Approved', 2, 53, 'Gokul P', NULL, 'Q32025', '2025-08-13', NULL, '2025-08-19', '2025-08-20', '2025-10', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 166666.00, NULL, 19999.92, 186665.92, 203346.00, NULL, NULL, NULL, 36680.00, NULL, NULL, NULL, NULL, 0.00, 16680.08, 8.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:29:41', '2025-08-21 02:29:41'),
(37, 2, 27, 'TP', NULL, 85, 9, 19, 29, NULL, 'Bagyalakshmi Jayapal', 'Tech Lead- RPA Developer', NULL, 2, 'Approved', 2, 57, 'Chanya Thakur', NULL, 'Q32025', '2025-08-13', NULL, '2025-08-19', '2025-08-22', '2025-10', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 133333.00, NULL, 20000.00, 153333.33, 185962.50, NULL, NULL, NULL, 52629.17, NULL, NULL, NULL, NULL, 0.00, 32629.17, 18.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:29:41', '2025-08-21 02:29:41'),
(38, 2, 22, 'TP', 5, 82, 8, NULL, 23, NULL, 'Devender verma', 'Fresher', NULL, 7, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-29', NULL, '2025-08-11', '2025-08-18', '2025-10', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:08', '2025-08-21 02:42:08'),
(39, 2, 22, 'TP', NULL, 82, 9, 34, 40, NULL, 'Praneet Tripathi', 'Fresher', NULL, 8, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-29', NULL, NULL, '2025-08-04', '2025-09', 2025, NULL, '2025-08-18', NULL, 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Candidate BO', NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:08', '2025-08-21 02:42:08'),
(40, 2, 22, 'TP', NULL, 82, 9, 34, 40, NULL, 'Rudra Pratap Singh', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-29', NULL, NULL, '2025-08-04', '2025-09', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:08', '2025-08-21 02:42:08'),
(41, 2, 22, 'TP', NULL, 82, 9, 34, 43, NULL, 'Saurabh Yadav', 'Fresher', NULL, 7, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-29', NULL, '2025-08-11', '2025-08-19', '2025-10', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:08', '2025-08-21 02:42:08'),
(42, 2, 22, 'TP', NULL, 82, 9, 34, 37, NULL, 'Shubham Sharma', 'Fresher', NULL, 7, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-29', NULL, '2025-08-11', '2025-08-18', '2025-10', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:09', '2025-08-21 02:42:09'),
(43, 2, 22, 'TP', NULL, 85, 9, 19, 29, NULL, 'Neelesh Chaturvedi', 'Fresher', NULL, 8, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-29', NULL, NULL, '2025-08-04', '2025-09', 2025, NULL, '2025-08-04', NULL, 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Candidate BO', NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:09', '2025-08-21 02:42:09'),
(44, 2, 22, 'TP', NULL, 85, 10, 18, 26, NULL, 'Dhruv Jha', 'Fresher', NULL, 7, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-29', NULL, '2025-08-11', '2025-08-19', '2025-10', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:09', '2025-08-21 02:42:09'),
(45, 2, 22, 'TP', NULL, 85, NULL, NULL, NULL, NULL, 'Aakanksha Saini', 'Fresher', NULL, 7, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-29', NULL, '2025-08-11', '2025-08-18', '2025-10', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:09', '2025-08-21 02:42:09'),
(46, 2, 22, 'TP', NULL, 85, NULL, NULL, NULL, NULL, 'Aayushh Yadav', 'Fresher', NULL, 7, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-29', NULL, '2025-08-11', '2025-08-18', '2025-10', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:09', '2025-08-21 02:42:09'),
(47, 2, 22, 'TP', NULL, 85, NULL, NULL, NULL, NULL, 'Sourabh Sharma', 'Fresher', NULL, 7, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-29', NULL, '2025-08-11', '2025-08-19', '2025-10', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:09', '2025-08-21 02:42:09'),
(48, 2, 22, 'TP', NULL, 85, 8, NULL, 36, NULL, 'Girisha Jain', 'Fresher', NULL, 8, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-29', NULL, NULL, '2025-08-04', '2025-09', 2025, NULL, '2025-08-04', NULL, 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Candidate BO', NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:09', '2025-08-21 02:42:09'),
(49, 2, 24, 'TP', NULL, 85, 8, NULL, 23, NULL, 'Yogesh D', 'Interview Coordinator', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-31', NULL, NULL, '2025-08-06', '2025-09', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25000.00, NULL, 3000.00, 28000.00, 29998.98, NULL, NULL, NULL, 4998.98, NULL, NULL, NULL, NULL, 0.00, 1998.98, 7.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:09', '2025-08-21 02:42:09'),
(50, 2, 24, 'TP', NULL, 84, 9, 19, 20, NULL, 'Nabanita Paul', 'Interview Coordinator', NULL, 7, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-31', NULL, '2025-08-05', '2025-08-08', '2025-09', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25000.00, NULL, 3000.00, 28000.00, 29998.98, NULL, NULL, NULL, 4998.98, NULL, NULL, NULL, NULL, 0.00, 1998.98, 7.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:09', '2025-08-21 02:42:09'),
(51, 2, 39, 'TP', NULL, 84, 9, 34, NULL, NULL, 'Srishti Gupta', 'Java Developer (Apache Flink)', NULL, 1, 'Approved', 2, 57, 'Stephen Anthony', NULL, 'Q32025', '2025-07-30', NULL, NULL, '2025-08-06', '2025-09', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 183333.00, NULL, 27500.00, 210833.33, 254475.00, NULL, NULL, NULL, 71141.67, NULL, NULL, NULL, NULL, 0.00, 43641.67, 17.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:09', '2025-08-21 02:42:09'),
(52, 2, 27, 'TP', NULL, 84, 9, 19, 28, NULL, 'Sameer Shaikh', 'Senior Functional Consultant - CRM', NULL, 7, 'Approved', 2, 57, 'Suresh S', NULL, 'Q32025', '2025-08-03', NULL, '2025-08-05', '2025-08-13', '2025-09', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 166666.00, NULL, 24999.90, 191665.90, 214346.25, NULL, NULL, NULL, 47680.25, NULL, NULL, NULL, NULL, 0.00, 22680.35, 11.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:09', '2025-08-21 02:42:09'),
(53, 2, 31, 'TP', NULL, 84, 8, NULL, NULL, NULL, 'Karishma Rai', 'PMO', NULL, 7, 'Approved', 2, 53, 'Dhamodharam', NULL, 'Q32025', '2025-08-01', NULL, '2025-08-04', '2025-08-07', '2025-09', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 125000.00, NULL, 15000.00, 140000.00, 152499.60, NULL, NULL, NULL, 27499.60, NULL, NULL, NULL, NULL, 0.00, 12499.60, 8.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:09', '2025-08-21 02:42:09'),
(54, 2, 39, 'TP', NULL, 84, 9, 34, 27, NULL, 'Sahil Lamba', 'Java Developer (Apache Flink)', NULL, 7, 'Approved', 2, 57, 'Stephen Anthony', NULL, 'Q32025', '2025-07-30', NULL, '2025-08-04', '2025-08-06', '2025-09', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 183333.00, NULL, 27500.00, 210833.33, 254475.00, NULL, NULL, NULL, 71141.67, NULL, NULL, NULL, NULL, 0.00, 43641.67, 17.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:09', '2025-08-21 02:42:09'),
(55, 2, 39, 'TP', NULL, 84, 9, 19, 28, NULL, 'Shivansh Rawat', 'Implementation Specialist', NULL, 7, 'Approved', 2, 57, 'Pooja Prabhuraj', NULL, 'Q32025', '2025-08-06', NULL, '2025-08-07', '2025-08-13', '2025-09', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 134167.00, NULL, 20125.00, 154291.66, 187920.00, NULL, NULL, NULL, 53753.34, NULL, NULL, NULL, NULL, 0.00, 33628.34, 18.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:09', '2025-08-21 02:42:09'),
(56, 2, 22, 'TP', NULL, 86, 8, NULL, 15, NULL, 'Luv Dixit', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-07', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:09', '2025-08-21 02:42:09'),
(57, 2, 22, 'TP', NULL, 86, 8, NULL, 15, NULL, 'Jitendra Kumar', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-07', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:09', '2025-08-21 02:42:09'),
(58, 2, 22, 'TP', NULL, 86, 8, NULL, 36, NULL, 'Tanu Bhaskar', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-07', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:09', '2025-08-21 02:42:09'),
(59, 2, 22, 'TP', NULL, 86, 8, NULL, 23, NULL, 'Shailja Choudhary', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-07', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:09', '2025-08-21 02:42:09'),
(60, 2, 22, 'TP', NULL, 86, 8, NULL, 15, NULL, 'Tushar Chauhan', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-07', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:09', '2025-08-21 02:42:09'),
(61, 2, 48, 'TP', NULL, 86, 8, NULL, 23, NULL, 'A Faseela', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-11', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:09', '2025-08-21 02:42:09'),
(62, 2, 48, 'TP', NULL, 86, 8, NULL, 23, NULL, 'Heraa Joy', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-11', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:09', '2025-08-21 02:42:09'),
(63, 2, 48, 'TP', NULL, 82, 8, NULL, 36, NULL, 'Inisha K', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-11', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:09', '2025-08-21 02:42:09'),
(64, 2, 48, 'TP', NULL, 82, 8, NULL, 36, NULL, 'Susmitha', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-11', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:09', '2025-08-21 02:42:09'),
(65, 2, 48, 'TP', NULL, 82, 8, NULL, 36, NULL, 'Yuvanika S', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-11', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:09', '2025-08-21 02:42:09'),
(66, 2, 48, 'TP', NULL, 82, 10, 22, 39, NULL, 'Bhuvana A', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-11', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:09', '2025-08-21 02:42:09'),
(67, 2, 48, 'TP', NULL, 82, 10, 22, 42, NULL, 'Deepika B', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-11', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:09', '2025-08-21 02:42:09'),
(68, 2, 48, 'TP', NULL, 82, 10, 18, 26, NULL, 'Saravana Moorthy', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-11', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:09', '2025-08-21 02:42:09'),
(69, 2, 48, 'TP', NULL, 82, 9, 34, 35, NULL, 'Charudesna K', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-11', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:09', '2025-08-21 02:42:09'),
(70, 2, 48, 'TP', NULL, 82, 9, 34, 40, NULL, 'Vahithulla A', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-11', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:09', '2025-08-21 02:42:09'),
(71, 2, 48, 'TP', NULL, 85, 10, 18, NULL, NULL, 'Swetha G', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-11', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:09', '2025-08-21 02:42:09'),
(72, 2, 48, 'TP', NULL, 85, 8, NULL, 36, NULL, 'Yosuva inbakumar', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-11', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:09', '2025-08-21 02:42:09'),
(73, 2, 24, 'TP', NULL, 85, 8, NULL, NULL, NULL, 'Sayapaneni Manoj', 'TIBCO Developer', NULL, 2, 'Approved', 2, 53, 'Gokul P', NULL, 'Q32025', '2025-08-13', NULL, '2025-08-19', '2025-08-20', '2025-10', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 166666.00, NULL, 19999.92, 186665.92, 203346.00, NULL, NULL, NULL, 36680.00, NULL, NULL, NULL, NULL, 0.00, 16680.08, 8.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:09', '2025-08-21 02:42:09'),
(74, 2, 27, 'TP', NULL, 85, 9, 19, 29, NULL, 'Bagyalakshmi Jayapal', 'Tech Lead- RPA Developer', NULL, 2, 'Approved', 2, 57, 'Chanya Thakur', NULL, 'Q32025', '2025-08-13', NULL, '2025-08-19', '2025-08-22', '2025-10', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 133333.00, NULL, 20000.00, 153333.33, 185962.50, NULL, NULL, NULL, 52629.17, NULL, NULL, NULL, NULL, 0.00, 32629.17, 18.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:09', '2025-08-21 02:42:09'),
(75, 2, 22, 'TP', 5, 82, 8, NULL, 23, NULL, 'Devender verma', 'Fresher', NULL, 7, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-29', NULL, '2025-08-11', '2025-08-18', '2025-10', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:22', '2025-08-21 02:42:22'),
(76, 2, 22, 'TP', NULL, 82, 9, 34, 40, NULL, 'Praneet Tripathi', 'Fresher', NULL, 8, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-29', NULL, NULL, '2025-08-04', '2025-09', 2025, NULL, '2025-08-18', NULL, 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Candidate BO', NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:22', '2025-08-21 02:42:22'),
(77, 2, 22, 'TP', NULL, 82, 9, 34, 40, NULL, 'Rudra Pratap Singh', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-29', NULL, NULL, '2025-08-04', '2025-09', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:22', '2025-08-21 02:42:22'),
(78, 2, 22, 'TP', NULL, 82, 9, 34, 43, NULL, 'Saurabh Yadav', 'Fresher', NULL, 7, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-29', NULL, '2025-08-11', '2025-08-19', '2025-10', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:22', '2025-08-21 02:42:22'),
(79, 2, 22, 'TP', NULL, 82, 9, 34, 37, NULL, 'Shubham Sharma', 'Fresher', NULL, 7, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-29', NULL, '2025-08-11', '2025-08-18', '2025-10', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:22', '2025-08-21 02:42:22'),
(80, 2, 22, 'TP', NULL, 85, 9, 19, 29, NULL, 'Neelesh Chaturvedi', 'Fresher', NULL, 8, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-29', NULL, NULL, '2025-08-04', '2025-09', 2025, NULL, '2025-08-04', NULL, 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Candidate BO', NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:22', '2025-08-21 02:42:22'),
(81, 2, 22, 'TP', NULL, 85, 10, 18, 26, NULL, 'Dhruv Jha', 'Fresher', NULL, 7, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-29', NULL, '2025-08-11', '2025-08-19', '2025-10', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:22', '2025-08-21 02:42:22'),
(82, 2, 22, 'TP', NULL, 85, NULL, NULL, NULL, NULL, 'Aakanksha Saini', 'Fresher', NULL, 7, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-29', NULL, '2025-08-11', '2025-08-18', '2025-10', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:22', '2025-08-21 02:42:22');
INSERT INTO `job_seekers` (`id`, `company_id`, `location_id`, `hire_type`, `business_unit_id`, `am_id`, `dm_id`, `tl_id`, `recruiter_id`, `consultant_code`, `consultant_name`, `skill`, `sap_id`, `status_id`, `form_status`, `process_status`, `client_id`, `poc`, `client_reporting_manager`, `quarter`, `selection_date`, `select_month`, `offer_date`, `join_date`, `join_month`, `join_year`, `qly_date`, `backout_term_date`, `backout_term_month`, `backout_term_year`, `type_of_attrition`, `reason_of_rejection`, `bo_type`, `bd_absconding_term`, `reason_of_attrition`, `currency`, `po_end_date`, `po_end_month`, `po_end_year`, `hire_status`, `pay_rate`, `pay_rate_usd`, `loaded_cost`, `pay_rate_1`, `bill_rate`, `bill_rate_usd`, `basic_pay_rate`, `benefits`, `gp_month`, `gp_hour`, `gp_hour_usd`, `otc`, `otc_split`, `msp_fees`, `final_gp`, `percentage_gp`, `ctc_offered`, `billing_value`, `loaded_gp`, `final_billing_value`, `actual_billing_value`, `invoice_no`, `end_client`, `lob`, `remark1`, `remark2`, `sources`, `source`, `domain`, `checker_id`, `maker_id`, `po_maker_id`, `po_checker_id`, `finance_maker_id`, `finance_checker_id`, `backout_maker_id`, `backout_checker_id`, `job_seeker_type`, `created_at`, `updated_at`) VALUES
(83, 2, 22, 'TP', NULL, 85, NULL, NULL, NULL, NULL, 'Aayushh Yadav', 'Fresher', NULL, 7, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-29', NULL, '2025-08-11', '2025-08-18', '2025-10', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:22', '2025-08-21 02:42:22'),
(84, 2, 22, 'TP', NULL, 85, NULL, NULL, NULL, NULL, 'Sourabh Sharma', 'Fresher', NULL, 7, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-29', NULL, '2025-08-11', '2025-08-19', '2025-10', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:22', '2025-08-21 02:42:22'),
(85, 2, 22, 'TP', NULL, 85, 8, NULL, 36, NULL, 'Girisha Jain', 'Fresher', NULL, 8, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-29', NULL, NULL, '2025-08-04', '2025-09', 2025, NULL, '2025-08-04', NULL, 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Candidate BO', NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:22', '2025-08-21 02:42:22'),
(86, 2, 24, 'TP', NULL, 85, 8, NULL, 23, NULL, 'Yogesh D', 'Interview Coordinator', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-31', NULL, NULL, '2025-08-06', '2025-09', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25000.00, NULL, 3000.00, 28000.00, 29998.98, NULL, NULL, NULL, 4998.98, NULL, NULL, NULL, NULL, 0.00, 1998.98, 7.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:22', '2025-08-21 02:42:22'),
(87, 2, 24, 'TP', NULL, 84, 9, 19, 20, NULL, 'Nabanita Paul', 'Interview Coordinator', NULL, 7, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-07-31', NULL, '2025-08-05', '2025-08-08', '2025-09', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25000.00, NULL, 3000.00, 28000.00, 29998.98, NULL, NULL, NULL, 4998.98, NULL, NULL, NULL, NULL, 0.00, 1998.98, 7.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:22', '2025-08-21 02:42:22'),
(88, 2, 39, 'TP', NULL, 84, 9, 34, NULL, NULL, 'Srishti Gupta', 'Java Developer (Apache Flink)', NULL, 1, 'Approved', 2, 57, 'Stephen Anthony', NULL, 'Q32025', '2025-07-30', NULL, NULL, '2025-08-06', '2025-09', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 183333.00, NULL, 27500.00, 210833.33, 254475.00, NULL, NULL, NULL, 71141.67, NULL, NULL, NULL, NULL, 0.00, 43641.67, 17.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:22', '2025-08-21 02:42:22'),
(89, 2, 27, 'TP', NULL, 84, 9, 19, 28, NULL, 'Sameer Shaikh', 'Senior Functional Consultant - CRM', NULL, 7, 'Approved', 2, 57, 'Suresh S', NULL, 'Q32025', '2025-08-03', NULL, '2025-08-05', '2025-08-13', '2025-09', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 166666.00, NULL, 24999.90, 191665.90, 214346.25, NULL, NULL, NULL, 47680.25, NULL, NULL, NULL, NULL, 0.00, 22680.35, 11.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:23', '2025-08-21 02:42:23'),
(90, 2, 31, 'TP', NULL, 84, 8, NULL, NULL, NULL, 'Karishma Rai', 'PMO', NULL, 7, 'Approved', 2, 53, 'Dhamodharam', NULL, 'Q32025', '2025-08-01', NULL, '2025-08-04', '2025-08-07', '2025-09', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 125000.00, NULL, 15000.00, 140000.00, 152499.60, NULL, NULL, NULL, 27499.60, NULL, NULL, NULL, NULL, 0.00, 12499.60, 8.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:23', '2025-08-21 02:42:23'),
(91, 2, 39, 'TP', NULL, 84, 9, 34, 27, NULL, 'Sahil Lamba', 'Java Developer (Apache Flink)', NULL, 7, 'Approved', 2, 57, 'Stephen Anthony', NULL, 'Q32025', '2025-07-30', NULL, '2025-08-04', '2025-08-06', '2025-09', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 183333.00, NULL, 27500.00, 210833.33, 254475.00, NULL, NULL, NULL, 71141.67, NULL, NULL, NULL, NULL, 0.00, 43641.67, 17.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:23', '2025-08-21 02:42:23'),
(92, 2, 39, 'TP', NULL, 84, 9, 19, 28, NULL, 'Shivansh Rawat', 'Implementation Specialist', NULL, 7, 'Approved', 2, 57, 'Pooja Prabhuraj', NULL, 'Q32025', '2025-08-06', NULL, '2025-08-07', '2025-08-13', '2025-09', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 134167.00, NULL, 20125.00, 154291.66, 187920.00, NULL, NULL, NULL, 53753.34, NULL, NULL, NULL, NULL, 0.00, 33628.34, 18.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:23', '2025-08-21 02:42:23'),
(93, 2, 22, 'TP', NULL, 86, 8, NULL, 15, NULL, 'Luv Dixit', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-07', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:23', '2025-08-21 02:42:23'),
(94, 2, 22, 'TP', NULL, 86, 8, NULL, 15, NULL, 'Jitendra Kumar', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-07', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:23', '2025-08-21 02:42:23'),
(95, 2, 22, 'TP', NULL, 86, 8, NULL, 36, NULL, 'Tanu Bhaskar', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-07', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:23', '2025-08-21 02:42:23'),
(96, 2, 22, 'TP', NULL, 86, 8, NULL, 23, NULL, 'Shailja Choudhary', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-07', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:23', '2025-08-21 02:42:23'),
(97, 2, 22, 'TP', NULL, 86, 8, NULL, 15, NULL, 'Tushar Chauhan', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-07', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:23', '2025-08-21 02:42:23'),
(98, 2, 48, 'TP', NULL, 86, 8, NULL, 23, NULL, 'A Faseela', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-11', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:23', '2025-08-21 02:42:23'),
(99, 2, 48, 'TP', NULL, 86, 8, NULL, 23, NULL, 'Heraa Joy', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-11', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:23', '2025-08-21 02:42:23'),
(100, 2, 48, 'TP', NULL, 82, 8, NULL, 36, NULL, 'Inisha K', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-11', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:23', '2025-08-21 02:42:23'),
(101, 2, 48, 'TP', NULL, 82, 8, NULL, 36, NULL, 'Susmitha', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-11', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:23', '2025-08-21 02:42:23'),
(102, 2, 48, 'TP', NULL, 82, 8, NULL, 36, NULL, 'Yuvanika S', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-11', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:23', '2025-08-21 02:42:23'),
(103, 2, 48, 'TP', NULL, 82, 10, 22, 39, NULL, 'Bhuvana A', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-11', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:23', '2025-08-21 02:42:23'),
(104, 2, 48, 'TP', NULL, 82, 10, 22, 42, NULL, 'Deepika B', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-11', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:23', '2025-08-21 02:42:23'),
(105, 2, 48, 'TP', NULL, 82, 10, 18, 26, NULL, 'Saravana Moorthy', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-11', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:23', '2025-08-21 02:42:23'),
(106, 2, 48, 'TP', NULL, 82, 9, 34, 35, NULL, 'Charudesna K', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-11', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:23', '2025-08-21 02:42:23'),
(107, 2, 48, 'TP', NULL, 82, 9, 34, 40, NULL, 'Vahithulla A', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-11', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:23', '2025-08-21 02:42:23'),
(108, 2, 48, 'TP', NULL, 85, 10, 18, NULL, NULL, 'Swetha G', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-11', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:23', '2025-08-21 02:42:23'),
(109, 2, 48, 'TP', NULL, 85, 8, NULL, 36, NULL, 'Yosuva inbakumar', 'Fresher', NULL, 1, 'Approved', 2, 53, 'Rakesh Kumar Chaubey', NULL, 'Q32025', '2025-08-11', NULL, NULL, NULL, '1900-02', 1900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20000.00, NULL, 2400.00, 22400.00, 23599.62, NULL, NULL, NULL, 3599.62, NULL, NULL, NULL, NULL, 0.00, 1199.62, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:23', '2025-08-21 02:42:23'),
(110, 2, 24, 'TP', NULL, 85, 8, NULL, NULL, NULL, 'Sayapaneni Manoj', 'TIBCO Developer', NULL, 2, 'Approved', 2, 53, 'Gokul P', NULL, 'Q32025', '2025-08-13', NULL, '2025-08-19', '2025-08-20', '2025-10', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 166666.00, NULL, 19999.92, 186665.92, 203346.00, NULL, NULL, NULL, 36680.00, NULL, NULL, NULL, NULL, 0.00, 16680.08, 8.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:23', '2025-08-21 02:42:23'),
(111, 2, 27, 'TP', NULL, 85, 9, 19, 29, NULL, 'Bagyalakshmi Jayapal', 'Tech Lead- RPA Developer', NULL, 2, 'Approved', 2, 57, 'Chanya Thakur', NULL, 'Q32025', '2025-08-13', NULL, '2025-08-19', '2025-08-22', '2025-10', 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 133333.00, NULL, 20000.00, 153333.33, 185962.50, NULL, NULL, NULL, 52629.17, NULL, NULL, NULL, NULL, 0.00, 32629.17, 18.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naukri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Temporary', '2025-08-21 02:42:23', '2025-08-21 02:42:23');

-- --------------------------------------------------------

--
-- Table structure for table `main_emails`
--

CREATE TABLE `main_emails` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `main_emails`
--

INSERT INTO `main_emails` (`id`, `email`, `name`, `password`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'karankalal20@gmail.com', 'KaranKalal', '$2y$12$/uw/oX4HYXGBNaRH.HxiKelSpNoMN0Wdp8V8zRP4.Mu5Q7l.zNs5y', 1, '2025-08-01 06:06:40', '2025-08-04 05:19:26');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_06_03_102036_create_company_masters_table', 1),
(5, '2025_06_05_104454_create_status_masters_table', 1),
(6, '2025_06_05_104606_create_business_unit_masters_table', 1),
(7, '2025_06_05_195349_create_branch_masters_table', 1),
(8, '2025_06_13_103918_create_consultants_table', 1),
(9, '2025_06_13_104118_create_clients_table', 1),
(10, '2025_06_13_104155_create_employees_table', 1),
(11, '2025_06_14_112457_create_job_seekers_table', 1),
(12, '2025_06_27_194021_update_branch_masters_table', 2),
(13, '2025_06_27_194137_update_business_unit_masters_table', 2),
(14, '2025_06_28_113002_update_clients_table', 3),
(15, '2025_06_28_132307_update_employees_table_add_company_id_and_update_roles', 4),
(16, '2025_06_28_135531_make_role_nullable_and_add_checker_emp_id_to_employees_table', 5),
(17, '2025_06_30_104655_update_job_seekers_table', 6),
(18, '2025_07_02_073025_update_employee_table_checker', 7),
(19, '2025_07_02_073603_update_jobseeker_table_roles', 7),
(20, '2025_07_02_082020_update_employee_table_self_cheker', 8),
(21, '2025_07_06_131100_update_job_seekers_table_for_finance', 9),
(22, '2025_07_06_131333_update_employees_table_for_finance', 9),
(23, '2025_07_08_110625_update_job_seekers_table_for_formfield', 10),
(24, '2025_07_17_201514_add_rejection_to_jobseekers_table', 11),
(25, '2025_07_29_095520_add_bo_type_to_job_seekers_table', 12),
(26, '2025_08_01_103720_add_emails_to_company_masters_table', 13),
(27, '2025_08_01_103725_create_main_emails_table', 13),
(28, '2025_08_02_095852_add_region_to_company_masters_table', 14),
(29, '2025_08_02_103336_add_password_to_main_emails_table', 15);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('6Y8exfuWnnPSxWdHNZioPfp7OOqR33kduBMamjiC', NULL, '54.197.177.182', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTmJhS3ZrblRaRWNBZ3dva3pVd2M3eGl3UmRMTjVIOVJRa2tBSXdZcSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vYWxvaXMucXVpY2thZC5iaXoiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1756039280),
('8nD8hXGUFdhh3bfBfs0WCaJIZOKk2WaKlwqB3hCv', 1, '103.249.234.51', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTnR6MGRGeDI2bW14TDNOV2MyZkZtdGcxdmk0Mlc2YVlZVVdmajBMZiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vYWxvaXMucXVpY2thZC5iaXoiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1755849769),
('JtVGjD53lfIwkAQI03drzzttv3OVVCXYBgQNu2AB', 1, '103.249.234.51', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiblpGT1B0OEtyZHVadGRFYndzaks4VlUxbzNQV0VwN1c3b3RxWjg5eiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6OTQ6Imh0dHBzOi8vYWxvaXMucXVpY2thZC5iaXovam9iLXNlZWtlcnMvcGVybWFuZW50P2NvbXBhbnlfaWQ9JnBlcl9wYWdlPTEwJnN0YXR1c19maWx0ZXI9QXBwcm92ZWQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1755845767),
('LZZ2pNrGgfwEP1cY7PquqwbTn7PVE84yogXlsIP5', NULL, '20.171.207.231', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; GPTBot/1.2; +https://openai.com/gptbot)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidTdjQTJqVVZrMU80Y0lqdklFc1h2TkhYUzZIMUxrVFd6ZDNwdTFXNyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHBzOi8vd3d3LmFsb2lzLnF1aWNrYWQuYml6Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1755964667),
('ooMOyuYETMugSXFGLWCWNJafI0Z7qaQ3IxMQIEBl', NULL, '20.171.207.235', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; GPTBot/1.2; +https://openai.com/gptbot)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOVVpUVBJQWJaOWxyZ3F0YlhQZ0U1VGRETkFFMmVxeVQ2TjJLbThzSiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vYWxvaXMucXVpY2thZC5iaXoiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1756018387),
('Q0hxsOeTqXyD00iqjTAPJbYxkAMLh9ezBk66jMtF', NULL, '198.235.24.130', '', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZXlBcVJKRTZuaVh2OVZwUkJ4MGVZTElIbnVqVU1qOHZBd0xRUUx5RyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vYWxvaXMucXVpY2thZC5iaXoiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1756022830),
('TgI9jIbd3WKC9kYfyOYJYK70s9OIbxtKyZlWhwbM', NULL, '143.198.82.202', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRDFrc1B0WUNJRFo5bXk1R1V1dE9tRnlHbGtpakNjYzY3S0hZekFEQiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vYWxvaXMucXVpY2thZC5iaXoiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1756123649),
('VpkDDpZ6SOqnsmASlsk42T65tlPQhhXGCgJV6ZBg', 1, '103.249.234.92', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQkJZU0ZPMGJ0TzJxdGc1MmZjNEppWXZlOFYyVHVpMzRtcnBpN3Q5NiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1755954911),
('W5XADMV6TC9vbhLFGAjQodvHboxVupZTBK6o8gO6', NULL, '44.204.28.70', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidXZrQ2RyeXlweGl2Qk5oZGM5S25Sd0pVcDlzR3JENURIYlhsdk9XbyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vYWxvaXMucXVpY2thZC5iaXoiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1756042231);

-- --------------------------------------------------------

--
-- Table structure for table `status_masters`
--

CREATE TABLE `status_masters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `status_masters`
--

INSERT INTO `status_masters` (`id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Selected', '2025-06-20 01:26:55', '2025-06-28 07:03:13'),
(2, 'Offered', '2025-06-20 01:27:03', '2025-06-28 07:03:42'),
(7, 'Joined', '2025-06-28 07:03:49', '2025-06-28 07:03:49'),
(8, 'Backout', '2025-06-28 07:03:56', '2025-06-28 07:03:56'),
(9, 'Termination', '2025-06-28 07:04:02', '2025-07-29 14:58:35'),
(10, 'FTE Conversion Fee', '2025-07-29 03:40:39', '2025-07-29 14:58:51');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'employee',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin User', 'admin@gmail.com', NULL, '$2y$12$4hziVzMauDOMSVK312gcC.i1kNErxGXAfKUJs8R5B95cKYmtMpeDy', 'admin', 'FEbP8m2XMxvpfD3u8WlexhvLmMcEzEd60CZF2GOCxc1LovvcGVk1imht4L2T', '2025-06-20 01:00:58', '2025-06-20 01:00:58'),
(2, 'Employee User', 'employee@gmail.com', NULL, '$2y$12$vEABKP56E7Rv7iANmDX4a.eJR6cJ9OtFaxPE3qKboU0KqoH45y5te', 'employee', NULL, '2025-06-20 01:00:58', '2025-06-20 01:00:58'),
(6, 'Twinkle Chauhan', 'twinkle.chauhan@aloissolutions.com', NULL, '$2y$12$QlvzSOldoKs6KL2Mg/y4dO71BE6NsvKnKMx3t1Icj0NRqFFwQKYtK', 'employee', NULL, '2025-08-18 04:42:46', '2025-08-18 04:42:46'),
(7, 'Chaitanya Nandedkar', 'chaitanya.nandedkar@aloissolutions.com', NULL, '$2y$12$29c26QP7Y1JOBr9FZ.cyye7HjlmAR6eDv/DMCqtbztMVKZ92watNW', 'employee', NULL, '2025-08-18 04:42:46', '2025-08-18 04:42:46'),
(8, 'Tashmit Sawhney', 'tashmit.sawhney@aloissolutions.com', NULL, '$2y$12$IGxGQtLx3obZiwG21ROHJemtgxKrQf3WNxEpUv6267Ssqu3fMOM9a', 'employee', NULL, '2025-08-18 04:42:47', '2025-08-18 04:42:47'),
(9, 'Khushbu Porwal', 'khushbu.porwal@aloissolutions.com', NULL, '$2y$12$h89/maFd/Id7VbkxFJOk3u2Ns72W3eknw6J8fAza/VrbrsZPIpW5O', 'employee', NULL, '2025-08-18 04:42:47', '2025-08-18 04:42:47'),
(10, 'Japan Bhatt', 'japan.bhatt@aloissolutions.com', NULL, '$2y$12$a7csXXUmm8f4laiMw3dTEu5CibQAPOqXbSCMhVfkpmv5qoO20mCse', 'employee', NULL, '2025-08-18 04:42:47', '2025-08-18 04:42:47'),
(11, 'Ruchir Shah', 'ruchir.shah@aloissolutions.com', NULL, '$2y$12$odhTwSVJMXtv1veGUxeb8OSlGi/fG/g.Kq.QpoOu8ie/.n/C/6OaW', 'employee', NULL, '2025-08-18 04:42:47', '2025-08-18 04:42:47'),
(12, 'Dipak Parmar', 'dipak.parmar@aloissolutions.com', NULL, '$2y$12$GHASe812o5IrNKp6w6YGr.TqxcJsRi32tKNH90Fddql3aIYgbZU1W', 'employee', NULL, '2025-08-18 04:42:48', '2025-08-18 04:42:48'),
(13, 'Simit Mehta', 'simit.mehta@aloissolutions.com', NULL, '$2y$12$6KWt9KvjHuAwLffbdP/p/O5yY.mz6lDyG4noKZVQwJaBygNwPiToS', 'employee', NULL, '2025-08-18 04:42:48', '2025-08-18 04:42:48'),
(14, 'Bansal Patel', 'bansal.patel@aloissolutions.com', NULL, '$2y$12$IfBvc6YpRzJQDTHhsguYRu2NQtWVSlb2ah0ZAJUH6FJx6DBEoGD06', 'employee', NULL, '2025-08-18 04:42:48', '2025-08-18 04:42:48'),
(15, 'Divyesh Hargun', 'divyesh.hargun@aloissolutions.com', NULL, '$2y$12$KsrF8kb7/GabXymZ2r8JrOe/JfyoMczgNvFxAgDkwpaq7Q8byOcnG', 'employee', NULL, '2025-08-18 04:42:49', '2025-08-18 04:42:49'),
(16, 'Uzeir Mal', 'uzeir.mal@aloissolutions.com', NULL, '$2y$12$t2zgR7kr5ES9uXk3uul6Be2LJ2dR2d8WR.bwVyACTuyoFt1iyj1he', 'employee', NULL, '2025-08-18 04:42:49', '2025-08-18 04:42:49'),
(17, 'Rachana Sane', 'rachana.sane@aloissolutions.com', NULL, '$2y$12$zFovpx.gOJByvqGL./lCv.vjXUPH/3VZKyXLsgzd33aEM1ZHWAOye', 'employee', NULL, '2025-08-18 04:42:49', '2025-08-18 04:42:49'),
(18, 'Peri Vegada', 'peri.vegada@aloissolutions.com', NULL, '$2y$12$axpyw9vSzY25nVxCqxSzWe/EfnVOawwSEBm6Aiv0bACmdzN0g5opq', 'employee', NULL, '2025-08-18 04:42:50', '2025-08-18 04:42:50'),
(19, 'Robert Anthony', 'robert.anthony@aloissolutions.com', NULL, '$2y$12$MgVKQT0ubMgDdlFvm6QNpOhgTAzdzF4MZunraTvYzQ9R93xOcZNPm', 'employee', NULL, '2025-08-18 04:42:50', '2025-08-18 04:42:50'),
(20, 'Khushboo Vanjani', 'khushi.vanjani@aloissolutions.com', NULL, '$2y$12$CIyBytBV2PKF.WpQ2FfJ/eCSSJpSCeAA3PNo7pVRve9EApbrGJQtq', 'employee', NULL, '2025-08-18 04:42:50', '2025-08-18 04:42:50'),
(21, 'Rahul Patel', 'rahul.patel@aloissolutions.com', NULL, '$2y$12$Mt3Pz0ufbFSUoFaGNuSMv.OjN95GTOQDQ5i7XduCmk555Wv4/EMVS', 'employee', NULL, '2025-08-18 04:42:50', '2025-08-18 04:42:50'),
(22, 'Ullas Mathur', 'ullas.mathur@aloissolutions.com', NULL, '$2y$12$KlgqUyiM89dmDNr8LfuH7uKwyB02SItaAymw4e2IKtwl/btX/0nEe', 'employee', NULL, '2025-08-18 04:42:51', '2025-08-18 04:42:51'),
(23, 'Akhilesh Nair', 'akhilesh.nair@aloissolutions.com', NULL, '$2y$12$3c5PZAEJYwjFUKL0n65ffOARMqlxiNMGClMKS61rZnRf9HatyIYjO', 'employee', NULL, '2025-08-18 04:42:51', '2025-08-18 04:42:51'),
(24, 'Lopa Pancholi', 'lopa.pancholi@aloissolutions.com', NULL, '$2y$12$eOwCtN6U/thyX.3.ZG64tO4Wowp42kp80g5nmSCbG0FW21q3KI2FO', 'employee', NULL, '2025-08-18 04:42:51', '2025-08-18 04:42:51'),
(25, 'Megha Vaghasia', 'megha.vaghasia@aloissolutions.com', NULL, '$2y$12$h27z6Pzku2g/tGjkCJi1x.N2EROkjoezew2XCONwXsYfVL4fXwLKC', 'employee', NULL, '2025-08-18 04:42:52', '2025-08-18 04:42:52'),
(26, 'Harsh Shah', 'harsh.shah@aloissolutions.com', NULL, '$2y$12$uxO.lcvILwtJdW2nEkp9ke93mNx4ZEsSzaox3HOa5Duxtvx3U/WVW', 'employee', NULL, '2025-08-18 04:42:52', '2025-08-18 04:42:52'),
(27, 'Vahbiz Patel', 'vahbiz.patel@aloissolutions.com', NULL, '$2y$12$qP5N30tG6OFxiKXYh3rqquwGHeU76XhDYHRvsWyGlJxVoVHrI31N6', 'employee', NULL, '2025-08-18 04:42:52', '2025-08-18 04:42:52'),
(28, 'Ruknaaz Patel', 'ruknaaz.patel@aloissolutions.com', NULL, '$2y$12$prDFwwMtdVEORNZekRndB.jWhCq2lD4beuiT6fLB.kVLsh7e0RkZm', 'employee', NULL, '2025-08-18 04:42:53', '2025-08-18 04:42:53'),
(29, 'Akshay Shah', 'akshay.shah@aloissolutions.com', NULL, '$2y$12$84AObtQ/N70ogllkGJVH5O7peq/jYWAaq57e8iRMYgGvv882qZ2te', 'employee', NULL, '2025-08-18 04:42:53', '2025-08-18 04:42:53'),
(30, 'Megha Khubchandani', 'megha.khubchandani@aloissolutions.com', NULL, '$2y$12$QBs2WAhRK4hEowSmILCafeOa3OR/waMXcoHo5dJX3Wlmtuzl7gfxW', 'employee', NULL, '2025-08-18 04:42:53', '2025-08-18 04:42:53'),
(31, 'Yukta Hairav', 'yukta.hairav@aloissolutions.com', NULL, '$2y$12$YJ0NDv0bQF0rzBxMvJGTKuSfM0bST49JvXXqypf7ztKNcRaP1DAny', 'employee', NULL, '2025-08-18 04:42:53', '2025-08-18 04:42:53'),
(32, 'Pooja Sah', 'pooja.sah@aloissolutions.com', NULL, '$2y$12$WlLa4B40ZvRRmhj5lq4y1O3C/yaH9xqQvyGnACCh6vWFnLNoeIhCi', 'employee', NULL, '2025-08-18 04:42:54', '2025-08-18 04:42:54'),
(33, 'Sangeeta Wadhwani', 'sangeeta.wadhwani@aloissolutions.com', NULL, '$2y$12$34f6K/3/n.Ta8A6ZpRMX8OLjH3aGEOubFsDrZzhtsgjPk0Beq1wTq', 'employee', NULL, '2025-08-18 04:42:54', '2025-08-18 04:42:54'),
(34, 'Chiranjeev Iyer', 'chiranjeev.iyer@aloissolutions.com', NULL, '$2y$12$6izNptKkp2qHLiitKpmjr.0ht8LqwEZj4Fv/HEoM6oxNddJjPoxJC', 'employee', NULL, '2025-08-18 04:42:54', '2025-08-18 04:42:54'),
(35, 'Sarang Sangamnerkar', 'sarang.sangamnekar@aloissolutions.com', NULL, '$2y$12$xJ4TiKrWyoqLbg596giJXeh7olL3Ik0sKxf6nlLZdfEeM3R8R/dAO', 'employee', NULL, '2025-08-18 04:42:55', '2025-08-18 04:42:55'),
(36, 'Jitendra Vyas', 'jitendra.vyas@aloissolutions.com', NULL, '$2y$12$AfT119WQmHSUEQnAeuHi/.hwgqyLfEEzhMIUwLwkpt6oKYN2944E.', 'employee', NULL, '2025-08-18 04:42:55', '2025-08-18 04:42:55'),
(37, 'Nishant Nair', 'nishant.nair@aloissolutions.com', NULL, '$2y$12$VjSwHZCuXLpQoQI46raVcOk./HV8pYDRtqrxDu7lQ656mpIZ3vJMO', 'employee', NULL, '2025-08-18 04:42:55', '2025-08-18 04:42:55'),
(38, 'Shubhi Sharma', 'shubhi.sharma@aloissolutions.com', NULL, '$2y$12$3Was4cQdj1nns79rVfIJv.8W2q2EJwu0UmfMCotcdJcAzF5nHe5hu', 'employee', NULL, '2025-08-18 04:42:56', '2025-08-18 04:42:56'),
(39, 'Jairaj Makwana', 'jairaj.makwana@aloissolutions.com', NULL, '$2y$12$nOI.taNWQkbHXGX2MeCQp.AQfHi5OX1M.LxVPP9a/pyoOaVOD2Bie', 'employee', NULL, '2025-08-18 04:42:56', '2025-08-18 04:42:56'),
(40, 'Bhavika Kataria', 'bhavika.kataria@aloissolutions.com', NULL, '$2y$12$gcH7dN.tFtoynJcCUNB2NeCYgCKlHSe8wq6YqTRkfD3seCAYfo5HS', 'employee', NULL, '2025-08-18 04:42:56', '2025-08-18 04:42:56'),
(41, 'Binal Lalwani', 'binal.lalwani@aloissolutions.com', NULL, '$2y$12$tdqN0Rr9snmNF7UGdODFn.dKhRPBLwauQPMQXBIVnaOG4gS8.hBDi', 'employee', NULL, '2025-08-18 04:42:56', '2025-08-18 04:42:56'),
(42, 'Jami Patel', 'jami.patel@aloissolutions.com', NULL, '$2y$12$IHdLYYc5HRWIKCISCgKPieIekg.cUKqnkf7j209uuj0fUvyfza3YW', 'employee', NULL, '2025-08-18 04:42:57', '2025-08-18 04:42:57'),
(43, 'Archita Parmar', 'archita.parmar@aloissolutions.com', NULL, '$2y$12$B521A7VQaabYHbEN7Sz6U.05MRxfnfx7j9NTljQEcGaf5SutgW8Sy', 'employee', NULL, '2025-08-18 04:42:57', '2025-08-18 04:42:57'),
(44, 'Saniya Chavda', 'saniya.chavda@aloissolutions.com', NULL, '$2y$12$hFne5c5v/YHg/015WD5QV.xWIqlvjG09j/ZhnE00GxSvE1OiKbzXS', 'employee', NULL, '2025-08-18 04:42:57', '2025-08-18 04:42:57'),
(45, 'Arun Nishad', 'arun.nishad@aloissolutions.com', NULL, '$2y$12$3Lw7zCDhZ./eC9nMQl9OfufsNw461uAKC5TE3hTng5QIqLei9Ni8G', 'employee', NULL, '2025-08-18 04:42:58', '2025-08-18 04:42:58'),
(46, 'Ketan Parmar', 'ketan.parmar@aloissolutions.com', NULL, '$2y$12$PKXSuwZy/AxWGzJyO/oS.Ow.MZBxeow4GC7pFXyOOYg/Tx7yu7qU2', 'employee', NULL, '2025-08-18 04:42:58', '2025-08-18 04:42:58'),
(47, 'Pooja Chavan', 'pooja.chavan@aloissolutions.com', NULL, '$2y$12$fJvrY/4aFS3NuVrxyncEoegS1LyvM1TNZILiUwjJUp5Lwe70gL.TG', 'employee', NULL, '2025-08-18 04:42:58', '2025-08-18 04:42:58'),
(48, 'Salomee Dcruz', 'salomee.dcruz@aloissolutions.com', NULL, '$2y$12$DENmaOdEJPY30.c5mDo5tOBg/bYogh9iveDoxhNWm64t16CkM3dEC', 'employee', NULL, '2025-08-18 04:42:59', '2025-08-18 04:42:59'),
(49, 'Heli Gohil', 'heli.gohil@aloissolutions.com', NULL, '$2y$12$46tJpVKdfIXzTydVuPiazOpITZDt8qWPq/hDEbo1.9vYGFvD0ZmC.', 'employee', NULL, '2025-08-18 04:42:59', '2025-08-18 04:42:59'),
(50, 'Heli Kamnani', 'heli.kamnani@aloissolutions.com', NULL, '$2y$12$BYLA9gKA/AVfXTYTI6vvFueKepmSFN.n8BXbbLJ3SQPyQYj9odobG', 'employee', NULL, '2025-08-18 04:42:59', '2025-08-18 04:42:59'),
(51, 'Dev Shah', 'dev.shah@aloissolutions.com', NULL, '$2y$12$O5wJCP4sMU4BX7w/VU5ba.A3OMaHuKcqsrD9VJC.1R1xYUGnimPsW', 'employee', NULL, '2025-08-18 04:42:59', '2025-08-18 04:42:59'),
(52, 'Nikul Patel', 'nikul.p@aloissolutions.com', NULL, '$2y$12$IY9D0O1cFlU.paeG7cgleOEbXMjomagiugFSStRezu5LkgwEX/.Om', 'employee', NULL, '2025-08-18 04:43:00', '2025-08-18 04:43:00'),
(54, 'Gaurav Kulkarni', 'gaurav.kulkarni@aloissolutions.com', NULL, '$2y$12$jYbSyv7Ppoer9ydy7bohjujc9JrFDWDgbMvrio/J9FogcOUbp1252', 'employee', NULL, '2025-08-18 04:43:00', '2025-08-18 04:43:00'),
(55, 'Mujahid Patni', 'mujahid.patni@aloissolutions.com.au', NULL, '$2y$12$dI8AF9Qe.s/bvEbpkb/ITen8pDXA8ECLXXR/SYDeNk2fyOR5wfKK.', 'employee', NULL, '2025-08-18 04:43:00', '2025-08-18 04:43:00'),
(56, 'Abutalib Shaikh ', 'shaikh.abutalib@aloissolutions.com.au', NULL, '$2y$12$fsHW5F2Az4G3QQjJs56wxeVVO5wAmFe4pUWFchigC1r8yjezc65Pe', 'employee', NULL, '2025-08-18 04:43:01', '2025-08-18 04:43:01'),
(57, 'Anjali Wadhwani', 'anjali.wadhwani@aloissolutions.com.au', NULL, '$2y$12$bEWgvKMaF52op4uze/CaFO0OBBBa8nSIFJTUzEy6zS1s4UIAzEyzi', 'employee', NULL, '2025-08-18 04:43:01', '2025-08-18 04:43:01'),
(58, 'Gaurav Kataria', 'gaurav.kataria@aloissolutions.com.au', NULL, '$2y$12$kS54bnHYMwARwexd2IArFu0dWHVh2m.tTYMbW126lWpImHBXmg8rC', 'employee', NULL, '2025-08-18 04:43:01', '2025-08-18 04:43:01'),
(59, 'Vedant Mane', 'vedant.mane@aloissolutions.com.au', NULL, '$2y$12$T1I2YAts70hge2dH.Ap03OUtpGd5e15sFu1Sf7zNS1pStR6QQlpxG', 'employee', NULL, '2025-08-18 04:43:01', '2025-08-18 04:43:01'),
(60, 'Neel Patel', 'neel.patel@aloissolutions.com.au', NULL, '$2y$12$OyHzatKZ7P7uu7SqGn02i.X05rAC1GCzLldL45XrShjr4jCtNqrfe', 'employee', NULL, '2025-08-18 04:43:02', '2025-08-18 04:43:02'),
(61, 'Deeksha Shetty', 'deeksha.shetty@aloissolutions.com.au', NULL, '$2y$12$eNDTM6n0KptvUcRn5lOY8OBcTDTJlEyg5qsznsIAn1mpsiGgKCTXq', 'employee', NULL, '2025-08-18 04:43:02', '2025-08-18 04:43:02'),
(62, 'Gaurav Solanki', 'gaurav.solanki@aloissolutions.com.au', NULL, '$2y$12$D/eDApX.SlEBZfjGv.v4N.iOe6IwoUdxoBgreqmVxufejgYQM42AG', 'employee', NULL, '2025-08-18 04:43:02', '2025-08-18 04:43:02'),
(63, 'Suraj Gupta', 'suraj.gupta@aloissolutions.com.au', NULL, '$2y$12$kmUmclYjFkGG8PCCnrk56.f7kdgLHAeJLDuVRsv3k8cyKcp.pJYs6', 'employee', NULL, '2025-08-18 04:43:03', '2025-08-18 04:43:03'),
(64, 'Azraa Chaviwala', 'azraa.chaviwala@aloissolutions.com.au', NULL, '$2y$12$LTmXoCBoqKnGM8LMCMBPFeMTxUrzRnlq/oncX6aEk3/diV80Ro5ey', 'employee', NULL, '2025-08-18 04:43:03', '2025-08-18 04:43:03'),
(65, 'Mariya Dhal', 'mariya.dhal@aloissolutions.com.au', NULL, '$2y$12$OuFZnXB4X4EB5aCkMFziM.I7fduQcK9FJ5PUha7TrvMo7jD32EGVi', 'employee', NULL, '2025-08-18 04:43:03', '2025-08-18 04:43:03'),
(66, 'Kritika Gohil', 'kritika.gohil@aloissolutions.com.au', NULL, '$2y$12$zR6bpSbf..o5ScW2wtvwOuSj.EtXTJGH6fkJcMkukh6zI0rAyNzfO', 'employee', NULL, '2025-08-18 04:43:04', '2025-08-18 04:43:04'),
(67, 'Dhara Chaudhari', 'dhara.chaudhari@aloissolutions.com.au', NULL, '$2y$12$JnWsCm0UMC9B9moFaEoL7OOT7bQjb5ZU8MHQ9s5IlUTD7drETHTKK', 'employee', NULL, '2025-08-18 04:43:04', '2025-08-18 04:43:04'),
(68, 'Vikas Sharma', 'vikas.sharma@aloissolutions.com', NULL, '$2y$12$OI9vO2Cqmq4pvDu2QQU5XO7BiCnwpKLnsAr9hwZxzu64c9qW/niVO', 'employee', NULL, '2025-08-18 04:43:04', '2025-08-18 04:43:04'),
(69, 'Namrata Patel', 'namrata.patel@aloissolutions.com', NULL, '$2y$12$SlEUkgrLOZVqxgFxgiu1mezKvNLXX/3eyq5buMWuKWKvMPyoOqZSC', 'employee', NULL, '2025-08-18 04:43:04', '2025-08-18 04:43:04'),
(70, 'Meera Parmar', 'meera.parmar@aloissolutions.com', NULL, '$2y$12$b2bo8zqBpaptBb2IxhODBODtN/IOHhdDHkNN1sHahH4z5.hUF7HqS', 'employee', NULL, '2025-08-18 04:43:05', '2025-08-18 04:43:05'),
(71, 'Tanay Sudarshan', 'tanay.sudarshan@aloissolutions.com', NULL, '$2y$12$nfT/hPAk5oOnOHlPs5Qg7.7xY.egGfMIhG8SH4kLnUWAkHkIcRhHi', 'employee', NULL, '2025-08-18 04:43:05', '2025-08-18 04:43:05'),
(72, 'Himani Bhatt', 'himani.bhatt@aloissolutions.com', NULL, '$2y$12$WUEtCeMiTC8888MJGGXosuDVTnnDF4lrNsQzvU6yEukQOymrIXsGS', 'employee', NULL, '2025-08-18 04:43:05', '2025-08-18 04:43:05'),
(73, 'Ankur Patel', 'ankur.patel@aloissolutions.com', NULL, '$2y$12$9Kc2qBLZZuz5LAsgUiz8t.UPLWBhn8nFGkRIcE3/ajDfP3/6Zutuu', 'employee', NULL, '2025-08-18 04:43:06', '2025-08-18 04:43:06'),
(74, 'Dhvani Shah', 'dhvani.shah@aloissolutions.com', NULL, '$2y$12$V9MrFRfpT/a7Nn9ibon1aeMqFzGi2UvYbQdkefzkmkKS8hNKl4qNa', 'employee', NULL, '2025-08-18 04:43:06', '2025-08-18 04:43:06'),
(75, 'Vaishnavi Tripathi', 'vaishnavi.tripathi@aloissolutions.com', NULL, '$2y$12$znrKl6xc4qeS0860PttMt.kVpa.oztZVk9YynKOagPXzaEHqTqJwm', 'employee', NULL, '2025-08-18 04:43:06', '2025-08-18 04:43:06'),
(76, 'Sumit Randhawa', 'sumit.randhawa@aloissolutions.com', NULL, '$2y$12$bgSHs7thsm.vb8TMXV7Vxuka2gBPxVZ4LHDqS2WbCn7Vz7tOWDmNu', 'employee', NULL, '2025-08-18 04:43:07', '2025-08-18 04:43:07'),
(77, 'Vidha Kathait', 'vidha.kathait@aloissolutions.com', NULL, '$2y$12$fEV0..6dAknYPutatEItQOggU7tWH3siMbLoijaJk0F8vXw7Eu9/K', 'employee', NULL, '2025-08-18 04:43:07', '2025-08-18 04:43:07'),
(78, 'Isha Vaya', 'isha.vaya@aloissolutions.com', NULL, '$2y$12$vnlNwGEDqr9UYvmLbqCIauOD1nC5HLpFTcmHLQpxJy7iUuM7LV6a.', 'employee', NULL, '2025-08-18 04:43:07', '2025-08-18 04:43:07'),
(79, 'Ali Sulaimani', 'ali.imran@aloissolutions.com', NULL, '$2y$12$lo22J6IZCfQQeGVs8ggof.liUzjgOJCsivMKl9msdLypS2QW/UPsq', 'employee', NULL, '2025-08-18 04:43:07', '2025-08-18 04:43:07'),
(80, 'Jiya Jethva', 'jiya.jethva@aloissolutions.com', NULL, '$2y$12$jI9W9Ahj.RCgl4KPQe.OOuTY9VX9CihXfVPFKD6uaKl0GEzIT6pqC', 'employee', NULL, '2025-08-18 04:43:08', '2025-08-18 04:43:08'),
(81, 'Shubham Pandey', 'shubham.pandey@aloissolutions.com', NULL, '$2y$12$0295Or4e.DHMCoyNg9dyKekJD8GWgPjIRtGm.rWgwJ.oFq0qlMQDW', 'employee', NULL, '2025-08-18 04:43:08', '2025-08-18 04:43:08'),
(82, 'Ishan Sharma', 'ishan.sharma@aloissolutions.com', NULL, '$2y$12$9I0RgTjo67P0n1Mf8gzBXuUWwrT4pFy1EiOCrZEEIvhVImj04VPCy', 'employee', NULL, '2025-08-18 04:43:08', '2025-08-18 04:43:08'),
(83, 'Ashutosh Yadav', 'ashutosh@aloissolutions.com.au', NULL, '$2y$12$dDsj4KDoQ.fTLdQ80Hwmwe.QTui81RYwqyazSt8g1J3hmjWGFN.d6', 'employee', NULL, '2025-08-18 04:59:53', '2025-08-18 04:59:53'),
(84, 'Vinutha P', 'vinutha.p@aloissolutions.com', NULL, '$2y$12$J6/7MFi3zKvSdVfyJnC46OZwvvo79epqLReft/gFjc/d1rNmAtBsy', 'employee', NULL, '2025-08-18 04:59:54', '2025-08-18 04:59:54'),
(85, 'Aashay Umratkar', 'aashay@aloissolutions.com', NULL, '$2y$12$xbS633Tfhg.S0PoV6xBae.eo1234ayRXP6V9.dg5kI0sGv8WnGMyW', 'employee', NULL, '2025-08-18 04:59:54', '2025-08-18 04:59:54'),
(86, 'Ashish Krishnan', 'ashish.krishnan@aloissolutions.com', NULL, '$2y$12$cLnrUE75NdTUWSulfh.ZYO2l9wo0CVGa4wuwqiXW6NwmZfxGG5ZiO', 'employee', NULL, '2025-08-18 05:02:09', '2025-08-18 05:02:09'),
(87, 'Bablu Pandey', 'bablu.pandey@aloissolutions.com.au', NULL, '$2y$12$rKsQXTG6BPbpqIzorK8kJ.oTnQWQYfrHX1KHTYsmuuPuYuyeGTvkW', 'employee', NULL, '2025-08-18 05:03:04', '2025-08-18 05:03:04'),
(88, 'Anamika Singh', 'anamika.singh@aloissolutions.com', NULL, '$2y$12$qixRA9v7tDFmSNSgDuL8bembNiSrnirujtdfM2gme5SDjDxIS1flO', 'employee', NULL, '2025-08-18 05:03:51', '2025-08-18 05:03:51'),
(89, 'Akash Shrivastav', 'akash.shrivastav@aloissolutions.com', NULL, '$2y$12$ncnW9//KugwhOzTCOXwjJuuGIb21JwzXCN4ILZ1FXlqrES/72O.0G', 'employee', NULL, '2025-08-18 05:05:32', '2025-08-18 05:05:32'),
(90, 'Pooja Singh', 'pooja.singh@aloissolutions.com', NULL, '$2y$12$JazfeKC78LSLysAmhLXjQOcJaNtl0e.V5rPPmySlZZmy4Tcgk9uBu', 'employee', NULL, '2025-08-18 05:06:21', '2025-08-18 05:06:21'),
(91, 'Shehzad Vandriwala', 'shehzad.vandriwala@aloissolutions.com', NULL, '$2y$12$CNTB3Uq3IlM.QfpVHSbbwOiUZqIYotZzz/51eUdlalF/YBSP01bFi', 'employee', NULL, '2025-08-18 05:07:05', '2025-08-18 05:07:05'),
(92, 'Priyanka Soni', 'priyanka.soni@aloissolutions.com.au', NULL, '$2y$12$.F2ux.jgA4Ke0phDH.gUfOo.k09WD2UDzbtfyj54RAfxk0Fnjuj3y', 'employee', NULL, '2025-08-18 05:08:37', '2025-08-18 05:08:37'),
(93, 'Piyush Singh', 'piyush.singh@aloissolutions.com', NULL, '$2y$12$6fRM3YxBT9ffIRZvZRybG.Sy8D9wRskZILXppw54vVvd6qllkOD5i', 'employee', NULL, '2025-08-18 05:09:53', '2025-08-18 05:09:53'),
(94, 'Pratiksha Parmar', 'pratiksha.parmar@aloissolutions.com', NULL, '$2y$12$ku.7JGq0k.C2RxLOCJUrVOvaeSxdI93zhB0ssh/jJqa4uobbFP.R6', 'employee', NULL, '2025-08-18 05:10:52', '2025-08-18 05:10:52'),
(95, 'Rishwinder Ghuman', 'rashwinder.singh@aloissolutions.com', NULL, '$2y$12$ObovmooWmuyNav4XMTXH7Oyb2dhmuHy659O0F7/RCgzGwaQnk59WK', 'employee', NULL, '2025-08-18 05:11:37', '2025-08-18 05:11:37'),
(96, 'Gurinder Nandra', 'gurinder.nandra@aloissolutions.com', NULL, '$2y$12$ZI.42TL6ujKzLXvcMadk0eCnqA/IlGEzlB3qKqhkmik6RsG0piq4e', 'employee', NULL, '2025-08-18 05:12:19', '2025-08-18 05:12:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `branch_masters`
--
ALTER TABLE `branch_masters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branch_masters_company_id_foreign` (`company_id`);

--
-- Indexes for table `business_unit_masters`
--
ALTER TABLE `business_unit_masters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `business_unit_masters_company_id_foreign` (`company_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clients_client_code_unique` (`client_code`),
  ADD KEY `clients_company_id_foreign` (`company_id`);

--
-- Indexes for table `company_masters`
--
ALTER TABLE `company_masters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `company_masters_name_unique` (`name`);

--
-- Indexes for table `consultants`
--
ALTER TABLE `consultants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `consultants_code_unique` (`code`),
  ADD UNIQUE KEY `consultants_phone1_unique` (`phone1`),
  ADD UNIQUE KEY `consultants_email1_unique` (`email1`),
  ADD UNIQUE KEY `consultants_phone2_unique` (`phone2`),
  ADD UNIQUE KEY `consultants_email2_unique` (`email2`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employees_user_id_unique` (`user_id`),
  ADD UNIQUE KEY `employees_emp_id_unique` (`emp_id`),
  ADD UNIQUE KEY `employees_email_unique` (`email`),
  ADD UNIQUE KEY `employees_phone_unique` (`phone`),
  ADD KEY `employees_company_id_foreign` (`company_id`),
  ADD KEY `employees_checker_id_foreign` (`checker_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `job_seekers`
--
ALTER TABLE `job_seekers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_seekers_company_id_foreign` (`company_id`),
  ADD KEY `job_seekers_location_id_foreign` (`location_id`),
  ADD KEY `job_seekers_business_unit_id_foreign` (`business_unit_id`),
  ADD KEY `job_seekers_am_id_foreign` (`am_id`),
  ADD KEY `job_seekers_dm_id_foreign` (`dm_id`),
  ADD KEY `job_seekers_tl_id_foreign` (`tl_id`),
  ADD KEY `job_seekers_recruiter_id_foreign` (`recruiter_id`),
  ADD KEY `job_seekers_status_id_foreign` (`status_id`),
  ADD KEY `job_seekers_client_id_foreign` (`client_id`),
  ADD KEY `job_seekers_checker_id_foreign` (`checker_id`),
  ADD KEY `job_seekers_maker_id_foreign` (`maker_id`),
  ADD KEY `job_seekers_po_maker_id_foreign` (`po_maker_id`),
  ADD KEY `job_seekers_po_checker_id_foreign` (`po_checker_id`),
  ADD KEY `job_seekers_backout_maker_id_foreign` (`backout_maker_id`),
  ADD KEY `job_seekers_backout_checker_id_foreign` (`backout_checker_id`),
  ADD KEY `job_seekers_finance_maker_id_foreign` (`finance_maker_id`),
  ADD KEY `job_seekers_finance_checker_id_foreign` (`finance_checker_id`);

--
-- Indexes for table `main_emails`
--
ALTER TABLE `main_emails`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `main_emails_email_unique` (`email`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `status_masters`
--
ALTER TABLE `status_masters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `status_masters_status_unique` (`status`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `branch_masters`
--
ALTER TABLE `branch_masters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=199;

--
-- AUTO_INCREMENT for table `business_unit_masters`
--
ALTER TABLE `business_unit_masters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=150;

--
-- AUTO_INCREMENT for table `company_masters`
--
ALTER TABLE `company_masters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `consultants`
--
ALTER TABLE `consultants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_seekers`
--
ALTER TABLE `job_seekers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT for table `main_emails`
--
ALTER TABLE `main_emails`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `status_masters`
--
ALTER TABLE `status_masters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `branch_masters`
--
ALTER TABLE `branch_masters`
  ADD CONSTRAINT `branch_masters_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `company_masters` (`id`);

--
-- Constraints for table `business_unit_masters`
--
ALTER TABLE `business_unit_masters`
  ADD CONSTRAINT `business_unit_masters_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `company_masters` (`id`);

--
-- Constraints for table `clients`
--
ALTER TABLE `clients`
  ADD CONSTRAINT `clients_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `company_masters` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_checker_id_foreign` FOREIGN KEY (`checker_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employees_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `company_masters` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employees_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_seekers`
--
ALTER TABLE `job_seekers`
  ADD CONSTRAINT `job_seekers_am_id_foreign` FOREIGN KEY (`am_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_seekers_backout_checker_id_foreign` FOREIGN KEY (`backout_checker_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_seekers_backout_maker_id_foreign` FOREIGN KEY (`backout_maker_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_seekers_business_unit_id_foreign` FOREIGN KEY (`business_unit_id`) REFERENCES `business_unit_masters` (`id`),
  ADD CONSTRAINT `job_seekers_checker_id_foreign` FOREIGN KEY (`checker_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_seekers_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
  ADD CONSTRAINT `job_seekers_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `company_masters` (`id`),
  ADD CONSTRAINT `job_seekers_dm_id_foreign` FOREIGN KEY (`dm_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_seekers_finance_checker_id_foreign` FOREIGN KEY (`finance_checker_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_seekers_finance_maker_id_foreign` FOREIGN KEY (`finance_maker_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_seekers_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `branch_masters` (`id`),
  ADD CONSTRAINT `job_seekers_maker_id_foreign` FOREIGN KEY (`maker_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_seekers_po_checker_id_foreign` FOREIGN KEY (`po_checker_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_seekers_po_maker_id_foreign` FOREIGN KEY (`po_maker_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_seekers_recruiter_id_foreign` FOREIGN KEY (`recruiter_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_seekers_status_id_foreign` FOREIGN KEY (`status_id`) REFERENCES `status_masters` (`id`),
  ADD CONSTRAINT `job_seekers_tl_id_foreign` FOREIGN KEY (`tl_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
