    <?php
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=login");
    exit();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$role = $_SESSION['role'] ?? '';
$id = (int)($_GET['id'] ?? 0);
$message = "";

if ($id <= 0) {
    header("Location: index.php?page=carte");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM arbres WHERE id = ?");
$stmt->execute([$id]);
$arbre = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$arbre) {
    header("Location: index.php?page=carte");
    exit();
}

if ($role === 'forestier') {
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM projets_forestiers
        WHERE id_projet = ? AND id_forestier = ?
    ");
    $stmt->execute([$arbre['id_projet'], $_SESSION['user_id']]);

    if (!(bool) $stmt->fetchColumn()) {
        header("Location: index.php?page=carte&error=acces_refuse");
        exit();
    }
} elseif (!in_array($role, ['admin', 'superadmin'], true)) {
    header("Location: index.php?page=carte&error=acces_refuse");
    exit();
}

if ($role === 'forestier') {
    $stmt = $pdo->prepare("
        SELECT p.id, p.nom
        FROM projets p
        JOIN projets_forestiers pf ON p.id = pf.id_projet
        WHERE pf.id_forestier = ?
        ORDER BY p.nom ASC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $projets = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $projets = $pdo->query("SELECT id, nom FROM projets ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);
}

$allowedProjectIds = array_map('intval', array_column($projets, 'id'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    $essence = trim($_POST['essence'] ?? '');
    $hauteur = trim($_POST['hauteur'] ?? '');
    $diametre = trim($_POST['diametre'] ?? '');
    $lat = trim($_POST['latitude'] ?? '');
    $lon = trim($_POST['longitude'] ?? '');
    $id_projet = (int)($_POST['id_projet'] ?? 0);

    $arbre['essence'] = $essence;
    $arbre['hauteur'] = $hauteur;
    $arbre['diametre'] = $diametre;
    $arbre['latitude'] = $lat;
    $arbre['longitude'] = $lon;
    $arbre['id_projet'] = $id_projet;

    if (empty($_SESSION['csrf_token']) || !is_string($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
        $message = "Erreur de securite : jeton invalide.";
    } elseif (empty($essence) || empty($lat) || empty($lon) || $id_projet <= 0) {
        $message = "Tous les champs (y compris le projet) sont obligatoires.";
    } elseif (!in_array($id_projet, $allowedProjectIds, true)) {
        $message = "Vous n'avez pas acces au projet selectionne.";
    } elseif (!is_numeric($lat) || !is_numeric($lon)) {
        $message = "Les coordonnees doivent etre des nombres.";
    } else {
        try {
            $sql = "UPDATE arbres
                    SET essence = ?, hauteur = ?, diametre = ?, latitude = ?, longitude = ?, id_projet = ?
                    WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$essence, $hauteur, $diametre, $lat, $lon, $id_projet, $id]);

            header("Location: index.php?page=carte&msg=succes_modif");
            exit();
        } catch (PDOException $e) {
            error_log("Erreur mise a jour arbre: " . $e->getMessage());
            $message = "Erreur technique lors de la mise a jour.";
        }
    }
}

include 'modifierarbreVue.php';