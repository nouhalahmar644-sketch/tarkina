<?php
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/i18n.php';

$L_ALL = [
    'fr' => [
        'page_suffix'    => ' — Tarkina Blog',
        'back'           => 'Retour au blog',
        'reco_label_a'   => 'La recommandation de ',
        'like'           => "J'aime",
        'confirm_delete' => 'Supprimer cet article ?',
        'delete'         => 'Supprimer',
        'comments'       => 'Commentaires',
        'no_comments'    => "Aucun commentaire pour l'instant. Lancez la discussion !",
        'ph_comment'     => 'Partagez votre avis ou posez une question...',
        'submit_comment' => 'Commenter',
        'login_link'     => 'Connectez-vous',
        'login_suffix'   => ' pour laisser un commentaire.',
    ],
    'ar' => [
        'page_suffix'    => ' — مدونة تاركينا',
        'back'           => 'العودة إلى المدونة',
        'reco_label_a'   => 'توصية ',
        'like'           => 'إعجاب',
        'confirm_delete' => 'هل تريد حذف هذا المقال؟',
        'delete'         => 'حذف',
        'comments'       => 'التعليقات',
        'no_comments'    => 'لا توجد تعليقات حتى الآن. كن أول من يبدأ النقاش!',
        'ph_comment'     => 'شارك رأيك أو اطرح سؤالًا...',
        'submit_comment' => 'علّق',
        'login_link'     => 'سجّل الدخول',
        'login_suffix'   => ' لإضافة تعليق.',
    ],
    'en' => [
        'page_suffix'    => ' — Tarkina Blog',
        'back'           => 'Back to blog',
        'reco_label_a'   => 'Recommendation from ',
        'like'           => 'Like',
        'confirm_delete' => 'Delete this post?',
        'delete'         => 'Delete',
        'comments'       => 'Comments',
        'no_comments'    => 'No comments yet. Start the conversation!',
        'ph_comment'     => 'Share your thoughts or ask a question...',
        'submit_comment' => 'Comment',
        'login_link'     => 'Log in',
        'login_suffix'   => ' to leave a comment.',
    ],
];
$L = $L_ALL[$lang] ?? $L_ALL['fr'];

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) { header('Location: blogs.php'); exit; }

$stmt = mysqli_prepare($conn, "SELECT b.*, u.prenom, u.nom, r.nom AS region_nom
        FROM blogs b JOIN utilisateur u ON b.utilisateur_id = u.id
        LEFT JOIN region r ON b.region_id = r.id WHERE b.id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$post = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);
if (!$post) { header('Location: blogs.php'); exit; }

// Comments
$comments = [];
$cs = mysqli_prepare($conn, "SELECT c.*, u.prenom, u.nom FROM blog_comments c
        JOIN utilisateur u ON c.utilisateur_id = u.id WHERE c.blog_id = ? ORDER BY c.created_at ASC");
mysqli_stmt_bind_param($cs, 'i', $id);
mysqli_stmt_execute($cs);
$cr = mysqli_stmt_get_result($cs);
while ($row = mysqli_fetch_assoc($cr)) { $comments[] = $row; }
mysqli_stmt_close($cs);

$isOwner = isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] === (int)$post['utilisateur_id'];
function bi2($p,$n){ return strtoupper(mb_substr((string)$p,0,1).mb_substr((string)$n,0,1)); }
/**
 * Resolve a blog photo to a usable src, repairing legacy/corrupted values
 * like "uploads\stories\https://images.unsplash.com/..." by extracting the URL.
 */
function post_photo_src($photo, $fallback) {
    $photo = trim((string) $photo);
    if ($photo === '') { return $fallback; }
    if (preg_match('~https?://\S+~', $photo, $m)) { return $m[0]; }
    if (preg_match('~^https?://~i', $photo)) { return $photo; }
    return str_replace('\\', '/', $photo);
}
function post_date_fr($datetime, $lang = 'fr') {
    $ts = strtotime((string) $datetime);
    if (!$ts) { return ''; }
    $months_fr = [1=>'janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
    $months_ar = [1=>'جانفي','فيفري','مارس','أفريل','ماي','جوان','جويلية','أوت','سبتمبر','أكتوبر','نوفمبر','ديسمبر'];
    $months_en = [1=>'January','February','March','April','May','June','July','August','September','October','November','December'];
    $d = (int)date('j', $ts);
    $m = (int)date('n', $ts);
    $y = date('Y', $ts);
    if ($lang === 'ar') { return $d . ' ' . $months_ar[$m] . ' ' . $y; }
    if ($lang === 'en') { return $months_en[$m] . ' ' . $d . ', ' . $y; }
    return $d . ' ' . $months_fr[$m] . ' ' . $y;
}
$cover = post_photo_src($post['photo'] ?? '', 'https://images.unsplash.com/photo-1539650116574-75c0c6d73f6e?w=1100&q=80');
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>" dir="<?= htmlspecialchars($dir) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($post['titre']) ?><?= htmlspecialchars($L['page_suffix']) ?></title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/rtl.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="assets/css/blog.css">
</head>
<body class="blog-page">
<?php include 'navbar.php'; ?>

<div class="post-wrap">
  <a href="blogs.php" class="post-back"><i class="bi bi-arrow-left"></i> <?= htmlspecialchars($L['back']) ?></a>

  <article class="post-card">
    <img class="post-cover" loading="lazy" src="<?= htmlspecialchars($cover) ?>" alt="<?= htmlspecialchars($post['titre']) ?>">
    <div class="post-body">
      <?php if (!empty($post['region_nom'])): ?><span class="post-region"><i class="bi bi-geo-alt-fill"></i> <?= htmlspecialchars($post['region_nom']) ?></span><?php endif; ?>
      <h1 class="post-title"><?= htmlspecialchars($post['titre']) ?></h1>
      <div class="post-author">
        <span class="avatar"><?= htmlspecialchars(bi2($post['prenom'],$post['nom'])) ?></span>
        <div>
          <div class="who"><?= htmlspecialchars($post['prenom'].' '.$post['nom']) ?></div>
          <div class="when"><i class="bi bi-calendar3"></i> <?= htmlspecialchars(post_date_fr($post['created_at'], $lang)) ?></div>
        </div>
      </div>

      <div class="post-content"><?= nl2br(htmlspecialchars($post['contenu'])) ?></div>

      <?php if (!empty(trim((string)$post['recommandation']))): ?>
        <div class="reco-box">
          <h4><i class="bi bi-lightbulb-fill"></i> <?= htmlspecialchars($L['reco_label_a']) ?><?= htmlspecialchars($post['prenom']) ?></h4>
          <p><?= nl2br(htmlspecialchars($post['recommandation'])) ?></p>
        </div>
      <?php endif; ?>

      <div class="post-actions">
        <button type="button" class="btn-like" id="likeBtn" data-id="<?= (int)$post['id'] ?>">
          <i class="bi bi-heart-fill"></i> <span id="likeCount"><?= (int)$post['likes'] ?></span> <?= htmlspecialchars($L['like']) ?>
        </button>
        <?php if ($isOwner): ?>
          <form method="post" action="blog-delete.php" onsubmit="return confirm('<?= htmlspecialchars($L['confirm_delete'], ENT_QUOTES) ?>');" style="margin:0;">
            <input type="hidden" name="id" value="<?= (int)$post['id'] ?>">
            <button type="submit" class="btn-del"><i class="bi bi-trash"></i> <?= htmlspecialchars($L['delete']) ?></button>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </article>
</div>

<section class="comments">
  <h3><i class="bi bi-chat-dots"></i> <?= htmlspecialchars($L['comments']) ?> (<?= count($comments) ?>)</h3>

  <?php foreach ($comments as $c): ?>
    <div class="comment">
      <div class="c-head">
        <span class="avatar"><?= htmlspecialchars(bi2($c['prenom'],$c['nom'])) ?></span>
        <span class="c-who"><?= htmlspecialchars($c['prenom'].' '.$c['nom']) ?></span>
        <span class="c-when">· <?= htmlspecialchars(post_date_fr($c['created_at'], $lang)) ?></span>
      </div>
      <div class="c-text"><?= nl2br(htmlspecialchars($c['contenu'])) ?></div>
    </div>
  <?php endforeach; ?>
  <?php if (empty($comments)): ?>
    <p style="color:var(--b-muted);"><?= htmlspecialchars($L['no_comments']) ?></p>
  <?php endif; ?>

  <?php if (isset($_SESSION['user_id'])): ?>
    <form class="comment-form" method="post" action="blog-comment.php">
      <input type="hidden" name="blog_id" value="<?= (int)$post['id'] ?>">
      <textarea name="contenu" required placeholder="<?= htmlspecialchars($L['ph_comment']) ?>"></textarea>
      <button type="submit" class="bf-submit" style="margin-top:12px;width:auto;padding:11px 24px;"><i class="bi bi-send"></i> <?= htmlspecialchars($L['submit_comment']) ?></button>
    </form>
  <?php else: ?>
    <div class="comment-login"><a href="login.php"><?= htmlspecialchars($L['login_link']) ?></a><?= htmlspecialchars($L['login_suffix']) ?></div>
  <?php endif; ?>
</section>

<?php include 'footer.php'; ?>
<script>
(function(){
  var btn = document.getElementById('likeBtn');
  if(!btn) return;
  btn.addEventListener('click', function(){
    var id = btn.getAttribute('data-id');
    fetch('blog-like.php', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'id='+encodeURIComponent(id)})
      .then(r=>r.json()).then(d=>{ if(d.success){ document.getElementById('likeCount').textContent = d.likes; } });
  });
})();
</script>
</body>
</html>
