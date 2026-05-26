<?php
session_start();
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/db.php';

$user_id = $_SESSION['user_id'];
$success_msg = '';
$error_msg = '';

// Handle cancel action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel') {
    $res_id = isset($_POST['reservation_id']) ? (int)$_POST['reservation_id'] : 0;
    if ($res_id > 0) {
        // Only allow canceling if 'en_attente'
        $res_check = mysqli_query($conn, "SELECT statut FROM reservations WHERE id = $res_id AND user_id = $user_id LIMIT 1");
        $row_check = $res_check ? mysqli_fetch_assoc($res_check) : null;

        if ($row_check && in_array($row_check['statut'], ['en_attente', 'confirmé', 'confirmée', 'confirmee'], true)) {
            if (mysqli_query($conn, "UPDATE reservations SET statut = 'annulée' WHERE id = $res_id")) {
                $success_msg = 'La réservation a été annulée avec succès.';
            } else {
                $error_msg = 'Erreur lors de l\'annulation de la réservation.';
            }
        } else {
            $error_msg = 'Cette réservation ne peut pas être annulée.';
        }
    }
}

// Fetch all reservations for the user
$sql = "
    SELECT r.*,
           h.titre AS hebergement_titre,
           rp.titre AS repas_titre,
           g.titre AS guide_titre,
           e.titre AS evenement_titre,
           a.titre AS artisanat_titre
    FROM reservations r
    LEFT JOIN hebergement h ON r.type_service = 'hebergement' AND r.service_id = h.id
    LEFT JOIN repas rp ON r.type_service = 'repas' AND r.service_id = rp.id
    LEFT JOIN guide g ON r.type_service = 'guide' AND r.service_id = g.id
    LEFT JOIN evenement e ON r.type_service = 'evenement' AND r.service_id = e.id
    LEFT JOIN artisanat a ON r.type_service = 'artisanat' AND r.service_id = a.id
    WHERE r.user_id = $user_id
    ORDER BY r.created_at DESC
";
$result = mysqli_query($conn, $sql);
$reservations = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $reservations[] = $row;
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mes Réservations – Tarkina</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Lato:wght@400;600;700&display=swap" rel="stylesheet" />
  <!-- Bootstrap 5.3 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --cream: #f5f2ee;
      --dark: #1c1c2e;
      --navy: #1B3A4B;
      --orange: #e8642c;
      --coral: #E05A2B;
      --white: #ffffff;
      --border: #e0dbd4;
      --radius: 14px;
      --green: #2ecc71;
      --red: #e74c3c;
      --muted: #6b6b6b;
    }
    body { font-family: 'Lato', sans-serif; background: var(--cream); color: var(--dark); font-size: 15px; line-height: 1.7; display: flex; flex-direction: column; min-height: 100vh; }
 
    nav { background: var(--white); border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; padding: 0 56px; height: 60px; }
    .nav-logo { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 800; color: var(--dark); text-decoration: none; display: flex; align-items: center; gap: 4px; }
    .nav-logo span { color: var(--orange); }
    .nav-links { display: flex; gap: 32px; list-style: none; }
    .nav-links a { text-decoration: none; color: var(--dark); font-size: 14px; font-weight: 600; opacity: .7; transition: opacity .2s; }
    .nav-links a:hover { opacity: 1; }
    .nav-actions { display: flex; align-items: center; gap: 16px; }
    .btn-nav { background: var(--orange); color: var(--white); border: none; border-radius: 8px; padding: 9px 22px; font-size: 14px; font-weight: 700; cursor: pointer; text-decoration:none; }

    .main-container { flex: 1; padding: 48px 56px; max-width: 1200px; margin: 0 auto; width: 100%; }
    
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; }
    .page-title { font-family: 'Playfair Display', serif; font-size: 36px; font-weight: 800; color: var(--dark); }
    
    .alert { padding: 16px; border-radius: 8px; margin-bottom: 24px; font-weight: 600; }
    .alert-success { background: rgba(46, 204, 113, 0.1); color: var(--green); border: 1px solid rgba(46, 204, 113, 0.2); }
    .alert-error { background: rgba(231, 76, 60, 0.1); color: var(--red); border: 1px solid rgba(231, 76, 60, 0.2); }

    .reservations-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 24px; }
    
    .res-card { background: var(--white); border-radius: var(--radius); padding: 24px; border: 1px solid var(--border); display: flex; flex-direction: column; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
    
    .res-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid var(--border); }
    .res-type { font-size: 11px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: var(--muted); margin-bottom: 4px; }
    .res-title { font-family: 'Playfair Display', serif; font-size: 20px; font-weight: 800; color: var(--dark); line-height: 1.2; }
    
    .badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; }
    .badge-en_attente { background: rgba(232, 100, 44, 0.1); color: var(--orange); }
    .badge-confirmee { background: rgba(46, 204, 113, 0.1); color: var(--green); }
    .badge-annulee { background: rgba(231, 76, 60, 0.1); color: var(--red); }
    
    .res-details { flex: 1; margin-bottom: 24px; }
    .detail-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px; }
    .detail-label { color: var(--muted); }
    .detail-value { font-weight: 600; color: var(--dark); }
    .res-total { font-size: 18px; font-weight: 800; margin-top: 16px; padding-top: 16px; border-top: 1px dashed var(--border); display: flex; justify-content: space-between; }
    
    .res-actions { display: flex; gap: 12px; }
    .btn { flex: 1; padding: 10px; text-align: center; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; text-decoration: none; transition: background 0.2s, opacity 0.2s; border: none; }
    .btn-outline { background: transparent; border: 1px solid var(--border); color: var(--dark); }
    .btn-outline:hover { background: var(--cream); }
    .btn-danger { background: rgba(231, 76, 60, 0.1); color: var(--red); }
    .btn-danger:hover { background: rgba(231, 76, 60, 0.2); }
    
    .empty-state { text-align: center; padding: 64px 20px; background: var(--white); border-radius: var(--radius); border: 1px solid var(--border); }
    .empty-state h3 { font-family: 'Playfair Display', serif; font-size: 24px; margin-bottom: 12px; }
    
    footer { background: var(--navy); color: var(--white); padding: 48px 56px; text-align: center; margin-top: auto; }

    /* ── Confirmation modal ──────────────────────────────────── */
    #cancelModal .modal-content {
      border-radius: 16px;
      border: none;
      box-shadow: 0 20px 60px rgba(0,0,0,0.18);
    }
    #cancelModal .modal-header {
      border-bottom: none;
      padding-bottom: 0;
    }
    #cancelModal .modal-title {
      font-family: 'Playfair Display', serif;
      font-size: 20px;
      font-weight: 800;
      color: var(--navy);
    }
    #cancelModal .modal-body {
      font-size: 15px;
      color: #444;
      padding: 12px 24px 20px;
      line-height: 1.6;
    }
    #cancelModal .modal-footer {
      border-top: none;
      padding-top: 0;
      gap: 10px;
      padding: 0 24px 24px;
    }
    #btnConfirmCancel {
      background: #E05A2B;
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 10px 22px;
      font-weight: 700;
      font-size: 14px;
      transition: background .2s;
      flex: 1;
    }
    #btnConfirmCancel:hover { background: #c44e22; }
    #btnKeepReservation {
      background: #1B3A4B;
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 10px 22px;
      font-weight: 700;
      font-size: 14px;
      transition: background .2s;
      flex: 1;
    }
    #btnKeepReservation:hover { background: #132b39; }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="main-container">
  <div class="page-header">
    <h1 class="page-title">Mes Réservations</h1>
  </div>

  <?php if ($success_msg): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success_msg) ?></div>
  <?php endif; ?>
  <?php if ($error_msg): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error_msg) ?></div>
  <?php endif; ?>

  <?php if (empty($reservations)): ?>
    <div class="empty-state">
      <h3>Aucune réservation</h3>
      <p style="color:var(--muted); margin-bottom:24px;">Vous n'avez pas encore effectué de réservation.</p>
      <a href="explorer.php" class="btn-nav">Explorer les offres</a>
    </div>
  <?php else: ?>
    <div class="reservations-grid">
      <?php foreach ($reservations as $r): 
        $type = '';
        $titre = '';
        $prix = 0;
        $url = '';
        
        if ($r['type_service'] === 'hebergement') {
            $type = 'Hébergement';
            $titre = $r['hebergement_titre'];
        } elseif ($r['type_service'] === 'repas') {
            $type = 'Gastronomie';
            $titre = $r['repas_titre'];
        } elseif ($r['type_service'] === 'guide') {
            $type = 'Guide Local';
            $titre = $r['guide_titre'];
        } elseif ($r['type_service'] === 'evenement') {
            $type = 'Événement';
            $titre = $r['evenement_titre'];
        } elseif ($r['type_service'] === 'artisanat') {
            $type = 'Artisanat';
            $titre = $r['artisanat_titre'];
        }
        $url = $r['type_service'] . '.php?id=' . $r['service_id'];
        
        $total = (float)$r['prix_total'];
        
        if ($r['type_service'] === 'hebergement') {
            $dates_str = date('d/m/Y', strtotime($r['date_debut'])) . ' au ' . date('d/m/Y', strtotime($r['date_fin']));
        } else {
            $dates_str = date('d/m/Y', strtotime($r['date_debut']));
        }
        
        $statut_class = '';
        $statut_label = '';
        switch ($r['statut']) {
            case 'en_attente':
                $statut_class = 'badge-en_attente';
                $statut_label = 'En attente';
                break;
            case 'confirmée':
            case 'confirmee':
                $statut_class = 'badge-confirmee';
                $statut_label = 'Confirmée';
                break;
            case 'annulée':
            case 'annulee':
                $statut_class = 'badge-annulee';
                $statut_label = 'Annulée';
                break;
            case 'terminé':
            case 'terminée':
            case 'termine':
                $statut_class = 'badge-confirmee';
                $statut_label = 'Terminée';
                break;
            default:
                $statut_class = 'badge-en_attente';
                $statut_label = htmlspecialchars($r['statut']);
        }
      ?>
      <div class="res-card">
        <div class="res-header">
            <div>
                <div class="res-type"><?= htmlspecialchars($type) ?></div>
                <div class="res-title"><?= htmlspecialchars($titre) ?></div>
            </div>
            <div class="badge <?= $statut_class ?>"><?= $statut_label ?></div>
        </div>
        
        <div class="res-details">
            <div class="detail-row">
                <span class="detail-label">N° de réservation</span>
                <span class="detail-value">#<?= $r['id'] ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date de la demande</span>
                <span class="detail-value"><?= date('d/m/Y', strtotime($r['created_at'])) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label"><?= $type === 'Hébergement' ? 'Dates du séjour' : 'Date prévue' ?></span>
                <span class="detail-value"><?= $dates_str ?></span>
            </div>
            
            <div class="res-total">
                <span class="detail-label">Total estimé</span>
                <span style="color:var(--orange);"><?= number_format($total, 2, '.', ' ') ?> TND</span>
            </div>
        </div>
        
        <div class="res-actions">
            <a href="<?= $url ?>" class="btn btn-outline">Voir détails</a>
            <?php if (in_array($r['statut'], ['en_attente', 'confirmé', 'confirmée', 'confirmee'], true)): ?>
                <button type="button" class="btn btn-danger"
                        onclick="openCancelModal(<?= (int)$r['id'] ?>)"
                        style="flex:1;">
                    Annuler
                </button>
            <?php endif; ?>
            <?php if (in_array($r['statut'], ['terminé','terminée','termine'], true)): ?>
                <a href="<?= htmlspecialchars($r['type_service']) ?>.php?id=<?= (int)$r['service_id'] ?>#avis" class="btn" style="flex:1;background:var(--orange);color:#fff;">Laisser un avis</a>
            <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</div>

<!-- ── Confirmation d'annulation (Bootstrap 5.3 modal) ──────── -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="cancelModalLabel">⚠️ Annuler la réservation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body">
        Êtes-vous sûr de vouloir annuler cette réservation ?<br>
        <strong>Cette action est irréversible.</strong>
      </div>
      <div class="modal-footer d-flex">
        <button type="button" id="btnConfirmCancel">Oui, annuler</button>
        <button type="button" id="btnKeepReservation" data-bs-dismiss="modal">Non, garder</button>
      </div>
    </div>
  </div>
</div>

<!-- Hidden form submitted when the user confirms cancellation -->
<form method="post" action="mes-reservations.php" id="cancelForm" style="display:none;">
  <input type="hidden" name="action" value="cancel">
  <input type="hidden" name="reservation_id" id="cancelReservationId" value="">
</form>

<?php include 'footer.php'; ?>

<!-- Bootstrap 5.3 JS bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openCancelModal(reservationId) {
  document.getElementById('cancelReservationId').value = reservationId;
  var modal = new bootstrap.Modal(document.getElementById('cancelModal'));
  modal.show();
}
document.getElementById('btnConfirmCancel').addEventListener('click', function () {
  document.getElementById('cancelForm').submit();
});
</script>
</body>
</html>
