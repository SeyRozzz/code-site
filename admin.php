<?php
//  si pas admin, on renvoie à l'accueil
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin')) {
    // dcp on redirige a l'accueil
    header("Location: index.php?page=accueil");
    exit();
}
// recupération de tous les comptes pour le panel
$stmt = $pdo->query("SELECT id, nom, email, role FROM utilisateurs ORDER BY nom ASC");
$users = $stmt->fetchAll();

include 'adminVue.php';