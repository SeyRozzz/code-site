<?php
// modifier.php
// ✅ SÉCURISÉ : CSRF token + gestion erreurs améliorée

// Vérifier session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

// SÉCURITÉ : Il faut être connecté pour modifier
if (!isset($_SESSION['role'])) {
    header("Location: index.php?page=login");
    exit();
}

// ✅ Générer token CSRF si absent
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$id = (int)($_GET['id'] ?? 0);
$message = "";

// 1. Si pas d'ID valide, on retourne à la carte
if ($id <= 0) {
    header("Location: index.php?page=carte");
    exit();
}

// 2. Traitement du formulaire de modification (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ✅ Vérifier CSRF
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = "Erreur de sécurité : requête invalide.";
    } else {
        $essence = trim($_POST['essence'] ?? '');
        $hauteur = trim($_POST['hauteur'] ?? '');
        $diametre = trim($_POST['diametre'] ?? '');
        $lat = trim($_POST['latitude'] ?? '');
        $lon = trim($_POST['longitude'] ?? '');

        // ✅ Validation : Champs obligatoires
        if (empty($essence) || empty($lat) || empty($lon)) {
            $message = "Essence, latitude et longitude sont obligatoires.";
        }
        // ✅ Validation : Les coordonnées doivent être des nombres
        elseif (!is_numeric($lat) || !is_numeric($lon)) {
            $message = "Les coordonnées doivent être des nombres.";
        }
        else {
            try {
                $stmt = $pdo->prepare("UPDATE arbres SET essence=?, hauteur=?, diametre=?, latitude=?, longitude=? WHERE id=?");
                $stmt->execute([$essence, $hauteur, $diametre, $lat, $lon, $id]);
                header("Location: index.php?page=carte&msg=succes_modif");
                exit();
            } catch (PDOException $e) {
                error_log("Modifier - Erreur BDD: " . $e->getMessage());
                $message = "Erreur technique. Contactez l'administrateur.";
            }
        }
    }
}

// 3. Récupération des infos actuelles de l'arbre (pour pré-remplir)
$stmt = $pdo->prepare("SELECT * FROM arbres WHERE id = ?");
$stmt->execute([$id]);
$arbre = $stmt->fetch();

if (!$arbre) {
    header("Location: index.php?page=carte"); // L'arbre n'existe pas
    exit();
}

// Appel de la vue
include 'modifierarbreVue.php';