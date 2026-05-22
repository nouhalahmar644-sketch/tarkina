<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || empty($_POST['id'])) {
    header('Location: stories.php');
    exit;
}

$id = (int)$_POST['id'];
$user_id = (int)$_SESSION['user_id'];

$result = mysqli_query($conn, "SELECT photo FROM stories WHERE id = $id AND utilisateur_id = $user_id");
if (mysqli_num_rows($result) === 0) {
    header('Location: stories.php');
    exit;
}

$story = mysqli_fetch_assoc($result);
$photo_path = 'uploads/stories/' . $story['photo'];
if (file_exists($photo_path)) {
    unlink($photo_path);
}

mysqli_query($conn, "DELETE FROM stories WHERE id = $id AND utilisateur_id = $user_id");
header('Location: stories.php');
exit;
?>
