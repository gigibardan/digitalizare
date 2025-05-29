<?php
// Verificăm dacă utilizatorul este autentificat și este admin
require_once 'includes/auth.php';
require_once 'includes/config.php'; 


// Dacă nu este admin, redirecționăm (folosim funcția din auth.php)
if (!isSessionValid() || !isAdmin()) {
    header('Location: /restricted.php');
    exit();
}

// Procesăm acțiunile de ștergere
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'clear_all':
            if (clearAllLogs()) {
                header('Location: ' . $_SERVER['PHP_SELF'] . '?message=all_cleared&type=success');
                exit();
            } else {
                header('Location: ' . $_SERVER['PHP_SELF'] . '?message=error&type=error');
                exit();
            }
            break;
            
        case 'clear_by_type':
            $type = $_POST['clear_type'] ?? '';
            if (!empty($type) && in_array($type, ['SUCCESS', 'FAILED', 'LOGOUT'])) {
                if (clearLogsByType($type)) {
                    header('Location: ' . $_SERVER['PHP_SELF'] . '?message=type_cleared&type=success&cleared_type=' . $type);
                    exit();
                } else {
                    header('Location: ' . $_SERVER['PHP_SELF'] . '?message=error&type=error');
                    exit();
                }
            }
            break;
            
        case 'clear_old':
            $days = intval($_POST['clear_days'] ?? 30);
            if ($days > 0) {
                if (clearOldLogs($days)) {
                    header('Location: ' . $_SERVER['PHP_SELF'] . '?message=old_cleared&type=success&days=' . $days);
                    exit();
                } else {
                    header('Location: ' . $_SERVER['PHP_SELF'] . '?message=error&type=error');
                    exit();
                }
            }
            break;
            
        case 'clear_selected':
            $selected_indices = $_POST['selected_logs'] ?? [];
            if (!empty($selected_indices)) {
                if (clearSelectedLogs($selected_indices)) {
                    $count = count($selected_indices);
                    header('Location: ' . $_SERVER['PHP_SELF'] . '?message=selected_cleared&type=success&count=' . $count);
                    exit();
                } else {
                    header('Location: ' . $_SERVER['PHP_SELF'] . '?message=error&type=error');
                    exit();
                }
            }
            break;
    }
}

// Procesăm mesajele din URL
$message = '';
$message_type = '';

if (isset($_GET['message'])) {
    $message_type = $_GET['type'] ?? 'info';
    
    switch ($_GET['message']) {
        case 'all_cleared':
            $message = 'Toate logurile au fost șterse cu succes! A fost creat un backup.';
            break;
        case 'type_cleared':
            $cleared_type = $_GET['cleared_type'] ?? 'necunoscut';
            $message = "Logurile de tipul {$cleared_type} au fost șterse cu succes! A fost creat un backup.";
            break;
        case 'old_cleared':
            $days = $_GET['days'] ?? '30';
            $message = "Logurile mai vechi de {$days} zile au fost șterse cu succes! A fost creat un backup.";
            break;
        case 'selected_cleared':
            $count = $_GET['count'] ?? '0';
            $message = "{$count} înregistrări au fost șterse cu succes! A fost creat un backup.";
            break;
        case 'error':
            $message = 'Eroare la ștergerea logurilor.';
            break;
    }
}

// Parametrii pentru sortare, căutare și paginație
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'date';
$sort_order = isset($_GET['order']) ? $_GET['order'] : 'desc';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_type = isset($_GET['type']) ? $_GET['type'] : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 20; // Înregistrări per pagină

// Calea către fișierul de log
$log_file = __DIR__ . '/includes/logs/login_log.txt';

$logs = [];
$all_logs = [];

// Verificăm dacă fișierul există
if (file_exists($log_file)) {
    // Citim conținutul fișierului
    $log_content = file_get_contents($log_file);
    
    // Împărțim pe linii
    $all_logs = explode("\n", $log_content);
    
    // Eliminăm liniile goale
    $all_logs = array_filter($all_logs);
    
    // Parsăm logurile și le convertim în array-uri pentru sortare
    $parsed_logs = [];
    foreach ($all_logs as $index => $log) {
        // Pattern actualizat pentru noul format cu nume complete
        if (preg_match('/\[(SUCCESS|FAILED|LOGOUT)\] (.*?) \| (?:User|Attempted User): (.*?) \((.*?)\) \| IP: (.*?)(?:\s\|\sAgent:\s(.*))?$/', $log, $matches)) {
            $parsed_logs[] = [
                'original_index' => $index,
                'raw' => $log,
                'type' => $matches[1],
                'timestamp' => $matches[2],
                'user' => $matches[3],
                'full_name' => $matches[4],
                'ip' => $matches[5],
                'agent' => isset($matches[6]) ? $matches[6] : 'Necunoscut'
            ];
        } 
        // Compatibilitate cu vechiul format (fără nume complete)
        elseif (preg_match('/\[(SUCCESS|FAILED|LOGOUT)\] (.*?) \| (?:User|Attempted User): (.*?) \| IP: (.*?)(?:\s\|\sAgent:\s(.*))?$/', $log, $matches)) {
            $parsed_logs[] = [
                'original_index' => $index,
                'raw' => $log,
                'type' => $matches[1],
                'timestamp' => $matches[2],
                'user' => $matches[3],
                'full_name' => getUserFullName($matches[3]), // Obținem numele din config
                'ip' => $matches[4],
                'agent' => isset($matches[5]) ? $matches[5] : 'Necunoscut'
            ];
        }
    }
    
    // Aplicăm filtrarea
    if (!empty($search) || !empty($filter_type)) {
        $parsed_logs = array_filter($parsed_logs, function($log) use ($search, $filter_type) {
            $match_search = true;
            $match_type = true;
            
            // Căutare în toate câmpurile, inclusiv numele complet
            if (!empty($search)) {
                $search_lower = mb_strtolower($search, 'UTF-8');
                $match_search = (
                    strpos(mb_strtolower($log['type'], 'UTF-8'), $search_lower) !== false ||
                    strpos(mb_strtolower($log['timestamp'], 'UTF-8'), $search_lower) !== false ||
                    strpos(mb_strtolower($log['user'], 'UTF-8'), $search_lower) !== false ||
                    strpos(mb_strtolower($log['full_name'], 'UTF-8'), $search_lower) !== false ||
                    strpos(mb_strtolower($log['ip'], 'UTF-8'), $search_lower) !== false ||
                    strpos(mb_strtolower($log['agent'], 'UTF-8'), $search_lower) !== false
                );
            }
            
            // Filtrare după tip
            if (!empty($filter_type)) {
                $match_type = ($log['type'] === $filter_type);
            }
            
            return $match_search && $match_type;
        });
    }
    
    // Aplicăm sortarea
    usort($parsed_logs, function($a, $b) use ($sort_by, $sort_order) {
        $result = 0;
        
        switch ($sort_by) {
            case 'type':
                $result = strcmp($a['type'], $b['type']);
                break;
            case 'date':
                $result = strcmp($a['timestamp'], $b['timestamp']);
                break;
            case 'user':
                $result = strcmp($a['user'], $b['user']);
                break;
            case 'name':
                $result = strcmp($a['full_name'], $b['full_name']);
                break;
            case 'ip':
                $result = strcmp($a['ip'], $b['ip']);
                break;
            case 'browser':
                $browser_a = getBrowserName($a['agent']);
                $browser_b = getBrowserName($b['agent']);
                $result = strcmp($browser_a, $browser_b);
                break;
            default:
                $result = strcmp($a['timestamp'], $b['timestamp']);
        }
        
        return ($sort_order === 'asc') ? $result : -$result;
    });
    
    // Calculăm paginația
    $total_logs = count($parsed_logs);
    $total_pages = ceil($total_logs / $per_page);
    $offset = ($page - 1) * $per_page;
    $logs = array_slice($parsed_logs, $offset, $per_page);
}

// Funcție pentru extragerea numelui browser-ului
function getBrowserName($agent) {
    if (strpos($agent, 'Chrome') !== false) return 'Chrome';
    if (strpos($agent, 'Firefox') !== false) return 'Firefox';
    if (strpos($agent, 'Safari') !== false) return 'Safari';
    if (strpos($agent, 'Edge') !== false) return 'Edge';
    return 'Necunoscut';
}

// Funcție pentru generarea URL-urilor de sortare
function getSortUrl($column) {
    global $sort_by, $sort_order, $search, $filter_type;
    
    $new_order = 'asc';
    if ($sort_by === $column && $sort_order === 'asc') {
        $new_order = 'desc';
    }
    
    $params = [
        'sort' => $column,
        'order' => $new_order
    ];
    
    if (!empty($search)) $params['search'] = $search;
    if (!empty($filter_type)) $params['type'] = $filter_type;
    
    return '?' . http_build_query($params);
}

// Funcție pentru iconița de sortare
function getSortIcon($column) {
    global $sort_by, $sort_order;
    
    if ($sort_by !== $column) {
        return '<i class="fas fa-sort sort-icon"></i>';
    }
    
    return $sort_order === 'asc' ? 
        '<i class="fas fa-sort-up sort-icon active"></i>' : 
        '<i class="fas fa-sort-down sort-icon active"></i>';
}

// Calculăm statisticile pentru toate logurile (nu doar pagina curentă)
$stats = ['SUCCESS' => 0, 'FAILED' => 0, 'LOGOUT' => 0];
if (!empty($parsed_logs)) {
    foreach ($parsed_logs as $log) {
        if (isset($stats[$log['type']])) {
            $stats[$log['type']]++;
        }
    }
}

// Obținem statisticile generale pentru administrare
$general_stats = getLogStats();
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jurnal autentificări | TechMinds Academy</title>
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        .logs-container {
            max-width: 1400px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        /* Mesaje */
        .message {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* Management section */
        .management-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 30px;
            border: 1px solid #e9ecef;
        }
        
        .management-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .management-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .action-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 15px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 6px;
            flex: 1;
            min-width: 250px;
        }
        
        .action-group h4 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 14px;
        }
        
        .action-form {
            display: flex;
            gap: 10px;
            align-items: end;
            flex-wrap: wrap;
        }
        
        .general-stats {
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #1e88e5;
        }
        
        .general-stats h4 {
            margin: 0 0 10px 0;
            color: #1565c0;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 10px;
            font-size: 14px;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-value {
            font-size: 18px;
            font-weight: bold;
            display: block;
        }
        
        /* Filtre și căutare */
        .filters-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 30px;
            border: 1px solid #e9ecef;
        }
        
        .filters-row {
            display: flex;
            gap: 15px;
            align-items: end;
            flex-wrap: wrap;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
            min-width: 150px;
        }
        
        .filter-group.search-group {
            flex: 1;
            min-width: 250px;
        }
        
        .filter-group label {
            font-size: 12px;
            font-weight: 500;
            color: #666;
            margin-bottom: 5px;
        }
        
        .filter-group input,
        .filter-group select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .filter-buttons {
            display: flex;
            gap: 10px;
        }
        
        .btn, .btn-danger, .btn-warning {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background-color: #1e88e5;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #1565c0;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #545b62;
        }
        
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c82333;
        }
        
        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }
        
        .btn-warning:hover {
            background-color: #e0a800;
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }
        
        /* Ștergere selectivă */
        .selection-section {
            background-color: #fff3cd;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid #ffeaa7;
            display: none;
        }
        
        .selection-section.active {
            display: block;
        }
        
        .selection-controls {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .selection-info {
            font-weight: 500;
            color: #856404;
        }
        
        /* Tabel */
        .log-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .log-table th {
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #ddd;
            font-size: 14px;
            background-color: #f8f8f8;
            font-weight: 600;
            color: #333;
            cursor: pointer;
            user-select: none;
            position: relative;
        }
        
        .log-table th:first-child {
            width: 40px;
            cursor: default;
        }
        
        .log-table th:hover:not(:first-child) {
            background-color: #e9ecef;
        }
        
        .log-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 14px;
        }
        
        .log-table tbody tr:hover {
            background-color: #f9f9f9;
        }
        
        .log-table tbody tr.selected {
            background-color: #e3f2fd;
        }
        
        .sort-icon {
            margin-left: 8px;
            opacity: 0.5;
            font-size: 12px;
        }
        
        .sort-icon.active {
            opacity: 1;
            color: #1e88e5;
        }
        
        .log-success {
            color: #28a745;
            font-weight: 500;
        }
        
        .log-failed {
            color: #dc3545;
            font-weight: 500;
        }
        
        .log-logout {
            color: #6c757d;
            font-weight: 500;
        }
        
        /* Checkbox styling */
        .log-checkbox {
            width: 16px;
            height: 16px;
            cursor: pointer;
        }
        
        #selectAll {
            width: 16px;
            height: 16px;
            cursor: pointer;
        }
        
        /* Statistici */
        .stats {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-box {
            flex: 1;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
        }
        
        .stat-success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .stat-failed {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .stat-logout {
            background-color: #e2e3e5;
            color: #383d41;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            display: block;
        }
        
        .stat-label {
            font-size: 12px;
            margin-top: 5px;
        }
        
        /* Paginație */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 30px;
            gap: 10px;
        }
        
        .pagination a,
        .pagination span {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #333;
        }
        
        .pagination a:hover {
            background-color: #e9ecef;
        }
        
        .pagination .current {
            background-color: #1e88e5;
            color: white;
            border-color: #1e88e5;
        }
        
        .pagination .disabled {
            color: #6c757d;
            cursor: not-allowed;
        }
        
        .pagination-info {
            text-align: center;
            margin-top: 15px;
            color: #666;
            font-size: 14px;
        }
        
        .no-logs {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #1e88e5;
            text-decoration: none;
            padding: 8px 16px;
            border: 1px solid #1e88e5;
            border-radius: 4px;
            transition: all 0.3s;
        }
        
        .back-link:hover {
            background-color: #1e88e5;
            color: white;
        }
        
        .results-info {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #e3f2fd;
            border-left: 4px solid #1e88e5;
            border-radius: 4px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .filters-row, .management-actions, .selection-controls {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filter-group, .action-group {
                min-width: auto;
            }
            
            .stats {
                flex-direction: column;
            }
            
            .log-table {
                font-size: 12px;
            }
            
            .log-table th,
            .log-table td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <!-- Header placeholder -->
    <div id="header-placeholder"></div>
    
    <div class="logs-container">
        <a href="/index.html" class="back-link">
            <i class="fas fa-arrow-left"></i> Înapoi la pagina principală
        </a>
        
        <h2>Jurnal autentificări</h2>
        
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $message_type; ?>">
                <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <!-- Secțiunea de administrare -->
        <div class="management-section">
            <div class="management-header">
                <h3><i class="fas fa-tools"></i> Administrare loguri</h3>
            </div>
            
            <!-- Statistici generale -->
            <div class="general-stats">
                <h4><i class="fas fa-chart-bar"></i> Statistici generale</h4>
                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="stat-value"><?php echo $general_stats['total']; ?></span>
                        <small>Total loguri</small>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value" style="color: #28a745;"><?php echo $general_stats['SUCCESS']; ?></span>
                        <small>Reușite</small>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value" style="color: #dc3545;"><?php echo $general_stats['FAILED']; ?></span>
                        <small>Eșuate</small>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value" style="color: #6c757d;"><?php echo $general_stats['LOGOUT']; ?></span>
                        <small>Deconectări</small>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value"><?php echo number_format($general_stats['size'] / 1024, 1); ?> KB</span>
                        <small>Mărime fișier</small>
                    </div>
                </div>
            </div>
            
            <!-- Acțiuni de administrare -->
            <div class="management-actions">
                <!-- Ștergere totală -->
                <div class="action-group">
                    <h4><i class="fas fa-trash-alt"></i> Ștergere totală</h4>
                    <p style="font-size: 12px; color: #666; margin: 0;">Șterge toate logurile (se creează backup)</p>
                    <form method="POST" class="action-form" onsubmit="return confirmAction('Ești sigur că vrei să ștergi TOATE logurile? Se va crea un backup înainte de ștergere.');">
                        <input type="hidden" name="action" value="clear_all">
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash"></i> Șterge tot
                        </button>
                    </form>
                </div>
                
                <!-- Ștergere după tip -->
                <div class="action-group">
                    <h4><i class="fas fa-filter"></i> Ștergere după tip</h4>
                    <p style="font-size: 12px; color: #666; margin: 0;">Șterge logurile de un anumit tip</p>
                    <form method="POST" class="action-form" onsubmit="return confirmAction('Ești sigur că vrei să ștergi logurile de tipul selectat?');">
                        <input type="hidden" name="action" value="clear_by_type">
                        <select name="clear_type" required>
                            <option value="">Selectează tipul</option>
                            <option value="SUCCESS">SUCCESS (<?php echo $general_stats['SUCCESS']; ?>)</option>
                            <option value="FAILED">FAILED (<?php echo $general_stats['FAILED']; ?>)</option>
                            <option value="LOGOUT">LOGOUT (<?php echo $general_stats['LOGOUT']; ?>)</option>
                        </select>
                        <button type="submit" class="btn btn-warning btn-sm">
                            <i class="fas fa-eraser"></i> Șterge
                        </button>
                    </form>
                </div>
                
                <!-- Ștergere loguri vechi -->
                <div class="action-group">
                    <h4><i class="fas fa-calendar-alt"></i> Ștergere loguri vechi</h4>
                    <p style="font-size: 12px; color: #666; margin: 0;">Șterge logurile mai vechi de X zile</p>
                    <form method="POST" class="action-form" onsubmit="return confirmAction('Ești sigur că vrei să ștergi logurile mai vechi de ' + this.clear_days.value + ' zile?');">
                        <input type="hidden" name="action" value="clear_old">
                        <input type="number" name="clear_days" min="1" max="365" value="30" required style="width: 80px;">
                        <label style="font-size: 12px;">zile</label>
                        <button type="submit" class="btn btn-secondary btn-sm">
                            <i class="fas fa-clock"></i> Șterge
                        </button>
                    </form>
                </div>
                
                <!-- Ștergere selectivă -->
                <div class="action-group">
                    <h4><i class="fas fa-check-square"></i> Ștergere selectivă</h4>
                    <p style="font-size: 12px; color: #666; margin: 0;">Selectează înregistrările din tabel și șterge-le</p>
                    <div class="action-form">
                        <button type="button" class="btn btn-primary btn-sm" onclick="toggleSelectionMode()">
                            <i class="fas fa-mouse-pointer"></i> Activează selecția
                        </button>
                   </div>
               </div>
           </div>
       </div>
       
       <!-- Secțiunea de ștergere selectivă -->
       <div class="selection-section" id="selectionSection">
           <form method="POST" id="selectionForm" onsubmit="return confirmSelectedDeletion();">
               <input type="hidden" name="action" value="clear_selected">
               <div class="selection-controls">
                   <span class="selection-info">
                       <i class="fas fa-info-circle"></i>
                       <span id="selectedCount">0</span> înregistrări selectate
                   </span>
                   <button type="button" class="btn btn-sm btn-secondary" onclick="selectAll()">
                       <i class="fas fa-check-double"></i> Selectează tot
                   </button>
                   <button type="button" class="btn btn-sm btn-secondary" onclick="selectNone()">
                       <i class="fas fa-times"></i> Deselectează tot
                   </button>
                   <button type="submit" class="btn btn-sm btn-danger" id="deleteSelectedBtn" disabled>
                       <i class="fas fa-trash"></i> Șterge selectate
                   </button>
                   <button type="button" class="btn btn-sm btn-warning" onclick="toggleSelectionMode()">
                       <i class="fas fa-times"></i> Anulează
                   </button>
               </div>
           </form>
       </div>
       
       <!-- Filtre și căutare -->
       <div class="filters-section">
           <form method="GET" action="">
               <div class="filters-row">
                   <div class="filter-group search-group">
                       <label for="search">Căutare globală:</label>
                       <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Caută în toate câmpurile (inclusiv nume complete)...">
                   </div>
                   
                   <div class="filter-group">
                       <label for="type">Tip eveniment:</label>
                       <select id="type" name="type">
                           <option value="">Toate tipurile</option>
                           <option value="SUCCESS" <?php echo $filter_type === 'SUCCESS' ? 'selected' : ''; ?>>SUCCESS</option>
                           <option value="FAILED" <?php echo $filter_type === 'FAILED' ? 'selected' : ''; ?>>FAILED</option>
                           <option value="LOGOUT" <?php echo $filter_type === 'LOGOUT' ? 'selected' : ''; ?>>LOGOUT</option>
                       </select>
                   </div>
                   
                   <div class="filter-buttons">
                       <button type="submit" class="btn btn-primary">
                           <i class="fas fa-search"></i> Filtrează
                       </button>
                       <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-secondary">
                           <i class="fas fa-times"></i> Resetează
                       </a>
                   </div>
               </div>
               
               <!-- Păstrăm parametrii de sortare -->
               <?php if (isset($_GET['sort'])): ?>
                   <input type="hidden" name="sort" value="<?php echo htmlspecialchars($_GET['sort']); ?>">
               <?php endif; ?>
               <?php if (isset($_GET['order'])): ?>
                   <input type="hidden" name="order" value="<?php echo htmlspecialchars($_GET['order']); ?>">
               <?php endif; ?>
           </form>
       </div>
       
       <?php if (empty($all_logs)): ?>
           <div class="no-logs">
               <i class="fas fa-file-alt" style="font-size: 48px; margin-bottom: 15px; color: #ccc;"></i>
               <p>Nu există înregistrări de autentificare.</p>
           </div>
       <?php else: ?>
           <!-- Informații despre rezultate -->
           <?php if (!empty($search) || !empty($filter_type)): ?>
               <div class="results-info">
                   <strong>Rezultate filtrare:</strong> 
                   <?php echo $total_logs; ?> înregistrări găsite
                   <?php if (!empty($search)): ?>
                       pentru căutarea "<em><?php echo htmlspecialchars($search); ?></em>"
                   <?php endif; ?>
                   <?php if (!empty($filter_type)): ?>
                       <?php echo !empty($search) ? ' și' : ''; ?> tipul "<em><?php echo $filter_type; ?></em>"
                   <?php endif; ?>
               </div>
           <?php endif; ?>
           
           <!-- Statistici pentru rezultatele filtrate -->
           <div class="stats">
               <div class="stat-box stat-success">
                   <span class="stat-number"><?php echo $stats['SUCCESS']; ?></span>
                   <div class="stat-label">Autentificări reușite</div>
               </div>
               <div class="stat-box stat-failed">
                   <span class="stat-number"><?php echo $stats['FAILED']; ?></span>
                   <div class="stat-label">Autentificări eșuate</div>
               </div>
               <div class="stat-box stat-logout">
                   <span class="stat-number"><?php echo $stats['LOGOUT']; ?></span>
                   <div class="stat-label">Deconectări</div>
               </div>
           </div>
           
           <?php if (empty($logs)): ?>
               <div class="no-logs">
                   <i class="fas fa-search" style="font-size: 48px; margin-bottom: 15px; color: #ccc;"></i>
                   <p>Nu s-au găsit înregistrări pentru criteriile de căutare.</p>
               </div>
           <?php else: ?>
               <!-- Tabelul cu logurile -->
               <table class="log-table">
                   <thead>
                       <tr>
                           <th>
                               <input type="checkbox" id="selectAll" style="display: none;" onchange="toggleAllCheckboxes()">
                               <span id="selectAllLabel" style="display: none;">
                                   <i class="fas fa-check-square"></i>
                               </span>
                           </th>
                           <th style="width: 12%;">
                               <a href="<?php echo getSortUrl('type'); ?>" style="color: inherit; text-decoration: none;">
                                   Tip <?php echo getSortIcon('type'); ?>
                               </a>
                           </th>
                           <th style="width: 20%;">
                               <a href="<?php echo getSortUrl('date'); ?>" style="color: inherit; text-decoration: none;">
                                   Data și ora <?php echo getSortIcon('date'); ?>
                               </a>
                           </th>
                           <th style="width: 15%;">
                               <a href="<?php echo getSortUrl('user'); ?>" style="color: inherit; text-decoration: none;">
                                   Username <?php echo getSortIcon('user'); ?>
                               </a>
                           </th>
                           <th style="width: 25%;">
                               <a href="<?php echo getSortUrl('name'); ?>" style="color: inherit; text-decoration: none;">
                                   Nume complet <?php echo getSortIcon('name'); ?>
                               </a>
                           </th>
                           <th style="width: 13%;">
                               <a href="<?php echo getSortUrl('ip'); ?>" style="color: inherit; text-decoration: none;">
                                   IP <?php echo getSortIcon('ip'); ?>
                               </a>
                           </th>
                           <th style="width: 15%;">
                               <a href="<?php echo getSortUrl('browser'); ?>" style="color: inherit; text-decoration: none;">
                                   Browser <?php echo getSortIcon('browser'); ?>
                               </a>
                           </th>
                       </tr>
                   </thead>
                   <tbody>
                       <?php foreach ($logs as $log): ?>
                           <?php
                           $type = $log['type'];
                           $timestamp = $log['timestamp'];
                           $user = $log['user'];
                           $full_name = $log['full_name'];
                           $ip = $log['ip'];
                           $agent = $log['agent'];
                           $browser = getBrowserName($agent);
                           
                           // Clasa CSS în funcție de tip
                           $class = '';
                           $icon = '';
                           if ($type === 'SUCCESS') {
                               $class = 'log-success';
                               $icon = '<i class="fas fa-check-circle"></i>';
                           } elseif ($type === 'FAILED') {
                               $class = 'log-failed';
                               $icon = '<i class="fas fa-times-circle"></i>';
                           } elseif ($type === 'LOGOUT') {
                               $class = 'log-logout';
                               $icon = '<i class="fas fa-sign-out-alt"></i>';
                           }
                           ?>
                           <tr>
                               <td>
                                   <input type="checkbox" class="log-checkbox" name="selected_logs[]" 
                                          value="<?php echo $log['original_index']; ?>" 
                                          style="display: none;" onchange="updateSelectedCount()">
                               </td>
                               <td class="<?php echo $class; ?>">
                                   <?php echo $icon; ?> <?php echo $type; ?>
                               </td>
                               <td><?php echo $timestamp; ?></td>
                               <td><?php echo htmlspecialchars($user); ?></td>
                               <td><strong><?php echo htmlspecialchars($full_name); ?></strong></td>
                               <td><?php echo htmlspecialchars($ip); ?></td>
                               <td><?php echo htmlspecialchars($browser); ?></td>
                           </tr>
                       <?php endforeach; ?>
                   </tbody>
               </table>
               
               <!-- Paginație -->
               <?php if ($total_pages > 1): ?>
                   <div class="pagination">
                       <?php
                       // Construim parametrii pentru paginație
                       $pagination_params = [];
                       if (!empty($search)) $pagination_params['search'] = $search;
                       if (!empty($filter_type)) $pagination_params['type'] = $filter_type;
                       if (!empty($sort_by)) $pagination_params['sort'] = $sort_by;
                       if (!empty($sort_order)) $pagination_params['order'] = $sort_order;
                       
                       // Pagina anterioară
                       if ($page > 1): ?>
                           <a href="?<?php echo http_build_query(array_merge($pagination_params, ['page' => $page - 1])); ?>">
                               <i class="fas fa-chevron-left"></i> Anterior
                           </a>
                       <?php else: ?>
                           <span class="disabled"><i class="fas fa-chevron-left"></i> Anterior</span>
                       <?php endif; ?>
                       
                       <?php
                       // Calculăm intervalul de pagini de afișat
                       $start_page = max(1, $page - 2);
                       $end_page = min($total_pages, $page + 2);
                       
                       // Prima pagină
                       if ($start_page > 1): ?>
                           <a href="?<?php echo http_build_query(array_merge($pagination_params, ['page' => 1])); ?>">1</a>
                           <?php if ($start_page > 2): ?>
                               <span>...</span>
                           <?php endif; ?>
                       <?php endif; ?>
                       
                       <?php
                       // Paginile din interval
                       for ($i = $start_page; $i <= $end_page; $i++): ?>
                           <?php if ($i == $page): ?>
                               <span class="current"><?php echo $i; ?></span>
                           <?php else: ?>
                               <a href="?<?php echo http_build_query(array_merge($pagination_params, ['page' => $i])); ?>"><?php echo $i; ?></a>
                           <?php endif; ?>
                       <?php endfor; ?>
                       
                       <?php
                       // Ultima pagină
                       if ($end_page < $total_pages): ?>
                           <?php if ($end_page < $total_pages - 1): ?>
                               <span>...</span>
                           <?php endif; ?>
                           <a href="?<?php echo http_build_query(array_merge($pagination_params, ['page' => $total_pages])); ?>"><?php echo $total_pages; ?></a>
                       <?php endif; ?>
                       
                       <?php
                       // Pagina următoare
                       if ($page < $total_pages): ?>
                           <a href="?<?php echo http_build_query(array_merge($pagination_params, ['page' => $page + 1])); ?>">
                               Următorul <i class="fas fa-chevron-right"></i>
                           </a>
                       <?php else: ?>
                           <span class="disabled">Următorul <i class="fas fa-chevron-right"></i></span>
                       <?php endif; ?>
                   </div>
                   
                   <div class="pagination-info">
                       Afișare <?php echo $offset + 1; ?> - <?php echo min($offset + $per_page, $total_logs); ?> din <?php echo $total_logs; ?> înregistrări
                       (Pagina <?php echo $page; ?> din <?php echo $total_pages; ?>)
                   </div>
               <?php endif; ?>
           <?php endif; ?>
       <?php endif; ?>
   </div>
   
   <!-- Footer placeholder -->
   <div id="footer-placeholder"></div>
   
   <script src="/js/main.js"></script>
   <script>
       let selectionMode = false;
       
       function confirmAction(message) {
           return confirm(message);
       }
       
       function toggleSelectionMode() {
           selectionMode = !selectionMode;
           const selectionSection = document.getElementById('selectionSection');
           const checkboxes = document.querySelectorAll('.log-checkbox');
           const selectAllCheckbox = document.getElementById('selectAll');
           const selectAllLabel = document.getElementById('selectAllLabel');
           
           if (selectionMode) {
               selectionSection.classList.add('active');
               checkboxes.forEach(cb => cb.style.display = 'block');
               selectAllCheckbox.style.display = 'block';
               selectAllLabel.style.display = 'block';
           } else {
               selectionSection.classList.remove('active');
               checkboxes.forEach(cb => {
                   cb.style.display = 'none';
                   cb.checked = false;
               });
               selectAllCheckbox.style.display = 'none';
               selectAllLabel.style.display = 'none';
               selectAllCheckbox.checked = false;
               updateSelectedCount();
           }
       }
       
       function toggleAllCheckboxes() {
           const selectAllCheckbox = document.getElementById('selectAll');
           const checkboxes = document.querySelectorAll('.log-checkbox');
           const isChecked = selectAllCheckbox.checked;
           
           checkboxes.forEach(cb => {
               cb.checked = isChecked;
               if (isChecked) {
                   cb.closest('tr').classList.add('selected');
               } else {
                   cb.closest('tr').classList.remove('selected');
               }
           });
           
           updateSelectedCount();
       }
       
       function selectAll() {
           const checkboxes = document.querySelectorAll('.log-checkbox');
           const selectAllCheckbox = document.getElementById('selectAll');
           
           checkboxes.forEach(cb => {
               cb.checked = true;
               cb.closest('tr').classList.add('selected');
           });
           
           selectAllCheckbox.checked = true;
           updateSelectedCount();
       }
       
       function selectNone() {
           const checkboxes = document.querySelectorAll('.log-checkbox');
           const selectAllCheckbox = document.getElementById('selectAll');
           
           checkboxes.forEach(cb => {
               cb.checked = false;
               cb.closest('tr').classList.remove('selected');
           });
           
           selectAllCheckbox.checked = false;
           updateSelectedCount();
       }
       
       function updateSelectedCount() {
           const checkboxes = document.querySelectorAll('.log-checkbox:checked');
           const count = checkboxes.length;
           const countElement = document.getElementById('selectedCount');
           const deleteBtn = document.getElementById('deleteSelectedBtn');
           const selectAllCheckbox = document.getElementById('selectAll');
           const allCheckboxes = document.querySelectorAll('.log-checkbox');
           
           countElement.textContent = count;
           deleteBtn.disabled = count === 0;
           
           // Actualizăm checkbox-ul "Selectează tot"
           if (count === 0) {
               selectAllCheckbox.indeterminate = false;
               selectAllCheckbox.checked = false;
           } else if (count === allCheckboxes.length) {
               selectAllCheckbox.indeterminate = false;
               selectAllCheckbox.checked = true;
           } else {
               selectAllCheckbox.indeterminate = true;
           }
           
           // Actualizăm vizual rândurile selectate
           allCheckboxes.forEach(cb => {
               if (cb.checked) {
                   cb.closest('tr').classList.add('selected');
               } else {
                   cb.closest('tr').classList.remove('selected');
               }
           });
       }
       
       function confirmSelectedDeletion() {
           const checkboxes = document.querySelectorAll('.log-checkbox:checked');
           const count = checkboxes.length;
           
           if (count === 0) {
               alert('Nu ați selectat nicio înregistrare pentru ștergere.');
               return false;
           }
           
           return confirm(`Ești sigur că vrei să ștergi ${count} înregistrări selectate? Se va crea un backup înainte de ștergere.`);
       }
       
       // Adaugăm event listeners pentru checkbox-uri
       document.addEventListener('DOMContentLoaded', function() {
           const checkboxes = document.querySelectorAll('.log-checkbox');
           checkboxes.forEach(cb => {
               cb.addEventListener('change', updateSelectedCount);
           });
       });
   </script>
</body>
</html>