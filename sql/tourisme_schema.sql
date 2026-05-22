-- Base de données et table pour le PFE tourisme (à exécuter une fois dans phpMyAdmin ou MySQL)
-- Adaptez le nom de la base si besoin : ici `tourisme` comme demandé.

CREATE DATABASE IF NOT EXISTS `tourisme` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `tourisme`;

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

-- Table des réservations liée à hebergement
CREATE TABLE IF NOT EXISTS `reservation` (
    `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `logement_id`     INT UNSIGNED NOT NULL,
    `utilisateur_id`  INT          NOT NULL,
    `date_debut`      DATE         NOT NULL,
    `date_fin`        DATE         NOT NULL,
    `nb_personnes`    INT          NOT NULL DEFAULT 1,
    `statut`          VARCHAR(30)  NOT NULL DEFAULT 'en_attente',
    `created_at`      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`logement_id`) REFERENCES `hebergement`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table OTP pour réinitialisation du mot de passe
CREATE TABLE IF NOT EXISTS `password_resets` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `email`      VARCHAR(150) NOT NULL,
    `otp`        VARCHAR(10)  NOT NULL,
    `expires_at` DATETIME     NOT NULL,
    INDEX (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
