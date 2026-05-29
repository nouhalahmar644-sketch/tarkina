<?php
require_once __DIR__ . '/includes/auth_admin.php';

// ---------- Auto-create table (so it works on any machine that pulls this code) ----------
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS gallery_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_path VARCHAR(500) NOT NULL,
    alt_text VARCHAR(255) NOT NULL DEFAULT '',
    position INT NOT NULL DEFAULT 0,
    statut VARCHAR(20) NOT NULL DEFAULT 'actif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

// ---------- Upload directory ----------
$uploadBase = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'gallery';
if (!is_dir($uploadBase)) { @mkdir($uploadBase, 0755, true); }

// ---------- Flash ----------
$flashSuccess = '';
$flashError   = '';
if (!empty($_SESSION['gal_flash_success'])) { $flashSuccess = (string) $_SESSION['gal_flash_success']; unset($_SESSION['gal_flash_success']); }
if (!empty($_SESSION['gal_flash_error']))   { $flashError   = (string) $_SESSION['gal_flash_error'];   unset($_SESSION['gal_flash_error']); }

// ---------- Upload helper ----------
function gal_upload_image(): string {
    global $uploadBase;
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) return '';
    $tmp  = $_FILES['image']['tmp_name'];
    $orig = $_FILES['image']['name'];
    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    if (!in_array(@mime_content_type($tmp), $allowed, true)) return '';
    if ($_FILES['image']['size'] > 6 * 1024 * 1024) return ''; // 6 MB max
    $ext  = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
    $file = uniqid('gal_', true) . '.' . $ext;
    if (!move_uploaded_file($tmp, $uploadBase . DIRECTORY_SEPARATOR . $file)) return '';
    return 'uploads/gallery/' . $file;
}

// ---------- POST handling ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'add') {
        $alt      = trim((string) ($_POST['alt_text'] ?? ''));
        $position = (int) ($_POST['position'] ?? 0);
        $path     = gal_upload_image();
        if ($path === '') {
            $_SESSION['gal_flash_error'] = 'Veuillez sélectionner une image valide (JPG, PNG, WebP, GIF — max 6 Mo).';
        } else {
            $st = mysqli_prepare($conn, "INSERT INTO gallery_images (image_path, alt_text, position) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($st, 'ssi', $path, $alt, $position);
            mysqli_stmt_execute($st);
            mysqli_stmt_close($st);
            $_SESSION['gal_flash_success'] = 'Image ajoutée à la galerie.';
        }
        header('Location: gallery.php'); exit;
    }

    if ($action === 'delete') {
        $id = (int) ($_POST['image_id'] ?? 0);
        if ($id > 0) {
            $st = mysqli_prepare($conn, "SELECT image_path FROM gallery_images WHERE id = ? LIMIT 1");
            mysqli_stmt_bind_param($st, 'i', $id);
            mysqli_stmt_execute($st);
            mysqli_stmt_bind_result($st, $path);
            $found = mysqli_stmt_fetch($st);
            mysqli_stmt_close($st);
            if ($found && $path && file_exists(__DIR__ . '/../' . $path)) { @unlink(__DIR__ . '/../' . $path); }
            $st = mysqli_prepare($conn, "DELETE FROM gallery_images WHERE id = ?");
            mysqli_stmt_bind_param($st, 'i', $id);
            mysqli_stmt_execute($st);
            mysqli_stmt_close($st);
            $_SESSION['gal_flash_success'] = 'Image supprimée.';
        }
        header('Location: gallery.php'); exit;
    }

    if ($action === 'toggle_status') {
        $id = (int) ($_POST['image_id'] ?? 0);
        if ($id > 0) {
            $st = mysqli_prepare($conn, "UPDATE gallery_images SET statut = CASE WHEN statut = 'actif' THEN 'inactif' ELSE 'actif' END WHERE id = ?");
            mysqli_stmt_bind_param($st, 'i', $id);
            mysqli_stmt_execute($st);
            mysqli_stmt_close($st);
            $_SESSION['gal_flash_success'] = 'Statut mis à jour.';
        }
        header('Location: gallery.php'); exit;
    }

    if ($action === 'update_meta') {
        $id       = (int) ($_POST['image_id'] ?? 0);
        $position = (int) ($_POST['position'] ?? 0);
        $alt      = trim((string) ($_POST['alt_text'] ?? ''));
        if ($id > 0) {
            $st = mysqli_prepare($conn, "UPDATE gallery_images SET position = ?, alt_text = ? WHERE id = ?");
            mysqli_stmt_bind_param($st, 'isi', $position, $alt, $id);
            mysqli_stmt_execute($st);
            mysqli_stmt_close($st);
            $_SESSION['gal_flash_success'] = 'Image mise à jour.';
        }
        header('Location: gallery.php'); exit;
    }
}

// ---------- Fetch list ----------
$images = [];
$res = mysqli_query($conn, "SELECT id, image_path, alt_text, position, statut, created_at FROM gallery_images ORDER BY position ASC, id ASC");
if ($res) { while ($r = mysqli_fetch_assoc($res)) { $images[] = $r; } }

$nbActive = 0;
foreach ($images as $img) { if ($img['statut'] === 'actif') $nbActive++; }

$pageTitle   = 'Galerie';
$pageHeading = 'Galerie « La Tunisie en Images »';
$activePage  = 'gallery';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
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

      <div class="form-card">
        <div class="stat-label">Ajouter une image à la galerie</div>
        <p class="muted" style="margin: 8px 0 16px; font-size: 13px;">
          Les <strong><?= $nbActive ?></strong> premières images actives apparaissent dans la section « La Tunisie en Images » de la page d'accueil (max 6 affichées). Glissez l'ordre via le champ <em>Position</em>.
        </p>
        <form method="post" action="gallery.php" enctype="multipart/form-data">
          <input type="hidden" name="action" value="add">
          <div class="form-grid">
            <div class="form-field" style="grid-column: 1 / span 2;">
              <label>Image (JPG / PNG / WebP / GIF, max 6 Mo)</label>
              <div class="custom-file-wrap">
                <input type="file" id="image" name="image" accept="image/*" required onchange="this.parentElement.querySelector('.custom-file-name').textContent = this.files[0] ? this.files[0].name : 'Aucun fichier choisi'">
                <label for="image" class="custom-file-btn">Choisir un fichier</label>
                <span class="custom-file-name">Aucun fichier choisi</span>
              </div>
            </div>
            <div class="form-field">
              <label>Texte alternatif (description courte)</label>
              <input type="text" name="alt_text" class="form-input" placeholder="Ex : Sidi Bou Saïd au coucher du soleil" maxlength="200">
            </div>
            <div class="form-field">
              <label>Position (plus petit = affiché en premier)</label>
              <input type="number" name="position" class="form-input" value="<?= count($images) ?>" min="0" max="999">
            </div>
          </div>
          <div class="actions" style="margin-top: 14px;">
            <button type="submit" class="btn-small btn-coral">+ Ajouter l'image</button>
          </div>
        </form>
      </div>

      <div class="toolbar" style="margin-top: 22px;">
        <div></div>
        <div class="muted">Total : <strong><?= count($images) ?></strong> · Actives : <strong><?= $nbActive ?></strong></div>
      </div>

      <?php if (empty($images)): ?>
        <div class="mini-card" style="text-align: center; padding: 40px;">
          <h4>Aucune image dans la galerie</h4>
          <p class="muted">Ajoutez votre première image avec le formulaire ci-dessus. Tant que la galerie est vide, la page d'accueil affiche les images par défaut.</p>
        </div>
      <?php else: ?>
        <div class="popular-grid">
          <?php foreach ($images as $img):
            $src = (strpos($img['image_path'], 'http') === 0) ? $img['image_path'] : ('../' . $img['image_path']);
          ?>
            <article class="pop-card" style="<?= $img['statut'] === 'inactif' ? 'opacity:.55;' : '' ?>">
              <div class="pop-card-img" style="height: 170px;">
                <span class="pop-badge" style="background: <?= $img['statut'] === 'actif' ? 'var(--coral)' : '#888' ?>;"><?= htmlspecialchars($img['statut']) ?></span>
                <img src="<?= htmlspecialchars($src) ?>" alt="<?= htmlspecialchars($img['alt_text']) ?>" onerror="this.style.background='#f0f0f0'">
              </div>
              <div class="pop-card-body">
                <form method="post" action="gallery.php" style="display:flex; flex-direction:column; gap:6px; margin:0;">
                  <input type="hidden" name="action" value="update_meta">
                  <input type="hidden" name="image_id" value="<?= (int) $img['id'] ?>">
                  <input type="text" name="alt_text" class="form-input" value="<?= htmlspecialchars($img['alt_text']) ?>" placeholder="Texte alternatif" maxlength="200" style="font-size:12px; padding:6px 8px;">
                  <div style="display:flex; gap:6px; align-items:center;">
                    <input type="number" name="position" class="form-input" value="<?= (int) $img['position'] ?>" min="0" max="999" title="Position" style="width:70px; font-size:12px; padding:6px 8px;">
                    <button type="submit" class="btn-small btn-soft" style="flex:1;" title="Enregistrer position + texte">Enregistrer</button>
                  </div>
                </form>
                <div class="actions" style="margin-top: 10px;">
                  <form method="post" action="gallery.php" class="inline-form">
                    <input type="hidden" name="action" value="toggle_status">
                    <input type="hidden" name="image_id" value="<?= (int) $img['id'] ?>">
                    <button type="submit" class="btn-small btn-navy" title="Activer / désactiver"><i class="bi <?= $img['statut'] === 'actif' ? 'bi-eye-slash' : 'bi-eye' ?>"></i></button>
                  </form>
                  <form method="post" action="gallery.php" class="inline-form" onsubmit="return confirm('Supprimer cette image ?');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="image_id" value="<?= (int) $img['id'] ?>">
                    <button type="submit" class="btn-small btn-soft" title="Supprimer" style="color:#b43737;"><i class="bi bi-trash"></i></button>
                  </form>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

    </div>
  </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
