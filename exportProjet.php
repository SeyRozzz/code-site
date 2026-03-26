<?php
require_once 'config.php';

// Vérifier authentification
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

$id_projet = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$id_projet) {
    header('Location: index.php?page=carte&error=invalid_project');
    exit;
}

// Récupérer les infos du projet
$stmt = $pdo->prepare("SELECT p.*, u.nom as createur_nom FROM projets p LEFT JOIN utilisateurs u ON p.id_createur = u.id WHERE p.id = ?");
$stmt->execute([$id_projet]);
$projet = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$projet) {
    header('Location: index.php?page=carte&error=project_not_found');
    exit;
}

// Vérifier accès
if ($_SESSION['role'] === 'forestier') {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM projets_forestiers WHERE id_projet = ? AND id_forestier = ?");
    $stmt->execute([$id_projet, $_SESSION['user_id']]);
    $hasAccess = $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
    
    if (!$hasAccess && $projet['id_createur'] !== $_SESSION['user_id']) {
        header('Location: index.php?page=carte&error=no_access');
        exit;
    }
}

// Récupérer arbres et forestiers
$stmt = $pdo->prepare("SELECT * FROM arbres WHERE id_projet = ? ORDER BY id DESC");
$stmt->execute([$id_projet]);
$arbres = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT u.nom, u.email FROM utilisateurs u JOIN projets_forestiers pf ON u.id = pf.id_forestier WHERE pf.id_projet = ? ORDER BY u.nom ASC");
$stmt->execute([$id_projet]);
$forestiers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$format = $_GET['format'] ?? 'html';

if ($format === 'html') {
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport Projet - <?= htmlspecialchars($projet['nom']) ?></title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #2c3e50; background: #ecf0f1; line-height: 1.6; }
        body { padding: 10px; }
        .page { background: white; max-width: 210mm; margin: 20px auto; padding: 40px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); }
        .header-section { display: grid; grid-template-columns: 1fr 3fr 1fr; align-items: center; gap: 20px; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 3px solid #40D969; }
        .logo-box { text-align: center; }
        .logo { width: 60px; height: 60px; background: linear-gradient(135deg, #40D969 0%, #34B853 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 32px; font-weight: bold; }
        .title-section h1 { font-size: 28px; font-weight: 700; color: #1A1A1A; margin-bottom: 5px; }
        .title-section p { color: #7f8c8d; font-size: 14px; margin: 3px 0; }
        .meta-box { text-align: right; font-size: 12px; }
        .meta-box p { color: #7f8c8d; margin: 4px 0; }
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin: 30px 0; }
        .stat-card { background: linear-gradient(135deg, #40D969 0%, #34B853 100%); color: white; padding: 20px; border-radius: 8px; text-align: center; box-shadow: 0 4px 8px rgba(64, 217, 105, 0.2); }
        .stat-card.secondary { background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); }
        .stat-card.accent { background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); }
        .stat-card .icon { font-size: 24px; margin-bottom: 8px; }
        .stat-card .value { font-size: 28px; font-weight: bold; margin: 8px 0; }
        .stat-card .label { font-size: 11px; text-transform: uppercase; opacity: 0.9; letter-spacing: 1px; }
        .section { margin: 30px 0; }
        .section-title { font-size: 18px; font-weight: 700; color: white; background: linear-gradient(90deg, #40D969 0%, #34B853 100%); padding: 12px 16px; border-radius: 6px; margin-bottom: 16px; }
        .charts-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 20px 0; }
        .chart-box { background: #f8f9fa; padding: 16px; border-radius: 8px; border: 1px solid #e9ecef; }
        .chart-box h4 { font-size: 14px; font-weight: 600; margin-bottom: 12px; color: #2c3e50; }
        table { width: 100%; border-collapse: collapse; margin: 16px 0; background: white; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); border-radius: 6px; overflow: hidden; }
        table thead { background: linear-gradient(90deg, #40D969 0%, #34B853 100%); color: white; }
        table th { padding: 14px; text-align: left; font-weight: 600; font-size: 12px; text-transform: uppercase; }
        table td { padding: 12px 14px; border-bottom: 1px solid #ecf0f1; font-size: 13px; }
        table tbody tr:nth-child(odd) { background: #f8f9fa; }
        .info-block { background: linear-gradient(135deg, #ecf0f1 0%, #f5f7fa 100%); padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #40D969; }
        .info-block-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
        .info-item { padding: 12px; background: white; border-radius: 6px; border: 1px solid #e9ecef; }
        .info-item label { font-weight: 600; color: #40D969; display: block; font-size: 11px; text-transform: uppercase; margin-bottom: 6px; }
        .info-item value { font-size: 15px; color: #2c3e50; font-weight: 500; }
        .empty-state { text-align: center; padding: 40px 20px; color: #95a5a6; background: #f8f9fa; border-radius: 8px; border: 2px dashed #bdc3c7; }
        .footer { margin-top: 40px; padding-top: 20px; border-top: 2px solid #ecf0f1; text-align: center; font-size: 11px; color: #7f8c8d; }
        .action-buttons { position: sticky; top: 0; background: white; padding: 16px; margin: -40px -40px 20px -40px; display: flex; gap: 10px; justify-content: center; border-bottom: 1px solid #ecf0f1; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); }
        .btn { padding: 10px 20px; background: #40D969; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; transition: all 0.3s ease; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
        .btn:hover { background: #34B853; }
        .btn-secondary { background: #34495e; }
        .separator { height: 1px; background: linear-gradient(90deg, transparent, #40D969, transparent); margin: 30px 0; }
        @media print { .action-buttons { display: none; } .page { margin: 0; padding: 0; box-shadow: none; } }
        @media (max-width: 900px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } .charts-grid { grid-template-columns: 1fr; } .info-block-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 640px) { .page { padding: 20px; } .stats-grid, .info-block-grid { grid-template-columns: 1fr; } .header-section { grid-template-columns: 1fr; text-align: center; } }
    </style>
</head>
<body>
    <div class="action-buttons">
        <button class="btn" onclick="window.print()">🖨️ Imprimer en PDF</button>
        <button class="btn" onclick="exportToCSV()">📊 Exporter CSV</button>
        <a href="index.php?page=carte" class="btn btn-secondary">← Retour</a>
    </div>

    <div class="page">
        <div class="header-section">
            <div class="logo-box"><div class="logo">🌲</div></div>
            <div class="title-section">
                <h1><?= htmlspecialchars($projet['nom']) ?></h1>
                <p><strong>Rapport d'inventaire forestier</strong></p>
            </div>
            <div class="meta-box">
                <p><strong>Généré le</strong><br><?= date('d/m/Y à H:i') ?></p>
                <p><strong>Projet #</strong><?= (int)$id_projet ?></p>
            </div>
        </div>

        <div class="info-block">
            <div class="info-block-grid">
                <div class="info-item"><label>Créateur</label><value><?= htmlspecialchars($projet['createur_nom'] ?? 'N/A') ?></value></div>
                <div class="info-item"><label>Arbres</label><value><?= count($arbres) ?></value></div>
                <div class="info-item"><label>Équipe</label><value><?= count($forestiers) ?></value></div>
                <div class="info-item"><label>Créé le</label><value><?= isset($projet['created_at']) ? date('d/m/Y', strtotime($projet['created_at'])) : 'N/A' ?></value></div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card"><div class="icon">📊</div><div class="label">Arbres total</div><div class="value"><?= count($arbres) ?></div></div>
            <?php if (!empty($arbres)): 
                $totalHauteur = array_sum(array_column($arbres, 'hauteur'));
                $moyenneHauteur = round($totalHauteur / count($arbres), 2);
            ?>
            <div class="stat-card secondary"><div class="icon">📏</div><div class="label">Hauteur moy.</div><div class="value"><?= $moyenneHauteur ?> m</div></div>
            <?php 
                $totalDiametre = array_sum(array_column($arbres, 'diametre'));
                $moyenneDiametre = round($totalDiametre / count($arbres), 2);
            ?>
            <div class="stat-card accent"><div class="icon">⭕</div><div class="label">Diamètre moy.</div><div class="value"><?= $moyenneDiametre ?> cm</div></div>
            <div class="stat-card"><div class="icon">👥</div><div class="label">Équipe</div><div class="value"><?= count($forestiers) ?></div></div>
            <?php endif; ?>
        </div>

        <?php if (!empty($arbres)): ?>
        <div class="section">
            <div class="section-title">Statistiques détaillées</div>
            <div class="charts-grid">
                <div class="chart-box"><h4>Distribution des hauteurs</h4><canvas id="heightChart"></canvas></div>
                <div class="chart-box"><h4>Distribution des diamètres</h4><canvas id="diameterChart"></canvas></div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($forestiers)): ?>
        <div class="separator"></div>
        <div class="section">
            <div class="section-title">👥 Équipe affectée</div>
            <table>
                <thead><tr><th>Nom</th><th>Email</th></tr></thead>
                <tbody>
                    <?php foreach ($forestiers as $f): ?>
                    <tr><td><strong><?= htmlspecialchars($f['nom']) ?></strong></td><td><?= htmlspecialchars($f['email']) ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <div class="separator"></div>
        <div class="section">
            <div class="section-title">🌳 Inventaire des arbres</div>
            <?php if (!empty($arbres)): ?>
            <table id="trees-table">
                <thead><tr><th>#ID</th><th>Essence</th><th>Hauteur (m)</th><th>Diamètre (cm)</th><th>Latitude</th><th>Longitude</th></tr></thead>
                <tbody>
                    <?php foreach ($arbres as $arbre): ?>
                    <tr><td><?= (int)$arbre['id'] ?></td><td><strong><?= htmlspecialchars($arbre['essence']) ?></strong></td><td><?= htmlspecialchars($arbre['hauteur']) ?></td><td><?= htmlspecialchars($arbre['diametre']) ?></td><td><?= htmlspecialchars($arbre['latitude'] ?? 'N/A') ?></td><td><?= htmlspecialchars($arbre['longitude'] ?? 'N/A') ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state"><p>📭 Aucun arbre</p></div>
            <?php endif; ?>
        </div>

        <div class="footer">
            <p>Rapport généré - Système de gestion forestière ONF</p>
            <p><?= htmlspecialchars($projet['nom']) ?> • Projet #<?= (int)$id_projet ?></p>
        </div>
    </div>

    <script>
        const arbresData = <?php echo json_encode($arbres); ?>;
        if (arbresData && arbresData.length > 0 && document.getElementById('heightChart')) {
            creerGraphiques();
        }
        function creerGraphiques() {
            const hauteurs = arbresData.map(a => parseFloat(a.hauteur));
            const diamètres = arbresData.map(a => parseFloat(a.diametre));
            new Chart(document.getElementById('heightChart'), {
                type: 'bar',
                data: { labels: hauteurs.map((_, i) => `Arbre ${arbresData[i].id}`), datasets: [{label: 'Hauteur (m)', data: hauteurs, backgroundColor: 'rgba(64, 217, 105, 0.7)', borderColor: '#40D969', borderWidth: 1, borderRadius: 4}] },
                options: { responsive: true, maintainAspectRatio: true, indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, grid: { display: true, color: '#f0f0f0' } } } }
            });
            new Chart(document.getElementById('diameterChart'), {
                type: 'bar',
                data: { labels: diamètres.map((_, i) => `Arbre ${arbresData[i].id}`), datasets: [{label: 'Diamètre (cm)', data: diamètres, backgroundColor: 'rgba(52, 152, 219, 0.7)', borderColor: '#3498db', borderWidth: 1, borderRadius: 4}] },
                options: { responsive: true, maintainAspectRatio: true, indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, grid: { display: true, color: '#f0f0f0' } } } }
            });
        }
        function exportToCSV() {
            const table = document.getElementById('trees-table');
            if (!table) { alert('Aucune donnée'); return; }
            let csv = 'Rapport d\'inventaire\nProjet: <?= htmlspecialchars($projet['nom']) ?>\nGénéré le: ' + new Date().toLocaleDateString('fr-FR') + '\n\n';
            const rows = Array.from(table.querySelectorAll('tr'));
            csv += rows.map(row => Array.from(row.querySelectorAll('td, th')).map(cell => `"${cell.textContent.trim()}"`).join(',')).join('\n');
            const blob = new Blob([csv], { type: 'text/csv' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'projet_<?= htmlspecialchars($projet['nom']) ?>_' + new Date().toISOString().split('T')[0] + '.csv';
            link.click();
        }
    </script>
</body>
</html>
<?php
} elseif ($format === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="projet_' . date('Y-m-d') . '.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Projet', htmlspecialchars($projet['nom'])]);
    fputcsv($output, ['Généré le', date('d/m/Y H:i')]);
    fputcsv($output, []);
    fputcsv($output, ['Essence', 'Hauteur (m)', 'Diamètre (cm)', 'Latitude', 'Longitude']);
    foreach ($arbres as $arbre) {
        fputcsv($output, [$arbre['essence'], $arbre['hauteur'], $arbre['diametre'], $arbre['latitude'] ?? '', $arbre['longitude'] ?? '']);
    }
    fclose($output);
} else {
    header('Location: index.php?page=carte&error=invalid_format');
}
?>
