<?php
/**
 * Connexion à la base de données MySQL (style procédural avec MySQLi).
 * Étape 1 : définir les paramètres de connexion (adaptés à XAMPP par défaut).
 */

// Hôte MySQL (127.0.0.1 = localhost)
$host = '127.0.0.1';

// Utilisateur MySQL (par défaut sous XAMPP : root sans mot de passe)
$db_user = 'root';
$db_pass = '';

// Nom de la base pour la plateforme tourisme (PFE)
$db_name = 'tourisme';

// Étape 2 : ouvrir la connexion MySQLi en mode procédural
$conn = mysqli_connect($host, $db_user, $db_pass, $db_name);

// Étape 3 : arrêter proprement si la connexion échoue
if (!$conn) {
    die('Erreur de connexion à la base de données : ' . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');

// Table réservations (utilisée par les pages de service)
require_once __DIR__ . '/includes/service_helpers.php';
service_ensure_reservations_table($conn);
