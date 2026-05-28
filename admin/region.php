<?php
require_once __DIR__ . '/includes/auth_admin.php';

// ---------- Upload directory ----------
$uploadBase = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'regions';
if (!is_dir($uploadBase)) { @mkdir($uploadBase, 0755, true); }

// ---------- Flash messages ----------
$flashSuccess = '';
$flashError   = '';
if (!empty($_SESSION['reg_flash_success'])) { $flashSuccess = (string)$_SESSION['reg_flash_success']; unset($_SESSION['reg_flash_success']); }
if (!empty($_SESSION['reg_flash_error']))   { $flashError   = (string)$_SESSION['reg_flash_error'];   unset($_SESSION['reg_flash_error']); }

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
    $file = uniqid('reg_', true) . '.' . $ext;
    if (!move_uploaded_file($tmp, $uploadBase . DIRECTORY_SEPARATOR . $file)) return '';
    return 'uploads/regions/' . $file;
}

// ---------- POST handling ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action   = (string)($_POST['action']   ?? '');
    $regionId = (int)($_POST['region_id']   ?? 0);
    $qReturn  = trim((string)($_POST['q']   ?? ''));
    $pgReturn = max(1, (int)($_POST['page'] ?? 1));
    $redirect = 'region.php?q=' . urlencode($qReturn) . '&page=' . $pgReturn;

    // DELETE
    if ($action === 'delete') {
        if ($regionId <= 0) {
            $_SESSION['reg_flash_error'] = 'Région invalide.';
        } else {
            $st = mysqli_prepare($conn, 'DELETE FROM region WHERE id = ? LIMIT 1');
            if ($st) {
                mysqli_stmt_bind_param($st, 'i', $regionId);
                mysqli_stmt_execute($st);
                $aff = mysqli_stmt_affected_rows($st);
                mysqli_stmt_close($st);
                $_SESSION[$aff > 0 ? 'reg_flash_success' : 'reg_flash_error'] =
                    $aff > 0 ? 'Région supprimée avec succès.' : 'Région introuvable.';
            } else {
                $_SESSION['reg_flash_error'] = 'Suppression impossible pour le moment.';
            }
        }
        header('Location: ' . $redirect); exit;
    }

    // SAVE
    if ($action === 'save') {
        $nom              = trim((string)($_POST['nom']              ?? ''));
        $description      = trim((string)($_POST['description']      ?? ''));
        $meilleure_saison = trim((string)($_POST['meilleure_saison'] ?? ''));
        $langues          = trim((string)($_POST['langues']          ?? ''));
        $monnaie          = trim((string)($_POST['monnaie']          ?? ''));

        if ($nom === '') {
            $_SESSION['reg_flash_error'] = 'Le nom est obligatoire.';
            header('Location: ' . $redirect); exit;
        }

        $photoP      = moveUploadedImage('photo_principale');
        $photosSec   = [];
        for ($i = 0; $i < 4; $i++) { $p = moveUploadedImage('photos_secondaires', $i); if ($p !== '') $photosSec[] = $p; }
        $photosSecJs = json_encode($photosSec, JSON_UNESCAPED_UNICODE);

        if ($regionId > 0) {
            if ($photoP !== '') {
                // UPDATE with new photo
                $sql = 'UPDATE region SET nom=?,description=?,meilleure_saison=?,langues=?,monnaie=?,photo_principale=?,photos_sec=? WHERE id=? LIMIT 1';
                $st  = mysqli_prepare($conn, $sql);
                if ($st) {
                    mysqli_stmt_bind_param($st,'sssssssi',$nom,$description,$meilleure_saison,$langues,$monnaie,$photoP,$photosSecJs,$regionId);
                    mysqli_stmt_execute($st); mysqli_stmt_close($st);
                    $_SESSION['reg_flash_success'] = 'Région mise à jour.';
                } else { $_SESSION['reg_flash_error'] = 'Modification impossible.'; }
            } else {
                // UPDATE without photo
                $sql = 'UPDATE region SET nom=?,description=?,meilleure_saison=?,langues=?,monnaie=? WHERE id=? LIMIT 1';
                $st  = mysqli_prepare($conn, $sql);
                if ($st) {
                    mysqli_stmt_bind_param($st,'sssssi',$nom,$description,$meilleure_saison,$langues,$monnaie,$regionId);
                    mysqli_stmt_execute($st); mysqli_stmt_close($st);
                    $_SESSION['reg_flash_success'] = 'Région mise à jour.';
                } else { $_SESSION['reg_flash_error'] = 'Modification impossible.'; }
            }
        } else {
            $sql = 'INSERT INTO region (nom,description,meilleure_saison,langues,monnaie,photo_principale,photos_sec) VALUES (?,?,?,?,?,?,?)';
            $st  = mysqli_prepare($conn, $sql);
            if ($st) {
                mysqli_stmt_bind_param($st,'sssssss',$nom,$description,$meilleure_saison,$langues,$monnaie,$photoP,$photosSecJs);
                mysqli_stmt_execute($st); mysqli_stmt_close($st);
                $_SESSION['reg_flash_success'] = 'Région ajoutée avec succès.';
            } else { $_SESSION['reg_flash_error'] = 'Création impossible pour le moment.'; }
        }
        header('Location: ' . $redirect); exit;
    }
}

// ---------- GET : search / pagination ----------
$search  = isset($_GET['q'])    ? trim((string)$_GET['q'])        : '';
$page    = isset($_GET['page']) ? max(1, (int)$_GET['page'])      : 1;
$perPage = 8;
$offset  = ($page - 1) * $perPage;

$editId   = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$editItem = null;
if ($editId > 0) {
    $sql = 'SELECT id,nom,description,meilleure_saison,langues,monnaie,photo_principale,photos_sec FROM region WHERE id=? LIMIT 1';
    $st  = mysqli_prepare($conn, $sql);
    if ($st) {
        mysqli_stmt_bind_param($st,'i',$editId);
        mysqli_stmt_execute($st);
        mysqli_stmt_bind_result($st,$eid,$enom,$edesc,$esaison,$elangues,$emonnaie,$ephoto,$ephsec);
        if (mysqli_stmt_fetch($st)) {
            $editItem = ['id'=>(int)$eid,'nom'=>$enom,'description'=>$edesc,'meilleure_saison'=>$esaison,
                         'langues'=>$elangues,'monnaie'=>$emonnaie,'photo_principale'=>$ephoto,'photos_sec'=>$ephsec];
        }
        mysqli_stmt_close($st);
    }
}

$where = ''; $like = '';
if ($search !== '') { $where = ' WHERE nom LIKE ?'; $like = '%'.$search.'%'; }

$totalItems = 0;
$cst = mysqli_prepare($conn, 'SELECT COUNT(*) FROM region'.$where);
if ($cst) {
    if ($search !== '') mysqli_stmt_bind_param($cst,'s',$like);
    mysqli_stmt_execute($cst);
    mysqli_stmt_bind_result($cst,$cdb);
    if (mysqli_stmt_fetch($cst)) $totalItems = (int)$cdb;
    mysqli_stmt_close($cst);
}

$totalPages = (int)ceil($totalItems / $perPage);
if ($totalPages > 0 && $page > $totalPages) { $page = $totalPages; $offset = ($page-1)*$perPage; }

$items = [];
$lst = mysqli_prepare($conn,'SELECT id,nom,meilleure_saison,langues,monnaie,photo_principale FROM region'.$where.' ORDER BY id DESC LIMIT ? OFFSET ?');
if ($lst) {
    if ($search !== '') mysqli_stmt_bind_param($lst,'sii',$like,$perPage,$offset);
    else               mysqli_stmt_bind_param($lst,'ii',$perPage,$offset);
    mysqli_stmt_execute($lst);
    mysqli_stmt_bind_result($lst,$iid,$inom,$isaison,$ilangues,$imonnaie,$iphoto);
    while (mysqli_stmt_fetch($lst)) {
        $items[] = ['id'=>(int)$iid,'nom'=>$inom,'meilleure_saison'=>$isaison,'langues'=>$ilangues,'monnaie'=>$imonnaie,'photo_principale'=>$iphoto];
    }
    mysqli_stmt_close($lst);
}

$pageTitle   = 'Régions';
$pageHeading = 'Régions';
$activePage  = 'region';

$showForm = isset($_GET['add']) || $editItem !== null;

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
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

      <?php if ($showForm): ?>
      <!-- FORM -->
      <div class="form-card">
        <div class="stat-label"><?php echo $editItem ? 'Modifier la région' : 'Ajouter une région'; ?></div>
        <form method="post" action="region.php?q=<?php echo urlencode($search); ?>&page=<?php echo (int)$page; ?>" enctype="multipart/form-data">
          <input type="hidden" name="action"    value="save">
          <input type="hidden" name="region_id" value="<?php echo $editItem ? (int)$editItem['id'] : 0; ?>">
          <input type="hidden" name="q"         value="<?php echo htmlspecialchars($search); ?>">
          <input type="hidden" name="page"      value="<?php echo (int)$page; ?>">

          <div class="form-grid">

            <!-- 1. Nom -->
            <div class="form-field">
              <label>Nom</label>
              <input type="text" name="nom" class="form-input"
                     value="<?php echo htmlspecialchars($editItem ? $editItem['nom'] : ''); ?>"
                     placeholder="Nom de la région" required>
            </div>

            <!-- 2. Meilleure saison -->
            <div class="form-field">
              <label>Meilleure saison</label>
              <input type="text" name="meilleure_saison" class="form-input"
                     value="<?php echo htmlspecialchars($editItem ? (string)$editItem['meilleure_saison'] : ''); ?>"
                     placeholder="Ex: Printemps, Été...">
            </div>

            <!-- 3. Langues -->
            <div class="form-field">
              <label>Langues</label>
              <input type="text" name="langues" class="form-input"
                     value="<?php echo htmlspecialchars($editItem ? (string)$editItem['langues'] : ''); ?>"
                     placeholder="Ex: Arabe, Français...">
            </div>

            <!-- 4. Monnaie -->
            <div class="form-field">
              <label>Monnaie</label>
              <input type="text" name="monnaie" class="form-input"
                     value="<?php echo htmlspecialchars($editItem ? (string)$editItem['monnaie'] : ''); ?>"
                     placeholder="Ex: TND, EUR...">
            </div>

            <!-- 5. Description -->
            <div class="form-field" style="grid-column:1/-1;">
              <label>Description</label>
              <textarea name="description" class="form-textarea" placeholder="Description de la région..."><?php echo htmlspecialchars($editItem ? $editItem['description'] : ''); ?></textarea>
            </div>

            <!-- 6. Photo principale -->
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

            <!-- 7. Photos secondaires -->
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

          </div><!-- /form-grid -->

          <div class="actions" style="margin-top:14px;">
            <button type="submit" class="btn-small btn-coral">
              <?php echo $editItem ? 'Enregistrer les modifications' : 'Ajouter la région'; ?>
            </button>
            <a href="region.php?q=<?php echo urlencode($search); ?>&page=<?php echo (int)$page; ?>" class="btn-small btn-soft">Annuler</a>
          </div>
        </form>
      </div><!-- /form-card -->
      <?php endif; ?>

      <!-- TOOLBAR -->
      <div class="toolbar">
        <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
            <a href="region.php?add=1" class="btn-small btn-coral">+ Ajouter une région</a>
            <form method="get" action="region.php" class="search-form" style="margin:0;">
              <input type="text" name="q" class="search-input"
                     value="<?php echo htmlspecialchars($search); ?>"
                     placeholder="Rechercher par nom...">
              <button type="submit" class="btn-small btn-navy">Rechercher</button>
              <?php if ($search !== ''): ?>
                <a href="region.php" class="btn-small btn-soft">Réinitialiser</a>
              <?php endif; ?>
            </form>
        </div>
        <div class="muted">Total régions : <?php echo (int)$totalItems; ?></div>
      </div>

      <!-- CARDS preview -->
      <div class="popular-grid">
        <?php if (empty($items)): ?>
          <div class="mini-card" style="grid-column: 1/-1;">
            <h4>Aucune région</h4>
            <p class="muted">Ajoutez votre première région.</p>
          </div>
        <?php else: ?>
          <?php foreach (array_slice($items, 0, 4) as $item): ?>
            <article class="pop-card">
              <div class="pop-card-img">
                <span class="pop-badge">Région</span>
                <?php 
                  $img = $item['photo_principale'] ?: 'https://images.unsplash.com/photo-1540260074744-934336c53549?auto=format&fit=crop&w=800&q=80';
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
                <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($item['nom']); ?>">
              </div>
              <div class="pop-card-body">
                <div class="pop-card-title"><?php echo htmlspecialchars($item['nom']); ?></div>
                <div class="pop-card-price">
                  <span style="font-size: 14px; color: var(--grey);"><?php echo htmlspecialchars($item['meilleure_saison'] ?: '—'); ?></span>
                  <a href="region.php?edit=<?php echo $item['id']; ?>" class="pop-btn">
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
              <th>Nom</th>
              <th>Meilleure saison</th>
              <th>Langues</th>
              <th>Monnaie</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($items)): ?>
              <tr><td colspan="6">Aucune région trouvée.</td></tr>
            <?php else: ?>
              <?php foreach ($items as $item): ?>
                <tr>
                  <td><?php echo (int)$item['id']; ?></td>
                  <td><?php echo htmlspecialchars($item['nom']); ?></td>
                  <td><?php echo htmlspecialchars($item['meilleure_saison'] ?: '—'); ?></td>
                  <td><?php echo htmlspecialchars($item['langues'] ?: '—'); ?></td>
                  <td><?php echo htmlspecialchars($item['monnaie'] ?: '—'); ?></td>
                  <td>
                    <div class="actions">
                      <a class="btn-small btn-soft"
                         href="region.php?edit=<?php echo (int)$item['id']; ?>&q=<?php echo urlencode($search); ?>&page=<?php echo (int)$page; ?>" title="Modifier"><i class="bi bi-pencil-square"></i></a>
                      <form method="post" action="region.php" class="inline-form"
                            onsubmit="return confirm('Supprimer cette région ?');">
                        <input type="hidden" name="action"    value="delete">
                        <input type="hidden" name="region_id" value="<?php echo (int)$item['id']; ?>">
                        <input type="hidden" name="q"         value="<?php echo htmlspecialchars($search); ?>">
                        <input type="hidden" name="page"      value="<?php echo (int)$page; ?>">
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
               href="region.php?page=<?php echo (int)$p; ?>&q=<?php echo urlencode($search); ?>">
              <?php echo (int)$p; ?>
            </a>
          <?php endfor; ?>
        </div>
      <?php endif; ?>

    </div><!-- /page-body -->
  </div><!-- /content-wrap -->
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

