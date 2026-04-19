<?php
/**
 * Protège une page : accessible uniquement si l'utilisateur est connecté.
 * Redirige vers login.php sinon.
 */

require_once __DIR__ . '/session_bootstrap.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
