<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À propos — Tarkina</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        :root { --primary: #1B6B45; --navy: #111111; --light-bg: #FFFFFF; --text-dark: #1a1a1a; --text-muted: #6b7280; --border: #e5e7eb; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: #fff; color: var(--text-dark); }
        .navbar { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; background: #fff; border-bottom: 1px solid var(--border); padding: 0 60px; height: 70px; display: flex; align-items: center; justify-content: space-between; }
        .nav-logo img { height: 36px; }
        .nav-logo span { font-size: 1.4rem; font-weight: 800; color: var(--navy); }
        .nav-links { display: flex; gap: 36px; list-style: none; }
        .nav-links a { text-decoration: none; color: var(--text-dark); font-size: 0.95rem; font-weight: 500; transition: color .2s; }
        .nav-links a:hover, .nav-links a.active { color: var(--primary); }
        .nav-auth { display: flex; gap: 12px; align-items: center; }
        .btn-nav-outline { padding: 8px 20px; border: 1.5px solid var(--navy); border-radius: 50px; color: var(--navy); text-decoration: none; font-size: 0.9rem; font-weight: 600; transition: all .2s; }
        .btn-nav-outline:hover { background: var(--navy); color: #fff; }
        .btn-nav-primary { padding: 8px 20px; background: var(--primary); border-radius: 50px; color: #fff; text-decoration: none; font-size: 0.9rem; font-weight: 600; transition: background .2s; }
        .btn-nav-primary:hover { background: #155a38; }
        .about-hero { padding: 130px 60px 80px; display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center; max-width: 1200px; margin: 0 auto; }
        .about-label { font-size: 0.78rem; font-weight: 700; letter-spacing: 2px; color: var(--primary); text-transform: uppercase; margin-bottom: 20px; }
        .about-heading { font-size: 2.8rem; font-weight: 800; line-height: 1.2; color: var(--text-dark); margin-bottom: 20px; font-family: Georgia, 'Times New Roman', serif; }
        .about-desc { font-size: 1rem; color: var(--text-muted); line-height: 1.75; margin-bottom: 36px; }
        .about-buttons { display: flex; gap: 16px; flex-wrap: wrap; }
        .btn-primary-solid { padding: 13px 28px; background: var(--primary); color: #fff; border-radius: 50px; text-decoration: none; font-weight: 700; font-size: 0.95rem; transition: background .2s; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary-solid:hover { background: #155a38; }
        .btn-outline-dark { padding: 13px 28px; border: 2px solid var(--text-dark); color: var(--text-dark); border-radius: 50px; text-decoration: none; font-weight: 700; font-size: 0.95rem; transition: all .2s; }
        .btn-outline-dark:hover { background: var(--text-dark); color: #fff; }
        .about-images { position: relative; height: 420px; }
        .about-img-main { position: absolute; right: 0; top: 0; width: 78%; height: 320px; border-radius: 16px; object-fit: cover; box-shadow: 0 20px 50px rgba(0,0,0,0.15); }
        .about-img-accent { position: absolute; left: 0; bottom: 0; width: 52%; height: 220px; border-radius: 16px; object-fit: cover; box-shadow: 0 20px 50px rgba(0,0,0,0.2); border: 4px solid #fff; }
        .section-divider { border: none; border-top: 1px solid var(--border); margin: 0 60px; }
        .mission-section { padding: 80px 60px; max-width: 1200px; margin: 0 auto; }
        .mission-grid { display: grid; grid-template-columns: 1fr 1.4fr; gap: 80px; margin-bottom: 60px; align-items: start; }
        .mission-label { font-size: 0.78rem; font-weight: 700; letter-spacing: 2px; color: var(--primary); text-transform: uppercase; margin-bottom: 16px; }
        .mission-heading { font-size: 2.4rem; font-weight: 800; line-height: 1.2; font-family: Georgia, serif; }
        .mission-text { font-size: 0.97rem; color: var(--text-muted); line-height: 1.75; margin-bottom: 16px; }
        .values-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; }
        .value-card { background: #fff; border: 1px solid var(--border); border-radius: 16px; padding: 28px 24px; transition: box-shadow .2s, transform .2s; }
        .value-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.08); transform: translateY(-3px); }
        .value-icon { width: 48px; height: 48px; background: #FEF0EA; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; }
        .value-icon svg { width: 22px; height: 22px; stroke: var(--primary); }
        .value-title { font-weight: 700; font-size: 0.95rem; margin-bottom: 8px; }
        .value-desc { font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; }
        .destinations-section { padding: 80px 60px; background: var(--light-bg); }
        .destinations-inner { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center; }
        .destinations-img { width: 100%; height: 400px; border-radius: 20px; object-fit: cover; box-shadow: 0 20px 50px rgba(0,0,0,0.12); }
        .dest-label { font-size: 0.78rem; font-weight: 700; letter-spacing: 2px; color: var(--primary); text-transform: uppercase; margin-bottom: 16px; }
        .dest-heading { font-size: 2.4rem; font-weight: 800; line-height: 1.25; font-family: Georgia, serif; margin-bottom: 20px; }
        .dest-text { font-size: 0.97rem; color: var(--text-muted); line-height: 1.75; margin-bottom: 28px; }
        .dest-pills { display: flex; flex-wrap: wrap; gap: 10px; }
        .dest-pill { padding: 8px 18px; border: 1.5px solid var(--border); border-radius: 50px; font-size: 0.88rem; font-weight: 600; color: var(--text-dark); background: #fff; cursor: pointer; transition: all .2s; text-decoration: none; }
        .dest-pill:hover { border-color: var(--primary); color: var(--primary); }
        footer { background: var(--navy); color: #fff; padding: 60px 60px 30px; }
        .footer-grid { display: grid; grid-template-columns: 1.4fr 1fr 1fr 1fr; gap: 48px; margin-bottom: 48px; }
        .footer-brand-name { font-size: 1.6rem; font-weight: 800; margin-bottom: 12px; }
        .footer-brand-desc { color: rgba(255,255,255,0.6); font-size: 0.9rem; line-height: 1.7; margin-bottom: 20px; }
        .footer-col h4 { font-size: 0.78rem; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: rgba(255,255,255,0.5); margin-bottom: 20px; }
        .footer-col ul { list-style: none; }
        .footer-col ul li { margin-bottom: 10px; }
        .footer-col ul li a { color: rgba(255,255,255,0.75); text-decoration: none; font-size: 0.9rem; transition: color .2s; }
        .footer-col ul li a:hover { color: #fff; }
        .footer-contact-item { display: flex; align-items: center; gap: 10px; color: rgba(255,255,255,0.75); font-size: 0.9rem; margin-bottom: 10px; }
        .footer-divider { border: none; border-top: 1px solid rgba(255,255,255,0.1); margin-bottom: 24px; }
        .footer-bottom { display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem; color: rgba(255,255,255,0.4); }
        .footer-watermark { text-align: center; font-size: 8vw; font-weight: 800; color: rgba(255,255,255,0.04); line-height: 1; margin-bottom: -10px; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<section style="padding-top:70px;">
    <div class="about-hero">
        <div>
            <p class="about-label">À propos de Tarkina</p>
            <h1 class="about-heading">Une passerelle vers la Tunisie vraie, portée par ses habitants.</h1>
            <p class="about-desc">Tarkina est une plateforme de voyage local qui connecte les visiteurs avec des hôtes, guides, artisans et familles dans les régions tunisiennes moins connues.</p>
            <div class="about-buttons">
                <a href="explorer.php" class="btn-primary-solid">Explorer les expériences →</a>
                <a href="contact.php" class="btn-outline-dark">Nous contacter</a>
            </div>
        </div>
        <div class="about-images">
            <img class="about-img-main" src="https://images.unsplash.com/photo-1561625116-5f8675632053?w=800&fit=crop" alt="Tunisie authentique">
            <img class="about-img-accent" src="https://images.unsplash.com/photo-1509316785289-025f5b846b35?w=800&fit=crop" alt="Sahara tunisien">
        </div>
    </div>
</section>
<hr class="section-divider">
<section>
    <div class="mission-section">
        <div class="mission-grid">
            <div>
                <p class="mission-label">Notre Mission</p>
                <h2 class="mission-heading">Faire voyager autrement.</h2>
            </div>
            <div>
                <p class="mission-text">Beaucoup de trésors tunisiens restent invisibles aux voyageurs : villages perchés, oasis, maisons traditionnelles, ateliers familiaux et tables locales. Tarkina existe pour rendre ces expériences accessibles sans les transformer en tourisme standardisé.</p>
                <p class="mission-text">Notre rôle est simple : donner une vitrine claire aux habitants, faciliter la réservation, et aider chaque visiteur à vivre une rencontre sincère avec le territoire.</p>
            </div>
        </div>
        <div class="values-grid">
            <div class="value-card">
                <div class="value-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                </div>
                <p class="value-title">Valoriser les habitants</p>
                <p class="value-desc">Tarkina met en avant les familles, guides, artisans et cuisinières qui font vivre chaque région.</p>
            </div>
            <div class="value-card">
                <div class="value-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                </div>
                <p class="value-title">Révéler les lieux oubliés</p>
                <p class="value-desc">Nous aidons les voyageurs à sortir des circuits classiques pour découvrir des villages et paysages authentiques.</p>
            </div>
            <div class="value-card">
                <div class="value-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                </div>
                <p class="value-title">Créer un échange juste</p>
                <p class="value-desc">Les expériences sont pensées pour rémunérer directement les locaux et préserver leur savoir-faire.</p>
            </div>
            <div class="value-card">
                <div class="value-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                </div>
                <p class="value-title">Voyager avec respect</p>
                <p class="value-desc">Chaque séjour encourage une découverte lente, responsable et attentive aux cultures locales.</p>
            </div>
        </div>
    </div>
</section>
<section class="destinations-section">
    <div class="destinations-inner">
        <img class="destinations-img" src="https://images.unsplash.com/photo-1509316785289-025f5b846b35?w=800&fit=crop" alt="Désert tunisien">
        <div>
            <p class="dest-label">Nos destinations</p>
            <h2 class="dest-heading">Des régions choisies pour leur âme.</h2>
            <p class="dest-text">De Chenini à Djerba, en passant par Tozeur, Takrouna ou Sidi Bou Saïd, chaque destination met en lumière un patrimoine vivant : architecture, cuisine, artisanat, récits et hospitalité.</p>
            <div class="dest-pills">
                <a href="explorer.php" class="dest-pill">Chenini</a>
                <a href="explorer.php" class="dest-pill">Takrouna</a>
                <a href="explorer.php" class="dest-pill">Ksar Ouled Soltane</a>
                <a href="explorer.php" class="dest-pill">Sidi Bou Saïd</a>
                <a href="explorer.php" class="dest-pill">Tozeur</a>
                <a href="explorer.php" class="dest-pill">Djerba</a>
            </div>
        </div>
    </div>
</section>
<?php include 'footer.php'; ?>
</body>
</html>

