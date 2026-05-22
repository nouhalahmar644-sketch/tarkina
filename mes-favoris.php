<?php
session_start();
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/db.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Favoris - Tarkina</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Lato:wght@400;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .page-container { padding: 100px 56px; max-width: 1200px; margin: 0 auto; min-height: 80vh; }
        .page-title { font-family: 'Playfair Display', serif; font-size: 48px; margin-bottom: 40px; }
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
    <?php if(isset($_SESSION['user_id'])): ?>
      <a href="profile.php" class="btn-nav-primary">Mon Profil</a>
      <a href="logout.php" class="btn-nav-outline">Déconnexion</a>
    <?php else: ?>
      <a href="login.php" class="btn-nav-outline">Connexion</a>
      <a href="register.php" class="btn-nav-primary">S'inscrire</a>
    <?php endif; ?>
  </div>
</nav>

<div class="page-container">
    <h1 class="page-title">Mes Favoris</h1>
    <p>Retrouvez ici tous les services que vous avez ajoutés à vos favoris.</p>
</div>

</body>
</html>
