<?php
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/i18n.php';

$L_ALL = [
    'fr' => [
        'page_title'    => 'Écrire un article — Tarkina',
        'back'          => 'Retour au blog',
        'heading'       => 'Partagez votre voyage',
        'sub'           => 'Racontez votre expérience, ajoutez une photo et une recommandation pour aider les autres voyageurs.',
        'preview_alt'   => 'Aperçu',
        'upload_line1'  => 'Cliquez pour ajouter une photo de votre voyage',
        'upload_line2'  => 'JPG, PNG, WEBP · max 6 Mo',
        'lbl_title'     => "Titre de l'article *",
        'ph_title'      => 'Ex : Trois jours magiques à Kairouan',
        'lbl_region'    => 'Région visitée',
        'opt_region'    => '— Choisir une région —',
        'lbl_story'     => 'Votre récit *',
        'ph_story'      => 'Décrivez votre séjour, ce qui vous a marqué, les lieux visités...',
        'lbl_reco'      => 'Votre recommandation (optionnel)',
        'ph_reco'       => 'Un bon plan, un conseil ou une adresse à ne pas manquer...',
        'submit'        => "Publier l'article",
        'err_upload'    => "Erreur lors de l'envoi de l'image.",
        'err_format'    => "Format d'image non supporté (JPG, PNG, WEBP, GIF).",
        'err_size'      => "L'image ne doit pas dépasser 6 Mo.",
        'err_save'      => "Impossible d'enregistrer l'image.",
        'err_required'  => 'Le titre et le récit sont obligatoires.',
        'err_generic'   => 'Une erreur est survenue, réessayez.',
    ],
    'ar' => [
        'page_title'    => 'اكتب مقالًا — تاركينا',
        'back'          => 'العودة إلى المدونة',
        'heading'       => 'شاركنا رحلتك',
        'sub'           => 'احكِ تجربتك، أضف صورة وتوصية لمساعدة المسافرين الآخرين.',
        'preview_alt'   => 'معاينة',
        'upload_line1'  => 'انقر لإضافة صورة من رحلتك',
        'upload_line2'  => 'JPG, PNG, WEBP · بحد أقصى 6 ميغابايت',
        'lbl_title'     => 'عنوان المقال *',
        'ph_title'      => 'مثال: ثلاثة أيام ساحرة في القيروان',
        'lbl_region'    => 'الجهة المُزارة',
        'opt_region'    => '— اختر جهة —',
        'lbl_story'     => 'حكايتك *',
        'ph_story'      => 'صف إقامتك وما لفت انتباهك والأماكن التي زرتها...',
        'lbl_reco'      => 'توصيتك (اختياري)',
        'ph_reco'       => 'فكرة ذكية أو نصيحة أو عنوان لا يُفوَّت...',
        'submit'        => 'انشر المقال',
        'err_upload'    => 'حدث خطأ أثناء تحميل الصورة.',
        'err_format'    => 'صيغة الصورة غير مدعومة (JPG, PNG, WEBP, GIF).',
        'err_size'      => 'يجب ألا تتجاوز الصورة 6 ميغابايت.',
        'err_save'      => 'تعذّر حفظ الصورة.',
        'err_required'  => 'العنوان والحكاية مطلوبان.',
        'err_generic'   => 'حدث خطأ، يُرجى المحاولة مجددًا.',
    ],
    'en' => [
        'page_title'    => 'Write a post — Tarkina',
        'back'          => 'Back to blog',
        'heading'       => 'Share your trip',
        'sub'           => 'Tell your story, add a photo and a recommendation to help other travellers.',
        'preview_alt'   => 'Preview',
        'upload_line1'  => 'Click to add a photo from your trip',
        'upload_line2'  => 'JPG, PNG, WEBP · max 6 MB',
        'lbl_title'     => 'Post title *',
        'ph_title'      => 'E.g. Three magical days in Kairouan',
        'lbl_region'    => 'Region visited',
        'opt_region'    => '— Choose a region —',
        'lbl_story'     => 'Your story *',
        'ph_story'      => 'Describe your stay, what struck you, the places you visited...',
        'lbl_reco'      => 'Your recommendation (optional)',
        'ph_reco'       => 'A good tip, advice, or an address not to miss...',
        'submit'        => 'Publish post',
        'err_upload'    => 'Error while uploading the image.',
        'err_format'    => 'Unsupported image format (JPG, PNG, WEBP, GIF).',
        'err_size'      => 'The image must not exceed 6 MB.',
        'err_save'      => 'Could not save the image.',
        'err_required'  => 'Title and story are required.',
        'err_generic'   => 'An error occurred, please try again.',
    ],
];
$L = $L_ALL[$lang] ?? $L_ALL['fr'];

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$regions = [];
$rq = mysqli_query($conn, "SELECT id, nom FROM region ORDER BY nom ASC");
while ($rq && $row = mysqli_fetch_assoc($rq)) { $regions[] = $row; }

$error = '';
$old = ['titre' => '', 'region_id' => '', 'contenu' => '', 'recommandation' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['titre']          = trim($_POST['titre'] ?? '');
    $old['region_id']      = trim($_POST['region_id'] ?? '');
    $old['contenu']        = trim($_POST['contenu'] ?? '');
    $old['recommandation'] = trim($_POST['recommandation'] ?? '');

    $titre   = $old['titre'];
    $contenu = $old['contenu'];
    $reco    = $old['recommandation'];
    $regionId = $old['region_id'] !== '' ? (int) $old['region_id'] : null;
    $userId  = (int) $_SESSION['user_id'];

    $photoPath = null;
    if (!empty($_FILES['photo']['name'])) {
        if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            $error = $L['err_upload'];
        } else {
            $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','webp','gif'];
            if (!in_array($ext, $allowed, true)) {
                $error = $L['err_format'];
            } elseif ($_FILES['photo']['size'] > 6 * 1024 * 1024) {
                $error = $L['err_size'];
            } else {
                $upDir = __DIR__ . '/uploads/blogs';
                if (!is_dir($upDir)) { @mkdir($upDir, 0755, true); }
                $file = uniqid('blog_', true) . '.' . $ext;
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $upDir . '/' . $file)) {
                    $photoPath = 'uploads/blogs/' . $file;
                } else {
                    $error = $L['err_save'];
                }
            }
        }
    }

    if ($error === '') {
        if ($titre === '' || $contenu === '') {
            $error = $L['err_required'];
        } else {
            $stmt = mysqli_prepare($conn, 'INSERT INTO blogs (utilisateur_id, region_id, titre, contenu, recommandation, photo) VALUES (?,?,?,?,?,?)');
            mysqli_stmt_bind_param($stmt, 'iissss', $userId, $regionId, $titre, $contenu, $reco, $photoPath);
            if (mysqli_stmt_execute($stmt)) {
                $newId = (int) mysqli_insert_id($conn);
                mysqli_stmt_close($stmt);
                header('Location: blog-post.php?id=' . $newId);
                exit;
            }
            $error = $L['err_generic'];
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>" dir="<?= htmlspecialchars($dir) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($L['page_title']) ?></title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/rtl.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="assets/css/blog.css">
</head>
<body class="blog-page">
<?php $navLight = true; include 'navbar.php'; ?>

<div class="blog-wrap">
  <a href="blogs.php" class="post-back"><i class="bi bi-arrow-left"></i> <?= htmlspecialchars($L['back']) ?></a>
  <div class="blog-form-card">
    <h2><?= htmlspecialchars($L['heading']) ?></h2>
    <p class="sub"><?= htmlspecialchars($L['sub']) ?></p>

    <?php if ($error): ?><div class="bf-alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
      <img id="bf-preview" src="" alt="<?= htmlspecialchars($L['preview_alt']) ?>">
      <div class="bf-upload" id="bf-up">
        <input type="file" name="photo" id="bf-file" accept="image/*">
        <i class="bi bi-image"></i>
        <span><?= htmlspecialchars($L['upload_line1']) ?><br><small><?= htmlspecialchars($L['upload_line2']) ?></small></span>
      </div>

      <div class="bf-group">
        <label><?= htmlspecialchars($L['lbl_title']) ?></label>
        <input type="text" name="titre" maxlength="200" required placeholder="<?= htmlspecialchars($L['ph_title']) ?>" value="<?= htmlspecialchars($old['titre']) ?>">
      </div>

      <div class="bf-group">
        <label><?= htmlspecialchars($L['lbl_region']) ?></label>
        <select name="region_id">
          <option value=""><?= htmlspecialchars($L['opt_region']) ?></option>
          <?php foreach ($regions as $r): ?>
            <option value="<?= (int)$r['id'] ?>" <?= ((string)$old['region_id'] === (string)$r['id']) ? 'selected' : '' ?>><?= htmlspecialchars($r['nom']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="bf-group">
        <label><?= htmlspecialchars($L['lbl_story']) ?></label>
        <textarea name="contenu" rows="7" required placeholder="<?= htmlspecialchars($L['ph_story']) ?>"><?= htmlspecialchars($old['contenu']) ?></textarea>
      </div>

      <div class="bf-group">
        <label><i class="bi bi-lightbulb"></i> <?= htmlspecialchars($L['lbl_reco']) ?></label>
        <textarea name="recommandation" rows="3" placeholder="<?= htmlspecialchars($L['ph_reco']) ?>"><?= htmlspecialchars($old['recommandation']) ?></textarea>
      </div>

      <button type="submit" class="bf-submit"><i class="bi bi-send"></i> <?= htmlspecialchars($L['submit']) ?></button>
    </form>
  </div>
</div>

<?php include 'footer.php'; ?>
<script>
const f = document.getElementById('bf-file');
f && f.addEventListener('change', function(){
  const file = this.files[0]; if(!file) return;
  const reader = new FileReader();
  reader.onload = e => {
    const img = document.getElementById('bf-preview');
    img.src = e.target.result; img.style.display = 'block';
    document.getElementById('bf-up').style.display = 'none';
  };
  reader.readAsDataURL(file);
});
</script>
</body>
</html>
