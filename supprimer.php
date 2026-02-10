<?php
// supprimer.php

// 1. SÉCURITÉ : Seuls les admins/superadmins peuvent supprimer
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin')) {
    // Si un petit malin essaye, on le renvoie dehors
    header("Location: index.php?page=carte&error=interdit");
    exit();
}

$id = $_GET['id'] ?? null;

if ($id) {
    require_once 'config.php';
    $stmt = $pdo->prepare("DELETE FROM arbres WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: index.php?page=carte");
exit();