<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
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
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-card {
            width: 100%;
            max-width: 360px;
            padding: 40px;
        }
        
        h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .subtitle {
            color: var(--text-secondary);
            font-size: 15px;
            margin-bottom: 32px;
        }
        
        .input-group {
            margin-bottom: 16px;
        }
        
        input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.2s;
            background: var(--bg-secondary);
            color: var(--text);
        }
        
        input::placeholder {
            color: var(--text-secondary);
        }
        
        input:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--bg);
            box-shadow: 0 0 0 3px rgba(52, 199, 89, 0.1);
        }
        
        .btn {
            width: 100%;
            padding: 12px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            margin-top: 20px;
            cursor: pointer;
            transition: opacity 0.2s;
        }
        
        .btn:hover {
            opacity: 0.85;
        }
        
        .error-msg {
            background: rgba(255, 107, 107, 0.15);
            color: var(--error);
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(255, 107, 107, 0.3);
        }
        
        .back-link {
            display: inline-block;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 13px;
            margin-top: 20px;
            transition: color 0.2s;
        }
        
        .back-link:hover {
            color: var(--text);
        }
    </style>
</head>
<body>

    <div class="login-card">
        <h2>Connexion</h2>
        <p class="subtitle">Accédez à votre compte</p>
        
        <?php if (isset($erreur)): ?>
            <div class="error-msg">
                <i class="fas fa-circle-xmark"></i>
                <?php echo htmlspecialchars($erreur); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="index.php?page=login">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
            
            <div class="input-group">
                <input type="email" name="email" placeholder="Email" required>
            </div>
            
            <div class="input-group">
                <input type="password" name="password" placeholder="Mot de passe" required>
            </div>
            
            <button type="submit" class="btn">Se connecter</button>
            
            <a href="index.php?page=accueil" class="back-link">← Retour</a>
        </form>
    </div>

</body>
</html>