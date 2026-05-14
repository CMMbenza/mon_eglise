-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 14 mai 2026 à 16:22
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `eglise_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `annonces`
--

CREATE TABLE `annonces` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `contenu` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `annonces`
--

INSERT INTO `annonces` (`id`, `titre`, `contenu`, `created_at`) VALUES
(1, 'Annonce & Communiqué', 'Chaque mardi nous avons culte.\r\nLe mariage de notre sœur c\'est pour ce vendre 12.05.2025', '2026-05-14 13:03:17');

-- --------------------------------------------------------

--
-- Structure de la table `cultes`
--

CREATE TABLE `cultes` (
  `id` int(11) NOT NULL,
  `theme` varchar(255) NOT NULL,
  `passage_biblique` varchar(255) DEFAULT NULL,
  `orateur` varchar(150) DEFAULT NULL,
  `interprete` varchar(150) DEFAULT NULL,
  `hommes` int(11) DEFAULT 0,
  `femmes` int(11) DEFAULT 0,
  `offrande` decimal(10,2) DEFAULT 0.00,
  `dime` decimal(10,2) DEFAULT 0.00,
  `sociale` decimal(10,2) DEFAULT 0.00,
  `autres` decimal(10,2) DEFAULT 0.00,
  `date_culte` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `cultes`
--

INSERT INTO `cultes` (`id`, `theme`, `passage_biblique`, `orateur`, `interprete`, `hommes`, `femmes`, `offrande`, `dime`, `sociale`, `autres`, `date_culte`, `created_at`) VALUES
(1, 'L\'AMOUR DE DIEU', 'JEAN 3:16', 'PST. ALBIN JOSEPH MPUTU', 'INT. GEDEON', 12, 22, 40.00, 10.00, 1500.00, 6000.00, '2026-05-13', '2026-05-14 12:48:00'),
(2, 'LA PUISSANCE DE DIEU', 'MATTHIEU 7:7', 'PST. ALBIN JOSEPH MPUTUss', 'INT. GEDEON', 18, 21, 4100.00, 50000.00, 1200.00, 1200.00, '2026-05-14', '2026-05-14 12:48:30');

-- --------------------------------------------------------

--
-- Structure de la table `depenses`
--

CREATE TABLE `depenses` (
  `id` int(11) NOT NULL,
  `motif` varchar(255) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `beneficiaire` varchar(150) DEFAULT NULL,
  `date_depense` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `depenses`
--

INSERT INTO `depenses` (`id`, `motif`, `montant`, `beneficiaire`, `date_depense`, `created_at`) VALUES
(1, 'Achat chaise', 500.00, NULL, '2026-05-12', '2026-05-14 13:00:54'),
(2, 'ACHAT DES MICROS', 10.00, NULL, '2026-05-14', '2026-05-14 13:01:03');

-- --------------------------------------------------------

--
-- Structure de la table `engagements_fonds`
--

CREATE TABLE `engagements_fonds` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `fonds_id` int(11) NOT NULL,
  `montant_engage` decimal(10,2) NOT NULL,
  `periode` enum('Journalier','Hebdomadaire','Mensuel','Chaque culte','Autre') NOT NULL,
  `description_periode` varchar(255) DEFAULT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `statut` enum('En cours','Terminé','Suspendu') DEFAULT 'En cours',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cycle_actif` tinyint(1) DEFAULT 1,
  `cycle_num` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `engagements_fonds`
--

INSERT INTO `engagements_fonds` (`id`, `user_id`, `fonds_id`, `montant_engage`, `periode`, `description_periode`, `date_debut`, `date_fin`, `statut`, `created_at`, `cycle_actif`, `cycle_num`) VALUES
(1, 5, 2, 5.00, 'Chaque culte', 'Chaque culte', '2026-05-14', '2026-12-31', 'En cours', '2026-05-14 13:44:07', 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `fideles`
--

CREATE TABLE `fideles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nom` varchar(100) NOT NULL,
  `postnom` varchar(100) DEFAULT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `sexe` enum('M','F') NOT NULL,
  `telephone` varchar(30) DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `fideles`
--

INSERT INTO `fideles` (`id`, `user_id`, `nom`, `postnom`, `prenom`, `sexe`, `telephone`, `adresse`, `date_naissance`, `created_at`) VALUES
(1, 5, 'NGOMA', 'MBENZA', 'CHRIS', 'M', '0815427815', 'KINSHASA', '2026-04-27', '2026-05-14 12:40:05'),
(2, 6, 'NZEMBO', 'MBENZA', 'DAVID', 'M', '0892530612', 'MONT NGAFULA', '2025-12-01', '2026-05-14 12:41:49'),
(3, 7, 'MBUMBA', 'MBENZA', 'MIRIAME', 'F', '0894484816', 'GOMBE', '2026-02-09', '2026-05-14 12:44:39');

-- --------------------------------------------------------

--
-- Structure de la table `fonds`
--

CREATE TABLE `fonds` (
  `id` int(11) NOT NULL,
  `montant` varchar(50) NOT NULL,
  `motif` text NOT NULL,
  `campagne` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `fonds`
--

INSERT INTO `fonds` (`id`, `montant`, `motif`, `campagne`, `created_at`) VALUES
(1, '500, 25, 10, 32, 10', 'Nous avons besoin de vos soutiens pour payer la location parcellaire.\r\net nous mettons en proposition des montants.', 'Paiement louer parcelle', '2026-05-14 13:06:15'),
(2, '20, 10, 5', 'Nous avons plus des micros et nous sollicitons votre soutiens pour l\'achat des micros.', 'Achat micro', '2026-05-14 13:06:52');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('admin','visiteur') DEFAULT 'visiteur',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `nom`, `email`, `mot_de_passe`, `role`, `created_at`) VALUES
(1, 'Joseph Albin Mputu', 'admin', '$2y$10$36Jmsq/xaPSMHbYTpTUKZuHXQNbDVIuIBNo4LuNSqhiWcK/vXtyl2', 'admin', '2026-05-11 13:40:25'),
(2, 'ODIA DIVINE MUAMBA', 'Odia Divine', '$2y$10$36Jmsq/xaPSMHbYTpTUKZuHXQNbDVIuIBNo4LuNSqhiWcK/vXtyl2', 'visiteur', '2026-05-11 13:40:25'),
(3, 'MBENZA MBENZA Charly', 'Charly Mbenza', '$2y$10$N27//rT045p1fqBdgcZULOVdHlUkZtIu9CygXPFOGOT1CPQGU46H6', 'visiteur', '2026-04-12 19:55:37'),
(4, 'MBUMBA MBENZA', 'Miriam Mbenza', '$2y$10$.k8Lwuftot7r5d/ek1wiPu7BnPhe/huLaO/RgI4H/wTuhAmIYxynS', 'visiteur', '2026-04-12 19:59:16'),
(5, 'NGOMA MBENZA', 'Chris Mbenza', '$2y$10$K7pjuJnSmRboEkfm4VIKK.YGV6w0sHUePBDFT/SWMGr1rabABKbHS', 'visiteur', '2026-05-14 12:40:05'),
(6, 'NZEMBO MBENZA', 'Nzembo Mbenza', '$2y$10$JPIH88R54HpWYW1iwWIR4e1t2PpCjZmIF5qHnn1laSlPjQVZOJX/S', 'visiteur', '2026-05-14 12:41:49'),
(7, 'MBUMBA MBENZA', 'Mbumba Mbenza', '$2y$10$yqJ5Qflr6CPnGfLQQhcbvugUNIO45yFtcRyUrPNiiNR0E1UwCqqR6', 'visiteur', '2026-05-14 12:44:39');

-- --------------------------------------------------------

--
-- Structure de la table `versements_fonds`
--

CREATE TABLE `versements_fonds` (
  `id` int(11) NOT NULL,
  `engagement_id` int(11) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `commentaire` text DEFAULT NULL,
  `date_versement` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `versements_fonds`
--

INSERT INTO `versements_fonds` (`id`, `engagement_id`, `montant`, `commentaire`, `date_versement`, `created_at`) VALUES
(1, 1, 2.00, 'RAS', '2026-05-14', '2026-05-14 13:52:24');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `annonces`
--
ALTER TABLE `annonces`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `cultes`
--
ALTER TABLE `cultes`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `depenses`
--
ALTER TABLE `depenses`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `engagements_fonds`
--
ALTER TABLE `engagements_fonds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_engagement_user` (`user_id`),
  ADD KEY `fk_engagement_fonds` (`fonds_id`);

--
-- Index pour la table `fideles`
--
ALTER TABLE `fideles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_fidele_user` (`user_id`);

--
-- Index pour la table `fonds`
--
ALTER TABLE `fonds`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `versements_fonds`
--
ALTER TABLE `versements_fonds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_versement_engagement` (`engagement_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `annonces`
--
ALTER TABLE `annonces`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `cultes`
--
ALTER TABLE `cultes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `depenses`
--
ALTER TABLE `depenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `engagements_fonds`
--
ALTER TABLE `engagements_fonds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `fideles`
--
ALTER TABLE `fideles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `fonds`
--
ALTER TABLE `fonds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `versements_fonds`
--
ALTER TABLE `versements_fonds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `engagements_fonds`
--
ALTER TABLE `engagements_fonds`
  ADD CONSTRAINT `fk_engagement_fonds` FOREIGN KEY (`fonds_id`) REFERENCES `fonds` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_engagement_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `fideles`
--
ALTER TABLE `fideles`
  ADD CONSTRAINT `fk_fidele_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `versements_fonds`
--
ALTER TABLE `versements_fonds`
  ADD CONSTRAINT `fk_versement_engagement` FOREIGN KEY (`engagement_id`) REFERENCES `engagements_fonds` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
