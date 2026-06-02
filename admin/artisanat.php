<?php
require_once __DIR__ . '/includes/auth_admin.php';

// ---------- Upload directory ----------
$uploadBase = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'artisanat';
if (!is_dir($uploadBase)) { @mkdir($uploadBase, 0755, true); }

// ---------- Flash messages ----------
$flashSuccess = '';
$flashError   = '';
if (!empty($_SESSION['art_flash_success'])) { $flashSuccess = (string)$_SESSION['art_flash_success']; unset($_SESSION['art_flash_success']); }
if (!empty($_SESSION['art_flash_error']))   { $flashError   = (string)$_SESSION['art_flash_error'];   unset($_SESSION['art_flash_error']); }

// ---------- Upload helper ----------
function moveUploadedImage(string $name, int $idx = -1): string {
    global $uploadBase;
    if ($idx >= 0) {
        if (!isset($_FILES[$name]['error'][$idx]) || $_FILES[$name]['error'][$idx] !== UPLOAD_ERR_OK) return '';
        $tmp = $_FILES[$name]['tmp_name'][$idx]; $orig = $_FILES[$name]['name'][$idx];
    } else {
        if (!isset($_FILES[$name]) || $_FILES[$name]['error'] !== UPLOAD_ERR_OK) return '';
        $tmp = $_FILES[$name]['tmp_name']; $orig = $_FILES[$name]['name'];
    }
    $allowed = ['image/jpeg','image/png','image/webp','image/gif'];
    if (!in_array(@mime_content_type($tmp), $allowed, true)) return '';
    $ext  = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
    $file = uniqid('art_', true) . '.' . $ext;
    if (!move_uploaded_file($tmp, $uploadBase . DIRECTORY_SEPARATOR . $file)) return '';
    return 'uploads/artisanat/' . $file;
}

// ---------- Fetch regions for dropdown ----------
$regionsList = [];
$resReg = mysqli_query($conn, 'SELECT id, nom FROM region ORDER BY nom ASC');
if ($resReg) {
    while ($r = mysqli_fetch_assoc($resReg)) {
        $regionsList[] = $r;
    }
}

// ---------- POST handling ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action    = (string)($_POST['action']     ?? '');
    $artId     = (int)($_POST['art_id']        ?? 0);
    $qReturn   = trim((string)($_POST['q']     ?? ''));
    $pgReturn  = max(1, (int)($_POST['page']   ?? 1));
    $redirect  = 'artisanat.php?q=' . urlencode($qReturn) . '&page=' . $pgReturn;

    // DELETE
    if ($action === 'delete') {
        if ($artId <= 0) {
            $_SESSION['art_flash_error'] = 'Produit invalide.';
        } else {
            $st = mysqli_prepare($conn, 'DELETE FROM artisanat WHERE id = ? LIMIT 1');
            if ($st) {
                mysqli_stmt_bind_param($st, 'i', $artId);
                mysqli_stmt_execute($st);
                $aff = mysqli_stmt_affected_rows($st);
                mysqli_stmt_close($st);
                $_SESSION[$aff > 0 ? 'art_flash_success' : 'art_flash_error'] =
                    $aff > 0 ? 'Produit supprimé avec succès.' : 'Produit introuvable.';
            } else {
                $_SESSION['art_flash_error'] = 'Suppression impossible pour le moment.';
            }
        }
        header('Location: ' . $redirect); exit;
    }

    // SAVE
    if ($action === 'save') {
        $titre        = trim((string)($_POST['titre']        ?? ''));
        $localisation = trim((string)($_POST['localisation'] ?? ''));
        $prixRaw      = trim((string)($_POST['prix']         ?? '0'));
        $stockRaw     = trim((string)($_POST['stock']        ?? '0'));
        $description  = trim((string)($_POST['description']  ?? ''));
        $statut       = trim((string)($_POST['statut']       ?? 'brouillon'));
        $regionIdRaw  = trim((string)($_POST['region_id']    ?? '0'));

        if ($titre === '') {
            $_SESSION['art_flash_error'] = 'Le titre est obligatoire.';
            header('Location: ' . $redirect); exit;
        }
        if (!is_numeric($prixRaw)) {
            $_SESSION['art_flash_error'] = 'Prix invalide.';
            header('Location: ' . $redirect); exit;
        }

        $prix      = (float)$prixRaw;
        $stock     = max(0, (int)$stockRaw);
        $regionId  = (int)$regionIdRaw;
        if ($regionId <= 0) $regionId = null;

        if (!in_array($statut, ['brouillon','publié'], true)) $statut = 'brouillon';

        $photoP      = moveUploadedImage('photo_principale');
        $photosSec   = [];
        for ($i = 0; $i < 4; $i++) { $p = moveUploadedImage('photos_secondaires', $i); if ($p !== '') $photosSec[] = $p; }
        $photosSecJs = json_encode($photosSec, JSON_UNESCAPED_UNICODE);

        if ($artId > 0) {
            if ($photoP !== '') {
                // UPDATE with new photo
                $sql = 'UPDATE artisanat SET titre=?,localisation=?,prix=?,stock=?,description=?,photo_principale=?,photos_sec=?,statut=?,region_id=? WHERE id=? LIMIT 1';
                $st  = mysqli_prepare($conn, $sql);
                if ($st) {
                    mysqli_stmt_bind_param($st,'ssdisssssi',$titre,$localisation,$prix,$stock,$description,$photoP,$photosSecJs,$statut,$regionId,$artId);
                    mysqli_stmt_execute($st); mysqli_stmt_close($st);
                    $_SESSION['art_flash_success'] = 'Produit mis à jour.';
                } else { $_SESSION['art_flash_error'] = 'Modification impossible.'; }
            } else {
                // UPDATE without photo
                $sql = 'UPDATE artisanat SET titre=?,localisation=?,prix=?,stock=?,description=?,statut=?,region_id=? WHERE id=? LIMIT 1';
                $st  = mysqli_prepare($conn, $sql);
                if ($st) {
                    mysqli_stmt_bind_param($st,'ssdissii',$titre,$localisation,$prix,$stock,$description,$statut,$regionId,$artId);
                    mysqli_stmt_execute($st); mysqli_stmt_close($st);
                    $_SESSION['art_flash_success'] = 'Produit mis à jour.';
                } else { $_SESSION['art_flash_error'] = 'Modification impossible.'; }
            }
        } else {
            $sql = 'INSERT INTO artisanat (titre,localisation,prix,stock,description,photo_principale,photos_sec,statut,region_id) VALUES (?,?,?,?,?,?,?,?,?)';
            $st  = mysqli_prepare($conn, $sql);
            if ($st) {
                mysqli_stmt_bind_param($st,'ssdissssi',$titre,$localisation,$prix,$stock,$description,$photoP,$photosSecJs,$statut,$regionId);
                mysqli_stmt_execute($st); mysqli_stmt_close($st);
                $_SESSION['art_flash_success'] = 'Produit ajouté avec succès.';
            } else { $_SESSION['art_flash_error'] = 'Création impossible pour le moment.'; }
        }
        header('Location: ' . $redirect); exit;
    }
}

// ---------- GET : search / pagination / status filter ----------
$search  = isset($_GET['q'])    ? trim((string)$_GET['q'])        : '';
$page    = isset($_GET['page']) ? max(1, (int)$_GET['page'])      : 1;
$status  = isset($_GET['statut']) ? trim((string)$_GET['statut']) : '';
$perPage = 8;
$offset  = ($page - 1) * $perPage;

$editId   = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$editItem = null;
if ($editId > 0) {
    $sql = 'SELECT id,titre,localisation,prix,stock,description,photo_principale,photos_sec,statut,region_id FROM artisanat WHERE id=? LIMIT 1';
    $st  = mysqli_prepare($conn, $sql);
    if ($st) {
        mysqli_stmt_bind_param($st,'i',$editId);
        mysqli_stmt_execute($st);
        mysqli_stmt_bind_result($st,$eid,$etit,$eloc,$eprix,$estk,$edesc,$ephoto,$ephsec,$estat,$ereg);
        if (mysqli_stmt_fetch($st)) {
            $editItem = ['id'=>(int)$eid,'titre'=>$etit,'localisation'=>$eloc,'prix'=>(float)$eprix,
                         'stock'=>(int)$estk,'description'=>$edesc,
                         'photo_principale'=>$ephoto,'photos_sec'=>$ephsec,'statut'=>$estat,'region_id'=>(int)$ereg];
        }
        mysqli_stmt_close($st);
    }
}

// Build dynamic WHERE: optional text search + optional status filter
$whereParts = []; $types = ''; $args = [];
if ($search !== '') {
    $whereParts[] = '(titre LIKE ? OR localisation LIKE ? OR statut LIKE ?)';
    $like = '%' . $search . '%';
    $types .= 'sss'; $args[] = $like; $args[] = $like; $args[] = $like;
}
if ($status !== '') {
    $whereParts[] = 'statut = ?';
    $types .= 's'; $args[] = $status;
}
$where = $whereParts ? ' WHERE ' . implode(' AND ', $whereParts) : '';

$totalItems = 0;
$cst = mysqli_prepare($conn, 'SELECT COUNT(*) FROM artisanat' . $where);
if ($cst) {
    if ($types !== '') mysqli_stmt_bind_param($cst, $types, ...$args);
    mysqli_stmt_execute($cst);
    mysqli_stmt_bind_result($cst,$cdb);
    if (mysqli_stmt_fetch($cst)) $totalItems = (int)$cdb;
    mysqli_stmt_close($cst);
}

$totalPages = (int)ceil($totalItems / $perPage);
if ($totalPages > 0 && $page > $totalPages) { $page = $totalPages; $offset = ($page-1)*$perPage; }

$items = [];
$lst = mysqli_prepare($conn,'SELECT id,titre,localisation,prix,stock,statut,photo_principale,region_id FROM artisanat'.$where.' ORDER BY id DESC LIMIT ? OFFSET ?');
if ($lst) {
    $allTypes = $types . 'ii'; $allArgs = $args; $allArgs[] = $perPage; $allArgs[] = $offset;
    mysqli_stmt_bind_param($lst, $allTypes, ...$allArgs);
    mysqli_stmt_execute($lst);
    mysqli_stmt_bind_result($lst,$iid,$itit,$iloc,$iprix,$istk,$istat,$iphoto,$ireg);
    while (mysqli_stmt_fetch($lst)) {
        $items[] = ['id'=>(int)$iid,'titre'=>$itit,'localisation'=>$iloc,'prix'=>(float)$iprix,
                    'stock'=>(int)$istk,'statut'=>$istat,'photo_principale'=>$iphoto,'region_id'=>$ireg];
    }
    mysqli_stmt_close($lst);
}

$pageTitle   = 'Artisanat';
$pageHeading = 'Produits d\'artisanat';
$activePage  = 'artisanat';

$showForm = isset($_GET['add']) || $editItem !== null;

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
require_once __DIR__ . '/includes/stats_helpers.php';
?>
<main class="admin-content">
  <div class="content-wrap">
    <div class="topbar">
      <h1><?php echo htmlspecialchars($pageHeading); ?></h1>
      <div class="admin-chip">Connecté : <?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
    </div>
    <div class="page-body">

      <?php if ($flashSuccess !== ''): ?>
        <div class="flash success"><?php echo htmlspecialchars($flashSuccess); ?></div>
      <?php endif; ?>
      <?php if ($flashError !== ''): ?>
        <div class="flash error"><?php echo htmlspecialchars($flashError); ?></div>
      <?php endif; ?>

      <?php if (!$showForm): ?>
        <?php admin_render_service_stats($conn, [
            'table' => 'artisanat',
            'label' => 'produits',
            'icon_total' => 'bi-bag-heart',
        ]); ?>
      <?php endif; ?>

      <?php if ($showForm): ?>
      <!-- FORM -->
      <div class="form-card">
        <div class="stat-label"><?php echo $editItem ? 'Modifier le produit' : 'Ajouter un produit'; ?></div>
        <form method="post" action="artisanat.php?q=<?php echo urlencode($search); ?>&page=<?php echo (int)$page; ?>" enctype="multipart/form-data">
          <input type="hidden" name="action"     value="save">
          <input type="hidden" name="art_id"     value="<?php echo $editItem ? (int)$editItem['id'] : 0; ?>">
          <input type="hidden" name="q"          value="<?php echo htmlspecialchars($search); ?>">
          <input type="hidden" name="page"       value="<?php echo (int)$page; ?>">

          <div class="form-grid">

            <!-- 1. Titre -->
            <div class="form-field">
              <label>Titre du produit</label>
              <input type="text" name="titre" class="form-input"
                     value="<?php echo htmlspecialchars($editItem ? $editItem['titre'] : ''); ?>"
                     placeholder="Tapis Berbère..." required>
            </div>

            <!-- 2. Localisation -->
            <div class="form-field">
              <label>Localisation / Origine</label>
              <input type="text" name="localisation" class="form-input"
                     value="<?php echo htmlspecialchars($editItem ? $editItem['localisation'] : ''); ?>"
                     placeholder="Village, région...">
            </div>

            <!-- 3. Region ID -->
            <div class="form-field">
              <label>Région associée</label>
              <?php $selReg = $editItem ? (int)$editItem['region_id'] : 0; ?>
              <select name="region_id" class="form-select">
                <option value="0">-- Aucune --</option>
                <?php foreach ($regionsList as $r): ?>
                    <option value="<?php echo $r['id']; ?>" <?php echo $selReg === (int)$r['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($r['nom']); ?>
                    </option>
                <?php endforeach; ?>
              </select>
            </div>

            <!-- 4. Prix -->
            <div class="form-field">
              <label>Prix (TND)</label>
              <input type="number" step="0.01" min="0" name="prix" class="form-input"
                     value="<?php echo htmlspecialchars($editItem ? (string)$editItem['prix'] : '0'); ?>">
            </div>

            <!-- 5. Stock -->
            <div class="form-field">
              <label>Stock disponible</label>
              <input type="number" min="0" name="stock" class="form-input"
                     value="<?php echo htmlspecialchars($editItem ? (string)$editItem['stock'] : '0'); ?>">
            </div>

            <!-- 6. Description -->
            <div class="form-field" style="grid-column:1/-1;">
              <label>Description</label>
              <textarea name="description" class="form-textarea" placeholder="Détails du produit, matériaux..."><?php echo htmlspecialchars($editItem ? $editItem['description'] : ''); ?></textarea>
            </div>

            <!-- 7. Photo principale -->
            <div class="form-field" style="grid-column:1/-1;">
              <label>Photo principale</label>
              <?php if ($editItem && !empty($editItem['photo_principale'])): ?>
                <p style="font-size:12px;color:#666;margin-bottom:6px;">
                  Actuelle : <a href="../../<?php echo htmlspecialchars($editItem['photo_principale']); ?>" target="_blank">voir</a>
                  — Sélectionnez une nouvelle image pour la remplacer.
                </p>
              <?php endif; ?>
              <div class="custom-file-wrap">
                <input type="file" id="photo_principale" name="photo_principale" accept="image/*" onchange="this.parentElement.querySelector('.custom-file-name').textContent = this.files[0] ? this.files[0].name : 'Aucun fichier choisi'">
                <label for="photo_principale" class="custom-file-btn">Choisir un fichier</label>
                <span class="custom-file-name">Aucun fichier choisi</span>
              </div>
            </div>

            <!-- 8. Photos secondaires -->
            <div class="form-field" style="grid-column:1/-1;">
              <label>Photos secondaires (jusqu'à 4)</label>
              <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:10px;margin-top:6px;">
                <?php for ($i = 0; $i < 4; $i++): ?>
                  <div class="custom-file-wrap">
                    <input type="file" id="photo_sec_<?php echo $i; ?>" name="photos_secondaires[]" accept="image/*" onchange="this.parentElement.querySelector('.custom-file-name').textContent = this.files[0] ? this.files[0].name : 'Aucun fichier choisi'">
                    <label for="photo_sec_<?php echo $i; ?>" class="custom-file-btn">Choisir un fichier</label>
                    <span class="custom-file-name">Aucun fichier choisi</span>
                  </div>
                <?php endfor; ?>
              </div>
            </div>

            <!-- 9. Statut -->
            <div class="form-field">
              <label>Statut</label>
              <?php $selStat = $editItem ? $editItem['statut'] : 'brouillon'; ?>
              <select name="statut" class="form-select">
                <option value="brouillon" <?php echo $selStat === 'brouillon' ? 'selected' : ''; ?>>Brouillon</option>
                <option value="publié"    <?php echo ($selStat === 'publié' || $selStat === 'actif') ? 'selected' : ''; ?>>Publié</option>
              </select>
            </div>

          </div><!-- /form-grid -->

          <div class="actions" style="margin-top:14px;">
            <button type="submit" class="btn-small btn-coral">
              <?php echo $editItem ? 'Enregistrer les modifications' : 'Ajouter le produit'; ?>
            </button>
            <a href="artisanat.php?q=<?php echo urlencode($search); ?>&page=<?php echo (int)$page; ?>" class="btn-small btn-soft">Annuler</a>
          </div>
        </form>
      </div><!-- /form-card -->
      <?php endif; ?>

      <!-- TOOLBAR -->
      <div class="toolbar">
        <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
            <a href="artisanat.php?add=1" class="btn-small btn-coral">+ Ajouter un produit</a>
            <form method="get" action="artisanat.php" class="search-form" style="margin:0;">
              <input type="text" name="q" class="search-input"
                     value="<?php echo htmlspecialchars($search); ?>"
                     placeholder="Rechercher titre, localisation, statut...">
              <?php if ($status !== ''): ?>
                <input type="hidden" name="statut" value="<?= htmlspecialchars($status) ?>">
              <?php endif; ?>
              <button type="submit" class="btn-small btn-navy">Rechercher</button>
              <?php if ($search !== '' || $status !== ''): ?>
                <a href="artisanat.php" class="btn-small btn-soft">Réinitialiser</a>
              <?php endif; ?>
            </form>
        </div>
        <div class="muted">Total produits : <?php echo (int)$totalItems; ?></div>
      </div>

      <!-- STATUS FILTER PILLS -->
      <div style="margin:0 0 18px;">
        <?php admin_render_status_filter($status, 'artisanat.php', ['q' => $search]); ?>
      </div>

      <!-- CARDS preview -->
      <div class="popular-grid">
        <?php if (empty($items)): ?>
          <div class="mini-card" style="grid-column: 1/-1;">
            <h4>Aucun produit</h4>
            <p class="muted">Ajoutez votre premier produit.</p>
          </div>
        <?php else: ?>
          <?php foreach (array_slice($items, 0, 4) as $item): ?>
            <article class="pop-card">
              <div class="pop-card-img">
                <span class="pop-badge">Artisanat</span>
                <?php 
                  $img = $item['photo_principale'] ?: 'https://images.unsplash.com/photo-1565193566173-7a0ee3dbe261?auto=format&fit=crop&w=600&q=80';
                  if (!empty($item['photo_principale'])) {
                      if (strpos($item['photo_principale'], 'http') === 0) {
                          $img = $item['photo_principale'];
                      } elseif (strpos($item['photo_principale'], 'uploads/') === 0) {
                          $img = '../' . $item['photo_principale'];
                      } else {
                          $img = '../uploads/' . $item['photo_principale'];
                      }
                  }
                ?>
                <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($item['titre']); ?>">
              </div>
              <div class="pop-card-body">
                <div class="pop-card-title"><?php echo htmlspecialchars($item['titre']); ?></div>
                <div class="pop-card-price">
                  <span style="font-size: 14px; color: var(--grey);"><?php echo htmlspecialchars($item['localisation']); ?> &bull; <?php echo number_format($item['prix'], 2, '.', ' '); ?> TND</span>
                  <a href="artisanat.php?edit=<?php echo $item['id']; ?>" class="pop-btn">
                    <i class="bi bi-pencil-square"></i>
                  </a>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <!-- TABLE -->
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Titre</th>
              <th>Localisation</th>
              <th>Prix (TND)</th>
              <th>Stock</th>
              <th>Statut</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($items)): ?>
              <tr><td colspan="7">Aucun produit trouvé.</td></tr>
            <?php else: ?>
              <?php foreach ($items as $item): ?>
                <tr>
                  <td><?php echo (int)$item['id']; ?></td>
                  <td><?php echo htmlspecialchars($item['titre']); ?></td>
                  <td><?php echo htmlspecialchars($item['localisation']); ?></td>
                  <td><?php echo number_format($item['prix'], 2, '.', ' '); ?></td>
                  <td><?php echo (int)$item['stock']; ?></td>
                  <td>
                    <span class="role-pill <?php echo ($item['statut'] === 'publié' || $item['statut'] === 'actif') ? 'admin' : ''; ?>">
                      <?php echo $item['statut'] === 'actif' ? 'Publié' : ucfirst(htmlspecialchars($item['statut'])); ?>
                    </span>
                  </td>
                  <td>
                    <div class="actions">
                      <a class="btn-small btn-soft"
                         href="artisanat.php?edit=<?php echo (int)$item['id']; ?>&q=<?php echo urlencode($search); ?>&page=<?php echo (int)$page; ?>" title="Modifier"><i class="bi bi-pencil-square"></i></a>
                      <form method="post" action="artisanat.php" class="inline-form"
                            onsubmit="return confirm('Supprimer ce produit ?');">
                        <input type="hidden" name="action"     value="delete">
                        <input type="hidden" name="art_id"     value="<?php echo (int)$item['id']; ?>">
                        <input type="hidden" name="q"          value="<?php echo htmlspecialchars($search); ?>">
                        <input type="hidden" name="page"       value="<?php echo (int)$page; ?>">
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
            <a class="btn-small <?php echo $p === $page ? 'btn-coral' : 'btn-soft'; ?>"
               href="artisanat.php?page=<?php echo (int)$p; ?>&q=<?php echo urlencode($search); ?>">
              <?php echo (int)$p; ?>
            </a>
          <?php endfor; ?>
        </div>
      <?php endif; ?>

    </div><!-- /page-body -->
  </div><!-- /content-wrap -->
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

