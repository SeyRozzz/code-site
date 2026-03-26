<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=login");
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$filtreProjet = (int)($_GET['id_projet'] ?? 0);

if ($_SESSION['role'] === 'forestier') {
    $projets = $pdo->prepare("
        SELECT DISTINCT p.id, p.nom 
        FROM projets p
        JOIN projets_forestiers pf ON p.id = pf.id_projet
        WHERE pf.id_forestier = ?
        ORDER BY p.nom ASC
    ");
    $projets->execute([$_SESSION['user_id']]);
    $projets = $projets->fetchAll(PDO::FETCH_ASSOC);
} else {
    $projets = $pdo->query("SELECT id, nom FROM projets ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);
}

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