<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Affecter des agents - Inventaire forestier</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        
        .subtitle {
            font-size: 13px;
            color: var(--text-secondary);
            margin-top: 4px;
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
        
        select {
            flex: 1;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.2s;
        }
        
        select:focus {
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
        
        .agents-list {
            display: grid;
            gap: 12px;
        }
        
        .agent-card {
            background: var(--bg);
            padding: 16px;
            border-radius: 12px;
            border: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.2s;
        }
        
        .agent-card:hover {
            border-color: var(--primary);
            box-shadow: 0 2px 8px rgba(64, 217, 105, 0.15);
        }
        
        .agent-info h3 {
            font-size: 16px;
            font-weight: 600;
            margin: 0 0 4px 0;
        }
        
        .agent-email {
            font-size: 13px;
            color: var(--text-secondary);
            margin: 0;
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
    <div>
        <h1>Affecter des agents</h1>
        <div class="subtitle">Projet: <?= htmlspecialchars($projet['nom']) ?></div>
    </div>
    <div class="header-right">
        <a href="index.php?page=gererProjet" class="btn btn-secondary btn-small">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
</div>

<div class="container">
    <?php if (!empty($message)): ?>
        <div class="message <?= strpos($message, '✅') !== false ? 'success' : 'error' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>
    
    <div class="section">
        <h2>Ajouter un agent</h2>
        <?php if (empty($forestiers_libres)): ?>
            <div class="empty">
                <p>Tous les agents sont déjà affectés à ce projet.</p>
            </div>
        <?php else: ?>
            <div class="form-card">
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <input type="hidden" name="action" value="ajouter">
                    
                    <div class="form-group">
                        <select name="id_forestier" required>
                            <option value="">-- Sélectionner un agent --</option>
                            <?php foreach ($forestiers_libres as $f): ?>
                                <option value="<?= (int)$f['id'] ?>">
                                    <?= htmlspecialchars($f['nom']) ?> (<?= htmlspecialchars($f['email']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Ajouter
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="section">
        <h2>Agents affectés (<?= count($forestiers_affectes) ?>)</h2>
        
        <?php if (empty($forestiers_affectes)): ?>
            <div class="empty">
                <p>Aucun agent affecté à ce projet.</p>
            </div>
        <?php else: ?>
            <div class="agents-list">
                <?php foreach ($forestiers_affectes as $f): ?>
                    <div class="agent-card">
                        <div class="agent-info">
                            <h3><?= htmlspecialchars($f['nom']) ?></h3>
                            <p class="agent-email"><?= htmlspecialchars($f['email']) ?></p>
                        </div>
                        
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                            <input type="hidden" name="action" value="retirer">
                            <input type="hidden" name="id_affectation" value="<?= (int)$f['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-small" onclick="return confirm('Retirer cet agent du projet ?')">
                                <i class="fas fa-trash"></i> Retirer
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
