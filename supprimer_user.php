<?php
// supprimer_user.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'config.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin')) {
    exit("Accès refusé");
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_GET['token'])) {
$token = $_GET['token'] ?? '';
if ($token !== $_SESSION['csrf_token']) {
    header("Location: index.php?page=admin&error=csrf");
    exit();
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Empêcher de se supprimer soi-même
    if ($id === (int)$_SESSION['user_id']) {
        header("Location: index.php?page=admin&error=selfdelete");
        exit();
    }

    $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: index.php?page=admin&success=deleted");
exit();