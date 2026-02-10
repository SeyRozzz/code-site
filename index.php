<?php
session_start();
require_once 'config.php';
require_once 'Router.php';

$router = new Router();

// Routes principales
$router->addRoute('accueil', 'accueil.php');
$router->addRoute('carte', 'carte.php');

// Routes Authentification
$router->addRoute('login', 'login.php');
$router->addRoute('signup', 'signup.php'); 
$router->addRoute('logout', 'logout.php');

// Routes Administration
$router->addRoute('admin', 'admin.php');
$router->addRoute('adminAdduser', 'adminAdduser.php');
$router->addRoute('changer_role', 'changer_role.php');
$router->addRoute('supprimer_user', 'supprimer_user.php');
// Route suppression d'arbres
$router->addRoute('supprimer', 'supprimer.php');
$router->addRoute('modifier', 'modifierarbre.php');

// Exécution
$router->execute($_GET['page'] ?? 'accueil', $pdo);