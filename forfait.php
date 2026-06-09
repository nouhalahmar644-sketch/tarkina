<?php
session_start();
mb_internal_encoding('UTF-8');
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/i18n.php';
require_once __DIR__ . '/includes/service_helpers.php';
mysqli_set_charset($conn, 'utf8mb4');
service_ensure_reservations_table($conn);

// ---------- Translations ----------
$L_ALL = [
    'fr' => [
        'page_title'   => 'Forfait — Tarkina',
        'err_invalid'  => 'Forfait invalide.',
        'err_notfound' => 'Forfait introuvable.',
        'back'         => 'Retour',
        'cat_label'    => 'PACK · TUNISIE',
        'in_region'    => 'À',
        'about_title'  => 'À propos de ce pack',
        'includes_h'   => "Ce qui est inclus",
        'reviews_h'    => 'Avis des voyageurs',
        'savings_lbl'  => 'Économie',
        'per_person'   => '/ pers.',
        'login_to_book'=> 'Connectez-vous pour réserver',
        'login'        => 'Connexion',
        'register'     => 'Créer un compte',
        'l_arrival'    => 'Date arrivée',
        'l_departure'  => 'Date départ',
        'l_persons'    => 'Voyageurs',
        'l_name'       => 'Votre nom',
        'l_email'      => 'E-mail',
        'l_message'    => 'Message (optionnel)',
        'btn_book'     => 'Réserver le forfait',
        'no_charge'    => 'Vous ne serez pas débité maintenant.',
        'calc_line'    => 'pers. × %d nuit(s) → %s TND',
        'total'        => 'Total',
        'days'         => 'jours',
        'err_dates'    => 'Veuillez sélectionner les dates.',
        'err_email'    => 'E-mail invalide.',
        'err_name'     => 'Veuillez indiquer votre nom.',
        'err_date_end' => "La date de départ doit être après la date d'arrivée.",
        'ok_booked'    => 'Réservation enregistrée avec succès ! Notre équipe vous contactera très vite.',
        'err_booking'  => 'Erreur lors de la réservation.',
        'see_service'  => 'Voir le service',
        'price_from'   => 'À partir de',
    ],
    'ar' => [
        'page_title'   => 'باقة — تاركينا',
        'err_invalid'  => 'الباقة غير صالحة.',
        'err_notfound' => 'الباقة غير موجودة.',
        'back'         => 'رجوع',
        'cat_label'    => 'باقة · تونس',
        'in_region'    => 'في',
        'about_title'  => 'حول هذه الباقة',
        'includes_h'   => 'ما تشمله الباقة',
        'reviews_h'    => 'آراء المسافرين',
        'savings_lbl'  => 'التوفير',
        'per_person'   => '/ للشخص',
        'login_to_book'=> 'سجّل دخولك للحجز',
        'login'        => 'تسجيل الدخول',
        'register'     => 'إنشاء حساب',
        'l_arrival'    => 'تاريخ الوصول',
        'l_departure'  => 'تاريخ المغادرة',
        'l_persons'    => 'المسافرون',
        'l_name'       => 'اسمك',
        'l_email'      => 'البريد الإلكتروني',
        'l_message'    => 'رسالة (اختيارية)',
        'btn_book'     => 'احجز الباقة',
        'no_charge'    => 'لن يتمّ خصم أي مبلغ الآن.',
        'calc_line'    => 'شخص × %d ليلة → %s دينار',
        'total'        => 'الإجمالي',
        'days'         => 'أيام',
        'err_dates'    => 'يُرجى اختيار التواريخ.',
        'err_email'    => 'بريد إلكتروني غير صالح.',
        'err_name'     => 'يُرجى إدخال اسمك.',
        'err_date_end' => 'يجب أن يكون تاريخ المغادرة بعد تاريخ الوصول.',
        'ok_booked'    => 'تمّ تسجيل الحجز بنجاح! سيتواصل معك فريقنا قريبًا.',
        'err_booking'  => 'حدث خطأ أثناء الحجز.',
        'see_service'  => 'عرض الخدمة',
        'price_from'   => 'ابتداءً من',
    ],
    'en' => [
        'page_title'   => 'Pack — Tarkina',
        'err_invalid'  => 'Invalid pack.',
        'err_notfound' => 'Pack not found.',
        'back'         => 'Back',
        'cat_label'    => 'PACK · TUNISIA',
        'in_region'    => 'in',
        'about_title'  => 'About this pack',
        'includes_h'   => "What's included",
        'reviews_h'    => 'Traveler reviews',
        'savings_lbl'  => 'Savings',
        'per_person'   => '/ pers.',
        'login_to_book'=> 'Sign in to book',
        'login'        => 'Sign in',
        'register'     => 'Create an account',
        'l_arrival'    => 'Check-in',
        'l_departure'  => 'Check-out',
        'l_persons'    => 'Travelers',
        'l_name'       => 'Your name',
        'l_email'      => 'Email',
        'l_message'    => 'Message (optional)',
        'btn_book'     => 'Book this pack',
        'no_charge'    => "You won't be charged now.",
        'calc_line'    => 'pers. × %d night(s) → %s TND',
        'total'        => 'Total',
        'days'         => 'days',
        'err_dates'    => 'Please select the dates.',
        'err_email'    => 'Invalid email.',
        'err_name'     => 'Please enter your name.',
        'err_date_end' => 'Check-out must be after check-in.',
        'ok_booked'    => 'Booking saved! Our team will contact you shortly.',
        'err_booking'  => 'Booking error.',
        'see_service'  => 'View the service',
        'price_from'   => 'From',
    ],
];
$L = $L_ALL[$lang] ?? $L_ALL['fr'];

// ---------- Auto-create tables (defensive) ----------
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS packs (id INT AUTO_INCREMENT PRIMARY KEY, titre VARCHAR(255) NOT NULL, slogan VARCHAR(500) NOT NULL DEFAULT '', region_id INT NOT NULL, image_path VARCHAR(500) NOT NULL DEFAULT '', prix_original DECIMAL(10,2) NOT NULL DEFAULT 0, prix_final DECIMAL(10,2) NOT NULL DEFAULT 0, statut VARCHAR(20) NOT NULL DEFAULT 'actif', position INT NOT NULL DEFAULT 0, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS pack_services (id INT AUTO_INCREMENT PRIMARY KEY, pack_id INT NOT NULL, service_type VARCHAR(20) NOT NULL, service_id INT NOT NULL, INDEX idx_pack (pack_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// ---------- Fetch pack ----------
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) { http_response_code(400); die(htmlspecialchars($L['err_invalid'])); }

$st = mysqli_prepare($conn, "SELECT p.*, r.nom AS region_nom, r.id AS region_id FROM packs p LEFT JOIN region r ON p.region_id = r.id WHERE p.id = ? AND p.statut = 'actif' LIMIT 1");
mysqli_stmt_bind_param($st, 'i', $id);
mysqli_stmt_execute($st);
$pres = mysqli_stmt_get_result($st);
$pack = mysqli_fetch_assoc($pres);
mysqli_stmt_close($st);
if (!$pack) { http_response_code(404); die(htmlspecialchars($L['err_notfound'])); }

// ---------- Fetch the pack's services with details ----------
$serviceTypes = ['hebergement','repas','guide','evenement','artisanat'];
$typeLabels   = [
    'hebergement' => ['fr'=>'Hébergement','ar'=>'إقامة','en'=>'Accommodation'],
    'repas'       => ['fr'=>'Repas maison','ar'=>'وجبة منزلية','en'=>'Home meal'],
    'guide'       => ['fr'=>'Guide local','ar'=>'مرشد محلي','en'=>'Local guide'],
    'evenement'   => ['fr'=>'Événement','ar'=>'فعالية','en'=>'Event'],
    'artisanat'   => ['fr'=>'Artisanat','ar'=>'حِرف يدوية','en'=>'Crafts'],
];
$typeIcons = ['hebergement'=>'🏠','repas'=>'🍽️','guide'=>'🧭','evenement'=>'🎉','artisanat'=>'💎'];

$rsv = mysqli_prepare($conn, "SELECT service_type, service_id FROM pack_services WHERE pack_id = ?");
mysqli_stmt_bind_param($rsv, 'i', $id);
mysqli_stmt_execute($rsv);
$rsvRes = mysqli_stmt_get_result($rsv);
$linked = [];
while ($row = mysqli_fetch_assoc($rsvRes)) {
    if (in_array($row['service_type'], $serviceTypes, true)) $linked[] = $row;
}
mysqli_stmt_close($rsv);

$packServices = [];
foreach ($linked as $lk) {
    $t = $lk['service_type']; $sid = (int) $lk['service_id'];
    $col = ($t === 'artisanat') ? 'stock' : 'capacite';
    $sql = "SELECT id, titre, description, prix, photo_principale, `$col` AS capacite, localisation FROM `$t` WHERE id = ? LIMIT 1";
    $stt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stt, 'i', $sid);
    mysqli_stmt_execute($stt);
    $sres = mysqli_stmt_get_result($stt);
    if ($row = mysqli_fetch_assoc($sres)) {
        $row['type'] = $t;
        $row['type_label'] = $typeLabels[$t][$lang] ?? $typeLabels[$t]['fr'];
        $row['icon'] = $typeIcons[$t] ?? '·';
        $packServices[] = $row;
    }
    mysqli_stmt_close($stt);
}

// ---------- Resolve images ----------
function tk_img($path, $folder, $fallback) {
    if (empty($path)) return $fallback;
    if (strpos($path, 'http') === 0) return $path;
    if (strpos($path, 'images/') === 0) return $path;
    if (strpos($path, 'uploads/') === 0) return $path;
    return 'uploads/' . $folder . '/' . ltrim($path, '/');
}
$fallbackHero = 'https://images.unsplash.com/photo-1509316785289-025f5b846b35?w=1200&q=80';
$heroImg = tk_img($pack['image_path'] ?? '', 'packs', '');
if ($heroImg === '') {
    // Fallback: first service's photo
    foreach ($packServices as $s) {
        if (!empty($s['photo_principale'])) {
            $heroImg = tk_img($s['photo_principale'], $s['type'], $fallbackHero);
            break;
        }
    }
    if ($heroImg === '') $heroImg = $fallbackHero;
}
// Side thumbnails: each service's photo (up to 4)
$sideThumbs = [];
foreach ($packServices as $s) {
    if (count($sideThumbs) >= 4) break;
    if (!empty($s['photo_principale'])) {
        $sideThumbs[] = tk_img($s['photo_principale'], $s['type'], $fallbackHero);
    }
}
$thumbPad = [
    'https://images.unsplash.com/photo-1565193566173-7a0ee3dbe261?w=600&q=80',
    'https://images.unsplash.com/photo-1548013146-72479768bada?w=600&q=80',
    'https://images.unsplash.com/photo-1561625116-5f8675632053?w=600&q=80',
    'https://images.unsplash.com/photo-1539635278303-d4002c07eae3?w=600&q=80',
];
while (count($sideThumbs) < 4) { $sideThumbs[] = $thumbPad[count($sideThumbs) % 4]; }

$prixOriginal = (float) $pack['prix_original'];
$prixFinal    = (float) $pack['prix_final'];
$savings      = max(0, $prixOriginal - $prixFinal);
$savingsPct   = $prixOriginal > 0 ? round(($savings / $prixOriginal) * 100) : 0;

// ---------- POST: reservation ----------
$successMsg = '';
$errorMsg   = '';
$alreadyReserved = !empty($_SESSION['user_id'])
    && service_user_has_reservation($conn, (int) $_SESSION['user_id'], 'forfait', (int) $pack['id']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
    $dateDebut   = trim($_POST['date_debut'] ?? '');
    $dateFin     = trim($_POST['date_fin'] ?? '');
    $nbVoyageurs = max(1, (int) ($_POST['nb_voyageurs'] ?? 1));
    $nom         = trim($_POST['nom'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $message     = trim($_POST['message'] ?? '');

    if ($dateDebut === '' || $dateFin === '') {
        $errorMsg = $L['err_dates'];
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = $L['err_email'];
    } elseif ($nom === '') {
        $errorMsg = $L['err_name'];
    } elseif (strtotime($dateFin) <= strtotime($dateDebut)) {
        $errorMsg = $L['err_date_end'];
    } else {
        $prixTotal = $prixFinal * $nbVoyageurs;
        $sqlError  = '';
        $resId = service_insert_reservation($conn, [
            'user_id'      => (int) $_SESSION['user_id'],
            'type_service' => 'forfait',
            'service_id'   => $id,
            'date_debut'   => $dateDebut,
            'date_fin'     => $dateFin,
            'nb_voyageurs' => $nbVoyageurs,
            'nom'          => $nom,
            'email'        => $email,
            'message'      => $message,
            'prix_total'   => $prixTotal,
        ], $sqlError);

        if ($resId) {
            $successMsg = $L['ok_booked'];
        } else {
            $errorMsg = $L['err_booking'];
            if ($sqlError !== '') $errorMsg .= ' (' . $sqlError . ')';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>" dir="<?= htmlspecialchars($dir) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pack['titre']) ?> — Tarkina</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800;900&family=Lato:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/service-page.css">
  <link rel="stylesheet" href="assets/css/rtl.css">
  <style>
    .pack-savings-badge { display:inline-block; background:#e8f5e9; color:#2e7d32; font-size:.78rem; font-weight:700; padding:3px 10px; border-radius:999px; margin-left:8px; vertical-align:middle; }
    .pack-price-old { text-decoration:line-through; color:#999; font-size:.95rem; font-weight:500; margin-left:8px; }
    .pack-includes-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:16px; margin-top:16px; }
    .pack-svc-card { background:#fff; border:1px solid #e8e8e8; border-radius:14px; overflow:hidden; display:flex; flex-direction:column; transition:box-shadow .2s, transform .2s; text-decoration:none; color:inherit; }
    .pack-svc-card:hover { box-shadow:0 10px 28px rgba(17,17,17,.10); transform:translateY(-3px); color:inherit; }
    .pack-svc-card img { width:100%; height:150px; object-fit:cover; display:block; }
    .pack-svc-card .pack-svc-body { padding:14px 16px; flex:1; display:flex; flex-direction:column; }
    .pack-svc-card .pack-svc-type { font-size:.7rem; font-weight:800; text-transform:uppercase; letter-spacing:.7px; color:#f16e22; margin-bottom:6px; }
    .pack-svc-card .pack-svc-title { font-weight:700; font-size:.97rem; color:#111; margin:0 0 6px; line-height:1.35; }
    .pack-svc-card .pack-svc-loc { font-size:.78rem; color:#777; margin:0 0 10px; }
    .pack-svc-card .pack-svc-foot { margin-top:auto; display:flex; justify-content:space-between; align-items:center; font-size:.85rem; }
    .pack-svc-card .pack-svc-price { font-weight:700; color:#111; }
    .pack-svc-card .pack-svc-link { color:#f16e22; font-weight:600; font-size:.78rem; }
    .pack-region-pill { display:inline-flex; align-items:center; gap:6px; background:#f1eee9; padding:5px 12px; border-radius:999px; font-size:.82rem; font-weight:600; color:#333; margin-right:8px; }
  </style>
</head>
<body class="service-page">

<?php $navLight = true; include __DIR__ . '/navbar.php'; ?>

<button onclick="history.back()" class="tk-back-fab">&#8592; <?= htmlspecialchars($L['back']) ?></button>

<div class="service-gallery-full">
  <div class="service-gallery-main"><img src="<?= htmlspecialchars($heroImg) ?>" alt="<?= htmlspecialchars($pack['titre']) ?>" onerror="this.src='<?= htmlspecialchars($fallbackHero) ?>'"></div>
  <div class="service-gallery-side">
    <?php foreach ($sideThumbs as $thumb): ?>
      <img src="<?= htmlspecialchars($thumb) ?>" alt="" onerror="this.src='<?= htmlspecialchars($fallbackHero) ?>'">
    <?php endforeach; ?>
  </div>
</div>

<div class="container py-3">
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="service-cat">🎁 <?= htmlspecialchars($L['cat_label']) ?></div>
      <h1 class="service-title">
        <?= htmlspecialchars($pack['titre']) ?>
        <?php if ($savings > 0): ?>
          <span class="pack-savings-badge">-<?= $savingsPct ?>%</span>
        <?php endif; ?>
      </h1>
      <div class="service-meta" style="margin-bottom:18px;">
        <span class="pack-region-pill">📍 <?= htmlspecialchars($pack['region_nom'] ?: '') ?></span>
        <span>👥 <?= count($packServices) ?> services</span>
      </div>

      <?php if (!empty($pack['slogan'])): ?>
        <p class="service-desc" style="font-size:1.05rem; color:#444; line-height:1.7;"><?= nl2br(htmlspecialchars($pack['slogan'])) ?></p>
      <?php endif; ?>

      <div class="service-section">
        <h3><?= htmlspecialchars($L['includes_h']) ?></h3>
        <div class="pack-includes-grid">
          <?php foreach ($packServices as $s):
            $simg = tk_img($s['photo_principale'] ?? '', $s['type'], $fallbackHero);
          ?>
          <a href="<?= htmlspecialchars($s['type']) ?>.php?id=<?= (int) $s['id'] ?>" class="pack-svc-card" target="_blank">
            <img src="<?= htmlspecialchars($simg) ?>" alt="<?= htmlspecialchars($s['titre']) ?>" loading="lazy" onerror="this.src='<?= htmlspecialchars($fallbackHero) ?>'">
            <div class="pack-svc-body">
              <div class="pack-svc-type"><?= $s['icon'] ?> <?= htmlspecialchars($s['type_label']) ?></div>
              <p class="pack-svc-title"><?= htmlspecialchars($s['titre']) ?></p>
              <?php if (!empty($s['localisation'])): ?>
                <p class="pack-svc-loc">📍 <?= htmlspecialchars($s['localisation']) ?></p>
              <?php endif; ?>
              <div class="pack-svc-foot">
                <span class="pack-svc-price"><?= number_format((float) $s['prix'], 0) ?> TND</span>
                <span class="pack-svc-link"><?= htmlspecialchars($L['see_service']) ?> →</span>
              </div>
            </div>
          </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="booking-card">
        <div class="booking-price" style="display:flex; align-items:baseline; gap:8px; flex-wrap:wrap;">
          <span><?= number_format($prixFinal, 0) ?> TND</span>
          <small><?= htmlspecialchars($L['per_person']) ?></small>
          <?php if ($savings > 0): ?>
            <span class="pack-price-old"><?= number_format($prixOriginal, 0) ?> TND</span>
          <?php endif; ?>
        </div>
        <?php if ($savings > 0): ?>
          <p style="margin: 4px 0 0; font-size:.85rem; color:#2e7d32; font-weight:600;">
            <?= htmlspecialchars($L['savings_lbl']) ?> : <strong><?= number_format($savings, 0) ?> TND</strong>
          </p>
        <?php endif; ?>

        <?php if ($errorMsg !== ''): ?>
          <div class="flash-err"><?= htmlspecialchars($errorMsg) ?></div>
        <?php endif; ?>

        <?php if ($successMsg !== ''): ?>
          <div class="booking-done">
            <div class="booking-done__icon">✓</div>
            <h3 class="booking-done__title">Paiement réussi !</h3>
            <p class="booking-done__msg">Votre forfait a bien été réservé. Vous recevrez un e-mail de confirmation très prochainement.</p>
            <a class="booking-done__link" href="mes-reservations.php">Voir mes réservations</a>
          </div>
        <?php elseif (empty($_SESSION['user_id'])): ?>
          <div class="login-prompt">
            <p><?= htmlspecialchars($L['login_to_book']) ?></p>
            <a href="login.php"><?= htmlspecialchars($L['login']) ?></a> · <a href="register.php"><?= htmlspecialchars($L['register']) ?></a>
          </div>
        <?php elseif ($alreadyReserved): ?>
          <div class="booking-done">
            <div class="booking-done__icon">✓</div>
            <h3 class="booking-done__title">Déjà réservé</h3>
            <p class="booking-done__msg">Vous avez déjà réservé ce forfait. Retrouvez le détail dans votre espace.</p>
            <a class="booking-done__link" href="mes-reservations.php">Voir mes réservations</a>
          </div>
        <?php else: ?>
          <form method="post" id="bookingForm" class="booking-flow">
            <hr class="booking-sep">

            <div class="bk-step bk-step--info">
              <div class="bk-step-head"><span class="bk-step-head__num">1</span> Vos informations</div>
              <div class="row g-2 mb-3">
                <div class="col-6">
                  <label class="form-label small fw-bold"><?= htmlspecialchars($L['l_arrival']) ?></label>
                  <input type="text" name="date_debut" id="date_debut" class="form-control" required>
                </div>
                <div class="col-6">
                  <label class="form-label small fw-bold"><?= htmlspecialchars($L['l_departure']) ?></label>
                  <input type="text" name="date_fin" id="date_fin" class="form-control" required>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label small fw-bold"><?= htmlspecialchars($L['l_persons']) ?></label>
                <input type="number" name="nb_voyageurs" id="nb_voyageurs" class="form-control" value="1" min="1" max="20">
              </div>
              <div class="mb-3">
                <label class="form-label small fw-bold"><?= htmlspecialchars($L['l_name']) ?></label>
                <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>" required>
              </div>
              <div class="mb-3">
                <label class="form-label small fw-bold"><?= htmlspecialchars($L['l_email']) ?></label>
                <input type="email" name="email" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label small fw-bold"><?= htmlspecialchars($L['l_message']) ?></label>
                <textarea name="message" class="form-control" rows="3"></textarea>
              </div>
              <hr class="booking-sep">
              <div class="booking-calc" id="calcLine"><?= number_format($prixFinal, 0) ?> TND × 1 <?= htmlspecialchars($L['l_persons']) ?> → <span id="calcTotal"><?= number_format($prixFinal, 0) ?></span> TND</div>
              <div class="booking-total-row"><span><?= htmlspecialchars($L['total']) ?></span><span id="totalDisplay"><?= number_format($prixFinal, 0) ?> TND</span></div>
              <button type="button" class="btn-book bk-next">Continuer vers le paiement →</button>
            </div>

            <div class="bk-step bk-step--payment" hidden>
              <button type="button" class="bk-back">← Retour aux informations</button>
              <div class="bk-step-head"><span class="bk-step-head__num">2</span> Paiement sécurisé</div>
              <div class="pay-box">
                <div class="mb-2"><label class="form-label small fw-bold">Numéro de carte</label><input type="text" inputmode="numeric" class="form-control" placeholder="1234 5678 9012 3456" maxlength="19" required></div>
                <div class="row g-2 mb-2">
                  <div class="col-7"><label class="form-label small fw-bold">Expiration</label><input type="text" inputmode="numeric" class="form-control" placeholder="MM/AA" maxlength="5" required></div>
                  <div class="col-5"><label class="form-label small fw-bold">CVC</label><input type="text" inputmode="numeric" class="form-control" placeholder="123" maxlength="4" required></div>
                </div>
                <div><label class="form-label small fw-bold">Titulaire</label><input type="text" class="form-control" placeholder="Nom sur la carte" required></div>
              </div>
              <hr class="booking-sep">
              <div class="booking-total-row"><span><?= htmlspecialchars($L['total']) ?></span><span><?= number_format($prixFinal, 0) ?> TND</span></div>
              <button type="submit" class="btn-book">Payer</button>
              <p class="booking-note"><?= htmlspecialchars($L['no_charge']) ?></p>
            </div>
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
  const prix = <?= json_encode($prixFinal) ?>;
  const PERS_WORD = <?= json_encode($L['l_persons']) ?>;
  const fpOpts = { locale: <?= json_encode($lang === 'fr' ? 'fr' : ($lang === 'ar' ? 'ar' : 'default')) ?>, dateFormat: 'Y-m-d', altInput: true, altFormat: 'd M Y', minDate: 'today' };
  const d1 = flatpickr('#date_debut', fpOpts);
  const d2 = flatpickr('#date_fin', fpOpts);

  function updateTotal() {
    const persons = parseInt(document.getElementById('nb_voyageurs').value, 10) || 1;
    const total = persons * prix;
    document.getElementById('calcLine').innerHTML =
      prix.toFixed(0) + ' TND × ' + persons + ' ' + PERS_WORD + ' → <span id="calcTotal">' + total.toFixed(0) + '</span> TND';
    document.getElementById('totalDisplay').textContent = total.toFixed(0) + ' TND';
  }
  document.getElementById('nb_voyageurs')?.addEventListener('input', updateTotal);
})();
</script>
<script src="assets/js/service-lightbox.js"></script>
</body>
</html>
