<?php
// login.php

// 1. Sécurité session : On démarre la session si ce n'est pas déjà fait par l'index
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

// 2. Si on est déjà connecté, inutile de rester ici -> direction la carte
if (isset($_SESSION['role'])) {
    header("Location: index.php?page=carte");
    exit();
}

$erreur = null;

// 3. Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nettoyage de l'email (enlève les espaces avant/après)
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        // A. Recherche de l'utilisateur par son email
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // B. VÉRIFICATION DU MOT DE PASSE (Haché)
        // password_verify compare le mot de passe saisi avec le hash en BDD
        if ($user && password_verify($password, $user['password'])) {
            
            // C. Connexion réussie : on remplit la session
            $_SESSION['id'] = $user['id'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // Redirection vers la carte
            header("Location: index.php?page=carte");
            exit();
        } else {
            $erreur = "Identifiants incorrects.";
        }
    } else {
        $erreur = "Veuillez remplir tous les champs.";
    }
}

// 4. On affiche la vue (le formulaire)
include 'loginVue.php';