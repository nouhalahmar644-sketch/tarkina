<?php
session_start();

require_once 'db.php';
require_once __DIR__ . '/includes/i18n.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$L_ALL = [
    'fr' => [
        'page_title'      => 'Mon Profil — Tarkina',
        'back'            => 'Retour',
        'profile_label'   => 'Profil voyageur',
        'member_since'    => 'Membre depuis',
        'edit_profile'    => 'Modifier le profil',
        'information'     => 'Informations',
        'verified'        => 'Compte vérifié',
        'preferences'     => 'Préférences',
        'pref_1'          => 'Villages berbères',
        'pref_2'          => 'Cuisine maison',
        'pref_3'          => 'Artisanat',
        'pref_4'          => 'Randonnées',
        'about'           => 'À propos',
        'history'         => 'Historique',
        'last_res'        => 'Dernières réservations',
        'see_all'         => 'Tout voir',
        'no_res'          => 'Aucune réservation pour le moment.',
        'explore_regions' => 'Explorer les régions →',
        'tunisie'         => 'Tunisie',
        'service'         => 'Service',
        'saved_fav'       => 'Favoris enregistrés',
        'view_all'        => 'Voir tout',
        'fav_singular'    => 'expérience sauvegardée pour votre prochain voyage.',
        'fav_plural'      => 'expériences sauvegardées pour votre prochain voyage.',
    ],
    'ar' => [
        'page_title'      => 'ملفي الشخصي — تاركينا',
        'back'            => 'عودة',
        'profile_label'   => 'ملف المسافر',
        'member_since'    => 'عضو منذ',
        'edit_profile'    => 'تعديل الملف الشخصي',
        'information'     => 'المعلومات',
        'verified'        => 'حساب موثّق',
        'preferences'     => 'التفضيلات',
        'pref_1'          => 'القرى الأمازيغية',
        'pref_2'          => 'المطبخ البيتي',
        'pref_3'          => 'الحِرف اليدوية',
        'pref_4'          => 'المشي في الطبيعة',
        'about'           => 'نبذة',
        'history'         => 'السجلّ',
        'last_res'        => 'آخر الحجوزات',
        'see_all'         => 'عرض الكل',
        'no_res'          => 'لا توجد حجوزات حتى الآن.',
        'explore_regions' => 'استكشف الجهات →',
        'tunisie'         => 'تونس',
        'service'         => 'خدمة',
        'saved_fav'       => 'المفضلات المحفوظة',
        'view_all'        => 'عرض الكل',
        'fav_singular'    => 'تجربة محفوظة لرحلتك القادمة.',
        'fav_plural'      => 'تجارب محفوظة لرحلتك القادمة.',
    ],
    'en' => [
        'page_title'      => 'My Profile — Tarkina',
        'back'            => 'Back',
        'profile_label'   => 'Traveler profile',
        'member_since'    => 'Member since',
        'edit_profile'    => 'Edit profile',
        'information'     => 'Information',
        'verified'        => 'Verified account',
        'preferences'     => 'Preferences',
        'pref_1'          => 'Berber villages',
        'pref_2'          => 'Home cooking',
        'pref_3'          => 'Handicrafts',
        'pref_4'          => 'Hiking',
        'about'           => 'About',
        'history'         => 'History',
        'last_res'        => 'Recent reservations',
        'see_all'         => 'See all',
        'no_res'          => 'No reservations yet.',
        'explore_regions' => 'Explore the regions →',
        'tunisie'         => 'Tunisia',
        'service'         => 'Service',
        'saved_fav'       => 'Saved favorites',
        'view_all'        => 'View all',
        'fav_singular'    => 'experience saved for your next trip.',
        'fav_plural'      => 'experiences saved for your next trip.',
    ],
];
$L = $L_ALL[$lang] ?? $L_ALL['fr'];

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

// Fetch favorites count — the column is `utilisateur_id` in the legacy schema
// but some installs may use `user_id`. Try both, swallow errors so the page never crashes.
$favoris_count = 0;
try {
    $stmt3 = mysqli_prepare($conn, "SELECT COUNT(*) FROM favoris WHERE utilisateur_id = ?");
    if ($stmt3) {
        mysqli_stmt_bind_param($stmt3, 'i', $user_id);
        mysqli_stmt_execute($stmt3);
        mysqli_stmt_bind_result($stmt3, $favoris_count);
        mysqli_stmt_fetch($stmt3);
        mysqli_stmt_close($stmt3);
    }
} catch (\Throwable $e) {
    try {
        $stmt3b = mysqli_prepare($conn, "SELECT COUNT(*) FROM favoris WHERE user_id = ?");
        if ($stmt3b) {
            mysqli_stmt_bind_param($stmt3b, 'i', $user_id);
            mysqli_stmt_execute($stmt3b);
            mysqli_stmt_bind_result($stmt3b, $favoris_count);
            mysqli_stmt_fetch($stmt3b);
            mysqli_stmt_close($stmt3b);
        }
    } catch (\Throwable $e2) {
        $favoris_count = 0;
    }
}
$favoris_count = (int) $favoris_count;
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>" dir="<?= htmlspecialchars($dir) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($L['page_title']) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/rtl.css">
    <style>
        :root { --primary: #f16e22; --navy: #0b1c30; --light-bg: #FFFFFF; --text-dark: #1a1a1a; --text-muted: #6b7280; --border: #e5e7eb; }
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
        .profile-banner { background: var(--navy); padding: 48px 60px 36px; }
        .profile-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
        .profile-avatar { overflow: hidden; }
        .profile-banner-inner { max-width: 1100px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; }
        .profile-banner-left { display: flex; align-items: center; gap: 24px; }
        .profile-avatar { width: 72px; height: 72px; border-radius: 50%; background: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.6rem; font-weight: 800; color: #fff; flex-shrink: 0; }
        .profile-label { font-size: 0.75rem; color: rgba(255,255,255,0.5); text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 6px; }
        .profile-name { font-size: 1.8rem; font-weight: 800; color: #fff; margin-bottom: 8px; }
        .profile-meta { display: flex; align-items: center; gap: 20px; }
        .profile-meta-item { display: flex; align-items: center; gap: 6px; color: rgba(255,255,255,0.6); font-size: 0.88rem; }
        .btn-edit { display: flex; align-items: center; gap: 8px; padding: 10px 22px; background: var(--primary); border-radius: 50px; color: #fff; text-decoration: none; font-size: 0.9rem; font-weight: 700; transition: background .2s; }
        .btn-edit:hover { background: #d95716; }
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
  color:#0b1c30;padding:14px 0 0 24px;display:flex;align-items:center;gap:6px;"
  onmouseover="this.style.color='#f16e22'" 
  onmouseout="this.style.color='#0b1c30'">
  &#8592;
</button>

<!-- PROFILE BANNER -->
<div class="profile-banner">
    <div class="profile-banner-inner">
        <div class="profile-banner-left">
            <?php
              // Show the user's uploaded profile photo if any, otherwise their initials
              $profilePhoto = trim((string) ($user['photo_profil'] ?? ''));
              $profileImg = '';
              if ($profilePhoto !== '') {
                  if (strpos($profilePhoto, 'http') === 0 || strpos($profilePhoto, 'uploads/') === 0 || strpos($profilePhoto, 'images/') === 0) {
                      $profileImg = $profilePhoto;
                  } elseif (file_exists('uploads/profils/' . $profilePhoto)) {
                      $profileImg = 'uploads/profils/' . $profilePhoto;
                  } elseif (file_exists('uploads/' . $profilePhoto)) {
                      $profileImg = 'uploads/' . $profilePhoto;
                  }
              }
            ?>
            <div class="profile-avatar">
              <?php if ($profileImg !== ''): ?>
                <img src="<?= htmlspecialchars($profileImg) ?>" alt="<?= htmlspecialchars($nom_complet) ?>" onerror="this.style.display='none';this.parentElement.innerText='<?= htmlspecialchars($initiales) ?>';this.parentElement.style.display='flex';">
              <?php else: ?>
                <?= $initiales ?>
              <?php endif; ?>
            </div>
            <div>
                <p class="profile-label"><?= htmlspecialchars($L['profile_label']) ?></p>
                <h1 class="profile-name"><?= $nom_complet ?></h1>
                <div class="profile-meta">
                    <?php if(!empty($user['ville'])): ?>
                    <div class="profile-meta-item">📍 <?= htmlspecialchars($user['ville']) ?></div>
                    <?php endif; ?>
                    <div class="profile-meta-item">📅 <?= htmlspecialchars($L['member_since']) ?> <?= date('F Y', strtotime($user['created_at'] ?? 'now')) ?></div>
                </div>
            </div>
        </div>
        <a href="edit-profile.php" class="btn-edit">✏ <?= htmlspecialchars($L['edit_profile']) ?></a>
    </div>
</div>

<!-- PROFILE BODY -->
<div class="profile-body">

    <!-- SIDEBAR -->
    <div class="profile-sidebar">
        <div class="sidebar-card">
            <h3><?= htmlspecialchars($L['information']) ?></h3>
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
            <div class="verified-badge">✓ <?= htmlspecialchars($L['verified']) ?></div>
        </div>
        <div class="sidebar-card">
            <h3><?= htmlspecialchars($L['preferences']) ?></h3>
            <span class="pref-pill"><?= htmlspecialchars($L['pref_1']) ?></span>
            <span class="pref-pill"><?= htmlspecialchars($L['pref_2']) ?></span>
            <span class="pref-pill"><?= htmlspecialchars($L['pref_3']) ?></span>
            <span class="pref-pill"><?= htmlspecialchars($L['pref_4']) ?></span>
        </div>
    </div>

    <!-- MAIN -->
    <div class="profile-main">

        <!-- About -->
        <?php if(!empty($user['bio'])): ?>
        <div class="profile-card">
            <div class="profile-card-title">
                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                <?= htmlspecialchars($L['about']) ?>
            </div>
            <p class="about-text"><?= htmlspecialchars($user['bio']) ?></p>
        </div>
        <?php endif; ?>

        <!-- Reservations -->
        <div class="profile-card">
            <div class="profile-card-header">
                <div>
                    <p class="section-label-small"><?= htmlspecialchars($L['history']) ?></p>
                    <p class="profile-card-title"><?= htmlspecialchars($L['last_res']) ?></p>
                </div>
                <a href="mes-reservations.php" class="link-all"><?= htmlspecialchars($L['see_all']) ?></a>
            </div>
            <?php if(count($reservations) > 0): ?>
            <div class="reservation-cards">
                <?php foreach($reservations as $res): ?>
                <a href="<?= !empty($res['type_service']) ? htmlspecialchars($res['type_service']) . '.php?id=' . (int)($res['service_id'] ?? 0) : 'mes-reservations.php' ?>" class="res-card">
                    <?php
                    // Resolve service photo: respect paths already starting with http/uploads/images.
                    $sp = trim((string) ($res['service_photo'] ?? ''));
                    if ($sp === '') {
                        $img = 'https://images.unsplash.com/photo-1539650116574-75c0c6d73f6e?w=400&fit=crop';
                    } elseif (strpos($sp, 'http') === 0 || strpos($sp, 'uploads/') === 0 || strpos($sp, 'images/') === 0) {
                        $img = $sp;
                    } else {
                        $img = 'uploads/' . ($res['type_service'] ?? '') . '/' . ltrim($sp, '/');
                    }
                    ?>
                    <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($res['service_titre'] ?? '') ?>" onerror="this.src='https://images.unsplash.com/photo-1539650116574-75c0c6d73f6e?w=400&fit=crop'">
                    <div class="res-card-body">
                        <p class="res-card-region"><?= htmlspecialchars($res['service_region'] ?? $L['tunisie']) ?></p>
                        <p class="res-card-title"><?= htmlspecialchars($res['service_titre'] ?? $L['service']) ?></p>
                        <div class="res-card-rating">
                            <span class="star">★</span> 4.8
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p style="color:var(--text-muted);font-size:0.92rem;"><?= htmlspecialchars($L['no_res']) ?> <a href="explorer.php" style="color:var(--primary);"><?= htmlspecialchars($L['explore_regions']) ?></a></p>
            <?php endif; ?>
        </div>

        <!-- Favorites -->
        <div class="profile-card">
            <div class="profile-card-header">
                <p class="profile-card-title">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                    <?= htmlspecialchars($L['saved_fav']) ?>
                </p>
                <a href="mes-favoris.php" class="link-all"><?= htmlspecialchars($L['view_all']) ?></a>
            </div>
            <div class="fav-empty">
                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                <?= $favoris_count ?> <?= htmlspecialchars($favoris_count == 1 ? $L['fav_singular'] : $L['fav_plural']) ?>
            </div>
        </div>

    </div>
</div>

<!-- FOOTER -->
<?php include 'footer.php'; ?>

</body>
</html>

