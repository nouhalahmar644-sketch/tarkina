<?php
/**
 * One-time script to fix data that was corrupted by PowerShell encoding.
 * Run via browser: http://localhost/tarkina/fix_data.php
 */
require_once __DIR__ . '/db.php';

$fixes = [];

// Fix statut on all service tables
$tables = ['hebergement', 'repas', 'guide', 'evenement', 'artisanat'];
foreach ($tables as $t) {
    $sql = "UPDATE `$t` SET statut = 'publié' WHERE statut != 'brouillon'";
    if (mysqli_query($conn, $sql)) {
        $fixes[] = "$t statut → publié (" . mysqli_affected_rows($conn) . " rows)";
    } else {
        $fixes[] = "ERROR on $t: " . mysqli_error($conn);
    }
}

// Fix region descriptions that may have encoding issues
$region_data = [
    1 => [
        'nom' => 'Kessra',
        'description' => "Perchée dans les montagnes du centre-ouest de la Tunisie, Kessra est un village berbère authentique avec une forteresse byzantine millénaire et des paysages à couper le souffle. Un lieu idéal pour découvrir la culture amazighe, les oliveraies en terrasses et l'hospitalité légendaire des montagnards tunisiens.",
        'meilleure_saison' => 'Printemps / Automne',
        'langues' => 'Arabe, Amazigh, Français',
        'monnaie' => 'TND (Dinar Tunisien)'
    ],
    2 => [
        'nom' => 'Djerba',
        'description' => "L'île des rêves, Djerba est une destination emblématique connue pour ses plages de sable doré, ses villages pittoresques, sa synagogue historique de la Ghriba et son artisanat raffiné. Un mélange unique de cultures arabe, juive et berbère dans un cadre méditerranéen enchanteur.",
        'meilleure_saison' => 'Mai – Octobre',
        'langues' => 'Arabe, Français',
        'monnaie' => 'TND (Dinar Tunisien)'
    ],
    3 => [
        'nom' => 'Tozeur',
        'description' => "Porte du Sahara tunisien, Tozeur fascine par son architecture en briques ocre, sa palmeraie luxuriante de plus de 200 000 palmiers, et ses oasis de montagne spectaculaires. Découvrez le désert, les chotts salés et l'hospitalité du Sud tunisien.",
        'meilleure_saison' => 'Novembre – Mars',
        'langues' => 'Arabe, Français',
        'monnaie' => 'TND (Dinar Tunisien)'
    ]
];

foreach ($region_data as $id => $data) {
    $stmt = mysqli_prepare($conn, "UPDATE region SET nom=?, description=?, meilleure_saison=?, langues=?, monnaie=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'sssssi', $data['nom'], $data['description'], $data['meilleure_saison'], $data['langues'], $data['monnaie'], $id);
    if (mysqli_stmt_execute($stmt)) {
        $fixes[] = "region #{$id} ({$data['nom']}) → fixed";
    }
    mysqli_stmt_close($stmt);
}

// Fix hebergement data
$heb_data = [
    1 => ['titre' => 'Dar El Jebel - Maison traditionnelle', 'description' => "Séjournez dans une maison traditionnelle berbère entièrement restaurée avec vue panoramique sur les montagnes. Petit-déjeuner traditionnel inclus, terrasse ombragée et accueil chaleureux garanti.", 'inclus' => '["Petit-déjeuner traditionnel","Wi-Fi gratuit","Parking gratuit","Terrasse panoramique"]'],
    2 => ['titre' => 'Riad Djerba Bleu', 'description' => "Un riad traditionnel au cœur de Houmt Souk, décoré avec des mosaïques artisanales et un patio intérieur fleuri. Proche de la plage et du marché.", 'inclus' => '["Petit-déjeuner","Climatisation","Piscine","Navette plage"]'],
    3 => ['titre' => 'Oasis Lodge Tozeur', 'description' => "Lodge éco-responsable niché au cœur de la palmeraie de Tozeur. Architecture en pisé, chambres climatisées et restaurant servant des spécialités du Sud.", 'inclus' => '["Demi-pension","Piscine","Excursion palmeraie","Wi-Fi"]']
];

foreach ($heb_data as $id => $data) {
    $stmt = mysqli_prepare($conn, "UPDATE hebergement SET titre=?, description=?, inclus=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'sssi', $data['titre'], $data['description'], $data['inclus'], $id);
    if (mysqli_stmt_execute($stmt)) {
        $fixes[] = "hebergement #{$id} → fixed";
    }
    mysqli_stmt_close($stmt);
}

// Fix repas
$repas_data = [
    1 => ['titre' => 'Couscous traditionnel chez Oum Salah', 'description' => "Dégustez un couscous préparé à la main par Oum Salah, selon une recette familiale transmise depuis quatre générations. Légumes du jardin, viande d'agneau et harissa maison.", 'inclus' => '["Entrée","Plat principal","Dessert","Thé à la menthe"]'],
    2 => ['titre' => 'Déjeuner pêcheur à Djerba', 'description' => "Repas de poissons frais pêchés le matin même, grillés au charbon de bois avec salade mechouia et pain tabouna. Vue sur la mer depuis la terrasse.", 'inclus' => '["Salade mechouia","Poisson grillé","Fruits de saison","Boisson"]']
];

foreach ($repas_data as $id => $data) {
    $stmt = mysqli_prepare($conn, "UPDATE repas SET titre=?, description=?, inclus=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'sssi', $data['titre'], $data['description'], $data['inclus'], $id);
    if (mysqli_stmt_execute($stmt)) {
        $fixes[] = "repas #{$id} → fixed";
    }
    mysqli_stmt_close($stmt);
}

// Fix guides
$guide_data = [
    1 => ['titre' => 'Randonnée Kessra - Sentier des Oliviers', 'description' => "Parcourez les sentiers millénaires de Kessra avec un guide local passionné. Visite de la forteresse byzantine, des grottes troglodytes et des oliveraies en terrasses. Déjeuner pique-nique inclus.", 'inclus' => '["Guide certifié","Déjeuner pique-nique","Eau minérale","Transport local"]'],
    2 => ['titre' => 'Tour de Djerba en Tuk-Tuk', 'description' => "Découvrez les trésors cachés de Djerba en tuk-tuk : villages de pêcheurs, synagogue de la Ghriba, ateliers de poterie et coucher de soleil sur la plage.", 'inclus' => '["Tuk-tuk privé","Guide francophone","Entrées musées","Thé offert"]']
];

foreach ($guide_data as $id => $data) {
    $stmt = mysqli_prepare($conn, "UPDATE guide SET titre=?, description=?, inclus=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'sssi', $data['titre'], $data['description'], $data['inclus'], $id);
    if (mysqli_stmt_execute($stmt)) {
        $fixes[] = "guide #{$id} → fixed";
    }
    mysqli_stmt_close($stmt);
}

// Fix evenements
$eve_data = [
    1 => ['titre' => 'Festival des Oliviers de Kessra', 'description' => "Festival annuel célébrant la récolte des olives avec musique traditionnelle, danse folklorique, ateliers de pressage d'huile d'olive et dégustation de produits locaux.", 'inclus' => '["Entrée festival","Dégustation huile d\'olive","Concert traditionnel"]'],
    2 => ['titre' => 'Nuit des Étoiles à Tozeur', 'description' => "Observation astronomique dans le désert avec un astrophotographe professionnel. Dîner bédouin sous les étoiles, balade en chameau au coucher du soleil.", 'inclus' => '["Transport désert","Dîner bédouin","Télescope","Balade chameau"]']
];

foreach ($eve_data as $id => $data) {
    $stmt = mysqli_prepare($conn, "UPDATE evenement SET titre=?, description=?, inclus=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'sssi', $data['titre'], $data['description'], $data['inclus'], $id);
    if (mysqli_stmt_execute($stmt)) {
        $fixes[] = "evenement #{$id} → fixed";
    }
    mysqli_stmt_close($stmt);
}

// Fix artisanat
$art_data = [
    1 => ['titre' => 'Tapis berbère fait main', 'description' => "Tapis tissé à la main par les artisanes de Kessra selon des motifs traditionnels amazighs. Laine naturelle teinte avec des pigments végétaux. Dimensions : 120x180 cm."],
    2 => ['titre' => 'Poterie de Djerba', 'description' => "Ensemble de poteries artisanales de Guellala, village potier historique de Djerba. Inclut un vase, un bol et une assiette décorée. Pièces uniques peintes à la main."],
    3 => ['titre' => "Huile d'olive extra vierge BIO", 'description' => "Huile d'olive extra vierge biologique pressée à froid, récoltée dans les oliveraies centenaires de Kessra. Bouteille artisanale de 750ml."]
];

foreach ($art_data as $id => $data) {
    $stmt = mysqli_prepare($conn, "UPDATE artisanat SET titre=?, description=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'ssi', $data['titre'], $data['description'], $id);
    if (mysqli_stmt_execute($stmt)) {
        $fixes[] = "artisanat #{$id} → fixed";
    }
    mysqli_stmt_close($stmt);
}

// Fix user passwords
$admin_hash = password_hash('admin123', PASSWORD_DEFAULT);
$test_hash = password_hash('test123', PASSWORD_DEFAULT);

$stmt = mysqli_prepare($conn, "UPDATE utilisateur SET mot_de_passe=? WHERE email='admin@tarkina.tn'");
mysqli_stmt_bind_param($stmt, 's', $admin_hash);
mysqli_stmt_execute($stmt);
$fixes[] = "admin password → fixed";
mysqli_stmt_close($stmt);

$stmt = mysqli_prepare($conn, "UPDATE utilisateur SET mot_de_passe=? WHERE email='test@test.com'");
mysqli_stmt_bind_param($stmt, 's', $test_hash);
mysqli_stmt_execute($stmt);
$fixes[] = "test user password → fixed";
mysqli_stmt_close($stmt);

echo "<h1>Data Fix Complete</h1>";
echo "<ul>";
foreach ($fixes as $f) {
    echo "<li>$f</li>";
}
echo "</ul>";
echo "<p><a href='index.php'>← Retour à l'accueil</a></p>";
?>
