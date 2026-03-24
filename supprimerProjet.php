<?php
// supprimer.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

// 1. SÉCURITÉ : Vérification du rôle (T3: Évaluation du contrôle d'accès)
// Seuls les admins ou superadmins peuvent supprimer
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'superadmin'])) {
    header("Location: index.php?page=carte&error=droits_insuffisants");
    exit();
}

// 2. SÉCURITÉ : Vérification du Token CSRF (T5: Évaluation de la sécurité)
// Empêche un pirate de forcer la suppression via un lien externe
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header("Location: index.php?page=carte&error=csrf_invalide");
        exit();
    }

    $id = (int)($_POST['id'] ?? 0);

    if ($id > 0) {
        try {
            // Requête de suppression
            $stmt = $pdo->prepare("DELETE FROM arbres WHERE id = ?");
            $stmt->execute([$id]);

            // Redirection avec message de succès
            header("Location: index.php?page=carte&msg=suppression_ok");
            exit();
        } catch (PDOException $e) {
            error_log("Erreur Suppression : " . $e->getMessage());
            header("Location: index.php?page=carte&error=erreur_bdd");
            exit();
        }
    }
}

// Si on arrive ici sans POST ou sans ID, on renvoie à la carte
header("Location: index.php?page=carte");
exit();