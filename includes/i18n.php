<?php
/**
 * Minimal i18n bootstrap.
 *
 * Include this near the top of every public page (BEFORE the <html> tag):
 *   require_once __DIR__ . '/includes/i18n.php';
 *
 * After include, the following are defined:
 *   $lang   — 'fr' | 'ar' | 'en'   (current language, session-stored)
 *   $dir    — 'ltr' | 'rtl'
 *   $is_rtl — bool
 *
 * Each page provides its own translations through a `$L` array, e.g.:
 *   $L = ['fr' => ['hero' => '…'], 'ar' => ['hero' => '…']][$lang] ?? [];
 *   echo $L['hero'];
 *
 * When $is_rtl, pages should include assets/css/rtl.css so layout adjusts.
 */

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

if (isset($_GET['lang']) && in_array($_GET['lang'], ['fr', 'en', 'ar'], true)) {
    $_SESSION['lang'] = $_GET['lang'];
}

$lang   = $_SESSION['lang'] ?? 'fr';
$dir    = ($lang === 'ar') ? 'rtl' : 'ltr';
$is_rtl = ($lang === 'ar');
