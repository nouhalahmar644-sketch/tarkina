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
  `date_inscription` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Si vous aviez une ancienne table avec une colonne `adresse` NOT NULL, vous pouvez soit
-- la rendre optionnelle : ALTER TABLE utilisateur MODIFY adresse varchar(255) NULL;
-- soit supprimer la colonne si elle ne sert pas au projet.
