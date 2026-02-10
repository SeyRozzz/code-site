<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter Utilisateur - ONF</title>
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* --- CHARTE GRAPHIQUE (Copie de l'accueil pour cohérence) --- */
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
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            /* Le même fond immersif que l'accueil */
            background: linear-gradient(135deg, rgba(5,10,8,0.95) 0%, rgba(15,25,20,0.85) 100%),
                        url('https://source.unsplash.com/1600x900/?forest,dark') no-repeat center center/cover;
        }

        /* --- CARTE FORMULAIRE (Effet Verre) --- */
        .form-card { 
            background: rgba(30, 30, 30, 0.85); /* Fond sombre semi-transparent */
            backdrop-filter: blur(12px);         /* Le flou derrière la carte */
            -webkit-backdrop-filter: blur(12px);
            padding: 40px; 
            border-radius: 20px; 
            border: 1px solid rgba(255,255,255,0.08); 
            width: 380px; 
            box-shadow: 0 25px 50px rgba(0,0,0,0.6);
            text-align: center;
            position: relative;
        }

        /* L'icône en haut du formulaire */
        .form-icon {
            font-size: 40px;
            color: var(--green);
            margin-bottom: 15px;
            filter: drop-shadow(0 0 10px rgba(46, 204, 113, 0.4));
        }

        h2 { 
            color: white; 
            margin: 0 0 30px 0; 
            font-weight: 600; 
            letter-spacing: 0.5px;
        }

        /* --- STYLES DES CHAMPS (INPUTS) --- */
        .input-group {
            position: relative;
            margin-bottom: 20px;
            text-align: left;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.85rem;
            color: var(--txt-secondary);
            font-weight: 600;
            margin-left: 5px;
        }

        .input-wrapper {
            position: relative;
        }

        /* Icône à l'intérieur du champ */
        .input-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            transition: 0.3s;
        }

        input, select { 
            width: 100%; 
            padding: 12px 15px 12px 45px; /* Padding à gauche pour laisser place à l'icône */
            background: rgba(0, 0, 0, 0.3); 
            border: 1px solid rgba(255, 255, 255, 0.1); 
            color: white; 
            border-radius: 8px; 
            box-sizing: border-box; 
            outline: none;
            font-size: 14px;
            transition: 0.3s;
        }

        /* Effet Focus : le champ s'allume en vert */
        input:focus, select:focus { 
            border-color: var(--green); 
            background: rgba(0, 0, 0, 0.5);
            box-shadow: 0 0 10px rgba(46, 204, 113, 0.1);
        }
        
        input:focus + i, .input-wrapper:focus-within i {
            color: var(--green);
        }

        /* --- BOUTONS --- */
        .btn-submit { 
            width: 100%; 
            background: var(--green); 
            color: #0a0f0d; 
            border: none; 
            padding: 14px; 
            border-radius: 8px; 
            font-weight: bold; 
            cursor: pointer; 
            font-size: 16px;
            margin-top: 10px;
            transition: 0.3s;
            box-shadow: 0 4px 15px rgba(46, 204, 113, 0.3);
        }

        .btn-submit:hover { 
            background: var(--green-hover); 
            transform: translateY(-2px);
            color: white;
        }

        .btn-cancel { 
            display: inline-block; 
            margin-top: 20px; 
            color: var(--txt-secondary); 
            text-decoration: none; 
            font-size: 14px; 
            transition: 0.3s;
        }
        .btn-cancel:hover { color: white; }

        /* --- MESSAGE ERREUR --- */
        .error { 
            background: rgba(231, 76, 60, 0.1); 
            color: var(--red); 
            padding: 10px; 
            border-radius: 8px; 
            border: 1px solid rgba(231, 76, 60, 0.3);
            margin-bottom: 20px; 
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
    </style>
</head>
<body>

    <div class="form-card">
        <div class="form-icon">
            <i class="fas fa-user-plus"></i>
        </div>

        <h2>Créer un Compte</h2>
        
        <?php if(!empty($message)): ?>
            <div class="error">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        
        <form action="index.php?page=adminAdduser" method="POST">
            
            <div class="input-group">
                <label>Nom complet</label>
                <div class="input-wrapper">
                    <input type="text" name="nom" placeholder="Ex: Jean Dupont" required autocomplete="off">
                    <i class="fas fa-user"></i>
                </div>
            </div>
            
            <div class="input-group">
                <label>Adresse Email</label>
                <div class="input-wrapper">
                    <input type="email" name="email" placeholder="Ex: jean@onf.fr" required autocomplete="off">
                    <i class="fas fa-envelope"></i>
                </div>
            </div>
            
            <div class="input-group">
                <label>Mot de passe</label>
                <div class="input-wrapper">
                    <input type="password" name="password" placeholder="••••••••" required>
                    <i class="fas fa-lock"></i>
                </div>
            </div>
            
            <div class="input-group">
                <label>Attribution du Rôle</label>
                <div class="input-wrapper">
                    <select name="role">
                        <option value="forestier">🌲 Forestier (Accès standard)</option>
                        <option value="admin">⚙️ Administrateur (Accès total)</option>
                    </select>
                    <i class="fas fa-id-badge"></i>
                </div>
            </div>
            
            <button type="submit" class="btn-submit">Valider la création</button>
            <a href="index.php?page=admin" class="btn-cancel">Annuler</a>
        </form>
    </div>

</body>
</html>