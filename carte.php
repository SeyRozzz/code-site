<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

// 🔐 PROTECTION : accès uniquement si connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=login");
    exit;
}

// 🛡️ CSRF (correct)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 🎯 FILTRE
$filtreProjet = (int)($_GET['id_projet'] ?? 0);

// 📦 PROJETS
$projets = $pdo->query("SELECT id, nom FROM projets ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);

// 🌳 REQUÊTE ARBRES
$sql = "SELECT arbres.*, 
               projets.nom AS projet_nom, 
               utilisateurs.nom AS createur_nom
        FROM arbres
        LEFT JOIN projets ON arbres.id_projet = projets.id
        LEFT JOIN utilisateurs ON arbres.id_createur = utilisateurs.id";

$params = [];

if ($filtreProjet > 0) {
    $sql .= " WHERE arbres.id_projet = ?";
    $params[] = $filtreProjet;
}

$sql .= " ORDER BY arbres.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$arbres = $stmt->fetchAll(PDO::FETCH_ASSOC);

// DEBUG TEMPORAIRE (à enlever après)
// var_dump($_SESSION); exit;

include 'carteVue.php';