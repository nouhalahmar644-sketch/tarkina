SET FOREIGN_KEY_CHECKS=0;
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: tourisme
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `tourisme`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `tourisme` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `tourisme`;

--
-- Table structure for table `artisanat`
--

DROP TABLE IF EXISTS `artisanat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `artisanat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `localisation` varchar(255) NOT NULL DEFAULT '',
  `prix` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `photo_principale` varchar(500) DEFAULT NULL,
  `photos_sec` text DEFAULT NULL,
  `statut` varchar(30) NOT NULL DEFAULT 'brouillon',
  `region_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_art_region` (`region_id`),
  CONSTRAINT `fk_art_region` FOREIGN KEY (`region_id`) REFERENCES `region` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `artisanat`
--

LOCK TABLES `artisanat` WRITE;
/*!40000 ALTER TABLE `artisanat` DISABLE KEYS */;
INSERT INTO `artisanat` VALUES (1,'Poterie de Nabeul — Série Sidi Bou Saïd','Sidi Bou Saïd',35.00,20,'Assiettes et bols peints à la main aux motifs bleus de Sidi Bou Saïd. Pièces uniques.','https://images.unsplash.com/photo-1565193566173-7a0ee3dbe261?w=600','','actif',2,'2026-05-22 12:36:18','2026-05-22 12:36:18'),(2,'Tapis berbère noué main — Kairouan','Kairouan',280.00,5,'Tapis authentique noué à la main par des artisanes de Kairouan. Laine naturelle, motifs géométriques.','https://images.unsplash.com/photo-1558769132-cb1aea458c5e?w=600','','actif',4,'2026-05-22 12:36:18','2026-05-22 12:36:18'),(3,'Vannerie palmier — Tozeur','Tozeur',25.00,30,'Paniers et objets décoratifs tressés à partir de feuilles de palmier par des artisans locaux.','https://images.unsplash.com/photo-1606760227091-3dd870d97f1d?w=600','','actif',5,'2026-05-22 12:36:18','2026-05-22 12:36:18'),(4,'Bijoux berbères — Takrouna','Takrouna',75.00,15,'Colliers, bracelets et bagues en argent ornés de pierres locales. Savoir-faire berbère ancestral.','https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=600','','actif',6,'2026-05-22 12:36:18','2026-05-22 12:36:18');
/*!40000 ALTER TABLE `artisanat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog_comments`
--

DROP TABLE IF EXISTS `blog_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blog_comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `blog_id` int(10) unsigned NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `contenu` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `blog_id` (`blog_id`),
  CONSTRAINT `blog_comments_ibfk_1` FOREIGN KEY (`blog_id`) REFERENCES `blogs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_comments`
--

LOCK TABLES `blog_comments` WRITE;
/*!40000 ALTER TABLE `blog_comments` DISABLE KEYS */;
INSERT INTO `blog_comments` VALUES (1,5,1,'Superbe récit, ça donne vraiment envie d\'y aller !','2026-05-22 15:13:31');
/*!40000 ALTER TABLE `blog_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blogs`
--

DROP TABLE IF EXISTS `blogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blogs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int(11) NOT NULL,
  `region_id` int(11) DEFAULT NULL,
  `titre` varchar(200) NOT NULL,
  `contenu` text NOT NULL,
  `recommandation` text DEFAULT NULL,
  `photo` varchar(500) DEFAULT NULL,
  `likes` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blogs`
--

LOCK TABLES `blogs` WRITE;
/*!40000 ALTER TABLE `blogs` DISABLE KEYS */;
INSERT INTO `blogs` VALUES (1,2,5,'Mon voyage à Tozeur','Un coucher de soleil magique sur les dunes de Tozeur. Une nuit inoubliable en campement berbère !',NULL,'uploads/stories/https://images.unsplash.com/photo-1509316785289-025f5b846b35?w=600',12,'2026-05-22 13:36:18'),(2,2,2,'Mon voyage à Sidi Bou Saïd','Les ruelles bleues et blanches de Sidi Bou Saïd, un vrai bijou. Le thé au café des Nattes est incontournable.',NULL,'uploads/stories/https://images.unsplash.com/photo-1539635278303-d4002c07eae3?w=600',8,'2026-05-22 13:36:18'),(3,1,4,'Mon voyage à Kairouan','La médina de Kairouan déborde d\'histoire. La Grande Mosquée est impressionnante.',NULL,'uploads/stories/https://images.unsplash.com/photo-1548013146-72479768bada?w=600',5,'2026-05-22 13:36:18'),(4,2,4,'Trois jours magiques à Kairouan','La médina de Kairouan m\'a transportée dans une autre époque. Les souks de tapis, la Grande Mosquée au lever du soleil, et l\'accueil chaleureux des habitants. Une expérience que je n\'oublierai jamais.','Visitez la Grande Mosquée tôt le matin pour éviter la foule, et goûtez absolument aux makroudh frais !','https://images.unsplash.com/photo-1561625116-5f8675632053?w=900&q=80',7,'2026-05-22 15:13:31'),(5,2,5,'Une nuit sous les étoiles à Tozeur','Le désert de Tozeur au coucher du soleil est tout simplement irréel. Nous avons dormi en campement berbère, partagé un méchoui autour du feu et écouté les histoires des nomades.','Réservez une excursion 4x4 au lever du soleil sur les dunes, ça vaut vraiment le détour.','https://images.unsplash.com/photo-1509316785289-025f5b846b35?w=900&q=80',27,'2026-05-22 15:13:31');
/*!40000 ALTER TABLE `blogs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commandes`
--

DROP TABLE IF EXISTS `commandes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commandes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int(10) unsigned DEFAULT NULL,
  `artisanat_id` int(10) unsigned DEFAULT NULL,
  `quantite` int(11) NOT NULL DEFAULT 1,
  `adresse_livraison` text DEFAULT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `statut` varchar(30) NOT NULL DEFAULT 'en_attente',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_cmd_user` (`utilisateur_id`),
  KEY `idx_cmd_art` (`artisanat_id`),
  CONSTRAINT `fk_cmd_art` FOREIGN KEY (`artisanat_id`) REFERENCES `artisanat` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_cmd_user` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commandes`
--

LOCK TABLES `commandes` WRITE;
/*!40000 ALTER TABLE `commandes` DISABLE KEYS */;
INSERT INTO `commandes` VALUES (1,2,1,2,'Rue de la Liberté, Sfax, Tunisie',70.00,'en_attente','2026-05-22 13:36:18');
/*!40000 ALTER TABLE `commandes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `evenement`
--

DROP TABLE IF EXISTS `evenement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `evenement` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `localisation` varchar(255) NOT NULL DEFAULT '',
  `description` text DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `region_id` int(10) unsigned DEFAULT NULL,
  `prix` decimal(10,2) NOT NULL DEFAULT 0.00,
  `capacite` int(11) NOT NULL DEFAULT 1,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `inclus` text DEFAULT NULL,
  `photo_principale` varchar(500) DEFAULT NULL,
  `photos_sec` text DEFAULT NULL,
  `statut` varchar(30) NOT NULL DEFAULT 'brouillon',
  `utilisateur_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_eve_region` (`region_id`),
  CONSTRAINT `fk_eve_region` FOREIGN KEY (`region_id`) REFERENCES `region` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `evenement`
--

LOCK TABLES `evenement` WRITE;
/*!40000 ALTER TABLE `evenement` DISABLE KEYS */;
INSERT INTO `evenement` VALUES (1,'Festival de Sidi Bou Saïd — Musique andalouse','Sidi Bou Saïd','Soirée musicale dans les jardins d\'une demeure historique. Malouf et musique arabo-andalouse.',NULL,2,30.00,50,'2025-06-01','2025-06-30','Entrée, Thé, Pâtisseries','https://images.unsplash.com/photo-1514320291840-2e0a9bf2a9ae?w=600','','actif',NULL,'2026-05-22 12:36:18','2026-05-22 12:36:18'),(2,'Nuit du henné — Djerba','Djerba','Soirée traditionnelle : application de henné, musique live, danse et dîner djerbien.',NULL,3,45.00,20,'2025-01-01','2026-12-31','Henné, Dîner, Musique','https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?w=600','','actif',NULL,'2026-05-22 12:36:18','2026-05-22 12:36:18'),(3,'Atelier tapis — Kairouan','Kairouan','Apprenez les techniques de tissage ancestrales avec une maître-artisane. Repartez avec votre création.',NULL,4,55.00,6,'2025-01-01','2026-12-31','Matériaux, Thé, Certificat','https://images.unsplash.com/photo-1452860606245-08befc0ff44b?w=600','','actif',NULL,'2026-05-22 12:36:18','2026-05-22 12:36:18'),(4,'Lever de soleil sur les dunes — Tozeur','Tozeur','Réveil à 4h, 4x4 jusqu\'aux grandes dunes, lever de soleil sur le Sahara, petit-déjeuner bédouin.',NULL,5,70.00,8,'2025-01-01','2026-12-31','Transport, Petit-déjeuner, Guide','https://images.unsplash.com/photo-1509316785289-025f5b846b35?w=600','','actif',NULL,'2026-05-22 12:36:18','2026-05-22 12:36:18');
/*!40000 ALTER TABLE `evenement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `favoris`
--

DROP TABLE IF EXISTS `favoris`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `favoris` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `type_service` varchar(50) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_fav_user` (`user_id`),
  CONSTRAINT `fk_fav_user` FOREIGN KEY (`user_id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `favoris`
--

LOCK TABLES `favoris` WRITE;
/*!40000 ALTER TABLE `favoris` DISABLE KEYS */;
INSERT INTO `favoris` VALUES (1,2,'hebergement',2,'2026-05-22 13:36:18'),(2,2,'repas',1,'2026-05-22 13:36:18');
/*!40000 ALTER TABLE `favoris` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `guide`
--

DROP TABLE IF EXISTS `guide`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `guide` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `localisation` varchar(255) NOT NULL DEFAULT '',
  `description` text DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `region_id` int(10) unsigned DEFAULT NULL,
  `prix` decimal(10,2) NOT NULL DEFAULT 0.00,
  `capacite` int(11) NOT NULL DEFAULT 1,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `inclus` text DEFAULT NULL,
  `photo_principale` varchar(500) DEFAULT NULL,
  `photos_sec` text DEFAULT NULL,
  `statut` varchar(30) NOT NULL DEFAULT 'brouillon',
  `utilisateur_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_guide_region` (`region_id`),
  CONSTRAINT `fk_guide_region` FOREIGN KEY (`region_id`) REFERENCES `region` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guide`
--

LOCK TABLES `guide` WRITE;
/*!40000 ALTER TABLE `guide` DISABLE KEYS */;
INSERT INTO `guide` VALUES (1,'Visite guidée — Sidi Bou Saïd','Sidi Bou Saïd','Découvrez les ruelles bleues et blanches, les cafés historiques et les demeures andalouses avec un guide passionné.',NULL,2,60.00,8,'2025-01-01','2026-12-31','Guide, Thé au café des Nattes','https://images.unsplash.com/photo-1539635278303-d4002c07eae3?w=600','','actif',NULL,'2026-05-22 12:36:18','2026-05-22 12:36:18'),(2,'Tour de l\'île — Djerba','Djerba','Journée complète : synagogue la Ghriba, village de potiers, marchés et coucher de soleil sur la plage.',NULL,3,80.00,10,'2025-01-01','2026-12-31','Guide, Transport, Déjeuner','https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=600','','actif',NULL,'2026-05-22 12:36:18','2026-05-22 12:36:18'),(3,'Médina de Kairouan — Visite historique','Kairouan','Plongez dans 13 siècles d\'histoire : Grande Mosquée, bassins des Aghlabides, souks des tapis.',NULL,4,50.00,12,'2025-01-01','2026-12-31','Guide certifié, Entrées mosquée','https://images.unsplash.com/photo-1488085061387-422e29b40080?w=600','','actif',NULL,'2026-05-22 12:36:18','2026-05-22 12:36:18'),(4,'Excursion Sahara — Tozeur','Tozeur','Dunes de sable, ride en dromadaire, coucher de soleil sur l\'erg et nuit en campement berbère optionnelle.',NULL,5,120.00,6,'2025-01-01','2026-12-31','Guide, Dromadaire, Transport 4x4','https://images.unsplash.com/photo-1509316785289-025f5b846b35?w=600','','actif',NULL,'2026-05-22 12:36:18','2026-05-22 12:36:18'),(5,'Randonnée Takrouna','Takrouna','Ascension du piton rocheux avec un guide local. Histoire du village berbère et panoramas exceptionnels.',NULL,6,40.00,8,'2025-01-01','2026-12-31','Guide local, Eau, Collation','https://images.unsplash.com/photo-1551632811-561732d1e306?w=600','','actif',NULL,'2026-05-22 12:36:18','2026-05-22 12:36:18');
/*!40000 ALTER TABLE `guide` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hebergement`
--

DROP TABLE IF EXISTS `hebergement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hebergement` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `localisation` varchar(255) NOT NULL DEFAULT '',
  `description` text DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `region_id` int(10) unsigned DEFAULT NULL,
  `prix` decimal(10,2) NOT NULL DEFAULT 0.00,
  `capacite` int(11) NOT NULL DEFAULT 1,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `inclus` text DEFAULT NULL,
  `photo_principale` varchar(500) DEFAULT NULL,
  `photos_sec` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `statut` varchar(30) NOT NULL DEFAULT 'brouillon',
  `utilisateur_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_heb_region` (`region_id`),
  CONSTRAINT `fk_heb_region` FOREIGN KEY (`region_id`) REFERENCES `region` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hebergement`
--

LOCK TABLES `hebergement` WRITE;
/*!40000 ALTER TABLE `hebergement` DISABLE KEYS */;
INSERT INTO `hebergement` VALUES (1,'Dar Sidi — Maison d\'hôtes traditionnelle','Sidi Bou Saïd','Une demeure andalouse authentique avec vue sur le golfe de Tunis. Chambres décorées à la main, petit-déjeuner tunisien inclus.',NULL,2,180.00,4,'2025-01-01','2026-12-31','Petit-déjeuner, Wi-Fi, Parking','https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=600','',NULL,'actif',NULL,'2026-05-22 12:36:18','2026-05-22 12:36:18'),(2,'Riad Djerba — Vue mer','Djerba','Riad traditionnel à deux pas de la plage. Piscine privée, terrasse avec hamacs et cuisine locale sur demande.',NULL,3,220.00,6,'2025-01-01','2026-12-31','Piscine, Petit-déjeuner, Wi-Fi','https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=600','',NULL,'actif',NULL,'2026-05-22 12:36:18','2026-05-22 12:36:18'),(3,'Gîte Kairouan — Médina','Kairouan','Logement au coeur de la médina, à 5 minutes de la Grande Mosquée. Déco berbère authentique.',NULL,4,120.00,3,'2025-01-01','2026-12-31','Wi-Fi, Thé d\'accueil','https://images.unsplash.com/photo-1548013146-72479768bada?w=600','',NULL,'actif',NULL,'2026-05-22 12:36:18','2026-05-22 12:36:18'),(4,'Campement Tozeur — Nuit sous les étoiles','Tozeur','Tentes berbères confortables aux portes du Sahara. Dîner traditionnel au feu de camp inclus.',NULL,5,95.00,8,'2025-01-01','2026-12-31','Dîner, Petit-déjeuner, Draps','https://images.unsplash.com/photo-1537225228614-56cc3556d7ed?w=600','',NULL,'actif',NULL,'2026-05-22 12:36:18','2026-05-22 12:36:18'),(5,'Maison Takrouna — Vue panoramique','Takrouna','Maison en pierre perchée sur le piton rocheux avec vue à 360° sur la plaine.',NULL,6,110.00,4,'2025-01-01','2026-12-31','Petit-déjeuner berbère, Wi-Fi','https://images.unsplash.com/photo-1566073771259-6a8506099945?w=600','',NULL,'actif',NULL,'2026-05-22 12:36:18','2026-05-22 12:36:18'),(6,'Dar Kessra — Montagne','Kessra','Maison montagnarde en pierres centenaires. Ambiance authentique, air pur et hospitalité berbère.',NULL,7,85.00,5,'2025-01-01','2026-12-31','Petit-déjeuner, Produits locaux','https://images.unsplash.com/photo-1445019980597-93fa8acb246c?w=600','',NULL,'actif',NULL,'2026-05-22 12:36:18','2026-05-22 12:36:18'),(10,'oussama','vb','111111',NULL,4,14.00,10,'2026-05-18','2026-05-23','[\"Accueil personnalisé\"]','uploads/hebergements/heb_6a105991dbb685.78726498.png','[]',NULL,'publié',NULL,'2026-05-22 13:26:41','2026-05-22 13:26:41');
/*!40000 ALTER TABLE `hebergement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(150) NOT NULL,
  `otp` varchar(10) NOT NULL,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `region`
--

DROP TABLE IF EXISTS `region`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `region` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `meilleure_saison` varchar(255) DEFAULT NULL,
  `langues` varchar(255) DEFAULT NULL,
  `monnaie` varchar(100) DEFAULT NULL,
  `photo` varchar(500) DEFAULT NULL,
  `photo_principale` varchar(500) DEFAULT NULL,
  `photos_sec` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `region`
--

LOCK TABLES `region` WRITE;
/*!40000 ALTER TABLE `region` DISABLE KEYS */;
INSERT INTO `region` VALUES (1,'Tunis','La capitale tunisienne, mêlant médina millénaire classée UNESCO, souks animés et avenues modernes. Point de départ idéal pour découvrir le pays.','Printemps','Arabe, Français','Dinar tunisien (TND)','https://images.unsplash.com/photo-1605216663980-b7ca6e9f2451?w=800&q=80','https://images.unsplash.com/photo-1605216663980-b7ca6e9f2451?w=800&q=80',NULL,'2026-05-22 12:36:18','2026-05-22 12:36:18'),(2,'Sidi Bou Saïd','Village perché sur une falaise dominant la mer, célèbre pour ses maisons bleu et blanc, ses cafés historiques et ses demeures andalouses.','Printemps, Automne','Arabe, Français','Dinar tunisien (TND)','https://images.unsplash.com/photo-1539635278303-d4002c07eae3?w=800&q=80','https://images.unsplash.com/photo-1539635278303-d4002c07eae3?w=800&q=80',NULL,'2026-05-22 12:36:18','2026-05-22 12:36:18'),(3,'Djerba','Surnommée l\'île des rêves, Djerba séduit par ses plages, sa synagogue El Ghriba, ses villages de potiers et son artisanat authentique.','Été, Automne','Arabe, Français','Dinar tunisien (TND)','https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800&q=80','https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800&q=80',NULL,'2026-05-22 12:36:18','2026-05-22 12:36:18'),(4,'Kairouan','Quatrième ville sainte de l\'Islam, Kairouan abrite une médina UNESCO, la Grande Mosquée Okba et un savoir-faire ancestral du tapis noué.','Printemps, Automne','Arabe, Français','Dinar tunisien (TND)','https://images.unsplash.com/photo-1548013146-72479768bada?w=800&q=80','https://images.unsplash.com/photo-1548013146-72479768bada?w=800&q=80',NULL,'2026-05-22 12:36:18','2026-05-22 12:36:18'),(5,'Tozeur','Porte du Sahara tunisien, Tozeur est réputée pour ses immenses palmeraies, son architecture en brique de terre cuite et ses oasis.','Hiver, Printemps','Arabe, Français','Dinar tunisien (TND)','https://images.unsplash.com/photo-1509316785289-025f5b846b35?w=800&q=80','https://images.unsplash.com/photo-1509316785289-025f5b846b35?w=800&q=80',NULL,'2026-05-22 12:36:18','2026-05-22 12:36:18'),(6,'Takrouna','Village berbère perché sur un piton rocheux surplombant la plaine, offrant une vue panoramique exceptionnelle et une architecture unique.','Printemps, Automne','Arabe, Français, Berbère','Dinar tunisien (TND)','https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&q=80','https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&q=80',NULL,'2026-05-22 12:36:18','2026-05-22 12:36:18'),(7,'Kessra','Village de montagne niché dans le Djebel Kesra, l\'un des plus beaux villages de Tunisie, avec ses ruelles en pierre et son calme authentique.','Printemps, Automne','Arabe, Français, Berbère','Dinar tunisien (TND)','https://images.unsplash.com/photo-1445019980597-93fa8acb246c?w=800&q=80','https://images.unsplash.com/photo-1445019980597-93fa8acb246c?w=800&q=80',NULL,'2026-05-22 12:36:18','2026-05-22 12:36:18'),(9,'soussa','','sif','francais','50',NULL,'uploads/regions/reg_6a105a4244ecb5.75142819.png','[]','2026-05-22 13:29:38','2026-05-22 13:29:38');
/*!40000 ALTER TABLE `region` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `repas`
--

DROP TABLE IF EXISTS `repas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `repas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `localisation` varchar(255) NOT NULL DEFAULT '',
  `description` text DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `region_id` int(10) unsigned DEFAULT NULL,
  `prix` decimal(10,2) NOT NULL DEFAULT 0.00,
  `capacite` int(11) NOT NULL DEFAULT 1,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `inclus` text DEFAULT NULL,
  `photo_principale` varchar(500) DEFAULT NULL,
  `photos_sec` text DEFAULT NULL,
  `statut` varchar(30) NOT NULL DEFAULT 'brouillon',
  `utilisateur_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_repas_region` (`region_id`),
  CONSTRAINT `fk_repas_region` FOREIGN KEY (`region_id`) REFERENCES `region` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `repas`
--

LOCK TABLES `repas` WRITE;
/*!40000 ALTER TABLE `repas` DISABLE KEYS */;
INSERT INTO `repas` VALUES (1,'Déjeuner chez Fatma — Cuisine du terroir','Sidi Bou Saïd','Repas traditionnel fait maison : brik, couscous, pâtisseries maison. Servi en terrasse avec vue sur la mer.',NULL,2,45.00,6,'2025-01-01','2026-12-31','Entrée, Plat, Dessert, Thé','https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=600','','actif',NULL,'2026-05-22 12:36:18','2026-05-22 12:36:18'),(2,'Table djerbienne — Fruits de mer','Djerba','Déjeuner de fruits de mer frais pêchés le matin même. Recettes transmises de génération en génération.',NULL,3,65.00,8,'2025-01-01','2026-12-31','Poisson du jour, Salade, Dessert','https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=600','','actif',NULL,'2026-05-22 12:36:18','2026-05-22 12:36:18'),(3,'Dîner médiéval — Kairouan','Kairouan','Dîner au coeur de la médina : ojja, couscous au mouton, makroudh maison.',NULL,4,40.00,10,'2025-01-01','2026-12-31','Plat complet, Thé, Pâtisseries','https://images.unsplash.com/photo-1467003909585-2f8a72700288?w=600','','actif',NULL,'2026-05-22 12:36:18','2026-05-22 12:36:18'),(4,'Méchoui saharien — Tozeur','Tozeur','Repas traditionnel au feu de bois dans une oasis de palmiers. Agneau au méchoui, salade et dattes fraîches.',NULL,5,55.00,12,'2025-01-01','2026-12-31','Méchoui, Salade, Dattes, Thé','https://images.unsplash.com/photo-1529543544282-ea669407fca3?w=600','','actif',NULL,'2026-05-22 12:36:18','2026-05-22 12:36:18');
/*!40000 ALTER TABLE `repas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reservations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `utilisateur_id` int(11) DEFAULT NULL,
  `type_service` varchar(50) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `logement_id` int(11) DEFAULT NULL,
  `repas_id` int(11) DEFAULT NULL,
  `guide_id` int(11) DEFAULT NULL,
  `evenement_id` int(11) DEFAULT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `nb_voyageurs` int(11) DEFAULT 1,
  `nb_personnes` int(11) DEFAULT 1,
  `prix_total` decimal(10,2) DEFAULT NULL,
  `nom` varchar(150) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `statut` varchar(30) DEFAULT 'en_attente',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservations`
--

LOCK TABLES `reservations` WRITE;
/*!40000 ALTER TABLE `reservations` DISABLE KEYS */;
INSERT INTO `reservations` VALUES (1,2,2,'hebergement',1,1,NULL,NULL,NULL,'2026-06-10','2026-06-14',2,2,720.00,'Voyageur Test','user@tarkina.tn',NULL,'en_attente','2026-05-22 13:36:18'),(2,2,2,'guide',3,NULL,NULL,3,NULL,'2026-07-02','2026-07-02',3,3,50.00,'Voyageur Test','user@tarkina.tn',NULL,'confirmée','2026-05-22 13:36:18');
/*!40000 ALTER TABLE `reservations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stories`
--

DROP TABLE IF EXISTS `stories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int(10) unsigned NOT NULL,
  `region_id` int(10) unsigned DEFAULT NULL,
  `photo` varchar(255) NOT NULL,
  `caption` text DEFAULT NULL,
  `likes` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_story_user` (`utilisateur_id`),
  KEY `idx_story_region` (`region_id`),
  CONSTRAINT `fk_story_region` FOREIGN KEY (`region_id`) REFERENCES `region` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_story_user` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stories`
--

LOCK TABLES `stories` WRITE;
/*!40000 ALTER TABLE `stories` DISABLE KEYS */;
INSERT INTO `stories` VALUES (1,2,5,'https://images.unsplash.com/photo-1509316785289-025f5b846b35?w=600','Un coucher de soleil magique sur les dunes de Tozeur. Une nuit inoubliable en campement berbère !',12,'2026-05-22 13:36:18'),(2,2,2,'https://images.unsplash.com/photo-1539635278303-d4002c07eae3?w=600','Les ruelles bleues et blanches de Sidi Bou Saïd, un vrai bijou. Le thé au café des Nattes est incontournable.',8,'2026-05-22 13:36:18'),(3,1,4,'https://images.unsplash.com/photo-1548013146-72479768bada?w=600','La médina de Kairouan déborde d\'histoire. La Grande Mosquée est impressionnante.',5,'2026-05-22 13:36:18');
/*!40000 ALTER TABLE `stories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `utilisateur` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'utilisateur',
  `adresse` varchar(255) DEFAULT NULL,
  `telephone` varchar(50) DEFAULT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `photo_profil` varchar(500) DEFAULT NULL,
  `date_inscription` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilisateur`
--

LOCK TABLES `utilisateur` WRITE;
/*!40000 ALTER TABLE `utilisateur` DISABLE KEYS */;
INSERT INTO `utilisateur` VALUES (1,'Admin','Tarkina','admin@tarkina.tn','$2y$10$Exrdpddfw1jBObajVPAB3ezY37DR8s2l7IjG38iINA5PPSEkTOkXC','admin','Avenue Habib Bourguiba, Tunis','+216 71 000 000','Tunis',NULL,NULL,'2026-05-22 12:36:18','2026-05-22 12:36:18'),(2,'Voyageur','Test','user@tarkina.tn','$2y$10$PVRJUwIHbb1oc74b8Q.os.ARYA5bQn5NdS8Na9CN0I4R3jY8wfBGO','utilisateur','Rue de la Liberté, Sfax','+216 98 123 456','Sfax',NULL,NULL,'2026-05-22 12:36:18','2026-05-22 12:36:18');
/*!40000 ALTER TABLE `utilisateur` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-22 15:33:26

SET FOREIGN_KEY_CHECKS=1;
