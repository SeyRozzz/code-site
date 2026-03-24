<?php
// gererProjets.php - Admin crée et gère les projets
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

// 🔐 SÉCURITÉ : Seuls les admins/superadmins
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'superadmin'])) {
    header("Location: index.php?page=accueil");
    exit();
}

// ✅ Générer token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = "";
$projets = [];

// 1. Traitement du formulaire AJOUTER PROJET
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
                $message = "✅ Projet créé avec succès!";
            } catch (PDOException $e) {
                $message = "❌ Erreur: " . $e->getMessage();
            }
        }
    }
}

// 2. Traitement de la SUPPRESSION DE PROJET (admin only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'supprimer') {
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = "❌ Erreur de sécurité: token CSRF invalide";
    } else {
        $id_projet = (int)($_POST['id_projet'] ?? 0);
        
        if ($id_projet > 0) {
            try {
                // D'abord supprimer les affectations
                $delete_affectations = $pdo->prepare("DELETE FROM projets_forestiers WHERE id_projet = ?");
                $delete_affectations->execute([$id_projet]);
                
                // Puis supprimer les arbres
                $delete_arbres = $pdo->prepare("DELETE FROM arbres WHERE id_projet = ?");
                $delete_arbres->execute([$id_projet]);
                
                // Enfin supprimer le projet
                $stmt = $pdo->prepare("DELETE FROM projets WHERE id = ?");
                $stmt->execute([$id_projet]);
                
                $message = "✅ Projet supprimé avec succès!";
            } catch (PDOException $e) {
                error_log("Erreur suppression projet: " . $e->getMessage());
                $message = "❌ Erreur lors de la suppression.";
            }
        }
    }
}

// 3. Récupérer tous les projets avec leurs infos
try {
    $stmt = $pdo->query("
        SELECT p.*, u.nom as createur_nom,
               (SELECT COUNT(*) FROM projets_forestiers WHERE id_projet = p.id) as nb_forestiers,
               (SELECT COUNT(*) FROM arbres WHERE id_projet = p.id) as nb_arbres
        FROM projets p
        LEFT JOIN utilisateurs u ON p.id_createur = u.id
        ORDER BY p.id DESC
    ");
    $projets = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erreur lecture projets: " . $e->getMessage());
}

include 'gererProjetVue.php';
?>
