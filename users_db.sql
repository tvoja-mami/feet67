-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gostitelj: 127.0.0.1
-- Čas nastanka: 30. okt 2025 ob 21.37
-- Različica strežnika: 10.4.32-MariaDB
-- Različica PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Zbirka podatkov: `users_db`
--

-- --------------------------------------------------------

--
-- Struktura tabele `grades`
--

CREATE TABLE `grades` (
  `id` int(11) NOT NULL,
  `student_email` varchar(255) DEFAULT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `grade` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Odloži podatke za tabelo `grades`
--

INSERT INTO `grades` (`id`, `student_email`, `subject`, `grade`, `date`) VALUES
(3, 'zan.user@gmail.com', 'Slovenščina', 4, '2025-10-30'),
(4, 'zan.user@gmail.com', 'Slovenščina', 1, '2025-10-30');

-- --------------------------------------------------------

--
-- Struktura tabele `profesor_ocena`
--

CREATE TABLE `profesor_ocena` (
  `id` int(11) NOT NULL,
  `ocena` int(5) NOT NULL,
  `ura_oddaje` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabele `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('učenec','profesor') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Odloži podatke za tabelo `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`) VALUES
(11, 'Zan', 'zan.user@gmail.com', '$2y$10$u9a20eIGuDaaFwV2VISNIeCvgWV8tbDXn6WCRsRQU7yBEnBDNDekm', 'učenec'),
(12, 'Zan', 'zan.admin@gmail.com', '$2y$10$gRlef2B3YhK0byC3l5nKr.jkkNKkR1bYNrvhZtBiGjTYl0AYk9xwe', 'profesor'),
(13, 'zannk', 'zan.k@gmail.com', '$2y$10$Jp1zv.u276K.iJ/5/ZN7p.vzHUgl1t4U3f6CcUCamBHTawACDE39C', 'profesor'),
(14, 'zank6', 'zan.kk@gmail.com', '$2y$10$xO/DTV5EDVrVeBhU4bHNS.ZWXN8f.Z5OkXpThKYAeL81ujKKEtUKa', 'učenec');

--
-- Indeksi zavrženih tabel
--

--
-- Indeksi tabele `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`);

--
-- Indeksi tabele `profesor_ocena`
--
ALTER TABLE `profesor_ocena`
  ADD PRIMARY KEY (`id`);

--
-- Indeksi tabele `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_unique` (`email`);

--
-- AUTO_INCREMENT zavrženih tabel
--

--
-- AUTO_INCREMENT tabele `grades`
--
ALTER TABLE `grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT tabele `profesor_ocena`
--
ALTER TABLE `profesor_ocena`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT tabele `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
