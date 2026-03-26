<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des projets</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="animations.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        :root {
            --primary: #40D969;
            --bg: #1A1A1A;
            --bg-secondary: #2D2D2D;
            --text: #E8E8E8;
            --text-secondary: #B0B0B0;
            --border: #3D3D3D;
            --error: #FF6B6B;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg);
            color: var(--text);
        }
        
        .header {
            padding: 16px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            background: var(--bg);
            z-index: 100;
        }
        
        h1 {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }
        
        .header-right {
            display: flex;
            gap: 12px;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 24px;
        }
        
        .section {
            margin-bottom: 32px;
        }
        
        .section h2 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 16px;
        }
        
        .form-card {
            background: var(--bg-secondary);
            padding: 24px;
            border-radius: 12px;
            border: 1px solid var(--border);
        }
        
        .form-group {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
        }
        
        .form-group input {
            flex: 1;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.2s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--bg);
            box-shadow: 0 0 0 3px rgba(64, 217, 105, 0.2);
        }
        
        .btn {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            opacity: 0.85;
        }
        
        .btn-secondary {
            background: var(--bg-secondary);
            color: var(--text);
            border: 1px solid var(--border);
        }
        
        .btn-secondary:hover {
            background: var(--border);
        }
        
        .btn-small {
            padding: 8px 12px;
            font-size: 13px;
        }
        
        .btn-danger {
            background: #FFE5E5;
            color: var(--error);
            border: 1px solid #FFE5E5;
        }
        
        .btn-danger:hover {
            background: #FF9999;
            color: white;
        }
        
        .message {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 14px;
        }
        
        .message.success {
            background: rgba(52, 199, 89, 0.1);
            color: var(--primary);
            border: 1px solid rgba(52, 199, 89, 0.2);
        }
        
        .message.error {
            background: #FFE5E5;
            color: var(--error);
            border: 1px solid #FFE5E5;
        }
        
        .projects-list {
            display: grid;
            gap: 12px;
        }
        
        .project-card {
            background: var(--bg);
            padding: 16px;
            border-radius: 12px;
            border: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.2s;
        }
        
        .project-card:hover {
            border-color: var(--primary);
            box-shadow: 0 2px 8px rgba(52, 199, 89, 0.1);
        }
        
        .project-info h3 {
            font-size: 16px;
            font-weight: 600;
            margin: 0 0 8px 0;
        }
        
        .project-meta {
            display: flex;
            gap: 16px;
            font-size: 13px;
            color: var(--text-secondary);
        }
        
        .project-actions {
            display: flex;
            gap: 8px;
        }
        
        .empty {
            text-align: center;
            padding: 32px 24px;
            color: var(--text-secondary);
        }
        
        .link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 24px;
            transition: opacity 0.2s;
        }
        
        .link:hover {
            opacity: 0.7;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Gestion des projets</h1>
    <div class="header-right">
        <a href="index.php?page=admin" class="btn btn-secondary btn-small">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
</div>

<div class="container">
    <?php if (!empty($message)): ?>
        <div class="alert <?= strpos($message, 'succès') !== false || strpos($message, '✅') !== false ? 'alert-success' : 'alert-error' ?>">
            <?= htmlspecialchars(str_replace(['✅', '❌'], '', $message)) ?>
        </div>
    <?php endif; ?>
    
    <div class="section">
        <h2>Créer un nouveau projet</h2>
        <div class="form-card">
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="action" value="ajouter">
                
                <div class="form-group">
                    <input type="text" name="nom" placeholder="Nom du projet" required>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Créer
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="section">
        <h2>Projets existants</h2>
        
        <?php if (empty($projets)): ?>
            <div class="empty">
                <p>Aucun projet pour le moment.</p>
            </div>
        <?php else: ?>
            <div class="projects-list">
                <?php foreach ($projets as $projet): ?>
                    <div class="project-card">
                        <div class="project-info">
                            <h3><?= htmlspecialchars($projet['nom']) ?></h3>
                            <div class="project-meta">
                                <span><i class="fas fa-user"></i> <?= htmlspecialchars($projet['createur_nom'] ?? 'Admin') ?></span>
                                <span><i class="fas fa-users"></i> <?= (int)$projet['nb_forestiers'] ?> agent(s)</span>
                                <span><i class="fas fa-tree"></i> <?= (int)$projet['nb_arbres'] ?> arbre(s)</span>
                            </div>
                        </div>
                        
                        <div class="project-actions">
                            <a href="index.php?page=exportProjet&id=<?= (int)$projet['id'] ?>" class="btn btn-secondary btn-small" title="Exporter en PDF">
                                <i class="fas fa-download"></i> Export
                            </a>
                            
                            <a href="index.php?page=affecterForestier&id=<?= (int)$projet['id'] ?>" class="btn btn-secondary btn-small">
                                <i class="fas fa-user-plus"></i> Gérer
                            </a>
                            
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                <input type="hidden" name="action" value="supprimer">
                                <input type="hidden" name="id_projet" value="<?= (int)$projet['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-small delete-project" data-project="<?= htmlspecialchars($projet['nom']) ?>">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="app.js"></script>
<script>
    // Gérer les clics sur les boutons de suppression de projet
    document.querySelectorAll('.delete-project').forEach(btn => {
        btn.addEventListener('click', function(e) {
            const projectName = this.dataset.project;
            confirmDeleteProject(e, projectName);
        });
    });
</script>

</body>
</html>
