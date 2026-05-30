<?php
/**
 * Shared site footer (Tarkina).  Usage:  <?php include 'footer.php'; ?>
 * Self-contained scoped styles (tkf- prefix).  Auto-translates FR/AR/EN.
 */
if (session_status() === PHP_SESSION_NONE && !headers_sent()) { session_start(); }
$__fLang = $_SESSION['lang'] ?? 'fr';
$tf = [
    'fr' => [
        'brand_desc' => 'Découvrez la Tunisie cachée à travers ses habitants, ses saveurs et son artisanat.',
        'col_explore' => 'Explorer',
        'all_regions' => 'Toutes les régions',
        'the_blog'    => 'Le Blog',
        'accommodations' => 'Hébergements',
        'meals' => 'Repas maison',
        'guides' => 'Guides locaux',
        'col_about' => 'À propos',
        'who_we_are' => 'Qui sommes-nous',
        'contact' => 'Contact',
        'become_host' => 'Devenir hôte',
        'crafts' => 'Artisanat',
        'events' => 'Événements',
        'col_contact' => 'Contact',
        'contact_lines' => '📍 Tunis, Tunisie<br>✉️ hello@tarkina.tn<br>📞 +216 71 000 000',
        'copyright' => '© 2026 Tarkina — Voyagez autrement en Tunisie.',
        'legal' => 'Mentions légales',
        'privacy' => 'Confidentialité',
    ],
    'en' => [
        'brand_desc' => 'Discover the hidden Tunisia through its people, flavours and crafts.',
        'col_explore' => 'Explore',
        'all_regions' => 'All regions',
        'the_blog'    => 'The Blog',
        'accommodations' => 'Accommodations',
        'meals' => 'Home meals',
        'guides' => 'Local guides',
        'col_about' => 'About',
        'who_we_are' => 'Who we are',
        'contact' => 'Contact',
        'become_host' => 'Become a host',
        'crafts' => 'Crafts',
        'events' => 'Events',
        'col_contact' => 'Contact',
        'contact_lines' => '📍 Tunis, Tunisia<br>✉️ hello@tarkina.tn<br>📞 +216 71 000 000',
        'copyright' => '© 2026 Tarkina — Travel Tunisia differently.',
        'legal' => 'Legal notice',
        'privacy' => 'Privacy',
    ],
    'ar' => [
        'brand_desc' => 'اكتشف تونس الخفية من خلال أهلها، نكهاتها وحرفها التقليدية.',
        'col_explore' => 'استكشف',
        'all_regions' => 'كل الجهات',
        'the_blog'    => 'المدونة',
        'accommodations' => 'الإقامة',
        'meals' => 'وجبات منزلية',
        'guides' => 'مرشدون محليون',
        'col_about' => 'عن تاركينا',
        'who_we_are' => 'من نحن',
        'contact' => 'اتصل بنا',
        'become_host' => 'كن مضيفًا',
        'crafts' => 'الحِرف التقليدية',
        'events' => 'الفعاليات',
        'col_contact' => 'تواصل',
        'contact_lines' => '📍 تونس<br>✉️ hello@tarkina.tn<br>📞 +216 71 000 000',
        'copyright' => '© 2026 تاركينا — سافر إلى تونس بطريقة مختلفة.',
        'legal' => 'إشعار قانوني',
        'privacy' => 'الخصوصية',
    ],
][$__fLang] ?? [];
?>
<style>
.tkf{background:#0b1c30;color:#fff;padding:56px clamp(20px,5vw,60px) 26px;margin-top:60px;font-family:'Lato','Segoe UI',system-ui,sans-serif;}
.tkf__grid{display:grid;grid-template-columns:1.6fr 1fr 1fr 1.2fr;gap:40px;max-width:1200px;margin:0 auto 40px;}
.tkf__brand-name{font-family:'Playfair Display',Georgia,serif;font-size:1.6rem;font-weight:800;margin-bottom:12px;}
.tkf__brand-desc{color:rgba(255,255,255,.6);font-size:.9rem;line-height:1.75;margin-bottom:18px;max-width:300px;}
.tkf__socials{display:flex;gap:10px;}
.tkf__socials a{width:38px;height:38px;border-radius:50%;background:rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;color:#fff;text-decoration:none;font-size:.8rem;font-weight:700;transition:background .2s;}
.tkf__socials a:hover{background:#f16e22;}
.tkf__col h4{font-size:.72rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:rgba(255,255,255,.45);margin:0 0 16px;}
.tkf__col ul{list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:10px;}
.tkf__col a{color:rgba(255,255,255,.72);text-decoration:none;font-size:.9rem;transition:color .2s;}
.tkf__col a:hover{color:#fff;}
.tkf__contact{color:rgba(255,255,255,.72);font-size:.9rem;line-height:2;}
.tkf__bottom{border-top:1px solid rgba(255,255,255,.1);padding-top:22px;max-width:1200px;margin:0 auto;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;font-size:.82rem;color:rgba(255,255,255,.4);}
.tkf__bottom a{color:rgba(255,255,255,.4);text-decoration:none;}
.tkf__bottom a:hover{color:rgba(255,255,255,.7);}
@media(max-width:760px){.tkf__grid{grid-template-columns:1fr 1fr;gap:28px;}.tkf__bottom{flex-direction:column;text-align:center;}}
</style>
<footer class="tkf">
  <div class="tkf__grid">
    <div>
      <div class="tkf__brand-name" style="margin-bottom: 16px;">
        <a href="index.php" style="display: inline-block;">
          <img src="images/tarkinalogo.png" alt="Tarkina" style="height: 32px; max-height: 32px; filter: brightness(0) invert(1); display: block;">
        </a>
      </div>
      <p class="tkf__brand-desc"><?= $tf['brand_desc'] ?></p>
      <div class="tkf__socials">
        <a href="https://instagram.com" target="_blank" rel="noopener" title="Instagram">ig</a>
        <a href="https://facebook.com" target="_blank" rel="noopener" title="Facebook">fb</a>
        <a href="https://twitter.com" target="_blank" rel="noopener" title="X">x</a>
      </div>
    </div>
    <div class="tkf__col">
      <h4><?= $tf['col_explore'] ?></h4>
      <ul>
        <li><a href="explorer.php"><?= $tf['all_regions'] ?></a></li>
        <li><a href="blogs.php"><?= $tf['the_blog'] ?></a></li>
        <li><a href="search.php?type=hebergement"><?= $tf['accommodations'] ?></a></li>
        <li><a href="search.php?type=repas"><?= $tf['meals'] ?></a></li>
        <li><a href="search.php?type=guide"><?= $tf['guides'] ?></a></li>
      </ul>
    </div>
    <div class="tkf__col">
      <h4><?= $tf['col_about'] ?></h4>
      <ul>
        <li><a href="about.php"><?= $tf['who_we_are'] ?></a></li>
        <li><a href="contact.php"><?= $tf['contact'] ?></a></li>
        <li><a href="register.php"><?= $tf['become_host'] ?></a></li>
        <li><a href="search.php?type=artisanat"><?= $tf['crafts'] ?></a></li>
        <li><a href="search.php?type=evenement"><?= $tf['events'] ?></a></li>
      </ul>
    </div>
    <div class="tkf__col">
      <h4><?= $tf['col_contact'] ?></h4>
      <p class="tkf__contact"><?= $tf['contact_lines'] ?></p>
    </div>
  </div>
  <div class="tkf__bottom">
    <span><?= $tf['copyright'] ?></span>
    <span><a href="about.php"><?= $tf['legal'] ?></a> · <a href="contact.php"><?= $tf['privacy'] ?></a></span>
  </div>
</footer>
