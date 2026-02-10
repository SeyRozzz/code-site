<?php
/**
 * Contrôleur : supprimer_user.php
 * Empêche la suppression des comptes Superadmin.
 */

// 1. Sécurité : Vérifier que c'est bien un admin ou superadmin qui agit
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin')) {
    header("Location: index.php?page=accueil");
    exit();
}

$idASupprimer = $_GET['id'] ?? null;
$monEmail = $_SESSION['email'] ?? '';

if ($idASupprimer) {
    // On récupère les infos de la cible (email et rôle)
    $check = $pdo->prepare("SELECT email, role FROM utilisateurs WHERE id = ?");
    $check->execute([$idASupprimer]);
    $userCible = $check->fetch();

    if ($userCible) {
        // 
        // Un admin "normal" ne peut pas supprimer un superadmin
        if ($userCible['email'] !== $monEmail && $userCible['role'] !== 'superadmin') {
            $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
            $stmt->execute([$idASupprimer]);
        }
    }
}

header("Location: index.php?page=admin");
exit();