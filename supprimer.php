<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin')) {
    header("Location: index.php?page=carte&error=interdit");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header("Location: index.php?page=carte&error=csrf");
        exit();
    }
    
    $id = (int)($_POST['id'] ?? 0);
    
    if ($id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM arbres WHERE id = ?");
            $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Supprimer - Erreur BDD: " . $e->getMessage());
        }
    }
}

header("Location: index.php?page=carte");
exit();