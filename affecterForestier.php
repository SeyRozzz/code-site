<?php
// affecterForestier.php - Admin affecte les forestiers aux projets
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

$id_projet = (int)($_GET['id'] ?? 0);
$message = "";
$projet = null;
$forestiers_libres = [];
$forestiers_affectes = [];

// Vérifier que le projet existe
if ($id_projet > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM projets WHERE id = ?");
        $stmt->execute([$id_projet]);
        $projet = $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Erreur lecture projet: " . $e->getMessage());
    }
}

if (!$projet) {
    header("Location: index.php?page=gererProjet");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ajouter') {
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = "❌ Erreur de sécurité: token CSRF invalide";
    } else {
        $id_forestier = (int)($_POST['id_forestier'] ?? 0);
        
        if ($id_forestier > 0) {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO projets_forestiers (id_projet, id_forestier) 
                    VALUES (?, ?)
                ");
                $stmt->execute([$id_projet, $id_forestier]);
                $message = "Forestier affecté avec succès!";
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), '1146') !== false) {
                    // Table n'existe pas
                    $message = "⚠️ La table projets_forestiers n'existe pas encore. Créez-la d'abord avec install_projets_forestiers.php";
                } elseif (strpos($e->getMessage(), 'Duplicate') !== false) {
                    $message = "❌ Ce forestier est déjà affecté à ce projet.";
                } else {
                    $message = "❌ Erreur: " . $e->getMessage();
                }
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'retirer') {
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = "❌ Erreur de sécurité: token CSRF invalide";
    } else {
        $id_affectation = (int)($_POST['id_affectation'] ?? 0);
        
        if ($id_affectation > 0) {
            try {
                $stmt = $pdo->prepare("DELETE FROM projets_forestiers WHERE id = ? AND id_projet = ?");
                $stmt->execute([$id_affectation, $id_projet]);
                $message = "Forestier retiré du projet.";
            } catch (PDOException $e) {
                error_log("Erreur retrait forestier: " . $e->getMessage());
                if (strpos($e->getMessage(), '1146') !== false) {
                    // Table n'existe pas
                    $message = "⚠️ La table projets_forestiers n'existe pas encore.";
                } else {
                    $message = "❌ Erreur technique lors du retrait.";
                }
            }
        }
    }
}


$forestiers_affectes = [];
try {
    $stmt = $pdo->prepare("
        SELECT pf.id, u.id as user_id, u.nom, u.email
        FROM projets_forestiers pf
        JOIN utilisateurs u ON pf.id_forestier = u.id
        WHERE pf.id_projet = ?
        ORDER BY u.nom ASC
    ");
    $stmt->execute([$id_projet]);
    $forestiers_affectes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Table n'existe pas encore, c'est normal
    error_log("Table projets_forestiers n'existe pas encore: " . $e->getMessage());
}

$forestiers_libres = [];
try {
    $ids_affectes = array_map(fn($f) => $f['user_id'], $forestiers_affectes);
    
    $sql = "SELECT id, nom, email FROM utilisateurs WHERE role = 'forestier'";
    if (!empty($ids_affectes)) {
        $placeholders = str_repeat('?,', count($ids_affectes) - 1) . '?';
        $sql .= " AND id NOT IN ($placeholders)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($ids_affectes);
    } else {
        $stmt = $pdo->query($sql);
    }
    $forestiers_libres = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erreur lecture forestiers libres: " . $e->getMessage());
}

include 'affecterForestierVue.php';
?>
