<?php
// Verificăm dacă utilizatorul este autentificat și este admin
require_once 'includes/auth.php';

// Dacă nu este admin, redirecționăm (folosim funcția din auth.php)
if (!isSessionValid() || !isAdmin()) {
    header('Location: /restricted.php');
    exit();
}

// Parametrii pentru sortare, căutare și paginație
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'date';
$sort_order = isset($_GET['order']) ? $_GET['order'] : 'desc';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_type = isset($_GET['type']) ? $_GET['type'] : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 20; // Înregistrări per pagină

// Calea către fișierul de log
$log_file = __DIR__ . '/logs/login_log.txt';
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
        if (preg_match('/\[(SUCCESS|FAILED|LOGOUT)\] (.*?) \| (?:User|Attempted User): (.*?) \| IP: (.*?)(?:\s\|\sAgent:\s(.*))?$/', $log, $matches)) {
            $parsed_logs[] = [
                'original_index' => $index,
                'raw' => $log,
                'type' => $matches[1],
                'timestamp' => $matches[2],
                'user' => $matches[3],
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
            
            // Căutare în toate câmpurile
            if (!empty($search)) {
                $search_lower = mb_strtolower($search, 'UTF-8');
                $match_search = (
                    strpos(mb_strtolower($log['type'], 'UTF-8'), $search_lower) !== false ||
                    strpos(mb_strtolower($log['timestamp'], 'UTF-8'), $search_lower) !== false ||
                    strpos(mb_strtolower($log['user'], 'UTF-8'), $search_lower) !== false ||
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
        
        .btn {
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
        
        .log-table th:hover {
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
            .filters-row {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filter-group {
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
        
        <!-- Filtre și căutare -->
        <div class="filters-section">
            <form method="GET" action="">
                <div class="filters-row">
                    <div class="filter-group search-group">
                        <label for="search">Căutare globală:</label>
                        <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Caută în toate câmpurile...">
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
            
            <!-- Statistici -->
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
                            <th style="width: 15%;">
                                <a href="<?php echo getSortUrl('type'); ?>" style="color: inherit; text-decoration: none;">
                                    Tip <?php echo getSortIcon('type'); ?>
                                </a>
                            </th>
                            <th style="width: 25%;">
                                <a href="<?php echo getSortUrl('date'); ?>" style="color: inherit; text-decoration: none;">
                                    Data și ora <?php echo getSortIcon('date'); ?>
                                </a>
                            </th>
                            <th style="width: 20%;">
                                <a href="<?php echo getSortUrl('user'); ?>" style="color: inherit; text-decoration: none;">
                                    Utilizator <?php echo getSortIcon('user'); ?>
                                </a>
                            </th>
                            <th style="width: 15%;">
                                <a href="<?php echo getSortUrl('ip'); ?>" style="color: inherit; text-decoration: none;">
                                    Adresă IP <?php echo getSortIcon('ip'); ?>
                                </a>
                            </th>
                            <th style="width: 25%;">
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
                                <td class="<?php echo $class; ?>">
                                    <?php echo $icon; ?> <?php echo $type; ?>
                                </td>
                                <td><?php echo $timestamp; ?></td>
                                <td><?php echo htmlspecialchars($user); ?></td>
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
</body>
</html>