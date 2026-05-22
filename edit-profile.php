<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Fetch current user data
$result = mysqli_query($conn, "SELECT * FROM utilisateur WHERE id = $user_id");
$user = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom       = mysqli_real_escape_string($conn, trim($_POST['nom'] ?? ''));
    $prenom    = mysqli_real_escape_string($conn, trim($_POST['prenom'] ?? ''));
    $telephone = mysqli_real_escape_string($conn, trim($_POST['telephone'] ?? ''));
    $ville     = mysqli_real_escape_string($conn, trim($_POST['ville'] ?? ''));
    $bio       = mysqli_real_escape_string($conn, trim($_POST['bio'] ?? ''));
    $photo_profil = mysqli_real_escape_string($conn, $user['photo_profil'] ?? '');

    // Handle photo upload
    if (!empty($_FILES['photo']['name'])) {
        $upload_dir = __DIR__ . '/uploads/profils/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $allowed)) {
            $error = "Format non supporté. Utilisez JPG, PNG ou WEBP.";
        } elseif ($_FILES['photo']['size'] > 3 * 1024 * 1024) {
            $error = "L'image ne doit pas dépasser 3 Mo.";
        } elseif ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            $error = "Erreur upload code: " . $_FILES['photo']['error'];
        } else {
            $filename = 'profil_' . $user_id . '_' . time() . '.' . $ext;
            $full_path = $upload_dir . $filename;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $full_path)) {
                $photo_profil = mysqli_real_escape_string($conn, 'uploads/profils/' . $filename);
            } else {
                $error = "move_uploaded_file failed. Check folder permissions on uploads/profils/";
            }
        }
    }

    if (!$error) {
        $sql = "UPDATE utilisateur 
                SET nom='$nom', prenom='$prenom', telephone='$telephone', 
                    ville='$ville', bio='$bio', photo_profil='$photo_profil'
                WHERE id=$user_id";
        mysqli_query($conn, $sql);
        $success = "Profil mis à jour avec succès !";

        // Refresh user data
        $result = mysqli_query($conn, "SELECT * FROM utilisateur WHERE id = $user_id");
        $user = mysqli_fetch_assoc($result);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier mon profil — Tarkina</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        :root { --primary: #E05A2B; --navy: #1B3A4B; --light-bg: #FAF8F5; --text-dark: #1a1a1a; --text-muted: #6b7280; --border: #e5e7eb; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: var(--light-bg); color: var(--text-dark); }
        .navbar { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; background: #fff; border-bottom: 1px solid var(--border); padding: 0 60px; height: 70px; display: flex; align-items: center; justify-content: space-between; }
        .nav-logo img { height: 36px; }
        .nav-logo span { font-size: 1.4rem; font-weight: 800; color: var(--navy); }
        .nav-links { display: flex; gap: 36px; list-style: none; }
        .nav-links a { text-decoration: none; color: var(--text-dark); font-size: 0.95rem; font-weight: 500; transition: color .2s; }
        .nav-links a:hover { color: var(--primary); }
        .nav-auth { display: flex; gap: 12px; align-items: center; }
        .btn-nav-outline { padding: 8px 20px; border: 1.5px solid var(--navy); border-radius: 50px; color: var(--navy); text-decoration: none; font-size: 0.9rem; font-weight: 600; transition: all .2s; }
        .btn-nav-outline:hover { background: var(--navy); color: #fff; }
        .btn-nav-primary { padding: 8px 20px; background: var(--primary); border-radius: 50px; color: #fff; text-decoration: none; font-size: 0.9rem; font-weight: 600; }
        footer { background: var(--navy); color: #fff; padding: 60px 60px 30px; margin-top: 60px; }
        .footer-grid { display: grid; grid-template-columns: 1.4fr 1fr 1fr 1fr; gap: 48px; margin-bottom: 48px; }
        .footer-brand-name { font-size: 1.6rem; font-weight: 800; margin-bottom: 12px; }
        .footer-brand-desc { color: rgba(255,255,255,0.6); font-size: 0.9rem; line-height: 1.7; margin-bottom: 20px; }
        .footer-col h4 { font-size: 0.78rem; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: rgba(255,255,255,0.5); margin-bottom: 20px; }
        .footer-col ul { list-style: none; }
        .footer-col ul li { margin-bottom: 10px; }
        .footer-col ul li a { color: rgba(255,255,255,0.75); text-decoration: none; font-size: 0.9rem; }
        .footer-divider { border: none; border-top: 1px solid rgba(255,255,255,0.1); margin-bottom: 24px; }
        .footer-bottom { display: flex; justify-content: space-between; font-size: 0.85rem; color: rgba(255,255,255,0.4); }
        .footer-watermark { text-align: center; font-size: 8vw; font-weight: 800; color: rgba(255,255,255,0.04); line-height: 1; margin-bottom: -10px; }

        .edit-profile-wrap {
            max-width: 640px;
            margin: 100px auto 60px;
            padding: 0 20px;
        }
        .edit-card {
            background: #fff;
            border-radius: 18px;
            padding: 40px;
            box-shadow: 0 4px 24px rgba(27,58,75,0.09);
        }
        .edit-card h1 {
            color: var(--navy);
            font-size: 1.6rem;
            margin-bottom: 28px;
        }
        .avatar-upload {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 28px;
        }
        .avatar-upload img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary);
        }
        .avatar-placeholder {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #fdeee8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--primary);
            border: 3px solid var(--primary);
        }
        .avatar-upload label.upload-btn {
            background: var(--light-bg);
            border: 1.5px dashed #ccc;
            border-radius: 10px;
            padding: 10px 18px;
            cursor: pointer;
            font-size: 0.9rem;
            color: var(--navy);
            transition: border-color 0.2s;
        }
        .avatar-upload label.upload-btn:hover { border-color: var(--primary); }
        .avatar-upload input[type="file"] { display: none; }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--navy);
            margin-bottom: 6px;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid #e2ddd8;
            border-radius: 10px;
            font-size: 0.95rem;
            font-family: inherit;
            transition: border-color 0.2s;
            box-sizing: border-box;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
        }
        .form-group textarea { resize: vertical; min-height: 90px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .btn-save {
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 13px 32px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            margin-top: 8px;
            transition: background 0.2s;
        }
        .btn-save:hover { background: #c44d22; }
        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        .alert-error {
            background: #fdeee8;
            color: #c44d22;
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
<nav class="navbar">
    <a href="index.php" class="nav-logo">
        <img src="assets/img/logo.png" alt="TARKINA" onerror="this.style.display='none';this.nextElementSibling.style.display='inline'">
        <span style="display:none">Tarkina</span>
    </a>
    <ul class="nav-links">
        <li><a href="index.php">Accueil</a></li>
        <li><a href="explorer.php">Explorer</a></li>
        <li><a href="about.php">À propos</a></li>
        <li><a href="contact.php">Contact</a></li>
    </ul>
    <div class="nav-auth">
        <a href="profile.php" class="btn-nav-primary">Mon Profil</a>
        <a href="logout.php" class="btn-nav-outline">Déconnexion</a>
    </div>
</nav>

<button onclick="history.back()" 
  style="background:none;border:none;cursor:pointer;font-size:1.3rem;
  color:#1B3A4B;padding:14px 0 0 24px;display:flex;align-items:center;gap:6px;"
  onmouseover="this.style.color='#E05A2B'" 
  onmouseout="this.style.color='#1B3A4B'">
  &#8592;
</button>

<div class="edit-profile-wrap">
    <div class="edit-card">
        <h1>Modifier mon profil</h1>

        <?php if ($success): ?>
            <div class="alert-success">✅ <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <!-- Avatar -->
            <div class="avatar-upload">
                <?php if (!empty($user['photo_profil'])): ?>
                    <img src="<?= htmlspecialchars($user['photo_profil']) ?>" alt="Photo de profil">
                <?php else: ?>
                    <div class="avatar-placeholder">👤</div>
                <?php endif; ?>
                <div>
                    <label class="upload-btn" for="photo">📷 Changer la photo</label>
                    <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp">
                    <div style="font-size:0.78rem;color:#999;margin-top:5px;">JPG, PNG ou WEBP · max 3 Mo</div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Prénom</label>
                    <input type="text" name="prenom" value="<?= htmlspecialchars($user['prenom'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Nom</label>
                    <input type="text" name="nom" value="<?= htmlspecialchars($user['nom'] ?? '') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="tel" name="telephone" placeholder="+216 XX XXX XXX"
                           value="<?= htmlspecialchars($user['telephone'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Ville</label>
                    <input type="text" name="ville" placeholder="Tunis, Sfax…"
                           value="<?= htmlspecialchars($user['ville'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Bio</label>
                <textarea name="bio" placeholder="Parlez-nous de vous…"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn-save">Enregistrer les modifications</button>
        </form>
    </div>
</div>

<footer>
    <div class="footer-grid">
        <div>
            <div class="footer-brand-name">Tarkina</div>
            <p class="footer-brand-desc">Découvrez la Tunisie cachée à travers ses habitants, ses saveurs et son artisanat.</p>
        </div>
        <div class="footer-col">
            <h4>Explorer</h4>
            <ul>
                <li><a href="explorer.php">Toutes les régions</a></li>
                <li><a href="search.php">Hébergements</a></li>
                <li><a href="search.php">Repas maison</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Compte</h4>
            <ul>
                <li><a href="mes-reservations.php">Mes réservations</a></li>
                <li><a href="mes-favoris.php">Mes favoris</a></li>
                <li><a href="edit-profile.php">Modifier le profil</a></li>
                <li><a href="logout.php">Déconnexion</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Contact</h4>
            <div style="color:rgba(255,255,255,0.75);font-size:0.9rem;line-height:2;">
                📍 Tunis, Tunisie<br>
                ✉️ hello@tarkina.tn<br>
                📞 +216 71 000 000
            </div>
        </div>
    </div>
    <div class="footer-watermark">TARKINA</div>
    <hr class="footer-divider">
    <div class="footer-bottom">
        <span>© 2026 Tarkina — Voyagez autrement en Tunisie.</span>
    </div>
</footer>
</body>
</html>