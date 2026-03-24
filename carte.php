<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'config.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$search     = $_GET['q']          ?? '';
$sort       = $_GET['sort']       ?? 'id';
$dir        = $_GET['dir']        ?? 'ASC';
$filtreProjet = (int)($_GET['id_projet'] ?? 0);

$allowedColumns = ['arbres.id', 'essence', 'hauteur', 'diametre', 'projets.nom'];
if (!in_array($sort, $allowedColumns)) $sort = 'arbres.id';
$dir = (strtoupper($dir) === 'DESC') ? 'DESC' : 'ASC';

// Récupérer tous les projets pour le filtre dropdown
$projets = $pdo->query("SELECT id, nom FROM projets ORDER BY nom ASC")->fetchAll();

// Requête principale avec JOIN
$sql    = "SELECT arbres.*, projets.nom AS projet_nom
           FROM arbres
           LEFT JOIN projets ON arbres.id_projet = projets.id";
$params = [];
$where  = [];

if (!empty($search)) {
    $where[] = "(essence LIKE ? OR arbres.id LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($filtreProjet > 0) {
    $where[] = "arbres.id_projet = ?";
    $params[] = $filtreProjet;
}
if (!empty($where)) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY $sort $dir";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$arbres  = $stmt->fetchAll(PDO::FETCH_ASSOC);
$nextDir = ($dir === 'ASC') ? 'DESC' : 'ASC';

include 'carteVue.php';