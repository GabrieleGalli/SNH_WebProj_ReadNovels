-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Dic 12, 2024 alle 11:11
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.2.12

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
-- Struttura della tabella `log_attempts`
--

DROP TABLE IF EXISTS `log_attempts`;
CREATE TABLE `log_attempts` (
  `ID` int(11) NOT NULL,
  `IP_ADDR` varchar(50) NOT NULL,
  `TIME` int(11) NOT NULL,
  `N_ATTEMPTS` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `log_attempts`
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
-- Struttura della tabella `long_novels`
--

DROP TABLE IF EXISTS `long_novels`;
CREATE TABLE `long_novels` (
  `ID` int(11) NOT NULL COMMENT 'ID univoco del record',
  `ID_U` int(11) NOT NULL COMMENT 'ID dell''utente che ha creato la novel',
  `PREMIUM` int(11) NOT NULL COMMENT 'Premium (1) o no (0)',
  `DATE` int(11) NOT NULL COMMENT 'Data di creatione (timestamp)',
  `TITLE` varchar(255) NOT NULL COMMENT 'Titolo della novel',
  `FILENAME` varchar(255) NOT NULL COMMENT 'Nome del fil PDF salvato sul server'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `psw_resets`
--

DROP TABLE IF EXISTS `psw_resets`;
CREATE TABLE `psw_resets` (
  `ID` int(11) NOT NULL,
  `ID_U` int(11) NOT NULL,
  `TOKEN` varchar(64) NOT NULL,
  `EXPIRE` int(11) NOT NULL COMMENT 'Timestamp della scadenza'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `psw_resets`
--

INSERT INTO `psw_resets` (`ID`, `ID_U`, `TOKEN`, `EXPIRE`) VALUES
(29, 21, '7c2a94533b801898ede2bd38d2739d6a803a4be5f72be74ac0bd7f5fa4acfad3', 1733674352),
(32, 22, '3887541bbf745ba437aed57db3442b68b26718b1854adfc96ccdcbc4c4b7973e', 1734000069),
(33, 22, '3d55eab4b31bc9f1cc2f612b2365bcf90f0243509fdc81a0b62e38d732ffa248', 1734000117),
(34, 22, '466cd5e395f7f2d5a2e819c985eee03922052b082cd0830fbd5277e1eb01f6fe', 1734000157);

-- --------------------------------------------------------

--
-- Struttura della tabella `remember_me_tokens`
--

DROP TABLE IF EXISTS `remember_me_tokens`;
CREATE TABLE `remember_me_tokens` (
  `ID` int(11) NOT NULL,
  `ID_U` int(11) NOT NULL,
  `TOKEN` varchar(255) NOT NULL,
  `EXPIRE` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `remember_me_tokens`
--

INSERT INTO `remember_me_tokens` (`ID`, `ID_U`, `TOKEN`, `EXPIRE`) VALUES
(1, 17, '62aeafc24d20a4ebe5de7f23e47a7b39956d26f16cf5659dddd1ec04192cd758', 1735561544);

-- --------------------------------------------------------

--
-- Struttura della tabella `short_novels`
--

DROP TABLE IF EXISTS `short_novels`;
CREATE TABLE `short_novels` (
  `ID` int(11) NOT NULL,
  `ID_U` int(11) NOT NULL,
  `PREMIUM` int(11) NOT NULL,
  `DATE` int(11) NOT NULL,
  `TITLE` varchar(255) NOT NULL,
  `CONTENT` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `short_novels`
--

INSERT INTO `short_novels` (`ID`, `ID_U`, `PREMIUM`, `DATE`, `TITLE`, `CONTENT`) VALUES
(4, 6, 1, 1732785664, 'Prova di gabri', 'Alert(&amp;quot;&amp;quot;)'),
(7, 22, 1, 2147483647, 'Il Viaggio Magico di Luna', 'alert(&amp;quot;ciao&amp;quot;)');

-- --------------------------------------------------------

--
-- Struttura della tabella `users`
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
-- Dump dei dati per la tabella `users`
--

INSERT INTO `users` (`ID`, `PREMIUM`, `USERNAME`, `NAME`, `SURNAME`, `EMAIL`, `PASSWORD`) VALUES
(16, 1, 'admin', 'admin', 'admin', 'test@test.com', '$2y$10$7N33TFqkxtQmSH7nU8Nf6OmME0gLGvUr/B0zPzK.M4RXRvSdRzxka'),
(22, 1, 'gabri', 'Gabriele', 'Galli', 'gabg3000@gmail.com', '$2y$10$WlTUKpeGXXyCuvUlKVWjp.EAY4nGzYeKvgBKjF3WnZZko/PGvVHue');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `log_attempts`
--
ALTER TABLE `log_attempts`
  ADD PRIMARY KEY (`ID`);

--
-- Indici per le tabelle `long_novels`
--
ALTER TABLE `long_novels`
  ADD PRIMARY KEY (`ID`);

--
-- Indici per le tabelle `psw_resets`
--
ALTER TABLE `psw_resets`
  ADD PRIMARY KEY (`ID`);

--
-- Indici per le tabelle `remember_me_tokens`
--
ALTER TABLE `remember_me_tokens`
  ADD PRIMARY KEY (`ID`);

--
-- Indici per le tabelle `short_novels`
--
ALTER TABLE `short_novels`
  ADD PRIMARY KEY (`ID`);

--
-- Indici per le tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `log_attempts`
--
ALTER TABLE `log_attempts`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT per la tabella `long_novels`
--
ALTER TABLE `long_novels`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID univoco del record', AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT per la tabella `psw_resets`
--
ALTER TABLE `psw_resets`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT per la tabella `remember_me_tokens`
--
ALTER TABLE `remember_me_tokens`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT per la tabella `short_novels`
--
ALTER TABLE `short_novels`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT per la tabella `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
