<?php
/**
 * Contrôleur : changer_role.php
 * Gère les permissions et la modification des rôles utilisateurs.
 */

// 1. SÉCURITÉ : Seuls les admins et le superadmin peuvent accéder à cette logique
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin')) {
    header("Location: index.php?page=accueil");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idCible = $_POST['user_id'];
    $nouveauRole = $_POST['new_role'];

    // 2. PROTECTION DU SUPERADMIN
    // On récupère le rôle actuel de la cible pour vérifier qu'on ne touche pas à un superadmin
    $check = $pdo->prepare("SELECT role FROM utilisateurs WHERE id = ?");
    $check->execute([$idCible]);
    $userCible = $check->fetch();

    //
    if ($userCible['role'] === 'superadmin') {
        header("Location: index.php?page=admin&error=protection_superadmin");
        exit();
    }

    // met a jour le role changé
    $stmt = $pdo->prepare("UPDATE utilisateurs SET role = ? WHERE id = ?");
    $stmt->execute([$nouveauRole, $idCible]);
}

// Retour au panel
header("Location: index.php?page=admin");
exit();