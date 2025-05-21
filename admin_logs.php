<?php
// Verificăm dacă utilizatorul este autentificat și este admin
require_once 'includes/auth.php';

// Verificăm dacă utilizatorul este admin (poți adăuga această verificare în auth.php)
function isAdmin() {
    return isset($_SESSION['user']) && $_SESSION['user'] === 'admin'; // Înlocuiește 'admin' cu numele utilizatorului admin
}

// Dacă nu este admin, redirecționăm
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
            max-width: 1000px;
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
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .log-table th {
            background-color: #f8f8f8;
        }
        
        .log-success {
            color: #28a745;
        }
        
        .log-failed {
            color: #dc3545;
        }
        
        .log-logout {
            color: #6c757d;
        }
    </style>
</head>
<body>
    <!-- Header placeholder -->
    <div id="header-placeholder"></div>
    
    <div class="logs-container">
        <h2>Jurnal autentificări</h2>
        
        <?php if (empty($logs)): ?>
            <p>Nu există înregistrări de autentificare.</p>
        <?php else: ?>
            <table class="log-table">
                <thead>
                    <tr>
                        <th>Tip</th>
                        <th>Data și ora</th>
                        <th>Utilizator</th>
                        <th>Adresă IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <?php
                        // Extragem informațiile din log
                        if (preg_match('/\[(SUCCESS|FAILED|LOGOUT)\] (.*?) \| User: (.*?) \| IP: (.*?)($| \|)/', $log, $matches)) {
                            $type = $matches[1];
                            $timestamp = $matches[2];
                            $user = $matches[3];
                            $ip = $matches[4];
                            
                            // Clasa CSS în funcție de tip
                            $class = '';
                            if ($type === 'SUCCESS') {
                                $class = 'log-success';
                            } elseif ($type === 'FAILED') {
                                $class = 'log-failed';
                            } elseif ($type === 'LOGOUT') {
                                $class = 'log-logout';
                            }
                        ?>
                        <tr class="<?php echo $class; ?>">
                            <td><?php echo $type; ?></td>
                            <td><?php echo $timestamp; ?></td>
                            <td><?php echo $user; ?></td>
                            <td><?php echo $ip; ?></td>
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