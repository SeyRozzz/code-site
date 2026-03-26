<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un utilisateur - Inventaire forestier</title>
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
            padding: 24px;
        }
        
        .container {
            max-width: 420px;
            margin: 0 auto;
        }
        
        .back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 24px;
            transition: opacity 0.2s;
        }
        
        .back:hover {
            opacity: 0.7;
        }
        
        .card {
            background: var(--bg-secondary);
            padding: 32px 24px;
            border-radius: 12px;
            border: 1px solid var(--border);
        }
        
        h1 {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 24px 0;
        }
        
        .message {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .message.error {
            background: #FFE5E5;
            color: var(--error);
            border: 1px solid #FFE5E5;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        input, select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.2s;
            background: var(--bg);
        }
        
        input:focus, select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(52, 199, 89, 0.1);
        }
        
        .btn {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            width: 100%;
            margin-top: 4px;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            opacity: 0.85;
        }
        
        .btn-secondary {
            background: transparent;
            color: var(--text-secondary);
            border: none;
            text-decoration: none;
            display: inline-block;
            margin-top: 16px;
            width: auto;
            padding: 8px 0;
        }
        
        .btn-secondary:hover {
            color: var(--text);
        }
    </style>
</head>
<body>

<div class="container">
    <a href="index.php?page=admin" class="back">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
    
    <div class="card">
        <h1>Créer un utilisateur</h1>
        
        <?php if (!empty($message)): ?>
            <div class="message error">
                <i class="fas fa-exclamation-circle"></i> 
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
            
            <div class="form-group">
                <label>Nom complet</label>
                <input type="text" name="nom" placeholder="Ex: Jean Dupont" required>
            </div>
            
            <div class="form-group">
                <label>Adresse email</label>
                <input type="email" name="email" placeholder="Ex: jean@example.fr" required>
            </div>
            
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            
            <div class="form-group">
                <label>Rôle</label>
                <select name="role" required>
                    <option value="forestier">Agent forestier</option>
                    <option value="admin">Administrateur</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Créer</button>
            <a href="index.php?page=admin" class="btn-secondary">Annuler</a>
        </form>
    </div>
</div>

</body>
</html>