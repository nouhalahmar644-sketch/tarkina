<?php
/**
 * Inscription d'un nouvel utilisateur (PFE tourisme).
 * - Validation côté serveur
 * - Mot de passe haché avec password_hash()
 * - Insertion sécurisée avec requête préparée (MySQLi procédural)
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/session_bootstrap.php';

$page_title = 'Inscription';
$errors = [];
$success = false;

// Traitement du formulaire uniquement si la méthode HTTP est POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Étape 1 : récupérer et nettoyer les entrées
    $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
    $prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $adresse = isset($_POST['adresse']) ? $_POST['adresse'] : '';

    // Étape 2 : validation côté serveur (obligatoire même si le JS valide aussi)
    if ($nom === '') {
        $errors[] = 'Le nom est obligatoire.';
    }
    if ($prenom === '') {
        $errors[] = 'Le prénom est obligatoire.';
    }
    if ($email === '') {
        $errors[] = 'L\'e-mail est obligatoire.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'L\'e-mail n\'est pas au bon format.';
    }
    if ($password === '') {
        $errors[] = 'Le mot de passe est obligatoire.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
    }

    // Étape 3 : si aucune erreur, insertion en base
    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $role = 'utilisateur'; // rôle par défaut pour un nouveau compte

        // Requête préparée : les ? sont remplacés proprement (anti-injection SQL)
        $sql = 'INSERT INTO utilisateur (nom, prenom, email, mot_de_passe,adresse, role) VALUES (?, ?, ?, ?,?, ?)';
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt === false) {
            $errors[] = 'Erreur de préparation de la requête : ' . mysqli_error($conn);
        } else {
            mysqli_stmt_bind_param($stmt, 'ssssss', $nom, $prenom, $email, $hash, $adresse, $role);

            if (mysqli_stmt_execute($stmt)) {
                $success = true;
            } else {
                // Doublon d'e-mail (contrainte UNIQUE)
                if (mysqli_errno($conn) === 1062) {
                    $errors[] = 'Cet e-mail est déjà utilisé.';
                } else {
                    $errors[] = 'Erreur lors de l\'enregistrement : ' . mysqli_stmt_error($stmt);
                }
            }
            mysqli_stmt_close($stmt);
        }
    }
}

require_once __DIR__ . '/includes/layout_header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card p-4">
            <h1 class="h4 mb-3 text-center">Créer un compte</h1>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0">
                        <?php foreach ($errors as $e): ?>
                            <li><?php echo htmlspecialchars($e); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success" role="alert">
                    Inscription réussie. Vous pouvez maintenant vous connecter.
                </div>
                <a class="btn btn-primary w-100" href="login.php">Aller à la connexion</a>
            <?php else: ?>
                <!-- novalidate : laisse le navigateur sans validation HTML5 pour tester notre JS + PHP -->
                <form id="registerForm" method="post" action="register.php" novalidate>
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="nom" name="nom" required
                               value="<?php echo isset($nom) ? htmlspecialchars($nom) : ''; ?>">
                        <div class="invalid-feedback">Veuillez saisir votre nom.</div>
                    </div>
                    <div class="mb-3">
                        <label for="prenom" class="form-label">Prénom</label>
                        <input type="text" class="form-control" id="prenom" name="prenom" required
                               value="<?php echo isset($prenom) ? htmlspecialchars($prenom) : ''; ?>">
                        <div class="invalid-feedback">Veuillez saisir votre prénom.</div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" required
                               value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                        <div class="invalid-feedback">Veuillez saisir un e-mail valide.</div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" required minlength="6">
                        <div class="form-text">Au moins 6 caractères.</div>
                        <div class="invalid-feedback">Mot de passe trop court.</div>
                    </div>
                    <div class="mb-3">
                        <label for="adresse" class="form-label">Adresse</label>
                        <input type="text" class="form-control" id="adresse" name="adresse" required
                               value="<?php echo isset($adresse) ? htmlspecialchars($adresse) : ''; ?>">
                        <div class="invalid-feedback">Veuillez saisir votre adresse.</div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
                </form>
                <p class="text-center mt-3 mb-0">
                    Déjà un compte ? <a href="login.php">Connexion</a>
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="assets/js/auth_forms.js"></script>
<script>
    // Active la validation JS spécifique à ce formulaire
    document.addEventListener('DOMContentLoaded', function () {
        setupAuthForm('registerForm', { mode: 'register' });
    });
</script>

<?php require_once __DIR__ . '/includes/layout_footer.php'; ?>
