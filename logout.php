<?php
session_start();       // On récupère la session en cours
session_unset();       // On vide les variables
session_destroy();     // On détruit la session
header("Location: index.php?page=login"); // On renvoie au login
exit();