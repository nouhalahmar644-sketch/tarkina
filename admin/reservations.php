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

    if ($targetId <= 0 || !in_array($newStatus, ['en_attente', 'confirmée', 'annulée'])) {
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

$countSql = 'SELECT COUNT(*) FROM reservations';
$countStmt = mysqli_prepare($conn, $countSql);
$totalReservations = 0;
if ($countStmt !== false) {
    mysqli_stmt_execute($countStmt);
    mysqli_stmt_bind_result($countStmt, $totalResDb);
    if (mysqli_stmt_fetch($countStmt)) {
        $totalReservations = (int) $totalResDb;
    }
    mysqli_stmt_close($countStmt);
}

$totalPages = (int) ceil($totalReservations / $perPage);
if ($totalPages > 0 && $page > $totalPages) {
    $page = $totalPages;
    $offset = ($page - 1) * $perPage;
}

$listSql = "
    SELECT r.*,
           u.nom AS user_nom, u.prenom AS user_prenom,
           h.titre AS hebergement_titre, h.prix AS hebergement_prix,
           rp.titre AS repas_titre, rp.prix AS repas_prix,
           g.titre AS guide_titre, g.prix AS guide_prix,
           e.titre AS evenement_titre, e.prix AS evenement_prix
    FROM reservations r
    LEFT JOIN utilisateur u ON r.utilisateur_id = u.id
    LEFT JOIN hebergement h ON r.logement_id = h.id
    LEFT JOIN repas rp ON r.repas_id = rp.id
    LEFT JOIN guide g ON r.guide_id = g.id
    LEFT JOIN evenement e ON r.evenement_id = e.id
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
                  $type = '';
                  $titre = '';
                  $prix = 0;
                  
                  if (!empty($r['logement_id'])) {
                      $type = 'Hébergement';
                      $titre = $r['hebergement_titre'];
                      $prix = (float)$r['hebergement_prix'];
                  } elseif (!empty($r['repas_id'])) {
                      $type = 'Repas';
                      $titre = $r['repas_titre'];
                      $prix = (float)$r['repas_prix'];
                  } elseif (!empty($r['guide_id'])) {
                      $type = 'Guide';
                      $titre = $r['guide_titre'];
                      $prix = (float)$r['guide_prix'];
                  } elseif (!empty($r['evenement_id'])) {
                      $type = 'Événement';
                      $titre = $r['evenement_titre'];
                      $prix = (float)$r['evenement_prix'];
                  }
                  
                  $total = $prix;
                  $dates_str = '';
                  
                  if ($type === 'Hébergement') {
                      $dates_str = date('d/m/Y', strtotime($r['date_debut'])) . '<br>au ' . date('d/m/Y', strtotime($r['date_fin']));
                      $d1 = new DateTime($r['date_debut']);
                      $d2 = new DateTime($r['date_fin']);
                      $nuits = max(1, $d1->diff($d2)->days);
                      $total = $prix * $nuits;
                  } else {
                      $dates_str = date('d/m/Y', strtotime($r['date_debut']));
                  }
                  
                  $userName = trim($r['user_prenom'] . ' ' . $r['user_nom']);
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
                      <form method="post" action="reservations.php" style="display:flex; gap:8px; align-items:center;">
                          <input type="hidden" name="action" value="change_status">
                          <input type="hidden" name="reservation_id" value="<?php echo (int) $r['id']; ?>">
                          <select name="statut" class="search-input" style="padding: 6px; width: 120px; font-size: 13px;" onchange="this.form.submit()">
                              <option value="en_attente" <?php if ($r['statut'] === 'en_attente') echo 'selected'; ?>>En attente</option>
                              <option value="confirmée" <?php if ($r['statut'] === 'confirmée' || $r['statut'] === 'confirmee') echo 'selected'; ?>>Confirmée</option>
                              <option value="annulée" <?php if ($r['statut'] === 'annulée' || $r['statut'] === 'annulee') echo 'selected'; ?>>Annulée</option>
                          </select>
                      </form>
                  </td>
                  <td>
                      <span class="role-pill" style="background:var(--cream); color:var(--charcoal);">
                          <?php echo htmlspecialchars(ucfirst($r['statut'])); ?>
                      </span>
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
