<?php
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/service_helpers.php';

mysqli_set_charset($conn, 'utf8mb4');
service_ensure_reservations_table($conn);

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) { header('Location: explorer.php'); exit; }
$item = service_fetch_item($conn, 'guide', $id);
if (!$item) { header('Location: explorer.php'); exit; }

service_resolve_region($conn, $item);
$regionId = (int) ($item['region_id'] ?? 0);
$regionNom = !empty($item['region_nom']) ? $item['region_nom'] : service_localisation($item);
$mainPhoto = service_main_photo($item, 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=1200&q=80');
$gridPhotos = service_secondary_photos($item, 6, $mainPhoto);
$prix = (float) ($item['prix'] ?? 0);
$capacite = max(1, (int) ($item['capacite'] ?? 4));
$guideName = explode(' ', trim($item['titre']))[0];
$inclus = service_default_inclus();
$reviews = service_placeholder_reviews();
$successMsg = ''; $errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; }
    $nb = max(1, (int) ($_POST['nb_voyageurs'] ?? 2));
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $today = date('Y-m-d');
    if ($nom === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = 'Veuillez remplir correctement le formulaire.';
    } else {
        $total = $nb * $prix;
        $ok = service_insert_reservation($conn, [
            'user_id' => (int) $_SESSION['user_id'],
            'type_service' => 'guide', 'service_id' => $id,
            'date_debut' => $today, 'date_fin' => $today,
            'nb_voyageurs' => $nb, 'nom' => $nom, 'email' => $email, 'message' => $message,
            'prix_total' => $total,
        ]);
        $successMsg = $ok ? 'Demande envoyée avec succès !' : 'Erreur lors de la réservation.';
    }
}
$desc = trim((string) ($item['description'] ?? 'Explorez la région avec un guide local passionné.'));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($item['titre']) ?> — Tarkina</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Lato:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/service-page.css">
</head>
<body class="service-page">
<?php include __DIR__ . '/navbar.php'; ?>

<button onclick="history.back()" 
  style="background:none;border:none;cursor:pointer;font-size:1.3rem;
  color:#1B3A4B;padding:14px 0 0 24px;display:flex;align-items:center;gap:6px;"
  onmouseover="this.style.color='#E05A2B'" 
  onmouseout="this.style.color='#1B3A4B'">
  &#8592;
</button>

<div class="service-gallery-grid6">
  <?php foreach ($gridPhotos as $ph): ?><img src="<?= htmlspecialchars($ph) ?>" alt=""><?php endforeach; ?>
</div>

<div class="container py-3">

  <div class="row g-4">
    <div class="col-lg-8">
      <div class="service-cat">🧭 GUIDE LOCAL</div>
      <h1 class="service-title"><?= htmlspecialchars($item['titre']) ?></h1>
      <div class="service-meta">
        <span><?= htmlspecialchars(service_localisation($item)) ?></span>
        <span>⭐ 4.9 (64 avis)</span>
        <span>Capacité <?= $capacite ?></span>
      </div>
      <p class="service-desc"><?= nl2br(htmlspecialchars($desc)) ?></p>
      <hr>
      <div class="service-section"><h3>Ce qui est inclus</h3><div class="inclus-grid"><?php foreach ($inclus as $i): ?><div class="inclus-item"><span class="check">✓</span> <?= htmlspecialchars($i) ?></div><?php endforeach; ?></div></div>
      <hr>
      <div class="service-section"><h3>Avis des voyageurs</h3>
        <?php foreach ($reviews as $r): ?><div class="review-card"><div class="review-head"><span class="review-name"><?= htmlspecialchars($r['name']) ?></span><span class="review-stars">★★★★★</span></div><p class="mb-0 text-muted"><?= htmlspecialchars($r['text']) ?></p></div><?php endforeach; ?>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="booking-card">
        <div class="booking-price"><?= number_format($prix, 0) ?> TND <small>/ pers.</small></div>
        <?php if ($successMsg): ?><div class="flash-ok"><?= htmlspecialchars($successMsg) ?></div><?php endif; ?>
        <?php if ($errorMsg): ?><div class="flash-err"><?= htmlspecialchars($errorMsg) ?></div><?php endif; ?>
        <?php if (empty($_SESSION['user_id'])): ?>
          <div class="login-prompt"><p>Connectez-vous pour réserver</p><a href="login.php">Connexion</a> · <a href="register.php">Créer un compte</a></div>
        <?php else: ?>
        <form method="post"><hr class="booking-sep">
          <div class="mb-3"><label class="form-label small fw-bold">Personnes</label><input type="number" name="nb_voyageurs" id="nb_voyageurs" class="form-control" value="2" min="1" max="<?= $capacite ?>"></div>
          <div class="mb-3"><label class="form-label small fw-bold">Votre nom</label><input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>" required></div>
          <div class="mb-3"><label class="form-label small fw-bold">E-mail</label><input type="email" name="email" class="form-control" required></div>
          <div class="mb-3"><label class="form-label small fw-bold">Message (optionnel)</label><textarea name="message" class="form-control" rows="3" placeholder="Dites quelque chose à <?= htmlspecialchars($guideName) ?>..."></textarea></div>
          <hr class="booking-sep">
          <div class="booking-calc" id="calcLine"><?= number_format($prix, 0) ?> TND × 2 pers. → <span id="calcTotal"><?= number_format($prix * 2, 0) ?></span> TND</div>
          <div class="booking-total-row"><span>Total</span><span id="totalDisplay"><?= number_format($prix * 2, 0) ?> TND</span></div>
          <button type="submit" class="btn-book">Réserver</button>
          <p class="booking-note">Vous ne serez pas débité maintenant.</p>
        </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/footer.php'; ?>
<script>
const prix = <?= json_encode($prix) ?>;
const pers = document.getElementById('nb_voyageurs');
if (pers) {
  function upd(){ const n=Math.max(1,parseInt(pers.value||2,10)); const t=n*prix;
    document.getElementById('calcLine').innerHTML = prix.toFixed(0)+' TND × '+n+' pers. → <span id="calcTotal">'+t.toFixed(0)+'</span> TND';
    document.getElementById('totalDisplay').textContent = t.toFixed(0)+' TND'; }
  pers.addEventListener('input', upd);
}
</script>
</body>
</html>
