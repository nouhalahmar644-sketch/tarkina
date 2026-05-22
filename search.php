<?php
session_start();
require_once __DIR__ . '/db.php';

$destination = isset($_GET['destination']) ? trim($_GET['destination']) : '';
$date_debut  = isset($_GET['date_debut'])  ? trim($_GET['date_debut'])  : '';
$date_fin    = isset($_GET['date_fin'])    ? trim($_GET['date_fin'])    : '';
$personnes   = isset($_GET['personnes'])   ? (int)$_GET['personnes']    : 1;

$services = [];
$searchTerm = '%' . $destination . '%';

// Availability filter logic
$dateFilter = "";
if ($date_debut !== '' && $date_fin !== '') {
    $dateFilter = " AND (date_debut IS NULL OR date_debut <= ?) AND (date_fin IS NULL OR date_fin >= ?)";
}

/**
 * Helper to execute queries for different service types
 */
function fetchServices($conn, $sql, $searchTerm, $date_debut, $date_fin, $dateFilter, $type, $label, &$services) {
    $st = mysqli_prepare($conn, $sql);
    if ($st) {
        if ($dateFilter !== '') {
            mysqli_stmt_bind_param($st, 'sss', $searchTerm, $date_debut, $date_fin);
        } else {
            mysqli_stmt_bind_param($st, 's', $searchTerm);
        }
        mysqli_stmt_execute($st);
        $res = mysqli_stmt_get_result($st);
        while ($row = mysqli_fetch_assoc($res)) {
            $row['type'] = $type;
            $row['type_label'] = $label;
            $services[] = $row;
        }
        mysqli_stmt_close($st);
    }
}

// 1. Hebergements
$sql_heb = "SELECT id, titre, prix, localisation, capacite, photo_principale FROM hebergement WHERE (localisation LIKE ? OR titre LIKE ?) AND statut IN ('publié', 'actif') AND capacite >= ?" . $dateFilter;
// Modification to handle title search as well
$st_heb = mysqli_prepare($conn, $sql_heb);
if ($st_heb) {
    if ($dateFilter !== '') {
        mysqli_stmt_bind_param($st_heb, 'sssii', $searchTerm, $searchTerm, $personnes, $date_debut, $date_fin);
    } else {
        mysqli_stmt_bind_param($st_heb, 'ssi', $searchTerm, $searchTerm, $personnes);
    }
    mysqli_stmt_execute($st_heb);
    $res = mysqli_stmt_get_result($st_heb);
    while ($row = mysqli_fetch_assoc($res)) {
        $row['type'] = 'hebergement';
        $row['type_label'] = 'Hébergement';
        $services[] = $row;
    }
    mysqli_stmt_close($st_heb);
}

// 2. Repas
$sql_rep = "SELECT id, titre, prix, localisation, capacite, photo_principale FROM repas WHERE (localisation LIKE ? OR titre LIKE ?) AND statut IN ('publié','actif') AND capacite >= ?" . $dateFilter;
$st_rep = mysqli_prepare($conn, $sql_rep);
if ($st_rep) {
    if ($dateFilter !== '') {
        mysqli_stmt_bind_param($st_rep, 'sssii', $searchTerm, $searchTerm, $personnes, $date_debut, $date_fin);
    } else {
        mysqli_stmt_bind_param($st_rep, 'ssi', $searchTerm, $searchTerm, $personnes);
    }
    mysqli_stmt_execute($st_rep);
    $res = mysqli_stmt_get_result($st_rep);
    while ($row = mysqli_fetch_assoc($res)) {
        $row['type'] = 'repas';
        $row['type_label'] = 'Repas maison';
        $services[] = $row;
    }
    mysqli_stmt_close($st_rep);
}

// 3. Guide
$sql_gui = "SELECT id, titre, prix, localisation, capacite, photo_principale FROM guide WHERE (localisation LIKE ? OR titre LIKE ?) AND statut IN ('publié','actif') AND capacite >= ?" . $dateFilter;
$st_gui = mysqli_prepare($conn, $sql_gui);
if ($st_gui) {
    if ($dateFilter !== '') {
        mysqli_stmt_bind_param($st_gui, 'sssii', $searchTerm, $searchTerm, $personnes, $date_debut, $date_fin);
    } else {
        mysqli_stmt_bind_param($st_gui, 'ssi', $searchTerm, $searchTerm, $personnes);
    }
    mysqli_stmt_execute($st_gui);
    $res = mysqli_stmt_get_result($st_gui);
    while ($row = mysqli_fetch_assoc($res)) {
        $row['type'] = 'guide';
        $row['type_label'] = 'Guide local';
        $services[] = $row;
    }
    mysqli_stmt_close($st_gui);
}

// 4. Evenement
$sql_eve = "SELECT id, titre, prix, localisation, capacite, photo_principale FROM evenement WHERE (localisation LIKE ? OR titre LIKE ?) AND statut IN ('publié','actif') AND capacite >= ?" . $dateFilter;
$st_eve = mysqli_prepare($conn, $sql_eve);
if ($st_eve) {
    if ($dateFilter !== '') {
        mysqli_stmt_bind_param($st_eve, 'sssii', $searchTerm, $searchTerm, $personnes, $date_debut, $date_fin);
    } else {
        mysqli_stmt_bind_param($st_eve, 'ssi', $searchTerm, $searchTerm, $personnes);
    }
    mysqli_stmt_execute($st_eve);
    $res = mysqli_stmt_get_result($st_eve);
    while ($row = mysqli_fetch_assoc($res)) {
        $row['type'] = 'evenement';
        $row['type_label'] = 'Événement';
        $services[] = $row;
    }
    mysqli_stmt_close($st_eve);
}

// 5. Artisanat (No dates, use stock)
$sql_art = "SELECT id, titre, prix, localisation, stock as capacite, photo_principale FROM artisanat WHERE (localisation LIKE ? OR titre LIKE ?) AND statut IN ('publié','actif') AND stock >= ?";
$st_art = mysqli_prepare($conn, $sql_art);
if ($st_art) {
    mysqli_stmt_bind_param($st_art, 'ssi', $searchTerm, $searchTerm, $personnes);
    mysqli_stmt_execute($st_art);
    $res = mysqli_stmt_get_result($st_art);
    while ($row = mysqli_fetch_assoc($res)) {
        $row['type'] = 'artisanat';
        $row['type_label'] = 'Artisanat';
        $services[] = $row;
    }
    mysqli_stmt_close($st_art);
}

shuffle($services);

function formatImagePath($path, $type) {
    if (empty($path)) return 'https://placehold.co/800x600?text=Pas+de+photo';
    if (strpos($path, 'http') === 0) return $path;
    
    // Determine the correct uploads subfolder
    $folder = $type;
    if ($type === 'hebergement') $folder = 'hebergement';
    elseif ($type === 'repas') $folder = 'repas';
    elseif ($type === 'guide') $folder = 'guide';
    elseif ($type === 'evenement') $folder = 'evenement';
    elseif ($type === 'artisanat') $folder = 'artisanat';

    if (strpos($path, 'uploads/') === 0) return $path;
    return 'uploads/' . $folder . '/' . ltrim($path, '/');
}

$page_title = 'Résultats de recherche – Tarkina';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Lato:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --cream: #f5f2ee;
            --dark: #1c1c2e;
            --navy: #1a2340;
            --orange: #e8642c;
            --muted: #6b6b6b;
            --border: #e0dbd4;
            --white: #ffffff;
            --radius: 14px;
        }
        body { font-family: 'Lato', sans-serif; background: var(--cream); color: var(--dark); line-height: 1.6; }

        /* NAV */
        nav { background: var(--white); border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; padding: 0 56px; height: 60px; position: sticky; top: 0; z-index: 100; }
        .nav-logo { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 800; color: var(--dark); text-decoration: none; display: flex; align-items: center; gap: 4px; }
        .nav-logo span { color: var(--orange); }
        .nav-links { display: flex; gap: 32px; list-style: none; }
        .nav-links a { text-decoration: none; color: var(--dark); font-size: 14px; font-weight: 600; opacity: .7; transition: opacity .2s; }
        .nav-links a:hover { opacity: 1; }

        /* SEARCH HEADER */
        .search-header { background: var(--navy); color: var(--white); padding: 40px 56px; text-align: center; }
        .search-header h1 { font-family: 'Playfair Display', serif; font-size: 36px; margin-bottom: 10px; }
        .search-summary { font-size: 14px; opacity: 0.8; }

        /* CONTENT */
        .container { padding: 48px 56px; max-width: 1400px; margin: 0 auto; }
        
        .results-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px; }
        
        .service-card-link {
            text-decoration: none;
            color: inherit;
            display: block;
            cursor: pointer;
            position: relative;
            z-index: 1;
        }
        .service-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
            pointer-events: auto;
        }
        .service-card-link:hover .service-card {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }
        
        .card-image { height: 200px; overflow: hidden; position: relative; }
        .card-image img { width: 100%; height: 100%; object-fit: cover; }
        .card-badge { position: absolute; top: 15px; left: 15px; background: var(--orange); color: var(--white); font-size: 10px; font-weight: 700; padding: 4px 10px; border-radius: 50px; text-transform: uppercase; letter-spacing: 0.05em; }

        .card-body { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; }
        .card-location { font-size: 12px; color: var(--muted); margin-bottom: 8px; display: flex; align-items: center; gap: 4px; }
        .card-title { font-family: 'Playfair Display', serif; font-size: 18px; font-weight: 700; margin-bottom: 12px; line-height: 1.3; }
        .card-footer { margin-top: auto; display: flex; justify-content: space-between; align-items: center; padding-top: 15px; border-top: 1px solid var(--border); }
        .card-price { font-weight: 800; font-size: 16px; }
        .card-price span { font-size: 12px; font-weight: 400; color: var(--muted); }
        .card-rating { display: flex; align-items: center; gap: 4px; font-weight: 700; font-size: 14px; }
        .card-rating svg { width: 14px; height: 14px; fill: var(--orange); }

        .no-results { text-align: center; padding: 100px 0; grid-column: 1 / -1; }
        .no-results h2 { font-family: 'Playfair Display', serif; font-size: 24px; color: var(--muted); margin-bottom: 20px; }
        .btn-back { display: inline-block; background: var(--orange); color: var(--white); text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: 700; }

        footer { background: var(--navy); color: var(--white); padding: 56px; text-align: center; margin-top: 60px; }
        footer p { opacity: 0.6; font-size: 14px; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<button onclick="history.back()" 
  style="background:none;border:none;cursor:pointer;font-size:1.3rem;
  color:#1B3A4B;padding:14px 0 0 24px;display:flex;align-items:center;gap:6px;"
  onmouseover="this.style.color='#E05A2B'" 
  onmouseout="this.style.color='#1B3A4B'">
  &#8592;
</button>

<div class="search-header">

    <h1>Résultats pour "<?= htmlspecialchars($destination ?: 'Toute la Tunisie') ?>"</h1>
    <p class="search-summary"><?= count($services) ?> service(s) trouvé(s) <?= $date_debut ? "du " . date('d/m/Y', strtotime($date_debut)) : "" ?> <?= $date_fin ? "au " . date('d/m/Y', strtotime($date_fin)) : "" ?></p>
</div>

<div class="container">
    <div class="results-grid">
        <?php if (empty($services)): ?>
            <div class="no-results">
                <h2>Aucun résultat trouvé</h2>
                <p style="margin-bottom: 30px; color: var(--muted);">Essayez de modifier vos critères de recherche ou de changer de destination.</p>

            </div>
        <?php else: ?>
            <?php foreach ($services as $srv):
                $img = formatImagePath($srv['photo_principale'], $srv['type']);
                $rating = number_format(rand(45, 50) / 10, 1);
                $serviceType = htmlspecialchars($srv['type'], ENT_QUOTES, 'UTF-8');
                $serviceId = (int) $srv['id'];
                $serviceUrl = $serviceType . '.php?id=' . $serviceId;
            ?>
                <a href="<?= $serviceUrl ?>" class="service-card-link" style="text-decoration:none;color:inherit;display:block;">
                    <div class="service-card">
                    <div class="card-image">
                        <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($srv['titre']) ?>">
                        <span class="card-badge"><?= htmlspecialchars($srv['type_label']) ?></span>
                    </div>
                    <div class="card-body">
                        <div class="card-location">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            <?= htmlspecialchars($srv['localisation']) ?>
                        </div>
                        <h2 class="card-title"><?= htmlspecialchars($srv['titre']) ?></h2>
                        <div class="card-footer">
                            <div class="card-price">
                                <?= number_format($srv['prix'], 2, '.', ' ') ?> TND
                                <span>/ <?= $srv['type'] === 'hebergement' ? 'nuit' : 'pers.' ?></span>
                            </div>
                            <div class="card-rating">
                                <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                <?= $rating ?>
                            </div>
                        </div>
                    </div>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>

</body>
</html>
