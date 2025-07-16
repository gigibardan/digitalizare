<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// config.php - VERSIUNEA FINALĂ (extensibilă)

// Încarcă hash-urile pre-generate
if (file_exists(__DIR__ . '/users_final.php')) {
    require_once __DIR__ . '/users_final.php';
} else {
    $users = ['admin' => password_hash('techminds', PASSWORD_DEFAULT)];
}

if (file_exists(__DIR__ . '/user_names.php')) {
    require_once __DIR__ . '/user_names.php';
} else {
    $user_full_names = ['admin' => 'Administrator'];
}

// Funcție pentru obținerea numelui complet
function getUserFullName($username) {
    global $user_full_names;
    return isset($user_full_names[$username]) ? $user_full_names[$username] : $username;
}

// Funcție pentru verificarea dacă un utilizator există
function userExists($username) {
    global $users;
    return isset($users[$username]);
}

// Funcție pentru verificarea parolei
function verifyPassword($username, $password) {
    global $users;
    if (!userExists($username)) {
        return false;
    }
    return password_verify($password, $users[$username]);
}

// Funcție pentru obținerea statisticilor utilizatorilor
function getUserStats() {
    global $users, $user_full_names;
    
    $stats = [
        'total_users' => count($users),
        'admin_users' => 0,
        'schools' => []
    ];
    
    foreach (array_keys($users) as $username) {
        // Contează adminii
        if ($username === 'admin' || in_array($username, ['admin'])) {
            $stats['admin_users']++;
        }
        
        // Analizează școala din username
        if (strpos($username, '.') !== false) {
            $parts = explode('.', $username);
            $school = end($parts);
            if (!isset($stats['schools'][$school])) {
                $stats['schools'][$school] = 0;
            }
            $stats['schools'][$school]++;
        }
    }
    
    return $stats;
}

// Funcție OPTIMIZATĂ pentru logarea evenimentelor
function logEvent($type, $username, $ip = null, $user_agent = null) {
    // DOAR pentru evenimente importante
    if ($type !== 'SUCCESS' && $type !== 'FAILED') {
        return;
    }
    
    $log_file = __DIR__ . '/logs/login_log.txt';
    
    // Creează director dacă nu există
    if (!is_dir(dirname($log_file))) {
        mkdir(dirname($log_file), 0755, true);
    }
    
    $ip = $ip ?? ($_SERVER['REMOTE_ADDR'] ?? 'Unknown');
    $full_name = getUserFullName($username);
    $timestamp = date('Y-m-d H:i:s');
    
    $log_message = "[{$type}] {$timestamp} | User: {$username} ({$full_name}) | IP: {$ip}";
    
    // Scrie direct, fără debug-uri
    @file_put_contents($log_file, $log_message . "\n", FILE_APPEND | LOCK_EX);
}

// Funcții pentru gestionarea logurilor
function clearAllLogs() {
    $log_file = __DIR__ . '/logs/login_log.txt';
    if (file_exists($log_file)) {
        $backup_file = __DIR__ . '/logs/backup_' . date('Y-m-d_H-i-s') . '_login_log.txt';
        copy($log_file, $backup_file);
        file_put_contents($log_file, '');
        return true;
    }
    return false;
}

function clearLogsByType($type) {
    $log_file = __DIR__ . '/logs/login_log.txt';
    if (!file_exists($log_file)) {
        return false;
    }
    
    $logs = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $filtered_logs = array_filter($logs, function($log) use ($type) {
        return !preg_match('/\[' . preg_quote($type, '/') . '\]/', $log);
    });
    
    $backup_file = __DIR__ . '/logs/backup_' . date('Y-m-d_H-i-s') . '_' . strtolower($type) . '_removed.txt';
    copy($log_file, $backup_file);
    file_put_contents($log_file, implode("\n", $filtered_logs) . (empty($filtered_logs) ? '' : "\n"));
    
    return true;
}

function clearOldLogs($days = 30) {
    $log_file = __DIR__ . '/logs/login_log.txt';
    if (!file_exists($log_file)) {
        return false;
    }
    
    $logs = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $limit_date = date('Y-m-d', strtotime("-{$days} days"));
    
    $filtered_logs = array_filter($logs, function($log) use ($limit_date) {
        if (preg_match('/\[(SUCCESS|FAILED|LOGOUT)\] (\d{4}-\d{2}-\d{2})/', $log, $matches)) {
            return $matches[2] >= $limit_date;
        }
        return true;
    });
    
    $backup_file = __DIR__ . '/logs/backup_' . date('Y-m-d_H-i-s') . '_old_logs_removed.txt';
    copy($log_file, $backup_file);
    file_put_contents($log_file, implode("\n", $filtered_logs) . (empty($filtered_logs) ? '' : "\n"));
    
    return true;
}

function getLogStats() {
    $log_file = __DIR__ . '/logs/login_log.txt';
    if (!file_exists($log_file)) {
        return ['total' => 0, 'SUCCESS' => 0, 'FAILED' => 0, 'LOGOUT' => 0, 'size' => 0];
    }
    
    $logs = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $stats = ['total' => count($logs), 'SUCCESS' => 0, 'FAILED' => 0, 'LOGOUT' => 0];
    
    foreach ($logs as $log) {
        if (preg_match('/\[(SUCCESS|FAILED|LOGOUT)\]/', $log, $matches)) {
            $stats[$matches[1]]++;
        }
    }
    
    $stats['size'] = filesize($log_file);
    return $stats;
}

function clearSelectedLogs($selected_indices) {
    $log_file = __DIR__ . '/logs/login_log.txt';
    if (!file_exists($log_file)) {
        return false;
    }
    
    $logs = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $selected_indices = array_map('intval', $selected_indices);
    
    $filtered_logs = [];
    foreach ($logs as $index => $log) {
        if (!in_array($index, $selected_indices)) {
            $filtered_logs[] = $log;
        }
    }
    
    $backup_file = __DIR__ . '/logs/backup_' . date('Y-m-d_H-i-s') . '_selected_removed.txt';
    copy($log_file, $backup_file);
    file_put_contents($log_file, implode("\n", $filtered_logs) . (empty($filtered_logs) ? '' : "\n"));
    
    return true;
}

// Configurări pentru sesiune
$session_timeout = 7200; // 2 ore în secunde

// Adresa de email pentru contact
$contact_email = 'office@techminds-academy.ro';

// Definim paginile publice
$public_pages = [
    'index.html',
    'index.php',
    'login.php',
    'restricted.php',
    'check_auth.php',
    'admin_logs.php',
];

// Afișează statistici pentru debug (doar pentru admin)
if (isset($_GET['debug']) && isLoggedIn() && isAdmin()) {
    echo "<pre>";
    echo "Statistici utilizatori:\n";
    print_r(getUserStats());
    echo "</pre>";
}
?>