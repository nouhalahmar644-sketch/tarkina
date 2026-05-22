<?php
session_start();

require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user
$user_id = (int)$_SESSION['user_id'];
$res = mysqli_query($conn, "SELECT * FROM utilisateur WHERE id = $user_id LIMIT 1");
$user = mysqli_fetch_assoc($res);

if (!$user) {
    header('Location: logout.php');
    exit;
}

$initiales = strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1));
$nom_complet = htmlspecialchars($user['prenom'] . ' ' . $user['nom']);

// Fetch last 3 reservations
$query = "
    SELECT r.*, 
        COALESCE(h.titre, rep.titre, g.titre, e.titre, a.titre) AS service_titre,
        COALESCE(h.photo_principale, rep.photo_principale, g.photo_principale, e.photo_principale, a.photo_principale) AS service_photo,
        COALESCE(h.localisation, rep.localisation, g.localisation, e.localisation, a.localisation) AS service_region
    FROM reservations r
    LEFT JOIN hebergement h ON r.type_service = 'hebergement' AND r.service_id = h.id
    LEFT JOIN repas rep ON r.type_service = 'repas' AND r.service_id = rep.id
    LEFT JOIN guide g ON r.type_service = 'guide' AND r.service_id = g.id
    LEFT JOIN evenement e ON r.type_service = 'evenement' AND r.service_id = e.id
    LEFT JOIN artisanat a ON r.type_service = 'artisanat' AND r.service_id = a.id
    WHERE r.user_id = $user_id
    ORDER BY r.created_at DESC
    LIMIT 3
";
$res2 = mysqli_query($conn, $query);

$reservations = [];
if ($res2) {
    while ($row = mysqli_fetch_assoc($res2)) {
        $reservations[] = $row;
    }
}

// Fetch favorites count
$res3 = mysqli_query($conn, "SELECT COUNT(*) FROM favoris WHERE user_id = $user_id");
$favoris_count = 0;
if ($res3) {
    $row = mysqli_fetch_row($res3);
    $favoris_count = $row[0] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil — Tarkina</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        :root { --primary: #E05A2B; --navy: #1B3A4B; --light-bg: #FAF8F5; --text-dark: #1a1a1a; --text-muted: #6b7280; --border: #e5e7eb; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: var(--light-bg); color: var(--text-dark); }
        .navbar { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; background: #fff; border-bottom: 1px solid var(--border); padding: 0 60px; height: 70px; display: flex; align-items: center; justify-content: space-between; }
        .nav-logo img { height: 36px; }
        .nav-logo span { font-size: 1.4rem; font-weight: 800; color: var(--navy); }
        .nav-links { display: flex; gap: 36px; list-style: none; }
        .nav-links a { text-decoration: none; color: var(--text-dark); font-size: 0.95rem; font-weight: 500; transition: color .2s; }
        .nav-links a:hover { color: var(--primary); }
        .nav-auth { display: flex; gap: 12px; align-items: center; }
        .btn-nav-outline { padding: 8px 20px; border: 1.5px solid var(--navy); border-radius: 50px; color: var(--navy); text-decoration: none; font-size: 0.9rem; font-weight: 600; transition: all .2s; }
        .btn-nav-outline:hover { background: var(--navy); color: #fff; }
        .btn-nav-primary { padding: 8px 20px; background: var(--primary); border-radius: 50px; color: #fff; text-decoration: none; font-size: 0.9rem; font-weight: 600; }
        .profile-banner { background: var(--navy); padding: 48px 60px 36px; margin-top: 70px; }
        .profile-banner-inner { max-width: 1100px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; }
        .profile-banner-left { display: flex; align-items: center; gap: 24px; }
        .profile-avatar { width: 72px; height: 72px; border-radius: 50%; background: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.6rem; font-weight: 800; color: #fff; flex-shrink: 0; }
        .profile-label { font-size: 0.75rem; color: rgba(255,255,255,0.5); text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 6px; }
        .profile-name { font-size: 1.8rem; font-weight: 800; color: #fff; margin-bottom: 8px; }
        .profile-meta { display: flex; align-items: center; gap: 20px; }
        .profile-meta-item { display: flex; align-items: center; gap: 6px; color: rgba(255,255,255,0.6); font-size: 0.88rem; }
        .btn-edit { display: flex; align-items: center; gap: 8px; padding: 10px 22px; background: var(--primary); border-radius: 50px; color: #fff; text-decoration: none; font-size: 0.9rem; font-weight: 700; transition: background .2s; }
        .btn-edit:hover { background: #c44d22; }
        .profile-body { max-width: 1100px; margin: 32px auto; padding: 0 60px; display: grid; grid-template-columns: 260px 1fr; gap: 28px; }
        .profile-sidebar { display: flex; flex-direction: column; gap: 20px; }
        .sidebar-card { background: #fff; border: 1px solid var(--border); border-radius: 16px; padding: 24px; }
        .sidebar-card h3 { font-size: 0.88rem; font-weight: 700; margin-bottom: 16px; color: var(--text-dark); }
        .sidebar-info-item { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; color: var(--text-muted); font-size: 0.88rem; }
        .sidebar-info-item svg { width: 16px; height: 16px; stroke: var(--primary); flex-shrink: 0; }
        .verified-badge { display: flex; align-items: center; gap: 6px; color: #059669; font-size: 0.82rem; font-weight: 600; }
        .pref-pill { display: inline-block; padding: 5px 12px; background: var(--light-bg); border-radius: 50px; font-size: 0.8rem; color: var(--text-dark); margin: 4px 4px 4px 0; }
        .profile-main { display: flex; flex-direction: column; gap: 20px; }
        .profile-card { background: #fff; border: 1px solid var(--border); border-radius: 16px; padding: 28px; }
        .profile-card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
        .profile-card-title { font-size: 1rem; font-weight: 700; display: flex; align-items: center; gap: 8px; }
        .profile-card-title svg { width: 18px; height: 18px; stroke: var(--primary); }
        .section-label-small { font-size: 0.75rem; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: var(--text-muted); margin-bottom: 16px; }
        .about-text { font-size: 0.95rem; color: var(--text-muted); line-height: 1.7; }
        .reservation-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        .res-card { border: 1px solid var(--border); border-radius: 12px; overflow: hidden; text-decoration: none; color: inherit; transition: box-shadow .2s; }
        .res-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .res-card img { width: 100%; height: 140px; object-fit: cover; }
        .res-card-body { padding: 14px; }
        .res-card-region { font-size: 0.75rem; color: var(--text-muted); margin-bottom: 4px; }
        .res-card-title { font-size: 0.9rem; font-weight: 700; margin-bottom: 8px; }
        .res-card-rating { display: flex; align-items: center; gap: 4px; font-size: 0.82rem; color: var(--text-muted); }
        .star { color: #F59E0B; }
        .link-all { font-size: 0.88rem; color: var(--primary); text-decoration: none; font-weight: 600; }
        .link-all:hover { text-decoration: underline; }
        .fav-empty { display: flex; align-items: center; gap: 12px; color: var(--text-muted); font-size: 0.92rem; }
        .fav-empty svg { width: 20px; height: 20px; stroke: var(--primary); }
        footer { background: var(--navy); color: #fff; padding: 60px 60px 30px; margin-top: 60px; }
        .footer-grid { display: grid; grid-template-columns: 1.4fr 1fr 1fr 1fr; gap: 48px; margin-bottom: 48px; }
        .footer-brand-name { font-size: 1.6rem; font-weight: 800; margin-bottom: 12px; }
        .footer-brand-desc { color: rgba(255,255,255,0.6); font-size: 0.9rem; line-height: 1.7; margin-bottom: 20px; }
        .footer-col h4 { font-size: 0.78rem; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: rgba(255,255,255,0.5); margin-bottom: 20px; }
        .footer-col ul { list-style: none; }
        .footer-col ul li { margin-bottom: 10px; }
        .footer-col ul li a { color: rgba(255,255,255,0.75); text-decoration: none; font-size: 0.9rem; }
        .footer-divider { border: none; border-top: 1px solid rgba(255,255,255,0.1); margin-bottom: 24px; }
        .footer-bottom { display: flex; justify-content: space-between; font-size: 0.85rem; color: rgba(255,255,255,0.4); }
        .footer-watermark { text-align: center; font-size: 8vw; font-weight: 800; color: rgba(255,255,255,0.04); line-height: 1; margin-bottom: -10px; }
    </style>
</head>
<body>

<!-- NAVBAR -->
<?php include 'navbar.php'; ?>

<button onclick="history.back()" 
  style="background:none;border:none;cursor:pointer;font-size:1.3rem;
  color:#1B3A4B;padding:14px 0 0 24px;display:flex;align-items:center;gap:6px;"
  onmouseover="this.style.color='#E05A2B'" 
  onmouseout="this.style.color='#1B3A4B'">
  &#8592;
</button>

<!-- PROFILE BANNER -->
<div class="profile-banner">
    <div class="profile-banner-inner">
        <div class="profile-banner-left">
            <div class="profile-avatar"><?= $initiales ?></div>
            <div>
                <p class="profile-label">Profil voyageur</p>
                <h1 class="profile-name"><?= $nom_complet ?></h1>
                <div class="profile-meta">
                    <?php if(!empty($user['ville'])): ?>
                    <div class="profile-meta-item">📍 <?= htmlspecialchars($user['ville']) ?></div>
                    <?php endif; ?>
                    <div class="profile-meta-item">📅 Membre depuis <?= date('F Y', strtotime($user['created_at'] ?? 'now')) ?></div>
                </div>
            </div>
        </div>
        <a href="edit-profile.php" class="btn-edit">✏ Modifier le profil</a>
    </div>
</div>

<!-- PROFILE BODY -->
<div class="profile-body">

    <!-- SIDEBAR -->
    <div class="profile-sidebar">
        <div class="sidebar-card">
            <h3>Informations</h3>
            <div class="sidebar-info-item">
                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                <?= htmlspecialchars($user['email']) ?>
            </div>
            <?php if(!empty($user['telephone'])): ?>
            <div class="sidebar-info-item">
                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                <?= htmlspecialchars($user['telephone']) ?>
            </div>
            <?php endif; ?>
            <div class="verified-badge">✓ Compte vérifié</div>
        </div>
        <div class="sidebar-card">
            <h3>Préférences</h3>
            <span class="pref-pill">Villages berbères</span>
            <span class="pref-pill">Cuisine maison</span>
            <span class="pref-pill">Artisanat</span>
            <span class="pref-pill">Randonnées</span>
        </div>
    </div>

    <!-- MAIN -->
    <div class="profile-main">

        <!-- About -->
        <?php if(!empty($user['bio'])): ?>
        <div class="profile-card">
            <div class="profile-card-title">
                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                À propos
            </div>
            <p class="about-text"><?= htmlspecialchars($user['bio']) ?></p>
        </div>
        <?php endif; ?>

        <!-- Reservations -->
        <div class="profile-card">
            <div class="profile-card-header">
                <div>
                    <p class="section-label-small">Historique</p>
                    <p class="profile-card-title">Dernières réservations</p>
                </div>
                <a href="mes-reservations.php" class="link-all">Tout voir</a>
            </div>
            <?php if(count($reservations) > 0): ?>
            <div class="reservation-cards">
                <?php foreach($reservations as $res): ?>
                <a href="<?= !empty($res['type_service']) ? htmlspecialchars($res['type_service']) . '.php?id=' . (int)($res['service_id'] ?? 0) : 'mes-reservations.php' ?>" class="res-card">
                    <?php
                    $img = !empty($res['service_photo'])
                        ? 'uploads/' . $res['service_photo']
                        : 'https://images.unsplash.com/photo-1539650116574-75c0c6d73f6e?w=400&fit=crop';
                    ?>
                    <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($res['service_titre'] ?? '') ?>">
                    <div class="res-card-body">
                        <p class="res-card-region"><?= htmlspecialchars($res['service_region'] ?? 'Tunisie') ?></p>
                        <p class="res-card-title"><?= htmlspecialchars($res['service_titre'] ?? 'Service') ?></p>
                        <div class="res-card-rating">
                            <span class="star">★</span> 4.8
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p style="color:var(--text-muted);font-size:0.92rem;">Aucune réservation pour le moment. <a href="explorer.php" style="color:var(--primary);">Explorer les régions →</a></p>
            <?php endif; ?>
        </div>

        <!-- Favorites -->
        <div class="profile-card">
            <div class="profile-card-header">
                <p class="profile-card-title">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                    Favoris enregistrés
                </p>
                <a href="mes-favoris.php" class="link-all">Voir tout</a>
            </div>
            <div class="fav-empty">
                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                <?= $favoris_count ?> expérience<?= $favoris_count != 1 ? 's' : '' ?> sauvegardée<?= $favoris_count != 1 ? 's' : '' ?> pour votre prochain voyage.
            </div>
        </div>

    </div>
</div>

<!-- FOOTER -->
<?php include 'footer.php'; ?>

</body>
</html>
