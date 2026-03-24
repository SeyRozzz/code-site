<?php
// changer_role.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'config.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin')) {
    exit("Accès refusé");
}

// ✅ Générer token CSRF si absent
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $new_role = $_POST['role'] ?? '';
    $token = $_POST['csrf_token'] ?? '';

    if ($token === $_SESSION['csrf_token'] && in_array($new_role, ['forestier', 'admin'])) {
        $stmt = $pdo->prepare("UPDATE utilisateurs SET role = ? WHERE id = ?");
        $stmt->execute([$new_role, $id]);
        header("Location: index.php?page=admin&success=rolechanged");
        exit();
    }
}

header("Location: index.php?page=admin&error=invalid");
exit();