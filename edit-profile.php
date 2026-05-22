<?php
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/db.php';

$userId = (int) $_SESSION['user_id'];

$fieldErrors = [
    'nom' => '',
    'prenom' => '',
    'email' => '',
    'adresse' => ''
];

$formData = [
    'nom' => '',
    'prenom' => '',
    'email' => '',
    'adresse' => ''
];

if (!empty($_SESSION['profile_errors']) && is_array($_SESSION['profile_errors'])) {
    foreach ($fieldErrors as $field => $value) {
        if (isset($_SESSION['profile_errors'][$field])) {
            $fieldErrors[$field] = (string) $_SESSION['profile_errors'][$field];
        }
    }
    unset($_SESSION['profile_errors']);
}

if (!empty($_SESSION['profile_old']) && is_array($_SESSION['profile_old'])) {
    foreach ($formData as $field => $value) {
        if (isset($_SESSION['profile_old'][$field])) {
            $formData[$field] = (string) $_SESSION['profile_old'][$field];
        }
    }
    unset($_SESSION['profile_old']);
} else {
    $sql = 'SELECT nom, prenom, email, adresse FROM utilisateur WHERE id = ? LIMIT 1';
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt !== false) {
        mysqli_stmt_bind_param($stmt, 'i', $userId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $nomDb, $prenomDb, $emailDb, $adresseDb);
        if (mysqli_stmt_fetch($stmt)) {
            $formData = [
                'nom' => $nomDb,
                'prenom' => $prenomDb,
                'email' => $emailDb,
                'adresse' => $adresseDb
            ];
        } else {
            mysqli_stmt_close($stmt);
            header('Location: logout.php');
            exit;
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Modifier Profil - Tarkina</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Lato:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
:root {
  --cream: #f5f2ee;
  --dark: #1c1c2e;
  --navy: #1a2340;
  --orange: #e8642c;
  --white: #ffffff;
  --border: #e0dbd4;
  --radius: 14px;
  --muted: #6b6b6b;
}

body {
  font-family: 'Lato', sans-serif;
  background: var(--cream);
  color: var(--dark);
  margin: 0;
  padding: 40px;
}

.container {
  max-width: 700px;
  margin: 40px auto;
  background: var(--white);
  border-radius: var(--radius);
  padding: 48px;
  border: 1px solid var(--border);
  box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}

h1 {
  font-family: 'Playfair Display', serif;
  font-size: 32px;
  font-weight: 800;
  color: var(--dark);
  margin-bottom: 32px;
}

.form-group {
  margin-bottom: 24px;
}

label {
  display: block;
  font-size: 13px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 8px;
  color: var(--muted);
}

input {
  width: 100%;
  padding: 14px 16px;
  border: 1.5px solid var(--border);
  border-radius: 10px;
  background: var(--white);
  font-family: 'Lato', sans-serif;
  font-size: 15px;
  color: var(--dark);
  outline: none;
  transition: border-color 0.2s;
}

input:focus {
  border-color: var(--orange);
}

.btn {
  margin-top: 16px;
  padding: 16px;
  background: var(--orange);
  color: #fff;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  width: 100%;
  font-size: 16px;
  font-weight: 700;
  transition: background 0.2s;
}

.btn:hover {
  background: #d45625;
}

.error-text {
  display: block;
  margin-top: 8px;
  font-size: 13px;
  color: #e74c3c;
}

.input-error {
  border-color: #e74c3c !important;
  background: #fffafa;
}

.back-link {
  display: inline-block;
  margin-bottom: 20px;
  text-decoration: none;
  color: var(--muted);
  font-weight: 600;
  font-size: 14px;
}
.back-link:hover { color: var(--dark); }
</style>
</head>

<div class="container">
  <a href="profile.php" class="back-link">← Retour au profil</a>
  <h1>Modifier mon profil</h1>

  <form method="POST" action="update-profile.php" novalidate>

    <div class="form-group">
      <label>Nom</label>
      <input type="text" name="nom" value="<?php echo htmlspecialchars($formData['nom']); ?>" class="<?php echo !empty($fieldErrors['nom']) ? 'input-error' : ''; ?>">
      <?php if (!empty($fieldErrors['nom'])): ?>
        <small class="error-text"><?php echo htmlspecialchars($fieldErrors['nom']); ?></small>
      <?php endif; ?>
    </div>

    <div class="form-group">
      <label>Prénom</label>
      <input type="text" name="prenom" value="<?php echo htmlspecialchars($formData['prenom']); ?>" class="<?php echo !empty($fieldErrors['prenom']) ? 'input-error' : ''; ?>">
      <?php if (!empty($fieldErrors['prenom'])): ?>
        <small class="error-text"><?php echo htmlspecialchars($fieldErrors['prenom']); ?></small>
      <?php endif; ?>
    </div>

    <div class="form-group">
      <label>Email</label>
      <input type="email" name="email" value="<?php echo htmlspecialchars($formData['email']); ?>" class="<?php echo !empty($fieldErrors['email']) ? 'input-error' : ''; ?>">
      <?php if (!empty($fieldErrors['email'])): ?>
        <small class="error-text"><?php echo htmlspecialchars($fieldErrors['email']); ?></small>
      <?php endif; ?>
    </div>

    <div class="form-group">
      <label>Adresse</label>
      <input type="text" name="adresse" value="<?php echo htmlspecialchars($formData['adresse']); ?>" class="<?php echo !empty($fieldErrors['adresse']) ? 'input-error' : ''; ?>">
      <?php if (!empty($fieldErrors['adresse'])): ?>
        <small class="error-text"><?php echo htmlspecialchars($fieldErrors['adresse']); ?></small>
      <?php endif; ?>
    </div>

    <button type="submit" class="btn">Enregistrer</button>

  </form>

</div>

</body>
</html>
