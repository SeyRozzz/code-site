<?php
// 🔧 Script SQL pour créer la table de liaison forestiers ↔ projets
session_start();
require_once 'config.php';

echo "<h1>🔧 Installation - Table projets_forestiers</h1>";
echo "<pre>";

try {
    // Vérifier si la table existe déjà
    $check = $pdo->query("SHOW TABLES LIKE 'projets_forestiers'")->fetchAll();
    
    if (!empty($check)) {
        echo "✓ Table 'projets_forestiers' existe déjà\n";
    } else {
        echo "⏳ Création de la table 'projets_forestiers'...\n";
        
        // Créer la table
        $sql = "CREATE TABLE projets_forestiers (
            id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            id_projet INT(11) NOT NULL,
            id_forestier INT(11) NOT NULL,
            date_affectation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_affectation (id_projet, id_forestier),
            FOREIGN KEY (id_projet) REFERENCES projets(id) ON DELETE CASCADE,
            FOREIGN KEY (id_forestier) REFERENCES utilisateurs(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        $pdo->exec($sql);
        echo "✓ Table créée avec succès!\n\n";
    }
    
    // Afficher la structure
    echo "📋 Structure de la table:\n";
    $columns = $pdo->query("DESCRIBE projets_forestiers")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo "  - {$col['Field']}: {$col['Type']}\n";
    }
    echo "\n✓ Installation terminée!\n";
    
} catch (Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
}

echo "</pre>";
echo "<p><a href='index.php?page=admin'>← Aller au panel admin</a></p>";
?>
