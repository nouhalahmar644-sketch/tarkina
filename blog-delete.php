<?php
session_start();
require_once __DIR__ . '/db.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {
    header('Location: blogs.php');
    exit;
}

$id     = (int) $_POST['id'];
$userId = (int) $_SESSION['user_id'];

// Only the author may delete; fetch to confirm ownership + remove photo file
$stmt = mysqli_prepare($conn, 'SELECT photo FROM blogs WHERE id = ? AND utilisateur_id = ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 'ii', $id, $userId);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if ($row) {
    if (!empty($row['photo']) && strpos($row['photo'], 'uploads/blogs/') === 0) {
        $file = __DIR__ . '/' . $row['photo'];
        if (is_file($file)) { @unlink($file); }
    }
    // blog_comments rows are removed via ON DELETE CASCADE
    $del = mysqli_prepare($conn, 'DELETE FROM blogs WHERE id = ? AND utilisateur_id = ?');
    mysqli_stmt_bind_param($del, 'ii', $id, $userId);
    mysqli_stmt_execute($del);
    mysqli_stmt_close($del);
}

header('Location: blogs.php');
exit;
