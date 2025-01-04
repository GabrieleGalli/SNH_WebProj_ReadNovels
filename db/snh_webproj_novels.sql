-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 04, 2025 at 05:34 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `snh_webproj_novels`
--
CREATE DATABASE IF NOT EXISTS `snh_webproj_novels` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `snh_webproj_novels`;

-- --------------------------------------------------------

--
-- Table structure for table `log_attempts`
--
DROP TABLE IF EXISTS `log_attempts`;
CREATE TABLE `log_attempts` (
  `ID` int(11) NOT NULL,
  `IP_ADDR` varchar(50) NOT NULL,
  `TIME` int(11) NOT NULL,
  `N_ATTEMPTS` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_attempts`
--

INSERT INTO `log_attempts` (`ID`, `IP_ADDR`, `TIME`, `N_ATTEMPTS`) VALUES
(1, '0', 1733401336, 1),
(2, '0', 1733507433, 1),
(3, '::1', 1733669486, 1),
(4, '::1', 1733669725, 1),
(5, '::1', 1733670436, 1),
(6, '::1', 1733670468, 1),
(7, '::1', 1733996168, 1);

-- --------------------------------------------------------

--
-- Table structure for table `long_novels`
--
DROP TABLE IF EXISTS `long_novels`;
CREATE TABLE `long_novels` (
  `ID` int(11) NOT NULL COMMENT 'ID univoco del record',
  `ID_U` int(11) NOT NULL COMMENT 'ID dell''utente che ha creato la novel',
  `PREMIUM` int(11) NOT NULL COMMENT 'Premium (1) o no (0)',
  `DATE` datetime NOT NULL COMMENT 'Data di creatione (timestamp)',
  `TITLE` varchar(255) NOT NULL COMMENT 'Titolo della novel',
  `FILENAME` varchar(255) NOT NULL COMMENT 'Nome del fil PDF salvato sul server'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `psw_resets`
--
DROP TABLE IF EXISTS `psw_resets`;
CREATE TABLE `psw_resets` (
  `ID` int(11) NOT NULL,
  `ID_U` int(11) NOT NULL,
  `TOKEN` varchar(64) NOT NULL,
  `EXPIRE` int(11) NOT NULL COMMENT 'Timestamp della scadenza'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `psw_resets`
--

INSERT INTO `psw_resets` (`ID`, `ID_U`, `TOKEN`, `EXPIRE`) VALUES
(29, 21, '7c2a94533b801898ede2bd38d2739d6a803a4be5f72be74ac0bd7f5fa4acfad3', 1733674352),
(32, 22, '3887541bbf745ba437aed57db3442b68b26718b1854adfc96ccdcbc4c4b7973e', 1734000069),
(33, 22, '3d55eab4b31bc9f1cc2f612b2365bcf90f0243509fdc81a0b62e38d732ffa248', 1734000117),
(34, 22, '466cd5e395f7f2d5a2e819c985eee03922052b082cd0830fbd5277e1eb01f6fe', 1734000157);

-- --------------------------------------------------------

--
-- Table structure for table `remember_me_tokens`
--
DROP TABLE IF EXISTS `remember_me_tokens`;
CREATE TABLE `remember_me_tokens` (
  `ID` int(11) NOT NULL,
  `ID_U` int(11) NOT NULL,
  `TOKEN` varchar(255) NOT NULL,
  `EXPIRE` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `remember_me_tokens`
--

INSERT INTO `remember_me_tokens` (`ID`, `ID_U`, `TOKEN`, `EXPIRE`) VALUES
(1, 17, '62aeafc24d20a4ebe5de7f23e47a7b39956d26f16cf5659dddd1ec04192cd758', 1735561544),
(2, 23, '99f46e88fffa4d9db7e82e544b5502bbd160713916c84c08420726c62a25a121', 1738595115),
(3, 23, '1849c366df9f0113b7756ca96444b4e2bf6e7c7a05ca9cb04a8579f413c398af', 1738596036),
(4, 23, 'cdb7bba57e270ec96467d3f09858c6cacee632f8a1fb4fd701b72b4bee643e1b', 1738596609),
(5, 23, '4ce69fd9f821f05a393c82b71278081eda6fa27e125aa59c1d06f04374b9f88d', 1738600236);

-- --------------------------------------------------------

--
-- Table structure for table `short_novels`
--
DROP TABLE IF EXISTS `short_novels`;
CREATE TABLE `short_novels` (
  `ID` int(11) NOT NULL,
  `ID_U` int(11) NOT NULL,
  `PREMIUM` int(11) NOT NULL,
  `DATE` datetime NOT NULL,
  `TITLE` varchar(255) NOT NULL,
  `CONTENT` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `ID` int(11) NOT NULL,
  `PREMIUM` int(11) NOT NULL COMMENT '0: Normale\r\n1: Premium',
  `USERNAME` varchar(30) NOT NULL,
  `NAME` text NOT NULL,
  `SURNAME` text NOT NULL,
  `EMAIL` varchar(30) NOT NULL,
  `PASSWORD` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`ID`, `PREMIUM`, `USERNAME`, `NAME`, `SURNAME`, `EMAIL`, `PASSWORD`) VALUES
(16, 1, 'admin', 'admin', 'admin', 'test@test.com', '$2y$10$7N33TFqkxtQmSH7nU8Nf6OmME0gLGvUr/B0zPzK.M4RXRvSdRzxka'),
(22, 1, 'gabri', 'Gabriele', 'Galli', 'gabg3000@gmail.com', '$2y$10$WlTUKpeGXXyCuvUlKVWjp.EAY4nGzYeKvgBKjF3WnZZko/PGvVHue'),
(23, 1, 'simone', 'Simone', 'Conti', 'contisimone4@gmail.com', '$2y$10$t0wIxuJs89e2JiA1O6Gx1.0edMF40a31rDO6d3a0A015w8aGtfIqC');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `log_attempts`
--
ALTER TABLE `log_attempts`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `long_novels`
--
ALTER TABLE `long_novels`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `psw_resets`
--
ALTER TABLE `psw_resets`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `remember_me_tokens`
--
ALTER TABLE `remember_me_tokens`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `short_novels`
--
ALTER TABLE `short_novels`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `log_attempts`
--
ALTER TABLE `log_attempts`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `long_novels`
--
ALTER TABLE `long_novels`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID univoco del record', AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `psw_resets`
--
ALTER TABLE `psw_resets`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `remember_me_tokens`
--
ALTER TABLE `remember_me_tokens`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `short_novels`
--
ALTER TABLE `short_novels`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
