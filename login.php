<?php
// login.php

require_once 'config.php';

// Si on est déjà connecté, on va direct à la carte
if (isset($_SESSION['role'])) {
    header("Location: index.php?page=carte");
    exit();
}

$erreur = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 1. On cherche l'utilisateur par son email
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // 2. VÉRIFICATION SÉCURISÉE DU MOT DE PASSE
    // On utilise password_verify qui compare le texte clair avec le hash
    if ($user && password_verify($password, $user['password'])) {
        
        // Connexion réussie : on remplit la session
        $_SESSION['id'] = $user['id'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        header("Location: index.php?page=carte");
        exit();
    } else {
        $erreur = "Identifiants incorrects.";
    }
}

// On affiche la vue (le formulaire)
include 'loginVue.php';