<?php
session_start();
require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {
    echo json_encode(['success' => false, 'error' => 'invalid']);
    exit;
}

if (empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'auth_required']);
    exit;
}

$blog_id = (int) $_POST['id'];
$user_id = (int) $_SESSION['user_id'];

// Auto-create the per-user tracker so it self-provisions on any machine.
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS blog_user_likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    blog_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_blog_user (blog_id, user_id),
    INDEX idx_blog (blog_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Does this user already like this blog?
$alreadyLiked = false;
if ($st = mysqli_prepare($conn, "SELECT 1 FROM blog_user_likes WHERE blog_id = ? AND user_id = ? LIMIT 1")) {
    mysqli_stmt_bind_param($st, 'ii', $blog_id, $user_id);
    mysqli_stmt_execute($st);
    mysqli_stmt_store_result($st);
    $alreadyLiked = mysqli_stmt_num_rows($st) > 0;
    mysqli_stmt_close($st);
}

if ($alreadyLiked) {
    // Toggle off (unlike)
    if ($st = mysqli_prepare($conn, "DELETE FROM blog_user_likes WHERE blog_id = ? AND user_id = ?")) {
        mysqli_stmt_bind_param($st, 'ii', $blog_id, $user_id);
        mysqli_stmt_execute($st);
        $removed = mysqli_stmt_affected_rows($st) > 0;
        mysqli_stmt_close($st);
        if ($removed) {
            $up = mysqli_prepare($conn, "UPDATE blogs SET likes = GREATEST(likes - 1, 0) WHERE id = ?");
            mysqli_stmt_bind_param($up, 'i', $blog_id);
            mysqli_stmt_execute($up);
            mysqli_stmt_close($up);
        }
    }
    $liked_now = false;
} else {
    // Toggle on (like) — UNIQUE constraint makes this safe against double-clicks
    if ($st = mysqli_prepare($conn, "INSERT IGNORE INTO blog_user_likes (blog_id, user_id) VALUES (?, ?)")) {
        mysqli_stmt_bind_param($st, 'ii', $blog_id, $user_id);
        mysqli_stmt_execute($st);
        $inserted = mysqli_stmt_affected_rows($st) > 0;
        mysqli_stmt_close($st);
        if ($inserted) {
            $up = mysqli_prepare($conn, "UPDATE blogs SET likes = likes + 1 WHERE id = ?");
            mysqli_stmt_bind_param($up, 'i', $blog_id);
            mysqli_stmt_execute($up);
            mysqli_stmt_close($up);
        }
    }
    $liked_now = true;
}

// Read final count
$likes = 0;
if ($r = mysqli_prepare($conn, "SELECT likes FROM blogs WHERE id = ? LIMIT 1")) {
    mysqli_stmt_bind_param($r, 'i', $blog_id);
    mysqli_stmt_execute($r);
    mysqli_stmt_bind_result($r, $l);
    if (mysqli_stmt_fetch($r)) { $likes = (int) $l; }
    mysqli_stmt_close($r);
}

echo json_encode([
    'success' => true,
    'liked'   => $liked_now,
    'likes'   => $likes,
]);
