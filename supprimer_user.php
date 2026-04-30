<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

if (!isset($_SESSION['user_id'], $_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'superadmin'], true)) {
    http_response_code(403);
    exit('Acces refuse');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?page=admin&error=invalid_method');
    exit();
}

$token = $_POST['csrf_token'] ?? '';
if (empty($_SESSION['csrf_token']) || !is_string($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
    header('Location: index.php?page=admin&error=csrf');
    exit();
}

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    header('Location: index.php?page=admin&error=invalid');
    exit();
}

if ($id === (int)$_SESSION['user_id']) {
    header('Location: index.php?page=admin&error=selfdelete');
    exit();
}

$stmt = $pdo->prepare("SELECT role FROM utilisateurs WHERE id = ?");
$stmt->execute([$id]);
$targetRole = $stmt->fetchColumn();

if ($targetRole === false) {
    header('Location: index.php?page=admin&error=notfound');
    exit();
}

if ($targetRole === 'superadmin') {
    header('Location: index.php?page=admin&error=forbidden');
    exit();
}

try {
    $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
    $stmt->execute([$id]);
} catch (PDOException $e) {
    error_log('Erreur suppression utilisateur: ' . $e->getMessage());
    header('Location: index.php?page=admin&error=delete_failed');
    exit();
}

header('Location: index.php?page=admin&success=deleted');
exit();