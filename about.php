<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>À propos – Tarkina</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Lato:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root { --cream:#f5f2ee; --dark:#1c1c2e; --navy:#1a2340; --orange:#e8642c; --muted:#6b6b6b; --border:#e0dbd4; --white:#fff; --radius:14px; }
    body { font-family:'Lato',sans-serif; background:var(--cream); color:var(--dark); font-size:15px; line-height:1.7; }
    nav { background:var(--white); border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; padding:0 56px; height:60px; position:sticky; top:0; z-index:100; }
    .nav-logo { font-family:'Playfair Display',serif; font-size:22px; font-weight:800; color:var(--dark); text-decoration:none; }
    .nav-logo span { color:var(--orange); }
    .nav-links { display:flex; gap:32px; list-style:none; }
    .nav-links a { text-decoration:none; color:var(--dark); font-size:14px; font-weight:600; opacity:.7; transition:opacity .2s; }
    .nav-links a:hover { opacity:1; }
    .btn-nav { background:var(--orange); color:var(--white); border:none; border-radius:8px; padding:9px 22px; font-size:14px; font-weight:700; text-decoration:none; }

    .hero { background:var(--navy); color:var(--white); padding:80px 56px; text-align:center; }
    .hero h1 { font-family:'Playfair Display',serif; font-size:48px; margin-bottom:16px; }
    .hero p { font-size:18px; opacity:.7; max-width:600px; margin:0 auto; }

    .container { max-width:1000px; margin:0 auto; padding:64px 56px; }

    .section { margin-bottom:64px; }
    .section h2 { font-family:'Playfair Display',serif; font-size:32px; margin-bottom:20px; color:var(--dark); }
    .section p { color:#555; font-size:16px; line-height:1.8; margin-bottom:16px; }

    .values-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:32px; margin-top:32px; }
    .value-card { background:var(--white); border:1px solid var(--border); border-radius:var(--radius); padding:32px; text-align:center; transition:transform .2s; }
    .value-card:hover { transform:translateY(-4px); box-shadow:0 8px 24px rgba(0,0,0,.06); }
    .value-icon { font-size:40px; margin-bottom:16px; }
    .value-card h3 { font-family:'Playfair Display',serif; font-size:20px; margin-bottom:10px; }
    .value-card p { font-size:14px; color:var(--muted); }

    .team-section { background:var(--white); border-radius:var(--radius); padding:48px; border:1px solid var(--border); }
    .team-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:32px; margin-top:32px; }
    .team-member { text-align:center; }
    .team-avatar { width:100px; height:100px; border-radius:50%; background:var(--cream); margin:0 auto 16px; display:flex; align-items:center; justify-content:center; font-size:36px; color:var(--orange); font-family:'Playfair Display',serif; font-weight:800; }
    .team-member h4 { font-size:16px; margin-bottom:4px; }
    .team-member p { font-size:13px; color:var(--muted); }

    .stats { display:grid; grid-template-columns:repeat(4,1fr); gap:24px; margin-top:48px; }
    .stat { background:var(--navy); color:var(--white); border-radius:var(--radius); padding:32px; text-align:center; }
    .stat-num { font-family:'Playfair Display',serif; font-size:36px; font-weight:800; color:var(--orange); }
    .stat-label { font-size:13px; opacity:.7; margin-top:8px; }

    footer { background:var(--navy); color:var(--white); padding:48px 56px; text-align:center; margin-top:40px; }
    footer p { opacity:.6; font-size:14px; }

    @media(max-width:768px) {
      nav { padding:0 20px; }
      .hero { padding:48px 20px; }
      .hero h1 { font-size:32px; }
      .container { padding:40px 20px; }
      .values-grid, .team-grid { grid-template-columns:1fr; }
      .stats { grid-template-columns:repeat(2,1fr); }
    }
  </style>
</head>
<body>

<nav>
  <a class="nav-logo" href="index.php">Tarkina <span>·</span></a>
  <ul class="nav-links">
    <li><a href="index.php">Accueil</a></li>
    <li><a href="explorer.php">Explorer</a></li>
    <li><a href="about.php" style="opacity:1;">À propos</a></li>
    <li><a href="contact.php">Contact</a></li>
  </ul>
  <div>
    <?php if(isset($_SESSION['user_id'])): ?>
      <a href="profile.php" class="btn-nav">Mon profil</a>
    <?php else: ?>
      <a href="login.php" class="btn-nav">Se connecter</a>
    <?php endif; ?>
  </div>
</nav>

<div class="hero">
  <h1>À propos de Tarkina</h1>
  <p>Nous connectons les voyageurs du monde entier avec les trésors cachés de la Tunisie à travers ses habitants.</p>
</div>

<div class="container">

  <div class="section">
    <h2>Notre mission</h2>
    <p>Tarkina est née d'une conviction simple : la vraie richesse de la Tunisie se trouve dans ses régions oubliées, ses traditions vivantes et l'hospitalité légendaire de ses habitants.</p>
    <p>Notre plateforme de tourisme alternatif met en relation les voyageurs curieux avec des hôtes locaux passionnés. Que ce soit pour dormir dans une maison traditionnelle à Kessra, déguster un couscous fait maison à Djerba, ou explorer le Sahara avec un guide bédouin à Tozeur, Tarkina vous offre des expériences authentiques et inoubliables.</p>
    <p>En choisissant Tarkina, vous soutenez directement l'économie locale et contribuez à la préservation du patrimoine culturel tunisien.</p>
  </div>

  <div class="section">
    <h2>Nos valeurs</h2>
    <div class="values-grid">
      <div class="value-card">
        <div class="value-icon">🤝</div>
        <h3>Confiance</h3>
        <p>Chaque hôte et chaque service est vérifié par notre équipe locale pour garantir votre sécurité et votre satisfaction.</p>
      </div>
      <div class="value-card">
        <div class="value-icon">❤️</div>
        <h3>Authenticité</h3>
        <p>Pas de tourisme de masse. Nous privilégions les expériences vraies, au cœur des traditions et de la culture locale.</p>
      </div>
      <div class="value-card">
        <div class="value-icon">🌍</div>
        <h3>Impact local</h3>
        <p>Chaque réservation soutient directement les familles et les artisans des régions que vous visitez.</p>
      </div>
    </div>
  </div>

  <div class="stats">
    <div class="stat">
      <div class="stat-num">500+</div>
      <div class="stat-label">Voyageurs satisfaits</div>
    </div>
    <div class="stat">
      <div class="stat-num">15</div>
      <div class="stat-label">Régions couvertes</div>
    </div>
    <div class="stat">
      <div class="stat-num">120</div>
      <div class="stat-label">Hôtes partenaires</div>
    </div>
    <div class="stat">
      <div class="stat-num">98%</div>
      <div class="stat-label">Taux de satisfaction</div>
    </div>
  </div>

  <div class="section" style="margin-top:64px;">
    <div class="team-section">
      <h2>L'équipe Tarkina</h2>
      <p>Une équipe passionnée par la Tunisie et le tourisme responsable.</p>
      <div class="team-grid">
        <div class="team-member">
          <div class="team-avatar">M</div>
          <h4>Malek Stiti</h4>
          <p>Fondateur & Développeur</p>
        </div>
        <div class="team-member">
          <div class="team-avatar">A</div>
          <h4>Ahmed Ben Salem</h4>
          <p>Responsable des partenariats</p>
        </div>
        <div class="team-member">
          <div class="team-avatar">S</div>
          <h4>Sara Mansouri</h4>
          <p>Community Manager</p>
        </div>
      </div>
    </div>
  </div>

</div>

<footer>
  <p>&copy; <?= date('Y') ?> Tarkina — Voyagez autrement en Tunisie.</p>
</footer>

</body>
</html>
