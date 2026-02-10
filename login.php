<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $mdp = $_POST['password'] ?? '';
    // recupération de l'utilisateur en fonction de l'email
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    // verification du mot de passe et de l'email dans la bdd
    if ($user && password_verify($mdp, $user['mot_de_passe'])) {
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['email'] = $user['email'];
        header("Location: index.php?page=carte");
        exit();
    }
    } else {
        $erreur = "Email ou mot de passe invalide.";
    }

include 'loginVue.php';