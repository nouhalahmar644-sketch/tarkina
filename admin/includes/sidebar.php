<?php
if (!isset($activePage)) {
    $activePage = '';
}
?>
<aside class="admin-sidebar">
  <div class="brand">Tarkina<span>.</span></div>
  <nav class="sidebar-nav">
    <a href="dashboard.php" class="sidebar-link <?php echo $activePage === 'dashboard' ? 'active' : ''; ?>">
      <i class="bi bi-grid-fill"></i> Tableau de bord
    </a>
    <a href="reservations.php" class="sidebar-link <?php echo $activePage === 'reservations' ? 'active' : ''; ?>">
      <i class="bi bi-calendar-check-fill"></i> Réservations
    </a>
    <a href="users.php" class="sidebar-link <?php echo $activePage === 'users' ? 'active' : ''; ?>">
      <i class="bi bi-people-fill"></i> Utilisateurs
    </a>
    <a href="content.php" class="sidebar-link <?php echo $activePage === 'content' ? 'active' : ''; ?>">
      <i class="bi bi-house-heart-fill"></i> Hébergements
    </a>
    <a href="repas.php" class="sidebar-link <?php echo $activePage === 'repas' ? 'active' : ''; ?>">
      <i class="bi bi-egg-fried"></i> Repas maison
    </a>
    <a href="guide.php" class="sidebar-link <?php echo $activePage === 'guide' ? 'active' : ''; ?>">
      <i class="bi bi-geo-alt-fill"></i> Guides locaux
    </a>
    <a href="evenement.php" class="sidebar-link <?php echo $activePage === 'evenement' ? 'active' : ''; ?>">
      <i class="bi bi-stars"></i> Événements
    </a>
    <a href="region.php" class="sidebar-link <?php echo $activePage === 'region' ? 'active' : ''; ?>">
      <i class="bi bi-map-fill"></i> Régions
    </a>
    <a href="artisanat.php" class="sidebar-link <?php echo $activePage === 'artisanat' ? 'active' : ''; ?>">
      <i class="bi bi-bag-heart-fill"></i> Artisanat
    </a>
    <a href="commandes.php" class="sidebar-link <?php echo $activePage === 'commandes' ? 'active' : ''; ?>">
      <i class="bi bi-receipt"></i> Commandes
    </a>
    <a href="logout.php" class="sidebar-link" style="margin-top: auto;">
      <i class="bi bi-box-arrow-right"></i> Déconnexion
    </a>
  </nav>

  <div class="upgrade-card">
    <h4>Site public</h4>
    <p>Consultez Tarkina tel que le voient vos visiteurs.</p>
    <a href="../index.php" target="_blank" rel="noopener" class="btn-upgrade">Voir le site</a>
  </div>
</aside>
