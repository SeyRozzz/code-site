<?php
// fichier pour se connecter bdd
// ⚠️ IMPORTANT: Utilisez des variables d'environnement en production!
// Exemple: $host = getenv('DB_HOST') ?: 'localhost';
$host = getenv('DB_HOST') ?: 'mysql-locaris.alwaysdata.net';
$db   = getenv('DB_NAME') ?: 'locaris_bdd';
$user = getenv('DB_USER') ?: 'locaris';
$pass = getenv('DB_PASS') ?: 'LVE@@291#';
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