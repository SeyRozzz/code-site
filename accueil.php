<?php
/**
 * Contrôleur : accueil.php
 */
// 1. Calcul du nombre total d'arbres
$totalArbres = $pdo->query("SELECT COUNT(*) FROM arbres")->fetchColumn();

// 2. Calcul de la hauteur moyenne
$moyenneHauteur = $pdo->query("SELECT ROUND(AVG(hauteur), 2) FROM arbres")->fetchColumn();

// 3. Trouve l'essence la plus fréquente
$essencePopulaire = $pdo->query("SELECT essence FROM arbres GROUP BY essence ORDER BY COUNT(*) DESC LIMIT 1")->fetchColumn();

// Sécurité si la base est vide, ça évite d'afficher "0"
$totalArbres = $totalArbres ? $totalArbres : 0;
$moyenneHauteur = $moyenneHauteur ? $moyenneHauteur : 0;
$essencePopulaire = $essencePopulaire ? $essencePopulaire : "Aucune";

// 4. Appel du html
include 'accueilVue.php';