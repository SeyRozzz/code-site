<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gérer les Projets - ONF</title>
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
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .header {
            background: var(--glass-bg);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            border: 1px solid rgba(255,255,255,0.08);
        }
        
        h1 {
            margin: 0 0 20px 0;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        /* Formulaire création */
        .form-creation {
            background: var(--glass-bg);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            border: 1px solid rgba(255,255,255,0.08);
        }
        
        .form-creation h2 {
            margin-top: 0;
            color: var(--green);
        }
        
        .form-group {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .form-group input {
            flex: 1;
            padding: 12px 15px;
            background: rgba(0,0,0,0.4);
            border: 1px solid rgba(255,255,255,0.1);
            color: white;
            border-radius: 8px;
            outline: none;
            transition: 0.3s;
        }
        
        .form-group input:focus {
            border-color: var(--green);
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
        
        /* Message */
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
        
        /* Liste des projets */
        .projets-list {
            display: grid;
            gap: 15px;
        }
        
        .projet-card {
            background: var(--glass-bg);
            padding: 20px;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: 0.3s;
        }
        
        .projet-card:hover {
            border-color: var(--green);
        }
        
        .projet-info h3 {
            margin: 0 0 5px 0;
            color: var(--green);
        }
        
        .projet-meta {
            font-size: 13px;
            color: #aaa;
        }
        
        .projet-meta span {
            margin-right: 20px;
        }
        
        .projet-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-small {
            padding: 8px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            transition: 0.3s;
        }
        
        .btn-affecter {
            background: rgba(46, 204, 113, 0.2);
            color: var(--green);
            border: 1px solid var(--green);
        }
        
        .btn-affecter:hover {
            background: var(--green);
            color: #0a0f0d;
        }
        
        .btn-delete {
            background: rgba(231, 76, 60, 0.2);
            color: var(--red);
            border: 1px solid var(--red);
        }
        
        .btn-delete:hover {
            background: var(--red);
            color: white;
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
                <i class="fas fa-folder-tree" style="color:var(--green);"></i>
                Gestion des Projets
            </h1>
            <?php if (!empty($message)): ?>
                <div class="message <?= strpos($message, '✅') ? 'success' : 'error' ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Formulaire création projet -->
        <div class="form-creation">
            <h2><i class="fas fa-plus"></i> Créer un Nouveau Projet</h2>
            
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="action" value="ajouter">
                
                <div class="form-group">
                    <input type="text" name="nom" placeholder="Nom du projet" required>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-plus"></i> Créer
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Liste des projets -->
        <h2 style="margin-top: 30px;">📋 Projets Existants</h2>
        
        <?php if (empty($projets)): ?>
            <p style="text-align: center; color: #aaa;">Aucun projet pour le moment.</p>
        <?php else: ?>
            <div class="projets-list">
                <?php foreach ($projets as $projet): ?>
                    <div class="projet-card">
                        <div class="projet-info">
                            <h3><?= htmlspecialchars($projet['nom']) ?></h3>
                            <div class="projet-meta">
                                <span><i class="fas fa-user"></i> <?= htmlspecialchars($projet['createur_nom'] ?? 'Admin') ?></span>
                                <span><i class="fas fa-users"></i> <?= $projet['nb_forestiers'] ?> forestier(s)</span>
                                <span><i class="fas fa-tree"></i> <?= $projet['nb_arbres'] ?> arbre(s)</span>
                            </div>
                        </div>
                        
                        <div class="projet-actions">
                            <a href="index.php?page=affecterForestier&id=<?= (int)$projet['id'] ?>" class="btn-small btn-affecter">
                                <i class="fas fa-user-plus"></i> Affecter
                            </a>
                            
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr? Cela supprimera aussi tous les arbres.');">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                <input type="hidden" name="action" value="supprimer">
                                <input type="hidden" name="id_projet" value="<?= (int)$projet['id'] ?>">
                                <button type="submit" class="btn-small btn-delete">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <a href="index.php?page=admin" class="back-link">
            <i class="fas fa-arrow-left"></i> Retour au panel admin
        </a>
    </div>
</body>
</html>
