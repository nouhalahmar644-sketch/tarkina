<?php
/**
 * Protège une page : accessible uniquement si l'utilisateur est connecté.
 * Redirige vers login.php sinon.
 */

require_once __DIR__ . '/session_bootstrap.php';

if (empty($_SESSION['user_id'])) {
    $current_page = basename($_SERVER['PHP_SELF']);
    $query_string = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
    header('Location: login.php?redirect=' . urlencode($current_page . $query_string));
    exit;
}
