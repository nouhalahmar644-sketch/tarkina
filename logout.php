<?php
/**
 * Déconnexion : destruction de la session puis redirection vers la page de connexion.
 */
require_once __DIR__ . '/includes/session_bootstrap.php';

// Vider toutes les variables de session
$_SESSION = [];

// Supprimer le cookie de session côté navigateur (si les paramètres sont connus)
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

session_destroy();

header('Location: login.php');
exit;
