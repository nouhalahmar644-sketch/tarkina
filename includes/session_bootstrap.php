<?php
/**
 * Démarrage de session avec quelques options de base (protection simple).
 * À inclure une seule fois au début des pages qui utilisent la session.
 */

if (session_status() === PHP_SESSION_NONE) {
    // Cookies de session plus stricts : HTTPOnly (pas accessible en JS) et SameSite
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => false,   // mettre true en HTTPS en production
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}
