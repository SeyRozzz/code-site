<?php
// modifier.php

// SÉCURITÉ : Il faut être connecté pour modifier
if (!isset($_SESSION['role'])) {
    header("Location: index.php?page=login");
    exit();
}

$id = $_GET['id'] ?? null;
$message = "";

// 1. Si pas d'ID, on retourne à la carte
if (!$id) {
    header("Location: index.php?page=carte");
    exit();
}

// 2. Traitement du formulaire de modification (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $essence = $_POST['essence'];
    $hauteur = $_POST['hauteur'];
    $diametre = $_POST['diametre'];
    $lat = $_POST['latitude'];
    $lon = $_POST['longitude'];

    if (!empty($essence) && !empty($lat) && !empty($lon)) {
        $stmt = $pdo->prepare("UPDATE arbres SET essence=?, hauteur=?, diametre=?, latitude=?, longitude=? WHERE id=?");
        if ($stmt->execute([$essence, $hauteur, $diametre, $lat, $lon, $id])) {
            header("Location: index.php?page=carte&msg=succes_modif");
            exit();
        } else {
            $message = "Erreur lors de la modification.";
        }
    } else {
        $message = "Champs obligatoires manquants.";
    }
}

// 3. Récupération des infos actuelles de l'arbre (pour pré-remplir)
$stmt = $pdo->prepare("SELECT * FROM arbres WHERE id = ?");
$stmt->execute([$id]);
$arbre = $stmt->fetch();

if (!$arbre) {
    header("Location: index.php?page=carte"); // L'arbre n'existe pas
    exit();
}

// Appel de la vue
include 'modifierarbreVue.php';