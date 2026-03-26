<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Carte des projets</title>
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
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
            --accent: #4DA6FF;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg);
            color: var(--text);
        }
        
        .header {
            background: var(--bg);
            border-bottom: 1px solid var(--border);
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        h2 {
            font-size: 20px;
            font-weight: 600;
            margin: 0;
        }
        
        .btn {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        
        .btn-secondary {
            background: var(--bg-secondary);
            color: var(--text);
            border: 1px solid var(--border);
        }
        
        .btn-secondary:hover {
            background: #393939;
            border-color: #484848;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            opacity: 0.85;
        }
        
        .header-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .user-badge {
            background: var(--bg-secondary);
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .main-content {
            max-width: 1400px;
            margin: 24px auto;
            padding: 0 24px;
        }
        
        .map-container {
            border-radius: 12px;
            border: 1px solid var(--border);
            overflow: hidden;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        #map {
            height: 500px;
            background: #252525;
        }
        
        .controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }
        
        select, input[type="text"] {
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.2s;
            background: var(--bg);
        }
        
        select:focus, input[type="text"]:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(64, 217, 105, 0.2);
        }
        
        .table-wrapper {
            border-radius: 12px;
            border: 1px solid var(--border);
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border);
            cursor: pointer;
            user-select: none;
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
        
        .actions {
            display: flex;
            gap: 8px;
        }
        
        .icon-btn {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            border: 1px solid var(--border);
            background: var(--bg);
            color: var(--text);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            text-decoration: none;
        }
        
        .icon-btn:hover {
            background: var(--bg-secondary);
            border-color: var(--text-secondary);
        }
        
        .icon-btn.delete {
            color: #FF3B30;
            border-color: #FFE5E5;
        }
        
        .icon-btn.delete:hover {
            background: #FFE5E5;
        }
    </style>
</head>
<body>

<div class="header">
    <div class="header-left">
        <a href="index.php?page=accueil" class="btn btn-secondary">
            <i class="fas fa-home"></i> Accueil
        </a>
        <h2>Inventaire forestier</h2>
    </div>
    
    <div class="header-right">
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if (in_array($_SESSION['role'], ['admin', 'superadmin'])): ?>
                <a href="index.php?page=admin" class="btn btn-secondary">
                    <i class="fas fa-sliders-h"></i> Admin
                </a>
            <?php endif; ?>
            <div class="user-badge">
                <i class="fas fa-user-circle"></i>
                <?= htmlspecialchars($_SESSION['nom'] ?? 'Agent') ?>
            </div>
            <a href="index.php?page=logout" class="btn btn-secondary">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        <?php else: ?>
            <a href="index.php?page=login" class="btn btn-primary">Se connecter</a>
        <?php endif; ?>
    </div>
</div>

<div class="main-content">
    <div class="map-container">
        <div id="map"></div>
    </div>

    <div class="controls">
        <select onchange="window.location='index.php?page=carte&id_projet='+this.value">
            <option value="0">Tous les projets</option>
            <?php foreach ($projets as $p): ?>
                <option value="<?= $p['id'] ?>" <?= (isset($_GET['id_projet']) && $_GET['id_projet'] == $p['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Rechercher essence ou agent...">
    </div>

    <div class="table-wrapper">
        <table id="treeTable">
            <thead>
                <tr>
                    <th onclick="sortTable(0)">Essence</th>
                    <th>Projet</th>
                    <th>Agent</th>
                    <th onclick="sortTable(3)">Hauteur (m)</th>
                    <th onclick="sortTable(4)">Diamètre (cm)</th>
                    <th>Localisation</th>
                    <?php if (isset($_SESSION['user_id'])): ?><th>Actions</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($arbres as $row): ?>
                <tr>
                    <td style="font-weight: 500;"><?= htmlspecialchars($row['essence']) ?></td>
                    <td><?= htmlspecialchars($row['projet_nom'] ?? '-') ?></td>
                    <td style="color: var(--text-secondary);">
                        <?= htmlspecialchars($row['createur_nom'] ?? 'Inconnu') ?>
                    </td>
                    <td><?= htmlspecialchars($row['hauteur']) ?></td>
                    <td><?= htmlspecialchars($row['diametre']) ?></td>
                    <td style="font-size: 12px; color: var(--text-secondary); font-family: monospace;">
                        <?= round($row['latitude'], 4) ?>, <?= round($row['longitude'], 4) ?>
                    </td>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <td>
                            <div class="actions">
                                <a href="index.php?page=modifier&id=<?= $row['id'] ?>" class="icon-btn" title="Modifier">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <?php if (in_array($_SESSION['role'], ['admin', 'superadmin'])): ?>
                                    <form method="POST" action="index.php?page=supprimer" style="display: inline;">
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                        <button type="submit" class="icon-btn delete delete-tree" title="Supprimer" data-essence="<?= htmlspecialchars($row['essence']) ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    var map = L.map('map').setView([48.297, 4.074], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    function getColor(ess) {
        if (!ess) return "#34C759";
        ess = ess.toLowerCase();
        if (ess.includes('chêne') || ess.includes('hêtre')) return "#8B4513";
        if (ess.includes('sapin') || ess.includes('épicéa')) return "#228B22";
        if (ess.includes('pin')) return "#D2691E";
        if (ess.includes('bouleau')) return "#696969";
        return "#34C759";
    }

    var trees = <?= json_encode($arbres) ?>;
    trees.forEach(function(t) {
        if (t.latitude && t.longitude) {
            L.circleMarker([parseFloat(t.latitude), parseFloat(t.longitude)], {
                radius: 8,
                fillColor: getColor(t.essence),
                color: "#fff",
                weight: 2,
                fillOpacity: 0.85
            }).addTo(map).bindPopup(
                "<strong>" + t.essence + "</strong><br>" +
                "Projet: " + (t.projet_nom || '-') + "<br>" +
                "Agent: " + (t.createur_nom || 'Inconnu') + "<br>" +
                "H: " + t.hauteur + "m | D: " + t.diametre + "cm"
            );
        }
    });

    function filterTable() {
        var filter = document.getElementById("searchInput").value.toLowerCase();
        var rows = document.querySelector("#treeTable tbody").rows;
        for (var i = 0; i < rows.length; i++) {
            rows[i].style.display = rows[i].textContent.toLowerCase().includes(filter) ? "" : "none";
        }
    }

    function sortTable(n) {
        var table = document.getElementById("treeTable");
        var rows = Array.from(table.rows).slice(1);
        var dir = table.getAttribute("data-dir") === "asc" ? "desc" : "asc";
        rows.sort((a, b) => {
            var x = a.cells[n].textContent.toLowerCase();
            var y = b.cells[n].textContent.toLowerCase();
            return dir === "asc" ? x.localeCompare(y, undefined, {numeric: true}) : y.localeCompare(x, undefined, {numeric: true});
        });
        rows.forEach(row => table.querySelector("tbody").appendChild(row));
        table.setAttribute("data-dir", dir);
    }
</script>

<script src="app.js"></script>
<script>
    // Gérer les clics sur les boutons de suppression d'arbre
    document.querySelectorAll('.delete-tree').forEach(btn => {
        btn.addEventListener('click', function(e) {
            const essence = this.dataset.essence;
            confirmDeleteTree(e, essence);
        });
    });
</script>

</body>
</html>