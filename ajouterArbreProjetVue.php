<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un arbre - Inventaire forestier</title>
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
            max-width: 600px;
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
            margin: 0 0 8px 0;
        }
        
        .subtitle {
            font-size: 13px;
            color: var(--text-secondary);
            margin-bottom: 24px;
        }
        
        .message {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 24px;
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
            color: var(--text);
        }
        
        input::placeholder {
            color: var(--text-secondary);
        }
        
        input:focus, select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(64, 217, 105, 0.2);
        }
        
        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        
        .btn {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            margin-top: 12px;
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
            margin-top: 8px;
        }
        
        .btn-secondary:hover {
            background: var(--border);
        }
    </style>
</head>
<body>

<div class="container">
    <a href="index.php?page=carte&id_projet=<?= (int)$id_projet ?>" class="back">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
    
    <div class="card">
        <h1>Ajouter un arbre</h1>
        <div class="subtitle">Projet: <?= htmlspecialchars($projet['nom']) ?></div>
        
        <?php if (!empty($message)): ?>
            <div class="message <?= strpos($message, '✅') !== false ? 'success' : 'error' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            
            <div class="form-group">
                <label>Essence</label>
                <select name="essence" required>
                    <option value="">-- Sélectionner une essence --</option>
                    <option value="Chêne">Chêne</option>
                    <option value="Hêtre">Hêtre</option>
                    <option value="Sapin">Sapin</option>
                    <option value="Épicéa">Épicéa</option>
                    <option value="Pin">Pin</option>
                    <option value="Bouleau">Bouleau</option>
                    <option value="Autre">Autre</option>
                </select>
            </div>
            
            <div class="row">
                <div class="form-group">
                    <label>Hauteur (m)</label>
                    <input type="number" step="0.1" name="hauteur" placeholder="0.0">
                </div>
                <div class="form-group">
                    <label>Diamètre (cm)</label>
                    <input type="number" step="0.1" name="diametre" placeholder="0.0">
                </div>
            </div>
            
            <div class="row">
                <div class="form-group">
                    <label>Latitude</label>
                    <input type="text" name="latitude" placeholder="48.297" required>
                </div>
                <div class="form-group">
                    <label>Longitude</label>
                    <input type="text" name="longitude" placeholder="4.074" required>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-plus"></i> Enregistrer
            </button>
            <a href="index.php?page=carte&id_projet=<?= (int)$id_projet ?>" class="btn btn-secondary" style="text-decoration: none;">
                Annuler
            </a>
        </form>
    </div>
</div>

</body>
</html>
