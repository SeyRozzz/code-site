<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    header("Location: index.php?page=carte");
    exit();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$erreur = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $erreur = "Erreur de sécurité : requête invalide.";
    } else {
        $email = trim($_POST['email'] ?? '');
        $mdp = $_POST['password'] ?? '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erreur = "Adresse email invalide.";
        } elseif (empty($mdp)) {
            $erreur = "Mot de passe requis.";
        } else {
            $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                $auth_ok = false;

                if (password_verify($mdp, $user['mot_de_passe'])) {
                    $auth_ok = true;
                } elseif ($mdp === $user['mot_de_passe']) {
                    $auth_ok = true;
                    
                    // Hacher le mot de passe pour la prochaine fois
                    $newHash = password_hash($mdp, PASSWORD_DEFAULT);
                    $update = $pdo->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?");
                    $update->execute([$newHash, $user['id']]);
                }

                if ($auth_ok) {
                    // Remplissage de la session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['nom'] = $user['nom'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['email'] = $user['email'];
                    
                    // Régénérer l'ID de session pour éviter le session fixation
                    session_regenerate_id(true);

                    header("Location: index.php?page=carte");
                    exit();
                } else {
                    $erreur = "Mot de passe incorrect.";
                }
            } else {
                $erreur = "Identifiants invalides.";
            }
        }
    }
}

include 'loginVue.php';