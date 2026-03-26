<?php
// adminAdduser.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin')) {
    header("Location: index.php?page=accueil");
    exit();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = "Erreur de sécurité : requête invalide.";
    } else {
        $nom = trim($_POST['nom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $passwordBrut = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'forestier';

        if (empty($nom) || empty($email) || empty($passwordBrut)) {
            $message = "Tous les champs sont obligatoires.";
        }
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Adresse email invalide.";
        }
        elseif (!in_array($role, ['forestier', 'admin'])) {
            $message = "Rôle invalide.";
        }
        else {
            try {
                $passwordHash = password_hash($passwordBrut, PASSWORD_DEFAULT);
                
                $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)");
                $stmt->execute([$nom, $email, $passwordHash, $role]);
                
                // Succès : redirection
                header("Location: index.php?page=admin&success=useradded");
                exit();
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), '1062') !== false || strpos($e->getMessage(), 'Duplicate') !== false) {
                    $message = "Erreur : Cet email est déjà utilisé.";
                } else {
                    $message = "Erreur technique lors de l'insertion.";
                }
            }
        }
    }
}

include 'adminAdduserVue.php';