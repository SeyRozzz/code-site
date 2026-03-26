<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['forestier', 'admin', 'superadmin'])) {
    header("Location: index.php?page=login");
    exit();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = "Erreur de sécurité : jeton invalide.";
    } else {
        $essence = trim($_POST['essence'] ?? '');
        $hauteur = $_POST['hauteur'] ?? 0;
        $diametre = $_POST['diametre'] ?? 0;
        $lat = $_POST['latitude'] ?? '';
        $lon = $_POST['longitude'] ?? '';
        $id_projet = (int)($_POST['id_projet'] ?? 0);
        $id_user = $_SESSION['user_id'];

        if ($_SESSION['role'] === 'forestier') {
            $stmt = $pdo->prepare("
                SELECT COUNT(*) FROM projets_forestiers 
                WHERE id_projet = ? AND id_forestier = ?
            ");
            $stmt->execute([$id_projet, $_SESSION['user_id']]);
            $has_access = $stmt->fetchColumn();
            
            if (!$has_access) {
                $message = "Erreur : vous n'avez pas accès à ce projet.";
                include 'ajouterVue.php';
                exit();
            }
        }

        if (empty($essence) || empty($lat) || empty($lon) || $id_projet <= 0) {
            $message = "Veuillez remplir tous les champs obligatoires.";
        } else {
            try {
                $sql = "INSERT INTO arbres (id_projet, id_createur, essence, hauteur, diametre, latitude, longitude) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$id_projet, $id_user, $essence, $hauteur, $diametre, $lat, $lon]);

                header("Location: index.php?page=carte&msg=succes_ajout");
                exit();
            } catch (PDOException $e) {
                error_log("Erreur Ajout : " . $e->getMessage());
                $message = "Erreur lors de l'enregistrement en base de données.";
            }
        }
    }
}

if ($_SESSION['role'] === 'forestier') {
    $stmt = $pdo->prepare("
        SELECT p.id, p.nom 
        FROM projets p
        JOIN projets_forestiers pf ON p.id = pf.id_projet
        WHERE pf.id_forestier = ?
        ORDER BY p.nom ASC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $projets = $stmt->fetchAll();
} else {
    // Admins voient tous les projets
    $projets = $pdo->query("SELECT id, nom FROM projets ORDER BY nom ASC")->fetchAll();
}

include 'ajouterVue.php';