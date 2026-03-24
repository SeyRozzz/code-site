<?php
// index.php
session_start();
require_once 'config.php';
require_once 'Router.php';

$router = new Router();

// --- ROUTES GÉNÉRALES ---
$router->addRoute('accueil', 'accueil.php');
$router->addRoute('carte', 'carte.php');
$router->addRoute('login', 'login.php');
$router->addRoute('logout', 'logout.php');

// --- GESTION DES ARBRES ---
$router->addRoute('ajouter', 'ajouter.php');           // Ajouté pour la création
$router->addRoute('modifier', 'modifier.php');         // Mis à jour
$router->addRoute('supprimer', 'supprimer.php');       // Mis à jour (sécurisé)

// --- GESTION DES PROJETS (Nouvelle BDD) ---
$router->addRoute('projets', 'ajouterProjet.php');
$router->addRoute('supprimer_projet', 'supprimerProjet.php');

// --- PANEL ADMINISTRATION ---
$router->addRoute('admin', 'admin.php');
$router->addRoute('adminAdduser', 'adminAdduser.php');
$router->addRoute('changer_role', 'changer_role.php');
$router->addRoute('supprimer_user', 'supprimer_user.php');

// Exécution du routage
$router->execute($_GET['page'] ?? 'accueil', $pdo);