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
    box-shadow: 0 10px 30px rgba(27, 107, 69,0.2);
  }
  .promo-banner h2 { font-size: 28px; margin-bottom: 8px; font-family: 'Playfair Display', serif; }
  .promo-banner p { font-size: 14px; opacity: 0.9; max-width: 60%; line-height: 1.5; }
  .promo-image { position: absolute; right: 24px; top: 50%; transform: translateY(-50%); height: 90%; opacity: 0.95; pointer-events: none; }
  .promo-image svg { height: 100%; width: auto; display: block; }
  .promo-image svg .tn-shape { fill: rgba(255,255,255,.18); stroke: #fff; stroke-width: 1.4; }
  .promo-image svg .tn-pin { fill: #fff; }

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
          <div class="promo-image" aria-hidden="true">
            <svg viewBox="0 0 250 489" xmlns="http://www.w3.org/2000/svg">
              <!-- Tunisia outline assembled from the explorer-page polygons -->
              <g class="tn-shape">
                <polygon points="161,21 155,22 148,34 147,38 152,41 168,36 158,27"/>
                <polygon points="98,17 92,20 88,27 98,59 95,68 100,72 109,71 119,66 125,69 134,66 141,59 137,51 125,40 117,46 103,27 98,24"/>
                <polygon points="163,44 173,48 168,65 164,67 156,57 147,53 160,49"/>
                <polygon points="99,17 130,6 140,7 143,13 153,11 164,17 157,21 148,33 140,31 124,39 117,45 103,26 98,24"/>
                <polygon points="58,65 58,73 70,69 85,70 92,65 95,66 98,59 88,27 83,31 72,32 73,40 64,46 63,52 48,59 48,64"/>
                <polygon points="58,73 55,78 52,113 56,121 63,118 69,120 73,119 74,110 84,112 94,117 96,104 103,94 95,81 90,67 84,70 70,69"/>
                <polygon points="125,41 136,49 141,59 148,52 153,50 149,43 152,41 146,38 148,33 140,31 126,38"/>
                <polygon points="173,48 180,45 181,37 207,22 212,36 199,51 194,66 179,72 169,71 168,64"/>
                <polygon points="89,67 95,83 103,94 95,106 93,118 112,131 119,130 107,115 117,116 122,113 120,101 135,91 135,84 128,83 125,78 132,76 135,69 134,66 124,69 119,65 108,72 99,72 95,68 94,65"/>
                <polygon points="168,36 170,38 165,45 163,44 160,49 153,51 149,43 152,41"/>
                <polygon points="147,53 134,66 135,69 132,76 125,78 128,83 135,84 135,91 144,91 151,96 170,79 170,72 168,65 164,67 156,57"/>
                <polygon points="162,153 159,139 166,124 158,107 164,96 159,88 151,96 144,91 134,91 120,102 121,114 116,116 107,115 121,133 121,141 140,154 148,153 151,164 158,154"/>
                <polygon points="56,121 55,139 59,151 54,160 52,180 68,185 83,180 93,175 99,164 108,160 108,150 101,146 103,140 112,132 109,128 93,117 88,116 80,111 73,111 72,120 67,120 63,118"/>
                <polygon points="205,126 194,133 169,127 166,124 159,138 162,153 172,157 182,151 183,147 195,150 206,158 213,147 206,142 209,130"/>
                <polygon points="188,110 183,114 186,120 178,129 194,133 205,126 208,120 197,117"/>
                <polygon points="217,175 222,176 222,181 212,186 211,185 217,175 201,168 190,187 170,203 150,219 145,220 135,215 134,206 152,198 151,192 138,190 151,164 158,154 162,153 172,157 182,152 183,147 195,150 206,158 201,168"/>
                <polygon points="119,130 111,131 101,146 108,151 108,160 98,165 93,175 83,180 107,187 110,196 125,206 124,214 129,212 135,215 134,206 152,198 151,192 137,190 151,164 148,153 140,154 121,141 121,133"/>
                <polygon points="179,71 175,82 176,95 188,111 183,114 186,120 178,129 170,127 166,124 158,106 164,96 159,88 170,79 170,71"/>
                <polygon points="149,218 145,220 129,211 120,216 109,216 108,229 113,247 126,257 125,266 136,279 151,276 162,269 174,254 162,247 151,232"/>
                <polygon points="53,180 67,186 83,181 107,186 110,195 125,207 124,213 119,216 96,218 93,224 57,224 54,218 51,218 39,210 44,204 44,195 50,186"/>
                <polygon points="22,269 22,283 42,291 54,310 57,331 92,321 105,321 124,316 129,311 136,311 145,322 149,322 143,312 149,299 144,295 145,284 135,279 124,265 125,258 113,247 107,229 109,217 96,218 94,224 74,224 73,230 51,250 29,268"/>
                <polygon points="174,254 178,255 188,250 190,236 202,238 210,244 207,256 213,262 211,278 219,285 236,286 231,324 238,331 237,337 216,348 225,331 211,320 207,295 191,287 183,280 157,287 153,292 144,293 146,284 135,280 151,276 164,267"/>
                <polygon points="57,331 97,359 123,484 138,478 143,477 167,441 156,404 166,383 173,383 177,385 180,380 199,356 216,348 225,331 210,319 207,295 191,287 183,280 165,285 157,287 154,292 144,293 148,299 143,312 149,322 144,322 136,311 128,311 124,316 105,321 90,321"/>
                <polygon points="44,194 44,204 39,210 49,218 54,218 58,224 74,224 72,232 48,252 28,269 21,269 12,251 8,238 10,223 18,216 25,215 27,203"/>
              </g>
              <!-- Decorative pins -->
              <circle class="tn-pin" cx="160" cy="40" r="4"/>
              <circle class="tn-pin" cx="150" cy="160" r="4"/>
              <circle class="tn-pin" cx="170" cy="260" r="4"/>
              <circle class="tn-pin" cx="120" cy="430" r="4"/>
            </svg>
          </div>
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


