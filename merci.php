<?php
session_start();
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/db.php';

$commande_id = isset($_GET['commande_id']) ? (int)$_GET['commande_id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);
$reservation_id = isset($_GET['reservation_id']) ? (int)$_GET['reservation_id'] : 0;

if ($commande_id <= 0 && $reservation_id <= 0) {
    die('Requête invalide.');
}

function formatImagePath($path, $type = '') {
    if (empty($path)) return 'https://placehold.co/800x600?text=Pas+de+photo';
    if (strpos($path, 'http') === 0) return $path;
    $folder = $type ? $type . '/' : '';
    return 'uploads/' . $folder . ltrim($path, '/');
}

$title = '';
$service_name = '';
$dates = '';
$total_price = 0;
$img = '';
$user_name = $_SESSION['user_name'] ?? 'Utilisateur';
$success_msg = '';

if ($commande_id > 0) {
    // Fetch commande
    $sql = "SELECT c.*, a.titre, a.photo_principale FROM commandes c JOIN artisanat a ON c.artisanat_id = a.id WHERE c.id = ? AND c.utilisateur_id = ? LIMIT 1";
    $st = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($st, 'ii', $commande_id, $_SESSION['user_id']);
    mysqli_stmt_execute($st);
    $res = mysqli_stmt_get_result($st);
    $commande = mysqli_fetch_assoc($res);
    mysqli_stmt_close($st);

    if (!$commande) {
        die('Commande introuvable.');
    }

    $title = 'Merci pour votre commande !';
    $success_msg = 'Votre commande a été enregistrée avec succès sous le numéro <strong>#' . $commande['id'] . '</strong>. Elle sera expédiée prochainement.';
    $service_name = $commande['titre'] . ' (x' . $commande['quantite'] . ')';
    $dates = 'N/A';
    $total_price = $commande['total'];
    $img = formatImagePath($commande['photo_principale'], 'artisanat');

} elseif ($reservation_id > 0) {
    // Fetch reservation
    $sql = "SELECT * FROM reservations WHERE id = ? AND utilisateur_id = ? LIMIT 1";
    $st = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($st, 'ii', $reservation_id, $_SESSION['user_id']);
    mysqli_stmt_execute($st);
    $res = mysqli_stmt_get_result($st);
    $reservation = mysqli_fetch_assoc($res);
    mysqli_stmt_close($st);

    if (!$reservation) {
        die('Réservation introuvable.');
    }

    $title = 'Merci pour votre réservation !';
    $success_msg = 'Votre réservation a été enregistrée avec succès sous le numéro <strong>#' . $reservation['id'] . '</strong>. Le prestataire vous contactera bientôt.';
    
    // Determine the type of service
    $service = null;
    $type = '';
    
    if (!empty($reservation['logement_id'])) {
        $st_srv = mysqli_prepare($conn, "SELECT titre, prix, photo_principale FROM hebergement WHERE id = ? LIMIT 1");
        mysqli_stmt_bind_param($st_srv, 'i', $reservation['logement_id']);
        $type = 'hebergement';
    } elseif (!empty($reservation['repas_id'])) {
        $st_srv = mysqli_prepare($conn, "SELECT titre, prix, photo_principale FROM repas WHERE id = ? LIMIT 1");
        mysqli_stmt_bind_param($st_srv, 'i', $reservation['repas_id']);
        $type = 'repas';
    } elseif (!empty($reservation['guide_id'])) {
        $st_srv = mysqli_prepare($conn, "SELECT titre, prix, photo_principale FROM guide WHERE id = ? LIMIT 1");
        mysqli_stmt_bind_param($st_srv, 'i', $reservation['guide_id']);
        $type = 'guide';
    } elseif (!empty($reservation['evenement_id'])) {
        $st_srv = mysqli_prepare($conn, "SELECT titre, prix, photo_principale FROM evenement WHERE id = ? LIMIT 1");
        mysqli_stmt_bind_param($st_srv, 'i', $reservation['evenement_id']);
        $type = 'evenement';
    }

    if (isset($st_srv)) {
        mysqli_stmt_execute($st_srv);
        $res_srv = mysqli_stmt_get_result($st_srv);
        $service = mysqli_fetch_assoc($res_srv);
        mysqli_stmt_close($st_srv);
    }

    if ($service) {
        $service_name = $service['titre'];
        $img = formatImagePath($service['photo_principale'], $type);
        $prix = (float)$service['prix'];
        
        if ($type === 'hebergement') {
            $dates = date('d/m/Y', strtotime($reservation['date_debut'])) . ' - ' . date('d/m/Y', strtotime($reservation['date_fin']));
            $datetime1 = new DateTime($reservation['date_debut']);
            $datetime2 = new DateTime($reservation['date_fin']);
            $interval = $datetime1->diff($datetime2);
            $nuits = max(1, $interval->days);
            $total_price = $prix * $nuits;
        } else {
            $dates = date('d/m/Y', strtotime($reservation['date_debut']));
            // Since we don't store "personnes" in the reservations table currently, we display the base price.
            $total_price = $prix; 
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= $title ?> – Tarkina</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Lato:wght@400;600;700&display=swap" rel="stylesheet" />
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --cream: #f5f2ee;
      --dark: #1c1c2e;
      --navy: #1a2340;
      --orange: #e8642c;
      --white: #ffffff;
      --border: #e0dbd4;
      --radius: 14px;
      --green: #2ecc71;
    }
    body { font-family: 'Lato', sans-serif; background: var(--cream); color: var(--dark); font-size: 15px; line-height: 1.7; display: flex; flex-direction: column; min-height: 100vh; }
 
    nav { background: var(--white); border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; padding: 0 56px; height: 60px; }
    .nav-logo { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 800; color: var(--dark); text-decoration: none; }
    .nav-logo span { color: var(--orange); }

    .main-container { flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px 20px; }
    
    .merci-card { background: var(--white); border-radius: var(--radius); padding: 48px; max-width: 600px; width: 100%; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid var(--border); }
    
    .success-icon { display: inline-flex; align-items: center; justify-content: center; width: 80px; height: 80px; background: rgba(46, 204, 113, 0.1); border-radius: 50%; color: var(--green); margin-bottom: 24px; }
    .success-icon svg { width: 40px; height: 40px; }
    
    h1 { font-family: 'Playfair Display', serif; font-size: 32px; font-weight: 800; color: var(--dark); margin-bottom: 12px; }
    p.subtitle { color: #666; margin-bottom: 32px; font-size: 16px; }

    .order-summary { background: var(--cream); border-radius: 8px; padding: 24px; text-align: left; margin-bottom: 32px; border: 1px solid var(--border); }
    
    .product-info { display: flex; align-items: center; gap: 16px; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid var(--border); }
    .product-info img { width: 60px; height: 60px; border-radius: 8px; object-fit: cover; }
    .product-info h3 { font-size: 16px; margin: 0; font-family: 'Lato', sans-serif; }
    
    .summary-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px; }
    .summary-row.total { font-weight: 800; font-size: 18px; margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--border); }
    
    .btn-return { display: inline-block; background: var(--navy); color: var(--white); text-decoration: none; padding: 14px 28px; border-radius: 8px; font-weight: 700; transition: background 0.2s; }
    .btn-return:hover { background: #111827; }

    footer { background: var(--navy); color: var(--white); padding: 48px 56px; text-align: center; }
  </style>
</head>
<body>

<nav class="navbar">
  <a href="index.php" class="nav-logo">
    <img src="assets/img/logo.png" alt="TARKINA" onerror="this.style.display='none';this.nextElementSibling.style.display='inline'">
    <span style="display:none">Tarkina</span>
  </a>
  <ul class="nav-links">
    <li><a href="index.php">Accueil</a></li>
    <li><a href="explorer.php">Explorer</a></li>
    <li><a href="about.php">À propos</a></li>
    <li><a href="contact.php">Contact</a></li>
  </ul>
  <div class="nav-auth">
    <?php if(isset($_SESSION['user_id'])): ?>
      <a href="profile.php" class="btn-nav-primary">Mon Profil</a>
      <a href="logout.php" class="btn-nav-outline">Déconnexion</a>
    <?php else: ?>
      <a href="login.php" class="btn-nav-outline">Connexion</a>
      <a href="register.php" class="btn-nav-primary">S'inscrire</a>
    <?php endif; ?>
  </div>
</nav>

<div class="main-container">
  <div class="merci-card">
    <div class="success-icon">
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
    </div>
    
    <h1><?= $title ?></h1>
    <p class="subtitle"><?= $success_msg ?></p>

    <div class="order-summary">
      <div class="product-info">
        <img src="<?= htmlspecialchars($img) ?>" alt="Service">
        <div>
          <h3><?= htmlspecialchars($service_name) ?></h3>
        </div>
      </div>
      
      <div class="summary-row">
        <span>Client</span>
        <span><?= htmlspecialchars($user_name) ?></span>
      </div>
      
      <?php if ($dates !== 'N/A'): ?>
      <div class="summary-row">
        <span>Date(s)</span>
        <span><?= htmlspecialchars($dates) ?></span>
      </div>
      <?php endif; ?>

      <div class="summary-row total">
        <span>Total estimé</span>
        <span><?= number_format($total_price, 2, '.', ' ') ?> TND</span>
      </div>
    </div>

    <a href="index.php" class="btn-return">Retour à l'accueil</a>
  </div>
</div>

<footer>
    <p>&copy; <?= date('Y') ?> Tarkina. Tous droits réservés.</p>
</footer>

</body>
</html>
