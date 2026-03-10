<?php
/**
 * Contrôleur : supprimer_user.php
 * Empêche la suppression des comptes Superadmin.
 * ✅ SÉCURISÉ : Vérification CSRF + logique améliorée
 */

// 1. Vérifier session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

// 2. Sécurité : Vérifier que c'est bien un admin ou superadmin qui agit
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin')) {
    header("Location: index.php?page=accueil");
    exit();
}

// 3. ✅ Vérifier le token CSRF
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header("Location: index.php?page=admin&error=csrf");
        exit();
    }
    
    $idASupprimer = (int)($_POST['user_id'] ?? 0);
    
    if ($idASupprimer > 0) {
        try {
            // Récupérer les infos de la cible
            $check = $pdo->prepare("SELECT id, role FROM utilisateurs WHERE id = ?");
            $check->execute([$idASupprimer]);
            $userCible = $check->fetch();

            if ($userCible) {
                // 🔒 PROTECTION : Ne pas supprimer un superadmin
                if ($userCible['role'] === 'superadmin') {
                    header("Location: index.php?page=admin&error=protection_superadmin");
                    exit();
                }
                
                // 🔒 Un admin standard ne peut supprimer que des "forestier"
                // Un superadmin peut supprimer tout le monde (sauf lui-même = check au-dessus)
                if ($_SESSION['role'] === 'admin' && $userCible['role'] === 'admin') {
                    header("Location: index.php?page=admin&error=permission");
                    exit();
                }
                
                // 🔒 Impossible de se supprimer soi-même
                if ($userCible['id'] === $_SESSION['id']) {
                    header("Location: index.php?page=admin&error=cannot_delete_self");
                    exit();
                }
                
                // ✅ Tout va bien, on supprime
                $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
                $stmt->execute([$idASupprimer]);
            }
        } catch (PDOException $e) {
            error_log("Supprimer_user - Erreur BDD: " . $e->getMessage());
        }
    }
}

// Retour au panel
header("Location: index.php?page=admin");
exit();