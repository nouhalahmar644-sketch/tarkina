<?php
session_start();

require 'db.php';

// Fetch regions
$regions = [];
if(isset($conn) && $conn) {
    $res = mysqli_query($conn, "SELECT r.*, 
        (SELECT COUNT(*) FROM hebergement WHERE region_id = r.id AND statut IN ('actif','publié')) +
        (SELECT COUNT(*) FROM repas WHERE region_id = r.id AND statut IN ('actif','publié')) +
        (SELECT COUNT(*) FROM guide WHERE region_id = r.id AND statut IN ('actif','publié')) +
        (SELECT COUNT(*) FROM evenement WHERE region_id = r.id AND statut IN ('actif','publié')) AS nb_services
        FROM region r ORDER BY r.nom ASC LIMIT 3");
    if($res) {
        while($row = mysqli_fetch_assoc($res)) {
            $regions[] = $row;
        }
    }
}

// Fetch guides for hosts section
$guides = [];
if(isset($conn) && $conn) {
    $res2 = mysqli_query($conn, "SELECT g.*, r.nom as region_nom FROM guide g LEFT JOIN region r ON g.region_id = r.id WHERE g.statut IN ('actif','publié') LIMIT 6");
    if($res2) {
        while($row = mysqli_fetch_assoc($res2)) {
            $guides[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tarkina — Voyagez autrement en Tunisie</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>
    <style>
        :root { --primary:#E05A2B; --navy:#1B3A4B; --light-bg:#FAF8F5; --text-dark:#1a1a1a; --text-muted:#6b7280; --border:#e5e7eb; }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Segoe UI',system-ui,sans-serif; background:#fff; color:var(--text-dark); }

        /* ── NAVBAR ── */
        .navbar { position:absolute; top:0; left:0; right:0; z-index:100; background:transparent !important; border-bottom:none !important; padding:0 60px; height:70px; display:flex; align-items:center; justify-content:space-between; transition:background .3s,box-shadow .3s; }
        .navbar .nav-logo-text, .navbar .nav-links a { color:#fff !important; }
        .navbar .nav-links a:hover { opacity:0.8; }
        .navbar.scrolled { position:fixed; background:#fff !important; box-shadow:0 2px 20px rgba(0,0,0,.08); }
        .navbar.scrolled .nav-logo-text, .navbar.scrolled .nav-links a { color:var(--text-dark) !important; }
        .navbar.scrolled .nav-links a:hover { color:var(--primary) !important; opacity:1; }
        .navbar.scrolled .btn-nav-outline { border-color:var(--navy); color:var(--navy); }
        .nav-logo img { height:36px; }
        .nav-logo-text { font-size:1.4rem; font-weight:800; text-decoration:none; letter-spacing:-1px; }
        .nav-links { display:flex; gap:36px; list-style:none; }
        .nav-links a { text-decoration:none; font-size:.95rem; font-weight:500; transition:color .2s; }
        .nav-auth { display:flex; gap:12px; align-items:center; }
        .btn-nav-outline { padding:8px 20px; border:1.5px solid rgba(255,255,255,.8); border-radius:50px; color:#fff; text-decoration:none; font-size:.9rem; font-weight:600; transition:all .2s; }
        .btn-nav-outline:hover { background:#fff; color:var(--navy); }
        .btn-nav-primary { padding:8px 20px; background:var(--primary); border-radius:50px; color:#fff; text-decoration:none; font-size:.9rem; font-weight:600; transition:background .2s; }
        .btn-nav-primary:hover { background:#c44d22; }

        /* ── HERO ── */
        .hero { min-height:100vh; background-image: url('https://images.unsplash.com/photo-1548013146-72479768bada?w=1600&q=80'); background-size: cover; background-position: center; position:relative; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:80px 40px 0 40px; }
        .hero > *:not(.hero-overlay) { position:relative; z-index:1; }
        .hero-title { font-size:clamp(2.5rem,6vw,5rem); font-weight:900; color:#fff; line-height:1.05; letter-spacing:-1px; margin-bottom:40px; font-family:Georgia,'Times New Roman',serif; text-shadow:0 2px 20px rgba(0,0,0,.3); }
        .hero-search { background:#fff; border-radius:50px; display:flex; align-items:center; padding:6px 6px 6px 20px; width:100%; max-width:600px; box-shadow:0 8px 30px rgba(0,0,0,.2); margin-bottom:24px; }
        .hero-search input { flex:1; border:none; outline:none; font-size:1rem; color:var(--text-dark); background:transparent; }
        .hero-search input::placeholder { color:#9ca3af; }
        .btn-search { padding:0; width:46px; height:46px; flex-shrink:0; background:var(--primary); color:#fff; border:none; border-radius:50%; cursor:pointer; transition:background .2s; display:flex; align-items:center; justify-content:center; }
        .btn-search svg { width:20px; height:20px; }
        .btn-search:hover { background:#c44d22; }
        .hero-pills { display:flex; gap:10px; flex-wrap:wrap; justify-content:center; margin-bottom:28px; }
        .hero-pill { padding:8px 18px; border:1.5px solid rgba(255,255,255,.6); border-radius:50px; color:#fff; text-decoration:none; font-size:.85rem; font-weight:600; display:flex; align-items:center; gap:6px; transition:all .2s; backdrop-filter:blur(4px); background:rgba(255,255,255,.1); }
        .hero-pill:hover, .hero-pill.active { background:#fff; color:var(--navy); border-color:#fff; }
        .booking-bar { background:rgba(255,255,255,.97); border-radius:16px; display:grid; grid-template-columns:2fr 1.2fr 1.2fr 1fr auto; align-items:center; width:100%; max-width:860px; overflow:hidden; box-shadow:0 8px 30px rgba(0,0,0,.2); }
        .booking-field { padding:16px 20px; border-right:1px solid var(--border); }
        .booking-field label { display:block; font-size:.68rem; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; color:#9ca3af; margin-bottom:5px; }
        .booking-field input { width:100%; border:none; outline:none; font-size:.95rem; color:var(--text-dark); background:transparent; }
        .booking-btn { padding:0 24px; background:var(--primary); height:100%; display:flex; align-items:center; justify-content:center; border:none; cursor:pointer; transition:background .2s; }
        .booking-btn:hover { background:#c44d22; }
        .booking-btn svg { width:22px; height:22px; stroke:#fff; }

        /* ── TRUST BAR ── */
        .trust-bar { background:#fff; border-top:1px solid var(--border); border-bottom:1px solid var(--border); padding:28px 60px; }
        .trust-bar-inner { max-width:1200px; margin:0 auto; display:grid; grid-template-columns:repeat(4,1fr); gap:32px; }
        .trust-item { display:flex; align-items:flex-start; gap:14px; }
        .trust-item svg { width:26px; height:26px; stroke:var(--primary); flex-shrink:0; margin-top:2px; fill:none; stroke-width:1.8; }
        .trust-item strong { display:block; font-size:.92rem; font-weight:700; margin-bottom:2px; }
        .trust-item span { font-size:.82rem; color:var(--text-muted); }

        /* ── SECTION COMMONS ── */
        .section-label { font-size:.75rem; font-weight:700; letter-spacing:2px; color:var(--primary); text-transform:uppercase; margin-bottom:12px; }
        .section-heading { font-size:2.2rem; font-weight:800; font-family:Georgia,serif; margin-bottom:8px; }
        .section-sub { color:var(--text-muted); font-size:.97rem; margin-bottom:40px; }

        /* ── REGIONS ── */
        .regions-section { padding:80px 60px; max-width:1200px; margin:0 auto; }
        .regions-header { display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:40px; }
        .link-all { color:var(--primary); text-decoration:none; font-weight:600; font-size:.92rem; }
        .link-all:hover { text-decoration:underline; }
        .regions-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:24px; }
        .region-card { border-radius:16px; overflow:hidden; text-decoration:none; color:inherit; display:block; box-shadow:0 4px 20px rgba(0,0,0,.08); transition:transform .2s,box-shadow .2s; position:relative; }
        .region-card:hover { transform:translateY(-5px); box-shadow:0 12px 40px rgba(0,0,0,.15); }
        .region-card img { width:100%; height:240px; object-fit:cover; display:block; }
        .region-card-placeholder { width:100%; height:240px; background:#e5e7eb; display:flex; align-items:center; justify-content:center; color:#9ca3af; font-size:.9rem; }
        .region-card-body { padding:20px; background:#fff; }
        .region-card-badge { position:absolute; top:16px; left:16px; background:rgba(0,0,0,.55); color:#fff; padding:4px 12px; border-radius:50px; font-size:.75rem; font-weight:600; backdrop-filter:blur(4px); }
        .region-card-name { font-size:1.1rem; font-weight:700; margin-bottom:6px; }
        .region-card-desc { font-size:.85rem; color:var(--text-muted); line-height:1.5; margin-bottom:10px; }
        .region-card-meta { display:flex; align-items:center; gap:12px; font-size:.8rem; color:var(--text-muted); }

        /* ── FORFAITS ── */
        .forfaits-section { padding:80px 60px; background:var(--light-bg); }
        .forfaits-inner { max-width:1200px; margin:0 auto; }
        .forfaits-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:24px; margin-top:40px; }
        .forfait-card { background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,.07); transition:transform .2s,box-shadow .2s; }
        .forfait-card:hover { transform:translateY(-5px); box-shadow:0 16px 40px rgba(0,0,0,.12); }
        .forfait-img-wrap { position:relative; }
        .forfait-img-wrap img { width:100%; height:220px; object-fit:cover; display:block; }
        .forfait-price-badge { position:absolute; top:16px; right:16px; background:#fff; border-radius:50px; padding:6px 14px; font-size:.85rem; font-weight:700; color:var(--text-dark); box-shadow:0 2px 10px rgba(0,0,0,.15); }
        .forfait-body { padding:24px; }
        .forfait-meta { font-size:.8rem; color:var(--text-muted); margin-bottom:8px; }
        .forfait-title { font-size:1.2rem; font-weight:700; margin-bottom:12px; font-family:Georgia,serif; }
        .forfait-tags { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:16px; }
        .forfait-tag { padding:4px 12px; border-radius:50px; font-size:.75rem; font-weight:600; }
        .tag-desert { background:#FEF3C7; color:#92400E; }
        .tag-famille { background:#DBEAFE; color:#1E40AF; }
        .tag-aventure { background:#D1FAE5; color:#065F46; }
        .tag-culture { background:#EDE9FE; color:#5B21B6; }
        .tag-histoire { background:#FCE7F3; color:#9D174D; }
        .tag-solo { background:#FEE2E2; color:#991B1B; }
        .tag-plage { background:#E0F2FE; color:#0C4A6E; }
        .tag-artisanat { background:#FDF2F8; color:#831843; }
        .tag-couple { background:#FFF7ED; color:#9A3412; }
        .forfait-includes { list-style:none; margin-bottom:20px; }
        .forfait-includes li { display:flex; align-items:center; gap:8px; font-size:.88rem; color:var(--text-muted); margin-bottom:8px; }
        .forfait-includes li::before { content:'✓'; color:#059669; font-weight:700; flex-shrink:0; }
        .btn-forfait { display:block; width:100%; padding:13px; background:var(--primary); color:#fff; border:none; border-radius:10px; font-size:.95rem; font-weight:700; cursor:pointer; text-align:center; text-decoration:none; transition:background .2s; }
        .btn-forfait:hover { background:#c44d22; }

        /* ── GALLERY ── */
        .gallery-section { padding:80px 60px; background:#fff; }
        .gallery-section-inner { max-width:1200px; margin:0 auto; }
        .gallery-title { text-align:center; font-size:2rem; font-weight:800; letter-spacing:2px; margin-bottom:8px; text-transform:uppercase; }
        .gallery-underline { width:60px; height:3px; background:var(--primary); margin:0 auto 40px; }
        .gallery-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; }
        .gallery-grid img { width:100%; height:280px; object-fit:cover; border-radius:14px; transition:transform .3s; cursor:pointer; }
        .gallery-grid img:hover { transform:scale(1.03); }
        .gallery-cta { text-align:center; margin-top:32px; }
        .btn-gallery { display:inline-block; padding:12px 32px; border:2px solid var(--primary); color:var(--primary); border-radius:50px; text-decoration:none; font-weight:700; font-size:.95rem; transition:all .2s; }
        .btn-gallery:hover { background:var(--primary); color:#fff; }

        /* ── EXPERIENCES ── */
        .experiences-section { padding:80px 60px; background:var(--light-bg); }
        .experiences-inner { max-width:1200px; margin:0 auto; }
        .exp-grid { display:grid; grid-template-columns:repeat(5,1fr); gap:16px; }
        .exp-card { background:#fff; border:1px solid var(--border); border-radius:16px; padding:24px 20px; text-decoration:none; color:inherit; display:flex; flex-direction:column; gap:10px; transition:all .2s; position:relative; }
        .exp-card:hover { border-color:var(--primary); box-shadow:0 8px 30px rgba(224,90,43,.1); transform:translateY(-3px); }
        .exp-icon { font-size:1.8rem; }
        .exp-adj { font-size:.72rem; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; color:var(--primary); }
        .exp-text strong { display:block; font-size:.95rem; font-weight:700; margin:2px 0; }
        .exp-text p { font-size:.8rem; color:var(--text-muted); line-height:1.5; }
        .exp-arrow { position:absolute; bottom:16px; right:16px; font-size:1rem; color:var(--primary); font-weight:700; opacity:0; transition:opacity .2s; }
        .exp-card:hover .exp-arrow { opacity:1; }

        /* ── HOSTS ── */
        .hosts-section { padding:80px 60px; background:#fff; }
        .hosts-inner { max-width:1200px; margin:0 auto; }
        .hosts-header { display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:36px; }
        .hosts-grid { display:grid; grid-template-columns:repeat(6,1fr); gap:16px; }
        .host-card { text-decoration:none; color:inherit; text-align:center; }
        .host-photo { width:100%; aspect-ratio:1; border-radius:50%; overflow:hidden; margin-bottom:12px; border:3px solid var(--light-bg); transition:border-color .2s; }
        .host-card:hover .host-photo { border-color:var(--primary); }
        .host-photo img { width:100%; height:100%; object-fit:cover; }
        .host-info strong { display:block; font-size:.88rem; font-weight:700; }
        .host-info span { font-size:.78rem; color:var(--text-muted); }
        .host-price { color:var(--primary) !important; font-weight:600 !important; }
        .btn-see-all { color:var(--primary); text-decoration:none; font-weight:700; font-size:.92rem; border:1.5px solid var(--primary); padding:8px 20px; border-radius:50px; transition:all .2s; }
        .btn-see-all:hover { background:var(--primary); color:#fff; }

        /* ── POURQUOI TARKINA ── */
        .pourquoi-section { padding:80px 60px; background:var(--light-bg); }
        .pourquoi-inner { max-width:1200px; margin:0 auto; text-align:center; }
        .pourquoi-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:24px; margin-top:48px; }
        .pourquoi-card { background:#fff; border-radius:16px; padding:32px 24px; text-align:center; border:1px solid var(--border); transition:box-shadow .2s,transform .2s; }
        .pourquoi-card:hover { box-shadow:0 8px 30px rgba(0,0,0,.08); transform:translateY(-3px); }
        .pourquoi-icon { width:56px; height:56px; border-radius:50%; background:#FEF0EA; display:flex; align-items:center; justify-content:center; margin:0 auto 16px; }
        .pourquoi-icon svg { width:24px; height:24px; stroke:var(--primary); fill:none; stroke-width:1.8; }
        .pourquoi-card h3 { font-size:1rem; font-weight:700; margin-bottom:10px; }
        .pourquoi-card p { font-size:.85rem; color:var(--text-muted); line-height:1.65; }

        /* ── TESTIMONIALS ── */
        .temoignages-section { background:var(--navy); padding:80px 60px; text-align:center; }
        .temoignages-inner { max-width:1100px; margin:0 auto; }
        .temo-label { font-size:.75rem; font-weight:700; letter-spacing:2.5px; color:var(--primary); text-transform:uppercase; margin-bottom:12px; }
        .temo-heading { font-size:2rem; font-weight:800; color:#fff; margin-bottom:48px; letter-spacing:1px; }
        .temo-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:24px; }
        .temo-card { position:relative; background:rgba(255,255,255,.07); border:1px solid rgba(255,255,255,.12); border-radius:16px; padding:28px 24px; text-align:left; cursor:pointer; transition:transform .2s,border-color .2s; }
        .temo-card:hover { transform:translateY(-4px); border-color:rgba(224,90,43,.5); }
        .temo-avatar { width:52px; height:52px; border-radius:50%; overflow:hidden; margin-bottom:16px; border:2px solid rgba(255,255,255,.2); }
        .temo-avatar img { width:100%; height:100%; object-fit:cover; }
        .temo-quote { color:rgba(255,255,255,.85); font-size:.92rem; line-height:1.6; font-style:italic; margin-bottom:14px; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden; }
        .temo-name { color:#fff; font-weight:700; font-size:.9rem; }
        .temo-city { color:rgba(255,255,255,.5); font-size:.8rem; margin-bottom:10px; }
        .temo-stars { color:var(--primary); font-size:1rem; letter-spacing:2px; }
        .temo-popup { position:absolute; bottom:calc(100% + 12px); left:50%; transform:translateX(-50%) translateY(8px); width:320px; background:#fff; border-radius:16px; box-shadow:0 20px 60px rgba(0,0,0,.25); padding:24px; z-index:100; opacity:0; pointer-events:none; transition:opacity .25s ease,transform .25s ease; display:flex; gap:16px; align-items:flex-start; text-align:left; }
        .temo-popup::after { content:''; position:absolute; top:100%; left:50%; transform:translateX(-50%); border:8px solid transparent; border-top-color:#fff; }
        .temo-card:hover .temo-popup { opacity:1; pointer-events:auto; transform:translateX(-50%) translateY(0); }
        .temo-popup img { width:56px; height:56px; border-radius:50%; object-fit:cover; flex-shrink:0; }
        .temo-popup-content { flex:1; }
        .temo-popup-content .temo-stars { margin-bottom:8px; font-size:.9rem; }
        .temo-popup-content p { font-size:.85rem; color:#374151; line-height:1.6; margin-bottom:10px; font-style:italic; }
        .temo-popup-content strong { display:block; font-size:.88rem; color:#1a1a1a; }
        .temo-popup-content span { font-size:.78rem; color:#6b7280; display:block; }
        .temo-tag { margin-top:6px !important; color:var(--primary) !important; font-weight:600 !important; }

        /* ── NEWSLETTER ── */
        .newsletter-section { position:relative; min-height:500px; background:linear-gradient(rgba(0,0,0,.55),rgba(0,0,0,.6)), url('https://images.unsplash.com/photo-1509316785289-025f5b846b35?w=1400&fit=crop') center/cover no-repeat; display:flex; align-items:center; justify-content:center; text-align:center; padding:80px 60px; }
        .newsletter-inner { max-width:600px; }
        .newsletter-avatars { display:flex; justify-content:center; margin-bottom:16px; }
        .newsletter-avatars img { width:40px; height:40px; border-radius:50%; border:2px solid #fff; margin-left:-8px; object-fit:cover; }
        .newsletter-avatars img:first-child { margin-left:0; }
        .newsletter-badge { color:rgba(255,255,255,.8); font-size:.85rem; margin-bottom:20px; }
        .newsletter-title { font-size:2.2rem; font-weight:800; color:#fff; margin-bottom:16px; line-height:1.2; font-family:Georgia,serif; }
        .newsletter-sub { color:rgba(255,255,255,.75); font-size:.97rem; margin-bottom:32px; line-height:1.6; }
        .newsletter-form { display:flex; background:#fff; border-radius:50px; overflow:hidden; box-shadow:0 8px 30px rgba(0,0,0,.2); }
        .newsletter-form input { flex:1; border:none; outline:none; padding:14px 24px; font-size:.97rem; color:var(--text-dark); }
        .newsletter-form button { padding:14px 28px; background:var(--primary); color:#fff; border:none; font-size:.95rem; font-weight:700; cursor:pointer; transition:background .2s; white-space:nowrap; }
        .newsletter-form button:hover { background:#c44d22; }

        /* ── CTA HOST ── */
        .cta-host-section { background:var(--primary); padding:80px 60px; text-align:center; }
        .cta-host-section h2 { font-size:2rem; font-weight:800; color:#fff; margin-bottom:16px; font-family:Georgia,serif; font-style:italic; }
        .cta-host-section p { color:rgba(255,255,255,.85); font-size:.97rem; margin-bottom:32px; max-width:540px; margin-left:auto; margin-right:auto; line-height:1.7; }
        .cta-host-btns { display:flex; gap:16px; justify-content:center; }
        .btn-cta-white { padding:13px 28px; background:#fff; color:var(--primary); border-radius:50px; text-decoration:none; font-weight:700; font-size:.95rem; transition:all .2s; }
        .btn-cta-white:hover { background:rgba(255,255,255,.9); }
        .btn-cta-outline-white { padding:13px 28px; border:2px solid #fff; color:#fff; border-radius:50px; text-decoration:none; font-weight:700; font-size:.95rem; transition:all .2s; }
        .btn-cta-outline-white:hover { background:#fff; color:var(--primary); }

        /* ── FOOTER ── */
        footer { background:var(--navy); color:#fff; padding:60px 60px 30px; }
        .footer-grid { display:grid; grid-template-columns:1.4fr 1fr 1fr 1fr; gap:48px; margin-bottom:48px; }
        .footer-brand-name { font-size:1.6rem; font-weight:800; margin-bottom:12px; }
        .footer-brand-desc { color:rgba(255,255,255,.6); font-size:.9rem; line-height:1.7; margin-bottom:20px; }
        .footer-socials { display:flex; gap:12px; }
        .footer-socials a { width:36px; height:36px; border-radius:50%; background:rgba(255,255,255,.1); display:flex; align-items:center; justify-content:center; color:#fff; text-decoration:none; font-size:.85rem; transition:background .2s; }
        .footer-socials a:hover { background:var(--primary); }
        .footer-col h4 { font-size:.78rem; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; color:rgba(255,255,255,.5); margin-bottom:20px; }
        .footer-col ul { list-style:none; }
        .footer-col ul li { margin-bottom:10px; }
        .footer-col ul li a { color:rgba(255,255,255,.75); text-decoration:none; font-size:.9rem; transition:color .2s; }
        .footer-col ul li a:hover { color:#fff; }
        .footer-contact-item { display:flex; align-items:center; gap:10px; color:rgba(255,255,255,.75); font-size:.9rem; margin-bottom:10px; }
        .footer-watermark { text-align:center; font-size:8vw; font-weight:800; color:rgba(255,255,255,.04); line-height:1; margin-bottom:-10px; pointer-events:none; }
        .footer-divider { border:none; border-top:1px solid rgba(255,255,255,.1); margin-bottom:24px; }
        .footer-bottom { display:flex; justify-content:space-between; font-size:.85rem; color:rgba(255,255,255,.4); }
        .footer-bottom a { color:rgba(255,255,255,.4); text-decoration:none; }
    </style>
</head>
<body>

<!-- NAVBAR -->
<?php $navTransparent = true; include 'navbar.php'; ?>

<!-- HERO -->
<section class="hero">
    <div class="hero-overlay" style="position:absolute;inset:0;background:linear-gradient(135deg,rgba(27,58,75,0.75) 0%,rgba(224,90,43,0.45) 100%);z-index:0;"></div>
    <h1 class="hero-title">VIVEZ LA TUNISIE <span style="color:var(--primary)">AUTREMENT</span>.<br>AU PLUS PRÈS DE SES HABITANTS.</h1>

    <form class="hero-search" action="search.php" method="GET">
        <input type="text" name="q" placeholder="Rechercher une ville, une expérience...">
        <button type="submit" class="btn-search" aria-label="Rechercher" title="Rechercher">
            <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35" stroke-linecap="round"/></svg>
        </button>
    </form>

    <form class="booking-bar" action="search.php" method="GET">
        <div class="booking-field sf-field">
            <label class="sf-label">Destination</label>
            <select name="destination" style="border:none;outline:none;background:transparent;font-size:14px;color:#333;font-family:inherit;width:100%;cursor:pointer;">
                <option value="">Où allez-vous ?</option>
                <?php
                if (isset($conn) && $conn) {
                    $reg_q = mysqli_query($conn, "SELECT id, nom FROM region ORDER BY nom ASC");
                    if($reg_q) {
                        while ($reg_row = mysqli_fetch_assoc($reg_q)):
                ?>
                <option value="<?= $reg_row['id'] ?>"><?= htmlspecialchars($reg_row['nom'], ENT_QUOTES, 'UTF-8') ?></option>
                <?php 
                        endwhile;
                    } 
                }
                ?>
            </select>
        </div>
        <div class="booking-field sf-field">
            <label>Arrivée</label>
            <input type="text" name="date_debut" id="date_debut" readonly>
        </div>
        <div class="booking-field sf-field">
            <label>Départ</label>
            <input type="text" name="date_fin" id="date_fin" readonly>
        </div>
        <div class="booking-field sf-field" style="border-right:none">
            <label>Voyageurs</label>
            <input type="number" name="personnes" id="personnes" value="1" min="1" max="20" style="width:60px;border:none;outline:none;background:transparent;font-size:14px;font-family:inherit;">
        </div>
        <button type="submit" class="booking-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35" stroke-linecap="round"/></svg>
        </button>
    </form>

    <div style="display:flex;gap:16px;flex-wrap:wrap;justify-content:center;margin-top:32px;">
        <a href="explorer.php" style="display:inline-block;padding:14px 32px;background:#E05A2B;color:#fff;border-radius:50px;text-decoration:none;font-weight:700;font-size:1rem;box-shadow:0 6px 24px rgba(224,90,43,.4);transition:background .2s;" onmouseover="this.style.background='#c44d22'" onmouseout="this.style.background='#E05A2B'">Explorer les régions</a>
        <a href="blogs.php" style="display:inline-block;padding:14px 32px;background:transparent;color:#fff;border:2px solid rgba(255,255,255,.85);border-radius:50px;text-decoration:none;font-weight:700;font-size:1rem;transition:all .2s;" onmouseover="this.style.background='#fff';this.style.color='#1B3A4B'" onmouseout="this.style.background='transparent';this.style.color='#fff'">Découvrir le blog</a>
    </div>
</section>

<!-- TRUST BAR -->
<section class="trust-bar">
    <div class="trust-bar-inner">
        <div class="trust-item">
            <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="#E05A2B" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div><strong>Annulation flexible</strong><span>Annulez jusqu'à 48h avant</span></div>
        </div>
        <div class="trust-item">
            <svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
            <div><strong>Hôtes vérifiés</strong><span>Chaque hôte est vérifié par notre équipe</span></div>
        </div>
        <div class="trust-item">
            <svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
            <div><strong>Paiement sécurisé</strong><span>Transactions protégées et cryptées</span></div>
        </div>
        <div class="trust-item">
            <svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
            <div><strong>Support 7j/7</strong><span>En français, arabe et anglais</span></div>
        </div>
    </div>
</section>

<div class="hero-pills" style="margin-top:40px;">
    <a href="search.php?type=hebergement" class="hero-pill">Hébergement</a>
    <a href="search.php?type=repas" class="hero-pill">Repas maison</a>
    <a href="search.php?type=guide" class="hero-pill">Guide local</a>
    <a href="search.php?type=artisanat" class="hero-pill">Produits artisanaux</a>
    <a href="search.php?type=evenement" class="hero-pill">Événements</a>
</div>

<!-- REGIONS -->
<section style="padding:80px 0;">
    <div class="regions-section" style="padding:0 60px;">
        <div class="regions-header">
            <div>
                <p class="section-label">Destinations · Tunisie</p>
                <h2 class="section-heading">Découvrez nos <span style="color:var(--primary)">régions</span></h2>
            </div>
            <a href="explorer.php" class="link-all">Voir toutes les régions →</a>
        </div>
        <div class="regions-grid">
            <?php if(count($regions) > 0): ?>
                <?php foreach($regions as $region): ?>
                <a href="region.php?id=<?= $region['id'] ?>" class="region-card animate-up">
                    <?php
                    $photo = '';
                    if (!empty($region['photo_principale'])) {
                        $p = $region['photo_principale'];
                        if (strpos($p, 'http') === 0) {
                            $photo = $p;
                        } elseif (file_exists('assets/img/regions/' . $p)) {
                            $photo = 'assets/img/regions/' . $p;
                        } elseif (file_exists('assets/img/' . $p)) {
                            $photo = 'assets/img/' . $p;
                        } elseif (file_exists('uploads/' . $p)) {
                            $photo = 'uploads/' . $p;
                        }
                    }
                    if (empty($photo)) {
                        $photo = 'https://images.unsplash.com/photo-1539635278303-d4002c07eae3?w=600&q=80';
                    }
                    ?>
                    <img loading="lazy" src="<?= $photo ?>" alt="<?= htmlspecialchars($region['nom'], ENT_QUOTES, 'UTF-8') ?>">
                    <div class="region-card-badge"><?= $region['nb_services'] ?> service<?= $region['nb_services'] != 1 ? 's' : '' ?></div>
                    <div class="region-card-body">
                        <p class="region-card-name"><?= htmlspecialchars($region['nom']) ?></p>
                        <p class="region-card-desc"><?= htmlspecialchars(mb_substr($region['description'] ?? '', 0, 100)) ?>...</p>
                        <div class="region-card-meta">
                            <?php if(!empty($region['meilleure_saison'])): ?>
                            <span>☀️ <?= htmlspecialchars($region['meilleure_saison']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color:var(--text-muted);grid-column:1/-1;text-align:center;">Aucune région disponible pour le moment.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- FORFAITS POPULAIRES -->
<section class="forfaits-section">
    <div class="forfaits-inner">
        <div style="text-align:center;">
            <p class="section-label">Nos Packs</p>
            <h2 class="section-heading">Partez l'esprit <span style="color:var(--primary)">tranquille</span></h2>
            <p class="section-sub">Des forfaits complets, pensés pour un voyage sans souci</p>
        </div>
        <div class="forfaits-grid">

            <!-- Forfait 1 -->
            <div class="forfait-card animate-up">
                <div class="forfait-img-wrap">
                    <img loading="lazy" src="https://images.unsplash.com/photo-1509316785289-025f5b846b35?w=800&fit=crop" alt="Escapade Saharienne" onerror="this.src='assets/img/placeholder.jpg'">
                    <span class="forfait-price-badge">À partir de 350 DT</span>
                </div>
                <div class="forfait-body">
                    <p class="forfait-meta">Douz & Tozeur · 4 jours</p>
                    <h3 class="forfait-title">Escapade Saharienne</h3>
                    <div class="forfait-tags">
                        <span class="forfait-tag tag-desert">Désert</span>
                        <span class="forfait-tag tag-famille">Famille</span>
                        <span class="forfait-tag tag-aventure">Aventure</span>
                    </div>
                    <ul class="forfait-includes">
                        <li>Hébergement</li>
                        <li>Guide local</li>
                        <li>Repas traditionnels</li>
                        <li>Balade à dromadaire</li>
                    </ul>
                    <a href="search.php?destination=Douz" class="btn-forfait">Réserver ce forfait</a>
                </div>
            </div>

            <!-- Forfait 2 -->
            <div class="forfait-card animate-up">
                <div class="forfait-img-wrap">
                    <img loading="lazy" src="https://images.unsplash.com/photo-1561625116-5f8675632053?w=800&fit=crop" alt="Kairouanite Culturelle" onerror="this.src='assets/img/placeholder.jpg'">
                    <span class="forfait-price-badge">À partir de 220 DT</span>
                </div>
                <div class="forfait-body">
                    <p class="forfait-meta">Kairouan · 3 jours</p>
                    <h3 class="forfait-title">Kairouanite Culturelle</h3>
                    <div class="forfait-tags">
                        <span class="forfait-tag tag-culture">Culture</span>
                        <span class="forfait-tag tag-histoire">Histoire</span>
                        <span class="forfait-tag tag-solo">Solo</span>
                    </div>
                    <ul class="forfait-includes">
                        <li>Hébergement</li>
                        <li>Guide certifié</li>
                        <li>Visites monuments</li>
                    </ul>
                    <a href="search.php?destination=Kairouan" class="btn-forfait">Réserver ce forfait</a>
                </div>
            </div>

            <!-- Forfait 3 -->
            <div class="forfait-card">
                <div class="forfait-img-wrap">
                    <img loading="lazy" src="https://images.unsplash.com/photo-1539650116574-75c0c6d73f6e?w=800&fit=crop" alt="Djerba Authentique" onerror="this.src='assets/img/placeholder.jpg'">
                    <span class="forfait-price-badge">À partir de 480 DT</span>
                </div>
                <div class="forfait-body">
                    <p class="forfait-meta">Djerba · 5 jours</p>
                    <h3 class="forfait-title">Djerba Authentique</h3>
                    <div class="forfait-tags">
                        <span class="forfait-tag tag-plage">Plage</span>
                        <span class="forfait-tag tag-artisanat">Artisanat</span>
                        <span class="forfait-tag tag-couple">Couple</span>
                    </div>
                    <ul class="forfait-includes">
                        <li>Hébergement bord de mer</li>
                        <li>Atelier poterie</li>
                        <li>Repas inclus</li>
                        <li>Transferts</li>
                    </ul>
                    <a href="search.php?destination=Djerba" class="btn-forfait">Réserver ce forfait</a>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- GALLERY -->
<section class="gallery-section">
    <div class="gallery-section-inner">
        <h2 class="gallery-title">La Tunisie en Images</h2>
        <div class="gallery-underline"></div>
        <div class="gallery-grid">
            <img loading="lazy" src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/73/Sidi_Bou_Said_-_TN.jpg/800px-Sidi_Bou_Said_-_TN.jpg" alt="Sidi Bou Said" onerror="this.src='https://images.unsplash.com/photo-1509316785289-025f5b846b35?w=800&fit=crop'">
            <img loading="lazy" src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/63/Kairouan_Great_Mosque.jpg/800px-Kairouan_Great_Mosque.jpg" alt="Kairouan" onerror="this.src='https://images.unsplash.com/photo-1561625116-5f8675632053?w=800&fit=crop'">
            <img loading="lazy" src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2d/Desert_-_Tunisia_%28Rades%29.jpg/800px-Desert_-_Tunisia_%28Rades%29.jpg" alt="Désert Tunisien" onerror="this.src='https://images.unsplash.com/photo-1509316785289-025f5b846b35?w=800&fit=crop'">
            <img loading="lazy" src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/6e/Houmt_Souk_port.jpg/800px-Houmt_Souk_port.jpg" alt="Djerba" onerror="this.src='https://images.unsplash.com/photo-1539650116574-75c0c6d73f6e?w=800&fit=crop'">
            <img loading="lazy" src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/6c/Matmata_cave_houses.jpg/800px-Matmata_cave_houses.jpg" alt="Matmata" onerror="this.src='https://images.unsplash.com/photo-1509316785289-025f5b846b35?w=800&fit=crop'">
            <img loading="lazy" src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5f/ElJem_amphitheatre.jpg/800px-ElJem_amphitheatre.jpg" alt="El Jem" onerror="this.src='https://images.unsplash.com/photo-1561625116-5f8675632053?w=800&fit=crop'">
        </div>
        <div class="gallery-cta">
            <a href="explorer.php" class="btn-gallery">Explorer toutes les régions →</a>
        </div>
    </div>
</section>

<!-- EXPERIENCES -->
<section class="experiences-section">
    <div class="experiences-inner">
        <p class="section-label">Expériences</p>
        <h2 class="section-heading">Trouvez votre <span style="color:var(--primary)">expérience idéale</span></h2>
        <p class="section-sub">Cinq façons de vivre la Tunisie autrement</p>
        <div class="exp-grid">
            <a href="search.php?type=hebergement" class="exp-card animate-up">
                <div class="exp-icon">🏠</div>
                <div class="exp-text">
                    <span class="exp-adj">Authentique</span>
                    <strong>Séjours</strong>
                    <p>Dormez chez l'habitant au cœur des régions</p>
                </div>
                <span class="exp-arrow">→</span>
            </a>
            <a href="search.php?type=repas" class="exp-card animate-up">
                <div class="exp-icon">🍽️</div>
                <div class="exp-text">
                    <span class="exp-adj">Savoureux</span>
                    <strong>Repas maison</strong>
                    <p>Tables familiales et cuisine traditionnelle</p>
                </div>
                <span class="exp-arrow">→</span>
            </a>
            <a href="search.php?type=guide" class="exp-card animate-up">
                <div class="exp-icon">🧭</div>
                <div class="exp-text">
                    <span class="exp-adj">Aventurier</span>
                    <strong>Guides locaux</strong>
                    <p>Explorez avec un habitant passionné</p>
                </div>
                <span class="exp-arrow">→</span>
            </a>
            <a href="search.php?type=artisanat" class="exp-card animate-up">
                <div class="exp-icon">🏺</div>
                <div class="exp-text">
                    <span class="exp-adj">Artisanal</span>
                    <strong>Artisanat local</strong>
                    <p>Pièces uniques faites à la main</p>
                </div>
                <span class="exp-arrow">→</span>
            </a>
            <a href="search.php?type=evenement" class="exp-card animate-up">
                <div class="exp-icon">🎉</div>
                <div class="exp-text">
                    <span class="exp-adj">Festif</span>
                    <strong>Événements</strong>
                    <p>Fêtes, festivals et célébrations locales</p>
                </div>
                <span class="exp-arrow">→</span>
            </a>
        </div>
    </div>
</section>

<!-- POURQUOI TARKINA -->
<section class="pourquoi-section">
    <div class="pourquoi-inner">
        <p class="section-label">Notre engagement</p>
        <h2 class="section-heading">Pourquoi <span style="color:var(--primary)">Tarkina</span> ?</h2>
        <p class="section-sub">Une plateforme pensée pour valoriser les territoires oubliés et leurs habitants.</p>
        <div class="pourquoi-grid">
            <div class="pourquoi-card">
                <div class="pourquoi-icon">
                    <svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                </div>
                <h3>Authentique</h3>
                <p>Des hôtes locaux qui partagent leur quotidien et leur culture, loin des services fabriqués.</p>
            </div>
            <div class="pourquoi-card">
                <div class="pourquoi-icon">
                    <svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                </div>
                <h3>Local</h3>
                <p>100% des services sont opérés par des habitants des régions tunisiennes méconnues.</p>
            </div>
            <div class="pourquoi-card">
                <div class="pourquoi-icon">
                    <svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                </div>
                <h3>Sécurisé</h3>
                <p>Hôtes vérifiés, paiement protégé, support 7j/7 en français, arabe et anglais.</p>
            </div>
            <div class="pourquoi-card">
                <div class="pourquoi-icon">
                    <svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3>Abordable</h3>
                <p>Des prix justes, fixés par les locaux, sans intermédiaires gourmands.</p>
            </div>
        </div>
    </div>
</section>

<!-- TESTIMONIALS -->
<section class="temoignages-section">
    <div class="temoignages-inner">
        <p class="temo-label">Témoignages</p>
        <h2 class="temo-heading">ILS ONT VOYAGÉ AVEC TARKINA</h2>
        <div class="temo-grid">

            <div class="temo-card">
                <div class="temo-card-inner">
                    <div class="temo-avatar"><img loading="lazy" src="https://randomuser.me/api/portraits/women/44.jpg" alt="Sarah B."></div>
                    <p class="temo-quote">"Un séjour inoubliable à Kessra. L'accueil était incroyable..."</p>
                    <p class="temo-name">Sarah B.</p>
                    <p class="temo-city">Voyageuse · Paris</p>
                    <div class="temo-stars">★★★★★</div>
                </div>
                <div class="temo-popup">
                    <img loading="lazy" src="https://randomuser.me/api/portraits/women/44.jpg" alt="Sarah B.">
                    <div class="temo-popup-content">
                        <div class="temo-stars">★★★★★</div>
                        <p>"Un séjour inoubliable à Kessra. L'accueil de la famille était incroyable et la nourriture... je n'ai jamais mangé un couscous aussi bon !"</p>
                        <strong>Sarah B.</strong>
                        <span>Voyageuse · Paris</span>
                        <span class="temo-tag">📍 Kessra, Siliana</span>
                    </div>
                </div>
            </div>

            <div class="temo-card">
                <div class="temo-card-inner">
                    <div class="temo-avatar"><img loading="lazy" src="https://randomuser.me/api/portraits/men/32.jpg" alt="Mehdi K."></div>
                    <p class="temo-quote">"Le guide local m'a emmené dans des endroits magiques..."</p>
                    <p class="temo-name">Mehdi K.</p>
                    <p class="temo-city">Voyageur · Lyon</p>
                    <div class="temo-stars">★★★★★</div>
                </div>
                <div class="temo-popup">
                    <img loading="lazy" src="https://randomuser.me/api/portraits/men/32.jpg" alt="Mehdi K.">
                    <div class="temo-popup-content">
                        <div class="temo-stars">★★★★★</div>
                        <p>"Le guide local m'a emmené dans des endroits que je n'aurais jamais trouvés seul. Une expérience humaine authentique, loin du tourisme de masse."</p>
                        <strong>Mehdi K.</strong>
                        <span>Voyageur · Lyon</span>
                        <span class="temo-tag">📍 Douz, Kébili</span>
                    </div>
                </div>
            </div>

            <div class="temo-card">
                <div class="temo-card-inner">
                    <div class="temo-avatar"><img loading="lazy" src="https://randomuser.me/api/portraits/women/68.jpg" alt="Amira T."></div>
                    <p class="temo-quote">"L'atelier poterie à Sejnane était une expérience magique..."</p>
                    <p class="temo-name">Amira T.</p>
                    <p class="temo-city">Voyageuse · Montréal</p>
                    <div class="temo-stars">★★★★★</div>
                </div>
                <div class="temo-popup">
                    <img loading="lazy" src="https://randomuser.me/api/portraits/women/68.jpg" alt="Amira T.">
                    <div class="temo-popup-content">
                        <div class="temo-stars">★★★★★</div>
                        <p>"L'atelier poterie à Sejnane était magique. La maîtresse artisane nous a transmis un savoir-faire ancestral avec une générosité rare."</p>
                        <strong>Amira T.</strong>
                        <span>Voyageuse · Montréal</span>
                        <span class="temo-tag">📍 Sejnane, Bizerte</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- NEWSLETTER -->
<section class="newsletter-section">
    <div class="newsletter-inner">
        <div class="newsletter-avatars">
            <img loading="lazy" src="https://randomuser.me/api/portraits/women/44.jpg" alt="">
            <img loading="lazy" src="https://randomuser.me/api/portraits/men/32.jpg" alt="">
            <img loading="lazy" src="https://randomuser.me/api/portraits/women/68.jpg" alt="">
        </div>
        <p class="newsletter-badge">Rejoignez 1 200+ voyageurs</p>
        <h2 class="newsletter-title">Prêt pour votre prochaine aventure ?</h2>
        <p class="newsletter-sub">Recevez nos meilleures offres, guides de destinations et conseils exclusifs directement dans votre boîte mail.</p>
        <form class="newsletter-form" onsubmit="return false;">
            <input type="email" placeholder="Votre adresse email">
            <button type="submit">S'abonner</button>
        </form>
    </div>
</section>

<!-- CTA HOST Removed -->

<!-- FOOTER -->
<?php include 'footer.php'; ?>


<script>
flatpickr.localize(flatpickr.l10ns.fr);

const departPicker = flatpickr("#date_fin", {
  dateFormat: "d M Y",
  minDate: "today",
  locale: "fr",
  disableMobile: true,
});

flatpickr("#date_debut", {
  dateFormat: "d M Y",
  minDate: "today",
  locale: "fr",
  disableMobile: true,
  onChange: function(selectedDates) {
    if (selectedDates[0]) departPicker.set('minDate', selectedDates[0]);
  }
});

// Open calendar on click anywhere in the field column
document.querySelectorAll('.sf-field').forEach(field => {
  field.addEventListener('click', function() {
    const input = this.querySelector('input[type="text"]');
    if (input && input._flatpickr) {
      input._flatpickr.open();
    }
  });
});

document.querySelector('form.booking-bar, .booking-bar form, .sf-btn')?.closest('form')?.addEventListener('submit', function(e) {
  const sel = this.querySelector('select[name="destination"]');
  if (sel && sel.value) {
    e.preventDefault();
    window.location.href = 'region.php?id=' + sel.value;
  }
});
</script>

</body>
</html>