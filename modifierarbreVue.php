<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un Arbre - ONF</title>
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* MÊME DESIGN QUE LOGIN/ADMIN */
        :root { --bg-dark: #0a0f0d; --green: #2ecc71; --green-hover: #27ae60; --txt-primary: #ffffff; --red: #e74c3c; }
        body { 
            font-family: 'Segoe UI', sans-serif; margin: 0; background-color: var(--bg-dark); color: var(--txt-primary);
            height: 100vh; display: flex; justify-content: center; align-items: center;
            background: linear-gradient(135deg, rgba(5,10,8,0.95) 0%, rgba(15,25,20,0.85) 100%),
                        url('https://source.unsplash.com/1600x900/?forest,leaves') no-repeat center center/cover;
        }
        .form-card { 
            background: rgba(30, 30, 30, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
            padding: 40px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.08); 
            width: 400px; text-align: center; box-shadow: 0 25px 60px rgba(0,0,0,0.6);
        }
        h2 { margin: 0 0 25px 0; font-weight: 600; color: white; }
        .input-group { position: relative; margin-bottom: 15px; text-align: left; }
        .input-group label { display: block; margin-bottom: 5px; font-size: 0.85rem; color: #aaa; font-weight: 600; margin-left:5px; }
        input, select { 
            width: 100%; padding: 12px 15px 12px 40px; background: rgba(0, 0, 0, 0.4); 
            border: 1px solid rgba(255, 255, 255, 0.1); color: white; border-radius: 8px; box-sizing: border-box; outline: none; transition: 0.3s;
        }
        input:focus, select:focus { border-color: var(--green); background: rgba(0, 0, 0, 0.6); }
        .input-icon { position: absolute; left: 15px; top: 38px; color: #888; }
        .input-group:focus-within .input-icon { color: var(--green); }
        .btn-submit { 
            background: var(--green); color: #0a0f0d; border: none; width: 100%; padding: 14px; 
            border-radius: 8px; font-weight: bold; cursor: pointer; margin-top: 15px; transition: 0.3s;
        }
        .btn-submit:hover { background: var(--green-hover); color: white; transform: translateY(-2px); }
        .row { display: flex; gap: 15px; } .col { flex: 1; }
        .btn-cancel { display: block; margin-top: 20px; color: #aaa; text-decoration: none; font-size: 13px; }
        .btn-cancel:hover { color: white; }
    </style>
</head>
<body>
    <div class="form-card">
        <h2><i class="fas fa-edit" style="color:var(--green);"></i> Modifier l'Arbre #<?= $arbre['id'] ?></h2>
        
        <form method="POST">
            <div class="input-group">
                <label>Essence</label>
                <i class="fas fa-leaf input-icon"></i>
                <select name="essence">
                    <?php 
                    $essences = ["Chêne", "Hêtre", "Sapin", "Épicéa", "Pin", "Mélèze", "Bouleau", "Frêne", "Autre"];
                    foreach($essences as $e): 
                    ?>
                        <option value="<?= $e ?>" <?= ($arbre['essence'] == $e) ? 'selected' : '' ?>><?= $e ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="row">
                <div class="col input-group">
                    <label>Hauteur (m)</label>
                    <i class="fas fa-ruler-vertical input-icon"></i>
                    <input type="number" step="0.1" name="hauteur" value="<?= htmlspecialchars($arbre['hauteur']) ?>">
                </div>
                <div class="col input-group">
                    <label>Diamètre (cm)</label>
                    <i class="fas fa-circle-notch input-icon"></i>
                    <input type="number" step="1" name="diametre" value="<?= htmlspecialchars($arbre['diametre']) ?>">
                </div>
            </div>

            <div class="row">
                <div class="col input-group">
                    <label>Latitude</label>
                    <input type="text" name="latitude" value="<?= htmlspecialchars($arbre['latitude']) ?>" required>
                </div>
                <div class="col input-group">
                    <label>Longitude</label>
                    <input type="text" name="longitude" value="<?= htmlspecialchars($arbre['longitude']) ?>" required>
                </div>
            </div>

            <button type="submit" class="btn-submit">Enregistrer les modifications</button>
            <a href="index.php?page=carte" class="btn-cancel">Annuler</a>
        </form>
    </div>
</body>
</html>