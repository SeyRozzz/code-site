<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - ONF</title>
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* --- CHARTE GRAPHIQUE HERO DARK --- */
        :root { 
            --bg-dark: #0a0f0d;
            --green: #2ecc71;
            --green-hover: #27ae60;
            --txt-primary: #ffffff;
            --txt-secondary: #a0a0a0;
            --red: #e74c3c;
        }

        body { 
            font-family: 'Segoe UI', sans-serif; 
            margin: 0; 
            padding: 0; 
            background-color: var(--bg-dark);
            color: var(--txt-primary);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            /* Fond forêt immersif identique aux autres pages */
            background: linear-gradient(135deg, rgba(5,10,8,0.95) 0%, rgba(15,25,20,0.85) 100%),
                        url('https://source.unsplash.com/1600x900/?forest,fog,night') no-repeat center center/cover;
        }

        /* --- CARTE GLASSMORPHISM --- */
        .login-card { 
            background: rgba(30, 30, 30, 0.85); /* Fond semi-transparent */
            backdrop-filter: blur(12px);         /* Flou d'arrière-plan */
            -webkit-backdrop-filter: blur(12px);
            padding: 40px; 
            border-radius: 20px; 
            border: 1px solid rgba(255,255,255,0.08); 
            width: 340px; 
            text-align: center; 
            box-shadow: 0 25px 60px rgba(0,0,0,0.6);
            position: relative;
        }

        /* L'icône Cadenas au dessus */
        .icon-header {
            font-size: 45px;
            color: var(--green);
            margin-bottom: 15px;
            filter: drop-shadow(0 0 15px rgba(46, 204, 113, 0.4));
        }

        h2 { 
            color: white; 
            margin: 0 0 30px 0;
            font-weight: 600;
            letter-spacing: 1px;
        }

        /* --- INPUTS TECH --- */
        .input-group {
            position: relative;
            margin-bottom: 20px;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
            transition: 0.3s;
        }

        input { 
            width: 100%; 
            padding: 14px 15px 14px 45px; /* Espace à gauche pour l'icône */
            background: rgba(0, 0, 0, 0.4); 
            border: 1px solid rgba(255, 255, 255, 0.1); 
            color: white; 
            border-radius: 8px; 
            box-sizing: border-box; 
            outline: none;
            font-size: 15px;
            transition: 0.3s;
        }

        /* Focus : l'input et l'icône s'allument */
        input:focus { 
            border-color: var(--green); 
            background: rgba(0, 0, 0, 0.6);
            box-shadow: 0 0 10px rgba(46, 204, 113, 0.1);
        }
        input:focus + i, .input-group:focus-within i {
            color: var(--green);
        }

        /* --- BOUTON --- */
        .btn { 
            background: var(--green); 
            color: #0a0f0d; /* Texte sombre pour contraste */
            border: none; 
            width: 100%; 
            padding: 14px; 
            border-radius: 8px; 
            cursor: pointer; 
            font-weight: bold; 
            font-size: 16px;
            transition: 0.3s;
            margin-top: 10px;
            box-shadow: 0 5px 15px rgba(46, 204, 113, 0.2);
        }
        .btn:hover { 
            background: var(--green-hover); 
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(46, 204, 113, 0.4);
            color: white;
        }

        /* --- LIEN RETOUR --- */
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 25px;
            color: var(--txt-secondary);
            text-decoration: none;
            font-size: 13px;
            transition: 0.3s;
        }
        .back-link:hover { color: white; }

        /* --- ERREUR --- */
        .error-msg {
            background: rgba(231, 76, 60, 0.15);
            color: var(--red);
            padding: 10px;
            border-radius: 6px;
            font-size: 13px;
            margin-bottom: 20px;
            border: 1px solid rgba(231, 76, 60, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="icon-header">
            <i class="fas fa-lock"></i>
        </div>

        <h2>Espace Agent</h2>
        
        <?php if(isset($erreur)): ?>
            <div class="error-msg">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($erreur) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            
            <div class="input-group">
                <input type="email" name="email" placeholder="Email professionnel" required autocomplete="email">
                <i class="fas fa-envelope"></i>
            </div>
            
            <div class="input-group">
                <input type="password" name="password" placeholder="Mot de passe" required autocomplete="current-password">
                <i class="fas fa-key"></i>
            </div>
            
            <button type="submit" class="btn">Se connecter</button>
        </form>
        
        <a href="index.php?page=carte" class="back-link">
            <i class="fas fa-arrow-left"></i> Retour à la carte publique
        </a>
    </div>

</body>
</html>