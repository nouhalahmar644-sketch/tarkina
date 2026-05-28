<?php
session_start();
require_once __DIR__ . '/includes/i18n.php';

$L_ALL = [
    'fr' => [
        'page_title' => 'À propos — Tarkina',
        'label'      => 'À propos de Tarkina',
        'heading'    => 'Une passerelle vers la Tunisie vraie, portée par ses habitants.',
        'desc'       => 'Tarkina est une plateforme de voyage local qui connecte les visiteurs avec des hôtes, guides, artisans et familles dans les régions tunisiennes moins connues.',
        'btn_explore'=> 'Explorer les expériences →',
        'btn_contact'=> 'Nous contacter',
        'img1_alt'   => 'Tunisie authentique',
        'img2_alt'   => 'Sahara tunisien',
        'mission_label'   => 'Notre Mission',
        'mission_heading' => 'Faire voyager autrement.',
        'mission_p1' => 'Beaucoup de trésors tunisiens restent invisibles aux voyageurs : villages perchés, oasis, maisons traditionnelles, ateliers familiaux et tables locales. Tarkina existe pour rendre ces expériences accessibles sans les transformer en tourisme standardisé.',
        'mission_p2' => 'Notre rôle est simple : donner une vitrine claire aux habitants, faciliter la réservation, et aider chaque visiteur à vivre une rencontre sincère avec le territoire.',
        'v1_t' => 'Valoriser les habitants',
        'v1_d' => 'Tarkina met en avant les familles, guides, artisans et cuisinières qui font vivre chaque région.',
        'v2_t' => 'Révéler les lieux oubliés',
        'v2_d' => 'Nous aidons les voyageurs à sortir des circuits classiques pour découvrir des villages et paysages authentiques.',
        'v3_t' => 'Créer un échange juste',
        'v3_d' => 'Les expériences sont pensées pour rémunérer directement les locaux et préserver leur savoir-faire.',
        'v4_t' => 'Voyager avec respect',
        'v4_d' => 'Chaque séjour encourage une découverte lente, responsable et attentive aux cultures locales.',
        'dest_label'   => 'Nos destinations',
        'dest_heading' => 'Des régions choisies pour leur âme.',
        'dest_text'    => "De Chenini à Djerba, en passant par Tozeur, Takrouna ou Sidi Bou Saïd, chaque destination met en lumière un patrimoine vivant : architecture, cuisine, artisanat, récits et hospitalité.",
        'dest_img_alt' => 'Désert tunisien',
    ],
    'ar' => [
        'page_title' => 'من نحن — تاركينا',
        'label'      => 'عن تاركينا',
        'heading'    => 'جسرٌ نحو تونس الأصيلة، يحمله أهلها.',
        'desc'       => 'تاركينا منصة سفرٍ محلية تربط الزوار بمضيفين ومرشدين وحرفيين وعائلات في الجهات التونسية الأقلّ شهرة.',
        'btn_explore'=> '← استكشف التجارب',
        'btn_contact'=> 'تواصل معنا',
        'img1_alt'   => 'تونس الأصيلة',
        'img2_alt'   => 'صحراء تونس',
        'mission_label'   => 'مهمتنا',
        'mission_heading' => 'اسفر بطريقةٍ مختلفة.',
        'mission_p1' => 'كثير من كنوز تونس تظلّ غير مرئية للزوار: قرى متعلقة بالجبال، واحات، بيوت تقليدية، ورشات عائلية وموائد محلية. تاركينا تعمل على إتاحة هذه التجارب دون تحويلها إلى سياحة نمطية.',
        'mission_p2' => 'دورنا بسيط: نقدّم واجهة واضحة للأهالي، نُسهّل الحجز، ونساعد كلّ زائرٍ على عيش لقاءٍ صادق مع الأرض وأهلها.',
        'v1_t' => 'إبراز الأهالي',
        'v1_d' => 'تاركينا تسلّط الضوء على العائلات والمرشدين والحرفيين والطبّاخات الذين يُحيون كل جهة.',
        'v2_t' => 'كشف الأماكن المنسيّة',
        'v2_d' => 'نساعد المسافرين على الخروج من المسارات التقليدية لاكتشاف قرى ومناظر أصيلة.',
        'v3_t' => 'تبادلٌ عادل',
        'v3_d' => 'التجارب مصمَّمة لتمنح الأهالي عائدًا مباشرًا وتحفظ مهاراتهم وحرفهم.',
        'v4_t' => 'سفرٌ باحترام',
        'v4_d' => 'كل إقامة تشجّع على اكتشافٍ هادئٍ ومسؤول، يحترم الثقافات المحلية.',
        'dest_label'   => 'وجهاتنا',
        'dest_heading' => 'جهاتٌ مختارة لروحها.',
        'dest_text'    => 'من شنني إلى جربة، مرورًا بتوزر وتكرونة وسيدي بوسعيد، كل وجهة تُبرز تراثًا حيًّا: عمارة، مطبخ، حِرف، حكايا، وكَرَم ضيافة.',
        'dest_img_alt' => 'الصحراء التونسية',
    ],
    'en' => [
        'page_title' => 'About — Tarkina',
        'label'      => 'About Tarkina',
        'heading'    => 'A bridge to the real Tunisia, carried by its people.',
        'desc'       => 'Tarkina is a local-travel platform connecting visitors with hosts, guides, artisans and families in lesser-known Tunisian regions.',
        'btn_explore'=> 'Explore experiences →',
        'btn_contact'=> 'Contact us',
        'img1_alt'   => 'Authentic Tunisia',
        'img2_alt'   => 'Tunisian Sahara',
        'mission_label'   => 'Our Mission',
        'mission_heading' => 'Travel, differently.',
        'mission_p1' => "Many Tunisian treasures stay hidden from travelers: clifftop villages, oases, traditional houses, family workshops and local tables. Tarkina exists to make these experiences accessible without turning them into mass tourism.",
        'mission_p2' => 'Our role is simple: give locals a clear showcase, make booking easy, and help every visitor have a genuine encounter with the land.',
        'v1_t' => 'Celebrate locals',
        'v1_d' => 'Tarkina puts families, guides, artisans and cooks at the heart of every region.',
        'v2_t' => 'Reveal forgotten places',
        'v2_d' => 'We help travelers leave the classic routes and discover authentic villages and landscapes.',
        'v3_t' => 'Build a fair exchange',
        'v3_d' => 'Experiences are designed to compensate locals directly and preserve their know-how.',
        'v4_t' => 'Travel respectfully',
        'v4_d' => 'Every stay encourages slow, responsible discovery mindful of local cultures.',
        'dest_label'   => 'Our destinations',
        'dest_heading' => 'Regions chosen for their soul.',
        'dest_text'    => 'From Chenini to Djerba, through Tozeur, Takrouna and Sidi Bou Saïd, each destination showcases a living heritage: architecture, food, crafts, stories and hospitality.',
        'dest_img_alt' => 'Tunisian desert',
    ],
];
$L = $L_ALL[$lang] ?? $L_ALL['fr'];
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
        :root { --primary: #1B6B45; --navy: #111111; --light-bg: #FFFFFF; --text-dark: #1a1a1a; --text-muted: #6b7280; --border: #e5e7eb; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: #fff; color: var(--text-dark); }
        .about-hero { padding: 80px 60px 80px; display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center; max-width: 1200px; margin: 0 auto; }
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
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<section style="padding-top:0;">
    <div class="about-hero">
        <div>
            <p class="about-label"><?= htmlspecialchars($L['label']) ?></p>
            <h1 class="about-heading"><?= htmlspecialchars($L['heading']) ?></h1>
            <p class="about-desc"><?= htmlspecialchars($L['desc']) ?></p>
            <div class="about-buttons">
                <a href="explorer.php" class="btn-primary-solid"><?= htmlspecialchars($L['btn_explore']) ?></a>
                <a href="contact.php" class="btn-outline-dark"><?= htmlspecialchars($L['btn_contact']) ?></a>
            </div>
        </div>
        <div class="about-images">
            <img class="about-img-main" src="https://images.unsplash.com/photo-1561625116-5f8675632053?w=800&fit=crop" alt="<?= htmlspecialchars($L['img1_alt']) ?>">
            <img class="about-img-accent" src="https://images.unsplash.com/photo-1509316785289-025f5b846b35?w=800&fit=crop" alt="<?= htmlspecialchars($L['img2_alt']) ?>">
        </div>
    </div>
</section>

<hr class="section-divider">

<section>
    <div class="mission-section">
        <div class="mission-grid">
            <div>
                <p class="mission-label"><?= htmlspecialchars($L['mission_label']) ?></p>
                <h2 class="mission-heading"><?= htmlspecialchars($L['mission_heading']) ?></h2>
            </div>
            <div>
                <p class="mission-text"><?= htmlspecialchars($L['mission_p1']) ?></p>
                <p class="mission-text"><?= htmlspecialchars($L['mission_p2']) ?></p>
            </div>
        </div>
        <div class="values-grid">
            <div class="value-card">
                <div class="value-icon"><svg fill="none" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg></div>
                <p class="value-title"><?= htmlspecialchars($L['v1_t']) ?></p>
                <p class="value-desc"><?= htmlspecialchars($L['v1_d']) ?></p>
            </div>
            <div class="value-card">
                <div class="value-icon"><svg fill="none" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg></div>
                <p class="value-title"><?= htmlspecialchars($L['v2_t']) ?></p>
                <p class="value-desc"><?= htmlspecialchars($L['v2_d']) ?></p>
            </div>
            <div class="value-card">
                <div class="value-icon"><svg fill="none" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg></div>
                <p class="value-title"><?= htmlspecialchars($L['v3_t']) ?></p>
                <p class="value-desc"><?= htmlspecialchars($L['v3_d']) ?></p>
            </div>
            <div class="value-card">
                <div class="value-icon"><svg fill="none" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg></div>
                <p class="value-title"><?= htmlspecialchars($L['v4_t']) ?></p>
                <p class="value-desc"><?= htmlspecialchars($L['v4_d']) ?></p>
            </div>
        </div>
    </div>
</section>

<section class="destinations-section">
    <div class="destinations-inner">
        <img class="destinations-img" src="https://images.unsplash.com/photo-1509316785289-025f5b846b35?w=800&fit=crop" alt="<?= htmlspecialchars($L['dest_img_alt']) ?>">
        <div>
            <p class="dest-label"><?= htmlspecialchars($L['dest_label']) ?></p>
            <h2 class="dest-heading"><?= htmlspecialchars($L['dest_heading']) ?></h2>
            <p class="dest-text"><?= htmlspecialchars($L['dest_text']) ?></p>
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
