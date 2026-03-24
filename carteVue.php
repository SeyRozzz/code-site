<?php
// Sécurité : s'assurer que la session est démarrée si ce n'est pas déjà fait par l'index
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Carte Interactive - ONF GNSS</title>
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* --- CHARTE GRAPHIQUE --- */
        :root { 
            --bg-dark: #0a0f0d;
            --green: #2ecc71;
            --green-hover: #27ae60;
            --gold: #f1c40f;
            --red: #e74c3c;
            --txt-primary: #ffffff;
            --glass-bg: rgba(20, 20, 20, 0.85);
            --glass-border: rgba(255, 255, 255, 0.08);
        }

        body { 
            font-family: 'Segoe UI', sans-serif; 
            margin: 0; 
            background-color: var(--bg-dark);
            color: var(--txt-primary);
            background: linear-gradient(135deg, rgba(5,10,8,0.95) 0%, rgba(15,25,20,0.90) 100%),
                        url('https://images.unsplash.com/photo-1441974231531-c6227db76b6e?auto=format&fit=crop&w=1920&q=80') no-repeat center center/cover;
            background-attachment: fixed;
        }

        /* --- HEADER --- */
        .header { 
            background: rgba(10, 10, 10, 0.7); 
            backdrop-filter: blur(10px);
            padding: 15px 40px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            border-bottom: 1px solid var(--glass-border);
            position: sticky; top: 0; z-index: 1000;
        }

        h2 { margin: 0; font-weight: 600; font-size: 1.2rem; display: flex; align-items: center; gap: 10px; }

        .btn {
            text-decoration: none; padding: 8px 16px; border-radius: 6px;
            font-size: 14px; font-weight: 600; transition: 0.3s;
            display: inline-flex; align-items: center; gap: 8px; cursor: pointer;
        }

        .btn-nav { color: var(--txt-primary); border: 1px solid rgba(255,255,255,0.2); }
        .btn-nav:hover { background: rgba(255,255,255,0.1); border-color: white; }

        .btn-admin { border: 1px solid var(--gold); color: var(--gold); background: rgba(241, 196, 15, 0.1); }
        .btn-admin:hover { background: var(--gold); color: #000; }

        .user-pill {
            background: rgba(255,255,255,0.1); padding: 6px 12px; border-radius: 50px;
            font-size: 13px; border: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; gap: 8px;
        }

        /* --- CONTENU --- */
        .main-content { width: 95%; max-width: 1400px; margin: 30px auto; }

        .map-wrapper {
            border-radius: 15px; overflow: hidden; border: 1px solid var(--glass-border);
            box-shadow: 0 20px 50px rgba(0,0,0,0.5); margin-bottom: 20px;
        }

        #map { height: 500px; width: 100%; background: #1a1a1a; }

        .action-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 15px; flex-wrap: wrap; }
        .filter-group { display: flex; gap: 10px; align-items: center; }

        .input-tech {
            background: rgba(0, 0, 0, 0.4); border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 10px 15px; border-radius: 8px; color: white; outline: none;
        }

        /* --- TABLEAU --- */
        .table-container { background: var(--glass-bg); backdrop-filter: blur(12px); border-radius: 15px; border: 1px solid var(--glass-border); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th { background: rgba(255,255,255,0.05); color: var(--green); padding: 15px; text-align: left; font-size: 12px; text-transform: uppercase; cursor: pointer; }
        td { padding: 12px 15px; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 14px; }
        tr:hover { background: rgba(46, 204, 113, 0.05); }

        .btn-action {
            display: inline-flex; justify-content: center; align-items: center;
            width: 32px; height: 32px; border-radius: 6px; text-decoration: none; transition: 0.2s; border: none;
        }
        .btn-edit { border: 1px solid var(--green); color: var(--green); background: none; }
        .btn-edit:hover { background: var(--green); color: #000; }
        .btn-delete { border: 1px solid var(--red); color: var(--red); background: none; cursor: pointer; }
        .btn-delete:hover { background: var(--red); color: white; }

        .legend-bar {
            background: var(--glass-bg); padding: 12px; border-radius: 10px; display: flex; justify-content: center;
            gap: 15px; margin-bottom: 20px; font-size: 12px; border: 1px solid var(--glass-border);
        }
        .dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 5px; }
    </style>
</head>
<body>

<div class="header">
    <div style="display:flex; align-items:center; gap:20px;">
        <a href="index.php?page=accueil" class="btn btn-nav"><i class="fas fa-home"></i> Accueil</a>
        <h2><i class="fas fa-map-marked-alt" style="color:var(--green);"></i> Inventaire Forestier</h2>
    </div>
    
    <div style="display:flex; align-items:center; gap:10px;">
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if (in_array($_SESSION['role'], ['admin', 'superadmin'])): ?>
                <a href="index.php?page=admin" class="btn btn-admin"><i class="fas fa-cogs"></i> Panel Admin</a>
            <?php endif; ?>
            <div class="user-pill">
                <i class="fas fa-user-circle"></i> 
                <?= htmlspecialchars($_SESSION['nom'] ?? 'Agent ONF') ?>
            </div>
            <a href="index.php?page=logout" class="btn btn-nav" style="border-color:var(--red); color:var(--red);"><i class="fas fa-sign-out-alt"></i></a>
        <?php else: ?>
            <a href="index.php?page=login" class="btn btn-nav">Se connecter</a>
        <?php endif; ?>
    </div>
</div>

<div class="main-content">
    <div class="map-wrapper"><div id="map"></div></div>

    <div class="legend-bar">
        <span><div class="dot" style="background:#964B00;"></div> Chêne/Hêtre</span>
        <span><div class="dot" style="background:#006400;"></div> Sapin/Épicéa</span>
        <span><div class="dot" style="background:#E67E22;"></div> Pin/Mélèze</span>
        <span><div class="dot" style="background:#ffffff; border:1px solid #aaa;"></div> Bouleau</span>
        <span><div class="dot" style="background:#2ecc71;"></div> Autre</span>
    </div>

    <div class="action-bar">
        <div class="filter-group">
            <select class="input-tech" onchange="window.location='index.php?page=carte&id_projet='+this.value">
                <option value="0">Tous les projets</option>
                <?php foreach ($projets as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= (isset($_GET['id_projet']) && $_GET['id_projet'] == $p['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <input type="text" id="searchInput" class="input-tech" onkeyup="filterTable()" placeholder="🔍 Rechercher essence ou agent...">
    </div>

    <div class="table-container">
        <table id="treeTable">
            <thead>
                <tr>
                    <th onclick="sortTable(0)">Essence</th>
                    <th>Projet</th>
                    <th>Créateur</th>
                    <th onclick="sortTable(3)">H (m)</th>
                    <th onclick="sortTable(4)">D (cm)</th>
                    <th>GPS</th>
                    <?php if (isset($_SESSION['user_id'])): ?><th>Actions</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($arbres as $row): ?>
                <tr>
                    <td style="font-weight:600;"><?= htmlspecialchars($row['essence']) ?></td>
                    <td style="color:var(--gold);"><?= htmlspecialchars($row['projet_nom'] ?? 'Aucun') ?></td>
                    <td style="font-size: 0.85rem; color: #ccc;">
                        <i class="fas fa-user-circle" style="font-size: 0.7rem;"></i> <?= htmlspecialchars($row['createur_nom'] ?? 'Inconnu') ?>
                    </td>
                    <td><?= htmlspecialchars($row['hauteur']) ?></td>
                    <td><?= htmlspecialchars($row['diametre']) ?></td>
                    <td style="font-family:monospace; font-size:11px; color:#888;">[<?= round($row['latitude'],4) ?>, <?= round($row['longitude'],4) ?>]</td>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <td style="white-space:nowrap;">
                            <a href="index.php?page=modifier&id=<?= $row['id'] ?>" class="btn-action btn-edit"><i class="fas fa-pen"></i></a>
                            <?php if (in_array($_SESSION['role'], ['admin', 'superadmin'])): ?>
                                <form method="POST" action="index.php?page=supprimer" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                    <button type="submit" class="btn-action btn-delete" onclick="return confirm('Supprimer cet arbre définitivement ?')"><i class="fas fa-trash"></i></button>
                                </form>
                            <?php endif; ?>
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
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);

    function getColor(ess) {
        if (!ess) return "#2ecc71";
        ess = ess.toLowerCase();
        if (ess.includes('chêne') || ess.includes('hêtre')) return "#964B00";
        if (ess.includes('sapin') || ess.includes('épicéa')) return "#006400";
        if (ess.includes('pin')) return "#E67E22";
        if (ess.includes('bouleau')) return "#FFFFFF";
        return "#2ecc71";
    }

    var trees = <?= json_encode($arbres) ?>;
    trees.forEach(function(t) {
        if(t.latitude && t.longitude) {
            L.circleMarker([parseFloat(t.latitude), parseFloat(t.longitude)], {
                radius: 8, fillColor: getColor(t.essence), color: "#000", weight: 1, fillOpacity: 0.8
            }).addTo(map).bindPopup(
                "<div style='font-family: Segoe UI;'><b>🌳 " + t.essence + "</b><br>" +
                "<small>Projet : " + (t.projet_nom || 'NC') + "</small><br>" +
                "<small>Auteur : <b>" + (t.createur_nom || 'Inconnu') + "</b></small><br><hr>" +
                "H: " + t.hauteur + "m | D: " + t.diametre + "cm</div>"
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
</body>
</html>