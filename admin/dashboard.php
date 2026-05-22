<?php
require_once __DIR__ . '/includes/auth_admin.php';

function table_exists($conn, $tableName)
{
    $safeName = mysqli_real_escape_string($conn, $tableName);
    $sql = "SHOW TABLES LIKE '" . $safeName . "'";
    $result = mysqli_query($conn, $sql);
    if ($result === false) {
        return false;
    }
    $exists = mysqli_num_rows($result) > 0;
    mysqli_free_result($result);
    return $exists;
}

function table_count($conn, $tableName)
{
    $safeName = mysqli_real_escape_string($conn, $tableName);
    $sql = "SELECT COUNT(*) AS total FROM `" . $safeName . "`";
    $result = mysqli_query($conn, $sql);
    if ($result === false) {
        return 0;
    }
    $row = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    return isset($row['total']) ? (int) $row['total'] : 0;
}

$totalUsers = table_count($conn, 'utilisateur');
$totalRegions = table_count($conn, 'region');
$totalHebergements = table_count($conn, 'hebergement');
$totalRepas = table_count($conn, 'repas');
$totalGuides = table_count($conn, 'guide');
$totalEvenements = table_count($conn, 'evenement');
$totalArtisanat = table_count($conn, 'artisanat');

$contentCount = $totalHebergements;
$contentLabel = 'Total hébergements';

$pageTitle = 'Tableau de bord';
$pageHeading = 'Tableau de bord';
$activePage = 'dashboard';


require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>
<style>
  .dashboard-layout {
    display: flex;
    gap: 32px;
    height: 100%;
  }
  .dashboard-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 0;
  }
  .dashboard-sidebar {
    width: 320px;
    display: flex;
    flex-direction: column;
    gap: 24px;
    flex-shrink: 0;
  }
  
  /* Banner */
  .promo-banner {
    background: linear-gradient(135deg, var(--coral) 0%, #d44d28 100%);
    border-radius: 20px;
    padding: 32px;
    color: #fff;
    position: relative;
    overflow: hidden;
    margin-bottom: 32px;
    box-shadow: 0 10px 30px rgba(232,96,60,0.2);
  }
  .promo-banner h2 { font-size: 28px; margin-bottom: 8px; font-family: 'Playfair Display', serif; }
  .promo-banner p { font-size: 14px; opacity: 0.9; max-width: 60%; line-height: 1.5; }
  .promo-image { position: absolute; right: 0; top: -20px; height: 130%; opacity: 0.9; }

  /* Categories */
  .section-title { font-size: 18px; font-weight: 700; color: var(--navy); margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center; }
  .section-title a { font-size: 13px; color: var(--coral); text-decoration: none; font-weight: 600; }
  
  .category-list {
    display: flex;
    gap: 16px;
    margin-bottom: 32px;
    overflow-x: auto;
    padding-bottom: 8px;
  }
  .category-list::-webkit-scrollbar { height: 6px; }
  .category-list::-webkit-scrollbar-thumb { background: #ddd; border-radius: 4px; }

  .category-item {
    min-width: 90px;
    background: var(--white);
    border-radius: 16px;
    padding: 16px 12px;
    text-align: center;
    text-decoration: none;
    color: var(--navy);
    border: 1px solid var(--border);
    transition: all 0.2s ease;
    box-shadow: 0 4px 12px rgba(0,0,0,0.02);
  }
  .category-item:hover, .category-item.active {
    background: var(--navy);
    color: #fff;
    border-color: var(--navy);
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(27,42,74,0.15);
  }
  .category-item i { font-size: 24px; margin-bottom: 8px; display: block; }
  .category-item span { font-size: 13px; font-weight: 600; }

  /* Popular Cards */
  .popular-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
  }
  .pop-card {
    background: var(--white);
    border-radius: 16px;
    border: 1px solid var(--border);
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.02);
  }
  .pop-card-img {
    height: 140px;
    background: var(--cream);
    position: relative;
  }
  .pop-card-img img { width: 100%; height: 100%; object-fit: cover; }
  .pop-badge { position: absolute; top: 12px; left: 12px; background: var(--coral); color: #fff; padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: 700; box-shadow: 0 2px 8px rgba(0,0,0,0.2); }
  .pop-card-body { padding: 16px; }
  .pop-card-title { font-family: 'Playfair Display', serif; font-size: 18px; font-weight: 700; margin-bottom: 4px; color: var(--navy); }
  .pop-card-price { font-size: 20px; font-weight: 700; color: var(--navy); display: flex; justify-content: space-between; align-items: center; }
  .pop-btn { width: 32px; height: 32px; background: var(--navy); color: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer; transition: background 0.2s; }
  .pop-btn:hover { background: var(--coral); }

  /* Right Sidebar Elements */
  .top-actions { display: flex; align-items: center; justify-content: flex-end; gap: 16px; margin-bottom: 8px; }
  .action-btn { width: 40px; height: 40px; border-radius: 12px; background: var(--white); color: var(--navy); display: flex; align-items: center; justify-content: center; text-decoration: none; border: 1px solid var(--border); font-size: 18px; transition: all 0.2s; }
  .action-btn:hover { background: var(--cream); color: var(--coral); border-color: var(--coral); }
  .user-avatar { width: 40px; height: 40px; border-radius: 12px; object-fit: cover; border: 2px solid var(--white); box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
  
  .balance-card {
    background: var(--navy);
    border-radius: 20px;
    padding: 24px;
    color: #fff;
    box-shadow: 0 10px 24px rgba(27,42,74,0.25);
  }
  .balance-title { font-size: 14px; opacity: 0.9; margin-bottom: 4px; }
  .balance-amount { font-family: 'Playfair Display', serif; font-size: 32px; font-weight: 700; margin-bottom: 20px; }
  .balance-actions { display: flex; gap: 12px; }
  .bal-btn { flex: 1; background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.2); padding: 10px; border-radius: 12px; font-size: 13px; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 6px; cursor: pointer; transition: all 0.2s; }
  .bal-btn:hover { background: var(--coral); border-color: var(--coral); }

  .address-card {
    background: var(--white);
    border-radius: 16px;
    padding: 20px;
    border: 1px solid var(--border);
    box-shadow: 0 4px 12px rgba(0,0,0,0.02);
  }
  .addr-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
  .addr-title { font-size: 14px; font-weight: 700; color: var(--navy); }
  .addr-change { font-size: 12px; color: var(--coral); text-decoration: none; font-weight: 600; }
  .addr-body { display: flex; gap: 12px; align-items: flex-start; }
  .addr-icon { color: var(--coral); font-size: 20px; }
  .addr-text p { font-size: 13px; font-weight: 600; color: var(--navy); margin-bottom: 4px; }
  .addr-text span { font-size: 12px; color: var(--grey); line-height: 1.4; display: block; }
  
  .order-menu {
    background: var(--white);
    border-radius: 16px;
    padding: 20px;
    border: 1px solid var(--border);
    flex: 1;
    box-shadow: 0 4px 12px rgba(0,0,0,0.02);
  }
  .order-title { font-size: 16px; font-weight: 700; color: var(--navy); margin-bottom: 16px; }
  .order-item { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
  .oi-info { display: flex; align-items: center; gap: 12px; }
  .oi-img { width: 48px; height: 48px; border-radius: 12px; background: var(--cream); overflow: hidden; }
  .oi-img img { width: 100%; height: 100%; object-fit: cover; }
  .oi-text h5 { font-size: 13px; font-weight: 700; color: var(--navy); margin-bottom: 2px; }
  .oi-text span { font-size: 11px; color: var(--grey); }
  .oi-price { font-size: 13px; font-weight: 700; color: var(--navy); }
  
  .order-total { margin-top: 24px; padding-top: 16px; border-top: 1px dashed var(--border); display: flex; justify-content: space-between; align-items: center; font-size: 16px; font-weight: 700; color: var(--navy); margin-bottom: 20px; }
  .btn-checkout { width: 100%; background: var(--coral); color: #fff; border: none; padding: 14px; border-radius: 12px; font-size: 14px; font-weight: 700; cursor: pointer; transition: opacity 0.2s; }
  .btn-checkout:hover { opacity: 0.9; }

  @media (max-width: 1100px) {
    .dashboard-layout { flex-direction: column; }
    .dashboard-sidebar { width: 100%; }
  }

  /* Modal Styles */
  .modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    justify-content: center;
    align-items: center;
    backdrop-filter: blur(4px);
  }
  .modal-content {
    background: var(--white);
    padding: 32px;
    border-radius: 20px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    animation: slideUp 0.3s ease;
  }
  @keyframes slideUp {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
  }
  .modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
  }
  .modal-header h3 { font-family: 'Playfair Display', serif; font-size: 24px; color: var(--navy); margin: 0; }
  .close-modal { font-size: 24px; cursor: pointer; color: var(--grey); border: none; background: none; }
  
  .form-group { margin-bottom: 16px; }
  .form-group label { display: block; font-size: 13px; font-weight: 600; color: var(--navy); margin-bottom: 6px; }
  .form-group input { 
    width: 100%; 
    padding: 12px; 
    border: 1px solid var(--border); 
    border-radius: 10px; 
    font-size: 14px;
    transition: border-color 0.2s;
  }
  .form-group input:focus { border-color: var(--coral); outline: none; }
  
  .modal-footer { display: flex; gap: 12px; margin-top: 24px; }
  .btn-save { 
    flex: 1; 
    background: var(--navy); 
    color: #fff; 
    border: none; 
    padding: 12px; 
    border-radius: 10px; 
    font-weight: 600; 
    cursor: pointer;
    transition: background 0.2s;
  }
  .btn-save:hover { background: var(--coral); }
  .btn-cancel { 
    flex: 1; 
    background: var(--cream); 
    color: var(--navy); 
    border: 1px solid var(--border); 
    padding: 12px; 
    border-radius: 10px; 
    font-weight: 600; 
    cursor: pointer;
  }
</style>

<main class="admin-content">
  <div class="content-wrap" style="padding-top: 24px; padding-bottom: 24px;">
    
    <div class="dashboard-layout">
      <!-- MAIN COLUMN -->
      <div class="dashboard-main">
        
        <div class="topbar" style="border:none; padding:0; background:transparent;">
          <div class="topbar-greeting">
            <h1>Hey, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h1>
          </div>
          <form class="search-wrapper" action="users.php" method="get">
            <i class="bi bi-search"></i>
            <input type="text" name="q" placeholder="Rechercher un utilisateur...">
          </form>
        </div>

        <div class="promo-banner">
          <h2>Gérez Tarkina<br>Simplement</h2>
          <p>Bienvenue sur votre tableau de bord. Retrouvez ici un aperçu de toutes les activités de la plateforme.</p>
          <img src="https://images.unsplash.com/photo-1548690312-e3b507d8c110?auto=format&fit=crop&w=600&q=80" class="promo-image" alt="Promo">
        </div>

        <div class="section-title">
          Catégories        </div>
        <div class="category-list">
          <a href="content.php" class="category-item active">
            <i class="bi bi-house-heart-fill"></i>
            <span>Hébergements</span>
          </a>
          <a href="repas.php" class="category-item">
            <i class="bi bi-egg-fried"></i>
            <span>Repas</span>
          </a>
          <a href="guide.php" class="category-item">
            <i class="bi bi-geo-alt-fill"></i>
            <span>Guides</span>
          </a>
          <a href="evenement.php" class="category-item">
            <i class="bi bi-stars"></i>
            <span>Événements</span>
          </a>
          <a href="region.php" class="category-item">
            <i class="bi bi-map-fill"></i>
            <span>Régions</span>
          </a>
          <a href="artisanat.php" class="category-item">
            <i class="bi bi-bag-heart-fill"></i>
            <span>Artisanat</span>
          </a>
        </div>

        <div class="section-title">
          Statistiques Principales        </div>
        <div class="popular-grid">
          
          <article class="pop-card">
            <div class="pop-card-img">
              <span class="pop-badge">Users</span>
              <img src="https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=600&q=80" alt="Users">
            </div>
            <div class="pop-card-body">
              <div class="pop-card-title">Total Utilisateurs</div>
              <p class="muted" style="margin-top:0; margin-bottom:12px;">Comptes inscrits</p>
              <div class="pop-card-price">
                <?php echo (int) $totalUsers; ?>
                <a href="users.php" class="pop-btn" title="Gérer les utilisateurs"><i class="bi bi-arrow-right"></i></a>
              </div>
            </div>
          </article>

          <article class="pop-card">
            <div class="pop-card-img">
              <span class="pop-badge">Hébergements</span>
              <img src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=600&q=80" alt="Content">
            </div>
            <div class="pop-card-body">
              <div class="pop-card-title">Total Hébergements</div>
              <p class="muted" style="margin-top:0; margin-bottom:12px;">Établissements</p>
              <div class="pop-card-price">
                <?php echo (int) $totalHebergements; ?>
                <a href="content.php?add=1" class="pop-btn" title="Ajouter un hébergement"><i class="bi bi-plus"></i></a>
              </div>
            </div>
          </article>

          <article class="pop-card">
            <div class="pop-card-img">
              <span class="pop-badge">Repas</span>
              <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=600&q=80" alt="Repas">
            </div>
            <div class="pop-card-body">
              <div class="pop-card-title">Total Repas</div>
              <p class="muted" style="margin-top:0; margin-bottom:12px;">Expériences</p>
              <div class="pop-card-price">
                <?php echo (int) $totalRepas; ?>
                <a href="repas.php?add=1" class="pop-btn" title="Ajouter un repas"><i class="bi bi-plus"></i></a>
              </div>
            </div>
          </article>

          <article class="pop-card">
            <div class="pop-card-img">
              <span class="pop-badge">Artisanat</span>
              <img src="https://images.unsplash.com/photo-1565193566173-7a0ee3dbe261?auto=format&fit=crop&w=600&q=80" alt="Artisanat">
            </div>
            <div class="pop-card-body">
              <div class="pop-card-title">Total Artisanat</div>
              <p class="muted" style="margin-top:0; margin-bottom:12px;">Produits</p>
              <div class="pop-card-price">
                <?php echo (int) $totalArtisanat; ?>
                <a href="artisanat.php?add=1" class="pop-btn" title="Ajouter un produit"><i class="bi bi-plus"></i></a>
              </div>
            </div>
          </article>

          <article class="pop-card">
            <div class="pop-card-img">
              <span class="pop-badge">Événements</span>
              <img src="https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?auto=format&fit=crop&w=600&q=80" alt="Evenement">
            </div>
            <div class="pop-card-body">
              <div class="pop-card-title">Total Événements</div>
              <p class="muted" style="margin-top:0; margin-bottom:12px;">Festivals & Sorties</p>
              <div class="pop-card-price">
                <?php echo (int) $totalEvenements; ?>
                <a href="evenement.php?add=1" class="pop-btn" title="Ajouter un événement"><i class="bi bi-plus"></i></a>
              </div>
            </div>
          </article>

          <article class="pop-card">
            <div class="pop-card-img">
              <span class="pop-badge">Guides</span>
              <img src="https://images.unsplash.com/photo-1501503060445-7382139d5381?auto=format&fit=crop&w=600&q=80" alt="Guide">
            </div>
            <div class="pop-card-body">
              <div class="pop-card-title">Total Guides</div>
              <p class="muted" style="margin-top:0; margin-bottom:12px;">Experts locaux</p>
              <div class="pop-card-price">
                <?php echo (int) $totalGuides; ?>
                <a href="guide.php?add=1" class="pop-btn" title="Ajouter un guide"><i class="bi bi-plus"></i></a>
              </div>
            </div>
          </article>

        </div>

        </div>

      <!-- Edit Modal removed -->


      <!-- RIGHT SIDEBAR -->
      <div class="dashboard-sidebar">
        
        <div class="top-actions">
          <a href="../index.php" target="_blank" class="action-btn" title="Voir le site"><i class="bi bi-box-arrow-up-right"></i></a>
          <a href="logout.php" class="action-btn" title="Déconnexion"><i class="bi bi-box-arrow-right"></i></a>
          <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name']); ?>&background=1B2A4A&color=fff" class="user-avatar" alt="Admin">
        </div>

        <div class="balance-card">
          <div class="balance-title">Statut Système</div>
          <div class="balance-amount">Actif</div>
          <div class="balance-actions">
            <a href="reservations.php" class="bal-btn"><i class="bi bi-calendar-check"></i> Réservations</a>
            <a href="commandes.php" class="bal-btn"><i class="bi bi-bag-check"></i> Commandes</a>
          </div>
        </div>

        <div class="address-card">
          <div class="addr-header">
            <div class="addr-title">Détails Serveur</div>
          </div>
          <div class="addr-body">
            <i class="bi bi-hdd-network addr-icon"></i>
            <div class="addr-text">
              <p>Tarkina Host v1.2</p>
              <span>Localhost MySQL<br>PHP 8.2</span>
            </div>
          </div>
        </div>

        <div class="order-menu">
          <div class="order-title">Résumé Activité</div>
          
          <div class="order-item">
            <div class="oi-info">
              <div class="oi-img"><img src="https://images.unsplash.com/photo-1493770348161-369560ae357d?auto=format&fit=crop&w=100&q=80" alt="Repas"></div>
              <div class="oi-text">
                <h5>Régions</h5>
                <span>x <?php echo $totalRegions; ?></span>
              </div>
            </div>
          </div>

          <div class="order-item">
            <div class="oi-info">
              <div class="oi-img"><img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=100&q=80" alt="Hebergements"></div>
              <div class="oi-text">
                <h5>Événements</h5>
                <span>x <?php echo $totalEvenements; ?></span>
              </div>
            </div>
          </div>

          <div class="order-item">
            <div class="oi-info">
              <div class="oi-img"><img src="https://images.unsplash.com/photo-1501503060445-7382139d5381?auto=format&fit=crop&w=100&q=80" alt="Guide"></div>
              <div class="oi-text">
                <h5>Guides</h5>
                <span>x <?php echo $totalGuides; ?></span>
              </div>
            </div>
          </div>

          <div class="order-item">
            <div class="oi-info">
              <div class="oi-img"><img src="https://images.unsplash.com/photo-1565193566173-7a0ee3dbe261?auto=format&fit=crop&w=100&q=80" alt="Artisanat"></div>
              <div class="oi-text">
                <h5>Artisanat</h5>
                <span>x <?php echo $totalArtisanat; ?></span>
              </div>
            </div>
          </div>

          <div class="order-total">
            <span>Total Objets</span>
            <span><?php echo $totalRegions + $totalHebergements + $totalRepas + $totalGuides + $totalEvenements + $totalArtisanat; ?></span>
          </div>
          
          <button class="btn-checkout" onclick="window.print()">Imprimer le rapport</button>
        </div>

      </div>

    </div>

  </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

