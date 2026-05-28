<?php
session_start();
require 'db.php';
require_once __DIR__ . '/includes/i18n.php';
mysqli_set_charset($conn, 'utf8mb4');

$L_ALL = [
    'fr' => [
        'page_title'    => 'Découvrir la Tunisie — Tarkina',
        'meta_desc'     => 'Découvrez les régions authentiques de la Tunisie : carte interactive, histoire, hébergements et expériences locales.',
        'hero_tag'      => 'Tunisie authentique',
        'hero_h1'       => 'Découvrir la Tunisie',
        'hero_sub'      => 'Explorez les régions de Tunisie sur la carte, plongez dans leur histoire et trouvez votre prochaine escapade authentique.',
        'section_title' => 'Nos régions',
        'section_sub'   => 'Chaque région a son histoire, ses paysages et ses trésors à découvrir.',
        'discover'      => 'Découvrir →',
        'osm_attr'      => '© contributeurs d\'OpenStreetMap',
    ],
    'ar' => [
        'page_title'    => 'اكتشف تونس — تاركينا',
        'meta_desc'     => 'اكتشف جهات تونس الأصيلة: خريطة تفاعلية، تاريخ، إقامات وتجارب محلية.',
        'hero_tag'      => 'تونس الأصيلة',
        'hero_h1'       => 'اكتشف تونس',
        'hero_sub'      => 'استكشف جهات تونس على الخريطة، انغمس في تاريخها واعثر على وجهتك الأصيلة القادمة.',
        'section_title' => 'جهاتنا',
        'section_sub'   => 'لكلّ جهة قصّتها ومناظرها وكنوزها التي تستحقّ الاكتشاف.',
        'discover'      => 'اكتشف ←',
        'osm_attr'      => '© مساهمو OpenStreetMap',
    ],
    'en' => [
        'page_title'    => 'Discover Tunisia — Tarkina',
        'meta_desc'     => 'Discover the authentic regions of Tunisia: interactive map, history, accommodations and local experiences.',
        'hero_tag'      => 'Authentic Tunisia',
        'hero_h1'       => 'Discover Tunisia',
        'hero_sub'      => 'Explore the regions of Tunisia on the map, dive into their history and find your next authentic getaway.',
        'section_title' => 'Our regions',
        'section_sub'   => 'Each region has its own history, landscapes and treasures to discover.',
        'discover'      => 'Discover →',
        'osm_attr'      => '© OpenStreetMap contributors',
    ],
];
$L = $L_ALL[$lang] ?? $L_ALL['fr'];

// --- Read regions (read-only) ---
$regions_query = mysqli_query($conn, "SELECT id, nom, description, photo_principale, photo FROM region ORDER BY nom");
$regions = [];
while ($r = mysqli_fetch_assoc($regions_query)) {
    $regions[] = $r;
}

// --- Hardcoded coordinates per region name (NOT stored in DB) ---
$region_coords = [
    'Tunis'         => [36.8065, 10.1815],
    'Sidi Bou Saïd' => [36.8704, 10.3417],
    'Djerba'        => [33.8076, 10.8451],
    'Kairouan'      => [35.6781, 10.0963],
    'Tozeur'        => [33.9197, 8.1335],
    'Takrouna'      => [36.3100, 10.3000],
    'Kessra'        => [35.8100, 9.3600],
    'Chenini'       => [32.9100, 10.2700],
];

// --- Per-region fallback images (varied Unsplash Tunisia photos) ---
$fallback_default = 'https://images.unsplash.com/photo-1548013146-72479768bada?w=800&q=80';
$fallback_by_name = [
    'Sidi Bou Saïd' => 'https://images.unsplash.com/photo-1605649487212-47bdab064df7?w=800&q=80',
    'Djerba'        => 'https://images.unsplash.com/photo-1590523741831-ab7e8b8f9c7f?w=800&q=80',
    'Kairouan'      => 'https://images.unsplash.com/photo-1612285342285-3e2e94c1d8c5?w=800&q=80',
    'Tozeur'        => 'https://images.unsplash.com/photo-1518709594023-6eab9bab7b23?w=800&q=80',
    'Takrouna'      => 'https://images.unsplash.com/photo-1559564484-0e7b54e3a0f4?w=800&q=80',
    'Kessra'        => 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=800&q=80',
    'Chenini'       => 'https://images.unsplash.com/photo-1547471080-7cc2caa01a7e?w=800&q=80',
];

/**
 * Resolve the best photo URL for a region.
 * Priority:
 *  1. photo_principale starting with http -> use as-is
 *  2. file exists at images/regions/<photo_principale> or uploads/regions/<photo_principale>
 *  3. existing 'photo' column file on disk (legacy assets)
 *  4. per-region Unsplash fallback (else generic Tunisia fallback)
 */
function region_photo(array $r, array $fallback_by_name, string $fallback_default): string
{
    $pp = trim((string)($r['photo_principale'] ?? ''));
    if ($pp !== '') {
        if (stripos($pp, 'http') === 0) {
            return $pp;
        }
        foreach (['images/regions/', 'uploads/regions/'] as $dir) {
            if (is_file(__DIR__ . '/' . $dir . $pp)) {
                return $dir . $pp;
            }
        }
    }

    // Legacy 'photo' column (e.g. assets/regions/xxx.jpg or images/regions/xxx.jpg)
    $photo = trim((string)($r['photo'] ?? ''));
    if ($photo !== '') {
        if (stripos($photo, 'http') === 0) {
            return $photo;
        }
        if (is_file(__DIR__ . '/' . ltrim($photo, '/'))) {
            return ltrim($photo, '/');
        }
        $base = basename($photo);
        foreach (['images/regions/', 'uploads/regions/', 'assets/regions/'] as $dir) {
            if (is_file(__DIR__ . '/' . $dir . $base)) {
                return $dir . $base;
            }
        }
    }

    return $fallback_by_name[$r['nom']] ?? $fallback_default;
}

/** Truncate a description to ~110 chars without cutting mid-word. */
function truncate_text(string $text, int $limit = 110): string
{
    $text = trim($text);
    if (mb_strlen($text) <= $limit) {
        return $text;
    }
    $cut = mb_substr($text, 0, $limit);
    $space = mb_strrpos($cut, ' ');
    if ($space !== false) {
        $cut = mb_substr($cut, 0, $space);
    }
    return $cut . '…';
}

// --- Build the JS payload: regions that have coordinates ---
$map_points = [];
foreach ($regions as $r) {
    if (isset($region_coords[$r['nom']])) {
        $map_points[] = [
            'id'   => (int)$r['id'],
            'nom'  => $r['nom'],
            'lat'  => $region_coords[$r['nom']][0],
            'lng'  => $region_coords[$r['nom']][1],
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>" dir="<?= htmlspecialchars($dir) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($L['page_title']) ?></title>
    <meta name="description" content="<?= htmlspecialchars($L['meta_desc']) ?>">

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Leaflet 1.9.4 -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Lato:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/rtl.css">

    <style>
        :root {
            --navy: #111111;
            --coral: #1B6B45;
            --cream: #FFFFFF;
            --border: #E5E7EB;
            --muted: #6B7280;
        }
        body { font-family: 'Lato', 'Segoe UI', sans-serif; background: var(--cream); color: var(--navy); }

        /* Page header */
        .discover-hero {
            background: linear-gradient(135deg, var(--navy) 0%, #163140 100%);
            color: #fff;
            padding: 80px 20px 70px;
            text-align: center;
            position: relative;
        }
        .discover-hero .tag {
            display: inline-block;
            background: rgba(27, 107, 69, 0.18);
            color: #ffd9c9;
            border: 1px solid rgba(27, 107, 69, 0.4);
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin-bottom: 18px;
        }
        .discover-hero h1 {
            font-family: 'Playfair Display', serif;
            font-weight: 800;
            font-size: clamp(2.2rem, 5vw, 3.4rem);
            margin: 0 0 14px;
        }
        .discover-hero p {
            color: rgba(255, 255, 255, 0.82);
            font-size: 1.1rem;
            max-width: 620px;
            margin: 0 auto;
        }

        /* Map */
        .map-section { max-width: 1200px; margin: -40px auto 0; padding: 0 16px; position: relative; z-index: 5; }
        #map {
            height: 460px;
            width: 100%;
            border-radius: 18px;
            box-shadow: 0 18px 50px rgba(17, 17, 17, 0.18);
            border: 4px solid #fff;
        }
        .leaflet-popup-content { font-family: 'Lato', sans-serif; }
        .popup-link {
            display: inline-block;
            margin-top: 6px;
            color: var(--coral);
            font-weight: 700;
            text-decoration: none;
        }
        .popup-link:hover { text-decoration: underline; }

        /* Region cards */
        .regions-section { max-width: 1200px; margin: 0 auto; padding: 64px 16px 80px; }
        .section-title {
            font-family: 'Playfair Display', serif;
            font-weight: 800;
            font-size: 2rem;
            color: var(--navy);
            text-align: center;
            margin-bottom: 8px;
        }
        .section-sub { text-align: center; color: var(--muted); margin-bottom: 40px; }

        .region-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: transform .25s ease, box-shadow .25s ease;
        }
        .region-card:hover { transform: translateY(-6px); box-shadow: 0 16px 40px rgba(17, 17, 17, 0.15); }
        .region-card .photo-wrap { position: relative; aspect-ratio: 16 / 10; overflow: hidden; }
        .region-card .photo-wrap img { width: 100%; height: 100%; object-fit: cover; transition: transform .4s ease; }
        .region-card:hover .photo-wrap img { transform: scale(1.06); }
        .region-card .badge-name {
            position: absolute; left: 12px; bottom: 12px;
            background: rgba(17, 17, 17, 0.85);
            color: #fff; padding: 5px 14px; border-radius: 50px;
            font-size: 0.8rem; font-weight: 700;
        }
        .region-card .card-content { padding: 20px; display: flex; flex-direction: column; flex: 1; }
        .region-card h3 {
            font-family: 'Playfair Display', serif;
            font-weight: 700; font-size: 1.3rem; color: var(--navy); margin: 0 0 10px;
        }
        .region-card p { color: var(--muted); font-size: 0.95rem; line-height: 1.5; flex: 1; margin: 0 0 18px; }
        .btn-discover {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            background: var(--coral); color: #fff; border: none;
            padding: 10px 22px; border-radius: 50px;
            font-weight: 700; font-size: 0.95rem; text-decoration: none;
            transition: background .2s ease;
            align-self: flex-start;
        }
        .btn-discover:hover { background: #c64a1f; color: #fff; }
        .fade-in-up { opacity: 0; transform: translateY(30px); transition: opacity 0.6s ease, transform 0.6s ease; }
        .fade-in-up.visible { opacity: 1; transform: translateY(0); }
    </style>
</head>
<body>

<!-- NAVBAR -->
<?php include 'navbar.php'; ?>

<!-- PAGE HEADER -->
<header class="discover-hero">
    <span class="tag"><?= htmlspecialchars($L['hero_tag']) ?></span>
    <h1><?= htmlspecialchars($L['hero_h1']) ?></h1>
    <p><?= htmlspecialchars($L['hero_sub']) ?></p>
</header>

<!-- INTERACTIVE MAP -->
<section class="map-section">
    <div id="map"></div>
</section>

<!-- REGION CARDS -->
<section class="regions-section">
    <h2 class="section-title"><?= htmlspecialchars($L['section_title']) ?></h2>
    <p class="section-sub"><?= htmlspecialchars($L['section_sub']) ?></p>

    <div class="row g-4">
        <?php foreach ($regions as $r):
            $name  = $r['nom'];
            $desc  = truncate_text((string)($r['description'] ?? ''), 110);
            $photo = region_photo($r, $fallback_by_name, $fallback_default);
        ?>
        <div class="col-12 col-sm-6 col-lg-4">
            <article class="region-card fade-in-up">
                <div class="photo-wrap">
                    <img src="<?= htmlspecialchars($photo) ?>" alt="<?= htmlspecialchars($name) ?>"
                         loading="lazy"
                         onerror="this.onerror=null;this.src='<?= htmlspecialchars($fallback_default) ?>';">
                    <span class="badge-name"><?= htmlspecialchars($name) ?></span>
                </div>
                <div class="card-content">
                    <h3><?= htmlspecialchars($name) ?></h3>
                    <p><?= htmlspecialchars($desc) ?></p>
                    <a href="region.php?id=<?= (int)$r['id'] ?>" class="btn-discover"><?= htmlspecialchars($L['discover']) ?></a>
                </div>
            </article>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- FOOTER -->
<?php include 'footer.php'; ?>

<script>
    const regionPoints = <?= json_encode($map_points, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;

    const map = L.map('map', {
        center: [34.0, 9.5],
        zoom: 6,
        scrollWheelZoom: false
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: <?= json_encode($L['osm_attr'], JSON_UNESCAPED_UNICODE) ?>
    }).addTo(map);

    const coralIcon = L.divIcon({
        className: '',
        html: '<div style="background:#1B6B45;width:16px;height:16px;border-radius:50%;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,0.35)"></div>',
        iconSize: [22, 22],
        iconAnchor: [11, 11],
        popupAnchor: [0, -12]
    });

    regionPoints.forEach(function (p) {
        L.marker([p.lat, p.lng], { icon: coralIcon })
            .addTo(map)
            .bindPopup(
                '<strong>' + p.nom + '</strong><br>' +
                '<a class="popup-link" href="region.php?id=' + p.id + '"><?= htmlspecialchars($L['discover'], ENT_QUOTES) ?></a>'
            );
    });

    // Re-enable scroll zoom once the user clicks the map
    map.on('click', function () { map.scrollWheelZoom.enable(); });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const observer = new IntersectionObserver((entries) => {
  entries.forEach(e => { if(e.isIntersecting) { e.target.classList.add('visible'); } });
}, { threshold: 0.1 });
document.querySelectorAll('.fade-in-up').forEach(el => observer.observe(el));
</script>
</body>
</html>

