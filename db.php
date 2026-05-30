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
$db_name = 'tarkina';

// Étape 2 : ouvrir la connexion MySQLi en mode procédural
// First, connect without selecting a database to ensure it exists
$initialConn = mysqli_connect($host, $db_user, $db_pass);
if (!$initialConn) {
    die('Initial connection failed: ' . mysqli_connect_error() . "\n");
}
if (!mysqli_query($initialConn, "CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
    die('Failed to create database: ' . mysqli_error($initialConn) . "\n");
}
mysqli_close($initialConn);

// Now connect to the (now existing) database
$conn = mysqli_connect($host, $db_user, $db_pass, $db_name);
if (!$conn) {
    die('Erreur de connexion à la base de données : ' . mysqli_connect_error());
}

// Étape 3 : arrêter proprement si la connexion échoue
// (already handled above)

mysqli_set_charset($conn, 'utf8mb4');
