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
        'cat' => 'ARTISANAT',
        'reviews' => 'avis',
        'login_to_order' => 'Connectez-vous pour commander',
        'login' => 'Connexion',
        'create_account' => 'Créer un compte',
        'quantity' => 'Quantité',
        'name' => 'Nom',
        'email' => 'Email',
        'delivery_address' => 'Adresse de livraison',
        'total' => 'Total',
        'order' => 'Commander',
        'customer_reviews' => 'Avis des clients',
        'err_required' => 'Veuillez remplir tous les champs obligatoires.',
        'ok_order' => 'Commande enregistrée avec succès !',
        'err_order' => 'Erreur lors de la commande.',
        'desc_default' => 'Produit artisanal authentique fabriqué à la main.',
    ],
    'ar' => [
        'back' => 'رجوع',
        'cat' => 'حِرف يدوية',
        'reviews' => 'مراجعة',
        'login_to_order' => 'سجّل دخولك لإتمام الطلب',
        'login' => 'تسجيل الدخول',
        'create_account' => 'إنشاء حساب',
        'quantity' => 'الكمّية',
        'name' => 'الاسم',
        'email' => 'البريد الإلكتروني',
        'delivery_address' => 'عنوان التسليم',
        'total' => 'الإجمالي',
        'order' => 'اطلب',
        'customer_reviews' => 'تقييمات العملاء',
        'err_required' => 'يُرجى ملء جميع الحقول المطلوبة.',
        'ok_order' => 'تمّ تسجيل الطلب بنجاح!',
        'err_order' => 'حدث خطأ أثناء الطلب.',
        'desc_default' => 'منتج حرفي أصيل مصنوع يدويًا.',
    ],
    'en' => [
        'back' => 'Back',
        'cat' => 'CRAFTS',
        'reviews' => 'reviews',
        'login_to_order' => 'Log in to order',
        'login' => 'Log in',
        'create_account' => 'Create an account',
        'quantity' => 'Quantity',
        'name' => 'Name',
        'email' => 'Email',
        'delivery_address' => 'Delivery address',
        'total' => 'Total',
        'order' => 'Order',
        'customer_reviews' => 'Customer reviews',
        'err_required' => 'Please fill in all required fields.',
        'ok_order' => 'Order saved successfully!',
        'err_order' => 'An error occurred during the order.',
        'desc_default' => 'Authentic handmade craft product.',
    ],
];
$L = $L_ALL[$lang] ?? $L_ALL['fr'];

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) { header('Location: explorer.php'); exit; }
$item = service_fetch_item($conn, 'artisanat', $id);
if (!$item) { header('Location: explorer.php'); exit; }
$row = $item;

service_resolve_region($conn, $item);
$regionId = (int) ($item['region_id'] ?? 0);
$regionNom = !empty($item['region_nom']) ? $item['region_nom'] : service_localisation($item);
$regionNomDisplay = strtoupper($regionNom);
$mainPhoto = service_main_photo($item, 'https://images.unsplash.com/photo-1459411552885-841d9bcaad72?w=1200&q=80');
$thumbs = service_secondary_photos($item, 4, $mainPhoto);
$prix = (float) ($item['prix'] ?? 0);
require_once __DIR__ . '/includes/avis_helpers.php';
$__sum = avis_summary($conn, 'artisanat', $id);
$successMsg = ''; $errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; }
    $qty = max(1, (int) ($_POST['quantite'] ?? 1));
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $today = date('Y-m-d');
    if ($nom === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $adresse === '') {
        $errorMsg = $L['err_required'];
    } else {
        $total = $qty * $prix;
        $msg = 'Adresse: ' . $adresse;
        $ok = service_insert_reservation($conn, [
            'user_id' => (int) $_SESSION['user_id'],
            'type_service' => 'artisanat', 'service_id' => $id,
            'date_debut' => $today, 'date_fin' => $today,
            'nb_voyageurs' => $qty, 'nom' => $nom, 'email' => $email, 'message' => $msg,
            'prix_total' => $total,
        ]);
        $successMsg = $ok ? $L['ok_order'] : $L['err_order'];
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
$allPhotos = array_merge([$mainPhoto], $thumbs);
$allPhotos = array_unique(array_filter($allPhotos));
?>

<div class="container py-4">

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

      <div class="service-cat"><?= htmlspecialchars($L['cat']) ?> · <?= htmlspecialchars($regionNomDisplay) ?></div>
      <h1 class="service-title"><?= htmlspecialchars($item['titre']) ?></h1>
      <div class="service-meta">
        <span>⭐ <?= $__sum['count'] > 0 ? number_format($__sum['avg'], 1) : '—' ?> · <?= (int) $__sum['count'] ?> <?= htmlspecialchars($L['reviews']) ?></span>
        <span>📍 <?= htmlspecialchars(service_localisation($item)) ?></span>
      </div>
      <p class="service-desc text-muted"><?= nl2br(htmlspecialchars($desc)) ?></p>

      <div class="service-section" style="margin-top:24px;">
        <h3><?= htmlspecialchars($L['customer_reviews']) ?></h3>
        <?php $serviceType = 'artisanat'; $serviceId = $id; include __DIR__ . '/includes/avis_section.php'; ?>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="booking-card">
        <div class="booking-price"><?= number_format($prix, 0) ?> TND</div>
        <?php if ($successMsg): ?><div class="flash-ok"><?= htmlspecialchars($successMsg) ?></div><?php endif; ?>
        <?php if ($errorMsg): ?><div class="flash-err"><?= htmlspecialchars($errorMsg) ?></div><?php endif; ?>

        <?php if (empty($_SESSION['user_id'])): ?>
          <div class="login-prompt"><p><?= htmlspecialchars($L['login_to_order']) ?></p><a href="login.php"><?= htmlspecialchars($L['login']) ?></a> · <a href="register.php"><?= htmlspecialchars($L['create_account']) ?></a></div>
        <?php else: ?>
        <form method="post">
          <hr class="booking-sep">
          <label class="form-label small fw-bold"><?= htmlspecialchars($L['quantity']) ?></label>
          <div class="qty-wrap mb-3">
            <button type="button" class="qty-btn" id="qtyMinus">−</button>
            <span class="qty-val" id="qtyVal">1</span>
            <input type="hidden" name="quantite" id="quantite" value="1">
            <button type="button" class="qty-btn" id="qtyPlus">+</button>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6"><label class="form-label small fw-bold"><?= htmlspecialchars($L['name']) ?></label><input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>" required></div>
            <div class="col-6"><label class="form-label small fw-bold"><?= htmlspecialchars($L['email']) ?></label><input type="email" name="email" class="form-control" required></div>
          </div>
          <div class="mb-3"><label class="form-label small fw-bold"><?= htmlspecialchars($L['delivery_address']) ?></label><input type="text" name="adresse" class="form-control" required></div>
          <hr class="booking-sep">
          <div class="booking-total-row"><span><?= htmlspecialchars($L['total']) ?></span><span id="totalDisplay"><?= number_format($prix, 0) ?> TND</span></div>
          <button type="submit" class="btn-book"><?= htmlspecialchars($L['order']) ?></button>
        </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
<script>
const prix = <?= json_encode($prix) ?>;
let qty = 1;
const qtyVal = document.getElementById('qtyVal');
const qtyInput = document.getElementById('quantite');
const totalEl = document.getElementById('totalDisplay');
function upd(){ totalEl.textContent = (qty * prix).toFixed(0) + ' TND'; qtyVal.textContent = qty; qtyInput.value = qty; }
document.getElementById('qtyMinus')?.addEventListener('click', ()=>{ if(qty>1){ qty--; upd(); }});
document.getElementById('qtyPlus')?.addEventListener('click', ()=>{ qty++; upd(); });
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

