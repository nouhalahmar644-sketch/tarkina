<?php
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/db.php';

$userId = (int) $_SESSION['user_id'];
$user = null;
$successMessage = '';

if (!empty($_SESSION['profile_success'])) {
    $successMessage = (string) $_SESSION['profile_success'];
    unset($_SESSION['profile_success']);
}

// Fetch user info including join date
$sql = 'SELECT nom, prenom, email, adresse, role, date_inscription FROM utilisateur WHERE id = ? LIMIT 1';
$stmt = mysqli_prepare($conn, $sql);

if ($stmt !== false) {
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $nomDb, $prenomDb, $emailDb, $adresseDb, $roleDb, $dateInsc);
    if (mysqli_stmt_fetch($stmt)) {
        $user = [
            'nom' => $nomDb,
            'prenom' => $prenomDb,
            'email' => $emailDb,
            'adresse' => $adresseDb,
            'role' => $roleDb,
            'date_inscription' => $dateInsc
        ];
    }
    mysqli_stmt_close($stmt);
}

if ($user === null) {
    header('Location: logout.php');
    exit;
}

// Fetch latest 3 reservations
$res_sql = "
    SELECT r.*,
           h.titre AS h_titre, h.photo_principale AS h_img,
           rp.titre AS rp_titre, rp.photo_principale AS rp_img,
           g.titre AS g_titre, g.photo_principale AS g_img,
           e.titre AS e_titre, e.photo_principale AS e_img
    FROM reservations r
    LEFT JOIN hebergement h ON r.logement_id = h.id
    LEFT JOIN repas rp ON r.repas_id = rp.id
    LEFT JOIN guide g ON r.guide_id = g.id
    LEFT JOIN evenement e ON r.evenement_id = e.id
    WHERE r.utilisateur_id = ?
    ORDER BY r.created_at DESC
    LIMIT 3
";
$stmt_res = mysqli_prepare($conn, $res_sql);
mysqli_stmt_bind_param($stmt_res, 'i', $userId);
mysqli_stmt_execute($stmt_res);
$latest_reservations = mysqli_stmt_get_result($stmt_res);

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
  <title>Profil - Tarkina</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Lato:wght@400;500;600;700&display=swap" rel="stylesheet">
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
      --muted: #6b6b6b;
    }
    body { font-family: 'Lato', sans-serif; background: var(--cream); color: var(--dark); font-size: 15px; line-height: 1.6; }

    nav { background: var(--white); border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; padding: 0 56px; height: 60px; }
    .nav-logo { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 800; color: var(--dark); text-decoration: none; }
    .nav-logo span { color: var(--orange); }
    .nav-links { display: flex; gap: 32px; list-style: none; }
    .nav-links a { text-decoration: none; color: var(--dark); font-size: 14px; font-weight: 600; opacity: .7; transition: opacity .2s; }
    .nav-links a:hover { opacity: 1; }
    .nav-actions { display: flex; align-items: center; gap: 16px; }
    .btn-nav { background: var(--orange); color: var(--white); border: none; border-radius: 8px; padding: 9px 22px; font-size: 14px; font-weight: 700; cursor: pointer; text-decoration:none; }

    /* Profile Header */
    .profile-header { background: var(--navy); color: var(--white); padding: 60px 56px; display: flex; align-items: center; justify-content: space-between; }
    .user-main-info { display: flex; align-items: center; gap: 24px; }
    .avatar-large { width: 100px; height: 100px; background: var(--orange); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-family: 'Playfair Display', serif; font-size: 42px; font-weight: 800; color: var(--white); }
    .user-title-group { display: flex; flex-direction: column; gap: 4px; }
    .user-role-label { font-size: 12px; text-transform: uppercase; letter-spacing: 1px; opacity: 0.7; }
    .user-fullname { font-family: 'Playfair Display', serif; font-size: 36px; font-weight: 800; line-height: 1.1; }
    .user-meta { display: flex; gap: 20px; font-size: 14px; opacity: 0.8; margin-top: 8px; }
    .user-meta span { display: flex; align-items: center; gap: 6px; }
    .btn-edit { background: var(--orange); color: var(--white); text-decoration: none; padding: 12px 24px; border-radius: 10px; font-weight: 700; font-size: 14px; transition: background 0.2s; display: flex; align-items: center; gap: 8px; }
    .btn-edit:hover { background: #d45625; }

    /* Main Grid */
    .profile-container { max-width: 1300px; margin: 0 auto; padding: 48px 56px; display: grid; grid-template-columns: 320px 1fr; gap: 48px; }
    
    .sidebar-section { background: var(--white); border-radius: var(--radius); border: 1px solid var(--border); padding: 32px; margin-bottom: 32px; }
    .sidebar-section h3 { font-family: 'Playfair Display', serif; font-size: 20px; margin-bottom: 24px; }
    .info-list { display: flex; flex-direction: column; gap: 16px; }
    .info-item { display: flex; align-items: flex-start; gap: 12px; font-size: 14px; }
    .info-icon { color: var(--orange); flex-shrink: 0; margin-top: 2px; }
    .info-text { color: var(--dark); font-weight: 500; }
    .tag-cloud { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 16px; }
    .tag { background: var(--cream); border: 1px solid var(--border); padding: 6px 14px; border-radius: 50px; font-size: 12px; font-weight: 600; color: var(--dark); }

    /* Content Area */
    .content-area { display: flex; flex-direction: column; gap: 32px; }
    .about-box { background: var(--white); border: 1px solid var(--border); border-radius: var(--radius); padding: 32px; display: flex; gap: 16px; }
    .about-icon { color: var(--orange); }
    .about-text h4 { font-family: 'Playfair Display', serif; font-size: 20px; margin-bottom: 8px; }
    .about-text p { color: var(--muted); font-size: 15px; }

    .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .section-title { font-family: 'Playfair Display', serif; font-size: 24px; font-weight: 800; }
    .section-subtitle { font-size: 12px; text-transform: uppercase; color: var(--muted); letter-spacing: 1px; font-weight: 700; margin-bottom: 4px; }
    .btn-all { color: var(--orange); font-weight: 700; text-decoration: none; font-size: 14px; border: 1px solid var(--border); padding: 6px 16px; border-radius: 8px; background: var(--white); }

    /* Reservation Cards */
    .res-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
    .res-card { background: var(--white); border-radius: var(--radius); border: 1px solid var(--border); overflow: hidden; text-decoration: none; color: inherit; transition: transform 0.2s; }
    .res-card:hover { transform: translateY(-5px); }
    .res-img { height: 160px; overflow: hidden; position: relative; }
    .res-img img { width: 100%; height: 100%; object-fit: cover; }
    .res-body { padding: 16px; }
    .res-loc { font-size: 11px; font-weight: 700; color: var(--muted); margin-bottom: 4px; }
    .res-titre { font-family: 'Playfair Display', serif; font-size: 16px; font-weight: 800; line-height: 1.2; margin-bottom: 8px; }
    .res-footer { display: flex; align-items: center; gap: 4px; font-size: 13px; font-weight: 700; color: var(--orange); }
    .res-footer svg { width: 14px; height: 14px; fill: var(--orange); }

    .empty-state { padding: 32px; text-align: center; color: var(--muted); background: var(--white); border-radius: var(--radius); border: 1px dashed var(--border); }

    .favoris-box { background: var(--white); border: 1px solid var(--border); border-radius: var(--radius); padding: 24px 32px; display: flex; align-items: center; gap: 16px; }

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
    <a href="logout.php" class="btn-nav" style="background:transparent; color:var(--dark); border:1px solid var(--border);">Déconnexion</a>
    <a href="register.php" class="btn-nav">S'inscrire</a>
  </div>
</nav>

<div class="profile-header">
  <div class="user-main-info">
    <div class="avatar-large">
      <?php echo strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1)); ?>
    </div>
    <div class="user-title-group">
      <div class="user-role-label">Profil <?= htmlspecialchars($user['role'] === 'admin' ? 'administrateur' : 'voyageur') ?></div>
      <h1 class="user-fullname"><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></h1>
      <div class="user-meta">
        <span>📍 <?= htmlspecialchars($user['adresse'] ?? 'Non spécifiée') ?></span>
        <span>📅 Membre depuis <?= date('F Y', strtotime($user['date_inscription'])) ?></span>
      </div>
    </div>
  </div>
  <a href="edit-profile.php" class="btn-edit">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
    Modifier le profil
  </a>
</div>

<div class="profile-container">
  
  <div class="profile-sidebar">
    <div class="sidebar-section">
      <h3>Informations</h3>
      <div class="info-list">
        <div class="info-item">
          <span class="info-icon">✉</span>
          <span class="info-text"><?= htmlspecialchars($user['email']) ?></span>
        </div>
        <div class="info-item">
          <span class="info-icon">📞</span>
          <span class="info-text">+216 20 000 000</span>
        </div>
        <div class="info-item">
          <span class="info-icon">✔</span>
          <span class="info-text" style="color: #2ecc71;">Compte vérifié</span>
        </div>
      </div>
    </div>

    <div class="sidebar-section">
      <h3>Préférences</h3>
      <div class="tag-cloud">
        <span class="tag">Villages berbères</span>
        <span class="tag">Cuisine maison</span>
        <span class="tag">Artisanat</span>
        <span class="tag">Randonnées</span>
      </div>
    </div>
  </div>

  <div class="content-area">
    
    <div class="about-box">
      <div class="about-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
      </div>
      <div class="about-text">
        <h4>À propos</h4>
        <p>Passionné par les villages du sud, les repas chez l'habitant et les adresses locales loin des circuits classiques.</p>
      </div>
    </div>

    <div>
      <div class="section-subtitle">Historique</div>
      <div class="section-header">
        <h2 class="section-title">Dernières réservations</h2>
        <a href="mes-reservations.php" class="btn-all">Tout voir</a>
      </div>

      <?php if (mysqli_num_rows($latest_reservations) === 0): ?>
        <div class="empty-state">Vous n'avez pas encore de réservations.</div>
      <?php else: ?>
        <div class="res-grid">
          <?php while($r = mysqli_fetch_assoc($latest_reservations)): 
            $type = ''; $titre = ''; $img = ''; $loc = '';
            if ($r['logement_id']) { $type = 'hebergement'; $titre = $r['h_titre']; $img = $r['h_img']; $loc = 'Hébergement'; }
            elseif ($r['repas_id']) { $type = 'repas'; $titre = $r['rp_titre']; $img = $r['rp_img']; $loc = 'Repas'; }
            elseif ($r['guide_id']) { $type = 'guide'; $titre = $r['g_titre']; $img = $r['g_img']; $loc = 'Guide'; }
            elseif ($r['evenement_id']) { $type = 'evenement'; $titre = $r['e_titre']; $img = $r['e_img']; $loc = 'Événement'; }
            
            $imageUrl = formatImg($img, $type);
          ?>
            <a href="<?= $type ?>.php?id=<?= $r['logement_id'] ?? $r['repas_id'] ?? $r['guide_id'] ?? $r['evenement_id'] ?>" class="res-card">
              <div class="res-img"><img src="<?= htmlspecialchars($imageUrl) ?>" alt="Service"></div>
              <div class="res-body">
                <div class="res-loc"><?= $loc ?></div>
                <div class="res-titre"><?= htmlspecialchars($titre) ?></div>
                <div class="res-footer">
                  <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                  <?= number_format(rand(45, 50)/10, 1) ?>
                </div>
              </div>
            </a>
          <?php endwhile; ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="favoris-box">
      <div style="color: var(--orange);">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
      </div>
      <div>
        <h4 style="font-family: 'Playfair Display', serif; font-size: 18px;">Favoris enregistrés</h4>
        <p style="font-size: 13px; color: var(--muted);">6 expériences sauvegardées pour votre prochain voyage.</p>
      </div>
    </div>

  </div>

</div>

<footer>
    <p>&copy; <?= date('Y') ?> Tarkina. Tous droits réservés.</p>
</footer>

</body>
</html>
