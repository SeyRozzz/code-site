<?php
// supprimer.php

// Sécurité : On vérifie si l'utilisateur est admin (seul l'admin peut supprimer un arbre)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?page=carte");
    exit();
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM arbres WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: index.php?page=carte");
exit();