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
            /* Image de fond forestière */
            background: linear-gradient(135deg, rgba(5,10,8,0.95) 0%, rgba(15,25,20,0.90) 100%),
                        url('https://source.unsplash.com/1600x900/?forest,dark') no-repeat center center/cover;
            background-attachment: fixed;
        }

        /* --- EN-TÊTE --- */
        .header { 
            background: rgba(10, 10, 10, 0.7); 
            backdrop-filter: blur(10px);
            padding: 15px 40px; 
            display: flex; justify-content: space-between; align-items: center; 
            border-bottom: 1px solid var(--glass-border);
            position: sticky; top: 0; z-index: 1000;
        }
        h2 { margin: 0; font-weight: 600; font-size: 1.2rem; display: flex; align-items: center; gap: 10px; }

        .btn { text-decoration: none; padding: 8px 16px; border-radius: 6px; font-size: 14px; font-weight: 600; transition: 0.3s; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; }
        .btn-nav { color: var(--txt-primary); border: 1px solid rgba(255,255,255,0.2); }
        .btn-nav:hover { background: rgba(255,255,255,0.1); border-color: white; }
        .btn-admin { border: 1px solid var(--gold); color: var(--gold); background: rgba(241, 196, 15, 0.1); }
        .btn-admin:hover { background: var(--gold); color: #000; }

        .user-pill { background: rgba(255,255,255,0.1); padding: 6px 12px; border-radius: 50px; font-size: 13px; border: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; gap: 8px; }

        /* --- CONTENU --- */
        .main-content { width: 95%; max-width: 1400px; margin: 30px auto; }

        /* CARTE */
        .map-wrapper { border-radius: 15px; overflow: hidden; border: 1px solid var(--glass-border); box-shadow: 0 20px 50px rgba(0,0,0,0.5); margin-bottom: 20px; }
        #map { height: 500px; width: 100%; background: #e0e0e0; }

        /* LÉGENDE */
        .legend-bar { background: var(--glass-bg); backdrop-filter: blur(10px); padding: 15px; border-radius: 10px; border: 1px solid var(--glass-border); display: flex; justify-content: center; flex-wrap: wrap; gap: 20px; margin-bottom: 30px; }
        .legend-item { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #ccc; }
        .dot { width: 10px; height: 10px; border-radius: 50%; box-shadow: 0 0 2px rgba(255,255,255,0.5); }

        /* --- BARRE D'OUTILS (Recherche + Export) --- */
        .tools-bar { display: flex; justify-content: flex-end; align-items: center; gap: 10px; margin-bottom: 15px; flex-wrap: wrap; }

        .input-tech { 
            background: rgba(0, 0, 0, 0.4); border: 1px solid rgba(255, 255, 255, 0.2); 
            padding: 10px 15px; border-radius: 6px; color: white; width: 250px; outline: none; transition: 0.3s; 
        }
        .input-tech:focus { border-color: var(--green); background-color: rgba(0,0,0,0.6); }

        .btn-search { background: var(--green); color: #000; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; font-weight: bold; }
        .btn-search:hover { background: var(--green-hover); }

        /* --- TABLEAU --- */
        .table-container { background: var(--glass-bg); backdrop-filter: blur(12px); border-radius: 15px; border: 1px solid var(--glass-border); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        
        /* En-têtes cliquables pour le tri */
        th { background: rgba(255,255,255,0.05); padding: 18px; text-align: left; text-transform: uppercase; font-size: 12px; letter-spacing: 1px; }
        th a { color: var(--green); text-decoration: none; display: flex; align-items: center; gap: 5px; transition: 0.2s; }
        th a:hover { color: white; }
        
        td { padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 14px; }
        tr:hover { background: rgba(46, 204, 113, 0.05); }
        .coord-text { font-family: 'Consolas', monospace; color: #888; font-size: 12px; }

        /* Boutons Actions (Tableau) */
        .btn-action { display: inline-flex; justify-content: center; align-items: center; width: 32px; height: 32px; border-radius: 6px; text-decoration: none; font-size: 13px; margin-right: 5px; transition: 0.2s; }
        .btn-edit { border: 1px solid var(--green); color: var(--green); background: rgba(46, 204, 113, 0.05); }
        .btn-edit:hover { background: var(--green); color: #000; }
        .btn-delete { border: 1px solid var(--red); color: var(--red); background: rgba(231, 76, 60, 0.05); }
        .btn-delete:hover { background: var(--red); color: white; }
        
        /* Messages d'alerte PHP */
        .alert { padding: 12px 20px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; text-align: center; font-weight: 500; }
        .alert-success { background: rgba(46, 204, 113, 0.2); border: 1px solid var(--green); color: var(--green); }
        .alert-error { background: rgba(231, 76, 60, 0.2); border: 1px solid var(--red); color: var(--red); }
    </style>
</head>
<body>

<div class="header">
    <div style="display:flex; align-items:center; gap:20px;">
        <a href="index.php?page=accueil" class="btn btn-nav"><i class="fas fa-home"></i> Accueil</a>
        <h2><i class="fas fa-map-marked-alt" style="color:var(--green);"></i> Inventaire GNSS</h2>
    </div>
    
    <div style="display:flex; align-items:center; gap:10px;">
        <?php if (isset($_SESSION['nom'])): ?>
            <?php if (isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'superadmin')): ?>
                <a href="index.php?page=admin" class="btn btn-admin"><i class="fas fa-cogs"></i> Panel Admin</a>
            <?php endif; ?>
            
            <div class="user-pill"><i class="fas fa-user-circle" style="color:var(--green);"></i> <?= htmlspecialchars($_SESSION['nom']) ?></div>
            <a href="index.php?page=logout" class="btn btn-nav" style="border-color:var(--red); color:var(--red);"><i class="fas fa-sign-out-alt"></i></a>
        <?php else: ?>
            <a href="index.php?page=login" class="btn btn-nav">Se connecter</a>
        <?php endif; ?>
    </div>
</div>

<div class="main-content">

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'succes_modif'): ?>
        <div class="alert alert-success"><i class="fas fa-check"></i> Modification enregistrée avec succès.</div>
    <?php endif; ?>
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'succes_ajout'): ?>
        <div class="alert alert-success"><i class="fas fa-check"></i> Nouvel arbre ajouté à l'inventaire.</div>
    <?php endif; ?>
    <?php if (isset($_GET['error']) && $_GET['error'] === 'interdit'): ?>
        <div class="alert alert-error"><i class="fas fa-times"></i> Action non autorisée. Vous n'avez pas les droits.</div>
    <?php endif; ?>
    
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

    <div class="tools-bar">
        <form method="GET" action="index.php" style="display:flex; gap:10px;">
            <input type="hidden" name="page" value="carte">
            <input type="hidden" name="sort" value="<?= htmlspecialchars($sort ?? 'id') ?>">
            <input type="hidden" name="dir" value="<?= htmlspecialchars($dir ?? 'ASC') ?>">
            
            <input type="text" name="q" class="input-tech" placeholder="Rechercher (Essence, ID...)" value="<?= htmlspecialchars($search ?? '') ?>">
            <button type="submit" class="btn-search"><i class="fas fa-search"></i></button>
        </form>

        <a href="export.php" class="btn btn-nav" target="_blank" title="Télécharger l'inventaire">
            <i class="fas fa-file-csv"></i> Export CSV
        </a>
    </div>

    <div class="table-container">
        <table id="treeTable">
            <thead>
                <tr>
                    <th><a href="index.php?page=carte&sort=essence&dir=<?= $nextDir ?>&q=<?= htmlspecialchars($search) ?>">Essence <i class="fas fa-sort"></i></a></th>
                    <th><a href="index.php?page=carte&sort=hauteur&dir=<?= $nextDir ?>&q=<?= htmlspecialchars($search) ?>">Hauteur <i class="fas fa-sort"></i></a></th>
                    <th><a href="index.php?page=carte&sort=diametre&dir=<?= $nextDir ?>&q=<?= htmlspecialchars($search) ?>">Diamètre <i class="fas fa-sort"></i></a></th>
                    <th>Coordonnées GPS</th>
                    <th>Actions</th>
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
                        
                        <td>
                            <a href="index.php?page=modifier&id=<?= $row['id'] ?>" class="btn-action btn-edit" title="Modifier">
                                <i class="fas fa-pen"></i>
                            </a>

                            <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'superadmin'): ?>
                                <a href="index.php?page=supprimer&id=<?= $row['id'] ?>" class="btn-action btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet arbre ?')" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align:center; padding:30px; color:#aaa;">
                            <i class="fas fa-tree" style="font-size:20px; display:block; margin-bottom:10px;"></i>
                            Aucun arbre trouvé pour cette recherche.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // 1. Initialisation de la Carte
    // On centre sur la Forêt d'Orient par défaut (coordonnées approximatives)
    var map = L.map('map').setView([48.297, 4.074], 15);
    
    // Tuiles OpenStreetMap (Fond clair standard)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    // Fonction pour déterminer la couleur selon l'essence
    function getColor(essence) {
        if (!essence) return "#2ecc71"; // Vert par défaut
        var e = essence.toLowerCase().trim();
        if (e.includes('chêne') || e.includes('hêtre')) return "#964B00"; // Marron
        if (e.includes('sapin') || e.includes('épicéa')) return "#006400"; // Vert foncé
        if (e.includes('pin') || e.includes('mélèze')) return "#E67E22"; // Orange
        if (e.includes('bouleau')) return "#FFFFFF"; // Blanc
        if (e.includes('frêne')) return "#8E44AD"; // Violet
        return "#2ecc71"; 
    }

    // Récupération des données PHP transformées en JSON pour JS
    var trees = <?php echo json_encode($arbres ?? []); ?>;
    
    // Ajout des points sur la carte
    if (trees.length > 0) {
        trees.forEach(function(t) {
            var lat = parseFloat(t.latitude);
            var lon = parseFloat(t.longitude);
            
            // On vérifie que les coordonnées sont valides
            if(!isNaN(lat) && !isNaN(lon)) {
                var markerColor = getColor(t.essence);
                
                // Création du cercle
                var marker = L.circleMarker([lat, lon], {
                    radius: 8, 
                    fillColor: markerColor, 
                    color: "#000000", // Bordure noire pour le contraste
                    weight: 1, 
                    opacity: 1, 
                    fillOpacity: 0.8
                }).addTo(map);

                // Informations de la Popup
                var ess = t.essence ? t.essence : "Non spécifiée";
                var hau = t.hauteur ? t.hauteur : "0";
                var dia = t.diametre ? t.diametre : "0";

                marker.bindPopup(
                    "<strong style='color:#333; font-size:14px;'>" + ess + "</strong><br>" +
                    "Hauteur : " + hau + " m<br>" +
                    "Diamètre : " + dia + " cm"
                );
            }
        });
    }
</script>

</body>
</html>