<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'superadmin'])) {
    header("Location: index.php?page=carte&error=droits_insuffisants");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header("Location: index.php?page=carte&error=csrf_invalide");
        exit();
    }

    $id = (int)($_POST['id'] ?? 0);

    if ($id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM arbres WHERE id = ?");
            $stmt->execute([$id]);
            header("Location: index.php?page=carte&msg=suppression_ok");
            exit();
        } catch (PDOException $e) {
            error_log("Erreur Suppression : " . $e->getMessage());
            header("Location: index.php?page=carte&error=erreur_bdd");
            exit();
        }
    }
}

header("Location: index.php?page=carte");
exit();