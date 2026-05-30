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
        'cat' => '🎉 ÉVÉNEMENTS',
        'reviews' => 'avis',
        'capacity' => 'Capacité',
        'included' => 'Ce qui est inclus',
        'traveler_reviews' => 'Avis des voyageurs',
        'login_to_signup' => 'Connectez-vous pour vous inscrire',
        'login' => 'Connexion',
        'create_account' => 'Créer un compte',
        'date' => 'Date',
        'persons' => 'Personnes',
        'your_name' => 'Votre nom',
        'email' => 'E-mail',
        'message_opt' => 'Message (optionnel)',
        'pers_short' => 'pers.',
        'total' => 'TOTAL',
        'signup' => "S'inscrire",
        'note_not_charged' => 'Vous ne serez pas débité maintenant.',
        'err_form' => 'Veuillez remplir correctement le formulaire.',
        'ok_signup' => 'Inscription enregistrée avec succès !',
        'err_signup' => "Erreur lors de l'inscription.",
        'desc_default' => 'Participez à un événement culturel unique en Tunisie.',
        'fp_locale' => 'fr',
    ],
    'ar' => [
        'back' => 'رجوع',
        'cat' => '🎉 فعالية',
        'reviews' => 'مراجعة',
        'capacity' => 'السعة',
        'included' => 'ما يشمله العرض',
        'traveler_reviews' => 'تقييمات المسافرين',
        'login_to_signup' => 'سجّل دخولك للتسجيل',
        'login' => 'تسجيل الدخول',
        'create_account' => 'إنشاء حساب',
        'date' => 'تاريخ الحجز',
        'persons' => 'الأشخاص',
        'your_name' => 'اسمك',
        'email' => 'البريد الإلكتروني',
        'message_opt' => 'رسالة (اختيارية)',
        'pers_short' => 'شخص',
        'total' => 'الإجمالي',
        'signup' => 'سجّل الآن',
        'note_not_charged' => 'لن يتمّ خصم أي مبلغ الآن.',
        'err_form' => 'يُرجى ملء النموذج بشكل صحيح.',
        'ok_signup' => 'تمّ تسجيل اشتراكك بنجاح!',
        'err_signup' => 'حدث خطأ أثناء التسجيل.',
        'desc_default' => 'شارك في فعالية ثقافية فريدة في تونس.',
        'fp_locale' => 'ar',
    ],
    'en' => [
        'back' => 'Back',
        'cat' => '🎉 EVENTS',
        'reviews' => 'reviews',
        'capacity' => 'Capacity',
        'included' => "What's included",
        'traveler_reviews' => 'Traveler reviews',
        'login_to_signup' => 'Log in to sign up',
        'login' => 'Log in',
        'create_account' => 'Create an account',
        'date' => 'Date',
        'persons' => 'People',
        'your_name' => 'Your name',
        'email' => 'E-mail',
        'message_opt' => 'Message (optional)',
        'pers_short' => 'pers.',
        'total' => 'TOTAL',
        'signup' => 'Sign up',
        'note_not_charged' => 'You will not be charged now.',
        'err_form' => 'Please fill in the form correctly.',
        'ok_signup' => 'Sign-up saved successfully!',
        'err_signup' => 'An error occurred during sign-up.',
        'desc_default' => 'Take part in a unique cultural event in Tunisia.',
        'fp_locale' => 'en',
    ],
];
$L = $L_ALL[$lang] ?? $L_ALL['fr'];

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
        $errorMsg = $L['err_form'];
    } else {
        $total = $nb * $prix;
        $ok = service_insert_reservation($conn, [
            'user_id' => (int) $_SESSION['user_id'],
            'type_service' => 'evenement', 'service_id' => $id,
            'date_debut' => $date, 'date_fin' => $date,
            'nb_voyageurs' => $nb, 'nom' => $nom, 'email' => $email, 'message' => $message,
            'prix_total' => $total,
        ]);
        $successMsg = $ok ? $L['ok_signup'] : $L['err_signup'];
    }
}
$desc = trim((string) ($item['description'] ?? $L['desc_default']));
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>" dir="<?= htmlspecialchars($dir) ?>">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($item['titre']) ?> — Tarkina</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Lato:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/service-page.css">
  <link rel="stylesheet" href="assets/css/rtl.css">
</head>
<body class="service-page">
<?php include __DIR__ . '/navbar.php'; ?>

<button onclick="history.back()" style="display:inline-flex;align-items:center;gap:8px;margin:14px 0 0 24px;background:#fff;border:1.5px solid #e2ddd8;border-radius:50px;padding:8px 18px;color:#0b1c30;cursor:pointer;font-weight:600;font-size:.9rem;font-family:inherit;transition:all .2s;" onmouseover="this.style.borderColor='#f16e22';this.style.color='#f16e22'" onmouseout="this.style.borderColor='#e2ddd8';this.style.color='#0b1c30'">&#8592; <?= htmlspecialchars($L['back']) ?></button>

<div class="service-gallery-grid6" style="height:480px;">
  <img src="<?= htmlspecialchars($row['image'] ?? $row['photo'] ?? $row['photo_principale'] ?? '') ?>" alt="<?= htmlspecialchars($item['titre']) ?>" onerror="this.src='images/placeholder.jpg'">
  <?php foreach (array_slice($gridPhotos, 0, 5) as $ph): ?>
    <img src="<?= htmlspecialchars($ph) ?>" alt="" onerror="this.src='images/placeholder.jpg'">
  <?php endforeach; ?>
</div>

<div class="container py-3">

  <div class="row g-4">
    <div class="col-lg-8">
      <div class="service-cat"><?= htmlspecialchars($L['cat']) ?></div>
      <h1 class="service-title"><?= htmlspecialchars($item['titre']) ?></h1>
      <div class="service-meta">
        <span>📍 <?= htmlspecialchars(service_localisation($item)) ?></span>
        <span>⭐ <?= $__sum['count'] > 0 ? number_format($__sum['avg'], 1) : '—' ?> (<?= (int) $__sum['count'] ?> <?= htmlspecialchars($L['reviews']) ?>)</span>
        <span>👥 <?= htmlspecialchars($L['capacity']) ?> <?= $capacite ?></span>
      </div>
      <p class="service-desc"><?= nl2br(htmlspecialchars($desc)) ?></p>
      <div class="service-section"><h3><?= htmlspecialchars($L['included']) ?></h3><div class="inclus-grid"><?php foreach ($inclus as $i): ?><div class="inclus-item"><span class="check">✓</span> <?= htmlspecialchars($i) ?></div><?php endforeach; ?></div></div>
      <div class="service-section"><h3><?= htmlspecialchars($L['traveler_reviews']) ?></h3>
        <?php $serviceType = 'evenement'; $serviceId = $id; include __DIR__ . '/includes/avis_section.php'; ?>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="booking-card">
        <div class="booking-price"><?= number_format($prix, 0) ?> TND</div>
        <?php if ($successMsg): ?><div class="flash-ok"><?= htmlspecialchars($successMsg) ?></div><?php endif; ?>
        <?php if ($errorMsg): ?><div class="flash-err"><?= htmlspecialchars($errorMsg) ?></div><?php endif; ?>
        <?php if (empty($_SESSION['user_id'])): ?>
          <div class="login-prompt"><p><?= htmlspecialchars($L['login_to_signup']) ?></p><a href="login.php"><?= htmlspecialchars($L['login']) ?></a> · <a href="register.php"><?= htmlspecialchars($L['create_account']) ?></a></div>
        <?php else: ?>
        <form method="post"><hr class="booking-sep">
          <div class="mb-3"><label class="form-label small fw-bold"><?= htmlspecialchars($L['date']) ?></label><input type="text" name="date_reservation" id="date_reservation" class="form-control" required></div>
          <div class="mb-3"><label class="form-label small fw-bold"><?= htmlspecialchars($L['persons']) ?></label><input type="number" name="nb_voyageurs" id="nb_voyageurs" class="form-control" value="2" min="1"></div>
          <div class="mb-3"><label class="form-label small fw-bold"><?= htmlspecialchars($L['your_name']) ?></label><input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>" required></div>
          <div class="mb-3"><label class="form-label small fw-bold"><?= htmlspecialchars($L['email']) ?></label><input type="email" name="email" class="form-control" required></div>
          <div class="mb-3"><label class="form-label small fw-bold"><?= htmlspecialchars($L['message_opt']) ?></label><textarea name="message" class="form-control" rows="3"></textarea></div>
          <hr class="booking-sep">
          <div class="booking-calc" id="calcLine"><?= number_format($prix, 0) ?> TND × 2 <?= htmlspecialchars($L['pers_short']) ?> → <span id="calcTotal"><?= number_format($prix * 2, 0) ?></span> TND</div>
          <div class="booking-total-row"><span><?= htmlspecialchars($L['total']) ?></span><span id="totalDisplay"><?= number_format($prix * 2, 0) ?> TND</span></div>
          <button type="submit" class="btn-book"><?= htmlspecialchars($L['signup']) ?></button>
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
const prix = <?= json_encode($prix) ?>;
const PERS_WORD = <?= json_encode($L['pers_short']) ?>;
flatpickr('#date_reservation', { locale: <?= json_encode($L['fp_locale']) ?>, dateFormat:'Y-m-d', altInput:true, altFormat:'d/m/Y', minDate:'today' });
const pers = document.getElementById('nb_voyageurs');
function upd(){ const n=Math.max(1,parseInt(pers.value||2,10)); const t=n*prix;
  document.getElementById('calcLine').innerHTML = prix.toFixed(0)+' TND × '+n+' '+PERS_WORD+' → <span id="calcTotal">'+t.toFixed(0)+'</span> TND';
  document.getElementById('totalDisplay').textContent = t.toFixed(0)+' TND'; }
pers.addEventListener('input', upd);
</script>
</body>
</html>

