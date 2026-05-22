<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || empty($_POST['id'])) {
    header('Location: blogs.php');
    exit;
}

$id      = (int) $_POST['id'];
$user_id = (int) $_SESSION['user_id'];

$result = mysqli_query($conn, "SELECT photo FROM blogs WHERE id = $id AND utilisateur_id = $user_id");
if (!$result || mysqli_num_rows($result) === 0) {
    header('Location: blogs.php');
    exit;
}

$blog      = mysqli_fetch_assoc($result);
$photo_path = 'uploads/blogs/' . $blog['photo'];
if (!empty($blog['photo']) && file_exists($photo_path)) {
    unlink($photo_path);
}

mysqli_query($conn, "DELETE FROM blogs WHERE id = $id AND utilisateur_id = $user_id");
header('Location: blogs.php');
exit;
