<?php
/**
 * Handles a single review submission, then redirects back to the service page.
 * One review per user per service is enforced in avis_insert().
 */
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/avis_helpers.php';

$type      = (string) ($_POST['type'] ?? '');
$serviceId = (int) ($_POST['service_id'] ?? 0);
$back      = avis_col($type) ? ($type . '.php?id=' . $serviceId . '#avis') : 'explorer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $back);
    exit;
}
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$note        = (int) ($_POST['note'] ?? 0);
$commentaire = trim((string) ($_POST['commentaire'] ?? ''));

$err = null;
$ok  = avis_insert($conn, $type, $serviceId, (int) $_SESSION['user_id'], $note, $commentaire, $err);

$_SESSION['avis_flash'] = $ok
    ? ['type' => 'ok',  'msg' => 'Merci ! Votre avis a été publié.']
    : ['type' => 'err', 'msg' => $err ?: 'Erreur lors de la publication de votre avis.'];

header('Location: ' . $back);
exit;
