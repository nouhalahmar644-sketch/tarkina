<?php
require_once __DIR__ . '/db.php';

$region_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($region_id <= 0) {
    die('Région invalide.');
}

// Fetch region
$st = mysqli_prepare($conn, "SELECT * FROM region WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($st, 'i', $region_id);
mysqli_stmt_execute($st);
$res = mysqli_stmt_get_result($st);
$region = mysqli_fetch_assoc($res);
mysqli_stmt_close($st);

if (!$region) {
    die('Région introuvable.');
}

$nom = $region['nom'];
$searchTerm = '%' . $nom . '%';

$dateFilter = "";
$reqDebut = isset($_GET['date_debut']) ? trim($_GET['date_debut']) : '';
$reqFin   = isset($_GET['date_fin'])   ? trim($_GET['date_fin'])   : '';

if ($reqDebut !== '' && $reqFin !== '') {
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $reqDebut) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $reqFin)) {
        $dateFilter = " AND (date_debut IS NULL OR date_debut <= ?) AND (date_fin IS NULL OR date_fin >= ?)";
    }
}

// Fetch services
$services = [];

// Helper function to bind params based on whether dateFilter is active
function executeServiceQuery($conn, $sql, $searchTerm, $reqDebut, $reqFin, $dateFilter, $type, $typeLabel, &$services) {
    $st = mysqli_prepare($conn, $sql);
    if ($st) {
        if ($dateFilter !== '') {
            mysqli_stmt_bind_param($st, 'sss', $searchTerm, $reqDebut, $reqFin);
        } else {
            mysqli_stmt_bind_param($st, 's', $searchTerm);
        }
        mysqli_stmt_execute($st);
        $res = mysqli_stmt_get_result($st);
        while ($row = mysqli_fetch_assoc($res)) {
            $row['type'] = $type;
            $row['type_label'] = $typeLabel;
            $services[] = $row;
        }
        mysqli_stmt_close($st);
    }
}

// 1. Hebergements
$sql_heb = "SELECT id, titre, prix, capacite, COALESCE(photo_principale, image) AS photo_principale, statut FROM hebergement WHERE localisation LIKE ? AND statut IN ('publié', 'actif')" . $dateFilter;
executeServiceQuery($conn, $sql_heb, $searchTerm, $reqDebut, $reqFin, $dateFilter, 'hebergement', 'Hébergement', $services);

// 2. Repas
$sql_rep = "SELECT id, titre, prix, capacite, photo_principale, statut FROM repas WHERE localisation LIKE ? AND statut = 'publié'" . $dateFilter;
executeServiceQuery($conn, $sql_rep, $searchTerm, $reqDebut, $reqFin, $dateFilter, 'repas', 'Repas maison', $services);

// 3. Guide
$sql_gui = "SELECT id, titre, prix, capacite, photo_principale, statut FROM guide WHERE localisation LIKE ? AND statut = 'publié'" . $dateFilter;
executeServiceQuery($conn, $sql_gui, $searchTerm, $reqDebut, $reqFin, $dateFilter, 'guide', 'Guide local', $services);

// 4. Evenement
$sql_eve = "SELECT id, titre, prix, capacite, photo_principale, statut FROM evenement WHERE localisation LIKE ? AND statut = 'publié'" . $dateFilter;
executeServiceQuery($conn, $sql_eve, $searchTerm, $reqDebut, $reqFin, $dateFilter, 'evenement', 'Événement', $services);

// Randomize services so they aren't completely grouped by type if desired, or keep grouped.
// Let's keep them ordered randomly for a more natural discover page look.
shuffle($services);

// Photos
$photos_sec = [];
if (!empty($region['photos_sec'])) {
    $dec = json_decode($region['photos_sec'], true);
    if (is_array($dec)) {
        $photos_sec = $dec;
    }
}
// Ensure we have exactly 4 for the grid if possible, or fill with placeholders
while (count($photos_sec) < 4) {
    $photos_sec[] = 'https://placehold.co/400x300?text=Photo';
}

function formatImagePath($path) {
    if (empty($path)) return 'https://placehold.co/800x600?text=Pas+de+photo';
    if (strpos($path, 'http') === 0) return $path;
    if (strpos($path, 'uploads/') === 0) return $path;
    return $path;
}

$main_photo = formatImagePath($region['photo_principale'] ?? '');
$photos_sec = array_map('formatImagePath', $photos_sec);

$page_title = 'Région – Tarkina';

$total_services = count($services);
$count_heb = 0; $count_rep = 0; $count_gui = 0; $count_eve = 0;
foreach ($services as $s) {
    if ($s['type'] === 'hebergement') $count_heb++;
    elseif ($s['type'] === 'repas') $count_rep++;
    elseif ($s['type'] === 'guide') $count_gui++;
    elseif ($s['type'] === 'evenement') $count_eve++;
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($page_title) ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Lato:wght@400;600;700&display=swap" rel="stylesheet" />
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --cream: #f5f2ee;
      --dark: #1c1c2e;
      --navy: #1a2340;
      --orange: #e8642c;
      --orange-light: #fde8dc;
      --muted: #6b6b6b;
      --border: #e0dbd4;
      --white: #ffffff;
      --radius: 14px;
    }
    body { font-family: 'Lato', sans-serif; background: var(--cream); color: var(--dark); font-size: 15px; line-height: 1.7; }
 
    /* ── NAV ── */
    nav { background: var(--white); border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; padding: 0 56px; height: 60px; position: sticky; top: 0; z-index: 100; }
    .nav-logo { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 800; color: var(--dark); text-decoration: none; display: flex; align-items: center; gap: 4px; }
    .nav-logo span { color: var(--orange); }
    .nav-links { display: flex; gap: 32px; list-style: none; }
    .nav-links a { text-decoration: none; color: var(--dark); font-size: 14px; font-weight: 600; opacity: .7; transition: opacity .2s; }
    .nav-links a:hover { opacity: 1; }
    .nav-actions { display: flex; align-items: center; gap: 16px; }
    .btn-ghost { background: none; border: none; cursor: pointer; font-family: 'Lato', sans-serif; font-size: 14px; font-weight: 600; color: var(--dark); opacity: .7; }
    .btn-nav { background: var(--orange); color: var(--white); border: none; border-radius: 8px; padding: 9px 22px; font-size: 14px; font-weight: 700; cursor: pointer; font-family: 'Lato', sans-serif; }
 
    /* ── BREADCRUMB ── */
    .breadcrumb { padding: 16px 56px; font-size: 13px; color: var(--muted); }
    .breadcrumb a { color: var(--dark); text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; }
    .breadcrumb a:hover { text-decoration: underline; }
 
    /* ── PHOTO GRID ── */
    .photo-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; grid-template-rows: 240px 240px; gap: 6px; padding: 0 56px; border-radius: var(--radius); overflow: hidden; }
    .photo-grid img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .photo-main { grid-row: 1 / 3; grid-column: 1 / 2; }
    /* fallback if no image */
    .photo-grid .no-photo { background: #d4be9a; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #888; font-size: 13px; }
 
    /* ── MAIN LAYOUT ── */
    .main-content { display: grid; grid-template-columns: 1fr 340px; gap: 48px; padding: 36px 56px 64px; align-items: start; }
 
    /* LEFT */
    .region-tag { font-size: 11px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--orange); margin-bottom: 10px; }
    .region-subtitle { font-size: 12px; color: var(--muted); font-weight: 600; margin-bottom: 6px; letter-spacing: .05em; }
    .region-title { font-family: 'Playfair Display', serif; font-size: 48px; font-weight: 800; color: var(--dark); line-height: 1.1; margin-bottom: 20px; }
    .region-description { font-size: 15px; color: #555; line-height: 1.8; margin-bottom: 40px; }
    .region-description p + p { margin-top: 14px; }
 
    /* ── SERVICES SECTION ── */
    .services-title { font-family: 'Playfair Display', serif; font-size: 28px; font-weight: 800; color: var(--dark); margin-bottom: 20px; }
    .filter-tabs { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 24px; }
    .filter-tab { background: none; border: 1.5px solid var(--border); border-radius: 50px; padding: 7px 18px; font-size: 13.5px; font-weight: 600; color: var(--dark); cursor: pointer; font-family: 'Lato', sans-serif; transition: all .2s; display: flex; align-items: center; gap: 6px; }
    .filter-tab:hover { border-color: var(--orange); color: var(--orange); }
    .filter-tab.active { background: var(--dark); border-color: var(--dark); color: var(--white); }
    .filter-tab .count { font-size: 11px; opacity: .7; }
 
    /* Services grid */
    .services-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
    .service-card { background: var(--white); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; cursor: pointer; transition: transform .2s, box-shadow .2s; text-decoration: none; color: inherit; }
    .service-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.1); }
    .service-card img { width: 100%; height: 160px; object-fit: cover; display: block; }
    .service-card .no-photo { width: 100%; height: 160px; background: #e8e2db; display: flex; align-items: center; justify-content: center; color: #aaa; font-size: 13px; }
    .card-body { padding: 16px; }
    .card-type { font-size: 10px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: var(--orange); margin-bottom: 6px; display: flex; align-items: center; gap: 5px; }
    .card-title { font-family: 'Playfair Display', serif; font-size: 16px; font-weight: 700; color: var(--dark); margin-bottom: 8px; line-height: 1.3; }
    .card-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 10px; }
    .card-price { font-size: 15px; font-weight: 700; color: var(--dark); }
    .card-price span { font-size: 12px; font-weight: 400; color: var(--muted); }
    .card-rating { display: flex; align-items: center; gap: 5px; font-size: 13px; font-weight: 700; color: var(--dark); }
    .card-rating svg { width: 14px; height: 14px; fill: var(--orange); }
    .card-capacity { font-size: 12px; color: var(--muted); display: flex; align-items: center; gap: 4px; margin-top: 6px; }
    .card-capacity svg { width: 13px; height: 13px; stroke: var(--muted); fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
 
    .no-services { background: var(--white); border: 1px solid var(--border); border-radius: var(--radius); padding: 40px; text-align: center; color: var(--muted); font-size: 15px; grid-column: 1 / -1; }
 
    /* RIGHT: Info card */
    .info-card { background: var(--white); border: 1px solid var(--border); border-radius: var(--radius); padding: 28px; position: sticky; top: 76px; }
    .info-card-title { font-family: 'Playfair Display', serif; font-size: 18px; font-weight: 700; color: var(--dark); margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid var(--border); }
    .info-row { display: flex; justify-content: space-between; align-items: flex-start; padding: 12px 0; border-bottom: 1px solid var(--border); gap: 16px; }
    .info-row:last-child { border-bottom: none; }
    .info-label { font-size: 11px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: var(--muted); flex-shrink: 0; }
    .info-value { font-size: 14px; font-weight: 600; color: var(--dark); text-align: right; }
    .services-count-badge { display: inline-block; background: var(--orange-light); color: var(--orange); font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 50px; }
 
    /* ── FOOTER ── */
    footer { background: var(--navy); padding: 56px 56px 32px; margin-top: 40px; }
    .footer-grid { display: grid; grid-template-columns: 1.4fr 1fr 1fr 1fr; gap: 48px; margin-bottom: 48px; }
    .footer-logo { font-family: 'Playfair Display', serif; font-size: 26px; font-weight: 800; color: var(--white); margin-bottom: 14px; }
    .footer-brand p { font-size: 14px; color: rgba(255,255,255,.55); line-height: 1.7; max-width: 260px; }
    .footer-col h4 { font-size: 11px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: rgba(255,255,255,.5); margin-bottom: 20px; }
    .footer-col ul { list-style: none; display: flex; flex-direction: column; gap: 12px; }
    .footer-col ul li a { text-decoration: none; font-size: 14px; color: rgba(255,255,255,.75); transition: color .2s; }
    .footer-col ul li a:hover { color: var(--white); }
    .footer-contact { display: flex; flex-direction: column; gap: 12px; }
    .footer-contact-item { display: flex; align-items: center; gap: 10px; font-size: 14px; color: rgba(255,255,255,.75); }
    .footer-contact-item svg { width: 15px; height: 15px; stroke: rgba(255,255,255,.5); fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0; }
    .footer-bottom { border-top: 1px solid rgba(255,255,255,.1); padding-top: 24px; text-align: center; font-size: 13px; color: rgba(255,255,255,.35); }
  </style>
</head>
<body>
 
<!-- NAV -->
<nav>
  <a class="nav-logo" href="index.php">Tarkina <span>·</span></a>
  <ul class="nav-links">
    <li><a href="index.php">Accueil</a></li>
    <li><a href="explorer.php">Explorer</a></li>
    <li><a href="about.php">À propos</a></li>
    <li><a href="contact.php">Contact</a></li>
  </ul>
  <div class="nav-actions">
    <button class="btn-ghost">Connexion</button>
    <button class="btn-nav">S'inscrire</button>
  </div>
</nav>
 
<!-- BREADCRUMB -->
<div class="breadcrumb">
  <a href="explorer.php">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
    Retour aux régions
  </a>
</div>
 
<!-- PHOTO GRID -->
<div class="photo-grid">
  <?php if (!empty($main_photo) && strpos($main_photo, 'placehold.co') === false): ?>
    <img class="photo-main" src="<?= htmlspecialchars($main_photo) ?>" alt="Photo principale" />
  <?php else: ?>
    <div class="photo-main no-photo">Pas de photo principale</div>
  <?php endif; ?>

  <?php for ($i=0; $i<4; $i++): ?>
    <?php if (!empty($photos_sec[$i]) && strpos($photos_sec[$i], 'placehold.co') === false): ?>
      <img src="<?= htmlspecialchars($photos_sec[$i]) ?>" alt="Photo <?= $i+2 ?>" />
    <?php else: ?>
      <div class="no-photo">Pas de photo</div>
    <?php endif; ?>
  <?php endfor; ?>
</div>
 
<!-- MAIN CONTENT -->
<div class="main-content">
 
  <!-- LEFT -->
  <div>
    <div class="region-subtitle">Tunisie · Région</div>
    <h1 class="region-title"><?= htmlspecialchars($region['nom']) ?></h1>
    <div class="region-description">
      <p><?= nl2br(htmlspecialchars($region['description'])) ?></p>
    </div>
 
    <!-- SERVICES DISPONIBLES PAR CATÉGORIE -->
    
    <!-- 1. Hébergements -->
    <?php if ($count_heb > 0): ?>
    <h2 class="services-title" style="margin-top:40px;">Hébergements à <?= htmlspecialchars($region['nom']) ?></h2>
    <div class="services-grid">
      <?php foreach ($services as $srv): if ($srv['type'] !== 'hebergement') continue; 
          $rating = number_format(rand(45, 50) / 10, 1);
          $img = formatImagePath($srv['photo_principale'] ?? '');
      ?>
      <a class="service-card" href="hebergement.php?id=<?= (int)$srv['id'] ?>">
        <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($srv['titre']) ?>" />
        <div class="card-body">
          <div class="card-type">Hébergement</div>
          <div class="card-title"><?= htmlspecialchars($srv['titre']) ?></div>
          <div class="card-footer">
            <div class="card-price"><?= number_format($srv['prix'], 2, '.', '') ?> TND <span>/ nuit</span></div>
          </div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- 2. Repas -->
    <?php if ($count_rep > 0): ?>
    <h2 class="services-title" style="margin-top:40px;">Gastronomie locale</h2>
    <div class="services-grid">
      <?php foreach ($services as $srv): if ($srv['type'] !== 'repas') continue; 
          $img = formatImagePath($srv['photo_principale'] ?? '');
      ?>
      <a class="service-card" href="repas.php?id=<?= (int)$srv['id'] ?>">
        <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($srv['titre']) ?>" />
        <div class="card-body">
          <div class="card-type">Repas maison</div>
          <div class="card-title"><?= htmlspecialchars($srv['titre']) ?></div>
          <div class="card-footer">
            <div class="card-price"><?= number_format($srv['prix'], 2, '.', '') ?> TND <span>/ pers.</span></div>
          </div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- 3. Guides & Événements -->
    <?php if ($count_gui > 0 || $count_eve > 0): ?>
    <h2 class="services-title" style="margin-top:40px;">Activités & Découvertes</h2>
    <div class="services-grid">
      <?php foreach ($services as $srv): if ($srv['type'] !== 'guide' && $srv['type'] !== 'evenement') continue; 
          $img = formatImagePath($srv['photo_principale'] ?? '');
      ?>
      <a class="service-card" href="<?= $srv['type'] ?>.php?id=<?= (int)$srv['id'] ?>">
        <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($srv['titre']) ?>" />
        <div class="card-body">
          <div class="card-type"><?= $srv['type_label'] ?></div>
          <div class="card-title"><?= htmlspecialchars($srv['titre']) ?></div>
          <div class="card-footer">
            <div class="card-price"><?= number_format($srv['prix'], 2, '.', '') ?> TND <span>/ pers.</span></div>
          </div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (empty($services)): ?>
      <div class="no-services">Aucun service disponible dans cette région pour le moment.</div>
    <?php endif; ?>
  </div>
 
  <!-- RIGHT: Info card -->
  <div class="info-card">
    <div class="info-card-title">À savoir</div>
    <div class="info-row">
      <span class="info-label">Meilleure saison</span>
      <span class="info-value"><?= htmlspecialchars($region['meilleure_saison'] ?: 'Toute l\'année') ?></span>
    </div>
    <div class="info-row">
      <span class="info-label">Langues</span>
      <span class="info-value"><?= htmlspecialchars($region['langues'] ?: 'Arabe, Français') ?></span>
    </div>
    <div class="info-row">
      <span class="info-label">Monnaie</span>
      <span class="info-value"><?= htmlspecialchars($region['monnaie'] ?: 'TND (Dinar Tunisien)') ?></span>
    </div>
    <div class="info-row">
      <span class="info-label">Services dispo.</span>
      <span class="info-value"><span class="services-count-badge"><?= $total_services ?></span></span>
    </div>
  </div>
 
</div>
 
<!-- FOOTER -->
<footer>
  <div class="footer-grid">
    <div class="footer-brand">
      <div class="footer-logo">Tarkina</div>
      <p>Découvrez la Tunisie cachée à travers ses habitants, ses saveurs et son artisanat.</p>
    </div>
    <div class="footer-col">
      <h4>Explorer</h4>
      <ul>
        <li><a href="#">Toutes les régions</a></li>
        <li><a href="#">Hébergements</a></li>
        <li><a href="#">Repas maison</a></li>
        <li><a href="#">Guides locaux</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>À propos</h4>
      <ul>
        <li><a href="#">Qui sommes-nous</a></li>
        <li><a href="#">Contact</a></li>
        <li><a href="#">Devenir hôte</a></li>
        <li><a href="#">CGU</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Contact</h4>
      <div class="footer-contact">
        <div class="footer-contact-item">
          <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="2.5"/></svg>
          Tunis, Tunisie
        </div>
        <div class="footer-contact-item">
          <svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          hello@tarkina.tn
        </div>
        <div class="footer-contact-item">
          <svg viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.27h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.91a16 16 0 0 0 6 6l.92-.92a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
          +216 71 000 000
        </div>
      </div>
    </div>
  </div>
  <div class="footer-bottom">© 2026 Tarkina — Voyagez autrement en Tunisie.</div>
</footer>
 
<script>
// Filter tabs
const tabs = document.querySelectorAll('.filter-tab');
const cards = document.querySelectorAll('.service-card');
 
tabs.forEach(tab => {
  tab.addEventListener('click', () => {
    tabs.forEach(t => t.classList.remove('active'));
    tab.classList.add('active');
    const filter = tab.dataset.filter;
    cards.forEach(card => {
      if (filter === 'all' || card.dataset.type === filter) {
        card.style.display = 'block';
      } else {
        card.style.display = 'none';
      }
    });
  });
});
</script>
 
</body>
</html>
