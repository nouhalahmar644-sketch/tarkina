<?php
session_start();
require 'db.php';
require_once __DIR__ . '/includes/i18n.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$L_ALL = [
    'fr' => [
        'page_title'   => 'Modifier mon profil — Tarkina',
        'heading'      => 'Modifier mon profil',
        'photo_alt'    => 'Photo de profil',
        'change_photo' => 'Changer la photo',
        'photo_hint'   => 'JPG, PNG ou WEBP · max 3 Mo',
        'prenom'       => 'Prénom',
        'nom'          => 'Nom',
        'telephone'    => 'Téléphone',
        'ph_phone'     => '+216 XX XXX XXX',
        'ville'        => 'Ville',
        'ph_city'      => 'Tunis, Sfax…',
        'bio'          => 'Bio',
        'ph_bio'       => 'Parlez-nous de vous…',
        'save'         => 'Enregistrer les modifications',
        'err_format'   => 'Format non supporté. Utilisez JPG, PNG ou WEBP.',
        'err_size'     => "L'image ne doit pas dépasser 3 Mo.",
        'err_upload'   => 'Erreur upload code: ',
        'err_move'     => 'move_uploaded_file failed. Check folder permissions on uploads/profils/',
        'ok_save'      => 'Profil mis à jour avec succès !',
    ],
    'ar' => [
        'page_title'   => 'تعديل ملفي الشخصي — تاركينا',
        'heading'      => 'تعديل ملفي الشخصي',
        'photo_alt'    => 'صورة الملف الشخصي',
        'change_photo' => 'تغيير الصورة',
        'photo_hint'   => 'JPG أو PNG أو WEBP · الحد الأقصى 3 ميغابايت',
        'prenom'       => 'اللقب',
        'nom'          => 'الاسم',
        'telephone'    => 'الهاتف',
        'ph_phone'     => '+216 XX XXX XXX',
        'ville'        => 'المدينة',
        'ph_city'      => 'تونس، صفاقس…',
        'bio'          => 'نبذة',
        'ph_bio'       => 'حدّثنا عن نفسك…',
        'save'         => 'حفظ التعديلات',
        'err_format'   => 'تنسيق غير مدعوم. استخدم JPG أو PNG أو WEBP.',
        'err_size'     => 'يجب ألاّ تتجاوز الصورة 3 ميغابايت.',
        'err_upload'   => 'خطأ في الرفع، رمز: ',
        'err_move'     => 'فشل نقل الملف. تحقّق من صلاحيات المجلد uploads/profils/',
        'ok_save'      => 'تمّ تحديث الملف الشخصي بنجاح!',
    ],
    'en' => [
        'page_title'   => 'Edit my profile — Tarkina',
        'heading'      => 'Edit my profile',
        'photo_alt'    => 'Profile photo',
        'change_photo' => 'Change photo',
        'photo_hint'   => 'JPG, PNG or WEBP · max 3 MB',
        'prenom'       => 'First name',
        'nom'          => 'Last name',
        'telephone'    => 'Phone',
        'ph_phone'     => '+216 XX XXX XXX',
        'ville'        => 'City',
        'ph_city'      => 'Tunis, Sfax…',
        'bio'          => 'Bio',
        'ph_bio'       => 'Tell us about yourself…',
        'save'         => 'Save changes',
        'err_format'   => 'Unsupported format. Use JPG, PNG or WEBP.',
        'err_size'     => 'The image must not exceed 3 MB.',
        'err_upload'   => 'Upload error code: ',
        'err_move'     => 'move_uploaded_file failed. Check folder permissions on uploads/profils/',
        'ok_save'      => 'Profile updated successfully!',
    ],
];
$L = $L_ALL[$lang] ?? $L_ALL['fr'];

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
            $error = $L['err_format'];
        } elseif ($_FILES['photo']['size'] > 3 * 1024 * 1024) {
            $error = $L['err_size'];
        } elseif ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            $error = $L['err_upload'] . $_FILES['photo']['error'];
        } else {
            $filename = 'profil_' . $user_id . '_' . time() . '.' . $ext;
            $full_path = $upload_dir . $filename;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $full_path)) {
                $photo_profil = mysqli_real_escape_string($conn, 'uploads/profils/' . $filename);
            } else {
                $error = $L['err_move'];
            }
        }
    }

    if (!$error) {
        $sql = "UPDATE utilisateur
                SET nom='$nom', prenom='$prenom', telephone='$telephone',
                    ville='$ville', bio='$bio', photo_profil='$photo_profil'
                WHERE id=$user_id";
        mysqli_query($conn, $sql);
        $success = $L['ok_save'];

        // Refresh user data
        $result = mysqli_query($conn, "SELECT * FROM utilisateur WHERE id = $user_id");
        $user = mysqli_fetch_assoc($result);
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
    <style>
        :root { --primary: #f16e22; --navy: #0b1c30; --light-bg: #FFFFFF; --text-dark: #1a1a1a; --text-muted: #6b7280; --border: #e5e7eb; }
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
            box-shadow: 0 4px 24px rgba(17, 17, 17,0.09);
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
            background: #e8f5ef;
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
        .btn-save:hover { background: #d95716; }
        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
<?php $navLight = true; include 'navbar.php'; ?>
<link rel="stylesheet" href="assets/css/service-page.css">

<button onclick="history.back()" class="tk-back-fab">&#8592; <?= htmlspecialchars($L['back'] ?? 'Retour') ?></button>

<div class="edit-profile-wrap">
    <div class="edit-card">
        <h1><?= htmlspecialchars($L['heading']) ?></h1>

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
                    <img src="<?= htmlspecialchars($user['photo_profil']) ?>" alt="<?= htmlspecialchars($L['photo_alt']) ?>">
                <?php else: ?>
                    <div class="avatar-placeholder">👤</div>
                <?php endif; ?>
                <div>
                    <label class="upload-btn" for="photo">📷 <?= htmlspecialchars($L['change_photo']) ?></label>
                    <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp">
                    <div style="font-size:0.78rem;color:#999;margin-top:5px;"><?= htmlspecialchars($L['photo_hint']) ?></div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><?= htmlspecialchars($L['prenom']) ?></label>
                    <input type="text" name="prenom" value="<?= htmlspecialchars($user['prenom'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label><?= htmlspecialchars($L['nom']) ?></label>
                    <input type="text" name="nom" value="<?= htmlspecialchars($user['nom'] ?? '') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><?= htmlspecialchars($L['telephone']) ?></label>
                    <input type="tel" name="telephone" placeholder="<?= htmlspecialchars($L['ph_phone']) ?>"
                           value="<?= htmlspecialchars($user['telephone'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label><?= htmlspecialchars($L['ville']) ?></label>
                    <input type="text" name="ville" placeholder="<?= htmlspecialchars($L['ph_city']) ?>"
                           value="<?= htmlspecialchars($user['ville'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label><?= htmlspecialchars($L['bio']) ?></label>
                <textarea name="bio" placeholder="<?= htmlspecialchars($L['ph_bio']) ?>"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn-save"><?= htmlspecialchars($L['save']) ?></button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
