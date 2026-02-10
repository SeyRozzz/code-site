<?php
// adminAdduser.php

// 1. On s'assure que la session est démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Inclusion de la connexion BDD (indispensable pour $pdo)
require_once 'config.php';

// 3. SÉCURITÉ : Seuls les admins et superadmins peuvent accéder ici
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin')) {
    header("Location: index.php?page=accueil");
    exit();
}

$message = "";

// 4. Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $passwordBrut = $_POST['password']; // Le mot de passe en clair (ex: "1234")
    $role = $_POST['role'];

    if (!empty($nom) && !empty($email) && !empty($passwordBrut)) {
        
        // --- HACHAGE SÉCURISÉ ---
        // C'est ici que la magie opère : on crypte le mot de passe
        $passwordHash = password_hash($passwordBrut, PASSWORD_DEFAULT);

        // Vérification si l'email existe déjà (pour éviter une erreur SQL moche)
        $check = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $check->execute([$email]);

        if ($check->rowCount() > 0) {
            $message = "Erreur : Cet email est déjà utilisé.";
        } else {
            // Insertion avec le mot de passe HACHÉ
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, password, role) VALUES (?, ?, ?, ?)");
            
            if ($stmt->execute([$nom, $email, $passwordHash, $role])) {
                // Succès : retour au panel admin
                header("Location: index.php?page=admin");
                exit();
            } else {
                $message = "Erreur technique lors de l'enregistrement.";
            }
        }
    } else {
        $message = "Tous les champs sont obligatoires.";
    }
}

// 5. Affichage de la vue
// (Attention à la casse exacte du nom de fichier)
include 'adminAdduserVue.php';