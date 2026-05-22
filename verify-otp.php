<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/session_bootstrap.php';

$errors = [];
$success = '';
$email = isset($_GET['email']) ? trim($_GET['email']) : '';
$otpNotice = '';

if (!empty($_SESSION['otp_notice'])) {
    $otpNotice = (string) $_SESSION['otp_notice'];
    unset($_SESSION['otp_notice']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $otp = isset($_POST['otp']) ? trim($_POST['otp']) : '';
    $newPassword = isset($_POST['new_password']) ? $_POST['new_password'] : '';

    if ($email === '') {
        $errors['email'] = 'E-mail manquant.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'E-mail invalide.';
    }

    if ($otp === '') {
        $errors['otp'] = 'Le code OTP est obligatoire.';
    } elseif (!preg_match('/^\d{6}$/', $otp)) {
        $errors['otp'] = 'Le code OTP doit contenir 6 chiffres.';
    }

    if ($newPassword === '') {
        $errors['new_password'] = 'Le nouveau mot de passe est obligatoire.';
    } elseif (strlen($newPassword) < 6) {
        $errors['new_password'] = 'Le mot de passe doit contenir au moins 6 caractères.';
    }

    if (empty($errors)) {
        $stmt = mysqli_prepare(
            $conn,
            'SELECT id, expires_at FROM password_resets WHERE email = ? AND otp = ? ORDER BY id DESC LIMIT 1'
        );

        if ($stmt === false) {
            $errors['general'] = 'Erreur serveur. Veuillez réessayer.';
        } else {
            mysqli_stmt_bind_param($stmt, 'ss', $email, $otp);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $resetId, $expiresAt);
            $found = mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            if (!$found) {
                $errors['otp'] = 'Code OTP invalide.';
            } elseif (strtotime((string) $expiresAt) < time()) {
                $errors['otp'] = 'Code OTP expiré. Veuillez demander un nouveau code.';
            } else {
                $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);

                $updateStmt = mysqli_prepare(
                    $conn,
                    'UPDATE utilisateur SET mot_de_passe = ? WHERE email = ? LIMIT 1'
                );

                if ($updateStmt === false) {
                    $errors['general'] = 'Impossible de mettre à jour le mot de passe.';
                } else {
                    mysqli_stmt_bind_param($updateStmt, 'ss', $passwordHash, $email);
                    $updated = mysqli_stmt_execute($updateStmt);
                    mysqli_stmt_close($updateStmt);

                    if (!$updated) {
                        $errors['general'] = 'Erreur lors de la mise à jour du mot de passe.';
                    } else {
                        $deleteStmt = mysqli_prepare($conn, 'DELETE FROM password_resets WHERE email = ?');
                        if ($deleteStmt) {
                            mysqli_stmt_bind_param($deleteStmt, 's', $email);
                            mysqli_stmt_execute($deleteStmt);
                            mysqli_stmt_close($deleteStmt);
                        }

                        $_SESSION['auth_success'] = 'Mot de passe réinitialisé avec succès. Vous pouvez vous connecter.';
                        header('Location: login.php');
                        exit;
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tarkina — Vérifier OTP</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
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
    max-width: 900px;
    min-height: 560px;
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
    min-width: 300px;
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

  .form-header { margin-bottom: 28px; }
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

  .form-group { margin-bottom: 12px; }
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

  .form-group input:focus {
    border-color: var(--coral);
    background: var(--white);
    box-shadow: 0 0 0 4px rgba(232,96,60,0.08);
  }

  .form-group input.is-invalid {
    border-color: #b43737;
    background: #fff9f9;
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

  .field-error {
    margin-top: 6px;
    color: #b43737;
    font-size: 12px;
    line-height: 1.4;
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
    margin-top: 10px;
    letter-spacing: 0.3px;
  }

  .btn-submit:hover {
    background: var(--coral-light);
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(232,96,60,0.3);
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

  .form-footer a:hover { text-decoration: underline; }

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
      <div class="auth-logo">Tarkina<span>.</span></div>
      <div class="auth-tagline">LA TUNISIE HORS DES SENTIERS BATTUS</div>
      <div class="auth-quote">Confirmez votre code et créez un nouveau mot de passe.</div>
      <div class="auth-sub">Le code OTP reste valide pendant 10 minutes pour sécuriser votre compte.</div>
    </div>
  </div>

  <div class="auth-form-panel">
    <div class="form-header">
      <h1>Vérifier le code OTP</h1>
      <p>Saisissez le code reçu par e-mail puis choisissez un nouveau mot de passe.</p>
      <?php if ($otpNotice !== ''): ?>
        <p class="form-alert success"><?php echo htmlspecialchars($otpNotice); ?></p>
      <?php endif; ?>
      <?php if (isset($errors['general'])): ?>
        <p class="form-alert"><?php echo htmlspecialchars($errors['general']); ?></p>
      <?php endif; ?>
    </div>

    <form method="post" action="verify-otp.php" novalidate>
      <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">

      <div class="form-group">
        <label>Email</label>
        <input
          type="email"
          value="<?php echo htmlspecialchars($email); ?>"
          class="<?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>"
          readonly
        >
        <?php if (isset($errors['email'])): ?>
          <div class="field-error"><?php echo htmlspecialchars($errors['email']); ?></div>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <label>Code OTP</label>
        <input
          type="text"
          name="otp"
          maxlength="6"
          placeholder="Entrez le code à 6 chiffres"
          value="<?php echo isset($_POST['otp']) ? htmlspecialchars((string) $_POST['otp']) : ''; ?>"
          class="<?php echo isset($errors['otp']) ? 'is-invalid' : ''; ?>"
          required
        >
        <?php if (isset($errors['otp'])): ?>
          <div class="field-error"><?php echo htmlspecialchars($errors['otp']); ?></div>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <label>Nouveau mot de passe</label>
        <div class="input-wrapper">
          <input
            type="password"
            name="new_password"
            id="new_password"
            minlength="6"
            placeholder="Nouveau mot de passe"
            class="<?php echo isset($errors['new_password']) ? 'is-invalid' : ''; ?>"
            required
          >
          <button type="button" class="toggle-pass" onclick="togglePass('new_password')">👁</button>
        </div>
        <?php if (isset($errors['new_password'])): ?>
          <div class="field-error"><?php echo htmlspecialchars($errors['new_password']); ?></div>
        <?php endif; ?>
      </div>

      <button type="submit" class="btn-submit">Mettre à jour le mot de passe</button>
    </form>

    <div class="form-footer">
      <a href="login.php">Retour à la connexion</a>
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
