<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="animations.css">
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
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 24px;
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        h1 {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }
        
        .header-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }
        
        .btn {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid var(--border);
            background: var(--bg-secondary);
            color: var(--text);
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        
        .btn:hover {
            background: var(--border);
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
            border: none;
        }
        
        .btn-primary:hover {
            opacity: 0.85;
        }
        
        .user-badge {
            background: var(--bg-secondary);
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px;
        }
        
        .table-wrapper {
            border-radius: 12px;
            border: 1px solid var(--border);
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.4);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background: var(--bg);
        }
        
        th {
            background: var(--bg-secondary);
            padding: 12px 16px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            border-bottom: 1px solid var(--border);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        td {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
            font-size: 14px;
        }
        
        tr:last-child td {
            border-bottom: none;
        }
        
        tr:hover {
            background: var(--bg-secondary);
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .badge-admin {
            background: rgba(52, 199, 89, 0.1);
            color: var(--primary);
        }
        
        .badge-forestier {
            background: rgba(52, 152, 219, 0.1);
            color: #007AFF;
        }
        
        .badge-lecteur {
            background: rgba(149, 165, 166, 0.1);
            color: var(--text-secondary);
        }
        
        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            border: 1px solid var(--border);
            background: var(--bg);
            color: var(--text);
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }
        
        .action-btn:hover {
            background: var(--bg-secondary);
            border-color: var(--text-secondary);
        }
        
        .action-btn.delete {
            color: var(--error);
            border-color: #FFE5E5;
        }
        
        .action-btn.delete:hover {
            background: #FFE5E5;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Administration</h1>
    
    <div class="header-actions">
        <a href="index.php?page=gererProjet" class="btn btn-primary">
            <i class="fas fa-folder-plus"></i> Gérer projets
        </a>
        <a href="index.php?page=adminAdduser" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Nouvel utilisateur
        </a>
        <a href="index.php?page=carte" class="btn">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
        <div class="user-badge">
            <i class="fas fa-user-circle"></i>
            <?= htmlspecialchars($_SESSION['nom'] ?? 'Admin') ?>
        </div>
    </div>
</div>

<div class="container">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td style="font-weight: 500;"><?= htmlspecialchars($u['nom']) ?></td>
                    <td style="color: var(--text-secondary);"><?= htmlspecialchars($u['email']) ?></td>
                    <td>
                        <span class="badge badge-<?= strtolower($u['role']) ?>">
                            <?= htmlspecialchars($u['role']) ?>
                        </span>
                    </td>
                    <td>
                        <?php $isCurrentUser = ($u['email'] === ($_SESSION['email'] ?? '')); ?>
                        <?php if ($u['role'] !== 'superadmin' && !$isCurrentUser): ?>
                            <form method="POST" action="index.php?page=supprimer_user" style="display: inline;">
                                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                <button type="submit" class="action-btn delete delete-user" data-user="<?= htmlspecialchars($u['nom']) ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        <?php elseif ($isCurrentUser): ?>
                            <span style="color: var(--text-secondary); font-size: 12px;">Votre compte</span>
                        <?php else: ?>
                            <span style="color: var(--text-secondary); font-size: 12px;">Système</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="app.js"></script>
<script>
    // Gérer les clics sur les boutons de suppression d'utilisateur
    document.querySelectorAll('.delete-user').forEach(btn => {
        btn.addEventListener('click', function(e) {
            const userName = this.dataset.user;
            confirmDeleteUser(e, userName);
        });
    });
</script>

</body>
</html>