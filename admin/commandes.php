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
    $targetId = isset($_POST['commande_id']) ? (int) $_POST['commande_id'] : 0;
    $newStatus = isset($_POST['statut']) ? trim((string) $_POST['statut']) : '';
    $redirectUrl = 'commandes.php';

    $allowedStatus = ['en_attente', 'confirmé', 'terminé', 'annulée'];
    if ($targetId <= 0 || !in_array($newStatus, $allowedStatus)) {
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
            $_SESSION['admin_flash_success'] = 'Statut de la commande mis à jour avec succès.';
        } else {
            $_SESSION['admin_flash_error'] = 'Commande introuvable.';
        }
    }

    header('Location: ' . $redirectUrl);
    exit;
}

// Pagination
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$page = max(1, $page);
$perPage = 15;
$offset = ($page - 1) * $perPage;

$countSql = "SELECT COUNT(*) FROM reservations WHERE type_service IN ('artisanat', 'repas')";
$countRes = mysqli_query($conn, $countSql);
$totalCommandes = 0;
if ($countRes) {
    $row = mysqli_fetch_row($countRes);
    $totalCommandes = (int)$row[0];
}

$totalPages = (int) ceil($totalCommandes / $perPage);
if ($totalPages > 0 && $page > $totalPages) {
    $page = $totalPages;
    $offset = ($page - 1) * $perPage;
}

// Fetch list with joins
$listSql = "
    SELECT r.*,
           u.nom AS user_nom, u.prenom AS user_prenom,
           CASE r.type_service
               WHEN 'artisanat' THEN a.titre
               WHEN 'repas'     THEN rp.titre
               ELSE NULL
           END AS produit_nom
    FROM reservations r
    LEFT JOIN utilisateur u ON r.user_id = u.id
    LEFT JOIN artisanat a  ON r.type_service = 'artisanat' AND r.service_id = a.id
    LEFT JOIN repas rp     ON r.type_service = 'repas'     AND r.service_id = rp.id
    WHERE r.type_service IN ('artisanat', 'repas')
    ORDER BY r.created_at DESC
    LIMIT ? OFFSET ?
";
$listStmt = mysqli_prepare($conn, $listSql);
$commandes = [];
if ($listStmt !== false) {
    mysqli_stmt_bind_param($listStmt, 'ii', $perPage, $offset);
    mysqli_stmt_execute($listStmt);
    $result = mysqli_stmt_get_result($listStmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $commandes[] = $row;
    }
    mysqli_stmt_close($listStmt);
}

$pageTitle = 'Gestion des Commandes';
$pageHeading = 'Commandes Artisanat & Repas';
$activePage = 'commandes';

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
        <div class="muted">Total: <?php echo (int) $totalCommandes; ?> commande(s)</div>
      </div>

      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Utilisateur</th>
              <th>Produit</th>
              <th>Quantité</th>
              <th>Adresse de livraison</th>
              <th>Total</th>
              <th>Date</th>
              <th>Statut</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($commandes)): ?>
              <tr>
                <td colspan="8">Aucune commande trouvée.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($commandes as $c):
                  $userName = trim(($c['user_prenom'] ?? '') . ' ' . ($c['user_nom'] ?? ''));
                  if ($userName === '') $userName = 'Utilisateur #' . (int)$c['user_id'];
                  $typeLabel = $c['type_service'] === 'artisanat' ? 'Artisanat' : 'Repas';
                  $sc = $c['statut'];
                  $pill_colors = [
                      'en_attente' => 'background:#fff3e0;color:#e65100;',
                      'confirmé'   => 'background:#e8f5e9;color:#2e7d32;',
                      'terminé'    => 'background:#eeeeee;color:#555;',
                      'annulée'    => 'background:#fce4ec;color:#c62828;',
                  ];
                  $pill_style = $pill_colors[$sc] ?? 'background:var(--cream);color:var(--charcoal);';
              ?>
                <tr>
                  <td><?php echo (int) $c['id']; ?></td>
                  <td><?php echo htmlspecialchars($userName); ?></td>
                  <td>
                      <strong><?php echo htmlspecialchars($c['produit_nom'] ?? 'Produit supprimé'); ?></strong><br>
                      <small style="color:#888;"><?php echo htmlspecialchars($typeLabel); ?></small>
                  </td>
                  <td><?php echo (int)($c['nb_voyageurs'] ?? 0); ?></td>
                  <td><small><?php echo nl2br(htmlspecialchars($c['message'] ?? '')); ?></small></td>
                  <td><strong><?php echo number_format((float)($c['prix_total'] ?? 0), 2, '.', ' '); ?> TND</strong></td>
                  <td><?php echo !empty($c['created_at']) ? date('d/m/Y H:i', strtotime($c['created_at'])) : '-'; ?></td>
                  <td>
                      <span class="role-pill" style="<?php echo $pill_style; ?>">
                          <?php echo htmlspecialchars(ucfirst($sc)); ?>
                      </span><br>
                      <form method="post" action="commandes.php" style="margin-top:6px;">
                          <input type="hidden" name="action" value="change_status">
                          <input type="hidden" name="commande_id" value="<?php echo (int) $c['id']; ?>">
                          <select name="statut" class="search-input" style="padding:5px; width:120px; font-size:12px;" onchange="this.form.submit()">
                              <option value="en_attente" <?php if ($sc === 'en_attente') echo 'selected'; ?>>En attente</option>
                              <option value="confirmé"   <?php if (in_array($sc, ['confirmé','confirmée'])) echo 'selected'; ?>>Confirmé</option>
                              <option value="terminé"    <?php if (in_array($sc, ['terminé','terminée'])) echo 'selected'; ?>>Terminé</option>
                              <option value="annulée"    <?php if (in_array($sc, ['annulée','annulee'])) echo 'selected'; ?>>Annulée</option>
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
            <a class="btn-small <?php echo $p === $page ? 'btn-coral' : 'btn-soft'; ?>" href="commandes.php?page=<?php echo (int) $p; ?>">
              <?php echo (int) $p; ?>
            </a>
          <?php endfor; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

