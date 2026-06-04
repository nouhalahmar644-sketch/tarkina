<?php
session_start();
mb_internal_encoding('UTF-8');
header('Content-Type: text/html; charset=utf-8');
require 'db.php';
require_once __DIR__ . '/includes/i18n.php';
require_once __DIR__ . '/includes/region_photo.php';
mysqli_set_charset($conn, 'utf8mb4');

$L_ALL = [
    'fr' => [
        'err_invalid'    => 'Région invalide.',
        'err_notfound'   => 'Région introuvable.',
        'back'           => 'Retour',
        'tag_label'      => 'TUNISIE · DESTINATION',
        'a_savoir'       => 'À savoir',
        'best_season'    => 'Meilleure saison',
        'languages'      => 'Langues',
        'currency'       => 'Monnaie',
        'services_count' => 'Services dispo.',
        'services_title' => 'Services disponibles',
        'filter_hint'    => 'Filtrez par catégorie pour trouver ce qui vous correspond.',
        'pill_all'       => 'Tout',
        'pill_heb'       => '🏠 Hébergement',
        'pill_repas'     => '🍽️ Repas maison',
        'pill_guide'     => '🧭 Guide local',
        'pill_event'     => '🎉 Événement',
        'pill_art'       => '💎 Artisanat',
        'b_hebergement'  => 'Hébergement',
        'b_repas'        => 'Repas maison',
        'b_guide'        => 'Guide local',
        'b_evenement'    => 'Événement',
        'b_artisanat'    => 'Artisanat',
        'u_nuit'         => 'nuit',
        'u_pers'         => 'pers.',
        'u_piece'        => 'pièce',
        'cap_max'        => 'max',
        'in_stock'       => 'en stock',
        'none_in_region' => 'Aucun service disponible dans cette région pour le moment.',
        'no_desc'        => 'Une expérience authentique à découvrir.',
        'packs_title'    => 'Packs disponibles dans cette région',
        'packs_sub'      => 'Des forfaits prêts à réserver, pensés autour de cette destination.',
        'pack_from'      => 'À partir de',
        'pack_save'      => 'Économie',
        'pack_reserve'   => 'Réserver ce pack',
    ],
    'ar' => [
        'err_invalid'    => 'الجهة غير صالحة.',
        'err_notfound'   => 'الجهة غير موجودة.',
        'back'           => 'رجوع',
        'tag_label'      => 'تونس · وجهة',
        'a_savoir'       => 'معلومات مفيدة',
        'best_season'    => 'أفضل موسم',
        'languages'      => 'اللغات',
        'currency'       => 'العملة',
        'services_count' => 'خدمات متاحة',
        'services_title' => 'الخدمات المتاحة',
        'filter_hint'    => 'صنّف حسب الفئة لتجد ما يناسبك.',
        'pill_all'       => 'الكل',
        'pill_heb'       => '🏠 إقامة',
        'pill_repas'     => '🍽️ وجبة منزلية',
        'pill_guide'     => '🧭 مرشد محلي',
        'pill_event'     => '🎉 فعالية',
        'pill_art'       => '💎 حِرف يدوية',
        'b_hebergement'  => 'إقامة',
        'b_repas'        => 'وجبة منزلية',
        'b_guide'        => 'مرشد محلي',
        'b_evenement'    => 'فعالية',
        'b_artisanat'    => 'حِرف يدوية',
        'u_nuit'         => 'ليلة',
        'u_pers'         => 'للشخص',
        'u_piece'        => 'قطعة',
        'cap_max'        => 'أقصى',
        'in_stock'       => 'متوفّر',
        'none_in_region' => 'لا توجد خدمات متاحة في هذه الجهة حالياً.',
        'no_desc'        => 'تجربة أصيلة في انتظار اكتشافك.',
        'packs_title'    => 'الباقات المتاحة في هذه الجهة',
        'packs_sub'      => 'باقات جاهزة للحجز مصمَّمة خصيصاً لهذه الوجهة.',
        'pack_from'      => 'ابتداءً من',
        'pack_save'      => 'توفير',
        'pack_reserve'   => 'احجز هذه الباقة',
    ],
    'en' => [
        'err_invalid'    => 'Invalid region.',
        'err_notfound'   => 'Region not found.',
        'back'           => 'Back',
        'tag_label'      => 'TUNISIA · DESTINATION',
        'a_savoir'       => 'Good to know',
        'best_season'    => 'Best season',
        'languages'      => 'Languages',
        'currency'       => 'Currency',
        'services_count' => 'Available services',
        'services_title' => 'Available services',
        'filter_hint'    => 'Filter by category to find what suits you.',
        'pill_all'       => 'All',
        'pill_heb'       => '🏠 Accommodation',
        'pill_repas'     => '🍽️ Home meal',
        'pill_guide'     => '🧭 Local guide',
        'pill_event'     => '🎉 Event',
        'pill_art'       => '💎 Crafts',
        'b_hebergement'  => 'Accommodation',
        'b_repas'        => 'Home meal',
        'b_guide'        => 'Local guide',
        'b_evenement'    => 'Event',
        'b_artisanat'    => 'Crafts',
        'u_nuit'         => 'night',
        'u_pers'         => 'pers.',
        'u_piece'        => 'piece',
        'cap_max'        => 'max',
        'in_stock'       => 'in stock',
        'none_in_region' => 'No services available in this region at the moment.',
        'no_desc'        => 'An authentic experience to discover.',
        'packs_title'    => 'Packs available in this region',
        'packs_sub'      => 'Ready-to-book packages curated around this destination.',
        'pack_from'      => 'From',
        'pack_save'      => 'Save',
        'pack_reserve'   => 'Book this pack',
    ],
];
$L = $L_ALL[$lang] ?? $L_ALL['fr'];

// ----- Fetch region -----
$region_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($region_id <= 0) die(htmlspecialchars($L['err_invalid']));

$st = mysqli_prepare($conn, "SELECT * FROM region WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($st, 'i', $region_id);
mysqli_stmt_execute($st);
$res = mysqli_stmt_get_result($st);
$region = mysqli_fetch_assoc($res);
mysqli_stmt_close($st);
if (!$region) die(htmlspecialchars($L['err_notfound']));

$regionNom = $region['nom'];

// ----- Parse photos -----
function tk_resolve_photo($path, $fallback) {
    if (empty($path)) return $fallback;
    if (strpos($path, 'http') === 0) return $path;
    if (strpos($path, 'images/') === 0) return $path;
    if (strpos($path, 'uploads/') === 0) return $path;
    if (strpos($path, 'assets/') === 0) return $path;
    if (file_exists('uploads/regions/' . $path)) return 'uploads/regions/' . $path;
    if (file_exists('images/regions/' . $path)) return 'images/regions/' . $path;
    if (file_exists('uploads/' . $path)) return 'uploads/' . $path;
    return 'uploads/regions/' . $path;
}

$fallbackImg = 'https://images.unsplash.com/photo-1539635278303-d4002c07eae3?w=1200&q=80';
$mainPhoto = tk_resolve_photo($region['photo_principale'] ?? '', '');
if ($mainPhoto === '') {
    // Code-only manifest fallback for admin uploads (DB rows don't sync via git)
    $fb = region_photo_fallback($region_id);
    if ($fb !== '') { $mainPhoto = $fb; }
}
if ($mainPhoto === '') { $mainPhoto = $fallbackImg; }

$secPhotos = [];
if (!empty($region['photos_sec'])) {
    $decoded = json_decode($region['photos_sec'], true);
    if (is_array($decoded)) {
        $secPhotos = array_values(array_filter($decoded, fn($p) => is_string($p) && $p !== ''));
    } else {
        $secPhotos = array_values(array_filter(array_map('trim', explode(',', (string) $region['photos_sec']))));
    }
}
// Need 4 thumbnails — pad with stock images if fewer
$stockPad = [
    'https://images.unsplash.com/photo-1509316785289-025f5b846b35?w=600&q=80',
    'https://images.unsplash.com/photo-1561625116-5f8675632053?w=600&q=80',
    'https://images.unsplash.com/photo-1548013146-72479768bada?w=600&q=80',
    'https://images.unsplash.com/photo-1565193566173-7a0ee3dbe261?w=600&q=80',
];
while (count($secPhotos) < 4) {
    $secPhotos[] = $stockPad[count($secPhotos) % 4];
}
$secPhotos = array_slice(array_map(fn($p) => tk_resolve_photo($p, $fallbackImg), $secPhotos), 0, 4);

// ----- Fetch services grouped by category -----
$categories = ['hebergement', 'repas', 'guide', 'evenement', 'artisanat'];
$catMeta = [
    'hebergement' => ['cap' => 'capacite', 'unit' => $L['u_nuit'],  'pill' => $L['pill_heb'],   'badge' => $L['b_hebergement']],
    'repas'       => ['cap' => 'capacite', 'unit' => $L['u_pers'],  'pill' => $L['pill_repas'], 'badge' => $L['b_repas']],
    'guide'       => ['cap' => 'capacite', 'unit' => $L['u_pers'],  'pill' => $L['pill_guide'], 'badge' => $L['b_guide']],
    'evenement'   => ['cap' => 'capacite', 'unit' => $L['u_pers'],  'pill' => $L['pill_event'], 'badge' => $L['b_evenement']],
    'artisanat'   => ['cap' => 'stock',    'unit' => $L['u_piece'], 'pill' => $L['pill_art'],   'badge' => $L['b_artisanat']],
];

$items_by_cat = [];
$counts = [];
$allItems = [];
foreach ($categories as $cat) {
    $col = $catMeta[$cat]['cap'];
    $sql = "SELECT id, titre, prix, localisation, `$col` AS capacite, description, photo_principale
            FROM `$cat`
            WHERE region_id = ? AND statut IN ('actif','publié')
            ORDER BY id DESC";
    $items = [];
    if ($q = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($q, 'i', $region_id);
        mysqli_stmt_execute($q);
        $r = mysqli_stmt_get_result($q);
        while ($row = mysqli_fetch_assoc($r)) {
            $row['_cat'] = $cat;
            $items[] = $row;
            $allItems[] = $row;
        }
        mysqli_stmt_close($q);
    }
    $items_by_cat[$cat] = $items;
    $counts[$cat] = count($items);
}
$total = array_sum($counts);

// ----- Existing favoris for the current user (to pre-fill heart state) -----
$userFavs = ['hebergement' => [], 'repas' => [], 'guide' => [], 'evenement' => [], 'artisanat' => []];
if (!empty($_SESSION['user_id'])) {
    $uid = (int) $_SESSION['user_id'];
    $favRes = mysqli_query($conn, "SELECT hebergement_id, repas_id, guide_id, evenement_id, artisanat_id
                                    FROM favoris WHERE utilisateur_id = $uid");
    if ($favRes) {
        while ($f = mysqli_fetch_assoc($favRes)) {
            if (!empty($f['hebergement_id'])) $userFavs['hebergement'][] = (int) $f['hebergement_id'];
            if (!empty($f['repas_id']))       $userFavs['repas'][]       = (int) $f['repas_id'];
            if (!empty($f['guide_id']))       $userFavs['guide'][]       = (int) $f['guide_id'];
            if (!empty($f['evenement_id']))   $userFavs['evenement'][]   = (int) $f['evenement_id'];
            if (!empty($f['artisanat_id']))   $userFavs['artisanat'][]   = (int) $f['artisanat_id'];
        }
    }
}
$isLogged = !empty($_SESSION['user_id']);

// ----- Fetch packs available in this region -----
// (auto-create the tables so the page never errors on a fresh install)
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS packs (
    id INT AUTO_INCREMENT PRIMARY KEY, titre VARCHAR(255) NOT NULL,
    slogan VARCHAR(500) NOT NULL DEFAULT '', region_id INT NOT NULL,
    image_path VARCHAR(500) NOT NULL DEFAULT '',
    prix_original DECIMAL(10,2) NOT NULL DEFAULT 0,
    prix_final DECIMAL(10,2) NOT NULL DEFAULT 0,
    statut VARCHAR(20) NOT NULL DEFAULT 'actif', position INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

$regionPacks = [];
if ($pq = mysqli_prepare($conn, "SELECT id, titre, slogan, image_path, prix_original, prix_final
                                  FROM packs WHERE region_id = ? AND statut = 'actif'
                                  ORDER BY position ASC, id DESC")) {
    mysqli_stmt_bind_param($pq, 'i', $region_id);
    mysqli_stmt_execute($pq);
    $pr = mysqli_stmt_get_result($pq);
    while ($prow = mysqli_fetch_assoc($pr)) { $regionPacks[] = $prow; }
    mysqli_stmt_close($pq);
}

function tk_pack_image(string $path, string $fallback): string {
    $path = trim($path);
    if ($path === '') return $fallback;
    if (stripos($path, 'http') === 0) return $path;
    if (str_starts_with($path, 'uploads/') || str_starts_with($path, 'images/')) return $path;
    return 'uploads/packs/' . ltrim($path, '/');
}

function tk_service_image($path, $cat, $fallback) {
    if (empty($path)) return $fallback;
    if (strpos($path, 'http') === 0) return $path;
    if (strpos($path, 'images/') === 0) return $path;
    if (strpos($path, 'uploads/') === 0) return $path;
    return 'uploads/' . $cat . '/' . ltrim($path, '/');
}
$svcFallback = 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=600&q=80';

// Stable per-item rating (no random reload jitter, no DB roundtrips)
function tk_stable_rating(int $id): array {
    $rating = 4.2 + (($id * 7) % 8) / 10;       // 4.2 - 4.9
    $count  = 10 + (($id * 13) % 90);            // 10 - 99
    return [number_format($rating, 1), $count];
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>" dir="<?= htmlspecialchars($dir) ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($regionNom, ENT_QUOTES, 'UTF-8') ?> — Tarkina</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800;900&family=Lato:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/rtl.css">
<style>
*,*::before,*::after{box-sizing:border-box;}
:root{--navy:#0b1c30;--coral:#f16e22;--text:#1a1a1a;--muted:#6b7280;--border:#e5e7eb;--bg:#ffffff;}
body{font-family:'Lato',sans-serif;background:var(--bg);color:var(--text);margin:0;}
a{color:inherit;}

/* ── HERO IMAGE (full-width, comme sur la page d'accueil) ── */
.region-hero{max-width:1240px;margin:24px auto 0;padding:0 20px;}
.region-hero__wrap{position:relative;height:420px;border-radius:18px;overflow:hidden;box-shadow:0 14px 40px rgba(11,28,48,.10);}
.region-hero__wrap img{width:100%;height:100%;object-fit:cover;display:block;}
.region-hero__wrap::after{content:'';position:absolute;inset:0;background:linear-gradient(180deg, rgba(0,0,0,0) 55%, rgba(11,28,48,.55) 100%);pointer-events:none;}
.region-hero__label{position:absolute;left:24px;bottom:22px;color:#fff;font-family:'Playfair Display',serif;font-weight:800;font-size:clamp(1.6rem,3.6vw,2.6rem);text-shadow:0 2px 12px rgba(0,0,0,.35);}
@media(max-width:760px){.region-hero__wrap{height:280px;}}

/* ── REGION HEADER ── */
.region-header{display:grid;grid-template-columns:1.6fr 1fr;gap:60px;max-width:1240px;margin:48px auto;padding:0 20px;align-items:start;}
.region-tag{color:var(--coral);font-size:12px;font-weight:700;letter-spacing:2px;margin-bottom:14px;display:flex;align-items:center;gap:8px;}
.region-tag::before{content:'◆';font-size:11px;}
.region-name{font-family:'Playfair Display',serif;font-size:clamp(2.4rem,4.6vw,4rem);font-weight:800;line-height:1.05;margin:0 0 22px;color:var(--navy);}
.region-desc p{color:#444;line-height:1.85;font-size:.97rem;margin:0 0 14px;}

.info-card{background:#fff;border:1px solid var(--border);border-radius:16px;padding:26px 26px 18px;box-shadow:0 4px 24px rgba(17,17,17,.04);}
.info-card h3{font-family:'Playfair Display',serif;font-size:1.45rem;font-weight:700;margin:0 0 18px;color:var(--navy);}
.info-row{display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-bottom:1px solid #f1f1f1;font-size:.92rem;gap:14px;}
.info-row:last-child{border-bottom:none;}
.info-label{color:var(--muted);}
.info-value{color:var(--navy);font-weight:700;text-align:right;}

@media(max-width:900px){.region-header{grid-template-columns:1fr;gap:30px;}.info-card{margin-top:0;}}

/* ── REGION PACKS ── */
.region-packs{max-width:1240px;margin:24px auto 0;padding:0 20px;}
.region-packs__title{font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:800;color:var(--navy);margin:0 0 6px;}
.region-packs__sub{color:var(--muted);font-size:.92rem;margin:0 0 22px;}
.region-packs__grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(290px,1fr));gap:22px;}
.region-pack-card{background:#fff;border:1px solid var(--border);border-radius:16px;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 4px 18px rgba(17,17,17,.05);transition:transform .2s,box-shadow .2s;}
.region-pack-card:hover{transform:translateY(-4px);box-shadow:0 16px 36px rgba(17,17,17,.10);}
.region-pack-card__img{position:relative;height:170px;overflow:hidden;}
.region-pack-card__img img{width:100%;height:100%;object-fit:cover;display:block;}
.region-pack-card__badge{position:absolute;top:12px;left:12px;background:var(--coral);color:#fff;padding:5px 13px;border-radius:50px;font-size:11px;font-weight:800;letter-spacing:.6px;}
.region-pack-card__body{padding:18px 18px 0;flex:1;display:flex;flex-direction:column;}
.region-pack-card__title{font-family:'Playfair Display',serif;font-weight:700;font-size:1.1rem;color:var(--navy);margin:0 0 8px;}
.region-pack-card__slogan{color:#555;font-size:.86rem;line-height:1.55;margin:0 0 14px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.region-pack-card__foot{margin-top:auto;border-top:1px solid #f1f1f1;padding:14px 18px;display:flex;flex-direction:column;gap:10px;}
.region-pack-card__price{display:flex;align-items:baseline;gap:6px;font-size:.85rem;color:var(--muted);}
.region-pack-card__price strong{color:var(--navy);font-size:1.2rem;font-weight:800;}
.region-pack-card__price del{color:#bbb;font-size:.85rem;}
.region-pack-card__cta{display:inline-flex;align-items:center;justify-content:center;gap:6px;background:var(--coral);color:#fff;text-decoration:none;padding:10px 14px;border-radius:50px;font-weight:700;font-size:.88rem;transition:background .2s;}
.region-pack-card__cta:hover{background:#d95716;color:#fff;}

/* ── SERVICES ── */
.services-section{max-width:1240px;margin:48px auto 0;padding:0 20px 80px;}
.services-heading{font-family:'Playfair Display',serif;font-size:2rem;font-weight:800;margin:0 0 22px;color:var(--navy);}
.services-pills{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:14px;}
.svc-pill{padding:8px 18px;border-radius:50px;background:#fff;border:1.5px solid var(--border);color:#333;font-size:.9rem;font-weight:600;cursor:pointer;transition:all .2s;font-family:inherit;}
.svc-pill:hover{border-color:var(--navy);color:var(--navy);}
.svc-pill.active{background:var(--navy);color:#fff;border-color:var(--navy);}
.svc-pill .pill-count{opacity:.7;margin-left:4px;font-weight:600;}
.svc-pill.active .pill-count{opacity:.9;}
.services-sub{color:var(--muted);font-size:.92rem;margin:0 0 26px;}

.services-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(290px,1fr));gap:22px;}
.svc-card{background:#fff;border:1px solid var(--border);border-radius:16px;overflow:hidden;display:flex;flex-direction:column;text-decoration:none;color:inherit;transition:transform .2s,box-shadow .2s;}
.svc-card:hover{transform:translateY(-4px);box-shadow:0 16px 36px rgba(17,17,17,.10);color:inherit;}
.svc-img{position:relative;height:185px;overflow:hidden;}
.svc-img img{width:100%;height:100%;object-fit:cover;display:block;}
.svc-badge{position:absolute;top:12px;left:12px;background:rgba(255,255,255,.96);color:var(--coral);padding:5px 13px;border-radius:50px;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.6px;box-shadow:0 2px 8px rgba(0,0,0,.08);}
.svc-rating{position:absolute;top:12px;right:12px;background:#fff;padding:5px 11px;border-radius:50px;font-size:12px;font-weight:700;color:var(--navy);display:inline-flex;align-items:center;gap:5px;box-shadow:0 2px 8px rgba(0,0,0,.08);}
.svc-rating .star{color:var(--coral);}
.svc-body{padding:16px 18px 0;flex:1;display:flex;flex-direction:column;}
.svc-title{font-weight:700;font-size:1rem;margin:0 0 8px;color:var(--navy);line-height:1.3;}
.svc-desc{color:#666;font-size:.86rem;line-height:1.55;margin:0 0 14px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.svc-footer{margin-top:auto;border-top:1px solid #f1f1f1;padding:12px 18px;display:flex;align-items:center;justify-content:space-between;gap:8px;font-size:.85rem;color:var(--muted);}
.svc-price{font-size:1.05rem;font-weight:800;color:var(--navy);}
.svc-price small{font-weight:500;color:var(--muted);font-size:.78rem;margin-left:2px;}
.svc-cap{display:inline-flex;align-items:center;gap:5px;font-weight:600;color:var(--muted);}

.svc-empty{grid-column:1/-1;text-align:center;padding:80px 20px;color:var(--muted);font-size:.95rem;}

@media(max-width:880px){.services-section{padding-bottom:60px;}.region-header{margin:32px auto;}}
</style>
</head>
<body>

<?php $navLight = true; include 'navbar.php'; ?>
<link rel="stylesheet" href="assets/css/service-page.css">

<button onclick="history.back()" class="tk-back-fab">&#8592; <?= htmlspecialchars($L['back']) ?></button>

<!-- HERO IMAGE -->
<section class="region-hero">
  <div class="region-hero__wrap">
    <img src="<?= htmlspecialchars($mainPhoto) ?>" alt="<?= htmlspecialchars($regionNom, ENT_QUOTES, 'UTF-8') ?>" onerror="this.src='<?= htmlspecialchars($fallbackImg) ?>'">
    <span class="region-hero__label"><?= htmlspecialchars($regionNom, ENT_QUOTES, 'UTF-8') ?></span>
  </div>
</section>

<!-- REGION HEADER -->
<section class="region-header">
  <div>
    <div class="region-tag"><?= htmlspecialchars($L['tag_label']) ?></div>
    <h1 class="region-name"><?= htmlspecialchars($regionNom, ENT_QUOTES, 'UTF-8') ?></h1>
    <div class="region-desc">
      <?php
      $desc = trim((string) ($region['description'] ?? ''));
      if ($desc === '') {
          echo '<p>' . htmlspecialchars($L['no_desc']) . '</p>';
      } else {
          $paras = preg_split('/\n\s*\n+/', $desc);
          foreach ($paras as $p) {
              $p = trim($p);
              if ($p === '') continue;
              echo '<p>' . nl2br(htmlspecialchars($p)) . '</p>';
          }
      }
      ?>
    </div>
  </div>

  <aside class="info-card">
    <h3><?= htmlspecialchars($L['a_savoir']) ?></h3>
    <?php if (!empty($region['meilleure_saison'])): ?>
      <div class="info-row"><span class="info-label"><?= htmlspecialchars($L['best_season']) ?></span><span class="info-value"><?= htmlspecialchars($region['meilleure_saison']) ?></span></div>
    <?php endif; ?>
    <?php if (!empty($region['langues'])): ?>
      <div class="info-row"><span class="info-label"><?= htmlspecialchars($L['languages']) ?></span><span class="info-value"><?= htmlspecialchars($region['langues']) ?></span></div>
    <?php endif; ?>
    <?php if (!empty($region['monnaie'])): ?>
      <div class="info-row"><span class="info-label"><?= htmlspecialchars($L['currency']) ?></span><span class="info-value"><?= htmlspecialchars($region['monnaie']) ?></span></div>
    <?php endif; ?>
    <div class="info-row"><span class="info-label"><?= htmlspecialchars($L['services_count']) ?></span><span class="info-value"><?= (int) $total ?></span></div>
  </aside>
</section>

<?php if (!empty($regionPacks)): ?>
<!-- PACKS DISPONIBLES DANS LA RÉGION -->
<section class="region-packs">
  <h2 class="region-packs__title"><?= htmlspecialchars($L['packs_title']) ?></h2>
  <p class="region-packs__sub"><?= htmlspecialchars($L['packs_sub']) ?></p>
  <div class="region-packs__grid">
    <?php foreach ($regionPacks as $pk):
      $pkImg     = tk_pack_image((string) $pk['image_path'], $fallbackImg);
      $pkFinal   = (float) $pk['prix_final'];
      $pkOrig    = (float) $pk['prix_original'];
      $hasSaving = $pkOrig > $pkFinal && $pkOrig > 0;
      $saving    = $hasSaving ? (int) round((($pkOrig - $pkFinal) / $pkOrig) * 100) : 0;
    ?>
    <article class="region-pack-card">
      <div class="region-pack-card__img">
        <img src="<?= htmlspecialchars($pkImg) ?>" alt="<?= htmlspecialchars($pk['titre']) ?>" loading="lazy" onerror="this.src='<?= htmlspecialchars($fallbackImg) ?>'">
        <?php if ($hasSaving): ?>
          <span class="region-pack-card__badge">-<?= $saving ?>%</span>
        <?php endif; ?>
      </div>
      <div class="region-pack-card__body">
        <h3 class="region-pack-card__title"><?= htmlspecialchars($pk['titre']) ?></h3>
        <?php if (!empty($pk['slogan'])): ?>
          <p class="region-pack-card__slogan"><?= htmlspecialchars($pk['slogan']) ?></p>
        <?php endif; ?>
      </div>
      <div class="region-pack-card__foot">
        <div class="region-pack-card__price">
          <?= htmlspecialchars($L['pack_from']) ?>&nbsp;<strong><?= number_format($pkFinal, 0) ?> TND</strong>
          <?php if ($hasSaving): ?>
            <del><?= number_format($pkOrig, 0) ?> TND</del>
          <?php endif; ?>
        </div>
        <a class="region-pack-card__cta" href="forfait.php?id=<?= (int) $pk['id'] ?>">
          <?= htmlspecialchars($L['pack_reserve']) ?> →
        </a>
      </div>
    </article>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- SERVICES -->
<section class="services-section">
  <h2 class="services-heading"><?= htmlspecialchars($L['services_title']) ?></h2>

  <div class="services-pills">
    <button class="svc-pill active" type="button" data-cat="all"><?= htmlspecialchars($L['pill_all']) ?><span class="pill-count">(<?= (int) $total ?>)</span></button>
    <?php foreach ($categories as $cat):
        if ($counts[$cat] === 0) continue; ?>
      <button class="svc-pill" type="button" data-cat="<?= $cat ?>"><?= htmlspecialchars($catMeta[$cat]['pill']) ?><span class="pill-count">(<?= (int) $counts[$cat] ?>)</span></button>
    <?php endforeach; ?>
  </div>
  <p class="services-sub"><?= htmlspecialchars($L['filter_hint']) ?></p>

  <div class="services-grid" id="servicesGrid">
    <?php if ($total === 0): ?>
      <div class="svc-empty"><?= htmlspecialchars($L['none_in_region']) ?></div>
    <?php else: ?>
      <?php foreach ($categories as $cat):
        if ($counts[$cat] === 0) continue;
        foreach ($items_by_cat[$cat] as $item):
            $img = tk_service_image($item['photo_principale'] ?? '', $cat, $svcFallback);
            $title = $item['titre'];
            $description = trim((string) ($item['description'] ?? ''));
            if ($description === '') $description = $L['no_desc'];
            [$rating, $nbAvis] = tk_stable_rating((int) $item['id']);
            $cap = (int) ($item['capacite'] ?? 1);
            $capWord = ($cat === 'artisanat') ? $L['in_stock'] : $L['cap_max'];
            $unit = $catMeta[$cat]['unit'];
            $badge = $catMeta[$cat]['badge'];
      ?>
      <a href="<?= htmlspecialchars($cat) ?>.php?id=<?= (int) $item['id'] ?>" class="svc-card" data-cat="<?= $cat ?>">
        <div class="svc-img">
          <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>" loading="lazy" onerror="this.src='<?= htmlspecialchars($svcFallback) ?>'">
          <span class="svc-badge"><?= htmlspecialchars($badge) ?></span>
          <span class="svc-rating"><span class="star">★</span><?= $rating ?> <span style="color:#888;font-weight:500;">(<?= $nbAvis ?>)</span></span>
          <?php $isFav = in_array((int) $item['id'], $userFavs[$cat] ?? [], true); ?>
          <button type="button" class="fav-btn <?= $isFav ? 'is-fav' : '' ?>"
                  data-type="<?= htmlspecialchars($cat) ?>" data-id="<?= (int) $item['id'] ?>"
                  data-logged="<?= $isLogged ? '1' : '0' ?>"
                  title="<?= $isFav ? 'Retirer des favoris' : 'Ajouter aux favoris' ?>" aria-label="Favori">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21s-7-4.534-7-10a4.5 4.5 0 0 1 8-2.83A4.5 4.5 0 0 1 19 11c0 5.466-7 10-7 10z"/></svg>
          </button>
        </div>
        <div class="svc-body">
          <p class="svc-title"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></p>
          <p class="svc-desc"><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div class="svc-footer">
          <span class="svc-price"><?= number_format((float) $item['prix'], 0) ?> TND <small>/<?= htmlspecialchars($unit) ?></small></span>
          <span class="svc-cap"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> <?= $cap ?> <?= htmlspecialchars($capWord) ?></span>
        </div>
      </a>
      <?php endforeach;
      endforeach; ?>
    <?php endif; ?>
  </div>
</section>

<?php include 'footer.php'; ?>

<script>
(function(){
  const pills = document.querySelectorAll('.svc-pill');
  const cards = document.querySelectorAll('#servicesGrid .svc-card');
  pills.forEach(p => p.addEventListener('click', () => {
    pills.forEach(o => o.classList.remove('active'));
    p.classList.add('active');
    const cat = p.dataset.cat;
    cards.forEach(c => { c.style.display = (cat === 'all' || c.dataset.cat === cat) ? '' : 'none'; });
  }));
})();
</script>
</body>
</html>
