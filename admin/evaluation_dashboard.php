<?php
// admin/evaluation_dashboard.php
session_start();

// Folosește funcțiile din sistemul de autentificare
require_once '../includes/auth.php';

// Verifică autentificarea și privilegiile admin
if (!isSessionValid() || !isAdmin()) {
    header('Location: /restricted.php');
    exit;
}

require_once '../config/database.php';

// Parametri pentru căutare, sortare și paginare
$search = $_GET['search'] ?? '';
$sortBy = $_GET['sort'] ?? 'timp_start';
$sortOrder = $_GET['order'] ?? 'DESC';
$page = max(1, intval($_GET['page'] ?? 1));
$itemsPerPage = 20;
$offset = ($page - 1) * $itemsPerPage;

// Validare pentru sortare
$allowedSorts = ['id', 'nume', 'prenume', 'cnp', 'institutia', 'scor', 'status', 'timp_start', 'durata_minute'];
if (!in_array($sortBy, $allowedSorts)) {
    $sortBy = 'timp_start';
}
$sortOrder = ($sortOrder === 'ASC') ? 'ASC' : 'DESC';

// Obține statisticile
$stmt = $pdo->query("SELECT * FROM test_statistics WHERE id = 1");
$stats = $stmt->fetch();

// Căutare și numărare totală
$whereClause = "";
$params = [];
if (!empty($search)) {
    $whereClause = "WHERE (nume LIKE ? OR prenume LIKE ? OR CONCAT(nume, ' ', prenume) LIKE ? OR institutia LIKE ? OR cnp LIKE ?)";
    $searchParam = "%$search%";
    $params = [$searchParam, $searchParam, $searchParam, $searchParam, $searchParam];
}

// Numără totalul pentru paginare
$countQuery = "SELECT COUNT(*) FROM test_attempts $whereClause";
$stmt = $pdo->prepare($countQuery);
$stmt->execute($params);
$totalTests = $stmt->fetchColumn();
$totalPages = ceil($totalTests / $itemsPerPage);

// Obține testele cu căutare, sortare și paginare
$query = "
    SELECT *, 
           TIMESTAMPDIFF(MINUTE, timp_start, timp_finalizare) as durata_minute
    FROM test_attempts 
    $whereClause
    ORDER BY $sortBy $sortOrder
    LIMIT $itemsPerPage OFFSET $offset
";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$tests = $stmt->fetchAll();

// Statistici pe module (doar dacă există teste completate)
$moduleStats = [];
if ($stats['total_completed'] > 0) {
    $stmt = $pdo->query("
        SELECT 
            i.modul,
            COUNT(*) as total_intrebari,
            AVG(CASE 
                WHEN JSON_EXTRACT(ta.raspunsuri_date, CONCAT('$.\"', i.id, '\"')) = i.raspuns_corect 
                THEN 1 ELSE 0 
            END) * 100 as procent_corect
        FROM intrebari i
        CROSS JOIN test_attempts ta
        WHERE ta.status IN ('promovat', 'esuat')
        GROUP BY i.modul
        ORDER BY i.modul
    ");
    $moduleStats = $stmt->fetchAll();
}

// Statistici suplimentare
$stmt = $pdo->query("
    SELECT 
        DATE(timp_start) as data,
        COUNT(*) as teste_pe_zi,
        AVG(scor) as scor_mediu_zi
    FROM test_attempts 
    WHERE status IN ('promovat', 'esuat') 
    AND timp_start >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY DATE(timp_start)
    ORDER BY data DESC
    LIMIT 7
");
$dailyStats = $stmt->fetchAll();

$stmt = $pdo->query("
    SELECT 
        institutia,
        COUNT(*) as numar_teste,
        AVG(scor) as scor_mediu,
        SUM(CASE WHEN status = 'promovat' THEN 1 ELSE 0 END) as promovati
    FROM test_attempts 
    WHERE status IN ('promovat', 'esuat')
    GROUP BY institutia
    ORDER BY numar_teste DESC
    LIMIT 10
");
$institutionStats = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Evaluare | Admin</title>
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/evaluation.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        .admin-header {
            background: linear-gradient(135deg, var(--primary-color), var(--hover-color));
            color: white;
            padding: 30px 0;
            margin-bottom: 40px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            text-align: center;
            transition: var(--transition);
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .stat-label {
            color: var(--gray-color);
            font-size: 0.9rem;
        }
        
        .search-controls {
            background: white;
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .search-input {
            flex: 1;
            min-width: 300px;
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: var(--border-radius);
            font-size: 1rem;
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        
        .btn-search {
            padding: 10px 20px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-clear {
            padding: 10px 20px;
            background: var(--gray-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: 1rem;
        }
        
        .tests-section {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .section-header {
            background: var(--primary-color);
            color: white;
            padding: 20px 25px;
            font-size: 1.3rem;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .tests-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .tests-table th,
        .tests-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .tests-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: var(--dark-color);
            cursor: pointer;
            position: relative;
            user-select: none;
        }
        
        .tests-table th:hover {
            background: #e9ecef;
        }
        
        .sort-icon {
            margin-left: 5px;
            font-size: 0.8rem;
            opacity: 0.5;
        }
        
        .sort-icon.active {
            opacity: 1;
            color: var(--primary-color);
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-promovat {
            background: rgba(52, 168, 83, 0.1);
            color: var(--secondary-color);
        }
        
        .status-esuat {
            background: rgba(234, 67, 53, 0.1);
            color: #ea4335;
        }
        
        .status-in_progres {
            background: rgba(255, 193, 7, 0.1);
            color: #ff9800;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            padding: 20px;
            background: #f8f9fa;
        }
        
        .pagination a,
        .pagination span {
            padding: 8px 12px;
            text-decoration: none;
            border-radius: var(--border-radius);
            color: var(--primary-color);
            border: 1px solid #e0e0e0;
        }
        
        .pagination a:hover {
            background: var(--primary-color);
            color: white;
        }
        
        .pagination .current {
            background: var(--primary-color);
            color: white;
        }
        
        .module-stats {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 15px;
            margin-top: 20px;
        }
        
        .module-card {
            background: white;
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            text-align: center;
        }
        
        .module-number {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 5px;
        }
        
        .module-name {
            font-size: 0.8rem;
            color: var(--gray-color);
            margin-bottom: 10px;
        }
        
        .module-percent {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--secondary-color);
        }
        
        .additional-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        
        .stat-section {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }
        
        .stat-section-header {
            background: var(--secondary-color);
            color: white;
            padding: 15px 20px;
            font-weight: 600;
        }
        
        .stat-section-content {
            padding: 20px;
        }
        
        .stat-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .stat-item:last-child {
            border-bottom: none;
        }
        
        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(3, 1fr);
            }
            .module-stats {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .module-stats {
                grid-template-columns: repeat(2, 1fr);
            }
            .search-controls {
                flex-direction: column;
            }
            .search-input {
                min-width: 100%;
            }
        }
        
        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .module-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header placeholder -->
    <div id="header-placeholder"></div>
    
    <div class="admin-header">
        <div class="container">
            <h1><i class="fas fa-chart-bar"></i> Dashboard Evaluare</h1>
            <p>Statistici și management teste de evaluare</p>
        </div>
    </div>
    
    <div class="container">
        <!-- Statistici generale -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $stats['total_attempts'] ?></div>
                <div class="stat-label">Total încercări</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['total_completed'] ?></div>
                <div class="stat-label">Teste finalizate</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['total_passed'] ?></div>
                <div class="stat-label">Teste promovate</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= number_format($stats['average_score'], 1) ?></div>
                <div class="stat-label">Scor mediu</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['total_completed'] > 0 ? round(($stats['total_passed'] / $stats['total_completed']) * 100) : 0 ?>%</div>
                <div class="stat-label">Rata de promovare</div>
            </div>
        </div>
        
        <!-- Statistici pe module -->
        <?php if (!empty($moduleStats)): ?>
        <div class="evaluation-card">
            <div class="section-header">
                <i class="fas fa-modules"></i> Performanță pe module
            </div>
            <div class="card-content">
                <div class="module-stats">
                    <?php foreach ($moduleStats as $module): ?>
                        <div class="module-card">
                            <div class="module-number">M<?= $module['modul'] ?></div>
                            <div class="module-name"><?= getModuleName($module['modul']) ?></div>
                            <div class="module-percent"><?= round($module['procent_corect']) ?>% corect</div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Controale căutare -->
        <div class="search-controls">
            <form method="GET" style="display: flex; gap: 15px; align-items: center; flex: 1;">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                       placeholder="Căutare după nume, prenume, instituție sau CNP..." class="search-input">
                <input type="hidden" name="sort" value="<?= htmlspecialchars($sortBy) ?>">
                <input type="hidden" name="order" value="<?= htmlspecialchars($sortOrder) ?>">
                <button type="submit" class="btn-search">
                    <i class="fas fa-search"></i> Căutare
                </button>
                <?php if (!empty($search)): ?>
                    <a href="?" class="btn-clear">
                        <i class="fas fa-times"></i> Șterge
                    </a>
                <?php endif; ?>
            </form>
            <div style="font-size: 0.9rem; color: var(--gray-color);">
                Afișate <?= count($tests) ?> din <?= $totalTests ?> teste
            </div>
        </div>
        
        <!-- Lista testelor -->
        <div class="tests-section">
            <div class="section-header">
                <span><i class="fas fa-list"></i> Toate testele</span>
                <span>Pagina <?= $page ?> din <?= $totalPages ?></span>
            </div>
            <div style="overflow-x: auto;">
                <table class="tests-table">
                    <thead>
                        <tr>
                            <?php
                            $columns = [
                                'id' => 'ID',
                                'nume' => 'Nume',
                                'cnp' => 'CNP',
                                'institutia' => 'Instituția',
                                'scor' => 'Scor',
                                'status' => 'Status',
                                'durata_minute' => 'Durată',
                                'timp_start' => 'Data'
                            ];
                            
                            foreach ($columns as $column => $label):
                                $newOrder = ($sortBy === $column && $sortOrder === 'ASC') ? 'DESC' : 'ASC';
                                $url = "?sort=$column&order=$newOrder&page=$page";
                                if (!empty($search)) $url .= "&search=" . urlencode($search);
                            ?>
                                <th onclick="window.location.href='<?= $url ?>'">
                                    <?= $label ?>
                                    <?php if ($sortBy === $column): ?>
                                        <i class="fas fa-sort-<?= $sortOrder === 'ASC' ? 'up' : 'down' ?> sort-icon active"></i>
                                    <?php else: ?>
                                        <i class="fas fa-sort sort-icon"></i>
                                    <?php endif; ?>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($tests)): ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 40px; color: var(--gray-color);">
                                    <i class="fas fa-search" style="font-size: 2rem; margin-bottom: 10px;"></i><br>
                                    Nu s-au găsit teste<?= !empty($search) ? ' pentru căutarea "' . htmlspecialchars($search) . '"' : '' ?>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($tests as $test): ?>
                                <tr>
                                    <td><?= $test['id'] ?></td>
                                    <td><?= htmlspecialchars($test['nume'] . ' ' . $test['prenume']) ?></td>
                                    <td><?= htmlspecialchars($test['cnp']) ?></td>
                                    <td><?= htmlspecialchars($test['institutia']) ?></td>
                                    <td>
                                        <?php if ($test['scor'] !== null): ?>
                                            <strong><?= $test['scor'] ?>/20</strong>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?= $test['status'] ?>">
                                            <?= ucfirst($test['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($test['durata_minute']): ?>
                                            <?= $test['durata_minute'] ?> min
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d.m.Y H:i', strtotime($test['timp_start'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginare -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=1&sort=<?= $sortBy ?>&order=<?= $sortOrder ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">
                            <i class="fas fa-angle-double-left"></i>
                        </a>
                        <a href="?page=<?= $page - 1 ?>&sort=<?= $sortBy ?>&order=<?= $sortOrder ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">
                            <i class="fas fa-angle-left"></i>
                        </a>
                    <?php endif; ?>
                    
                    <?php
                    $start = max(1, $page - 2);
                    $end = min($totalPages, $page + 2);
                    
                    for ($i = $start; $i <= $end; $i++):
                    ?>
                        <?php if ($i === $page): ?>
                            <span class="current"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?page=<?= $i ?>&sort=<?= $sortBy ?>&order=<?= $sortOrder ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?>&sort=<?= $sortBy ?>&order=<?= $sortOrder ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">
                            <i class="fas fa-angle-right"></i>
                        </a>
                        <a href="?page=<?= $totalPages ?>&sort=<?= $sortBy ?>&order=<?= $sortOrder ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">
                            <i class="fas fa-angle-double-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Statistici suplimentare -->
        <div class="additional-stats">
            <!-- Activitate recentă -->
            <?php if (!empty($dailyStats)): ?>
            <div class="stat-section">
                <div class="stat-section-header">
                    <i class="fas fa-calendar-alt"></i> Activitate ultimele 7 zile
                </div>
                <div class="stat-section-content">
                    <?php foreach ($dailyStats as $day): ?>
                        <div class="stat-item">
                            <span><?= date('d.m.Y', strtotime($day['data'])) ?></span>
                            <span><strong><?= $day['teste_pe_zi'] ?></strong> teste (mediu: <?= number_format($day['scor_mediu_zi'], 1) ?>)</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Top instituții -->
            <?php if (!empty($institutionStats)): ?>
            <div class="stat-section">
                <div class="stat-section-header">
                    <i class="fas fa-school"></i> Top 10 instituții
                </div>
                <div class="stat-section-content">
                    <?php foreach ($institutionStats as $institution): ?>
                        <div class="stat-item">
                            <span><?= htmlspecialchars(substr($institution['institutia'], 0, 30)) ?><?= strlen($institution['institutia']) > 30 ? '...' : '' ?></span>
                            <span><strong><?= $institution['numar_teste'] ?></strong> teste (<?= $institution['promovati'] ?> promovați)</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Acțiuni admin -->
        <div class="evaluation-card" style="margin-top: 30px;">
            <div class="section-header">
                <i class="fas fa-tools"></i> Acțiuni administrative
            </div>
            <div class="card-content">
                <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                    <a href="evaluation_export.php" class="btn btn-primary">
                        <i class="fas fa-download"></i> Export CSV
                    </a>
                    <a href="/resources/evaluation.html" class="btn btn-secondary">
                        <i class="fas fa-external-link-alt"></i> Vezi testul
                    </a>
                    <a href="/admin_logs.php" class="btn btn-secondary">
                        <i class="fas fa-list-alt"></i> Jurnale autentificare
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer placeholder -->
    <div id="footer-placeholder"></div>
    
    <script src="/js/main.js"></script>
</body>
</html>

<?php
function getModuleName($moduleNumber) {
    $modules = [
        1 => 'Introducere',
        2 => 'Tabla interactivă',
        3 => 'Primii pași',
        4 => 'Tehnici predare',
        5 => 'Implicarea elevilor',
        6 => 'Evaluare digitală'
    ];
    
    return $modules[$moduleNumber] ?? "Modul $moduleNumber";
}
?>