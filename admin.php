<?php
// admin.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'config.php';

// Sécurité
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin')) {
    header("Location: index.php?page=accueil");
    exit();
}

// Token pour les actions rapides (suppression/rôle)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$success_msg = "";
$error_msg = "";
if (isset($_GET['success'])) {
    if ($_GET['success'] === 'useradded') $success_msg = "Utilisateur créé avec succès !";
    if ($_GET['success'] === 'deleted') $success_msg = "Utilisateur supprimé.";
    if ($_GET['success'] === 'rolechanged') $success_msg = "Rôle mis à jour.";
}

if (isset($_GET['error'])) {
    if ($_GET['error'] === 'csrf') $error_msg = "La requete a ete refusee pour raison de securite.";
    if ($_GET['error'] === 'delete_failed') $error_msg = "Erreur technique lors de la suppression.";
    if ($_GET['error'] === 'forbidden') $error_msg = "Cette action n'est pas autorisee sur ce compte.";
    if ($_GET['error'] === 'invalid') $error_msg = "La requete est incomplete ou invalide.";
    if ($_GET['error'] === 'invalid_method') $error_msg = "Cette action doit etre envoyee en POST.";
    if ($_GET['error'] === 'notfound') $error_msg = "Utilisateur introuvable.";
    if ($_GET['error'] === 'selfdelete') $error_msg = "Vous ne pouvez pas supprimer votre propre compte.";
    if ($_GET['error'] === 'selfrole') $error_msg = "Vous ne pouvez pas modifier votre propre role.";
}

// Récupération des utilisateurs
$stmt = $pdo->query("SELECT id, nom, email, role FROM utilisateurs ORDER BY id DESC");
$users = $stmt->fetchAll();

include 'adminVue.php';