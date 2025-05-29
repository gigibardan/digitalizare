<?php
// Definim utilizatorii și parolele (parolele trebuie să fie hash-uri)
$users = [
    // Administrator
    'admin' => password_hash('techminds', PASSWORD_DEFAULT),
    
    // Utilizatori ȘCOALA GIMNAZIALĂ SMARDIOASA
    // Toți utilizatorii au parola: scoalasmardioasa
    'slavu.smardioasa' => password_hash('scoalasmardioasa', PASSWORD_DEFAULT),      // Slavu Lorelai Mihaela
    'petre.smardioasa' => password_hash('scoalasmardioasa', PASSWORD_DEFAULT),      // Petre Elena  
    'cristea.smardioasa' => password_hash('scoalasmardioasa', PASSWORD_DEFAULT),    // Cristea Olimpia
    'zahariea.smardioasa' => password_hash('scoalasmardioasa', PASSWORD_DEFAULT),   // Zahariea Cristina
    'ionescu.smardioasa' => password_hash('scoalasmardioasa', PASSWORD_DEFAULT),    // Ionescu Ileana
    'raportaru.smardioasa' => password_hash('scoalasmardioasa', PASSWORD_DEFAULT),  // Raportaru Aurelian
    'paun.smardioasa' => password_hash('scoalasmardioasa', PASSWORD_DEFAULT),       // Păun Roxana Elena
    'bratu.smardioasa' => password_hash('scoalasmardioasa', PASSWORD_DEFAULT),      // Bratu Angelica Mihaela
    'urucu.smardioasa' => password_hash('scoalasmardioasa', PASSWORD_DEFAULT),      // Urucu Angelica Mădălina
    'dociu.smardioasa' => password_hash('scoalasmardioasa', PASSWORD_DEFAULT),      // Dociu Mihaela Simona
    'dragomirescu.smardioasa' => password_hash('scoalasmardioasa', PASSWORD_DEFAULT), // Dragomirescu Alina
    'lazarica.smardioasa' => password_hash('scoalasmardioasa', PASSWORD_DEFAULT),   // Lăzărică Liliana
    'joinel.smardioasa' => password_hash('scoalasmardioasa', PASSWORD_DEFAULT),     // Joinel Mihaela
    'avram.smardioasa' => password_hash('scoalasmardioasa', PASSWORD_DEFAULT),      // Avram Roxana Madalina
];

// MAPAREA NUMELOR COMPLETE PENTRU LOGURI
$user_full_names = [
    'admin' => 'Administrator',
    'slavu.smardioasa' => 'Slavu Lorelai Mihaela',
    'petre.smardioasa' => 'Petre Elena',
    'cristea.smardioasa' => 'Cristea Olimpia',
    'zahariea.smardioasa' => 'Zahariea Cristina',
    'ionescu.smardioasa' => 'Ionescu Ileana',
    'raportaru.smardioasa' => 'Raportaru Aurelian',
    'paun.smardioasa' => 'Păun Roxana Elena',
    'bratu.smardioasa' => 'Bratu Angelica Mihaela',
    'urucu.smardioasa' => 'Urucu Angelica Mădălina',
    'dociu.smardioasa' => 'Dociu Mihaela Simona',
    'dragomirescu.smardioasa' => 'Dragomirescu Alina',
    'lazarica.smardioasa' => 'Lăzărică Liliana',
    'joinel.smardioasa' => 'Joinel Mihaela',
    'avram.smardioasa' => 'Avram Roxana Madalina',
];

// Funcție pentru obținerea numelui complet
function getUserFullName($username) {
    global $user_full_names;
    return isset($user_full_names[$username]) ? $user_full_names[$username] : $username;
}

// Funcție pentru logarea evenimentelor cu nume complet
function logEvent($type, $username, $ip = null, $user_agent = null) {
    $log_file = __DIR__ . '/logs/login_log.txt';
    
    // Creăm directorul logs dacă nu există
    $log_dir = dirname($log_file);
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    // Obținem IP-ul și user agent-ul dacă nu sunt furnizate
    if ($ip === null) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'Necunoscut';
    }
    if ($user_agent === null) {
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Necunoscut';
    }
    
    // Obținem numele complet
    $full_name = getUserFullName($username);
    
    // Formatăm mesajul de log cu numele complet
    $timestamp = date('Y-m-d H:i:s');
    
    // Formatăm diferit în funcție de tip
    if ($type === 'FAILED') {
        $log_message = "[{$type}] {$timestamp} | Attempted User: {$username} ({$full_name}) | IP: {$ip} | Agent: {$user_agent}";
    } else {
        $log_message = "[{$type}] {$timestamp} | User: {$username} ({$full_name}) | IP: {$ip} | Agent: {$user_agent}";
    }
    
    // Scriem în fișier
    file_put_contents($log_file, $log_message . "\n", FILE_APPEND | LOCK_EX);
}

// Funcții pentru gestionarea logurilor (folosite în admin_logs.php)
function clearAllLogs() {
    $log_file = __DIR__ . '/logs/login_log.txt';
    if (file_exists($log_file)) {
        // Creăm backup înainte de ștergere
        $backup_file = __DIR__ . '/logs/backup_' . date('Y-m-d_H-i-s') . '_login_log.txt';
        copy($log_file, $backup_file);
        
        // Golim fișierul
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
    
    // Citim toate logurile
    $logs = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    // Filtrăm logurile (păstrăm cele care NU sunt de tipul specificat)
    $filtered_logs = array_filter($logs, function($log) use ($type) {
        return !preg_match('/\[' . preg_quote($type, '/') . '\]/', $log);
    });
    
    // Creăm backup
    $backup_file = __DIR__ . '/logs/backup_' . date('Y-m-d_H-i-s') . '_' . strtolower($type) . '_removed.txt';
    copy($log_file, $backup_file);
    
    // Scriem logurile filtrate înapoi
    file_put_contents($log_file, implode("\n", $filtered_logs) . (empty($filtered_logs) ? '' : "\n"));
    
    return true;
}

function clearOldLogs($days = 30) {
    $log_file = __DIR__ . '/logs/login_log.txt';
    if (!file_exists($log_file)) {
        return false;
    }
    
    // Citim toate logurile
    $logs = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    // Data limită
    $limit_date = date('Y-m-d', strtotime("-{$days} days"));
    
    // Filtrăm logurile (păstrăm doar cele mai noi)
    $filtered_logs = array_filter($logs, function($log) use ($limit_date) {
        // Extragem data din log
        if (preg_match('/\[(SUCCESS|FAILED|LOGOUT)\] (\d{4}-\d{2}-\d{2})/', $log, $matches)) {
            return $matches[2] >= $limit_date;
        }
        return true; // Păstrăm logurile cu format necunoscut
    });
    
    // Creăm backup
    $backup_file = __DIR__ . '/logs/backup_' . date('Y-m-d_H-i-s') . '_old_logs_removed.txt';
    copy($log_file, $backup_file);
    
    // Scriem logurile filtrate înapoi
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
    
    // Mărimea fișierului
    $stats['size'] = filesize($log_file);
    
    return $stats;
}

// Adaugă această funcție în config.php
function clearSelectedLogs($selected_indices) {
    $log_file = __DIR__ . '/logs/login_log.txt';
    if (!file_exists($log_file)) {
        return false;
    }
    
    // Citim toate logurile
    $logs = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    // Convertim indicii la integers pentru comparație sigură
    $selected_indices = array_map('intval', $selected_indices);
    
    // Filtrăm logurile (păstrăm cele care NU sunt selectate)
    $filtered_logs = [];
    foreach ($logs as $index => $log) {
        if (!in_array($index, $selected_indices)) {
            $filtered_logs[] = $log;
        }
    }
    
    // Creăm backup
    $backup_file = __DIR__ . '/logs/backup_' . date('Y-m-d_H-i-s') . '_selected_removed.txt';
    copy($log_file, $backup_file);
    
    // Scriem logurile filtrate înapoi
    file_put_contents($log_file, implode("\n", $filtered_logs) . (empty($filtered_logs) ? '' : "\n"));
    
    return true;
}

// Configurări pentru sesiune
$session_timeout = 7200; // 2 ore în secunde

// Adresa de email pentru contact
$contact_email = 'office@techminds-academy.ro';

// Definim paginile publice (care nu vor fi protejate)
$public_pages = [
    'index.html',
    'index.php',
    'login.php',
    'restricted.php',
    'check_auth.php',
    'admin_logs.php',
    // Doar pagina index din fiecare modul (dacă dorești să fie accesibile)
    //'modules/module1/index.html',
    //'modules/module2/index.html',
    //'modules/module3/index.html',
    //'modules/module4/index.html',
    //'modules/module5/index.html',
    //'modules/module6/index.html'
];

/* 
LISTĂ UTILIZATORI ȘCOALA GIMNAZIALĂ SMARDIOASA:
================================================
Username: slavu.smardioasa      | Nume: Slavu Lorelai Mihaela     | Email: lory.slavu@gmail.com
Username: petre.smardioasa      | Nume: Petre Elena               | Email: elis_2u@yahoo.com
Username: cristea.smardioasa    | Nume: Cristea Olimpia           | Email: cristea.olimpia1964@yahoo.com
Username: zahariea.smardioasa   | Nume: Zahariea Cristina         | Email: cristina.zahariea@yahoo.com
Username: ionescu.smardioasa    | Nume: Ionescu Ileana            | Email: ionescuileana1981@yahoo.com
Username: raportaru.smardioasa  | Nume: Raportaru Aurelian        | Email: aurelianraportaru@yahoo.com
Username: paun.smardioasa       | Nume: Păun Roxana Elena         | Email: roxanapaun33@gmail.com
Username: bratu.smardioasa      | Nume: Bratu Angelica Mihaela    | Email: bratumihaelaangelica@yahoo.com
Username: urucu.smardioasa      | Nume: Urucu Angelica Mădălina   | Email: urucu.madalina@gmail.com
Username: dociu.smardioasa      | Nume: Dociu Mihaela Simona      | Email: dociumihaelasimona@yahoo.com
Username: dragomirescu.smardioasa | Nume: Dragomirescu Alina      | Email: alinutad76@yahoo.com
Username: lazarica.smardioasa   | Nume: Lăzărică Liliana          | Email: lazaricaliliana134@gmail.com
Username: joinel.smardioasa     | Nume: Joinel Mihaela            | Email: joinel_mihaela@yahoo.com
Username: avram.smardioasa      | Nume: Avram Roxana Madalina     | Email: avram_roxana2009@yahoo.com

PAROLA COMUNĂ PENTRU TOȚI: scoalasmardioasa

NOUL FORMAT DE LOGURI:
======================
[SUCCESS] 2025-05-29 14:30:15 | User: cristea.smardioasa (Cristea Olimpia) | IP: 192.168.1.1 | Agent: Mozilla/5.0...
[FAILED] 2025-05-29 14:32:10 | Attempted User: wrong.user (Utilizator Necunoscut) | IP: 192.168.1.1 | Agent: Mozilla/5.0...
[LOGOUT] 2025-05-29 15:45:20 | User: cristea.smardioasa (Cristea Olimpia) | IP: 192.168.1.1 | Agent: Mozilla/5.0...
*/
?>