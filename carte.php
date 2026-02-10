<?php
/**
 * Contrôleur pour la page Carte.
 * Il récupère les données de l'inventaire GNSS (essence, hauteur, diamètre).
 */

// On utilise $pdo qui a été injecté par le Router pour exécuter la requête SQL
$stmt = $pdo->query("SELECT id, essence, hauteur, diametre, latitude, longitude FROM arbres");
$arbres = $stmt->fetchAll();

// Une fois les données récupérées, on appelle la "Vue" pour l'affichage
include 'carteVue.php';