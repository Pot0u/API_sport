-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 19 mars 2025 à 08:19
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
-- Base de données : `r401`
--

-- --------------------------------------------------------

--
-- Structure de la table `chuckn_facts`
--

CREATE TABLE `chuckn_facts` (
  `id` int(11) NOT NULL,
  `phrase` text NOT NULL,
  `vote` int(11) DEFAULT NULL,
  `date_ajout` datetime DEFAULT NULL,
  `date_modif` datetime DEFAULT NULL,
  `faute` tinyint(1) DEFAULT NULL,
  `signalement` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `chuckn_facts`
--

INSERT INTO `chuckn_facts` (`id`, `phrase`, `vote`, `date_ajout`, `date_modif`, `faute`, `signalement`) VALUES
(1, 'Chuck Norris peut faire démarrer un Airbus A380 avec une épingle à nourrice.', NULL, '2023-12-22 10:47:03', NULL, NULL, NULL),
(2, 'Quand Chuck Norris fabrique un avion en papier, Airbus achète.', NULL, '2023-12-22 10:47:04', NULL, NULL, NULL),
(3, 'Quand Chuck Norris va sur Google, c\'est Google qui lui demande des renseignements.', NULL, '2023-12-22 10:47:05', NULL, NULL, NULL),
(4, 'Chuck Norris peut dire \"C\'est pas faux\" et comprendre quand même.', NULL, '2023-12-22 10:47:06', NULL, NULL, NULL),
(5, 'Hulk a voulu défier Chuck Norris en duel, maintenant on l\'appelle Shrek.', NULL, '2023-12-22 10:47:07', NULL, NULL, NULL),
(7, 'Chuck Norris peut faire du RAID avec un seul disque dur.', NULL, '2023-12-22 10:47:09', NULL, NULL, NULL),
(8, 'Chuck Norris peut faire de l\'algorithmique en HTML', NULL, '2023-12-22 10:47:10', NULL, NULL, NULL),
(9, 'La mémé de Chuck Norris n\'a pas peur des orties.', NULL, '2023-12-22 10:47:11', NULL, NULL, NULL),
(10, 'Quand Graham Bel a inventé le téléphone, il avait 3 appels en absence de Chuck Norris.', NULL, '2023-12-22 10:47:12', NULL, NULL, NULL),
(11, 'Bill Gates s\'est inspiré de Chuck Norris quand il a inventé le mode sans échec.', NULL, '2023-12-22 10:47:13', NULL, NULL, NULL),
(12, 'Il n\'y a pas de réchauffement climatique, c\'est Chuck Norris qui a lâché une caisse.', NULL, '2023-12-22 10:47:14', NULL, NULL, NULL),
(13, 'Chuck Norris peut faire croiser 3 droites parallèles.', NULL, '2023-12-22 10:47:15', NULL, NULL, NULL),
(14, 'Chuck Norris n\'a pas besoin d\'un navigateur pour aller sur le web, un bateau suffit.', NULL, '2023-12-22 10:47:16', NULL, NULL, NULL),
(15, 'Quand Chuck Norris regarde froidement un cobra, le cobra caille.', NULL, '2023-12-22 10:47:17', NULL, NULL, NULL),
(16, 'Chuck Norris a déjà cassé un Nokia.', NULL, '2023-12-22 10:47:18', NULL, NULL, NULL),
(17, 'Chuck Norris peut parler binaire.', NULL, '2023-12-22 10:47:19', NULL, NULL, NULL),
(18, 'Chuck Norris a inventé le binaire le jour où il a affronté l\'armée Mongole, il a transformé les Huns en Zéro.', NULL, '2023-12-22 10:47:20', NULL, NULL, NULL),
(19, 'L\'ordinateur de Chuck Norris fonctionne sans système d\'exploitation, personne n\'exploite l\'ordinateur de Chuck Norris.', NULL, '2023-12-22 10:47:21', NULL, NULL, NULL),
(20, 'Ctrl + Alt + Suppr est un nom de code pour appeler Chuck Norris sur Windows.', NULL, '2023-12-22 10:47:22', NULL, NULL, NULL),
(21, 'Chuck Norris a lu la base de registre Windows en entier.... deux fois.', NULL, '2023-12-22 10:47:23', NULL, NULL, NULL),
(22, 'Si Windows existe encore c\'est parce que Chuck Norris ne s\'est jamais intéressé à l\'informatique.', NULL, '2023-12-22 10:47:24', NULL, NULL, NULL),
(23, 'Tout le monde sait que la réponse est 42, Chuck Norris connait la question.', NULL, '2023-12-22 10:47:25', NULL, NULL, NULL),
(24, 'Chuck Norris a déjà fait loucher un cyclope.', NULL, '2023-12-22 10:47:26', NULL, NULL, NULL),
(25, 'Sur Internet, Chuck Norris n\'attrape pas de cookies, il les mange.', NULL, '2023-12-22 10:47:27', NULL, NULL, NULL),
(26, 'Chuck Norris comprend Brad Pitt dans Snatch.', NULL, '2023-12-22 10:47:28', NULL, NULL, NULL),
(27, 'Quand Chuck Norris envoie un sms, il ne reçoit pas d\'accusé, on n\'accuse pas Chuck Norris.', NULL, '2023-12-22 10:47:29', NULL, NULL, NULL),
(28, 'Chuck Norris a inventé le code barre en marchant sur un zèbre.', NULL, '2023-12-22 10:47:30', NULL, NULL, NULL),
(29, 'Chuck Norris a déjà terminé World Of Warcraft.', NULL, '2023-12-22 10:47:31', NULL, NULL, NULL),
(30, 'Certains disent : \"La violence ne résoud rien\", Chuck Norris leur répond : \"C\'est que t\'as pas tapé assez fort\"', NULL, '2023-12-22 10:47:32', NULL, NULL, NULL),
(31, 'Chuck Norris ne reçoit pas de spam.', NULL, '2023-12-22 10:47:33', NULL, NULL, NULL),
(32, 'Si tu as une femme, c\'est que Chuck Norris n\'en a pas voulu.', NULL, '2023-12-22 10:47:34', NULL, NULL, NULL),
(33, 'Chuck Norris a déjà fini Half-Life sans se servir du pied-de-biche.', NULL, '2023-12-22 10:47:35', NULL, NULL, NULL),
(34, 'Quand Chuck Norris utilise Internet Explorer, les geeks ne se moquent pas.', NULL, '2023-12-22 10:47:36', NULL, NULL, NULL),
(35, 'Il n\'y a pas de touche \"Contrôle\" sur l\'ordinateur de Chuck Norris, Chuck Norris a toujours le contrôle.', NULL, '2023-12-22 10:47:37', NULL, NULL, NULL),
(36, 'Chuck Norris te bat à Counter-Strike sur un Windows 95 avec du 56k, l\'ordinateur n\'ose pas laguer.', NULL, '2023-12-22 10:47:38', NULL, NULL, NULL),
(37, 'K2000 était la voiture téléguidée de Chuck Norris quand il était petit.', NULL, '2023-12-22 10:47:39', NULL, NULL, NULL),
(38, 'Si Anakin Skywalker avait connu Chuck Norris, il n\'aurait pas choisi le côté obscur de la Force, il aurait pris une bonne claque dans sa gueule et puis c\'est tout.', NULL, '2023-12-22 10:47:40', NULL, NULL, NULL),
(39, 'Chuck Norris a viré la touche \"F1\" de son clavier, Chuck Norris n\'a pas besoin d\'aide.', NULL, '2023-12-22 10:47:41', NULL, NULL, NULL),
(40, 'Chuck Norris a fini la campagne d\'Age of Empire avec un villageois.', NULL, '2023-12-22 10:47:42', NULL, NULL, NULL),
(41, 'Chuck Norris peut endormir Derrick.', NULL, '2023-12-22 10:47:43', NULL, NULL, NULL),
(42, 'Bruce Lee a peut-être battu Chuck Norris mais il n\'y aura pas de Bruce Lee Facts.', NULL, '2023-12-22 10:47:44', NULL, NULL, NULL),
(43, 'Si Chuck Norris avait été dans Benny Hill, Benny aurai couru moins longtemps.', NULL, '2023-12-22 10:47:45', NULL, NULL, NULL),
(44, 'Chuck Norris peut faire tenir un Divx sur une disquette.', NULL, '2023-12-22 10:47:46', NULL, NULL, NULL),
(45, 'test', NULL, NULL, NULL, NULL, NULL),
(46, 'yghyhuy', NULL, NULL, NULL, NULL, NULL),
(47, 'phrrraasseseesse', NULL, NULL, NULL, NULL, NULL),
(48, 'phrasssssssssssssssse', NULL, '2025-02-13 10:25:12', NULL, NULL, NULL),
(49, 'phrasssssssssssssssse', NULL, '2025-02-13 10:26:32', NULL, NULL, NULL),
(50, 'phrasssssssssssssssse', NULL, '2025-02-13 10:32:58', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `login` varchar(45) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `login`, `password`, `role`) VALUES
(1, 'bob', '$2y$10$PsJZvmmnvbGwIYDBeEGtC.Z1Bkulg1W1eyhOtPsjPSL4DJ72D97xy', 'user'),
(2, 'prof', '$2y$10$cefdlQN3gWXEo7AseLD4q.GoF3LH73CaD.b5nhmeIGf8aYNMJ8Mje', 'admin');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `chuckn_facts`
--
ALTER TABLE `chuckn_facts`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `chuckn_facts`
--
ALTER TABLE `chuckn_facts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
