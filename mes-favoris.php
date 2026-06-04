<?php
session_start();
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/i18n.php';

$L_ALL = [
    'fr' => [
        'page_title' => 'Mes Favoris — Tarkina',
        'heading'    => 'Mes Favoris',
        'subtext'    => 'Retrouvez ici tous les services que vous avez ajoutés à vos favoris.',
        'empty'      => 'Vous n\'avez encore aucun service en favori.',
        'empty_cta'  => 'Explorer les régions',
        'remove'     => 'Retirer',
        'view'       => 'Voir',
        'cat_hebergement' => 'Hébergement',
        'cat_repas'       => 'Repas maison',
        'cat_guide'       => 'Guide local',
        'cat_evenement'   => 'Événement',
        'cat_artisanat'   => 'Artisanat',
    ],
    'ar' => [
        'page_title' => 'مفضلاتي — تاركينا',
        'heading'    => 'مفضلاتي',
        'subtext'    => 'تجد هنا جميع الخدمات التي أضفتها إلى مفضلاتك.',
        'empty'      => 'لا توجد خدمات في مفضلاتك حتى الآن.',
        'empty_cta'  => 'استكشف الجهات',
        'remove'     => 'إزالة',
        'view'       => 'عرض',
        'cat_hebergement' => 'إقامة',
        'cat_repas'       => 'وجبة منزلية',
        'cat_guide'       => 'مرشد محلي',
        'cat_evenement'   => 'فعالية',
        'cat_artisanat'   => 'حِرف يدوية',
    ],
    'en' => [
        'page_title' => 'My Favorites — Tarkina',
        'heading'    => 'My Favorites',
        'subtext'    => 'All the services you have added to your favorites.',
        'empty'      => 'You have no favorites yet.',
        'empty_cta'  => 'Explore regions',
        'remove'     => 'Remove',
        'view'       => 'View',
        'cat_hebergement' => 'Accommodation',
        'cat_repas'       => 'Home meal',
        'cat_guide'       => 'Local guide',
        'cat_evenement'   => 'Event',
        'cat_artisanat'   => 'Crafts',
    ],
];
$L = $L_ALL[$lang] ?? $L_ALL['fr'];

$uid = (int) $_SESSION['user_id'];

function fav_resolve_image($path, $type) {
    $path = trim((string) $path);
    if ($path === '') return 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=600&q=80';
    if (strpos($path, 'http') === 0) return $path;
    if (strpos($path, 'images/') === 0 || strpos($path, 'uploads/') === 0) return $path;
    return 'uploads/' . $type . '/' . ltrim($path, '/');
}

$cats = ['hebergement', 'repas', 'guide', 'evenement', 'artisanat'];
$items = [];
foreach ($cats as $cat) {
    $col = $cat . '_id';
    $sql = "SELECT f.id AS fav_id, s.id, s.titre, s.prix, s.photo_principale, s.localisation
            FROM favoris f
            INNER JOIN `$cat` s ON f.`$col` = s.id
            WHERE f.utilisateur_id = ? AND f.`$col` IS NOT NULL
            ORDER BY f.id DESC";
    if ($st = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($st, 'i', $uid);
        mysqli_stmt_execute($st);
        $r = mysqli_stmt_get_result($st);
        while ($row = mysqli_fetch_assoc($r)) {
            $row['type'] = $cat;
            $row['type_label'] = $L['cat_' . $cat];
            $items[] = $row;
        }
        mysqli_stmt_close($st);
    }
}
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
      .fav-wrap { max-width: 1240px; margin: 0 auto; padding: 60px 24px 80px; min-height: 70vh; }
      .fav-head { margin-bottom: 32px; }
      .fav-title { font-family: 'Playfair Display', serif; font-size: clamp(2rem, 4vw, 2.6rem); font-weight: 800; color: #0b1c30; margin: 0 0 6px; }
      .fav-sub { color: #6b7280; font-size: .95rem; }
      .fav-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 22px; }
      .fav-card { background: #fff; border: 1px solid #e7e1da; border-radius: 16px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 4px 16px rgba(11,28,48,.05); transition: transform .2s, box-shadow .2s; position: relative; }
      .fav-card:hover { transform: translateY(-4px); box-shadow: 0 16px 36px rgba(11,28,48,.12); }
      .fav-card__img { height: 170px; overflow: hidden; position: relative; }
      .fav-card__img img { width: 100%; height: 100%; object-fit: cover; display: block; }
      .fav-card__badge { position: absolute; top: 12px; left: 12px; background: rgba(255,255,255,.95); color: #f16e22; padding: 4px 11px; border-radius: 50px; font-size: 11px; font-weight: 800; letter-spacing: .6px; text-transform: uppercase; }
      .fav-card__body { padding: 16px 18px 0; flex: 1; display: flex; flex-direction: column; }
      .fav-card__title { font-weight: 700; font-size: 1rem; color: #0b1c30; margin: 0 0 6px; }
      .fav-card__loc { color: #888; font-size: .82rem; margin: 0 0 12px; }
      .fav-card__foot { margin-top: auto; border-top: 1px solid #f1f1f1; padding: 12px 18px; display: flex; align-items: center; justify-content: space-between; gap: 10px; }
      .fav-card__price { font-weight: 800; color: #0b1c30; }
      .fav-card__view { background: #f16e22; color: #fff; text-decoration: none; padding: 8px 14px; border-radius: 50px; font-size: .82rem; font-weight: 700; }
      .fav-card__view:hover { background: #d95716; color: #fff; }
      .fav-empty { text-align: center; padding: 60px 20px; background: #fff; border-radius: 16px; border: 1px solid #e7e1da; }
      .fav-empty p { color: #6b7280; margin: 0 0 18px; }
      .fav-empty a { background: #f16e22; color: #fff; text-decoration: none; padding: 10px 22px; border-radius: 50px; font-weight: 700; }
    </style>
</head>
<body>

<?php $navLight = true; include 'navbar.php'; ?>

<main class="fav-wrap">
  <div class="fav-head">
    <h1 class="fav-title"><?= htmlspecialchars($L['heading']) ?></h1>
    <p class="fav-sub"><?= htmlspecialchars($L['subtext']) ?></p>
  </div>

  <?php if (empty($items)): ?>
    <div class="fav-empty">
      <p>♡ <?= htmlspecialchars($L['empty']) ?></p>
      <a href="explorer.php"><?= htmlspecialchars($L['empty_cta']) ?> →</a>
    </div>
  <?php else: ?>
    <div class="fav-grid">
      <?php foreach ($items as $it):
        $img = fav_resolve_image($it['photo_principale'], $it['type']);
      ?>
        <article class="fav-card">
          <button type="button" class="fav-btn is-fav" data-type="<?= htmlspecialchars($it['type']) ?>" data-id="<?= (int) $it['id'] ?>" data-logged="1" title="<?= htmlspecialchars($L['remove']) ?>" aria-label="Retirer">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21s-7-4.534-7-10a4.5 4.5 0 0 1 8-2.83A4.5 4.5 0 0 1 19 11c0 5.466-7 10-7 10z"/></svg>
          </button>
          <div class="fav-card__img">
            <span class="fav-card__badge"><?= htmlspecialchars($it['type_label']) ?></span>
            <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($it['titre']) ?>" loading="lazy" onerror="this.src='https://images.unsplash.com/photo-1566073771259-6a8506099945?w=600&q=80'">
          </div>
          <div class="fav-card__body">
            <h3 class="fav-card__title"><?= htmlspecialchars($it['titre']) ?></h3>
            <?php if (!empty($it['localisation'])): ?>
              <p class="fav-card__loc">📍 <?= htmlspecialchars($it['localisation']) ?></p>
            <?php endif; ?>
          </div>
          <div class="fav-card__foot">
            <span class="fav-card__price"><?= number_format((float) $it['prix'], 0) ?> TND</span>
            <a class="fav-card__view" href="<?= htmlspecialchars($it['type']) ?>.php?id=<?= (int) $it['id'] ?>"><?= htmlspecialchars($L['view']) ?> →</a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</main>

<?php include 'footer.php'; ?>
<script>
// Remove the card from the DOM when the heart is unliked from the favoris page.
document.querySelectorAll('.fav-card .fav-btn').forEach(function (b) {
  b.addEventListener('click', function () {
    setTimeout(function () {
      if (!b.classList.contains('is-fav')) {
        var card = b.closest('.fav-card');
        if (card) card.style.transition = 'opacity .3s ease';
        if (card) card.style.opacity = '0';
        if (card) setTimeout(function () { card.remove(); }, 280);
      }
    }, 250);
  });
});
</script>
</body>
</html>
