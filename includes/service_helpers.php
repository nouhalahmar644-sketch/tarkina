<?php
/**
 * Helpers partagés pour les pages de service (hébergement, repas, guide, etc.)
 */

function service_ensure_reservations_table($conn)
{
    $createSql = "CREATE TABLE reservations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        type_service VARCHAR(50),
        service_id INT,
        date_debut DATE,
        date_fin DATE,
        nb_voyageurs INT DEFAULT 1,
        prix_total DECIMAL(10,2),
        nom VARCHAR(150),
        email VARCHAR(150),
        message TEXT,
        statut VARCHAR(30) DEFAULT 'en_attente',
        created_at DATETIME DEFAULT NOW()
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    $tableRes = mysqli_query($conn, "SHOW TABLES LIKE 'reservations'");
    $tableExists = $tableRes && mysqli_num_rows($tableRes) > 0;
    if ($tableRes) {
        mysqli_free_result($tableRes);
    }

    if (!$tableExists) {
        mysqli_query($conn, $createSql);
        return;
    }

    $colRes = mysqli_query($conn, "SHOW COLUMNS FROM reservations LIKE 'user_id'");
    $hasUserId = $colRes && mysqli_num_rows($colRes) > 0;
    if ($colRes) {
        mysqli_free_result($colRes);
    }

    if (!$hasUserId) {
        mysqli_query($conn, 'DROP TABLE IF EXISTS reservations');
        mysqli_query($conn, $createSql);
    }
}

function service_fetch_item($conn, $table, $id)
{
    $allowed = ['hebergement', 'repas', 'guide', 'evenement', 'artisanat'];
    if (!in_array($table, $allowed, true) || $id <= 0) {
        return null;
    }

    $sql = "SELECT t.*, r.id AS region_id, r.nom AS region_nom
            FROM `$table` t
            LEFT JOIN region r ON t.region_id = r.id
            WHERE t.id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt === false) {
        return null;
    }
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $row ?: null;
}

function service_photo_url($path, $fallback = '')
{
    if (empty($path)) {
        return $fallback !== '' ? $fallback : 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1200&q=80';
    }
    if (strpos($path, 'http') === 0) {
        return $path;
    }
    if (strpos($path, 'uploads/') === 0 || strpos($path, 'images/') === 0) {
        return $path;
    }
    return 'uploads/' . ltrim($path, '/');
}

function service_main_photo($item, $fallback = '')
{
    if (!empty($item['photo_principale'])) {
        return service_photo_url($item['photo_principale'], $fallback);
    }
    if (!empty($item['image'])) {
        return service_photo_url($item['image'], $fallback);
    }
    return service_photo_url('', $fallback);
}

function service_secondary_photos($item, $count = 4, $fallback = '')
{
    $photos = [];
    if (!empty($item['photos_sec'])) {
        $decoded = json_decode($item['photos_sec'], true);
        if (is_array($decoded)) {
            foreach ($decoded as $p) {
                $url = service_photo_url($p, '');
                // Only include non-empty, non-duplicate URLs
                if ($url !== '' && !in_array($url, $photos, true)) {
                    $photos[] = $url;
                }
            }
        }
    }
    // Return only real photos — no padding with the main photo
    return array_slice($photos, 0, $count);
}

function service_localisation($item)
{
    if (!empty($item['localisation'])) {
        return $item['localisation'];
    }
    if (!empty($item['region'])) {
        return $item['region'];
    }
    if (!empty($item['region_nom'])) {
        return $item['region_nom'];
    }
    return 'Tunisie';
}

function service_region_link_id($item)
{
    return !empty($item['region_id']) ? (int) $item['region_id'] : 0;
}

/**
 * Résout region_id et region_nom si le JOIN n'a pas trouvé la région.
 */
function service_resolve_region($conn, array &$item)
{
    if (!empty($item['region_id']) && (int) $item['region_id'] > 0) {
        if (empty($item['region_nom'])) {
            $rid = (int) $item['region_id'];
            $stmt = mysqli_prepare($conn, 'SELECT nom FROM region WHERE id = ? LIMIT 1');
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'i', $rid);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $nomDb);
                if (mysqli_stmt_fetch($stmt)) {
                    $item['region_nom'] = $nomDb;
                }
                mysqli_stmt_close($stmt);
            }
        }
        return (int) $item['region_id'];
    }

    $candidates = array_unique(array_filter([
        isset($item['region']) ? trim((string) $item['region']) : '',
        isset($item['localisation']) ? trim((string) $item['localisation']) : '',
    ]));

    foreach ($candidates as $name) {
        $like = '%' . $name . '%';
        $stmt = mysqli_prepare($conn, 'SELECT id, nom FROM region WHERE nom LIKE ? LIMIT 1');
        if ($stmt === false) {
            continue;
        }
        mysqli_stmt_bind_param($stmt, 's', $like);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $idDb, $nomDb);
        if (mysqli_stmt_fetch($stmt)) {
            $item['region_id'] = (int) $idDb;
            $item['region_nom'] = $nomDb;
            mysqli_stmt_close($stmt);
            return (int) $idDb;
        }
        mysqli_stmt_close($stmt);
    }

    return 0;
}

function service_default_inclus()
{
    return [
        'Accueil personnalisé',
        'Hôte local vérifié',
        'Support 7j/7',
        'Annulation flexible',
    ];
}

function service_placeholder_reviews()
{
    return [
        ['name' => 'Léa M.', 'text' => 'Une rencontre incroyable, hôte adorable et lieu magique.'],
        ['name' => 'Yassine R.', 'text' => 'À refaire absolument. Tout était parfait et authentique.'],
    ];
}

/**
 * Normalise a date string to Y-m-d for safe MySQL insertion.
 * Accepts: Y-m-d, d/m/Y, Y/m/d, or any format strtotime() understands.
 * Returns NULL string ('') when the date cannot be parsed.
 */
function sanitize_date(string $raw): string
{
    $raw = trim($raw);
    if ($raw === '') {
        return '';
    }

    // Already Y-m-d (most common from flatpickr)
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $raw)) {
        return $raw;
    }

    // d/m/Y  (flatpickr altFormat, or manual user input)
    if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $raw, $m)) {
        return sprintf('%04d-%02d-%02d', (int)$m[3], (int)$m[2], (int)$m[1]);
    }

    // Y/m/d
    if (preg_match('/^(\d{4})\/(\d{1,2})\/(\d{1,2})$/', $raw, $m)) {
        return sprintf('%04d-%02d-%02d', (int)$m[1], (int)$m[2], (int)$m[3]);
    }

    // Fallback: let strtotime() try
    $ts = strtotime($raw);
    if ($ts === false || $ts < 0) {
        return '';
    }
    return date('Y-m-d', $ts);
}

function service_insert_reservation($conn, array $data, &$errorMessage = null)
{
    service_ensure_reservations_table($conn);

    $sql = 'INSERT INTO reservations
        (user_id, type_service, service_id, date_debut, date_fin, nb_voyageurs, prix_total, nom, email, message, statut, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())';
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt === false) {
        $errorMessage = mysqli_error($conn);
        return false;
    }

    $statut = 'en_attente';
    $userId = (int) $data['user_id'];
    $typeService = (string) $data['type_service'];
    $serviceId = (int) $data['service_id'];
    $dateDebut = sanitize_date((string) $data['date_debut']);
    $dateFin   = sanitize_date((string) $data['date_fin']);
    $nbVoyageurs = (int) $data['nb_voyageurs'];
    $prixTotal = (float) $data['prix_total'];
    $nom = (string) $data['nom'];
    $email = (string) $data['email'];
    $message = (string) ($data['message'] ?? '');

    mysqli_stmt_bind_param(
        $stmt,
        'isissidssss',
        $userId,
        $typeService,
        $serviceId,
        $dateDebut,
        $dateFin,
        $nbVoyageurs,
        $prixTotal,
        $nom,
        $email,
        $message,
        $statut
    );

    $ok = mysqli_stmt_execute($stmt);
    if (!$ok) {
        $errorMessage = mysqli_stmt_error($stmt) . ' | ' . mysqli_error($conn);
        mysqli_stmt_close($stmt);
        return false;
    }

    $id = (int) mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);
    return $id > 0 ? $id : false;
}

/**
 * Returns true if the given user has already reserved this service.
 * Ignores annulée/cancelled rows.
 */
function service_user_has_reservation($conn, int $userId, string $type, int $serviceId): bool
{
    if ($userId <= 0 || $serviceId <= 0 || $type === '') return false;
    service_ensure_reservations_table($conn);
    $sql = "SELECT 1 FROM reservations
            WHERE user_id = ? AND type_service = ? AND service_id = ?
              AND statut NOT IN ('annulée','annulee','cancelled')
            LIMIT 1";
    if (!$st = mysqli_prepare($conn, $sql)) return false;
    mysqli_stmt_bind_param($st, 'isi', $userId, $type, $serviceId);
    mysqli_stmt_execute($st);
    mysqli_stmt_store_result($st);
    $exists = mysqli_stmt_num_rows($st) > 0;
    mysqli_stmt_close($st);
    return $exists;
}
