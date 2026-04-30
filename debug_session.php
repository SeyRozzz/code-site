<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$debugEnabled = getenv('APP_DEBUG') === '1';
$isSuperadmin = isset($_SESSION['role']) && $_SESSION['role'] === 'superadmin';

if (!$debugEnabled || !$isSuperadmin) {
    http_response_code(404);
    exit('Page introuvable');
}

echo "<h1>🔍 Diagnostic de la Session & CSRF</h1>";
echo "<pre>";

// Test 1: Session PHP
echo "=== SESSION PHP ===\n";
echo "Status: " . session_status() . " (1=disabled, 2=none, 0=active)\n";
echo "Session ID: " . session_id() . "\n";
echo "Session Name: " . session_name() . "\n";
echo "Session Save Path: " . session_save_path() . "\n";
echo "PHP SAPI: " . php_sapi_name() . "\n\n";

// Test 2: Cookies
echo "=== COOKIES ===\n";
echo "Cookies reçus: " . count($_COOKIE) . "\n";
if (isset($_COOKIE[session_name()])) {
    echo "✓ Cookie PHPSESSID présent: " . substr($_COOKIE[session_name()], 0, 10) . "...\n";
} else {
    echo "❌ Cookie PHPSESSID ABSENT!\n";
    echo "Cookies disponibles: " . implode(", ", array_keys($_COOKIE)) . "\n";
}
echo "\n";

// Test 3: $_SESSION
echo "=== SESSION STORAGE ===\n";
echo "Données en $_SESSION:\n";
$_SESSION['test_' . time()] = "test_value_" . time();
foreach ($_SESSION as $key => $value) {
    echo "  - $key = " . (is_array($value) ? json_encode($value) : substr((string)$value, 0, 50)) . "\n";
}
echo "\n";

// Test 4: Token CSRF
echo "=== TOKEN CSRF ===\n";
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    echo "✓ Token créé: " . substr($_SESSION['csrf_token'], 0, 20) . "...\n";
} else {
    echo "✓ Token existant: " . substr($_SESSION['csrf_token'], 0, 20) . "...\n";
}
echo "\n";

// Test 5: Méthode de requête
echo "=== REQUÊTE ===\n";
echo "Méthode: " . $_SERVER['REQUEST_METHOD'] . "\n";
echo "URL: " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "\n";
echo "\n";

echo "Session (var_dump):\n";
var_dump($_SESSION);

echo "</pre>";
echo "<p><a href='" . htmlspecialchars($_SERVER['REQUEST_URI']) . "'>🔄 Rafraîchir cette page</a></p>";
echo "<p><a href='index.php?page=login'>← Retour au login</a></p>";
?>
