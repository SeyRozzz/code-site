<?php
// ajouterArbreProjet.php - Ajouter un arbre depuis la page du projet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

// 🔐 PROTECTION : accès uniquement si connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=login");
    exit();
}

// Récupérer l'ID du projet
$id_projet = (int)($_GET['id'] ?? 0);

if ($id_projet <= 0) {
    header("Location: index.php?page=carte");
    exit();
}

// 🔒 SÉCURITÉ : Vérifier que le projet existe et que le user a accès
try {
    $stmt = $pdo->prepare("SELECT p.id, p.nom FROM projets p WHERE p.id = ?");
    $stmt->execute([$id_projet]);
    $projet = $stmt->fetch();
    
    if (!$projet) {
        header("Location: index.php?page=carte");
        exit();
    }
    
    // Pour les forestiers: vérifier l'accès au projet
    if ($_SESSION['role'] === 'forestier') {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM projets_forestiers 
            WHERE id_projet = ? AND id_forestier = ?
        ");
        $stmt->execute([$id_projet, $_SESSION['user_id']]);
        $has_access = $stmt->fetchColumn();
        
        if (!$has_access) {
            header("Location: index.php?page=carte&error=acces_refuse");
            exit();
        }
    }
} catch (PDOException $e) {
    error_log("Erreur vérif projet: " . $e->getMessage());
    header("Location: index.php?page=carte");
    exit();
}

// ✅ Générer token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = "";

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = "❌ Erreur de sécurité: requête invalide.";
    } else {
        $essence = trim($_POST['essence'] ?? '');
        $hauteur = floatval($_POST['hauteur'] ?? 0);
        $diametre = floatval($_POST['diametre'] ?? 0);
        $lat = trim($_POST['latitude'] ?? '');
        $lon = trim($_POST['longitude'] ?? '');
        
        if (empty($essence) || empty($lat) || empty($lon)) {
            $message = "❌ Veuillez remplir tous les champs obligatoires.";
        } elseif (!is_numeric($lat) || !is_numeric($lon)) {
            $message = "❌ Les coordonnées doivent être des nombres.";
        } else {
            try {
                $sql = "INSERT INTO arbres (id_projet, id_createur, essence, hauteur, diametre, latitude, longitude) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$id_projet, $_SESSION['user_id'], $essence, $hauteur, $diametre, $lat, $lon]);
                
                $message = "✅ Arbre ajouté avec succès!";
                // Rediriger après succès
                header("Location: index.php?page=carte&id_projet=" . $id_projet . "&msg=succes_ajout");
                exit();
            } catch (PDOException $e) {
                error_log("Erreur Ajout Arbre: " . $e->getMessage());
                $message = "❌ Erreur lors de l'enregistrement.";
            }
        }
    }
}

include 'ajouterArbreProjetVue.php';
?>
