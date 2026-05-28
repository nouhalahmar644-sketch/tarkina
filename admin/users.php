<?php
require_once __DIR__ . '/includes/auth_admin.php';

$adminUserId = (int) $_SESSION['user_id'];
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? (string) $_POST['action'] : '';
    $targetId = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;
    $searchReturn = isset($_POST['q']) ? trim((string) $_POST['q']) : '';
    $pageReturn = isset($_POST['page']) ? (int) $_POST['page'] : 1;
    $redirectUrl = 'users.php?q=' . urlencode($searchReturn) . '&page=' . max(1, $pageReturn);

    if ($targetId <= 0) {
        $_SESSION['admin_flash_error'] = 'Utilisateur invalide.';
        header('Location: ' . $redirectUrl);
        exit;
    }

    if ($action === 'delete') {
        if ($targetId === $adminUserId) {
            $_SESSION['admin_flash_error'] = 'Vous ne pouvez pas supprimer votre propre compte admin.';
            header('Location: ' . $redirectUrl);
            exit;
        }

        $deleteSql = 'DELETE FROM utilisateur WHERE id = ? LIMIT 1';
        $deleteStmt = mysqli_prepare($conn, $deleteSql);
        if ($deleteStmt === false) {
            $_SESSION['admin_flash_error'] = 'Suppression impossible pour le moment.';
        } else {
            mysqli_stmt_bind_param($deleteStmt, 'i', $targetId);
            mysqli_stmt_execute($deleteStmt);
            $affected = mysqli_stmt_affected_rows($deleteStmt);
            mysqli_stmt_close($deleteStmt);
            if ($affected > 0) {
                $_SESSION['admin_flash_success'] = 'Utilisateur supprimé avec succès.';
            } else {
                $_SESSION['admin_flash_error'] = 'Utilisateur introuvable.';
            }
        }

        header('Location: ' . $redirectUrl);
        exit;
    }

    if ($action === 'change_role') {
        $newRole = isset($_POST['role']) ? trim((string) $_POST['role']) : '';
        if ($newRole !== 'admin' && $newRole !== 'utilisateur') {
            $_SESSION['admin_flash_error'] = 'Rôle invalide.';
            header('Location: ' . $redirectUrl);
            exit;
        }

        if ($targetId === $adminUserId && $newRole !== 'admin') {
            $_SESSION['admin_flash_error'] = 'Vous ne pouvez pas retirer votre propre rôle admin.';
            header('Location: ' . $redirectUrl);
            exit;
        }

        $updateSql = 'UPDATE utilisateur SET role = ? WHERE id = ? LIMIT 1';
        $updateStmt = mysqli_prepare($conn, $updateSql);
        if ($updateStmt === false) {
            $_SESSION['admin_flash_error'] = 'Changement de rôle impossible pour le moment.';
        } else {
            mysqli_stmt_bind_param($updateStmt, 'si', $newRole, $targetId);
            mysqli_stmt_execute($updateStmt);
            $affected = mysqli_stmt_affected_rows($updateStmt);
            mysqli_stmt_close($updateStmt);
            if ($affected >= 0) {
                $_SESSION['admin_flash_success'] = 'Rôle mis à jour.';
            } else {
                $_SESSION['admin_flash_error'] = 'Utilisateur introuvable.';
            }
        }

        header('Location: ' . $redirectUrl);
        exit;
    }
}

$search = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$page = max(1, $page);
$perPage = 10;
$offset = ($page - 1) * $perPage;

$whereSql = '';
$searchTerm = '';
if ($search !== '') {
    $whereSql = ' WHERE nom LIKE ? OR prenom LIKE ? OR email LIKE ? OR role LIKE ?';
    $searchTerm = '%' . $search . '%';
}

$countSql = 'SELECT COUNT(*) FROM utilisateur' . $whereSql;
$countStmt = mysqli_prepare($conn, $countSql);
$totalUsers = 0;
if ($countStmt !== false) {
    if ($search !== '') {
        mysqli_stmt_bind_param($countStmt, 'ssss', $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    }
    mysqli_stmt_execute($countStmt);
    mysqli_stmt_bind_result($countStmt, $totalUsersDb);
    if (mysqli_stmt_fetch($countStmt)) {
        $totalUsers = (int) $totalUsersDb;
    }
    mysqli_stmt_close($countStmt);
}

$totalPages = (int) ceil($totalUsers / $perPage);
if ($totalPages > 0 && $page > $totalPages) {
    $page = $totalPages;
    $offset = ($page - 1) * $perPage;
}

$listSql = 'SELECT id, nom, prenom, email, adresse, role FROM utilisateur' . $whereSql . ' ORDER BY id DESC LIMIT ? OFFSET ?';
$listStmt = mysqli_prepare($conn, $listSql);
$users = [];
if ($listStmt !== false) {
    if ($search !== '') {
        mysqli_stmt_bind_param($listStmt, 'ssssii', $searchTerm, $searchTerm, $searchTerm, $searchTerm, $perPage, $offset);
    } else {
        mysqli_stmt_bind_param($listStmt, 'ii', $perPage, $offset);
    }
    mysqli_stmt_execute($listStmt);
    mysqli_stmt_bind_result($listStmt, $idDb, $nomDb, $prenomDb, $emailDb, $adresseDb, $roleDb);
    while (mysqli_stmt_fetch($listStmt)) {
        $users[] = [
            'id' => (int) $idDb,
            'nom' => $nomDb,
            'prenom' => $prenomDb,
            'email' => $emailDb,
            'adresse' => $adresseDb,
            'role' => $roleDb
        ];
    }
    mysqli_stmt_close($listStmt);
}

$pageTitle = 'Utilisateurs';
$pageHeading = 'Utilisateurs';
$activePage = 'users';

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
        <form method="get" action="users.php" class="search-form">
          <input type="text" name="q" class="search-input" placeholder="Rechercher nom, prénom, e-mail, rôle..." value="<?php echo htmlspecialchars($search); ?>">
          <button type="submit" class="btn-small btn-navy">Rechercher</button>
          <?php if ($search !== ''): ?>
            <a href="users.php" class="btn-small btn-soft">Réinitialiser</a>
          <?php endif; ?>
        </form>
        <div class="muted">Total: <?php echo (int) $totalUsers; ?> utilisateur(s)</div>
      </div>

      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nom complet</th>
              <th>Email</th>
              <th>Adresse</th>
              <th>Rôle</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($users)): ?>
              <tr>
                <td colspan="6">Aucun utilisateur trouvé.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($users as $user): ?>
                <tr>
                  <td><?php echo (int) $user['id']; ?></td>
                  <td><?php echo htmlspecialchars(trim($user['prenom'] . ' ' . $user['nom'])); ?></td>
                  <td><?php echo htmlspecialchars($user['email']); ?></td>
                  <td><?php echo htmlspecialchars($user['adresse']); ?></td>
                  <td>
                    <span class="role-pill <?php echo $user['role'] === 'admin' ? 'admin' : ''; ?>">
                      <?php echo htmlspecialchars($user['role']); ?>
                    </span>
                  </td>
                  <td>
                    <div class="actions">
                      <a class="btn-small btn-soft" href="user-details.php?id=<?php echo (int) $user['id']; ?>">Voir</a>

                      <form method="post" action="users.php" class="inline-form">
                        <input type="hidden" name="action" value="change_role">
                        <input type="hidden" name="user_id" value="<?php echo (int) $user['id']; ?>">
                        <input type="hidden" name="role" value="<?php echo $user['role'] === 'admin' ? 'utilisateur' : 'admin'; ?>">
                        <input type="hidden" name="q" value="<?php echo htmlspecialchars($search); ?>">
                        <input type="hidden" name="page" value="<?php echo (int) $page; ?>">
                        <button type="submit" class="btn-small btn-coral">
                          <?php echo $user['role'] === 'admin' ? 'Définir utilisateur' : 'Définir admin'; ?>
                        </button>
                      </form>

                      <form method="post" action="users.php" class="inline-form" onsubmit="return confirm('Supprimer cet utilisateur ? Cette action est irréversible.');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="user_id" value="<?php echo (int) $user['id']; ?>">
                        <input type="hidden" name="q" value="<?php echo htmlspecialchars($search); ?>">
                        <input type="hidden" name="page" value="<?php echo (int) $page; ?>">
                        <button type="submit" class="btn-small btn-soft" title="Supprimer"><i class="bi bi-trash" style="color:#c0392b"></i></button>
                      </form>
                    </div>
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
            <a class="btn-small <?php echo $p === $page ? 'btn-coral' : 'btn-soft'; ?>" href="users.php?page=<?php echo (int) $p; ?>&q=<?php echo urlencode($search); ?>">
              <?php echo (int) $p; ?>
            </a>
          <?php endfor; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

