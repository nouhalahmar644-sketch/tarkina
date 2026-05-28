<?php
/**
 * Shared site footer (Tarkina).  Usage:  <?php include 'footer.php'; ?>
 * Self-contained scoped styles (tkf- prefix).
 */
?>
<style>
.tkf{background:#111111;color:#fff;padding:56px clamp(20px,5vw,60px) 26px;margin-top:60px;font-family:'Lato','Segoe UI',system-ui,sans-serif;}
.tkf__grid{display:grid;grid-template-columns:1.6fr 1fr 1fr 1.2fr;gap:40px;max-width:1200px;margin:0 auto 40px;}
.tkf__brand-name{font-family:'Playfair Display',Georgia,serif;font-size:1.6rem;font-weight:800;margin-bottom:12px;}
.tkf__brand-desc{color:rgba(255,255,255,.6);font-size:.9rem;line-height:1.75;margin-bottom:18px;max-width:300px;}
.tkf__socials{display:flex;gap:10px;}
.tkf__socials a{width:38px;height:38px;border-radius:50%;background:rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;color:#fff;text-decoration:none;font-size:.8rem;font-weight:700;transition:background .2s;}
.tkf__socials a:hover{background:#1B6B45;}
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
      <div class="tkf__brand-name">Tarkina</div>
      <p class="tkf__brand-desc">Découvrez la Tunisie cachée à travers ses habitants, ses saveurs et son artisanat.</p>
      <div class="tkf__socials">
        <a href="https://instagram.com" target="_blank" rel="noopener" title="Instagram">ig</a>
        <a href="https://facebook.com" target="_blank" rel="noopener" title="Facebook">fb</a>
        <a href="https://twitter.com" target="_blank" rel="noopener" title="X">x</a>
      </div>
    </div>
    <div class="tkf__col">
      <h4>Explorer</h4>
      <ul>
        <li><a href="explorer.php">Toutes les régions</a></li>
        <li><a href="blogs.php">Le Blog</a></li>
        <li><a href="search.php?type=hebergement">Hébergements</a></li>
        <li><a href="search.php?type=repas">Repas maison</a></li>
        <li><a href="search.php?type=guide">Guides locaux</a></li>
      </ul>
    </div>
    <div class="tkf__col">
      <h4>À propos</h4>
      <ul>
        <li><a href="about.php">Qui sommes-nous</a></li>
        <li><a href="contact.php">Contact</a></li>
        <li><a href="register.php">Devenir hôte</a></li>
        <li><a href="search.php?type=artisanat">Artisanat</a></li>
        <li><a href="search.php?type=evenement">Événements</a></li>
      </ul>
    </div>
    <div class="tkf__col">
      <h4>Contact</h4>
      <p class="tkf__contact">📍 Tunis, Tunisie<br>✉️ hello@tarkina.tn<br>📞 +216 71 000 000</p>
    </div>
  </div>
  <div class="tkf__bottom">
    <span>© 2026 Tarkina — Voyagez autrement en Tunisie.</span>
    <span><a href="about.php">Mentions légales</a> · <a href="contact.php">Confidentialité</a></span>
  </div>
</footer>

