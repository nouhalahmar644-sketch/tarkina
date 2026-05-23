<?php
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/service_helpers.php';

mysqli_set_charset($conn, 'utf8mb4');
service_ensure_reservations_table($conn);

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) { header('Location: explorer.php'); exit; }
$item = service_fetch_item($conn, 'artisanat', $id);
if (!$item) { header('Location: explorer.php'); exit; }

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
        $errorMsg = 'Veuillez remplir tous les champs obligatoires.';
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
        $successMsg = $ok ? 'Commande enregistrée avec succès !' : 'Erreur lors de la commande.';
    }
}
$desc = trim((string) ($item['description'] ?? 'Produit artisanal authentique fabriqué à la main.'));
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

<button onclick="history.back()" style="display:inline-flex;align-items:center;gap:8px;margin:14px 0 0 24px;background:#fff;border:1.5px solid #e2ddd8;border-radius:50px;padding:8px 18px;color:#1B3A4B;cursor:pointer;font-weight:600;font-size:.9rem;font-family:inherit;transition:all .2s;" onmouseover="this.style.borderColor='#E05A2B';this.style.color='#E05A2B'" onmouseout="this.style.borderColor='#e2ddd8';this.style.color='#1B3A4B'">&#8592; Retour</button>

<div class="container py-4">


  <div class="row g-4">
    <div class="col-lg-6">
      <img id="mainProductImg" class="artisanat-main-img" src="<?= htmlspecialchars($mainPhoto) ?>" alt="<?= htmlspecialchars($item['titre']) ?>">
      <div class="artisanat-thumbs">
        <?php foreach ($thumbs as $i => $th): ?>
          <img src="<?= htmlspecialchars($th) ?>" alt="" class="thumb-img <?= $i === 0 ? 'active' : '' ?>" data-src="<?= htmlspecialchars($th) ?>">
        <?php endforeach; ?>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="service-cat">ARTISANAT · <?= htmlspecialchars($regionNomDisplay) ?></div>
      <h1 class="service-title"><?= htmlspecialchars($item['titre']) ?></h1>
      <div class="service-meta">
        <span>⭐ <?= $__sum['count'] > 0 ? number_format($__sum['avg'], 1) : '—' ?> · <?= (int) $__sum['count'] ?> avis</span>
        <span>📍 <?= htmlspecialchars(service_localisation($item)) ?></span>
      </div>
      <div class="booking-price" style="margin:16px 0;"><?= number_format($prix, 0) ?> TND</div>
      <p class="service-desc text-muted"><?= nl2br(htmlspecialchars($desc)) ?></p>

      <?php if ($successMsg): ?><div class="flash-ok"><?= htmlspecialchars($successMsg) ?></div><?php endif; ?>
      <?php if ($errorMsg): ?><div class="flash-err"><?= htmlspecialchars($errorMsg) ?></div><?php endif; ?>

      <?php if (empty($_SESSION['user_id'])): ?>
        <div class="login-prompt order-box"><p>Connectez-vous pour commander</p><a href="login.php">Connexion</a> · <a href="register.php">Créer un compte</a></div>
      <?php else: ?>
      <form method="post" class="order-box">
        <label class="form-label small fw-bold">Quantité</label>
        <div class="qty-wrap">
          <button type="button" class="qty-btn" id="qtyMinus">−</button>
          <span class="qty-val" id="qtyVal">1</span>
          <input type="hidden" name="quantite" id="quantite" value="1">
          <button type="button" class="qty-btn" id="qtyPlus">+</button>
        </div>
        <div class="row g-2 mb-3">
          <div class="col-6"><label class="form-label small fw-bold">Nom</label><input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>" required></div>
          <div class="col-6"><label class="form-label small fw-bold">Email</label><input type="email" name="email" class="form-control" required></div>
        </div>
        <div class="mb-3"><label class="form-label small fw-bold">Adresse de livraison</label><input type="text" name="adresse" class="form-control" required></div>
        <hr class="booking-sep">
        <div class="booking-total-row"><span>Total</span><span id="totalDisplay"><?= number_format($prix, 0) ?> TND</span></div>
        <button type="submit" class="btn-book">Commander</button>
      </form>
      <?php endif; ?>
    </div>
  </div>

  <div class="service-section" style="margin-top:24px;">
    <h3>Avis des clients</h3>
    <?php $serviceType = 'artisanat'; $serviceId = $id; include __DIR__ . '/includes/avis_section.php'; ?>
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
