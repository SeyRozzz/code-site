<?php
// fichier pour se connecter bdd
$host = 'mysql-locaris.alwaysdata.net';
$db   = 'locaris_bdd';
$user = 'locaris';
$pass = 'LVE@@291#';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // ✅ Active les erreurs SQL

];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}