<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Arbre - <?= htmlspecialchars($projet['nom']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-dark: #0a0f0d;
            --green: #2ecc71;
            --green-hover: #27ae60;
            --txt-primary: #ffffff;
            --red: #e74c3c;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background-color: var(--bg-dark);
            color: var(--txt-primary);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, rgba(5,10,8,0.95) 0%, rgba(15,25,20,0.85) 100%),
                        url('https://images.unsplash.com/photo-1441974231531-c6227db76b6e?auto=format&fit=crop&w=1600&q=80') no-repeat center center/cover;
            background-attachment: fixed;
        }

        .form-card {
            background: rgba(30, 30, 30, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 40px;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.08);
            width: 500px;
            text-align: center;
            box-shadow: 0 25px 60px rgba(0,0,0,0.6);
        }

        h2 {
            margin: 0 0 10px 0;
            font-weight: 600;
            color: white;
        }

        .project-name {
            color: var(--green);
            font-size: 0.9rem;
            margin-bottom: 20px;
            opacity: 0.9;
        }

        .input-group {
            position: relative;
            margin-bottom: 18px;
            text-align: left;
        }

        .input-group label {
            display: block;
            margin-bottom: 5px;
            font-size: 0.85rem;
            color: #aaa;
            font-weight: 600;
            margin-left: 5px;
        }

        input, select {
            width: 100%;
            padding: 12px 15px 12px 40px;
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            border-radius: 8px;
            box-sizing: border-box;
            outline: none;
            transition: 0.3s;
        }

        input:focus, select:focus {
            border-color: var(--green);
            background: rgba(0, 0, 0, 0.6);
            box-shadow: 0 0 10px rgba(46, 204, 113, 0.2);
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 38px;
            color: #888;
            z-index: 5;
        }

        .btn-submit {
            background: var(--green);
            color: #0a0f0d;
            border: none;
            width: 100%;
            padding: 14px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 15px;
            transition: 0.3s;
            font-size: 1rem;
        }

        .btn-submit:hover {
            background: var(--green-hover);
            color: white;
            transform: translateY(-2px);
        }

        .row {
            display: flex;
            gap: 15px;
        }

        .col {
            flex: 1;
        }

        .btn-cancel {
            display: block;
            margin-top: 20px;
            color: #aaa;
            text-decoration: none;
            font-size: 13px;
        }

        .btn-cancel:hover {
            color: white;
        }

        .alert {
            background: rgba(231, 76, 60, 0.1);
            color: var(--red);
            padding: 12px;
            border-radius: 8px;
            border: 1px solid var(--red);
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert.success {
            background: rgba(46, 204, 113, 0.1);
            color: var(--green);
            border-color: var(--green);
        }
    </style>
</head>
<body>
    <div class="form-card">
        <h2><i class="fas fa-plus-circle" style="color:var(--green);"></i> Ajouter un Arbre</h2>
        <p class="project-name">📍 Projet: <?= htmlspecialchars($projet['nom']) ?></p>

        <?php if (!empty($message)): ?>
            <div class="alert <?= strpos($message, '✅') ? 'success' : '' ?>">
                <i class="fas <?= strpos($message, '✅') ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

            <div class="input-group">
                <label>Essence</label>
                <i class="fas fa-leaf input-icon"></i>
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
                <div class="col input-group">
                    <label>Hauteur (m)</label>
                    <i class="fas fa-ruler-vertical input-icon"></i>
                    <input type="number" step="0.1" name="hauteur" placeholder="0.0">
                </div>
                <div class="col input-group">
                    <label>Diamètre (cm)</label>
                    <i class="fas fa-circle-notch input-icon"></i>
                    <input type="number" step="0.1" name="diametre" placeholder="0.0">
                </div>
            </div>

            <div class="row">
                <div class="col input-group">
                    <label>Latitude</label>
                    <i class="fas fa-location-dot input-icon"></i>
                    <input type="text" name="latitude" placeholder="48.297" required>
                </div>
                <div class="col input-group">
                    <label>Longitude</label>
                    <i class="fas fa-location-dot input-icon"></i>
                    <input type="text" name="longitude" placeholder="4.074" required>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-save"></i> Enregistrer l'Arbre
            </button>
            <a href="index.php?page=carte&id_projet=<?= $id_projet ?>" class="btn-cancel">
                ← Annuler
            </a>
        </form>
    </div>
</body>
</html>
