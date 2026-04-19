<?php
/**
 * Connexion utilisateur.
 * - Vérification du mot de passe avec password_verify()
 * - Session : id, nom affiché, rôle
 * - Redirection vers le tableau de bord si OK
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/session_bootstrap.php';

// Si déjà connecté, pas besoin d'afficher le formulaire
if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$page_title = 'Connexion';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ($email === '' || $password === '') {
        $errors[] = 'Veuillez remplir tous les champs.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'E-mail invalide.';
    } else {
        // Requête préparée : récupérer l'utilisateur par e-mail
        $sql = 'SELECT id, nom, prenom, email, mot_de_passe, role FROM utilisateur WHERE email = ? LIMIT 1';
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt === false) {
            $errors[] = 'Erreur de préparation : ' . mysqli_error($conn);
        } else {
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            // bind_result fonctionne sans l'extension mysqlnd (compatible hébergements variés)
            mysqli_stmt_bind_result($stmt, $uid, $nom_db, $prenom_db, $email_db, $hash_db, $role_db);
            $found = mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            if (!$found || !password_verify($password, $hash_db)) {
                $errors[] = 'E-mail ou mot de passe incorrect.';
            } else {
                // Évite le vol de session : nouvel identifiant de session après login
                session_regenerate_id(true);

                $_SESSION['user_id'] = (int) $uid;
                // "Nom" pour l'affichage : prénom + nom (lisible sur le tableau de bord)
                $_SESSION['user_name'] = trim($prenom_db . ' ' . $nom_db);
                $_SESSION['user_role'] = $role_db;

                header('Location: index.php');
                exit;
            }
        }
    }
}

require_once __DIR__ . '/includes/layout_header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-5 col-lg-4">
        <div class="card p-4">
            <h1 class="h4 mb-3 text-center">Connexion</h1>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php foreach ($errors as $e): ?>
                        <div><?php echo htmlspecialchars($e); ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form id="loginForm" method="post" action="login.php" novalidate>
                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" class="form-control" id="email" name="email" required
                           value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                    <div class="invalid-feedback">E-mail requis.</div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <div class="invalid-feedback">Mot de passe requis.</div>
                </div>
                <button type="submit" class="btn btn-primary w-100">Se connecter</button>
            </form>
            <p class="text-center mt-3 mb-0">
                Pas encore de compte ? <a href="register.php">Inscription</a>
            </p>
        </div>
    </div>
</div>

<script src="assets/js/auth_forms.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        setupAuthForm('loginForm', { mode: 'login' });
    });
</script>

<?php require_once __DIR__ . '/includes/layout_footer.php'; ?>
