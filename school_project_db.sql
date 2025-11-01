-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `school_project_db`
--
CREATE DATABASE IF NOT EXISTS `school_project_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `school_project_db`;

-- --------------------------------------------------------

--
-- Table structure for table `uporabniki` (Users)
--
DROP TABLE IF EXISTS `uporabniki`;
CREATE TABLE `uporabniki` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uporabnisko_ime` VARCHAR(50) NOT NULL UNIQUE,
    `geslo` VARCHAR(255) NOT NULL,
    `ime` VARCHAR(100) NOT NULL,
    `priimek` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `razred` CHAR(3) DEFAULT NULL,
    `vloga` ENUM('admin', 'ucitelj', 'ucenec') NOT NULL,
    `ustvarjen_ob` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `predmeti` (Subjects)
--
DROP TABLE IF EXISTS `predmeti`;
CREATE TABLE `predmeti` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `ime` VARCHAR(255) NOT NULL,
    `opis` TEXT DEFAULT NULL,
    `kljuc_za_vpis` VARCHAR(8) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gradiva` (Teaching Materials)
--
DROP TABLE IF EXISTS `gradiva`;
CREATE TABLE `gradiva` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `id_predmet` BIGINT UNSIGNED NOT NULL,
    `naslov` VARCHAR(255) NOT NULL,
    `pot_datoteke` VARCHAR(512) NOT NULL,
    `izvirno_ime_datoteke` VARCHAR(255) NOT NULL,
    `nalozen_ob` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`id_predmet`) REFERENCES `predmeti`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `naloge` (Assignments)
--
DROP TABLE IF EXISTS `naloge`;
CREATE TABLE `naloge` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `id_predmet` BIGINT UNSIGNED NOT NULL,
    `naslov` VARCHAR(255) NOT NULL,
    `opis` TEXT DEFAULT NULL,
    `rok_oddaje` DATETIME NOT NULL,
    FOREIGN KEY (`id_predmet`) REFERENCES `predmeti`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oddaje` (Submissions)
--
DROP TABLE IF EXISTS `oddaje`;
CREATE TABLE `oddaje` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `id_naloga` BIGINT UNSIGNED NOT NULL,
    `id_ucenec` BIGINT UNSIGNED NOT NULL,
    `pot_datoteke` VARCHAR(512) NOT NULL,
    `izvirno_ime_datoteke` VARCHAR(255) NOT NULL,
    `ocena` TINYINT UNSIGNED DEFAULT NULL,
    `komentar_ucitelja` TEXT DEFAULT NULL,
    `oddano_ob` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unq_naloga_ucenec` (`id_naloga`, `id_ucenec`),
    FOREIGN KEY (`id_naloga`) REFERENCES `naloge`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`id_ucenec`) REFERENCES `uporabniki`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ucitelji_predmeti` (Teachers <-> Subjects)
--
DROP TABLE IF EXISTS `ucitelji_predmeti`;
CREATE TABLE `ucitelji_predmeti` (
    `id_ucitelj` BIGINT UNSIGNED NOT NULL,
    `id_predmet` BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (`id_ucitelj`, `id_predmet`),
    FOREIGN KEY (`id_ucitelj`) REFERENCES `uporabniki`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`id_predmet`) REFERENCES `predmeti`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ucenci_predmeti` (Students <-> Subjects)
--
DROP TABLE IF EXISTS `ucenci_predmeti`;
CREATE TABLE `ucenci_predmeti` (
    `id_ucenec` BIGINT UNSIGNED NOT NULL,
    `id_predmet` BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (`id_ucenec`, `id_predmet`),
    FOREIGN KEY (`id_ucenec`) REFERENCES `uporabniki`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`id_predmet`) REFERENCES `predmeti`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add admin user with password 'admin123'
INSERT INTO `uporabniki` (`uporabnisko_ime`, `geslo`, `ime`, `priimek`, `email`, `vloga`) VALUES
('admin', '$2y$10$8WrFULAqUWgj6oWP8aqgUenp8QJGoP6i3qVHcPvQXHMcMH1BtdLgG', 'Administrator', 'Sistema', 'admin@school.com', 'admin');

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;