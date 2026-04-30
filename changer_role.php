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

$id = (int)($_POST['id'] ?? 0);
$new_role = $_POST['role'] ?? '';
$token = $_POST['csrf_token'] ?? '';

if (empty($_SESSION['csrf_token']) || !is_string($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
    header('Location: index.php?page=admin&error=csrf');
    exit();
}

if ($id <= 0 || !in_array($new_role, ['forestier', 'admin'], true)) {
    header('Location: index.php?page=admin&error=invalid');
    exit();
}

if ($id === (int)$_SESSION['user_id']) {
    header('Location: index.php?page=admin&error=selfrole');
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
    $stmt = $pdo->prepare("UPDATE utilisateurs SET role = ? WHERE id = ?");
    $stmt->execute([$new_role, $id]);
} catch (PDOException $e) {
    error_log('Erreur changement role: ' . $e->getMessage());
    header('Location: index.php?page=admin&error=invalid');
    exit();
}

header('Location: index.php?page=admin&success=rolechanged');
exit();