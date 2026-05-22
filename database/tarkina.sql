-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 22, 2026 at 11:57 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tarkina`
--

-- --------------------------------------------------------

--
-- Table structure for table `artisanat`
--

CREATE TABLE `artisanat` (
  `id` int(10) UNSIGNED NOT NULL,
  `titre` varchar(255) NOT NULL,
  `localisation` varchar(255) NOT NULL,
  `prix` decimal(10,2) NOT NULL DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 1,
  `photo_principale` varchar(500) DEFAULT NULL,
  `photos_sec` text DEFAULT NULL,
  `statut` varchar(30) NOT NULL DEFAULT 'brouillon',
  `region_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `artisanat`
--

INSERT INTO `artisanat` (`id`, `titre`, `localisation`, `prix`, `description`, `stock`, `photo_principale`, `photos_sec`, `statut`, `region_id`, `created_at`, `updated_at`) VALUES
(1, 'Poterie de Nabeul — Série Sidi Bou Saïd', '', 35.00, '0', 20, 'https://images.unsplash.com/photo-1565193566173-7a0ee3dbe261?w=600', '', 'actif', 2, '2026-05-14 21:08:36', '2026-05-14 21:08:36'),
(2, 'Tapis berbère noué main — Kairouan', '', 280.00, '0', 5, 'https://images.unsplash.com/photo-1558769132-cb1aea458c5e?w=600', '', 'actif', 4, '2026-05-14 21:08:36', '2026-05-14 21:08:36'),
(3, 'Vannerie palmier — Tozeur', '', 25.00, '0', 30, 'https://images.unsplash.com/photo-1606760227091-3dd870d97f1d?w=600', '', 'actif', 5, '2026-05-14 21:08:36', '2026-05-14 21:08:36'),
(4, 'Bijoux berbères — Takrouna', '', 75.00, '0', 15, 'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=600', '', 'actif', 6, '2026-05-14 21:08:36', '2026-05-14 21:08:36'),
(9, 'La Clé du Ksar', 'Chenini', 40.00, 'bijoux berbères faits main, prix 40 DT, stock 30', 30, '0', NULL, 'actif', 10, '2026-05-22 20:53:34', '2026-05-22 20:53:34'),
(10, 'Tapis berbère de Chenini', 'Chenini', 180.00, 'tissés à la main motifs géométriques, prix 180 DT, stock 15', 15, '0', NULL, 'actif', 10, '2026-05-22 20:53:34', '2026-05-22 20:53:34');

-- --------------------------------------------------------

--
-- Table structure for table `avis`
--

CREATE TABLE `avis` (
  `id` int(11) NOT NULL,
  `hebergement_id` int(11) DEFAULT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `note` int(11) NOT NULL,
  `commentaire` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `repas_id` int(10) UNSIGNED DEFAULT NULL,
  `guide_id` int(10) UNSIGNED DEFAULT NULL,
  `evenement_id` int(10) UNSIGNED DEFAULT NULL,
  `artisanat_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

CREATE TABLE `blogs` (
  `id` int(10) UNSIGNED NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `region_id` int(11) DEFAULT NULL,
  `titre` varchar(200) NOT NULL,
  `contenu` text NOT NULL,
  `recommandation` text DEFAULT NULL,
  `photo` varchar(500) DEFAULT NULL,
  `likes` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blogs`
--

INSERT INTO `blogs` (`id`, `utilisateur_id`, `region_id`, `titre`, `contenu`, `recommandation`, `photo`, `likes`, `created_at`) VALUES
(1, 2, 5, 'Mon voyage à Tozeur', 'Un coucher de soleil magique sur les dunes de Tozeur. Une nuit inoubliable en campement berbère !', NULL, 'uploads/stories/https://images.unsplash.com/photo-1509316785289-025f5b846b35?w=600', 12, '2026-05-22 13:36:18'),
(2, 2, 2, 'Mon voyage à Sidi Bou Saïd', 'Les ruelles bleues et blanches de Sidi Bou Saïd, un vrai bijou. Le thé au café des Nattes est incontournable.', NULL, 'uploads/stories/https://images.unsplash.com/photo-1539635278303-d4002c07eae3?w=600', 8, '2026-05-22 13:36:18'),
(3, 1, 4, 'Mon voyage à Kairouan', 'La médina de Kairouan déborde d\'histoire. La Grande Mosquée est impressionnante.', NULL, 'uploads/stories/https://images.unsplash.com/photo-1548013146-72479768bada?w=600', 5, '2026-05-22 13:36:18'),
(4, 2, 4, 'Trois jours magiques à Kairouan', 'La médina de Kairouan m\'a transportée dans une autre époque. Les souks de tapis, la Grande Mosquée au lever du soleil, et l\'accueil chaleureux des habitants. Une expérience que je n\'oublierai jamais.', 'Visitez la Grande Mosquée tôt le matin pour éviter la foule, et goûtez absolument aux makroudh frais !', 'https://images.unsplash.com/photo-1561625116-5f8675632053?w=900&q=80', 7, '2026-05-22 15:13:31'),
(5, 2, 5, 'Une nuit sous les étoiles à Tozeur', 'Le désert de Tozeur au coucher du soleil est tout simplement irréel. Nous avons dormi en campement berbère, partagé un méchoui autour du feu et écouté les histoires des nomades.', 'Réservez une excursion 4x4 au lever du soleil sur les dunes, ça vaut vraiment le détour.', 'https://images.unsplash.com/photo-1509316785289-025f5b846b35?w=900&q=80', 27, '2026-05-22 15:13:31');

-- --------------------------------------------------------

--
-- Table structure for table `blog_comments`
--

CREATE TABLE `blog_comments` (
  `id` int(10) UNSIGNED NOT NULL,
  `blog_id` int(10) UNSIGNED NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `contenu` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blog_comments`
--

INSERT INTO `blog_comments` (`id`, `blog_id`, `utilisateur_id`, `contenu`, `created_at`) VALUES
(1, 5, 1, 'Superbe récit, ça donne vraiment envie d\'y aller !', '2026-05-22 15:13:31');

-- --------------------------------------------------------

--
-- Table structure for table `commandes`
--

CREATE TABLE `commandes` (
  `id` int(10) UNSIGNED NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `artisanat_id` int(10) UNSIGNED NOT NULL,
  `quantite` int(11) NOT NULL DEFAULT 1,
  `adresse_livraison` text NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `statut` varchar(30) NOT NULL DEFAULT 'en_attente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `evenement`
--

CREATE TABLE `evenement` (
  `id` int(10) UNSIGNED NOT NULL,
  `titre` varchar(255) NOT NULL,
  `localisation` varchar(255) NOT NULL,
  `prix` decimal(10,2) NOT NULL DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `capacite` int(11) NOT NULL DEFAULT 1,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `inclus` text DEFAULT NULL,
  `photo_principale` varchar(500) DEFAULT NULL,
  `photos_sec` text DEFAULT NULL,
  `statut` varchar(30) NOT NULL DEFAULT 'brouillon',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `region_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `evenement`
--

INSERT INTO `evenement` (`id`, `titre`, `localisation`, `prix`, `description`, `capacite`, `date_debut`, `date_fin`, `inclus`, `photo_principale`, `photos_sec`, `statut`, `created_at`, `updated_at`, `region_id`) VALUES
(1, 'Festival de Sidi Bou Saïd — Musique andalouse', '', 30.00, '0', 50, '2025-06-01', '2025-06-30', 'Entrée, Thé, Pâtisseries', 'https://images.unsplash.com/photo-1514320291840-2e0a9bf2a9ae?w=600', '', 'actif', '2026-05-14 21:08:36', '2026-05-14 21:08:36', 2),
(2, 'Nuit du henné — Djerba', '', 45.00, '0', 20, '2025-01-01', '2026-12-31', 'Henné, Dîner, Musique', 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?w=600', '', 'actif', '2026-05-14 21:08:36', '2026-05-14 21:08:36', 3),
(3, 'Atelier tapis — Kairouan', '', 55.00, '0', 6, '2025-01-01', '2026-12-31', 'Matériaux, Thé, Certificat', 'https://images.unsplash.com/photo-1452860606245-08befc0ff44b?w=600', '', 'actif', '2026-05-14 21:08:36', '2026-05-14 21:08:36', 4),
(4, 'Lever de soleil sur les dunes — Tozeur', '', 70.00, '0', 8, '2025-01-01', '2026-12-31', 'Transport, Petit-déjeuner, Guide', 'https://images.unsplash.com/photo-1509316785289-025f5b846b35?w=600', '', 'actif', '2026-05-14 21:08:36', '2026-05-14 21:08:36', 5),
(7, 'Festival des Ksour', 'Chenini', 15.00, 'célébration culture berbère, prix 15 DT, capacité 200, date_debut: 2027-03-10, date_fin: 2027-03-13', 200, '2027-03-10', '0000-00-00', 'Entrée, Programme culturel', 'images/evenements/festival_ksour.jpg', NULL, 'actif', '2026-05-22 20:53:34', '2026-05-22 20:53:34', 10);

-- --------------------------------------------------------

--
-- Table structure for table `favoris`
--

CREATE TABLE `favoris` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `hebergement_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `repas_id` int(10) UNSIGNED DEFAULT NULL,
  `guide_id` int(10) UNSIGNED DEFAULT NULL,
  `evenement_id` int(10) UNSIGNED DEFAULT NULL,
  `artisanat_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guide`
--

CREATE TABLE `guide` (
  `id` int(10) UNSIGNED NOT NULL,
  `titre` varchar(255) NOT NULL,
  `localisation` varchar(255) NOT NULL,
  `prix` decimal(10,2) NOT NULL DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `capacite` int(11) NOT NULL DEFAULT 1,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `inclus` text DEFAULT NULL,
  `photo_principale` varchar(500) DEFAULT NULL,
  `photos_sec` text DEFAULT NULL,
  `statut` varchar(30) NOT NULL DEFAULT 'brouillon',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `region_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `guide`
--

INSERT INTO `guide` (`id`, `titre`, `localisation`, `prix`, `description`, `capacite`, `date_debut`, `date_fin`, `inclus`, `photo_principale`, `photos_sec`, `statut`, `created_at`, `updated_at`, `region_id`) VALUES
(1, 'Visite guidée — Sidi Bou Saïd', '', 60.00, '0', 8, '2025-01-01', '2026-12-31', 'Guide, Thé au café des Nattes', 'https://images.unsplash.com/photo-1539635278303-d4002c07eae3?w=600', '', 'actif', '2026-05-14 21:08:36', '2026-05-14 21:08:36', 2),
(2, 'Tour de l\'île — Djerba', '', 80.00, '0', 10, '2025-01-01', '2026-12-31', 'Guide, Transport, Déjeuner', 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=600', '', 'actif', '2026-05-14 21:08:36', '2026-05-14 21:08:36', 3),
(3, 'Médina de Kairouan — Visite historique', '', 50.00, '0', 12, '2025-01-01', '2026-12-31', 'Guide certifié, Entrées mosquée', 'https://images.unsplash.com/photo-1488085061387-422e29b40080?w=600', '', 'actif', '2026-05-14 21:08:36', '2026-05-14 21:08:36', 4),
(4, 'Excursion Sahara — Tozeur', '', 120.00, '0', 6, '2025-01-01', '2026-12-31', 'Guide, Dromadaire, Transport 4x4', 'https://images.unsplash.com/photo-1509316785289-025f5b846b35?w=600', '', 'actif', '2026-05-14 21:08:36', '2026-05-14 21:08:36', 5),
(5, 'Randonnée Takrouna', '', 40.00, '0', 8, '2025-01-01', '2026-12-31', 'Guide local, Eau, Collation', 'https://images.unsplash.com/photo-1551632811-561732d1e306?w=600', '', 'actif', '2026-05-14 21:08:36', '2026-05-14 21:08:36', 6),
(8, 'Adel', 'Chenini', 80.00, 'Guide local natif de Chenini, spécialiste histoire berbère et ksour, prix 80 DT/jour, capacité 10', 10, '2025-01-01', '0000-00-00', 'Guide certifié, Eau', 'images/guides/adel_chenini.jpg', NULL, 'actif', '2026-05-22 20:53:34', '2026-05-22 20:53:34', 10);

-- --------------------------------------------------------

--
-- Table structure for table `hebergement`
--

CREATE TABLE `hebergement` (
  `id` int(10) UNSIGNED NOT NULL,
  `titre` varchar(255) NOT NULL,
  `localisation` varchar(255) NOT NULL,
  `prix` decimal(10,2) NOT NULL DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `statut` varchar(30) NOT NULL DEFAULT 'brouillon',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `capacite` int(11) NOT NULL DEFAULT 1,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `inclus` text DEFAULT NULL,
  `photo_principale` varchar(500) DEFAULT NULL,
  `photos_sec` text DEFAULT NULL,
  `region_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hebergement`
--

INSERT INTO `hebergement` (`id`, `titre`, `localisation`, `prix`, `description`, `statut`, `created_at`, `updated_at`, `capacite`, `date_debut`, `date_fin`, `inclus`, `photo_principale`, `photos_sec`, `region_id`) VALUES
(1, 'Dar Sidi — Maison d\'hôtes traditionnelle', '', 180.00, '0', 'actif', '2026-05-14 21:08:36', '2026-05-14 21:08:36', 4, '2025-01-01', '2026-12-31', 'Petit-déjeuner, Wi-Fi, Parking', 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=600', '', 2),
(2, 'Riad Djerba — Vue mer', '', 220.00, '0', 'actif', '2026-05-14 21:08:36', '2026-05-14 21:08:36', 6, '2025-01-01', '2026-12-31', 'Piscine, Petit-déjeuner, Wi-Fi', 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=600', '', 3),
(3, 'Gîte Kairouan — Médina', '', 120.00, '0', 'actif', '2026-05-14 21:08:36', '2026-05-14 21:08:36', 3, '2025-01-01', '2026-12-31', 'Wi-Fi, Thé d\'accueil', 'https://images.unsplash.com/photo-1548013146-72479768bada?w=600', '', 4),
(4, 'Campement Tozeur — Nuit sous les étoiles', '', 95.00, '0', 'actif', '2026-05-14 21:08:36', '2026-05-14 21:08:36', 8, '2025-01-01', '2026-12-31', 'Dîner, Petit-déjeuner, Draps', 'https://images.unsplash.com/photo-1537225228614-56cc3556d7ed?w=600', '', 5),
(5, 'Maison Takrouna — Vue panoramique', '', 110.00, '0', 'actif', '2026-05-14 21:08:36', '2026-05-14 21:08:36', 4, '2025-01-01', '2026-12-31', 'Petit-déjeuner berbère, Wi-Fi', 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=600', '', 6),
(6, 'Dar Kessra — Montagne', '', 85.00, '0', 'actif', '2026-05-14 21:08:36', '2026-05-14 21:08:36', 5, '2025-01-01', '2026-12-31', 'Petit-déjeuner, Produits locaux', 'https://images.unsplash.com/photo-1445019980597-93fa8acb246c?w=600', '', 7),
(11, 'Résidence Kenza', 'Chenini', 120.00, 'gîte troglodytique 8 grottes, capacité 16, prix 120 DT', 'actif', '2026-05-22 20:53:34', '2026-05-22 20:53:34', 16, '2025-01-01', '0000-00-00', 'Petit-déjeuner berbère', 'images/hebergements/kenza_chenini.jpg', NULL, 10),
(12, 'Azul Chenini', 'Chenini', 80.00, 'maison d\'hôtes authentique, capacité 8, prix 80 DT', 'actif', '2026-05-22 20:53:34', '2026-05-22 20:53:34', 8, '2025-01-01', '0000-00-00', 'Petit-déjeuner, Thé d\'accueil', 'images/hebergements/azul_chenini.jpg', NULL, 10);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `otp` varchar(6) DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `otp`, `expires_at`, `created_at`) VALUES
(20, 'nouhalahmar644@gmail.com', '318781', '2026-04-28 16:11:15', '2026-04-28 14:01:15');

-- --------------------------------------------------------

--
-- Table structure for table `region`
--

CREATE TABLE `region` (
  `id` int(10) UNSIGNED NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `photo_principale` varchar(500) DEFAULT NULL,
  `latitude` decimal(10,6) DEFAULT NULL,
  `longitude` decimal(10,6) DEFAULT NULL,
  `photos_sec` text DEFAULT NULL,
  `meilleure_saison` varchar(100) DEFAULT NULL,
  `langues` varchar(255) DEFAULT NULL,
  `monnaie` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `region`
--

INSERT INTO `region` (`id`, `nom`, `description`, `photo_principale`, `latitude`, `longitude`, `photos_sec`, `meilleure_saison`, `langues`, `monnaie`, `created_at`, `photo`) VALUES
(2, 'Sidi Bou Sa´d', 'Village perch? sur une falaise dominant la mer, c?l?bre pour ses maisons bleu et blanc.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-12 00:15:32', 'assets/regions/sidi-bou-said.jpg'),
(3, 'Djerba', 'Surnomm?e l\'?le des r?ves, habit?e depuis l\'Antiquit?. Sa synagogue El Ghriba est l\'une des plus anciennes du monde.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-12 00:15:32', 'assets/regions/djerba.jpg'),
(4, 'Kairouan', 'Fond?e en 670 ap. J.-C., Kairouan est la quatri?me ville sainte de l\'Islam. Son m?dina est class?e UNESCO.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-12 00:15:32', 'assets/regions/kairouan.jpg'),
(5, 'Tozeur', 'Porte du Sahara tunisien, connue pour ses palmeraies et son architecture en brique de terre cuite. Star Wars y fut tourn?.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-12 00:15:32', 'assets/regions/tozeur.jpg'),
(6, 'Takrouna', 'Village berb?re perch? sur un piton rocheux surplombant la plaine. L\'un des derniers villages berb?res de Tunisie.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-12 00:15:32', 'assets/regions/takrouna.jpg'),
(7, 'Kessra', 'Village de montagne nich? dans le Djebel Kesra. Ses ruelles en pierre et maisons traditionnelles en font un refuge authentique.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-12 00:15:32', 'assets/regions/kessra.jpg'),
(10, 'Chenini', 'village troglodytique berbère à 18km de Tataouine, inspiré le nom de la planète Tatooine dans Star Wars.', NULL, 32.911700, 10.261900, NULL, 'Mars — Mai / Septembre — Novembre', NULL, NULL, '2026-05-22 20:53:34', 'images/regions/chenini.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `repas`
--

CREATE TABLE `repas` (
  `id` int(10) UNSIGNED NOT NULL,
  `titre` varchar(255) NOT NULL,
  `localisation` varchar(255) NOT NULL,
  `prix` decimal(10,2) NOT NULL DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `capacite` int(11) NOT NULL DEFAULT 1,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `inclus` text DEFAULT NULL,
  `photo_principale` varchar(500) DEFAULT NULL,
  `photos_sec` text DEFAULT NULL,
  `statut` varchar(30) NOT NULL DEFAULT 'brouillon',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `region_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `repas`
--

INSERT INTO `repas` (`id`, `titre`, `localisation`, `prix`, `description`, `capacite`, `date_debut`, `date_fin`, `inclus`, `photo_principale`, `photos_sec`, `statut`, `created_at`, `updated_at`, `region_id`) VALUES
(1, 'Déjeuner chez Fatma — Cuisine du terroir', '', 45.00, '0', 6, '2025-01-01', '2026-12-31', 'Entrée, Plat, Dessert, Thé', 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=600', '', 'actif', '2026-05-14 21:08:36', '2026-05-14 21:08:36', 2),
(2, 'Table djerbienne — Fruits de mer', '', 65.00, '0', 8, '2025-01-01', '2026-12-31', 'Poisson du jour, Salade, Dessert', 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=600', '', 'actif', '2026-05-14 21:08:36', '2026-05-14 21:08:36', 3),
(3, 'Dîner médiéval — Kairouan', '', 40.00, '0', 10, '2025-01-01', '2026-12-31', 'Plat complet, Thé, Pâtisseries', 'https://images.unsplash.com/photo-1467003909585-2f8a72700288?w=600', '', 'actif', '2026-05-14 21:08:36', '2026-05-14 21:08:36', 4),
(4, 'Méchoui saharien — Tozeur', '', 55.00, '0', 12, '2025-01-01', '2026-12-31', 'Méchoui, Salade, Dattes, Thé', 'https://images.unsplash.com/photo-1529543544282-ea669407fca3?w=600', '', 'actif', '2026-05-14 21:08:36', '2026-05-14 21:08:36', 5),
(11, 'Koucha berbère', 'Chenini', 35.00, 'Famille Missaoui, agneau mijoté aux épices berbères, prix 35 DT, capacité 8', 8, '2025-01-01', '0000-00-00', 'Plat principal, Pain maison, Thé', 'images/repas/koucha_chenini.jpg', NULL, 'actif', '2026-05-22 20:53:34', '2026-05-22 20:53:34', 10),
(12, 'Couscous berbère', 'Chenini', 25.00, 'Famille Ben Salem, couscous au poulet fermier, prix 25 DT, capacité 10', 10, '2025-01-01', '0000-00-00', 'Entrée, Couscous, Dessert', 'images/repas/couscous_chenini.jpg', NULL, 'actif', '2026-05-22 20:53:34', '2026-05-22 20:53:34', 10),
(13, 'Gargoulette Meslane', 'Chenini', 30.00, 'Résidence Kenza, plat cuit en poterie terre cuite, prix 30 DT, capacité 12', 12, '2025-01-01', '0000-00-00', 'Plat complet, Salade, Thé', 'images/repas/gargoulette_chenini.jpg', NULL, 'actif', '2026-05-22 20:53:34', '2026-05-22 20:53:34', 10);

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type_service` varchar(50) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `nb_voyageurs` int(11) DEFAULT 1,
  `prix_total` decimal(10,2) DEFAULT NULL,
  `nom` varchar(150) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `statut` varchar(30) DEFAULT 'en_attente',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `user_id`, `type_service`, `service_id`, `date_debut`, `date_fin`, `nb_voyageurs`, `prix_total`, `nom`, `email`, `message`, `statut`, `created_at`) VALUES
(1, 2, 'hebergement', 4, '2026-05-15', '0000-00-00', 2, 190.00, '0', 'nouhalahmar644@gmail.com', 'bbbbbbbbbbb', 'en_attente', '2026-05-15 22:01:49');

-- --------------------------------------------------------

--
-- Table structure for table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `date_inscription` timestamp NOT NULL DEFAULT current_timestamp(),
  `telephone` varchar(20) DEFAULT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `photo_profil` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `nom`, `prenom`, `email`, `mot_de_passe`, `adresse`, `role`, `date_inscription`, `telephone`, `ville`, `bio`, `photo_profil`) VALUES
(1, 'abir', 'azertyu', 'nouhalahmar@gmail.com', '$2y$10$ZmOVnWInUFgPuzw5aHcWsO2giDZ/hNqLoD9n4VMCfakBxGjHK/qIK', '', 'utilisateur', '2026-04-18 23:46:41', NULL, NULL, NULL, NULL),
(2, 'admin', 'lahmar', 'admin@gmail.com', '$2y$10$w9ovSeOY1fVKy9cH19qeb..rHHsQ0c.SJ0Yzm.mBtSlZlljYaQfqi', 'hammamet', 'admin', '2026-04-18 23:52:02', '25398766', 'TUNIS', 'fffffffff', 'uploads/profils/profil_2_1779058557.png'),
(5, 'nouhaj', 'lahmar', 'nouhalahmar644@gmail.com', '$2y$10$470i.jiKCMU99ahQuzDYi.LZ3xFDWMtdE4H4cS8ZcE9B3giibgKmi', 'hammamet', 'utilisateur', '2026-04-25 13:24:56', NULL, NULL, NULL, NULL),
(6, 'abir', 'bnkhlifa', 'benkhlifaabiir@gmail.com', '$2y$10$bsJ2K3Rfz8Zln//WfsT/s.ozMHmoTNq2mTYTk.3EXsdYZ.gStUYKq', 'hammamet', 'utilisateur', '2026-04-25 14:04:58', NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `artisanat`
--
ALTER TABLE `artisanat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `avis`
--
ALTER TABLE `avis`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog_comments`
--
ALTER TABLE `blog_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `blog_id` (`blog_id`);

--
-- Indexes for table `commandes`
--
ALTER TABLE `commandes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `evenement`
--
ALTER TABLE `evenement`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `favoris`
--
ALTER TABLE `favoris`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guide`
--
ALTER TABLE `guide`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hebergement`
--
ALTER TABLE `hebergement`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `region`
--
ALTER TABLE `region`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `repas`
--
ALTER TABLE `repas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `artisanat`
--
ALTER TABLE `artisanat`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `avis`
--
ALTER TABLE `avis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `blog_comments`
--
ALTER TABLE `blog_comments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `commandes`
--
ALTER TABLE `commandes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `evenement`
--
ALTER TABLE `evenement`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `favoris`
--
ALTER TABLE `favoris`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guide`
--
ALTER TABLE `guide`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `hebergement`
--
ALTER TABLE `hebergement`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `region`
--
ALTER TABLE `region`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `repas`
--
ALTER TABLE `repas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
