<?php
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/service_helpers.php';

mysqli_set_charset($conn, 'utf8mb4');
service_ensure_reservations_table($conn);

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header('Location: explorer.php');
    exit;
}

$item = service_fetch_item($conn, 'hebergement', $id);
if (!$item) {
    header('Location: explorer.php');
    exit;
}

service_resolve_region($conn, $item);
$regionId = (int) ($item['region_id'] ?? 0);
$regionNom = !empty($item['region_nom']) ? $item['region_nom'] : service_localisation($item);
$mainPhoto = service_main_photo($item, 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1200&q=80');
$sidePhotos = service_secondary_photos($item, 4, $mainPhoto);
$prix = (float) ($item['prix'] ?? 0);
$capacite = max(1, (int) ($item['capacite'] ?? 2));
require_once __DIR__ . '/includes/avis_helpers.php';
$__sum = avis_summary($conn, 'hebergement', $id);
$note = $__sum['count'] > 0 ? number_format($__sum['avg'], 1) : '—';
$nbAvis = $__sum['count'];
$inclus = service_default_inclus();

$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }

    $dateDebut = trim($_POST['date_debut'] ?? '');
    $dateFin = trim($_POST['date_fin'] ?? '');
    $nbVoyageurs = max(1, (int) ($_POST['nb_voyageurs'] ?? 2));
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($dateDebut === '' || $dateFin === '') {
        $errorMsg = 'Veuillez sélectionner les dates.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = 'E-mail invalide.';
    } elseif ($nom === '') {
        $errorMsg = 'Veuillez indiquer votre nom.';
    } elseif (strtotime($dateFin) <= strtotime($dateDebut)) {
        $errorMsg = 'La date de départ doit être après la date d\'arrivée.';
    } else {
        $nights = (int) ((strtotime($dateFin) - strtotime($dateDebut)) / 86400);
        if ($nights < 1) {
            $nights = 1;
        }
        $prixTotal = $nights * $prix;

        $sqlError = '';
        $resId = service_insert_reservation($conn, [
            'user_id' => (int) $_SESSION['user_id'],
            'type_service' => 'hebergement',
            'service_id' => $id,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'nb_voyageurs' => $nbVoyageurs,
            'nom' => $nom,
            'email' => $email,
            'message' => $message,
            'prix_total' => $prixTotal,
        ], $sqlError);

        if ($resId) {
            $successMsg = 'Réservation enregistrée avec succès !';
        } else {
            $errorMsg = 'Erreur lors de la réservation.';
            if ($sqlError !== '') {
                $errorMsg .= ' (' . $sqlError . ')';
            } else {
                $errorMsg .= ' ' . mysqli_error($conn);
            }
        }
    }
}

$desc = trim((string) ($item['description'] ?? ''));
$descParts = $desc !== '' ? preg_split("/\n\s*\n/", $desc) : [];
if (count($descParts) < 2) {
    $descParts = [
        $desc !== '' ? $desc : 'Découvrez un hébergement authentique au cœur de la région.',
        'Un séjour chaleureux avec un hôte local pour vivre la Tunisie autrement.',
    ];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($item['titre']) ?> — Tarkina</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Lato:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/service-page.css">
  <style>
    .back-btn{display:inline-flex;align-items:center;gap:8px;margin:14px 0 0 24px;background:#fff;border:1.5px solid #e2ddd8;border-radius:50px;padding:8px 18px;color:#1B3A4B;cursor:pointer;font-weight:600;font-size:.9rem;font-family:inherit;text-decoration:none;transition:all .2s;}
    .back-btn:hover{border-color:#E05A2B;color:#E05A2B;}
    .pay-box{background:#FAF8F5;border:1px solid #ece7e1;border-radius:12px;padding:14px;margin-top:4px;}
    .pay-head{font-weight:700;color:#1B3A4B;font-size:.9rem;margin-bottom:10px;display:flex;align-items:center;gap:6px;}
    .pay-box .form-control{font-size:.9rem;}
    .pay-badges{font-size:.72rem;color:#8a8a8a;margin-top:8px;letter-spacing:.3px;}
  </style>
</head>
<body class="service-page">

<?php include __DIR__ . '/navbar.php'; ?>

<button onclick="history.back()" class="back-btn">&#8592; Retour</button>

<div class="service-gallery-full">
  <div class="service-gallery-main"><img src="<?= htmlspecialchars($mainPhoto) ?>" alt="<?= htmlspecialchars($item['titre']) ?>"></div>
  <div class="service-gallery-side">
    <?php foreach ($sidePhotos as $ph): ?>
      <img src="<?= htmlspecialchars($ph) ?>" alt="">
    <?php endforeach; ?>
  </div>
</div>

<div class="container py-3">


  <div class="row g-4">
    <div class="col-lg-8">
      <div class="service-cat">🏠 HÉBERGEMENT</div>
      <h1 class="service-title"><?= htmlspecialchars($item['titre']) ?></h1>
      <div class="service-meta">
        <span>📍 <?= htmlspecialchars(service_localisation($item)) ?></span>
        <span>⭐ <?= $note ?> (<?= $nbAvis ?> avis)</span>
        <span>👥 Capacité <?= $capacite ?></span>
      </div>

      <?php foreach (array_slice($descParts, 0, 2) as $para): ?>
        <p class="service-desc"><?= nl2br(htmlspecialchars($para)) ?></p>
      <?php endforeach; ?>

      <div class="service-section">
        <h3>Ce qui est inclus</h3>
        <div class="inclus-grid">
          <?php foreach ($inclus as $inc): ?>
            <div class="inclus-item"><span class="check">✓</span> <?= htmlspecialchars($inc) ?></div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="service-section">
        <h3>Avis des voyageurs</h3>
        <?php $serviceType = 'hebergement'; $serviceId = $id; include __DIR__ . '/includes/avis_section.php'; ?>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="booking-card">
        <div class="booking-price"><?= number_format($prix, 0, '.', ' ') ?> TND <small>/nuit</small></div>

        <?php if ($successMsg !== ''): ?>
          <div class="flash-ok"><?= htmlspecialchars($successMsg) ?></div>
        <?php endif; ?>
        <?php if ($errorMsg !== ''): ?>
          <div class="flash-err"><?= htmlspecialchars($errorMsg) ?></div>
        <?php endif; ?>

        <?php if (empty($_SESSION['user_id'])): ?>
          <div class="login-prompt">
            <p>Connectez-vous pour réserver</p>
            <a href="login.php">Connexion</a> · <a href="register.php">Créer un compte</a>
          </div>
        <?php else: ?>
          <form method="post" id="bookingForm">
            <hr class="booking-sep">
            <div class="row g-2 mb-3">
              <div class="col-6">
                <label class="form-label small fw-bold">Date arrivée</label>
                <input type="text" name="date_debut" id="date_debut" class="form-control" required>
              </div>
              <div class="col-6">
                <label class="form-label small fw-bold">Date départ</label>
                <input type="text" name="date_fin" id="date_fin" class="form-control" required>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label small fw-bold">Personnes</label>
              <input type="number" name="nb_voyageurs" id="nb_voyageurs" class="form-control" value="2" min="1" max="<?= $capacite ?>">
            </div>
            <div class="mb-3">
              <label class="form-label small fw-bold">Votre nom</label>
              <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label small fw-bold">E-mail</label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label small fw-bold">Message (optionnel)</label>
              <textarea name="message" class="form-control" rows="3"></textarea>
            </div>

            <div class="pay-box">
              <div class="pay-head">💳 Paiement sécurisé</div>
              <div class="mb-2">
                <label class="form-label small fw-bold">Numéro de carte</label>
                <input type="text" id="cardNum" inputmode="numeric" class="form-control" placeholder="1234 5678 9012 3456" maxlength="19" required>
              </div>
              <div class="row g-2 mb-2">
                <div class="col-7">
                  <label class="form-label small fw-bold">Expiration</label>
                  <input type="text" id="cardExp" inputmode="numeric" class="form-control" placeholder="MM/AA" maxlength="5" required>
                </div>
                <div class="col-5">
                  <label class="form-label small fw-bold">CVC</label>
                  <input type="text" inputmode="numeric" class="form-control" placeholder="123" maxlength="4" required>
                </div>
              </div>
              <div>
                <label class="form-label small fw-bold">Titulaire de la carte</label>
                <input type="text" class="form-control" placeholder="Nom sur la carte" required>
              </div>
              <div class="pay-badges">🔒 Connexion chiffrée · VISA · Mastercard · Démonstration</div>
            </div>

            <hr class="booking-sep">
            <div class="booking-calc" id="calcLine"><?= number_format($prix, 0) ?> TND × 1 nuit(s) → <span id="calcTotal"><?= number_format($prix, 0) ?></span> TND</div>
            <div class="booking-total-row"><span>Total</span><span id="totalDisplay"><?= number_format($prix, 0) ?> TND</span></div>
            <button type="submit" class="btn-book">Réserver</button>
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
(function() {
  const prix = <?= json_encode($prix) ?>;
  const fpOpts = { locale: 'fr', dateFormat: 'Y-m-d', altInput: true, altFormat: 'd/m/Y', minDate: 'today' };
  const d1 = flatpickr('#date_debut', fpOpts);
  const d2 = flatpickr('#date_fin', fpOpts);

  function updateTotal() {
    const a = d1.selectedDates[0];
    const b = d2.selectedDates[0];
    let nights = 1;
    if (a && b && b > a) {
      nights = Math.ceil((b - a) / 86400000);
    }
    const total = nights * prix;
    document.getElementById('calcLine').innerHTML =
      prix.toFixed(0) + ' TND × ' + nights + ' nuit(s) → <span id="calcTotal">' + total.toFixed(0) + '</span> TND';
    document.getElementById('totalDisplay').textContent = total.toFixed(0) + ' TND';
  }
  d1.config.onChange.push(updateTotal);
  d2.config.onChange.push(updateTotal);
})();
</script>
<script>
(function(){
  var n=document.getElementById('cardNum');
  if(n)n.addEventListener('input',function(){this.value=this.value.replace(/\D/g,'').replace(/(.{4})/g,'$1 ').trim();});
  var e=document.getElementById('cardExp');
  if(e)e.addEventListener('input',function(){var v=this.value.replace(/\D/g,'');if(v.length>=3)v=v.slice(0,2)+'/'+v.slice(2,4);this.value=v;});
})();
</script>
</body>
</html>
