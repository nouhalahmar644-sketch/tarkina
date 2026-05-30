<?php
session_start();
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/i18n.php';

$L_ALL = [
    'fr' => [
        'page_title'       => 'Mes Réservations & Commandes – Tarkina',
        'page_heading'     => 'Mes Réservations & Commandes',
        'tab_res'          => 'Mes Réservations',
        'tab_cmd'          => 'Mes Commandes',
        'ok_cancel'        => 'La réservation a été annulée avec succès.',
        'err_cancel'       => "Erreur lors de l'annulation de la réservation.",
        'err_not_cancel'   => 'Cette réservation ne peut pas être annulée.',
        'empty_res_h'      => 'Aucune réservation',
        'empty_res_p'      => "Vous n'avez pas encore effectué de réservation.",
        'empty_cmd_h'      => 'Aucune commande',
        'empty_cmd_p'      => "Vous n'avez pas encore effectué de commande.",
        'explore_btn'      => 'Explorer les offres',
        'type_hebergement' => 'Hébergement',
        'type_repas'       => 'Gastronomie',
        'type_guide'       => 'Guide Local',
        'type_evenement'   => 'Événement',
        'type_artisanat'   => 'Artisanat',
        'type_forfait'     => 'Forfait',
        'from'             => 'du',
        'to'               => 'au',
        'on'               => 'le',
        'st_en_attente'    => 'En attente',
        'st_confirmee'     => 'Confirmée',
        'st_annulee'       => 'Annulée',
        'st_terminee'      => 'Terminée',
        'res_number'       => 'N° de réservation',
        'req_date'         => 'Date de la demande',
        'dates'            => 'Dates',
        'travelers'        => 'Voyageurs',
        'total_est'        => 'Total estimé',
        'cmd_number'       => 'N° de commande',
        'cmd_date'         => 'Date de commande',
        'qty'              => 'Quantité',
        'total'            => 'Total',
        'view_details'     => 'Voir détails',
        'cancel'           => 'Annuler',
        'leave_review'     => 'Laisser un avis',
        'modal_title'      => 'Annuler la réservation',
        'modal_body'       => 'Êtes-vous sûr de vouloir annuler cette réservation ? Cette action est irréversible.',
        'modal_close'      => 'Fermer',
        'modal_confirm'    => 'Oui, annuler',
        'modal_keep'       => 'Non, garder',
    ],
    'ar' => [
        'page_title'       => 'حجوزاتي وطلباتي – تاركينا',
        'page_heading'     => 'حجوزاتي وطلباتي',
        'tab_res'          => 'حجوزاتي',
        'tab_cmd'          => 'طلباتي',
        'ok_cancel'        => 'تمّ إلغاء الحجز بنجاح.',
        'err_cancel'       => 'حدث خطأ أثناء إلغاء الحجز.',
        'err_not_cancel'   => 'لا يمكن إلغاء هذا الحجز.',
        'empty_res_h'      => 'لا توجد حجوزات',
        'empty_res_p'      => 'لم تقم بأي حجز بعد.',
        'empty_cmd_h'      => 'لا توجد طلبات',
        'empty_cmd_p'      => 'لم تقم بأي طلب بعد.',
        'explore_btn'      => 'استكشف العروض',
        'type_hebergement' => 'إقامة',
        'type_repas'       => 'مأكولات',
        'type_forfait'     => 'باقة',
        'type_guide'       => 'مرشد محلي',
        'type_evenement'   => 'فعالية',
        'type_artisanat'   => 'حِرف يدوية',
        'from'             => 'من',
        'to'               => 'إلى',
        'on'               => 'بتاريخ',
        'st_en_attente'    => 'قيد الانتظار',
        'st_confirmee'     => 'مؤكّد',
        'st_annulee'       => 'ملغى',
        'st_terminee'      => 'منتهي',
        'res_number'       => 'رقم الحجز',
        'req_date'         => 'تاريخ الطلب',
        'dates'            => 'التواريخ',
        'travelers'        => 'المسافرون',
        'total_est'        => 'الإجمالي التقديري',
        'cmd_number'       => 'رقم الطلب',
        'cmd_date'         => 'تاريخ الطلب',
        'qty'              => 'الكمية',
        'total'            => 'الإجمالي',
        'view_details'     => 'عرض التفاصيل',
        'cancel'           => 'إلغاء',
        'leave_review'     => 'اترك تقييمًا',
        'modal_title'      => 'إلغاء الحجز',
        'modal_body'       => 'هل أنت متأكد من إلغاء هذا الحجز؟ هذا الإجراء لا يمكن التراجع عنه.',
        'modal_close'      => 'إغلاق',
        'modal_confirm'    => 'نعم، ألغِ',
        'modal_keep'       => 'لا، احتفظ به',
    ],
    'en' => [
        'page_title'       => 'My Reservations & Orders – Tarkina',
        'page_heading'     => 'My Reservations & Orders',
        'tab_res'          => 'My Reservations',
        'tab_cmd'          => 'My Orders',
        'ok_cancel'        => 'The reservation has been cancelled successfully.',
        'err_cancel'       => 'An error occurred while cancelling the reservation.',
        'err_not_cancel'   => 'This reservation cannot be cancelled.',
        'empty_res_h'      => 'No reservations',
        'empty_res_p'      => "You haven't made any reservation yet.",
        'empty_cmd_h'      => 'No orders',
        'empty_cmd_p'      => "You haven't placed any order yet.",
        'explore_btn'      => 'Explore offers',
        'type_hebergement' => 'Accommodation',
        'type_repas'       => 'Gastronomy',
        'type_forfait'     => 'Pack',
        'type_guide'       => 'Local Guide',
        'type_evenement'   => 'Event',
        'type_artisanat'   => 'Handicrafts',
        'from'             => 'from',
        'to'               => 'to',
        'on'               => 'on',
        'st_en_attente'    => 'Pending',
        'st_confirmee'     => 'Confirmed',
        'st_annulee'       => 'Cancelled',
        'st_terminee'      => 'Completed',
        'res_number'       => 'Reservation no.',
        'req_date'         => 'Request date',
        'dates'            => 'Dates',
        'travelers'        => 'Travelers',
        'total_est'        => 'Estimated total',
        'cmd_number'       => 'Order no.',
        'cmd_date'         => 'Order date',
        'qty'              => 'Quantity',
        'total'            => 'Total',
        'view_details'     => 'View details',
        'cancel'           => 'Cancel',
        'leave_review'     => 'Leave a review',
        'modal_title'      => 'Cancel reservation',
        'modal_body'       => 'Are you sure you want to cancel this reservation? This action cannot be undone.',
        'modal_close'      => 'Close',
        'modal_confirm'    => 'Yes, cancel',
        'modal_keep'       => 'No, keep it',
    ],
];
$L = $L_ALL[$lang] ?? $L_ALL['fr'];

$user_id = (int) $_SESSION['user_id'];
$success_msg = '';
$error_msg = '';

// Handle cancel action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel') {
    $res_id = isset($_POST['reservation_id']) ? (int)$_POST['reservation_id'] : 0;
    if ($res_id > 0) {
        // Only allow canceling if 'en_attente' or 'confirmé'
        $res_check = mysqli_query($conn, "SELECT statut FROM reservations WHERE id = $res_id AND user_id = " . (int)$_SESSION['user_id'] . " LIMIT 1");
        $row_check = $res_check ? mysqli_fetch_assoc($res_check) : null;

        if ($row_check && in_array($row_check['statut'], ['en_attente', 'confirmé', 'confirmée', 'confirmee'], true)) {
            if (mysqli_query($conn, "UPDATE reservations SET statut = 'annulée' WHERE id = $res_id")) {
                $success_msg = $L['ok_cancel'];
            } else {
                $error_msg = $L['err_cancel'];
            }
        } else {
            $error_msg = $L['err_not_cancel'];
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
           a.titre AS artisanat_titre,
           pk.titre AS forfait_titre
    FROM reservations r
    LEFT JOIN hebergement h ON r.type_service = 'hebergement' AND r.service_id = h.id
    LEFT JOIN repas rp ON r.type_service = 'repas' AND r.service_id = rp.id
    LEFT JOIN guide g ON r.type_service = 'guide' AND r.service_id = g.id
    LEFT JOIN evenement e ON r.type_service = 'evenement' AND r.service_id = e.id
    LEFT JOIN artisanat a ON r.type_service = 'artisanat' AND r.service_id = a.id
    LEFT JOIN packs pk ON r.type_service = 'forfait' AND r.service_id = pk.id
    WHERE r.user_id = " . (int)$_SESSION['user_id'] . "
    ORDER BY r.created_at DESC
";
$result = mysqli_query($conn, $sql);
$reservations = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $reservations[] = $row;
    }
}

// Filter for Tab 1 (Mes Réservations): hebergement, guide, evenement only
$reservations_list = [];
foreach ($reservations as $r) {
    if (in_array($r['type_service'], ['hebergement', 'guide', 'evenement', 'forfait'], true)) {
        $reservations_list[] = $r;
    }
}

// Filter for Tab 2 (Mes Commandes): artisanat, repas
$commandes_list = [];
foreach ($reservations as $r) {
    if (in_array($r['type_service'], ['artisanat', 'repas'], true)) {
        $commandes_list[] = $r;
    }
}

?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>" dir="<?= htmlspecialchars($dir) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($L['page_title']) ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Lato:wght@400;600;700&display=swap" rel="stylesheet" />
  <!-- Bootstrap 5.3 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/rtl.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --cream: #FFFFFF;
      --dark: #1c1c2e;
      --navy: #0b1c30;
      --orange: #f16e22;
      --coral: #f16e22;
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

    /* Custom Nav Tabs Styling */
    .nav-tabs {
      border-bottom: 2px solid var(--border);
      margin-bottom: 32px !important;
      display: flex;
      gap: 12px;
    }
    .nav-tabs .nav-item {
      margin-bottom: -2px;
    }
    .nav-tabs .nav-link {
      background: none;
      border: none;
      border-bottom: 3px solid transparent;
      color: var(--navy);
      font-weight: 700;
      font-size: 16px;
      padding: 12px 24px;
      transition: all 0.3s ease;
      cursor: pointer;
      border-radius: 0;
    }
    .nav-tabs .nav-link:hover {
      color: var(--coral);
      border-color: transparent;
    }
    .nav-tabs .nav-link.active {
      color: var(--coral);
      border-bottom: 3px solid var(--coral);
      background: none;
    }

    .reservations-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 24px; }
    
    .res-card { background: var(--white); border-radius: var(--radius); padding: 24px; border: 1px solid var(--border); display: flex; flex-direction: column; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
    
    .res-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid var(--border); }
    .res-type { font-size: 11px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: var(--muted); margin-bottom: 4px; }
    .res-title { font-family: 'Playfair Display', serif; font-size: 20px; font-weight: 800; color: var(--dark); line-height: 1.2; }
    
    .badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; border: none; display: inline-block; }
    .badge-en_attente { background: rgba(241,110,34,0.1); color: var(--coral); }
    .badge-confirmee { background: rgba(46, 204, 113, 0.1); color: var(--green); }
    .badge-annulee { background: rgba(231, 76, 60, 0.1); color: var(--red); }
    .badge-terminee { background: rgba(107, 107, 107, 0.1); color: var(--muted); }
    
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
      padding: 20px 24px;
      line-height: 1.6;
    }
    #cancelModal .modal-footer {
      border-top: none;
      padding-top: 0;
      gap: 10px;
      padding: 0 24px 24px;
    }
    #btnConfirmCancel {
      background: #f16e22;
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 10px 22px;
      font-weight: 700;
      font-size: 14px;
      transition: background .2s;
      flex: 1;
    }
    #btnConfirmCancel:hover { background: #d95716; }
    #btnKeepReservation {
      background: #0b1c30;
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 10px 22px;
      font-weight: 700;
      font-size: 14px;
      transition: background .2s;
      flex: 1;
    }
    #btnKeepReservation:hover { background: #0d2440; }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="main-container">
  <div class="page-header">
    <h1 class="page-title"><?= htmlspecialchars($L['page_heading']) ?></h1>
  </div>

  <?php if ($success_msg): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success_msg) ?></div>
  <?php endif; ?>
  <?php if ($error_msg): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error_msg) ?></div>
  <?php endif; ?>

  <!-- Tabs Navigation -->
  <ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="reservations-tab" data-bs-toggle="tab" data-bs-target="#reservations-pane" type="button" role="tab" aria-controls="reservations-pane" aria-selected="true"><?= htmlspecialchars($L['tab_res']) ?></button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="commandes-tab" data-bs-toggle="tab" data-bs-target="#commandes-pane" type="button" role="tab" aria-controls="commandes-pane" aria-selected="false"><?= htmlspecialchars($L['tab_cmd']) ?></button>
    </li>
  </ul>

  <div class="tab-content" id="myTabContent">
    
    <!-- Tab 1: Mes Réservations -->
    <div class="tab-pane fade show active" id="reservations-pane" role="tabpanel" aria-labelledby="reservations-tab" tabindex="0">
      <?php if (empty($reservations_list)): ?>
        <div class="empty-state">
          <h3><?= htmlspecialchars($L['empty_res_h']) ?></h3>
          <p style="color:var(--muted); margin-bottom:24px;"><?= htmlspecialchars($L['empty_res_p']) ?></p>
          <a href="explorer.php" class="btn-nav"><?= htmlspecialchars($L['explore_btn']) ?></a>
        </div>
      <?php else: ?>
        <div class="reservations-grid">
          <?php foreach ($reservations_list as $r):
            $type = '';
            $titre = '';

            if ($r['type_service'] === 'hebergement') {
                $type = $L['type_hebergement'];
                $titre = $r['hebergement_titre'];
            } elseif ($r['type_service'] === 'repas') {
                $type = $L['type_repas'];
                $titre = $r['repas_titre'];
            } elseif ($r['type_service'] === 'guide') {
                $type = $L['type_guide'];
                $titre = $r['guide_titre'];
            } elseif ($r['type_service'] === 'evenement') {
                $type = $L['type_evenement'];
                $titre = $r['evenement_titre'];
            } elseif ($r['type_service'] === 'forfait') {
                $type = $L['type_forfait'];
                $titre = $r['forfait_titre'];
            }

            $url = $r['type_service'] . '.php?id=' . $r['service_id'];
            $total = (float)$r['prix_total'];

            if ($r['type_service'] === 'hebergement' || $r['type_service'] === 'forfait') {
                $dates_str = $L['from'] . ' ' . date('d/m/Y', strtotime($r['date_debut'])) . ' ' . $L['to'] . ' ' . date('d/m/Y', strtotime($r['date_fin']));
            } else {
                $dates_str = $L['on'] . ' ' . date('d/m/Y', strtotime($r['date_debut']));
            }

            $statut_class = '';
            $statut_label = '';
            switch ($r['statut']) {
                case 'en_attente':
                    $statut_class = 'badge-en_attente';
                    $statut_label = $L['st_en_attente'];
                    break;
                case 'confirmée':
                case 'confirmee':
                case 'confirmé':
                    $statut_class = 'badge-confirmee';
                    $statut_label = $L['st_confirmee'];
                    break;
                case 'annulée':
                case 'annulee':
                    $statut_class = 'badge-annulee';
                    $statut_label = $L['st_annulee'];
                    break;
                case 'terminé':
                case 'terminée':
                case 'termine':
                    $statut_class = 'badge-terminee';
                    $statut_label = $L['st_terminee'];
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
                <div class="badge <?= $statut_class ?>"><?= htmlspecialchars($statut_label) ?></div>
            </div>

            <div class="res-details">
                <div class="detail-row">
                    <span class="detail-label"><?= htmlspecialchars($L['res_number']) ?></span>
                    <span class="detail-value">#<?= $r['id'] ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><?= htmlspecialchars($L['req_date']) ?></span>
                    <span class="detail-value"><?= date('d/m/Y', strtotime($r['created_at'])) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><?= htmlspecialchars($L['dates']) ?></span>
                    <span class="detail-value"><?= htmlspecialchars($dates_str) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><?= htmlspecialchars($L['travelers']) ?></span>
                    <span class="detail-value"><?= (int)$r['nb_voyageurs'] ?></span>
                </div>

                <div class="res-total">
                    <span class="detail-label"><?= htmlspecialchars($L['total_est']) ?></span>
                    <span style="color:var(--orange);"><?= number_format($total, 2, '.', ' ') ?> TND</span>
                </div>
            </div>

            <div class="res-actions">
                <a href="<?= $url ?>" class="btn btn-outline"><?= htmlspecialchars($L['view_details']) ?></a>
                <?php if (in_array($r['statut'], ['en_attente', 'confirmé', 'confirmée', 'confirmee'], true)): ?>
                    <button type="button" class="btn btn-danger"
                            onclick="openCancelModal(<?= (int)$r['id'] ?>)"
                            style="flex:1;">
                        <?= htmlspecialchars($L['cancel']) ?>
                    </button>
                <?php endif; ?>
                <?php if (in_array($r['statut'], ['terminé','terminée','termine'], true)): ?>
                    <a href="<?= htmlspecialchars($r['type_service']) ?>.php?id=<?= (int)$r['service_id'] ?>#avis" class="btn" style="flex:1;background:var(--orange);color:#fff;"><?= htmlspecialchars($L['leave_review']) ?></a>
                <?php endif; ?>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- Tab 2: Mes Commandes -->
    <div class="tab-pane fade" id="commandes-pane" role="tabpanel" aria-labelledby="commandes-tab" tabindex="0">
      <?php if (empty($commandes_list)): ?>
        <div class="empty-state">
          <h3><?= htmlspecialchars($L['empty_cmd_h']) ?></h3>
          <p style="color:var(--muted); margin-bottom:24px;"><?= htmlspecialchars($L['empty_cmd_p']) ?></p>
          <a href="explorer.php" class="btn-nav"><?= htmlspecialchars($L['explore_btn']) ?></a>
        </div>
      <?php else: ?>
        <div class="reservations-grid">
          <?php foreach ($commandes_list as $r):
            $type = '';
            $titre = '';

            if ($r['type_service'] === 'artisanat') {
                $type = $L['type_artisanat'];
                $titre = $r['artisanat_titre'];
            } elseif ($r['type_service'] === 'repas') {
                $type = $L['type_repas'];
                $titre = $r['repas_titre'];
            }

            $url = $r['type_service'] . '.php?id=' . $r['service_id'];
            $total = (float)$r['prix_total'];

            $statut_class = '';
            $statut_label = '';
            switch ($r['statut']) {
                case 'en_attente':
                    $statut_class = 'badge-en_attente';
                    $statut_label = $L['st_en_attente'];
                    break;
                case 'confirmée':
                case 'confirmee':
                case 'confirmé':
                    $statut_class = 'badge-confirmee';
                    $statut_label = $L['st_confirmee'];
                    break;
                case 'annulée':
                case 'annulee':
                    $statut_class = 'badge-annulee';
                    $statut_label = $L['st_annulee'];
                    break;
                case 'terminé':
                case 'terminée':
                case 'termine':
                    $statut_class = 'badge-terminee';
                    $statut_label = $L['st_terminee'];
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
                <div class="badge <?= $statut_class ?>"><?= htmlspecialchars($statut_label) ?></div>
            </div>

            <div class="res-details">
                <div class="detail-row">
                    <span class="detail-label"><?= htmlspecialchars($L['cmd_number']) ?></span>
                    <span class="detail-value">#<?= $r['id'] ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><?= htmlspecialchars($L['cmd_date']) ?></span>
                    <span class="detail-value"><?= date('d/m/Y', strtotime($r['date_debut'])) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><?= htmlspecialchars($L['qty']) ?></span>
                    <span class="detail-value"><?= (int)$r['nb_voyageurs'] ?></span>
                </div>

                <div class="res-total">
                    <span class="detail-label"><?= htmlspecialchars($L['total']) ?></span>
                    <span style="color:var(--orange);"><?= number_format($total, 2, '.', ' ') ?> TND</span>
                </div>
            </div>

            <div class="res-actions">
                <a href="<?= $url ?>" class="btn btn-outline"><?= htmlspecialchars($L['view_details']) ?></a>
                <?php if (in_array($r['statut'], ['en_attente', 'confirmé', 'confirmée', 'confirmee'], true)): ?>
                    <button type="button" class="btn btn-danger"
                            onclick="openCancelModal(<?= (int)$r['id'] ?>)"
                            style="flex:1;">
                        <?= htmlspecialchars($L['cancel']) ?>
                    </button>
                <?php endif; ?>
                <?php if (in_array($r['statut'], ['terminé','terminée','termine'], true)): ?>
                    <a href="<?= htmlspecialchars($r['type_service']) ?>.php?id=<?= (int)$r['service_id'] ?>#avis" class="btn" style="flex:1;background:var(--orange);color:#fff;"><?= htmlspecialchars($L['leave_review']) ?></a>
                <?php endif; ?>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

  </div>

</div>

<!-- ── Confirmation d'annulation (Bootstrap 5.3 modal) ──────── -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="cancelModalLabel">⚠️ <?= htmlspecialchars($L['modal_title']) ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= htmlspecialchars($L['modal_close']) ?>"></button>
      </div>
      <div class="modal-body">
        <?= htmlspecialchars($L['modal_body']) ?>
      </div>
      <div class="modal-footer d-flex">
        <button type="button" id="btnConfirmCancel"><?= htmlspecialchars($L['modal_confirm']) ?></button>
        <button type="button" id="btnKeepReservation" data-bs-dismiss="modal"><?= htmlspecialchars($L['modal_keep']) ?></button>
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

