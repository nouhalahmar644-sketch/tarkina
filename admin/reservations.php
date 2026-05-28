<?php
require_once __DIR__ . '/includes/auth_admin.php';

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

$countSql = "SELECT COUNT(*) FROM reservations WHERE type_service IN ('hebergement', 'guide', 'evenement')";
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

$listSql = "
    SELECT r.*,
           u.nom AS user_nom, u.prenom AS user_prenom,
           CASE r.type_service
               WHEN 'hebergement' THEN h.titre
               WHEN 'guide'       THEN g.titre
               WHEN 'evenement'   THEN e.titre
               ELSE NULL
           END AS service_titre
    FROM reservations r
    LEFT JOIN utilisateur u  ON r.user_id = u.id
    LEFT JOIN hebergement h  ON r.type_service = 'hebergement' AND r.service_id = h.id
    LEFT JOIN guide g        ON r.type_service = 'guide'       AND r.service_id = g.id
    LEFT JOIN evenement e    ON r.type_service = 'evenement'   AND r.service_id = e.id
    WHERE r.type_service IN ('hebergement', 'guide', 'evenement')
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

