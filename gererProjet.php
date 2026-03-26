<?php
// gererProjets.php - Admin crée et gère les projets
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'superadmin'])) {
    header("Location: index.php?page=accueil");
    exit();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = "";
$projets = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ajouter') {
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = "❌ Erreur de sécurité: token CSRF invalide";
    } else {
        $nom = trim($_POST['nom'] ?? '');
        
        if (empty($nom)) {
            $message = "❌ Le nom du projet est obligatoire.";
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO projets (nom, id_createur) VALUES (?, ?)");
                $stmt->execute([$nom, $_SESSION['user_id']]);
                $message = "Projet créé avec succès!";
            } catch (PDOException $e) {
                $message = "❌ Erreur: " . $e->getMessage();
            }
        }
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'supprimer') {
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = "❌ Erreur de sécurité: token CSRF invalide";
    } else {
        $id_projet = (int)($_POST['id_projet'] ?? 0);
        
        if ($id_projet > 0) {
            try {
                // D'abord supprimer les affectations (si la table existe)
                try {
                    $delete_affectations = $pdo->prepare("DELETE FROM projets_forestiers WHERE id_projet = ?");
                    $delete_affectations->execute([$id_projet]);
                } catch (PDOException $e) {
                    // Table n'existe pas, on continue
                }
                
                // Puis supprimer les arbres
                $delete_arbres = $pdo->prepare("DELETE FROM arbres WHERE id_projet = ?");
                $delete_arbres->execute([$id_projet]);
                
                // Enfin supprimer le projet
                $stmt = $pdo->prepare("DELETE FROM projets WHERE id = ?");
                $stmt->execute([$id_projet]);
                
                $message = "Projet supprimé avec succès!";
            } catch (PDOException $e) {
                error_log("Erreur suppression projet: " . $e->getMessage());
                $message = "❌ Erreur lors de la suppression.";
            }
        }
    }
}

try {
    $stmt = $pdo->query("
        SELECT p.*, u.nom as createur_nom,
               (SELECT COUNT(*) FROM arbres WHERE id_projet = p.id) as nb_arbres
        FROM projets p
        LEFT JOIN utilisateurs u ON p.id_createur = u.id
        ORDER BY p.id DESC
    ");
    $projets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer le nombre de forestiers affectés à chaque projet
    foreach ($projets as &$p) {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM projets_forestiers WHERE id_projet = ?");
            $stmt->execute([$p['id']]);
            $p['nb_forestiers'] = $stmt->fetchColumn() ?: 0;
        } catch (PDOException $e) {
            // Table n'existe pas encore
            $p['nb_forestiers'] = 0;
        }
    }
} catch (PDOException $e) {
    error_log("Erreur lecture projets: " . $e->getMessage());
}

include 'gererProjetVue.php';
?>
