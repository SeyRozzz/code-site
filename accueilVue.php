<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Inventaire forestier</title>
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
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }
        
        .hero {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 40px 24px;
            background: linear-gradient(135deg, #1A1A1A 0%, #252525 100%);
        }
        
        .container {
            width: 100%;
            max-width: 600px;
            text-align: center;
        }
        
        .logo {
            font-size: 64px;
            margin-bottom: 24px;
            color: var(--primary);
        }
        
        h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 12px;
            line-height: 1.2;
        }
        
        .subtitle {
            font-size: 16px;
            color: var(--text-secondary);
            margin-bottom: 48px;
            line-height: 1.5;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 48px;
        }
        
        .stat {
            background: var(--bg-secondary);
            padding: 20px 12px;
            border-radius: 12px;
            transition: all 0.2s;
        }
        
        .stat:hover {
            background: #383838;
        }
        
        .stat-icon {
            font-size: 24px;
            color: var(--primary);
            margin-bottom: 8px;
        }
        
        .stat-value {
            display: block;
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        
        .stat-label {
            font-size: 11px;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .welcome-msg {
            background: rgba(64, 217, 105, 0.15);
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 32px;
            font-size: 14px;
            color: var(--primary);
        }
        
        .actions {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
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
        
        .link-secondary {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 14px;
            margin-top: 24px;
            display: inline-block;
            transition: color 0.2s;
        }
        
        .link-secondary:hover {
            color: var(--text);
        }
        
        @media (max-width: 640px) {
            h1 { font-size: 28px; }
            .stats { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="hero">
        <div class="container">
            <div class="logo">🌲</div>
            
            <h1>Inventaire forestier</h1>
            <p class="subtitle">Gestion cartographique des données GNSS</p>
            
            <div class="stats">
                <div class="stat">
                    <i class="fas fa-seedling stat-icon"></i>
                    <span class="stat-value"><?= number_format($totalArbres, 0, ',', ' ') ?></span>
                    <span class="stat-label">Arbres</span>
                </div>
                <div class="stat">
                    <i class="fas fa-ruler-vertical stat-icon"></i>
                    <span class="stat-value"><?= round($moyenneHauteur, 1) ?>m</span>
                    <span class="stat-label">Hauteur moy.</span>
                </div>
                <div class="stat">
                    <i class="fas fa-leaf stat-icon"></i>
                    <span class="stat-value"><?= htmlspecialchars(mb_strimwidth($essencePopulaire, 0, 12, "...")) ?></span>
                    <span class="stat-label">Espèce</span>
                </div>
            </div>
            
            <div class="actions">
                <?php if (isset($_SESSION['nom'])): ?>
                    <div class="welcome-msg">
                        Bienvenue, <?= htmlspecialchars($_SESSION['nom']) ?> 👋
                    </div>
                    
                    <a href="index.php?page=carte" class="btn btn-primary">
                        <i class="fas fa-map"></i> Carte interactive
                    </a>
                    
                    <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'superadmin'): ?>
                        <a href="index.php?page=admin" class="btn btn-secondary">
                            <i class="fas fa-sliders-h"></i> Administration
                        </a>
                    <?php endif; ?>
                    
                    <a href="index.php?page=logout" class="link-secondary">Se déconnecter</a>
                
                <?php else: ?>
                    <a href="index.php?page=carte" class="btn btn-primary">
                        <i class="fas fa-map"></i> Carte publique
                    </a>
                    <a href="index.php?page=login" class="btn btn-secondary">
                        <i class="fas fa-sign-in-alt"></i> Accès agent
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>