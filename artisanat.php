<?php
session_start();
require_once __DIR__ . '/db.php';

// Redirect to login if form is submitted but user not logged in
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_SESSION['user_id'])) {
    $id = isset($_POST['artisanat_id']) ? (int)$_POST['artisanat_id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);
    header("Location: login.php?redirect=" . urlencode("artisanat.php?id=$id"));
    exit;
}

$artisanat_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($artisanat_id <= 0) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $artisanat_id = isset($_POST['artisanat_id']) ? (int)$_POST['artisanat_id'] : 0;
    }
}

if ($artisanat_id <= 0) {
    die('Produit invalide.');
}

// Fetch produit
$st = mysqli_prepare($conn, "SELECT * FROM artisanat WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($st, 'i', $artisanat_id);
mysqli_stmt_execute($st);
$res = mysqli_stmt_get_result($st);
$produit = mysqli_fetch_assoc($res);
mysqli_stmt_close($st);

if (!$produit) {
    die('Produit introuvable.');
}

// POST Handling (Order)
$order_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $quantite = isset($_POST['quantite']) ? (int)$_POST['quantite'] : 1;
    $adresse = trim(isset($_POST['adresse_livraison']) ? $_POST['adresse_livraison'] : '');
    $user_id = $_SESSION['user_id'];

    if ($quantite <= 0) {
        $order_error = 'Quantité invalide.';
    } elseif ($quantite > $produit['stock']) {
        $order_error = 'Stock insuffisant.';
    } elseif (empty($adresse)) {
        $order_error = 'Veuillez renseigner une adresse de livraison.';
    } else {
        $total = $quantite * $produit['prix'];
        
        // Start transaction
        mysqli_begin_transaction($conn);
        try {
            // Insert commande
            $stmt = mysqli_prepare($conn, "INSERT INTO commandes (utilisateur_id, artisanat_id, quantite, adresse_livraison, total, statut) VALUES (?, ?, ?, ?, ?, 'en attente')");
            mysqli_stmt_bind_param($stmt, 'iiisd', $user_id, $artisanat_id, $quantite, $adresse, $total);
            mysqli_stmt_execute($stmt);
            $commande_id = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);

            // Decrement stock
            $new_stock = $produit['stock'] - $quantite;
            $stmt2 = mysqli_prepare($conn, "UPDATE artisanat SET stock = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt2, 'ii', $new_stock, $artisanat_id);
            mysqli_stmt_execute($stmt2);
            mysqli_stmt_close($stmt2);

            mysqli_commit($conn);

            header("Location: merci.php?id=" . $commande_id);
            exit;
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $order_error = 'Une erreur est survenue lors de la commande.';
        }
    }
}

// Formatting images helper
function formatImagePath($path) {
    if (empty($path)) return 'https://placehold.co/800x600?text=Pas+de+photo';
    if (strpos($path, 'http') === 0) return $path;
    if (strpos($path, 'uploads/artisanat/') === 0) return $path;
    return 'uploads/artisanat/' . ltrim($path, '/');
}

$main_photo = formatImagePath($produit['photo_principale'] ?? '');
$photos_sec = [];
if (!empty($produit['photos_sec'])) {
    $dec = json_decode($produit['photos_sec'], true);
    if (is_array($dec)) {
        $photos_sec = array_map('formatImagePath', $dec);
    }
}
// fill up to 4
while (count($photos_sec) < 4) {
    $photos_sec[] = 'https://placehold.co/400x300?text=Photo';
}

$rating = number_format(rand(45, 50) / 10, 1);
$prix = (float)$produit['prix'];
$stock = (int)$produit['stock'];

$page_title = htmlspecialchars($produit['titre']) . ' – Tarkina';
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
      --orange-light: #fde8dc;
      --muted: #6b6b6b;
      --border: #e0dbd4;
      --white: #ffffff;
      --radius: 14px;
      --green: #2ecc71;
      --red: #e74c3c;
    }
    body { font-family: 'Lato', sans-serif; background: var(--cream); color: var(--dark); font-size: 15px; line-height: 1.7; }
 
    /* NAV */
    nav { background: var(--white); border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; padding: 0 56px; height: 60px; position: sticky; top: 0; z-index: 100; }
    .nav-logo { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 800; color: var(--dark); text-decoration: none; display: flex; align-items: center; gap: 4px; }
    .nav-logo span { color: var(--orange); }
    .nav-links { display: flex; gap: 32px; list-style: none; }
    .nav-links a { text-decoration: none; color: var(--dark); font-size: 14px; font-weight: 600; opacity: .7; transition: opacity .2s; }
    .nav-links a:hover { opacity: 1; }
    .nav-actions { display: flex; align-items: center; gap: 16px; }
    .btn-nav { background: var(--orange); color: var(--white); border: none; border-radius: 8px; padding: 9px 22px; font-size: 14px; font-weight: 700; cursor: pointer; text-decoration:none; }

    /* BREADCRUMB */
    .breadcrumb { padding: 16px 56px; font-size: 13px; color: var(--muted); }
    .breadcrumb a { color: var(--dark); text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; }
    .breadcrumb a:hover { text-decoration: underline; }

    /* LAYOUT */
    .product-container { display: grid; grid-template-columns: 1fr 400px; gap: 48px; padding: 10px 56px 64px; align-items: start; }
    
    @media (max-width: 900px) {
        .product-container { grid-template-columns: 1fr; }
    }

    /* GALLERY */
    .gallery { display: flex; flex-direction: column; gap: 12px; }
    .main-image { width: 100%; height: 500px; object-fit: cover; border-radius: var(--radius); }
    .thumbnails { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
    .thumbnails img { width: 100%; height: 100px; object-fit: cover; border-radius: 8px; cursor: pointer; transition: opacity 0.2s; border: 2px solid transparent; }
    .thumbnails img:hover { opacity: 0.8; }
    .thumbnails img.active { border-color: var(--orange); }

    /* DETAILS */
    .product-details { background: var(--white); padding: 32px; border-radius: var(--radius); border: 1px solid var(--border); position: sticky; top: 80px; }
    .p-tag { font-size: 11px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: var(--orange); margin-bottom: 8px; }
    .p-title { font-family: 'Playfair Display', serif; font-size: 32px; font-weight: 800; color: var(--dark); line-height: 1.2; margin-bottom: 12px; }
    
    .p-meta { display: flex; align-items: center; gap: 16px; margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid var(--border); }
    .p-rating { display: flex; align-items: center; gap: 6px; font-weight: 700; }
    .p-rating svg { fill: var(--orange); width: 16px; height: 16px; }
    .p-stock { font-size: 13px; font-weight: 600; padding: 4px 10px; border-radius: 20px; }
    .stock-ok { background: rgba(46, 204, 113, 0.1); color: var(--green); }
    .stock-out { background: rgba(231, 76, 60, 0.1); color: var(--red); }

    .p-price { font-size: 28px; font-weight: 800; color: var(--dark); margin-bottom: 24px; display: flex; align-items: baseline; gap: 6px; }
    .p-price span { font-size: 14px; color: var(--muted); font-weight: 400; }

    /* FORM */
    .form-group { margin-bottom: 20px; }
    .form-label { display: block; font-size: 13px; font-weight: 700; margin-bottom: 8px; color: var(--dark); }
    
    .qty-selector { display: inline-flex; align-items: center; border: 1px solid var(--border); border-radius: 8px; overflow: hidden; background: #fff; }
    .qty-btn { width: 40px; height: 40px; background: none; border: none; font-size: 18px; font-weight: 600; cursor: pointer; color: var(--dark); transition: background 0.2s; }
    .qty-btn:hover { background: var(--cream); }
    .qty-input { width: 50px; height: 40px; border: none; border-left: 1px solid var(--border); border-right: 1px solid var(--border); text-align: center; font-size: 15px; font-weight: 600; -moz-appearance: textfield; }
    .qty-input::-webkit-outer-spin-button, .qty-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }

    .input-field { width: 100%; padding: 12px 14px; border: 1px solid var(--border); border-radius: 8px; font-family: 'Lato', sans-serif; font-size: 15px; transition: border-color 0.2s; }
    .input-field:focus { outline: none; border-color: var(--orange); }

    .user-info { background: var(--cream); padding: 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
    .user-info strong { display: block; margin-bottom: 4px; color: var(--dark); }
    
    .total-row { display: flex; justify-content: space-between; align-items: center; font-size: 18px; font-weight: 800; margin: 24px 0; padding-top: 24px; border-top: 1px solid var(--border); }
    
    .btn-submit { width: 100%; background: var(--orange); color: var(--white); border: none; border-radius: 8px; padding: 16px; font-size: 16px; font-weight: 700; cursor: pointer; transition: background 0.2s; }
    .btn-submit:hover { background: #d65a25; }
    .btn-submit:disabled { background: var(--muted); cursor: not-allowed; opacity: 0.7; }

    .error-msg { background: rgba(231, 76, 60, 0.1); color: var(--red); padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; font-weight: 600; }

    .description-block { margin-top: 20px; }
    .description-block h3 { font-family: 'Playfair Display', serif; font-size: 22px; margin-bottom: 12px; }
    .description-block p { font-size: 15px; color: #555; white-space: pre-wrap; }

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
  <a href="explorer.php">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
    Retour
  </a>
</div>

<div class="product-container">
  
  <!-- Left: Images & Description -->
  <div class="left-column">
    <div class="gallery">
      <img id="mainImage" class="main-image" src="<?= htmlspecialchars($main_photo) ?>" alt="<?= htmlspecialchars($produit['titre']) ?>" />
      <div class="thumbnails">
        <?php foreach ($photos_sec as $idx => $photo): ?>
          <img class="thumb <?= $idx === 0 ? 'active' : '' ?>" src="<?= htmlspecialchars($photo) ?>" onclick="changeImage(this, '<?= htmlspecialchars($photo) ?>')" alt="Thumbnail" />
        <?php endforeach; ?>
      </div>
    </div>

    <div class="description-block">
      <h3>Description du produit</h3>
      <p><?= htmlspecialchars($produit['description']) ?></p>
    </div>
  </div>

  <!-- Right: Details & Checkout -->
  <div class="product-details">
    <div class="p-tag">Artisanat local · <?= htmlspecialchars($produit['localisation']) ?></div>
    <h1 class="p-title"><?= htmlspecialchars($produit['titre']) ?></h1>
    
    <div class="p-meta">
      <div class="p-rating">
        <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        <?= $rating ?>
      </div>
      <?php if ($stock > 0): ?>
        <div class="p-stock stock-ok">En stock (<?= $stock ?>)</div>
      <?php else: ?>
        <div class="p-stock stock-out">Rupture de stock</div>
      <?php endif; ?>
    </div>

    <div class="p-price">
      <span id="unitPrice" data-price="<?= $prix ?>"><?= number_format($prix, 2, '.', ' ') ?></span> TND 
      <span>/ unité</span>
    </div>

    <?php if ($order_error): ?>
      <div class="error-msg"><?= htmlspecialchars($order_error) ?></div>
    <?php endif; ?>

    <form method="post" action="artisanat.php?id=<?= $artisanat_id ?>">
      <input type="hidden" name="artisanat_id" value="<?= $artisanat_id ?>">
      
      <div class="form-group">
        <label class="form-label">Quantité</label>
        <div class="qty-selector">
          <button type="button" class="qty-btn" onclick="updateQty(-1)">-</button>
          <input type="number" id="qtyInput" name="quantite" class="qty-input" value="1" min="1" max="<?= $stock > 0 ? $stock : 1 ?>" readonly>
          <button type="button" class="qty-btn" onclick="updateQty(1)">+</button>
        </div>
      </div>

      <?php if(isset($_SESSION['user_id'])): ?>
      <div class="user-info">
        <strong>Information acheteur :</strong>
        <?= htmlspecialchars($_SESSION['user_name'] ?? 'Utilisateur') ?> 
        (<?= htmlspecialchars($_SESSION['user_email'] ?? '') ?>)
      </div>
      <?php endif; ?>

      <div class="form-group">
        <label class="form-label">Adresse de livraison complète</label>
        <textarea name="adresse_livraison" class="input-field" rows="3" placeholder="Numéro, Rue, Ville, Code Postal..." required></textarea>
      </div>

      <div class="total-row">
        <span>Total</span>
        <span id="totalPrice"><?= number_format($prix, 2, '.', ' ') ?> TND</span>
      </div>

      <?php if ($stock > 0): ?>
        <button type="submit" class="btn-submit">Commander</button>
      <?php else: ?>
        <button type="button" class="btn-submit" disabled>Rupture de stock</button>
      <?php endif; ?>
    </form>
  </div>

</div>

<script>
  function changeImage(element, src) {
    document.getElementById('mainImage').src = src;
    document.querySelectorAll('.thumb').forEach(el => el.classList.remove('active'));
    element.classList.add('active');
  }

  const unitPrice = parseFloat(document.getElementById('unitPrice').dataset.price);
  const qtyInput = document.getElementById('qtyInput');
  const totalPriceEl = document.getElementById('totalPrice');
  const maxStock = <?= $stock ?>;

  function updateQty(change) {
    let current = parseInt(qtyInput.value);
    current += change;
    
    if (current < 1) current = 1;
    if (current > maxStock) current = maxStock;
    
    qtyInput.value = current;
    
    const total = (current * unitPrice).toFixed(2);
    totalPriceEl.textContent = total + ' TND';
  }
</script>

</body>
</html>
