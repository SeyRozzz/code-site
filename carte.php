<?php
// carte.php

// 1. Démarrage session et vérification connexion
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si pas connecté -> Oust !
if (!isset($_SESSION['role'])) {
    header("Location: index.php?page=login");
    exit();
}

require_once 'config.php';

// 2. Gestion des paramètres de Tri et Recherche (100% PHP)
$search = $_GET['q'] ?? '';           // Mot clé recherché
$sort   = $_GET['sort'] ?? 'id';      // Colonne à trier
$dir    = $_GET['dir'] ?? 'ASC';      // Direction (ASC ou DESC)

// Sécurité : Liste blanche des colonnes autorisées pour le tri (anti-injection SQL)
$allowedColumns = ['id', 'essence', 'hauteur', 'diametre'];
if (!in_array($sort, $allowedColumns)) {
    $sort = 'id';
}

// Sécurité : Direction
$dir = strtoupper($dir);
if ($dir !== 'ASC' && $dir !== 'DESC') {
    $dir = 'ASC';
}

// 3. Construction de la requête SQL Dynamique
$sql = "SELECT * FROM arbres";
$params = [];

// A. Si recherche active
if (!empty($search)) {
    $sql .= " WHERE essence LIKE ? OR id LIKE ?";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// B. Ajout du tri
$sql .= " ORDER BY $sort $dir";

// 4. Exécution
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$arbres = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 5. Calcul de la direction inverse pour les liens de tri (pour la Vue)
$nextDir = ($dir === 'ASC') ? 'DESC' : 'ASC';

// 6. Appel de la vue
include 'carteVue.php';