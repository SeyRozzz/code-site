<?php
// carte.php - Contrôleur de la page de visualisation
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';

// Sécurité : Génération du token CSRF pour les formulaires de la vue
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Récupération des paramètres de filtrage et tri
$search       = $_GET['q']          ?? '';
$sort         = $_GET['sort']       ?? 'arbres.id';
$dir          = $_GET['dir']        ?? 'ASC';
$filtreProjet = (int)($_GET['id_projet'] ?? 0);

// Whitelisting des colonnes pour éviter les injections SQL via ORDER BY
$allowedColumns = ['arbres.id', 'essence', 'hauteur', 'diametre', 'projets.nom'];
if (!in_array($sort, $allowedColumns)) {
    $sort = 'arbres.id';
}
$dir = (strtoupper($dir) === 'DESC') ? 'DESC' : 'ASC';

// 1. Récupérer la liste de tous les projets pour le menu déroulant (Filtre)
$projets = $pdo->query("SELECT id, nom FROM projets ORDER BY nom ASC")->fetchAll();

// 2. Requête principale avec JOIN pour lier Arbres, Projets et Utilisateurs
// On récupère le nom du projet et le nom du créateur de l'arbre
$sql = "SELECT arbres.*, 
               projets.nom AS projet_nom, 
               utilisateurs.nom AS createur_nom
        FROM arbres
        LEFT JOIN projets ON arbres.id_projet = projets.id
        LEFT JOIN utilisateurs ON arbres.id_createur = utilisateurs.id";

$params = [];
$where  = [];

// Filtre de recherche textuelle (Essence ou ID)
if (!empty($search)) {
    $where[] = "(essence LIKE ? OR arbres.id LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Filtre par projet spécifique
if ($filtreProjet > 0) {
    $where[] = "arbres.id_projet = ?";
    $params[] = $filtreProjet;
}

// Construction finale de la requête
if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY $sort $dir";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$arbres = $stmt->fetchAll(PDO::FETCH_ASSOC);

$nextDir = ($dir === 'ASC') ? 'DESC' : 'ASC';

// Appel de la vue pour l'affichage
include 'carteVue.php';