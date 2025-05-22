<?php
// admin/evaluation_dashboard.php
session_start();

// Verifică autentificarea admin
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true || $_SESSION['user_type'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

require_once '../includes/auth.php';  // În loc de 'includes/auth.php'
require_once '../config/database.php';

// Obține statisticile
$stmt = $pdo->query("SELECT * FROM test_statistics WHERE id = 1");
$stats = $stmt->fetch();

// Obține toate testele
$stmt = $pdo->query("
    SELECT *, 
           TIMESTAMPDIFF(MINUTE, timp_start, timp_finalizare) as durata_minute
    FROM test_attempts 
    ORDER BY timp_start DESC
");
$tests = $stmt->fetchAll();

// Statistici pe module
$stmt = $pdo->query("
    SELECT 
        i.modul,
        COUNT(*) as total_intrebari,
        AVG(CASE 
            WHEN JSON_EXTRACT(ta.raspunsuri_date, CONCAT('$.', i.id)) = i.raspuns_corect 
            THEN 1 ELSE 0 
        END) * 100 as procent_corect
    FROM intrebari i
    CROSS JOIN test_attempts ta
    WHERE ta.status IN ('promovat', 'esuat')
    GROUP BY i.modul
    ORDER BY i.modul
");
$moduleStats = $stmt->fetchAll();
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
    <style>
        .admin-header {
            background: linear-gradient(135deg, var(--primary-color), var(--hover-color));
            color: white;
            padding: 30px 0;
            margin-bottom: 40px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            text-align: center;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .stat-label {
            color: var(--gray-color);
            font-size: 1rem;
        }
        
        .tests-section {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }
        
        .section-header {
            background: var(--primary-color);
            color: white;
            padding: 20px 25px;
            font-size: 1.3rem;
            font-weight: 600;
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
        
        .module-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 30px;
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
            font-size: 0.9rem;
            color: var(--gray-color);
            margin-bottom: 10px;
        }
        
        .module-percent {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--secondary-color);
        }
    </style>
</head>
<body>
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
        <div class="evaluation-card">
            <div class="section-header">
                <i class="fas fa-modules"></i> Performanță pe module
            </div>
            <div class="card-content">
                <div class="module-stats">
                    <?php foreach ($moduleStats as $module): ?>
                        <div class="module-card">
                            <div class="module-number">Modul <?= $module['modul'] ?></div>
                            <div class="module-name"><?= getModuleName($module['modul']) ?></div>
                            <div class="module-percent"><?= round($module['procent_corect']) ?>% corect</div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Lista testelor -->
        <div class="tests-section">
            <div class="section-header">
                <i class="fas fa-list"></i> Toate testele (<?= count($tests) ?>)
            </div>
            <div style="overflow-x: auto;">
                <table class="tests-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nume</th>
                            <th>CNP</th>
                            <th>Instituția</th>
                            <th>Scor</th>
                            <th>Status</th>
                            <th>Durată</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
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
                    </tbody>
                </table>
            </div>
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
                    <a href="/admin_logs.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Înapoi la Admin
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php
function getModuleName($moduleNumber) {
    $modules = [
        1 => 'Introducere în digitalizare',
        2 => 'Tabla interactivă - prezentare',
        3 => 'Primii pași cu tabla',
        4 => 'Tehnici de predare digitală',
        5 => 'Implicarea elevilor',
        6 => 'Evaluare și feedback digital'
    ];
    
    return $modules[$moduleNumber] ?? "Modul $moduleNumber";
}
?>