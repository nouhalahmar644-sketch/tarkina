<?php
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/i18n.php';

$L_ALL = [
    'fr' => [
        'page_title' => 'Contact — Tarkina',
        'label' => 'Contact',
        'heading_a' => 'Une question ?', 'heading_b' => 'Parlons-en.',
        'subtext' => 'Notre équipe est disponible 7j/7 pour vous accompagner avant, pendant et après votre voyage.',
        'mail_lab' => 'Email',
        'phone_lab' => 'Téléphone',
        'office_lab' => 'Bureau', 'office_val' => '12 rue de la Médina, Tunis 1000',
        'lbl_name' => 'Nom', 'ph_name' => 'Votre nom',
        'lbl_email' => 'Email', 'ph_email' => 'votre@email.com',
        'lbl_subject' => 'Sujet', 'ph_subject' => "De quoi s'agit-il ?",
        'lbl_message' => 'Message', 'ph_message' => 'Décrivez votre demande...',
        'submit' => 'Envoyer le message',
        'err_empty' => 'Veuillez remplir tous les champs.',
        'ok_sent'   => 'Votre message a bien été envoyé !',
        'err_send'  => "Une erreur est survenue lors de l'envoi de votre message.",
        'err_prep'  => "Erreur lors de la préparation de l'envoi.",
    ],
    'ar' => [
        'page_title' => 'تواصل معنا — تاركينا',
        'label' => 'تواصل',
        'heading_a' => 'لديك سؤال؟', 'heading_b' => 'لنتحدّث.',
        'subtext' => 'فريقنا متاحٌ على مدار الأسبوع لمرافقتك قبل رحلتك وأثناءها وبعدها.',
        'mail_lab' => 'البريد الإلكتروني',
        'phone_lab' => 'الهاتف',
        'office_lab' => 'المكتب', 'office_val' => '12 نهج المدينة، تونس 1000',
        'lbl_name' => 'الاسم', 'ph_name' => 'اسمك',
        'lbl_email' => 'البريد الإلكتروني', 'ph_email' => 'votre@email.com',
        'lbl_subject' => 'الموضوع', 'ph_subject' => 'ما هو موضوعك؟',
        'lbl_message' => 'الرسالة', 'ph_message' => 'صِف طلبك...',
        'submit' => 'أرسل الرسالة',
        'err_empty' => 'يُرجى ملء جميع الحقول.',
        'ok_sent'   => 'تمّ إرسال رسالتك بنجاح!',
        'err_send'  => 'حدث خطأ أثناء إرسال رسالتك.',
        'err_prep'  => 'خطأ أثناء تحضير الإرسال.',
    ],
    'en' => [
        'page_title' => 'Contact — Tarkina',
        'label' => 'Contact',
        'heading_a' => 'Have a question?', 'heading_b' => "Let's talk.",
        'subtext' => 'Our team is available 7/7 to support you before, during and after your trip.',
        'mail_lab' => 'Email',
        'phone_lab' => 'Phone',
        'office_lab' => 'Office', 'office_val' => '12 Médina street, Tunis 1000',
        'lbl_name' => 'Name', 'ph_name' => 'Your name',
        'lbl_email' => 'Email', 'ph_email' => 'your@email.com',
        'lbl_subject' => 'Subject', 'ph_subject' => "What's it about?",
        'lbl_message' => 'Message', 'ph_message' => 'Describe your request...',
        'submit' => 'Send message',
        'err_empty' => 'Please fill in all fields.',
        'ok_sent'   => 'Your message has been sent!',
        'err_send'  => 'An error occurred while sending your message.',
        'err_prep'  => 'Error preparing to send.',
    ],
];
$L = $L_ALL[$lang] ?? $L_ALL['fr'];

$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom     = trim($_POST['nom'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $sujet   = trim($_POST['sujet'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if ($nom === '' || $email === '' || $sujet === '' || $message === '') {
        $error = $L['err_empty'];
    } else {
        mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `messages` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `nom` varchar(255) NOT NULL,
            `email` varchar(255) NOT NULL,
            `sujet` varchar(255) NOT NULL,
            `message` text NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
        $st = mysqli_prepare($conn, "INSERT INTO `messages` (`nom`, `email`, `sujet`, `message`) VALUES (?, ?, ?, ?)");
        if ($st) {
            mysqli_stmt_bind_param($st, 'ssss', $nom, $email, $sujet, $message);
            if (mysqli_stmt_execute($st)) { $success = $L['ok_sent']; }
            else { $error = $L['err_send']; }
            mysqli_stmt_close($st);
        } else { $error = $L['err_prep']; }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>" dir="<?= htmlspecialchars($dir) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($L['page_title']) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/rtl.css">
    <style>
        :root { --primary: #f16e22; --navy: #0b1c30; --light-bg: #FFFFFF; --text-dark: #1a1a1a; --text-muted: #6b7280; --border: #e5e7eb; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: #fff; color: var(--text-dark); }
        .page-wrapper { min-height: 100vh; display: flex; flex-direction: column; }
        .contact-section { flex: 1; padding: 60px 60px; display: grid; grid-template-columns: 1fr 1.2fr; gap: 80px; max-width: 1200px; margin: 0 auto; width: 100%; margin-top: 80px; }
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
        .btn-submit:hover { background: #d95716; }
        .alert-success { background: #d1fae5; color: #065f46; border-radius: 10px; padding: 12px 16px; font-size: 0.9rem; margin-bottom: 20px; }
        .alert-error { background: #fee2e2; color: #991b1b; border-radius: 10px; padding: 12px 16px; font-size: 0.9rem; margin-bottom: 20px; }
    </style>
</head>
<body>
<?php $navLight = true; include 'navbar.php'; ?>
<div class="page-wrapper">
    <section class="contact-section">
        <div class="contact-info">
            <p class="contact-label"><?= htmlspecialchars($L['label']) ?></p>
            <h1 class="contact-heading"><?= htmlspecialchars($L['heading_a']) ?><br><?= htmlspecialchars($L['heading_b']) ?></h1>
            <p class="contact-subtext"><?= htmlspecialchars($L['subtext']) ?></p>
            <div class="contact-item">
                <div class="contact-icon"><svg fill="none" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg></div>
                <div>
                    <p class="contact-item-label"><?= htmlspecialchars($L['mail_lab']) ?></p>
                    <p class="contact-item-value">hello@tarkina.tn</p>
                </div>
            </div>
            <div class="contact-item">
                <div class="contact-icon"><svg fill="none" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg></div>
                <div>
                    <p class="contact-item-label"><?= htmlspecialchars($L['phone_lab']) ?></p>
                    <p class="contact-item-value">+216 71 000 000</p>
                </div>
            </div>
            <div class="contact-item">
                <div class="contact-icon"><svg fill="none" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg></div>
                <div>
                    <p class="contact-item-label"><?= htmlspecialchars($L['office_lab']) ?></p>
                    <p class="contact-item-value"><?= htmlspecialchars($L['office_val']) ?></p>
                </div>
            </div>
        </div>
        <div class="contact-form-card">
            <?php if ($success): ?>
                <div class="alert-success"><?= htmlspecialchars($success) ?></div>
            <?php elseif ($error): ?>
                <div class="alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nom"><?= htmlspecialchars($L['lbl_name']) ?></label>
                        <input type="text" id="nom" name="nom" placeholder="<?= htmlspecialchars($L['ph_name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email"><?= htmlspecialchars($L['lbl_email']) ?></label>
                        <input type="email" id="email" name="email" placeholder="<?= htmlspecialchars($L['ph_email']) ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="sujet"><?= htmlspecialchars($L['lbl_subject']) ?></label>
                    <input type="text" id="sujet" name="sujet" placeholder="<?= htmlspecialchars($L['ph_subject']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="message"><?= htmlspecialchars($L['lbl_message']) ?></label>
                    <textarea id="message" name="message" placeholder="<?= htmlspecialchars($L['ph_message']) ?>" required></textarea>
                </div>
                <button type="submit" class="btn-submit"><?= htmlspecialchars($L['submit']) ?></button>
            </form>
        </div>
    </section>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
