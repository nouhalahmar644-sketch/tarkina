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
$navTransparent = isset($navTransparent) ? (bool) $navTransparent : false;
$__tkCur = basename(parse_url($_SERVER['REQUEST_URI'] ?? ($_SERVER['PHP_SELF'] ?? 'index.php'), PHP_URL_PATH));
$__tkIsAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
$__tkLoggedIn = isset($_SESSION['user_id']);
$tk_active = function (string $f) use ($__tkCur) {
    return $__tkCur === $f ? 'tk-nav-link active' : 'tk-nav-link';
};
?>
<link rel="stylesheet" href="assets/css/typography.css">
<style>
.tk-nav{position:fixed;top:0;left:0;right:0;z-index:1000;height:68px;display:flex;align-items:center;justify-content:space-between;padding:0 clamp(18px,5vw,60px);background:#fff;border-bottom:1px solid #ededed;box-shadow:0 1px 14px rgba(27,58,75,.05);transition:background .3s,box-shadow .3s,border-color .3s;font-family:'Lato','Segoe UI',system-ui,sans-serif;}
.tk-nav__logo{display:flex;align-items:center;gap:8px;text-decoration:none;font-family:'Playfair Display',Georgia,serif;font-weight:800;font-size:1.45rem;color:#1B3A4B;letter-spacing:-.5px;}
.tk-nav__logo img{height:34px;width:auto;display:block;}
.tk-nav__menu{display:flex;align-items:center;gap:34px;}
.tk-nav__links{display:flex;gap:30px;list-style:none;margin:0;padding:0;}
.tk-nav-link{position:relative;text-decoration:none;color:#33404a;font-size:.95rem;font-weight:600;padding:6px 2px;transition:color .2s;}
.tk-nav-link:hover{color:#E05A2B;}
.tk-nav-link.active{color:#E05A2B;}
.tk-nav-link.active::after{content:'';position:absolute;left:0;right:0;bottom:-7px;height:2px;background:#E05A2B;border-radius:2px;}
.tk-nav__auth{display:flex;gap:10px;align-items:center;}
.tk-btn{padding:7px 15px;border-radius:50px;font-size:.85rem;font-weight:700;text-decoration:none;cursor:pointer;transition:all .2s;border:1.5px solid transparent;white-space:nowrap;display:inline-flex;align-items:center;gap:6px;line-height:1;}
.tk-btn--solid{background:#E05A2B;color:#fff;}
.tk-btn--solid:hover{background:#c44d22;}
.tk-btn--outline{border-color:#1B3A4B;color:#1B3A4B;background:transparent;}
.tk-btn--outline:hover{background:#1B3A4B;color:#fff;}
.tk-nav__toggle{display:none;background:none;border:none;cursor:pointer;padding:8px;margin:-8px;}
.tk-nav__toggle span{display:block;width:24px;height:2px;background:#1B3A4B;border-radius:2px;margin:5px 0;transition:.3s;}
.tk-nav-spacer{height:68px;}
/* transparent (homepage hero) variant */
.tk-nav--transparent{background:transparent;border-color:transparent;box-shadow:none;}
.tk-nav--transparent .tk-nav__logo{color:#fff;}
.tk-nav--transparent .tk-nav-link{color:rgba(255,255,255,.92);}
.tk-nav--transparent .tk-nav-link:hover,.tk-nav--transparent .tk-nav-link.active{color:#fff;}
.tk-nav--transparent .tk-nav-link.active::after{background:#fff;}
.tk-nav--transparent .tk-btn--outline{border-color:rgba(255,255,255,.85);color:#fff;}
.tk-nav--transparent .tk-btn--outline:hover{background:#fff;color:#1B3A4B;}
.tk-nav--transparent .tk-nav__toggle span{background:#fff;}
.tk-nav--transparent.tk-nav--scrolled{background:#fff;border-bottom-color:#ededed;box-shadow:0 1px 14px rgba(27,58,75,.06);}
.tk-nav--transparent.tk-nav--scrolled .tk-nav__logo{color:#1B3A4B;}
.tk-nav--transparent.tk-nav--scrolled .tk-nav-link{color:#33404a;}
.tk-nav--transparent.tk-nav--scrolled .tk-nav-link:hover,.tk-nav--transparent.tk-nav--scrolled .tk-nav-link.active{color:#E05A2B;}
.tk-nav--transparent.tk-nav--scrolled .tk-nav-link.active::after{background:#E05A2B;}
.tk-nav--transparent.tk-nav--scrolled .tk-btn--outline{border-color:#1B3A4B;color:#1B3A4B;}
.tk-nav--transparent.tk-nav--scrolled .tk-nav__toggle span{background:#1B3A4B;}
@media(max-width:880px){
  .tk-nav__toggle{display:block;}
  .tk-nav__menu{position:fixed;top:68px;left:0;right:0;flex-direction:column;align-items:stretch;gap:18px;background:#fff;padding:22px 28px 28px;border-bottom:1px solid #ededed;box-shadow:0 16px 30px rgba(27,58,75,.10);transform:translateY(-130%);opacity:0;pointer-events:none;transition:transform .3s ease,opacity .2s;}
  .tk-nav.open .tk-nav__menu{transform:translateY(0);opacity:1;pointer-events:auto;}
  .tk-nav__links{flex-direction:column;gap:6px;}
  .tk-nav-link{padding:10px 2px;color:#33404a;font-size:1rem;}
  .tk-nav-link.active::after{display:none;}
  .tk-nav__auth{flex-direction:column;align-items:stretch;}
  .tk-btn{text-align:center;justify-content:center;padding:12px 20px;}
  /* mobile menu is always solid even in transparent mode */
  .tk-nav--transparent .tk-nav-link{color:#33404a;}
  .tk-nav--transparent .tk-btn--outline{border-color:#1B3A4B;color:#1B3A4B;}
}
</style>
<nav class="tk-nav<?= $navTransparent ? ' tk-nav--transparent' : '' ?>" id="tkNav">
  <a href="index.php" class="tk-nav__logo">
    <img src="assets/img/logo.png" alt="" onerror="this.style.display='none'">
    <span>Tarkina</span>
  </a>
  <button class="tk-nav__toggle" id="tkToggle" aria-label="Menu" aria-expanded="false">
    <span></span><span></span><span></span>
  </button>
  <div class="tk-nav__menu" id="tkMenu">
    <ul class="tk-nav__links">
      <li><a href="index.php" class="<?= $tk_active('index.php') ?>">Accueil</a></li>
      <li><a href="explorer.php" class="<?= $tk_active('explorer.php') ?>">Explorer</a></li>
      <li><a href="blogs.php" class="<?= strpos($__tkCur, 'blog') === 0 ? 'tk-nav-link active' : 'tk-nav-link' ?>">Blog</a></li>
      <li><a href="about.php" class="<?= $tk_active('about.php') ?>">À propos</a></li>
      <li><a href="contact.php" class="<?= $tk_active('contact.php') ?>">Contact</a></li>
    </ul>
    <div class="tk-nav__auth">
      <?php if ($__tkLoggedIn): ?>
        <?php if ($__tkIsAdmin): ?>
          <a href="admin/dashboard.php" class="tk-btn tk-btn--outline">Dashboard</a>
        <?php endif; ?>
        <a href="profile.php" class="tk-btn tk-btn--solid">Mon Profil</a>
        <a href="logout.php" class="tk-btn tk-btn--outline">Déconnexion</a>
      <?php else: ?>
        <a href="login.php" class="tk-btn tk-btn--outline">Connexion</a>
        <a href="register.php" class="tk-btn tk-btn--solid">S'inscrire</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
<?php if (!$navTransparent): ?><div class="tk-nav-spacer"></div><?php endif; ?>
<script>
(function(){
  var nav=document.getElementById('tkNav');
  var toggle=document.getElementById('tkToggle');
  if(toggle&&nav){toggle.addEventListener('click',function(){var o=nav.classList.toggle('open');toggle.setAttribute('aria-expanded',o);});}
  if(nav&&nav.classList.contains('tk-nav--transparent')){
    var onScroll=function(){nav.classList.toggle('tk-nav--scrolled',window.scrollY>40);};
    window.addEventListener('scroll',onScroll,{passive:true});onScroll();
  }
})();
</script>
<?php // reset so a later include in the same request defaults to solid
$navTransparent = false; ?>
