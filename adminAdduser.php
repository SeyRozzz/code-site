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

// Génération du token CSRF si absent
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = "";

// 4. Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 🔍 DEBUG
    error_log("DEBUG: POST reçu. CSRF token: " . ($_POST['csrf_token'] ?? 'ABSENT'));
    error_log("DEBUG: SESSION token: " . ($_SESSION['csrf_token'] ?? 'ABSENT'));
    
    // ✅ VÉRIFICATION CSRF
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = "Erreur de sécurité : requête invalide.";
        error_log("DEBUG: CSRF FAILED");
    } else {
        $nom = trim($_POST['nom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $passwordBrut = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'forestier';

        error_log("DEBUG: nom=$nom, email=$email, role=$role");

        // ✅ VALIDATION : Les champs sont remplis
        if (empty($nom) || empty($email) || empty($passwordBrut)) {
            $message = "Tous les champs sont obligatoires.";
            error_log("DEBUG: Champs vides");
        }
        // ✅ VALIDATION : Email valide
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Adresse email invalide.";
            error_log("DEBUG: Email invalide: $email");
        }
        // ✅ VALIDATION : Rôle autorisé (whitelist)
        elseif (!in_array($role, ['forestier', 'admin'])) {
            $message = "Rôle invalide.";
            error_log("DEBUG: Rôle invalide: $role");
        }
        else {
            error_log("DEBUG: Toutes les validations OK, insertion...");
            try {
                // --- HACHAGE SÉCURISÉ ---
                $passwordHash = password_hash($passwordBrut, PASSWORD_DEFAULT);
                
                // Insertion avec le mot de passe HACHÉ
                $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)");
                $stmt->execute([$nom, $email, $passwordHash, $role]);
                error_log("DEBUG: Insertion réussie!");
                
                // Succès : retour au panel admin
                header("Location: index.php?page=admin");
                exit();
            } catch (PDOException $e) {
                error_log("DEBUG: Exception PDO: " . $e->getMessage());
                // ✅ Vérifier si c'est une violation de contrainte UNIQUE (error code 23000)
                if (strpos($e->getMessage(), '1062') !== false || strpos($e->getMessage(), 'Duplicate') !== false) {
                    $message = "Erreur : Cet email est déjà utilisé.";
                } else {
                    // Autres erreurs BDD
                    error_log("AdminAdduser - Erreur BDD: " . $e->getMessage());
                    $message = "Erreur technique. Contactez l'administrateur.";
                }
            }
        }
    }
}

// 5. Affichage de la vue
// (Attention à la casse exacte du nom de fichier)
include 'adminAdduserVue.php';