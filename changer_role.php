<?php
/**
 * Contrôleur : changer_role.php
 * Gère les permissions et la modification des rôles utilisateurs.
 * ✅ SÉCURISÉ : CSRF token + validation rôle
 */

// 1. Vérifier session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

// 2. SÉCURITÉ : Seuls les admins et le superadmin peuvent accéder à cette logique
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin')) {
    header("Location: index.php?page=accueil");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ✅ Vérifier le token CSRF
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header("Location: index.php?page=admin&error=csrf");
        exit();
    }
    
    $idCible = (int)($_POST['user_id'] ?? 0);
    $nouveauRole = $_POST['new_role'] ?? '';

    // ✅ Validation : Rôle autorisé (whitelist)
    if (!in_array($nouveauRole, ['forestier', 'admin'])) {
        header("Location: index.php?page=admin&error=invalid_role");
        exit();
    }

    if ($idCible > 0) {
        try {
            // Récupérer le rôle actuel de la cible
            $check = $pdo->prepare("SELECT id, role FROM utilisateurs WHERE id = ?");
            $check->execute([$idCible]);
            $userCible = $check->fetch();

            if ($userCible) {
                // 🔒 PROTECTION : Ne pas modifier un superadmin
                if ($userCible['role'] === 'superadmin') {
                    header("Location: index.php?page=admin&error=protection_superadmin");
                    exit();
                }
                
                // 🔒 Un admin standard ne peut pas modifier un autre admin
                if ($_SESSION['role'] === 'admin' && $userCible['role'] === 'admin') {
                    header("Location: index.php?page=admin&error=permission");
                    exit();
                }

                // ✅ Mettre à jour le rôle
                $stmt = $pdo->prepare("UPDATE utilisateurs SET role = ? WHERE id = ?");
                $stmt->execute([$nouveauRole, $idCible]);
            }
        } catch (PDOException $e) {
            error_log("Changer_role - Erreur BDD: " . $e->getMessage());
        }
    }
}

// Retour au panel
header("Location: index.php?page=admin");
exit();