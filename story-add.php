<?php
require 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$regions = mysqli_query($conn, "SELECT id, nom FROM region ORDER BY nom ASC");
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $caption = mysqli_real_escape_string($conn, trim($_POST['caption']));
    $region_id = !empty($_POST['region_id']) ? (int)$_POST['region_id'] : 'NULL';
    $user_id = (int)$_SESSION['user_id'];

    if (empty($_FILES['photo']['name'])) {
        $error = 'Veuillez sélectionner une photo.';
    } else {
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];
        if (!in_array($ext, $allowed)) {
            $error = 'Format non supporté. Utilisez JPG, PNG ou WEBP.';
        } elseif ($_FILES['photo']['size'] > 5 * 1024 * 1024) {
            $error = 'La photo ne doit pas dépasser 5 Mo.';
        } else {
            if (!is_dir('uploads/stories')) mkdir('uploads/stories', 0755, true);
            $filename = uniqid('story_') . '.' . $ext;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], 'uploads/stories/' . $filename)) {
                $region_val = ($region_id === 'NULL') ? 'NULL' : $region_id;
                mysqli_query($conn, "INSERT INTO stories (utilisateur_id, region_id, photo, caption)
                    VALUES ($user_id, $region_val, '$filename', '$caption')");
                $success = true;
            } else {
                $error = 'Erreur lors de l\'upload. Réessayez.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Partager une Story — Tarkina</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <style>
    .add-story-wrap {
      min-height: 80vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 60px 0;
      background: #FAF8F5;
    }
    .add-story-card {
      background: #fff;
      border-radius: 20px;
      padding: 40px;
      max-width: 540px;
      width: 100%;
      border: 1px solid #e2ddd8;
      box-shadow: 0 8px 32px rgba(27,58,75,0.08);
    }
    .add-story-card h2 {
      font-size: 1.6rem;
      font-weight: 700;
      color: #1B3A4B;
      margin-bottom: 0.3rem;
    }
    .add-story-card p.sub {
      color: #888;
      font-size: 14px;
      margin-bottom: 1.8rem;
    }
    .photo-upload-area {
      border: 2px dashed #e2ddd8;
      border-radius: 12px;
      padding: 30px;
      text-align: center;
      cursor: pointer;
      transition: border-color 0.2s, background 0.2s;
      margin-bottom: 1.2rem;
      position: relative;
    }
    .photo-upload-area:hover { border-color: #E05A2B; background: #fdeee8; }
    .photo-upload-area input[type=file] {
      position: absolute; inset: 0; opacity: 0; cursor: pointer;
    }
    .photo-upload-area i { font-size: 2rem; color: #ccc; display: block; margin-bottom: 8px; }
    .photo-upload-area span { font-size: 13px; color: #aaa; }
    #preview-img {
      width: 100%; border-radius: 10px; display: none; margin-bottom: 1.2rem; max-height: 280px; object-fit: cover;
    }
    .form-label { font-weight: 500; color: #1B3A4B; font-size: 14px; }
    .form-control, .form-select {
      border-radius: 10px;
      border: 1.5px solid #e2ddd8;
      font-size: 14px;
      padding: 10px 14px;
    }
    .form-control:focus, .form-select:focus {
      border-color: #E05A2B;
      box-shadow: 0 0 0 3px rgba(224,90,43,0.12);
    }
    .btn-submit {
      background: #E05A2B;
      color: #fff;
      border: none;
      border-radius: 999px;
      padding: 12px 32px;
      font-size: 15px;
      font-weight: 600;
      width: 100%;
      transition: background 0.18s;
    }
    .btn-submit:hover { background: #c94e22; }
    .success-box {
      text-align: center;
      padding: 20px 0;
    }
    .success-box i { font-size: 3rem; color: #E05A2B; display: block; margin-bottom: 1rem; }
  </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="add-story-wrap">
  <div class="add-story-card">
    <?php if ($success): ?>
      <div class="success-box">
        <i class="bi bi-check-circle-fill"></i>
        <h2>Story publiée !</h2>
        <p class="sub">Votre expérience est maintenant visible par tous les voyageurs.</p>
        <a href="stories.php" class="btn-submit" style="display:inline-block;width:auto;text-decoration:none;">Voir les stories</a>
      </div>
    <?php else: ?>
      <h2>Partagez votre expérience</h2>
      <p class="sub">Inspirez d'autres voyageurs avec votre aventure en Tunisie</p>

      <?php if ($error): ?>
        <div class="alert alert-danger" style="border-radius:10px;font-size:14px;"><?= $error ?></div>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data">

        <img id="preview-img" src="" alt="Aperçu" style="width:100%;border-radius:10px;display:none;max-height:280px;object-fit:cover;margin-bottom:10px;">

        <div class="photo-upload-area" id="upload-area">
          <input type="file" name="photo" id="photo-input" accept="image/*" required>
          <i class="bi bi-image"></i>
          <span>Cliquez pour choisir une photo<br><small>JPG, PNG, WEBP · max 5 Mo</small></span>
        </div>

        <button type="button" id="change-btn" style="display:none;background:#fff;border:1.5px solid #E05A2B;color:#E05A2B;border-radius:999px;padding:7px 20px;font-size:13px;font-weight:600;cursor:pointer;margin-bottom:1rem;">
          Changer la photo
        </button>

        <div class="mb-3">
          <label class="form-label">Votre expérience</label>
          <textarea name="caption" class="form-control" rows="3" placeholder="Décrivez votre séjour, ce qui vous a marqué..." required maxlength="500"><?= isset($_POST['caption']) ? htmlspecialchars($_POST['caption']) : '' ?></textarea>
        </div>

        <div class="mb-4">
          <label class="form-label">Région visitée</label>
          <select name="region_id" class="form-select">
            <option value="">— Choisir une région —</option>
            <?php while ($r = mysqli_fetch_assoc($regions)): ?>
              <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['nom']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>

        <button type="submit" class="btn-submit">
          <i class="bi bi-send"></i> Publier ma story
        </button>
      </form>
    <?php endif; ?>
  </div>
</div>

<?php include 'footer.php'; ?>
<script>
const photoInput = document.getElementById('photo-input');
const uploadArea = document.getElementById('upload-area');
const changeBtn = document.getElementById('change-btn');

photoInput.addEventListener('change', function() {
  const file = this.files[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = e => {
    document.getElementById('preview-img').src = e.target.result;
    document.getElementById('preview-img').style.display = 'block';
    uploadArea.style.display = 'none';
    changeBtn.style.display = 'inline-block';
  };
  reader.readAsDataURL(file);
});

changeBtn.addEventListener('click', function() {
  photoInput.value = '';
  document.getElementById('preview-img').style.display = 'none';
  uploadArea.style.display = 'block';
  changeBtn.style.display = 'none';
  photoInput.click();
});
</script>
</body>
</html>
