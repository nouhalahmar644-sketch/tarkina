-- Base de données pour le PFE tourisme Tarkina
-- Exécuter dans phpMyAdmin ou MySQL CLI

CREATE DATABASE IF NOT EXISTS `tourisme` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `tourisme`;

-- ─── Utilisateurs ───
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `adresse` varchar(255) NULL,
  `date_inscription` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ─── Régions ───
CREATE TABLE IF NOT EXISTS `region` (
    `id`               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nom`              VARCHAR(255)  NOT NULL,
    `description`      TEXT,
    `meilleure_saison` VARCHAR(255)  NULL,
    `langues`          VARCHAR(255)  NULL,
    `monnaie`          VARCHAR(100)  NULL,
    `photo_principale` VARCHAR(500)  NULL,
    `photos_sec`       TEXT          NULL,
    `created_at`       TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ─── Hébergement ───
CREATE TABLE IF NOT EXISTS `hebergement` (
    `id`               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `titre`            VARCHAR(255)  NOT NULL,
    `localisation`     VARCHAR(255)  NOT NULL DEFAULT '',
    `description`      TEXT,
    `region`           VARCHAR(100),
    `prix`             DECIMAL(10,2) NOT NULL DEFAULT 0,
    `capacite`         INT           NOT NULL DEFAULT 1,
    `date_debut`       DATE          NULL,
    `date_fin`         DATE          NULL,
    `inclus`           TEXT          NULL,
    `photo_principale` VARCHAR(500)  NULL,
    `photos_sec`       TEXT          NULL,
    `image`            VARCHAR(255),
    `statut`           VARCHAR(30)   NOT NULL DEFAULT 'brouillon',
    `utilisateur_id`   INT,
    `created_at`       TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ─── Repas ───
CREATE TABLE IF NOT EXISTS `repas` (
    `id`               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `titre`            VARCHAR(255)  NOT NULL,
    `localisation`     VARCHAR(255)  NOT NULL DEFAULT '',
    `description`      TEXT,
    `prix`             DECIMAL(10,2) NOT NULL DEFAULT 0,
    `capacite`         INT           NOT NULL DEFAULT 1,
    `date_debut`       DATE          NULL,
    `date_fin`         DATE          NULL,
    `inclus`           TEXT          NULL,
    `photo_principale` VARCHAR(500)  NULL,
    `photos_sec`       TEXT          NULL,
    `statut`           VARCHAR(30)   NOT NULL DEFAULT 'brouillon',
    `utilisateur_id`   INT,
    `created_at`       TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ─── Guide ───
CREATE TABLE IF NOT EXISTS `guide` (
    `id`               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `titre`            VARCHAR(255)  NOT NULL,
    `localisation`     VARCHAR(255)  NOT NULL DEFAULT '',
    `description`      TEXT,
    `prix`             DECIMAL(10,2) NOT NULL DEFAULT 0,
    `capacite`         INT           NOT NULL DEFAULT 1,
    `date_debut`       DATE          NULL,
    `date_fin`         DATE          NULL,
    `inclus`           TEXT          NULL,
    `photo_principale` VARCHAR(500)  NULL,
    `photos_sec`       TEXT          NULL,
    `statut`           VARCHAR(30)   NOT NULL DEFAULT 'brouillon',
    `utilisateur_id`   INT,
    `created_at`       TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ─── Événement ───
CREATE TABLE IF NOT EXISTS `evenement` (
    `id`               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `titre`            VARCHAR(255)  NOT NULL,
    `localisation`     VARCHAR(255)  NOT NULL DEFAULT '',
    `description`      TEXT,
    `prix`             DECIMAL(10,2) NOT NULL DEFAULT 0,
    `capacite`         INT           NOT NULL DEFAULT 1,
    `date_debut`       DATE          NULL,
    `date_fin`         DATE          NULL,
    `inclus`           TEXT          NULL,
    `photo_principale` VARCHAR(500)  NULL,
    `photos_sec`       TEXT          NULL,
    `statut`           VARCHAR(30)   NOT NULL DEFAULT 'brouillon',
    `utilisateur_id`   INT,
    `created_at`       TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ─── Artisanat (NOUVEAU) ───
CREATE TABLE IF NOT EXISTS `artisanat` (
    `id`               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `titre`            VARCHAR(255)  NOT NULL,
    `localisation`     VARCHAR(255)  NOT NULL DEFAULT '',
    `description`      TEXT,
    `prix`             DECIMAL(10,2) NOT NULL DEFAULT 0,
    `stock`            INT           NOT NULL DEFAULT 0,
    `photo_principale` VARCHAR(500)  NULL,
    `photos_sec`       TEXT          NULL,
    `statut`           VARCHAR(30)   NOT NULL DEFAULT 'brouillon',
    `utilisateur_id`   INT,
    `created_at`       TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ─── Réservations (corrigé : pluriel + FK multiples) ───
CREATE TABLE IF NOT EXISTS `reservations` (
    `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `utilisateur_id`  INT          NOT NULL,
    `logement_id`     INT UNSIGNED NULL,
    `repas_id`        INT UNSIGNED NULL,
    `guide_id`        INT UNSIGNED NULL,
    `evenement_id`    INT UNSIGNED NULL,
    `date_debut`      DATE         NOT NULL,
    `date_fin`        DATE         NULL,
    `nb_personnes`    INT          NOT NULL DEFAULT 1,
    `statut`          VARCHAR(30)  NOT NULL DEFAULT 'en_attente',
    `created_at`      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`logement_id`)    REFERENCES `hebergement`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`repas_id`)       REFERENCES `repas`(`id`)       ON DELETE CASCADE,
    FOREIGN KEY (`guide_id`)       REFERENCES `guide`(`id`)       ON DELETE CASCADE,
    FOREIGN KEY (`evenement_id`)   REFERENCES `evenement`(`id`)   ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ─── Commandes (artisanat) ───
CREATE TABLE IF NOT EXISTS `commandes` (
    `id`                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `utilisateur_id`    INT          NOT NULL,
    `artisanat_id`      INT UNSIGNED NOT NULL,
    `quantite`          INT          NOT NULL DEFAULT 1,
    `adresse_livraison` TEXT         NOT NULL,
    `total`             DECIMAL(10,2) NOT NULL DEFAULT 0,
    `statut`            VARCHAR(30)  NOT NULL DEFAULT 'en attente',
    `created_at`        TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`artisanat_id`)   REFERENCES `artisanat`(`id`)   ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ─── Avis (reviews) ───
CREATE TABLE IF NOT EXISTS `avis` (
    `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `utilisateur_id`  INT          NOT NULL,
    `hebergement_id`  INT UNSIGNED NULL,
    `repas_id`        INT UNSIGNED NULL,
    `guide_id`        INT UNSIGNED NULL,
    `evenement_id`    INT UNSIGNED NULL,
    `note`            INT          NOT NULL DEFAULT 5,
    `commentaire`     TEXT,
    `created_at`      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ─── OTP pour réinitialisation du mot de passe ───
CREATE TABLE IF NOT EXISTS `password_resets` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `email`      VARCHAR(150) NOT NULL,
    `otp`        VARCHAR(10)  NOT NULL,
    `expires_at` DATETIME     NOT NULL,
    INDEX (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ═══════════════════════════════════════════════════════════
-- DONNÉES DE DÉMONSTRATION
-- ═══════════════════════════════════════════════════════════

-- Admin user (email: admin@tarkina.tn, password: admin123)
INSERT IGNORE INTO `utilisateur` (`nom`, `prenom`, `email`, `mot_de_passe`, `role`, `adresse`) VALUES
('Admin', 'Tarkina', 'admin@tarkina.tn', '$2y$10$mVLMibS.StF7HOe.4HvSVeUsy9uR4LMsT4lOIJncyQ0tpIjfyJ6dK', 'admin', 'Tunis, Tunisie');

-- Utilisateur test (email: test@test.com, password: test123)
INSERT IGNORE INTO `utilisateur` (`nom`, `prenom`, `email`, `mot_de_passe`, `role`, `adresse`) VALUES
('Ben Ali', 'Mohamed', 'test@test.com', '$2y$10$rLgiVLNroNbM2AlBbYcyfuFXE75Xt2ddVmWHBpxXyUjbJSvK0oSgG', 'utilisateur', 'Sousse, Tunisie');

-- Régions
INSERT IGNORE INTO `region` (`id`, `nom`, `description`, `meilleure_saison`, `langues`, `monnaie`, `photo_principale`) VALUES
(1, 'Kessra', 'Perchée dans les montagnes du centre-ouest de la Tunisie, Kessra est un village berbère authentique avec une forteresse byzantine millénaire et des paysages à couper le souffle. Un lieu idéal pour découvrir la culture amazighe, les oliveraies en terrasses et l''hospitalité légendaire des montagnards tunisiens.', 'Printemps / Automne', 'Arabe, Amazigh, Français', 'TND (Dinar Tunisien)', 'https://images.unsplash.com/photo-1540260074744-934336c53549?auto=format&fit=crop&w=800&q=80'),
(2, 'Djerba', 'L''île des rêves, Djerba est une destination emblématique connue pour ses plages de sable doré, ses villages pittoresques, sa synagogue historique de la Ghriba et son artisanat raffiné. Un mélange unique de cultures arabe, juive et berbère dans un cadre méditerranéen enchanteur.', 'Mai – Octobre', 'Arabe, Français', 'TND (Dinar Tunisien)', 'https://images.unsplash.com/photo-1568322503251-e3a0ecb4ea3e?auto=format&fit=crop&w=800&q=80'),
(3, 'Tozeur', 'Porte du Sahara tunisien, Tozeur fascine par son architecture en briques ocre, sa palmeraie luxuriante de plus de 200 000 palmiers, et ses oasis de montagne spectaculaires. Découvrez le désert, les chotts salés et l''hospitalité du Sud tunisien.', 'Novembre – Mars', 'Arabe, Français', 'TND (Dinar Tunisien)', 'https://images.unsplash.com/photo-1517309230475-6736d926b979?auto=format&fit=crop&w=800&q=80');

-- Hébergements
INSERT IGNORE INTO `hebergement` (`id`, `titre`, `localisation`, `description`, `region`, `prix`, `capacite`, `date_debut`, `date_fin`, `inclus`, `photo_principale`, `statut`) VALUES
(1, 'Dar El Jebel - Maison traditionnelle', 'Kessra', 'Séjournez dans une maison traditionnelle berbère entièrement restaurée avec vue panoramique sur les montagnes. Petit-déjeuner traditionnel inclus, terrasse ombragée et accueil chaleureux garanti.', 'Kessra', 85.00, 6, '2026-01-01', '2026-12-31', '["Petit-déjeuner traditionnel","Wi-Fi gratuit","Parking gratuit","Terrasse panoramique"]', 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80', 'publié'),
(2, 'Riad Djerba Bleu', 'Djerba', 'Un riad traditionnel au cœur de Houmt Souk, décoré avec des mosaïques artisanales et un patio intérieur fleuri. Proche de la plage et du marché.', 'Djerba', 120.00, 4, '2026-01-01', '2026-12-31', '["Petit-déjeuner","Climatisation","Piscine","Navette plage"]', 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=80', 'publié'),
(3, 'Oasis Lodge Tozeur', 'Tozeur', 'Lodge éco-responsable niché au cœur de la palmeraie de Tozeur. Architecture en pisé, chambres climatisées et restaurant servant des spécialités du Sud.', 'Tozeur', 95.00, 8, '2026-01-01', '2026-12-31', '["Demi-pension","Piscine","Excursion palmeraie","Wi-Fi"]', 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=800&q=80', 'publié');

-- Repas
INSERT IGNORE INTO `repas` (`id`, `titre`, `localisation`, `description`, `prix`, `capacite`, `date_debut`, `date_fin`, `inclus`, `photo_principale`, `statut`) VALUES
(1, 'Couscous traditionnel chez Oum Salah', 'Kessra', 'Dégustez un couscous préparé à la main par Oum Salah, selon une recette familiale transmise depuis quatre générations. Légumes du jardin, viande d''agneau et harissa maison.', 25.00, 8, '2026-01-01', '2026-12-31', '["Entrée","Plat principal","Dessert","Thé à la menthe"]', 'https://images.unsplash.com/photo-1585937421612-70a008356fbe?auto=format&fit=crop&w=800&q=80', 'publié'),
(2, 'Déjeuner pêcheur à Djerba', 'Djerba', 'Repas de poissons frais pêchés le matin même, grillés au charbon de bois avec salade mechouia et pain tabouna. Vue sur la mer depuis la terrasse.', 35.00, 6, '2026-01-01', '2026-12-31', '["Salade mechouia","Poisson grillé","Fruits de saison","Boisson"]', 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=800&q=80', 'publié');

-- Guides
INSERT IGNORE INTO `guide` (`id`, `titre`, `localisation`, `description`, `prix`, `capacite`, `date_debut`, `date_fin`, `inclus`, `photo_principale`, `statut`) VALUES
(1, 'Randonnée Kessra - Sentier des Oliviers', 'Kessra', 'Parcourez les sentiers millénaires de Kessra avec un guide local passionné. Visite de la forteresse byzantine, des grottes troglodytes et des oliveraies en terrasses. Déjeuner pique-nique inclus.', 40.00, 10, '2026-01-01', '2026-12-31', '["Guide certifié","Déjeuner pique-nique","Eau minérale","Transport local"]', 'https://images.unsplash.com/photo-1551632811-561732d1e306?auto=format&fit=crop&w=800&q=80', 'publié'),
(2, 'Tour de Djerba en Tuk-Tuk', 'Djerba', 'Découvrez les trésors cachés de Djerba en tuk-tuk : villages de pêcheurs, synagogue de la Ghriba, ateliers de poterie et coucher de soleil sur la plage.', 55.00, 4, '2026-01-01', '2026-12-31', '["Tuk-tuk privé","Guide francophone","Entrées musées","Thé offert"]', 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?auto=format&fit=crop&w=800&q=80', 'publié');

-- Événements
INSERT IGNORE INTO `evenement` (`id`, `titre`, `localisation`, `description`, `prix`, `capacite`, `date_debut`, `date_fin`, `inclus`, `photo_principale`, `statut`) VALUES
(1, 'Festival des Oliviers de Kessra', 'Kessra', 'Festival annuel célébrant la récolte des olives avec musique traditionnelle, danse folklorique, ateliers de pressage d''huile d''olive et dégustation de produits locaux.', 15.00, 50, '2026-11-15', '2026-11-17', '["Entrée festival","Dégustation huile d''olive","Concert traditionnel"]', 'https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?auto=format&fit=crop&w=800&q=80', 'publié'),
(2, 'Nuit des Étoiles à Tozeur', 'Tozeur', 'Observation astronomique dans le désert avec un astrophotographe professionnel. Dîner bédouin sous les étoiles, balade en chameau au coucher du soleil.', 60.00, 20, '2026-08-10', '2026-08-10', '["Transport désert","Dîner bédouin","Télescope","Balade chameau"]', 'https://images.unsplash.com/photo-1419242902214-272b3f66ee7a?auto=format&fit=crop&w=800&q=80', 'publié');

-- Artisanat
INSERT IGNORE INTO `artisanat` (`id`, `titre`, `localisation`, `description`, `prix`, `stock`, `photo_principale`, `statut`) VALUES
(1, 'Tapis berbère fait main', 'Kessra', 'Tapis tissé à la main par les artisanes de Kessra selon des motifs traditionnels amazighs. Laine naturelle teinte avec des pigments végétaux. Dimensions : 120x180 cm.', 250.00, 5, 'https://images.unsplash.com/photo-1600166898405-da9535204843?auto=format&fit=crop&w=800&q=80', 'publié'),
(2, 'Poterie de Djerba', 'Djerba', 'Ensemble de poteries artisanales de Guellala, village potier historique de Djerba. Inclut un vase, un bol et une assiette décorée. Pièces uniques peintes à la main.', 85.00, 12, 'https://images.unsplash.com/photo-1565193566173-7a0ee3dbe261?auto=format&fit=crop&w=800&q=80', 'publié'),
(3, 'Huile d''olive extra vierge BIO', 'Kessra', 'Huile d''olive extra vierge biologique pressée à froid, récoltée dans les oliveraies centenaires de Kessra. Bouteille artisanale de 750ml.', 18.00, 30, 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?auto=format&fit=crop&w=800&q=80', 'publié');
