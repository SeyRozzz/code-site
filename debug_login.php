<?php
// 🔍 DEBUG - À SUPPRIMER APRÈS VÉRIFICATION
session_start();
require_once 'config.php';

echo "<h1>🔍 DEBUG - Test de Connexion</h1>";
echo "<pre>";

try {
    // Test 1: Connexion BDD
    echo "✓ Connexion PDO: OK\n\n";
    
    // Test 2: Table utilisateurs existe?
    $tables = $pdo->query("SHOW TABLES LIKE 'utilisateurs'")->fetchAll();
    if (!empty($tables)) {
        echo "✓ Table utilisateurs existe\n\n";
        
        // Test 3: Structure de la table
        echo "📋 Structure de la table:\n";
        $columns = $pdo->query("DESCRIBE utilisateurs")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $col) {
            echo "  - {$col['Field']}: {$col['Type']}\n";
        }
        echo "\n";
        
        // Test 4: Utilisateurs existants
        echo "👥 Utilisateurs en base:\n";
        $users = $pdo->query("SELECT id, nom, email, role FROM utilisateurs")->fetchAll(PDO::FETCH_ASSOC);
        if (empty($users)) {
            echo "  ⚠️ AUCUN UTILISATEUR EN BASE!\n";
        } else {
            foreach ($users as $user) {
                echo "  - ID: {$user['id']}, Nom: {$user['nom']}, Email: {$user['email']}, Role: {$user['role']}\n";
            }
        }
        echo "\n";
        
        // Test 5: Tester avec un utilisateur spécifique
        echo "🔐 Test de mot de passe (exemple):\n";
        if (!empty($users)) {
            $testUser = $users[0];
            $stmt = $pdo->prepare("SELECT mot_de_passe FROM utilisateurs WHERE id = ?");
            $stmt->execute([$testUser['id']]);
            $pwdHash = $stmt->fetchColumn();
            echo "  Email: {$testUser['email']}\n";
            echo "  Hash stocké: " . substr($pwdHash, 0, 20) . "...\n";
            echo "  Type: " . (strpos($pwdHash, '$2') === 0 ? 'HASHÉ (bcrypt)' : 'EN CLAIR') . "\n";
        }
        
    } else {
        echo "❌ Table utilisateurs N'EXISTE PAS!\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERREUR BDD: " . $e->getMessage() . "\n";
}

echo "</pre>";
echo "<p><a href='index.php?page=login'>← Retour au login</a></p>";
?>
