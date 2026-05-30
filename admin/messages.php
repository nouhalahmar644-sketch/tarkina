<?php
require_once __DIR__ . '/includes/auth_admin.php';

// Ensure table exists
$createTableQuery = "CREATE TABLE IF NOT EXISTS `messages` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nom` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `sujet` varchar(255) NOT NULL,
    `message` text NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
mysqli_query($conn, $createTableQuery);

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
    $targetId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    
    if ($action === 'delete' && $targetId > 0) {
        $deleteSql = 'DELETE FROM messages WHERE id = ? LIMIT 1';
        $deleteStmt = mysqli_prepare($conn, $deleteSql);
        if ($deleteStmt) {
            mysqli_stmt_bind_param($deleteStmt, 'i', $targetId);
            mysqli_stmt_execute($deleteStmt);
            mysqli_stmt_close($deleteStmt);
            $_SESSION['admin_flash_success'] = 'Message supprimé avec succès.';
        } else {
            $_SESSION['admin_flash_error'] = 'Erreur lors de la suppression.';
        }
        header('Location: messages.php');
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
    $whereSql = ' WHERE nom LIKE ? OR email LIKE ? OR sujet LIKE ? OR message LIKE ?';
    $searchTerm = '%' . $search . '%';
}

$countSql = 'SELECT COUNT(*) FROM messages' . $whereSql;
$countStmt = mysqli_prepare($conn, $countSql);
$totalMessages = 0;
if ($countStmt !== false) {
    if ($search !== '') {
        mysqli_stmt_bind_param($countStmt, 'ssss', $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    }
    mysqli_stmt_execute($countStmt);
    mysqli_stmt_bind_result($countStmt, $totalMessagesDb);
    if (mysqli_stmt_fetch($countStmt)) {
        $totalMessages = (int) $totalMessagesDb;
    }
    mysqli_stmt_close($countStmt);
}

$totalPages = (int) ceil($totalMessages / $perPage);
if ($totalPages > 0 && $page > $totalPages) {
    $page = $totalPages;
    $offset = ($page - 1) * $perPage;
}

$listSql = 'SELECT id, nom, email, sujet, message, created_at FROM messages' . $whereSql . ' ORDER BY created_at DESC LIMIT ? OFFSET ?';
$listStmt = mysqli_prepare($conn, $listSql);
$messages = [];
if ($listStmt !== false) {
    if ($search !== '') {
        mysqli_stmt_bind_param($listStmt, 'ssssii', $searchTerm, $searchTerm, $searchTerm, $searchTerm, $perPage, $offset);
    } else {
        mysqli_stmt_bind_param($listStmt, 'ii', $perPage, $offset);
    }
    mysqli_stmt_execute($listStmt);
    mysqli_stmt_bind_result($listStmt, $idDb, $nomDb, $emailDb, $sujetDb, $messageDb, $createdAtDb);
    while (mysqli_stmt_fetch($listStmt)) {
        $messages[] = [
            'id' => (int) $idDb,
            'nom' => $nomDb,
            'email' => $emailDb,
            'sujet' => $sujetDb,
            'message' => $messageDb,
            'created_at' => $createdAtDb
        ];
    }
    mysqli_stmt_close($listStmt);
}

$pageTitle = 'Messages Reçus';
$pageHeading = 'Messages Reçus';
$activePage = 'messages';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>
<style>
  :root {
    --navy: #0b1c30 !important;
    --coral: #f16e22 !important;
  }
</style>
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
        <form method="get" action="messages.php" class="search-form">
          <input type="text" name="q" class="search-input" placeholder="Rechercher nom, email, sujet, message..." value="<?php echo htmlspecialchars($search); ?>">
          <button type="submit" class="btn-small btn-navy">Rechercher</button>
          <?php if ($search !== ''): ?>
            <a href="messages.php" class="btn-small btn-soft">Réinitialiser</a>
          <?php endif; ?>
        </form>
        <div class="muted">Total: <?php echo (int) $totalMessages; ?> message(s)</div>
      </div>

      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>Nom</th>
              <th>Email</th>
              <th>Sujet</th>
              <th>Message</th>
              <th>Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($messages)): ?>
              <tr>
                <td colspan="6" style="text-align: center; color: #8A8A8A; padding: 20px;">Aucun message reçu.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($messages as $msg): ?>
                <tr>
                  <td><strong><?php echo htmlspecialchars($msg['nom']); ?></strong></td>
                  <td><a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>" style="color: var(--coral); text-decoration: none; font-weight: 600;"><?php echo htmlspecialchars($msg['email']); ?></a></td>
                  <td><?php echo htmlspecialchars($msg['sujet']); ?></td>
                  <td style="max-width: 350px; white-space: pre-wrap;"><?php echo htmlspecialchars($msg['message']); ?></td>
                  <td><?php echo date('d/m/Y H:i', strtotime($msg['created_at'])); ?></td>
                  <td>
                    <div class="actions">
                      <form method="post" action="messages.php" class="inline-form" onsubmit="return confirm('Supprimer ce message définitivement ?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo (int) $msg['id']; ?>">
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
            <a class="btn-small <?php echo $p === $page ? 'btn-coral' : 'btn-soft'; ?>" href="messages.php?page=<?php echo (int) $p; ?>&q=<?php echo urlencode($search); ?>">
              <?php echo (int) $p; ?>
            </a>
          <?php endfor; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

