-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : dim. 19 jan. 2025 à 23:51
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
-- Base de données : `foot`
--

-- --------------------------------------------------------

--
-- Structure de la table `jouer`
--

CREATE TABLE `jouer` (
  `numero_de_licence` varchar(50) NOT NULL,
  `Id_match` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `joueur`
--

CREATE TABLE `joueur` (
  `numero_de_licence` varchar(50) NOT NULL,
  `nom` varchar(50) DEFAULT NULL,
  `prenom` varchar(50) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `taille` decimal(15,0) DEFAULT NULL,
  `poids` decimal(15,0) DEFAULT NULL,
  `evaluation` decimal(1,0) DEFAULT NULL,
  `statut` varchar(8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `joueur`
--

INSERT INTO `joueur` (`numero_de_licence`, `nom`, `prenom`, `date_naissance`, `taille`, `poids`, `evaluation`, `statut`) VALUES
('aaaaaeaeaea', 'Doe', 'john', '2025-01-18', 156, 64, 4, 'Inactif'),
('GJBHK', 'UH', 'GYIHUO', '2024-04-25', 87, 49, 5, 'Suspendu'),
('LIC00001', 'Doe', 'John', '4234-01-01', 185, 80, 4, 'Inactif'),
('UGI87', '86iyg', '876igUu', '2010-03-28', 200, 87, 2, 'Actif'),
('UGI872314', 'AZe', 'REz', '2025-01-02', 50, 250, 1, 'Actif'),
('Y57689UYGI', 'Ioij', 'AZERTY', '3342-06-29', 190, 69, 4, 'BlessÃ©');

-- --------------------------------------------------------

--
-- Structure de la table `match_foot`
--

CREATE TABLE `match_foot` (
  `Id_match` varchar(50) NOT NULL,
  `Date_match` date DEFAULT NULL,
  `Heure_match` time DEFAULT NULL,
  `Nom_equipe_adverse` varchar(50) DEFAULT NULL,
  `Domicile_externe` tinyint(1) DEFAULT NULL,
  `Resultat_match` enum('Victoire','Défaite','Nul','Non joué') DEFAULT 'Non joué'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `match_foot`
--

INSERT INTO `match_foot` (`Id_match`, `Date_match`, `Heure_match`, `Nom_equipe_adverse`, `Domicile_externe`, `Resultat_match`) VALUES
('MATCH00001', '2025-01-25', '23:40:00', 'Trop a Null ation', 0, 'Non joué'),
('MATCH00002', '2025-01-01', '02:38:00', 'Team WW', 0, 'Non joué');

-- Update existing matches with results
UPDATE `match_foot` SET `Resultat_match` = 'Victoire' WHERE `Id_match` = 'MATCH00001';
UPDATE `match_foot` SET `Resultat_match` = 'Défaite' WHERE `Id_match` = 'MATCH00002';

-- Add new matches with results
INSERT INTO `match_foot` 
(`Id_match`, `Date_match`, `Heure_match`, `Nom_equipe_adverse`, `Domicile_externe`, `Resultat_match`) VALUES
('MATCH00003', '2024-01-15', '15:00:00', 'FC Victory', 1, 'Victoire'),
('MATCH00004', '2024-01-22', '16:30:00', 'Real Winners', 0, 'Défaite'),
('MATCH00005', '2024-01-29', '14:00:00', 'Athletic Draw', 1, 'Nul');

-- --------------------------------------------------------

--
-- Structure de la table `note_personnelle`
--

CREATE TABLE `note_personnelle` (
  `Id_note` varchar(50) NOT NULL,
  `Commentaire` varchar(50) DEFAULT NULL,
  `numero_de_licence` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `note_personnelle`
--

INSERT INTO `note_personnelle` 
(`Id_note`, `Commentaire`, `numero_de_licence`) VALUES
('NOTE001', 'Excellent gardien', 'LIC00001'),
('NOTE002', 'Besoin amélioration défense', 'GJBHK'),
('NOTE003', 'Rapide mais manque endurance', 'UGI87'),
('NOTE004', 'Fort potentiel attaquant', 'UGI872314'),
('NOTE005', 'Polyvalent', 'Y57689UYGI');

-- --------------------------------------------------------

--
-- Structure de la table `participation_match`
--

CREATE TABLE `participation_match` (
  `Id_Participation_match` int(11) NOT NULL,
  `Titulaire` tinyint(1) DEFAULT NULL,
  `Poste` varchar(50) DEFAULT NULL,
  `Id_match` varchar(50) DEFAULT NULL,
  `numero_de_licence` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `participation_match`
--

INSERT INTO `participation_match` 
(`Titulaire`, `Poste`, `Id_match`, `numero_de_licence`) VALUES
(1, 'Gardien', 'MATCH00001', 'LIC00001'),
(1, 'Défenseur', 'MATCH00001', 'GJBHK'),
(1, 'Milieu', 'MATCH00001', 'UGI87'),
(0, 'Attaquant', 'MATCH00001', 'UGI872314'),
(1, 'Défenseur', 'MATCH00001', 'Y57689UYGI'),
(1, 'Gardien', 'MATCH00002', 'LIC00001'),
(0, 'Défenseur', 'MATCH00002', 'GJBHK'),
(1, 'Milieu', 'MATCH00002', 'UGI87'),
(1, 'Attaquant', 'MATCH00002', 'UGI872314'),
(1, 'Défenseur', 'MATCH00002', 'Y57689UYGI');

-- Add participations for new matches
INSERT INTO `participation_match` 
(`Titulaire`, `Poste`, `Id_match`, `numero_de_licence`) VALUES
-- Match 3 (Victory)
(1, 'Gardien', 'MATCH00003', 'LIC00001'),
(1, 'Défenseur', 'MATCH00003', 'GJBHK'),
(1, 'Milieu', 'MATCH00003', 'UGI87'),
(1, 'Attaquant', 'MATCH00003', 'UGI872314'),
-- Match 4 (Loss)
(1, 'Gardien', 'MATCH00004', 'LIC00001'),
(0, 'Défenseur', 'MATCH00004', 'Y57689UYGI'),
(1, 'Milieu', 'MATCH00004', 'UGI87'),
(1, 'Attaquant', 'MATCH00004', 'UGI872314'),
-- Match 5 (Draw)
(1, 'Gardien', 'MATCH00005', 'LIC00001'),
(1, 'Défenseur', 'MATCH00005', 'GJBHK'),
(0, 'Milieu', 'MATCH00005', 'Y57689UYGI'),
(1, 'Attaquant', 'MATCH00005', 'UGI872314');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`username`, `password`) VALUES
('test', '$2y$10$2LJkHZmZCgRWzo8fU2eQbOLUV4GgUTjrNTLJIQj8v/jM/4nvSmJ0e');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `jouer`
--
ALTER TABLE `jouer`
  ADD PRIMARY KEY (`numero_de_licence`,`Id_match`),
  ADD KEY `Id_match` (`Id_match`);

--
-- Index pour la table `joueur`
--
ALTER TABLE `joueur`
  ADD PRIMARY KEY (`numero_de_licence`);

--
-- Index pour la table `match_foot`
--
ALTER TABLE `match_foot`
  ADD PRIMARY KEY (`Id_match`);

--
-- Index pour la table `note_personnelle`
--
ALTER TABLE `note_personnelle`
  ADD PRIMARY KEY (`Id_note`),
  ADD KEY `numero_de_licence` (`numero_de_licence`);

--
-- Index pour la table `participation_match`
--
ALTER TABLE `participation_match`
  ADD PRIMARY KEY (`Id_Participation_match`),
  ADD KEY `Id_match` (`Id_match`),
  ADD KEY `numero_de_licence` (`numero_de_licence`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `participation_match`
--
ALTER TABLE `participation_match`
  MODIFY `Id_Participation_match` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `jouer`
--
ALTER TABLE `jouer`
  ADD CONSTRAINT `jouer_ibfk_1` FOREIGN KEY (`numero_de_licence`) REFERENCES `joueur` (`numero_de_licence`),
  ADD CONSTRAINT `jouer_ibfk_2` FOREIGN KEY (`Id_match`) REFERENCES `match_foot` (`Id_match`);

--
-- Contraintes pour la table `note_personnelle`
--
ALTER TABLE `note_personnelle`
  ADD CONSTRAINT `note_personnelle_ibfk_1` FOREIGN KEY (`numero_de_licence`) REFERENCES `joueur` (`numero_de_licence`);

--
-- Contraintes pour la table `participation_match`
--
ALTER TABLE `participation_match`
  ADD CONSTRAINT `participation_match_ibfk_1` FOREIGN KEY (`Id_match`) REFERENCES `match_foot` (`Id_match`),
  ADD CONSTRAINT `participation_match_ibfk_2` FOREIGN KEY (`numero_de_licence`) REFERENCES `joueur` (`numero_de_licence`);
COMMIT;

-- Drop unused jouer table
DROP TABLE IF EXISTS `jouer`;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
