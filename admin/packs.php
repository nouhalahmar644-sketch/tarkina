<?php
require_once __DIR__ . '/includes/auth_admin.php';

// ---------- Auto-create tables ----------
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS packs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    slogan VARCHAR(500) NOT NULL DEFAULT '',
    region_id INT NOT NULL,
    image_path VARCHAR(500) NOT NULL DEFAULT '',
    prix_original DECIMAL(10,2) NOT NULL DEFAULT 0,
    prix_final DECIMAL(10,2) NOT NULL DEFAULT 0,
    statut VARCHAR(20) NOT NULL DEFAULT 'actif',
    position INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS pack_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pack_id INT NOT NULL,
    service_type VARCHAR(20) NOT NULL,
    service_id INT NOT NULL,
    INDEX idx_pack (pack_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

// ---------- Upload directory ----------
$uploadBase = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'packs';
if (!is_dir($uploadBase)) { @mkdir($uploadBase, 0755, true); }

// ---------- Flash ----------
$flashSuccess = '';
$flashError = '';
if (!empty($_SESSION['pack_flash_success'])) { $flashSuccess = (string) $_SESSION['pack_flash_success']; unset($_SESSION['pack_flash_success']); }
if (!empty($_SESSION['pack_flash_error']))   { $flashError   = (string) $_SESSION['pack_flash_error'];   unset($_SESSION['pack_flash_error']); }

function pack_upload_image(): string {
    global $uploadBase;
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) return '';
    $tmp = $_FILES['image']['tmp_name']; $orig = $_FILES['image']['name'];
    $allowed = ['image/jpeg','image/png','image/webp','image/gif'];
    if (!in_array(@mime_content_type($tmp), $allowed, true)) return '';
    if ($_FILES['image']['size'] > 6 * 1024 * 1024) return '';
    $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
    $file = uniqid('pack_', true) . '.' . $ext;
    if (!move_uploaded_file($tmp, $uploadBase . DIRECTORY_SEPARATOR . $file)) return '';
    return 'uploads/packs/' . $file;
}

$serviceTypes = ['hebergement','repas','guide','evenement','artisanat'];

// ---------- POST ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'delete') {
        $id = (int) ($_POST['pack_id'] ?? 0);
        if ($id > 0) {
            // remove image file
            $st = mysqli_prepare($conn, "SELECT image_path FROM packs WHERE id = ? LIMIT 1");
            mysqli_stmt_bind_param($st, 'i', $id);
            mysqli_stmt_execute($st);
            mysqli_stmt_bind_result($st, $imgp);
            $found = mysqli_stmt_fetch($st);
            mysqli_stmt_close($st);
            if ($found && $imgp && file_exists(__DIR__ . '/../' . $imgp)) { @unlink(__DIR__ . '/../' . $imgp); }
            mysqli_query($conn, "DELETE FROM pack_services WHERE pack_id = $id");
            $st = mysqli_prepare($conn, "DELETE FROM packs WHERE id = ?");
            mysqli_stmt_bind_param($st, 'i', $id);
            mysqli_stmt_execute($st);
            mysqli_stmt_close($st);
            $_SESSION['pack_flash_success'] = 'Pack supprimé.';
        }
        header('Location: packs.php'); exit;
    }

    if ($action === 'toggle_status') {
        $id = (int) ($_POST['pack_id'] ?? 0);
        if ($id > 0) {
            $st = mysqli_prepare($conn, "UPDATE packs SET statut = CASE WHEN statut = 'actif' THEN 'inactif' ELSE 'actif' END WHERE id = ?");
            mysqli_stmt_bind_param($st, 'i', $id);
            mysqli_stmt_execute($st);
            mysqli_stmt_close($st);
            $_SESSION['pack_flash_success'] = 'Statut mis à jour.';
        }
        header('Location: packs.php'); exit;
    }

    if ($action === 'save') {
        $editingId = isset($_POST['pack_id']) ? (int) $_POST['pack_id'] : 0;
        $titre = trim((string) ($_POST['titre'] ?? ''));
        $slogan = trim((string) ($_POST['slogan'] ?? ''));
        $regionId = (int) ($_POST['region_id'] ?? 0);
        $prixFinal = (float) ($_POST['prix_final'] ?? 0);
        $services = $_POST['services'] ?? []; // array of "type:id"
        $servicesClean = [];
        if (is_array($services)) {
            foreach ($services as $sv) {
                if (!is_string($sv) || strpos($sv, ':') === false) continue;
                [$t, $i] = explode(':', $sv, 2);
                if (in_array($t, $serviceTypes, true) && (int) $i > 0) {
                    $servicesClean[] = ['type' => $t, 'id' => (int) $i];
                }
            }
        }
        $nServices = count($servicesClean);
        $editRedirect = $editingId > 0 ? "packs.php?edit={$editingId}" : 'packs.php?add=1';
        if ($titre === '') { $_SESSION['pack_flash_error'] = 'Le titre est obligatoire.'; header('Location: ' . $editRedirect); exit; }
        if ($regionId <= 0) { $_SESSION['pack_flash_error'] = 'Veuillez choisir une région.'; header('Location: ' . $editRedirect); exit; }
        if ($nServices < 2 || $nServices > 5) { $_SESSION['pack_flash_error'] = 'Vous devez sélectionner entre 2 et 5 services.'; header('Location: ' . $editRedirect . '&region=' . $regionId); exit; }
        if ($prixFinal <= 0) { $_SESSION['pack_flash_error'] = 'Veuillez indiquer un prix final supérieur à 0.'; header('Location: ' . $editRedirect . '&region=' . $regionId); exit; }

        // Verify each selected service belongs to the chosen region and fetch prices
        $prixOriginal = 0.0;
        $okAll = true;
        foreach ($servicesClean as $sv) {
            $t = $sv['type']; $sid = $sv['id'];
            $sqlcheck = "SELECT prix FROM `$t` WHERE id = ? AND region_id = ? LIMIT 1";
            $st = mysqli_prepare($conn, $sqlcheck);
            if (!$st) { $okAll = false; break; }
            mysqli_stmt_bind_param($st, 'ii', $sid, $regionId);
            mysqli_stmt_execute($st);
            mysqli_stmt_bind_result($st, $prx);
            $f = mysqli_stmt_fetch($st);
            mysqli_stmt_close($st);
            if (!$f) { $okAll = false; break; }
            $prixOriginal += (float) $prx;
        }
        if (!$okAll) { $_SESSION['pack_flash_error'] = 'Un ou plusieurs services sélectionnés ne sont pas valides.'; header('Location: ' . $editRedirect . '&region=' . $regionId); exit; }

        $imgPath = pack_upload_image();
        if ($imgPath === '') {
            // No upload — try to use the first service photo as fallback (read from its table)
            $first = $servicesClean[0];
            $st = mysqli_prepare($conn, "SELECT photo_principale FROM `{$first['type']}` WHERE id = ? LIMIT 1");
            mysqli_stmt_bind_param($st, 'i', $first['id']);
            mysqli_stmt_execute($st);
            mysqli_stmt_bind_result($st, $pp);
            if (mysqli_stmt_fetch($st)) { $imgPath = $pp ?: ''; }
            mysqli_stmt_close($st);
        }

        if ($editingId > 0) {
            // UPDATE existing pack
            if ($imgPath !== '') {
                $st = mysqli_prepare($conn, "UPDATE packs SET titre = ?, slogan = ?, region_id = ?, image_path = ?, prix_original = ?, prix_final = ? WHERE id = ?");
                mysqli_stmt_bind_param($st, 'ssisddi', $titre, $slogan, $regionId, $imgPath, $prixOriginal, $prixFinal, $editingId);
            } else {
                $st = mysqli_prepare($conn, "UPDATE packs SET titre = ?, slogan = ?, region_id = ?, prix_original = ?, prix_final = ? WHERE id = ?");
                mysqli_stmt_bind_param($st, 'ssiddi', $titre, $slogan, $regionId, $prixOriginal, $prixFinal, $editingId);
            }
            mysqli_stmt_execute($st);
            mysqli_stmt_close($st);
            $packId = $editingId;

            // Refresh service links
            mysqli_query($conn, "DELETE FROM pack_services WHERE pack_id = $packId");
            $_SESSION['pack_flash_success'] = 'Pack mis à jour avec succès.';
        } else {
            // INSERT new pack
            $st = mysqli_prepare($conn, "INSERT INTO packs (titre, slogan, region_id, image_path, prix_original, prix_final) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($st, 'ssisdd', $titre, $slogan, $regionId, $imgPath, $prixOriginal, $prixFinal);
            mysqli_stmt_execute($st);
            $packId = mysqli_insert_id($conn);
            mysqli_stmt_close($st);
            $_SESSION['pack_flash_success'] = 'Pack créé avec succès.';
        }

        // (Re)insert pack→service links
        $linkSt = mysqli_prepare($conn, "INSERT INTO pack_services (pack_id, service_type, service_id) VALUES (?, ?, ?)");
        foreach ($servicesClean as $sv) {
            mysqli_stmt_bind_param($linkSt, 'isi', $packId, $sv['type'], $sv['id']);
            mysqli_stmt_execute($linkSt);
        }
        mysqli_stmt_close($linkSt);

        header('Location: packs.php'); exit;
    }
}

// ---------- Fetch regions ----------
$regions = [];
$rqr = mysqli_query($conn, "SELECT id, nom FROM region ORDER BY nom ASC");
while ($rqr && ($r = mysqli_fetch_assoc($rqr))) { $regions[] = $r; }

// ---------- Fetch ALL services (used by the form to pick from) ----------
$servicesByRegion = []; // region_id => [ ['type','id','titre','prix'], ... ]
foreach ($serviceTypes as $t) {
    $rs = mysqli_query($conn, "SELECT id, titre, prix, region_id FROM `$t` WHERE statut IN ('actif','publié') ORDER BY region_id, titre");
    while ($rs && ($s = mysqli_fetch_assoc($rs))) {
        $rid = (int) $s['region_id'];
        if ($rid <= 0) continue;
        $s['type'] = $t;
        $servicesByRegion[$rid][] = $s;
    }
}

// ---------- Fetch existing packs (with their services for display) ----------
$packs = [];
$pres = mysqli_query($conn, "SELECT p.*, r.nom AS region_nom FROM packs p LEFT JOIN region r ON p.region_id = r.id ORDER BY p.position ASC, p.id DESC");
while ($pres && ($p = mysqli_fetch_assoc($pres))) {
    $p['services'] = [];
    $packs[(int) $p['id']] = $p;
}
if (!empty($packs)) {
    $packIds = array_keys($packs);
    $sql = "SELECT pack_id, service_type, service_id FROM pack_services WHERE pack_id IN (" . implode(',', array_map('intval', $packIds)) . ")";
    $rsv = mysqli_query($conn, $sql);
    while ($rsv && ($row = mysqli_fetch_assoc($rsv))) {
        $pid = (int) $row['pack_id'];
        // Fetch service title (best effort)
        $t = $row['service_type']; $sid = (int) $row['service_id'];
        if (in_array($t, $serviceTypes, true)) {
            $stt = mysqli_prepare($conn, "SELECT titre FROM `$t` WHERE id = ? LIMIT 1");
            mysqli_stmt_bind_param($stt, 'i', $sid);
            mysqli_stmt_execute($stt);
            mysqli_stmt_bind_result($stt, $tttitre);
            if (mysqli_stmt_fetch($stt)) {
                $packs[$pid]['services'][] = ['type' => $t, 'id' => $sid, 'titre' => $tttitre];
            } else {
                $packs[$pid]['services'][] = ['type' => $t, 'id' => $sid, 'titre' => '(service supprimé)'];
            }
            mysqli_stmt_close($stt);
        }
    }
}

$editId        = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$editPack      = $editId > 0 && isset($packs[$editId]) ? $packs[$editId] : null;
$showAdd       = isset($_GET['add']) || $editPack !== null;
$preRegion     = $editPack ? (int) $editPack['region_id'] : (isset($_GET['region']) ? (int) $_GET['region'] : 0);
$editServiceIds = [];
if ($editPack) {
    foreach ($editPack['services'] as $sv) {
        $editServiceIds[] = $sv['type'] . ':' . $sv['id'];
    }
}

// ── Pack stats ──
$packsTotal = count($packs);
$packsActifs = 0; $packsInactifs = 0; $packsCA = 0.0;
foreach ($packs as $pk) {
    if ($pk['statut'] === 'actif') $packsActifs++; else $packsInactifs++;
    $packsCA += (float)$pk['prix_final'];
}

$pageTitle     = 'Packs';
$pageHeading   = "Packs « Partez l'esprit tranquille »";
$activePage    = 'packs';

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

      <?php if ($flashSuccess !== ''): ?><div class="flash success"><?= htmlspecialchars($flashSuccess) ?></div><?php endif; ?>
      <?php if ($flashError !== ''): ?><div class="flash error"><?= htmlspecialchars($flashError) ?></div><?php endif; ?>

      <?php if (!$showAdd): ?>
      <!-- ── Mini stats (list view only) ── -->
      <style>
        .mini-stat-row { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:28px; }
        @media(max-width:900px){ .mini-stat-row { grid-template-columns:repeat(2,1fr); } }
        .mini-stat-card { background:var(--white); border-radius:16px; border:1px solid var(--border); padding:20px 18px 16px; box-shadow:0 4px 14px rgba(0,0,0,0.04); transition:transform .2s,box-shadow .2s; }
        .mini-stat-card:hover { transform:translateY(-3px); box-shadow:0 8px 22px rgba(0,0,0,0.08); }
        .msi { width:34px; height:34px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:17px; margin-bottom:12px; }
        .msi.navy  { background:#eef0f6; color:var(--navy); }
        .msi.orange{ background:#fff4eb; color:var(--coral); }
        .msi.green { background:#eef6ee; color:#2e7d32; }
        .msi.red   { background:#fdecea; color:#c0392b; }
        .msl { font-size:10px; font-weight:800; letter-spacing:1.5px; text-transform:uppercase; color:#8892a4; margin-bottom:5px; }
        .msv { font-size:26px; font-weight:800; color:var(--navy); line-height:1; margin-bottom:10px; }
        .msd { border:none; border-top:1px solid var(--border); margin:0 0 8px; }
        .mss { font-size:11px; color:#8892a4; line-height:1.5; }
        .mss.pos { color:#2e7d32; } .mss.neg { color:#c0392b; }
      </style>
      <div class="mini-stat-row">
        <div class="mini-stat-card">
          <div class="msi navy"><i class="bi bi-box-seam"></i></div>
          <div class="msl">TOTAL PACKS</div>
          <div class="msv"><?= $packsTotal ?></div>
          <hr class="msd">
          <div class="mss"><?= $packsTotal ?> pack(s) créé(s)</div>
        </div>
        <div class="mini-stat-card">
          <div class="msi orange"><i class="bi bi-eye"></i></div>
          <div class="msl">ACTIFS</div>
          <div class="msv"><?= $packsActifs ?></div>
          <hr class="msd">
          <div class="mss pos"><?= $packsTotal > 0 ? round($packsActifs / $packsTotal * 100) : 0 ?>% publiés</div>
        </div>
        <div class="mini-stat-card">
          <div class="msi red"><i class="bi bi-eye-slash"></i></div>
          <div class="msl">INACTIFS</div>
          <div class="msv"><?= $packsInactifs ?></div>
          <hr class="msd">
          <div class="mss neg">Non publiés</div>
        </div>
        <div class="mini-stat-card">
          <div class="msi green"><i class="bi bi-cash-coin"></i></div>
          <div class="msl">VALEUR CATALOGUE</div>
          <div class="msv" style="font-size:18px;"><?= number_format($packsCA, 2, '.', ' ') ?> TND</div>
          <hr class="msd">
          <div class="mss pos">Prix finaux cumulés</div>
        </div>
      </div>
      <?php endif; ?>

      <?php if ($showAdd): ?>
        <!-- ======= ADD / EDIT FORM ======= -->
        <div class="form-card">
          <div class="stat-label"><?= $editPack ? 'Modifier le pack' : 'Créer un nouveau pack' ?></div>
          <p class="muted" style="font-size: 13px; margin: 8px 0 16px;">
            Choisissez une région, sélectionnez <strong>2 à 5 services</strong> de cette région, et fixez le prix final du pack. Le prix « avant » est calculé automatiquement à partir des services sélectionnés.
          </p>
          <form method="post" action="packs.php" enctype="multipart/form-data" id="packForm">
            <input type="hidden" name="action" value="save">
            <?php if ($editPack): ?>
              <input type="hidden" name="pack_id" value="<?= (int) $editPack['id'] ?>">
            <?php endif; ?>
            <div class="form-grid">

              <div class="form-field">
                <label>Titre du pack</label>
                <input type="text" name="titre" class="form-input" placeholder="Ex : Escapade Saharienne" required maxlength="255" value="<?= htmlspecialchars($editPack['titre'] ?? '') ?>">
              </div>

              <div class="form-field">
                <label>Région</label>
                <select name="region_id" id="regionSelect" class="form-select" required>
                  <option value="">— Choisir une région —</option>
                  <?php foreach ($regions as $r): ?>
                    <option value="<?= (int) $r['id'] ?>" <?= $preRegion === (int) $r['id'] ? 'selected' : '' ?>><?= htmlspecialchars($r['nom']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="form-field" style="grid-column: 1 / -1;">
                <label>Slogan / description courte</label>
                <input type="text" name="slogan" class="form-input" placeholder="Ex : 4 jours dans le désert, hébergement, guide et repas inclus" maxlength="500" value="<?= htmlspecialchars($editPack['slogan'] ?? '') ?>">
              </div>

              <div class="form-field" style="grid-column: 1 / -1;">
                <label>Image principale (JPG / PNG / WebP, max 6 Mo) — <?= $editPack && $editPack['image_path'] ? 'laisser vide pour conserver l\'image actuelle' : 'si vide, l\'image du premier service sera utilisée' ?></label>
                <?php if ($editPack && !empty($editPack['image_path'])): ?>
                  <p style="font-size: 12px; color: #666; margin-bottom: 6px;">Image actuelle : <a href="../<?= htmlspecialchars($editPack['image_path']) ?>" target="_blank">voir</a></p>
                <?php endif; ?>
                <div class="custom-file-wrap">
                  <input type="file" id="image" name="image" accept="image/*" onchange="this.parentElement.querySelector('.custom-file-name').textContent = this.files[0] ? this.files[0].name : 'Aucun fichier choisi'">
                  <label for="image" class="custom-file-btn">Choisir un fichier</label>
                  <span class="custom-file-name">Aucun fichier choisi</span>
                </div>
              </div>

              <!-- Services picker -->
              <div class="form-field" style="grid-column: 1 / -1;">
                <label>Services inclus (2 à 5)</label>
                <div id="svcMsg" class="muted" style="font-size: 13px; margin-bottom: 10px;">Choisissez d'abord une région ci-dessus.</div>
                <div id="svcGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 10px; max-height: 380px; overflow-y: auto; padding: 4px;"></div>
              </div>

              <div class="form-field">
                <label>Prix total des services sélectionnés (calculé)</label>
                <input type="text" id="prixOriginalDisplay" class="form-input" value="0 TND" readonly style="background: #f5f3ee; font-weight: 700;">
              </div>

              <div class="form-field">
                <label>Prix final du pack (TND)</label>
                <input type="number" name="prix_final" id="prixFinalInput" class="form-input" placeholder="Ex : 350" min="1" step="0.01" required value="<?= $editPack ? htmlspecialchars((string) $editPack['prix_final']) : '' ?>">
              </div>

              <div class="form-field" style="grid-column: 1 / -1;">
                <div id="econDisplay" class="muted" style="font-size: 14px;">Économie pour le client : —</div>
              </div>

            </div>

            <div class="actions" style="margin-top: 14px;">
              <button type="submit" id="saveBtn" class="btn-small btn-coral"><?= $editPack ? 'Enregistrer les modifications' : 'Créer le pack' ?></button>
              <a href="packs.php" class="btn-small btn-soft">Annuler</a>
            </div>
          </form>
        </div>

        <!-- All services payload for JS filtering -->
        <script>
          const ALL_SERVICES = <?= json_encode($servicesByRegion, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_QUOT) ?>;
          const PRESELECTED  = <?= json_encode($editServiceIds, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_QUOT) ?>;
          const LABELS = {
            hebergement: '🏠 Hébergement', repas: '🍽️ Repas', guide: '🧭 Guide', evenement: '🎉 Événement', artisanat: '💎 Artisanat'
          };
          const sel    = document.getElementById('regionSelect');
          const grid   = document.getElementById('svcGrid');
          const msg    = document.getElementById('svcMsg');
          const prixO  = document.getElementById('prixOriginalDisplay');
          const prixF  = document.getElementById('prixFinalInput');
          const econ   = document.getElementById('econDisplay');
          const saveBtn= document.getElementById('saveBtn');

          function renderServices() {
            const rid = parseInt(sel.value, 10) || 0;
            grid.innerHTML = '';
            if (!rid) { msg.textContent = "Choisissez d'abord une région ci-dessus."; updateCalc(); return; }
            const list = ALL_SERVICES[rid] || [];
            if (list.length === 0) { msg.textContent = 'Aucun service publié dans cette région pour le moment.'; updateCalc(); return; }
            msg.innerHTML = 'Cochez entre <strong>2 et 5</strong> services à inclure dans le pack. <span id="countLive">0 sélectionné(s)</span>';
            list.forEach(s => {
              const id = 'svc_' + s.type + '_' + s.id;
              const valKey = s.type + ':' + s.id;
              const checked = PRESELECTED.indexOf(valKey) !== -1 ? 'checked' : '';
              const wrap = document.createElement('label');
              wrap.style.cssText = 'display:flex; gap:10px; align-items:flex-start; padding:10px 12px; border:1.5px solid var(--border); border-radius:10px; cursor:pointer; background:#fff;';
              wrap.innerHTML = `
                <input type="checkbox" class="svc-cb" name="services[]" value="${valKey}" data-price="${s.prix}" id="${id}" ${checked} style="margin-top:3px;">
                <div style="flex:1;">
                  <div style="font-weight:700; font-size:13px; color:var(--navy);">${LABELS[s.type] || s.type}</div>
                  <div style="font-size:13px; color:#333; margin: 2px 0;">${escapeHtml(s.titre)}</div>
                  <div style="font-size:12px; color:var(--coral); font-weight:700;">${parseFloat(s.prix).toFixed(0)} TND</div>
                </div>`;
              grid.appendChild(wrap);
            });
            grid.querySelectorAll('.svc-cb').forEach(cb => cb.addEventListener('change', enforceLimit));
            updateCalc();
          }

          function escapeHtml(s) { return String(s).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }

          function enforceLimit(e) {
            const checked = grid.querySelectorAll('.svc-cb:checked');
            if (checked.length > 5) {
              e.target.checked = false;
              alert('Vous ne pouvez sélectionner que 5 services au maximum.');
            }
            updateCalc();
          }

          function updateCalc() {
            const checked = grid.querySelectorAll('.svc-cb:checked');
            const n = checked.length;
            const total = [...checked].reduce((acc, cb) => acc + parseFloat(cb.dataset.price || 0), 0);
            prixO.value = total.toFixed(0) + ' TND';
            const countEl = document.getElementById('countLive');
            if (countEl) countEl.textContent = n + ' sélectionné(s)';
            const final = parseFloat(prixF.value) || 0;
            if (final > 0 && total > final) {
              econ.innerHTML = 'Économie pour le client : <strong style="color:#2e7d32;">' + (total - final).toFixed(0) + ' TND</strong> (-' + Math.round(((total - final) / total) * 100) + '%)';
            } else if (final > 0 && total > 0) {
              econ.innerHTML = '<span style="color:#b43737;">Le prix final est supérieur ou égal au prix total. Vérifiez la valeur.</span>';
            } else {
              econ.textContent = 'Économie pour le client : —';
            }
            // Visual hint only — keep button enabled so click always submits; server validates strictly.
            if (n >= 2 && n <= 5 && final > 0 && sel.value) {
              saveBtn.style.opacity = '1'; saveBtn.title = '';
            } else {
              saveBtn.style.opacity = '0.7'; saveBtn.title = 'Choisissez une région, 2-5 services, et un prix final.';
            }
          }

          sel.addEventListener('change', renderServices);
          prixF.addEventListener('input', updateCalc);
          renderServices();
        </script>
      <?php else: ?>
        <!-- ======= LIST ======= -->
        <div class="toolbar">
          <div style="display:flex; gap:12px; align-items:center;">
            <a href="packs.php?add=1" class="btn-small btn-coral">+ Créer un pack</a>
          </div>
          <div class="muted">Total : <strong><?= count($packs) ?></strong> pack(s)</div>
        </div>

        <?php if (empty($packs)): ?>
          <div class="mini-card" style="text-align:center; padding: 50px;">
            <h4>Aucun pack pour le moment</h4>
            <p class="muted">Tant qu'il n'y a pas de pack actif, la page d'accueil affiche les forfaits par défaut. Créez votre premier pack avec le bouton ci-dessus.</p>
          </div>
        <?php else: ?>
          <div class="popular-grid">
            <?php foreach ($packs as $p):
              $img = $p['image_path'] ?: '';
              if ($img && strpos($img, 'http') !== 0 && strpos($img, 'uploads/') !== 0 && strpos($img, 'images/') !== 0) { $img = '../' . $img; }
              elseif ($img && strpos($img, 'uploads/') === 0) { $img = '../' . $img; }
              if (!$img) { $img = 'https://images.unsplash.com/photo-1509316785289-025f5b846b35?w=600'; }
              $econ = max(0, (float) $p['prix_original'] - (float) $p['prix_final']);
              $econPct = ((float) $p['prix_original'] > 0) ? round(($econ / (float) $p['prix_original']) * 100) : 0;
            ?>
              <article class="pop-card" style="<?= $p['statut'] === 'inactif' ? 'opacity:.55;' : '' ?>">
                <div class="pop-card-img" style="height: 170px;">
                  <span class="pop-badge" style="background: <?= $p['statut'] === 'actif' ? 'var(--coral)' : '#888' ?>;"><?= htmlspecialchars($p['statut']) ?></span>
                  <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($p['titre']) ?>" onerror="this.style.background='#f0f0f0'">
                </div>
                <div class="pop-card-body">
                  <div class="pop-card-title" style="margin-bottom: 4px;"><?= htmlspecialchars($p['titre']) ?></div>
                  <div class="muted" style="font-size: 12px; margin-bottom: 8px;">📍 <?= htmlspecialchars($p['region_nom'] ?: '—') ?></div>
                  <?php if (!empty($p['slogan'])): ?>
                    <div class="muted" style="font-size: 12px; margin-bottom: 8px;"><?= htmlspecialchars($p['slogan']) ?></div>
                  <?php endif; ?>
                  <div style="display:flex; gap:10px; align-items:baseline; margin: 6px 0 10px;">
                    <span style="font-size: 18px; font-weight: 800; color: var(--coral);"><?= number_format((float) $p['prix_final'], 0) ?> TND</span>
                    <?php if ((float) $p['prix_original'] > (float) $p['prix_final']): ?>
                      <span style="font-size: 13px; color: #999; text-decoration: line-through;"><?= number_format((float) $p['prix_original'], 0) ?> TND</span>
                      <span style="font-size: 11px; font-weight:700; color: #2e7d32; background: #e8f5e9; padding: 2px 6px; border-radius: 4px;">-<?= $econPct ?>%</span>
                    <?php endif; ?>
                  </div>
                  <div style="display:flex; flex-wrap:wrap; gap:4px; margin-bottom: 10px;">
                    <?php foreach ($p['services'] as $sv): ?>
                      <span style="font-size: 11px; background: #f1eee9; padding: 3px 8px; border-radius: 10px; color: #333;" title="<?= htmlspecialchars($sv['titre']) ?>">
                        <?= ['hebergement'=>'🏠','repas'=>'🍽️','guide'=>'🧭','evenement'=>'🎉','artisanat'=>'💎'][$sv['type']] ?? '·' ?>
                        <?= htmlspecialchars(mb_substr($sv['titre'], 0, 22)) . (mb_strlen($sv['titre']) > 22 ? '…' : '') ?>
                      </span>
                    <?php endforeach; ?>
                  </div>
                  <div class="actions pack-actions">
                    <a href="packs.php?edit=<?= (int) $p['id'] ?>" class="btn-small btn-coral" title="Modifier"><i class="bi bi-pencil-square"></i> Modifier</a>
                    <a href="../forfait.php?id=<?= (int) $p['id'] ?>" target="_blank" rel="noopener" class="btn-small btn-soft" title="Voir sur le site"><i class="bi bi-box-arrow-up-right"></i></a>
                    <form method="post" action="packs.php" class="inline-form">
                      <input type="hidden" name="action" value="toggle_status">
                      <input type="hidden" name="pack_id" value="<?= (int) $p['id'] ?>">
                      <button type="submit" class="btn-small btn-navy" title="Activer / désactiver"><i class="bi <?= $p['statut'] === 'actif' ? 'bi-eye-slash' : 'bi-eye' ?>"></i></button>
                    </form>
                    <form method="post" action="packs.php" class="inline-form" onsubmit="return confirm('Supprimer ce pack ?');">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="pack_id" value="<?= (int) $p['id'] ?>">
                      <button type="submit" class="btn-small btn-soft" title="Supprimer" style="color:#b43737;"><i class="bi bi-trash"></i></button>
                    </form>
                  </div>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      <?php endif; ?>

    </div>
  </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
