<?php
require_once __DIR__ . '/db.php';
session_start();

// Fetch all regions
$regions = [];
$res_reg = mysqli_query($conn, "SELECT * FROM region ORDER BY nom ASC");
if ($res_reg) { while ($r = mysqli_fetch_assoc($res_reg)) $regions[] = $r; }

// Fetch published services
$hebergements = [];
$res = mysqli_query($conn, "SELECT id, titre, prix, localisation, capacite, photo_principale FROM hebergement WHERE statut IN ('publié','actif') ORDER BY created_at DESC LIMIT 6");
if ($res) { while ($r = mysqli_fetch_assoc($res)) { $r['type']='hebergement'; $r['type_label']='Hébergement'; $hebergements[] = $r; } }

$repas_list = [];
$res = mysqli_query($conn, "SELECT id, titre, prix, localisation, capacite, photo_principale FROM repas WHERE statut='publié' ORDER BY created_at DESC LIMIT 6");
if ($res) { while ($r = mysqli_fetch_assoc($res)) { $r['type']='repas'; $r['type_label']='Repas maison'; $repas_list[] = $r; } }

$guides = [];
$res = mysqli_query($conn, "SELECT id, titre, prix, localisation, capacite, photo_principale FROM guide WHERE statut='publié' ORDER BY created_at DESC LIMIT 6");
if ($res) { while ($r = mysqli_fetch_assoc($res)) { $r['type']='guide'; $r['type_label']='Guide local'; $guides[] = $r; } }

$evenements = [];
$res = mysqli_query($conn, "SELECT id, titre, prix, localisation, capacite, photo_principale FROM evenement WHERE statut='publié' ORDER BY created_at DESC LIMIT 6");
if ($res) { while ($r = mysqli_fetch_assoc($res)) { $r['type']='evenement'; $r['type_label']='Événement'; $evenements[] = $r; } }

$artisanats = [];
$res = mysqli_query($conn, "SELECT id, titre, prix, localisation, stock as capacite, photo_principale FROM artisanat WHERE statut='publié' ORDER BY created_at DESC LIMIT 6");
if ($res) { while ($r = mysqli_fetch_assoc($res)) { $r['type']='artisanat'; $r['type_label']='Artisanat'; $artisanats[] = $r; } }

function fmtImg($path, $type) {
    if (empty($path)) return 'https://placehold.co/800x600?text=Pas+de+photo';
    if (strpos($path, 'http') === 0) return $path;
    if (strpos($path, 'uploads/') === 0) return $path;
    return 'uploads/' . $type . '/' . ltrim($path, '/');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Explorer – Tarkina</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Lato:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --cream: #f5f2ee; --dark: #1c1c2e; --navy: #1a2340; --orange: #e8642c;
      --muted: #6b6b6b; --border: #e0dbd4; --white: #ffffff; --radius: 14px;
    }
    body { font-family: 'Lato', sans-serif; background: var(--cream); color: var(--dark); }

    nav { background: var(--white); border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; padding: 0 56px; height: 60px; position: sticky; top: 0; z-index: 100; }
    .nav-logo { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 800; color: var(--dark); text-decoration: none; }
    .nav-logo span { color: var(--orange); }
    .nav-links { display: flex; gap: 32px; list-style: none; }
    .nav-links a { text-decoration: none; color: var(--dark); font-size: 14px; font-weight: 600; opacity: .7; transition: opacity .2s; }
    .nav-links a:hover { opacity: 1; }
    .nav-actions { display: flex; gap: 12px; }
    .btn-nav { background: var(--orange); color: var(--white); border: none; border-radius: 8px; padding: 9px 22px; font-size: 14px; font-weight: 700; text-decoration: none; }

    .hero-banner { background: var(--navy); color: var(--white); padding: 60px 56px; text-align: center; }
    .hero-banner h1 { font-family: 'Playfair Display', serif; font-size: 42px; margin-bottom: 12px; }
    .hero-banner p { opacity: .7; font-size: 16px; max-width: 600px; margin: 0 auto; }

    .container { padding: 48px 56px; max-width: 1400px; margin: 0 auto; }

    .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .section-title { font-family: 'Playfair Display', serif; font-size: 28px; font-weight: 800; }
    .section-link { color: var(--orange); font-weight: 700; text-decoration: none; font-size: 14px; }
    .section-link:hover { text-decoration: underline; }

    .region-scroll { display: flex; gap: 20px; overflow-x: auto; padding-bottom: 16px; margin-bottom: 48px; }
    .region-card { min-width: 280px; height: 200px; border-radius: var(--radius); overflow: hidden; position: relative; flex-shrink: 0; cursor: pointer; text-decoration: none; }
    .region-card img { width: 100%; height: 100%; object-fit: cover; transition: transform .4s; }
    .region-card:hover img { transform: scale(1.08); }
    .region-card .overlay { position: absolute; bottom: 0; left: 0; right: 0; padding: 20px; background: linear-gradient(transparent, rgba(0,0,0,.7)); color: var(--white); }
    .region-card .overlay h3 { font-family: 'Playfair Display', serif; font-size: 20px; margin-bottom: 4px; }
    .region-card .overlay p { font-size: 12px; opacity: .8; }

    .services-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px; margin-bottom: 56px; }
    .card { background: var(--white); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; text-decoration: none; color: inherit; transition: transform .2s, box-shadow .2s; }
    .card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(0,0,0,.08); }
    .card-img { height: 200px; overflow: hidden; position: relative; }
    .card-img img { width: 100%; height: 100%; object-fit: cover; }
    .card-badge { position: absolute; top: 12px; left: 12px; background: var(--orange); color: var(--white); font-size: 10px; font-weight: 700; padding: 4px 10px; border-radius: 50px; text-transform: uppercase; letter-spacing: .05em; }
    .card-body { padding: 18px; }
    .card-loc { font-size: 12px; color: var(--muted); margin-bottom: 6px; }
    .card-title { font-family: 'Playfair Display', serif; font-size: 17px; font-weight: 700; margin-bottom: 10px; line-height: 1.3; }
    .card-footer { display: flex; justify-content: space-between; align-items: center; padding-top: 12px; border-top: 1px solid var(--border); }
    .card-price { font-weight: 800; font-size: 15px; }
    .card-price small { font-weight: 400; color: var(--muted); }
    .card-rating { display: flex; align-items: center; gap: 4px; font-weight: 700; font-size: 13px; }
    .card-rating svg { width: 14px; height: 14px; fill: var(--orange); }

    .empty-msg { text-align: center; padding: 40px; color: var(--muted); background: var(--white); border-radius: var(--radius); border: 1px solid var(--border); }

    footer { background: var(--navy); color: var(--white); padding: 48px 56px; text-align: center; margin-top: 40px; }
    footer p { opacity: .6; font-size: 14px; }

    @media (max-width: 768px) {
      nav { padding: 0 20px; }
      .hero-banner { padding: 40px 20px; }
      .hero-banner h1 { font-size: 28px; }
      .container { padding: 32px 20px; }
    }
  </style>
</head>
<body>

<nav>
  <a class="nav-logo" href="index.php">Tarkina <span>·</span></a>
  <ul class="nav-links">
    <li><a href="index.php">Accueil</a></li>
    <li><a href="explorer.php" style="opacity:1;">Explorer</a></li>
    <li><a href="about.php">À propos</a></li>
    <li><a href="contact.php">Contact</a></li>
  </ul>
  <div class="nav-actions">
    <?php if(isset($_SESSION['user_id'])): ?>
      <a href="profile.php" class="btn-nav">Mon profil</a>
    <?php else: ?>
      <a href="login.php" class="btn-nav">Se connecter</a>
    <?php endif; ?>
  </div>
</nav>

<div class="hero-banner">
  <h1>Explorez la Tunisie autrement</h1>
  <p>Hébergements, gastronomie, guides locaux, artisanat et événements – trouvez votre prochaine aventure.</p>
</div>

<div class="container">

  <!-- RÉGIONS -->
  <?php if (!empty($regions)): ?>
  <div class="section-header">
    <h2 class="section-title">Nos régions</h2>
  </div>
  <div class="region-scroll">
    <?php foreach ($regions as $reg):
      $img = !empty($reg['photo_principale']) ? (strpos($reg['photo_principale'],'http')===0 ? $reg['photo_principale'] : 'uploads/'.$reg['photo_principale']) : 'https://images.unsplash.com/photo-1540260074744-934336c53549?auto=format&fit=crop&w=800&q=80';
    ?>
    <a class="region-card" href="region.php?id=<?= $reg['id'] ?>">
      <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($reg['nom']) ?>">
      <div class="overlay">
        <h3><?= htmlspecialchars($reg['nom']) ?></h3>
        <p><?= htmlspecialchars(mb_substr($reg['description'] ?? '', 0, 60)) ?>...</p>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <!-- HÉBERGEMENTS -->
  <div class="section-header" id="hebergements">
    <h2 class="section-title">Hébergements</h2>
    <a href="search.php?type=hebergement" class="section-link">Voir tout →</a>
  </div>
  <?php if (empty($hebergements)): ?>
    <div class="empty-msg">Aucun hébergement disponible pour le moment.</div>
  <?php else: ?>
  <div class="services-grid">
    <?php foreach ($hebergements as $s): $img = fmtImg($s['photo_principale']??'', $s['type']); ?>
    <a class="card" href="hebergement.php?id=<?= $s['id'] ?>">
      <div class="card-img">
        <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($s['titre']) ?>">
        <span class="card-badge"><?= $s['type_label'] ?></span>
      </div>
      <div class="card-body">
        <div class="card-loc"><?= htmlspecialchars($s['localisation']) ?></div>
        <div class="card-title"><?= htmlspecialchars($s['titre']) ?></div>
        <div class="card-footer">
          <div class="card-price"><?= number_format($s['prix'],2,'.',' ') ?> TND <small>/ nuit</small></div>
          <div class="card-rating"><svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg> <?= number_format(rand(45,50)/10,1) ?></div>
        </div>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <!-- REPAS -->
  <div class="section-header">
    <h2 class="section-title">Repas maison</h2>
    <a href="search.php?type=repas" class="section-link">Voir tout →</a>
  </div>
  <?php if (empty($repas_list)): ?>
    <div class="empty-msg">Aucun repas disponible pour le moment.</div>
  <?php else: ?>
  <div class="services-grid">
    <?php foreach ($repas_list as $s): $img = fmtImg($s['photo_principale']??'', $s['type']); ?>
    <a class="card" href="repas.php?id=<?= $s['id'] ?>">
      <div class="card-img">
        <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($s['titre']) ?>">
        <span class="card-badge"><?= $s['type_label'] ?></span>
      </div>
      <div class="card-body">
        <div class="card-loc"><?= htmlspecialchars($s['localisation']) ?></div>
        <div class="card-title"><?= htmlspecialchars($s['titre']) ?></div>
        <div class="card-footer">
          <div class="card-price"><?= number_format($s['prix'],2,'.',' ') ?> TND <small>/ pers.</small></div>
          <div class="card-rating"><svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg> <?= number_format(rand(45,50)/10,1) ?></div>
        </div>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <!-- GUIDES -->
  <div class="section-header">
    <h2 class="section-title">Guides locaux</h2>
    <a href="search.php?type=guide" class="section-link">Voir tout →</a>
  </div>
  <?php if (empty($guides)): ?>
    <div class="empty-msg">Aucun guide disponible pour le moment.</div>
  <?php else: ?>
  <div class="services-grid">
    <?php foreach ($guides as $s): $img = fmtImg($s['photo_principale']??'', $s['type']); ?>
    <a class="card" href="guide.php?id=<?= $s['id'] ?>">
      <div class="card-img">
        <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($s['titre']) ?>">
        <span class="card-badge"><?= $s['type_label'] ?></span>
      </div>
      <div class="card-body">
        <div class="card-loc"><?= htmlspecialchars($s['localisation']) ?></div>
        <div class="card-title"><?= htmlspecialchars($s['titre']) ?></div>
        <div class="card-footer">
          <div class="card-price"><?= number_format($s['prix'],2,'.',' ') ?> TND <small>/ pers.</small></div>
          <div class="card-rating"><svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg> <?= number_format(rand(45,50)/10,1) ?></div>
        </div>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <!-- ÉVÉNEMENTS -->
  <div class="section-header">
    <h2 class="section-title">Événements</h2>
    <a href="search.php?type=evenement" class="section-link">Voir tout →</a>
  </div>
  <?php if (empty($evenements)): ?>
    <div class="empty-msg">Aucun événement disponible pour le moment.</div>
  <?php else: ?>
  <div class="services-grid">
    <?php foreach ($evenements as $s): $img = fmtImg($s['photo_principale']??'', $s['type']); ?>
    <a class="card" href="evenement.php?id=<?= $s['id'] ?>">
      <div class="card-img">
        <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($s['titre']) ?>">
        <span class="card-badge"><?= $s['type_label'] ?></span>
      </div>
      <div class="card-body">
        <div class="card-loc"><?= htmlspecialchars($s['localisation']) ?></div>
        <div class="card-title"><?= htmlspecialchars($s['titre']) ?></div>
        <div class="card-footer">
          <div class="card-price"><?= number_format($s['prix'],2,'.',' ') ?> TND <small>/ pers.</small></div>
          <div class="card-rating"><svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg> <?= number_format(rand(45,50)/10,1) ?></div>
        </div>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <!-- ARTISANAT -->
  <div class="section-header">
    <h2 class="section-title">Boutique artisanale</h2>
    <a href="search.php?type=artisanat" class="section-link">Voir tout →</a>
  </div>
  <?php if (empty($artisanats)): ?>
    <div class="empty-msg">Aucun produit artisanal disponible pour le moment.</div>
  <?php else: ?>
  <div class="services-grid">
    <?php foreach ($artisanats as $s): $img = fmtImg($s['photo_principale']??'', $s['type']); ?>
    <a class="card" href="artisanat.php?id=<?= $s['id'] ?>">
      <div class="card-img">
        <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($s['titre']) ?>">
        <span class="card-badge"><?= $s['type_label'] ?></span>
      </div>
      <div class="card-body">
        <div class="card-loc"><?= htmlspecialchars($s['localisation']) ?></div>
        <div class="card-title"><?= htmlspecialchars($s['titre']) ?></div>
        <div class="card-footer">
          <div class="card-price"><?= number_format($s['prix'],2,'.',' ') ?> TND <small>/ unité</small></div>
          <div class="card-rating"><svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg> <?= number_format(rand(45,50)/10,1) ?></div>
        </div>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

</div>

<footer>
  <p>&copy; <?= date('Y') ?> Tarkina — Voyagez autrement en Tunisie.</p>
</footer>

</body>
</html>
