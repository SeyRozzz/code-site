<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un arbre</title>
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
            padding: 24px;
        }
        
        .form-card {
            width: 100%;
            max-width: 500px;
            padding: 40px;
            background: var(--bg);
            border-radius: 12px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.4);
        }
        
        h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .subtitle {
            color: var(--text-secondary);
            font-size: 15px;
            margin-bottom: 32px;
        }
        
        .alert {
            background: rgba(255, 107, 107, 0.15);
            color: var(--error);
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 24px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        input, select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 15px;
            font-family: inherit;
            background: var(--bg-secondary);
            transition: all 0.2s;
        }
        
        input:focus, select:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--bg);
            box-shadow: 0 0 0 3px rgba(64, 217, 105, 0.2);
        }
        
        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
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
        
        .link {
            display: block;
            margin-top: 16px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 14px;
            text-align: center;
            transition: color 0.2s;
        }
        
        .link:hover {
            color: var(--text);
        }
        
        @media (max-width: 640px) {
            .form-card { padding: 32px 24px; }
            .row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="form-card">
        <h1>Nouvel arbre</h1>
        <p class="subtitle">Ajouter un arbre à votre projet</p>
        
        <?php if ($message): ?>
            <div class="alert"><?= htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
            <div class="form-group">
                <label>Projet</label>
                <select name="id_projet" required>
                    <option value="">-- Sélectionner un projet --</option>
                    <?php foreach($projets as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Essence</label>
                <select name="essence">
                    <option>Chêne</option>
                    <option>Hêtre</option>
                    <option>Sapin</option>
                    <option>Épicéa</option>
                    <option>Pin</option>
                    <option>Bouleau</option>
                    <option>Autre</option>
                </select>
            </div>

            <div class="row">
                <div class="form-group">
                    <label>Hauteur (m)</label>
                    <input type="number" step="0.1" name="hauteur" placeholder="0.0">
                </div>
                <div class="form-group">
                    <label>Diamètre (cm)</label>
                    <input type="number" name="diametre" placeholder="0">
                </div>
            </div>

            <div class="row">
                <div class="form-group">
                    <label>Latitude</label>
                    <input type="text" name="latitude" required placeholder="48.297">
                </div>
                <div class="form-group">
                    <label>Longitude</label>
                    <input type="text" name="longitude" required placeholder="4.074">
                </div>
            </div>

            <button type="submit" class="btn">Enregistrer</button>
            <a href="index.php?page=carte" class="link">Annuler</a>
        </form>
    </div>
</body>
</html>