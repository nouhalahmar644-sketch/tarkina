<?php
require_once __DIR__ . '/includes/auth_admin.php';

// ---------- Upload directory ----------
$uploadBase = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'evenements';
if (!is_dir($uploadBase)) { @mkdir($uploadBase, 0755, true); }

// ---------- Fetch regions for dropdown ----------
$regionsList = [];
$resReg = mysqli_query($conn, 'SELECT id, nom FROM region ORDER BY nom ASC');
if ($resReg) {
    while ($r = mysqli_fetch_assoc($resReg)) {
        $regionsList[] = $r;
    }
}

// ---------- Flash messages ----------
$flashSuccess = '';
$flashError   = '';
if (!empty($_SESSION['eve_flash_success'])) { $flashSuccess = (string)$_SESSION['eve_flash_success']; unset($_SESSION['eve_flash_success']); }
if (!empty($_SESSION['eve_flash_error']))   { $flashError   = (string)$_SESSION['eve_flash_error'];   unset($_SESSION['eve_flash_error']); }

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
    $file = uniqid('eve_', true) . '.' . $ext;
    if (!move_uploaded_file($tmp, $uploadBase . DIRECTORY_SEPARATOR . $file)) return '';
    return 'uploads/evenements/' . $file;
}

// ---------- POST handling ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action    = (string)($_POST['action']     ?? '');
    $evenementId = (int)($_POST['evenement_id'] ?? 0);
    $qReturn   = trim((string)($_POST['q']     ?? ''));
    $pgReturn  = max(1, (int)($_POST['page']   ?? 1));
    $redirect  = 'evenement.php?q=' . urlencode($qReturn) . '&page=' . $pgReturn;

    // DELETE
    if ($action === 'delete') {
        if ($evenementId <= 0) {
            $_SESSION['eve_flash_error'] = 'Événement invalide.';
        } else {
            $st = mysqli_prepare($conn, 'DELETE FROM evenement WHERE id = ? LIMIT 1');
            if ($st) {
                mysqli_stmt_bind_param($st, 'i', $evenementId);
                mysqli_stmt_execute($st);
                $aff = mysqli_stmt_affected_rows($st);
                mysqli_stmt_close($st);
                $_SESSION[$aff > 0 ? 'eve_flash_success' : 'eve_flash_error'] =
                    $aff > 0 ? 'Événement supprimé avec succès.' : 'Événement introuvable.';
            } else {
                $_SESSION['eve_flash_error'] = 'Suppression impossible pour le moment.';
            }
        }
        header('Location: ' . $redirect); exit;
    }

    // SAVE
    if ($action === 'save') {
        $titre        = trim((string)($_POST['titre']        ?? ''));
        $localisation = trim((string)($_POST['localisation'] ?? ''));
        $prixRaw      = trim((string)($_POST['prix']         ?? '0'));
        $capRaw       = trim((string)($_POST['capacite']     ?? '1'));
        $dateDebut    = trim((string)($_POST['date_debut']   ?? ''));
        $dateFin      = trim((string)($_POST['date_fin']     ?? ''));
        $description  = trim((string)($_POST['description']  ?? ''));
        $inclusArr    = (isset($_POST['inclus']) && is_array($_POST['inclus'])) ? $_POST['inclus'] : [];
        $statut       = trim((string)($_POST['statut']       ?? 'brouillon'));
        $regionIdRaw  = trim((string)($_POST['region_id']    ?? '0'));

        if ($titre === '' || $localisation === '') {
            $_SESSION['eve_flash_error'] = 'Titre et localisation sont obligatoires.';
            header('Location: ' . $redirect); exit;
        }
        if (!is_numeric($prixRaw)) {
            $_SESSION['eve_flash_error'] = 'Prix invalide.';
            header('Location: ' . $redirect); exit;
        }

        $prix      = (float)$prixRaw;
        $capacite  = max(1, (int)$capRaw);
        $ddVal     = $dateDebut !== '' ? $dateDebut : null;
        $dfVal     = $dateFin   !== '' ? $dateFin   : null;
        $inclusJs  = json_encode(array_values($inclusArr), JSON_UNESCAPED_UNICODE);
        if (!in_array($statut, ['brouillon','publié'], true)) $statut = 'brouillon';
        $regionId  = (int)$regionIdRaw;
        if ($regionId <= 0) $regionId = null;

        $photoP      = moveUploadedImage('photo_principale');
        $photosSec   = [];
        for ($i = 0; $i < 4; $i++) { $p = moveUploadedImage('photos_secondaires', $i); if ($p !== '') $photosSec[] = $p; }
        $photosSecJs = json_encode($photosSec, JSON_UNESCAPED_UNICODE);

        if ($evenementId > 0) {
            if ($photoP !== '') {
                // UPDATE with new photo
                $sql = 'UPDATE evenement SET titre=?,localisation=?,prix=?,capacite=?,date_debut=?,date_fin=?,description=?,inclus=?,photo_principale=?,photos_sec=?,statut=?,region_id=? WHERE id=? LIMIT 1';
                $st  = mysqli_prepare($conn, $sql);
                if ($st) {
                    mysqli_stmt_bind_param($st,'ssdisssssssii',$titre,$localisation,$prix,$capacite,$ddVal,$dfVal,$description,$inclusJs,$photoP,$photosSecJs,$statut,$regionId,$evenementId);
                    mysqli_stmt_execute($st); mysqli_stmt_close($st);
                    $_SESSION['eve_flash_success'] = 'Événement mis à jour.';
                } else { $_SESSION['eve_flash_error'] = 'Modification impossible.'; }
            } else {
                // UPDATE without photo
                $sql = 'UPDATE evenement SET titre=?,localisation=?,prix=?,capacite=?,date_debut=?,date_fin=?,description=?,inclus=?,statut=?,region_id=? WHERE id=? LIMIT 1';
                $st  = mysqli_prepare($conn, $sql);
                if ($st) {
                    mysqli_stmt_bind_param($st,'ssdisssssii',$titre,$localisation,$prix,$capacite,$ddVal,$dfVal,$description,$inclusJs,$statut,$regionId,$evenementId);
                    mysqli_stmt_execute($st); mysqli_stmt_close($st);
                    $_SESSION['eve_flash_success'] = 'Événement mis à jour.';
                } else { $_SESSION['eve_flash_error'] = 'Modification impossible.'; }
            }
        } else {
            $sql = 'INSERT INTO evenement (titre,localisation,prix,capacite,date_debut,date_fin,description,inclus,photo_principale,photos_sec,statut,region_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)';
            $st  = mysqli_prepare($conn, $sql);
            if ($st) {
                mysqli_stmt_bind_param($st,'ssdisssssssi',$titre,$localisation,$prix,$capacite,$ddVal,$dfVal,$description,$inclusJs,$photoP,$photosSecJs,$statut,$regionId);
                mysqli_stmt_execute($st); mysqli_stmt_close($st);
                $_SESSION['eve_flash_success'] = 'Événement ajouté avec succès.';
            } else { $_SESSION['eve_flash_error'] = 'Création impossible pour le moment.'; }
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
    $sql = 'SELECT id,titre,localisation,prix,capacite,date_debut,date_fin,description,inclus,photo_principale,photos_sec,statut,region_id FROM evenement WHERE id=? LIMIT 1';
    $st  = mysqli_prepare($conn, $sql);
    if ($st) {
        mysqli_stmt_bind_param($st,'i',$editId);
        mysqli_stmt_execute($st);
        mysqli_stmt_bind_result($st,$eid,$etit,$eloc,$eprix,$ecap,$edd,$edf,$edesc,$einc,$ephoto,$ephsec,$estat,$ereg);
        if (mysqli_stmt_fetch($st)) {
            $editItem = ['id'=>(int)$eid,'titre'=>$etit,'localisation'=>$eloc,'prix'=>(float)$eprix,
                         'capacite'=>(int)$ecap,'date_debut'=>$edd,'date_fin'=>$edf,'description'=>$edesc,
                         'inclus'=>$einc,'photo_principale'=>$ephoto,'photos_sec'=>$ephsec,'statut'=>$estat,'region_id'=>(int)$ereg];
        }
        mysqli_stmt_close($st);
    }
}

$where = ''; $like = '';
if ($search !== '') { $where = ' WHERE titre LIKE ? OR localisation LIKE ? OR statut LIKE ?'; $like = '%'.$search.'%'; }

$totalItems = 0;
$cst = mysqli_prepare($conn, 'SELECT COUNT(*) FROM evenement'.$where);
if ($cst) {
    if ($search !== '') mysqli_stmt_bind_param($cst,'sss',$like,$like,$like);
    mysqli_stmt_execute($cst);
    mysqli_stmt_bind_result($cst,$cdb);
    if (mysqli_stmt_fetch($cst)) $totalItems = (int)$cdb;
    mysqli_stmt_close($cst);
}

$totalPages = (int)ceil($totalItems / $perPage);
if ($totalPages > 0 && $page > $totalPages) { $page = $totalPages; $offset = ($page-1)*$perPage; }

$items = [];
$lst = mysqli_prepare($conn,'SELECT id,titre,localisation,prix,capacite,date_debut,date_fin,statut,photo_principale,region_id FROM evenement'.$where.' ORDER BY id DESC LIMIT ? OFFSET ?');
if ($lst) {
    if ($search !== '') mysqli_stmt_bind_param($lst,'sssii',$like,$like,$like,$perPage,$offset);
    else               mysqli_stmt_bind_param($lst,'ii',$perPage,$offset);
    mysqli_stmt_execute($lst);
    mysqli_stmt_bind_result($lst,$iid,$itit,$iloc,$iprix,$icap,$idd,$idf,$istat,$iphoto,$ireg);
    while (mysqli_stmt_fetch($lst)) {
        $items[] = ['id'=>(int)$iid,'titre'=>$itit,'localisation'=>$iloc,'prix'=>(float)$iprix,
                    'capacite'=>(int)$icap,'date_debut'=>$idd,'date_fin'=>$idf,'statut'=>$istat,'photo_principale'=>$iphoto,'region_id'=>$ireg];
    }
    mysqli_stmt_close($lst);
}

// ---------- Decode edit inclus ----------
$editInclus = [];
if ($editItem && !empty($editItem['inclus'])) {
    $dec = json_decode($editItem['inclus'], true);
    if (is_array($dec)) $editInclus = $dec;
}

$inclusOptions = ['Accueil personnalisé','Support 7j/7','Hôte local vérifié','Annulation flexible'];

$pageTitle   = 'Événements';
$pageHeading = 'Événements';
$activePage  = 'evenement';

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
        <div class="stat-label"><?php echo $editItem ? 'Modifier l\'événement' : 'Ajouter un événement'; ?></div>
        <form method="post" action="evenement.php?q=<?php echo urlencode($search); ?>&page=<?php echo (int)$page; ?>" enctype="multipart/form-data">
          <input type="hidden" name="action"       value="save">
          <input type="hidden" name="evenement_id" value="<?php echo $editItem ? (int)$editItem['id'] : 0; ?>">
          <input type="hidden" name="q"            value="<?php echo htmlspecialchars($search); ?>">
          <input type="hidden" name="page"         value="<?php echo (int)$page; ?>">

          <div class="form-grid">

            <!-- 1. Titre -->
            <div class="form-field">
              <label>Titre</label>
              <input type="text" name="titre" class="form-input"
                     value="<?php echo htmlspecialchars($editItem ? $editItem['titre'] : ''); ?>"
                     placeholder="Nom de l'événement" required>
            </div>

            <!-- 2. Localisation -->
            <div class="form-field">
              <label>Localisation</label>
              <input type="text" name="localisation" class="form-input"
                     value="<?php echo htmlspecialchars($editItem ? $editItem['localisation'] : ''); ?>"
                     placeholder="Région, ville..." required>
            </div>

            <!-- 2b. Région dropdown -->
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

            <!-- 3. Prix -->
            <div class="form-field">
              <label>Prix (TND)</label>
              <input type="number" step="0.01" min="0" name="prix" class="form-input"
                     value="<?php echo htmlspecialchars($editItem ? (string)$editItem['prix'] : '0'); ?>">
            </div>

            <!-- 4. Capacité -->
            <div class="form-field">
              <label>Capacité (personnes)</label>
              <input type="number" min="1" max="20" name="capacite" class="form-input"
                     value="<?php echo htmlspecialchars($editItem ? (string)$editItem['capacite'] : '1'); ?>">
            </div>

            <!-- 5. Date de début -->
            <div class="form-field">
              <label>Date de début</label>
              <input type="date" name="date_debut" class="form-input"
                     value="<?php echo htmlspecialchars($editItem ? (string)$editItem['date_debut'] : ''); ?>">
            </div>

            <!-- 6. Date de fin -->
            <div class="form-field">
              <label>Date de fin</label>
              <input type="date" name="date_fin" class="form-input"
                     value="<?php echo htmlspecialchars($editItem ? (string)$editItem['date_fin'] : ''); ?>">
            </div>

            <!-- 7. Description -->
            <div class="form-field" style="grid-column:1/-1;">
              <label>Description</label>
              <textarea name="description" class="form-textarea" placeholder="Description de l'événement..."><?php echo htmlspecialchars($editItem ? $editItem['description'] : ''); ?></textarea>
            </div>

            <!-- 8. Ce qui est inclus -->
            <div class="form-field" style="grid-column:1/-1;">
              <label>Ce qui est inclus</label>
              <div style="display:flex;flex-wrap:wrap;gap:14px;margin-top:6px;">
                <?php foreach ($inclusOptions as $opt): ?>
                  <label style="display:flex;align-items:center;gap:6px;font-size:14px;font-weight:400;text-transform:none;letter-spacing:0;color:#333;cursor:pointer;">
                    <input type="checkbox" name="inclus[]" value="<?php echo htmlspecialchars($opt); ?>"
                           <?php echo in_array($opt, $editInclus, true) ? 'checked' : ''; ?>
                           style="width:16px;height:16px;accent-color:var(--coral);">
                    <?php echo htmlspecialchars($opt); ?>
                  </label>
                <?php endforeach; ?>
              </div>
            </div>

            <!-- 9. Photo principale -->
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

            <!-- 10. Photos secondaires -->
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

            <!-- 11. Statut -->
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
              <?php echo $editItem ? 'Enregistrer les modifications' : 'Ajouter l\'événement'; ?>
            </button>
            <a href="evenement.php?q=<?php echo urlencode($search); ?>&page=<?php echo (int)$page; ?>" class="btn-small btn-soft">Annuler</a>
          </div>
        </form>
      </div><!-- /form-card -->
      <?php endif; ?>

      <!-- TOOLBAR -->
      <div class="toolbar">
        <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
            <a href="evenement.php?add=1" class="btn-small btn-coral">+ Ajouter un événement</a>
            <form method="get" action="evenement.php" class="search-form" style="margin:0;">
              <input type="text" name="q" class="search-input"
                     value="<?php echo htmlspecialchars($search); ?>"
                     placeholder="Rechercher titre, localisation, statut...">
              <button type="submit" class="btn-small btn-navy">Rechercher</button>
              <?php if ($search !== ''): ?>
                <a href="evenement.php" class="btn-small btn-soft">Réinitialiser</a>
              <?php endif; ?>
            </form>
        </div>
        <div class="muted">Total événements : <?php echo (int)$totalItems; ?></div>
      </div>

      <!-- CARDS preview -->
      <div class="popular-grid">
        <?php if (empty($items)): ?>
          <div class="mini-card" style="grid-column: 1/-1;">
            <h4>Aucun événement</h4>
            <p class="muted">Ajoutez votre premier événement.</p>
          </div>
        <?php else: ?>
          <?php foreach (array_slice($items, 0, 4) as $item): ?>
            <article class="pop-card">
              <div class="pop-card-img">
                <span class="pop-badge">Événement</span>
                <?php 
                  $img = $item['photo_principale'] ?: 'https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?auto=format&fit=crop&w=600&q=80';
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
                  <a href="evenement.php?edit=<?php echo $item['id']; ?>" class="pop-btn">
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
              <th>Capacité</th>
              <th>Disponibilités</th>
              <th>Statut</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($items)): ?>
              <tr><td colspan="8">Aucun événement trouvé.</td></tr>
            <?php else: ?>
              <?php foreach ($items as $item): ?>
                <tr>
                  <td><?php echo (int)$item['id']; ?></td>
                  <td><?php echo htmlspecialchars($item['titre']); ?></td>
                  <td><?php echo htmlspecialchars($item['localisation']); ?></td>
                  <td><?php echo number_format($item['prix'], 2, '.', ' '); ?></td>
                  <td><?php echo (int)$item['capacite']; ?></td>
                  <td style="white-space:nowrap;">
                    <?php echo $item['date_debut'] ? htmlspecialchars($item['date_debut']) : '—'; ?>
                    <?php echo ($item['date_debut'] && $item['date_fin']) ? ' → ' : ''; ?>
                    <?php echo $item['date_fin']   ? htmlspecialchars($item['date_fin'])   : ''; ?>
                  </td>
                  <td>
                    <span class="role-pill <?php echo ($item['statut'] === 'publié' || $item['statut'] === 'actif') ? 'admin' : ''; ?>">
                      <?php echo $item['statut'] === 'actif' ? 'Publié' : ucfirst(htmlspecialchars($item['statut'])); ?>
                    </span>
                  </td>
                  <td>
                    <div class="actions">
                      <a class="btn-small btn-soft"
                         href="evenement.php?edit=<?php echo (int)$item['id']; ?>&q=<?php echo urlencode($search); ?>&page=<?php echo (int)$page; ?>" title="Modifier"><i class="bi bi-pencil-square"></i></a>
                      <form method="post" action="evenement.php" class="inline-form"
                            onsubmit="return confirm('Supprimer cet événement ?');">
                        <input type="hidden" name="action"       value="delete">
                        <input type="hidden" name="evenement_id" value="<?php echo (int)$item['id']; ?>">
                        <input type="hidden" name="q"            value="<?php echo htmlspecialchars($search); ?>">
                        <input type="hidden" name="page"         value="<?php echo (int)$page; ?>">
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
               href="evenement.php?page=<?php echo (int)$p; ?>&q=<?php echo urlencode($search); ?>">
              <?php echo (int)$p; ?>
            </a>
          <?php endfor; ?>
        </div>
      <?php endif; ?>

    </div><!-- /page-body -->
  </div><!-- /content-wrap -->
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
