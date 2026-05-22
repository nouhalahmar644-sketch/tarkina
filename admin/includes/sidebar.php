<?php
if (!isset($activePage)) {
    $activePage = '';
}
?>
<aside class="admin-sidebar">
  <div class="brand">Tarkina<span>.</span></div>
  <nav class="sidebar-nav">
    <a href="dashboard.php" class="sidebar-link <?php echo $activePage === 'dashboard' ? 'active' : ''; ?>">Tableau de bord</a>
    <a href="reservations.php" class="sidebar-link <?php echo $activePage === 'reservations' ? 'active' : ''; ?>">Réservations</a>
    <a href="users.php" class="sidebar-link <?php echo $activePage === 'users' ? 'active' : ''; ?>">Utilisateurs</a>
    <a href="content.php" class="sidebar-link <?php echo $activePage === 'content' ? 'active' : ''; ?>">Hébergements</a>
    <a href="repas.php" class="sidebar-link <?php echo $activePage === 'repas' ? 'active' : ''; ?>">Repas maison</a>
    <a href="guide.php" class="sidebar-link <?php echo $activePage === 'guide' ? 'active' : ''; ?>">Guides locaux</a>
    <a href="evenement.php" class="sidebar-link <?php echo $activePage === 'evenement' ? 'active' : ''; ?>">Événements</a>
    <a href="region.php" class="sidebar-link <?php echo $activePage === 'region' ? 'active' : ''; ?>">Régions</a>
    <a href="artisanat.php" class="sidebar-link <?php echo $activePage === 'artisanat' ? 'active' : ''; ?>">Artisanat</a>
    <a href="commandes.php" class="sidebar-link <?php echo $activePage === 'commandes' ? 'active' : ''; ?>">Commandes</a>
    <a href="logout.php" class="sidebar-link">Déconnexion</a>
  </nav>
</aside>
