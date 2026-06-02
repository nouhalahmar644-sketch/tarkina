<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/session_bootstrap.php';
require_once __DIR__ . '/includes/i18n.php';

$L_ALL = [
    'fr' => [
        'page_title' => 'Tarkina — Créer un compte',
        'tagline' => 'LA TUNISIE HORS DES SENTIERS BATTUS',
        'quote_a' => 'Découvrez la', 'quote_b' => 'vraie', 'quote_c' => 'Tunisie.',
        'sub' => 'Hébergement, repas maison, guides locaux, artisanat & événements dans les régions oubliées du pays.',
        'h1' => 'Créer un compte',
        'success_msg' => 'Inscription réussie. Vous pouvez maintenant vous connecter.',
        'go_login' => 'Aller à la connexion',
        'lbl_nom' => 'Nom', 'lbl_prenom' => 'Prénom', 'lbl_email' => 'Email',
        'lbl_pass' => 'Mot de passe', 'lbl_adresse' => 'Adresse',
        'btn' => 'Créer un compte',
        'footer_q' => 'Vous avez déjà un compte ?', 'footer_a' => 'Se connecter',
        'err_nom' => 'Le nom est obligatoire.',
        'err_prenom' => 'Le prénom est obligatoire.',
        'err_email_req' => "L'e-mail est obligatoire.",
        'err_email_fmt' => "L'e-mail n'est pas au bon format.",
        'err_pass_req' => 'Le mot de passe est obligatoire.',
        'err_pass_len' => 'Le mot de passe doit contenir au moins 6 caractères.',
        'err_adresse' => "L'adresse est obligatoire.",
        'err_email_dup' => 'Cet e-mail est déjà utilisé.',
    ],
    'ar' => [
        'page_title' => 'تاركينا — إنشاء حساب',
        'tagline' => 'تونس خارج المسارات المعتادة',
        'quote_a' => 'اكتشف', 'quote_b' => 'تونس', 'quote_c' => 'الحقيقية.',
        'sub' => 'إقامة، وجبات منزلية، مرشدون محليون، حِرف وفعاليات في جهات تونس المنسيّة.',
        'h1' => 'إنشاء حساب',
        'success_msg' => 'تمّ التسجيل بنجاح. يمكنك الآن تسجيل الدخول.',
        'go_login' => 'الانتقال إلى تسجيل الدخول',
        'lbl_nom' => 'اللقب', 'lbl_prenom' => 'الاسم', 'lbl_email' => 'البريد الإلكتروني',
        'lbl_pass' => 'كلمة المرور', 'lbl_adresse' => 'العنوان',
        'btn' => 'إنشاء حساب',
        'footer_q' => 'هل لديك حساب بالفعل؟', 'footer_a' => 'تسجيل الدخول',
        'err_nom' => 'اللقب مطلوب.',
        'err_prenom' => 'الاسم مطلوب.',
        'err_email_req' => 'البريد الإلكتروني مطلوب.',
        'err_email_fmt' => 'صيغة البريد الإلكتروني غير صحيحة.',
        'err_pass_req' => 'كلمة المرور مطلوبة.',
        'err_pass_len' => 'يجب أن تحتوي كلمة المرور على 6 أحرف على الأقل.',
        'err_adresse' => 'العنوان مطلوب.',
        'err_email_dup' => 'هذا البريد الإلكتروني مستخدم بالفعل.',
    ],
    'en' => [
        'page_title' => 'Tarkina — Create an account',
        'tagline' => 'TUNISIA OFF THE BEATEN PATH',
        'quote_a' => 'Discover the', 'quote_b' => 'real', 'quote_c' => 'Tunisia.',
        'sub' => 'Stays, home meals, local guides, crafts & events in the forgotten regions of the country.',
        'h1' => 'Create an account',
        'success_msg' => 'Sign-up successful. You can now log in.',
        'go_login' => 'Go to sign in',
        'lbl_nom' => 'Surname', 'lbl_prenom' => 'First name', 'lbl_email' => 'Email',
        'lbl_pass' => 'Password', 'lbl_adresse' => 'Address',
        'btn' => 'Create account',
        'footer_q' => 'Already have an account?', 'footer_a' => 'Sign in',
        'err_nom' => 'Surname is required.',
        'err_prenom' => 'First name is required.',
        'err_email_req' => 'Email is required.',
        'err_email_fmt' => "Email format isn't valid.",
        'err_pass_req' => 'Password is required.',
        'err_pass_len' => 'Password must be at least 6 characters.',
        'err_adresse' => 'Address is required.',
        'err_email_dup' => 'This email is already used.',
    ],
];
$L = $L_ALL[$lang] ?? $L_ALL['fr'];

$page_title = $L['h1'];
$success = false;

$fieldErrors = [
    'nom' => '',
    'prenom' => '',
    'email' => '',
    'password' => '',
    'adresse' => ''
];

$nom = '';
$prenom = '';
$email = '';
$adresse = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
    $prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $adresse = isset($_POST['adresse']) ? $_POST['adresse'] : '';

    if ($nom === '') {
        $fieldErrors['nom'] = $L['err_nom'];
    }
    if ($prenom === '') {
        $fieldErrors['prenom'] = $L['err_prenom'];
    }
    if ($email === '') {
        $fieldErrors['email'] = $L['err_email_req'];
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $fieldErrors['email'] = $L['err_email_fmt'];
    }
    if ($password === '') {
        $fieldErrors['password'] = $L['err_pass_req'];
    } elseif (strlen($password) < 6) {
        $fieldErrors['password'] = $L['err_pass_len'];
    }
    if ($adresse === '') {
        $fieldErrors['adresse'] = $L['err_adresse'];
    }

    if (!array_filter($fieldErrors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $role = 'utilisateur';

        $sql = 'INSERT INTO utilisateur (nom, prenom, email, mot_de_passe,adresse, role) VALUES (?, ?, ?, ?,?, ?)';
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt !== false) {
            mysqli_stmt_bind_param($stmt, 'ssssss', $nom, $prenom, $email, $hash, $adresse, $role);

            if (mysqli_stmt_execute($stmt)) {
                $success = true;
            } else {
                if (mysqli_errno($conn) === 1062) {
                    $fieldErrors['email'] = $L['err_email_dup'];
                }
            }
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
    max-width: 960px;
    min-height: 620px;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 40px 80px rgba(0,0,0,0.4);
  }
 
  .auth-image {
    flex: 1;
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 40px;
    min-width: 340px;
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
 
  .auth-logo span {
    color: var(--coral);
  }
 
  .auth-tagline {
    color: rgba(255,255,255,0.6);
    font-size: 13px;
    font-weight: 300;
    letter-spacing: 0.5px;
    margin-bottom: 32px;
  }
 
  .auth-quote {
    font-family: 'Playfair Display', serif;
    font-size: 22px;
    font-weight: 600;
    color: var(--white);
    line-height: 1.4;
    margin-bottom: 12px;
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
  }

  .form-alert.error {
    color: #b43737;
  }

  .form-alert.success {
    color: #1f7a43;
  }

  .error-text {
    display: block;
    margin-top: 6px;
    font-size: 12px;
    color: #b43737;
  }

  .input-error {
    border-color: #b43737 !important;
    background: #fff5f5;
  }
 
  .btn-google {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 13px;
    border: 1.5px solid var(--border);
    border-radius: 10px;
    background: var(--white);
    color: var(--charcoal);
    font-family: 'DM Sans', sans-serif;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    margin-bottom: 24px;
    text-decoration: none;
  }
 
  .btn-google:hover {
    border-color: var(--coral);
    background: var(--cream);
  }
 
  .btn-google img {
    width: 18px;
    height: 18px;
  }
 
  .divider {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 24px;
    color: var(--grey);
    font-size: 12px;
    letter-spacing: 0.5px;
  }
 
  .divider::before,
  .divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--border);
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
 
  .btn-submit {
    width: 100%;
    padding: 15px;
    background: var(--coral);
    color: var(--white);
    border: none;
    border-radius: 50px;
    font-family: 'DM Sans', sans-serif;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.25s ease;
    margin-top: 8px;
    letter-spacing: 0.3px;
  }
 
  .btn-submit:hover {
    background: var(--coral-light);
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(27, 107, 69,0.3);
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
 
  <div class="auth-image">
    <div class="auth-image-content">
      <a href="index.php" class="auth-logo" style="text-decoration:none;display:inline-block;">Tarkina<span>.</span></a>
      <div class="auth-tagline"><?= htmlspecialchars($L['tagline']) ?></div>
      <div class="auth-quote"><?= htmlspecialchars($L['quote_a']) ?> <span><?= htmlspecialchars($L['quote_b']) ?></span> <?= htmlspecialchars($L['quote_c']) ?></div>
      <div class="auth-sub"><?= htmlspecialchars($L['sub']) ?></div>
    </div>
  </div>
 
  <div class="auth-form-panel">
    <div class="form-header">
      <h1><?= htmlspecialchars($L['h1']) ?></h1>
      <?php if ($success): ?>
        <p class="form-alert success"><?= htmlspecialchars($L['success_msg']) ?></p>
      <?php endif; ?>
    </div>

    <?php if ($success): ?>
      <a href="login.php" class="btn-submit" style="display:block; text-align:center; text-decoration:none;"><?= htmlspecialchars($L['go_login']) ?></a>
    <?php else: ?>
    <form method="post" action="register.php" novalidate>
      <div class="form-group">
        <label><?= htmlspecialchars($L['lbl_nom']) ?></label>
        <input type="text" name="nom" value="<?php echo htmlspecialchars($nom); ?>" class="<?php echo !empty($fieldErrors['nom']) ? 'input-error' : ''; ?>" required>
        <?php if (!empty($fieldErrors['nom'])): ?>
          <small class="error-text"><?php echo $fieldErrors['nom']; ?></small>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <label><?= htmlspecialchars($L['lbl_prenom']) ?></label>
        <input type="text" name="prenom" value="<?php echo htmlspecialchars($prenom); ?>" class="<?php echo !empty($fieldErrors['prenom']) ? 'input-error' : ''; ?>" required>
        <?php if (!empty($fieldErrors['prenom'])): ?>
          <small class="error-text"><?php echo $fieldErrors['prenom']; ?></small>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <label><?= htmlspecialchars($L['lbl_email']) ?></label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" class="<?php echo !empty($fieldErrors['email']) ? 'input-error' : ''; ?>" required>
        <?php if (!empty($fieldErrors['email'])): ?>
          <small class="error-text"><?php echo $fieldErrors['email']; ?></small>
        <?php endif; ?>
      </div>
 
      <div class="form-group">
        <label><?= htmlspecialchars($L['lbl_pass']) ?></label>
        <div class="input-wrapper">
          <input type="password" name="password" id="pass1" class="<?php echo !empty($fieldErrors['password']) ? 'input-error' : ''; ?>" required minlength="6">
          <?php if (!empty($fieldErrors['password'])): ?>
            <small class="error-text"><?php echo $fieldErrors['password']; ?></small>
          <?php endif; ?>
          <button type="button" class="toggle-pass" onclick="togglePass('pass1')">👁</button>
        </div>
      </div>

      <div class="form-group">
        <label><?= htmlspecialchars($L['lbl_adresse']) ?></label>
        <input type="text" name="adresse" value="<?php echo htmlspecialchars($adresse); ?>" class="<?php echo !empty($fieldErrors['adresse']) ? 'input-error' : ''; ?>" required>
        <?php if (!empty($fieldErrors['adresse'])): ?>
          <small class="error-text"><?php echo $fieldErrors['adresse']; ?></small>
        <?php endif; ?>
      </div>
 
      <button type="submit" class="btn-submit"><?= htmlspecialchars($L['btn']) ?></button>
    </form>
    <?php endif; ?>
 
    <div class="form-footer">
      <?= htmlspecialchars($L['footer_q']) ?> <a href="login.php"><?= htmlspecialchars($L['footer_a']) ?></a>
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
