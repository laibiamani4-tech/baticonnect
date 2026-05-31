-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 20 mai 2026 à 23:26
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
-- Base de données : `baticonnect`
--

-- --------------------------------------------------------

--
-- Structure de la table `fournisseurs`
--

CREATE TABLE `fournisseurs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nom_entreprise` varchar(150) NOT NULL,
  `siret` varchar(50) DEFAULT NULL,
  `adresse` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `fournisseurs`
--

INSERT INTO `fournisseurs` (`id`, `user_id`, `nom_entreprise`, `siret`, `adresse`) VALUES
(1, 4, 'Matériaux Pro SNC', '41234567890123', 'Zone Industrielle, Sétif'),
(2, 5, 'Bâtissime SARL', '98765432109876', 'Hydra, Alger'),
(3, 7, 'SDJZDBJZDBZUD', 'DBZJADBZAD', 'BDZJDGZAUD');

-- --------------------------------------------------------

--
-- Structure de la table `produits`
--

CREATE TABLE `produits` (
  `id` int(11) NOT NULL,
  `fournisseur_id` int(11) NOT NULL,
  `nom_produit` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `prix` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `categorie` enum('ciment','fer','carrelage','peinture','platre','autre') DEFAULT 'autre',
  `stock` int(11) DEFAULT 0,
  `telephone_contact` varchar(20) DEFAULT NULL,
  `email_contact` varchar(100) DEFAULT NULL,
  `facebook_link` varchar(255) DEFAULT NULL,
  `localisation` varchar(255) DEFAULT NULL,
  `date_ajout` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`id`, `fournisseur_id`, `nom_produit`, `description`, `prix`, `image`, `categorie`, `stock`, `telephone_contact`, `email_contact`, `facebook_link`, `localisation`, `date_ajout`) VALUES
(1, 1, 'Ciment Portland CPJ 45', 'Ciment de haute qualité pour construction résidentielle et industrielle. Sac de 50kg.', 1450.00, 'uploads/ciment.jpg', 'ciment', 500, '0555123456', 'ventes@matpro.dz', 'https://facebook.com/matpro', 'Sétif', '2026-05-14 16:56:53'),
(2, 1, 'Fer à béton Ø12mm', 'Fer à béton laminoir, norme algérienne. Longueur 12m.', 3200.00, 'uploads/fer.jpg', 'fer', 200, '0555123456', 'ventes@matpro.dz', 'https://facebook.com/matpro', 'Sétif', '2026-05-14 16:56:53'),
(3, 2, 'Carrelage 60x60 Blanc', 'Carrelage rectifié, finition brillante. Idéal pour salon et cuisine.', 2900.00, 'uploads/carrelage.jpg', 'carrelage', 150, '0555443322', 'contact@batissime.dz', 'https://facebook.com/batissime', 'Alger', '2026-05-14 16:56:53'),
(4, 2, 'Peinture Extérieure 20L', 'Peinture acrylique, résistante aux UV et aux intempéries.', 4800.00, 'uploads/peinture.jpg', 'peinture', 80, '0555443322', 'contact@batissime.dz', 'https://facebook.com/batissime', 'Alger', '2026-05-14 16:56:53'),
(5, 1, 'Plâtre en sac 40kg', 'Plâtre fin pour enduits et plafonds.', 850.00, 'uploads/platre.jpg', 'platre', 300, '0555123456', 'ventes@matpro.dz', 'https://facebook.com/matpro', 'Sétif', '2026-05-14 16:56:53');

-- --------------------------------------------------------

--
-- Structure de la table `professionnels`
--

CREATE TABLE `professionnels` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `specialite` varchar(100) NOT NULL,
  `experience` text DEFAULT NULL,
  `localisation` varchar(255) DEFAULT NULL,
  `disponible` enum('oui','non') DEFAULT 'oui',
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `professionnels`
--

INSERT INTO `professionnels` (`id`, `user_id`, `specialite`, `experience`, `localisation`, `disponible`, `photo`) VALUES
(1, 1, 'Électricien industriel', '12 ans d\'expérience dans les installations électriques industrielles et résidentielles. Certifié en domotique et énergies renouvelables.', 'Sétif, Algérie', 'oui', 'uploads/pro1-electricien.jpg'),
(2, 2, 'Maçon - Chef de chantier', '15 ans d\'expérience. Spécialisé dans les constructions neuves et rénovations complètes. Références disponibles sur demande.', 'Alger, Algerie', 'oui', 'uploads/pro2-macon.jpg'),
(3, 3, 'Plombier - Sanitaire', '8 ans d\'expérience. Installation et réparation de systèmes sanitaires et de chauffage. Intervention rapide.', 'Oran, Algérie', 'oui', 'uploads/pro3-plombier.jpg'),
(4, 6, 'DSHCDCHHCHC', 'CBHCCBE', 'BBA', 'oui', 'uploads/pro_6_1778782941.jpg');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `role` enum('professionnel','fournisseur') NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `statut` enum('actif','suspendu') DEFAULT 'actif',
  `date_inscription` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `nom`, `email`, `password`, `telephone`, `role`, `photo`, `statut`, `date_inscription`) VALUES
(1, 'Karim Boudiaf', 'karim@pro.dz', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0555123456', 'professionnel', NULL, 'actif', '2026-05-14 16:56:52'),
(2, 'Mohamed Said', 'said@pro.dz', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0555987654', 'professionnel', NULL, 'actif', '2026-05-14 16:56:52'),
(3, 'Ahmed Mansouri', 'ahmed@pro.dz', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0555778899', 'professionnel', NULL, 'actif', '2026-05-14 16:56:52'),
(4, 'Matériaux Pro', 'contact@matpro.dz', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0555112233', 'fournisseur', NULL, 'actif', '2026-05-14 16:56:52'),
(5, 'Bâtissime', 'info@batissime.dz', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0555443322', 'fournisseur', NULL, 'actif', '2026-05-14 16:56:52'),
(6, 'aya', 'aya@gmail.com', '$2y$10$ZqLbviIne7Cio.IbZJm46OYLyX8V9G2WXNCScWcnMtuzKFlqQNW/q', '07901666710', 'professionnel', NULL, 'actif', '2026-05-14 17:01:17'),
(7, 'zoubir', 'titzoubir@gmail.com', '$2y$10$5W2NAImiq8bV/10ud8zVcOXjxhqis5I7itOdJVils970.4hkrfYVa', '82284922309', 'fournisseur', NULL, 'actif', '2026-05-14 17:03:22');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `fournisseurs`
--
ALTER TABLE `fournisseurs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `produits`
--
ALTER TABLE `produits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fournisseur_id` (`fournisseur_id`);

--
-- Index pour la table `professionnels`
--
ALTER TABLE `professionnels`
  ADD PRIMARY KEY (`id`),
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
-- AUTO_INCREMENT pour la table `fournisseurs`
--
ALTER TABLE `fournisseurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `produits`
--
ALTER TABLE `produits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `professionnels`
--
ALTER TABLE `professionnels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `fournisseurs`
--
ALTER TABLE `fournisseurs`
  ADD CONSTRAINT `fournisseurs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `produits`
--
ALTER TABLE `produits`
  ADD CONSTRAINT `produits_ibfk_1` FOREIGN KEY (`fournisseur_id`) REFERENCES `fournisseurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `professionnels`
--
ALTER TABLE `professionnels`
  ADD CONSTRAINT `professionnels_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
