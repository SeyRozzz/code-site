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

$role = $_SESSION['role'] ?? '';
$filtreProjet = (int)($_GET['id_projet'] ?? 0);

if ($role === 'forestier') {
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

$projetIdsAutorises = array_map('intval', array_column($projets, 'id'));

if ($role === 'forestier' && $filtreProjet > 0 && !in_array($filtreProjet, $projetIdsAutorises, true)) {
    header("Location: index.php?page=carte&error=acces_refuse");
    exit();
}

$sql = "SELECT arbres.*, 
               projets.nom AS projet_nom, 
               utilisateurs.nom AS createur_nom
        FROM arbres
        LEFT JOIN projets ON arbres.id_projet = projets.id
        LEFT JOIN utilisateurs ON arbres.id_createur = utilisateurs.id";

$params = [];
$whereClauses = [];

if ($role === 'forestier') {
    if (empty($projetIdsAutorises)) {
        $whereClauses[] = '1 = 0';
    } else {
        $placeholders = implode(', ', array_fill(0, count($projetIdsAutorises), '?'));
        $whereClauses[] = "arbres.id_projet IN ($placeholders)";
        $params = array_merge($params, $projetIdsAutorises);
    }
}

if ($filtreProjet > 0) {
    $whereClauses[] = "arbres.id_projet = ?";
    $params[] = $filtreProjet;
}

if (!empty($whereClauses)) {
    $sql .= ' WHERE ' . implode(' AND ', $whereClauses);
}

$sql .= " ORDER BY arbres.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$arbres = $stmt->fetchAll(PDO::FETCH_ASSOC);

// DEBUG TEMPORAIRE (à enlever après)
// var_dump($_SESSION); exit;

include 'carteVue.php';