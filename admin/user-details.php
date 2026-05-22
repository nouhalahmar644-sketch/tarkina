<?php
require_once __DIR__ . '/includes/auth_admin.php';

$userId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$user = null;

if ($userId > 0) {
    $sql = 'SELECT id, nom, prenom, email, adresse, role FROM utilisateur WHERE id = ? LIMIT 1';
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt !== false) {
        mysqli_stmt_bind_param($stmt, 'i', $userId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $idDb, $nomDb, $prenomDb, $emailDb, $adresseDb, $roleDb);
        if (mysqli_stmt_fetch($stmt)) {
            $user = [
                'id' => (int) $idDb,
                'nom' => $nomDb,
                'prenom' => $prenomDb,
                'email' => $emailDb,
                'adresse' => $adresseDb,
                'role' => $roleDb
            ];
        }
        mysqli_stmt_close($stmt);
    }
}

$pageTitle = 'Détails utilisateur';
$pageHeading = 'Détails utilisateur';
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
      <a href="users.php" class="btn-small btn-soft" style="margin-bottom:12px;">&larr; Retour</a>

      <?php if ($user === null): ?>
        <div class="flash error">Utilisateur introuvable.</div>
      <?php else: ?>
        <div class="stat-card">
          <div class="stat-label">Identifiant</div>
          <div class="muted"><?php echo (int) $user['id']; ?></div>

          <div class="stat-label" style="margin-top:14px;">Nom</div>
          <div class="muted"><?php echo htmlspecialchars($user['nom']); ?></div>

          <div class="stat-label" style="margin-top:14px;">Prénom</div>
          <div class="muted"><?php echo htmlspecialchars($user['prenom']); ?></div>

          <div class="stat-label" style="margin-top:14px;">Email</div>
          <div class="muted"><?php echo htmlspecialchars($user['email']); ?></div>

          <div class="stat-label" style="margin-top:14px;">Adresse</div>
          <div class="muted"><?php echo htmlspecialchars($user['adresse']); ?></div>

          <div class="stat-label" style="margin-top:14px;">Rôle</div>
          <div class="muted">
            <span class="role-pill <?php echo $user['role'] === 'admin' ? 'admin' : ''; ?>">
              <?php echo htmlspecialchars($user['role']); ?>
            </span>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
