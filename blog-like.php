<?php
session_start();
require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$id = (int) $_POST['id'];
$stmt = mysqli_prepare($conn, 'UPDATE blogs SET likes = likes + 1 WHERE id = ?');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

$res = mysqli_prepare($conn, 'SELECT likes FROM blogs WHERE id = ? LIMIT 1');
mysqli_stmt_bind_param($res, 'i', $id);
mysqli_stmt_execute($res);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($res));
mysqli_stmt_close($res);

echo json_encode(['success' => (bool)$row, 'likes' => $row ? (int)$row['likes'] : 0]);
