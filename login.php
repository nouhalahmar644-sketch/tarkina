<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/session_bootstrap.php';
require_once __DIR__ . '/includes/i18n.php';

$L_ALL = [
    'fr' => [
        'page_title' => 'Tarkina — Se connecter',
        'tagline' => 'LA TUNISIE HORS DES SENTIERS BATTUS',
        'quote_a' => 'Bienvenue', 'quote_b' => 'à nouveau.',
        'auth_sub' => 'Retrouvez vos réservations, vos favoris et vos expériences authentiques en un clic.',
        'h1' => 'Se connecter',
        'lbl_email' => 'Email', 'ph_email' => 'Entrez votre adresse e-mail',
        'lbl_pass' => 'Mot de passe', 'ph_pass' => 'Votre mot de passe',
        'forgot' => 'Mot de passe oublié ?',
        'btn' => 'Se connecter',
        'footer_q' => 'Nouveau sur Tarkina ?', 'footer_a' => 'Créer un compte',
        'err_empty' => 'Veuillez remplir tous les champs.',
        'err_invalid' => 'E-mail invalide.',
        'err_wrong' => 'E-mail ou mot de passe incorrect.',
        'err_prep' => 'Erreur de préparation : ',
    ],
    'ar' => [
        'page_title' => 'تاركينا — تسجيل الدخول',
        'tagline' => 'تونس خارج المسارات المعتادة',
        'quote_a' => 'مرحبًا بعودتك', 'quote_b' => 'من جديد.',
        'auth_sub' => 'استعد حجوزاتك ومفضلاتك وتجاربك الأصيلة بنقرة واحدة.',
        'h1' => 'تسجيل الدخول',
        'lbl_email' => 'البريد الإلكتروني', 'ph_email' => 'أدخل عنوان بريدك الإلكتروني',
        'lbl_pass' => 'كلمة المرور', 'ph_pass' => 'كلمة المرور الخاصة بك',
        'forgot' => 'هل نسيت كلمة المرور؟',
        'btn' => 'تسجيل الدخول',
        'footer_q' => 'جديد في تاركينا؟', 'footer_a' => 'إنشاء حساب',
        'err_empty' => 'يُرجى ملء جميع الحقول.',
        'err_invalid' => 'بريد إلكتروني غير صالح.',
        'err_wrong' => 'البريد الإلكتروني أو كلمة المرور غير صحيحين.',
        'err_prep' => 'خطأ أثناء التحضير: ',
    ],
    'en' => [
        'page_title' => 'Tarkina — Sign in',
        'tagline' => 'TUNISIA OFF THE BEATEN PATH',
        'quote_a' => 'Welcome', 'quote_b' => 'back.',
        'auth_sub' => 'Find your bookings, favourites and authentic experiences in one click.',
        'h1' => 'Sign in',
        'lbl_email' => 'Email', 'ph_email' => 'Enter your email address',
        'lbl_pass' => 'Password', 'ph_pass' => 'Your password',
        'forgot' => 'Forgot your password?',
        'btn' => 'Sign in',
        'footer_q' => 'New to Tarkina?', 'footer_a' => 'Create an account',
        'err_empty' => 'Please fill in all fields.',
        'err_invalid' => 'Invalid email.',
        'err_wrong' => 'Wrong email or password.',
        'err_prep' => 'Preparation error: ',
    ],
];
$L = $L_ALL[$lang] ?? $L_ALL['fr'];

// Si déjà connecté, rediriger selon le rôle
if (!empty($_SESSION['user_id'])) {
    if (!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: index.php');
    }
    exit;
}

$page_title = 'Connexion';
$errors = [];
$email = '';
$successMessage = '';

if (!empty($_SESSION['auth_success'])) {
    $successMessage = (string) $_SESSION['auth_success'];
    unset($_SESSION['auth_success']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ($email === '' || $password === '') {
        $errors[] = $L['err_empty'];
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = $L['err_invalid'];
    } else {
        // Requête préparée : récupérer l'utilisateur par e-mail
        $sql = 'SELECT id, nom, prenom, email, mot_de_passe, role FROM utilisateur WHERE email = ? LIMIT 1';
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt === false) {
            $errors[] = $L['err_prep'] . mysqli_error($conn);
        } else {
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            // bind_result fonctionne sans l'extension mysqlnd (compatible hébergements variés)
            mysqli_stmt_bind_result($stmt, $uid, $nom_db, $prenom_db, $email_db, $hash_db, $role_db);
            $found = mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            if (!$found || !password_verify($password, $hash_db)) {
                $errors[] = $L['err_wrong'];
            } else {
                // Évite le vol de session : nouvel identifiant de session après login
                session_regenerate_id(true);

                $_SESSION['user_id'] = (int) $uid;
                // "Nom" pour l'affichage : prénom + nom
                $_SESSION['user_name'] = trim($prenom_db . ' ' . $nom_db);
                $_SESSION['user_role'] = $role_db;

                if ($role_db === 'admin') {
                    header('Location: admin/dashboard.php');
                } else {
                    header('Location: index.php');
                }
                exit;
            }
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
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/typography.css">
<link rel="stylesheet" href="assets/css/rtl.css">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
 
  :root {
    --navy: #0b1c30;
    --coral: #f16e22;
    --coral-light: #f07355;
    --cream: #FFFFFF;
    --charcoal: #2D2D2D;
    --grey: #8A8A8A;
    --grey-light: #F0EDE8;
    --border: #E2DDD6;
    --white: #ffffff;
  }
 
  body {
    font-family: 'DM Sans', sans-serif;
    background: var(--charcoal);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
  }
 
  .auth-container {
    display: flex;
    width: 100%;
    max-width: 900px;
    min-height: 560px;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 40px 80px rgba(0,0,0,0.4);
  }
 
  /* LEFT — image panel */
  .auth-image {
    flex: 1;
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 40px;
    min-width: 300px;
  }
 
  .auth-image::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url('assets/loginimage.png') center center / cover no-repeat;
    transform: scale(1.02);
  }
 
  .auth-image::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(7,11,20,0.38) 0%, rgba(9,15,29,0.56) 45%, rgba(7,12,22,0.84) 100%);
  }
 
  .auth-image-content {
    position: relative;
    z-index: 2;
  }
 
  .auth-logo {
    font-family: 'Playfair Display', serif;
    font-size: 28px;
    font-weight: 700;
    color: var(--white);
    letter-spacing: -0.5px;
    margin-bottom: 4px;
  }
 
  .auth-logo span { color: var(--coral); }
 
  .auth-tagline {
    color: rgba(255,255,255,0.6);
    font-size: 13px;
    font-weight: 300;
    letter-spacing: 0.5px;
    margin-bottom: 32px;
  }
 
  .auth-quote {
    font-family: 'Playfair Display', serif;
    font-size: 20px;
    font-weight: 600;
    color: var(--white);
    line-height: 1.4;
    margin-bottom: 10px;
  }
 
  .auth-quote span {
    color: var(--coral);
    font-style: italic;
  }
 
  .auth-sub {
    color: rgba(255,255,255,0.55);
    font-size: 13px;
    line-height: 1.6;
  }
 
  /* RIGHT — form panel */
  .auth-form-panel {
    flex: 1.1;
    background: var(--white);
    padding: 52px 48px;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }
 
  .form-header {
    margin-bottom: 32px;
  }
 
  .form-header h1 {
    font-family: 'Playfair Display', serif;
    font-size: 28px;
    font-weight: 700;
    color: var(--navy);
    margin-bottom: 8px;
  }
 
  .form-header p {
    color: var(--grey);
    font-size: 14px;
    line-height: 1.5;
  }

  .form-alert {
    margin-top: 12px;
    font-size: 13px;
    line-height: 1.4;
    color: #b43737;
  }

  .form-alert.success {
    color: #1f7a43;
  }
 
  .form-group {
    margin-bottom: 18px;
  }
 
  .form-group label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: var(--navy);
    letter-spacing: 0.5px;
    text-transform: uppercase;
    margin-bottom: 7px;
  }
 
  .input-wrapper {
    position: relative;
  }
 
  .form-group input {
    width: 100%;
    padding: 13px 16px;
    border: 1.5px solid var(--border);
    border-radius: 10px;
    font-family: 'DM Sans', sans-serif;
    font-size: 14px;
    color: var(--charcoal);
    background: var(--cream);
    outline: none;
    transition: all 0.2s ease;
  }
 
  .form-group input::placeholder {
    color: #BDBDBD;
  }
 
  .form-group input:focus {
    border-color: var(--coral);
    background: var(--white);
    box-shadow: 0 0 0 4px rgba(27, 107, 69,0.08);
  }
 
  .toggle-pass {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    color: var(--grey);
    font-size: 16px;
    padding: 0;
  }
 
  .forgot-link {
    display: block;
    text-align: right;
    font-size: 12px;
    color: var(--coral);
    text-decoration: none;
    font-weight: 500;
    margin-top: 6px;
  }
 
  .forgot-link:hover {
    text-decoration: underline;
  }
 
  .btn-submit {
    width: 100%;
    padding: 15px;
    background: var(--navy);
    color: var(--white);
    border: none;
    border-radius: 50px;
    font-family: 'DM Sans', sans-serif;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.25s ease;
    margin-top: 20px;
    letter-spacing: 0.3px;
  }
 
  .btn-submit:hover {
    background: #243660;
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(27,42,74,0.25);
  }
 
  .btn-submit:active {
    transform: translateY(0);
  }
 
  .form-footer {
    text-align: center;
    margin-top: 22px;
    font-size: 13px;
    color: var(--grey);
  }
 
  .form-footer a {
    color: var(--coral);
    font-weight: 600;
    text-decoration: none;
  }
 
  .form-footer a:hover {
    text-decoration: underline;
  }
 
  .social-links {
    display: flex;
    justify-content: center;
    gap: 14px;
    margin-top: 20px;
  }
 
  .social-links a {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 1.5px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--grey);
    font-size: 14px;
    text-decoration: none;
    transition: all 0.2s ease;
  }
 
  .social-links a:hover {
    border-color: var(--coral);
    color: var(--coral);
    background: rgba(27, 107, 69,0.06);
  }
 
  @media (max-width: 700px) {
    .auth-image { display: none; }
    .auth-form-panel { padding: 40px 28px; }
  }
</style>
</head>
<body>
 
<div class="auth-container">
 
  <!-- LEFT -->
  <div class="auth-image">
    <div class="auth-image-content">
      <a href="index.php" class="auth-logo" style="text-decoration:none;display:inline-block;">Tarkina<span>.</span></a>
      <div class="auth-tagline"><?= htmlspecialchars($L['tagline']) ?></div>
      <div class="auth-quote"><?= htmlspecialchars($L['quote_a']) ?> <span><?= htmlspecialchars($L['quote_b']) ?></span></div>
      <div class="auth-sub"><?= htmlspecialchars($L['auth_sub']) ?></div>
    </div>
  </div>
 
  <!-- RIGHT -->
  <div class="auth-form-panel">
    <div class="form-header">
      <h1><?= htmlspecialchars($L['h1']) ?></h1>
      <p>Retrouvez vos réservations, vos favoris et vos expériences authentiques en un clic.</p>
      <?php if ($successMessage !== ''): ?>
        <p class="form-alert success"><?php echo htmlspecialchars($successMessage); ?></p>
      <?php endif; ?>
      <?php if (!empty($errors)): ?>
        <p class="form-alert">
          <?php foreach ($errors as $e): ?>
            <?php echo htmlspecialchars($e); ?><br>
          <?php endforeach; ?>
        </p>
      <?php endif; ?>
    </div>
 
    <form method="post" action="login.php" novalidate>
      <div class="form-group">
        <label><?= htmlspecialchars($L['lbl_email']) ?></label>
        <input type="email" name="email" placeholder="<?= htmlspecialchars($L['ph_email']) ?>" value="<?php echo htmlspecialchars($email); ?>" required>
      </div>
 
      <div class="form-group">
        <label><?= htmlspecialchars($L['lbl_pass']) ?></label>
        <div class="input-wrapper">
          <input type="password" name="password" placeholder="<?= htmlspecialchars($L['ph_pass']) ?>" id="pass1" required>
          <button type="button" class="toggle-pass" onclick="togglePass('pass1')">👁</button>
        </div>
        <a href="forgot-password.php" class="forgot-link"><?= htmlspecialchars($L['forgot']) ?></a>
      </div>
 
      <button type="submit" class="btn-submit"><?= htmlspecialchars($L['btn']) ?></button>
    </form>
 
    <div class="form-footer">
      <?= htmlspecialchars($L['footer_q']) ?> <a href="register.php"><?= htmlspecialchars($L['footer_a']) ?></a>
    </div>
 
    <div class="social-links">
      <a href="https://facebook.com" target="_blank" rel="noopener" title="Facebook">f</a>
      <a href="https://twitter.com" target="_blank" rel="noopener" title="X">𝕏</a>
      <a href="https://instagram.com" target="_blank" rel="noopener" title="Instagram">📷</a>
      <a href="https://linkedin.com" target="_blank" rel="noopener" title="LinkedIn">in</a>
    </div>
  </div>
 
</div>
 
<script>
  function togglePass(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
  }
</script>
 
</body>
</html>

