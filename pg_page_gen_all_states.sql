-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 05, 2021 at 02:18 PM
-- Server version: 5.7.33
-- PHP Version: 7.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `suicidec_wp`
--

-- --------------------------------------------------------

--
-- Table structure for table `pg_page_gen_all_states`
--

CREATE TABLE `pg_page_gen_all_states` (
  `id` int(10) NOT NULL,
  `name` varchar(256) NOT NULL,
  `create_dttm` datetime NOT NULL,
  `update_dttm` datetime NOT NULL,
  `status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pg_page_gen_all_states`
--

INSERT INTO `pg_page_gen_all_states` (`id`, `name`, `create_dttm`, `update_dttm`, `status`) VALUES
(2, 'ACT', '2015-03-08 11:32:42', '2015-03-16 04:35:56', 1),
(3, 'NSW', '2015-03-08 11:32:52', '2015-03-08 11:32:52', 1),
(4, 'NT', '2015-03-08 11:33:01', '2015-03-08 11:33:01', 1),
(5, 'QLD', '2015-03-08 11:33:10', '2015-03-08 11:33:10', 1),
(6, 'SA', '2015-03-08 11:33:19', '2015-03-08 11:33:19', 1),
(7, 'TAS', '2015-03-08 11:33:27', '2015-03-08 11:33:27', 1),
(8, 'VIC', '2015-03-08 11:33:34', '2015-03-08 11:33:34', 1),
(9, 'WA', '2015-03-08 11:33:43', '2015-03-08 11:33:43', 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
