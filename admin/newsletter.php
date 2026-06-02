<?php
require_once __DIR__ . '/includes/auth_admin.php';

// Auto-create the same table the public newsletter form uses, so this page
// always opens even on fresh installs that haven't loaded the home page yet.
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS newsletter (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(190) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$flashSuccess = $_SESSION['nl_flash_success'] ?? ''; unset($_SESSION['nl_flash_success']);
$flashError   = $_SESSION['nl_flash_error']   ?? ''; unset($_SESSION['nl_flash_error']);

// Delete handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id > 0) {
        $st = mysqli_prepare($conn, "DELETE FROM newsletter WHERE id = ? LIMIT 1");
        mysqli_stmt_bind_param($st, 'i', $id);
        mysqli_stmt_execute($st);
        $_SESSION['nl_flash_success'] = mysqli_stmt_affected_rows($st) > 0
            ? 'Inscription supprimée.'
            : 'Inscription introuvable.';
        mysqli_stmt_close($st);
    }
    header('Location: newsletter.php'); exit;
}

$search  = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
$page    = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 20;
$offset  = ($page - 1) * $perPage;

$where = ''; $like = '';
if ($search !== '') { $where = ' WHERE email LIKE ?'; $like = '%' . $search . '%'; }

$total = 0;
$cs = mysqli_prepare($conn, 'SELECT COUNT(*) FROM newsletter' . $where);
if ($cs) {
    if ($search !== '') mysqli_stmt_bind_param($cs, 's', $like);
    mysqli_stmt_execute($cs);
    mysqli_stmt_bind_result($cs, $tdb);
    if (mysqli_stmt_fetch($cs)) $total = (int) $tdb;
    mysqli_stmt_close($cs);
}

$totalPages = (int) ceil($total / $perPage);
if ($totalPages > 0 && $page > $totalPages) { $page = $totalPages; $offset = ($page - 1) * $perPage; }

$subs = [];
$ls = mysqli_prepare($conn, 'SELECT id, email, created_at FROM newsletter' . $where . ' ORDER BY created_at DESC LIMIT ? OFFSET ?');
if ($ls) {
    if ($search !== '') mysqli_stmt_bind_param($ls, 'sii', $like, $perPage, $offset);
    else                mysqli_stmt_bind_param($ls, 'ii', $perPage, $offset);
    mysqli_stmt_execute($ls);
    mysqli_stmt_bind_result($ls, $idDb, $emailDb, $createdAtDb);
    while (mysqli_stmt_fetch($ls)) {
        $subs[] = ['id' => (int) $idDb, 'email' => $emailDb, 'created_at' => $createdAtDb];
    }
    mysqli_stmt_close($ls);
}

// Global stats (ignore filter)
$totAll = 0; $tot7 = 0; $tot30 = 0; $last = null;
$sres = mysqli_query($conn, "SELECT COUNT(*) AS total,
    SUM(created_at >= (NOW() - INTERVAL 7 DAY))  AS d7,
    SUM(created_at >= (NOW() - INTERVAL 30 DAY)) AS d30,
    MAX(created_at) AS last
    FROM newsletter");
if ($sres && $sr = mysqli_fetch_assoc($sres)) {
    $totAll = (int) $sr['total']; $tot7 = (int) $sr['d7']; $tot30 = (int) $sr['d30']; $last = $sr['last'];
}

$pageTitle   = 'Newsletter';
$pageHeading = 'Abonnés newsletter';
$activePage  = 'newsletter';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
require_once __DIR__ . '/includes/stats_helpers.php';
?>
<main class="admin-content">
  <div class="content-wrap">
    <div class="topbar">
      <h1><?= htmlspecialchars($pageHeading) ?></h1>
      <div class="admin-chip">Connecté : <?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></div>
    </div>
    <div class="page-body">

      <?php if ($flashSuccess !== ''): ?>
        <div class="flash success"><?= htmlspecialchars($flashSuccess) ?></div>
      <?php endif; ?>
      <?php if ($flashError !== ''): ?>
        <div class="flash error"><?= htmlspecialchars($flashError) ?></div>
      <?php endif; ?>

      <?php admin_stats_css(); ?>
      <div class="mini-stat-row">
        <div class="mini-stat-card">
          <div class="msi navy"><i class="bi bi-envelope-paper"></i></div>
          <div class="msl">TOTAL ABONNÉS</div>
          <div class="msv"><?= $totAll ?></div>
          <hr class="msd">
          <div class="mss"><?= $totAll ?> adresse(s) enregistrée(s)</div>
        </div>
        <div class="mini-stat-card">
          <div class="msi orange"><i class="bi bi-clock-history"></i></div>
          <div class="msl">7 DERNIERS JOURS</div>
          <div class="msv"><?= $tot7 ?></div>
          <hr class="msd">
          <div class="mss pos">Nouveaux inscrits</div>
        </div>
        <div class="mini-stat-card">
          <div class="msi green"><i class="bi bi-calendar3"></i></div>
          <div class="msl">30 DERNIERS JOURS</div>
          <div class="msv"><?= $tot30 ?></div>
          <hr class="msd">
          <div class="mss pos">Tendance mensuelle</div>
        </div>
        <div class="mini-stat-card">
          <div class="msi purple"><i class="bi bi-person-plus"></i></div>
          <div class="msl">DERNIER ABONNÉ</div>
          <div class="msv" style="font-size:18px;"><?= $last ? date('d/m/Y', strtotime($last)) : '—' ?></div>
          <hr class="msd">
          <div class="mss"><?= $last ? 'À ' . date('H:i', strtotime($last)) : 'Aucun abonné' ?></div>
        </div>
      </div>

      <div class="toolbar">
        <form method="get" action="newsletter.php" class="search-form" style="margin:0;">
          <input type="text" name="q" class="search-input" value="<?= htmlspecialchars($search) ?>" placeholder="Rechercher une adresse e-mail...">
          <button type="submit" class="btn-small btn-navy">Rechercher</button>
          <?php if ($search !== ''): ?>
            <a href="newsletter.php" class="btn-small btn-soft">Réinitialiser</a>
          <?php endif; ?>
        </form>
        <div class="muted">Total : <?= (int) $total ?> résultat(s)</div>
      </div>

      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>E-mail</th>
              <th>Inscrit le</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($subs)): ?>
              <tr><td colspan="4">Aucun abonné trouvé.</td></tr>
            <?php else: foreach ($subs as $s): ?>
              <tr>
                <td><?= (int) $s['id'] ?></td>
                <td><?= htmlspecialchars($s['email']) ?></td>
                <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($s['created_at']))) ?></td>
                <td>
                  <form method="post" action="newsletter.php" class="inline-form"
                        onsubmit="return confirm('Désinscrire cette adresse ?');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= (int) $s['id'] ?>">
                    <button type="submit" class="btn-small btn-soft" title="Supprimer">
                      <i class="bi bi-trash" style="color:#c0392b"></i>
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>

      <?php if ($totalPages > 1): ?>
        <div class="pagination">
          <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <a class="btn-small <?= $p === $page ? 'btn-coral' : 'btn-soft' ?>"
               href="newsletter.php?page=<?= (int) $p ?>&q=<?= urlencode($search) ?>"><?= (int) $p ?></a>
          <?php endfor; ?>
        </div>
      <?php endif; ?>

    </div>
  </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
