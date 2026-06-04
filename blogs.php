<?php
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/i18n.php';

$L_ALL = [
    'fr' => [
        'page_title'   => 'Blog — Tarkina',
        'hero_a'       => 'Le ', 'hero_b' => 'Blog', 'hero_c' => ' des voyageurs',
        'hero_sub'     => 'Récits, conseils et recommandations partagés par la communauté Tarkina. Inspirez-vous et racontez vos aventures en Tunisie.',
        'all_regions'  => 'Toutes les régions',
        'btn_write'    => 'Écrire un article',
        'btn_login'    => 'Connectez-vous pour publier',
        'empty_a'      => 'Aucun article pour le moment',
        'empty_b'      => ' dans cette région',
        'empty_c'      => '. Soyez le premier à partager votre voyage !',
    ],
    'ar' => [
        'page_title'   => 'المدونة — تاركينا',
        'hero_a'       => '', 'hero_b' => 'مدونة', 'hero_c' => ' المسافرين',
        'hero_sub'     => 'حكايات ونصائح وتوصيات يتقاسمها مجتمع تاركينا. استلهم منها وشاركنا مغامراتك في تونس.',
        'all_regions'  => 'جميع الجهات',
        'btn_write'    => 'اكتب مقالًا',
        'btn_login'    => 'سجّل الدخول لنشر مقال',
        'empty_a'      => 'لا توجد مقالات حتى الآن',
        'empty_b'      => ' في هذه الجهة',
        'empty_c'      => '. كن أول من يشارك رحلته!',
    ],
    'en' => [
        'page_title'   => 'Blog — Tarkina',
        'hero_a'       => 'The travellers\' ', 'hero_b' => 'Blog', 'hero_c' => '',
        'hero_sub'     => 'Stories, tips and recommendations shared by the Tarkina community. Get inspired and share your own adventures in Tunisia.',
        'all_regions'  => 'All regions',
        'btn_write'    => 'Write a post',
        'btn_login'    => 'Log in to publish',
        'empty_a'      => 'No posts yet',
        'empty_b'      => ' in this region',
        'empty_c'      => '. Be the first to share your trip!',
    ],
];
$L = $L_ALL[$lang] ?? $L_ALL['fr'];

// Per-language hero copy + search placeholder/button label
$L_HERO = [
    'fr' => ['title' => 'Le Blog des Voyageurs', 'tag' => 'Blog',     'ph' => 'Rechercher un article…',  'search' => 'Rechercher'],
    'ar' => ['title' => 'مدونة المسافرين',      'tag' => 'المدونة', 'ph' => 'ابحث عن مقال…',          'search' => 'بحث'],
    'en' => ['title' => 'The Travellers\' Blog', 'tag' => 'Blog',     'ph' => 'Search articles…',         'search' => 'Search'],
];
$LH = $L_HERO[$lang] ?? $L_HERO['fr'];

$regionFilter = isset($_GET['region']) ? (int) $_GET['region'] : 0;
$searchTerm   = isset($_GET['search']) ? trim((string) $_GET['search']) : '';

// Regions for the filter bar
$regions = [];
$rq = mysqli_query($conn, "SELECT id, nom FROM region ORDER BY nom ASC");
while ($rq && $row = mysqli_fetch_assoc($rq)) { $regions[] = $row; }

// Posts — filter by region + free-text search on title/content/recommendation
$where  = [];
$params = [];
$types  = '';
if ($regionFilter > 0) { $where[] = 'b.region_id = ?'; $params[] = $regionFilter; $types .= 'i'; }
if ($searchTerm !== '') {
    $where[] = '(b.titre LIKE ? OR b.contenu LIKE ? OR b.recommandation LIKE ?)';
    $like = '%' . $searchTerm . '%';
    $params[] = $like; $params[] = $like; $params[] = $like;
    $types .= 'sss';
}
$sql = "SELECT b.*, u.prenom, u.nom, r.nom AS region_nom
        FROM blogs b
        JOIN utilisateur u ON b.utilisateur_id = u.id
        LEFT JOIN region r ON b.region_id = r.id"
       . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
       . ' ORDER BY b.created_at DESC';
$posts = [];
if ($pst = mysqli_prepare($conn, $sql)) {
    if ($params) { mysqli_stmt_bind_param($pst, $types, ...$params); }
    mysqli_stmt_execute($pst);
    $pr = mysqli_stmt_get_result($pst);
    while ($pr && $row = mysqli_fetch_assoc($pr)) { $posts[] = $row; }
    mysqli_stmt_close($pst);
}

function blog_excerpt($text, $len = 130) {
    $text = trim((string) $text);
    return mb_strlen($text) > $len ? mb_substr($text, 0, $len) . '…' : $text;
}
function blog_initials($prenom, $nom) {
    return strtoupper(mb_substr((string)$prenom, 0, 1) . mb_substr((string)$nom, 0, 1));
}
/**
 * Resolve a blog photo to a usable src.
 * Handles legacy/corrupted values like "uploads\stories\https://images.unsplash.com/..."
 * by extracting the embedded URL. Falls back to a tasteful Tunisia image.
 */
function blog_photo_src($photo, $fallback) {
    $photo = trim((string) $photo);
    if ($photo === '') { return $fallback; }
    // Corrupted path that contains a full URL glued onto a local prefix
    if (preg_match('~https?://\S+~', $photo, $m)) { return $m[0]; }
    // Already a clean absolute URL
    if (preg_match('~^https?://~i', $photo)) { return $photo; }
    // Local upload path: normalise Windows backslashes to forward slashes
    return str_replace('\\', '/', $photo);
}
function blog_date_fr($datetime, $lang = 'fr') {
    $ts = strtotime((string) $datetime);
    if (!$ts) { return ''; }
    $months_fr = [1=>'janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
    $months_ar = [1=>'جانفي','فيفري','مارس','أفريل','ماي','جوان','جويلية','أوت','سبتمبر','أكتوبر','نوفمبر','ديسمبر'];
    $months_en = [1=>'January','February','March','April','May','June','July','August','September','October','November','December'];
    $d = (int)date('j', $ts);
    $m = (int)date('n', $ts);
    $y = date('Y', $ts);
    if ($lang === 'ar') { return $d . ' ' . $months_ar[$m] . ' ' . $y; }
    if ($lang === 'en') { return $months_en[$m] . ' ' . $d . ', ' . $y; }
    return $d . ' ' . $months_fr[$m] . ' ' . $y;
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>" dir="<?= htmlspecialchars($dir) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($L['page_title']) ?></title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/rtl.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="assets/css/blog.css">
</head>
<body class="blog-page">
<?php $navLight = true; include 'navbar.php'; ?>

<section class="blog-head">
  <div class="blog-head__inner">
    <span class="blog-head__tag"><?= htmlspecialchars($LH['tag']) ?></span>
    <h1 class="blog-head__title"><?= htmlspecialchars($LH['title']) ?></h1>
    <p class="blog-head__sub"><?= htmlspecialchars($L['hero_sub']) ?></p>
    <form class="blog-search" method="get" action="blogs.php" role="search">
      <?php if ($regionFilter > 0): ?>
        <input type="hidden" name="region" value="<?= (int)$regionFilter ?>">
      <?php endif; ?>
      <i class="bi bi-search blog-search__icon" aria-hidden="true"></i>
      <input type="text" name="search" class="blog-search__input"
             value="<?= htmlspecialchars($searchTerm) ?>"
             placeholder="<?= htmlspecialchars($LH['ph']) ?>">
      <?php if ($searchTerm !== ''): ?>
        <a href="blogs.php<?= $regionFilter ? '?region=' . (int)$regionFilter : '' ?>"
           class="blog-search__clear" aria-label="Clear"><i class="bi bi-x-lg"></i></a>
      <?php endif; ?>
      <button type="submit" class="blog-search__btn"><?= htmlspecialchars($LH['search']) ?></button>
    </form>
  </div>
</section>

<div class="blog-wrap">
  <div class="blog-toolbar">
    <div class="region-filter">
      <a href="blogs.php" class="region-chip <?= $regionFilter === 0 ? 'active' : '' ?>"><?= htmlspecialchars($L['all_regions']) ?></a>
      <?php foreach ($regions as $r): ?>
        <a href="blogs.php?region=<?= (int)$r['id'] ?>" class="region-chip <?= $regionFilter === (int)$r['id'] ? 'active' : '' ?>"><?= htmlspecialchars($r['nom']) ?></a>
      <?php endforeach; ?>
    </div>
    <?php if (isset($_SESSION['user_id'])): ?>
      <a href="blog-add.php" class="btn-create"><i class="bi bi-plus-lg"></i> <?= htmlspecialchars($L['btn_write']) ?></a>
    <?php else: ?>
      <a href="login.php" class="btn-create"><i class="bi bi-pencil"></i> <?= htmlspecialchars($L['btn_login']) ?></a>
    <?php endif; ?>
  </div>

  <div class="row g-4">
    <div class="col-lg-8">
      <?php if (empty($posts)): ?>
        <div class="blog-empty">
          <i class="bi bi-journal-text" style="font-size:2.5rem;color:#ccc;"></i>
          <p><?= htmlspecialchars($L['empty_a']) ?><?= $regionFilter ? htmlspecialchars($L['empty_b']) : '' ?><?= htmlspecialchars($L['empty_c']) ?></p>
        </div>
      <?php else: ?>
        <div class="blog-grid">
          <?php foreach ($posts as $p): ?>
            <?php
              $img = blog_photo_src($p['photo'] ?? '', 'https://images.unsplash.com/photo-1539650116574-75c0c6d73f6e?w=700&q=80');
            ?>
            <a href="blog-post.php?id=<?= (int)$p['id'] ?>" class="blog-card">
              <div class="blog-card__img">
                <img loading="lazy" src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($p['titre']) ?>">
              </div>
              <div class="blog-card__body">
                <?php if (!empty($p['region_nom'])): ?>
                  <span class="blog-card__region-badge"><i class="bi bi-geo-alt-fill"></i> <?= htmlspecialchars($p['region_nom']) ?></span>
                <?php endif; ?>
                <h3 class="blog-card__title"><?= htmlspecialchars($p['titre']) ?></h3>
                <span class="blog-card__date"><i class="bi bi-calendar3"></i> <?= htmlspecialchars(blog_date_fr($p['created_at'], $lang)) ?></span>
                <p class="blog-card__excerpt"><?= htmlspecialchars(blog_excerpt($p['contenu'])) ?></p>
                <div class="blog-card__more">Lire la suite &rarr;</div>
                <div class="blog-card__meta">
                  <span class="blog-card__author"><span class="avatar"><?= htmlspecialchars(blog_initials($p['prenom'], $p['nom'])) ?></span><?= htmlspecialchars($p['prenom'].' '.$p['nom']) ?></span>
                  <span class="blog-like"><i class="bi bi-heart-fill"></i> <?= (int)$p['likes'] ?></span>
                </div>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="col-lg-4">
      <div class="blog-sidebar">
        <?php
        $recentPosts = array_slice($posts, 0, 3);
        if (!empty($recentPosts)):
        ?>
          <div class="sidebar-widget">
            <h4 class="sidebar-widget__title">Articles récents</h4>
            <div class="recent-posts-list">
              <?php foreach ($recentPosts as $rp): ?>
                <?php
                  $rpImg = blog_photo_src($rp['photo'] ?? '', 'https://images.unsplash.com/photo-1539650116574-75c0c6d73f6e?w=150&q=80');
                ?>
                <a href="blog-post.php?id=<?= (int)$rp['id'] ?>" class="recent-post-item">
                  <div class="recent-post-thumb">
                    <img src="<?= htmlspecialchars($rpImg) ?>" alt="<?= htmlspecialchars($rp['titre']) ?>" onerror="this.src='images/placeholder.jpg'">
                  </div>
                  <div class="recent-post-details">
                    <div class="recent-post-title"><?= htmlspecialchars($rp['titre']) ?></div>
                    <div class="recent-post-date"><?= htmlspecialchars(blog_date_fr($rp['created_at'], $lang)) ?></div>
                  </div>
                </a>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
