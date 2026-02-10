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
            /* Fond Forêt Immersif */
            background: linear-gradient(135deg, rgba(5,10,8,0.95) 0%, rgba(15,25,20,0.90) 100%),
                        url('https://source.unsplash.com/1600x900/?forest,dark') no-repeat center center/cover;
            background-attachment: fixed;
        }

        /* --- HEADER GLASS --- */
        .header { 
            background: rgba(10, 10, 10, 0.7); 
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 15px 40px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            border-bottom: 1px solid var(--glass-border);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        h2 { margin: 0; font-weight: 600; font-size: 1.2rem; display: flex; align-items: center; gap: 10px; }

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
            cursor: pointer;
        }

        .btn-nav { color: var(--txt-primary); border: 1px solid rgba(255,255,255,0.2); }
        .btn-nav:hover { background: rgba(255,255,255,0.1); border-color: white; }

        .btn-admin { border: 1px solid var(--gold); color: var(--gold); background: rgba(241, 196, 15, 0.1); }
        .btn-admin:hover { background: var(--gold); color: #000; }

        .btn-login { background: var(--green); color: #000; border: none; }
        .btn-login:hover { background: var(--green-hover); color: white; }

        .user-pill {
            background: rgba(255,255,255,0.1);
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 13px;
            border: 1px solid rgba(255,255,255,0.1);
            display: flex; align-items: center; gap: 8px;
        }

        /* --- CONTENU PRINCIPAL --- */
        .main-content {
            width: 95%;
            max-width: 1400px;
            margin: 30px auto;
        }

        /* --- CARTE DESIGN --- */
        .map-wrapper {
            border-radius: 15px;
            overflow: hidden;
            border: 1px solid var(--glass-border);
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            margin-bottom: 20px;
        }

        /* Fond gris clair par défaut le temps que la carte charge */
        #map { height: 550px; width: 100%; background: #e0e0e0; }

        /* --- LÉGENDE STYLE DASHBOARD --- */
        .legend-bar {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            padding: 15px;
            border-radius: 10px;
            border: 1px solid var(--glass-border);
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
        }

        .legend-item { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #ccc; }
        .dot { width: 10px; height: 10px; border-radius: 50%; box-shadow: 0 0 2px rgba(255,255,255,0.5); }

        /* --- BARRE DE RECHERCHE --- */
        .search-wrapper {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 15px;
        }

        .input-tech {
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 12px 15px 12px 40px;
            border-radius: 8px;
            color: white;
            width: 300px;
            outline: none;
            transition: 0.3s;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="gray" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg>');
            background-repeat: no-repeat;
            background-position: 12px center;
        }

        .input-tech:focus {
            border-color: var(--green);
            background-color: rgba(0,0,0,0.6);
            box-shadow: 0 0 10px rgba(46, 204, 113, 0.2);
        }

        /* --- TABLEAU GLASS --- */
        .table-container {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            border-radius: 15px;
            border: 1px solid var(--glass-border);
            overflow: hidden;
        }

        table { width: 100%; border-collapse: collapse; }
        
        th { 
            background: rgba(255,255,255,0.05); 
            color: var(--green); 
            padding: 18px; 
            text-align: left; 
            text-transform: uppercase; 
            font-size: 12px; 
            letter-spacing: 1px;
            cursor: pointer;
            transition: 0.2s;
        }
        th:hover { background: rgba(255,255,255,0.1); color: white; }
        
        td { padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 14px; }
        tr:hover { background: rgba(46, 204, 113, 0.05); }

        .coord-text { font-family: 'Consolas', monospace; color: #888; font-size: 12px; }

        /* Nouveaux styles pour les boutons d'action */
        .btn-action {
            display: inline-flex; justify-content: center; align-items: center;
            width: 32px; height: 32px; border-radius: 6px; text-decoration: none; 
            font-size: 13px; margin-right: 5px; transition: 0.2s;
        }
        .btn-edit { 
            border: 1px solid var(--green); color: var(--green); background: rgba(46, 204, 113, 0.05);
        }
        .btn-edit:hover { background: var(--green); color: #000; }

        .btn-delete { 
            border: 1px solid var(--red); color: var(--red); background: rgba(231, 76, 60, 0.05);
        }
        .btn-delete:hover { background: var(--red); color: white; }

    </style>
</head>
<body>

<div class="header">
    <div style="display:flex; align-items:center; gap:20px;">
        <a href="index.php?page=accueil" class="btn btn-nav">
            <i class="fas fa-home"></i> Accueil
        </a>
        <h2><i class="fas fa-map-marked-alt" style="color:var(--green);"></i> Inventaire GNSS</h2>
    </div>
    
    <div style="display:flex; align-items:center; gap:10px;">
        <?php if (isset($_SESSION['nom'])): ?>
            
            <?php if (isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'superadmin')): ?>
                <a href="index.php?page=admin" class="btn btn-admin">
                    <i class="fas fa-cogs"></i> Panel Admin
                </a>
            <?php endif; ?>

            <div class="user-pill">
                <i class="fas fa-user-circle" style="color:var(--green);"></i> 
                <?= htmlspecialchars($_SESSION['nom']) ?>
            </div>
            
            <a href="index.php?page=logout" class="btn btn-nav" style="border-color:var(--red); color:var(--red);">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        
        <?php else: ?>
            <a href="index.php?page=login" class="btn btn-nav">Se connecter</a>
        <?php endif; ?>
    </div>
</div>

<div class="main-content">
    
    <div class="map-wrapper">
        <div id="map"></div>
    </div>

    <div class="legend-bar">
        <div class="legend-item"><div class="dot" style="background: #964B00;"></div> Chêne/Hêtre</div>
        <div class="legend-item"><div class="dot" style="background: #006400;"></div> Sapin/Épicéa</div>
        <div class="legend-item"><div class="dot" style="background: #E67E22;"></div> Pin/Mélèze</div>
        <div class="legend-item"><div class="dot" style="background: #ffffff; border:1px solid #aaa;"></div> Bouleau</div>
        <div class="legend-item"><div class="dot" style="background: #8E44AD;"></div> Frêne</div>
        <div class="legend-item"><div class="dot" style="background: #2ecc71;"></div> Autre</div>
    </div>

    <div class="search-wrapper">
        <input type="text" id="searchInput" class="input-tech" onkeyup="filterTable()" placeholder="Rechercher (essence, id...)">
    </div>

    <div class="table-container">
        <table id="treeTable">
            <thead>
                <tr>
                    <th onclick="sortTable(0)">Essence <i class="fas fa-sort"></i></th>
                    <th onclick="sortTable(1)">Hauteur <i class="fas fa-sort"></i></th>
                    <th onclick="sortTable(2)">Diamètre <i class="fas fa-sort"></i></th>
                    <th>Coordonnées GPS</th>
                    <?php if (isset($_SESSION['role'])): ?>
                        <th>Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($arbres)): ?>
                    <?php foreach ($arbres as $row): ?>
                    <tr>
                        <td style="font-weight:600; color:white;"><?= htmlspecialchars($row['essence'] ?? 'Inconnue') ?></td>
                        <td><?= htmlspecialchars($row['hauteur'] ?? '0') ?> m</td>
                        <td><?= htmlspecialchars($row['diametre'] ?? '0') ?> cm</td>
                        <td class="coord-text">[<?= htmlspecialchars($row['latitude'] ?? '0') ?>, <?= htmlspecialchars($row['longitude'] ?? '0') ?>]</td>
                        
                        <?php if (isset($_SESSION['role'])): ?>
                            <td>
                                <a href="index.php?page=modifier&id=<?= $row['id'] ?>" 
                                   class="btn-action btn-edit" title="Modifier">
                                   <i class="fas fa-pen"></i>
                                </a>

                                <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'superadmin'): ?>
                                    <a href="index.php?page=supprimer&id=<?= $row['id'] ?>" 
                                       class="btn-action btn-delete" 
                                       onclick="return confirm('Supprimer cet arbre définitivement ?')" title="Supprimer">
                                       <i class="fas fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // 1. Initialisation de la Carte
    var map = L.map('map').setView([48.297, 4.074], 15);
    
    // Tuiles STANDARD CLAIRES (OpenStreetMap)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    function getColor(essence) {
        if (!essence) return "#2ecc71";
        var e = essence.toLowerCase().trim();
        if (e.includes('chêne') || e.includes('hêtre')) return "#964B00"; 
        if (e.includes('sapin') || e.includes('épicéa')) return "#006400"; 
        if (e.includes('pin') || e.includes('mélèze')) return "#E67E22"; 
        if (e.includes('bouleau')) return "#FFFFFF"; 
        if (e.includes('frêne')) return "#8E44AD"; 
        return "#2ecc71"; 
    }

    var trees = <?php echo json_encode($arbres ?? []); ?>;
    
    if (trees.length > 0) {
        trees.forEach(function(t) {
            var lat = parseFloat(t.latitude);
            var lon = parseFloat(t.longitude);
            
            if(!isNaN(lat) && !isNaN(lon)) {
                var markerColor = getColor(t.essence);
                
                var marker = L.circleMarker([lat, lon], {
                    radius: 8, 
                    fillColor: markerColor, 
                    color: "#000000",
                    weight: 1, 
                    opacity: 1, 
                    fillOpacity: 0.8
                }).addTo(map);

                var ess = t.essence ? t.essence : "Non spécifiée";
                var hau = t.hauteur ? t.hauteur : "0";
                var dia = t.diametre ? t.diametre : "0";

                marker.bindPopup("<strong style='color:#333'>" + ess + "</strong><br>Hauteur : " + hau + "m<br>Diamètre : " + dia + "cm");
            }
        });
    }

    function filterTable() {
        var input = document.getElementById("searchInput");
        var filter = input.value.toLowerCase();
        var table = document.getElementById("treeTable");
        var tr = table.getElementsByTagName("tr");

        for (var i = 1; i < tr.length; i++) {
            var text = tr[i].textContent.toLowerCase();
            tr[i].style.display = text.includes(filter) ? "" : "none";
        }
    }

    function sortTable(n) {
        var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
        table = document.getElementById("treeTable");
        switching = true; dir = "asc"; 
        while (switching) {
            switching = false; rows = table.rows;
            for (i = 1; i < (rows.length - 1); i++) {
                shouldSwitch = false;
                x = rows[i].getElementsByTagName("TD")[n];
                y = rows[i + 1].getElementsByTagName("TD")[n];
                if (!x || !y) continue;
                var valX = isNaN(x.innerText) ? x.innerText.toLowerCase() : parseFloat(x.innerText);
                var valY = isNaN(y.innerText) ? y.innerText.toLowerCase() : parseFloat(y.innerText);
                if (dir == "asc") { if (valX > valY) { shouldSwitch = true; break; } } 
                else if (dir == "desc") { if (valX < valY) { shouldSwitch = true; break; } }
            }
            if (shouldSwitch) { rows[i].parentNode.insertBefore(rows[i + 1], rows[i]); switching = true; switchcount ++; } 
            else { if (switchcount == 0 && dir == "asc") { dir = "desc"; switching = true; } }
        }
    }
</script>
</body>
</html>