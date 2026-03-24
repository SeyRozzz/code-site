<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Affecter des Forestiers - ONF</title>
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { 
            --bg-dark: #0a0f0d;
            --green: #2ecc71;
            --green-hover: #27ae60;
            --red: #e74c3c;
            --txt-primary: #ffffff;
            --glass-bg: rgba(30, 30, 30, 0.85);
        }
        
        body { 
            font-family: 'Segoe UI', sans-serif; 
            margin: 0; 
            background-color: var(--bg-dark);
            color: var(--txt-primary);
            background: linear-gradient(135deg, rgba(5,10,8,0.95) 0%, rgba(15,25,20,0.90) 100%),
                        url('https://source.unsplash.com/1600x900/?forest,dark') no-repeat center center/cover;
            background-attachment: fixed;
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .header {
            background: var(--glass-bg);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            border: 1px solid rgba(255,255,255,0.08);
        }
        
        h1, h2 {
            margin: 0 0 15px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .project-title {
            color: var(--green);
            font-size: 1.5rem;
        }
        
        .message {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        
        .message.success {
            background: rgba(46, 204, 113, 0.1);
            border: 1px solid var(--green);
            color: var(--green);
        }
        
        .message.error {
            background: rgba(231, 76, 60, 0.1);
            border: 1px solid var(--red);
            color: var(--red);
        }
        
        .section {
            background: var(--glass-bg);
            padding: 25px;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.08);
            margin-bottom: 20px;
        }
        
        .form-group {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        select {
            flex: 1;
            padding: 12px 15px;
            background: rgba(0,0,0,0.4);
            border: 1px solid rgba(255,255,255,0.1);
            color: white;
            border-radius: 8px;
            outline: none;
            transition: 0.3s;
        }
        
        select:focus {
            border-color: var(--green);
        }
        
        select option {
            background: #1a1a1a;
            color: white;
        }
        
        .btn-primary {
            padding: 12px 25px;
            background: var(--green);
            color: #0a0f0d;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }
        
        .btn-primary:hover {
            background: var(--green-hover);
        }
        
        .liste-affectes {
            display: grid;
            gap: 10px;
        }
        
        .forestier-item {
            background: rgba(0,0,0,0.3);
            padding: 15px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid rgba(255,255,255,0.05);
        }
        
        .forestier-info {
            flex: 1;
        }
        
        .forestier-info h3 {
            margin: 0;
            color: var(--green);
        }
        
        .forestier-info p {
            margin: 3px 0 0 0;
            color: #aaa;
            font-size: 13px;
        }
        
        .btn-delete {
            padding: 8px 15px;
            background: rgba(231, 76, 60, 0.2);
            color: var(--red);
            border: 1px solid var(--red);
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            transition: 0.3s;
        }
        
        .btn-delete:hover {
            background: var(--red);
            color: white;
        }
        
        .empty-state {
            text-align: center;
            color: #aaa;
            padding: 20px;
        }
        
        .back-link {
            display: inline-block;
            color: #aaa;
            text-decoration: none;
            margin-top: 20px;
        }
        
        .back-link:hover {
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                <i class="fas fa-user-plus" style="color:var(--green);"></i>
                Affecter des Forestiers
            </h1>
            <p class="project-title">Projet: <?= htmlspecialchars($projet['nom']) ?></p>
            
            <?php if (!empty($message)): ?>
                <div class="message <?= strpos($message, '✅') ? 'success' : 'error' ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Section: Ajouter un forestier -->
        <div class="section">
            <h2><i class="fas fa-plus"></i> Ajouter un Forestier</h2>
            
            <?php if (empty($forestiers_libres)): ?>
                <p class="empty-state">Tous les forestiers sont déjà affectés à ce projet.</p>
            <?php else: ?>
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <input type="hidden" name="action" value="ajouter">
                    
                    <div class="form-group">
                        <select name="id_forestier" required>
                            <option value="">-- Sélectionner un forestier --</option>
                            <?php foreach ($forestiers_libres as $f): ?>
                                <option value="<?= $f['id'] ?>">
                                    <?= htmlspecialchars($f['nom']) ?> (<?= htmlspecialchars($f['email']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-plus"></i> Affecter
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
        
        <!-- Section: Forestiers affectés -->
        <div class="section">
            <h2><i class="fas fa-users"></i> Forestiers Affectés (<?= count($forestiers_affectes) ?>)</h2>
            
            <?php if (empty($forestiers_affectes)): ?>
                <p class="empty-state">Aucun forestier affecté à ce projet.</p>
            <?php else: ?>
                <div class="liste-affectes">
                    <?php foreach ($forestiers_affectes as $f): ?>
                        <div class="forestier-item">
                            <div class="forestier-info">
                                <h3><i class="fas fa-user-circle"></i> <?= htmlspecialchars($f['nom']) ?></h3>
                                <p><?= htmlspecialchars($f['email']) ?></p>
                            </div>
                            
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                <input type="hidden" name="action" value="retirer">
                                <input type="hidden" name="id_affectation" value="<?= $f['id'] ?>">
                                <button type="submit" class="btn-delete" onclick="return confirm('Retirer ce forestier du projet?');">
                                    <i class="fas fa-trash"></i> Retirer
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <a href="index.php?page=gererProjet" class="back-link">
            <i class="fas fa-arrow-left"></i> Retour à la gestion des projets
        </a>
    </div>
</body>
</html>
