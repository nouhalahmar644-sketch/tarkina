<?php
session_start();
require 'db.php';
mysqli_set_charset($conn, 'utf8mb4');

$regions_query = mysqli_query($conn, "SELECT * FROM region");
$regions = [];
while ($r = mysqli_fetch_assoc($regions_query)) {
    $regions[] = $r;
}

// Static history + coordinates per region (match by name)
$region_meta = [
    'Djerba' => [
        'lat' => 33.8075, 'lng' => 10.8451,
        'history' => "Surnommée l'île des rêves, Djerba est habitée depuis l'Antiquité. Phéniciens, Romains et Berbères y ont laissé leurs traces. Sa synagogue El Ghriba est l'une des plus anciennes du monde.",
        'highlight' => 'Synagogue El Ghriba · Plages de Sidi Mahres · Musée Guellala'
    ],
    'Kairouan' => [
        'lat' => 35.6781, 'lng' => 10.0963,
        'history' => "Fondée en 670 ap. J.-C., Kairouan est la quatrième ville sainte de l'Islam. Son médina classée UNESCO abrite la Grande Mosquée Okba, l'une des plus anciennes d'Afrique.",
        'highlight' => 'Grande Mosquée · Bassins des Aghlabides · Médina UNESCO'
    ],
    'Sidi Bou Saïd' => [
        'lat' => 36.8689, 'lng' => 10.3417,
        'history' => "Village perché sur une falaise dominant la mer, célèbre pour ses maisons bleu et blanc. Paul Klee et Gustave Flaubert y ont séjourné. Un symbole de l'identité tunisienne.",
        'highlight' => 'Café des Nattes · Dar Ennejma Ezzahra · Vue sur Carthage'
    ],
    'Tozeur' => [
        'lat' => 33.9197, 'lng' => 8.1335,
        'history' => "Porte du Sahara tunisien, Tozeur est connue pour ses palmeraies immenses et son architecture en brique de terre cuite. Star Wars y fut tourné. Une oasis au bord du désert.",
        'highlight' => 'Chott El Jérid · Palmeraie · Décors Star Wars'
    ],
    'Takrouna' => [
        'lat' => 36.0500, 'lng' => 10.0833,
        'history' => "Village berbère perché sur un piton rocheux surplombant la plaine. L'un des derniers villages berbères de Tunisie, Takrouna offre une vue panoramique exceptionnelle et une architecture unique.",
        'highlight' => 'Village berbère · Vue panoramique · Artisanat local'
    ],
    'Kessra' => [
        'lat' => 35.7167, 'lng' => 9.3667,
        'history' => "Village de montagne niché dans le Djebel Kesra, l'un des plus beaux villages de Tunisie. Ses ruelles en pierre, ses maisons traditionnelles et son calme en font un refuge authentique.",
        'highlight' => 'Architecture en pierre · Randonnées · Vue sur la vallée'
    ],
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explorer les régions — Tarkina</title>
    <meta name="description" content="Découvrez toutes les régions de Tunisie, leurs hébergements, repas maison, guides locaux et événements authentiques.">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Lato:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        :root {
            --navy: #1B3A4B;
            --coral: #E05A2B;
            --cream: #FAF8F5;
            --border: #E5E7EB;
            --muted: #6B7280;
            --dark: #1a1a2e;
            --primary: #E05A2B;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Lato', 'Segoe UI', sans-serif; background: var(--cream); color: var(--dark); }
        
        .navbar { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; background: #fff; border-bottom: 1px solid var(--border); padding: 0 60px; height: 70px; display: flex; align-items: center; justify-content: space-between; }
        .nav-logo { display: flex; align-items: center; text-decoration: none; }
        .nav-logo img { height: 36px; }
        .nav-links { display: flex; gap: 36px; list-style: none; }
        .nav-links a { text-decoration: none; color: var(--text-dark); font-size: 0.95rem; font-weight: 500; transition: color .2s; }
        .nav-links a:hover { color: var(--primary); }
        .nav-auth { display: flex; gap: 12px; align-items: center; }
        .btn-nav-outline { padding: 8px 20px; border: 1.5px solid var(--navy); border-radius: 50px; color: var(--navy); text-decoration: none; font-size: 0.9rem; font-weight: 600; transition: all .2s; }
        .btn-nav-outline:hover { background: var(--navy); color: #fff; }
        .btn-nav-primary { padding: 8px 20px; background: var(--primary); border-radius: 50px; color: #fff; text-decoration: none; font-size: 0.9rem; font-weight: 600; }
    </style>
</head>
<body>

<!-- NAVBAR -->
<?php include 'navbar.php'; ?>

<div style="position:absolute;top:80px;left:40px;z-index:100;margin-top:0;padding-top:0;">
    <a href="index.php" style="display:inline-flex;align-items:center;gap:6px;color:#fff;font-size:0.9rem;font-weight:600;text-decoration:none;opacity:0.7;transition:opacity 0.2s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'">
        ← Retour à l'accueil
    </a>
</div>

<div class="explorer-hero" style="margin-top: 70px;">
    <div class="hero-tag">Tunisie authentique</div>
    <h1>Explorez la Tunisie</h1>
    <p>Découvrez nos régions, leur histoire et leurs trésors cachés</p>
    <div class="hero-search">
        <input type="text" id="region-search" placeholder="Rechercher une région…" oninput="filterCards(this.value)">
        <button onclick="filterCards(document.getElementById('region-search').value)">Rechercher</button>
    </div>
    <div class="hero-stats">
        <div class="hero-stat"><span class="stat-num">6</span><span class="stat-label">Régions</span></div>
        <div class="hero-stat"><span class="stat-num">24+</span><span class="stat-label">Expériences</span></div>
        <div class="hero-stat"><span class="stat-num">100%</span><span class="stat-label">Authentique</span></div>
    </div>
</div>

<div class="explorer-map" id="map"></div>

<div class="explorer-regions">
    <div class="section-header">
        <h2>Nos régions</h2>
    </div>
    <div class="regions-list" id="regions-grid">
        <?php foreach ($regions as $r):
            $name = $r['nom'];
            $meta = $region_meta[$name] ?? ['history' => '', 'highlight' => '', 'lat' => 0, 'lng' => 0];
            $photo = !empty($r['photo']) 
                ? (strpos($r['photo'], 'http') === 0 ? $r['photo'] : 'uploads/' . $r['photo']) 
                : 'https://images.unsplash.com/photo-1540260074744-934336c53549?w=800&fit=crop';
        ?>
        <div class="region-card" id="card-<?= $r['id'] ?>" data-lat="<?= $meta['lat'] ?>" data-lng="<?= $meta['lng'] ?>" data-id="<?= $r['id'] ?>" data-name="<?= strtolower(htmlspecialchars($name)) ?>">
            <div class="card-img">
                <img src="<?= htmlspecialchars($photo) ?>" alt="<?= htmlspecialchars($name) ?>">
                <div class="card-img-badge"><?= htmlspecialchars($name) ?></div>
            </div>
            <div class="card-body">
                <div class="card-top">
                    <h3><?= htmlspecialchars($name) ?></h3>
                    <p><?= htmlspecialchars($meta['history']) ?></p>
                </div>
                <div class="card-bottom">
                    <div class="card-highlights">📍 <?= htmlspecialchars($meta['highlight']) ?></div>
                    <a href="region.php?id=<?= $r['id'] ?>" class="btn-explore">Découvrir →</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- BLOG TEASER —— before footer in explorer.php -->
<section style="background:#1B3A4B;padding:60px 0;text-align:center;">
  <div class="container">
    <h2 style="color:#fff;font-size:2rem;font-weight:700;margin-bottom:0.5rem;">Le Blog des voyageurs</h2>
    <p style="color:rgba(255,255,255,0.7);font-size:1rem;margin-bottom:1.5rem;">
      De vraies expériences partagées par des voyageurs comme vous
    </p>
    <a href="blogs.php" style="background:#E05A2B;color:#fff;border-radius:999px;padding:12px 32px;font-size:15px;font-weight:600;text-decoration:none;">
      Découvrir le blog →
    </a>
  </div>
</section>

<!-- FOOTER -->
<?php include 'footer.php'; ?>

<script>
const map = L.map('map', {
    center: [33.8869, 9.5375],
    zoom: 6,
    minZoom: 5,
    maxZoom: 10,
    maxBounds: [[29.0, 7.0], [38.5, 13.5]],
    maxBoundsViscosity: 1.0,
    zoomControl: false
});

L.control.zoom({
    zoomInTitle: 'Zoom avant',
    zoomOutTitle: 'Zoom arrière'
}).addTo(map);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© contributeurs d\'OpenStreetMap'
}).addTo(map);

const orangeIcon = L.divIcon({
    className: '',
    html: `<div style="background:#E05A2B;width:14px;height:14px;border-radius:50%;border:3px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,0.3)"></div>`,
    iconSize: [20, 20], iconAnchor: [10, 10]
});

document.querySelectorAll('.region-card').forEach(card => {
    const lat = parseFloat(card.dataset.lat);
    const lng = parseFloat(card.dataset.lng);
    const id = card.dataset.id;
    const name = card.querySelector('h3').textContent;

    if (!lat || !lng) return;

    const marker = L.marker([lat, lng], { icon: orangeIcon })
        .addTo(map)
        .bindPopup(`<strong>${name}</strong>`);

    marker.on('click', () => {
        document.querySelectorAll('.region-card').forEach(c => c.classList.remove('active'));
        card.classList.add('active');
        card.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });

    card.addEventListener('click', () => {
        map.setView([lat, lng], 9, { animate: true });
        marker.openPopup();
        document.querySelectorAll('.region-card').forEach(c => c.classList.remove('active'));
        card.classList.add('active');
    });
});
function filterCards(query) {
    const q = query.toLowerCase();
    document.querySelectorAll('.region-card').forEach(card => {
        const name = card.dataset.name || '';
        card.style.display = name.includes(q) ? 'flex' : 'none';
    });
}
</script>
</body>
</html>
