<?php
session_start();
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/i18n.php';

$L_ALL = [
    'fr' => [
        'page_title' => 'Mes Favoris - Tarkina',
        'heading'    => 'Mes Favoris',
        'subtext'    => 'Retrouvez ici tous les services que vous avez ajoutés à vos favoris.',
    ],
    'ar' => [
        'page_title' => 'مفضلاتي - تاركينا',
        'heading'    => 'مفضلاتي',
        'subtext'    => 'تجد هنا جميع الخدمات التي أضفتها إلى مفضلاتك.',
    ],
    'en' => [
        'page_title' => 'My Favorites - Tarkina',
        'heading'    => 'My Favorites',
        'subtext'    => 'Find here all the services you have added to your favorites.',
    ],
];
$L = $L_ALL[$lang] ?? $L_ALL['fr'];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>" dir="<?= htmlspecialchars($dir) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($L['page_title']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Lato:wght@400;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/rtl.css">
    <style>
        .page-container { padding: 100px 56px; max-width: 1200px; margin: 0 auto; min-height: 80vh; }
        .page-title { font-family: 'Playfair Display', serif; font-size: 48px; margin-bottom: 40px; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="page-container">
    <h1 class="page-title"><?= htmlspecialchars($L['heading']) ?></h1>
    <p><?= htmlspecialchars($L['subtext']) ?></p>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
