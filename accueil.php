<?php

$totalArbres = $pdo->query("SELECT COUNT(*) FROM arbres")->fetchColumn();
$moyenneHauteur = $pdo->query("SELECT ROUND(AVG(hauteur), 2) FROM arbres")->fetchColumn();
$essencePopulaire = $pdo->query("SELECT essence FROM arbres GROUP BY essence ORDER BY COUNT(*) DESC LIMIT 1")->fetchColumn();

$totalArbres = $totalArbres ? $totalArbres : 0;
$moyenneHauteur = $moyenneHauteur ? $moyenneHauteur : 0;
$essencePopulaire = $essencePopulaire ? $essencePopulaire : "Aucune";

include 'accueilVue.php';