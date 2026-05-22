<?php
require_once __DIR__ . '/bootstrap.php';

if (empty($_SESSION['user_id']) || empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    $current_page = 'admin/' . basename($_SERVER['PHP_SELF']);
    $query_string = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
    header('Location: ../login.php?redirect=' . urlencode($current_page . $query_string));
    exit;
}
