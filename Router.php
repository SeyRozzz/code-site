<?php
class Router {
    private $routes = [];
    // Permet d'ajouter une route à notre routeur
    public function addRoute($nom, $fichier) {
        $this->routes[$nom] = $fichier;
    }

    // On ajoute $pdo ici pour qu'il soit accessible dans les fichiers inclus
    public function execute($pageDemandee, $pdo) {
        if (array_key_exists($pageDemandee, $this->routes)) {
            include $this->routes[$pageDemandee];
        } else {
            include 'accueil.php';
        }
    }
}