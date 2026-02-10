<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil ONF - Inventaire GNSS</title>
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* --- CHARTE GRAPHIQUE PRO DARK --- */
        :root { 
            --bg-dark: #0a0f0d;       /* Noir très profond */
            --card-bg: rgba(25, 30, 28, 0.85); /* Fond semi-transparent */
            --green: #2ecc71;         /* Le Vert Néon emblématique */
            --green-hover: #27ae60;
            --accent-gold: #f1c40f;   /* Pour les admins */
            --red: #e74c3c;
            --txt-primary: #ffffff;
            --txt-secondary: #a0a0a0;
        }

        body { 
            font-family: 'Segoe UI', sans-serif; 
            margin: 0; 
            padding: 0; 
            background-color: var(--bg-dark);
            color: var(--txt-primary);
            height: 100vh;
            overflow: hidden;
        }

        /* --- HERO SECTION --- */
        .hero-section {
            position: relative;
            height: 100%;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            /* Fond avec dégradé sombre sur image de forêt */
            background: linear-gradient(135deg, rgba(5,10,8,0.95) 0%, rgba(15,25,20,0.85) 100%),
                        url('https://source.unsplash.com/1600x900/?forest,fog,dark') no-repeat center center/cover;
        }

        /* --- CONTENEUR PRINCIPAL (Glassmorphism) --- */
        .content-wrapper {
            text-align: center;
            z-index: 2;
            max-width: 850px;
            padding: 50px;
            background: var(--card-bg);
            backdrop-filter: blur(12px); /* Effet de flou derrière */
            -webkit-backdrop-filter: blur(12px);
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.08);
            box-shadow: 0 25px 60px rgba(0,0,0,0.6);
        }

        /* --- LOGO ARBRE TECH (CSS PUR) --- */
        .hero-logo {
            font-size: 80px; /* Taille de l'arbre */
            color: var(--green);
            margin-bottom: 15px;
            display: inline-block;
            /* Lueur Néon Verte autour de l'arbre */
            filter: drop-shadow(0 0 15px rgba(46, 204, 113, 0.5));
            animation: breathe 4s ease-in-out infinite alternate;
        }

        @keyframes breathe {
            from { filter: drop-shadow(0 0 10px rgba(46, 204, 113, 0.3)); transform: scale(1); }
            to { filter: drop-shadow(0 0 25px rgba(46, 204, 113, 0.7)); transform: scale(1.02); }
        }

        h1 {
            font-size: 2.8rem;
            font-weight: 700;
            margin: 10px 0 5px 0;
            letter-spacing: 1px;
            background: -webkit-linear-gradient(45deg, #fff, #aaa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .tagline {
            font-size: 1.2rem;
            color: var(--txt-secondary);
            font-weight: 300;
            margin-bottom: 40px;
        }

        /* --- DASHBOARD STATS --- */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 45px;
        }

        .stat-card {
            background: rgba(255,255,255,0.03);
            padding: 20px 10px;
            border-radius: 12px;
            border: 1px solid rgba(46, 204, 113, 0.15);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            background: rgba(255,255,255,0.05);
            border-color: var(--green);
        }

        .stat-icon { font-size: 24px; color: var(--green); margin-bottom: 10px; opacity: 0.9; }
        .stat-value { display: block; font-size: 1.8rem; font-weight: 700; color: white; }
        .stat-label { font-size: 0.85rem; color: #888; text-transform: uppercase; letter-spacing: 1px; margin-top: 5px; }

        /* --- BOUTONS --- */
        .action-section { margin-top: 30px; }

        .user-welcome {
            background: rgba(46, 204, 113, 0.08);
            color: var(--green);
            padding: 8px 16px;
            border-radius: 50px;
            display: inline-block;
            margin-bottom: 25px;
            font-weight: 600;
            font-size: 0.9rem;
            border: 1px solid rgba(46, 204, 113, 0.2);
        }

        .btn-container-flex { display: flex; gap: 15px; justify-content: center; flex-wrap: wrap; }

        .btn {
            padding: 14px 28px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        /* Bouton Principal Vert */
        .btn-primary-cta {
            background: var(--green);
            color: #0a0f0d;
            box-shadow: 0 0 20px rgba(46, 204, 113, 0.2);
            border: none;
        }
        .btn-primary-cta:hover { 
            background: var(--green-hover); 
            transform: translateY(-2px); 
            box-shadow: 0 0 30px rgba(46, 204, 113, 0.4);
            color: white;
        }

        /* Boutons Secondaires */
        .btn-secondary { background: transparent; border: 1px solid rgba(255,255,255,0.2); color: var(--txt-primary); }
        .btn-secondary:hover { border-color: white; background: rgba(255,255,255,0.05); }

        .btn-admin { border-color: var(--accent-gold); color: var(--accent-gold); }
        .btn-admin:hover { background: rgba(241, 196, 15, 0.1); border-color: #f39c12; }

        .link-discrete { display: block; margin-top: 25px; color: #666; text-decoration: none; font-size: 0.85rem; transition: 0.3s; }
        .link-discrete:hover { color: var(--red); }
    </style>
</head>
<body>

    <div class="hero-section">
        <div class="content-wrapper">
            
            <div class="hero-logo">
                <i class="fas fa-tree"></i>
            </div>
            
            <h1>Plateforme d'Inventaire Forestier</h1>
            <p class="tagline">Centralisation et cartographie des données GNSS en temps réel.</p>

            <div class="stats-grid">
                <div class="stat-card">
                    <i class="fas fa-seedling stat-icon"></i>
                    <span class="stat-value"><?= number_format($totalArbres, 0, ',', ' ') ?></span>
                    <span class="stat-label">Arbres Recensés</span>
                </div>
                <div class="stat-card">
                    <i class="fas fa-ruler-vertical stat-icon"></i>
                    <span class="stat-value"><?= $moyenneHauteur ?><small>m</small></span>
                    <span class="stat-label">Hauteur Moyenne</span>
                </div>
                <div class="stat-card">
                    <i class="fas fa-dna stat-icon"></i>
                    <span class="stat-value" style="font-size: 1.4rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <?= htmlspecialchars(mb_strimwidth($essencePopulaire, 0, 10, "..")) ?>
                    </span>
                    <span class="stat-label">Espèce Dominante</span>
                </div>
            </div>

            <div class="action-section">
                <?php if (isset($_SESSION['nom'])): ?>
                    <div class="user-welcome">
                        <i class="fas fa-user-astronaut"></i> Bonjour, <?= htmlspecialchars($_SESSION['nom']) ?>
                    </div>
                    
                    <div class="btn-container-flex">
                        <a href="index.php?page=carte" class="btn btn-primary-cta">
                            <i class="fas fa-globe-europe"></i> Carte Interactive
                        </a>
                        
                        <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'superadmin'): ?>
                            <a href="index.php?page=admin" class="btn btn-secondary btn-admin">
                                <i class="fas fa-tools"></i> Administration
                            </a>
                        <?php endif; ?>
                    </div>
                    <a href="index.php?page=logout" class="link-discrete">Se déconnecter</a>
                
                <?php else: ?>
                    <div class="btn-container-flex">
                        <a href="index.php?page=carte" class="btn btn-primary-cta">
                            <i class="fas fa-map"></i> Carte Publique
                        </a>
                        <a href="index.php?page=login" class="btn btn-secondary">
                            <i class="fas fa-fingerprint"></i> Accès Agent
                        </a>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>

</body>
</html>