-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 09, 2023 at 11:31 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.0.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `boiler`
--

-- --------------------------------------------------------

--
-- Table structure for table `apptoken`
--

CREATE TABLE `apptoken` (
  `id` int(15) NOT NULL,
  `apptoken` varchar(100) NOT NULL,
  `appname` varchar(40) NOT NULL,
  `devName` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `apptoken`
--

INSERT INTO `apptoken` (`id`, `apptoken`, `appname`, `devName`) VALUES
(1, 'eyJpZCI6MSwibmFtZSI6IkJpbGx5aGFkaWF0IFRhb2ZlZXEifQ==', 'boiler', 'Billycodes');

-- --------------------------------------------------------

--
-- Table structure for table `tblusers`
--

CREATE TABLE `tblusers` (
  `id` int(15) NOT NULL,
  `name` varchar(100) NOT NULL,
  `mail` varchar(100) NOT NULL,
  `phone` int(20) NOT NULL,
  `address` varchar(100) NOT NULL,
  `pword` varchar(200) NOT NULL,
  `usertoken` int(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblusers`
--

INSERT INTO `tblusers` (`id`, `name`, `mail`, `phone`, `address`, `pword`, `usertoken`) VALUES
(1, 'Billyhadit', 'billyhadiattaofeeq@gmail.com', 2147483647, 't is a long established fact that a reader will be distracted by the readable content of a page when', '$2y$10$lzYSC2HnRxAgWf96VIXxuONOPJtq6cSDzVWlFDW8df2bcgAGJJB16', 370958);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `apptoken`
--
ALTER TABLE `apptoken`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblusers`
--
ALTER TABLE `tblusers`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `apptoken`
--
ALTER TABLE `apptoken`
  MODIFY `id` int(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblusers`
--
ALTER TABLE `tblusers`
  MODIFY `id` int(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
