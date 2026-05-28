<?php
session_start();
require_once __DIR__ . '/db.php';

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
            $error = "Erreur lors de l'envoi de l'image.";
        } else {
            $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','webp','gif'];
            if (!in_array($ext, $allowed, true)) {
                $error = 'Format d\'image non supporté (JPG, PNG, WEBP, GIF).';
            } elseif ($_FILES['photo']['size'] > 6 * 1024 * 1024) {
                $error = 'L\'image ne doit pas dépasser 6 Mo.';
            } else {
                $dir = __DIR__ . '/uploads/blogs';
                if (!is_dir($dir)) { @mkdir($dir, 0755, true); }
                $file = uniqid('blog_', true) . '.' . $ext;
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $dir . '/' . $file)) {
                    $photoPath = 'uploads/blogs/' . $file;
                } else {
                    $error = 'Impossible d\'enregistrer l\'image.';
                }
            }
        }
    }

    if ($error === '') {
        if ($titre === '' || $contenu === '') {
            $error = 'Le titre et le récit sont obligatoires.';
        } else {
            $stmt = mysqli_prepare($conn, 'INSERT INTO blogs (utilisateur_id, region_id, titre, contenu, recommandation, photo) VALUES (?,?,?,?,?,?)');
            mysqli_stmt_bind_param($stmt, 'iissss', $userId, $regionId, $titre, $contenu, $reco, $photoPath);
            if (mysqli_stmt_execute($stmt)) {
                $newId = (int) mysqli_insert_id($conn);
                mysqli_stmt_close($stmt);
                header('Location: blog-post.php?id=' . $newId);
                exit;
            }
            $error = 'Une erreur est survenue, réessayez.';
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Écrire un article — Tarkina</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="assets/css/blog.css">
</head>
<body class="blog-page">
<?php include 'navbar.php'; ?>

<div class="blog-wrap">
  <a href="blogs.php" class="post-back"><i class="bi bi-arrow-left"></i> Retour au blog</a>
  <div class="blog-form-card">
    <h2>Partagez votre voyage</h2>
    <p class="sub">Racontez votre expérience, ajoutez une photo et une recommandation pour aider les autres voyageurs.</p>

    <?php if ($error): ?><div class="bf-alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
      <img id="bf-preview" src="" alt="Aperçu">
      <div class="bf-upload" id="bf-up">
        <input type="file" name="photo" id="bf-file" accept="image/*">
        <i class="bi bi-image"></i>
        <span>Cliquez pour ajouter une photo de votre voyage<br><small>JPG, PNG, WEBP · max 6 Mo</small></span>
      </div>

      <div class="bf-group">
        <label>Titre de l'article *</label>
        <input type="text" name="titre" maxlength="200" required placeholder="Ex : Trois jours magiques à Kairouan" value="<?= htmlspecialchars($old['titre']) ?>">
      </div>

      <div class="bf-group">
        <label>Région visitée</label>
        <select name="region_id">
          <option value="">— Choisir une région —</option>
          <?php foreach ($regions as $r): ?>
            <option value="<?= (int)$r['id'] ?>" <?= ((string)$old['region_id'] === (string)$r['id']) ? 'selected' : '' ?>><?= htmlspecialchars($r['nom']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="bf-group">
        <label>Votre récit *</label>
        <textarea name="contenu" rows="7" required placeholder="Décrivez votre séjour, ce qui vous a marqué, les lieux visités..."><?= htmlspecialchars($old['contenu']) ?></textarea>
      </div>

      <div class="bf-group">
        <label><i class="bi bi-lightbulb"></i> Votre recommandation (optionnel)</label>
        <textarea name="recommandation" rows="3" placeholder="Un bon plan, un conseil ou une adresse à ne pas manquer..."><?= htmlspecialchars($old['recommandation']) ?></textarea>
      </div>

      <button type="submit" class="bf-submit"><i class="bi bi-send"></i> Publier l'article</button>
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

