<?php
session_start();
require_once __DIR__ . '/db.php';

// Redirect to login if form is submitted but user not logged in
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_SESSION['user_id'])) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);
    header("Location: login.php?redirect=" . urlencode("guide.php?id=$id"));
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
}

if ($id <= 0) {
    die('Guide invalide.');
}

// Fetch guide
$st = mysqli_prepare($conn, "SELECT * FROM guide WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($st, 'i', $id);
mysqli_stmt_execute($st);
$res = mysqli_stmt_get_result($st);
$item = mysqli_fetch_assoc($res);
mysqli_stmt_close($st);

if (!$item) {
    die('Guide introuvable.');
}

// Use localisation for breadcrumb
$region_name = !empty($item['localisation']) ? $item['localisation'] : 'Accueil';

// Fetch avis
$avis_list = [];
$st_avis = mysqli_prepare($conn, "SELECT a.*, u.nom as user_nom FROM avis a JOIN utilisateur u ON a.utilisateur_id = u.id WHERE a.guide_id = ? ORDER BY a.created_at DESC");
if ($st_avis) {
    mysqli_stmt_bind_param($st_avis, 'i', $id);
    mysqli_stmt_execute($st_avis);
    $res_avis = mysqli_stmt_get_result($st_avis);
    while ($row = mysqli_fetch_assoc($res_avis)) {
        $avis_list[] = $row;
    }
    mysqli_stmt_close($st_avis);
}

// POST Handling (Reservation)
$order_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $date = $_POST['date'] ?? '';
    
    if (empty($date)) {
        $order_error = 'Veuillez sélectionner une date.';
    } else {
        $user_id = $_SESSION['user_id'];
        
        // Insert reservation
        $nb_pers = isset($_POST['personnes']) ? (int)$_POST['personnes'] : 1;
        $stmt = mysqli_prepare($conn, "INSERT INTO reservations (utilisateur_id, guide_id, date_debut, nb_personnes, statut) VALUES (?, ?, ?, ?, 'en_attente')");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'iisi', $user_id, $id, $date, $nb_pers);
            mysqli_stmt_execute($stmt);
            $reservation_id = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);
            
            header("Location: merci.php?reservation_id=" . $reservation_id);
            exit;
        } else {
            $order_error = 'Erreur lors de la réservation.';
        }
    }
}

// Formatting images helper
function formatImagePath($path) {
    if (empty($path)) return 'https://placehold.co/800x600?text=Pas+de+photo';
    if (strpos($path, 'http') === 0) return $path;
    if (strpos($path, 'uploads/guide/') === 0) return $path;
    return 'uploads/guide/' . ltrim($path, '/');
}

$main_photo = formatImagePath($item['photo_principale'] ?? '');
$photos_sec = [];
if (!empty($item['photos_sec'])) {
    $dec = json_decode($item['photos_sec'], true);
    if (is_array($dec)) {
        $photos_sec = array_map('formatImagePath', $dec);
    }
}
while (count($photos_sec) < 4) {
    $photos_sec[] = 'https://placehold.co/400x300?text=Photo';
}

$rating = number_format(rand(45, 50) / 10, 1);
$prix = (float)$item['prix'];
$capacite = (int)$item['capacite'];

$inclus = [];
if (!empty($item['inclus'])) {
    $dec_inclus = json_decode($item['inclus'], true);
    if (is_array($dec_inclus)) {
        $inclus = $dec_inclus;
    }
}

$page_title = htmlspecialchars($item['titre']) . ' – Tarkina';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= $page_title ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Lato:wght@400;600;700&display=swap" rel="stylesheet" />
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --cream: #f5f2ee;
      --dark: #1c1c2e;
      --navy: #1a2340;
      --orange: #e8642c;
      --border: #e0dbd4;
      --white: #ffffff;
      --radius: 14px;
      --muted: #6b6b6b;
      --green: #2ecc71;
      --red: #e74c3c;
    }
    body { font-family: 'Lato', sans-serif; background: var(--cream); color: var(--dark); font-size: 15px; line-height: 1.7; }
 
    nav { background: var(--white); border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; padding: 0 56px; height: 60px; position: sticky; top: 0; z-index: 100; }
    .nav-logo { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 800; color: var(--dark); text-decoration: none; display: flex; align-items: center; gap: 4px; }
    .nav-logo span { color: var(--orange); }
    .nav-links { display: flex; gap: 32px; list-style: none; }
    .nav-links a { text-decoration: none; color: var(--dark); font-size: 14px; font-weight: 600; opacity: .7; transition: opacity .2s; }
    .nav-links a:hover { opacity: 1; }
    .nav-actions { display: flex; align-items: center; gap: 16px; }
    .btn-nav { background: var(--orange); color: var(--white); border: none; border-radius: 8px; padding: 9px 22px; font-size: 14px; font-weight: 700; cursor: pointer; text-decoration:none; }

    .breadcrumb { padding: 16px 56px; font-size: 13px; color: var(--muted); }
    .breadcrumb a { color: var(--dark); text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; }
    .breadcrumb a:hover { text-decoration: underline; }

    .hero-gallery { padding: 0 56px; margin-bottom: 32px; }
    .photo-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; height: 400px; border-radius: var(--radius); overflow: hidden; }
    .photo-main { width: 100%; height: 100%; object-fit: cover; }
    .photo-side-grid { display: grid; grid-template-columns: 1fr 1fr; grid-template-rows: 1fr 1fr; gap: 12px; height: 100%; }
    .photo-side-grid img { width: 100%; height: 100%; object-fit: cover; }

    .product-container { display: grid; grid-template-columns: 1fr 400px; gap: 48px; padding: 10px 56px 64px; align-items: start; }
    @media (max-width: 900px) {
        .product-container { grid-template-columns: 1fr; }
        .photo-grid { grid-template-columns: 1fr; height: auto; }
        .photo-side-grid { display: none; }
        .photo-main { height: 300px; }
    }

    .p-tag { font-size: 11px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: var(--orange); margin-bottom: 8px; display: flex; align-items: center; gap: 6px; }
    .p-tag svg { width: 14px; height: 14px; }
    .p-title { font-family: 'Playfair Display', serif; font-size: 32px; font-weight: 800; color: var(--dark); line-height: 1.2; margin-bottom: 12px; }
    
    .p-meta { display: flex; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid var(--border); }
    .p-rating { display: flex; align-items: center; gap: 6px; font-weight: 700; }
    .p-rating svg { fill: var(--orange); width: 16px; height: 16px; }
    .p-meta-item { display: flex; align-items: center; gap: 6px; font-size: 14px; color: var(--muted); }

    .description-block { margin-top: 20px; margin-bottom: 40px; }
    .description-block h3 { font-family: 'Playfair Display', serif; font-size: 22px; margin-bottom: 16px; }
    .description-block p { font-size: 15px; color: #555; white-space: pre-wrap; margin-bottom: 24px; }

    .inclus-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .inclus-item { display: flex; align-items: center; gap: 12px; font-size: 15px; font-weight: 600; }
    .inclus-item svg { width: 20px; height: 20px; color: var(--green); flex-shrink: 0; }

    .avis-section { margin-top: 48px; border-top: 1px solid var(--border); padding-top: 32px; }
    .avis-card { background: var(--white); padding: 20px; border-radius: var(--radius); margin-bottom: 16px; border: 1px solid var(--border); }
    .avis-header { display: flex; justify-content: space-between; margin-bottom: 12px; }
    .avis-user { font-weight: 700; }
    .avis-date { font-size: 13px; color: var(--muted); }
    .avis-text { font-size: 14px; color: #555; }

    .booking-card { background: var(--white); padding: 32px; border-radius: var(--radius); border: 1px solid var(--border); position: sticky; top: 80px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
    .p-price { font-size: 28px; font-weight: 800; color: var(--dark); margin-bottom: 24px; display: flex; align-items: baseline; gap: 6px; }
    .p-price span { font-size: 14px; color: var(--muted); font-weight: 400; }

    .form-group { margin-bottom: 16px; }
    .form-label { display: block; font-size: 13px; font-weight: 700; margin-bottom: 8px; color: var(--dark); }
    .input-field { width: 100%; padding: 12px 14px; border: 1px solid var(--border); border-radius: 8px; font-family: 'Lato', sans-serif; font-size: 15px; transition: border-color 0.2s; background: var(--cream); }
    .input-field:focus { outline: none; border-color: var(--orange); background: var(--white); }

    .btn-submit { width: 100%; background: var(--orange); color: var(--white); border: none; border-radius: 8px; padding: 16px; font-size: 16px; font-weight: 700; cursor: pointer; transition: background 0.2s; margin-top: 16px; }
    .btn-submit:hover { background: #d65a25; }
    .error-msg { background: rgba(231, 76, 60, 0.1); color: var(--red); padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; font-weight: 600; }
    
    footer { background: var(--navy); color: var(--white); padding: 48px 56px; margin-top: 64px; text-align: center; }

  </style>
</head>
<body>

<nav>
  <a class="nav-logo" href="index.php">Tarkina <span>·</span></a>
  <ul class="nav-links">
    <li><a href="index.php">Accueil</a></li>
    <li><a href="explorer.php">Explorer</a></li>
    <li><a href="about.php">À propos</a></li>
    <li><a href="contact.php">Contact</a></li>
  </ul>
  <div class="nav-actions">
    <?php if(isset($_SESSION['user_id'])): ?>
        <a href="profile.php" class="btn-nav">Mon profil</a>
    <?php else: ?>
        <a href="login.php?redirect=<?php echo urlencode(basename($_SERVER['PHP_SELF']) . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '')); ?>" class="btn-nav">Se connecter</a>
    <?php endif; ?>
  </div>
</nav>

<div class="breadcrumb">
  <a href="index.php">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
    Retour – <?= htmlspecialchars($region_name) ?>
  </a>
</div>

<div class="hero-gallery">
  <div class="photo-grid">
    <img class="photo-main" src="<?= htmlspecialchars($main_photo) ?>" alt="Photo principale" />
    <div class="photo-side-grid">
      <?php foreach($photos_sec as $p): ?>
        <img src="<?= htmlspecialchars($p) ?>" alt="Photo secondaire" />
      <?php endforeach; ?>
    </div>
  </div>
</div>

<div class="product-container">
  <div class="left-column">
    <div class="p-tag">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
        Guide & Excursion
    </div>
    <h1 class="p-title"><?= htmlspecialchars($item['titre']) ?></h1>
    
    <div class="p-meta">
      <div class="p-rating">
        <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        <?= $rating ?>
      </div>
      <div class="p-meta-item">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
        <?= htmlspecialchars($item['localisation']) ?>
      </div>
      <div class="p-meta-item">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
        Capacité: <?= $capacite ?> pers.
      </div>
    </div>

    <div class="description-block">
      <h3>Description du circuit</h3>
      <p><?= htmlspecialchars($item['description']) ?></p>
      
      <?php if(!empty($inclus)): ?>
      <h3 style="margin-top:32px;">Ce qui est inclus</h3>
      <div class="inclus-grid">
        <?php foreach($inclus as $inc): ?>
        <div class="inclus-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
            <?= htmlspecialchars($inc) ?>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>

    <div class="avis-section">
        <h3 style="font-family:'Playfair Display', serif; font-size:22px; margin-bottom:24px;">Avis des voyageurs</h3>
        <?php if(empty($avis_list)): ?>
            <p style="color:var(--muted);">Aucun avis pour le moment.</p>
        <?php else: ?>
            <?php foreach($avis_list as $avis): ?>
            <div class="avis-card">
                <div class="avis-header">
                    <span class="avis-user"><?= htmlspecialchars($avis['user_nom']) ?></span>
                    <span class="avis-date"><?= date('d/m/Y', strtotime($avis['created_at'])) ?></span>
                </div>
                <div class="p-rating" style="margin-bottom:8px;">
                    <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <?= (int)$avis['note'] ?>/5
                </div>
                <div class="avis-text">
                    <?= nl2br(htmlspecialchars($avis['commentaire'])) ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
  </div>

  <div class="booking-card">
    <div class="p-price">
      <?= number_format($prix, 2, '.', ' ') ?> TND 
      <span>/ pers.</span>
    </div>

    <?php if ($order_error): ?>
      <div class="error-msg"><?= htmlspecialchars($order_error) ?></div>
    <?php endif; ?>

    <form method="post" action="guide.php?id=<?= $id ?>">
      <input type="hidden" name="id" value="<?= $id ?>">
      
      <div class="form-group">
        <label class="form-label">Date</label>
        <input type="date" name="date" class="input-field" required>
      </div>

      <div class="form-group">
        <label class="form-label">Personnes</label>
        <input type="number" name="personnes" class="input-field" value="1" min="1" max="<?= $capacite ?>" required>
      </div>

      <div class="form-group">
        <label class="form-label">Nom complet</label>
        <input type="text" name="nom" class="input-field" value="<?= isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : '' ?>" required>
      </div>

      <div class="form-group">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="input-field" value="<?= isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : '' ?>" required>
      </div>

      <div class="form-group">
        <label class="form-label">Message (Optionnel)</label>
        <textarea name="message" class="input-field" rows="3" placeholder="Informations complémentaires..."></textarea>
      </div>

      <button type="submit" class="btn-submit">Réserver</button>
    </form>
  </div>
</div>

<footer>
    <p>&copy; <?= date('Y') ?> Tarkina. Tous droits réservés.</p>
</footer>

</body>
</html>
