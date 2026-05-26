<?php
session_start();
require_once __DIR__ . '/db.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom     = trim($_POST['nom'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $sujet   = trim($_POST['sujet'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    if ($nom === '' || $email === '' || $sujet === '' || $message === '') {
        $error = "Veuillez remplir tous les champs.";
    } else {
        // Ensure table exists
        $createTableQuery = "CREATE TABLE IF NOT EXISTS `messages` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `nom` varchar(255) NOT NULL,
            `email` varchar(255) NOT NULL,
            `sujet` varchar(255) NOT NULL,
            `message` text NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
        mysqli_query($conn, $createTableQuery);

        $insertQuery = "INSERT INTO `messages` (`nom`, `email`, `sujet`, `message`) VALUES (?, ?, ?, ?)";
        $st = mysqli_prepare($conn, $insertQuery);
        if ($st) {
            mysqli_stmt_bind_param($st, 'ssss', $nom, $email, $sujet, $message);
            if (mysqli_stmt_execute($st)) {
                $success = "Votre message a bien été envoyé !";
            } else {
                $error = "Une erreur est survenue lors de l'envoi de votre message.";
            }
            mysqli_stmt_close($st);
        } else {
            $error = "Erreur lors de la préparation de l'envoi.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact — Tarkina</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        :root { --primary: #E05A2B; --navy: #1B3A4B; --light-bg: #FAF8F5; --text-dark: #1a1a1a; --text-muted: #6b7280; --border: #e5e7eb; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: #fff; color: var(--text-dark); }
        .navbar { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; background: #fff; border-bottom: 1px solid var(--border); padding: 0 60px; height: 70px; display: flex; align-items: center; justify-content: space-between; }
        .nav-logo img { height: 36px; }
        .nav-logo span { font-size: 1.4rem; font-weight: 800; color: var(--navy); letter-spacing: -1px; }
        .nav-links { display: flex; gap: 36px; list-style: none; }
        .nav-links a { text-decoration: none; color: var(--text-dark); font-size: 0.95rem; font-weight: 500; transition: color .2s; }
        .nav-links a:hover, .nav-links a.active { color: var(--primary); }
        .nav-auth { display: flex; gap: 12px; align-items: center; }
        .btn-nav-outline { padding: 8px 20px; border: 1.5px solid var(--navy); border-radius: 50px; color: var(--navy); text-decoration: none; font-size: 0.9rem; font-weight: 600; transition: all .2s; }
        .btn-nav-outline:hover { background: var(--navy); color: #fff; }
        .btn-nav-primary { padding: 8px 20px; background: var(--primary); border-radius: 50px; color: #fff; text-decoration: none; font-size: 0.9rem; font-weight: 600; transition: background .2s; }
        .btn-nav-primary:hover { background: #c44d22; }
        .page-wrapper { min-height: 100vh; padding-top: 70px; display: flex; flex-direction: column; }
        .contact-section { flex: 1; padding: 80px 60px; display: grid; grid-template-columns: 1fr 1.2fr; gap: 80px; max-width: 1200px; margin: 0 auto; width: 100%; }
        .contact-info { padding-top: 12px; }
        .contact-label { font-size: 0.78rem; font-weight: 700; letter-spacing: 2px; color: var(--primary); text-transform: uppercase; margin-bottom: 20px; }
        .contact-heading { font-size: 3rem; font-weight: 800; line-height: 1.15; color: var(--text-dark); margin-bottom: 20px; font-family: Georgia, 'Times New Roman', serif; }
        .contact-subtext { font-size: 1rem; color: var(--text-muted); line-height: 1.7; margin-bottom: 48px; }
        .contact-item { display: flex; align-items: flex-start; gap: 16px; margin-bottom: 28px; }
        .contact-icon { width: 46px; height: 46px; border-radius: 12px; background: #FEF0EA; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .contact-icon svg { width: 20px; height: 20px; stroke: var(--primary); }
        .contact-item-label { font-weight: 700; font-size: 0.95rem; margin-bottom: 3px; }
        .contact-item-value { color: var(--text-muted); font-size: 0.9rem; }
        .contact-form-card { background: #fff; border: 1px solid var(--border); border-radius: 20px; padding: 44px; box-shadow: 0 4px 40px rgba(0,0,0,0.07); }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .form-group { display: flex; flex-direction: column; gap: 8px; margin-bottom: 20px; }
        .form-group label { font-size: 0.88rem; font-weight: 600; color: var(--text-dark); }
        .form-group input, .form-group textarea { padding: 12px 16px; border: 1.5px solid var(--border); border-radius: 10px; font-size: 0.95rem; font-family: inherit; color: var(--text-dark); transition: border-color .2s; background: #fafafa; outline: none; }
        .form-group input:focus, .form-group textarea:focus { border-color: var(--primary); background: #fff; }
        .form-group textarea { resize: vertical; min-height: 130px; }
        .btn-submit { width: 100%; padding: 14px; background: var(--primary); color: #fff; border: none; border-radius: 10px; font-size: 1rem; font-weight: 700; cursor: pointer; transition: background .2s; margin-top: 4px; }
        .btn-submit:hover { background: #c44d22; }
        .alert-success { background: #d1fae5; color: #065f46; border-radius: 10px; padding: 12px 16px; font-size: 0.9rem; margin-bottom: 20px; }
        .alert-error { background: #fee2e2; color: #991b1b; border-radius: 10px; padding: 12px 16px; font-size: 0.9rem; margin-bottom: 20px; }
        footer { background: var(--navy); color: #fff; padding: 60px 60px 30px; }
        .footer-grid { display: grid; grid-template-columns: 1.4fr 1fr 1fr 1fr; gap: 48px; margin-bottom: 48px; }
        .footer-brand-name { font-size: 1.6rem; font-weight: 800; margin-bottom: 12px; }
        .footer-brand-desc { color: rgba(255,255,255,0.6); font-size: 0.9rem; line-height: 1.7; margin-bottom: 20px; }
        .footer-socials { display: flex; gap: 12px; }
        .footer-socials a { width: 36px; height: 36px; border-radius: 50%; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; color: #fff; text-decoration: none; font-size: 0.85rem; transition: background .2s; }
        .footer-socials a:hover { background: var(--primary); }
        .footer-col h4 { font-size: 0.78rem; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: rgba(255,255,255,0.5); margin-bottom: 20px; }
        .footer-col ul { list-style: none; }
        .footer-col ul li { margin-bottom: 10px; }
        .footer-col ul li a { color: rgba(255,255,255,0.75); text-decoration: none; font-size: 0.9rem; transition: color .2s; }
        .footer-col ul li a:hover { color: #fff; }
        .footer-contact-item { display: flex; align-items: center; gap: 10px; color: rgba(255,255,255,0.75); font-size: 0.9rem; margin-bottom: 10px; }
        .footer-contact-item svg { width: 15px; height: 15px; flex-shrink: 0; stroke: rgba(255,255,255,0.5); }
        .footer-divider { border: none; border-top: 1px solid rgba(255,255,255,0.1); margin-bottom: 24px; }
        .footer-bottom { display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem; color: rgba(255,255,255,0.4); }
        .footer-watermark { text-align: center; font-size: 8vw; font-weight: 800; color: rgba(255,255,255,0.04); letter-spacing: 4px; line-height: 1; margin-bottom: -10px; pointer-events: none; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="page-wrapper">
    <section class="contact-section">
        <div class="contact-info">
            <p class="contact-label">Contact</p>
            <h1 class="contact-heading">Une question ?<br>Parlons-en.</h1>
            <p class="contact-subtext">Notre équipe est disponible 7j/7 pour vous accompagner avant, pendant et après votre voyage.</p>
            <div class="contact-item">
                <div class="contact-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                </div>
                <div>
                    <p class="contact-item-label">Email</p>
                    <p class="contact-item-value">hello@tarkina.tn</p>
                </div>
            </div>
            <div class="contact-item">
                <div class="contact-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                </div>
                <div>
                    <p class="contact-item-label">Téléphone</p>
                    <p class="contact-item-value">+216 71 000 000</p>
                </div>
            </div>
            <div class="contact-item">
                <div class="contact-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                </div>
                <div>
                    <p class="contact-item-label">Bureau</p>
                    <p class="contact-item-value">12 rue de la Médina, Tunis 1000</p>
                </div>
            </div>
        </div>
        <div class="contact-form-card">
            <?php if($success): ?>
                <div class="alert-success"><?= htmlspecialchars($success) ?></div>
            <?php elseif($error): ?>
                <div class="alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nom">Nom</label>
                        <input type="text" id="nom" name="nom" placeholder="Votre nom" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="votre@email.com" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="sujet">Sujet</label>
                    <input type="text" id="sujet" name="sujet" placeholder="De quoi s'agit-il ?" required>
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" placeholder="Décrivez votre demande..." required></textarea>
                </div>
                <button type="submit" class="btn-submit">Envoyer le message</button>
            </form>
        </div>
    </section>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
