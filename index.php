<?php
/**
 * Tableau de bord : réservé aux utilisateurs connectés.
 */
require_once __DIR__ . '/includes/auth_guard.php';

$page_title = 'Tableau de bord';
require_once __DIR__ . '/includes/layout_header.php';

$name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Utilisateur';
$role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';
?>

<div class="row">
    <div class="col-lg-8">
        <div class="card p-4">
            <h1 class="h4 mb-3">Bienvenue</h1>
            <p class="mb-1"><strong>Nom :</strong> <?php echo htmlspecialchars($name); ?></p>
            <p class="mb-4"><strong>Rôle :</strong> <?php echo htmlspecialchars($role); ?></p>
            <p class="text-muted mb-0">Vous êtes connecté à la plateforme tourisme (PFE).</p>
        </div>
    </div>
    <div class="col-lg-4 mt-3 mt-lg-0">
        <div class="card p-4">
            <h2 class="h6 mb-3">Session</h2>
            <p class="small text-muted mb-3">Identifiant utilisateur en session : <?php echo (int) $_SESSION['user_id']; ?></p>
            <a class="btn btn-outline-danger w-100" href="logout.php">Déconnexion</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/layout_footer.php'; ?>
