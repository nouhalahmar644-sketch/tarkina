<?php
/**
 * Shared site navbar (Tarkina).
 * Usage:  <?php include 'navbar.php'; ?>
 * For the homepage hero (transparent over image): set  $navTransparent = true;  before the include.
 * Self-contained: ships its own scoped styles (tk- prefix) so it never clashes with page CSS.
 */
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}
if (isset($_GET['lang']) && in_array($_GET['lang'], ['fr', 'en', 'ar'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = $_SESSION['lang'] ?? 'fr';
$t = [
    'fr' => ['home'=>'Accueil', 'explore'=>'Explorer', 'blog'=>'Blog', 'about'=>'À propos', 'contact'=>'Contact', 'profile'=>'Mon Profil', 'logout'=>'Déconnexion', 'login'=>'Connexion', 'register'=>"S'inscrire", 'search_title'=>'Rechercher', 'search_placeholder'=>'Rechercher...', 'search_btn'=>'Chercher', 'profile_title'=>'Profil', 'dashboard'=>'Dashboard'],
    'en' => ['home'=>'Home', 'explore'=>'Explore', 'blog'=>'Blog', 'about'=>'About', 'contact'=>'Contact', 'profile'=>'My Profile', 'logout'=>'Logout', 'login'=>'Login', 'register'=>'Sign up', 'search_title'=>'Search', 'search_placeholder'=>'Search...', 'search_btn'=>'Search', 'profile_title'=>'Profile', 'dashboard'=>'Dashboard'],
    'ar' => ['home'=>'الرئيسية', 'explore'=>'استكشف', 'blog'=>'المدونة', 'about'=>'من نحن', 'contact'=>'اتصل بنا', 'profile'=>'ملفي', 'logout'=>'تسجيل الخروج', 'login'=>'تسجيل الدخول', 'register'=>'إنشاء حساب', 'search_title'=>'ابحث', 'search_placeholder'=>'ابحث...', 'search_btn'=>'بحث', 'profile_title'=>'الملف الشخصي', 'dashboard'=>'لوحة التحكم']
][$lang];
$navDir = ($lang === 'ar') ? 'dir="rtl"' : '';

$navTransparent = isset($navTransparent) ? (bool) $navTransparent : false;
$__tkCur = basename(parse_url($_SERVER['REQUEST_URI'] ?? ($_SERVER['PHP_SELF'] ?? 'index.php'), PHP_URL_PATH));
$__tkIsAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
$__tkLoggedIn = isset($_SESSION['user_id']);
$tk_active = function (string $f) use ($__tkCur) {
    return $__tkCur === $f ? 'tk-nav-link active' : 'tk-nav-link';
};
?>
<link rel="stylesheet" href="assets/css/typography.css">
<link rel="stylesheet" href="assets/css/rtl.css">
<style>
.tk-nav{position:fixed;top:0;left:0;right:0;z-index:1000;height:68px;display:flex;align-items:center;justify-content:space-between;padding:0 clamp(18px,5vw,60px);background:#fff;border-bottom:1px solid #ededed;box-shadow:0 1px 14px rgba(17,17,17,.05);transition:background .3s,box-shadow .3s,border-color .3s;font-family:'Lato','Segoe UI',system-ui,sans-serif;}
.tk-nav__logo{display:flex;align-items:center;gap:8px;text-decoration:none;font-family:'Playfair Display',Georgia,serif;font-weight:800;font-size:1.45rem;color:#111111;letter-spacing:-.5px;}
.tk-nav__logo img{height:34px;width:auto;display:block;}
.tk-nav__menu{display:flex;align-items:center;gap:34px;}
.tk-nav__links{display:flex;gap:30px;list-style:none;margin:0;padding:0;}
.tk-nav-link{position:relative;text-decoration:none;color:#33404a;font-size:.95rem;font-weight:600;padding:6px 2px;transition:color .2s;}
.tk-nav-link:hover{color:#1B6B45;}
.tk-nav-link.active{color:#1B6B45;}
.tk-nav-link.active::after{content:'';position:absolute;left:0;right:0;bottom:-7px;height:2px;background:#1B6B45;border-radius:2px;}
.tk-nav__auth{display:flex;gap:10px;align-items:center;}
.tk-btn{padding:7px 15px;border-radius:50px;font-size:.85rem;font-weight:700;text-decoration:none;cursor:pointer;transition:all .2s;border:1.5px solid transparent;white-space:nowrap;display:inline-flex;align-items:center;gap:6px;line-height:1;}
.tk-btn--solid{background:#1B6B45;color:#fff;}
.tk-btn--solid:hover{background:#155a38;}
.tk-btn--outline{border-color:#111111;color:#111111;background:transparent;}
.tk-btn--outline:hover{background:#111111;color:#fff;}
.tk-nav__toggle{display:none;background:none;border:none;cursor:pointer;padding:8px;margin:-8px;}
.tk-nav__toggle span{display:block;width:24px;height:2px;background:#111111;border-radius:2px;margin:5px 0;transition:.3s;}
}
.tk-nav-spacer{height:68px;}
.tk-icon-link{color:#111111;text-decoration:none;transition:color .2s;display:flex;align-items:center;padding:4px;}
.tk-icon-link:hover{color:#1B6B45;}
.lang-menu{display:none;position:absolute;top:100%;right:0;background:#fff;border:1px solid #ededed;border-radius:8px;padding:8px;flex-direction:column;gap:4px;box-shadow:0 4px 12px rgba(17,17,17,0.1);min-width:130px;z-index:100;margin-top:10px;}
.lang-dropdown:hover .lang-menu, .lang-menu.show{display:flex;}
.lang-menu a{color:#333;text-decoration:none;padding:6px 10px;border-radius:4px;font-size:0.9rem;}
.lang-menu a:hover{background:#f5f5f5;}
.search-menu{display:none;position:absolute;top:100%;right:0;background:#fff;border:1px solid #ededed;border-radius:8px;padding:12px;box-shadow:0 4px 12px rgba(17,17,17,0.1);min-width:280px;z-index:100;margin-top:10px;}
.search-menu.show{display:block;}
.tk-nav-icons-auth{display:flex;align-items:center;}
</style>
<nav class="tk-nav" id="tkNav" <?= $navDir ?>>
  <a href="index.php" class="tk-nav__logo">
    <img src="images/tarkinalogo.png" alt="Tarkina" style="height:32px; max-height:32px;">
  </a>

  <button class="tk-nav__toggle" id="tkToggle" aria-label="Menu" aria-expanded="false">
    <span></span><span></span><span></span>
  </button>
  <div class="tk-nav__menu" id="tkMenu">
    <ul class="tk-nav__links">
      <li><a href="index.php" class="<?= $tk_active('index.php') ?>"><?= $t['home'] ?></a></li>
      <li><a href="explorer.php" class="<?= $tk_active('explorer.php') ?>"><?= $t['explore'] ?></a></li>
      <li><a href="blogs.php" class="<?= strpos($__tkCur, 'blog') === 0 ? 'tk-nav-link active' : 'tk-nav-link' ?>"><?= $t['blog'] ?></a></li>
      <li><a href="about.php" class="<?= $tk_active('about.php') ?>"><?= $t['about'] ?></a></li>
      <li><a href="contact.php" class="<?= $tk_active('contact.php') ?>"><?= $t['contact'] ?></a></li>
    </ul>
    <div class="tk-nav-icons-auth">
      <div class="tk-nav-icons" style="display:flex; gap:12px; align-items:center; margin-right:20px; <?= $lang === 'ar' ? 'margin-right:0; margin-left:20px;' : '' ?>">
          <div class="search-dropdown" style="position:relative;">
              <a href="#" id="searchIconLink" title="<?= $t['search_title'] ?>" class="tk-icon-link" onclick="document.getElementById('searchMenu').classList.toggle('show'); return false;">
                  <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
              </a>
              <div id="searchMenu" class="search-menu">
                  <form action="search.php" method="GET" style="display:flex; gap:8px; margin:0;">
                      <input type="text" name="q" placeholder="<?= $t['search_placeholder'] ?>" style="padding:8px 12px; border:1px solid #ddd; border-radius:4px; outline:none; flex:1; font-size:0.9rem;">
                      <button type="submit" style="background:#1B6B45; color:#fff; border:none; padding:8px 16px; border-radius:4px; cursor:pointer; font-weight:600; font-size:0.9rem;"><?= $t['search_btn'] ?></button>
                  </form>
              </div>
          </div>
          
          <div class="lang-dropdown" style="position:relative;">
              <a href="#" id="langIconLink" class="tk-icon-link" onclick="document.getElementById('langMenu').classList.toggle('show'); return false;"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg></a>
              <div id="langMenu" class="lang-menu">
                  <a href="?lang=fr">🇫🇷 Français</a>
                  <a href="?lang=en">🇬🇧 English</a>
                  <a href="?lang=ar">🇹🇳 العربية</a>
              </div>
          </div>

          <a href="profile.php" title="<?= $t['profile_title'] ?>" class="tk-icon-link"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg></a>
      </div>
      <div class="tk-nav__auth">
        <?php if ($__tkLoggedIn): ?>
          <?php if ($__tkIsAdmin): ?>
            <a href="admin/dashboard.php" class="tk-btn tk-btn--outline"><?= $t['dashboard'] ?></a>
          <?php endif; ?>
          <a href="logout.php" class="tk-btn tk-btn--outline"><?= $t['logout'] ?></a>
        <?php else: ?>
          <a href="login.php" class="tk-btn tk-btn--outline"><?= $t['login'] ?></a>
          <a href="register.php" class="tk-btn tk-btn--solid"><?= $t['register'] ?></a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
<div class="tk-nav-spacer"></div>
<script>
(function(){
  var nav=document.getElementById('tkNav');
  var toggle=document.getElementById('tkToggle');
  if(toggle&&nav){toggle.addEventListener('click',function(){var o=nav.classList.toggle('open');toggle.setAttribute('aria-expanded',o);});}
  document.addEventListener('click', function(e) {
    var sm = document.getElementById('searchMenu');
    var si = document.getElementById('searchIconLink');
    if (sm && sm.classList.contains('show') && !sm.contains(e.target) && (!si || !si.contains(e.target))) {
      sm.classList.remove('show');
    }
    var lm = document.getElementById('langMenu');
    var li = document.getElementById('langIconLink');
    if (lm && lm.classList.contains('show') && !lm.contains(e.target) && (!li || !li.contains(e.target))) {
      lm.classList.remove('show');
    }
  });
})();
</script>

