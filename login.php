<?php
// login.php

// 1. Initialisation de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

// 2. Si l'utilisateur est déjà connecté, on le redirige vers la carte
if (isset($_SESSION['role'])) {
    header("Location: index.php?page=carte");
    exit();
}

$erreur = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $mdp = $_POST['password'] ?? '';

    if (!empty($email) && !empty($mdp)) {
        // Récupération de l'utilisateur (on utilise ta colonne 'email')
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $auth_ok = false;

            // TEST 1 : Mot de passe haché (Sécurisé)
            if (password_verify($mdp, $user['mot_de_passe'])) {
                $auth_ok = true;
            } 
            // TEST 2 : Mot de passe en clair (Anciens comptes)
            else if ($mdp === $user['mot_de_passe']) {
                $auth_ok = true;
                
                // On en profite pour hacher le mot de passe pour la prochaine fois
                $newHash = password_hash($mdp, PASSWORD_DEFAULT);
                $update = $pdo->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?");
                $update->execute([$newHash, $user['id']]);
            }

            if ($auth_ok) {
                // Remplissage de la session
                $_SESSION['id'] = $user['id'];
                $_SESSION['nom'] = $user['nom'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['email'] = $user['email'];

                header("Location: index.php?page=carte");
                exit();
            } else {
                $erreur = "Mot de passe incorrect.";
            }
        } else {
            $erreur = "Identifiants invalides.";
        }
    } else {
        $erreur = "Veuillez remplir tous les champs.";
    }
}

// 3. Affichage de la vue
include 'loginVue.php';