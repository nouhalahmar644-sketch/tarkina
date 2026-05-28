<?php
session_start();
mb_internal_encoding('UTF-8');
header('Content-Type: text/html; charset=utf-8');
require 'db.php';
require_once __DIR__ . '/includes/i18n.php';
mysqli_set_charset($conn, 'utf8mb4');

$L_ALL = [
    'fr' => [
        'err_invalid'    => 'Région invalide.',
        'err_notfound'   => 'Région introuvable.',
        'lbl_destination'=> 'Destination',
        'lbl_arrival'    => 'Arrivée',
        'lbl_departure'  => 'Départ',
        'lbl_travelers'  => 'Voyageurs',
        'btn_search'     => 'Rechercher',
        'filters'        => 'Filtres',
        'category'       => 'Catégorie',
        'price_max'      => 'Prix max',
        'up_to'          => "Jusqu'à",
        'reset'          => 'Réinitialiser',
        'experience'     => 'expérience',
        'experiences'    => 'expériences',
        'available'      => 'disponible',
        'availables'     => 'disponibles',
        'traveler'       => 'voyageur',
        'travelers_word' => 'voyageurs',
        'for_prefix'     => 'Pour',
        'sort_default'   => 'Recommandés ▾',
        'sort_asc'       => 'Prix croissant',
        'sort_desc'      => 'Prix décroissant',
        'none_in_region' => 'Aucun service disponible dans cette région pour le moment.',
        'reserve'        => 'Réserver',
        'c_hebergement'  => '🏠 Hébergement',
        'c_repas'        => '🍽️ Repas maison',
        'c_guide'        => '🧭 Guide local',
        'c_artisanat'    => '💎 Produits artisanaux',
        'c_evenement'    => '🎉 Événements',
        'b_hebergement'  => 'Hébergement',
        'b_repas'        => 'Repas maison',
        'b_guide'        => 'Guide local',
        'b_artisanat'    => 'Artisanat',
        'b_evenement'    => 'Événement',
    ],
    'ar' => [
        'err_invalid'    => 'الجهة غير صالحة.',
        'err_notfound'   => 'الجهة غير موجودة.',
        'lbl_destination'=> 'الوجهة',
        'lbl_arrival'    => 'الوصول',
        'lbl_departure'  => 'المغادرة',
        'lbl_travelers'  => 'المسافرون',
        'btn_search'     => 'ابحث',
        'filters'        => 'الفلاتر',
        'category'       => 'الفئة',
        'price_max'      => 'السعر الأقصى',
        'up_to'          => 'حتى',
        'reset'          => 'إعادة تعيين',
        'experience'     => 'تجربة',
        'experiences'    => 'تجارب',
        'available'      => 'متاحة',
        'availables'     => 'متاحة',
        'traveler'       => 'مسافر',
        'travelers_word' => 'مسافرين',
        'for_prefix'     => 'لـ',
        'sort_default'   => 'الموصى به ▾',
        'sort_asc'       => 'السعر تصاعدياً',
        'sort_desc'      => 'السعر تنازلياً',
        'none_in_region' => 'لا توجد خدمات متاحة في هذه الجهة حالياً.',
        'reserve'        => 'احجز',
        'c_hebergement'  => '🏠 إقامة',
        'c_repas'        => '🍽️ وجبة منزلية',
        'c_guide'        => '🧭 مرشد محلي',
        'c_artisanat'    => '💎 منتجات حرفية',
        'c_evenement'    => '🎉 فعاليات',
        'b_hebergement'  => 'إقامة',
        'b_repas'        => 'وجبة منزلية',
        'b_guide'        => 'مرشد محلي',
        'b_artisanat'    => 'حِرف يدوية',
        'b_evenement'    => 'فعالية',
    ],
    'en' => [
        'err_invalid'    => 'Invalid region.',
        'err_notfound'   => 'Region not found.',
        'lbl_destination'=> 'Destination',
        'lbl_arrival'    => 'Arrival',
        'lbl_departure'  => 'Departure',
        'lbl_travelers'  => 'Travelers',
        'btn_search'     => 'Search',
        'filters'        => 'Filters',
        'category'       => 'Category',
        'price_max'      => 'Max price',
        'up_to'          => 'Up to',
        'reset'          => 'Reset',
        'experience'     => 'experience',
        'experiences'    => 'experiences',
        'available'      => 'available',
        'availables'     => 'available',
        'traveler'       => 'traveler',
        'travelers_word' => 'travelers',
        'for_prefix'     => 'For',
        'sort_default'   => 'Recommended ▾',
        'sort_asc'       => 'Price ascending',
        'sort_desc'      => 'Price descending',
        'none_in_region' => 'No services available in this region at the moment.',
        'reserve'        => 'Book',
        'c_hebergement'  => '🏠 Accommodation',
        'c_repas'        => '🍽️ Home meal',
        'c_guide'        => '🧭 Local guide',
        'c_artisanat'    => '💎 Crafts',
        'c_evenement'    => '🎉 Events',
        'b_hebergement'  => 'Accommodation',
        'b_repas'        => 'Home meal',
        'b_guide'        => 'Local guide',
        'b_artisanat'    => 'Crafts',
        'b_evenement'    => 'Event',
    ],
];
$L = $L_ALL[$lang] ?? $L_ALL['fr'];

$region_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($region_id <= 0) die(htmlspecialchars($L['err_invalid']));

$st = mysqli_prepare($conn, "SELECT * FROM region WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($st, 'i', $region_id);
mysqli_stmt_execute($st);
$res = mysqli_stmt_get_result($st);
$region = mysqli_fetch_assoc($res);
mysqli_stmt_close($st);
if (!$region) die(htmlspecialchars($L['err_notfound']));

$nom = $region['nom'];
$reqDebut = isset($_GET['date_debut']) ? trim($_GET['date_debut']) : '';
$reqFin   = isset($_GET['date_fin'])   ? trim($_GET['date_fin'])   : '';
$reqPers  = isset($_GET['personnes'])  ? max(1,(int)$_GET['personnes']) : 1;

$cats = ['hebergement','repas','guide','artisanat','evenement'];
$prix_max = 1000;

$all_items = [];

function fetch_category($conn, $table, $type_label, $region_id, $prix_max) {
    $items = [];
    $r = mysqli_query($conn, "SELECT * FROM `$table` WHERE region_id = $region_id AND prix <= $prix_max AND statut IN ('actif','publié')");
    while ($r && $row = mysqli_fetch_assoc($r)) {
        $row['_type'] = $type_label;
        $items[] = $row;
    }
    return $items;
}

$all_items = array_merge(
    fetch_category($conn, 'hebergement', 'hebergement', $region_id, $prix_max),
    fetch_category($conn, 'repas',       'repas',       $region_id, $prix_max),
    fetch_category($conn, 'guide',       'guide',       $region_id, $prix_max),
    fetch_category($conn, 'artisanat',   'artisanat',   $region_id, $prix_max),
    fetch_category($conn, 'evenement',   'evenement',   $region_id, $prix_max)
);

$total = count($all_items);
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>" dir="<?= htmlspecialchars($dir) ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($nom, ENT_QUOTES, 'UTF-8') ?> — Tarkina</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Lato:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/rtl.css">
<style>
*,*::before,*::after{box-sizing:border-box;}
:root{--navy:#111111;--coral:#1B6B45;--cream:#FFFFFF;--border:#E5E7EB;--muted:#6B7280;}
body{font-family:'Lato',sans-serif;background:var(--cream);}

nav.top-nav{background:#fff;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;padding:0 40px;height:62px;position:sticky;top:0;z-index:200;}
.nav-logo{font-family:'Playfair Display',serif;font-size:1.3rem;font-weight:800;color:var(--navy);text-decoration:none;}
.nav-links{display:flex;gap:28px;list-style:none;margin:0;padding:0;}
.nav-links a{text-decoration:none;color:#333;font-size:14px;font-weight:600;opacity:.7;}
.nav-links a:hover{opacity:1;}
.nav-auth{display:flex;gap:10px;align-items:center;}
.btn-primary-nav{background:var(--coral);color:#fff;border-radius:50px;padding:8px 20px;text-decoration:none;font-size:13px;font-weight:700;}
.btn-outline-nav{color:var(--navy);border:1.5px solid var(--navy);border-radius:50px;padding:8px 20px;text-decoration:none;font-size:13px;font-weight:700;}
.btn-outline-nav:hover{background:var(--navy);color:#fff;}

.search-strip{background:var(--navy);padding:16px 40px;}
.search-form{display:flex;background:#fff;border-radius:12px;overflow:hidden;max-width:900px;height:52px;}
.sf-field{flex:1;display:flex;flex-direction:column;justify-content:center;padding:6px 16px;border-right:1px solid var(--border);}
.sf-label{font-size:9px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);}
.sf-field input{border:none;outline:none;font-size:13px;background:transparent;width:100%;font-family:'Lato',sans-serif;}
.sf-btn{background:var(--coral);color:#fff;border:none;padding:0 24px;font-size:14px;font-weight:700;cursor:pointer;white-space:nowrap;border-radius:0 12px 12px 0;font-family:'Lato',sans-serif;}
.sf-btn:hover{background:#155a38;}

.breadcrumb-bar{padding:12px 40px;font-size:13px;color:var(--muted);}
.breadcrumb-bar a{color:var(--navy);text-decoration:none;font-weight:600;}

.sidebar-box{background:#fff;border-radius:16px;border:1px solid var(--border);padding:24px;position:sticky;top:80px;}
.filter-label-main{font-size:11px;font-weight:700;letter-spacing:.1em;color:var(--navy);text-transform:uppercase;margin-bottom:16px;display:block;}
.filter-label-sub{font-size:10px;font-weight:700;letter-spacing:.08em;color:#aaa;text-transform:uppercase;margin-bottom:10px;display:block;}
.filter-check-item{display:flex;align-items:center;gap:8px;font-size:14px;margin-bottom:8px;cursor:pointer;}
.filter-check-item input{accent-color:var(--coral);}
.price-display{display:flex;justify-content:space-between;font-size:13px;margin-bottom:6px;}
.price-display span{color:#aaa;}
.price-display strong{color:var(--coral);}
.btn-reset{width:100%;margin-top:16px;padding:10px;border:1.5px solid var(--border);border-radius:10px;background:#fff;font-size:14px;cursor:pointer;color:var(--navy);font-weight:600;font-family:'Lato',sans-serif;}
.btn-reset:hover{border-color:var(--navy);}

.results-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:10px;}
.results-header h1{font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:800;color:var(--navy);margin:0;}
.results-header p{font-size:13px;color:#aaa;margin:4px 0 0;}
.sort-sel{border:1.5px solid var(--border);border-radius:10px;padding:8px 14px;font-size:13px;color:var(--navy);background:#fff;font-family:'Lato',sans-serif;cursor:pointer;}

.service-card{background:#fff;border-radius:16px;overflow:hidden;border:1px solid var(--border);height:100%;transition:transform .2s,box-shadow .2s;}
.service-card:hover{transform:translateY(-4px);box-shadow:0 12px 32px rgba(17, 17, 17,.12);}
.service-card img{width:100%;height:200px;object-fit:cover;display:block;}
.card-badge{position:absolute;top:10px;left:10px;border-radius:999px;padding:4px 12px;font-size:12px;font-weight:600;}
.card-body-inner{padding:16px;}
.card-title{font-size:15px;font-weight:700;color:var(--navy);margin:0 0 6px;}
.card-location{font-size:13px;color:#aaa;margin:0 0 8px;}
.card-desc{font-size:13px;color:#555;margin:0 0 14px;line-height:1.5;}
.card-footer-inner{display:flex;align-items:center;justify-content:space-between;}
.card-price{font-size:18px;font-weight:700;color:var(--coral);}
.card-price small{font-size:12px;font-weight:400;color:#aaa;}
.btn-reserver{background:var(--navy);color:#fff;border-radius:999px;padding:8px 20px;font-size:13px;font-weight:600;text-decoration:none;}
.btn-reserver:hover{background:#0f2533;color:#fff;}

footer{background:var(--navy);color:#fff;padding:48px 40px 28px;margin-top:60px;}
.footer-grid{display:grid;grid-template-columns:1.4fr 1fr 1fr;gap:48px;max-width:1200px;margin:0 auto 36px;}
.footer-brand{font-family:'Playfair Display',serif;font-size:1.4rem;font-weight:800;margin-bottom:10px;}
.footer-desc{color:rgba(255,255,255,.55);font-size:13px;line-height:1.7;}
.footer-col h4{font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:rgba(255,255,255,.4);margin-bottom:16px;}
.footer-col ul{list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:10px;}
.footer-col ul li a{color:rgba(255,255,255,.7);text-decoration:none;font-size:13px;}
.footer-col ul li a:hover{color:#fff;}
.footer-bottom{border-top:1px solid rgba(255,255,255,.1);padding-top:20px;text-align:center;font-size:13px;color:rgba(255,255,255,.35);max-width:1200px;margin:0 auto}
.fade-in-up { opacity: 0; transform: translateY(30px); transition: opacity 0.6s ease, transform 0.6s ease; }
.fade-in-up.visible { opacity: 1; transform: translateY(0); }
</style>
</head>
<body>

<?php include 'navbar.php'; ?>

<button onclick="history.back()" 
  style="background:none;border:none;cursor:pointer;font-size:1.3rem;
  color:#111111;padding:14px 0 0 24px;display:flex;align-items:center;gap:6px;"
  onmouseover="this.style.color='#1B6B45'" 
  onmouseout="this.style.color='#111111'">
  &#8592;
</button>

<div class="search-strip">
  <form class="search-form" method="GET" action="region.php">
    <input type="hidden" name="id" value="<?= $region_id ?>">
    <div class="sf-field">
      <span class="sf-label"><?= htmlspecialchars($L['lbl_destination']) ?></span>
      <input type="text" name="destination" value="<?= htmlspecialchars($nom, ENT_QUOTES, 'UTF-8') ?>" readonly>
    </div>
    <div class="sf-field">
      <span class="sf-label"><?= htmlspecialchars($L['lbl_arrival']) ?></span>
      <input type="date" name="date_debut" value="<?= $reqDebut ?>">
    </div>
    <div class="sf-field">
      <span class="sf-label"><?= htmlspecialchars($L['lbl_departure']) ?></span>
      <input type="date" name="date_fin" value="<?= $reqFin ?>">
    </div>
    <div class="sf-field" style="flex:0.6;">
      <span class="sf-label"><?= htmlspecialchars($L['lbl_travelers']) ?></span>
      <input type="number" name="personnes" value="<?= $reqPers ?>" min="1" max="20">
    </div>
    <button type="submit" class="sf-btn"><?= htmlspecialchars($L['btn_search']) ?></button>
  </form>
</div>

<div class="breadcrumb-bar">
  <?= htmlspecialchars($nom, ENT_QUOTES, 'UTF-8') ?>
</div>

<div class="container-fluid px-4 my-4">
  <div class="row g-4">

    <div class="col-lg-3">
      <div class="sidebar-box">
        <span class="filter-label-main"><?= htmlspecialchars($L['filters']) ?></span>

        <span class="filter-label-sub"><?= htmlspecialchars($L['category']) ?></span>
        <?php
        $catLabels = [
            'hebergement' => $L['c_hebergement'],
            'repas'       => $L['c_repas'],
            'guide'       => $L['c_guide'],
            'artisanat'   => $L['c_artisanat'],
            'evenement'   => $L['c_evenement'],
        ];
        foreach ($catLabels as $val => $label): ?>
        <label class="filter-check-item">
          <input type="checkbox" class="cat-filter" value="<?= $val ?>" checked>
          <?= htmlspecialchars($label) ?>
        </label>
        <?php endforeach; ?>

        <span class="filter-label-sub" style="margin-top:16px;"><?= htmlspecialchars($L['price_max']) ?></span>
        <div class="price-display">
          <span><?= htmlspecialchars($L['up_to']) ?></span>
          <strong id="priceLabel">1000 TND</strong>
        </div>
        <input type="range" id="priceSlider" min="0" max="1000" value="1000" step="10" style="width:100%;accent-color:#1B6B45;">
        <button class="btn-reset" id="resetBtn"><?= htmlspecialchars($L['reset']) ?></button>
      </div>
    </div>

    <div class="col-lg-9">
      <div class="results-header">
        <div>
          <h1 id="resultCount"><?= $total ?> <?= htmlspecialchars($total > 1 ? $L['experiences'] : $L['experience']) ?> <?= htmlspecialchars($total > 1 ? $L['availables'] : $L['available']) ?></h1>
          <p><?= htmlspecialchars($L['for_prefix']) ?> <?= $reqPers ?> <?= htmlspecialchars($reqPers > 1 ? $L['travelers_word'] : $L['traveler']) ?> · <?= htmlspecialchars($nom, ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <select id="sortSelect" class="sort-sel">
          <option value="default"><?= htmlspecialchars($L['sort_default']) ?></option>
          <option value="price_asc"><?= htmlspecialchars($L['sort_asc']) ?></option>
          <option value="price_desc"><?= htmlspecialchars($L['sort_desc']) ?></option>
        </select>
      </div>

      <div class="row g-3" id="cardsGrid">
        <?php if ($total === 0): ?>
          <div class="col-12" style="text-align:center;padding:60px 0;color:#aaa;">
            <p><?= htmlspecialchars($L['none_in_region']) ?></p>
          </div>
        <?php else: ?>
          <?php
          $badges = [
            'hebergement' => ['🏠', $L['b_hebergement'], '#e8f4fd', '#1a6fa8'],
            'repas'       => ['🍽️', $L['b_repas'],       '#fff3e0', '#e65100'],
            'guide'       => ['🧭', $L['b_guide'],       '#e8f5e9', '#2e7d32'],
            'artisanat'   => ['💎', $L['b_artisanat'],   '#f3e5f5', '#6a1b9a'],
            'evenement'   => ['🎉', $L['b_evenement'],   '#fce4ec', '#c62828'],
          ];
          foreach ($all_items as $item):
            $type = $item['_type'];
            $b = $badges[$type] ?? ['📌', $type, '#f5f5f5', '#333'];
            $photo = !empty($item['photo_principale']) ? $item['photo_principale'] : 'assets/img/placeholder.jpg';
            $titre = $item['titre'];
            $loc  = !empty($item['localisation']) ? $item['localisation'] : (!empty($item['ville']) ? $item['ville'] : '');
            $desc = !empty($item['description']) ? mb_substr($item['description'], 0, 90, 'UTF-8') . '...' : '';
          ?>
          <div class="col-sm-6 col-xl-4 svc-card-wrap" data-type="<?= $type ?>" data-price="<?= $item['prix'] ?>">
            <div class="service-card fade-in-up">
              <div style="position:relative;">
                <img src="<?= $photo ?>" alt="<?= htmlspecialchars($titre, ENT_QUOTES, 'UTF-8') ?>"
                     onerror="this.src='assets/img/placeholder.jpg'">
                <span class="card-badge" style="background:<?= $b[2] ?>;color:<?= $b[3] ?>;">
                  <?= $b[0] ?> <?= $b[1] ?>
                </span>
              </div>
              <div class="card-body-inner">
                <p class="card-title"><?= $titre ?></p>
                <p class="card-location">📍 <?= $loc ?></p>
                <p class="card-desc"><?= $desc ?></p>
                <div class="card-footer-inner">
                  <span class="card-price"><?= number_format($item['prix'], 0) ?> <small>TND</small></span>
                  <a href="<?= $type ?>.php?id=<?= $item['id'] ?>" class="btn-reserver"><?= htmlspecialchars($L['reserve']) ?></a>
                </div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

  </div>
</div>

<?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const slider = document.getElementById('priceSlider');
const label  = document.getElementById('priceLabel');
const cards  = document.querySelectorAll('.svc-card-wrap');
const countEl= document.getElementById('resultCount');

const I18N_EXP_SINGULAR = <?= json_encode($L['experience'], JSON_UNESCAPED_UNICODE) ?>;
const I18N_EXP_PLURAL   = <?= json_encode($L['experiences'], JSON_UNESCAPED_UNICODE) ?>;
const I18N_AVAIL_SING   = <?= json_encode($L['available'], JSON_UNESCAPED_UNICODE) ?>;
const I18N_AVAIL_PLUR   = <?= json_encode($L['availables'], JSON_UNESCAPED_UNICODE) ?>;

function updateCards() {
  const maxPrice  = parseInt(slider.value);
  const activeCats= [...document.querySelectorAll('.cat-filter:checked')].map(c=>c.value);
  let visible = 0;
  cards.forEach(card => {
    const show = parseFloat(card.dataset.price) <= maxPrice && activeCats.includes(card.dataset.type);
    card.style.display = show ? '' : 'none';
    if (show) visible++;
  });
  const expWord   = visible > 1 ? I18N_EXP_PLURAL : I18N_EXP_SINGULAR;
  const availWord = visible > 1 ? I18N_AVAIL_PLUR : I18N_AVAIL_SING;
  countEl.textContent = visible + ' ' + expWord + ' ' + availWord;
  label.textContent   = maxPrice + ' TND';
}

slider.addEventListener('input', updateCards);
document.querySelectorAll('.cat-filter').forEach(cb => cb.addEventListener('change', updateCards));
document.getElementById('resetBtn').addEventListener('click', () => {
  slider.value = 1000;
  document.querySelectorAll('.cat-filter').forEach(cb => cb.checked = true);
  updateCards();
});
document.getElementById('sortSelect').addEventListener('change', function() {
  const grid  = document.getElementById('cardsGrid');
  const items = [...grid.querySelectorAll('.svc-card-wrap')];
  if (this.value === 'price_asc')  items.sort((a,b) => parseFloat(a.dataset.price) - parseFloat(b.dataset.price));
  if (this.value === 'price_desc') items.sort((a,b) => parseFloat(b.dataset.price) - parseFloat(a.dataset.price));
  items.forEach(el => grid.appendChild(el));
});

const observer = new IntersectionObserver((entries) => {
  entries.forEach(e => { if(e.isIntersecting) { e.target.classList.add('visible'); } });
}, { threshold: 0.1 });
document.querySelectorAll('.fade-in-up').forEach(el => observer.observe(el));
</script>
</body>
</html>
