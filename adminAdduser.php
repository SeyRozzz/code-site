<?php
// adminAdduser.php

// SÉCURITÉ : Seuls les admins/superadmins peuvent créer des gens
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin')) {
    header("Location: index.php?page=accueil");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $passwordBrut = $_POST['password']; // Le mot de passe en clair (ex: "1234")
    $role = $_POST['role'];

    if (!empty($nom) && !empty($email) && !empty($passwordBrut)) {
        
        // HACHAGE SÉCURISÉ AVANT ENREGISTREMENT
        $passwordHash = password_hash($passwordBrut, PASSWORD_DEFAULT);

        // On insère le mot de passe HACHÉ ($passwordHash) et non le brut
        $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, password, role) VALUES (?, ?, ?, ?)");
        
        if ($stmt->execute([$nom, $email, $passwordHash, $role])) {
            header("Location: index.php?page=admin");
            exit();
        } else {
            $message = "Erreur : Cet email est peut-être déjà utilisé.";
        }
    } else {
        $message = "Tous les champs sont obligatoires.";
    }
}

include 'adminAddUserVue.php';