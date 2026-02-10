<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Panel Administration - ONF</title>
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* --- CHARTE GRAPHIQUE (Cohérente avec l'accueil) --- */
        :root { 
            --bg-dark: #0a0f0d;
            --green: #2ecc71;
            --green-hover: #27ae60;
            --gold: #f1c40f;
            --red: #e74c3c;
            --txt-primary: #ffffff;
            --glass-bg: rgba(30, 30, 30, 0.85);
            --glass-border: rgba(255, 255, 255, 0.08);
        }

        body { 
            font-family: 'Segoe UI', sans-serif; 
            margin: 0; 
            background-color: var(--bg-dark);
            color: var(--txt-primary);
            /* Fond forêt immersif */
            background: linear-gradient(135deg, rgba(5,10,8,0.95) 0%, rgba(15,25,20,0.90) 100%),
                        url('https://source.unsplash.com/1600x900/?forest,dark') no-repeat center center/cover;
            background-attachment: fixed; /* L'image reste fixe quand on scrolle */
            min-height: 100vh;
        }

        /* --- HEADER GLASS --- */
        .header { 
            background: rgba(20, 20, 20, 0.6); 
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 15px 40px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            border-bottom: 1px solid var(--glass-border);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        h2 { margin: 0; font-weight: 600; letter-spacing: 0.5px; display: flex; align-items: center; gap: 10px; }
        
        /* --- BOUTONS --- */
        .btn {
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-back { 
            color: var(--txt-primary); 
            border: 1px solid rgba(255,255,255,0.2); 
        }
        .btn-back:hover { background: rgba(255,255,255,0.1); border-color: white; }

        .btn-add { 
            background: var(--green); 
            color: #0a0f0d; 
            box-shadow: 0 0 15px rgba(46, 204, 113, 0.2);
        }
        .btn-add:hover { 
            background: var(--green-hover); 
            transform: translateY(-2px); 
            color: white;
        }

        .user-pill {
            background: rgba(255,255,255,0.1);
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 13px;
            border: 1px solid rgba(255,255,255,0.1);
        }

        /* --- CONTENEUR TABLEAU --- */
        .container { width: 90%; max-width: 1200px; margin: 40px auto; }

        .table-wrapper {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 15px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            overflow: hidden; /* Arrondir les coins du tableau */
        }

        table { width: 100%; border-collapse: collapse; }

        th { 
            background: rgba(0,0,0,0.3); 
            color: var(--green); 
            padding: 18px; 
            text-align: left; 
            text-transform: uppercase; 
            font-size: 12px; 
            letter-spacing: 1px;
            border-bottom: 1px solid var(--glass-border);
        }

        td { 
            padding: 18px; 
            border-bottom: 1px solid rgba(255,255,255,0.05); 
            font-size: 14px;
            vertical-align: middle;
        }

        tr:last-child td { border-bottom: none; }
        
        tr:hover { background: rgba(255,255,255,0.02); }

        /* --- BADGES ROLES --- */
        .badge { 
            padding: 6px 12px; 
            border-radius: 50px; 
            font-size: 11px; 
            font-weight: 700; 
            text-transform: uppercase; 
            display: inline-block; 
            letter-spacing: 0.5px;
        }
        
        .badge-superadmin { 
            background: rgba(155, 89, 182, 0.2); 
            color: #d2b4de; 
            border: 1px solid #9b59b6;
            box-shadow: 0 0 10px rgba(155, 89, 182, 0.2);
        }
        
        .badge-admin { 
            background: rgba(241, 196, 15, 0.2); 
            color: #f7dc6f; 
            border: 1px solid #f1c40f;
        }
        
        .badge-user { 
            background: rgba(255, 255, 255, 0.1); 
            color: #ccc; 
            border: 1px solid #555;
        }

        /* --- ACTIONS --- */
        .action-delete {
            color: var(--red);
            text-decoration: none;
            font-size: 12px;
            font-weight: bold;
            padding: 5px 10px;
            border: 1px solid rgba(231, 76, 60, 0.3);
            border-radius: 4px;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .action-delete:hover { 
            background: rgba(231, 76, 60, 0.2); 
            border-color: var(--red);
        }

        .system-tag { color: var(--gold); font-size: 12px; display: flex; align-items: center; gap: 5px; opacity: 0.8; }
        .self-tag { color: #888; font-style: italic; font-size: 12px; }

    </style>
</head>
<body>

<div class="header">
    <div style="display:flex; align-items:center; gap:20px;">
        <a href="index.php?page=carte" class="btn btn-back">
            <i class="fas fa-arrow-left"></i> Carte
        </a>
        <h2><i class="fas fa-cogs" style="color:var(--green);"></i> Administration</h2>
    </div>

    <div style="display:flex; align-items:center; gap:20px;">
        <a href="index.php?page=adminAdduser" class="btn btn-add">
            <i class="fas fa-user-plus"></i> Nouvel Utilisateur
        </a>
        <div class="user-pill">
            <i class="fas fa-user-circle"></i> <?= htmlspecialchars($_SESSION['nom'] ?? 'Admin') ?>
        </div>
    </div>
</div>

<div class="container">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th><i class="fas fa-user"></i> Nom</th>
                    <th><i class="fas fa-envelope"></i> Email</th>
                    <th><i class="fas fa-id-badge"></i> Rôle</th>
                    <th><i class="fas fa-tools"></i> Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td style="font-weight:600; color:white;"><?= htmlspecialchars($u['nom']) ?></td>
                    
                    <td style="color:#aaa;"><?= htmlspecialchars($u['email']) ?></td>
                    
                    <td>
                        <?php 
                            $badgeClass = 'badge-user';
                            $icon = 'fa-tree'; // Icone par défaut
                            
                            if($u['role'] === 'superadmin') { 
                                $badgeClass = 'badge-superadmin'; 
                                $icon = 'fa-crown';
                            }
                            elseif($u['role'] === 'admin') { 
                                $badgeClass = 'badge-admin'; 
                                $icon = 'fa-shield-alt';
                            }
                        ?>
                        <span class="badge <?= $badgeClass ?>">
                            <i class="fas <?= $icon ?>"></i> <?= htmlspecialchars($u['role']) ?>
                        </span>
                    </td>
                    
                    <td>
                        <?php 
                        $monEmail = $_SESSION['email'] ?? ''; 
                        
                        if ($u['role'] === 'superadmin'): ?>
                            <div class="system-tag"><i class="fas fa-lock"></i> Système</div>
                        
                        <?php elseif ($u['email'] === $monEmail): ?>
                            <div class="self-tag">Votre compte</div>
                        
                        <?php else: ?>
                            <a href="index.php?page=supprimer_user&id=<?= $u['id'] ?>" 
                            class="action-delete" 
                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer <?= htmlspecialchars($u['nom']) ?> ?')">
                            <i class="fas fa-trash-alt"></i> Supprimer
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>