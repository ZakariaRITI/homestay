-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 21 jan. 2026 à 15:31
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `homestay2030`
--

-- --------------------------------------------------------

--
-- Structure de la table `logements`
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `logements`
--

INSERT INTO `logements` (`id`, `user_id`, `titre`, `ville`, `type_logement`, `capacite`, `equipements`, `image`, `prix`, `prix_suggere_ia`, `created_at`) VALUES
(1, 1, 'Appartement cosy au centre', 'Casablanca', 'Appartement', 3, 'Wi-Fi, Cuisine, Climatisation', '1.jpg', 350.00, NULL, '2026-01-15 21:25:56'),
(2, 1, 'Maison traditionnelle avec patio', 'Marrakech', 'Maison', 5, 'Piscine, Jardin, Parking', '2.jpg', 500.00, NULL, '2026-01-15 21:25:56'),
(3, 1, 'Chambre moderne et lumineuse', 'Rabat', 'Chambre', 2, 'Wi-Fi, Balcon', '3.jpg', 200.00, NULL, '2026-01-15 21:25:56'),
(4, 1, 'boustane', 'casa', 'Appartement', 4, 'wifi,telephone', '1768852321_1.jpg', 500.00, 450.00, '2026-01-19 19:52:01'),
(5, 1, 'fgn', 'fgn', 'Appartement', 6, 'gfn', '1768853141_1.jpg', 600.00, NULL, '2026-01-19 20:05:41'),
(6, 1, 'sdv', 'casa', 'Appartement', 2, 'wifi', 'images/1.jpg', 805.10, 805.10, '2026-01-19 20:35:50'),
(7, 1, 'boustane', 'casa', '', 6, 'wifi', 'images/3.jpg', 1637.60, 1637.60, '2026-01-19 21:07:29'),
(8, 1, 'zreg', 'zreg', '', 9, 'zeg', 'images/2.jpg', 2180.00, 2180.00, '2026-01-19 21:24:59'),
(9, 1, 'test', 'rabat', '', 4, 'wifi', 'images/2.jpg', 700.00, 991.00, '2026-01-19 21:26:07'),
(10, 1, 'zg', 'eth', '', 3, 'erh', 'images/2.jpg', 895.50, 895.50, '2026-01-19 21:34:43'),
(11, 1, 'sv', 'erh', '', 6, 'erg', '2.jpg', 1764.00, 1764.00, '2026-01-19 21:37:12'),
(12, 1, 'rabat', 'rabat', '', 3, 'erggre', '3.jpg', 600.00, 0.00, '2026-01-19 21:38:04'),
(13, 1, 'zef', 'er', '', 6, 'er', '1.jpg', 1764.00, 1764.00, '2026-01-19 21:40:09'),
(14, 1, 'lux', 'marrakech', 'Appartement', 5, 'wifi,clim', '4.jpg', 1569.00, 1569.00, '2026-01-20 11:01:17'),
(15, 1, 'tey', 'ety', '', 6, 'zrhzrh', '1.jpg', 500.00, 1764.00, '2026-01-20 11:02:37'),
(16, 1, 'test', 'test', '', 2, 'test', '5.jpg', 839.00, 839.00, '2026-01-20 11:04:30'),
(17, 1, 'test', 'test', '', 2, 'test', '5.jpg', 839.00, 839.00, '2026-01-20 11:05:37'),
(18, 1, 'test', 'test', '', 3, 'test', '5.jpg', 895.50, 895.50, '2026-01-20 11:06:15'),
(19, 1, 'test', 'test', '', 6, 'test', '4.jpg', 1764.00, 1764.00, '2026-01-20 11:06:38'),
(20, 1, 'test', 'test', '', 3, 'test', 'logement_696f632a7d48b1.54485001.jpg', 895.50, 895.50, '2026-01-20 11:12:43'),
(21, 1, 'test', 'test', '', 6, 'test', 'logement_696f63468ca912.32754319.jpg', 1764.00, 1764.00, '2026-01-20 11:13:11'),
(22, 1, 'test', 'test', '', 3, 'test', 'logement_696f645a37e375.69497048.jpg', 895.50, 895.50, '2026-01-20 11:17:47'),
(23, 1, 'test', 'test', '', 6, 'test', 'logement_696f6483ded826.42981464.jpg', 1764.00, 1764.00, '2026-01-20 11:18:28'),
(24, 1, 'test', 'test', '', 3, 'test', 'logement_696f64c7856175.02912785.jpg', 895.50, 895.50, '2026-01-20 11:19:36'),
(25, 1, 'test', 'test', '', 7, 'test', 'logement_696f651f4bb5f2.45128340.jpg', 1887.00, 1887.00, '2026-01-20 11:21:04'),
(26, 1, 'test', 'rabat', 'Appartement', 3, 'wifi,climatise,piscine', 'logement_696f87380c0193.34668082.jpg', 1006.00, 1006.00, '2026-01-20 13:46:39'),
(27, 1, 'test', 'tanger', '', 10, 'wifi', 'logement_696fba3bd82255.59577281.jpg', 2471.00, 2471.00, '2026-01-20 17:24:13');

-- --------------------------------------------------------

--
-- Structure de la table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `logement_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `prix_total` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reservations`
--

INSERT INTO `reservations` (`id`, `logement_id`, `user_id`, `date_debut`, `date_fin`, `prix_total`, `created_at`) VALUES
(1, 2, 1, '2026-01-20', '2026-01-29', 4500.00, '2026-01-20 11:34:49'),
(2, 1, 1, '2026-01-20', '2026-01-25', 1750.00, '2026-01-20 11:35:50'),
(9, 3, 1, '2026-01-20', '2026-01-23', 600.00, '2026-01-20 17:24:47');

-- --------------------------------------------------------

--
-- Structure de la table `users`
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
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `nom`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'user', 'user@gmail.com', '$2y$10$UtLh3yBVDT2p8rSw0nq9quA46vMj78asqh6iTsakuxL8xKkOogCk6', 'USER', '2026-01-15 21:01:36'),
(2, 'Admin Test', 'admin@test.com', '$2y$10$6fZCh6YElco2l5LtELn9au7BCM//uHET9tJU/QiB8eNLhE.iWhvTm', 'ADMIN', '2026-01-15 21:01:36'),
(3, 'riti zakaria', 'zak@gmail.com', '$2y$10$jIaMl5Z72X5.9CXat/Jxeee.Be3UkDGj0V9RYu0szpFVlQ32KsMQS', 'USER', '2026-01-15 21:22:07');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `logements`
--
ALTER TABLE `logements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `logement_id` (`logement_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `logements`
--
ALTER TABLE `logements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT pour la table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `logements`
--
ALTER TABLE `logements`
  ADD CONSTRAINT `logements_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`logement_id`) REFERENCES `logements` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
