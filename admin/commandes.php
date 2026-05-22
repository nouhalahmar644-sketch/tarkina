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

    $allowedStatus = ['en_attente', 'expédiée', 'livrée', 'annulée'];
    if ($targetId <= 0 || !in_array($newStatus, $allowedStatus)) {
        $_SESSION['admin_flash_error'] = 'Action invalide.';
        header('Location: ' . $redirectUrl);
        exit;
    }

    $updateSql = 'UPDATE commandes SET statut = ? WHERE id = ? LIMIT 1';
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

$countSql = 'SELECT COUNT(*) FROM commandes';
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
    SELECT c.*,
           u.nom AS user_nom, u.prenom AS user_prenom,
           a.titre AS produit_nom
    FROM commandes c
    LEFT JOIN utilisateur u ON c.utilisateur_id = u.id
    LEFT JOIN artisanat a ON c.artisanat_id = a.id
    ORDER BY c.created_at DESC
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
$pageHeading = 'Gestion des Commandes Artisanat';
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
                  $userName = trim(($c['user_prenom'] ?? '') . ' ' . ($c['user_nom'] ?? 'Utilisateur inconnu'));
              ?>
                <tr>
                  <td><?php echo (int) $c['id']; ?></td>
                  <td><?php echo htmlspecialchars($userName); ?></td>
                  <td><?php echo htmlspecialchars($c['produit_nom'] ?? 'Produit supprimé'); ?></td>
                  <td><?php echo (int) $c['quantite']; ?></td>
                  <td><small><?php echo nl2br(htmlspecialchars($c['adresse_livraison'])); ?></small></td>
                  <td><strong><?php echo number_format($c['total'], 2, '.', ' '); ?> TND</strong></td>
                  <td><?php echo date('d/m/Y H:i', strtotime($c['created_at'])); ?></td>
                  <td>
                      <form method="post" action="commandes.php" style="display:flex; gap:8px; align-items:center;">
                          <input type="hidden" name="action" value="change_status">
                          <input type="hidden" name="commande_id" value="<?php echo (int) $c['id']; ?>">
                          <select name="statut" class="search-input" style="padding: 6px; width: 130px; font-size: 13px;" onchange="this.form.submit()">
                              <option value="en_attente" <?php if ($c['statut'] === 'en_attente') echo 'selected'; ?>>En attente</option>
                              <option value="expédiée" <?php if ($c['statut'] === 'expédiée') echo 'selected'; ?>>Expédiée</option>
                              <option value="livrée" <?php if ($c['statut'] === 'livrée') echo 'selected'; ?>>Livrée</option>
                              <option value="annulée" <?php if ($c['statut'] === 'annulée') echo 'selected'; ?>>Annulée</option>
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
