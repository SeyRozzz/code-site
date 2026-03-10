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
if (isset($_GET['success'])) {
    if ($_GET['success'] === 'useradded') $success_msg = "Utilisateur créé avec succès !";
    if ($_GET['success'] === 'deleted') $success_msg = "Utilisateur supprimé.";
    if ($_GET['success'] === 'rolechanged') $success_msg = "Rôle mis à jour.";
}

// Récupération des utilisateurs
$stmt = $pdo->query("SELECT id, nom, email, role FROM utilisateurs ORDER BY id DESC");
$users = $stmt->fetchAll();

include 'adminVue.php';