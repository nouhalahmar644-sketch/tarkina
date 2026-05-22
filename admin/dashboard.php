<?php
require_once __DIR__ . '/includes/auth_admin.php';

function table_exists($conn, $tableName)
{
    $safeName = mysqli_real_escape_string($conn, $tableName);
    $sql = "SHOW TABLES LIKE '" . $safeName . "'";
    $result = mysqli_query($conn, $sql);
    if ($result === false) {
        return false;
    }
    $exists = mysqli_num_rows($result) > 0;
    mysqli_free_result($result);
    return $exists;
}

function table_count($conn, $tableName)
{
    $safeName = mysqli_real_escape_string($conn, $tableName);
    $sql = "SELECT COUNT(*) AS total FROM `" . $safeName . "`";
    $result = mysqli_query($conn, $sql);
    if ($result === false) {
        return 0;
    }
    $row = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    return isset($row['total']) ? (int) $row['total'] : 0;
}

$totalUsers = table_count($conn, 'utilisateur');

$contentCount = null;
$contentLabel = 'Total hébergements / publications';

if (table_exists($conn, 'hebergement')) {
    $contentCount = table_count($conn, 'hebergement');
    $contentLabel = 'Total hébergements';
} elseif (table_exists($conn, 'posts')) {
    $contentCount = table_count($conn, 'posts');
    $contentLabel = 'Total publications';
}

$pageTitle = 'Tableau de bord';
$pageHeading = 'Tableau de bord';
$activePage = 'dashboard';

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
      <div class="card-grid">
        <article class="stat-card">
          <div class="stat-label">Total utilisateurs</div>
          <div class="stat-value"><?php echo (int) $totalUsers; ?></div>
          <p class="muted">Comptes inscrits sur la plateforme.</p>
        </article>

        <article class="stat-card">
          <div class="stat-label"><?php echo htmlspecialchars($contentLabel); ?></div>
          <div class="stat-value">
            <?php echo $contentCount === null ? '--' : (int) $contentCount; ?>
          </div>
          <p class="muted">
            <?php if ($contentCount === null): ?>
              Aucune table de contenus trouvée pour le moment (dummy).
            <?php else: ?>
              Donnée récupérée depuis la base.
            <?php endif; ?>
          </p>
        </article>
      </div>
    </div>
  </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
