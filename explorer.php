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

require_once __DIR__ . '/includes/region_photo.php';

/**
 * Resolve the best photo URL for a region.
 * Priority:
 *  1. photo_principale starting with http -> use as-is
 *  2. file exists at images/regions/<photo_principale> or uploads/regions/<photo_principale>
 *  3. JSON manifest (data/region_photos.json) — code-only fallback for admin
 *     uploads when the DB isn't synced across machines
 *  4. existing 'photo' column file on disk (legacy assets)
 *  5. per-region Unsplash fallback (else generic Tunisia fallback)
 */
function region_photo(array $r, array $fallback_by_name, string $fallback_default): string
{
    $pp = trim((string)($r['photo_principale'] ?? ''));
    if ($pp !== '') {
        if (stripos($pp, 'http') === 0) {
            return $pp;
        }
        // Path already relative-to-root (uploads/regions/x.jpg, images/regions/x.jpg, etc.) — use as-is if the file exists
        if (preg_match('#^(uploads|images|assets)/#', $pp) === 1) {
            if (is_file(__DIR__ . '/' . $pp)) {
                return $pp;
            }
            // Try basename in known dirs as a recovery
            $base = basename($pp);
            foreach (['uploads/regions/', 'images/regions/'] as $dir) {
                if (is_file(__DIR__ . '/' . $dir . $base)) {
                    return $dir . $base;
                }
            }
        } else {
            // Bare filename — try known dirs
            foreach (['uploads/regions/', 'images/regions/'] as $dir) {
                if (is_file(__DIR__ . '/' . $dir . $pp)) {
                    return $dir . $pp;
                }
            }
        }
    }

    // Manifest fallback (code-only, propagates admin uploads across machines)
    $rid = (int) ($r['id'] ?? 0);
    if ($rid > 0) {
        $manifest = region_photo_fallback($rid);
        if ($manifest !== '') {
            if (stripos($manifest, 'http') === 0) return $manifest;
            if (is_file(__DIR__ . '/' . ltrim($manifest, '/'))) return ltrim($manifest, '/');
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

// --- Build the JS payload for SVG mapping ---
$js_regions = [];
$mapping = [
    'Sidi Bou Saïd' => ['x' => 67.2, 'y' => 7.36, 'zone' => 'north'],
    'Djerba'        => ['x' => 70.4, 'y' => 51.53, 'zone' => 'south'],
    'Kairouan'      => ['x' => 59.2, 'y' => 23.52, 'zone' => 'center'],
    'Tozeur'        => ['x' => 9.6,  'y' => 48.06, 'zone' => 'south'],
    'Takrouna'      => ['x' => 60.0, 'y' => 17.38, 'zone' => 'center'],
    'Kessra'        => ['x' => 44.0, 'y' => 21.47, 'zone' => 'center'],
    'Chenini'       => ['x' => 66.4, 'y' => 78.32, 'zone' => 'south'],
];

foreach ($regions as $r) {
    $photo = region_photo($r, $fallback_by_name, $fallback_default);
    $name = $r['nom'];
    
    // Normalize name for clean mapping and UI display
    $normalized_name = $name;
    if (stripos($name, 'Sidi') !== false) {
        $normalized_name = 'Sidi Bou Saïd';
    }
    
    $desc = trim((string)($r['description'] ?? ''));
    
    if (isset($mapping[$normalized_name])) {
        $js_regions[$r['id']] = [
            'id'          => (int)$r['id'],
            'nom'         => $normalized_name,
            'description' => $desc,
            'photo'       => $photo,
            'x'           => $mapping[$normalized_name]['x'],
            'y'           => $mapping[$normalized_name]['y'],
            'zone'        => $mapping[$normalized_name]['zone']
        ];
    } else {
        $lat = isset($r['latitude']) ? (float)$r['latitude'] : 0;
        $lng = isset($r['longitude']) ? (float)$r['longitude'] : 0;
        if ($lat > 0 && $lng > 0) {
            $minLng = 7.2; $maxLng = 11.8;
            $minLat = 30.0; $maxLat = 37.6;
            $pctX = (($lng - $minLng) / ($maxLng - $minLng)) * 100;
            $pctY = (($maxLat - $lat) / (maxLat - minLat)) * 100;
            
            $zone = 'center';
            if ($lat > 36.0) {
                $zone = 'north';
            } elseif ($lat < 34.0) {
                $zone = 'south';
            }
            
            $js_regions[$r['id']] = [
                'id'          => (int)$r['id'],
                'nom'         => $normalized_name,
                'description' => $desc,
                'photo'       => $photo,
                'x'           => round($pctX, 2),
                'y'           => round($pctY, 2),
                'zone'        => $zone
            ];
        }
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
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800;900&family=Lato:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/rtl.css">

    <style>
        :root {
            --navy: #0b1c30;
            --coral: #f16e22;
            --cream: #ffffff;
        }

        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            overflow-x: hidden;
            scroll-behavior: smooth;
            font-family: 'Lato', 'Segoe UI', sans-serif;
            background-color: var(--navy);
        }

        .discover-page-wrapper {
            position: relative;
            width: 100%;
        }

        /* Navbar glassmorphism overrides */
        .tk-nav {
            background: rgba(11, 28, 48, 0.4) !important;
            backdrop-filter: blur(10px) !important;
            -webkit-backdrop-filter: blur(10px) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15) !important;
            box-shadow: none !important;
        }
        .tk-nav__logo, .tk-nav-link, .tk-icon-link {
            color: #ffffff !important;
        }
        .tk-nav-link.active {
            color: var(--coral) !important;
        }
        .tk-btn--outline {
            border-color: #ffffff !important;
            color: #ffffff !important;
        }
        .tk-btn--outline:hover {
            background: #ffffff !important;
            color: var(--navy) !important;
        }
        .tk-nav-spacer {
            display: none !important;
        }

        /* Fixed map wrapper on the side */
        .fixed-map-wrapper {
            position: fixed;
            top: 150px;
            inset-inline-end: 80px;
            width: 320px;
            height: calc(100vh - 200px);
            z-index: 10;
            display: flex;
            justify-content: center;
            align-items: center;
            pointer-events: none; /* Let background content be clickable if needed */
        }
        .map-container-inner {
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }
        .svg-map-wrapper {
            position: relative;
            width: auto;
            height: 100%;
            max-height: 520px;
            aspect-ratio: 250 / 489;
            pointer-events: auto; /* Re-enable clicks inside map */
        }

        /* SVG outlines */
        #tunisia-svg {
            width: 100%;
            height: 100%;
            display: block;
        }
        .map-zone {
            transition: opacity 0.5s ease;
            cursor: pointer;
        }
        .map-zone.faded-out {
            opacity: 0.25;
        }
        .map-zone.faded-out .gov-poly {
            fill: #f3f4f6 !important;
            opacity: 0.8;
        }
        #zone-north .gov-poly {
            fill: #82C842;
            transition: fill 0.3s ease;
        }
        #zone-center .gov-poly {
            fill: #f16e22;
            transition: fill 0.3s ease;
        }
        #zone-south .gov-poly {
            fill: #0091ea;
            transition: fill 0.3s ease;
        }
        .gov-poly {
            stroke: #ffffff;
            stroke-width: 0.8px;
            transition: stroke-width 0.2s;
        }
        .gov-poly:hover {
            stroke-width: 1.5px;
        }

        /* Absolute pins overlay */
        #pins-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }
        .map-pin {
            position: absolute;
            width: 24px;
            height: 24px;
            transform: translate(-50%, -50%);
            cursor: pointer;
            z-index: 15;
            pointer-events: auto;
            transition: transform 0.3s ease, filter 0.3s ease;
        }
        .map-pin::before {
            content: '';
            display: block;
            width: 16px;
            height: 16px;
            background-color: #ffffff;
            border: 3.5px solid #ffffff;
            border-radius: 50% 50% 50% 0;
            transform: rotate(-45deg);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.4);
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
        .map-pin:hover {
            transform: translate(-50%, -50%) scale(1.25);
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3));
        }
        .map-pin.active::before {
            background-color: var(--coral);
            border-color: #ffffff;
        }
        .pin-label {
            position: absolute;
            bottom: 26px;
            left: 50%;
            transform: translateX(-50%) translateY(5px);
            background-color: rgba(11, 28, 48, 0.9);
            color: #ffffff;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 700;
            white-space: nowrap;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, transform 0.3s ease, visibility 0.3s;
            pointer-events: none;
        }
        .map-pin:hover .pin-label,
        .map-pin.active .pin-label {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(0);
        }

        /* Scrollable Full-height Sections */
        .sections-container {
            width: 100%;
        }
        .region-section {
            position: relative;
            width: 100%;
            height: 100vh;
            background-size: cover;
            background-position: center;
            background-attachment: fixed; /* Parallax transitions */
            display: flex;
            flex-direction: column;
        }
        .section-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at center, rgba(11, 28, 48, 0.4) 0%, rgba(11, 28, 48, 0.85) 100%);
            z-index: 1;
        }

        /* Header bar elements inside first section */
        .overview-section .discover-header {
            position: relative;
            margin-top: 100px;
            padding: 0 60px;
            width: 100%;
            max-width: 1200px;
            margin-inline-start: 60px;
            z-index: 10;
        }
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: 8px;
        }
        .header-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            font-weight: 800;
            color: #ffffff;
            margin: 0;
        }
        .header-breadcrumbs {
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.85);
        }
        .header-breadcrumbs a {
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            transition: color 0.2s;
        }
        .header-breadcrumbs a:hover {
            color: var(--coral);
        }
        .header-line {
            height: 6px;
            background-color: var(--coral);
            width: 100%;
            border-radius: 3px;
        }

        /* Content block positioning */
        .section-content-wrapper {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 1200px;
            height: 100%;
            margin-inline-start: 60px;
            display: flex;
            align-items: center;
        }
        .overview-section .section-content-wrapper {
            flex: 1;
        }

        /* Left-floating details card */
        .details-card {
            background: rgba(11, 28, 48, 0.65);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            padding: 40px;
            max-width: 500px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            color: #ffffff;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }
        .details-card h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 800;
            letter-spacing: 1px;
            margin: 0 0 15px 0;
            text-transform: uppercase;
        }
        .card-title-line {
            height: 3px;
            background-color: var(--coral);
            width: 60px;
            margin-block-end: 25px;
            margin-inline-start: 0;
            margin-inline-end: auto;
        }
        .details-card p {
            font-size: 1rem;
            line-height: 1.8;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 30px;
        }
        .btn-learn-more {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background-color: var(--coral);
            color: #ffffff;
            padding: 12px 28px;
            border-radius: 50px;
            font-weight: 700;
            text-decoration: none;
            transition: background-color 0.3s, transform 0.2s;
        }
        .btn-learn-more:hover {
            background-color: #d95716;
            color: #ffffff;
            transform: translateY(-2px);
        }
        .btn-learn-more .arrow {
            transition: transform 0.2s;
        }
        .btn-learn-more:hover .arrow {
            transform: translateX(4px);
        }

        /* RTL support offsets */
        html[dir="rtl"] .card-title-line {
            margin-inline-start: auto;
            margin-inline-end: 0;
        }
        html[dir="rtl"] .overview-section .discover-header,
        html[dir="rtl"] .section-content-wrapper {
            margin-inline-start: auto;
            margin-inline-end: 60px;
        }

        /* Responsiveness modifications */
        @media (max-width: 1199px) {
            .fixed-map-wrapper {
                inset-inline-end: 40px;
                width: 280px;
            }
            .details-card {
                padding: 30px;
                max-width: 440px;
            }
            .details-card h2 {
                font-size: 2.1rem;
            }
            .details-card p {
                font-size: 0.95rem;
                line-height: 1.7;
            }
        }

        @media (max-width: 991px) {
            .region-section {
                height: 100vh;
                background-attachment: scroll;
            }
            .overview-section .discover-header {
                padding: 0 20px;
                margin-inline-start: 0;
                margin-top: 90px;
            }
            .section-content-wrapper {
                margin-inline-start: 0;
                padding: 0 20px;
                justify-content: center;
            }
            html[dir="rtl"] .overview-section .discover-header,
            html[dir="rtl"] .section-content-wrapper {
                margin-inline-end: 0;
                padding: 0 20px;
                justify-content: center;
            }
            .details-card {
                max-width: 100%;
                width: 100%;
            }
            .fixed-map-wrapper {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<div class="discover-page-wrapper">
    <!-- NAVBAR -->
    <?php include 'navbar.php'; ?>

    <!-- FIXED MAP COLUMN -->
    <div class="fixed-map-wrapper">
        <div class="map-container-inner">
            <div class="svg-map-wrapper">
                <!-- SVG Outline Map of Tunisia -->
                <svg id="tunisia-svg" viewBox="0 0 250 489">
                    <!-- North Region -->
                    <g id="zone-north" class="map-zone" data-zone="north">
                        <polygon class="gov-poly" data-gov="Ariana" points="161,21 155,22 148,34 147,38 152,41 168,36 158,27" />
                        <polygon class="gov-poly" data-gov="Béja" points="98,17 92,20 88,27 98,59 95,68 100,72 109,71 119,66 125,69 134,66 141,59 137,51 125,40 117,46 103,27 98,24" />
                        <polygon class="gov-poly" data-gov="Ben Arous" points="163,44 173,48 168,65 164,67 156,57 147,53 160,49" />
                        <polygon class="gov-poly" data-gov="Bizerte" points="99,17 130,6 140,7 143,13 153,11 164,17 157,21 148,33 140,31 124,39 117,45 103,26 98,24" />
                        <polygon class="gov-poly" data-gov="Jendouba" points="58,65 58,73 70,69 85,70 92,65 95,66 98,59 88,27 83,31 72,32 73,40 64,46 63,52 48,59 48,64" />
                        <polygon class="gov-poly" data-gov="Le Kef" points="58,73 55,78 52,113 56,121 63,118 69,120 73,119 74,110 84,112 94,117 96,104 103,94 95,81 90,67 84,70 70,69" />
                        <polygon class="gov-poly" data-gov="La Manouba" points="125,41 136,49 141,59 148,52 153,50 149,43 152,41 146,38 148,33 140,31 126,38" />
                        <polygon class="gov-poly" data-gov="Nabeul" points="173,48 180,45 181,37 207,22 212,36 199,51 194,66 179,72 169,71 168,64" />
                        <polygon class="gov-poly" data-gov="Siliana" points="89,67 95,83 103,94 95,106 93,118 112,131 119,130 107,115 117,116 122,113 120,101 135,91 135,84 128,83 125,78 132,76 135,69 134,66 124,69 119,65 108,72 99,72 95,68 94,65" />
                        <polygon class="gov-poly" data-gov="Tunis" points="168,36 170,38 165,45 163,44 160,49 153,51 149,43 152,41" />
                        <polygon class="gov-poly" data-gov="Zaghouan" points="147,53 134,66 135,69 132,76 125,78 128,83 135,84 135,91 144,91 151,96 170,79 170,72 168,65 164,67 156,57" />
                    </g>
                    <!-- Center Region -->
                    <g id="zone-center" class="map-zone" data-zone="center">
                        <polygon class="gov-poly" data-gov="Kairouan" points="162,153 159,139 166,124 158,107 164,96 159,88 151,96 144,91 134,91 120,102 121,114 116,116 107,115 121,133 121,141 140,154 148,153 151,164 158,154" />
                        <polygon class="gov-poly" data-gov="Kasserine" points="56,121 55,139 59,151 54,160 52,180 68,185 83,180 93,175 99,164 108,160 108,150 101,146 103,140 112,132 109,128 93,117 88,116 80,111 73,111 72,120 67,120 63,118" />
                        <polygon class="gov-poly" data-gov="Mahdia" points="205,126 194,133 169,127 166,124 159,138 162,153 172,157 182,151 183,147 195,150 206,158 213,147 206,142 209,130" />
                        <polygon class="gov-poly" data-gov="Monastir" points="188,110 183,114 186,120 178,129 194,133 205,126 208,120 197,117" />
                        <polygon class="gov-poly" data-gov="Sfax" points="217,175 222,176 222,181 212,186 211,185 217,175 201,168 190,187 170,203 150,219 145,220 135,215 134,206 152,198 151,192 138,190 151,164 158,154 162,153 172,157 182,152 183,147 195,150 206,158 201,168" />
                        <polygon class="gov-poly" data-gov="Sidi Bouzid" points="119,130 111,131 101,146 108,151 108,160 98,165 93,175 83,180 107,187 110,196 125,206 124,214 129,212 135,215 134,206 152,198 151,192 137,190 151,164 148,153 140,154 121,141 121,133" />
                        <polygon class="gov-poly" data-gov="Sousse" points="179,71 175,82 176,95 188,111 183,114 186,120 178,129 170,127 166,124 158,106 164,96 159,88 170,79 170,71" />
                    </g>
                    <!-- South Region -->
                    <g id="zone-south" class="map-zone" data-zone="south">
                        <polygon class="gov-poly" data-gov="Gabès" points="149,218 145,220 129,211 120,216 109,216 108,229 113,247 126,257 125,266 136,279 151,276 162,269 174,254 162,247 151,232" />
                        <polygon class="gov-poly" data-gov="Gafsa" points="53,180 67,186 83,181 107,186 110,195 125,207 124,213 119,216 96,218 93,224 57,224 54,218 51,218 39,210 44,204 44,195 50,186" />
                        <polygon class="gov-poly" data-gov="Kébili" points="22,269 22,283 42,291 54,310 57,331 92,321 105,321 124,316 129,311 136,311 145,322 149,322 143,312 149,299 144,295 145,284 135,279 124,265 125,258 113,247 107,229 109,217 96,218 94,224 74,224 73,230 51,250 29,268" />
                        <polygon class="gov-poly" data-gov="Médenine" points="174,254 178,255 188,250 190,236 202,238 210,244 207,256 213,262 211,278 219,285 236,286 231,324 238,331 237,337 216,348 225,331 211,320 207,295 191,287 183,280 157,287 153,292 144,293 146,284 135,280 151,276 164,267" />
                        <polygon class="gov-poly" data-gov="Tataouine" points="57,331 97,359 123,484 138,478 143,477 167,441 156,404 166,383 173,383 177,385 180,380 199,356 216,348 225,331 210,319 207,295 191,287 183,280 165,285 157,287 154,292 144,293 148,299 143,312 149,322 144,322 136,311 128,311 124,316 105,321 90,321" />
                        <polygon class="gov-poly" data-gov="Tozeur" points="44,194 44,204 39,210 49,218 54,218 58,224 74,224 72,232 48,252 28,269 21,269 12,251 8,238 10,223 18,216 25,215 27,203" />
                    </g>
                </svg>

                <!-- Interactive Pin Overlay -->
                <div id="pins-container">
                    <?php foreach ($js_regions as $id => $r): ?>
                    <div class="map-pin" data-id="<?= $id ?>" data-zone="<?= $r['zone'] ?>" style="left: <?= $r['x'] ?>%; top: <?= $r['y'] ?>%;">
                        <div class="pin-label"><?= htmlspecialchars($r['nom']) ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- SCROLLABLE SECTIONS CONTAINER -->
    <div class="sections-container">
        <!-- Overview / Tunisia Intro Section -->
        <section id="overview-section" class="region-section overview-section" style="background-image: url('images/hero-tunisia.jpg');">
            <div class="section-overlay"></div>
            
            <div class="discover-header">
                <div class="header-top">
                    <h1 class="header-title">Découvrir</h1>
                    <div class="header-breadcrumbs">
                        <a href="index.php">Accueil</a> / <span>Découvrir</span>
                    </div>
                </div>
                <div class="header-line"></div>
            </div>
            
            <div class="section-content-wrapper">
                <div class="details-card">
                    <h2>TUNISIE</h2>
                    <div class="card-title-line"></div>
                    <p>
                        Il y a tant de choses à découvrir dans chaque région de la Tunisie : des paysages contrastés, un littoral long de 1250 km parsemé d'îles et d'archipels, des traditions et coutumes diverses, un riche héritage historique. Nous vous présentons ces régions en les répartissant, par commodité, en trois zones : le nord, le centre et le sud.
                    </p>
                </div>
            </div>
        </section>

        <!-- Dynamic Region Sections -->
        <?php foreach ($js_regions as $id => $r): ?>
        <section id="region-<?= $id ?>" class="region-section" data-id="<?= $id ?>" data-zone="<?= $r['zone'] ?>" style="background-image: url('<?= htmlspecialchars($r['photo']) ?>');">
            <div class="section-overlay"></div>
            
            <div class="section-content-wrapper">
                <div class="details-card">
                    <h2><?= htmlspecialchars($r['nom']) ?></h2>
                    <div class="card-title-line"></div>
                    <p><?= htmlspecialchars($r['description']) ?></p>
                    <a href="region.php?id=<?= $id ?>" class="btn-learn-more">
                        En savoir plus <span class="arrow">➔</span>
                    </a>
                </div>
            </div>
        </section>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const regionsData = <?= json_encode($js_regions, JSON_UNESCAPED_UNICODE) ?>;

    // DOM Elements
    const pins = document.querySelectorAll('.map-pin');
    const zones = document.querySelectorAll('.map-zone');
    const sections = document.querySelectorAll('.region-section[data-id]');
    const overviewSection = document.getElementById('overview-section');

    let activeRegionId = null;
    let isScrollingFromClick = false;

    /* ── Highlight the map for a given region (or reset for overview) ── */
    function highlightMap(regionId) {
        if (regionId === activeRegionId) return;
        activeRegionId = regionId;

        if (regionId === null) {
            // Overview — show full map, no active pin
            zones.forEach(z => z.classList.remove('faded-out'));
            pins.forEach(p => p.classList.remove('active'));
            return;
        }

        const r = regionsData[regionId];
        if (!r) return;

        // Highlight active zone, dim others
        zones.forEach(z => {
            z.classList.toggle('faded-out', z.dataset.zone !== r.zone);
        });

        // Set active pin
        pins.forEach(p => {
            p.classList.toggle('active', p.dataset.id == regionId);
        });
    }

    /* ── IntersectionObserver — sync map as user scrolls ── */
    const observerOptions = { root: null, rootMargin: '-40% 0px -40% 0px', threshold: 0 };

    const sectionObserver = new IntersectionObserver((entries) => {
        if (isScrollingFromClick) return; // Don't fight programmatic scrolls

        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const id = entry.target.dataset.id;
                if (id) {
                    highlightMap(id);
                } else if (entry.target.id === 'overview-section') {
                    highlightMap(null);
                }
            }
        });
    }, observerOptions);

    // Observe overview section
    if (overviewSection) sectionObserver.observe(overviewSection);
    // Observe each region section
    sections.forEach(sec => sectionObserver.observe(sec));

    /* ── Click a pin → scroll to its region section ── */
    pins.forEach(pin => {
        pin.addEventListener('click', (e) => {
            e.stopPropagation();
            const id = pin.dataset.id;
            const target = document.getElementById('region-' + id);
            if (!target) return;

            isScrollingFromClick = true;
            highlightMap(id);
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });

            // Release lock after scroll settles
            setTimeout(() => { isScrollingFromClick = false; }, 900);
        });
    });

    /* ── Click a zone → scroll to the first region in that zone ── */
    zones.forEach(zone => {
        zone.addEventListener('click', () => {
            const zoneName = zone.dataset.zone;
            const match = Object.values(regionsData).find(r => r.zone === zoneName);
            if (!match) return;

            const target = document.getElementById('region-' + match.id);
            if (!target) return;

            isScrollingFromClick = true;
            highlightMap(match.id);
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            setTimeout(() => { isScrollingFromClick = false; }, 900);
        });
    });
</script>

</body>
</html>


