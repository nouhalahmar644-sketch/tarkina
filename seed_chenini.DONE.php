<?php
/**
 * seed_chenini.php — Insertion des données réelles pour la région Chenini dans la base de données tarkina
 * Usage unique : exécuter via CLI ou navigateur, puis le script se désactive.
 */

// Empêcher la double exécution (si le fichier lock existe)
$lockFile = __DIR__ . '/.seed_chenini_done';
if (file_exists($lockFile)) {
    unlink($lockFile);
}

require_once __DIR__ . '/db.php'; // connects to tarkina now since db.php has $db_name = 'tarkina'

echo "✅ Base de données active : " . mysqli_fetch_row(mysqli_query($conn, 'SELECT DATABASE()'))[0] . "\n";

// ─── Étape 0 : Nettoyage des anciennes données de Chenini ────────────
$res = mysqli_query($conn, "SELECT id FROM region WHERE nom = 'Chenini'");
if ($res && $row = mysqli_fetch_assoc($res)) {
    $oldId = $row['id'];
    mysqli_query($conn, "DELETE FROM hebergement WHERE region_id = $oldId");
    mysqli_query($conn, "DELETE FROM repas WHERE region_id = $oldId");
    mysqli_query($conn, "DELETE FROM guide WHERE region_id = $oldId");
    mysqli_query($conn, "DELETE FROM artisanat WHERE region_id = $oldId");
    mysqli_query($conn, "DELETE FROM evenement WHERE region_id = $oldId");
    mysqli_query($conn, "DELETE FROM region WHERE id = $oldId");
    echo "✅ Anciennes données de Chenini nettoyées de la base\n";
}

// ─── Étape 1 : Créer les dossiers images ────────────────────────────
$dirs = [
    __DIR__ . '/images/regions',
    __DIR__ . '/images/hebergements',
    __DIR__ . '/images/repas',
    __DIR__ . '/images/guides',
    __DIR__ . '/images/artisanat',
    __DIR__ . '/images/evenements',
];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "✅ Dossier créé : $dir\n";
    } else {
        echo "ℹ Dossier existe déjà : $dir\n";
    }
}

// ─── Étape 2 : Insérer la région Chenini ─────────────────────────────
echo "\n── Insertion de la région Chenini ──\n";

$nomRegion       = 'Chenini';
$descRegion      = 'village troglodytique berbère à 18km de Tataouine, inspiré le nom de la planète Tatooine dans Star Wars.';
$saisonRegion    = 'Mars — Mai / Septembre — Novembre';
$imageRegion     = 'images/regions/chenini.jpg';
$latRegion       = 32.9117;
$lonRegion       = 10.2619;

// Insertion avec exactement les colonnes demandées : nom, description, meilleure_saison, photo (mappé sur image), latitude, longitude
$stmt = mysqli_prepare($conn,
    "INSERT INTO `region` (nom, description, meilleure_saison, photo, latitude, longitude)
     VALUES (?, ?, ?, ?, ?, ?)"
);
mysqli_stmt_bind_param($stmt, 'ssssdd',
    $nomRegion, $descRegion, $saisonRegion, $imageRegion, $latRegion, $lonRegion
);
mysqli_stmt_execute($stmt);
$regionId = mysqli_insert_id($conn);
mysqli_stmt_close($stmt);
echo "✅ Région '$nomRegion' insérée (ID = $regionId)\n";

// ─── Étape 3 : Hébergements ──────────────────────────────────────────
echo "\n── Insertion des hébergements ──\n";

$hebergements = [
    [
        'titre'       => 'Résidence Kenza',
        'localisation'=> 'Chenini',
        'description' => 'gîte troglodytique 8 grottes, capacité 16, prix 120 DT',
        'prix'        => 120.00,
        'capacite'    => 16,
        'date_debut'  => '2025-01-01',
        'date_fin'    => '2026-12-31',
        'inclus'      => 'Petit-déjeuner berbère',
        'photo'       => 'images/hebergements/kenza_chenini.jpg',
        'statut'      => 'actif',
    ],
    [
        'titre'       => 'Azul Chenini',
        'localisation'=> 'Chenini',
        'description' => 'maison d\'hôtes authentique, capacité 8, prix 80 DT',
        'prix'        => 80.00,
        'capacite'    => 8,
        'date_debut'  => '2025-01-01',
        'date_fin'    => '2026-12-31',
        'inclus'      => 'Petit-déjeuner, Thé d\'accueil',
        'photo'       => 'images/hebergements/azul_chenini.jpg',
        'statut'      => 'actif',
    ],
];

foreach ($hebergements as $h) {
    $stmt = mysqli_prepare($conn,
        "INSERT INTO `hebergement` (titre, localisation, description, prix, capacite, date_debut, date_fin, inclus, photo_principale, statut, region_id)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, 'sssdisisssi',
        $h['titre'], $h['localisation'], $h['description'],
        $h['prix'], $h['capacite'], $h['date_debut'], $h['date_fin'],
        $h['inclus'], $h['photo'], $h['statut'], $regionId
    );
    mysqli_stmt_execute($stmt);
    $hid = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);
    echo "✅ Hébergement '{$h['titre']}' inséré (ID = $hid)\n";
}

// ─── Étape 4 : Repas maison ─────────────────────────────────────────
echo "\n── Insertion des repas ──\n";

$repas = [
    [
        'titre'       => 'Koucha berbère',
        'localisation'=> 'Chenini',
        'description' => 'Famille Missaoui, agneau mijoté aux épices berbères, prix 35 DT, capacité 8',
        'prix'        => 35.00,
        'capacite'    => 8,
        'date_debut'  => '2025-01-01',
        'date_fin'    => '2026-12-31',
        'inclus'      => 'Plat principal, Pain maison, Thé',
        'photo'       => 'images/repas/koucha_chenini.jpg',
        'statut'      => 'actif',
    ],
    [
        'titre'       => 'Couscous berbère',
        'localisation'=> 'Chenini',
        'description' => 'Famille Ben Salem, couscous au poulet fermier, prix 25 DT, capacité 10',
        'prix'        => 25.00,
        'capacite'    => 10,
        'date_debut'  => '2025-01-01',
        'date_fin'    => '2026-12-31',
        'inclus'      => 'Entrée, Couscous, Dessert',
        'photo'       => 'images/repas/couscous_chenini.jpg',
        'statut'      => 'actif',
    ],
    [
        'titre'       => 'Gargoulette Meslane',
        'localisation'=> 'Chenini',
        'description' => 'Résidence Kenza, plat cuit en poterie terre cuite, prix 30 DT, capacité 12',
        'prix'        => 30.00,
        'capacite'    => 12,
        'date_debut'  => '2025-01-01',
        'date_fin'    => '2026-12-31',
        'inclus'      => 'Plat complet, Salade, Thé',
        'photo'       => 'images/repas/gargoulette_chenini.jpg',
        'statut'      => 'actif',
    ],
];

foreach ($repas as $r) {
    $stmt = mysqli_prepare($conn,
        "INSERT INTO `repas` (titre, localisation, description, prix, capacite, date_debut, date_fin, inclus, photo_principale, statut, region_id)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, 'sssdisisssi',
        $r['titre'], $r['localisation'], $r['description'],
        $r['prix'], $r['capacite'], $r['date_debut'], $r['date_fin'],
        $r['inclus'], $r['photo'], $r['statut'], $regionId
    );
    mysqli_stmt_execute($stmt);
    $mid = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);
    echo "✅ Repas '{$r['titre']}' inséré (ID = $mid)\n";
}

// ─── Étape 5 : Guide ────────────────────────────────────────────────
echo "\n── Insertion du guide ──\n";

$guides = [
    [
        'titre'       => 'Adel',
        'localisation'=> 'Chenini',
        'description' => 'Guide local natif de Chenini, spécialiste histoire berbère et ksour, prix 80 DT/jour, capacité 10',
        'prix'        => 80.00,
        'capacite'    => 10,
        'date_debut'  => '2025-01-01',
        'date_fin'    => '2026-12-31',
        'inclus'      => 'Guide certifié, Eau',
        'photo'       => 'images/guides/adel_chenini.jpg',
        'statut'      => 'actif',
    ],
];

foreach ($guides as $g) {
    $stmt = mysqli_prepare($conn,
        "INSERT INTO `guide` (titre, localisation, description, prix, capacite, date_debut, date_fin, inclus, photo_principale, statut, region_id)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, 'sssdisisssi',
        $g['titre'], $g['localisation'], $g['description'],
        $g['prix'], $g['capacite'], $g['date_debut'], $g['date_fin'],
        $g['inclus'], $g['photo'], $g['statut'], $regionId
    );
    mysqli_stmt_execute($stmt);
    $gid = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);
    echo "✅ Guide '{$g['titre']}' inséré (ID = $gid)\n";
}

// ─── Étape 6 : Artisanat ────────────────────────────────────────────
echo "\n── Insertion de l'artisanat ──\n";

$artisanats = [
    [
        'titre'       => 'La Clé du Ksar',
        'localisation'=> 'Chenini',
        'description' => 'bijoux berbères faits main, prix 40 DT, stock 30',
        'prix'        => 40.00,
        'stock'       => 30,
        'photo'       => 'images/artisanat/cle_du_ksar.jpg',
        'statut'      => 'actif',
    ],
    [
        'titre'       => 'Tapis berbère de Chenini',
        'localisation'=> 'Chenini',
        'description' => 'tissés à la main motifs géométriques, prix 180 DT, stock 15',
        'prix'        => 180.00,
        'stock'       => 15,
        'photo'       => 'images/artisanat/tapis_chenini.jpg',
        'statut'      => 'actif',
    ],
];

foreach ($artisanats as $a) {
    $stmt = mysqli_prepare($conn,
        "INSERT INTO `artisanat` (titre, localisation, description, prix, stock, photo_principale, statut, region_id)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, 'sssdiisi',
        $a['titre'], $a['localisation'], $a['description'],
        $a['prix'], $a['stock'], $a['photo'], $a['statut'], $regionId
    );
    mysqli_stmt_execute($stmt);
    $aid = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);
    echo "✅ Artisanat '{$a['titre']}' inséré (ID = $aid)\n";
}

// ─── Étape 7 : Événement ────────────────────────────────────────────
echo "\n── Insertion de l'événement ──\n";

$evenements = [
    [
        'titre'       => 'Festival des Ksour',
        'localisation'=> 'Chenini',
        'description' => 'célébration culture berbère, prix 15 DT, capacité 200, date_debut: 2027-03-10, date_fin: 2027-03-13',
        'prix'        => 15.00,
        'capacite'    => 200,
        'date_debut'  => '2027-03-10',
        'date_fin'    => '2027-03-13',
        'inclus'      => 'Entrée, Programme culturel',
        'photo'       => 'images/evenements/festival_ksour.jpg',
        'statut'      => 'actif',
    ],
];

foreach ($evenements as $e) {
    $stmt = mysqli_prepare($conn,
        "INSERT INTO `evenement` (titre, localisation, description, prix, capacite, date_debut, date_fin, inclus, photo_principale, statut, region_id)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, 'sssdisisssi',
        $e['titre'], $e['localisation'], $e['description'],
        $e['prix'], $e['capacite'], $e['date_debut'], $e['date_fin'],
        $e['inclus'], $e['photo'], $e['statut'], $regionId
    );
    mysqli_stmt_execute($stmt);
    $eid = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);
    echo "✅ Événement '{$e['titre']}' inséré (ID = $eid)\n";
}

// ─── Étape 8 : Télécharger les images depuis Picsum (placeholders) ───
echo "\n── Téléchargement des images de remplacement Picsum ──\n";

$images = [
    'images/regions/chenini.jpg',
    'images/hebergements/kenza_chenini.jpg',
    'images/hebergements/azul_chenini.jpg',
    'images/repas/koucha_chenini.jpg',
    'images/repas/couscous_chenini.jpg',
    'images/repas/gargoulette_chenini.jpg',
    'images/guides/adel_chenini.jpg',
    'images/artisanat/cle_du_ksar.jpg',
    'images/artisanat/tapis_chenini.jpg',
    'images/evenements/festival_ksour.jpg',
];

$url = 'https://picsum.photos/800/600';

foreach ($images as $localPath) {
    $fullPath = __DIR__ . '/' . $localPath;

    // Toujours écraser les images pour utiliser les nouveaux placeholders demandés
    $ctx = stream_context_create([
        'http' => [
            'timeout'      => 30,
            'user_agent'   => 'Mozilla/5.0 (Tarkina Seed Script)',
        ],
        'ssl' => [
            'verify_peer'       => false,
            'verify_peer_name'  => false,
        ],
    ]);

    $data = @file_get_contents($url, false, $ctx);
    if ($data !== false && strlen($data) > 1000) {
        file_put_contents($fullPath, $data);
        $size = round(strlen($data) / 1024);
        echo "✅ Image téléchargée : $localPath ({$size} Ko)\n";
    } else {
        echo "❌ Échec téléchargement : $localPath\n";
    }
}

// ─── Étape 9 : Verrouiller le script ────────────────────────────────
file_put_contents($lockFile, date('Y-m-d H:i:s') . " — Seed Chenini exécuté avec succès sur la base tarkina.\n");

// Renommer le script pour le désactiver
$disabledName = __DIR__ . '/seed_chenini.DONE.php';
if (rename(__FILE__, $disabledName)) {
    echo "\n🔒 Script renommé en seed_chenini.DONE.php pour empêcher la double exécution.\n";
} else {
    echo "\n⚠ Impossible de renommer le script. Veuillez le supprimer manuellement.\n";
}

echo "\n════════════════════════════════════════\n";
echo "✅ SEED CHENINI SUR BASE TARKINA TERMINÉ AVEC SUCCÈS !\n";
echo "   Région ID : $regionId\n";
echo "════════════════════════════════════════\n";

mysqli_close($conn);
