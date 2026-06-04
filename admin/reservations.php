<?php
require_once __DIR__ . '/includes/auth_admin.php';

// Make sure the `packs` table exists (auto-created elsewhere) before we LEFT-JOIN it.
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS packs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    slogan VARCHAR(500) NOT NULL DEFAULT '',
    region_id INT NOT NULL,
    image_path VARCHAR(500) NOT NULL DEFAULT '',
    prix_original DECIMAL(10,2) NOT NULL DEFAULT 0,
    prix_final DECIMAL(10,2) NOT NULL DEFAULT 0,
    statut VARCHAR(20) NOT NULL DEFAULT 'actif',
    position INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

$flashSuccess = '';
$flashError = '';

if (!empty($_SESSION['admin_flash_success'])) {
    $flashSuccess = (string) $_SESSION['admin_flash_success'];
    unset($_SESSION['admin_flash_success']);
}
if (!empty($_SESSION['admin_flash_error'])) {
    $flashError = (string) $_SESSION['admin_flash_error'];
    unset($_SESSION['admin_flash_error']);
}

// Handle status change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_status') {
    $targetId = isset($_POST['reservation_id']) ? (int) $_POST['reservation_id'] : 0;
    $newStatus = isset($_POST['statut']) ? trim((string) $_POST['statut']) : '';
    $redirectUrl = 'reservations.php';

    if ($targetId <= 0 || !in_array($newStatus, ['en_attente', 'confirmé', 'terminé', 'annulée'])) {
        $_SESSION['admin_flash_error'] = 'Action invalide.';
        header('Location: ' . $redirectUrl);
        exit;
    }

    $updateSql = 'UPDATE reservations SET statut = ? WHERE id = ? LIMIT 1';
    $updateStmt = mysqli_prepare($conn, $updateSql);
    if ($updateStmt === false) {
        $_SESSION['admin_flash_error'] = 'Modification impossible pour le moment.';
    } else {
        mysqli_stmt_bind_param($updateStmt, 'si', $newStatus, $targetId);
        mysqli_stmt_execute($updateStmt);
        $affected = mysqli_stmt_affected_rows($updateStmt);
        mysqli_stmt_close($updateStmt);
        if ($affected >= 0) {
            $_SESSION['admin_flash_success'] = 'Statut mis à jour avec succès.';
        } else {
            $_SESSION['admin_flash_error'] = 'Réservation introuvable.';
        }
    }

    header('Location: ' . $redirectUrl);
    exit;
}

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$page = max(1, $page);
$perPage = 15;
$offset = ($page - 1) * $perPage;

// Show ALL service-types in the master reservations view so admins see every booking
// (hebergement, repas, guide, evenement, artisanat, forfait).
$countSql = "SELECT COUNT(*) FROM reservations";
$countRes = mysqli_query($conn, $countSql);
$totalReservations = 0;
if ($countRes) {
    $row = mysqli_fetch_row($countRes);
    $totalReservations = (int) $row[0];
}

$totalPages = (int) ceil($totalReservations / $perPage);
if ($totalPages > 0 && $page > $totalPages) {
    $page = $totalPages;
    $offset = ($page - 1) * $perPage;
}

// ── Mini-stat counts ──
$resEnAttente = 0; $resConfirmees = 0; $resTerminees = 0; $resAnnulees = 0; $resCA = 0.0; $resDerniereConf = null;
$statRes = mysqli_query($conn, "SELECT
    SUM(statut IN ('en_attente','pending')) AS en_attente,
    SUM(statut IN ('confirmé','confirmed','confirmee','accepté')) AS confirmees,
    SUM(statut IN ('terminé','terminee','termine')) AS terminees,
    SUM(statut IN ('annulée','annulee','cancelled')) AS annulees,
    SUM(CASE WHEN statut NOT IN ('annulée','annulee','cancelled') THEN prix_total ELSE 0 END) AS ca,
    MAX(CASE WHEN statut IN ('confirmé','confirmed','confirmee','accepté') THEN created_at ELSE NULL END) AS derniere_conf
    FROM reservations");
if ($statRes && $sr = mysqli_fetch_assoc($statRes)) {
    $resEnAttente   = (int)($sr['en_attente'] ?? 0);
    $resConfirmees  = (int)($sr['confirmees'] ?? 0);
    $resTerminees   = (int)($sr['terminees'] ?? 0);
    $resAnnulees    = (int)($sr['annulees'] ?? 0);
    $resCA          = (float)($sr['ca'] ?? 0);
    $resDerniereConf = $sr['derniere_conf'] ?? null;
}

$listSql = "
    SELECT r.*,
           u.nom AS user_nom, u.prenom AS user_prenom,
           CASE r.type_service
               WHEN 'hebergement' THEN h.titre
               WHEN 'repas'       THEN rp.titre
               WHEN 'guide'       THEN g.titre
               WHEN 'evenement'   THEN e.titre
               WHEN 'artisanat'   THEN a.titre
               WHEN 'forfait'     THEN p.titre
               ELSE NULL
           END AS service_titre
    FROM reservations r
    LEFT JOIN utilisateur u  ON r.user_id = u.id
    LEFT JOIN hebergement h  ON r.type_service = 'hebergement' AND r.service_id = h.id
    LEFT JOIN repas rp       ON r.type_service = 'repas'       AND r.service_id = rp.id
    LEFT JOIN guide g        ON r.type_service = 'guide'       AND r.service_id = g.id
    LEFT JOIN evenement e    ON r.type_service = 'evenement'   AND r.service_id = e.id
    LEFT JOIN artisanat a    ON r.type_service = 'artisanat'   AND r.service_id = a.id
    LEFT JOIN packs p        ON r.type_service = 'forfait'     AND r.service_id = p.id
    ORDER BY r.created_at DESC
    LIMIT ? OFFSET ?
";
$listStmt = mysqli_prepare($conn, $listSql);
$reservations = [];
if ($listStmt !== false) {
    mysqli_stmt_bind_param($listStmt, 'ii', $perPage, $offset);
    mysqli_stmt_execute($listStmt);
    $result = mysqli_stmt_get_result($listStmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $reservations[] = $row;
    }
    mysqli_stmt_close($listStmt);
}

$pageTitle = 'Réservations';
$pageHeading = 'Gestion des Réservations';
$activePage = 'reservations';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>
<main class="admin-content">
  <div class="content-wrap">
    <div class="topbar">
      <h1><?php echo htmlspecialchars($pageHeading); ?></h1>
      <div class="admin-chip">Connecté: <?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
    </div>
    <div class="page-body">
      <?php if ($flashSuccess !== ''): ?>
        <div class="flash success"><?php echo htmlspecialchars($flashSuccess); ?></div>
      <?php endif; ?>
      <?php if ($flashError !== ''): ?>
        <div class="flash error"><?php echo htmlspecialchars($flashError); ?></div>
      <?php endif; ?>

      <!-- ── Mini stats ── -->
      <style>
        .mini-stat-row { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:28px; }
        @media(max-width:900px){ .mini-stat-row { grid-template-columns:repeat(2,1fr); } }
        .mini-stat-card { background:var(--white); border-radius:16px; border:1px solid var(--border); padding:20px 18px 16px; box-shadow:0 4px 14px rgba(0,0,0,0.04); transition:transform .2s,box-shadow .2s; }
        .mini-stat-card:hover { transform:translateY(-3px); box-shadow:0 8px 22px rgba(0,0,0,0.08); }
        .msi { width:34px; height:34px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:17px; margin-bottom:12px; }
        .msi.navy  { background:#eef0f6; color:var(--navy); }
        .msi.orange{ background:#fff4eb; color:var(--coral); }
        .msi.green { background:#eef6ee; color:#2e7d32; }
        .msi.red   { background:#fdecea; color:#c0392b; }
        .msl { font-size:10px; font-weight:800; letter-spacing:1.5px; text-transform:uppercase; color:#8892a4; margin-bottom:5px; }
        .msv { font-size:26px; font-weight:800; color:var(--navy); line-height:1; margin-bottom:10px; }
        .msd { border:none; border-top:1px solid var(--border); margin:0 0 8px; }
        .mss { font-size:11px; color:#8892a4; line-height:1.5; }
        .mss.pos { color:#2e7d32; } .mss.neg { color:#c0392b; }
      </style>
      <div class="mini-stat-row">
        <div class="mini-stat-card">
          <div class="msi navy"><i class="bi bi-clipboard-check"></i></div>
          <div class="msl">EN ATTENTE</div>
          <div class="msv"><?= $resEnAttente ?></div>
          <hr class="msd">
          <div class="mss"><?= $resEnAttente + $resConfirmees > 0 ? round($resEnAttente / ($resEnAttente + $resConfirmees) * 100) : 0 ?>% du volume actif</div>
        </div>
        <div class="mini-stat-card">
          <div class="msi orange"><i class="bi bi-check-circle"></i></div>
          <div class="msl">CONFIRMÉES</div>
          <div class="msv"><?= $resConfirmees ?></div>
          <hr class="msd">
          <div class="mss"><?php if ($resDerniereConf): $diff = round((time() - strtotime($resDerniereConf)) / 3600); echo 'Dernière il y a ' . $diff . 'h'; else: echo 'Aucune encore'; endif; ?></div>
        </div>
        <div class="mini-stat-card">
          <div class="msi red"><i class="bi bi-x-circle"></i></div>
          <div class="msl">ANNULÉES</div>
          <div class="msv"><?= $resAnnulees ?></div>
          <hr class="msd">
          <div class="mss neg"><?= $resTerminees ?> terminée(s)</div>
        </div>
        <div class="mini-stat-card">
          <div class="msi green"><i class="bi bi-cash-coin"></i></div>
          <div class="msl">CHIFFRE D'AFFAIRES</div>
          <div class="msv" style="font-size:18px;"><?= number_format($resCA, 2, '.', ' ') ?> TND</div>
          <hr class="msd">
          <div class="mss pos">Hors annulées</div>
        </div>
      </div>

      <div class="toolbar">
        <div></div>
        <div class="muted">Total: <?php echo (int) $totalReservations; ?> réservation(s)</div>
      </div>

      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Utilisateur</th>
              <th>Service</th>
              <th>Dates</th>
              <th>Total</th>
              <th>Statut</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($reservations)): ?>
              <tr>
                <td colspan="7">Aucune réservation trouvée.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($reservations as $r):
                  $typeLabels = [
                      'hebergement' => 'Hébergement',
                      'repas'       => 'Repas',
                      'guide'       => 'Guide',
                      'artisanat'   => 'Artisanat',
                      'evenement'   => 'Événement',
                  ];
                  $type  = $typeLabels[$r['type_service']] ?? ucfirst((string)$r['type_service']);
                  $titre = $r['service_titre'] ?? '(service supprimé)';

                  $dates_str = '';
                  if (!empty($r['date_debut'])) {
                      if ($r['type_service'] === 'hebergement' && !empty($r['date_fin'])) {
                          $dates_str = date('d/m/Y', strtotime($r['date_debut'])) . '<br>au ' . date('d/m/Y', strtotime($r['date_fin']));
                      } else {
                          $dates_str = date('d/m/Y', strtotime($r['date_debut']));
                      }
                  }

                  $total    = (float)($r['prix_total'] ?? 0);
                  $userName = trim(($r['user_prenom'] ?? '') . ' ' . ($r['user_nom'] ?? ''));
                  if ($userName === '') $userName = 'Utilisateur #' . (int)$r['user_id'];
              ?>
                <tr>
                  <td><?php echo (int) $r['id']; ?></td>
                  <td><?php echo htmlspecialchars($userName); ?></td>
                  <td>
                      <strong><?php echo htmlspecialchars($titre); ?></strong><br>
                      <small style="color:var(--grey);"><?php echo htmlspecialchars($type); ?></small>
                  </td>
                  <td><?php echo $dates_str; ?></td>
                  <td><strong><?php echo number_format($total, 2, '.', ' '); ?> TND</strong></td>
                  <td>
                      <?php
                          $sc = $r['statut'];
                          $pill_colors = [
                              'en_attente' => 'background:#fff3e0;color:#e65100;',
                              'confirmé'   => 'background:#e8f5e9;color:#2e7d32;',
                              'confirmée'  => 'background:#e8f5e9;color:#2e7d32;',
                              'terminé'    => 'background:#eeeeee;color:#555;',
                              'annulée'    => 'background:#fce4ec;color:#c62828;',
                          ];
                          $pill_style = $pill_colors[$sc] ?? 'background:var(--cream);color:var(--charcoal);';
                      ?>
                      <span class="role-pill" style="<?php echo $pill_style; ?>">
                          <?php echo htmlspecialchars(ucfirst($sc)); ?>
                      </span>
                  </td>
                  <td>
                      <form method="post" action="reservations.php" style="display:flex; gap:8px; align-items:center;">
                          <input type="hidden" name="action" value="change_status">
                          <input type="hidden" name="reservation_id" value="<?php echo (int) $r['id']; ?>">
                          <select name="statut" class="search-input" style="padding:6px; width:130px; font-size:13px;" onchange="this.form.submit()">
                              <option value="en_attente" <?php if ($sc === 'en_attente') echo 'selected'; ?>>En attente</option>
                              <option value="confirmé"   <?php if (in_array($sc, ['confirmé','confirmée','confirmee'])) echo 'selected'; ?>>Confirmé</option>
                              <option value="terminé"    <?php if (in_array($sc, ['terminé','terminée','termine']))   echo 'selected'; ?>>Terminé</option>
                              <option value="annulée"    <?php if (in_array($sc, ['annulée','annulee']))              echo 'selected'; ?>>Annulée</option>
                          </select>
                      </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <?php if ($totalPages > 1): ?>
        <div class="pagination">
          <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <a class="btn-small <?php echo $p === $page ? 'btn-coral' : 'btn-soft'; ?>" href="reservations.php?page=<?php echo (int) $p; ?>">
              <?php echo (int) $p; ?>
            </a>
          <?php endfor; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

