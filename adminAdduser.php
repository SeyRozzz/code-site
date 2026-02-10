<?php
// admin.php

//  on autorise admin et superadmin
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin')) {
    // Si ta pas le role on te met a l'accueil
    header("Location: index.php?page=accueil");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $email = $_POST['email'] ?? '';
    $mdpRaw = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'forestier';

    if (!empty($nom) && !empty($email) && !empty($mdpRaw)) {
        // on hache le mot de passe
        $mdpHash = password_hash($mdpRaw, PASSWORD_DEFAULT);

        //  on vérifie si l'email existe déjà
        $check = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $check->execute([$email]);
        
        if ($check->fetch()) {
            $message = "❌ Cet email est déjà utilisé.";
        } else {
            // insertion dans la bdd
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$nom, $email, $mdpHash, $role])) {
                header("Location: index.php?page=admin&msg=success");
                exit();
            } else {
                $message = " Erreur lors de la création.";
            }
        }
    } else {
        $message = " Veuillez remplir tous les champs.";
    }
}

include 'adminAdduserVue.php';