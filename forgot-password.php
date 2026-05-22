<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/session_bootstrap.php';
require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$errors = [];
$success = '';
$email = '';

function createMailer(): PHPMailer {
    $mail = new PHPMailer(true);
    $mail->SMTPDebug = SMTP::DEBUG_OFF;
    $mail->Debugoutput = function($str, $level) {
        error_log("PHPMailer [$level]: $str");
    };
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'nouhalahmar644@gmail.com';
    $mail->Password   = 'uxay qxht exub lieo';  // App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';
    return $mail;
}

function sendOtpEmail(string $toEmail, string $otp, array &$errors): bool
{
    try {
        $mail = createMailer();

        $mail->setFrom('nouhalahmar644@gmail.com', 'Tarkina');
        $mail->addAddress($toEmail);

        $mail->Subject = 'Réinitialisation de mot de passe — Tarkina';
        $mail->isHTML(false);
        $mail->Body = "Votre code OTP est : $otp\n\nCe code est valide pendant 10 minutes.\n\nSi vous n'avez pas demandé cette réinitialisation, ignorez cet e-mail.";

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log('PHPMailer Exception: ' . $e->getMessage());
        error_log('PHPMailer ErrorInfo: ' . $mail->ErrorInfo);
        $errors[] = "Erreur envoi : " . $e->getMessage();
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';

    if ($email === '') {
        $errors['email'] = 'L\'e-mail est obligatoire.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'E-mail invalide.';
    } else {
        $stmt = mysqli_prepare($conn, 'SELECT id FROM utilisateur WHERE email = ? LIMIT 1');
        if ($stmt === false) {
            $errors['general'] = 'Erreur serveur. Veuillez réessayer.';
        } else {
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $uid);
            $exists = mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            if (!$exists) {
                $errors['email'] = 'Aucun compte trouvé avec cet e-mail.';
            } else {
                $otp = (string) rand(100000, 999999);
                $expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));

                $deleteStmt = mysqli_prepare($conn, 'DELETE FROM password_resets WHERE email = ?');
                if ($deleteStmt) {
                    mysqli_stmt_bind_param($deleteStmt, 's', $email);
                    mysqli_stmt_execute($deleteStmt);
                    mysqli_stmt_close($deleteStmt);
                }

                $insertStmt = mysqli_prepare(
                    $conn,
                    'INSERT INTO password_resets (email, otp, expires_at) VALUES (?, ?, ?)'
                );

                if ($insertStmt === false) {
                    $errors['general'] = 'Impossible de générer le code OTP.';
                } else {
                    mysqli_stmt_bind_param($insertStmt, 'sss', $email, $otp, $expires_at);
                    $ok = mysqli_stmt_execute($insertStmt);
                    mysqli_stmt_close($insertStmt);

                    if (!$ok) {
                        $errors['general'] = 'Impossible de sauvegarder le code OTP.';
                    } else {
                        $mailErrors = [];
                        $emailSent = sendOtpEmail($email, $otp, $mailErrors);

                        if ($emailSent) {
                            $_SESSION['otp_notice'] = 'Code OTP envoyé par e-mail.';
                        } else {
                            $_SESSION['otp_notice'] = 'Le code OTP a été généré, mais l\'envoi e-mail a échoué. Vérifiez la configuration SMTP.';
                            error_log('OTP email not sent for ' . $email . ' - ' . implode(' | ', $mailErrors));
                        }

                        header('Location: verify-otp.php?email=' . urlencode($email));
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
  <title>Tarkina — Mot de passe oublié</title>
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
        <div class="auth-quote">Réinitialisez votre accès en toute sécurité.</div>
        <div class="auth-sub">Nous vous enverrons un code OTP temporaire pour créer un nouveau mot de passe.</div>
      </div>
    </div>

    <div class="auth-form-panel">
      <div class="form-header">
        <h1>Mot de passe oublié</h1>
        <p>Entrez votre e-mail pour recevoir un code OTP valide pendant 10 minutes.</p>
        <?php if (isset($errors['general'])): ?>
          <p class="form-alert"><?php echo htmlspecialchars($errors['general']); ?></p>
        <?php endif; ?>
      </div>

      <form method="post" action="forgot-password.php" novalidate>
        <div class="form-group">
          <label>Email</label>
          <input
            type="email"
            name="email"
            placeholder="Entrez votre adresse e-mail"
            value="<?php echo htmlspecialchars($email); ?>"
            class="<?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>"
            required
          >
          <?php if (isset($errors['email'])): ?>
            <div class="field-error"><?php echo htmlspecialchars($errors['email']); ?></div>
          <?php endif; ?>
        </div>

        <button type="submit" class="btn-submit">Envoyer le code OTP</button>
      </form>

      <div class="form-footer">
        <a href="login.php">Retour à la connexion</a>
      </div>
    </div>
  </div>
  </body>
  </html>