<?php
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/db.php';

$user_id = $_SESSION['user_id'];
$success_msg = '';
$error_msg = '';

// Handle cancel action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel') {
    $res_id = isset($_POST['reservation_id']) ? (int)$_POST['reservation_id'] : 0;
    if ($res_id > 0) {
        $stmt_check = mysqli_prepare($conn, "SELECT statut FROM reservations WHERE id = ? AND utilisateur_id = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt_check, 'ii', $res_id, $user_id);
        mysqli_stmt_execute($stmt_check);
        $res_check = mysqli_stmt_get_result($stmt_check);
        $row_check = mysqli_fetch_assoc($res_check);
        mysqli_stmt_close($stmt_check);

        if ($row_check && ($row_check['statut'] === 'en_attente' || $row_check['statut'] === 'confirmée')) {
            $stmt_update = mysqli_prepare($conn, "UPDATE reservations SET statut = 'annulée' WHERE id = ?");
            mysqli_stmt_bind_param($stmt_update, 'i', $res_id);
            if (mysqli_stmt_execute($stmt_update)) {
                $success_msg = 'La réservation a été annulée avec succès.';
            } else {
                $error_msg = 'Erreur lors de l\'annulation de la réservation.';
            }
            mysqli_stmt_close($stmt_update);
        } else {
            $error_msg = 'Cette réservation ne peut pas être annulée.';
        }
    }
}

// Fetch user info for sidebar
$user_sql = 'SELECT nom, prenom, email, adresse, role FROM utilisateur WHERE id = ? LIMIT 1';
$u_stmt = mysqli_prepare($conn, $user_sql);
mysqli_stmt_bind_param($u_stmt, 'i', $user_id);
mysqli_stmt_execute($u_stmt);
$user_res = mysqli_stmt_get_result($u_stmt);
$user = mysqli_fetch_assoc($user_res);
mysqli_stmt_close($u_stmt);

// Fetch all reservations for the user
$sql = "
    SELECT r.*,
           h.titre AS hebergement_titre, h.photo_principale AS h_img,
           rp.titre AS repas_titre, rp.photo_principale AS rp_img,
           g.titre AS guide_titre, g.photo_principale AS g_img,
           e.titre AS evenement_titre, e.photo_principale AS e_img,
           reg.nom AS region_nom
    FROM reservations r
    LEFT JOIN hebergement h ON r.logement_id = h.id
    LEFT JOIN repas rp ON r.repas_id = rp.id
    LEFT JOIN guide g ON r.guide_id = g.id
    LEFT JOIN evenement e ON r.evenement_id = e.id
    LEFT JOIN region reg ON (h.localisation = reg.nom OR rp.localisation = reg.nom OR g.localisation = reg.nom OR e.localisation = reg.nom)
    WHERE r.utilisateur_id = ?
    ORDER BY r.created_at DESC
";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$reservations = [];
while ($row = mysqli_fetch_assoc($result)) {
    $reservations[] = $row;
}
mysqli_stmt_close($stmt);

function formatImg($path, $type) {
    if (empty($path)) return 'https://placehold.co/800x600?text=Pas+de+photo';
    if (strpos($path, 'http') === 0) return $path;
    return 'uploads/' . $type . '/' . ltrim($path, '/');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mes Réservations – Tarkina</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Lato:wght@400;500;600;700&display=swap" rel="stylesheet" />
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
      --red: #e74c3c;
      --muted: #6b6b6b;
      --sidebar: #fbf9f6;
    }
    body { font-family: 'Lato', sans-serif; background: var(--cream); color: var(--dark); font-size: 15px; line-height: 1.6; }
 
    nav { background: var(--white); border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; padding: 0 56px; height: 60px; position: sticky; top: 0; z-index: 100; }
    .nav-logo { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 800; color: var(--dark); text-decoration: none; }
    .nav-logo span { color: var(--orange); }
    .nav-links { display: flex; gap: 32px; list-style: none; }
    .nav-links a { text-decoration: none; color: var(--dark); font-size: 14px; font-weight: 600; opacity: .7; transition: opacity .2s; }
    .nav-links a:hover { opacity: 1; }
    .btn-nav { background: var(--orange); color: var(--white); border: none; border-radius: 8px; padding: 9px 22px; font-size: 14px; font-weight: 700; cursor: pointer; text-decoration:none; }

    .main-grid { max-width: 1300px; margin: 40px auto; padding: 0 56px; display: grid; grid-template-columns: 300px 1fr; gap: 48px; }

    /* Sidebar */
    .sidebar { background: var(--white); border: 1px solid var(--border); border-radius: var(--radius); padding: 32px; align-self: start; }
    .sidebar-user { margin-bottom: 32px; }
    .sidebar-user h2 { font-family: 'Playfair Display', serif; font-size: 22px; margin-bottom: 4px; }
    .sidebar-user p { color: var(--muted); font-size: 13px; }

    .sidebar-menu { list-style: none; display: flex; flex-direction: column; gap: 8px; }
    .menu-item { display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 10px; text-decoration: none; color: var(--dark); font-weight: 600; font-size: 14px; transition: all 0.2s; }
    .menu-item.active { background: var(--orange); color: var(--white); }
    .menu-item:not(.active):hover { background: var(--cream); }
    .menu-item svg { width: 18px; height: 18px; opacity: 0.8; }
    .menu-item.active svg { opacity: 1; }

    .menu-divider { height: 1px; background: var(--border); margin: 16px 0; }
    .menu-logout { color: var(--orange); }

    /* Content Area */
    .content-title { font-family: 'Playfair Display', serif; font-size: 32px; font-weight: 800; margin-bottom: 32px; }
    .alert { padding: 16px; border-radius: 10px; margin-bottom: 24px; font-weight: 600; font-size: 14px; }
    .alert-success { background: #eafaf1; color: var(--green); border: 1px solid #d4f2e1; }
    .alert-error { background: #fdf2f2; color: var(--red); border: 1px solid #f9e2e2; }

    .res-list { display: flex; flex-direction: column; gap: 20px; }
    .res-card { background: var(--white); border-radius: var(--radius); border: 1px solid var(--border); display: flex; overflow: hidden; position: relative; }
    .res-img-wrap { width: 240px; height: 180px; flex-shrink: 0; }
    .res-img-wrap img { width: 100%; height: 100%; object-fit: cover; }
    
    .res-info { flex: 1; padding: 24px; display: flex; flex-direction: column; justify-content: center; }
    .res-card-title { font-family: 'Playfair Display', serif; font-size: 20px; font-weight: 800; margin-bottom: 8px; }
    .res-card-meta { font-size: 13px; color: var(--muted); margin-bottom: 12px; font-weight: 500; }
    .res-price { font-size: 18px; font-weight: 800; color: var(--dark); }

    .res-status-wrap { position: absolute; top: 20px; right: 24px; text-align: right; }
    .badge { padding: 4px 12px; border-radius: 50px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
    .badge-en_attente { background: #fdf4f0; color: var(--orange); }
    .badge-confirmée, .badge-confirmee { background: #eafaf1; color: var(--green); }
    .badge-annulée, .badge-annulee { background: #fdf2f2; color: var(--red); }

    .res-card-actions { margin-top: 16px; display: flex; gap: 12px; }
    .btn-action { padding: 8px 16px; border-radius: 8px; border: 1px solid var(--border); background: var(--white); color: var(--dark); font-size: 13px; font-weight: 700; cursor: pointer; text-decoration: none; transition: all 0.2s; }
    .btn-action:hover { background: var(--cream); border-color: var(--muted); }
    .btn-cancel { color: var(--muted); border: none; background: transparent; padding: 8px 0; }
    .btn-cancel:hover { color: var(--red); background: transparent; }

    .empty-state { text-align: center; padding: 80px 40px; background: var(--white); border-radius: var(--radius); border: 1px solid var(--border); }
    .empty-state h3 { font-family: 'Playfair Display', serif; font-size: 24px; margin-bottom: 16px; }

    footer { background: var(--navy); color: var(--white); padding: 48px 56px; text-align: center; margin-top: auto; }
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
    <a href="register.php" class="btn-nav">S'inscrire</a>
  </div>
</nav>

<div class="main-grid">
  
  <aside class="sidebar">
    <div class="sidebar-user">
      <h2><?= htmlspecialchars($user['prenom']) ?> !</h2>
      <p><?= htmlspecialchars($user['email']) ?></p>
    </div>

    <nav class="sidebar-menu">
      <a href="mes-reservations.php" class="menu-item active">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
        Mes réservations
      </a>
      <a href="mes-commandes.php" class="menu-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path><path d="M3 6h18"></path><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
        Mes commandes
      </a>
      <a href="mes-avis.php" class="menu-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 1 1-7.6-13.6 8.38 8.38 0 0 1 3.8.9L21 3z"></path></svg>
        Mes avis
      </a>
      <a href="profile.php" class="menu-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
        Mon profil
      </a>
      <div class="menu-divider"></div>
      <a href="logout.php" class="menu-item menu-logout">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
        Déconnexion
      </a>
    </nav>
  </aside>

  <main class="content">
    <h1 class="content-title">Mes réservations</h1>

    <?php if ($success_msg): ?>
      <div class="alert alert-success"><?= htmlspecialchars($success_msg) ?></div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error_msg) ?></div>
    <?php endif; ?>

    <?php if (empty($reservations)): ?>
      <div class="empty-state">
        <h3>Vous n'avez pas de réservations</h3>
        <p style="color: var(--muted); margin-bottom: 24px;">Explorez nos offres d'hébergements et d'activités pour commencer votre voyage.</p>
        <a href="explorer.php" class="btn-nav">Explorer maintenant</a>
      </div>
    <?php else: ?>
      <div class="res-list">
        <?php foreach ($reservations as $r): 
          $type = ''; $titre = ''; $img = ''; $prix = 0; $id = 0;
          if ($r['logement_id']) { $type = 'hebergement'; $titre = $r['hebergement_titre']; $img = $r['h_img']; $id = $r['logement_id']; }
          elseif ($r['repas_id']) { $type = 'repas'; $titre = $r['repas_titre']; $img = $r['rp_img']; $id = $r['repas_id']; }
          elseif ($r['guide_id']) { $type = 'guide'; $titre = $r['g_titre']; $img = $r['g_img']; $id = $r['guide_id']; }
          elseif ($r['evenement_id']) { $type = 'evenement'; $titre = $r['e_titre']; $img = $r['e_img']; $id = $r['evenement_id']; }
          
          $imageUrl = formatImg($img, $type);
          $dates = date('j', strtotime($r['date_debut'])) . ($r['date_fin'] ? ' - ' . date('j F Y', strtotime($r['date_fin'])) : ' ' . date('F Y', strtotime($r['date_debut'])));
        ?>
          <div class="res-card">
            <div class="res-img-wrap"><img src="<?= htmlspecialchars($imageUrl) ?>" alt="Image"></div>
            <div class="res-info">
              <h3 class="res-card-title"><?= htmlspecialchars($titre) ?></h3>
              <div class="res-card-meta"><?= htmlspecialchars($r['localisation'] ?? 'Tunisie') ?> · <?= $dates ?></div>
              <div class="res-price"><?= number_format(45, 0) ?> TND</div>
              
              <div class="res-card-actions">
                <a href="<?= $type ?>.php?id=<?= $id ?>" class="btn-action">Voir détails</a>
                <?php if ($r['statut'] === 'en_attente'): ?>
                  <form method="post" onsubmit="return confirm('Annuler cette réservation ?');">
                    <input type="hidden" name="action" value="cancel">
                    <input type="hidden" name="reservation_id" value="<?= $r['id'] ?>">
                    <button type="submit" class="btn-action btn-cancel">Annuler</button>
                  </form>
                <?php endif; ?>
              </div>
            </div>
            <div class="res-status-wrap">
              <span class="badge badge-<?= $r['statut'] ?>"><?= ucfirst($r['statut']) ?></span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>

</div>

<footer>
    <p>&copy; <?= date('Y') ?> Tarkina. Tous droits réservés.</p>
</footer>

</body>
</html>
