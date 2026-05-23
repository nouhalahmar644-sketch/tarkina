<?php
session_start();
require_once __DIR__ . '/db.php';

// ---------- Read filters ----------
$destination = isset($_GET['destination']) ? trim($_GET['destination']) : (isset($_GET['q']) ? trim($_GET['q']) : '');
$date_debut  = isset($_GET['date_debut']) ? trim($_GET['date_debut']) : '';
$date_fin    = isset($_GET['date_fin'])   ? trim($_GET['date_fin'])   : '';
$personnes   = isset($_GET['personnes'])  ? max(1, (int) $_GET['personnes']) : 1;
$regionId    = isset($_GET['region'])     ? (int) $_GET['region'] : 0;
$prixMax     = (isset($_GET['prix_max']) && $_GET['prix_max'] !== '') ? (float) $_GET['prix_max'] : 0;

$allTypes = ['hebergement', 'repas', 'guide', 'evenement', 'artisanat'];
$typeMeta = [
    'hebergement' => ['cap' => 'capacite', 'label' => 'Hébergement', 'unit' => 'nuit'],
    'repas'       => ['cap' => 'capacite', 'label' => 'Repas maison', 'unit' => 'pers.'],
    'guide'       => ['cap' => 'capacite', 'label' => 'Guide local', 'unit' => 'pers.'],
    'evenement'   => ['cap' => 'capacite', 'label' => 'Événement', 'unit' => 'pers.'],
    'artisanat'   => ['cap' => 'stock', 'label' => 'Artisanat', 'unit' => 'pièce'],
];

// Which categories did the user explicitly choose? (?type=repas or ?type[]=...)
$rawType = $_GET['type'] ?? null;
$typesActive = [];
if (is_array($rawType)) {
    $typesActive = array_values(array_intersect($allTypes, $rawType));
} elseif (is_string($rawType) && in_array($rawType, $allTypes, true)) {
    $typesActive = [$rawType];
}
$selectedTypes = empty($typesActive) ? $allTypes : $typesActive; // default: all

$searchTerm = '%' . $destination . '%';
$dateFilter = '';
if ($date_debut !== '' && $date_fin !== '') {
    $dateFilter = " AND (date_debut IS NULL OR date_debut <= ?) AND (date_fin IS NULL OR date_fin >= ?)";
}

// ---------- Run queries for each selected category ----------
$services = [];
foreach ($selectedTypes as $t) {
    $cap = $typeMeta[$t]['cap'];
    $sql = "SELECT id, titre, prix, localisation, `$cap` AS capacite, photo_principale, region_id
            FROM `$t`
            WHERE (localisation LIKE ? OR titre LIKE ?) AND statut IN ('publié','actif') AND `$cap` >= ?";
    $params = [$searchTerm, $searchTerm, $personnes];
    $ptypes = 'ssi';
    if ($regionId > 0) { $sql .= " AND region_id = ?"; $params[] = $regionId; $ptypes .= 'i'; }
    if ($prixMax > 0)  { $sql .= " AND prix <= ?";     $params[] = $prixMax;  $ptypes .= 'd'; }
    if ($t !== 'artisanat' && $dateFilter !== '') {
        $sql .= $dateFilter; $params[] = $date_debut; $params[] = $date_fin; $ptypes .= 'ss';
    }
    if ($st = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($st, $ptypes, ...$params);
        mysqli_stmt_execute($st);
        $res = mysqli_stmt_get_result($st);
        while ($row = mysqli_fetch_assoc($res)) {
            $row['type'] = $t;
            $row['type_label'] = $typeMeta[$t]['label'];
            $row['unit'] = $typeMeta[$t]['unit'];
            $services[] = $row;
        }
        mysqli_stmt_close($st);
    }
}

// Regions for the filter dropdown
$regionsList = [];
if ($rq = mysqli_query($conn, "SELECT id, nom FROM region ORDER BY nom ASC")) {
    while ($r = mysqli_fetch_assoc($rq)) { $regionsList[] = $r; }
}

function formatImagePath($path, $type) {
    if (empty($path)) return 'https://placehold.co/800x600?text=Pas+de+photo';
    if (strpos($path, 'http') === 0) return $path;
    if (strpos($path, 'images/') === 0) return $path;
    if (strpos($path, 'uploads/') === 0) return $path;
    return 'uploads/' . $type . '/' . ltrim($path, '/');
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
        *, *::before, *::after { box-sizing: border-box; }
        :root { --cream:#f5f2ee; --dark:#1c1c2e; --navy:#1B3A4B; --orange:#E05A2B; --muted:#6b6b6b; --border:#e0dbd4; --white:#fff; --radius:14px; }
        body { font-family:'Lato',sans-serif; background:var(--cream); color:var(--dark); line-height:1.6; }

        .search-header { background:var(--navy); color:var(--white); padding:36px 56px; text-align:center; }
        .search-header h1 { font-family:'Playfair Display',serif; font-size:32px; margin-bottom:8px; }
        .search-summary { font-size:14px; opacity:.85; }

        .search-layout { display:flex; gap:30px; align-items:flex-start; max-width:1400px; margin:0 auto; padding:36px 56px; }

        /* FILTER SIDEBAR */
        .filter-sidebar { width:285px; flex-shrink:0; background:var(--white); border:1px solid var(--border); border-radius:var(--radius); padding:24px; position:sticky; top:24px; }
        .filter-sidebar h3 { font-family:'Playfair Display',serif; font-size:20px; margin-bottom:20px; color:var(--navy); display:flex; align-items:center; gap:8px; }
        .filter-group { margin-bottom:22px; padding-bottom:22px; border-bottom:1px solid #efeae3; }
        .filter-group:last-of-type { border-bottom:none; }
        .filter-group h4 { font-size:12px; text-transform:uppercase; letter-spacing:.08em; color:var(--muted); margin-bottom:12px; font-weight:700; }
        .filter-check { display:flex; align-items:center; gap:9px; margin-bottom:9px; font-size:14px; cursor:pointer; color:var(--dark); }
        .filter-check:last-child { margin-bottom:0; }
        .filter-check input { accent-color:var(--orange); width:16px; height:16px; cursor:pointer; }
        .filter-field { width:100%; padding:10px 12px; border:1px solid var(--border); border-radius:9px; font-family:inherit; font-size:14px; background:var(--white); color:var(--dark); outline:none; }
        .filter-field:focus { border-color:var(--orange); box-shadow:0 0 0 3px rgba(224,90,43,.12); }
        input[type=range].filter-field { padding:0; accent-color:var(--orange); height:6px; }
        .filter-range-val { font-weight:700; color:var(--orange); }
        .btn-filter { width:100%; background:var(--orange); color:#fff; border:none; border-radius:9px; padding:12px; font-weight:700; cursor:pointer; font-size:14px; transition:background .2s; }
        .btn-filter:hover { background:#c44d22; }
        .btn-reset { display:block; text-align:center; margin-top:10px; color:var(--muted); font-size:13px; text-decoration:none; }
        .btn-reset:hover { color:var(--orange); }

        .results-area { flex:1; min-width:0; }
        .results-count { font-size:14px; color:var(--muted); margin-bottom:18px; }
        .results-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(265px,1fr)); gap:24px; }

        .service-card-link { text-decoration:none; color:inherit; display:block; }
        .service-card { background:var(--white); border:1px solid var(--border); border-radius:var(--radius); overflow:hidden; transition:transform .2s, box-shadow .2s; height:100%; display:flex; flex-direction:column; }
        .service-card-link:hover .service-card { transform:translateY(-5px); box-shadow:0 10px 30px rgba(0,0,0,.08); }
        .card-image { height:185px; overflow:hidden; position:relative; }
        .card-image img { width:100%; height:100%; object-fit:cover; }
        .card-badge { position:absolute; top:14px; left:14px; background:var(--orange); color:#fff; font-size:10px; font-weight:700; padding:4px 10px; border-radius:50px; text-transform:uppercase; letter-spacing:.05em; }
        .card-body { padding:18px; flex-grow:1; display:flex; flex-direction:column; }
        .card-location { font-size:12px; color:var(--muted); margin-bottom:8px; display:flex; align-items:center; gap:4px; }
        .card-title { font-family:'Playfair Display',serif; font-size:17px; font-weight:700; margin-bottom:12px; line-height:1.3; }
        .card-footer { margin-top:auto; display:flex; justify-content:space-between; align-items:center; padding-top:14px; border-top:1px solid var(--border); }
        .card-price { font-weight:800; font-size:15px; }
        .card-price span { font-size:12px; font-weight:400; color:var(--muted); }
        .card-rating { display:flex; align-items:center; gap:4px; font-weight:700; font-size:14px; }
        .card-rating svg { width:14px; height:14px; fill:var(--orange); }
        .no-results { text-align:center; padding:80px 20px; }
        .no-results h2 { font-family:'Playfair Display',serif; font-size:24px; color:var(--muted); margin-bottom:16px; }
        .back-btn { display:inline-flex; align-items:center; gap:8px; margin:14px 0 0 24px; background:#fff; border:1.5px solid #e2ddd8; border-radius:50px; padding:8px 18px; color:var(--navy); cursor:pointer; font-weight:600; font-size:.9rem; font-family:inherit; transition:all .2s; }
        .back-btn:hover { border-color:var(--orange); color:var(--orange); }

        @media(max-width:900px){ .search-layout{ flex-direction:column; padding:24px; } .filter-sidebar{ width:100%; position:static; } }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<button onclick="history.back()" class="back-btn">&#8592; Retour</button>

<div class="search-header">
    <h1>Résultats pour "<?= htmlspecialchars($destination ?: 'Toute la Tunisie') ?>"</h1>
    <p class="search-summary"><?= count($services) ?> service(s) trouvé(s) <?= $date_debut ? "du " . date('d/m/Y', strtotime($date_debut)) : "" ?> <?= $date_fin ? "au " . date('d/m/Y', strtotime($date_fin)) : "" ?></p>
</div>

<div class="search-layout">

    <!-- FILTER SIDEBAR -->
    <aside class="filter-sidebar">
        <form method="get" action="search.php">
            <h3>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#E05A2B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                Filtres
            </h3>

            <div class="filter-group">
                <h4>Recherche</h4>
                <input type="text" name="destination" class="filter-field" placeholder="Ville, mot-clé..." value="<?= htmlspecialchars($destination) ?>">
            </div>

            <div class="filter-group">
                <h4>Catégories</h4>
                <?php foreach ($typeMeta as $tk => $tm):
                    $checked = (empty($typesActive) || in_array($tk, $typesActive, true)) ? 'checked' : ''; ?>
                    <label class="filter-check">
                        <input type="checkbox" name="type[]" value="<?= $tk ?>" <?= $checked ?>>
                        <?= htmlspecialchars($tm['label']) ?>
                    </label>
                <?php endforeach; ?>
            </div>

            <div class="filter-group">
                <h4>Région</h4>
                <select name="region" class="filter-field">
                    <option value="">Toutes les régions</option>
                    <?php foreach ($regionsList as $rg): ?>
                        <option value="<?= (int) $rg['id'] ?>" <?= $regionId === (int) $rg['id'] ? 'selected' : '' ?>><?= htmlspecialchars($rg['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <h4>Prix maximum : <span class="filter-range-val" id="prixVal"><?= $prixMax > 0 ? (int) $prixMax : 1000 ?> TND</span></h4>
                <input type="range" name="prix_max" min="0" max="1000" step="10" value="<?= $prixMax > 0 ? (int) $prixMax : 1000 ?>" class="filter-field" id="prixRange" oninput="document.getElementById('prixVal').textContent=this.value+' TND'">
            </div>

            <div class="filter-group">
                <h4>Voyageurs</h4>
                <input type="number" name="personnes" class="filter-field" min="1" max="50" value="<?= (int) $personnes ?>">
            </div>

            <?php if ($date_debut !== ''): ?><input type="hidden" name="date_debut" value="<?= htmlspecialchars($date_debut) ?>"><?php endif; ?>
            <?php if ($date_fin !== ''): ?><input type="hidden" name="date_fin" value="<?= htmlspecialchars($date_fin) ?>"><?php endif; ?>

            <button type="submit" class="btn-filter">Appliquer les filtres</button>
            <a href="search.php" class="btn-reset">Réinitialiser les filtres</a>
        </form>
    </aside>

    <!-- RESULTS -->
    <div class="results-area">
        <p class="results-count"><strong><?= count($services) ?></strong> résultat(s)</p>
        <div class="results-grid">
            <?php if (empty($services)): ?>
                <div class="no-results" style="grid-column:1/-1;">
                    <h2>Aucun résultat trouvé</h2>
                    <p style="margin-bottom:24px; color:var(--muted);">Essayez d'élargir vos filtres ou de changer de destination.</p>
                    <a href="search.php" class="btn-filter" style="display:inline-block; width:auto; padding:12px 28px; text-decoration:none;">Réinitialiser</a>
                </div>
            <?php else: ?>
                <?php foreach ($services as $srv):
                    $img = formatImagePath($srv['photo_principale'], $srv['type']);
                    $rating = number_format(4.2 + ((((int) $srv['id']) * 7) % 8) / 10, 1); // stable per item
                    $serviceUrl = htmlspecialchars($srv['type'], ENT_QUOTES) . '.php?id=' . (int) $srv['id'];
                ?>
                    <a href="<?= $serviceUrl ?>" class="service-card-link">
                        <div class="service-card">
                            <div class="card-image">
                                <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($srv['titre']) ?>" loading="lazy">
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
                                        <span>/ <?= htmlspecialchars($srv['unit']) ?></span>
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
</div>

<?php include 'footer.php'; ?>

</body>
</html>
