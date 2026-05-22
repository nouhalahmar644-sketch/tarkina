<!-- NAVBAR -->
<nav class="navbar" style="position: fixed; top: 0; left: 0; right: 0; z-index: 1000; background: #fff; border-bottom: 1px solid #E5E7EB; padding: 0 60px; height: 70px; display: flex; align-items: center; justify-content: space-between;">
  <a href="index.php" class="nav-logo" style="display: flex; align-items: center; text-decoration: none;">
    <img src="assets/img/logo.png" alt="TARKINA" style="height: 36px;" onerror="this.style.display='none';this.nextElementSibling.style.display='inline'">
    <span style="display:none;font-weight:800;font-size:1.4rem;color:#1B3A4B;">Tarkina</span>
  </a>
  <ul class="nav-links" style="display: flex; gap: 36px; list-style: none;">
    <li><a href="index.php" style="text-decoration: none; color: #1a1a1a; font-size: 0.95rem; font-weight: 500; transition: color .2s;">Accueil</a></li>
    <li><a href="explorer.php" style="text-decoration: none; color: #1a1a1a; font-size: 0.95rem; font-weight: 500; transition: color .2s;">Explorer</a></li>
    <li><a href="stories.php" style="text-decoration: none; color: #1a1a1a; font-size: 0.95rem; font-weight: 500; transition: color .2s;">Stories</a></li>
    <li><a href="about.php" style="text-decoration: none; color: #1a1a1a; font-size: 0.95rem; font-weight: 500; transition: color .2s;">À propos</a></li>
    <li><a href="contact.php" style="text-decoration: none; color: #1a1a1a; font-size: 0.95rem; font-weight: 500; transition: color .2s;">Contact</a></li>
  </ul>
  <div class="nav-auth" style="display: flex; gap: 12px; align-items: center;">
    <?php if(isset($_SESSION['user_id'])): ?>
      <a href="profile.php" style="padding: 8px 20px; background: #E05A2B; border-radius: 50px; color: #fff; text-decoration: none; font-size: 0.9rem; font-weight: 600;">Mon Profil</a>
      <a href="logout.php" style="padding: 8px 20px; border: 1.5px solid #1B3A4B; border-radius: 50px; color: #1B3A4B; text-decoration: none; font-size: 0.9rem; font-weight: 600; transition: all .2s;">Déconnexion</a>
    <?php else: ?>
      <a href="login.php" style="padding: 8px 20px; border: 1.5px solid #1B3A4B; border-radius: 50px; color: #1B3A4B; text-decoration: none; font-size: 0.9rem; font-weight: 600; transition: all .2s;">Connexion</a>
      <a href="register.php" style="padding: 8px 20px; background: #E05A2B; border-radius: 50px; color: #fff; text-decoration: none; font-size: 0.9rem; font-weight: 600;">S'inscrire</a>
    <?php endif; ?>
  </div>
</nav>
<div style="height: 70px;"></div>
