<?php
// carte.php - Contrôleur de la page de visualisation
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';

// 1. SÉCURITÉ : Génération du token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 2. RÉCUPÉRATION DES FILTRES (via GET)
$search       = $_GET['q'] ?? '';
$sort         = $_GET['sort'] ?? 'arbres.id';
$dir          = $_GET['dir'] ?? 'ASC';
$filtreProjet = (int)($_GET['id_projet'] ?? 0);

// Whitelisting pour le tri (sécurité SQL)
$allowedColumns = ['arbres.id', 'essence', 'hauteur', 'diametre', 'projet_nom', 'createur_nom'];
if (!in_array($sort, $allowedColumns)) {
    $sort = 'arbres.id';
}
$dir = (strtoupper($dir) === 'DESC') ? 'DESC' : 'ASC';

// 3. RÉCUPÉRATION DES PROJETS (pour le menu déroulant)
$projets = $pdo->query("SELECT id, nom FROM projets ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);

// 4. CONSTRUCTION DE LA REQUÊTE PRINCIPALE (JOIN pour nom projet et créateur)
$sql = "SELECT arbres.*, 
               projets.nom AS projet_nom, 
               utilisateurs.nom AS createur_nom
        FROM arbres
        LEFT JOIN projets ON arbres.id_projet = projets.id
        LEFT JOIN utilisateurs ON arbres.id_createur = utilisateurs.id";

$params = [];
$where  = [];

// Filtre par texte (essence)
if (!empty($search)) {
    $where[] = "essence LIKE ?";
    $params[] = "%$search%";
}

// Filtre par projet (venant du menu déroulant)
if ($filtreProjet > 0) {
    $where[] = "arbres.id_projet = ?";
    $params[] = $filtreProjet;
}

// Assemblage des conditions WHERE
if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

// Application du tri
$sql .= " ORDER BY $sort $dir";

// Exécution
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$arbres = $stmt->fetchAll(PDO::FETCH_ASSOC);

$nextDir = ($dir === 'ASC') ? 'DESC' : 'ASC';

// 5. CHARGEMENT DE LA VUE
include 'carteVue.php';