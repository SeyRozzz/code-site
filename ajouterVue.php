<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Arbre - ONF</title>
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --bg-dark: #0a0f0d; --green: #2ecc71; --green-hover: #27ae60; --txt-primary: #ffffff; --red: #e74c3c; }
        body { 
            font-family: 'Segoe UI', sans-serif; margin: 0; background-color: var(--bg-dark); color: var(--txt-primary);
            height: 100vh; display: flex; justify-content: center; align-items: center;
            background: linear-gradient(135deg, rgba(5,10,8,0.95) 0%, rgba(15,25,20,0.85) 100%),
                        url('https://images.unsplash.com/photo-1441974231531-c6227db76b6e?auto=format&fit=crop&w=1600&q=80') no-repeat center center/cover;
        }
        .form-card { 
            background: rgba(30, 30, 30, 0.85); backdrop-filter: blur(12px); padding: 40px; border-radius: 20px; 
            border: 1px solid rgba(255,255,255,0.08); width: 450px; text-align: center; box-shadow: 0 25px 60px rgba(0,0,0,0.6);
        }
        h2 { margin-bottom: 25px; font-weight: 600; }
        .input-group { position: relative; margin-bottom: 15px; text-align: left; }
        .input-group label { display: block; margin-bottom: 5px; font-size: 0.85rem; color: #aaa; font-weight: 600; }
        input, select { 
            width: 100%; padding: 12px 15px 12px 40px; background: rgba(0, 0, 0, 0.4); 
            border: 1px solid rgba(255, 255, 255, 0.1); color: white; border-radius: 8px; box-sizing: border-box; outline: none;
        }
        input:focus, select:focus { border-color: var(--green); }
        .input-icon { position: absolute; left: 15px; top: 38px; color: #888; }
        .btn-submit { 
            background: var(--green); color: #0a0f0d; border: none; width: 100%; padding: 14px; 
            border-radius: 8px; font-weight: bold; cursor: pointer; margin-top: 15px; transition: 0.3s;
        }
        .btn-submit:hover { background: var(--green-hover); color: white; }
        .row { display: flex; gap: 15px; } .col { flex: 1; }
        .alert { background: rgba(231, 76, 60, 0.1); color: var(--red); padding: 10px; border-radius: 8px; border: 1px solid var(--red); margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="form-card">
        <h2><i class="fas fa-plus-circle" style="color:var(--green);"></i> Nouvel Arbre</h2>
        
        <?php if ($message): ?><div class="alert"><?= $message ?></div><?php endif; ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
            <div class="input-group">
                <label>Projet Forestier</label>
                <i class="fas fa-folder-tree input-icon"></i>
                <select name="id_projet" required>
                    <option value="">-- Sélectionner un projet --</option>
                    <?php foreach($projets as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="input-group">
                <label>Essence</label>
                <i class="fas fa-leaf input-icon"></i>
                <select name="essence">
                    <option>Chêne</option><option>Hêtre</option><option>Sapin</option>
                    <option>Épicéa</option><option>Pin</option><option>Bouleau</option><option>Autre</option>
                </select>
            </div>

            <div class="row">
                <div class="col input-group"><label>H (m)</label><i class="fas fa-ruler-vertical input-icon"></i>
                    <input type="number" step="0.1" name="hauteur" placeholder="0.0"></div>
                <div class="col input-group"><label>D (cm)</label><i class="fas fa-circle-notch input-icon"></i>
                    <input type="number" name="diametre" placeholder="0"></div>
            </div>

            <div class="row">
                <div class="col input-group"><label>Latitude</label><input type="text" name="latitude" required placeholder="48.297"></div>
                <div class="col input-group"><label>Longitude</label><input type="text" name="longitude" required placeholder="4.074"></div>
            </div>

            <button type="submit" class="btn-submit">Enregistrer l'arbre</button>
            <a href="index.php?page=carte" style="display:block; margin-top:15px; color:#aaa; text-decoration:none; font-size:13px;">Annuler</a>
        </form>
    </div>
</body>
</html>