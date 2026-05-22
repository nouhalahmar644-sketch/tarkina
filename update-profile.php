<?php
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: edit-profile.php');
    exit;
}

$userId = (int) $_SESSION['user_id'];

$nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
$prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$adresse = isset($_POST['adresse']) ? trim($_POST['adresse']) : '';

$fieldErrors = [
    'nom' => '',
    'prenom' => '',
    'email' => '',
    'adresse' => ''
];

if ($nom === '') {
    $fieldErrors['nom'] = 'Le nom est obligatoire.';
}
if ($prenom === '') {
    $fieldErrors['prenom'] = 'Le prénom est obligatoire.';
}
if ($email === '') {
    $fieldErrors['email'] = 'L\'e-mail est obligatoire.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $fieldErrors['email'] = 'L\'e-mail n\'est pas au bon format.';
}
if ($adresse === '') {
    $fieldErrors['adresse'] = 'L\'adresse est obligatoire.';
}

if (array_filter($fieldErrors)) {
    $_SESSION['profile_errors'] = $fieldErrors;
    $_SESSION['profile_old'] = [
        'nom' => $nom,
        'prenom' => $prenom,
        'email' => $email,
        'adresse' => $adresse
    ];
    header('Location: edit-profile.php');
    exit;
}

$emailCheckSql = 'SELECT id FROM utilisateur WHERE email = ? AND id <> ? LIMIT 1';
$emailCheckStmt = mysqli_prepare($conn, $emailCheckSql);

if ($emailCheckStmt !== false) {
    mysqli_stmt_bind_param($emailCheckStmt, 'si', $email, $userId);
    mysqli_stmt_execute($emailCheckStmt);
    mysqli_stmt_bind_result($emailCheckStmt, $existingId);
    $emailExists = mysqli_stmt_fetch($emailCheckStmt);
    mysqli_stmt_close($emailCheckStmt);

    if ($emailExists) {
        $_SESSION['profile_errors'] = [
            'nom' => '',
            'prenom' => '',
            'email' => 'Cet e-mail est déjà utilisé.',
            'adresse' => ''
        ];
        $_SESSION['profile_old'] = [
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'adresse' => $adresse
        ];
        header('Location: edit-profile.php');
        exit;
    }
}

$updateSql = 'UPDATE utilisateur SET nom = ?, prenom = ?, email = ?, adresse = ? WHERE id = ?';
$updateStmt = mysqli_prepare($conn, $updateSql);

if ($updateStmt === false) {
    $_SESSION['profile_errors'] = [
        'nom' => '',
        'prenom' => '',
        'email' => 'Une erreur est survenue. Veuillez réessayer.',
        'adresse' => ''
    ];
    $_SESSION['profile_old'] = [
        'nom' => $nom,
        'prenom' => $prenom,
        'email' => $email,
        'adresse' => $adresse
    ];
    header('Location: edit-profile.php');
    exit;
}

mysqli_stmt_bind_param($updateStmt, 'ssssi', $nom, $prenom, $email, $adresse, $userId);
$updated = mysqli_stmt_execute($updateStmt);
mysqli_stmt_close($updateStmt);

if (!$updated) {
    $_SESSION['profile_errors'] = [
        'nom' => '',
        'prenom' => '',
        'email' => 'Une erreur est survenue. Veuillez réessayer.',
        'adresse' => ''
    ];
    $_SESSION['profile_old'] = [
        'nom' => $nom,
        'prenom' => $prenom,
        'email' => $email,
        'adresse' => $adresse
    ];
    header('Location: edit-profile.php');
    exit;
}

$_SESSION['user_name'] = trim($prenom . ' ' . $nom);
$_SESSION['profile_success'] = 'Profil mis à jour avec succès.';

header('Location: profile.php');
exit;
?>
