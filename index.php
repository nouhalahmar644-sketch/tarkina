<?php
session_start();

require 'db.php';
require_once __DIR__ . '/includes/i18n.php';   // sets $lang, $dir, $is_rtl

// ---------- Translations ----------
$L_ALL = [
    'fr' => [
        'page_title' => 'Tarkina — Voyagez autrement en Tunisie',
        'hero_full'  => "VIVEZ LA TUNISIE <span style=\"color:var(--primary)\">AUTREMENT</span>.<br>AU PLUS PRÈS DE SES HABITANTS.",
        'hero_sub'   => "Découvrez l'autre Tunisie. Des régions inexplorées, des rencontres authentiques.",
        'bk_destination' => 'Destination',  'bk_where'       => 'Où allez-vous ?',
        'bk_arrival'     => 'Arrivée',      'bk_departure'   => 'Départ',
        'bk_add_date'    => 'Ajouter une date',
        'bk_travelers'   => 'Voyageurs',    'bk_search_title' => 'Rechercher',
        'cta_explore_regions' => 'Explorer les régions',
        'cta_discover_blog'   => 'Découvrir le blog',
        'trust1_t' => 'Annulation flexible',  'trust1_s' => "Annulez jusqu'à 48h avant",
        'trust2_t' => 'Hôtes vérifiés',       'trust2_s' => 'Chaque hôte est vérifié par notre équipe',
        'trust3_t' => 'Paiement sécurisé',    'trust3_s' => 'Transactions protégées et cryptées',
        'trust4_t' => 'Support 7j/7',         'trust4_s' => 'En français, arabe et anglais',
        'pill_heb' => 'Hébergement', 'pill_repas' => 'Repas maison', 'pill_guide' => 'Guide local',
        'pill_art' => 'Produits artisanaux', 'pill_event' => 'Événements',
        'regs_label' => 'Destinations · Tunisie',
        'regs_h_pre' => 'Découvrez nos ', 'regs_h_em' => 'régions',
        'regs_see_all' => 'Voir toutes les régions →',
        'regs_service' => 'service', 'regs_services' => 'services',
        'regs_none' => 'Aucune région disponible pour le moment.',
        'pk_label' => 'Nos Packs',
        'pk_h_pre' => "Partez l'esprit ", 'pk_h_em' => 'tranquille',
        'pk_sub' => 'Des forfaits complets, pensés pour un voyage sans souci',
        'pk_from' => 'À partir de',
        'pk1_loc' => 'Douz & Tozeur · 4 jours', 'pk1_title' => 'Escapade Saharienne',
        'pk1_i1' => 'Hébergement', 'pk1_i2' => 'Guide local', 'pk1_i3' => 'Repas traditionnels', 'pk1_i4' => 'Balade à dromadaire',
        'pk2_loc' => 'Kairouan · 3 jours', 'pk2_title' => 'Kairouanite Culturelle',
        'pk2_i1' => 'Hébergement', 'pk2_i2' => 'Guide certifié', 'pk2_i3' => 'Visites monuments',
        'pk3_loc' => 'Djerba · 5 jours', 'pk3_title' => 'Djerba Authentique',
        'pk3_i1' => 'Hébergement bord de mer', 'pk3_i2' => 'Atelier poterie', 'pk3_i3' => 'Repas inclus', 'pk3_i4' => 'Transferts',
        'pk_book' => 'Réserver ce forfait',
        'stat_trav' => 'Voyageurs', 'stat_regs' => 'Régions découvertes',
        'stat_lodge' => 'Hébergements', 'stat_sat' => 'Satisfaction moyenne',
        'gal_title' => 'La Tunisie en Images', 'gal_cta' => 'Explorer toutes les régions →',
        'exp_label' => 'Expériences',
        'exp_h_pre' => 'Trouvez votre ', 'exp_h_em' => 'expérience idéale',
        'exp_sub' => 'Cinq façons de vivre la Tunisie autrement',
        'exp1_adj' => 'Authentique', 'exp1_t' => 'Séjours',         'exp1_d' => "Dormez chez l'habitant au cœur des régions",
        'exp2_adj' => 'Savoureux',   'exp2_t' => 'Repas maison',    'exp2_d' => 'Tables familiales et cuisine traditionnelle',
        'exp3_adj' => 'Aventurier',  'exp3_t' => 'Guides locaux',   'exp3_d' => 'Explorez avec un habitant passionné',
        'exp4_adj' => 'Artisanal',   'exp4_t' => 'Artisanat local', 'exp4_d' => 'Pièces uniques faites à la main',
        'exp5_adj' => 'Festif',      'exp5_t' => 'Événements',      'exp5_d' => 'Fêtes, festivals et célébrations locales',
        'why_label' => 'Notre engagement',
        'why_h_pre' => 'Pourquoi ', 'why_h_em' => 'Tarkina', 'why_h_q' => ' ?',
        'why_sub' => 'Une plateforme pensée pour valoriser les territoires oubliés et leurs habitants.',
        'why1_t' => 'Authentique', 'why1_d' => 'Des hôtes locaux qui partagent leur quotidien et leur culture, loin des services fabriqués.',
        'why2_t' => 'Local',       'why2_d' => '100% des services sont opérés par des habitants des régions tunisiennes méconnues.',
        'why3_t' => 'Sécurisé',    'why3_d' => 'Hôtes vérifiés, paiement protégé, support 7j/7 en français, arabe et anglais.',
        'why4_t' => 'Abordable',   'why4_d' => 'Des prix justes, fixés par les locaux, sans intermédiaires gourmands.',
        'temo_label' => 'Témoignages', 'temo_h' => 'ILS ONT VOYAGÉ AVEC TARKINA',
        'temo1_q' => "\"Un séjour inoubliable à Kessra. L'accueil était incroyable...\"",
        'temo1_qf' => "\"Un séjour inoubliable à Kessra. L'accueil de la famille était incroyable et la nourriture... je n'ai jamais mangé un couscous aussi bon !\"",
        'temo1_role' => 'Voyageuse · Paris', 'temo1_tag' => '📍 Kessra, Siliana',
        'temo2_q' => "\"Le guide local m'a emmené dans des endroits magiques...\"",
        'temo2_qf' => "\"Le guide local m'a emmené dans des endroits que je n'aurais jamais trouvés seul. Une expérience humaine authentique, loin du tourisme de masse.\"",
        'temo2_role' => 'Voyageur · Lyon', 'temo2_tag' => '📍 Douz, Kébili',
        'temo3_q' => "\"L'atelier poterie à Sejnane était une expérience magique...\"",
        'temo3_qf' => "\"L'atelier poterie à Sejnane était magique. La maîtresse artisane nous a transmis un savoir-faire ancestral avec une générosité rare.\"",
        'temo3_role' => 'Voyageuse · Montréal', 'temo3_tag' => '📍 Sejnane, Bizerte',
        'nl_badge' => 'Rejoignez 1 200+ voyageurs',
        'nl_title' => 'Prêt pour votre prochaine aventure ?',
        'nl_sub' => 'Recevez nos meilleures offres, guides de destinations et conseils exclusifs directement dans votre boîte mail.',
        'nl_email_ph' => 'Votre adresse email', 'nl_submit' => "S'abonner",
        'nl_msg_invalid' => 'Veuillez saisir une adresse e-mail valide.',
        'nl_msg_dup' => 'Cette adresse e-mail est déjà inscrite.',
        'nl_msg_err' => 'Une erreur est survenue. Réessayez.',
        'nl_msg_ok' => 'Merci pour votre inscription !',
    ],
    'ar' => [
        'page_title' => 'تاركينا — سافر إلى تونس بطريقة مختلفة',
        'hero_full'  => "عِش تونس <span style=\"color:var(--primary)\">بطريقة مختلفة</span>.<br>على مقربةٍ من أهلها.",
        'hero_sub'   => "اكتشف وجه تونس الآخر. جهات لم تُستكشف ولقاءات أصيلة.",
        'bk_destination' => 'الوجهة',  'bk_where' => 'إلى أين تذهب؟',
        'bk_arrival' => 'الوصول',      'bk_departure' => 'المغادرة',
        'bk_add_date' => 'اختر تاريخًا',
        'bk_travelers' => 'المسافرون',  'bk_search_title' => 'ابحث',
        'cta_explore_regions' => 'استكشف الجهات',
        'cta_discover_blog'   => 'اكتشف المدونة',
        'trust1_t' => 'إلغاء مرن',          'trust1_s' => 'ألغِ حتى 48 ساعة قبل الموعد',
        'trust2_t' => 'مضيفون موثّقون',     'trust2_s' => 'نتحقق من كل مضيف بأنفسنا',
        'trust3_t' => 'دفع آمن',            'trust3_s' => 'معاملات محمية ومشفّرة',
        'trust4_t' => 'دعم على مدار الأسبوع','trust4_s' => 'بالفرنسية والعربية والإنجليزية',
        'pill_heb' => 'الإقامة', 'pill_repas' => 'وجبات منزلية', 'pill_guide' => 'مرشد محلي',
        'pill_art' => 'منتجات حرفية', 'pill_event' => 'فعاليات',
        'regs_label' => 'وجهات · تونس',
        'regs_h_pre' => 'اكتشف ', 'regs_h_em' => 'جهاتنا',
        'regs_see_all' => '← شاهد كل الجهات',
        'regs_service' => 'خدمة', 'regs_services' => 'خدمات',
        'regs_none' => 'لا توجد جهات متاحة حاليًا.',
        'pk_label' => 'باقاتنا',
        'pk_h_pre' => 'سافر بكل ', 'pk_h_em' => 'اطمئنان',
        'pk_sub' => 'باقات متكاملة لرحلة دون عناء',
        'pk_from' => 'ابتداءً من',
        'pk1_loc' => 'دوز وتوزر · 4 أيام', 'pk1_title' => 'هروبة صحراوية',
        'pk1_i1' => 'إقامة', 'pk1_i2' => 'مرشد محلي', 'pk1_i3' => 'وجبات تقليدية', 'pk1_i4' => 'جولة على الجمل',
        'pk2_loc' => 'القيروان · 3 أيام', 'pk2_title' => 'القيروان الثقافية',
        'pk2_i1' => 'إقامة', 'pk2_i2' => 'مرشد معتمد', 'pk2_i3' => 'زيارة معالم',
        'pk3_loc' => 'جربة · 5 أيام', 'pk3_title' => 'جربة الأصيلة',
        'pk3_i1' => 'إقامة على البحر', 'pk3_i2' => 'ورشة خزف', 'pk3_i3' => 'وجبات مشمولة', 'pk3_i4' => 'تنقّلات',
        'pk_book' => 'احجز هذه الباقة',
        'stat_trav' => 'مسافر', 'stat_regs' => 'جهات مكتشفة',
        'stat_lodge' => 'أماكن إقامة', 'stat_sat' => 'متوسط الرضا',
        'gal_title' => 'تونس بالصور', 'gal_cta' => '← استكشف كل الجهات',
        'exp_label' => 'تجارب',
        'exp_h_pre' => 'اعثر على ', 'exp_h_em' => 'تجربتك المثالية',
        'exp_sub' => 'خمس طرق لعيش تونس بشكل مختلف',
        'exp1_adj' => 'أصيل',    'exp1_t' => 'إقامات',     'exp1_d' => 'بِت عند الأهالي في قلب الجهات',
        'exp2_adj' => 'شهيّ',    'exp2_t' => 'وجبات منزلية','exp2_d' => 'موائد عائلية ومطبخ تقليدي',
        'exp3_adj' => 'مغامِر',  'exp3_t' => 'مرشدون محليون','exp3_d' => 'استكشف مع ساكنٍ شغوف',
        'exp4_adj' => 'حِرفي',   'exp4_t' => 'حِرف محلية',  'exp4_d' => 'قطع فريدة مصنوعة يدويًا',
        'exp5_adj' => 'احتفالي', 'exp5_t' => 'فعاليات',     'exp5_d' => 'حفلات ومهرجانات واحتفالات محلية',
        'why_label' => 'التزامنا',
        'why_h_pre' => 'لماذا ', 'why_h_em' => 'تاركينا', 'why_h_q' => '؟',
        'why_sub' => 'منصة لإبراز الأماكن المنسيّة وأهلها.',
        'why1_t' => 'أصيل',          'why1_d' => 'مضيفون محليون يشاركونك يومياتهم وثقافتهم بعيدًا عن الخدمات المصطنعة.',
        'why2_t' => 'محلي',          'why2_d' => 'كل الخدمات يقدّمها سكان جهات تونسية أقلّ شهرة.',
        'why3_t' => 'آمن',           'why3_d' => 'مضيفون موثّقون، دفع آمن، ودعم على مدار الأسبوع بثلاث لغات.',
        'why4_t' => 'بأسعار معقولة', 'why4_d' => 'أسعار عادلة يحددها الأهالي، بلا وسطاء.',
        'temo_label' => 'شهادات', 'temo_h' => 'سافروا مع تاركينا',
        'temo1_q'  => '"إقامة لا تُنسى في كسرى. كانت الضيافة رائعة..."',
        'temo1_qf' => '"إقامة لا تُنسى في كسرى. ضيافة العائلة كانت رائعة، والطعام... لم أتذوّق كسكسًا ألذّ منه!"',
        'temo1_role' => 'مسافرة · باريس', 'temo1_tag' => '📍 كسرى، سليانة',
        'temo2_q'  => '"أخذني المرشد المحلي إلى أماكن ساحرة..."',
        'temo2_qf' => '"أخذني المرشد إلى أماكن لم أكن لأجدها وحدي. تجربة إنسانية أصيلة بعيدة عن السياحة الجماهيرية."',
        'temo2_role' => 'مسافر · ليون', 'temo2_tag' => '📍 دوز، قبلي',
        'temo3_q'  => '"كانت ورشة الخزف في سجنان تجربة ساحرة..."',
        'temo3_qf' => '"كانت ورشة الخزف في سجنان ساحرة. نقلت لنا المعلّمة الحرفية تراثًا عريقًا بسخاءٍ نادر."',
        'temo3_role' => 'مسافرة · مونتريال', 'temo3_tag' => '📍 سجنان، بنزرت',
        'nl_badge' => 'انضم إلى أكثر من 1200 مسافر',
        'nl_title' => 'هل أنت جاهز لمغامرتك القادمة؟',
        'nl_sub' => 'استلم أفضل العروض وأدلة الوجهات ونصائح حصرية مباشرة في بريدك.',
        'nl_email_ph' => 'عنوان بريدك الإلكتروني', 'nl_submit' => 'اشترك',
        'nl_msg_invalid' => 'يُرجى إدخال عنوان بريد إلكتروني صحيح.',
        'nl_msg_dup' => 'هذا البريد الإلكتروني مسجّل من قبل.',
        'nl_msg_err' => 'حدث خطأ. أعد المحاولة.',
        'nl_msg_ok' => 'شكرًا لاشتراكك!',
    ],
    'en' => [
        'page_title' => 'Tarkina — Travel Tunisia differently',
        'hero_full'  => "LIVE TUNISIA <span style=\"color:var(--primary)\">DIFFERENTLY</span>.<br>CLOSER TO ITS PEOPLE.",
        'hero_sub'   => "Discover the other Tunisia. Unexplored regions, authentic encounters.",
        'bk_destination' => 'Destination', 'bk_where' => 'Where to?',
        'bk_arrival' => 'Check-in', 'bk_departure' => 'Check-out',
        'bk_add_date' => 'Add a date',
        'bk_travelers' => 'Travelers', 'bk_search_title' => 'Search',
        'cta_explore_regions' => 'Explore regions',
        'cta_discover_blog'   => 'Discover the blog',
        'trust1_t' => 'Flexible cancellation', 'trust1_s' => 'Cancel up to 48h before',
        'trust2_t' => 'Verified hosts',        'trust2_s' => 'Every host is vetted by our team',
        'trust3_t' => 'Secure payment',        'trust3_s' => 'Protected, encrypted transactions',
        'trust4_t' => '7/7 support',           'trust4_s' => 'In French, Arabic and English',
        'pill_heb' => 'Stays', 'pill_repas' => 'Home meals', 'pill_guide' => 'Local guides',
        'pill_art' => 'Crafts', 'pill_event' => 'Events',
        'regs_label' => 'Destinations · Tunisia',
        'regs_h_pre' => 'Discover our ', 'regs_h_em' => 'regions',
        'regs_see_all' => 'See all regions →',
        'regs_service' => 'service', 'regs_services' => 'services',
        'regs_none' => 'No regions available for now.',
        'pk_label' => 'Our Packs',
        'pk_h_pre' => 'Travel with ', 'pk_h_em' => 'peace of mind',
        'pk_sub' => 'Complete packs designed for a stress-free trip',
        'pk_from' => 'From',
        'pk1_loc' => 'Douz & Tozeur · 4 days', 'pk1_title' => 'Saharan Getaway',
        'pk1_i1' => 'Accommodation', 'pk1_i2' => 'Local guide', 'pk1_i3' => 'Traditional meals', 'pk1_i4' => 'Camel ride',
        'pk2_loc' => 'Kairouan · 3 days', 'pk2_title' => 'Kairouan Cultural',
        'pk2_i1' => 'Accommodation', 'pk2_i2' => 'Certified guide', 'pk2_i3' => 'Monument visits',
        'pk3_loc' => 'Djerba · 5 days', 'pk3_title' => 'Djerba Authentic',
        'pk3_i1' => 'Seaside lodging', 'pk3_i2' => 'Pottery workshop', 'pk3_i3' => 'Meals included', 'pk3_i4' => 'Transfers',
        'pk_book' => 'Book this pack',
        'stat_trav' => 'Travelers', 'stat_regs' => 'Regions discovered',
        'stat_lodge' => 'Stays', 'stat_sat' => 'Average rating',
        'gal_title' => 'Tunisia in Images', 'gal_cta' => 'Explore all regions →',
        'exp_label' => 'Experiences',
        'exp_h_pre' => 'Find your ', 'exp_h_em' => 'ideal experience',
        'exp_sub' => 'Five ways to live Tunisia differently',
        'exp1_adj' => 'Authentic',   'exp1_t' => 'Stays',          'exp1_d' => 'Sleep with locals at the heart of regions',
        'exp2_adj' => 'Savoury',     'exp2_t' => 'Home meals',     'exp2_d' => 'Family tables and traditional cuisine',
        'exp3_adj' => 'Adventurous', 'exp3_t' => 'Local guides',   'exp3_d' => 'Explore with a passionate local',
        'exp4_adj' => 'Artisanal',   'exp4_t' => 'Local crafts',   'exp4_d' => 'One-of-a-kind handmade pieces',
        'exp5_adj' => 'Festive',     'exp5_t' => 'Events',         'exp5_d' => 'Parties, festivals and local celebrations',
        'why_label' => 'Our commitment',
        'why_h_pre' => 'Why ', 'why_h_em' => 'Tarkina', 'why_h_q' => '?',
        'why_sub' => 'A platform built to celebrate forgotten regions and the people who live in them.',
        'why1_t' => 'Authentic',  'why1_d' => 'Local hosts who share their daily life and culture, far from manufactured experiences.',
        'why2_t' => 'Local',      'why2_d' => '100% of services are run by people from lesser-known Tunisian regions.',
        'why3_t' => 'Secure',     'why3_d' => 'Verified hosts, protected payments, 7/7 support in French, Arabic and English.',
        'why4_t' => 'Affordable', 'why4_d' => 'Fair prices set by locals, no greedy middlemen.',
        'temo_label' => 'Testimonials', 'temo_h' => 'THEY TRAVELED WITH TARKINA',
        'temo1_q'  => '"An unforgettable stay in Kessra. The welcome was incredible..."',
        'temo1_qf' => '"An unforgettable stay in Kessra. The family\'s welcome was amazing and the food... I\'ve never had such a good couscous!"',
        'temo1_role' => 'Traveler · Paris', 'temo1_tag' => '📍 Kessra, Siliana',
        'temo2_q'  => '"The local guide took me to magical places..."',
        'temo2_qf' => '"The local guide took me to places I\'d never have found alone. A genuine human experience, far from mass tourism."',
        'temo2_role' => 'Traveler · Lyon', 'temo2_tag' => '📍 Douz, Kébili',
        'temo3_q'  => '"The pottery workshop in Sejnane was a magical experience..."',
        'temo3_qf' => '"The pottery workshop in Sejnane was magical. The master artisan passed on an ancestral know-how with rare generosity."',
        'temo3_role' => 'Traveler · Montréal', 'temo3_tag' => '📍 Sejnane, Bizerte',
        'nl_badge' => 'Join 1,200+ travelers',
        'nl_title' => 'Ready for your next adventure?',
        'nl_sub' => 'Get our best offers, destination guides and exclusive tips straight to your inbox.',
        'nl_email_ph' => 'Your email address', 'nl_submit' => 'Subscribe',
        'nl_msg_invalid' => 'Please enter a valid email address.',
        'nl_msg_dup' => 'This email is already subscribed.',
        'nl_msg_err' => 'An error occurred. Please try again.',
        'nl_msg_ok' => 'Thank you for subscribing!',
    ],
];
$L = $L_ALL[$lang] ?? $L_ALL['fr'];

// ---------- Newsletter subscription (table auto-created so it works on any machine) ----------
$nlMsg = ''; $nlOk = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['newsletter_submit'])) {
    $nlEmail = trim((string) ($_POST['newsletter_email'] ?? ''));
    if ($nlEmail === '' || !filter_var($nlEmail, FILTER_VALIDATE_EMAIL)) {
        $nlMsg = $L['nl_msg_invalid'];
    } elseif (isset($conn) && $conn) {
        try {
            mysqli_query($conn, "CREATE TABLE IF NOT EXISTS newsletter (id INT AUTO_INCREMENT PRIMARY KEY, email VARCHAR(190) NOT NULL UNIQUE, created_at DATETIME DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            $already = false;
            if ($chk = mysqli_prepare($conn, "SELECT 1 FROM newsletter WHERE email = ? LIMIT 1")) {
                mysqli_stmt_bind_param($chk, 's', $nlEmail);
                mysqli_stmt_execute($chk);
                mysqli_stmt_store_result($chk);
                $already = mysqli_stmt_num_rows($chk) > 0;
                mysqli_stmt_close($chk);
            }
            if ($already) {
                $nlMsg = $L['nl_msg_dup'];
            } else {
                $ins = mysqli_prepare($conn, "INSERT INTO newsletter (email) VALUES (?)");
                mysqli_stmt_bind_param($ins, 's', $nlEmail);
                mysqli_stmt_execute($ins);
                mysqli_stmt_close($ins);
                $nlMsg = $L['nl_msg_ok'];
                $nlOk = true;
            }
        } catch (\mysqli_sql_exception $e) {
            $nlMsg = (mysqli_errno($conn) === 1062) ? $L['nl_msg_dup'] : $L['nl_msg_err'];
        }
    }
}

// ---------- Fetch regions ----------
$regions = [];
if (isset($conn) && $conn) {
    // Most popular regions = those with the most published services
    $res = mysqli_query($conn, "SELECT r.*,
        (SELECT COUNT(*) FROM hebergement WHERE region_id = r.id AND statut IN ('actif','publié')) +
        (SELECT COUNT(*) FROM repas WHERE region_id = r.id AND statut IN ('actif','publié')) +
        (SELECT COUNT(*) FROM guide WHERE region_id = r.id AND statut IN ('actif','publié')) +
        (SELECT COUNT(*) FROM evenement WHERE region_id = r.id AND statut IN ('actif','publié')) +
        (SELECT COUNT(*) FROM artisanat WHERE region_id = r.id AND statut IN ('actif','publié')) AS nb_services
        FROM region r ORDER BY nb_services DESC, r.nom ASC LIMIT 3");
    if ($res) { while ($row = mysqli_fetch_assoc($res)) { $regions[] = $row; } }
}

// ---------- Fetch guides for hosts section ----------
$guides = [];
if (isset($conn) && $conn) {
    $res2 = mysqli_query($conn, "SELECT g.*, r.nom as region_nom FROM guide g LEFT JOIN region r ON g.region_id = r.id WHERE g.statut IN ('actif','publié') LIMIT 6");
    if ($res2) { while ($row = mysqli_fetch_assoc($res2)) { $guides[] = $row; } }
}

// ---------- Fetch gallery images (admin-managed; auto-creates table so it works on any machine) ----------
$galleryImages = [];
if (isset($conn) && $conn) {
    try {
        mysqli_query($conn, "CREATE TABLE IF NOT EXISTS gallery_images (id INT AUTO_INCREMENT PRIMARY KEY, image_path VARCHAR(500) NOT NULL, alt_text VARCHAR(255) NOT NULL DEFAULT '', position INT NOT NULL DEFAULT 0, statut VARCHAR(20) NOT NULL DEFAULT 'actif', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
        $gres = mysqli_query($conn, "SELECT image_path, alt_text FROM gallery_images WHERE statut = 'actif' ORDER BY position ASC, id ASC LIMIT 6");
        if ($gres) { while ($g = mysqli_fetch_assoc($gres)) { $galleryImages[] = $g; } }
    } catch (\Throwable $e) { /* fall back to defaults below */ }
}
// Fallback (no admin-managed images yet)
if (empty($galleryImages)) {
    $galleryImages = [
        ['image_path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/73/Sidi_Bou_Said_-_TN.jpg/800px-Sidi_Bou_Said_-_TN.jpg', 'alt_text' => 'Sidi Bou Said'],
        ['image_path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/63/Kairouan_Great_Mosque.jpg/800px-Kairouan_Great_Mosque.jpg', 'alt_text' => 'Kairouan'],
        ['image_path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2d/Desert_-_Tunisia_%28Rades%29.jpg/800px-Desert_-_Tunisia_%28Rades%29.jpg', 'alt_text' => 'Sahara'],
        ['image_path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6e/Houmt_Souk_port.jpg/800px-Houmt_Souk_port.jpg', 'alt_text' => 'Djerba'],
        ['image_path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6c/Matmata_cave_houses.jpg/800px-Matmata_cave_houses.jpg', 'alt_text' => 'Matmata'],
        ['image_path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5f/ElJem_amphitheatre.jpg/800px-ElJem_amphitheatre.jpg', 'alt_text' => 'El Jem'],
    ];
}

// ---------- Fetch admin-managed packs (auto-creates tables) ----------
$dbPacks = [];
if (isset($conn) && $conn) {
    try {
        mysqli_query($conn, "CREATE TABLE IF NOT EXISTS packs (id INT AUTO_INCREMENT PRIMARY KEY, titre VARCHAR(255) NOT NULL, slogan VARCHAR(500) NOT NULL DEFAULT '', region_id INT NOT NULL, image_path VARCHAR(500) NOT NULL DEFAULT '', prix_original DECIMAL(10,2) NOT NULL DEFAULT 0, prix_final DECIMAL(10,2) NOT NULL DEFAULT 0, statut VARCHAR(20) NOT NULL DEFAULT 'actif', position INT NOT NULL DEFAULT 0, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
        mysqli_query($conn, "CREATE TABLE IF NOT EXISTS pack_services (id INT AUTO_INCREMENT PRIMARY KEY, pack_id INT NOT NULL, service_type VARCHAR(20) NOT NULL, service_id INT NOT NULL, INDEX idx_pack (pack_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
        $pres = mysqli_query($conn, "SELECT p.id, p.titre, p.slogan, p.region_id, p.image_path, p.prix_original, p.prix_final, r.nom AS region_nom FROM packs p LEFT JOIN region r ON p.region_id = r.id WHERE p.statut = 'actif' ORDER BY p.position ASC, p.id DESC LIMIT 3");
        if ($pres) {
            while ($p = mysqli_fetch_assoc($pres)) { $p['services'] = []; $dbPacks[(int) $p['id']] = $p; }
        }
        if (!empty($dbPacks)) {
            $ids = implode(',', array_map('intval', array_keys($dbPacks)));
            $svcRes = mysqli_query($conn, "SELECT pack_id, service_type, service_id FROM pack_services WHERE pack_id IN ($ids)");
            $allowedSvcTypes = ['hebergement','repas','guide','evenement','artisanat'];
            while ($svcRes && ($row = mysqli_fetch_assoc($svcRes))) {
                $stype = $row['service_type']; $sid = (int) $row['service_id'];
                if (!in_array($stype, $allowedSvcTypes, true)) continue;
                $titreSt = mysqli_prepare($conn, "SELECT titre FROM `$stype` WHERE id = ? LIMIT 1");
                mysqli_stmt_bind_param($titreSt, 'i', $sid);
                mysqli_stmt_execute($titreSt);
                mysqli_stmt_bind_result($titreSt, $stitre);
                if (mysqli_stmt_fetch($titreSt)) {
                    $dbPacks[(int) $row['pack_id']]['services'][] = ['type' => $stype, 'titre' => $stitre];
                }
                mysqli_stmt_close($titreSt);
            }
        }
    } catch (\Throwable $e) { $dbPacks = []; }
}

// Flatpickr locale (uses ar for Arabic, fr for French, default for English)
$fpLocale = ($lang === 'ar') ? 'ar' : (($lang === 'en') ? 'default' : 'fr');
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>" dir="<?= htmlspecialchars($dir) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($L['page_title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="assets/css/rtl.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <?php if ($lang === 'fr'): ?><script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script><?php endif; ?>
    <?php if ($lang === 'ar'): ?><script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ar.js"></script><?php endif; ?>
    <style>
        :root { --primary:#f16e22; --navy:#0b1c30; --light-bg:#FFFFFF; --text-dark:#1a1a1a; --text-muted:#6b7280; --border:#e5e7eb; }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Segoe UI',system-ui,sans-serif; background:#fff; color:var(--text-dark); }

        /* ── HERO ── */
        .hero { min-height:85vh; background-image: url('images/hero-tunisia.jpg'); background-size: cover; background-position: center; position:relative; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:80px 40px 0 40px; }
        .hero > *:not(.hero-overlay) { position:relative; z-index:1; }
        .hero-title { font-size:clamp(2rem,5vw,4rem); font-weight:900; color:#fff; line-height:1.05; letter-spacing:-1px; margin-bottom:40px; font-family:Georgia,'Times New Roman',serif; text-shadow:0 2px 20px rgba(0,0,0,.3); }
        .hero-pills { display:flex; gap:10px; flex-wrap:wrap; justify-content:center; margin-bottom:28px; margin-top:24px; }
        .hero-pill { padding:8px 18px; border:1px solid #ddd; border-radius:50px; color:#0b1c30; text-decoration:none; font-size:.85rem; font-weight:600; display:flex; align-items:center; gap:6px; transition:all .2s; background:#f5f5f5; }
        .hero-pill:hover, .hero-pill.active { background:#fff; color:var(--primary); border-color:var(--primary); }
        .booking-bar { background:#fff; border-radius:18px; display:flex; align-items:stretch; width:100%; max-width:940px; box-shadow:0 14px 44px rgba(0,0,0,.22); overflow:hidden; }
        .booking-field { flex:1; padding:13px 22px; border-right:1px solid #eee; display:flex; flex-direction:column; justify-content:center; cursor:pointer; transition:background .15s; min-width:0; text-align:left; }
        .booking-field:hover { background:#FFFFFF; }
        .booking-field.dest { flex:1.4; }
        .booking-field label { display:block; font-size:.66rem; font-weight:800; letter-spacing:1.2px; text-transform:uppercase; color:#9aa3ab; margin-bottom:4px; pointer-events:none; }
        .bf-control { display:flex; align-items:center; gap:8px; position:relative; }
        .bf-ico { width:15px; height:15px; flex-shrink:0; pointer-events:none; }
        .booking-field.dest select { padding-right:18px; }
        .booking-field input, .booking-field select { width:100%; border:none; outline:none; background:transparent; font-size:.93rem; color:#0b1c30; font-family:inherit; font-weight:600; cursor:pointer; padding:0; appearance:none; -webkit-appearance:none; -moz-appearance:none; text-overflow:ellipsis; }
        .booking-field input::placeholder { color:#aab1b8; font-weight:500; }
        .booking-caret { width:13px; height:13px; position:absolute; right:0; top:50%; transform:translateY(-50%); pointer-events:none; }
        .booking-btn { flex-shrink:0; width:62px; background:var(--primary); display:flex; align-items:center; justify-content:center; border:none; cursor:pointer; transition:background .2s; }
        .booking-btn:hover { background:#d95716; }
        .booking-btn svg { width:22px; height:22px; stroke:#fff; }
        @media(max-width:760px){ .booking-bar{ flex-direction:column; max-width:420px; } .booking-field{ border-right:none; border-bottom:1px solid #eee; } .booking-btn{ width:100%; height:52px; } }

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
        .regions-outer { background:#fbf9f5; padding:80px 0; }
        .regions-section { padding:0 60px; max-width:1200px; margin:0 auto; }
        .regions-header { display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:40px; }
        .regions-header .section-label { color:var(--primary); }
        .regions-header .section-heading { color:var(--navy); }
        .link-all { color:var(--primary); text-decoration:none; font-weight:600; font-size:.92rem; }
        .link-all:hover { text-decoration:underline; }
        /* Slider for regions */
        .regions-slider { width:100%; height:280px; overflow:hidden; mask-image:linear-gradient(to right, transparent, #000 8% 92%, transparent); }
        .regions-slider .rs-list { display:flex; width:max-content; animation:rsMarquee 25s linear infinite; }
        .regions-slider:hover .rs-list { animation-play-state:paused; }
        .regions-slider .rs-item { width:300px; height:280px; flex-shrink:0; transition:filter .4s; }
        @keyframes rsMarquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .regions-slider:hover .rs-item { filter:grayscale(.6); }
        .regions-slider .rs-item:hover { filter:grayscale(0) !important; }
        .region-card { border-radius:16px; overflow:hidden; text-decoration:none; color:inherit; display:block; box-shadow:0 10px 30px rgba(11,28,48,0.06); transition:transform .2s,box-shadow .2s; position:relative; width:280px; height:260px; margin:0 10px; }
        .region-card:hover { transform:translateY(-5px); box-shadow:0 12px 40px rgba(11,28,48,0.12); }
        .region-card img { width:100%; height:180px; object-fit:cover; display:block; }
        .region-card-body { padding:14px 16px; background:#fff; border-top:2px solid var(--primary); }
        .region-card-badge { position:absolute; top:12px; left:12px; background:var(--primary); color:#fff; padding:3px 10px; border-radius:50px; font-size:.72rem; font-weight:700; }
        .region-card-name { font-size:1rem; font-weight:700; margin-bottom:4px; color:var(--navy); }
        .region-card-desc { font-size:.78rem; color:rgba(11,28,48,.6); line-height:1.4; margin-bottom:6px; display:none; }
        .region-card-meta { display:flex; align-items:center; gap:8px; font-size:.75rem; color:rgba(11,28,48,.6); }
        /* Static grid fallback for non-slider view */
        .regions-grid-static { display:grid; grid-template-columns:repeat(3,1fr); gap:24px; }

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
        .forfait-includes { list-style:none; margin-bottom:20px; }
        .forfait-includes li { display:flex; align-items:center; gap:8px; font-size:.88rem; color:var(--text-muted); margin-bottom:8px; }
        .forfait-includes li::before { content:'✓'; color:#059669; font-weight:700; flex-shrink:0; }
        .btn-forfait { display:block; width:100%; padding:13px; background:var(--primary); color:#fff; border:none; border-radius:50px; font-size:.95rem; font-weight:700; cursor:pointer; text-align:center; text-decoration:none; transition:background .2s; }
        .btn-forfait:hover { background:#d95716; }

        /* ── GALLERY ── */
        .gallery-section { padding:80px 0; background:#fbf9f5; }
        .gallery-section-inner { max-width:1200px; margin:0 auto; padding:0 60px; }
        .gallery-title { text-align:center; font-size:2rem; font-weight:800; letter-spacing:2px; margin-bottom:8px; text-transform:uppercase; color:var(--navy); }
        .gallery-underline { width:60px; height:3px; background:var(--primary); margin:0 auto 40px; }
        /* Gallery auto-slider */
        .gallery-slider { width:100%; height:220px; overflow:hidden; mask-image:linear-gradient(to right, transparent, #000 8% 92%, transparent); margin-bottom:16px; }
        .gallery-slider .gs-list { display:flex; width:max-content; animation:gsMarquee 20s linear infinite; }
        .gallery-slider:hover .gs-list { animation-play-state:paused; }
        .gallery-slider .gs-item { width:220px; height:220px; flex-shrink:0; padding:0 8px; transition:filter .4s; }
        @keyframes gsMarquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .gallery-slider:hover .gs-item { filter:grayscale(.5); }
        .gallery-slider .gs-item:hover { filter:grayscale(0) !important; }
        .gallery-slider img { width:100%; height:220px; object-fit:cover; border-radius:14px; cursor:pointer; transition:transform .3s; }
        .gallery-slider img:hover { transform:scale(1.04); }
        .gallery-cta { text-align:center; margin-top:32px; }
        .btn-gallery { display:inline-block; padding:12px 32px; border:2px solid var(--primary); color:var(--primary); border-radius:50px; text-decoration:none; font-weight:700; font-size:.95rem; transition:all .2s; }
        .btn-gallery:hover { background:var(--primary); color:#fff; }

        /* ── EXPERIENCES ── */
        .experiences-section { padding:80px 60px; background:#F7F7F7; }
        .experiences-inner { max-width:1200px; margin:0 auto; }
        .exp-grid { display:grid; grid-template-columns:repeat(5,1fr); gap:16px; }
        .exp-card { background:#fff; border:1px solid var(--border); border-radius:16px; padding:24px 20px; text-decoration:none; color:inherit; display:flex; flex-direction:column; gap:10px; transition:all .2s; position:relative; }
        .exp-card:hover { border-color:var(--primary); box-shadow:0 8px 30px rgba(241, 110, 34,.1); transform:translateY(-3px); }
        .exp-icon { font-size:1.8rem; }
        .exp-adj { font-size:.72rem; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; color:var(--primary); }
        .exp-text strong { display:block; font-size:.95rem; font-weight:700; margin:2px 0; }
        .exp-text p { font-size:.8rem; color:var(--text-muted); line-height:1.5; }
        .exp-arrow { position:absolute; bottom:16px; right:16px; font-size:1rem; color:var(--primary); font-weight:700; opacity:0; transition:opacity .2s; }
        .exp-card:hover .exp-arrow { opacity:1; }

        /* ── POURQUOI TARKINA ── */
        .pourquoi-section { padding:80px 60px; background:var(--navy); }
        .pourquoi-inner { max-width:1200px; margin:0 auto; text-align:center; }
        .pourquoi-section .section-heading { color:#fff !important; }
        .pourquoi-section .section-sub { color:rgba(255,255,255,.7) !important; }
        .pourquoi-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:24px; margin-top:48px; }
        .pourquoi-card { background:#fcfaf6; border-radius:16px; padding:32px 24px; text-align:center; border:1px solid rgba(11,28,48,0.05); transition:box-shadow .2s,transform .2s; color:var(--navy); }
        .pourquoi-card:hover { box-shadow:0 12px 40px rgba(0,0,0,0.25); transform:translateY(-5px); border-color:var(--primary); }
        .pourquoi-icon { width:56px; height:56px; border-radius:50%; background:rgba(241, 110, 34, 0.1); display:flex; align-items:center; justify-content:center; margin:0 auto 16px; }
        .pourquoi-icon svg { width:24px; height:24px; stroke:var(--primary); fill:none; stroke-width:1.8; }
        .pourquoi-card h3 { font-size:1rem; font-weight:700; margin-bottom:10px; color:var(--navy) !important; }
        .pourquoi-card p { font-size:.85rem; color:rgba(11,28,48,0.7) !important; line-height:1.65; }

        /* ── TESTIMONIALS ── */
        .temoignages-section { background:#fbf9f5; padding:80px 60px; text-align:center; }
        .temoignages-inner { max-width:1100px; margin:0 auto; }
        .temo-label { font-size:.75rem; font-weight:700; letter-spacing:2.5px; color:var(--primary); text-transform:uppercase; margin-bottom:12px; }
        .temo-heading { font-size:2rem; font-weight:800; color:var(--navy); margin-bottom:48px; letter-spacing:1px; }
        .temo-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:24px; }
        .temo-card { position:relative; background:#fff; border:1px solid rgba(11,28,48,0.08); border-radius:16px; padding:28px 24px; text-align:left; cursor:pointer; transition:transform .2s,border-color .2s; box-shadow: 0 10px 35px rgba(11,28,48,0.04); }
        .temo-card:hover { transform:translateY(-4px); border-color:rgba(241, 110, 34,.5); }
        .temo-avatar { width:52px; height:52px; border-radius:50%; overflow:hidden; margin-bottom:16px; border:2px solid rgba(11,28,48,.1); }
        .temo-avatar img { width:100%; height:100%; object-fit:cover; }
        .temo-quote { color:#4b5563; font-size:.92rem; line-height:1.6; font-style:italic; margin-bottom:14px; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden; }
        .temo-name { color:var(--navy); font-weight:700; font-size:.9rem; }
        .temo-city { color:rgba(11,28,48,.6); font-size:.8rem; margin-bottom:10px; }
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
        .newsletter-form button:hover { background:#d95716; }

        /* ── STATS BAR ── */
        .stats-bar-section { background:var(--navy) !important; padding:60px 0; border-top:1px solid rgba(255,255,255,0.1); border-bottom:1px solid rgba(255,255,255,0.1); }
        .stat-item { display:flex; flex-direction:column; align-items:center; text-align:center; }
        .stat-number { font-size:2.8rem; font-weight:800; color:#f16e22; line-height:1.1; margin-bottom:6px; font-family:'Outfit','Playfair Display',sans-serif; }
        .stat-label { font-size:.95rem; font-weight:700; color:rgba(255,255,255,0.85); text-transform:uppercase; letter-spacing:.5px; font-family:'Outfit',sans-serif; }
        .fade-in-up { opacity:0; transform:translateY(30px); transition:opacity .6s ease, transform .6s ease; }
        .fade-in-up.visible { opacity:1; transform:translateY(0); }
        .pourquoi-grid .pourquoi-card:nth-child(1) { transition-delay:0s; }
        .pourquoi-grid .pourquoi-card:nth-child(2) { transition-delay:.1s; }
        .pourquoi-grid .pourquoi-card:nth-child(3) { transition-delay:.2s; }
        .pourquoi-grid .pourquoi-card:nth-child(4) { transition-delay:.3s; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<!-- HERO -->
<section class="hero">
    <div class="hero-overlay" style="position:absolute;inset:0;background:rgba(0,0,0,0.45);z-index:0;"></div>
    <h1 class="hero-title" style="margin-bottom:16px;"><?= $L['hero_full'] ?></h1>
    <p style="color:#fff; font-size:1.1rem; font-weight:300; margin-bottom:40px;"><?= htmlspecialchars($L['hero_sub']) ?></p>

    <form class="booking-bar" action="search.php" method="GET" id="bookingBar">
        <div class="booking-field dest">
            <label><?= htmlspecialchars($L['bk_destination']) ?></label>
            <div class="bf-control">
                <svg class="bf-ico" viewBox="0 0 24 24" fill="none" stroke="#f16e22" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <select name="destination" id="bk_dest" aria-label="<?= htmlspecialchars($L['bk_destination']) ?>">
                    <option value=""><?= htmlspecialchars($L['bk_where']) ?></option>
                    <?php
                    if (isset($conn) && $conn) {
                        $reg_q = mysqli_query($conn, "SELECT id, nom FROM region ORDER BY nom ASC");
                        if ($reg_q) { while ($reg_row = mysqli_fetch_assoc($reg_q)): ?>
                            <option value="<?= (int) $reg_row['id'] ?>"><?= htmlspecialchars($reg_row['nom'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endwhile; }
                    } ?>
                </select>
                <svg class="booking-caret" viewBox="0 0 24 24" fill="none" stroke="#9aa3ab" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
        </div>
        <div class="booking-field sf-field">
            <label><?= htmlspecialchars($L['bk_arrival']) ?></label>
            <div class="bf-control">
                <svg class="bf-ico" viewBox="0 0 24 24" fill="none" stroke="#f16e22" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <input type="text" name="date_debut" id="date_debut" placeholder="<?= htmlspecialchars($L['bk_add_date']) ?>" readonly>
            </div>
        </div>
        <div class="booking-field sf-field">
            <label><?= htmlspecialchars($L['bk_departure']) ?></label>
            <div class="bf-control">
                <svg class="bf-ico" viewBox="0 0 24 24" fill="none" stroke="#f16e22" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <input type="text" name="date_fin" id="date_fin" placeholder="<?= htmlspecialchars($L['bk_add_date']) ?>" readonly>
            </div>
        </div>
        <div class="booking-field">
            <label><?= htmlspecialchars($L['bk_travelers']) ?></label>
            <div class="bf-control">
                <svg class="bf-ico" viewBox="0 0 24 24" fill="none" stroke="#f16e22" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                <input type="number" name="personnes" id="personnes" value="1" min="1" max="20">
            </div>
        </div>
        <button type="submit" class="booking-btn" aria-label="<?= htmlspecialchars($L['bk_search_title']) ?>" title="<?= htmlspecialchars($L['bk_search_title']) ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </button>
    </form>

    <div style="display:flex;gap:16px;flex-wrap:wrap;justify-content:center;margin-top:16px;">
        <a href="explorer.php" style="display:inline-block;padding:14px 32px;background:#f16e22;color:#fff;border-radius:50px;text-decoration:none;font-weight:700;font-size:1rem;box-shadow:0 6px 24px rgba(241,110,34,.4);transition:background .2s;" onmouseover="this.style.background='#d95716'" onmouseout="this.style.background='#f16e22'"><?= htmlspecialchars($L['cta_explore_regions']) ?></a>
        <a href="blogs.php" style="display:inline-block;padding:14px 32px;background:transparent;color:#fff;border:2px solid rgba(255,255,255,.85);border-radius:50px;text-decoration:none;font-weight:700;font-size:1rem;transition:all .2s;" onmouseover="this.style.background='#fff';this.style.color='#0b1c30'" onmouseout="this.style.background='transparent';this.style.color='#fff'"><?= htmlspecialchars($L['cta_discover_blog']) ?></a>
    </div>
</section>

<!-- TRUST BAR -->
<section class="trust-bar">
    <div class="trust-bar-inner">
        <div class="trust-item">
            <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="#f16e22" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div><strong><?= htmlspecialchars($L['trust1_t']) ?></strong><span><?= htmlspecialchars($L['trust1_s']) ?></span></div>
        </div>
        <div class="trust-item">
            <svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
            <div><strong><?= htmlspecialchars($L['trust2_t']) ?></strong><span><?= htmlspecialchars($L['trust2_s']) ?></span></div>
        </div>
        <div class="trust-item">
            <svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
            <div><strong><?= htmlspecialchars($L['trust3_t']) ?></strong><span><?= htmlspecialchars($L['trust3_s']) ?></span></div>
        </div>
        <div class="trust-item">
            <svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
            <div><strong><?= htmlspecialchars($L['trust4_t']) ?></strong><span><?= htmlspecialchars($L['trust4_s']) ?></span></div>
        </div>
    </div>
</section>

<div class="hero-pills" style="margin-top:40px;">
    <a href="search.php?type=hebergement" class="hero-pill"><?= htmlspecialchars($L['pill_heb']) ?></a>
    <a href="search.php?type=repas" class="hero-pill"><?= htmlspecialchars($L['pill_repas']) ?></a>
    <a href="search.php?type=guide" class="hero-pill"><?= htmlspecialchars($L['pill_guide']) ?></a>
    <a href="search.php?type=artisanat" class="hero-pill"><?= htmlspecialchars($L['pill_art']) ?></a>
    <a href="search.php?type=evenement" class="hero-pill"><?= htmlspecialchars($L['pill_event']) ?></a>
</div>

<!-- REGIONS -->
<section class="regions-outer">
    <div class="regions-section">
        <div class="regions-header">
            <div>
                <p class="section-label"><?= htmlspecialchars($L['regs_label']) ?></p>
                <h2 class="section-heading"><?= htmlspecialchars($L['regs_h_pre']) ?><span style="color:var(--primary)"><?= htmlspecialchars($L['regs_h_em']) ?></span></h2>
            </div>
            <a href="explorer.php" class="link-all"><?= htmlspecialchars($L['regs_see_all']) ?></a>
        </div>
    </div>
    <!-- Auto-scrolling regions slider -->
    <?php
    $allRegionsSlider = [];
    if (isset($conn) && $conn) {
        $rall = mysqli_query($conn, "SELECT r.*, (SELECT COUNT(*) FROM hebergement WHERE region_id = r.id AND statut IN ('actif','publié')) + (SELECT COUNT(*) FROM repas WHERE region_id = r.id AND statut IN ('actif','publié')) + (SELECT COUNT(*) FROM guide WHERE region_id = r.id AND statut IN ('actif','publié')) AS nb_services FROM region r ORDER BY r.nom ASC LIMIT 9");
        if ($rall) { while ($rr = mysqli_fetch_assoc($rall)) { $allRegionsSlider[] = $rr; } }
    }
    if (empty($allRegionsSlider)) { $allRegionsSlider = $regions; }
    // Pad to at least 6 items for the slider to look good
    while (count($allRegionsSlider) < 6) { $allRegionsSlider = array_merge($allRegionsSlider, $allRegionsSlider); }
    $sliderRegions = array_slice($allRegionsSlider, 0, 9);
    $sliderCount = count($sliderRegions);
    ?>
    <div class="regions-slider">
        <div class="rs-list">
        <?php for ($loop = 0; $loop < 2; $loop++): ?>
            <?php foreach ($sliderRegions as $ri => $region):
                $photo = '';
                if (!empty($region['photo_principale'])) {
                    $p = $region['photo_principale'];
                    if (strpos($p, 'http') === 0 || strpos($p, 'uploads/') === 0 || strpos($p, 'images/') === 0 || strpos($p, 'assets/') === 0) { $photo = $p; }
                    elseif (file_exists('uploads/regions/' . $p)) { $photo = 'uploads/regions/' . $p; }
                    elseif (file_exists('images/regions/' . $p)) { $photo = 'images/regions/' . $p; }
                    elseif (file_exists('uploads/' . $p)) { $photo = 'uploads/' . $p; }
                }
                if (empty($photo)) { $photo = 'https://images.unsplash.com/photo-1539635278303-d4002c07eae3?w=600&q=80'; }
                $nbServices = (int)($region['nb_services'] ?? 0);
                $svcWord = $nbServices === 1 ? $L['regs_service'] : $L['regs_services'];
            ?>
                <div class="rs-item">
                    <a href="region.php?id=<?= $region['id'] ?>" class="region-card">
                        <img loading="lazy" src="<?= $photo ?>" alt="<?= htmlspecialchars($region['nom'], ENT_QUOTES, 'UTF-8') ?>">
                        <div class="region-card-badge"><?= $nbServices ?> <?= htmlspecialchars($svcWord) ?></div>
                        <div class="region-card-body">
                            <p class="region-card-name"><?= htmlspecialchars($region['nom']) ?></p>
                            <div class="region-card-meta">☀️ <?= htmlspecialchars($region['meilleure_saison'] ?? '') ?></div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endfor; ?>
        </div>
    </div>
    <div style="text-align:center;margin-top:32px;">
        <a href="explorer.php" style="display:inline-block;padding:12px 32px;border:2px solid var(--primary);color:var(--primary);border-radius:50px;text-decoration:none;font-weight:700;font-size:.95rem;transition:all .2s;" onmouseover="this.style.background='#f16e22';this.style.color='#fff'" onmouseout="this.style.background='transparent';this.style.color='#f16e22'"><?= htmlspecialchars($L['regs_see_all']) ?></a>
    </div>
</section>

<!-- FORFAITS POPULAIRES -->
<section class="forfaits-section">
    <div class="forfaits-inner">
        <div style="text-align:center;">
            <p class="section-label"><?= htmlspecialchars($L['pk_label']) ?></p>
            <h2 class="section-heading"><?= htmlspecialchars($L['pk_h_pre']) ?><span style="color:var(--primary)"><?= htmlspecialchars($L['pk_h_em']) ?></span></h2>
            <p class="section-sub"><?= htmlspecialchars($L['pk_sub']) ?></p>
        </div>
        <div class="forfaits-grid">
            <?php if (!empty($dbPacks)): ?>
                <?php foreach ($dbPacks as $pk):
                    $pkImg = $pk['image_path'];
                    if ($pkImg && strpos($pkImg, 'http') !== 0 && strpos($pkImg, 'uploads/') !== 0 && strpos($pkImg, 'images/') !== 0) {
                        $pkImg = 'uploads/packs/' . ltrim($pkImg, '/');
                    }
                    if (!$pkImg) $pkImg = 'https://images.unsplash.com/photo-1509316785289-025f5b846b35?w=800&fit=crop';
                    $pkPrixO = (float) $pk['prix_original'];
                    $pkPrixF = (float) $pk['prix_final'];
                    $hasDiscount = $pkPrixO > $pkPrixF && $pkPrixO > 0;
                ?>
                <div class="forfait-card animate-up">
                    <div class="forfait-img-wrap">
                        <img loading="lazy" src="<?= htmlspecialchars($pkImg) ?>" alt="<?= htmlspecialchars($pk['titre']) ?>" onerror="this.src='assets/img/placeholder.jpg'">
                        <span class="forfait-price-badge">
                            <?= htmlspecialchars($L['pk_from']) ?> <?= number_format($pkPrixF, 0) ?> DT
                            <?php if ($hasDiscount): ?>
                                <small style="text-decoration:line-through; color:#aaa; font-weight:500; margin-left:6px;"><?= number_format($pkPrixO, 0) ?></small>
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="forfait-body">
                        <p class="forfait-meta">📍 <?= htmlspecialchars($pk['region_nom'] ?: '') ?> · <?= count($pk['services']) ?> services</p>
                        <h3 class="forfait-title"><?= htmlspecialchars($pk['titre']) ?></h3>
                        <?php if (!empty($pk['slogan'])): ?>
                            <p style="font-size:.86rem; color:var(--text-muted); margin: 0 0 12px; line-height:1.5;"><?= htmlspecialchars($pk['slogan']) ?></p>
                        <?php endif; ?>
                        <ul class="forfait-includes">
                            <?php foreach ($pk['services'] as $sv): ?>
                                <li><?= htmlspecialchars($sv['titre']) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <a href="forfait.php?id=<?= (int) $pk['id'] ?>" class="btn-forfait"><?= htmlspecialchars($L['pk_book']) ?></a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Default packs (shown until admin creates packs in /admin/packs.php) -->
                <div class="forfait-card animate-up">
                    <div class="forfait-img-wrap">
                        <img loading="lazy" src="https://images.unsplash.com/photo-1509316785289-025f5b846b35?w=800&fit=crop" alt="<?= htmlspecialchars($L['pk1_title']) ?>" onerror="this.src='assets/img/placeholder.jpg'">
                        <span class="forfait-price-badge"><?= htmlspecialchars($L['pk_from']) ?> 350 DT</span>
                    </div>
                    <div class="forfait-body">
                        <p class="forfait-meta"><?= htmlspecialchars($L['pk1_loc']) ?></p>
                        <h3 class="forfait-title"><?= htmlspecialchars($L['pk1_title']) ?></h3>
                        <ul class="forfait-includes">
                            <li><?= htmlspecialchars($L['pk1_i1']) ?></li>
                            <li><?= htmlspecialchars($L['pk1_i2']) ?></li>
                            <li><?= htmlspecialchars($L['pk1_i3']) ?></li>
                            <li><?= htmlspecialchars($L['pk1_i4']) ?></li>
                        </ul>
                        <a href="region.php?id=5" class="btn-forfait"><?= htmlspecialchars($L['pk_book']) ?></a>
                    </div>
                </div>
                <div class="forfait-card animate-up">
                    <div class="forfait-img-wrap">
                        <img loading="lazy" src="https://images.unsplash.com/photo-1561625116-5f8675632053?w=800&fit=crop" alt="<?= htmlspecialchars($L['pk2_title']) ?>" onerror="this.src='assets/img/placeholder.jpg'">
                        <span class="forfait-price-badge"><?= htmlspecialchars($L['pk_from']) ?> 220 DT</span>
                    </div>
                    <div class="forfait-body">
                        <p class="forfait-meta"><?= htmlspecialchars($L['pk2_loc']) ?></p>
                        <h3 class="forfait-title"><?= htmlspecialchars($L['pk2_title']) ?></h3>
                        <ul class="forfait-includes">
                            <li><?= htmlspecialchars($L['pk2_i1']) ?></li>
                            <li><?= htmlspecialchars($L['pk2_i2']) ?></li>
                            <li><?= htmlspecialchars($L['pk2_i3']) ?></li>
                        </ul>
                        <a href="region.php?id=4" class="btn-forfait"><?= htmlspecialchars($L['pk_book']) ?></a>
                    </div>
                </div>
                <div class="forfait-card">
                    <div class="forfait-img-wrap">
                        <img loading="lazy" src="https://images.unsplash.com/photo-1539650116574-75c0c6d73f6e?w=800&fit=crop" alt="<?= htmlspecialchars($L['pk3_title']) ?>" onerror="this.src='assets/img/placeholder.jpg'">
                        <span class="forfait-price-badge"><?= htmlspecialchars($L['pk_from']) ?> 480 DT</span>
                    </div>
                    <div class="forfait-body">
                        <p class="forfait-meta"><?= htmlspecialchars($L['pk3_loc']) ?></p>
                        <h3 class="forfait-title"><?= htmlspecialchars($L['pk3_title']) ?></h3>
                        <ul class="forfait-includes">
                            <li><?= htmlspecialchars($L['pk3_i1']) ?></li>
                            <li><?= htmlspecialchars($L['pk3_i2']) ?></li>
                            <li><?= htmlspecialchars($L['pk3_i3']) ?></li>
                            <li><?= htmlspecialchars($L['pk3_i4']) ?></li>
                        </ul>
                        <a href="region.php?id=3" class="btn-forfait"><?= htmlspecialchars($L['pk_book']) ?></a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- STATISTICS BAR -->
<section class="stats-bar-section">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16" style="color:#f16e22;margin-bottom:12px;"><path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724C2.3 10.634 3.227 10 4.92 10M1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4"/></svg>
                    <div class="stat-number" data-target="1200" data-suffix="+">1 200+</div>
                    <div class="stat-label"><?= htmlspecialchars($L['stat_trav']) ?></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16" style="color:#f16e22;margin-bottom:12px;"><path d="M8 16.016a8 8 0 1 1 0-16 8 8 0 0 1 0 16.016M8 1.406a6.594 6.594 0 1 0 0 13.188 6.594 6.594 0 0 0 0-13.188"/><path d="M7.682 3.602a.5.5 0 0 1 .636 0l4.92 4.223a.5.5 0 0 1-.177.837l-5.148 1.716a.5.5 0 0 1-.63-.63L8.99 4.6l-1.308-1a.5.5 0 0 1 0-.798z"/></svg>
                    <div class="stat-number" data-target="8" data-suffix="">8</div>
                    <div class="stat-label"><?= htmlspecialchars($L['stat_regs']) ?></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16" style="color:#f16e22;margin-bottom:12px;"><path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L2 8.207V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V8.207l.646.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293zM13 7.207V13.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V7.207l5-5z"/></svg>
                    <div class="stat-number" data-target="50" data-suffix="+">50+</div>
                    <div class="stat-label"><?= htmlspecialchars($L['stat_lodge']) ?></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16" style="color:#f16e22;margin-bottom:12px;"><path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957-2.88-2.748 4.013-.57 1.802-3.663 1.802 3.664 4.014.568-2.88 2.749.694 3.958z"/></svg>
                    <div class="stat-number" data-target="4.8" data-decimals="1" data-suffix="/5">4.8/5</div>
                    <div class="stat-label"><?= htmlspecialchars($L['stat_sat']) ?></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- GALLERY -->
<section class="gallery-section">
    <div class="gallery-section-inner">
        <h2 class="gallery-title"><?= htmlspecialchars($L['gal_title']) ?></h2>
        <div class="gallery-underline"></div>
        <!-- Auto-scrolling gallery slider -->
        <div class="gallery-slider">
            <div class="gs-list">
            <?php for ($loop = 0; $loop < 2; $loop++): ?>
                <?php foreach ($galleryImages as $gi => $gimg):
                    $gsrc = $gimg['image_path'];
                    if ($gsrc && strpos($gsrc, 'http') !== 0 && strpos($gsrc, 'uploads/') !== 0 && strpos($gsrc, 'images/') !== 0) {
                        $gsrc = 'uploads/gallery/' . ltrim($gsrc, '/');
                    }
                ?>
                <div class="gs-item">
                    <img loading="lazy" src="<?= htmlspecialchars($gsrc) ?>" alt="<?= htmlspecialchars($gimg['alt_text']) ?>" onerror="this.src='https://images.unsplash.com/photo-1509316785289-025f5b846b35?w=800&fit=crop'">
                </div>
                <?php endforeach; ?>
            <?php endfor; ?>
            </div>
        </div>
        <div class="gallery-cta">
            <a href="explorer.php" class="btn-gallery"><?= htmlspecialchars($L['gal_cta']) ?></a>
        </div>
    </div>
</section>

<!-- EXPERIENCES -->
<section class="experiences-section">
    <div class="experiences-inner">
        <p class="section-label"><?= htmlspecialchars($L['exp_label']) ?></p>
        <h2 class="section-heading"><?= htmlspecialchars($L['exp_h_pre']) ?><span style="color:var(--primary)"><?= htmlspecialchars($L['exp_h_em']) ?></span></h2>
        <p class="section-sub"><?= htmlspecialchars($L['exp_sub']) ?></p>
        <div class="exp-grid">
            <a href="search.php?type=hebergement" class="exp-card animate-up">
                <div class="exp-icon">🏠</div>
                <div class="exp-text"><span class="exp-adj"><?= htmlspecialchars($L['exp1_adj']) ?></span><strong><?= htmlspecialchars($L['exp1_t']) ?></strong><p><?= htmlspecialchars($L['exp1_d']) ?></p></div>
                <span class="exp-arrow">→</span>
            </a>
            <a href="search.php?type=repas" class="exp-card animate-up">
                <div class="exp-icon">🍽️</div>
                <div class="exp-text"><span class="exp-adj"><?= htmlspecialchars($L['exp2_adj']) ?></span><strong><?= htmlspecialchars($L['exp2_t']) ?></strong><p><?= htmlspecialchars($L['exp2_d']) ?></p></div>
                <span class="exp-arrow">→</span>
            </a>
            <a href="search.php?type=guide" class="exp-card animate-up">
                <div class="exp-icon">🧭</div>
                <div class="exp-text"><span class="exp-adj"><?= htmlspecialchars($L['exp3_adj']) ?></span><strong><?= htmlspecialchars($L['exp3_t']) ?></strong><p><?= htmlspecialchars($L['exp3_d']) ?></p></div>
                <span class="exp-arrow">→</span>
            </a>
            <a href="search.php?type=artisanat" class="exp-card animate-up">
                <div class="exp-icon">🏺</div>
                <div class="exp-text"><span class="exp-adj"><?= htmlspecialchars($L['exp4_adj']) ?></span><strong><?= htmlspecialchars($L['exp4_t']) ?></strong><p><?= htmlspecialchars($L['exp4_d']) ?></p></div>
                <span class="exp-arrow">→</span>
            </a>
            <a href="search.php?type=evenement" class="exp-card animate-up">
                <div class="exp-icon">🎉</div>
                <div class="exp-text"><span class="exp-adj"><?= htmlspecialchars($L['exp5_adj']) ?></span><strong><?= htmlspecialchars($L['exp5_t']) ?></strong><p><?= htmlspecialchars($L['exp5_d']) ?></p></div>
                <span class="exp-arrow">→</span>
            </a>
        </div>
    </div>
</section>

<!-- POURQUOI TARKINA -->
<section class="pourquoi-section">
    <div class="pourquoi-inner">
        <p class="section-label"><?= htmlspecialchars($L['why_label']) ?></p>
        <h2 class="section-heading"><?= htmlspecialchars($L['why_h_pre']) ?><span style="color:var(--primary)"><?= htmlspecialchars($L['why_h_em']) ?></span><?= htmlspecialchars($L['why_h_q']) ?></h2>
        <p class="section-sub"><?= htmlspecialchars($L['why_sub']) ?></p>
        <div class="pourquoi-grid">
            <div class="pourquoi-card fade-in-up">
                <div class="pourquoi-icon"><svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg></div>
                <h3><?= htmlspecialchars($L['why1_t']) ?></h3>
                <p><?= htmlspecialchars($L['why1_d']) ?></p>
            </div>
            <div class="pourquoi-card fade-in-up">
                <div class="pourquoi-icon"><svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg></div>
                <h3><?= htmlspecialchars($L['why2_t']) ?></h3>
                <p><?= htmlspecialchars($L['why2_d']) ?></p>
            </div>
            <div class="pourquoi-card fade-in-up">
                <div class="pourquoi-icon"><svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg></div>
                <h3><?= htmlspecialchars($L['why3_t']) ?></h3>
                <p><?= htmlspecialchars($L['why3_d']) ?></p>
            </div>
            <div class="pourquoi-card fade-in-up">
                <div class="pourquoi-icon"><svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                <h3><?= htmlspecialchars($L['why4_t']) ?></h3>
                <p><?= htmlspecialchars($L['why4_d']) ?></p>
            </div>
        </div>
    </div>
</section>

<!-- TESTIMONIALS -->
<section class="temoignages-section">
    <div class="temoignages-inner">
        <p class="temo-label"><?= htmlspecialchars($L['temo_label']) ?></p>
        <h2 class="temo-heading"><?= htmlspecialchars($L['temo_h']) ?></h2>
        <div class="temo-grid">
            <div class="temo-card">
                <div class="temo-card-inner">
                    <div class="temo-avatar"><img loading="lazy" src="https://randomuser.me/api/portraits/women/44.jpg" alt="Sarah B."></div>
                    <p class="temo-quote"><?= htmlspecialchars($L['temo1_q']) ?></p>
                    <p class="temo-name">Sarah B.</p>
                    <p class="temo-city"><?= htmlspecialchars($L['temo1_role']) ?></p>
                    <div class="temo-stars">★★★★★</div>
                </div>
                <div class="temo-popup">
                    <img loading="lazy" src="https://randomuser.me/api/portraits/women/44.jpg" alt="Sarah B.">
                    <div class="temo-popup-content">
                        <div class="temo-stars">★★★★★</div>
                        <p><?= htmlspecialchars($L['temo1_qf']) ?></p>
                        <strong>Sarah B.</strong>
                        <span><?= htmlspecialchars($L['temo1_role']) ?></span>
                        <span class="temo-tag"><?= htmlspecialchars($L['temo1_tag']) ?></span>
                    </div>
                </div>
            </div>
            <div class="temo-card">
                <div class="temo-card-inner">
                    <div class="temo-avatar"><img loading="lazy" src="https://randomuser.me/api/portraits/men/32.jpg" alt="Mehdi K."></div>
                    <p class="temo-quote"><?= htmlspecialchars($L['temo2_q']) ?></p>
                    <p class="temo-name">Mehdi K.</p>
                    <p class="temo-city"><?= htmlspecialchars($L['temo2_role']) ?></p>
                    <div class="temo-stars">★★★★★</div>
                </div>
                <div class="temo-popup">
                    <img loading="lazy" src="https://randomuser.me/api/portraits/men/32.jpg" alt="Mehdi K.">
                    <div class="temo-popup-content">
                        <div class="temo-stars">★★★★★</div>
                        <p><?= htmlspecialchars($L['temo2_qf']) ?></p>
                        <strong>Mehdi K.</strong>
                        <span><?= htmlspecialchars($L['temo2_role']) ?></span>
                        <span class="temo-tag"><?= htmlspecialchars($L['temo2_tag']) ?></span>
                    </div>
                </div>
            </div>
            <div class="temo-card">
                <div class="temo-card-inner">
                    <div class="temo-avatar"><img loading="lazy" src="https://randomuser.me/api/portraits/women/68.jpg" alt="Amira T."></div>
                    <p class="temo-quote"><?= htmlspecialchars($L['temo3_q']) ?></p>
                    <p class="temo-name">Amira T.</p>
                    <p class="temo-city"><?= htmlspecialchars($L['temo3_role']) ?></p>
                    <div class="temo-stars">★★★★★</div>
                </div>
                <div class="temo-popup">
                    <img loading="lazy" src="https://randomuser.me/api/portraits/women/68.jpg" alt="Amira T.">
                    <div class="temo-popup-content">
                        <div class="temo-stars">★★★★★</div>
                        <p><?= htmlspecialchars($L['temo3_qf']) ?></p>
                        <strong>Amira T.</strong>
                        <span><?= htmlspecialchars($L['temo3_role']) ?></span>
                        <span class="temo-tag"><?= htmlspecialchars($L['temo3_tag']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- NEWSLETTER -->
<section class="newsletter-section" id="newsletter">
    <div class="newsletter-inner">
        <div class="newsletter-avatars">
            <img loading="lazy" src="https://randomuser.me/api/portraits/women/44.jpg" alt="">
            <img loading="lazy" src="https://randomuser.me/api/portraits/men/32.jpg" alt="">
            <img loading="lazy" src="https://randomuser.me/api/portraits/women/68.jpg" alt="">
        </div>
        <p class="newsletter-badge"><?= htmlspecialchars($L['nl_badge']) ?></p>
        <h2 class="newsletter-title"><?= htmlspecialchars($L['nl_title']) ?></h2>
        <p class="newsletter-sub"><?= htmlspecialchars($L['nl_sub']) ?></p>
        <?php if (!empty($nlMsg)): ?>
          <div style="margin:0 auto 16px;max-width:520px;padding:11px 18px;border-radius:50px;font-weight:600;font-size:.92rem;color:#fff;background:<?= $nlOk ? 'rgba(46,204,113,.25)' : 'rgba(231,76,60,.3)' ?>;border:1px solid rgba(255,255,255,.25);"><?= htmlspecialchars($nlMsg) ?></div>
        <?php endif; ?>
        <form class="newsletter-form" method="post" action="index.php#newsletter">
            <input type="email" name="newsletter_email" placeholder="<?= htmlspecialchars($L['nl_email_ph']) ?>" required>
            <button type="submit" name="newsletter_submit"><?= htmlspecialchars($L['nl_submit']) ?></button>
        </form>
    </div>
</section>

<?php include 'footer.php'; ?>

<script>
<?php if ($lang === 'fr'): ?>flatpickr.localize(flatpickr.l10ns.fr);<?php endif; ?>
<?php if ($lang === 'ar'): ?>flatpickr.localize(flatpickr.l10ns.ar);<?php endif; ?>

const departPicker = flatpickr("#date_fin", { dateFormat: "Y-m-d", altInput: true, altFormat: "d M Y", minDate: "today", disableMobile: true });
flatpickr("#date_debut", {
  dateFormat: "Y-m-d", altInput: true, altFormat: "d M Y", minDate: "today", disableMobile: true,
  onChange: function(selectedDates) { if (selectedDates[0]) departPicker.set('minDate', selectedDates[0]); }
});

document.querySelectorAll('.sf-field').forEach(field => {
  field.addEventListener('click', function() {
    const input = this.querySelector('input[type="text"]');
    if (input && input._flatpickr) { input._flatpickr.open(); }
  });
});

document.getElementById('bookingBar')?.addEventListener('submit', function(e) {
  const dest = this.querySelector('select[name="destination"]');
  if (dest && dest.value) {
    e.preventDefault();
    const p = new URLSearchParams({ id: dest.value });
    const dd = this.querySelector('[name="date_debut"]').value;
    const df = this.querySelector('[name="date_fin"]').value;
    const pers = this.querySelector('[name="personnes"]').value;
    if (dd) p.set('date_debut', dd);
    if (df) p.set('date_fin', df);
    if (pers) p.set('personnes', pers);
    window.location.href = 'region.php?' + p.toString();
  }
});

const observer = new IntersectionObserver((entries) => {
  entries.forEach(e => { if(e.isIntersecting) { e.target.classList.add('visible'); } });
}, { threshold: 0.1 });
document.querySelectorAll('.fade-in-up').forEach(el => observer.observe(el));

const statsObserver = new IntersectionObserver((entries, observerInstance) => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      const el = e.target;
      observerInstance.unobserve(el);
      const target = parseFloat(el.getAttribute('data-target'));
      const suffix = el.getAttribute('data-suffix') || '';
      const decimals = parseInt(el.getAttribute('data-decimals') || '0', 10);
      const duration = 1500;
      const startTime = performance.now();
      function updateNumber(now) {
        const elapsed = now - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const easeProgress = progress * (2 - progress);
        const currentVal = easeProgress * target;
        let formattedVal = decimals > 0 ? currentVal.toFixed(decimals) : Math.floor(currentVal).toLocaleString('fr-FR');
        el.textContent = formattedVal + suffix;
        if (progress < 1) requestAnimationFrame(updateNumber);
        else el.textContent = (decimals > 0 ? target.toFixed(decimals) : target.toLocaleString('fr-FR')) + suffix;
      }
      requestAnimationFrame(updateNumber);
    }
  });
}, { threshold: 0.1 });
document.querySelectorAll('.stat-number').forEach(el => statsObserver.observe(el));

(function(){
  var df = document.querySelector('.booking-field.dest');
  var ds = document.getElementById('bk_dest');
  if (df && ds) {
    df.addEventListener('click', function(e){
      if (e.target === ds) return;
      if (typeof ds.showPicker === 'function') { try { ds.showPicker(); return; } catch(_){} }
      ds.focus();
    });
  }
})();
</script>

</body>
</html>
