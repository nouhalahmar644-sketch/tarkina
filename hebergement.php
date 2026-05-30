<?php
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/service_helpers.php';
require_once __DIR__ . '/includes/i18n.php';

mysqli_set_charset($conn, 'utf8mb4');
service_ensure_reservations_table($conn);

$L_ALL = [
    'fr' => [
        'back' => 'Retour',
        'cat' => '🏠 HÉBERGEMENT',
        'reviews' => 'avis',
        'capacity' => 'Capacité',
        'included' => 'Ce qui est inclus',
        'traveler_reviews' => 'Avis des voyageurs',
        'per_night' => '/nuit',
        'login_to_book' => 'Connectez-vous pour réserver',
        'login' => 'Connexion',
        'create_account' => 'Créer un compte',
        'date_arrival' => 'Date arrivée',
        'date_departure' => 'Date départ',
        'persons' => 'Personnes',
        'your_name' => 'Votre nom',
        'email' => 'E-mail',
        'message_opt' => 'Message (optionnel)',
        'secure_payment' => '💳 Paiement sécurisé',
        'card_number' => 'Numéro de carte',
        'expiration' => 'Expiration',
        'cvc' => 'CVC',
        'card_holder' => 'Titulaire de la carte',
        'card_holder_ph' => 'Nom sur la carte',
        'badges' => '🔒 Connexion chiffrée · VISA · Mastercard · Démonstration',
        'nights_word' => 'nuit(s)',
        'total' => 'Total',
        'reserve' => 'Réserver',
        'note_not_charged' => 'Vous ne serez pas débité maintenant.',
        'err_dates' => 'Veuillez sélectionner les dates.',
        'err_email' => 'E-mail invalide.',
        'err_name' => 'Veuillez indiquer votre nom.',
        'err_date_order' => "La date de départ doit être après la date d'arrivée.",
        'ok_booking' => 'Réservation enregistrée avec succès !',
        'err_booking' => 'Erreur lors de la réservation.',
        'desc_default_1' => 'Découvrez un hébergement authentique au cœur de la région.',
        'desc_default_2' => 'Un séjour chaleureux avec un hôte local pour vivre la Tunisie autrement.',
        'fp_locale' => 'fr',
    ],
    'ar' => [
        'back' => 'رجوع',
        'cat' => '🏠 إقامة',
        'reviews' => 'مراجعة',
        'capacity' => 'السعة',
        'included' => 'ما يشمله العرض',
        'traveler_reviews' => 'تقييمات المسافرين',
        'per_night' => '/الليلة',
        'login_to_book' => 'سجّل دخولك للحجز',
        'login' => 'تسجيل الدخول',
        'create_account' => 'إنشاء حساب',
        'date_arrival' => 'تاريخ الوصول',
        'date_departure' => 'تاريخ المغادرة',
        'persons' => 'الأشخاص',
        'your_name' => 'اسمك',
        'email' => 'البريد الإلكتروني',
        'message_opt' => 'رسالة (اختيارية)',
        'secure_payment' => '💳 دفع آمن',
        'card_number' => 'رقم البطاقة',
        'expiration' => 'تاريخ الانتهاء',
        'cvc' => 'رمز الأمان',
        'card_holder' => 'حامل البطاقة',
        'card_holder_ph' => 'الاسم على البطاقة',
        'badges' => '🔒 اتصال مشفّر · VISA · Mastercard · عرض تجريبي',
        'nights_word' => 'ليلة(ليالٍ)',
        'total' => 'الإجمالي',
        'reserve' => 'احجز',
        'note_not_charged' => 'لن يتمّ خصم أي مبلغ الآن.',
        'err_dates' => 'يُرجى اختيار التواريخ.',
        'err_email' => 'بريد إلكتروني غير صالح.',
        'err_name' => 'يُرجى إدخال اسمك.',
        'err_date_order' => 'يجب أن يكون تاريخ المغادرة بعد تاريخ الوصول.',
        'ok_booking' => 'تمّ تسجيل الحجز بنجاح!',
        'err_booking' => 'حدث خطأ أثناء الحجز.',
        'desc_default_1' => 'اكتشف إقامة أصيلة في قلب الجهة.',
        'desc_default_2' => 'إقامة دافئة مع مضيف محلي لاكتشاف تونس بطريقة مختلفة.',
        'fp_locale' => 'ar',
    ],
    'en' => [
        'back' => 'Back',
        'cat' => '🏠 STAY',
        'reviews' => 'reviews',
        'capacity' => 'Capacity',
        'included' => "What's included",
        'traveler_reviews' => 'Traveler reviews',
        'per_night' => '/night',
        'login_to_book' => 'Log in to book',
        'login' => 'Log in',
        'create_account' => 'Create an account',
        'date_arrival' => 'Check-in',
        'date_departure' => 'Check-out',
        'persons' => 'People',
        'your_name' => 'Your name',
        'email' => 'E-mail',
        'message_opt' => 'Message (optional)',
        'secure_payment' => '💳 Secure payment',
        'card_number' => 'Card number',
        'expiration' => 'Expiry',
        'cvc' => 'CVC',
        'card_holder' => 'Cardholder',
        'card_holder_ph' => 'Name on card',
        'badges' => '🔒 Encrypted connection · VISA · Mastercard · Demo',
        'nights_word' => 'night(s)',
        'total' => 'Total',
        'reserve' => 'Book now',
        'note_not_charged' => 'You will not be charged now.',
        'err_dates' => 'Please select the dates.',
        'err_email' => 'Invalid e-mail.',
        'err_name' => 'Please enter your name.',
        'err_date_order' => 'The check-out date must be after the check-in date.',
        'ok_booking' => 'Booking saved successfully!',
        'err_booking' => 'An error occurred during booking.',
        'desc_default_1' => 'Discover authentic lodging at the heart of the region.',
        'desc_default_2' => 'A warm stay with a local host to experience Tunisia differently.',
        'fp_locale' => 'en',
    ],
];
$L = $L_ALL[$lang] ?? $L_ALL['fr'];

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
$row = $item;

service_resolve_region($conn, $item);
$regionId = (int) ($item['region_id'] ?? 0);
$regionNom = !empty($item['region_nom']) ? $item['region_nom'] : service_localisation($item);
$mainPhoto = service_main_photo($item, 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1200&q=80');
$sidePhotos = service_secondary_photos($item, 4);
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
        $errorMsg = $L['err_dates'];
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = $L['err_email'];
    } elseif ($nom === '') {
        $errorMsg = $L['err_name'];
    } elseif (strtotime($dateFin) <= strtotime($dateDebut)) {
        $errorMsg = $L['err_date_order'];
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
            $successMsg = $L['ok_booking'];
        } else {
            $errorMsg = $L['err_booking'];
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
        $desc !== '' ? $desc : $L['desc_default_1'],
        $L['desc_default_2'],
    ];
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>" dir="<?= htmlspecialchars($dir) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($item['titre']) ?> — Tarkina</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Lato:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/service-page.css">
  <link rel="stylesheet" href="assets/css/rtl.css">
  <style>
    .back-btn{display:inline-flex;align-items:center;gap:8px;margin:14px 0 0 24px;background:#fff;border:1.5px solid #e2ddd8;border-radius:50px;padding:8px 18px;color:#0b1c30;cursor:pointer;font-weight:600;font-size:.9rem;font-family:inherit;text-decoration:none;transition:all .2s;}
    .back-btn:hover{border-color:#f16e22;color:#f16e22;}
    .pay-box{background:#FFFFFF;border:1px solid #ece7e1;border-radius:12px;padding:14px;margin-top:4px;}
    .pay-head{font-weight:700;color:#0b1c30;font-size:.9rem;margin-bottom:10px;display:flex;align-items:center;gap:6px;}
    .pay-box .form-control{font-size:.9rem;}
    .pay-badges{font-size:.72rem;color:#8a8a8a;margin-top:8px;letter-spacing:.3px;}
  </style>
</head>
<body class="service-page">

<?php include __DIR__ . '/navbar.php'; ?>

<button onclick="history.back()" class="back-btn">&#8592; <?= htmlspecialchars($L['back']) ?></button>

<?php $sideCount = count($sidePhotos); ?>

<?php if ($sideCount === 0): ?>
  <!-- Single image: full-width hero -->
  <div class="service-gallery-full" style="height:480px;">
    <div style="width:100%;height:100%;">
      <img src="<?= htmlspecialchars($row['image'] ?? $row['photo'] ?? $row['photo_principale'] ?? '') ?>" alt="<?= htmlspecialchars($item['titre']) ?>" style="width:100%;height:100%;object-fit:cover;display:block;" onerror="this.src='images/placeholder.jpg'">
    </div>
  </div>

<?php else: ?>
  <div class="service-gallery-full">
    <div class="service-gallery-main">
      <img src="<?= htmlspecialchars($row['image'] ?? $row['photo'] ?? $row['photo_principale'] ?? '') ?>" alt="<?= htmlspecialchars($item['titre']) ?>" onerror="this.src='images/placeholder.jpg'">
    </div>
    <div class="service-gallery-side"
         style="grid-template-rows: repeat(<?= $sideCount <= 2 ? $sideCount : 2 ?>, 1fr);
                grid-template-columns: <?= $sideCount === 1 ? '1fr' : '1fr 1fr' ?>;">
      <?php foreach ($sidePhotos as $ph): ?>
        <img src="<?= htmlspecialchars($ph) ?>" alt="" onerror="this.src='images/placeholder.jpg'">
      <?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>


<div class="container py-3">


  <div class="row g-4">
    <div class="col-lg-8">
      <div class="service-cat"><?= htmlspecialchars($L['cat']) ?></div>
      <h1 class="service-title"><?= htmlspecialchars($item['titre']) ?></h1>
      <div class="service-meta">
        <span>📍 <?= htmlspecialchars(service_localisation($item)) ?></span>
        <span>⭐ <?= $note ?> (<?= $nbAvis ?> <?= htmlspecialchars($L['reviews']) ?>)</span>
        <span>👥 <?= htmlspecialchars($L['capacity']) ?> <?= $capacite ?></span>
      </div>

      <?php foreach (array_slice($descParts, 0, 2) as $para): ?>
        <p class="service-desc"><?= nl2br(htmlspecialchars($para)) ?></p>
      <?php endforeach; ?>

      <div class="service-section">
        <h3><?= htmlspecialchars($L['included']) ?></h3>
        <div class="inclus-grid">
          <?php foreach ($inclus as $inc): ?>
            <div class="inclus-item"><span class="check">✓</span> <?= htmlspecialchars($inc) ?></div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="service-section">
        <h3><?= htmlspecialchars($L['traveler_reviews']) ?></h3>
        <?php $serviceType = 'hebergement'; $serviceId = $id; include __DIR__ . '/includes/avis_section.php'; ?>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="booking-card">
        <div class="booking-price"><?= number_format($prix, 0, '.', ' ') ?> TND <small><?= htmlspecialchars($L['per_night']) ?></small></div>

        <?php if ($successMsg !== ''): ?>
          <div class="flash-ok"><?= htmlspecialchars($successMsg) ?></div>
        <?php endif; ?>
        <?php if ($errorMsg !== ''): ?>
          <div class="flash-err"><?= htmlspecialchars($errorMsg) ?></div>
        <?php endif; ?>

        <?php if (empty($_SESSION['user_id'])): ?>
          <div class="login-prompt">
            <p><?= htmlspecialchars($L['login_to_book']) ?></p>
            <a href="login.php"><?= htmlspecialchars($L['login']) ?></a> · <a href="register.php"><?= htmlspecialchars($L['create_account']) ?></a>
          </div>
        <?php else: ?>
          <form method="post" id="bookingForm">
            <hr class="booking-sep">
            <div class="row g-2 mb-3">
              <div class="col-6">
                <label class="form-label small fw-bold"><?= htmlspecialchars($L['date_arrival']) ?></label>
                <input type="text" name="date_debut" id="date_debut" class="form-control" required>
              </div>
              <div class="col-6">
                <label class="form-label small fw-bold"><?= htmlspecialchars($L['date_departure']) ?></label>
                <input type="text" name="date_fin" id="date_fin" class="form-control" required>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label small fw-bold"><?= htmlspecialchars($L['persons']) ?></label>
              <input type="number" name="nb_voyageurs" id="nb_voyageurs" class="form-control" value="2" min="1" max="<?= $capacite ?>">
            </div>
            <div class="mb-3">
              <label class="form-label small fw-bold"><?= htmlspecialchars($L['your_name']) ?></label>
              <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label small fw-bold"><?= htmlspecialchars($L['email']) ?></label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label small fw-bold"><?= htmlspecialchars($L['message_opt']) ?></label>
              <textarea name="message" class="form-control" rows="3"></textarea>
            </div>

            <div class="pay-box">
              <div class="pay-head"><?= htmlspecialchars($L['secure_payment']) ?></div>
              <div class="mb-2">
                <label class="form-label small fw-bold"><?= htmlspecialchars($L['card_number']) ?></label>
                <input type="text" id="cardNum" inputmode="numeric" class="form-control" placeholder="1234 5678 9012 3456" maxlength="19" required>
              </div>
              <div class="row g-2 mb-2">
                <div class="col-7">
                  <label class="form-label small fw-bold"><?= htmlspecialchars($L['expiration']) ?></label>
                  <input type="text" id="cardExp" inputmode="numeric" class="form-control" placeholder="MM/AA" maxlength="5" required>
                </div>
                <div class="col-5">
                  <label class="form-label small fw-bold"><?= htmlspecialchars($L['cvc']) ?></label>
                  <input type="text" inputmode="numeric" class="form-control" placeholder="123" maxlength="4" required>
                </div>
              </div>
              <div>
                <label class="form-label small fw-bold"><?= htmlspecialchars($L['card_holder']) ?></label>
                <input type="text" class="form-control" placeholder="<?= htmlspecialchars($L['card_holder_ph']) ?>" required>
              </div>
              <div class="pay-badges"><?= htmlspecialchars($L['badges']) ?></div>
            </div>

            <hr class="booking-sep">
            <div class="booking-calc" id="calcLine"><?= number_format($prix, 0) ?> TND × 1 <?= htmlspecialchars($L['nights_word']) ?> → <span id="calcTotal"><?= number_format($prix, 0) ?></span> TND</div>
            <div class="booking-total-row"><span><?= htmlspecialchars($L['total']) ?></span><span id="totalDisplay"><?= number_format($prix, 0) ?> TND</span></div>
            <button type="submit" class="btn-book"><?= htmlspecialchars($L['reserve']) ?></button>
            <p class="booking-note"><?= htmlspecialchars($L['note_not_charged']) ?></p>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<?php if ($lang === 'fr'): ?><script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script><?php endif; ?>
<?php if ($lang === 'ar'): ?><script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ar.js"></script><?php endif; ?>
<script>
(function() {
  const prix = <?= json_encode($prix) ?>;
  const NIGHT_WORD = <?= json_encode($L['nights_word']) ?>;
  const fpOpts = { locale: <?= json_encode($L['fp_locale']) ?>, dateFormat: 'Y-m-d', altInput: true, altFormat: 'd/m/Y', minDate: 'today' };
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
      prix.toFixed(0) + ' TND × ' + nights + ' ' + NIGHT_WORD + ' → <span id="calcTotal">' + total.toFixed(0) + '</span> TND';
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

