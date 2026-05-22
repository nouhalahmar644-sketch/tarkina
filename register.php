<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/session_bootstrap.php';

$page_title = 'Inscription';
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
        $fieldErrors['nom'] = 'Le nom est obligatoire.';
    }
    if ($prenom === '') {
        $fieldErrors['prenom'] = 'Le prénom est obligatoire.';
    }
    if ($email === '') {
        $fieldErrors['email'] = 'L\'e-mail est obligatoire.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $fieldErrors['email'] = 'L\'e-mail n\'est pas au bon format.';
    }
    if ($password === '') {
        $fieldErrors['password'] = 'Le mot de passe est obligatoire.';
    } elseif (strlen($password) < 6) {
        $fieldErrors['password'] = 'Le mot de passe doit contenir au moins 6 caractères.';
    }
    if ($adresse === '') {
        $fieldErrors['adresse'] = 'L\'adresse est obligatoire.';
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
                    $fieldErrors['email'] = 'Cet e-mail est déjà utilisé.';
                }
            }
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
<title>Tarkina — Créer un compte</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/typography.css">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
 
  :root {
    --navy: #1B2A4A;
    --coral: #E8603C;
    --coral-light: #f07355;
    --cream: #FAF7F2;
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
    background: url('https://images.unsplash.com/photo-1592743263126-bb241ee76ac7?auto=format&fit=crop&w=1400&q=80') center center / cover no-repeat;
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
    box-shadow: 0 0 0 4px rgba(232,96,60,0.08);
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
    border-radius: 10px;
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
    box-shadow: 0 8px 20px rgba(232,96,60,0.3);
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
    background: rgba(232,96,60,0.06);
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
      <div class="auth-tagline">LA TUNISIE HORS DES SENTIERS BATTUS</div>
      <div class="auth-quote">Découvrez la <span>vraie</span> Tunisie.</div>
      <div class="auth-sub">Hébergement, repas maison, guides locaux, artisanat & événements dans les régions oubliées du pays.</div>
    </div>
  </div>
 
  <div class="auth-form-panel">
    <div class="form-header">
      <h1>Créer un compte</h1>
      <?php if ($success): ?>
        <p class="form-alert success">Inscription réussie. Vous pouvez maintenant vous connecter.</p>
      <?php endif; ?>
    </div>
 
    <?php if ($success): ?>
      <a href="login.php" class="btn-submit" style="display:block; text-align:center; text-decoration:none;">Aller à la connexion</a>
    <?php else: ?>
    <form method="post" action="register.php" novalidate>
      <div class="form-group">
        <label>Nom</label>
        <input type="text" name="nom" value="<?php echo htmlspecialchars($nom); ?>" class="<?php echo !empty($fieldErrors['nom']) ? 'input-error' : ''; ?>" required>
        <?php if (!empty($fieldErrors['nom'])): ?>
          <small class="error-text"><?php echo $fieldErrors['nom']; ?></small>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <label>Prénom</label>
        <input type="text" name="prenom" value="<?php echo htmlspecialchars($prenom); ?>" class="<?php echo !empty($fieldErrors['prenom']) ? 'input-error' : ''; ?>" required>
        <?php if (!empty($fieldErrors['prenom'])): ?>
          <small class="error-text"><?php echo $fieldErrors['prenom']; ?></small>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" class="<?php echo !empty($fieldErrors['email']) ? 'input-error' : ''; ?>" required>
        <?php if (!empty($fieldErrors['email'])): ?>
          <small class="error-text"><?php echo $fieldErrors['email']; ?></small>
        <?php endif; ?>
      </div>
 
      <div class="form-group">
        <label>Mot de passe</label>
        <div class="input-wrapper">
          <input type="password" name="password" id="pass1" class="<?php echo !empty($fieldErrors['password']) ? 'input-error' : ''; ?>" required minlength="6">
          <?php if (!empty($fieldErrors['password'])): ?>
            <small class="error-text"><?php echo $fieldErrors['password']; ?></small>
          <?php endif; ?>
          <button type="button" class="toggle-pass" onclick="togglePass('pass1')">👁</button>
        </div>
      </div>

      <div class="form-group">
        <label>Adresse</label>
        <input type="text" name="adresse" value="<?php echo htmlspecialchars($adresse); ?>" class="<?php echo !empty($fieldErrors['adresse']) ? 'input-error' : ''; ?>" required>
        <?php if (!empty($fieldErrors['adresse'])): ?>
          <small class="error-text"><?php echo $fieldErrors['adresse']; ?></small>
        <?php endif; ?>
      </div>
 
      <button type="submit" class="btn-submit">Créer un compte</button>
    </form>
    <?php endif; ?>
 
    <div class="form-footer">
      Vous avez déjà un compte ? <a href="login.php">Se connecter</a>
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