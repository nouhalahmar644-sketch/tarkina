<?php
session_start();
require_once __DIR__ . '/db.php';

$page_title = 'Tarkina - Voyagez chez l\'habitant en Tunisie';

// Récupérer les régions pour la section "Découvrez nos régions"
$sql_regions = "SELECT * FROM region ORDER BY id DESC LIMIT 3";
$res_regions = mysqli_query($conn, $sql_regions);
$regions = [];
if ($res_regions) {
    while ($row = mysqli_fetch_assoc($res_regions)) {
        $regions[] = $row;
    }
}

// Nous n'utilisons pas layout_header ici car le hero demande une structure spécifique (navbar transparente)
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- NAVBAR (Transparent over Hero) -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">TARKINA<span>.</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link px-3" href="explorer.php">Explorer</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="about.php">À propos</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="artisanat.php">Boutique</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="contact.php">Contact</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li class="nav-item ms-lg-3">
                        <a class="btn btn-outline-light rounded-pill px-4" href="profile.php">Mon Compte</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item ms-lg-3">
                        <a class="btn btn-outline-light rounded-pill px-4" href="login.php?redirect=index.php">Connexion</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- HERO SECTION -->
<header class="hero" style="background-image: url('https://images.unsplash.com/photo-1516026672322-bc52d61a55d5?auto=format&fit=crop&w=1920&q=80');">
    <div class="container hero-content text-center">
        <h1 class="fw-black">VOYAGEZ CHEZ L'HABITANT.<br>DÉCOUVREZ LA VRAIE TUNISIE.</h1>
        
        <!-- SEARCH BAR -->
        <div class="search-container">
            <form action="search.php" method="GET" class="d-flex w-100 align-items-center">
                <div class="search-field">
                    <label>DESTINATION</label>
                    <input type="text" name="destination" placeholder="Où allez-vous ?" required>
                </div>
                <div class="search-field">
                    <label>ARRIVÉE</label>
                    <input type="date" name="date_debut">
                </div>
                <div class="search-field">
                    <label>DÉPART</label>
                    <input type="date" name="date_fin">
                </div>
                <div class="search-field border-0">
                    <label>VOYAGEURS</label>
                    <input type="number" name="personnes" value="1" min="1">
                </div>
                <button type="submit" class="btn-search">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </div>

        <!-- CATEGORY PILLS -->
        <div class="category-pills">
            <a href="search.php?type=hebergement" class="pill"><i class="bi bi-house-door me-2"></i> Hébergement</a>
            <a href="search.php?type=repas" class="pill"><i class="bi bi-egg-fried me-2"></i> Repas maison</a>
            <a href="search.php?type=guide" class="pill"><i class="bi bi-geo-alt me-2"></i> Guide local</a>
            <a href="search.php?type=artisanat" class="pill"><i class="bi bi-bag-heart me-2"></i> Produits artisanaux</a>
            <a href="search.php?type=evenement" class="pill"><i class="bi bi-calendar-event me-2"></i> Événements</a>
        </div>
    </div>
</header>

<!-- NOS RÉGIONS -->
<section class="bg-white">
    <div class="container">
        <h2 class="section-title">Découvrez nos régions</h2>
        <div class="row">
            <?php foreach ($regions as $reg): ?>
                <div class="col-md-4 mb-4">
                    <a href="region.php?id=<?php echo $reg['id']; ?>" class="text-decoration-none">
                        <div class="region-card">
                            <img src="<?php 
                                if ($reg['photo_principale']) {
                                    $path = $reg['photo_principale'];
                                    echo htmlspecialchars((strpos($path, 'uploads/') === 0) ? $path : 'uploads/' . $path);
                                } else {
                                    echo 'https://images.unsplash.com/photo-1540260074744-934336c53549?auto=format&fit=crop&w=800&q=80';
                                }
                            ?>" alt="<?php echo htmlspecialchars($reg['nom']); ?>">
                            <div class="region-overlay">
                                <h3 class="mb-1"><?php echo htmlspecialchars($reg['nom']); ?></h3>
                                <p class="small opacity-75 mb-0"><?php echo htmlspecialchars(substr($reg['description'], 0, 80)); ?>...</p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- POURQUOI TARKINA -->
<section class="bg-cream">
    <div class="container">
        <h2 class="section-title">Pourquoi Tarkina ?</h2>
        <div class="row g-4">
            <div class="col-md-3">
                <div class="feature-card">
                    <i class="bi bi-shield-check feature-icon"></i>
                    <h4>Confiance</h4>
                    <p class="small text-muted">Hôtes et logements vérifiés par notre équipe locale.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="feature-card">
                    <i class="bi bi-heart feature-icon"></i>
                    <h4>Authenticité</h4>
                    <p class="small text-muted">Vivez des moments uniques au cœur des traditions tunisiennes.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="feature-card">
                    <i class="bi bi-people feature-icon"></i>
                    <h4>Communauté</h4>
                    <p class="small text-muted">Un réseau d'habitants passionnés par leur région.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="feature-card">
                    <i class="bi bi-globe-europe-africa feature-icon"></i>
                    <h4>Impact Local</h4>
                    <p class="small text-muted">Votre voyage soutient directement l'économie des régions.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- NOS SERVICES -->
<section>
    <div class="container text-center">
        <h2 class="section-title">Nos services</h2>
        <div class="row justify-content-center g-5">
            <div class="col-6 col-md-2">
                <div class="service-icon-wrap">
                    <div class="service-icon-circle"><i class="bi bi-house"></i></div>
                    <h6 class="fw-bold">Séjours</h6>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="service-icon-wrap">
                    <div class="service-icon-circle"><i class="bi bi-cup-hot"></i></div>
                    <h6 class="fw-bold">Gastronomie</h6>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="service-icon-wrap">
                    <div class="service-icon-circle"><i class="bi bi-compass"></i></div>
                    <h6 class="fw-bold">Exploration</h6>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="service-icon-wrap">
                    <div class="service-icon-circle"><i class="bi bi-hammer"></i></div>
                    <h6 class="fw-bold">Artisanat</h6>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="service-icon-wrap">
                    <div class="service-icon-circle"><i class="bi bi-stars"></i></div>
                    <h6 class="fw-bold">Événements</h6>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- TÉMOIGNAGES -->
<section class="bg-navy text-white overflow-hidden">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-5">
                <h2 class="playfair display-4 mb-4">Ce qu'ils en disent...</h2>
                <p class="lead opacity-75">Plus de 500 voyageurs ont déjà vécu l'expérience Tarkina cette année.</p>
            </div>
            <div class="col-md-7">
                <div class="card bg-white text-dark p-5 rounded-4 shadow-lg">
                    <i class="bi bi-quote fs-1 text-coral"></i>
                    <p class="fs-4 italic">"Un séjour inoubliable à tarkina. L'accueil de la famille était incroyable et la nourriture... je n'ai jamais mangé un couscous aussi bon !"</p>
                    <div class="d-flex align-items-center mt-4">
                        <div class="rounded-circle bg-soft-grey me-3" style="width: 50px; height: 50px;"></div>
                        <div>
                            <h6 class="mb-0 fw-bold">Sarah B.</h6>
                            <small class="text-muted">Voyageuse - France</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="footer-logo">TARKINA<span>.</span></div>
                <p class="small opacity-75">Plateforme de tourisme alternatif en Tunisie. Découvrez l'hospitalité légendaire et les trésors cachés de nos régions.</p>
                <div class="d-flex gap-3 mt-4">
                    <a href="#" class="text-white fs-5"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="text-white fs-5"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-white fs-5"><i class="bi bi-twitter-x"></i></a>
                </div>
            </div>
            <div class="col-md-2 offset-md-1">
                <h6 class="fw-bold mb-4">Navigation</h6>
                <ul class="footer-links">
                    <li><a href="explorer.php">Toutes les offres</a></li>
                    <li><a href="region.php">Régions</a></li>
                    <li><a href="artisanat.php">Boutique</a></li>
                    <li><a href="evenement.php">Événements</a></li>
                </ul>
            </div>
            <div class="col-md-2">
                <h6 class="fw-bold mb-4">Tarkina</h6>
                <ul class="footer-links">
                    <li><a href="about.php">À propos</a></li>
                    <li><a href="#">Devenir hôte</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h6 class="fw-bold mb-4">Newsletter</h6>
                <p class="small opacity-75">Recevez nos meilleures pépites locales.</p>
                <div class="input-group mb-3">
                    <input type="text" class="form-control bg-transparent border-secondary text-white" placeholder="Email">
                    <button class="btn btn-coral text-white" type="button">OK</button>
                </div>
            </div>
        </div>
        <hr class="mt-5 mb-4 opacity-25">
        <div class="d-flex justify-content-between small opacity-50">
            <p>&copy; 2026 Tarkina. Tous droits réservés.</p>
            <div class="d-flex gap-4">
                <a href="#" class="text-white text-decoration-none">Mentions légales</a>
                <a href="#" class="text-white text-decoration-none">Confidentialité</a>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            document.querySelector('.navbar').classList.add('scrolled');
        } else {
            document.querySelector('.navbar').classList.remove('scrolled');
        }
    });
</script>
</body>
</html>
