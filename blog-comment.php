<?php
session_start();
require_once __DIR__ . '/db.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: blogs.php');
    exit;
}

$blogId  = isset($_POST['blog_id']) ? (int) $_POST['blog_id'] : 0;
$contenu = trim($_POST['contenu'] ?? '');
$userId  = (int) $_SESSION['user_id'];

if ($blogId > 0 && $contenu !== '') {
    // ensure the blog exists
    $chk = mysqli_prepare($conn, 'SELECT id FROM blogs WHERE id = ? LIMIT 1');
    mysqli_stmt_bind_param($chk, 'i', $blogId);
    mysqli_stmt_execute($chk);
    $exists = mysqli_fetch_row(mysqli_stmt_get_result($chk));
    mysqli_stmt_close($chk);
    if ($exists) {
        $stmt = mysqli_prepare($conn, 'INSERT INTO blog_comments (blog_id, utilisateur_id, contenu) VALUES (?,?,?)');
        mysqli_stmt_bind_param($stmt, 'iis', $blogId, $userId, $contenu);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

header('Location: blog-post.php?id=' . $blogId . '#comments');
exit;
