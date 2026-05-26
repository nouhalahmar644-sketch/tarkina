<?php
/**
 * Reviews section partial for a service page.
 * Requires in scope: $conn, $serviceType (string), $serviceId (int).
 * Self-contained styles (avis- prefix). Bootstrap-compatible.
 */
if (!isset($conn, $serviceType, $serviceId)) {
    return;
}
require_once __DIR__ . '/avis_helpers.php';

$__avisList        = avis_list($conn, $serviceType, (int) $serviceId);
$__avisSum         = avis_summary($conn, $serviceType, (int) $serviceId);
$__loggedIn        = !empty($_SESSION['user_id']);
$__alreadyReviewed = $__loggedIn ? avis_user_has($conn, $serviceType, (int) $serviceId, (int) $_SESSION['user_id']) : false;
$__hasReservation  = $__loggedIn ? avis_user_has_completed_reservation($conn, $serviceType, (int) $serviceId, (int) $_SESSION['user_id']) : false;
$__avisFlash       = $_SESSION['avis_flash'] ?? null;
unset($_SESSION['avis_flash']);
?>
<style>
.avis-wrap{--coral:#E05A2B;--navy:#1B3A4B;--cream:#FAF8F5;}
.avis-summary{display:flex;align-items:center;gap:14px;margin-bottom:18px;}
.avis-avg{font-size:2.2rem;font-weight:800;color:var(--navy);line-height:1;font-family:'Playfair Display',Georgia,serif;}
.avis-summary .stars{color:var(--coral);font-size:1.05rem;letter-spacing:2px;}
.avis-summary .count{color:#6b7280;font-size:.85rem;}
.avis-card{background:#fff;border:1px solid #ececec;border-radius:14px;padding:14px 18px;margin-bottom:12px;box-shadow:0 2px 10px rgba(27,58,75,.04);}
.avis-card .head{display:flex;justify-content:space-between;align-items:center;}
.avis-card .name{font-weight:700;color:var(--navy);}
.avis-card .stars{color:var(--coral);letter-spacing:1px;}
.avis-card .date{color:#9ca3af;font-size:.78rem;margin:2px 0 6px;}
.avis-card p{margin:0;color:#444;font-size:.95rem;}
.avis-empty{color:#6b7280;background:var(--cream);border-radius:12px;padding:16px;text-align:center;font-size:.92rem;}
.avis-form{background:var(--cream);border:1px solid #ececec;border-radius:14px;padding:18px;margin-top:18px;}
.avis-form h4{color:var(--navy);font-weight:800;margin-bottom:12px;font-size:1.05rem;}
.avis-rate{display:inline-flex;flex-direction:row-reverse;gap:4px;margin-bottom:12px;}
.avis-rate input{display:none;}
.avis-rate label{font-size:1.9rem;color:#d6d3ce;cursor:pointer;transition:color .15s;line-height:1;}
.avis-rate input:checked ~ label,.avis-rate label:hover,.avis-rate label:hover ~ label{color:var(--coral);}
.avis-flash{padding:10px 14px;border-radius:10px;margin-bottom:14px;font-weight:600;font-size:.9rem;}
.avis-flash.ok{background:#eaf8f0;color:#1f7a43;border:1px solid #b6e2c6;}
.avis-flash.err{background:#fff1f1;color:#b43737;border:1px solid #f1c9c9;}
.avis-btn{background:var(--coral);color:#fff;border:none;border-radius:50px;padding:10px 26px;font-weight:700;cursor:pointer;}
.avis-btn:hover{background:#c44d22;}
.avis-ta{border-radius:10px;border:1px solid #d6d3ce;width:100%;padding:10px;font-family:inherit;resize:vertical;}
</style>
<div class="avis-wrap" id="avis">
  <?php if ($__avisFlash): ?>
    <div class="avis-flash <?= $__avisFlash['type'] === 'ok' ? 'ok' : 'err' ?>"><?= htmlspecialchars($__avisFlash['msg']) ?></div>
  <?php endif; ?>

  <?php if ($__avisSum['count'] > 0): ?>
    <div class="avis-summary">
      <span class="avis-avg"><?= number_format($__avisSum['avg'], 1) ?></span>
      <div>
        <div class="stars"><?= avis_stars_html((int) round($__avisSum['avg'])) ?></div>
        <div class="count"><?= (int) $__avisSum['count'] ?> avis</div>
      </div>
    </div>
  <?php endif; ?>

  <?php if (empty($__avisList)): ?>
    <div class="avis-empty">Aucun avis pour le moment. Soyez le premier à partager votre expérience&nbsp;!</div>
  <?php else: ?>
    <?php foreach ($__avisList as $av): ?>
      <div class="avis-card">
        <div class="head">
          <span class="name"><?= htmlspecialchars(trim(($av['prenom'] ?? '') . ' ' . ($av['nom'] ?? ''))) ?: 'Voyageur' ?></span>
          <span class="stars"><?= avis_stars_html((int) $av['note']) ?></span>
        </div>
        <div class="date"><?= date('d/m/Y', strtotime($av['created_at'])) ?></div>
        <?php if (trim((string) $av['commentaire']) !== ''): ?>
          <p><?= nl2br(htmlspecialchars($av['commentaire'])) ?></p>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

  <?php if (!$__loggedIn): ?>
    <div class="avis-empty" style="margin-top:14px;"><a href="login.php" style="color:#E05A2B;font-weight:700;">Connectez-vous</a> pour laisser un avis.</div>
  <?php elseif ($__alreadyReviewed): ?>
    <div class="avis-empty" style="margin-top:14px;">Vous avez déjà laissé un avis pour ce service.</div>
  <?php elseif ($__hasReservation): ?>
    <form class="avis-form" method="post" action="avis-add.php">
      <h4>Laisser un avis</h4>
      <input type="hidden" name="type" value="<?= htmlspecialchars($serviceType) ?>">
      <input type="hidden" name="service_id" value="<?= (int) $serviceId ?>">
      <div class="avis-rate">
        <input type="radio" id="st5" name="note" value="5" required><label for="st5" title="5 étoiles">★</label>
        <input type="radio" id="st4" name="note" value="4"><label for="st4" title="4 étoiles">★</label>
        <input type="radio" id="st3" name="note" value="3"><label for="st3" title="3 étoiles">★</label>
        <input type="radio" id="st2" name="note" value="2"><label for="st2" title="2 étoiles">★</label>
        <input type="radio" id="st1" name="note" value="1"><label for="st1" title="1 étoile">★</label>
      </div>
      <textarea name="commentaire" class="avis-ta mb-3" rows="3" placeholder="Partagez votre expérience..." maxlength="1000"></textarea>
      <div style="margin-top:12px;"><button type="submit" class="avis-btn">Publier mon avis</button></div>
    </form>
  <?php endif; ?>
</div>
