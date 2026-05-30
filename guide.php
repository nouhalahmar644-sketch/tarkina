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
        'cat' => '🧭 GUIDE LOCAL',
        'reviews' => 'avis',
        'capacity' => 'Capacité',
        'included' => 'Ce qui est inclus',
        'traveler_reviews' => 'Avis des voyageurs',
        'per_person' => '/ pers.',
        'login_to_book' => 'Connectez-vous pour réserver',
        'login' => 'Connexion',
        'create_account' => 'Créer un compte',
        'persons' => 'Personnes',
        'your_name' => 'Votre nom',
        'email' => 'E-mail',
        'message_opt' => 'Message (optionnel)',
        'msg_placeholder_pre' => 'Dites quelque chose à ',
        'msg_placeholder_post' => '...',
        'pers_short' => 'pers.',
        'total' => 'Total',
        'reserve' => 'Réserver',
        'note_not_charged' => 'Vous ne serez pas débité maintenant.',
        'err_form' => 'Veuillez remplir correctement le formulaire.',
        'ok_request' => 'Demande envoyée avec succès !',
        'err_booking' => 'Erreur lors de la réservation.',
        'desc_default' => 'Explorez la région avec un guide local passionné.',
    ],
    'ar' => [
        'back' => 'رجوع',
        'cat' => '🧭 مرشد محلي',
        'reviews' => 'مراجعة',
        'capacity' => 'السعة',
        'included' => 'ما يشمله العرض',
        'traveler_reviews' => 'تقييمات المسافرين',
        'per_person' => '/ للشخص',
        'login_to_book' => 'سجّل دخولك للحجز',
        'login' => 'تسجيل الدخول',
        'create_account' => 'إنشاء حساب',
        'persons' => 'الأشخاص',
        'your_name' => 'اسمك',
        'email' => 'البريد الإلكتروني',
        'message_opt' => 'رسالة (اختيارية)',
        'msg_placeholder_pre' => 'اكتب رسالة إلى ',
        'msg_placeholder_post' => '...',
        'pers_short' => 'شخص',
        'total' => 'الإجمالي',
        'reserve' => 'احجز',
        'note_not_charged' => 'لن يتمّ خصم أي مبلغ الآن.',
        'err_form' => 'يُرجى ملء النموذج بشكل صحيح.',
        'ok_request' => 'تمّ إرسال الطلب بنجاح!',
        'err_booking' => 'حدث خطأ أثناء الحجز.',
        'desc_default' => 'اكتشف الجهة مع مرشد محلي شغوف.',
    ],
    'en' => [
        'back' => 'Back',
        'cat' => '🧭 LOCAL GUIDE',
        'reviews' => 'reviews',
        'capacity' => 'Capacity',
        'included' => "What's included",
        'traveler_reviews' => 'Traveler reviews',
        'per_person' => '/ person',
        'login_to_book' => 'Log in to book',
        'login' => 'Log in',
        'create_account' => 'Create an account',
        'persons' => 'People',
        'your_name' => 'Your name',
        'email' => 'E-mail',
        'message_opt' => 'Message (optional)',
        'msg_placeholder_pre' => 'Say something to ',
        'msg_placeholder_post' => '...',
        'pers_short' => 'pers.',
        'total' => 'Total',
        'reserve' => 'Book now',
        'note_not_charged' => 'You will not be charged now.',
        'err_form' => 'Please fill in the form correctly.',
        'ok_request' => 'Request sent successfully!',
        'err_booking' => 'An error occurred during booking.',
        'desc_default' => 'Explore the region with a passionate local guide.',
    ],
];
$L = $L_ALL[$lang] ?? $L_ALL['fr'];

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) { header('Location: explorer.php'); exit; }
$item = service_fetch_item($conn, 'guide', $id);
if (!$item) { header('Location: explorer.php'); exit; }
$row = $item;

service_resolve_region($conn, $item);
$regionId = (int) ($item['region_id'] ?? 0);
$regionNom = !empty($item['region_nom']) ? $item['region_nom'] : service_localisation($item);
$mainPhoto = service_main_photo($item, 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=1200&q=80');
$gridPhotos = service_secondary_photos($item, 6, $mainPhoto);
$prix = (float) ($item['prix'] ?? 0);
$capacite = max(1, (int) ($item['capacite'] ?? 4));
$guideName = explode(' ', trim($item['titre']))[0];
$inclus = service_default_inclus();
require_once __DIR__ . '/includes/avis_helpers.php';
$__sum = avis_summary($conn, 'guide', $id);
$successMsg = ''; $errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; }
    $nb = max(1, (int) ($_POST['nb_voyageurs'] ?? 2));
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $today = date('Y-m-d');
    if ($nom === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = $L['err_form'];
    } else {
        $total = $nb * $prix;
        $ok = service_insert_reservation($conn, [
            'user_id' => (int) $_SESSION['user_id'],
            'type_service' => 'guide', 'service_id' => $id,
            'date_debut' => $today, 'date_fin' => $today,
            'nb_voyageurs' => $nb, 'nom' => $nom, 'email' => $email, 'message' => $message,
            'prix_total' => $total,
        ]);
        $successMsg = $ok ? $L['ok_request'] : $L['err_booking'];
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
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Lato:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/service-page.css">
  <link rel="stylesheet" href="assets/css/rtl.css">
</head>
<body class="service-page">
<?php include __DIR__ . '/navbar.php'; ?>

<button onclick="history.back()" style="display:inline-flex;align-items:center;gap:8px;margin:14px 0 0 24px;background:#fff;border:1.5px solid #e2ddd8;border-radius:50px;padding:8px 18px;color:#111111;cursor:pointer;font-weight:600;font-size:.9rem;font-family:inherit;transition:all .2s;" onmouseover="this.style.borderColor='#1B6B45';this.style.color='#1B6B45'" onmouseout="this.style.borderColor='#e2ddd8';this.style.color='#111111'">&#8592; <?= htmlspecialchars($L['back']) ?></button>

<?php
$allPhotos = array_merge([$mainPhoto], $gridPhotos);
$allPhotos = array_unique(array_filter($allPhotos));
?>

<div class="container py-3">

  <div class="row g-4">
    <div class="col-lg-7">
      
      <!-- Photo Gallery: main photo + thumbnail grid below it -->
      <div class="mb-4">
        <img id="mainProductImg" class="artisanat-main-img" src="<?= htmlspecialchars($mainPhoto) ?>" alt="<?= htmlspecialchars($item['titre']) ?>" style="width: 100%; border-radius: 12px; height: 380px; object-fit: cover; margin-bottom: 12px;" onerror="this.src='images/placeholder.jpg'">
        <?php if (count($allPhotos) > 1): ?>
          <div class="artisanat-thumbs" style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 8px;">
            <?php foreach ($allPhotos as $i => $ph): ?>
              <img src="<?= htmlspecialchars($ph) ?>" alt="" class="thumb-img <?= $i === 0 ? 'active' : '' ?>" data-src="<?= htmlspecialchars($ph) ?>" style="height: 70px; width: 100%; object-fit: cover; border-radius: 8px; cursor: pointer; border: 2px solid transparent; transition: all 0.2s;" onerror="this.src='images/placeholder.jpg'">
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <div class="service-cat"><?= htmlspecialchars($L['cat']) ?></div>
      <h1 class="service-title"><?= htmlspecialchars($item['titre']) ?></h1>
      <div class="service-meta">
        <span>📍 <?= htmlspecialchars(service_localisation($item)) ?></span>
        <span>⭐ <?= $__sum['count'] > 0 ? number_format($__sum['avg'], 1) : '—' ?> (<?= (int) $__sum['count'] ?> <?= htmlspecialchars($L['reviews']) ?>)</span>
        <span>👥 <?= htmlspecialchars($L['capacity']) ?> <?= $capacite ?></span>
      </div>
      <p class="service-desc"><?= nl2br(htmlspecialchars($desc)) ?></p>
      <hr>
      <div class="service-section"><h3><?= htmlspecialchars($L['included']) ?></h3><div class="inclus-grid"><?php foreach ($inclus as $i): ?><div class="inclus-item"><span class="check">✓</span> <?= htmlspecialchars($i) ?></div><?php endforeach; ?></div></div>
      <hr>
      <div class="service-section"><h3><?= htmlspecialchars($L['traveler_reviews']) ?></h3>
        <?php $serviceType = 'guide'; $serviceId = $id; include __DIR__ . '/includes/avis_section.php'; ?>
      </div>
    </div>
    <div class="col-lg-5">
      <div class="booking-card">
        <div class="booking-price"><?= number_format($prix, 0) ?> TND <small><?= htmlspecialchars($L['per_person']) ?></small></div>
        <?php if ($successMsg): ?><div class="flash-ok"><?= htmlspecialchars($successMsg) ?></div><?php endif; ?>
        <?php if ($errorMsg): ?><div class="flash-err"><?= htmlspecialchars($errorMsg) ?></div><?php endif; ?>
        <?php if (empty($_SESSION['user_id'])): ?>
          <div class="login-prompt"><p><?= htmlspecialchars($L['login_to_book']) ?></p><a href="login.php"><?= htmlspecialchars($L['login']) ?></a> · <a href="register.php"><?= htmlspecialchars($L['create_account']) ?></a></div>
        <?php else: ?>
        <form method="post"><hr class="booking-sep">
          <div class="mb-3"><label class="form-label small fw-bold"><?= htmlspecialchars($L['persons']) ?></label><input type="number" name="nb_voyageurs" id="nb_voyageurs" class="form-control" value="2" min="1" max="<?= $capacite ?>"></div>
          <div class="mb-3"><label class="form-label small fw-bold"><?= htmlspecialchars($L['your_name']) ?></label><input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>" required></div>
          <div class="mb-3"><label class="form-label small fw-bold"><?= htmlspecialchars($L['email']) ?></label><input type="email" name="email" class="form-control" required></div>
          <div class="mb-3"><label class="form-label small fw-bold"><?= htmlspecialchars($L['message_opt']) ?></label><textarea name="message" class="form-control" rows="3" placeholder="<?= htmlspecialchars($L['msg_placeholder_pre'] . $guideName . $L['msg_placeholder_post']) ?>"></textarea></div>
          <hr class="booking-sep">
          <div class="booking-calc" id="calcLine"><?= number_format($prix, 0) ?> TND × 2 <?= htmlspecialchars($L['pers_short']) ?> → <span id="calcTotal"><?= number_format($prix * 2, 0) ?></span> TND</div>
          <div class="booking-total-row"><span><?= htmlspecialchars($L['total']) ?></span><span id="totalDisplay"><?= number_format($prix * 2, 0) ?> TND</span></div>
          <button type="submit" class="btn-book"><?= htmlspecialchars($L['reserve']) ?></button>
          <p class="booking-note"><?= htmlspecialchars($L['note_not_charged']) ?></p>
        </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/footer.php'; ?>
<script>
const prix = <?= json_encode($prix) ?>;
const PERS_WORD = <?= json_encode($L['pers_short']) ?>;
const pers = document.getElementById('nb_voyageurs');
if (pers) {
  function upd(){ const n=Math.max(1,parseInt(pers.value||2,10)); const t=n*prix;
    document.getElementById('calcLine').innerHTML = prix.toFixed(0)+' TND × '+n+' '+PERS_WORD+' → <span id="calcTotal">'+t.toFixed(0)+'</span> TND';
    document.getElementById('totalDisplay').textContent = t.toFixed(0)+' TND'; }
  pers.addEventListener('input', upd);
}
document.querySelectorAll('.thumb-img').forEach(img=>{
  img.addEventListener('click', ()=>{
    document.getElementById('mainProductImg').src = img.dataset.src;
    document.querySelectorAll('.thumb-img').forEach(t=>t.classList.remove('active'));
    img.classList.add('active');
  });
});
</script>
</body>
</html>

