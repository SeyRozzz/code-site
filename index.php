<?php
session_start();
require_once 'config.php';
require_once 'Router.php';

$router = new Router();

$router->addRoute('accueil', 'accueil.php');
$router->addRoute('carte', 'carte.php');
$router->addRoute('login', 'login.php');
$router->addRoute('logout', 'logout.php');
$router->addRoute('admin', 'admin.php');
$router->addRoute('adminAdduser', 'adminAdduser.php');
$router->addRoute('changer_role', 'changer_role.php');
$router->addRoute('supprimer_user', 'supprimer_user.php');
$router->addRoute('supprimer', 'supprimer.php');
$router->addRoute('modifier', 'modifierarbre.php');
// Nouveau
$router->addRoute('projets', 'projets.php');
$router->addRoute('supprimer_projet', 'supprimer_projet.php');

$router->execute($_GET['page'] ?? 'accueil', $pdo);