<?php
session_start();
require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'method']);
    exit;
}
if (empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'auth']);
    exit;
}

$type = trim((string) ($_POST['type'] ?? ''));
$id   = (int) ($_POST['id'] ?? 0);
$user = (int) $_SESSION['user_id'];

$columns = [
    'hebergement' => 'hebergement_id',
    'repas'       => 'repas_id',
    'guide'       => 'guide_id',
    'evenement'   => 'evenement_id',
    'artisanat'   => 'artisanat_id',
];
if (!isset($columns[$type]) || $id <= 0) {
    echo json_encode(['success' => false, 'error' => 'params']);
    exit;
}
$col = $columns[$type];

// Already a favorite?
$stmt = mysqli_prepare($conn, "SELECT id FROM favoris WHERE utilisateur_id = ? AND `$col` = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 'ii', $user, $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $favId);
$exists = mysqli_stmt_fetch($stmt) ? (int) $favId : 0;
mysqli_stmt_close($stmt);

if ($exists > 0) {
    // Remove
    $del = mysqli_prepare($conn, "DELETE FROM favoris WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($del, 'i', $exists);
    mysqli_stmt_execute($del);
    mysqli_stmt_close($del);
    echo json_encode(['success' => true, 'liked' => false]);
} else {
    // Add — set the right FK column, all others NULL
    $ins = mysqli_prepare($conn, "INSERT INTO favoris (utilisateur_id, `$col`) VALUES (?, ?)");
    mysqli_stmt_bind_param($ins, 'ii', $user, $id);
    mysqli_stmt_execute($ins);
    mysqli_stmt_close($ins);
    echo json_encode(['success' => true, 'liked' => true]);
}
