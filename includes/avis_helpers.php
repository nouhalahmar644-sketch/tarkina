<?php
/**
 * Helpers for the "avis" (reviews) system.
 * The `avis` table stores one rating per service via a per-type FK column
 * (hebergement_id, repas_id, guide_id, evenement_id, artisanat_id).
 * MySQLi procedural. Read/write only the existing table (no schema changes).
 */

function avis_col(string $type): ?string
{
    $map = [
        'hebergement' => 'hebergement_id',
        'repas'       => 'repas_id',
        'guide'       => 'guide_id',
        'evenement'   => 'evenement_id',
        'artisanat'   => 'artisanat_id',
    ];
    return $map[$type] ?? null;
}

function avis_list($conn, string $type, int $serviceId): array
{
    $col = avis_col($type);
    if (!$col || $serviceId <= 0) {
        return [];
    }
    $sql = "SELECT a.note, a.commentaire, a.created_at, u.nom, u.prenom
            FROM avis a
            JOIN utilisateur u ON a.utilisateur_id = u.id
            WHERE a.`$col` = ?
            ORDER BY a.created_at DESC";
    $st = mysqli_prepare($conn, $sql);
    if (!$st) {
        return [];
    }
    mysqli_stmt_bind_param($st, 'i', $serviceId);
    mysqli_stmt_execute($st);
    $res = mysqli_stmt_get_result($st);
    $rows = [];
    while ($r = mysqli_fetch_assoc($res)) {
        $rows[] = $r;
    }
    mysqli_stmt_close($st);
    return $rows;
}

function avis_summary($conn, string $type, int $serviceId): array
{
    $col = avis_col($type);
    if (!$col || $serviceId <= 0) {
        return ['count' => 0, 'avg' => 0.0];
    }
    $sql = "SELECT COUNT(*) AS c, COALESCE(AVG(note), 0) AS a FROM avis WHERE `$col` = ?";
    $st = mysqli_prepare($conn, $sql);
    if (!$st) {
        return ['count' => 0, 'avg' => 0.0];
    }
    mysqli_stmt_bind_param($st, 'i', $serviceId);
    mysqli_stmt_execute($st);
    mysqli_stmt_bind_result($st, $c, $a);
    mysqli_stmt_fetch($st);
    mysqli_stmt_close($st);
    return ['count' => (int) $c, 'avg' => round((float) $a, 1)];
}

function avis_user_has($conn, string $type, int $serviceId, int $userId): bool
{
    $col = avis_col($type);
    if (!$col || $serviceId <= 0 || $userId <= 0) {
        return false;
    }
    $sql = "SELECT 1 FROM avis WHERE `$col` = ? AND utilisateur_id = ? LIMIT 1";
    $st = mysqli_prepare($conn, $sql);
    if (!$st) {
        return false;
    }
    mysqli_stmt_bind_param($st, 'ii', $serviceId, $userId);
    mysqli_stmt_execute($st);
    mysqli_stmt_store_result($st);
    $has = mysqli_stmt_num_rows($st) > 0;
    mysqli_stmt_close($st);
    return $has;
}

/**
 * Insert one review, enforcing one-per-user-per-service.
 * Returns true on success; sets $err on failure.
 */
function avis_insert($conn, string $type, int $serviceId, int $userId, int $note, string $commentaire, ?string &$err = null): bool
{
    $col = avis_col($type);
    if (!$col) { $err = 'Type de service invalide.'; return false; }
    if ($serviceId <= 0 || $userId <= 0) { $err = 'Données invalides.'; return false; }
    if ($note < 1 || $note > 5) { $err = 'Veuillez choisir une note entre 1 et 5 étoiles.'; return false; }
    if (avis_user_has($conn, $type, $serviceId, $userId)) {
        $err = 'Vous avez déjà laissé un avis pour ce service.';
        return false;
    }
    $sql = "INSERT INTO avis (`$col`, utilisateur_id, note, commentaire) VALUES (?, ?, ?, ?)";
    $st = mysqli_prepare($conn, $sql);
    if (!$st) { $err = 'Erreur technique.'; return false; }
    mysqli_stmt_bind_param($st, 'iiis', $serviceId, $userId, $note, $commentaire);
    $ok = mysqli_stmt_execute($st);
    if (!$ok) { $err = mysqli_stmt_error($st); }
    mysqli_stmt_close($st);
    return $ok;
}

function avis_stars_html(int $n): string
{
    $n = max(0, min(5, $n));
    return str_repeat('★', $n) . str_repeat('☆', 5 - $n);
}
