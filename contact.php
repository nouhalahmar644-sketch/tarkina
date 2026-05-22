<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact – Tarkina</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Lato:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root { --cream:#f5f2ee; --dark:#1c1c2e; --navy:#1a2340; --orange:#e8642c; --muted:#6b6b6b; --border:#e0dbd4; --white:#fff; --radius:14px; --green:#2ecc71; }
    body { font-family:'Lato',sans-serif; background:var(--cream); color:var(--dark); font-size:15px; line-height:1.7; display:flex; flex-direction:column; min-height:100vh; }

    nav { background:var(--white); border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; padding:0 56px; height:60px; position:sticky; top:0; z-index:100; }
    .nav-logo { font-family:'Playfair Display',serif; font-size:22px; font-weight:800; color:var(--dark); text-decoration:none; }
    .nav-logo span { color:var(--orange); }
    .nav-links { display:flex; gap:32px; list-style:none; }
    .nav-links a { text-decoration:none; color:var(--dark); font-size:14px; font-weight:600; opacity:.7; transition:opacity .2s; }
    .nav-links a:hover { opacity:1; }
    .btn-nav { background:var(--orange); color:var(--white); border:none; border-radius:8px; padding:9px 22px; font-size:14px; font-weight:700; text-decoration:none; }

    .hero { background:var(--navy); color:var(--white); padding:60px 56px; text-align:center; }
    .hero h1 { font-family:'Playfair Display',serif; font-size:42px; margin-bottom:12px; }
    .hero p { font-size:16px; opacity:.7; max-width:500px; margin:0 auto; }

    .main { flex:1; display:grid; grid-template-columns:1fr 1fr; gap:48px; max-width:1100px; margin:0 auto; padding:56px; width:100%; }

    .contact-info h2 { font-family:'Playfair Display',serif; font-size:28px; margin-bottom:20px; }
    .contact-info p { color:#555; margin-bottom:24px; }
    .info-item { display:flex; align-items:flex-start; gap:16px; margin-bottom:24px; }
    .info-icon { width:48px; height:48px; background:rgba(232,100,44,.1); border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:20px; }
    .info-label { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); margin-bottom:4px; }
    .info-value { font-weight:600; color:var(--dark); }
    .info-value a { color:var(--orange); text-decoration:none; }
    .info-value a:hover { text-decoration:underline; }

    .social-links { display:flex; gap:12px; margin-top:32px; }
    .social-link { width:44px; height:44px; border-radius:50%; border:1.5px solid var(--border); display:flex; align-items:center; justify-content:center; text-decoration:none; color:var(--dark); font-size:18px; transition:all .2s; }
    .social-link:hover { border-color:var(--orange); color:var(--orange); background:rgba(232,100,44,.05); }

    .contact-form { background:var(--white); border:1px solid var(--border); border-radius:var(--radius); padding:40px; }
    .contact-form h3 { font-family:'Playfair Display',serif; font-size:22px; margin-bottom:24px; }

    .form-group { margin-bottom:20px; }
    .form-label { display:block; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--dark); margin-bottom:8px; }
    .form-input { width:100%; padding:13px 16px; border:1.5px solid var(--border); border-radius:10px; font-family:'Lato',sans-serif; font-size:14px; color:var(--dark); background:var(--cream); outline:none; transition:all .2s; }
    .form-input:focus { border-color:var(--orange); background:var(--white); box-shadow:0 0 0 4px rgba(232,100,44,.08); }
    textarea.form-input { resize:vertical; min-height:120px; }

    .btn-submit { width:100%; padding:15px; background:var(--orange); color:var(--white); border:none; border-radius:10px; font-family:'Lato',sans-serif; font-size:15px; font-weight:700; cursor:pointer; transition:all .25s; }
    .btn-submit:hover { background:#d45625; transform:translateY(-1px); box-shadow:0 8px 20px rgba(232,100,44,.25); }

    .success-msg { background:rgba(46,204,113,.1); color:var(--green); padding:16px; border-radius:10px; margin-bottom:20px; font-weight:600; display:none; }

    footer { background:var(--navy); color:var(--white); padding:48px 56px; text-align:center; }
    footer p { opacity:.6; font-size:14px; }

    @media(max-width:768px) {
      nav { padding:0 20px; }
      .hero { padding:40px 20px; }
      .hero h1 { font-size:28px; }
      .main { grid-template-columns:1fr; padding:32px 20px; }
    }
  </style>
</head>
<body>

<nav>
  <a class="nav-logo" href="index.php">Tarkina <span>·</span></a>
  <ul class="nav-links">
    <li><a href="index.php">Accueil</a></li>
    <li><a href="explorer.php">Explorer</a></li>
    <li><a href="about.php">À propos</a></li>
    <li><a href="contact.php" style="opacity:1;">Contact</a></li>
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
  <h1>Contactez-nous</h1>
  <p>Une question, un partenariat ou besoin d'aide ? Notre équipe est là pour vous.</p>
</div>

<div class="main">

  <div class="contact-info">
    <h2>Restons en contact</h2>
    <p>N'hésitez pas à nous écrire pour toute question concernant nos services, un partenariat ou pour devenir hôte sur Tarkina.</p>

    <div class="info-item">
      <div class="info-icon">📍</div>
      <div>
        <div class="info-label">Adresse</div>
        <div class="info-value">Tunis, Tunisie</div>
      </div>
    </div>

    <div class="info-item">
      <div class="info-icon">✉️</div>
      <div>
        <div class="info-label">Email</div>
        <div class="info-value"><a href="mailto:hello@tarkina.tn">hello@tarkina.tn</a></div>
      </div>
    </div>

    <div class="info-item">
      <div class="info-icon">📞</div>
      <div>
        <div class="info-label">Téléphone</div>
        <div class="info-value"><a href="tel:+21671000000">+216 71 000 000</a></div>
      </div>
    </div>

    <div class="info-item">
      <div class="info-icon">🕐</div>
      <div>
        <div class="info-label">Horaires</div>
        <div class="info-value">Lun – Ven : 9h – 18h</div>
      </div>
    </div>

    <div class="social-links">
      <a href="#" class="social-link">f</a>
      <a href="#" class="social-link">𝕏</a>
      <a href="#" class="social-link">📷</a>
      <a href="#" class="social-link">in</a>
    </div>
  </div>

  <div class="contact-form">
    <h3>Envoyez-nous un message</h3>

    <div class="success-msg" id="successMsg">✓ Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.</div>

    <form id="contactForm" onsubmit="return handleSubmit(event)">
      <div class="form-group">
        <label class="form-label">Nom complet</label>
        <input type="text" class="form-input" name="nom" placeholder="Votre nom" required>
      </div>

      <div class="form-group">
        <label class="form-label">Email</label>
        <input type="email" class="form-input" name="email" placeholder="votre@email.com" required>
      </div>

      <div class="form-group">
        <label class="form-label">Sujet</label>
        <select class="form-input" name="sujet" required>
          <option value="">Choisir un sujet</option>
          <option value="question">Question générale</option>
          <option value="reservation">Aide réservation</option>
          <option value="hote">Devenir hôte</option>
          <option value="partenariat">Partenariat</option>
          <option value="autre">Autre</option>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Message</label>
        <textarea class="form-input" name="message" placeholder="Décrivez votre demande..." required></textarea>
      </div>

      <button type="submit" class="btn-submit">Envoyer le message</button>
    </form>
  </div>

</div>

<footer>
  <p>&copy; <?= date('Y') ?> Tarkina — Voyagez autrement en Tunisie.</p>
</footer>

<script>
function handleSubmit(e) {
  e.preventDefault();
  document.getElementById('successMsg').style.display = 'block';
  document.getElementById('contactForm').reset();
  setTimeout(() => { document.getElementById('successMsg').style.display = 'none'; }, 5000);
  return false;
}
</script>

</body>
</html>
