<?php
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/service_helpers.php';

mysqli_set_charset($conn, 'utf8mb4');
service_ensure_reservations_table($conn);

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) { header('Location: explorer.php'); exit; }
$item = service_fetch_item($conn, 'evenement', $id);
if (!$item) { header('Location: explorer.php'); exit; }
$row = $item;

service_resolve_region($conn, $item);
$regionId = (int) ($item['region_id'] ?? 0);
$regionNom = !empty($item['region_nom']) ? $item['region_nom'] : service_localisation($item);
$mainPhoto = service_main_photo($item, 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?w=1200&q=80');
$gridPhotos = service_secondary_photos($item, 6, $mainPhoto);
$prix = (float) ($item['prix'] ?? 0);
$capacite = max(1, (int) ($item['capacite'] ?? 50));
$inclus = service_default_inclus();
require_once __DIR__ . '/includes/avis_helpers.php';
$__sum = avis_summary($conn, 'evenement', $id);
$successMsg = ''; $errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; }
    $date = trim($_POST['date_reservation'] ?? '');
    $nb = max(1, (int) ($_POST['nb_voyageurs'] ?? 2));
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if ($date === '' || $nom === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = 'Veuillez remplir correctement le formulaire.';
    } else {
        $total = $nb * $prix;
        $ok = service_insert_reservation($conn, [
            'user_id' => (int) $_SESSION['user_id'],
            'type_service' => 'evenement', 'service_id' => $id,
            'date_debut' => $date, 'date_fin' => $date,
            'nb_voyageurs' => $nb, 'nom' => $nom, 'email' => $email, 'message' => $message,
            'prix_total' => $total,
        ]);
        $successMsg = $ok ? 'Inscription enregistrée avec succès !' : 'Erreur lors de l\'inscription.';
    }
}
$desc = trim((string) ($item['description'] ?? 'Participez à un événement culturel unique en Tunisie.'));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($item['titre']) ?> — Tarkina</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Lato:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/service-page.css">
</head>
<body class="service-page">
<?php include __DIR__ . '/navbar.php'; ?>

<button onclick="history.back()" style="display:inline-flex;align-items:center;gap:8px;margin:14px 0 0 24px;background:#fff;border:1.5px solid #e2ddd8;border-radius:50px;padding:8px 18px;color:#111111;cursor:pointer;font-weight:600;font-size:.9rem;font-family:inherit;transition:all .2s;" onmouseover="this.style.borderColor='#1B6B45';this.style.color='#1B6B45'" onmouseout="this.style.borderColor='#e2ddd8';this.style.color='#111111'">&#8592; Retour</button>

<div class="service-gallery-grid6" style="height:480px;">
  <img src="<?= htmlspecialchars($row['image'] ?? $row['photo'] ?? $row['photo_principale'] ?? '') ?>" alt="<?= htmlspecialchars($item['titre']) ?>" onerror="this.src='images/placeholder.jpg'">
  <?php foreach (array_slice($gridPhotos, 0, 5) as $ph): ?>
    <img src="<?= htmlspecialchars($ph) ?>" alt="" onerror="this.src='images/placeholder.jpg'">
  <?php endforeach; ?>
</div>

<div class="container py-3">

  <div class="row g-4">
    <div class="col-lg-8">
      <div class="service-cat">🎉 ÉVÉNEMENTS</div>
      <h1 class="service-title"><?= htmlspecialchars($item['titre']) ?></h1>
      <div class="service-meta">
        <span>📍 <?= htmlspecialchars(service_localisation($item)) ?></span>
        <span>⭐ <?= $__sum['count'] > 0 ? number_format($__sum['avg'], 1) : '—' ?> (<?= (int) $__sum['count'] ?> avis)</span>
        <span>👥 Capacité <?= $capacite ?></span>
      </div>
      <p class="service-desc"><?= nl2br(htmlspecialchars($desc)) ?></p>
      <div class="service-section"><h3>Ce qui est inclus</h3><div class="inclus-grid"><?php foreach ($inclus as $i): ?><div class="inclus-item"><span class="check">✓</span> <?= htmlspecialchars($i) ?></div><?php endforeach; ?></div></div>
      <div class="service-section"><h3>Avis des voyageurs</h3>
        <?php $serviceType = 'evenement'; $serviceId = $id; include __DIR__ . '/includes/avis_section.php'; ?>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="booking-card">
        <div class="booking-price"><?= number_format($prix, 0) ?> TND</div>
        <?php if ($successMsg): ?><div class="flash-ok"><?= htmlspecialchars($successMsg) ?></div><?php endif; ?>
        <?php if ($errorMsg): ?><div class="flash-err"><?= htmlspecialchars($errorMsg) ?></div><?php endif; ?>
        <?php if (empty($_SESSION['user_id'])): ?>
          <div class="login-prompt"><p>Connectez-vous pour vous inscrire</p><a href="login.php">Connexion</a> · <a href="register.php">Créer un compte</a></div>
        <?php else: ?>
        <form method="post"><hr class="booking-sep">
          <div class="mb-3"><label class="form-label small fw-bold">Date</label><input type="text" name="date_reservation" id="date_reservation" class="form-control" required></div>
          <div class="mb-3"><label class="form-label small fw-bold">Personnes</label><input type="number" name="nb_voyageurs" id="nb_voyageurs" class="form-control" value="2" min="1"></div>
          <div class="mb-3"><label class="form-label small fw-bold">Votre nom</label><input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>" required></div>
          <div class="mb-3"><label class="form-label small fw-bold">E-mail</label><input type="email" name="email" class="form-control" required></div>
          <div class="mb-3"><label class="form-label small fw-bold">Message (optionnel)</label><textarea name="message" class="form-control" rows="3"></textarea></div>
          <hr class="booking-sep">
          <div class="booking-calc" id="calcLine"><?= number_format($prix, 0) ?> TND × 2 pers. → <span id="calcTotal"><?= number_format($prix * 2, 0) ?></span> TND</div>
          <div class="booking-total-row"><span>TOTAL</span><span id="totalDisplay"><?= number_format($prix * 2, 0) ?> TND</span></div>
          <button type="submit" class="btn-book">S'inscrire</button>
          <p class="booking-note">Vous ne serez pas débité maintenant.</p>
        </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>
<script>
const prix = <?= json_encode($prix) ?>;
flatpickr('#date_reservation', { locale:'fr', dateFormat:'Y-m-d', altInput:true, altFormat:'d/m/Y', minDate:'today' });
const pers = document.getElementById('nb_voyageurs');
function upd(){ const n=Math.max(1,parseInt(pers.value||2,10)); const t=n*prix;
  document.getElementById('calcLine').innerHTML = prix.toFixed(0)+' TND × '+n+' pers. → <span id="calcTotal">'+t.toFixed(0)+'</span> TND';
  document.getElementById('totalDisplay').textContent = t.toFixed(0)+' TND'; }
pers.addEventListener('input', upd);
</script>
</body>
</html>

