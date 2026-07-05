-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 20, 2026 at 11:17 PM
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
-- Database: `homestay2030`
--

-- --------------------------------------------------------

--
-- Table structure for table `indisponibilites`
--

CREATE TABLE `indisponibilites` (
  `id` int(11) NOT NULL,
  `logement_id` int(11) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `motif` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `logements`
--

CREATE TABLE `logements` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `titre` varchar(150) DEFAULT NULL,
  `ville` varchar(100) NOT NULL,
  `type_logement` enum('Chambre','Appartement','Maison') NOT NULL,
  `capacite` int(11) NOT NULL,
  `equipements` text DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `prix` decimal(10,2) DEFAULT NULL,
  `prix_suggere_ia` decimal(10,2) DEFAULT NULL,
  `disponible` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logements`
--

INSERT INTO `logements` (`id`, `user_id`, `titre`, `ville`, `type_logement`, `capacite`, `equipements`, `image`, `prix`, `prix_suggere_ia`, `disponible`, `created_at`) VALUES
(1, 1, 'Appartement cosy au centre', 'Casablanca', 'Appartement', 3, 'Wi-Fi, Cuisine, Climatisation', '1.jpg', 350.00, NULL, 1, '2026-01-15 21:25:56'),
(2, 1, 'Maison traditionnelle avec patio', 'Marrakech', 'Maison', 5, 'Piscine, Jardin, Parking', '2.jpg', 500.00, NULL, 1, '2026-01-15 21:25:56'),
(3, 1, 'Chambre moderne et lumineuse', 'Rabat', 'Chambre', 2, 'Wi-Fi, Balcon', '3.jpg', 200.00, NULL, 1, '2026-01-15 21:25:56'),
(4, 1, 'boustane', 'casa', 'Appartement', 4, 'wifi,telephone', '1768852321_1.jpg', 500.00, 450.00, 1, '2026-01-19 19:52:01'),
(5, 1, 'fgn', 'fgn', 'Appartement', 6, 'gfn', '1768853141_1.jpg', 600.00, NULL, 0, '2026-01-19 20:05:41'),
(6, 1, 'sdv', 'casa', 'Appartement', 2, 'wifi', 'images/1.jpg', 805.10, 805.10, 1, '2026-01-19 20:35:50'),
(7, 1, 'boustane', 'casa', '', 6, 'wifi', 'images/3.jpg', 1637.60, 1637.60, 1, '2026-01-19 21:07:29'),
(8, 1, 'zreg', 'zreg', '', 9, 'zeg', 'images/2.jpg', 2180.00, 2180.00, 1, '2026-01-19 21:24:59'),
(9, 1, 'test', 'rabat', '', 4, 'wifi', 'images/2.jpg', 700.00, 991.00, 1, '2026-01-19 21:26:07'),
(10, 1, 'zg', 'eth', '', 3, 'erh', 'images/2.jpg', 895.50, 895.50, 1, '2026-01-19 21:34:43'),
(11, 1, 'sv', 'erh', '', 6, 'erg', '2.jpg', 1764.00, 1764.00, 1, '2026-01-19 21:37:12'),
(12, 1, 'rabat', 'rabat', '', 3, 'erggre', '3.jpg', 600.00, 0.00, 1, '2026-01-19 21:38:04'),
(13, 1, 'zef', 'er', '', 6, 'er', '1.jpg', 1764.00, 1764.00, 0, '2026-01-19 21:40:09');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `logement_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `statut` enum('EN_ATTENTE','CONFIRMEE','ANNULEE') NOT NULL DEFAULT 'EN_ATTENTE',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `logement_id`, `user_id`, `date_debut`, `date_fin`, `statut`, `created_at`) VALUES
(1, 13, 1, '2026-01-01', '2026-01-04', 'CONFIRMEE', '2026-01-20 15:53:13');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('USER','ADMIN') DEFAULT 'USER',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nom`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Test User', 'user@test.com', '$2y$10$9shSKEyHPESqzHeIgeD3kusPPyb66K1eVYd0SXo9wF9LsFNdKw0me', 'USER', '2026-01-15 21:01:36'),
(2, 'Admin Test', 'admin@test.com', '$2y$10$6fZCh6YElco2l5LtELn9au7BCM//uHET9tJU/QiB8eNLhE.iWhvTm', 'ADMIN', '2026-01-15 21:01:36'),
(3, 'riti zakaria', 'zak@gmail.com', '$2y$10$jIaMl5Z72X5.9CXat/Jxeee.Be3UkDGj0V9RYu0szpFVlQ32KsMQS', 'USER', '2026-01-15 21:22:07'),
(4, 'ysf', 'ysf@test.com', '$2y$10$8r/hY1vakkFCgqwpAB1dgeWdD8ABIORxo12vc7K2Z.M1ozZSXryYO', 'USER', '2026-01-20 15:01:25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `indisponibilites`
--
ALTER TABLE `indisponibilites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_logement_dates` (`logement_id`,`date_debut`,`date_fin`);

--
-- Indexes for table `logements`
--
ALTER TABLE `logements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `logement_id` (`logement_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_reservations_statut` (`statut`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `indisponibilites`
--
ALTER TABLE `indisponibilites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `logements`
--
ALTER TABLE `logements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `indisponibilites`
--
ALTER TABLE `indisponibilites`
  ADD CONSTRAINT `fk_indispo_logement` FOREIGN KEY (`logement_id`) REFERENCES `logements` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `logements`
--
ALTER TABLE `logements`
  ADD CONSTRAINT `logements_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`logement_id`) REFERENCES `logements` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
