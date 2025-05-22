<?php
// Verificăm dacă utilizatorul este autentificat și este admin
require_once 'includes/auth.php';

// Dacă nu este admin, redirecționăm (folosim funcția din auth.php)
if (!isSessionValid() || !isAdmin()) {
    header('Location: /restricted.php');
    exit();
}

// Calea către fișierul de log
$log_file = __DIR__ . '/logs/login_log.txt';
$logs = [];

// Verificăm dacă fișierul există
if (file_exists($log_file)) {
    // Citim conținutul fișierului
    $log_content = file_get_contents($log_file);
    
    // Împărțim pe linii
    $logs = explode("\n", $log_content);
    
    // Eliminăm liniile goale
    $logs = array_filter($logs);
    
    // Inversăm ordinea pentru a afișa cele mai recente loguri primele
    $logs = array_reverse($logs);
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
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .log-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .log-table th, .log-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 14px;
        }
        
        .log-table th {
            background-color: #f8f8f8;
            font-weight: 600;
            color: #333;
        }
        
        .log-table tbody tr:hover {
            background-color: #f9f9f9;
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
        
        <?php if (empty($logs)): ?>
            <div class="no-logs">
                <i class="fas fa-file-alt" style="font-size: 48px; margin-bottom: 15px; color: #ccc;"></i>
                <p>Nu există înregistrări de autentificare.</p>
            </div>
        <?php else: ?>
            <?php
            // Calculăm statisticile
            $stats = [
                'SUCCESS' => 0,
                'FAILED' => 0,
                'LOGOUT' => 0
            ];
            
            foreach ($logs as $log) {
                if (preg_match('/\[(SUCCESS|FAILED|LOGOUT)\]/', $log, $matches)) {
                    $stats[$matches[1]]++;
                }
            }
            ?>
            
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
            
            <!-- Tabelul cu logurile -->
            <table class="log-table">
                <thead>
                    <tr>
                        <th style="width: 15%;">Tip</th>
                        <th style="width: 25%;">Data și ora</th>
                        <th style="width: 20%;">Utilizator</th>
                        <th style="width: 15%;">Adresă IP</th>
                        <th style="width: 25%;">Browser</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <?php
                        // Pattern pentru toate tipurile de loguri
                        if (preg_match('/\[(SUCCESS|FAILED|LOGOUT)\] (.*?) \| (?:User|Attempted User): (.*?) \| IP: (.*?)(?:\s\|\sAgent:\s(.*))?$/', $log, $matches)) {
                            $type = $matches[1];
                            $timestamp = $matches[2];
                            $user = $matches[3];
                            $ip = $matches[4];
                            $agent = isset($matches[5]) ? $matches[5] : 'Necunoscut';
                            
                            // Extragem numele browser-ului din user agent
                            $browser = 'Necunoscut';
                            if (strpos($agent, 'Chrome') !== false) {
                                $browser = 'Chrome';
                            } elseif (strpos($agent, 'Firefox') !== false) {
                                $browser = 'Firefox';
                            } elseif (strpos($agent, 'Safari') !== false) {
                                $browser = 'Safari';
                            } elseif (strpos($agent, 'Edge') !== false) {
                                $browser = 'Edge';
                            }
                            
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
                        <?php } ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <!-- Footer placeholder -->
    <div id="footer-placeholder"></div>
    
    <script src="/js/main.js"></script>
</body>
</html>