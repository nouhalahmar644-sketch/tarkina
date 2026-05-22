<?php
require 'db.php';
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$id = (int)$_POST['id'];
mysqli_query($conn, "UPDATE stories SET likes = likes + 1 WHERE id = $id");
$result = mysqli_query($conn, "SELECT likes FROM stories WHERE id = $id");
$row = mysqli_fetch_assoc($result);

echo json_encode(['success' => true, 'likes' => $row['likes']]);
?>
