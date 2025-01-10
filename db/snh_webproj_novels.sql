-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Gen 10, 2025 alle 11:19
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

CREATE TABLE `log_attempts` (
  `ID` int(11) NOT NULL,
  `USERNAME` varchar(30) NOT NULL,
  `IP_ADDR` varchar(50) NOT NULL,
  `TIME` int(11) NOT NULL,
  `N_ATTEMPTS` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `long_novels`
--

CREATE TABLE `long_novels` (
  `ID` int(11) NOT NULL COMMENT 'ID univoco del record',
  `ID_U` int(11) NOT NULL COMMENT 'ID dell''utente che ha creato la novel',
  `PREMIUM` int(11) NOT NULL COMMENT 'Premium (1) o no (0)',
  `DATE` datetime NOT NULL COMMENT 'Data di creatione (timestamp)',
  `TITLE` varchar(255) NOT NULL COMMENT 'Titolo della novel',
  `FILENAME` varchar(255) NOT NULL COMMENT 'Nome del fil PDF salvato sul server'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `long_novels`
--

INSERT INTO `long_novels` (`ID`, `ID_U`, `PREMIUM`, `DATE`, `TITLE`, `FILENAME`) VALUES
(15, 30, 0, '2025-01-10 10:00:28', 'Il Viaggio Magico di Luna', '6780e1acf1edd_Il Viaggio Magico di Luna.pdf'),
(16, 30, 1, '2025-01-10 10:04:27', 'Il Viaggio Magico di Luna', '6780e29b32aad_Il Viaggio Magico di Luna.pdf');

-- --------------------------------------------------------

--
-- Struttura della tabella `psw_resets`
--

CREATE TABLE `psw_resets` (
  `ID` int(11) NOT NULL,
  `ID_U` int(11) NOT NULL,
  `TOKEN` varchar(64) NOT NULL,
  `EXPIRE` int(11) NOT NULL COMMENT 'Timestamp della scadenza'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `remember_me_tokens`
--

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
(9, 29, '7ab5620803ba62ee3cc90924f2454783cb7ef83dedb467489cf14e805c20428f', 1739090288);

-- --------------------------------------------------------

--
-- Struttura della tabella `short_novels`
--

CREATE TABLE `short_novels` (
  `ID` int(11) NOT NULL,
  `ID_U` int(11) NOT NULL,
  `PREMIUM` int(11) NOT NULL,
  `DATE` datetime NOT NULL,
  `TITLE` varchar(255) NOT NULL,
  `CONTENT` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `short_novels`
--

INSERT INTO `short_novels` (`ID`, `ID_U`, `PREMIUM`, `DATE`, `TITLE`, `CONTENT`) VALUES
(41, 30, 1, '2025-01-10 09:55:48', 'Il Viaggio Magico di Luna', 'Alert(&amp;quot;ciao&amp;quot;)'),
(42, 29, 1, '2025-01-10 11:04:14', 'Il Viaggio Magico di Luna', 'deed'),
(43, 29, 0, '2025-01-10 11:04:22', 'Prova di gabri', 'cece');

-- --------------------------------------------------------

--
-- Struttura della tabella `users`
--

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
(26, 1, 'admin', 'admin', 'admin', 'admin@admin.admin', '$2y$10$65eq7Qu3YvirKbMcAdzl9e9jN6ZBxUewj2m16V5dwung2EWrUok2e'),
(29, 1, 'gabri', 'gabriele', 'galli', 'gabg3000@gmail.com', '$2y$10$.ZIFG.QuP7RyyJZReNXS6.beK0PZTHtmdgZJy8LmsHVuA2y.TdSji'),
(30, 0, 'simo', 'simone', 'conti', 'test@test.com', '$2y$10$JZ9s.i7p2BDGO5uH32Svv.hN9555fOn62FnMN/By07xYWbR775QBe');

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
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT per la tabella `long_novels`
--
ALTER TABLE `long_novels`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID univoco del record', AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT per la tabella `psw_resets`
--
ALTER TABLE `psw_resets`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT per la tabella `remember_me_tokens`
--
ALTER TABLE `remember_me_tokens`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT per la tabella `short_novels`
--
ALTER TABLE `short_novels`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT per la tabella `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
